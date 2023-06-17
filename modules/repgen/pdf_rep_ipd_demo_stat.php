<?php
   #created by Cherry
   #11-17-09
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_DemographicData extends RepGen{
var $colored = TRUE;
var $from, $to; 
var $location;    
                 
                           
   function RepGen_DemographicData ($from, $to, $location) {
    global $db;
    $this->RepGen("MEDICAL RECORDS: DISCHARGE - TYPE OF ACCOMMODATION");
        
    $this->ColumnWidth = array(60, 18,24,24,24, 18,24,24,24, 15,20);
    $this->RowHeight = 5;
    $this->TextHeight = 5;
    $this->TextPadding = 0.2;
    $this->Alignment = array('L', 'C','C','C','C', 'C','C','C','C', 'C','C');
    $this->PageOrientation = "L";
    $this->NoWrap = FALSE;
    $this->LEFTMARGIN = 2;
    $this->location = $location;
    if ($from) $this->from=date("Y-m-d",strtotime($from));
        if ($to) $this->to=date("Y-m-d",strtotime($to));    
    
    $this->useMultiCell = TRUE;
    #$this->SetFillColor(0xFF);
    $this->SetFillColor(255);
    if ($this->colored)  $this->SetDrawColor(0xDD);
    
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
    $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',75,8,20);
    $this->SetFont("Arial","I","9");
    $total_w = 165;
    $this->Cell(55,4);
      #$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
    $this->Cell(55,4);
      #$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
      $this->Ln(2);
    $this->SetFont("Arial","B","10");
    $this->Cell(55,4);
      #$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
    $this->SetFont("Arial","","9");
    $this->Cell(55,4);
      #$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
    $this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
      $this->Ln(4);
      $this->SetFont('Arial','B',12);
    $this->Cell(55,5);
    
    $this->Cell($total_w,4,'HOSPITAL STATISTICAL REPORT (Inpatient)',$border2,1,'C');
     $this->SetFont('Arial','B',9);
    $this->Cell(55,5);
    
    if ($this->from==$this->to)
      $text = "For ".date("F j, Y",strtotime($this->from));
    else
        #$text = "Full History";
      $text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
      
      $this->Cell($total_w,4,$text,$border2,1,'C');
      $this->Ln(4);
    
    
    
        switch($this->location){
          case '0' :
                    $dist = "Congressional districts within and outside the region";
                    break;
          case '1': 
                    $dist = "Congressional districts within the province";
                    break;
          case '2': 
                    $dist = "Congressional districts outside the province but within the region";
                    break;
          case '3':
                    $dist = "Congressional districts outside the region";
                    break;
        }
    
    $this->Cell(2,5);
    $this->Cell($total_w,4,$dist,$border2,1,'L');
    $this->Ln();

    # Print table header    
      $this->SetFont('Arial','B',8);
    #if ($this->colored) $this->SetFillColor(0xED);
    if ($this->colored) $this->SetFillColor(255);
    $this->SetTextColor(0);
    $row=6;
    
        $this->Cell(60, 4, "", "TLR", 0, 'C');
    $this->Cell(180, 4, "Number of Patients", "1", 0, 'C');
    $this->Cell(15, 4, "", "TLR", 0, 'C');  
        $this->Cell(20, 4, "", "TLR", 1, 'C');  
        
        $this->Cell(60, 4, "Districts", "LR", 0, 'C');
    
    $this->Cell(90, 4, "Pay", "1", 0, 'C');
    $this->Cell(90, 4, "Service", "1", 0, 'C');
        
        $this->Cell(15, 4, "Total", "LR", 0, 'C');  
        $this->Cell(20, 4, "% of", "LR", 1, 'C'); 
        
        $this->Cell(60, 4, "", "LR", 0, 'C');
        $this->Cell(18, 4, "Non-PHIC", "LR", 0, 'C'); 
        $this->Cell(48, 4, "PHIC", "1", 0, 'C'); 
        $this->Cell(24, 4, "", "LR", 0, 'C'); 
        
        $this->Cell(18, 4, "Non-PHIC", "LR", 0, 'C'); 
        $this->Cell(48, 4, "PHIC", "1", 0, 'C');
        $this->Cell(24, 4, "", "LR", 0, 'C');  
        
        $this->Cell(15, 4, "", "LR", 0, 'C');  
        $this->Cell(20, 4, "Grand", "LR", 1, 'C'); 
        
        $this->Cell(60, 4, "", "LRB", 0, 'C');
        $this->Cell(18, 4, "", "BLR", 0, 'C');  
        
        $this->Cell(24, 4, "Member/Dep", "1", 0, 'C'); 
        $this->Cell(24, 4, "Indigent", "1", 0, 'C'); 
        $this->Cell(24, 4, "OWWA", "LRB", 0, 'C'); 
        
        $this->Cell(18, 4, "", "LRB", 0, 'C');
        $this->Cell(24, 4, "Member/Dep", "1", 0, 'C'); 
        $this->Cell(24, 4, "Indigent", "1", 0, 'C'); 
        $this->Cell(24, 4, "OWWA", "LRB", 0, 'C'); 
        
        $this->Cell(15, 4, "", "LRB", 0, 'C');  
        $this->Cell(20, 4, "Total", "LRB", 0, 'C');
    
    $this->Ln();
  }

  function Footer(){
    $this->SetY(-7);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
  }
    
  function BeforeRow() {
    $this->FONTSIZE = 8;
    if ($this->colored) {
      if (($this->ROWNUM%2)>0) 
        #$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
        $this->FILLCOLOR=array(255, 255, 255);
      else
        $this->FILLCOLOR=array(255,255,255);
      $this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
      #$this->DRAWCOLOR = array(255,255,255);
    }
  }
  
  function BeforeData() {
    if ($this->colored) {
      $this->DrawColor = array(0xDD,0xDD,0xDD);
      #$this->DrawColor = array(255,255,255);
    }
  }
  
  function BeforeCellRender() {
    $this->FONTSIZE = 8;
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
      $this->SetFont('Arial','B',9);
      $this->SetFillColor(255);
      $this->SetTextColor(0);
      $this->Cell(275, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
    }
    
    $cols = array();
  }  

    
  function FetchData(){
    global $db;
    
    //To get grand total
    $sql_total = "SELECT SUM(t.total) as total FROM (SELECT count(e.encounter_nr) AS total

                  FROM  care_encounter AS e
                  INNER JOIN care_person AS p ON p.pid = e.pid 
                  LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr = e.encounter_nr
                  LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                  LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                  LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                  LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                  LEFT JOIN (SELECT s.pid,SUBSTRING(MAX(CONCAT(s.grant_dte,s.discountid)),20) AS discountid 
                        FROM seg_charity_grants_pid AS s 
                        WHERE s.discountid='D'
                        GROUP BY s.pid 
                        ORDER BY s.pid, s.grant_dte DESC) AS soc ON soc.pid=e.pid
                  WHERE e.encounter_type IN (3,4) 
                  AND (DATE(e.encounter_date) BETWEEN '".$this->from."' AND '".$this->to."') 
                  AND e.status NOT IN ('deleted','hidden','inactive','void') 
                  ";
    
     //---------------------------------------------------------------------------
    
     //within Davao del Sur
     $sql_loc1 = "SELECT  mun.mun_name AS Districts, prov.prov_name,
                SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member, 
                SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay, SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=1 then 1 else 0 end) AS charity_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_member, 
                SUM(CASE WHEN w.accomodation_type=1 then 1 else 0 end) AS total_charity, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) AS total
                
                FROM care_encounter AS e 
                
                INNER JOIN care_person AS p ON p.pid=e.pid
                INNER JOIN care_ward AS w ON e.current_ward_nr=w.nr 
                LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
                LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
                LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr 
                LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

                LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                 
                WHERE e.encounter_type IN (3,4) 
                AND (DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."') 
                AND e.status NOT IN ('deleted','hidden','inactive','void') 
                AND prov.prov_name like '%Davao del Sur%'
                GROUP BY mun.mun_name
                ORDER BY SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) DESC;
                ";
   //========================================================
       //All from Region XI excluding Davao del Sur
       
     $sql_loc2 = "SELECT  mun.mun_name AS Districts, prov.prov_name, reg.region_name,
                SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member, 
                SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay, SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=1 then 1 else 0 end) AS charity_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_member, 
                SUM(CASE WHEN w.accomodation_type=1 then 1 else 0 end) AS total_charity, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) AS total
                FROM care_encounter AS e 
                INNER JOIN care_person AS p ON p.pid=e.pid
                INNER JOIN care_ward AS w ON e.current_ward_nr=w.nr 
                LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
                LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
                LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr 
                LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

                LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                 
                WHERE e.encounter_type IN (3,4) 
                AND (DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."') 
                AND e.status NOT IN ('deleted','hidden','inactive','void') 
                AND reg.region_name='Region XI'
                AND prov.prov_name!='DAVAO DEL SUR'
                GROUP BY mun.mun_name
                ORDER BY prov.prov_name, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) DESC;";
 
  //=============================================================
      //Outside Region XI
      
      $sql_loc3 = "SELECT  mun.mun_name AS Districts, prov.prov_name, reg.region_name,
                    SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic, 
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic, 
                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent, 
                    SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa, 
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member, 
                    SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay, SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_non_phic, 
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic, 
                    SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_indigent, 
                    SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=1 then 1 else 0 end) AS charity_owwa, 
                    SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_member, 
                    SUM(CASE WHEN w.accomodation_type=1 then 1 else 0 end) AS total_charity, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) AS total
                    
                    FROM care_encounter AS e 
                    INNER JOIN care_person AS p ON p.pid=e.pid
                    INNER JOIN care_ward AS w ON e.current_ward_nr=w.nr 
                    LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
                    LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
                    LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr 
                    LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

                    LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                    LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                    LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                     
                    WHERE e.encounter_type IN (3,4) 
                    AND (DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."') 
                    AND e.status NOT IN ('deleted','hidden','inactive','void') 
                    AND reg.region_name!='Region XI'
                    GROUP BY mun.mun_name
                    ORDER BY reg.region_name,prov.prov_name, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) DESC;";
 
 //===============================================================
      //All Regions (no discrimination =^_^=)
      $sql_loc0 = "SELECT  mun.mun_name AS Districts, prov.prov_name, reg.region_name,
                SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member, 
                SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay, SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_non_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic, 
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_indigent, 
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=1 then 1 else 0 end) AS charity_owwa, 
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=1 then 1 else 0 end) AS charity_phic_member, 
                SUM(CASE WHEN w.accomodation_type=1 then 1 else 0 end) AS total_charity, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) AS total
                FROM care_encounter AS e 
                INNER JOIN care_person AS p ON p.pid=e.pid
                INNER JOIN care_ward AS w ON e.current_ward_nr=w.nr 
                LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
                LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
                LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr 
                LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

                LEFT JOIN seg_municity AS mun ON mun.mun_nr=p.mun_nr
                LEFT JOIN seg_provinces AS prov ON prov.prov_nr=mun.prov_nr
                LEFT JOIN seg_regions AS reg ON reg.region_nr=prov.region_nr
                 
                WHERE e.encounter_type IN (3,4) 
                AND (DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."') 
                AND e.status NOT IN ('deleted','hidden','inactive','void') 
                GROUP BY mun.mun_name
                ORDER BY reg.region_name,prov.prov_name, SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2) then 1 else 0 end) DESC;";
                      

      if($this->location == 1){
        $sql = $sql_loc1;
        $sql_total .="AND prov.prov_name = 'Davao del Sur'
                    GROUP BY e.encounter_nr
                    ORDER BY count(e.encounter_nr) DESC) as t"; 
      }
      else if($this->location == 2){
        $sql = $sql_loc2;
        $sql_total .="AND reg.region_name='Region XI' 
                      AND prov.prov_name!='DAVAO DEL SUR'
                      GROUP BY e.encounter_nr
                      ORDER BY count(e.encounter_nr) DESC) as t ";
      }
      else if($this->location == 3){
        $sql = $sql_loc3;
        $sql_total .="AND reg.region_name!='Region XI'
                      GROUP BY e.encounter_nr
                      ORDER BY count(e.encounter_nr) DESC) as t";
      }
      else if ($this->location == 0){
        $sql = $sql_loc0;
        $sql_total .="GROUP BY e.encounter_nr
                      ORDER BY count(e.encounter_nr) DESC) as t";
      }
      
      $tot_result = $db->Execute($sql_total);
      $grandtotal = $tot_result->FetchRow();
      #echo $grandtotal['total'];
      $result=$db->Execute($sql);
        if ($result) {
      
          $this->_count = $result->RecordCount();
            $this->Data=array();
        
              
          while ($row=$result->FetchRow()) {
            
         $percentage = ($row['total'] / $grandtotal['total']) * 100;
         $percentage = round($percentage,2);
         
         $this->Data[] = array(
                    $row['Districts'],
                    
                    $row['pay_non_phic'],
                    $row['pay_phic'],
                    $row['pay_phic_indigent'],
                    $row['pay_owwa'],
                    
                    $row['charity_non_phic'],
                    $row['charity_phic'],
                    $row['charity_phic_indigent'],
                    $row['charity_owwa'],
                    
                    $row['total'],
                     $percentage."%",   
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

$rep = new RepGen_DemographicData($_GET['from'], $_GET['to'], $_GET['location']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
