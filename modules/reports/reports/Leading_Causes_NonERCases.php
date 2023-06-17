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
            
            $age_bracket,
            
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
            AND e.encounter_class_nr IN (1)
            AND e.encounter_type IN (1)
            AND e.encounter_status NOT IN ('direct_admission')
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
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $male_total = (int) $row['male_below1'] + (int) $row['male_1to4'] + (int) $row['male_5to9']
                          + (int) $row['male_10to14'] + (int) $row['male_15to19'] +  (int) $row['male_20to44']
                          + (int) $row['male_45to59'] + (int) $row['male_60up'];
            $female_total = (int) $row['female_below1'] + (int) $row['female_1to4'] + (int) $row['female_5to9']
                          + (int) $row['female_10to14'] + (int) $row['female_15to19'] +  (int) $row['female_20to44']
                          + (int) $row['female_45to59'] + (int) $row['female_60up'];
            $total = $male_total + $female_total;
            #$grand_total = ((int) $total / (int) $overall) * 100;
            $grand_total += $total;
            
            if ($row['tab_index'])
                $tab_index = $row['tab_index'];
            else    
                $tab_index = $row['code'];
                
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'code' => $row['code'],
                              'description' => $row['description'], 
                              'male_below1' => (int) $row['male_below1'],
                              'female_below1' => (int) $row['female_below1'],
                              'male_1to4' => (int) $row['male_1to4'],
                              'female_1to4' => (int) $row['female_1to4'],
                              'male_5to9' => (int) $row['male_5to9'],
                              'female_5to9' => (int) $row['female_5to9'],
                              'male_10to14' => (int) $row['male_10to14'],
                              'female_10to14' => (int) $row['female_10to14'],
                              'male_15to19' => (int) $row['male_15to19'],
                              'female_15to19' => (int) $row['female_15to19'],
                              'male_20to44' => (int) $row['male_20to44'],
                              'female_20to44' => (int) $row['female_20to44'],
                              'male_45to59' => (int) $row['male_45to59'],
                              'female_45to59' => (int) $row['female_45to59'],
                              'male_60up' => (int) $row['male_60up'],
                              'female_60up' => (int) $row['female_60up'],
                              'male_total' => (int) $male_total,
                              'female_total' => (int) $female_total,
                              'total' => (int) $total,
                              'tab_index' => $tab_index,
                              );
                              
           $rowindex++;
        }  
          $grand_total = (int) $grand_total;
          $params->put("grand_total", $grand_total);
    }else{
        $data[0]['code'] = NULL; 
    }     