<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'/classes/tcpdf/config/lang/eng.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/classes/tcpdf/tcpdf.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
include_once($root_path.'include/care_api_classes/class_hospital_admin.php');
include_once($root_path.'include/care_api_classes/class_insurance.php');
include_once($root_path.'include/care_api_classes/billing/class_billing_new.php');
include_once($root_path.'include/care_api_classes/class_person.php');
include_once($root_path.'classes/tcpdf/barcodes.php');

define('INFO_INDENT', 10);
define('NEWBORN', '99432');
define('OUT_PATIENT',2);


class PhilhealthForm2 extends TCPDF {

	var $fontsize_label = 6.5;
	var $fontsize_label2 = 16;
	var $fontsize_label3 = 8;
	var $fontsize_label4 = 6;
	var $fontsize_label5 = 6.5;
	var $fontsize_answer = 10;
	var $fontsize_answer2 = 12;
	var $fontsize_answer_check = 12;
	var $fontsize_answer_check2 = 10;
	var $fontsize_answer_cert = 8.5;
	var $fontsize_answer_table = 9;

	var $fontstyle_label_bold = "";
	var $fontstyle_label_bold_italicized = "BI";
	var $fontstyle_label_italicized = "I";
	var $fontstyle_label_normal = '';
	var $fontstyle_answer = "B";

	var $fontfamily_label = "tahoma";
	var $fontfamily_answer = "freeserif";
	var $fontfamily_times = "times";
	var $fontfamily_sans = "dejavusans";

	var $totwidth = 200;
	var $rheight = 5;
	var $rheight2 = 2;
	var $rheight3 = 3;
	var $rheight4 = 4;
	var $rheight6 = 6;
	var $rheight7 = 7;

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

	var $encounter_nr = '';
	var $encounter_type = 0;
	var $hcare_id = 0;
	var $name_first = '';	// patient's first name
	var $name_suffix = ''; 	// patient's suffix
	var $name_middle = ''; 	// patient's middle name
	var $name_last = '';   	// patient's last name
	
	var $final_diagnosis;
	var $room_type;
	var $auth_rep;            // Authorized representative of hospital.
	var $rep_capacity;        // Official capacity of authorized representative.


	var $hospaccnum = 0;      // Accreditation No. of Hospital (part1)
	var $hosp_name = '';		  // Name of hospital (part1)
	var $hosp_address = '';	  // Address of hospital (part1)
	var $is_refer_by_another_hci = 0; //3. Was patient referred by another Health Care Institution (HCI)?
	var $another_hci_name = '';
	var $another_hci_address = '';
	var $phil_health_id_num     = ''; //patient's philhealth id (member or if dependent)
	var $is_principal = 0;			//patient if principal holder of philhealt 
	var $confinement_array;   // Array of class Confinement.
	var $date_admitted = '';  		//confinement date admitted
	var $date_discharged = ''; 		//confinement date discharged
	var $time_admitted = '';		//confinement time admitted
	var $time_admitted_ampm = '';		//confinement time admitted
	var $time_discharged_ampm = '';		//confinement time admitted

	var $time_discharged = '';		//confinement time discharged
	var $is_improved = false;		//5. patient disposition
	var $is_expired = false;		//5. patient disposition
	var $date_expired = ''; 		//5. patient disposition
	var $time_expired = '';			//5. patient disposition
	var $is_recovered = false;		//5. patient disposition
	var $is_transferred = false;	//5. patient disposition
	var $trans_hci_name = '';		//5. patient disposition
	var $trans_hci_address = '';	//5. patient disposition
	var $trans_reasons = '';		//5. patient dispositionreasons fo transfer or referral
	var $is_discharge = false;
	var $is_absconded = false;
	var $admission_diagnosis = '';
	var $diagnosis_array;     // Array of class Diagnosis.
	var $rvs_array; 
	var $professional_fee_array;
	
	var $hospserv_array;      // Array of class HospServices.
	
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

    var $type_private='';
 	var	$type_nonprivate='';

 	var $disp_code;
 	var $result_code;


 	//added by Nick, 2/17/2014
 	var $icd_arr_desc,$icd_arr_code;
 	var $rvs_arr_desc,$rvs_arr_code,$rvs_arr_date,$rvs_arr_late;
    var $rvs_arr_specials;
    var $isNewBorn;

	function PhilhealthForm2(){

		$pg_array = array('215.9','330.2');
		$this->__construct('P', 'mm', $pg_array, true, 'UTF-8', false);
		$this->SetDrawColor(0,0,0);
        $this->SetMargins(8,8,1);
		$this->SetAutoPageBreak(true, 1);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->setLanguageArray($l);
	}
	function pageBoarder(){

		$x = $this->GetX();
		$y = $this->GetY()+3;
		// $this->SetXY($x,$y);
		$this->Rect($x-1, 5, $this->totwidth+1, 1, 'DF', "",  array(FFF, FFF, FFF));	//top
		$this->Rect($x-1, $this->totwidth+123, $this->totwidth+1, 1, 'DF', "",  array(FFF, FFF, FFF));	//bot
		$this->Rect($x-1, 5, 1, $this->totwidth+119, 'DF', "",  array(FFF, FFF, FFF));	//left
		$this->Rect(($x*20)+10, 5, 1, $this->totwidth+119, 'DF', "",  array(FFF, FFF, FFF));	//right
	}

	

	function addHeader()
	{
		$series_number = "";

		global $root_path, $db;

		//labels
		$republic = "Republic of the Philippines";
		$Philippine = "PHILIPPINE HEALTH INSURANCE CORPORATION";
		$Citystate = "Citystate Centre 709 Shaw Boulevard, Pasig City";
		$Call = "Call Center (02) 441-7442 ".chr(127)." Trunkline (02) 441-7444";
		$domain = "www.philhealth.gov.ph";
		$email = "email: actioncenter@philhealth.gov.ph";
		$form = "This form may be reproduced and";
		$notForSale = "is NOT FOR SALE";
		$philhealth = "PHILHEALTH";
		$cf2 = "CF-2";
		$partner = "Your Partner in Health";
		$claimform = "(Claim Form 2)";
		$revised = "Revised September 2018";
		$series = "Series #";
		$reminders = "IMPORTANT REMINDERS:";
		$rule_part1 = "PLEASE WRITE IN CAPITAL";
		$rule_letters = "LETTERS";
		$rule_and = "AND";
		$rule_check = "CHECK";
		$rule_part2 = "THE APPROPRIATE BOXES.";
		$reminder_line1_part1 = "This form together with other supporting documents should be filed within sixty (60) calendar days from date of discharge.";
		$reminder_line2 = "All information, fields and tick boxes required in this form are necessary. Claim forms with incomplete information shall not be processed.";
		$reminder_line3 = "FALSE / INCORRECT INFORMATION OR MISREPRESENTATION SHALL BE SUBJECT TO CRIMINAL, CIVIL OR ADMINISTRATIVE LIABILITIES.";
		$series_width = 53;   //width for series number
		// $series_number = 13;


		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(65, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);


		$this->SetFont($this->fontfamily_sans, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight, $form, $this->withoutborder,$this->nextline,$this->alignCenter);


		$width = $this->GetStringWidth($philhealth);
		$this->Image('../../images/phic_logo.png', $this->lMargin+3, $this->tMargin+5, $width+$this->rheight*6 , $this->rheight*2.5);
		$y = $this->GetY()-$this->rheight*4;
		$this->Cell(52, $y, "", $this->withoutborder, $this->continueline, $this->alignLeft);


		$this->SetFont($this->fontfamily_times, $this->fontstyle_answer, 10);
		$this->Cell(100, $this->rheight-3, $Philippine, $this->withoutborder, $this->continueline, $this->alignLeft);

		$this->SetFont($this->fontfamily_sans, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell(0, $this->rheight-70, $notForSale, $this->withoutborder,$this->nextline,$this->alignLeft);

		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, 24);
		$width = $this->totwidth - ($width + $this->space);
		$this->Cell(($width-$this->rheight2*9.5)+12, $y, $cf2, $this->withoutborder, $this->nextline, $this->alignRight);


		$this->SetXY(89, 10);
		$this->SetFont($this->fontfamily_times, $this->fontstyle_label_italicized, $this->fontsize_label);
		$this->Cell(0, 0, $republic, $this->withoutborder, $this->continueline, $this->alignLeft);
	
		$this->SetXY(74, 17);
		$this->SetFont($this->fontfamily_times, $this->fontstyle_label_normal, 8);
		$this->Cell(0, 0, $Citystate, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetXY(70, 20);
		$this->SetFont($this->fontfamily_times, $this->fontstyle_label_normal, 8);
		$this->Cell(0, 0, $Call, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetXY(87, 23);
		$this->SetFont($this->fontfamily_times, $this->fontstyle_label_normal, 8);
		$this->Cell(0, 0, $domain, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetXY(79, 26);
		$this->SetFont($this->fontfamily_times, $this->fontstyle_label_normal, 8);
		$this->Cell(0, 0, $email, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetXY(10, 26);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight);

		$width = $this->GetStringWidth($partner) + $this->space;
		$this->Cell($width-5, $this->rheight, "", $this->withoutborder, $this->continueline, $this->alignLeft);

		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($this->totwidth-($width+$this->rheight2*12.75), $this->inspace, $claimform, $this->withoutborder, $this->nextline, $this->alignRight);
		$this->Cell(($this->totwidth-$this->rheight2*7)-3, $this->inspace, $revised, $this->withoutborder, $this->nextline, $this->alignRight);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3);


//daryl

if ($series_number == "")
		{
			$series_number = '            ';

		}
		else{
		$series_number =  $series_number;
	}



		$xsave = $this->GetX();

		$ysave = $this->GetY();
		// $series_no = str_split($series_number);
		$series_no = $this->Rect($x-13, $y+7, $this->boxwidth+1, $this->boxheight+1);
		$series_len = strlen($series_number);
			$width = $this->GetStringWidth($series);
		$width = $this->totwidth -50 - ($width + $this->space);
		# Edited by James 1/30/2014
		// $length = $this->GetStringWidth($series_no);


	$this->Cell($width-2, $this->blockheight, $series, $this->withouborder, $this->continueline, $this->alignRight);
$this-> setXY($xsave+140, $ysave);
		for($cnt = 0; $cnt < $series_len; $cnt++){
			if($cnt == 0 || $cnt > 0){

					$this->writeBlock($xsave+135, $ysave, 13);
					$this->Cell($this->blockwidth, $this->blockheight, $series_no[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}


		}
					$this->Ln();
		//end

    	$x = $this->GetX();
		$y = $this->GetY()+3;
		// $this->SetXY($x,$y);
		// $this->Rect($x, 5, $this->totwidth, 1, 'DF', "",  array(FFF, FFF, FFF));
		$this->Rect($x, $y, $this->totwidth, 0.5, 'DF', "",  array(FFF, FFF, FFF));
		// $this->Line($x, $y+5, $x+$this->totwidth+3, $y+5);
		// $this->Line($x, $y+0.5, $x+$this->totwidth, $y+0.5);
		$this->Ln();
		$this->Cell($this->GetX(), $this->rheight, $reminders, $this->withoutborder, $this->continueline, $this->alignLeft);
		$width = $this->totwidth - $series_width;
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		// $this->Cell($width-$this->rheight4*3, $this->rheight, $series, $this->withoutborder, $this->continueline, $this->alignRight);
		// $x = $this->GetX()+$this->rheight2;
		// $y = $this->GetY();
		// $this->writeBlock($x, $y, $series_number);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$ser_width = $this->GetStringWidth($series);
		$width = $this->totwidth - ($ser_width + $series_width);
		$this->Ln();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_part1)+$this->inspace,$this->rheight3,$rule_part1, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_letters)+$this->inspace, $this->rheight3, $rule_letters, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_and)+$this->inspace, $this->rheight3, $rule_and, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_check)+$this->inspace, $this->rheight3, $rule_check, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rule_part2), $this->rheight3, $rule_part2, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontsyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($reminder_line1_part1)+$this->inspace, $this->rheight3, $reminder_line1_part1, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Cell($this->totwidth, $this->rheight3, $reminder_line2, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->totwidth, $this->rheight3, $reminder_line3, $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->Ln();
	}

