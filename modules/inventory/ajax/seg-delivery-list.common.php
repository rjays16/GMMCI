<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/inventory/ajax/seg-delivery-list.server.php");

$xajax->setCharEncoding("ISO-8859-1");
# $xajax->configure('debug',true); 
#$xajax->register(XAJAX_FUNCTION, "populateProductList");  
    
$xajax->register(XAJAX_FUNCTION, "updateFilterTrackers");
$xajax->register(XAJAX_FUNCTION, "updatePageTracker");
$xajax->register(XAJAX_FUNCTION, "clearFilterTrackers");
$xajax->register(XAJAX_FUNCTION, "clearPageTracker");
$xajax->register(XAJAX_FUNCTION, "updateFilterOption");              
?>
