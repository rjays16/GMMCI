<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
        
    $sql = "SELECT CONCAT(h.serv_dt, ' ', h.serv_tm ) AS date_time, 
            h.pid, p.date_birth, p.sex,
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ', IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS patient_name,
            w.NAME AS ward, e.encounter_type,b.GROUP AS blood_type, d.ordering,
            d.serial_no AS serial_number, d.component AS component, d.result AS result,
            d.received_date AS date_received, s.done_date AS date_crossmatched, s.issuance_date AS date_issued 
            FROM seg_lab_serv h
            INNER JOIN seg_lab_servdetails dr ON dr.refno=h.refno
            INNER JOIN seg_blood_received_details d ON d.refno=h.refno AND dr.service_code=d.service_code
            INNER JOIN seg_blood_received_status s ON s.refno=d.refno
                 AND s.service_code=d.service_code AND s.ordering=d.ordering
             
            INNER JOIN care_person p ON p.pid=h.pid

            LEFT JOIN care_encounter e ON e.encounter_nr=h.encounter_nr
            LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr

            LEFT JOIN seg_blood_type_patient tp ON tp.pid=h.pid
            LEFT JOIN seg_blood_type b ON b.id=tp.blood_type
            WHERE DATE(h.serv_dt) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            AND h.ref_source='BB'
            AND d.STATUS IN ('received')
            AND h.STATUS NOT IN ('deleted','hidden','inactive','void') 
            ORDER BY h.serv_dt, h.serv_tm, p.name_last, p.name_first, p.name_middle";        
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'blood_component' => $row['blood_component'], 
                          'o_deposited'     => (int) $row['o_deposited'],
                          'o_crossmatched'  => (int) $row['o_crossmatched'],
                          'o_transfused'    => (int) $row['o_transfused'],
                          'a_deposited'     => (int) $row['a_deposited'],
                          'a_crossmatched'  => (int) $row['a_crossmatched'],
                          'a_transfused'    => (int) $row['a_transfused'],
                          'b_deposited'     => (int) $row['b_deposited'],
                          'b_crossmatched'  => (int) $row['b_crossmatched'],
                          'b_transfused'    => (int) $row['b_transfused'],
                          'ab_deposited'     => (int) $row['ab_deposited'],
                          'ab_crossmatched'  => (int) $row['ab_crossmatched'],
                          'ab_transfused'    => (int) $row['ab_transfused']
                          );
                          
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['blood_component'] = NULL; 
    }  
