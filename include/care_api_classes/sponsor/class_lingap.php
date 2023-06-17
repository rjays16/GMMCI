<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

/**
*
*/
class SegLingap extends Core {

	private $lingapTable = "seg_lingap_entries";
	private $loggerName = 'sponsor.lingap';
	private $id;
	private $data;

	/**
	* Constructor
	*
	*/
	public function __construct($id=null) {
		global $db;
		$this->setupLogger($this->loggerName);
		$this->setTable($this->lingapTable, $fetchMetadata=true);
		if ($id) {
			$this->fetch($id);
		}
	}



	/**
	* Override Core fetch function
	*
	* @param mixed $id
	* @return ADODB
	*/
	public function fetch($id) {
		$data = parent::fetch(array('id'=>$id));
		if ($data !== false) {
			$this->id=$id;
			$this->data = $data;
		}
		else {
			$this->logger->warn('Could not retrieve Lingap entry data: '.$this->getErrorMsg()."\nQuery:".$this->getQuery());
			unset($this->id);
			unset($this->data);
		}
		return $data;
	}

	public function save($data, $force_insert=false) {
		if (parent::save($data, $forced_insert) !== false) {
			$this->data = $data;
		}
		return false;
	}










//	function getNewControl($today=NULL) {
//		global $db;
//		$today = $db->qstr($today);
//		$this->sql="SELECT IFNULL(MAX(CAST(control_nr AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(control_nr,1,4)=EXTRACT(YEAR FROM NOW())";
//		return $db->GetOne($this->sql);
//	}

//	function deleteEntry($id) {
//		global $db;
//		if ($this->clearEntry($id)) {
//			return $this->delete(array('id'=>$id));
//		}
//		else
//			return FALSE;
//	}

//	/* Clears the details of the Linap entry */
//	function clearEntry($id) {
//		global $db;

//		# Simple deletion will not suffice, need to unflag all corresponding requests
//		$this->sql =
//			"SELECT ref_source,ref_no,service_code\n".
//				"FROM {$this->lingap_items_tb}\n".
//				"WHERE entry_id=".$db->qstr($id);
//		if ($this->result=$db->Execute($this->sql)) {
//			$result=$this->result;
//			while ($row=$result->FetchRow()) {
//				$this->sql =
//					"DELETE FROM $this->lingap_items_tb\n".
//						"WHERE entry_id=".$db->qstr($id)."\n".
//							"AND ref_source=".$db->qstr($row['ref_source'])."\n".
//							"AND ref_no=".$db->qstr($row['ref_no'])."\n".
//							"AND service_code=".$db->qstr($row['service_code'])."\n";
//				if ($this->result=$db->Execute($this->sql)) {
//					$replace=$this->flag($row['ref_source'], $row['ref_no'], $row['service_code'], $flag=FALSE);
//					if (!$replace)
//						return FALSE;
//				}
//				else
//					return FALSE;
//			}
//			return TRUE;
//		}
//		else
//			return FALSE;
//	}

//	function flag ($src, $ref, $item, $flag) {
//		global $db;
//		switch (strtoupper($src)) {
//			case 'PH':
//				$this->result=$db->Replace('seg_pharma_order_items',
//					array(
//						'refno'=>$db->qstr($ref),
//						'bestellnum'=>$db->qstr($item),
//						'request_flag'=>$flag?"'LINGAP'":'NULL'
//					),
//					array('refno','bestellnum'),
//					$autoquote=FALSE
//				);
//			break;
//			case 'LD':
//				$this->result=$db->Replace('seg_lab_servdetails',
//					array(
//						'refno'=>$db->qstr($ref),
//						'service_code'=>$db->qstr($item),
//						'request_flag'=>$flag?"'LINGAP'":'NULL'
//					),
//					array('refno','service_code'),
//					$autoquote=FALSE
//				);
//			break;
//			case 'RD':
//				$this->result=$db->Replace('care_test_request_radio',
//					array(
//						'refno'=>$db->qstr($ref),
//						'service_code'=>$db->qstr($item),
//						'request_flag'=>$flag?"'LINGAP'":'NULL'
//					),
//					array('refno','service_code'),
//					$autoquote=FALSE
//				);
//			break;
//		}
//		return $this->result;
//	}

//	function addDetails($id, $arr) {
//		global $db;
		//$this->sql = "INSERT INTO $this->lingap_items_tb(entry_id,ref_source,ref_no,service_code,service_name,quantity,amount) VALUES(".$db->qstr($id).",?,?,?,?,?,?)";
//		$this->_statement = "INSERT INTO $this->lingap_items_tb(entry_id,ref_source,ref_no,service_code,service_name,quantity,amount) VALUES(".$db->qstr($id).",?,?,?,?,?,?)";

//		foreach ($arr as $i=>$v) {

//			$src = $v[0];
//			$ref = $v[1];
//			$item = $v[2];
//			$name = $v[3];
//			$qty = $v[4];
//			$amt = $v[5];

//			$this->result=$db->Execute( $this->_statement, array($src, $ref, $item, $name, $qty, $amt) );
//			if ($this->result) {
//				$this->flag($src, $ref, $item, $flag=TRUE);
//				if ($this->result===0) return FALSE;
//			}
//			else return FALSE;
//		}

//		return TRUE;
//	}

