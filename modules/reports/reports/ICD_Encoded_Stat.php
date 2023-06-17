<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $dept_label);

    $sql = "SELECT d.name_formal AS department ,
                  SUM(CASE WHEN cpi.hcare_id='18' THEN 1 ELSE 0 END) AS PHIC,
                  SUM(CASE WHEN cpi.hcare_id!='18' OR cpi.hcare_id IS NULL THEN 1 ELSE 0 END) AS NPHIC
            FROM care_person_insurance AS cpi
            INNER JOIN care_encounter AS e ON e.pid = cpi.pid
            INNER JOIN care_department AS d ON d.nr = e.current_dept_nr
            RIGHT JOIN (SELECT DISTINCT encounter_nr FROM care_encounter_diagnosis 
                WHERE code IS NOT NULL 
                AND status NOT IN('deleted', 'void', 'hidden', 'cancelled')) AS ced
            ON e.encounter_nr = ced.encounter_nr
            WHERE   
            AND e.status NOT IN ('deleted', 'void', 'hidden', 'cancelled')
            AND e.encounter_type IN ($patient_type)
            $enc_dept_cond
            AND DATE(ced.create_time) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            GROUP BY d.name_formal;";
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $total_nphic += $row['NPHIC'];
            $total_phic += $row['PHIC'];
            $data[$rowindex]=array(
                              'department' => mb_strtoupper($row['department']),
                              'PHIC' => (int) $row['PHIC'],
                              'NPHIC' => (int) $row['NPHIC'],
                              'Total_NPHIC' => (int) $total_nphic,
                              'Total_PHIC' => (int) $total_phic,
                              'Total_Records' => (int) $rowindex+1,
                              'encoder_name'=> $encoder_name
                              );                  
           $rowindex++;
        }  
        
    }else{
        $data[0]['department'] = NULL; 
    }       