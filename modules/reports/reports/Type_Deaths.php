<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Neonatal Mortality Including > 1 year old');
    
    $patient_type = '3,4';
    
    $sql_fetal = "SELECT SUM(CASE WHEN (birth_weight < 500) THEN 1 ELSE 0 END) AS fetal_less22,
                        SUM(CASE WHEN (birth_weight >= 500) THEN 1 ELSE 0 END) AS fetal_22above
                        FROM seg_cert_death_fetal f
                        INNER JOIN care_person p ON p.pid=f.pid
                        WHERE DATE(p.death_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format);                                
    
    $fetal = $db->GetRow($sql_fetal); 
    
    #$dept = "AND IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN ('191','193')";
    $dept = '';
    $sql_neonatal = "SELECT SUM(CASE WHEN (((DATEDIFF(p.death_date,p.date_birth)) < 3) AND p.fromtemp=1 AND sr.result_code IN (4,8,9,10))  THEN 1 ELSE 0 END) AS neonatal_p1,
                        SUM(CASE WHEN ((DATEDIFF(p.death_date,p.date_birth)) BETWEEN 3 AND 28 AND p.fromtemp=1 AND sr.result_code IN (4,8,9,10)) THEN 1 ELSE 0 END) AS neonatal_p2,
                        SUM(CASE WHEN ((DATEDIFF(p.death_date,p.date_birth)) BETWEEN 29 AND 364 AND p.fromtemp=1 AND sr.result_code IN (4,8,9,10)) THEN 1 ELSE 0 END) AS neonatal_p3,
                        SUM(CASE WHEN ((DATEDIFF(p.death_date,p.date_birth)) > 365 AND p.fromtemp=1 AND sr.result_code IN (4,8,9,10)) THEN 1 ELSE 0 END) AS infant,
                        SUM(CASE WHEN (((DATEDIFF(p.death_date,p.date_birth)) < 3) AND p.fromtemp=0 AND sr.result_code IN (4,8,9,10))  THEN 1 ELSE 0 END) AS neonatal_p1_outside,
                        SUM(CASE WHEN ((DATEDIFF(p.death_date,p.date_birth)) BETWEEN 3 AND 28 AND p.fromtemp=0 AND sr.result_code IN (4,8,9,10)) THEN 1 ELSE 0 END) AS neonatal_p2_outside,
                        SUM(CASE WHEN ((DATEDIFF(p.death_date,p.date_birth)) BETWEEN 29 AND 364 AND p.fromtemp=0 AND sr.result_code IN (4,8,9,10)) THEN 1 ELSE 0 END) AS neonatal_p3_outside,
                        SUM(CASE WHEN ((DATEDIFF(p.death_date,p.date_birth)) > 365 AND p.fromtemp=0 AND sr.result_code IN (4,8,9,10)) THEN 1 ELSE 0 END) AS infant,
                        SUM(CASE WHEN (FLOOR(IF(fn_calculate_age(DATE(e.discharge_date),p.date_birth),(fn_get_ageyr(DATE(e.discharge_date),p.date_birth)),p.age)) <= 5) THEN 1 ELSE 0 END) AS total_infant
                        FROM care_department AS d
                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid AND p.death_encounter_nr=e.encounter_nr
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.discharge_date IS NOT NULL
                        AND e.encounter_type IN (3,4)
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        AND (IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN (125)
                        OR IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN ( 
                        SELECT nr FROM care_department AS d WHERE d.parent_dept_nr IN (125))) ";
    
    $neonatal = $db->GetRow($sql_neonatal); 
    
    $sql_er_death = "SELECT SUM(CASE WHEN sr.result_code IN (4,8,9,10) THEN 1 ELSE 0 END) AS er_death,
                        SUM(CASE WHEN e.is_discharged=1 THEN 1 ELSE 0 END) AS total_er_discharges
                        FROM care_encounter e
                        LEFT JOIN seg_encounter_result sr ON sr.encounter_nr=e.encounter_nr
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format) ."
                        AND e.encounter_type IN (1)";
    
    $er = $db->GetRow($sql_er_death); 
    
    $sql_doa = "SELECT COUNT(*) AS doa
                        FROM care_encounter e
                        INNER JOIN care_person p ON p.pid=e.pid #AND p.death_encounter_nr=e.encounter_nr
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format) ."
                        AND e.encounter_type IN (1)
                        AND e.is_DOA = 1";
    
    $doa = $db->GetOne($sql_doa);
    
    $sql_death = "SELECT 
                        SUM(CASE WHEN e.is_discharged=1 THEN 1 ELSE 0 END) AS total_discharges,
                        SUM(CASE WHEN (sr.result_code IN (4,8,9,10)) THEN 1 ELSE 0 END) AS total_death,
                        SUM(CASE WHEN ( (sr.result_code IN (4,8,9,10)) AND (DATEDIFF((IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)), DATE(e.admission_dt))<2))THEN 1 ELSE 0 END) AS death_less48H
                        FROM care_encounter e
                        INNER JOIN care_person p ON p.pid=e.pid #and p.death_encounter_nr=e.encounter_nr
                        LEFT JOIN seg_encounter_result sr ON sr.encounter_nr=e.encounter_nr
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format) ." 
                        AND e.encounter_type IN (3,4)";                                
    
    $death = $db->GetRow($sql_death); 
    
    $sql_total_birth = "SELECT COUNT(*) AS total_birth
                        FROM care_encounter_diagnosis AS ed
                        INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
                        INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.CODE
                        INNER JOIN care_person AS p ON p.pid=e.pid
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND ed.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND ed.encounter_type IN (3,4)
                        AND DATE(e.discharge_date) BETWEEN '2013-01-01' AND '2013-04-30' 
                        AND ed.CODE IN ('Z37.0','Z37.1','Z37.2','Z37.3','Z37.4')";                                
    
    $total_birth = $db->GetOne($sql_total_birth);
    
    
    
    $data[$rowindex] = array( 'below22' => (int) $fetal['fetal_less22'],
                              'above22' => (int) $fetal['fetal_22above'],
                              'neonatal1' => (int) $neonatal['neonatal_p1'],
                              'neonatal2' => (int) $neonatal['neonatal_p2'],
                              'neonatal3' => (int) $neonatal['neonatal_p3'],
                              'infant' => (int) $neonatal['infant'],
                              'neonatal1_outside' => (int) $neonatal['neonatal_p1_outside'],
                              'neonatal2_outside' => (int) $neonatal['neonatal_p2_outside'],
                              'neonatal3_outside' => (int) $neonatal['neonatal_p3_outside'],
                              'infant_outside' => (int) $neonatal['infant_outside'],
                              'maternal_death' => 0,
                              'early' => 0,
                              'early_direct' => 0,
                              'early_indirect' => 0,
                              'late' => 0,
                              'late_direct' => 0,
                              'late_indirect' => 0,
                              'post_death' => 0,
                              'er_death' => (int) $er['er_death'],
                              'doa' => (int) $doa,
                              'total_death' => (int) $death['total_death'],
                              'death_less48H' => (int) $death['death_less48H'],
                              'total_live_stillbirth' => (int) $total_birth,
                              'total_infant' => (int) $neonatal['total_infant'],
                              'total_discharges' => (int) $death['total_discharges'],
                              'total_er_discharges' => (int) $er['total_er_discharges'],
                              );