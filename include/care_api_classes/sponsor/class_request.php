<?php
/**
 * @package sponsor
 */

require "./roots.php";
require_once $root_path.'include/care_api_classes/class_core.php';
require_once $root_path.'include/care_api_classes/sponsor/helpers/QueryBuilder.php';
require_once $root_path.'include/care_api_classes/sponsor/class_grant.php';

class SegRequest extends Core {

	const BILLING_REQUEST 						= 1001;
	const PHARMACY_REQUEST 						= 1002;
	const LABORATORY_REQUEST 					= 1003;
	const RADIOLOGY_REQUEST 					= 1004;
	const OR_REQUEST 									= 1005;
	const MISC_REQUEST 								= 1006;
	const PHARMACY_WALKIN_REQUEST 		= 1011;
	const IC_BILLING_REQUEST  				= 1012;       // added by cha, september 7, 2010
	const MDC_REQUEST  								= 1013;       // added by cha, september 13, 2010
	const DOCTOR_REQUEST 						= 1014; //added by ken 2/24/2014

	const EXTERNAL_PHARMACY_REQUEST 	= 2002;
	const EXTERNAL_LABORATORY_REQUEST = 2003;
	const EXTERNAL_RADIOLOGY_REQUEST 	= 2004;



//	const FLAG_NONE 					= 0;
//	const FLAG_PAID 					= 1001;
//	const FLAG_CMAP 					= 1002;
//	const FLAG_LINGAP 				= 1003;


	private $requestType 	= null;
	private $keys 				= null;
	private $builder 			= null;

	/**
	 * @var String Id of the Logger object to be used for logging purposes
	 * @access Private
	 */
	private static $loggerName='sponsor';

	/**
	 * @var Array The available request types and short descriptions
	 */
	private static $requestTypes = Array(
		self::BILLING_REQUEST => 'BILLING',
		self::DOCTOR_REQUEST => 'DOCTOR',
		self::PHARMACY_REQUEST => 'PHARMACY',
		self::LABORATORY_REQUEST => 'LABORATORY',
		self::RADIOLOGY_REQUEST => 'RADIOLOGY',
		//self::OR_REQUEST => 'OPERATING ROOM',
		self::MISC_REQUEST => 'OTHER SERVICES',
		self::PHARMACY_WALKIN_REQUEST => 'PHARMACY (WALK-IN)',
		//self::IC_BILLING_REQUEST => 'IC BILLING',
		//self::MDC_REQUEST => 'DIALYSIS',
		//self::EXTERNAL_LABORATORY_REQUEST => 'OUTSIDE LABORATORY',
		//self::EXTERNAL_RADIOLOGY_REQUEST 	=> 'OUTSIDE RADIOLOGY',
		//self::EXTERNAL_PHARMACY_REQUEST 	=> 'OUTSIDE PHARMACY',
	);



	/**
	 * Default constructor
	 *
	 */
	public function __construct($requestType, $keys=null) {
		$this->setupLogger(self::$loggerName);
		$this->requestType = (int) $requestType;
		$this->builder = $this->get_builder_def($this->requestType);
		if (!$this->builder) {
			$this->logger->error("Unable to create builder array! Type:".var_export($this->requestType, true));
		}

		if ($keys) {
			// @todo Trim keys based on core table metadata
			$this->keys = $keys;
		}
	}



	public static function getRequestTypes() {
		return self::$requestTypes;
	}




	public function getBuilderQuery( $options ) {

	}

