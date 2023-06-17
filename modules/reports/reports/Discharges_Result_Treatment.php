<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $area_type);
    $params->put("column_name",$column_name_disp);
    
    $sql = " $query_sub_result
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
            $disp_total = (int) $row['disp_disch'] + (int) $row['disp_none'] + (int) $row['disp_admit_opd'] 
                           + (int) $row['disp_hama'] + (int) $row['disp_absc'] + (int) $row['disp_trans'];
            $total_death = (int) $row['deathbelow48'] + (int) $row['deathabove48'];
            $over_total = $disp_total + $total_death;
            #$total_result = (int) $row['total_rec'] + (int) $row['total_imp'] + (int) $row['total_unimp'];
            #$total_noresult = $disp_total - $total_result;
           
            $disp_disc = (int) $row['disp_disch'] + (int) $row['disp_none'] + (int) $row['disp_admit_opd'];
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'Type_Of_Service' => $row['Type_Of_Service'],
                              'disp_disch' => $disp_disc,
                              'disp_hama' => (int) $row['disp_hama'],
                              'disp_absc' => (int) $row['disp_absc'],
                              'disp_trans' => (int) $row['disp_trans'],
                              'disp_none' => (int) $row['disp_none'] + (int) $row['disp_admit_opd'],
                              'disp_total' => (int) $disp_total,
                              'total_rec' => (int) $row['total_rec'],
                              'total_imp' => (int) $row['total_imp'],
                              'total_unimp' => (int) $row['total_unimp'],
                              'total_noresult' => (int) $row['total_noresult'],
                              'deathbelow48'  => (int) $row['deathbelow48'],
                              'deathabove48' => (int) $row['deathabove48'],
                              'total_death' => (int) $total_death,
                              'over_total' => (int) $over_total,
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Type_Of_Service'] = NULL; 
    }       