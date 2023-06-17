<?php

require "./roots.php";
require_once $root_path.'include/care_api_classes/class_core.php';
require_once $root_path.'include/care_api_classes/sponsor/grantors/class_lingap_grantor.php';

/**
* Lingap Walkin helper class
*
* @author Alvin Quinones
*/
class SegLingapWalkin extends Core {

	private static $lingapTable = "seg_lingap_walkin_entries";
	private static $loggerName = "sponsor.lingap";

	/**
	* Constructor
	*
	*/
	public function __construct() {
		global $db;
		$this->setTable(self::$lingapTable, $fetch_metadata = true);
		$this->setupLogger(self::$loggerName);
	}


	/**
	* put your comment there...
	*
	* @param mixed $id
	*/
	public function deleteEntry($id) {
		global $db;
		if ($this->clearDetails($id)) {
			$this->setQuery("DELETE FROM ".$this->coretable." WHERE id=".$db->qstr($id));
			$this->setResult(  $db->Execute($this->getQuery()) );
			if ($this->getResult() !== false) {
				return true;
			}
			else {
				$this->logger->error("SQL error:".$db->ErrorMsg()."\nQuery".$this->getQuery());
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	* put your comment there...
	*
	* @param mixed $id
	*/
	public function clearDetails($id) {
		global $db;

		# Simple deletion will not suffice, need to unflag all corresponding requests
		$this->sql =
			"SELECT ref_source,ref_no,service_code\n".
				"FROM $this->lingap_items_tb\n".
				"WHERE entry_id=".$db->qstr($id);
		if ($this->result=$db->Execute($this->sql)) {
			$result=$this->result;
			while ($row=$result->FetchRow()) {
				$this->sql =
					"DELETE FROM $this->lingap_items_tb\n".
						"WHERE entry_id=".$db->qstr($id)."\n".
							"AND ref_source=".$db->qstr($row['ref_source'])."\n".
							"AND ref_no=".$db->qstr($row['ref_no'])."\n".
							"AND service_code=".$db->qstr($row['service_code'])."\n";
				if ($this->result=$db->Execute($this->sql)) {
					$replace=$this->flag($row['ref_source'], $row['ref_no'], $row['service_code'], $flag=FALSE);
					if (!$replace)
						return FALSE;
				}
				else
					return FALSE;
			}
			return TRUE;
		}
		else
			return FALSE;
	}

	function flag ($src, $ref, $item, $flag) {
		global $db, $HTTP_SESSION_VARS;
		switch (strtoupper($src)) {
			case 'PH':
				$this->result=$db->Replace('seg_pharma_order_items',
					array(
						'refno'=>$db->qstr($ref),
						'bestellnum'=>$db->qstr($item),
						'request_flag'=>$flag?"'LINGAP'":'NULL'
					),
					array('refno','bestellnum'),
					$autoquote=FALSE
				);
			break;
			case 'LD':
                #added by VAS 07-04-2012
                #add history for update
                $history = $this->ConcatHistory("Update request_flag [NULL] -lingap walkin ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				$this->result=$db->Replace('seg_lab_servdetails',
					array(
						'refno'=>$db->qstr($ref),
						'service_code'=>$db->qstr($item),
						'request_flag'=>$flag?"'LINGAP'":'NULL',
                        'history'=>$history
					),
					array('refno','service_code'),
					$autoquote=FALSE
				);
			break;
			case 'RD':
				$this->result=$db->Replace('care_test_request_radio',
					array(
						'refno'=>$db->qstr($ref),
						'service_code'=>$db->qstr($item),
						'request_flag'=>$flag?"'LINGAP'":'NULL'
					),
					array('refno','service_code'),
					$autoquote=FALSE
				);
			break;
		}
		return $this->result;
	}

	function addDetails($id, $arr) {
		global $db;
		//$this->sql = "INSERT INTO $this->lingap_items_tb(entry_id,ref_source,ref_no,service_code,service_name,quantity,amount) VALUES(".$db->qstr($id).",?,?,?,?,?,?)";
		$this->_statement = "INSERT INTO $this->lingap_items_tb(entry_id,ref_source,ref_no,service_code,service_name,quantity,amount) VALUES(".$db->qstr($id).",?,?,?,?,?,?)";

		foreach ($arr as $i=>$v) {

			$src = $v[0];
			$ref = $v[1];
			$item = $v[2];
			$name = $v[3];
			$qty = $v[4];
			$amt = $v[5];

			$this->result=$db->Execute( $this->_statement, array($src, $ref, $item, $name, $qty, $amt) );
			if ($this->result) {
				$this->flag($src, $ref, $item, $flag=TRUE);
				if ($this->result===0) return FALSE;
			}
			else return FALSE;
		}

		return TRUE;
	}


	/**
	* put your comment there...
	*
	* @param mixed $filters
	* @return ADODB
	*/
	public function getGrants($filters) {
		global $db;

		$offset = 0;
		$rowcount = 15;
		$sortSQL = "e.entry_date DESC";

		$where = array();

		$where[] = "";
		if (is_array($filters)) {
		foreach ($filters as $i=>$v) {
			switch (strtolower($i)) {
				case 'offset':
					$offset=$v;
				break;
				case 'rowcount':
					$rowcount=$v;
				break;
				case 'sortsql':
					$sortSQL = $v;
				break;
			}
		}}

		$whereSQL=implode(") AND (",$where);
		if ($whereSQL) $whereSQL = "WHERE ($whereSQL)";

		$this->sql="SELECT SQL_CALC_FOUND_ROWS e.*,\n".
			"(SELECT GROUP_CONCAT(d.service_name SEPARATOR ', ') FROM seg_lingap_entry_details AS d WHERE d.entry_id=e.id) AS `items`,\n".
			"(SELECT SUM(d.amount) FROM seg_lingap_entry_details AS d WHERE d.entry_id=e.id) AS `total`\n".
			"FROM seg_lingap_entries AS e\n".
			"$whereSQL\n";
		$this->sql .= "ORDER BY $sortSQL\n" .
			"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }

	}

//	function getGrants($filters) {
//		global $db;

//		$offset=0;
//		$rowcount=10;
//		$sort_sql = '';

//		$where = array();
//		foreach ($filters as $key=>$value ) {
//			switch($key) {
//				case 'PID':
//					$where[] = "l.pid=".$db->qstr($value);
//				break;
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

//		$this->sql = "SELECT l.entry_id,l.entry_date AS `date`,l.control_nr,ref_source AS `source`,d.ref_no AS `refno`,d.service_code AS `code`,\n".
//			"d.service_name AS `name`,d.quantity AS `quantity`,d.amount AS `amount`,l.is_advance\n".
//			"FROM seg_lingap_walkin_entry_details AS d\n".
//			"INNER JOIN seg_lingap_walkin_entries AS l ON l.entry_id=d.entry_id\n";

//		if ($where)	{
//			$this->sql.="WHERE (".implode(")\nAND (", $where).")\n";
//		}

//		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
//		$this->sql.="LIMIT $offset, $rowcount";

//		if ($this->result = $db->Execute($this->sql))
//			return $this->result;
//		else
//			return false;
//	}

	function getWalkinItems($filters, $offset=0, $rowcount=10, $sort_sql="") {
		global $db;

		$count=0;
		$sql=array();

		$where = array();

		$where[] = "o.is_cash=1";
		$where[] = "o.walkin_pid IS NOT NULL";
		foreach ($filters as $key=>$value ) {
			switch($key) {
				case 'PID':
					$where[] = "o.walkin_pid=".$db->qstr($value);
				break;
				case 'DATE':
					$where[] = "DATE(o.orderdate)=".$db->qstr($value);
				break;
				case 'ENTRY':
					$this->sql =
						"SELECT d.ref_no,d.service_code FROM seg_lingap_walkin_entry_details d\n".
							"WHERE d.entry_id=".$db->qstr($filters['ENTRY']);
					#print_r($this->sql);
					$result=$db->Execute($this->sql);
					$or_array = array();
					if ($result) {
						$or_array = array();
						while ($row=$result->FetchRow()) {
							$or_array[] = "oi.refno=".$db->qstr($row['ref_no'])." AND oi.bestellnum=".$db->qstr($row['service_code']);
						}
					}
					if ($or_array) {
						$where[] = "(".implode(") OR (", $or_array).")";
					}
					else {
						$where[] = '0';
					}
				break;
			}
		}

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS 'PH' AS `source`,\n".
			"o.orderdate AS `date`,o.refno AS `refno`,\n".
			"oi.bestellnum AS `itemno`,p.artikelname AS `name`,oi.quantity AS `qty`,\n".
			"oi.price_orig*oi.quantity AS `total`,oi.pricecash*oi.quantity AS `discounted`,\n".
			"oi.request_flag\n".
			"FROM seg_pharma_order_items AS oi\n".
			"INNER JOIN seg_pharma_orders AS o ON o.refno=oi.refno\n".
			"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=oi.bestellnum\n";
		$this->sql.="WHERE (".implode(")\nAND (", $where).")\n";
		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount\n";

		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}

}
