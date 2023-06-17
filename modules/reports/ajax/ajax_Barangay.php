<?php
   #Created by Jarel 02/02/2013
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require "{$root_path}classes/json/json.php";

    global $db;
    
    $prov_nr = html_entity_decode(urldecode($_GET['prov_nr']));
    $mun_nr = html_entity_decode(urldecode($_GET['mun_nr']));
    $barangay = html_entity_decode(urldecode($_GET['term']));
    
    $municipal = (($mun_nr == 0) || is_null($mun_nr)) ? " " : " AND b.mun_nr = $mun_nr ";
    #$province = (($prov_nr == 0) || is_null($prov_nr)) ? " " : " AND m.prov_nr = $prov_nr ";  
    
    /*$sql =  "SELECT brgy_nr, brgy_name, mun_name \n
                  FROM seg_barangays b INNER JOIN seg_municity m \n
                     ON b.mun_nr = m.mun_nr \n
                  INNER JOIN seg_provinces p \n
                     ON p.prov_nr = m.prov_nr   
                  WHERE brgy_name REGEXP '^$barangay.*' \n
                     AND brgy_name <> '' $municipal\n
                  ORDER BY brgy_name";*/
    $sql =  "SELECT brgy_nr, brgy_name, mun_name \n
                  FROM seg_barangays b INNER JOIN seg_municity m \n
                     ON b.mun_nr = m.mun_nr \n
                  WHERE brgy_name REGEXP '^$barangay.*' \n
                     AND brgy_name <> '' $municipal\n
                  ORDER BY brgy_name";
                          
    #echo "<br>sql = ".$sql;
    if($result=$db->Execute($sql)){
        if($result->RecordCount()){
             while ($row = $result->FetchRow()){
                 
                     $data[] = array (
                       'id'=>trim($row['brgy_nr']),
                       'label' => trim($row['brgy_name']).' ====== '.trim($row['mun_name']),
                       'value' => trim($row['brgy_name']),
                     );    
              }
        }else{
                $data[] = array (
                   'id'=> '0',
                   'label' => 'No Barangay Found!',
                   'value' => 'No Barangay Found!'
                 );
        };
    }else{return FALSE; } 
     $json = new Services_JSON;
     echo $json->encode($data); 
?>