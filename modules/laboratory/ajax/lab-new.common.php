<?php
	//require_once ($root"../../xajax.inc.php");
	require('./roots.php');
	//require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	#require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	$xajax = new xajax($root_path."modules/laboratory/ajax/lab-new.server.php");
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

		$xajax->registerFunction("saveOfficialResult");

	$xajax->registerFunction("saveProcessRequest");

	$xajax->registerFunction("getDeptDocValues");

	$xajax->registerFunction("populate_promissory_note");
	$xajax->registerFunction("populateServedRequestList");

	$xajax->registerFunction("savedSentOutRequest");

	#added by Raissa 04-03-09
	$xajax->registerFunction("populateResultList");
	#added by Raissa 04-24-09
	$xajax->registerFunction("PopulateUndoneRequests");
	$xajax->registerFunction("ColHeaderUndoneRequest");
	$xajax->registerFunction("PaginateUndoneRequestList");
	#added by Raissa 04-29-09
	$xajax->registerFunction("PopulateRequests");
	$xajax->registerFunction("ColHeaderRequest");
	$xajax->registerFunction("PaginateRequestList");
	#added by Raissa 05-28-09
	$xajax->registerFunction("populateServiceListByGroup");

	#added by VAN 10-02-09
	$xajax->registerFunction("getAllServiceOfPackage");

	#added by VAN 01-09-10
	$xajax->registerFunction("servedRequest");

	$xajax->registerFunction("saveOfficialResult");

	#added by CHA, March 20, 2010
	$xajax->registerFunction("populate_lab_checklist");

	$xajax->registerFunction("populateLabRequestList");
    $xajax->registerFunction("populateLabResultList");
  	
  	$xajax->registerFunction("serviceByGroup"); #added by daryl
/*
	$xajax->registerFunction("addTransactionDetail");
	$xajax->registerFunction("delTransactionDetail");
	$xajax->registerFunction("populateDetails");
	$xajax->registerFunction("populateDiscountSelection");
	$xajax->registerFunction("addRetailDiscount");
	$xajax->registerFunction("populateRetailDiscounts");
	$xajax->registerFunction("rmvRetailDiscount"); */
?>