	/**
	 * Retrieves the request list
	 *
	 * @param mixed $fetchType
	 * @param mixed $options
	 * @return mixed
	 */
	public function get( $options ) {
		global $db;
		$rows = -1;
		$limit = -1;

		$requestType = $this->requestType;
		$builder_array = $this->builder;
		if (!$builder_array) {
			return false;
		}

		$where = array();

		// fetch only single row?
		$get_row = false;

		$include_fields = null;
		foreach ($options as $key=>$value ) {
			switch($key) {
				// behavior options
				case 'GETROW':
					if ($value) {
						$get_row = true;
					}
					break;

				// filtering options
				case 'PID':
					if ($value) {
						if($value[0]=='W')
							$builder_array['where'][] = array('EQ', 'walkinPid', substr($value,1));
						else
							//$builder_array['where'][] = array('EQ', 'pid', substr($value,1));
							$builder_array['where'][] = array('EQ', 'pid', $value);
					}
					break;
				case 'CASH':
					$builder_array['where'][] = array('EQ', 'isCash', ($value == true));
					break;
				case 'ENCOUNTER':
					if ($value) {
						$builder_array['where'][] = array('EQ', 'encounterNr', $value);
					}
					break;
				case 'DATE':
					if ($value) {
						$builder_array['where'][] = array('DATE', 'date', date('Y-m-d',strtotime($value)));
					}
					break;
				case 'INTERVALFROMDATE':
					if ($value) {
						$builder_array['where'][] = array('INTERVALFROMDATE', 'date', $value);
					}
					break;
				case 'INTERVAL':
					if ($value) {
						$builder_array['where'][] = array('INTERVAL', 'date', array( $value[0], $value[1] ));
					}
					break;
				case 'DATEBETWEEN':	//added by cha, 11-12-2010
					if ($value) {
						$builder_array['where'][] = array(
							'DATEBETWEEN',
							'date',
							array(
								date('Y-m-d',strtotime($value[0])),
								date('Y-m-d',strtotime($value[1]))
							)
						);
					}
					break;
				case 'MISC_DEPT':	//added by cha, 11-26-2010
					if ($value) {
						$builder_array['where'][] = array('EQ', 'dept_nr', $value);
					}
					break;
				case 'FLAG':
					$builder_array['where'][] = array('EQ', 'requestFlag', $value);
					break;
				case 'NAME':
					if ($value) {
						$builder_array['where'][] = array('REGEXP', 'name', "[[:<:]]".$value);
					}
					break;
				case 'REFERENCE':
					if ($value) {
						$builder_array['where'][] = array('EQ', 'refNo', $value);
					}
					break;
				case 'ITEM':
					if ($value) {
						$builder_array['where'][] = array('EQ', 'itemNo', $value);
					}
					break;
				case 'DELETED':
						$builder_array['where'][] = array('EQ', 'isDeleted', $value);
					break;
				case 'SERVED':
						$builder_array['where'][] = array('EQ', 'isServed', $value);
					break;
				case 'DISCOUNT':
						$builder_array['where'][] = array('EQ', 'discountId', $value);
					break;

				// data manipulation options
				case 'FIELDS':
					if ($value) {
						if (!is_array($value))
							$value = (array) $value;
						$builder_array['selectMask'] = $value;
					}
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
					if ($value) {
						$builder_array['orderBy'] = $value;
					}
					break;
				case 'ROWS':
					$rows = (int) $value;
					break;
				case 'OFFSET':
					$offset = (int) $value;
					break;
			}
		}

		$query = QueryBuilder::build($builder_array, $calcFoundRows=true);

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
					//$this->logger->warn('Zero rows fetched...'.$this->getQuery());
					// empty
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
	 */
	public function getTotalGrants() {
		$grantor = SegGrant::getInstance( );
		return $grantor->getTotalGrants( $this );
	}


	/**
	 * put your comment there...
	 *
	 * @param mixed $flag
	 */
	public function flag($flag=null) {
		global $db, $HTTP_SESSION_VARS;

		$builder = $this->builder;
		$where = array();

		foreach ($this->keys as $key=>$val) {
			$expr = QueryBuilder::express($builder, 'EQ', $key, $val);
			if ($expr) $where[] = $expr;
		}

		// disallow flagging of served requests
		$query = "SELECT ".$builder['fields']['requestFlag']."\n".
			"FROM ".$builder['coreTable']."\n".
			"WHERE (".implode(") AND (", $where).")";
		$served = $db->GetOne( $query );
		if ($served == '1') {
			$this->logger->warn('Attempting to flag SERVED request:'.$query );
			return false;
		}

        if ($builder['coreTable']=='seg_lab_servdetails ld'){
            $history = $this->ConcatHistory("Update request_flag [".$flag."] -class_request ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
            $history_cond = ", history=".$history."\n";
        }
		$query = "UPDATE ".$builder['coreTable']." SET ".$builder['fields']['requestFlag']."=".$db->qstr($flag).$history_cond." WHERE (".implode(") AND (", $where).") LIMIT 1";
		$ok = $this->setResult( $db->Execute($this->setQuery($query)) );
		if ($ok === false) {
			$this->logger->error('Failed to flag request: '.$db->ErrorMsg()."\nQuery: ".$query);
			return false;
		}
		return true;
	}


	/**
	 * put your comment there...
	 *
	 * @param mixed $flag
	 */
	public function unflag() {
		global $db;
		$builder = $this->builder;
		$where = array();
		foreach ($this->keys as $key=>$val) {
			$expr = QueryBuilder::express($builder, 'EQ', $key, $val);
			if ($expr) $where[] = $expr;
		}


		// disallow unflagging of served requests
		$query = "SELECT ".$builder['fields']['requestFlag']."\n".
			"FROM ".$builder['coreTable']."\n".
			"WHERE (".implode(") AND (", $where).")";
		$served = $db->GetOne( $query );
		if ($served == '1') {
			$this->logger->warn('Attempting to flag SERVED request:'.$query );
			return false;
		}

		$query = "UPDATE ".$builder['coreTable']." SET ".$builder['fields']['requestFlag']."=NULL WHERE (".implode(") AND (", $where).") LIMIT 1";
		$ok = $this->setResult( $db->Execute($this->setQuery($query)) );
		if ($ok === false) {
			$this->logger->error('Failed to unflag request: '.$db->ErrorMsg()."\nQuery: ".$query);
			return false;
		}
		return true;
	}


	/**
	 * Retrieves a single row of request data based on the Keys passed upon instantiation
	 *
	 * An optional <code>mask</code> can be specified, which limits the fields returned by
	 * the query.
	 *
	 * @param Array $mask An array of field Ids specifying the specific fields to be returned by the query
	 */
	public function fetch($mask = null) {
		return $this->get(
			Array(
				'FIELDS' => $mask,
				'GETROW' => true,
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
	public function getKeys() {
		return $this->keys;
	}

	/**
	 *
	 */
	public function getRequestType() {
		return $this->requestType;
	}






	private function get_builder_def($request_type) {
		switch($request_type) {
			case self::BILLING_REQUEST:
				return Array(
					'coreTable' => 'seg_billing_encounter b',
					'joins' 		=> Array("INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr"),
					'fields' 		=> Array(
						//'refNo' 		=> 'b.bill_nr',
						'date' 			=> 'b.bill_dte',
						'isCash' => '1',
						'pid'				=> 'e.pid',
						'encounterNr'	=> 'e.pid',
						'itemNo' 		=> 'b.bill_nr',
						'itemName' 	=> "'Hospital bill'",
						'description' => 'fn_get_person_lastname_first(e.pid)',
						'unitPrice' => 'fn_compute_bill(b.bill_nr)',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'b.request_flag',
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			//added by ken 2/24/2014 for changing the flag of doctor bill
			case self::DOCTOR_REQUEST:
				return Array(
					'coreTable' => 'seg_billing_encounter b',
					'joins' 		=> Array("INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr"),
					'fields' 		=> Array(
						'date' 			=> 'b.bill_dte',
						'isCash' => '1',
						'pid'				=> 'e.pid',
						'encounterNr'	=> 'e.pid',
						'itemNo' 		=> 'b.bill_nr',
						'itemName' 	=> "'Hospital bill'",
						'description' => 'fn_get_person_lastname_first(e.pid)',
						'unitPrice' => 'fn_compute_bill(b.bill_nr)',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'b.doctor_flag',
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			case self::PHARMACY_REQUEST:
				return Array(
					'coreTable' => 'seg_pharma_order_items oi',
					'joins' 		=> Array(
						"INNER JOIN seg_pharma_orders o ON o.refno=oi.refno",
						"INNER JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'oi.refno',
						'date' 			=> 'o.orderdate',
						'isCash' => 'o.is_cash',
						'pid'				=> 'o.pid',
						'walkinPid'	=> 'o.walkin_pid',
						'encounterNr'				=> 'o.encounter_nr',
						'itemNo' 		=> 'oi.bestellnum',
						'itemName' 	=> 'p.artikelname',
						'description' => 'p.generic',
						'unitPrice' => 'IF(o.is_cash, oi.pricecash,oi.pricecharge)',
						'quantity' 	=> 'oi.quantity',
						'totalDue' 	=> '0',
						'requestFlag' => 'oi.request_flag',
						'isServed'	=> 'oi.serve_status=\'S\'',
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			case self::RADIOLOGY_REQUEST:
				return Array(
					'coreTable' => 'care_test_request_radio rd',
					'joins' 		=> Array(
						"INNER JOIN seg_radio_serv r ON r.refno=rd.refno",
						"INNER JOIN seg_radio_services s ON s.service_code=rd.service_code",
						"INNER JOIN seg_radio_service_groups g ON g.group_code=s.group_code"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'rd.refno',
						'date' 			=> "CONCAT(r.request_date,' ',r.request_time)",
						'pid'				=> 'r.pid',
						'isCash' => 'r.is_cash',
						'encounterNr'				=> 'r.encounter_nr',
						'itemNo' 		=> 'rd.service_code',
						'itemName' 	=> 's.name',
						'description' => 'g.name',
						'unitPrice' => 'IF(r.is_cash,rd.price_cash,rd.price_charge)',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'rd.request_flag',
						'isServed'	=> 'rd.status=\'done\'',
						'discountId' => 'r.discountid',
						'isDeleted' => "rd.status IN ('deleted','hidden','inactive','void')"
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			case self::LABORATORY_REQUEST:
				return Array(
					'coreTable' => 'seg_lab_servdetails ld',
					'joins' 		=> Array(
						"INNER JOIN seg_lab_serv l ON l.refno=ld.refno",
						"INNER JOIN seg_lab_services s ON s.service_code=ld.service_code",
						"INNER JOIN seg_lab_service_groups g ON g.group_code=s.group_code"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'ld.refno',
						'date' 			=> "CONCAT(l.serv_dt,' ',l.serv_tm)",
						'isCash' => 'l.is_cash',
						'pid'				=> 'l.pid',
						'encounterNr'				=> 'l.encounter_nr',
						'itemNo' 		=> 'ld.service_code',
						'itemName' 	=> 's.name',
						'description' => 'g.name',
						'unitPrice' => 'IF(l.is_cash,ld.price_cash,ld.price_charge)',
						'quantity' 	=> 'ld.quantity',
						'totalDue' 	=> '0',
						'requestFlag' => 'ld.request_flag',
						'isServed'	=> 'ld.is_served',
						'discountId' => 'l.discountid',
						'isDeleted' => "ld.status IN ('deleted','hidden','inactive','void')"
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			case self::MISC_REQUEST:
				return Array(
					//'coreTable' => 'seg_misc_chrg_details md',		//modified by cha, July 7, 2010
					'coreTable' => 'seg_misc_service_details md',
					'joins' 		=> Array(
						//"INNER JOIN seg_misc_chrg m ON m.refno=md.refno",    //modified by cha, July 7, 2010
						"INNER JOIN seg_misc_service m ON m.refno=md.refno",
						"INNER JOIN care_encounter e ON e.encounter_nr=m.encounter_nr",
						//"LEFT JOIN seg_other_services s ON s.service_code=md.service_code",  //modified by cha, July 7, 2010
						"INNER JOIN seg_other_services s ON s.alt_service_code=md.service_code",
						"INNER JOIN seg_cashier_account_subtypes t ON s.account_type=t.type_id",
						"LEFT JOIN care_department d ON d.nr=s.dept_nr"	//added by cha, 11-26-2010
					),
					'fields' 		=> Array(
						'refNo' 		=> 'md.refno',
						'date' 			=> "m.chrge_dte",
						'isCash' => 'm.is_cash',
						'pid'				=> 'e.pid',
						'encounterNr'	=> 'm.encounter_nr',
						'entryNo' 	=> 'md.entry_no',
						'itemNo' 		=> 'md.service_code',
						'itemName' 	=> 's.name',
						'description' => 't.name_long',
						'unitPrice' => 'IF(m.is_cash,md.adjusted_amnt,md.chrg_amnt)',
						'quantity' 	=> 'md.quantity',
						'totalDue' 	=> '0',
						'requestFlag' => 'md.request_flag',
						"dept_nr"=> 's.dept_nr',	//added by cha, 11-26-2010
                        'discountId' => 'm.discountid'//added by Jarel 04/12/2013
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);

			case self::PHARMACY_WALKIN_REQUEST:
				return Array(
					'coreTable' => 'seg_pharma_order_items oi',
					'joins' 		=> Array(
						"INNER JOIN seg_pharma_orders o ON o.refno=oi.refno",
						"LEFT JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'oi.refno',
						'date' 			=> 'o.orderdate',
						'isCash' => '1',
						'pid'				=> 'o.walkin_pid',
						'itemNo' 		=> 'oi.bestellnum',
						'itemName' 	=> 'p.artikelname',
						'description' => 'p.generic',
						'unitPrice' => 'IF(o.is_cash, oi.pricecash,oi.pricecharge)',
						'quantity' 	=> 'oi.quantity',
						'totalDue' 	=> '0',
						'requestFlag' => 'oi.request_flag',
						'isServed'	=> 'oi.serve_status=\'S\'',

					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);

			//added by cha, september 5, 2010
			case self::OR_REQUEST:
				return Array(
					'coreTable' => 'seg_or_main op',
					'joins' 		=> Array(
						"INNER JOIN care_encounter e ON e.encounter_nr=op.encounter_nr",
						"LEFT JOIN seg_packages p ON p.package_id=op.procedure_id",
					),
					'fields' 		=> Array(
						'refNo' 		=> 'op.or_main_refno',
						'date' 			=> "op.date_request",
						'isCash' => '1',
						'pid'				=> 'e.pid',
						'itemNo' 		=> 'op.procedure_id',
						'itemName' 	=> 'p.package_name',
						'unitPrice' => 'p.package_price',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'op.request_flag',
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			//end cha

			case self::IC_BILLING_REQUEST:
				return Array(
					'coreTable' => 'seg_industrial_bill_h bh',
					'joins' 		=> Array(
							"INNER JOIN seg_industrial_bill_d bd ON bd.bill_nr=bh.bill_nr",
							"INNER JOIN seg_industrial_company c ON c.company_id=bh.company_id"
					),
					'fields' 		=> Array(
						//'refNo' 		=> 'bh.bill_nr',
						'date' 			=> 'bh.cutoff_date',
						'isCash' => '1',
						'encounterNr'	=> 'bd.encounter_nr',
						'itemNo' 		=> 'bh.bill_nr',
						'itemName' 	=> "'Hospital bill'",
						'description' => 'c.name',
						'unitPrice' => '(bd.total_med_charge+bd.total_msc_charge+bd.total_srv_charge+bd.total_sup_charge)',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'bh.request_flag',
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);

				//added by cha, september 5, 2010
			case self::MDC_REQUEST:
				return Array(
					'coreTable' => 'seg_dialysis_request_details dt',
					'joins' 		=> Array(
						"INNER JOIN seg_dialysis_request d ON d.refno=dt.refno",
						"INNER JOIN seg_dialysis_package as p ON p.id=dt.package_id"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'dt.refno',
						'date' 			=> "d.request_date",
						'isCash' => '1',
						'pid'				=> 'd.pid',
						'itemNo' 		=> 'dt.package_id',
						'itemName' 	=> 'p.name',
						'unitPrice' => 'dt.amount',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'dt.request_flag',
						'isServed'	=> '0'
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);
			//end cha


			case self::EXTERNAL_LABORATORY_REQUEST:
				return Array(
					'coreTable' => 'seg_dialysis_request_details dt',
					'joins' 		=> Array(
						"INNER JOIN seg_dialysis_request d ON d.refno=dt.refno",
						"INNER JOIN seg_dialysis_package as p ON p.id=dt.package_id"
					),
					'fields' 		=> Array(
						'refNo' 		=> 'dt.refno',
						'date' 			=> "d.request_date",
						'isCash' => '1',
						'pid'				=> 'd.pid',
						'itemNo' 		=> 'dt.package_id',
						'itemName' 	=> 'p.name',
						'unitPrice' => 'dt.amount',
						'quantity' 	=> '1',
						'totalDue' 	=> '0',
						'requestFlag' => 'dt.request_flag',
					),
					'orderBy'		=> Array(),
					'where' 		=> Array()
				);

			default:
				@$this->logger->warn('Invalid request type passed to builder function:'.var_export($request_type, true));
				return false;
			break;
		}
	}
}