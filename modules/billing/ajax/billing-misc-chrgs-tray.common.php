<?php	
	require('./roots.php');
	require_once($root_path.'classes/xajax/xajax.inc.php');
	$xajax = new xajax($root_path.'modules/billing/ajax/billing-misc-chrgs-tray.server.php');
	
	$xajax->registerFunction("populateChrgsList");
?>