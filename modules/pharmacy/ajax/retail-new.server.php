<?php
	function addTransactionDetail($refno, $pid, $name, $price, $qty) {
		$pharma_obj=new SegPharma;
		$entryno=$pharma_obj->AddTransactionDetails($refno, $pid, $qty, $price);
		$objResponse = new xajaxResponse();
		if (is_numeric($entryno)) {
			$objResponse->addScriptCall("pharma_retail_gui_addDestProductRow", $pid, $name, $entryno, round($price,2), round($qty), TRUE);
			#$objResponse->addAlert($pharma_obj->sql);
		}
		else {
			$objResponse->addAlert($pharma_obj->sql);
		}

		return $objResponse;
	}
	
	function delTransactionDetail($refno, $entryno, $rowno) {
		$pharma_obj=new SegPharma;
		$result=$pharma_obj->RemoveTransactionDetails($refno, $entryno);
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("pharma_retail_gui_rmvDestProductRow",$rowno);
		}
		//$objResponse->addAlert($pharma_obj->sql);
		return $objResponse;
	}
	
	function populateDetails($refno) {
		$pharma_obj=new SegPharma;
		$ergebnis=$pharma_obj->GetTransactionDetails($refno);
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("pharma_retail_gui_clearDestRows");
		if ($ergebnis) {
			$recCount = $ergebnis->RecordCount();
			$counter=0;
			if ($recCount>0) {
				while($result=$ergebnis->FetchRow()) {
					$counter++;
					$objResponse->addScriptCall("pharma_retail_gui_addDestProductRow",$result["bestellnum"],$result["artikelname"],$result["entrynum"],round($result["rpriceppk"],2),$result["qty"]-0, $counter==$recCount);
				}				
			}
		}
#		else
#			$objResponse->addAlert(print_r($pharama_obj->sql,TRUE));
		return $objResponse;		
	}
	
	function populateProductList($keyword, $iscash) {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		# clean input data		
		
		/*
		$sql="SELECT * FROM $dbtable WHERE  bestellnum LIKE '%$keyword%'
					OR artikelnum LIKE '%$keyword%'
					OR industrynum LIKE '%$keyword%'
					OR artikelname LIKE '%$keyword%'
					OR generic LIKE '%$keyword%'
					OR description LIKE '%$keyword%' ORDER BY artikelname";
		*/
			
		//$sql="SELECT * FROM $dbtable WHERE artikelname LIKE '%$keyword%' ORDER BY artikelname";
		$sql="SELECT a.*, b.ppriceppk, b.chrgrpriceppk, b.cshrpriceppk FROM $dbtable AS a LEFT JOIN $prctable AS b ON a.bestellnum=b.bestellnum WHERE artikelname REGEXP '[[:<:]]$keyword' ORDER BY artikelname";
	  $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("pharma_retail_gui_clearSrcRows");
		
		
		while($result=$ergebnis->FetchRow()) {
/*
						$objResponse->addAlert($iscash);
			if ($iscash) {
				$objResponse->addAlert("IS CASH!!");
			}
			else {
				$objResponse->addAlert("IS CHARGE!!");
			}


			ob_start();
			var_dump($iscash);
			$sTemp = ob_get_contents();
			ob_end_clean();

			$objResponse->addAlert(print_r($sTemp,TRUE));
								*/
								
			$price=$iscash?$result["cshrpriceppk"]:$result["chrgrpriceppk"];
			if (!$price) $price="N/A";
			else $price=number_format($price,2,'.','');
			$objResponse->addScriptCall("pharma_retail_gui_addSrcProductRow",$result["bestellnum"],$result["artikelname"],  $price);
		}
		if (!$rows) $objResponse->addScriptCall("pharma_retail_gui_addSrcProductRow",NULL);
		
		//$objResponse->addAlert(print_r($sql,TRUE));
		return $objResponse;
	}
	
	function populateDiscountSelection() {
		global $db;
		$dbtable='seg_discount';
		$sql="SELECT * FROM $dbtable ORDER BY discountdesc";
	  $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("clearDiscountOptions");
		
		$cntr=0;
		while($result=$ergebnis->FetchRow()) {
			$objResponse->addScriptCall("addDiscountOption",$result["discountid"],$result["discountdesc"], $result["discount"], !$cntr);
			$cntr++;
		}
		if (!$rows) $objResponse->addScriptCall("addDiscountOption",NULL);
		
		//$objResponse->addAlert(print_r($sql,TRUE));
		return $objResponse;
	}
	
	function addRetailDiscount($refno, $id, $desc, $discount) {
		$dscObj=new SegDiscount;
		$result=$dscObj->AddRetailDiscount($refno, $id, "pharma");
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("gui_addRDiscountRow", $id, $desc, $discount, TRUE);
		}
		else {
			$objResponse->addAlert(print_r($dscObj->sql,TRUE));
		}
		
		//$objResponse->addAlert("refno:$refno, id=$id, desc=$desc, discount=$discount");
		return $objResponse;
	}
	
	function populateRetailDiscounts($refno) {
		global $db;
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("gui_clearRDiscountRows");
		
		$dbtable='seg_discount';
		$rdtable='seg_pharma_rdiscount';
		$sql="SELECT a.* FROM $dbtable AS a, $rdtable AS b WHERE a.discountid=b.discountid AND b.refno='$refno'";
	  $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();		
		$cntr=0;
		while($result=$ergebnis->FetchRow()) {
			//$objResponse->addAlert(print_r($result,TRUE));
			$objResponse->addScriptCall("gui_addRDiscountRow", $result['discountid'], $result['discountdesc'], $result['discount']);
			$cntr++;
		}
		return $objResponse;
	}
	
	function rmvRetailDiscount($refno, $discountid, $rowno) {
		$dscObj=new SegDiscount;
		$result=$dscObj->RemoveRetailDiscount($refno, $discountid, "pharma");
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("gui_rmvRDiscountRow",$rowno);
		}
		else {
			$objResponse->addAlert($dscObj->sql);
		}

		return $objResponse;
	}
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_pharma_transaction.php');
	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path."modules/pharmacy/ajax/retail-new.common.php");
	$xajax->processRequests();
?>