<?php
/**
* @package SegHIS_api
*/

/******
*
*	Class containing all properties and methods related to an encounter's supplies.
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*   @date created:  Sept. 5, 2007
*	@date updated:	Feb. 15, 2009
*
*****/	
require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');

class Supply {	
	/**
	 * @var string
	 */
	var $ref_no;
	/**
	 *@var date 
	 */
	var $trans_dtetme;
	/**
	 * @var string
	 */
	var $department;
	var $bestellnum;
	/**
	 * @var string
	 */	
	var $artikelname;
	/**
	 * @var decimal
	 */
	var $item_qty;
	/**
	 * @var decimal
	 */
	var $item_price;
	
	function getRefNo() {
		return($this->ref_no);
	}
	function setRefNo($refno){
		$this->ref_no = $refno;
	}
	
	function getTransDte() {
		return($this->trans_dtetime);
	}
	function setTransDte($transDate){		
		$this->trans_dtetime = $strfDate; 
	}
	
	function getDept() {
		return($this->department);
	}
	function setDept($sDept) {
		$this->department = $sDept;
	}
	
	function getBestellNum() {
		return($this->bestellnum);
	}
	function setBestellNum($bestellNum){
		$this->bestellnum = $bestellNum;
	}
	
	function getArtikelName() {
		return($this->artikelname);
	}
	function setArtikelName($artikelName){
		$this->artikelname = $artikelName;
	}
	
	function getItemQty() {
		return($this->item_qty);
	}
	function setItemQty($itemQty){
		$this->item_qty  = $itemQty;
	}
	
	function getItemPrice() {
		return($this->item_price);
	}
	function setItemPrice($itemPrice){
		$this->item_price = $itemPrice; 
	}		
}

class PerSupplyCoverage extends Core {	
	var $bill_dte;
	var $current_enr;
    var $prev_encounter_nr = ''; 
	var $bestellnum;
	var $artikelname;
	var $item_qty;
	var $item_price;
	var $item_charge;
	var $available_hplans;
	var $available_limitedhplans;
	var $skedvalues;

	/**
	 * @var decimal
	 */
	var $total_coverage         = 0;
	var $total_limited_coverage = 0;	
	/**
	 * Database table for healthcare products / medicine
	 * @var string
	 */	
	var $seg_hcare_product = 'seg_hcare_products';		
	
	function setBillDte($b_dte) {
		$this->bill_dte = $b_dte;
	}	
	 
	function setCurrentEncounterNr($enr) {
		$this->current_enr = $enr;
	}	 		
    
    function setPrevEncounterNr($enr) {
        $this->prev_encounter_nr = $enr;
    }    
	
	function setBestellNum($bestellNum){
		$this->bestellnum = $bestellNum;
	}
	
	function setArtikelName($artikelName){
		$this->artikelname = $artikelName;
	}		
	
	function getItemQty() {
		return($this->item_qty);
	}
	function setItemQty($itemQty){
		$this->item_qty  = $itemQty;
	}
	
	function getItemPrice() {
		return($this->item_price);
	}
	function setItemPrice($itemPrice){
		$this->item_price = $itemPrice; 
	}		
	
	function setItemCharge($itemCharge){
		$this->item_charge = $itemCharge; 
	}			
	function getItemCharge() {
		return($this->item_charge);
	}	
		
	function getTotalCoverage() {
		return($this->total_coverage);
	}			
	function getTotalLimitedCoverage() {
		return($this->total_limited_coverage);
	}	
	
	function getHCareSkedPerProduct($nbsked_id, $nproduct_id) {
		global $db;
		
/*		$strSQL = "select * from ". $this->seg_hcare_product. " ".
				  "   where hcare_id   = $nhcare_id and ".
				  "         benefit_id = $nbenefit_id and ".
				  "         bestellnum = '$nproduct_id'";*/
				  
		$strSQL = "select * from ". $this->seg_hcare_product. " ".
				  "   where bsked_id   = $nbsked_id and ".
				  "         bestellnum = '$nproduct_id'";				  
				  
		if ($result = $db->Execute($strSQL)) {				
			if ($result->RecordCount()) {		
				$this->skedvalues = array();
			
				while ($row = $result->FetchRow()) {
					$this->skedvalues['amountlimit'] = $row['amountlimit'];
				}
			}
		}								 		
	}		
		
