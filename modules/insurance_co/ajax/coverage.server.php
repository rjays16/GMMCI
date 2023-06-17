<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path."include/care_api_classes/class_pharma_product.php");
require_once($root_path.'modules/insurance_co/ajax/coverage.common.php');

function clearExistingCoverage($ref_no, $mode='M') {
	global $db;

	$objResponse = new xajaxResponse();

	switch ($mode) {
		case "M": $source = array('M'); break;
		default: $source = array('R','L','O','E','S'); break;
	}
	$sql = "DELETE FROM seg_applied_coverage WHERE ref_no=".$db->qstr($ref_no)." AND source IN ('".implode("','", $source)."')";
	$db->Execute($sql);

	return $objResponse;
}

#Modified by Jarel 10/24/2013 Use multiple insert to fix coverage issue
function saveCoverage($data) {
	global $db;
	$objResponse = new xajaxResponse();
	$saveok=FALSE;
	$data = substr($data, 0, -1);

	if($data){
		$sql = "INSERT INTO seg_applied_coverage(ref_no,source,item_code,hcare_id,coverage,priority) ".
					 "VALUES $data";
		
	    if ($db->Execute($sql)) {
	        if ($db->Affected_Rows()) {
	            $saveok=TRUE;
	        }
	    }
	}else{
		$saveok=TRUE;
	}


	if (!$saveok) {
		$objResponse->alert('Error: '.$db->ErrorMsg()."\n$sql");
	}else{
		$objResponse->alert('Applied coverage saved successfully!');
	}

	return $objResponse;
}

$xajax->processRequest();
?>