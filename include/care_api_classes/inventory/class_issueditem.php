<?php

class IssuedItem extends Core{
    /*
    * @var String
    */
    var $refno;
    /*
    * @var Item
    */
    var $item;
    /*
    * @var Double
    */
    var $item_qty;
    /*
    * @var Unit
    */
    var $item_unit;
    /*
    * @var RequestServed[]
    */
    var $requests_served = array();
    
    #functions
    #parameter (String)
    function getItemInfo($item_code){
        return $this->item;
    }
    
    function getItemInfo($unit_id) {
        return $this->item_unit;
    }
    
    function applyIssueQty() {
        #should return RequestServed[]
    }
}
?>
