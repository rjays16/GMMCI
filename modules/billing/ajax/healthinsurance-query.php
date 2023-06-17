<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');
    
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');    
require($root_path.'include/care_api_classes/class_insurance.php');

$query = html_entity_decode(urldecode($_GET['query']));
getSelectedInsurance($query);

function getSelectedInsurance($sfilter) {
    $objhcare = new Insurance();
    
    if ($result = $objhcare->getHealthInsurances($sfilter)) {
        while ($row = $result->FetchRow()) {
            $id = trim($row["hcare_id"]);
            $desc  = trim($row["firm_id"]);            
                
            print "$desc\t$id\n";  
        }
    }
    else
        print "\tNo health insurance found!\n";            
}
?>
