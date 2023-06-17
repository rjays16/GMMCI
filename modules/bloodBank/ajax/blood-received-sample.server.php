<?php

    function save_dateinfo($refno, $service_code, $index, $mode, $date, $time, $return_reason){
        global $db;
        $srvObj=new SegLab();
        $objResponse = new xajaxResponse();    
        
        $datetime = $date." ".$time;
        
        //added by: borj
        //2013/28/11
        $RetReason = $srvObj->checkRetReason($refno);
        
       
        if ($return_reason){
            if ($RetReason != $return_reason)
                $RetReason = $return_reason;
       }
       
       $ok = $srvObj->UpdateBloodStatus($refno, $service_code, $index, $mode, $datetime, $RetReason); 
       
        
        if ($ok)
            $msg = "The Data is successfully change.";
        else    
            $msg = "Updating Data is failed.";
        
        $objResponse->alert($msg);
        
                
        return $objResponse;
    }
   
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require($root_path.'modules/bloodBank/ajax/blood-received-sample.common.php');
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    
    $xajax->processRequest();
?>