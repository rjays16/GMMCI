<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

class SurgeryRequest extends FPDF{
	var $encounter_nr;
	var $refno;
	var $type;
	var $fontfamily_label = "Arial";
	var $fontfamily_answer = "Arial";
	var $fontstyle_label = "B";
	var $fontstyle_label2 = '';
	var $fontstyle_answer = '';
	var $fontsize_label = 9;
	var $fontsize_label2 = 12;
	var $fontsize_answer = 10;
	var $totwidth = 195;
	var $rowheight = 5;
	var $continueline = 0;
	var $nextline = 1;
	var $alignCenter = "C";
	var $alignLeft = "L";
	var $withoutborder = 0;
	var $city = "Davao City";
	var $title = "REQUEST FOR SURGERY";
	var $lineAdjustment = 1;

	function SurgeryRequest($type, $enc){
		global $db;
		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);
		$this->SetTopMargin(3);
		$this->Alignment = array('C','L','C','C','C','L','C','C');
		$this->FPDF("P", 'mm', array(216, 139.5));
		$this->type = $type;
		#$this->encounter_nr = $enc;
		$this->refno = $enc;
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
			$row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rowheight, $row['hosp_name'], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Cell($this->totwidth, $this->rowheight, $this->city, $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label2);
		$this->Cell($this->totwidth, $this->rowheight, $this->title, $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln();
	}

