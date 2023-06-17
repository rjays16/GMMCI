<?php
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	require_once($root_path.'modules/or/ajax/ajax_report_common.php');

	function getData($date){
		 global $db;
		$objResponse = new xajaxResponse();
		$objResponse->alert("meow!");
		//$query = "SELECT * FROM seg_";
		//$result = $db->Execute($query);
	/*
		if ($result) {
			$objResponse->alert('Successfully deleted');
		}
		else {
			$objResponse->alert('Failed to delete');
		}

		return $objResponse;
	}          */

	$xajax->processRequest();

?>
