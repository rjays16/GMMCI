<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require_once($root_path . '/classes/tcpdf/config/lang/eng.php');
require($root_path . "/classes/fpdf/pdf.class.php");
require($root_path . '/classes/tcpdf/tcpdf.php');
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path . 'include/inc_date_format_functions.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

define('INFO_INDENT', 10);
define('HCARE_ID', 18);
define('HSM', 9); //Define Membership Category id for HOSPITAL SPONSORED MEMBER
define('SM', 5); //Define Membership Category id for SPONSORED MEMBER

class PhilhealthForm2Part3 extends TCPDF
{


	var $fontsize_label = 8;
	var $fontsize_label2 = 16;
	var $fontsize_label3 = 8;
	var $fontsize_label33 = 7;
	var $fontsize_label4 = 6;
	var $fontsize_label5 = 6.5;
	var $fontsize_answer = 10;
	var $fontsize_answer2 = 12;
	var $fontsize_answer_check = 11;
	var $fontsize_answer_check2 = 10;
	var $fontsize_answer_cert = 8.5;
	var $fontsize_answer_table = 9;

	var $fontstyle_label_bold = "";
	var $fontstyle_label_bold_italicized = "BI";
	var $fontstyle_label_italicized = "I";
	var $fontstyle_label_normal = '';
	var $fontstyle_answer = "B";

	var $fontfamily_label = "tahoma";
	var $fontfamily_sans = "dejavusans";
	var $fontfamily_answer = "freeserif";
	var $fontfamily_times = "times";

	var $totwidth = 200;
	var $rheight = 6;
	var $rheight2 = 2;
	var $rheight22 = 0;
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

	var $total_phic_hci;
	var $total_phic_doc;
	var $total_hci_charges;
	var $total_doc_charge;
	var $total_hci_discount;
	var $total_doc_discount;
	var $total_hci_coverage;
	var $total_doc_coverage;
	var $total_charge;
	var $total_discount;
	var $total_coverage;
	var $patient_name;
	var $memcategory_id;
	var $excess;
	var $bill_nr;
	var $bhousecase;
	var $is_discharged;
	var $total_meds;
	var $total_xlo;
	var $total_outside;
	var $bill_dte;
	var $charity;
	var $conforme_name; //added by mai 08-27-2014
	var $pid; //added by mai 09-25-2014

	function PhilhealthForm2Part3()
	{
		$pg_array = array('215.9', '350');
		$this->__construct('P', 'mm', $pg_array, true, 'UTF-8', false);
		$this->SetDrawColor(0, 0, 0);
		$this->SetMargins(8, 8, 1);
		//$this->SetAutoPageBreak(false, 1);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->setLanguageArray($l);
	}


