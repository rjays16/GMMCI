<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
        
    $sql = "SELECT 
            DATE(d.serve_dt) AS daily,
            SUM(CASE WHEN p.bestellnum IN
                      (SELECT item_code FROM seg_blood_products_item WHERE service_code = 'CRYO') THEN 1 ELSE 0 END) 
                AS 'Cryo',
            SUM(CASE WHEN p.bestellnum IN
                      (SELECT item_code FROM seg_blood_products_item WHERE service_code = 'FFP') THEN 1 ELSE 0 END) 
                AS 'FFP',
            SUM(CASE WHEN p.bestellnum IN
                      (SELECT item_code FROM seg_blood_products_item WHERE service_code = 'PC') THEN 1 ELSE 0 END) 
                AS 'Platelet',
            SUM(CASE WHEN p.bestellnum IN
                      (SELECT item_code FROM seg_blood_products_item WHERE service_code = 'PRBC') THEN 1 ELSE 0 END) 
                AS 'PRBC',
            SUM(CASE WHEN p.bestellnum IN
                      (SELECT item_code FROM seg_blood_products_item WHERE service_code = 'WB') THEN 1 ELSE 0 END) 
                AS 'WB',
            SUM(CASE WHEN p.bestellnum IN
                      (SELECT item_code FROM seg_blood_products_item WHERE service_code = 'ALIQUOT') THEN 1 ELSE 0 END) AS 'Aliquot' 
                
            FROM seg_pharma_orders h
            INNER JOIN seg_pharma_order_items d ON d.refno=h.refno
            INNER JOIN care_pharma_products_main p ON p.bestellnum=d.bestellnum
            WHERE h.pharma_area='BB'
            AND DATE(h.orderdate) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            AND d.serve_status='S'
            AND p.is_deleted=0
            AND d.is_deleted=0
            GROUP BY DATE(h.orderdate)
            ORDER BY DATE(h.orderdate)";        
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'daily' => date("m/d/Y", strtotime($row['daily'])),
                              'Cryo' => (int) $row['Cryo'],
                              'FFP' => (int) $row['FFP'],
                              'Platelet' => (int) $row['Platelet'],
                              'PRBC' => (int) $row['PRBC'],
                              'WB' => (int) $row['WB'],
                              'Aliquot' => (int) $row['Aliquot']
                              );
                              
                              
           $total = (int) $row['Cryo'] + (int) $row['FFP'] + (int) $row['Platelet'] + (int) $row['PRBC'] + (int) $row['WB'] + (int) $row['Aliquot'];
           $grand_total += $total;
           
           $data[$rowindex]['total'] = $total;
           
           $rowindex++;
        }  

          $data[0]['grand_total'] = $grand_total;
          
          #print_r($data);
          #exit();
    }else{
        $data[0]['daily'] = NULL; 
    }     
