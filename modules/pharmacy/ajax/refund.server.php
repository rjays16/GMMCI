<?php
	function populateProductList($sElem,$keyword,$discountID,$area) {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		$objResponse = new xajaxResponse();

		# "IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),b.cshrpriceppk*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
		# "IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),b.cshrpriceppk*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n".
		
		
		# Later: Put this in a Class
		$sql="SELECT a.*,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_charge*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n".
				"IF(a.is_socialized,\n".
					"IFNULL(\n".
						"(SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),\n".
						"a.price_cash),\n".
					"a.price_cash) AS dprice,\n".
				"IFNULL(a.price_cash,0) AS cshrpriceppk,\n".
				"IFNULL(a.price_charge,0) AS chrgrpriceppk\n".
				"FROM care_pharma_products_main AS a\n".
				"WHERE artikelname REGEXP '[[:<:]]$keyword'";
		if ($area) {
			$sql .= " AND a.bestellnum IN (SELECT bestellnum FROM seg_pharma_products_availability WHERE area_code='$area')";
		}
				
		#$objResponse->addScriptCall("display",$sql);
		#return $objResponse;

		#$objResponse->addScriptCall("display",$sql);			
	  $ergebnis=$db->Execute($sql);
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			$objResponse->addScriptCall("clearList","product-list");
			while($result=$ergebnis->FetchRow()) {
				$objResponse->addScriptCall("addProductToList",
				"product-list",
				$result["bestellnum"],
				$result["artikelname"],
				$result["description"],
				$result["cshrpriceppk"],
				$result["chrgrpriceppk"],
				$result["cashscprice"],
				$result["chargescprice"],
				$result["dprice"],
				$result["is_socialized"]
				);
			}
		}
		else {
			if (defined("__DEBUG_MODE"))
				$objResponse->addScriptCall("display",$sql);
			else
				$objResponse->addAlert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
		}
		if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_refund.php');
	require($root_path."modules/pharmacy/ajax/refund.common.php");
	$xajax->processRequests();	
?>