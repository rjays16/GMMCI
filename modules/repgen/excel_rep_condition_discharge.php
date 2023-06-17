<?php
  #created by CHA 07-21-09
  #Research and Query Report- for DMC
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_ResultOfTreatment_Discharge extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      
      var $fromdate;
      var $todate;
      var $dept_nr;
      var $count;
      
      var $imp_male,$imp_female,$imp_total,$unimp_male,$unimp_female,$unimp_total,$total_f,$total_m;
      
      function ExcelGen_ResultOfTreatment_Discharge($fromdate, $todate, $dept_nr)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          $this->worksheet->setPaper(5);      // Legal
          $this->worksheet->setPortrait();
          $this->worksheet->setMarginTop(1.8);
          $this->worksheet->setMarginLeft(0.7);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.5);
          $this->Headers = array(
              'Condition on Discharge',
              'Discharge Diagnosis(Primary)',
              'Improved', 'Unimproved', 'Total',
              'ICD-10 CODE', 'M', 'F'
            );
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
          
          if($fromdate) $this->fromdate=$fromdate;
          if($todate) $this->todate=$todate;
          if($dept_nr) $this->dept_nr=$dept_nr;
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
          if ($this->fromdate==$this->todate)
            $text = "For ".date("F j, Y",strtotime($this->fromdate));
          else
            $text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));            
            
          if ($this->dept_nr){
            #$deptinfo = $dept_obj->getDeptAllInfo($this->dept_nr);
            #$deptname = $deptinfo['name_formal'];
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
          $this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name']."\n".$row['hosp_addr1']."\n\n".$deptname." - DISCHARGE DIAGNOSIS (PRIMARY)\n".$text."\n",0.5);

          $this->worksheet->setColumn(0, 0, 3);
          $this->worksheet->setColumn(0, 1, 38);
          $this->worksheet->setColumn(0, 2, 4);
          $this->worksheet->setColumn(0, 5, 4);
          $this->worksheet->setColumn(0, 8, 4);
          $this->worksheet->setColumn(0, 9, 4);
          $this->worksheet->setColumn(0, 10, 10);
          $this->worksheet->write(0, 6, $this->Headers[0], $this->format1);
          $this->worksheet->write(1, 1, $this->Headers[1], $this->format1);
          $this->worksheet->write(1, 3, $this->Headers[2], $this->format1);
          $this->worksheet->write(1, 6, $this->Headers[3], $this->format1);
          $this->worksheet->write(1, 8, $this->Headers[4], $this->format1);
          $this->worksheet->write(1, 10, $this->Headers[5], $this->format1);
          $this->worksheet->write(2, 2, $this->Headers[6], $this->format1);
          $this->worksheet->write(2, 3, $this->Headers[7], $this->format1);
          $this->worksheet->write(2, 4, $this->Headers[4], $this->format1);
          $this->worksheet->write(2, 5, $this->Headers[6], $this->format1);
          $this->worksheet->write(2, 6, $this->Headers[7], $this->format1);
          $this->worksheet->write(2, 7, $this->Headers[4], $this->format1);
          $this->worksheet->write(2, 8, $this->Headers[6], $this->format1);
          $this->worksheet->write(2, 9, $this->Headers[7], $this->format1);
          
      }
               
      function FetchData()
      {
          global $db;
          $dept_obj = new Department();
  
          if ($this->fromdate) {
            $where[]="DATE(discharge_date) BETWEEN '".$this->fromdate."' AND '".$this->todate."'";
          }

          if ($where)
            $whereSQL = "AND (".implode(") AND (",$where).")";
    
          $sql_dept=""; 
          if ($this->dept_nr){
              #$sql_dept = " AND (e.current_dept_nr='".$this->dept_nr."' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='".$this->dept_nr."'))";      
							if ($this->dept_nr==1)
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
      
          $sql = "SELECT cd.code,cd.code_parent AS ICD,en.description AS diagnosis, 
                SUM(CASE WHEN (sr.result_code = 5 OR sr.result_code = 6) AND sd.disp_code=7 AND p.sex = 'm' then 1 else 0 end) AS Improved_M, 
                SUM(CASE WHEN (sr.result_code = 5 OR sr.result_code = 6) AND sd.disp_code=7 AND p.sex = 'f' then 1 else 0 end) AS Improved_F, 
                SUM(CASE WHEN sr.result_code = 7 AND sd.disp_code=7 AND p.sex = 'm' then 1 else 0 end) AS Unimproved_M, 
                SUM(CASE WHEN sr.result_code = 7 AND sd.disp_code=7 AND p.sex = 'f' then 1 else 0 end) AS Unimproved_F

              FROM care_encounter_diagnosis AS cd 
              INNER JOIN care_icd10_en AS en ON en.diagnosis_code = cd.code
              LEFT JOIN care_encounter AS e ON e.encounter_nr = cd.encounter_nr 

              LEFT JOIN (SELECT ser.encounter_nr,SUBSTRING(MAX(CONCAT(ser.create_time,ser.result_code)),20) AS result_code
                                FROM seg_encounter_result AS ser 
                                INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr 
                                WHERE em.encounter_type IN (3,4) 
                                AND em.discharge_date IS NOT NULL
                                $whereSQL
                                GROUP BY ser.encounter_nr
                                ORDER BY ser.encounter_nr, ser.create_time DESC) AS sr ON sr.encounter_nr = cd.encounter_nr 

              LEFT JOIN (SELECT sed.encounter_nr,SUBSTRING(MAX(CONCAT(sed.create_time,sed.disp_code)),20) AS disp_code
                                FROM seg_encounter_disposition AS sed 
                                INNER JOIN care_encounter AS em ON em.encounter_nr=sed.encounter_nr 
                                WHERE em.encounter_type IN (3,4) 
                                AND em.discharge_date IS NOT NULL
                                $whereSQL
                                GROUP BY sed.encounter_nr
                                ORDER BY sed.encounter_nr, sed.create_time DESC) AS sd ON sd.encounter_nr = cd.encounter_nr

              INNER JOIN care_person AS p ON p.pid = e.pid
              WHERE cd.status NOT IN ('deleted','hidden','inactive','void')
              AND e.encounter_type IN (3,4)
              AND e.discharge_date IS NOT NULL 
              $whereSQL 
              $sql_dept
              GROUP BY cd.code
              ORDER BY SUM(CASE WHEN (sr.result_code = 5 OR sr.result_code = 6 OR sr.result_code = 7)
                       AND sd.disp_code=7 then 1 else 0 end) DESC LIMIT 25";

        $result=$db->Execute($sql);
        if ($result)
        {      
          $i=1;
          $this->imp_male = 0;
          $this->imp_female = 0;
          $this->imp_total = 0;
          
          $this->unimp_male = 0;
          $this->unimp_female = 0;
          $this->unimp_total = 0;
          
          $this->total_m = 0;
          $this->total_f = 0;
          
          $this->count = $result->RecordCount(); 
          $newrow=3;
          while ($row=$result->FetchRow())
          {              
            $total_improved =  $row['Improved_M'] + $row['Improved_F'];
            $total_unimproved =  $row['Unimproved_M'] + $row['Unimproved_F'];
            
            $total_female =  $row['Improved_F'] + $row['Unimproved_F'];
            $total_male =  $row['Improved_M'] + $row['Unimproved_M'];
            
            $total =  $total_male + $total_female;
            
            $this->imp_male += $row['Improved_M'];
            $this->imp_female += $row['Improved_F'];
            $this->imp_total = $this->imp_total + ($row['Improved_M'] + $row['Improved_F']);
            
            $this->unimp_male += $row['Unimproved_M'];
            $this->unimp_female += $row['Unimproved_F'];
            $this->unimp_total =  $this->unimp_total + ($row['Unimproved_M'] + $row['Unimproved_F']);
            
            $this->total_m = $this->total_m + $total_male;
            $this->total_f = $this->total_f + $total_female;
            
            if ($row['diagnosis'])
              $diagnosis = $row['diagnosis'];
            else
              $diagnosis = $row['code'];  
            
             $col=0;
             $this->worksheet->write($newrow, $col, $i, $this->format2);
             $this->worksheet->write($newrow, $col+1, $diagnosis, $this->format2);
             $this->worksheet->write($newrow, $col+2, $row['Improved_M'], $this->format3);
             $this->worksheet->write($newrow, $col+3, $row['Improved_F'], $this->format3);
             $this->worksheet->write($newrow, $col+4, $total_improved, $this->format3);
             $this->worksheet->write($newrow, $col+5, $row['Unimproved_M'], $this->format3);
             $this->worksheet->write($newrow, $col+6, $row['Unimproved_F'], $this->format3);
             $this->worksheet->write($newrow, $col+7, $total_unimproved, $this->format3);
             $this->worksheet->write($newrow, $col+8, $total_male, $this->format3);
             $this->worksheet->write($newrow, $col+9, $total_female, $this->format3);
             $this->worksheet->write($newrow, $col+10, $row['code'], $this->format3);
             $newrow++; 
            $i++;
          }     
        }
      }
      
      function AfterData()
      {
          if (!$this->count) 
          {
            $this->worksheet->write(3, 0, "No records found for this report...");
          }
      } 
  }
  
  $rep = new ExcelGen_ResultOfTreatment_Discharge($_GET['from'], $_GET['to'], $_GET['dept_nr_sub']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_condition_discharge.xls');
  $rep->close();
?>