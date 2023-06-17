<?php
  #created by Cherry 01-11-11
  #OB Admission List
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_OBAdmissionList extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      
      var $from, $to;
      var $lrow = 2;
     
      function ExcelGen_OBAdmissionList($from, $to)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          $this->worksheet->setPaper(1);      // Letter
          $this->worksheet->setPortrait();
          $this->worksheet->setMarginTop(1.9);
          $this->worksheet->setMarginLeft(0.5);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.5);
          $this->Headers = array(
             '', 'Patient Name', 'Admission Date', '', 'Department', 'HRN', 'CASE'
            );
          $this->ColumnWidth = array(5, 24, 15, 10, 15, 12, 11);
          $this->Caption = "OB & BIRTHING HOME ADMISSION LIST";
          
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
          
          if ($from) $this->from=date("Y-m-d",strtotime($from));
          if ($to) $this->to=date("Y-m-d",strtotime($to)); 
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
         
          if ($this->from==$this->to)
            $text = "For ".date("F j, Y",strtotime($this->from));
          else
            $text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
                      
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
        $where[]="DATE(e.admission_dt) BETWEEN '$this->from' AND '$this->to'";
       }

       if ($where)
        $whereSQL = "AND (".implode(") AND (",$where).")"; 
          
       $sql = "SELECT
        CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name, 
        d.name_formal AS department_name, (CASE current_room_nr WHEN 0 then area ELSE current_room_nr END) AS Area_P,
        e.pid AS HOSP_Num, e.encounter_nr AS CASE_Num, e.discharge_date, e.received_date,
        ins.hcare_id, IF(ins.hcare_id=18,'P','NP') AS insurance, e.admission_dt
        FROM care_encounter AS e
        LEFT JOIN care_person AS p ON p.pid=e.pid
        LEFT JOIN care_department AS d ON d.nr=e.current_dept_nr
        LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
        WHERE (e.encounter_type = 3 OR e.encounter_type = 4) 
        AND e.current_dept_nr IN (124,139,155,140)
        AND e.admission_dt IS NOT NULL $whereSQL\n  
        GROUP BY patient_name ORDER BY patient_name";
                    
        $result=$db->Execute($sql);
        if ($result)
        {      
          $this->count = $result->RecordCount(); 
          $i = 1;
          $newrow=2;
          while ($row=$result->FetchRow())
          {       
            $discharged_date = "";
            $received_date = "";
            $admission_date = "";
            
            if (!(empty($row['discharge_date'])))
              $discharged_date = date("m/d/Y",strtotime($row['discharge_date']));  
            
            if (!(empty($row['admission_dt'])))
              $admission_date = date("m/d/Y",strtotime($row['admission_dt']));  
 
             $col=0;
             $this->worksheet->write($newrow, $col, $i, $this->format2);
             $this->worksheet->write($newrow, $col+1, mb_strtoupper($row['patient_name']), $this->format2);
             $this->worksheet->write($newrow, $col+2, $admission_date, $this->format3);
             $this->worksheet->write($newrow, $col+3, $row['insurance'], $this->format3);
             $this->worksheet->write($newrow, $col+4, $row['department_name'], $this->format3);
             $this->worksheet->write($newrow, $col+5, $row['HOSP_Num'], $this->format3);
             $this->worksheet->write($newrow, $col+6, $row['CASE_Num'], $this->format3);
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
  
  $rep = new ExcelGen_OBAdmissionList($_GET['from'], $_GET['to']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_ob_admission_list.xls');
  $rep->close();
?>