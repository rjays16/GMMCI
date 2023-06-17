<?php
	if (empty($root_path)) $root_path="../../";
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax("address.server.php");
	$xajax->registerFunction("setAll");
	$xajax->registerFunction("setBarangay");
	$xajax->registerFunction("setMuniCity");
	$xajax->registerFunction("setZipcode");
	$xajax->registerFunction("setProvince");	
	$xajax->registerFunction("setRegion");	
?>