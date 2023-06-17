<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_jasperReporting.php');
require($root_path.'/modules/repgen/repgenclass.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Cashier_DailyCollectionPerAccount extends RepGen {
	var $encoder;
	var $date;
	var $shift_start;
	var $shift_end;
	var $detailed;
	var $account_name;

	function RepGen_Cashier_DailyCollectionPerAccount ($encoder="", $date=FALSE, $acct_id=FALSE, $or_from=FALSE, $or_to=FALSE) {
		global $db;
		$this->RepGen("CASHIER",'P','Letter');
		#$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
		# 196
		$this->ColumnWidth = array(25,59,20,20,20,37,15);
		$this->Columns = 5;
		$this->ColumnWidth = array(32,25,32,67,40);
		$this->Alignment = array('C','C','R','L','C');
		$this->PageOrientation = "P";
		$this->ColumnLabels = array(
			'DATE/TIME',
			'O.R. #',
			'AMOUNT',
			'NAME',
			'NOTES'
		);
		if ($date) $this->date=date("Y-m-d",strtotime($date));
		else $this->date=date("Y-m-d");

		$this->encoder=$encoder;
		$this->account_id=$acct_id;
		$this->or_from=$or_from;
		$this->or_to=$or_to;

		$this->RowHeight = 4.5;
		$txtpad = 0.2;
		$this->TextPadding = array('T'=>$txtpad, 'B'=>$txtpad, 'L'=>$txtpad, 'R'=>$txtpad);
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

		// if ($row = $objInfo->getAllHospitalInfo()) {
		// 	$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		// 	$row['hosp_name']   = strtoupper($row['hosp_name']);
		// }
		// else {
		// 	$row['hosp_country'] = "Republic of the Philippines";
		// 	$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
		//} removed as per gmmci request

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

		$this->account_name = strtoupper($db->GetOne("SELECT formal_name FROM seg_pay_accounts WHERE id=".$db->qstr($_GET['type'])));
		$this->Cell($total_w,4,"DAILY COLLECTION ('".$this->account_name."')",$border2,1,'C');

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
		$this->FONTSIZE=8;
	}

	function BeforeData() {
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
	}

	function BeforeCellRender() {
		$this->FONTSIZE = 8;
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

		$dialysisCodeName = 'mdc';
		$amount_com = "(SUM(pr.amount_due) - pay.discount_tendered)";
		$ERPatient = "";
		if($this->account_id == $dialysisCodeName){
			$amount_com = "SUM(pr.amount_due)";
			$ERPatient = " INNER JOIN care_encounter AS ce ON ce.`encounter_nr` = pay.`encounter_nr` AND (ce.`encounter_type` IN ('1'))";
		}
		$sql = "SELECT\n".
				"fn_get_pay_account_type(pr.ref_source, pr.ref_no, pr.service_code, pr.or_no) AS account,\n".
				"pay.or_no,pay.or_date,pay.or_name,IF(pay.cancel_date IS NULL,pay.amount_due,0) AS amount_due,\n".
				"pay.cancel_date,pay.cancelled_by,\n".
				"".$amount_com." AS `amount`,(cancel_date IS NOT NULL) AS is_cancelled\n".
			"FROM seg_pay_request AS pr\n".
				"INNER JOIN seg_pay AS pay ON pr.or_no=pay.or_no\n".
				$ERPatient;

		$where = array();
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
		}

		if ($this->or_from)
			$where[] = "CAST(pay.or_no AS UNSIGNED) >= ".$db->qstr($this->or_from);

		if ($this->or_to)
			$where[] = "CAST(pay.or_no AS UNSIGNED) <= ".$db->qstr($this->or_to);

		if ($this->encoder) {
			$where[]="pay.create_id=".$db->qstr($this->encoder);
		}

		$dialysisCodesFilter = '';
		if($this->account_id == $dialysisCodeName){
			$dialysisSQL = "SELECT value from care_config_global WHERE type='dialysis_report_codes'";
			$dialysis=$db->GetOne($dialysisSQL);
			$dialysisCodesFilter = " AND SUBSTRING(pr.service_code,1,LENGTH(pr.service_code)-1) IN (".$dialysis.") ";
		}

		if ($where)
			$sql .= "WHERE (".implode(") AND (",$where).")".$dialysisCodesFilter." GROUP BY or_no\n";
		$sql .= "HAVING (SELECT parent_account FROM seg_pay_subaccounts WHERE id=`account`)=".$db->qstr($this->account_id)."\n";

		$sql .= "ORDER BY pay.or_no ASC";
		// var_dump($sql); die();
		$data = array();
		$result=$db->Execute($sql);
		$this->_count=0;
		$this->_total = 0;

		if ($result) {
			$this->Data=array();
			$i = 0;
			while ($row=$result->FetchRow()) {
				$this->Data[]=array(
					date("m/d h:ia",strtotime($row['or_date'])),
					$row['or_no'],
					($row['is_cancelled']!='1') ? number_format($row['amount'],2) : '0.00',
					strtoupper($row['or_name']),
					($row['is_cancelled']=='1' ? 'Cancelled' : '')
				);
				if ($row['is_cancelled']!='1') $this->_total+=$row['amount'];


				if($_GET['isPDF']){
					$data[$i] = array(
						  'or_date'=>date("m/d h:ia",strtotime($row['or_date'])),
			              'or_no'=>$row['or_no'],
			              'amount'=>($row['is_cancelled']!='1') ? number_format($row['amount'],2) : '0.00',
			              'or_name'=>strtoupper($row['or_name']),
			              'is_cancelled'=>($row['is_cancelled']=='1' ? 'Cancelled' : ''),
	             );

				$i++;

				}
				$this->_count++;

			}
#			die(print_r($row,TRUE));
		} else {

			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}

		// var_dump($_GET['isPDF']); die();
		if($_GET['isPDF']){
			// $this->acc

			$this->account_name = strtoupper($db->GetOne("SELECT formal_name FROM seg_pay_accounts WHERE id=".$db->qstr($_GET['type'])));
			$title = "DAILY COLLECTION (".$this->account_name.")";
			$encoder = "";

			if ($this->encoder) {
				$sql = "SELECT name FROM care_users WHERE login_id=".$db->qstr($this->encoder);
				$this->encoderName = $db->GetOne($sql);
			}

			if ($this->encoder){
				$encoder = $this->encoderName;
			}
			else{
				$encoder = "All encoders";
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

			$params = array(
				"hosp_country"=>$row['hosp_country'],
	            "hosp_agency"=>$row['hosp_agency'],
	            "hosp_name"=>$row['hosp_name'],
	            "hosp_addr1"=>$row['hosp_addr1'],
	            "title"=>$title,
	            "encoder"=>$encoder,
	            "date_time"=>date("F j, Y",strtotime($this->date)),
	            "sum_amount" => number_format($this->_total,2)
	           );
			// var_dump($params); die();
			if(count($data) < 1){
				$data[0]['or_date'] = "No data";
			}
			showReport('Dialysis',$params,$data,"Excel");
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
		$this->Cell($this->ColumnWidth[1], $this->RowHeight, strtoupper("OR used : $this->_count"), 1, 0, 'C', 1);
		$this->Cell($this->ColumnWidth[2], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
		$this->Cell($this->ColumnWidth[3]+$this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);


		$this->Cell(0,5,'',0,1);

		$this->CM = TRUE;

		$this->Data = $this->DataCM;
		$this->_total = $this->_totalCM;
		$this->_count = $this->_countCM;

	}
}
$rep =& new RepGen_Cashier_DailyCollectionPerAccount($_GET['encoder'],$_GET['date'],$_GET['type'],$_GET['orfrom'],$_GET['orto']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>