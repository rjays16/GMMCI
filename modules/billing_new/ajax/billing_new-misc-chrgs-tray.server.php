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

		$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->call("clearList","miscellaneous-list");
		if ($result) {
			$rows=$result->RecordCount();
			while($row=$result->FetchRow()) {
				$details->code = trim($row['code']);
				$details->scode = str_pad($row["code"],6,'0',STR_PAD_LEFT);
				$details->name = $row['name'];
				$details->description = $row['description'] ? $row['description'] : '<em>No description</em>';
				$details->account_type = $row['account_type'];
				$details->price = $row["price"];
				$details->ptype_name = $row["ptype_name"];
				$details->type_name = $row["type_name"];
				$details->class = (($count%2)==0)?"":"wardlistrow2";
				$objResponse->call("addMiscItemToList", $details);

				$count++;
			}#end of while
		} #end of if

		if (!$rows) $objResponse->call("addMiscItemToList",NULL);
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}
	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');

	include_once($root_path."include/care_api_classes/class_cashier_service.php");
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');

	require($root_path."modules/billing_new/ajax/billing_new-misc-chrgs-tray.common.php");
	$xajax->processRequest();
?>