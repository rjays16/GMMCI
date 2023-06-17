<?php
	if (empty($root_path)) $root_path="../../";
	require_once($root_path.'classes/xajax/xajax.inc.php');
#	$xajax = new xajax("address.server.php");
	$xajax = new xajax($root_path.'modules/registration_admission/address.server.php');
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("setAll");
	$xajax->registerFunction("setBarangay");
	$xajax->registerFunction("setMuniCity");
	$xajax->registerFunction("setZipcode");
	$xajax->registerFunction("setProvince");	
	$xajax->registerFunction("setRegion");	
	
	#added by VAN 05-06-08
	$xajax->registerFunction("checkinDBperson");
	
	#added by VAN 04-29-09
	$xajax->registerFunction("validateDept");
?>