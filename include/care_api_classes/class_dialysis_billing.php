<?php
require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_core.php');

class Dialysis_billing extends Core{

	var $details = array();
	var $result;
	var $sql;
	var $userID;

	function Dialysis_billing(){
		$this->userID=$_SESSION['sess_temp_userid'];
	}

	function getDialysisPatients($filters, $offset=0, $rowcount=15, $keyword){
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$filter_err = '';
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters [] = " DATE(d.create_dte) = DATE(NOW()) ";
					break;
					case 'datethisweek':
						$phFilters [] = " YEAR(d.create_dte)=YEAR(NOW()) AND WEEK(d.create_dte)=WEEK(NOW()) ";
					break;
					case 'datethismonth':
						$phFilters [] = " YEAR(d.create_dte)=YEAR(NOW()) AND MONTH(d.create_dte)=MONTH(NOW()) ";
					break;
					case 'date':
						$phFilters [] = " DATE(d.create_dte)=".$db->qstr($v)." ";
					break;
					case 'datebetween':
						$phFilters[]= " d.create_dte>=".$db->qstr($v[0])." AND d.create_dte<=".$db->qstr($v[1])." ";
					break;
					case 'pid':
						$phFilters[] = " cp.pid = ".$db->qstr($keyword)." ";
					break;
					case 'done':
						$phFilters[] = " d.trans_flag ='done' ";
					break;
					case 'active':
						$phFilters[] = " d.trans_flag ='active' ";
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
			$phFilters[] = " DATE(d.create_dte) = DATE(NOW()) ";
		}

		$whereClause = " WHERE ".implode(" AND ",$phFilters);
		
