<?php
/**
* class_cmap_allotment
* 
* provides helper functions for CMAP account allotment entries
* 
* @package care_api_classes/sponsor
* @author Alvin Quinones
* @version v1.0.0 (2010-06-12)
*/

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCmapAllotment extends Core {
	private $loggerName = 'sponsor.cmap';
	private $allotmentTable;
	
	private $id;
	
	/**
	* Constructor
	* 
	*/
	function SegCmapAllotment($id=null) {
		global $db;
		$this->allotmentTable= 'seg_cmap_allotments';
		$this->setTable($this->allotmentTable, $fetch_metadata=true);
		$this->setupLogger($this->loggerName);
		
		if ($id) {
			$this->id = $id;
		}
	}
	
	
	/**
	* put your comment there...
	* 
	* @param mixed $data
	* @param mixed $force_insert
	*/
	function save($data, $force_insert=false) {
		global $db;
		
		if (!$force_insert) {
			// Determine if the operation is an update so we'll know if it is necessary to validate the allotment amount
			$this->result = $db->GetOne("SELECT EXISTS(SELECT * FROM $this->coretable WHERE id=".$db->qstr($data['id']).")");
			if ($this->result === '1') {
				$total_referral = (float) $db->GetOne("SELECT SUM(current_balance) FROM seg_cmap_referrals WHERE cmap_account=".$db->qstr($data['cmap_account']));
				$total_allotments = (float) $db->GetOne("SELECT SUM(amount) FROM seg_cmap_allotments WHERE cmap_account=".$db->qstr($data['cmap_account'])." AND id!=".$db->qstr($data['id'])) ;
				$new_amount = (float) $data['amount'];
				if ($total_allotments+$new_amount < $total_referral) {
					$this->setErrorMsg('Cannot set the allotment total to less than the total referral amount...');
					return false;
				}
			}
		}
		if (parent::save($data, $force_insert)) {
			return true;
		}
		else {
			$this->logger->error('Save failed...');
			return false;
		}
	}
	
	
	
	/**
	* put your comment there...
	* 
	* @param mixed $id
	* @return ADODB
	*/
	function delete($id) {
	
	}
	
	
	/**
	* put your comment there...
	* 
	* @param string $id
	*/
	function fetch($id=null) {
		if (!$id) {
			$id=$this->id;
		}
		return parent::fetch(array('id'=>$id));
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $account_id
	*/
	function getAllotments($cmap_account, $sort_sql='', $numrows=1, $offset=-1) {
		global $db;
		
		$this->sql = "SELECT SQL_CALC_FOUND_ROWS alt.id,alt.cmap_account,acc.account_name,\n".
			"alt.allotment_date,alt.amount,alt.remarks,alt.create_id,alt.create_time,\n".
			"u.name create_name,nca.nca_no,saro.saro_no\n".
			"FROM ".$this->coretable." alt\n".
			"LEFT JOIN seg_cmap_accounts acc ON acc.account_nr=alt.cmap_account\n".
			"LEFT JOIN care_users u ON u.login_id=alt.create_id\n".
			"LEFT JOIN seg_cmap_nca nca ON nca.id=alt.id\n".
			"LEFT JOIN seg_cmap_saro saro ON saro.id=alt.id\n".
			"WHERE alt.cmap_account=".$db->qstr($cmap_account)."\n";
		if ($sort_sql) {
			$this->sql .= $sort_sql;
		}
		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		$this->result = $db->SelectLimit($this->sql, $numrows, $offset);
		if ($this->result!==false) {
			return $this->result->GetRows();
		}
		else {
			$this->logger->error("Unable to fetch allotments: ".$this->getErrorMsg()."\nQuery: ".$this->getLastQuery());
			return false;
		}
	}
}
