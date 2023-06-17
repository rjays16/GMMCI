<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
#require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
include_once($root_path.'include/care_api_classes/class_hospital_admin.php');
include_once($root_path.'include/care_api_classes/class_insurance.php');

define(INFO_INDENT, 10);

class PhilhealthForm3 extends FPDF{
	var $encounter_nr = '';
	var $hcare_id = 0;
	var $fontsize_label = 7;
	var $fontsize_label2 = 22;
	var $fontsize_label3 = 7.5;
	var $fontsize_label4 = 6;
	var $fontsize_label5 = 7;
	var $fontsize_answer = 10;
	var $fontsize_answer2 = 14;
	var $fontsize_answer_check = 14;
	var $fontsize_answer_check2 = 10;
	var $fontsize_answer_cert = 8.5;
	var $fontsize_answer_table = 9;

	var $fontstyle_label_bold = "B";
	var $fontstyle_label_bold_italicized = "BI";
	var $fontstyle_label_italize = "I";
	var $fontstyle_label_normal = '';
	var $fontstyle_answer = "B";

	var $fontfamily_label = "Arial";
	var $fontfamily_answer = "Arial";

	var $totwidth = 200;
	var $rheight = 5;
	var $rheight2 = 2;
	var $rheight3 = 4;

	var $alignRight = "R";
	var $alignCenter = "C";
	var $alignLeft = "L";
	var $alignJustify = "J";

	var $withborder = 1;
	var $withoutborder = 0;
	var $borderTopLeftRight = "TLR";
	var $borderBottomLeftRight = "BLR";
	var $borderTopLeft = "TL";
	var $borderTopRight = "TR";
	var $borderTopBottom = "TB";
	var $borderTop = "T";
	var $borderBottom = "B";
	var $borderLeftRight = "LR";
	var $borderTopLeftBottom = "TLB";
	var $borderTopRightBottom = "TRB";

	var $lineAdjustment = 0.5;
	var $lineHeaderPlus = 0.75;

	var $nextline = 1;
	var $continueline = 0;

	var $boxheight = 3;
	var $boxwidth = 3;

	var $blockheight = 3;
	var $blockwidth = 4;

	var $inspace = 1;
	var $space = 5;

	var $bNoPageBreak = false;

	var $name_first;
	var $name_middle;
	var $name_last;
	var $admission_diagnosis;
	var $final_diagnosis;
	var $room_type;
	var $auth_rep;            // Authorized representative of hospital.
	var $rep_capacity;        // Official capacity of authorized representative.

	var $hospnum;             // Hospital Number (HRN or PID) of patient.
	var $hospaccnum;          // Accreditation No. of Hospital

