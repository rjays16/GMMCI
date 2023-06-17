<?php
class Payment {
	var $or_no;
	var $or_date;
	var $amount_paid;
	
	function getORNo() {
		return($this->or_no);
	}
	
	function setORNo($s_orno) {
		$this->or_no = $s_orno;
	}
	
	function getORDate() {
		return($this->or_date);
	}
	
	function setORDate($d_ordate) {
		$this->or_date = $d_ordate;
	}
	
	function getAmountPaid() {
		return($this->amount_paid);
	}

	function setAmountPaid($n_amntpaid) {
		$this->amount_paid = $n_amntpaid;
	}	
}
?>
