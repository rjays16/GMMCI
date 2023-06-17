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
    $params->put("column_code","ICPM CODE");
    
    $patient_type = '3,4';
    
    $sql = "SELECT ed.CODE AS CODE,
            COUNT(ed.code_parent) AS occurrence, c.description,
            SUM(CASE WHEN ins.hcare_id=18 THEN 1 ELSE 0 END) AS phic_occurrence,
            SUM(CASE WHEN ins.hcare_id<>18 OR ins.hcare_id IS NULL THEN 1 ELSE 0 END) AS nonphic_occurrence
            FROM  care_encounter_procedure AS ed
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
            INNER JOIN care_ops301_en AS c ON c.CODE=ed.CODE
            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type)
            AND IF(INSTR(c.CODE,'.'),SUBSTR(c.CODE,1,IF(INSTR(c.CODE,'.'),INSTR(c.CODE,'.')-1,0)),c.CODE)
            REGEXP '^[[:digit:]]-[[:digit:]]'
            GROUP BY ed.CODE
            ORDER BY COUNT(ed.CODE) DESC";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $occurrence = (int) $row['phic_occurrence'] + (int) $row['nonphic_occurrence'];
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'CODE' => $row['CODE'],
                              'occurrence' => (int) $occurrence,
                              'description' => $row['description'],
                              'phic_occurrence' => (int) $row['phic_occurrence'],
                              'nonphic_occurrence' => (int) $row['nonphic_occurrence'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['CODE'] = NULL; 
    }       