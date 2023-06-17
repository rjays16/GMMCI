<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

define(INFO_INDENT, 10);

class PhilhealthForm1 extends FPDF{
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

	var $blockheight = 4;
	var $blockwidth = 5;

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

	function PhilhealthForm1($encounter_nr, $hcare_id){

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
		$cf2 = "CF1";
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

	//type = 1 for month and day, type = 2 for year
	function writeBlockDate($xcoord, $ycoord, $num, $type){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$len = $this->blockwidth * $number;
		$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);

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
					}

				}
	}

	function writeBlock($xcoord, $ycoord, $num){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$len = $this->blockwidth * $number;
		$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);

			$y1 = $y;
			//$y1 = $y + ($this->blockheight / 2);
			$y2 = $y + $this->blockheight;
			$x1 = $x;
				for($cnt = 0; $cnt<=$number; $cnt++){
					$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
					$x1 += $this->blockwidth;
				}
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

	function addPart1(){
		global $db;

		//labels
		$label = array('PART I - MEMBER and PATIENT INFORMATION',
						'(Member/Representative to fill out all items with the assistance of the Health Care Provider)',
						'1. PhilHealth Identification No. (PIN) : ', '2. Member Category : ', '3. Name of Member',
						'4. Mailing Address : ', '5. Date of Birth : ', '6. Contact Information (if available) : ',
						'7. Name of Patient : ', '9. CERTIFICATION OF MEMBER : ',
						'10. Relationship of the Representative to the Member : ',
						'11. Reason for Signing on Behalf of the Member : '
						);
		$category_label = array('Employed', 'Government', 'Private','Sponsored', 'OFW',
						'Individually Paying', 'Lifetime'
						);
		$name_label = array('Last Name', 'First Name', 'Middle Name');
		$address_label = array('(House Number & Name of Street)', '(Barangay)',
											'(City / Municipality)', '(Province)', '(ZIP Code)');
		$date_label = array('(Month)', '(Day)', '(Year)');
		$contact_label = array('E-mail Address : ', 'Mobile No. : ', 'Landline No. : ');
		$member_label = array('Patient is the Member', 'Patient is a Dependent', 'Child',
										'Spouse', 'Parent', 'Guardian / Next of Kin');
		$cert_label = array('I hereby certify that the herein information are true and correct and may be used for any legal purpose.',
									'Signature Over Printed Name of Member',
									"Signature Over Printed Name of Member's Representative", 'Date Signed (month-day-year)'
									);
		$reason_label = array('Member is Abroad / Out-of-Town',
										'Member is Incapacitated', 'Other Reasons: ');
		$sig_date_label = 'Date Signed (month-day-year)';
		$ColumnWidth = array(40, 50, 40, 60);
		$AddressWidth = array(50, 50, 30);
		$ContactWidth = array(25, 30, 25, 30, 25, 30, 25);
		$ReasonWidth = array($this->boxwidth, 45, $this->boxwidth, 45, $this->boxwidth, 20, 61);
		$combinelen = $ColumnWidth[0] + $ColumnWidth[1] + $ColumnWidth[2];
		$this->is_member = $this->checkifmember();

		//get data for member
			if($this->is_member == 1){
			$sql_1 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
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
						WHERE i.hcare_id = $this->hcare_id AND i.is_principal = 1 AND e.encounter_nr = $this->encounter_nr";

			$result = $db->Execute($sql_1);
			#echo "member!";
		}
		else{
			if ($member_encounter = $this->checkdependence()) {
	//            $sql_2 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
	//                      p.name_3 AS ThirdName, p.name_middle AS MiddleName,
	//                      i.insurance_nr AS IdNum
	//                      FROM care_person AS p
	//                      LEFT JOIN care_encounter AS e ON e.pid = p.pid
	//                      LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
	//                      WHERE i.hcare_id = $this->hcare_id AND i.is_principal = 1 AND e.encounter_nr = $member_encounter";

	//            $result = $db->Execute($sql_2);
				$result = $this->getPrincipalNm($member_encounter);                 // Modified by LST - 06.13.2009
			}
			else{
				$result = $this->getPrincipalNmFromTmp($this->encounter_nr);
			}
				#$result = $this->getPrincipalNmFromTmp($this->encounter_nr);
				#echo "not a member"."<br>";
		}

		$mem = $result->FetchRow();
		$insurance_number = $mem['IdNum'];
		//$insurance_number = '123456789123';
		$fname_member = $mem['FirstName'].",";
		$lname_member = $mem['LastName'].",";
		$mname_member = $mem['MiddleName'];
		$st_member = $mem['Street'];
		$brgy_member = $mem['Barangay'];
		$city_member = $mem['Municity'];
		$prov_member = $mem['Province'];
		$zip_member = $mem['Zipcode'];
		$birth_member = $mem['date_birth'];
		$email = $mem['email'];
		if($mem['phone_1_code']=='0' || $mem['phone_1_code']=='' || $mem['p.phone_1_nr']=='0' || $mem['p.phone_1_nr']==''){
			$landline = "";
		}else{
			$landline = $mem['phone_1_code'].$mem['p.phone_1_nr'];
		}
		if($mem['cellphone_1_nr']=='0' || $mem['cellphone_1_nr']==''){
			$mobile = "";
		}else{
			$mobile = $mem['cellphone_1_nr'];
		}

		//get data for patient
		$sql= "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
				p.name_3 AS ThirdName, p.name_middle AS MiddleName
				FROM care_person AS p
				LEFT JOIN care_encounter AS e ON e.pid = p.pid
				LEFT JOIN care_person_insurance AS pi ON pi.pid = p.pid
				LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr = e.encounter_nr
				WHERE i.hcare_id = $this->hcare_id AND e.encounter_nr = $this->encounter_nr";
		$result2 = $db->Execute($sql);
		$pat = $result2->FetchRow();

		$lname_patient = $pat['LastName'].",";
		$fname_patient = $pat['FirstName'].",";
		$mname_patient = $pat['MiddleName'];
		if($this->is_member == 1){
			$is_member = "/";
		}else{
			$is_dependent = "/";
		}

		#echo "data= ";
		#print_r($res);

		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth, $this->rheight, $label[0], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->Cell($this->totwidth, $this->rheight, $label[1], $this->withoutborder, $this->nextline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$length = $this->GetStringWidth($label[2]);
		$this->Cell($length+$this->space, $this->rheight, $label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		//put philhealth identification number here
		$len = $combinelen - $length;
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$xsave = $this->GetX();
		$xsave2 = $xsave;
		$ysave = $this->GetY();
		$insurance = str_split($insurance_number);
		$ins_len = strlen($insurance_number);
		//echo "length = ".$ins_len;
		//print_r($insurance);
		for($cnt = 0; $cnt < $ins_len; $cnt++){
			if($cnt == 0 || $cnt == 1){
				if($cnt == 0){
					$this->writeBlock($xsave, $ysave, 2);
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else if($cnt == 1){
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$xsave = $this->GetX();
					$ysave = $this->GetY();
				}
			}else if($cnt > 1 && $cnt < 11){
				if($cnt == 2){
					$this->writeBlock($xsave, $ysave, 9);
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else if($cnt > 2 && $cnt!=10){
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$xsave = $this->GetX();
					$ysave = $this->GetY();
				}
			}else{
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->writeBlock($xsave, $ysave, 1);
			}

		}

		#$this->Cell($len, $this->rheight, $insurance_number, $this->withoutborder, $this->continueline, $this->alignLeft);
		#$this->Cell($len, $this->rheight, $insurance_number, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetX($xsave2 + $len);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$length = $this->GetStringWidth($label[3]);
		$this->Cell($length+$this->space, $this->rheight, $label[3], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($combinelen+$this->space, $this->rheight);
		//if answer is employed
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $category_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		//if answer is sponsored
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $category_label[3], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($combinelen+($this->space*2), $this->rheight, $label[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		//if answer is employed (government)
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth-($this->space*2))/2, $this->rheight3, $category_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		//if answer is OFW
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $category_label[4], $this->withoutborder, $this->nextline, $this->alignLeft);

		$x = $this->GetX();
		$y = $this->GetY();
		//put lastname here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ColumnWidth[0], $this->rheight, $lname_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put firstname here
		$this->Cell($ColumnWidth[1], $this->rheight, $fname_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put middlename here
		$this->Cell($ColumnWidth[2], $this->rheight, $mname_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space*2, $this->rheight);
		$this->Line($x+$this->inspace, $y+($this->rheight - $this->lineAdjustment), $x+$combinelen, $y+($this->rheight - $this->lineAdjustment));
		//if answer is employed (private)
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth-($this->space*2))/2, $this->rheight3, $category_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		//if answer is lifetime
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $category_label[6], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rheight, $name_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth[1], $this->rheight, $name_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth[2], $this->rheight, $name_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $category_label[5], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($combinelen+$this->space, $this->rheight, $label[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth[3], $this->rheight, $label[6], $this->withoutborder, $this->nextline, $this->alignLeft);

		//put house number and name of street here
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace, $y+($this->rheight - $this->lineAdjustment), $x+$combinelen, $y+($this->rheight - $this->lineAdjustment));
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ColumnWidth[0]+($ColumnWidth[1]/2), $this->rheight, $st_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put barangay here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ColumnWidth[2]+($ColumnWidth[1]/2), $this->rheight, $brgy_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		//put date of birth here
		$x = $this->GetX();
		$xsave = $x;
		$y = $this->GetY();
		$bdate = date("mdY", strtotime($birth_member));
		$bdate_arr = str_split($bdate);
		$bdate_len = strlen($bdate);

		for($cnt = 0; $cnt<$bdate_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlock($x, $y, 2);
					$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			}else if($cnt > 3){
				if($cnt == 4){
					$this->writeBlock($x, $y, 4);
				}
				$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}

		#print_r($bdate_arr);
		#echo "bdate= ".$bdate;
		#$this->Line($x+$this->lineAdjustment, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth[3], $y+($this->rheight - $this->lineAdjustment));
		#$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		#$this->Cell($ColumnWidth[3], $this->rheight, date("m - d - Y", strtotime($birth_member)), $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth[0]+($ColumnWidth[1]/2), $this->rheight, $address_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[0]+($ColumnWidth[1]/2), $this->rheight, $address_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		#$this->Cell($ColumnWidth[3], $this->rheight, $date_label[0].$date_label[1].$date_label[2], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Cell($this->blockwidth*2, $this->rheight, $date_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight);
		$this->Cell($this->blockwidth*2, $this->rheight, $date_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->blockwidth, $this->rheight);
		$this->Cell($this->blockwidth*4, $this->rheight, $date_label[2], $this->withoutborder, $this->nextline, $this->alignCenter);

		//put city/municipality here
		$combine_add = $AddressWidth[0] + $AddressWidth[1] + $AddressWidth[2];
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace, $y+($this->rheight - $this->lineAdjustment), $x+$combine_add, $y+($this->rheight - $this->lineAdjustment));
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($AddressWidth[0], $this->rheight, $city_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put province here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($AddressWidth[1], $this->rheight, $prov_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put zip code here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($AddressWidth[2], $this->rheight, $zip_member, $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($AddressWidth[0], $this->rheight, $address_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($AddressWidth[1], $this->rheight, $address_label[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($AddressWidth[2], $this->rheight, $address_label[4], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($combinelen, $this->rheight, $label[7], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ContactWidth[0], $this->rheight, $contact_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		//put e-mail address here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ContactWidth[1], $this->rheight, $email, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ContactWidth[1], $y+($this->rheight - $this->lineAdjustment));
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ContactWidth[2], $this->rheight, $contact_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		//put mobile number here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ContactWidth[3], $this->rheight, $mobile, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ContactWidth[3], $y+($this->rheight - $this->lineAdjustment));
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ContactWidth[4], $this->rheight, $contact_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		//put landline number here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ContactWidth[5], $this->rheight, $landline, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ContactWidth[5], $y+($this->rheight - $this->lineAdjustment));
		$this->Ln($this->rheight2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($combinelen, $this->rheight, $label[8], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);
		$str = "8.";
		$len = $this->GetStringWidth($str);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($len, $this->rheight3, $str, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);
		//if patient is member
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $is_member, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth[3]-($len + $this->space + $this->boxwidth), $this->rheight3, $member_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($this->rheight);

		$x = $this->GetX();
		$y = $this->GetY();
		//put lastname of patient here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ColumnWidth[0], $this->rheight, $lname_patient, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put firstname of patient here
		$this->Cell($ColumnWidth[1], $this->rheight, $fname_patient, $this->withoutborder, $this->continueline, $this->alignCenter);
		//put middlename of patient here
		$this->Cell($ColumnWidth[2], $this->rheight, $mname_patient, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space*2, $this->rheight);
		$this->Line($x+$this->inspace, $y+($this->rheight - $this->lineAdjustment), $x+$combinelen, $y+($this->rheight - $this->lineAdjustment));
		$this->Cell($len, $this->rheight);
		//if patient is a dependent
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth[3]-($len + $this->space + $this->boxwidth), $this->rheight3, $member_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rheight, $name_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth[1], $this->rheight, $name_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth[2], $this->rheight, $name_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space*4, $this->rheight);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		//if dependent is a child
		$this->Cell($this->boxwidth, $this->boxheight, $is_dependent, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$len = $this->GetStringWidth($member_label[2]) + $this->inspace;
		$this->Cell($len, $this->rheight3, $member_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		//if dependent is parent
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$len = $this->GetStringWidth($member_label[4]) + $this->inspace;
		$this->Cell($len, $this->rheight3, $member_label[4], $this->withoutborder, $this->nextline, $this->alignCenter);
		$length = $ColumnWidth[0] + $ColumnWidth[1] + $ColumnWidth[2] + ($this->space * 4);

		$this->Cell($length, $this->rheight);
		$len = $this->GetStringWidth($member_label[3]);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight);
		$this->Cell($len, $this->rheight3, $member_label[3], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->Cell($this->totwidth, $this->rheight, $label[9], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label3);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($this->totwidth-$this->space, $this->rheight, $cert_label[0], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln($this->rheight);

		//length of signatures
		$len_sig = round($this->totwidth / 3) - $this->space;
		$x = $this->GetX();
		$y = $this->GetY();
		//put name of member here
		$this->Cell($len_sig, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Line($x+$this->inspace, $y+($this->rheight - $this->lineAdjustment), $x+$len_sig, $y+($this->rheight - $this->lineAdjustment));
		$this->Cell($this->space, $this->rheight);
		//member's representative
		$x = $this->GetX();
		$this->Cell($len_sig, $this->rheight, "", $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$len_sig, $y+($this->rheight - $this->lineAdjustment));

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($len_sig, $this->rheight3, $cert_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight3);
		$this->Cell($len_sig, $this->rheight3, $cert_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight3);
		$this->Cell($len_sig, $this->rheight3, $label[10], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight);

		$this->Cell($this->space, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$x1 = $x;
		$len_date = $len_sig - ($this->space*2);
		//$this->Cell($len_date, $this->rheight);

		for($tmp=0; $tmp<=2; $tmp++){
			if($tmp!=2){
				$this->writeBlockDate($x, $y, 2, 1);
				$this->Cell($this->blockwidth*2, $this->blockheight);
				$xline = $this->GetX();
				$yline = $this->GetY();
				$this->Line($xline+$this->inspace*2, $yline+($this->blockheight/2), $xline+$this->inspace*3, $yline+($this->blockheight/2));
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				$x = $this->GetX();
				$y = $this->GetY();
			}else{
				$this->writeBlockDate($x, $y, 4, 2);
				$this->Cell($this->blockwidth*4, $this->blockheight);
			}

		}

		$this->SetX($x1 + $len_date);
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$len_date, $y+($this->rheight - $this->lineAdjustment));
		$this->Cell($this->space *3, $this->rheight);
		$x = $this->GetX();
		$x1 = $x;
		$y = $this->GetY();

		for($tmp=0; $tmp<=2; $tmp++){
			if($tmp!=2){
				$this->writeBlockDate($x, $y, 2, 1);
				$this->Cell($this->blockwidth*2, $this->blockheight);
				$xline = $this->GetX();
				$yline = $this->GetY();
				$this->Line($xline+$this->inspace*2, $yline+($this->blockheight/2), $xline+$this->inspace*3, $yline+($this->blockheight/2));
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				$x = $this->GetX();
				$y = $this->GetY();
			}else{
				$this->writeBlockDate($x, $y, 4, 2);
				$this->Cell($this->blockwidth*4, $this->blockheight);
			}

		}

		#$this->Cell($len_date, $this->rheight);
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$len_date, $y+($this->rheight - $this->lineAdjustment));
		$this->SetX($x1 + $len_date);
		$this->Cell($this->space*3, $this->rheight);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$len = $this->GetStringWidth($member_label[2]) + $this->inspace;
		$lensave = $len;
		$this->Cell($len, $this->rheight3, $member_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space*2, $this->rheight);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$len = $this->GetStringWidth($member_label[4]) + $this->inspace;
		$this->Cell($len, $this->rheight3, $member_label[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->Cell($this->space, $this->rheight);
		$this->Cell($len_date, $this->rheight, $sig_date_label, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space *3, $this->rheight);
		$this->Cell($len_date, $this->rheight, $sig_date_label, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space*3, $this->rheight);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$len = $this->GetStringWidth($member_label[3]) + $this->inspace;
		$this->Cell($len, $this->rheight3, $member_label[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->space*2+($lensave - $len), $this->rheight);
		$this->Cell($this->boxwidth, $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$len = $this->GetStringWidth($member_label[5]) + $this->inspace;
		$this->Cell($len, $this->rheight3, $member_label[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $label[11], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($this->space, $this->rheight3);
		$this->Cell($ReasonWidth[0], $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($ReasonWidth[1], $this->rheight3, $reason_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ReasonWidth[2], $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($ReasonWidth[3], $this->rheight3, $reason_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ReasonWidth[4], $this->boxheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($ReasonWidth[5], $this->rheight3, $reason_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight3 - $this->lineAdjustment), $x+$ReasonWidth[6], $y+$this->rheight3 - $this->lineAdjustment);
		$this->Cell($ReasonWidth[6], $this->rheight3, "", $this->withoutborder, $this->nextline);
		$this->Ln($this->rheight3);

	}

	function addPart2(){
		global $db;

		$label = array("PART II - EMPLOYER'S CERTIFICATION", '(for employed members only)',
							'1. PhilHealth Employer No. (PEN): ', '2. Contact No.:',
							'3. Business Name and Official Address: ', '4. CERTIFICATION OF EMPLOYER: ');
		$business_label = array('(Business Name of Employer)', '(Building Number and Street Name)',
											'(City / Municipality)', '(Province)', '(ZIP Code)');
		$certify = "     This is to certify that all monthly premium contributions for and in behalf of the member, while employed in this company, including the applicable three (3) monthly premium contributions within the past six (6) months period prior to the first day of this confinement, have been deducted/collected and remitted to PhilHealth, and that the information supplied by the member or his/her representative on Part I are consistent with our available records.";
		$cert_label = array('Signature Over Printed Name of Employer / Authorized Representative',
									'Official Capacity / Designation', 'Date Signed (month-day-year)',
									'(For PhilHealth use only)');
		$ColumnWidth1 = array(110,80);
		$ColumnWidth2 = array(80,40);
		$ColumnWidth3 = array(67, 67, 66);
		$ColumnWidth4 = array(85, 55, 45);

		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($ColumnWidth1[0], $this->rheight, $label[0], $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth1[1], $this->rheight, $label[1], $this->withoutborder, $this->nextline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->Ln($this->rheight2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$len = round($this->GetStringWidth($label[2]));
		$this->Cell($len+$this->space, $this->rheight, $label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		#$this->Cell($ColumnWidth2[0], $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);

		$x = $this->GetX();
		$x1 = $x;
		$xsave = $x;
		$y = $this->GetY();
		$this->writeBlock($x, $y, 2);
		$this->Cell($this->blockwidth*2, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
		$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->writeBlock($x, $y, 9);
		$this->Cell($this->blockwidth*9, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));
		$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->writeBlock($x, $y, 1);

		$this->SetX($x1+$ColumnWidth2[0]);
		/*for($cnt = 0; $cnt<12; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlock($x, $y, 2);
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
					$this->writeBlock($x, $y, 9);
				}
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}              */

		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth2[0], $y+($this->rheight - $this->lineAdjustment));
		$this->Cell($this->space, $this->rheight);
		$len = $this->GetStringWidth($label[3]);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($len, $this->rheight, $label[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Cell($ColumnWidth2[1], $this->rheight, "", $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth2[1], $y+($this->rheight - $this->lineAdjustment));

		$this->Cell($this->totwidth, $this->rheight, $label[4], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln();
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace, $y, $x+$this->totwidth, $y);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $business_label[0], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln();
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace, $y, $x+$this->totwidth, $y);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $business_label[1], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln();
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace, $y, $x+$this->totwidth, $y);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth3[0], $this->rheight, $business_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth3[1], $this->rheight, $business_label[3], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth3[2], $this->rheight, $business_label[4], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $label[5], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->MultiCell($this->totwidth, $this->rheight, "     ".$certify, $this->withoutborder, $this->alignJustify);
		#$this->Ln($this->rheight * 2);
		$this->Ln($this->rheight);
		$this->Cell($ColumnWidth4[0] + $ColumnWidth4[1] + $this->space*2, $this->rheight);

		$x = $this->GetX();
		$y = $this->GetY();

		for($tmp=0; $tmp<=2; $tmp++){
			if($tmp!=2){
				$this->writeBlockDate($x, $y, 2, 1);
				$this->Cell($this->blockwidth*2, $this->blockheight);
				$xline = $this->GetX();
				$yline = $this->GetY();
				$this->Line($xline+$this->inspace*2, $yline+($this->blockheight/2), $xline+$this->inspace*3, $yline+($this->blockheight/2));
				$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
				$x = $this->GetX();
				$y = $this->GetY();
			}else{
				$this->writeBlockDate($x, $y, 4, 2);
				$this->Cell($this->blockwidth*4, $this->blockheight);
			}

		}
		$this->Ln();

		$x = $this->GetX();
		$y = $this->GetY();
		//$this->Line($x+$this->inspace, $y, $x+$ColumnWidth4[0], $y);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth4[0], $this->rheight3, $cert_label[0], $this->borderTop, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight3);
		$this->Cell($ColumnWidth4[1], $this->rheight3, $cert_label[1], $this->borderTop, $this->continueline,  $this->alignCenter);
		$this->Cell($this->space, $this->rheight3);
		$this->Cell($ColumnWidth4[2], $this->rheight3, $cert_label[2], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln();
		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $cert_label[3], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->DashedRect($x, $y, $x+$this->totwidth, $y+50, 0.2, 70);
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

$pdf = new PhilhealthForm1($_GET['encounter_nr'], $_GET['id']);
//include_once($root_path.'modules/billing/billing-gendata-phic-cf2.php');
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->addHeader();
$pdf->addPart1();
$pdf->addPart2();
$pdf->Output();
?>