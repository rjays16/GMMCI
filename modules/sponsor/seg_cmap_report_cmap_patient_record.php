<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgenclass.php');
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_referral.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_Cmap_Patient_Record extends RepGen {

const USE_FONT = 'Arial';
const USE_FONTSIZE = 9;
private $totalRow=0;
private $account;

public function __construct($referral, $patientPid, $date)
{
	global $db;

	if ($referral)
	{
		$this->referral=$referral;
		$this->account = $db->GetOne("SELECT cmap_account FROM seg_cmap_referrals WHERE id=".$db->qstr($referral));
	}

	if (!$this->account) die("Invalid referral specified...");



	//echo  $this->account."hoooooooooooooo";

	if ($patientPid) {
		$this->patientPid=$patientPid;
	}
	else
	{
		die('Invalid patient specified...');
	}

	if ($date) $this->date=date("Y-m-d",strtotime($date));
	else $this->date=date("Y-m-d");

	$celTemp = array();
	$celTemp[] = $this->patientPid;
	$filters = array();
	$filters['PID'] = $this->patientPid;

	$referral = new SegCmapReferral($this->referral);
	$this->balance = $referral->getReferralAmount();
//	$result=$referral->getReferrals($filters);
//		if($result) {
//			while($row = $result->FetchRow()) {
//				if($row['cmap_account']==$this->account){
//					$this->balance = $row['referral_amount'];
//					break;
//				}
//			}
//		}

		//else echo "SQL error:\n".$referral->sql;

	$this->amount_given = $this->balance;

	$this->totalRow= $totalRow;
	#$this->RepGen("CMAP Printout","P", array(215.9, 139.7));
				#edited by VAN 02-08-2011

	#$this->RepGen("CMAP Printout","P", array(165.1, 107.95));
	$this->PageWidth = 215.9;
	$this->PageHeight = 110;

	$this->RepGen("CMAP Printout","P", array($this->PageWidth, $this->PageHeight));

	// 95,125
	// 215.9, 279.4

	$this->ColumnWidth =  array(20,70,25,25);


	$this->TopMargin = 5;
	$this->LeftMargin = $this->PageWidth/2 - array_sum($this->ColumnWidth)/2;  #8
	$this->RightMargin = 5; #8
	$this->SetLineWidth(0.2);

	$this->TextPadding = 0.3;

	$this->Headers = array(
			"DATE", "PROCEDURE", "AMOUNT", "BALANCE"
	);
	$this->colored = FALSE;

	$this->RowHeight = 4.5;
	$this->Alignment = array('C','L','R','R');
	$this->SetAutoPageBreak(true, 5);
	//$this->PageOrientation = "L";

	if ($this->colored)
		$this->SetDrawColor(0xDD);
}

public function Header()
{
	global $root_path, $db;
	if(strpos($this->patientPid, 'W')!==FALSE) {
		$sql = "SELECT \n".
			"fn_get_walkin_name(w.pid) AS name \n".
			"FROM seg_walkin w WHERE w.pid=".$db->qstr(substr($this->patientPid,1));
	} else {
		$sql = "SELECT fn_get_person_lastname_first(".$db->qstr($this->patientPid).")";
	}
	$patient_name = ucwords(strtolower($db->GetOne($sql)));

	$sql = "SELECT account_name FROM seg_cmap_accounts WHERE account_nr=".$db->qstr($this->account);
	$cong_name = ucwords(strtolower($db->GetOne($sql)));

	//edit variables
//	if(strpos($this->patientPid, 'W')!==FALSE)
//		$sql= "SELECT control_nr FROM seg_cmap_referrals WHERE walkin_pid=".$db->qstr(substr($this->patientPid,1))." AND cmap_account=".$db->qstr($this->account);
//	else
//		$sql= "SELECT control_nr FROM seg_cmap_referrals WHERE pid=".$db->qstr($this->patientPid)." AND cmap_account=".$db->qstr($this->account);
	$sql= "SELECT control_nr FROM seg_cmap_referrals WHERE id = ".$db->qstr($this->referral);

	$this->control_no = $db->GetOne($sql);
	//$today=date("F d, Y",strtotime( $this->date));
	$today = date("M d, Y (h:iA)");


	$hospital = new Hospital_Admin();
	$hospitalInfo = $hospital->getAllHospitalInfo();
	$total_w = $this->PageWidth;

	$this->SetFont(self::USE_FONT, "", self::USE_FONTSIZE);
	$this->Cell($this->PageWidth-$this->LeftMargin*2,2,$hospitalInfo['hosp_country'],$border2, 1, 'C');
	$this->Ln(1.5);

	$this->Cell($this->PageWidth-$this->LeftMargin*2,2,$hospitalInfo['hosp_agency'],$border2, 1, 'C');
	$this->Ln(2);

	$this->SetFont(self::USE_FONT,"B", self::USE_FONTSIZE+2);
	$this->Cell($this->PageWidth-$this->LeftMargin*2,3,$hospitalInfo['hosp_name'],$border2,1,'C');
	$this->Ln(1);

	$this->SetFont(self::USE_FONT,"", self::USE_FONTSIZE);
	$this->Cell($this->PageWidth-$this->LeftMargin*2,2,$hospitalInfo['hosp_addr1'],$border2,1,'C');
	$this->Ln(3);

	$col1 = 25;
	$col2 = 56;  #110
	$col1point5 = 36;

	$this->SetFont(self::USE_FONT, '', self::USE_FONTSIZE);
	$this->Cell($col1, 3, "Name of Patient: ",$border2,0,'L');
	$this->SetFont(self::USE_FONT, 'B', self::USE_FONTSIZE);
	$this->Cell($col2, 3, strtoupper($patient_name),"",0,'L');

	#$x = $this->GetX();
	$this->SetFont(self::USE_FONT, '', self::USE_FONTSIZE);
	$this->Cell(23,3,"Date (Time):",$border2,0,'L');
	#$this->SetX(0);
	$this->SetFont(self::USE_FONT, 'B', self::USE_FONTSIZE);
	$this->Cell(0,3, $today,$border2, 0,'L');

	$this->Ln();
	$this->SetFont(self::USE_FONT, '', self::USE_FONTSIZE);
	$this->Cell($col1,3,"Name of Cong:     ",$border2,0,'L');
	$this->SetFont(self::USE_FONT, 'B', self::USE_FONTSIZE);
	$this->Cell($col2,3, strtoupper($cong_name), $border2,0,'L');
	$this->SetFont(self::USE_FONT, '', self::USE_FONTSIZE);
	$this->Cell(23,3,"Amount Given:    ",$border2,0,'L');
	$this->SetFont(self::USE_FONT, 'B', self::USE_FONTSIZE);
	$this->Cell(0,3,"P ".number_format($this->amount_given,2),$border2,0,'L');

	$this->Ln();
	$this->SetFont(self::USE_FONT, '', self::USE_FONTSIZE);
	$this->Cell($col1,3,"Control No:",$border2,0,'L');
	$this->SetFont(self::USE_FONT, 'B', self::USE_FONTSIZE);
	$this->Cell($col1point5,3, $this->control_no, $border2,0,'L');
	$this->SetFont(self::USE_FONT, '', self::USE_FONTSIZE);
	$this->Cell($col1,3,"Remarks:",$border2,0,'L');

	$sql= "SELECT remarks FROM seg_cmap_referrals WHERE id = ".$db->qstr($this->referral);
	$this->Cell(0,3,$db->GetOne($sql),$border2,0,'L');

	$this->Ln(4);

	$this->SetTextColor(0);
	$row=3;
	$this->SetFont(self::USE_FONT,"B",self::USE_FONTSIZE);
	$this->Cell($this->ColumnWidth[0],$this->RowHeight+1,$this->Headers[0],1,0,'C');
	$this->Cell($this->ColumnWidth[1],$this->RowHeight+1,$this->Headers[1],1,0,'C');
	$this->Cell($this->ColumnWidth[2],$this->RowHeight+1,$this->Headers[2],1,0,'C');
	$this->Cell($this->ColumnWidth[3],$this->RowHeight+1,$this->Headers[3],1,0,'C');
	$this->Ln();
//	parent::Header();
}

public function Footer()
{
	//$this->SetY(90);
//	$this->SetY(-6);
//	$this->SetFont('Arial','I',5);
//	$this->Cell(0,1,'Page '.$this->PageNo().' of {nb}',0,0,'R');
//	parent::Footer();
}

public function AcceptPageBreak()
{
		return true;
//	if($this->totalRow<20)
//	{
//		return false;
//	}
//	else
//	{
//		$this->totalRow=0;
//		return true;
//	}
}


public function BeforeData()
{
	if ($this->colored) {
		$this->DrawColor = array(0xDD,0xDD,0xDD);
	}
	$this->FONTFAMILY  = self::USE_FONT;
	$this->FONTSIZE = self::USE_FONTSIZE;

	global $db;
//	if (!$this->_count) {
//		$this->SetFont(self::USE_FONT,"B","7");
//		$this->SetFillColor(255);
//		$this->SetTextColor(0);
//		$this->Cell(107, $this->RowHeight, "No records found for this report...", 1, 1, 'L');
//	}
	$cols = array();
}

function BeforeCellRender()
{
	if ($this->colored) {
		if (($this->RENDERPAGEROWNUM%2)>0)
			$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
		else
			$this->RENDERCELL->FillColor=array(255,255,255);
	}
}

function AfterData()
{
		//global $db;
//
//		if (!$this->_count) {
//				$this->SetFont(self::USE_FONT,"B","7");
//				$this->SetFillColor(255);
//				$this->SetTextColor(0);
//				$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
//		}
//
//		$cols = array();
}


function FetchData()
{
	$this->SetFont(self::USE_FONT,'',7);
	global $db;

	$where = array();
	if(strpos($this->patientPid,'W')!==FALSE)
		$where[] = "%source%.walkin_pid=".$db->qstr(substr($this->patientPid,1));
	else
		$where[] = "%source%.pid=".$db->qstr($this->patientPid);
	$where[] = "r.cmap_account=".$db->qstr($this->account);

	$from_date = mktime(0,0,0,date("m"),date("d"),date("Y")-3);
	$from_date = date("Y-m-d", $from_date);
	//echo $from_date; //gets accounts from 3 years back to date given
	$where[] ="(DATE(%source%.create_time) BETWEEN ".$db->qstr($from_date)." AND ".$db->qstr($this->date).")";

	if ($where)
		$where_clause = "WHERE (\n". implode(")\n AND (", $where) . ")\n";


	$sql="SELECT v.* FROM (\n";

	# Pharmacy
	$sql.=
		"(SELECT ph.create_time edate,\n".
			"'PH' `source`,\n".
			"service_name `service`, quantity, amount\n".
		"FROM seg_cmap_entries_pharmacy ph\n".
		"INNER JOIN seg_cmap_referrals r ON r.id=ph.referral_id\n".
		str_replace("%source%", 'ph', $where_clause).
		")\n";

	# Lab
	$sql.=
	"UNION ALL\n".
	"(SELECT ld.create_time AS edate,\n".
		"'LD' `source`,\n".
		"service_name `service`, quantity, amount\n".
	"FROM seg_cmap_entries_laboratory ld\n".
	"INNER JOIN seg_cmap_referrals r ON r.id=ld.referral_id\n".
	str_replace("%source%", 'ld', $where_clause).
	")\n";

	# Radio
	$sql.=
	"UNION ALL\n".
	"(SELECT rd.create_time edate,\n".
		"'RD' `source`,\n".
		"service_name `service`, quantity, amount\n".
	"FROM seg_cmap_entries_radiology rd\n".
	"INNER JOIN seg_cmap_referrals r ON r.id=rd.referral_id\n".
	str_replace("%source%", 'rd', $where_clause).
	")\n";

	# Billing
	$sql.=
	"UNION ALL\n".
	"(SELECT fb.create_time edate,\n".
		"'FB' `source`,\n".
		"service_name AS `service`, quantity, amount\n".
	"FROM seg_cmap_entries_bill fb\n".
	"INNER JOIN seg_cmap_referrals r ON r.id=fb.referral_id\n".
	str_replace("%source%", 'fb', $where_clause).
	")\n";

	# Misc
	$sql.=
	"UNION ALL\n".
	"(SELECT m.create_time edate,\n".
		"'OTHER' `source`,\n".
		"service_name AS `service`, quantity, amount\n".
	"FROM seg_cmap_entries_misc m\n".
	"INNER JOIN seg_cmap_referrals r ON r.id=m.referral_id\n".
	str_replace("%source%", 'm', $where_clause).
	")\n";

	$sql.=
		") AS v\n".
			//"INNER JOIN seg_cmap_accounts AS a ON a.account_nr=v.acct\n".
		"ORDER BY v.edate ASC, v.service ASC";
		/*echo "<pre>";
		print_r($sql);
		echo "</pre>"*/;

	/**
	* Do not fetch results, leave blank per Sir Benjie's request on June
	*/
	/*
	$result = $db->Execute($sql);
	if($result!==false)	{
		$this->_count = $result->RecordCount();
		$this->Data=array();
		while($row=$result->FetchRow()) {
			$this->Data[]=array(
				date('d-M-Y', strtotime($row['edate'])),
				number_format($row['quantity'])." x ".ucwords(strtolower($row['service']))." ".$row['source'].")",
				number_format($row['amount'],2),
				number_format($this->balance-=($row['amount']),2)
			);
			$this->totalRow++;
		}
	}
	else {
		print_r($sql);
		print_r($db->ErrorMsg());
		exit;
		# Error
	}
	*/


	for(;$this->totalRow<14;$this->totalRow++){
		 $this->Data[]=array(
		 "","","",""
		 );
	}

}


}
$rep = new RepGen_Cmap_Patient_Record($_GET['referral'], $_GET['pid'], $_GET['date']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

