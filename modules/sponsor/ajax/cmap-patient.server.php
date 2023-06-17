<?php
	function updateBalance($pid) {
		global $db, $errorReporter;
		$objResponse = new xajaxResponse();
		$pc = new SegCmapPatient;
		$bal = $pc->getBalance($pid);
		$objResponse->assign('bal','value',number_format($bal,2));
		if ($errorReporter->hasErrors()) {
			$objResponse->call('alertSeg', $errorReporter->report(), 'alert',
				array(
					'id'=>'bOk',
					'label'=>'Okay!',
					'icon'=>'../../gui/img/common/default/tick.png',
					'callback'=>'doneLoading'
				)
			);
		}
		return $objResponse;
	}

	function populatePatientRequestList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
		global $errorReporter;
		$objResponse = new xajaxResponse();
		$cc = new SegCMAPPatient;

		$filters = array();
		$filters["PID"] = $args["pid"];

		$offset = $page_num * $max_rows;
		$sortColumns = array('date','source','refno','name','account','amount');
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

		$filters["OFFSET"] = $offset;
		$filters["MAXROWS"] = $max_rows;
		$filters["SORTSQL"] = $sort_sql;

		$result=$cc->getGrants($filters);
		if($result) {
			$found_rows = $cc->FoundRows();
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
					'PH'=>'Meds/Supp',
					'OP'=>'Operating room',
				);

				while($row = $result->FetchRow()) {
					$DATA[$i]['entry_id'] = $row['entry_id'];
					$DATA[$i]['source'] = $row['source'];
					$DATA[$i]['refno'] = $row['refno'];
					$DATA[$i]['date'] = nl2br(date("Y-m-d\nh:ia", strtotime($row['date'])));
					$DATA[$i]['itemno'] = $row['code'];
					$DATA[$i]['name'] = $row['name'];
					$DATA[$i]['qty'] = $row['quantity'];
					$DATA[$i]['total'] = $row['amount'];
					$DATA[$i]['discounted'] = $row['amount'];
					$DATA[$i]['account'] = $row['account'];
					$DATA[$i]['status'] = '';
					$DATA[$i]['FLAG'] = 1;
					$i++;
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
		}

		$objResponse->script('this.fetchDone()');
		return $objResponse;
	}

	function cancelEntry($src, $id) {
		global $db;
		$objResponse = new xajaxResponse();
		$cc = new SegCMAPPatient();
		$db->StartTrans();
		if ($cc->cancelEntry($src, $id)) {
			$objResponse->call('prepareDelete',$src.$id);
			$objResponse->call('alertSeg', 'Entry successfully saved!', 'info');
			$objResponse->script('xajax.call("updateBalance", { parameters: [$("pid").value] })');
		}
		else {
			$db->FailTrans();
			$objResponse->call('cancelDelete',$src.$id);
			$objResponse->call('alertSeg', $errorReporter->report(), 'alert',
				array(
					'id'=>'bOk',
					'label'=>'Okay!',
					'icon'=>'../../gui/img/common/default/tick.png',
					'callback'=>'doneLoading'
				)
			);
		}
		$db->CompleteTrans();
		return $objResponse;
	}

	/**
	* AJAX processor for populating Referrals list for the CMAP Entry interface.
	* called internally from ListGen.reload
	* 
	* @param mixed $page_num
	* @param mixed $max_rows
	* @param mixed $sort_obj
	* @param mixed $args
	* @return xajaxResponse
	*/
	function populateFundsTransfers($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
		global $config;
		
		$objResponse = new xajaxResponse();
		$pc = new SegCMAPPatient();

		$filters = array();
		$filters["PID"] = $args["pid"];

		$offset = $page_num * $max_rows;
		$sortColumns = array('referral_date','account_name','referral_amount','current_balance','create_id');
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
		$filters['ENTRYTYPE'] = 'transfer';

		$result=$pc->getReferrals($filters);
		if($result) {
			$found_rows = $pc->FoundRows();
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
					$DATA[$i]['id'] = $row['id'];
					$DATA[$i]['nr'] = $row['control_nr'];
					$DATA[$i]['date'] = nl2br(date("Y-m-d\nh:ia",strtotime($row['referral_date'])));
					$DATA[$i]['account_id'] = $row['cmap_account'];
					$DATA[$i]['account_name'] = $row['account_name'];
					$DATA[$i]['amount'] = $row['referral_amount'];
					$DATA[$i]['balance'] = $row['current_balance'];
					$DATA[$i]['encoder'] = $row['create_id'];
					$DATA[$i]['remarks'] = $row['remarks'];
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
					$objResponse->alert("SQL empty result:\n".$pc->sql);
			}

		} else {
			// error
				if ($config['debug'])
					$objResponse->alert("SQL error:\n".$pc->sql);
				else
					$objResponse->alert("A database error has occurred. Please contact your system administrator...");

			$objResponse->contextAssign('dataSize', -1);
			$objResponse->contextAssign('listData', NULL);
		}

		$objResponse->script('this.fetchDone()');
		return $objResponse;
	}

