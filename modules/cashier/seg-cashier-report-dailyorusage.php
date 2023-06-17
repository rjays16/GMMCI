<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Cashier_DailyUsage extends RepGen {
		var $encoder;
		var $encoderName;
		var $type;

		public $startDate;
		public $endDate;

		var $orFrom;
		var $orTo;

	function RepGen_Cashier_DailyUsage ($type, $encoder=FALSE, $date_start=FALSE, $time_start=FALSE, $date_end=FALSE, $time_end=FALSE, $or_from=FALSE, $or_to=FALSE) {
		global $db;
				$this->RepGen("CASHIER",'P','Letter');
		# 196
		#$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
		$this->ColumnWidth = array(60,43,43,50);
		$this->Alignment = array('L','C','C','R');
		$this->PageOrientation = "P";
		$this->Headers = array(
			'ENCODER',
			'O.R. FROM',
			'O.R. TO',
			'AMOUNT'
		);


//		if ($date_start || $date_end)
//		{
//			if ($date_start)
//				$this->startDate = date("Ymd",strtotime($date_start));
//			else
//				$this->startDate = date("Ymd");

//			if ($date_end)
//				$this->endDate = date("Ymd",strtotime($date_end));
//			else
//				$this->endDate = $this->startDate;

//			if (!$time_start)
//				$this->startDate .= "000000";
//			else
//				$this->startDate .= $time_start;

//			if (!$time_end)
//				$this->endDate .= "235959";
//			else
//				$this->endDate .= $time_end;
//		}

		$this->orFrom = $or_from;
		$this->orTo = $or_to;
		$this->encoder = $encoder;

		$this->type = $type;


		$this->RowHeight = 6;
		$this->colored=FALSE;
		if ($this->colored)	$this->SetDrawColor(0xDD);
				//echo "encoder:".$this->encoder." date:".$this->date." type:".$this->type." start:".$this->shift_start." end:".$this->shift_end." from:".$this->or_from." to:".$this->or_to;
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


		$this->SetFont('Arial','B',10);
		if ($this->type) {
			$account = $db->GetOne('SELECT formal_name FROM seg_pay_accounts WHERE id='.$db->qstr($this->type));
			$this->Cell($total_w,4,'DAILY OR USAGE ('.$account.')',$border2,1,'C');
		}
		else
			$this->Cell($total_w,4,'DAILY OR USAGE',$border2,1,'C');

		$this->SetFont('Arial','B',9);

		if ($this->encoder)
			$this->Cell($total_w,4,$this->encoderName,$border2,1,'C');
		else
			$this->Cell($total_w,4,"All encoders",$border2,1,'C');

		$this->SetFont('Arial','B',9);

		if ($this->startDate)
		{
			$this->Cell(0,4,
				date("M j, Y H:ia",strtotime($this->startDate))." to ".
				date("M j, Y H:ia",strtotime($this->endDate)),$border2,1,'C');
		}

		$this->Ln(4);

		$this->SetTextColor(0);

		$row=5;
		$this->SetFont('Arial','B',9);

		$this->Cell($this->ColumnWidth[0],$this->RowHeight,$this->Headers[0],1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$this->RowHeight,$this->Headers[1],1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$this->RowHeight,$this->Headers[2],1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$this->RowHeight,$this->Headers[3],1,0,'C',1);
		$this->Ln();
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

	function Footer()	{
		$this->SetY(-18);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function FetchData() {
		global $db;

		$where = array();
		$having = array();

//		if($this->encoder)
//		{
//			$where[]="p.create_id=".$db->qstr($this->encoder);
//		}
		if($this->type)
		{
			$query= "SELECT sa.id FROM seg_pay_subaccounts sa\n".
				"WHERE sa.parent_account=".$db->qstr($this->type);
			$rs = $db->Execute($query);
			$rows = $rs->GetRows();
			$types = array();
			foreach ($rows as $row) {
				$types[] = $row['id'];
			}
			$having[] = "`account` IN ('".implode("','", $types)."')";
		}

		if($this->orFrom && $this->orTo)
		{
			$where[]="r.or_no BETWEEN ".$db->qstr($this->orFrom)." AND ".$db->qstr($this->orTo);
		}
		else
		{
			if ($this->startDate)
				$where[] = "p.or_date BETWEEN ".$db->qstr($this->startDate)." AND ".$db->qstr($this->endDate);
		}

		$query="SELECT fn_get_pay_account_type(r.ref_source,r.ref_no,r.service_code,r.or_no) `account`,\n".
			"r.or_no, r.amount_due as amount, p.create_id as encoder\n".
		"FROM seg_pay_request r\n".
		"INNER JOIN seg_pay p ON p.or_no=r.or_no\n";

		if ($where)
		{
			$query .= "WHERE (".implode(") AND (",$where).")\n";
		}
		else
		{
			die("No data filter specified...");
		}
		if ($having)
		{
			$query .= "HAVING (".implode(") AND (",$having).")\n";
		}
		$query .= "ORDER BY CAST(r.or_no AS UNSIGNED)";

		$db->SetFetchMode(ADODB_FETCH_ASSOC);

		$result = $db->Execute($query);
		if ($result)
		{
			$this->Data=array();
			$total_amount_per_or=0;
			$cnt=1;
			$i=0;
			$encoders = array();
			$names = array();
			$amount = array();
			$ors_from = array();
			$ors_to = array();
			$temp_enc;
			$temp_or;
			while ($row=$result->FetchRow())
			{
				if($cnt==1)
				{
					$encoders[$i] = $row['encoder'];
					$names[$i] = $this->getFullName($row['encoder']);
					$ors_from[$i] = $row['or_no'];
					$total_amount_per_or+=$row['amount'];
					$temp_enc = $encoders[$i];
					$temp_or = $row['or_no'];
				}
				else
				{
					if($row['encoder']==$temp_enc && ((int)$row['or_no']-(int)$temp_or)<=1)
					{
						$total_amount_per_or+=$row['amount'];
					}
					else
					{
						$ors_to[$i] = $temp_or;
						$amount[$i] = $total_amount_per_or;

						if (!$this->encoder || ($this->encoder==$encoders[$i]))
						{
							$this->Data[] = Array(
								strtoupper($names[$i]),
								$ors_from[$i],
								$ors_to[$i],
								number_format($amount[$i],2)
							);
						}
						$i++;
						$total_amount_per_or = $row['amount'];
						$names[$i] = $this->getFullName($row['encoder']);
						$encoders[$i] = $row['encoder'];
						$ors_from[$i] = $row['or_no'];
					}
				}
				$temp_enc=$row['encoder'];
				$temp_or = $row['or_no'];
				$cnt++;
			}

			$ors_to[$i] = $temp_or;
			$amount[$i] = $total_amount_per_or;

			if($amount[$i])
			{
				if (!$this->encoder || ($this->encoder==$encoders[$i]))
				{
					$this->Data[] = Array(
						strtoupper($names[$i]),
						$ors_from[$i],
						$ors_to[$i],
						number_format( $amount[$i],2)
					);
				}
			}

			$this->_count = count($this->Data);
		}
		else
		{
			print_r($query);
			print_r($db->ErrorMsg());
			exit;
		}
	}

	function AfterData()
	{
		global $db;
		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(196, 6, "No payments found...", 1, 1, 'L', 1);
		}
	}

	function getFullName($userid)
	{
		global $db;
		$sql = "SELECT name FROM care_users WHERE login_id=".$db->qstr($userid);
		return $db->GetOne($sql);
	}

}


$rep =& new RepGen_Cashier_DailyUsage(
	$_GET['type'],
	$_GET['encoder'],
	$_GET['datestart'],
	$_GET['timestart'],
	$_GET['dateend'],
	$_GET['timeend'],
	$_GET['orfrom'],
	$_GET['orto']
);

$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>