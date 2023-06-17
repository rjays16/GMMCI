<?php
/**
* @package SegHIS_api
*/

/******
*
*	Class containing all properties and methods related to discounts applied in billing.
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*   @date created:  June 18, 2008
*	@date updated:	Dec. 4, 2012
*
*****/	
class BillingDiscount {	
	/**
	 * @var string
	 */
	var $discountid;
	/**
	 *@var string
	 */
	var $discountdesc;
	/**
	 * @var decimal (10,4)
	 */
	var $discount;
    var $discount_amnt;
	
	function getDiscountID() {
		return($this->discountid);
	}
	function setDiscountID($id){
		$this->discountid = $id;
	}
	
	function getDiscountDesc() {
		return($this->discountdesc);
	}	
	function setDiscountDesc($desc){		
		$this->discountdesc = $desc; 
	}	
	
	function getDiscountRate() {
		return($this->discount);
	}
	function setDiscountRate($rate){
		$this->discount = $rate; 
    }
    
    function getDiscountAmount() {
        return $this->discount_amnt;
    }
    function setDiscountAmount($amnt) {
        $this->discount_amnt = (is_null($amnt) ? 0 : $amnt);
    }
}
