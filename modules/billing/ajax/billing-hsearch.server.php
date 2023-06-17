<?php
	require('./roots.php');
	
	require($root_path.'include/inc_environment_global.php');
	
	require_once($root_path.'include/care_api_classes/class_insurance.php');			
	require_once($root_path.'include/care_api_classes/class_paginator.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');	
    require($root_path."modules/billing/ajax/billing-hsearch.common.php");		
	
	function populateInsuranceList($sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$ins_obj=new Insurance;
		$offset = $page * $maxRows;
		if ($searchkey==NULL)
			$searchkey = '*';
		$total_srv = $ins_obj->countSearchSelect($searchkey,$maxRows,$offset,"name","ASC");
		$total = $ins_obj->count;
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$ins_obj->SearchSelect($searchkey,$maxRows,$offset,"name","ASC");
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","hcplanlistTable");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {			
				$firmId = $result['firm_id'];
 				$firmName = $result['name'];							
				                                                             
			 	$objResponse->addScriptCall("addInsurance","hcplanlistTable",$result["hcare_id"],$firmId,$firmName,$result["phone_main"],$result["fax_main"],$result["addr"],$result['witholding_tax']);			   
			}
		}
		if (!$rows) $objResponse->addScriptCall("addInsurance","hcplanlistTable",NULL);
		if ($sElem) {		
			$objResponse->addScriptCall("endAJAXSearch", $sElem);
		}
		
		return $objResponse;
	}	
	
//	function getHCareID($nhcareid) {
//		$objResponse = new xajaxResponse();
//		$_SESSION["current_hcare_id"] = $nhcareid;
//		return $objResponse;	
//	}			
	
	$xajax->processRequests();
?>