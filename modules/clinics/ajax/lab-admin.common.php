<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/clinics/ajax/lab-admin.server.php");
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("psrv");
	$xajax->registerFunction("nsrv");
	$xajax->registerFunction("dsrv");
	$xajax->registerFunction("getServiceGroup");
	$xajax->registerFunction("getLabListReq");
	$xajax->registerFunction("drequestor");
	#added by VAN 03-10-08
	$xajax->registerFunction("populateLabGroupList");
	$xajax->registerFunction("deleteLabGroup");
	#$xajax->registerFunction("saveLabGroup");
	$xajax->registerFunction("populateLabServiceList");
	$xajax->registerFunction("deleteService");
?>