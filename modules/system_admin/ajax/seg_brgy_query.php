<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');
    
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$query  = html_entity_decode(urldecode($_GET['query']));
$mun_nr = html_entity_decode(urldecode($_GET['mun_nr']));   
getSelectedBrgy($query, $mun_nr);

function getSelectedBrgy($sfilter, $nr = 0) {
    global $db;      
    
    $qryfilter = (($nr == 0) || is_null($nr)) ? " " : " and b.mun_nr = $nr ";               
    $strSQL = "select brgy_nr, brgy_name, mun_name \n
                  from seg_barangays as b inner join seg_municity as m \n
                     on b.mun_nr = m.mun_nr \n
                  where brgy_name regexp '^$sfilter.*' and \n
                     brgy_name <> ''$qryfilter\n
                  order by brgy_name";                      
    if ($result = $db->Execute($strSQL)) {
        while ($row = $result->FetchRow()) {
            $s_nr   = trim($row["brgy_nr"]);
            $s_name = trim($row["brgy_name"]);  
            $m_name = trim($row["mun_name"]);         
                
            print "$s_nr\t$s_name\t$m_name\n";  
        }    
    }    
    else
        print "\tNo barangay found!\t \n";            
}
?>