<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/billing/ajax/billing-hsearch.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->registerFunction("populateInsuranceList");
//	$xajax->registerFunction("getHCareID");
?>
