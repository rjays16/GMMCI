<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/cashier/ajax/or-assignment.common.php');

function unlockitem($login_id, $from_date, $to_date){
    global $db;
    $objResponse = new xajaxResponse();
    
    $sql = "UPDATE seg_assigned_ornos SET is_locked=0 WHERE login_id='$login_id' AND from_date='$from_date' AND to_date='$to_date' AND is_locked=1 AND is_deleted=0";
    //$objResponse->alert($sql);
    $result = $db->Execute($sql);
    
    return $objResponse;
}

function lockitem($login_id, $from_date, $to_date){
    global $db;
    $objResponse = new xajaxResponse();
    
    $sql = "UPDATE seg_assigned_ornos SET is_locked=1 WHERE login_id='$login_id' AND from_date='$from_date' AND to_date='$to_date' AND is_locked=0 AND is_deleted=0";
    //$objResponse->alert($sql);
    $result = $db->Execute($sql);
    
    return $objResponse;
}

function deleteORAssign($login_id, $from_date, $to_date){
    global $db;
    $objResponse = new xajaxResponse();
    
    $sql = "UPDATE seg_assigned_ornos SET is_deleted=1 WHERE login_id='$login_id' AND from_date='$from_date' AND to_date='$to_date' AND is_deleted=0";
    //$objResponse->alert($sql);
    $result = $db->Execute($sql);
    
    return $objResponse;
}

//added by Francis 7-27-13
function deleteprintersetup($ip,$port){
    global $db;
    $objResponse = new xajaxResponse();

    $sql = "DELETE FROM seg_print_default where ip_address='$ip'";
    //$objResponse->alert($sql);
    $result = $db->Execute($sql);
    
    if($result){
        $objResponse->Call("refreshWindow");
    }else{
        $objResponse->alert("Deletion failed");
    }

    return $objResponse;
}

//added by Francis 7-27-13
function addPrinter($ip,$printer){
    global $db;
    $objResponse = new xajaxResponse();

    $ipAdd = $ip;
    $sharedname = '\\\\'.$ipAdd.'\\'.$printer;
    $ipExist = "";

    $sql_exist = "SELECT * FROM seg_print_default WHERE ip_address='$ipAdd'";

    if ($buf=$db->Execute($sql_exist)){
        if($buf->RecordCount()) {
            $ipExist = "Failed to save setup. IP Address is already registered.";
        }
    }

    if(!$ipExist){
        $sql_add = "INSERT INTO seg_print_default (ip_address, printer_port, printer_model) VALUES ('$ipAdd',".$db->qstr($sharedname).",'EPSON-ESCP2')";
        
        if (filter_var($ipAdd, FILTER_VALIDATE_IP) && strlen($printer)<=10) {
            $result = $db->Execute($sql_add);
        }

        if($result){
            $objResponse->alert("Printer setup successfully saved !");
            $objResponse->Call("refreshWindow");

        }else if(!filter_var($ipAdd, FILTER_VALIDATE_IP)){
            
            $objResponse->alert("Failed to save printer setup. Invalid IP Address.");

        }else if(strlen($printer)>10){

            $objResponse->alert("Failed to save printer setup. Printer name too long.");

        }else{
            
            $objResponse->alert("Failed to save printer setup.");
            
        }
    }else{
        $objResponse->alert($ipExist);
    }
    //$objResponse->alert($msg);
    

    return $objResponse;

}

function updateFilterOption($noption, $bchecked) {
    $objResponse = new xajaxResponse();
    
    $_SESSION["filteroption"][$noption] = $bchecked;
    
    return $objResponse;
}
    
function updateFilterTrackers($sfiltertype, $ofilter) {
    $objResponse = new xajaxResponse();
    
    $_SESSION["filtertype"] = $sfiltertype;
    $_SESSION["filter"] = $ofilter;        
    
    return $objResponse;
}

function updatePageTracker($npage) {
    $objResponse = new xajaxResponse();
    $_SESSION["current_page"] = $npage;        
    
#    $objResponse->alert($_SESSION["current_page"]);
    return $objResponse;    
}

function clearFilterTrackers() {
    $objResponse = new xajaxResponse();

    unset($_SESSION["filtertype"]);
    unset($_SESSION["filter"]);

    return $objResponse;    
}

function clearPageTracker() {
    $objResponse = new xajaxResponse();
    unset($_SESSION["current_page"]);
    return $objResponse;    
}

$xajax->processRequest();
?>