		$this->sql = "SELECT
					  SQL_CALC_FOUND_ROWS
					  d.id,
					  DATE(d.create_dte) AS date_create,
					  (SELECT 
					    MIN(DATE(session_date)) 
					  FROM
					    seg_dialysis_transaction_d 
					  WHERE id = d.`id`) AS start_date,
					  (SELECT 
					    MAX(DATE(session_date)) 
					  FROM
					    seg_dialysis_transaction_d 
					  WHERE id = d.`id`) AS end_date,
					  d.pid,
					  fn_get_person_lastname_first (d.pid) AS p_name,
					  (SELECT 
					    COUNT(id) 
					  FROM
					    seg_dialysis_transaction_d 
					  WHERE id = d.id) sessions,
					  d.trans_flag  
					FROM
					  seg_dialysis_transaction_h d 
					  LEFT JOIN care_person cp 
					    ON cp.pid = d.pid 
					  $whereClause
					  ORDER BY p_name ASC, DATE(date_create) DESC 
						LIMIT $offset, $rowcount";

		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} 

		return false;
	}

	function getCycleInfo(){
		global $db;

		$this->sql = "SELECT 
					  h.`id`,
					  h.`pid`,
					  fn_get_person_lastname_first (h.pid) AS p_name,
					  fn_get_age(DATE(NOW()), DATE(date_birth)) AS age,
					  fn_get_complete_address(h.`pid`) address,
					  h.`trans_flag` 
					FROM
					  seg_dialysis_transaction_h h 
					  LEFT JOIN care_person cp 
					    ON cp.`pid` = h.`pid` 
					WHERE h.`id` = ".$db->qstr($this->details['id']);

		if($this->result = $db->Execute($this->sql)){
			return $this->result->FetchRow();
		}

		return false;
	}

	function getSessions(){
		global $db;

		$this->sql = "SELECT 
					  d.`id`,
					  d.`entry_no`,
					  sbe.`encounter_nr`,
					  sbe.`bill_frmdte` AS case_date,
					  d.`bill_nr`,
					  sbe.`bill_dte`,
					  cte.`type` 
					FROM
					  seg_dialysis_transaction_d d 
					  LEFT JOIN seg_billing_encounter sbe 
					    ON sbe.`bill_nr` = d.`bill_nr` 
					  LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = sbe.`encounter_nr` 
					  LEFT JOIN care_type_encounter cte 
					    ON cte.`type_nr` = ce.`encounter_type`
					WHERE id = ".$db->qstr($this->details['id'])."
					ORDER BY DATE(case_date) ASC ";

		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

	function getBilled(){
		global $db;

		$where = '';

		if($this->details['id']){
			$where = " WHERE id <> ".$db->qstr($this->details['id']);
		}

		$this->sql = "SELECT 
					  ce.`encounter_date`,
					  ce.`encounter_nr`,
					  sbe.`bill_nr`,
					  (SELECT 
					    GROUP_CONCAT(cif.firm_id) 
					  FROM
					    seg_billing_coverage sbc 
					    LEFT JOIN care_insurance_firm cif 
					      ON sbc.hcare_id = cif.hcare_id 
					  WHERE sbc.bill_nr = sbe.`bill_nr` 
					  GROUP BY sbc.bill_nr) insurance,
					  cte.`type`
					FROM
					  care_encounter ce 
					  INNER JOIN seg_billing_encounter sbe 
					    ON (
					      sbe.`encounter_nr` = ce.`encounter_nr` 
					      AND (
					        sbe.`is_deleted` <> 1 
					        OR sbe.`is_deleted` IS NULL
					      ) 
					      AND sbe.`is_final` = 1
					    ) 
						LEFT JOIN care_type_encounter cte 
					    ON cte.`type_nr` = ce.`encounter_type`
					WHERE ce.`pid` = ".$db->qstr($this->details['pid'])." 
					  AND ce.`encounter_status` <> 'cancelled' 
					  AND ce.`encounter_nr` NOT IN 
					    (SELECT 
						    encounter_nr 
						  FROM
						    seg_transmittal_details) 
						  AND sbe.`bill_nr` NOT IN 
						  (SELECT 
						    bill_nr 
						  FROM
						    seg_dialysis_transaction_d ".$where.") 
						ORDER BY DATE(ce.`encounter_date`)";
						
		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

	function getThisBilling(){
		global $db;

		$this->sql="SELECT 
					  sbe.`encounter_nr`,
					  sbe.`bill_frmdte` AS case_date,
					  sbe.`bill_nr`,
					  sbe.`bill_dte`,
					  cte.`type` 
					FROM
					  seg_billing_encounter sbe 
					  LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = sbe.`encounter_nr` 
					  LEFT JOIN care_type_encounter cte 
					    ON cte.`type_nr` = ce.`encounter_type`
					WHERE sbe.`bill_nr` = ".$db->qstr($this->details['bill_nr']);

		if($this->result=$db->Execute($this->sql)){
			return $this->result->FetchRow();
		}

		return false;
	}

	function updateCycle(){
		global $db;

		$history = $db->qstr("\n".$this->details['history']." ".$this->userID." ");

		$this->sql="UPDATE seg_dialysis_transaction_h SET ".
						"trans_flag = ".$db->qstr($this->details['trans_flag']).",".
						"modify_dte = NOW(),".
						"modify_id = ".$db->qstr($this->userID).",".
						"history=CONCAT(history, ".$history." , NOW())
						WHERE id=".$db->qstr($this->details['id']);
		
		if($db->Execute($this->sql)){
			return true;
		}

		return false;
	}

	function getLatesId(){
		$objPerson = new Person();
		$this->details['id'] = $objPerson->getNewDialysisId();
	}

	function insertCycle(){
		global $db;

		$history = $db->qstr("create ".$this->userID." ");

		$this->sql="INSERT INTO seg_dialysis_transaction_h (id, pid, trans_flag, 
							modify_dte, modify_id, create_dte, create_id, history) ".
						" VALUES(".$db->qstr($this->details['id']).",".
								   $db->qstr($this->details['pid']).",".
								   $db->qstr($this->details['trans_flag']).", NOW(),".
								   $db->qstr($this->userID).", NOW(), ".
								   $db->qstr($this->userID).", ".
								   $history.")";

		if($db->Execute($this->sql)){
			return true;
		}

		return false;
	}

	function getThisSession(){
		global $db;

		$this->sql = "SELECT id, entry_no, bill_nr, session_date FROM seg_dialysis_transaction_d ".
							" WHERE bill_nr = ".$db->qstr($this->details['bill_nr']);
		
		if($this->result = $db->Execute($this->sql)){
			return $this->result;
		}

		return false;
	}

	function saveDia($data){
		global $db;	

		if(!$this->details['id']){
			$this->getLatesId();
			if(!$this->insertCycle()) return false;
		}else{
			if(!$this->updateCycle()) return false;
		}
		
		if($this->deleteDiaItems()){
			for($i=0; $i<count($data); $i++){
				$details = array("id"=>$this->details['id'],
								"count"=>$i,
								"bill_nr"=>$data[$i]['bill_nr'],
								"session_date"=>$data[$i]['session_date']);
				$this->insertItems($details);
			}

			return $this->details['id'];
		}

		return false;
	}

	function insertItems($data){
		global $db;

		$this->sql = "INSERT INTO seg_dialysis_transaction_d (id, entry_no, bill_nr, session_date) ".
									" VALUES(".$db->qstr($data['id']).", ".
												$db->qstr($data['count']).", ".
												$db->qstr($data['bill_nr']).",".
												$db->qstr($data['session_date']).")";
		$db->Execute($this->sql);
	}

	function deleteDiaItems(){
		global $db;

		$this->sql = "DELETE FROM seg_dialysis_transaction_d WHERE id= ".$db->qstr($this->details['id']);

		if($db->Execute($this->sql)){
			return true;
		}

		return false;
	}

	function hasTransmittal(){
		global $db;

		$this->sql = "SELECT 
						  d.`transmit_no` 
						FROM
						  seg_transmittal_details d 
						  LEFT JOIN seg_transmittal h 
						    ON h.`transmit_no` = d.`transmit_no` 
						WHERE d.`encounter_nr` = ".$db->qstr($this->details['encounter_nr'])."
						  AND h.`hcare_id` = ".$db->qstr(PHIC_ID);
						 
		if($this->result=$db->Execute($this->sql)){
			if($this->result->RecordCount() > 0){
				return true;
			}
		}

		return false;
	}
}
