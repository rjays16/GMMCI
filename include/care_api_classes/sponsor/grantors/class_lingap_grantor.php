<?php
require "./roots.php";
require_once $root_path.'include/care_api_classes/sponsor/class_request.php';
require_once $root_path.'include/care_api_classes/sponsor/helpers/QueryBuilder.php';
require_once $root_path.'include/care_api_classes/sponsor/grantors/class_grantor.php';


/**
* Grantor class for the Lingap submodule
*
*
*/
class SegLingapGrantor extends Grantor {
	private $loggerName = 'sponsor.lingap';
	private $referral;
	private $flagValue = 'lingap';


	private static $GrantSources = Array(
		SegRequest::BILLING_REQUEST 					=> 'seg_lingap_entries_bill',
		SegRequest::PHARMACY_REQUEST 					=> 'seg_lingap_entries_pharmacy',
		SegRequest::RADIOLOGY_REQUEST 				=> 'seg_lingap_entries_radiology',
		SegRequest::LABORATORY_REQUEST 				=> 'seg_lingap_entries_laboratory',
		SegRequest::MISC_REQUEST 							=> 'seg_lingap_entries_misc',
		SegRequest::PHARMACY_WALKIN_REQUEST 	=> 'seg_lingap_entries_pharmacy_walkin'
	);


	/**
	* Default constructor
	*
	* A SegLingapGrantor instance can be assigned a SegReferral object which is required
	* by some of its methods. A SegLingapGrantor without a SegReferral object assigned can be
	* used for fetching data for Lingap grants not constrained to within a single referral.
	*
	* A SegLingapGrantor without an assigned SegReferral is only allowed access to the following
	* methods. Other methods invoked will explicitly return a false value:
	*   - get
	* 	- getTotalGrants
	*
	*
	* @param SegLingapReferral $referral
	* @return SegLingapGrantor
	*/
	public function __construct( SegLingapReferral $referral=null ) {
		$this->setupLogger($this->loggerName);
		$this->referral = $referral;
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

		if (!$this->referral) {
			// need to specify a referral for this method
			return false;
		}

		$types = SegRequest::getRequestTypes();
		$this->logger->info("Attempting to grant LINGAP...type:".$types[$request->getRequestType()]."\nkey:".print_r($request->getKeys(), true));

		$builder = $this->get_builder_def( $request->getRequestType() );
		if (!$builder) {
			$this->logger->error("Invalid builder def found: ".$request->getRequestType()."def:".print_r($request->getKeys(), true));
			return false;
		}

		$info = $request->fetch();
		if (!$info) {
			$this->logger->error('Invalid request item...');
			return false;
		}

		$pid = $info['pid'];
		$grant_amount = (float)$amount;
		if (!$amount ) {
			$this->logger->error('Invalid amount specified. amount:'.var_export($amount, true));
			return false;
		}


		$amount_due = (float)$info['unitPrice'] * (float)$info['quantity'];
		if (!$amount_due) {
			$this->logger->error('Ooops! Amount due is 0! Check query: '.$request->getQuery());
			return false;
		}

		// Only grant exact amount due for the request, excess amount will be discarded
		if ( $grant_amount > $amount_due) {
			$this->logger->warn('Request amount (due) is lower than grant amount specified...grant:'.$grant_amount."\ndue:".$amount_due);
			$grant_amount = $amount_due;
		}


		$keys = $request->getKeys();
		$data = Array();

//		if ($builder['fields']['entryId']) {
//			$builder['fields']['entryId']	= $db->qstr($this->referral->getId());
//		}

		$save_values = array(
			'entryId' 			=> $db->qstr( $this->referral->getId() ),
			'refNo' 				=> $db->qstr( $keys['refNo'] ),
			'totalAmount'		=> (float)$grant_amount
		);

		switch ($request->getRequestType()) {
			case SegRequest::MISC_REQUEST:
				$save_values['entryNo'] = $db->qstr( $info['entryNo'] );
			case SegRequest::PHARMACY_REQUEST:
			case SegRequest::PHARMACY_WALKIN_REQUEST:
			case SegRequest::RADIOLOGY_REQUEST:
			case SegRequest::LABORATORY_REQUEST:
				$save_values['itemNo'] = $db->qstr( $keys['itemNo'] );
				$save_values['itemName'] = $db->qstr( $info['itemName'] );
				$save_values['quantity'] = $db->qstr( $info['quantity'] );
			break;
		}

		foreach ( $save_values as $field=>$value ) {
			if ($builder['fields'][$field]) {
				if (!is_null($value)) {
					$data[$builder['fields'][$field]]	= $value;
				}
			}
		}

		$keyFields = array();
		foreach ( $keys as $field=>$value ) {
			if ($builder['fields'][$field]) {
				$keyFields[]=$builder['fields'][$field];
			}
		}

//		$data = Array(


//		);
		//$this->logger->debug('Data: '.print_r($data, true));

		// entry_no for Miscellaneous requests
		if ($info['entryNo']) {
			$data['entry_no'] = $db->qstr( $info['entryNo'] );
		}

		if ($this->setResult( $db->Replace($builder['coreTable'], $data, $keyFields, $autoQuote = false) ) === 0) {
			$this->logger->error( 'Cannot save Lingap grant information: '.$db->ErrorMsg().'...Data:'.print_r($data, true). '...Keys: '.print_r($keyFields, true) );
			return false;
		}

		if ($grant_amount < $amount_due) {
			$this->logger->warn('Amount not fully granted (grant amount less than amount due). Remaining amount: '.$running_amount);
		}
		else {
			$this->logger->info("Attempting to flag request...\nkey:".print_r($request->getKeys(), true));
			$ok = $request->flag('lingap');
			if (!$ok) {
				return false;
			}
			else {
				$this->logger->error( 'Cannot flag request: '.$db->ErrorMsg().'...Data:'.print_r($data, true). '...Keys: '.print_r($keys, true) );
			}
		}

		return true;
	}



