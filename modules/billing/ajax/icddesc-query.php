<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_icd10.php');

$query = html_entity_decode(urldecode($_GET['query']));
getSelectedICD($query);

function getSelectedICD($sfilter) {
    $objicd = new Icd();

    if ($result = $objicd->getSelectedICDDesc($sfilter)) {
        while ($row = $result->FetchRow()) {
             $scode = trim($row["diagnosis_code"]);
            //$scode = trim($row["code"]);
            $desc  = substr(trim($row["description"]), 0, 55);
            print "$desc\t$scode\n";
        }
    }
    else
        print "\tNo ICD found!\n";
}
?>