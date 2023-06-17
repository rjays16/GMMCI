<?php
  #created by Cherry 01-11-11
  #OB Admission List
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_OPS_Case extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      
      var $from, $to;
     
      function ExcelGen_OPS_Case($from, $to)
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
              'Patient Name', 'Case #', 'Sex', 'Age', 'Discharged',
              'Department/Serv', 'ICPM', 'Result'
            );
          $this->ColumnWidth = array(20, 10, 8, 8, 12, 15, 8, 10);
          $this->Caption = "PROCEDURE REPORT";
          
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
            $text = "As of ".date("F j, Y",strtotime($this->from));
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
       
       if (!$this->to)  {
            $where[]="DATE(e.discharge_date) BETWEEN '$this->from' AND NOW()";  
       }else if ($this->from) {
            $where[]="DATE(e.discharge_date) BETWEEN '$this->from' AND '$this->to'";
       }        
       if ($where)
            $whereSQL = "AND (".implode(") AND (",$where).")";
          
       $sql = "SELECT 
                        /*CONCAT(IFNULL(CONCAT(UPPER(SUBSTRING(p.name_last, 1, 1)), LOWER(SUBSTRING(p.name_last FROM 2))),''),
                        IFNULL(CONCAT(', ', CONCAT(UPPER(SUBSTRING(p.name_first, 1, 1)), LOWER(SUBSTRING(p.name_first FROM 2)))),''),
                        IFNULL(CONCAT(' ', CONCAT(UPPER(SUBSTRING(p.name_middle, 1, 1)), LOWER(SUBSTRING(p.name_middle FROM 2)))),'')) AS patient_name,*/
                        CONCAT(IF (trim(p.name_last) IS NULL,'',trim(p.name_last)),', ' ,IF(trim(p.name_first) IS NULL ,' ',trim(p.name_first)),' ', IF(trim(p.name_middle) IS NULL,' ',trim(p.name_middle))) AS patient_name,
                        e.encounter_nr,
                        /*MAX(er.modify_time) as mytime,*/
                        er.modify_time as mytime,
                        p.sex,
                        /*IF(p.date_birth='0000-00-00','N/A',FLOOR(fn_calculate_age(p.date_birth,NOW()))) AS age,*/
                        IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
                        e.discharge_date,
                        d.name_formal AS department_name,
                        IF(r.result_desc!='',r.result_desc,'Unimproved') AS result_desc,
                        pr.code

                        FROM care_encounter AS e

                        LEFT JOIN care_person AS p ON p.pid=e.pid
                        LEFT JOIN care_department AS d ON d.nr=e.current_dept_nr
                        LEFT JOIN care_encounter_procedure AS pr ON e.encounter_nr=pr.encounter_nr
                        LEFT JOIN care_ops301_en AS op ON op.code=pr.code
                        /*LEFT JOIN seg_encounter_result AS er ON er.encounter_nr=e.encounter_nr*/
                        LEFT JOIN (SELECT ser.encounter_nr,SUBSTRING(MAX(CONCAT(ser.create_time,ser.result_code)),20) AS result_code,
                                  MAX(ser.modify_time) AS modify_time
                                  FROM seg_encounter_result AS ser 
                                  INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr 
                                  WHERE (DATE(discharge_date) BETWEEN '$this->from' AND '$this->to') 
                                  AND em.encounter_type IN (3,4) 
                                  AND em.discharge_date IS NOT NULL
                                  GROUP BY ser.encounter_nr 
                                  ORDER BY ser.encounter_nr, ser.create_time DESC) AS er ON er.encounter_nr=e.encounter_nr
                                  
                        LEFT JOIN seg_results AS r ON r.result_code=er.result_code
                        WHERE e.encounter_type IN (3,4) 
                        AND e.discharge_date IS NOT NULL $whereSQL\n 
                        AND pr.procedure_nr IS NOT NULL 
                        AND e.status NOT IN ('deleted','hidden','inactive','void') 
                        AND pr.status NOT IN ('deleted','hidden','inactive','void')
                        AND er.modify_time IS NOT NULL";
                        
        $sql .= " GROUP BY er.encounter_nr,pr.code ";
        $sql .= "ORDER BY patient_name,discharge_date";
                    
        $result=$db->Execute($sql);
        if ($result)
        {      
          $this->count = $result->RecordCount(); 
          $i = 1;
          $newrow=2;
          while ($row=$result->FetchRow())
          {       
            
 
             $col=0;
             $this->worksheet->write($newrow, $col, mb_strtoupper($row['patient_name']), $this->format2);
             $this->worksheet->write($newrow, $col+1, $row['encounter_nr'], $this->format2);
             $this->worksheet->write($newrow, $col+2, strtoupper($row['sex']), $this->format3);
             $this->worksheet->write($newrow, $col+3, $row['age'], $this->format3);
             $this->worksheet->write($newrow, $col+4, date("m/d/Y",strtotime($row['discharge_date'])), $this->format3);
             $this->worksheet->write($newrow, $col+5, $row['department_name'], $this->format2);
             $this->worksheet->write($newrow, $col+6, $row['code'], $this->format3);
             $this->worksheet->write($newrow, $col+7, $row['result_desc'], $this->format3);
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
  
  $rep = new ExcelGen_OPS_Case($_GET['from'], $_GET['to']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_ops_case.xls');
  $rep->close();
?>