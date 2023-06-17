<?php

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require($root_path . '/modules/repgen/repgen.inc.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once $root_path . 'include/care_api_classes/sponsor/class_cmap_account.php';

/**
 * SegHIS - Hospital Information System (DMC Deployment)
 * Enhanced by Segworks Technologies Corporation
 */
class RepGen_Cmap_Congressional extends RepGen {

	var $center;
	var $date_from;
	var $date_to;
	var $total_amount;

	function RepGen_Cmap_Congressional($account, $datefrom, $dateto) {
		global $db;
		$this->RepGen("CMAP CONGRESSIONAL REPORT", "L", "LETTER");
		/* $this->Headers = array(
		  'Date', 'CMAP Account', 'Cost Center', 'Patient Name', 'Service Name', 'Quantity', 'Subtotal'
		  ); */
		$this->Headers = array(
			'Patient Name', 'Amt Recom', 'CMAP#', 'Referral#', 'Date', 'Remarks'
		);
		$this->colored = FALSE;
		//$this->ColumnWidth = array(26,30,22,52,81,20,25);
		//$this->ColumnWidth = array(70, 50, 40, 30);
		$this->ColumnWidth = array(50, 25, 20, 40, 25, 30);
		$this->RowHeight = 7;
		//$this->Alignment = array('C','L','C','L','L','C','R');
		$this->Alignment = array('L', 'R', 'C', 'C', 'C', 'L');
		//$this->PageOrientation = "L";
		$this->PageOrientation = "P";
		$this->SetMargins(3, 1, 3);
		if ($datefrom)
			$this->date_from = date("Y-m-d", strtotime($datefrom));
		else
			$this->date_from = date("Y-m-d");
		if ($dateto)
			$this->date_to = date("Y-m-d", strtotime($dateto));
		else
			$this->date_to = date("Y-m-d");
		$this->account = $account;

//		$this->NoWrap = false;
		if ($this->colored)
			$this->SetDrawColor(0xDD);
	}

	function Header() {
		global $root_path, $db;
//	$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',78,10,20);
		$this->SetFont("Arial", "I", "9");

		$hospital = new Hospital_Admin();
		$hospitalInfo = $hospital->getAllHospitalInfo();

		$total_w = 0;
		$this->Cell($total_w, 4, $hospitalInfo['hosp_country'], $border2, 1, 'C');
		$this->Cell($total_w, 4, $hospitalInfo['hosp_agency'], $border2, 1, 'C');
		$this->Ln(2);
		$this->SetFont("Arial", "B", "10");
		$this->Cell($total_w, 4, $hospitalInfo['hosp_name'], $border2, 1, 'C');
		$this->SetFont("Arial", "", "9");
		$this->Cell($total_w, 4, $hospitalInfo['hosp_addr1'], $border2, 1, 'C');
		$this->Ln(6);
		$this->SetFont('Arial', 'B', 12);
		$this->Cell($total_w, 5, 'CMAP RECOM REPORT', $border2, 1, 'C');
		$this->SetFont('Arial', 'B', 12);
		if ($this->account) {
			$sql = "SELECT account_name FROM seg_cmap_accounts WHERE account_nr=" . $db->qstr($this->account);
			$area_name = "CMAP ACCOUNT - " . strtoupper($db->GetOne($sql));
		} else {
			$area_name = "All CMAP accounts";
		}

		$this->Ln(2);
		$this->Cell($total_w, 5, $area_name, $border2, 1, 'C');
		if ($this->date_from && $this->date_to) {
			$this->Cell($total_w, 5, date("F j, Y", strtotime($this->date_from)) . " to " . date("F j, Y", strtotime($this->date_to)), $border2, 1, 'C');
		}
		$this->Ln(4);
		$this->SetTextColor(0);
		$row = 5;
		$this->SetFont('Arial', 'B', 9);

		$this->Cell($this->ColumnWidth[0], $this->RowHeight, $this->Headers[0], 1, 0, 'C', 1);
		$this->Cell($this->ColumnWidth[1], $this->RowHeight, $this->Headers[1], 1, 0, 'C', 1);
		$this->Cell($this->ColumnWidth[2], $this->RowHeight, $this->Headers[2], 1, 0, 'C', 1);
		$this->Cell($this->ColumnWidth[3], $this->RowHeight, $this->Headers[3], 1, 0, 'C', 1);
		$this->Cell($this->ColumnWidth[4], $this->RowHeight, $this->Headers[4], 1, 0, 'C', 1);
		$this->Cell($this->ColumnWidth[5], $this->RowHeight, $this->Headers[5], 1, 0, 'C', 1);
		//$this->Cell($this->ColumnWidth[4],$this->RowHeight,$this->Headers[4],1,0,'C',1);
//	$this->Cell($this->ColumnWidth[5],$this->RowHeight,$this->Headers[5],1,0,'C',1);
//	$this->Cell($this->ColumnWidth[6],$this->RowHeight,$this->Headers[6],1,0,'C',1);
		$this->Ln();
	}

	function Footer() {
		$this->SetY(-23);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(0, 10, 'Page ' . $this->PageNo() . ' of {nb}. Generated: ' . date("Y-m-d h:i:sa"), 0, 0, 'R');
	}

	function BeforeData() {
		if ($this->colored) {
			$this->DrawColor = array(0xDD, 0xDD, 0xDD);
		}
		$this->ColumnFontSize = 10;
	}

	function BeforeCellRender() {
		$this->FONTSIZE = 9;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM % 2) > 0)
				$this->RENDERCELL->FillColor = array(0xee, 0xef, 0xf4);
			else
				$this->RENDERCELL->FillColor = array(255, 255, 255);
		}
	}

	function AfterData() {
		global $db;

		if (!$this->_count) {
			$this->SetFont('Arial', 'B', 9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(246, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		} else {

			$this->Ln(5);
			$this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 0, 0, "L");
			$this->Cell($this->ColumnWidth[0], $this->RowHeight, number_format($this->total_amount, 2), 0, 1, "L");
			$this->Ln(2);
                        $columnwidth = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2];
			$this->Cell($columnwidth, $this->RowHeight, "REMAINING BALANCE AS OF " . strtoupper(date("M Y", strtotime($this->date_to))), 0, 0, "L");

			//$cmap_obj = new SegCmapAccount();
			//$balance = $cmap_obj->getActualBalance($this->account);
			//$remaining = parseFloatEx($balance - $this->total_amount);
			$sql = "SELECT SUM(amount) FROM seg_cmap_allotments WHERE cmap_account=" . $db->qstr($this->account) . " \n" .
					"AND DATE(allotment_date) <= " . $db->qstr($this->date_to);
			$allotments = $db->GetOne($sql);

			$sql = "SELECT SUM(referral_amount) FROM seg_cmap_referrals WHERE cmap_account=" . $db->qstr($this->account) . " \n" .
					"AND DATE(referral_date) <= " . $db->qstr($this->date_to);
			$referrals = $db->GetOne($sql);

			$balance = parseFloatEx($allotments - $referrals);
			$this->Cell($this->ColumnWidth[0], $this->RowHeight, number_format($balance, 2), 0, 1, "L");
		}

		$cols = array();
	}

	function FetchData() {
		$this->SetFont('Arial', '', 8);
		global $db;
		$where_center = "";
		$where_date = "";

		$where = array();
		if ($this->account) {
			$where[] = "r.cmap_account=" . $db->qstr($this->account);
		}
		if ($this->date_from != $this->date_to) {
			$where[] = "(DATE(r.referral_date) BETWEEN " . $db->qstr($this->date_from) . " AND " . $db->qstr($this->date_to) . ")";
		} else if ($this->date_from == $this->date_to) {
			$where[] = "(DATE(r.referral_date)=" . $db->qstr($this->date_from) . ")";
		}
		if ($where)
			$where_clause = "WHERE (\n" . implode(")\n AND (", $where) . ")\n";


		$sql =
				" SELECT IF(r.pid IS NOT NULL, fn_get_person_name(r.pid), \n" .
				"fn_get_walkin_name(r.walkin_pid)) `name`, r.referral_amount `amount`,
				r.control_nr, r.referral_nr, r.referral_date `ref_date`, r.`remarks`
				FROM seg_cmap_referrals r
				LEFT JOIN care_person p ON r.pid=p.pid\n" .
				"LEFT JOIN seg_walkin w ON r.walkin_pid=w.pid \n" .
				$where_clause;

		$sql.="ORDER BY ref_date ASC";
		/* echo "<pre>";
		  print_r($sql);
		  echo "</pre>"; */
		$result = $db->Execute($sql);
		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data = array();
			while ($row = $result->FetchRow()) {
				/* $this->Data[]=array(
				  date('d-M-Y', strtotime($row['edate'])),
				  ucwords(strtolower($row['account_name'])),
				  $row['source'],
				  ucwords(strtolower($row['name'])),
				  ucwords(strtolower($row['service'])),
				  number_format($row['quantity'],2),
				  number_format($row['amount'],2)
				  ); */
				$this->Data[] = array(
					ucwords(strtolower($row['name'])),
					number_format($row['amount'], 2),
					$row['control_nr'] ? $row['control_nr'] : '-',
					$row['referral_nr'] ? $row['referral_nr'] : '-',
					date('d-M-Y', strtotime($row['ref_date'])),
					$row['remarks']
				);
				$this->total_amount+=($row['amount']);
			}
			/* $this->Data[]=array(
			  "TOTAL","","","","","",number_format($this->total_amount,2)
			  ); */
		} else {
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}
	}

}

$rep = new RepGen_Cmap_Congressional($_GET['account'], $_GET['datefrom'], $_GET['dateto']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>