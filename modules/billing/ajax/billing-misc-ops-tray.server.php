<?php
	function getCurrentOpsInEncounter($enc_nr, $bill_frmdte, $bill_dt, $page, $b_all) {
				global $db;
				$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

				$glob_obj->getConfig('pagin_patient_search_max_block_rows');
				$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

				$objResponse = new xajaxResponse();
				$srv=new SegOps;

		$b_all = ($b_all != 0);
		
				$offset = $page * $maxRows;
				#$searchkey = utf8_decode($searchkey);
		$total_srv = $srv->countCurrentOP($enc_nr, $bill_frmdte, $bill_dt, $maxRows, $offset, $b_all);        

				$total = $srv->count;

				$lastPage = floor($total/$maxRows);

				if ((floor($total%$maxRows))==0)
						$lastPage = $lastPage-1;

				if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchCurrentOP($enc_nr, $bill_frmdte, $bill_dt, $maxRows, $offset, $b_all);

				#$objResponse->addAlert("STATUS = ".$ergebnis);
//		$objResponse->addAlert("Qry = ".$srv->sql); 

				$rows=0;

				$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
				$objResponse->addScriptCall("clearList","procedure-list");
		$objResponse->addScriptCall("toggleCurOpsHeader",true);                

				if ($ergebnis) {
						$rows=$ergebnis->RecordCount();
						while($result=$ergebnis->FetchRow()) {
								$description_short = $result["description"];

								#$objResponse->addAlert($description_short);

								if (strlen($description_short)>50)
										$description_short = substr(trim($result["description"]),0,50)."...";

				$objResponse->addScriptCall("addCurrentOpsToList","procedure-list", trim($result["code"]), (is_null($result["op_date"]) ? '00-00-0000' : strftime("%m-%d-%Y", strtotime($result["op_date"]))), (($result["group_code"] == '') ? 'NONE' : trim($result["group_code"])), trim($description_short),trim($result["description"]),$result["rvu"],$result["multiplier"],$result["provider"], trim($result["refno"]), $result["entry_no"], $result["op_count"]);
						}#end of while
				} #end of if

				if (!$rows) $objResponse->addScriptCall("addCurrentOpsToList","procedure-list",NULL);

				return $objResponse;
		}

	function populateICPMList($sElem,$searchkey,$page){
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv=new SegOps;
		#$objResponse->addAlert('page = '.$page);
		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
		$total_srv = $srv->countSearchOP($searchkey,$maxRows,$offset);

		#$objResponse->addAlert($srv->sql);

		$total = $srv->count;

		#$objResponse->addAlert('total = '.$total);

		$lastPage = floor($total/$maxRows);

		if ((floor($total%$maxRows))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchOP($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","procedure-list");
		$objResponse->addScriptCall("toggleCurOpsHeader",false);  
		
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$description_short = $result["description"];
				if (strlen($description_short)>50)
					$description_short = substr(trim($result["description"]),0,50)."...";

				$objResponse->addScriptCall("addProductToList","procedure-list",trim($result["code"]),trim($description_short),trim($result["description"]),$result["rvu"],$result["multiplier"]);
			}#end of while
		} #end of if

		if (!$rows)
			$objResponse->addScriptCall("addProductToList","procedure-list",NULL);
		else             
			$objResponse->addScriptCall("chngDefaultOption"); 
			
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function updateGrpID($grpid, $opcode, $refno, $entryno, $provider) {
		$objResponse = new xajaxResponse(); 
		
		$opobj = new SegOps();        
		$bsuccess = $opobj->updateGrpID($grpid, $opcode, $refno, $entryno, $provider);        
		if ($bsuccess) 
			$objResponse->addAlert("Group code of {$opcode} was updated successfully!");    
		else
			$objResponse->addAlert("Update of group code of item {$opcode} failed!");   
			
		return $objResponse;
	}
	
	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');

	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');

	require($root_path."modules/billing/ajax/billing-misc-ops-tray.common.php");
	$xajax->processRequests();
?>