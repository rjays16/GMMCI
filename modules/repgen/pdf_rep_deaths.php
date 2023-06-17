<?php
#created by Cherry 12-09-09
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_Deaths extends RepGen{
var $colored = TRUE;
var $from, $to;     
                 
                           
   function RepGen_Deaths ($from, $to) {
        global $db;
        $this->RepGen("MEDICAL RECORDS: DISCHARGE - TYPE OF ACCOMMODATION");
        
        $this->ColumnWidth = array(25, 25, 25, 25, 25, 25, 25, 25);
        $this->RowHeight = 5;
        $this->TextHeight = 5;
        $this->TextPadding = 0.2;
        $this->Alignment = array('L', 'C','C','C','C', 'C','C','C','C', 'C','C');
        $this->PageOrientation = "P";
        $this->NoWrap = FALSE;
        $this->LEFTMARGIN = 8;
        
        if ($from) $this->from=date("Y-m-d",strtotime($from));
        if ($to) $this->to=date("Y-m-d",strtotime($to));    
    
        $this->useMultiCell = TRUE;
        #$this->SetFillColor(0xFF);
        $this->SetFillColor(255);
        if ($this->colored)    $this->SetDrawColor(0xDD);
        
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
        #$this->Cell(50,4);
          #$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
        $this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
        #$this->Cell(50,4);
          #$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
        $this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
          $this->Ln(2);
        $this->SetFont("Arial","B","10");
        #$this->Cell(50,4);
          #$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
        $this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
        $this->SetFont("Arial","","9");
        #$this->Cell(50,4);
          #$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
        $this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
          $this->Ln(4);
          $this->SetFont('Arial','B',12);
        #$this->Cell(50,5);
        
        $this->Cell(0,4,'TYPES OF DEATHS FOR THE GIVEN PERIOD',$border2,1,'C');
         $this->SetFont('Arial','B',9);
        #$this->Cell(50,5);
        
        if ($this->from==$this->to)
            $text = "For ".date("F j, Y",strtotime($this->from));
        else
              #$text = "Full History";
            $text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
            
          $this->Cell(0,4,$text,$border2,1,'C');
        $this->Ln(5);

        # Print table header        
        $this->SetFont('Arial','B',8);
        #if ($this->colored) $this->SetFillColor(0xED);
        if ($this->colored) $this->SetFillColor(255);
        $this->SetTextColor(0);
        $row=5;
        
        $this->Cell($this->ColumnWidth[0]+$this->ColumnWidth[1], $row, "FETAL DEATHS", 1, 0, 'C');
        for($num=2; $num<8; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "TLR", 0);
        }
        $this->Ln();
        
        $this->Cell($this->ColumnWidth[0], $row, "< 22 weeks", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[1], $row, "> 22 weeks", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[2], $row, "Neonatal", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[3], $row, "Infant", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[4], $row, "Maternal", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[5], $row, "ER", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[6], $row, "Dead on", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[7], $row, "Deaths", "LR", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0], $row, "or", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[1], $row, "or", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[2], $row, "Deaths", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[3], $row, "Deaths", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[4], $row, "Deaths", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[5], $row, "Deaths", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[6], $row, "Arrival", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[7], $row, "< 48 hours", "LR", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0], $row, "< 500g", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[1], $row, "> 500g", "LR", 0, 'C');
        for($num=2; $num<8; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LR", 0);
        }
        
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

    function getLOS($current_dept_nr){
         global $db;        
         
        $tot_sql = "SELECT SUM(len.totallenstay) AS total_len_stay
                    FROM (SELECT enc.current_dept_nr, enc.is_discharged, enc.discharge_date, enc.admission_dt, DATEDIFF(enc.discharge_date,enc.admission_dt) AS totallenstay
                            FROM care_encounter AS enc
                            WHERE enc.encounter_type IN (3,4)
                            AND enc.discharge_date IS NOT NULL 
                            AND (DATE(enc.discharge_date) BETWEEN '".$this->from."' AND '".$this->to."')
                            AND enc.status NOT IN ('deleted','hidden','inactive','void')
                            AND enc.current_dept_nr='".$current_dept_nr."') AS len";
                            
        #echo "<br><br>".$tot_sql;                    
     
        $tot_result=$db->Execute($tot_sql);    
        $over_alltotal  = $tot_result->FetchRow(); 
        
        return  $over_alltotal['total_len_stay'];
        
    }
    
    function FetchData(){
      global $db;
     
     $sql = "SELECT d.name_formal AS Type_Of_Service, d.nr,
                SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_non_phic,
                
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic,
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_indigent,
                SUM(CASE WHEN em.memcategory_id=3 AND w.accomodation_type=2 then 1 else 0 end) AS pay_owwa,
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL)  AND w.accomodation_type=2 then 1 else 0 end) AS pay_phic_member,

                SUM(CASE WHEN w.accomodation_type=2 then 1 else 0 end) AS total_pay,

                SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND(w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_non_phic,
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_phic,
                SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_phic_indigent,
                SUM(CASE WHEN em.memcategory_id=3 AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_owwa,
                SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id<>5 OR em.memcategory_id IS NULL)  AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS charity_phic_member,

                SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type IS NULL) then 1 else 0 end) AS total_charity,

                SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2 OR w.accomodation_type IS NULL) then 1 else 0 end) AS total_discharge

             FROM care_department AS d
             INNER JOIN care_encounter AS e ON e.current_dept_nr=d.nr
             LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr
             LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
             LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
             LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
             LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id

             WHERE  e.encounter_type IN (3,4)
             AND e.discharge_date IS NOT NULL 
             AND (DATE(e.discharge_date) BETWEEN '".$this->from."' AND '".$this->to."')
             AND e.status NOT IN ('deleted','hidden','inactive','void') 
             GROUP BY d.name_formal
             ORDER BY d.name_formal";

            #echo "sql = ".$sql;

            $result=$db->Execute($sql);
            if ($result) {
            
              $this->_count = $result->RecordCount();
                $this->Data=array();
              $i=1;
              
              while ($row=$result->FetchRow()) {
                
                $los = $this->getLOS($row['nr']);
                  
                $this->Data[]=array(
                    $row['Type_Of_Service'],
                    
                    $row['pay_non_phic'], 
                    $row['pay_phic'], 
                    $row['pay_phic_indigent'], 
                    $row['pay_owwa'], 
                    
                    $row['charity_non_phic'], 
                    $row['charity_phic'], 
                    $row['charity_phic_indigent'], 
                    $row['charity_owwa'], 
                    
                    $row['total_discharge'], 
                    $los
                    
                 );
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

$rep = new RepGen_Deaths($_GET['from'], $_GET['to']);
$rep->AliasNbPages();
#$rep->FetchData();
$rep->Report();
?>