<?php

function populateWalkinRequestList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $db, $config;
	$objResponse = new xajaxResponse();
	$referral = new SegLingapReferral();

	$filters = array();
	$pid = $args["pid"];

	$offset = $page_num * $max_rows;
	$sortColumns = array('entry_date');
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
	else $sort_sql = 'entry_date DESC';

	$filters["OFFSET"] = $offset;
	$filters["MAXROWS"] = $max_rows;
	$filters["SORTSQL"] = $sort_sql;

	//$result=$lc->getGrants($filters);

	$result = $referral->fetchAll("walkin_pid=".$db->qstr($pid), $sort_sql, $offset, $max_rows, true);

	if($result !== false) {
		$found_rows = $referral->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=sizeof($result)) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();

//			$source_text = array(
//				'FB'=>'Hospital bill',
//				'LD'=>'Laboratory',
//				'RD'=>'Radiology',
//				'PH'=>'Meds/Supplies',
//				'OP'=>'Operating room',
//			);

			foreach ($result as $i=>$row) {
				//$DATA[$i]['source'] = $source_text[$row['source']];
				$DATA[$i]['entryId'] = $row['id'];
				$DATA[$i]['controlNo'] = $row['control_nr'];
				$DATA[$i]['date'] = nl2br(date("d-M-Y\nh:ia", strtotime($row['entry_date'])));
				$DATA[$i]['name'] = $row['name'];
				$DATA[$i]['quantity'] = $row['quantity'];
				$DATA[$i]['isAdvance'] = $row['is_advance'];

				$referrer = new SegLingapReferral($row['id']);
				$lingapGrantor = new SegLingapGrantor( $referrer );
				$grants = $lingapGrantor->getGrants();

				$items = array();
				$total = 0;
				foreach ($grants as $grant) {
					$items[] = $grant['itemName'];
					$total += $grant['totalAmount'];
				}

				$DATA[$i]['items'] = implode(", ", $items);
				$DATA[$i]['totalAmount'] = $total;
			} //end while

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);

//			if ($config['debug'])
//				$objResponse->alert($referral->getQuery());
		}

	} else {
		// error
			if ($config['debug'])
				$objResponse->alert($referral->getQuery());
			else
				$objResponse->alert("A database error has occurred. Please contact your system administrator...");

		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}


function cancel($id, $remarks='') {
	$objResponse = new xajaxResponse();
	$referral = new SegLingapReferral($id);
	if ($referral) {
		$saveok = $referral->cancel($remarks);
	}
	else {
		$saveok = false;
	}

	if ($saveok) {
		$objResponse->alert('Lingap referral successfully cancelled!');
	}
	else {
		$objResponse->alert("Error occurred in cancelling Lingap referral. Please contact your System administrator.");
	}
	return $objResponse;
}

//function populateLingapEntries($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
//	global $config;

//	$objResponse = new xajaxResponse();
//	$lc = new SegLingapWalkin();
//	$filters = array();

//	$offset = $page_num * $max_rows;
//	$sortColumns = array('entry_date','control_nr','name','item','total','encoder');
//	$sort = array();
//	if (is_array($sort_obj)) {
//		foreach ($sort_obj as $i=>$v) {
//			$col = $sortColumns[$i] ? $sortColumns[$i] : "entry_date";
//			if ((int)$v < 0) $sort[] = "$col DESC";
//			elseif ((int)$v > 0) $sort[] = "$col ASC";
//		}
//	}
//	if ($sort) $sort_sql = implode(',', $sort);
//	else $sort_sql = 'entry_date DESC';

//	$filters["PID"] = $args[0];
//	$filters["OFFSET"] = $offset;
//	$filters["ROWCOUNT"] = $max_rows;
//	$filters["SORTSQL"] = $sort_sql;

//	$result=$lc->getAllEntries($filters);

//	if($result) {
//		$found_rows = $lc->FoundRows();
//		$last_page = ceil($found_rows/$max_rows)-1;
//		if ($page_num > $last_page) $page_num=$last_page;

//		if($data_size=$result->RecordCount()) {
//			$temp=0;
//			$i=0;
//			$objResponse->contextAssign('currentPage', $page_num);
//			$objResponse->contextAssign('lastPage', $last_page);
//			$objResponse->contextAssign('maxRows', $max_rows);
//			$objResponse->contextAssign('listSize', $found_rows);

//			$DATA = array();
//			while($row = $result->FetchRow()) {

//				$DATA[$i]['date'] = $row['entry_date'];
//				$DATA[$i]['nr'] = $row['control_nr'];
//				$DATA[$i]['name'] = $row['name'];
//				$DATA[$i]['items'] = $row['items'];
//				$DATA[$i]['total'] = $row['total'];
//				$DATA[$i]['encoder'] = $row['create_id'];
//				$DATA[$i]['status'] = "";
//				$DATA[$i]['FLAG'] = 1;
//				$i++;
//			} //end while

//			$objResponse->contextAssign('dataSize', $data_size);
//			$objResponse->contextAssign('listData', $DATA);
//		}
//		else {
//			$objResponse->contextAssign('dataSize', 0);
//			$objResponse->contextAssign('listData', NULL);

//			if ($config['debug'])
//				$objResponse->alert($lc->sql);
//		}

//	} else {
		// error
//			if ($config['debug'])
//				$objResponse->alert($lc->sql);
//			else
//				$objResponse->alert("A database error has occurred. Please contact your system administrator...");

//		$objResponse->contextAssign('dataSize', -1);
//		$objResponse->contextAssign('listData', NULL);
//	}

//	$objResponse->script('this.fetchDone()');
//	return $objResponse;
//}


require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/sponsor/class_lingap_referral.php';
require_once $root_path.'include/care_api_classes/sponsor/grantors/class_lingap_grantor.php';

//require_once($root_path.'include/care_api_classes/sponsor/class_lingap.php');
//require_once($root_path.'include/care_api_classes/sponsor/class_lingap_walkin.php');

require_once $root_path.'modules/sponsor/ajax/lingap_walkin.common.php';
$xajax->processRequest();
