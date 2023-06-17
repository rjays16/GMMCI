<?php
/**
* @package SegHIS_api
*/

/******
*
*	Class containing all properties and methods related to an encounter's availed operations.
*   (OR procedures).
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*   @date created:  Oct. 3, 2007
*	@date updated:	Feb. 15, 2009
*
*****/
require_once('roots.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');

class Operation {
	var $ref_no;
	var $trans_dtetme;			// Date and time.
	var $op_code;
	var $op_desc;
	var $op_rvu;
	var $op_multiplier;
	var $op_charge;
	var $op_provider;

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

	function getOpCode() {
		return($this->op_code);
	}

	function setOpCode($sOpCode) {
		$this->op_code = $sOpCode;
	}

	function getOpDesc() {
		return($this->op_desc);
	}

	function setOpDesc($sOpDesc) {
		$this->op_desc = $sOpDesc;
	}

	function getOpRVU() {
		return($this->op_rvu);
	}

	function setOpRVU($nRVU) {
		$this->op_rvu = $nRVU;
	}

	function getOpMultiplier() {
		return($this->op_multiplier);
	}

	function setOpMultiplier($nMultiplier) {
		$this->op_multiplier = $nMultiplier;
	}

	function getOpCharge() {
		return($this->op_charge);
	}

	function setOpCharge($ncharge) {
		$this->op_charge = $ncharge;
	}

	function getOpProvider() {
		return($this->op_provider);
	}

	function setOpProvider($sProvider) {
		$this->op_provider = $sProvider;
	}
}

class PerOpCoverage {
	var $bill_dte;
	var $current_enr;
	var $prev_encounter_nr = '';
	var $op_code;
	var $performed_opcode;
	var $op_desc;
	var $op_rvu;
	var $op_multiplier;
	var $op_charge;
	var $op_provider;
	var $available_hplans;
	var $available_limitedhplans;
	var $skedvalues;

	/**
	 * @var decimal
	 */
	var $total_coverage         = 0;
	var $total_limited_coverage = 0;
	/**
	 * Database table for healthcare services and procedures
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

	function setOpCode($opcode){
		$this->op_code = $opcode;
	}
	function getOpCode() {
		return($this->op_code);
	}

	function setOpCodePerformed($opcode) {
		$this->performed_opcode = $opcode;
	}
	function getOpCodePerformed() {
		return $this->performed_opcode;
	}

	function setOpDesc($opdesc) {
		$this->op_desc = $opdesc;
	}
	function getOpDesc() {
		return($this->op_desc);
	}

	function getOpRVU() {
		return($this->op_rvu);
	}
	function setOpRVU($nRVU) {
		$this->op_rvu = $nRVU;
	}

	function getOpMultiplier() {
		return($this->op_multiplier);
	}
	function setOpMultiplier($nMultiplier) {
		$this->op_multiplier = $nMultiplier;
	}

	function getOpCharge() {
		return($this->op_charge);
	}

	function setOpCharge($ncharge) {
		$this->op_charge = $ncharge;
	}

	function getOpProvider() {
		return($this->op_provider);
	}
	function setOpProvider($sProvider) {
		$this->op_provider = $sProvider;
	}

	function getTotalCoverage() {
		return($this->total_coverage);
	}
	function getTotalLimitedCoverage() {
		return($this->total_limited_coverage);
	}

	function getHCareSkedPerOp($nbsked_id, $nsrvop_code) {
		global $db;

/*		$strSQL = "select * from ". $this->seg_hcare_table . " ".
					"   where hcare_id   = ". $nhcare_id ." and ".
					"         benefit_id = ". $nbenefit_id ." and ".
					"         code = '".$nsrvop_code . "' and " .
					"         provider = '". $this->op_provider ."'";*/

		$strSQL = "select * from ". $this->seg_hcare_table . " ".
					"   where bsked_id = ". $nbsked_id ." and ".
					"         code     = '".$nsrvop_code . "' and " .
					"         provider = '". $this->op_provider ."'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$this->skedvalues = array();

				while ($row = $result->FetchRow()) {
					$this->skedvalues['amountlimit'] = $row['amountlimit'];
					$this->skedvalues['maxRVU']      = $row['maxRVU'];
				}
			}
		}
	}

	function computeTotalCoverage($n_drate = 0){
		global $db;

		$totalCoverage = 0;
				$filter = '';

		// Get all available health insurances with benefits categorized in billable area 'OR' based on product ...
/*		$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis ".
					"   from ((care_insurance_firm as ci inner join ".
					"         seg_hcare_bsked as bs on ci.hcare_id = bs.hcare_id) ".
					"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
					"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
					"   where hb.bill_area = 'OR' and (bs.basis & 8) and si.encounter_nr = '". $this->current_enr. "' ".
					"      and exists (select * from " . $this->seg_hcare_table . " as sp ".
					"                 where sp.hcare_id = ci.hcare_id and ".
					"                    sp.benefit_id = hb.benefit_id and ".
					"                    sp.code = '". $this->op_code. "' and ".
					"                    sp.provider = '". $this->op_provider ."') ".
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
					"   where hb.bill_area = 'OR' and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
					"      and exists (select * from " . $this->seg_hcare_table . " as sp ".
					"                 where sp.bsked_id = bs.bsked_id and ".
					"                    sp.code = '". $this->op_code. "' and ".
					"                    sp.provider = '". $this->op_provider ."') ".
					"   order by priority";

				$this->available_hplans        = array();
				$this->available_limitedhplans = array();

		if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
				// Take note of the charge for this procedure ....
				while ($row = $result->FetchRow()) {
					$nhcare_id   = $row['hcare_id'];		// Insurance id
					$nbenefit_id = $row['benefit_id'];		// Health benefit id
					$nbsked_id   = $row['bsked_id'];		// Benefit schedule id

					$this->getHCareSkedPerOp($nbsked_id, $this->op_code);

					// Compute actual charge.
					$nRVU = $this->op_rvu;
					if (($nRVU > $this->skedvalues['maxRVU']) && ($this->skedvalues['maxRVU'] > 0))
						$nRVU = $this->skedvalues['maxRVU'];

					$nCharge = ($nRVU * $this->op_multiplier) * (1 - $n_drate);

					if (($nCharge > $this->skedvalues['amountlimit']) && ($this->skedvalues['amountlimit'] > 0))
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
} //end class PerOpCoverage
?>