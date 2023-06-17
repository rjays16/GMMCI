<?php

require_once('./roots.php');  
require_once($root_path.'include/care_api_classes/class_core.php');

class Cashier_Promissory extends Core {
  /**
  var $seg_grant_accounts = 'seg_grant_accounts';
  var $seg_grant_entries = 'seg_grant_entries';
  var $seg_pay = 'seg_pay';
  **/
  function get_guarantor() {
    global $db;
    $this->sql = "SELECT sga.account_id, sga.account_name, sga.account_title, k.num FROM seg_grant_accounts as sga
                  INNER JOIN (SELECT COUNT(account_id) as num, account_title FROM seg_grant_accounts WHERE prototype IN ('guarantor', 'corporate') 
                  GROUP BY account_title) as k ON (sga.account_title=k.account_title)";
    if ($this->result = $db->Execute($this->sql)) {
      if ($this->result->RecordCount() > 0) {
        while ($row = $this->result->FetchRow()) {
          if ($row['num'] > 1)
            $array[$row['account_title']][$row['account_id']] = $row['account_name'];
          else
            $array[$row['account_id']] = $row['account_name'];
        }
         return $array;
      }
    }
  }
  
  function get_guarantor_by_orno($orno) {
    global $db;
        
    $this->sql = "SELECT sga.account_id, sga.account_name, sga.account_title, a.amount_tendered FROM 
                  (SELECT substr(pid, 2) as pay_pid, amount_tendered FROM seg_pay WHERE or_no=$orno) 
                  AS a INNER JOIN seg_grant_accounts sga ON (sga.account_id=a.pay_pid)";
    if ($this->result = $db->Execute($this->sql)) {
      if ($this->result->RecordCount() == 1) {
        $row = $this->result->FetchRow();
        return $row;
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }
  
  function get_grant_entries($account_id) {
    global $db;                      
    $this->sql = "SELECT sge.grant_no, cp.name_last, cp.name_first, cp.name_middle, sbe.bill_dte, SUM(sged.amount) as details_total_amt, spr.service_code
                  FROM seg_grant_entries sge INNER JOIN seg_billing_encounter sbe  ON (sge.ref_no=sbe.bill_nr)
                  INNER JOIN seg_grant_entry_details sged ON (sged.grant_no=sge.grant_no)
                  INNER JOIN care_encounter ce ON (ce.encounter_nr=sbe.encounter_nr)
                  INNER JOIN care_person cp ON (cp.pid=ce.pid)
                  LEFT JOIN seg_pay_request spr ON (spr.service_code=sge.grant_no AND spr.ref_no=sge.ref_no) 
                  WHERE sge.account_id=$account_id GROUP BY sge.grant_no";
    if ($this->result = $db->Execute($this->sql)) {
      if ($this->result->RecordCount() > 0) {
        return $this->result;
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }
  
  function get_grant_entry_details($grant_number) {
    global $db;
    $this->sql = "SELECT sged.service_code, sged.amount, scat.name_long, sge.ref_no FROM seg_grant_entry_details sged
                  INNER JOIN seg_grant_entries sge ON (sge.grant_no=sged.grant_no)
                  INNER JOIN seg_cashier_account_types scat ON (scat.type_id = sged.service_code) 
                  WHERE sged.grant_no=$grant_number";
    if ($this->result = $db->Execute($this->sql)) {
      if ($this->result->RecordCount() > 0) {
        return $this->result;
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }
  function save_pay_info($array)  {
    global $db;
    $db->StartTrans();
    if ($this->save_pay($array)) {
      if (!$this->save_pay_details($array)) {
        $db->FailTrans();
        $db->CompleteTrans();
        return false;
      }
      else {
        $db->CompleteTrans();
        return true;
      }   
    }
    else {
      $db->FailTrans();
      $db->CompleteTrans();
      return false;
    }
    
    
  }  
  function save_pay($array) {
    global $db;
    extract($array);
    $create_id = $_SESSION['sess_temp_userid'];
    $history = 'Created '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n";
    $this->sql = "INSERT INTO seg_pay(or_no, or_date, or_name, or_address, pid, amount_tendered, amount_due, history, modify_id, modify_dt, create_id, create_dt) 
                  VALUES('$or_no', '$or_date', '$or_name', '$or_address', 'P$pid', $amount_tendered, $amount_due, '$history', '$create_id', NOW(), '$create_id', NOW())"; 
   
    if ($this->result = $db->Execute($this->sql)) {
      if ($db->Affected_Rows() > 0) {
        return true;
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }
  
  function save_pay_details($array) {
    global $db;
    extract($array);
    $refno_count = count($ref_no);
    $sc_count = count($service_code);
    $amount_count = count($amount_due_individual);
    $has_failed = false;
    if (($refno_count == $sc_count) && ($refno_count == $amount_count)) {
      $iterator = 0;
      foreach ($ref_no as $key => $refno) {
        
        $this->sql = "INSERT INTO seg_pay_request(or_no, ref_no, ref_source, qty, amount_due, service_code)
                      VALUES ('$or_no', '$refno', '$ref_source', $qty, {$amount_due_individual[$key]}, '{$service_code[$key]}')";
        if ($this->result = $db->Execute($this->sql)) {
          if ($db->Affected_Rows() <= 0) {
            $has_failed = true;
            break;  
          }  
        }
        else {
          $has_failed = true;
          break;
        }
        $iterator++;
      }
      if ($has_failed) {
        return false;
      }
      else {
        if ($iterator == $refno_count && is_array($ref_no)) {
          return true;
        }
        else {
          return false;
        }
      }
    }
    else {
      return false;
    }
  }
  
  function get_grant_account_details($account_id) {
    global $db;
    $this->sql = "SELECT account_title, address FROM seg_grant_accounts WHERE account_id=$account_id";
    if ($this->result = $db->Execute($this->sql)) {
      if ($this->result->RecordCount() > 0) {
        return $this->result->FetchRow();
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  } 
  
}

?>
