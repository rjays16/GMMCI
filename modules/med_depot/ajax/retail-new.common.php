<?php
	//require_once ($root"../../xajax.inc.php");
	require('./roots.php');
	//require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path."modules/med_depot/ajax/retail-new.server.php");
	$xajax->registerFunction("populateProductList");
	$xajax->registerFunction("addTransactionDetail");
	$xajax->registerFunction("delTransactionDetail");
	$xajax->registerFunction("populateDetails");
	$xajax->registerFunction("populateDiscountSelection");
	$xajax->registerFunction("addRetailDiscount");
	$xajax->registerFunction("populateRetailDiscounts");
	$xajax->registerFunction("rmvRetailDiscount");
?>