	function writeBlockNumber($xcoord, $ycoord, $num, $insurance){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord-$this->lineAdjustment; //y-coordinate of block
		$number = $num;
		$len = $this->blockwidth * $number;

		$y1 = $y+4;
		$y2 = $y + $this->blockheight/2;
		$x1 = $x;
		$x2 = $x;
		$this->SetLineWidth(0.3);
		$this->SetX($x,$y);
		if($number < 14){
			$new_number = 14;
			for($cnt = 0; $cnt<$new_number; $cnt++){
				$this->SetLineWidth(0.3);
				if($cnt!=2 && $cnt!=12){
					$this->Line($x1, $y1+$this->lineAdjustment, $x1, $y2+$this->lineAdjustment);
					$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$this->blockwidth, $y+$this->blockheight+$this->lineAdjustment);
					$x2 = $x1 + $this->blockwidth;
					$this->Line($x2, $y1+$this->lineAdjustment, $x2, $y2+$this->lineAdjustment);
					$this->Cell($this->blockwidth, $this->blockheight+2, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				}else{
					#echo "for - "."<br>";
					$this->Line($x+$this->inspace*1, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));

					$this->Cell($this->blockwidth, $this->blockheight+2, "", $this->withoutborder, $this->continueline, $this->alignCenter);
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
					$this->Cell($this->blockwidth, $this->blockheight+2, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				}else{
					#$x = $this->GetX();
					#$y = $this->GetY();
					$this->Line($x+$this->inspace*2, $y+($this->blockheight/2), $x+$this->inspace*3, $y+($this->blockheight/2));

					$this->Cell($this->blockwidth, $this->blockheight+2, "", $this->withoutborder, $this->continueline, $this->alignCenter);
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
		$y2 = $y + $this->blockheight;
		$y3 = $y + $this->blockheight/1.5;
		for($cnt = 0; $cnt<=$number; $cnt++){
			if($type == 1){
				$this->Line($x, $y2+$this->lineAdjustment, $x, $y3);
				$x += $this->blockwidth;
			}else if($type == 2){
				$this->Line($x, $y2+$this->lineAdjustment, $x, $y3);
				$x += $this->blockwidth;
			}

		}
		$this->SetLineWidth(0.2);
	}



	function writeBlock($xcoord, $ycoord, $num){
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;
		
		$this->SetLineWidth(0.3);

		$len = $this->blockwidth * $number;
		// $this->Line($x, $y+$this->blockheight+$this->lineAdjustment-10, $x+$len, $y+$this->blockheight+$this->lineAdjustment);
		$this->Line($x, $y+$this->blockheight+$this->lineAdjustment, $x+$len, $y+$this->blockheight+$this->lineAdjustment);
		// var_dump($len);die;
		$y2 = $y + $this->blockheight;
		$y3 = $y + $this->blockheight/1.5;
		for($cnt = 0; $cnt<=$number; $cnt++){
			$this->Line($x, $y2+$this->lineAdjustment, $x, $y3);
			$x += $this->blockwidth;
		}
		$this->SetLineWidth(0.2);
		
	}

	function writeBlockTime($time, $atime){
		$len = strlen($time);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($i=0; $i<$len; $i++){
			if($i==0 || $i==3){
				$x = $this->GetX();
				$y = $this->GetY();
				$this->writeBlock($x, $y, 2);
			}
			$this->Cell($this->blockwidth, $this->blockheight+1, substr($time, $i, 1), $this->withoutborder, $this->continueline, $this->alignRight);
		}
		$x = $this->GetX()+$this->rheight3;
		$y = $this->GetY()+$this->rheight2;
		// $this->Rect($x,$y,$this->rheight2);
		$this->Rect($x-2, $y-2, $this->boxwidth+1, $this->boxheight+1);

		$x1 = $this->GetX()+$this->rheight2;
		$this->SetX($x1);
		$this->Cell($this->rheight2, $this->blockheight+1, $atime=='AM' ? '/' : ' ', $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetX($x);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->rheight*2, $this->blockheight, 'AM', $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$x = $this->GetX()+$thius->rheight7;
		// $this->Rect($x,$y,$this->rheight2);
		$this->Rect($x-2, $y-2, $this->boxwidth+1, $this->boxheight+1);

		$this->Cell($this->lineAdjustment, $this->blockheight+1, $atime=='PM' ? '/' : ' ', $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->rheight4*2, $this->blockheight, 'PM', $this->withoutborder, $this->continueline, $this->alignRight);
 	}

	function addBlockDate($admit_date){
		$admit_date_arr = str_split($admit_date);
        $admit_date_len = strlen($admit_date);

		$x = $this->GetX();
		$y = $this->GetY();


		 for($cnt = 0; $cnt<$admit_date_len; $cnt++){
		 	$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
			if($cnt < 4){
				if($cnt == 0 || $cnt == 2){
					$this->writeBlockDate($x, $y, 2, 1);
					$this->Cell($this->blockwidth, $this->blockheight+1, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				}else{
					$this->Cell($this->blockwidth, $this->blockheight+1, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
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
				$this->Cell($this->blockwidth, $this->blockheight+1, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
 			}
 		}
 	}

 	function addAccrediationNo($text){
		$text_len = strlen($text);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for($i=0; $i<$text_len;$i++){
			if($text[$i]!='-'){
				$x = $this->GetX();
				$y = $this->GetY();
				$this->writeBlock($x, $y, 1);
			}
			$this->Cell($this->blockwidth, $this->blockheight+1, $text[$i], $this->withoutborder, $this->continueline, $this->alignCenter);
		}
 	}

	function checkdependence(){
				global $db;
				$sql = "SELECT d.parent_pid AS parent
									FROM care_encounter e
									LEFT JOIN seg_dependents d
									ON (d.dependent_pid = e.pid)
									WHERE e.encounter_nr = ".$db->qstr($this->encounter_nr);
				if ($result = $db->Execute($sql)) {
						if ($row = $result->FetchRow())
								return $row['parent'];
				}
				return false;
	}

	function getPrincipalNm($pid) {
		global $db;

		$strSQL = "SELECT i.insurance_nr AS IdNum
					FROM care_person_insurance AS i
					WHERE i.hcare_id = $this->hcare_id AND i.is_principal = 1 AND p.pid = '$pid'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			}
		}

		return false;
	}

	function checkDeathInfo(){
		global $db;

		$sql = "SELECT cp.`death_date` AS deathdate, cp.`death_time` AS deathtime
					FROM seg_insurance_member_info AS sim
						LEFT JOIN care_person AS cp ON cp.`pid` = sim.`pid`
						INNER JOIN care_encounter AS ce ON ce.`pid` = sim.`pid`
					WHERE ce.encounter_nr = ".$db->qstr($this->encounter_nr);


					$death_result = $db->Execute($sql);

					return $death_result;

	}

	function getRVSCode(){
		global $db;

		$sql = "SELECT smod.ops_code , scrp.description, smod.laterality, smod.op_date
					FROM seg_insurance_member_info AS sim
						INNER JOIN care_encounter AS ce ON ce.pid = sim.pid
						INNER JOIN seg_misc_ops AS smo ON smo.encounter_nr = ce.encounter_nr
						INNER JOIN seg_misc_ops_details AS smod ON smod.refno = smo.refno
						INNER JOIN seg_case_rate_packages AS scrp ON smod.ops_code = scrp.code
					WHERE ce.encounter_nr = ".$db->qstr($this->encounter_nr);

					$rvs_result = $db->Execute($sql);

					return $rvs_result;
	}
	# End James


	function get_encounter_data(){
				global $db;

				$sql_1 = "SELECT e.admission_dt AS admition_date, e.discharge_date AS dis_date, e.discharge_time as dis_time
						FROM care_person AS cp
						INNER JOIN care_encounter AS e ON cp.pid = e.pid
						LEFT JOIN seg_insurance_member_info AS d ON d.pid = cp.pid
						WHERE d.hcare_id = '$this->hcare_id'  AND e.encounter_nr = ".$db->qstr($this->encounter_nr);

					$result3 = $db->Execute($sql_1);

					return $result3;
			}

	function getDischargeDiag() {
			global $db;

			$strSQL = "SELECT ci.`description` AS dis_desc, ce.`code` AS dis_code
			 FROM seg_encounter_diagnosis AS ce
			 LEFT JOIN  care_icd10_en AS ci ON ce.`code` = ci.`diagnosis_code`
			 LEFT JOIN care_encounter AS i ON ce.`encounter_nr` = i.`encounter_nr`
				WHERE ce.encounter_nr = ".$db->qstr($this->encounter_nr);


					$result4 = $db->Execute($strSQL);

					return $result4;
		}

	function getdisposition()
	{
		global $db;
		$strSQL="SELECT ser.`result_code` , sed.`disp_code`
				FROM  seg_encounter_disposition_refer AS sed
				INNER JOIN seg_encounter_result as ser
					ON ser.encounter_nr = sed.encounter_nr
				WHERE sed.encounter_nr = ".$db->qstr($this->encounter_nr);

		if($result = $db->Execute($strSQL)){
			if($result->RecordCount()){
				while ($row = $result->FetchRow()) {
					$this->disp_code = $row['disp_code'];
					$this->result_code =  $row['result_code'];
				}
			}else {return false;}
		}else {return false;}

	}

	function check_is_death()
	{
		global $db;
		$strSQL="SELECT cp.`death_date` AS deathdate, cp.`death_time` AS deathtime
		 		FROM care_encounter AS ce
		 		INNER JOIN care_person AS cp ON cp.`pid` = ce.`pid`
				WHERE ce.encounter_nr = ".$db->qstr($this->encounter_nr);

		if($result = $db->Execute($strSQL)){
			if($result->RecordCount()){
				return $result;
			}else {return false;}
		}else {return false;}

	}

	function get_doctor_info()
	{
		global $db;
		$strSQL="SELECT sbe.`bill_dte`, fn_get_personell_lastname_first2(sbp.`dr_nr`) as doc_name, sbp.`dr_charge`, sbp.`dr_claim`,
				(SELECT accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = sbp.dr_nr and sda.hcare_id = '$this->hcare_id') as acc_no
				FROM seg_billing_encounter AS sbe
				INNER JOIN seg_billing_pf AS sbp ON sbe.`bill_nr` = sbp.`bill_nr`
				WHERE sbe.is_final = '1' AND sbe.is_deleted IS NULL
				AND sbe.`encounter_nr` = ".$db->qstr($this->encounter_nr);
		//echo($strSQL);
		if($result = $db->Execute($strSQL)){
			if($result->RecordCount()){
				return $result;
			}else {return false;}
		}else {return false;}
	}



	function get_doctor_info2()
	{
		$data = array();
			$index = 0;

			$this->objBill->getProfFeesList();
			$this->objBill->getProfFeesBenefits();
			$hsp_pfs_benefits = $this->objBill->getPFBenefits();
			$proffees_list = $this->objBill->proffees_list;

			foreach($hsp_pfs_benefits as $key=> $value) {
				if ($value->role_area == $prevrole_area) continue;
				$prevrole_area = $value->role_area;
				reset($proffees_list);
				$this->objBill->initProfFeesCoverage($value->role_area);
				$totalCharge = number_format($this->objBill->getTotalPFCharge($value->role_area), 2);
				$coverage    = number_format($this->objBill->pfs_confine_coverage[$value->role_area], 2, '.', ',');
				$tr ='';
				foreach($proffees_list as $key=>$profValue){
					if($value->role_area == $profValue->role_area) {
						$opcodes = $profValue->getOpCodes();
						if ($opcodes != '') {
							$opcodes = explode(";", $opcodes);
						}
						if (is_array($opcodes)) {
							foreach($opcodes as $v) {
								$i = strpos($v, '-');
								if (!($i === false)) {
									$code = substr($v, 0, $i);
		                              if ($this->objBill->getIsCoveredByPkg()) break;
								}#if
							}#foreach
						}#if

						$drName = $profValue->dr_first." ".$profValue->dr_mid.(substr($profValue->dr_mid, strlen($profValue->dr_mid)-1,1) == '.' ? " " : ". ").$profValue->dr_last;
						$drCharge = number_format($profValue->dr_charge, 2, '.', ',');
						$totalPF += $profValue->dr_charge;

						$data[$index] = array("dr_charge"=>$profValue->dr_charge,
							                  "role_area"=>$value->role_area,
									          "role_desc"=>$value->role_desc,
									          "total_charge"=>$this->objBill->getTotalPFCharge($value->role_area),
									          "coverage"=>number_format($this->objBill->pfs_confine_coverage[$value->role_area], 2, '.', ','),
									          "drName"=>$drName,
									          "dr_nr" => $profValue->dr_nr
								  	         );
						$index++;
					}#if
				}#foreach
			}#foreach
	}

	function getCaseRate($type){
		global $db;
		$strSQL = "SELECT package_id
					FROM seg_billing_encounter sbe
					INNER JOIN seg_billing_caserate sbc
						ON sbe.bill_nr=sbc.bill_nr
					WHERE sbe.is_final = 1
					AND sbe.is_deleted IS NULL
					AND sbc.rate_type = ".$db->qstr($type)."
					AND encounter_nr = ".$db->qstr($this->encounter_nr);

		if($result = $db->Execute($strSQL)){
			if($result->RecordCount()){
				while ($row = $result->FetchRow()) {
					return $row['package_id'];
				}
			}else {return false;}
		}else {return false;}
	}



	function isCharity()
	{
		global $db;
		$strSQL= $db->Prepare("SELECT accommodation_type
				  FROM seg_billing_encounter
				  WHERE is_deleted IS NULL
				  AND is_final = '1'
				  AND encounter_nr = ? ");

		if($result = $db->Execute($strSQL,$this->encounter_nr)){
			if($result->RecordCount()){
				while ($row = $result->FetchRow()) {
					if($row['accommodation_type']=='1')
						return true;
					else{
						return false;
					}
				}
			}else {return false;}
		}else {return false;}

	}

//function added by Sarah Sept. 7, 2015
	function isNewBornBaby(){
		global $db;
		$enc = $_GET['encounter_nr'];
		$strSQL = "SELECT pid FROM care_encounter WHERE encounter_nr = ".$enc;
		if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
				while ($row = $result->FetchRow()){
				    $pid = $row['pid'];
				}
			}
        }
		$strSQL = $db->Prepare("SELECT birth_time FROM care_person WHERE pid = ".$pid);
		if($result = $db->Execute($strSQL)){
			if($result->RecordCount()){
				while($row = $result->FetchRow()){
					if($row['birth_time'] != "00:00:00"){
						return true;
					}
				}
			}
			else{
				return false;
			}

		}
		else{
			return false;
		}
	}
//function added by Sarah Sept. 7, 2015 end

	function getTotalAppliedDiscounts(){
        global $db;

        $sql = "SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount
                WHERE encounter_nr = ".$db->qstr($this->encounter_nr);

        $rs = $db->Execute($sql);
             if($rs){
            if($rs->RecordCount()>0){
                $row = $rs->FetchRow();
                return $row['total_discount'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //updated by Nick, 4/10/2014 - opd conditions
    function setAllData($data){
        extract($data);

        #added by Nick 2/19/2014
        $this->encounter_nr = $encounter_nr;
        $this->icd_arr_desc = $icd_desc;
        $this->icd_arr_code = $icd_code;
        $this->rvs_arr_desc = $rvs_desc;
        $this->rvs_arr_code = $rvs_code;
        $this->rvs_arr_date = $rvs_date;
        $this->rvs_arr_late = $rvs_late;
        $this->rvs_arr_specials = $spdates;

        /*PART I - HEALTH CARE INSTITUTION (HCI) INFORMATION */
        $objInfo = new Hospital_Admin();
        if ($row = $objInfo->getAllHospitalInfo()) {
            $this->hosp_name = strtoupper($row['hosp_name']);
            $this->hosp_address = strtoupper($row['hosp_addr1']);
        } else {
            $this->hosp_name = strtoupper("Southern Philippines Medical Center");
            $this->hosp_address = strtoupper("JICA Bldg. J.P. Laurel Bajada, Davao City, Davao Del Sur, 8000");
        }

        $enc_obj = new Encounter;
        $encInfo = $enc_obj->getEncounterInfo($this->encounter_nr);
        $aux_note = $enc_obj->getAuxNote($this->encounter_nr);

        //added by genz
        $erEncInfo = $enc_obj->getErEncounterDate($this->encounter_nr);
        $objPerson = new Person;
   		//$pid = $objPerson->getPid($_GET['encounter_nr']);
   		$refno = $objPerson->getDialysisTransIdDone($_GET['encounter_nr']);
        $this->encounter_type = $encInfo['encounter_type'];
        //ended by genz

        //echo  $this->encounter_type;
        //added by Nick, 05-06-2914
        $objBilling = new Billing;
        $deathdate = $objBilling->getDeathDate2($this->encounter_nr, $this->encounter_nr);

        $fname_patient = strtoupper($encInfo['name_first']);
        $mname_patient = strtoupper($encInfo['name_middle']);
        $lname_patient = strtoupper($encInfo['name_last']);
        $suffix_patient = strtoupper($encInfo['suffix']);
        $admission_diag_ = ($this->encounter_type == 2) ? $encInfo['chief_complaint'] : $encInfo['er_opd_diagnosis'];
        $isDischarge_ = $encInfo['is_discharged'];
        $birth_patient = $encInfo['date_birth'];
        $dis_dates_ = ($deathdate) ? $deathdate : $encInfo['discharge_dt']; //updated by Nick, 3/26/2014 - from discharged_dt to mgh_setdte
        
        $admits_date_ = ($this->encounter_type == 2) ? $encInfo['er_opd_datetime'] : $encInfo['admission_dt'];

        //added by genz
        if($refno){
	    	$admissionDtDialysis = $objPerson->getEcounterAdmissionDtDialysis($refno);
	    	$admits_date_ = ($this->encounter_type == 1) ? $admissionDtDialysis : $encInfo['admission_dt'];
			
			$dischargedt = $objPerson->getEcounterDischargeDtDialysis($refno);
			$dis_dates_ = ($dischargedt) ? $dischargedt : $encInfo['admission_dt'];

	    	// Modified by EJ 11/06/2014
	    	// $admissionDate = $objPerson->getAdmissionDate($pid);
	    	// $admits_date_ = ($this->encounter_type == 1) ? $admissionDate : $encInfo['admission_dt'];

	    }else{
	    	$admits_date_ = ($this->encounter_type == 1) ? $erEncInfo['encounter_date'] : $encInfo['admission_dt'];
	    }
	    //ended by genz

        $this->admission_diagnosis = $admission_diag_;
        $this->name_last = mb_convert_encoding(strtoupper(trim($lname_patient)), 'UTF-8');
        $this->name_first = mb_convert_encoding(strtoupper(trim($fname_patient)), 'UTF-8');
        $this->name_suffix = mb_convert_encoding(strtoupper(trim($suffix_patient)), 'UTF-8');
        $this->name_middle = mb_convert_encoding(strtoupper(trim($mname_patient)), 'UTF-8');

        if ($isDischarge_ == 1) {
            $this->date_discharged = date("mdY", strtotime($dis_dates_));
            $this->time_discharged = date("h:i:s", strtotime($dis_dates_));
            $this->time_discharged_ampm = date("A", strtotime($dis_dates_));
        } else {
            $this->date_discharged = "        ";
            $this->time_discharged = "     ";
            $this->time_discharged_ampm = "";
        }

        if ($admits_date_ != "") {
            $this->date_admitted = date("mdY", strtotime($admits_date_));
            $this->time_admitted = date("h:i:s", strtotime($admits_date_));
            $this->time_admitted_ampm = date("A", strtotime($admits_date_));
        } else {
            $this->date_admitted = "        ";
            $this->time_admitted = "     ";
            $this->time_admitted_ampm = "";
        }

    }

    //end function setAllData

	function writeChars($string){
		$chars = str_split($string);
		foreach ($chars as $key => $char) {
			if((ord($char) >= 65 && ord($char) <= 90) || (ord($char) >= 97 && ord($char) <= 122) || (ord($char) == 195)){
				$ascii = ( ord($char) == 195 ) ? 241 : ord($char);
				// $this->Cell(3,4,$this->unichr($ascii));
				str_replace($char, $this->unichr($ascii), $string);
			}else{
				str_replace($char, '', $string);
			}
		}

		return $string;
	}

	function addPart1and2(){

		// /*added by Genz*/
		// $encounter_obj=new Encounter($this->encounter_nr);
		// $patient_enc_disp_refer = $encounter_obj->getPatientEncounterDispRefer($this->encounter_nr);
		
		// if($patient_enc_disp_refer['disp_code'] == 8){
		// 	$is_refer_by_another_hci = 1;
		// 	$another_hci_name = strtoupper($patient_enc_disp_refer['hosp_name']);
		// 	$another_hci_address = strtoupper($patient_enc_disp_refer['hosp_add']);
		// }
		// /*end by Genz*/

		/*added by Genz*/
		$encounter_obj=new Encounter($this->encounter_nr);
		$patient_enc_disp_refer = $encounter_obj->getPatientEncounterDisp($this->encounter_nr);
		
		if($patient_enc_disp_refer['disp_code'] == 3){
			$is_refer_by_another_hci = 1;
			$another_hci_name = strtoupper($patient_enc_disp_refer['hosp_name']);
			$another_hci_address = strtoupper($patient_enc_disp_refer['hosp_add']);
		}
		/*end by Genz*/



		//labels for part 1
		$part1 = "PART I - HEALTH CARE INSTITUTION (HCI) INFORMATION";
		$acc_no = "1. PhilHealth Accreditation Number (PAN) of Health Care Institution:";
		$facility = "2. Name of Health Care Institution:";
		$address = "3. Address:";
		$address_sub_text = array('Building Number and Street Name', 'City/Municipality', 'Province','Zip Code');

		//labels for part 2
		$part2 = "PART II - PATIENT CONFINEMENT INFORMATION";
		// $pin = array('1. PhilHealth Identification Number (PIN)', 'a. ', 'b. '
		// 		,'Member', 'Patient (if dependent)');
		$name = array('1. Name of Patient:', 'Last Name', 'First Name', 'Name Extension(JR/SR/III)','Middle Name' ,'(example: DELA CRUZ, JUAN JR. SIPAG)');
		$patient_referred = array('2. Was patient referred by another Health Care Institution (HCI)?', 'NO', 'YES');
		$nameRefHCI = 'Name of Referring Health Care Institution';

		//label widths
		$ColumnWidth = array(30, 170, 50);
		$ColumnWidth2 = array(60, 70, 70);
		$ColumnWidthName = array(25,30, 20, 30, 30, 10);
		$ColumnWidthNameRefHCI = array(32, 55, 40, 25, 25, 10);
		$ColumnWidthAddress = array(70,40,40,40);
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

		//----------part 1 - display data -------------//
		$this->addTitleBar($part1,'');

		//philhealth accreditation label
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rheight7, $acc_no, $this->withoutborder, $this->continueline, $this->alignLeft);

		//philhealth accreditation number

		$objinsurance = new Insurance();
		$this->hospaccnum = $objinsurance->getAccreditationNo($_GET['id']);

		$this->Cell($this->blockwidth+48, $this->blockheight+$this->rheight3, '', $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY()+1;
		$this->writeBlock($x, $y, $acc_number);
		$width = $ColumnWidth2[1]/2;
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$hosp_acc = str_split($this->hospaccnum);
		$hosp_acc_len = strlen($this->hospaccnum);
		for($cnt = 0; $cnt < $hosp_acc_len; $cnt++){
				$this->Cell($this->blockwidth, $this->blockheight+$this->rheight3, $hosp_acc[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
		}

		$this->Cell($this->blockwidth, $this->blockheight, '', $this->withoutborder, $this->nextline, $this->alignCenter);
		//name of health care institution
		$this->Ln(1); # Added by James 2/5/2014
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rheight7+$this->rheight3, $facility, $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY()+1+$this->rheight;
		$this->Line($x+$this->rheight*2+2, $y, $x+$ColumnWidth[1], $y);
		//put the name of facility here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ColumnWidth[1], $this->rheight7+$this->rheight2-2, $this->hosp_name, $this->withoutborder, $this->continueline, $this->alignCenter);
		//address
		$this->Cell($this->blockwidth, $this->blockheight, '', $this->withoutborder, $this->nextline, $this->alignCenter);
		//address of health care institution
		$this->Ln(1); # Added by James 2/5/2014
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidth[0], $this->rheight7+$this->rheight3, $address, $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY()+1+$this->rheight;
		$this->Line($x-$this->rheight2*8, $y, $x+$ColumnWidth[1], $y);
		//put the name of facility here
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$this->Cell($ColumnWidth[1], $this->rheight7+$this->rheight2-2, $this->hosp_address, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->inspace);
		//put address sub text here
		$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label4);
		$this->Cell($ColumnWidthAddress[0]-$this->inspace, $this->rheight2*6.5, $address_sub_text[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthAddress[1], $this->rheight2*6.5, $address_sub_text[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($ColumnWidthAddress[2], $this->rheight2*6.5, $address_sub_text[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		// $this->Cell($ColumnWidthAddress[3], $this->rheight2*6.5, $address_sub_text[3], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight*2);
		//------------end of part 1 display text ----------------//

		//----------part 2 - display text ---------------------//
		$this->addTitleBar($part2,'');

		# Edited by James
	 	//name of patient
	 	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidthName[0], $this->rheight7, $name[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$x = $this->GetX();
		$y = $this->GetY()+.5;
		$width = $ColumnWidthName[1]+$ColumnWidthName[2]+$ColumnWidthName[3]+$ColumnWidthName[4]+$ColumnWidthName[5];
		// $full_name = $this->name_last. ', '.$this->name_suffix.' '.$this->name_first.', '.$this->name_middle;

		//lastname
		// $width = $this->GetStringWidth($this->name_last);
		$this->SetFont("helveticaB", $this->fontstyle_label_bold, $this->fontsize_label+3); # Added by James
		$this->Cell(33, $this->rheight7+$this->lineAdjustment*2, $this->writeChars($this->name_last), $this->withoutborder, $this->continueline, $this->alignLeft);

		//suffix name
		// $width = $this->GetStringWidth($this->name_last);
		$this->Cell(23, $this->rheight7+$this->lineAdjustment*2, $this->writeChars($this->name_first), $this->withoutborder, $this->continueline, $this->alignLeft);

		//first name
		// $width = $this->GetStringWidth($this->name_suffix);
		$this->Cell(33, $this->rheight7+$this->lineAdjustment*2, $this->writeChars($this->name_suffix), $this->withoutborder, $this->continueline, $this->alignLeft);

		//first name
		// $width = $this->GetStringWidth($this->name_first);
		$this->Cell($width, $this->rheight7+$this->lineAdjustment*2, $this->writeChars($this->name_middle), $this->withoutborder, $this->nextline, $this->alignLeft);

		$this->SetXY($x, $y);
		$x = $this->GetX()+$ColumnWidthName[0]+$this->rheight;
		$y = $this->GetY();
		$this->Line($x-$ColumnWidth[0]-$this->rheight2, $y+($this->rheight - $this->lineAdjustment)+1, $x+($ColumnWidth[0]*2)+$ColumnWidth[2]+$this->rheight7+20, $y+($this->rheight - $this->lineAdjustment)+1);
		//put name sub text here
	 	$this->Cell($this->blockwidth, $this->rheight2, '', $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label4);
		$this->Cell($ColumnWidthName[0], $this->rheight2, '', $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthName[1]+3, $this->fontsize_answer_cert-$this->lineAdjustment*2, $name[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthName[2]+3, $this->fontsize_answer_cert-$this->lineAdjustment*2, $name[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthName[3]+3, $this->fontsize_answer_cert-$this->lineAdjustment*2, $name[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthName[4]+3, $this->fontsize_answer_cert-$this->lineAdjustment*2, $name[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthName[5]+3, $this->fontsize_answer_cert-$this->lineAdjustment*2, $name[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		//Was patient referred by another Health Care Institution (HCI)?
	 	$this->Cell($this->blockwidth, $this->rheight2, '', $this->withoutborder, $this->nextline, $this->alignLeft);
	 	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidthName[0], $this->rheight7+$this->rheight2, $patient_referred[0],$this->withoutborder, $this->continueline, $this->alignLeft);

		$this->Cell($this->blockwidth+$this->rheight2, $this->rheight6, '', $this->withoutborder, $this->nextline, $this->alignLeft);
			//draw box
		$x = $this->GetX()+$this->rheight4;
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
			// check if referred by another HCI - NO
		$this->Cell($this->blockwidth+$this->inspace*1, $this->rheight3+$this->inspace*3, '', $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
		$text_val = $is_refer_by_another_hci==1 ? ' ' : '/';
		$this->Cell($this->boxwidth, $this->rheight3+$this->inspace*3, $text_val);
	 		//LABEL -NO
	 	$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->inspace, $this->rheight);
		$this->Cell($this->GetStringWidth($patient_referred[1]), $this->rheight+$this->inspace*1, $patient_referred[1], $this->withoutborder, $this->continueline, $this->alignCenter);

			//draw box
		$x = $this->GetX()+$this->rheight;
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
			// check if referred by another HCI - Yes
		$this->Cell($this->blockwidth+$this->inspace*1, $this->rheight3+$this->inspace*3, '', $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
		$text_val = $is_refer_by_another_hci==1 ? '/' : ' ';
		$this->Cell($this->boxwidth, $this->rheight3+$this->inspace*3, $text_val );
			//LABEL - YES
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->inspace, $this->rheight);
		$this->Cell($this->GetStringWidth($patient_referred[2])+$this->inspace*2, $this->rheight+$this->inspace*1, $patient_referred[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		// 3. Was patient referred by another hci
		if($is_refer_by_another_hci==1){
	 		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
			$this->Cell($this->lineAdjustment, $this->rheight7, '');
			$this->Cell($ColumnWidthNameRefHCI[1], $this->rheight7, $another_hci_name, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->Cell($this->rheight4+$this->lineAdjustment, $this->rheight7, '');

			$width = $ColumnWidthNameRefHCI[2]+$ColumnWidthNameRefHCI[3]+$ColumnWidthNameRefHCI[4]+$ColumnWidthNameRefHCI[5]+$this->rheight;
			$this->Cell($width, $this->rheight7, $another_hci_address, $this->withoutborder, $this->continueline, $this->alignCenter);
		}
		// HCI underline
		$xLine1 = $x+($this->rheight6*2);
		$yLine1 = $y+($this->rheight - $this->lineAdjustment)+1;
		$xWLine1 = $x+($ColumnWidth[0]*2)+$this->rheight7+2;
		$yWLine1 = $y+($this->rheight - $this->lineAdjustment)+1;
		$this->Line($xLine1, $yLine1, $xWLine1, $yWLine1);
		$this->Line($xLine1+$xWLine1-35, $yLine1, 200, $yWLine1);
		// HCI sub text here
	 	$this->Cell($this->blockwidth, $this->rheight2, '', $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label4);
		$this->Cell($ColumnWidthNameRefHCI[0], $this->fontsize_answer_cert, '', $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthNameRefHCI[1]-$this->rheight2, $this->fontsize_answer_cert-$this->lineAdjustment*2, $nameRefHCI, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->rheight,$this->fontsize_answer_cert-$this->lineAdjustment*2,'');
		$this->Cell($ColumnWidthNameRefHCI[2], $this->fontsize_answer_cert-$this->lineAdjustment*2, $address_sub_text[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthNameRefHCI[3], $this->fontsize_answer_cert-$this->lineAdjustment*2, $address_sub_text[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthNameRefHCI[4], $this->fontsize_answer_cert-$this->lineAdjustment*2, $address_sub_text[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthNameRefHCI[5], $this->fontsize_answer_cert-$this->lineAdjustment*2, $address_sub_text[3], $this->withoutborder, $this->continueline, $this->alignLeft);

		//----------------confinemt period label
	 	$this->addConfinementPeriod();

	 	//patient disposition label
		$this->addPatientDisposition();

		//admission diagnosis label
		$this->addAdmissionDiagnosis();

		//discharge diagnosis
		$this->addDischargeDiagnosis($this->icd_arr_desc,$this->icd_arr_code,$this->rvs_arr_desc,$this->rvs_arr_code,$this->rvs_arr_date,$this->rvs_arr_late);

		//special considerations
//		$this->addSpecialConsiderations();
        $this->addSpecialConsiderations2();

		//philhealth benefits
		$this->addPhilHealthBenefits();

		if($this->getAutoPageBreak()){
			$this->addName();
		}
	}

	function addTitleBar($title, $subtitle){
    	$x = $this->GetX();
		$y = $this->GetY()-2;
		//draw lines

		$this->Rect($x, $y+0.5, $this->totwidth, 5.5, 'DF', "",  array(FFF, FFF, FFF));
		$this->SetTextColor(255, 255, 255);
		// $this->Line($x, $y, $x+$this->totwidth, $y);
		// $this->Line($x, $y+0.5, $x+$this->totwidth, $y+0.5);

		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3);
		$x1 = $this->GetY()-1;
		$this->SetY($x1);
		$this->Cell($this->totwidth, $this->rheight2, $title, $this->withoutborder, $this->nextline, $this->alignCenter);
		if($subtitle!=''){
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italicized, $this->fontsize_label5);
			$this->Cell($this->totwidth, $this->rheight2, $subtitle, $this->withoutborder, $this->nextline, $this->alignCenter);
		}

		$this->SetTextColor(0,0,0);

		$x = $this->GetX();
		$y = $this->GetY();
		//draw lines
		// $this->Line($x, $y, $x+$this->totwidth, $y);
		// $this->Line($x, $y+0.5, $x+$this->totwidth, $y+0.5);
    }

    function addPhilHealthNumber($idnum){
    	$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$xsave = $this->GetX()+2;
		$ysave = $this->GetY()+1;
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
		if($ins_len <= 1){
			$this->writeBlockNumber($xsave, $ysave, $ins_len, $insurance);
		}else{
			$this->writeBlockNumber($xsave, $ysave, $ins_len, $insurance);
		}
    }


	function addConfinementPeriod(){
		//labels
		$confinement = array('3. Confinement Period:', 'a. Date Admitted:', 'b. Time Admitted:', 'c. Date Discharged:',
										'd. Time Discharged:');
		$formatdate = array('month', 'day', 'year', 'hour', 'min');
		$ColWidthDate = array(55.5, 13, 15.5, 42, 12.5, 16);
		$ColumnWidthConfinement = array(30, 25, 25, 12, 3, 12, 3, 32, 24);



		//----------------gather data
		$admit_date = $this->date_admitted;
        $admit_time = substr($this->time_admitted, 0, 5);
        $adtime = $this->time_admitted_ampm;

        // echo "<script>
        // alert('$adtime');
        // 	</script>
        // ";
        $discharge_date = $this->date_discharged;
        $discharge_time = substr($this->time_discharged, 0, 5) ;
        $distime = $this->time_discharged_ampm;
		//-----------end gather data

        $this->Ln(6);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColumnWidthConfinement[0], $this->rheight, $confinement[0], $this->withoutborder, $this->continueline, $this->alignLeft);
 		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthConfinement[1], $this->rheight, $confinement[1], $this->withoutborder, $this->continueline, $this->alignLeft);

		//date admitted
		$this->addBlockDate($admit_date);

 		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidthConfinement[2], $this->rheight, $confinement[2], $this->withoutborder, $this->continueline, $this->alignLeft);

		//time admitted
        $this->writeBlockTime($admit_time, $adtime);

        //admit date and time subtext
		$this->Ln($this->inspace);
       	$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label4);
		$this->Cell($ColWidthDate[0], $this->blockheight, '', $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[1], $this->blockheight+$this->rheight6, $formatdate[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[2], $this->blockheight+$this->rheight6, $formatdate[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[3], $this->blockheight+$this->rheight6, $formatdate[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[4], $this->blockheight+$this->rheight6, $formatdate[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[5], $this->blockheight+$this->rheight6, $formatdate[4], $this->withoutborder, $this->continueline, $this->alignLeft);

	 	$this->Cell($this->rheight, $this->rheight7, '', $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($ColumnWidthConfinement[0], $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColumnWidthConfinement[1], $this->rheight, $confinement[3], $this->withoutborder, $this->continueline, $this->alignLeft);

		//date discharged
		$this->addBlockDate($discharge_date);

 		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->space, $this->rheight);
		$this->Cell($ColumnWidthConfinement[2], $this->rheight, $confinement[4], $this->withoutborder, $this->continueline, $this->alignLeft);

		//time discharged
		$this->writeBlockTime($discharge_time, $distime);
		//discharge date and time subtext
		$this->Ln($this->inspace);
       	$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label4);
		$this->Cell($ColWidthDate[0], $this->blockheight, '', $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[1], $this->blockheight+$this->rheight6, $formatdate[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[2], $this->blockheight+$this->rheight6, $formatdate[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[3], $this->blockheight+$this->rheight6, $formatdate[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[4], $this->blockheight+$this->rheight6, $formatdate[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($ColWidthDate[5], $this->blockheight+$this->rheight6, $formatdate[4], $this->withoutborder, $this->continueline, $this->alignLeft);
	}

	function addPatientDisposition(){
		// /*added by Genz*/
		// $encounter_obj=new Encounter($this->encounter_nr);
		// $patient_enc_disp = $encounter_obj->getPatientEncounterDisp($this->encounter_nr);
		
		// if($patient_enc_disp['disp_code'] == 3){
		// 	$trans_hci_name = strtoupper($patient_enc_disp['hosp_name']);
		// 	$trans_hci_address = strtoupper($patient_enc_disp['hosp_add']);
		// }
		// /*end by Genz*/

		/*added by Genz*/
		$encounter_obj=new Encounter($this->encounter_nr);
		$patient_enc_disp = $encounter_obj->getPatientEncounterDispRefer($this->encounter_nr);
		
		if($patient_enc_disp['disp_code'] == 8){
			$trans_hci_name = strtoupper($patient_enc_disp['hosp_name']);
			$trans_hci_address = strtoupper($patient_enc_disp['hosp_add']);
		}
		/*end by Genz*/

		$pat_disposition = '4. Patient Disposition: (select only 1)';
		$ColWidthDispo = array(2,50,2,20,10,10);
		$pat_dis_labels = array('a. Improved', 'e. Expired,  Date:', 'b. Recovered', 'f. Transferred/Referred'
					, 'c. Home/Discharged Against Medical Advice', '','d. Absconded');
		$pat_dis_subtext = 'Name of Referral Health Care Institution';
		$address_sub_text = array('Building Number and Street Name', 'City/Municipality', 'Province', 'Zip Code');
		$reasons = 'Reason/s for referral/transfer:';
		$time_label = 'Time:';

		$this->Ln($this->inspace+4);
	 	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($ColWidthDispo[0], $this->rheight7+$this->rheight2, $pat_disposition,$this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln(1); # Added by James 2/5/2014
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);

		$this->getdisposition();

		$objBilling = new Billing();
		$row = $objBilling->getDeathDate($this->encounter_nr);

		$date = explode(' ', $row);
		$get_is_death_time = $date[1];
		$get_is_death_date = $date[0];

        $this->isNewBorn = $this->isNewBornBaby();
        //var_dump($this->isNewBorn);die;

        if ($get_is_death_date == ""){

        //Created by EJ 10/20/2014
        if ($this->disp_code == 3 || $this->disp_code == 8) {
        	$disp_trans = ' / ';
        }

        else if ($this->disp_code == 4 || $this->disp_code == 9) {
        	$disp_dama = ' / ';
        }

        else if ($this->disp_code == 5 || $this->disp_code == 10) {
        	$disp_abs = ' / ';
        }

        else if ($this->disp_code == 2 || $this->disp_code == 7) {
        	if ($this->result_code == 1 || $this->result_code == 5) {
        		$disp_reco = ' / ';
        	}
        	else if ($this->result_code == 2 || $this->result_code == 6) {
        		$disp_imp = ' / ';
        	}
        }
       
		/* Added by Genesis Oritz
		$encounter_obj=new Encounter($this->encounter_nr);
		$patient_enc_disp = $encounter_obj->getPatientEncounterDisp($this->encounter_nr);
       
       if($this->encounter_type == OUT_PATIENT || $this->isNewBorn){
			$disp_imp = ' / ';
		}

		else if ($this->disp_code){
				if ($this->disp_code == 8 || $patient_enc_disp['disp_code'] == 3)
					$disp_trans = ' / ';
				else if ($this->disp_code == 4)
					$disp_dama = ' / ';
				else if ($this->disp_code == 5)
					$disp_abs = ' / ';
			}

		else if ($this->result_code || $this->disp_code){

			if($this->result_code == 5 || $this->result_code == 1){
				$disp_reco = ' / ';

				if($this->disp_code == 8 || $this->disp_code == 3 || $patient_enc_disp['disp_code'] == 3){
					$disp_trans = ' / ';
				}    	
				else if ($this->disp_code == 9 || $this->disp_code == 4) {
					$disp_dama = " / ";
				} 
				else if ($this->disp_code == 5 || $this->disp_code == 10) {
					$disp_abs = " / ";
				} 
			}  

			else if ($this->result_code == 6 || $this->result_code == 2){
				$disp_imp = '/';

				if($this->disp_code == 8 || $this->disp_code == 3 || $patient_enc_disp['disp_code'] == 3){
					$disp_trans = ' / ';
				}
				else if ($this->disp_code == 9 || $this->disp_code == 4) {
					$disp_dama = " / ";
				} 
				else if ($this->disp_code == 5 || $this->disp_code == 10) {
					$disp_abs = " / ";
				}
			}
		} Ended by Genesis Oritz */

        } 

        else {

            $this->date_expired = date("mdY", strtotime($get_is_death_date));
            $this->time_expired = date("h:i", strtotime($get_is_death_time));
            $this->time_expired_ampm = date("A", strtotime($get_is_death_time));
            $dispo_expi = "/";
        }

		$expired_time = $this->time_expired;
		$exp_time = $this->time_expired_ampm;

        // $exptime_ = date("A", strtotime($exp_time));

		for($i=0; $i<8; $i++){
			if($i <= 3 || ($i%2==0)){
				//draw box
				if($i%2==0){
					$this->Ln($this->inspace*4);
					if($i==2){
						$this->Ln($this->inspace/2);
					}
				}
				$check_value = ' ';
				//get values
				if($i==0){//is improved
					$check_value = $disp_imp;
				}else if($i==1){
					$check_value = $dispo_expi;
				}
				else if($i==2){
					$check_value = $disp_reco;
				}
				else if($i==3){
					$check_value = $disp_trans;
				}
				else if($i==4){
					$check_value = $disp_dama;
				}
				else{
					if($i==6){
						$check_value = $disp_abs;
					}
				}
				$x = $this->GetX()+$this->rheight;
				$y = $this->GetY();
				$this->SetLineWidth(0.3);
				$this->Rect($x, $y+$this->inspace, $this->boxwidth+2, $this->boxheight+1);
				$this->SetLineWidth(0.2);
				$this->Cell($this->blockwidth+$this->inspace*2, $this->rheight3+$this->inspace*3, '', $this->withoutborder, $this->continueline, $this->alignLeft);
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
				$this->Cell($this->boxwidth, $this->rheight3+$this->inspace*3, $check_value);

				//add label
				$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label);
				$colX1 = $i%2 ==0 ? $ColWidthDispo[0] : $ColWidthDispo[2];
				$colX2 = $i%2 ==0 ? $ColWidthDispo[1] : $ColWidthDispo[3];
				$this->Cell($colX1, $this->rheight6, '',$this->withoutborder, $this->continueline, $this->alignLeft);
				$this->Cell($colX2, $this->rheight6, $pat_dis_labels[$i],$this->withoutborder, $this->continueline, $this->alignLeft);
			}
			switch ($i) {
				case 1:
					//add expired date
					$date_value = empty($this->date_expired)? '        ': $this->date_expired;
					$this->addBlockDate($date_value);
					$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label);
					$this->Cell($ColWidthDispo[4], $this->rheight6, $time_label,$this->withoutborder, $this->continueline, $this->alignRight);
					//add expired time
					$this->writeBlockTime($expired_time, $exp_time);
					break;
				case 3:
					//add name of referral HCI
 					$x = $this->GetX();
 					$y = $this->GetY();
					$this->SetFont($this->fontfamily_label."bd", $this->fontsize_label_bold, $this->fontsize_label5);
					$this->Cell( $ColWidthDispo[1]*2+$this->rheight2, $this->rheight2*3, $trans_hci_name, $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->Line($x+$this->rheight4, $y+($this->rheight - $this->lineAdjustment), $x+($ColWidthDispo[1]*2), $y+($this->rheight - $this->lineAdjustment));
					$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label4);
					$this->Ln($this->lineAdjustment);
					$this->Cell($x+$ColWidthDispo[1]+$this->rheight4*3, $this->rheight2*5.5, $pat_dis_subtext, $this->withoutborder, $this->continueline, $this->alignRight);

					break;
				case 5:
					//add address of referral HCI
					$x = $this->GetX();
 					$y = $this->GetY();
					$this->SetFont($this->fontfamily_label."bd", $this->fontsize_label_bold, $this->fontsize_label5);
					$this->Cell($this->rheight2*13, $this->rheight4*2, '', $this->withoutborder, $this->continueline, $this->alignLeft);
					$this->Cell( ($ColWidthDispo[4]*12), $this->rheight4*2, $trans_hci_address, $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->Line($x+$ColWidthDispo[3]+$this->rheight2*7.5, $y+($this->rheight6 - $this->lineAdjustment), ($ColWidthDispo[1]*4), $y+($this->rheight6 - $this->lineAdjustment));
					$this->Ln($this->lineAdjustment);
					$this->SetFont($this->fontfamily_label, $this->fontsize_label_normal, $this->fontsize_label4);
					$this->Cell($x+$ColWidthDispo[4]*6, $this->rheight2*6.5, $address_sub_text[0], $this->withoutborder, $this->continueline, $this->alignRight);
					$this->Cell($ColWidthDispo[4]*2, $this->rheight2*6.5, $address_sub_text[1], $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->Cell($ColWidthDispo[4]*2+$this->rheight, $this->rheight2*6.5, $address_sub_text[2], $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->Cell($ColWidthDispo[4]*2, $this->rheight2*6.5, $address_sub_text[3], $this->withoutborder, $this->continueline, $this->alignCenter);
					break;
				case 7:
					//add reasons 	for referral or transfer
					$x = $this->GetX();
 					$y = $this->GetY();
					$this->SetFont($this->fontfamily_label."bd", $this->fontsize_label_bold, $this->fontsize_label);
					$this->Cell($x-$ColWidthDispo[3]-$this->rheight2*7, $this->rheight4*2.75, $reasons, $this->withoutborder, $this->continueline, $this->alignRight);
					$this->SetFont($this->fontfamily_label."bd", $this->fontsize_label_bold, $this->fontsize_label5);
					$this->Cell($this->rheight2*13, $this->rheight4*2, '', $this->withoutborder, $this->continueline, $this->alignLeft);
					$this->Cell( ($ColWidthDispo[4]*5), $this->rheight*2, $this->trans_reasons, $this->withoutborder, $this->continueline, $this->alignCenter);
					$this->Line($x+$ColWidthDispo[3]+$this->rheight2*7.5, $y+$this->rheight7, ($ColWidthDispo[1]*4), $y+$this->rheight7);
					break;
				default:
					break;
 			}
 		}
//
			 		$this->ln();
			 		$this->setY($y+5);
				 	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);

					//updated by Nick, 4/10/2014 - private accomodation for out-patients
					if($this->encounter_type == OUT_PATIENT){
						$this->type_private = ' / ';
					}else{
						if($this->isCharity()){
							 $this->type_nonprivate = ' / ';
						}else{
							 $this->type_private = ' / ';
						}
					}

					$this->Cell($this->rheight2*13, $this->rheight4*2, '5. Type of Accommodation:', $this->withoutborder, $this->continueline, $this->alignLeft);
					$this->Rect($x-28, $y+7, $this->boxwidth+1, $this->boxheight+1);
	 				$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3+4);
					$this->SetX($x-29);
	 				$this->Cell(2, $this->rheight4*2, $this->type_private, $this->withoutborder, $this->continueline, $this->alignLeft);
					$this->SetX($x-24);
	 				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
					$this->Cell($this->rheight2*13, $this->rheight4*2, 'Private', $this->withoutborder, $this->continueline, $this->alignLeft);

					$this->Rect($x-13, $y+7, $this->boxwidth+1, $this->boxheight+1);
					$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label3+4);
					$this->SetX($x-14);
	 				$this->Cell(2, $this->rheight4*2, $this->type_nonprivate, $this->withoutborder, $this->continueline, $this->alignLeft);
					$this->SetX($x-9);
	 				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
					$this->Cell($this->rheight2*13, $this->rheight4*2, 'Non-Private (Charity/Service)', $this->withoutborder, $this->continueline, $this->alignLeft);

 	}

 	function addAdmissionDiagnosis()
 	{	
 		$colWidth = array(150);
		$adm_diagnosis = '6. Admission Diagnosis/es:';
		$this->Ln($this->rheight4*2);
		$x=$this->GetX();
		$y=$this->GetY();
		$this->Rect($x, $y, $this->totwidth, 0.5, 'DF', "",  array(FFF, FFF, FFF));
		// $this->Line($x,$y,$x+$this->totwidth,$y);
		// $this->Ln($this->lineAdjustment);
		$this->Ln(2); # Addded by James 2/5/2014
	 	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell(45, $this->rheight2, $adm_diagnosis,$this->withoutborder, $this->continueline, $this->alignLeft);

	 	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->MultiCell($colWidth[0], $this->rheight," $this->admission_diagnosis",$this->withoutborder, $this->alignLeft, 0, 1, '','', true, 0, false,true,0,'T',true);

 		$x=$this->GetX();
		$y=$this->GetY()+1;
		$this->Rect($x, $y, $this->totwidth, 0.5, 'DF', "",  array(FFF, FFF, FFF));
		// $this->Line($x,$y,$x+$this->totwidth,$y);
		$this->Ln(1);
 	}

#added by Nick 2/14/2014

	function addDischargeDiagnosis($icd_desc, $icd_code, $rvs_desc, $rvs_code, $rvs_date, $rvs_late) {

       $this->Ln(1);
        $this->SetFont($this->fontfamily_label . "bd", 'B', 7);
        $this->Cell($this->totwidth, 4, '7. Discharged Diagnosis/es (Use additional CF2 if necessary)', '', 1);
        $this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, 7);

        $w = array(43, 18, 43, 18, 20, 43);

        $x = array();

        $y = $this->GetY() + 4;
        $this->Cell($w[0], 4, 'Diagnosis', '', 0, 'C');
        $this->Cell($w[1] + 10, 4, 'ICD Code/s', '', 0, 'C');
        $this->Cell($w[2] - 6, 4, 'Related Procedure/s (if there\'s any)', '', 0, 'C');
        $this->Cell($w[3] + 14, 4, 'RVS Code', '', 0, 'C');
        $this->Cell($w[4] - 9, 4, 'Date of Procedure', '', 0, 'C');
        $this->Cell($w[5] + 2, 4, 'Laterality', '', 1, 'C');
        // $countLines=0;
        //diagnosis

		global $countLetter;
		global $countRoman;
        $setICDLayers=14;
        $countICD = count($icd_desc);
        $letter = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n");
        $roman = array("i", "ii", "iii", "iv", "v", "vi", "vii", "viii", "ix", "x", "xi", "xii", "xiii", "ix");
        if((!is_numeric($countLetter))){
        	$countLetter=0;
        	$countRoman=0;
        }
        for ($i = 0; $i <= $setICDLayers; $i++) {
            $x[0] = $this->GetX() + $w[0]+3;
             $this->SetFontSize(5);
            if($icd_code[$i]==""){
            	if($icd_code[$i]=="" && $icd_desc[$i]==""){
					if($i%3==0){
						$this->Cell(4, 0, $letter[$countLetter].". ", $this->withoutborder,$this->continueline,$this->alignLeft);
            			$countLetter++;
					}else{
					$this->Cell(4, 0, " ", $this->withoutborder,$this->continueline,$this->alignLeft);
					}
				}else if($icd_code[$i]=="" && $icd_desc[$i]!=""){
					$this->Cell(4, 0, " ", $this->withoutborder,$this->continueline,$this->alignLeft);

				}
            }else{
				$this->Cell(4, 0, $letter[$countLetter].". ", $this->withoutborder,$this->continueline,$this->alignLeft);
				
            	$countLetter++;
            }
            $this->Cell($w[0], 3, strtoupper($icd_desc[$i]), 'B', 1, 'L');//edited by genz change to upper case
        }

        //icd codes
        $this->SetY($y);
        for ($i = 0; $i <= $setICDLayers; $i++) {
            $this->SetX($x[0] + 2);
            $x[1] = $this->GetX() + $w[1];
            $this->Cell($w[1], 3, $icd_code[$i], 'B', 1, 'C');
        }

        //Procedures
        $this->SetY($y);
        for ($i = 0; $i <= $setICDLayers; $i++) {
            $this->SetX($x[1] + 2);
            $x[2] = $this->GetX() + $w[2];
            $this->SetFontSize(5);

				if($rvs_code[$i]!=""){
					$countRoman = 0;
				}else if($rvs_code[$i]=="" && $rvs_desc[$i]==""){
					if($i%3==0){
						$countRoman = 0;
					}
				} 
				$this->Cell(4, 0, $roman[$countRoman].". ", $this->withoutborder,$this->continueline,$this->alignLeft);
				$countRoman++;

            $this->Cell($w[2]-5, 3, strtoupper($rvs_desc[$i]), 'B', 1, 'L');//edited by genz change to upper case
        }

        //rvs codes
        $this->SetY($y);
        for ($i = 0; $i <= $setICDLayers; $i++) {
            $this->SetX($x[2] + 2);
            $x[3] = $this->GetX() + $w[3];
            $this->Cell($w[3], 3, $rvs_code[$i], 'B', 1, 'C');
        }

        //date of procedures
        $this->SetY($y);
        for ($i = 0; $i <= $setICDLayers; $i++) {
            $this->SetX($x[3] + 2);
            $x[4] = $this->GetX() + $w[4];
            $this->Cell($w[4], 3, ($rvs_date[$i] != '') ? date('m-d-Y', strtotime($rvs_date[$i])) : '', 'B', 1, 'C');
        }

        //laterality
        $this->SetY($y)-2;
        for ($i = 0; $i <= $setICDLayers; $i++) {
            $this->SetX($x[4] + 2);

            $x[5] = $this->GetX() + $w[5];
            $this->Cell(3, 3, ($rvs_late[$i] == 'L') ? '/' : '', 'TBLR', 0, 'C');
            $this->Cell(10, 3, 'Left', '', 0, 'C');

            $this->Cell(3, 3, ($rvs_late[$i] == 'R') ? '/' : '', 'TBLR', 0, 'C');
            $this->Cell(10, 3, 'Right', '', 0, 'C');

            $this->Cell(3, 3, ($rvs_late[$i] == 'B') ? '/' : '', 'TBLR', 0, 'C');
            $this->Cell(10, 3, 'Both', '', 1, 'C');
        }

        // $this->SetY(225);
        // $this->Cell(200, 4, '', 'B', 1, 'C');
    }

#end nick

	function formatSpecialDates($strdate){
		$strdate = trim($strdate,',');
		$dates = explode(',', $strdate);
		$output = '';
		foreach ($dates as $key => $value) {
			$output .= (strtotime($value) >= 0 && strtotime($value) != '') ? date('m-d-Y',strtotime($value)) . ',' : '';
		}
		$output = trim($output,',');
		return $output;
	}

    //added by Nick 06-03-2014
    function addSpecialConsiderations2(){
        /*************************************
         * Declarations/Variables
         *************************************/
        $this->Ln();
        // $this->Ln();
        $objBilling = new Billing();

        /*** LABELS FOR PACKAGES ***/
        $pkgLabels = array(
            'a. For the following repetitive procedures, check box that applies and enumerate the procedure/session dates [mm-dd-yyyy]. For chemotherapy, see guidelines.',
            'b. For Z-Benefit Package',
            'c .For MCP Package (enumerate four dates [mm-dd-yyyy] of pre-natal check-ups)',
            'd. For TB DOTS Package',
            'e. For Animal Bite Package (write the dates [mm-dd-yyyy] when the following doses of vaccine were given)',
            'f. for Newborn Care Package',
            'g. For Outpatient HIV/AIDS Treatment Package'
        );
        /*** LABELS FOR SPECIAL PACKAGES ***/
        $spLabels = array(
            'hemodialysis' => 'Hemodialysis',
            'peritoneal' => 'Peritoneal Dialysis',
            'linac' => 'Radiotherapy (LINAC)',
            'cobalt' => 'Radiotherapy (COBALT)',
            'blood_transfusion' => 'Blood transfusion',
            'brachytherapy' => 'Brachytherapy',
            'chemotherapy' => 'Chemotherapy',
            'debridment' => 'Simple Debridement'
        );
        /*** LABELS FOR NEWBORN PACKAGES ***/
        $nbLabels = array(
            'Essential Newborn Care',
            'Newborn Hearing Screening Test',
            'Newborn Screening Test',
            //'For Essential Newborn Care,',//unused
            'Immediate drying of newborn',
            'Timely cord clamping',
            'Weighing of the newborn',
            'BCG vaccination',
            'Hepatitis B vaccination',
            'Early skin-to-skin contact',
            'Eye prophylaxis',
            'Vitamin K administration',
            'Non-separation of mother/baby for early breastfeeding initiation'
        );
        /*** ANIMAL BITES LABELS ***/
        $aLabels = array(
            'Day 0 ARV',
            'Day 3 ARV',
            'Day 7 ARV',
            'RIG',
            'Others (Specify)'
        );

        /******************************
         * OUTPUT
         ******************************/

        /*** Special Procedures ***/
    	$x = $this->GetX();
		$y = $this->GetY();
		$this->Rect($x, $y, $this->totwidth, 0.5, 'DF', "",  array(FFF, FFF, FFF));
        $this->setFontStyle('b');
        $this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3);
        $this->cell(100,4,"8. Special Considerations:",'',1,'L');

		$this->Rect($x, $y+6, $this->totwidth, 0.5, 'DF', "",  array(FFF, FFF, FFF));
		$this->ln(1);

        $this->Ln();
        $this->setFontStyle('n');
        $this->Indent();
        $this->cell(100,4,$pkgLabels[0],'',1,'L');
        $spLabelsByBatch = array_chunk($spLabels,4);
        $spLabelsByBatchValues = array_chunk($this->rvs_arr_specials,4);
//        echo "<pre>".print_r($spLabelsByBatchValues,true)."</pre>";
//        echo "<pre>".print_r($this->rvs_arr_specials,true)."</pre>";
        for($i=0;$i<=3;$i++){
            $this->Indent();
            $val = (count($spLabelsByBatchValues[0][$i]) >= 1) ? implode(',',$spLabelsByBatchValues[0][$i]) : null;
            $this->checkLabel($spLabelsByBatch[0][$i],isset($val),$val,0);
            $this->Indent();
            $val = (count($spLabelsByBatchValues[1][$i]) >= 1) ? implode(',',$spLabelsByBatchValues[1][$i]) : null;
            $this->checkLabel($spLabelsByBatch[1][$i],isset($val),$val,1);
            $this->ln(0.3);
        }

        /*** Z Benefit ***/
        $this->Indent();
        $this->cell(25,4,$pkgLabels[1],'',0,'L');
        $this->Indent();
        $this->setFontStyle('b');
        $this->captionLabel('Z-Benefit Package Code:','',1);//TODO

        /*** MCP Package ***/
        $this->Indent();
        $this->setFontStyle('n');
        $this->cell(25,4,$pkgLabels[2],'',1,'L');
        $this->Indent();
        $this->setFontStyle('b');
        for($i=1;$i<=4;$i++){
            $ln = ($i == 4) ? 1 : 0;
            $this->captionLabel($i,'',$ln,3,40);//TODO
            $this->Indent(2);
        }

        /*** TB-DOTS Package ***/
        $this->Ln(1);
        $this->Indent();
        $this->setFontStyle('n');
        $this->cell(30,4,$pkgLabels[3],'',0,'L');
        $this->checkLabel('Intensive Phase',false,'',0,false);//TODO
        $this->checkLabel('Maintenance Phase',false,'',1,false);//TODO

        /*** Animal Bite Package ***/
        $this->Ln(1);
        $this->Indent();
        $this->setFontStyle('n');
        $this->cell(110,4,$pkgLabels[4],'',0,'L');
        $this->Indent();
        $this->setFontStyle('b');
        $this->cell(75,4,'NOTE: Anti Rabies Vaccine (ARV), RAbies Immunoglobin (RIG)','TBLR',1,'L');

        $cw = array(15,15,15,7,20);
        $bw = array(20,20,20,10,25);
        foreach ($aLabels as $key => $aLabel) {
            if($key >= count($aLabels) - 1)
                $ln = 1;
            else
                $ln = 0;
            $this->cell($cw[$key],4,$aLabel,'',0,'L');
            $this->cell($bw[$key],4,'','B',$ln,'L');
            $this->Indent(3);
        }

        /*** Newborn Package ***/

        $isNewborn = $this->isNewBorn;
        $hasHearingTest = $objBilling->isHearingTestAvailed($this->encounter_nr,$isNewborn);

        $this->Ln(1);
        $this->Indent();
        $this->setFontStyle('n');
        $this->cell(35,4,$pkgLabels[5],'',0,'L');
        $this->checkLabel($nbLabels[0],$isNewborn,'',0,false,true,28);
        $this->checkLabel($nbLabels[1],!$hasHearingTest && $isNewborn,'',0,false,true,39);
        $this->checkLabel($nbLabels[2],!$hasHearingTest && $isNewborn,'',0,false,true,28);
        $this->Indent(1);
        $this->setFontStyle('b');
        $caption = "    For Newborn Screening,\n    please attach NBS Sticker here";
        $this->MultiCell(47,12,$caption,'TBLR','L',false,1,'','',true,0,false,true,12,'M',false);
        $this->SetY($this->GetY() - 4);
        $this->Indent();
        $this->Indent();
        $this->cell(65,4,"For Essential Newborn Care, (Check applicable boxes)",'TBLR',1,'L');
        $this->ln(1);
        $this->addNewBornPkgItems($isNewborn,$nbLabels);

        /*** HIV/AIDS Treatment Package ***/
        $this->ln(1);
        $this->Indent();
        $this->cell(50,4,$pkgLabels[6],'',0,'L');
        $this->Indent();
        $this->setFontStyle('b');
        $this->checkLabel('Laboratory No:',false,'',1,true,false,20);
    }//end function

    //added by Nick 06-04-2014
    function addNewBornPkgItems($hasHearing,$nbLabels){
        $this->Indent();
        $this->Rect($this->GetX(),$this->GetY(),190,10);
        $this->SetY($this->GetY() + 1);
        $this->Indent();
        $this->setFontStyle('n');
        $w = array(36,30,35,25,30);
        $ci = 0;
        for($i=3;$i<=11;$i++){
            if($i == 7 || $i == 11){
                $ln = 1;
                $ci = 0;
            }else{
                $ln = 0;
            }
            $this->Indent(1);
            $this->checkLabel($nbLabels[$i],$hasHearing,'',$ln,false,$doCheckBox=true,$w[$ci]);
            if($i == 7){
                $this->Indent();
            }else{
                $ci++;
            }
        }
    }

    //added by Nick 06-03-2014
    function captionLabel($caption,$info,$ln,$cw=30,$bw=50){
        $this->cell($cw,4,$caption,'',0,'L');
        $this->cell($bw,4,$info,'B',$ln,'L');
    }

    //added by Nick 06-03-2014
    function checkLabel($caption,$isChecked,$info,$ln,$doLine=true,$doCheckBox=true,$cw=30,$bw=50){
        //var_dump($isChecked);die;
        if($doCheckBox){
            $this->cell(4,4,($isChecked) ? '/' : '' ,'TBLR',0,'C');
        }
        $this->cell($cw,4,$caption,'',($doLine) ? 0 : $ln,$cAlign);
        if($doLine){
            $this->cell($bw,4,$info,'B',$ln,'L');
        }
    }

    //added by Nick 06-03-2014
    function Indent($w=5){
        $indention = $w;
        $this->cell($indention,4,"",'',0,'L');
    }

    //added by Nick 06-03-2014
    function setFontStyle($mode='n'){
        if($mode == 'b'){
            $style =  $this->fontstyle_label_bold;
            $family = $this->fontfamily_label."bd";
        }else if($mode == 'i'){
            $style =  $this->fontstyle_label_italicized;
            $family = $this->fontfamily_label;
        }else if($mode == 'n'){
            $style =  $this->fontstyle_label_normal;
            $family = $this->fontfamily_label;
        }
        $this->SetFont($family, $style, $this->fontsize_label);
    }

    function addSpecialConsiderations()
    {
        $objBilling = new Billing(); //added by Nick, 4/24/2014
        $spcl_considerations = array('8. Speacial Considerations:',
            '',
            'a. For the following repetitive procedures, check box that applies and enumerate the procedure/session dates [mm-dd-yyyy]. For chemotherapy, see guidelines.',
            '',
            'b. For Z-Benefit Package',
            '',
            'c .For MCP Package (enumerate four dates [mm-dd-yyyy] of pre-natal check-ups)',
            '',
            'd. For TB DOTS Package',
            '',
            'e. For Animal Bite Package (write the dates [mm-dd-yyyy] when the following doses of vaccine were given)',
            '',
            'f. for Newborn Care Package',
            '',
            'g. For Outpatient HIV/AIDS Treatment Package');

        $spcl_considerationsA = array('Hemodialysis', 'Peritoneal Dialysis', 'Radiotherapy (LINAC)', 'Radiotherapy (COBALT)');
        $spcl_considerationsAA = array('Blood transfusion', 'Brachytherapy', 'Chemotherapy', 'Simple Debridement');

        $spcl_considerationsB = array('Z-Benefit Package Code:');
        $formatdate = array('month', 'day', 'year');
        $spcl_considerationsD = array('Intensive Phase', 'Maintenance Phase');
        $spcl_considerationsE = array('Essential Newborn Care',
            'Newborn Hearing Screening Test',
            'Newborn Screening Test',
            'For Essential Newborn Care,',
            'Immediate drying of newborn',
            'Timely cord clamping',
            'Weighing of the newborn',
            'BCG vaccination',
            'Hepatitis B vaccination',
            'Early skin-to-skin contact',
            'Eye prophylaxis',
            'Vitamin K administration',
            'Non-separation of mother/baby for early breastfeeding initiation');
        $spcl_considerationsF = 'Laboratory Number:';
        $ColWidthDate = array(9.5, 13, 15.5, 10);
        $ColWidthSpcl = array(50, 10, 30);

        for ($ii = 0; $ii < 4; $ii++) {

            /*** RESULT SET ***/
            $result8_A[$ii] = $this->get_special_con($spcl_considerationsA[$ii]);
            $result8_B[$ii] = $this->get_special_con(($spcl_considerationsAA[$ii] == 'Simple Debridement') ? 'Debridement' : $spcl_considerationsAA[$ii]);

            /*** DATA ARRAY ***/
            $result8_AA = $result8_A[$ii]->FetchRow();
            $result8_BB = $result8_B[$ii]->FetchRow();

            /*** DATES ***/
            $RSdate_A[$ii] = $this->formatSpecialDates($result8_AA['special_dates']);
            $RSdate_B[$ii] = $this->formatSpecialDates($result8_BB['special_dates']);

            /*** DATA COUNT ***/
            $rcountRS8_A[$ii] = $result8_A[$ii]->RecordCount();
            $rcountRS8_B[$ii] = $result8_B[$ii]->RecordCount();

            if ($rcountRS8_A[$ii] > 0) {
                $RS8value_A[$ii] = 1;
            }

            // echo $rcountRS8[$ii];
            if ($rcountRS8_B[$ii] > 0) {
                $RS8value_B[$ii] = 1;
            }
        }

        $repetitive_procedures = array(
            'Hemodialysis' => array($RS8value_A[0], $RSdate_A[0]), // 'true/false', 'concatenated dates'
            'Peritoneal Dialysis' => array($RS8value_A[1], $RSdate_A[1]),
            'Radiotherapy (LINAC)' => array($RS8value_A[2], $RSdate_A[2]),
            'Radiotherapy (COBALT)' => array($RS8value_A[3], $RSdate_A[3])

        );

        $repetitive_procedures2 = array(

            'Blood transfusion' => array($RS8value_B[0], $RSdate_B[0]),
            'Brachytherapy' => array($RS8value_B[1], $RSdate_B[1]),
            'Chemotherapy' => array($RS8value_B[2], $RSdate_B[2]),
            'Simple Debridement' => array($RS8value_B[3], $RSdate_B[3])

        );

        $z_code = '';
        $z_tranche = '';
        $prenatal_dates = array();
        $is_intesive_phase = ''; //default false , put "/" if true
        $is_maintenance_phase = '';
        $is_essential_newborn = '';
        $is_newborn_screening = '';
        $is_newborn_hearing = '';
        $is_immediate_dying = '';
        $is_vitaminK = '';
        $is_bcg = '';
        $is_hepa = '';
        $filter_no = '';
        $lab_no = '';

//variable for mcp package
        $mcppack1 = "";
        $mcppack2 = "";
        $mcppack3 = "";
        $mcppack4 = "";

        //for bite
        $bite_day1 = "";
        $bite_day3 = "";
        $bite_day7 = "";
        $bite_rig = "";
        $bite_others = "";

        // $this->Ln($this->lineAdjustment);
        # Added by James 2/5/2014
        $this->setTopMargin(20);
        $this->addName();
        $this->Ln(2);

        $x = $this->GetX() - $ColWidthSpcl[0];
        $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label);
        $this->Cell($x, $this->rheight2, $spcl_considerations[0], $this->withoutborder, $this->nextline, $this->alignLeft);

        for ($row = 1; $row < 14; $row++) {
            $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
            $this->Cell($this->rheight6, $this->rheight2, $spcl_considerations[$row], $this->withoutborder, $this->continueline, $this->alignRight);
            $row++;
            $this->SetFont($this->fontfamily_label, $this->fontstyle_label_italicized, $this->fontsize_label5);
            $nxtLine = $row == 2 || $row == 6 ? $this->nextline : $this->continueline;

            $this->Cell($this->GetStringWidth($spcl_considerations[$row]), $this->rheight2, $spcl_considerations[$row], $this->withoutborder, $nxtLine, $this->alignCenter);
            switch ($row) {
                case 2:

                    for ($i = 0; $i < 4; $i++) {
                        if ($i != 0) {
                            $this->Ln($this->inspace + $this->lineAdjustment);
                        }
                        $check_value = $repetitive_procedures[$spcl_considerationsA[$i]][0] ? '/' : '';
                        $date_value = $repetitive_procedures[$spcl_considerationsA[$i]][1];
                        $x = $this->GetX() + $this->rheight6;
                        $y = $this->GetY() - 1;
                        //edited by Nick, 3/5/2014
                        $this->SetX($x);
                        $this->SetLineWidth(0.3);
                        $this->Cell(4, 4, $check_value, $this->withborder, $this->continueline, $this->alignRight);
                        // $this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
                        $this->SetLineWidth(0.2);
                        $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                        // $this->Cell($this->blockwidth*1.5, $this->rheight3+$this->inspace*3, '', $this->withoutborder, $this->continueline, $this->alignRight);
                        // $this->Cell($this->blockwidth, $this->rheight3+$this->inspace*2, $check_value);
                        $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                        $this->Cell($ColWidthSpcl[2], $this->rheight3, $spcl_considerationsA[$i], $this->withoutborder, $this->continueline, $this->alignLeft);
                        $x = $this->GetX();
                        $y = $this->GetY() + $this->rheight3;
                        $this->Line($x, $y, ($this->totwidth / 2), $y);
                        $this->Cell($this->blockwidth, $this->rheight3 + $this->inspace * 3, '', $this->withoutborder, $this->continueline, $this->alignLeft);
                        $x = $this->GetX() - 4;
                        $this->SetX($x);
                        $this->Cell($x + 10, $this->rheight2, $date_value, $this->withoutborder, $this->continueline, $this->alignLeft);

                        $check_value2 = $repetitive_procedures2[$spcl_considerationsAA[$i]][0] ? '/' : '';
                        $date_value2 = $repetitive_procedures2[$spcl_considerationsAA[$i]][1];
                        $x = $this->GetX() + $this->rheight6 - 10;
                        $y = $this->GetY() - 1;
                        $this->SetX($x);
                        $this->SetLineWidth(0.3);
                        $this->Cell(4, 4, $check_value2, $this->withborder, $this->continueline, $this->alignRight);
                        // $this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
                        $this->SetLineWidth(0.2);
                        $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                        // $this->Cell($this->blockwidth, $this->rheight3+$this->inspace*3, '', $this->withborder, $this->continueline, $this->alignRight);
                        // $this->Cell($this->blockwidth, $this->rheight3+$this->inspace*2, $check_value2);
                        $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                        // $this->Cell(.5, $this->rheight3, "",$this->withoutborder, $this->continueline, $this->alignLeft);
                        //end Nick
                        $this->Cell($ColWidthSpcl[2], $this->rheight3, $spcl_considerationsAA[$i], $this->withoutborder, $this->continueline, $this->alignLeft);
                        $x = $this->GetX();
                        $y = $this->GetY() + $this->rheight3;
                        $this->Line($x, $y, ($this->totwidth / 2) + 80, $y);
                        $this->Cell($this->blockwidth, $this->rheight3 + $this->inspace * 3, '', $this->withoutborder, $this->continueline, $this->alignLeft);
                        $x = $this->GetX() - 4;
                        $this->SetX($x);
                        $this->Cell($this->totwidth, $this->rheight2, $date_value2, $this->withoutborder, $this->nextline, $this->alignLeft);
                    }

                    $this->Ln($this->rheight2);

                    break;
                case 4:
                    $x = $this->GetX() - 3;
                    $this->SetX($x);
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell($this->GetStringWidth($spcl_considerationsB[0]) + $ColWidthSpcl[1], $this->rheight3, $spcl_considerationsB[0], $this->withoutborder, $this->continueline, $this->alignRight);
                    $x = $ColWidthSpcl[0] + $ColWidthSpcl[1] * 2.5;
                    $y = $this->GetY() + $this->rheight3 + $this->lineAdjustment;
                    $width = $ColWidthSpcl[1] * 5 + $this->rheight2;
                    $this->Line($x + 50, $y, $width + 22, $y);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[2], $this->rheight3, $z_code, $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->Ln($this->rheight4);
                    break;
                case 6:

                    for ($i = 1; $i <= 4; $i++) {
                        $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label4);

                        $this->Cell($this->blockwidth * 2, $this->rheight3, $i, $this->withoutborder, $this->continueline, $this->alignRight);

                        $yy = $this->GetY();
                        $xx = $this->GetX();
                        $this->Cell(40, $this->rheight3, "", "B", $this->continueline, $this->alignRight);

                        if ($i == 1) {
                            $this->SetY($yy);
                            $this->SetX($xx);
                            $this->Cell(40, $this->rheight3, $mcppack1, $this->withoutborder, $this->continueline, $this->alignCenter);

                        }

                        if ($i == 2) {
                            $this->SetY($yy);
                            $this->SetX($xx);
                            $this->Cell(40, $this->rheight3, $mcppack2, $this->withoutborder, $this->continueline, $this->alignCenter);

                        }

                        if ($i == 3) {
                            $this->SetY($yy);
                            $this->SetX($xx);
                            $this->Cell(40, $this->rheight3, $mcppack3, $this->withoutborder, $this->continueline, $this->alignCenter);

                        }

                        if ($i == 4) {
                            $this->SetY($yy);
                            $this->SetX($xx);
                            $this->Cell(40, $this->rheight3, $mcppack4, $this->withoutborder, $this->continueline, $this->alignCenter);

                        }

                        $this->Cell($this->inspace, $this->rheight3, '', $this->withoutborder, $this->continueline, $this->alignRight);
                        $x = $this->GetX() - 3;
                        $this->SetX($x);
                        $date_val = sizeof($prenatal_dates) > 0 && sizeof($prenatal_dates) <= $i ? $prenatal_dates[$i - 1] : '        ';
                        // $this->addBlockDate($date_val);
                    }
                    //dates subtext
                    $this->Ln($this->inspace);

                    $this->Ln(4); # Added by James 2/2014
                    break;
                case 8:
                    //intensive phase
                    $x = $this->GetX() + $this->rheight2;
                    $y = $this->GetY() - $this->lineAdjustment * 3;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x, $y + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->inspace * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $is_intesive_phase);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3, $this->rheight2, $spcl_considerationsD[0], $this->withoutborder, $this->continueline, $this->alignLeft);
                    //maintenance phase
                    $x = $this->GetX() + $this->rheight2;
                    $y = $this->GetY() - $this->lineAdjustment * 3;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x, $y + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->inspace * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $is_maintenance_phase);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3, $this->rheight2, $spcl_considerationsD[1], $this->withoutborder, $this->continueline, $this->alignLeft);
                    $this->Ln($this->rheight4);
                    break;
                case 10:
                    $x = $this->GetX() + $this->rheight2;
                    $y = $this->GetY() - $this->lineAdjustment * 3 + 10; // james malatabon
                    $this->Cell($this->blockwidth, $this->inspace, $check_value);
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold_italicized, $this->fontsize_label5);
                    $this->Cell(78, $this->rheight2 + 2, "NOTE: Anti Rabies Vaccine (ARV), RAbies Immunoglobin (RIG)", $this->withborder, $this->nextline, $this->alignCenter);

                    $this->Ln(1); # Added by James 2/2014
//day 0
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell(15, $this->rheight3, "Day 0 ARV", $this->withoutborder, $this->continueline, $this->alignLeft);
                    $width = $ColWidthSpcl[1] * 5 + $this->rheight2;
                    $this->Line(23, $y, $width - 7, $y);
                    $this->Cell(22, $this->rheight3, $bite_day1, $this->withoutborder, $this->continueline, $this->alignLeft);
//day 3
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell(15, $this->rheight3, "Day 3 ARV", $this->withoutborder, $this->continueline, $this->alignLeft);
                    $width = $ColWidthSpcl[1] * 5 + $this->rheight2;
                    $this->Line(60, $y, $width + 30, $y);
                    $this->Cell(22, $this->rheight3, $bite_day3, $this->withoutborder, $this->continueline, $this->alignLeft);
//day 7
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell(15, $this->rheight3, "Day 7 ARV", $this->withoutborder, $this->continueline, $this->alignLeft);
                    $width = $ColWidthSpcl[1] * 5 + $this->rheight2;
                    $this->Line(97, $y, $width + 67, $y);
                    $this->Cell(22, $this->rheight3, $bite_day7, $this->withoutborder, $this->continueline, $this->alignLeft);
//RIG
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell(7, $this->rheight3, "RIG", $this->withoutborder, $this->continueline, $this->alignLeft);
                    $width = $ColWidthSpcl[1] * 5 + $this->rheight2;
                    $this->Line(126, $y, $width + 95, $y);
                    $this->Cell(22, $this->rheight3, $bite_rig, $this->withoutborder, $this->continueline, $this->alignLeft);
//Others
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell(20, $this->rheight3, "Others (Specify)", $this->withoutborder, $this->continueline, $this->alignLeft);
                    $width = $ColWidthSpcl[1] * 5 + $this->rheight2;
                    $this->Line(169, $y, $width + 150, $y);
                    $this->Cell(22, $this->rheight3, $bite_others, $this->withoutborder, $this->continueline, $this->alignLeft);
                    $this->Ln($this->rheight4);
                    break;
                case 12:
                    //added by Nick, 4-24-2014
                    $isNewBorn = $this->isNewBorn();
                    $hasAvailedHearingTest = $objBilling->isHearingTestAvailed($this->encounter_nr, $isNewBorn);
                    //end nick
                    $check_newborn = (($isNewBorn) ? ' /' : ' ');
                    //$check_newborn = ' / ';
                    $x = $this->GetX() + $this->rheight2;
                    $y = $this->GetY() - $this->lineAdjustment * 3;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x, $y + $this->inspace + .5, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->inspace * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3 - 3, $this->rheight2, $spcl_considerationsE[0], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $x = $this->GetX() + $this->rheight2;
                    $y = $this->GetY() - $this->lineAdjustment * 3;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x, $y + $this->inspace + .5, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->inspace * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, ($hasAvailedHearingTest && $isNewBorn) ? ' / ' : ''); //added by Nick, 4-24-2014
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3 + 7, $this->rheight2, $spcl_considerationsE[1], $this->withoutborder, $this->continueline, $this->alignLeft);

                    if ($isNewBorn) {
                        $chk_scrntest = ($hasAvailedHearingTest) ? '' : ' / ';
                    } else {
                        $chk_scrntest = false;
                    }

                    $x = $this->GetX() + $this->rheight2;
                    $y = $this->GetY() - $this->lineAdjustment * 3;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x, $y + $this->inspace + .5, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->inspace * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $chk_scrntest); //added by Nick, 4-24-2014
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3 - 1, $this->rheight2, $spcl_considerationsE[2], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $yyy = $this->GetY();
                    $xxx = $this->GetX();
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3 + 17, $this->rheight2 + 11, "", $this->withborder, $this->continueline, $this->alignLeft);

                    $this->SetY($yyy + 3.5);
                    $this->SetX($xxx + 3.5);
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3 - 1, $this->rheight2 / 2, "For Newborn Screening,", $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->SetY($yyy + 6.3);
                    $this->SetX($xxx + 3.5);
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_italicized, $this->fontsize_label);
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3 - 1, $this->rheight2 / 2, "please attach NBS Sticker here", $this->withoutborder, $this->continueline, $this->alignLeft);
                    $this->Ln($this->rheight4 + $this->lineAdjustment);

                    $this->SetY($yyy + 8);
                    $this->SetX($xxx - 137);
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1] + $this->rheight7 * 3 + 34, $this->rheight2 + 3, "For Essential Newborn Care, (Check applicable boxes)", $this->withborder, $this->nextline, $this->alignLeft);

                    //for essential new born care BOx
                    $this->Cell($this->blockwidth * 1.5, $this->inspace, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $x = $this->GetX();
                    $y = $this->GetY() - $this->lineAdjustment * 3;
                    $width = $ColWidthSpcl[0] * 2 + $ColWidthSpcl[1] * 3 + $this->rheight;
                    $height = $ColWidthSpcl[1] + $this->lineAdjustment * 2;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x, $y + $this->inspace + 2, $width + 50, $height);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    // $this->Cell($ColWidthSpcl[2]+$ColWidthSpcl[1], $this->rheight6, $spcl_considerationsE[3], $this->withoutborder, $this->continueline, $this->alignLeft);
                    $x = $this->GetX();

                    $this->Ln(2); # Added by James 2/2014
                    $this->SetX($x - 8);
                    //Immediate dying of newborn etc.

                    $x1 = $this->GetX() + $this->rheight * 2;
                    $y1 = $this->GetY() - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x1, $y1 + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[2], $this->rheight, $spcl_considerationsE[4], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->SetX($x - 8);

                    //Early skin
                    $x = $this->GetX() + $this->rheight * 2;
                    $y = $this->GetY() + $this->rheight - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x, $y + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace + $this->rheight7 * 2, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[2], $this->inspace + $this->rheight7 * 2, $spcl_considerationsE[9], $this->withoutborder, $this->continueline, $this->alignLeft);

                    //timely cord
                    $x = $this->GetX();
                    $x1 = $this->GetX() + $this->rheight * 2;
                    $y1 = $this->GetY() - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x1, $y1 + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1], $this->rheight, $spcl_considerationsE[5], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->SetX($x);
                    //eye prophylaxis
                    $x1 = $this->GetX() + $this->rheight * 2;
                    $y1 = $this->GetY() + $this->rheight - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x1, $y1 + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight7 * 2, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace + $this->rheight7 * 2, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1], $this->rheight7 * 2, $spcl_considerationsE[10], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->SetX($x + 30);

                    //weighing of the newborn
                    $x = $this->GetX();
                    $x1 = $this->GetX() + $this->rheight * 2;
                    $y1 = $this->GetY() - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x1, $y1 + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1], $this->rheight, $spcl_considerationsE[6], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->SetX($x);
                    //vitamin K
                    $x1 = $this->GetX() + $this->rheight * 2;
                    $y1 = $this->GetY() + $this->rheight - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x1, $y1 + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight7 * 2, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace + $this->rheight7 * 2, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1], $this->rheight7 * 2, $spcl_considerationsE[11], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->SetX($x + 35);

                    //BCG vaccination
                    $x = $this->GetX();
                    $x1 = $this->GetX() + $this->rheight * 2;
                    $y1 = $this->GetY() - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x1, $y1 + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1], $this->rheight, $spcl_considerationsE[7], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->SetX($x);
                    //Non-seperation
                    $x1 = $this->GetX() + $this->rheight * 2;
                    $y1 = $this->GetY() + $this->rheight - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x1, $y1 + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight7 * 2, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace + $this->rheight7 * 2, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1], $this->rheight7 * 2, $spcl_considerationsE[12], $this->withoutborder, $this->continueline, $this->alignLeft);

                    //Hepaptitis
                    $this->SetX($x + 35);
                    $x = $this->GetX();
                    $x1 = $this->GetX() + $this->rheight * 2;
                    $y1 = $this->GetY() - $this->lineAdjustment;
                    $this->SetLineWidth(0.3);
                    $this->Rect($x1, $y1 + $this->inspace, $this->boxwidth + 1, $this->boxheight + 1);
                    $this->SetLineWidth(0.2);
                    $this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check2);
                    $this->Cell($this->rheight * 2, $this->rheight, '', $this->withoutborder, $this->continueline, $this->alignRight);
                    $this->Cell($this->blockwidth, $this->inspace, $check_newborn);
                    $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[1], $this->rheight, $spcl_considerationsE[8], $this->withoutborder, $this->continueline, $this->alignLeft);

                    $this->Ln($this->rheight7 + $this->rheight4);
                    break;

                case 14:
                    $this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label5);
                    $this->Cell($ColWidthSpcl[2], $this->rheight3, $spcl_considerationsF, $this->withoutborder, $this->continueline, $this->alignRight);
                    $x = $this->GetX();
                    $this->Cell($ColWidthSpcl[2], $this->rheight3, $lab_no, $this->withoutborder, $this->continueline, $this->alignLeft);
                    $y = $this->GetY() + $this->rheight3 + $this->lineAdjustment;
                    $width = $ColWidthSpcl[0] * 2 + $ColWidthSpcl[1] * 2 + $this->rheight7;
                    $this->Line($x, $y, $width, $y);
                    break;
                default:
                    # code...
                    break;
            }
        }
        $this->Ln($this->rheight4 + $this->lineAdjustment);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Line($x, $y, $x + $this->totwidth, $y);
    }

 	function addPhilHealthBenefits(){
		$benefits_label = array('9. PhilHealth Benefits',
		'ICD 10 or RVS Code',
		'Total Case Rate Amount',
		 'a. First Case Rate', 
		 'b. Second Case Rate',
		  'Grand Total:'); 
	$ColWidthBen = array(40,28,30,5,30);
   $x = $this->GetX();
   $y = $this->GetY();
   $this->Ln(1);
   $this->Rect($x, $y+2, $this->totwidth, 0.5, 'DF', "",  array(FFF, FFF, FFF));
	$this->Ln(2);
	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3);
   $this->Cell($ColWidthBen[0], $this->rheight2, $benefits_label[0],$this->withoutborder, $this->nextline, $this->alignLeft);
   $this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label5);
   $this->Cell($ColWidthBen[1], $this->rheight2, '',$this->withoutborder, $this->continueline, $this->alignLeft);
   $this->Cell($ColWidthBen[2], $this->rheight2, $benefits_label[1],$this->withoutborder, $this->continueline, $this->alignCenter);

   
   //first case
   // $this->Cell($ColWidthBen[0], $this->rheight2, '',$this->withoutborder, $this->continueline, $this->alignLeft);
   $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
   $this->Cell($ColWidthBen[1]-8, $this->rheight2, $benefits_label[3],$this->withoutborder, $this->continueline, $this->alignLeft);
   //icd first case
   $x = $this->GetX();
   $this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3+2);
   $this->Cell($ColWidthBen[2], $this->rheight2, $this->getCaseRate(1) ,$this->withoutborder, $this->continueline, $this->alignCenter);
   $this->SetX($x);
   $x1=$this->GetX();
   $y1=$this->GetY()+$this->rheight3;
   $this->Line($x1,$y1+1,$x1+$ColWidthBen[2],$y1+1);
   $this->Cell($ColWidthBen[3], $this->rheight2, '',$this->withoutborder, $this->continueline, $this->alignLeft);

   //second case
   $this->Cell($ColWidthBen[0], $this->rheight2, '',$this->withoutborder, $this->continueline, $this->alignLeft);
   $this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
   $this->Cell($ColWidthBen[1], $this->rheight2, $benefits_label[4],$this->withoutborder, $this->continueline, $this->alignLeft);
   //icd second case
   $x=$this->GetX();
   $this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label3+2);
   $this->Cell($ColWidthBen[2], $this->rheight2,$this->getCaseRate(2),$this->withoutborder, $this->continueline, $this->alignCenter);
   $this->SetX($x);
   $x=$this->GetX();
   $y=$this->GetY()+$this->rheight3;
   $this->Line($x,$y+1,$x+$ColWidthBen[2],$y+1);
   $this->Cell($ColWidthBen[3], $this->rheight2, '',$this->withoutborder, $this->continueline, $this->alignLeft);
   $this->Ln(5);
   $this->SetFont($this->fontfamily_label."bd", '', '5');
 	}

 	function addProfessionalFees(){
 		$prof_fee_label = '10. Professional Fees / Charges (use additional sheet if necessary):';
 		$col_header = array('Accreditation Number / Name of Accredited Health Care Professional / Date Signed:',
 			'Details');
 		$prof_labels = array('Accreditation No.:','Date Signed:','No Co-pay on top of PhilHealth Benefit', 'with Co-pay on top of PhilHealth Benefit', 'P');
 		$prof_sub_text = array('Signature Over Printed Name', 'month','day','year');
 		///-------gather data-----------------///

			// if (!empty($this->professional_fee_array)) {
			// 	foreach($this->professional_fee_array as $objconf){
			// 		$obj = new ProfessionalFee();
			// 		$obj->accrediation_no =$objconf->$accrediation_no;
			// 		$obj->name =$objconf->$name;
			// 		$obj->date_signed =$objconf->$date_signed;
			// 		$obj->is_co_pay =$objconf->$is_co_pay;
			// 		$obj->amt =$objconf->$amt;
			// 		$prof_fee_arr[] = $obj;
			// 		$lenProf++;
			// 	}
			// }
 		$ColWidthProf = array(100, 50, 25, 15, 10, 5);

 		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label5);
		$this->Cell($ColWidthBen[0], $this->rheight2, $prof_fee_label,$this->withoutborder, $this->nextline, $this->alignLeft);
		//horizontal line
 		$x=$this->GetX();
		$y=$this->GetY()+$this->lineAdjustment;
		$this->Line($x,$y,$x+$this->totwidth,$y);
		$this->Ln($this->rheight2);
		//column header
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$x1 = (100 - $this->GetStringWidth($col_header[0]))/2;
		$this->Cell($x1, $this->rheight2, '',$this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->GetStringWidth($col_header[0]), $this->rheight2, $col_header[0],$this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($x1, $this->rheight2, '',$this->withoutborder, $this->continueline, $this->alignCenter);
		//vertical line (at the center)
		$x=$this->GetX();
		$y=$this->GetY()-$this->lineAdjustment*3;
		$height = $y + $ColWidthProf[1]*2.5-$this->rheight3;
		$this->Line($x,$y,$x, $height);
		//column header
		$this->Cell($ColWidthProf[0]-$this->GetStringWidth($col_header[1]), $this->rheight2, $col_header[1],$this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Ln($this->rheight2);

		$applied_discount = $this->getTotalAppliedDiscounts();
		$result = $this->get_doctor_info();
 		if($result)
		{
			while($row=$result->FetchRow())
			{
				$cnt++;
				if($cnt!=0){
			 		$this->Ln($this->rheight);
			 	}
				//horizontal line
				$x=$this->GetX();
				$y=$this->GetY();
				$this->Line($x,$y,$x+$this->totwidth,$y);
					//accrediation label
				$this->Ln($this->rheight4);
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
				$this->Cell($x+$this->GetStringWidth($prof_labels[0])+$this->rheight2, $this->rheight2, $prof_labels[0],$this->withoutborder, $this->continueline, $this->alignCenter);
				$acc_num = $lenProf > 0 ? $row['acc_no'] : '    -       - ';
				$this->addAccrediationNo($row['acc_no']);
				$this->Ln($this->rheight);
				
				//line for signature 
				$this->Cell($this->rheight4*4,'');
				$x=$this->GetX();
				$y=$this->GetY()+$this->rheight7;
				$width = $ColWidthProf[1]+$ColWidthProf[2]+$ColWidthProf[4];
				$this->Line($x,$y,$width,$y);
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
				//$prof_name = $lenProf > 0 ? $prof_fee_arr[$i][1] : '';
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label3);
				$this->Cell($width-$this->GetStringWidth($row['doc_name'])+20, $this->rheight*2, strtoupper($row['doc_name']),$this->withoutborder, $this->continueline, $this->alignCenter);
				//signature sub text
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
				$this->Ln($this->rheight7);
				$this->Cell($this->rheight6*3,'');
				$this->Cell($width-$this->GetStringWidth($prof_sub_text[0]), $this->rheight2, $prof_sub_text[0],$this->withoutborder, $this->nextline, $this->alignCenter);
				$this->Ln($this->rheight2);
				$this->Cell($this->rheight6*3,'');
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
				$this->Cell($width-$this->GetStringWidth($prof_labels[1]), $this->rheight2, $prof_labels[1],$this->withoutborder, $this->nextline, $this->alignLeft);
				$x = $this->GetX() + $ColWidthProf[2] +$ColWidthProf[4]-$this->lineAdjustment*3;
				$y = $this->GetY() - $this->rheight4 ;
				$this->SetXY($x,$y);
				$date_val = $lenProf > 0 ? $prof_fee_arr[$i][2] : '        ';
				$this->addBlockDate($date_val);
				//date sub text
				$y += $this->rheight4+$this->lineAdjustment;
				$this->SetXY($x,$y);
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
				$this->Cell($this->GetStringWidth($prof_sub_text[1])+$this->rheight3, $this->rheight2, $prof_sub_text[1],$this->withoutborder, $this->continueline, $this->alignCenter);
				$this->Cell($this->GetStringWidth($prof_sub_text[2])+$this->rheight7, $this->rheight2, $prof_sub_text[2],$this->withoutborder, $this->continueline, $this->alignRight);
				$this->Cell($this->GetStringWidth($prof_sub_text[3])+$this->rheight6*2+$this->lineAdjustment, $this->rheight2, $prof_sub_text[3],$this->withoutborder, $this->continueline, $this->alignRight);
				$yLast = $this->GetY();
				$isCoPay =  $lenProf > 0 ? $prof_fee_arr[$i][3] : false;
				//no co pay 
				$doc_discount = $row['dr_charge'] * $applied_discount;
				$copay_amount = $row['dr_charge'] - $doc_discount - $row['dr_claim'];
				$check_value = '';
				$check_value1 = '';
				if($copay_amount<=0){
					$check_value = ' / ';
					$copay_amount = '';
				}else{
					$check_value1 =' / ';
					$copay_amount = number_format($copay_amount,2,'.',',');
				}

				$x1 = $this->GetX()+$ColWidthProf[3]*2+$this->rheight4*2;
				$y1 = $this->GetY()-$ColWidthProf[4]-$this->rheight6;
				$this->SetXY($x1,$y1);
				$x = $this->GetX();
				$y = $this->GetY();
				$width = $this->blockwidth+$this->lineAdjustment*1.75;
				$height = $this->blockheight+$this->lineAdjustment;
				$this->SetLineWidth(0.3);
				$this->Rect($x, $y+$this->inspace, $width, $height);
				$this->SetLineWidth(0.2);
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
				
				$this->Cell($this->rheight6, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignCenter);
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
				$this->Cell($width-$this->GetStringWidth($prof_labels[2]), $this->rheight7, $prof_labels[2],$this->withoutborder, $this->nextline, $this->alignLeft);
				//with co pay
				$y1 += $this->rheight6;
				$this->SetXY($x1,$y1);
				$x = $this->GetX();
				$y = $this->GetY();
				$width = $this->blockwidth+$this->lineAdjustment*1.75;
				$height = $this->blockheight+$this->lineAdjustment;
				$this->SetLineWidth(0.3);
				$this->Rect($x, $y+$this->inspace, $width, $height);
				$this->SetLineWidth(0.2);
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
				
				$this->Cell($this->rheight6, $this->rheight7, $check_value1, $this->withoutborder, $this->continueline, $this->alignCenter);
				$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
				$this->Cell($this->GetStringWidth($prof_labels[3]), $this->rheight7, $prof_labels[3],$this->withoutborder, $this->continueline, $this->alignLeft);
				$this->Cell($this->rheight7, $this->rheight7, $prof_labels[4],$this->withoutborder, $this->continueline, $this->alignRight);
				//line
				$x=$this->GetX();
				$y=$this->GetY()+$this->rheight+$this->lineAdjustment;
				$width = $ColWidthProf[0]*2;
				$this->Line($x,$y,$width,$y);
				$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_table);
				$x-= $this->lineAdjustment*2;
				$y-= 7 ;
				$this->SetXY($x,$y);
				$amt_val = $lenProf > 0 && $isCoPay==true ? $prof_fee_arr[$i][3] : '';
				$this->Cell($ColWidthProf[2]+$this->rheight, $this->rheight*2, $copay_amount ,$this->withoutborder, $this->continueline, $this->alignCenter);
				$this->SetY($yLast);
			}
		}	

		
		for($i=$cnt; $i<3; $i++){
			 if($i!=0){
			 	$this->Ln($this->rheight);
			 }
			//horizontal line
			$x=$this->GetX();
			$y=$this->GetY();
			$this->Line($x,$y,$x+$this->totwidth,$y);
			//accrediation label
			$this->Ln($this->rheight4);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($x+$this->GetStringWidth($prof_labels[0])+$this->rheight2, $this->rheight2, $prof_labels[0],$this->withoutborder, $this->continueline, $this->alignRight);
			$acc_num = $lenProf > 0 ? $prof_fee_arr[$i][0] : '    -       - ';
			$this->addAccrediationNo($acc_num);
			$this->Ln($this->rheight);
			
			//line for signature 
			$this->Cell($this->rheight4*4,'');
			$x=$this->GetX();
			$y=$this->GetY()+$this->rheight7+8;
			$width = $ColWidthProf[1]+$ColWidthProf[2]+$ColWidthProf[4];
			$this->Line($x,$y,$width,$y);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$prof_name = $lenProf > 0 ? $prof_fee_arr[$i][1] : '';
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label3);
			$this->Cell($width-$this->GetStringWidth($prof_name), $this->rheight*2, $prof_name,$this->withoutborder, $this->continueline, $this->alignCenter);
			//signature sub text
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Ln($this->rheight7+8);
			$this->Cell($this->rheight6*3,'');
			$this->Cell($width-$this->GetStringWidth($prof_sub_text[0]), $this->rheight2, $prof_sub_text[0],$this->withoutborder, $this->nextline, $this->alignCenter);
			$this->Ln($this->rheight2);
			$this->Cell($this->rheight6*3,'');
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($width-$this->GetStringWidth($prof_labels[1]), $this->rheight2, $prof_labels[1],$this->withoutborder, $this->nextline, $this->alignLeft);
			$x = $this->GetX() + $ColWidthProf[2] +$ColWidthProf[4]-$this->lineAdjustment*3;
			$y = $this->GetY() - $this->rheight4 ;
			$this->SetXY($x,$y);
			$date_val = $lenProf > 0 ? $prof_fee_arr[$i][2] : '        ';
			$this->addBlockDate($date_val);
			//date sub text
			$y += $this->rheight4+$this->lineAdjustment;
			$this->SetXY($x,$y);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
			$this->Cell($this->GetStringWidth($prof_sub_text[1])+$this->rheight3, $this->rheight2, $prof_sub_text[1],$this->withoutborder, $this->continueline, $this->alignCenter);
			$this->Cell($this->GetStringWidth($prof_sub_text[2])+$this->rheight7, $this->rheight2, $prof_sub_text[2],$this->withoutborder, $this->continueline, $this->alignRight);
			$this->Cell($this->GetStringWidth($prof_sub_text[3])+$this->rheight6*2+$this->lineAdjustment, $this->rheight2, $prof_sub_text[3],$this->withoutborder, $this->continueline, $this->alignRight);
			$yLast = $this->GetY();
			$isCoPay =  $lenProf > 0 ? $prof_fee_arr[$i][3] : false;
			//no co pay 
			$x1 = $this->GetX()+$ColWidthProf[3]*2+$this->rheight4*2;
			$y1 = $this->GetY()-$ColWidthProf[4]-$this->rheight6;
			$this->SetXY($x1,$y1);
			$x = $this->GetX();
			$y = $this->GetY();
			$width = $this->blockwidth+$this->lineAdjustment*1.75;
			$height = $this->blockheight+$this->lineAdjustment;
			$this->SetLineWidth(0.3);
			$this->Rect($x, $y+$this->inspace, $width, $height);
			$this->SetLineWidth(0.2);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
			$check_value = $isCoPay==false && $lenProf > 0 ? '/' : '';
			$this->Cell($this->rheight6, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($width-$this->GetStringWidth($prof_labels[2]), $this->rheight7, $prof_labels[2],$this->withoutborder, $this->nextline, $this->alignLeft);
			//with co pay
			$y1 += $this->rheight6;
			$this->SetXY($x1,$y1);
			$x = $this->GetX();
			$y = $this->GetY();
			$width = $this->blockwidth+$this->lineAdjustment*1.75;
			$height = $this->blockheight+$this->lineAdjustment;
			$this->SetLineWidth(0.3);
			$this->Rect($x, $y+$this->inspace, $width, $height);
			$this->SetLineWidth(0.2);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
			$check_value = $isCoPay==true ? '/' : '';
			$this->Cell($this->rheight6, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($this->GetStringWidth($prof_labels[3]), $this->rheight7, $prof_labels[3],$this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->rheight7, $this->rheight7, $prof_labels[4],$this->withoutborder, $this->continueline, $this->alignRight);
			//line
			$x=$this->GetX();
			$y=$this->GetY()+$this->rheight+$this->lineAdjustment;
			$width = $ColWidthProf[0]*2;
			$this->Line($x,$y,$width,$y);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_table);
			$x-= $this->lineAdjustment*2;
			$y-= 1 ;
			$this->SetXY($x,$y);
			$amt_val = $lenProf > 0 && $isCoPay==true ? $prof_fee_arr[$i][3] : '';
			$this->Cell($ColWidthProf[2]+$this->rheight, $this->rheight*2, $amt_val,$this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetY($yLast);
		}//end for loop accrediation no

		$this->Ln();

		$x = $this->GetX();
		$y = $this->GetY();
		//draw lines
		$this->Line($x, $y, $x+$this->totwidth, $y);
		$this->Line($x, $y+0.5, $x+$this->totwidth, $y+0.5);
 	}
	function addPart3(){
		$part3 = 'PART III - CERTIFICATION OF CONSUMPTION OF BENEFITS AND CONSENT TO ACCESS PATIENT RECORD/S';
		$part3_sub = 'NOTE: Member should sign only after the applicable charges have been filled-out';
		$a = array('A.',' CERTIFICATION OF CONSUMPTION OF BENEFITS');
		$a_ln1 = 'Statement of Account (SOA) is attached amounting to P';
		$a_ln2 = '(tick one that applies)';
		$a_ln3 = 'No outside purchases of drugs/medicines,supplies,diagnostics, and co-pay for professional fees from member/patient.';
		$a_ln4 = 'PhilHealth benefit is enough to cover facility and PF charges.';
		$a_ln5 = 'The benefits of the member/patient was completely used up prior to co-pay OR the benefit of the member/patient is not completely consumed BUT with ';
		$a_ln55 = 'purchase/expenses for drugs/medicines,supplies,diagnostics and others.';
		$a_ln6 = 'The total co-pay for the following is/are:';
		$a_co_pay = array('HCI changes','Outside purchase/s for drugs/medicines and/or medical supplies not paid for by the HCI','Cost of diagnostic/laboratory examinations done outside not paid for by the HCI','Total Co-pay for Professional Fee/s (including non-accredited health care professionals)', 'TOTAL CO-PAY');
		$a_ln11 = array('P', 'None');
		$b = array('B.',' CONSENT TO ACCESS PATIENT RECORD/S');
		$b_ln1 = array('I hereby consent to the examination by PhilHealth of the patient\'s medical records for the sole purpose of verifying the veracity of this claim.','I hereby hold PhilHealth or any of its officers, employees and/or representatives free from any and all liabilities relative to the herein-mentioned consent which I have voluntarily', 'and willingly given in connection with this claim for reimbursement before PhilHealth.');
		$conforme_label = array('', 'Signature Over Printed Name of Patient/Authorized Representative');
		$date_label = array('Date Signed:', 'month', 'day', 'year');
		$relationship_label = array('Relationship of the representative to the patient:','Spouse','Sibling','Child', 'Parent','Others, Specify');
		$reasons_label = array('Reasons for signing on behalf of the patient:', 'Patient is Incapacitated', 'Other Reasons:');
		$representative_label = array('If patient/representative is unable to write, put right thumbmark. Patient/representative should be assisted by an HCI representative. Check the appropriate box:','Patient', 'Representative'); 


		$soa_amt = ''; //soa amount variable 
		$is_no_outside_purchases = ''; //check box if true put '/'
		$is_benefit_of_member = ''; //check box if true put '/'
		$hci_amt = 0;
		$out_purchases_amt = 0;
		$cost_exam_amt = 0;
		$prof_fee_amt = 0;
		$conforme_name = '';
		$conforme_signed = '';
		$conforme_relationship = ''; // either 'spouse', 'child', 'parent', 'sibling', 'other'
		$rel_other = ''; //relationship others specify 
		$conforme_reasons = '';  //either 'incapacitated' or 'other'
		$reasons_other = ''; //reasons others specify
		$thumbmark_data = ''; // either 'patient' or 'representative'

		$Smember1="";
		$Smember2="";
		$Shmo1="";
		$Shmo2="";
		$Sothers1="";
		$Sothers2="";


		$none1="";
		$none2="";
		$total_amount1="";
		$total_amount2="";
		$total_value1="";
		$total_value2="";

		$ColWidthPart3 = array(10,20,30,40,50,100);

		$this->Ln($this->rheight7);
		$this->addTitleBar($part3,$part3_sub);
		//A. CERTIFICATION OF CONSUMPTION OF BENEFITS
		//$this->Ln($this->inspace);
	 	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($a[0]), $this->rheight7, $a[0],$this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($a[1]), $this->rheight7, $a[1],$this->withoutborder, $this->nextline, $this->alignLeft);
		
		$x1 = $this->GetX()+$this->rheight6;
		$y1 = $this->GetY()+$this->lineAdjustment-3;
		$this->SetLineWidth(0.3);
		$this->Rect($x1, $y1+$this->inspace, $this->boxwidth+1.5, $this->boxheight+1.5);
		$this->SetLineWidth(0.2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal-1, $this->fontsize_label_normal);

		$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"PhilHealth benefit is enough to cover HCI and PF charges.",$this->withoutborder, $this->nextline, $this->alignRight);


		$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+57, $this->rheight2,"No purchase of drugs/medicine,supplies,diagnostics,and co-pay for professional fees by the member/patient.",$this->withoutborder, $this->nextline, $this->alignRight);



			$x2 = $this->setx($x1+15);
		$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"",$this->withborder, $this->continueline, $this->alignRight);
		$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"Total Actual Changes*",$this->withborder, $this->nextline, $this->alignCenter);


			$x2 = $this->setx($x1+15);
			$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"Total Health Care Institution Fees",$this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"",$this->withborder, $this->nextline, $this->alignCenter);




			$x2 = $this->setx($x1+15);
			$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"Total Professional Fees",$this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"",$this->withborder, $this->nextline, $this->alignCenter);



	$x2 = $this->setx($x1+15);
			$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"Grand Total",$this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->GetStringWidth($a_ln1)+$this->rheight7+8, $this->rheight2,"",$this->withborder, $this->nextline, $this->alignCenter);


