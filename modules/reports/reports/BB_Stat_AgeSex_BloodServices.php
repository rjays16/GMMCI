<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
        
    $sql = "SELECT d.component as bestellnum, c.long_name as artikelname, 
             SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5))))<=0) 
                  THEN 1 ELSE 0 END) AS male_below1, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5))))<=0) 
                  THEN 1 ELSE 0 END) AS female_below1, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - (
                  RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN  1 AND 4) THEN 1 ELSE 0 END) AS male_1to4, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - (
                  RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN  1 AND 4) THEN 1 ELSE 0 END) AS female_1to4, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN  5 AND 9) THEN 1 ELSE 0 END) AS male_5to9, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN  5 AND 9) THEN 1 ELSE 0 END) AS female_5to9, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS male_10to14, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS female_10to14, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS male_15to19, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS female_15to19, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 20 AND 44) THEN 1 ELSE 0 END) AS male_20to44, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 20 AND 44) THEN 1 ELSE 0 END) AS female_20to44, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 45 AND 59) THEN 1 ELSE 0 END) AS male_45to59, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 45 AND 59) THEN 1 ELSE 0 END) AS female_45to59, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5))))>=60) 
                  THEN 1 ELSE 0 END) AS male_60up, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.serv_dt)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.serv_dt),5)<RIGHT(p.date_birth,5))))>=60) 
                  THEN 1 ELSE 0 END) AS female_60up, 
              SUM(CASE WHEN p.sex='m' THEN 1 ELSE 0 END) AS male_total, 
              SUM(CASE WHEN p.sex='f' THEN 1 ELSE 0 END) AS female_total, COUNT(h.refno) AS total
            
             FROM seg_blood_received_details d
             INNER JOIN seg_blood_component c ON c.id=d.component
             INNER JOIN seg_lab_serv h ON h.refno=d.refno
             INNER JOIN care_person p ON p.pid=h.pid
             WHERE DATE(h.serv_dt) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
             AND h.ref_source='BB'
             AND h.STATUS NOT IN ('deleted','hidden','inactive','void')
             AND d.STATUS = 'received'
             GROUP BY c.id
             ORDER BY COUNT(d.refno) DESC";        
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $total_row = $row['male_total'] + $row['female_total'];
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'bestellnum' => $row['bestellnum'],
                          'artikelname' => $row['artikelname'], 
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
                          'male_total' => (int) $row['male_total'],
                          'female_total' => (int) $row['female_total'],
                          'total' => (int) $row['total']
                          );
                          
           $grand_total += $row['total'];
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }  
