<?php
	require_once('./roots.php'); 
  require_once($root_path.'classes/xajax/xajax.inc.php');
  $xajax = new xajax($root_path."modules/personell_admin/ajax/accre-insurance.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->registerFunction("populateInsurance");
	
	#added by VAN 11-04-09
	$xajax->registerFunction("setDeactivatePersonnel");
	$xajax->registerFunction("setChangePassword");
?>