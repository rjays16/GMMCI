<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_referral.php');
require_once($root_path.'include/care_api_classes/sponsor/class_cmap_account.php');

class SegCmapPatient extends Core {

	private $loggerName = 'sponsor.cmap';
	private $PID;
	private $WALKIN_PID;
	private $TYPE;

	/**
	* Constructor
	*
	*/
	public function __construct($type='pid', $value = null) {
		global $db;
		$this->setupLogger($this->loggerName);
		$this->TYPE = $type;
		if ($this->TYPE=='pid') {
			$this->PID = $value;
		}
		else if($this->TYPE=='walkin') {
			$this->WALKIN_PID = $value;
		}
	}



	public function getBalance() {
		$referral = new SegCmapReferral;
		if($this->TYPE=='pid') {
			$total = $referral->total(array('PID'=>$this->PID));
			$this->setQuery($referral->getQuery());
			return $total;
		}
		else if($this->TYPE=='walkin') {
			$total = $referral->total(array('WALKIN_PID'=>$this->WALKIN_PID));
			$this->setQuery($referral->getQuery());
			return $total;
		}
	}


	public function getCmapGrants() {

	}
























	/**
	* Retrieve current ledger entries for the patient
	*
	* @param mixed $filters
	* @return ADODB
	*/
//	function getLedger($filters) {
//		global $db, $errorReporter;

//		$offset = 0;
//		$rowcount = 15;
//		$sortSQL = "p.entry_date DESC";

//		$where = array();

//		if (is_array($filters)) {
//		foreach ($filters as $i=>$v) {
//			switch (strtolower($i)) {
//				case 'pid':
//					$where[] = 'p.pid='.$db->qstr($v);
//				break;
//				case 'offset':
//					$offset=$v;
//				break;
//				case 'rowcount':
//					$rowcount=$v;
//				break;
//				case 'sortsql':
//					$sortSQL = $v;
//				break;
//				case 'entrytype':
//					$where[] = 'p.entry_type='.$db->qstr($v);
//				break;
//			}
//		}}

//		$this->sql=
//			"SELECT SQL_CALC_FOUND_ROWS p.entry_id,p.entry_date,p.control_nr,p.pid,\n".
//				"p.associated_id,p.entry_type,p.amount,p.remarks,c.account_name,\n".
//				"p.create_id,b.running_balance\n".
//				"FROM seg_cmap_ledger_patient p\n".
//					"LEFT JOIN seg_cmap_accounts c ON c.account_nr=p.associated_id\n".
//					"LEFT JOIN seg_cmap_patient_balance b ON b.pid=p.pid AND b.account_nr=p.associated_id\n";
//		if ($where)
//			$this->sql.="WHERE (".implode(")\n AND (",$where).")\n";
//		$this->sql .= "ORDER BY $sortSQL\n".
//			"LIMIT $offset, $rowcount";

//		if($this->result=$db->Execute($this->sql)) {
//			return $this->result;
//		} else {
// --- ugly errorReporter
//			$errorReporter->raiseError(SEG_FATAL_ERROR_SQL,
//				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
//			);
//			return false;
//		}
//	}

