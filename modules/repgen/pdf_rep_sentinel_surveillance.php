<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class SentinelSurveillance extends FPDF{
	var $from;
	var $to;
	var $count_rows;
	var $code1;
	var $code2;

	function SentinelSurveillance($from, $to, $code, $sclass){
		global $db;
		$this->ColumnWidth = array(8,22,56,32,15,12,72,33,25);
		$this->SetTopMargin(3);
		$this->Alignment = array('L','C','L','C','C','C','L','C','C');
		$this->FPDF("L", 'mm', 'Letter');
		$this->code1 = $code.".-";
		$this->code2 = $code."-";
		//$this->code = $code;

		if($code != 'all')
		//$this->icd_cond = "AND (ed.code_parent = '".$this->code."'";
				#$this->icd_cond = "AND (ed.code_parent = '".$this->code1."' OR ed.code_parent = '".$this->code2."')";
				#edited by VAN 05-20-09
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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',80,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 0;
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->SetFont('Arial', 'B', 14);
		$this->Ln(5);
		$this->Cell(0, 6, "SENTINEL SURVEILLANCE WORKSHEET",0,1,'C');

		$this->SetFont('Arial', 'B', 12);
		if($this->from == $this->to){
			$this->Cell(0,6, "Period Covered : As of ".date("F j, Y",strtotime($this->from)), 0,1,'C');
		}
		else{
			$this->Cell(0, 4, "Period Covered  : From ".date("F j, Y", strtotime($this->from))." to ".date("F j, Y",strtotime($this->to)),0,1,'C');
		}
		$this->Ln(5);

	}


	function GetDiagnosisData(){
		global $db;
		$this->SetLeftMargin(2);
		$prev_diag = "";

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
						$this->Cell($this->ColumnWidth[0], 4, "", "TLR",0,'L');
						$this->Cell($this->ColumnWidth[1], 4, "", "TLR",0,'L');
						$this->Cell($this->ColumnWidth[2], 4, "", "TLR",0,'C');
						$this->Cell($this->ColumnWidth[3], 4, "", "TLR",0,'C');
						$this->Cell($this->ColumnWidth[4], 4, "", "TLR",0,'C');
						$this->Cell($this->ColumnWidth[5], 4, "", "TLR", 0,'C');
						$this->Cell($this->ColumnWidth[6], 4, "", "TLR", 0,'C');
						$this->Cell($this->ColumnWidth[7], 4, "IF DENGUE", "TLR",0,'C');
						$this->Cell($this->ColumnWidth[8], 4, "", "TLR",1,'C');

						$this->Cell($this->ColumnWidth[0], 4, "", "LR",0,'C');
						$this->Cell($this->ColumnWidth[1], 4, "CASE NO.", "LR",0,'C');
						$this->Cell($this->ColumnWidth[2], 4, "FULL NAME", "LR",0, 'C');
						$this->Cell($this->ColumnWidth[3], 4, "DATE ADMITTED", "LR",0,'C');
						$this->Cell($this->ColumnWidth[4], 4, "AGE", "LR",0, 'C');
						$this->Cell($this->ColumnWidth[5], 4, "SEX", "LR",0,'C');
						$this->Cell($this->ColumnWidth[6], 4, "COMPLETE ADDRESS", "LR",0,'C');
						$this->Cell($this->ColumnWidth[7], 4, "Platelet below 100,000", "LR", 0,'C');
						$this->Cell($this->ColumnWidth[8], 4, "FATALITY","LR",1,'C');

						$this->Cell($this->ColumnWidth[0], 4, "", "BLR",0,'C');
						$this->Cell($this->ColumnWidth[1], 4, "", "BLR",0,'C');
						$this->Cell($this->ColumnWidth[2], 4, "", "BLR",0,'C');
						$this->Cell($this->ColumnWidth[3], 4, "", "BLR",0,'C');
						$this->Cell($this->ColumnWidth[4], 4, "", "BLR",0,'C');
						$this->Cell($this->ColumnWidth[5], 4, "", "BLR",0,'C');
						$this->Cell($this->ColumnWidth[6], 4, "", "BLR",0,'C');
						$this->Cell($this->ColumnWidth[7], 4, "or less per mm3", "BLR",0,'C');
						$this->Cell($this->ColumnWidth[8], 4, "", "BLR",0,'C');
						$this->Ln();
						$this->SetFont('Arial', '', 9);

						$prev_diag = $diag;
						$i=1;
					}

					#--- transferred by VAN and delete the function GetDiagnosisOccurrence
						if (($rows['discharge_date']!='0000-00-00')&&(!(empty($rows['discharge_date']))))
							$discharged_date = date("m/d/Y",strtotime($rows['discharge_date']));
						else
							$discharged_date = 'still in';

						if (($rows['admission_dt']!='0000-00-00')&&(!(empty($rows['admission_dt']))))
							$admission_date = date("m/d/Y h:i A",strtotime($rows['admission_dt']));

						if (($rows['fatality']!='0000-00-00')&&(!(empty($rows['fatality']))))
							$fatality = date("m/d/Y",strtotime($rows['fatality']));

						$this->Row(array($i,
											 trim($rows['encounter_nr']),
											 mb_strtoupper(trim($rows['patient_name'])),
											 trim($admission_date),
											 trim($rows['age']),
											 strtoupper(trim($rows['sex'])),
											 mb_strtoupper(trim($rows['address'])),
											 trim($rows['if_dengue']),
											 $fatality));
						$i++;
						#---
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
					$nb2=$this->NbLines($this->ColumnWidth[1],$data[1]);
					$nb3=$this->NbLines($this->ColumnWidth[5],$data[5]);
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

					//$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
					//print_r($nb2, $nb3);

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

$pdf = new SentinelSurveillance($from, $to, $code,$_GET['sclass']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetDiagnosisData();
$pdf->Output();
?>