<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');
    
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$query = html_entity_decode(urldecode($_GET['query']));
getSelectedProv($query);

function getSelectedProv($sfilter) {
    global $db;      
    
    $strSQL = "select prov_nr, prov_name \n
                  from seg_provinces \n
                  where prov_name regexp '^$sfilter.*' and \n
                     prov_name <> '' \n
                  order by prov_name";

    if ($result = $db->Execute($strSQL)) {
        while ($row = $result->FetchRow()) {
            $s_nr   = trim($row["prov_nr"]);
            $s_name = trim($row["prov_name"]);            
                
            print "$s_nr\t$s_name\n";  
        }    
    }    
    else
        print "\tNo province found!\n";            
}
?>