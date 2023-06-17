<?php
	function reset_nr() {
		global $db;
		$objResponse = new xajaxResponse();
		$cm = new SegCreditMemo();
		$lastnr = $cm->getLastNr();
		if ($lastnr)
			$objResponse->addScriptCall("resetNr",$lastnr);
		else
			$objResponse->addScriptCall("resetNr","Error!",1);
		return $objResponse;
	}
	
	function populate_items( $nr, $disabled=NULL ) {
		global $db;
		$objResponse = new xajaxResponse();
		$cm = new SegCreditMemo();		
		$result = $cm->GetMemoDetails($nr);
		$objResponse->addScriptCall("clearItems",NULL);
		$rows = 0;
		if ($result) {
			$rows=$result->RecordCount();
		 	while ($row=$result->FetchRow()) {
				$obj = (object) 'details';
				$obj->id = $row["service_code"];
				$obj->orno = $row["or_no"];
				$obj->src = $row["ref_source"];
				$obj->ref = $row["ref_no"];				
				$obj->name= $row["service_name"];
				$obj->desc= $row["service_desc"];
				$obj->previous = $row["previous"];
				$obj->price = $row["price"];
				$obj->qty = $row["quantity"];
				$obj->refund = $row["refund"];
				$objResponse->addScriptCall("appendItem", NULL, $obj, $disabled);
			}
			if (!$rows) $objResponse->addScriptCall("appendItem",NULL,NULL);
		}
		else {			
			if (true) {
				$objResponse->addScriptCall("display",$order_obj->sql);
				# $objResponse->addAlert($sql);
			}
			else {
				$objResponse->addAlert("A database error has occurred. Please contact your system administrator...");
			}
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_credit_memo.php');
	require($root_path."modules/cashier/ajax/memo.common.php");
	$xajax->processRequests();
?>