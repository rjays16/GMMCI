<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing/ajax/seg-deposit-dist.common.php');

function saveDeposit($ref_no, $data) {
    global $db;
    $objResponse = new xajaxResponse();
  
    $db->StartTrans();  
  
    $sql = "DELETE FROM seg_applied_deposit WHERE ref_no=".$db->qstr($ref_no);
    $saveok = $db->Execute($sql);
    
//    if ($saveok && ($pkg_id != 0)) {
//        $sql = "delete from seg_billing_pkg where ref_no = ".$db->qstr($ref_no);
//        $saveok = $db->Execute($sql);
//    }
  
    if ($saveok) {    
       if (!empty($data)) {
          $sql = "INSERT INTO seg_applied_deposit(ref_no, bill_area, deposit, priority) ".
                 "VALUES(".$db->qstr($ref_no).",?,?,?)";
          $saveok = $db->Execute( $sql, $data );                    
       }
       
//       if ($saveok && ($pkg_id != 0)) {
//          $sql = "insert into seg_billing_pkg (ref_no, package_id) ".
//                 "   values(".$db->qstr($ref_no).", {$pkg_id})";
//          $saveok = $db->Execute( $sql );
//       }
    }
    
    if ($saveok) {
        $db->CompleteTrans();
        $objResponse->alert('Deposit distribution saved successfully!');
    }
    else {
        $db->FailTrans();
        $db->CompleteTrans();
        $objResponse->alert('Error: '.$db->ErrorMsg()."\n$sql"."\n".print_r($data, true));
    }
    return $objResponse;
}

$xajax->processRequest();
?>