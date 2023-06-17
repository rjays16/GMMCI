<?php
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_BirthCertList extends RepGen {
	var $from, $to;
	var $colored = TRUE;
	var $dept_nr;

	function RepGen_BirthCertList ($from, $to) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: LIST OF BABIES WITH BIRTH CERTIFICATE");
		# 165

		$this->ColumnWidth = array(10,60, 60,30,40);
		$this->RowHeight = 5.5;
		$this->Alignment = array('C', 'L', 'L', 'C', 'C');
		$this->PageOrientation = "P";
		$this->LEFTMARGIN=5;
		$this->DEFAULT_TOPMARGIN = 4;
		$this->SetAutoPageBreak(FALSE);
		$this->NoWrap = FALSE;

		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));
		#$this->SetFillColor(0xFF);
	$this->SetFillColor(255);
		#if ($this->colored) $this->SetDrawColor(0xDD);
	if ($this->colored) $this->SetDrawColor(255);
	}

	function Header() {
		global $root_path, $db;
		$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";
		}

		//edited by Cherry 04-14-09
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',55,3,20);
		$this->SetFont("Arial","I","9");
		$total_w = 165;
		//$this->Cell(17,4);
		$this->Cell(30,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(30,4);
		#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
			 $this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(30,4);
			 #$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(30,4);
		 #$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		 $this->Ln(4);
		 $this->SetFont('Arial','B',12);
		$this->Cell(30,5);
		 $this->Cell($total_w,4,'LIST OF BABIES WITH BIRTH CERTIFICATE',$border2,1,'C');
		 $this->SetFont('Arial','B',12);
		$this->Cell(34,5);
		/*
		if ($this->from || $this->to) {
			$text = "From ".date("F j, Y",strtotime($this->from))." to ".date("F j, Y",strtotime($this->to));
		}
		else
			$text = "Full History";
		*/
		if ($this->from==$this->to)
			$text = "For ".date("F j, Y",strtotime($this->from));
		else
				#$text = "Full History";
			$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));

		$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);
		/*
		$from_dt=strtotime($this->from_date);
		$to_dt=strtotime($this->to_date);
		$this->SetFont("Arial","","9");
		if (!empty($this->from_date) && !empty($this->to_date))
			$this->Cell(0,5,
				sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
				$border2,1,'C');
		*/
		# Print table header

		$this->SetFont('Arial','B',12);
		#if ($this->colored) $this->SetFillColor(0xED);
	if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'Patient Name',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2], $row, "Mother's Name", 1, 0, 'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'Birthdate',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4], $row, 'Birth Cert. Created', 1, 0, 'C', 1);
		$this->Ln();

	}

	function Footer()
	{
		$this->SetY(-7);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function BeforeData() {
		$this->FONTSIZE = 10;
		if ($this->colored) {
			#$this->DrawColor = array(0xDD,0xDD,0xDD);
		$this->DrawColor = array(255,255,255);
		}
	}

	function BeforeCellRender() {
		$this->FONTSIZE = 10;

		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				#$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
		$this->RENDERCELL->FillColor=array(255, 255, 255);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}

	function AfterData() {
		global $db;

		if (!$this->_count) {
			$this->SetFont('Arial','B',12);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		$cols = array();
	}

	function FetchData() {
		global $db;

/*$sql = "SELECT CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name,
DATE(p.date_birth) AS birthday,
CONCAT(IFNULL(p.mother_lname,''),IFNULL(CONCAT(', ', p.mother_fname),''),IFNULL(CONCAT(' ', p.mother_maidenname),'')) AS mother_name,
DATE(scb.create_dt) birth_cert_dt
from care_person AS p
INNER JOIN seg_cert_birth AS scb ON scb.pid = p.pid
WHERE DATE(scb.create_dt) BETWEEN '$this->from' AND '$this->to'
AND p.status NOT IN ('deleted', 'void', 'hidden')
ORDER BY patient_name ASC";  */

$sql = "SELECT CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name,
DATE(p.date_birth) AS birthday,
/*CONCAT(IFNULL(p.mother_lname,''),IFNULL(CONCAT(', ', p.mother_fname),''),IFNULL(CONCAT(' ', p.mother_maidenname),'')) AS mother_name, */
p.mother_lname, p.mother_fname, p.mother_maidenname, p.mother_mname,
DATE(scb.create_dt) birth_cert_dt
from care_person AS p
INNER JOIN seg_cert_birth AS scb ON scb.pid = p.pid
WHERE DATE(scb.create_dt) BETWEEN '$this->from' AND '$this->to'
AND p.status NOT IN ('deleted', 'void', 'hidden')
ORDER BY patient_name ASC";

	 # echo "sql = ".$sql;
		$result=$db->Execute($sql);
		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();
		$i=1;
			while ($row=$result->FetchRow()) {
			 if($row['birthday']!='0000-00-00'){
				$bday = date("d M Y", strtotime($row['birthday']));
			 }else{
				$bday = "NOT PROVIDED";
			 }
			 $bcert_dt = date("d M Y", strtotime($row['birth_cert_dt']));

			 if($row['mother_lname']=='' || $row['mother_lname']==NULL){
				 if($row['mother_maidenname'])
					$mother_name = strtoupper($row['mother_maidenname'].", ".$row['mother_fname']." ".$row['mother_mname']);
				 else
					$mother_name = strtoupper($row['mother_fname']." ".$row['mother_mname']);
			 }else{
				 $mother_name = strtoupper($row['mother_lname'].", ".$row['mother_fname']." ".$row['mother_maidenname']);
			 }

				$this->Data[]=array(
						$i,
						mb_strtoupper($row['patient_name']),
						mb_strtoupper($mother_name),
						$bday,
						$bcert_dt
						);
				$i++;
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

$rep = new RepGen_BirthCertList($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
