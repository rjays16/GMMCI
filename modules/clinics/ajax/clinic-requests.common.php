<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/clinics/ajax/clinic-requests.server.php');
$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "populateMiscRequests");
$xajax->register(XAJAX_FUNCTION, "populateIpRequests");
$xajax->register(XAJAX_FUNCTION, "populateMgRequests");
$xajax->register(XAJAX_FUNCTION, "populateLabRequests");
$xajax->register(XAJAX_FUNCTION, "populateICLabRequests");
$xajax->register(XAJAX_FUNCTION, "populateBloodRequests");
$xajax->register(XAJAX_FUNCTION, "populateRadioRequests");
$xajax->register(XAJAX_FUNCTION, "deleteRequest");
$xajax->register(XAJAX_FUNCTION, "deleteRadioServiceRequest");
$xajax->register(XAJAX_FUNCTION, "deleteOrder");
$xajax->register(XAJAX_FUNCTION, "deleteMiscRequest");
$xajax->register(XAJAX_FUNCTION, "populateSpLabRequests");
$xajax->register(XAJAX_FUNCTION, "computeTotalPayment");
$xajax->register(XAJAX_FUNCTION, "autoTagging"); //Added by Jarel 11/19/2013
?>
