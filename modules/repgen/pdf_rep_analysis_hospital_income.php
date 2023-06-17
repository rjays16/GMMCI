<?php
#created by Cherry 07-02-09
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgenclass.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  
class RepGen_Income_Analysis extends RepGen{
  var $colored;
  var $monthnum;
  var $month;
  var $year;
  
  function RepGen_Income_Analysis ($month, $year) {
    global $db;
      
      $this->RepGen("HOSPITAL CONSOLIDATED INCOME REPORT");    
      $this->colored = FALSE;
      $this->ColumnWidth = array(60,25,25,25,25,25,27,27,25,25,25,20);                
      #$this->Caption="Hospital Consolidated Income Report";  
      $this->RowHeight = 6;
      $this->TextHeight = 6;
      $this->Alignment = array('L','L','R','R','R','R');
      #$this->PageOrientation = "L";
      $this->FPDF("L", 'mm', 'Legal');
      $this->monthnum = $month;
      if($month==1){
        $this->month="January";
      }else if($month==2){
        $this->month="February";
      }else if($month==3){
        $this->month="March";
      }else if($month==4){
        $this->month="April";
      }else if($month==5){
        $this->month="May";
      }else if($month==6){
        $this->month="June";
      }else if($month==7){
        $this->month="July";
      }else if($month==8){
        $this->month="August";
      }else if($month==9){
        $this->month="September";
      }else if($month==10){
        $this->month="October";
      }else if($month==11){
        $this->month="November";
      }else if($month==12){
        $this->month="December";
      }
      
      $this->year = $year;  
      #if ($from) $this->from=date("Y-m-d",strtotime($from));
      #if ($to) $this->to=date("Y-m-d",strtotime($to)); 
    
  }
  
  function Header(){
    global $root_path, $db;
        
   /* $objInfo = new Hospital_Admin();
    
    if ($row = $objInfo->getAllHospitalInfo()) {      
      $row['hosp_agency'] = strtoupper($row['hosp_agency']);
      $row['hosp_name']   = strtoupper($row['hosp_name']);
    }else {
      $row['hosp_country'] = "Republic of the Philippines";
      $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
      $row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL";
      $row['hosp_addr1']   = "Bukidnon Province";      
    } */
        
    $this->SetFont('Arial', 'B', 11);
    $this->Cell(0, 6, "PROVINCIAL ECONOMIC ENTERPRISE DEVELOPMENT AND MANAGEMENT OFFICE", 0,1,'C');
    $this->SetFont('Arial', 'B', 10);
    //$this->Cell(0, 6, "Analysis of ".ucwords(strtolower($row['hosp_name']))."s Income",0,1,'C');
    $this->Cell(0, 6, "As of ".$this->month." ".$this->year, 0,1,'C');
    $this->Ln(5);
   
   //=======================================
   //Table Header
    $headerrow = 4;
    $this->SetFont('Arial', 'B', 9);
    
    //first row
    $this->Cell($this->ColumnWidth[0], $headerrow, "", "TLR", 0);
    $this->Cell($this->ColumnWidth[1], $headerrow, "", "TR", 0);
    $this->Cell($this->ColumnWidth[2], $headerrow, "Total Cash", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[3], $headerrow, "%", 'TR', 0, 'C');
    $hospserv = $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6];
    $this->Cell($hospserv, $headerrow, "Hospital Services without Payments", 1,0,'C');
    $this->Cell($this->ColumnWidth[7], $headerrow, "TOTAL", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[8], $headerrow, "PHIC", "TR",0,'C');
    $this->Cell($this->ColumnWidth[9], $headerrow, "%", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[10], $headerrow, "Total Gross","TR",0,'C');
    $this->Cell($this->ColumnWidth[11], $headerrow, "% Gross","TR",1,'C' );
    
    //second row
    $this->Cell($this->ColumnWidth[0], $headerrow, "BUKIDNON", "LR", 0, 'C');
    $this->Cell($this->ColumnWidth[1], $headerrow, "Target Annual", "R",0,'C');
    $this->Cell($this->ColumnWidth[2], $headerrow, "Collected", "R",0,'C');
    $this->Cell($this->ColumnWidth[3], $headerrow, "cash", "R",0, 'C');
    $this->Cell($this->ColumnWidth[4], $headerrow, "Unpaid Excess", "R",0,'C');
    $this->Cell($this->ColumnWidth[5], $headerrow, "PHIC-Denied/","R",0,'C');
    $this->Cell($this->ColumnWidth[6], $headerrow, "Charity Cases","R",0,'C');
    $this->Cell($this->ColumnWidth[7], $headerrow, "Hospital Services", "R",0,'C');
    $this->Cell($this->ColumnWidth[8], $headerrow, "Receivable", "R",0,'C');
    $this->Cell($this->ColumnWidth[9], $headerrow, "cash", "R",0,'C');
    $this->Cell($this->ColumnWidth[10], $headerrow, "Income", "R",0,'C');
    $this->Cell($this->ColumnWidth[11], $headerrow, "Annual","R",1,'C');
    
