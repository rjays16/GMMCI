<?php
  
  function save( $data ) {
    global $db, $debug_env;
    $objResponse = new xajaxResponse();
    $lc = new SegCMAPPatient();
    
    $db->StartTrans();
    if ($data['adjtype'] == 'add') {
    	$bal = $lc->getBalance($data['pid']);
    	$data['amount'] = (float)$bal + (float)$data['amount'];
    	if ($data['amount'] < 0) $data['amount']=0;
		}
    
    $data['entry_type'] = 'adjustment';
    $data['history'] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
    $data['modify_id'] = $_SESSION['sess_temp_userid'];
    $data['modify_time'] = date('YmdHis');
    $data['create_id']=$_SESSION['sess_temp_userid'];
    $data['create_time']=date('YmdHis');
    $lc->setDataArray($data);
    $saveok=$lc->insertDataFromInternalArray();

    if ($saveok) {
      $saveok = $lc->updateBalance($data['pid'], $data['entry_type'], $data['amount']);
		}
      
    if ($saveok) {
    	$db->CompleteTrans();
    	
      $objResponse->call('parent.xajax_updateBalance', $data['pid']);
      $objResponse->call('parent.alst.reload');
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

 	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
  
  require($root_path.'include/care_api_classes/sponsor/class_cmap_patient.php');
	require_once($root_path.'modules/sponsor/ajax/cmap_adjustment_edit.common.php');
	$xajax->processRequest();
