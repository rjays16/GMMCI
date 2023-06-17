<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", '');
    
    $patient_type = '3,4';
    
    $sql = "SELECT 
            SUM(CASE WHEN ed.CODE IN ('O03.9') THEN 1 ELSE 0 END) AS ns_comp_spon,
            SUM(CASE WHEN ed.CODE IN ('O06.9') THEN 1 ELSE 0 END) AS ns_comp_induced,
            SUM(CASE WHEN ed.CODE IN ('O03.4') THEN 1 ELSE 0 END) AS ns_incomp_spon,
            SUM(CASE WHEN ed.CODE IN ('O06.4') THEN 1 ELSE 0 END) AS ns_incomp_induced,
            SUM(CASE WHEN ed.CODE IN ('O03.5') THEN 1 ELSE 0 END) AS s_comp_spon,
            SUM(CASE WHEN ed.CODE IN ('O06.5') THEN 1 ELSE 0 END) AS s_comp_induced,
            SUM(CASE WHEN ed.CODE IN ('O03.0') THEN 1 ELSE 0 END) AS s_incomp_spon,
            SUM(CASE WHEN ed.CODE IN ('O06.0') THEN 1 ELSE 0 END) AS s_incomp_induced,
            SUM(CASE WHEN ed.CODE IN ('O02.0') THEN 1 ELSE 0 END) AS blighted,
            SUM(CASE WHEN ed.CODE IN ('O02.1') THEN 1 ELSE 0 END) AS missed_abortion,
            SUM(CASE WHEN ed.CODE IN ('O00.9') THEN 1 ELSE 0 END) AS ectopic,
            SUM(CASE WHEN ed.CODE IN ('O00.1') THEN 1 ELSE 0 END) AS ectopic_tubal,
            SUM(CASE WHEN ed.CODE IN ('O00.2') THEN 1 ELSE 0 END) AS ectopic_ovarian,
            SUM(CASE WHEN ed.CODE IN ('O00.8') THEN 1 ELSE 0 END) AS ectopic_other,
            SUM(CASE WHEN ed.CODE IN ('O20.0') THEN 1 ELSE 0 END) AS threatened_abortion
            FROM care_encounter_diagnosis AS ed
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.CODE
            INNER JOIN care_person AS p ON p.pid=e.pid
            WHERE ed.encounter_type IN ($patient_type)
            AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.CODE IN ('O03.9','O06.9','O03.4','O06.4','O03.5','O06.5','O03.0','O06.0','O02.0','O02.1', 'O00.9','O00.1','O00.2','O00.8','O20.0')";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'ns_comp_spon' => (int) $row['ns_comp_spon'],
                              'ns_comp_induced' => (int) $row['ns_comp_induced'],
                              'ns_incomp_spon' => (int) $row['ns_incomp_spon'],
                              'ns_incomp_induced' => (int) $row['ns_incomp_induced'],
                              's_comp_spon' => (int) $row['s_comp_spon'],
                              's_comp_induced' => (int) $row['s_comp_induced'],
                              's_incomp_spon' => (int) $row['s_incomp_spon'],
                              's_incomp_induced' => (int) $row['s_incomp_induced'],
                              'blighted' => (int) $row['blighted'],
                              'missed_abortion' => (int) $row['missed_abortion'],
                              'ectopic' => (int) $row['ectopic'],
                              'ectopic_tubal' => (int) $row['ectopic_tubal'],
                              'ectopic_ovarian' => (int) $row['ectopic_ovarian'],
                              'ectopic_other' => (int) $row['ectopic_other'],
                              'threatened_abortion' => (int) $row['threatened_abortion'],
                              );
                              
           $rowindex++;
        }  
          
    }else{
        $data[0]['code'] = NULL; 
    }     