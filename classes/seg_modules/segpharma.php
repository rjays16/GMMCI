<?php
// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require('getdbparam.php');
require('seginventory.php');

class SegPharma extends DBParam {
	var $dbcnn;
	
	function DBConnect($cnn) {	
		// Read the server name, user ID, password and database name from a CONF file.			
		if (!isset($cnn)) {	
			DBParam::getconnectparams($server, $userID, $password, $database);

			$this->dbcnn = mysql_pconnect($server, $userID, $password) or die("Unable to connect to the database server");
			mysql_select_db($database) or die("Unable to connect to the database <b>$database</b>");
		}
		else
			$this->dbcnn = $cnn;		
	}
	
	// Function for inserting new pharmacy transaction.
	function insPharmaTrans($refNo, 			// Unique no. identifying transaction.
							$purchasedte, 		// Date of purchase.
							$pid, 				// Patient id.
							$encoder, 			// Encode id.
							$enc_date, 			// Date encoded.
							$enc_time) {		// Time encoded.
														
		$qry = "insert into seg_pharma_retail (refno, purchasedte, pid, encoder, enc_date, enc_time) 
				values ('$refNo', '$puchasedte', $pid, '$encoder', '$enc_date', '$enc_time')";
		$result = mysql_query($qry, $this->dbcnn);
		$status = $result <> 0 ? TRUE : FALSE;			
		return($status);
	}							
							
	// Function for inserting new pharmacy transaction detail.
	function insPharmaTransDetail($refNo, 			// Unique no. identifying transaction.
								  $bestellnum, 		// Number of pharmacy product or item.
								  $qty, 			// Qty of items to buy.
								  $rpriceppk, 		// Retail price per pack.
								  $pack) {			// Pack of item.								

		$qry = "insert into seg_pharma_rdetails (refno, bestellnum, qty, rpriceppk, pack) 
				values ('$refNo', $bestellnum, $qty, $rpriceppk, '$pack')";
		$result = mysql_query($qry, $this->dbcnn);
		$status = $result <> 0 ? TRUE : FALSE;			
		
		if ($status) {		// Update the inventory.
			$objinventory = new 
			
			
		return ($status);
	}							
		
	// Function for deleting pharmacy transaction identified by its reference/document no.
	// This function will also delete the transaction details.
	function delPharmaTrans($refNo) {
		if ($refNo == "")
			$qry = "delete from seg_pharma_retail";
		else
			$qry = "delete from seg_pharma_retail where refno = '$refNo'";
		$result = mysql_query($qry, $this->dbcnn);
		$status = $result <> 0 ? TRUE : FALSE;				
		return($status);		
	}
	
	// Function for updating the pharmacy transaction identified by its reference/document no.
	function updPharmaTrans($refNo, 			// Unique no. identifying transaction.
							$purchasedte, 		// Date of purchase.
							$pid, 				// Patient id.
							$encoder, 			// Encode id.
							$enc_date, 			// Date encoded.
							$enc_time,			// Time encoded.	
							$oldrefno) {		// Previous ref. no.

		$qry = "update seg_pharma_retail set 
				refno = '$refno', 
				purchasedte = '$purchasedte', 
				pid = $pid, 
				encoder = '$encoder', 
				enc_date = '$enc_date', 
				enc_time = '$enc_time'
				where refno = '$oldrefno'";				
		$result = mysql_query($qry, $this->dbcnn);
		$status = $result <> 0 ? TRUE : FALSE;							   
		return($status);
	}

?>