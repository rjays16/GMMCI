<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/system_admin/ajax/seg_override_server.php");

	$xajax->setCharEncoding("iso-8859-1");
	$xajax->register(XAJAX_FUNCTION, "populateRequests");

?>