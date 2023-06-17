<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    #$params->put("department", $dept_label);
    $params->put("department", $area_type);
    $params->put("column_name",$column_name_ave);
    
    #create temp data for admission
    /*$sql_view_adm = "CREATE OR REPLACE VIEW view_admissions AS
                    SELECT DATE(e.admission_dt) AS dates,COUNT(encounter_nr) AS admission
                    FROM care_encounter e
                    WHERE DATE(e.admission_dt) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                    AND e.encounter_type IN (3,4)
                    AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
                    GROUP BY DATE(e.admission_dt)";*/
                    
    $sql_view_adm = "INSERT INTO seg_report_admission_dept(nr, dept_name,dates, admission, total_refer_from)
                        SELECT  d.nr, d.name_formal AS dept_name,
                        DATE($ave_based_date) AS dates,
                        COUNT(*) AS admissions  ,
                        SUM(CASE WHEN sed.disp_code IN (3)  THEN 1 ELSE 0 END) AS total_refer_from        
                        FROM care_encounter e
                        INNER JOIN care_person p ON p.pid=e.pid
                        LEFT JOIN care_department AS d 
                        ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
                        LEFT JOIN seg_encounter_disposition sed ON sed.encounter_nr = e.encounter_nr
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.encounter_type IN ($ave_patient_type,12) 
                        AND DATE($ave_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        GROUP BY d.name_formal#, DATE($ave_based_date)
                        ORDER BY d.name_formal";                
                   
    #create temp data for discharges
    $sql_view_disc = "INSERT INTO seg_report_discharges_dept(nr, dept_name, dates, discharges, discharges_alive,
                                                                discharges_died, discharges_noresult,total_no_days, total_refer_to)
                        SELECT d.nr, d.name_formal AS dept_name,
                        DATE(e.discharge_date) AS dates,
                        COUNT(e.encounter_nr) AS discharges,
                        SUM(CASE WHEN sr.result_code NOT IN (4,8,9,10) THEN 1 ELSE 0 END) AS discharges_alive,
                        SUM(CASE WHEN sr.result_code IN (4,8,9,10) THEN 1 ELSE 0 END) AS discharges_died,
                        SUM(CASE WHEN sr.result_code IS NULL THEN 1 ELSE 0 END) AS discharges_noresult,
                        SUM(DATEDIFF(e.discharge_date,IF(e.admission_dt, e.admission_dt, e.encounter_date))+1) AS total_no_days,
                        SUM(CASE WHEN sedr.disp_code IN (8)  THEN 1 ELSE 0 END) AS total_refer_to
                        FROM care_encounter e
                        LEFT JOIN care_department AS d 
                        ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
                        LEFT JOIN seg_encounter_result sr ON sr.encounter_nr=e.encounter_nr
                        LEFT JOIN seg_encounter_disposition_refer sedr ON sedr.encounter_nr = e.encounter_nr
                        WHERE DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."  
                        AND e.encounter_type IN ($ave_patient_type)
                        AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        GROUP BY d.name_formal#, DATE(e.discharge_date)
                        ORDER BY d.name_formal";
    
    $ok_adm = $db->Execute("TRUNCATE seg_report_admission_dept");                
    if ($ok_adm)
        $ok_adm = $db->Execute($sql_view_adm); 
    
    $ok_disc = $db->Execute("TRUNCATE seg_report_discharges_dept");                
    if ($ok_disc)    
        $ok_disc = $db->Execute($sql_view_disc);  
        
    if ($area_type == "Inpatient"){    
        $sql_trxn_prev = "INSERT INTO seg_report_prev_census_dept
                            SELECT d.nr, d.name_formal AS dept_name,
                            SUM(CASE WHEN (DATE($ave_based_date) < ".$db->qstr($from_date_format).")  THEN 1 ELSE 0 END) AS admitted,
                            SUM(CASE WHEN (DATE(e.discharge_date) < ".$db->qstr($from_date_format).")  THEN 1 ELSE 0 END) AS discharges
                            FROM care_encounter e
                            LEFT JOIN care_department AS d 
                            ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
                            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                            AND e.encounter_type IN ($ave_patient_type)
                            #AND d.nr IS NOT NULL
                            GROUP BY d.name_formal
                            ORDER BY d.name_formal";                                
        
        #$trxn_prev = $db->GetRow($sql_trxn_prev); 
        #$initial_census = $trxn_prev['admitted'] - $trxn_prev['discharges'];
    }else{
        #fixed   
        #$start_date = '2012-12-01';  #change the start date to avoid negative result in ER
        $start_date = '2011-12-01';
        $sql_trxn_prev = "INSERT INTO seg_report_prev_census_dept
                            SELECT  d.nr, d.name_formal AS dept_name,
                            SUM(CASE WHEN (DATE(e.encounter_date) 
                                BETWEEN ".$db->qstr($start_date)."
                                AND (DATE_SUB(DATE(".$db->qstr($from_date_format)."), 
                                INTERVAL 1 DAY))
                            ) THEN 1 ELSE 0 END) AS admitted,

                            SUM(CASE WHEN (DATE(e.discharge_date) 
                                BETWEEN ".$db->qstr($start_date)."
                                AND (DATE_SUB(DATE(".$db->qstr($from_date_format)."), 
                                INTERVAL 1 DAY))
                            ) THEN 1 ELSE 0 END) AS discharges

                            FROM care_encounter e
                            LEFT JOIN care_department AS d 
                                ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
                            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                            AND e.encounter_type IN ($ave_patient_type)
                            #AND d.nr IS NOT NULL
                            GROUP BY d.name_formal
                            HAVING SUM(CASE WHEN (DATE(e.encounter_date) 
                                    BETWEEN ".$db->qstr($start_date)."
                                    AND (DATE_SUB(DATE(".$db->qstr($from_date_format)."), 
                                    INTERVAL 1 DAY))
                                    ) THEN 1 ELSE 0 END) > 0
                            ORDER BY d.name_formal";                                
        
        #$trxn_prev = $db->GetRow($sql_trxn_prev); 
        #$initial_census = $trxn_prev['admitted'] - $trxn_prev['discharges'];
    } 
    
    $ok_census = $db->Execute("TRUNCATE seg_report_prev_census_dept");                
    if ($ok_census)    
        $ok_census = $db->Execute($sql_trxn_prev);  
    
    $sql = "SELECT  d.name_formal AS Type_Of_Service, 
            (pd.admitted - pd.discharges) AS initial_census,
            ad.admission AS admission,
            ed.discharges_alive AS discharges_alive,
            ed.discharges_died AS discharges_died,
            ed.discharges_noresult AS discharges_noresult,
            ed.total_no_days AS total_no_days,
            ed.dates as disc_dates,
            ad.dates as adm_dates,
            ed.total_refer_to as refer_to,
            ad.total_refer_from as refer_from
            FROM care_encounter e
            INNER JOIN care_person p ON p.pid=e.pid
            LEFT JOIN care_department AS d 
            ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            LEFT JOIN seg_report_cases_census c ON c.pid=e.pid
            LEFT JOIN seg_report_discharges_dept AS ed ON ed.nr=d.nr
            LEFT JOIN seg_report_admission_dept AS ad ON ad.nr=d.nr
            LEFT JOIN seg_report_prev_census_dept AS pd ON pd.nr=d.nr

            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($ave_patient_type) 
            AND (ad.admission > 0 OR ed.discharges > 0 OR (pd.admitted - pd.discharges) > 0)
            GROUP BY d.name_formal
            ORDER BY d.name_formal";        
    
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $adm_disc_count = 0;
    $refer_to_count = 0;
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){

        if($row['disc_dates'] != '' &&  $row['adm_dates'] != '' ){
             if($row['disc_dates'] == $row['adm_dates'])
                $adm_disc_count++;
        }

    // echo $row['refer_to']."<br>";
   
            $admissions = $row['admission'];
            $discharges =  (int) $row['discharges_alive'] + (int) $row['discharges_noresult'] +  (int) $row['discharges_died'];
            $initial_census = $row['initial_census'];
            $daily_census = ($initial_census + $admissions) - $discharges;
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'Type_Of_Service' => $row['Type_Of_Service'],
                          'initial_census' => (int) $row['initial_census'],
                          'admissions' => (int) $row['admission'],
                          'discharges_alive' => (int) $row['discharges_alive']+(int) $row['discharges_noresult'],
                          'discharges_died' => (int) $row['discharges_died'],
                          'total_no_days' => (int) $row['total_no_days'],
                          'discharges' => (int) $discharges,
                          'still_not_discharge' => 0,
                          'daily_census' => (int) $daily_census,
                          'total_same_day'=>$adm_disc_count,
                          'refer_to'=>(int)$row['refer_to'],
                          'refer_from'=>(int)$row['refer_from']
                          );
            $rowindex++;
        }  
        
          $sql_total_days = "SELECT (DATEDIFF(".$db->qstr($to_date_format).",".$db->qstr($from_date_format).")+1) AS total_days";
          $total_days = $db->GetOne($sql_total_days);
      
          $params->put("total_no_days", (int) $total_days); 
          $params->put("ft_start_date", date("m/d/Y", strtotime($start_date)));
          #print_r($data);   
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }  
// die;