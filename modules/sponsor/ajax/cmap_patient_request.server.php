<?php


function save( $wId, $data ) {
	global $db, $errorReporter;
	$objResponse = new xajaxResponse();

// Expected format for $data array
//    [src] 					=> Code for request type
//    [ref_no] 				=> Reference no. for request
//    [service_code] 	=> Item code of request item
//    [entry] 				=> entry number (for seg_misc_services)
//    [pid] 					=> pid of patient
//    [service_name] 	=> Request item name
//    [amount] 				=> Amount to grant
//    [remarks] 			=> Additional remarks

	$requestType = $data['src'];
	$request = new SegRequest($requestType, array(
		'refNo' 	=> $data['ref_no'],
		'itemNo' 	=> $data['service_code'],
		'entryNo'	=> $data['entry']
	));

	$cmap = new SegCmapGrantor();

	//$grant = new SegRequestGrant($requestType);

	$db->StartTrans();
	//$saveok = $grant->grant(SegRequestGrant::GRANT_CMAP, $data['amount'], $data['remarks']);

	$saveok = $cmap->grant($request, $data['amount'], $data['remarks']);

	if ($saveok) {
		$db->CompleteTrans();
		//$objResponse->call('parent.xajax_updateBalance', $data['pid'] );
		//$objResponse->call('parent.alst.reload');
		//$objResponse->call('parent.rlst.reload');
		//$objResponse->call('rlst.reload');
		//$objResponse->call('alert', 'Entry successfully saved!');
		$objResponse->call('doneLoading');
		$objResponse->call('closeGrant', $wId);
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		$objResponse->alert("An error has occurred. Please contact your administrator!");
		$objResponse->call('doneLoading');
	}
	return $objResponse;
}


function populateRequestList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $db;
	$objResponse = new xajaxResponse();

	//$filters = $args;
	$filters['PID'] = $args['PID'];

	//if ($args['ENCOUNTER'])
		//$filters['ENCOUNTER'] = $args['ENCOUNTER'];

	if ($args['FILTER_NAME'])
		$filters['NAME'] = $args['FILTER_NAME'];

	if ($args['FILTER_DATE'])
		$filters['DATE'] = $args['FILTER_DATE'];

	//added by cha, 11-12-2010
	if ($args['FILTER_DATEBETWEEN'])
		$filters['DATEBETWEEN'] = $args['FILTER_DATEBETWEEN'];

	//added by cha, 11-26-2010
	if ($args['FILTER_MISC_DEPT'])
		$filters['MISC_DEPT'] = $args['FILTER_MISC_DEPT'];

	if ($args['FILTER_FLAG']) {
		// Treat 'NULL' literal as equivalent to database NULL value
		if ($args['FILTER_FLAG'] === 'NULL') {
			$filters['FLAG'] = null;
		}
		else {
			$filters['FLAG'] = $args['FILTER_FLAG'];
		}
	}

	$filters['CASH'] = true;
	$filters['DELETED'] = 0;

	if ($args['FILTER_SOURCE'])
		$requestType = $args['FILTER_SOURCE'];
	else
		$requestType = SegRequest::BILLING_REQUEST;

	if (!$filters)
		$filters=array();

	$offset = $page_num * $max_rows;
	$sortColumns = array('date','source','refno','name','total','grant');
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
	else $sort_sql = 'source ASC';

	list($filters['OFFSET'], $filters['ROWS'], $filters['SORT']) = array($offset, $max_rows, $sort_sql);

	$request = new SegRequest($requestType);
	$rows=$request->get($filters);
	//$objResponse->alert($request->getLastQuery());
	if($rows!==false) {
		$found_rows = $request->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if ($data_size=count($rows)) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			foreach ($rows as $row) {

				$DATA[$i] = Array(
					'source' 	=> $requestType,
					'refno' 	=> $row['refNo'],
					'date' 		=> nl2br(date("d-M-Y\nh:ia", strtotime($row['date']))),
					'entry' 	=> $row['entryNo'],
					'itemno' 	=> $row['itemNo'],
					'name' 		=> $row['itemName'],
					'desc' 		=> $row['description'],
					'qty'	 	=> $row['quantity'],
					'price' 	=> $row['unitPrice'],
					'total' 	=> $row['total'],
					'flag' 		=> strtolower($row['requestFlag']),
					'served'	=> $row['isServed']
				);

				//$grant = new SegRequestGrant($requestType, array('refNo'=>$row['refNo'], 'itemNo'=>$row['itemNo'], 'entryNo'=>$row['entryNo'] ));
				$request = new SegRequest( $requestType, array('refNo'=>$row['refNo'], 'itemNo'=>$row['itemNo'], 'entryNo'=>$row['entryNo'] ) );

				$grant = SegGrant::getInstance();
				$DATA[$i]['grant'] = $grant->getTotalGrants( $request );
				$i++;

			} //end while

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
			$objResponse->assign('print_button', 'disabled', false);	//added by cha, 11-12-2010
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
			$objResponse->assign('print_button', 'disabled', true);	//added by cha, 11-12-2010
		}
	} else {
		// error
		$objResponse->alert("An error has occurred! Please contact your system administrator!");
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

//function populateCMAPEntries($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
//	global $db, $config;

//	$objResponse = new xajaxResponse();
//	$lc = new SegCMAP();
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

//	$filters["OFFSET"] = $offset;
//	$filters["ROWS"] = $max_rows;
//	$filters["SORT"] = $sort_sql;

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

//			if (defined('__DEBUG_MODE'))
//				$objResponse->alert($lc->sql);
//		}

//	} else {
		// error
//			if (defined('__DEBUG_MODE'))
//				$objResponse->alert($lc->sql);
//			else
//				$objResponse->alert("A database error has occurred. Please contact your system administrator...");

//		$objResponse->contextAssign('dataSize', -1);
//		$objResponse->contextAssign('listData', NULL);
//	}

//	$objResponse->script('this.fetchDone()');
//	return $objResponse;
//}


require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/sponsor/class_cmap.php";
require_once $root_path."include/care_api_classes/sponsor/class_cmap_patient.php";

require_once $root_path."include/care_api_classes/sponsor/class_request.php";
require_once $root_path."include/care_api_classes/sponsor/class_grant.php";
require_once $root_path."include/care_api_classes/sponsor/grantors/class_cmap_grantor.php";


require_once $root_path."modules/sponsor/ajax/cmap_patient_request.common.php";
$xajax->processRequest();
