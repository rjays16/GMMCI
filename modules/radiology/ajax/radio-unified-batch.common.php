<?php
    /*created by Raissa 02-11-09
    */
    require('./roots.php');
    require_once($root_path.'classes/xajax/xajax.inc.php');
    $xajax = new xajax($root_path."modules/radiology/ajax/radio-unified-batch.server.php");
    $xajax->setCharEncoding("ISO-8859-1");
    $xajax->registerFunction("populateUnifiedBatchList");  
    $xajax->registerFunction("populateUnifiedBatchRequestList");
	
	$xajax->registerFunction("getFindingNr");
?>