$this->ln(5);



		$x1 = $this->GetX()+$this->rheight6;
		$y1 = $this->GetY()+$this->lineAdjustment-3;
		$this->SetLineWidth(0.3);
		$this->Rect($x1, $y1+$this->inspace, $this->boxwidth+1.5, $this->boxheight+1.5);
		$this->SetLineWidth(0.2);


	 $this->setx($x1);
	$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal-1, $this->fontsize_label_normal);

		$this->Cell($this->GetStringWidth($a_ln5)+$this->rheight7+8, $this->rheight2,$a_ln5,$this->withoutborder, $this->nextline, $this->alignCenter);
		$this->Cell($this->GetStringWidth($a_ln55)+$this->rheight7+8, $this->rheight2,$a_ln55,$this->withoutborder, $this->nextline, $this->alignRight);
		
		$this->Cell($this->GetStringWidth("a.) The total co-pay for the following are:")+$this->rheight7+8, $this->rheight2,"a.) The total co-pay for the following are:",$this->withoutborder, $this->nextline, $this->alignRight);

	 	$this->setx($x1+10);
		$this->Cell(20, $this->rheight2+6.2,"",$this->withborder, $this->continueline, $this->alignRight);

			$this->setx($x1+30);
 			$yy2 = $this->getY();
					$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
	

		$this->Multicell(20, $this->rheight2+6.2,"Total Actual Charges*",$this->withborder,'C');

 
 $this->sety($yy2);
