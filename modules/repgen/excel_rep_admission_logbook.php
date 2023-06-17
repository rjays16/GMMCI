<?php
  #created by CHERRY 01-06-11
  # OPD Daily Transactions Report
  
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
  require_once($root_path."/classes/excel/Writer.php");
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  include_once($root_path.'include/care_api_classes/class_personell.php');   
  require_once($root_path.'include/care_api_classes/class_department.php'); 
  
  class ExcelGen_AdmissionLogbook extends Spreadsheet_Excel_Writer
  {
      var $worksheet;
      var $Headers;
      var $format1, $format2, $format3;
      var $count;
      
      var $from_date;
      var $to_date;
      var $dept_nr;
      
      function ExcelGen_AdmissionLogbook($from, $to, $dept_nr)
      {
          $this->Spreadsheet_Excel_Writer();
          $this->worksheet = & $this->addWorksheet();
          
          $this->dept_nr = $dept_nr;
                                                    
          $this->worksheet->setPaper(1);      // Letter
          $this->worksheet->setLandscape();
          $this->worksheet->setMarginTop(1.2);
          $this->worksheet->setMarginLeft(0.6);
          $this->worksheet->setMarginRight(0.5);
          $this->worksheet->setMarginBottom(0.5);
          
          $this->Caption = "COMPUTERIZED ADMISSION LOGBOOK"; 
          
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
         
          $this->ColumnWidth = array(5,8,7,8,15,8,4,4,5,15,10,15,15);  
          
          $this->Headers = array(
            '',
            'Adm. No.',
            'HRN',
            'Admitted',
            'Patient',
            'Birth Date',
            'Age',
            'Sex',
            'Status',
            'Address',
            'Department',
            'Adm. Doctor',
            'Adm. Diagnosis'
          );
          
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
          
          $this->worksheet->setHeader("".$row['hosp_country']."\n".$row['hosp_agency']."\n".$row['hosp_name'],0.5);  
     
          if (($this->from)==($this->to))
            $text = "For ".date("m/d/Y",strtotime($this->from));
          else
            #$this->Cell(0,4,date("m/d/Y",strtotime($this->from))." - ".date("m/d/Y",strtotime($this->to)),$border2,1,'C');
            $text = date("m/d/Y",strtotime($this->from))." - ".date("m/d/Y",strtotime($this->to));
     
            $rownum1 = 0;
            $rownum2 = 3;
            $this->len_header = count($this->Headers);   
            for($cnt = 0; $cnt < $this->len_header; $cnt++){
              $this->worksheet->setColumn($rownum1, $cnt, $this->ColumnWidth[$cnt]); 
              $this->worksheet->write($rownum2, $cnt, $this->Headers[$cnt], $this->format1); 
            }         
            
         $center = ceil($this->len_header / 2);   
            
         $this->worksheet->write(0, $center, $this->Caption, $this->format3); 
         $this->worksheet->write(1, $center, $text, $this->format3);
         #$this->worksheet->write(2, 0, "Number of Records : ", $this->format4);
          
      }
               
      function FetchData()
      {
        global $db;
    
        $pers_obj=new Personell();
        $dept_obj = new Department();
        
        if (empty($this->to)) $end_date="NOW()";
        else $end_date=$this->to;
        if (empty($this->from)) $start_date="NOW()";
        else
        $start_date=$this->from;
    
        $sql_dept=""; 
        
        if ($this->dept_nr){
          $sql_dept = " AND (ce.current_dept_nr='".$this->dept_nr."' OR ce.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='".$this->dept_nr."'))";
        }
    
        $sql = "SELECT ce.encounter_nr, cp.pid,
          CAST(admission_dt as DATE) as admission_date,
          CAST(admission_dt AS TIME) AS admission_time,
                CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS patientname,
          cp.street_name,  sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name,
          cp.date_birth,IF(fn_calculate_age(NOW(),cp.date_birth),fn_get_age(CAST(encounter_date AS date),cp.date_birth),age) AS age,
                UPPER(sex) AS p_sex,cp.civil_status,
          cd.name_formal,ce.current_att_dr_nr,ce.consulting_dr_nr,
          ce.er_opd_diagnosis,
           addr_str, cd.id
        FROM (care_encounter AS ce
          INNER JOIN care_person AS cp ON ce.pid = cp.pid)

          LEFT JOIN care_department AS cd ON ce.current_dept_nr = cd.nr
          LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
          LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
          LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
          LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
        WHERE (ce.create_time >= '$start_date'
          AND CONCAT(CAST(ce.create_time AS date), ' 00:00:00') < DATE_ADD('$end_date', INTERVAL 1 DAY))
          AND ce.encounter_type IN (3,4)
          AND ce.status NOT IN ('deleted','hidden','inactive','void')
          $sql_dept";
        
        $sql .= " ORDER BY ce.encounter_nr, name_last, name_first, name_middle"; 
        
        $result=$db->Execute($sql);
        $this->_count = $result->RecordCount();  
        if ($result)
        {      
          $i=1;  
          $newrow=4;  
          while ($row=$result->FetchRow())
          {              
            
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
                #$province = trim($prov_name);
                $prov_name = "";
              }
            }else
              $prov_name = "";

            $addr = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

            if ($row["current_att_dr_nr"])
              $docInfo = $pers_obj->getPersonellInfo($row["current_att_dr_nr"]);
            else
              $docInfo = $pers_obj->getPersonellInfo($row["consulting_dr_nr"]);
              
            $dr_middleInitial = "";
            if (trim($docInfo['name_middle'])!=""){
              $thisMI=split(" ",$docInfo['name_middle']);
              foreach($thisMI as $value){
                if (!trim($value)=="")
                  $dr_middleInitial .= $value[0];
              }
              if (trim($dr_middleInitial)!="")
                $dr_middleInitial = " ".$dr_middleInitial.".";
            }
            $name_doctor = $docInfo['name_last'].", ".$docInfo['name_first']." ".$dr_middleInitial;
            
            if (trim($name_doctor)==',')
              $name_doctor = "";

            if ($row['civil_status']=='married')
              $cstatus = "M";
            elseif ($row['civil_status']=='single')
              $cstatus = "S";
            elseif ($row['civil_status']=='child')
              $cstatus = "CH";
            elseif ($row['civil_status']=='divorced')
              $cstatus = "D";
            elseif ($row['civil_status']=='widowed')
              $cstatus = "W";
            elseif ($row['civil_status']=='separated')
              $cstatus = "S";
              
            $age ='';
            if (($row['date_birth']) && ($row['date_birth']!='0000-00-00') ){
              $bdate = date("m/d/Y",strtotime($row['date_birth']));
            }else{
              $bdate = 'unknown';
            }
            
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
            }else{
              $age = floor($row['age']).' y';
            }
            
               $col=0;
               $this->worksheet->write($newrow, $col, $i, $this->format2);
               $this->worksheet->write($newrow, $col+1, $row['encounter_nr'], $this->format5);
               $this->worksheet->write($newrow, $col+2, $row['pid'], $this->format5);
               $this->worksheet->write($newrow, $col+3, date("m/d/y",strtotime($row['admission_date']))." ".date("h:iA",strtotime($row['admission_time'])), $this->format5);
               $this->worksheet->write($newrow, $col+4, mb_strtoupper(trim($row['patientname'])), $this->format2);
               $this->worksheet->write($newrow, $col+5, $bdate, $this->format5);
               $this->worksheet->write($newrow, $col+6, $age, $this->format5);
               $this->worksheet->write($newrow, $col+7, mb_strtoupper($row['p_sex']), $this->format5);
               $this->worksheet->write($newrow, $col+8, mb_strtoupper($cstatus), $this->format5);
               $this->worksheet->write($newrow, $col+9, ucwords(mb_strtolower(trim($addr))), $this->format2); 
               $this->worksheet->write($newrow, $col+10, $row['name_formal'], $this->format2);
               $this->worksheet->write($newrow, $col+11, ucwords(mb_strtolower($name_doctor)), $this->format2);
               $this->worksheet->write($newrow, $col+12, trim($row['er_opd_diagnosis']), $this->format2);
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
  
  $rep = new ExcelGen_AdmissionLogbook($_GET['from'],$_GET['to'],$_GET['dept_nr']);
  $rep->ExcelHeader();
  $rep->FetchData();
  $rep->AfterData(); 
  $rep->send('rep_admission_logbook.xls');
  $rep->close();
?>