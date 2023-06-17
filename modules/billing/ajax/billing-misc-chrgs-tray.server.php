<?php
	function populateChrgsList($sElem,$searchkey,$page){
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$objchrg = new SegCashierService('miscellaneous');

		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);

		$types = $objchrg->getPayWardSubTypes();
		if (!$types)
			$seltypes = array();
		else
			foreach($types as $v) {
				$seltypes[] = $v['type_id'];
			}
		
		$result =$objchrg->searchServices($searchkey, $seltypes, $include_locked=FALSE, $maxRows * $page, $maxRows);
		$total = $objchrg->FoundRows();

		$lastPage = floor($total/$maxRows);

		if ((floor($total%$maxRows))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;

		$rows=0;
		$count=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","miscellaneous-list");
		if ($result) {
			$rows=$result->RecordCount();
			while($row=$result->FetchRow()) {
				$code  = trim($row['code']);
				$scode = str_pad($row["code"],6,'0',STR_PAD_LEFT);
				$description = $row['description'] ? $row['description'] : '<em>No description</em>';
				$class = (($count%2)==0)?"":"wardlistrow2";

				$objResponse->addScriptCall("addMiscItemToList","miscellaneous-list", $code, $scode, $row['name'], $description, $row['account_type'], $row["price"],$row["ptype_name"], $row["type_name"], $class);

				$count++;
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addMiscItemToList","miscellaneous-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}
	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');

	include_once($root_path."include/care_api_classes/class_cashier_service.php");
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');

	require($root_path."modules/billing/ajax/billing-misc-chrgs-tray.common.php");
	$xajax->processRequests();
?>