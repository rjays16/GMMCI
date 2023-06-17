<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    #$params->put("department", $dept_label);
    $params->put("department", $loc_area." (".$date_based_label.")");
    $params->put("column_name", "Municipalities/Cities");
    
    $patient_type = '3,4';
    
    /*$sql_total = "SELECT COUNT(*) AS total
                   FROM care_encounter AS e
                   INNER JOIN care_person p ON p.pid=e.pid
                   INNER JOIN care_ward w ON e.current_ward_nr=w.nr
                   LEFT JOIN seg_encounter_insurance i ON i.encounter_nr=e.encounter_nr
                   LEFT JOIN care_person_insurance pti ON pti.pid=e.pid
                   LEFT JOIN seg_encounter_memcategory em ON em.encounter_nr=e.encounter_nr
                   LEFT JOIN seg_memcategory m ON m.memcategory_id=em.memcategory_id
                   LEFT JOIN seg_municity mun ON mun.mun_nr=p.mun_nr
                   LEFT JOIN seg_provinces sp ON sp.prov_nr=mun.prov_nr
                   LEFT JOIN seg_regions sr ON sr.region_nr=sp.region_nr
                   WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                   AND e.encounter_type IN ($patient_type) 
                   AND DATE(e.admission_dt) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                   $loc_cond";
    
    $overall = $db->GetOne($sql_total);*/   
    
    $sql = "SELECT  mun.mun_name AS Districts, mun.mun_nr,p.mun_nr, sp.prov_name, sr.region_name,
            SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_non_phic,
            SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_memdep,
            SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_indigent,
            SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_phic_owwa,
            #SUM(CASE WHEN (w.accomodation_type=2) THEN 1 ELSE 0 END) AS total_pay,

            SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_non_phic,
            SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_phic_memdep,
            SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS charity_phic_indigent,
            SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS charity_phic_owwa
            #SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS total_charity,
            #SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type=2 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS total
            FROM care_encounter e
            INNER JOIN care_person p ON p.pid=e.pid
            INNER JOIN care_ward w ON e.current_ward_nr=w.nr
            LEFT JOIN seg_encounter_insurance i ON i.encounter_nr=e.encounter_nr
            LEFT JOIN care_person_insurance pti ON pti.pid=e.pid
            LEFT JOIN seg_encounter_memcategory em ON em.encounter_nr=e.encounter_nr
            LEFT JOIN seg_memcategory m ON m.memcategory_id=em.memcategory_id
            LEFT JOIN seg_municity mun ON mun.mun_nr=p.mun_nr
            LEFT JOIN seg_provinces sp ON sp.prov_nr=mun.prov_nr
            LEFT JOIN seg_regions sr ON sr.region_nr=sp.region_nr
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type) 
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            $loc_cond
            GROUP BY mun.mun_name
            ORDER BY mun.ordering, mun.mun_name, sp.prov_name";        
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $total_pay = (int) $row['pay_non_phic'] + (int) $row['pay_phic_memdep'] + (int) $row['pay_phic_indigent'] + (int) $row['pay_phic_owwa'];
            $total_charity = (int) $row['charity_non_phic'] + (int) $row['charity_phic_memdep'] + (int) $row['charity_phic_indigent'] + (int) $row['charity_phic_owwa'];
            $total = $total_pay + $total_charity;
            #$grand_total = ((int) $total / (int) $overall) * 100;
            
            /*if ($loc_area == 'All from Region XI excluding Davao del Sur')
                $place_name = trim($row['Districts']).", ".trim($row['prov_name']); 
            elseif ($loc_area == 'Outside Region XI')
                $place_name = trim($row['Districts']).", ".trim($row['prov_name']).",".trim($row['reg_name']); 
            elseif ($loc_area == 'Within and Outside of Region XI')
                $place_name = trim($row['Districts']).", ".trim($row['prov_name']).",".trim($row['reg_name']);        
            else*/
                $place_name = trim($row['Districts']);    
            
            $grand_total += $total;
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'Districts' => $place_name,
                          'pay_non_phic' => (int) $row['pay_non_phic'],
                          'pay_phic_memdep' => (int) $row['pay_phic_memdep'],
                          'pay_phic_indigent' => (int) $row['pay_phic_indigent'],
                          'pay_phic_owwa' => (int) $row['pay_phic_owwa'],
                          'total_pay' => (int) $row['total_pay'],
                          'charity_non_phic' => (int) $row['charity_non_phic'],
                          'charity_phic_memdep' => (int) $row['charity_phic_memdep'],
                          'charity_phic_indigent' => (int) $row['charity_phic_indigent'],
                          'charity_phic_owwa' => (int) $row['charity_phic_owwa'],
                          'total_charity' => (int) $row['total_charity'],
                          'total' => (int) $total,
                          );
            
           
            $rowindex++;
        }  
          $grand_total = (int) $grand_total;
          $params->put("grand_total", $grand_total);
    }else{
        $data[0]['id'] = NULL; 
    }  
