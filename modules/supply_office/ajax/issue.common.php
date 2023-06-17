<?php
    require('./roots.php');
    
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/supply_office/ajax/issue.server.php");
    
    $xajax->setCharEncoding("ISO-8859-1");
    #$xajax->configure('debug',true); 
    $xajax->register(XAJAX_FUNCTION, "add_item");
    $xajax->register(XAJAX_FUNCTION, "getRequestedAreasIss");
    $xajax->register(XAJAX_FUNCTION, "getSourceAreasbypidIss");
    $xajax->register(XAJAX_FUNCTION, "reset_referenceno");

    $xajax->register(XAJAX_FUNCTION, "getAvailableQty"); //Added by 11/04/2014
    $xajax->register(XAJAX_FUNCTION, "getPendingQty"); //Added by 11/04/2014
    $xajax->register(XAJAX_FUNCTION, "getAvgCost"); //Added by 11/04/2014
    $xajax->register(XAJAX_FUNCTION, "getTotalCost"); //Added by 11/04/2014
	$xajax->register(XAJAX_FUNCTION, "populateIssueProduct"); //Added by Jarel 11/04/2014
    
?>
