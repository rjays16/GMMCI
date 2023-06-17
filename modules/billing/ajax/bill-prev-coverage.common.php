<?php
require('roots.php');
require_once($root_path.'classes/xajax/xajax.inc.php');
$xajax = new xajax($root_path.'modules/billing/ajax/bill-prev-coverage.server.php');
$xajax->setCharEncoding("ISO-8859-1");

$xajax->registerFunction("getHealthInsurancesForEdit");
$xajax->registerFunction("getHealthInsurancesForViewing");
$xajax->registerFunction("showPrevCoverageDetails");
$xajax->registerFunction("delPrevCoverageDetail");
?>
