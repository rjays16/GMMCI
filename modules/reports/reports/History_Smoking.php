<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $sub_caption);
        
    $patient_type = '3,4';
    $date_based = 'e.encounter_date';
    
    $sql = "SELECT DISTINCT e.pid AS hrn,
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',
            IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ',
            IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS 'Full_Name',
            e.admission_dt AS 'Date_Admitted', e.encounter_date AS 'Date_Consultation', 
            UPPER(p.sex) AS Sex,
            IF(p.date_birth!='0000-00-00',p.date_birth,NULL) AS date_birth,
            IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
            d.name_formal AS department,
            CONCAT(IF (TRIM(p.street_name) IS NULL,'',TRIM(p.street_name)),' ',
                IF (TRIM(sb.brgy_name) IS NULL,'',TRIM(sb.brgy_name)),' ',
                IF (TRIM(sm.mun_name) IS NULL,'',TRIM(sm.mun_name)),' ',
                IF (TRIM(sm.zipcode) IS NULL,'',TRIM(sm.zipcode)),' ',
                IF (TRIM(sp.prov_name) IS NULL,'',TRIM(sp.prov_name)),' ',
                IF (TRIM(sr.region_name) IS NULL,'',TRIM(sr.region_name))) AS 'Complete_Address',
            UPPER(IF (e.current_att_dr_nr,
            fn_get_personell_name(e.current_att_dr_nr),fn_get_personell_name(e.consulting_dr_nr))) AS 'Attending_Physician',
            e.encounter_type, e.smoker_history, e.drinker_history
            
            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_department AS d 
                ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            LEFT JOIN seg_encounter_result AS ser ON ser.encounter_nr = e.encounter_nr
            INNER JOIN seg_results AS res ON res.result_code=ser.result_code
            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr

            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
            LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            GROUP BY IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr),e.pid ORDER BY encounter_date";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
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
              
            if ($row['drinker_history']=='yes')
              $drinker_history = "DRINKER";
            elseif ($row['drinker_history']=='no')
              $drinker_history = "NON-DRINKER";
            elseif ($row['drinker_history']=='na')
              $drinker_history = "UNSPECIFIED";
            else
              $drinker_history = "UNSPECIFIED";      
                        
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'hrn' => $row['hrn'],
                              'Full_Name' => $row['Full_Name'],
                              'Date_Admitted' => date("m/d/Y h:i A",strtotime($row['Date_Admitted'])),
                              'Age' => $row['Age'],
                              'Sex' => $row['Sex'],
                              'Complete_Address' => $row['Complete_Address'],
                              'department' => $row['department'],
                              'patient_type' => $patient_type,
                              'is_smoking' => $smoking_history,
                              'is_drinking' => $drinker_history,
                              'Attending_Physician' => $row['Attending_Physician'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Case_No'] = NULL; 
    }       