	function GetData(){
		global $db;
		$seg_department = new Department();
		$seg_personell = new Personell();

		$ColumnWidth = array(40,45,25,40,45);

		$sql = "SELECT som.*, e.pid, e.encounter_date, e.chief_complaint, e.encounter_type, e.er_opd_diagnosis
					FROM seg_or_main AS som
					INNER JOIN care_encounter AS e ON e.encounter_nr = som.encounter_nr
					WHERE som.ceo_refno = '$this->refno'";
		$result = $db->Execute($sql);
		$row = $result->FetchRow();

		if($row['encounter_type']==2){
			$diagnosis = $row['chief_complaint'];
		}else{
			$diagnosis = $row['er_opd_diagnosis'];
		}

		$pid = $row['pid'];
		$seg_person = new Person($pid);
		$person_info = $seg_person->getAllInfoArray();
		//print_r($person_info);
		$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
		$person_name = ucwords($person_info['name_last']) . ', ' . ucwords($person_info['name_first']) . ' ' . $middle_initial;
		$person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
		$person_age = (int)$seg_person->getAge(date('m/d/Y', strtotime($person_info['date_birth'])));
		$person_age = is_int($person_age) ? $person_age . ' years old' : '';

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rowheight, "Date and Time Requested:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put date and time requested here...
		$this->Cell($ColumnWidth[1], $this->rowheight, date("F j, Y h:i", strtotime($row['date_request'])), $this->withoutborder, $this->conitnueline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+$ColumnWidth[1], $y+($this->rowheight - $this->lineAdjustment));
		$this->Cell($ColumnWidth[2], $this->rowheight);
		$this->Cell($ColumnWidth[3], $this->rowheight, "Date and Time Received:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+$ColumnWidth[4], $y+($this->rowheight - $this->lineAdjustment));
		//put date and time received here...
		$this->Cell($ColumnWidth[4], $this->rowheight, date("F j, Y h:i", strtotime($row['encounter_date'])), $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rowheight, "Hospital Number:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put hospital number here...
		$this->Cell($ColumnWidth[1], $this->rowheight, $pid, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+$ColumnWidth[1], $y+($this->rowheight - $this->lineAdjustment));
		$this->Cell($ColumnWidth[2], $this->rowheight);

		if($this->type=='1'){
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label);
			$this->Cell($ColumnWidth[3], $this->rowheight, "Priority:", $this->withoutborder, $this->continueline, $this->alignLeft);
			$x = $this->GetX();
			$y = $this->GetY();
			$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+$ColumnWidth[4], $y+($this->rowheight - $this->lineAdjustment));
			//put priority type here...
			$this->Cell($ColumnWidth[4], $this->rowheight, $row['request_priority'], $this->withoutborder, $this->nextline, $this->alignLeft);
			//$this->Ln();
		}else{
			//$this->Ln($this->rowheight * 2);
			$this->Ln();
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rowheight, "Department:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put department here...
		$department = $seg_department->FormalName($row['dept_nr']);
		$this->Cell($ColumnWidth[1], $this->rowheight, $department, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+$ColumnWidth[1], $y+($this->rowheight - $this->lineAdjustment));
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label2, $this->fontsize_label);
		$this->Cell(13, $this->rowheight, "Name:", $this->withoutborder, $this->continueline, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		//put name here...
		$this->Cell(105, $this->rowheight, $person_name, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+105, $y+($this->rowheight - $this->lineAdjustment));
		$this->Cell(15, $this->rowheight, "Age:", $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		//put age here...
		$this->Cell(25, $this->rowheight, $person_age, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+25, $y+($this->rowheight - $this->lineAdjustment));
		$this->Cell(12, $this->rowheight, "Sex:", $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		//put sex here....
		$this->Cell(25, $this->rowheight, $person_gender, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+25, $y+($this->rowheight - $this->lineAdjustment));

		$this->Cell(17, $this->rowheight, "Diagnosis:", $this->withoutborder, 0, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put diagnosis here...
		$this->Cell($this->totwidth - 17, $this->rowheight, $diagnosis, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+178, $y+($this->rowheight - $this->lineAdjustment));

		$this->Cell(33, $this->rowheight, "Operation Procedure:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put operation procedure here
		$this->Cell(162, $this->rowheight, $row['or_procedure'], $this->withoutborder, $this->nextline,$this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+162, $y+($this->rowheight - $this->lineAdjustment));

		$this->Cell(28, $this->rowheight, "Date of Operation:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Cell(40, $this->rowheight, date("F j, Y", strtotime($row['date_operation'])), $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+40, $y+($this->rowheight - $this->lineAdjustment));
		$this->Cell(10, $this->rowheight);
		$this->Cell(40, $this->rowheight, "Cashier/Billing Clearance :", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+77, $y+($this->rowheight - $this->lineAdjustment));
		$this->Cell(77, $this->rowheight, "", $this->withoutborder, $this->nextline);

		$this->Cell(15, $this->rowheight, "OR # :", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Cell(53, $this->rowheight, "", $this->withoutborder, $this->continueline);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+53, $y+($this->rowheight - $this->lineAdjustment));
		$this->Cell(10, $this->rowheight);
		$this->Cell(40, $this->rowheight, "Amount Paid / Deposited :", $this->withoutborder, $this->continueline,$this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+20, $y+($this->rowheight - $this->lineAdjustment));
		$this->Ln($this->rowheight * 3);

		$this->Cell(20, $this->rowheight, "Encoded by:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put requested by here...
		$this->Cell(60, $this->rowheight, $row['create_id'], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+60, $y+($this->rowheight - $this->lineAdjustment));
		$this->Cell(5, $this->rowheight);
		$this->Cell(31, $this->rowheight, "Requesting Doctor :", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put requesting doctor here...
		$doc = $seg_personell->getPersonellInfo($row['dr_nr']);
		//print_r($doc);
		$doctor = strtoupper($doc['name_first'])." ".strtoupper($doc['name_middle']).". ".strtoupper($doc['name_last']).", M.D.";
		$this->Cell(79, $this->rowheight, $doctor, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Line($x, $y+($this->rowheight - $this->lineAdjustment), $x+79, $y+($this->rowheight - $this->lineAdjustment));
	}

	function Footer()
	{
		//$this->SetY(-23);
		//$this->SetFont('Arial','I',8);
		//$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
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

$pdf = new SurgeryRequest($_GET['type'], $_GET['refno']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetData();
$pdf->Output();
?>