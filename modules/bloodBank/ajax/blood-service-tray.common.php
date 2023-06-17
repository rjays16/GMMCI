<?php	
	require('./roots.php');
#	require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path.'modules/bloodBank/ajax/blood-service-tray.server.php');
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("populateLabServiceList");
	$xajax->registerFunction("setALLDepartment");
	$xajax->registerFunction("setDepartmentOfDoc");
	$xajax->registerFunction("setDoctors");
	
	$xajax->registerFunction("getAllServiceOfPackage");

	$xajax->registerFunction("getDeptDocValues"); 
?>