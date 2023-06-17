<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/dialysis/ajax/dialysis-service-request.common.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_blood_bank.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/class_discount.php');
require_once($root_path.'include/care_api_classes/class_cashier_service.php');

function populateLabServiceList($area='',$group_code,$sElem,$searchkey,$page)
{
	global $db;
	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

	$objResponse = new xajaxResponse();
	$srv=new SegLab;
	$offset = $page * $maxRows;
	$searchkey = utf8_decode($searchkey);

	if (stristr($searchkey,",")){
		$keyword_multiple = explode(",",$searchkey);
		$codenum = 0;
		if (is_numeric($keyword_multiple[0]))
				$codenum = 1;

		for ($i=0;$i<sizeof($keyword_multiple);$i++){
			$keyword .= "'".trim($keyword_multiple[$i])."',";
		}
		$word = trim($keyword);
		$searchkey = substr($word,0,strlen($word)-1);
		$multiple = 1;
	}else{
		$multiple = 0;
	}

	$total_srv = $srv->SearchService($group_code,$searchkey,$multiple,$maxRows,$offset,$area, $codenum,1);
	$total = $srv->count;

	$lastPage = floor($total/$maxRows);

	if ((floor($total%10))==0)
		$lastPage = $lastPage-1;

	if ($page > $lastPage) $page=$lastPage;
	$ergebnis=$srv->SearchService($group_code,$searchkey,$multiple, $maxRows,$offset,$area, $codenum,0);
	$rows=0;

	$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
	$objResponse->call("clearList","product-list");
	if ($ergebnis) {
		$rows=$ergebnis->RecordCount();
		while($result=$ergebnis->FetchRow()) {
				#check if the service is socialized
				if ($result["is_socialized"]){
					$sql2 = "SELECT DISTINCT * FROM seg_service_discounts
								 WHERE service_code='".$result["service_code"]."'";
					$res=$db->Execute($sql2);
					$row=$res->RecordCount();

					if ($row!=0){
						while($rsObj=$res->FetchRow()) {
							if ($rsObj["discountid"] == C1){
								$price_C1 = $rsObj["price"];
							}
							if ($rsObj["discountid"] == C2){
								$price_C2 = $rsObj["price"];
							}
							if ($rsObj["discountid"] == C3){
								$price_C3 = $rsObj["price"];
							}
						}
					}else{
													$price_C1 = '';
													$price_C2 = '';
													$price_C3 = '';

											}
				}else{

					$price_C1 = number_format(trim($result["price_cash"]),2,'.', '');
					$price_C2 = number_format(trim($result["price_cash"]),2,'.', '');
					$price_C3 = number_format(trim($result["price_cash"]),2,'.', '');
				}

				if ($result['status']=='unavailable')
					$available = 0;
				else
					$available = 1;

			$objResponse->call("addProductToList","product-list",trim($result["service_code"]),trim($result["name"]),number_format(trim($result["price_cash"]),2,'.', ''),number_format(trim($result["price_charge"]),2,'.', ''), $result["is_socialized"],$result["group_code"],$price_C1,$price_C2,$price_C3, $available);
		}#end of while
	} #end of if

	if (!$rows) $objResponse->call("addProductToList","product-list",NULL);
	if ($sElem) {
		$objResponse->call("endAJAXSearch",$sElem);
	}

	return $objResponse;
}

function getAllServiceOfPackage($service_code)
{
	global $db;
	$objResponse = new xajaxResponse();
	$srv=new SegLab;

	$rs_group = $srv->isServiceAPackage($service_code);
	$rs_count = $srv->count;

	if ($rs_count){
		$rs_group_inc = $srv->getAllServiceOfPackage($service_code);
		#lab exam request that is a package
		while ($row=$rs_group_inc->FetchRow()){
				$objResponse->call("prepareAdd_Package",$row['service_code'],$row['name'],$row['cash'],$row['charge'],$row['sservice'],$row['group_code'],$row['priceC1'],$row['priceC2'],$row['priceC3']);
		}

	} else{
		 #lab exam request that is not a package
		 $objResponse->call("prepareAdd_NotPackage",$service_code);
	}

	return $objResponse;
}

