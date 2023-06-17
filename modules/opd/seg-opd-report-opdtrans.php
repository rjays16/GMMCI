<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path."include/care_api_classes/class_department");
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/repgen.inc.php');

class RepGen_OPD_Trans extends RepGen{
	var $from_date;
	var $to_date;	
	
	function RepGen_OPD_Trans($from, $to, $dept) {
		global $db;
		$this->RepGen("OUTPATIENT DEPARTMENT: DAILY TRANSACTIONS", "L", "Letter");
		$this->Caption = "Outpatient Preventive Care Center Daily Transactions";
		
		# 165
		$this->Conn = &$db;
		$this->ColumnWidth = array(25,60,20,18,18,15.3,73,30);
		$this->TotalWidth = array_sum($this->ColumnWidth);		
		$this->Columns = 8;
		$this->ColumnLabels = array(
			'Patient ID',
			'Fullname',
			'Date',
			'Time',
			'Age',
			'Gender',
			'Address',
			'Department'
		);
		
		$this->RowHeight = 6;
		$this->Alignment = array('C','L','C','C','C','C','L','L');
		$this->PageOrientation = "L";
		
		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));

		$this->NoWrap = FALSE;
		//$this->UseTheme("media");
	}
	
	function Header() {
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

		$this->LogoX = 94;
		$this->LogoY = 8;		
		$this->Image('../../gui/img/logos/dmc_logo.jpg',$this->LogoX,$this->LogoY,16,20);
		$this->SetFont("Arial","I","9");
		$this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
		$this->SetFont("Arial","B","10");
		$this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
	   $this->SetFont('Arial','B',11);
		$this->Ln(2);
   	$this->Cell(0,5,'Outpatient Preventive Care Center Daily Transactions',$border2,1,'C');
		$from_dt=strtotime($this->from_date);
		$to_dt=strtotime($this->to_date);
		$this->SetFont("Arial","","8");
		if (!empty($this->from_date) && !empty($this->to_date))
			$this->Cell(0,5,
				sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
				$border2,1,'C');
		$this->Ln(3);
		
		parent::Header();
	}
	
	/*
	function Header() {
		$objInfo = new Hospital_Admin();
		
		if ($row = $objInfo->getAllHospitalInfo()) {			
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";			
		}
		
		$this->SetFont("Arial","I","9");
		$total_w = 279;
		
		$this->Image('../../gui/img/logos/dmc_logo.jpg',102,8,16,19);
		$this->SetFont("Arial","I","9");
		
		$this->Cell(17,4);
		$this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
	   #$this->Cell(0,4,'DEPARTMENT OF HEALTH',$border2,1,'C');

		$this->Cell(17,4);
		$this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');

		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
		$this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
		
		$this->SetFont("Arial","","9");
		$this->Cell(17,4);
		$this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
	  
		$this->Ln(3);
		$this->SetFont('Arial','B',11);
		$this->Cell(17,4);
		$this->Cell(0,4,'Outpatient Preventive Care Center Daily Transactions',$border2,1,'C');
		
		$from_dt=strtotime($this->from_date);
		$to_dt=strtotime($this->to_date);
		$this->SetFont("Arial","","9");
		if (!empty($this->from_date) && !empty($this->to_date))
			$this->Cell(0,5,
				sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
				$border2,1,'C');
		if ($this->from==$this->to)
			$text = "For ".date("F j, Y",strtotime($this->from));
		else
			$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
		
		
		
		$this->Cell(17,4);
  	$this->Cell(0,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header
		
    $this->SetFont('Arial','B',9);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=6;
		
		$this->Cell($this->ColumnWidth[0],$row,'Patient ID',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'Fullname',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'Date',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'Time',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'Age',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'Gender',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'Address',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'Department',1,0,'C',1);
		$this->Ln();
	}
	
	
	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
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
	
	function AfterData() {
		global $db;
		
		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
		$cols = array();
	}
	*/
	
	function FetchData() {		
		$start_date = $this->from;
		$end_date = $this->to;
		if (empty($end_date)) $end_date="NOW()";
		else $end_date="'$end_date'";
		if (empty($start_date)) $start_date="0000-00-00";
		$start_date="'$start_date'";
		
		$sql = 
"SELECT distinct cp.pid, 
	CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname, 
	CAST(encounter_date as DATE) as consult_date, 
	CAST(encounter_date AS TIME) AS consult_time, 
	fn_get_age(CAST(encounter_date AS date), CAST(date_birth AS DATE)) AS age, 
	UPPER(sex) AS p_sex, addr_str, cd.id,
	cp.street_name,	sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name
FROM (care_encounter AS ce 
	INNER JOIN care_person AS cp ON ce.pid = cp.pid) 
  INNER JOIN (care_encounter_diagnosis AS ced 
	INNER JOIN care_department AS cd ON ced.diagnosing_dept_nr = cd.nr) ON ce.encounter_nr = ced.encounter_nr 
	LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
	LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr
	LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
	LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
WHERE (encounter_date >= $start_date 
	AND CONCAT(CAST(encounter_date AS date), ' 00:00:00') < DATE_ADD($end_date, INTERVAL 1 DAY)) 
	AND ce.encounter_type=2";
	
		if ($this->dept) {
			$sql .= " AND ce.current_dept_nr=".$db->qstr($this->dept);
		}
	
	
		$result=$this->Conn->Execute($sql);
		$this->_count = $result->RecordCount();
		$this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
		if ($result) {
			$this->Data=array();
			while ($row=$result->FetchRow()) {
				$addr = implode(", ",array_filter(array($row['street_name'], $row["brgy_name"], $row["mun_name"])));
				if ($row["zipcode"])	$addr.=" ".$row["zipcode"];
				if ($row["prov_name"])	$addr.=" ".$row["prov_name"];
				$this->Data[]=array(
					$row['pid'],
					$row['fullname'],
					$row['consult_date'],
					$row['consult_time'],
					$row['age'],
					strtoupper($row['p_sex']),
					$addr,
					$row['id']
				);
			}
		}
		else
			echo $this->Conn->ErrorMsg();
	}
}

$rep =& new RepGen_OPD_Trans($_GET['from'],$_GET['to'], $_GET['dept']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>