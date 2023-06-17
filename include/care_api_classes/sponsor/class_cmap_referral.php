<?php
/**
* class_cmap_referral
*
* provides helper functions for posting referral data from CMAP accounts
* to patient accounts
*
* @package care_api_classes/sponsor
* @todo Validate data for save method
* @author Alvin Quinones
* @version v1.0.0 (2010-06-09)
*/

require("./roots.php");
require_once $root_path.'include/care_api_classes/class_core.php';
require_once $root_path.'include/care_api_classes/sponsor/class_cmap_account.php';

class SegCmapReferral extends Core {
	private $referralTable = 'seg_cmap_referrals';
	private $loggerName = 'sponsor.cmap';

	private $id;
	private $cmapAccount;
	private $amount;

	/**
	* Constructor
	*
	*/
	public function __construct($id=null) {
		global $db;

		$this->setTable($this->referralTable, $fetch_metadata=true);
		if ($id) {
			//$this->id = $id;
			$row = $this->fetch( Array("id"=>$id) );
			if ($row !== false)
			{
				$this->id = $id;
				$this->cmapAccount = $row['cmap_account'];
				$this->amount = $row['referral_amount'];
			}
			else
			{
				$this->id = null;
				$this->cmapAccount = null;
				$this->amount = 0;
			}
		}
		$this->setupLogger($this->loggerName);
	}


	/**
	* Returns the referral Id (36-char length code)
	*
	*/
	public function getId() {
		return $this->id;
	}

	/**
	* Returns the referral amount
	*
	*/
	public function getReferralAmount()
	{
		return ($this->id ? $this->amount : null);
	}


	public function deleteReferral()
	{
		global $db;

		if (!$this->id || !$this->cmapAccount || !$this->amount)
		{
			$this->logger->error("Error deleting referral: ".print_r(
				Array(
					"id"=>$this->id,
					"cmapAccount" => $this->cmapAccount,
					"amount" => $this->amount,
				), true)
			);
			return false;
		}

		$account = new SegCmapAccount();
		$ok = $account->updateBalance( $this->cmapAccount, 'cancel', $this->amount);

		if ($ok)
		{
			$ok=$this->delete(Array("id"=>$this->getId()));
		}

		if ($ok === false)
		{
			$this->logger->error("Error deleting referral: ".$this->getErrorMsg()."\nQuery:".$this->getQuery());
		}

		return $ok;
	}


	public function total($filters) {
		global $db;
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtoupper($i)) {
					case 'PID':
						$where = 'pid='.$db->qstr($v);
					break;
					case 'WALKIN_PID':
						$where = 'walkin_pid='.$db->qstr($v);
						break;
					case 'ACCOUNT':
						$where = 'cmap_account='.$db->qstr($v);
					break;
				}
			}
		}
		else {
			return false;
		}
		$query = "SELECT SUM(current_balance) FROM ".$this->coretable;
		if ($where) {
			$query.=" WHERE $where";
		}

		if ( $this->setResult( $db->GetOne( $this->setQuery($query) ) ) !== false ) {
			return $this->getResult();
		} else {
			@$this->logger->error("Error retrieving totals: ".$db->ErrorMsg()."\nQuery: ".$this->getQuery());
			return false;
		}
	}



	/**
	* put your comment there...
	*
	*/
	public function getBalance()
	{
		global $db;
		return $db->GetOne("SELECT current_balance FROM $this->referralTable WHERE id=".$db->qstr($this->getId()));
	}


	/**
	* put your comment there...
	*
	* @param mixed $amount
	* @param mixed $id
	*/
	public function updateBalance( $amount, $id=null)
	{
		global $db;

		if (!is_numeric($amount)) {
			$this->logger->warn('Non-numeric value passed as amount: '.var_export($amount,true));
			return false;
		}

		if (!$id) {
			$id = $this->id;
		}

		$query= "UPDATE ".$this->referralTable." SET current_balance=".$amount." WHERE id=".$db->qstr($id);
		$ok = $this->setResult( $db->Execute($this->setQuery($query)) );
		if ($ok === false) {
			$this->logger->error('Failed to update balance: '.$db->ErrorMsg()."\nQuery: ".$query);
			return false;
		}
		return true;
	}


	/**
	* put your comment there...
	*
	* @param mixed $filters
	*/
	public function getReferrals($filters) {
		global $db;

		$offset = -1;
		$rowcount = -1;
		$sortSQL = "r.referral_date DESC";

		$where = array();
		$found_rows = false;

		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtoupper($i)) {
					case 'FOUNDROWS':
						if ($v) {
							$found_rows = true;
						}
					break;
					case 'PID':
						if ($v) {
							if (strpos($v,'W') !== FALSE)
								$where[] = 'r.walkin_pid='.$db->qstr( substr($v,1) );
							else
								$where[] = 'r.pid='.$db->qstr($v);
						}
					break;
					case 'NOINACTIVE':
						if ($v) {
							$where[] = 'r.current_balance>0';
						}
					break;
					case 'OFFSET':
						$offset=(int)$v;
					break;
					case 'ROWCOUNT':
						$rowcount=(int)$v;
					break;
					case 'SORTSQL':
						if ($v) {
							$sortSQL = $v;
						}
					break;
				}
			}
		}
		else {
			@$this->logger->error('Invalid filter: '.var_export($filters, true));
			return false;
		}

		$sql=
			"SELECT SQL_CALC_FOUND_ROWS r.id,r.referral_date,r.control_nr, r.referral_nr, r.pid,\n".
				"r.cmap_account,r.referral_amount,r.current_balance,r.remarks,\n".
				"c.account_name,r.create_id,u.name create_name\n".
			"FROM seg_cmap_referrals r\n".
				"INNER JOIN seg_cmap_accounts c ON c.account_nr=r.cmap_account\n".
				"LEFT JOIN care_users u ON u.login_id=r.create_id\n";
		if ($where)
			$sql.="WHERE (".implode(")\n AND (",$where).")\n";
		$sql .= "ORDER BY $sortSQL";

		$this->setQuery($sql);

		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		if (($this->result=$db->SelectLimit($this->getQuery(), $rowcount, $offset)) !== false) {
			return $this->result;
		} else {
			@$this->logger->error("Error retrieving referrals: ".$db->ErrorMsg()."\nQuery: ".$this->getQuery());
			return false;
		}
	}
}
