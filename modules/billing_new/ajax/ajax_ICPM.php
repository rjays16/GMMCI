<?php
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require "{$root_path}classes/json/json.php";
require_once($root_path . 'include/care_api_classes/billing/class_ops.php');
require_once($root_path . "include/care_api_classes/class_hospital_admin.php");

global $db;

$term = $_GET['term'];
$iscode = strtoupper($_GET['iscode']);
$hospObj = new Hospital_Admin();
$srv = new SegOps;

if ($iscode == "TRUE") {
    $where = "cp.code LIKE '$term%'";
} else {
    $where = "cp.description LIKE '$term%'";
}

if ($srv->isHouseCase($enc_nr))
    $nPCF = HOUSE_CASE_PCF;
else
    $nPCF = $hospObj->getDefinedPCF();

$sql = "SELECT
            cp.code, cp.description, op.rvu, {$nPCF} as multiplier,
            cp.for_laterality, cp.special_case
        FROM seg_case_rate_packages AS cp
        INNER JOIN seg_ops_rvs AS op ON cp.code=op.code
        WHERE op.is_active<>'0' AND cp.case_type='p' AND $where
        GROUP BY cp.code";

if ($result = $db->Execute($sql)) {
    if ($result->RecordCount()) {
        while ($row = $result->FetchRow()) {

            if ($iscode == "TRUE") {
                $data[] = array(
                    'id' => trim($row['code']),
                    'description' => trim($row['description']),
                    'label' => trim($row['code']) . " " . trim($row['description']),
                    'value' => trim($row['code']),
                    'rvu' => trim($row['rvu']),
                    'laterality' => trim($row['for_laterality']),
                    'multiplier' => trim($row['multiplier']),
                    'special_case' => trim($row['special_case'])
                );
            } else {
                $data[] = array(
                    'id' => trim($row['code']),
                    'description' => trim($row['description']),
                    'label' => trim($row['code']) . " " . trim($row['description']),
                    'value' => trim($row['description']),
                    'rvu' => trim($row['rvu']),
                    'laterality' => trim($row['for_laterality']),
                    'multiplier' => trim($row['multiplier']),
                    'special_case' => trim($row['special_case'])
                );
            }

        }
    } else {
        $data[] = array(
            'id' => 'No ICP Found!',
            'label' => 'No ICP Found!',
            'laterality' => 0,
            'value' => 'No ICP Found!'
        );
    };
} else {
    return FALSE;
}

$json = new Services_JSON;
echo $json->encode($data);