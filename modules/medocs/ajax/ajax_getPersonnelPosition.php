<?php
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require "{$root_path}classes/json/json.php";

    global $db;
    
    $personell_nr = $_GET['personell_nr'];
    $document = $_GET['document'];
    
    $sql="SELECT s.*
            FROM seg_signatory AS s
            WHERE document_code=".$db->qstr($document)." 
            AND personell_nr=".$db->qstr($personell_nr);
    
    if($result=$db->Execute($sql)){
        if($result->RecordCount()){
            $data[] = array (
                       'personell_nr'=>$personell_nr,
                       'signatory_position' => trim($row['signatory_position']),
                       'signatory_title' => trim($row['signatory_title'])
                     );    
                 
        };
    }else{return FALSE; } 
    $json = new Services_JSON;
    echo $json->encode($data); 
?>
