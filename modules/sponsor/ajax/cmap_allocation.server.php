<?php
  
  function save( $data ) {
    global $db, $debug_env;
    $objResponse = new xajaxResponse();
    $lc = new SegCMAPAccount();
    
    $db->StartTrans();    
    $data['entry_type'] = 'allocation';
    $data['history'] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
    $data['modify_id'] = $_SESSION['sess_temp_userid'];
    $data['modify_time'] = date('YmdHis');
    $data['create_id']=$_SESSION['sess_temp_userid'];
    $data['create_time']=date('YmdHis');
    $lc->setDataArray($data);
    $saveok=$lc->insertDataFromInternalArray();
    $errorMessage = 'Unable to update account ledger...';

    if ($saveok) {
    	$errorMessage = 'Unable to update account balance...';
      $saveok = $lc->updateBalance($data['account_nr'], $data['entry_type'], $data['amount']);
		}
      
    if ($saveok) {
    	$db->CompleteTrans();
    	
      $objResponse->call('parent.xajax_updateBalance', $data['account_nr']);
      $objResponse->call('parent.alst.reload');
      $objResponse->alert('Entry successfully saved...');
      $objResponse->call('parent.cClick');
    }
    else {
    	$msg = $lc->sql;
    	
      $db->FailTrans();
    	$db->CompleteTrans();
    	
      $objResponse->alert('Error saving entry...'.$errorMessage);
      $objResponse->call('doneLoading');
    }
    
    
    return $objResponse;
  }

 	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
  
  require($root_path.'include/care_api_classes/sponsor/class_cmap_account.php');
	require_once($root_path.'modules/sponsor/ajax/cmap_allocation.common.php');
	$xajax->processRequest();
