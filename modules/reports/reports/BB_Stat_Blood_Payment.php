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
            SUM(CASE WHEN h.is_cash=1 AND d.request_flag='paid' THEN 1 ELSE 0 END) AS paid,
            SUM(CASE WHEN h.is_cash=1 AND d.request_flag='charity' THEN 1 ELSE 0 END) AS charity,
            SUM(CASE WHEN h.is_cash=1 AND d.request_flag='lingap' THEN 1 ELSE 0 END) AS lingap,
            SUM(CASE WHEN h.is_cash=1 AND d.request_flag='cmap' THEN 1 ELSE 0 END) AS cmap,
            SUM(CASE WHEN h.is_cash=0 AND (charge_type='PERSONAL') THEN 1 ELSE 0 END) AS tpl,
            SUM(CASE WHEN h.is_cash=0 AND (charge_type='PHIC') THEN 1 ELSE 0 END) AS phic,
            SUM(CASE WHEN h.is_cash=0 AND (charge_type IN ('COH','CAO')) THEN 1 ELSE 0 END) AS cao_coh,
            SUM(CASE WHEN h.is_cash=0 AND (charge_type='PCSO') THEN 1 ELSE 0 END) AS pcso,
            SUM(CASE WHEN h.is_cash=0 AND (charge_type='MISSION') THEN 1 ELSE 0 END) AS mission,           
            SUM(CASE WHEN h.is_cash=0 AND 
                (charge_type NOT IN ('PERSONAL','PHIC','MISSION','PCSO','CAO','COH','LINGAP','CMAP','MISSION')) 
               THEN 1 ELSE 0 END) AS others
            FROM seg_pharma_orders h
            INNER JOIN seg_pharma_order_items d ON d.refno=h.refno
            INNER JOIN care_pharma_products_main p ON p.bestellnum=d.bestellnum 
            INNER JOIN seg_blood_products_item i ON i.item_code = d.bestellnum
            WHERE DATE(h.orderdate) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            AND h.pharma_area='BB'
            AND d.serve_status='S'
            AND p.is_deleted=0
            AND d.is_deleted=0
            AND h.is_deleted=0
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
                              'paid' => (int) $row['paid'],
                              'charity' => (int) $row['charity'],
                              'lingap' => (int) $row['lingap'],
                              'cmap' => (int) $row['cmap'],
                              'tpl' => (int) $row['tpl'],
                              'phic' => (int) $row['phic'],
                              'cao_coh' => (int) $row['cao_coh'],
                              'pcso' => (int) $row['pcso'],
                              'mission' => (int) $row['mission'],
                              'others' => (int) $row['others']
                              );
                              
           $total = (int) $row['paid'] + (int) $row['charity'] + (int) $row['lingap'] + (int) $row['cmap'] 
                    + (int) $row['tpl'] + (int) $row['phic'] + (int) $row['cao_coh'] + (int) $row['pcso'] 
                    + (int) $row['mission'] + (int) $row['others'];
           
           $data[$rowindex]['total'] = $total;                   
           
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }     
