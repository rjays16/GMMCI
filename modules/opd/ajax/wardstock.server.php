<?php
	function reset_stocknr() {
		global $db;
		$objResponse = new xajaxResponse();
		
		$wc = new SegPharmaWard();
		$lastnr = $wc->getLastNr();

		if ($lastnr)
			$objResponse->addScriptCall("resetRefNo",$lastnr);
		else
			$objResponse->addScriptCall("resetRefNo","Error!",1);
		return $objResponse;
	}
	
	function populate_stock( $nr, $disabled=NULL ) {
		global $db;
		$objResponse = new xajaxResponse();
		$wc = new SegPharmaWard();		
		$objResponse->addScriptCall("clearOrder",NULL);
		$rows = 0;
		if ($nr) {
			$result = $wc->getStockItems($nr);
			if ($result) {
				$rows=$result->RecordCount();
		 		while ($row=$result->FetchRow()) {
					$obj = (object) 'details';
					$obj->id = $row["bestellnum"];
					$obj->name = $row["artikelname"];
					$obj->qty = $row["quantity"];
					$obj->desc= $result["generic"];
					$objResponse->addScriptCall("appendOrder", NULL, $obj, $disabled);
					#$objResponse->addAlert(print_r($row,TRUE));
				}
				if (!$rows) $objResponse->addScriptCall("appendOrder",NULL,NULL);
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
		}
		else $objResponse->addScriptCall("appendOrder", NULL, NULL, $disabled);
		return $objResponse;
	}

	
	function add_item( $discountID, $items, $qty, $prc, $consigned ) {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		$objResponse = new xajaxResponse();

		# Later: Put this in a Class
		if (!is_array($items)) $item = array($items);
		if (!is_array($qty)) $qty = array($qty);
		if (!is_array($prc)) $prc = array($prc);
		if (!is_array($consigned)) $prc = array($consigned);
		
		foreach ($items as $i=>$item) {
		
			$sql="SELECT a.*,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),b.cshrpriceppk*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),b.cshrpriceppk*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n".
				"IFNULL(b.ppriceppk,0) AS ppriceppk,\n".
				"IFNULL(b.chrgrpriceppk,0) AS chrgrpriceppk,\n".
				"IF(a.is_socialized,\n".
					"IFNULL((SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),b.cshrpriceppk),\n".
					"cshrpriceppk) AS dprice,\n".
				"IFNULL(b.cshrpriceppk,0) AS cshrpriceppk\n".
				"FROM care_pharma_products_main AS a\n".
				"LEFT JOIN seg_pharma_prices AS b ON a.bestellnum=b.bestellnum\n".
				"WHERE a.bestellnum = '$item'";
		  $ergebnis=$db->Execute($sql);

#			$objResponse->addAlert(print_r($qty,true));
			if ($ergebnis) {
				$rows=$ergebnis->RecordCount();
				$objResponse->addScriptCall("clearOrder",NULL);
				while($result=$ergebnis->FetchRow()) {
					$obj = (object) 'details';
					$obj->id = $result["bestellnum"];
					$obj->name = $result["artikelname"];
					$obj->desc= $result["description"];
					$obj->prcCash = $result["cshrpriceppk"];
					$obj->prcCharge = $result["chrgrpriceppk"];
					$obj->prcCashSC = $result["cashscprice"];
					$obj->prcChargeSC = $result["chargescprice"];
					$obj->prcDiscounted = $result["dprice"];
					$obj->isSocialized = $result["is_socialized"];
					$obj->forcePrice = $prc[$i];
					$obj->qty = $qty[$i];
					$obj->isConsigned = $consigned[$i];
					$objResponse->addScriptCall("appendOrder", NULL, $obj);
				}
			}
			else {			
				if (defined('__DEBUG_MODE'))
					$objResponse->addScriptCall("display",$sql);
				else
					$objResponse->addAlert("A database error has occurred. Please contact your system administrator...");
			}
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_pharma_ward.php');
	require($root_path."modules/pharmacy/ajax/wardstock.common.php");
	$xajax->processRequests();
?>