<?php
	
function save( $data ) {
	global $db, $debug_env;
	$objResponse = new xajaxResponse();
	$lc = new SegLingapPatient();
	
	$data['entry_type'] = 'transfer';
	$data['history'] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
	$data['modify_id'] = $_SESSION['sess_temp_userid'];
	$data['modify_time'] = date('YmdHis');
	$data['create_id']=$_SESSION['sess_temp_userid'];
	$data['create_time']=date('YmdHis');
	$lc->setDataArray($data);
	$saveok=$lc->insertDataFromInternalArray();
	
	if ($saveok) {
		$lc->updateBalance($data['pid'], $data['entry_type'], $data['amount']);      
		$objResponse->call('parent.xajax_updateBalance', $data['pid']);
		$objResponse->call('parent.flst.reload');
		$objResponse->alert('Entry successfully saved...');
		$objResponse->call('parent.cClick');
	}
	else {
		$objResponse->alert('Error saving entry...');
		$objResponse->call('doneLoading');
	}
	
	
	return $objResponse;
}

require('./roots.php');
require($root_path.'include/inc_environment_global.php');

require($root_path.'include/care_api_classes/sponsor/class_lingap_patient.php');
require_once($root_path.'modules/sponsor/ajax/lingap-patient-transfer.common.php');
$xajax->processRequest();
