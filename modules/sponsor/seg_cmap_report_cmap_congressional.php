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

class RepGen_Cmap_Congressional extends RepGen {

 var $center;
 var $date_from;
 var $date_to;
 var $total_amount;
 var $account_type;

function RepGen_Cmap_Congressional($account, $datefrom, $dateto, $category)
{
	global $db;
	$this->RepGen("CMAP CONGRESSIONAL REPORT","L","LETTER");
	$this->Headers = array(
			'Date', 'CMAP Account', 'Cost Center', 'Patient Name', 'Service Name', 'Quantity', 'Subtotal'
	);
	$this->colored = FALSE;
	$this->ColumnWidth = array(26,30,22,52,81,20,25);
	$this->RowHeight = 7;
	$this->Alignment = array('C','L','C','L','L','C','R');
	$this->PageOrientation = "L";
	if ($datefrom) $this->date_from=date("Y-m-d",strtotime($datefrom));
	else $this->date_from=date("Y-m-d");
	if ($dateto) $this->date_to=date("Y-m-d",strtotime($dateto));
	else $this->date_to=date("Y-m-d");
	$this->account=$account;
	$this->account_type=$category;

	if ($this->colored)
		$this->SetDrawColor(0xDD);
}

function Header()
{
	global $root_path, $db;
//	$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',78,10,20);
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
	$this->Cell($total_w,5,'CMAP CONGRESSIONAL REPORT',$border2,1,'C');
	$this->SetFont('Arial','B',12);
	if($this->account)	{
		$sql = "SELECT account_name FROM seg_cmap_accounts WHERE account_nr=".$db->qstr($this->account);
		$area_name = "CMAP ACCOUNT - ".strtoupper($db->GetOne($sql));
	}
	else {
		$area_name = "All CMAP accounts";
	}

	$this->Ln(2);
	$this->Cell($total_w,5,$area_name,$border2,1,'C');
	if($this->date_from && $this->date_to)	{
		$this->Cell($total_w,5,date("F j, Y",strtotime($this->date_from))." to ".date("F j, Y",strtotime($this->date_to)),$border2,1,'C');
	}
	$this->Ln(4);
	$this->SetTextColor(0);
	$row=5;
	$this->SetFont('Arial','B',9);
	$this->Cell($this->ColumnWidth[0],$this->RowHeight,$this->Headers[0],1,0,'C',1);
	$this->Cell($this->ColumnWidth[1],$this->RowHeight,$this->Headers[1],1,0,'C',1);
	$this->Cell($this->ColumnWidth[2],$this->RowHeight,$this->Headers[2],1,0,'C',1);
	$this->Cell($this->ColumnWidth[3],$this->RowHeight,$this->Headers[3],1,0,'C',1);
	$this->Cell($this->ColumnWidth[4],$this->RowHeight,$this->Headers[4],1,0,'C',1);
	$this->Cell($this->ColumnWidth[5],$this->RowHeight,$this->Headers[5],1,0,'C',1);
	$this->Cell($this->ColumnWidth[6],$this->RowHeight,$this->Headers[6],1,0,'C',1);
	$this->Ln();
}

function Footer()
{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
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
}

function FetchData()
{
	$this->SetFont('Arial','',8);
	global $db;
	$where_center="";
	$where_date="";

	$where = array();
	if($this->account) {
		$where[] = "r.cmap_account=".$db->qstr($this->account);
	}
	if($this->date_from!=$this->date_to)
	{
		$where[] = "(DATE(%source%.create_time) BETWEEN ".$db->qstr($this->date_from)." AND ".$db->qstr($this->date_to).")";
	}
	else if($this->date_from==$this->date_to)
	{
		$where[] = "(DATE(%source%.create_time)=".$db->qstr($this->date_from).")";
	}
	if ($where)
		$where_clause = "WHERE (\n". implode(")\n AND (", $where) . ")\n";


	$sql="SELECT v.*,a.account_name FROM (\n";

	# Pharmacy
	$sql.=
		"(SELECT r.cmap_account acct, ph.create_time edate,referral_id rid,\n".
			"IF(ph.pid IS NULL, fn_get_walkin_name(ph.walkin_pid), fn_get_person_lastname_first(ph.pid))`name`, 'PH' `source`,\n".
			"service_name `service`, quantity, amount, ph.ref_no, ph.service_code `item`\n".
		"FROM seg_cmap_entries_pharmacy ph\n".
		"INNER JOIN seg_cmap_referrals r ON r.id=ph.referral_id\n".
		str_replace("%source%", 'ph', $where_clause).
		")\n";

	# Lab
	$sql.=
	"UNION ALL\n".
	"(SELECT r.cmap_account acct,ld.create_time AS edate,referral_id rid,\n".
		"fn_get_person_lastname_first(ld.pid) `name`, 'LD' `source`,\n".
		"service_name `service`, quantity, amount, ld.ref_no, ld.service_code `item`\n".
	"FROM seg_cmap_entries_laboratory ld\n".
	"INNER JOIN seg_cmap_referrals r ON r.id=ld.referral_id\n".
	str_replace("%source%", 'ld', $where_clause).
	")\n";

	# Radio
	$sql.=
	"UNION ALL\n".
	"(SELECT r.cmap_account acct,rd.create_time edate,referral_id rid,\n".
		"fn_get_person_lastname_first(rd.pid) `name`, 'RD' `source`,\n".
		"service_name `service`, quantity, amount, rd.ref_no, rd.service_code `item`\n".
	"FROM seg_cmap_entries_radiology rd\n".
	"INNER JOIN seg_cmap_referrals r ON r.id=rd.referral_id\n".
	str_replace("%source%", 'rd', $where_clause).
	")\n";

	# Billing
	$sql.=
	"UNION ALL\n".
	"(SELECT r.cmap_account acct,fb.create_time edate,referral_id rid,\n".
		"fn_get_person_lastname_first(fb.pid) `name`, 'FB' `source`,\n".
		"service_name AS `service`, quantity, amount, fb.ref_no, fb.service_code `item`\n".
	"FROM seg_cmap_entries_bill fb\n".
	"INNER JOIN seg_cmap_referrals r ON r.id=fb.referral_id\n".
	str_replace("%source%", 'fb', $where_clause).
	")\n";

	# Misc
	$sql.=
	"UNION ALL\n".
	"(SELECT r.cmap_account acct,m.create_time edate,referral_id rid,\n".
		"fn_get_person_lastname_first(m.pid) `name`, 'OTHER' `source`,\n".
		"service_name AS `service`, quantity, amount, m.ref_no, m.service_code `item`\n".
	"FROM seg_cmap_entries_misc m\n".
	"INNER JOIN seg_cmap_referrals r ON r.id=m.referral_id\n".
	str_replace("%source%", 'm', $where_clause).
	")\n";

	$sql.=
		") AS v\n".
			"INNER JOIN seg_cmap_accounts AS a ON a.account_nr=v.acct\n";

		//modified by cha, 01-17-2011
	if($this->account_type) {
		//$where[] = "fn_get_account_type()=".$db->qstr($this->account_type);
		$sql.= "WHERE fn_get_account_type(v.ref_no, v.source, v.item, 'N')=".$db->qstr($this->account_type)."\n";
	}
	$sql.="ORDER BY v.edate DESC, v.service ASC";

		/*echo "<pre>";
	print_r($sql);
	echo "</pre>";*/
	$result = $db->Execute($sql);
	if($result)	{
		$this->_count = $result->RecordCount();
		$this->Data=array();
		while($row=$result->FetchRow()) {
			$this->Data[]=array(
				date('d-M-Y', strtotime($row['edate'])),
				ucwords(strtolower($row['account_name'])),
				$row['source'],
				ucwords(strtolower($row['name'])),
				ucwords(strtolower($row['service'])),
				number_format($row['quantity'],2),
				number_format($row['amount'],2)
			);
			$this->total_amount+=($row['amount']);
		}
		$this->Data[]=array(
			"TOTAL","","","","","",number_format($this->total_amount,2)
		);
	}
	else {
		print_r($sql);
		print_r($db->ErrorMsg());
		exit;
		# Error
	}
}

}
$rep = new RepGen_Cmap_Congressional($_GET['account'], $_GET['datefrom'], $_GET['dateto'], $_GET['category']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>