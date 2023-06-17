<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'/modules/repgen/repgenclass.php';
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_Lingap_Summary extends RepGen {

	var $accounts;
	var $dateFrom;
	var $dateTo;
	var $total_amount;

	protected $total;

    const WITH_BORDER = 1;
    const NO_BORDER = 0;
    const NEW_LINE = 1;
    const NO_NEW_LINE = 0;
    const NO_FILL = 0;
    
	function RepGen_Lingap_Summary($datefrom, $dateto, $ep)
	{
		global $db;
		$this->RepGen("LINGAP GRANT TO PATIENT");
//		$this->Headers = array(
//			'', 'Control Nr', 'Encoder', 'Cost Center', 'Patient Name', 'Service Name','Amount'
//		);
		$this->colored = FALSE;
		$this->ColumnWidth = array(0.05, 0.2, 0.2, 0.05, 0.12, 0.38);
		$this->RowHeight = 6;
		$this->Alignment = array('C','L','L','C','L','L');
		$this->PageOrientation = "P";
		if ($datefrom) $this->dateFrom=date("Y-m-d",strtotime($datefrom));
		else $this->dateFrom=date("Y-m-d");
		if ($dateto) $this->dateTo=date("Y-m-d",strtotime($dateto));
		else $this->dateTo=date("Y-m-d");
        $this->isAdvanced = $ep;
		$this->accounts=$accounts;
//		if ($this->colored) {
//			$this->SetDrawColor(0xDD);
//        }
	}

	function Header()
	{
		global $root_path, $db;

        if (empty($this->hasHeader)) {

            $this->hasHeader = true;
		    $hospital = new Hospital_Admin();
		    $hospitalInfo = $hospital->getAllHospitalInfo();

		    $totalWidth = $this->w - $this->lMargin - $this->rMargin;
            $this->totalWidth = $totalWidth;
            
            $this->SetFont("Arial","I","10");
		    $this->Cell($totalWidth,4, $hospitalInfo['hosp_country'],$noBorder, 1,'C');
		    $this->Cell($totalWidth,4, $hospitalInfo['hosp_agency'],$noBorder, 1,'C');
		    $this->Ln(2);
		    $this->SetFont("Arial","B","11");
		    $this->Cell($totalWidth,4,$hospitalInfo['hosp_name'],$noBorder, 1,'C');
		    $this->SetFont("Arial","","10");
		    $this->Cell($totalWidth,4,$hospitalInfo['hosp_addr1'],$noBorder,1,'C');
		    $this->Ln(2);
		    $this->SetFont('Arial','B',11);
            
            if ($this->isAdvanced == 'y') {
                $this->Cell($totalWidth,5,'LINGAP GRANTED TO PATIENT - RECOMMENDATION TO BE FOLLOWED',$noBorder,1,'C');
            } 
            elseif ($this->isAdvanced == 'n') {
                $this->Cell($totalWidth,5,'LINGAP GRANTED TO PATIENT - WITH RECOMMENDATION',$noBorder,1,'C');
            }
            else {
                $this->Cell($totalWidth,5,'LINGAP GRANTED TO PATIENT',$noBorder,1,'C');
            }
		    
		    $this->SetFont('Arial','',11);
            

		    $this->SetFont('Arial','B',11);
		    if($this->dateFrom && $this->dateTo) {
			    $this->Cell($totalWidth,4,date("M d, Y",strtotime($this->dateFrom))." to ".date("M d, Y",strtotime($this->dateTo)),$noBorder,1,'C');
		    }
		    $this->Ln(8);
		    $this->SetTextColor(0);
		    $row = 5;
            
        } else {
            $this->SetFont('Arial','B',9);
            $this->Cell($totalWidth * 0.5, 5, 'LINGAP GRANTED TO PATIENT (cont\'d)', 'B', self::NO_NEW_LINE,'L');
            if($this->dateFrom && $this->dateTo) {
                $this->Cell($totalWidth * 0.5, 5, 
                    date("M d, Y",strtotime($this->dateFrom))."-". date("M d, Y",strtotime($this->dateTo)),
                    $noBorder,
                    self::NEW_LINE,
                    'R'
                );
            }
            $this->Ln(8);
        }

	}

	function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa").".",0,0,'R');
	}
    
    
    
    public function mergeData($data) {
        if (!empty($data) && is_array($data)) {
            foreach ($data as $item) {
                $date = date('Ymd', strtotime(@$item['date']));
                $pid = $item['pid'];
                $key = $date . '_' . $pid;
                if (empty($this->data[$key])) {
                    $item['amount'] = (float) $item['amount'];
                    $item['accounts'] = array($item['cost_center']);
                    $this->data[$key] = $item;
                }
                else {
                    $this->data[$key]['amount'] += (float) $item['amount'];
                    if (!in_array($item['cost_center'], $this->data[$key]['accounts'])) {
                        $this->data[$key]['accounts'][] = $item['cost_center'];
                    }
                }
            }
        }
    }
    
    
    public function query() 
    {
        global $db;
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        
        $where = array();
        $having=array();
        
        if ($this->isAdvanced == 'y' || $this->isAdvanced == 'n') {
            if ($this->isAdvanced == 'y') {
                $where[] = "is_advance=1";
                
            } else {
                $where[] = "is_advance=0";
            }
        }
        
        if($this->dateFrom!=$this->dateTo) {
            $where[] = "DATE(entry_date) BETWEEN " . $db->qstr($this->dateFrom) . " AND " . $db->qstr($this->dateTo);
        }
        else if($this->dateFrom==$this->dateTo)
        {
            $where[] = "DATE(entry_date)=" . $db->qstr($this->dateFrom);
        }
        if ($where) {
            $where_clause .= "WHERE (".implode(")\n AND (", $where) . ")\n";
        }
//        if ($having) {
//            $having_clause.="HAVING (".implode(")\n AND (", $having). ")\n";
//        }

        // billing
        $query = "SELECT e.entry_date `date`, e.control_nr `control_nr`,e.pid, e.walkin_pid,\n".
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
        "$where_clause\n".
        "ORDER BY e.entry_date ASC";
        $entries = $db->GetAll($query);
        $this->mergeData($entries);
        
        
        // Medicines
        $query = "SELECT e.entry_date `date`, e.control_nr `control_nr`,e.pid, e.walkin_pid,\n".
            "a.area_name `cost_center`,p.artikelname `service_name`,\n".
            "d.quantity, d.amount, e.create_id `encoder`,fn_get_account_type(d.ref_no,'PH',d.service_code,'N') `account`\n".
        "FROM seg_lingap_entries_pharmacy d\n".
            "INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
            "INNER JOIN seg_pharma_order_items oi ON oi.refno=d.ref_no AND oi.bestellnum=d.service_code\n".
            "INNER JOIN seg_pharma_orders o ON o.refno=d.ref_no\n".
            "INNER JOIN care_pharma_products_main p ON p.bestellnum=d.service_code\n".
            "INNER JOIN seg_pharma_areas a ON a.area_code=o.pharma_area\n".
            "LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause\n".
        "ORDER BY e.entry_date ASC";
        $entries = $db->GetAll($query);
        $this->mergeData($entries);
        
        
        // Walkin Pharmacy
        $query = "SELECT e.entry_date `date`, e.control_nr `control_nr`,e.pid, e.walkin_pid,\n".
            "a.area_name `cost_center`,p.artikelname `service_name`,\n".
            "d.quantity, d.amount, e.create_id `encoder`,fn_get_account_type(d.ref_no,'PH',d.service_code,'N') `account`\n".
        "FROM seg_lingap_entries_pharmacy_walkin d\n".
            "INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
            "INNER JOIN seg_pharma_order_items oi ON oi.refno=d.ref_no AND oi.bestellnum=d.service_code\n".
            "INNER JOIN seg_pharma_orders o ON o.refno=d.ref_no\n".
            "INNER JOIN care_pharma_products_main p ON p.bestellnum=d.service_code\n".
            "INNER JOIN seg_pharma_areas a ON a.area_code=o.pharma_area\n".
            "LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause\n".
        "ORDER BY e.entry_date ASC";
        $entries = $db->GetAll($query);
        $this->mergeData($entries);
        
        
        // Lab
        $query = "SELECT e.entry_date `date`, e.control_nr `control_nr`,e.pid, e.walkin_pid,\n".
            "(CASE l.ref_source WHEN 'LB' THEN 'Laboratory' WHEN 'BB' THEN 'Blood Bank' WHEN 'SPL' THEN 'Special Lab' END) `cost_center`,\n".
            "ls.name `service_name`, d.quantity, d.amount, e.create_id `encoder`,\n".
            "fn_get_account_type(d.ref_no,'LD',d.service_code,'N') `account`\n".
        "FROM seg_lingap_entries_laboratory d\n".
            "INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
            "INNER JOIN seg_lab_servdetails ld ON ld.refno=d.ref_no AND ld.service_code=d.service_code\n".
            "INNER JOIN seg_lab_serv l ON l.refno=d.ref_no\n".
            "INNER JOIN seg_lab_services ls ON ls.service_code=d.service_code\n".
            "LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause\n".
        "ORDER BY e.entry_date ASC";
        $entries = $db->GetAll($query);
        $this->mergeData($entries);
        
        
        // Radiology
        $query = "SELECT e.entry_date `date`, e.control_nr `control_nr`,e.pid, e.walkin_pid,\n".
            "IF(e.pid IS NULL, fn_get_walkin_name(e.walkin_pid), fn_get_person_name(e.pid)) `name`,\n".
            "dept.name_short `cost_center`,rs.name `service_name`, d.quantity, d.amount, e.create_id `encoder`,\n".
            "fn_get_account_type(d.ref_no,'RD',d.service_code,'N') `account`\n".
        "FROM seg_lingap_entries_radiology d\n".
            "INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
            "INNER JOIN care_test_request_radio rd ON rd.refno=d.ref_no AND rd.service_code=d.service_code\n".
            "INNER JOIN seg_radio_serv r ON r.refno=d.ref_no\n".
            "INNER JOIN seg_radio_services rs ON rs.service_code=d.service_code\n".
            "INNER JOIN seg_radio_service_groups rg ON rg.group_code=rs.group_code\n".
            "INNER JOIN care_department dept ON dept.nr=rg.department_nr\n".
            "LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause\n".
        "ORDER BY e.entry_date ASC";
        $entries = $db->GetAll($query);
        $this->mergeData($entries);
        
        
        // MISC
        $query = "SELECT e.entry_date `date`, e.control_nr `control_nr`,e.pid, e.walkin_pid,\n".
            "'Miscellaneous' `cost_center`,os.name `service_name`, d.quantity, d.amount, e.create_id `encoder`,\n".
            "fn_get_account_type(NULL,'OTHER',d.service_code,'N') `account`\n".
        "FROM seg_lingap_entries_misc d\n".
            "INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
            "INNER JOIN seg_misc_service_details md ON md.refno=d.ref_no AND md.service_code=d.service_code\n".
            "INNER JOIN seg_misc_service m ON m.refno=d.ref_no\n".
            "INNER JOIN seg_other_services os ON os.alt_service_code=d.service_code\n".
            "LEFT JOIN care_users u ON u.login_id=e.create_id\n".
        "$where_clause\n".
        "ORDER BY e.entry_date ASC";
        $entries = $db->GetAll($query);
        $this->mergeData($entries);
        
        ksort($this->data, SORT_STRING);
    }
    
    
    
    

	function BeforeData()
	{
        global $db;
        
//		if ($this->colored) {
//			$this->DrawColor = array(0xDD,0xDD,0xDD);
//		}
//		$this->ColumnFontSize = 10;
        $this->data = array();
        $this->query();
        
        // set width for columns
        $widths = array();
        foreach ($this->ColumnWidth as $i => $w) {
            $widths[$i] = $w * $this->totalWidth;
        }
        
        $oldDate = null;
        $count = 1;
        $this->total = 0;
        foreach ($this->data as $key => $item) {
            $date = substr($key, 0, 8);
            if ($oldDate !== $date)
            {
                $this->SetFont('Arial', 'B', 11);
                if (!is_null($oldDate)) {   // end of the day                    
                    $this->Cell($widths[0], $this->RowHeight, '', self::WITH_BORDER, self::NO_NEW_LINE, 'C', NO_FILL);
                    $this->Cell(array_sum(array_slice($widths, 1, 3)), $this->RowHeight, 'TOTAL', self::WITH_BORDER, self::NO_NEW_LINE, 'C', NO_FILL);
                    $this->Cell($widths[4], $this->RowHeight, number_format($this->total, 2), self::WITH_BORDER, self::NO_NEW_LINE, 'R', NO_FILL);
                    $this->Cell($widths[5], $this->RowHeight, '', self::WITH_BORDER, self::NEW_LINE, 'C', NO_FILL);
                    
                    $this->Ln($this->RowHeight);
                }
                $this->Cell($this->totalWidth, $this->RowHeight, date('F j, Y', strtotime($date)), self::WITH_BORDER, self::NEW_LINE, 'C', NO_FILL);
                $count = 1;
                $oldDate = $date;
            }
            
            $this->SetFont('Arial', '', 10);
            $this->Cell($widths[0], $this->RowHeight, $count++, self::WITH_BORDER, self::NO_NEW_LINE, 'C', NO_FILL);
            
            if (!empty($item['pid'])) {
                $query = "SELECT name_last, name_first, name_middle FROM care_person WHERE pid=" . $db->qstr($item['pid']);
            } else {
                $query = "SELECT name_last, name_first, name_middle FROM seg_walkin WHERE pid=" . $db->qstr($item['walkin_pid']);
            }
            $name = $db->GetRow($query);
            
            $this->Cell($widths[1], $this->RowHeight, $name['name_last'], self::WITH_BORDER, self::NO_NEW_LINE, 'L', NO_FILL);
            $this->Cell($widths[2], $this->RowHeight, $name['name_first'], self::WITH_BORDER, self::NO_NEW_LINE, 'L', NO_FILL);
            $this->Cell($widths[3], $this->RowHeight, empty($name['name_middle']) ? '' : substr($name['name_middle'], 0, 1) . '.', self::WITH_BORDER, self::NO_NEW_LINE, 'L', NO_FILL);
            $this->Cell($widths[4], $this->RowHeight, number_format($item['amount'],2), self::WITH_BORDER, self::NO_NEW_LINE, 'R', NO_FILL);
            $this->Cell($widths[5], $this->RowHeight, implode(', ', $item['accounts']), self::WITH_BORDER, self::NEW_LINE, 'L', NO_FILL);
            
            $this->total += (float)$item['amount'];
        }

		if ($this->total) {
			$this->SetFont('Arial', 'B', 11);
			$this->Cell($widths[0], $this->RowHeight, '', self::WITH_BORDER, self::NO_NEW_LINE, 'C', NO_FILL);
			$this->Cell(array_sum(array_slice($widths, 1, 3)), $this->RowHeight, 'TOTAL', self::WITH_BORDER, self::NO_NEW_LINE, 'C', NO_FILL);
			$this->Cell($widths[4], $this->RowHeight, number_format($this->total, 2), self::WITH_BORDER, self::NO_NEW_LINE, 'R', NO_FILL);
			$this->Cell($widths[5], $this->RowHeight, '', self::WITH_BORDER, self::NEW_LINE, 'C', NO_FILL);
		}

	}

	function BeforeCellRender()
	{
		$this->FONTSIZE = 8;
//		if ($this->colored) {
//			if (($this->RENDERPAGEROWNUM%2)>0)
//				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
//			else
//				$this->RENDERCELL->FillColor=array(255,255,255);
//		}
	}

	function AfterData()
	{
		global $db;
//		if (!$this->_count) {
//			$this->SetFont('Arial','B',9);
//			$this->SetFillColor(255);
//			$this->SetTextColor(0);
//			$this->Cell(246, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
//		}

		$cols = array();
		#----added by CHA, Jan 28, 2010----------
		if($this->GetY() >= ($this->h-65))
		{
			$this->NoHeader = TRUE;
			$this->AddPage('P');
		}

		$this->Ln(8);
        
        
		$this->SetFont('Arial', 'B', 10);
		$this->Cell($this->totalWidth * 0.3, 6, "PREPARED BY: ", 0, 0, 'C', 0);
        $this->Cell($this->totalWidth * 0.4, 6);
		$this->Cell($this->totalWidth * 0.3, 6, "RECEIVED BY", 0, 0, 'C', 0);
		$this->Ln(14);
        
		$nr = $db->GetOne("SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION['sess_temp_userid']));

		$sql = "SELECT fn_get_pid_name(pid) `name`, job_position FROM care_personell WHERE nr=".$db->qstr($nr);
		$personnel = $db->GetRow($sql);

		$this->SetFont('Arial','', 10);
		$this->Cell($this->totalWidth * 0.3, 4, $personnel['name'], 0, 0, 'C', 0);

        
		$this->Ln(4);
		$this->SetFont('Arial','', 10);
		$this->Cell($this->totalWidth * 0.3, 6, $personnel['job_position'], 'T', 0, 'C', 0);
        $this->Cell($this->totalWidth * 0.4, 6);
        $this->Cell($this->totalWidth * 0.3, 6, "Lingap-in-Charge", 'T', 0, 'C', 0);
        
        $this->Ln(12);
        $this->SetFont('Arial','', 10);
        $this->Cell($this->totalWidth * 0.7, 6);
        $this->Cell($this->totalWidth * 0.3, 6, "Date", 'T', 0, 'C', 0);
//		$this->Cell(80, 3, "CMO LINGAP-IN-CHARGE", 0, 0, 'C', 0);
//		$this->Cell(70, 3, "CHIEF ADMIN OFFICER", 0, 0, 'C', 0);
//		$this->Cell(60, 3, 'CHIEF OF HOSPITAL', 0, 0, 'C', 0);

		#----end CHA-----------------------------
	}

	function FetchData()
	{
        $this->Data = array();
    }
}

$accounts = explode(',',$_GET['account']);
$rep =& new RepGen_Lingap_Summary($_GET['datefrom'], $_GET['dateto'], $_GET['ep']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

