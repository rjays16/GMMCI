<?php
require('roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/billing_new/ajax/billing-dialysis.server.php');

$xajax->setCharEncoding("ISO-8859-1");

$xajax->register(XAJAX_FUNCTION, "getSessions");
$xajax->register(XAJAX_FUNCTION, "getEncData");
$xajax->register(XAJAX_FUNCTION, "updateCycle");
$xajax->register(XAJAX_FUNCTION, "saveCycle");
$xajax->register(XAJAX_FUNCTION, "checkTransmittal");
?>