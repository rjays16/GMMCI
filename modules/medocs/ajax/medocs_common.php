<?php
require('./roots.php');
require_once($root_path.'classes/xajax/xajax.inc.php');
$xajax= new xajax($root_path."modules/medocs/ajax/medocs_server.php");
	$xajax->setCharEncoding("iso-8859-1");
    //ICD, ICP
	$xajax->registerFunction("addCode");
	$xajax->registerFunction("rmvCode");
	$xajax->registerFunction("populateCode");
	$xajax->registerFunction("getTime");

    //Departments and Doctors

	$xajax->registerFunction("setDoctors_d");
	$xajax->registerFunction("setDepartments_d");
	#$xajax->registerFunction("setALLDoctor_d");
	$xajax->registerFunction("setALLDepartment_d");

	$xajax->registerFunction("setDoctors_p");
	$xajax->registerFunction("setDepartments_p");
	#$xajax->registerFunction("setALLDoctor_p");
	$xajax->registerFunction("setALLDepartment_p");

	$xajax->registerFunction("setDoctors_f");
	$xajax->registerFunction("setDepartments_f");
	#$xajax->registerFunction("setALLDoctor_f");
	$xajax->registerFunction("setALLDepartment_f");

	$xajax->registerFunction("setDoctors_c");
	$xajax->registerFunction("setDepartments_c");
	$xajax->registerFunction("setALLDepartment_c");

    //Result,Disposition, condition
	$xajax->registerFunction("setAddBtn");

	$xajax->registerFunction("showDiagnosisTherapy");

	#added by VAN 03-28-08
	$xajax->registerFunction("populateICD_ICP");

	$xajax->registerFunction("saveICDifnotExist");

	#add by Dommie 03-07-14
	$xajax->registerFunction("saveAltDiagDesc");

	#added by VAN 02-18-09
	$xajax->registerFunction("updateReceivedDate");

	#added by VAN 06-08-09
	$xajax->registerFunction("cancelDischarged");
	$xajax->registerFunction("cancelReceived");

    #added by jarel 03-04-2013
    $xajax->registerFunction("cancelDeath");
    
    #added by VAS 12-20-2011
    $xajax->registerFunction("undoCancellation");
    
    #added by shand 05-21-2013
    $xajax->registerFunction("undoMGH");
    $xajax->registerFunction("undoIsfinal");
    
    #--- notification
    $xajax->registerFunction("addNotificationCode");
    $xajax->registerFunction("rmvNotificationCode");
    $xajax->registerFunction("populateNotification");
    $xajax->registerFunction("InsertNotificationCode");
    #-----------
    
?>