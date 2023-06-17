<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');
    
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$query  = html_entity_decode(urldecode($_GET['query']));
getSelectedIcp($query);

function getSelectedIcp($sfilter) {
    global $db;                    
    $strSQL = "SELECT DISTINCT d.code, d.description 
               FROM care_ops301_en as d
               WHERE IF(instr(d.code,'.'), substr(d.code,1,IF(instr(d.code,'.'),instr(d.code,'.')-1,0)),
               d.code) <> ''
               and d.code regexp '^$sfilter-*'
               order by d.code";                      
               
    if ($result = $db->Execute($strSQL)) {
        while ($row = $result->FetchRow()) {
            if($row['description'])
            {
              $code_name = trim($row['description']);
            }
            else
            {
                $code_name = "No description";
            }                
            $code_nr   = trim($row["code"]);
                      
                
           print "\t".$code_nr."\t".$code_name."\t\n";   
        }    
    }    
    
        print "\tNo ICP Code found!\t \n";            
}
?>