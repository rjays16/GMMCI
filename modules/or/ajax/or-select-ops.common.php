<?php
		require('./roots.php');
		require_once($root_path.'classes/xajax/xajax.inc.php');
		$xajax = new xajax($root_path.'modules/or/ajax/or-select-ops.server.php');
		
		$xajax->registerFunction("populateAppliedOpsList"); 
		$xajax->registerFunction("addSelectedOP");      
?>
