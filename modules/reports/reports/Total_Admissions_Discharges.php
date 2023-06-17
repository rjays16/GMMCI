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
    
    $sql_view_adm = "INSERT INTO seg_report_admission
                        SELECT DATE($ave_based_date) AS dates,COUNT(encounter_nr) AS discharges
                        FROM care_encounter e
                        WHERE DATE($ave_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        AND e.encounter_type IN ($ave_patient_type)
                        AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        GROUP BY DATE($ave_based_date)";                
                    
    #create temp data for discharges
    $sql_view_disc = "INSERT INTO seg_report_discharges
                        SELECT DATE(e.discharge_date) AS dates,
                        COUNT(e.encounter_nr) AS discharges,
                        SUM(CASE WHEN sr.result_code NOT IN (4,8,9,10) THEN 1 ELSE 0 END) AS discharges_alive,
                        SUM(CASE WHEN sr.result_code IN (4,8,9,10) THEN 1 ELSE 0 END) AS discharges_died,
                        SUM(CASE WHEN sr.result_code IS NULL THEN 1 ELSE 0 END) AS discharges_noresult,
                        SUM(DATEDIFF(e.discharge_date,$ave_based_date)+1) AS total_no_days
                        FROM care_encounter e
                        LEFT JOIN seg_encounter_result sr ON sr.encounter_nr=e.encounter_nr
                        WHERE DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        AND e.encounter_type IN ($ave_patient_type)
                        AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        GROUP BY DATE(e.discharge_date)";
    
    $ok_adm = $db->Execute("TRUNCATE seg_report_admission");                
    if ($ok_adm)
        $ok_adm = $db->Execute($sql_view_adm); 
    
    $ok_disc = $db->Execute("TRUNCATE seg_report_discharges");                
    if ($ok_disc)    
        $ok_disc = $db->Execute($sql_view_disc);                              
        
    $sql = "SELECT date as dates FROM dates
            WHERE date BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ORDER BY date";        
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $qry_admission = "SELECT admission
                              FROM seg_report_admission
                              WHERE dates=".$db->qstr($row['dates']);                  
            $admissions = $db->GetOne($qry_admission);
            
            $qry_discharged = "SELECT discharges, total_no_days
                              FROM seg_report_discharges
                              WHERE dates=".$db->qstr($row['dates']);
            $discharges = $db->GetRow($qry_discharged);
            
            $daily_census = ((int) $initial_census + (int) $admissions) - (int) $discharges['total_no_days'];
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'dates' => date("m/d/Y",strtotime($row['dates'])),
                          'admissions' => (int) $admissions,
                          'discharges' => (int) $discharges['discharges'],
                          'total_no_days' => (int) $discharges['total_no_days'],
                          );
            
            $rowindex++;
        }  
          #print_r($data);   
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }  
