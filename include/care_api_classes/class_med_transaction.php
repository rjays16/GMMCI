<?php
// Class for updating `seg_med_retail` and `seg_med_rdetails` tables.
// Created: 8-4-2006 (Bernard Klinch S. Clarito II)

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegMed extends Core {

	/**
	* Database table for the medical depot transaction price
	* @var string
	*/
	var $tb_med_prices='seg_med_prices';

	/**
	* Database table for the medical depot transaction details
	* @var string
	*/
	var $tb_med_rdetails='seg_med_rdetails';	
	var $tb_med_products='care_med_products_main';

	/**
	* Database table for the medical depot transaction information
	* @var string
	*/
	var $tb_med_retail='seg_med_retail';
	
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
	var $fld_med_retail=array(
		"refno",
		"purchasedte",
		"encounter_nr",
		"is_cash",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
		);
		
	var $fld_med_rdetails=array(
		"refno",
		"bestellnum",
		"entrynum",
		"qty",
		"rpriceppk"
	);
	
	var $fld_med_prices=array(
		"bestellnum",
		"ppriceppk",
		"chrgrpriceppk",
		"cshrpriceppk",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
	);

	/**
	* Constructor
	* @param string refno
	*/
	function SegMed($refno=''){
		if(!empty($refno)) $this->refno=$refno;
		$this->setTable($this->tb_med_retail);
		$this->setRefArray($this->tabfields);
	}
	
	/**
	* Sets the core object to point to seg_med_retail and corresponding field names.
	*/
	function useMedRetail(){
		$this->coretable=$this->tb_med_retail;
		$this->ref_array=$this->fld_med_retail;
	}
	
	/**
	* Sets the core object to point to seg_med_rdetails and corresponding field names.
	*/
	function useMedRdetails(){
		$this->coretable=$this->tb_med_rdetails;
		$this->ref_array=$this->fld_med_rdetails;
	}
	
	/**
	* Sets the core object to point to seg_med_prices and corresponding field names.
	*/
	function useMedPrices(){
		$this->coretable=$this->tb_med_prices;
		$this->ref_array=$this->fld_med_prices;
	}
	
	/**
	* Updates the product pricing details. If the product pricing information does 
		not exist the function attempts to create it.
	* @access public
	* @param string Product number
	* @param double Product purchase price
	* @param double Product current retail price
	* @return boolean.
	*/
	function UpdateProductPrice($encoder, $nr, $pprice, $cshprice, $chrgprice) {
		//if (empty($nr)) return false;	
		global $db;
		$this->useMedPrices();
		$this->sql="SELECT 1 FROM $this->coretable WHERE bestellnum='$nr'";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {				
				// Price information exists...
				//	...we update the price information
				$this->sql = "UPDATE $this->coretable SET ppriceppk=$pprice,chrgrpriceppk=$chrgprice,cshrpriceppk=$cshprice,modify_id='$encoder',modify_dt=NOW() WHERE bestellnum='$nr'";
			} else { 
				// Price information does not exist, and so we need to create it first
				$this->sql = "INSERT INTO $this->coretable (bestellnum, ppriceppk, cshrpriceppk, chrgrpriceppk, modify_id, modify_dt, create_id, create_dt) ".
				                                   "VALUES ('$nr',$pprice,$cshprice,$chrgprice,'$encoder',NOW(),'$encoder',NOW())";
			}
			// return the SQL Transaction result (true means the product has been updated)
			return $this->Transact();
		} else { 
			// SQL Error occurred
			return false; 
		}
	}	
	/**
	* Retrieves a Medical Depot's record from the database's 'seg_med_prices' table.
	* @access public
	* @param string Product number
	* @return boolean OR the Laboratory Service record including the Service Group name
	*    documented by: burn Sept. 11, 2006	
	*/		
	function GetProductPrice($bestellnum) {
		global $db;
		$this->useMedPrices();
		$this->count=0;
		$this->sql="SELECT * FROM $this->coretable WHERE bestellnum='$bestellnum'";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}	
	/**
	* Creates a Medical Depot's Transaction record in the database's 'seg_med_retail' table. 
	* @access public
	* @param string Reference number of the Transaction
	* @param Date purchase
	* @param int Patient encounter number
	* @param int Payment mode
	* @param Encoder id
	* @return boolean
	*    documented by: burn Sept. 11, 2006
	*/
	function CreateMedTransaction(
							$refno, 		// Unique no. identifying transaction.
							$purchasedte, 	// Date of purchase.
							$encounter_nr, 	// Patient encounter number
							$is_cash, 		// Payement is in cash
							$encoder_id) {	// Encoder id
		$this->useMedRetail();
		$this->sql = "INSERT INTO $this->coretable (refno, purchasedte, encounter_nr, is_cash, modify_id, modify_dt, create_id, create_dt)    
				VALUES ('$refno', '$purchasedte', $encounter_nr, $is_cash, '$encoder_id', NOW(), '$encoder_id', NOW())"; # burn modified: lines 172-173 September 8, 2006; Sept 8, 2006
		return $this->Transact();
	}
	
	/**
	* Deletes a Medical Depot's transaction record in the database's 'seg_med_retail' table. 
	* @access public
	* @param int Reference number
	* @return boolean.
	*    documented by: burn Sept. 11, 2006
	*/
	function DeleteMedTransaction($refno){
		$this->useMedRetail();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno'";
     	return $this->Transact();
	}
	/**
	* Updates a Medical Depot's transaction record in the database's 'seg_med_retail' table. 
	* @access public
	* @param string Reference number of the Transaction
	* @param string New Reference number of the Transaction
	* @param Date purchased.
	* @param int Patient encounter number
	* @param int Payment mode
	* @param Encoder id
	* @return boolean
	*    documented by: burn Sept. 11, 2006
	*/	
	function UpdateMedTransaction(
							$refno,         // Unique no. identifying transaction.
							$newrefno,      // New Unique no. identifying transaction.
							$purchasedte, 	// Date of purchase.
							$encounter_nr,	// Patient encounter number
							$is_cash,		// Payment is in cash?
							$encoder_id) 	// Encode id.
	{
		$this->useMedRetail();
		$this->sql = "UPDATE $this->coretable SET " .
							"refno='$newrefno', " .
							"purchasedte='$purchasedte', " . 
							"encounter_nr=$encounter_nr, " .
							"is_cash=".$is_cash.", " .	
							"modify_id='$encoder_id', " .
							"modify_dt=NOW() " .
							"WHERE refno = '$refno'";				
		return $this->Transact();
	}	
	/**
	* Checks if the Medical Depot's Transaction exists based on the reference number given.
	*   - uses the 'seg_med_retail' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean
	*    documented by: burn Sept. 11, 2006
	*/
	function TransactionExists($refno){
		global $db;
		$this->useMedRetail();
		$this->sql="SELECT refno FROM $this->coretable WHERE refno='$refno'";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Creates a Medical Depot's Transaction Details record into the database's 'seg_med_rdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @param string Product number
	* @param int Quantity of product involved
	* @param double Product Price 
	* @return boolean OR the 'maxentry'
	*    documented by: burn Sept. 11, 2006
	*/		
	function AddTransactionDetails(
							$refno,		// reference no
							$nr,		// product no
							$qty,		// quantity of product involved
							$rpriceppk	// the retail price per package of the product as per the transaction
	) 
	{
		global $db;
		$this->useMedRdetails();
		$this->sql="
			INSERT INTO $this->coretable(refno,bestellnum,qty,rpriceppk) 
			VALUES('$refno','$nr',$qty,$rpriceppk)";					
		$sqlResult=$this->Transact();
		if ($sqlResult) {
			$this->sql="SELECT MAX(entrynum) AS maxentry FROM $this->coretable WHERE refno='$refno' AND bestellnum='$nr'";
			if ($result=$db->Execute($this->sql)) {
				$row=$result->FetchRow();
				return $row["maxentry"];
			}
			else {return FALSE;}
		}
		else{return FALSE;}
	}
	/**
	* Clears a Medical Depot's Transaction Details record from the database's 'seg_med_rdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean
	*    documented by: burn Sept. 11, 2006
	*/			
	function ClearTransactionDetails($refno) {
		$this->useMedRdetails();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno'";
		return $this->Transact();
	}
	/**
	* Removes a Medical Depot's Transaction Details record from the database's 'seg_med_rdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @param int Entry number
	* @return boolean
	*    documented by: burn Sept. 11, 2006
	*/			
	function RemoveTransactionDetails($refno, $entrynum) {
		$this->useMedRdetails();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno' AND entrynum=$entrynum";
		return $this->Transact();
	}
	/**
	* Retrieves a Medical Depot's Transaction Details record from the database's 'seg_med_rdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean OR
	*         the Medical Depot's Transaction Details record including the product/article name
	*    documented by: burn Sept. 11, 2006	
	*/	
	function GetTransactionDetails($refno) {
		global $db;
		$tb_products=$this->tb_med_products;
		$this->useMedRdetails();
		$this->count=0;
		$this->sql="SELECT $this->coretable.*, ".$tb_products.".artikelname FROM $this->coretable,$tb_products WHERE refno='$refno' AND $this->coretable.bestellnum=".$tb_products.".bestellnum";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}
}//end of class SegMed
?>