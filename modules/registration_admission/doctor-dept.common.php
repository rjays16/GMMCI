<?php

#--------------- EDITED BY VANESSA -----------------------

	if (empty($root_path)) $root_path="../../";
	require_once($root_path.'classes/xajax/xajax.inc.php');

	$xajax = new xajax("doctor-dept.server.php");
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("setDoctorsEROPD");
	$xajax->registerFunction("setAllDepartmentEROPD");
	$xajax->registerFunction("setDepartmentEROPD");
	$xajax->registerFunction("setDoctors");
	$xajax->registerFunction("setDepartments");
	//$xajax->registerFunction("setALLDoctor");
	$xajax->registerFunction("setALLDepartment");

	#added by VAN 04-18-2010
	$xajax->registerFunction("setConsultingDoctors");
	$xajax->registerFunction("setConsultingDepartments");
	$xajax->registerFunction("setALLConsultingDepartment");
	$xajax->registerFunction("setALLConsultingDoctor");

	#added by VAN 02-01-08
	$xajax->registerFunction("setRooms");
	$xajax->registerFunction("setBeds");

	$xajax->registerFunction("checkPreviousTrxn");
	$xajax->registerFunction("validateOR");

	#Added by Jarel 03102015
	$xajax->registerFunction("populateEncounterDRAddList");

	$xajax->registerFunction("setReferralReason");

?>