<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');

class ClinicalSummary extends FPDF{
	var $from;
	var $to;
	var $count_rows;
	var $refno;

	var $chief_complaint = '';
	var $pe_history = '';
	var $adm_impression = '';
	var $medications = '';
	var $procedures = '';
	var $final_diagnosis = '';
	var $preop_diagnosis = '';
	var $op_performed = '';
	var $date_op = '0000-00-00';
	var $complicatons = '';
	var $recommendations = '';
	var $discharge_condition = '';

	function ClinicalSummary($refno){
		global $db;
		$this->ColumnWidth = array(50,28,28,15,71,12,70,25,53);
		$this->SetTopMargin(3);
		$this->Alignment = array('L','C','C','C','L','C','L','L','L');
		$this->FPDF("P", 'mm', 'Letter');
		$this->refno = $refno;
	}

	function Header() {
		global $root_path, $db;
		$rowheight = 5;

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

		$this->SetFont('Arial', '', 9);
		$this->Cell(0, $rowheight, $row['hosp_country'], 0, 1, 'C');
		$this->Cell(0, $rowheight, "Center for Health Development for Southern Mindanao", 0, 1, 'C');
		$this->SetFont('Arial', '', 10);
		$this->Cell(0, $rowheight, strtoupper($row['hosp_name']), 0, 1, 'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(0, $rowheight, $row['hosp_addr1'], 0, 1, 'C');
		$this->SetFont('Arial', 'B', 11);
		$this->Cell(0, $rowheight, "MRFI-02", 0, 1, 'R');
		$this->Cell(0, $rowheight, "CLINICAL SUMMARY", 0, 1, 'C');
		$this->SetFont('Arial', '', 11);
		$this->Cell(0, $rowheight, "(Medical/Surgical/Pedia)", 0, 1, 'C');
		$this->Ln();

	}

	function GetBasicInfo(){
		global $db;
		$rowheight = 5;
		$seg_ops = new SegOps();

		$nr = $seg_ops->getOpRequestNrByRefNo($this->refno);
		$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
		$or_main_info = $seg_ops->get_or_main_basic_info($this->refno);

		$seg_person = new Person($basic_info['pid']);
		$person_info = $seg_person->getAllInfoArray();
		//print_r($person_info);
		$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
		$person_name = $person_info['name_last'] . ', ' . $person_info['name_first'] . ' ' . $middle_initial;
		$person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
		$person_age = (int)$seg_person->getAge(date('m/d/Y', strtotime($person_info['date_birth'])));
		$person_age = is_int($person_age) ? $person_age . ' years old' : 'Not Specified';

		$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));

		$enc = $or_main_info['encounter_nr'];

		$sql = "SELECT encounter_type, encounter_date, admission_dt, discharge_date
						FROM care_encounter WHERE encounter_nr = '".$enc."'";
		$result = $db->Execute($sql);
		$row_info = $result->FetchRow();

		if($row_info['encounter_type'] == 1 || $row_info['encounter_type'] == 2){
			$date_admission = date("m/d/Y", strtotime($row_info['encounter_date']));
		}else if($row_info['encounter_type'] == 3 || $row_info['encounter_type'] == 4){
			$date_admission = date("m/d/Y", strtotime($row_info['admission_dt']));
		}
		if($row_info['discharge_date'])
			$date_discharge = date("m/d/Y", strtotime($row_info['discharge_date']));
		else
			$date_discharge = "Not discharged";

		$this->SetFont('Arial', '', 9);
		$this->Cell(11, $rowheight, "NAME:", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+90, $y+($rowheight-0.5));
		$this->Cell(90, $rowheight, strtoupper($person_name), 0, 0, 'L');  //put name here
		$this->Cell(9, $rowheight, "AGE:", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+20, $y+($rowheight-0.5));
		$this->Cell(20, $rowheight, $person_age, 0, 0, 'L'); //put age here
		$this->Cell(9, $rowheight, "SEX:", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+13, $y+($rowheight-0.5));
		$this->Cell(13, $rowheight, $person_gender, 0, 0, 'L'); //put sex here
		$this->Cell(21, $rowheight, "HOSPITAL #:", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+22, $y+($rowheight-0.5));
		$this->Cell(22, $rowheight, $basic_info['pid'], 0, 1, 'L'); //put pid

