<?php
/**
* @package sponsor
* @deprecated This class has been dropped in favor of a healthier SegGrant
*/

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/sponsor/helpers/QueryBuilder.php');
require_once($root_path.'include/care_api_classes/sponsor/class_request.php');
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_referral.php');

class SegRequestGrant extends Core {

	const GRANT_LINGAP 		= 1001;
	const GRANT_CMAP 			= 1002;

	private $keys 				= null;
	private $requestType 	= null;

	/**	@var String Id of the Logger to be used for this module	*/
	private static $loggerName='sponsor';


	private static $lingapGrantSources = Array(
		SegRequest::BILLING_REQUEST 		=> 'seg_lingap_entries_bill',
		SegRequest::PHARMACY_REQUEST 		=> 'seg_lingap_entries_pharmacy',
		SegRequest::RADIOLOGY_REQUEST 	=> 'seg_lingap_entries_radiology',
		SegRequest::LABORATORY_REQUEST 	=> 'seg_lingap_entries_laboratory',
//		SegRequest::OR_REQUEST 					=> 'seg_lingap_entries_or',
		SegRequest::MISC_REQUEST 				=> 'seg_lingap_entries_misc'
	);

	private static $cmapGrantSources = Array(
		SegRequest::BILLING_REQUEST 		=> 'seg_cmap_entries_bill',
		SegRequest::PHARMACY_REQUEST 		=> 'seg_cmap_entries_pharmacy',
		SegRequest::RADIOLOGY_REQUEST 	=> 'seg_cmap_entries_radiology',
		SegRequest::LABORATORY_REQUEST 	=> 'seg_cmap_entries_laboratory',
//		SegRequest::OR_REQUEST 					=> 'seg_cmap_entries_or',
		SegRequest::MISC_REQUEST 				=> 'seg_cmap_entries_misc'
	);

	/**
	* Constructor
	*
	* @param Int $requestType Any of the defined SegRequest::*_REQUEST constants
	* @param Array $keys The key values specifying the data for the request to be granted. Ideally, this array's keys correspond to the field names of the key columns of the request source table
	*/
	public function __construct($GranterObject) {
		global $db;
		$this->setupLogger(self::$loggerName);
		$this->requestType = $requestType;
		$this->keys = $keys;
	}




	/**
	* put your comment there...
	*
	* @param mixed $grantType
	* @param mixed $requestType
	*/
	public function grant($grantType, $amount, $remarks) {
		if ($grantType === self::GRANT_CMAP) {
			return (float) $this->_cmap_grant($amount, $remarks);
		}
		else {
			$this->logger->warn( 'Grant type not handled: '.var_export($grantType, true) );
			return false;
		}
	}



	/**
	* put your comment there...
	*
	*/
	public function getTotalGrant($grant_type=null) {
		if ($grant_type === self::GRANT_LINGAP) {
			return (float) $this->_lingap_get_total_grant();
		}
		elseif ($grant_type === self::GRANT_CMAP) {
			return (float) $this->_cmap_get_total_grant();
		}
		else {
			return (float) $this->_lingap_get_total_grant() + (float) $this->_cmap_get_total_grant();
		}
	}



	/**
	*
	*/
	public function getGrantHistory($keys=null, $sort=null, $rows=-1, $offser=-1) {

	}




	/**
	* LINGAP functions
	*
	*/

	/**
	* put your comment there...
	*
	* @access private
	* @param mixed $request_type
	*/
	private function _lingap_get_builder_def($request_type) {
		$def = Array(
			'coreTable' => self::$cmapGrantSources[$request_type],
			'joins' 		=> Array(),
			'fields' 		=> Array(
				'referral' 		=> 'referral_id',
				'refNo' 			=> 'ref_no',
				'pid'					=> 'pid',
				'itemNo' 			=> 'service_code',
				'itemName' 		=> 'service_name',
				'unitPrice' 	=> 'amount/quantity',
				'quantity' 		=> 'quantity',
				'totalAmount' => 'amount',
				'remarks' 		=> 'remarks'
			),
			'orderBy'		=> Array(),
			'where' 		=> Array()
		);

		switch($request_type) {
			case SegRequest::BILLING_REQUEST:
			case SegRequest::PHARMACY_REQUEST:
			case SegRequest::RADIOLOGY_REQUEST:
			case SegRequest::LABORATORY_REQUEST:
			case SegRequest::OR_REQUEST:
				return $def;
			case SegRequest::MISC_REQUEST:
				$def['fields']['entryNo'] = 'entry_no';
				return $def;
			default:
				return false;
		}
	}

