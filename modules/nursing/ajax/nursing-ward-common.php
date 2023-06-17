<?php
	#created by VAN 04-08-08
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/nursing/ajax/nursing-ward-server.php");
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("populateWardList");

	$xajax->registerFunction("moveToWaitingList"); //added by Francis 07-18-13
?>