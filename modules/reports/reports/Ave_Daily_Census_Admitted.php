<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    #$params->put("department", $dept_label);
    if ($dept_label)
        $dept_add = "(".$dept_label.")";
    $params->put("department", $area_type." ".$dept_add);
    $params->put("column_name",$column_name_ave);
    
    #create temp data for admission
    $sql_view_adm = "INSERT INTO seg_report_admission
                        SELECT DATE($ave_based_date) AS dates,COUNT(encounter_nr) AS discharges
                        FROM care_encounter e
                        WHERE DATE($ave_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        AND e.encounter_type IN ($ave_patient_type)
                        AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        $census_dept_cond
                        GROUP BY DATE($ave_based_date)";                
                    
    #create temp data for discharges
    $sql_view_disc = "INSERT INTO seg_report_discharges
                        SELECT DATE(e.discharge_date) AS dates,
                        COUNT(e.encounter_nr) AS discharges,
                        SUM(CASE WHEN sr.result_code NOT IN (4,8,9,10) THEN 1 ELSE 0 END) AS discharges_alive,
                        SUM(CASE WHEN sr.result_code IN (4,8,9,10) THEN 1 ELSE 0 END) AS discharges_died,
                        SUM(CASE WHEN sr.result_code IS NULL THEN 1 ELSE 0 END) AS discharges_noresult,
                        SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_no_days
                        FROM care_encounter e
                        LEFT JOIN seg_encounter_result sr ON sr.encounter_nr=e.encounter_nr
                        WHERE DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        AND e.encounter_type IN ($ave_patient_type)
                        AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        $census_dept_cond
                        GROUP BY DATE(e.discharge_date)";
    
    $ok_adm = $db->Execute("TRUNCATE seg_report_admission");                
    if ($ok_adm)
        $ok_adm = $db->Execute($sql_view_adm); 
    
    $ok_disc = $db->Execute("TRUNCATE seg_report_discharges");                
    if ($ok_disc)    
        $ok_disc = $db->Execute($sql_view_disc);  
    
    if ($area_type == "Inpatient"){    
        $sql_trxn_prev = "SELECT SUM(CASE WHEN (DATE($ave_based_date) < ".$db->qstr($from_date_format).")  THEN 1 ELSE 0 END) AS admitted,
                            SUM(CASE WHEN (DATE(e.discharge_date) < ".$db->qstr($from_date_format).")  THEN 1 ELSE 0 END) AS discharges
                            FROM care_encounter e
                            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                            $census_dept_cond
                            AND e.encounter_type IN ($ave_patient_type)";                                
        
        $trxn_prev = $db->GetRow($sql_trxn_prev); 
        $initial_census = $trxn_prev['admitted'] - $trxn_prev['discharges'];
    }else{
        #fixed   
        $start_date = '2012-12-01';
        $sql_trxn_prev = "SELECT SUM(CASE WHEN (DATE(e.encounter_date) 
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
                            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                            $census_dept_cond
                            AND e.encounter_type IN ($ave_patient_type)";                                
        
        $trxn_prev = $db->GetRow($sql_trxn_prev); 
        $initial_census = $trxn_prev['admitted'] - $trxn_prev['discharges'];
    }    
        
    $sql = "SELECT date as dates FROM dates
            WHERE date BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ORDER BY date";        
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        #truncate table seg_report_ipd_census
        $db->Execute("TRUNCATE seg_report_ipd_census");
        while($row=$rs->FetchRow()){
            #no. of admission
            $qry_admission = "SELECT admission
                              FROM seg_report_admission
                              WHERE dates=".$db->qstr($row['dates']);                  
            $admissions = $db->GetOne($qry_admission);
            
            #no. of discharges
            $qry_discharged = "SELECT discharges_alive, discharges_died, discharges_noresult
                              FROM seg_report_discharges
                              WHERE dates=".$db->qstr($row['dates']);
            $info = $db->GetRow($qry_discharged);
            
            $discharges =  (int) $info['discharges_alive'] + (int) $info['discharges_noresult'] +  (int) $info['discharges_died'];
            
            $daily_census = ($initial_census + $admissions) - $discharges;
            
            #save in table 
            $db->Execute("INSERT INTO seg_report_ipd_census VALUES(".$db->qstr($row['dates']).",".$db->qstr($daily_census).")"); 
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'dates' => date("m/d/Y",strtotime($row['dates'])),
                          'initial_census' => (int) $initial_census,
                          'admissions' => (int) $admissions,
                          'discharges_alive' => (int) $info['discharges_alive']+(int) $info['discharges_noresult'],
                          'discharges_died' => (int) $info['discharges_died'],
                          'discharges' => (int) $discharges,
                          'still_not_discharge' => 0,
                          'daily_census' => (int) $daily_census,
                          );
            $initial_census = $daily_census;              
           
            $rowindex++;
        }  
          #print_r($data);   
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }  
