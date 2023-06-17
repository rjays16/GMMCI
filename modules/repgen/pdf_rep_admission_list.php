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
  var $dept_nr;

  function RepGen_AdmissionList ($from, $to, $dept_nr) {
    global $db;
    $this->RepGen("MEDICAL RECORDS: MASTER INPATIENT INDEX");
    # 165
	
    #$this->ColumnWidth = array(10,90,15,40,15,20,30);
	//$this->ColumnWidth = array(10,78,20,25,9,32,20,30);
    $this->ColumnWidth = array(10,20,25,60,25,40,12,30,25,30);
    $this->RowHeight = 5.5;
    $this->Alignment = array('L','L','L','L','L','L','L','L','L');
    #$this->PageOrientation = "P";
    $this->PageOrientation = "L";
	$this->LEFTMARGIN=1;
	$this->DEFAULT_TOPMARGIN = 3;
	
  $this->SetAutoPageBreak(TRUE,3);
	$this->NoWrap = FALSE;
  //Added by Cherry 04-14-09
  $this->dept_nr = $dept_nr; 
	
    if ($from) $this->from=date("Y-m-d",strtotime($from));
    if ($to) $this->to=date("Y-m-d",strtotime($to));    
    #$this->SetFillColor(0xFF);
	$this->SetFillColor(255);
    #if ($this->colored) $this->SetDrawColor(0xDD);
	if ($this->colored) //$this->SetDrawColor(255);
  $this->SetDrawColor(128,0,0); // added by genz
  }
  
  function Header() {
    global $root_path, $db;
    $objInfo = new Hospital_Admin();
    
    if ($row = $objInfo->getAllHospitalInfo()) {      
      $row['hosp_agency'] = strtoupper($row['hosp_agency']);
      $row['hosp_name']   = strtoupper($row['hosp_name']);
    }
    else {
      // $row['hosp_country'] = "Republic of the Philippines";
      // $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
      $row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
      $row['hosp_addr1']   = "Quezon Ave., Digos City";      
    }
    
    //edited by Cherry 04-14-09
    $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',55,3,20);
    $this->SetFont("Arial","I","9");
    $total_w = 165;
    //$this->Cell(17,4);
    #$this->Cell(30,4);
    #$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
   // $this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
    #$this->Cell(30,4);
    #$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
    // $this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
    // $this->Ln(2);
    // $this->SetFont("Arial","B","10");
    #$this->Cell(30,4);
    #$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
    $this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
    $this->SetFont("Arial","","9");
    $this->Cell(30,4);
    #$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
    $this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
    $this->Ln(4);
    $this->SetFont('Arial','B',12);
    #$this->Cell(30,5);
    #$this->Cell($total_w,4,'ADMISSION LIST',$border2,1,'C');
    $this->Cell(0,4,'ADMISSION LIST',$border2,1,'C');
    $this->SetFont('Arial','B',12);
    #$this->Cell(30,5);
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
      
    #$this->Cell($total_w,4,$text,$border2,1,'C');
    $this->Cell(0,4,$text,$border2,1,'C');
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
	/*
	$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
    $this->Cell($this->ColumnWidth[1],$row,'Patient Name',1,0,'C',1);
    $this->Cell($this->ColumnWidth[2],$row,'Ward',1,0,'C',1);
    $this->Cell($this->ColumnWidth[3],$row,'Department',1,0,'C',1);
    $this->Cell($this->ColumnWidth[4],$row,'Area',1,0,'C',1);
    $this->Cell($this->ColumnWidth[5],$row,'HOSP #',1,0,'C',1);
    $this->Cell($this->ColumnWidth[6],$row,'CASE #',1,0,'C',1);
	*/
	$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
    $this->Cell($this->ColumnWidth[1],$row,'HRN',1,0,'L',1);
    $this->Cell($this->ColumnWidth[2],$row,'CASE #',1,0,'L',1);
    $this->Cell($this->ColumnWidth[3],$row,'Patient Name',1,0,'L',1);
    $this->Cell($this->ColumnWidth[4],$row,'Received',1,0,'L',1);
    $this->Cell($this->ColumnWidth[5],$row,'Admission Date',1,0,'L',1);
    $this->Cell($this->ColumnWidth[6],$row,'',1,0,'L',1);
    $this->Cell($this->ColumnWidth[7],$row,'Department',1,0,'L',1);
    $this->Cell($this->ColumnWidth[8],$row,'Discharged',1,0,'L',1);
    $this->Cell($this->ColumnWidth[9],$row,'ER Date',1,0,'L',1);
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
	  //$this->DrawColor = array(255,255,255);
      $this->SetDrawColor(128,0,0); // added by genz
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
      #$where[]="DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to'";
      $where[]="DATE(e.create_time) BETWEEN '$this->from' AND '$this->to'";
    }
    
    //Added by Cherry 04-13-09
    $sql_dept="";
   # echo "dep = ".$this->dept_nr;      
   if ($this->dept_nr){  
      
      $sql_dept = " AND (e.current_dept_nr='".$this->dept_nr."' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='".$this->dept_nr."'))";
      
    }    
    
    if ($where)
      $whereSQL = "AND (".implode(") AND (",$where).")";

  $sql = " SELECT
            CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name, w.ward_id AS Ward,
            d.name_formal AS department_name, (CASE current_room_nr WHEN 0 then area ELSE current_room_nr END) AS Area_P,
            IF(accomodation_type=1,'CHA','PAY') AS ward_type,
            e.pid AS HOSP_Num, e.encounter_nr AS CASE_Num, e.discharge_date, e.received_date,
            ins.hcare_id, IF(ins.hcare_id=18,'P','NP') AS insurance,
            e.parent_encounter_nr, e.admission_dt,
            (SELECT ee.encounter_date FROM care_encounter ee WHERE ee.encounter_nr=e.parent_encounter_nr) AS er_date
            FROM care_encounter AS e
            LEFT JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_department AS d ON d.nr=e.current_dept_nr
            LEFT JOIN care_ward AS w ON w.nr = e.current_ward_nr
            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
            WHERE e.encounter_type IN ('3','4') 
            $sql_dept
            AND e.status NOT IN ('deleted','hidden','inactive','void')
            AND e.admission_dt IS NOT NULL $whereSQL\n  
            GROUP BY patient_name 
            ORDER BY e.admission_dt";
               
    #echo "sql = ".$sql;
    $result=$db->Execute($sql);
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
	  $i=1;
      while ($row=$result->FetchRow()) {
	  	$discharged_date = "";
		$received_date = "";
		if (!(empty($row['received_date'])))
			$received_date = date("m/d/Y",strtotime($row['received_date']));
		else
			$received_date = 'not yet';	
			
		if (!(empty($row['discharge_date'])))
			$discharged_date = date("m/d/Y",strtotime($row['discharge_date']));
            
        if ((!(empty($row['er_date'])))&&($row['er_date']!='0000-00-00 00:00:00'))
            $er_date = date("m/d/Y h:i A",strtotime($row['er_date']));
        else        
            $er_date = 'Direct Admission';	
            
        if ((!(empty($row['admission_dt'])))&&($row['admission_dt']!='0000-00-00 00:00:00'))
            $admission_date = date("m/d/Y h:i A",strtotime($row['admission_dt']));    
        else
            $admission_date = '';    
		
        $this->Data[]=array(
		 $i,
          $row['HOSP_Num'],
          $row['CASE_Num'], 
          mb_strtoupper($row['patient_name']),
          $received_date,
          $admission_date,
      $row['insurance'],
      $row['department_name'],
          $discharged_date,
          $er_date);
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

$rep = new RepGen_AdmissionList($_GET['from'], $_GET['to'],$_GET['dept_nr']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
