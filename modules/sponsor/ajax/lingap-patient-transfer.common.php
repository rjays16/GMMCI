<?php
	require('./roots.php');
	#commented out bry bryan on Sept 15, 2008
	/*require_once($root_path.'classes/xajax/xajax.inc.php');*/
	
	#added by bryan on Sept 15, 2008
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/sponsor/ajax/lingap-patient-transfer.server.php");
	
	$xajax->setCharEncoding("ISO-8859-1");
  $xajax->register(XAJAX_FUNCTION, "save");
  $xajax->register(XAJAX_FUNCTION, "getBalance");
  
