<?php
	require './roots.php';
	require_once $root_path.'classes/xajax/xajax_core/xajax.inc.php';
	$xajax = new xajax($root_path."modules/sponsor/ajax/cmap_patient.server.php");

	$xajax->setCharEncoding("ISO-8859-1");

	$xajax->register(XAJAX_FUNCTION, "populatePatientRequestList");
	$xajax->register(XAJAX_FUNCTION, "populateLingapEntries");

	$xajax->register(XAJAX_FUNCTION, "populateFundsTransfers");
	$xajax->register(XAJAX_FUNCTION, "deleteReferral");
	$xajax->register(XAJAX_FUNCTION, "updateBalance");
	$xajax->register(XAJAX_FUNCTION, "cancelEntry");



