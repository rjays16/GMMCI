<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/laboratory/ajax/lab-post.server.php");
	$xajax->setCharEncoding("ISO-8859-1");

	$xajax->register(XAJAX_FUNCTION, "populateRequestListSerialItem");
    $xajax->register(XAJAX_FUNCTION, "submitrequest");
    $xajax->register(XAJAX_FUNCTION, "deleterequest");
?>