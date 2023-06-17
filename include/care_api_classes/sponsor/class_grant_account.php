<?php

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegGrantAccount extends Core {
  
  function SegGrantAccount() {
    $this->coretable = "seg_grant_accounts";
  }
  
  function getAccounts($id) {
    global $db;    
    $this->sql = "SELECT * FROM $this->coretable\n";
    if ($id) $this->sql .= "WHERE account_id=".$db->qstr($id);
    else $this->sql .= "ORDER BY is_personal DESC,account_name ASC";
    
    if ($this->result=$db->Execute($this->sql))
      return $this->result;
    else
      return false;
  }
  
  function getAccountsByPrototype($prototypes=NULL) {
    global $db;    
    
    $this->sql = "SELECT * FROM $this->coretable\n";
    if ($prototypes) {
      if (is_array($prototypes)) {
        $where = array();
        foreach ($prototypes as $v) 
          $where[] = "prototype=".$db->qstr($v);
        $this->sql .= "WHERE (".implode(")\n OR (", $where). ")\n";
      }
      else
        $this->sql .= "WHERE prototype=".$db->qstr($prototypes)."\n";
    }
    $this->sql .= "ORDER BY account_name ASC\n";
    
    if ($this->result=$db->Execute($this->sql))
      return $this->result;
    else
      return false;
  }
  
}
  
