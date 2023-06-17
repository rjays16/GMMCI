<?php

// Helper class for Lingap/CMAP module
// Created by: AJMQ

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegLingapCMAP extends Core {

	var $sponsor_tb = "seg_hcare_sponsors";

	function SegLingapCMAP () {
		$this->coretable = $this->sponsor_tb;
	}
	
	function get() {
		global $db;
		$this->sql = "SELECT * FROM $this->coretable ORDER BY account_name";
		return $this->Transact();
	}
}

?>