$this->setx($x1+50);
		$this->Multicell(35, $this->rheight2+5,"Amount after Application of Discount (i.e., personal discount, Senior Citizen/PWD)",$this->withborder,'C');


$this->sety($yy2);
$this->setx($x1+85);
		$this->Multicell(20, $this->rheight2+6.2,"Philhealth Benefit",$this->withborder,'C');



$this->sety($yy2);
$this->setx($x1+105);
		$this->Multicell(50, $this->rheight2+6.2,"Amount after Philhealth Deduction",$this->withborder,'C');


//-----------------------------------r2-----------------------------------------------

			$this->setx($x1+10);
 			$yy2 = $this->getY();
			$this->Multicell(20, $this->rheight2+15,"Total Health Care Institution Fees",$this->withborder,'C');


$this->sety($yy2);
$this->setx($x1+30);
		$this->Multicell(20, $this->rheight2+15,"",$this->withborder,'C');


 $this->sety($yy2);
$this->setx($x1+50);
		$this->Multicell(35, $this->rheight2+15,"",$this->withborder,'C');



 $this->sety($yy2);
$this->setx($x1+85);
		$this->Multicell(20, $this->rheight2+15,"",$this->withborder,'C');


$this->sety($yy2);
$this->setx($x1+105);
		$this->Multicell(50, $this->rheight2+15,"",$this->withborder,'C');

