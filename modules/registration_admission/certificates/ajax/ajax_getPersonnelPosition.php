<?php
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require "{$root_path}classes/json/json.php";

    global $db;

    $personell_name = $_REQUEST['personell_name'];
    $document = $_REQUEST['document'];

    $sql="SELECT s.*, UPPER(fn_get_personell_name2(personell_nr)) AS name, signatory_position, signatory_title
            FROM seg_signatory AS s
            WHERE document_code=".$db->qstr($document)."
            AND UPPER(fn_get_personell_name2(personell_nr))='".$personell_name."'";

    #echo $sql;
    if($result=$db->Execute($sql)){
        if($result->RecordCount()){
            $row = $result->FetchRow();
            $data = array (
                       'personell_name'=>trim($personell_name),
                       'signatory_position' => trim($row['signatory_position']),
                       'signatory_title' => trim($row['signatory_title'])
                     );

        }else{
            $data = array (
                       'personell_name'=>trim($personell_name),
                       'signatory_position' => '',
                       'signatory_title' => ''
                     );
        }
    }else{return FALSE; }
    $json = new Services_JSON;
    echo $json->encode($data);
?>
