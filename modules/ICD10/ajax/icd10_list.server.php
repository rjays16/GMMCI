<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/ICD10/ajax/icd10_list.common.php');

function deleteSelectedICDs($icdcodes) {           
    global $db;
    $objResponse = new xajaxResponse();
    
    $status = "";
        
    if (is_array($icdcodes) && (count($icdcodes) > 0)) {
        $db->StartTrans();
        foreach ($icdcodes as $k => $v) {
            $v = str_replace("'", "", $v);
            
            $strSQL = "DELETE FROM care_icd10_en WHERE diagnosis_code = '$v'";
            $bSuccess = $db->Execute($strSQL);
                        
            if (!$bSuccess) {
                if ($status != "") $status .= "\n\n";
                $status .= "DELETION OF CODE $v STATUS: ".$db->ErrorMsg();
            }            
        }  
        $db->CompleteTrans();
    }
    
    if ($status != "") {
        $objResponse->alert($status);
    }
    
    return $objResponse;
}

$xajax->processRequest();
?>