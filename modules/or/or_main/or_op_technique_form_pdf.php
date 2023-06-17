<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/inc_date_format_functions.php');

class OPTechniqueForm extends FPDF{
	var $from;
	var $to;
	var $count_rows;
	var $refno;
	var $pid;

	function OPTechniqueForm($refno){
		global $db;
		//echo "refno = ".$refno;
		$this->ColumnWidth = array(50,28,28,15,71,12,70,25,53);
		$this->SetTopMargin(3);
		$this->Alignment = array('L','C','C','C','L','C','L','L','L');
		$this->FPDF("P", 'mm', 'Letter');
		//$this->code1 = trim($code).".-";
		//$this->code2 = trim($code)."-";
		//$this->code = $code;
		$this->refno = $refno;

		if($code != 'all')
		//$this->icd_cond = "AND (ed.code_parent = '".$this->code."'";
		$this->icd_cond = "AND (ed.code_parent = '".$this->code1."' OR ed.code_parent = '".$this->code2."')";
	else
		$this->icd_cond = "";
		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));
	}

	function Header() {
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		$rowheight = 5;

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

		$this->SetFont('Arial', '', 9);
		$this->Cell(0, $rowheight, $row['hosp_country'], 0, 1, 'C');
		$this->Cell(0, $rowheight, "Center for Health Development for Southern Mindanao", 0, 1, 'C');
		$this->SetFont('Arial', '', 10);
		$this->Cell(0, $rowheight, $row['hosp_name'], 0, 1, 'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(0, $rowheight, $row['hosp_addr1'], 0, 1, 'C');

		$this->SetFont('Arial', 'B', 12);
		$this->Cell(0, $rowheight, "MRFI 17 - 1", 0, 1, 'R');

		$this->SetFont('Arial', 'B', 14);
		$this->Cell(0, $rowheight, "OPERATIVE TECHNIQUE FORM", 0, 1, 'C');
		$this->Ln();

		/*
		$this->SetFont('Arial', 'B', 12);
		if($this->from == $this->to){
			$this->Cell(0,6, "As of ".date("F j, Y",strtotime($this->from)), 0,1,'C');
		}
		else{
			$this->Cell(0, 4, "From ".date("F j, Y", strtotime($this->from))." to ".date("F j, Y",strtotime($this->to)),0,1,'C');
		}  */
		$this->Ln(5);


	}

	function GetData(){
		global $db;
		$seg_ops = new SegOps();

		$rowheight = 5;

		$nr = $seg_ops->getOpRequestNrByRefNo($this->refno);
		$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
		$or_main_info = $seg_ops->get_or_main_basic_info($this->refno);
		//print_r($basic_info);

		//echo 'pid= '.$basic_info['pid'];
		$seg_person = new Person($basic_info['pid']);
		$person_info = $seg_person->getAllInfoArray();
		//print_r($person_info);
		$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
		$person_name = $person_info['name_last'] . ', ' . $person_info['name_first'] . ' ' . $middle_initial;
		$person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
		$person_age = (int)$seg_person->getAge(date('m/d/Y', strtotime($person_info['date_birth'])));
		$person_age = is_int($person_age) ? $person_age . ' years old' : 'Not Specified';

		$seg_department = new Department();
		$department = $seg_department->FormalName($or_main_info['dept_nr']);

		$post_op_details = $seg_ops->get_or_main_post_details($or_main_info['or_main_refno']);
		//$length = date("H:i", strtotime($post_op_details['time_finished'])) - date("H:i", strtotime($post_op_details['time_started']));
		//$length = date_diff(date("H:i", strtotime($post_op_details['time_finished'])), date("H:i", strtotime($post_op_details['time_started'])));
		//$start_hour = date("H:i", strtotime($post_op_details['time_finished']));
		//$start_min =  date("H:i", strtotime($post_op_details['time_started']));
		//echo "start= ".$start_hour."-".$start_min."= ".$length;
		$or_refno = $or_main_info['or_main_refno'];
		$sqldate = "SELECT extract(HOUR FROM TIMEDIFF(time_finished, time_started)) AS length_hour,
								extract(MINUTE FROM TIMEDIFF(time_finished, time_started)) AS length_minute
								FROM seg_or_main_post WHERE or_main_refno = '$or_refno';";
		$resultdate = $db->Execute($sqldate);
		$rowdate = $resultdate->FetchRow();

		$surgeon_pid = $seg_ops->getOpsPersonellNr($this->refno, 7);
		$asst = $seg_ops->getOpsPersonellNr($this->refno, 8);
		//print_r($surgeon_pid);
		$personell = new Personell;
		//$surgeon = $personell->get_Person_name($surgeon_pid[0]);
		//$assistant = $personell->get_Person_name($asst[0]);
		//$surgeon = $seg_ops->getOpsPersonellInfo($this->refno, 7);
		//print_r($assistant);
		//print_r($surgeon);

		//$anesthesia = $seg_ops->get_or_main_anesthesia($or_main_info['or_main_refno']);
		$or_main_refno = $or_main_info['or_main_refno'];

		$this->sql = "SELECT c.nr as anesthesia_nr, c.name, s.anesthesia, s.anesthesia_category as `category`, s.anesthesia_specific as `specific`,
									time_format(s.time_begun, '%h:%i') as time_begun,
									time_format(s.time_ended, '%h:%i') as time_ended,
									time_format(s.time_begun, '%p') as tb_meridian,
									time_format(s.time_ended, '%p') as te_meridian
					FROM care_type_anaesthesia c INNER JOIN seg_encounter_anesthesia s ON (c.nr=s.anesthesia)

					WHERE s.or_main_refno = $or_main_refno";
		$result=$db->Execute($this->sql);
		if($result){
			 $row=$result->FetchRow();
		}


		$this->SetFont('Arial', '', 9);
		$this->Cell(25, $rowheight, "Name of Patient: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+1, $y+($rowheight-1), $x+110, $y+($rowheight-1));
		$this->Cell(110, $rowheight, ucwords($person_name), 0, 0, 'L');  //put name here
		$this->Cell(8, $rowheight, "Age: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-1), $x+30, $y+($rowheight-1));
		$this->Cell(30, $rowheight, $person_age, 0, 0, 'L'); //put age here
		$this->Cell(8, $rowheight, "Sex: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-1), $x+15, $y+($rowheight-1));
		$this->Cell(15, $rowheight, $person_gender, 0, 1, 'L');  //put sex here

		$this->Cell(26, $rowheight, "Hospital Number: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-1), $x+23, $y+($rowheight-1));
		$this->Cell(23, $rowheight, $basic_info['pid'], 0, 0, 'L'); //put hospital number here
		$this->Cell(22, $rowheight, "Case Number: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-1), $x+22, $y+($rowheight-1));
		$this->Cell(22, $rowheight, $or_main_info['encounter_nr'], 0, 0, 'L');
		$this->Cell(27, $rowheight, "Date of Operation: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-1), $x+27, $y+($rowheight-1));
		$this->Cell(27, $rowheight, date("m/d/Y", strtotime($or_main_info['date_operation'])), 0,0, 'L'); //put date of operation here
		$this->Cell(22, $rowheight, "Dept./Service: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-1), $x+27, $y+($rowheight-1));
		$this->Cell(27, $rowheight, $department, 0, 1, 'L');

		$this->Cell(17, $rowheight, "Diagnosis: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-1), $x+178, $y+($rowheight-1));
		$this->Cell(178, $rowheight, $post_op_details['post_op_diagnosis'], 0, 1, 'L');  //put diagnosis here

		$this->Cell(26, $rowheight, "Procedure Done: ", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-1), $x+170, $y+($rowheight-1));
		$this->Cell(169, $rowheight, $or_main_info['or_procedure'], 0,1,'L'); //put procedure done here

		$this->Cell(29, $rowheight, "Type of Anesthesia:", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+70, $y+($rowheight-0.5));
		$this->Cell(70, $rowheight, $row['category'], 0,0, 'L');  //type of anesthesia
		$this->Cell(39, $rowheight, "Total Length of Procedure:", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+57, $y+($rowheight-0.5));
		if($rowdate['length_hour']>1)
			$hour = "hours ";
		else
			$hour = "hour ";

		if($rowdate['length_minute']>1)
			$minute = "minutes";
		else
			$minute = "minute";
		$this->Cell(57, $rowheight, $rowdate['length_hour']." ".$hour.$rowdate['length_minute']." ".$minute, 0, 1, 'L');  //total length of procedure

		$this->Cell(17, $rowheight, "Surgeon/s:", 0, 0, 'L');
		$xsave = $this->GetX();
		$ysave = $this->GetY();
		//$this->Line($x, $y+($rowheight-0.5), $x+82, $y+($rowheight-0.5));
		//$this->Cell(82, $rowheight, $surgeon['dr_name'].", M.D.", 0, 0, 'L'); //put surgeon/s here
		$num = count($surgeon_pid);
		//echo "num= ".$num;
		for ($n=1; $n<=$num; $n++){
			$x = $this->GetX();
			$y = $this->GetY();
			$this->Line($x, $y+($rowheight-0.5), $x+82, $y+($rowheight-0.5));
			$surgeon = $personell->get_Person_name($surgeon_pid[$n-1]);
			 //print_r($surgeon);
			 //$this->MultiCell(82, $rowheight, $surgeon['dr_name']);
			 $this->MultiCellBlt(82,6,"$n)",$surgeon['dr_name']);
		}

		$this->SetXY($xsave+82, $ysave);
		$this->Cell(17, $rowheight, "Assistant/s:", 0, 0, 'L');
		$num2 = count($asst);
		//echo "num2= ".$num2;
		for($m = 1; $m<=$num2; $m++){
			$x2 = $this->GetX();
			$y2 = $this->GetY();
			$this->Line($x2, $y2+($rowheight-0.5), $x2+82, $y2+($rowheight-0.5));
			$assistant = $personell->get_Person_name($asst[$m-1]);
			$this->MultiCellBlt(80,6,"$m)",$assistant['dr_name']);
		}

		if($y > $y2){
			$this->SetY($y+$rowheight);
		}elseif($y < $y2){
			$this->SetY($y2+$rowheight);
		}else{
			$this->SetY($y+$rowheight);
		}


		/*
		$this->Cell(17, $rowheight, "Assistant/s:", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+80, $y+($rowheight-0.5));
		$this->Cell(80, $rowheight, $assistant['dr_name'].", M.D.", 0,1,'L');  //put assistant/s here   */
		$this->Ln($rowheight);

		$this->Cell(0, $rowheight, "TECHNIQUE:", 0,1,'L');
		$this->Ln($rowheight*5);

		for($cnt=1; $cnt<=21; $cnt++){
			$x1 = $this->GetX();
			$y1 = $this->GetY();
			$this->Line($x1, $y1+(($rowheight*$cnt)-0.5), $x1+195, $y1+(($rowheight*$cnt)-0.5));
		}



		$this->MultiCell(195, $rowheight, $post_op_details['or_technique'], 0, 'L'); //technique


	}

	function Footer(){
		$this->SetY(-23);
		$this->Line(140, 256, 210, 256);
		$this->SetFont('Arial', '', 9);
		$this->Cell(0, 4, "Signature of Physician Over Printed Name", 0, 1, 'R');
	}

	//-------------------------------------
	 //MultiCell with bullet
		function MultiCellBlt($w, $h, $blt, $txt, $border=0, $align='J', $fill=false)
		{
				//Get bullet width including margins
				$blt_width = $this->GetStringWidth($blt)+$this->cMargin*2;

				//Save x
				$bak_x = $this->x;

				//Output bullet
				$this->Cell($blt_width,$h,$blt,0,'',$fill);

				//Output text
				$this->MultiCell($w-$blt_width,$h,$txt,$border,$align,$fill);

				//Restore x
				$this->x = $bak_x;
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



$pdf = new OPTechniqueForm($_GET['refno']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetData();
//$pdf->GetDiagnosisData();
$pdf->Output();
?>