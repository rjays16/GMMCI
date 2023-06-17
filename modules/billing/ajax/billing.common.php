<?php
require('roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/billing/ajax/billing.server.php');
//$xajax->configure("debug",true);
$xajax->setCharEncoding("ISO-8859-1");

$xajax->register(XAJAX_FUNCTION, "mainBilling");
$xajax->register(XAJAX_FUNCTION, "clearBilling");
$xajax->register(XAJAX_FUNCTION, "saveThisBilling");
$xajax->register(XAJAX_FUNCTION, "showBilling");
$xajax->register(XAJAX_FUNCTION, "deleteBilling");

$xajax->register(XAJAX_FUNCTION, "setConfinementType");
$xajax->register(XAJAX_FUNCTION, "getConfineType");
$xajax->register(XAJAX_FUNCTION, "rmPrivateDr");
$xajax->register(XAJAX_FUNCTION, "rmDr");

$xajax->register(XAJAX_FUNCTION, "setALLDepartment");
$xajax->register(XAJAX_FUNCTION, "setDoctors");
//$xajax->register(XAJAX_FUNCTION, "getDoctorInfo");
$xajax->register(XAJAX_FUNCTION, "setRoleArea");
$xajax->register(XAJAX_FUNCTION, "setOptionRoleLevel");

$xajax->register(XAJAX_FUNCTION, "ProcessPrivateDrCharge");
$xajax->register(XAJAX_FUNCTION, "chargeMiscProcedure");
$xajax->register(XAJAX_FUNCTION, "delMiscOp");

$xajax->register(XAJAX_FUNCTION, "setMemCategoryOptions");
$xajax->register(XAJAX_FUNCTION, "setMemCategory");
$xajax->register(XAJAX_FUNCTION, "setWardOptions");
$xajax->register(XAJAX_FUNCTION, "setWardRooms");
$xajax->register(XAJAX_FUNCTION, "setORWardOptions");
$xajax->register(XAJAX_FUNCTION, "setORWardRooms");
$xajax->register(XAJAX_FUNCTION, "getRoomRate");

$xajax->register(XAJAX_FUNCTION, "chargeMiscChrg");
$xajax->register(XAJAX_FUNCTION, "chargeMiscService");
$xajax->register(XAJAX_FUNCTION, "saveAccommodation");
$xajax->register(XAJAX_FUNCTION, "delMiscChrg");
$xajax->register(XAJAX_FUNCTION, "delMiscService");
$xajax->register(XAJAX_FUNCTION, "delSupply");
$xajax->register(XAJAX_FUNCTION, "delAccommodation");
$xajax->register(XAJAX_FUNCTION, "delAccom");
$xajax->register(XAJAX_FUNCTION, "chargeMedorSupply");

$xajax->register(XAJAX_FUNCTION, "recalcDiscount");
$xajax->register(XAJAX_FUNCTION, "setCaseType");


$xajax->register(XAJAX_FUNCTION, "saveORAccommodation");
$xajax->register(XAJAX_FUNCTION, "delOpAccommodation");

$xajax->register(XAJAX_FUNCTION, "updateRVUTotal");
$xajax->register(XAJAX_FUNCTION, "toggleMGH");
$xajax->register(XAJAX_FUNCTION, "populatePkgCbo");
$xajax->register(XAJAX_FUNCTION, "showPkgCoveredAmount");
$xajax->register(XAJAX_FUNCTION, "removePkgDist");
$xajax->register(XAJAX_FUNCTION, "removeCoverageAdjustments");
$xajax->register(XAJAX_FUNCTION, "assignDefaultPkgPF");
$xajax->register(XAJAX_FUNCTION, "assignDefaultCharge");
$xajax->register(XAJAX_FUNCTION, "delPostedItemsForDialysisPkg");

$xajax->register(XAJAX_FUNCTION, "computeAccommodation");
$xajax->register(XAJAX_FUNCTION, "computeXLO");
$xajax->register(XAJAX_FUNCTION, "computeDrugsMeds");
$xajax->register(XAJAX_FUNCTION, "computePF");
$xajax->register(XAJAX_FUNCTION, "computeOP");
$xajax->register(XAJAX_FUNCTION, "computeMisc");
$xajax->register(XAJAX_FUNCTION, "computeLastPart");
$xajax->register(XAJAX_FUNCTION, "doLastPartComputation");
$xajax->register(XAJAX_FUNCTION, "setActivityFlag");
$xajax->register(XAJAX_FUNCTION, "updateObjBilling");
$xajax->register(XAJAX_FUNCTION, "clearSessionVars");
$xajax->register(XAJAX_FUNCTION, "checkPHIC");      //added by pol
$xajax->register(XAJAX_FUNCTION, "showCategoryPrompt"); //added by pol
$xajax->register(XAJAX_FUNCTION, "UnsetDeathDate");//added by Jarel 08/27/2013
$xajax->register(XAJAX_FUNCTION, "GetPhicNumber"); //added by pol 07/24/2913
$xajax->register(XAJAX_FUNCTION, "getClerks"); //added by nick 2/1/2014
?>