<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

class SegLingapBilling extends Core {

	var $target;
	var $lingap_tb = "seg_lingap_entries";
	var $lingap_items_tb="seg_lingap_entry_details";

	var $fld_lingap;
	var $fld_lingap_items;

	function SegLingapBilling() {
		global $db;
		$this->coretable = $this->lingap_tb;
		$this->fld_lingap = $db->MetaColumnNames($this->lingap_tb);
		$this->setRefArray($this->fld_lingap);
		$this->fld_lingap_items = $db->MetaColumnNames($this->lingap_items_tb);
	}

	function getNewControl($today=NULL) {
		global $db;
		$today = $db->qstr($today);
		$this->sql="SELECT IFNULL(MAX(CAST(control_nr AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(control_nr,1,4)=EXTRACT(YEAR FROM NOW())";
		return $db->GetOne($this->sql);
	}

	function deleteEntry($id) {
		global $db;
		if ($this->clearEntry($id)) {
			$this->sql = "DELETE FROM $this->coretable WHERE entry_id=".$db->qstr($id);
			return $this->Transact();
		}
		else
			return FALSE;
	}

	/* Clears the details of the Linap entry */
	function clearEntry($id) {
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
                $history = $this->ConcatHistory("Update request_flag [NULL] -lingap billing ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
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
					"l.entry_id,l.is_advance\n".
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

	function getBillingStatements($filters, $offset=0, $rowcount=10, $sort_sql="") {
		global $db;

		$count=0;
		$sql=array();

		if (!$filters['DATE']) $filters['DATE']=date("Y-m-d");

		$this->sql = "";
		$this->sql = "SELECT SQL_CALC_FOUND_ROWS 'FB' AS `source`,\n".
			"b.bill_dre `date`, b.bill_nr AS `refno`,\n".
			"fn_get_compute_bill(b.bill_nr) `amount_due`,\n".
			"fn_compute_bill_grants `grants`".
			"oi.request_flag\n".
			"FROM seg_billing_encounter b\n".
			"INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr\n";
			"LEFT JOIN "

		$where = array();

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

		$this->sql.="WHERE (".implode(")\nAND (", $where).")";

		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount";

		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}
}
