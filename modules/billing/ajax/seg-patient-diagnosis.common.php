<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path.'modules/billing/ajax/seg-patient-diagnosis.server.php');
		
	$xajax->register(XAJAX_FUNCTION, "addDiagnosis");       
	$xajax->register(XAJAX_FUNCTION, "remDiagnosis");  
	$xajax->register(XAJAX_FUNCTION, "updateDiagnosis");     
	$xajax->register(XAJAX_FUNCTION, "remDiagnosis");
	$xajax->register(XAJAX_FUNCTION, "populateFinalDiagnosisList");
?>