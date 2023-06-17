<?php

	function populateORList($sElem, $keyword, $is_refund, $page) {
		global $db;
		$cc = new SegCashier();
		$objResponse = new xajaxResponse();
		
		$objResponse->addAlert(print_r($is_refund,TRUE));
		
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = 10;
		
		#$filters['MODE'] = "OR";
		$filters['OR+NAME'] = $keyword;
		$filters['DAYSAGO'] = 15;
		$filters['SRC'] = 'PH';
		
		$offset = $page * $maxRows;
		$result = $cc->GetPayments($filters, $offset, $maxRows);
		
		if ($result) {
			$total = $cc->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;
			
			$rows=$result->RecordCount();
			
			$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->addScriptCall("clearList",NULL);
			while($row=$result->FetchRow()) {
				if (!$row['items'] && $row['deposit'])  $row['items'] = "Deposit";
			
				$obj = (object) 'details';
				$obj->id = $row["or_no"];
				$obj->date = date("Y-m-d",strtotime($row["or_date"]));
				$obj->name = $row["or_name"];
				$obj->items = $row["items"];
				$obj->item_count = $row["items"] ? (substr_count($row["items"],"\n")+1) : 0;
				$obj->type_main = $row["type_main"];
				$obj->type_sub = $row["type_sub"];
				$obj->amount = $row["amount_due"];
				$objResponse->addScriptCall("addToList", NULL, $obj);
			}
		}
		else {
			define("__DEBUG_MODE",1);
			if (defined("__DEBUG_MODE"))
				$objResponse->addScriptCall("display",$cc->sql);
			else
				$objResponse->addAlert("A database error has occurred. Please contact your system administrator...\n" . $db->ErrorMsg());
		}
		if (!$rows) $objResponse->addScriptCall("addToList",NULL,NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;

	}

	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');	
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_cashier.php');
	require($root_path."modules/pharmacy/ajax/return-or-tray.common.php");
	$xajax->processRequests();	
?>