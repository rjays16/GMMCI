<?php

ini_set('memory_limit','1024M');
set_time_limit(0);

require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'classes/tcpdf/tcpdf.php';

/**
 * Daily Collection Summary Report
 * @author Alvin Quinones
 */
class DailyCollectionSummary extends TCPDF {
	const FONT_FAMILY = 'helvetica';
	const FIXED_WIDTH = 10;
    const HORIZ_SCALING = 1;

	/**
	 * @var array $info 
	 */
	protected $info = array();
	/**
	 * @var string $account The account code referring to the account that will be reported on
	 */
	protected $account = null;
	/**
	 * @var int $reportDate Unix timestamp of the date that will be used as the basis for the report
	 */
	protected $reportDate = null;
	/**
	 * @var string $seriesStart Start of the OR number series to be included in the report
	 */
	protected $seriesStart = null;
	/**
	 * @var string $seriesEnd End of the OR number series to be included in the report
	 */
	protected $seriesEnd = null;

	protected $data = array();

	/**
	 * 
	 */
	public function __construct() 
	{
		global $db;
		$db->SetFetchMode(ADODB_FETCH_ASSOC);

		parent::__construct();

		$this->setMeta();
		$this->setLayout();
	}

	/**
	 * Sets the OR number series to be included in the report
	 * @param string $start 
	 * @param string $end 
	 * @return void
	 */
	public function setSeries($start, $end) 
	{
		if (!$start xor !$end) {
			throw new Exception('Please specify both START and END of OR# series');
		}
		$this->seriesStart = $start ? $start : null;
		$this->seriesEnd = $end ? $end : null;
	}

	/**
	 * Sets the date that will be the basis for the report
	 * @param string $date description
	 * @return void
	 */
	public function setReportDate($date) 
	{
		if ($date) {
			$this->reportDate = strtotime($date);
		} else {
			$this->reportDate = false;
		}
		if ($this->reportDate === false) {
			$this->info['reportDate'] = null;
		} else {
			$this->info['reportDate'] = date('F j, Y', $this->reportDate);
		}
	}

	/**
	 * 
	 * @param string $encoder
	 * @return void
	 */
	public function setEncoder($encoder)
	{
		global $db;
		if (!$encoder) {
			$this->info['encoderName'] = '-ALL ENCODERS-';
		} else {
			$sql = "SELECT name FROM care_users WHERE login_id=".$db->qstr($this->encoder);
			$this->info['encoderName'] = $db->GetOne($sql);
		}
	}

	/**
	 * 
	 * @param string $account
	 * @return void
	 */
	public function setAccount($account) 
	{
		global $db;
		if (!$account) {
			throw new Exception('Account must be specified');
		}

		$this->account = $account;
		$sql = "SELECT formal_name FROM seg_pay_accounts WHERE id=".$db->qstr($account);
		$this->info['accountName'] = $db->GetOne($sql);

		$sql = "SELECT id,short_name FROM seg_pay_subaccounts WHERE parent_account=".$db->qstr($account);
		$rs = $db->Execute($sql);
		if ($rs !== false) {
			$this->info['subaccounts'] = $rs->GetAssoc();
		} else {
			throw new Exception('Unable to retrieve subaccounts for account [' . $account . ']');
		}
	}

	/**
	 * Description
	 * @return void
	 */
    protected function setMeta()
    {
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Segworks Technologies Corporation');
        $this->SetTitle('Cashier Report: Daily Collection Summary');
        $this->SetSubject('Daily collection summary per account across OR series');
        $this->SetKeywords('report,cashier,daily,collection,account,series');
        
        $info = new Hospital_Admin();
        if ($row = $info->getAllHospitalInfo()) {
            $this->info['hospitalType'] = strtoupper($row['hosp_type']);
            $this->info['hospitalName'] = strtoupper($row['hosp_name']);
            $this->info['hospitalAddress'] = strtoupper($row['hosp_addr1']);
            $this->info['country'] = $row['hosp_country'];
            $this->info['agency'] = $row['hosp_agency'];
        } else {
        	$this->info['country'] = 'Republic of the Philippines';
            $this->info['agency'] = 'DEPARTMENT OF HEALTH';
            $this->info['hospitalName'] = strtoupper("Southern Philippines Medical Center");
            $this->info['hospitalAddress'] = strtoupper("JICA Bldg. J.P. Laurel Bajada, Davao City");
        }
    }

