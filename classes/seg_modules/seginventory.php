<?php
// Class for updating `seg_hosp_inventory` table.
// Created: 4-11-2006 (Lemuel S. Trazo)

require('getdbparam.php');

class SegInventory extends DBParam {
	var $dbcnn;
	var $itemid;
	var $itemqty;
	var $areaid;
	
	function DBConnect($cnn=NULL) {	
		// Read the server name, user ID, password and database name from a CONF file.			
		if ($cnn===NULL) {	
			DBParam::getconnectparams($server, $userID, $password, $database);

			echo "SERVER: ".$server."<br />\n";
			echo "USERID: ".$userID."<br />\n";
			echo "PASSWORD: ".$password."<br />\n";
			echo "DATABASE: ".$database."<br />\n";
			
			$this->dbcnn = mysql_pconnect($server, $userID, $password) or die("Unable to connect to the database server");
			mysql_select_db($database,$this->dbcnn) or die("Unable to connect to the database <b>$database</b>");
		
			echo "sulod: ".$this->dbcnn;
		}
		else
			$this->dbcnn = $cnn;		
			
		return($this->dbcnn);	
	}

		function IsItemInInventory($itemid) {
		//echo "naa";
		$qry = "select count(*) from seg_hosp_inventory where bestellnum = '$itemid'";
		
		echo "<br />\nisitemsulod: ".$this->dbcnn."....".$server;
		
		$result = mysql_query($qry, $this->dbcnn);
		
		$recrow = mysql_fetch_row($result);
		//$status = ISNULL($recrow[0]) || $recrow[0] == 0 ? FALSE : TRUE;
		//$status = ($recrow[0]=== NULL) || ($recrow[0] == 0) ? FALSE : TRUE;
		if ($recrow[0]==0) {
				$status = FALSE;
		}else{
				$status = TRUE;		
		}
		echo "status: ".$status;
		return($status);
	}
	
	function GetItmCost($itemid) {
		//print "sulod";
		$qry = "select avgcost from seg_hosp_inventory where bestellnum = '$itemid'";
		$result = mysql_query($qry, $this->dbcnn);
		$recrow = mysql_fetch_row($result);
		//if (!ISNULL($recrow[0]))
		if (($recrow[0])!=NULL)
			return($recrow[0]);
		else
			return(0);
	}
	
	function GetItmQty($itemid) {
		//print "try";
		$qry = "select qty from seg_hosp_inventory where bestellnum = '$itemid'";
		$result = mysql_query($qry, $this->dbcnn);
		$recrow = mysql_fetch_row($result);
		//if (!ISNULL($recrow[0]))
		if (($recrow[0])!=NULL)
			return($recrow[0]);
		else
			return(0);
	}

	function AddItem($itemid, $itemqty, $ucost) {
	  //echo "add"; 
		$this->DBconnect();
		if ($this->IsItemInInventory($itemid)) {
			if ($ucost > 0) {
				$nQty = abs($this->GetItmQty($itemid));		
				$nTotal = $this->GetItmCost($itemid) * $nQty;
				$nTotal = $nTotal + ($itemqty * $ucost);
				$qry = "update seg_hosp_inventory set 
						qty = qty + $itemqty";
						
				echo "ucost>0";		
			}
			
		 else  { 
			$qry = "insert into seg_hosp_inventory (bestellnum, qty, avgcost, areaid)
					values ('$itemid', $itemqty, $ucost, $areaid)";
					echo "ucost<0";		
			}		
 		}
		
 		//echo "dbcnn: ".$this->dbcnn;
		$result = mysql_query($qry, $this->dbcnn);
		$status = $result <> 0 ? TRUE : FALSE;			
		echo "<br />\n";
		echo "sulodadd".$this->dbcnn;
		echo "<br />\n";
		return($status);			
	}
	//------ added code ------
 
  function GetItemID($itemid) {
 		$this->itemid = $itemid;
		return ($this->itemid);	
  }
 
  function GetItemQty($itemqty){
 		$this->itemqty = $itemqty;
 		return ($this->itemqty);
  }
	
	function GetAreaID($areaid){
		$this->areaid = $areaid;
		return ($this->areaid);
	}
 
  function GetCost($itemid) {
 		if ($itemid != "") {
				$this->itemid = $itemid;
		}
		$getcost = GetItmCost($this->itemid);
		return ($getcost);
  }
 
	function GetQty($itemid){
			if ($itemid != "") {
					$this->itemid = $itemid;
			}
			$getqty = GetItmQty($this->itemid);
			return ($getqty);
	} 

	function RemoveItem($itemid, $itemqty){
		//echo "remove";
		if ($this->GetItmQty($itemqty) == $this->itemqty) {
						$qry = "DELETE seg_hosp_inventory WHERE bestellnum = '$nItemID'";
				} else {
						if (IsItemInInventory($itemid)) {
								$qry = "UPDATE seg_hosp_inventory set 
									  		qty = qty - $nQty
												where bestellnum = '$nItemID'";
						}							
						else  
								AddItem($this->itemid, $this->itemqty);
					
		}
		
			
			$result = mysql_query($qry, $this->dbcnn);
			$status = $result <> 0 ? TRUE : FALSE;			
			return($status);			   
	}
	//------------
	
 
 }

		
?>