$this->sety($yy2+1);
$this->setx($x1+105);


	$this->Cell(10, $this->rheight2,"     Amount P",$this->withoutborder,'C');
	$this->Cell(13, $this->rheight2,"     Amount P",$this->withoutborder,'R');
$this->ln();

$this->sety($yy2+3.5);
$this->setx($x1+108);
$this->Cell(13, $this->rheight2,"Paid by (Check all that applies):",$this->withoutborder,'R');

// $this->sety($yy2+3.5);
// $this->setx($x1+120);
$this->Rect($x1+108, $yy2+6, $this->boxwidth+1.5, $this->boxheight+1.5);

$this->sety($yy2+6.5);
$this->setx($x1+112);
	$this->Cell(13, $this->rheight2,"Member/Patient",$this->withoutborder,'R');


$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->sety($yy2+6);
$this->setx($x1+109);
	$this->Cell(5, $this->rheight2,$Smember1,$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);

//------------------------

$this->Rect($x1+128, $yy2+6, $this->boxwidth+1.5, $this->boxheight+1.5);

$this->sety($yy2+6.5);
$this->setx($x1+132);
	$this->Cell(13, $this->rheight2,"HMO",$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->sety($yy2+6);
$this->setx($x1+129);
	$this->Cell(5, $this->rheight2,$Shmo1,$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);


//---------------------------------


$this->Rect($x1+108, $yy2+11, $this->boxwidth+1.5, $this->boxheight+1.5);

$this->sety($yy2+12);
$this->setx($x1+112);
	$this->Cell(13, $this->rheight2,"Others (i.e., PCSO, Promissory note, etc.)",$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->sety($yy2+11);
$this->setx($x1+109);
	$this->Cell(5, $this->rheight2,$Sothers1,$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);

			$this->sety($yy2+1);
		$this->setx($x1+105);
			// $this->Line(132,89,160,89);

//end ----------------------------------r2--------------------------------------------------

$this->ln(16);

//-----------------------------------r3-----------------------------------------------

$this->setx($x1+10);
	$yy2 = $this->getY();
$this->Multicell(20, $this->rheight2+15,"Total Professional Fees (for accredited and non-accredited professionals)",$this->withborder,'C');


$this->sety($yy2);
$this->setx($x1+30);
		$this->Multicell(20, $this->rheight2+15,"",$this->withborder,'C');

 $this->sety($yy2);
$this->setx($x1+50);
		$this->Multicell(35, $this->rheight2+15,"",$this->withborder,'C');

 $this->sety($yy2);
$this->setx($x1+85);
		$this->Multicell(20, $this->rheight2+15,"",$this->withborder,'C');

$this->sety($yy2);
$this->setx($x1+105);
		$this->Multicell(50, $this->rheight2+15,"",$this->withborder,'C');

$this->sety($yy2+1);
$this->setx($x1+105);

	$this->Cell(10, $this->rheight2,"     Amount P",$this->withoutborder,'C');
	$this->Cell(13, $this->rheight2,"     Amount P",$this->withoutborder,'R');
$this->ln();

$this->sety($yy2+3.5);
$this->setx($x1+108);
$this->Cell(13, $this->rheight2,"Paid by (Check all that applies):",$this->withoutborder,'R');

// $this->sety($yy2+3.5);
// $this->setx($x1+120);
$this->Rect($x1+108, $yy2+6, $this->boxwidth+1.5, $this->boxheight+1.5);

$this->sety($yy2+6.5);
$this->setx($x1+112);
	$this->Cell(13, $this->rheight2,"Member/Patient",$this->withoutborder,'R');


$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->sety($yy2+6);
$this->setx($x1+109);
	$this->Cell(5, $this->rheight2,$Smember2,$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);

$this->Rect($x1+128, $yy2+6, $this->boxwidth+1.5, $this->boxheight+1.5);

$this->sety($yy2+6.5);
$this->setx($x1+132);
	$this->Cell(13, $this->rheight2,"HMO",$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->sety($yy2+6);
$this->setx($x1+129);
	$this->Cell(5, $this->rheight2,$Shmo2,$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);




//---------------------------------


$this->Rect($x1+108, $yy2+11, $this->boxwidth+1.5, $this->boxheight+1.5);

$this->sety($yy2+12);
$this->setx($x1+112);
	$this->Cell(13, $this->rheight2,"Others (i.e., PCSO, Promissory note, etc.)",$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->sety($yy2+11);
$this->setx($x1+109);
	$this->Cell(5, $this->rheight2,$Sothers2,$this->withoutborder,'R');

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);

			$this->sety($yy2+1);
		$this->setx($x1+105);
			// $this->Line(132,106,160,106);

//---------------------------------
//end ----------------------------------r3--------------------------------------------------
$this->ln(17);

	$this->Cell($this->GetStringWidth("b.) Purchase/Expenses")+$this->rheight7+7, $this->rheight2,"b.) Purchase/Expenses",$this->withoutborder, $this->continueline, $this->alignRight);

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label_normal);

	$this->Cell(5, $this->rheight2,"NOT",$this->withoutborder, $this->continueline, $this->alignRight);

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
	$this->Cell($this->GetStringWidth("  included in the Health Care Institution Charges"), $this->rheight2,"included in the Health Care Institution Charges",$this->withoutborder, $this->nextline, $this->alignRight);

	
$b1="Total cost of purchase/s for drugs/medicines and/or medical supplies bought by the patient/member within/outside the HCI during confinement";	
$yy3 = $this->getY();
$xx3 = $this->getX();	

$this->setx($x1+10);
	$this->Multicell(($this->GetStringWidth($b1)/2)+10, $this->rheight2+4,$b1,$this->withborder, $this->alignLeft);


$this->SetY($yy3);
$this->SetX($xx3+86.8);
$this->Cell(($this->GetStringWidth($b1)/2)+10, $this->rheight2+4,"",$this->withborder, $this->alignLeft);


$this->Rect($xx3+92, $yy3+.8, $this->boxwidth+1.5, $this->boxheight+1.5);

$this->SetY($yy3);
$this->SetX($xx3+93);

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->Cell(10, $this->rheight2+4,$none1,$this->withoutborder, $this->alignLeft);

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);


$this->SetY($yy3);
$this->SetX($xx3+97);
$this->Cell(10, $this->rheight2+4,"NONE",$this->withoutborder, $this->alignLeft);


$this->Rect($xx3+108, $yy3+.8, $this->boxwidth+1.5, $this->boxheight+1.5);
$this->SetY($yy3);
$this->SetX($xx3+112);
$this->Cell(10, $this->rheight2+4,"Total Amount",$this->withoutborder, $this->alignLeft);



$this->SetY($yy3);
$this->SetX($xx3+109);

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->Cell(10, $this->rheight2+4,$total_amount1,$this->withoutborder, $this->alignLeft);

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);



