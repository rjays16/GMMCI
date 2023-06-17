<?php
	require('./roots.php');
	#commented out bry bryan on Sept 15, 2008
	/*require_once($root_path.'classes/xajax/xajax.inc.php');*/
	
	#added by bryan on Sept 15, 2008
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/sponsor/ajax/sponsor.server.php");
	
	$xajax->setCharEncoding("ISO-8859-1");

	$xajax->register(XAJAX_FUNCTION, "populatePatientRequestList");
  $xajax->register(XAJAX_FUNCTION, "populatePatientBillingAccounts");
  
  $xajax->register(XAJAX_FUNCTION, "populateBillingBreakdown");
  $xajax->register(XAJAX_FUNCTION, "populateBreakdownDetails");
  
  $xajax->register(XAJAX_FUNCTION, "populateGrants");
  
  $xajax->register(XAJAX_FUNCTION, "refreshTotalGrant");
  $xajax->register(XAJAX_FUNCTION, "addGrant");
  $xajax->register(XAJAX_FUNCTION, "deleteGrant");
  
  $xajax->register(XAJAX_FUNCTION, "populateBillGrantAccounts");
?>