	/**
	* put your comment there...
	*
	* @param SegRequest $request
	* @param mixed $amount
	*/
	public function ungrant(SegRequest $request) {
		global $db;

		if (!$this->referral) {
			// need to specify a referral for this method
			return false;
		}

		$types = SegRequest::getRequestTypes();
		$this->logger->info("Attempting to UNgrant LINGAP...type:".$types[$request->getRequestType()]."\nkey:".print_r($request->getKeys(), true));
		$builder = $this->get_builder_def( $request->getRequestType() );


		$where = array();
		foreach ($request->getKeys() as $key=>$value) {
			$expr = QueryBuilder::express($builder, 'EQ', $key, $value);
			if ($expr) $where[] = $expr;
		}
		$expr = QueryBuilder::express($builder, 'EQ', 'entryId', $this->referral->getId());
		if ($expr) $where[] = $expr;

		$query = "DELETE FROM ".$builder['coreTable']." WHERE (".implode(") AND (", $where).")";
		$ok = $this->setResult( $db->Execute($this->setQuery($query)) );
		if ($ok === false) {
			$this->logger->error('Failed to delete Lingap entry detail: '.$db->ErrorMsg()."\nQuery: ".$query);
			return false;
		}

		if (!$request->unflag()) {
			return false;
		}

		return true;
	}




	/**
	* Get the grant items for the Lingap referral entry
	*
	* @param SegRequest $request
	* @return array
	*/
	public function getGrants(SegRequest $request=null) {
		global $db;

		if (!$this->referral) {
			// need to specify a referral for this method
			return false;
		}

		if ($request) {
			/**
			*  @todo get Grant details for a specific request
			*/
		}
		else {
			$types = SegRequest::getRequestTypes();
			$type_keys = array_keys($types);

			$grants = Array();
			foreach ($type_keys as $type) {

				$builder = $this->get_builder_def($type);

				if ($builder) {
					$builder['fields']['type'] = $db->qstr($type);
					$builder['where'][] = QueryBuilder::express($builder, 'EQ', 'entryId', $this->referral->getId());
					$query = QueryBuilder::build($builder, $calcFoundRows=false);
					if ($query) {
						$this->setQuery($query);
						$db->SetFetchMode(ADODB_FETCH_ASSOC);
						$rs = $db->Execute($this->getQuery());
						if ($rs !== false) {
							$result = $rs->GetRows();
							$grants = array_merge( $grants, $result);
						}
						else {
							$this->logger->error("SQL error: ".$db->ErrorMsg()."\nQuery: ".$this->getQuery());
							return false;
						}
					}
				}
				else {
					$this->logger->error("Invalid builder def. type:".$type);
					return false;
				}
			}

			return $grants;
		}
	}



