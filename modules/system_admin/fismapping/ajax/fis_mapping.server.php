<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/system_admin/fismapping/ajax/fis_mapping.common.php');
require_once($root_path.'include/care_api_classes/class_seg_fis_mapping.php');

function SaveMappingWithCostCenter($mapping){
	$objResponse = new xajaxResponse();
	$objFis = new FisMapping();
	
	$result = $objFis->checkExisting($mapping, $hasArea='1');
	if($result){
		if($mapping['area'] == 'LD'){
			$getGroupCode = $objFis->GetGroupCode($mapping);
			$OkUpdate = $objFis->UpdateFisMappingLR($mapping, $getGroupCode, $result);
		}else if($mapping['area'] == 'RD'){
			$getGroupCode = $objFis->GetGroupCode($mapping);
			$OkUpdate = $objFis->UpdateFisMappingLR($mapping, $getGroupCode, $result);
		}else if($mapping['area'] == 'PH'){
			$OkUpdate = $objFis->UpdateFisMappingPT($mapping, $result);
		}else{
			$OkUpdate = $objFis->UpdateFisMappingPT($mapping, $result);
		}
		$objResponse->alert("Successfully Updated!");
	}else{
		if($mapping['area'] == 'LD'){
			$getGroupCode = $objFis->GetGroupCode($mapping);
			$OkInsert = $objFis->InsertFisMappingLR($mapping, $getGroupCode);
		}else if($mapping['area'] == 'RD'){
			$getGroupCode = $objFis->GetGroupCode($mapping);
			$OkInsert = $objFis->InsertFisMappingLR($mapping, $getGroupCode);
		}else if($mapping['area'] == 'PH'){
			$OkInsert = $objFis->InsertFisMappingPT($mapping);
		}else{
			$OkInsert = $objFis->InsertFisMappingPT($mapping);
		}
		if($OkInsert){
			$objResponse->alert("Successfully added!");
		}
	}
	
	return $objResponse;
}

function SaveMappingWithOutCostCenter($mapping){
	$objResponse = new xajaxResponse();
	$objFis = new FisMapping();
	
	$result = $objFis->checkExisting($mapping);
	if($result){
		$OkUpdate = $objFis->UpdateFismappingWCC($mapping, $result);
		if($OkUpdate){
			$objResponse->alert("Successfully Updated!");
		}
	}else{
		
		$OkInsert = $objFis->InsertFisMappingWCC($mapping);
		if($OkInsert){
			$objResponse->alert("Successfully added!");
		}
	}

	return $objResponse;
}

function CheckIfHasAreas($transCode, $area=NULL){
	$objResponse = new xajaxResponse();
	$objFis = new FisMapping();

	$Transaction = $objFis->GetAccountlist($transCode);

	if($area == "Transaction")
		$objResponse->call("DisableCostCenterArea", $Transaction);
	else if($area == "Search")
		$objResponse->call("CheckSearchTransaction", $Transaction[0][2]);

	return $objResponse;
}

$xajax->processRequest();
?>
