<?php
class HCareCoverage {
	var $hcare_id;
    var $firm_id;
	var $hcare_desc;
	var $hcare_coverage;
	var $hcare_days_covered = 0;
    var $hcare_amountlimit = 0;
	
	function getID() {
		return($this->hcare_id);
	}
    
    function getFirmID() {
        return($this->firm_id);
    }
	
	function getDesc() {
		return($this->hcare_desc);
	}
	
	function getCoverage() {
		return($this->hcare_coverage);
	}
	
	function getDaysCovered() {
		return($this->hcare_days_covered);
	}
    
    function getAmountLimit() {
        return($this->hcare_amountlimit);
    }    
	
	function setID($sID) {
		$this->hcare_id = $sID;
	}
    
    function setFirmID($sFirmID) {
        $this->firm_id = $sFirmID;
    }
	
	function setDesc($sDesc) {
		$this->hcare_desc = $sDesc;
	}
	
	function setCoverage($nCoverage) {
		$this->hcare_coverage = $nCoverage;
	}
	
	function setDaysCovered($ndays) {
		$this->hcare_days_covered = $ndays;
	}
    
    function setAmountLimit($nlimit) {
        $this->hcare_amountlimit = $nlimit;
    }
}
?>
