<?php
class PFClaim {
	var $role_area;
	var $dr_nr;
	var $dr_charge = 0;
	var $dr_claim  = 0;

	function setRoleArea($area) {
			$this->role_area = $area;
	}
	function setDrNr($value) {
		$this->dr_nr = $value;
	}
	function setDrCharge($value) {
		$this->dr_charge = $value;
	}
	function setDrClaim($value) {
		$this->dr_claim = $value;
	}

	function getRoleArea() {
			return($this->role_area);
	}
	function getDrNr() {
		return($this->dr_nr);
	}
	function getDrCharge() {
		return($this->dr_charge);
	}
	function getDrClaim() {
		return($this->dr_claim);
	}
}

class PFClaimPerHCare extends PFClaim {
	var $hcare_id;
	var $hcare_desc;

	function getID() {
		return($this->hcare_id);
	}
	function getDesc() {
		return($this->hcare_desc);
	}
	function setID($sID) {
		$this->hcare_id = $sID;
	}
	function setDesc($sDesc) {
		$this->hcare_desc = $sDesc;
	}
}
?>
