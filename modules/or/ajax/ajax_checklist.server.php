<?php
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');              
	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	require_once($root_path.'modules/or/ajax/ajax_checklist.common.php');
	
	
	
	function delete_item(){ 
		global $db;
		$objResponse = new xajaxResponse();  
		$query = "DELETE FROM seg_pharma_orders WHERE refno='$order_id'";
		$result = $db->Execute($query);
		 
		if ($result) {
			$objResponse->alert('Successfully deleted');    
		}
		else {
			$objResponse->alert('Failed to delete');    
		}  
		
		return $objResponse;
	
	}
	$xajax->processRequest();       