$this->SetY($yy3);
$this->SetX($xx3+125);
$this->Cell(10, $this->rheight2+4,"P",$this->withoutborder,$this->continueline, $this->alignLeft);
$this->Line(136,$yy3+4,160,$yy3+4);

$this->SetY($yy3);
$this->SetX($xx3+128);
$this->Cell(10, $this->rheight2+4,$total_value1,$this->withoutborder,$this->continueline, $this->alignLeft);


$this->ln();
//end-------------------------------------------------
//----------------------------------------------------------


$b2="Total cost of diagnostic/laboratory examinations paid for by the patient/member done within/outside the HCI during confinement";	
$yy3 = $this->getY();
$xx3 = $this->getX();	

$this->setx($x1+10);
	$this->Multicell(($this->GetStringWidth($b1)/2)+10, $this->rheight2+4,$b2,$this->withborder, $this->alignLeft);


$this->SetY($yy3);
$this->SetX($xx3+86.8);
$this->Cell(($this->GetStringWidth($b1)/2)+10, $this->rheight2+4,"",$this->withborder, $this->alignLeft);


$this->Rect($xx3+92, $yy3+.8, $this->boxwidth+1.5, $this->boxheight+1.5);

$this->SetY($yy3);
$this->SetX($xx3+93);

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->Cell(10, $this->rheight2+4,$none2,$this->withoutborder, $this->alignLeft);

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);


