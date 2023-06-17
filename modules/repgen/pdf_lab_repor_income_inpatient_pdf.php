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

  class RepGen_Laboratory_Income extends RepGen {
  #var $date;
  var $colored = FALSE;
  var $fromdate;
  var $todate;

  function RepGen_Laboratory_Income($fromdate, $todate) {
    global $db;
    $this->RepGen("INCOME REPORT: IN-PATIENT");
    # 165
    $this->ColumnWidth = array(20,40,25,22,22,22,25,22);
    $this->RowHeight = 5.5;
    $this->Alignment = array('C','C','C','C','C','C','C','C');
    $this->PageOrientation = "P";
    if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
    if ($todate) $this->todate=date("Y-m-d",strtotime($todate));
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
  
    $this->SetFont("Arial","B","8");
    $this->Cell(25,4);
      $this->Cell($total_w,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$border2,1,'C');
    $this->Ln(2);
  $this->SetFont('Arial','B',12);
    $this->Cell(17,5);  
  $this->Cell($total_w,4,'INCOME REPORT',$border2,1,'C');
   $this->SetFont('Arial','B',9);
    $this->Cell(17,5);
    if ($this->fromdate==$this->todate)
      $text = "For ".date("F j, Y",strtotime($this->fromdate));
    else
        #$text = "Full History";
      $text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));
      
    $this->Cell($total_w,4,$text,$border2,1,'C');
    $this->Ln(5);

    # Print table header    
    $this->SetFont('Arial','B',8);
    if ($this->colored) $this->SetFillColor(0xED);
    $this->SetTextColor(0);
    $row=6;
    #$this->Cell(0,4,'',1,1,'C');
    $this->Cell($this->ColumnWidth[0],$row,'DATE',1,0,'C',1);
    $this->Cell($this->ColumnWidth[1],$row,'SHIFT',1,0,'C',1);
    $this->Cell($this->ColumnWidth[2],$row,'AMT BILLED',1,0,'C',1);
    $this->Cell($this->ColumnWidth[3],$row,'AMT PAID',1,0,'C',1);
    $this->Cell($this->ColumnWidth[4],$row,'CHARITY',1,0,'C',1);
    $this->Cell($this->ColumnWidth[5],$row,'LINGAP',1,0,'C',1);
    $this->Cell($this->ColumnWidth[6],$row,'SS DISCOUNT',1,0,'C',1);
    $this->Cell($this->ColumnWidth[7],$row,'OTHER',1,0,'C',1);
    $this->Ln();
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
  
  function FetchData() {    
    global $db;
    //$this->fromdate ="2007-01-01";
    //  $this->todate ="2008-01-30";
      
    if (($this->fromdate)&&($this->todate)) {
      
      #$where[]="DATE(e.discharge_date)='$this->date'";
      #$whereSQL = " AND (e.discharge_date>='".$this->fromdate."' AND e.discharge_date<='".$this->todate."')";
      $where[]="DATE(e.discharge_date) BETWEEN '$this->fromdate' AND '$this->todate'";
    }

    if ($where)
      $whereSQL = "AND (".implode(") AND (",$where).")";

    $sql = "SELECT ls.serv_dt AS DATE_, 
ls.serv_tm AS SHIFT, 
ld.price_cash_orig AS AMT_BILLED, 
ld.price_cash AS AMT_PAID, 
(CASE WHEN gr.ref_source = 'LD' then ld.price_cash_orig else 0.00 end) AS Charity,
(CASE WHEN ls.type_charge = 1 then (price_cash_orig-price_cash) else 0.00 end)
AS LINGAP, 
(CASE WHEN ls.type_charge = 0 AND (ls.discountid = 'C3' OR ls.discountid = 'C1' OR ls.discountid = 'C2') then (price_cash_orig-price_cash)
else 0.00 end) AS PAYWARD, 
(CASE WHEN ls.type_charge!=0 AND ls.type_charge!=1 then (price_cash_orig-price_cash) else 0.00 end) AS OTHER
FROM seg_lab_serv AS ls 
LEFT JOIN seg_lab_servdetails AS ld ON ld.refno = ls.refno
LEFT JOIN seg_granted_request AS gr ON gr.ref_no = ld.refno AND gr.service_code = ld.service_code
WHERE ls.serv_dt IS NOT NULL $whereSQL\n ORDER BY ls.serv_dt ASC;";

    #echo "sql = ".$sql;
    $result=$db->Execute($sql);
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
      while ($row=$result->FetchRow()) {
        $timeframe = date("h:i A",strtotime($row['SHIFT']));
        //print $timeframe;
        if($timeframe > '07:00 AM' || $timeframe < '03:00 PM'){
          $shift = "07:00 AM - 03:00 PM ";
        }
        else if($timeframe > '03:00 PM' || $timeframe < '11:00 PM'){
         $shift = "03:00 PM - 11:00 PM";
        }
        else if($timeframe > '11:00 PM' || $timeframe < '07:00 AM'){
         $shift = "11:00 PM - 07:00 AM";
        }
        $DATE = date("m/d",strtotime($row['DATE_'])); 
        $this->Data[]=array(
          #mb_strtoupper($row['patient_name']),
          #$row['pid'],
          #$row['encounter_nr'],
          #date("m/d/Y",strtotime($row['admission_date'])),
          #date("m/d/Y",strtotime($row['discharge_date'])),
          #$row['department_name'],
          #strtoupper($row['sex']),
          #$row['result_desc']);
          $DATE,
          $shift,
          $row['AMT_BILLED'],
          $row['AMT_PAID'],
          $row['Charity'],
          $row['LINGAP'],
          $row['PAYWARD'],
          $row['OTHER']);
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

$fromdate = $_GET['from'];
$todate = $_GET['to'];

$rep = new RepGen_Laboratory_Income($fromdate, $todate);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>