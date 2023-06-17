<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    #$params->put("department", $dept_label);
    $params->put("department", $area_type);
    $params->put("column_name",$column_name_ave);
    
    $sql = "SELECT d.nr, d.name_formal AS Type_Of_Service,
            SUM(CASE WHEN (DATE($ave_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).")  THEN 1 ELSE 0 END) AS admitted,
            SUM(CASE WHEN (DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).")  THEN 1 ELSE 0 END) AS discharges,
            SUM(CASE WHEN (DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).")  
                THEN (DATEDIFF(e.discharge_date,IF(e.admission_dt, e.admission_dt, e.encounter_date))+1) ELSE 0 END) AS length_stay
            FROM care_encounter e
            LEFT JOIN care_department AS d 
            ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($ave_patient_type)
            GROUP BY d.name_formal
            ORDER BY d.name_formal";        
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $ave_length_stay = $row['length_stay'] / $row['discharges'];
            if (($ave_length_stay) || ($row['admitted'])){
                $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'Type_Of_Service' => $row['Type_Of_Service'],
                              'admissions' => (int) $row['admitted'],
                              'length_stay' => (int) $row['length_stay'],
                              'discharges' => (int) $row['discharges'],
                              'ave_length_stay' => (float) $ave_length_stay,
                              );
                
                $rowindex++;
            }    
        }  
          #print_r($data);   
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }  