	# This function is not in use because Lingap granting is handled by SegLingap classes
	private function _lingap_grant() {
		return false;
	}

	# This function is not in use because Lingap granting is handled by SegLingap classes
	private function _lingap_remove_grant() {
		return false;
	}


	/**
	* put your comment there...
	*
	* @access private
	*/
	private function _lingap_get_total_grant() {
		global $db;
		if ($this->requestType == SegRequest::BILLING_REQUEST) {
			$sql = "SELECT SUM(amount) FROM seg_lingap_entry_details_fb WHERE ref_no=".$db->qstr($this->keys['refNo']);
		}
		elseif ($this->requestType == SegRequest::MISC_REQUEST) {
			$sql = "SELECT SUM(amount) FROM seg_lingap_entry_details WHERE ref_source=".$db->qstr(self::$lingapGrantSources[$this->requestType]).
				" AND ref_no=".$db->qstr($this->keys['refNo']).
				" AND entry_no=".$db->qstr($this->keys['entryNo']).
				" AND service_code=".$db->qstr($this->keys['itemNo']);
		}
		else {
			$sql = "SELECT SUM(amount) FROM seg_lingap_entry_details WHERE ref_source=".$db->qstr(self::$lingapGrantSources[$this->requestType]).
				" AND ref_no=".$db->qstr($this->keys['refNo'])." AND service_code=".$db->qstr($this->keys['itemNo']);
		}
		$this->setQuery($sql);
		$this->setResult($db->GetOne($this->getQuery()));
		if ($this->getResult() === false) {
			$this->logger->error("Failed to retrieve total Lingap grant: ".$db->ErrorMsg()."\n Query:".$this->getQuery());
		}
		return $this->getResult();
	}



	/**
	* CMAP functions
	*
	*/

	/**
	* put your comment there...
	*
	* @access private
	* @param mixed $request_type
	*/
	private function _cmap_get_builder_def($request_type) {
		$def = Array(
			'coreTable' => self::$cmapGrantSources[$request_type],
			'joins' 		=> Array(),
			'fields' 		=> Array(
				'referral' 		=> 'referral_id',
				'refNo' 			=> 'ref_no',
				'pid'					=> 'pid',
				'itemNo' 			=> 'service_code',
				'itemName' 		=> 'service_name',
				'unitPrice' 	=> 'amount/quantity',
				'quantity' 		=> 'quantity',
				'totalAmount' => 'amount',
				'remarks' 		=> 'remarks'
			),
			'orderBy'		=> Array(),
			'where' 		=> Array()
		);

		switch($request_type) {
			case SegRequest::BILLING_REQUEST:
			case SegRequest::PHARMACY_REQUEST:
			case SegRequest::RADIOLOGY_REQUEST:
			case SegRequest::LABORATORY_REQUEST:
			case SegRequest::OR_REQUEST:
				return $def;
			case SegRequest::MISC_REQUEST:
				$def['fields']['entryNo'] = 'entry_no';
				return $def;
			case SegRequest::EXTERNAL_LABORATORY_REQUEST:
				return $def;
			default:
				return false;
		}
	}



