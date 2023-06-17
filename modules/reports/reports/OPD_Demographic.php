<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    #$params->put("department", $dept_label);
    
    if ($field_with_municity){
        $department = "Municipalities/Cities";
        $column_name = "Municipalities/Cities";
        $fields = "mun.mun_name AS Districts, mun.mun_nr,p.mun_nr, sp.prov_name, sr.region_name,"; 
        $group_order = "GROUP BY mun.mun_name
                        ORDER BY mun.ordering, mun.mun_name, sp.prov_name"; 
    }else{
        $department = "By Province";
        $column_name = "Provinces";
        $fields = "sp.prov_name AS Districts, sp.prov_nr, sr.region_name,";
        $group_order = "GROUP BY sp.prov_name
                        ORDER BY sp.ordering, sp.prov_name"; 
    }    
    
    #$params->put("department", $department);
    $params->put("department", $loc_area." (".$department.")");
    $params->put("column_name", $column_name);
    
    $patient_type = '2';
    
     $sql_view_discount = "INSERT INTO seg_report_charity_discount
                            SELECT e.pid, SUBSTRING(MAX(CONCAT(soc.grant_dte,soc.discountid)),20) AS discountid
                            FROM seg_charity_grants_pid soc
                            INNER JOIN care_encounter e ON e.pid=soc.pid
                            WHERE DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."  
                            GROUP BY e.pid
                            HAVING SUBSTRING(MAX(CONCAT(soc.grant_dte,soc.discountid)),20)='D'
                            OR SUBSTRING(MAX(CONCAT(soc.grant_dte,soc.discountid)),20) IN 
                            (SELECT discountid FROM seg_discount d WHERE d.parentid='D')"; 
                        
     $ok_discount = $db->Execute("TRUNCATE seg_report_charity_discount");                
     if ($ok_discount)
        $ok_discount = $db->Execute($sql_view_discount);                      
    
    /*$sql_total = "SELECT COUNT(*)
                    FROM care_encounter e
                    INNER JOIN care_person p ON p.pid=e.pid
                    LEFT JOIN seg_encounter_insurance i ON i.encounter_nr=e.encounter_nr
                    LEFT JOIN care_person_insurance pti ON pti.pid=e.pid
                    LEFT JOIN seg_encounter_memcategory em ON em.encounter_nr=e.encounter_nr
                    LEFT JOIN seg_memcategory m ON m.memcategory_id=em.memcategory_id
                    LEFT JOIN seg_municity mun ON mun.mun_nr=p.mun_nr
                    LEFT JOIN seg_provinces sp ON sp.prov_nr=mun.prov_nr
                    LEFT JOIN seg_regions sr ON sr.region_nr=sp.region_nr
                    LEFT JOIN  seg_charity_grants_pid soc ON soc.pid=e.pid
                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                    AND e.encounter_type IN ($patient_type) 
                    AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format);
    
    $overall = $db->GetOne($sql_total);*/   
    
    $sql = "SELECT  $fields
            SUM(CASE WHEN (soc.discountid IS NULL) THEN 1 ELSE 0 END) AS pay_non_phic,
            0 AS pay_phic_memdep,
            0 AS pay_phic_indigent,
            0 AS pay_phic_owwa,
            SUM(CASE WHEN (soc.discountid IS NOT NULL) THEN 1 ELSE 0 END) AS charity_non_phic,
            0 AS charity_phic_memdep,
            0 AS charity_phic_indigent,
            0 AS charity_phic_owwa
            FROM care_encounter e
            INNER JOIN care_person p ON p.pid=e.pid
            LEFT JOIN seg_municity mun ON mun.mun_nr=p.mun_nr
            LEFT JOIN seg_provinces sp ON sp.prov_nr=mun.prov_nr
            LEFT JOIN seg_regions sr ON sr.region_nr=sp.region_nr
            LEFT JOIN  seg_report_charity_discount soc ON soc.pid=e.pid
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type) 
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            $loc_cond
            $group_order";
    
    /*$sql = "SELECT  sp.prov_name AS Districts, mun.mun_nr,p.mun_nr, mun.mun_name, sr.region_name,
            SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (soc.discountid!='D' OR soc.discountid IS NULL) THEN 1 ELSE 0 END) AS pay_non_phic,
            SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) AND (soc.discountid!='D' OR soc.discountid IS NULL) THEN 1 ELSE 0 END) AS pay_phic_memdep,
            SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (soc.discountid!='D' OR soc.discountid IS NULL) THEN 1 ELSE 0 END) AS pay_phic_indigent,
            SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) AND (soc.discountid!='D' OR soc.discountid IS NULL) THEN 1 ELSE 0 END) AS pay_phic_owwa,
            
            SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (soc.discountid='D') THEN 1 ELSE 0 END) AS charity_non_phic,
            SUM(CASE WHEN i.hcare_id=18 AND (em.memcategory_id IN (1,2,4,6,8) OR em.memcategory_id IS NULL) AND (soc.discountid='D') THEN 1 ELSE 0 END) AS charity_phic_memdep,
            SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id=5 AND (soc.discountid='D') THEN 1 ELSE 0 END) AS charity_phic_indigent,
            SUM(CASE WHEN i.hcare_id=18 AND em.memcategory_id IN (3,7) AND (soc.discountid='D') THEN 1 ELSE 0 END) AS charity_phic_owwa
            
            FROM care_encounter e
            INNER JOIN care_person p ON p.pid=e.pid
            LEFT JOIN seg_encounter_insurance i ON i.encounter_nr=e.encounter_nr
            LEFT JOIN care_person_insurance pti ON pti.pid=e.pid
            LEFT JOIN seg_encounter_memcategory em ON em.encounter_nr=e.encounter_nr
            LEFT JOIN seg_memcategory m ON m.memcategory_id=em.memcategory_id
            LEFT JOIN seg_municity mun ON mun.mun_nr=p.mun_nr
            LEFT JOIN seg_provinces sp ON sp.prov_nr=mun.prov_nr
            LEFT JOIN seg_regions sr ON sr.region_nr=sp.region_nr
            LEFT JOIN  seg_charity_grants_pid soc ON soc.pid=e.pid
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type) 
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            GROUP BY sp.prov_name
            ORDER BY sp.prov_name";*/
           
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