//	function populateCMAPEntries($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
//		global $config;
//		
//		$objResponse = new xajaxResponse();
//		$lc = new SegCMAP();
//		$filters = array();

//		$offset = $page_num * $max_rows;
//		$sortColumns = array('entry_date','control_nr','name','item','total','encoder');
//		$sort = array();
//		if (is_array($sort_obj)) {
//			foreach ($sort_obj as $i=>$v) {
//				$col = $sortColumns[$i] ? $sortColumns[$i] : "entry_date";
//				if ((int)$v < 0) $sort[] = "$col DESC";
//				elseif ((int)$v > 0) $sort[] = "$col ASC";
//			}
//		}
//		if ($sort) $sort_sql = implode(',', $sort);
//		else $sort_sql = 'entry_date DESC';

//		$filters["OFFSET"] = $offset;
//		$filters["ROWCOUNT"] = $max_rows;
//		$filters["SORTSQL"] = $sort_sql;

//		$result=$lc->getAllEntries($filters);

//		if($result) {
//			$found_rows = $lc->FoundRows();
//			$last_page = ceil($found_rows/$max_rows)-1;
//			if ($page_num > $last_page) $page_num=$last_page;

//			if($data_size=$result->RecordCount()) {
//				$temp=0;
//				$i=0;
//				$objResponse->contextAssign('currentPage', $page_num);
//				$objResponse->contextAssign('lastPage', $last_page);
//				$objResponse->contextAssign('maxRows', $max_rows);
//				$objResponse->contextAssign('listSize', $found_rows);

//				$DATA = array();
//				while($row = $result->FetchRow()) {

//					$DATA[$i]['date'] = $row['entry_date'];
//					$DATA[$i]['nr'] = $row['control_nr'];
//					$DATA[$i]['name'] = $row['name'];
//					$DATA[$i]['items'] = $row['items'];
//					$DATA[$i]['total'] = $row['total'];
//					$DATA[$i]['encoder'] = $row['create_id'];
//					$DATA[$i]['status'] = "";
//					$DATA[$i]['FLAG'] = 1;
//					$i++;
//				} //end while

//				$objResponse->contextAssign('dataSize', $data_size);
//				$objResponse->contextAssign('listData', $DATA);
//			}
//			else {
//				$objResponse->contextAssign('dataSize', 0);
//				$objResponse->contextAssign('listData', NULL);

//				if ($config['debug'])
//					$objResponse->alert("SQL empty result:\n".$lc->sql);
//			}

//		} else {
			// error
//				if ($config['debug'])
//					$objResponse->alert("SQL error:\n".$lc->sql);
//				else
//					$objResponse->alert("A database error has occurred. Please contact your system administrator...");

//			$objResponse->contextAssign('dataSize', -1);
//			$objResponse->contextAssign('listData', NULL);
//		}

//		$objResponse->script('this.fetchDone()');
//		return $objResponse;
//	}


	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');

	require_once($root_path.'include/care_api_classes/sponsor/class_cmap.php');
	require_once($root_path.'include/care_api_classes/sponsor/class_cmap_patient.php');
	require_once($root_path.'modules/sponsor/ajax/cmap-patient.common.php');
	$xajax->processRequest();
