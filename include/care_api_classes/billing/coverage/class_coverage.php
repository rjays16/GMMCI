<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCoverage extends Core {
	
	var $target;
	var $coverage_tb = "seg_applied_coverage";
	
	function SegCoverage() 
	{
		global $db;
		$this->coretable = $this->coverage_tb;
	}

	function clearReference( $refno, $source ) 
	{
		global $db;
		$this->sql = "DELETE FROM $this->coretable WHERE ref_no=".$db->qstr($refno)." AND source=".$db->qstr($source);
    if($this->result=$db->Execute($this->sql)) {
			return true;
		} else { return false; }
	}
	
	function addCoverage( $ref_array, $src_array, $code_array, $hcare_array, $coverage_array ) 
	{
		global $db;
		
		$bulk = array();
		$j=1;
		foreach ($ref_array as $i=>$ref) {
			$bulk[$i] = array( $ref, $src_array[$i], $code_array[$i], $hcare_array[$i], $j++, $coverage_array[$i] );
		}
		
		$this->sql = "INSERT INTO $this->coretable(ref_no,source,item_code,hcare_id,priority,coverage) VALUES(?,?,?,?,?,?)";

		if($this->result=$db->Execute($this->sql,$bulk, $autoquote=FALSE)) {
			return true;
		} else { 
			return false; 
		}
	}
	
}

