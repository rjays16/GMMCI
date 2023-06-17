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
	var $fontsize_label1 = 8;
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
	var $rheight1 = 3;

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

		$pg_array = array('215.9','345');
		$this->FPDF('P', 'mm', $pg_array);
		$this->SetDrawColor(0,0,0);
		//$this->SetMargins(5,5,1);
		$this->SetMargins(8,5,1);
		$this->SetAutoPageBreak(true, 2);
		$this->encounter_nr = $encounter_nr;
		$this->hcare_id = $hcare_id;
		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);

	}

	function addHeader()
	{
		//variable for series #
		//$series_number
		$series_number = "092233556789";
		global $root_path, $db;


		
		//labels
		$form = "This form may be reproduced and is NOT FOR SALE";

		$philhealth = "PHILHEALTH";
		$cf2 = "CF1";
		$partner = "Your Partner in Health";
		$claimform = "(Claim Form)";
		$revised = "revised September 2013";
		$series = "Series #";
		$use = "For PhilHealth use only";
		$reminders = "IMPORTANT REMINDERS:";
		$rule_part1 = "PLEASE WRITE IN CAPITAL";
		$rule_letters = "LETTERS";
		$rule_and = "AND";
		$rule_check = "CHECK";
		$rule_part2 = "THE APPROPRIATE BOXES.";
		$reminder_line1_part1 = "For";
		$reminder_line1_part2 = "local availment,";
		$reminder_line1_part3 = "this form together with other PhilHealth claim forms and other supporting documents should be filed within 60 days from date of discharge.";
		$reminder_line1_part4 = "60 DAYS";
		$reminder_line1_part5 = "from date of discharge.";
		$reminder_line2 = "All information required in this form are necessary and claim forms with incomplete information shall not be processed.";
		$reminder_line3 = "FALSE / INCORRECT INFORMATION OR MISREPRESENTATION SHALL BE SUBJECT TO CRIMINAL, CIVIL OR ADMINISTRATIVE LIABILITIES.";
		$availment = "availment of benefits abroad,";
		$this_form = "this form together with other supporting documents should be filed within 180 days from date of discharge.";
		$rep_of = "Representative of the Health Care Institutions (HCI) shall assist the member/authorized representative in filling out this form.";
		$all_info = "All information required in this form are necessary. Claim forms with incomplete information shall not be processed.";
		
		$phic_logo_root = $root_path. 'images/phic_logo.png';
		$phic_logo = $this->Image($phic_logo_root, $this->GetX(), $this->GetY(), 60);
		
		$series_width = 10;   //width for series number
		
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell($this->totwidth, $this->rheight, $form, $this->withoutborder,$this->nextline,$this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label2);
		$width = $this->GetStringWidth($philhealth);
		$this->Cell($width, $this->rheight, $phic_logo, $this->withoutborder, $this->continueline, $this->alignLeft);
		$width = $this->totwidth-10 - ($width + $this->space);
		$this->Cell($width, $this->rheight, $cf2, $this->withoutborder, $this->nextline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight);
		

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth-18, $this->rheight, $claimform, $this->withoutborder, $this->nextline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell($this->totwidth-6, $this->rheight2, $revised, $this->withoutborder, $this->nextline, $this->alignRight);
					
		$this->ln();

		
	
	
		if ($series_number == "")
		{
			$series_number = '            ';

		}
		else{
		$series_number =  $series_number;
	}	

		
		
		$xsave = $this->GetX();
	
		$ysave = $this->GetY();
		$series_no = str_split($series_number);
		$series_len = strlen($series_number);
			$width = $this->GetStringWidth($series);
		$width = $this->totwidth -50 - ($width + $this->space);
		$length = $this->GetStringWidth($series_no);
		
	$this->ln();
	$this->Cell($width, $this->blockheight, $series, $this->withoutborder, $this->continueline, $this->alignRight);
$this-> setXY($xsave+135, $ysave);
		for($cnt = 0; $cnt < $series_len; $cnt++){
			if($cnt == 0 || $cnt > 0){
									
				
					$this->writeBlock($xsave+135, $ysave, 13);
				
			
					$this->Cell($this->blockwidth, $this->blockheight, $series_no[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
		
				
			}

	
		}
					$this->Ln();
		//start
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$width = $this->GetStringWidth($reminders) + $this->space;
		$this->Cell($width, $this->rheight1, $reminders, $this->withoutborder, $this->nextline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$width = $this->GetStringWidth($rule_part1) + $this->space;
		$this->Cell($width, $this->rheight1, $rule_part1, $this->withoutborder, $this->continueline, $this->alignRight);
		
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell(12, $this->rheight1, $rule_letters, $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(6, $this->rheight1, $rule_and, $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell(10, $this->rheight1, $rule_check, $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(35, $this->rheight1, $rule_part2, $this->withoutborder, $this->nextline, $this->alignRight);

		$width = $this->GetStringWidth($reminder_line1_part1) + $this->space;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($width, $this->rheight1, $reminder_line1_part1, $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell(20, $this->rheight1, $reminder_line1_part2, $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(152, $this->rheight1, $reminder_line1_part3, $this->withoutborder, $this->nextline, $this->alignRight);

		$width = $this->GetStringWidth($reminder_line1_part1) + $this->space;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($width, $this->rheight1, $reminder_line1_part1, $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell(35, $this->rheight1, $availment, $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(117, $this->rheight1, $this_form, $this->withoutborder, $this->nextline, $this->alignRight);

		$width = $this->GetStringWidth($rep_of) + $this->space;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($width, $this->rheight1, $rep_of, $this->withoutborder, $this->nextline, $this->alignRight);
		
		$width = $this->GetStringWidth($all_info) + $this->space;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($width, $this->rheight1, $all_info, $this->withoutborder, $this->nextline, $this->alignRight);
		
		$width = $this->GetStringWidth($reminder_line3) + $this->space;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($width+2.5, $this->rheight1, $reminder_line3, $this->withoutborder, $this->nextline, $this->alignRight);
	
	
	
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
		//variable
		//mailing address
			//room number
				$room_number = "";
				$building_name = "";
				$building_num = "";
				$subdivision = "";

//put "/" in a box
		$proceed_III = "";
		$proceed_II = "";


		//labels
		$label = array('PART I - MEMBER INFORMATION',
						'1. PhilHealth Identification No. (PIN) of Member : ',
						 '2. Name of member : ', 
						 '3. Date of Birth:',
						'4. Mailing Address : ',
						 '5. Contact information : ', 
						 '6. Patient is the member? ',
											);
		$category_label = array('Employed', 'Government', 'Private','Sponsored', 'OFW',
						'Individually Paying', 'Lifetime'
						);
		$name_label = array('Last Name', 'First Name', 'Middle Name');
		$address_label = array('Unit/Room No., Floor', 'Building Name',
											'House/Building No.', 'Street',
											'Subdivision/Village','Barangay','City/Municipality',
											'Province','Zip Code');
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
		$landline_= 'Landline No:';
$Mobile_= 'Mobile No:';
$patient_box1= 'Yes, Proceed to Part III';
$patient_box2= 'No, Proceed to Part II';

		$sig_date_label = 'Date Signed (month-day-year)';
		$ColumnWidth = array(40, 50, 40, 60);
		$Column_mailing = array(40, 35, 40, 40, 8);
		$Column_mailing2 = array(40, 45, 35, 70);
		$AddressWidth = array(50, 50, 30);
		$ContactWidth = array(50, 30, 25, 30, 25, 30, 25);
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
	//          
				$result = $this->getPrincipalNm($member_encounter);           
			}
			else{
				$result = $this->getPrincipalNmFromTmp($this->encounter_nr);
			}
				
		}

		$mem = $result->FetchRow();
		$insurance_number = $mem['IdNum'];
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

		
		
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth, $this->rheight, $label[0], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$length = $this->GetStringWidth($label[1]);
		$this->Cell($length+$this->space, $this->rheight, $label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		//put philhealth identification number here
		$len = $combinelen - $length;
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$xsave = $this->GetX();
		$xsave2 = $xsave;
		$ysave = $this->GetY();
		$insurance = str_split($insurance_number);
		$ins_len = 12;
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
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->nextline, $this->alignCenter);
				$this->writeBlock($xsave, $ysave, 1);
			}

		}
$this-> ln(5);
		

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$length = $this->GetStringWidth($label[2]);

		//name member
		$this->Cell($length+$this->space, $this->rheight, $label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
	

		//birthdate
		$this->Cell($this->totwidth-84, $this->rheight, $label[3], $this->withoutborder, $this->continueline, $this->alignRight);

	
		//date of birth
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

$this->ln(5);
		//put lastname here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell($ColumnWidth[0], $this->rheight, $lname_member, $this->borderBottom, $this->continueline, $this->alignCenter);
		//put firstname here
		$this->Cell($ColumnWidth[1], $this->rheight, $fname_member, $this->borderBottom, $this->continueline, $this->alignCenter);
		//put middlename here
		$this->Cell($ColumnWidth[2], $this->rheight, $mname_member, $this->borderBottom, $this->nextline, $this->alignCenter);
	
	//label for fname,mname,lname
		$this->setX(20);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rheight, $name_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth[1], $this->rheight, $name_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth[2], $this->rheight, $name_label[2], $this->withoutborder, $this->nextline, $this->alignLeft);
		
		//mailing address
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell($length+$this->space, $this->rheight, $label[4], $this->withoutborder, $this->nextline, $this->alignLeft);

	
		$x = $this->GetX();
		$y = $this->GetY();
		//put Unit / room number
		$this->Line($x+$this->inspace, $y+($this->rheight - $this->lineAdjustment), $x+$combinelen+10, $y+($this->rheight - $this->lineAdjustment));
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label1);
		$this->Cell($Column_mailing[0]+($ColumnWidth[1]/2)-40, $this->rheight,$room_number, $this->withoutborder, $this->continueline, $this->alignCenter);
			//building name
		$this->Cell($Column_mailing[1]+($ColumnWidth[1]/2)-20, $this->rheight, $building_name, $this->withoutborder, $this->continueline, $this->alignCenter);
	//building number
		$this->Cell($Column_mailing[2]+($ColumnWidth[1]/2)-48, $this->rheight, $building_num, $this->withoutborder, $this->continueline, $this->alignCenter);
		//street
		$this->Cell($Column_mailing[3]+($ColumnWidth[1]/2)-30, $this->rheight, $st_member, $this->withoutborder, $this->continueline, $this->alignCenter);
//subdivision
		$this->Cell($Column_mailing[4]+($ColumnWidth[1]/2)-8, $this->rheight,$subdivision, $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->Cell($this->space, $this->rheight);
	

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($Column_mailing[0]+($ColumnWidth[1]/2)-45, $this->rheight, $address_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($Column_mailing[1]+($ColumnWidth[1]/2)-25, $this->rheight, $address_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($Column_mailing[2]+($ColumnWidth[1]/2)-40, $this->rheight, $address_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($Column_mailing[3]+($ColumnWidth[1]/2)-45, $this->rheight, $address_label[3], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($Column_mailing[4]+($ColumnWidth[1]/2)+5, $this->rheight, $address_label[4], $this->withoutborder, $this->nextline, $this->alignCenter);
		



	
		$x = $this->GetX();
		$y = $this->GetY();
	
		$this->Line($x+$this->inspace, $y+($this->rheight - $this->lineAdjustment), $x+$combinelen, $y+($this->rheight - $this->lineAdjustment));
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label1);
		$this->Cell($Column_mailing[0]+($ColumnWidth[1]/2)-45, $this->rheight, $brgy_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		
		$this->Cell($Column_mailing[1]+($ColumnWidth[1]/2), $this->rheight, $city_member, $this->withoutborder, $this->continueline, $this->alignCenter);
	
		//put house / building number
		//$brgy_member

		$this->Cell($Column_mailing[2]+($ColumnWidth[1]/2)-32, $this->rheight,$prov_member, $this->withoutborder, $this->continueline, $this->alignCenter);
		
		
		$this->Cell($Column_mailing[3]+($ColumnWidth[1]/2)-45, $this->rheight, $zip_member, $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->Cell($this->space, $this->rheight);
	

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($Column_mailing2[0]+($ColumnWidth[1]/2)-45, $this->rheight, $address_label[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($Column_mailing2[1]+($ColumnWidth[1]/2)-25, $this->rheight, $address_label[6], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($Column_mailing2[2]+($ColumnWidth[1]/2)-40, $this->rheight, $address_label[7], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($Column_mailing2[3]+($ColumnWidth[1]/2)-35, $this->rheight, $address_label[8], $this->withoutborder, $this->nextline, $this->alignCenter);
		
		
		

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell(50, $this->rheight, $label[5], $this->withoutborder, $this->continueline, $this->alignLeft);




//put mobile number here
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(-5, $this->rheight, $contact_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);


		$x = $this->GetX();
		$y = $this->GetY();
		//put landline number here

		$this->Line($x+$this->inspace+10, $y+($this->rheight - $this->lineAdjustment), $x+$combinelen-80, $y+($this->rheight - $this->lineAdjustment));
		$this->setX(65);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label1);
		$this->Cell(35, $this->rheight, $landline, $this->withoutborder, $this->continueline, $this->alignLeft);

		

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell(45, $this->rheight, $label[6], $this->withoutborder, $this->continueline, $this->alignRight);
		
		$this ->setX(145);
		
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $proceed_III, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $patient_box1, $this->withoutborder, $this->nextline, $this->alignLeft);
	//mobile

			$this ->setX(47);
		$this->Line($x+$this->inspace+10, $y+($this->rheight - $this->lineAdjustment)+4, $x+$combinelen-80, $y+($this->rheight - $this->lineAdjustment)+4);

	$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ContactWidth[1], $this->rheight, $Mobile_, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this ->setX(65);

		$this->Cell(40, $this->rheight, $mobile, $this->withoutborder, $this->continueline, $this->alignLeft);
	
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		
		$this ->setX(145);
		
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $proceed_II, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $patient_box2, $this->withoutborder, $this->nextline, $this->alignLeft);
			
$this->Ln();
	}

	function addPart2(){
		global $db;

//variable
		//philhealth identification dependent
		$PINDEPENDENT = "";
			//suffix name for patient
		$suffix_patient = "";
		//b-day for patient
		$birth_patient="";

	//put "/" on box
		$rel_child = "";
		$rel_parent = "";
		$rel_spouse = "";
		$dependent_memphil_records_yes = "";
		$dependent_memphil_records_no = "";
	

		$relation_patient = array('Child','Parent','Spouse');
		$label = array("PART II - PATIENT INFORMATION",
		 				'(To be filled-out only if the patient is a dependent)',
							'1. PhilHealth Identification Number (PIN) of Dependent: ',
							 '2. Name of Patient:',
							'3. Date of Birth: ',
							 '4. Relationship to Member: ',
							  "5. Is the patient declared as a dependent in the member's PhilHealth record? ");
		$name_label = array('Last Name', 'Name Suffix',
											'First Name', 'Middle Name', '(example: Dela Cruz, Jr Juan , Sipag)');
		$certify = "     This is to certify that all monthly premium contributions for and in behalf of the member, while employed in this company, including the applicable three (3) monthly premium contributions within the past six (6) months period prior to the first day of this confinement, have been deducted/collected and remitted to PhilHealth, and that the information supplied by the member or his/her representative on Part I are consistent with our available records.";
		$cert_label = array('Signature Over Printed Name of Employer / Authorized Representative',
									'Official Capacity / Designation', 'Date Signed (month-day-year)',
									'(For PhilHealth use only)');
		$ColumnWidth1 = array(110,200);
		$ColumnWidth2 = array(80,40);
		$ColumnWidth3 = array(67, 67, 66);
		$ColumnWidth4 = array(85, 55, 45);

		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($ColumnWidth1[0]+13, $this->rheight, $label[0], $this->withoutborder, $this->nextline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth1[1], $this->rheight, $label[1], $this->withoutborder, $this->nextline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->Ln($this->rheight2);


$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
$len = round($this->GetStringWidth($label[2]));
$this->Cell($len+$this->space, $this->rheight, $label[2], $this->withoutborder, $this->continueline, $this->alignLeft);

///-----------------------


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



	

	if ($PINDEPENDENT == ""){
		$PINDEPENDENT = '              ';
	}
	else
		$PINDEPENDENT = $PINDEPENDENT;

	
		
		$xsave = $this->GetX();
	
		$ysave = $this->GetY();
		$insurance = str_split($PINDEPENDENT);
		$ins_len = strlen($PINDEPENDENT);
		$length = $this->GetStringWidth($series_no);
		
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




$this -> ln();
		$this->SetX($x1+$ColumnWidth2[0]); 
	
		$this->Cell($this->space, $this->rheight);
		$len = $this->GetStringWidth($label[3]);
		$this -> setX(8);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($len, $this->rheight, $label[3], $this->withoutborder, $this->continueline, $this->alignLeft);


		$x = $this->GetX();
		$y = $this->GetY();
		$this->Cell($ColumnWidth2[1], $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);
		
$this->setx(125);
		$this->Cell($this->totwidth, $this->rheight, $label[4], $this->withoutborder, $this->nextline, $this->alignLeft);
		

		//date of birth of patient
		$this->setx(150);
		$x = $this->GetX();
		$xsave = $x;
		$y = $this->GetY();
		
		$bdate_pat = date("mdY", strtotime($birth_patient));
		//$bdate_arr_patient = str_split($bdate_pat);
		$bdate_len_patient = strlen($bdate_pat);
		
	 		if ($bdate_pat == "01011970")
					{
						$bdate_arr_patient = str_split("");	
					}
					else
					{
						$bdate_arr_patient = str_split($bdate_pat);	
					}

		for($cnt = 0; $cnt<$bdate_len_patient; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlock($x, $y, 2);
					$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr_patient[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr_patient[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
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
				$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr_patient[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}

		$x = $this->GetX();
		$y = $this->GetY();
		
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->ln();
	
	$this->Line($x+$this->inspace-190, $y+($this->rheight - $this->lineAdjustment)+4, $x+$combinelen-80, $y+($this->rheight - $this->lineAdjustment)+4);
	$this->Line($x+$this->inspace-190, $y+($this->rheight - $this->lineAdjustment)+4, $x+$combinelen-50, $y+($this->rheight - $this->lineAdjustment)+4);

	
	$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(22, $this->rheight, $lname_patient, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(22, $this->rheight,$suffix_patient, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(22, $this->rheight, $fname_patient, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(22, $this->rheight, $mname_patient, $this->withoutborder, $this->nextline, $this->alignCenter);
		
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(22, $this->rheight, $name_label[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(22, $this->rheight, $name_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(22, $this->rheight, $name_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell(22, $this->rheight, $name_label[3], $this->withoutborder, $this->continueline, $this->alignCenter);
		
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$this->Cell(50, $this->rheight, $name_label[4], $this->withoutborder, $this->nextline, $this->alignCenter);
		
		$this-> SetX(7);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell(50, $this->rheight, $label[5], $this->withoutborder, $this->continueline, $this->alignLeft);

		$this -> SetX(45);
		
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $rel_child, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $relation_patient[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		
		$this -> SetX(58);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $rel_parent, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $relation_patient[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		
		$this -> SetX(75);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $rel_spouse, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $relation_patient[2], $this->withoutborder, $this->nextline, $this->alignLeft);
		

	$this->ln();
		$this -> SetX(35);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell(50, $this->rheight, $label[6], $this->withoutborder, $this->continueline, $this->alignCenter);
		
		$p2lastoption = array('Yes','No, but with attached supporting document/s ');
	$this -> SetX(115);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $dependent_memphil_records_yes, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $p2lastoption[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		

			$this -> SetX(125);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $dependent_memphil_records_no, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $p2lastoption[1], $this->withoutborder, $this->nextline, $this->alignLeft);
		


		$this->Ln(5);
	
	}



	function addPart3(){
		global $db,$root_path;
		//variable
			$date_sign1="";
			$date_sign2="";
			$p3text1="";
			$sign_patient="";
			 $other_specify = "";
			 $other_reason = "";
			 $sign_member_name="";
			 $sign_patient_name="";

			//put "/" on a box
			 $prt3_spouse = "";
			 $prt3_child = "";
			 $prt3_parent = "";
			 $prt3_sibling = "";
			 $prt3_others = "";
			 $prt3_incapacitated = "";
			 $prt3_other_reason = "";
			 $prt3_patient = "";
			 $prt3_rep = "";


		$label = array("PART III - MEMBER CERTIFICATION");
	
		$p3text1 = '       I hereby certify that the herein information are true and correct and may be used for any legal purpose. ';
		$sign_member = '     Signature Over Printed Name of Member     ';
		$sign_patient = "     Signature Over Printed Name of Patient's Representative     ";
		$date_sign = "Date Signed:";
		$p3text2 = 'If patient/representativeis unable to write, put right thumbmark. Patient/representative should be assisted by an HCI representative. Check the appropriate box:';

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
		$this->Cell($ColumnWidth1[0]+20, $this->rheight, $label[0], $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth1[1], $this->rheight, $label[1], $this->withoutborder, $this->nextline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+$this->lineHeaderPlus, $x+$this->totwidth, $y+$this->lineHeaderPlus);
		$this->Ln($this->rheight2);


		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->Cell($ColumnWidth2[0], $this->rheight, $p3text1, $this->withoutborder, $this->continueline, $this->alignLeft);

		
		$this->ln(13);
		$this->Cell(60, $this->rheight,$sign_member_name, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->Cell(90, $this->rheight,$sign_patient_name, $this->withoutborder, $this->nextline, $this->alignRight);
		$this->SetX(35);
		

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(70, $this->rheight, $sign_member, $this->borderTop, $this->continueline, $this->alignCenter);

		
		$this->Cell(20, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
		$this->Cell(70, $this->rheight, $sign_patient, $this->borderTop, $this->nextline, $this->alignRight);


		


		
		$this->Cell(40, $this->rheight, $date_sign, $this->withoutborder, $this->continueline, $this->alignRight);

		$x = $this->GetX();
		$xsave = $x;
		$y = $this->GetY();
		$date1 = date("mdY", strtotime($date_sign1));
		//$bdate_arr1 = str_split($date1);
		$bdate_len1 = strlen($date1);
					
					if ($date1 == "01011970")
					{
						$bdate_arr1 = str_split("");	
					}
					else
					{
						$bdate_arr1 = str_split($date1);	
					}

		for($cnt = 0; $cnt<$bdate_len1; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlock($x, $y, 2);
					$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr1[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr1[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
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
				$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr1[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}



			$this->Cell(40, $this->rheight, $date_sign, $this->withoutborder, $this->continueline, $this->alignRight);

	
		$x = $this->GetX();
		$xsave = $x;
		$y = $this->GetY();
		$date2 = date("mdY", strtotime($date_sign2));
		$bdate_arr2 = str_split($date2);
		$bdate_len2 = strlen($date2);

					if ($date2 == "01011970")
					{
						$bdate_arr2 = str_split("");	
					}
					else
					{
						$bdate_arr2 = str_split($date2);	
					}

		for($cnt = 0; $cnt<$bdate_len2; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlock($x, $y, 2);
					$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr2[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr2[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
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
				$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr2[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}


		$this->ln();

			$this-> SetX(42);
			$this->Cell(15, $this->rheight, 'month', $this->withoutborder, $this->continueline, $this->alignRight);
			$this->Cell(15, $this->rheight, 'day', $this->withoutborder, $this->continueline, $this->alignRight);
			$this->Cell(20, $this->rheight, 'year', $this->withoutborder, $this->continueline, $this->alignRight);

		$this-> SetX(133);
			$this->Cell(15, $this->rheight, 'month', $this->withoutborder, $this->continueline, $this->alignRight);
			$this->Cell(15, $this->rheight, 'day', $this->withoutborder, $this->continueline, $this->alignRight);
			$this->Cell(18, $this->rheight, 'year', $this->withoutborder, $this->continueline, $this->alignRight);

$this->ln(8);

			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, 8);
		
			$this->SetX(43);
			$this->Cell(18, $this->rheight, 'If patient/representativeis unable to write, ', $this->withoutborder, $this->continueline, $this->alignRight);
			
			//image box
			$this->SetX(65);
		$phic_logo_root = $root_path. 'images/box.png';
		$phic_logo = $this->Image($phic_logo_root, $this->GetX(), $this->GetY(), 30);

		$this->SetX(118);
		$this->Cell(9, $this->rheight, 'Relationship of the', $this->withoutborder, $this->continueline, $this->alignRight);


			$this -> SetX(140);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $prt3_spouse, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Spouse', $this->withoutborder, $this->continueline, $this->alignLeft);
		

			$this -> SetX(155);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $prt3_child, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Child', $this->withoutborder, $this->continueline, $this->alignLeft);
		

			$this -> SetX(168);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $prt3_parent, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Parent', $this->withoutborder, $this->nextline, $this->alignLeft);
		//----
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, 8);
		$this->SetX(46);
		$this->Cell(18, $this->rheight, 'put right thumbmark. Patient/representative ', $this->withoutborder, $this->continueline, $this->alignRight);
			

		$this->SetX(127);
		$this->Cell(13, $this->rheight, 'representative to the patient: ', $this->withoutborder, $this->continueline, $this->alignRight);


		$this -> SetX(140);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $prt3_sibling, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Sibling', $this->withoutborder, $this->continueline, $this->alignLeft);
		

			$this -> SetX(155);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight,$prt3_others, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		
		$this->Line(175, $y+($this->rheight - $this->lineAdjustment)+15, $x+35, $y+($this->rheight - $this->lineAdjustment)+15);
		
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Others, Specify', $this->withoutborder, $this->continueline, $this->alignLeft);
		

		$this -> SetX(176);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, $other_specify, $this->withoutborder, $this->nextline, $this->alignLeft);
		

//---
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, 8);
		$this->SetX(48);
		$this->Cell(18, $this->rheight, 'should be assisted by an HCI representative.', $this->withoutborder, $this->continueline, $this->alignRight);
			
$this->SetX(113);
		$this->Cell(18, $this->rheight, 'Reason for signing on', $this->withoutborder, $this->continueline, $this->alignRight);


		$this -> SetX(140);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $prt3_incapacitated, $this->withborder, $this->continueline, $this->alignCenter);
		
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Patient is incapacitated', $this->withoutborder, $this->nextline, $this->alignLeft);
		
		

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, 8);
		$this->SetX(25);
		$this->Cell(18, $this->rheight, 'Check the appropriate box:', $this->withoutborder, $this->continueline, $this->alignRight);
		
		$this->SetX(111);
		$this->Cell(18, $this->rheight, 'behalf of the patient:', $this->withoutborder, $this->continueline, $this->alignRight);


		$this -> SetX(140);
	$this->Line(163, $y+($this->rheight - $this->lineAdjustment)+23, $x+35, $y+($this->rheight - $this->lineAdjustment)+23);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $prt3_other_reason, $this->withborder, $this->continueline, $this->alignCenter);
		//$this->Line(180, $y+($this->rheight - $this->lineAdjustment)+15, $x+38, $y+($this->rheight - $this->lineAdjustment)+15);
		
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Other Reasons:', $this->withoutborder, $this->continueline, $this->alignLeft);
		
		$this -> setX(170);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3,$other_reason, $this->withoutborder, $this->continueline, $this->alignLeft);
		
$this-> ln();
		$this -> SetX(15);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $prt3_patient, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Patient', $this->withoutborder, $this->continueline, $this->alignLeft);
		

		$this -> SetX(35);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($this->boxwidth, $this->boxheight, $prt3_rep, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(($ColumnWidth[3]-$this->boxwidth)/2, $this->rheight3, 'Representative', $this->withoutborder, $this->continueline, $this->alignLeft);
		

		$this->ln(8);
	}



function addPart4(){
		global $db;
			//variable
		$bname_employer="";
		$building_num_street="";
		$b_city="";
		$b_province="";
		$b_zipcode="";
		
		$label = array("PART IV - EMPLOYER'S CERTIFICATION", '(for employed members only)',
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
	

	$this->Cell($this->space, $this->rheight);
		$len = $this->GetStringWidth($label[3]);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($len, $this->rheight, $label[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Cell($ColumnWidth2[1], $this->rheight, "", $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth2[1], $y+($this->rheight - $this->lineAdjustment));

		$this->Cell($this->totwidth, $this->rheight, $label[4], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($this->totwidth, $this->rheight, $bname_employer, $this->withoutborder, $this->nextline, $this->alignCenter);




		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace, $y, $x+$this->totwidth, $y);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $business_label[0], $this->withoutborder, $this->nextline, $this->alignCenter);
		

		$this->Cell($this->totwidth, $this->rheight, $building_num_street, $this->withoutborder, $this->nextline, $this->alignCenter);

		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x+$this->inspace, $y, $x+$this->totwidth, $y);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $business_label[1], $this->withoutborder, $this->nextline, $this->alignCenter);
	
		$this->Cell($ColumnWidth3[0], $this->rheight, $b_city, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth3[1], $this->rheight, $b_city, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth3[2], $this->rheight, $b_city, $this->withoutborder, $this->nextline, $this->alignCenter);

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
	
	
	}

	function addPart5(){
		global $db;

		//variables
		$verify_LHIO = "";
		$verify_PRO = "";
		$member_by = "";

		$label = array("PART V - MEMBERSHIP VERIFICATION ", 
			'(for PhilHealth use only)',
							'LHIO/PRO Signature Over Printed Name',
							 'Date Received:');
		$sub_label = array('LHIO', 'PRO');

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
		$xx = $this->GetX();
		$yy = $this->GetY();

		$this->setXY($xx+30,$yy);
		$this->Cell(50, 6, $sub_label[0]."  ".$verify_LHIO , $this->withborder, $this->continueline, $this->alignLeft);
		

		$this->setXY($xx+30,$yy+6);
		$this->Cell(50, 6, $sub_label[1] ."  ".$verify_PRO, $this->withborder, $this->continueline, $this->alignLeft);
		

		$this->setXY($xx+8,$yy+3);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell(50, 6, $label[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		


		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$xx = $this->GetX();
		$yy = $this->GetY();

		
		$this->setXY($xx+50,$yy-3);
		$this->Cell(50, 6, $member_by, $this->withborder, $this->continueline, $this->alignLeft);
		

		$this->setXY($xx+50,$yy+3);
		$this->Cell(50, 6, $label[2], $this->withborder, $this->continueline, $this->alignLeft);
		

		$this->setXY($xx+42,$yy);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label1);
		$this->Cell(50, 6, "BY:", $this->withoutborder, $this->continueline, $this->alignLeft);



		
	
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
$pdf->addPart3();
$pdf->addPart4();
$pdf->addPart5();

//$pdf->AddPage();

$pdf->Output();
?>