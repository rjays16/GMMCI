<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/radiology/ajax/radio-finding.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->registerFunction("populateRadioFinding");
	$xajax->registerFunction("saveRadioFinding");
	$xajax->registerFunction("updateRadioFinding");
	$xajax->registerFunction("deleteRadioFinding");
	$xajax->registerFunction("referralRadioFinding");
	$xajax->registerFunction("saveOnlyRadioFinding");
	$xajax->registerFunction("saveAndDoneRadioFinding");
	$xajax->registerFunction("setDoctor");
	$xajax->registerFunction("setRadioStatus");
    $xajax->registerFunction("setDoctorNr");
    $xajax->registerFunction("getRadioDoctor");

    $xajax->registerFunction("saveAddImpression");
?>