<?php

require('./roots.php'); 
include_once($root_path."include/care_api_classes/inventory/class_inventory.php"); 
require($root_path.'include/inc_environment_global.php');

$pogz = new Inventory();
$bool;$bool1;$bool2;$bool3;$bool4;

$pogz->item_code='000000000036';
$pogz->location_nr=1;
$pogz->serial_detail_array = array('2008',10.05,'2008-05-20',107);
/*
$bool=$pogz->addInventory(5,0,'2006-12-12',NULL);
$bool1=$pogz->addInventory(1,0,'2006-12-12',NULL);
$bool2=$pogz->addInventory(2,0,'2006-12-12',NULL);
$bool3=$pogz->addInventory(3,0,'2006-12-12',NULL);
*/
#/*
$bool=$pogz->remInventory(5,0,'2006-12-12',NULL);
$bool1=$pogz->remInventory(1,0,'2006-12-12',NULL);
$bool2=$pogz->remInventory(2,0,'2006-12-12',NULL);
$bool3=$pogz->remInventory(3,0,'2006-12-12',NULL);
#*/
if($bool){
    echo "pass";
}
else{
    echo "fail";
}
if($bool1){
    echo "pass";
}
else{
    echo "fail";
}
if($bool2){
    echo "pass";
}
else{
    echo "fail";
}
if($bool3){
    echo "pass";
}
else{
    echo "fail";
}

?>
