<?php
  
class RequestedItem extends Item {
     #created by Bryan
    /*
    * @var String
    */
    var $refno;
    /*
    *
    * @var Item
    */
    var $item;
    /*
    *
    * @var Double
    */
    var $item_qty;
    /*
    *
    * @var Integer
    */
    var $item_unit;
    /*
    *
    * @var Boolean
    */
    var $is_unit_per_pc;
    
    /*parameters (string)*/
    function getItemInfo($item_code){
        /*
        * must return Item
        */
        return $this->item;
    }
}
?>
