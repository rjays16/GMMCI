<?php
   #Created by Jarel 02/02/2013
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require "{$root_path}classes/json/json.php";

    global $db;
    
    $term = $_GET['term'];
    $iscode = strtoupper($_GET['iscode']);
   
    if($iscode=="TRUE"){
        $where = "WHERE diagnosis_code LIKE '$term%'";  
    }else{
        $where = "WHERE  description LIKE '$term%'"; 
    }  
        $sql="SELECT diagnosis_code,description FROM  care_icd10_en $where";
        #echo "<br>sql = ".$this->sql;
        if($result=$db->Execute($sql)){
            if($result->RecordCount()){
                 while ($row = $result->FetchRow()){
                     
                         $data[] = array (
                           'id'=>trim($row['diagnosis_code']),
                           'label' => trim($row['diagnosis_code'])." ".trim($row['description']),
                           'value' => trim($row['diagnosis_code'])." ".trim($row['description'])
                         );    
                  }
            }else{
                    $data[] = array (
                       'id'=> 'No ICD Found!',
                       'label' => 'No ICD Found!',
                       'value' => 'No ICD Found!'
                     );
            };
        }else{return FALSE; } 
         $json = new Services_JSON;
         echo $json->encode($data); 
?>
