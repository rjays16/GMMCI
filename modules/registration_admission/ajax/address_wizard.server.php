<?php

require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/registration_admission/ajax/address_wizard.common.php');
require_once($root_path.'include/care_api_classes/class_address.php');


function populateAddress($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	$objResponse = new xajaxResponse();	
	$ac = new Address();
	$keyword = $args[0];
	$mode = strtoupper($args[1]);
	$offset = $page_num * $max_rows;
	$sortColumns = array('code','name','full','location');
	$sort = array();
	if (is_array($sort_obj)) {
		foreach ($sort_obj as $i=>$v) {
			$col = $sortColumns[$i] ? $sortColumns[$i] : "name";
			if ((int)$v < 0) $sort[] = "$col DESC";
			elseif ((int)$v > 0) $sort[] = "$col ASC";
		}
	}
	if ($sort) $sort_sql = implode(',', $sort);
	else $sort_sql = 'name ASC';
	
	$result = $ac->catchAllAddresses($keyword, $mode, $offset, $max_rows, $sort_sql);
	# $objResponse->alert($ac->sql);
	if($result) {
		$found_rows = $ac->FoundRows();
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

				$DATA[$i]['code'] = $row['code'];
				$DATA[$i]['name'] = $row['name'];
				$DATA[$i]['full'] = $row["full"];
				$DATA[$i]['location'] = $row['location'];
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

$xajax->processRequest();
