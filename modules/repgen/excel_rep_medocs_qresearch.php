<?php
  #created by Cherry 01-11-11
  #OB Admission List
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_Medocs_Query4Research extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      
      var $from, $to;
      var $lrow = 2;
     
      function ExcelGen_Medocs_Query4Research($from, $to, $code)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          $this->worksheet->setPaper(1);      // Letter
          $this->worksheet->setLandscape();
          $this->worksheet->setMarginTop(1.9);
          $this->worksheet->setMarginLeft(0.5);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.5);
          $this->Headers = array(
             'PID', 'Patient Name', 'Admitted', 'Discharged', 'Age',
             'Brgy', 'Municipal', 'Code', 'Description'
            );
          $this->ColumnWidth = array(8, 25, 12, 12, 5, 12, 12, 8, 29);
          $this->Caption = "QUERY FOR RESEARCH";
          
          $this->format1=& $this->addFormat();
          $this->format1->setSize(9);
          $this->format1->setBold();
          $this->format1->setAlign('center');
          
          $this->format2=& $this->addFormat();
          $this->format2->setSize(8);
          $this->format2->setAlign('left');
          $this->format2->setTextWrap(1);
          
          $this->format3=& $this->addFormat();
          $this->format3->setSize(9);
          $this->format3->setAlign('center');
          $this->format3->setTextWrap(1);
          
          if ($from) $this->from=$from;
          if ($to) $this->to=$to;
          if ($code) $this->code=$code;
      }
      
      function ExcelHeader()
      {
          $dept_obj = new Department();
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
         
          if ($this->from)
            $text = "From ".date("F j, Y",strtotime($this->from))." to ".date("F j, Y",strtotime($this->to));
          else
            $text = "Full History";
                      
          $this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\n".$this->Caption."\n".$text."\n",0.5);

           $rownum1 = 0;
           $rownum2 = 1;
           $this->len_header = count($this->Headers);   
           for($cnt = 0; $cnt < $this->len_header; $cnt++){
            $this->worksheet->setColumn($rownum1, $cnt, $this->ColumnWidth[$cnt]); 
            $this->worksheet->write($rownum2, $cnt, $this->Headers[$cnt], $this->format1); 
           }         
          
      }
               
      function FetchData()
      {
       global $db;
        
       if ($this->from) {
          $where[]="DATE(e.discharge_date) BETWEEN '$this->from' AND '$this->to'";
       }
        
       if ($this->code) {
          $where[] = "d.code='$this->code'";
       }

       if ($where)
          $whereSQL = "AND (".implode(") AND (",$where).")";
       
       $sql = "SELECT 
        p.pid,CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name,p.sex,
        FLOOR(fn_calculate_age(p.date_birth,NOW())) AS age,
        brgy.brgy_name,mun.mun_name,
        e.admission_dt AS admission_date, e.discharge_date,
        d.code, icd.description
        FROM care_encounter_diagnosis AS d
        LEFT JOIN care_encounter AS e ON e.encounter_nr=d.encounter_nr
        LEFT JOIN care_person AS p ON p.pid=e.pid
        LEFT JOIN seg_barangays AS brgy ON brgy.brgy_nr=p.brgy_nr
        LEFT JOIN seg_municity AS mun ON mun.mun_nr=brgy.mun_nr
        LEFT JOIN care_icd10_en AS icd ON d.code=diagnosis_code
        WHERE (e.encounter_type=3 OR e.encounter_type=4) AND e.discharge_date IS NOT NULL $whereSQL\n";
        
        $sql .= "ORDER BY DATE(discharge_date),patient_name";             
        $result=$db->Execute($sql);
        if ($result)
        {      
          $this->count = $result->RecordCount(); 
          $i = 1;
          $newrow=2;
          while ($row=$result->FetchRow())
          {       
             $col=0;
             $this->worksheet->write($newrow, $col, $row['pid'], $this->format2);
             $this->worksheet->write($newrow, $col+1, mb_strtoupper($row['patient_name']), $this->format2);
             $this->worksheet->write($newrow, $col+2, date("m/d/Y h:ia",strtotime($row['admission_date'])), $this->format3);
             $this->worksheet->write($newrow, $col+3, date("m/d/Y h:ia",strtotime($row['discharge_date'])), $this->format3);
             $this->worksheet->write($newrow, $col+4, $row['age'], $this->format3);
             $this->worksheet->write($newrow, $col+5, $row['brgy_name'], $this->format2);
             $this->worksheet->write($newrow, $col+6, $row['mun_name'], $this->format2);
             $this->worksheet->write($newrow, $col+7, $row['code'], $this->format3); 
             $this->worksheet->write($newrow, $col+8, $row['description'], $this->format2); 
             $newrow++; 
            $i++;
          }     
        }
        $this->lrow = $newrow;
      }
      
      function AfterData()
      {
          if (!$this->count) 
          {
            $this->worksheet->write($this->lrow, 0, "No records found for this report...");
          }
      } 
  }
  
  $rep = new ExcelGen_Medocs_Query4Research($_GET['from'], $_GET['to'], $_GET['code']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_query_for_research.xls');
  $rep->close();
?>