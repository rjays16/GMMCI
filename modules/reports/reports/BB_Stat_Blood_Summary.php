     <?php
     error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
     require_once('./roots.php');
     require_once($root_path.'include/inc_environment_global.php');
    
     include('parameters.php');

     #TITLE of the report
     $params->put("hospital_name", mb_strtoupper($hosp_name));
     $params->put("header", $report_title);
     $params->put("department", "Laboratory");
     #$params->put("area", $patient_type_label." (".$date_based_label.") from ".trim(mb_strtoupper($area)));
     
         $sql = "SELECT sbrd.`component` AS component, 
                                
                            SUM(IF(sbrd.`ordering`='1' AND sbrd.`received_date` IS NOT NULL,1,0)) AS ANEG_DREC, 
                            SUM(IF(sbrd.`ordering`='1' AND sbrs.`done_date` IS NOT NULL,1,0)) AS ANEG_DDON,
                            SUM(IF(sbrd.`ordering`='1' AND sbrs.`issuance_date` IS NOT NULL,1,0)) AS ANEG_DISS,
                            
                            SUM(IF(sbrd.`ordering`='2' AND sbrd.`received_date` IS NOT NULL,1,0)) AS APOS_DREC,
                            SUM(IF(sbrd.`ordering`='2' AND sbrs.`done_date` IS NOT NULL,1,0)) AS APOS_DDON,
                            SUM(IF(sbrd.`ordering`='2' AND sbrs.`issuance_date` IS NOT NULL,1,0)) AS APOS_DISS,
                            
                            SUM(IF(sbrd.`ordering`='3' AND sbrd.`received_date` IS NOT NULL,1,0)) AS BNEG_DREC, 
                            SUM(IF(sbrd.`ordering`='3' AND sbrs.`done_date` IS NOT NULL,1,0)) AS BNEG_DDON,
                            SUM(IF(sbrd.`ordering`='3' AND sbrs.`issuance_date` IS NOT NULL,1,0)) AS BNEG_DISS,
                            
                            SUM(IF(sbrd.`ordering`='4' AND sbrd.`received_date` IS NOT NULL,1,0)) AS BPOS_DREC,
                            SUM(IF(sbrd.`ordering`='4' AND sbrs.`done_date` IS NOT NULL,1,0)) AS BPOS_DDON,
                            SUM(IF(sbrd.`ordering`='4' AND sbrs.`issuance_date` IS NOT NULL,1,0)) AS BPOS_DISS,
                            
                            SUM(IF(sbrd.`ordering`='5' AND sbrd.`received_date` IS NOT NULL,1,0)) AS ONEG_DREC, 
                            SUM(IF(sbrd.`ordering`='5' AND sbrs.`done_date` IS NOT NULL,1,0)) AS ONEG_DDON,
                            SUM(IF(sbrd.`ordering`='5' AND sbrs.`issuance_date` IS NOT NULL,1,0)) AS ONEG_DISS,
                            
                            SUM(IF(sbrd.`ordering`='6' AND sbrd.`received_date` IS NOT NULL,1,0)) AS OPOS_DREC,
                            SUM(IF(sbrd.`ordering`='6' AND sbrs.`done_date` IS NOT NULL,1,0)) AS OPOS_DDON,
                            SUM(IF(sbrd.`ordering`='6' AND sbrs.`issuance_date` IS NOT NULL,1,0)) AS OPOS_DISS,
                            
                            SUM(IF(sbrd.`ordering`='7' AND sbrd.`received_date` IS NOT NULL,1,0)) AS ABNEG_DREC,    
                            SUM(IF(sbrd.`ordering`='7' AND sbrs.`done_date` IS NOT NULL,1,0)) AS ABNEG_DDON,
                            SUM(IF(sbrd.`ordering`='7' AND sbrs.`issuance_date` IS NOT NULL,1,0)) AS ABNEG_DISS,
                            
                            SUM(IF(sbrd.`ordering`='8' AND sbrd.`received_date` IS NOT NULL,1,0)) AS ABPOS_DREC,
                            SUM(IF(sbrd.`ordering`='8' AND sbrs.`done_date` IS NOT NULL,1,0)) AS ABPOS_DDON,
                            SUM(IF(sbrd.`ordering`='8' AND sbrs.`issuance_date` IS NOT NULL,1,0)) AS ABPOS_DISS
                
                FROM            seg_blood_received_status `sbrs`
                LEFT JOIN       seg_blood_received_details `sbrd` ON sbrs.`refno` = sbrd.`refno`
                WHERE           DATE(sbrd.received_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
                GROUP BY        sbrd.`component`";
           

     $rs = $db->Execute($sql);

    
     $datacom =  array('ALIQUOT' => "Aliquot",
                       'CRYO' => "Cryoprecipitated",
                       'FFP'=> "Fresh Frozen Plasma",
                       'PC' => "Platelet Cells",
                       'PRBC' => "Packed Blood Cells",
                       'WB' => "Whole Blood",
                       'WB_PRBC' => "WB and PRBC");
     $rowindex = 0;
     $data = array();
        while($row=$rs->FetchRow()){
              
                          $data[$rowindex] =  array('component' => isset($datacom[$row['component']]) ? $datacom[$row['component']] : $row['component'] ,
                                                    'aneg_received' => $row['ANEG_DREC'],
                                                    'aneg_done' => $row['ANEG_DDON'],
                                                    'aneg_issued' => $row['ANEG_DISS'],
                                                    
                                                    'apos_received' => $row['APOS_DREC'],
                                                    'apos_done' => $row['APOS_DDON'],
                                                    'apos_issued' => $row['APOS_DISS'],

                                                    'bneg_received' => $row['BNEG_DREC'],
                                                    'bneg_done' => $row['BNEG_DDON'],
                                                    'bneg_issued' => $row['BNEG_DISS'],

                                                    'bpos_received' => $row['BPOS_DREC'],
                                                    'bpos_done' => $row['BPOS_DDON'],
                                                    'bpos_issued' => $row['BPOS_DISS'],

                                                    'oneg_received' => $row['ONEG_DREC'],
                                                    'oneg_done' => $row['ONEG_DDON'],
                                                    'oneg_issued' => $row['ONEG_DISS'],

                                                    'opos_received' => $row['OPOS_DREC'],
                                                    'opos_done' => $row['OPOS_DDON'],
                                                    'opos_issued' => $row['OPOS_DISS'],

                                                    'abneg_received' => $row['ABNEG_DREC'],
                                                    'abneg_done' => $row['ABNEG_DDON'],
                                                    'abneg_issued' => $row['ABNEG_DISS'],

                                                    'abpos_received' => $row['ABPOS_DREC'],
                                                    'abpos_done' => $row['ABPOS_DDON'],
                                                    'abpos_issued' => $row['ABPOS_DISS']);
            $rowindex++;
        }
     
    


     



 
