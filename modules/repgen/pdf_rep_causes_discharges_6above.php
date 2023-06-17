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
        
        $this->ColumnWidth = array(8, 55, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 15, 15, 15, 15);
        $this->RowHeight = 5;
        $this->TextHeight = 5;
        $this->TextPadding = 0.2;
        $this->Alignment = array('L', 'L','C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $this->PageOrientation = "L";
        $this->NoWrap = FALSE;
        $this->LEFTMARGIN = 8;
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
        $this->Cell(0, 4, 'AMONG 6 YEARS OLD AND ABOVE',$border2,1,'C');
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
        $age_width = $this->ColumnWidth[2] + $this->ColumnWidth[3] + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8];
        $age_width += $this->ColumnWidth[9] + $this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15];
        
        $this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1], $row,"","TLR",0);
        $this->Cell($age_width, $row, "Age Distribution of Patients", 1, 0, 'C');
        $this->Cell($this->ColumnWidth[16] + $this->ColumnWidth[17] + $this->ColumnWidth[18], $row, "","TLR",0);
        $this->Cell($this->ColumnWidth[19], $row, "ICD 10", "TLR", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1], $row, "Discharge Diagnosis", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[2] + $this->ColumnWidth[3], $row, "6-9", "TLR", 0, 'C');
        $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5], $row, "10-14", "TLR", 0, 'C');
        $this->Cell($this->ColumnWidth[6] + $this->ColumnWidth[7], $row, "15-19", "TLR", 0, 'C');
        $this->Cell($this->ColumnWidth[8] + $this->ColumnWidth[9], $row, "20-44", "TLR", 0, 'C');
        $this->Cell($this->ColumnWidth[10] + $this->ColumnWidth[11], $row, "45-59", "TLR", 0, 'C');
        $this->Cell($this->ColumnWidth[12] + $this->ColumnWidth[13], $row, "60-64", "TLR", 0, 'C');
        $this->Cell($this->ColumnWidth[14] + $this->ColumnWidth[15], $row, "", "TLR", 0);
        $this->Cell($this->ColumnWidth[16] + $this->ColumnWidth[17] + $this->ColumnWidth[18], $row, "TOTAL", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[19], $row, "CODE/", "LR", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1], $row, "(Primary) No", "LR", 0, 'C');
        $this->Cell($this->ColumnWidth[2] + $this->ColumnWidth[3], $row, "y.o", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5], $row, "y.o", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[6] + $this->ColumnWidth[7], $row, "y.o", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[8] + $this->ColumnWidth[9], $row, "y.o", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[10] + $this->ColumnWidth[11], $row, "y.o", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[12] + $this->ColumnWidth[13], $row, "y.o", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[14] + $this->ColumnWidth[15], $row, "=>65yo", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[16] + $this->ColumnWidth[17] + $this->ColumnWidth[18], $row, "", "LRB",0);
        $this->Cell($this->ColumnWidth[19], $row, "TABU-", "LR", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1], $row, "Abbreviation", "LR", 0, 'C');
        for($cnt = 2; $cnt<18; $cnt++){
            $this->Cell($this->ColumnWidth[$cnt], $row, "", "LR", 0);
        }
        $this->Cell($this->ColumnWidth[18], $row, "", "LR", 0);
        $this->Cell($this->ColumnWidth[19], $row, "LATION", "LR", 1, 'C');
        
        $this->Cell($this->ColumnWidth[0] + $this->ColumnWidth[1], $row, "", "LRB", 0);
        $this->Cell($this->ColumnWidth[2], $row, "M", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[3], $row, "F", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[4], $row, "M", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[5], $row, "F", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[6], $row, "M", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[7], $row, "F", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[8], $row, "M", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[9], $row, "F", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[10], $row, "M", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[11], $row, "F", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[12], $row, "M", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[13], $row, "F", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[14], $row, "M", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[15], $row, "F", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[16], $row, "M", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[17], $row, "F", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[18], $row, "Total", "LRB", 0, 'C');
        $this->Cell($this->ColumnWidth[19], $row, "LIST", "LRB", 0, 'C');
        
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
            $this->Cell(264, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
        }
        
        $cols = array();
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

     $sql = "SELECT ed.code, c.description,
    
            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=6 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=9 then 1 else 0 end) AS male_6to9,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=6 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=9 then 1 else 0 end) AS female_6to9,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=10 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=14 then 1 else 0 end) AS male_10to14,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=10 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=14 then 1 else 0 end) AS female_10to14,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=15 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=19 then 1 else 0 end) AS male_15to19,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=15 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=19 then 1 else 0 end) AS female_15to19,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=20 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=44 then 1 else 0 end) AS male_20to44,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=20 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=44 then 1 else 0 end) AS female_20to44,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=45 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=59 then 1 else 0 end) AS male_45to59,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=45 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=59 then 1 else 0 end) AS female_45to59,

            SUM(CASE WHEN p.sex='m' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=60 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=64 then 1 else 0 end) AS male_60to64,
            SUM(CASE WHEN p.sex='f' 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=60 
                AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))<=64 then 1 else 0 end) AS female_60to64,

            SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=65 then 1 else 0 end) AS male_65up,
            SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=65 then 1 else 0 end) AS female_65up,

            SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=6 then 1 else 0 end) AS male_total,
            SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=6 then 1 else 0 end) AS female_total,

        SUM(CASE WHEN (p.sex='m' OR p.sex='f') AND floor(IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age))>=6 then 1 else 0 end) AS total

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
                    $row['male_6to9'],
                    $row['female_6to9'],
                    $row['male_10to14'],
                    $row['female_10to14'],
                    $row['male_15to19'],
                    $row['female_15to19'],
                    $row['male_20to44'],
                    $row['female_20to44'],
                    $row['male_45to59'],
                    $row['female_45to59'],
                    $row['male_60to64'],
                    $row['female_60to64'],
                    $row['male_65up'],
                    $row['female_65up'],
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