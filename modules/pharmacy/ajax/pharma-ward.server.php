<?php
	function newWard($name) {
		global $db;
		$objResponse = new xajaxResponse();
		$ward=&new SegPharmaWard();
		$ergebnis = $ward->createPharmaWard($name);
		if ($ergebnis) {
			$data = (object) 'details';
			$data->id = $ergebnis;
			$data->name = $name;
			$objResponse->addScriptCall("prepareAdd", $data);
		}
		else {
			$objAlert("Database error occurred:\n".$ward->sql);
		}
		return $objResponse;
	}
	
	function editWard($id,$name) {
		global $db;
		$objResponse = new xajaxResponse();
		$ward=&new SegPharmaWard();
		$ergebnis = $ward->updatePharmaWard($id,$name);
		if ($ergebnis) {
			$objResponse->addScriptCall("updateWard", $id, $name);
		}
		else {
			$objResponse->addAlert("Database error occurred:\n".$ward->sql);
		}
		return $objResponse;
	}
	
	function deleteWard($id) {
		global $db;
		$objResponse = new xajaxResponse();
		$ward=&new SegPharmaWard();
		$ergebnis = $ward->deletePharmaWard($id);
		if ($ergebnis) {
			$objResponse->addScriptCall("removeItem", $id);
		}
		else {
			$objResponse->addAlert("Database error occurred:\n".$ward->sql);
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require($root_path.'include/care_api_classes/class_pharma_ward.php');
	require($root_path."modules/pharmacy/ajax/pharma-ward.common.php");
	$xajax->processRequests();
?>