<?php

function populateSSRequests($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $config;
	$objResponse = new xajaxResponse();

	# initialize Lingap helper class
	$lc = new SegLingap;

	# Get passed args
	$filters = array();
	$filters["PID"] = $args["pid"];

	# Prepare sorting scheme
	$sortColumns = array('date_generated','control_nr','name', 'is_advance'); // corresponding sort fields
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
	else $sort_sql = 'date_generated DESC'; // default sorting
	$filters["SORTSQL"] = $sort_sql;

	# Compute query offset and limit
	$offset = $page_num * $max_rows;
	$filters["OFFSET"] = $offset;
	$filters["MAXROWS"] = $max_rows;

	# Fetch data
	$result=$lc->getSSRequests($filters);
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
				$DATA[$i]['nr'] = $row['control_nr'];
				#$DATA[$i]['date'] = nl2br(date("d-M-Y\nh:ia", strtotime($row['date'])));
				$DATA[$i]['date'] = $row['date_generated'];
				$DATA[$i]['name'] = $row['name'];
				$DATA[$i]['entry'] = $row['entry_id'];
				$DATA[$i]['is_advance'] = $row['is_advance'];
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
				$objResponse->alert($lc->sql);
		}

	} else {
		// error
			if ($config['debug'])
				$objResponse->alert($lc->sql);
			else
				$objResponse->alert("A database error has occurred. Please contact your system administrator...");

		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

function cancelEntry($id) {
	global $db;
	$objResponse = new xajaxResponse();
	$lc = new SegLingapPatient();
	$db->StartTrans();
	if ($lc->cancelEntry($id)) {
		$objResponse->call('prepareDelete',$src.$id);
		$objResponse->alert('Entry successfully saved!');
		$objResponse->script('xajax.call("updateBalance", { parameters: [$("pid").value] })');
	}
	else {
		$db->FailTrans();
		$objResponse->call('cancelDelete',$src.$id);
		$objResponse->alert('An error has occurred! Please contact your administrator!');
		$objresponse->call('doneLoading');
	}
	$db->CompleteTrans();
	return $objResponse;
}

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/sponsor/class_lingap.php');
require($root_path.'include/care_api_classes/sponsor/class_lingap_patient.php');
require_once($root_path.'modules/sponsor/ajax/lingap-patient.common.php');
$xajax->processRequest();
