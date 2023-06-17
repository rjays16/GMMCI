<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgenclass.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/class_encounter.php';

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
* Requests printout for Clinics, OPD, and Wards (cash only)
* created by CHA, august 20, 2010
*/

class RepGen_Clinic_Requests_Slip extends RepGen {

	var $encounter_nr;
	var $total_payment = 0;

	function RepGen_Clinic_Requests_Slip($encounter_nr)
	{
		global $db;

		if($encounter_nr)
			$this->encounter_nr=$encounter_nr;

		$this->date=date("Y-m-d");

		$this->totalRow= $totalRow;
		$this->RepGen("REQUEST SLIP","L", array(280,108));

		$this->TopMargin = 5;
		$this->LeftMargin = 6;
		$this->RightMargin = 6;
		$this->SetLineWidth(0.2);
		$this->TextPadding = 0.3;
		$this->colored = FALSE;
		//$this->RowHeight = 3;
		$this->FONTSTYLE = "Times";
		//$this->NoWrap=FALSE;

		if ($this->colored)
			$this->SetDrawColor(0xDD);
	}

	function Header()
	{
		global $root_path, $db;
		$today = date("F d, Y");

		$this->SetFont("Times","I","9");

		$hospital = new Hospital_Admin();
		$hospitalInfo = $hospital->getAllHospitalInfo();
		//$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',4,4,7,8);
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',10,5,10,10);
		$total_w = 0;
		$this->Cell($total_w,2,$hospitalInfo['hosp_country'],$border2,1,'C');
		$this->Cell($total_w,2,$hospitalInfo['hosp_agency'],$border2,1,'C');
		$this->Ln(1);
		$this->SetFont("Times","B","9");
		$this->Cell($total_w,3,$hospitalInfo['hosp_name'],$border2,1,'C');
		$this->SetFont("Times","","9");
		$this->Cell($total_w,2,$hospitalInfo['hosp_addr1'],$border2,1,'C');
		$this->Ln(3);
		$this->SetFont("Times","B","9");
		$this->Cell($total_w,3,"REQUEST SLIP",$border2,1,'C');
		$this->Ln(3);

		$this->SetFont("Times","","8");
		$person = new Encounter();
		$person_info = $person->getEncounterInfo($this->encounter_nr,0);
		$this->Cell(21,3, "PID: ".$person_info['pid'], $border2, 0, 'L');
		$this->SetXY(70,24);
		$this->Cell(21,3, "Date: ".$today, $border2, 1, 'L');
		$this->Cell(21,3, "Patient Name: ".strtoupper($person_info['name_last'].", ".$person_info['name_first']." ".$person_info['name_middle']), $border2, 0, 'L');
		$this->Ln(3);
		$this->printRequests();
		//	parent::Header();
	}

	function Footer()
	{

	}

	function AcceptPageBreak()
	{
		if($this->totalRow<20)
		{
			return false;
		}
		else
		{
			$this->totalRow=0;
			return true;
		}
	}


	function BeforeData()
	{
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
		$this->ColumnFontSize = 6;

	}

	function BeforeCellRender()
	{
		$this->FONTSIZE = 7;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}

	function AfterData()
	{
		$this->Line($this->GetX()-1,$this->GetY()+3,$this->GetX()+97,$this->GetY()+3);
		$this->Ln(3);
		$this->SetFont("Times", "B", 8);
		$this->Cell(100, 4, "Total Amount Payable: ".number_format($this->total_payment, 2), 0, 1, "L");
		$this->Line($this->GetX()-1,$this->GetY(),$this->GetX()+97,$this->GetY());

	}

