<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/laboratory/ajax/lab-admin.server.php");
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
    
    $xajax->registerFunction("populateLabReagentsList");
    $xajax->registerFunction("deleteLabReagent");
    
    $xajax->registerFunction("populateServiceReagentsList");
    $xajax->registerFunction("populateReagentList");
    
    #added by Raissa 02-04-09
    $xajax->registerFunction("populateLabTestParametersList");
    #added by Raissa 05-27-09
    $xajax->registerFunction("deleteTestGroup");
    $xajax->registerFunction("populateLabTestGroups");
?>