		$this->Cell(18, $rowheight, "ADDRESS:", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+92, $y+($rowheight-0.5));
		$this->Cell(92, $rowheight, $person_address, 0, 0, 'L'); //put address here
		$this->Cell(19, $rowheight, "DATE ADM.", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+20, $y+($rowheight-0.5));
		$this->Cell(20, $rowheight, $date_admission, 0, 0, 'L'); //put date admission here
		$this->Cell(22, $rowheight, "DATE DISCH.", 0, 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight-0.5), $x+24, $y+($rowheight-0.5));
		$this->Cell(24, $rowheight, $date_discharge, 0, 1, 'L');  //put date discharged here

	}

	function GetRecord() {
		global $db;
		$rowheight = 5;
		$col = 65;
		$seg_ops = new SegOps();
		$or_main_info = $seg_ops->get_or_main_basic_info($this->refno);

		$this->SetFont('Arial', '', 9);
		$this->Cell($col, $rowheight, "", "R", 1);
		$this->Cell($col, $rowheight, "ORDER OF RECORDING", "R", 1, 'C');
		$this->Cell(23, $rowheight, "1. Brief History:", 0, 0, 'L');
		$this->Cell(2, $rowheight, "-", 0,0,'C');
		$this->Cell($col-25, $rowheight, "Chief Complaint", "R", 0, 'L');
		$xcol = $this->GetX();
		$ycol = $this->GetY();
		$this->Ln();
		$this->Cell(23, $rowheight);
		$this->Cell(2, $rowheight, "-", 0, 0, 'C');
		$this->Cell($col-25, $rowheight, "History & P.E.", "R", 1, 'L');
		$this->Cell($col, $rowheight, "2. Adm. Impression", "R", 1, 'L');
		$this->Cell($col, $rowheight, "3. Medications", "R", 1, 'L');
		$this->Cell($col, $rowheight, "4. Procedures", "R", 1, 'L');
		$this->Cell($col, $rowheight, "5. Laboratory results, ECG, X-ray, Biopsy,", "R", 1, 'L');
		$this->Cell(15, $rowheight);
		$this->Cell($col-15, $rowheight, "etc. (specify results)", "R", 1, "L");
		$this->Cell(33, $rowheight, "6. Course in the Ward:", 0, 0, 'L');
		$this->Cell($col-33, $rowheight, "> no. of infection", "R", 1, 'L');
		$this->Cell(33, $rowheight);
		$this->Cell($col-33, $rowheight, "> no. of referrals", "R", 1, 'L');
		$this->Cell($col, $rowheight, "7. Final Diagnosis", "R", 1, 'L');
		$this->Cell($col, $rowheight, "8. Surgery if any:", "R", 1, 'L');
		$this->Cell(15, $rowheight);
		$this->Cell($col-15, $rowheight, "Pre-Operative Diagnosis", "R", 1, 'L');
		$this->Cell(15, $rowheight);
		$this->Cell($col-15, $rowheight, "Operation Performed", "R", 1, 'L');
		$this->Cell(15, $rowheight);
		$this->Cell($col-15, $rowheight, "Date of Operation", "R", 1, 'L');
		$this->Cell(15, $rowheight);
		$this->Cell($col-15, $rowheight, "Complications", "R", 1, 'L');
		$this->Cell($col, $rowheight, "9. Recommendations", "R", 1, 'L');
		$this->Cell($col, $rowheight, "10. Condition on Discharge", "R", 1, 'L');
		$this->Cell($col, $rowheight, "11. Physician's Signature", "R", 0, 'L');
		$xcol2 = $this->GetX();
		$ycol2 = $this->GetY();
		$this->Line($xcol2, $ycol2, $xcol2, $ycol2+110);

		//Data part....
		$this->SetXY($xcol, $ycol);
		$this->SetFont('Arial', '', 9);

		$this->Cell(5, $rowheight, "1.)", 0,0, 'L');
		$this->MultiCell(105, $rowheight, $this->chief_complaint, 0, 'L');  //put chief complaint here
		$this->Ln();

		$this->SetX($xcol);
		$this->Cell(5, $rowheight, "2.)", 0,0, 'L');
		$this->MultiCell(105, $rowheight, $this->adm_impression, 0, 'L');  //put adm. impression
		$this->Ln();

		$this->SetX($xcol);
		$this->Cell(5, $rowheight, "3.)", 0,0, 'L');
		$this->MultiCell(105, $rowheight, $this->medications, 0, 'L'); //put medications here
		$this->Ln();

		$this->SetX($xcol);
		$this->Cell(5, $rowheight, "4.)", 0,0, 'L');
		$this->MultiCell(105, $rowheight, $this->procedures, 0, 'L'); //put procedures here
		$this->Ln();

		$this->SetX($xcol);
		$this->Cell(5, $rowheight, "5.)", 0,0, 'L');
		$this->SetFont('Arial', 'U', 9);
		$this->MultiCell(105, $rowheight, "Please see attached paper", 0, 'L'); //lab result
		$this->Ln();

		$this->SetFont('Arial', '', 9);
		$this->SetX($xcol);
		$this->Cell(5, $rowheight, "6.)", 0,0, 'L');
		$this->MultiCell(105, $rowheight, "", 0, 'L'); //put course in ward here
		$this->Ln();

		$this->SetX($xcol);
		$this->Cell(5, $rowheight, "7.)", 0,0, 'L');
		$this->MultiCell(105, $rowheight, $this->final_diagnosis, 0, 'L'); //put final diagnosis here
		$this->Ln();

//		$post_op_details = $seg_ops->get_or_main_post_details($or_main_info['or_main_refno']);
//		$pre_op = "not specified";
//		$op_performed = $post_op_details['operation_performed'];

		$this->SetX($xcol);
		$this->Cell(5, $rowheight, "8.)", 0,0, 'L');
		$this->SetX($xcol+5);
		$this->MultiCell(105, $rowheight, "Pre-Operative Diagnosis: ".$this->preop_diagnosis, 0, 'L');
		$this->SetX($xcol+5);
		$this->MultiCell(105, $rowheight, "Operation Performed: ".$this->op_performed, 0, 'L');
		$this->SetX($xcol+5);
		$this->MultiCell(105, $rowheight, "Date of Operation: ".date("F j, Y", strtotime($this->date_op)), 0, 'L');
		$this->SetX($xcol+5);
		$this->MultiCell(105, $rowheight, "Complications: ".$this->complicatons, 0, 'L');
		$this->Ln();

		$this->SetX($xcol);
		$this->Cell(5, $rowheight, "9.)", 0,0, 'L');
		$this->MultiCell(105, $rowheight, $this->recommendations, 0, 'L'); //put recommendations here
		$this->Ln();

		$this->SetX($xcol);
		$this->Cell(6, $rowheight, "10.)", 0,0, 'L');
		$this->MultiCell(104, $rowheight, $this->discharge_condition, 0, 'L'); //put condition on discharge here

	}

	function Footer()
	{
		$this->SetY(-23);
		$this->Line(140, 256, 210, 256);
		$this->SetFont('Arial', '', 9);
		$this->Cell(0, 4, "(Name & Signature of Physician In-Charge)", 0, 1, 'R');
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

$pdf = new ClinicalSummary($_GET['refno']);
include_once('or_clinical_summary_data.php');

$pdf->chief_complaint = getBriefHistory($pid);
$pdf->pe_history = "";
$pdf->adm_impression = getAdmImpression($encounter_nr);
$pdf->medications = getMedications($encounter_nr);
$pdf->procedures = getProcedures($encounter_nr);
$pdf->final_diagnosis = getDoctorDiagnosis($encounter_nr);
$pdf->preop_diagnosis = getPreOpDiagnosis();
$pdf->op_performed = $op_performed;
$pdf->date_op = strftime("%m/%d/%Y", strtotime($op_date));
$pdf->complicatons = $complication;
$pdf->recommendations = $recommendations;
$pdf->discharge_condition = getDischargeCondition($encounter_nr);

$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetBasicInfo();
$pdf->GetRecord();
$pdf->Output();
?>