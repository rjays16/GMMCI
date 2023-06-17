<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/cashier/ajax/or-assignment.server.php");
//$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "unlockitem");
$xajax->register(XAJAX_FUNCTION, "lockitem");
$xajax->register(XAJAX_FUNCTION, "updateFilterOption");
$xajax->register(XAJAX_FUNCTION, "updateFilterTrackers");
$xajax->register(XAJAX_FUNCTION, "updatePageTracker");
$xajax->register(XAJAX_FUNCTION, "clearFilterTrackers");
$xajax->register(XAJAX_FUNCTION, "clearPageTracker");
$xajax->register(XAJAX_FUNCTION, "deleteORAssign");

//added by Francis 7-27-13
$xajax->register(XAJAX_FUNCTION, "deleteprintersetup");
$xajax->register(XAJAX_FUNCTION, "addPrinter");
?>