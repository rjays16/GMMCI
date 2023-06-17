<?php

function save( $data, $Saro, $Nca ) {
	global $db, $debug_env;
	$objResponse = new xajaxResponse();
	$allotment = new SegCmapAllotment();
	
	$db->StartTrans();    

	if (is_array($data)) {
		if (!$data['id']) {
			$data['id'] = create_guid();
		}
		$allotmentData['id'] = $data['id'];
		$allotmentData['allotment_date'] = date('Y-m-d',strtotime($data['allotment_date'])); 
		$allotmentData['cmap_account'] = $data['cmap_account'];
		$allotmentData['amount'] = $data['amount'];
		$allotmentData['amount_word'] = $data['amount_in_words'];
		$allotmentData['remarks'] = $data['remarks'];
		$saveok = $allotment->save($allotmentData);
		if ($saveok) {
			$entryId = $allotmentData['id'];
		}
		else {
			$errorMessage = $allotment->getErrorMsg();
		}
	}
	else {
		$saveok = TRUE;
		$entryId = $data;
	}

	if ($saveok) {
		$saroClass = new SegCmapSaro();
		if ($Saro) {
			$Saro['id'] = $entryId;
			$Saro['saro_date'] = date('Y-m-d', strtotime($Saro['saro_date']) );
			if (($saveok = $saroClass->save($Saro))===false) {
				$errorMessage = 'Unable to update SARO details...';
			}
		}
		else {
			$Saro = Array( 'id'=>$entryId );
			if (($saveok = $saroClass->delete($Saro)) === false) {
				$errorMessage = 'Unable to delete SARO details...';
			}
		}
	}

	if ($saveok) {
		$ncaClass = new SegCmapNca();
		if ($Nca) {
			$Nca['id'] = $entryId;
			$Nca['nca_date'] = date('Y-m-d', strtotime($Nca['nca_date']) );
			if (($saveok = $ncaClass->save($Nca))===false) {
				$errorMessage = 'Unable to update NCA details...';
			}
		}
		else {
			$Nca = Array( 'id'=>$entryId );
			if (($saveok = $ncaClass->delete($Nca))===false) {
				$errorMessage = 'Unable to delete NCA details';
			}
		}
	}

	if ($saveok) {
		$db->CompleteTrans();
		$objResponse->call('parent.xajax_updateBalance', $data['cmap_account']);
		$objResponse->call('parent.alst.reload');
		$objResponse->alert('Allotment successfully saved...');
		$objResponse->call('parent.cClick');
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		if ($errorMessage) {
			$objResponse->alert('Error saving entry! '.$errorMessage);
		}
		else {
			$objResponse->alert('Oops! An error has occurred. Please contact your system administrator!');
		}
		$objResponse->call('doneLoading');
	}
	
	
	return $objResponse;
}


function delete() {

}

require('./roots.php');
require($root_path.'include/inc_environment_global.php');

require($root_path.'include/care_api_classes/sponsor/class_cmap_allotment.php');
require($root_path.'include/care_api_classes/sponsor/class_cmap_saro.php');
require($root_path.'include/care_api_classes/sponsor/class_cmap_nca.php');
require_once($root_path.'modules/sponsor/ajax/cmap_allotment.common.php');
$xajax->processRequest();