	function getAllEntries($filters) {
		global $db;

		$offset = 0;
		$rowcount = 15;
		$sortSQL = "e.entry_date DESC";

		$where = array();

		$where[] = " NOT is_deleted";
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
			"(SELECT GROUP_CONCAT(d.service_name SEPARATOR ', ') FROM seg_lingap_entry_details AS d WHERE d.entry_id=e.entry_id) AS `items`,\n".
			"(SELECT SUM(d.amount) FROM seg_lingap_entry_details AS d WHERE d.entry_id=e.entry_id) AS `total`\n".
			"FROM seg_lingap_entries AS e\n".
			"$whereSQL\n";
		$this->sql .= "ORDER BY $sortSQL\n" .
			"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }

	}

	function getGrants($filters) {
		global $db;

		$offset=0;
		$rowcount=10;
		$sort_sql = '';

		$where = array();
		foreach ($filters as $key=>$value ) {
			switch($key) {
				case 'PID':
					$where[] = "l.pid=".$db->qstr($value);
				break;
				case 'OFFSET':
					$offset=$value;
				break;
				case 'ROWCOUNT':
					$rowcount=$value;
				break;
				case 'SORTSQL':
					$sort_sql=$value;
				break;
			}
		}

		$this->sql =
			"SELECT l.entry_date AS `date`,ref_source AS `source`,d.ref_no AS `refno`,\n".
				"d.service_code AS `code`,\n".
				"d.service_name AS `name`,d.quantity AS `quantity`,d.amount AS `amount`\n".
				"FROM seg_lingap_entry_details AS d\n".
				"INNER JOIN seg_lingap_entries AS l ON l.entry_id=d.entry_id\n";

		if ($where)	{
			$this->sql.="WHERE (".implode(")\nAND (", $where).")";
		}

		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount";

		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}

	function getSSRequests($filters) {
		global $db;

		$offset=0;
		$rowcount=10;
		$sort_sql = '';

		$where = array();
		foreach ($filters as $key=>$value ) {
			switch($key) {
				case 'PID':
					$where[] = "sl.pid=".$db->qstr($value);
				break;
				case 'OFFSET':
					$offset=$value;
				break;
				case 'ROWCOUNT':
					$rowcount=$value;
				break;
				case 'SORTSQL':
					$sort_sql=$value;
				break;
			}
		}

		$this->sql =
			"SELECT u.name,sl.date_generated,sl.control_nr,\n".
					"l.id entry_id,l.is_advance\n".
				"FROM seg_social_lingap AS sl\n".
					"INNER JOIN care_users AS u ON u.login_id=sl.create_id\n".
					"LEFT JOIN seg_lingap_entries AS l ON l.ss_nr=sl.control_nr\n";
		if ($where)	{
			$this->sql.="WHERE (".implode(")\nAND (", $where).")";
		}

		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount";

		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}

