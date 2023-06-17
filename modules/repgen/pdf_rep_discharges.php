<?php
#created by Cherry 11-26-09
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_CausesOfMortality extends RepGen{
var $colored = TRUE;
var $from, $to; 
var $dept_nr;    
var $t_male_below1, $t_female_below1, $t_male_1to4, $t_female_1to4;
var $t_male_5to9, $t_female_5to9, $t_male_10to14, $t_female_10to14;
var $t_male_15to19, $t_female_15to19,$t_male_20to44,$t_female_20to44; 
var $t_male_45to59, $t_female_45to59, $t_male_60up, $t_female_60up; 
var $t_male_total, $t_female_total, $t_total;                   
                           
   function RepGen_CausesOfMortality ($from, $to, $dept_nr) {
        global $db;
        $this->RepGen("MEDICAL RECORDS: CAUSES OF MORTALITY");
        
        #$this->ColumnWidth = array(7, 44, 12,12,12, 12, 12, 12, 12, 12, 12, 12, 12, 25);
        $this->ColumnWidth = array(25, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 15);
        $this->RowHeight = 5;
        $this->TextHeight = 5;
        $this->TextPadding = 0.2;
        #$this->Alignment = array('C','L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $this->Alignment = array('L','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');
        $this->PageOrientation = "L";
        $this->NoWrap = FALSE;
        $this->LEFTMARGIN = 5;
        $this->dept_nr = $dept_nr;
        
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
        $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,6,17);
        $this->SetFont("Arial","I","9");
        #$total_w = 165;
        $total_w = 0;
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
          $this->SetFont('Arial','B',11);
        #$this->Cell(50,5);
        
        if ($this->dept_nr){
            if ($this->dept_nr==1)
                    $deptname = "Gynecology";
            elseif ($this->dept_nr==2)
                    $deptname = "Medicines";        
            elseif ($this->dept_nr==3)
                    $deptname = "Obstetrics";                
            elseif ($this->dept_nr==4)
                    $deptname = "Pediatrics";                
            elseif ($this->dept_nr==5)
                    $deptname = "Surgery";                
        }else
            $deptname = "All Department";
        
        $deptname = mb_strtoupper($deptname);    
        
        $this->Cell(0,4,'TWENTY LEADING CAUSES OF MORTALITY ('.$deptname.')',$border2,1,'C');
        $this->Cell(0, 4, 'AMONG CHILDREN 0 - < 6 YEARS OLD', $border2,1,'C');
        #$this->Cell(0,4,'');
         $this->SetFont('Arial','B',9);
        #$this->Cell(50,5);
        
        if ($this->from==$this->to)
            $text = "For ".date("F j, Y",strtotime($this->from));
        else
              #$text = "Full History";
            $text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
            
          $this->Cell($total_w,4,$text,$border2,1,'C');
        $this->Ln(5);

        # Print table header        
        $this->SetFont('Arial','B',8);
        #if ($this->colored) $this->SetFillColor(0xED);
        if ($this->colored) $this->SetFillColor(255);
        $this->SetTextColor(0);
        $row=4;
        $type_acco = $this->ColumnWidth[3] + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7];
        $type_acco += $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10] + $this->ColumnWidth[11];
        $cond_disch = $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15] + $this->ColumnWidth[16];
        $cond_disch += $this->ColumnWidth[17] + $this->ColumnWidth[18] + $this->ColumnWidth[19] + $this->ColumnWidth[20];
        
        $this->Cell($this->ColumnWidth[0], $row, "", "TLR", 0);
        $this->Cell($this->ColumnWidth[1], $row, "", "TLR", 0);
        $this->Cell($this->ColumnWidth[2], $row, "", "TLR", 0);
        $this->Cell($type_acco, $row, "TYPE OF ACCOMODATION", 1, 0, 'C');
        $this->Cell($cond_disch, $row, "CONDITION ON DISCHARGE", 1, 1, 'C');
        
        $this->Cell($this->ColumnWidth[0], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[1], $row, "", "LR",0);
        $this->Cell($this->ColumnWidth[2], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[3]+$this->ColumnWidth[4]+$this->ColumnWidth[5], $row, "", "TLR", 0);
        $this->Cell($this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9], $row, "", "TLR", 0); 
        for($num=10; $num<=16; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LR", 0);
        }
        $this->Cell($this->ColumnWidth[17]+$this->ColumnWidth[18]+$this->ColumnWidth[19], $row, "", "TLR", 0);
        $this->Cell($this->ColumnWidth[20], $row, "TOTAL", "TLR", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[1], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[2], $row, "Total", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[3]+$this->ColumnWidth[4]+$this->ColumnWidth[5], $row, "Non-PhilHealth", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9], $row, "PhilHealth", "LR", 0, 'C');
        for($num=10; $num<=16; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LR", 0);
        }
        $this->Cell($this->ColumnWidth[17]+$this->ColumnWidth[18]+$this->ColumnWidth[19], $row, "DEATHS", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[20], $row, "DISCH-", "LR", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[1], $row, "No.", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[2], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[3]+$this->ColumnWidth[4]+$this->ColumnWidth[5], $row, "", "LRB", 0);
        $this->Cell($this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9], $row, "", "LRB", 0);
        for($num=10; $num<=16; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LR", 0);
        }
        $this->Cell($this->ColumnWidth[17]+$this->ColumnWidth[18]+$this->ColumnWidth[19], $row, "", "LRB", 0);
        $this->Cell($this->ColumnWidth[20], $row, "ARGE", "LRB", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0], $row, "Type of", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[1], $row, "of", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[2], $row, "Length", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[3], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[4], $row, "Service", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[5], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[6], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[7]+$this->ColumnWidth[8], $row, "Service", 1, 0, 'C');
        
        for($num=9; $num<=20; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LR", 0);  
        }
        $this->Ln();
        
        $this->Cell($this->ColumnWidth[0], $row, "Service", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[1], $row, "Patients", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[2], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[3], $row, "Pay", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[4], $row, "/", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[5], $row, "Total", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[6], $row, "Pay", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[7], $row, "Member", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[8], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[9], $row, "Total", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[10], $row, "HMO", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[11], $row, "OWWA", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[12], $row, "R/I", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[13], $row, "T", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[14], $row, "H", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[15], $row, "A", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[16], $row, "U", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[17], $row, "Died", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[18], $row, "Died", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[19], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[20], $row, "", "LR", 1);
        
        $this->Cell($this->ColumnWidth[0], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[1], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[2], $row, "Of", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[3], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[4], $row, "Charity", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[5], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[6], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[7], $row, "/", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[8], $row, "Indigent", "LR", 0, 'C');
        for($num=9; $num<=16; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LR", 0);
        }
        $this->Cell($this->ColumnWidth[17], $row, "< 48", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[18], $row, "> 48", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[19], $row, "Total", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[20], $row, "", "LR", 1);
        
        $this->Cell($this->ColumnWidth[0], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[1], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[2], $row, "Stay", "LR", 0, 'C');
        for($num=3; $num<=6; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LR", 0);
        }
        $this->Cell($this->ColumnWidth[7], $row, "Depen-", "LR", 0, 'C');
        for($num = 8; $num<=16; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LR", 0);
        }
        $this->Cell($this->ColumnWidth[17], $row, "hours", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[18], $row, "hours", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[19], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[20], $row, "", "LR", 1);
        
        
        for($num=0; $num<=6; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LRB", 0);
        }
        $this->Cell($this->ColumnWidth[8], $row, "dent", "LR", 0, 'C');
        for($num=8; $num<=20; $num++){
            $this->Cell($this->ColumnWidth[$num], $row, "", "LRB", 0);
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
            $this->Cell(200, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
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
                            AND (DATE(enc.discharge_date) BETWEEN '2008-12-01' AND '2008-12-01')
                            AND enc.status NOT IN ('deleted','hidden','inactive','void')
                            AND enc.current_dept_nr='".$current_dept_nr."') AS len";
                            
        #echo "<br><br>".$tot_sql;                    
     
        $tot_result=$db->Execute($tot_sql);    
        $over_alltotal  = $tot_result->FetchRow(); 
        
        return  $over_alltotal['total_len_stay'];
        
    } 

    function FetchData(){
      global $db;
         
     $sql_dept="";
   if ($this->dept_nr){
            if ($this->dept_nr==0)
                    #all department
                    $sql_dept = " ";
            elseif ($this->dept_nr==1)
                    #Gynecology
                    $sql_dept = " AND (e.current_dept_nr='124' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='124'))  ";         
            elseif ($this->dept_nr==2)
                    #Medicines
                    $sql_dept = " AND (e.current_dept_nr IN (133,154,104) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='133')OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='154') OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='104')) ";             
            elseif ($this->dept_nr==3)
                    #Obstetrics
                    $sql_dept = " AND (e.current_dept_nr='139' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='139')) ";             
            elseif ($this->dept_nr==4)
                    #Pediatrics
                    $sql_dept = " AND (e.current_dept_nr IN (125) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='125')) ";             
            elseif ($this->dept_nr==5)
                    #Surgery
                    $sql_dept = " AND (e.current_dept_nr IN (117,141,136,131,122) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='117')) ";                             
        }             

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

                SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2 OR w.accomodation_type IS NULL) then 1 else 0 end) AS total_discharge,
    
        SUM(CASE WHEN sr.result_code=5 AND sd.disp_code = 7 then 1 else 0 end) AS rec_disch, 
                SUM(CASE WHEN sr.result_code=5 AND sd.disp_code = 8 then 1 else 0 end) AS rec_trans, 
                SUM(CASE WHEN sr.result_code=5 AND sd.disp_code = 9 then 1 else 0 end) AS rec_hama, 
                SUM(CASE WHEN sr.result_code=5 AND sd.disp_code = 10 then 1 else 0 end) AS rec_absc, 
                SUM(CASE WHEN (sr.result_code=5 AND(sd.disp_code = 7 OR sd.disp_code = 8 OR sd.disp_code = 9 OR sd.disp_code = 10)) then 1 else 0 end) AS total_rec, 

                SUM(CASE WHEN sr.result_code=6 AND sd.disp_code = 7 then 1 else 0 end) AS imp_disch, 
                SUM(CASE WHEN sr.result_code=6 AND sd.disp_code = 8 then 1 else 0 end) AS imp_trans, 
                SUM(CASE WHEN sr.result_code=6 AND sd.disp_code = 9 then 1 else 0 end) AS imp_hama, 
                SUM(CASE WHEN sr.result_code=6 AND sd.disp_code = 10 then 1 else 0 end) AS imp_absc, 
                SUM(CASE WHEN (sr.result_code=6 AND(sd.disp_code = 7 OR sd.disp_code = 8 OR sd.disp_code = 9 OR sd.disp_code = 10)) then 1 else 0 end) AS total_imp, 

                SUM(CASE WHEN sr.result_code=7 AND sd.disp_code = 7 then 1 else 0 end) AS unimp_disch, 
                SUM(CASE WHEN sr.result_code=7 AND sd.disp_code = 8 then 1 else 0 end) AS unimp_trans, 
                SUM(CASE WHEN sr.result_code=7 AND sd.disp_code = 9 then 1 else 0 end) AS unimp_hama, 
                SUM(CASE WHEN sr.result_code=7 AND sd.disp_code = 10 then 1 else 0 end) AS unimp_absc, 
                SUM(CASE WHEN (sr.result_code=7 AND(sd.disp_code = 7 OR sd.disp_code = 8 OR sd.disp_code = 9 OR sd.disp_code = 10)) then 1 else 0 end) AS total_unimp, 

                SUM(CASE WHEN (sr.result_code=8 AND floor(IF(fn_calculate_age(cp.death_date,cp.date_birth),fn_get_age(cp.death_date,cp.date_birth),cp.age))<48) then 1 else 0 end) AS deathbelow48, 
                SUM(CASE WHEN (sr.result_code=8 AND floor(IF(fn_calculate_age(cp.death_date,cp.date_birth),fn_get_age(cp.death_date,cp.date_birth),cp.age))>=48) then 1 else 0 end) AS deathabove48, 
                SUM(CASE WHEN (sr.result_code=8 
                    AND ((floor(IF(fn_calculate_age(cp.death_date,cp.date_birth),fn_get_age(cp.death_date,cp.date_birth),cp.age))<48) 
                    OR (floor(IF(fn_calculate_age(cp.death_date,cp.date_birth),fn_get_age(cp.death_date,cp.date_birth),cp.age))>=48))) then 1 else 0 end) 
                    AS total_death 

             FROM care_department AS d
             INNER JOIN care_encounter AS e ON e.current_dept_nr=d.nr
             LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr
             LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
             LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
             LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
             LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
         LEFT JOIN (SELECT ser.encounter_nr,SUBSTRING(MAX(CONCAT(ser.create_time,ser.result_code)),20) AS result_code
                            FROM seg_encounter_result AS ser 
                            INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr 
                            WHERE em.encounter_type IN (3,4) 
                            AND DATE(discharge_date) BETWEEN '2008-12-01' AND '2008-12-01'       
                            AND em.discharge_date IS NOT NULL
                            GROUP BY ser.encounter_nr
                            ORDER BY ser.encounter_nr, ser.create_time DESC) AS sr ON sr.encounter_nr = e.encounter_nr 
          LEFT JOIN (SELECT sed.encounter_nr,SUBSTRING(MAX(CONCAT(sed.create_time,sed.disp_code)),20) AS disp_code
                        FROM seg_encounter_disposition AS sed 
                        INNER JOIN care_encounter AS em ON em.encounter_nr=sed.encounter_nr 
                        WHERE em.encounter_type IN (3,4) 
                        AND DATE(discharge_date) BETWEEN '2008-12-01' AND '2008-12-01' 
                        AND em.discharge_date IS NOT NULL
                        GROUP BY sed.encounter_nr
                        ORDER BY sed.encounter_nr, sed.create_time DESC) AS sd ON sd.encounter_nr = e.encounter_nr
           INNER JOIN care_person AS cp ON cp.pid = e.pid 

             WHERE  e.encounter_type IN (3,4)
             AND e.discharge_date IS NOT NULL 
             AND (DATE(e.discharge_date) BETWEEN '2008-12-01' AND '2008-12-01')
             AND e.status NOT IN ('deleted','hidden','inactive','void') 
             GROUP BY d.name_formal
             ORDER BY d.name_formal";
     
     
            $result=$db->Execute($sql);
            if ($result) {
            
              $this->_count = $result->RecordCount();
                $this->Data=array();
              $percentage = 0; 
              while ($row=$result->FetchRow()) {
                 
                 $los = $this->getLOS($row['nr']);      
                  
                $this->Data[]=array(
                  $row['Type_Of_Service'],
                  $row['total_pay']+$row['total_charity'],
                  $los,
                  $row['pay_non_phic'],
                  $row['charity_non_phic'],
                  $row['pay_non_phic'] + $row['charity_non_phic'],
                  $row['pay_phic'] + $row['pay_phic_indigent'],
                  $row['charity_phic'],
                  $row['charity_phic_indigent'],
                  $row['pay_phic'] + $row['pay_phic_indigent'] + $row['charity_phic'] + $row['charity_phic_indigent'],
                  '0',
                  $row['pay_owwa'] + $row['charity_owwa'],
                  $row['total_rec'] + $row['total_imp'],
                  $row['rec_trans'] + $row['imp_trans'] + $row['unimp_trans'],
                  $row['rec_hama'] + $row['imp_hama'] + $row['unimp_hama'],
                  $row['rec_absc'] + $row['imp_absc'] + $row['unimp_absc'], 
                  $row['total_unimp'],
                  $row['deathbelow48'],
                  $row['deathabove48'],
                  $row['total_death'],
                  $row['total_discharge']
                 );
                  $i++;
                  //$percentage = 0;
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

$rep = new RepGen_CausesOfMortality($_GET['from'], $_GET['to'], $_GET['dept_nr_sub']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>