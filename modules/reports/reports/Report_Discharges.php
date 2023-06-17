<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $sub_caption);
        
    $patient_type = '3,4';
    $date_based = 'e.discharge_date';
    
    $sql = "SELECT DISTINCT 
            e.`discharge_date`,
            e.`discharge_time`,
            e.pid AS hrn,
            e.encounter_nr AS 'Case_No',
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',
            IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ',
            IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS 'Full_Name',
            e.admission_dt AS 'Date_Admitted', 
            CONCAT(e.discharge_date, ' ', e.discharge_time) AS 'Date_Discharged',
            IF(p.date_birth!='0000-00-00',p.date_birth,NULL) AS date_birth,
            IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
            e.er_opd_diagnosis,
            UPPER(p.sex) AS Sex,
            res.result_desc AS 'Remarks',
            IF(p.fromtemp, 'Newborn (Born Alive)', d.name_formal) AS department,
            e.received_date, ins.hcare_id, IF(ins.hcare_id=18,'P','NP') AS insurance

            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_department AS d 
                ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            LEFT JOIN seg_encounter_result AS ser ON ser.encounter_nr = e.encounter_nr
            INNER JOIN seg_results AS res ON res.result_code=ser.result_code
            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND e.encounter_type IN ($patient_type)
            $cond_classification
            $cond_mode_chart
            $cond_status
            $enc_dept_cond
            ORDER BY CONCAT(
            e.`discharge_date`,
            e.`discharge_time`
          ) DESC ";
           
    // echo $sql; 
    // exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            if(($row['date_birth']!='0000-00-00') && ($row['date_birth']!=NULL))
                $date_birth = date("m/d/Y",strtotime($row['date_birth']));
                
            if(($row['Date_Discharged']!='0000-00-00') && ($row['Date_Discharged']!=NULL))
                $Date_Discharged = date("m/d/Y",strtotime($row['Date_Discharged']));    
                
            if(($row['Date_Received']!='0000-00-00') && ($row['Date_Received']!=NULL))
                $Date_Received = date("m/d/Y",strtotime($row['Date_Received']));        
            else
                $Date_Received = 'Not yet';
                
            if ($row['insurance']=='P')    
                $insurance = 'Yes';
            else    
                $insurance = 'None';
                

    $discharge_time_f = date('g:i A', strtotime($row['discharge_time']));
    

            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'hrn' => $row['hrn'],
                              'Case_No' => $row['Case_No'],
                              'Full_Name' => $row['Full_Name'],
                              'Date_Admitted' => date("m/d/Y h:i A",strtotime($row['Date_Admitted'])),
                              'Date_Discharged' => $row['discharge_date']." ".$discharge_time_f,
                              'Date_Received' => $Date_Received,
                              'Age' => $row['Age'],
                              'er_opd_diagnosis' => $row['er_opd_diagnosis'],
                              'Sex' => $row['Sex'],
                              'Remarks' => $row['Remarks'],
                              'insurance' => $row['insurance'],
                              'department' => $row['department'],
                              'insurance' => $row['insurance'],
                              
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Case_No'] = NULL; 
    }       