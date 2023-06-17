<?php
require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCmapRequest extends Core {

	const BILLING_REQUEST 		= 1001;
	const PHARMACY_REQUEST 		= 1002;
	const LABORATORY_REQUEST 	= 1003;
	const RADIOLOGY_REQUEST 	= 1004;
	const OR_REQUEST 					= 1005;
	const MISC_REQUEST 				= 1006;


	private $keys = null;
	private $requestType = null;

	/**
	* @var String Id of the Logger object to be used for logging purposes
	* @access Private
	*/
	private static $loggerName='sponsor.cmap';

	/**
	* @var Array The available request types and short descriptions
	*/
	private static $requestTypes = Array(
		SegCmapRequest::BILLING_REQUEST 		=> 'BILLING',
		SegCmapRequest::PHARMACY_REQUEST 		=> 'PHARMACY',
		SegCmapRequest::LABORATORY_REQUEST 	=> 'LABORATORY',
		SegCmapRequest::RADIOLOGY_REQUEST 	=> 'RADIOLOGY',
		SegCmapRequest::OR_REQUEST 					=> 'OPERATING ROOM',
		SegCmapRequest::MISC_REQUEST 				=> 'OTHER SERVICES'
	);

	/**
	* @var Array Table names where grant data is stored as per request
	* @access Private
	*/
	private static $grantingTable = Array(
		SegCmapRequest::BILLING_REQUEST 		=> 'seg_cmap_entries_bill',
		SegCmapRequest::PHARMACY_REQUEST 		=> 'seg_cmap_entries_pharmacy',
		SegCmapRequest::LABORATORY_REQUEST 	=> 'seg_cmap_entries_laboratory',
		SegCmapRequest::RADIOLOGY_REQUEST 	=> 'seg_cmap_entries_radiology',
		SegCmapRequest::OR_REQUEST 					=> 'seg_cmap_entries_or',
		SegCmapRequest::MISC_REQUEST 				=> 'seg_cmap_entries_misc'
	);

	/**
	* Default constructor
	*
	*/
	public function __construct($requestType, $keys=null) {
		$this->requestType = (int) $requestType;
		if ($keys) {
			$this->keys = $keys;
		}
		$this->setupLogger(self::$loggerName);
	}




	private function _build_query_expr() {
		global $db;

		$builderArray = $this->_get_builder_array($this->requestType);
		$args = func_num_args();
		list($operation, $field, $value) = func_get_args();

		if ($builderArray['fields'][$field]) {
			$field = $builderArray['fields'][$field];
		}
		else {
			return false;
		}

		switch($operation) {
			case 'TRUE':
				return "1=1";
			case 'FALSE':
				return "1=0";
			case 'EQ':
				return "$field=".$db->qstr($value);
			case 'NOTEQ':
				return "$field<>".$db->qstr($value);
			case 'LIKE':
				return "$field LIKE ".$db->qstr($value);
			case 'REGEXP':
				return "$field REGEXP ".$db->qstr($value);
			default:
				return false;
		}
	}




	private function _get_builder_array($request_type) {

		switch($request_type) {
			case SegCmapRequest::BILLING_REQUEST:
				return Array(
					'coreTable' => 'seg_billing_encounter b',
					'joins' 		=> Array("INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr"),
					'fields' 		=> Array(
						'refNo' 		=> 'b.bill_nr',
						'date' 			=> 'b.bill_dte',
						'pid'				=> 'e.pid',
						'walkinPid'	=> 'NULL',
						'itemNo' 		=> 'b.bill_nr',
						'itemName' 	=> 'CONCAT("Hospital bill (",fn_get_person_lastname_first(e.pid),")")',
						'unitPrice' => 'fn_compute_bill(b.bill_nr)',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'b.request_flag'
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			case SegCmapRequest::PHARMACY_REQUEST:
				return Array(
					'coreTable' => 'seg_pharma_order_items oi',
					'joins' 		=> Array(
						"INNER JOIN seg_pharma_orders o ON o.refno=oi.refno",
						"LEFT JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'o.refno',
						'date' 			=> 'o.orderdate',
						'pid'				=> 'o.pid',
						'walkinPid'	=> 'o.walkin_pid',
						'itemNo' 		=> 'oi.bestellnum',
						'itemName' 	=> 'p.artikelname',
						'unitPrice' => 'IF(o.is_cash, oi.pricecash,oi.pricecharge)',
						'quantity' 	=> 'oi.quantity',
						'totalDue' 	=> '0',
						'requestFlag' => 'oi.request_flag'
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			case SegCmapRequest::RADIOLOGY_REQUEST:
				return Array(
					'coreTable' => 'care_test_request_radio rd',
					'joins' 		=> Array(
						"INNER JOIN seg_radio_serv r ON r.refno=rd.refno",
						"LEFT JOIN seg_radio_services s ON s.service_code=rd.service_code"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'r.refno',
						'date' 			=> 'r.request_date',
						'pid'				=> 'r.pid',
						'walkinPid'	=> 'NULL',
						'itemNo' 		=> 'rd.service_code',
						'itemName' 	=> 's.name',
						'unitPrice' => 'IF(r.is_cash,rd.price_cash,rd.price_charge)',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'rd.request_flag'
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			case SegCmapRequest::RADIOLOGY_REQUEST:
				return Array(
					'coreTable' => 'seg_lab_servdetails ld',
					'joins' 		=> Array(
						"INNER JOIN seg_lab_serv l ON l.refno=ld.refno",
						"LEFT JOIN seg_lab_services s ON s.service_code=ld.service_code"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'l.refno',
						'date' 			=> 'l.serv_dt',
						'pid'				=> 'l.pid',
						'walkinPid'	=> 'NULL',
						'itemNo' 		=> 'ld.service_code',
						'itemName' 	=> 's.name',
						'unitPrice' => 'IF(l.is_cash,ld.price_cash,ld.price_charge)',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'ld.request_flag'
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			default:
				$this->logger->warn('Invalid request type passed to builder function:'.var_export($reqest_type, true));
				return false;
			break;
		}
	}




	/**
	* put your comment there...
	*
	* @param mixed $builderArray
	* @param mixed $calculateFoundRows
	* @param mixed $whereCondition
	* @param mixed $orderByClause
	* @return mixed
	*/
	private function _build_fetch_query( $builderArray, $calculateFoundRows=false ) {
		global $db;
		$query = "SELECT\n";
		if ($calcFoundRows) {
			$query.="SQL_CALC_FOUND_ROWS\n";
		}
		if (!is_array($builderArray) || !count($builderArray)) {
			$this->logger->warn('Invalid builder array passed to fetch function: '.var_export($builderArray, true));
			return '';
		}
		if (is_array($builderArray['fields'])) {
			$fields = array();
			foreach ($builderArray['fields'] as $field=>$value) {
				$fields[] = "$value `$field`";
			}
			$query.=implode(",", $fields);
		}
		else {
			// return NULL if no fields are specified
			$this->logger->warn('Invalid builder array passed to fetch function...'.var_export($builderArray, true));
			$query.="NULL\n";
		}
		if ($builderArray['coreTable']) {
			$query.="FROM ".$builderArray['coreTable']."\n";
			if (is_array($builderArray['joins'])) {
				foreach ($builderArray['joins'] as $join) {
					$query.=$join."\n";
				}
			}
			$where = array();
			if ($builderArray['where']) {
				foreach ($builderArray['where'] as $expr) {
					if (is_array($expr)) {
						list($op, $fieldId, $value) = $expr;
						if ($queryExpr = $this->_build_query_expr($op, $fieldId, $value)) {
							$where[] = $queryExpr;
						}
					}
					else {
						$where[] = (string) $expr;
					}
				}
			}

			if ($where) {
				$query.="WHERE (".implode(") AND (", $where).")\n";
			}
			if ($builderArray['orderBy']) {
				$query.="ORDER BY {$builderArray['orderBy']}\n";
			}
		}
		return $query;
	}




	public static function getRequestTypes() {
		return SegCmapRequest::$requestTypes;
	}

	/**
	* Retrieves the request list
	*
	* @param mixed $fetchType
	* @param mixed $options
	* @return ADODB
	*/
	public function get( $options ) {
		global $db;
		$rows = -1;
		$limit = -1;

		$requestType = $this->requestType;
		$builder_array = $this->_get_builder_array($requestType);
		if (!$builder_array) {
			return false;
		}

		$where = array();


		foreach ($options as $key=>$value ) {
			switch($key) {
				case 'PID':
					$builder_array['where'][] = array('EQ', 'pid', $value);
					break;
				case 'DATE':
					$builder_array['where'][] = array('EQ', 'date', date('Y-m-d',strtotime($value)));
					break;
				case 'NAME':
					$builder_array['where'][] = array('REGEXP', 'name', "[[:<:]]".$value);
					break;
				case 'REFERENCE':
					$builder_array['where'][] = array('EQ', 'refNo', $value);
					break;
				case 'ITEM':
					$builder_array['where'][] = array('EQ', 'itemNo', $value);
					break;
				case 'WHERE':
						if (is_array($value)) {
							foreach ($value as $key=>$val) {
								if ($builder_array['fields'][$key]) {
									$builder_array['where'][] = array('EQ', $key, $val);
								}
							}
						}
					break;
				case 'SORT':
					$builder_array['orderBy'] = $value;
					break;
				case 'ROWS':
					$rows = $value;
					break;
				case 'OFFSET':
					$offset = $value;
					break;
			}
		}

		$query = $this->_build_fetch_query($builder_array, $calcFoundRows=true);
		if ($query) {
			$this->setQuery($query);

			$db->SetFetchMode(ADODB_FETCH_ASSOC);
			$rs = $db->SelectLimit($this->getQuery(), $rows, $offset);
			if ($rs !== false) {
				$this->setResult($rs->GetRows());
				if (!count($this->getResult())) {
					$this->logger->info('Zero rows fetched...'.var_export($this->getResult(), true));
				}
				return $this->getResult();
			}
			else {
				$this->logger->error("SQL error! Error:".$db->ErrorMsg()."\nQuery:".$this->getQuery());
				return false;
			}
		}
		else {
			$this->logger->error("Unable to build query! Build Array:".print_r($buildArray, true));
			return false;
		}
	}


	/**
	* put your comment there...
	*
	*/
	public function fetch() {
		return $this->get(
			Array(
				'WHERE' => $this->keys,
				'ROWS' => 1,
				'OFFSET' => 0
			)
		);

	}

	/**
	* put your comment there...
	*
	*/
	public function getAmountGranted() {
		global $db;

		$builder_array = $this->_get_builder_array($this->requestType);
		$grantTable = self::$grantingTable[ $this->requestType ];
		$sql = "SELECT SUM(amount) FROM $grantTable ";
		$where = array();
		foreach ($this->keys as $key=>$value) {
			$where[] = $this->_build_query_expr('EQ', $key, $value);
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
	*/
	public function grant() {

	}


	/**
	* put your comment there...
	*
	*/
	public function getKeys() {
		return $this->keys;
	}

	/**
	*
	*/
	public function getRequestType() {
		return $this->requestType;
	}
}