	function getRequestItems($filters, $offset=0, $rowcount=10, $sort_sql="") {
		global $db;

		$count=0;
		$sql=array();

		if (!$filters['DATE']) $filters['DATE']=date("Y-m-d");

		$this->sql = "";
		if (!$filters["EXCLUDE_PH"]) {
			$sql[$count] = "SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'PH' AS `source`,\n".
				"o.orderdate AS `date`,o.refno AS `refno`,\n".
				"oi.bestellnum AS `itemno`,p.artikelname AS `name`,oi.quantity AS `qty`,\n".
				"oi.price_orig*oi.quantity AS `total`,oi.pricecash*oi.quantity AS `discounted`,\n".
				"oi.request_flag\n".
				"FROM seg_pharma_order_items AS oi\n".
				"INNER JOIN seg_pharma_orders AS o ON o.refno=oi.refno\n".
				"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=oi.bestellnum\n";

			$where = array();

			$where[] = "o.is_cash=1";
			foreach ($filters as $key=>$value ) {
				switch($key) {
					case 'PID':
						$where[] = "o.pid=".$db->qstr($value);
					break;
					case 'DATE':
						$where[] = "DATE(o.orderdate)=".$db->qstr($value);
					break;
				}
			}

			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
			$count++;
		}

		if (!$filters["EXCLUDE_LD"]) {
			$sql[$count] = "SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'LD' AS `source`,\n".
				"CONCAT(l.serv_dt,' ',l.serv_tm) AS `date`,l.refno AS `refno`,\n".
				"ld.service_code AS `itemno`,s.name AS `name`,1 AS `qty`,\n".
				"ld.price_cash AS `discounted`,ld.price_cash_orig As `total`,ld.request_flag\n".
				"FROM seg_lab_servdetails AS ld\n".
				"INNER JOIN seg_lab_serv AS l ON l.refno=ld.refno\n".
				"LEFT JOIN seg_lab_services AS s ON s.service_code=ld.service_code\n";

			$where = array();

			$where[] = "l.is_cash=1";
			$where[] = "l.status!='deleted'";
			foreach ($filters as $key=>$value ) {
				switch($key) {
					case 'PID':
						$where[] = "l.pid=".$db->qstr($value);
					break;
					case 'DATE':
						$where[] = "l.serv_dt=".$db->qstr($value);
					break;
				}
			}
			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
			$count++;
		}

		if (!$filters["EXCLUDE_RD"]) {
			$sql[$count] = "SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'RD' AS `source`,\n".
				"CONCAT(r.request_date,' ',r.request_time) AS `date`,r.refno AS `refno`,\n".
				"rd.service_code AS `itemno`,s.name AS `name`,1 AS `qty`,rd.price_cash AS `discounted`,\n".
				"rd.price_cash_orig As `total`,rd.request_flag\n".
				"FROM care_test_request_radio AS rd\n".
				"INNER JOIN seg_radio_serv AS r ON r.refno=rd.refno\n".
				"LEFT JOIN seg_radio_services AS s ON s.service_code=rd.service_code\n";

			$where = array();

			$where[] = "r.is_cash=1";
			$where[] = "r.status!='deleted'";
			foreach ($filters as $key=>$value ) {
				switch($key) {
					case 'PID':
						$where[] = "r.pid=".$db->qstr($value);
					break;
					case 'DATE':
						$where[] = "r.request_date=".$db->qstr($value);
					break;
				}
			}
			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
			$count++;
		}

		$this->sql = "(".implode(")\n UNION ALL (", $sql).")";
		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount";

		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}

	function getWalkinItems($filters, $offset=0, $rowcount=10, $sort_sql="") {
		global $db;

		$count=0;
		$sql=array();

		if (!$filters['DATE']) $filters['DATE']=date("Y-m-d");
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
			}
		}

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS 'PH' AS `source`,\n".
			"o.orderdate AS `date`,o.refno AS `refno`,\n".
			"oi.bestellnum AS `itemno`,p.artikelname AS `name`,oi.quantity AS `qty`,\n".
			"oi.price_orig*oi.quantity AS `total`,oi.pricecash*oi.quantity AS `discounted`,\n".
			"(SELECT ) AS lingap\n".
			"oi.request_flag\n".
			"FROM seg_pharma_order_items AS oi\n".
			"INNER JOIN seg_pharma_orders AS o ON o.refno=oi.refno\n".
			"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=oi.bestellnum\n";
		$this->sql.="WHERE (".implode(")\nAND (", $where).")";
		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount";

		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}

}
