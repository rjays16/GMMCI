<?php
  
require('./roots.php'); 
include_once($root_path."include/care_api_classes/inventory/class_inventory.php"); 
require($root_path.'include/inc_environment_global.php');

$pogz = new Inventory();
$pogz1 = new Inventory();
$pogz2 = new Inventory();
$pogz3 = new Inventory();
$pogz4 = new Inventory();
$bool;$bool1;$bool2;$bool3;$bool4;

$pogz->item_code='000000000036';
$pogz->location_nr=1;
$pogz->serial_detail_array = array('2008',10.05,'2008-05-20',107);

$bool=$pogz->addNocommit(5,1,'2006-12-12',NULL);


if($bool){
    echo "pass";
}
else{
    echo "fail";
}
  
?>
