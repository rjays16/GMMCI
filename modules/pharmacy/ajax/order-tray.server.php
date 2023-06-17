<?php
	function populateProductList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$auto=false) {
		global $db, $config;
                $skuInventory = new SkuInventory();
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		$objResponse = new xajaxResponse();
		$pc = new SegPharmaProduct();

		$maxRows = 10;
		$offset = $page * $maxRows;

		$ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows);
		//$objResponse->alert($pc->sql);

		#return $objResponse;
		if ($ergebnis) {
			$total = $pc->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;

			$rows=$ergebnis->RecordCount();

			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			while($result=$ergebnis->FetchRow()) {
                $extended = $pc->getExtendedProductInfo($result["bestellnum"]);
                if($extended) {
                    $reorderPoint = $extended['min_qty'];
                } else {
                    $reorderPoint = 1;
                }
				$details->id = $result["bestellnum"];
				$details->name = $result["artikelname"];
				$details->desc = $result["generic"];
				
				//Added by genz
				if(!$auto && $result['prod_class'] == "M" && $result['type_cat'] != 'GMAP'){
					$details->cash = $result["cshrpriceppk"] - ($result["cshrpriceppk"] * .10);	
				}
				else{
					$details->cash = $result["cshrpriceppk"];
				}
				//$details->charge = (($auto && $result["prod_class"] == "M" && $result["is_twelve"] == '0') ? $result["chrgrpriceppk"] + ($result["chrgrpriceppk"] * .12) : $result["chrgrpriceppk"] );//Added by Jarel For GMMCI
				
				//Added by genz
				if($auto && ($result["prod_class"] == "M" && $result["percent"] == '0')){
				  $details->charge = $result["chrgrpriceppk"] + ($result["chrgrpriceppk"] * .12);
				}
				else{
					$details->charge = $result["chrgrpriceppk"];
				}


			$condition = $pc->getProductDiscounts($result["bestellnum"]);	
			if ($condition){
				$details->cashsc = $result["cashscprice"];
				$details->chargesc = $result["chargescprice"];
			}else{
				$details->cashsc = "--";
				$details->chargesc = "--";
			}
				if($result['type_cat'] != 'GMAP')
					$details->d = $result["dprice"] - ($result["dprice"] *.10);
				else
					$details->d = $result["dprice"];

                $details->qty = $skuInventory->getItemQty($details->id, '', $area);
				$details->soc = $result["is_socialized"];
				$details->noqty = ($disable_qty==1) ? TRUE : FALSE;
				$details->restricted = $result["is_restricted"]; //added by cha, august 17, 2010
                $details->isCritical = $details->qty <= $reorderPoint ? true : false;
				$objResponse->call("addProductToList","product-list",$details);
			}
		}
		else {
			if ($config['debug'])
				$objResponse->addScriptCall("display",$sql);
			else
				$objResponse->addAlert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
		}
		if (!$rows) {
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			$objResponse->call("addProductToList","product-list",NULL);
		}
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	#--for requesting anesthetic medicines only from OR
	function populateORProductList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$targetItems='') {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		$objResponse = new xajaxResponse();
		$pc = new SegPharmaProduct();

		$maxRows = 10;
		$offset = $page * $maxRows;

		#-------added by CHA 12-15-2009 --------------------
		/*if($area=='OR')
			$ergebnis = $pc->search_products_for_anesthesia_tray($keyword, $discountID, $area, $offset, $maxRows, $targetItems);
		else*/	#--removed by cha, jan 9,2010
			$ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows);

		#$objResponse->alert($pc->sql);
		#return $objResponse;
		if ($ergebnis) {
			$total = $pc->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;

			$rows=$ergebnis->RecordCount();

			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			while($result=$ergebnis->FetchRow()) {

				//$objResponse->alert($result['expiration_dates']);
				$details->id = $result["bestellnum"];
				$details->name = $result["artikelname"];
				$details->desc = $result["generic"];
				$details->restricted = $result["is_restricted"];
				$details->cash = $result["cshrpriceppk"];
				$details->charge = $result["chrgrpriceppk"];
				$details->cashsc = $result["cashscprice"];
				$details->chargesc = $result["chargescprice"];
				$details->d = $result["dprice"];
				$details->expiry = $result["expiration_dates"];
				$details->soc = $result["is_socialized"];
				$details->stock = $result["qty_stock"];
				$details->noqty = ($disable_qty==1) ? TRUE : FALSE;
								 $details->classification = $result['class_name'];

				#--added by CHA, jan 9, 2010
					$objResponse->call("addProductToAnestheticList","product-list",$details);
			}
		}
		else {
			if ($config['debug'])
				$objResponse->call("display",$sql);
			else
				$objResponse->alert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
		}
		if (!$rows) {
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			$objResponse->call("addProductToList","product-list",NULL);
		}
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	#-----added by CHA, Feb 10, 2010------------
	#-----for or packages------------------------
	function populatePackageItemList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$mode) {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		$objResponse = new xajaxResponse();
		$pc = new SegPharmaProduct();

		$maxRows = 10;
		$offset = $page * $maxRows;

		if($area=='PH'){
                    $area='';
                    $ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows);
		}
		else
                    $ergebnis = $pc->search_products_for_package_itemsTray($keyword, $discountID, $area, $offset, $maxRows);
		#$objResponse->alert($pc->sql);
		#return $objResponse;
		if ($ergebnis) {
			$total = $pc->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;

			$rows=$ergebnis->RecordCount();

			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");

			while($result=$ergebnis->FetchRow()) {

				//$objResponse->alert($result['expiration_dates']);
				$details->id = $result["bestellnum"];
				$details->name = $result["artikelname"];
				$details->desc = $result["generic"];
				$details->restricted = $result["is_restricted"];
				$unit_options='';
				$res = $db->Execute("select * from seg_unit where is_deleted='0' order by unit_name");
				while($row=$res->FetchRow())
				{
					$unit_options.='<option value="'.$row['unit_name'].'">'.$row['unit_name'].'</option>';
				}
				$details->opt = $unit_options;
				$details->mode = $mode;
				$objResponse->call("addPackageItemsToList","product-list",$details);

			}
		}
		else {
			if (defined("__DEBUG_MODE"))
				$objResponse->call("display",$sql);
			else
				$objResponse->alert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
		}
		if (!$rows) {
			$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","product-list");
			$objResponse->call("addProductToList","product-list",NULL);
		}
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_pharma_product.php');
	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
	require($root_path."modules/pharmacy/ajax/order-tray.common.php");
	$xajax->processRequest();
