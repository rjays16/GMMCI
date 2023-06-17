<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
  

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

  class RepGen_Socserv_MSW_Registry extends RepGen {
  var $date;
  var $colored = FALSE;
  var $encoder_name, $fromdate, $todate;
  
  
  function RepGen_Socserv_MSW_Registry ($class, $encoder_name, $fromdate, $todate) {
    global $db;
    #$this->RepGen("PATIENT'S LIST","L","Legal");
    $this->RepGen("MSW REGISTRY","L","Legal");
    $this->SetAutoPageBreak(FALSE);
    
    # 165
    $this->ColumnWidth = array(22,15,20,50,10,16,60,22,22,15,55,25,15);
    #$this->RowHeight = 7;
    $this->RowHeight = 4.5;
    $this->TextHeight = 5;
    
    $this->Alignment = array('C','C','C','C','C','C','C','C','C','C','C','C','C');
    #$this->PageOrientation = "L";
    #$this->PageFormat = "Legal";
    $this->LEFTMARGIN=5;
    $this->DEFAULT_TOPMARGIN = 5;
    $this->NoWrap = false;
    
    if ($encoder_name!='all') 
      $this->encoder_name = mb_strtoupper($encoder_name);
    else  
      $this->encoder_name = "ALL SOCIAL WORKERS";
      
    if (($fromdate)&&($fromdate!='0000-00-00'))
      $this->fromdate = mb_strtoupper(date("F d, Y",strtotime($fromdate)));
      
    if (($todate)&&($todate!='0000-00-00'))
      $this->todate = mb_strtoupper(date("F d, Y",strtotime($todate)));  
      
    
    $this->SetFillColor(0xFF);
    if ($this->colored) $this->SetDrawColor(0xDD);
  }
  
  function Header() {
    global $root_path, $db;
    $objInfo = new Hospital_Admin();
    $pers_obj=new Personell;
    
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
    
    $this->SetFont("Arial","B","14");
    $this->Cell(17,4);
      $this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
    
    $this->SetFont("Arial","B","12");    
    $this->Cell(17,4);
      $this->Cell($total_w,4,'MSW REGISTRY',$border2,1,'C');
    #$this->Ln(2);
    
    $this->Cell(5,5);
    $this->SetFont('Arial','',10);
      if ($this->fromdate==$this->todate)
          $text = "For ".date("F j, Y",strtotime($this->fromdate));
      else
            $text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));
    
    $this->Cell($total_w,4,$text,$border2,1,'C');
      $this->Ln(5);
    
    # Print table header
    $this->SetFont('Arial', 'B', 9);
    $tablerow = 4;
    
    $this->Cell($this->ColumnWidth[0], $tablerow, "DATE OF", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[1], $tablerow, "MSW", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[2], $tablerow, "HOSP NO.", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[3], $tablerow, "NAME", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[4], $tablerow, "AGE", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[5], $tablerow, "GENDER", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[6], $tablerow, "ADDRESS", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[7], $tablerow, "CLINICAL", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[8], $tablerow, "MED.", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[9], $tablerow, "MSW", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[10], $tablerow, "SOCIAL DIAGNOSIS", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[11], $tablerow, "SOURCE OF", "TR", 0, 'C');
    $this->Cell($this->ColumnWidth[12], $tablerow, "MSW","T", 1, 'C');
    
    $this->Cell($this->ColumnWidth[0], $tablerow, "INTAKE", "BR", 0, 'C');
    $this->Cell($this->ColumnWidth[1], $tablerow, "NO.", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[2], $tablerow, "", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[3], $tablerow, "", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[4], $tablerow, "", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[5], $tablerow, "", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[6], $tablerow, "", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[7], $tablerow, "AREA ENTRY", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[8], $tablerow, "CAT", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[9], $tablerow, "CLASS", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[10], $tablerow, "", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[11], $tablerow, "REFFERAL", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[12], $tablerow, "", "B", 1, 'C');
    
    /*for($cnt = 0; $cnt<5; $cnt++){
      $this->Cell($this->ColumnWidth[$cnt], $tablerow, "", "RB", 0, 'C');
    }
    $this->Cell($this->ColumnWidth[5]/2, $tablerow, "F", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[5]/2, $tablerow, "M", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[6], $tablerow, "", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[7]/3, $tablerow, "ER", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[7]/3, $tablerow, "OPD", "RB", 0, 'C');
    $this->Cell($this->ColumnWidth[7]/3, $tablerow, "IP", "RB", 0, 'C');
    
    for($cnt1 = 8; $cnt1<=12; $cnt1++){
      if($cnt1!=12)
        $this->Cell($this->ColumnWidth[$cnt1], $tablerow, "", "RB", 0, 'C');
      else
        $this->Cell($this->ColumnWidth[$cnt1], $tablerow, "", "B", 1, 'C');
    }*/
  
  }
  
  function Footer()
  {
    $this->SetY(-7);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:s A"),0,0,'R');
  }
  
  function BeforeRow() {
    $this->FONTSIZE = 10;
    if ($this->colored) {
      if (($this->ROWNUM%2)>0) 
        #$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
        $this->FILLCOLOR=array(255,255,255);
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
        #$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
        $this->RENDERCELL->FillColor=array(255,255,255);
      else
        $this->RENDERCELL->FillColor=array(255,255,255);
    }
  }
  
  function AfterData() {
    global $db;
    
    if (!$this->_count) {
      $this->SetFont('Arial','B',10);
      $this->SetFillColor(255);
      $this->SetTextColor(0);
      $this->Cell(285, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
    }  
    
    $cols = array();
  }
  
  //function FetchData($class, $encoder_name, $fromdate, $todate) {    
  function FetchData(){ 
    global $db;
    
    $sql = "SELECT DISTINCT IF(e.encounter_type IN (2), DATE(cgp.grant_dte), DATE(cg.grant_dte)) AS DATE_, sp.mss_no AS MSW_No, sp.pid AS HOSP_NO,
            CONCAT(p.name_last,', ', p.name_first) AS Fullname, p.age AS Age, p.sex AS Gender,
            IF(p.street_name IS NOT NULL, p.street_name, '') AS Street, 
            IF(p.brgy_nr = 0, '', sb.brgy_name) AS Brgy,
            IF(p.mun_nr = 0, '', sm.mun_name) AS Municity,
            e.encounter_type AS Clinical_Area,
            d.name_formal AS MedCat,
            IF(e.encounter_type IN (2), cgp.discountid, cg.discountid) AS MSW_Class,
            IF(e.encounter_type IN (2), cgp.personal_circumstance, cg.personal_circumstance) AS PC,
            ssm.mod_subdesc AS Diagnosis,
            '' AS Source,
            IF(e.encounter_type IN (2), cgp.sw_nr, cg.sw_nr) AS MSW
            FROM seg_social_patient AS sp
            LEFT JOIN care_person AS p ON p.pid = sp.pid
            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr
            LEFT JOIN seg_municity AS sm ON sm.mun_nr = p.mun_nr
            INNER JOIN care_encounter AS e ON e.pid = p.pid
            LEFT JOIN care_department AS d ON d.nr = e.current_dept_nr
            LEFT JOIN seg_charity_grants AS cg ON cg.encounter_nr = e.encounter_nr
            LEFT JOIN seg_charity_grants_pid AS cgp ON cgp.pid = e.pid
            LEFT JOIN seg_social_service_submodifiers AS ssm ON ssm.mod_subcode = cgp.personal_circumstance OR ssm.mod_subcode = cg.personal_circumstance
            /*WHERE DATE(cgp.grant_dte) BETWEEN '2009-02-01' AND '2009-02-05'
            OR DATE(cg.grant_dte) BETWEEN '2009-02-01' AND '2009-02-05'*/
            /*WHERE DATE(sp.create_time) BETWEEN '2009-02-01' AND '2009-02-05'*/
            WHERE IF(e.encounter_type IN (2), DATE(cgp.grant_dte), DATE(cg.grant_dte)) BETWEEN '2009-02-01' AND '2009-02-02'
            GROUP BY Fullname 
            ORDER BY DATE_, Fullname ASC;";
    #echo "sql = ".$sql;
    $result=$db->Execute($sql);
    if ($result) {
      $this->_count = $result->RecordCount();
      $this->Data=array();
      $total_gross_amount = 0;
      while ($row=$result->FetchRow()) {
        if($row['Street'])
          $address = $row['Street']." ";
        if($row['Brgy'])
          $address .= $row['Brgy'];
        if($row['Municity'])
          $address .= ", ".$row['Municity'];
        
        if($row['Clinical_Area']==1)
          $area = "ER";
        else if($row['Clinical_Area']==2)
          $area = "OPD";
        else if(($row['Clinical_Area']==3) || ($row['Clinical_Area'])==4)
          $area = "IP";
        
        $this->Data[]=array(
          $row['DATE_'],
          $row['MSW_No'],
          $row['HOSP_NO'],
          $row['Fullname'],
          $row['Age'],
          strtoupper($row['Gender']),
          ucwords(strtolower($address)),
          $area,
          $row['MedCat'],
          $row['MSW_Class'],
          $row['Diagnosis'],
          $row['Source'],
          $row['MSW']
          
        );
      
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
$encoder_name = $_GET['encoder'];
$class = $_GET['class'];

#echo "fromdate = ".$fromdate;
#echo "<br>todate = ".$todate;
#echo "<br>encoder_name = ".$encoder_name;
#echo "<br>class = ".$class;

$iss = new RepGen_Socserv_MSW_Registry($class, $encoder_name, $fromdate, $todate);
$iss->AliasNbPages();
//$iss->FetchData($class, $encoder_name, $fromdate, $todate);
$iss->FetchData();
$iss->Report();

?>