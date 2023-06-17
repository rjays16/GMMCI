<?php
	#edited by VAN 04-17-08
	function populateLabService($sElem,$grpcode,$is_param,$searchkey, $page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_insurance_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_insurance_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv=new SegLab;
		$offset = $page * $maxRows;

		if ($grpcode=='B'){
			$frommgr = 1;
			$grp = $grpcode;
		}else{
			$frommgr = 0;
			$grp = '';
		}
		$area = '';

		$searchkey = utf8_decode($searchkey);
		#$objResponse->addAlert('keyword = '. $searchkey);
		if (stristr($searchkey,",")){
				$keyword_multiple = explode(",",$searchkey);

				for ($i=0;$i<sizeof($keyword_multiple);$i++){
						$keyword .= "'".trim($keyword_multiple[$i])."',";
				}
				#$objResponse->addAlert('keyword1 = '.$keyword);
				$word = trim($keyword);
				#$objResponse->addAlert('word = '.$word);
				$searchkey = substr($word,0,strlen($word)-1);
				#$objResponse->addAlert('keyword = '.$keyword);
				$multiple = 1;
		}else{
				$multiple = 0;
		}
		# $objResponse->addAlert('keyword = '. $searchkey);

		#$total_srv = $srv->countSearchService($grp,$searchkey,$multiple,$maxRows,$offset,$area,0,$frommgr);
		$ergebnis=$srv->SearchService($grp,$searchkey,$multiple,$maxRows,$offset,$area,0,1,$frommgr);
		#$objResponse->addAlert($srv->sql);
		$total = $srv->count;
		#$objResponse->addAlert('total = '.$total);

		$lastPage = floor($total/$maxRows);
		#$objResponse->addAlert('total = '.$lastPage);
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchService($grp,$searchkey,$multiple,$maxRows,$offset,$area,0,0,$frommgr);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","product-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				#$objResponse->addAlert("here = ".$result["service_code"]);
				$objResponse->addScriptCall("addProductToList","product-list",$is_param,trim($result["service_code"]),trim($result["name"]),number_format(trim($result["price_cash"]),2,'.', ''),number_format(trim($result["price_charge"]),2,'.', ''));
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	require_once('./roots.php');

		require($root_path.'include/inc_environment_global.php');
		require($root_path."modules/laboratory/ajax/lab-service.common.php");
	#added by VAN 04-17-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$xajax->processRequests();
?>