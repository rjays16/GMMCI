<?php

function updateBalance($nr) {
	global $db;
	$objResponse = new xajaxResponse();
	//$bal = $db->GetOne('SELECT running_balance FROM seg_cmap_accounts WHERE account_nr='.$db->qstr($nr));
	$account = new SegCmapAccount($nr);
	$actual_balance = $account->getActualBalance();
	$referred_balance = $account->getReferredBalance();
	$objResponse->assign('actual-balance','value',number_format($actual_balance, 2));
	$objResponse->assign('referred-balance','value',number_format($referred_balance, 2));
	return $objResponse;
}

function populateAllotments($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $config;

	$objResponse = new xajaxResponse();
	$allotment = new SegCmapAllotment();

	$filters = array();
	$filters["NR"] = $args["nr"];
	$offset = $page_num * $max_rows;
	$sortColumns = array('alt.allotment_date','acc.account_name','alt.amount','alt.create_name');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			 if ($col = ($sortColumns[$i] ? $sortColumns[$i] : FALSE)) {
				 if ((int)$v < 0) $sort[] = "$col DESC";
				 elseif ((int)$v > 0) $sort[] = "$col ASC";
			 }
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'allotment_date DESC';

	$filters['OFFSET'] = $offset;
	$filters['MAXROWS'] = $max_rows;
	$filters['SORTSQL'] = $sort_sql;

	$data = $allotment->getAllotments($args['nr'], 'ORDER BY '.$sort_sql, $max_rows, $offset);
	if($data!==false) {
		$found_rows = $allotment->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=sizeof($data)) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();

			foreach ($data as $row) {
				$DATA[$i]['id'] = $row['id'];
				$DATA[$i]['date'] = date("F j, Y",strtotime($row['allotment_date']));
				$DATA[$i]['amount'] = $row['amount'];
				$DATA[$i]['saro'] = $row['saro_no'];
				$DATA[$i]['nca'] = $row['nca_no'];
				$DATA[$i]['encoder'] = $row['create_name'];
				$DATA[$i]['remarks'] = $row['remarks'];
				$DATA[$i]['status'] = '';
				$DATA[$i]['FLAG'] = 1;
				$i++;
			} //end for

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		// error
		if ($config['debug'])
		{
			$objResponse->alert($allotment->getQuery());
		}
		else
		{
			$objResponse->alert("A database error has occurred. Please contact your system administrator...");
		}
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

#added by cha, June 16, 2010
function deleteAccount($account_id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "UPDATE seg_cmap_accounts SET is_deleted='1' WHERE account_nr=".$db->qstr($account_id);
	if($result=$db->Execute($sql))
	{
		if($db->Affected_Rows()>0){
			$objResponse->call("showResponse","Delete successful.", "");
		}
	}else
	{
		$objResponse->call("showResponse","Delete not successful.", $db->ErrorMsg());
	}
	return $objResponse;
}

function saveAccount($name, $address)
{
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "INSERT INTO seg_cmap_accounts (account_name, account_address) VALUES (".
		$db->qstr($name).",".$db->qstr($address).
		")";
	if($result=$db->Execute($sql))
	{
		if($db->Affected_Rows()>0){
			$objResponse->call("showResponse","Save successful.", "");
		}
	}else
	{
		$objResponse->call("showResponse","Save not successful.", $db->ErrorMsg());
	}
	return $objResponse;
}

function updateAccount($id, $name, $address)
{
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "UPDATE seg_cmap_accounts SET account_name=".$db->qstr($name).
		", account_address=".$db->qstr($address)." WHERE account_nr=".$db->qstr($id);
	if($result=$db->Execute($sql))
	{
		if($db->Affected_Rows()>0){
			$objResponse->call("showResponse","Update successful.", "");
		}
	}else
	{
		$objResponse->call("showResponse","Update not successful.", $db->ErrorMsg());
	}
	return $objResponse;
}
#end cha

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/sponsor/class_cmap.php');
require($root_path.'include/care_api_classes/sponsor/class_cmap_allotment.php');
require($root_path.'include/care_api_classes/sponsor/class_cmap_account.php');
require_once($root_path.'modules/sponsor/ajax/cmap_account.common.php');
$xajax->processRequest();
