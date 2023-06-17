<?php
	function populateProductPrices($keyword) {
		global $db;
		$dbtable='care_med_products_main';
		$prctable='seg_med_prices';

		#TODO: Code a smarter SQL statement for searching a person's name given a keyword
		$sql="SELECT a.*, b.ppriceppk, b.cshrpriceppk, b.chrgrpriceppk FROM $dbtable as a LEFT JOIN $prctable as b ON a.bestellnum=b.bestellnum WHERE a.bestellnum REGEXP '[[:<:]]$keyword' OR a.artikelname REGEXP '[[:<:]]$keyword' ORDER BY artikelname";
		$objResponse = new xajaxResponse();
		
    $ergebnis=$db->Execute($sql);

		$objResponse->addScriptCall("retail_clearProductPrices");
		$rows=$ergebnis->RecordCount();
		while($result=$ergebnis->FetchRow()) {
			//$objResponse->addAlert(print_r($result,TRUE));
			$objResponse->addScriptCall("retail_addProductPrice", $result['bestellnum'], $result['artikelname'], $result['ppriceppk']-0, $result['cshrpriceppk']-0, $result['chrgrpriceppk']-0);
			//$result["pid"],$result["name_last"],$result["name_first"],$result["date_birth"]);
		}
		if (!$rows) $objResponse->addScriptCall("retail_addProductPrice",NULL);
		//$objResponse->addAlert(print_r($rows,TRUE));
		return $objResponse;
	}
	
	function updateProductPrice($encoder, $rowno, $nr, $pprice, $cshprice, $chrgprice) {
		$med_obj=new SegMed;
		$result=$med_obj->UpdateProductPrice($encoder, $nr, $pprice, $cshprice, $chrgprice);
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("ppricecolorrow",$rowno);
			$objResponse->addAlert("Product price updated...");		
		}
		else {
			$objResponse->addAlert("Error occured while updating product...");	
			//$objResponse->addAlert($med_obj->sql);
		}
		return $objResponse;
	}

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
		global $db;
		$persontb = "care_person";
		$encountertb = "care_encounter";
		$detailstb = "seg_med_retail";		
		$sql="SELECT d.*, CONCAT(p.name_first, ' ', p.name_last) AS fullname FROM $detailstb as d,$encountertb as e,$persontb as p WHERE d.encounter_nr=e.encounter_nr AND e.pid=p.pid AND p.pid=$pid ORDER BY purchasedte DESC";
		//SELECT d.*, CONCAT(p.name_first, ' ', p.name_last) AS fullname FROM seg_med_retail as d,care_encounter as e,care_person as p WHERE d.encounter_nr=e.encounter_nr AND e.pid=p.pid AND p.pid=$pid ORDER BY purchasedte
    $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("retail_clearTransactions");
		while($result=$ergebnis->FetchRow()) {
			$objResponse->addScriptCall("retail_addTransaction",$result["refno"], $result["encounter_nr"], $result["fullname"], date("M j, Y",strtotime($result["purchasedte"])),$result["is_cash"]);
		}
		if (!$rows) $objResponse->addScriptCall("retail_addTransaction",NULL);
		//$objResponse->addAlert($sql);
		return $objResponse;
	}
	
	function delTransaction($refno,$rowno) {
		$med_obj=new SegMed;
		$result=$med_obj->DeleteMedTransaction($refno);
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("retail_rmvTransaction",$rowno);
			$objResponse->addAlert("Transaction entry successgfuly deleted...");
		}
		//$objResponse->addAlert($med_obj->sql);
		return $objResponse;
	}


	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_med_transaction.php');
	require($root_path."modules/med_depot/ajax/retail-pprice.common.php");
	$xajax->processRequests();
?>