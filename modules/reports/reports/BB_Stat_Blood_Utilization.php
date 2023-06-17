<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
        
    $sql = "SELECT c.long_name AS blood_component, 
            SUM(CASE WHEN (t.GROUP='O' AND (DATE(d.received_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS o_deposited,
            SUM(CASE WHEN (t.GROUP='O' AND (DATE(s.done_date)     BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS o_crossmatched,
            SUM(CASE WHEN (t.GROUP='O' AND (DATE(s.issuance_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS o_transfused,

            SUM(CASE WHEN (t.GROUP='A' AND (DATE(d.received_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS a_deposited,
            SUM(CASE WHEN (t.GROUP='A' AND (DATE(s.done_date)     BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS a_crossmatched,
            SUM(CASE WHEN (t.GROUP='A' AND (DATE(s.issuance_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS a_transfused,

            SUM(CASE WHEN (t.GROUP='B' AND (DATE(d.received_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS b_deposited,
            SUM(CASE WHEN (t.GROUP='B' AND (DATE(s.done_date)     BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS b_crossmatched,
            SUM(CASE WHEN (t.GROUP='B' AND (DATE(s.issuance_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS b_transfused,

            SUM(CASE WHEN (t.GROUP='AB' AND (DATE(d.received_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS ab_deposited,
            SUM(CASE WHEN (t.GROUP='AB' AND (DATE(s.done_date)     BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS ab_crossmatched,
            SUM(CASE WHEN (t.GROUP='AB' AND (DATE(s.issuance_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS ab_transfused,

            SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND (DATE(d.received_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS other_deposited,
            SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND (DATE(s.done_date)     BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS other_crossmatched,
            SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND (DATE(s.issuance_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."))  THEN 1 ELSE 0 END) AS other_transfused
                        
            FROM seg_blood_received_details d
            INNER JOIN seg_blood_received_status s ON s.refno=d.refno
                 AND s.service_code=d.service_code AND s.ordering=d.ordering       
            INNER JOIN seg_blood_component c ON c.id=d.component
            INNER JOIN seg_lab_serv h ON h.refno=d.refno
            LEFT JOIN seg_blood_type_patient bp ON bp.pid=h.pid
            LEFT JOIN seg_blood_type t ON t.id=bp.blood_type
            WHERE d.STATUS IN ('received')
            GROUP BY d.component
            ORDER BY COUNT(d.refno) DESC";        
           
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
