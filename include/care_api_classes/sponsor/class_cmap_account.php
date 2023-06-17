<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCmapAccount extends Core {
	private $accountTable = 'seg_cmap_accounts';
	private $loggerName = 'sponsor.cmap';
	/**
	* @access private
	* @var String account Id for the CMAP account
	*/
	private $_accountId;

	/**
	* Constructor, PHP4 style
	*
	*/
	function SegCmapAccount($account=null) {
		global $db;
		$this->setTable($this->accountTable, $fetch_metadata=true);
		$this->setupLogger($this->loggerName);

		if ($account) {
			$this->_accountId = $account;
		}
	}

	/**
	* put your comment there...
	*
	* @param mixed $id
	*/
	function getTotalAllotments($id=null) {
		global $db;
		if (!$id) {
			$id=$this->_accountId;
		}
		$sql = 'SELECT SUM(amount) FROM seg_cmap_allotments WHERE cmap_account='.$db->qstr($id);
		if (($total = $db->GetOne($sql)) !== false) {
			return $total;
		}
		else {
			$this->logger->info('SQL error...query:'.$sql."\nerror:".$db->ErrorMsg());
			return false;
		}
	}

	/**
	* put your comment there...
	*
	*/
	function getActualBalance($id=null) {
		global $db;
		if (!$id) {
			$id=$this->_accountId;
		}
		$total_allotments = $this->getTotalAllotments($id);
		if ($total_allotments === false) {
			return false;
		}
		$sql = 'SELECT SUM(referral_amount-current_balance) FROM seg_cmap_referrals WHERE cmap_account='.$db->qstr($id);
		if (($total = $db->GetOne($sql)) !== false) {
			return $total_allotments - $total;
		}
		else {
			$this->logger->info('SQL error...query:'.$sql."\nerror:".$db->ErrorMsg());
			return false;
		}
	}


	/**
	* put your comment there...
	*
	*/
	function getReferredBalance() {
		global $db;
		if (!$id) {
			$id=$this->_accountId;
		}
		$total_allotments = $this->getTotalAllotments($id);
		if ($total_allotments === false) {
			return false;
		}

		$sql = 'SELECT SUM(referral_amount) FROM seg_cmap_referrals WHERE cmap_account='.$db->qstr($id);
		if (($total = $db->GetOne($sql)) !== false) {
			return $total_allotments - $total;
		}
		else {
			$this->logger->info('SQL error...query:'.$sql."\nerror:".$db->ErrorMsg());
			return false;
		}
	}

	/**
	* put your comment there...
	*
	* @param mixed $id
	* @param mixed $show_locked
	* @param mixed $show_deleted
	* @return ADODB
	*/
	function get($id = NULL, $show_locked=FALSE, $show_deleted=FALSE) {
		global $db;

		$this->sql = "SELECT account_nr,account_name,account_address,running_balance,is_locked,is_deleted\n".
			"FROM seg_cmap_accounts\n";

		$where = array();
		if ($id) {
			$where[] = "account_nr=".$db->qstr($nr)."\n";
		}
		else {
			if (!$show_locked) {
				$where[] = "NOT is_locked\n";
			}
			if (!$show_deleted) {
				$where[] = "NOT is_deleted\n";
			}
		}
		if ($where) $this->sql.="WHERE (".implode(")\n AND (", $where).")\n";
		if (!$id) $this->sql.="ORDER BY account_name";

		if ($this->result=$db->Execute($this->sql))
			return $this->result;
		else
			return FALSE;
	}

	function getBalance($id) {
		global $db;

		$this->sql = "SELECT running_balance FROM seg_cmap_accounts WHERE account_nr=".$db->qstr($id);
		return $db->GetOne($this->sql);
	}

	/**
	* updates the internal balance tracker of the CMAP accountm based on the type
	* of transaction that occurred
	*
	* @param mixed $nr id number of the CMAP account
	* @param mixed $type type of transaction that prompts the balance update
	* @param mixed $amount amount involved in the transaction
	*/
	function updateBalance($nr, $type, $amount) {
		global $db;

		$multiplier = array(
			'allotment'=>1.0,
			'deposit'=>1.0,
			'grant'=>-1.0,
			'referral'=>-1.0,
			'cancel'=>1.0
		);

		$amount = $amount * $multiplier[$type];

		$bal=$this->getBalance($nr);
		if (!$bal) $bal=0;

		if ($type=='adjustment')
			$bal=$amount;
		else
			$bal += $amount;

		$ret = $db->Replace('seg_cmap_accounts',
			array(
				'account_nr'=>$nr,
				'running_balance'=>$bal
			),
			'account_nr',
			$autoquote = true
		);

		if ($ret===0)
			return FALSE;
		else
			return TRUE;
	}

}