	function computeTotalCoverage($n_drate = 0){
		global $db;
		
		$totalCoverage = 0;	
        $filter = '';	
		
		// Get all available health insurances with benefits categorized in billable area 'MS' based on product ...										
/*		$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis ".
 				  "   from ((care_insurance_firm as ci inner join ".
        		  "         seg_hcare_bsked as bs on ci.hcare_id = bs.hcare_id) ".
           		  "            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
				  "            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
   				  "   where hb.bill_area = 'MS' and (bs.basis & 8) and si.encounter_nr = '". $this->current_enr. "' ".
      			  "      and exists (select * from ". $this->seg_hcare_product ." as sp ".
                  "                 where sp.hcare_id = ci.hcare_id and ".
                  "                    sp.benefit_id = hb.benefit_id and ".
				  "                    sp.bestellnum = '". $this->bestellnum. "') ".
				  "   order by priority";*/
		
        if ($this->prev_encounter_nr != '') $filter = " or si.encounter_nr = '$this->prev_encounter_nr'";    		  
		$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
 				  "   from ((care_insurance_firm as ci inner join ".
				  "            (select * from seg_hcare_bsked as shb ".						  						  
				  "                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
				  "                   and (shb.basis & 8) ".
				  "                   and (select max(effectvty_dte) as latest ".
				  "                           from seg_hcare_bsked as shb2 ".
				  "                           where shb2.hcare_id = shb.hcare_id ".
				  "                              and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
           		  "            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
				  "            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
   				  "   where hb.bill_area = 'MS' and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
      			  "      and exists (select * from ". $this->seg_hcare_product ." as sp ".
                  "                 where sp.bsked_id = bs.bsked_id and ".
				  "                    sp.bestellnum = '". $this->bestellnum. "') ".
				  "   order by priority";				  
		
		if ($result = $db->Execute($strSQL)) {
			$this->available_hplans        = array();
			$this->available_limitedhplans = array();
            
            if ($result->RecordCount()) {     				
				// Take note of the charge for this item .... 
				$nCharge = $this->item_charge * (1 - $n_drate);
				
				while ($row = $result->FetchRow()) {				
					$nhcare_id   = $row['hcare_id'];		// Insurance id
					$nbenefit_id = $row['benefit_id'];		// Health benefit id
					$nbsked_id   = $row['bsked_id'];		// Benefit schedule id					
					
					$this->getHCareSkedPerProduct($nbsked_id, $this->bestellnum);
					
					// Compute actual charge.
					if ($nCharge > $this->skedvalues['amountlimit']) 
						$nCoverage = $this->skedvalues['amountlimit'];
					else
						$nCoverage = $nCharge;

					if ($nCoverage > 0) {											
						$objCoverage = new HCareCoverage;
						
						$objCoverage->setID($nhcare_id);
						$objCoverage->setDesc($row['name']);
						$objCoverage->setCoverage($nCoverage);
						
						if ($row['basis'] & 1) {
							$this->available_limitedhplans[] = $objCoverage;
							$this->total_limited_coverage   += $nCoverage;
						}							
						else {
							$this->available_hplans[] = $objCoverage;
							$this->total_coverage    += $nCoverage;							
						}
							
						$totalCoverage += $nCoverage;
					}
				}   // while loop								
			}  // if RecordCount() ...
		}  // if Execute() ...
		
//		$this->total_coverage = $totalCoverage;
				
		return($totalCoverage);
	} // end of function computeTotalCoverage				
} //end class PerSupplyCoverage

?>
