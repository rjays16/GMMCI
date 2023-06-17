<?php
	function updateBalance($pid) {
		global $db, $errorReporter;
		$objResponse = new xajaxResponse();
		$pc = new SegCmapPatient($pid);
		$bal = $pc->getBalance();
		$objResponse->assign('bal','value',number_format($bal,2));
		return $objResponse;
	}



//
//	function populatePatientRequestList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
//		global $errorReporter;
//		$objResponse = new xajaxResponse();
//		$cc = new SegCmapPatient;

//		$filters = array();
//		$filters["PID"] = $args["pid"];

//		$offset = $page_num * $max_rows;
//		$sortColumns = array('date','source','refno','name','account','amount');
//		$sort = array();
//		if (is_array($sort_obj)) {
//			foreach ($sort_obj as $i=>$v) {
//				 if ($col = ($sortColumns[$i] ? $sortColumns[$i] : FALSE)) {
//					 if ((int)$v < 0) $sort[] = "$col DESC";
//					 elseif ((int)$v > 0) $sort[] = "$col ASC";
//				 }
//			}
//		}
//		if ($sort) $sort_sql = implode(',', $sort);
//		else $sort_sql = 'source ASC';

//		$filters["OFFSET"] = $offset;
//		$filters["MAXROWS"] = $max_rows;
//		$filters["SORTSQL"] = $sort_sql;

//		$result=$cc->getGrants($filters);
//		if($result) {
//			$found_rows = $cc->FoundRows();
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

//				$source_text = array(
//					'FB'=>'Hospital bill',
//					'LD'=>'Laboratory',
//					'RD'=>'Radiology',
//					'PH'=>'Meds/Supp',
//					'OP'=>'Operating room',
//				);

//				while($row = $result->FetchRow()) {
//					$DATA[$i]['entry_id'] = $row['entry_id'];
//					$DATA[$i]['source'] = $row['source'];
//					$DATA[$i]['refno'] = $row['refno'];
//					$DATA[$i]['date'] = nl2br(date("Y-m-d\nh:ia", strtotime($row['date'])));
//					$DATA[$i]['itemno'] = $row['code'];
//					$DATA[$i]['name'] = $row['name'];
//					$DATA[$i]['qty'] = $row['quantity'];
//					$DATA[$i]['total'] = $row['amount'];
//					$DATA[$i]['discounted'] = $row['amount'];
//					$DATA[$i]['account'] = $row['account'];
//					$DATA[$i]['status'] = '';
//					$DATA[$i]['FLAG'] = 1;
//					$i++;
//				} //end while

//				$objResponse->contextAssign('dataSize', $data_size);
//				$objResponse->contextAssign('listData', $DATA);
//			}
//			else {
				// empty record
//				$objResponse->contextAssign('dataSize', 0);
//				$objResponse->contextAssign('listData', NULL);
//			}
//		} else {
			// error

//			$objResponse->contextAssign('dataSize', -1);
//			$objResponse->contextAssign('listData', NULL);
//		}

//		$objResponse->script('this.fetchDone()');
//		return $objResponse;
//	}



	/**
	* put your comment there...
	*
	* @param mixed $src
	* @param mixed $id
	* @return xajaxResponse
	*/
	function cancelEntry($src, $id) {
		global $db;
		$objResponse = new xajaxResponse();
		$cc = new SegCMAPPatient();
		$db->StartTrans();
		if ($cc->cancelEntry($src, $id)) {
			$objResponse->call('prepareDelete',$src.$id);
			$objResponse->alert('Entry successfully saved!');
			$objResponse->script('xajax.call("updateBalance", { parameters: [$("pid").value] })');
		}
		else {
			$db->FailTrans();
			$objResponse->call('cancelDelete',$src.$id);
			$objResponse->alert("A database error has occurred! Please contact your Administrator!");
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
		$referral = new SegCmapReferral();

		$filters = array();
		$filters["PID"] = $args["pid"];

		$offset = $page_num * $max_rows;
		$sortColumns = array('referral_date', 'control_nr', 'referral_nr', 'account_name','referral_amount','current_balance','create_id');
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
		else $sort_sql = 'referral_date ASC';

		$filters['OFFSET'] = $offset;
		$filters['MAXROWS'] = $max_rows;
		$filters['SORTSQL'] = $sort_sql;
		$filters['ENTRYTYPE'] = 'transfer';

		$result=$referral->getReferrals($filters);
		if($result) {
			$found_rows = $referral->FoundRows();
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
					$DATA[$i]['refnr'] = $row['referral_nr'];
					$DATA[$i]['date'] = nl2br(date("Y-m-d\nh:ia",strtotime($row['referral_date'])));
					$DATA[$i]['account_id'] = $row['cmap_account'];
					$DATA[$i]['account_name'] = $row['account_name'];
					$DATA[$i]['amount'] = $row['referral_amount'];
					$DATA[$i]['balance'] = $row['current_balance'];
					$DATA[$i]['encoder'] = $row['create_name'];
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
				// empty!
			}

		} else {
			// error
			if ($config['debug'])
				$objResponse->alert("SQL error:\n".$referral->sql);
			else
				$objResponse->alert("Ooops! A database error has occurred. Please contact your system administrator...");

			$objResponse->contextAssign('dataSize', -1);
			$objResponse->contextAssign('listData', NULL);
		}

		$objResponse->script('this.fetchDone()');
		return $objResponse;
	}


	function deleteReferral($id)
	{
		global $db, $config;
		$objResponse = new xajaxResponse();

		$db->StartTrans();
		$referral = new SegCmapReferral($id);
		if ($referral->deleteReferral())
		{
			$objResponse->alert("Referral successfully deleted...");
			$objResponse->script("flst.reload()");
			$objResponse->call("showBalance");
		}
		else
		{
			$db->FailTrans();
			if ($config['debug'])
				$objResponse->alert("Error deleting referral:".$referral->getErrorMsg()."\nQuery:".$referral->getQuery());
			else
				$objResponse->alert("Unable to delete referral entry...");
		}
		$db->CompleteTrans();
		return $objResponse;
	}


	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');

	require_once($root_path.'include/care_api_classes/sponsor/class_cmap.php');
	require_once($root_path.'include/care_api_classes/sponsor/class_cmap_referral.php');
	require_once($root_path.'include/care_api_classes/sponsor/class_cmap_patient.php');
	require_once($root_path.'modules/sponsor/ajax/cmap_patient.common.php');
	$xajax->processRequest();
