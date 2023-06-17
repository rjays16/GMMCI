<?php
//created by cha 05-21-09
  function populateORList($from_ORNo, $to_ORNo)
  {
    global $db;      
    $objResponse = new xajaxResponse();
    $cashObj = new SegCashierEditOR();

    $rows=$cashObj->getORDetails($from_ORNo,$to_ORNo);

    #$objResponse->alert('result='.var_export($cashObj->sql,true));
    $total = sizeof($rows);

    $objResponse->call("clearList","ORList");
    $objResponse->call("clearBuffer","ORList");

    #while($cnt<$total)
    foreach ($rows as $i=>$row) {
      if(!$row["cancel_date"]) {
        $status="";
      }
      else {
        $status="Cancelled";
      }
      $objResponse->call("viewORList","ORList",$row["or_no"], $row["or_date"], $row["or_name"], $status, $row["create_id"]);
      $objResponse->call("saveOldORNo",$row["or_no"]);
    } 
    if (!$total) $objResponse->call("viewORList","ORList",NULL);   
    return $objResponse;
  }

  function generateNewOR($oldORArray,$arraylen,$new_fr_or,$new_to_or)
  {
    global $db;
    $glob_obj = new GlobalConfig($GLOBAL_CONFIG);       
    $glob_obj->getConfig('cashier_or_number_digits');
    $maxORLength = $GLOBAL_CONFIG['cashier_or_number_digits'];
    
    $objResponse = new xajaxResponse();
    $cashObj = new SegCashierEditOR();
    
    $cnt=0;
    $int_fr_or=(int)$new_fr_or;
    $int_to_or=(int)$new_to_or;
    $temp_or=$int_fr_or;   
    //$objResponse->call("clearList","ORList");
    
    $checker=false;
     while($cnt<$arraylen)
    { 
      $str_or=(string)$temp_or;
      $appendzero=$maxORLength-(strlen($str_or));
      switch($appendzero)
      {
         case 1: $zero="0"; break;
         case 2: $zero="00"; break;
         case 3: $zero="000"; break;
         case 4: $zero="0000"; break;
         case 5: $zero="00000"; break;
         case 6: $zero="000000"; break;
         case 7: $zero="0000000"; break;
      }
      $new_or=$zero."".$temp_or;
      /*
      $ifexists=$cashObj->ifORExists($new_or);
      if($ifexists==$new_or)
      {
       $checker=true;
       $objResponse->call('generateOR',$new_or,$oldORArray[$cnt]);
       $objResponse->call("highlightOR",$checker,$oldORArray[$cnt],$new_or,'#ff0000'); 
      }
      else
      {
        $checker=false;
        $objResponse->call('generateOR',$new_or,$oldORArray[$cnt]);
        $objResponse->call("highlightOR",$checker,$oldORArray[$cnt],$new_or,'#000000');
      }
      //$objResponse->call("listNewOR","ORList",$local_row["or_no"],$local_row["or_date"],$local_row["or_name"],$status,$new_or,$checker);
      */
      $objResponse->call('generateOR',$new_or,$oldORArray[$cnt]);
      $objResponse->call("saveNewORNo",$new_or);
      if($temp_or<$int_to_or)
      {
        $temp_or++;
      }
      $cnt++;
    }
    
    $objResponse->call('validateInputs');
    return $objResponse;
  }

  /*
  function saveChanges($newORArray, $oldORArray, $arraylen,$date)
  {
    global $db;    
    $objResponse = new xajaxResponse();
    $cashObj = new SegCashierEditOR();

    $result=false;
    $cnt=0;
    while($cnt<$arraylen)
    {
      $oldhist=$cashObj->getHistory($oldORArray[$cnt]);
      $newhist=$oldhist." OR# changed (old:".$oldORArray[$cnt]."): ".$date."[admin]";
      $result=$cashObj->saveNewOR($newORArray[$cnt],$oldORArray[$cnt],$newhist);
      $cnt++;
    }
    if($result)
    {
     $objResponse->alert("Save successful!"); 
     $objResponse->call("clearHeader","Search");
     $objResponse->call("clearList","ORList");
     $objResponse->call("viewORList","ORList",NULL);
    }
    if(!$result) $objResponse->alert("Save not successful!");
    return $objResponse;
  }
  */
  
  function saveChanges( $orno_array ) {
    global $db;
    $objResponse = new xajaxResponse();
    
    $old_ors = array_keys($orno_array);
    $new_ors = array_values($orno_array);
    if ($old_ors === $new_ors)
      $objResponse->alert("No changes found for the OR series...");
    else {
      $db->StartTrans();
      $chunk = dechex(rand(0,65535));
      $sql = "UPDATE seg_pay SET or_no=CONCAT('".$chunk."', or_no) WHERE or_no IN ('".implode("','",$old_ors)."')";
      $ok=$db->Execute($sql);
      if ($ok) {
        foreach ($orno_array as $old=>$new) {
          $history_log_entry = "CONCAT('OR# changed (old:".$old.") ', NOW(),".$db->qstr("[".$_SESSION['sess_temp_userid']."]").")";
          $sql = "UPDATE seg_pay SET or_no=".$db->qstr($new).",history=CONCAT(history,$history_log_entry) WHERE or_no=".$db->qstr($chunk.$old);
          $ok=$db->Execute($sql);
          if (!$ok) {
            $last_error_msg = "Error updating OR #$old to #$new";
            break;
          }
        }
      }
      else {
        $last_error_msg = "Error assigning temporary numbers to OR series...";
      }
      
      if ($ok) {
        $db->CompleteTrans();
        $objResponse->alert("Save successful...");
        $objResponse->call("clearHeader","Search");
        $objResponse->call("clearList","ORList");
        $objResponse->call("viewORList","ORList",NULL);
      }
      else {
        $db->FailTrans();
        $db->CompleteTrans();
        $objResponse->alert($last_error_msg."\n".$sql);
      }
    }
    return $objResponse;
  }

  function checkIfORExists($orig_or,$new_or)
  {
    global $db;    
    $objResponse = new xajaxResponse();
    $cashObj = new SegCashierEditOR();
    $ifexists=$cashObj->ifORExists($new_or);
    $checker=false;
    if($ifexists==$new_or)
    {
        //$objResponse->alert("OR#".$or_no." exists already!");
        $checker=true;
        //$objResponse->alert('checker='.$checker);
        //$objResponse->call("highlightOR",$checker,$orig_or,$new_or,'#ff0000');
        $objResponse->call("showWarning", $orig_or, 0);
        //$objResponse->alert("OR#".$new_or." exists already!");
        return $objResponse; 
    }
    else
    {
        $checker=false;
        //$objResponse->alert('ok ang OR');
        //$objResponse->call("highlightOR",$checker,$orig_or,$new_or,'#000000');
        $objResponse->call("showWarning", $orig_or, 1);
        return $objResponse; 
    }
   
  }
  
  function checkORNos( $ornos ) {
    global $db;
    $objResponse = new xajaxResponse();
    
    #$objResponse->alert(print_r( $ornos, TRUE));
    #return $objResponse;
    
    if (!ornos) $error_found = TRUE;
    else {
      $frequency_array = array();
      $error_found = FALSE;
      
      foreach ($ornos as $old=>$new) {
        if (!$frequency_array[$new]) $frequency_array[$new]=1;
        else $frequency_array[$new]++;
      }
      
      $ornos_in_array = implode("','",array_keys($ornos));
      foreach ($ornos as $old=>$new) {
        if (!$new) {
          $objResponse->call('showWarning', $old, -1, 'OR number cannot be blank');
          $error_found = TRUE;
        }
        elseif (!is_numeric($new)) {
          $objResponse->call('showWarning', $old, -1, 'OR number must be a numeric value');
          $error_found = TRUE;
        }
        elseif ($frequency_array[$new] > 1) {
          $objResponse->call('showWarning', $old, -1, 'OR numbers must be unique');
          $error_found = TRUE;
        }
        else {
          if (!in_array( $new, array_keys($ornos) )) {
            $sql = "SELECT EXISTS(SELECT or_no FROM seg_pay WHERE or_no=".$db->qstr($new).")";
            $result = $db->GetOne($sql);
            
            if ($result==1) {
              #$objResponse->alert($sql);
              $objResponse->call('showWarning', $old, -1, 'OR number already exists');
              $error_found = TRUE;
            }
            else {
              $objResponse->call('showWarning', $old, 1,'OR number is valid');
            }
          }
          else {
            $objResponse->call('showWarning', $old, 1,'OR number is valid');
          }
        }
      }
    }
    
    if ($error_found) $objResponse->call('setValid', 0);
    else $objResponse->call('setValid', 1);
    return $objResponse;
  }

  require('./roots.php');
  include_once($root_path.'include/care_api_classes/class_globalconfig.php'); 
  require($root_path.'include/inc_environment_global.php');    
  require($root_path.'include/care_api_classes/class_cashier_edit_or_no.php');
  require($root_path.'modules/cashier/ajax/seg-cashier-edit-or.common.php');
  $xajax->processRequest();
?>
