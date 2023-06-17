<?php
	function addTransactionDetail($refno, $pid, $name, $price, $qty) {
		$med_obj=new SegMed;
		$entryno=$med_obj->AddTransactionDetails($refno, $pid, $qty, $price);
		$objResponse = new xajaxResponse();
		if ($entryno) {
			$objResponse->addScriptCall("med_retail_gui_addDestProductRow", $pid, $name, $entryno, round($price,2), round($qty));
		}

		return $objResponse;
	}
	
	function delTransactionDetail($refno, $entryno, $rowno) {
		$med_obj=new SegMed;
		$result=$med_obj->RemoveTransactionDetails($refno, $entryno);
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("med_retail_gui_rmvDestProductRow",$rowno);
		}
		//$objResponse->addAlert($med_obj->sql);
		return $objResponse;
	}
	
	function populateDetails($refno) {
		$med_obj=new SegMed;
		$ergebnis=$med_obj->GetTransactionDetails($refno);
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("med_retail_gui_clearDestRows");
		if ($med_obj->result->RecordCount()>0) {
			while($result=$ergebnis->FetchRow()) {
				$objResponse->addScriptCall("med_retail_gui_addDestProductRow",$result["bestellnum"],$result["artikelname"],$result["entrynum"],round($result["rpriceppk"],2),round($result["qty"]));
			}				
		}
		//$objResponse->addAlert(print_r($pharama_obj->sql,TRUE));
		return $objResponse;		
	}

	function populateProductList($keyword, $iscash) {
		global $db;
		$dbtable='care_med_products_main';
		$prctable = 'seg_med_prices';
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
		$objResponse->addScriptCall("med_retail_gui_clearSrcRows");
		
		
		while($result=$ergebnis->FetchRow()) {
			//$objResponse->addAlert(print_r("aaa",TRUE));
			$price=$iscash?$result["cshrpriceppk"]:$result["chrgrpriceppk"];
			if (!$price) $price="N/A";
			else $price=number_format($price,2,'.',',');
			$objResponse->addScriptCall("med_retail_gui_addSrcProductRow",$result["bestellnum"],$result["artikelname"],  $price);
		}
		if (!$rows) $objResponse->addScriptCall("med_retail_gui_addSrcProductRow",NULL);
		
		//$objResponse->addAlert(print_r($sql,TRUE));
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_med_transaction.php');
	require($root_path."modules/med_depot/ajax/retail-new.common.php");
	$xajax->processRequests();
?>