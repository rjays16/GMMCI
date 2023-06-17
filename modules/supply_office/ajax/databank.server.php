<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path."include/care_api_classes/class_pharma_product.php");
include_once($root_path."include/care_api_classes/inventory/class_sku_inventory.php");
include_once($root_path."include/care_api_classes/curl/class_curl.php");
require_once($root_path.'modules/supply_office/ajax/databank.common.php');

function populateProducts($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$pclass = new SegPharmaProduct();
	$codename = $args['codename'];
	$generic = $args['generic'];
	$prodclass = $args['prodclass'];
	$showdeleted = $args['showdeleted'];
	$area = ($args['area']) ? $args['area'] : '';

	$offset = $page_num * $max_rows;
	$sortColumns = array('prod_class','p.bestellnum','artikelname','p.price_cash','supplier_price','sc_price','stock');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "artikelname";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'date_request DESC';

	$f = array();
	if ($codename) $f['CODENAME']=$codename;
	if ($generic) $f['GENERIC']=$generic;
	if ($prodclass) $f['PRODCLASS']=$prodclass;
	if ($showdeleted) $f['SHOWDELETED']=$showdeleted;
	if ($area) $f['AREA'] = $area; #added by monmon

	$f['SORT'] = $sort_sql;
	$f['OFFSET']=$offset;
	$f['ROWCOUNT']=$max_rows;

	$result=$pclass->search( $f );

	if($result) {
		$found_rows = $pclass->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=$result->RecordCount()) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			while($row = $result->FetchRow()) {

				$skuObj = new SkuInventory();

				$DATA[$i]['bestellnum'] = $row['bestellnum'];
				$DATA[$i]['artikelname'] = $row['artikelname'];
				$DATA[$i]['generic'] = $row['generic'];
				$DATA[$i]['price_cash'] = $row["price_cash"];
				$DATA[$i]['sc_price'] = $row['sc_price'];
				$DATA[$i]['c1_price'] = $row['c1_price'];
				//$DATA[$i]['supplier_price'] = $row['supplier_price'];

				$DATA[$i]['supplier_price'] = $skuObj->getItemAvgCost($row['bestellnum'],'',$area);
				$qty =  $skuObj->getItemQty($row['bestellnum'],'',$area);

				$DATA[$i]['stock'] = $qty;
				$DATA[$i]['is_deleted'] = $row['is_deleted'];
				$DATA[$i]['prod_class'] = $row['prod_class'];
				$DATA[$i]['FLAG'] = 1;
				$i++;

			} //end while

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		// error
		$objResponse->alert($pclass->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}
	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

function deleteProduct($id, $flag=1) {
	global $db;
	$objResponse = new xajaxResponse();
	$pc = new SegPharmaProduct();
	$curl = new Rest_Curl();
	if ($pc->deleteProduct($id, $flag)) {
		$objResponse->script('plst.reload()');
		if ($flag) {
			$data = array("id"=>$id);
			$curl->deleteItem($data);
			$objResponse->alert('Item successfully deleted...');
		}
		else {
			$objResponse->alert('Deleted successfully restored...');
		}
	}
	else {
		if (strpos($pc->db_error_msg,'a foreign key constraint fails')!==FALSE) {
			$objResponse->alert('This item is currently in use and cannot be deleted...');
		}
		else
			$objResponse->alert('Database error:'.$pc->db_error_msg);
	}
	return $objResponse;
}

function getCode($name, $prod_class, $unit) {
	global $db;
	$objResponse = new xajaxResponse();
	$pc=new SegPharmaProduct();
	$type_row = $pc->getTypebyProdClass($prod_class);
	$type=$type_row['nr'];
	if ($code = $pc->createNR()) {
		$objResponse->assign('bestellnum', 'value', $code);
	}
	else {
		$objResponse->assign('bestellnum', 'value', '');
	}
	return $objResponse;
}

function getHealthInsurances($hcare_id) {
	global $db;

	$objResponse = new xajaxResponse();

	$strSQL = "SELECT hcare_id, firm_id as name FROM care_insurance_firm ORDER BY name";

	if($result = $db->Execute($strSQL)){
		if($result->RecordCount()){
			$objResponse->call("js_ClearOptions", "exclude_hcareid");
			$objResponse->call("js_AddOptions","exclude_hcareid", "- Select Insurance -", 0);
			while($row = $result->FetchRow()){
				$objResponse->call("js_AddOptions","exclude_hcareid", $row['name'], $row['hcare_id']);
			}

			if($hcare_id){
				$objResponse->call("js_setOption", "exclude_hcareid", $hcare_id);
			}else{
				$objResponse->call("js_setOption", "exclude_hcareid", "- Select Insurance -");
			}
		}else{
			$objResponse->alert("ERROR: No case type found");
		}
	}

	return $objResponse;
} // end of function getHealthInsurances

$xajax->processRequest();