	function writeBlockNumber($xcoord, $ycoord, $num, $insurance)
	{
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord - $this->lineAdjustment; //y-coordinate of block
		$number = $num;
		$len = $this->blockwidth * $number;

		$y1 = $y + 4;
		$y2 = $y + $this->blockheight / 2;
		$x1 = $x;
		$x2 = $x;
		$this->SetLineWidth(0.3);
		$this->SetX($x, $y);
		if ($number < 14) {
			$new_number = 14;
			for ($cnt = 0; $cnt < $new_number; $cnt++) {
				$this->SetLineWidth(0.3);
				if ($cnt != 2 && $cnt != 12) {
					$this->Line($x1, $y1 + $this->lineAdjustment, $x1, $y2 + $this->lineAdjustment);
					$this->Line($x, $y + $this->blockheight + $this->lineAdjustment, $x + $this->blockwidth, $y + $this->blockheight + $this->lineAdjustment);
					$x2 = $x1 + $this->blockwidth;
					$this->Line($x2, $y1 + $this->lineAdjustment, $x2, $y2 + $this->lineAdjustment);
					$this->Cell($this->blockwidth, $this->blockheight + 2, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				} else {
					#echo "for - "."<br>";
					$this->Line($x + $this->inspace * 1, $y + ($this->blockheight / 2), $x + $this->inspace * 3, $y + ($this->blockheight / 2));

					$this->Cell($this->blockwidth, $this->blockheight + 2, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				}
			}
		} else {
			for ($cnt = 0; $cnt < $number; $cnt++) {
				$this->SetLineWidth(0.3);
				if ($insurance[$cnt] != '-') {
					$this->Line($x1, $y1 + $this->lineAdjustment, $x1, $y2 + $this->lineAdjustment);
					$this->Line($x, $y + $this->blockheight + $this->lineAdjustment, $x + $this->blockwidth, $y + $this->blockheight + $this->lineAdjustment);
					$x2 = $x1 + $this->blockwidth;
					$this->Line($x2, $y1 + $this->lineAdjustment, $x2, $y2 + $this->lineAdjustment);
					$this->Cell($this->blockwidth, $this->blockheight + 2, $insurance[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				} else {
					#$x = $this->GetX();
					#$y = $this->GetY();
					$this->Line($x + $this->inspace * 2, $y + ($this->blockheight / 2), $x + $this->inspace * 3, $y + ($this->blockheight / 2));

					$this->Cell($this->blockwidth, $this->blockheight + 2, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					#$x1 = $this->GetX();
					$x1 += $this->blockwidth;
					$x += $this->blockwidth;
				}
			}
		}
		$this->SetLineWidth(0.2);
	}

	//type = 1 for month and day, type = 2 for year
	function writeBlockDate($xcoord, $ycoord, $num, $type)
	{
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$this->SetLineWidth(0.3);
		$len = $this->blockwidth * $number;
		$this->Line($x, $y + $this->blockheight + $this->lineAdjustment, $x + $len, $y + $this->blockheight + $this->lineAdjustment);
		$y2 = $y + $this->blockheight;
		$y3 = $y + $this->blockheight / 1.5;
		for ($cnt = 0; $cnt <= $number; $cnt++) {
			if ($type == 1) {
				$this->Line($x, $y2 + $this->lineAdjustment, $x, $y3);
				$x += $this->blockwidth;
			} else if ($type == 2) {
				$this->Line($x, $y2 + $this->lineAdjustment, $x, $y3);
				$x += $this->blockwidth;
			}
		}
		$this->SetLineWidth(0.2);
	}



	function writeBlock($xcoord, $ycoord, $num)
	{
		$x = $xcoord; //x-coordinate of block
		$y = $ycoord; //y-coordinate of block
		$number = $num;

		$this->SetLineWidth(0.3);
		$len = $this->blockwidth * $number;
		$this->Line($x, $y + $this->blockheight + $this->lineAdjustment, $x + $len, $y + $this->blockheight + $this->lineAdjustment);
		$y2 = $y + $this->blockheight;
		$y3 = $y + $this->blockheight / 1.5;
		for ($cnt = 0; $cnt <= $number; $cnt++) {
			$this->Line($x, $y2 + $this->lineAdjustment, $x, $y3);
			$x += $this->blockwidth;
		}
		$this->SetLineWidth(0.2);
	}

	function writeBlockTime($time, $atime)
	{
		$len = strlen($time);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for ($i = 0; $i < $len; $i++) {
			if ($i == 0 || $i == 3) {
				$x = $this->GetX();
				$y = $this->GetY();
				$this->writeBlock($x, $y, 2);
			}
			$this->Cell($this->blockwidth, $this->blockheight + 1, substr($time, $i, 1), $this->withoutborder, $this->continueline, $this->alignRight);
		}
		$x = $this->GetX() + $this->rheight3;
		$y = $this->GetY() + $this->rheight2;
		// $this->Rect($x,$y,$this->rheight2);
		$this->Rect($x - 2, $y - 2, $this->boxwidth + 1, $this->boxheight + 1);

		$x1 = $this->GetX() + $this->rheight2;
		$this->SetX($x1);
		$this->Cell($this->rheight2, $this->blockheight + 1, $atime == 'AM' ? '/' : ' ', $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetX($x);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->rheight * 2, $this->blockheight, 'AM', $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		$x = $this->GetX() + $thius->rheight7;
		// $this->Rect($x,$y,$this->rheight2);
		$this->Rect($x - 2, $y - 2, $this->boxwidth + 1, $this->boxheight + 1);

		$this->Cell($this->lineAdjustment, $this->blockheight + 1, $atime == 'PM' ? '/' : ' ', $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->rheight4 * 2, $this->blockheight, 'PM', $this->withoutborder, $this->continueline, $this->alignRight);
	}

	function addBlockDate($admit_date)
	{
		$admit_date_arr = str_split($admit_date);
		$admit_date_len = strlen($admit_date);

		$x = $this->GetX();
		$y = $this->GetY();


		for ($cnt = 0; $cnt < $admit_date_len; $cnt++) {
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
			if ($cnt < 4) {
				if ($cnt == 0 || $cnt == 2) {
					$this->writeBlockDate($x, $y, 2, 1);
					$this->Cell($this->blockwidth, $this->blockheight + 1, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
				} else {
					$this->Cell($this->blockwidth, $this->blockheight + 1, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
					$this->Line($x + $this->inspace * 2, $y + ($this->blockheight / 2), $x + $this->inspace * 3, $y + ($this->blockheight / 2));
					$this->Cell($this->blockwidth, $this->blockheight, "", $this->withoutborder, $this->continueline, $this->alignCenter);
					$x = $this->GetX();
					$y = $this->GetY();
				}
			} else if ($cnt > 3) {
				if ($cnt == 4) {
					$this->writeBlockDate($x, $y, 4, 2);
				}
				$this->Cell($this->blockwidth, $this->blockheight + 1, $admit_date_arr[$cnt], $this->withoutborder, $this->continueline, $this->alignCenter);
			}
		}
	}


	function addTitleBar($title, $subtitle)
	{
		$x = $this->GetX();
		$y = $this->GetY() - 2;
		//draw lines
		// $this->Line($x, $y, $x+$this->totwidth, $y);
		// $this->Line($x, $y+0.5, $x+$this->totwidth, $y+0.5);


		if ($subtitle != '') {
			$this->SetTextColor(255, 255, 255);
			$this->Rect($x, $y + 0.5, $this->totwidth, 7, 'DF', "",  array(FFF, FFF, FFF));
			$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_label_bold, $this->fontsize_label3 - 1);
			$x1 = $this->GetY() - 1;
			$this->SetY($x1);
			$this->Cell($this->totwidth, $this->rheight2, $title, $this->withoutborder, $this->nextline, $this->alignCenter);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italicized, $this->fontsize_label5);
			$this->Cell($this->totwidth, $this->rheight2, $subtitle, $this->withoutborder, $this->nextline, $this->alignCenter);
		} else {
			$this->SetTextColor(255, 255, 255);
			$this->Rect($x, $y + 0.5, $this->totwidth, 5.5, 'DF', "",  array(FFF, FFF, FFF));
			$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_label_bold, $this->fontsize_label3 - 1);
			$x1 = $this->GetY() - 1;
			$this->SetY($x1);
			$this->Cell($this->totwidth, $this->rheight2, $title, $this->withoutborder, $this->nextline, $this->alignCenter);
		}

		$this->SetTextColor(0, 0, 0);
		$this->Ln(1);
		$x = $this->GetX();
		$y = $this->GetY();
		//draw lines
		// $this->Line($x, $y, $x+$this->totwidth, $y);
		// $this->Line($x, $y+0.5, $x+$this->totwidth, $y+0.5);
	}



	function getBillingDetails()
	{
		global $db;


		$sql = "SELECT 
				  sbe.accommodation_type,
				  sbe.bill_dte,
				  ce.pid,
				  `fn_get_person_name` (ce.pid) AS name,
				  ce.`encounter_nr`,
				  sbe.`total_doc_charge`,
				  SUM(
				    IFNULL(sbc.total_msc_coverage, 0) +
					IFNULL(sbc.total_acc_coverage, 0) +
					IFNULL(sbc.total_med_coverage, 0) +
					IFNULL(sbc.total_srv_coverage, 0) +
					IFNULL(sbc.total_ops_coverage, 0)
				  ) AS total_services_coverage,
				  SUM(
				    IFNULL(sbc.`total_d1_coverage`, 0) + IFNULL(sbc.`total_d2_coverage`, 0) + IFNULL(sbc.`total_d3_coverage`, 0) + 
				    IFNULL(sbc.`total_d4_coverage`, 0)
				  ) AS total_doc_coverage,
				  SUM(
				    IFNULL(sbe.`total_acc_charge`, 0) + IFNULL(sbe.`total_med_charge`, 0) + 
				    IFNULL(sbe.`total_ops_charge`, 0) + IFNULL(sbe.`total_msc_charge`, 0) + IFNULL(sbe.`total_srv_charge`, 0) + 
				    IFNULL(sbe.`total_sup_charge`, 0)
				  ) AS total_hci_charge,
				  SUM(
				    IFNULL(sbd.`total_d1_discount`, 0) + IFNULL(sbd.`total_d2_discount`, 0) + IFNULL(sbd.`total_d3_discount`, 0) + 
				    IFNULL(sbd.`total_d4_discount`, 0)
				  ) AS total_doc_discount,
				  SUM(
				    IFNULL(sbd.total_acc_discount, 0) + IFNULL(sbd.total_msc_discount, 0) + IFNULL(sbd.total_med_discount, 0) + IFNULL(sbd.total_ops_discount, 0) + IFNULL(sbd.total_srv_discount, 0) + IFNULL(sbd.total_sup_discount, 0)
				  ) AS total_hci_discount,
				  (SELECT SUM(dr_claim) FROM seg_billing_pf a WHERE a.bill_nr = sbe.bill_nr AND a.hcare_id='18' 
				  	) as total_doc_coverage2,
					sem.memcategory_id,
					ce.is_discharged,
					ser.total_meds,
					ser.total_xlo,
				  (SELECT SUM(hci_amount) FROM seg_billing_caserate WHERE bill_nr = sbe.bill_nr ) as total_services_coverage2,
				  (SELECT SUM(pf_amount) FROM seg_billing_caserate WHERE bill_nr = sbe.bill_nr ) as total_doc_coverage2
				FROM
				  care_encounter ce 
				  INNER JOIN seg_billing_encounter sbe
				    ON sbe.`encounter_nr` = ce.`encounter_nr` 
				  LEFT JOIN seg_encounter_memcategory sem
				  	ON sem.encounter_nr = sbe.encounter_nr
				  INNER JOIN seg_billing_coverage sbc 
				    ON sbe.bill_nr = sbc.`bill_nr` AND sbc.hcare_id='18'
				  INNER JOIN seg_billingcomputed_discount sbd 
				    ON sbd.`bill_nr` = sbe.`bill_nr`
				  LEFT JOIN seg_encounter_reimbursed ser
				    ON ser.encounter_nr = ce.encounter_nr 
				WHERE sbe.`bill_nr` =" . $db->qstr($this->bill_nr);
		if ($result = $db->Execute($sql)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$row['total_doc_coverage'] = (($row['total_doc_coverage'] < $row['total_doc_coverage']) ? $row['total_doc_coverage'] : $row['total_doc_coverage']);
				$this->total_doc_charge = $row['total_doc_charge'];
				$this->total_doc_discount = $row['total_doc_discount'];
				$this->total_doc_coverage = $row['total_doc_coverage'];
				$this->total_hci_charge = $row['total_hci_charge'];
				$this->total_hci_discount = $row['total_hci_discount'];
				$this->total_hci_coverage = $row['total_services_coverage'];
				$this->patient_name = $row['name'];
				$this->total_charge = $row['total_doc_charge'] + $row['total_hci_charge'];
				$this->total_coverage = $row['total_doc_coverage'] + $row['total_services_coverage'];
				$this->total_discount = $row['total_doc_discount'] + $row['total_hci_discount'];
				$this->excess = $this->total_charge - $this->total_discount -  $this->total_coverage;
				$this->memcategory_id = $row['memcategory_id'];
				$this->is_discharged = $row['is_discharged'];
				$this->total_meds = (($row['total_meds']) ? $row['total_meds'] : 0);
				$this->total_xlo = (($row['total_xlo']) ? $row['total_xlo'] : 0);
				$this->total_outside = $this->total_meds + $this->total_xlo;
				$this->bill_dte = $row['bill_dte'];
				$this->charity = (($row['accommodation_type'] == '1') ? true : false);
				$this->pid = $row['pid'];
				$this->total_phic_hci = $row['total_services_coverage'];
				$this->total_phic_doc = $row['total_doc_coverage'];
			}
		}
		// var_dump($sql);
		// die;
	}

	/*added by mai 08-27-2014*/
	function getConformeName()
	{
		global $db;
		$sql = "SELECT 
				  CONCAT(
				    cp.member_fname,
				    ' ',
				    IFNULL(
				      CONCAT(SUBSTRING(cp.member_mname, 1, 1), '.'), 
				      ''
				    ), ' ', cp.member_lname
				  ) AS patient_name 
				FROM
				  seg_insurance_member_info cp  
				  WHERE cp.pid = " . $db->qstr($this->pid);

		if ($result = $db->Execute($sql)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$this->conforme_name = $row['patient_name'];
			}
		}
	}
	/*end added by mai*/

	function isHouseCase()
	{
		global $db;

		$housecase = true;
		$strSQL = "select fn_isHouseCase('" . $this->encounter_nr . "') as casetype";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					$housecase = is_null($row["casetype"]) ? true : ($row["casetype"] == 1);
				}
			}
		}

		$this->bhousecase = $housecase;
	}



