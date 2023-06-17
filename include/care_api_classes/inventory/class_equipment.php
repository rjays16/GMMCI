<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_item.php');  
  
class Equipment extends Item {
     #created by Bryan
    /*
    * assigns equipment type (E) for item
    */
    var $item_type = "E";
    /*
    *
    * @var String
    */
    var $serial_no;
    /*
    *
    * @var Iventory[]
    */
    var $inventory;
    
    
    function getInventory(){
        /*
        * must return Inventory[]
        */
        return $this->inventory;
    }
}
?>
