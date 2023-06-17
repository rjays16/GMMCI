<?php

function updateBalance($pid) {
	global $db, $config;
	$objResponse = new xajaxResponse();
	$bal = $db->GetOne('SELECT running_balance FROM seg_lingap_patient_balance WHERE pid='.$db->qstr($pid));
	$objResponse->assign('bal','value',number_format($bal,2));
	return $objResponse;
}

function populateAdjustments($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
	global $config;
	$objResponse = new xajaxResponse();  
	$lc = new SegLingapPatient();
	
	$filters = array();
	$filters["PID"] = $args["pid"];
	
	$offset = $page_num * $max_rows;
	$sortColumns = array('entry_date','account_name','amount','create_id');
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
	else $sort_sql = 'entry_date ASC';
	
	$filters['OFFSET'] = $offset;
	$filters['MAXROWS'] = $max_rows;
	$filters['SORTSQL'] = $sort_sql;
	$filters['ENTRYTYPE'] = 'adjustment';
	
	$result=$lc->getLedger($filters);
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
				$DATA[$i]['date'] = $row['entry_date'];
				$DATA[$i]['amount'] = $row['amount'];
				$DATA[$i]['encoder'] = $row['create_id'];
				$DATA[$i]['remarks'] = $row['remarks'];
				$DATA[$i]['status'] = '';
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

require('./roots.php');
require($root_path.'include/inc_environment_global.php'); 
require($root_path.'include/care_api_classes/sponsor/class_lingap.php');
require($root_path.'include/care_api_classes/sponsor/class_lingap_patient.php');
require_once($root_path.'modules/sponsor/ajax/lingap_adjustment.common.php');
$xajax->processRequest();