	/**
	* Retrieve all granted CMAP requests. Granted requests are saved in the seg_cmap_entries_*
	* tables.
	*
	* @param mixed $filters array of query filters for narrowing search
	* @return mixed returns NULL if an SQL error occurs, 0 if no records are found, and the
	* 	ADODB ResultSet object if successful
	*/
//	function getGrants($filters) {
//		global $db;

//		$offset=0;
//		$rowcount=10;
//		$sort_sql = '';

//		$where = array();
//		foreach ($filters as $key=>$value ) {
//			switch($key) {
//				case 'OFFSET':
//					$offset=$value;
//				break;
//				case 'ROWCOUNT':
//					$rowcount=$value;
//				break;
//				case 'SORTSQL':
//					$sort_sql=$value;
//				break;
//			}
//		}

//		$this->sql = "";
//		if (!$filters["EXCLUDE_PH"]) {
//			$sql[$count] = "SELECT 'PH' AS `source`,\n".
//				"d.entry_id,d.ref_no AS `ref_no`,d.create_time AS `date`,d.create_id AS `encoder`,\n".
//				"d.service_code,d.service_name AS `service_name`,d.quantity AS `quantity`,d.amount AS `amount`,\n".
//				"a.account_name\n".
//				"FROM seg_cmap_entries_ph AS d\n".
//				"INNER JOIN seg_cmap_accounts AS a ON d.account_nr=a.account_nr\n".
//				"INNER JOIN seg_pharma_orders AS r ON d.ref_no=r.refno\n";
//			$where = array();
//			$where[] = "NOT d.is_deleted";
//			foreach ($filters as $key=>$value ) {
//				switch($key) {
//					case 'PID':
//						$where[] = "r.pid=".$db->qstr($value);
//					break;
//					case 'DATE':
//						$where[] = "DATE(d.create_time)=".$db->qstr($value);
//					break;
//				}
//			}
//			$sql[$count].="WHERE (".implode(")\nAND (", $where).")\n";
//			$count++;
//		}

//		if (!$filters["EXCLUDE_LD"]) {
//			$sql[$count] = "SELECT 'LD' AS `source`,\n".
//				"d.entry_id,d.ref_no AS `ref_no`,d.create_time AS `date`,d.create_id AS `encoder`,\n".
//				"d.service_code,d.service_name AS `service_name`,d.quantity AS `quantity`,d.amount AS `amount`,\n".
//				"a.account_name\n".
//				"FROM seg_cmap_entries_ld AS d\n".
//				"INNER JOIN seg_cmap_accounts AS a ON d.account_nr=a.account_nr\n".
//				"INNER JOIN seg_lab_serv AS r ON d.ref_no=r.refno\n";
//			$where = array();
//			$where[] = "NOT d.is_deleted";
//			foreach ($filters as $key=>$value ) {
//				switch($key) {
//					case 'PID':
//						$where[] = "r.pid=".$db->qstr($value);
//					break;
//					case 'DATE':
//						$where[] = "DATE(d.create_time)=".$db->qstr($value);
//					break;
//				}
//			}
//			$sql[$count].="WHERE (".implode(")\nAND (", $where).")\n";
//			$count++;
//		}

//		if (!$filters["EXCLUDE_RD"]) {
//			$sql[$count] = "SELECT 'RD' AS `source`,\n".
//				"d.entry_id,d.ref_no AS `ref_no`,d.create_time AS `date`,d.create_id AS `encoder`,\n".
//				"d.service_code,d.service_name AS `service_name`,d.quantity AS `quantity`,d.amount AS `amount`,\n".
//				"a.account_name\n".
//				"FROM seg_cmap_entries_rd AS d\n".
//				"INNER JOIN seg_cmap_accounts AS a ON d.account_nr=a.account_nr\n".
//				"INNER JOIN seg_radio_serv AS r ON d.ref_no=r.refno\n";
//			$where = array();
//			$where[] = "NOT d.is_deleted";
//			foreach ($filters as $key=>$value ) {
//				switch($key) {
//					case 'PID':
//						$where[] = "r.pid=".$db->qstr($value);
//					break;
//					case 'DATE':
//						$where[] = "DATE(d.create_time)=".$db->qstr($value);
//					break;
//				}
//			}
//			$sql[$count].="WHERE (".implode(")\nAND (", $where).")\n";
//			$count++;
//		}


//		if (!$filters["EXCLUDE_FB"]) {
//			$sql[$count] = "SELECT 'FB' AS `source`,\n".
//				"d.entry_id,d.ref_no AS `ref_no`,d.create_time AS `date`,d.create_id AS `encoder`,\n".
//				"d.service_code,d.service_name AS `service_name`,d.quantity AS `quantity`,d.amount AS `amount`,\n".
//				"a.account_name\n".
//				"FROM seg_cmap_entries_fb AS d\n".
//				"INNER JOIN seg_cmap_accounts AS a ON d.account_nr=a.account_nr\n".
//				"INNER JOIN seg_billing_encounter AS b ON d.ref_no=b.bill_nr\n".
//				"INNER JOIN care_encounter AS e ON e.encounter_nr=b.encounter_nr\n";
//			$where = array();
//			$where[] = "NOT d.is_deleted";
//			foreach ($filters as $key=>$value ) {
//				switch($key) {
//					case 'PID':
//						$where[] = "e.pid=".$db->qstr($value);
//					break;
//					case 'DATE':
//						$where[] = "DATE(d.create_time)=".$db->qstr($value);
//					break;
//				}
//			}
//			$sql[$count].="WHERE (".implode(")\nAND (", $where).")\n";
//			$count++;
//		}


//		$this->sql = "SELECT SQL_CALC_FOUND_ROWS entry_id AS `entry_id`,source AS `source`,\n".
//			"date AS `date`,ref_no AS `refno`,service_code AS `code`,\n".
//			"service_name AS `name`,quantity AS `quantity`,amount AS `amount`,\n".
//			"account_name AS `account`,encoder AS `encoder`\n".
//			"FROM (\n";

//		$this->sql.="(".implode(")\nUNION ALL\n(", $sql).")) AS t\n";

//		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
//		$this->sql.="LIMIT $offset, $rowcount";

//		if ($this->result = $db->Execute($this->sql))
//			return $this->result;
//		else
//			return false;
//	}


