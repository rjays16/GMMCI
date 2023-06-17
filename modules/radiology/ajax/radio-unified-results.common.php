<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
    $xajax = new xajax($root_path."modules/radiology/ajax/radio-unified-results.server.php");
    
    $xajax->setCharEncoding("iso-8859-1");
    $xajax->registerFunction("PopulateRadioUndoneRequest");
    $xajax->registerFunction("PopulateRadioUnscheduledRequest");
    $xajax->registerFunction("saveScheduledRequest");
    $xajax->registerFunction("updateScheduledRequest");
    $xajax->registerFunction("deleteScheduledRadioRequest");
    $xajax->registerFunction("PopulateRadioScheduledRequests");
    
    $xajax->registerFunction("populateUnifiedBatchList");
    
    #added by VAN 07-09-08
    $xajax->registerFunction("saveProcessRequest");
    $xajax->registerFunction("updateProcessRequest");
    
?>