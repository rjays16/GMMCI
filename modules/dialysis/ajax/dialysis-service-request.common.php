<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/dialysis/ajax/dialysis-service-request.server.php');
$xajax->setCharEncoding("ISO-8859-1");

$xajax->register(XAJAX_FUNCTION, "populateLabServiceList");
$xajax->register(XAJAX_FUNCTION, "getAllServiceOfPackage");
$xajax->register(XAJAX_FUNCTION, "populateBloodServiceList");
$xajax->register(XAJAX_FUNCTION, "getAllBloodServiceOfPackage");
$xajax->register(XAJAX_FUNCTION, "populateRadioServiceList");
$xajax->register(XAJAX_FUNCTION, "populateProductList");
$xajax->register(XAJAX_FUNCTION, "populateMiscServiceList");
?>
