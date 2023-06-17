<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
        
    $sql = "SELECT p.bestellnum, p.artikelname, 
            COUNT(d.refno) AS total
            FROM seg_pharma_orders h
            INNER JOIN seg_pharma_order_items d ON d.refno=h.refno
            INNER JOIN care_pharma_products_main p ON p.bestellnum=d.bestellnum
            INNER JOIN seg_blood_products_item i ON i.item_code = d.bestellnum
            WHERE h.pharma_area='BB'
            AND DATE(h.orderdate) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            AND d.serve_status='S'
            AND p.is_deleted=0
            AND d.is_deleted=0
            GROUP BY i.service_code
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
                              'id' => $row['bestellnum'],
                              'item' => $row['artikelname'],
                              'total' => (int) $row['total']
                              );
           
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }     
