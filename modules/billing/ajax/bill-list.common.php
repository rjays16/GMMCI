<?php
require('roots.php');
require_once($root_path.'classes/xajax/xajax.inc.php');
$xajax = new xajax($root_path.'modules/billing/ajax/bill-list.server.php');
$xajax->setCharEncoding("ISO-8859-1");

$xajax->registerFunction("updateFilterTrackers");
$xajax->registerFunction("updatePageTracker");
$xajax->registerFunction("clearFilterTrackers");
$xajax->registerFunction("clearPageTracker");
$xajax->registerFunction("assignToSessionVar");
$xajax->registerFunction("showTransmittalDetails");
$xajax->registerFunction("updateFilterOption");
$xajax->registerFunction("noteSelectedEncounter");
$xajax->registerFunction("toggleTransmittal");
$xajax->registerFunction("addClaimDet");
$xajax->registerFunction("removePromi");
$xajax->registerFunction("untagPF");
?>