<?php
	//require_once ($root"../../xajax.inc.php");
	require('./roots.php');
	//require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	#require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	$xajax = new xajax($root_path."modules/clinics/ajax/lab-new.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	#$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("populateServiceGroups");
	$xajax->registerFunction("populateServices");
	$xajax->registerFunction("srvGui");
	$xajax->registerFunction("getAjxGui");
	$xajax->registerFunction("populateLabServiceList");
	$xajax->registerFunction("deleteRequest");
	$xajax->registerFunction("populateRequestList");
	$xajax->registerFunction("setALLDepartment");
	$xajax->registerFunction("setDepartmentOfDoc");
	$xajax->registerFunction("setDoctors");
	
	$xajax->registerFunction("populateRequestList2");
	#---- added by VAN 11-08-07----------
	$xajax->registerFunction("populateOrderList");
	
	#added by VAN 08-21-08
	$xajax->registerFunction("savedServedPatient");
    
  #added by VAN 04-13-09
  $xajax->registerFunction("getDeptDocValues");
    
  #added by VAN 10-02-09
  $xajax->registerFunction("getAllServiceOfPackage");
  
    	
/*
	$xajax->registerFunction("addTransactionDetail");
	$xajax->registerFunction("delTransactionDetail");
	$xajax->registerFunction("populateDetails");
	$xajax->registerFunction("populateDiscountSelection");
	$xajax->registerFunction("addRetailDiscount");
	$xajax->registerFunction("populateRetailDiscounts");
	$xajax->registerFunction("rmvRetailDiscount"); */
?>