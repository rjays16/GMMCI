<?php 
// Class for updating `seg_pharma_eod` table.


require('getdbparam.php');

class SegEOD extends DBParam {

	var $dbcnn;
	var $itemid;
	var $EODqty;
	var $EODdate = date("Y-m-d h:i:s");
	
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

	function GetItemID($itemid){
			$this->itemid = $itemid;
			return ($this->itemid);
	}
	
	function GetEODqty($EODqty){
			$this->EODqty = $EODqty;
			return ($this->EODqty);
	}
	
	function GetEODdate($EODdate) {
		$EODdate = date("Y-m-d h:i:s");
		$this->EODdate = $EODdate;
		return ($this->EODdate);
	}
	
	

}

?>