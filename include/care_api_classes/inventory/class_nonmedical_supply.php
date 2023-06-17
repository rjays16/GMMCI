<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_item.php');
  
class NonmedicalSupply extends Item {
     #created by Bryan
    /*
    * assigns nonmedical supply type (NS) for item
    */
    var $item_type = "NS";
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