	function printRequests()
	{
		global $db;

		$this->Ln(2);
		$sql = "SELECT l.refno FROM seg_lab_serv AS l INNER JOIN seg_lab_servdetails AS ld ON l.refno=ld.refno\n".
						"WHERE l.is_cash=1 AND l.ref_source='LB' AND l.status<>'deleted' \n".
						"AND (ld.request_flag IS NULL OR ld.request_flag!='paid')\n".
						"AND l.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(l.serv_dt)=DATE(NOW())";
		$has_lab = $db->GetOne($sql);
		if($has_lab!==FALSE) {
			$this->SetFont("Times","B", 8);
			$this->Cell(30, 4, "Laboratory :", "", 1, 'L');
			$this->printLabRequests();
			$this->Ln(2);
		}

		$sql = "SELECT l.refno FROM seg_lab_serv AS l INNER JOIN seg_lab_servdetails AS ld ON l.refno=ld.refno\n".
						"WHERE l.is_cash=1 AND l.ref_source='BB' AND l.status<>'deleted' \n".
						"AND (ld.request_flag IS NULL OR ld.request_flag!='paid')\n".
						"AND l.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(l.serv_dt)=DATE(NOW())";
		$has_blood = $db->GetOne($sql);
		if($has_blood!==FALSE) {
			$this->SetFont("Times","B", 8);
			$this->Cell(30, 4, "Blood Bank :", "", 1, 'L');
			$this->printBloodRequests();
			$this->Ln(2);
		}

		$sql = "SELECT l.refno FROM seg_lab_serv AS l INNER JOIN seg_lab_servdetails AS ld ON l.refno=ld.refno\n".
						"WHERE l.is_cash=1 AND l.ref_source='SPL' AND l.status<>'deleted' \n".
						"AND (ld.request_flag IS NULL OR ld.request_flag!='paid')\n".
						"AND l.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(l.serv_dt)=DATE(NOW())";
		$has_splab = $db->GetOne($sql);
		if($has_splab!==FALSE) {
			$this->SetFont("Times","B", 8);
			$this->Cell(30, 4, "Special Lab :", "", 1, 'L');
			$this->printSplabRequests();
			$this->Ln(2);
		}

		$sql = "SELECT l.refno FROM seg_lab_serv AS l INNER JOIN seg_lab_servdetails AS ld ON l.refno=ld.refno\n".
						"WHERE l.is_cash=1 AND l.ref_source='IC' AND l.status<>'deleted' \n".
						"AND (ld.request_flag IS NULL OR ld.request_flag!='paid')\n".
						"AND l.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(l.serv_dt)=DATE(NOW())";
		$has_iclab = $db->GetOne($sql);
		if($has_iclab!==FALSE) {
			$this->SetFont("Times","B", 8);
			$this->Cell(30, 4, "Industrial Clinic Lab :", "", 1, 'L');
			$this->printICLabRequests();
			$this->Ln(2);
		}

		$sql = "SELECT r.refno FROM seg_radio_serv AS r INNER JOIN care_test_request_radio AS rd ON r.refno=rd.refno\n".
						"WHERE r.is_cash=1 AND r.status<>'deleted' \n".
						"AND (rd.request_flag IS NULL OR rd.request_flag!='paid')\n".
						"AND r.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(r.request_date)=DATE(NOW())";
		$has_radio = $db->GetOne($sql);
		if($has_radio!==FALSE) {
			$this->SetFont("Times","B", 8);
			$this->Cell(30, 4, "Radiology :", "", 1, 'L');
			$this->printRadioRequests();
			$this->Ln(2);
		}


		$sql = "SELECT p.refno FROM seg_pharma_orders AS p INNER JOIN seg_pharma_order_items AS ph ON p.refno=ph.refno \n".
						"WHERE p.is_cash=1 AND p.pharma_area='IP' \n".
						"AND (ph.request_flag IS NULL OR ph.request_flag!='paid')\n".
						"AND p.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(p.orderdate)=DATE(NOW())";
		$has_ip = $db->GetOne($sql);
		if($has_ip!==FALSE) {
			$this->SetFont("Times","B", 8);
			$this->Cell(30, 4, "Inpatient Pharmacy :", "", 1, 'L');
			$this->printIpRequests();
			$this->Ln(2);
		}

		$sql = "SELECT p.refno FROM seg_pharma_orders AS p INNER JOIN seg_pharma_order_items AS ph ON p.refno=ph.refno \n".
						"WHERE p.is_cash=1 AND p.pharma_area='MG' \n".
						"AND (ph.request_flag IS NULL OR ph.request_flag!='paid')\n".
						"AND p.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(p.orderdate)=DATE(NOW())";
		$has_mg = $db->GetOne($sql);
		if($has_mg!==FALSE) {
			$this->SetFont("Times","B", 8);
			$this->Cell(30, 4, "Murang Gamot :", "", 1, 'L');
			$this->printMgRequests();
			$this->Ln(2);
		}

		$sql = "SELECT m.refno FROM seg_misc_service AS m INNER JOIN seg_misc_service_details AS md ON m.refno=md.refno \n".
						"WHERE m.is_cash=1 \n".
						"AND (md.request_flag IS NULL OR md.request_flag!='paid')\n".
						"AND m.encounter_nr=".$db->qstr($this->encounter_nr)." AND DATE(m.chrge_dte)=DATE(NOW())";
		$has_misc = $db->GetOne($sql);
		if($has_misc!==FALSE) {
			$this->SetFont("Times","B", 8);
			$this->Cell(30, 4, "Miscellaneous :", "", 1, 'L');
			$this->printMiscRequests();
		}

	}

