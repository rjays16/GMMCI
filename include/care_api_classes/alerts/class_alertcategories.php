<?php

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegAlertCategory extends Core {
 
  function SegAlertCategory() {
    $this->coretable = "seg_alert_categories";
  }
  
  function getAllCategories() {
		global $db;
		
		$this->sql="SELECT * FROM $this->coretable ORDER BY category_name";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				 return $this->result->GetArray();
			} else {
				return array();
			}
		} 
		else {
			return FALSE;
		}
	}

}


