<?php
	function populateRequestList($sElem,$keyword) {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';

		$sql="SELECT a.*, b.ppriceppk, b.chrgrpriceppk, b.cshrpriceppk FROM $dbtable AS a LEFT JOIN $prctable AS b ON a.bestellnum=b.bestellnum WHERE artikelname REGEXP '[[:<:]]$keyword' ORDER BY artikelname";
	   $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("clearList","product-list");
		while($result=$ergebnis->FetchRow()) {
			#$objResponse->addScriptCall("addProductToList","product-list",$result["bestellnum"],$result["artikelname"],substr($result["description"],0,40)."...",$result["cshrpriceppk"],$result["chrgrpriceppk"]);
			$objResponse->addScriptCall("addProductToList","product-list",$result["bestellnum"],$result["artikelname"],substr($result["description"],0,40)."...",$result["cshrpriceppk"],$result["chrgrpriceppk"]);
		}
		if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_pharma_transaction.php');
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');

	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path."modules/laboratory/ajax/request-tray.common.php");
	$xajax->processRequests();	
?>