	/**
	* put your comment there...
	*
	* @param SegRequest $request
	* @return ADODB
	*/
	public function getTotalGrants(SegRequest $request) {
		global $db;

		$builder = $this->get_builder_def( $request->getRequestType() );
		if (!$builder) {
			$this->logger->info( 'Cannot process request of this type: '. $request->getRequestType() );
			return 0;
		}

		$sql = "SELECT SUM(".$builder['fields']['totalAmount'].") FROM {$builder['coreTable']} ";
		$where = array();

		foreach ($request->getKeys() as $key=>$value) {
			$expr = QueryBuilder::express($builder, 'EQ', $key, $value);
			if ($expr) $where[] = $expr;
		}

		if ($this->referral) {
			$expr = QueryBuilder::express($builder, 'EQ', 'entryId', $this->referral->getId());
			if ($expr) $where[] = $expr;
		}

		if ($where) {
			$sql.=" WHERE (".implode(") AND (", $where).")";
		}
		$this->setQuery($sql);
		$this->setResult($db->GetOne($this->getQuery()));

		if ($this->getResult() === false) {
			$this->logger->error("Cannot retrieve amount! Error: ".$db->ErrorMsg()."\nQuery: ".$this->getQuery());
		}
		return $this->getResult();
	}





	/**
	* put your comment there...
	*
	* @param mixed $request_type
	*/
	public function get($request_type, $rows=-1, $offset=-1) {
		global $db;

		$builder = $this->get_builder_def( $request_type );
		$builder['where'][] = array("EQ", "entryId", $this->referral->getId());
		$query = QueryBuilder::build($builder);
		if ($query) {
			$this->setQuery($query);

			$db->SetFetchMode(ADODB_FETCH_ASSOC);
			$rs = $db->SelectLimit($this->getQuery(), $rows, $offset);
			if ($rs !== false) {
				$result = $rs->GetRows();
				if ($get_row) {
					$result = current($result);
				}
				$this->setResult($result);
				if (!count($this->getResult())) {
					//$this->logger->info('Zero rows fetched...'.$this->getQuery());
				}
				return $this->getResult();
			}
			else {
				$this->logger->error("SQL error: ".$db->ErrorMsg()."\nQuery: ".$this->getQuery());
				return false;
			}
		}
		else {
			$this->logger->error("Unable to build query! Build array:".var_export($builder_array, true));
			return false;
		}
	}




	/**
	* put your comment there...
	*
	* @access private
	* @param mixed $request_type
	*/
	private function get_builder_def($request_type) {
		$builder = Array(
			'coreTable' => self::$GrantSources[$request_type],
			'joins' 		=> Array(),
			'fields' 		=> Array(
				'entryId' 		=> 'entry_id',
				'refNo' 			=> 'ref_no',
				'unitPrice' 	=> 'amount/quantity',
				'totalAmount' => 'amount'
			),
			'orderBy'		=> Array(),
			'where' 		=> Array()
		);

		switch($request_type) {
			case SegRequest::MISC_REQUEST:
				$builder['fields']['entryNo'] = 'entry_no';
			case SegRequest::PHARMACY_REQUEST:
			case SegRequest::PHARMACY_WALKIN_REQUEST:
			case SegRequest::RADIOLOGY_REQUEST:
			case SegRequest::LABORATORY_REQUEST:
				$builder['fields']['itemNo'] 		= 'service_code';
				$builder['fields']['itemName'] 	= 'service_name';
				$builder['fields']['quantity'] 	= 'quantity';
				return $builder;
			case SegRequest::BILLING_REQUEST:
				$builder['fields']['unitPrice'] = 'amount';
				$builder['fields']['quantity'] 	= '1';
				return $builder;
			default:
				return false;
		}
	}

}

