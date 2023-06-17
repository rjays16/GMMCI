<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class NotifiableDiseases extends FPDF{
	var $from;
	var $to;
	var $count_rows;
	var $code1;
	var $code2;

	function NotifiableDiseases($from, $to, $code, $sclass){
		global $db;
		$this->ColumnWidth = array(8,55,22,33,28,10,55,12,60,20,50);
		$this->SetTopMargin(3);
		$this->Alignment = array('L','L','C','C','C','C','L','C','L','L','L');
		$this->FPDF("L", 'mm', 'Legal');
		$this->code1 = trim($code).".-";
		$this->code2 = trim($code)."-";
		//$this->code = $code;

		if($code != 'all')
			//$this->icd_cond = "AND (ed.code_parent = '".$this->code."'";
			#$this->icd_cond = " AND (ed.code_parent = '".$this->code1."' OR ed.code_parent = '".$this->code2."') ";
			$this->icd_cond = " AND IF(instr(ed.icd,'.'),
														substr(ed.icd,1,IF(instr(ed.icd,'.'),instr(ed.icd,'.')-1,0)),
														ed.icd) = '$code' ";
		else
			$this->icd_cond = "";

		if ($sclass=='primary'){
			$this->type_cond = " AND ed.type_nr='1' ";
		}elseif ($sclass=='secondary'){
			$this->type_cond = " AND ed.type_nr='0' ";
		}elseif ($sclass=='all')
			$this->type_cond = "";

		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));
	}

	function Header() {
		global $root_path, $db;

		$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";
		}
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',110,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 0;
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->SetFont('Arial', 'B', 11);
		$this->Ln(2);
		$this->Cell(0, 6, "OFFICE OF THE CITY HEALTH OFFICER",0,1,'C');
		#$this->SetFont('Arial', 'B', 12);
		if($this->from == $this->to){
			$this->Cell(0,6, "For the Period : As of ".date("F j, Y",strtotime($this->from)), 0,1,'C');
		}
		else{
			$this->Cell(0, 4, "For the Period : From ".date("F j, Y", strtotime($this->from))." to ".date("F j, Y",strtotime($this->to)),0,1,'C');
		}

		$this->Cell(0, 6, "REPORT of NOTIFIABLE DISEASES",0,1,'C');
		$this->Cell(0, 6, "(As required under R.A. No. 3573)",0,1,'C');

		$this->Ln(5);
	}


	function GetDiagnosisData(){
		global $db;
		$this->SetLeftMargin(2);
		$prev_diag = "";
		$rowheader = 6;

		$sql = "SELECT DISTINCT ed.*
						FROM seg_rep_medrec_patient_icd_tbl AS ed
						WHERE DATE(ed.admission_dt) BETWEEN  '".$this->from."' AND '".$this->to."'
						".$this->icd_cond."
						".$this->type_cond."
						AND ed.encounter_type IN (3,4)
						ORDER BY ed.icd, DATE(ed.admission_dt) ASC, ed.patient_name";
		#echo $sql;
		$result = $db->Execute($sql);
		$this->count = $result->RecordCount();

		if($this->count==0){
				$this->Ln(2);
				$this->SetFont('Arial', 'B', 12);
				$this->Cell(0, $rowheader, "No records found for this report...", 0,0,'C');
		}else{
			while($rows = $result->FetchRow()){

				#echo $row['ICD'];
				$diag = $rows['icd'];
				$desc = $rows['icd_desc'];

					if($prev_diag != $diag){
						$this->Ln();
						$this->SetFont('Arial', 'B', 9);
						$this->Cell(18, $RowWidth, "Diagnosis : ", 0,0,'L');
						$l_diag = $this->GetStringWidth($diag);
						$this->Cell($l_diag+2, $RowWidth, $diag); // for ICD 10
						$this->Cell(20, $RowWidth, "", 0, 0, 'L');
						$this->Cell(20, $RowWidth, "Desciption : ", 0, 0, 'L');
						$this->Cell(0, $RowWidth, $desc, 0, 1, 'L'); //for description
						$this->Ln(2);
						$this->SetFont('Arial', 'B', 8);
						$this->Cell($this->ColumnWidth[0], $rowheader, "", 1,0,'L');
						$this->Cell($this->ColumnWidth[1], $rowheader, "PATIENT NAME", 1,0,'C');
						$this->Cell($this->ColumnWidth[2], $rowheader, "CASE NO.", 1,0,'C');
						$this->Cell($this->ColumnWidth[3], $rowheader, "DATE ADMITTED", 1,0,'C');
						$this->Cell($this->ColumnWidth[4], $rowheader, "DATE DISCHARGED", 1,0,'C');
						$this->Cell($this->ColumnWidth[5], $rowheader, "AGE", 1,0,'C');
						$this->Cell($this->ColumnWidth[6], $rowheader, "DIAGNOSIS", 1,0,'C');
						$this->Cell($this->ColumnWidth[7], $rowheader, "SEX", 1,0,'C');
						$this->Cell($this->ColumnWidth[8], $rowheader, "ADDRESS", 1,0,'C');
						$this->Cell($this->ColumnWidth[9], $rowheader, "REMARKS", 1,0,'C');
						$this->Cell($this->ColumnWidth[10], $rowheader, "ATTENDING PHYSICIAN", 1,0,'C');
						$this->Ln();
						$this->SetFont('Arial', '', 9);

						$prev_diag = $diag;
						$i=1;
					}

					#--- transferred by VAN and delete the function GetDiagnosisOccurrence
					$sql4 = "SELECT * FROM seg_results WHERE result_code='".$rows[result_code]."'";
					$result4 = $db->Execute($sql4);
					if (is_object($result4)){
						$row_rem = $result4->FetchRow();
						$remarks = trim($row_rem['result_desc']);
					}

					if (($rows['discharge_date']!='0000-00-00')&&(!(empty($rows['discharge_date']))))
						$discharged_date = date("m/d/Y",strtotime($rows['discharge_date']));
					else
						$discharged_date = 'still in';

					if (($rows['admission_dt']!='0000-00-00')&&(!(empty($rows['admission_dt']))))
						$admission_date = date("m/d/Y h:i A",strtotime($rows['admission_dt']));

					$this->Row(array( $i,
														mb_strtoupper(trim($rows['patient_name'])),
														trim($rows['encounter_nr']),
														trim($admission_date),
														trim($discharged_date),
														trim($rows['age']),
														trim($rows['diagnosis']),
														strtoupper(trim($rows['sex'])),
														mb_strtoupper(trim($rows['address'])),
														$remarks,
														mb_strtoupper(trim($rows['physician']))));
					$i++;
					#----
			}
		}
	}

	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	//-------------------------------------
	 function SetWidths($w)
	 {
			//Set the array of column widths
			$this->widths=$w;
	 }

	 function SetAligns($a)
	 {
			//Set the array of column alignments
			$this->aligns=$a;
	 }

	 function Row($data)
	 {
		$row = 4;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->ColumnWidth[$i],$data[$i]));
					$nb2=$this->NbLines($this->ColumnWidth[4],$data[4]);
					$nb3=$this->NbLines($this->ColumnWidth[6],$data[6]);
					#echo "(nb_2): ".$nb2." (nb_3): ".$nb3;
					if($nb2>$nb3){

						$nbdiff = $nb2 - $nb3;
						$nbdiff = $nbdiff*$row;

						k == 1;
					}
					else if($nb3>$nb2){
						$nbdiff = $nb3 - $nb2;
						 $nbdiff = $nbdiff*$row;
						k==0;
					}
					else{
						$nbdiff = 0;
					}

				 $l_data4 = $this->GetStringWidth($data[4]);
				 $l_data6 = $this->GetStringWidth($data[6]);
				 #echo "data4: ".$l_data4." data6:".$l_data6;
						if(($l_data4 >$l_data6) && ($l_data6 > $this->ColumnWidth[6]) && ($nb2 > $nb3)){
							$lgreater = $l_data4;
							$ldiff = $lgreater - $l_data6;
							#echo intval($l);
							#echo "l_data4: ".$l_data4." l_data6: ".$l_data6." ldiff: ".$ldiff;
								for($cnt = 0; $cnt<intval($ldiff); $cnt++)
									 $data[6].= " ";

						}else if(($l_data6 > $l_data4) && ($l_data4 > $this->ColumnWidth[4]) && ($nb3 > $nb2)){

							$lgreater = $l_data6;
							$ldiff = $lgreater - $l_data4;
							#echo "l_data6: ".$l_data6." l_data4: ".$l_data4." ldiff: ".$ldiff;
								for($cnt = 0; $cnt<intval($ldiff); $cnt++)
									$data[4].=" ";
						}
				 $l_data0 = $this->GetStringWidth($data[0]);
				 $l_data8 = $this->GetStringWidth($data[8]);

					if($l_data0 > $this->ColumnWidth[0]){
						$ldiff2 = $lgreater - $l_data0;
						for($cnt1 = 0; $cnt1<intval($ldiff2); $cnt1++)
							$data[0].=" ";
					}

					if($l_data8 > $this->ColumnWidth[8]){
						$ldiff3 = $lgreater - $l_data8;
						for($cnt2 = 0; $cnt2<intval($ldiff3); $cnt2++)
							$data[8].=" ";
					}
								#echo $data[6];
					//$nb = $nb*2;
					//print_r($nb);
			$h=$row*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row

			for($i=0;$i<count($data);$i++)
			{
					$w=$this->ColumnWidth[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
					//Save the current position

					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border

							$length = $this->GetStringWidth($data[$i]);
							if($length < $this->ColumnWidth[$i]){
								//$this->Cell($w, $h, $data[$i],1,0,'L');
								$this->Cell($w, $h, $data[$i], 1, 0, $this->Alignment[$i]);
							}
							else{
								$nbrow = 3;

								// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								//$this->MultiCell($w, $row,$data[$i],1,'L');
								$this->MultiCell($w, $row, $data[$i], 1,$this->Alignment[$i]);

								//$this->MultiCell($length, $row,$data[$i],1,'L');

							}

					//Put the position to the right of the cell
					$this->SetXY($x+$w,$y);
			}
			//Go to the next line
			$this->Ln($h);
		}

		function CheckPageBreak($h) {
				//If the height h would cause an overflow, add a new page immediately
				if($this->GetY()+$h>$this->PageBreakTrigger)
						$this->AddPage($this->CurOrientation);
		}

		function NbLines($w,$txt) {
				//Computes the number of lines a MultiCell of width w will take
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
						$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 and $s[$nb-1]=="\n")
						$nb--;
				$sep=-1;
				$i=0;
				$j=0;
				$l=0;
				$nl=1;
				while($i<$nb)
				{
						$c=$s[$i];
						if($c=="\n")
						{
								$i++;
								$sep=-1;
								$j=$i;
								$l=0;
								$nl++;
								continue;
						}
						if($c==' ')
								$sep=$i;
						$l+=$cw[$c];
						if($l>$wmax)
						{
								if($sep==-1)
								{
										if($i==$j)
												$i++;
								}
								else
										$i=$sep+1;
								$sep=-1;
								$j=$i;
								$l=0;
								$nl++;
						}
						else
								$i++;
				}
				return $nl;
		}

}

$from = $_GET['from'];
$to = $_GET['to'];
$code = $_GET['icd'];

$pdf = new NotifiableDiseases($from, $to, $code,$_GET['sclass']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetDiagnosisData();
$pdf->Output();
?>