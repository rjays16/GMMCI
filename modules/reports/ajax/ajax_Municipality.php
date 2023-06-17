<?php
   #Created by Jarel 02/02/2013
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require "{$root_path}classes/json/json.php";

    global $db;
    
    $prov_nr = html_entity_decode(urldecode($_GET['prov_nr']));
    $municipal = html_entity_decode(urldecode($_GET['term']));
    
    $province = (($prov_nr == 0) || is_null($prov_nr)) ? " " : " AND m.prov_nr = $prov_nr "; 
    
    $sql =  "SELECT mun_nr, mun_name, prov_name \n
                  FROM seg_municity m INNER JOIN seg_provinces p \n
                     ON p.prov_nr = m.prov_nr \n
                  WHERE mun_name REGEXP '^$municipal.*' \n
                     AND mun_name <> '' $province\n
                  ORDER BY mun_name";
            
    #echo "<br>sql = ".$sql;
    if($result=$db->Execute($sql)){
        if($result->RecordCount()){
             while ($row = $result->FetchRow()){
                 
                     $data[] = array (
                       'id'=>trim($row['mun_nr']),
                       'label' => trim($row['mun_name']).' ====== '.trim($row['prov_name']),
                       'value' => trim($row['mun_name']),
                     );    
              }
        }else{
                $data[] = array (
                   'id'=> '0',
                   'label' => 'No Municipality  or City Found!',
                   'value' => 'No Municipality  or City Found!'
                 );
        };
    }else{return FALSE; } 
     $json = new Services_JSON;
     echo $json->encode($data); 
?>