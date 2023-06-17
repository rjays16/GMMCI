<?php

	function save( $data ) {
		global $db, $debug_env;
		$objResponse = new xajaxResponse();
		$pc = new SegCmapPatient();
		$ac = new SegCmapAccount();

		$db->StartTrans();
		$data['entry_type'] = 'transfer';
		$data['account'] = 'transfer';
		$data['history'] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
		$data['modify_id'] = $_SESSION['sess_temp_userid'];
		$data['modify_time'] = date('YmdHis');
		$data['create_id']=$_SESSION['sess_temp_userid'];
		$data['create_time']=date('YmdHis');
		$pc->setDataArray($data);
		$saveok=$pc->insertDataFromInternalArray();

		if ($saveok) {
			$data_acct = array(
				'control_nr'		=> $data['control_nr'],
				'entry_date'		=> $data['entry_date'],
				'entry_type'		=> 'transfer',
				'account_nr'		=> $data['associated_id'],
				'associated_id'	=> $data['pid'],
				'amount'				=> $data['amount'],
				'remarks'				=> $data['remarks'],
				'history'				=> $data['history'],
				'create_id'			=> $data['create_id'],
				'create_time'		=> $data['create_time'],
				'modify_id'			=> $data['modify_id'],
				'modify_time'		=> $data['modify_time']
			);
			$ac->setDataArray($data_acct);
			$saveok=$ac->insertDataFromInternalArray();
		}

		if ($saveok) {
			$saveok=$pc->updateBalance($data['associated_id'], $data['pid'], $data['entry_type'], $data['amount']);
		}

		if ($saveok) {
			$saveok=$ac->updateBalance($data_acct['account_nr'], $data_acct['entry_type'], $data_acct['amount']);
		}

		if ($saveok) {
			$db->CompleteTrans();
			$objResponse->call('parent.xajax_updateBalance', $data['pid']);
			$objResponse->call('parent.flst.reload');
			$objResponse->alert('Entry successfully saved...');
			$objResponse->call('parent.cClick');

		}
		else {
			$db->FailTrans();
			$db->CompleteTrans();
			$objResponse->alert('Error saving entry...');
			$objResponse->call('doneLoading');
		}


		return $objResponse;
	}

	function getFund($id) {
		global $db;
		$objResponse = new xajaxResponse();

		$ac = new SegCMAPAccount();
		$fund = $ac->getBalance($id);

		$objResponse->assign("show_fund","value", number_format($fund,2));
		$objResponse->assign("fund","value", $fund);

		return $objResponse;
	}

	//added by cha, june 7, 2010
	function update($data)	{
		global $db;
		$objResponse = new xajaxResponse();
		$db->StartTrans();
		//start update cmap ledger main
		$sql = "";
		return $objResponse;
	}
	//end cha

	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');

	require_once($root_path.'include/care_api_classes/sponsor/class_cmap_patient.php');
	require_once($root_path.'include/care_api_classes/sponsor/class_cmap_account.php');
	require_once($root_path.'modules/sponsor/ajax/cmap-patient-transfer.common.php');
	$xajax->processRequest();