	function PhilhealthForm3($encounter_nr, $hcare_id){
	#function PhilhealthForm3(){
		$pg_array = array('215.9','330.2');
		$this->FPDF('P', 'mm', $pg_array);
		$this->SetDrawColor(0,0,0);
		//$this->SetMargins(5,5,1);
		$this->SetMargins(8,5,1);
		$this->SetAutoPageBreak(true, 0.75);
		$this->encounter_nr = $encounter_nr;
		$this->hcare_id = $hcare_id;
		#$this->encounter_nr = '2008000004';
		#$this->hcare_id = '18';
		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);

	}

	function addHeader()
	{
		global $root_path, $db;

		//labels
		$form = "This form may be reproduced and is NOT FOR SALE";
		$philhealth = "PHILHEALTH";
		$cf2 = "CF3";
		$partner = "Your Partner in Health";
		$claimform = "(Claim Form)";
		$revised = "revised February 2010";
		$series = "Series #";
		$use = "For PhilHealth use only";
		$reminders = "IMPORTANT REMINDERS:";
		$rule_part1 = "PLEASE WRITE IN CAPITAL";
		$rule_letters = "LETTERS";
		$rule_and = "AND";
		$rule_check = "CHECK";
		$rule_part2 = "THE APPROPRIATE BOXES.";
		$reminder_line1_part1 = "For";
		$reminder_line1_part2 = "local confinement,";
		$reminder_line1_part3 = "this form together with CF1 and other supporting documents should be filed within";
		$reminder_line1_part4 = "60 DAYS";
		$reminder_line1_part5 = "from date of discharge.";
		$reminder_line2 = "All information required in this form are necessary and claim forms with incomplete information shall not be processed.";
		$reminder_line3 = "FALSE / INCORRECT INFORMATION OR MISREPRESENTATION SHALL BE SUBJECT TO CRIMINAL, CIVIL OR ADMINISTRATIVE LIABILITIES.";
		$series_width = 30;   //width for series number
		//$space = 5;
		//$inspace = 1;

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $form, $this->withoutborder,$this->nextline,$this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label2);
		$width = $this->GetStringWidth($philhealth);
		$this->Cell($width, $this->rheight, $philhealth, $this->withoutborder, $this->continueline, $this->alignLeft);
		$width = $this->totwidth - ($width + $this->space);
		$this->Cell($width, $this->rheight, $cf2, $this->withoutborder, $this->nextline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight);
		$width = $this->GetStringWidth($partner) + $this->space;
		$this->Cell($width, $this->rheight, $partner, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth-($width + $this->space + 2), $this->rheight, $claimform, $this->withoutborder, $this->nextline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$this->Cell($this->totwidth, $this->rheight, $revised, $this->withoutborder, $this->nextline, $this->alignRight);
		$this->Ln();
	}

	function checkifmember(){
				global $db;
				$sql = "SELECT i.is_principal AS Member FROM care_person_insurance AS i
								LEFT JOIN care_encounter e ON e.pid = i.pid
								WHERE e.encounter_nr = $this->encounter_nr";
				$result = $db->Execute($sql);
				if($result){
					$row = $result->FetchRow();
				}
				return $row['Member'];
		}

		function checkdependence(){
				global $db;

				$sql = "SELECT d.parent_pid AS Parent
								FROM seg_dependents AS d
								LEFT JOIN care_encounter AS e ON e.pid = d.dependent_pid
								where e.encounter_nr = $this->encounter_nr AND d.status = 'member'";

				if ($result = $db->Execute($sql)) {
						if ($row = $result->FetchRow())
								return $row['Parent'];
				}
				return false;
		}

	function getPrincipalNm($pid) {
		global $db;

		$strSQL = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
						p.name_3 AS ThirdName, p.name_middle AS MiddleName, i.insurance_nr AS IdNum,
						p.street_name AS Street, sb.brgy_name AS Barangay,
						sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province, p.date_birth,
						p.phone_1_code, p.phone_1_nr, p.cellphone_1_nr, p.email
						FROM care_person AS p
						LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
						LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr
						LEFT JOIN seg_municity AS sg ON sg.mun_nr = sb.mun_nr
						LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr
						WHERE i.hcare_id = $this->hcare_id AND i.is_principal = 1 AND p.pid = '$pid'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		#echo "sql= ".$strSQL;
		return false;
	}

	function getPrincipalNmFromTmp($enc_nr) {
		global $db;

		$strSQL = "select member_lname as LastName, member_fname as FirstName, '' as SecondName,
						 '' as ThirdName, member_mname as MiddleName, mi.insurance_nr as IdNum,
						 p.street_name AS Street, sb.brgy_name AS Barangay,
						sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province, p.date_birth,
						p.phone_1_code, p.phone_1_nr, p.cellphone_1_nr, p.email
						from seg_insurance_member_info as mi
						inner join care_encounter as ce on mi.pid = ce.pid
						LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr
						LEFT JOIN seg_municity AS sg ON sg.mun_nr = sb.mun_nr
						LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr
						inner join care_person as p on p.pid = mi.pid

						where ce.encounter_nr = '$enc_nr'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}
		#echo "sql 2nd= ".$strSQL;
		return false;
	}

	function getPrincipalAddr($pid) {
		global $db;

		$strSQL = "SELECT p.street_name AS Street, sb.brgy_name AS Barangay,                        \n
						sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province    \n
						FROM care_person AS p                                                         \n
							LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr                   \n
							LEFT JOIN seg_municity AS sg ON sg.mun_nr = sb.mun_nr                     \n
							LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr                  \n
						WHERE p.pid = '$pid'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

	function getPrincipalAddrFromTmp($enc_nr) {
		global $db;

		$strSQL = "select street_name as Street, brgy_name as Barangay, mun_name as Municity,                 \n
						 zipcode as Zipcode, prov_name as Province                                            \n
						from (seg_insurance_member_info as mi                                                   \n
						 left join ((seg_barangays as b inner join seg_municity as m on m.mun_nr = b.mun_nr)  \n
							inner join seg_provinces as p on m.prov_nr = p.prov_nr)                           \n
							on mi.brgy_nr = b.brgy_nr) inner join care_encounter as ce on mi.pid = ce.pid     \n
						where ce.encounter_nr = '$enc_nr'                                                       \n
						order by create_dt desc limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

	//type = 1 for month and day, type = 2 for year, type = 3 for (mm/dd/yy)
	function writeBlockDate($xcoord, $ycoord, $num, $type){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		if($type!= 3){
			$len = $this->blockwidth * $number;
			$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);
		}else{
			$len = $this->blockwidth2 * $number;
			$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);
		}

			$y1 = $y;
			//$y1 = $y + ($this->blockheight / 2);
			$y2 = $y + $this->blockheight;
			$y3 = $y + $this->blockheight/2;
			$x1 = $x;
				for($cnt = 0; $cnt<=$number; $cnt++){
					if($type == 1){
						if($cnt != 1){
							$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
						}else{
							$this->Line($x1, $y2+$this->lineAdjustment, $x1, $y3);
						}
						$x1 += $this->blockwidth;
					}else if($type == 2){
						if($cnt == 0 || $cnt == 4){
							$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
						}else{
							$this->Line($x1, $y2+$this->lineAdjustment, $x1, $y3);
						}
						$x1 += $this->blockwidth;
					}else if($type == 3){
						if($cnt == 0 || $cnt == 3){
							$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
						}else{
							$this->Line($x1, $y2+$this->lineAdjustment, $x1, $y3);
						}
						$x1 += $this->blockwidth2;
					}

				}
	}

	function writeBlockSpecial($xcoord, $ycoord, $width, $height, $num){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$len = $width * $number;
		$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);

			$y1 = $y;
			$y2 = $y + $height;
			$x1 = $x;
				for($cnt = 0; $cnt<=$number; $cnt++){
					$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
					$x1 += $width;
				}
	}

	function writeBlock($xcoord, $ycoord, $num){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$len = $this->blockwidth * $number;
		$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);

			$y1 = $y;
			$y2 = $y + $this->blockheight;
			$x1 = $x;
				for($cnt = 0; $cnt<=$number; $cnt++){
					$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
					$x1 += $this->blockwidth;
				}
	}

	function addPart1(){
		global $db;

		//labels
		$label = array("PART I - PATIENT'S CLINICAL RECORD", '1. PhilHealth Accreditation No. (PAN) - Institutional Health Care Provider:',
						 '2. Name of Patient', '3. Chief Complaint / Reason for Admission:', '4. Date Admitted:',
						 'Time Admitted:', '5. Date Discharged', 'Time Discharged:',
						 '6. Brief History of Present Illness / OB History:',
						 '7. Physical Examination (Pertinent Findings per System)',
						 '8. Course in the Wards (attach additional sheets if necessary):',
						 '9. Pertinent Laboratory and Diagnostic Findings: (CBC, Urinalysis, Fecalysis, X-ray, Biopsy, etc.)',
						 '10. Disposition on Discharge');
		$name_label = array('Last Name', 'First Name', 'Middle Name');
		$date_label = array('Month', 'Day', 'Year');
		$time_label = array('hh-mm', 'AM', 'PM');
		$exam_label = array('General Survey', 'Vital Signs', 'BP:', 'CR:', 'RR:','Temperature:',
									'Abdomen', 'HEENT', 'GU (IE)', 'Chest/Lungs', 'Skin Extremities',
									'CVS', 'Neuro Examination');
		$disposition_label = array('Improved', 'Transferred', 'HAMA', 'Absconded', 'Expired');
		$colon = ":";

		//column widths
		$ColumnWidth1 = array(85, 40); //acc number
		$ColumnWidth2 = array(140, 60); //name of patient (label) & chief complaint
		$ColumnWidth3 = array(45, 45, 45); //last name, first name, middle name
		$ColumnWidth4 = array(28, 12, 28, 12, 8); //date & time admitted, date & time discharged
		$ColumnWidth5 = array(20, 5, 80, 10,25, 5, 45);//physical exam data
		$ColumnWidth6 = array(50, 20, 20, 20, 25, 20);

		//number of blocks
		$acc_number = 9;

		//height
		$ColumnHeight = array(50, 50, 45);

		//get data
		$enc_obj=new Encounter;
		$encInfo=$enc_obj->getEncounterInfo($this->encounter_nr);

		$sql = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
						p.name_3 AS ThirdName, p.name_middle AS MiddleName,
						i.insurance_nr AS IdNum, p.street_name AS Street, sb.brgy_name AS Barangay,                        \n
						sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province, p.date_birth,
						p.phone_1_code, p.phone_1_nr, p.cellphone_1_nr, p.email
						FROM care_person AS p
						INNER JOIN care_encounter AS e ON e.pid = p.pid
						INNER JOIN care_person_insurance AS i ON i.pid = p.pid
						LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr                   \n
						LEFT JOIN seg_municity AS sg ON sg.mun_nr = sb.mun_nr                     \n
						LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr
						WHERE e.encounter_nr = $this->encounter_nr";

			$result = $db->Execute($sql);
			$data = $result->FetchRow();

			$lastname = $data['LastName'];
			$middlename = $data['MiddleName'];
			$firstname = $data['FirstName'];
			#print_r($encInfo);

			if(empty($encInfo['admission_dt'])){
				$date_admitted = $encInfo['encounter_date'];
				$time_admitted = strftime("%I:%M %p", strtotime($encInfo['encounter_date']));
			}else{
				$date_admitted = $encInfo['admission_dt'];
				$time_admitted = strftime("%I:%M %p", strtotime($encInfo['admission_dt']));
			}
			#echo "date admitted = ".$date_admitted;
			$date_discharged = $encInfo['discharge_dt'];
			$time_discharged = strftime("%I:%M %p", strtotime($encInfo['discharge_dt']));

			if(empty($encInfo['er_opd_diagnosis'])){
				$complaint = $encInfo['chief_complaint'];
			}else{
				$complaint = $encInfo['er_opd_diagnosis'];
			}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth, $this->rheight, $label[0], $this->withoutborder, $this->nextline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->Ln($this->rheight2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth1[0], $this->rheight, $label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();

		//put accreditation number here
		$objinsurance = new Insurance();
		if ($a_no = $objinsurance->getAccreditationNo($this->hcare_id))
		$this->hospaccnum = $a_no;
		#echo "accnum churva = ".$a_no;

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		#$this->Cell($ColumnWidth1[1], $this->rheight, "", $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->writeBlock($x, $y, $acc_number);
		$hosp_acc = str_split($this->hospaccnum);
		$hosp_acc_len = strlen($this->hospaccnum);
		for($cnt = 0; $cnt < $hosp_acc_len; $cnt++){
			$this->Cell($this->blockwidth, $this->blockheight, $hosp_acc[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
		}
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth1[1], $y+($this->rheight - $this->lineAdjustment));
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth2[0], $this->rheight, $label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth2[1], $this->rheight, $label[3], $this->borderTopLeftRight, $this->nextline, $this->alignLeft);
		$xsave = $this->GetX();
		$ysave = $this->GetY();
		$this->Cell($ColumnWidth2[0],$this->rheight2);
		//put chief complaint / reason for admission here
		$this->MultiCell($ColumnWidth2[1], $this->rheight3, $complaint, $this->borderBottomLeftRight, $this->alignJustify);

		$this->SetXY($xsave+$this->inspace, $ysave);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		//put last name of patient here
		$this->Cell($ColumnWidth3[0], $this->rheight, strtoupper($lastname), $this->withoutborder, $this->continueline, $this->alignCenter);
		//put first name of patient here
		$this->Cell($ColumnWidth3[1], $this->rheight, strtoupper($firstname), $this->withoutborder, $this->continueline, $this->alignCenter);
		//put middle name of patient here
		$this->Cell($ColumnWidth3[2], $this->rheight, strtoupper($middlename), $this->withoutborder, $this->nextline, $this->alignCenter);
		$tot_len_col3 = $ColumnWidth3[0] + $ColumnWidth3[1] + $ColumnWidth3[2];
		$this->Line($xsave+$this->inspace, $ysave+($this->rheight - $this->lineAdjustment), $xsave+$tot_len_col3, $ysave+($this->rheight - $this->lineAdjustment));

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth3[0], $this->rheight, $name_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth3[1], $this->rheight, $name_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth3[2], $this->rheight, $name_label[2], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth4[0], $this->rheight, $label[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put date admitted here
		$admit_date = date("mdY", strtotime($date_admitted));
		$admit_date_arr = str_split($admit_date);
		$admit_date_len = strlen($admit_date);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$admit_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}
		#$this->Cell($ColumnWidth4[1]-$this->space, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth4[1]-$this->space, $y+($this->rheight - $this->lineAdjustment));
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidth4[2]-$this->space, $this->rheight, $label[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();

		//put time admitted here
		$admit_time = substr($time_admitted, 0, 6);
		$adtime = substr($time_admitted, 6, 8);
		if($adtime=='AM'){
			$admit_time_am = $admit_time;
		}else if($adtime=='PM'){
			$admit_time_pm = $admit_time;
		}

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidth4[3], $this->blockheight, 1);
		$this->Cell($ColumnWidth4[3], $this->rheight3, $admit_time_am, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth4[4], $this->rheight, $time_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidth4[3], $this->blockheight, 1);
		$this->Cell($ColumnWidth4[3], $this->rheight3, $admit_time_pm, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth4[4], $this->rheight, $time_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln();
		#$this->Cell($ColumnWidth4[3], $this->rheight, "", $this->withoutborder, $this->nextline, $this->alignCenter);
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth4[3], $y+($this->rheight - $this->lineAdjustment));

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth4[0], $this->rheight3);
		#$this->Cell($ColumnWidth4[1], $this->rheight3, $date_label[0].$date_label[1].$date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth * 2, $this->rheight3, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight3);
		$this->Cell($this->blockwidth * 2, $this->rheight3, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight3);
		$this->Cell($this->blockwidth * 4, $this->rheight3, $date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth4[2], $this->rheight3);
		$this->Cell($ColumnWidth4[3], $this->rheight3, $time_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth4[4], $this->rheight3);
		$this->Cell($ColumnWidth4[3], $this->rheight3, $time_label[0], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln($this->rheight2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth4[0], $this->rheight, $label[6], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put date discharged here
		$discharge_date = date("mdY", strtotime($date_discharged));
		$discharge_date_arr = str_split($discharge_date);
		$discharge_date_len = strlen($discharge_date);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$discharge_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, $discharge_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, $discharge_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, $discharge_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidth4[2]-$this->space, $this->rheight, $label[7], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put time discharged here
		$discharge_time = substr($time_discharged, 0, 6);
		$distime = substr($time_discharged, 6, 8);
		if($distime=='AM'){
			$discharge_time_am = $discharge_time;
		}else if($distime=='PM'){
			$discharge_time_pm = $discharge_time;
		}

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidth4[3], $this->blockheight, 1);
		$this->Cell($ColumnWidth4[3], $this->rheight3, $discharge_time_am, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth4[4], $this->rheight, $time_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidth4[3], $this->blockheight, 1);
		$this->Cell($ColumnWidth4[3], $this->rheight3, $discharge_time_pm, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth4[4], $this->rheight, $time_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth4[0], $this->rheight3);
		#$this->Cell($ColumnWidth4[1], $this->rheight3, $date_label[0].$date_label[1].$date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth * 2, $this->rheight3, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight3);
		$this->Cell($this->blockwidth * 2, $this->rheight3, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight3);
		$this->Cell($this->blockwidth * 4, $this->rheight3, $date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth4[2], $this->rheight3);
		$this->Cell($ColumnWidth4[3], $this->rheight3, $time_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth4[4], $this->rheight3);
		$this->Cell($ColumnWidth4[3], $this->rheight3, $time_label[0], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln($this->rheight2);


		//-------------- 6. Brief History of Present Illness / OB History:---------
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $label[8], $this->borderTop, $this->nextline, $this->alignLeft);
		$yheight = $this->GetY();
		//put brief history here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->MultiCell($this->totwidth, $this->rheight, "", $this->withoutborder, $this->alignJustify);

		//-------------7. Physical Examination--------------
		$this->SetY($yheight + $ColumnHeight[0]);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $label[9], $this->borderTop, $this->nextline, $this->alignLeft);

		$this->Cell($this->totwidth, $this->rheight, $exam_label[0].$colon, $this->withoutborder, $this->nextline, $this->alignLeft);
		//$ColumnWidth5 = array(30, 5, 80, 35, 5, 45);//physical exam data
		#$this->Ln($this->rheight);
		$this->Cell($ColumnWidth5[0], $this->rheight, $exam_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth5[1], $this->rheight, $colon, $this->withoutborder, $this->continueline, $this->alignCenter);
		$length = $ColumnWidth5[2] / 4;

		$len = $this->GetStringWidth($exam_label[2]) + $this->inspace*2;
		$this->Cell($len, $this->rheight, $exam_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($length - $len), $y+($this->rheight - $this->lineAdjustment));
	 //put bp here
		$this->Cell($length - $len, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);

		$len = $this->GetStringWidth($exam_label[3]) + $this->inspace*2;
		$this->Cell($len, $this->rheight, $exam_label[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($length - $len), $y+($this->rheight - $this->lineAdjustment));
	 //put cr here
		$this->Cell($length - $len, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);

		$len = $this->GetStringWidth($exam_label[4]) + $this->inspace*2;
		$this->Cell($len, $this->rheight, $exam_label[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($length - $len), $y+($this->rheight - $this->lineAdjustment));
	 //put rr here
		$this->Cell($length - $len, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);

		$length += $ColumnWidth5[3];
		$len = $this->GetStringWidth($exam_label[5]) + $this->inspace*2;
		$this->Cell($len, $this->rheight, $exam_label[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($length - $len), $y+($this->rheight - $this->lineAdjustment));
	 //put temperature here
		$this->Cell($length - $len, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidth5[4], $this->rheight, $exam_label[6], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth5[5], $this->rheight, $colon, $this->withoutborder, $this->continueline, $this->alignCenter);

		//put data for abdomen examination here
		#$example = "sdcdsc cdscdscdsc dcaskcjsdcs sdcaskcsc ssdkcs ksc scs sdcsc scskdcsdc ksdcasnc scksdacsdc";
		$this->MultiCell($ColumnWidth5[6], $this->rheight, "", $this->withoutborder, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->Cell($ColumnWidth5[0], $this->rheight, $exam_label[7], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth5[1], $this->rheight, $colon, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put heent data here
		$this->Cell($ColumnWidth5[2]+$ColumnWidth5[3], $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidth5[4], $this->rheight, $exam_label[8], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth5[5], $this->rheight, $colon, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put data for GU(IE) here
		$this->MultiCell($ColumnWidth5[6], $this->rheight, "", $this->withoutborder, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->Cell($ColumnWidth5[0], $this->rheight, $exam_label[9], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth5[1], $this->rheight, $colon, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put chest/lungs data here
		$this->Cell($ColumnWidth5[2]+$ColumnWidth5[3], $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidth5[4], $this->rheight, $exam_label[10], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth5[5], $this->rheight, $colon, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put data skin/extremities here
		$this->MultiCell($ColumnWidth5[6], $this->rheight, "", $this->withoutborder, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->Cell($ColumnWidth5[0], $this->rheight, $exam_label[11], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth5[1], $this->rheight, $colon, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put CVS data here
		$this->Cell($ColumnWidth5[2]+$ColumnWidth5[3], $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidth5[4], $this->rheight, $exam_label[12], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth5[5], $this->rheight, $colon, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put neuro examination here
		$this->MultiCell($ColumnWidth5[6], $this->rheight, "", $this->withoutborder, $this->alignLeft);
		$this->Ln($this->rheight*3);

		$this->Cell($this->totwidth, $this->rheight, $label[10], $this->borderTop, $this->continueline, $this->alignLeft);
		$yheight = $this->GetY();

		$this->SetY($yheight + $ColumnHeight[1]);
		$this->Cell($this->totwidth, $this->rheight, $label[11], $this->borderTop, $this->continueline, $this->alignLeft);
		$yheight = $this->GetY();

		$this->SetY($yheight + $ColumnHeight[2]);
		$this->Cell($this->totwidth, $this->rheight, "", $this->borderTop);
		$this->Ln($this->rheight);
		$this->Cell($ColumnWidth6[0], $this->rheight, $label[12], $this->withoutborder, $this->continueline, $this->alignLeft);
		//$ColumnWidth6 = array(35, 20, 20, 20, 20, 20);  $disposition_label = array('Improved', 'Transferred', 'HAMA', 'Absconded', 'Expired');
		//check if condition is improved
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth6[1], $this->rheight, $disposition_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);

		//check if condition is transfered
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth6[2], $this->rheight, $disposition_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);

		//check if condition is HAMA
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth6[3], $this->rheight, $disposition_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);

		//check if condition is Absconded
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth6[4], $this->rheight, $disposition_label[3], $this->withoutborder, $this->continueline, $this->alignCenter);

		//check if condition is expired
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth6[5], $this->rheight, $disposition_label[4], $this->withoutborder, $this->continueline, $this->alignCenter);

		$this->Ln($this->rheight * 2);
		$this->Cell($this->totwidth, $this->rheight, "", $this->borderTop);

	}

	function addPart2(){
		global $db;

		$label = array('PART II - MATERNITY CARE PACKAGE', 'PRENATAL CONSULTATION',
									'DELIVERY OUTCOME', 'POSTPARTUM CARE');
		$sub_label = array('1. Initial Prenatal Consultation', '2. Clinical History and Physical Examination',
									'3. Obstetric risk factors', '4. Medical/Surgical risk factors', '5. Admitting Diagnosis',
									'6. Delivery Plan', '7. Follow-up Prenatal Consultation', '8. Date and Time of Delivery',
									'9. Maternal Outcome:', 'Pregnancy Uterine,', '10. Birth Outcome',
									'11. Scheduled Postpartum follow-up consultation 1 week after delivery',
									'12. Date and Time Discharged', '13. Perineal wound care',
									'14. Signs of Maternal Postpartum Complications',
									'15. Counseling and Education',
									'16. Provided family planning service to patient (as requested by patient)',
									'17. Referred to partner physician for Voluntary Surgical Sterilization (as requested by pt.)',
									'18. Schedule the next postpartum follow-up',
									'19. Certification of Attending Physician/Midwife:'
									);
		$history_exam_label = array('a. Vital signs are normal', 'b. Ascertain the present Pregnancy is low-Risk',
														'c. Menstrual History LMP', 'd. Obstetric History', 'Age of Menarche', 'G', 'P', 'T', 'P','A','L'
													);
		$open_par = "(";
		$close_par = ")";
		$comma = ",";
		$obs_risk_fact_label = array('a. Multiple pregnancy', 'b. Ovarian cyst', 'c. Myoma uteri',
														'd. Placenta previa', 'e. History of 3 miscarriages', 'f. History of stillbirth',
														'g. History of pre-eclampsia', 'h. History of eclampsia',
														'i. Premature contraction'
													);
		$date_label = array('Month', 'Day', 'Year');
		$med_risk_fact_label = array('a. Hypertension', 'b. Heart Disease', 'c. Diabetes', 'd. Thyroid Disorder',
														'e. Obesity', 'f. Moderate to severe asthma', 'g. Epilepsy', 'h. Renal disease',
														'i. Bleeding disorders', 'j. History of previous cesarian section',
														'k. History of uterine myomectomy'
													);
		$dev_plan_label = array('a. Orientation to MCP/Availment of Benefits',
												'b. Expected date of delivery'
											);
		$ans_label = array('yes', 'no');
		$vital_signs_label = array('d.1. Weight', 'd.2. Cardiac Rate', 'd.3. Respiratory Rate',
														'd.4. Blood Pressure', 'd.5. Temperature');
		$followup_label = array('a. Prenatal Consultation No', 'b. Date of visit (mm/dd/yy)',
												'c. AOG in weeks', 'd. Weight & Vital Signs:');
		$cons_num_label = array('2nd','3rd','4th','5th','6th','7th','8th','9th','10th','11th','12th');
		$date_time_label = array('Date', 'Time', 'AM', 'PM', 'hh-mm');
		$mat_outcome_label = array('Obstetric Index', 'AOG by LMP', 'Manner of Delivery', 'Presentation');
		$birth_outcome_label = array('Fetal Outcome', 'Sex', 'Birth Weight (gm)', 'APGAR Score');
		$post_label = array('done', 'Remarks');
		$counsel_label = array('a. Breastfeeding and Nutrition', 'b. Family Planning');
		$cert_label = array('I certify that the above information given in this form are true and correct.',
										'Signature Over Printed Name of Attending Physician/Midwife',
										'Date Signed (Month / Day/ Year)');

		$ColumnWidth1 = array(40, 40); //for initial prenatal consultation
		$ColumnWidth2 = array(55, 30, $this->blockwidth * 10, 20, 10); //for clinical history and physical exam
		$ColumnWidth3 = array(30, 10, 30, 10, 30); //for obstetric risk factors
		$ColumnWidth4 = array(30, 10, 30, 10, 30, 10, 40);//for medical/surgical risk factors
		$ColumnWidth5 = array(30, 150); //for admitting diagnosis
		$ColumnWidth6 = array(60, 10, 40); //for delivery plan
		$ColumnWidthConsNo = array(35,5,5,5,7,10); //width for prenatal consultation no.
		$ColumnWidthDateTime = array(35, 10, 10, $this->blockwidth * 10, 10, 10, 12,7,12,7);
		$ColumnWidthMatOut = array(30, 15, 25, 30,30,30);
		$ColumnWidthBirthOut = array(30, 32, 31, 31, 31);
		$ColumnWidthSched = array(80, 15);
		$ColumnWidthPost = array(100, 70);
		$ColumnWidthCert = array(70, 20);
		$this->blockwidth2 = $ColumnWidthConsNo[1];

		$this->Ln($this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $label[0], $this->withoutborder, $this->nextline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->Ln($this->rheight2);

		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $label[1], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$this->totwidth, $y+($this->rheight - $this->lineAdjustment));
		$this->Ln($this->rheight2);

		$this->Cell($ColumnWidth1[0], $this->rheight, $sub_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		//put initial prenatal consultation here
		$cons_date = date("mdY", strtotime($date_consult));
		$cons_date_arr = str_split($cons_date);
		$cons_date_len = strlen($cons_date);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$cons_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}
		$this->Ln($this->rheight3);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italize, $this->fontsize_label4);
		$this->Cell($ColumnWidth1[0], $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 4, $this->rheight2, $date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $sub_label[1], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($this->inspace, $this->rheight);
		$this->Cell($ColumnWidth2[0], $this->rheight, $history_exam_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);
		//check if vital signs are normal
		$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidth2[1], $this->rheight, $history_exam_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);

		//put menstrual history lmp date here
		$mens_date = date("mdY", strtotime($date_mens));
		$mens_date_arr = str_split($mens_date);
		$mens_date_len = strlen($mens_date);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$mens_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight3);
		$this->Cell($ColumnWidth2[3], $this->rheight, $history_exam_label[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		//put age of menarch here
		$this->Cell($ColumnWidth2[4], $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth2[4], $y+($this->rheight - $this->lineAdjustment));
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italize, $this->fontsize_label4);
		$span_len = $ColumnWidth2[0] + ($this->space * 2) + $ColumnWidth2[1] + $this->boxwidth + $this->inspace;
		$this->Cell($span_len, $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 4, $this->rheight2, $date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->inspace, $this->rheight);
		$this->Cell($ColumnWidth2[0], $this->rheight, $history_exam_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);

		//check if pregnancy is low - risk
		$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidth2[1], $this->rheight, $history_exam_label[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$glen = $this->GetStringWidth($history_exam_label[5]) + $this->inspace;
		$this->Cell($glen, $this->rheight, $history_exam_label[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($this->space), $y+($this->rheight - $this->lineAdjustment));
		//put obstetric history G here
		$this->Cell($this->space, $this->rheight, "", $this->withoutborder, $this->continueline);
		$this->Cell($this->inspace, $this->rheight);
		$plen = $this->GetStringWidth($history_exam_label[6]) + $this->inspace;
		$this->Cell($plen, $this->rheight, $history_exam_label[6], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($this->space), $y+($this->rheight - $this->lineAdjustment));
		//put obstetric history P here
		$this->Cell($this->space, $this->rheight, "", $this->withoutborder, $this->continueline);

		$par_len = $this->GetStringWidth($open_par) + $this->inspace;
		$this->Cell($par_len, $this->rheight, $open_par, $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($this->space), $y+($this->rheight - $this->lineAdjustment));
		//put obstetric history T here
		$this->Cell($this->space, $this->rheight, "", $this->withoutborder, $this->continueline);

		$comma_len = $this->GetStringWidth($comma) + $this->inspace;
		$this->Cell($comma_len, $this->rheight, $comma, $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($this->space), $y+($this->rheight - $this->lineAdjustment));
		//put obstetric history P here
		$this->Cell($this->space, $this->rheight, "", $this->withoutborder, $this->continueline);

		$this->Cell($comma_len, $this->rheight, $comma, $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($this->space), $y+($this->rheight - $this->lineAdjustment));
		//put obstetric history A here
		$this->Cell($this->space, $this->rheight, "", $this->withoutborder, $this->continueline);

		$this->Cell($comma_len, $this->rheight, $comma, $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($this->space), $y+($this->rheight - $this->lineAdjustment));
		//put obstetric history L here
		$this->Cell($this->space, $this->rheight, "", $this->withoutborder, $this->continueline);

		$par2_len = $this->GetStringWidth($close_par) + $this->inspace;
		$this->Cell($par2_len, $this->rheight, $close_par, $this->withoutborder, $this->nextline, $this->alignCenter);

		$length = $ColumnWidth2[0] + ($this->space * 4) + $this->blockwidth + $ColumnWidth2[1] + $glen + $plen + $par_len + $this->inspace;
		$this->Cell($length, $this->rheight);
		$this->Cell($this->space, $this->rheight, $history_exam_label[7], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($comma_len, $this->rheight);
		$this->Cell($this->space, $this->rheight, $history_exam_label[8], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($comma_len, $this->rheight);
		$this->Cell($this->space, $this->rheight, $history_exam_label[9], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($comma_len, $this->rheight);
		$this->Cell($this->space, $this->rheight, $history_exam_label[10], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->Cell($this->totwidth, $this->rheight, $sub_label[2], $this->withoutborder, $this->nextline, $this->alignLeft);

		for($cnt = 0; $cnt<3; $cnt++){
			$this->Cell($this->inspace, $this->rheight);
			$this->Cell($ColumnWidth3[0], $this->rheight, $obs_risk_fact_label[$cnt], $this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->space, $this->rheight);
			$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder);
			$this->Cell($ColumnWidth3[1], $this->rheight);
			$this->Cell($ColumnWidth3[2], $this->rheight, $obs_risk_fact_label[$cnt+3], $this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->space, $this->rheight);
			$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder);
			$this->Cell($ColumnWidth3[3], $this->rheight);
			$this->Cell($ColumnWidth3[4], $this->rheight, $obs_risk_fact_label[$cnt+6], $this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->space, $this->rheight);
			$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder);
			$this->Ln($this->rheight);
		}

		$this->Cell($this->totwidth, $this->rheight, $sub_label[3], $this->withoutborder, $this->nextline, $this->alignLeft);

		for($cnt = 0; $cnt<3; $cnt++){
			$this->Cell($this->inspace, $this->rheight);
			$this->Cell($ColumnWidth4[0], $this->rheight, $med_risk_fact_label[$cnt], $this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->space, $this->rheight);
			$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder);
			$this->Cell($ColumnWidth4[1], $this->rheight);
			$this->Cell($ColumnWidth4[2], $this->rheight, $med_risk_fact_label[$cnt+3], $this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->space, $this->rheight);
			$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder);
			$this->Cell($ColumnWidth4[3], $this->rheight);
			$this->Cell($ColumnWidth4[4], $this->rheight, $med_risk_fact_label[$cnt+6], $this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->space, $this->rheight);
			$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder);
			if($med_risk_fact_label[$cnt+9]){
				$this->Cell($ColumnWidth4[5], $this->rheight);
				$this->Cell($ColumnWidth4[6], $this->rheight, $med_risk_fact_label[$cnt+9], $this->withoutborder, $this->continueline, $this->alignLeft);
				$this->Cell($this->space, $this->rheight);
				$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder);
			}
			$this->Ln($this->rheight);
		}
		$this->Ln($this->rheight2);

		$this->Cell($ColumnWidth5[0], $this->rheight, $sub_label[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Cell($this->totwidth - $ColumnWidth5[0], $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($this->totwidth - $ColumnWidth5[0]), $y+($this->rheight - $this->lineAdjustment));
		$this->Ln($this->rheight);

		$this->Cell($this->totwidth, $this->rheight, $sub_label[5], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln($this->rheight2);

		$this->Cell($this->inspace, $this->rheight);
		$this->Cell($ColumnWidth6[0], $this->rheight, $dev_plan_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check if yes
		$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder, $this->continueline);
		$this->Cell($this->space, $this->rheight);
		//check if no
		$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder, $this->continueline);
		$this->Cell($ColumnWidth6[1], $this->rheight);
		$this->Cell($ColumnWidth6[2], $this->rheight, $dev_plan_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);

		//put expected date of delivery here
		$exdev_date = date("mdY", strtotime($date_exdev));
		$exdev_date_arr = str_split($exdev_date);
		$exdev_date_len = strlen($exdev_date);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$exdev_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italize, $this->fontsize_label4);
		$this->Cell($ColumnWidth6[0], $this->rheight2);
		$this->Cell($this->boxwidth, $this->rheight2, $ans_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight2);
		$this->Cell($this->boxwidth, $this->rheight2, $ans_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth6[1] + $ColumnWidth6[2] + $this->inspace, $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->boxwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->boxwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 4, $this->rheight2, $date_label[2], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $sub_label[6], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($ColumnWidthConsNo[0], $this->rheight, $followup_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$sub = $ColumnWidthConsNo[4] - $ColumnWidthConsNo[1];
		$div = $sub/2;
		for($cnt=0; $cnt<11; $cnt++){
			$this->Cell($ColumnWidthConsNo[1]-$div, $this->rheight);
			$this->Cell($ColumnWidthConsNo[4], $this->rheight3, $cons_num_label[$cnt], $this->withborder, $this->continueline, $this->alignCenter);
			$this->Cell($ColumnWidthConsNo[3]-$div, $this->rheight);
		}
		$this->Ln($this->rheight);

		$this->Cell($ColumnWidthConsNo[0], $this->rheight, $followup_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$num = 3;
		$type = 3;
		for($cnt=0; $cnt<11; $cnt++){
			$this->writeBlockDate($x, $y, $num, $type);
			$this->Cell($ColumnWidthConsNo[1] + $ColumnWidthConsNo[2] + $ColumnWidthConsNo[3], $this->rheight);
			$x = $this->GetX();
		}
		$this->Ln($this->rheight);

		$this->Cell($ColumnWidthConsNo[0], $this->rheight, $followup_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$sub = ($ColumnWidthConsNo[1] + $ColumnWidthConsNo[2] + $ColumnWidthConsNo[3]) - $ColumnWidthConsNo[5];
		$div = $sub/2;

		for($cnt=0; $cnt<11; $cnt++){
			$this->Cell($div, $this->rheight);
			$this->Cell($ColumnWidthConsNo[5], $this->rheight3, "", $this->withborder, $this->continueline, $this->alignCenter);
			$this->Cell($div, $this->rheight);
		}
		$this->Ln($this->rheight);

		$this->Cell($this->totwidth, $this->rheight, $followup_label[3], $this->withoutborder, $this->nextline, $this->alignLeft);

		for($cnt1=0; $cnt1<5; $cnt1++){
			$this->Cell($this->inspace, $this->rheight);
			$this->Cell($ColumnWidthConsNo[0] - $this->inspace, $this->rheight, $vital_signs_label[$cnt1], $this->withoutborder, $this->continueline, $this->alignLeft);
			for($cnt2=0; $cnt2<11; $cnt2++){
				$this->Cell($div, $this->rheight);
				$this->Cell($ColumnWidthConsNo[5], $this->rheight3, "", $this->withborder, $this->continueline, $this->alignCenter);
				$this->Cell($div, $this->rheight);
			}
			$this->Ln($this->rheight);
		}

		$this->Ln($this->rheight);

		//--------------------Delivery Outcome-------------------------
		$this->Cell($this->totwidth, $this->rheight, $label[2], $this->borderTopBottom, $this->nextline, $this->alignCenter);
		$this->Ln($this->rheight2);

		$this->Cell($ColumnWidthDateTime[0], $this->rheight, $sub_label[7], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthDateTime[1], $this->rheight);
		$this->Cell($ColumnWidthDateTime[2], $this->rheight, $date_time_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		//put date of delivery here
		$dev_date = date("mdY", strtotime($date_dev));
		$dev_date_arr = str_split($dev_date);
		$dev_date_len = strlen($dev_date);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$dev_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthDateTime[4], $this->rheight);
		$this->Cell($ColumnWidthDateTime[5], $this->rheight, $date_time_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);


		$delivery_time = substr($time_delivery, 0, 6);
		$devtime = substr($time_delivery, 6, 8);
		if($devtime=='AM'){
			$delivery_time_am = $delivery_time;
		}else if($devtime=='PM'){
			$delivery_time_pm = $delivery_time;
		}
		 //delivery time AM
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidthDateTime[6], $this->blockheight, 1);
		$this->Cell($ColumnWidthDateTime[6], $this->rheight3, $delivery_time_am, $this->withoutborder, $this->continueline, $this->alignCenter);

		$this->Cell($this->inspace, $this->rheight3);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthDateTime[7], $this->rheight, $date_time_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight3);
		$x = $this->GetX();
		$y = $this->GetY();

		//delivery time PM
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidthDateTime[8], $this->blockheight, 1);
		$this->Cell($ColumnWidthDateTime[8], $this->rheight3, $delivery_time_pm, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight3);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthDateTime[9], $this->rheight, $date_time_label[3], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->Cell($ColumnWidthDateTime[0] + $ColumnWidthDateTime[1] + $ColumnWidthDateTime[2], $this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italize, $this->fontsize_label4);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 4, $this->rheight2, $date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthDateTime[4]+$ColumnWidthDateTime[5], $this->rheight);
		$this->Cell($ColumnWidthDateTime[6], $this->rheight2, $date_time_label[4], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthDateTime[7], $this->rheight2);
		$this->Cell($ColumnWidthDateTime[8], $this->rheight2, $date_time_label[4], $this->withoutborder, $this->nextline, $this->alignCenter);
		#$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthMatOut[0], $this->rheight, $sub_label[8], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthMatOut[1], $y+($this->rheight - $this->lineAdjustment));
		//obstetric index
		$this->Cell($ColumnWidthMatOut[1], $this->rheight, "", $this->withoutborder, $this->continueline);
		$this->Cell($ColumnWidthMatOut[2], $this->rheight, $sub_label[9], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthMatOut[3], $y+($this->rheight - $this->lineAdjustment));
		//AOG by LMP
		$this->Cell($ColumnWidthMatOut[3], $this->rheight, "", $this->withoutborder, $this->continueline);
		$this->Cell($this->space, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthMatOut[4], $y+($this->rheight - $this->lineAdjustment));
		//Manner of Delivery
		$this->Cell($ColumnWidthMatOut[4], $this->rheight, "", $this->withoutborder, $this->continueline);
		$this->Cell($this->space, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthMatOut[5], $y+($this->rheight - $this->lineAdjustment));
		//Presentation
		$this->Cell($ColumnWidthMatOut[5], $this->rheight, "", $this->withoutborder, $this->continueline);
		$this->Ln($this->rheight);
		#$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$this->Cell($ColumnWidthMatOut[0], $this->rheight2);
		$this->Cell($ColumnWidthMatOut[1], $this->rheight2, $mat_outcome_label[0], $this->withoutborder, $this->continueline,$this->alignCenter);
		$this->Cell($ColumnWidthMatOut[2], $this->rheight2);
		$this->Cell($ColumnWidthMatOut[3], $this->rheight2, $mat_outcome_label[1], $this->withoutborder, $this->continueline,$this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidthMatOut[4], $this->rheight2, $mat_outcome_label[2], $this->withoutborder, $this->continueline,$this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidthMatOut[5], $this->rheight2, $mat_outcome_label[3], $this->withoutborder, $this->nextline,$this->alignCenter);
		$this->Ln($this->rheight2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthBirthOut[0], $this->rheight, $sub_label[10], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthBirthOut[1], $y+($this->rheight - $this->lineAdjustment));
		//fetal outcome
		$this->Cell($ColumnWidthBirthOut[1], $this->rheight, "", $this->withoutborder, $this->continueline);
		$this->Cell($this->space, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthBirthOut[2], $y+($this->rheight - $this->lineAdjustment));
		//sex
		$this->Cell($ColumnWidthBirthOut[2], $this->rheight, "", $this->withoutborder, $this->continueline);
		$this->Cell($this->space, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthBirthOut[3], $y+($this->rheight - $this->lineAdjustment));
		//birth weight
		$this->Cell($ColumnWidthBirthOut[3], $this->rheight, "", $this->withoutborder, $this->continueline);
		$this->Cell($this->space, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthBirthOut[4], $y+($this->rheight - $this->lineAdjustment));
		//APGAR score
		$this->Cell($ColumnWidthBirthOut[4], $this->rheight, "", $this->withoutborder, $this->nextline);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$this->Cell($ColumnWidthBirthOut[0], $this->rheight2);
		$this->Cell($ColumnWidthBirthOut[1], $this->rheight2, $birth_outcome_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight2);
		$this->Cell($ColumnWidthBirthOut[2], $this->rheight2, $birth_outcome_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight2);
		$this->Cell($ColumnWidthBirthOut[1], $this->rheight2, $birth_outcome_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight2);
		$this->Cell($ColumnWidthBirthOut[1], $this->rheight2, $birth_outcome_label[3], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthSched[0], $this->rheight, $sub_label[11], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthSched[1], $this->rheight);
		//put scheduled follow-up consultation here
		$sched_date = date("mdY", strtotime($date_sched));
		$sched_date_arr = str_split($sched_date);
		$sched_date_len = strlen($sched_date);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$sched_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italize, $this->fontsize_label4);
		$this->Cell($ColumnWidthSched[0] + $ColumnWidthSched[1], $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 4, $this->rheight2, $date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthDateTime[0], $this->rheight, $sub_label[12], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthDateTime[1], $this->rheight);
		$this->Cell($ColumnWidthDateTime[2], $this->rheight, $date_time_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		//put date of discharge here
		$dis_date = date("mdY", strtotime($date_dis));
		$dis_date_arr = str_split($dis_date);
		$dis_date_len = strlen($dis_date);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$dis_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthDateTime[4], $this->rheight);
		$this->Cell($ColumnWidthDateTime[5], $this->rheight, $date_time_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);


		$dis_time = substr($time_dis, 0, 6);
		$distime = substr($time_dis, 6, 8);
		if($distime=='AM'){
			$dis_time_am = $dis_time;
		}else if($distime=='PM'){
			$dis_time_pm = $dis_time;
		}
		 //delivery time AM
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidthDateTime[6], $this->blockheight, 1);
		$this->Cell($ColumnWidthDateTime[6], $this->rheight3, $dis_time_am, $this->withoutborder, $this->continueline, $this->alignCenter);

		$this->Cell($this->inspace, $this->rheight3);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthDateTime[7], $this->rheight, $date_time_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight3);
		$x = $this->GetX();
		$y = $this->GetY();

		//delivery time PM
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidthDateTime[8], $this->blockheight, 1);
		$this->Cell($ColumnWidthDateTime[8], $this->rheight3, $dis_time_pm, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight3);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthDateTime[9], $this->rheight, $date_time_label[3], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->Cell($ColumnWidthDateTime[0] + $ColumnWidthDateTime[1] + $ColumnWidthDateTime[2], $this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italize, $this->fontsize_label4);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 2, $this->rheight2, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight2);
		$this->Cell($this->blockwidth * 4, $this->rheight2, $date_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthDateTime[4]+$ColumnWidthDateTime[5], $this->rheight);
		$this->Cell($ColumnWidthDateTime[6], $this->rheight2, $date_time_label[4], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthDateTime[7], $this->rheight2);
		$this->Cell($ColumnWidthDateTime[8], $this->rheight2, $date_time_label[4], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln($this->rheight);

		//---------------POSTPARTUM CARE---------------
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $label[3], $this->borderTopBottom, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italize, $this->fontsize_label);
		$this->Cell($ColumnWidthPost[0], $this->rheight);
		$this->Cell($this->blockwidth, $this->rheight, $post_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthPost[1], $this->rheight, $post_label[1], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		for($cnt = 13; $cnt<= 18; $cnt++){
			if($cnt!=15){
				$this->Cell($ColumnWidthPost[0], $this->rheight, $sub_label[$cnt], $this->withoutborder, $this->continueline, $this->alignLeft);
				$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder, $this->continueline);
				$this->Cell($this->space, $this->rheight);
				$x = $this->GetX();
				$y = $this->GetY();
				$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthPost[1], $y+($this->rheight - $this->lineAdjustment));
				$this->Cell($ColumnWidthPost[1], $this->rheight, "", $this->withoutborder, $this->nextline);
			}else{
				$this->Cell($this->totwidth, $this->rheight, $sub_label[$cnt], $this->withoutborder, $this->nextline, $this->alignLeft);

				$this->Cell($this->space, $this->rheight);
				$this->Cell($ColumnWidthPost[0] - $this->space, $this->rheight, $counsel_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
				$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder, $this->continueline);
				$this->Cell($this->space, $this->rheight);
				$x = $this->GetX();
				$y = $this->GetY();
				$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthPost[1], $y+($this->rheight - $this->lineAdjustment));
				$this->Cell($ColumnWidthPost[1], $this->rheight, "", $this->withoutborder, $this->nextline);

				$this->Cell($this->space, $this->rheight);
				$this->Cell($ColumnWidthPost[0] - $this->space, $this->rheight, $counsel_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
				$this->Cell($this->boxwidth, $this->rheight3, "", $this->withborder, $this->continueline);
				$this->Cell($this->space, $this->rheight);
				$x = $this->GetX();
				$y = $this->GetY();
				$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthPost[1], $y+($this->rheight - $this->lineAdjustment));
				$this->Cell($ColumnWidthPost[1], $this->rheight3, "", $this->withoutborder, $this->continueline);
				$this->Ln($this->rheight);
			}

		}
		$this->Ln($this->rheight);

		//-------------Certify---------
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Rect($x, $y, $this->totwidth, $this->rheight*6);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $sub_label[19], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->space*2, $this->rheight);
		$this->Cell($this->totwidth - ($this->space * 2), $this->rheight, $cert_label[0], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->Cell($this->space, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthCert[0], $y+($this->rheight - $this->lineAdjustment));
		$this->Cell($ColumnWidthCert[0] + $ColumnWidthCert[1], $this->rheight);

		//put date signed here
		$cert_date = date("mdY", strtotime($date_cert));
		$cert_date_arr = str_split($cert_date);
		$cert_date_len = strlen($cert_date);
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($cnt = 0; $cnt<$cert_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}
		$this->Ln($this->rheight);

		$this->Cell($this->space, $this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$this->Cell($ColumnWidthCert[0], $this->rheight2, $cert_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthCert[1], $this->rheight2);
		$this->Cell($this->blockwidth * 10, $this->rheight2, $cert_label[2], $this->withoutborder, $this->nextline, $this->alignCenter);
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

	function AcceptPageBreak() {
		return (!$this->bNoPageBreak);
	}

		function CheckPageBreak($h) {
				//If the height h would cause an overflow, add a new page immediately
				if($this->GetY()+$h>$this->PageBreakTrigger){
					$this->AddPage($this->CurOrientation);
					if ($this->PageNo() > 1) {
						$this->addName();
						$x = $this->GetX();
						$y = $this->GetY();
						$this->Line($x, $y, $x+$this->totwidth, $y);
					}
					return true;
				}else{
					return false;
				}
		}

		function DashedRect($x1, $y1, $x2, $y2, $width=1, $nb=15)
		{
				$this->SetLineWidth($width);
				$longueur=abs($x1-$x2);
				$hauteur=abs($y1-$y2);
				if($longueur>$hauteur) {
						$Pointilles=($longueur/$nb)/2; // length of dashes
				}
				else {
						$Pointilles=($hauteur/$nb)/2;
				}
				for($i=$x1;$i<=$x2;$i+=$Pointilles+$Pointilles) {
						for($j=$i;$j<=($i+$Pointilles);$j++) {
								if($j<=($x2-1)) {
										$this->Line($j,$y1,$j+1,$y1); // upper dashes
										$this->Line($j,$y2,$j+1,$y2); // lower dashes
								}
						}
				}
				for($i=$y1;$i<=$y2;$i+=$Pointilles+$Pointilles) {
						for($j=$i;$j<=($i+$Pointilles);$j++) {
								if($j<=($y2-1)) {
										$this->Line($x1,$j,$x1,$j+1); // left dashes
										$this->Line($x2,$j,$x2,$j+1); // right dashes
								}
						}
				}
		}


		function NbLines($w,$txt) {
				//Computes the number of lines a MultiCell of width w will take
				$cw=&$this->CurrentFont['cw'];
				if($w==0)
						$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
				$s=str_replace("?r",'',$txt);
				$nb=strlen($s);
				if($nb>0 and $s[$nb-1]=="?n")
						$nb--;
				$sep=-1;
				$i=0;
				$j=0;
				$l=0;
				$nl=1;
				while($i<$nb)
				{
						$c=$s[$i];
						if($c=="?n")
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

$pdf = new PhilhealthForm3($_GET['encounter_nr'], $_GET['id']);
#$pdf = new PhilhealthForm3();
#include_once($root_path.'modules/billing/billing-gendata-phic-cf2.php');
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->addHeader();
$pdf->addPart1();
$pdf->AddPage();
$pdf->addPart2();
$pdf->Output();
?>