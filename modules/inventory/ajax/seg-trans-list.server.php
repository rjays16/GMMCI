<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/inventory/ajax/seg-trans-list.common.php');

function updateFilterOption($noption, $skey = '') {
    $objResponse = new xajaxResponse();
    
    $_SESSION["filteroption"][$noption] = $skey;
    
    return $objResponse;
}
    
function updateFilterTrackers($sfiltertype, $ofilter) {
    $objResponse = new xajaxResponse();    
    
    $_SESSION["filtertype"][$sfiltertype] = $sfiltertype;
    $_SESSION["filter"][$sfiltertype] = $ofilter;        
    
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
