<?php

function save( $form ) {
	global $db, $errorReporter;
	$objResponse = new xajaxResponse();
//	$bc = new SegBillingGrant;
//	$bc->useLingap();

//	$objResponse->call('doneLoading');
//	$objResponse->alert(print_r($form, TRUE));
//	return $objResponse;

	/* Map form data to row fields */
	$data = array(
		'id' 				 	=> $form['eid'],
		'control_nr' 	=> $form['control_nr'],
		'is_advance' 	=> $form['is_advance'],
		'entry_date' 	=> $form['entry_date'],
		'remarks' 	 	=> $form['remarks'],
	);

	$form_errors = array();

	# validate data
	# Missing control number
	if (!$data['control_nr']) {
		$form_valid = false;
		$objResponse->call('flagInput', 'control_nr');
		$form_errors[] = 'Enter the Control Number for this entry...';
	}

	# Invalid grant amount
	if ((float)$form['grant'] <= 0.0) {
		$objResponse->call('flagInput', 'grant');
		$form_errors[] = 'Enter a valid grant amount...';
	}

	# Missing entry date
	if (!$data['entry_date']) {
		$data['entry_date']=date('YmdHis');
	}

	# Missing bill number
	if (!$form['bill_nr']) {
		$objResponse->call('flagInput', 'bill_nr');
		$form_errors[] = 'No billing number provided...';
	} else {
		$sql = "SELECT e.pid, b.encounter_nr,\n".
				"fn_get_complete_address(e.pid) AS `address`,\n".
				"fn_get_person_lastname_first(e.pid) AS `name`\n".
			"FROM seg_billing_encounter b\n".
			"INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr\n".
			"WHERE b.bill_nr=".$form['bill_nr'];
		if (($bill_info = $db->GetRow($sql))!==FALSE) {
			$data['pid'] = $bill_info['pid'];
			$data['encounter_nr'] = $bill_info['encounter_nr'];
			$data['name'] = $bill_info['name'];
			$data['address'] = $bill_info['address'];
		}
		else {
			$objResponse->alert($sql);
			$objResponse->call('flagInput', 'bill_nr');
			$form_errors[] = 'Invalid billing number...';
		}
	}
	if ($form_errors) {
		$objResponse->alert(
			"Errors found:".
			implode("\n* ", $form_errors)
		);
		return $objResponse;
	}

//	$data['modify_id'] = $_SESSION['sess_temp_userid'];
//	$data['modify_time'] = date('YmdHis');
	$errorMessage = 'Unable to update Lingap entry table...';

	$db->StartTrans();
	/* Edit mode */
//	if ($data['id']) {
//		$data['history'] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
//		$data['history'] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
//		$data["history"]=$bc->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
//		$bc->setDataArray($data);
//		$bc->where = "id=".$db->qstr($data['entry_id']);
//		$saveok = $bc->updateDataFromInternalArray($data['entry_id'],FALSE);
//	}
//	else {
//		$data['id'] = create_guid();
//		$data['history'] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
//		$data['create_id']=$_SESSION['sess_temp_userid'];
//		$data['create_time']=date('YmdHis');
//		$bc->setDataArray($data);
//		$saveok=$bc->insertDataFromInternalArray();
//	}
	$referral = new SegLingapReferral();

	$insert = true;
	if (!$data['id']) {
		$data['id'] = create_guid();
		$insert = false;
	}
	$saveok = $referral->save($data);


	if ($saveok) {
		// grant
		$referral = new SegLingapReferral($data['id']);
		$lingapGrantor = new SegLingapGrantor($referral);
		$request = new SegRequest(SegRequest::BILLING_REQUEST, array(
			'refNo' => $form['bill_nr']
		));

		$saveok = $lingapGrantor->ungrant($request);
		if ($saveok) {
			$saveok = $lingapGrantor->grant($request, $form['grant']);
		}
	}

	$objResponse->call('doneLoading');

	if ($saveok) {
		$db->CompleteTrans();
		$objResponse->alert('Referral successfully saved!');
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		$objResponse->alert('Unable to save referral. Please contact your system administrator!');
	}

	return $objResponse;
}

function loadGrantDetails( $id, $nr ) {
	global $db;
	$objResponse = new xajaxResponse();
	$saveok = true;

	# validate if correct billing number is submitted
	if (!$nr) {
		$objResponse->alert("Invalid billing number encountered...");
		$saveok=false;
	}
	else {
		$sql =
			"SELECT fn_get_person_lastname_first(e.pid) AS `name`,\n".
				"e.pid,\n".
				"fn_compute_bill(b.bill_nr) AS `due`,\n".
				"fn_compute_bill_grants(b.bill_nr) AS `grant`\n".
				"FROM seg_billing_encounter b\n".
					"INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr\n".
				"WHERE b.bill_nr=".$db->qstr($nr);
		if ($bill_info=$db->GetRow($sql)) {
			$due = (float) $bill_info['due'];
			$grant = (float) $bill_info['grant'];
			$due = $due - $grant;
		}
		else {
			$objResponse->alert($sql);
		}
	}

	if ($id) {
		$sql =
			"SELECT e.id,e.control_nr,e.entry_date,e.remarks,e.is_advance,\n".
				"d.ref_no,d.amount\n".
				"FROM seg_lingap_entries e\n".
				"INNER JOIN seg_lingap_entries_bill d ON d.entry_id=e.id\n".
				"WHERE e.id=".$db->qstr($id);
	}
	if ($id && $grant_info=$db->GetRow($sql)) {
		# Existing grant
		$due += (float) $grant_info['amount'];
		$ts = strtotime($grant_info['entry_date']);
	}
	else {
		# New grant, load default values
		$grant_info = array(
			'control_nr' => '',
			'remarks' => '',
			'is_advance' => false,
			'amount' => 0.0
		);
		$objResponse->assign('select-grant', 'value', '' );
		if ($id) {
		}
	}

	# Update controls
	$objResponse->assign('pid', 'value', $bill_info['pid'] );
	$objResponse->assign('name', 'value', $bill_info['name'] );
	$objResponse->assign('remarks', 'value', $grant_info['remarks'] );
	$objResponse->assign('control_nr', 'value', $grant_info['control_nr']);
	$objResponse->assign('is_advance', 'checked', $grant_info['is_advance']=='1'?true:false  );
	if (!$ts)
		$ts = time();
	$objResponse->assign('entry_date', 'value', date("Y-m-d H:s", $ts) );
	$objResponse->assign('partial','disabled',$due > 0 ? false: true);
	$objResponse->assign('full','disabled',$due > 0 ? false: true);
	$objResponse->assign('due', 'value', $due );
	$objResponse->assign('due_view', 'value', number_format($due,2) );
	$objResponse->assign('grant', 'value', $grant_info['amount'] );
	$objResponse->assign('grant_view', 'value', number_format( $grant_info['amount'],2) );

	$objResponse->assign('form_save','disabled', !$saveok ? true: false);

	$objResponse->call('doneLoading');

	return $objResponse;
}

require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/sponsor/class_lingap.php';
require_once $root_path.'include/care_api_classes/sponsor/class_billing_grant.php';

require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
require_once $root_path.'include/care_api_classes/sponsor/class_lingap_referral.php';
require_once $root_path.'include/care_api_classes/sponsor/grantors/class_lingap_grantor.php';

require_once $root_path.'modules/sponsor/ajax/lingap_billing_request.common.php';
$xajax->processRequest();
