<?php
require('./roots.php');
require_once($root_path.'classes/xajax/xajax.inc.php');
//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/radiology/ajax/radio-schedule-server.php');

	//register a function here for xajax script
	$xajax->registerFunction("populateScheduledList");
	$xajax->registerFunction("deleteScheduledRadioRequest");
	$xajax->setCharEncoding("iso-8859-1");
    
    $xajax->registerFunction("savedServedPatient"); 
	
?>