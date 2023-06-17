<?php
	#edited by VAN 05-05-08
	function populateProductList($sElem,$searchkey,$areas,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$srv=new Insurance;
		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
		
		#$objResponse->addAlert('searchkey,areas,max, off =  '.$searchkey.", ".$areas.", ".$maxRows.", ".$offset);
		$total_srv = $srv->countSearchService($searchkey,$areas,$maxRows,$offset);
		#$objResponse->addAlert($srv->sql);
		$total = $srv->count;
		#$objResponse->addAlert('total = '.$total);
		
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchService($searchkey,$areas,$maxRows,$offset);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","product-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$objResponse->addScriptCall("addProductToList","product-list",trim($result["code"]),trim($result["name"]),substr(trim($result["description"]),0,40),$areas);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	#added by VAN 05-05-08	
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_insurance.php');
	require($root_path."modules/insurance_co/ajax/product-tray.common.php");
	$xajax->processRequests();	
?>