<?php
/**
* @package SegHIS_api
*/

/******
*
*	Class containing all properties and methods related to an encounter's availed hospital services
*	(laboratory and radiology).
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*   @date created:  Sept. 26, 2007
*	@date updated:	Feb. 15, 2009
*
*****/	
require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');

class Service {
	var $ref_no;
	var $trans_dtetme;			// Date and time.
	var $serv_code;
	var $serv_desc;
	var $group_code;
	var $group_desc;
	var $serv_qty;
	var $serv_price;
	var $serv_provider;
	
	function getRefNo() {
		return($this->ref_no);
	}
	
	function setRefNo($refno){
		$this->ref_no = $refno;
	}
	
	function getTransDteTime() {
		return($this->trans_dtetme);
	}
	
	function setTransDteTime($trans_dte, $trans_time) {
		$this->trans_dtetme = strftime("%Y-%m-%d", strtotime($trans_dte)). ' '.strftime("%H:%M:%S",  strtotime($trans_time));
	}	
	
	function getServiceCode() {
		return($this->serv_code);
	}	

	function setServiceCode($sServCode) {
		$this->serv_code = $sServCode;
	}
	
	function getServiceDesc() {
		return($this->serv_desc);
	}	

	function setServiceDesc($sServDesc) {
		$this->serv_desc = $sServDesc;
	}	
	
	function getGroupCode() {
		return($this->group_code);
	}	

	function setGroupCode($sGroupCode) {
		$this->group_code = $sGroupCode;
	}
	
	function getGroupDesc() {
		return($this->group_desc);
	}
	
	function setGroupDesc($sGroupDesc) {
		$this->group_desc = $sGroupDesc;
	}		
	
	function getServQty() {
		return($this->serv_qty);
	}
	
	function setServQty($nQty) {
		$this->serv_qty = $nQty;
	}
	
	function getServPrice() {
		return($this->serv_price);
	}
	
	function setServPrice($nPrice) {
		$this->serv_price = $nPrice;
	}		
	
	function getServProvider() {
		return($this->serv_provider);
	}
	
	function setServProvider($sProvider) {
		$this->serv_provider = $sProvider;
	}
}

class PerServiceCoverage extends Core {	
	var $bill_dte;
	var $current_enr;	
    var $prev_encounter_nr = '';
	var $serv_code;
	var $serv_desc;
	var $group_code;
	var $group_desc;
	var $serv_qty;
	var $serv_price;
	var $serv_provider;	
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
	var $seg_hcare_table = 'seg_hcare_srvops';		
	
	function setBillDte($b_dte) {
		$this->bill_dte = $b_dte;
	}
	 
	function setCurrentEncounterNr($enr) {
		$this->current_enr = $enr;
	}	
    
    function setPrevEncounterNr($enr) {
        $this->prev_encounter_nr = $enr;
    }   
	
	function setServiceCode($srvcode){
		$this->serv_code = $srvcode;
	}
	function getServiceCode() {
		return($this->serv_code);
	}
	
	function setServiceDesc($srvdesc) {
		$this->serv_desc = $srvdesc;
	}
	function getServiceDesc() {
		return($this->serv_desc);
	}
	
	function setGroupCode($grpcode) {
		$this->group_code = $grpcode;
	}
	function getGroupCode() {
		return($this->group_code);
	}
	
	function setGroupDesc($grpdesc) {
		$this->group_desc = $grpdesc;
	}
	function getGroupDesc() {
		return($this->group_desc);
	}	
		
	function getServQty() {
		return($this->serv_qty);
	}	
	function setServQty($nQty) {
		$this->serv_qty = $nQty;
	}
	
	function getServPrice() {
		return($this->serv_price);
	}	
	function setServPrice($nPrice) {
		$this->serv_price = $nPrice;
	}	
	
	function getServCharge() {
		return($this->serv_price * $this->serv_qty);
	}	
	
	function getServProvider() {
		return($this->serv_provider);
	}	
	function setServProvider($sProvider) {
		$this->serv_provider = $sProvider;
	}	
	
	function getTotalCoverage() {
		return($this->total_coverage);
	}			
	function getTotalLimitedCoverage() {
		return($this->total_limited_coverage);
	}		
	
	function getHCareSkedPerService($sbsked_id, $nsrvop_code) {
		global $db;
		
/*		$strSQL = "select * from ". $this->seg_hcare_table . " ".
				  "   where hcare_id   = ". $nhcare_id ." and ".
				  "         benefit_id = ". $nbenefit_id ." and ".
				  "         code = '".$nsrvop_code . "' and " .
				  "         provider = '". $this->serv_provider ."'";*/
				  
		$strSQL = "select * from ". $this->seg_hcare_table . " ".
				  "   where bsked_id = ". $sbsked_id ." and ".
				  "         code     = '".$nsrvop_code . "' and " .
				  "         provider = '". $this->serv_provider ."'";
				  
		if ($result = $db->Execute($strSQL)) {				
			if ($result->RecordCount()) {		
				$this->skedvalues = array();
			
				while ($row = $result->FetchRow()) {
					$this->skedvalues['amountlimit'] = $row['amountlimit'];
//					$this->skedvalues['maxRVU']      = $row['maxRVU'];
				}
			}
		}								 		
	}		
		
	function computeTotalCoverage($n_drate = 0){
		global $db;
		
		$totalCoverage = 0;	
        $filter = '';
				
		// Get all available health insurances with benefits categorized in billable area 'HS' based on product ...										
/*		$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis ".
				  "   from ((care_insurance_firm as ci inner join ".
				  "         seg_hcare_bsked as bs on ci.hcare_id = bs.hcare_id) ".
				  "            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
				  "            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
				  "   where hb.bill_area = 'HS' and (bs.basis & 8) and si.encounter_nr = '". $this->current_enr. "' ".
				  "      and exists (select * from " . $this->seg_hcare_table . " as sp ".
				  "                 where sp.hcare_id = ci.hcare_id and ".
				  "                    sp.benefit_id = hb.benefit_id and ".
				  "                    sp.code = '". $this->serv_code. "' and ".
				  "                    sp.provider = '". $this->serv_provider ."') ".
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
				  "   where hb.bill_area = 'HS' and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
				  "      and exists (select * from " . $this->seg_hcare_table . " as sp ".
				  "                 where sp.bsked_id = bs.bsked_id and ".
				  "                    sp.code = '". $this->serv_code. "' and ".
				  "                    sp.provider = '". $this->serv_provider ."') ".
				  "   order by priority";				  
		
		if ($result = $db->Execute($strSQL)) {
			$this->available_hplans        = array();
			$this->available_limitedhplans = array();
            
            if ($result->RecordCount()) {                                    				
				// Take note of the charge for this service .... 
				$nCharge = ($this->serv_price * $this->serv_qty) * (1 - $n_drate);
				
				while ($row = $result->FetchRow()) {				
					$nhcare_id   = $row['hcare_id'];		// Insurance id
					$nbenefit_id = $row['benefit_id'];		// Health benefit id
					$sbsked_id   = $row['bsked_id'];
					
					$this->getHCareSkedPerService($sbsked_id, $this->serv_code);
					
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
} //end class PerServiceCoverage
?>