	function printTableHeader()
	{
		$this->SetFont("Times", "B", 6);
		$this->Cell(15,4, "Reference No.", 1, 0, 'C');
		$this->Cell(35,4, "Item Name", 1, 0, 'C');
		$this->Cell(15,4, "Quantity", 1, 0, 'C');
		$this->Cell(15,4, "Unit Price", 1, 0, 'C');
		$this->Cell(15,4, "Total", 1, 1, 'C');
		$this->SetFont("Times", "", 6);
	}

	function printLabRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT l.refno, CONCAT(l.serv_dt,' ', l.serv_tm) AS `date`, ls.name, ld.quantity, ld.price_cash, ld.request_flag \n".
					"FROM seg_lab_servdetails AS ld \n".
					"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
					"LEFT JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
					"WHERE l.encounter_nr=".$db->qstr($this->encounter_nr)." AND l.status<>'deleted' AND l.is_cash=1 \n".
					"AND l.ref_source='LB' AND (ld.request_flag IS NULL OR ld.request_flag!='paid') AND DATE(l.serv_dt)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_lab = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_lab+=parseFloatEx($total);
					$this->Cell(15, 4, $row["refno"], 1, 0, "C");

					//$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					if($this->GetStringWidth($row["name"])>32) {
						$this->SetFont('Times', '', 5);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
						$this->SetFont('Times', '', 6);
					} else {
						$this->SetFont('Times', '', 6);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(15, 4, $row["quantity"], 1, 0, "C");
					$this->Cell(15, 4, number_format($row["price_cash"],2), 1, 0, "R");
					//$this->Cell(15, 4, number_format($total,2), 1, 1, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(15, 4, $total_label, 1, 1, "R");
				}
				$this->Cell(80, 4, "TOTAL CASH: ", 0, 0, "L");
				$this->Cell(15, 4, number_format($total_lab,2), 0, 1, "R");
				$this->total_payment+=$total_lab;
			} else {
				$this->Cell(95, 4, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printBloodRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT l.refno, CONCAT(l.serv_dt,' ', l.serv_tm) AS `date`, ls.name, ld.quantity, ld.price_cash, ld.request_flag \n".
					"FROM seg_lab_servdetails AS ld \n".
					"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
					"LEFT JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
					"WHERE l.encounter_nr=".$db->qstr($this->encounter_nr)." AND l.status<>'deleted' AND l.is_cash=1 \n".
					"AND l.ref_source='BB' AND (ld.request_flag IS NULL OR ld.request_flag!='paid') AND DATE(l.serv_dt)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_blood = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_blood+=parseFloatEx($total);
					$this->Cell(15, 4, $row["refno"], 1, 0, "C");

					//$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					if($this->GetStringWidth($row["name"])>32) {
						$this->SetFont('Times', '', 5);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
						$this->SetFont('Times', '', 6);
					} else {
						$this->SetFont('Times', '', 6);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(15, 4, $row["quantity"], 1, 0, "C");
					$this->Cell(15, 4, number_format($row["price_cash"],2), 1, 0, "R");
					//$this->Cell(15, 4, number_format($total,2), 1, 1, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(15, 4, $total_label, 1, 1, "R");
				}
				$this->Cell(80, 4, "TOTAL CASH: ", 0, 0, "L");
				$this->Cell(15, 4, number_format($total_blood,2), 0, 1, "R");
				$this->total_payment+=$total_blood;
			} else {
				$this->Cell(95, 4, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printSpLabRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT l.refno, CONCAT(l.serv_dt,' ', l.serv_tm) AS `date`, ls.name, ld.quantity, ld.price_cash, ld.request_flag \n".
					"FROM seg_lab_servdetails AS ld \n".
					"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
					"LEFT JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
					"WHERE l.encounter_nr=".$db->qstr($this->encounter_nr)." AND l.status<>'deleted' AND l.is_cash=1 \n".
					"AND l.ref_source='SPL' AND (ld.request_flag IS NULL OR ld.request_flag!='paid' AND DATE(l.serv_dt)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_lab = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_lab+=parseFloatEx($total);
					$this->Cell(15, 4, $row["refno"], 1, 0, "C");

					//$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					if($this->GetStringWidth($row["name"])>32) {
						$this->SetFont('Times', '', 4);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
						$this->SetFont('Times', '', 6);
					} else {
						$this->SetFont('Times', '', 6);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(15, 4, $row["quantity"], 1, 0, "C");
					$this->Cell(15, 4, number_format($row["price_cash"],2), 1, 0, "R");
					//$this->Cell(15, 4, number_format($total,2), 1, 1, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(15, 4, $total_label, 1, 1, "R");
				}
				$this->Cell(80, 4, "TOTAL CASH: ", 0, 0, "L");
				$this->Cell(15, 4, number_format($total_lab,2), 0, 1, "R");
				$this->total_payment+=$total_lab;
			} else {
				$this->Cell(95, 4, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printICLabRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT l.refno, CONCAT(l.serv_dt,' ', l.serv_tm) AS `date`, ls.name, ld.quantity, ld.price_cash, ld.request_flag \n".
					"FROM seg_lab_servdetails AS ld \n".
					"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
					"LEFT JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
					"WHERE l.encounter_nr=".$db->qstr($this->encounter_nr)." AND l.status<>'deleted' AND l.is_cash=1 \n".
					"AND l.ref_source='IC' AND (ld.request_flag IS NULL OR ld.request_flag!='paid') AND DATE(l.serv_dt)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_lab = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_lab+=parseFloatEx($total);
					$this->Cell(15, 4, $row["refno"], 1, 0, "C");

					//$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					if($this->GetStringWidth($row["name"])>32) {
						$this->SetFont('Times', '', 4);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
						$this->SetFont('Times', '', 6);
					} else {
						$this->SetFont('Times', '', 6);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(15, 4, $row["quantity"], 1, 0, "C");
					$this->Cell(15, 4, number_format($row["price_cash"],2), 1, 0, "R");
					//$this->Cell(15, 4, number_format($total,2), 1, 1, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(15, 4, $total_label, 1, 1, "R");
				}
				$this->Cell(80, 4, "TOTAL CASH: ", 0, 0, "L");
				$this->Cell(15, 4, number_format($total_lab,2), 0, 1, "R");
				$this->total_payment+=$total_lab;
			} else {
				$this->Cell(95, 4, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printRadioRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT r.refno, CONCAT(r.request_date,' ', r.request_time) AS `date`, rs.name, 1 AS `quantity`, rd.price_cash, rd.request_flag \n".
					"FROM care_test_request_radio AS rd \n".
					"INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno \n".
					"LEFT JOIN seg_radio_services AS rs ON rd.service_code=rs.service_code \n".
					"WHERE r.encounter_nr=".$db->qstr($this->encounter_nr)." AND r.status<>'deleted' AND r.is_cash=1 \n".
					"AND (rd.request_flag IS NULL OR rd.request_flag!='paid') AND DATE(r.request_date)=DATE(NOW()) ORDER BY date DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_radio = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price_cash']);
					$total_radio+=parseFloatEx($total);
					$this->Cell(15, 4, $row["refno"], 1, 0, "C");

					//$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					if($this->GetStringWidth($row["name"])>32) {
						$this->SetFont('Times', '', 4);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
						$this->SetFont('Times', '', 6);
					} else {
						$this->SetFont('Times', '', 6);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(15, 4, $row["quantity"], 1, 0, "C");
					$this->Cell(15, 4, number_format($row["price_cash"],2), 1, 0, "R");
					//$this->Cell(15, 4, number_format($total,2), 1, 1, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(15, 4, $total_label, 1, 1, "R");
				}
				$this->Cell(80, 4, "TOTAL CASH: ", 0, 0, "L");
				$this->Cell(15, 4, number_format($total_radio,2), 0, 1, "R");
				$this->total_payment+=$total_radio;
			} else {
				$this->Cell(95, 4, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printIpRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT p.refno, p.orderdate, ph.artikelname, pi.quantity, pi.pricecash, pi.request_flag \n".
					"FROM seg_pharma_order_items AS pi \n".
					"INNER JOIN seg_pharma_orders AS p ON pi.refno=p.refno \n".
					"INNER JOIN care_pharma_products_main AS ph ON pi.bestellnum=ph.bestellnum \n".
					"WHERE p.encounter_nr=".$db->qstr($this->encounter_nr)." AND p.is_cash=1 AND p.pharma_area='IP' \n".
					"AND (pi.request_flag IS NULL OR pi.request_flag!='paid') AND DATE(p.orderdate)=DATE(NOW()) ORDER BY orderdate DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_ip = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['pricecash']);
					$total_ip+=parseFloatEx($total);
					$this->Cell(15, 4, $row["refno"], 1, 0, "C");

					//$this->Cell(35, 4, ucfirst($row["artikelname"]), 1, 0, "L");
					//$this->MultiCell(35, 6, ucfirst($row["artikelname"]), 1, 'L');
					if($this->GetStringWidth($row["artikelname"])>32) {
						$this->SetFont('Times', '', 4);
						$this->Cell(35, 4, ucfirst($row["artikelname"]), 1, 0, "L");
						$this->SetFont('Times', '', 6);
					} else {
						$this->SetFont('Times', '', 6);
						$this->Cell(35, 4, ucfirst($row["artikelname"]), 1, 0, "L");
					}

					$this->Cell(15, 4, $row["quantity"], 1, 0, "C");
					$this->Cell(15, 4, number_format($row["pricecash"],2), 1, 0, "R");
					//$this->Cell(15, 4, number_format($total,2), 1, 1, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(15, 4, $total_label, 1, 1, "R");
				}
				$this->Cell(80, 4, "TOTAL CASH: ", 0, 0, "L");
				$this->Cell(15, 4, number_format($total_ip,2), 0, 1, "R");
				$this->total_payment+=$total_ip;
			} else {
				$this->Cell(95, 4, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printMgRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT p.refno, p.orderdate, ph.artikelname, pi.quantity, pi.pricecash, pi.request_flag \n".
					"FROM seg_pharma_order_items AS pi \n".
					"INNER JOIN seg_pharma_orders AS p ON pi.refno=p.refno \n".
					"INNER JOIN care_pharma_products_main AS ph ON pi.bestellnum=ph.bestellnum \n".
					"WHERE p.encounter_nr=".$db->qstr($this->encounter_nr)." AND p.is_cash=1 AND p.pharma_area='MG' \n".
					"AND (pi.request_flag IS NULL OR pi.request_flag!='paid') AND DATE(p.orderdate)=DATE(NOW()) ORDER BY orderdate DESC ";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_mg = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['pricecash']);
					$total_mg+=parseFloatEx($total);
					$this->Cell(15, 4, $row["refno"], 1, 0, "C");

					//$this->Cell(35, 4, ucfirst($row["artikelname"]), 1, 0, "L");
					//$this->MultiCell(35, 6, ucfirst($row["artikelname"]), 1, 'L');
					if($this->GetStringWidth($row["artikelname"])>32) {
						$this->SetFont('Times', '', 4);
						$this->Cell(35, 4, ucfirst($row["artikelname"]), 1, 0, "L");
						$this->SetFont('Times', '', 6);
					} else {
						$this->SetFont('Times', '', 6);
						$this->Cell(35, 4, ucfirst($row["artikelname"]), 1, 0, "L");
					}

					$this->Cell(15, 4, $row["quantity"], 1, 0, "C");
					$this->Cell(15, 4, number_format($row["pricecash"],2), 1, 0, "R");
					//$this->Cell(15, 4, number_format($total,2), 1, 1, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(15, 4, $total_label, 1, 1, "R");
				}
				$this->Cell(80, 4, "TOTAL CASH: ", 0, 0, "L");
				$this->Cell(15, 4, number_format($total_mg,2), 0, 1, "R");
				$this->total_payment+=$total_mg;
			} else {
				$this->Cell(95, 4, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}

	function printMiscRequests()
	{
		global $db;
		$this->printTableHeader();
		$this->NoWrap=TRUE;
		$sql = "SELECT m.refno, m.chrge_dte, ms.name, md.quantity, md.chrg_amnt AS `price`, md.request_flag \n".
					"FROM seg_misc_service_details AS md \n".
					"INNER JOIN seg_misc_service AS m ON md.refno=m.refno \n".
					"INNER JOIN seg_other_services AS ms ON md.service_code=ms.alt_service_code \n".
					"WHERE m.encounter_nr=".$db->qstr($this->encounter_nr)." AND m.is_cash=1 \n".
					"AND (md.request_flag IS NULL OR md.request_flag!='paid') AND DATE(m.chrge_dte)=DATE(NOW()) ORDER BY chrge_dte DESC";
		$result = $db->Execute($sql);
		if($result!==FALSE) {
			if($result->RecordCount()>0){
				$total_misc = 0;
				while($row=$result->FetchRow())
				{
					$total=parseFloatEx($row['quantity']*$row['price']);
					$total_misc+=parseFloatEx($total);
					$this->Cell(15, 4, $row["refno"], 1, 0, "C");

					//$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					if($this->GetStringWidth($row["name"])>32) {
						$this->SetFont('Times', '', 4);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
						$this->SetFont('Times', '', 6);
					} else {
						$this->SetFont('Times', '', 6);
						$this->Cell(35, 4, ucfirst($row["name"]), 1, 0, "L");
					}

					$this->Cell(15, 4, $row["quantity"], 1, 0, "C");
					$this->Cell(15, 4, number_format($row["price"],2), 1, 0, "R");
					//$this->Cell(15, 4, number_format($total,2), 1, 1, "R");
					$total_label = ((isset($row['request_flag']) && strtolower($row["request_flag"])!='paid') ? strtoupper($row["request_flag"]) : number_format($total,2) );
					$this->Cell(15, 4, $total_label, 1, 1, "R");
				}
				$this->Cell(80, 4, "TOTAL CASH: ", 0, 0, "L");
				$this->Cell(15, 4, number_format($total_misc,2), 0, 1, "R");
				$this->total_payment+=$total_misc;
			} else {
				$this->Cell(95, 4, "No cash items for this center.", 1, 1, "L");
			}
		}else {
			echo $db->ErrorMsg();
			echo "<br>LAST QUERY: ".$sql;
			exit();
		}
	}


}
$rep = new RepGen_Clinic_Requests_Slip($_GET['encounter_nr']);
$rep->AliasNbPages();
$rep->Report();

