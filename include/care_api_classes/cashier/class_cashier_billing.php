<?php

// Helper class for Cashier module
// Created by: AJMQ

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCashierBilling extends Core {

	function SegCashierBilling() {
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $pid
	* @param mixed $orno
	* @param mixed $offset
	* @param mixed $rowcount
	* @return ADODB
	*/
	function GetBillingPayables($pid, $orno=FALSE, $offset=0, $rowcount=15) {
		global $db;
		$pid = $db->qstr($pid);
		if ($orno) {
			$orno = $db->qstr($orno);
		}
		$this->sql = 
			"SELECT\n".
				"g.grant_no,p.pid,p.name_last,name_middle,name_first, be.bill_nr,be.bill_frmdte,be.bill_dte,\n".
				"be.encounter_nr,SUM(gd.amount) AS amount,pr.or_no AS is_paid\n".
			"FROM seg_grant_entry_details AS gd\n".
				"INNER JOIN seg_grant_entries AS g ON g.grant_no=gd.grant_no\n".
				"INNER JOIN seg_billing_encounter AS be ON g.ref_source='FB' AND g.ref_no=be.bill_nr\n".
				"INNER JOIN care_encounter AS e ON e.encounter_nr=be.encounter_nr\n".
				"INNER JOIN care_person AS p ON p.pid=e.pid\n".
				"LEFT JOIN seg_grant_accounts AS ga ON ga.account_id=g.account_id\n".
				"LEFT JOIN seg_pay_request AS pr ON pr.ref_source='FB' AND pr.service_code=g.grant_no\n".
			"WHERE g.ref_source='FB' AND p.pid=$pid AND ga.prototype='payable'\n".
			"GROUP BY g.ref_no,g.ref_source,g.account_id\n".
			"ORDER BY be.bill_frmdte DESC";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	
	function GetGrantEntryDetailsByGrantNo($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = 
			"SELECT t.type_id,t.name_long,SUM(ge.amount) AS amount\n".
			"FROM seg_grant_entry_details AS ge\n".
			"LEFT JOIN seg_cashier_account_types AS t ON t.type_id=ge.service_code\n".
			"WHERE ge.grant_no=$nr\n".
			"GROUP BY ge.service_code";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

}

?>