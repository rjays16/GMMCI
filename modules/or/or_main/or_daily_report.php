<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require_once('./roots.php'); //traverse the root directory
//include_once($root_path.'/classes/fpdf/fpdf.php');
require($root_path.'/modules/repgen/repgen.inc.php');  

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');

class RepGen_Promissory extends Repgen {
  var $after_data;
  var $printable_width = 330.2;
  var $half_width = 165.1;
  function RepGen_Promissory() {
    
    $this->RepGen('OR Daily Report', 'L', array('215.9', '330.2'));
    //$this->SetMargins(120.7, 120.7, 120.7);
    $this->DEFAULT_LEFTMARGIN = 12.7;
    $this->DEFAULT_RIGHTMARGIN = 12.7;
    $this->DEFAULT_TOPMARGIN = 12.7;
    $this->ColumnWidth = array(10, 10, 10, 45.5, 10, 10, 10, 36, 36, 36, 30, 30, 30);
    $this->RowHeight = 5.5;
    $this->Alignment = array('L','L','L','C','L','L');
    $this->PageOrientation = "L";
    $this->NoWrap = false;
  }
  function Header() {
   global $root_path;
   /** Print Header **/
   $hospital = new Hospital_Admin();
   $hospital_info = $hospital->getAllHospitalInfo();
   $hospital_info_array = array($hospital_info['hosp_country'], strtoupper($hospital_info['hosp_agency']), 
                                strtoupper($hospital_info['hosp_name']), $hospital_info['hosp_addr1']);
   $this->SetFont("Times", "B", "10");
   $this->MultiCell(0, 5, implode("\n", $hospital_info_array), 0, 'C'); 
   $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,5,30,30);
   $this->Line(0, $this->GetY()+2.54, $this->printable_width, $this->GetY()+2.54);
   $this->SetY($this->GetY()+7.08); 
   /** End: Print Header **/

   /** For title **/
   $this->SetFont('Arial', 'BU', 16);
   $this->Cell(0, 6, 'OR Daily Report', 0, 1, 'C');
   if (isset($_GET['report_date'])) {
      $date = date('F d, Y', strtotime($_GET['report_date']));
      $this->Cell(0, 6, $date, 0, 1, 'C');
   }
   
   $this->Ln(8);
   /** End for title **/

  

  $row=6;
  $this->SetFont('Arial', 'B', 8);
  $this->Cell($this->ColumnWidth[0],$row,'No',1,0,'C',1);
  $this->Cell($this->ColumnWidth[1],$row,'Time',1,0,'C',1);
  $this->Cell($this->ColumnWidth[2],$row,'Time',1,0,'C',1);
  $this->Cell($this->ColumnWidth[3],$row,'Patient\'s Name',1,0,'C',1);
  $this->Cell($this->ColumnWidth[4],$row,'Age',1,0,'C',1);
  $this->Cell($this->ColumnWidth[5],$row,'Sex',1,0,'C',1);
  $this->Cell($this->ColumnWidth[6],$row,'Status',1,0,'C',1);
  $this->Cell($this->ColumnWidth[7],$row,'Pre-op Diagnosis',1,0,'C',1);
  $this->Cell($this->ColumnWidth[8],$row,'Operation Performed',1,0,'C',1);
  $this->Cell($this->ColumnWidth[9],$row,'Post-op Diagnosis',1,0,'C',1);
  $this->Cell($this->ColumnWidth[10],$row,'Surgeon',1,0,'C',1);
  $this->Cell($this->ColumnWidth[11],$row,'Anesthesiologist',1,0,'C',1);
  $this->Cell($this->ColumnWidth[12],$row,'Anesthesia',1,0,'C',1);
  $this->Ln();
}
  
  function FetchData() {
    if (isset($_GET['report_date'])) {
      $date = $_GET['report_date'];
      $seg_ops = new SegOps();
      $this->Data = $seg_ops->get_or_daily_report($date);
      $this->after_data = !$this->Data ? false : true;                                                                     
    
    }
  }
  
  function AfterData() {
    if (!$this->after_data)
      $this->Cell(0, 5, 'No operation was conducted this day.', 0, 1, 'L');  
  }
  
  function Footer() {
   
  }
}




$x = new RepGen_Promissory();
$x->AliasNbPages();
$x->FetchData();

$x->Report();

?>