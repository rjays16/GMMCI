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

	/*added by mai 09-01-2014*/
	var $total_cash;
	var $total_check;
	var $total_ph;
	var $total_csr;
	var $total_ld;
	var $total_rd;
	var $total_pf;
	var $total_others;
	var $total_refund;
	var $cashier;
	var $jobcashier;
	var $hosp_system;
	/*end added by mai*/
	var $total_amount_tendered;

	private $typeMap = null;

	function RepGen_Cashier_DailyIncomeFull ($typeAcct="", $encoder="", $date_start=FALSE, $time_start=FALSE, $date_end=FALSE, $time_end=FALSE, $or_from=FALSE, $or_to=FALSE, $detailed=FALSE) {
		global $db;

		$this->RepGen("CASHIER",'L','Legal');
		$this->PageOrientation = 'P';

		$this->Columns = 7;
	
		$this->ColumnWidth = array_merge(
			array(20,28,45,12,50,20,20)
		);

		$this->TextPadding=array('T'=>'0.25','B'=>'0.25','L'=>'0.25','R'=>'0.25');
		$this->TextHeight = 6;

		$this->Alignment = array_merge(
			array('C','C','L','C', 'L', 'R', 'L')
		);

		$this->ColumnLabels = array_merge(
			array('O.R. #', 'DATE/TIME', 'NAME OF PAYOR', 'AREA', 'PARTICULARS', 'AMOUNT', 'ENCODER')
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
			$this->endTime = "23000";

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
			$row['hosp_addr1'] = strtoupper($row['hosp_addr1']);
		}
		else {
			/*$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";*/
			$row['hosp_name']    = "CAINGLET MEDICAL HOSPITAL INCORPORATED";
			$row['hosp_addr1']   = "Panabo City";
		}

		// $this->Image('../../../gui/img/logos/gmmci_logo.png',20,10,22,20);

		#$this->Image($root_path.'gui/img/logos/dmhi_logo.jpg',70,8,15);
		$this->SetFont("Arial","I","8");
		$total_w = 0;

//		$this->Cell(0,3,'',1,1,'C');
//		$this->Cell(335.75,3,'',1,1,'C');


		#$this->Cell(19,3);
		/*$this->Cell($total_w,3,$row['hosp_country'],$border2,1,'C');*/
		#$this->Cell(19,3);
		$this->Cell($total_w,3,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(1);
		$this->SetFont("Arial","B","14");
		#$this->Cell(19,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		//$this->SetFont("Arial","B","10");
		#$this->Cell(19,5);
		//$this->Cell($total_w,3,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(1);


		$account = $db->GetOne('SELECT formal_name FROM seg_pay_accounts WHERE id='.$db->qstr($this->type));
		//echo 'SELECT formal_name FROM seg_pay_accounts WHERE id='.$db->qstr($this->type);

		$this->SetFont('Arial','B',10);
		#$this->Cell(19,6);
		$this->Cell($total_w,4,'MONTHLY COLLECTION REPORT ('.$account.')',$border2,1,'C');

		$this->SetFont('Arial','',10);

		$this->Cell(0,4,
			strtoupper(date("M j, Y h:ia",strtotime($this->startDate." ".$this->startTime)))." - ".
			strtoupper(date("M j, Y h:ia",strtotime($this->endDate." ".$this->endTime))),$border2,1,'C');

		#$this->Cell(19,3);
		$this->SetFont('Arial','',10);
		if ($this->encoder)
			$this->Cell($total_w,4,strtoupper($this->encoderName),$border2,1,'C');
		else
			$this->Cell($total_w,4,"ALL ENCODERS",$border2,1,'C');
/*		$end_date = $this->endDate." ".$this->endTime."0";
		echo "start=".$end_date;
		echo "date=".date("M j, Y h:ia", strtotime($end_date));

		//echo "startdate=".$this->startDate." endDate=".$this->endDate." starttime=".$this->startTime." endtime=".$this->endTime;
		die("die"); */
		

		$this->Ln(7);

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

	/*added by mai 09-01-2014*/
	function totalCollection(){
		$this->Columns = 0;

		$this->getRefund();
		$this->getCashier();
		$remaining_cash = $this->total_cash - $this->total_refund;
		$total_cash = $remaining_cash + $this->total_check;

		$this->Ln(20);
		$this->Cell(68, $this->RowHeight, "TOTAL CASH AND CHECK COLLECTION", 1, 1, 'L', 1);
		$this->Cell(68, $this->RowHeight, "", 1, 1, 'L', 1);
		$this->Cell(45, $this->RowHeight, "Total Check Collection", 1, 0, 'L', 1);
		$this->Cell(23, $this->RowHeight, number_format($this->total_check,2), 1, 1, 'R', 1);
		$this->Cell(68, $this->RowHeight, "", 1, 1, 'L', 1);
		
		if($this->_count){
			$this->SetFont("Arial", "", 9);
			$this->Cell(45, $this->RowHeight, "Pharmacy", 1, 0, 'L', 1);
			$this->Cell(23, $this->RowHeight, number_format($this->total_ph, 2), 1, 1, 'R', 1);
			$this->Cell(45, $this->RowHeight, "Supplies", 1, 0, 'L', 1);
			$this->Cell(23, $this->RowHeight, number_format($this->total_csr, 2), 1, 1, 'R', 1);
			$this->Cell(45, $this->RowHeight, "Laboratory", 1, 0, 'L', 1);
			$this->Cell(23, $this->RowHeight, number_format($this->total_ld, 2), 1, 1, 'R', 1);
			$this->Cell(45, $this->RowHeight, "Radiology", 1, 0, 'L', 1);
			$this->Cell(23, $this->RowHeight, number_format($this->total_rd, 2), 1, 1, 'R', 1);
			$this->Cell(45, $this->RowHeight, "Professional Fee", 1, 0, 'L', 1);
			$this->Cell(23, $this->RowHeight, number_format($this->total_pf, 2), 1, 1, 'R', 1);
			$this->Cell(45, $this->RowHeight, "Other Income", 1, 0, 'L', 1);
			$this->Cell(23, $this->RowHeight, number_format($this->total_others, 2), 1, 1, 'R', 1);
			// $this->Cell(45, $this->RowHeight, "Hospital Information System", 1, 0, 'L', 1);
			// $this->Cell(23, $this->RowHeight, number_format($this->hosp_system, 2), 1, 1, 'R', 1);
		}

		$this->SetFont("Arial", "B", 9);
		$this->Cell(68, $this->RowHeight, "", 1, 1, 'L', 1);
		$this->Cell(45, $this->RowHeight, "Total Cash Collection", 1, 0, 'L', 1);
		$this->Cell(23, $this->RowHeight, number_format($this->total_cash,2), 1, 1, 'R', 1);
		$this->SetFont("Arial", "", 9);
		$this->Cell(45, $this->RowHeight, "Less: Refund", 1, 0, 'L', 1);
		$this->Cell(23, $this->RowHeight, number_format($this->total_refund,2), 1, 1, 'R', 1);
		$this->SetFont("Arial", "B", 9);
		$this->Cell(45, $this->RowHeight, "Total Cash Remaining", 1, 0, 'L', 1);
		
		$this->Cell(23, $this->RowHeight, number_format($remaining_cash,2), 1, 1, 'R', 1);
		$this->Cell(68, $this->RowHeight, "", 1, 1, 'L', 1);
		$this->Cell(45, $this->RowHeight, "TOTAL COLLECTION", 1, 0, 'L', 1);
		$this->Cell(23, $this->RowHeight, number_format($total_cash,2), 1, 1, 'R', 1);

		$this->Ln(18);
		$this->SetFont('Arial','',12);
		$this->Cell(68, $this->RowHeight, 'PREPARED BY: ', 0, 1, 'L', 1);
		$this->Ln(10);
		$this->SetFont('Arial','B',12);
		$this->Cell(68, $this->RowHeight, $this->cashier, 0, 1, 'C', 1);
		$this->SetFont('Arial','',10);
		$this->Cell(68, $this->RowHeight, $this->jobcashier, 'T', 1, 'C', 1);
	}

	function getCashier(){
		global $db;
		 $log_id = $_SESSION['sess_temp_userid'];
		 $strSQL = "select pa.location_nr, cp.name_last, cp.name_first, cp.name_middle, cper.job_function_title ".
						"   from care_person as cp inner join (care_users as cu inner join care_personell as cper ".
						"      on cu.personell_nr = cper.nr) on cper.pid = cp.pid ".
                        " INNER JOIN care_personell_assignment pa ON pa.personell_nr=cper.nr ".
						"   where login_id = '".$log_id."'".
                        "  AND cper.STATUS NOT IN ('deleted','hidden','inactive','void')  ";

        if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
                    $sname = strtoupper($row["name_first"] . (is_null($row["name_middle"]) || ($row["name_middle"] == '') ? " " : " ".substr($row["name_middle"],0,1).". ").$row["name_last"]);
					$sjob = strtoupper($row['job_function_title']);
			}
		}

		$this->cashier = $sname;
		$this->jobcashier = $sjob; 
	}

	function getRefund(){
		global $db;

		$query = "SELECT 
				  SUM(refund_amount) AS total_refund
				FROM
				  seg_credit_memos ";

		$where[] = "issue_date BETWEEN DATE_FORMAT(".$db->qstr($this->startDate.$this->startTime).", '%Y-%m-%d %T') AND DATE_FORMAT(".
														$db->qstr($this->endDate.$this->endTime).", '%Y-%m-%d %T' )";
	
		$where[] = "status = 0";

		if ($this->orFrom)
			$where[] = "CAST(pay.or_no AS UNSIGNED) >= ".$db->qstr($this->orFrom);

		if ($this->orTo)
			$where[] = "CAST(pay.or_no AS UNSIGNED) <= ".$db->qstr($this->orTo);

		if ($this->encoder)
		{
			$where[]="personnel=".$db->qstr($this->encoder);
		}
		
		if ($where)
		{
			$query .= "WHERE (".implode(") AND (",$where).")\n";
		}

		$result = $db->Execute($query);
		if($result){
			$rows = $result->FetchRow();
			if($rows){
				$this->total_refund = $rows['total_refund'];
			}
		}
	}

	/*end added by mai*/

	function AfterData() {
		global $db;


		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(20+28+45+12+50+20+20, $this->RowHeight, "No payments found...", 1, 1, 'L', 1);

		}else{ //added by mai 09-01-2014
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(20+28+45+12+50, $this->RowHeight, "TOTAL COLLECTION", 1, 0, 'R', 1);
			$this->Cell(20, $this->RowHeight, number_format($this->total_amount_tendered, 2), 1, 0, 'R', 1);
			$this->Cell(20, $this->RowHeight, "", 1, 1, 'L', 1);
		}

		$this->totalCollection();


		/*$this->SetFont('Arial','B',9);
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
		$this->_count = $this->_countCM;*/

	}

	function Footer()	{
		$this->SetY(-18);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function FetchData() {
		global $db;

		$query = "SELECT 
					  pay.or_date,
					  pay.or_no,
					  pay.or_name,
					  IF(
					    pd.`ref_source` = 'other',
					    'MISC',
					    IF(
					      pd.ref_source = 'PH',
					      (SELECT 
					        IF(
					          prod_class = 'S',
					          'SUP',
					          pd.ref_source
					        ) 
					      FROM
					        care_pharma_products_main 
					      WHERE bestellnum = pd.service_code),
					      pd.ref_source
					    )
					  ) AS ref_source,
					  CASE
					    pd.`ref_source` 
					    WHEN 'PH' 
					    THEN 
					    (SELECT 
					      artikelname 
					    FROM
					      care_pharma_products_main cpm 
					    WHERE cpm.bestellnum = pd.service_code) 
					    WHEN 'MISC' 
					    THEN 
					    (SELECT 
					      sos.name 
					    FROM
					      seg_other_services sos 
					    WHERE sos.alt_service_code = pd.service_code) 
					    WHEN 'LD' 
					    THEN 
					    (SELECT 
					      sls.name 
					    FROM
					      seg_lab_services sls 
					    WHERE sls.service_code = pd.service_code) 
					    WHEN 'RD' 
					    THEN 
					    (SELECT 
					      srs.name 
					    FROM
					      seg_radio_services srs 
					    WHERE srs.service_code = pd.service_code) 
					    WHEN 'OTHER' 
					    THEN 
					    (SELECT 
					      sos.name 
					    FROM
					      seg_other_services sos 
					    WHERE sos.service_code = SUBSTRING(pd.service_code, 1, 8)) 
					    WHEN 'PF' 
					    THEN 
					    (SELECT 
					      CONCAT(
					        'DR. ',
					        cp.name_last,
					        ', ',
					        cp.name_first
					      ) 
					    FROM
					      care_personell cpl 
					      LEFT JOIN care_person cp 
					        ON cp.pid = cpl.pid 
					    WHERE cpl.short_id = pd.service_code) 
					    ELSE 'other'  
					  END AS service_code,
					  (
					      pd.amount_due - (pay.discount_tendered/(SELECT COUNT(*) FROM seg_pay_request WHERE or_no = pay.or_no))
					  ) amount,
					  pay.create_id,
					  pay.create_dt,
					  (pay.cancel_date IS NOT NULL) is_cancelled,
					  pay.cancel_date,
					  pay.cancelled_by,
					  spc.check_no,
					  pay.amount_tendered,
					  pay.amount_due
					FROM
					  seg_pay_request pd 
					  INNER JOIN seg_pay pay 
					    ON pd.or_no = pay.or_no 
					  LEFT JOIN seg_pay_checks spc 
    					ON spc.or_no = pay.or_no ";

		$where = array();
		$having = array();

		$where[] = "pay.or_date BETWEEN DATE_FORMAT(".$db->qstr($this->startDate.$this->startTime).", '%Y-%m-%d %T') AND DATE_FORMAT(".
														$db->qstr($this->endDate.$this->endTime).", '%Y-%m-%d %T' )";
	
		$where[] = "cancel_date IS NULL ";

		if ($this->orFrom)
			$where[] = "CAST(pay.or_no AS UNSIGNED) >= ".$db->qstr($this->orFrom);

		if ($this->orTo)
			$where[] = "CAST(pay.or_no AS UNSIGNED) <= ".$db->qstr($this->orTo);

		if ($this->encoder)
		{
			$where[]="create_id=".$db->qstr($this->encoder);
		}

		if ($where)
		{
			$query .= "WHERE (".implode(") AND (",$where).")\n";
		}
		
		if ($having)
		{
			$query .= "HAVING (".implode(") AND (",$having).")\n";
		}
		$query .= "GROUP BY pay.or_no ORDER BY pay.or_date ASC";
		// echo $query;
		$result=$db->Execute($query);

		$typeKeyMap = array_keys($this->typeMap);
		$this->_total = array_fill(0, count($typeKeyMap), '');
		if ($result) {

			$rows = $result->GetRows();

			$data = array();

			$count = 0;
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

					// $data[$orNo."-".$i++] = array_merge(
					// 	array($orNo, $row['or_date'], $row['or_name'], $row['ref_source'], $row['service_code'], number_format($row['amount'], 2), $row['create_id'])
					// );

					if ($count == 0) {
						if ($row['amount_tendered'] < $row['amount_due']) {
							$amount_due = $row['amount_tendered'];
						}else{
							$amount_due = $row['amount_due'];
						}
						$tempOrNo = $orNo;
						$amount_tendered = $amount_due;
						$count++;
					}

					if (strcmp($orNo, $tempOrNo) == 0) {
						if ($row['amount_tendered'] < $row['amount_due']) {
							$amount_due = $row['amount_tendered'];
						}else{
							$amount_due = $row['amount_due'];
						}
						$amount += $amount_due;
						$particulars .= $row['service_code'].", ";
					} else {
						$tempOrNo = $orNo;
						$amount = $row['amount'];
						$particulars = $row['service_code'];
						
						if ($row['amount_tendered'] < $row['amount_due']) {
							$amount_due = $row['amount_tendered'];
						}else{
							$amount_due = $row['amount_due'];
						}
						$amount_tendered += $amount_due;
						$j++;
					}
					
					$this->total_amount_tendered = $amount_tendered;

					$data[$orNo."-".$j] = array_merge(
						array($orNo, $row['or_date'], $row['or_name'], $row['ref_source'], $particulars, number_format($amount_due, 2), $row['create_id'])
					);

					/*added by mai 09-01-2014*/
					if(!$row['check_no']){
						switch(strtolower($row['ref_source'])){
							case 'ph':
								$this->total_ph += $amount_due;
								break;
							case 'sup':
								$this->total_csr += $amount_due;
								break;
							case 'ld':
								$this->total_ld += $amount_due;
								break;
							case 'rd':
								$this->total_rd += $amount_due;
								break;
							case 'pf':
								$this->total_pf += $amount_due;
								break;
							default:
								if(strtoupper($row['service_code']) == "HOSPITAL INFORMATION SYSTEM" && $row['ref_source'] == "MISC"){
									$this->hosp_system += $row['amount'];
								}else{
									$this->total_others += $row['amount'];
								}
								break;
						}

						$this->total_cash += $amount_due;
					}else{
						$this->total_check += $row['amount'];
					}
					/*end added by mai*/
				}

				/**
				*  if the row is already set up, we retrieve the type of account for this row and
				* determine the column offset in the row entry to insert the data. Offset is determined
				* by the ordering assigned in the $typeKeyMap variable
				*
				*/
				//$offset = array_search($row['account'], $typeKeyMap) + 5;
				//$data[$orNo][$offset] = number_format($row['amount'], 2);

				/*if (is_numeric($data[$orNo][count($data[$orNo])-1]))
					$data[$orNo][count($data[$orNo])-1] += $row['amount'];*/

				//$this->_total[$offset-5] += (float)$row['amount'];


			}

			$this->Data = array_values($data);
			/*foreach ($this->Data as $i=>$v)
			{
				if ($v[count($v)-1] !== 'Cancelled')
					$v[count($v)-1] = number_format($v[count($v)-1],2);
				$this->Data[$i] = $v;
			}*/
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