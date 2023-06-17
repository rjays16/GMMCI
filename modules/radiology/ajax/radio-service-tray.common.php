<?php
	require('./roots.php');
#	require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path.'modules/radiology/ajax/radio-service-tray.server.php');
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("populateRadioServiceList");
	$xajax->registerFunction("setALLDepartment");
	$xajax->registerFunction("setDepartmentOfDoc");
	$xajax->registerFunction("setDoctors");

    $xajax->registerFunction("setDoctorsCLHis"); //added by Francis 01-25-13

	$xajax->registerFunction("populate_radio_checklist");

	$xajax->registerFunction("populateRadioSections");	//added by cha, june 8, 2010

	$xajax->registerFunction("getAllServiceOfPackage");

	$xajax->registerFunction("getDeptDocValues");

	$xajax->registerFunction("validateImpression");
?>