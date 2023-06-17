<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');
    
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$query = html_entity_decode(urldecode($_GET['query']));
$prov_nr = html_entity_decode(urldecode($_GET['prov_nr'])); 
getSelectedMuniCity($query, $prov_nr);

function getSelectedMuniCity($sfilter, $nr = 0) {
    global $db;      
    
    $qryfilter = (($nr == 0) || is_null($nr)) ? " " : " and prov_nr = $nr "; 
    $strSQL = "select mun_nr, mun_name \n
                  from seg_municity \n
                  where mun_name regexp '^$sfilter.*' and \n
                     mun_name <> ''$qryfilter\n
                  order by mun_name";

    if ($result = $db->Execute($strSQL)) {
        while ($row = $result->FetchRow()) {
            $s_nr   = trim($row["mun_nr"]);
            $s_name = trim($row["mun_name"]);            
                
            print "$s_nr\t$s_name\n";  
        }    
    }    
    else
        print "\tNo municipality or city found!\n";            
}
?>