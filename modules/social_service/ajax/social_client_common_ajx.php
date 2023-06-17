<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/social_service/ajax/social_client_server_ajx.php");

//register function here
	//$xajax->registerFunction("ProcessDiscountForm"); //ProcessAddSScForm
/*
	$xajax->registerFunction("ProcessAddSScForm");
	$xajax->registerFunction("UpdateProfileForm");

	$xajax->registerFunction("PopulateSSC");

	$xajax->registerFunction("AddOptions");
	$xajax->registerFunction("js_SetOptionDesc");
	$xajax->registerFunction("OnChangeOptions");

	//added by VAN 05-13-08
	$xajax->registerFunction("setMSS");
	$xajax->registerFunction("AddOptions_modifiers");

	#added by VAN 08-05-08
	$xajax->registerFunction("OnChangeSubOptions");
*/
	$xajax->setCharEncoding("iso-8859-1");
	$xajax->register(XAJAX_FUNCTION, "ProcessAddSScForm");
	$xajax->register(XAJAX_FUNCTION, "UpdateProfileForm");
	$xajax->register(XAJAX_FUNCTION, "PopulateSSC");
	$xajax->register(XAJAX_FUNCTION, "AddOptions");
	$xajax->register(XAJAX_FUNCTION, "js_SetOptionDesc");
	$xajax->register(XAJAX_FUNCTION, "OnChangeOptions");
	$xajax->register(XAJAX_FUNCTION, "setMSS");
	$xajax->register(XAJAX_FUNCTION, "AddOptions_modifiers");
	$xajax->register(XAJAX_FUNCTION, "OnChangeSubOptions");
	$xajax->register(XAJAX_FUNCTION, "populateClassifications");
	$xajax->register(XAJAX_FUNCTION, "populateRequests");
	$xajax->register(XAJAX_FUNCTION, "populateProfile");
	$xajax->register(XAJAX_FUNCTION, "RemoveSocServPatient");   //Added by Cherry 07-21-10
	$xajax->register(XAJAX_FUNCTION, "ViewSocServPatient");  //Added by Cherry 07-23-10
	$xajax->register(XAJAX_FUNCTION, "disableReadonlysegSocservPatient");  //Added by Cherry 07-23-10
    $xajax->register(XAJAX_FUNCTION, "addDependent");//Added by Jarel 03-01-13
    $xajax->register(XAJAX_FUNCTION, "ProcessDeMeData");//Added by Jarel 03-01-13
    $xajax->register(XAJAX_FUNCTION, "getSubClass");//Added by Jarel 03-07-13
    $xajax->register(XAJAX_FUNCTION, "getSubMod");//Added by Jarel 03-07-13
    $xajax->register(XAJAX_FUNCTION, "removeDepedent");//Added by Jarel 03-11-13
    $xajax->register(XAJAX_FUNCTION, "populateDependent");//Added by Jarel 03-11-13
    $xajax->register(XAJAX_FUNCTION, "saveSocialFunctioning");//Added by Jarel 03-20-13
    $xajax->register(XAJAX_FUNCTION, "saveSocialProblem");//Added by Jarel 03-21-13 
    $xajax->register(XAJAX_FUNCTION, "saveSocialFindings");//Added by Jarel 03-21-13 
    $xajax->register(XAJAX_FUNCTION, "saveSocialCase");//Added by Jarel 03-27-13 
    $xajax->register(XAJAX_FUNCTION, "checkEncounterDetails");//Added by Jarel 05-28-13 
    $xajax->register(XAJAX_FUNCTION, "applyConsultation");//Added by Jarel 06/14/2013
    $xajax->register(XAJAX_FUNCTION, "applyConsultationWithAmount");//Added by Jarel 07/24/2013
	#added by VAN 12-08-09
	$xajax->register(XAJAX_FUNCTION, "ajaxApplyBillDiscount");
    $xajax->register(XAJAX_FUNCTION, "ajaxDiscardBillDiscount");
    
    $xajax->register(XAJAX_FUNCTION, "isForNewBilling");
	//$xajax->registerFunction("OccupationOptions");

?>