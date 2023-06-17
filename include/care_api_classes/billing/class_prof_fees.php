<?php
/**
* @package SegHIS_api
*/

/******
*
*	Class containing all properties and methods related to an encounter's doctors' fees.
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*   @date created:  Oct. 9, 2007
*	@date updated:	Oct. 11, 2008
*
*****/
require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');

class ProfFee {
	var $dr_nr;			// System number of doctor.
	var $dr_last;		// last, first and middle name of doctor.
	var $dr_first;
	var $dr_mid;
	var $role_nr;
	var $role_desc;
	var $role_area;
	var $role_level;
	var $days_attend;
	var $dr_dailyrate;
	var $dr_charge;
	var $ops_rvu;
	var $ops_multiplier;
	var $is_excluded = false;
	var $chrg_for_coverage;
	var $opcodes;

	function setDrNr($nr) {
		$this->dr_nr = $nr;
	}
	function getDrNr() {
		return($this->dr_nr);
	}

	function setDrLast($last) {
		$this->dr_last = $last;
	}
	function getDrLast() {
		return($this->dr_last);
	}

	function setDrFirst($first) {
		$this->dr_first = $first;
	}
	function getDrFirst() {
		return($this->dr_first);
	}

	function setDrMid($middle) {
		$this->dr_mid = $middle;
	}
	function getDrMid() {
		return($this->dr_mid);
	}

	function setRoleNo($r_nr) {
			$this->role_nr = $r_nr;
	}

	function getRoleNo() {
			return($this->role_nr);
	}

	function setRoleDesc($s_role) {
		$this->role_desc = $s_role;
	}
	function getRoleDesc() {
		return($this->role_desc);
	}

	function setRoleBenefit($s_benefit) {
		$this->role_area = $s_benefit;
	}
	function getRoleBenefit() {
		return($this->role_area);
	}

	function setRoleLevel($nlevel) {
		$this->role_level = $nlevel;
	}
	function getRoleLevel() {
		return $this->role_level;
	}

	function setDaysAttended($n) {
		$this->days_attend = $n;
	}
	function getDaysAttended() {
		return($this->days_attend);
	}

	function setDrDailyRate($n) {
		$this->dr_dailyrate = $n;
	}
	function getDrDailyRate() {
		return($this->dr_dailyrate);
	}

	function setDrCharge($n) {
		$this->dr_charge = $n;
	}
	function getDrCharge() {
		return($this->dr_charge);
	}

	function setRVU($n) {
		$this->ops_rvu = $n;
	}
	function getRVU() {
		return($this->ops_rvu);
	}

	function setMultiplier($n) {
		$this->ops_multiplier = $n;
	}
	function getMultiplier() {
		return($this->ops_multiplier);
	}

		function setChrgForCoverage($n) {
				$this->chrg_for_coverage = $n;
		}
		function getChrgForCoverage() {
				return($this->chrg_for_coverage);
		}

		function setIsExcludedFlag($flag) {
				$this->is_excluded = $flag;
		}
		function getIsExcludedFlag() {
				return($this->is_excluded);
		}

		function setOpCodes($codes) {
			$this->opcodes = $codes;
		}
		function getOpCodes() {
			return($this->opcodes);
		}

		function setTblSource($source){ //added by mai 08-26-2014
			$this->tbl_source = $source;
		}
}

class ProfFeeCoverage extends Core {
	var $dr_nr;
	var $role_area;
	var $role_level;
	var $role_desc;
	var $total_days;
	var $total_rvu;
	var $avg_multiplier;
	var $tot_charge;
	var $chrg_for_coverage;
	var $opcodes;
	var $tbl_source; //added by mai 08-26-2014

//	var $current_enr;
//	var $available_hplans;
//	var $available_limitedhplans;
//	var $skedvalues;

	/**
	 * @var decimal
	 */
	var $total_coverage = 0;

//	function setCurrentEncounterNr($enr) {
//		$this->current_enr = $enr;
//	}

	function setDrNr($drnr) {
		$this->dr_nr = $drnr;
	}
	function getDrNr() {
		return $this->dr_nr;
	}

	function setRoleBenefit($s_benefit) {
		$this->role_area = $s_benefit;
	}
		function setRoleLevel($level) {
				$this->role_level = $level;
		}
	function getRoleBenefit() {
		return($this->role_area);
	}
		function getRoleLevel() {
				return($this->role_level);
		}

//	function setRoleDesc($s_role) {
//		$this->role_desc = $s_role;
//	}
	function getRoleDesc() {
		global $db;

		$r_desc = "";

		$strSQL = "select benefit_desc from seg_hcare_benefits where bill_area = '". $this->role_area ."'";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$r_desc = $row['benefit_desc'];
				}
			}
		}

		$this->role_desc = $r_desc;

		return($r_desc);
	}

	function setDaysAttended($n) {
		$this->total_days = $n;
	}
	function getDaysAttended() {
		return($this->total_days);
	}

	function setDrCharge($n) {
		$this->tot_charge = $n;
	}
	function getDrCharge() {
		return($this->tot_charge);
	}

	function setRVU($n) {
		$this->total_rvu = $n;
	}
	function getRVU() {
		return($this->total_rvu);
	}

	function setMultiplier($n) {
		$this->avg_multiplier = $n;
	}
	function getMultiplier() {
		return($this->avg_multiplier);
	}

	function setTotalCoverage($ntotal) {
		$this->total_coverage = $ntotal;
	}
	function getTotalCoverage() {
		return($this->total_coverage);
	}

	function setChrgForCoverage($nCharge) {
			$this->chrg_for_coverage = $nCharge;
	}
	function getChrgForCoverage() {
			return($this->chrg_for_coverage);
	}

	function setOpCodes($codes) {
		$this->opcodes = $codes;
	}
	function getOpCodes() {
		return($this->opcodes);
	}

	function setTblSource($source){ //added by mai 08-26-2014
		$this->tbl_source = $source;
	}
}
?>