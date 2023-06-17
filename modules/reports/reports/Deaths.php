<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $death_hours_label);
    #$params->put("icd_class", $icd_class);
    $params->put("column_name","Type of Service");
    $params->put("image_path",$image_path);
    
    $data = array();
    $total_death_computation = 0;
    $less48hrs_computation = 0;  

    if ($for_all) {
      
    }

    if ($for_all) {
        $less_48H = "AND (DATEDIFF((IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                DATE(e.admission_dt))<2)";
        $greater_48H = "AND (DATEDIFF((IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                DATE(e.admission_dt))>=2)";
    }elseif ($less_48H && $for_all) {
        $less_48H = "AND (DATEDIFF((IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                DATE(e.admission_dt))<2)";
        $greater_48H = "";
    }elseif ($greater_48H && $for_all) {
        $less_48H = "";
        $greater_48H = "AND (DATEDIFF((IF(p.death_date='0000-00-00',e.discharge_date, p.death_date)),           
                DATE(e.admission_dt))>=2)";
    }

    $total_discharges = "SELECT COUNT(e.pid) AS total_discharges  
                        FROM care_department AS d
                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.is_discharged IS NOT NULL
                        AND e.encounter_type IN (1,2,3,4)
                        AND e.discharge_date IS NOT NULL
                        ";


    $total_discharges_given_period = "SELECT COUNT(e.pid) AS total_discharges  
                        FROM care_department AS d
                        INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                        LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                        LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                        INNER JOIN care_person AS p ON p.pid = e.pid 
                        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                        AND e.is_discharged IS NOT NULL
                        AND e.encounter_type IN (1,2,3,4)
                        AND e.discharge_date IS NOT NULL
                        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                        ";

  
    $total_deaths = "SELECT COUNT(e.pid) AS total_deaths  
                      FROM care_department AS d
                      INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                      LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
                      LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
                      INNER JOIN care_person AS p ON p.pid = e.pid 
                      WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                      AND e.discharge_date IS NOT NULL
                      AND e.encounter_type IN (1,2,3,4)
                      AND sr.result_code IN (4,8,9,10)
                      AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                      ";

    $total_deaths_ipd = "SELECT COUNT(e.pid) AS ipd  
           
            FROM care_department AS d
            INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND e.encounter_type IN (3,4)
            AND sr.result_code IN (4,8,9,10)
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";


    $less_48hours = "SELECT COUNT(e.pid) AS less_48hours  
    
            FROM care_department AS d
            INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND e.encounter_type IN (3,4)
            AND sr.result_code IN (4,8,9,10)
            $less_48H
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";

    $up_48hours = "SELECT COUNT(e.pid) AS up_48hours  
           
            FROM care_department AS d
            INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND e.encounter_type IN (3,4)
            AND sr.result_code IN (4,8,9,10)
            $greater_48H
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";

      $ER_deaths = "SELECT COUNT(e.pid) AS er_deaths  
           
            FROM care_department AS d
            INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND e.encounter_type = 1
            AND sr.result_code IN (4,8,9,10)
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";

      $DOA = "SELECT COUNT(e.pid) AS doa  
           
            FROM care_department AS d
            INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND e.encounter_type = 1
            AND e.is_DOA = 1
            AND sr.result_code IN (4,8,9,10)
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";




      $stillbirths = "SELECT
            count(e.pid) AS stillbirths
            FROM care_encounter_diagnosis AS ed 
            INNER JOIN care_encounter AS e ON  e.encounter_nr=ed.encounter_nr
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND ed.code IN ('Z37','Z37.1','Z37.3','Z37.4','Z37.7')
            AND e.encounter_type IN (1,2,3,4)
            AND sr.result_code IN (4,8,9,10)
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";


        $neonatal = "SELECT
            count(e.pid) AS neonatal
            FROM care_encounter_diagnosis AS ed 
            INNER JOIN care_encounter AS e ON  e.encounter_nr=ed.encounter_nr
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND e.encounter_type IN (1,2,3,4)
            AND sr.result_code IN (4,8,9,10)
            AND ((DATEDIFF(p.death_date,p.date_birth)) BETWEEN 0 AND 364 AND p.fromtemp=1)
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";



        $maternal = "SELECT
            count(e.pid) AS maternal
            FROM care_encounter_diagnosis AS ed 
            INNER JOIN care_encounter AS e ON  e.encounter_nr=ed.encounter_nr
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = e.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND ed.code IN ('O72','O85','O86','O15','O20','O03','O31',
                            'O04','O05','O06','O07','O08','O88','O97',
                            'O95','O96','O98','O99')
            AND e.encounter_type IN (1,2,3,4)
            AND sr.result_code IN (4,8,9,10)
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";
   
     $rs = $db->Execute($total_discharges);
           
     $data[0]['for_output'] = 1;
    
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
             $total_discharges                = $db->GetOne($total_discharges);
             $total_discharges_given_period   = $db->GetOne($total_discharges_given_period);
             $total_deaths                    = $db->GetOne($total_deaths);
             $total_deaths_ipd                = $db->GetOne($total_deaths_ipd);
             $less_48hours                    = $db->GetOne($less_48hours);
             $up_48hours                      = $db->GetOne($up_48hours);
             $ER_deaths                       = $db->GetOne($ER_deaths);
             $DOA                             = $db->GetOne($DOA);
             $stillbirths                     = $db->GetOne($stillbirths);
             $neonatal                        = $db->GetOne($neonatal);
             $maternal                        = $db->GetOne($maternal);

             $gross = ($total_deaths)/($total_discharges_given_period)*100;
             $net = ($total_deaths-$less_48hours)/(($total_discharges)-($less_48hours))*100;

             $gross = strval(round($gross,2));
             $net = strval(round($net,2));
            
                              
        }  
          
    }else{
       
    }     

             $params->put("total_deaths",$total_deaths);
             $params->put("ipd",$total_deaths_ipd);
             $params->put("less_48hours",$less_48hours);
             $params->put("up_48hours",$up_48hours);
             $params->put("er_deaths",$ER_deaths);
             $params->put("doa",$DOA);
             $params->put("stillbirths",$stillbirths);
             $params->put("neonatal",$neonatal);
             $params->put("maternal",$maternal);
             $params->put("gross_death",$gross);
             $params->put("net_death",$net); 



    // exit();