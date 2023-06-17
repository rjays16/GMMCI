<?php
global $config;

function populateWalkinRequestList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $config;
	$objResponse = new xajaxResponse();

	/**
	* Script only handles PHARMACY walkin request for now
	*
	* @todo Handle walkin requests from other cost centers
	*/
	$requestType = SegRequest::PHARMACY_WALKIN_REQUEST;
	$request = new SegRequest( $requestType );

	$filters = array();
	$filters["PID"] = $args["pid"];
	if ($args["date"]) {
		$filters["DATE"] = $args["date"];
	}
	if ($args["entry"]) {
		$Id = $args["entry"];
	}

	$offset = $page_num * $max_rows;
	$sortColumns = array('date','refNo','itemName');
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
	else $sort_sql = 'date DESC';

	$filters['SORT'] = $sort_sql;
	$filters['ROWS'] = $max_rows;
	$filters['OFFSET'] = $offset;
	$result = $request->get( $filters );
	if($result !== false) {
		$found_rows = $request->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=count($result)) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			if ($Id) {
				$referral = new SegLingapReferral($Id);
				$lingapGrantor = new SegLingapGrantor($referral);
			}

			foreach ($result as $i=>$row) {
				$DATA[$i]['source'] = $requestType;
				$DATA[$i]['refno'] = $row['refNo'];
				$DATA[$i]['date'] = nl2br(date("d-M-Y\nh:ia", strtotime($row['date'])));
				$DATA[$i]['itemno'] = $row['itemNo'];
				$DATA[$i]['name'] = $row['itemName'];
				$DATA[$i]['qty'] = $row['quantity'];
				$DATA[$i]['total'] = (float)$row['unitPrice'] * (float)$row['quantity'];
				$DATA[$i]['flag'] = $row['requestFlag'];
				$DATA[$i]['entryId'] = '';

				if ($row['requestFlag'] == 'lingap' && $lingapGrantor) {
					$wRequest = new SegRequest($requestType, Array(
						'refNo' => $row['refNo'],
						'itemNo' => $row['itemNo']
					));

					$total = $lingapGrantor->getTotalGrants($wRequest);
					if ($total) {
						$DATA[$i]['entryId'] = $Id;
					}

				}
			} //end while

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);

//			if ( $config['debug'] )
//				$objResponse->alert("SQL empty result:\n".$request->getQuery());
		}

	} else {
		// error
		if ( $config['debug'] )
			$objResponse->alert($request->getQuery());
		else
			$objResponse->alert("A database error has occurred. Please contact your system administrator...");

		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');

	return $objResponse;
}

//function populateLingapEntries($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
//	global $config;

//	$objResponse = new xajaxResponse();
//	$lc = new SegLingap();
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

//			if ( $config['debug'] )
//				$objResponse->alert("SQL empty result:\n".$lc->sql);
//		}

//	} else {
		// error
//			if ( $config['debug'] )
//				$objResponse->alert("SQL error:\n".$lc->sql);
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
require_once $root_path.'include/care_api_classes/sponsor/class_lingap_walkin.php';
require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
require_once $root_path.'include/care_api_classes/sponsor/class_lingap_referral.php';
require_once $root_path.'include/care_api_classes/sponsor/grantors/class_lingap_grantor.php';
require_once $root_path.'modules/sponsor/ajax/lingap_walkin_request.common.php';
$xajax->processRequest();
