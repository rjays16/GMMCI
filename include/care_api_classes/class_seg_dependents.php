<?php
// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegDependents extends Core {

	/**
	* Database table for the discount data
	* @var string
	*/
	var $tb_dependent='seg_dependents';

	/**
	* SQL query result. Resulting ADODB record object.
	* @var object
	*/
	var $result;
	
	/**
	* Resulting record count
	* @var int
	*/
	var $count;
	var $saveOk;

	/**
	* Fieldnames of the care_appointment table.
	* @var array
	*/	
	var $fld_dependent=array(
		"parent_pid", 
		"dependent_pid", 
		"relationship", 
		"status",
		"history", 
		"modify_id",
		"modify_dt",      
		"create_id",  
		"create_dt"
		);
		
	
	/**
	* Constructor
	* @param string refno
        	*/
	function SegDependents(){
		$this->setTable($this->tb_dependent);
		$this->setRefArray($this->fld_dependent);
	}
	
	/**
	* Sets the core object to point to seg_discount and corresponding field names.
	*/
	function useSegDependents(){
		$this->coretable=$this->tb_dependent;
		$this->ref_array=$this->fld_dependent;
	}
	
	function clearDependentList($parent_id){
		global $db;
		
		$this->sql = "DELETE FROM $this->tb_dependent WHERE parent_pid='$parent_id'";
		return $this->Transact();
	}
	
	function addDependent($data, $dep_list){
		global $db;
		
		$parent_id = $db->qstr($data['pid']);
		$history = $db->qstr($data['history']);
		$modify_id = $db->qstr($data['modify_id']);
		$modify_dt = $db->qstr($data['modify_dt']);
		$create_id = $db->qstr($data['create_id']);
		$create_dt = $db->qstr($data['create_dt']);
		
		$this->sql = "INSERT INTO $this->tb_dependent(parent_pid,dependent_pid,relationship,status,history,modify_id,modify_dt,create_id,create_dt) VALUES($parent_id ,?,?,?,$history,$modify_id,$modify_dt,$create_id,$create_dt)";
		#echo "sql = ".$this->sql;
		if($buf=$db->Execute($this->sql,$dep_list)) {
			$this->saveOK = true;
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { $this->saveOK = false; return false; }
	}
	
	function getAllDependents($parent_id){
		global $db;
		
		$this->sql="SELECT d.*,IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS person_age, p.* 
					FROM seg_dependents AS d
					INNER JOIN care_person AS p ON p.pid=d.dependent_pid
					WHERE parent_pid='".$parent_id."'
					AND d.status NOT IN ('cancelled','deleted','expired') ";
		#echo "sql = ".$this->sql;
		if ($this->result=$db->Execute($this->sql)) {
	    if ($this->count=$this->result->RecordCount()){
			return $this->result;
		 }else{
				return FALSE;
		 }
	 	}else{
			return FALSE;
    	}
	}
}
?>