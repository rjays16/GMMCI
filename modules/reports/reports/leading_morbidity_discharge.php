<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $dept_label);
    #$params->put("area", $patient_type_label." (".$date_based_label.") from ".trim(mb_strtoupper($area)));
    $params->put("icd_class", $icd_class);
    $params->put("image_path", $image_path);
    
    /*$sql_total = "SELECT SUM(t.total) AS total FROM (SELECT c.description AS descr, 
                    ed.CODE AS subcode,  
                    COUNT(ed.CODE) AS total 
                    FROM care_encounter_diagnosis AS ed 
                    INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr 
                    INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.CODE 
                    INNER JOIN care_person AS p ON p.pid=e.pid 
                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void') 
                    AND ed.STATUS NOT IN ('deleted','hidden','inactive','void') 
                    AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                    AND ed.type_nr IN ($type_nr) 
                    AND ed.encounter_type IN ($patient_type) 
                    AND IF(INSTR(c.diagnosis_code,'.'),
                    SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
                    c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
                    GROUP BY 
                        (SELECT IF(INSTR(ed.code,'.'), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'/'), 
                                SUBSTRING(ed.code, 1, 5), 
                                    IF(INSTR(ed.code,','), 
                                    SUBSTRING(ed.code, 1, 3), 
                                        IF(INSTR(ed.code,'-'), 
                                        SUBSTRING(ed.code, 1, 3),ed.code))))) 
                    
                    ORDER BY COUNT(*) DESC LIMIT $limit ) AS t";*/
    
    #$overall = $db->GetOne($sql_total);
    
    #$base_date = 'DATE(e.admission_dt)';
    #$age_bdate = 'FLOOR((YEAR('.$base_date.') - YEAR(p.date_birth)) - (RIGHT('.$base_date.',5)<RIGHT(p.date_birth,5)))';
    
    $sql = "SELECT c.description as descr, 
            ed.code AS subcode,  
              (SELECT IF(INSTR(ed.code,'.'), 
                SUBSTRING(ed.code, 1, 3), 
                    IF(INSTR(ed.code,'/'), 
                        SUBSTRING(ed.code, 1, 5), 
                        IF(INSTR(ed.code,','), 
                            SUBSTRING(ed.code, 1, 3), 
                            IF(INSTR(ed.code,'-'), 
                            SUBSTRING(ed.code, 1, 3),ed.code))))) AS code,
            IF(t.description IS NOT NULL,t.description,                 
            (SELECT description FROM care_icd10_en ic WHERE ic.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), IF(INSTR(ed.code,'/'), 
                    SUBSTRING(ed.code, 1, 5), IF(INSTR(ed.code,','), 
                    SUBSTRING(ed.code, 1, 3), IF(INSTR(ed.code,'-'), 
                    SUBSTRING(ed.code, 1, 3),ed.code))))))) AS description, 
            
            SUM(CASE WHEN $age_bdate < 0) THEN 0 ELSE 1 END) AS count_all,
            
            t.tab_code AS tab_index
            FROM care_encounter_diagnosis AS ed 
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr 
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), 
                        IF(INSTR(ed.code,'/'), 
                            SUBSTRING(ed.code, 1, 5), 
                            IF(INSTR(ed.code,','), 
                                SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'-'), 
                                SUBSTRING(ed.code, 1, 3),ed.code))))) 
            INNER JOIN care_person AS p ON p.pid=e.pid 
            
            LEFT JOIN seg_icd_10_morbidity_tabular t ON t.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), 
                        IF(INSTR(ed.code,'/'), 
                        SUBSTRING(ed.code, 1, 5), 
                            IF(INSTR(ed.code,','), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'-'), 
                                SUBSTRING(ed.code, 1, 3),ed.code)))))
            
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type) 
            AND IF(INSTR(c.diagnosis_code,'.'),
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
            c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
            $enc_dept_cond
            GROUP BY
               IF(t.tab_code IS NOT NULL, t.tab_code, 
                (SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), 
                        IF(INSTR(ed.code,'/'), 
                        SUBSTRING(ed.code, 1, 5), 
                            IF(INSTR(ed.code,','), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'-'), 
                                SUBSTRING(ed.code, 1, 3),ed.code)))))) 
            
            ORDER BY COUNT(*) DESC LIMIT $limit ";
           
    // echo $sql; 
    // exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
      
           
        if ($rs->_numOfRows == 0) {
           $data[0]['description'] = ""; 
        }else{
          while($row=$rs->FetchRow()){
              
              $data[$rowindex] = array('rowindex' => $rowindex+1,
                                'code' => $row['code'],
                                'description' => $row['description'], 
                                'total' => $row['count_all'],
                                );
                                
             $rowindex++;
          }
        }  
    }else{
        $data[0]['code'] = NULL; 
    }     