<?php
	require_once('./roots.php'); 
    require_once($root_path.'classes/xajax/xajax.inc.php');
    $xajax = new xajax($root_path."modules/laboratory/ajax/lab-service.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->registerFunction("populateLabService");
?>