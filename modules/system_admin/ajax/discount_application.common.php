<?php
	//require_once ($root"../../xajax.inc.php");
	require('./roots.php');	
	require_once($root_path.'classes/xajax/xajax.inc.php');	
	$xajax = new xajax($root_path."modules/system_admin/ajax/discount_application.server.php");
	
	$xajax->registerFunction("getBillAreas");
	$xajax->registerFunction("applyToBillArea");	
	//$xajax->debugOn();
?>
