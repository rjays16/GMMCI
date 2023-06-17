<?php
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
					$DATA[$i]['status'] = $row['status'];
					$DATA[$i]['FLAG'] = 1;
					$i++;

				} //end while
				
				$objResponse->contextAssign('dataSize', $data_size);
				$objResponse->contextAssign('listData', $DATA);
			}
			else {
				$objResponse->contextAssign('dataSize', 0);
				$objResponse->contextAssign('listData', NULL);
				
				if ( $config['debug'] )
					$objResponse->alert($lc->sql);
			}
			
		} else {
			// error
				if ( $config['debug'] )
					$objResponse->alert($lc->sql);
				else
					$objResponse->alert("A database error has occurred. Please contact your system administrator...");
					
			$objResponse->contextAssign('dataSize', -1);
			$objResponse->contextAssign('listData', NULL);
		}  
		
		$objResponse->script('this.fetchDone()');
		return $objResponse;
	}

	function populateLingapEntries($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
		
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
					$objResponse->alert($lc->sql);
			}
			
		} else {
			// error
				if ($config['debug']) {
					$objResponse->alert($lc->sql);
				}
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
	require_once($root_path.'modules/sponsor/ajax/lingap.common.php');
	$xajax->processRequest();
