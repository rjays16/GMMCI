<?php


function populateRequestList($filters) {
	global $config, $root_path, $db;
	$objResponse = new xajaxResponse();

	require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
	$types=SegRequest::getRequestTypes();
	$type_keys = array_keys($types);

	$query = "SELECT value,unit FROM seg_default_value WHERE name='lingap expiry' AND source='SS'";
	$expiry_params = $db->GetRow($query);

#	$objResponse->alert(print_r($filters, true));
	foreach ($type_keys as $type) {

		$options = Array(
			'PID' 	=> $filters['PID'],
			'INTERVALFROMDATE' 	=> array(
				'date' => $filters['DATE'],
				'count' => $expiry_params['value'],
				'interval' => $expiry_params['unit']
			),
			'DISCOUNT' => 'lingap',
			'DELETED' => 0,
			'SORT' 	=> 'date DESC'
		);

		$data = array();
		// ignore Billing and Walkin requests
		if (!in_array($type, array(SegRequest::BILLING_REQUEST, SegRequest::PHARMACY_WALKIN_REQUEST))) {

			$request = new SegRequest($type);
			$rows = $request->get($options);

//				$DATA[$i]['source'] = $row['source'];
//				$DATA[$i]['refno'] = $row['refno'];
//				$DATA[$i]['date'] = nl2br(date("d-M-Y\nh:ia", strtotime($row['date'])));
//				$DATA[$i]['itemno'] = $row['itemno'];
//				$DATA[$i]['name'] = $row['name'];
//				$DATA[$i]['qty'] = $row['qty'];
//				$DATA[$i]['total'] = $row['total'];
//				$DATA[$i]['discounted'] = $row['discounted'];
//				$DATA[$i]['flag'] = $row['request_flag'];

//			if ($_SESSION['sess_temp_userid'] == 'medocs') {
//				$objResponse->alert($request->getQuery());
//			}
			if ($rows !== false) {                
                $objResponse->call('clearRequestList', $type);
				foreach ($rows as $row) {
					$data = Array(
						'type' => $type,
						'typeName' => ucfirst(strtolower($types[$type])).' requests',
						'date' => nl2br(date("d-M-Y\nh:ia", strtotime($row['date']))),
						'refNo' => $row['refNo'],
						'itemNo' => $row['itemNo'],
						'entryNo' => $row['entryNo'],
						'itemName' => $row['itemName'],
						'quantity' => $row['quantity'],
						'unitPrice' => $row['unitPrice'],
						'totalAmount' => (float)$row['unitPrice']*(float)$row['quantity'],
						'requestFlag' => $row['requestFlag']
					);
					$objResponse->call('addRequest', $data);
				}
			}
			else {
				$objResponse->alert('A server error has occurred! Please contact your system Administrator!');
				return $objResponse;
			}
		}
		else {
			// ignored requests
		}
	}
	$objResponse->call('calculateTotals');
	return $objResponse;

}

/**
* put your comment there...
*
* @param mixed $page_num
* @param mixed $max_rows
* @param mixed $sort_obj
* @param mixed $args
* @return xajaxResponse
*/
function populatePatientRequestList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $config;
	$objResponse = new xajaxResponse();
	$lc = new SegLingap();

	$filters = array();
	$filters["PID"] = $args["pid"];
	$filters["DATE"] = $args["date"];

	$offset = $page_num * $max_rows;
	$sortColumns = array('date','source','refno','name','total');
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

	$result=$lc->getRequestItems($filters, $offset, $max_rows, $sort_sql);
	if($result) {
		$found_rows = $lc->FoundRows();
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

			$source_text = array(
				'FB'=>'Hospital bill',
				'LD'=>'Laboratory',
				'RD'=>'Radiology',
				'PH'=>'Meds/Supplies',
				'OP'=>'Operating room',
			);

			while($row = $result->FetchRow()) {

				$DATA[$i]['source'] = $row['source'];
				$DATA[$i]['refno'] = $row['refno'];
				$DATA[$i]['date'] = nl2br(date("d-M-Y\nh:ia", strtotime($row['date'])));
				$DATA[$i]['itemno'] = $row['itemno'];
				$DATA[$i]['name'] = $row['name'];
				$DATA[$i]['qty'] = $row['qty'];
				$DATA[$i]['total'] = $row['total'];
				$DATA[$i]['discounted'] = $row['discounted'];
				$DATA[$i]['flag'] = $row['request_flag'];
				$i++;

			} //end while

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);

			if ( $config['debug'] ) {
				$objResponse->alert('SQL empty result: '.$lc->sql);
			}
		}

	} else {
		// error
		if ( $config['debug'] )
			$objResponse->alert('SQL error: '.$lc->sql);
		else
			$objResponse->alert("A database error has occurred. Please contact your system administrator...");

		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	sleep(1);
	return $objResponse;
}

function populateLingapEntries($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $config;

	$objResponse = new xajaxResponse();
	$lc = new SegLingap();
	$filters = array();

	$offset = $page_num * $max_rows;
	$sortColumns = array('entry_date','control_nr','name','item','total','encoder');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "entry_date";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'entry_date DESC';

	$filters["OFFSET"] = $offset;
	$filters["ROWCOUNT"] = $max_rows;
	$filters["SORTSQL"] = $sort_sql;

	$result=$lc->getAllEntries($filters);

	if($result) {
		$found_rows = $lc->FoundRows();
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

				$DATA[$i]['date'] = $row['entry_date'];
				$DATA[$i]['nr'] = $row['control_nr'];
				$DATA[$i]['name'] = $row['name'];
				$DATA[$i]['items'] = $row['items'];
				$DATA[$i]['total'] = $row['total'];
				$DATA[$i]['encoder'] = $row['create_id'];
				$DATA[$i]['status'] = "";
				$DATA[$i]['FLAG'] = 1;
				$i++;
			} //end while

			$objResponse->contextAssign('dataSize', $data_size);
			$objResponse->contextAssign('listData', $DATA);
		}
		else {
			$objResponse->contextAssign('dataSize', 0);
			$objResponse->contextAssign('listData', NULL);

			if ($config['debug'])
				$objResponse->alert('SQL empty result: '.$lc->sql);
		}

	} else {
		// error
			if ($config['debug'])
				$objResponse->alert('SQL error: '.$lc->sql);
			else
				$objResponse->alert("A database error has occurred. Please contact your system administrator...");

		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}


require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/sponsor/class_lingap.php';
require_once $root_path.'modules/sponsor/ajax/lingap_patient_request.common.php';
$xajax->processRequest();
