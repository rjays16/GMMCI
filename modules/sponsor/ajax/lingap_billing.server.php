<?php

function populateBillingStatements($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $db, $config;
	$objResponse = new xajaxResponse();

	# initialize helper class
	$request = new SegRequest(SegRequest::BILLING_REQUEST);
	$grant = SegGrant::getInstance();

	# Get passed args
	$pid = $args["pid"];

	# Prepare sorting scheme
	$sortColumns = array(FALSE,'bill_dte'); // corresponding sort fields
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			 if ($col = ($sortColumns[$i] ? $sortColumns[$i] : FALSE)) {
				 if ((int)$v < 0) $sort[] = "$col DESC";
				 elseif ((int)$v > 0) $sort[] = "$col ASC";
			 }
		}
	}
	if (!$sort)
		$sort = array('bill_dte DESC'); // default sorting
	//$filters["SORTSQL"] = $sort_sql;

	# Compute query offset and limit
	$offset = $page_num * $max_rows;
//	$filters["OFFSET"] = $offset;
//	$filters["ROWCOUNT"] = $max_rows;

	# Fetch data
	//$result=$bc->getBillingStatements($filters);
	$result=$request->get(Array(
		'PID' => $pid,
		'SORT' => $sort,
		'ROWS' => $max_rows,
		'OFFSET' => $offset
	));
	if ($result !== false) {
		//$found_rows = $bc->FoundRows();
		$found_rows = $request->FoundRows();
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

			foreach ($result as $i=>$row) {
				$DATA[$i]['nr'] = $row['refNo'];
				$DATA[$i]['date'] = nl2br(date("d-M-Y\nh:ia", strtotime($row['date'])));
				$DATA[$i]['due'] = $row['unitPrice']*$row['quantity'];

				//$DATA[$i]['grant'] = $row['grant'];
				$rq = new SegRequest(SegRequest::BILLING_REQUEST, array(
					'refNo' => $row['refNo']
				));
				$DATA[$i]['grant'] = $grant->getTotalGrants($rq);
				$DATA[$i]['ward'] = $row['ward'];
			} //end while

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			// empty record
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);
		}

	} else {
		// error
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
		$objResponse->alert('An error has occurred! Please contact your administrator.');
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/sponsor/class_billing_grant.php';
require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
require_once $root_path.'include/care_api_classes/sponsor/class_grant.php';
require_once $root_path.'modules/sponsor/ajax/lingap_billing.common.php';

$xajax->processRequest();