    //third row
    $this->Cell($this->ColumnWidth[0], $headerrow, "PROVINCIAL","LR",0,'C');
    $this->Cell($this->ColumnWidth[1], $headerrow, "Income","R",0,'C');
    $this->Cell($this->ColumnWidth[2], $headerrow, "","R",0);
    $this->Cell($this->ColumnWidth[3], $headerrow, "income","R",0,'C');
    $this->Cell($this->ColumnWidth[4], $headerrow, "PIHP","R",0,'C');
    $this->Cell($this->ColumnWidth[5], $headerrow, "Slashed","R",0,'C');
    $this->Cell($this->ColumnWidth[6], $headerrow, "Class D, 20% Dis.","R",0,'C');
    $this->Cell($this->ColumnWidth[7], $headerrow, "without","R",0,'C');
    $this->Cell($this->ColumnWidth[8], $headerrow, "", "R",0);
    $this->Cell($this->ColumnWidth[9], $headerrow, "income", "R", 0, 'C');
    $this->Cell($this->ColumnWidth[10], $headerrow, "", "R", 0);
    $this->Cell($this->ColumnWidth[11], $headerrow, "Income", "R",1,'C');
    
    //fourth row
    $this->Cell($this->ColumnWidth[0], $headerrow, "HOSPITAL", "LR",0,'C');
    $this->Cell($this->ColumnWidth[1], $headerrow, "", "R",0);
    $this->Cell($this->ColumnWidth[2], $headerrow, "", "R",0);
    $this->Cell($this->ColumnWidth[3], $headerrow, "to", "R",0,'C');
    $this->Cell($this->ColumnWidth[4], $headerrow, "& Out Patient", "R", 0,'C');
    $this->Cell($this->ColumnWidth[5], $headerrow, "", "R",0);
    $this->Cell($this->ColumnWidth[6], $headerrow, "Sen. Cit., Un pd", "R",0,'C');
    $this->Cell($this->ColumnWidth[7], $headerrow, "Payment", "R",0,'C');
    $this->Cell($this->ColumnWidth[8], $headerrow, "", "R",0,'C');
    $this->Cell($this->ColumnWidth[9], $headerrow, "and", "R",0,'C');
    $this->Cell($this->ColumnWidth[10], $headerrow, "","R",0,'C');
    $this->Cell($this->ColumnWidth[11], $headerrow, "against","R",1,'C');
    
    //fifth row
    $this->Cell($this->ColumnWidth[0], $headerrow, "", "LRB", 0);
    $this->Cell($this->ColumnWidth[1], $headerrow, "", "RB", 0);
    $this->Cell($this->ColumnWidth[2], $headerrow, "a", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[3], $headerrow, "target", "RB",0,'C');
    $this->Cell($this->ColumnWidth[4], $headerrow, "Benefit", "RB",0,'C');
    $this->Cell($this->ColumnWidth[5], $headerrow, "", "RB",0);
    $this->Cell($this->ColumnWidth[6], $headerrow, "& PN","RB",0,'C');
    $this->Cell($this->ColumnWidth[7], $headerrow, "b", "RB",0,'C');
    $this->Cell($this->ColumnWidth[8], $headerrow, "d", "RB",0,'C');
    $this->Cell($this->ColumnWidth[9], $headerrow, "rec'l.", "RB",0,'C');
    $this->Cell($this->ColumnWidth[10], $headerrow, "e=a+b+d","RB",0,'C');
    $this->Cell($this->ColumnWidth[11], $headerrow, "Target", "RB",1,'C');
  }
  
  function Footer()
  {
    $this->SetY(-23);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
  }
  
  function BeforeRow() {
    $this->FONTSIZE = 8;
    if ($this->colored) {
      if (($this->ROWNUM%2)>0) 
        $this->FILLCOLOR=array(0xee, 0xef, 0xf4);
      else
        $this->FILLCOLOR=array(255,255,255);
      $this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
    }
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
  
   function GetAnalysis($idnum){
     global $db;
     
     $sql2 = "";
     $result2=$db->Execute($sql2);
     if($result2){
     
     }
   }
  
  function FetchData() {    
    global $db;
      
    $sql = "SELECT shi.hosp_id AS Idnum, shi.hosp_name AS HospName FROM seg_hospital_info AS shi";
   
    $result=$db->Execute($sql);
    #echo $result;
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
      while ($row=$result->FetchRow()) { 
        #$row2 = $this->GetAnalysis($row['Idnum']);
        #echo $row;
        $this->Data[]=array(
          $row['Idnum'],
          $row['HospName']);
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
$rep = new RepGen_Income_Analysis($_GET['month'], $_GET['year']);
$rep->AliasNbPages();                                                                                      
$rep->FetchData();
$rep->Output();
#$rep->Report();
?>
