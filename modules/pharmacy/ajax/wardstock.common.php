<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/pharmacy/ajax/wardstock.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->registerFunction("populate_stock");
	$xajax->registerFunction("reset_stocknr");
?>