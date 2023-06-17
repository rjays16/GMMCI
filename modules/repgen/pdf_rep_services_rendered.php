<?php
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
require('repgen.inc.php');
include_once($root_path."/include/inc_init_main.php");
include_once($root_path."/classes/adodb/adodb.inc.php");
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

class RepGen_ServicesRendered extends RepGen {
  var $from, $to;
  var $colored = FALSE;
  var $Conn;

  function RepGen_ServicesRendered ($from, $to) {
    global $db;
     $this->RepGen("MEDICAL RECORDS: MASTER INPATIENT INDEX"); 
    # 165
    $this->Conn = &ADONewConnection($dbtype);
    $this->Conn->PConnect($dbhost,$dbusername,$dbpassword,$dbname);
    $this->ColumnWidth = array(30,20,25,20,10,10,20,20,20,20,20);
    $this->RowHeight = 5.5;
    $this->Alignment = array('L','R','R','R','R','R','R','R','R','R','R');
    $this->PageOrientation = "L";
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
    
    $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',80,8,20);
    $this->SetFont("Arial","I","9");
    $total_w = 165;
    $this->Cell(50,4);
      #$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
    $this->Cell(50,4);
    #$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
       $this->Ln(2);
    $this->SetFont("Arial","B","10");
    $this->Cell(50,4);
       #$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
    $this->SetFont("Arial","","9");
    $this->Cell(50,4);
     #$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
     $this->Ln(4);
     
     $this->SetFont('Arial','B',12);
    $this->Cell(50,5);
     $this->Cell($total_w,4,'Services Rendered and Patients Attended',$border2,1,'C');
     $this->SetFont('Arial','B',9);
    $this->Cell(50,5);
    
    if ($this->from==$this->to)
      $text = "For ".date("F j, Y",strtotime($this->from));
    else
        #$text = "Full History";
      $text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
      
    $this->Cell($total_w,4,$text,$border2,1,'C');
    $this->Ln(5);
 
    # Print table header
    
    $this->SetFont('Arial','B',7);
    if ($this->colored) $this->SetFillColor(0xED);
    $this->SetTextColor(0);
    $row=6;
   // $this->Cell($this->ColumnWidth[0],$row,'#',1,0,'C',1);
    $this->Cell($this->ColumnWidth[0],$row,'Type of Service',1,0,'C',1);
    $this->Cell($this->ColumnWidth[1],$row,'Non-PHIC (Pay)',1,0,'C',1);
    $this->Cell($this->ColumnWidth[2],$row,'PHIC Member/Dep (Pay)',1,0,'C',1);
    $this->Cell($this->ColumnWidth[3],$row,'PHIC Indigent (Pay)',1,0,'C',1);
    $this->Cell($this->ColumnWidth[4],$row,'OWWA',1,0,'C',1);
    $this->Cell($this->ColumnWidth[5],$row,'Non-PHIC (Service)',1,0,'C',1);
    $this->Cell($this->ColumnWidth[6],$row,'PHIC Member/Dep (Service)',1,0,'C',1);
    $this->Cell($this->ColumnWidth[7],$row,'PHIC Indigent (Service)',1,0,'C',1);
    $this->Cell($this->ColumnWidth[8],$row,'OWWA (Service)',1,0,'C',1);
    $this->Cell($this->ColumnWidth[9],$row,'Total Discharge',1,0,'C',1);
    $this->Cell($this->ColumnWidth[10],$row,'Total Length of Stay',1,0,'C',1);
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

  $sql = "SELECT en.description AS Diagnosis, 
SUM(CASE WHEN sr.result_code = 5 AND p.sex = 'm' then 1 else 0 end) AS Recovered_M,
SUM(CASE WHEN sr.result_code = 5 AND p.sex = 'f' then 1 else 0 end) AS Recovered_F,
SUM(CASE WHEN sr.result_code = 6 AND p.sex = 'm' then 1 else 0 end) AS Improved_M,
SUM(CASE WHEN sr.result_code = 6 AND p.sex = 'f' then 1 else 0 end) AS Improved_F,
SUM(CASE WHEN sr.result_code = 7 AND p.sex = 'm' then 1 else 0 end) AS Unimproved_M,
SUM(CASE WHEN sr.result_code = 7 AND p.sex = 'f' then 1 else 0 end) AS Unimproved_F,
SUM(CASE WHEN (sr.result_code = 5 OR sr.result_code = 6 OR sr.result_code = 7) AND p.sex = 'm' then 1 else 0 end) AS Total_M, 
SUM(CASE WHEN (sr.result_code = 5 OR sr.result_code = 6 OR sr.result_code = 7) AND p.sex = 'f' then 1 else 0 end) AS Total_F,
cd.code_parent AS ICD10
FROM care_encounter_diagnosis AS cd
LEFT JOIN care_icd10_en AS en ON en.diagnosis_code = cd.code_parent
/*LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = cd.encounter_nr*/

LEFT JOIN (SELECT ser.encounter_nr,SUBSTRING(MAX(CONCAT(ser.create_time,ser.result_code)),20) AS result_code,
                                  MAX(ser.modify_time) AS modify_time
                                  FROM seg_encounter_result AS ser 
                                  INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr 
                                  WHERE (DATE(discharge_date) BETWEEN '$this->from' AND '$this->to') 
                                  AND em.encounter_type IN (3,4) 
                                  AND em.discharge_date IS NOT NULL
                                  GROUP BY ser.encounter_nr 
                                  ORDER BY ser.encounter_nr, ser.create_time DESC) AS sr ON sr.encounter_nr = cd.encounter_nr

LEFT JOIN care_encounter AS e ON e.encounter_nr = cd.encounter_nr
LEFT JOIN care_person AS p ON e.pid = p.pid
WHERE sr.encounter_nr = e.encounter_nr AND e.current_dept_nr = cd.diagnosing_dept_nr AND (cd.diagnosing_dept_nr>=117 AND cd.diagnosing_dept_nr<=121) AND cd.status !='deleted' 
AND (sr.result_code >= 5 AND sr.result_code<=7) 
AND e.status NOT IN ('deleted','hidden','inactive','void') 
AND cd.status NOT IN ('deleted','hidden','inactive','void')
AND cd.date IS NOT NULL $whereSQL\n 
      GROUP BY ICD10
ORDER BY SUM(CASE WHEN (sr.result_code = 5 OR sr.result_code = 6 OR sr.result_code = 7) then 1 else 0 end) DESC";
 
  
    
    $count_recovered_male = 0;
    $count_recovered_female = 0;
    $count_improved_male = 0;
    $count_improved_female = 0;
    $count_unimproved_male = 0;
    $count_unimproved_female = 0;
    $count_total_male = 0;
    $count_total_female =0;
    
    $result=$db->Execute($sql);
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
      while ($row=$result->FetchRow()) {
        $this->Data[]=array(
          $row['Diagnosis'],
          $row['Recovered_M'],
          $row['Recovered_F'],
          $row['Improved_M'],
          $row['Improved_F'],
          $row['Unimproved_M'],
          $row['Unimproved_F'],
          $row['Total_M'],
          $row['Total_F'],
          $row['ICD10']);
      $count_recovered_male = $row['Recovered_M'] + $count_recovered_male;
      $count_recovered_female = $row['Recovered_F'] + $count_recovered_female;
      $count_improved_male = $row['Improved_M'] + $count_improved_male;
      $count_improved_female = $row['Improved_F'] + $count_improved_female;
      $count_unimproved_male = $row['Unimproved_M'] + $count_unimproved_male;
      $count_unimproved_female = $row['Unimproved_F'] + $count_unimproved_female;
      $count_total_male = $row['Total_M'] + $count_total_male; 
      $count_total_female = $row['Total_F'] + $count_total_female;
           
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
      $totals[1] = $count_recovered_male;
      $totals[2] = $count_recovered_female;
      $totals[3] = $count_improved_male;
      $totals[4] = $count_improved_female;
      $totals[5] = $count_unimproved_male;
      $totals[6] = $count_unimproved_female;
      $totals[7] = $count_total_male;
      $totals[8] = $count_total_female;
       $totals[9] = "";
      $this->Data[] = $totals;
      

      
      
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
$rep = new RepGen_ServicesRendered($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();      
$rep->Report();
?>
