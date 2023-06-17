<?php
/**
* @package SegHIS_api
*/

/******
*
*	Class containing all properties and methods related to an encounter's availed miscellaneous services.
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*   @date created:  June 17, 2008
*	@date updated:	Aug. 12, 2008
*
*****/	
require_once('roots.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');

class Miscellaneous {
	var $ref_no;
	var $chrge_dtetme;			// Date and time.
	var $misc_code;
	var $misc_name;
	var $misc_desc;
	var $misc_qty;
	var $misc_chrg;	
	var $misc_provider = 'OA';
	
	function getRefNo() {
		return($this->ref_no);
	}
	
	function getChrgeDteTme() {
		return($this->chrge_dtetme);
	}
	
	function getMiscCode() {
		return($this->misc_code);
	}
	
	function getMiscName() {
		return($this->misc_name);
	}
	
	function getMiscDesc() {
		return($this->misc_desc);
	}		
	
	function getMiscQty() {
		return($this->misc_qty);
	}    
	
	function getMiscChrg() {
		return($this->misc_chrg);
	}
    
    function getTotalMiscChrg() {
        return($this->misc_qty * $this->misc_chrg);
    }

// ----------------------------------

	function setRefNo($srefno) {
		$this->ref_no = $srefno;
	}
	
	function setChrgeDteTme($chrg_dtetme) {
		$this->chrge_dtetme = $chrg_dtetme;
	}
	
	function setMiscCode($scode) {
		$this->misc_code = $scode;
	}
	
	function setMiscName($sname) {
		$this->misc_name = $sname;
	}
	
	function setMiscDesc($sdesc) {
		$this->misc_desc = $sdesc;
	}
	
	function setMiscQty($nqty) {
		$this->misc_qty = $nqty;
	}	
	
	function setMiscChrg($nchrg) {
		$this->misc_chrg = $nchrg;
	}
}

class PerMscChrgCoverage {
	var $bill_dte;
	var $current_enr;
    var $prev_encounter_nr = ''; 
	var $misc_code;
	var $misc_name;
	var $misc_desc;
	var $misc_qty;	
	var $misc_chrg;
	var $misc_provider = 'OA';		
	var $available_hplans;
	var $available_limitedhplans;
	var $skedvalues;		
	
	/**
	 * @var decimal
	 */
	var $total_coverage         = 0;
	var $total_limited_coverage = 0;		
	/**
	 * Database table for covered services ...
	 * @var string
	 */	
	var $seg_hcare_table = 'seg_hcare_srvops';	
	
	function getMiscCode() {
		return($this->misc_code);
	}
	
	function getMiscName() {
		return($this->misc_name);
	}
	
	function getMiscDesc() {
		return($this->misc_desc);
	}		
	
	function getMiscQty() {
		return($this->misc_qty);
	}	
	
	function getMiscChrg() {
		return($this->misc_chrg);
	}
    
    function getTotalMiscChrg() {
        return($this->misc_qty * $this->misc_chrg);
    }    

// ----------------------------------

	function setBillDte($b_dte) {
		$this->bill_dte = $b_dte;
	}
	 
	function setCurrentEncounterNr($enr) {
		$this->current_enr = $enr;
	}
    
    function setPrevEncounterNr($enr) {
        $this->prev_encounter_nr = $enr;
    }    
		
	function setMiscCode($scode) {
		$this->misc_code = $scode;
	}
	
	function setMiscName($sname) {
		$this->misc_name = $sname;
	}
	
	function setMiscDesc($sdesc) {
		$this->misc_desc = $sdesc;
	}
	
	function setMiscQty($nqty) {
		$this->misc_qty = $nqty;
	}		
	
	function setMiscChrg($nchrg) {
		$this->misc_chrg = $nchrg;
	}

	function getTotalCoverage() {
		return($this->total_coverage);
	}			
	function getTotalLimitedCoverage() {
		return($this->total_limited_coverage);
	}
	
	function getHCareSkedPerMiscItm($sbsked_id, $nmisc_code) {
		global $db;		
				  
		$strSQL = "select * from ". $this->seg_hcare_table . " ".
				  "   where bsked_id = ". $sbsked_id ." and ".
				  "         code     = '".$nmisc_code . "' and " .
				  "         provider = '". $this->misc_provider ."'";
				  
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
				
		// Get all available health insurances with benefits categorized in billable area 'XC' based on services ...
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
				  "   where hb.bill_area = 'XC' and )(si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
				  "      and exists (select * from " . $this->seg_hcare_table . " as sp ".
				  "                 where sp.bsked_id = bs.bsked_id and ".
				  "                    sp.code = '". $this->misc_code. "' and ".
				  "                    sp.provider = '". $this->misc_provider ."') ".
				  "   order by priority";                  	  
		
        $this->available_hplans        = array();
        $this->available_limitedhplans = array();            
            
        if ($result = $db->Execute($strSQL)) {                         
			if ($result->RecordCount()) {				
				// Take note of the charge for this miscellaneous item .... 
				$nCharge = $this->misc_chrg * (1 - $n_drate);
				
				while ($row = $result->FetchRow()) {				
					$nhcare_id   = $row['hcare_id'];		// Insurance id
					$nbenefit_id = $row['benefit_id'];		// Health benefit id
					$sbsked_id   = $row['bsked_id'];
					
					$this->getHCareSkedPerMiscItm($sbsked_id, $this->misc_code);
					
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
				
		return($totalCoverage);
	} // end of function computeTotalCoverage				
}
?>