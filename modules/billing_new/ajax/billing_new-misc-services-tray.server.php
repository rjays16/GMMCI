<?php
	function populateServicesList($sElem,$searchkey,$page){
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$objsrv = new SegMiscService;

		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);

		$result =$objsrv->searchServices($searchkey, NULL, $include_locked=FALSE, $maxRows * $page, $maxRows);
		//$objResponse->alert($objsrv->sql);
		$total = $objsrv->FoundRows();

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
				if($row['source']!= "MISC"){ # Added by Jarel Remove Adding Misc. In XLO
					$details->code = trim($row['item_code']);
					$details->name = $row['item_name'];
					$details->price = $row['price'];
					$details->scode = str_pad($row['item_code'],12,'0',STR_PAD_LEFT);
					$details->account_type = $row['account_type'];
					$details->ptype_name = $row['ptype_name'];
					$details->type_name = $row['type_name'];
					$details->class = (($count%2)==0)?"":"wardlistrow2";;
					$details->source = $row['source']== "MISC" ? 'Miscellaneous' : 'Pharmacy';
					$objResponse->call("addMiscSrvcToList", $details);
					$count++;
				}# End if

			}#end of while
		} #end of if

		if (!$rows) $objResponse->call("addMiscSrvcToList",0,0,0,0,0,0,0,NULL);
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function getPharmaAreas(){
		$objResponse = new xajaxResponse();
		$objsrv = new Billing();

		$result = $objsrv->getPharmaAreas();
		if($result){
			$objResponse->call("js_ClearOptions", "area_combo");
             $objResponse->call("js_AddOptions","area_combo", "- Select Pharmacy Area -", "-");
			while($row=$result->FetchRow()) {
			 	$objResponse->call("js_AddOptions","area_combo", $row["area_name"], $row["area_code"]);
			}
		}
		return $objResponse;
	}
	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');

	include_once($root_path."include/care_api_classes/billing/class_misc_service_new.php");
	require_once($root_path.'include/care_api_classes/billing/class_billing_new.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');

	require($root_path."modules/billing_new/ajax/billing_new-misc-services-tray.common.php");
	$xajax->processRequest();
?>