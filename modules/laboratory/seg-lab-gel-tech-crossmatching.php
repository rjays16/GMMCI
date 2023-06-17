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

  class RepGen_Billing_Accountability extends RepGen {
  #var $date;
  var $colored = FALSE;
  var $fromdate;
  var $todate;
  var $fromshift;
  var $toshift;

  function RepGen_Billing_Accountability($fromdate, $todate, $fromshift, $toshift) {
    global $db;
    $this->RepGen("INCOME REPORT: IN-PATIENT");
    # 165
    $this->ColumnWidth = array(15,45,25,25,25,25,25,25,25,25);
    $this->RowHeight = 5.5;
    #$this->Alignment = array('R','C','R','R','R','R','R','R');
    $this->Alignment = array('C','C','R','R','R','R','R','R','R','R');
    $this->PageOrientation = "L";
    $this->LEFTMARGIN = 2;
    if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
    if ($todate) $this->todate=date("Y-m-d",strtotime($todate));
  
    $this->pat_type = $pat_type;
    #added by Cherry 04-21-09
    $this->servgroup = $servgroup;

    if ($this->pat_type==0){
      $this->enctype = "";
      $this->patient_type = "ALL PATIENT";
    }elseif ($this->pat_type==1){
      #ER PATIENT
      $this->enctype = " AND e.encounter_type IN (1)";
      $this->patient_type = "ER PATIENT";
    }elseif ($this->pat_type==2){
      #ADMITTED PATIENT
      $this->enctype = " AND e.encounter_type IN (3,4)";
      $this->patient_type = "INPATIENT";
    }elseif ($this->pat_type==3){
      #OUT PATIENT    
      $this->enctype = " AND e.encounter_type IN (2)";
      $this->patient_type = "OUTPATIENT";
    }elseif ($this->pat_type==4){
      #WALKIN  
      $this->enctype = " AND e.encounter_type IS NULL";
      $this->patient_type = "WALKIN";
    }elseif ($this->pat_type==5){
      #OUT PATIENT AND WALKIN  
      $this->enctype = " AND (e.encounter_type IN (2) OR e.encounter_type IS NULL)";
      $this->patient_type = "OUTPATIENT AND WALKIN";
    }
    
     if($this->servgroup != 'all')
    $this->group_cond = "AND s.group_code = '".$this->servgroup."'";
  else
    $this->group_cond = "";
  
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
      $row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";      
    }
  
  #$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
    $this->SetFont("Arial","I","9");
    $total_w = 165;
    $this->Cell(53,4);
      #$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
    $this->Cell(53,4);
      #$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
    $this->Ln(2);
    $this->SetFont("Arial","B","10");
    $this->Cell(53,4);
      #$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
    $this->SetFont("Arial","","9");
    $this->Cell(53,4);
      #$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
    $this->Ln(4);
  
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(53,4);
    $this->Cell($total_w,4,'BILLING and ACCOUNTABILITY FOR GEL TECH CROSSMATCHING',$border2,1,'C');
    $this->Ln(2);
    $this->Cell(53,5);
    $this->SetFont('Arial', 'B', 9);
    $this->Ln(2);
    $date = "Date : ";
    $ldate = $this->GetStringWidth($date);
    $this->Cell($ldate, 4, $date, $border2, 0, 'C');
    
    if ($this->fromdate==$this->todate)
      $text = date("F j, Y",strtotime($this->fromdate));
    else
        #$text = "Full History";
      $text = date("F j, Y",strtotime($this->fromdate))." - ".date("F j, Y",strtotime($this->todate));
    $ltext = $this->GetStringWidth($text);  
    $this->Cell($ltext,4,$text,$border2,0,'C');
    $this->Ln(4);
    $shift = "Shift : ";
    $lshift = $this->GetStringWidth($shift);
    $this->Cell($lshift, 4, $shift, $border2, 0, 'C');
    $this->Ln(5);

    # Print table header    
    $this->SetFont('Arial','B',8);
    if ($this->colored) $this->SetFillColor(0xED);
    $this->SetTextColor(0);
    $row=6;
    
    $this->Cell(60, 3, "PATIENT", "TLR", 0, 'C');
    $this->Cell(23, 3, "WARD", "TLR", 0, 'C');
    $this->Cell(23, 3, "CASE", "TLR", 0, 'C');  
    $this->Cell(18, 3, "OFFICIAL", "TLR", 0, 'C'); 
    $this->Cell(24, 3, "# of GEL TUBES", "TLR",0,'C'); 
    $this->Cell(15, 3, "FULL", "TLR", 0, 'C');
    $this->Cell(18, 3, "SOCIALIZED", "TLR", 0, 'C');
    $this->Cell(16, 3, "PHIC", "TLR", 0, 'C');
    $this->Cell(16, 3, "LINGAP", "TLR", 0, 'C');
    $this->Cell(16, 3, "", "TLR", 0, 'C');
    $this->Cell(16, 3, "", "TLR", 0, 'C');
    $this->Cell(15, 3, "", "TLR", 0, 'C');
    $this->Cell(15, 3, "SCITIZEN", "TLR", 1, 'C');
    
    $this->Cell(60, 3, "", "LR", 0, 'C');
    $this->Cell(23, 3, "", "LR", 0,'C');
    $this->Cell(23, 3, "NUMBER", "LR", 0, 'C');
    $this->Cell(18, 3, "RECEIPT", "LR", 0, 'C');
    $this->Cell(24, 3, "USED", "LR", 0, 'C');
    $this->Cell(15, 3, "PAY", "LR", 0, 'C');
    $this->Cell(18, 3, "", "LR", 0, 'C');
    $this->Cell(16, 3, "", "LR", 0,'C');
    $this->Cell(16, 3, "PCSO", "LR", 0, 'C');
    $this->Cell(16, 3, "PAYWARD", "LR", 0, 'C');
    $this->Cell(16, 3, "CHARITY", "LR", 0, 'C');
    $this->Cell(15, 3, "CHARGE", "LR", 0, 'C');
    $this->Cell(15, 3, "","LR", 1,'C');
    
    $this->Cell(60, 3, "", "LRB", 0, 'C');
    $this->Cell(23, 3, "", "LRB", 0, 'C');
    $this->Cell(23, 3, "", "LRB", 0, 'C');
    $this->Cell(18, 3, "NUMBER", "LRB", 0, 'C');
    $this->Cell(24, 3, "", "LRB", 0, 'C');
    $this->Cell(15, 3, "", "LRB", 0, 'C');
    $this->Cell(18, 3, "", "LRB", 0, 'C');
    $this->Cell(16, 3, "", "LRB", 0, 'C');
    $this->Cell(16, 3, "", "LRB", 0, 'C');
    $this->Cell(16, 3, "", "LRB", 0,'C');
    $this->Cell(16, 3, "", "LRB", 0, 'C');
    $this->Cell(15, 3, "", "LRB", 0, 'C');
    $this->Cell(15, 3, "", "LRB", 0, 'C'); 
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
    
    /*if (!$this->_count) {
      $this->SetFont('Arial','B',9);
      $this->SetFillColor(255);
      $this->SetTextColor(0);
      $this->Cell(200, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
    }else{
    $this->SetFont('Arial','B',12);
    $this->Ln(4);
    $this->Cell(80, $this->RowHeight, 'AMOUNT BILLED', 0, 0, 'L', 1);
    $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_BILLED,2,'.',','), 0, 1, 'R', 1);
    $this->Cell(80, $this->RowHeight, 'AMOUNT PAID ', 0, 0, 'L', 0);
    $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_PAID,2,'.',','), 0, 1, 'R', 1);
    $this->Cell(80, $this->RowHeight, 'CHARITY', 0, 0, 'L', 0);
    $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, number_format($this->SUM_CHARITY,2,'.',','), 0, 1, 'R', 1);
    $this->Cell(80, $this->RowHeight, 'LINGAP', 0, 0, 'L', 0);
    $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, number_format($this->SUM_LINGAP,2,'.',','), 0, 1, 'R', 1);
    $this->Cell(80, $this->RowHeight, 'SOCIALIZED', 0, 0, 'L', 0);
    $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, number_format($this->SUM_SOCIALIZED,2,'.',','), 0, 1, 'R', 1);
    $this->Cell(80, $this->RowHeight, 'OTHER CHARGES', 0, 0, 'L', 0);
    $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, number_format($this->SUM_OTHER,2,'.',','), 0, 1, 'R', 1);
    $this->Ln(5);
    $this->Cell(80, $this->RowHeight, 'NUMBER OF PATIENTS SERVED', 0, 0, 'L', 0);
    $this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, $this->no_of_patients, 0, 1, 'R', 1);
    
    $this->Cell(80, $this->RowHeight, 'NUMBER OF REQUESTS', 0, 0, 'L', 0);
    $this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, $this->no_of_requests, 0, 1, 'R', 1);
    
    $this->Cell(80, $this->RowHeight, 'NUMBER OF SERVICES REQUESTED', 0, 0, 'L', 0);
    $this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
    $this->Cell(30, $this->RowHeight, $this->no_of_services, 0, 1, 'R', 1);
    
  } */
    
    $cols = array();
  }
 
  function getTotalAmount($fromtime, $totime, $date){
    global $db;
   
    unset($result2);
    unset($row2);
    #echo"<br> after:".$result2;
    #echo"<br> after:".$row2['AMT_PAID'];
  
   /*if (($this->fromdate)&&($this->todate)) {
      
      #$where[]="DATE(e.discharge_date)='$this->date'";
      #$whereSQL = " AND (e.discharge_date>='".$this->fromdate."' AND e.discharge_date<='".$this->todate."')";
      $where[]=" DATE(ls.serv_dt) BETWEEN '".$this->fromdate."' AND '".$this->todate."'";
    }*/
    if($date){
      $where[]=" DATE(ls.serv_dt) = '".$date."'";
    }
  
  if (($fromtime)&&($totime)){
    $sql_time = " AND (ls.serv_tm BETWEEN '".$fromtime."' AND '".$totime."') ";
  }
   
    if ($where)
      $whereSQL = "AND (".implode(") AND (",$where).")";
  
  $sql2 = "SELECT distinct sum(if((is_cash),ld.price_cash_orig,ld.price_charge)) AS AMT_BILLED, 
      sum((CASE WHEN (pay.or_no IS NOT NULL OR '') AND pay.ref_source = 'LD' AND (pay.service_code IS NOT NULL)then ld.price_cash else 0.00 end)) AS AMT_PAID,
      sum((CASE WHEN (gr.ref_source = 'LD') AND (gr.grant_no IS NOT NULL) then ld.price_cash_orig else 0.00 end)) AS Charity, 
      sum((CASE WHEN ls.type_charge = 1 then ld.price_cash else 0.00 end)) AS LINGAP,
      sum((CASE WHEN sei.hcare_id = 18 then ld.price_cash else 0.00 end)) AS PHIC,  
      sum((CASE WHEN ls.type_charge = 0 AND (ls.discountid = 'C3' OR ls.discountid = 'C1' OR ls.discountid = 'C2') then (ld.price_cash_orig - ld.price_cash)
      WHEN sei.hcare_id = 18 then ld.price_cash else 0.00 end)) AS SOCIALIZED,
      sum(if((is_cash = 0), ld.price_cash, NULL)) AS CHARGE, 
      sum((CASE WHEN ls.type_charge!=0 AND ls.type_charge!=1 then (ld.price_cash) else 0.00 end)) AS OTHER 
      FROM seg_lab_serv AS ls 
      LEFT JOIN care_encounter AS e ON e.encounter_nr = ls.refno ".$this->enctype." 
      INNER JOIN seg_lab_servdetails AS ld ON ld.refno = ls.refno
      INNER JOIN seg_lab_services AS s ON s.service_code=ld.service_code
      INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code 
      LEFT JOIN seg_encounter_insurance AS sei ON sei.encounter_nr = ls.refno
      LEFT JOIN seg_granted_request AS gr ON gr.ref_no = ld.refno AND gr.service_code = ld.service_code AND gr.ref_source='LD' 
      LEFT JOIN seg_pay AS sp ON sp.pid=ls.pid AND (ISNULL(sp.cancel_date) OR sp.cancel_date='0000-00-00 00:00:00')
      LEFT JOIN seg_pay_request AS pay ON sp.or_no=pay.or_no AND pay.ref_no=ls.refno AND pay.ref_source='LD' AND pay.service_code=ld.service_code 
      WHERE ls.serv_dt IS NOT NULL $whereSQL
      AND ls.status NOT IN ('deleted','hidden','inactive','void') 
      AND ld.status NOT IN ('deleted','hidden','inactive','void') \n
      ".$sql_time."
      ".$this->group_cond."
      ".$this->enctype."
      ORDER BY ls.serv_dt ASC, ls.serv_tm ASC";
      
            
      #echo "<br><br><br>".$sql2.";";
      #die();
     
      $result2=$db->Execute($sql2);
      $row2 = $result2->FetchRow();
      $row2['AMT_BILLED']  = $row2['AMT_PAID']  + $row2['Charity'] + $row2['LINGAP'] + $row2['SOCIALIZED'] + $row2['PHIC'] + $row2['CHARGE'] + $row2['OTHER'];
       #echo"<br> before:".$result2;
    #echo"<br> before:".$row2['AMT_PAID'];
   # echo "<br><br><br>".$row2.";";
     #echo '<br>';
     #print_r($row2 );
     # die();  
      return $row2;
  }
  
  
  
  function FetchData() {    
    global $db;
  $dead_stat="'deleted','hidden','inactive','void'";
  
  #Added by Cherry 04-22-09
 
    
  if (($this->fromdate)&&($this->todate)) {
      
      $where[]="DATE(ls.serv_dt) BETWEEN '".$this->fromdate."' AND '".$this->todate."'";
    }

    if ($where)
      $whereSQL = "AND (".implode(") AND (",$where).")";
    
  #Edited by Cherry 04-22-09
  $sql = "SELECT distinct ls.serv_dt AS DATE_, 
          ls.serv_tm AS SHIFT, 
          if((is_cash),ld.price_cash_orig,ld.price_charge) AS AMT_BILLED, 
          (CASE WHEN (pay.or_no IS NOT NULL OR '') AND pay.ref_source = 'LD' AND (pay.service_code IS NOT NULL)then ld.price_cash else 0.00 end) AS AMT_PAID, 
          (CASE WHEN (gr.ref_source = 'LD') AND (gr.grant_no IS NOT NULL) then ld.price_cash_orig else 0.00 end) AS Charity, 
          (CASE WHEN ls.type_charge = 1 then ld.price_cash else 0.00 end) AS LINGAP,
          (CASE WHEN sei.hcare_id =18 then ld.price_cash else 0.00 end) AS PHIC,
          (CASE WHEN ls.type_charge = 0 AND (ls.discountid = 'C3' OR ls.discountid = 'C1' OR ls.discountid = 'C2') then (ld.price_cash_orig - ld.price_cash) WHEN sei.hcare_id = 18 then ld.price_cash else 0.00 end) AS SOCIALIZED, 
          if((is_cash = 0), ld.price_cash, 0.00) AS CHARGE,
          (CASE WHEN ls.type_charge!=0 AND ls.type_charge!=1 then (ld.price_cash) else 0.00 end) AS OTHER
          FROM seg_lab_serv AS ls 
          LEFT JOIN care_encounter AS e ON e.encounter_nr = ls.refno ".$this->enctype."
          LEFT JOIN seg_encounter_insurance AS sei ON sei.encounter_nr = ls.refno
          INNER JOIN seg_lab_servdetails AS ld ON ld.refno = ls.refno 
          INNER JOIN seg_lab_services AS s ON s.service_code=ld.service_code
          INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
          LEFT JOIN seg_granted_request AS gr ON gr.ref_no = ld.refno AND gr.service_code = ld.service_code AND gr.ref_source='LD'
          LEFT JOIN seg_pay AS sp ON sp.pid=ls.pid AND (ISNULL(sp.cancel_date) OR sp.cancel_date='0000-00-00 00:00:00')
          LEFT JOIN seg_pay_request AS pay ON sp.or_no=pay.or_no AND pay.ref_no=ls.refno AND pay.ref_source='LD' AND pay.service_code=ld.service_code            
          WHERE ls.serv_dt IS NOT NULL $whereSQL\n 
          AND ls.status NOT IN (".$dead_stat.")
          AND ld.status NOT IN (".$dead_stat.")
          ".$this->group_cond."   
          ".$this->enctype."   
          ORDER BY ls.serv_dt ASC, ls.serv_tm ASC";      
   
    $result=$db->Execute($sql);
  #echo "<br>count = ".$result->RecordCount();
  $this->no_of_services = $result->RecordCount();
  #echo $this->no_of_services;
  if (!$this->no_of_services)
    $this->no_of_services = 0;
    
  $sql_pat = "SELECT refno
                FROM seg_lab_serv AS ls 
        LEFT JOIN care_encounter AS e ON e.encounter_nr=ls.encounter_nr
                WHERE ls.serv_dt IS NOT NULL $whereSQL\n 
        AND ls.status NOT IN (".$dead_stat.")
        ".$this->enctype."
                GROUP BY ls.pid
                ORDER BY ls.serv_dt ASC, ls.serv_tm ASC";  
#echo "<br>sql = ".$sql_pat;

  $result_pat=$db->Execute($sql_pat);
  #echo "<br>count = ".$result->RecordCount();
  $this->no_of_patients = $result_pat->RecordCount();
  
  if (!$this->no_of_patients)
    $this->no_of_patients = 0;
    
  $sql_ref = "SELECT refno
                FROM seg_lab_serv AS ls
        LEFT JOIN care_encounter AS e ON e.encounter_nr=ls.encounter_nr
                WHERE ls.serv_dt IS NOT NULL $whereSQL\n 
        AND ls.status NOT IN (".$dead_stat.")
        ".$this->enctype."
                GROUP BY ls.refno
                ORDER BY ls.serv_dt ASC, ls.serv_tm ASC";  





#echo "<br>sql = ".$sql_ref;

  $result_ref=$db->Execute($sql_ref);
  #echo "<br>count = ".$result->RecordCount();
  $this->no_of_requests = $result_ref->RecordCount();
  
  if (!$this->no_of_requests)
    $this->no_of_requests = 0;  
  
  $SUM_AMT_BILLED = 0;
  $SUM_AMT_PAID = 0;
  $SUM_CHARITY = 0;
  $SUM_LINGAP = 0;
  $SUM_SOCIALIZED = 0;
  $SUM_OTHER = 0;
  
  # Added by Cherry 04-27-09
  $count_7_8am = 0;
  $count_8_9am = 0;
  $count_9_10am = 0;
  $count_10_11am = 0;
  $count_11_12nn = 0;
  $count_12_1pm = 0;
  $count_1_2pm = 0;
  $count_2_3pm = 0;
  $count_3_4pm = 0;
  $count_4_5pm = 0;
  $count_5_6pm = 0;
  $count_6_7pm = 0;
  $count_7_8pm = 0;
  $count_8_9pm = 0;
  $count_9_10pm = 0;
  $count_10_11pm = 0;
  $count_11_12mn = 0;
  $count_12_1am = 0;
  $count_1_2am = 0;
  $count_2_3am = 0;
  $count_3_4am = 0;
  $count_4_5am = 0;
  $count_5_6am = 0;
  $count_6_7am = 0;
  
  $total_amt_billed = 0;
  $total_amt_paid = 0;
  $total_amt_charity = 0;
  $total_amt_lingap = 0;
  $total_amt_socialized = 0;
  $total_amt_other = 0;
  
  $prev_date = "";
 # $checkdate = "";
  $first = TRUE;
  
  
    #edited by Cherry 04-21-09
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
   #echo "<br> <br>";
      while ($row=$result->FetchRow()) {
          $timeframe = $row['SHIFT'];
          $DATE = date("m/d",strtotime($row['DATE_'])); 
          #echo "<br>date today: ".$DATE;
          #echo "<br> previous date: ".$prev_date;
          #echo "<br>".$timeframe."<br>";
          #echo "<br>".$checkdate."<br>";
        #$timeframe = date("h:i A",strtotime($row['SHIFT']));
        /*if($timeframe == '12:10 AM'){
          $checkdate++;
        }*/
         
          
          
      #1st shift
      if(($timeframe >= '07:00:00') && ($timeframe <= '07:59:59')){
        if($count_7_8am == 0){
            $shift = "07:00 AM - 08:00 AM ";
            $row2 = $this->getTotalAmount('07:00:00', '07:59:59', $row['DATE_']);
            $total_amt_billed += $row2['AMT_BILLED'];
            $total_amt_paid += $row2['AMT_PAID'];
            $total_amt_charity += $row2['Charity'];
            $total_amt_lingap += $row2['LINGAP'];
            $total_amt_socialized += $row2['SOCIALIZED'];
            $total_amt_other += $row2['OTHER'];
            $count_7_8am++; 
            #echo "entered 7am-8am";
          }
        
     }
     else if(($timeframe >= '08:00:00') && ($timeframe <= '08:59:59')){    
      if($count_8_9am == 0){
        $shift = "08:00 AM - 09:00 AM";
        $row2 = $this->getTotalAmount('08:00:00', '08:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER']; 
        $count_8_9am++;
        #echo "entered 8am-9am";
      }    
        
     }
     else if(($timeframe >= '09:00:00') && ($timeframe <= '09:59:59')){
      if($count_9_10am == 0){
        $shift = "09:00 AM - 10:00 AM";
        $row2 = $this->getTotalAmount('09:00:00', '09:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];   
        $count_9_10am++;
        #echo "entered 9am-10am";  
      }    
        
     }
    else if(($timeframe >= '10:00:00') && ($timeframe <= '10:59:59')){
      if($count_10_11am == 0){
        $shift = "10:00 AM - 11:00 AM";
        $row2 = $this->getTotalAmount('10:00:00', '10:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER']; 
        $count_10_11am++;
        #echo "entered 10am-11am";
      } 
        
    }
    else if(($timeframe >= '11:00:00') && ($timeframe <= '11:59:59')){
      if($count_11_12nn == 0){
       $shift = "11:00 AM - 12:00 PM";
        $row2 = $this->getTotalAmount('11:00:00', '11:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];
        $count_11_12nn++;
        #echo "entered 11am-12nn";
      }  
      
    }
    else if(($timeframe >= '12:00:00') && ($timeframe <= '12:59:59')){
      if($count_12_1pm == 0){
        $shift = "12:00 PM - 01:00 PM";
        $row2 = $this->getTotalAmount('12:00:00', '12:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];
        $count_12_1pm++;
        #echo "entered 12nn-1pm";
      }  
      
    }
    else if(($timeframe >= '13:00:00') && ($timeframe <= '13:59:59')){
      if($count_1_2pm == 0){
        $shift = "01:00 PM - 02:00 PM";
        $row2 = $this->getTotalAmount('13:00:00', '13:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];
        $count_1_2pm++; 
        #echo "entered 1pm-2pm";
      }  
      
    }
    else if(($timeframe >= '14:00:00') && ($timeframe <= '14:59:59')){
      if($count_2_3pm == 0){
        $shift = "02:00 PM - 03:00 PM";
        $row2 = $this->getTotalAmount('14:00:00', '14:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];  
        $count_2_3pm++;
        #echo "entered 2pm-3pm";
      }  
      
    }
    #2nd shift
    else if(($timeframe >= '15:00:00') && ($timeframe <= '15:59:59')){
      if($count_3_4pm == 0){
        $shift = "03:00 PM - 04:00 PM";
        $row2 = $this->getTotalAmount('15:00:00', '15:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];
        $count_3_4pm++; 
        #echo "entered 3pm-4pm";     
      }  
      
    }
    else if(($timeframe >= '16:00:00') && ($timeframe <= '16:59:59')){
      if($count_4_5pm == 0){
        $shift = "04:00 PM - 05:00 PM";
        $row2 = $this->getTotalAmount('16:00:00', '16:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];  
        $count_4_5pm++;
        #echo "entered 4pm-5pm"; 
      }  
      
    }
    else if(($timeframe >= '17:00:00') && ($timeframe <= '17:59:59')){
      if($count_5_6pm == 0){
        $shift = "05:00 PM - 06:00 PM";
        $row2 = $this->getTotalAmount('17:00:00', '17:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];
        $count_5_6pm++;  
        #echo "entered 5pm-6pm";  
      }
        
    }
    else if(($timeframe >= '18:00:00') && ($timeframe <= '18:59:59')){
      if($count_6_7pm == 0){
        $shift = "06:00 PM - 07:00 PM";
        $row2 = $this->getTotalAmount('18:00:00', '18:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];  
        $count_6_7pm++; 
        #echo "entered 6pm-7pm";
      }  
      
    }
    else if(($timeframe >= '19:00:00') && ($timeframe <= '19:59:59')){
      if($count_7_8pm == 0){
        $shift = "07:00 PM - 08:00 PM";
        $row2 = $this->getTotalAmount('19:00:00', '19:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];
        $count_7_8pm++;    
        #echo "entered 7pm-8pm";
      }  
      
    }
    else if(($timeframe >= '20:00:00') && ($timeframe <= '20:59:59')){
      if($count_8_9pm == 0){
        $shift = "08:00 PM - 09:00 PM";
        $row2 = $this->getTotalAmount('20:00:00', '20:59:59', $row['DATE_']);
        $total_amt_billed += $row2['AMT_BILLED'];
        $total_amt_paid += $row2['AMT_PAID'];
        $total_amt_charity += $row2['Charity'];
        $total_amt_lingap += $row2['LINGAP'];
        $total_amt_socialized += $row2['SOCIALIZED'];
        $total_amt_other += $row2['OTHER'];
        $count_8_9pm++;
        #echo "entered 8pm-9pm";     
      }  
      
    }
    else if(($timeframe >= '21:00:00') && ($timeframe <= '21:59:59')){
      if($count_9_10pm == 0){
        $shift = "09:00 PM - 10:00 PM";
         $row2 = $this->getTotalAmount('21:00:00', '21:59:59', $row['DATE_']);
         $total_amt_billed += $row2['AMT_BILLED'];
         $total_amt_paid += $row2['AMT_PAID'];
         $total_amt_charity += $row2['Charity'];
         $total_amt_lingap += $row2['LINGAP'];
         $total_amt_socialized += $row2['SOCIALIZED'];
         $total_amt_other += $row2['OTHER'];
         $count_9_10pm++;
         #echo "entered 9pm-10pm";    
      }   
      
    }
    else if(($timeframe >= '22:00:00') && ($timeframe <= '22:59:59')){
      if($count_10_11pm == 0){
        $shift = "10:00 PM - 11:00 PM";
         $row2 = $this->getTotalAmount('22:00:00', '22:59:59', $row['DATE_']);
         $total_amt_billed += $row2['AMT_BILLED'];
         $total_amt_paid += $row2['AMT_PAID'];
         $total_amt_charity += $row2['Charity'];
         $total_amt_lingap += $row2['LINGAP'];
         $total_amt_socialized += $row2['SOCIALIZED'];
         $total_amt_other += $row2['OTHER']; 
         $count_10_11pm++;
         #echo "entered 10pm-11pm";
      }   
      
    }
  
    #3rd shift
    else if(($timeframe >= '23:00:00') && ($timeframe <= '23:59:59')){
      $prev_date = $DATE;
      if($count_11_12mn == 0){
        $shift = "11:00 PM - 12:00 AM";
         $row2 = $this->getTotalAmount('23:00:00', '23:59:59', $row['DATE_']);
         $total_amt_billed += $row2['AMT_BILLED'];
         $total_amt_paid += $row2['AMT_PAID'];
         $total_amt_charity += $row2['Charity'];
         $total_amt_lingap += $row2['LINGAP'];
         $total_amt_socialized += $row2['SOCIALIZED'];
         $total_amt_other += $row2['OTHER']; 
         $count_11_12mn++;
         #echo "entered 11pm-12midnight";
      }   
      
    }
    else if(($timeframe >= '00:00:00') && ($timeframe <= '00:59:59')){
      if($DATE!=$prev_date){
        $count_7_8am = 0;
        $count_8_9am = 0;
        $count_9_10am = 0;
        $count_10_11am = 0;
        $count_11_12nn = 0;
        $count_12_1pm = 0;
        $count_1_2pm = 0;
        $count_2_3pm = 0;
        $count_3_4pm = 0;
        $count_4_5pm = 0;
        $count_5_6pm = 0;
        $count_6_7pm = 0;
        $count_7_8pm = 0;
        $count_8_9pm = 0;
        $count_9_10pm = 0;
        $count_10_11pm = 0;
        $count_11_12mn = 0;
        $count_12_1am = 0;
        $count_1_2am = 0;
        $count_2_3am = 0;
        $count_3_4am = 0;
        $count_4_5am = 0;
        $count_5_6am = 0;
        $count_6_7am = 0;
            
        /*$total_amt_billed = 0;
            $total_amt_paid = 0;
            $total_amt_charity = 0;
            $total_amt_lingap = 0;
            $total_amt_socialized = 0;
            $total_amt_other = 0;   */
      }
      else{
        #$checkdate = FALSE;
      }

   
   
        #$DATE = date("m/d",strtotime($row['DATE_'])); 
     
      
   
    
    
   if($shift!=$old_shift){      
         $this->Data[]=array(
          $DATE,
          $shift,
          number_format($row2['AMT_BILLED'],2,'.',','),
          number_format($row2['AMT_PAID'],2,'.',','),
          number_format($row2['Charity'],2,'.',','),
          number_format($row2['LINGAP'],2,'.',','),
          number_format($row2['SOCIALIZED'],2,'.',','),
          number_format($row2['PHIC'],2,'.',','),
          number_format($row2['CHARGE'],2,'.',','),
          number_format($row2['OTHER'],2,'.',',')
          );
    }
     
     
     $old_shift = $shift;
      }  
     #echo '<br>';
     #print_r($row_total);
     #$row_total['AMT_PAID']  = $row_total['AMT_BILLED'] - ($row_total['Charity'] + $row_total['LINGAP'] + $row_total['SOCIALIZED'] + $row_total['OTHER']);
    
    #echo $row_total['AMT_PAID'];
    #echo "total: ".$row_total['Charity']." "; 
  
    $this->SUM_AMT_BILLED = $total_amt_billed;
    $this->SUM_AMT_PAID = $total_amt_paid;
    $this->SUM_CHARITY = $total_amt_charity;
    $this->SUM_LINGAP = $total_amt_lingap;
    $this->SUM_SOCIALIZED = $total_amt_socialized;
    $this->SUM_OTHER = $total_amt_other;
     
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

$fromdate = $_GET['fromdate'];
$todate = $_GET['todate'];
$fromshift = $_GET['fromtime'];
$toshift = $_GET['totime'];
#$pat_type = $_GET['patient_type'];
#echo "pat = ".$pat_type;
#Added by Cherry 04-21-09
#$servgroup = $_GET['serv_group'];
#echo "section = ".$servgroup;

$rep = new RepGen_Billing_Accountability($fromdate, $todate, $fromshift, $toshift);
$rep->AliasNbPages();
#$rep->FetchData();
$rep->Report();
?>