	/**
	* put your comment there...
	*
	* @param mixed $pid
	* @param mixed $acct
	* @return ADODB
	*/
//	function getBalance($pid, $acct=null) {
//		global $db, $errorReporter;
//		if (!$acct)
//			$this->sql = "SELECT SUM(running_balance) FROM seg_cmap_patient_balance\n".
//				"WHERE pid=".$db->qstr($pid)."\n".
//				"GROUP BY pid";
//		else
//			$this->sql = "SELECT running_balance FROM seg_cmap_patient_balance\n".
//				"WHERE pid=".$db->qstr($pid)." AND account_nr=".$db->qstr($acct);
//		if ($this->result=$db->GetOne($this->sql)) {
//			if (!is_null($this->result)) {
//				return $this->result;
//			}
//			else {
//				$errorReporter->raiseError( SEG_NOTICE_SQL_NO_RECORD,
//					array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
//				);
//				return 0;
//			}
//		}
//		else {
//			$errorReporter->raiseError( SEG_FATAL_ERROR_SQL,
//				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
//			);
//			return FALSE;
//		}
//	}

//	function updateBalance($acct, $pid, $type, $amount) {
//		global $db, $errorReporter;

//		$multiplier = array(
//			'adjustment'=>1.0,
//			'deposit'=>1.0,
//			'grant'=>-1.0,
//			'transfer'=>1.0
//		);

//		$amount = $amount * $multiplier[$type];

//		$bal=$this->getBalance($pid, $acct);
//		if (!$bal) $bal=0;

//		if ($type=='adjustment')
//			$bal=$amount;
//		else
//			$bal += $amount;
//		$ret = $db->Replace('seg_cmap_patient_balance',
//			array('account_nr'=>$acct, 'pid'=>$pid, 'running_balance'=>$bal),
//			array('pid','account_nr'),
//			$autoquote = true);
//		if ($ret===0) {
//			$errorReporter->raiseError( SEG_ERROR_SQL_CANNOT_EDIT,
//				$debug=array( 'error'=>$db->ErrorMsg(),	'result'=>$ret ),
//				$tips=array( 'Cannot update patient\'s running balance' )
//			);
//			return FALSE;
//		}
//		else
//			return TRUE;
//	}

