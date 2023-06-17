<?php
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require('repgen.inc.php');
include_once($root_path."/include/inc_init_main.php");
include_once($root_path."/classes/adodb/adodb.inc.php");
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

  class RepGen_DischargedDiagnosis_Obstetrics extends RepGen {
  var $from, $to;
  var $colored = FALSE;
  var $Conn;

  function RepGen_DischargedDiagnosis_Obstetrics ($from, $to) {
    global $db;
    $this->RepGen("MEDICAL RECORDS: MASTER INPATIENT INDEX");
    # 165
    $this->Conn = &ADONewConnection($dbtype);
    $this->Conn->PConnect($dbhost,$dbusername,$dbpassword,$dbname);
    $this->ColumnWidth = array(70,25,25,25,25,25);
    $this->RowHeight = 5.5;
    $this->Alignment = array('L','R','R','R','R','C');
    $this->PageOrientation = "P";
    if ($from) $this->from=date("Y-m-d",strtotime($from));
    if ($to) $this->to=date("Y-m-d",strtotime($to));    
    $this->SetFillColor(0xFF);
    if ($this->colored) $this->SetDrawColor(0xDD);
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
    
    $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
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
     $this->Cell($total_w,4,'Condition on Discharge (Obstetrics)',$border2,1,'C');
     $this->SetFont('Arial','B',9);
    $this->Cell(17,5);
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
    
    $this->SetFont('Arial','B',8);
    if ($this->colored) $this->SetFillColor(0xED);
    $this->SetTextColor(0);
    $row=6;
   // $this->Cell($this->ColumnWidth[0],$row,'#',1,0,'C',1);
    $this->Cell($this->ColumnWidth[0],$row,'Discahrged Diagnosis',1,0,'C',1);
    $this->Cell($this->ColumnWidth[1],$row,'Recovered',1,0,'C',1);
    $this->Cell($this->ColumnWidth[2],$row,'Improved',1,0,'C',1);
    $this->Cell($this->ColumnWidth[3],$row,'Unimproved',1,0,'C',1);
    $this->Cell($this->ColumnWidth[4],$row,'Total',1,0,'C',1);
    $this->Cell($this->ColumnWidth[5],$row,'ICD-10 Code',1,0,'C',1);
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
  
  function FetchData() {    
    global $db;
    if ($this->from) {
      $where[]="DATE(cd.date) BETWEEN '$this->from' AND '$this->to'";
    }

    if ($where)
      $whereSQL = "AND (".implode(") AND (",$where).")";

  $sql = "SELECT en.description AS Diagnosis, SUM(CASE WHEN sr.result_code = 5 then 1 else 0 end) AS Recovered, 
SUM(CASE WHEN sr.result_code = 6 then 1 else 0 end) AS Improved,
SUM(CASE WHEN sr.result_code = 7 then 1 else 0 end) AS Unimproved, 
SUM(CASE WHEN (sr.result_code = 5 OR sr.result_code = 6 OR sr.result_code = 7) then 1 else 0 end) AS Total, cd.code_parent AS ICD10
FROM care_encounter_diagnosis AS cd
LEFT JOIN care_icd10_en AS en ON en.diagnosis_code = cd.code_parent
LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = cd.encounter_nr
WHERE cd.diagnosing_dept_nr =139 AND cd.date IS NOT NULL $whereSQL\n GROUP BY ICD10 ORDER BY Total Desc";    
 /*   $sql = "  $whereSQL\n  GROUP BY patient_name ORDER BY patient_name";
*/    
    $count_recovered = 0;
    $count_improved = 0;
    $count_unimproved = 0;
    $count_total = 0;
    
    $result=$db->Execute($sql);
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
      while ($row=$result->FetchRow()) {
        $this->Data[]=array(
          $row['Diagnosis'],
          $row['Recovered'],
          $row['Improved'],
          $row['Unimproved'],
          $row['Total'],
          $row['ICD10']);
      $count_recovered = $row['Recovered'] + $count_recovered; 
      $count_improved = $row['Improved'] + $count_improved;
      $count_unimproved = $row['Unimproved'] + $count_unimproved;
      $count_total = $row['Total'] + $count_total; 
           
      }
      
              foreach ($this->data as $j=>$row) {
        $desc=$this->data[$j][1];
        if (strlen($desc) > 30) $desc = substr($desc,0,30) . "...";
        $this->data[$j][1] = $desc;
        #$total_m = 0;
        #$total_f = 0;
        foreach ($row as $i=>$v) {
          #if ($i%2) $total_f += $v;
          #else $total_m += $v;
          $totals[$i] += $v;
        }
              }
   // print $count_recovered;          
      $totals[0] = "Total";
      $totals[1] = $count_recovered;
      $totals[2] = $count_improved;
      $totals[3] = $count_unimproved;
      $totals[4] = $count_total;
       $totals[5] = "";
      $this->Data[] = $totals;
      //$grand_total = $totals[sizeof($totals)-1];

      
      
     $this->data = $this->Data;
    }
    else {
      print_r($sql);
      print_r($db->ErrorMsg());
      exit;
      # Error
    }       
 
    
  
  }
}
$rep = new RepGen_DischargedDiagnosis_Obstetrics($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();      
$rep->Report();
?>
