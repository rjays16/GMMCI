<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Cashier_DailyIncome extends RepGen {
	var $encoder;
	var $date;
	var $shift_start;
	var $shift_end;
	var $detailed;

	function RepGen_Cashier_DailyIncome ($encoder="", $date=FALSE, $shift_start=FALSE, $shift_end=FALSE, $or_from=FALSE, $or_to=FALSE, $detailed=FALSE) {
		global $db;
		$this->RepGen("CASHIER",'P','Letter');
		#$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
		$this->ColumnWidth = array(25,59,20,20,20,37,15);
		$this->Alignment = array('C','L','R','R','R','L','L');
		$this->PageOrientation = "P";
		$this->Headers = array(
			'DATE/TIME',
			'NAME OF PAYOR',
			'O.R. #',
			'AMOUNT',
			'ACC TYPE',
			'COLLECTION',
			'NOTES'
		);
		if ($date) $this->date=date("Y-m-d",strtotime($date));
		else $this->date=date("Y-m-d");
		$this->or_from = $or_from;
		$this->or_to = $or_to;
		$this->encoder=$encoder;
		$this->detailed=$detailed;
		$this->shift_start=$shift_start;
		$this->shift_end=$shift_end;
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

		#$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,8,15);
		$this->SetFont("Arial","I","8");
		$total_w = 0;
		#$this->Cell(19,3);
		$this->Cell($total_w,3,'Republic of the Philippines',$border2,1,'C');
		#$this->Cell(19,3);
		$this->Cell($total_w,3,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Ln(1);
		$this->SetFont("Arial","B","9");
		#$this->Cell(19,4);
		$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->SetFont("Arial","B","8");
		#$this->Cell(19,5);
		$this->Cell($total_w,3,'CASHIER\'S OFFICE',$border2,1,'C');
		$this->Ln(2);

		$this->SetFont('Arial','B',10);
		#$this->Cell(19,6);
		$this->Cell($total_w,4,'DAILY COLLECTION REPORT',$border2,1,'C');

		$this->SetFont('Arial','B',9);
		#$this->Cell(19,3);
		if ($this->encoder)
			$this->Cell($total_w,4,$this->encoderName,$border2,1,'C');
		else
			$this->Cell($total_w,4,"All encoders",$border2,1,'C');

		$this->SetFont('Arial','B',9);
		#$this->Cell(19,5);
		if ($this->date) {
			$this->Cell($total_w,4,date("F j, Y",strtotime($this->date)),$border2,1,'C');
		}
		else
			$this->Cell($total_w,4,"All payments",$border2,1,'C');
		$this->Ln(4);

		if (!$this->NoHeader) {
			# Print table header

			$this->SetTextColor(0);
			$row=5;

			if ($this->CM) {
				$this->SetFont('Arial','B',11);
				$this->Cell(0,$row,'CREDIT MEMOS',1,0,'C',1);
				$this->Ln($row);
				$this->SetFont('Arial','B',9);
				$this->Cell($this->ColumnWidth[0],$row,'DATE/TIME',1,0,'C',1);
				$this->Cell($this->ColumnWidth[1],$row,'NAME OF PAYOR',1,0,'C',1);
				$this->Cell($this->ColumnWidth[2],$row,'O.R. #',1,0,'C',1);
				$this->Cell($this->ColumnWidth[3],$row,'REFUND',1,0,'C',1);
				$this->Cell($this->ColumnWidth[4],$row,'ACC TYPE',1,0,'C',1);
				$this->Cell($this->ColumnWidth[5],$row,'COLLECTION',1,0,'C',1);
				$this->Cell($this->ColumnWidth[6],$row,'NOTES',1,0,'C',1);
			}
			else {
				$this->SetFont('Arial','B',11);
				$this->Cell(0,$row,'PAYMENTS',1,0,'C',1);
				$this->Ln($row);
				$this->SetFont('Arial','B',9);

				$this->Cell($this->ColumnWidth[0],$row,'DATE/TIME',1,0,'C',1);
				$this->Cell($this->ColumnWidth[1],$row,'NAME OF PAYOR',1,0,'C',1);
				$this->Cell($this->ColumnWidth[2],$row,'O.R. #',1,0,'C',1);
				$this->Cell($this->ColumnWidth[3],$row,'AMOUNT',1,0,'C',1);
				$this->Cell($this->ColumnWidth[4],$row,'ACC TYPE',1,0,'C',1);
				$this->Cell($this->ColumnWidth[5],$row,'COLLECTION',1,0,'C',1);
				$this->Cell($this->ColumnWidth[6],$row,'NOTES',1,0,'C',1);
			}
			$this->Ln();
		}
	}

	function BeforeCell() {
		$this->FONTSIZE=8;
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

	function AfterData() {
		global $db;

		if (!$this->CM) {
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
			$this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $this->RowHeight, strtoupper("Total No. of O.R. Used : $this->_count"), 1, 0, 'C', 1);
			$this->Cell($this->ColumnWidth[3], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
			$this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
			$this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
			$this->Cell($this->ColumnWidth[6], $this->RowHeight, "", 1, 0, '', 1);


			$this->Cell(0,5,'',0,1);

			$this->CM = TRUE;

			$this->Data = $this->DataCM;
			$this->_total = $this->_totalCM;
			$this->_count = $this->_countCM;

			$this->Report();
		}
		else {
			if (!$this->_count) {
				$this->SetFont('Arial','B',9);
				$this->SetFillColor(255);
				$this->SetTextColor(0);
				$this->Cell(0, $this->RowHeight, "No credit memos found...", 1, 1, 'L', 1);
			}

			$this->SetFont('Arial','B',9);
			if ($this->colored)	$this->SetFillColor(0xEE);
			$this->SetTextColor(0);
			$this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
			$this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $this->RowHeight, strtoupper("Total Memos Issued : $this->_count"), 1, 0, 'C', 1);
			$this->Cell($this->ColumnWidth[3], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
			$this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
			$this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
			$this->Cell($this->ColumnWidth[6], $this->RowHeight, "", 1, 0, '', 1);


			$this->Cell(0,5,'',0,1);
			$this->NoHeader = TRUE;
		}
	}

	function Footer()	{
		$this->SetY(-18);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function FetchData() {
		global $db;
		$sql = "SELECT pay.or_no,pay.or_date,pay.or_name,IF(pay.cancel_date IS NULL,pay.amount_due,0) AS amount_due,
pay.cancel_date,pay.cancelled_by,
(SELECT GROUP_CONCAT(fn_get_account_type(pr.ref_no,pr.ref_source, pr.service_code,'S') SEPARATOR '\\n')
	FROM seg_pay_request AS pr
	WHERE pr.or_no=pay.or_no
) AS a_types, (cancel_date IS NOT NULL) AS is_cancelled
FROM seg_pay AS pay\n";

		$sqlCM = "SELECT m.memo_nr, m.issue_date, m.memo_name, m.refund_amount, m.personnel,
GROUP_CONCAT(fn_get_account_type(md.ref_no,md.ref_source,md.service_code,'S') SEPARATOR '\n') AS a_types
FROM seg_credit_memo_details AS md
LEFT JOIN seg_credit_memos AS m ON m.memo_nr=md.memo_nr\n";

		$where = array();
		$whereCM = array();
		if ($this->date) {
			$where[]="DATE(pay.or_date)='$this->date'";
			$whereCM[]="DATE(m.issue_date)='$this->date'";
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
				$whereCM[] = "m.issue_date>='".date("Y-m-d H:i:s",$start_time)."' AND m.issue_date<='".date("Y-m-d H:i:s",$end_time)."'";
			}
			elseif (is_numeric($this->shift_start)) {
				$start_time = $dTime + $this->shift_start*3600;
				$where[] = "pay.or_date>='".date("Y-m-d H:i:s",$start_time)."' AND pay.or_date<=NOW()";
				$whereCM[] = "m.issue_date>='".date("Y-m-d H:i:s",$start_time)."' AND m.issue_date<=NOW()";
			}
		}

		if ($this->or_from)
			$where[] = "CAST(pay.or_no AS UNSIGNED) >= ".$db->qstr($this->or_from);

		if ($this->or_to)
			$where[] = "CAST(pay.or_no AS UNSIGNED) <= ".$db->qstr($this->or_to);

		if ($this->encoder) {
			$where[]="create_id=".$db->qstr($this->encoder);
			$whereCM[]="m.personnel=".$db->qstr($this->encoder);
		}

		if ($where)
			$sql .= "WHERE (".implode(") AND (",$where).") ORDER BY pay.or_no ASC";
		else $sql .= "ORDER BY pay.or_no ASC";

		if ($whereCM)
			$sqlCM .= "WHERE (".implode(") AND (",$whereCM).")\n";
		$sqlCM .= "GROUP BY m.memo_nr,md.or_no ORDER BY md.or_no";

		$result=$db->Execute($sql);
		$cmResult=$db->Execute($sqlCM);

		$this->_total = 0;
		$this->_totalCM = 0;
		if ($result) {
			$this->Data=array();
			$this->DataCM=array();
			while ($row=$result->FetchRow()) {
				$account = array();
				$collection = array();
				$a_types = explode("\n",$row['a_types']);
				foreach ($a_types as $i=>$type) {
					$type_arr = explode('|',$type);
					if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
					if (!in_array($type_arr[0],$account)) $account[] = $type_arr[0];
					if (!in_array($type_arr[1],$collection)) $collection[] = $type_arr[1];
				}

				$this->Data[]=array(
					date("m/d h:ia",strtotime($row['or_date'])),
					$row['or_name'],
					$row['or_no'],
					number_format($row['amount_due'],2),
					implode(', ',$account),
					implode(', ',$collection),
					($row['is_cancelled']=='1' ? 'Cancelled' : '')
				);
				$this->_total+=$row['amount_due'];
			}
			$this->_count = count($this->Data);

			if ($cmResult) {
				while ($row=$cmResult->FetchRow()) {
					$account = array();
					$collection = array();
					$a_types = explode("\n",$row['a_types']);
					foreach ($a_types as $i=>$type) {
						$type_arr = explode('|',$type);
						if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
						if (!in_array($type_arr[0],$account)) $account[] = $type_arr[0];
						if (!in_array($type_arr[1],$collection)) $collection[] = $type_arr[1];
					}
					$this->DataCM[]=array(
						date("m/d h:ia",strtotime($row['issue_date'])),
						$row['memo_name'],
						$row['or_no'],
						number_format($row['refund_amount'],2),
						implode(', ',$account),
						implode(', ',$collection),
						'C.Memo'
					);
					$this->_totalCM+=$row['refund_amount'];
				}
				$this->_countCM = count($this->DataCM);
			}
		}
		else {
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}
	}
}
$rep =& new RepGen_Cashier_DailyIncome($_GET['encoder'],$_GET['date'],$_GET['shiftstart'],$_GET['shiftend'],$_GET['orfrom'],$_GET['orto']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>