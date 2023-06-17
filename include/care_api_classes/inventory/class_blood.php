<?php
  
require("./roots.php");
require_once($root_path.'include/care_api_classes/class_item.php');  
  
class Blood extends Item {
    #created by Bryan
    /*
    * assigns blood type (B) for item
    */
    var $item_type = "B";
    /*
    *
    * @var String
    */
    var $blood_type;
    /*
    *
    * @var Date
    */
    var $expiry_date;
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
