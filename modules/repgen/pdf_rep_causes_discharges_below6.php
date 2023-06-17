<?php
#created by Cherry 11-23-09
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_CausesOfDischarges extends RepGen{
var $colored = TRUE;
var $from, $to; 
var $dept_nr;    
var $t_male_below1, $t_female_below1, $t_male_1to4, $t_female_1to4;
var $t_male_5to9, $t_female_5to9, $t_male_10to14, $t_female_10to14;
var $t_male_15to19, $t_female_15to19,$t_male_20to44,$t_female_20to44; 
var $t_male_45to59, $t_female_45to59, $t_male_60up, $t_female_60up; 
var $t_male_total, $t_female_total, $t_total;                   
                           
   function RepGen_CausesOfDischarges ($from, $to, $dept_nr) {
        global $db;
        $this->RepGen("MEDICAL RECORDS: CAUSES OF DISCHARGES");
        
        #$this->ColumnWidth = array(10,45, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10,10, 15,15, 15);
        $this->ColumnWidth = array(8,67, 12, 12, 12, 12, 12, 12, 12, 12, 12, 25);
        $this->RowHeight = 5;
        $this->TextHeight = 5;
        $this->TextPadding = 0.2;
        #$this->Alignment = array('L','L', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C','C', 'C','C', 'C');
        $this->Alignment = array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $this->PageOrientation = "P";
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
        
        $this->Cell(0,4,'TWENTY LEADING CAUSES OF MORBIDITY ('.$deptname.')',$border2,1,'C');
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
        $row=6;
        $age_width = $this->ColumnWidth[2] + $this->ColumnWidth[3] + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7];
        /*
        $this->Cell(10, 4, "", "TLR", 0, 'C'); 
        $this->Cell(45, 4, "", "TLR", 0, 'C');
        $this->Cell(160, 4, "Age Distribution of Patients", "1", 0, 'C');
        $this->Cell(30, 4, "", "TLR", 0, 'C');
         $this->Cell(15, 4, "", "TLR", 0, 'C');  
        $this->Cell(15, 4, "", "TLR", 1, 'C');  
        
        $this->Cell(10, 4, "#", "LR", 0, 'C'); 
        $this->Cell(45, 4, "Discharge of Diagnosis", "LR", 0, 'C');
        
        $this->Cell(20, 4, "<1", "1", 0, 'C');
        $this->Cell(20, 4, "1-4", "1", 0, 'C');
        $this->Cell(20, 4, "5-9", "1", 0, 'C');
        $this->Cell(20, 4, "10-14", "1", 0, 'C');
        $this->Cell(20, 4, "15-19", "1", 0, 'C');
        $this->Cell(20, 4, "20-44", "1", 0, 'C');
        $this->Cell(20, 4, "45-59", "1", 0, 'C');
        $this->Cell(20, 4, "60 up", "1", 0, 'C');
        
        $this->Cell(30, 4, "Total", "LR", 0, 'C');
        
        $this->Cell(15, 4, "% of", "LR", 0, 'C');
        
        $this->Cell(15, 4, "ICD10", "LR", 1, 'C');
       
        $this->Cell(10, 4, "", "LRB", 0, 'C');  
        $this->Cell(45, 4, "(Primary)", "LRB", 0, 'C');  
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        
        $this->Cell(10, 4, "M", "1", 0, 'C');
        $this->Cell(10, 4, "F", "1", 0, 'C');
        $this->Cell(10, 4, "T", "1", 0, 'C');
        
        $this->Cell(15, 4, "Total", "LRB", 0, 'C');
        
        $this->Cell(15, 4, "Tab No.", "LRB", 0, 'C');
        */
        
        $this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1],4, "", "TLR",0);
        $this->Cell($age_width,4,"Age Distribution of Patients", 1, 0, 'C');
        $this->Cell($this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10],4,"","TLR",0);
        $this->Cell($this->ColumnWidth[11],4,"ICD 10","TLR",1,'C');
        
        $this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1],4,"","LR",0);
        $this->Cell($this->ColumnWidth[2] + $this->ColumnWidth[3],4,"","LR",0);
        $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5],4,"","LR",0);
        $this->Cell($this->ColumnWidth[6] + $this->ColumnWidth[7],4,"5- < 6 yrs", "LR",0,'C');
        $this->Cell($this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10],4,"TOTAL","LR",0,'C');
        $this->Cell($this->ColumnWidth[11],4,"CODE/","LR",1,'C');
        
        $this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1] ,4,"Discharge Diagnosis (Primary) No Abbreviation","LR",0,'C');
        $this->Cell($this->ColumnWidth[2] + $this->ColumnWidth[3],4, "Under 1", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5],4, "1-4 yrs old", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[6] + $this->ColumnWidth[7],4, "old", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[8]+$this->ColumnWidth[9]+$this->ColumnWidth[10],4,"","LRB", 0);
        $this->Cell($this->ColumnWidth[11],4,"TABULATI-", "LR",1,'C');
        
        $this->Cell($this->ColumnWidth[0],4,"","LB",0,'C');
        $this->Cell($this->ColumnWidth[1],4,"","RB",0);
        $this->Cell($this->ColumnWidth[2],4,"M","LRB",0,'C');
        $this->Cell($this->ColumnWidth[3],4,"F","LRB",0,'C');
        $this->Cell($this->ColumnWidth[4],4,"M","LRB",0,'C');
        $this->Cell($this->ColumnWidth[5],4,"F","LRB",0,'C');
        $this->Cell($this->ColumnWidth[6],4,"M","LRB",0,'C');
        $this->Cell($this->ColumnWidth[7],4,"F","LRB",0,'C');
        $this->Cell($this->ColumnWidth[8],4,"M","LRB",0,'C');
        $this->Cell($this->ColumnWidth[9],4,"F","LRB",0,'C');
        $this->Cell($this->ColumnWidth[10],4,"Total","LRB",0,'C');
        $this->Cell($this->ColumnWidth[11],4,"ON LIST","LRB",0,'C');
        
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

    function FetchData(){
      global $db;
    
     $tot_sql = "SELECT SUM(t.total) as total FROM (SELECT count(ed.code) AS total

                    FROM  care_encounter_diagnosis AS ed
                    INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
                    INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
                    INNER JOIN care_person AS p ON p.pid=e.pid
                    WHERE ed.encounter_type IN (3,4)
                    AND e.status NOT IN ('deleted','hidden','inactive','void')
                    AND ed.status NOT IN ('deleted','hidden','inactive','void')
                    AND DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'    
                    GROUP BY ed.code
                    ORDER BY count(ed.code) DESC LIMIT 20) as t";
     
     $tot_result=$db->Execute($tot_sql);    
     $over_alltotal  = $tot_result->FetchRow(); 
         
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

     $sql = "SELECT ed.code, c.description,

            SUM(CASE WHEN p.sex='m' AND (floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<1 OR floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age)) IS NULL) then 1 else 0 end) AS male_below1,
            SUM(CASE WHEN p.sex='f' AND (floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<1 OR floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age)) IS NULL) then 1 else 0 end) AS female_below1,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=1 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=4 then 1 else 0 end) AS male_1to4,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=1 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=4 then 1 else 0 end) AS female_1to4,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=5 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<6 then 1 else 0 end) AS male_5,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=5 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<6 then 1 else 0 end) AS female_5,

            SUM(CASE WHEN p.sex='m' AND (floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<6 OR floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age)) IS NULL)  then 1 else 0 end) AS male_total,
            SUM(CASE WHEN p.sex='f' AND (floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<6 OR floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age)) IS NULL) then 1 else 0 end) AS female_total,

            SUM(CASE WHEN (p.sex='m' OR p.sex='f') AND (floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<6 OR floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age)) IS NULL) then 1 else 0 end) AS total
            
            FROM  care_encounter_diagnosis AS ed
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.code
            INNER JOIN care_person AS p ON p.pid=e.pid
            WHERE ed.encounter_type IN (3,4)
            AND e.status NOT IN ('deleted','hidden','inactive','void')
            AND ed.status NOT IN ('deleted','hidden','inactive','void')
            AND DATE(e.admission_dt) BETWEEN '".$this->from."' AND '".$this->to."'
            $sql_dept   
            GROUP BY ed.code
            ORDER BY count(ed.code) DESC LIMIT 20";
     
     
            $result=$db->Execute($sql);
            if ($result) {
            
              $this->_count = $result->RecordCount();
                $this->Data=array();
              $i=1;
              $percentage = 0; 
              while ($row=$result->FetchRow()) {
                 
                $this->Data[]=array(
                  $i.". ",
                  $row['description'],
                  $row['male_below1'],
                  $row['female_below1'],
                  $row['male_1to4'],
                  $row['female_1to4'],
                  $row['male_5'],
                  $row['female_5'],
                  $row['male_total'],
                  $row['female_total'],
                  $row['total'],
                  $row['code']
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

$rep = new RepGen_CausesOfDischarges($_GET['from'], $_GET['to'], $_GET['dept_nr_sub']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>