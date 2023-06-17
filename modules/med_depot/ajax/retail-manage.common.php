<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/med_depot/ajax/retail-manage.server.php");	
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("populatePersonList");
	$xajax->registerFunction("populateTransactions");
	$xajax->registerFunction("populateTransactionsByRefNo");
	$xajax->registerFunction("delTransaction");
?>