<?php
require("./roots.php");
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_referral.php');
require_once($root_path.'include/care_api_classes/sponsor/class_request.php');
require_once($root_path.'include/care_api_classes/sponsor/class_grantor.php');


/**
*
*/
class SegCmapGrantor extends Grantor {
	private $loggerName = 'sponsor.cmap';


	private static $GrantSources = Array(
		SegRequest::BILLING_REQUEST 		=> 'seg_cmap_entries_bill',
		SegRequest::PHARMACY_REQUEST 		=> 'seg_cmap_entries_pharmacy',
		SegRequest::RADIOLOGY_REQUEST 	=> 'seg_cmap_entries_radiology',
		SegRequest::LABORATORY_REQUEST 	=> 'seg_cmap_entries_laboratory',
		SegRequest::MISC_REQUEST 				=> 'seg_cmap_entries_misc'
	);

	public function __construct() {
		$this->setupLogger($this->loggerName);
	}


	/**
	* put your comment there...
	*
	* @param SegRequest $request
	* @param mixed $amount
	* @param mixed $remarks
	*/
	public function grant(SegRequest $request, $amount=0, $remarks='') {
		global $db;

		$types = SegRequest::getRequestTypes();
		$this->logger->info("Attempting to grant CMAP...type:".$types[$request->getRequestType()]."\nkey:".print_r($request->getKeys, true));

		$def = $this->get_builder_def( $request->getRequestType() );
		if (!$def) {
			$this->logger->warn("Invalid builder def found: "."def:".print_r($request->getKeys, true));
		}

		$info = $request->fetch();
		if (!$info) {
			return false;
		}

		$pid = $info['pid'];
		$grant_amount = (float)$amount;
		if (!$amount ) {
			$this->logger->warn('Invalid amount specified. amount:'.var_export($amount, true));
			return false;
		}

		// Only grant exact amount due for the request, excess amount will be discarded
		$amount_due = (float)$info['amount'];
		if ( $grant_amount > $amount_due) {
			$this->logger->warn('Request amount is lower than grant amount specified...amount:'.$grant_amount);
			$grant_amount = $amount_due;
		}

		$referral = new SegCmapReferral();
		$referral_balance = (float) $referral->total(array('PID'=>$pid));

		// Check if patient has enough balance
		if ($referral_balance < $grant_amount) {
			$this->logger->error('Grant amount exceeds referral balance...amount:'.$grant_amount);
			return false;
		}

		// Go through all referrals by descending date and keep deducting from remaining balance until entire amount is covered
		$referrals = $referral->getReferrals(array(
			'PID' 				=> $pid,
			'NOINACTIVE' 	=> true,
			'SORTSQL' 		=> 'referral_date ASC'
		));
		$running_amount = $grant_amount;
		if ($referrals) {
			foreach ($referrals as $refer) {

				$balance = (float) $refer['current_balance'];
				if ($balance==0)
					continue;

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

				$keys = $request->getKeys();
				$data = Array(
					'id' 						=> $db->qstr($grantId),
					'referral_id' 	=> $db->qstr($refer['id']),
					'pid'						=> $db->qstr($info['pid']),
					'ref_no' 				=> $db->qstr($keys['refNo']),
					'service_code' 	=> $db->qstr($keys['itemNo']),
					'service_name' 	=> $db->qstr($info['itemName']),
					'quantity'			=> $db->qstr($info['quantity']),
					'amount'				=> (float)$grant_amount,
					'remarks'				=> $db->qstr($remarks),
					'create_id'			=> $db->qstr($_SESSION['sess_temp_userid']),
					'create_time'		=> "NOW()"
				);

				// entry_no for Miscellaneous requests
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


	private function getTotalGrants(SegRequest $request) {
		global $db;

		$def = $this->get_builder_def( $request->getRequestType() );

		if (!$def) {
			$this->logger->info( 'Cannot process request of this type: '. $request->getRequestType() );
			return 0;
		}

		$sql = "SELECT SUM(".$def['fields']['totalAmount'].") FROM {$def['coreTable']} ";
		$where = array();
		foreach ($request->getKeys as $key=>$value) {
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


	/**
	* put your comment there...
	*
	* @access private
	* @param mixed $request_type
	*/
	private function get_builder_def($request_type) {
		$def = Array(
			'coreTable' => self::$GrantSources[$request_type],
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
				return $def;
			case SegRequest::MISC_REQUEST:
				$def['fields']['entryNo'] = 'entry_no';
				return $def;
			default:
				return false;
		}
	}

}

