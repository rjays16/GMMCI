<?php

// Helper class for sponsors table
// Created by: AJMQ

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegSponsor extends Core {

	var $sponsor_tb = "seg_hcare_sponsors";

	function SegSponsor () {
		$this->coretable = $this->sponsor_tb;
	}
	
	function get() {
		global $db;
		$this->sql = "SELECT sp_id,sp_name FROM $this->coretable ORDER BY sp_name";
		if ($this->result=$db->Execute($this->sql)) {
			return $this->result;
		}
		else return FALSE;
	}
}

?>