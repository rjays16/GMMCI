<?php
#Created by Jarel 02/02/2013
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
require "{$root_path}classes/json/json.php";

global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('icd_code_expiry');
        $icd_code_expiry = $GLOBAL_CONFIG['icd_code_expiry'];

$term = $_GET['term'];
$iscode = strtoupper($_GET['iscode']);

if ($iscode == "TRUE") {
    $where = "WHERE code LIKE '$term%'";
} else {
    $where = "WHERE  description LIKE '$term%'";
}

  $sql="SELECT code,description FROM  seg_case_rate_packages $where AND case_type='m' AND date_to = ".$db->qstr($icd_code_expiry)."";
#var_dump($sql);die();
if ($result = $db->Execute($sql)) { 
    if ($result->RecordCount()) {   
        while ($row = $result->FetchRow()) {
            if ($iscode == "TRUE") {
                $data[] = array(
                    'id' => trim($row['code']),
                    'description' => trim($row['description']),
                    'label' => trim($row['code']) . " " . trim($row['description']),
                    'value' => trim($row['code'])
                );
            } else {
                $data[] = array(
                    'id' => trim($row['code']),
                    'description' => trim($row['description']),
                    'label' => trim($row['code']) . " " . trim($row['description']),
                    'value' => trim($row['description'])
                );
            }
        }
    } else {
        $data[] = array(
            'id' => 'No ICD Found!',
            'label' => 'No ICD Found!',
            'value' => 'No ICD Found!'
        );
    };
} else {
    return FALSE;
}

$json = new Services_JSON;
echo $json->encode($data);