    /**
     * 
     */
    protected function setLayout()
    {
        $dim = $this->getPageSizeFromFormat('A4');

        $this->pageWidth = $dim[0];
        $this->pageHeight = $dim[1];

        $this->setPageFormat($dim, 'P');
        $this->setFont(self::FONT_FAMILY);
        $this->setMargins(40,105.5,40,true);
        $this->setAutoPageBreak(true, 40);
    }

    /**
     * 
     */
    public function Header() 
    {
    	$this->setY(10);
    	$this->setFont(self::FONT_FAMILY, '', 22);
    	$this->Cell(0,10,$this->info['country'],0,1,'C');
    	$this->Cell(0,10,$this->info['agency'],0,1,'C');
    	$this->setFont(self::FONT_FAMILY, 'B', 22);
    	$this->Cell(0,10,$this->info['hospitalName'],0,1,'C');
    	$this->setFont(self::FONT_FAMILY, 'B', 25);
    	$this->Cell(0,10,'Cashier\'s Office',0,1,'C');
    	$this->setFont(self::FONT_FAMILY, 'B', 30);
    	$this->Cell(0,15, sprintf('Daily Collection Summary (%s)', $this->info['accountName']),0,1,'C');
    	$this->setFont(self::FONT_FAMILY, 'B', 25);
    	
    	$info = array();
    	$info[] = $this->info['encoderName'];
    	if ($this->reportDate) {
    		$info[] = $this->info['reportDate'];
		}
		if ($this->seriesStart) {
			$info[] = sprintf('OR# %s - %s', $this->seriesStart, $this->seriesEnd);
		}
		$this->Cell(0,10, implode(' / ', $info),0,1,'C');

		$this->ln(5);
		$this->renderGridHeader();
    }

 	// Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-20);
        // Set font
        $this->SetFont(self::FONT_FAMILY, 'I', 18);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages() . '. Segworks Hospital Information System. Checksum: ' . $this->info['hash'], 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    /**
     * 
     */
    protected function fetchData()
    {
    	global $db;

    	if (!$this->reportDate && !$this->seriesStart) {
    		throw new Exception('Report date OR Series range should be specified');
    	}
    	
    	if ($this->reportDate) {
    		$where[] = "or_date LIKE '" . date('Y-m-d', $this->reportDate) . "%'";
		}

		if ($this->seriesStart) {
			$where[] = sprintf("p.or_no BETWEEN %s AND %s", 
				$db->qstr($this->seriesStart),
				$db->qstr($this->seriesEnd));
		}

		if ($this->encoder) {
			$where[] = "create_id=".$db->qstr($this->encoder);
		}

    	$query = "SELECT p.or_date,p.or_no,r.amount_due,p.discount_tendered,fn_get_pay_account_type(r.ref_source, r.ref_no, r.service_code, r.or_no) `type`\n".
    		"FROM seg_pay p\n".
    			"LEFT JOIN seg_pay_request r ON r.or_no=p.or_no\n".
			"WHERE\n" . "(" . implode(")\n AND (", $where) . ")\n" .
			"ORDER BY or_no ASC";

		$rs = $db->Execute($query);
		if ($rs === false) {
			throw new Exception('Databse query error');
		}

		$data = array();

		$accounts = array_keys($this->info['subaccounts']);
		while ($row = $rs->FetchRow()) {
			if (in_array($row['type'], $accounts)) {
				if (empty($data[$row['or_no']])) {
					$data[$row['or_no']] = array('date' => strtotime($row['or_date']));
				}

				if (empty($data[$row['or_no']][$row['type']])) {
					$data[$row['or_no']][$row['type']] = 0;
				}

				$data[$row['or_no']][$row['type']] += (float) $row['amount_due'];
                $data[$row['or_no']]['discount'] = (float) $row['discount_tendered'];
			}
		}

		$this->data = $data;
		$this->info['hash'] = sha1(json_encode($array));
    }

