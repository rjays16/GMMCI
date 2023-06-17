<?php	
	require('./roots.php');
#	require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path.'modules/nursing/ajax/nursing-station-radio-tray.server.php');
	
	$xajax->registerFunction("populateRequestList");
	$xajax->registerFunction("setALLDepartment");
	$xajax->registerFunction("setDepartmentOfDoc");
	$xajax->registerFunction("setDoctors");

?>