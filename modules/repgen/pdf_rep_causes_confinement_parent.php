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
    $this->RepGen("MEDICAL RECORDS: COMMON CAUSES OF CONFINEMENT");
    # 165
	
	$this->ColumnWidth = array(15,35,20,23,30,90);
    $this->RowHeight = 5.5;
    $this->Alignment = array('C','C','C','C','C','L');
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
     $this->Cell($total_w,4,'COMMON CAUSES OF CONFINEMENT',$border2,1,'C');
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
	if ($this->colored) $this->SetFillColor(255);
    $this->SetTextColor(0);
    $row=6;

	$this->Cell($this->ColumnWidth[0],$row,'RANK',1,0,'C',1);
    $this->Cell($this->ColumnWidth[1],$row,'OCCURRENCE',1,0,'C',1);
    $this->Cell($this->ColumnWidth[2],$row,'PHIC',1,0,'C',1);
    $this->Cell($this->ColumnWidth[3],$row,'NON-PHIC',1,0,'C',1);
	$this->Cell($this->ColumnWidth[4],$row,'CODE',1,0,'C',1);
	$this->Cell($this->ColumnWidth[5],$row,'ICD DESCRIPTION',1,0,'C',1);
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
    
  $sql = "SELECT (SELECT IF(INSTR(ed.code_parent,'.'), SUBSTRING(ed.code_parent, 1, 3), IF(INSTR(ed.code_parent,'/'), SUBSTRING(ed.code_parent, 1, 5),ed.code_parent))) AS code, 
                   count(ed.code_parent) AS occurrence,
                   c.description,
                   SUM(CASE WHEN ins.hcare_id=18 then 1 else 0 end) AS phic_occurrence,
                   SUM(CASE WHEN ins.hcare_id<>18 OR ins.hcare_id IS NULL then 1 else 0 end) AS nonphic_occurrence
            FROM  care_encounter_diagnosis AS ed
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=(SELECT IF(INSTR(ed.code_parent,'.'), SUBSTRING(ed.code_parent, 1, 3), IF(INSTR(ed.code_parent,'/'), SUBSTRING(ed.code_parent, 1, 5),ed.code_parent)))
            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr 
            WHERE ed.encounter_type IN (3,4)
            AND e.status NOT IN ('deleted','hidden','inactive','void')
            AND ed.status NOT IN ('deleted','hidden','inactive','void')
            AND DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'
            GROUP BY ed.code_parent
            ORDER BY count(ed.code) DESC";
    
   # echo "sql = ".$sql;
    $result=$db->Execute($sql);
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
	  $i=1;
      while ($row=$result->FetchRow()) {
	  	$this->Data[]=array(
		  $i,	
          $row['occurrence'],
          $row['phic_occurrence'],
		  $row['nonphic_occurrence'],
		  $row['code'],
		  $row['description']);
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