    /**
     * 
     */
    protected function renderGridHeader()
    {

		$htmlTemplate = '<table border="1" cellpadding="4">{{html}}</table>';
		$headerTemplate = '<th width="{{width}}" align="center" valign="middle" colspan="{{colspan}}" rowspan="{{rowspan}}">{{content}}</th>';
        
        $headersHtml = array(
    		array(),
    		array()
    	);

        $fixedWidth = self::FIXED_WIDTH;
        $headersHtml[0][] =strtr($headerTemplate, array(
    		'{{width}}' => $fixedWidth . '%',
    		'{{content}}' => 'DATE',
    		'{{colspan}}' => 1,
    		'{{rowspan}}' => 2
    	));

		$headersHtml[0][] =strtr($headerTemplate, array(
    		'{{width}}' => $fixedWidth . '%',
    		'{{content}}' => 'OR#',
    		'{{colspan}}' => 1,
    		'{{rowspan}}' => 2
    	));

    	$headersHtml[0][] =strtr($headerTemplate, array(
    		'{{width}}' => $fixedWidth . '%',
    		'{{content}}' => 'Amount',
    		'{{colspan}}' => 1,
    		'{{rowspan}}' => 2
    	));

    	$remainderWidth = 100-($fixedWidth*3);
		
		$headersHtml[0][] =strtr($headerTemplate, array(
    		'{{width}}' => $remainderWidth . '%',
    		'{{content}}' => 'Classification',
    		'{{colspan}}' => sizeof($this->info['subaccounts']),
    		'{{rowspan}}' => 1
    	));

        foreach ($this->info['subaccounts'] as $subAccount) {
            $headersHtml[1][] = strtr($headerTemplate, array(
                '{{width}}' => $remainderWidth/sizeof($this->info['subaccounts']) . '%',
                '{{content}}' => $subAccount,
                '{{colspan}}' => 1,
                '{{rowspan}}' => 1,
            ));
        }

        $this->setFont(self::FONT_FAMILY, 'B', 20);
        $this->writeHTML(
            strtr($htmlTemplate, array(
                '{{html}}' => '<tr>'.implode('',$headersHtml[0]).'</tr><tr>'.implode('',$headersHtml[1]).'</tr>'
            )),
            false, false, false, false, ''
        );
    }

    /**
     * 
     */
    protected function renderDatagrid()
    {
        $this->setFont(self::FONT_FAMILY, '', 20);
        $this->setCellPaddings(2,0,2,0);

        $margins = $this->getMargins();
        $w = $this->pageWidth - $margins['left'] - $margins['right'];

        $rowHeight = 10;
        $this->info['_oldDate'] = null;
        $this->info['_currentPage'] = $this->PageNo();

        $cellWidth = $w * (100-(self::FIXED_WIDTH*3))/(100*sizeof($this->info['subaccounts']));

        $totals = array();
        foreach ($this->data as $orNo => $row) {
        	$date = date('d-M-y', $row['date']);
        	// if ($this->info['_oldDate'] == $date && $this->info['_currentPage'] == $this->PageNo()) {
        	// 	$date = '';
        	// } else {
        	// 	$this->info['_oldDate'] = $date;
        	// 	$this->info['_currentPage'] = $this->PageNo();
        	// }

        	$total = 0;
            foreach ($row as $key=>$value) {
            	if ($key !== 'date' && $key !== 'discount') $total += $value;
                if ($key == 'discount') $total -= $value;
            }

        	$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,$date,1,0,'C');
			$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,$orNo,1,0,'C');
			$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,number_format($total,2),1,0,'R');

    		foreach ($this->info['subaccounts'] as $id=>$label) {
    			if (empty($totals[$id])) {
    				$totals[$id] = 0.0;
    			}
                if($row[$id])
                    $subtotal[$id] = $row[$id] - $row['discount'];
    			$totals[$id] += $subtotal[$id];
    			$this->Cell($cellWidth,$rowHeight,(!empty($row[$id]) ? number_format($subtotal[$id],2) : ' '),1,0,'R');
    		}

    		$this->ln($rowHeight);

    		if (empty($totals['total'])) {
    			$totals['total'] = 0.0;
    		}
    		$totals['total'] += $total;
        }

        $this->setFont(self::FONT_FAMILY, 'B', 20);
        $this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,'',1,0,'C');
		$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,'TOTALS',1,0,'C');
		$this->Cell($w*(self::FIXED_WIDTH/100),$rowHeight,number_format($totals['total'],2),1,0,'R');
		
		foreach ($this->info['subaccounts'] as $id=>$label) {
			$this->Cell($cellWidth,$rowHeight, number_format($totals[$id],2),1,0,'R');
		}

    }

    /**
     * Description
     * @return void
     */
	public function report() 
	{
		$this->AddPage();
		$this->setFont(self::FONT_FAMILY, '', 30);
		$this->fetchData();
		$this->renderDatagrid();

		$this->Output('claim_status.pdf', 'I');
	}

}

$report = new DailyCollectionSummary;

try {
	$report->setEncoder($_GET['encoder']);
	$report->setAccount($_GET['account']);
	$report->setSeries($_GET['orfrom'], $_GET['orto']);
	$report->setReportDate($_GET['date']);
	$report->report();
} catch(Exception $e) {
	echo $e->getMessage();
}