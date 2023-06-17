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
    
    $sql = "SELECT dt.ops_code AS CODE,
            c.description, dt.rvu,
            SUM(CASE WHEN ins.hcare_id=18 THEN 1 ELSE 0 END) AS phic_occurrence,
            SUM(CASE WHEN ins.hcare_id<>18 OR ins.hcare_id IS NULL THEN 1 ELSE 0 END) AS nonphic_occurrence,
            COUNT(dt.ops_code) AS occurrence
            FROM  seg_misc_ops AS ed
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
            INNER JOIN seg_misc_ops_details AS dt ON dt.refno=ed.refno
            INNER JOIN seg_ops_rvs AS c ON c.CODE=dt.ops_code
            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND DATE(e.admission_dt) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            $cond_surgery
            AND e.encounter_type IN ($patient_type)
            GROUP BY dt.ops_code
            ORDER BY COUNT(dt.ops_code) DESC";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $occurrence = (int) $row['phic_occurrence'] + (int) $row['nonphic_occurrence'];
            $grand_total += $occurrence;
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'CODE' => $row['CODE'],
                              'rvu' => $row['rvu'],
                              'description' => $row['description'],
                              'phic_occurrence' => (int) $row['phic_occurrence'],
                              'nonphic_occurrence' => (int) $row['nonphic_occurrence'],
                              'occurrence' => (int) $occurrence,
                              );
                              
           $rowindex++;
        }  
        
          $grand_total = (int) $grand_total;
          $params->put("grand_total", $grand_total);
          #print_r($data);
    }else{
        $data[0]['CODE'] = NULL; 
    }       