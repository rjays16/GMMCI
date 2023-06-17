<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	$xajax = new xajax($root_path."modules/social_service/ajax/social_add_server_ajx.php");
	#$xajax = new xajax($root_path."modules/social_service/ajax/social_server_ajx.php");										
	//$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("processForm");
	$xajax->registerFunction("refresh");
	//$xajax->registerFunction("updateData");

?>