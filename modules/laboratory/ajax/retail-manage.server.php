<?php
	function populatePersonList($keyword) {
		global $db;
		$dbtable='care_person';

		#TODO: Code a smarter SQL statement for searching a person's name given a keyword
		$sql="SELECT * FROM $dbtable WHERE name_last REGEXP '[[:<:]]$keyword' OR name_first REGEXP '[[:<:]]$keyword' OR name_2 REGEXP '[[:<:]]$keyword' OR name_3 REGEXP '[[:<:]]$keyword' ORDER BY name_last";

    $ergebnis=$db->Execute($sql);
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("retail_clearPersons");
		$rows=$ergebnis->RecordCount();
		while($result=$ergebnis->FetchRow()) {
			$objResponse->addScriptCall("retail_addPerson",$result["pid"],$result["name_last"],$result["name_first"],$result["date_birth"]);
		}
		if (!$rows) $objResponse->addScriptCall("retail_addPerson",NULL);
		//$objResponse->addAlert(print_r($linecount,TRUE));
		return $objResponse;
	}
	
	function populateTransactions($pid) {
		global $db, $phpfd;		
		$persontb = "care_person";
		$encountertb = "care_encounter";
		$detailstb = "seg_pharma_retail";		
		$sql="SELECT d.*, CONCAT(p.name_first, ' ', p.name_last) AS fullname FROM $detailstb as d,$encountertb as e,$persontb as p WHERE d.encounter_nr=e.encounter_nr AND e.pid=p.pid AND p.pid=$pid ORDER BY purchasedte DESC";
		//SELECT d.*, CONCAT(p.name_first, ' ', p.name_last) AS fullname FROM seg_pharma_retail as d,care_encounter as e,care_person as p WHERE d.encounter_nr=e.encounter_nr AND e.pid=p.pid AND p.pid=$pid ORDER BY purchasedte
    $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("retail_clearTransactions");
		while($result=$ergebnis->FetchRow()) {
			$objResponse->addScriptCall("retail_addTransaction",$result["refno"], $result["encounter_nr"], $result["fullname"], date($phpfd,strtotime($result["purchasedte"])),$result["is_cash"]);
		}
		if (!$rows) $objResponse->addScriptCall("retail_addTransaction",NULL);
		//$objResponse->addAlert($sql);
		return $objResponse;
	}
	
	function populateTransactionsByRefno($refno) {
		global $db, $phpfd;
		$persontb = "care_person";
		$encountertb = "care_encounter";
		$detailstb = "seg_pharma_retail";		
		$sql="SELECT d.*, CONCAT(p.name_first, ' ', p.name_last) AS fullname FROM $detailstb as d,$encountertb as e,$persontb as p WHERE d.encounter_nr=e.encounter_nr AND e.pid=p.pid AND refno LIKE '$refno%' ORDER BY purchasedte DESC";
    $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("retail_clearRefTransactions");
		while($result=$ergebnis->FetchRow()) {
			$objResponse->addScriptCall("retail_addRefTransaction",$result["refno"], $result["encounter_nr"], $result["fullname"], date($phpfd,strtotime($result["purchasedte"])),$result["is_cash"]);
		}
		if (!$rows) $objResponse->addScriptCall("retail_addRefTransaction",NULL);
		//$objResponse->addAlert($date_format);
		return $objResponse;
	}
	
	function delTransaction($refno,$rowno,$isref) {
		$pharma_obj=new SegPharma;
		$result=$pharma_obj->DeletePharmaTransaction($refno);
		$objResponse = new xajaxResponse();
		if ($result) {
			if ($isref) {
				$objResponse->addScriptCall("retail_rmvRefTransaction",$rowno);
			}
			else {
				$objResponse->addScriptCall("retail_rmvTransaction",$rowno);
			}
			$objResponse->addAlert("Transaction entry successfuly deleted...");
		}
		//$objResponse->addAlert($pharm	a_obj->sql);
		return $objResponse;
	}


	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];
	$phpfd=$date_format;
	$phpfd=str_replace("dd", "d", strtolower($phpfd));
	$phpfd=str_replace("mm", "m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","Y", strtolower($phpfd));
	$phpfd=str_replace("yy","y", strtolower($phpfd));

	require($root_path.'include/care_api_classes/class_pharma_transaction.php');
	require($root_path."modules/pharmacy/ajax/retail-manage.common.php");
	$xajax->processRequests();
?>