	/**
	* put your comment there...
	*
	* @param mixed $args
	* @return ADODB
	*/
//	function getActiveReferrals( $args ) {
//		global $db, $errorReporter;

//		if (!is_array($args))
//			$args = array('pid'=>$args);

//		if (!isset($args['pid'])) {
//			$errorReporter->raiseError( SEG_NOTICE_DATA_EMPTY,
//				array( 'error'=>'args[pid] not set', '$args'=>var_export($args,true) )
//			);
//			return FALSE;
//		}
//		$minimum= $args['minimum'] ? $args['minimum'] : 0;

//		$this->sql = "SELECT a.account_nr,a.account_name,b.running_balance,\n".
//			"(SELECT control_nr FROM seg_cmap_ledger_patient\n".
//				"WHERE associated_id=b.account_nr ORDER BY entry_date DESC LIMIT 1) AS control_nr\n".
//			"FROM seg_cmap_patient_balance AS b\n".
//			"INNER JOIN seg_cmap_accounts AS a ON a.account_nr=b.account_nr\n";

//		$where = array(
//			"NOT a.is_deleted",
//			"NOT a.is_locked",
//			"pid=".$db->qstr($args['pid']),
//			"b.running_balance>$minimum"
//		);

//		$this->sql.="WHERE\n".
//			"(".implode(")\n AND (",$where).")\n";

//		$this->sql.="ORDER BY a.account_name";

//		if($this->result=$db->Execute($this->sql)) {
//			return $this->result;
//		} else {
//			$errorReporter->raiseError( SEG_FATAL_ERROR_SQL,
//				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
//			);
//			return false;
//		}
//	}



	function cancelEntry($src, $id) {
		global $db, $errorReporter, $HTTP_SESSION_VARS;

		$src=strtolower($src);
		$this->sql =
			"SELECT entry_id,account_nr,ref_no,pid,is_deleted,service_code,amount\n".
				"FROM ".substr($db->qstr('seg_cmap_entries_'.$src),1,-1)."\n".
				"WHERE entry_id=".$db->qstr($id);
		$info=$db->GetRow($this->sql);
		if ($info !== FALSE) {
			if (!$info) {
				$errorReporter->raise( SEG_ERROR_SQL_NO_RECORD,
					$debug=array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() ),
					$tips=array( 'Details for this grant cannot be retrieved from the database.' )
				);
				return false;
			}
			$saveok=$this->updateBalance($info['account_nr'], $info['pid'], 'grant', -$info['amount']);
			if (!$saveok) {
				// No need to report error
				return FALSE;
			}

			if ($src == 'fb') {
					$this->sql =
						"UPDATE seg_billing_encounter SET request_flag=NULL\n".
							"WHERE bill_nr=".$db->qstr($info['ref_no'])." AND request_flag='CMAP'";
			}
			else {
				$tables = array(
					'ld'=>'seg_lab_servdetails',
					'rd'=>'care_test_request_radio',
					'ph'=>'seg_pharma_order_items'
				);
				$codes = array(
					'ld'=>'service_code',
					'rd'=>'service_code',
					'ph'=>'bestellnum',
				);
                #added by VAS 07-04-2012
                #add history for update
                if ($tables[strtolower($src)]=='seg_lab_servdetails'){
                    $history = $this->ConcatHistory("Update request_flag [NULL] -class cmap patient ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				    $history_cond = ", history=".$history."\n";
                }
				$this->sql =
					"UPDATE {$tables[strtolower($src)]} SET request_flag=NULL\n".
                        $history_cond.
						"WHERE refno=".$db->qstr($info['ref_no']).
						"AND {$codes[strtolower($src)]}=".$db->qstr($info['service_code']).
						"AND request_flag='CMAP'";
			}

			$this->result = $db->Execute($this->sql);
			if ($this->result) {
				$this->sql=
					"DELETE FROM ".substr($db->qstr('seg_cmap_entries_'.$src),1,-1)."\n".
						"WHERE entry_id=".$db->qstr($id);
				$this->result = $db->Execute($this->sql);
				if ($this->result)
					return TRUE;
				else {
					$errorReporter->raise( SEG_FATAL_ERROR_SQL,
						array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
					);
				}
			}
			else {
				$errorReporter->raise( SEG_FATAL_ERROR_SQL,
					array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
				);
			}
		}
		else {
			$errorReporter->raise( SEG_FATAL_ERROR_SQL,
				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
			);
			return FALSE;
		}
	}


