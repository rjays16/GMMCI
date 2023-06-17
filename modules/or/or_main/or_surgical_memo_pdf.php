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
require_once($root_path.'include/care_api_classes/class_vitalsign.php'); //load the vital sign class
include_once($root_path.'include/care_api_classes/class_encounter.php');

class SurgicalMemorandum extends FPDF{
	var $from;
	var $to;
	var $count_rows;
	var $code1;
	var $code2;
	var $total_w = 200;
	var $refno;

	var $fontfamily_label = "Arial";
	var $fontfamily_answer = "Arial";
	var $fontstyle_label = 'B';
	var $fontstyle_label2 = "B";
	var $fontstyle_label3 = "BU";
	var $fontstyle_answer = "";
	var $fontsize_label = 8.5;
	var $fontsize_answer = 9;
	var $fontsize_label2 = 12;
	var $withoutborder = 0;
	var $continueline = 0;
	var $borderBottom = "B";
	var $nextline = 1;
	var $alignLeft = "L";
	var $alignRight = "R";
	var $alignCenter = "C";
	var $ColumnWidthAnes = array(60,60,40,40);
	var $rheight = 5;

	function SurgicalMemorandum($refno){
		global $db;
		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);
		$this->SetTopMargin(3);
		$this->Alignment = array('L','L','C','C');
		$this->FPDF("P", 'mm', 'Letter');
		$this->refno = $refno;
		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));
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

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		//$this->Cell(0, $rowheight, "DMC FORM NO. 61", 0,1, 'L');
		$this->Cell(0, $rowheight, $row['hosp_country'], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(0, $rowheight, $row['hosp_agency'], $this->withoutborder,$this->nextline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label2);
		$this->Cell(0, $rowheight, $row['hosp_name'], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(0, $rowheight, $row['hosp_addr1'], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln($rowheight*2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label3, $this->fontsize_label2);
		$this->Cell(0, $rowheight, "SURGICAL MEMORANDUM", $this->withoutborder, $this->nextline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->Line($x, $y+($rowheight), $x+$this->total_w, $y+$rowheight);
		$this->Ln();
	}

	function GetData(){
	 global $db;
	 $seg_ops = new SegOps();
	 $vital_sign = new SegVitalsign();
	 $enc_obj=new Encounter;
	 $rowheight = 5;

	 $nr = $seg_ops->getOpRequestNrByRefNo($this->refno);
	 $basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
	 $or_main_info = $seg_ops->get_or_main_basic_info($this->refno);
	 $encounter_nr = $or_main_info['encounter_nr'];
	 $encInfo = $enc_obj->getEncounterInfo($encounter_nr);

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

	 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
	 $this->Cell(39, $rowheight, "FAMILY NAME", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(59, $rowheight, "FIRST NAME", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(14, $rowheight, "MI", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(24, $rowheight, "AGE", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight,":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(29, $rowheight, "DEPARTMENT", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(29, $rowheight, "HOSP. NO.", $this->withoutborder, $this->nextline, $this->alignCenter);

	 //put first line of data here
	 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
	 $this->Cell(40, $rowheight, strtoupper($person_info['name_last']), $this->borderBottom, $this->continueline, $this->alignCenter); //family name
	 $this->Cell(60, $rowheight, strtoupper($person_info['name_first']), $this->borderBottom, $this->continueline, $this->alignCenter); //first name
	 $this->Cell(15, $rowheight, strtoupper($middle_initial), $this->borderBottom, $this->continueline, $this->alignCenter); //middle initial
	 $this->Cell(25, $rowheight, $person_age, $this->borderBottom, $this->continueline, $this->alignCenter); //age
	 $this->Cell(30, $rowheight, $department, $this->borderBottom, $this->continueline, $this->alignCenter); //department
	 $this->Cell(30, $rowheight, $basic_info['pid'], $this->borderBottom, $this->nextline, $this->alignCenter); //hosp num

	 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
	 $this->Cell(39, $rowheight, "DATE OF OPERATION", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(29, $rowheight, "WEIGHT", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(29, $rowheight, "BP", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(29, $rowheight, "TEMP", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(29, $rowheight, "PULSE", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(40, $rowheight, "RESPIRATION", $this->withoutborder, $this->nextline, $this->alignCenter);

	 //put second line of data here
	 $vs = $vital_sign->get_latest_vital_signs($basic_info['pid'], $or_main_info['encounter_nr']);

	 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label);
	 $this->Cell(40, $rowheight, date("F j, Y", strtotime($or_main_info['date_operation'])), $this->borderBottom, $this->continueline, $this->alignCenter); //date of operation
	 $this->Cell(30, $rowheight, "", $this->borderBottom, $this->continueline, $this->alignCenter); //weight
	 $this->Cell(30, $rowheight, $vs['systole']."/".$vs['diastole']." mm HG", $this->borderBottom, $this->continueline, $this->alignCenter); //BP
	 $this->Cell(30, $rowheight, $vs['temp']." C", $this->borderBottom, $this->continueline, $this->alignCenter); //temp
	 $this->Cell(30, $rowheight, $vs['pulse_rate']." b/m", $this->borderBottom, $this->continueline, $this->alignCenter); //pulse
	 $this->Cell(40, $rowheight, $vs['resp_rate']." br/m", $this->borderBottom, $this->nextline, $this->alignCenter); //respiration
	 $x1 = $this->GetX();

	 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
	 $this->Cell(100, $rowheight, "SURGEON", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
	 $this->Cell(99, $rowheight, "ASSISTANT SURGEON/S", $this->withoutborder, $this->nextline, $this->alignCenter);
	 $xsave = $this->GetX();
	 $ysave = $this->GetY();

	 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
	 $surgeon_pid = $seg_ops->getOpsPersonellNr($this->refno, 7);
	 $asst = $seg_ops->getOpsPersonellNr($this->refno, 8);
	 $anes = $seg_ops->getOpsPersonellNr($this->refno, 12);
	 $inst = $seg_ops->getOpsPersonellNr($this->refno, 10);
	 #echo "inst= ".$inst;
	 $scrub = $seg_ops->getOpsPersonellNr($this->refno, 9);
	 //print_r($surgeon_pid);
	 $personell = new Personell;

	 $num = count($surgeon_pid);
	 if($surgeon_pid){
		 for ($n=1; $n<=$num; $n++){
			//$x = $this->GetX();
			//$y = $this->GetY();
			//$this->Line($x, $y+($rowheight-0.5), $x+82, $y+($rowheight-0.5));
			$surgeon = $personell->get_Person_name($surgeon_pid[$n-1]);
			 //print_r($surgeon);
			 //$this->MultiCell(82, $rowheight, $surgeon['dr_name']);
			 $this->MultiCellBlt(100,6,"$n)",strtoupper($surgeon['dr_name']));
		}
	 }
		$ysurg = $this->GetY();

		$this->SetXY($xsave+100, $ysave);
		$num2 = count($asst);
		//echo "num2= ".$num2;
		if($asst){
			for($m = 1; $m<=$num2; $m++){
			//$x2 = $this->GetX();
			$y2 = $this->GetY();
			//$this->Line($x2, $y2+($rowheight-0.5), $x2+82, $y2+($rowheight-0.5));
			$assistant = $personell->get_Person_name($asst[$m-1]);
			$this->MultiCellBlt(99,6,"$m)",strtoupper($assistant['dr_name']));
			}
		}
		$yasst = $this->GetY();

		if($ysurg > $yasst){
			$this->SetY($ysurg);
			$this->Line($x1, $ysurg, $x1+$this->total_w, $ysurg);
		}
		else{
			$this->SetY($yasst);
			$this->Line($x1, $yasst, $x1+$this->total_w, $yasst);
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(66, $rowheight, "ANESTHESIOLOGIST/S", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(66, $rowheight, "INSTRUMENT NURSE", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(66, $rowheight, "SPONGE NURSE", $this->withoutborder, $this->nextline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$num3 = count($anes);
		if($anes){
			for($a = 1; $a<=$num3; $a++){
			//$x2 = $this->GetX();
			$y2 = $this->GetY();
			//$this->Line($x2, $y2+($rowheight-0.5), $x2+82, $y2+($rowheight-0.5));
			$anesthesiologist = $personell->get_Person_name($anes[$a-1]);
			$this->MultiCellBlt(66,6,"$a)",strtoupper($anesthesiologist['dr_name']));
			}
		}
		$yanes = $this->GetY();

		$this->SetXY($x+67, $y);
		$num4 = count($inst);
		#echo $num4;
		if($inst){
			for($a = 1; $a<=$num4; $a++){
			//$x2 = $this->GetX();
			$y2 = $this->GetY();
			//$this->Line($x2, $y2+($rowheight-0.5), $x2+82, $y2+($rowheight-0.5));
			$instrument = $personell->get_Person_name($inst[$a-1]);
			$this->MultiCellBlt(66,6,"$a)",strtoupper($instrument['dr_name']));
			}
		}
		$yinst = $this->GetY();

		$this->SetXY($x+134, $y);
		$num5 = count($scrub);
		if($scrub){
			for($a = 1; $a<=$num5; $a++){
			//$x2 = $this->GetX();
			$y2 = $this->GetY();
			//$this->Line($x2, $y2+($rowheight-0.5), $x2+82, $y2+($rowheight-0.5));
			$scrubnurse = $personell->get_Person_name($scrub[$a-1]);
			$this->MultiCellBlt(66,6,"$a)",strtoupper($scrubnurse['dr_name']));
			}
		}
		$yscrub = $this->GetY();

		$max = $yanes;
		if($max < $yinst){
			$max = $yinst;
		}
		if($max < $yscrub){
			$max = $yscrub;
		}
		$this->Line($x, $max, $x+200, $max);

		$this->SetXY($x, $max);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(59, $rowheight, "ANESTHESIA", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(59, $rowheight, "ANESTHETIC", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(39, $rowheight, "TIME BEGUN", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(40, $rowheight, "TIME ENDED", $this->withoutborder, $this->nextline,$this->alignCenter);

		//put anesthesia, anesthetic, time begun, time ended
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$or_main_refno = $or_main_info['or_main_refno'];
		/*
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

		$this->sql2 = "SELECT cp.artikelname FROM care_pharma_products_main AS cp
										INNER JOIN seg_encounter_anesthetic AS sea ON sea.anesthetic_id = cp.bestellnum
										WHERE or_main_refno = '".$or_main_refno."'";
		$result2 = $db->Execute($this->sql2);
		if($result2){
			$row2=$result2->FetchRow();
		} */

		$this->sql = "SELECT s.anesthesia_care_id ,c.nr as anesthesia_nr, c.name, s.anesthesia, s.anesthesia_category as `category`, s.anesthesia_specific as `specific`,
									time_format(s.time_begun, '%h:%i') as time_begun,
									time_format(s.time_ended, '%h:%i') as time_ended,
									time_format(s.time_begun, '%p') as tb_meridian,
									time_format(s.time_ended, '%p') as te_meridian
					FROM care_type_anaesthesia c INNER JOIN seg_encounter_anesthesia s ON (c.nr=s.anesthesia)

					WHERE s.or_main_refno = $or_main_refno";
					//echo $this->sql."<br>";
		$result=$db->Execute($this->sql);
		if($result){
			 while($row=$result->FetchRow()){
				 $id = $row['anesthesia_care_id'];


					 $this->sql2 = "SELECT cp.artikelname FROM care_pharma_products_main AS cp
										INNER JOIN seg_encounter_anesthetic AS sea ON sea.anesthetic_id = cp.bestellnum
										WHERE or_main_refno = '".$or_main_refno."'
										AND sea.anesthesia_care_id = '".$id."'";
										//echo $this->sql2;
					 $result2 = $db->Execute($this->sql2);
					 if($result2){
						 $count = $result2->RecordCount();
						 $cnt = 1;
							while($row2=$result2->FetchRow()){
										//print_r($row);
								if($count == $cnt)
									$meds .= $row2['artikelname'];
								else
									$meds .= $row2['artikelname'].", ";

								$cnt++;
							}
					 }
				$category = $row['category']."[".$row['specific']."]";
				$start_time = $row['time_begun']." ".$row['tb_meridian'];
				$end_time = $row['time_ended']." ".$row['te_meridian'];
				//$this->Cell(60, $rowheight, $row['category']."[".$row['specific']."]", $this->withoutborder, $this->continueline, $this->alignCenter);
				//$this->MultiCell(60, $rowheight, $meds, $this->withoutborder, $this->continueline, $this->alignCenter);
				//$this->Cell(40, $rowheight, $row['time_begun']." ".$row['tb_meridian'], $this->withoutborder, $this->continueline, $this->alignCenter);
				//$this->Cell(40, $rowheight, $row['time_ended']." ".$row['te_meridian'], $this->withoutborder, $this->nextline, $this->alignCenter);
				$this->Row2(array($category, $meds, $start_time, $end_time));
				$meds = "";
			 }
		}
	 /*
		$sql_anesthesia = "SELECT c.nr as anesthesia_nr, c.name, s.anesthesia, s.anesthesia_category as `category`, s.anesthesia_specific as `specific`,
											cp.artikelname,
											time_format(s.time_begun, '%h:%i') as time_begun, time_format(s.time_ended, '%h:%i') as time_ended, time_format(s.time_begun, '%p') as tb_meridian, time_format(s.time_ended, '%p') as te_meridian
											FROM care_type_anaesthesia c
											INNER JOIN seg_encounter_anesthesia s ON (c.nr=s.anesthesia)
											INNER JOIN seg_encounter_anesthetic AS sea ON sea.anesthesia_care_id = s.anesthesia_care_id
											INNER JOIN care_pharma_products_main AS cp ON cp.bestellnum = sea.anesthetic_id
											WHERE s.or_main_refno = '".$or_main_refno."'";
		$result_anesthesia = $db->Execute($sql_anesthesia);
		//echo $sql_anesthesia;
		if($result_anesthesia){
			while($row_anes = $result_anesthesia->FetchRow()){
					$this->Cell(60, $rowheight, $row_anes['category']."[".$row_anes['specific']."]", $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->Cell(60, $rowheight, $row_anes['artikelname'], $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->Cell(40, $rowheight, $row_anes['time_begun']." ".$row_anes['tb_meridian'], $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->Cell(40, $rowheight, $row_anes['time_ended']." ".$row_anes['te_meridian'], $this->withoutborder, $this->nextline, $this->alignCenter);
			}
		}else{
				$this->Cell(60, $rowheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				$this->Cell(60, $rowheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				$this->Cell(40, $rowheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				$this->Cell(40, $rowheight, "", $this->withoutborder, $this->nextline, $this->alignCenter);

		}*/


		$y = $this->GetY();
		$this->Line($x1, $y, $x1+200, $y);

		//query to get info in seg_or_main_post
		$sql_post = "SELECT * FROM seg_or_main_post WHERE or_main_refno = '$or_main_refno'";
		$result_post = $db->Execute($sql_post);
		$row_post = $result_post->FetchRow();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(45, $rowheight, "CALCULATED FLUID", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(5, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell(150, $rowheight, $row_post['fluids'],$this->withoutborder, $this->nextline, $this->alignLeft);  //calculated fluid
		$this->Line($x, $y+($rowheight), $x+150, $y+($rowheight));

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(45, $rowheight, "BLOOD REPLACEMENT", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(5, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
			$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell(150, $rowheight, $row_post['blood_replacement'], $this->withoutborder, $this->nextline, $this->alignLeft);	//blood replacement
		$this->Line($x, $y+$rowheight, $x+150, $y+$rowheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(45, $rowheight, "BLOOD LOSS", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(5, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
			$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell(150, $rowheight, $row_post['blood_loss'], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Line($x, $y+$rowheight, $x+150, $y+$rowheight);
			$y = $this->GetY();
		$this->Line($x1, $y+$rowheight, $x1+200, $y+$rowheight);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(99, $rowheight, "DRUGS", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		//$this->Cell(59, $rowheight, "SUTURES", $this->withoutborder, $this->continueline, $this->alignCenter);
		//$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(100, $rowheight, "SPONGE", $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->Ln();

		$xsave = $this->GetX();
		$ysave = $this->GetY();
		$cnt = 1;
		$sql_drugs = "SELECT cp.artikelname FROM care_pharma_products_main AS cp
									INNER JOIN seg_pharma_order_items AS sp ON sp.bestellnum = cp.bestellnum
									INNER JOIN seg_pharma_orders AS so ON sp.refno = so.refno
									INNER JOIN seg_or_main AS som ON som.encounter_nr = so.encounter_nr
									WHERE so.encounter_nr = '$encounter_nr'
									AND som.or_main_refno = '$or_main_refno'
									AND cp.prod_class = 'M'
									AND so.request_source='OR'
									;";
		$result_drugs = $db->Execute($sql_drugs);
		if($result_drugs){
			while($row_drugs = $result_drugs->FetchRow()){
				$x = $this->GetX();
				$y = $this->GetY();
				$this->Line($x, $y+($rowheight - 0.5), $x+99, $y+($rowheight - 0.5));
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
				$this->Cell(99, $rowheight, $cnt.". ".$row_drugs['artikelname'], $this->withoutborder, $this->continueline, $this->alignLeft);
				//if($cnt == 3){
				//	$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->nextline, $this->alignLeft);
				//}else{
					$this->Cell(1, $rowheight, "", $this->withoutborder, $this->nextline, $this->alignLeft);
				//}
				$cnt++;
			}
			$yfinala = $this->GetY();
		}

		 $this->SetXY($xsave+100, $ysave);
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Cell(7, $rowheight, ":OS", $this->withoutborder, $this->continueline, $this->alignLeft);
		 $this->Cell(3, $rowheight, "=", $this->withoutborder, $this->continueline, $this->alignCenter);
		 $x = $this->GetX();
		 $y = $this->GetY();
		 $this->Line($x, $y+($rowheight - 0.5), $x+90, $y+($rowheight - 0.5));
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell(90, $rowheight, $row_post['sponge_os'], $this->withoutborder, $this->nextline, $this->alignLeft);

		 $this->SetX($xsave+100);
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Cell(7, $rowheight, ":AP", $this->withoutborder, $this->continueline, $this->alignLeft);
		 $this->Cell(3, $rowheight, "=", $this->withoutborder, $this->continueline, $this->alignCenter);
		 $x = $this->GetX();
		 $y = $this->GetY();
		 $this->Line($x, $y+($rowheight - 0.5), $x+40, $y+($rowheight - 0.5));
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell(40, $rowheight, $row_post['sponge_ap'], $this->withoutborder, $this->continueline, $this->alignLeft);
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Cell(7, $rowheight, "CB", $this->withoutborder, $this->continueline, $this->alignLeft);
		 $this->Cell(3, $rowheight, "=", $this->withoutborder, $this->continueline, $this->alignCenter);
		 $x = $this->GetX();
		 $y = $this->GetY();
		 $this->Line($x, $y+($rowheight - 0.5), $x+40, $y+($rowheight - 0.5));
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell(40, $rowheight, $row_post['sponge_cb'], $this->withoutborder, $this->nextline, $this->alignLeft);

		 $this->SetX($xsave+100);
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Cell(7, $rowheight, ":PP", $this->withoutborder, $this->continueline, $this->alignLeft);
		 $this->Cell(3, $rowheight, "=", $this->withoutborder, $this->continueline, $this->alignCenter);
		 $x = $this->GetX();
		 $y = $this->GetY();
		 $this->Line($x, $y+($rowheight - 0.5), $x+40, $y+($rowheight - 0.5));
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell(40, $rowheight, $row_post['sponge_pp'], $this->withoutborder, $this->continueline, $this->alignLeft);
		 $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		 $this->Cell(17, $rowheight, "PEANUTS", $this->withoutborder, $this->continueline, $this->alignLeft);
		 $this->Cell(3, $rowheight, "=", $this->withoutborder, $this->continueline, $this->alignCenter);
		 $x = $this->GetX();
		 $y = $this->GetY();
		 $this->Line($x, $y+($rowheight - 0.5), $x+30, $y+($rowheight - 0.5));
		 $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		 $this->Cell(30, $rowheight, $row_post['sponge_peanuts'], $this->withoutborder, $this->nextline, $this->alignLeft);

		 $yfinalb = $this->GetY();

		 if($yfinala > $yfinalb){
			 $this->SetY($yfinala);
		 }else{
			 $this->SetY($yfinalb);
		 }

		 //$this->Cell();

		//$this->Line($x, $y+$rowheight, $x+59, $y+$rowheight);
		//$this->Cell(59, $rowheight, "", $this->withoutborder, $this->continueline, $this->alignLeft); //drugs
		//$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignLeft);
		/*	 $x = $this->GetX();
			$y = $this->GetY();
		//$this->Line($x+2, $y+$rowheight, $x+59, $y+$rowheight);
		$this->SetXY($xsave+60, $ysave);
		$this->Cell(59, $rowheight, "axasx", $this->withoutborder, $this->continueline, $this->alignLeft); //sutures
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(8, $rowheight, "OS", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(5, $rowheight, "=", $this->withoutborder, $this->continueline, $this->alignCenter);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Cell(67, $rowheight, $row_post['sponge_os'], $this->withoutborder, $this->nextline, $this->alignLeft); //OS
		$this->Line($x, $y+$rowheight, $x+67, $y+$rowheight);

			$x = $this->GetX();
			$y = $this->GetY();
		$this->Line($x, $y+$rowheight, $x+59, $y+$rowheight);
		$this->Cell(59, $rowheight, "", $this->withoutborder, $this->continueline, $this->alignLeft); //drugs
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignLeft);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Line($x+2, $y+$rowheight, $x+59, $y+$rowheight);
		$this->Cell(59, $rowheight, "", $this->withoutborder, $this->continueline, $this->alignLeft); //sutures
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(8, $rowheight, "SP", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(5, $rowheight, "=", $this->withoutborder, $this->continueline, $this->alignCenter);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Cell(25, $rowheight, $row_post['sponge_ap'], $this->withoutborder, $this->continueline, $this->alignLeft); //AP
		$this->Line($x, $y+$rowheight, $x+25, $y+$rowheight);
		$this->Cell(8, $rowheight, "CB", $this->withoutborder, $this->continueline, $this->alignCenter);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Cell(34, $rowheight, $row_post['sponge_cb'], $this->withoutborder, $this->nextline, $this->alignLeft); //CB
		$this->Line($x, $y+$rowheight, $x+34, $y+$rowheight);

			$x = $this->GetX();
			$y = $this->GetY();
		$this->Line($x, $y+$rowheight, $x+59, $y+$rowheight);
		$this->Cell(59, $rowheight, "", $this->withoutborder, $this->continueline, $this->alignLeft); //drugs
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignLeft);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Line($x+2, $y+$rowheight, $x+59, $y+$rowheight);
		$this->Cell(59, $rowheight, "", $this->withoutborder,$this->continueline, $this->alignLeft); //sutures
		$this->Cell(1, $rowheight, ":", $this->withoutborder, $this->continueline,$this->alignLeft);
		$this->Cell(8, $rowheight, "VP", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(5, $rowheight, "=", $this->withoutborder, $this->continueline, $this->alignCenter);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Cell(25, $rowheight, $row_post['sponge_pp'], $this->withoutborder, $this->continueline, $this->alignLeft); //PP
		$this->Line($x, $y+$rowheight, $x+25, $y+$rowheight);
		$this->Cell(20, $rowheight, "PEANUTS", $this->withoutborder, $this->continueline, $this->alignCenter);
			$x = $this->GetX();
			$y = $this->GetY();
		$this->Cell(22, $rowheight, $row_post['sponge_peanuts'], $this->withoutborder, $this->nextline, $this->alignLeft); //PEANUTS
		$this->Line($x, $y+$rowheight, $x+22, $y+$rowheight);
		*/
		if($encInfo['encounter_type']==2){
			$pre_op = $encInfo['chief_complaint'];
		}else{
			$pre_op = $encInfo['er_opd_diagnosis'];
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(18, $rowheight, "SUTURES:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($rowheight - 0.5), $x+10, $y+($rowheight - 0.5));
		$this->Cell(10, $rowheight, $row_post['sutures'], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(47, $rowheight, "PRE-OPERATIVE DIAGNOSIS", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(3, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->MultiCell(150, $rowheight, $pre_op, $this->withoutborder, $this->alignLeft); //pre_op diagnosis
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(47, $rowheight, "OPERATION PERFORMED", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(3, $rowheight, ":", $this->withoutborder, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->MultiCell(155, $rowheight, $or_main_info['or_procedure'], $this->withoutborder, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(45, $rowheight, "OPERATION STARTED :", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+2, $y+$rowheight, $x+55, $y+$rowheight);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell(55, $rowheight, date("h:i A", strtotime($row_post['time_started'])), $this->withoutborder, $this->continueline, $this->alignLeft); //time started
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(45, $rowheight, "OPERATION FINISHED :", $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+2, $y+$rowheight, $x+55, $y+$rowheight);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell(55, $rowheight, date("h:i A", strtotime($row_post['time_finished'])),$this->withoutborder,$this->nextline,$this->alignLeft); //time finished
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(47, $rowheight, "OPERATION DIAGNOSIS:", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(3, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->MultiCell($this->total_w-50, $rowheight, $row_post['post_op_diagnosis'], $this->withoutborder, $this->alignLeft); //operation Diagnosis
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
		$this->Cell(47, $rowheight, "REMARKS", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell(3, $rowheight, ":", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->MultiCell($this->total_w-50, $rowheight, $row_post['remarks'], $this->withoutborder, $this->alignLeft);  //remarks


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

	function Footer()
	{
		$this->SetY(-23);
		$this->Line(150, 256, 210, 256);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 4, "SIGNATURE OF PHYSICIAN", 0, 1, 'R');
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

		function Row2($data)
	 {
		$row = $this->rheight;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->ColumnWidthAnes[$i],$data[$i]));
					$nb2=$this->NbLines($this->ColumnWidthAnes[1],$data[1]);
					$nb3=$this->NbLines($this->ColumnWidthAnes[5],$data[5]);
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
					$w=$this->ColumnWidthAnes[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
					//Save the current position

					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border

							$length = $this->GetStringWidth($data[$i]);
							if($length < $this->ColumnWidthAnes[$i]){
								//$this->Cell($w, $h, $data[$i],1,0,'L');
								//if($i < 3)
									$this->Cell($w, $h, $data[$i], $this->withoutborder, $this->continueline, $this->Alignment[$i]);
								//else
								//	$this->Cell($w, $h, number_format($data[$i],2,'.',','), $this->withoutborder, $this->continueline, $this->Alignment[$i]);
								//$this->Cell($this->ColumnWidth[$i], $this->rheight, $data[$i], 1, 0, 'L');
							}
							else{
								$nbrow = 3;
								// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								//$this->MultiCell($w, $row,$data[$i],1,'L');
								//if($i < 3)
									$this->MultiCell($w, $row, $data[$i], $this->withoutborder, $this->Alignment[$i]);
								//else
								//	$this->MultiCell($w, $row, number_format($data[$i],2,'.',','), $this->withoutborder, $this->Alignment[$i]);
								//$this->MultiCell($this->ColumnWidth[$i], $this->rheight, $data[$i], 1, 'L');

								//$this->MultiCell($length, $row,$data[$i],1,'L');

							}

					//Put the position to the right of the cell
					//$this->SetXY($x+$w,$y);
					$this->SetXY($x+$this->ColumnWidthAnes[$i],$y);
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

$pdf = new SurgicalMemorandum($_GET['refno']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetData();
$pdf->Output();
?>