<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing/ajax/seg-coverage-adjustment.common.php');

function saveCoverage($ref_no, $data) {
    global $db;
    $objResponse = new xajaxResponse();
  
    $db->StartTrans();  
  
    $sql = "DELETE FROM seg_billingcoverage_adjustment WHERE ref_no=".$db->qstr($ref_no);
    $saveok = $db->Execute($sql);    
  
    if ($saveok) {    
       if (!empty($data)) {
          $sql = "INSERT INTO seg_billingcoverage_adjustment(ref_no, bill_area, hcare_id, coverage, priority) ".
                 "VALUES(".$db->qstr($ref_no).",?,?,?,?)";
          $saveok = $db->Execute( $sql, $data );                    
       }       
    }
    
    if ($saveok) {
        $db->CompleteTrans();
        $objResponse->alert('Adjusted coverage saved successfully!');
        $objResponse->call("setCoverageAdjustedFlag", 1);
    }
    else {
        $db->FailTrans();
        $db->CompleteTrans();
        $objResponse->alert('Error: '.$db->ErrorMsg()."\n$sql");
        $objResponse->alert(print_r($data,true));
    }
    return $objResponse;
}

$xajax->processRequest();


?>