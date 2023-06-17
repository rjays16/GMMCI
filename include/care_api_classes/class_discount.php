<?php
// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegDiscount extends Core {

	/**
	* Database table for the discount data
	* @var string
	*/
	var $tb_discount='seg_discount';

	/**
	* Database table for the pharmacy transaction discount
	* @var string
	*/
	var $tb_rdiscount_pharma='seg_pharma_order_discount';	
	var $tb_rdiscount_med_depot='seg_med_order_discount';	
	var $tb_charity_grants = "seg_charity_grants";
	
	/**
	* Reference number
	* @var string
	*/
	var $refno;
	
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

	/**
	* Fieldnames of the care_appointment table.
	* @var array
	*/	
	var $fld_discount=array(
		"discountid",
		"discountdesc",
		"discount",
		"create_id",
		"create_timestamp",
		"modify_id",
		"modify_timestamp"
		);
		
	var $fld_rdiscount=array(
		"refno",
		"discountid"
	);
	
	/**
	* Constructor
	* @param string refno
        	*/
	function SegDiscount(){
		$this->setTable($this->tb_discount);
		$this->setRefArray($this->fld_discount);
	}
	
	/**
	* Sets the core object to point to seg_discount and corresponding field names.
	*/
	function useDiscount(){
		$this->coretable=$this->tb_discount;
		$this->ref_array=$this->fld_discount;
	}
	
	/**
	* Sets the core object to point to seg_rdiscount and corresponding field names.
	*/
	function useRDiscount($target){
		if ($target=="med_depot")
			$this->coretable=$this->tb_rdiscount_med_depot;
		else
			$this->coretable=$this->tb_rdiscount_pharma;
		$this->ref_array=$this->fld_rdiscount;
	}

	function CreateDiscount(
							$id,
							$desc,
							$discount,
							$area_used,
							$bill_areas,
							$encoder_id) {
		global $db;
		$id = $db->qstr($id);
		$desc = $db->qstr($desc);
		$discount = $db->qstr($discount);
		$encoder_id = $db->qstr($encoder_id);		
		$bill_areas = $db->qstr($bill_areas);
		
		$this->useDiscount();
		
		#added by VAN 06-18-08
		if ($area_used=='A'){
			$area_used = '';
			$is_forall = 1;
		}else{
			$is_forall = 0;
		}	
		#----------------------
		
		$this->sql = "INSERT INTO $this->coretable (discountid,discountdesc, discount, area_used, is_forall, billareas_applied, create_id, modify_id)
				VALUES ($id, $desc, $discount,'$area_used', $is_forall, $bill_areas, $encoder_id, $encoder_id)";
		return $this->Transact();
	}
	
	/**
	* Deletes a transaction record.
	* @access public
	* @param int Reference number
	* @return boolean.
	*/
	function DeleteDiscount($id){
		global $db;
		$id = $db->qstr($id);
		$this->useDiscount();
		$this->sql="DELETE FROM $this->coretable WHERE discountid=$id";
    return $this->Transact();
	}
	
	function UpdateDiscount($id, $newid, $desc, $discount, $area_used, $bill_areas, $encoder_id)	{
		global $db;
		$id = $db->qstr($id);
		$newid = $db->qstr($newid);
		$desc = $db->qstr($desc);
		$discount = $db->qstr($discount);
		$encoder_id = $db->qstr($encoder_id);
		$bill_areas = $db->qstr($bill_areas);
		$this->useDiscount();
		
		if ($area_used=='A'){
			$area_used = '';
			$is_forall = 1;
		}else{
			$is_forall = 0;
		}	
		
		$this->sql = "UPDATE $this->coretable SET\n" .
							"discountid=$newid,\n" .
							"discountdesc=$desc,\n" . 
							"discount=$discount,\n" .
							"area_used='$area_used',\n" .
							"billareas_applied=$bill_areas,\n" .
							"is_forall=$is_forall,\n" .
							"modify_id=$encoder_id,\n" .
							"modify_timestamp=CURRENT_TIMESTAMP\n" .
							"WHERE discountid = $id\n";				
		return $this->Transact();
	}
	
	function AddRetailDiscount($refno, $discountid, $target) {
		$this->useRDiscount($target);
		$this->sql = "INSERT INTO $this->coretable (refno, discountid) VALUES('$refno', $discountid)";
		return $this->Transact();
	}
	
	function RemoveRetailDiscount($refno, $discountid, $target) {
		global $db;
		$this->useRDiscount($target);
		$refno = $db->qstr($refno);
		$discountid = $db->qstr($discountid);
		$this->sql = "DELETE FROM $this->coretable WHERE refno=$refno AND discountid=$discountid";
		return $this->Transact();
	}
	
	function ClearRetailDiscounts($refno, $target) {
		global $db;
		$this->useRDiscount($target);
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->coretable WHERE refno=$refno";
		return $this->Transact();
	}

	function GetEncounterCharityGrants($encounter_nr) {
		global $db;
		$nr = $db->qstr($encounter_nr);
		$this->sql="SELECT * FROM $this->tb_charity_grants WHERE encounter_nr=$nr";
		if($this->result=$db->Execute($this->sql)){
			return $this->result;
		}
		else{ return FALSE; }
	}
	
	function GetDiscounts() {
		global $db;
		
		$this->sql="SELECT * FROM $this->coretable ORDER BY is_charity ASC";
		if($this->result=$db->Execute($this->sql)){
			return $this->result;
		}
		else{ return FALSE; }
	}
	
	function getBillAreas($sid) {
		global $db;
		
		$sid = $db->qstr($sid);
		$this->sql = "select billareas_applied from $this->coretable where discountid = $sid";
		if($this->result = $db->Execute($this->sql)) {
			$row = $this->result->FetchRow();
			return $row['billareas_applied'];
		}
		else{ return FALSE; }
	}
	
	function getDiscount($sid) {
		global $db;
		
		$sid = $db->qstr($sid);
		$this->sql = "select discount from $this->coretable where discountid = $sid";
		if($this->result = $db->Execute($this->sql)) {
			$row = $this->result->FetchRow();
			return $row['discount'];
		}
		else{ return FALSE; }		
	}
}
?>