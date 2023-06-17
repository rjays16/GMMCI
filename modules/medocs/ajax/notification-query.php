<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_medocs.php');

$query = html_entity_decode(urldecode($_GET['query']));
getSelectedNotification($query);

function getSelectedNotification($sfilter) {
    $objmedocs = new Medocs();

    if ($result = $objmedocs->getSelectedNotification($sfilter)) {
        while ($row = $result->FetchRow()) {
            $scode = trim($row["id"]);
            $desc  = substr(trim($row["description"]), 0, 55);

            print "$scode\t$desc\n";
        }
    }
    else
        print "\tNo Notification found!\n";
}
?>