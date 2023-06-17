<?php
	function populateOLRServiceList($sElem,$keyword,$olr,$page) {
		global $db;
		$objResponse = new xajaxResponse();
		
		$csClass = new SegCashierService();
		
		#$dbtable='seg_other_services';
		#$sql="SELECT * FROM $dbtable WHERE name REGEXP '[[:<:]]$keyword' OR service_code REGEXP '[[:<:]]$keyword' ORDER BY name";		
		
		$maxRows = 10;
		$offset = $page * $maxRows;

		$ergebnis = $csClass->searchOLRServices($keyword, $olr, $offset, $maxRows);
#		$objResponse->addScriptCall('display',$csClass->sql);
#		return $objResponse;
		$rows=$ergebnis->RecordCount();

		$total = $csClass->FoundRows();
		$lastPage = floor($total/$maxRows);
		if ($page > $lastPage) $page=$lastPage;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","service-list");
		while($result=$ergebnis->FetchRow()) {
			$details->id = $result["code"];
			$details->name = $result['name'];
			$details->desc = $result['group'];
			$details->source = strtoupper($result['source']);
			$details->deposit = 0;
			$objResponse->addScriptCall("addServiceToList","service-list",$details);
		}
		if (!$rows) $objResponse->addScriptCall("addServiceToList","service-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_cashier_service.php');
	require($root_path."modules/cashier/ajax/olr-services.common.php");
	$xajax->processRequests();
?>