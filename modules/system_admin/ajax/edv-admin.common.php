<?php
	//require_once ($root"../../xajax.inc.php");
	require('./roots.php');	
	require_once($root_path.'classes/xajax/xajax.inc.php');	
	$xajax = new xajax($root_path."modules/system_admin/ajax/edv-admin.server.php");
	$xajax->registerFunction("deleteOccupationItem");
	$xajax->registerFunction("deleteReligionItem");
	$xajax->registerFunction("deleteEthnicItem");
?>