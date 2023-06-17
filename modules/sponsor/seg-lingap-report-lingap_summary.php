<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'/modules/repgen/repgen.inc.php';
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_Lingap_Summary extends RepGen {

	var $accounts;
	var $date_from;
	var $date_to;
	var $total_amount;

	function RepGen_Lingap_Summary($accounts, $datefrom, $dateto)
	{
		global $db;
		$this->RepGen("LINGAP SUMMARY REPORT");
		$this->Headers = array(
			'Date', 'Control Nr', 'Encoder', 'Cost Center', 'Patient Name', 'Service Name','Amount'
		);
		$this->colored = FALSE;
		$this->ColumnWidth = array(28,28,25,40,55,60,25);
		$this->RowHeight = 8;
		$this->Alignment = array('C','L','L','C','L','L','R');
		$this->PageOrientation = "L";
		if ($datefrom) $this->date_from=date("Y-m-d",strtotime($datefrom));
		else $this->date_from=date("Y-m-d");
		if ($dateto) $this->date_to=date("Y-m-d",strtotime($dateto));
		else $this->date_to=date("Y-m-d");

		$this->accounts=$accounts;
		if ($this->colored)
			$this->SetDrawColor(0xDD);
	}

	function Header()
	{
		global $root_path, $db;

		$this->SetFont("Arial","I","9");

		$hospital = new Hospital_Admin();
		$hospitalInfo = $hospital->getAllHospitalInfo();

		$total_w = 0;
		$this->Cell($total_w,4,$hospitalInfo['hosp_country'],$border2,1,'C');
		$this->Cell($total_w,4,$hospitalInfo['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell($total_w,4,$hospitalInfo['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell($total_w,4,$hospitalInfo['hosp_addr1'],$border2,1,'C');
		$this->Ln(6);
		$this->SetFont('Arial','B',12);
		$this->Cell($total_w,4,'LINGAP SUMMARY REPORT',$border2,1,'C');
		$this->SetFont('Arial','B',11);
		if($this->accounts)
		{
			$accounts=array();
			foreach ($this->accounts as $v) {
				$sql = "SELECT name_long FROM seg_cashier_account_types WHERE type_id=".$db->qstr($v);
				$name=$db->GetOne($sql);
				if ($name) {
					$accounts[]=trim($name);
				}
				else
					die($sql);
			}
			$area_name = strtoupper(implode(', ',$accounts));
		}
		else
		{
			$area_name = "ALL ACCOUNTS";
		}

		$this->SetFont('Arial','B',10);
		$this->MultiCell(0,4,$area_name,$border2,'C');

		$this->SetFont('Arial','B',11);
		if($this->date_from && $this->date_to) {
			$this->Cell($total_w,4,date("F j, Y",strtotime($this->date_from))." to ".date("F j, Y",strtotime($this->date_to)),$border2,1,'C');
		}
		$this->Ln(5);
		$this->SetTextColor(0);
		$row=5;

		if (!$this->NoHeader) {
			$this->SetFont('Arial','B',11);
			for ($i=0; $i<sizeof($this->Headers); $i++) {
				$this->Cell($this->ColumnWidth[$i],$this->RowHeight,$this->Headers[$i],1,0,'C',1);
			}
			$this->Ln();
		}
	}

	function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa").".",0,0,'R');
	}

	function BeforeData()
	{
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
		$this->ColumnFontSize = 10;
	}

	function BeforeCellRender()
	{
		$this->FONTSIZE = 9;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}

	function AfterData()
	{
		global $db;
		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(246, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
		$cols = array();
		#----added by CHA, Jan 28, 2010----------
		if($this->GetY()>=187)
		{
			$this->NoHeader = TRUE;
			$this->AddPage('L');
		}

		$this->Ln(5);
		$this->SetFont('Arial','B',9);
		$this->Cell(50, 3, "PREPARED BY: ", 0, 0, 'C', 0);
		$this->Cell(80, 3, "CHECKED BY:", 0, 0, 'C', 0);
		$this->Cell(70, 3, "CERTIFIED CORRECT", 0, 0, 'C', 0);
		$this->Cell(60, 3, "APPROVED BY", 0, 0, 'C', 0);
		$this->Ln(5);
		$this->SetFont('Arial','U',9);
		$this->Cell(50, 3, "CECIL BOK-JAPSON, CPA", 0, 0, 'C', 0);
		$this->Cell(80, 3, "HENRIETTA MACAPUNDAG", 0, 0, 'C', 0);
		$this->Cell(70, 3, "RICARDO S.D. JUSTOL", 0, 0, 'C', 0);
		$this->Cell(60, 3, 'LEOPOLDO J. VEGA', 0, 0, 'C', 0);
		$this->Ln(5);
		$this->SetFont('Arial','',9);
		$this->Cell(50, 3, "ACCOUNTANT III", 0, 0, 'C', 0);
		$this->Cell(80, 3, "CMO LINGAP-IN-CHARGE", 0, 0, 'C', 0);
		$this->Cell(70, 3, "CHIEF ADMIN OFFICER", 0, 0, 'C', 0);
		$this->Cell(60, 3, 'CHIEF OF HOSPITAL', 0, 0, 'C', 0);

		#----end CHA-----------------------------
	}

	function FetchData()
	{
		$this->SetFont('Arial','',9);
		global $db;
		$where=array('NOT is_advance');
		$having=array();
		if($this->accounts) {
			$having[] = "entries.account IN (".implode(',',$this->accounts).")";
		}
		if($this->date_from!=$this->date_to) {
			$where[]="DATE(entry_date) BETWEEN ".$db->qstr($this->date_from)." AND ".$db->qstr($this->date_to);
		}
		else if($this->date_from==$this->date_to)
		{
			$where[]="DATE(entry_date)=".$db->qstr($this->date_from);
		}
		if ($where) {
			$where_clause.="WHERE (".implode(")\n AND (", $where). ")\n";
		}
		if ($having) {
			$having_clause.="HAVING (".implode(")\n AND (", $having). ")\n";
		}

//		$sql=
//			"SELECT entries.*,t.name_short AS `account_name` FROM\n".
//			"(\n".
//			"(SELECT 0 AS `walkin`,le.entry_date,le.control_nr,le.name,\n".
//					"ld.ref_source,ld.service_name,ld.quantity,ld.amount,\n".
//					"fn_get_account_type(ref_no,ref_source,service_code,'N') AS `account`\n".
//				"FROM seg_lingap_entry_details ld\n".
//					"INNER JOIN seg_lingap_entries le ON ld.entry_id=le.entry_id\n".
//				"$where_clause\n".
//			")".
//			"UNION ALL\n".
//			"(SELECT 1 AS `walkin`,le.entry_date,le.control_nr,le.name,\n".
//					"ld.ref_source,ld.service_name,ld.quantity,ld.amount,\n".
//					"fn_get_account_type(ref_no,ref_source,service_code,'N') AS `account`\n".
//				"FROM seg_lingap_walkin_entry_details ld\n".
//					"INNER JOIN seg_lingap_walkin_entries le ON ld.entry_id=le.entry_id\n".
//				"$where_clause\n".
//			")\n".
//			") AS entries\n".
//			"LEFT JOIN seg_cashier_account_types AS t\n".
//				"ON t.type_id=entries.account\n".

		$sql =
			"SELECT entries.*\n".
			"FROM (\n".

		"(SELECT e.entry_date `date`, e.control_nr `control_nr`,\n".
		"IF(e.pid IS NULL, fn_get_walkin_name(e.walkin_pid), fn_get_person_name(e.pid)) `name`,\n".
		"CASE w.accomodation_type\n".
			"WHEN 1 THEN 'Charity'\n".
			"WHEN 2 THEN 'Payward'\n".
			"ELSE 'N/A'\n".
		"END `cost_center`, 'Hospital bill' `service_name`,\n".
		"1 `quantity`, d.amount, e.create_id `encoder`,fn_get_account_type(d.ref_no,'FB',NULL,'N') `account`\n".
		"FROM seg_lingap_entries_bill d\n".
			"INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
			"INNER JOIN seg_billing_encounter b ON b.bill_nr=d.ref_no\n".
			"INNER JOIN care_encounter enc ON enc.encounter_nr=b.encounter_nr\n".
			"LEFT JOIN care_ward w ON w.nr=enc.current_ward_nr\n".
			"LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause)\n".

		"UNION ALL\n".

		"(SELECT e.entry_date `date`, e.control_nr `control_nr`,\n".
			"IF(e.pid IS NULL, fn_get_walkin_name(e.walkin_pid), fn_get_person_name(e.pid)) `name`,\n".
			"a.area_name `cost_center`,p.artikelname `service_name`,\n".
			"d.quantity, d.amount, e.create_id `encoder`,fn_get_account_type(d.ref_no,'PH',d.service_code,'N') `account`\n".
		"FROM seg_lingap_entries_pharmacy d\n".
			"INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
			"INNER JOIN seg_pharma_order_items oi ON oi.refno=d.ref_no AND oi.bestellnum=d.service_code\n".
			"INNER JOIN seg_pharma_orders o ON o.refno=d.ref_no\n".
			"INNER JOIN care_pharma_products_main p ON p.bestellnum=d.service_code\n".
			"INNER JOIN seg_pharma_areas a ON a.area_code=o.pharma_area\n".
			"LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause)\n".

		"UNION ALL\n".

		"(SELECT e.entry_date `date`, e.control_nr `control_nr`,\n".
			"IF(e.pid IS NULL, fn_get_walkin_name(e.walkin_pid), fn_get_person_name(e.pid)) `name`,\n".
			"a.area_name `cost_center`,p.artikelname `service_name`,\n".
			"d.quantity, d.amount, e.create_id `encoder`,fn_get_account_type(d.ref_no,'PH',d.service_code,'N') `account`\n".
		"FROM seg_lingap_entries_pharmacy_walkin d\n".
			"INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
			"INNER JOIN seg_pharma_order_items oi ON oi.refno=d.ref_no AND oi.bestellnum=d.service_code\n".
			"INNER JOIN seg_pharma_orders o ON o.refno=d.ref_no\n".
			"INNER JOIN care_pharma_products_main p ON p.bestellnum=d.service_code\n".
			"INNER JOIN seg_pharma_areas a ON a.area_code=o.pharma_area\n".
			"LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause)\n".

		"UNION ALL\n".

		"(SELECT e.entry_date `date`, e.control_nr `control_nr`,\n".
			"IF(e.pid IS NULL, fn_get_walkin_name(e.walkin_pid), fn_get_person_name(e.pid)) `name`,\n".
			"(CASE l.ref_source WHEN 'LB' THEN 'Laboratory' WHEN 'BB' THEN 'Blood Bank' WHEN 'SPL' THEN 'Special Lab' END) `cost_center`,\n".
			"ls.name `service_name`, d.quantity, d.amount, e.create_id `encoder`,\n".
			"fn_get_account_type(d.ref_no,'LD',d.service_code,'N') `account`\n".
		"FROM seg_lingap_entries_laboratory d\n".
			"INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
			"INNER JOIN seg_lab_servdetails ld ON ld.refno=d.ref_no AND ld.service_code=d.service_code\n".
			"INNER JOIN seg_lab_serv l ON l.refno=d.ref_no\n".
			"INNER JOIN seg_lab_services ls ON ls.service_code=d.service_code\n".
			"LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause)\n".

		"UNION ALL\n".

		"(SELECT e.entry_date `date`, e.control_nr `control_nr`,\n".
			"IF(e.pid IS NULL, fn_get_walkin_name(e.walkin_pid), fn_get_person_name(e.pid)) `name`,\n".
			"dept.name_formal `cost_center`,rs.name `service_name`, d.quantity, d.amount, e.create_id `encoder`,\n".
			"fn_get_account_type(d.ref_no,'RD',d.service_code,'N') `account`\n".
		"FROM seg_lingap_entries_radiology d\n".
			"INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
			"INNER JOIN care_test_request_radio rd ON rd.refno=d.ref_no AND rd.service_code=d.service_code\n".
			"INNER JOIN seg_radio_serv r ON r.refno=d.ref_no\n".
			"INNER JOIN seg_radio_services rs ON rs.service_code=d.service_code\n".
			"INNER JOIN seg_radio_service_groups rg ON rg.group_code=rs.group_code\n".
			"INNER JOIN care_department dept ON dept.nr=rg.department_nr\n".
			"LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause)\n".

		"UNION ALL\n".

		"(SELECT e.entry_date `date`, e.control_nr `control_nr`,\n".
			"IF(e.pid IS NULL, fn_get_walkin_name(e.walkin_pid), fn_get_person_name(e.pid)) `name`,\n".
			"'Miscellaneous' `cost_center`,os.name `service_name`, d.quantity, d.amount, e.create_id `encoder`,\n".
			"fn_get_account_type(NULL,'OTHER',d.service_code,'N') `account`\n".
		"FROM seg_lingap_entries_misc d\n".
			"INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
			"INNER JOIN seg_misc_service_details md ON md.refno=d.ref_no AND md.service_code=d.service_code\n".
			"INNER JOIN seg_misc_service m ON m.refno=d.ref_no\n".
			"INNER JOIN seg_other_services os ON os.alt_service_code=d.service_code\n".
			"LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause)\n".

		") `entries`";

		$sql.=$having_clause;
		$sql.="ORDER BY entries.date ASC";

		$result = $db->Execute($sql);
		if($result)
		{
			$this->_count = $result->RecordCount();
			$this->Data=array();
			while($row=$result->FetchRow())
			{
//				if($row['ref_source']=="LD")  $center="Laboratory";
//				if($row['ref_source']=="OR")  $center="Operating Room";
//				if($row['ref_source']=="PH")  $center="Pharmacy";
//				if($row['ref_source']=="RD")  $center="Radiology";
				$this->Data[]=array(
						date('d-M-Y',strtotime($row['date'])),
						$row['control_nr'],
						$row['encoder'],
						$row['cost_center'],
						strtoupper($row['name']),
						ucwords(strtolower($row['service_name'])),
//						number_format($row['quantity'],4),
						number_format($row['amount'],2)
				);
				$this->total_amount+=((float)$row['amount']);
			}
			$this->Data[]=array(
				"","","","","","TOTAL AMOUNT",number_format($this->total_amount,2)
			);
		}
		else
		{
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
		}
	}
}

$accounts = explode(',',$_GET['account']);
$rep =& new RepGen_Lingap_Summary($accounts, $_GET['datefrom'], $_GET['dateto']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

