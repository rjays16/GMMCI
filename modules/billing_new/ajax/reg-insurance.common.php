<?php
	require('./roots.php');
//    require_once($root_path.'classes/xajax/xajax.inc.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/billing_new/ajax/reg-insurance.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
    
//    $xajax->registerFunction("populateInsurance");
//    $xajax->registerFunction("check_holder_data"); //added by Omick, June 10, 2009 1627
//    $xajax->registerFunction("getMuniCityandProv"); //added by Omick, June 10, 2009 1316
//    $xajax->registerFunction("get_barangay_municipality_name"); //added by Omick, June 13, 2009 2339
//    $xajax->registerFunction("goVerify");
    
    $xajax->register(XAJAX_FUNCTION, "populateInsurance");
    $xajax->register(XAJAX_FUNCTION, "check_holder_data");    
    $xajax->register(XAJAX_FUNCTION, "getMuniCityandProv");
    $xajax->register(XAJAX_FUNCTION, "get_barangay_municipality_name");  
    $xajax->register(XAJAX_FUNCTION, "goVerify");
    $xajax->register(XAJAX_FUNCTION, "saveMemberInfo");
?>