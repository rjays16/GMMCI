<?php
require('./roots.php');
require_once($root_path.'/classes/tcpdf/config/lang/eng.php');
//require($root_path."/classes/fpdf/pdf.class.php");
//define('FPDF_FONTPATH', 'font/');
//require($root_path.'/classes/fpdf/js_form.php');
require($root_path.'/classes/tcpdf/tcpdf.php');
//require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

define('INFO_INDENT', 10);
define('WELLBABY', 12); //added by jasper 07/31/2013 FOR BUGZILLA #188 WELLBABY
define('DEFAULT_NBPKG_NAME','NEW BORN');//Added By Jarel 12/09/2013

class PhilhealthForm2 extends TCPDF {
	var $encounter_nr = '';
    var $HouseCase; //added by jasper 08/27/2013 - FIX FOR BUGZILLA 209 ROOM AND BOARD
    var $Charity; //added by jasper 08/27/2013 - FIX FOR BUGZILLA 209 ROOM AND BOARD
	var $hcare_id = 0;
	var $fontsize_label = 7;
	var $fontsize_label2 = 20;
	var $fontsize_label3 = 7.5;
	var $fontsize_label4 = 6;
	var $fontsize_label5 = 7;
	var $fontsize_answer = 10;
	var $fontsize_answer2 = 12;
	var $fontsize_answer_check = 12;
	var $fontsize_answer_check2 = 10;
	var $fontsize_answer_cert = 8.5;
	var $fontsize_answer_table = 9;

	var $fontstyle_label_bold = "B";
	var $fontstyle_label_bold_italicized = "BI";
	var $fontstyle_label_normal = '';
	var $fontstyle_answer = "B";

//	var $fontfamily_label = "Arial";
//	var $fontfamily_label = "dejavusans";
	var $fontfamily_label = "freeserif";
//	var $fontfamily_answer = "Arial";
//	var $fontfamily_answer = "dejavusans";
	var $fontfamily_answer = "freeserif";

	var $totwidth = 200;
	var $rheight = 5;
	var $rheight2 = 2;
	var $rheight3 = 3;

	var $alignRight = "R";
	var $alignCenter = "C";
	var $alignLeft = "L";
	var $alignJustify = "J";

	var $servicedate_no = 0;

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

	var $nextline = 1;
	var $continueline = 0;

	var $boxheight = 3;
	var $boxwidth = 3;

	var $blockheight = 4;
	var $blockwidth = 4;

	var $inspace = 1;
	var $vspace = 0;
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

	var $meds_array;          // Array of class Medicine.
	var $confinement_array;   // Array of class Confinement.
	var $hospserv_array;      // Array of class HospServices.
	var $diagnosis_array;     // Array of class Diagnosis.
//	var $healthperson_array;  // Array of class Health Personnel.
	var $surgeon_array;       // Array of class Surgeon.
	var $anesth_array;				// Array of Anaesthesiologists.
	var $xray_array;					// Array of class X-Ray Charges
	var $lab_array;           // Array of class Laboratory Charges.
	var $sup_array;           // Array of object supplies and other charges
	var $others_array;        // Array of object other charges.

	var $total_rlo_charges  = 0;    // Total charges of x-ray, lab and others
	var $total_rlo_hospital = 0;    // Total hospital claims in x-ray, lab and others
	var $total_rlo_patient  = 0;    // Total patient claims in x-ray, lab and others

	var $coveredbypkg = false;	// Default flag for coverage ...
    var $pkglimit = 0.00;
    var $issurgical = false;

	function PhilhealthForm2(){

		$pg_array = array('215.9','330.2');
		$this->__construct('P', 'mm', $pg_array, true, 'UTF-8', false);
//		$this->addTTFfont('../../classes/tcpdf/ttfs/arial.ttf', 'TrueType', '', 32);
		$this->SetDrawColor(0,0,0);
		//$this->SetMargins(5,5,1);
		//$this->SetMargins(8,5,1);
        //edited by jasper 05/23/2013
        $this->SetMargins(8,20,1);
        //edited by jasper 05/23/2013
		$this->SetAutoPageBreak(true, 1);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);

		$this->setLanguageArray($l);

		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);
	}

	function addHeader()
	{
		global $root_path, $db;

		//labels
		$form = "This form may be reproduced and is NOT FOR SALE";
		$philhealth = "PHILHEALTH";
		$cf2 = "CF2";
		$partner = "Your Partner in Health";
		$claimform = "(Claim Form)";
		$revised = "revised February 2010";
		$series = "Series #";
		$use = "For PhilHealth use only";
//		$reminders = "IMPORTANT REMINDERS:";
//		$rule_part1 = "PLEASE WRITE IN CAPITAL";
//		$rule_letters = "LETTERS";
//		$rule_and = "AND";
//		$rule_check = "CHECK";
//		$rule_part2 = "THE APPROPRIATE BOXES.";
//		$reminder_line1_part1 = "For";
//		$reminder_line1_part2 = "local confinement,";
//		$reminder_line1_part3 = "this form together with CF1 and other supporting documents should be filed within";
//		$reminder_line1_part4 = "60 DAYS";
//		$reminder_line1_part5 = "from date of discharge.";
//		$reminder_line2 = "All information required in this form are necessary and claim forms with incomplete information shall not be processed.";
//		$reminder_line3 = "FALSE / INCORRECT INFORMATION OR MISREPRESENTATION SHALL BE SUBJECT TO CRIMINAL, CIVIL OR ADMINISTRATIVE LIABILITIES.";
		$series_width = 53;   //width for series number
		$series_number = 13;
		//$space = 5;
		//$inspace = 1;

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $form, $this->withoutborder,$this->nextline,$this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label2);

		$width = $this->GetStringWidth($philhealth);
		$this->Image('../../images/phic_logo.png', $this->lMargin, $this->tMargin+$this->rheight, $width, $this->rheight*3);
//		$this->Cell($width, $this->rheight, $philhealth, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($width, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);

		$width = $this->totwidth - ($width + $this->space);
		$this->Cell($width, $this->rheight, $cf2, $this->withoutborder, $this->nextline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight);

		$width = $this->GetStringWidth($partner) + $this->space;
