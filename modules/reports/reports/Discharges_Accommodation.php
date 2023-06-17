<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $area_type);
    
    $sql = " $query_sub_accom
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            GROUP BY d.name_formal
            ORDER BY d.name_formal";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $total_pay = (int) $row['pay_non_phic'] + (int) $row['pay_phic_memdep'] + (int) $row['pay_phic_indigent'] + (int) $row['pay_phic_owwa'];
            $total_charity = (int) $row['charity_non_phic'] + (int) $row['charity_phic_memdep'] + (int) $row['charity_phic_indigent'] + (int) $row['charity_phic_owwa'];
            $total_discharge = $total_pay + $total_charity;
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'Type_Of_Service' => $row['Type_Of_Service'],
                              'pay_non_phic' => (int) $row['pay_non_phic'],
                              'pay_phic_memdep' => (int) $row['pay_phic_memdep'],
                              'pay_phic_indigent' => (int) $row['pay_phic_indigent'],
                              'pay_phic_owwa' => (int) $row['pay_phic_owwa'],
                              'total_pay' => (int) $total_pay,
                              'charity_non_phic' => (int) $row['charity_non_phic'],
                              'charity_phic_memdep' => (int) $row['charity_phic_memdep'],
                              'charity_phic_indigent' => (int) $row['charity_phic_indigent'],
                              'charity_phic_owwa' => (int) $row['charity_phic_owwa'],
                              'total_charity'  => (int) $total_charity,
                              'total_discharge' => (int) $total_discharge,
                              'total_len_stay' => (int) $row['total_len_stay'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Type_Of_Service'] = NULL; 
    }       