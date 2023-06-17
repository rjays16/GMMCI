<?php

/**
* handler for AJAX function: save
*
* @param string $data the referral data to be saved
* @return xajaxResponse xajax response object
*/
function save( $pid, $data ) {
	global $db, $config;
	$objResponse = new xajaxResponse();
	$referral = new SegCmapReferral();

	// save referral data
	$referral_data = array(
		'referral_date' => $data['referral_date'],
		'control_nr' => $data['control_nr'],
		'referral_nr' => $data['referral_nr'],
		'cmap_account' => $data['cmap_account'],
		'referral_amount' => $data['amount'],
		'current_balance' => $data['amount'],
		'remarks' => $data['remarks']
	);


	if ( $pid[0] == 'W' )
		$referral_data['walkin_pid'] = substr($pid,1);
	else
		$referral_data['pid'] = $pid;

	if ($data['id']) {
		$referral_data['id'] = $data['id'];
	}
	else {
		$referral_data['id'] = create_guid();
	}

	$db->StartTrans();
	$saveok = $referral->save($referral_data);

	if ($saveok) {
		$db->CompleteTrans();
		$objResponse->call('parent.xajax_updateBalance', $data['pid']);
		$objResponse->call('parent.flst.reload');
		$objResponse->alert('Referral successfully saved...');
		$objResponse->call('parent.cClick');

	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		$objResponse->alert('Error saving referral entry! Please contact your Administrator!');
		$objResponse->call('doneLoading');
	}

	return $objResponse;
}

function getFund($id) {
	global $db;
	$objResponse = new xajaxResponse();

	$ac = new SegCmapAccount($id);
	$fund = $ac->getReferredBalance();

	if ($fund === false) {
		$objResponse->assign("show_fund","value", "0.00");
		$objResponse->assign("fund","value", 0);
		$objResponse->alert('Cannot retrieve account balance...');
	}
	else {
		$objResponse->assign("show_fund","value", number_format($fund,2));
		$objResponse->assign("fund","value", $fund);
	}
	return $objResponse;
}

function checkExistingCmapNo($cmap_no) {
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "SELECT control_nr FROM seg_cmap_referrals WHERE control_nr=".$db->qstr($cmap_no);
	$result = $db->GetOne($sql);
	if($result!==FALSE) {
		$objResponse->alert("This CMAP control no. is already existing!");
		$objResponse->call("document.getElementById('control_nr').focus");
	}
	return $objResponse;
}

function checkExistingReferralNo($account, $referral_no) {
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "SELECT referral_nr FROM seg_cmap_referrals WHERE referral_nr=".$db->qstr($referral_no)."\n".
							" AND cmap_account=".$db->qstr($account);
	$result = $db->GetOne($sql);
	if($result!==FALSE) {
		$objResponse->alert("The referral control no. is already existing for this account!");
		$objResponse->call("document.getElementById('referral_nr').focus");
	}
	return $objResponse;
}

require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_referral.php');
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_patient.php');
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_account.php');
require_once($root_path.'modules/sponsor/ajax/cmap_patient_transfer.common.php');
$xajax->processRequest();