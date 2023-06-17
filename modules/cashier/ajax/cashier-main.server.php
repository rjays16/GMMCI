<?php

function getLatestORNumber($vat) {
	global $db;
	$objResponse = new xajaxResponse();
	$cc = new SegCashier();
	$userid = $_SESSION['sess_temp_userid'];
	if($vat == 'yes')
		$vat = '1';
	else
		$vat = '0';
	$orno = $cc->getNextORNum($userid, $vat);
	if ($orno) {
		$objResponse->assign('orno','value',$orno);
		$objResponse->call('warn','OR Number is valid!!',1);
	}
	else {

		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('cashier_%');

		$sql = "SELECT or_no FROM seg_pay\n";
		if ($_SESSION['sess_temp_userid'])
			$sql .= "WHERE create_id=".$db->qstr($_SESSION['sess_temp_userid'])."\n";
		$sql.= "ORDER BY or_date DESC";
		$or=$db->GetOne($sql);

		$dbOk = true;

		if ($or) {
			//$or = (int) preg_replace('\D','',$or);
			if (is_numeric($or)) {
				$len = strlen($or);
				if ($len < $GLOBAL_CONFIG['cashier_or_number_digits']) {
					$len = $GLOBAL_CONFIG['cashier_or_number_digits'];
				}
				$new_or = str_pad((int)$or+1,$len,'0', STR_PAD_LEFT);

				if (($result = $db->GetOne("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($new_or)))!==FALSE) {
					if (((int)$result) > 0) {
						# $objResponse->alert("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($new_or));
						$objResponse->assign('orno','value','');
						$objResponse->call('warn','OR #'.$new_or.' already in use!',0);
					}
					else {
						if ((($new_or-1)%$GLOBAL_CONFIG['cashier_or_batch_size'])==0) {
							$objResponse->assign('orno','value','');
							# $ $db->GetOne("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($new_or)
							$objResponse->call('warn','Please re-enter OR Number for the new feed...',0);
						}
						else {
							$objResponse->assign('orno','value',$new_or);
							$objResponse->call('warn','OR Number is valid!',1);
						}
					}
				}
				else {
					$dbOk = false;
				}
			}
			else {
				$objResponse->call('warn','Invalid (non-numeric) OR Number!',0);
			}
		}
		else {
			$dbOk = false;
		}

		if (!$dbOk) {
			$objResponse->assign('orno','value','');
			$objResponse->call('warn','Cannot retrieve latest OR Number from database...',0);
		}
	}
	return $objResponse;
}

function checkORNoExists($orno, $theORNo = FALSE) {
	global $db;
	$objResponse = new xajaxResponse();

	$len = strlen($orno);
	if ($len < 7) $len = 0;
	$orno = str_pad($orno,$len,'0', STR_PAD_LEFT);
	$objResponse->assign('orno','value',$orno);

	if ($orno && ($orno===$theORNo))
		$objResponse->call('warn','OR Number is valid!',1);
	else {
		if (($result = $db->GetOne("SELECT COUNT(*) FROM seg_pay WHERE or_no=".$db->qstr($orno)))!==FALSE) {
			if (((int)$result) > 0) {
				$objResponse->call('warn','OR Number already in use!',0);
			}
			else $objResponse->call('warn','OR Number is valid!',1);
		}
	}
	return $objResponse;
}

function refreshReference($src, $ref) {
	global $db;
	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();
	$resultInfo = $cClass->GetRequestInfo($ref,$src);
	if ($resultInfo) {

	}
}

function populateDetails($sDept, $sRefNo, $hide_load=0, $checked_requests, $sOR = NULL) {
	global $db;
	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();
	$resultInfo = $cClass->GetRequestInfo($sRefNo,$sDept,$sOR);
	if ($resultInfo) $rRow = $resultInfo->FetchRow();

	if (is_numeric($rRow['grant_amount']))
		$limit = $rRow['grant_amount'];
	else
		$limit = -1;

//	if ($_SESSION['sess_temp_userid'] == 'medocs')
//	{
//		$objResponse->alert($sOR);
//	}

	$rsDetails = $cClass->GetRequestDetails($sRefNo, $sDept, $sOR);
	if ($rsDetails) {
		while ($rowDetails=$rsDetails->FetchRow()) {
			$item->src = strtolower($sDept);
			$item->ref = $sRefNo;
			$item->id = $rowDetails["item_no"];
			$item->name = $rowDetails['item_name'];
			$item->desc = $rowDetails['item_group'];
			$item->qty = $rowDetails['quantity'];
			$item->ispaid = $rowDetails['is_paid'];
			$item->price = $rowDetails["price_cash"];
			$item->origprice = $rowDetails["price_cash_orig"];
			$item->limit = $limit;
			$item->checked = (strpos($checked_requests, $sDept.$sRefNo.$rowDetails["item_no"])!==FALSE || !$sOR);
			$item->showdel= 0;
			$item->calculate= 0;
			$item->doreplace= 1;
			$objResponse->call("addServiceToList",$item);
		}
	}
	$objResponse->call("calcSubTotal",$sDept, $sRefNo);
	if ($hide_load==1) {
		$objResponse->call("refreshTotal");
		$objResponse->call("doneLoading");
	}
	return $objResponse;

}
//edited for splitting doctor pf ken 2/20/2014
function addPFOItem($src, $ref, $code, $qty, $amount, $doctor="") {
	$price = (float)$amount / (float)$qty;

	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();
	// For now ignore items from LD, PH & RD

	if (in_array(strtolower($src),array('other','pp','fb','doctor'))) {
		$info = $cClass->GetPFOItemInfo($src, $ref, $code);
#		$objResponse->addAlert(print_r($info,TRUE));
#		return $objResponse;
		if ($info) {

			$item->src = strtolower($src);
			$item->ref = $ref;
			$item->id = $code;
			$item->name = $info['name'];
			$item->desc = $info['desc'];
			$item->qty = (float)$qty;
			$item->ispaid = 0;
			$item->price = $price;
			$item->origprice = $price;
			$item->limit = -1;
			$item->checked = 1;
			$item->showdel= 1;
			$item->calculate= 1;
			$item->doreplace= 1;

#		$objResponse->alert(print_r($item,TRUE));
#		return $objResponse;
			//edited by ken 2/22/2014 for getting the amount paid in billing
			if($item->src == 'fb'){
				$item->doctor = (float)$doctor / (float)$qty;
				$item->hospital = (float)$amount / (float)$qty;
				$item->ispaid = '';
				$item->isdoctor = '';
				if($amount == 0){
					$item->desc = 'Doctor Bill';
					$item->price = (float)$doctor / (float)$qty;
					$item->origprice = (float)$doctor / (float)$qty;
					$objResponse->call("addServiceToList",$item);
				}
				else if($doctor == 0){
					$item->desc = 'Hospital Bill';
					$item->price = (float)$amount / (float)$qty;
					$item->origprice = (float)$amount / (float)$qty;
					$objResponse->call("addServiceToList",$item);
				}
				else
					$objResponse->call("addServiceToList1",$item);
			}
			else
			$objResponse->call("addServiceToList",$item);
		}
		else {
			$objResponse->alert($cClass->sql);
		}
	}
	return $objResponse;
}

function addReference($sDept, $sRefNo, $checked_requests=NULL, $hide_load=0, $sOR=NULL, $isvat="") {
	global $db;
	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();

	$sDept = strtolower($sDept);
	$resultInfo = $cClass->GetRequestInfo($sRefNo,$sDept,$sOR);
	if ($resultInfo) $rRow = $resultInfo->FetchRow();

	if (is_numeric($rRow['grant_amount']))
		$limit = $rRow['grant_amount'];
	else
		$limit = -1;
	if (in_array(strtolower($sDept), array('ph','rd','ld','misc'))) {
		$dept_names = array('ph'=>'Pharmacy request', 'rd'=>'Radiology request', 'ld'=>'Laboratory request', 'fb'=>'Final billing',	'pp'=>'Partial payment', 'or'=>'Operating room', 'other'=>'Misc. services', 'misc'=>'Miscellaneous request');
		$name = $dept_names[strtolower($sDept)] . " no. $sRefNo";

		$details->name = $name;
		$details->limit = $limit;
		$details->populate = 0;

		$objResponse->call("refreshRequest",$sDept,$sRefNo,$details);
	}

	$rsDetails = $cClass->GetRequestDetails($sRefNo, $sDept, $sOR);
	$if_InPatient = $cClass->checkEncTypeByRefno($sRefNo);#added by daryl

	if ($rsDetails) {
		while ($rowDetails=$rsDetails->FetchRow()) {
			$item->src = $sDept;
			$item->ref = $sRefNo;
			$item->id = $rowDetails["item_no"];
			$item->name = $rowDetails['item_name'];
			$item->desc = $rowDetails['item_group'];
			$item->qty = $rowDetails['quantity'];
			$item->ispaid = $rowDetails['is_paid'];
			$item->price = $rowDetails["price_cash"];
			$item->origprice = $rowDetails["price_cash_orig"];

			$itemOr = $cClass->GetRequestOrNumber($item->ref, $item->src, $item->id);
//			if ($_SESSION['sess_temp_userid'] == 'medocs')
//			{
				// $objResponse->alert($isvat);
//			}

			if ($itemOr !== $sOR)
				$item->flag = strtolower($rowDetails["request_flag"]);
			else
				$item->flag = '';
			if($rowDetails['source_dept'] == 'PH'){
				$item->is_vat = $rowDetails["is_vat"];

				// if($if_InPatient != '3'){
				if($isvat == 'yes')
					$objResponse->call("displayVat", 'yes');

				if($isvat == 'request'){
					if($if_InPatient != '3'){
						$objResponse->call("displayVat", 'yes');
					}
				}

				// }
			}
			$item->limit = $limit;
			$item->checked = (strpos($checked_requests, $sDept.$sRefNo.$rowDetails["item_no"])!==FALSE || !$sOR);
			$item->showdel= 0;
			$item->calculate= 0;
			$item->doreplace= 1;
			$objResponse->call("addServiceToList",$item);
		}
	}
	$objResponse->call("calcSubTotal",$sDept, $sRefNo);
	if ($hide_load==1) {
		$objResponse->call("refreshTotal");
		$objResponse->call("doneLoading");
	}
	return $objResponse;
}

function populateReferences($sDeptArray, $sRefNoArray , $checked_requests) {
	global $db;
	$cClass = new SegCashier();
	$objResponse = new xajaxResponse();

	foreach ($sRefNoArray as $index=>$sRefNo) {
		$sDept = strtolower($sDeptArray[$index]);
		$resultInfo = $cClass->GetRequestInfo($sRefNo,$sDept);
		if ($resultInfo) $rRow = $resultInfo->FetchRow();

		if (is_numeric($rRow['grant_amount']))
			$limit = $rRow['grant_amount'];
		else
			$limit = -1;

		$dept_names = array('ph'=>'Pharmacy request', 'rd'=>'Radiology request', 'ld'=>'Laboratory request', 'fb'=>'Final billing',	'pp'=>'Partial payment', 'or'=>'Operating room', 'other'=>'Misc. services');
		$name = $dept_names[strtolower($sDept)] . " no. $sRefNo";

		$details->name = $name;
		$details->limit = $limit;
		$details->populate = TRUE;

		$objResponse->call("refreshRequest",$sDept,$sRefNo,$details);
	}
	return $objResponse;
}

function populateORParticulars($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$cc = new SegCashier;
	$nr = $args[0];  // get Fetcher parameters
	$offset = $page_num * $max_rows;
	$sortColumns = array('r.service_code','CAST(r.ref_source AS CHAR)','service','r.qty','r.amount_due'); // the data column to be sorted
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "service";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'service ASC'; // Default Sort option

	$result = $cc->GetPayDetails($nr, $offset, $max_rows, $sort_sql);
	if($result) {
		$found_rows = $cc->FoundRows();
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

				$DATA[$i]['code'] = $row['service_code'];
				$DATA[$i]['source'] = $row['ref_source'];
				$DATA[$i]['service'] = $row["service"];
				$DATA[$i]['price'] = number_format($row['amount_due']/$row['qty'],2);
				$DATA[$i]['quantity'] = $row['qty'];
				$DATA[$i]['total'] = number_format($row['amount_due'],2);
				$DATA[$i]['FLAG'] = 1;
				$i++;
			} //end while
			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
			//$objResponse->alert(print_r($DATA,true));
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		// error
		$objResponse->alert($cc->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}
	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

require('./roots.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_cashier.php');
require($root_path.'include/care_api_classes/class_cashier_service.php');
require($root_path."modules/cashier/ajax/cashier-main.common.php");
$xajax->processRequest();