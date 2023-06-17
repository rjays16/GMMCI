<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	$xajax = new xajax($root_path."modules/or/ajax/ajax_report_server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->register(XAJAX_FUNCTION, "getData");
?>