	/**
	* put your comment there...
	*
	* @param mixed $data
	*/
	function saveEntry($data) {
		global $db, $errorReporter, $HTTP_SESSION_VARS;

		$referrals = $this->getActiveReferrals($data['pid']);
		if ($referrals) {
			$due = (float) $data['amount'];
			if ($due==0) {
				$errorReporter->raiseError( SEG_ERROR_DATA_INVALID,
					$debug='Grant amount should be greater than Php 0.00',
					array(
						'Grant amount should be greater than Php 0.00',
						'Enter a valid grant amount and try saving the entry again'
					)
				);
				return FALSE;
			}
			while ($due>0 && $row=$referrals->FetchRow()) {
				$referral_amount = (float) $row['running_balance'];
				if ($referral_amount > $due) {
					$grant_amount=$due;
					$due=0;
				}
				else {
					$due-=$referral_amount;
					$grant_amount=$referral_amount;
				}

				$saveok=$this->updateBalance($row['account_nr'], $data['pid'], 'grant', $grant_amount);
				if ($saveok) {
					# save the data
					$data_save = $data;

					# generate row guid
					$data_save['entry_id'] = create_guid();

					$data_save['account_nr'] = $row['account_nr'];
					$data_save['referral_nr'] = $row['control_nr'];
					$data_save['amount'] = $grant_amount;

					$table_name = 'seg_cmap_entries_'.strtolower($data['src']);
					$fields = $db->MetaColumnNames($table_name);

					if (!function_exists('array_intersect_key')) {
						function array_intersect_key ($isec, $arr2) {
							$argc = func_num_args();
							for ($i = 1; !empty($isec) && $i < $argc; $i++) {
								$arr = func_get_arg($i);
								foreach ($isec as $k => $v)
									if (!isset($arr[$k]))
										unset($isec[$k]);
							}
							return $isec;
						}
					}

					$internal = array_intersect_key($data_save, array_flip($fields));

					foreach( $internal as $i=>$v ) {
						$internal[$i] = $db->qstr($v);
					}
					$result = $db->Replace( $table_name, $internal, array('account_nr','ref_no','service_code'), $autoqoute = FALSE );
					if (!$result) {
						$errorReporter->raiseError( SEG_ERROR_SQL_CANNOT_EDIT,
							array( 'error'=>$db->ErrorMsg(), 'result'=>$result )
						);
						return FALSE;
					}
				}
				else {
					// no need for error reporting...
					return FALSE;
				}
			}

			if ($due <= 0) {
				if ($data['src'] == 'FB') {
					$this->sql =
						"UPDATE seg_billing_encounter SET request_flag='CMAP'\n".
							"WHERE bill_nr=".$db->qstr($data['ref_no']);
				}
				else {
					$tables = array(
						'LD'=>'seg_lab_servdetails',
						'RD'=>'care_test_request_radio',
						'PH'=>'seg_pharma_order_items'
					);
					$codes = array(
						'LD'=>'service_code',
						'RD'=>'service_code',
						'PH'=>'bestellnum',
					);
                    
                    #added by VAS 07-04-2012
                    #add history for update
                    if ($tables[strtoupper($data['src'])]=='seg_lab_servdetails'){
                        $history = $this->ConcatHistory("Update request_flag [CMAP] -cmap patient ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
					    $history_cond = ", history=".$history."\n";
                    }
					$this->sql =
                        "UPDATE {$tables[strtoupper($data['src'])]} SET request_flag='CMAP',\n".
                            $history_cond.
							"WHERE refno=".$db->qstr($data['ref_no']).
							"AND {$codes[strtoupper($data['src'])]}=".$db->qstr($data['service_code']);
				}
				if ($this->result=$db->Execute($this->sql)) {
					return TRUE;
				}
				else {
					$errorReporter->raiseError( SEG_ERROR_SQL_CANNOT_EDIT,
						array( 'error'=>$db->ErrorMsg(), 'result'=>$result )
					);
					return FALSE;
				}

			}
		}
		else {
			// no need for error reporting
			return FALSE;
		}

		return TRUE;
	}


	/**
	*
	*/
//	function getRequestItems($filters, $offset=0, $rowcount=10, $sort_sql="") {
//		global $db, $errorReporter;

//		$count=0;
//		$sql=array();

//		/* if (!$filters['DATE']) $filters['DATE']=date("Y-m-d"); */

//		$this->sql = "";
//		if (!$filters["FILTER_SOURCE"] || strtoupper($filters['FILTER_SOURCE'])=='PH') {
//			$sql[$count] = "SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'PH' AS `source`,\n".
//				"o.orderdate AS `date`,o.refno AS `refno`,\n".
//				"oi.bestellnum AS `itemno`,p.artikelname AS `name`,oi.quantity AS `qty`,\n".
//				"oi.price_orig*oi.quantity AS `total`,oi.pricecash*oi.quantity AS `discounted`,\n".
//				"oi.request_flag,\n".
//				"oi.price_orig*oi.quantity-IFNULL((SELECT SUM(ce.amount) FROM seg_cmap_entries_ph AS ce\n".
//					"WHERE ce.ref_no=oi.refno AND ce.service_code=oi.bestellnum),0) AS `due`\n".
//				"FROM seg_pharma_order_items AS oi\n".
//				"INNER JOIN seg_pharma_orders AS o ON o.refno=oi.refno\n".
//				"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=oi.bestellnum\n";

//			$where = array();

//			$where[] = "o.is_cash=1";
//			foreach ($filters as $key=>$value ) {
//				switch(strtoupper($key)) {
//					case 'PID':
//						$where[] = "o.pid=".$db->qstr($value);
//					break;
//					case 'FILTER_DATE':
//						$where[] = "DATE(o.orderdate)=".$db->qstr($value);
//					break;
//					case 'FILTER_NAME':
//						$where[] = "p.artikelname LIKE ".$db->qstr("$value%");
//					break;
//					case 'FILTER_GRANT':
//						if (strtoupper($value)=='NULL')
//							$where[] = "oi.request_flag IS NULL";
//						else
//							$where[] = "oi.request_flag=".$db->qstr($value);
//					break;
//				}
//			}

//			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
//			$count++;
//		}

//		if (!$filters["FILTER_SOURCE"] || strtoupper($filters['FILTER_SOURCE'])=='LD') {
//			$sql[$count] = "SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'LD' AS `source`,\n".
//				"CONCAT(l.serv_dt,' ',l.serv_tm) AS `date`,l.refno AS `refno`,\n".
//				"ld.service_code AS `itemno`,s.name AS `name`,ld.quantity AS `qty`,\n".
//				"ld.price_cash AS `discounted`,ld.price_cash*ld.quantity As `total`,ld.request_flag,\n".
//				"ld.price_cash*ld.quantity-IFNULL((SELECT SUM(ce.amount) FROM seg_cmap_entries_ld AS ce\n".
//					"WHERE ce.ref_no=ld.refno AND ce.service_code=ld.service_code),0) AS `due`\n".
//				"FROM seg_lab_servdetails AS ld\n".
//				"INNER JOIN seg_lab_serv AS l ON l.refno=ld.refno\n".
//				"LEFT JOIN seg_lab_services AS s ON s.service_code=ld.service_code\n";

//			$where = array();

//			$where[] = "l.is_cash=1";
//			$where[] = "l.status!='deleted'";
//			foreach ($filters as $key=>$value ) {
//				switch($key) {
//					case 'PID':
//						$where[] = "l.pid=".$db->qstr($value);
//					break;
//					case 'FILTER_DATE':
//						$where[] = "l.serv_dt=".$db->qstr($value);
//					break;
//					case 'FILTER_NAME':
//						$where[] = "s.name LIKE ".$db->qstr("$value%");
//					break;
//					case 'FILTER_GRANT':
//						if (strtoupper($value)=='NULL')
//							$where[] = "ld.request_flag IS NULL";
//						else
//							$where[] = "ld.request_flag=".$db->qstr($value);
//					break;
//				}
//			}
//			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
//			$count++;
//		}

//		if (!$filters["FILTER_SOURCE"] || strtoupper($filters['FILTER_SOURCE'])=='RD') {
//			$sql[$count] = "SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'RD' AS `source`,\n".
//				"CONCAT(r.request_date,' ',r.request_time) AS `date`,r.refno AS `refno`,\n".
//				"rd.service_code AS `itemno`,s.name AS `name`,1 AS `qty`,rd.price_cash AS `discounted`,\n".
//				"rd.price_cash_orig As `total`,rd.request_flag,\n".
//				"rd.price_cash-IFNULL((SELECT SUM(ce.amount) FROM seg_cmap_entries_rd AS ce\n".
//					"WHERE ce.ref_no=rd.refno AND ce.service_code=rd.service_code),0) AS `due`\n".
//				"FROM care_test_request_radio AS rd\n".
//				"INNER JOIN seg_radio_serv AS r ON r.refno=rd.refno\n".
//				"LEFT JOIN seg_radio_services AS s ON s.service_code=rd.service_code\n";

//			$where = array();

//			$where[] = "r.is_cash=1";
//			$where[] = "r.status!='deleted'";
//			foreach ($filters as $key=>$value ) {
//				switch($key) {
//					case 'PID':
//						$where[] = "r.pid=".$db->qstr($value);
//					break;
//					case 'FILTER_DATE':
//						$where[] = "r.request_date=".$db->qstr($value);
//					break;
//					case 'FILTER_NAME':
//						$where[] = "s.name LIKE ".$db->qstr("$value%");
//					break;
//					case 'FILTER_GRANT':
//						if (strtoupper($value)=='NULL')
//							$where[] = "rd.request_flag IS NULL";
//						else
//							$where[] = "rd.request_flag=".$db->qstr($value);
//					break;
//				}
//			}
//			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
//			$count++;
//		}

//		if (!$filters["FILTER_SOURCE"] || strtoupper($filters['FILTER_SOURCE'])=='FB') {
//			$sql[$count] =
//				"SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'FB' AS `source`,\n".
//						"b.bill_dte AS `date`,b.bill_nr AS `refno`,\n".
//						"b.bill_nr AS `itemno`,CONCAT(\"Hospoital bill (\",fn_get_person_lastname_first(e.pid),\")\") AS `name)`,\n".
//						"1 AS `qty`,b.request_flag,".
//						"fn_compute_bill(b.bill_nr) AS `discounted`,\n".
//						"fn_compute_bill(b.bill_nr) AS `total`,\n".
//						"0 AS `due`\n".
//				"FROM seg_billing_encounter AS b\n".
//				"INNER JOIN care_encounter AS e ON e.encounter_nr=b.encounter_nr\n";

//		$where = array();
//			foreach ($filters as $key=>$value ) {
//				switch($key) {
//					case 'PID':
//						$where[] = "e.pid=".$db->qstr($value);
//					break;
//					case 'FILTER_DATE':
//						$where[] = "DATE(b.bill_dte)=".$db->qstr($value);
//					break;
//					case 'FILTER_GRANT':
//						if (strtoupper($value)=='NULL')
//							$where[] = "b.request_flag IS NULL";
//						else
//							$where[] = "b.request_flag=".$db->qstr($value);
//					break;
//				}
//			}
//			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
//			$count++;
//		}

//		$this->sql = "(".implode(")\n UNION ALL (", $sql).")";
//		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
//		$this->sql.="LIMIT $offset, $rowcount";

//		if ($this->result = $db->Execute($this->sql)) {
//			if (!$this->result->RecordCount()) {
//				$errorReporter->raiseError( SEG_NOTICE_SQL_NO_RECORD,
//					array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
//				);
//			}
//			return $this->result;
//		}
//		else {
//			$errorReporter->raiseError(SEG_FATAL_ERROR_SQL,
//				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
//			);
//			return false;
//		}
//	}


}