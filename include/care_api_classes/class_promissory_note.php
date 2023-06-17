<?php
/**
*Created By: Maimai
*Created On: 09/10/2014
*/

require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');

class Promissory_note extends Core{
	var $sql;
	var $result;
	var $tbl_promi = "seg_promissory_note";

	function insertPromi($due_date, $encounter_nr, $amount, $remarks, $name_g, $address_g, $rel_patient){
		global $db, $HTTP_SESSION_VARS;
		
		$refno = $this->getNewRefno();

		if($refno){
		$this->sql = "INSERT INTO ".$this->tbl_promi."(refno, encounter_nr, amount, due_date, remarks, name_guarantor, address_guarantor, relationship_patient, create_id, create_dt)".
					" VALUES (".$db->qstr($refno).", ".$db->qstr($encounter_nr).",".$db->qstr($amount)
							.",".$db->qstr($due_date).",".$db->qstr($remarks)
							.",".$db->qstr($name_g).",".$db->qstr($address_g)
							.",".$db->qstr($rel_patient)
							.",".$db->qstr($_SESSION['sess_temp_userid']).", NOW()"
							.")";
				
			if($db->Execute($this->sql)){
				return $refno;
			}
		}else{
			return false;
		}
	}

	function getNewRefno(){
		global $db;
		$this->sql = "SELECT fn_get_new_refno_promi(NOW()) as refno";
		if($this->result = $db->Execute($this->sql)){
			if( $row = $this->result->Fetchrow()){
				return $row['refno'];
			}
		}

		return false;
	}

	function updatePromi($refno, $due_date, $amount, $remarks, $name_g, $address_g, $rel_patient){
		global $db, $HTTP_SESSION_VARS;
		
		$this->sql = "UPDATE ".$this->tbl_promi
					." SET due_date = ".$db->qstr($due_date)
					.", amount = ".$db->qstr($amount)
					.", remarks = ".$db->qstr($remarks)
					.", name_guarantor = ".$db->qstr($name_g)
					.", address_guarantor = ".$db->qstr($address_g)
					.", relationship_patient = ".$db->qstr($rel_patient)
					.", modify_id = ".$db->qstr($_SESSION['sess_temp_userid'])
					.", modify_dt = NOW()"
					." WHERE refno =".$db->qstr($refno)." AND note_status <> 'deleted'";

		if($db->Execute($this->sql)){
			return true;
		}else{
			return false;
		}
	}

	function selectPromiDetails($encounter_nr){
		global $db;
		$this->sql = "SELECT 
					  spn.`refno`,
					  spn.`encounter_nr`,
					  fn_get_person_lastname_first (ce.`pid`) AS patient_name,
					  DATE(ce.`encounter_date`) AS encounter_date,
					  ce.`discharge_date`,
					  spn.amount,
					  spn.name_guarantor,
					  spn.address_guarantor,
					  spn.relationship_patient,
					  spn.due_date,
					  DATE(spn.due_date) - DATE(spn.create_dt) AS days_to_pay,
					  spn.remarks 
					FROM
					  seg_promissory_note spn 
					  LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = spn.`encounter_nr` 
					  LEFT JOIN care_person cp 
					    ON cp.pid = ce.`pid` 
					WHERE spn.`encounter_nr` =  ".$db->qstr($encounter_nr);
					
		$this->result = $db->Execute($this->sql);
		if($this->result){
			return $this->result;
		}else{
			return false;
		}

	}

