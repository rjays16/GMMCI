<?php
    require('./roots.php');
    
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/supply_office/ajax/issue-acknowledge-server.php");
    
    $xajax->setCharEncoding("ISO-8859-1");
    #$xajax->configure('debug',true); 
    $xajax->register(XAJAX_FUNCTION, "populateIssueAcknowledge");
    $xajax->register(XAJAX_FUNCTION, "populateIssueDetailsAck");
    $xajax->register(XAJAX_FUNCTION, "populateTypesCombo");
?>
