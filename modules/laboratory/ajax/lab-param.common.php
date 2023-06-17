<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/laboratory/ajax/lab-param.server.php");
	$xajax->registerFunction("lsrv");
	$xajax->registerFunction("nparam");
	$xajax->registerFunction("pparam");
	$xajax->registerFunction("loadsrvparam");
?>