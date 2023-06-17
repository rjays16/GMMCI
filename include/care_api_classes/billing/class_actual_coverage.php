<?php
class HCareActualCoverage {
	var $hcare_id;
	var $hcare_desc;
		var $firm_id;
	var $acc_coverage = 0;
	var $med_coverage = 0;
	var $sup_coverage = 0;
	var $srv_coverage = 0;
	var $ops_coverage = 0;
	var $d1_coverage = 0;
	var $d2_coverage = 0;
	var $d3_coverage = 0;
	var $d4_coverage = 0;
	var $msc_coverage = 0;

	var $days_covered = 0;

	function getID() {
		return($this->hcare_id);
	}
	function getDesc() {
		return($this->hcare_desc);
	}
		function getFirmID() {
				return($this->firm_id);
		}
	function getAccCoverage() {
		return($this->acc_coverage);
	}
	function getMedCoverage() {
		return($this->med_coverage);
	}
	function getSupCoverage() {
		return($this->sup_coverage);
	}
	function getSrvCoverage() {
		return($this->srv_coverage);
	}
	function getOpsCoverage() {
		return($this->ops_coverage);
	}
	function getD1Coverage() {
		return($this->d1_coverage);
	}
	function getD2Coverage() {
		return($this->d2_coverage);
	}
	function getD3Coverage() {
		return($this->d3_coverage);
	}
	function getD4Coverage() {
		return($this->d4_coverage);
	}
	function getMscCoverage() {
		return($this->msc_coverage);
	}

	function getDaysCovered() {
		return($this->days_covered);
	}

	function setID($sID) {
		$this->hcare_id = $sID;
	}
	function setDesc($sDesc) {
		$this->hcare_desc = $sDesc;
	}
		function setFirmID($sFirmID) {
				$this->firm_id = $sFirmID;
		}
	function setAccCoverage($nCoverage) {
		$this->acc_coverage = (is_null($nCoverage) || $nCoverage == '') ? 0 : $nCoverage;
	}
	function setMedCoverage($nCoverage) {
		$this->med_coverage = (is_null($nCoverage) || $nCoverage == '') ? 0 : $nCoverage;
	}
	function setSupCoverage($nCoverage) {
		$this->sup_coverage = $nCoverage;
	}
	function setSrvCoverage($nCoverage) {
		$this->srv_coverage = (is_null($nCoverage) || $nCoverage == '') ? 0 : $nCoverage;
	}
	function setOpsCoverage($nCoverage) {
		$this->ops_coverage = (is_null($nCoverage) || $nCoverage == '') ? 0 : $nCoverage;
	}
	function setD1Coverage($nCoverage) {
		$this->d1_coverage = $nCoverage;
	}
	function setD2Coverage($nCoverage) {
		$this->d2_coverage = $nCoverage;
	}
	function setD3Coverage($nCoverage) {
		$this->d3_coverage = $nCoverage;
	}
	function setD4Coverage($nCoverage) {
		$this->d4_coverage = $nCoverage;
	}
	function setMscCoverage($nCoverage) {
		$this->msc_coverage = $nCoverage;
	}

	function setDaysCovered($ndays) {
		$this->days_covered = $ndays;
	}
}
?>