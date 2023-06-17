<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/insurance_co/ajax/hcplan-admin.server.php");	
	$xajax->setCharEncoding("iso-8859-1");
//	$xajax->registerFunction("getHealthInsurances");
//	$xajax->registerFunction("delHealthPlan");

	$xajax->registerFunction("getCarePersons");
	$xajax->registerFunction("getResultsetMaxRows");
	
	$xajax->registerFunction("PopulateHealthPlanList");
	
	#---------added by VAN-------------
	$xajax->registerFunction("populateInsuranceList");
	$xajax->registerFunction("deleteInsurance");
	$xajax->registerFunction("populateConfinementBenefit");
	$xajax->registerFunction("populateRoomTypeBenefit");
	$xajax->registerFunction("populateRVUBenefit");
	$xajax->registerFunction("populateItemBenefit");
	$xajax->registerFunction("getBenefitSked");
	$xajax->registerFunction("getBenefitArea");
	$xajax->registerFunction("deleteBenefitItem");
	$xajax->registerFunction("deleteConfinementItem");
	$xajax->registerFunction("deleteOtherHospServItem");
	
	$xajax->registerFunction("getAllEffDateofBenSked");
	
	$xajax->registerFunction("deleteRoomTypeItem");
    $xajax->registerFunction("deleteEffectivityDateofBsked");        
	#----------------------------------
	$xajax->registerFunction("setOptionRoleLevel");               
    $xajax->registerFunction("checkIfHasRowLevel");
    
    $xajax->registerFunction("showAssocTabs");
    $xajax->registerFunction("populatePkgsWithBenefit");
    	
	$xajax->processRequests();
?>