	function getTotalAppliedDiscounts()
	{
		global $db;

		$sql = "SELECT SUM(discount) AS total_discount FROM seg_billingapplied_discount 
                WHERE encounter_nr = " . $db->qstr($this->encounter_nr);

		$rs = $db->Execute($sql);
		if ($rs) {
			if ($rs->RecordCount() > 0) {
				$row = $rs->FetchRow();
				return $row['total_discount'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	function get_doctor_info()
	{
		global $db;
		$strSQL = "SELECT sbe.`bill_dte`, CONCAT('DR. ', fn_get_personell_firstname_last(sbp.`dr_nr`)) as doc_name, sbp.`dr_charge`, sbp.`dr_claim`, sbp.`role_area`,
				(SELECT accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = sbp.dr_nr and sda.hcare_id = '" . HCARE_ID . "') as acc_no
				FROM seg_billing_encounter AS sbe
				INNER JOIN seg_billing_pf AS sbp ON sbe.`bill_nr` = sbp.`bill_nr` AND sbp.hcare_id = '" . HCARE_ID . "' 
				WHERE sbe.is_final = '1' AND sbe.is_deleted IS NULL
				AND sbe.`encounter_nr` = " . $db->qstr($this->encounter_nr);
		//echo($strSQL);
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	/**
	 * Created By Jarel
	 * Created On 02/20/2014
	 * Get the default Doctor's if House Case
	 * @param string role
	 * @return result
	 **/
	function getHouseCaseDoctor($role)
	{
		global $db;

		switch ($role) {
			case 'D1':
			case 'D2':
				$filter = "cpr.is_housecase_attdr = 1";
				break;
			case 'D3':
				$filter = "cpr.is_housecase_surgeon = 1";
				break;
			case 'D4':
				$filter = "cpr.is_housecase_anesth = 1";
		}

		$strSQL = "SELECT fn_get_personell_lastname_first2(cpr.nr) as doc_name,\n
				  (SELECT accreditation_nr FROM seg_dr_accreditation AS sda WHERE sda.dr_nr = cpr.nr AND sda.hcare_id = '" . HCARE_ID . "') AS acc_no \n
				  FROM care_personell cpr 
				  WHERE $filter";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return $result;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Created By Jarel
	 * Created On 03/07/2014
	 * Get Calculate Date Excluding Weekends
	 * @param string bill_dte
	 * @return date
	 **/
	function getCalculateDate($bill_dte)
	{
		$bill_dte = date('Y-m-d', strtotime($bill_dte));
		#commented by Genz
		//$numberofdays = 10;

		$date_orig = new DateTime($bill_dte);

		$t = $date_orig->format("U"); //get timestamp

		#commented by Genz
		// // loop for X days
		// for($i=0; $i<$numberofdays ; $i++){

		//     // add 1 day to timestamp
		//     $addDay = 86400;

		//     // get what day it is next day
		//     $nextDay = date('w', ($t+$addDay));

		//     // if it's Saturday or Sunday get $i-1
		//     if($nextDay == 0 || $nextDay == 6) {
		//         $i--;
		//     }

		//     // modify timestamp, add 1 day
		//     $t = $t+$addDay;
		// }

		return date('mdY', ($t));
	}



	/**
	 * Created By Jarel
	 * Created On 02/20/2014
	 * Look up if patient avail Medical And Surgical Case
	 * @return boolean
	 **/
	function isDiffCase()
	{
		global $db;
		$first_type = '';
		$second_type = '';
		$strSQL = " SELECT p.case_type, sc.rate_type
				    FROM seg_billing_caserate sc 
				    INNER JOIN seg_case_rate_packages p 
				   		ON p.`code` = sc.`package_id`
				    WHERE bill_nr = " . $db->qstr($this->bill_nr);

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if ($row['rate_type'] == 1)
						$first_type = $row['case_type'];
					else
						$second_type = $row['case_type'];
				}
			}
		}

		if ($first_type != $second_type && $second_type != '') {
			return true;
		} else {
			return false;
		}
	}


	function addAccrediationNo($text)
	{
		$text_len = strlen($text);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer);
		for ($i = 0; $i < $text_len; $i++) {
			if ($text[$i] != '-') {
				$x = $this->GetX();
				$y = $this->GetY();
				$this->writeBlock($x, $y, 1);
			}
			$this->Cell($this->blockwidth, $this->blockheight + 1, $text[$i], $this->withoutborder, $this->continueline, $this->alignCenter);
		}
	}


	function addProfessionalFees($doctors)
	{
		$prof_fee_label = '10. Professional Fees / Charges (use additional sheet if necessary):';
		$col_header = array(
			'Accreditation Number/Name of Accredited Health Care Professional/Date Signed and Professional Fees/Charges:',
			'Details'
		);
		$prof_labels = array('Accreditation No.:', 'Date Signed:', 'No Co-pay on top of PhilHealth Benefit', 'with Co-pay on top of PhilHealth Benefit', 'P');
		$prof_sub_text = array('Signature Over Printed Name', 'month', 'day', 'year');

		$this->isHouseCase();
		$ColWidthProf = array(100, 50, 25, 15, 10, 5);

		$this->SetFont($this->fontfamily_label . "bd", $this->fontstyle_label_bold, $this->fontsize_label3);
		$this->Cell($ColWidthBen[0], $this->rheight2, $prof_fee_label, $this->withoutborder, $this->nextline, $this->alignLeft);
		//horizontal line
		$x = $this->GetX();
		$y = $this->GetY() + $this->lineAdjustment;
		$this->Line($x, $y, $x + $this->totwidth, $y);
		$this->Line($x + 95, $y, $x + 95, 98);
		$this->Ln($this->rheight2);


		$this->SetXY($x + 5, $y + 2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(0, 0, "Accreditation number/Name of Accredited Health Care Professional/Date Signed", $this->withoutborder, $this->continueline, $this->alignLeft);

		$this->SetXY($x + 140, $y + 2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$this->Cell(0, 0, "Details", $this->withoutborder, $this->continueline, $this->alignLeft);
		//column header
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$x1 = (100 - $this->GetStringWidth($col_header[0])) / 2;
		$this->Cell($x1, $this->rheight2, '', $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($this->GetStringWidth($col_header[0]), $this->rheight2, $col_header[0], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Cell($x1, $this->rheight2, '', $this->withoutborder, $this->continueline, $this->alignCenter);
		//vertical line (at the center)
		$x = $this->GetX();
		$y = $this->GetY() - $this->lineAdjustment * 3;
		$height = $y + $ColWidthProf[1] * 2.5 - $this->rheight3;
		$this->Line($x, $y, $x, $height - 41);
		//column header
		$this->Cell($ColWidthProf[0] - $this->GetStringWidth($col_header[1]), $this->rheight2, $col_header[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->Ln($this->rheight2);

		$applied_discount = $this->getTotalAppliedDiscounts();

		//added by Nick, 5-5-2014
		if (!is_array($doctors)) {
			$this->Ln(3);
		}

		//modified by Nick 5-5-2014
		foreach ($doctors as $dkey => $row) {
			$cnt++;
			if ($cnt != 0) {
				$this->Ln(3);
			}

			/*if($this->bhousecase){
			 		if (!$this->isDiffCase() && $row['role_area']=='D1'){
			 			$result2 = $this->getHouseCaseDoctor('D3');
			 		}else{
			 			$result2 = $this->getHouseCaseDoctor($row['role_area']);
			 		}
			 		
			 		if ($result2) {
			 			while($row2=$result2->FetchRow()){
			 				$acc_no = $row2['acc_no'];
			 				$doc_name = $row2['doc_name'];
			 			}
			 		}

			 	}else{*/
			$acc_no = $row['acc_no'];
			$doc_name = $row['doc_name'];
			//}
			//horizontal line
			$x = $this->GetX();
			$y = $this->GetY();
			$this->Line($x, $y, $x + $this->totwidth, $y);
			//accrediation label
			$this->Ln(1);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($x + $this->GetStringWidth($prof_labels[0]) + $this->rheight2, $this->rheight2, $prof_labels[0], $this->withoutborder, $this->continueline, $this->alignCenter);
			$acc_num = $acc_no ? $acc_no : '    -       - ';
			$this->addAccrediationNo($acc_num);
			$this->Ln($this->rheight);

			//line for signature 
			$this->Cell((($this->rheight4 * 4) + 25), '');
			$x = $this->GetX();
			$y = $this->GetY() + $this->rheight7;
			$width = $ColWidthProf[1] + $ColWidthProf[2] + $ColWidthProf[4];
			$this->Line($x - 25, $y, $width, $y);
			//$prof_name = $lenProf > 0 ? $prof_fee_arr[$i][1] : '';
			$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_answer, $this->fontsize_label3 - 1);
			// $this->Cell($width-$this->GetStringWidth($doc_name)-20, $this->rheight*2, mb_convert_encoding(strtoupper(trim($doc_name)), 'UTF-8'),$this->withoutborder, $this->continueline, $this->alignCenter);
			$this->Cell(10, $this->rheight * 2, mb_convert_encoding(strtoupper(trim($doc_name)), 'UTF-8'), $this->withoutborder, $this->continueline, $this->alignCenter);
			//signature sub text
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Ln($this->rheight7);
			$this->Cell($this->rheight6 * 3, '');
			$this->Cell($width - $this->GetStringWidth($prof_sub_text[0]), $this->rheight2, $prof_sub_text[0], $this->withoutborder, $this->nextline, $this->alignCenter);
			$this->Ln($this->rheight2);
			$this->Cell($this->rheight6 * 3, '');
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($width - $this->GetStringWidth($prof_labels[1]), $this->rheight2, $prof_labels[1], $this->withoutborder, $this->nextline, $this->alignLeft);
			$x = $this->GetX() + $ColWidthProf[2] + $ColWidthProf[4] - $this->lineAdjustment * 3;
			$y = $this->GetY() - $this->rheight4;
			$this->SetXY($x, $y);

			$datenow = $this->getCalculateDate($this->bill_dte);
			$datenow  = $datenow && $this->bhousecase ? $datenow  : '        ';
			$this->addBlockDate($datenow);
			//date sub text
			$y += $this->rheight4 + $this->lineAdjustment;
			$this->SetXY($x, $y);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
			$this->Cell($this->GetStringWidth($prof_sub_text[1]) + $this->rheight3, $this->rheight2, $prof_sub_text[1], $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->Cell($this->GetStringWidth($prof_sub_text[2]) + $this->rheight7, $this->rheight2, $prof_sub_text[2], $this->withoutborder, $this->continueline, $this->alignRight);
			$this->Cell($this->GetStringWidth($prof_sub_text[3]) + $this->rheight6 * 2 + $this->lineAdjustment, $this->rheight2, $prof_sub_text[3], $this->withoutborder, $this->continueline, $this->alignRight);
			$yLast = $this->GetY();
			$isCoPay =  $lenProf > 0 ? $prof_fee_arr[$i][3] : false;
			//no co pay 
			$doc_discount = $row['dr_charge'] * $applied_discount;
			$copay_amount = $row['dr_charge'] - $doc_discount - $row['dr_claim'];
			$check_value = '';
			$check_value1 = '';
			if ($copay_amount <= 0) {
				$check_value = ' / ';
				$copay_amount = '';
			} else {
				$check_value1 = ' / ';
				$copay_amount = number_format($copay_amount, 2, '.', ',');
			}
			// echo $copay_amount; die();
			$x1 = $this->GetX() + $ColWidthProf[3] * 2 + $this->rheight4 * 2;
			$y1 = $this->GetY() - $ColWidthProf[4] - $this->rheight6;
			$this->SetXY($x1, $y1);
			$x = $this->GetX();
			$y = $this->GetY();
			$width = $this->blockwidth + $this->lineAdjustment * 1.75;
			$height = $this->blockheight + $this->lineAdjustment;
			$this->SetLineWidth(0.3);
			$this->Rect($x, $y + $this->inspace, $width, $height);
			$this->SetLineWidth(0.2);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);

			$this->Cell($this->rheight6, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($width - $this->GetStringWidth($prof_labels[2]), $this->rheight7, $prof_labels[2], $this->withoutborder, $this->nextline, $this->alignLeft);
			//with co pay
			$y1 += $this->rheight6;
			$this->SetXY($x1, $y1);
			$x = $this->GetX();
			$y = $this->GetY();
			$width = $this->blockwidth + $this->lineAdjustment * 1.75;
			$height = $this->blockheight + $this->lineAdjustment;
			$this->SetLineWidth(0.3);
			$this->Rect($x, $y + $this->inspace, $width, $height);
			$this->SetLineWidth(0.2);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);

			$this->Cell($this->rheight6, $this->rheight7, $check_value1, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($this->GetStringWidth($prof_labels[3]), $this->rheight7, $prof_labels[3], $this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->rheight7, $this->rheight7, $prof_labels[4], $this->withoutborder, $this->continueline, $this->alignRight);
			//line
			$x = $this->GetX();
			$y = $this->GetY() + $this->rheight + $this->lineAdjustment;
			$width = $ColWidthProf[0] * 2;
			$this->Line($x, $y, $width, $y);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_table);
			$x -= $this->lineAdjustment * 2;
			$y -= 7;
			$this->SetXY($x, $y);
			$amt_val = $lenProf > 0 && $isCoPay == true ? $prof_fee_arr[$i][3] : '';
			$this->Cell($ColWidthProf[2] + $this->rheight, $this->rheight * 2, $copay_amount, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetY($yLast);
		}


		for ($i = $cnt; $i < 3; $i++) {
			if ($i != 0) {
				$this->Ln(3);
			}
			//horizontal line
			$x = $this->GetX();
			$y = $this->GetY();
			$this->Line($x, $y, $x + $this->totwidth, $y);
			//accrediation label
			$this->Ln(1);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($x + $this->GetStringWidth($prof_labels[0]) + $this->rheight2, $this->rheight2, $prof_labels[0], $this->withoutborder, $this->continueline, $this->alignRight);
			$acc_num = $lenProf > 0 ? $prof_fee_arr[$i][0] : '    -       - ';
			$this->addAccrediationNo($acc_num);
			$this->Ln($this->rheight);

			//line for signature 
			$this->Cell($this->rheight4 * 4, '');
			$x = $this->GetX();
			$y = $this->GetY() + $this->rheight7;
			$width = $ColWidthProf[1] + $ColWidthProf[2] + $ColWidthProf[4];
			$this->Line($x, $y, $width, $y);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$prof_name = $lenProf > 0 ? $prof_fee_arr[$i][1] : '';
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label3);
			$this->Cell($width - $this->GetStringWidth($prof_name), $this->rheight * 2, $prof_name, $this->withoutborder, $this->continueline, $this->alignCenter);
			//signature sub text
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Ln($this->rheight7);
			$this->Cell($this->rheight6 * 3, '');
			$this->Cell($width - $this->GetStringWidth($prof_sub_text[0]), $this->rheight2, $prof_sub_text[0], $this->withoutborder, $this->nextline, $this->alignCenter);
			$this->Ln($this->rheight2);
			$this->Cell($this->rheight6 * 3, '');
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($width - $this->GetStringWidth($prof_labels[1]), $this->rheight2, $prof_labels[1], $this->withoutborder, $this->nextline, $this->alignLeft);
			$x = $this->GetX() + $ColWidthProf[2] + $ColWidthProf[4] - $this->lineAdjustment * 3;
			$y = $this->GetY() - $this->rheight4;
			$this->SetXY($x, $y);
			$date_val = $lenProf > 0 ? $prof_fee_arr[$i][2] : '        ';
			$this->addBlockDate($date_val);
			//date sub text
			$y += $this->rheight4 + $this->lineAdjustment;
			$this->SetXY($x, $y);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);
			$this->Cell($this->GetStringWidth($prof_sub_text[1]) + $this->rheight3, $this->rheight2, $prof_sub_text[1], $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->Cell($this->GetStringWidth($prof_sub_text[2]) + $this->rheight7, $this->rheight2, $prof_sub_text[2], $this->withoutborder, $this->continueline, $this->alignRight);
			$this->Cell($this->GetStringWidth($prof_sub_text[3]) + $this->rheight6 * 2 + $this->lineAdjustment, $this->rheight2, $prof_sub_text[3], $this->withoutborder, $this->continueline, $this->alignRight);
			$yLast = $this->GetY();
			$isCoPay =  $lenProf > 0 ? $prof_fee_arr[$i][3] : false;
			//no co pay 
			$x1 = $this->GetX() + $ColWidthProf[3] * 2 + $this->rheight4 * 2;
			$y1 = $this->GetY() - $ColWidthProf[4] - $this->rheight6;
			$this->SetXY($x1, $y1);
			$x = $this->GetX();
			$y = $this->GetY();
			$width = $this->blockwidth + $this->lineAdjustment * 1.75;
			$height = $this->blockheight + $this->lineAdjustment;
			$this->SetLineWidth(0.3);
			$this->Rect($x, $y + $this->inspace, $width, $height);
			$this->SetLineWidth(0.2);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
			$check_value = $isCoPay == false && $lenProf > 0 ? '/' : '';
			$this->Cell($this->rheight6, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($width - $this->GetStringWidth($prof_labels[2]), $this->rheight7, $prof_labels[2], $this->withoutborder, $this->nextline, $this->alignLeft);
			//with co pay
			$y1 += $this->rheight6;
			$this->SetXY($x1, $y1);
			$x = $this->GetX();
			$y = $this->GetY();
			$width = $this->blockwidth + $this->lineAdjustment * 1.75;
			$height = $this->blockheight + $this->lineAdjustment;
			$this->SetLineWidth(0.3);
			$this->Rect($x, $y + $this->inspace, $width, $height);
			$this->SetLineWidth(0.2);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
			$check_value = $isCoPay == true ? '/' : '';
			$this->Cell($this->rheight6, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
			$this->Cell($this->GetStringWidth($prof_labels[3]), $this->rheight7, $prof_labels[3], $this->withoutborder, $this->continueline, $this->alignLeft);
			$this->Cell($this->rheight7, $this->rheight7, $prof_labels[4], $this->withoutborder, $this->continueline, $this->alignRight);
			//line
			$x = $this->GetX();
			$y = $this->GetY() + $this->rheight + $this->lineAdjustment;
			$width = $ColWidthProf[0] * 2;
			$this->Line($x, $y, $width, $y);
			$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_table);
			$x -= $this->lineAdjustment * 2;
			$y -= 1;
			$this->SetXY($x, $y);
			$amt_val = $lenProf > 0 && $isCoPay == true ? $prof_fee_arr[$i][3] : '';
			$this->Cell($ColWidthProf[2] + $this->rheight, $this->rheight * 2, $amt_val, $this->withoutborder, $this->continueline, $this->alignCenter);
			$this->SetY($yLast);
		} //end for loop accrediation no

		$this->Ln(3);
	}

	function addPart3()
	{

		$part3 = 'PART III - CERTIFICATION OF CONSUMPTION OF BENEFITS AND CONSENT TO ACCESS PATIENT RECORD/S';
		$part3_sub = 'NOTE: Member should sign only after the applicable charges have been filled-out';
		$a = array('A.', ' CERTIFICATION OF CONSUMPTION OF BENEFITS');
		$a_ln1 = "PhilHealth benefit is enough to cover HCI and PF charges.";
		$a_ln2 = "No purchase of drugs/medicine,supplies,diagnostics,and co-pay for professional fees by the member/patient.";
		$a_ln3 = 'No outside purchases of drugs/medicines,supplies,diagnostics, and co-pay for professional fees from member/patient.';
		$a_ln4 = 'PhilHealth benefit is enough to cover facility and PF charges.';
		$a_ln5 = 'The benefits of the member/patient was completely used up prior to co-pay OR the benefit of the member/patient is not completely consumed BUT with purchase/expenses for drugs/medicines,supplies,diagnostics and others. ';
		$a_ln6 = 'The total co-pay for the following is/are:';
		$a_co_pay = array('HCI changes', 'Outside purchase/s for drugs/medicines and/or medical supplies not paid for by the HCI', 'Cost of diagnostic/laboratory examinations done outside not paid for by the HCI', 'Total Co-pay for Professional Fee/s (including non-accredited health care professionals)', 'TOTAL CO-PAY');
		$a_ln11 = array('P', 'None');
		$b = array('B.', ' CONSENT TO ACCESS PATIENT RECORD/S');
		$b_ln1 = array('hereby consent to the submission and examination of the patient\'s pertinent medical records for the purpose of verifying the veracity of this claim to effect ', 'efficient processing of benefit payment', 'I hereby hold PhilHealth or any of its officers, employees and/or representatives free from any and all legal liabilities relative to the herein-mentioned consent', 'which I have voluntarily and willingly given in connection with this claim for reimbursement before PhilHealth.');
		$conforme_label = array('', 'Signature Over Printed Name of Member/Authorized Representative');
		$date_label = array('Date Signed:', 'month', 'day', 'year');
		$relationship_label = array('Relationship of the representative to the patient:', 'Spouse', 'Sibling', 'Child', 'Parent', 'Others, Specify');
		$reasons_label = array('Reasons for signing on behalf of the patient:', 'Patient is Incapacitated', 'Other Reasons:');
		$representative_label = array('If patient/representative is unable to write, put right thumbmark. Patient/ representative should be assisted by an HCI representative. Check the appropriate box:', 'Patient', 'Representative');


		$soa_amt = ''; //soa amount variable 
		$is_no_outside_purchases = ''; //check box if true put '/'
		$is_benefit_of_member = ''; //check box if true put '/'
		$hci_amt = 0;
		$out_purchases_amt = 0;
		$cost_exam_amt = 0;
		$prof_fee_amt = 0;
		$conforme_name = ''; //$this->conforme_name;
		$conforme_signed = '';
		$conforme_relationship = ''; // either 'spouse', 'child', 'parent', 'sibling', 'other'
		$rel_other = ''; //relationship others specify 
		$conforme_reasons = '';  //either 'incapacitated' or 'other'
		$reasons_other = ''; //reasons others specify
		$thumbmark_data = ''; // either 'patient' or 'representative'




		$Smember1 = "";
		$Smember2 = "";
		$Shmo1 = "";
		$Shmo2 = "";
		$Sothers1 = "";
		$Sothers2 = "";


		$none1 = "";
		$none2 = "";
		$total_amount1 = "";
		$total_amount2 = "";
		$total_value1 = "";
		$total_value2 = "";




		$ColWidthPart3 = array(10, 20, 30, 40, 50, 100);

		$this->Ln($this->rheight7 - 3);
		$this->addTitleBar($part3, $part3_sub);
		//A. CERTIFICATION OF CONSUMPTION OF BENEFITS
		//$this->Ln($this->inspace);

		$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_label_bold, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($a[0]), $this->rheight7, $a[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_label_bold_italicized, $this->fontsize_label3);
		$this->Cell($this->GetStringWidth($a[1]), $this->rheight7, $a[1], $this->withoutborder, $this->nextline, $this->alignLeft);

		$x1 = $this->GetX() + $this->rheight6;
		$y1 = $this->GetY() + $this->lineAdjustment - 3;
		$this->SetLineWidth(0.3);
		$this->Rect($x1 + 1, $y1 + $this->inspace + 2, $this->boxwidth + 1, $this->boxheight + 1);
		$this->SetLineWidth(0.2);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);

		if (($this->excess <= 0 || (($this->memcategory_id == HSM || $this->memcategory_id == SM) && $this->charity)) && ($this->total_outside <= 0)) {
			$check_value = '/';
			$check_value2 = '';
			$total_hci = number_format($this->total_hci_charge, 2, '.', ',');
			$total_doc = number_format($this->total_doc_charge, 2, '.', ',');
			$total_grand = number_format($this->total_hci_charge + $this->total_doc_charge, 2, '.', ',');
		} else {
			$check_value = '';
			$check_value2 = '/';
			$total_hci2 = number_format($this->total_hci_charge, 2, '.', ',');
			$total_doc2 = number_format($this->total_doc_charge, 2, '.', ',');
			$total_hci_coverage = number_format($this->total_phic_hci, 2, '.', ',');
			// var_dump($this->total_phic_hci);
			// die;
			$total_doc_coverage = number_format($this->total_phic_doc, 2, '.', ',');
			$total_hci_excess = number_format($this->total_hci_charge - ($this->total_hci_discount + $this->total_phic_hci), 2, '.', ',');
			$total_doc_excess = number_format($this->total_doc_charge - ($this->total_doc_discount + $this->total_phic_doc), 2, '.', ',');
			if ($this->total_hci_discount != 0)
				$discount_hci = number_format($this->total_hci_charge - $this->total_hci_discount, 2, '.', ',');
			if ($this->total_doc_discount != 0)
				$discount_doc = number_format($this->total_doc_charge - $this->total_doc_discount, 2, '.', ',');
		}
		$this->SetXY($x1, $y1 + $this->inspace + 1);
		$this->Cell($this->rheight6, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignCenter);
		$this->SetXY($x1 - 6, $y1 + $this->inspace + 1);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal - 1, $this->fontsize_label3 - .5);
		$this->Cell($this->GetStringWidth($a_ln1) + $this->rheight7 + 8, $this->rheight2, $a_ln1, $this->withoutborder, $this->nextline, $this->alignRight);
		$this->Cell($this->GetStringWidth($a_ln2) + $this->rheight7 + 8, $this->rheight2, $a_ln2, $this->withoutborder, $this->nextline, $this->alignRight);

		$this->Ln(1);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal - 1, $this->fontsize_label_normal);
		$x2 = $this->setx($x1 + 15);
		$this->Cell(80, $this->rheight2, "", $this->withborder, $this->continueline, $this->alignRight);
		$this->Cell(80, $this->rheight2, "Total Actual Charges*", $this->withborder, $this->nextline, $this->alignCenter);


		$x2 = $this->setx($x1 + 15);
		$this->Cell(80, $this->rheight2, "Total Health Care Institution Fees", $this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell(80, $this->rheight2, $total_hci, $this->withborder, $this->nextline, $this->alignCenter);


		$x2 = $this->setx($x1 + 15);
		$this->Cell(80, $this->rheight2, "Total Professional Fees", $this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell(80, $this->rheight2, $total_doc, $this->withborder, $this->nextline, $this->alignCenter);



		$x2 = $this->setx($x1 + 15);
		$this->Cell(80, $this->rheight2, "Grand Total", $this->withborder, $this->continueline, $this->alignLeft);
		$this->Cell(80, $this->rheight2, $total_grand, $this->withborder, $this->nextline, $this->alignCenter);

		$this->ln(3);
		$x1 = $this->GetX() + $this->rheight6;
		$y1 = $this->GetY() + $this->lineAdjustment - 3;
		$this->SetLineWidth(0.3);
		$this->Rect($x1, $y1 + $this->inspace + 1, $this->boxwidth + 1.5, $this->boxheight + 1.5);
		$this->SetLineWidth(0.2);

		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$this->SetXY($x1 - 1, $y1 + $this->inspace);
		$this->Cell($this->rheight6, $this->rheight7, $check_value2, $this->withoutborder, $this->continueline, $this->alignCenter);

		$this->setx($x1 + 6);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal - 1, $this->fontsize_label3 - .5);

		$this->Multicell(180, $this->rheight2, $a_ln5, $this->withoutborder, $this->alignLeft);

		$this->Cell($this->GetStringWidth("a.) The total co-pay for the following are:") + $this->rheight7 + 8, $this->rheight2, "a.) The total co-pay for the following are:", $this->withoutborder, $this->nextline, $this->alignRight);
		$this->Ln(1);
		$this->setx($x1 + 13);
		$this->Cell(25, $this->rheight2 + 8.4, "", $this->withborder, $this->continueline, $this->alignRight);

		$this->setx($x1 + 38);
		$yy2 = $this->getY();
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label_normal);


		$this->Multicell(20, $this->rheight2 + 8.4, "Total Actual Charges*", $this->withborder, 'C');


		$this->sety($yy2);
		$this->setx($x1 + 58);
		$this->Multicell(38, $this->rheight2 + 8.4, "Amount after Application of Discount (i.e., personal discount, Senior Citizen/PWD)", $this->withborder, 'C');


		$this->sety($yy2);
		$this->setx($x1 + 96);
		$this->Multicell(22, $this->rheight2 + 8.4, "Philhealth Benefit", $this->withborder, 'C');



		$this->sety($yy2);
		$this->setx($x1 + 118);
		$this->Multicell(58, $this->rheight2 + 8.4, "Amount after Philhealth Deduction", $this->withborder, 'C');


		//-----------------------------------r2-----------------------------------------------

		$this->setx($x1 + 13);
		$yy2 = $this->getY();
		$this->Multicell(25, $this->rheight2 + 18, "Total Health Care Institution Fees", $this->withborder, 'C');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->sety($yy2);
		$this->setx($x1 + 38);
		$this->Cell(20, $this->rheight2 + 18, $total_hci2, $this->withborder, 'C');


		$this->sety($yy2);
		$this->setx($x1 + 58);

		$this->Cell(38, $this->rheight2 + 18, $discount_hci, $this->withborder, 'C');

		$this->sety($yy2);
		$this->setx($x1 + 96);
		$this->Cell(22, $this->rheight2 + 18, $total_hci_coverage, $this->withborder, 'C');
		// var_dump($total_hci_coverage);die;

		$this->sety($yy2);
		$this->setx($x1 + 118);
		$this->Multicell(58, $this->rheight2 + 18, "", $this->withborder, 'C');

		$this->sety($yy2 + 1);
		$this->setx($x1 + 120);

		$this->Cell(13, $this->rheight2, "Amount P ", $this->withoutborder, 'C');

		$this->SetFont($this->fontfamily_label, "U", $this->fontsize_label3 - .5);
		$this->Cell(10, $this->rheight2, $total_hci_excess, $this->withoutborder, 'C');
		$this->Ln(4);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		//$this->sety($yy2+5);
		$this->SetX($x1 + 120);
		$this->Cell(13, $this->rheight2, "Paid by (Check all that applies):", $this->withoutborder, 'R');
		$this->Ln(5);

		$this->Rect($x1 + 120, $yy2 + 10, $this->boxwidth + .5, $this->boxheight + .5);

		//$this->sety($yy2+6.5);
		$this->setx($x1 + 125);
		$this->Cell(13, $this->rheight2, "Member/Patient", $this->withoutborder, 'R');


		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);
		$y = $this->GetY();
		if ($total_hci_excess != '' && $total_hci_excess != '0.00') {
			$this->SetXY($x1 + 120, $this->GetY() - .9);
			$this->Cell(5, $this->rheight2, '/', $this->withoutborder, 'R');
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);

		//------------------------
		$this->Rect($x1 + 147, $yy2 + 10, $this->boxwidth + .5, $this->boxheight + .5);

		$this->SetXY($x1 + 151, $y);
		$this->Cell(13, $this->rheight2, "HMO", $this->withoutborder, 'R');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$this->Ln(5);
		$this->setx($x1 + 129);
		$this->Cell(5, $this->rheight2, $Shmo1, $this->withoutborder, 'R');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		//---------------------------------

		$this->Rect($x1 + 120, $yy2 + 15, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetX($x1 + 125);
		$this->Cell(13, $this->rheight2, "Others (i.e., PCSO, Promissory note, etc.)", $this->withoutborder, 'R');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$this->sety($yy2 + 11);
		$this->setx($x1 + 109);
		$this->Cell(5, $this->rheight2, $Sothers1, $this->withoutborder, 'R');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);

		$this->sety($yy2 + 10);
		$this->setx($x1 + 105);
		// $this->Line(132,89,160,89);

		//---------------------------------
		//end ----------------------------------r2--------------------------------------------------
		$this->ln(10);

		//-----------------------------------r3-----------------------------------------------

		$this->setx($x1 + 13);
		$yy2 = $this->getY();
		$this->Multicell(25, $this->rheight2 + 17, "Total Professional Fees (for accredited and non-accredited professionals)", $this->withborder, 'C');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3);
		$this->sety($yy2);
		$this->setx($x1 + 38);
		$this->Cell(20, $this->rheight2 + 17, $total_doc2, $this->withborder, 'C');


		$this->sety($yy2);
		$this->setx($x1 + 58);
		$height = $this->rheight2 + 17;
		$this->Cell(38, $height, $discount_doc, $this->withborder, 'C');


		$this->sety($yy2);
		$this->setx($x1 + 96);
		$this->Cell(22, $this->rheight2 + 17, $total_doc_coverage, $this->withborder, 'C');


		$this->sety($yy2);
		$this->setx($x1 + 118);
		$this->Cell(58, $this->rheight2 + 17, "", $this->withborder, 'C');

		$this->sety($yy2 + 1);
		$this->setx($x1 + 120);

		$this->Cell(13, $this->rheight2, "Amount P ", $this->withoutborder, 'C');
		$this->SetFont($this->fontfamily_label, "U", $this->fontsize_label3 - .5);
		$this->Cell(10, $this->rheight2, $total_doc_excess, $this->withoutborder, 'C');

		$this->ln();

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);

		$this->setx($x1 + 120);
		$this->Cell(13, $this->rheight2, "Paid by (Check all that applies):", $this->withoutborder, 'R');
		$this->Ln(5);

		$this->Rect($x1 + 120, $yy2 + 10, $this->boxwidth + .5, $this->boxheight + .5);

		//$this->sety($yy2+6.5);
		$this->setx($x1 + 125);
		$this->Cell(13, $this->rheight2, "Member/Patient", $this->withoutborder, 'R');


		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$y = $this->GetY();
		if ($total_doc_excess != '' && $total_doc_excess != '0.00') {
			$this->SetXY($x1 + 120, $this->GetY() - .5);
			$this->Cell(5, $this->rheight2 - 2, '/', $this->withoutborder, 'R');
		}

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);

		//------------------------
		$this->Rect($x1 + 147, $yy2 + 10, $this->boxwidth + .5, $this->boxheight + .5);

		$this->SetXY($x1 + 151, $y);
		$this->Cell(13, $this->rheight2, "HMO", $this->withoutborder, 'R');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$this->Ln(5);
		$this->setx($x1 + 129);
		$this->Cell(5, $this->rheight2, $Shmo1, $this->withoutborder, 'R');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		//---------------------------------

		$this->Rect($x1 + 120, $yy2 + 15, $this->boxwidth + .5, $this->boxheight + .5);
		$this->setx($x1 + 130);
		$this->setx($x1 + 125);
		$this->Cell(13, $this->rheight2, "Others (i.e., PCSO, Promissory note, etc.)", $this->withoutborder, 'R');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$this->sety($yy2 + 11);
		$this->setx($x1 + 109);
		$this->Cell(5, $this->rheight2, $Sothers2, $this->withoutborder, 'R');

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label4);

		$this->sety($yy2 + 1);
		$this->setx($x1 + 105);
		// $this->Line(132,106,160,106);

		//---------------------------------
		//end ----------------------------------r3--------------------------------------------------
		$this->ln(20);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$this->Cell($this->GetStringWidth("b.) Purchase/Expenses") + $this->rheight7 + 7, $this->rheight2, "b.) Purchase/Expenses", $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_label_bold, $this->fontsize_label3 - .5);

		$this->Cell(7, $this->rheight2, "NOT", $this->withoutborder, $this->continueline, $this->alignRight);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$this->Cell($this->GetStringWidth("  included in the Health Care Institution Charges"), $this->rheight2, "included in the Health Care Institution Charges", $this->withoutborder, $this->nextline, $this->alignRight);
		$this->Ln(1);

		$b1 = "Total cost of purchase/s for drugs/medicines and/or medical supplies bought by the patient/member within/outside the HCI during confinement";
		$yy3 = $this->getY();
		$xx3 = $this->getX();

		$this->setx($x1 + 13);
		$this->Multicell(($this->GetStringWidth($b1) / 2) + 10, $this->rheight2 + 5, $b1, $this->withborder, $this->alignLeft);


		$this->SetY($yy3);
		$this->SetX($xx3 + 113);
		$this->Cell(68, $this->rheight2 + 5, "", $this->withborder, $this->alignLeft);


		$this->Rect($xx3 + 115, $yy3 + 1.5, $this->boxwidth + .5, $this->boxheight + .5);

		if ($this->total_meds > 0 && ($this->memcategory_id != HSM || $this->memcategory_id != SM)) {
			$checknone = "";
			$check = "/";
			$value = "P " . number_format($this->total_meds, 2, '.', ',');
		} else {
			$checknone = "/";
			$check = "";
			$value = "P";
		}

		$this->SetY($yy3 - .5);
		$this->SetX($xx3 + 115);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$this->Cell(15, $this->rheight2 + 5, $checknone, $this->withoutborder, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);


		$this->SetY($yy3);
		$this->SetX($xx3 + 119);
		$this->Cell(10, $this->rheight2 + 5, "NONE", $this->withoutborder, $this->alignLeft);


		$this->Rect($xx3 + 130, $yy3 + 1.5, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetY($yy3);
		$this->SetX($xx3 + 135);
		$this->Cell(10, $this->rheight2 + 5, "Total Amount", $this->withoutborder, $this->alignLeft);

		$this->SetY($yy3);
		$this->SetX($xx3 + 130);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$this->Cell(10, $this->rheight2 + 4, $check, $this->withoutborder, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);

		$this->SetY($yy3);
		$this->SetX($xx3 + 155);
		$this->Cell(10, $this->rheight2 + 5, $value, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetX($xx3 + 158);
		$this->Line(165, $yy3 + 6, 188, $yy3 + 6);


		$this->Ln(7);
		//end-------------------------------------------------
		//----------------------------------------------------------

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$b2 = "Total cost of diagnostic/laboratory examinations paid for by the patient/member done within/outside the HCI during confinement";
		$yy3 = $this->getY();
		$xx3 = $this->getX();

		$this->setx($x1 + 13);
		$this->Multicell(94, $this->rheight2 + 5, $b2, $this->withborder, $this->alignLeft);


		$this->SetY($yy3);
		$this->SetX($xx3 + 113);
		$this->Cell(68, $this->rheight2 + 5, "", $this->withborder, $this->alignLeft);

		if ($this->total_xlo > 0 && ($this->memcategory_id != HSM || $this->memcategory_id != SM)) {
			$checknone = "";
			$check = "/";
			$value = "P " . number_format($this->total_xlo, 2, '.', ',');
		} else {
			$checknone = "/";
			$check = "";
			$value = "P";
		}

		$this->Rect($xx3 + 115, $yy3 + 1.5, $this->boxwidth + .5, $this->boxheight + .5);

		$this->SetY($yy3);
		$this->SetX($xx3 + 115);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$this->Cell(15, $this->rheight2 + 4, $checknone, $this->withoutborder, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);


		$this->SetY($yy3);
		$this->SetX($xx3 + 119);
		$this->Cell(10, $this->rheight2 + 5, "NONE", $this->withoutborder, $this->alignLeft);


		$this->Rect($xx3 + 130, $yy3 + 1.5, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetY($yy3);
		$this->SetX($xx3 + 135);
		$this->Cell(10, $this->rheight2 + 5, "Total Amount", $this->withoutborder, $this->alignLeft);

		$this->SetY($yy3);
		$this->SetX($xx3 + 130);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_answer_check);

		$this->Cell(10, $this->rheight2 + 4, $check, $this->withoutborder, $this->alignLeft);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);

		$this->SetY($yy3);
		$this->SetX($xx3 + 155);
		$this->Cell(10, $this->rheight2 + 5, $value, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetX($xx3 + 300);
		$this->Line(165, $yy3 + 6, 188, $yy3 + 6);

		$this->SetY($yy3);
		$this->SetX($xx3 + 128);
		$this->Cell(10, $this->rheight2 + 4, $total_value2, $this->withoutborder, $this->nextline, $this->alignLeft);

		//end

		$this->Ln(2);

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		//$this->SetY($yy3);
		$this->SetX($xx3 + 13);
		$string1 = "*NOTE:  Total Actual Charges should be based on Statement of Account (SoA)";
		$this->Cell($this->GetStringWidth($string1) + $this->rheight7, $this->rheight2, $string1, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->Ln(2);


		//B. CONSENT TO ACCESS PATIENT RECORD/S
		$this->Ln($this->rheight2);
		$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_answer, $this->fontsize_label3);
		$this->Cell($this->GetStringWidth($b[0]), $this->rheight7, $b[0], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_label_bold_italicized, $this->fontsize_label3);
		$this->Cell($this->GetStringWidth($b[1]), $this->rheight7, $b[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($this->rheight6);
		$this->SetFont($this->fontfamily_times, $this->fontstyle_label_bold_italicized, $this->fontsize_label3);
		$this->SetX($xx3 + 5);
		$this->Multicell(195, $this->rheight22, $b_ln1[0] . "\n" . $b_ln1[1] . "\n" . $b_ln1[2] . "\n" . $b_ln1[3] . "\n", $this->withoutborder, $this->alignLeft);
		// $this->Cell($this->GetStringWidth($b_ln1[0]) + $this->rheight2, $this->rheight2, $b_ln1[0], $this->withoutborder, $this->nextline, $this->alignRight);
		// $this->Cell($this->GetStringWidth($b_ln1[1]) + $this->rheight2, $this->rheight2, $b_ln1[1], $this->withoutborder, $this->nextline, $this->alignRight);
		// $this->Cell($this->GetStringWidth($b_ln1[2]) + $this->rheight2, $this->rheight2, $b_ln1[2], $this->withoutborder, $this->nextline, $this->alignRight);
		//conforme label
		$this->Ln(4);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_bold, $this->fontsize_label);
		$width = $this->GetStringWidth($conforme_label[0]) + $ColWidthPart3[0] + $this->rheight7;
		$this->Cell($width, $this->rheight2, $conforme_label[0], $this->withoutborder, $this->nextline, $this->alignRight);
		//line for signature 
		$this->Cell($this->rheight4 * 4, '');
		$x = $this->GetX();
		$y = $this->GetY() + $this->rheight7;
		$this->setY($y - 13);
		$width += $ColWidthPart3[1] + $ColWidthPart3[0];
		//$this->Line($x+30,$y-5,$width+68,$y-5);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label3);
		$this->Cell($width - $this->GetStringWidth($conforme_name) + 73, $this->rheight * 2 + $this->lineAdjustment * 2, $conforme_name, $this->withoutborder, $this->continueline, $this->alignCenter);
		//signature sub text
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label5);
		$this->Ln($this->rheight7);
		$this->Cell(($this->rheight4 * 4) - 5, '');
		$this->Cell($width + 25, $this->rheight2, $conforme_label[1], $this->borderTop, $this->nextline, $this->alignLeft);
		//date signed
		$this->Ln($this->lineAdjustment * 2);
		$this->Cell($ColWidthPart3[1] + $this->rheight2, '');
		$this->Cell($this->GetStringWidth($date_label[0]) + $this->rheight2, $this->rheight2, $date_label[0], $this->withoutborder, $this->continueline, $this->alignLeft);

		$x = $this->GetX();
		$y = $this->GetY() - $this->lineAdjustment;
		$this->SetXY($x, $y);

		$dt = new DateTime();
		$date_val = $dt->format('mdY');;
		$this->addBlockDate($date_val);
		$this->Ln($this->rheight4 + $this->lineAdjustment);
		//date format subtext $ColWidthPart3 = array(10,20,30,40,50,100);
		$this->SetX($x - 37);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$this->Cell($this->GetStringWidth($date_label[1]) + $ColWidthPart3[3], $this->rheight2, $date_label[1], $this->withoutborder, $this->continueline, $this->alignRight);
		$this->Cell($this->GetStringWidth($date_label[2]) + $ColWidthPart3[0] - 3, $this->rheight2, $date_label[2], $this->withoutborder, $this->continueline, $this->alignRight);
		$this->Cell($this->GetStringWidth($date_label[3]) + $ColWidthPart3[0], $this->rheight2, $date_label[3], $this->withoutborder, $this->continueline, $this->alignRight);

		$this->Ln($ColWidthPart3[0]);
		$this->SetX($ColWidthPart3[0] + $this->rheight2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$y1 = $this->GetY();
		$x1 = $ColWidthPart3[2] + $this->rheight;
		$this->SetXY($xT + 10, $y1 - 3);
		$this->Cell($this->lineAdjustment * 2, '');
		$this->MultiCell($x1, $this->rheight, $relationship_label[0], $this->withoutborder, $this->alignLeft, 0, 1, '', '', true, 0, false, true, 0, 'T', true);
		//draw box  is spouse
		$x1 += $ColWidthPart3[0] + $this->rheight2;
		$y1 -= $this->rheight2 - $this->lineAdjustment;
		$this->SetXY($x1, $y1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x - 3, $y + $this->inspace - 3, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check is spouse
		$x1 -= $this->rheight6;
		$this->SetXY($x1 - 2, $y - 3);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'spouse' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($ColWidthPart3[0], $this->rheight6, $relationship_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		//draw box is child
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x + 2, $y + $this->inspace, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check is child
		$x1 += $ColWidthPart3[0] + $this->rheight3 + $this->lineAdjustment;
		$this->SetXY($x1, $y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'child' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($ColWidthPart3[0], $this->rheight6, $relationship_label[3], $this->withoutborder, $this->continueline, $this->alignCenter);
		//draw box is parent
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y + $this->inspace, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check is parent
		$x1 += $ColWidthPart3[0] + $this->rheight3 + $this->lineAdjustment;
		$this->SetXY($x1, $y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'parent' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($ColWidthPart3[0], $this->rheight6, $relationship_label[4], $this->withoutborder, $this->continueline, $this->alignCenter);
		$xT = $this->GetX(); //x y third column
		$yT = $this->GetY();
		//draw box is sibling
		$x1 -= $ColWidthPart3[1] + $this->lineAdjustment * 2;
		$y1 += $this->rheight4 + $this->lineAdjustment * 2;
		$this->SetXY($x1, $y1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x - 3, $y + $this->inspace - 3, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check is sibling
		$x1 -= $this->rheight6;
		$this->SetXY($x1 - 2, $y - 3);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'sibling' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($ColWidthPart3[0], $this->rheight6, $relationship_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		//draw box is others 
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x + 2, $y + $this->inspace, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check is others
		$x1 += $ColWidthPart3[0] + $this->rheight3 + $this->lineAdjustment;
		$this->SetXY($x1, $y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_relationship == 'other' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($this->GetStringWidth($relationship_label[5]) + $this->rheight3, $this->rheight6, $relationship_label[5], $this->withoutborder, $this->continueline, $this->alignRight);
		$x = $this->GetX();
		$y = $this->GetY() + $this->rheight4 + $this->lineAdjustment;
		$width = $ColWidthPart3[5] + $ColWidthPart3[0];
		$this->Line($x, $y, $width, $y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label);
		$this->Cell($this->GetStringWidth($rel_other), $this->rheight + $this->lineAdjustment, $rel_other, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln($ColWidthPart3[0]);
		$this->SetX($ColWidthPart3[0] + $this->rheight2);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$y1 = $this->GetY();
		$x1 = $ColWidthPart3[2];
		$this->SetXY($x1 - 20, $y1 - 2);
		$this->Cell($this->lineAdjustment * 2, '');
		$this->MultiCell($x1, $this->rheight, $reasons_label[0], $this->withoutborder, $this->alignLeft, 0, 1, '', '', true, 0, false, true, 0, 'T', true);
		//draw box  Patient is Incapacitated
		$x1 += $ColWidthPart3[0] + $this->rheight7;
		$y1 -= $this->rheight2 - $this->lineAdjustment;
		$this->SetXY($x1, $y1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x - 3, $y + $this->inspace - 2, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check is Patient is Incapacitated
		$x1 -= $this->rheight6;
		$this->SetXY($x1, $y - 3);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_reasons == 'incapacitated' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($ColWidthPart3[1] + $this->rheight6, $this->rheight6, $reasons_label[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		//draw box Other Reasons:
		$x1 += $this->rheight6;
		$y1 += $this->rheight4 + $this->lineAdjustment * 2;
		$this->SetXY($x1, $y1);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x - 3, $y + $this->inspace - 2, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check Other Reasons:
		$x1 -= $this->rheight6;
		$this->SetXY($x1, $y - 2);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $conforme_reasons == 'other' ? '/' : ' ';
		$this->Cell($ColWidthPart3[0], $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignRight);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($ColWidthPart3[1] - $this->rheight2, $this->rheight6, $reasons_label[2], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX();
		$y = $this->GetY() + $this->rheight4 + $this->lineAdjustment;
		$width = $ColWidthPart3[5] + $ColWidthPart3[0];
		$this->Line($x, $y, $width, $y);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_label3 - .5);
		$this->Cell($this->GetStringWidth($reasons_other), $this->rheight + $this->lineAdjustment, $reasons_other, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$xT += $ColWidthPart3[1] + $this->rheight;
		$yT += $this->lineAdjustment * 3;


		// $this->SetXY($xT, $yT);
		$this->SetXY($xT, 260);
		$width = $ColWidthPart3[4] - $this->rheight4;
		$this->MultiCell($width + 10, $this->rheight, $representative_label[0], $this->withoutborder, $this->alignLeft, 0, 1, '', '', true, 0, false, true, 0, 'T', true);
		//thumbmark box
		$xT += $width;
		$yT -= $this->rheight3;
		$this->SetXY($xT, 250);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$width = $ColWidthPart3[2];
		$height = $ColWidthPart3[1] + $this->rheight2 + 10;
		$this->Rect($x + 10, $y, $width, $height);
		$this->SetLineWidth(0.2);
		//draw box patient 
		$xT -= ($ColWidthPart3[1] * 2 + $this->rheight);
		$yT += $ColWidthPart3[0] + $this->rheight; //; - $this->lineAdjustment;
		$this->SetXY($xT, $yT);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y + $this->inspace + 2, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check patient
		$x1 -= $this->rheight5 + $this->lineAdjustment;
		$this->SetXY($xT, $yT + 2);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $thumbmark_data == 'patient' ? '/' : ' ';
		$this->Cell($this->rheight, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($this->GetStringWidth($representative_label[1]), $this->rheight6, $representative_label[1], $this->withoutborder, $this->continueline, $this->alignLeft);
		//draw box representative
		$xT += $ColWidthPart3[0] + $this->rheight7;
		//$yT += $ColWidthPart3[0]+$this->rheight;//; - $this->lineAdjustment;
		$this->SetXY($xT, $yT);
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetLineWidth(0.3);
		$this->Rect($x, $y + $this->inspace + 2, $this->boxwidth + .5, $this->boxheight + .5);
		$this->SetLineWidth(0.2);
		//check representative
		$x1 -= $this->rheight5 + $this->lineAdjustment;
		$this->SetXY($xT, $yT + 2);
		$this->SetFont($this->fontfamily_answer, $this->fontstyle_answer, $this->fontsize_answer_check);
		$check_value = $thumbmark_data == 'representative' ? '/' : ' ';
		$this->Cell($this->rheight, $this->rheight7, $check_value, $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label);
		$x = $this->GetX() + $this->lineAdjustment * 1.5;
		$this->Cell($this->GetStringWidth($representative_label[2]), $this->rheight6, $representative_label[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$this->Ln(3);
	}

	function addPart4()
	{
		$part4 = 'PART IV - CERTIFICATION OF HEALTH CARE INSTITUTION';
		$certify_label = 'I certify that services rendered were recorded in the patient\'s chart and health care institution records and that the herein information given are true and correct.';
		$sub_text = array('Signature Over Printed Name of Authorized HCI Representative', 'Official Capacity / Designation', 'Date Signed:', 'month', 'day', 'year');

		$hci_rep_name = '';
		$official_capacity = '';
		$date_signed = '';

		$this->Ln($this->rheight7);
		$this->addTitleBar($part4, '');
		$ColWidthPart4 = array(200, 13, 50, 20, 15);

		$objInfo = new Hospital_Admin();
		if ($row = $objInfo->getAllHospitalInfo()) {
			$hci_rep_name = $row['authrep'];
			$official_capacity = $row['designation'];
		}

		$this->Ln();
		$this->SetFont($this->fontfamily_times, $this->fontstyle_label_bold_italicized, $this->fontsize_label);
		$this->Ln($this->rheight4 - 3);
		$this->MultiCell($ColWidthPart4[0], $this->rheight, $certify_label, $this->withoutborder, $this->alignLeft, 0, 1, $ColWidthPart4[1], '', true, 0, false, true, 0, 'T', true);
		// $this->Ln();
		$this->Ln($ColWidthPart4[1] - 3);
		$this->SetX($ColWidthPart4[1] + 8);
		$width = $ColWidthPart4[2] + $ColWidthPart4[3];
		$this->SetFont($this->fontfamily_label . 'bd', $this->fontstyle_answer, $this->fontsize_label3 + 1);
		$this->Cell($this->GetStringWidth($hci_rep_name) - 20, $this->rheight2, mb_convert_encoding(strtoupper(trim($hci_rep_name)), 'UTF-8'), $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $ColWidthPart4[1] + $width - $this->rheight6;
		$this->SetXY($x + 8, $this->GetY());
		$this->Cell($width + $ColWidthPart4[4] - $this->GetStringWidth($official_capacity), $this->rheight2 + $this->lineAdjustment, $official_capacity, $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $width * 2 + $ColWidthPart4[1] - $this->rheight7;
		$this->SetXY($x - 9, $this->GetY() - 2);
		$this->Cell($this->GetStringWidth($sub_text[2]) + $this->lineAdjustment * 4, $this->rheight2 + $this->lineAdjustment, $sub_text[2], $this->withoutborder, $this->continueline, $this->alignLeft);
		$add_days = 10;
		$date = $this->getCalculateDate($this->bill_dte);
		$date = empty($date) ? '        ' : $date;
		//$this->SetXY($x+4,$this->GetY()-3);
		$dt = new DateTime();
		$date = $dt->format('mdY');
		$this->addBlockDate($date);
		$this->Ln($this->rheight3 + $this->lineAdjustment);
		//line for signature
		$this->SetX($ColWidthPart4[1]);
		$x = $this->GetX();
		$y = $this->GetY(); //+$this->rheight4+$this->lineAdjustment;
		$this->Line($x, $y + 3, $width, $y + 3);
		//line for designation
		$x = $ColWidthPart4[1] + $width - $this->rheight;
		$this->SetX($x);
		$x = $this->GetX();
		$y = $this->GetY();
		$width2 = $width * 2;
		$this->Line($x, $y + 3, $width2 - 5, $y + 3);
		//sub text
		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_normal, $this->fontsize_label3 - .5);
		$this->SetXY($ColWidthPart4[1], $y + 3);
		$y = $this->GetY();
		$this->MultiCell($width - $ColWidthPart4[1] - $this->rheight * 2, $this->rheight, $sub_text[0], $this->withoutborder, $this->alignCenter, 0, 1, $ColWidthPart4[4] + $this->rheight3, '', true, 0, false, true, 0, 'T', true);
		$x = $ColWidthPart4[1] + $width - $this->rheight4;
		$this->SetXY($x, $y);
		$this->Cell($width - $ColWidthPart4[1], $this->rheight2, $sub_text[1], $this->withoutborder, $this->continueline, $this->alignCenter);
		$x = $this->GetX() + $ColWidthPart4[3] + $this->rheight6 + $this->lineAdjustment;
		$this->SetXY($x - 8, $y - 3);
		$this->Cell($this->GetStringWidth($subtext[3]) + $ColWidthPart4[1] + $this->lineAdjustment, $this->rheight, $sub_text[3], $this->withoutborder, $this->continueline, $this->alignRight);
		$this->Cell($this->GetStringWidth($subtext[4]) + $ColWidthPart4[4] - 5, $this->rheight, $sub_text[4], $this->withoutborder, $this->continueline, $this->alignRight);
		$this->Cell($this->GetStringWidth($subtext[5]) + $ColWidthPart4[4], $this->rheight, $sub_text[5], $this->withoutborder, $this->continueline, $this->alignRight);
		// $this->Line($x, $y + 3, $width2 - 5, $y + 3);
		// $this->Line($x, $y + 3, $width2 - 5, $y + 3);
	}


	//-------------------------------------
	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths = $w;
	}


	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns = $a;
	}


	function pageBoarders()
	{

		$x = $this->GetX();
		$y = $this->GetY() + 3;
		// $this->SetXY($x,$y);
		$this->Rect($x - 1, 5, $this->totwidth + 1, 1, 'DF', "",  array(FFF, FFF, FFF));	//top
		$this->Rect($x - 1, $this->totwidth + 140, $this->totwidth + 1, 1, 'DF', "",  array(FFF, FFF, FFF));	//bot
		$this->Rect($x - 1, 5, 1, $this->totwidth + 135, 'DF', "",  array(FFF, FFF, FFF));	//left
		$this->Rect(($x * 20) + 48, 5, 1, $this->totwidth + 135, 'DF', "",  array(FFF, FFF, FFF));	//right
	}

	function addName()
	{

		$this->SetTopMargin(7);
		$this->getBillingDetails();
		$this->getConformeName(); //added by mai 08-27-2014
		$addname = "Patient: " . $this->patient_name . " - (con't)";

		$this->SetFont($this->fontfamily_label, $this->fontstyle_label_italicized, $this->fontsize_answer_cert);
		$this->Cell($this->totwidth, $this->rheight,  mb_convert_encoding(strtoupper(trim($addname)), 'UTF-8'), $this->withoutborder, $this->nextline, $this->alignLeft);
		$this->pageBoarders();
	}

	/**
	 * @author Nick B. Alcala 05-05-2014
	 * Get all doctors info by 3s
	 * @return array
	 */
	function getDoctorsArrayChunks()
	{
		$result = $this->get_doctor_info();
		if ($result) {
			if ($result->RecordCount()) {
				$rows = $result->GetRows();
			}
		}
		return array_chunk($rows, 3);
	}
}


header("Content-type: text/html; charset=utf-8");

$pdf = new PhilhealthForm2Part3();
$pdf->encounter_nr = $_GET['encounter_nr'];
$pdf->bill_nr = $_GET['bill_nr'];

//added by Nick, 5-5-2014
$doctors = $pdf->getDoctorsArrayChunks();
if (is_array($doctors)) {
	foreach ($doctors as $key => $doctor) {
		$pdf->Open();
		$pdf->AddPage();
		$pdf->addName();
		$pdf->addProfessionalFees($doctor);
		$pdf->addPart3();
		$pdf->addPart4();
	}
} else {
	$pdf->Open();
	$pdf->AddPage();
	$pdf->pageBoarders();
	$pdf->addName();
	$pdf->addProfessionalFees($doctor);
	$pdf->addPart3();
	$pdf->addPart4();
}
//end Nick

$pdf->Output();
