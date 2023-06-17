<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", "");
    $params->put("column_name","OPERATIONS PERFORMED");
    
    $patient_type = '3,4';
    
    $sql = "SELECT DISTINCT e.encounter_nr AS 'Case_No', e.admission_dt AS 'Date_Admitted', e.er_opd_diagnosis,
            IF(p.date_birth!='0000-00-00',p.date_birth,NULL) AS date_birth,
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',
            IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ',
            IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS 'Patient_Name',
            IF(fn_calculate_age(NOW(),p.date_birth),
              fn_get_age(NOW(),p.date_birth),age) AS Age, UPPER(p.sex) AS Sex,
            CONCAT(IF (TRIM(p.street_name) IS NULL,'',TRIM(p.street_name)),' ',
                IF (TRIM(sb.brgy_name) IS NULL,'',TRIM(sb.brgy_name)),' ',
                IF (TRIM(sm.mun_name) IS NULL,'',TRIM(sm.mun_name)),' ',
                IF (TRIM(sm.zipcode) IS NULL,'',TRIM(sm.zipcode)),' ',
                IF (TRIM(sp.prov_name) IS NULL,'',TRIM(sp.prov_name)),' ',
                IF (TRIM(sr.region_name) IS NULL,'',TRIM(sr.region_name))) AS 'Address',
            res.result_desc AS 'Result',
            ed.CODE AS 'ICD_CODE', c.description AS 'ICD_Description',
            UPPER(IF (e.current_att_dr_nr, fn_get_personell_name

            (e.current_att_dr_nr),fn_get_personell_name(e.consulting_dr_nr))) AS 'Physician'

            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_encounter_diagnosis AS ed ON ed.encounter_nr=e.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.CODE
            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
            LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
            LEFT JOIN seg_encounter_result AS ser ON ser.encounter_nr = ed.encounter_nr
            INNER JOIN seg_results AS res ON res.result_code=ser.result_code
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type)
            AND IF(INSTR(c.diagnosis_code,'.'),
                        SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
                        c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'

            AND ed.CODE IN (SELECT diagnosis_code FROM care_icd10_en WHERE diagnosis_code
            LIKE 'c%' OR diagnosis_code LIKE 'd%' OR diagnosis_code LIKE '%/%')
            ORDER BY ed.CODE, p.name_last, p.name_first, p.name_middle, e.encounter_nr, DATE(e.admission_dt) ASC";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            if(($row['date_birth']!='0000-00-00') && ($row['date_birth']!=NULL))
                $date_birth = date("m/d/Y",strtotime($row['date_birth']));
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'Case_No' => $row['Case_No'],
                              'Date_Admitted' => date("m/d/Y h:i A",strtotime($row['Date_Admitted'])),
                              'er_opd_diagnosis' => $row['er_opd_diagnosis'],
                              'date_birth' => $date_birth,
                              'Patient_Name' => $row['Patient_Name'],
                              'Age' => $row['Age'],
                              'Sex' => $row['Sex'],
                              'Address' => $row['Address'],
                              'Result' => $row['Result'],
                              'ICD_CODE' => $row['ICD_CODE'],
                              'ICD_Description' => $row['ICD_Description'],
                              'Physician' => $row['Physician'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Case_No'] = NULL; 
    }       