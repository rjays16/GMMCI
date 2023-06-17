<?php
	//require_once ($root"../../xajax.inc.php");
	require('./roots.php');	
	require_once($root_path.'classes/xajax/xajax.inc.php');	
	$xajax = new xajax($root_path."modules/system_admin/ajax/discount.server.php");
	$xajax->registerFunction("listDiscounts");
	$xajax->registerFunction("newDiscount");
	$xajax->registerFunction("updDiscount");
	$xajax->registerFunction("delDiscount");
	$xajax->registerFunction("saveBillAreas");
	//$xajax->debugOn();
?>