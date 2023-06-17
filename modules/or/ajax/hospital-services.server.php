<?php
	function populateServiceList($sElem,$keyword,$type,$page) {
		global $db;
		$objResponse = new xajaxResponse();

		$csClass = new SegCashierService();

		#$dbtable='seg_other_services';
		#$sql="SELECT * FROM $dbtable WHERE name REGEXP '[[:<:]]$keyword' OR service_code REGEXP '[[:<:]]$keyword' ORDER BY name";

		$maxRows = 25;
		$offset = $page * $maxRows;

		$ergebnis = $csClass->searchServices($keyword, $type, FALSE, $offset, $maxRows, 's.name,s.price');
		$rows=$ergebnis->RecordCount();
		$total = $csClass->FoundRows();
		$lastPage = floor($total/$maxRows);
		if ($page > $lastPage) $page=$lastPage;

		$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->call("clearList","service-list");
		while($result=$ergebnis->FetchRow()) {
			//$last_code = $result["code"];
			$last_code = $result["alt_code"];
			//$details->id = $result["code"];
			$details->id = $result["alt_code"];
			$details->name = $result['name'];
			$details->desc = $result['name_short'];
			$details->price = $result['price'];
			$objResponse->call("addServiceToList","service-list",$details);
		}
		if (!$rows) $objResponse->call("addServiceToList","service-list",NULL);
		if ($rows==1) {
			$objResponse->call("prepareAdd", $last_code);
		}
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_cashier_service.php');
	require($root_path."modules/or/ajax/hospital-services.common.php");
	$xajax->processRequest();
?>