<?php
require('./roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/radiology/ajax/radio-daily-schedule-server.php');

//register a function here for xajax script
$xajax->registerFunction("populateRadioPatientRecords");
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("setALLDepartment");
	$xajax->registerFunction("setDepartmentOfDoc");
	$xajax->registerFunction("setDoctors");
	$xajax->registerFunction("saveRadioBorrow");
	$xajax->registerFunction("updateRadioBorrow");
	$xajax->registerFunction("updateRadioReturn");
	$xajax->registerFunction("updateRadioDone");
	
?>