<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Distribution of Beds and Bed Occupancy Rate');
    
    $patient_type = '3,4';
    
    #no of admissions
    $sql_census = "SELECT SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." )  THEN 1 ELSE 0 END) AS admitted,
                    SUM(CASE WHEN (DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." )  THEN 1 ELSE 0 END) AS discharges,
                    (DATEDIFF(".$db->qstr($to_date_format).",".$db->qstr($from_date_format).")+1) AS total_days
                    FROM care_encounter e
                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                    AND e.encounter_type IN ($patient_type)";
    $census = $db->GetRow($sql_census);
    
    #get census
    $sql_initial_census = "SELECT SUM(initial_census) AS census
                            FROM seg_report_ipd_census";
    $initial_census = $db->GetOne($sql_initial_census);        
    
    $sql = "SELECT a.dept_name AS Type_Of_Service, 
            a.pay_allocated_bed AS alloc_pay, 
            a.service_allocated_bed AS alloc_service,
            SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS nphic_pay, 
            SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS nphic_service, 
            SUM(CASE WHEN i.hcare_id=18 AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS phic_pay, 
            SUM(CASE WHEN i.hcare_id=18 AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS phic_service,
            SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_ipd_days
            FROM seg_report_dept_bed_allocation a
            INNER JOIN care_encounter e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN (a.dept_nr)
            LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr 
            LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
            LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type) 
            #AND e.in_ward=1
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            GROUP BY a.id
            ORDER BY ordering";
    
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $alloc_total = (int) $row['alloc_pay'] + (int) $row['alloc_service'];
            $total_pay = (int) $row['nphic_pay'] + (int) $row['phic_pay'];
            $total_service = (int) $row['nphic_service'] + (int) $row['phic_service'];
            $total = (int) $total_pay + (int) $total_service;
            
            $bor = (float) (($row['total_ipd_days']/($alloc_total * $census['total_days'])) * 100);
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'Type_Of_Service' => $row['Type_Of_Service'], 
                          'alloc_pay' => (int) $row['alloc_pay'],
                          'alloc_service' => (int) $row['alloc_service'],
                          'alloc_total' => (int) $alloc_total,
                          'nphic_pay' => (int) $row['nphic_pay'],
                          'nphic_service' => (int) $row['nphic_service'],
                          'phic_pay' => (int) $row['phic_pay'],
                          'phic_service' => (int) $row['phic_service'],
                          'total_pay' => $total_pay,
                          'total_service' =>  $total_service,
                          'total' => (int) $total,
                          'total_ipd_days' => (int) $row['total_ipd_days'],
                          'bor' => $bor,
                          );
            
            $rowindex++;
        }  
          $params->put("total_admitted", (int) $census['admitted']);
          $params->put("total_discharges", (int) $census['discharges']);
          $params->put("total_days", (int) $census['total_days']);
          $params->put("total_initial_census", (int) $initial_census);
          
    }else{
        $data[0]['id'] = NULL; 
    }  
