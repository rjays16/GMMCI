<?php
   #Created by Jarel 02/02/2013
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require "{$root_path}classes/json/json.php";

    global $db;
    
    $province = html_entity_decode(urldecode($_GET['term']));
    
    $sql="SELECT prov_nr, prov_name \n
            FROM seg_provinces \n
            WHERE prov_name REGEXP '^$province.*' \n
            AND prov_name <> '' \n
            ORDER by prov_name";
            
    #echo "<br>sql = ".$sql;
    if($result=$db->Execute($sql)){
        if($result->RecordCount()){
             while ($row = $result->FetchRow()){
                 
                     $data[] = array (
                       'id'=>trim($row['prov_nr']),
                       'label' => trim($row['prov_name']),
                       'value' => trim($row['prov_name'])
                     );    
              }
        }else{
                $data[] = array (
                   'id'=> '0',
                   'label' => 'No Province Found!',
                   'value' => 'No Province Found!'
                 );
        };
    }else{return FALSE; } 
     $json = new Services_JSON;
     echo $json->encode($data); 
?>