	private function _cmap_grant($amount, $remarks='') {
		global $db;
		$request = new SegRequest($this->requestType, $this->keys);
		$info = $request->fetch();
		if (!$info) {
			return false;
		}

		$pid = $info['pid'];
		$amount = (float) $amount;
		if (!$amount ) {
			$this->logger->warn('Invalid amount specified. amount:'.var_export($amount, true));
			return false;
		}

		# Only grant exact amount, excess amount should be discarded
		if ( $amount  < (float)$amount ) {
			$types = SegRequest::getRequestTypes();
			$this->logger->warn('Request amount is lower than grant amount specified. type:'.$types[$this->requestType]."\nkey:".print_r($this->keys, true));
			$amount = $info['amount'];
		}

		$referral = new SegCmapReferral();
		$referral_balance = (float) $referral->total(array('PID'=>$pid));

		# Check if patient has enough balance
		if ($referral_balance < $amount) {
		$this->logger->info('Attempt to grant amount exceeding referral balance. type:'.$types[$this->requestType]."\nkey:".print_r($this->keys, true));
			return false;
		}

		$def = $this->_cmap_get_builder_def($this->requestType);

		# Go through all referrals by date and keep deducting from remaining balance until entire amount is covered
		$referrals = $referral->getReferrals(array(
			'PID' 				=> $pid,
			'NOINACTIVE' 	=> true,
			'SORTSQL' 		=> 'referral_date ASC'
		));
		$running_amount = $amount;
		if ($referrals) {
			foreach ($referrals as $refer) {

				$balance = (float) $refer['current_balance'];
				if ($balance==0)
					continue;

				#$this->logger->info('balance:'.var_export($balance, true));
				if ( $balance>$running_amount ) {
					$grant_amount = $running_amount;
					$balance -= $running_amount;
					$running_amount = 0.0;
				}
				else {
					$grant_amount = $balance;
					$running_amount -= $balance;
					$balance = 0.0;
				}

				# Update referral running balance
				if (($ok = $referral->updateBalance($balance, $refer['id'])) === false)  {
					return false;
				}

				# Save grant
				$grantId = create_guid();


				/**
				* @todo Build data array from builder definition for more flexibility to changes in structure
				*/
				$data = Array(
					'id' 						=> $db->qstr($grantId),
					'referral_id' 	=> $db->qstr($refer['id']),
					'pid'						=> $db->qstr($info['pid']),
					'ref_no' 				=> $db->qstr($this->keys['refNo']),
					'service_code' 	=> $db->qstr($this->keys['itemNo']),
					'service_name' 	=> $db->qstr($info['itemName']),
					'quantity'			=> $db->qstr($info['quantity']),
					'amount'				=> $db->qstr($grant_amount),
					'remarks'				=> $db->qstr($remarks),
					'create_id'			=> $db->qstr($_SESSION['sess_temp_userid']),
					'create_time'		=> "NOW()"
				);

				if ($info['entryNo']) {
					$data['entry_no'] = $info['entryNo'];
				}

				if ($this->setResult( $db->Replace($def['coreTable'], $data, 'id', $autoQuote = false) ) === 0) {
					$this->logger->error( 'Cannot save grant information: '.$db->ErrorMsg() );
					return false;
				}


				# If running amount reaches 0, no need to search more...
				if ($running_amount == 0)
					break;
			}

			if ($running_amount > 0) {
				$this->logger->warn('Amount not fully granted (referral amount exceeded). Remaining amount: '.$running_amount);
				return false;
			}
			else {
				$ok = $request->flag('cmap');
				if (!$ok) {
					return false;
				}
			}

			// Success
			return TRUE;
		}
		else {
			return false;
		}

	}



	/**
	* put your comment there...
	*
	*/
	private function _cmap_get_total_grant() {
		global $db;

		$def = $this->_cmap_get_builder_def($this->requestType);
		$sql = "SELECT SUM(".$def['fields']['totalAmount'].") FROM {$def['coreTable']} ";
		$where = array();
		foreach ($this->keys as $key=>$value) {
			$expr = QueryBuilder::express($def, 'EQ', $key, $value);
			if ($expr) $where[] = $expr;
		}
		if ($where) {
			$sql.=" WHERE (".implode(") AND (", $where).")";
		}
		$this->setQuery($sql);
		$this->setResult($db->GetOne($this->getQuery()));
		//$this->logger->info($sql);
		if ($this->getResult() === false) {
			$this->logger->error("Cannot retrieve amount! Error: ".$db->ErrorMsg()."\nQuery: ".$this->getQuery());
		}
		return $this->getResult();
	}

	private function _cmap_history($keys=null, $sort=null, $rows=-1, $offset=-1) {
		global $db;



	}


}

