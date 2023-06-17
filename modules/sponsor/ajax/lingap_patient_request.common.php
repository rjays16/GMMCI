<?php
require './roots.php';
require_once $root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php';
$xajax = new xajax($root_path."modules/sponsor/ajax/lingap_patient_request.server.php");
$xajax->setCharEncoding("ISO-8859-1");
//$xajax->setFlag('debug',true);
$xajax->register(XAJAX_FUNCTION, "populatePatientRequestList");
$xajax->register(XAJAX_FUNCTION, "populateLingapEntries");
$xajax->register(XAJAX_FUNCTION, "populateRequestList");