	function selectBill($encounter_nr){
		global $db;

		$this->sql = "SELECT 
					  sbe.bill_nr,
					  sbe.`bill_dte`,
					  sbe.`bill_frmdte`,
					  fn_billing_compute_net_amount (sbe.bill_nr) - sbe.total_prevpayments AS total_bill 
					FROM
					  seg_billing_encounter sbe 
					WHERE (
					    sbe.encounter_nr = ".$db->qstr($encounter_nr)." 
					    AND sbe.is_final = 1 
					    AND (
					      sbe.is_deleted <> 1 
					      OR sbe.is_deleted IS NULL
					    )
					  )";

		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

	function getSummary($date_from, $date_to){
		global $db;

		$this->sql = "SELECT 
					  spn.`due_date`,
					  sbe.`bill_nr`,
					  spn.`encounter_nr`,
					  fn_get_person_lastname_first (ce.`pid`) AS pname,
					  spn.`amount`,
					 fn_billing_compute_net_amount (sbe.`bill_nr`) - sbe.total_prevpayments AS total_bill,
					  (
					    sbe.total_prevpayments - (
					      (SELECT 
					        IFNULL(SUM(a.amount), 0) amount 
					      FROM
					        `seg_person_ledger_d` a 
					      WHERE a.`bill_nr` = sbe.`bill_nr` 
					        AND a.`entry_type` = 'credit' 
					        AND a.`pay_type` = 'memo') + 
					      (SELECT 
					        IFNULL(SUM(a.amount), 0) amount 
					      FROM
					        `seg_person_ledger_d` a 
					      WHERE a.`bill_nr` = sbe.`bill_nr` 
					        AND a.`entry_type` = 'debit')
					    )
					  ) - sbe.total_prevpayments AS total_payment,
					  spn.`remarks` 
					FROM
					  seg_promissory_note spn 
					  LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = spn.`encounter_nr` 
					  LEFT JOIN seg_billing_encounter sbe 
					    ON (
					      sbe.`encounter_nr` = spn.`encounter_nr` 
					      AND sbe.is_deleted IS NULL
					    ) WHERE DATE(spn.`create_dt`) BETWEEN DATE(".$db->qstr($date_from).") AND DATE(".$db->qstr($date_to).")
						AND spn.`note_status` <> 'deleted' 
					ORDER BY DATE(spn.due_date),
					  fn_get_person_lastname_first (ce.`pid`)";
		
		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

	function getPromiList($filters, $offset=0, $rowcount=15, $keyword){
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$filter_err = '';
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters [] = " DATE(d.due_date) = DATE(NOW()) ";
					break;
					case 'datethisweek':
						$phFilters [] = " YEAR(d.due_date)=YEAR(NOW()) AND WEEK(d.due_date)=WEEK(NOW()) ";
					break;
					case 'datethismonth':
						$phFilters [] = " YEAR(d.due_date)=YEAR(NOW()) AND MONTH(d.due_date)=MONTH(NOW()) ";
					break;
					case 'date':
						$phFilters [] = " DATE(d.due_date)=".$db->qstr($v)." ";
					break;
					case 'datebetween':
						$phFilters[]= " d.due_date>=".$db->qstr($v[0])." AND d.due_date<=".$db->qstr($v[1])." ";
					break;
					case 'pid':
						$phFilters[] = " cp.pid = ".$db->qstr($keyword)." ";
					break;
					case 'encounter_nr':
						$phFilters[] = " ce.encounter_nr = ".$db->qstr($keyword)." ";
					break;
					case 'name':
						if (strpos($keyword, ",") === false) {
							$phFilters[]= " cp.name_last like '".trim($keyword)."%' ";
							if ( (trim($keyword) == '') || (strlen(trim($keyword)) < 2) ) $filter_err = "Specify at least 2 characters in patient's family name!";
						}
						else {
							$tmp = explode(",", $v);
							$phFilters[] = " cp.name_last like '".trim($tmp[0])."%' ";
							$phFilters[] = " cp.name_first like '".trim($tmp[1])."%' ";

							if ( (trim($tmp[0]) == '') || (strlen(trim($tmp[0])) < 2) )
								$filter_err = "Specify at least 2 characters in patient's family name!";
							else
								if ( (trim($tmp[1]) == '') || (strlen(trim($tmp[1])) < 2) ) $filter_err = "Specify at least 2 characters in patient's first name!";
						}
					break;
				}
			}
		}

		if ($filter_err != '') {
			$this->error_msg = $filter_err;
			return false;
		}

		if(!$phFilters){
			$phFilters[] = " DATE(d.due_date) = DATE(NOW()) ";
		}

		$whereClause = " AND ".implode(" AND ",$phFilters);

		$this->sql = "SELECT 
						  d.`refno`,
						  d.`encounter_nr`,
						  fn_get_person_lastname_first (ce.`pid`) patient,
						  d.`due_date`
						FROM
						  seg_promissory_note d 
						  LEFT JOIN care_encounter ce 
						    ON ce.`encounter_nr` = d.`encounter_nr`
						  LEFT JOIN care_person cp
						  	ON cp.`pid` = ce.`pid`  
						  WHERE d.`note_status` <> 'deleted' 
						  $whereClause  
						  LIMIT $offset, $rowcount ";

		if($this->result=$db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

	function removePromi($refno){
		global $db;

		$this->sql= "UPDATE seg_promissory_note SET note_status = 'deleted',
						modify_id = ".$db->qstr($_SESSION['sess_temp_userid']).",
						modify_dt = NOW() WHERE refno =".$db->qstr($refno);

		if($db->Execute($this->sql)){
			return true;
		}

		return false;
	}
}

?>