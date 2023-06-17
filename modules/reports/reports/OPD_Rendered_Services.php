<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", '');
    $params->put("image_path", $image_path);
    
    $patient_type = '2';
    
     $sql_view_cases = "INSERT INTO seg_report_cases_census
                            SELECT e.pid, COUNT(*) AS no_encounters
                            FROM care_encounter e
                            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                            AND e.encounter_type IN ($patient_type) 
                            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                            GROUP BY e.pid"; 
                        
     $ok_cases = $db->Execute("TRUNCATE seg_report_cases_census");                
     if ($ok_cases)
        $ok_cases = $db->Execute($sql_view_cases);
        
    #no of patients
    $sql_no_patient = "SELECT COUNT(*) AS no_patients FROM seg_report_cases_census";                          
    $no_patients = $db->GetOne($sql_no_patient);
    
    #no of patients admitted from opd
    $sql_no_patient_ipdopd = "SELECT COUNT(*) AS no_patient_ipdopd
                                FROM care_person p
                                INNER JOIN care_encounter e ON e.pid=p.pid
                                LEFT JOIN care_department AS d 
                                ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
                                LEFT JOIN seg_report_cases_census c ON c.pid=e.pid
                                WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                AND e.encounter_type IN (4) 
                                AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format);
    $no_patient_ipdopd = $db->GetOne($sql_no_patient_ipdopd);                            
    
    $sql_no_weekdays = "SELECT 5 * (DATEDIFF(".$db->qstr($to_date_format).", ".$db->qstr($from_date_format).") DIV 7) + 
                        MID('0123444401233334012222340111123400012345001234550', 7 * WEEKDAY(".$db->qstr($from_date_format).") + WEEKDAY(".$db->qstr($to_date_format).") + 1, 1) AS no_weekdays";                                
    $no_weekdays = $db->GetOne($sql_no_weekdays);
    
    $sql = "SELECT  d.name_formal AS Type_Of_Service, 
            SUM(CASE WHEN c.no_encounters <= 1 THEN 1 ELSE 0 END) AS new_patient,
            SUM(CASE WHEN c.no_encounters > 1 THEN 1 ELSE 0 END) AS revisit            
            FROM care_encounter e
            INNER JOIN care_person p ON p.pid=e.pid
            LEFT JOIN care_department AS d 
              ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            LEFT JOIN seg_report_cases_census c ON c.pid=e.pid
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type) 
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            GROUP BY d.name_formal
            ORDER BY d.name_formal";
    
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $total = (int) $row['new_patient'] + (int) $row['revisit'];
            $grand_total += $total;
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'Type_Of_Service' => $row['Type_Of_Service'], 
                          'new' => (int) $row['new_patient'],
                          'revisit' => (int) $row['revisit'],
                          'total' => (int) $total,
                          );
            
           
            $rowindex++;
        }  
          #$grand_total = (int) $grand_total;
          $params->put("grand_total", (int) $grand_total);
          $params->put("no_weekdays", (int) $no_weekdays);
          $params->put("no_holidays", (int) $no_holidays);
          $params->put("no_patients", (int) $no_patients);
          $params->put("no_patient_ipdopd", (int) $no_patient_ipdopd);
    }else{
        $data[0]['id'] = NULL; 
    }  



    $opd_patient = "SELECT COUNT(e.pid) AS opd_patient  
                        FROM care_department AS d
                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.discharge_date IS NOT NULL
                        AND e.encounter_type IN ($patient_type)
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
                        GROUP BY e.pid
                        ";

    $pedia = "SELECT sum(CASE WHEN $age_bdate <= 13) THEN 1 ELSE 0 END) AS pedia_opd  
                        FROM care_department AS d
                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.discharge_date IS NOT NULL
                        AND e.encounter_type IN ($patient_type)
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
                       
                        ";

    $adult = "SELECT sum(CASE WHEN $age_bdate >= 14) THEN 1 ELSE 0 END) AS adult_opd  
                        FROM care_department AS d
                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.discharge_date IS NOT NULL
                        AND e.encounter_type IN ($patient_type)
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
                       
                        ";


    $medical = "SELECT sum(CASE WHEN d.nr=241 OR d.nr=212 THEN 1 ELSE 0 END) AS medical
                        FROM care_department AS d
                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.discharge_date IS NOT NULL
                        AND e.encounter_type IN ($patient_type)
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
                       
                        ";



     $surgical_opd = "SELECT
                        count(e.pid) AS surgical_opd
                        FROM care_encounter_procedure AS ed 
                        INNER JOIN care_encounter AS e ON  e.encounter_nr=ed.encounter_nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.discharge_date IS NOT NULL
                        AND e.encounter_type IN ($patient_type)
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        GROUP BY e.encounter_nr";


    $non_surgical_opd = "SELECT
                        count(e.pid) AS non_surgical_opd
                        FROM care_encounter_diagnosis AS ed 
                        INNER JOIN care_encounter AS e ON  e.encounter_nr=ed.encounter_nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.discharge_date IS NOT NULL
                        AND e.encounter_type IN ($patient_type)
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        GROUP BY e.encounter_nr";

    
    //echo $opd_patient."<br><br>".$pedia."<br><br>".$adult."<br><br>".$surgical_opd."<br><br>".$non_surgical_opd."<br><br>".$medical."<br><br>";

    $total_new_patient = 0;
    $total_revisit = 0;
    $data = array();

    $rs = $db->Execute($opd_patient);
    
    $data[0]['for_output'] = 1;

    if (is_object($rs)){
        if ($rs->_numOfRows == 0) {
            $total_new_patient = 0;
            $total_revisit = 0;
        }else{
            while($row=$rs->FetchRow()){
                if ($row['opd_patient'] == 1 ) {
                    $total_new_patient = $total_new_patient+=$row['opd_patient'];
                }else{
                    $total_revisit = $total_revisit+=$row['opd_patient'];
                }
            }  
        }
    }

    $adult = $db->GetOne($adult);
    $pedia = $db->GetOne($pedia);
    $medical = $db->GetOne($medical);
    $surgical_opd = $db->GetOne($surgical_opd);
    $non_surgical_opd = $db->GetOne($non_surgical_opd);

    if ($adult == null && $pedia == null && $medical == null) {
        $adult = 0;
        $pedia = 0;
        $medical = 0;
    }elseif($pedia == null ){
        $pedia = 0;
    }elseif($adult == null ){
        $adult = 0;
    }elseif($medical == null){
        $medical = 0;
    }

    $params->put("total_new_patient",strval($total_new_patient));
    $params->put("total_revisit",strval($total_revisit));
    $params->put("adult",strval($adult));
    $params->put("pedia",strval($pedia));
    $params->put("medical",strval($medical));
    $params->put("surgical_opd",strval($surgical_opd));
    $params->put("non_surgical_opd",strval($non_surgical_opd));

    // echo $total_new_patient;
    // echo $total_revisit;
    // echo $adult;
    // echo $pedia;
    // echo $medical;
    // echo $surgical_opd;
    // echo $non_surgical_opd;

    //exit();