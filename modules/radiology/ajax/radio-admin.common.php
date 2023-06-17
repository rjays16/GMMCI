<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	#$xajax = new xajax($root_path."modules/radiology/ajax/radio-admin.server.php");
	#echo "root_path = ".$root_path;
	$xajax = new xajax($root_path."modules/radiology/ajax/radio-admin.server.php");
	#$xajax = new xajax("../../../modules/radiology/ajax/radio-admin.server.php");
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("psrv");
	$xajax->registerFunction("nsrv");
	$xajax->registerFunction("dsrv");
	$xajax->registerFunction("getServiceGroup");
	$xajax->registerFunction("populateRadioServiceList");
	$xajax->registerFunction("deleteService");
	$xajax->registerFunction("populateRadioGroupList");
	$xajax->registerFunction("deleteRadioGroup");
	
	#added by VAN 07-07-08
	$xajax->registerFunction("populateRadioFindingsList");
	$xajax->registerFunction("deleteRadioFindings");
	
	$xajax->registerFunction("populateRadioImpressionList");
	$xajax->registerFunction("deleteRadioImpression");
	
	$xajax->registerFunction("populateRadioPartnersList");                         
	$xajax->registerFunction("deleteRadioPartners");       
	
	#added by celsy 08/11/10
	$xajax->registerFunction("setImpression");       
?>