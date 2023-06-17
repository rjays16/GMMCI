<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

die('Not implemented yet...');

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once $root_path.'/modules/repgen/repgen.inc.php';
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Cashier_MonthlyCollectionPerAccount extends RepGen {
	var $encoder;
	var $date;
	var $shift_start;
	var $shift_end;
	var $detailed;

	function RepGen_Cashier_MonthlyCollectionPerAccount ($encoder="", $month=FALSE, $year=FALSE, $acct_id=FALSE) {
		global $db;
		$this->RepGen("CASHIER",'P','Letter');
		#$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
		# 196
		$this->ColumnWidth = array(25,59,20,20,20,37,15);
		$this->Columns = 4;
		$this->ColumnWidth = array(46,50,50,50);
		$this->Alignment = array('C','C','R','C');
		$this->PageOrientation = "P";
		$this->Headers = array(
			'DATE/TIME',
			'O.R. #',
			'AMOUNT',
			'NOTES'
		);
		
		$this->month=$month ? $month : date("n");
		$this->year=$year ? $year : date("Y");
		
		$this->encoder=$encoder;
		$this->account_id=$acct_id;
		$this->or_from=$or_from;
		$this->or_to=$or_to;

		$this->RowHeight = 6;
		$this->colored=FALSE;
		if ($this->colored)	$this->SetDrawColor(0xDD);
	}
	
	function Header() {
		global $root_path, $db;
		
		if ($this->encoder) {
			$sql = "SELECT name FROM care_users WHERE login_id=".$db->qstr($this->encoder);
			$this->encoderName = $db->GetOne($sql);
		}
		
    $objInfo = new Hospital_Admin();
    
    if ($row = $objInfo->getAllHospitalInfo()) {      
      $row['hosp_agency'] = strtoupper($row['hosp_agency']);
      $row['hosp_name']   = strtoupper($row['hosp_name']);
    }
    else {
      $row['hosp_country'] = "Republic of the Philippines";
      $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
      $row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
      $row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";      
    }
		
		#$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,8,15);
		$this->SetFont("Arial","I","8");
		$total_w = 0;
		#$this->Cell(19,3);
  	$this->Cell($total_w,3,$row['hosp_country'],$border2,1,'C');
		#$this->Cell(19,3);
	  $this->Cell($total_w,3,$row['hosp_agency'],$border2,1,'C');
  	$this->Ln(1);
		$this->SetFont("Arial","B","9");
		#$this->Cell(19,4);
  	$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","B","8");
		#$this->Cell(19,5);
  	$this->Cell($total_w,3,'CASHIER\'S OFFICE',$border2,1,'C');
		$this->Ln(2);
		
	  $this->SetFont('Arial','B',10);
		#$this->Cell(19,6);
		
		$acct_name = strtoupper($db->GetOne("SELECT name_long FROM seg_pay_accounts WHERE id=".$db->qstr($this->account_id)));
  	$this->Cell($total_w,4,"DAILY COLLECTION ($acct_name)",$border2,1,'C');
		
		$this->SetFont('Arial','B',9);
		#$this->Cell(19,3);
		
		$this->SetFont('Arial','B',11);
		$this->Cell($total_w,4,"for " . date("F Y",strtotime("{$this->month}/1/{$this->year}")),$border2,1,'C');
		
		if ($this->encoder)
	  	$this->Cell($total_w,4,$this->encoderName,$border2,1,'C');
		else
	  	$this->Cell($total_w,4,"All encoders",$border2,1,'C');
		
		#$this->Cell(19,5);
		/*
		if ($this->date) {
	  	$this->Cell($total_w,4,date("F j, Y",strtotime($this->date)),$border2,1,'C');
		}
		else
	  	$this->Cell($total_w,4,"All payments",$border2,1,'C');
		*/
		$this->Ln(4);

		
		$this->SetTextColor(0);
		$row=5;			
		$this->SetFont('Arial','B',11);
		
		/*
		$this->Cell($this->ColumnWidth[0],$row,'DATE/TIME',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'NAME OF PAYOR',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'O.R. #',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'AMOUNT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'ACC TYPE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'COLLECTION',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'NOTES',1,0,'C',1);
		*/		
		parent::Header();
	}
	
	function BeforeCell() {
		$this->FONTSIZE=10;
	}
	
	function BeforeData() {
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
	}
	
	function BeforeCellRender() {
		$this->FONTSIZE = 10;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0) 
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}	
	
	function Footer()	{
		$this->SetY(-18);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}
	
	function FetchData() {		
		global $db;
/*
		$sql = "SELECT pay.or_no,pay.or_date,pay.or_name,IF(pay.cancel_date IS NULL,pay.amount_due,0) AS amount_due,
pay.cancel_date,pay.cancelled_by,
(SELECT GROUP_CONCAT(fn_get_account_type(pr.ref_no,pr.ref_source, pr.service_code,'S') SEPARATOR '\\n') 
	FROM seg_pay_request AS pr
	WHERE pr.or_no=pay.or_no
) AS a_types, (cancel_date IS NOT NULL) AS is_cancelled
FROM seg_pay AS pay\n";
*/
		$sql = "SELECT
fn_get_account_type(pr.ref_no,pr.ref_source, pr.service_code,'N') AS account_type,
pay.or_no,pay.or_date,pay.or_name,IF(pay.cancel_date IS NULL,pay.amount_due,0) AS amount_due,
pay.cancel_date,pay.cancelled_by,
SUM(pr.amount_due) AS `amount`,(cancel_date IS NOT NULL) AS is_cancelled
FROM seg_pay_request AS pr
INNER JOIN seg_pay AS pay ON pr.or_no=pay.or_no
INNER JOIN seg_cashier_account_subtypes cacc ON cacc.type_id=pr.account_type
INNER JOIN seg_pay_subaccounts pacc ON pacc.id=cacc.pay_account
\n";

		$where = array();
		/*
		if ($this->date) {
			$where[]="DATE(pay.or_date)='$this->date'";
			$dTime = strtotime($this->date);
			if (is_numeric($this->shift_start) && is_numeric($this->shift_end)) {
				if ($this->shift_start < $this->shift_end) {
					$start_time = $dTime + $this->shift_start*3600;
					$end_time = $dTime + $this->shift_end*3600;
				}
				else {
					$start_time = $dTime + $this->shift_start*3600;
					$end_time = $dTime + $this->shift_end*3600+86400;
				}
				$where[] = "pay.or_date>='".date("Y-m-d H:i:s",$start_time)."' AND pay.or_date<='".date("Y-m-d H:i:s",$end_time)."'";
			}
			elseif (is_numeric($this->shift_start)) {
				$start_time = $dTime + $this->shift_start*3600;
				$where[] = "pay.or_date>='".date("Y-m-d H:i:s",$start_time)."' AND pay.or_date<=NOW()";
			}
		}*/
		
		if ($this->encoder) {
			$where[]="pay.create_id=".$db->qstr($this->encoder);
		}
		
		$where[] = "MONTH(pay.or_date)=".$db->qstr($this->month)." AND YEAR(pay.or_date)=".$db->qstr($this->year);
        
        if ($this->account_id) {
            $where[] = "pacc.parent_account=".$db->qstr($this->account_id);
        }
		
		if ($where)
			$sql .= "WHERE (".implode(") AND (",$where).") GROUP BY or_no\n";
		//$sql .= "HAVING `account_type`=".$db->qstr($this->account_id)."\n";
		
		$sql .= "ORDER BY pay.or_no ASC";

		$result=$db->Execute($sql);
		$this->_count=0;
		$this->_total = 0;
		if ($result) {
			$this->Data=array();
			while ($row=$result->FetchRow()) {
				$this->Data[]=array(
					date("m-d-Y h:ia",strtotime($row['or_date'])),
					$row['or_no'],
					number_format($row['amount'],2),
					($row['is_cancelled']=='1' ? 'Cancelled' : '')
				);
				$this->_total+=$row['amount'];
				$this->_count++;
			}
#			die(print_r($row,TRUE));
		}
		else {
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}			
	}
	
	function AfterData() {
		global $db;
		
	
		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No payments found...", 1, 1, 'L', 1);
		}
		
		$this->SetFont('Arial','B',9);
		if ($this->colored)	$this->SetFillColor(0xEE);
		
		$this->SetTextColor(0);
		$this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
		$this->Cell($this->ColumnWidth[1], $this->RowHeight, strtoupper("O.R. Used : $this->_count"), 1, 0, 'C', 1);
		$this->Cell($this->ColumnWidth[2], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
		$this->Cell($this->ColumnWidth[3], $this->RowHeight, "", 1, 0, '', 1);
		
		
		$this->Cell(0,5,'',0,1);
		
		$this->CM = TRUE;
		
		$this->Data = $this->DataCM;
		$this->_total = $this->_totalCM;
		$this->_count = $this->_countCM;

	}
}
$rep =& new RepGen_Cashier_MonthlyCollectionPerAccount($_GET['encoder'],$_GET['month'],$_GET['year'],$_GET['type']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>