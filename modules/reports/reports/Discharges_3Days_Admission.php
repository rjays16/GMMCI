<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", "");
    
    $patient_type = '3,4';
    
    $sql = "SELECT DISTINCT p.pid AS hrn,
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',
              IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ', 
              IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS 'Full_Name',
            e.admission_dt AS 'Date_Admitted', 
            e.discharge_date AS 'Date_Discharged',
            IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
            e.er_opd_diagnosis,
            UPPER(p.sex) AS Sex,
            UPPER(IF (e.current_att_dr_nr, fn_get_personell_name(e.current_att_dr_nr),
              fn_get_personell_name(e.consulting_dr_nr))) AS 'Attending_Physician',
            res.result_desc AS 'Remarks',
            DATEDIFF(e.discharge_date,e.admission_dt)+1 AS 'Length_of_Stay'

            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN seg_encounter_result AS ser ON ser.encounter_nr = e.encounter_nr
            INNER JOIN seg_results AS res ON res.result_code=ser.result_code
            WHERE DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND e.encounter_type IN ($patient_type)
            AND e.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND (DATEDIFF(e.discharge_date,e.admission_dt)+1) <= 3
            ORDER BY DATE(e.admission_dt) ASC, p.name_last, p.name_first, p.name_middle";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            if(($row['Date_Discharged']!='0000-00-00') && ($row['Date_Discharged']!=NULL))
                $Date_Discharged = date("m/d/Y",strtotime($row['Date_Discharged']));    
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'hrn' => $row['hrn'],
                              'Full_Name' => $row['Full_Name'],
                              'Date_Admitted' => date("m/d/Y h:i A",strtotime($row['Date_Admitted'])),
                              'Date_Discharged' => $Date_Discharged,
                              'Age' => $row['Age'],
                              'Sex' => $row['Sex'],
                              'Attending_Physician' => $row['Attending_Physician'],
                              'Remarks' => $row['Remarks'],
                              'Length_of_Stay' => $row['Length_of_Stay'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['hrn'] = NULL; 
    }       