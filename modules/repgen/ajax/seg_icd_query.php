<?php
/* yadl_spaceid - Skip Stamping */
header('Content-type: text/plain');
    
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$query  = html_entity_decode(urldecode($_GET['query'])); 
getSelectedIcd($query);

function getSelectedIcd($sfilter) {
    global $db;                    
    $strSQL = "SELECT DISTINCT d.diagnosis_code, d.description 
               FROM care_icd10_en as d
               WHERE IF(instr(d.diagnosis_code,'.'), substr(d.diagnosis_code,1,IF(instr(d.diagnosis_code,'.'),instr(d.diagnosis_code,'.')-1,0)),
               d.diagnosis_code) <> ''
               and (d.diagnosis_code between (d.diagnosis_code regexp '^0.*') and (d.diagnosis_code regexp '^z.*'))
               and (d.diagnosis_code!='>')
               and d.diagnosis_code regexp '^$sfilter.*'
               order by d.diagnosis_code";                      
               
    if ($result = $db->Execute($strSQL)) {
        while ($row = $result->FetchRow()) {
            if($row['description'])
            {
              $code_name = trim($row["description"]);
            }
            else
            {
                $code_name = "No description";
            }                
            $code_nr   = trim($row["diagnosis_code"]);
                      
                
            print "\t".$code_nr."\t".$code_name."\t\n";    
        }    
    }    
   
        print "\tNo ICD10 Code found!\t \n";            
}
?>