//		$this->Cell($width, $this->rheight, $partner, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($width, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth-($width + $this->space + 2), $this->rheight-2, $claimform, $this->withoutborder, $this->nextline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$this->Cell($this->totwidth, $this->rheight, $revised, $this->withoutborder, $this->nextline, $this->alignRight);
		$width = $this->totwidth - $series_width;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($width, $this->rheight, $series, $this->withoutborder, $this->continueline, $this->alignRight);
		$x = $this->GetX();
		$y = $this->GetY();
		//$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$series_width, $y+($this->rheight-$this->lineAdjustment));
		//$this->Cell($series_width, $this->rheight, "", $this->withoutborder, $this->nextline, $this->alignLeft); //put series number here
		/*$len = $this->blockwidth * 13;
		$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);

		$y1 = $y;
		$y2 = $y + $this->blockheight;
		$x1 = $x;
		for($cnt = 0; $cnt<=13; $cnt++){
			$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
			$x1 += $this->blockwidth;
		}                 */
		$this->writeBlock($x, $y, $series_number);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$ser_width = $this->GetStringWidth($series);
		$width = $this->totwidth - ($ser_width + $series_width);
		$this->Cell($width, $this->rheight2);
		$this->Cell($ser_width + $series_width, $this->rheight2, $use, $this->withoutborder, $this->nextline, $this->alignCenter);
		/*
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth, $this->rheight, $reminders, $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_part1)+$this->inspace,$this->rheight3,$rule_part1, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_letters)+$this->inspace, $this->rheight3, $rule_letters, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_and)+$this->inspace, $this->rheight3, $rule_and, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_check)+$this->inspace, $this->rheight3, $rule_check, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_part2), $this->rheight3, $rule_part2, $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontsyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($reminder_line1_part1)+$this->inspace, $this->rheight3, $reminder_line1_part1, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($reminder_line1_part2)+$this->inspace, $this->rheight3, $reminder_line1_part2, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontsyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($reminder_line1_part3)+$this->inspace, $this->rheight3, $reminder_line1_part3, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($reminder_line1_part4)+$this->inspace, $this->rheight3, $reminder_line1_part4, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontsyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($reminder_line1_part5), $this->rheight3, $reminder_line1_part5, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Cell($this->totwidth, $this->rheight3, $reminder_line2, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Cell($this->totwidth, $this->rheight3, $reminder_line3, $this->withoutborder, $this->nextline, $this->alignLeft);
		*/
		$this->Ln();
	}

	function writeBlockNumber($xcoord, $ycoord, $num, $insurance){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$len = $this->blockwidth * $number;
		#$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);

		/*if($type == 0)
		{   */
		$y1 = $y;
		//$y1 = $y + ($this->blockheight / 2);
		$y2 = $y + $this->blockheight;
		$x1 = $x;
		$x2 = $x;
		$this->SetLineWidth(0.3);
		if($number < 14){
			$new_number = 14;
			for($cnt = 0; $cnt<$new_number; $cnt++){
				$this->SetLineWidth(0.3);
				if($cnt!=2 && $cnt!=12){
					$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
					$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$this->blockwidth, $y+$this->blockheight+$this->lineAdjustment);
					$x2 = $x1 + $this->blockwidth;
					$this->Line($x2, $y1+$this->lineAdjustment, $x2, $y2+$this->lineAdjustment);
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				}else{
					#echo "for - "."<br>";
					$this->Line($x+$this->inspace*1, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));

					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				}
			}
		}else{
			for($cnt = 0; $cnt<$number; $cnt++){
				$this->SetLineWidth(0.3);
				if($insurance[$cnt]!='-'){
					$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
					$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$this->blockwidth, $y+$this->blockheight+$this->lineAdjustment);
					$x2 = $x1 + $this->blockwidth;
					$this->Line($x2, $y1+$this->lineAdjustment, $x2, $y2+$this->lineAdjustment);
					$this->Cell($this->blockwidth, $this->blockheight, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				}else{
					#$x = $this->GetX();
					#$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));

					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					#$x1 = $this->GetX();
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				}
			}
		}
		$this->SetLineWidth(0.2);
	}

	//type = 1 for month and day, type = 2 for year
	function writeBlockDate($xcoord, $ycoord, $num, $type){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$this->SetLineWidth(0.3);
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
		$this->SetLineWidth(0.2);
	}

	function writeBlockSpecial($xcoord, $ycoord, $width, $height, $num){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$this->SetLineWidth(0.3);
		$len = $width * $number;
		$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);

		$y1 = $y;
		$y2 = $y + $height;
		$x1 = $x;
		for($cnt = 0; $cnt <= $number; $cnt++){
			$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
			$x1 += $width;
		}
		$this->SetLineWidth(0.2);
	}

	function writeBlock($xcoord, $ycoord, $num){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$this->SetLineWidth(0.3);
		$len = $this->blockwidth * $number;
		$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);

		/*if($type == 0)
		{   */
			$y1 = $y;
			//$y1 = $y + ($this->blockheight / 2);
			$y2 = $y + $this->blockheight;
			$x1 = $x;
				for($cnt = 0; $cnt<=$number; $cnt++){
					$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
					$x1 += $this->blockwidth;
				}
		/*}else{
			$y1a = $y;
			$y1b = $y + ($this->blockheight / 2);
			$y2 = $y + $this->blockheight;
			$x1 = $x;
				for($cnt = 0; $cnt<=$number; $cnt++){
					$this->Line($x1, $y1a+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
					$x1 += $this->blockwidth;
				}
		}          */
		$this->SetLineWidth(0.2);
	}

	function checkifmember(){
				global $db;
				$sql = "SELECT i.is_principal AS Member FROM care_person_insurance AS i
								LEFT JOIN care_encounter e ON e.pid = i.pid
								WHERE e.encounter_nr = '{$this->encounter_nr}'";
				$result = $db->Execute($sql);
				$row = $result->FetchRow();
				return $row['Member'];
		}

		function checkdependence(){
				global $db;

//				$sql = "SELECT d.parent_pid AS Parent
//								FROM seg_dependents AS d
//								LEFT JOIN care_encounter AS e ON e.pid = d.dependent_pid
//								where e.encounter_nr = $this->encounter_nr AND d.status = 'member'";

				$sql = "SELECT d.parent_pid AS parent
									FROM care_encounter e
									LEFT JOIN seg_dependents d
									ON (d.dependent_pid = e.pid)
									WHERE e.encounter_nr = '$this->encounter_nr'";
				if ($result = $db->Execute($sql)) {
						if ($row = $result->FetchRow())
								return $row['parent'];
				}
				return false;
		}

	function getPrincipalNm($pid) {
		global $db;

		$strSQL = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName, \n
								p.name_3 AS ThirdName, p.name_middle AS MiddleName, i.insurance_nr AS IdNum     \n
							 FROM care_person AS p LEFT JOIN care_person_insurance AS i ON i.pid = p.pid        \n
							 WHERE i.hcare_id = $this->hcare_id AND i.is_principal = 1 AND p.pid = '$pid'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

	function getPrincipalNmFromTmp($enc_nr) {
		global $db;

		$strSQL = "select member_lname as LastName, member_fname as FirstName, '' as SecondName, \n
								 '' as ThirdName, member_mname as MiddleName, mi.insurance_nr as IdNum   \n
								from seg_insurance_member_info as mi inner join care_encounter as ce       \n
								 on mi.pid = ce.pid                                                      \n
								where ce.encounter_nr = '$enc_nr'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

    //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
    function isWellBaby() {
        global $db;

        $enc_type = 0;
        $strSQL = "select encounter_type ".
                            "   from care_encounter ".
                            "   where encounter_nr = '".$this->encounter_nr."'";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $enc_type = $row['encounter_type'];
            }
        }

        return ($enc_type == WELLBABY);
    }
    //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
    
    function getPackageName($enc_nr) {
        global $db;

        $strSQL = "SELECT
                      sp.package_name
                    FROM
                      (
                        seg_packages sp
                        INNER JOIN
                        seg_billing_pkg sbp
                        ON sp.package_id = sbp.package_id
                      )
                      INNER JOIN
                      seg_billing_encounter sbe
                      ON sbp.ref_no = sbe.bill_nr
                    WHERE sbe.encounter_nr = '$enc_nr'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
                if ($row = $result->FetchRow())
                    //added by jasper 09/09/2013 FOR BUGZILLA #188 - WELLBABY
                    $pkg_name = is_null($row['package_name']) ? "" : $row['package_name'];
	                if (!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false)) {
	                    $pkg_name = substr($pkg_name, 0, strlen($pkg_name) - 2);
	                }
                    //added by jasper 09/09/2013 FOR BUGZILLA #188 - WELLBABY
                    //return $row['package_name'];
                    return $pkg_name;
			}
		}

		return "";
    }

  function hasPrivateAccommodation($enc_nr) {
    global $db;

    $n = 0;
    $strSQL = "SELECT COUNT(*) rcount
                  FROM seg_encounter_case sc
                    INNER JOIN seg_type_case st ON sc.casetype_id = st.casetype_id
                  WHERE encounter_nr = '$enc_nr' AND !sc.is_deleted
                    AND INSTR(UPPER(st.casetype_desc), 'PRIVATE') > 0";
    if($result = $db->Execute($strSQL)){
        if($result->RecordCount()){
            if ($row = $result->FetchRow()) {
              $n = is_null($row['rcount']) ? 0 : $row['rcount'];
            }
        }
    }

    return $n > 0;
  }

	function isHouseCase($encounter_nr) {
		global $db;

		$bhousecase = false;
		$strSQL = "select fn_isHouseCase('".$encounter_nr."') as casetype";
		if ($result=$db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					 $bhousecase = is_null($row["casetype"]) ? false : ($row["casetype"] == 1);
				}
			}
		}

    return $bhousecase && !$this->hasPrivateAccommodation($encounter_nr);
	}

	function addPart1(){
		global  $db;

		//labels
		$part1 = "PART I - PROVIDER INFORMATION (Institutional Health Care Provider to fill out items 1 to 13)";
		$facility = "1. Name of Facility:";
		$address = "2. Address:";
		$acc_no = array('3. PhilHealth Accreditation No.', '(PAN):', '(Institutional Health Care Provider)');
		$pin = array('5. PhilHealth Identification No.', '(PIN): ', '(Member)');
		$category = array('4. Category of Facility','T-L4 / L3', 'ASC', 'RHU', 'S-L2', 'FDC', 'TB DOTS', 'P-L1','MCP', '(OTHERS)');
		$name = array('6. Name of Patient', 'Last Name', 'First Name', 'Middle Name');
		$birth = "7. Date of Birth";
		$age = array('8. Age','Year/s', 'Month/s', 'Day/s');
		$sex = array('9. Sex', 'Male', 'Female');
		$formatdate = "(month-day-year)";
		$confinement = array('10. Confinement Period', 'a. Date Admitted:', 'b. Time Admitted:', 'c. Date Discharged:',
										'd. Time Discharged:', 'e. No. of Days Claimed', 'f. In case of Death,', 'specify date');
		$formattime = array('AM', 'PM');
		$healthcare_tableheader = array('11. Health Care Provider Services', 'Actual Charges',
															'PhilHealth Benefit', 'For PhilHealth Use Only', '(Adjustments / Remarks)');
		$healthcare_sub = array('a. Room and Board', 'b. Drugs and Medicines (Part II for details)',
											'c. X-ray/Lab/Supplies & Others (Part III for details)', 'd. Operating Room Fee',
											'TOTAL', 'e. Benefit Package');
		$room = array('Private', 'Ward');
		$case_type = array('12. Case Type* ', 'A', 'B', 'C', 'D', '* This is only applicable for claims with fee for service payment mechanism');
		$diagnosis_label = array('13. Complete ICD-10 Code/s', '14. Admission Diagnosis',
												'15. Complete Final Diagnosis', '(Professional Health Care Providers to fill out items 14 to 16)');
		$prof_label = array('16. Professional Fees / Charges', 'a. Name of Professional', 'b. PhilHealth Accreditation No.',
									'c. No. of Visits/RVS Code', 'd. Inclusive Dates','(mm-dd-yyyy)', 'e. Total Actual',
									'f. PhilHealth', 'g. Amount paid', 'h. Signature', 'i. Date Signed', 'For PhilHealth', 'PF Charges',
									'Benefit', 'by members', 'i. Date Signed', 'Use Only');

		//label widths
		$ColumnWidth = array(30, 170);
		$ColumnWidth2 = array(60, 70, 70);
		$ColumnWidthName = array(50, 50, 50);
		$ColumnWidthBirth = array(25, 37);
		$ColumnWidthAge = array(15, 8, 15,15,15);
		$ColumnWidthSex = array(15, 15, 15);
		$ColumnWidthConfinement = array(26, 30, 27, 12, 3, 12, 3, 32, 24);
		$ColumnWidthHealthService = array(70, 44, 44, 42);
		$ColumnWidthCaseType = array(5,5,5,5,40, 90);
		$ColumnWidthDiagnosis = array(70, 130);
		$this->ColumnWidthProf = array(60, 40, 20, 20, 20, 20, 20);

		//number of blocks
		$acc_number = 9;

		//---------get data--------
			$objInfo = new Hospital_Admin();
		if ($row = $objInfo->getAllHospitalInfo()) {
				$row['hosp_type'] = strtoupper($row['hosp_type']);
				$row['hosp_name']   = strtoupper($row['hosp_name']);
				$row['hosp_addr1'] = strtoupper($row['hosp_addr1']);
		}else{
				$row['hosp_name'] = strtoupper("Southern Philippines Medical Center");
				$row['hosp_addr1'] = strtoupper("JICA Bldg. J.P. Laurel Bajada, Davao City");
		}

		if($row['hosp_type']=='TH')
			$tertiary = "/";
		else if($row['hosp_type']=='PH')
			$primary = "/";
		else if($row['hosp_type']=='SH')
			$secondary = "/";

		//get patient details
		$enc_obj=new Encounter;

		$encInfo=$enc_obj->getEncounterInfo($this->encounter_nr);
		//echo "encInfo= "."<br>";
		//print_r($encInfo);
		if($encInfo['sex']=='m')
			$is_male = "/";
		else if($encInfo['sex']=='f')
			$is_female = "/";

		//get room type - 1 for ward, 2 for private
//		 $room_nr = $encInfo['current_room_nr'];
//		 $sql_ward="SELECT cw.accomodation_type FROM care_ward AS cw
//												INNER JOIN care_room AS cr ON cr.ward_nr=cw.nr
//												WHERE room_nr='".$room_nr."'";
//							$result_ward = $db->Execute($sql_ward);
//							$row_ward = $result_ward->FetchRow();

			getAccommodationType($this->encounter_nr, $accom_type, $accom_name);

//							$this->room_type = $row_ward['accomodation_type'];
			$this->room_type = $accom_type;

			if($this->room_type == '1'){
				$is_ward = "/";
			}else if($this->room_type == '2'){
				$is_private = "/";
			}

		 $this->auth_rep = $row['authrep'];
		 $this->rep_capacity = $row['designation'];

		 //---------------get insurance number  of member------------
//		 $this->is_member = $this->checkifmember();
//		 if($this->is_member == 1){
				$sql_1 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
									p.name_3 AS ThirdName, p.name_middle AS MiddleName,
									i.insurance_nr AS IdNum
									FROM care_person AS p
									LEFT JOIN care_encounter AS e ON e.pid = p.pid
									LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
									WHERE i.hcare_id = $this->hcare_id /* AND i.is_principal = 1 */ AND e.encounter_nr = '{$this->encounter_nr}'";

				$resultid = $db->Execute($sql_1);
//		 }
//		 else{
		 if (!$resultid) {
				if ($member_pid = $this->checkdependence()) {
//						$sql_2 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
//											p.name_3 AS ThirdName, p.name_middle AS MiddleName,
//											i.insurance_nr AS IdNum
//											FROM care_person AS p
//											LEFT JOIN care_encounter AS e ON e.pid = p.pid
//											LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
//											WHERE i.hcare_id = $this->hcare_id AND e.encounter_nr = $member_encounter";

//						$resultid = $db->Execute($sql_2);
					$resultid = $this->getPrincipalNm($member_pid);                 // Copied from previous form --- LST --- 12.04.2010
				}
				else {
					$resultid = $this->getPrincipalNmFromTmp($this->encounter_nr);
					if (!$resultid) {
						$sql_2 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName,
											p.name_3 AS ThirdName, p.name_middle AS MiddleName,
											i.insurance_nr AS IdNum
											FROM care_person AS p
											LEFT JOIN care_encounter AS e ON e.pid = p.pid
											LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
											WHERE i.hcare_id = $this->hcare_id AND e.encounter_nr = '{$this->encounter_nr}'";

						$resultid = $db->Execute($sql_2);
					}
				}
			}
			if ($resultid) {
						$rowid = $resultid->FetchRow();
						$idnum     = $rowid['IdNum'];
			}else {
						$idnum     = " ";
			}

		//--------end data collection--------


		$x = $this->GetX();
		$y = $this->GetY();
		//draw lines
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+0.5, $x+$this->totwidth, $y+0.5);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth, $this->rheight, $part1, $this->withoutborder, $this->nextline, $this->alignCenter);

		$x = $this->GetX();
		$y = $this->GetY();
		//draw lines
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+0.5, $x+$this->totwidth, $y+0.5);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth[0], $this->rheight, $facility, $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth[1], $y+($this->rheight - $this->lineAdjustment));
		//put the name of facility here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer2);
		$this->Cell($ColumnWidth[1], $this->rheight, $row['hosp_name'], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth[0], $this->rheight, $address, $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidth[1], $y+($this->rheight - $this->lineAdjustment));
		//put the address of the facility here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ColumnWidth[1], $this->rheight, $row['hosp_addr1'], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln($this->rheight2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($this->GetStringWidth($acc_no[0]) + $this->inspace, $this->rheight, $acc_no[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label5);
		$this->Cell($this->GetStringWidth($acc_no[1]) + $this->inspace, $this->rheight, $acc_no[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$length = $this->GetStringWidth($acc_no[0]) + $this->inspace;
		$length2 = $this->GetStringWidth($acc_no[1]) + $this->inspace;
		$this->Cell($ColumnWidth2[0] - ($length + $length2), $this->rheight);
		$x = $this->GetX();
		$x1 = $x;
		$y = $this->GetY();
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($ColumnWidth2[1]/2), $y+($this->rheight - $this->lineAdjustment));
		$this->writeBlock($x, $y, $acc_number);
		$width = $ColumnWidth2[1]/2;

		//philhealth accreditation number
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$hosp_acc = str_split($this->hospaccnum);
		$hosp_acc_len = strlen($this->hospaccnum);

		for($cnt = 0; $cnt < $hosp_acc_len; $cnt++){
			$this->Cell($this->blockwidth, $this->blockheight, $hosp_acc[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
		}

		#$this->Cell($width, $this->rheight, $this->hospaccnum, $this->withoutborder, $this->continueline, $this->alignLeft);
		#$this->Cell($width, $this->rheight);
		$this->SetX($x1+$ColumnWidth2[1]);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2], $this->rheight, $category[0], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidth2[0], $this->rheight2, $acc_no[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth2[1], $this->rheight);
		//check T-L4/L3
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, $tertiary, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3.5 - $this->boxwidth, $this->rheight, $category[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check ASC
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, " ", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3.5 - $this->boxwidth, $this->rheight, $category[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check RHU
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, " ", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3.5 - $this->boxwidth, $this->rheight, $category[3], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln(0.95);

		$this->Cell($this->GetStringWidth($pin[0]) + $this->inspace, $this->rheight, $pin[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label5);
		$this->Cell($this->GetStringWidth($pin[1]) + $this->inspace, $this->rheight, $pin[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$length = $this->GetStringWidth($pin[0]) + $this->inspace;
		$length2 = $this->GetStringWidth($pin[1]) + $this->inspace;
		$this->Cell($ColumnWidth2[0] - ($length + $length2), $this->rheight);
		$x = $this->GetX();
		$x1 = $x;
		$y = $this->GetY();
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+($ColumnWidth2[1]/1.5), $y+($this->rheight - $this->lineAdjustment));
		$width = $ColumnWidth2[1]/1.5;

		//philhealth identification number (member)
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$xsave = $this->GetX();
		$ysave = $this->GetY();
		$insurance_temp = str_split(trim($idnum));
		$insurance_len = count($insurance_temp);
		$temp = 0;
		//reformat phil
		for($cnt = 0; $cnt<$insurance_len; $cnt++){
			if($insurance_temp[$cnt]!='-' && $temp!=2 && $temp!=12 ){
				$insurance[$temp] = $insurance_temp[$cnt];
				$temp++;
			}else if($temp == 2 || $temp == 12){
				$insurance[$temp] = "-";
					if($insurance_temp[$cnt]!='-'){
						$insurance[$temp+1] = $insurance_temp[$cnt];
						$temp += 2;
					}else{
						$temp++;
					}
				}
			}

		$ins_len = strlen(trim($idnum));
		#echo "ins_len = ".$ins_len."<br>";
		if($ins_len <= 1){
			//$ins_len = 12;
			$this->writeBlockNumber($xsave, $ysave, $ins_len, $insurance);
		}else{
			$this->writeBlockNumber($xsave, $ysave, $ins_len, $insurance);
		}
		#echo "id= ".$idnum;
			/*for($cnt = 0; $cnt < $ins_len; $cnt++){
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
			}                   */

		#$this->Cell($width, $this->rheight, $idnum, $this->withoutborder, $this->continueline, $this->alignLeft);
		#$this->Cell($ColumnWidth2[1] - ($width - $this->inspace*2.5), $this->rheight);
//		$this->SetX($x1 + $ColumnWidth2[1]+ $this->inspace*2.5);
		$ret = $this->getMargins();
		$this->SetX($ret['left']);
		$this->Cell($ColumnWidth2[0], $this->rheight2, str_repeat(" ", strlen($acc_no[2])), $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth2[1], $this->rheight);

		//check S-L2
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, $secondary, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3.5 - $this->boxwidth, $this->rheight, $category[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check FDC
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, " ", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3.5 - $this->boxwidth, $this->rheight, $category[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check TB DOTS
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, " ", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3 - $this->boxwidth, $this->rheight, $category[6], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln(0.95);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidth2[0]/2, $this->rheight);
		$this->Cell($ColumnWidth2[0]/2, $this->rheight2, $pin[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth2[1], $this->rheight);
		//check P-L1
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, $primary, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3.5 - $this->boxwidth, $this->rheight, $category[7], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check MCP
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, " ", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3.5 - $this->boxwidth, $this->rheight, $category[8], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check OTHERS
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, " ", $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($this->inspace*2, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->rheight - $this->vspace), $x+($ColumnWidth2[2]/3), $y+($this->rheight - $this->vspace));
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidth2[2]/3 - $this->boxwidth, $this->rheight, "", $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$width = $ColumnWidthName[0] + $ColumnWidthName[1] + $ColumnWidthName[2];
		$this->Cell($width, $this->rheight, $name[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidth2[2]/3, $this->rheight);
		$this->Cell($ColumnWidth2[2]/3.5, $this->rheight2, $category[9], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$x = $this->GetX();
		$y = $this->GetY();
		$width = $ColumnWidthName[0] + $ColumnWidthName[1] + $ColumnWidthName[2];

		$this->Line($x, $y+($this->rheight - $this->vspace), $x+$width, $y+($this->rheight - $this->vspace));

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);

		$this->name_first = strtoupper($encInfo['name_first']);
		$this->name_middle = strtoupper($encInfo['name_middle']);
		$this->name_last = strtoupper($encInfo['name_last']);

		//lastname
		$this->Cell($ColumnWidthName[0], $this->rheight, strtoupper($encInfo['name_last']), $this->withoutborder, $this->continueline, $this->alignCenter, false, '', 1);
		//firstname
		$this->Cell($ColumnWidthName[1], $this->rheight, strtoupper($encInfo['name_first']), $this->withoutborder, $this->continueline, $this->alignCenter);
		//middlename
		$this->Cell($ColumnWidthName[2], $this->rheight, strtoupper($encInfo['name_middle']), $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthName[0], $this->rheight2, $name[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthName[1], $this->rheight2, $name[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthName[2], $this->rheight2, $name[3], $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthBirth[0], $this->rheight, $birth, $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$x1 = $x;
		$y = $this->GetY();

		//date of birth
		$bdate = date("mdY", strtotime($encInfo['date_birth']));
		$bdate_arr = str_split($bdate);
		$bdate_len = strlen($bdate);
		#print_r($bdate_arr);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);

		for($cnt = 0; $cnt<$bdate_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2,1);
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
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight, $bdate_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}

		#$this->Cell($ColumnWidthBirth[1], $this->rheight, date("m-d-Y", strtotime($encInfo['date_birth'])), $this->withoutborder, $this->continueline, $this->alignCenter);
		#$this->Line($x, $y+($this->rheight - $this->inspace), $x+$ColumnWidthBirth[1], $y+($this->rheight - $this->inspace));
		#$this->Cell($this->space, $this->rheight);
		$this->SetX($x1 + $ColumnWidthBirth[1] + $this->space);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthAge[0], $this->rheight, $age[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		#$this->Line($x, $y+($this->rheight - $this->inspace), $x+$ColumnWidthAge[1], $y+($this->rheight - $this->inspace));

		//age
		$mystring = $encInfo['age'];
		$years = "";
		$months = "";
		$days = "";
		$findyears   = 'year';
		$pos1 = strpos($mystring, $findyears);
		if($pos1){
			$years = "/";
			$pos = $pos1;
		}else{
			$findmonths = 'm';
			$pos2 = strpos($mystring, $findmonths);
		}

		if($pos2){
			$months = "/";
			$pos = $pos2;
		}else{
			$finddays = 'd';
			$pos3 = strpos($mystring, $finddays);
		}

		if($pos3){
			$days = "/";
			$pos = $pos3;
		}

		#echo "pos = ".$pos;
		$agestring = substr($mystring, 0, $pos);
		#echo "age? ".$agestring;

		//$ColumnWidthAge = array(20, 35, 5,5,5);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidthAge[1], $this->blockheight, 1);
		$this->Cell($ColumnWidthAge[1], $this->rheight3, $agestring, $this->withoutborder, $this->continueline, $this->alignCenter);
		#$this->Cell($ColumnWidthAge[1], $this->rheight, strtoupper($encInfo['age']." old"), $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthAge[2], $this->rheight, $age[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		//check year/s
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, $years, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthAge[3], $this->rheight, $age[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		//check month/s
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, $months, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthAge[4], $this->rheight, $age[3], $this->withoutborder, $this->continueline, $this->alignCenter);
		//check day/s
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, $days, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidthSex[0], $this->rheight, $sex[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		//male
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, $is_male, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthSex[1], $this->rheight, $sex[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		//female
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, $is_female, $this->withborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthSex[2], $this->rheight, $sex[2], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthBirth[0], $this->rheight);
		$this->Cell($ColumnWidthBirth[1], $this->rheight2, $formatdate, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight);

		//confinement period

		if (!empty($this->confinement_array)) {
			foreach($this->confinement_array as $objconf){
				$date_admitted = $objconf->admit_dt;
				$time_admitted = $objconf->admit_tm;
				$date_discharged = $objconf->discharge_dt;
				$time_discharged = $objconf->discharge_tm;
				$claim_days = $objconf->claim_days;
				$date_death = $objconf->death_dt;
			}
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($this->totwidth, $this->rheight, $confinement[0], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($ColumnWidthConfinement[0], $this->rheight, $confinement[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthConfinement[1], $y+($this->rheight - $this->lineAdjustment));

		//date admitted\
        //added by jasper 03/08/2013
        if($encInfo['encounter_type']==OUT_PATIENT) { //OPD
            $admit_date = date("mdY", strtotime($date_discharged));
            $admit_date_arr = str_split($admit_date);
            $admit_date_len = strlen($admit_date);
        } else {
            $admit_date = date("mdY", strtotime($date_admitted));
            $admit_date_arr = str_split($admit_date);
            $admit_date_len = strlen($admit_date);
        }
        //added by jasper 03/08/2013

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);

		 for($cnt = 0; $cnt<$admit_date_len; $cnt++){
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2, 1);
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

		#$this->Cell($ColumnWidthConfinement[1], $this->rheight, date("m-d-Y", strtotime($date_admitted)), $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidthConfinement[2], $this->rheight, $confinement[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthConfinement[3], $y+($this->rheight - $this->lineAdjustment));

		//$ColumnWidthConfinement = array(25, 30, 25, 20, 1, 5, 5, 25, 30);

		//time admitted  AM
        //added by jasper 03/08/2013
        if ($encInfo['encounter_type']==OUT_PATIENT) { //OPD
            $admit_time_am = "";
            $admit_time_pm = "";
        } else {
            $admit_time = substr($time_admitted, 0, 6);
		    $adtime = substr($time_admitted, 6, 8);
		    if($adtime=='AM'){
			    $admit_time_am = $admit_time;
		    }else if($adtime=='PM'){
			    $admit_time_pm = $admit_time;
		    }
        }

		    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		    $this->writeBlockSpecial($x, $y, $ColumnWidthConfinement[3], $this->blockheight, 1);
		    $this->Cell($ColumnWidthConfinement[3], $this->rheight, $admit_time_am, $this->withoutborder, $this->continueline, $this->alignCenter);
		    $this->Cell($this->inspace, $this->rheight);
    //		$this->Cell($this->inspace, $this->rheight);
		    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		    $this->Cell($ColumnWidthConfinement[4], $this->rheight, $formattime[0], $this->withoutborder, $this->continueline, $this->alignCenter);
    //		$this->Cell($this->inspace, $this->rheight3);
		    $this->Cell($this->inspace, $this->rheight);
		    $x = $this->GetX();
		    $y = $this->GetY();
		    //$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthConfinement[5], $y+($this->rheight - $this->lineAdjustment));
		    //$this->Cell($ColumnWidthConfinement[5] + $ColumnWidthConfinement[6] + $this->space, $this->rheight);
		    //time/ admitted PM
		    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		    $this->writeBlockSpecial($x, $y, $ColumnWidthConfinement[5], $this->blockheight, 1);
		    $this->Cell($ColumnWidthConfinement[5], $this->rheight, $admit_time_pm, $this->withoutborder, $this->continueline, $this->alignCenter);
		    $this->Cell($this->inspace, $this->rheight);
		    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		    #$this->Cell($ColumnWidthConfinement[6], $this->rheight, $formattime[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		    $this->Cell($ColumnWidthConfinement[6], $this->rheight, $formattime[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		    $this->Cell($this->space, $this->rheight);
		    $this->Cell($ColumnWidthConfinement[7], $this->rheight, $confinement[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		    $x = $this->GetX();
		    $y = $this->GetY();
		    #$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthConfinement[5], $y+($this->rheight - $this->lineAdjustment));
		//Number of Days Claimed
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidthConfinement[5], $this->blockheight, 1);
		$this->Cell($ColumnWidthConfinement[5], $this->rheight, $claim_days, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthConfinement[0], $this->rheight);
		$this->Cell($ColumnWidthConfinement[1], $this->rheight, $formatdate, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthConfinement[0], $this->rheight, $confinement[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();

		//date discharged
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

		#$this->Cell($ColumnWidthConfinement[1], $this->rheight, date("m-d-Y", strtotime($date_discharged)), $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthConfinement[1], $y+($this->rheight - $this->lineAdjustment));
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidthConfinement[2], $this->rheight, $confinement[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthConfinement[3], $y+($this->rheight - $this->lineAdjustment));

		//time discharged  AM
        //added by jasper 03/08/2013
        if ($encInfo['encounter_type']==OUT_PATIENT) { //OPD
            $discharge_time_am = "";
            $discharge_time_pm = "";
        } else {
            $discharge_time = substr($time_discharged, 0, 6);
            $distime = substr($time_discharged, 6, 8);
            if($distime=='AM'){
                $discharge_time_am = $discharge_time;
            }else if($distime=='PM'){
                $discharge_time_pm = $discharge_time;
            }
        }

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidthConfinement[3], $this->blockheight, 1);
		$this->Cell($ColumnWidthConfinement[3], $this->rheight, $discharge_time_am, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthConfinement[4], $this->rheight, $formattime[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight);
		$x = $this->GetX();
		$y = $this->GetY();
		//$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthConfinement[5], $y+($this->rheight - $this->lineAdjustment));

		//time discharged PM
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->writeBlockSpecial($x, $y, $ColumnWidthConfinement[5], $this->blockheight, 1);
		$this->Cell($ColumnWidthConfinement[5], $this->rheight, $discharge_time_pm, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->inspace, $this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthConfinement[6], $this->rheight, $formattime[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight);
		#$this->Cell($ColumnWidthConfinement[5] + $ColumnWidthConfinement[6] + $this->space, $this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthConfinement[8], $this->rheight, $confinement[6], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		#$this->Line($x, $y+($this->rheight - $this->lineAdjustment), $x+$ColumnWidthConfinement[8], $y+($this->rheight - $this->lineAdjustment));
		//in case of death, specify date
		if($date_death!='00-00-0000'){
			$death_date = date("mdY", strtotime($date_death));
			$death_date_arr = str_split($death_date);
			$death_date_len = strlen($death_date);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);

			for($cnt = 0; $cnt<$death_date_len; $cnt++){
				if($cnt < 4){
					if($cnt == 0 || $cnt == 2){
						$this->writeBlockDate($x, $y, 2,1);
						$this->Cell($this->blockwidth, $this->blockheight, $death_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					}else{
						$this->Cell($this->blockwidth, $this->blockheight, $death_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
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
					$this->Cell($this->blockwidth, $this->blockheight, $death_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}
			}
		}else{
			for($cnt = 0; $cnt<8; $cnt++){
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
		}


		#$this->Cell($ColumnWidthConfinement[8], $this->rheight, $date_death, $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln($this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);

		$this->Cell($ColumnWidthConfinement[0], $this->rheight2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthConfinement[1], $this->rheight2, $formatdate, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space, $this->rheight2);
		$width = $ColumnWidthConfinement[2] + $ColumnWidthConfinement[3] + $ColumnWidthConfinement[4] + $ColumnWidthConfinement[5] + $ColumnWidthConfinement[6];
		$this->Cell($width, $this->rheight2);
		$this->Cell($this->space*3, $this->rheight);
		$this->Cell($ColumnWidthConfinement[7], $this->rheight2, $confinement[7], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthConfinement[8], $this->rheight2, $formatdate, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight);

		//table header for health care provider services
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($ColumnWidthHealthService[0], $this->rheight, "", $this->borderTopLeftRight, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthHealthService[1], $this->rheight, "", $this->borderTopLeftRight);
		$this->Cell($ColumnWidthHealthService[2], $this->rheight, "", $this->borderTopLeftRight);
		$this->Cell($ColumnWidthHealthService[3], $this->rheight, $healthcare_tableheader[3], $this->borderTopLeftRight, $this->nextline, $this->alignCenter);
		$this->Cell($ColumnWidthHealthService[0], $this->rheight, $healthcare_tableheader[0], $this->borderBottomLeftRight, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthHealthService[1], $this->rheight, $healthcare_tableheader[1], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthHealthService[2], $this->rheight, $healthcare_tableheader[2], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthHealthService[3], $this->rheight, $healthcare_tableheader[4], $this->borderBottomLeftRight, $this->nextline, $this->alignCenter);

		// Determine if covered by package or not ...
		$benefit = 0.00;
		$sql_benefit = "SELECT SUM(sap.coverage) AS benefit FROM seg_applied_pkgcoverage AS sap
										LEFT JOIN seg_billing_encounter AS sbe ON sbe.bill_nr = sap.ref_no
										WHERE sbe.encounter_nr = '".$this->encounter_nr."' ";
		$result_benefit = $db->Execute($sql_benefit);
        //echo $sql_benefit;
		if ($result_benefit) {
			if ($row_benefit = $result_benefit->FetchRow()) {
				if (!is_null($row_benefit['benefit'])) {
					$benefit = $row_benefit['benefit'];
					$this->coveredbypkg = ($benefit > 0);
				}
			}
		}

		foreach($this->hospserv_array as $objroom) {

			 if($i == 0){
					$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
					$this->Cell($this->GetStringWidth($healthcare_sub[0]), $this->rheight, $healthcare_sub[0], $this->borderTopLeft, $this->continueline, $this->alignLeft);
					$this->Cell($this->space, $this->rheight);
					$this->Cell($this->GetStringWidth($room[0])+$this->inspace*2, $this->rheight, $room[0], $this->withoutborder, $this->continueline, $this->alignLeft);
					$x = $this->GetX();
					$y = $this->GetY();

					$this->SetLineWidth(0.3);
					$this->Rect($x, $y+$this->inspace, $this->boxwidth, $this->boxheight);
					$this->SetLineWidth(0.2);

					//check private
					$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
					$this->Cell($this->boxwidth, $this->rheight3, $this->isHouseCase($this->encounter_nr) ? "" : $is_private);
					$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
					$this->Cell($this->inspace, $this->rheight);
					$this->Cell($this->GetStringWidth($room[1])+$this->inspace*2, $this->rheight, $room[1], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();

					$this->SetLineWidth(0.3);
					$this->Rect($x, $y+$this->inspace, $this->boxwidth, $this->boxheight);
					$this->SetLineWidth(0.2);

					//check ward
					$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
					$this->Cell($this->boxwidth, $this->rheight3, ($this->coveredbypkg) ? "" : $is_ward);
					$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
					$width = $this->GetStringWidth($healthcare_sub[0]) + $this->space*2 +($this->boxwidth*2) + $this->GetStringWidth($room[0]) + $this->GetStringWidth($room[1]);
					$this->Cell($ColumnWidthHealthService[0] - $width, $this->rheight);

					$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
					$this->Cell($ColumnWidthHealthService[1], $this->rheight, (!$this->coveredbypkg) ? number_format($objroom->charges,2,'.',',') : " ", $this->withborder, $this->continueline, $this->alignRight);
					$this->Cell($ColumnWidthHealthService[2], $this->rheight, (!$this->coveredbypkg) ? number_format($objroom->claim_hospital,2,'.',',') : " ",$this->withborder, $this->continueline,$this->alignRight);
					$this->Cell($ColumnWidthHealthService[3], $this->rheight, "", $this->withborder, $this->nextline);

			 }else{
					if($i == 4){
						$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
						$this->Cell($ColumnWidthHealthService[0], $this->rheight, $healthcare_sub[$i], $this->withborder, $this->continueline, $this->alignLeft);
						$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
						$this->Cell($ColumnWidthHealthService[1], $this->rheight, (!$this->coveredbypkg) ? number_format($h_charges,2,'.',',') : " ", $this->withborder, $this->continueline, $this->alignRight);
						$this->Cell($ColumnWidthHealthService[2], $this->rheight, (!$this->coveredbypkg) ? number_format($h_hospital_claim, 2, '.', ',') : " ",$this->withborder, $this->continueline,$this->alignRight);
						$this->Cell($ColumnWidthHealthService[3], $this->rheight, "", $this->withborder, $this->nextline);
					}else{
						$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
						$this->Cell($ColumnWidthHealthService[0], $this->rheight, $healthcare_sub[$i], $this->withborder, $this->continueline, $this->alignLeft);
						$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
						$this->Cell($ColumnWidthHealthService[1], $this->rheight, (!$this->coveredbypkg) ? number_format($objroom->charges,2,'.',',') : " ", $this->withborder, $this->continueline, $this->alignRight);
						$this->Cell($ColumnWidthHealthService[2], $this->rheight, (!$this->coveredbypkg) ? number_format($objroom->claim_hospital,2,'.',',') : " ",$this->withborder, $this->continueline,$this->alignRight);
						$this->Cell($ColumnWidthHealthService[3], $this->rheight, "", $this->withborder, $this->nextline);
					}
			 }
			 $i++;
			 $h_charges += $objroom->charges;
			 $h_hospital_claim += $objroom->claim_hospital;

			 $this->total_rlo_patient += $objroom->claim_patient;		// Get patient's claim for meds/services purchased outside for reimbursement.
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
//		$this->Cell($ColumnWidthHealthService[0], $this->rheight, $healthcare_sub[5], $this->withborder, $this->continueline, $this->alignLeft, false, '', 1);
        $this->Cell($this->GetStringWidth($healthcare_sub[5]), $this->rheight, $healthcare_sub[5], "LTB", $this->continueline, $this->alignLeft, false, '', 1);

        $this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
        $this->Cell($ColumnWidthHealthService[0]-$this->GetStringWidth($healthcare_sub[5])+0.9, $this->rheight, ($this->coveredbypkg) ? "  (".$this->getPackageName($this->encounter_nr).")" : "", "TBR", $this->continueline, $this->alignLeft, false, '', 1);

        $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
//		$this->Cell($ColumnWidthHealthService[1], $this->rheight, ($this->coveredbypkg) ? number_format($h_charges,2,'.',',') : "", $this->withborder, $this->continueline, $this->alignRight);
        $this->Cell($ColumnWidthHealthService[1], $this->rheight, "", $this->withborder, $this->continueline, $this->alignRight);

		//benefit package

//		$sql_benefit = "SELECT SUM(sap.coverage) AS benefit FROM seg_applied_pkgcoverage AS sap
//										LEFT JOIN seg_billing_encounter AS sbe ON sbe.bill_nr = sap.ref_no
//										WHERE sbe.encounter_nr = '".$this->encounter_nr."' ";
//		$result_benefit = $db->Execute($sql_benefit);
//		$row_benefit = $result_benefit->FetchRow();
//		$benefit = $row_benefit['benefit'];

		if ($this->pkglimit != 0) $benefit = $this->pkglimit;

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ColumnWidthHealthService[2], $this->rheight, ($this->coveredbypkg) ? number_format($benefit,2,'.',',') : ' ', $this->withborder, $this->continueline, $this->alignRight);
		$this->Cell($ColumnWidthHealthService[3], $this->rheight, " ", $this->withborder, $this->nextline);

		//$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		/*$this->Cell($this->GetStringWidth($healthcare_sub[0]), $this->rheight, $healthcare_sub[0], $this->borderTopLeft, $this->continueline, $this->alignLeft);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($this->GetStringWidth($room[0])+$this->inspace*2, $this->rheight, $room[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Rect($x, $y+$this->inspace, $this->boxwidth, $this->boxheight-1);
		//check private
		$this->Cell($this->boxwidth, $this->boxheight-1, "");
		$this->Cell($this->inspace, $this->rheight);
		$this->Cell($this->GetStringWidth($room[1])+$this->inspace*2, $this->rheight, $room[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Rect($x, $y+$this->inspace, $this->boxwidth, $this->boxheight-1);
		//check ward
		$this->Cell($this->boxwidth, $this->boxheight-1, "");
		$width = $this->GetStringWidth($healthcare_sub[0]) + $this->space*2 +($this->boxwidth*2) + $this->GetStringWidth($room[0]) + $this->GetStringWidth($room[1]);
		$this->Cell($ColumnWidthHealthService[0] - $width, $this->rheight);

		//room actual charges
		$this->Cell($ColumnWidthHealthService[1], $this->rheight, "", $this->withborder, $this->continueline, $this->alignRight);
		//philhealth benefit
		$this->Cell($ColumnWidthHealthService[2], $this->rheight, "", $this->withborder, $this->continueline, $this->alignLeft);
		//remarks
		$this->Cell($ColumnWidthHealthService[3], $this->rheight, "", $this->withborder, $this->nextline);   */

		/*$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		for($i = 1; $i<=5; $i++){
			if($i == 4){
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3);
				$this->Cell($ColumnWidthHealthService[0], $this->rheight, $healthcare_sub[$i], $this->withborder, $this->continueline, $this->alignLeft);
			}else{
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
				$this->Cell($ColumnWidthHealthService[0], $this->rheight, $healthcare_sub[$i], $this->withborder, $this->continueline, $this->alignLeft);
			}

			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
			$this->Cell($ColumnWidthHealthService[1], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
			$this->Cell($ColumnWidthHealthService[2], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
			$this->Cell($ColumnWidthHealthService[3], $this->rheight, "", $this->withborder, $this->nextline, $this->alignCenter);
		}  */
		$this->Ln();

		$x = $this->GetX();
		$y = $this->GetY() + $this->rheight;
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($this->GetStringWidth($case_type[0])+$this->inspace, $this->rheight, $case_type[0], $this->withoutborder, $this->continueline, $this->alignLeft);

		//determine case type
		if(!empty($this->diagnosis_array)) {
						$objdiag = $this->diagnosis_array[0];

						//$case_arr = array('','','','');
						//if (($objdiag->case_type >= 1) && ($objdiag->case_type <= 4)) $case_arr[$objdiag->case_type-1] = 'X';
						if($objdiag->case_type == '1')
							$case_a = "/";
						else if($objdiag->case_type == '2')
							$case_b = "/";
						else if($objdiag->case_type == '3')
							$case_c = "/";
						else if($objdiag->case_type == '4')
							$case_d = "/";

		}

		//check case type A
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, (!$this->coveredbypkg) ? $case_a : "", $this->withborder);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthCaseType[0], $this->rheight, $case_type[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		//check case type B
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, (!$this->coveredbypkg) ? $case_b : "", $this->withborder);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthCaseType[1], $this->rheight, $case_type[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		//check case type C
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, (!$this->coveredbypkg) ? $case_c : "", $this->withborder);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthCaseType[2], $this->rheight, $case_type[3], $this->withoutborder, $this->continueline, $this->alignCenter);
		//check case type D
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->Cell($this->boxwidth, $this->boxheight, (!$this->coveredbypkg) ? $case_d : "", $this->withborder);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($ColumnWidthCaseType[3], $this->rheight, $case_type[4], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->space*2, $this->rheight);
		$this->Cell($ColumnWidthCaseType[4], $this->rheight, $diagnosis_label[0]);
		//complete ICD-10 Code/s


				if(!empty($this->diagnosis_array)) {
						$diagnosis = "";
						$code = "";
						reset($this->diagnosis_array);
						$i = 1;
                $j = 1;
						foreach($this->diagnosis_array as $objdiag) {
							if (isset($objdiag->fin_diagnosis) && (trim($objdiag->fin_diagnosis) != '') && !is_null($objdiag->fin_diagnosis)) {
//                                $diagnosis .= (($diagnosis == "") ? $i.". " : "\n".$i.". ").$objdiag->fin_diagnosis;
                                $diagnosis .= (($diagnosis == "") ? "" : ";\n").$objdiag->fin_diagnosis;
                                $i++;
                            }

                            if ($objdiag->code != '') {
                                if ($code != '') $code .= ", ";
//                                $code .= $j++.". ".$objdiag->code;
                                $code .= $objdiag->code;
                                $j++;
							}

//						$code .= (($code == "") ? "" : " ").$objdiag->code.",";
						}
						/*$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
						$length = $this->GetStringWidth($code);
						if($length < $ColumnWidthCaseType[5]) {
								$this->Cell($length, $this->rheight, $code, $this->withoutborder,$this->continueline,$this->alignLeft);
						}
						else {
								$this->MultiCell($ColumnWidthCaseType[5], $this->rheight, $code, $this->withoutborder,$this->alignJustify);
						}      */
//						}

					$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
					$length = $this->GetStringWidth($code);

//					$this->CellFitScale($length, $this->rheight, $code, $this->withoutborder,$this->continueline,$this->alignLeft);
//					$this->Cell($length, $this->rheight, $code, $this->withoutborder,$this->continueline,$this->alignLeft, false, '', 1);
					$this->MultiCell($ColumnWidthCaseType[4]-$this->GetStringWidth($diagnosis_label[0]), $this->rheight, $code, $this->withoutborder, $this->alignLeft, 0, 1, '', '', true, 0, false, true, 0, 'T', true);

//					if($length < $ColumnWidthCaseType[5]) {
//						$this->Cell($length, $this->rheight, $code, $this->withoutborder,$this->continueline,$this->alignLeft);
//					}
//					else {
//						$this->MultiCell($ColumnWidthCaseType[5], $this->rheight, $code, $this->withoutborder,$this->alignJustify);
//					}
				}

				//get diagnosis
				$this->final_diagnosis = $diagnosis;
				if($encInfo['encounter_type']=='2')
					$this->admission_diagnosis = $encInfo['chief_complaint'];
				else
					$this->admission_diagnosis = $encInfo['er_opd_diagnosis'];

		//$this->MultiCell($ColumnWidthCaseType[5], $this->rheight, "", $this->withoutborder, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$xsave = $this->GetX();
		$ysave = $this->GetY();
		$this->SetXY($x, $y);
		$this->Cell($this->totwidth, $this->rheight, $case_type[5], $this->withoutborder, $this->nextline, $this->alignLeft);
		$ynew = $this->GetY();

		if($ysave > $ynew){
			#$this->SetXY($xsave, $ysave);
			$this->SetY($ysave);
		}else{
			#$this->SetXY($xsave, $ynew);
			$this->SetY($ynew);
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label5);
		$this->Cell($this->totwidth, $this->rheight, $diagnosis_label[3], $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$xrect = $this->GetX();
		$yrect = $this->GetY();
		$this->Cell($ColumnWidthDiagnosis[0], $this->rheight, $diagnosis_label[1], "LTR", $this->continueline, $this->alignLeft);
		$xrect2 = $this->GetX();
		$this->Cell($ColumnWidthDiagnosis[1], $this->rheight, $diagnosis_label[2], "LTR", $this->nextline, $this->alignLeft);

		//$this->Row(array($this->admission_diagnosis, $this->final_diagnosis));

//		$nline_admission = $this->NbLines($ColumnWidthDiagnosis[0], $this->admission_diagnosis);
        $nline_admission = $this->getNumLines($this->admission_diagnosis, $ColumnWidthDiagnosis[0], true, true,'',1);
//		$nline_final = $this->NbLines($ColumnWidthDiagnosis[1], $this->final_diagnosis);
        $nline_final = $this->getNumLines($this->final_diagnosis, $ColumnWidthDiagnosis[1], true, true,'',1);

        $max_line = 6;
        $nline_admission = ($nline_admission < $max_line) ? $max_line : $nline_admission;
        $nline_final = ($nline_final < $max_line) ? $max_line : $nline_final;

		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);

        $nline_final = ($nline_admission > $nline_final) ? $nline_admission : $nline_final;

        //Admission Diagnosis
        //added by jasper 08/01/2013 FOR BUGZILLA #188 WELLBABY
        if($encInfo['encounter_type'] == WELLBABY) {
            $this->setFontSubsetting(false);
            $this->TextField('admission_dx',$ColumnWidthDiagnosis[0], $this->rheight*$nline_final, array('multiline'=>true, 'lineWidth'=>0, 'borderStyle'=>'none'), array(V=>'Input admitting diagnosis here'));
        } else {
        $this->MultiCell($ColumnWidthDiagnosis[0], $this->rheight*$nline_final, $this->admission_diagnosis, $this->withoutborder, $this->alignLeft, 0, 1, '', '', true, 0, false, true, 0, 'T', true);
        }
        //added by jasper 08/01/2013 FOR BUGZILLA #188 WELLBABY

        $newy = $this->GetY();
        $h1 = $newy - $y;
		$this->SetXY($x+$ColumnWidthDiagnosis[0], $y);

		//Complete Final Diagnosis
        $this->MultiCell($ColumnWidthDiagnosis[1], $this->rheight*$nline_final, $this->final_diagnosis, $this->withoutborder, $this->alignLeft, 0, 1, '', '', true, 0, false, true, 0, 'T', true);

        $newy = $this->GetY();
        $h2 = $newy - $y;

        $h1 = ($h1 > $h2) ? $h1 : $h2;

        // Draw the borders ...
        $this->SetXY($x, $y);

        //Admission Diagnosis
        $this->MultiCell($ColumnWidthDiagnosis[0], $h1, "", $this->withborder, $this->alignLeft, 0, 1, '', '', true, 0, false, true, 0, 'T', true);
        $this->SetXY($x+$ColumnWidthDiagnosis[0], $y);

        //Complete Final Diagnosis
        $this->MultiCell($ColumnWidthDiagnosis[1], $h1, "", $this->withborder, $this->alignLeft, 0, 1, '', '', true, 0, false, true, 0, 'T', true);

		$this->Ln($this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Cell($this->totwidth, $this->rheight, $prof_label[0], $this->withoutborder, $this->nextline, $this->alignLeft);

		//table header for professional fees
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->ColumnWidthProf[0], $this->rheight, $prof_label[1], $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[1], $this->rheight, $prof_label[3], $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[2], $this->rheight, $prof_label[6], $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[3], $this->rheight, $prof_label[7], $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[4], $this->rheight, $prof_label[8], $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[5], $this->rheight, $prof_label[9], $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[6], $this->rheight, $prof_label[11], $this->borderTopLeftRight, $this->nextline, $this->alignCenter);

		$this->Cell($this->ColumnWidthProf[0], $this->rheight, $prof_label[2], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[1], $this->rheight, $prof_label[4], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[2], $this->rheight, $prof_label[12], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[3], $this->rheight, $prof_label[13], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[4], $this->rheight, $prof_label[14], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[5], $this->rheight, $prof_label[15], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidthProf[6], $this->rheight, $prof_label[16], $this->borderBottomLeftRight, $this->nextline, $this->alignCenter);
		//end table header

		$this->SetLineWidth(0.2);

		$this->total_pf_charge = 0;
		$blank = "";
		$blank1 = "";
		$blank2 = "";
		$blank3 = "";

//		if(!empty($this->healthperson_array)){
//			// --- this portion is not reachable ---- by LST ------
//			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
//
//			$this->Ln($this->RowHeight * 0.5);
//
//			$services1 = "";
//			$healthperson_num = count($this->healthperson_array);
//			foreach($this->healthperson_array as $objphysician){
//				if ($objphysician->role_area != 'D4') {
//					$services1 .= (($services1 != '') ? " " : "").$objphysician->servperformance;
//					$physician = $objphysician->name.";".$objphysician->accnum;
//					$this->RowDoc(array($physician, $services1, $objphysician->profcharges, $objphysician->claim_physician, $blank1, $blank2, $blank3));
//				}
//			}
//
//						// --- this portion is not reachable ---- by LST ------
//		}else{
//			$healthperson_num = 0;
//		}

		$healthperson_num = 0;
		if(!empty($this->anesth_array)) {
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
			$services3 = "";
			foreach($this->anesth_array as $objperson) {
				if ($objperson->role_area != 'D4') {
					$healthperson_num++;
					if ($objperson->inclusive_dates != '')
	//					$services3 .= (($services3 != '') ? " " : "").$objperson->servperformance."\n".$objperson->inclusive_dates;
                        //edited by jasper 02/27/2013
						//$services3 = $objperson->servperformance."\n".$objperson->inclusive_dates;
                        $services3 = $objperson->servperformance."**".$objperson->inclusive_dates;
					else
	//					$services3 .= (($services3 != '') ? " " : "").$objperson->servperformance."\n ";
						//$services3 = $objperson->servperformance."\n ";
                        $services3 = $objperson->servperformance."** ";
					$length = $this->GetStringWidth($services3);
					$person = $objperson->name.";".(($objperson->accnum == '') ? "            " : $objperson->accnum);
					$this->RowDoc(array($person, $services3, $objperson->profcharges, $objperson->claim_physician, $blank1, $blank2, $blank3));
				}
			}
		}

		if(!empty($this->surgeon_array)) {
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
			$services2 = "";
			$surgeon_num = count($this->surgeon_array);
			foreach($this->surgeon_array as $objsurgeon) {
//				$services2 .= (($services2 != '') ? " " : "").$objsurgeon->servperformance;
				$services2 = $objsurgeon->servperformance;
				$length = $this->GetStringWidth($services2);
				$surgeon_name = $objsurgeon->name.";".(($objsurgeon->accnum == '') ? "            " : $objsurgeon->accnum);
				if ($objsurgeon->operation_dt != '')
                    //edited by jasper 02/27/2013
					//$serv = $services2."\n".$objsurgeon->operation_dt;
                    $serv = $services2."**".$objsurgeon->operation_dt;
				else
					//$serv = $services2."\n ";
                    $serv = $services2."** ";
				$this->RowDoc(array($surgeon_name, $serv, $objsurgeon->profcharges, $objsurgeon->claim_physician, $blank1, $blank2, $blank3));
			}
		}else{
			$surgeon_num = 0;
		}

		$anesth_num = 0;
		if(!empty($this->anesth_array)) {
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
			$services3 = "";
			foreach($this->anesth_array as $objperson) {
				if ($objperson->role_area == 'D4') {
					$anesth_num++;
					if ($objperson->inclusive_dates != '')
	//					$services3 .= (($services3 != '') ? " " : "").$objperson->servperformance."\n".$objperson->inclusive_dates;
                        //edited by jasper 02/27/2013
						//$services3 = $objperson->servperformance."\n".$objperson->inclusive_dates;
                        $services3 = $objperson->servperformance."**".$objperson->inclusive_dates;
					else
	//					$services3 .= (($services3 != '') ? " " : "").$objperson->servperformance."\n ";
						//$services3 = $objperson->servperformance."\n ";
                        $services3 = $objperson->servperformance."** ";
					$length = $this->GetStringWidth($services3);
					$person = $objperson->name.";".(($objperson->accnum == '') ? "            " : $objperson->accnum);
					$this->RowDoc(array($person, $services3, $objperson->profcharges, $objperson->claim_physician, $blank1, $blank2, $blank3));
				}
			}
		}

		$total_prof = $healthperson_num + $surgeon_num + $anesth_num;
		$prof_num_default = 4;
		if($total_prof!=$prof_num_default)
			$prof_num = $prof_num_default - $total_prof;
		else
			$prof_num = 0;

        //removed by jasper 05/20/2013
		/*for($cnt = 0; $cnt < $prof_num; $cnt++){
			$this->RowDoc2(array($blank, $blank, $blank, $blank, $blank1, $blank2, $blank3));
		}*/
	}

	function addPart2(){

		//labels and column widths
        //added by jasper 05/23/2013
        $this->Cell($this->totwidth, $this->rheight, ' ', $this->withoutborder, $this->nextline, $this->alignCenter);
        //added by jasper 05/23/2013
		$tableheader = array('Generic/Brand name', 'Preparation', 'Qty', 'Unit Price', 'Actual', 'PhilHealth',
										'Charges', 'Benefit');
		$title = "PART II - DRUGS AND MEDICINES";
		$this->ColumnWidth = array(70, 30, 18, 22, 25, 35);
		$this->Alignment = array('L', 'L', 'C', 'R', 'R', 'R');
		$total = "TOTAL";
		$tablewidth = $this->ColumnWidth[0] + $this->ColumnWidth[1] +$this->ColumnWidth[2] + $this->ColumnWidth[3] + $this->ColumnWidth[4] + $this->ColumnWidth[5];

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label5);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Cell($this->totwidth, $this->rheight, $title, $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Line($x, $y+($this->rheight), $x+$this->totwidth, $y+($this->rheight));

		//table header for drugs and medicines
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->Cell($this->ColumnWidth[0], $this->rheight, "", $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[1], $this->rheight, "", $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[2], $this->rheight, "", $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[3], $this->rheight, "", $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[4], $this->rheight, $tableheader[4], $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[5], $this->rheight, $tableheader[5], $this->borderTopLeftRight, $this->nextline, $this->alignCenter);

		$this->Cell($this->ColumnWidth[0], $this->rheight, $tableheader[0], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[1], $this->rheight, $tableheader[1], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[2], $this->rheight, $tableheader[2], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[3], $this->rheight, $tableheader[3], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[4], $this->rheight, $tableheader[6], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->ColumnWidth[5], $this->rheight, $tableheader[7], $this->borderBottomLeftRight, $this->nextline, $this->alignCenter);
		//end table header

		if (!empty($this->meds_array)) {
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_table);
					foreach ($this->meds_array as $v) {
						$total_actual += $v->actual_charges;
						$total_hospital += $v->claim_hospital;
						//$this->Row(array($test1, $v->brand, $v->preparation, $v->qty, $v->unit_price, $v->actual_charges, $v->claim_hospital, $v->claim_patient));
						$this->Row2(array($v->gen_name, $v->preparation, $v->qty, $v->unit_price, $v->actual_charges, $v->claim_hospital));

					}
		}else{
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell($tablewidth, $this->rheight, " ", $this->withborder, $this->continueline);
			$this->Ln(6);
		}

		//total
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1] +$this->ColumnWidth[2], $this->rheight, "", $this->borderTopLeftBottom);
		$this->Cell($this->ColumnWidth[3], $this->rheight, $total, $this->borderTopBottom, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		if(!empty($this->meds_array)){
			$this->Cell($this->ColumnWidth[4], $this->rheight, ($this->coveredbypkg) ? " " : number_format($total_actual,2,'.',','), $this->borderTopBottom, $this->continueline, $this->alignRight);
			$this->Cell($this->ColumnWidth[5], $this->rheight, ($this->coveredbypkg) ? " " : number_format($total_hospital,2,'.',','), $this->borderTopRightBottom, $this->continueline, $this->alignRight);
		}else{
			$this->Cell($this->ColumnWidth[4], $this->rheight, ($this->coveredbypkg) ? " " : number_format(0,2,'.',','), $this->borderTopBottom, $this->continueline, $this->alignRight);
			$this->Cell($this->ColumnWidth[5], $this->rheight, ($this->coveredbypkg) ? " " : number_format(0,2,'.',','), $this->borderTopRightBottom, $this->continueline, $this->alignRight);
		}

		$this->Ln();
		$this->Ln($this->rheight2);
	}

	function addPart3(){
		//labels and column widths
        //added by jasper 05/23/2013
        $this->Cell($this->totwidth, $this->rheight, ' ', $this->withoutborder, $this->nextline, $this->alignCenter);
        //added by jasper 05/23/2013
        if ($this->GetY()>305) {
            $this->AddPage();
            $this->addName();
        }
        //added by jasper 05/23/2013
		$title = "PART III - X-RAY, LABORATORIES, SUPPLIES AND OTHERS (use additional sheet if necessary)";
		$tableheader = array('Particulars', 'Qty', 'Unit Price', 'Actual', 'PhilHealth', 'Charges', 'Benefit');
		$particulars = array('A. X-Ray (Imaging)', 'B. Laboratories/Diagnostics', 'C. Supplies and Others');
		$this->widths = array(100, 18, 22,25,35);
		$total = "TOTAL";
		$attach = "Official receipts for drugs and medicines / supplies purchased by member from external sources as well as laboratory procedures done outside the hospital which are necessary for the confinement are attached to this claim.";
		$tablewidth = $this->widths[0] + $this->widths[1] + $this->widths[2] + $this->widths[3] + $this->widths[4];

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label5);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Cell($this->totwidth, $this->rheight, $title, $this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Line($x, $y+($this->rheight), $x+$this->totwidth, $y+($this->rheight));

		//table header for xray, lab, supplies and others...
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->Cell($this->widths[0], $this->rheight, "", $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[1], $this->rheight, "", $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[2], $this->rheight, "", $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[3], $this->rheight, $tableheader[3], $this->borderTopLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[4], $this->rheight, $tableheader[4], $this->borderTopLeftRight, $this->nextline, $this->alignCenter);

		$this->Cell($this->widths[0], $this->rheight, $tableheader[0], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[1], $this->rheight, $tableheader[1], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[2], $this->rheight, $tableheader[2], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[3], $this->rheight, $tableheader[5], $this->borderBottomLeftRight, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[4], $this->rheight, $tableheader[6], $this->borderBottomLeftRight, $this->nextline, $this->alignCenter);
		//end table header

		//for X-ray
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->Cell($this->widths[0], $this->rheight, $particulars[0], $this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->widths[1], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[2], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[3], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[4], $this->rheight, "", $this->withborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_table);
		if (!empty($this->xray_array)) {
					foreach ($this->xray_array as $objxray) {
							$this->RowPart4(array($objxray->particulars, $objxray->qty, $objxray->unit_price,$objxray->actual_charges,$objxray->claim_hospital));

						$this->total_rlo_charges  += $objxray->actual_charges;
						$this->total_rlo_hospital += $objxray->claim_hospital;
					}
			}else{
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell($tablewidth, $this->rheight, " ", $this->withborder, $this->continueline);
			$this->Ln();
		 }
		//end X-ray

		//for Lab
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->Cell($this->widths[0], $this->rheight, $particulars[1], $this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->widths[1], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[2], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[3], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[4], $this->rheight, "", $this->withborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_table);
		if (!empty($this->lab_array)) {
					foreach ($this->lab_array as $objlab) {
							$this->RowPart4(array($objlab->particulars, $objlab->qty, $objlab->unit_price,$objlab->actual_charges,$objlab->claim_hospital));

						$this->total_rlo_charges  += $objlab->actual_charges;
						$this->total_rlo_hospital += $objlab->claim_hospital;
					}
			}else{
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell($tablewidth, $this->rheight, " ", $this->withborder, $this->continueline);
			$this->Ln();
		 }
		//end Lab

		//for Supplies
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->Cell($this->widths[0], $this->rheight, $particulars[2], $this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->widths[1], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[2], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[3], $this->rheight, "", $this->withborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->widths[4], $this->rheight, "", $this->withborder, $this->nextline, $this->alignCenter);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_table);
		if (!empty($this->sup_array)) {
					foreach ($this->sup_array as $objsup) {
							$this->RowPart4(array($objsup->particulars, $objsup->qty, $objsup->unit_price,$objsup->actual_charges,$objsup->claim_hospital));
							$this->total_rlo_charges  += $objsup->actual_charges;
							$this->total_rlo_hospital += $objsup->claim_hospital;
					}

			}else{
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell($tablewidth, $this->rheight, " ", $this->withborder, $this->continueline);
			$this->Ln();
			//$this->Cell(0, $this->RowHeight, "No records found for this report...", 0, 1, 'L');
			}

		//Total
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($this->widths[0] + $this->widths[1], $this->rheight, "", $this->borderTopLeftBottom);
		$this->Cell($this->widths[2], $this->rheight, $total, $this->borderTopBottom, $this->continueline, $this->alignCenter);
		 if (!empty($this->lab_array) || !empty($this->sup_array) || !empty($this->xray_array)) {
						//Total
						//$this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
						//$this->Cell($length2, $rowtable, "Total", 1, 0, 'R');
						$this->Cell($this->widths[3], $this->rheight, ($this->coveredbypkg) ? " " : number_format($this->total_rlo_charges,2,'.',','), $this->borderTopBottom, $this->continueline, $this->alignRight);
						$this->Cell($this->widths[4], $this->rheight, ($this->coveredbypkg) ? " " : number_format($this->total_rlo_hospital,2,'.',','), $this->borderTopRightBottom, $this->nextline, $this->alignRight);
						$this->Ln($this->rheight2);
						/*$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
						$this->bNoPageBreak = true;
						$this->Cell($tablewidth, $this->rheight, " ", 1, 0, 'L');
						$this->bNoPageBreak = false;
						$this->Ln($this->rheight);   */
			 }else{
					$this->Cell($this->widths[3], $this->rheight, ($this->coveredbypkg) ? " " : number_format(0,2,'.',','), $this->borderTopBottom, $this->continueline, $this->alignRight);
					$this->Cell($this->widths[4], $this->rheight, ($this->coveredbypkg) ? " " : number_format(0,2,'.',','), $this->borderTopRightBottom, $this->nextline, $this->alignRight);
			 }
		$this->Ln();


        //$this->cell(0,0,$this->GetX() . " " . $this->GetY() . " " . $this->PageNo(), 0, 1);
        //$pdf->SetXY($pdf->GetX(), $pdf->GetY()+20);
        //added by jasper 05/23/2013
        if ($this->GetY()>310) {
            $this->AddPage();
            $this->addName();
            $this->Cell($this->totwidth, $this->rheight, ' ', $this->withoutborder, $this->nextline, $this->alignCenter);
        }
        //added by jasper 05/23/2013

		//check if with attached official receipts
		$this->Cell($this->boxwidth, $this->boxheight, (($this->total_rlo_patient > 0) ? "/" : ""), $this->withborder);
		$this->Cell($this->inspace, $this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->MultiCell($this->totwidth - ($this->boxwidth + $this->inspace), $this->rheight, $attach.(($this->total_rlo_patient > 0) ? "  (TOTAL = P".number_format($this->total_rlo_patient, 2).")" : ""), $this->withoutborder, $this->alignLeft, false, 0, '', '', true, 1);
		$this->Ln($this->rheight * 2);
	}

	function addPart4(){

		/*$ret = $this->CheckPageBreak($this->rheight, $this->GetY());*/
        //removed and added by jasper 05/23/2013
        if ($this->CheckPageBreak($this->rheight, $this->GetY())) {
            $this->addName();
        }

        //added by jasper 05/23/2013
        if ($this->GetY()>295) {
            $this->AddPage();
            $this->addName();
        }
        $this->Cell($this->totwidth, $this->rheight, ' ', $this->withoutborder, $this->nextline, $this->alignCenter);
        //added by jasper 05/23/2013
        //removed and added by jasper 05/23/2013
        //added by jasper 04/18/2013
        /*if ($this->GetY()==5 && $this->PageNo()>1) {
            $this->SetTopMargin(15);
        }*/
        //added by jasper 04/18/2013

//		if($ret){
//			$this->addName();
//		}

		//labels and widths
		$label = array('Signature Over Printed Name of Authorized Representative', 'Official Capacity / Designation', 'Date Signed (month-day-year)');
		$title = "PART IV - CERTIFICATION OF INSTITUTIONAL HEALTH CARE PROVIDER";
		$certify1 = "I certify that services rendered were recorded in the patient's chart and hospital records and that the herein information given are true and correct.";
		$certify2 = "The foregoing items and charges are in compliance with the applicable laws, rules and regulations.";
		$ColumnWidth = array(80, 10, 50, 10, 50);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label5);
		$this->Cell($this->totwidth, $this->rheight, $title, $this->borderTopBottom, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $certify1, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Cell($this->totwidth, $this->rheight, $certify2, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln($this->rheight);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_cert);
		$this->Cell($ColumnWidth[0], $this->rheight, $this->auth_rep, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[1], $this->rheight);
		$this->Cell($ColumnWidth[2], $this->rheight, $this->rep_capacity, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[3], $this->rheight);
		$this->Cell($ColumnWidth[4], $this->rheight, date("m-d-Y"), $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rheight, $label[0], $this->borderTop, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[1], $this->rheight);
		$this->Cell($ColumnWidth[2], $this->rheight, $label[1], $this->borderTop, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[3], $this->rheight);
		$this->Cell($ColumnWidth[4], $this->rheight, $label[2], $this->borderTop, $this->nextline, $this->alignCenter);
		$this->Ln();
	}

	function addPart5(){
	 /*
		if ($this->CheckPageBreak($this->rheight * 12))
			$this->SetY($this->tMargin);
		else
			$this->SetY($this->GetY()-0.39);   */
		//$ret = $this->CheckPageBreak($this->rheight*12);
        //removed and added by jasper 05/23/2013
        if ($this->CheckPageBreak($this->rheight*12)) {
            $this->addName();
            $this->Cell($this->totwidth, $this->rheight, ' ', $this->withoutborder, $this->nextline, $this->alignCenter);
        }
        //removed and added by jasper 05/23/2013
        //$this->Cell(0,0,$this->GetX() . " " . $this->GetY() . " " . $this->PageNo(), 0, 1);
        //added by jasper 04/18/2013
        /*if ($this->GetY()==10 && $this->PageNo()>1) {
            $this->SetTopMargin(15);
        }*/
        //added by jasper 04/18/2013
//		if($ret){
//			$this->addName();
//		}

		//labels width
		$title = "PART V - CONSENT TO ACCESS PATIENT RECORD/S";
		$certify1 = "I hereby consent to the examination by PhilHealth of the patient's medical records for the sole purpose of verifying the veracity of this claim.";
		$certify2 = "I hereby hold PhilHealth or any of its officers, employees and/or representatives free from any and all liabilities relative to the herein-mentioned consent which I have voluntarily and willingly given in connection with this claim for reimbursement before PhilHealth.";
		$label = array("Signature Over Printed Name of Patient", "Signature Over Printed Name of Patient's Representative",
							"Relation of the Representative to the Patient:");
		$date_signed = "Date Signed (month-day-year)";
		$relation = array('Spouse', 'Child', 'Parent', 'Guardian /', 'Next of Kin');
		$ColumnWidth = array(50,10,70,10,60);
		$width = 12;
		$reason = array('Reason for Signing on Behalf of the Patient:', 'Patient is Incapacitated', 'Other Reasons:');
		$ColumnWidthReason = array(50, 50, 25, 75);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label5);
		$this->Cell($this->totwidth, $this->rheight, $title, $this->borderTopBottom, $this->nextline, $this->alignCenter);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $certify1, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->MultiCell($this->totwidth, $this->rheight, $certify2, $this->withoutborder, $this->alignLeft);
		$this->Ln($this->rheight*2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rheight, $label[0], $this->borderTop, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[1], $this->rheight);
		$this->Cell($ColumnWidth[2], $this->rheight, $label[1], $this->borderTop, $this->continueline, $this->alignCenter);
//		$this->Cell($ColumnWidth[3], $this->rheight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidth[3] + $ColumnWidth[4], $this->rheight, $label[2], $this->withoutborder, $this->nextline, $this->alignCenter);

		$this->Cell($ColumnWidth[1]/2, $this->rheight);
		//patient date signed
		$this->Cell($ColumnWidth[0]-$ColumnWidth[1], $this->rheight, "", $this->borderBottom, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[1]*2, $this->rheight);
		//representative date signed
		$this->Cell($ColumnWidth[2]-$ColumnWidth[1], $this->rheight, "", $this->borderBottom, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[3], $this->rheight);

		//check spouse
		$this->Cell($this->boxwidth, $this->boxheight+1, "", $this->withborder, $this->continueline);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($width, $this->rheight, $relation[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check child
		$this->Cell($this->boxwidth, $this->boxheight+1, "", $this->withborder, $this->continueline);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($width, $this->rheight, $relation[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check parent
		$this->Cell($this->boxwidth, $this->boxheight+1, "", $this->withborder, $this->continueline);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($width, $this->rheight, $relation[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check guardian
		$this->Cell($this->boxwidth, $this->boxheight+1, "", $this->withborder, $this->continueline);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($width + ($this->inspace*2), $this->rheight, $relation[3], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($ColumnWidth[0], $this->rheight, $date_signed, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[1], $this->rheight);
		$this->Cell($ColumnWidth[2], $this->rheight, $date_signed, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidth[3], $this->rheight);
		$this->Cell($ColumnWidth[4], $this->rheight, $relation[4] . "     ", $this->withoutborder, $this->nextline, $this->alignRight);
		$this->Ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthReason[0], $this->rheight);
		$this->Cell($ColumnWidthReason[1], $this->rheight, $reason[0], $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->Cell($ColumnWidthReason[0], $this->rheight);
		//check if patient is incapacitated
		$this->Cell($this->boxwidth, $this->boxheight+1, " ", $this->withborder, $this->continueline);
		$this->Cell($ColumnWidthReason[1]-$this->boxwidth, $this->rheight, $reason[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		//check for other reasons
		$this->Cell($this->boxwidth, $this->boxheight+1, " ", $this->withborder, $this->continueline);
		$this->Cell($ColumnWidthReason[2]-$this->boxwidth, $this->rheight, $reason[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		//other reasons:
		$this->Cell($ColumnWidthReason[3], $this->rheight, " ", $this->borderBottom, $this->nextline, $this->alignLeft);
	}

	function Header() {

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

	 function RowPart4($data) {
			 $row = $this->rheight;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
				//print_r($nb=max($nb,$this->NbLines($this->widths[$i],$data[$i])));
//				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
				$nb=max($nb,$this->getNumLines($data[$i], $this->widths[$i],true,true,'',1));

			$h=$row*$nb;
			//Issue a page break first if needed
			//$this->CheckPageBreak($h);
            //removed and added by jasper 05/23/2013
            if ($this->CheckPageBreak($h)) {
                $this->addName();
            }
            //removed and added by jasper 05/23/2013
			//Draw the cells of the row
			for($i=0;$i<count($data);$i++)
			{
				$w=$this->widths[$i];
				//$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
				//Save the current position
				$x=$this->GetX();
				$y=$this->GetY();
				//Draw the border
				//$this->Rect($x,$y,$w,$h);
				//Print the text

				if($i==0){
							$length = $this->GetStringWidth($data[0]);
							if($length < $this->widths[0]) {
								$this->Cell($w, $h, $data[0],$this->withborder,$this->continueline, $this->alignLeft, false, '', 1);
							}
							else{
//								if ($_SESSION['sess_temp_userid'] == 'medocs') {
//									var_dump("Testing this part!");
//								}
								$nrows = $this->MultiCell($w, $h, $data[0],$this->withborder, $this->alignJustify, false, 0, '','', true, 1);
							}
				}
				else{
							if($i==1) {
								$this->Cell($w, $h, $data[$i], $this->withborder,$this->continueline, $this->alignCenter, false, '', 1);
							}
							else{
								$this->Cell($w, $h, ( ($i == (count($data)-1)) && ($data[$i] == 0.00) )  ? " " : ($this->coveredbypkg) ? " " : number_format($data[$i],2,'.',','), $this->withborder, $this->continueline, $this->alignRight, false, '', 1);
							}
				}
				//$this->MultiCell($w,5,$data[$i],1,$a);
				//Put the position to the right of the cell
				$this->SetXY($x+$w,$y);
			}
			//Go to the next line
			$this->Ln($h);
		}

	function Row($data) {
	$row = $this->RowHeight-1;
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));

				$nb2=$this->NbLines($this->widths[1],$data[1]);
				$nb3=$this->NbLines($this->widths[0],$data[0]);

//				if($nb2>$nb3){
//					$nbdiff = $nb2 - $nb3;
//					 $nbdiff = $nbdiff*$row;
//					k == 1;
//				}
//				else if($nb3>$nb2){
//					$nbdiff = $nb3 - $nb2;
//					 $nbdiff = $nbdiff*$row;
//					k==0;
//				}
//				else{
//					$nbdiff = 0;
//				}


				//$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
				//print_r($nb2, $nb3);

				//$nb = $nb*2;
				//print_r($nb);

		$h=$row*$nb;
		//Issue a page break first if needed
		//$this->CheckPageBreak($h);
        //removed and added by jasper 05/23/2013
        if ($this->CheckPageBreak($h)) {
            $this->addName();
        }
        //removed and added by jasper 05/23/2013
        //added by jasper 04/18/2013
        /*if ($this->GetY()==5 && $this->PageNo()>1) {
            $this->SetTopMargin(15);
        }*/
        //added by jasper 04/18/2013
		//Draw the cells of the row

		for($i=0;$i<count($data);$i++) {
				$w=$this->widths[$i];
				$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
				//Save the current position
				$x=$this->GetX();
				$y=$this->GetY();
				//Draw the border
				//$this->Rect($x,$y,$w,$h);
				//Print the text
				//print_r($data[i]);
				//number_format($objroom->charges,2,'.',',')
				if($i==2){
					$length = $this->GetStringWidth($data[$i]);
						if($length < $this->widths[$i]){
							$this->Cell($w, $h, $data[$i],1,0,'L');
						}
						else{
							$this->MultiCell($w, $row,$data[$i],1,'L');
						}
				}
				else if($i>2){
					//print_r(i);
						if($i==3){
							$this->Cell($w, $h, $data[$i],1,0,'R');
						}
						else{
							$this->Cell($w, $h, number_format($data[$i],2,'.',','),1,0, 'R');
						}

				}
				else{
					$length = $this->GetStringWidth($data[$i]);
						if($length < $this->widths[$i]){
							$this->Cell($w, $h, $data[$i],1,0,'L');
						}
						else{
							// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
							$this->MultiCell($w, $row,$data[$i],1,'L');

							//$this->MultiCell($length, $row,$data[$i],1,'L');

						}

				}

				//Put the position to the right of the cell
				$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function addName(){
		 //$this->SetY(5);
         //edited by jasper 04/18/2013
         //$this->cell(0,0,$this->GetX() . " " . $this->GetY() . " " . $this->PageNo(), 0, 1);
         if (($this->GetY()<=21 && $this->GetY()>=5) && $this->PageNo()>1) {
             $this->SetTopMargin(20);
		     $addname = "Patient: ".$this->name_last.", ".$this->name_first." ".$this->name_middle." - (con't)";

		     $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		     $this->Cell($this->totwidth, $this->rheight, $addname, $this->withoutborder, $this->nextline, $this->alignLeft);
         }
         //edited by jasper 04/18/2013
	}

	function AcceptPageBreak() {
        //added by jasper 04/18/2013
        if ($this->PageNo()>1) {
            $this->SetTopMargin(15);
        }
        //added by jasper 04/18/2013
		return (!$this->bNoPageBreak);
	}

	 function Row2($data)
	 {
		$row = $this->rheight;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
				//print_r($nb=max($nb,$this->NbLines($this->widths[$i],$data[$i])));
//				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
				$nb=max($nb,$this->getNumLines($data[$i], $this->ColumnWidth[$i], true, true,'',1));

//			for($i=0;$i<count($data);$i++)
//					$nb=max($nb,$this->NbLines($this->ColumnWidth[$i],$data[$i]));
//					$nb2=$this->NbLines($this->ColumnWidth[1],$data[1]);
//					$nb3=$this->NbLines($this->ColumnWidth[5],$data[5]);
//					if($nb2>$nb3){
//						$nbdiff = $nb2 - $nb3;
//						 $nbdiff = $nbdiff*$row;
//						k == 1;
//					}
//					else if($nb3>$nb2){
//						$nbdiff = $nb3 - $nb2;
//						 $nbdiff = $nbdiff*$row;
//						k==0;
//					}
//					else{
//						$nbdiff = 0;
//					}

					//$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
					//print_r($nb2, $nb3);

					//$nb = $nb*2;
					//print_r($nb);
			$h=$row*$nb;
			//Issue a page break first if needed
			//$this->CheckPageBreak($h);
            //removed and added by jasper 05/23/2013
            if ($this->CheckPageBreak($h)) {
                $this->addName();
            }
            //removed and added by jasper 05/23/2013
            //added by jasper 04/18/2013
            /*if ($this->GetY()==5 && $this->PageNo()>1) {
               $this->SetTopMargin(15);
            }*/
            //added by jasper 04/18/2013
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
								if($i < 3)
									$this->Cell($w, $h, $data[$i], $this->withborder, $this->continueline, $this->Alignment[$i]);
								else
									$this->Cell($w, $h, ( ($i == (count($data)-1)) && ($data[$i] == 0.00) )  ? " " : ($this->coveredbypkg) ? " " : number_format($data[$i],2,'.',','), $this->withborder, $this->continueline, $this->Alignment[$i]);
								//$this->Cell($this->ColumnWidth[$i], $this->rheight, $data[$i], 1, 0, 'L');
							}
							else{
								// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								//$this->MultiCell($w, $row,$data[$i],1,'L');
								if($i < 3)
									$this->MultiCell($w, $h, $data[$i], $this->withborder, $this->Alignment[$i]);
								else
									$this->MultiCell($w, $h, ( ($i == (count($data)-1)) && ($data[$i] == 0.00) )  ? " " : ($this->coveredbypkg) ? " " : number_format($data[$i],2,'.',','), $this->withborder, $this->Alignment[$i]);
								//$this->MultiCell($this->ColumnWidth[$i], $this->rheight, $data[$i], 1, 'L');

								//$this->MultiCell($length, $row,$data[$i],1,'L');

							}

					//Put the position to the right of the cell
					//$this->SetXY($x+$w,$y);
					$this->SetXY($x+$this->ColumnWidth[$i],$y);
			}
			//Go to the next line
			$this->Ln($h);
		}

	 function RowDoc($data)
	 {
            //added by jasper 06/23/2013
            if ($this->GetY()>315) {
                $this->AddPage();
                $this->addName();
            }
            //added by jasper 06/23/2013
            //echo print_r($data);
			$row = $this->rheight;
			//Calculate the height of the row
			$pf_col0_max_chars = 25;

			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->ColumnWidthProf[$i],$data[$i]));

//			$nb2=$this->NbLines($this->ColumnWidthProf[0],$data[0]);
//			$nb3=$this->NbLines($this->ColumnWidthProf[1],$data[1]);
//			$nb2 = $nb2 + 2;

					#echo "nb2 = ".$nb2." nb3 = ".$nb3."<br>";
//			if($nb2>$nb3){
//				$nbdiff = $nb2 - $nb3;
//				 $nbdiff = $nbdiff*$row;
//				k == 1;
//			}
//			else if($nb3>$nb2){
//				$nbdiff = $nb3 - $nb2;
//				 $nbdiff = $nbdiff*$row;
//				k==0;
//			}
//			else{
//				$nbdiff = 0;
//			}

					//$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
					//print_r($nb2, $nb3);

					//$nb = $nb*2;
					//print_r($nb);
			$h=$row*$nb;
			//Issue a page break first if needed
			if ($this->CheckPageBreak($h*2,'',true)) {
                $this->addName();
                $this->Cell($this->totwidth, $this->rheight, ' ', $this->withoutborder, $this->nextline, $this->alignCenter);
            }
            //added by jasper 04/18/2013
            /*if ($this->GetY()==5 && $this->PageNo()>1) {
               $this->SetTopMargin(15);
            } */
            //added by jasper 04/18/2013
			//Draw the cells of the row
			$adj = 2;

			for($i=0;$i<count($data);$i++)
			{
					$w=$this->ColumnWidthProf[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
					//Save the current position

					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
							$length = $this->GetStringWidth($data[$i]);
							#echo "length = ".$length."<br>";
							/*if($length < $this->ColumnWidthProf[$i]){
								//$this->Cell($w, $h, $data[$i],1,0,'L');
								if($i < 2 || $i==5 || $i==6)
									$this->Cell($w, $h, $data[$i], $this->withborder, $this->continueline, $this->alignLeft);
								else
									$this->Cell($w, $h, number_format($data[$i],2,'.',','), $this->withborder, $this->continueline, $this->alignRight);
								//$this->Cell($this->ColumnWidth[$i], $this->rheight, $data[$i], 1, 0, 'L');
							}         */
							if($length <= $this->ColumnWidthProf[$i]){
								//$this->Cell($w, $h, $data[$i],1,0,'L');
								if($i < 2 || $i==5 || $i==6){
									#$this->Cell($w, $row * 2, $data[$i], $this->withborder, $this->continueline, $this->alignLeft);
									/*if($nb2 > $nb3){
										$this->Rect($x, $y, $w, $row * $nb2);
										$diff = $nb2 - $nb3;
									}else{
										$this->Rect($x, $y, $w, $row * $nb3);
										$diff = $nb3 - $nb2;
									}      */
//									if($nb2 > $nb3)
//										$mulcnt = $nb2;
//									else
//										$mulcnt = $nb3;

									$this->SetLineWidth(0.3);
									$this->Rect($x, $y, $w, ($row * $nb) + $row + 1);

									if($i == 0){
										$phys = $data[$i];
										$phys2 = explode(";",$phys);
										$phys_acc = str_split(trim($phys2[1]));
										if (trim($phys2[1]) == '') $phys_acc = str_split("            ");
										$phys_acc_len = count($phys_acc);
										#print_r($phys_acc);
										#echo "<br>";
										$temp = 0;
										for($cnt = 0; $cnt<$phys_acc_len; $cnt++){
											if($phys_acc[$cnt]!='-' && $temp!=4 && $temp!=12 ){
												$phys_arr[$temp] = $phys_acc[$cnt];
												$temp++;
											}else if($temp == 4 || $temp == 12){
												$phys_arr[$temp] = "-";
												if($phys_acc[$cnt]!='-'){
													$phys_arr[$temp+1] = $phys_acc[$cnt];
													$temp += 2;
												}else{
													$temp++;
												}
											}
										}

										$phys_arr_len = count($phys_arr);
										#echo "length = ".$phys_acc."<br>";
										#print_r($phys_arr);
//										$this->MultiCell($w, $row, strtoupper($phys2[0]), $this->withoutborder, $this->alignLeft);

										if (trim($phys2[0]) != '') {
											// Doctor's name ...
//											$this->MultiCell($w, $row, strtoupper($phys2[0]), "B", $this->alignLeft,false, 1,'','', false, 3);
											$this->Cell($w, $row, strtoupper($phys2[0]), "B", 1, $this->alignLeft, false, '', 1);

											if (strlen($phys2[0]) <= $pf_col0_max_chars)
												$this->SetY($this->GetY()+1);
											else
												$this->SetY($this->GetY()+0.8);
//												$this->SetY($this->GetY()+1);
										}
										else {
											// No doctor ...
											$this->MultiCell($w, $row, strtoupper($phys2[0]), $this->withoutborder, $this->alignLeft,false, 1,'','', false, 3);
										}

										$ysave = $this->GetY();
										$x1 = $this->GetX();
										#$ysave -= $row;
										$x1 = $x1 + 2;
										$this->SetX($x1);
//										print_r($phys_acc);
										$this->writeBlockNumber($x1, $ysave, $phys_arr_len, $phys_arr);
										#$this->Cell($w, $row, "__________");
									}else{
										if ($i == 1) {
											if ($data[$i] != '') {
                                                    //edited by jasper 02/26/2013
													//$tmpdta = explode("\n",$data[$i]);
                                                    $tmpdta = explode("**",$data[$i]);
                                                    //echo print_r($tmpdta);
													$x1 = $this->GetX();
													$yorig = $this->GetY();
                                                    //edited by jasper 02/26/2013
													//$this->MultiCell($w, $row * (($nb + 1) * 0.5), $tmpdta[0].(($this->coveredbypkg && $this->issurgical) ? "(P)" : ""), "B", $this->alignCenter,false, 1,'','', false, 3);
                                                    //$this->MultiCell($w, $row * (($nb + 1) * 0.5), $tmpdta[0].(($this->coveredbypkg && $this->issurgical) ? "(P)" : "")."\n ".$tmpdta[1], $this->withoutborder, $this->alignCenter,false, 1,'','', false, 3);
                                                    //$this->MultiCell($w, $row * (($nb+1) * 0.5), $tmpdta[0].(($this->coveredbypkg && $this->issurgical) ? "(P)" : ""), "B",$this->alignCenter,false, 1,'','',true, 3);
                                                    $this->MultiCell($w, $row * (($nb+1) * 0.5), $tmpdta[0], "B",$this->alignCenter,false, 1,'','',true, 3);
													$this->SetY($this->GetY());
													$this->SetX($x1);
                                                    $this->SetFont($this->fontfamily_label, "", 8); //added by jasper 03/11/2013
                                                    $this->MultiCell($w, $row * (($nb + 1) * 0.5), $tmpdta[1], $this->withoutborder,$this->alignCenter,false, 1,'','',true, 3);
													$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
													//$this->TextField('service_dates'.$this->servicedate_no,$w, $row*(($nb + 1)*0.75), array('value'=>$tmpdta[1]), array(),'','', false);
													$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);

//													TextField ($name, $w, $h, $prop=array(), $opt=array(), $x='', $y='', $js=false)

//													$this->Cell($w, $row * (($nb + 1) * 0.5), $tmpdta[1], "", 0, $this->alignCenter);
													$this->SetY($yorig);
													$adj = 1;
											}
											else
												$this->MultiCell($w, $row, $data[$i], $this->withoutborder, $this->alignLeft,false, 1,'','', false, 3);
										}
										else if ($i == 5) {
											$this->MultiCell($w, $row * (($nb + 1) * 0.5), $data[$i], "B", $this->alignCenter,false, 1,'','',true, 3);
										}
										else
											$this->MultiCell($w, $row, $data[$i], $this->withoutborder, $this->alignLeft,false, 1,'','',true, 3);
									}
								}
								else{
									$this->Cell($w, ($row * $nb) + $row + 1, (!$this->coveredbypkg) ? number_format($data[$i],2,'.',',') : " ", $this->withborder, $this->nextline, $this->alignRight, false, '', 1);
								}
								//$this->Cell($this->ColumnWidth[$i], $this->rheight, $data[$i], 1, 0, 'L');
							}
							else {
								// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								//$this->MultiCell($w, $row,$data[$i],1,'L');
								if($i < 2 || $i==5 || $i==6) {
//									if($nb2 > $nb3){
//										$this->Rect($x, $y, $w, $row * $nb2);
//										$mulcnt = $nb2;
//										$diff = $nb2 - $nb3;
//									}else{
//										$this->Rect($x, $y, $w, $row * $nb3);
//										$mulcnt = $nb3;
//										$diff = $nb3 - $nb2;
//									}

									$this->SetLineWidth(0.3);
									$this->Rect($x, $y, $w, ($row * $nb) + $row + 1);
									if($i == 0){
										$phys = $data[$i];
										$phys2 = explode(";",$phys);
										$phys_acc = str_split(trim($phys2[1]));
										if (trim($phys2[1]) == '') $phys_acc = str_split("            ");
										$phys_acc_len = count($phys_acc);
										#print_r($phys_acc);
										#echo "<br>";
										$temp = 0;
										for($cnt = 0; $cnt<$phys_acc_len; $cnt++){
											if($phys_acc[$cnt]!='-' && $temp!=4 && $temp!=12 ){
												$phys_arr[$temp] = $phys_acc[$cnt];
												$temp++;
											}else if($temp == 4 || $temp == 12){
												$phys_arr[$temp] = "-";
												if($phys_acc[$cnt]!='-'){
													$phys_arr[$temp+1] = $phys_acc[$cnt];
													$temp += 2;
												}else{
													$temp++;
												}
											}
										}

										$phys_arr_len = count($phys_arr);
										#echo "length = ".$phys_acc."<br>";
										#print_r($phys_arr);
										if (trim($phys2[0]) != '') {
//											$this->MultiCell($w, $row, strtoupper($phys2[0]), "B", $this->alignLeft,false, 1,'','',true, 3);

											$this->Cell($w, $row, strtoupper($phys2[0]), "B", 1, $this->alignLeft, false, '', 1);
											$this->SetY($this->GetY()+1);
										}
										else {
											$this->MultiCell($w, $row, strtoupper($phys2[0]), $this->withoutborder, $this->alignLeft,false, 1,'','',true, 3);
										}
										$ysave = $this->GetY();
										$x1 = $this->GetX();
										#$ysave -= $row;
										$x1 = $x1 + 2;
										$this->SetX($x1);
										#print_r($phys_acc);
										$this->writeBlockNumber($x1, $ysave, $phys_arr_len, $phys_arr);
										#$this->Cell($w, $row, "__________");
									}else{
										if ($i == 1) {
											if ($data[$i] != '') {
                                                    //edited by jasper 02/26/2013
													//$tmpdta = explode("\n",$data[$i]);
                                                    $tmpdta = explode("**",$data[$i]);
													$x1 = $this->GetX();
													$yorig = $this->GetY();
                                                    //edited by jasper 02/26/2013
													//$this->MultiCell($w, $row * (($nb + 1) * 0.5), $tmpdta[0], "B", $this->alignCenter,false, 1,'','',true, 3);
                                                    //$this->MultiCell($w, $row * (($nb + 1) * 0.5), $tmpdta[0]."\n ".$tmpdta[1], $this->withoutborder,$this->alignCenter,false, 1,'','',true, 3);
                                                    $this->MultiCell($w, $row * (($nb+1) * 0.5), $tmpdta[0], "B",$this->alignCenter,false, 1,'','',true, 3);
													$this->SetY($this->GetY());
													$this->SetX($x1);
                                                    //edited by jasper 02/28/2013
													//$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
                                                    $this->SetFont($this->fontfamily_label, "", 8); //added by jasper 03/11/2013
                                                    $this->MultiCell($w, $row * (($nb + 1) * 0.5), $tmpdta[1], $this->withoutborder,$this->alignCenter,false, 1,'','',true, 3);
													//$this->TextField('service_dates'.++$this->servicedate_no,$w, $row*(($nb + 1)*0.5), array('value'=>$tmpdta[1]), array(),'','',false);
													$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
//													$this->Cell($w, $row * (($nb + 1) * 0.5), $tmpdta[1], "", 0, $this->alignCenter);
													$this->SetY($yorig);
													$adj = 1;
											}
											else
												$this->MultiCell($w, $row, $data[$i], $this->withoutborder, $this->alignLeft,false, 1,'','',true, 3);
										}
										else if ($i == 5) {
											$this->MultiCell($w, $row * (($nb + 1) * 0.5), $data[$i], "B", $this->alignCenter,false, 1,'','',true, 3);
										}
										else
											$this->MultiCell($w, $row, $data[$i], $this->withoutborder, $this->alignLeft,false, 1,'','',true, 3);
									}
								}
								else{
									$this->Cell($w, ($row * $nb) + $row + 1, (!$this->coveredbypkg) ? number_format($data[$i],2,'.',',') : " ", $this->withborder, $this->nextline, $this->alignRight, false, '', 1);

//									$this->MultiCell($w, ($row * $nb) + $row + 0.9, (!$this->coveredbypkg) ? number_format($data[$i],2,'.',',') : " ", $this->withborder, $this->alignRight, false, 1, '', '', true, 3);
								}
							}

					//Put the position to the right of the cell
					//$this->SetXY($x+$w,$y);
					$this->SetXY($x+$this->ColumnWidthProf[$i],$y);
			}
			$this->SetLineWidth(0.2);

			//Go to the next line
			$this->Ln($h+$row*$adj+1);
		}

		function RowDoc2($data)
	 {
		$row = $this->rheight;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->ColumnWidthProf[$i],$data[$i]));
					$nb2=$this->NbLines($this->ColumnWidthProf[0],$data[0]);
					$nb3=$this->NbLines($this->ColumnWidthProf[1],$data[1]);
					$nb2 = $nb2 + 1;
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
			$h=$row*$nb;
			//Issue a page break first if needed
			//$this->CheckPageBreak($h);
            //removed and added by jasper 05/23/2013
            if ($this->CheckPageBreak($h)) {
                $this->addName();
            }
            //removed and added by jasper 05/23/2013
            //added by jasper 04/18/2013
            /*if ($this->GetY()==5 && $this->PageNo()>1) {
               $this->SetTopMargin(15);
            }*/
            //added by jasper 04/18/2013
			//Draw the cells of the row
			for($i=0;$i<count($data);$i++)
			{
					$w=$this->ColumnWidthProf[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
					//Save the current position

					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border
							$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontstyle_answer);
							$length = $this->GetStringWidth($data[$i]);

								if($i < 2 || $i==5 || $i==6){
									if($nb2 > $nb3)
										$mulcnt = $nb2;
									else
										$mulcnt = $nb3;

									$this->SetLineWidth(0.3);
									$this->Rect($x, $y, $w, $row * $mulcnt);

									if($i == 0){
										$phys = $data[$i];
										$phys2 = explode(";",$phys);
										$phys_acc = str_split(trim($phys2[1]));
										if (trim($phys2[1]) == '') $phys_acc = str_split("            ");
										$phys_acc_len = count($phys_acc);
										$temp = 0;
										for($cnt = 0; $cnt<$phys_acc_len; $cnt++){
											if($phys_acc[$cnt]!='-' && $temp!=4 && $temp!=12 ){
												$phys_arr[$temp] = $phys_acc[$cnt];
												$temp++;
											}else if($temp == 4 || $temp == 12){
												$phys_arr[$temp] = "-";
												if($phys_acc[$cnt]!='-'){
													$phys_arr[$temp+1] = $phys_acc[$cnt];
													$temp += 2;
												}else{
													$temp++;
												}
											}
										}

										$phys_arr_len = count($phys_arr);
										$this->MultiCell($w, $row, strtoupper($phys2[0]), $this->withoutborder, $this->alignLeft);
										$ysave = $this->GetY();
										$x1 = $this->GetX();
										$x1 = $x1 + 2;
										$this->SetX($x1);
										$this->writeBlockNumber($x1, $ysave, $phys_arr_len, $phys_arr);
									}else{
										$this->MultiCell($w, $row, $data[$i], $this->withoutborder, $this->alignLeft);
									}
								}else{
									$this->Cell($w, $row * $mulcnt, ($data[$i] != "") ? number_format($data[$i],2,'.',',') : " ", $this->withborder, $this->nextline, $this->alignRight);
								}

					//Put the position to the right of the cell
					$this->SetXY($x+$this->ColumnWidthProf[$i],$y);
			}
			$this->SetLineWidth(0.2);

			//Go to the next line
			$this->Ln($h+$row*1);
		}

//		function CheckPageBreak($h) {
				//If the height h would cause an overflow, add a new page immediately
//				if($this->GetY()+$h>$this->PageBreakTrigger){
//					$this->AddPage($this->CurOrientation);
//					if ($this->PageNo() > 1) {
//						$this->addName();
//						$x = $this->GetX();
//						$y = $this->GetY();
//						$this->Line($x, $y, $x+$this->totwidth, $y);
//					}
//					return true;
//				}else{
//					return false;
//				}
//		}

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

class Form2Meds {
	var $gen_name;
	var $brand;
	var $preparation;
	var $qty;
	var $unit_price;
	var $actual_charges;
	var $claim_hospital;
	var $claim_patient;
}

class Laboratories{
	var $particulars;
	var $qty;
	var $unit_price;
	var $actual_charges;
	var $claim_hospital;
	var $claim_patient;
}

class Confinement{
	var $admit_dt;
	var $discharge_dt;
	var $death_dt;
	var $claim_days;
	var $admit_tm;
	var $discharge_tm;
}

class Diagnosis{
var $fin_diagnosis;
var $code;
var $case_type;
}

class HospServices{
	var $charges;
	var $claim_hospital;
	var $claim_patient;
	var $reduction;
}

class HealthPersonnel{
		var $name;
		var $accnum;
		var $bir_tin_num;
		var $servperformance;
		var $profcharges;
		var $claim_physician;
		var $claim_patient;
		var $inclusive_dates;
		var $role_area;
}

class Surgeon{
		var $name;
		var $accnum;
		var $bir_tin_num;
		var $servperformance;
		var $profcharges;
		var $claim_physician;
		var $claim_patient;
		var $operation_dt;
}

header("Content-type: text/html; charset=utf-8");

$pdf = new PhilhealthForm2();
include_once($root_path.'modules/billing/billing-gendata-phic-cf2.php');
$pdf->Open();
//$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->addHeader();
$pdf->addPart1();
//added by jasper 04/18/2013
if ($pdf->GetY()>244) {
    $pdf->AddPage();
}
//added by jasper 04/18/2013
$pdf->addName();
$pdf->addPart2();
$pdf->addPart3();
$pdf->addPart4();
$pdf->addPart5();

//removed by jasper 03/24/2013
//Format text fields then print ...
/*$pdf->script.="
for (i=1;i<=".$pdf->servicedate_no.";i++) {
	f=getField('service_dates'+i);
	f.alignment = 'center';
	f.strokeColor = color.transparent;
	f.fillColor = color.transparent;
	f.highlight = highlight.n;
	f.textSize = 8;
}
";
$pdf->IncludeJS($pdf->script); */
$pdf->Output();
?>