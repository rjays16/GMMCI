<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/dialysis/ajax/dialysis-transaction.server.php');
$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "getDoctors");
$xajax->register(XAJAX_FUNCTION, "getNurses");
$xajax->register(XAJAX_FUNCTION, "setVisitNo");
$xajax->register(XAJAX_FUNCTION, "populatePersonList");
$xajax->register(XAJAX_FUNCTION, "populateMiscRequests");
$xajax->register(XAJAX_FUNCTION, "populateIpRequests");
$xajax->register(XAJAX_FUNCTION, "populateMgRequests");
$xajax->register(XAJAX_FUNCTION, "populateLabRequests");
$xajax->register(XAJAX_FUNCTION, "populateBloodRequests");
$xajax->register(XAJAX_FUNCTION, "populateRadioRequests");
$xajax->register(XAJAX_FUNCTION, "deleteRequest");
$xajax->register(XAJAX_FUNCTION, "deleteRadioServiceRequest");
$xajax->register(XAJAX_FUNCTION, "deleteOrder");
$xajax->register(XAJAX_FUNCTION, "deleteMiscRequest");
$xajax->register(XAJAX_FUNCTION, "changeTransactionStatus");
$xajax->register(XAJAX_FUNCTION, "populateSpLabRequests");
$xajax->register(XAJAX_FUNCTION, "deleteDialysisRequest");
$xajax->register(XAJAX_FUNCTION, "deleteBill");
?>
