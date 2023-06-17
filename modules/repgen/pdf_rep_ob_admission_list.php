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

  class RepGen_AdmissionList extends RepGen {
  var $from, $to;
  var $colored = TRUE;

  function RepGen_AdmissionList ($from, $to) {
    global $db;
    $this->RepGen("MEDICAL RECORDS: OB & BIRTHING HOME ADMISSION LIST");
    # 165
	
    #$this->ColumnWidth = array(10,90,15,40,15,20,30);
	$this->ColumnWidth = array(10,85,35,10,32,20,30);
    $this->RowHeight = 5.5;
    $this->Alignment = array('L','L','C','L','L','L','L');
    $this->PageOrientation = "P";
	$this->LEFTMARGIN=1;
	$this->DEFAULT_TOPMARGIN = 2;
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
    
    $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,8,20);
    $this->SetFont("Arial","I","9");
    $total_w = 165;
    $this->Cell(17,4);
      #$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
    $this->Cell(17,4);
    #$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
       $this->Ln(2);
    $this->SetFont("Arial","B","10");
    $this->Cell(17,4);
       #$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
    $this->SetFont("Arial","","9");
    $this->Cell(17,4);
     #$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
     $this->Ln(4);
     $this->SetFont('Arial','B',12);
    $this->Cell(17,5);
     $this->Cell($total_w,4,'OB & BIRTHING HOME ADMISSION LIST',$border2,1,'C');
     $this->SetFont('Arial','B',12);
    $this->Cell(17,5);
    
    if ($this->from==$this->to)
      $text = "For ".date("F j, Y",strtotime($this->from));
    else
        #$text = "Full History";
      $text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
      
    $this->Cell($total_w,4,$text,$border2,1,'C');
    $this->Ln(5);
    
    # Print table header
    
    $this->SetFont('Arial','B',12);
    #if ($this->colored) $this->SetFillColor(0xED);
	if ($this->colored) $this->SetFillColor(255);
    $this->SetTextColor(0);
    $row=6;
	
	$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
    $this->Cell($this->ColumnWidth[1],$row,'Patient Name',1,0,'L',1);
    $this->Cell($this->ColumnWidth[2],$row,'Admission Date',1,0,'L',1);
    #$this->Cell($this->ColumnWidth[3],$row,'Discharged',1,0,'L',1);
	$this->Cell($this->ColumnWidth[3],$row,'',1,0,'L',1);
	$this->Cell($this->ColumnWidth[4],$row,'Department',1,0,'L',1);
    $this->Cell($this->ColumnWidth[5],$row,'HRN',1,0,'L',1);
    $this->Cell($this->ColumnWidth[6],$row,'CASE #',1,0,'L',1);
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
    if ($this->from) {
      $where[]="DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to'";
    }

    if ($where)
      $whereSQL = "AND (".implode(") AND (",$where).")";

  $sql = " SELECT
CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name, 
d.name_formal AS department_name, (CASE current_room_nr WHEN 0 then area ELSE current_room_nr END) AS Area_P,
e.pid AS HOSP_Num, e.encounter_nr AS CASE_Num, e.discharge_date, e.received_date,
ins.hcare_id, IF(ins.hcare_id=18,'P','NP') AS insurance, e.admission_dt
FROM care_encounter AS e
LEFT JOIN care_person AS p ON p.pid=e.pid
LEFT JOIN care_department AS d ON d.nr=e.current_dept_nr
LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
WHERE (e.encounter_type = 3 OR e.encounter_type = 4) 
AND e.current_dept_nr IN (124,139,155,140)
AND e.admission_dt IS NOT NULL $whereSQL\n  
GROUP BY patient_name ORDER BY patient_name";
    
   # echo "sql = ".$sql;
    $result=$db->Execute($sql);
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
	  $i=1;
      while ($row=$result->FetchRow()) {
	  	$discharged_date = "";
		$received_date = "";
		$admission_date = "";
		/*
		if (!(empty($row['received_date'])))
			$received_date = date("m/d/Y",strtotime($row['received_date']));
		else
			$received_date = 'not yet';	
		*/	
		if (!(empty($row['discharge_date'])))
			$discharged_date = date("m/d/Y",strtotime($row['discharge_date']));	
		
		if (!(empty($row['admission_dt'])))
			$admission_date = date("m/d/Y",strtotime($row['admission_dt']));	
			
        $this->Data[]=array(
		  $i,	
          mb_strtoupper($row['patient_name']),
          $admission_date,
          $row['insurance'],
		  $row['department_name'],
          $row['HOSP_Num'],
          $row['CASE_Num']);
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

$rep = new RepGen_AdmissionList($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
