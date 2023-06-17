<?php
// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegPharma extends Core {

	/**
	* Database table for the pharmacy transaction price
	* @var string
	*/
	var $tb_pharma_prices='seg_pharma_prices';

	/**
	* Database table for the pharmacy transaction details
	* @var string
	*/
	var $tb_pharma_rdetails='seg_pharma_rdetails';	
	var $tb_pharma_products='care_pharma_products_main';

	/**
	* Database table for the pharmacy transaction information
	* @var string
	*/
	var $tb_pharma_retail='seg_pharma_retail';
	
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
	var $fld_pharma_retail=array(
		"refno",
		"purchasedte",
		"encounter_nr",
		"is_cash",
		"create_id",
		"create_timestamp",
		"modify_id",
		"modify_timestamp"
		);
		
	var $fld_pharma_rdetails=array(
		"refno",
		"bestellnum",
		"entrynum",
		"qty",
		"rpriceppk"
	);
	
	var $fld_pharma_prices=array(
		"bestellnum",
		"ppriceppk",
		"chrgrpriceppk",
		"cshrpriceppk",
		"modify_id",
		"modify_date",
		"create_id",
		"create_date"
	);

	/**
	* Constructor
	* @param string refno
	*/
	function SegPharma($refno=''){
		if(!empty($refno)) $this->refno=$refno;
		$this->setTable($this->tb_pharma_retail);
		$this->setRefArray($this->tabfields);
	}
	
	/**
	* Sets the core object to point to seg_pharma_retail and corresponding field names.
	*/
	function usePharmaRetail(){
		$this->coretable=$this->tb_pharma_retail;
		$this->ref_array=$this->fld_pharma_retail;
	}
	
	/**
	* Sets the core object to point to seg_pharma_rdetails and corresponding field names.
	*/
	function usePharmaRdetails(){
		$this->coretable=$this->tb_pharma_rdetails;
		$this->ref_array=$this->fld_pharma_rdetails;
	}
	
	/**
	* Sets the core object to point to seg_pharma_prices and corresponding field names.
	*/
	function usePharmaPrices(){
		$this->coretable=$this->tb_pharma_prices;
		$this->ref_array=$this->fld_pharma_prices;
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
		$this->usePharmaPrices();
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
		return true;
	}	
	/**
	* Retrieves a Pharmacy's record from the database's 'seg_pharma_prices' table.
	* @access public
	* @param string Product number
	* @return boolean OR the Pharmaceutical Product record 
	*    documented by: burn Sept. 11, 2006	
	*/		
	function GetProductPrice($bestellnum) {
		global $db;
		$this->usePharmaPrices();
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
	* Creates a Pharmacy Transaction record
	*/
	function CreatePharmaTransaction(
							$refno, 				// Unique no. identifying transaction.
							$purchasedte, 	// Date of purchase.
							$encounter_nr, 	// Patient encounter number
							$is_cash, 			// Payement is in cash
							$encoder_id) {	// Encoder id
		$this->usePharmaRetail();
		$this->sql = "INSERT INTO $this->coretable (refno, purchasedte, encounter_nr, is_cash, create_id, modify_id, create_dt, modify_dt)
				VALUES ('$refno', '$purchasedte', $encounter_nr, $is_cash, '$encoder_id', '$encoder_id', NOW(), NOW())";
		return $this->Transact();
	}
	
	/**
	* Deletes a transaction record.
	* @access public
	* @param int Reference number
	* @return boolean.
	*/
	function DeletePharmaTransaction($refno){
			$this->usePharmaRetail();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno'";
     return $this->Transact();
	}
	
	function UpdatePharmaTransaction(
							$refno,
							$newrefno,
							$purchasedte, 	// Date of purchase.
							$encounter_nr,	// Patient encounter number
							$is_cash,				// Payment is in cash?
							$encoder_id) 		// Encode id.
	{
		$this->usePharmaRetail();
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
	* Checks if the transaction exists based on the referenc numbr given.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean
	*/
	function TransactionExists($refno){
		global $db;
		$this->usePharmaRetail($type);
		$this->sql="SELECT refno FROM $this->coretable WHERE refno='$refno'";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
	/**
	* Creates a Pharmaceutical Transaction Details record into the database's 'seg_pharma_rdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @param string Pharmaceutical Product number
	* @param int Quantity of product involved
	* @param double Product Price 
	* @return boolean OR the 'maxentry'
	*    documented by: burn Sept. 11, 2006
	*/	
	function AddTransactionDetails(
							$refno,			// reference no
							$nr,				// product no
							$qty,				// quantity of product involved
							$rpriceppk	// the retail price per package of the product as per the transaction
	) 
	{

		global $db;
		$this->usePharmaRdetails();
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
	* Clears a Pharmaceutical Transaction Details record from the database's 'seg_pharma_rdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean
	*    documented by: burn Sept. 11, 2006
	*/				
	function ClearTransactionDetails($refno) {
		$this->usePharmaRdetails();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno'";
		return $this->Transact();
	}
	/**
	* Removes a Pharmaceutical Transaction Details record from the database's 'seg_pharma_rdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @param int Entry number
	* @return boolean
	*    documented by: burn Sept. 11, 2006
	*/				
	function RemoveTransactionDetails($refno, $entrynum) {
		$this->usePharmaRdetails();
		$this->sql="DELETE FROM $this->coretable WHERE refno='$refno' AND entrynum=$entrynum";
		return $this->Transact();
	}
	/**
	* Retrieves a Pharmaceutical Transaction Details record from the database's 'seg_pharma_rdetails' table.
	* @access public
	* @param string Reference number of the Transaction
	* @return boolean OR
	*         the Pharmaceutical Transaction Details record including the product/article name
	*    documented by: burn Sept. 11, 2006	
	*/		
	function GetTransactionDetails($refno) {
		global $db;
		$tb_products=$this->tb_pharma_products;
		$this->usePharmaRdetails();
		$this->count=0;
		$this->sql="SELECT $this->coretable.*, ".$tb_products.".artikelname FROM $this->coretable,$tb_products WHERE refno='$refno' AND $this->coretable.bestellnum=".$tb_products.".bestellnum";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}
	
	
}
?>