$this->SetY($yy3);
$this->SetX($xx3+97);
$this->Cell(10, $this->rheight2+4,"NONE",$this->withoutborder, $this->alignLeft);


$this->Rect($xx3+108, $yy3+.8, $this->boxwidth+1.5, $this->boxheight+1.5);
$this->SetY($yy3);
$this->SetX($xx3+112);
$this->Cell(10, $this->rheight2+4,"Total Amount",$this->withoutborder, $this->alignLeft);



$this->SetY($yy3);
$this->SetX($xx3+109);

$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_answer_check);

$this->Cell(10, $this->rheight2+4,$total_amount2,$this->withoutborder, $this->alignLeft);

$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);



$this->SetY($yy3);
$this->SetX($xx3+125);
$this->Cell(10, $this->rheight2+4,"P",$this->withoutborder,$this->continueline, $this->alignLeft);
$this->Line(136,$yy3+4,160,$yy3+4);

$this->SetY($yy3);
$this->SetX($xx3+128);
$this->Cell(10, $this->rheight2+4,$total_value2,$this->withoutborder,$this->nextline, $this->alignLeft);

//end


$string1 = "*NOTE:  Total Actual Charges should be based on Statement of Account (SoA)";
$this->Cell($this->GetStringWidth($string1)+$this->rheight7, $this->rheight2, $string1,$this->withoutborder, $this->continueline, $this->alignRight);

		
		
		//B. CONSENT TO ACCESS PATIENT RECORD/S
		$this->Ln($this->rheight2);
	 	$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($b[0]), $this->rheight7, $b[0],$this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($b[1]), $this->rheight7, $b[1],$this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($this->rheight6);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($b_ln1[0])+$this->rheight7, $this->rheight2, $b_ln1[0],$this->withoutborder, $this->nextline, $this->alignRight);
		$this->Cell($this->GetStringWidth($b_ln1[1])+$this->rheight7, $this->rheight2, $b_ln1[1],$this->withoutborder, $this->nextline, $this->alignRight);
		$this->Cell($this->GetStringWidth($b_ln1[2])+$this->rheight7, $this->rheight2, $b_ln1[2],$this->withoutborder, $this->nextline, $this->alignRight);
		//conforme label
		$this->Ln($this->rheight2);
		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold, $this->fontsize_label);
		$width =$this->GetStringWidth($conforme_label[0])+$ColWidthPart3[0]+$this->rheight7;
		$this->Cell($width, $this->rheight2, $conforme_label[0],$this->withoutborder, $this->nextline, $this->alignRight);
		//line for signature 
		$this->Cell($this->rheight4*4,'');
		$x=$this->GetX();
		$y=$this->GetY()+$this->rheight7;
		$width += $ColWidthPart3[1]+$ColWidthPart3[0];
		$this->Line($x,$y,$width,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label3);
		$this->Cell($width-$this->GetStringWidth($conforme_name), $this->rheight*2+$this->lineAdjustment*2, $conforme_name,$this->withoutborder, $this->continueline, $this->alignCenter);
		//signature sub text
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Ln($this->rheight7);
		$this->Cell($this->rheight4*4,'');
		$this->Cell($width+25, $this->rheight2, $conforme_label[1],$this->borderTop, $this->nextline, $this->alignLeft);
		//date signed
		$this->Ln($this->lineAdjustment*2);
		$this->Cell($ColWidthPart3[1]+$this->rheight2,'');
		$this->Cell($this->GetStringWidth($date_label[0])+$this->rheight2, $this->rheight2, $date_label[0],$this->withoutborder, $this->continueline, $this->alignLeft);

		$x = $this->GetX();
		$y = $this->GetY()-$this->lineAdjustment;
		$this->SetXY($x,$y);
		$date_val = empty($conforme_signed) ? '        ' : $conforme_signed;
		$this->addBlockDate($date_val);
		$this->Ln($this->rheight4+$this->lineAdjustment);
		//date format subtext $ColWidthPart3 = array(10,20,30,40,50,100);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
		$this->Cell($this->GetStringWidth($date_label[1])+$ColWidthPart3[3], $this->rheight2, $date_label[1],$this->withoutborder, $this->continueline, $this->alignRight);
		$this->Cell($this->GetStringWidth($date_label[2])+$ColWidthPart3[0]+$this->rheight3+$this->lineAdjustment, $this->rheight2, $date_label[2],$this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->GetStringWidth($date_label[3])+$ColWidthPart3[0]+$this->rheight2, $this->rheight2, $date_label[3],$this->withoutborder, $this->continueline, $this->alignCenter);

		$this->Ln($ColWidthPart3[0]);
		$this->SetX($ColWidthPart3[0]+$this->rheight2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$y1 = $this->GetY();
		$x1 = $ColWidthPart3[2]+$this->rheight;
		$this->Cell($this->lineAdjustment*2,'');
		$this->MultiCell($x1, $this->rheight, $relationship_label[0],$this->withoutborder, $this->alignLeft, 0, 1, '','', true, 0, false,true,0,'T',true);
		//draw box  is spouse
		$x1 += $ColWidthPart3[0]+$this->rheight2;
		$y1 -= $this->rheight2 - $this->lineAdjustment;
		$this->SetXY($x1, $y1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check is spouse
		$x1 -= $this->rheight6; 
		$this->SetXY($x1,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'spouse' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($ColWidthPart3[0], $this->rheight6, $relationship_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		//draw box is child
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check is child
		$x1 += $ColWidthPart3[0]+$this->rheight3+$this->lineAdjustment; 
		$this->SetXY($x1,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'child' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($ColWidthPart3[0], $this->rheight6, $relationship_label[3], $this->withoutborder, $this->continueline, $this->alignCenter);
		//draw box is parent
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check is parent
		$x1 += $ColWidthPart3[0]+$this->rheight3+$this->lineAdjustment; 
		$this->SetXY($x1,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'parent' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($ColWidthPart3[0], $this->rheight6, $relationship_label[4], $this->withoutborder, $this->continueline, $this->alignCenter);
		$xT = $this->GetX();//x y third column
		$yT = $this->GetY();
		//draw box is sibling
		$x1 -= $ColWidthPart3[1] +$this->lineAdjustment*2;
		$y1 += $this->rheight4 + $this->lineAdjustment*2;
		$this->SetXY($x1, $y1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check is sibling
		$x1 -= $this->rheight6; 
		$this->SetXY($x1,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'sibling' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($ColWidthPart3[0], $this->rheight6, $relationship_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		//draw box is others 
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check is others
		$x1 += $ColWidthPart3[0]+$this->rheight3+$this->lineAdjustment; 
		$this->SetXY($x1,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'other' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($this->GetStringWidth($relationship_label[5])+$this->rheight3, $this->rheight6, $relationship_label[5], $this->withoutborder, $this->continueline, $this->alignRight);
		$x = $this->GetX();
		$y= $this->GetY()+$this->rheight4+$this->lineAdjustment;
		$width = $ColWidthPart3[5]+$ColWidthPart3[0];
		$this->Line($x,$y, $width,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rel_other), $this->rheight+$this->lineAdjustment, $rel_other,$this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($ColWidthPart3[0]);
		$this->SetX($ColWidthPart3[0]+$this->rheight2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$y1 = $this->GetY();
		$x1 = $ColWidthPart3[2];
		$this->Cell($this->lineAdjustment*2,'');
		$this->MultiCell($x1, $this->rheight, $reasons_label[0],$this->withoutborder, $this->alignLeft, 0, 1, '','', true, 0, false,true,0,'T',true);
		//draw box  Patient is Incapacitated
		$x1 += $ColWidthPart3[0]+$this->rheight7;
		$y1 -= $this->rheight2 - $this->lineAdjustment;
		$this->SetXY($x1, $y1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check is Patient is Incapacitated
		$x1 -= $this->rheight6; 
		$this->SetXY($x1,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_reasons=='incapacitated'? '/': ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($ColWidthPart3[1]+$this->rheight6, $this->rheight6, $reasons_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		//draw box Other Reasons:
		$x1 += $this->rheight6;
		$y1 += $this->rheight4 + $this->lineAdjustment*2;
		$this->SetXY($x1, $y1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check Other Reasons:
		$x1 -= $this->rheight6; 
		$this->SetXY($x1,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_reasons=='other'? '/': ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($ColWidthPart3[1]-$this->rheight2, $this->rheight6, $reasons_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y= $this->GetY()+$this->rheight4+$this->lineAdjustment;
		$width = $ColWidthPart3[5]+$ColWidthPart3[0];
		$this->Line($x,$y, $width,$y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($reasons_other), $this->rheight+$this->lineAdjustment, $reasons_other,$this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$xT += $ColWidthPart3[1]+$this->rheight;
		$yT += $this->lineAdjustment*3;
		$this->SetXY($xT, $yT);
		$width = $ColWidthPart3[4]-$this->rheight4;
		$this->MultiCell($width, $this->rheight, $representative_label[0],$this->withoutborder, $this->alignLeft, 0, 1, '','', true, 0, false,true,0,'T',true);
		//thumbmark box
		$xT += $width;
		$yT -= $this->rheight3;
		$this->SetXY($xT, $yT);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$width = $ColWidthPart3[2];
		$height = $ColWidthPart3[1]+$this->rheight2;
		$this->Rect($x, $y, $width, $height);
		$this->SetLineWidth(0.2);
		//draw box patient 
		$xT -= ($ColWidthPart3[1]*2+$this->rheight);
		$yT += $ColWidthPart3[0]+$this->rheight;//; - $this->lineAdjustment;
		$this->SetXY($xT, $yT);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check patient
		$x1 -= $this->rheight5+$this->lineAdjustment; 
		$this->SetXY($xT,$yT);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $thumbmark_data=='patient' ? '/' :' ';
		$this->Cell($this->rheight, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($this->GetStringWidth($representative_label[1]), $this->rheight6, $representative_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		//draw box representative
		$xT += $ColWidthPart3[0]+$this->rheight7;
		//$yT += $ColWidthPart3[0]+$this->rheight;//; - $this->lineAdjustment;
		$this->SetXY($xT, $yT);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y+$this->inspace, $this->boxwidth+1, $this->boxheight+1);
		$this->SetLineWidth(0.2);
		//check representative
		$x1 -= $this->rheight5+$this->lineAdjustment; 
		$this->SetXY($xT,$yT);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $thumbmark_data=='representative' ? '/' :' ';
		$this->Cell($this->rheight, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX()+$this->lineAdjustment*1.5;
		$this->Cell($this->GetStringWidth($representative_label[2]), $this->rheight6, $representative_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($ColWidthPart3[0]);
	}

	function addPart4(){
		$part4 = 'PART IV - CERTIFICATION OF HEALTH CARE INSTITUTION';
		$certify_label = 'I certify that services rendered were recorded in the patient\'s chart and health care institution records and that the herein information given are true and correct.';
		$sub_text = array('Signature Over Printed Name of Authorized HCI Representative', 'Official Capacity / Designation', 'Date Signed:', 'month','day','year');

		$hci_rep_name = '';
		$official_capacity = '';
		$date_signed = '';

		$this->Ln($this->rheight7);
		$this->addTitleBar($part4, '');
		$ColWidthPart4 = array(165,13,50,20,15);

		$this->SetFont($this->fontfamily_label."bd", $this->fontstyle_label_bold_italicized, $this->fontsize_label5);
		$this->Ln($this->rheight4);
		$this->MultiCell($ColWidthPart4[0], $this->rheight, $certify_label,$this->withoutborder, $this->alignLeft, 0, 1, $ColWidthPart4[1],'', true, 0, false,true,0,'T',true);
		$this->Ln($ColWidthPart4[1]);
		$this->SetX($ColWidthPart4[1]);
		$width = $ColWidthPart4[2]+$ColWidthPart4[3];
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label5);
		$this->Cell($width+$this->rheight3-$this->GetStringWidth($hci_rep_name), $this->rheight2+$this->lineAdjustment, $hci_rep_name,$this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $ColWidthPart4[1] + $width-$this->rheight6;
		$this->SetX($x);
		$this->Cell($width+$ColWidthPart4[4]-$this->GetStringWidth($official_capacity), $this->rheight2+$this->lineAdjustment, $official_capacity,$this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $width*2+$ColWidthPart4[1]-$this->rheight7;
		$this->SetX($x);
		$this->Cell($this->GetStringWidth($sub_text[2])+$this->lineAdjustment*4, $this->rheight2+$this->lineAdjustment, $sub_text[2],$this->withoutborder, $this->continueline, $this->alignLeft);
		$date_value = empty($date_signed) ?'        ' : $date_signed;
		$this->addBlockDate($date_value);
		$this->Ln($this->rheight3+$this->lineAdjustment);
		//line for signature
		$this->SetX($ColWidthPart4[1]);
		$x = $this->GetX();
		$y= $this->GetY();//+$this->rheight4+$this->lineAdjustment;
		$this->Line($x,$y, $width,$y);
		//line for designation
		$x = $ColWidthPart4[1] + $width-$this->rheight;
		$this->SetX($x);
		$x = $this->GetX();
		$y= $this->GetY();
		$width2 = $width*2;   
		$this->Line($x,$y, $width2,$y);
		//sub text
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->SetX($ColWidthPart4[1]);
		$y=$this->GetY();
		$this->MultiCell($width-$ColWidthPart4[1]-$this->rheight*2, $this->rheight, $sub_text[0],$this->withoutborder, $this->alignCenter, 0, 1, $ColWidthPart4[4]+$this->rheight3,'', true, 0, false,true,0,'T',true);
		$x = $ColWidthPart4[1]+$width-$this->rheight4;
		$this->SetXY($x,$y);
		$this->Cell($width-$ColWidthPart4[1], $this->rheight2, $sub_text[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX()+$ColWidthPart4[3]+$this->rheight6+$this->lineAdjustment;
		$this->SetX($x);
		$this->Cell($this->GetStringWidth($subtext[3])+$ColWidthPart4[1]+$this->lineAdjustment, $this->rheight, $sub_text[3], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->GetStringWidth($subtext[4])+$ColWidthPart4[4], $this->rheight, $sub_text[4], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Cell($this->GetStringWidth($subtext[5]), $this->rheight, $sub_text[5], $this->withoutborder, $this->continueline, $this->alignLeft);
		
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

	function addName(){
         if (($this->GetY()<=21 && $this->GetY()>=5) && $this->PageNo()>1) {
             $this->SetTopMargin(20);
		     $addname = "Patient: ".$this->name_last.", ".$this->name_first." ".$this->name_middle." - (con't)";

		     $this->SetFont($this->fontfamily_answer, $this->fontstyle_label_italicized, $this->fontsize_answer);
		     $this->Cell($this->totwidth, $this->rheight, $addname, $this->withoutborder, $this->nextline, $this->alignLeft);
         }
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
class RVS {
	var $icd_code;
	var $rvs_code;
	var $operation_date;
	var $laterality;
}
class ProfessionalFee{
	var $accrediation_no;
	var $name;
	var $date_signed;
	var $is_co_pay;
	var $amt;
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

#added by Nick, 2/17/2014
#updated by Nick, 4/7/2014
#updated by Nick, 6/4/2014
class ICD_ICP {
    function addInfo(&$doAddInfo, &$infos, $row, $doAlign = true) {
        if ($doAddInfo) {
            array_push($infos, $row);
            $doAddInfo = false;
        } else {
            if ($doAlign) {
                array_push($infos, array());
            }
        }
    }

    function setCodesData($rows, $max_len, $doAlign = true) {
        $output = new StdClass;
        $sentences = array();
        $infos = array();
        $spAddedCodes = array();
        foreach ($rows as $rkey => $row) {
            if(in_array($row['ops_code'],$spAddedCodes)/* && $row['special_case'] == 1*/){
                $spAddedCodes[] = $row['description'];
            }else{
                $spAddedCodes[] = $row['ops_code'];
            }
            $index = 0;
            $line = '';
            $words = explode(' ', $row['description']);
            $count = count($words) - 1;
            $doAddInfo = true;
            foreach ($words as $wkey => $word) {
                if ((strlen($word) + strlen($line)) < $max_len) {
                    $line .= ' '.$word;
                } else {
                    if(isset($row['ops_code'])){
                        array_push($sentences, $line);
                    }else if(trim($row['description']) != ""){
                        array_push($sentences, $line);
                    }
                    $this->addInfo($doAddInfo, $infos, $row, $doAlign);
                    $line = $word;
                }
                if ($count <= $index) {
                    if(isset($row['ops_code'])){
                        array_push($sentences, $line);
                    }else if(trim($row['description']) != ""){
                        array_push($sentences, $line);
                    }
                    $this->addInfo($doAddInfo, $infos, $row, $doAlign);
                }
                $index++;
            }
        }

        $output->sentences = $sentences;
        $output->infos = $infos;
        return $output;
    }

    function getCf2Data($icdData, $rvsData) {
        $cf2 = array();
        foreach ($icdData->sentences as $skey => $sentence) {
            $cf2['icd_desc'][] = $sentence;
        }
        foreach ($icdData->infos as $ikey => $info) {
            $cf2['icd_code'][] = $info['code'];
        }
        foreach ($rvsData->sentences as $skey => $sentence) {
            $cf2['rvs_desc'][] = $sentence;
        }
        foreach ($rvsData->infos as $skey => $info) {
            $cf2['rvs_code'][] = $info['ops_code'];
            $cf2['rvs_date'][] = $info['op_date'];
            $cf2['rvs_late'][] = $info['laterality'];
        }
        return $cf2;
    }

    function divideArray(&$cf2, $divisor) {
        $cf2['icd_desc'] = array_chunk($cf2['icd_desc'], $divisor);
        $cf2['icd_code'] = array_chunk($cf2['icd_code'], $divisor);
        $cf2['rvs_desc'] = array_chunk($cf2['rvs_desc'], $divisor);
        $cf2['rvs_code'] = array_chunk($cf2['rvs_code'], $divisor);
        $cf2['rvs_date'] = array_chunk($cf2['rvs_date'], $divisor);
        $cf2['rvs_late'] = array_chunk($cf2['rvs_late'], $divisor);
    }

    //added by Nick 06-03-2014 - format array if it contains special procedures
    function formatSpecialProcedures(&$rows){
        $output = array();
        foreach ($rows as $key => $row) {
            if($row['special_case'] == 1){
                $specialDates = trim($row['special_dates'],',');
                $spDates = explode(',',$specialDates);
                foreach ($spDates as $key1 => $spdate) {
                    $new_row = array(
                        'ops_code' => $row['ops_code'],
                        'description' => $row['description'],
                        'laterality' => $row['laterality'],
                        'op_date' => $spdate,
                        'special_case' => $row['special_case']
                    );
                    array_push($output,$new_row);
                }
            }else{
                array_push($output,$row);
            }
        }
        $rows = $output;
    }

    //added by Nick 06-04-2014
    function getSpecialProcedureDates($enc){
        global $db;

        //$db->debug = true;

        $objPerson = new Person;
        //$pid = $objPerson->getPid($enc);
        $refno = $objPerson->getDialysisTransIdDone($enc);

        if($refno){
        	$enc_list = $objPerson->getEcounterListDialysis($refno);
        	$exlp = explode(",",$enc_list);
        	$encs = implode("','", $exlp);
        }

        $encslist =  $encs."','".$enc;

        $this->sql = "SELECT
                                      smod.ops_code,
                                      scrp.description,
                                      smod.laterality,
                                      smod.op_date,
                                      GROUP_CONCAT(DISTINCT
                                        TRIM(
                                          TRAILING ',' FROM
                                          IF(smod.special_dates IS NULL OR smod.special_dates = '',
                                            smod.op_date,
                                            smod.special_dates
                                          )
                                        ) SEPARATOR ','
                                      ) AS special_dates
                                    FROM
                                      seg_insurance_member_info AS sim
                                      LEFT JOIN care_person AS cp
                                        ON cp.pid = sim.pid
                                      INNER JOIN care_encounter AS ce
                                        ON ce.pid = sim.pid
                                      INNER JOIN seg_misc_ops AS smo
                                        ON smo.encounter_nr = ce.encounter_nr
                                      INNER JOIN seg_misc_ops_details AS smod
                                        ON smod.refno = smo.refno
                                      INNER JOIN seg_case_rate_packages AS scrp
                                        ON smod.ops_code = scrp.code
                    WHERE ce.encounter_nr IN ('$encslist') 
                                      AND scrp.description REGEXP ?
                                      AND scrp.special_case = 1
                                    GROUP BY smod.ops_code
                    ORDER BY scrp.description";
        $spLabels = array(
            'hemodialysis' => 'Hemodialysis',
            'peritoneal' => 'Peritoneal Dialysis',
            'linac' => 'Radiotherapy (LINAC)',
            'cobalt' => 'Radiotherapy (COBALT)',
            'blood_transfusion' => 'Blood transfusion',
            'brachytherapy' => 'Brachytherapy',
            'chemotherapy' => 'Chemotherapy',
            'debridment' => 'Debridement'
        );

        foreach ($spLabels as $key => $spLabel) {
            $temp = array();
            $params = array(
                $spLabel
            );
            $rs = $db->Execute($this->sql, $params);

            if ($rs) {
                if ($rs->RecordCount()) {
                    while ($row = $rs->FetchRow()) {

                        $dates = explode(',', trim($row['special_dates'], ','));
                        foreach ($dates as $key1 => $date) {
                            array_push($temp,date('m-d-Y',strtotime($date)));
                        }
                    }



                    switch($key){
                        case 'hemodialysis' : $hemodialysis = array_chunk($temp,4); break;
                        case 'peritoneal' : $peritoneal = array_chunk($temp,4); break;
                        case 'linac' : $linac = array_chunk($temp,4); break;
                        case 'cobalt' : $cobalt = array_chunk($temp,4); break;
                        case 'blood_transfusion' : $blood_transfusion = array_chunk($temp,4); break;
                        case 'brachytherapy' : $brachytherapy = array_chunk($temp,4); break;
                        case 'chemotherapy' : $chemotherapy = array_chunk($temp,4); break;
                        case 'debridment' : $debridment = array_chunk($temp,4); break;
                    }
                }
            }
        }



        $output = array(
            'hemodialysis' => $hemodialysis,
            'peritoneal' => $peritoneal,
            'linac' => $linac,
            'cobalt' => $cobalt,
            'blood_transfusion' => $blood_transfusion,
            'brachytherapy' => $brachytherapy,
            'chemotherapy' => $chemotherapy,
            'debridment' => $debridment,
        );
        return $output;
    }
}//end class


/*** GET ICD RVS ***/
 $enc_obj = new Encounter;
 $encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']);
$objBilling = new Billing();

$objPerson = new Person;
    //$pid = $objPerson->getPid($_GET['encounter_nr']);//Commented by Jarel 11/19/2104
    $refno = $objPerson->getDialysisTransIdDone($_GET['encounter_nr']); // Eddited By Jarel 11/19/2014

    if($refno){
    	$enc_list = $objPerson->getEcounterListDialysis($refno);
    	$exlp = explode(",",$enc_list);
    	$encs = implode("','", $exlp);
    	$encounter_ = $encs;
    }else{ //added by mai 11-18-2014
    	$encounter_ = $_GET['encounter_nr'];
    }

$icd = $objBilling->getIcd($encounter_ ,$encInfo['encounter_type']); //edited by mai 11-18-2014
$rvs = $objBilling->getRvs($encs, $_GET['encounter_nr']);
/*** RECONSTRUCT ICD RVS ***/
$objICD_ICP = new ICD_ICP;
$objICD_ICP->formatSpecialProcedures($rvs);
$icdData = $objICD_ICP->setCodesData($icd,30,true);
$rvsData = $objICD_ICP->setCodesData($rvs,30,true);
$cf2 = $objICD_ICP->getCf2Data($icdData,$rvsData);
$objICD_ICP->divideArray($cf2,16);

/*** GET SPECIAL DATES ***/
$spdates = $objICD_ICP->getSpecialProcedureDates($_GET['encounter_nr']);

/*** OUTPUT DATA ***/
$count = max(
        max(
            count($spdates['hemodialysis']),
            count($spdates['peritoneal']),
            count($spdates['linac']),
            count($spdates['cobalt']),
            count($spdates['blood_transfusion']),
            count($spdates['brachytherapy']),
            count($spdates['chemotherapy']),
            count($spdates['debridment'])
        ),
        count($cf2['icd_desc']),
        count($cf2['rvs_desc'])
    ) - 1;

$pdf = new PhilhealthForm2();
$pdf->Open();

for ($i = 0; $i <= $count; $i++) {
    $data = array(
        'encounter_nr' => $_GET['encounter_nr'],
        'icd_desc' => $cf2['icd_desc'][$i],
        'icd_code' => $cf2['icd_code'][$i],
        'rvs_desc' => $cf2['rvs_desc'][$i],
        'rvs_code' => $cf2['rvs_code'][$i],
        'rvs_date' => $cf2['rvs_date'][$i],
        'rvs_late' => $cf2['rvs_late'][$i],
        'spdates' => array(
            'hemodialysis' => $spdates['hemodialysis'][$i],
            'peritoneal' => $spdates['peritoneal'][$i],
            'linac' => $spdates['linac'][$i],
            'cobalt' => $spdates['cobalt'][$i],
            'blood_transfusion' => $spdates['blood_transfusion'][$i],
            'brachytherapy' => $spdates['brachytherapy'][$i],
            'chemotherapy' => $spdates['chemotherapy'][$i],
            'debridment' => $spdates['debridment'][$i]
        )
    );

    $pdf->SetMargins(10, 8, 10, true);
    $pdf->SetAutoPageBreak(TRUE, 5); // Added by James for footer Margins
    $pdf->AddPage();
     $pdf->pageBoarder();
    $pdf->addHeader();
    $pdf->setAllData($data);
    $pdf->addPart1and2();
    $pdf->addName();
}

$pdf->Output();
//end nick
