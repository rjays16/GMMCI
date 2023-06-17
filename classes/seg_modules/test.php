<?php 
include "seginventory.php";

 // to test
	$inventory = new SegInventory;
		
  print "ItemID: ".$inventory->GetItemID(12)."<br /> \r\n"; 
	print "ItemQty: ".$inventory->GetItemQty("1.00")."<br /> \r\n";

	$inventory->AddItem($inventory->GetItemID(12),$inventory->GetItemQty(1.00),12);

	//$inventory->DBconnect();
?>