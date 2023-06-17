<?php
  #created by Cherry 01-11-11
  #Admission List
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_AdmissionList extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      
      var $from, $to;
      var $dept_nr;
      var $lrow = 2;
     
      function ExcelGen_AdmissionList($from, $to, $dept_nr)
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
             '', 'Patient Name', 'Received', 'Discharged', '', 'Department', 'HRN', 'CASE', 'Admission Date', 'ER Date'
            );
          $this->ColumnWidth = array(5, 22, 10, 10, 10, 12, 10, 12, 20, 20);
          $this->Caption = "ADMISSION LIST";
          
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
          $this->dept_nr = $dept_nr;  
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
          $where[]="DATE(e.create_time) BETWEEN '$this->from' AND '$this->to'";
       }
       $sql_dept="";     
       if ($this->dept_nr){  
          $sql_dept = " AND (e.current_dept_nr='".$this->dept_nr."' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='".$this->dept_nr."'))";
       }    
        
       if ($where)
          $whereSQL = "AND (".implode(") AND (",$where).")"; 
        
       $sql = "SELECT
        CONCAT(IFNULL(p.name_last,''),IFNULL(CONCAT(', ', p.name_first),''),IFNULL(CONCAT(' ', p.name_middle),'')) AS patient_name, w.ward_id AS Ward,
        d.name_formal AS department_name, (CASE current_room_nr WHEN 0 then area ELSE current_room_nr END) AS Area_P,
        IF(accomodation_type=1,'CHA','PAY') AS ward_type,
        e.pid AS HOSP_Num, e.encounter_nr AS CASE_Num, e.discharge_date, e.received_date,
        ins.hcare_id, IF(ins.hcare_id=18,'P','NP') AS insurance,
        e.parent_encounter_nr, e.admission_dt,
        (SELECT ee.encounter_date FROM care_encounter ee WHERE ee.encounter_nr=e.parent_encounter_nr) AS er_date
        FROM care_encounter AS e
        LEFT JOIN care_person AS p ON p.pid=e.pid
        LEFT JOIN care_department AS d ON d.nr=e.current_dept_nr
        LEFT JOIN care_ward AS w ON w.nr = e.current_ward_nr
        LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
        WHERE e.encounter_type IN ('3','4') 
        $sql_dept
        AND e.status NOT IN ('deleted','hidden','inactive','void') 
        AND e.admission_dt IS NOT NULL $whereSQL\n
        GROUP BY patient_name 
        ORDER BY patient_name, e.admission_dt";
                    
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
            
            if (!(empty($row['received_date'])))
              $received_date = date("m/d/Y",strtotime($row['received_date']));
            else
              $received_date = 'not yet';  
              
            if (!(empty($row['discharge_date'])))
              $discharged_date = date("m/d/Y",strtotime($row['discharge_date']));
              
            if ((!(empty($row['er_date'])))&&($row['er_date']!='0000-00-00 00:00:00'))
                $er_date = date("m/d/Y h:i A",strtotime($row['er_date']));
            else        
                $er_date = 'Direct Admission';    
                
            if ((!(empty($row['admission_dt'])))&&($row['admission_dt']!='0000-00-00 00:00:00'))
                $admission_date = date("m/d/Y h:i A",strtotime($row['admission_dt']));    
            else
                $admission_date = '';   
            
             $col=0;
             $this->worksheet->write($newrow, $col, $i, $this->format2);
             $this->worksheet->write($newrow, $col+1, mb_strtoupper($row['patient_name']), $this->format2);
             $this->worksheet->write($newrow, $col+2, $received_date, $this->format3);
             $this->worksheet->write($newrow, $col+3, $discharged_date, $this->format3);
             $this->worksheet->write($newrow, $col+4, $row['insurance'], $this->format3);
             $this->worksheet->write($newrow, $col+5, $row['department_name'], $this->format2);
             $this->worksheet->write($newrow, $col+6, $row['HOSP_Num'], $this->format3);
             $this->worksheet->write($newrow, $col+7, $row['CASE_Num'], $this->format3);
             $this->worksheet->write($newrow, $col+8, $er_date, $this->format3);
             $this->worksheet->write($newrow, $col+9, $admission_date, $this->format3);
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
  
  $rep = new ExcelGen_AdmissionList($_GET['from'], $_GET['to'], $_GET['dept_nr']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_admission_list.xls');
  $rep->close();
?>