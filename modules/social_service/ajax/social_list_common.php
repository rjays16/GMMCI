<?php
	require('./roots.php');
	#require_once($root_path.'classes/xajax/xajax.inc.php');
	require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	
	#$xajax = new xajax($root_path."modules/laboratory/ajax/lab-new.server.php");
	$xajax = new xajax($root_path."modules/social_service/ajax/social_list_server.php");
	
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->registerFunction("populateServiceGroups");
	$xajax->registerFunction("populateServices");
	$xajax->registerFunction("srvGui");
	$xajax->registerFunction("getAjxGui");
	$xajax->registerFunction("populateLabServiceList");
	#$xajax->registerFunction("deleteRequest");
	$xajax->registerFunction("populateRequestList");
	
	#$xajax->registerFunction("setALLDepartment");
	#$xajax->registerFunction("setDepartmentOfDoc");
	#$xajax->registerFunction("setDoctors");
	
	#---- added by VAN 11-08-07----------
	$xajax->registerFunction("populateOrderList");
	
?>