function populateBloodServiceList($area='',$sElem,$searchkey,$page)
{
	global $db;
	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

	$objResponse = new xajaxResponse();
	$srv=new SegBloodBank();
	$offset = $page * $maxRows;

	$group_code = "B";

	$total_srv = $srv->countSearchService($group_code,$searchkey,$multiple,$maxRows,$offset,$area);
	$total = $srv->count;

	$lastPage = floor($total/$maxRows);

	if ((floor($total%10))==0)
		$lastPage = $lastPage-1;

	if ($page > $lastPage) $page=$lastPage;
	$ergebnis=$srv->SearchService($group_code,$searchkey,$multiple,$maxRows,$offset,$area);
	$rows=0;

	$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
	$objResponse->call("clearList","request-list");
	if ($ergebnis) {
		$rows=$ergebnis->RecordCount();
		while($result=$ergebnis->FetchRow()) {
			$name = $result["name"];
			if (strlen($name)>40)
				$name = substr($result["name"],0,40)."...";

			if ($result["is_socialized"]){
					$sql2 = "SELECT DISTINCT * FROM seg_service_discounts
								 WHERE service_code='".$result["service_code"]."'";
					$res=$db->Execute($sql2);
					$row=$res->RecordCount();

					if ($row!=0){
						while($rsObj=$res->FetchRow()) {
							if ($rsObj["discountid"] == C1){
								$price_C1 = $rsObj["price"];
							}
							if ($rsObj["discountid"] == C2){
								$price_C2 = $rsObj["price"];
							}
							if ($rsObj["discountid"] == C3){
								$price_C3 = $rsObj["price"];
							}
						}
					}
				}else{
					$price_C1 = number_format(trim($result["price_cash"]),2,'.', '');
					$price_C2 = number_format(trim($result["price_cash"]),2,'.', '');
					$price_C3 = number_format(trim($result["price_cash"]),2,'.', '');
				}

			if ($result['status']=='unavailable')
					$available = 0;
			else
					$available = 1;

			$objResponse->call("addProductToList","request-list",$result["service_code"],
													$name,$result["group_code"],$result["price_cash"],
													$result["price_charge"], $result['is_socialized'],$price_C1,$price_C2,$price_C3, $available);
		}#end of while
	} #end of if

	if (!$rows) $objResponse->call("addProductToList","request-list",NULL);
	if ($sElem) {
		$objResponse->call("endAJAXSearch",$sElem);
	}

	return $objResponse;
}

function getAllBloodServiceOfPackage($service_code)
{
	global $db;
	$objResponse = new xajaxResponse();
	$srv=new SegBloodBank();

	$rs_group = $srv->isServiceAPackage($service_code);
	$rs_count = $srv->count;

	if ($rs_count){
		$rs_group_inc = $srv->getAllServiceOfPackage($service_code);
		#lab exam request that is a package
		while ($row=$rs_group_inc->FetchRow()){
				$objResponse->call("prepareAdd_Package",$row['service_code'],$row['name'],$row['cash'],$row['charge'],$row['sservice'],$row['group_code'],$row['priceC1'],$row['priceC2'],$row['priceC3']);
		}

	} else{
		 #lab exam request that is not a package
		 $objResponse->call("prepareAdd_NotPackage",$service_code);
	}

	return $objResponse;
}

