<?php

	function populateTrayList($sElem, $keyword, $is_refund, $page) {
		global $db;
		global $root_path;
		$objResponse = new xajaxResponse();

		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = 10;

		if ($is_refund) {
			require_once($root_path.'include/care_api_classes/class_cashier.php');
			$cc = new SegCashier();

			$filters = array();
			$filters['OR+NAME'] = $keyword;
			//$filters['DAYSAGO'] = 15;
			$filters['NOCANCEL'] = 1;
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
					$obj->mode = 'or';
					$obj->iscash = 1;
					$obj->paid = 1;
					$obj->ssamount = -1;
					$obj->istpl = 0;
					$objResponse->addScriptCall("addToList", NULL, $obj);
				}
			}
		}
		else {
			require_once($root_path.'include/care_api_classes/class_order.php');
			$oc = new SegOrder('pharma');

			$filters = array();
			$filters['REFNO+NAME'] = $keyword;
			//$filters['DAYSAGO'] = 15;
			$filters['NOPAY'] = 1;
			$filters['WITHTOTALS'] = 1;

			$offset = $page * $maxRows;
			#$result = $oc->getServeReadyOrders($filters, $offset, $maxRows);
			#edited by VAN 12-22-08
			$result = $oc->getServeReadyOrders($filters, $offset, $maxRows, 1);
			//$objResponse->addAlert(print_r($oc->sql,TRUE));
			#return $objResponse;
			if ($result) {
				$total = $oc->FoundRows();
				$lastPage = floor($total/$maxRows);
				if ($page > $lastPage) $page=$lastPage;

				$rows=$result->RecordCount();
				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList",NULL);
				while($row=$result->FetchRow()) {
					$obj = (object) 'details';
					$obj->id = $row["refno"];
					$obj->date = date("Y-m-d",strtotime($row["orderdate"]));

					//$obj->name = $row["ordername"]; #modified by cha, july 8, 2010
					$obj->name = $row["name"];

					$obj->items = $row["items"];
					$obj->item_count = $row["items"] ? (substr_count($row["items"],"\n")+1) : 0;
					$obj->type_main = $row["type_main"];
					$obj->type_sub = $row["area_full"];
					$obj->amount = $row["amount_due"];
					$obj->mode = 'ref';
					$obj->iscash = $row['is_cash'];
					$obj->paid = 0;
					$obj->ssamount = (is_numeric($row['ss_amount']) ? ((float) $row['ss_amount']) : -1);
					$obj->istpl = $row['is_tpl'];
					//$objResponse->addAlert(print_r($obj,TRUE));
					$objResponse->addScriptCall("addToList", NULL, $obj);
				}
			}
		}

		if (!$result) {
			define("__DEBUG_MODE",1);
			if (defined("__DEBUG_MODE")) {
				if ($is_refund)
					$objResponse->addScriptCall("display",htmlentities($cc->sql));
				else
					$objResponse->addScriptCall("display",htmlentities($oc->sql));
			}
			else
				$objResponse->addAlert("A database error has occurred. Please contact your system administrator...\n" . $db->ErrorMsg());
		}
		if (!$rows) $objResponse->addScriptCall("addToList",NULL,NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;

	}

	//added by cha, july 8, 2010
	function populatePaymentList($enc_nr, $pid, $page)
	{
		 global $db;
		global $root_path;
		$objResponse = new xajaxResponse();

		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = 10;

		require_once($root_path.'include/care_api_classes/class_cashier.php');
		$cc = new SegCashier();

		$filters = array();
		$filters['NOCANCEL'] = 1;
		$filters['SRC'] = 'PH';
		$filters['PID'] = $pid;
		$filters['INPATIENT'] = $enc_nr;

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
				$obj->mode = 'or';
				$obj->iscash = 1;
				$obj->paid = 1;
				$obj->ssamount = -1;
				$obj->istpl = 0;
				$objResponse->addScriptCall("addToList", NULL, $obj);
			}
		}
		if (!$result) {
			define("__DEBUG_MODE",1);
			if (defined("__DEBUG_MODE")) {
				if ($is_refund)
					$objResponse->addScriptCall("display",htmlentities($cc->sql));
				else
					$objResponse->addScriptCall("display",htmlentities($oc->sql));
			}
			else
				$objResponse->addAlert("A database error has occurred. Please contact your system administrator...\n" . $db->ErrorMsg());
		}
		if (!$rows) $objResponse->addScriptCall("addToList",NULL,NULL);
		$objResponse->addAssign("ajax-loading","style.display","none");
		return $objResponse;
	}

	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require($root_path."modules/pharmacy/ajax/return-tray.common.php");
	$xajax->processRequests();
?>