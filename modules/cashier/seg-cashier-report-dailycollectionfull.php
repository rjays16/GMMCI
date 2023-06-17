<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require './roots.php';
require $root_path.'include/inc_environment_global.php';
require $root_path.'/modules/repgen/repgenclass.php';
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Cashier_DailyIncomeFull extends RepGen {
	var $encoder;
	var $date;
	var $shift_start;
	var $shift_end;
	var $detailed;

	var $startDate;
	var $endDate;
	var $startTime;
	var $endTime;

	var $types;

	private $typeMap = null;

	function RepGen_Cashier_DailyIncomeFull ($typeAcct="", $encoder="", $date_start=FALSE, $time_start=FALSE, $date_end=FALSE, $time_end=FALSE, $or_from=FALSE, $or_to=FALSE, $detailed=FALSE) {
		global $db;


		$query= "SELECT sa.id, sa.short_name `name`, pa.short_name `parent` FROM seg_pay_subaccounts sa\n".
			"INNER JOIN seg_pay_accounts pa ON pa.id=sa.parent_account\n".
			"WHERE sa.parent_account=".$db->qstr($typeAcct)."\n".
			"ORDER BY pa.priority, sa.priority";
		$rs = $db->Execute($query);
		$types = $rs->GetRows();

		$this->typeMap = array();
		foreach ($types as $type)
		{
			$this->typeMap[$type['id']] = $type['name'];
		}

		if (count($types) >= 3)
		{
			$this->RepGen("CASHIER",'P','Legal');
			$this->PageOrientation = 'L';
			$W = 335.75;

			$this->Columns = 4+count($this->typeMap);
			$widthAccts = ($W - ($W*0.05 + $W*0.09 + $W*0.18 + $W*0.1)) / count($this->typeMap);

			$this->ColumnWidth = array_merge(
				array($W*0.05, $W*0.09, $W*0.18),
				array_fill(0, count($this->typeMap), $widthAccts),
				array($W*0.1)
			);
		}
		else
		{
			$this->RepGen("CASHIER",'L','Legal');
			$this->PageOrientation = 'P';
			$W = 196.0625;

			$this->Columns = 4+count($this->typeMap);
			$widthAccts = ($W - ($W*0.1 + $W*0.16 + $W*0.27 + $W*0.15)) / count($this->typeMap);

			$this->ColumnWidth = array_merge(
				array($W*0.1, $W*0.16, $W*0.27),
				array_fill(0, count($this->typeMap), $widthAccts),
				array($W*0.15)
			);
		}

		$this->TextPadding=array('T'=>'0.25','B'=>'0.25','L'=>'0.25','R'=>'0.25');
		$this->TextHeight = 6;

		$this->Alignment = array_merge(
			array('C','C','L'),
			array_fill(0, count($this->typeMap),'R'),
			array('R')
		);
		$this->ColumnLabels = array_merge(
			array('O.R. #', 'DATE/TIME', 'NAME OF PAYOR'),
			array_values($this->typeMap),
			array('TOTAL')
		);

		if ($date_start)
			$this->startDate = date("Ymd",strtotime($date_start));
		else
			$this->startDate = date("Ymd");

		if ($date_end)
			$this->endDate = date("Ymd",strtotime($date_end));
		else
			$this->endDate = $this->startDate;

		$this->type = $typeAcct;
		$this->orFrom = $or_from;
		$this->orTo = $or_to;
		$this->encoder=$encoder;

		$this->detailed=$detailed;

		$this->startTime=$time_start;
		if (!$this->startTime)
			$this->startTime = "000000";

		$this->endTime=$time_end;
		if (!$this->endTime)
			$this->endTime = "235959";

		$this->endTime=$this->endTime."0";

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

//		$this->Cell(0,3,'',1,1,'C');
//		$this->Cell(335.75,3,'',1,1,'C');


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


		$account = $db->GetOne('SELECT formal_name FROM seg_pay_accounts WHERE id='.$db->qstr($this->type));
		//echo 'SELECT formal_name FROM seg_pay_accounts WHERE id='.$db->qstr($this->type);

		$this->SetFont('Arial','B',10);
		#$this->Cell(19,6);
		$this->Cell($total_w,4,'DAILY COLLECTION REPORT ('.$account.')',$border2,1,'C');

		$this->SetFont('Arial','B',9);
		#$this->Cell(19,3);
		if ($this->encoder)
			$this->Cell($total_w,4,$this->encoderName,$border2,1,'C');
		else
			$this->Cell($total_w,4,"All encoders",$border2,1,'C');

		$this->SetFont('Arial','B',9);
/*		$end_date = $this->endDate." ".$this->endTime."0";
		echo "start=".$end_date;
		echo "date=".date("M j, Y h:ia", strtotime($end_date));

		//echo "startdate=".$this->startDate." endDate=".$this->endDate." starttime=".$this->startTime." endtime=".$this->endTime;
		die("die"); */
		$this->Cell(0,4,
			date("M j, Y h:ia",strtotime($this->startDate." ".$this->startTime))." to ".
			date("M j, Y h:ia",strtotime($this->endDate." ".$this->endTime)),$border2,1,'C');


		$this->Ln(4);

		if (!$this->NoHeader) {
			# Print table header

			$this->SetTextColor(0);
			$row=5;
			parent::Header();
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

		$i = 3;
		$totalAmount = 0.0;
		foreach ($this->_total as $total)
		{
			$this->Cell($this->ColumnWidth[$i], $this->RowHeight, number_format($total,2), 1, 0, 'R', 1);
			$totalAmount += (float) $total;
			$i++;
		}

		$this->Cell($this->ColumnWidth[$i++], $this->RowHeight, number_format($totalAmount,2), 1, 0, 'R', 1);
		$this->Cell($this->ColumnWidth[$i++], $this->RowHeight, "", 1, 0, '', 1);


		$this->Cell(0,5,'',0,1);

		$this->CM = TRUE;

		$this->Data = $this->DataCM;
		$this->_total = $this->_totalCM;
		$this->_count = $this->_countCM;

	}

	function Footer()	{
		$this->SetY(-18);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function FetchData() {
		global $db;

		$query = "SELECT\n".
				"pay.or_no,pay.or_date,pay.or_name,\n".
				"pay.cancel_date,pay.cancelled_by,\n".
				"fn_get_pay_account_type(pd.ref_source, pd.ref_no, pd.service_code, pd.or_no) `account`,\n".
				"SUM(IF(pay.cancel_date IS NULL,pd.amount_due,0.0)) amount,\n".
				"(pay.cancel_date IS NOT NULL) is_cancelled\n".
			"FROM seg_pay_request pd\n".
				"INNER JOIN seg_pay pay ON pd.or_no=pay.or_no\n";

		$where = array();
		$having = array();

		$where[] = "pay.or_date BETWEEN ".$db->qstr($this->startDate.$this->startTime)." AND ".$db->qstr($this->endDate.$this->endTime);

		if ($this->orFrom)
			$where[] = "CAST(pay.or_no AS UNSIGNED) >= ".$db->qstr($this->orFrom);

		if ($this->orTo)
			$where[] = "CAST(pay.or_no AS UNSIGNED) <= ".$db->qstr($this->orTo);

		if ($this->encoder)
		{
			$where[]="create_id=".$db->qstr($this->encoder);
		}
		if ($this->type)
		{
			$having[] = "`account` IN ('".implode("','", array_keys($this->typeMap))."')";
		}

		if ($where)
		{
			$query .= "WHERE (".implode(") AND (",$where).")\n";
		}
		$query .= "GROUP BY `or_no`,`account`\n";
		if ($having)
		{
			$query .= "HAVING (".implode(") AND (",$having).")\n";
		}
		$query .= "ORDER BY pay.or_no ASC";


		//echo $query;
		$result=$db->Execute($query);

		$typeKeyMap = array_keys($this->typeMap);
		$this->_total = array_fill(0, count($typeKeyMap), '');
		if ($result) {

			$rows = $result->GetRows();


			$data = array();

			foreach ($rows as $row)
			{
				$orNo = $row['or_no'];
				if (!$data[$orNo])
				{
					/**
					*
					*  do an array merge of the first 3 columns, which remains constant per OR number
					* and a whitespace-filled array corresponding to the number of account types to be
					* shown plus one (representing the last fixed column, i.e., Notes column)
					*
					*/

					$data[$orNo] = array_merge(
						array($orNo, $row['or_date'], $row['or_name']),
						array_fill(0, count($typeKeyMap), '0.00'),
						array(($row['is_cancelled']=='1' ? 'Cancelled' : 0))
					);

				}

				/**
				*  if the row is already set up, we retrieve the type of account for this row and
				* determine the column offset in the row entry to insert the data. Offset is determined
				* by the ordering assigned in the $typeKeyMap variable
				*
				*/
				$offset = array_search($row['account'], $typeKeyMap) + 3;
				$data[$orNo][$offset] = number_format($row['amount'], 2);

				if (is_numeric($data[$orNo][count($data[$orNo])-1]))
					$data[$orNo][count($data[$orNo])-1] += $row['amount'];

				$this->_total[$offset-3] += (float)$row['amount'];


			}

			$this->Data = array_values($data);
			foreach ($this->Data as $i=>$v)
			{
				if ($v[count($v)-1] !== 'Cancelled')
					$v[count($v)-1] = number_format($v[count($v)-1],2);
				$this->Data[$i] = $v;
			}
//			foreach ($this->Data as $i=>$datum)
//			{

//				if ($datum[count($datum)-1] !== 'Cancelled')
//				{
					//$shift_datum = $datum;
//					$datum[count($datum)-1] = array_sum(array_slice($datum, 3));
//				}
//				$this->Data[$i] = $datum;
//			}

//			$this->Data=array();

//			while ($row=$result->FetchRow()) {
//				$account = array();
//				$collection = array();
//				$a_types = explode("\n",$row['a_types']);
//				foreach ($a_types as $i=>$type) {
//					$type_arr = explode('|',$type);
//					if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
//					if (!in_array($type_arr[0],$account)) $account[] = $type_arr[0];
//					if (!in_array($type_arr[1],$collection)) $collection[] = $type_arr[1];
//				}

//				$this->Data[]=array(
//					$row['or_no'],
//					date("m/d h:ia",strtotime($row['or_date'])),
//					$row['or_name'],
//					number_format($row['hoi'],2),
//					number_format($row['meds'],2),
//					number_format($row['consigned'],2),
//					number_format($row['payward'],2),
//					number_format($row['ctscan'],2),
//					number_format($row['aff'],2),
//					number_format($row['bloodcenter'],2),
//					number_format($row['amount_due'],2),
//					($row['is_cancelled']=='1' ? 'Cancelled' : '')
//				);

//				if ($row['is_cancelled']!='1') {
//					$this->_total['hoi'] += (float) $row['hoi'];
//					$this->_total['meds'] += (float) $row['meds'];
//					$this->_total['consigned'] += (float) $row['consigned'];
//					$this->_total['payward'] += (float) $row['payward'];
//					$this->_total['ctscan'] += (float) $row['ctscan'];
//					$this->_total['aff'] += (float) $row['aff'];
//					$this->_total['bloodcenter'] += (float) $row['bloodcenter'];

//					$this->_total['amount_due'] += (float) $row['amount_due'];

//					if ((float)$row['refund_amount']>0) {
//						$this->Data[]=array(
//							"Refund",
//							"",
//							"",
//							"-".number_format($row['hoi_refund'],2),
//							"-".number_format($row['meds_refund'],2),
//							"-".number_format($row['consigned_refund'],2),
//							"-".number_format($row['payward_refund'],2),
//							"-".number_format($row['ctscan_refund'],2),
//							"-".number_format($row['aff_refund'],2),
//							"-".number_format($row['bloodcenter_refund'],2),
//							"-".number_format($row['refund_amount'],2),
//							""
//						);

//						$this->_total['hoi'] -= (float) $row['hoi_refund'];
//						$this->_total['meds'] -= (float) $row['meds_refund'];
//						$this->_total['consigned'] -= (float) $row['consigned_refund'];
//						$this->_total['payward'] -= (float) $row['payward_refund'];
//						$this->_total['ctscan'] -= (float) $row['ctscan_refund'];
//						$this->_total['aff'] -= (float) $row['aff_refund'];
//						$this->_total['bloodcenter'] -= (float) $row['bloodcenter_refund'];
//					}
//				}
//			}
			$this->_count = count($this->Data);
//			print_r(count($this->Data));
//			exit;
		}
		else {
			echo "<pre>", $query, "</pre>";
			print_r($db->ErrorMsg());
			exit;
			# Error
		}
	}
}
$rep =& new RepGen_Cashier_DailyIncomeFull($_GET['type'],$_GET['encoder'],$_GET['datestart'],$_GET['timestart'],$_GET['dateend'],$_GET['timeend'],$_GET['orfrom'],$_GET['orto']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>