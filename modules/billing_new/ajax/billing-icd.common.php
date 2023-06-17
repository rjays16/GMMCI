<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/billing_new/ajax/billing-icd.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->registerFunction("populateDiagnosisList");
    $xajax->registerFunction("addCode");
    $xajax->registerFunction("rmvCode"); 
    $xajax->registerFunction("saveAltDesc");    
    $xajax->registerFunction("updateAltICD"); //added by jasper 04/24/2013
    $xajax->registerFunction("saveAltCode"); //added by jasper 06/30/2013
?>