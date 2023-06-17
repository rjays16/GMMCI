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
    
    $sql = "SELECT DISTINCT d.name_formal AS Type_Of_Service,
            COUNT(*) AS no_encounters
            FROM care_encounter AS e
            LEFT JOIN care_department AS d 
             ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            WHERE DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND e.encounter_type IN ($patient_type)
            AND e.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND (DATEDIFF(e.discharge_date,e.admission_dt)+1) <= 3
            GROUP BY d.name_formal
            ORDER BY d.name_formal";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                                  'Type_Of_Service' => $row['Type_Of_Service'],
                                  'no_encounters' => (int) $row['no_encounters'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Type_Of_Service'] = NULL; 
    }       