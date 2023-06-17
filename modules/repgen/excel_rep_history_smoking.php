<?php
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_OPDTrans extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      var $count;
      
      var $from_date;
      var $to_date;  
      var $dept_nr;
      var $from_time;  
      var $to_time;  
      var $OB_array;
      var $orderby;
      
      function ExcelGen_OPDTrans($from, $to, $dept_nr, $fromtime, $totime, $orderby)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          
          $this->dept_nr = $dept_nr;
          
          $this->worksheet->setPaper(1);  //Letter
          $this->ColumnWidth = array(5, 8, 20, 8, 8, 10, 20, 12, 20, 20, 30);
          $this->Headers = array(
              '',
              'Patient ID',
              'Fullname',
              'Time',
              'Age',
              'Gender',
              'Address',
              'Department',
              'Patient Type',
              'History of Smoking',
              'Physician'
            );
                                                    
          $this->worksheet->setLandscape();
          $this->worksheet->setMarginTop(0.5);
          $this->worksheet->setMarginLeft(0.5);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.5);
          
          $this->Caption = "Outpatient Preventive Care Center History of Smoking";  
          $this->orderby = $orderby; 
          
          $this->format1=& $this->addFormat();
          $this->format1->setSize(9);
          $this->format1->setBold();
          $this->format1->setAlign('center');
          
          $this->format2=& $this->addFormat();
          $this->format2->setSize(8);
          $this->format2->setAlign('left');
          $this->format2->setTextWrap(1);
          
          $this->format3=& $this->addFormat();
          $this->format3->setSize(12);
          $this->format3->setBold();
          $this->format3->setAlign('center');
          
          $this->format4=& $this->addFormat();
          $this->format4->setSize(12);
          $this->format4->setBold();
          $this->format4->setAlign('left');
          
          $this->format5=& $this->addFormat();
          $this->format5->setSize(8);
          $this->format5->setAlign('center');
          $this->format5->setTextWrap(1);
          
         if ($from) $this->from=date("Y-m-d",strtotime($from));
         if ($to) $this->to=date("Y-m-d",strtotime($to));
         
         $this->from_time = $fromtime;
         $this->to_time = $totime;
         
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
            $row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
            $row['hosp_addr1']   = "JICA Bldg. JP Laurel Bajada, Davao City";      
          }
     
            $rownum1 = 0;
            $rownum2 = 3;
            $this->len_header = count($this->Headers);   
            for($cnt = 0; $cnt < $this->len_header; $cnt++){
              $this->worksheet->setColumn($rownum1, $cnt, $this->ColumnWidth[$cnt]); 
              $this->worksheet->write($rownum2, $cnt, $this->Headers[$cnt], $this->format1); 
            }         
            
         $center = ceil($this->len_header / 2);   
            
         $this->worksheet->write(0, $center, $this->Caption, $this->format3); 
         $this->worksheet->write(1, $center, date("m/d/Y",strtotime($this->from))."  ".date("h:i A",strtotime($this->from_time))." - ".date("m/d/Y",strtotime($this->to))."  ".date("h:i A",strtotime($this->to_time)), $this->format3);
          
      }
               
      function FetchData(){
        global $db;
    
        if (empty($this->to)) $end_date="NOW()";
        else $end_date=$this->to;
        if (empty($this->from)) $start_date="NOW()";
        else
        $start_date=$this->from;
        
         if ($this->dept_nr) {
            $sql_dept = " AND ce.current_dept_nr=".$this->dept_nr;
            $grp_sql = " ";
          
          if ($this->orderby)
              $order_sql = " ORDER BY name_last, name_first, name_middle ";
          else
            $order_sql = " ORDER BY encounter_date ";  
          }else{
            $grp_sql = " GROUP BY ce.current_dept_nr,ce.pid ";
            
          if ($this->orderby)
              $order_sql = " ORDER BY name_last, name_first, name_middle ";
          else
            $order_sql = " ORDER BY encounter_date ";
          }
    
        $sql =  "SELECT distinct cp.pid, cd.name_formal,
                    CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname, 
                    CAST(encounter_date as DATE) as consult_date, 
                    CAST(encounter_date AS TIME) AS consult_time, 
                    fn_get_age(CAST(encounter_date AS date), CAST(date_birth AS DATE)) AS age, 
                    UPPER(sex) AS p_sex, addr_str, cd.id,
                    cp.street_name,    sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name, ce.encounter_nr,
                    ce.encounter_type, smoker_history, 
                    fn_get_personell_name(fn_get_icd_dr_encounter(ce.encounter_nr)) AS diagnosing_clinician
                    
                FROM care_encounter AS ce 
                    INNER JOIN care_person AS cp ON ce.pid = cp.pid
                      LEFT JOIN care_department AS cd ON ce.current_dept_nr = cd.nr
                    LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
                    LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
                    LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
                    LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
                WHERE DATE(ce.encounter_date) BETWEEN '$start_date' AND '$end_date'
                    AND ce.status NOT IN ('deleted','hidden','inactive','void')
                  $sql_dept";
        
        $sql .= " $grp_sql $order_sql";  
        
        $result=$db->Execute($sql);
        $this->_count = $result->RecordCount();
        if ($result)
        {      
          $i=1;  
          $newrow=4;  
              while ($row=$result->FetchRow()){              
               
                if (trim($row['street_name'])){
                    if (trim($row["brgy_name"])!="NOT PROVIDED")
                        $street_name = trim($row['street_name']).", ";
                    else
                        $street_name = trim($row['street_name']).", ";  
                }else{
                    $street_name = "";  
                }  
            
        
                if ((!(trim($row["brgy_name"]))) || (trim($row["brgy_name"])=="NOT PROVIDED"))
                    $brgy_name = "";
                else 
                    $brgy_name  = trim($row["brgy_name"]).", ";  
              
                if ((!(trim($row["mun_name"]))) || (trim($row["mun_name"])=="NOT PROVIDED"))
                    $mun_name = "";    
                else{  
                    if ($brgy_name)
                      $mun_name = trim($row["mun_name"]);  
                    else
                      $mun_name = trim($row["mun_name"]);    
              }
            
                if ((!(trim($row["prov_name"]))) || (trim($row["prov_name"])=="NOT PROVIDED"))
                    $prov_name = "";    
                else
                    $prov_name = trim($row["prov_name"]);      
            
                if(stristr(trim($row["mun_name"]), 'city') === FALSE){
                    if ((!empty($row["mun_name"]))&&(!empty($row["prov_name"]))){
                        if ($prov_name!="NOT PROVIDED")  
                            $prov_name = ", ".trim($prov_name);
                        else
                            $prov_name = trim($prov_name);  
                    }else{
                        $prov_name = "";
                    }
                }else
                    $prov_name = "";  
            
                $addr = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);
                
                if (stristr($row['age'],'years')){
                    $age = substr($row['age'],0,-5);
                    $age = floor($age).' y';
                }elseif (stristr($row['age'],'year')){    
                    $age = substr($row['age'],0,-4);
                    $age = floor($age).' y';
                }elseif (stristr($row['age'],'months')){    
                    $age = substr($row['age'],0,-6);
                    $age = floor($age).' m';    
                }elseif (stristr($row['age'],'month')){    
                    $age = substr($row['age'],0,-5);
                    $age = floor($age).' m';        
                }elseif (stristr($row['age'],'days')){    
                    $age = substr($row['age'],0,-4);
                        
                if ($age>30){
                    $age = $age/30;
                    $label = 'm';
                }else
                    $label = 'd';
                            
                    $age = floor($age).' '.$label;        
                }elseif (stristr($row['age'],'day')){    
                    $age = substr($row['age'],0,-3);
                    $age = floor($age).' d';        
                }
                
                    if ($row['encounter_type']==2)
                        $patient_type = 'Outpatient';
                    elseif ($row['encounter_type']==1)
                        $patient_type = 'ER Patient';
                    elseif (($row['encounter_type']==3) || ($row['encounter_type']==4))
                        $patient_type = 'Inpatient';
                    else
                        $patient_type = 'Walkin';        
                        
                    if ($row['smoking_history']=='yes')
                      $smoking_history = "SMOKER";
                    elseif ($row['smoking_history']=='no')
                      $smoking_history = "NON-SMOKER";
                    elseif ($row['smoking_history']=='na')
                      $smoking_history = "UNSPECIFIED";
                    else
                      $smoking_history = "UNSPECIFIED";  
                
                $col=0;
                $this->worksheet->write($newrow, $col, $i, $this->format2);
                $this->worksheet->write($newrow, $col+1, $row['pid'], $this->format2);
                $this->worksheet->write($newrow, $col+2, trim($row['fullname']), $this->format2);
                $this->worksheet->write($newrow, $col+3, date("h:i A",strtotime($row['consult_time'])), $this->format5);
                $this->worksheet->write($newrow, $col+4, $age, $this->format5);
                $this->worksheet->write($newrow, $col+5, strtoupper($row['p_sex']), $this->format5);
                $this->worksheet->write($newrow, $col+6, trim($addr), $this->format2);
                $this->worksheet->write($newrow, $col+7, $row['name_formal'], $this->format2);
                $this->worksheet->write($newrow, $col+8, $patient_type, $this->format2);
                $this->worksheet->write($newrow, $col+9, $smoking_history, $this->format2);
                $this->worksheet->write($newrow, $col+10, $row['diagnosing_clinician'], $this->format2);
                $newrow++; 
                $i++;
                   
            }     
        }
        
        $this->worksheet->write(2, 0, "Number of Records : ".$this->_count, $this->format4); 
      }
      
      function AfterData()
      {
          if (!$this->count) 
          {
            $this->worksheet->write(4, 0, "No records found for this report...");
          }
      } 
  }
  
  $rep = new ExcelGen_OPDTrans($_GET['from'],$_GET['to'], $_GET['dept_nr'],$_GET['fromtime'],$_GET['totime'],$_GET['orderby']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_history_smoking.xls');
  $rep->close();
?>