function populateRadioServiceList($dept_nr=0, $area='',$area_type='',$sElem,$searchkey,$page)
{
	global $db;
	$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('pagin_patient_search_max_block_rows');
	$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

	$objResponse = new xajaxResponse();
	$srv=new SegRadio();
	$offset = $page * $maxRows;
	$total_srv = $srv->countSearchService2($searchkey,$maxRows,$offset,$area,$dept_nr);
	$total = $srv->count;

	$lastPage = floor($total/$maxRows);

	if ((floor($total%10))==0)
		$lastPage = $lastPage-1;

	if ($page > $lastPage) $page=$lastPage;
	$ergebnis=$srv->SearchService2($searchkey,$maxRows,$offset,$area,$dept_nr);
	$rows=0;

	$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
	$objResponse->call("clearList","request-list");
	if ($ergebnis) {
		$rows=$ergebnis->RecordCount();
		while($result=$ergebnis->FetchRow()) {
			$name = $result["name"];
			if (strlen($name)>40)
				$name = substr($result["name"],0,40)."...";

			if ($result['status']=='unavailable')
					$available = 0;
			else
					$available = 1;

			if ($area_type){
					$query4 = "SELECT p.price_cash, p.price_charge
											FROM seg_service_pricelist AS p
											WHERE p.service_code=".$db->qstr($result["service_code"])."
											AND p.ref_source='RD' AND p.area_code='$area_type'";
					$radio_serv2 = $db->GetRow($query4);
					if ($radio_serv2){
						$result["price_cash"] = $radio_serv2["price_cash"];
						$result["price_charge"] = $radio_serv2["price_charge"];
					}
		 }

			$objResponse->call("addProductToList","request-list",$result["service_code"],
													$name,trim($result['dept_short_name']),$result["price_cash"],
													$result["price_charge"], $result['is_socialized'], $available);
		}#end of while
	} #end of if

	if (!$rows) $objResponse->call("addProductToList","request-list",NULL);
	if ($sElem) {
		$objResponse->call("endAJAXSearch",$sElem);
	}

	return $objResponse;
}

function populateProductList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$prod_class)
{
	global $db, $config;
	$dbtable='care_pharma_products_main';
	$prctable = 'seg_pharma_prices';
	$objResponse = new xajaxResponse();
	$pc = new SegPharmaProduct();

	$maxRows = 10;
	$offset = $page * $maxRows;

	$ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows, $prod_class);
	if ($ergebnis) {
		$total = $pc->FoundRows();
		$lastPage = floor($total/$maxRows);
		if ($page > $lastPage) $page=$lastPage;

		$rows=$ergebnis->RecordCount();

		$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->call("clearList","product-list");

		while($result=$ergebnis->FetchRow()) {
			$details->id = $result["bestellnum"];
			$details->name = $result["artikelname"];
			$details->desc = $result["generic"];
			$details->cash = $result["cshrpriceppk"];
			$details->charge = $result["chrgrpriceppk"];
			$details->cashsc = $result["cashscprice"];
			$details->chargesc = $result["chargescprice"];
			$details->d = $result["dprice"];
			$details->soc = $result["is_socialized"];
			$details->noqty = ($disable_qty==1) ? TRUE : FALSE;
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

function populateMiscServiceList($sElem,$keyword,$type,$page)
{
	global $db;
	$objResponse = new xajaxResponse();

	$csClass = new SegCashierService();

	#$dbtable='seg_other_services';
	#$sql="SELECT * FROM $dbtable WHERE name REGEXP '[[:<:]]$keyword' OR service_code REGEXP '[[:<:]]$keyword' ORDER BY name";

	$maxRows = 25;
	$offset = $page * $maxRows;

	$ergebnis = $csClass->searchServices($keyword, $type, FALSE, $offset, $maxRows, 's.name,s.price');
	$rows=$ergebnis->RecordCount();
	$total = $csClass->FoundRows();
	$lastPage = floor($total/$maxRows);
	if ($page > $lastPage) $page=$lastPage;
	$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
	$objResponse->call("clearList","service-list");
	//$objResponse->alert($csClass->sql);
	while($result=$ergebnis->FetchRow()) {
		//$last_code = $result["code"];
		$last_code = $result["alt_code"];
		//$details->id = $result["code"];
		$details->id = $result["alt_code"];
		$details->name = $result['name'];
		//added by: ian villanueva
		$details->lock = $result['is_not_socialized'];
		$details->desc = $result['name_short'];
		$details->price = $result['price'];
		$details->dept_name = $result['dept_name'];
		$objResponse->call("addServiceToList","service-list",$details);
	}
	if (!$rows) $objResponse->call("addServiceToList","service-list",NULL);
	if ($rows==1) {
		$objResponse->call("prepareAdd", $last_code);
	}
	if ($sElem) {
		$objResponse->call("endAJAXSearch",$sElem);
	}
	return $objResponse;
}

$xajax->processRequest();

