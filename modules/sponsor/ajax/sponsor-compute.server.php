<?php
  define('__DEBUG_MODE',1);

  function autoCompute($nr, $grants, $amounts, $is_paid) {
    global $db;
    $objResponse = new xajaxResponse();
    $bc = new SegBillingGrant();
    
    
    # User entered grant amounts
    $account_entries = array();
    foreach ($grants as $i=>$g) {
      //$user_amounts[$g] = (float)$amounts[$i];
      $account_entries[] = array(
        'guarantor' => $g,
        'amount' => ($amounts[$i]==-1) ? 999999999 : (double) $amounts[$i],
        'paid' => $is_paid[$i]        
      );
    }

    # Get per billing area actual amounts, as a whole
//    $bc = new SegBillingGrant();
//    $result=$bc->getBillAreaTotals($nr);
//    $bill_areas = array('srv','med','ops','doc','msc','acc');
//    $area_totals = array();
//    if ($result) {
//      foreach ($bill_areas as $barea) {
//        $area_totals[strtoupper($barea)] = (float) $result[$barea.'_total'];
//      }
//    }
    
    # Get per billing area amounts, classified acdg. to cashier account type
//    $result = $bc->getAllBillingAccounts($nr);
//    $billing_accounts = array();
//    if ($result) {
//      $grant_counter = 0;
//      while ($row=$result->FetchRow()) $billing_accounts[] = $row;
//    }
//    else {
//      if (defined('__DEBUG_MODE'))
//        $objResponse->alert($bc->sql);
//      else
//        $objResponse->alert("A database error has occurred. Please contact your system administrator...");
//    }
    
    
    # Now calculate!
//    reset($user_amounts);
//    
//    $bulk = array();
//    foreach ($area_totals as $i=>$total) { 
//      if ($total && current($user_amounts)!==FALSE) { // ignore billing areas with totals amounting to 0
//        $currentTotal = $total;
//        $result = $bc->getBillingAccounts($nr, $i, 0, 255, 't.priority DESC');
//        
//        if ($result) {
//          
//          while ($currentTotal > 0) {
//            $row = $result->FetchRow();
//            $item_amount = (float) $row['total'];
//            
//            if ($currentTotal < $item_amount) {
//              $item_amount = $currentTotal;
//              $currentTotal = 0;
//            }
//            else {
//              $currentTotal -= $item_amount;
//            }
//            
//            while ($item_amount>0) {
//              if (current($user_amounts)<0 || current($user_amounts)>=$item_amount)  { # Handles all excesses                
//                $grant_amount = $item_amount;
//                $item_amount=0;
//              }
//              else {
//                $grant_amount=current($user_amounts);
//                $item_amount-=current($user_amounts);
//              }
//              $bulk[] = array($i, $row['account_type'], key($user_amounts), $grant_amount);
//              $user_amounts[key($user_amounts)] -= $grant_amount;
//              
//              if (current($user_amounts)==0) next($user_amounts);
//              if (current($user_amounts) === FALSE) {
//                $item_amount=0;
//                $current_total=0;
//              }
//            }
//          }
//        }
//        else {
//          
//        }
//      }
//    }

    $result = $bc->getAllBillingAccounts($nr);

    $breakdown = array();
    while ($row=$result->FetchRow()) {
      $breakdown[$row['area'].$row['account_type']] = array(
        'area' => $row['area'],
        'account' => $row['account_type'],
        'amount' => (double) $row['amount']
      );
    }

    // account for paid items first
    /*
    foreach ( $account_entries as $i=>$entry ) {
      if ($entry['paid']) {
        $result = $bc->getGrantEntryDetailsByIndex($nr, 'FB', $entry['guarantor']);
        while ($row=$result->FetchRow()) {
          $breakdown[$row['bill_area'].$row['service_code']]['amount'] -= $row['amount'];
        }
      }
    }
    */

    $bulk = array();
    foreach ( $account_entries as $i=>$entry ) {
      if (!$entry['paid']) {
        foreach ( $breakdown as $j=>$bd ) {
          if ($account_entries[$i]['amount'] <= 0.0) break;
          
          if ($breakdown[$j]['amount'] > 0.0) {
            if ($breakdown[$j]['amount'] > $account_entries[$i]['amount']) {
              $breakdown[$j]['amount'] -= $account_entries[$i]['amount'];
              $amount = $account_entries[$i]['amount'];
              $account_entries[$i]['amount'] = 0.0;
            }
            else {
              $account_entries[$i]['amount'] -= $breakdown[$j]['amount'];
              $amount = $breakdown[$j]['amount'];
              $breakdown[$j]['amount'] = 0.0;
            }
            $bulk[] = array('index'=>$i, 'area'=>$bd['area'], 'account_type'=>$bd['account'], 'guarantor'=>$entry['guarantor'], 'amount'=>$amount);
          }
          else {
            // negative breakdown amount
          }
        }
      }
    }

//    $objResponse->alert(print_r($bulk,TRUE));
//    $objResponse->script('doneLoading()');
//    return $objResponse;
    
    $db->StartTrans();
    $sql = "DELETE FROM seg_grant_entries 
WHERE ref_source='FB'
AND ref_no=".$db->qstr($nr);
    $ok=$db->Execute($sql);

    if ($ok) {
      $indices = array();
      foreach ($bulk as $i=>$v)
        $indices[] = $v['index'];
      $indices = array_unique($indices);      

      $insert_ids = array();
      foreach ($indices as $v) {
        $sql = "INSERT INTO seg_grant_entries(ref_no, ref_source, account_id, create_id, create_time)
VALUES(".$db->qstr($nr).",'FB',".$db->qstr($account_entries[$v]['guarantor']).",".$db->qstr($_SESSION['sess_temp_user']).",NOW())";
        $ok = $db->Execute($sql);
        if (!$ok) break;
        else $insert_ids[$v] = $db->Insert_ID();
      }
    }

    if ($ok) {
      foreach ($bulk as $v) {
        if ($grant_no=$insert_ids[$v['index']]) {
          $sql = "INSERT INTO seg_grant_entry_details(grant_no, bill_area, service_code, amount)
VALUES(".$db->qstr($grant_no).",".$db->qstr($v['area']).",".$db->qstr($v['account_type']).",".$db->qstr($v['amount']).")";
          $ok=$db->Execute($sql);
          if (!$ok) break;
        }
      }
    }

    if (!$ok) {
      $objResponse->alert("Unable to save grant entries to the database...".$db->ErrorMsg());
      $db->FailTrans();
    }
    else {
      $objResponse->alert("Auto-compute entries successfully saved...");
    }
    
    $db->CompleteTrans();
    $objResponse->call('init');
    $objResponse->call('doneLoading');
    return $objResponse;
  }
  
  function populateAutoComputedEntries($nr) {
    global $db;
    $objResponse = new xajaxResponse();
    $bc = new SegBillingGrant();
    
    $result = $bc->populateAutoComputedEntries($nr,'FB');
    $objResponse->call('clear');
    if ($result) {
      while ($row=$result->FetchRow()) {
        $details = array();
        $details['id']  = $row['account_id'];
        $details['name']  = $row['account_name'];
        $details['amount']  = $row['amount'];
        $details['status']  = $row['status'];
        $details['FLAG']  = 1;
        
        if ($row['status'])
          $objResponse->call('addPaid', $details);
        else
        $objResponse->call('add', $details);
      }
    }
    else {
      $details = array('message'=>$bc->sql);
      $objResponse->call('add', $details);
    }
    
    return $objResponse;
  }

 	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/sponsor/class_sponsor.php');
  require($root_path.'include/care_api_classes/sponsor/class_grant.php');
  require($root_path.'include/care_api_classes/sponsor/class_billing_grant.php');
	require_once($root_path.'modules/sponsor/ajax/sponsor-compute.common.php');
	$xajax->processRequest();
?>