<?php
function populateRequests($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();
	$objSS = new SocialService;
	$enc_obj=new Encounter;
	global $HTTP_SESSION_VARS, $db;

	$encounter_nr = $HTTP_SESSION_VARS['sess_en'];
	$pid = $HTTP_SESSION_VARS['sess_pid'];

	$offset = $page_num * $max_rows;
	$sortColumns = array('refno','date_request','dept','total_charge');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "date_request";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'date_request DESC';

	$sslist = $objSS->getLCRInforequest($pid, $encounter_nr, $offset, $max_rows, $sort_sql);
	//$objResponse->alert($objSS->sql);
	if($sslist) {
		$found_rows = $objSS->FoundRows();
		$last_page = ceil($found_rows/$max_rows)-1;
		if ($page_num > $last_page) $page_num=$last_page;

		if($data_size=$sslist->RecordCount()) {
			$temp=0;
			$i=0;
			$objResponse->contextAssign('currentPage', $page_num);
			$objResponse->contextAssign('lastPage', $last_page);
			$objResponse->contextAssign('maxRows', $max_rows);
			$objResponse->contextAssign('listSize', $found_rows);

			$DATA = array();
			while($row = $sslist->FetchRow()) {
				#$date_request = date("Y-m-d  h:ia",strtotime($row["time_request"]));
				$date_request = date("m-d-Y",strtotime($row["date_request"]))." ".date("h:ia",strtotime($row["time_request"]));
				$total_charge = number_format($row['total_charge'], 2);

				$DATA[$i]['ref_no'] = $row['refno'];
				$DATA[$i]['request_date'] = $date_request;
				$DATA[$i]['timestamp'] = $row["time_request"];
				$DATA[$i]['total_charge'] = $total_charge;
				$DATA[$i]['dept'] = $row['dept'];
				$DATA[$i]['modifier3_text'] = $mod3[1];
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
		#$objResponse->alert($objSS->sql);
		$objResponse->contextAssign('dataSize', -1);
		$objResponse->contextAssign('listData', NULL);
	}

	$objResponse->script('this.fetchDone()');
	return $objResponse;
}

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_social_service.php');
require($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'modules/system_admin/ajax/seg_override_common.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
$xajax->processRequest();

?>