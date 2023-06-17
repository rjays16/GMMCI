<?php
require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

class SegBillingGrant extends Core {
	var $tb_lingap = 'seg_lingap_entries';
	var $tb_lingap_details = 'seg_lingap_entry_details';
	var $fld_lingap;
	var $fld_lingap_details;

	var $tb_cmap = 'seg_cmap_entries_fb';
	var $fld_cmap;

	function SegBillingGrant() {
	}

	function useLingap() {
		global $db;
		$this->coretable = $this->tb_lingap;
		$this->fld_lingap = $db->MetaColumnNames($this->tb_lingap);
		$this->setRefArray($this->fld_lingap);
	}

	function useCmap() {
		global $db;
		$this->coretable = $this->tb_cmap;
		$this->fld_cmap = $db->MetaColumnNames($this->tb_cmap);
		$this->setRefArray($this->fld_cmap);
	}

	function getLingapBillingGrants($filters) {
		global $db, $errorReporter;

		# Set default limit params
		$offset = 0;
		$row_count = 10;

		$this->sql =
			"SELECT SQL_CALC_FOUND_ROWS\n".
				"e.entry_id,e.control_nr,e.entry_date, d.ref_no, d.amount, e.is_advance\n".
				"FROM seg_lingap_entry_details d\n".
				"INNER JOIN seg_lingap_entries e ON e.entry_id=d.entry_id\n".
				"LEFT JOIN seg_billing_encounter b ON b.bill_nr=d.ref_no\n".
				"";

		$where = array();
		$where[] = "d.ref_source='FB'";

		# process filter args
		foreach ($filters as $key=>$value ) {
			switch(strtoupper($key)) {
				case 'PID':
					$where[] = "e.pid=".$db->qstr($value);
				break;
				case 'DATE':
					$where[] = "DATE(e.entry_date)=".$db->qstr($value);
				break;
				case 'SORTSQL':
					$sort_sql = $value;
				break;
				case 'OFFSET':
					$offset = $value;
				break;
				case 'ROWCOUNT':
					$row_count = $value;
				break;
			}
		}

		$this->sql.="WHERE (".implode(")\nAND (", $where).")\n";

		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $row_count";

		if ($this->result = $db->Execute($this->sql)) {
			if (!$this->result->RecordCount()) {
				# empty recordset
				$errorReporter->raiseError( SEG_NOTICE_SQL_NO_RECORD,
					array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
				);
			}
			return $this->result;
		}
		else {
			# sql error
			$errorReporter->raiseError(SEG_FATAL_ERROR_SQL,
				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
			);
			return false;
		}
	}

	function addGrant($id, $target, $nr, $amount) {
		global $db, $errorReporter;

		switch (strtolower($target)) {
			case 'fb':
			default:
				$tb = 'seg_lingap_entry_details_fb';
			break;
		}

		$this->sql = "SELECT COUNT(1) FROM $tb WHERE entry_id=".$db->qstr($id)." AND ref_no=".$db->qstr($nr);
		if (($this->result=$db->GetOne($this->sql)) !== FALSE) {
		}
		else {
			$errorReporter->raiseError(SEG_ERROR_SQL_INVALID,
				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
			);
			return FALSE;
		}

		if ($this->result === '1') {
			$this->sql = "UPDATE $tb SET amount=".$db->qstr($amount)."\n".
				"WHERE entry_id=".$db->qstr($id)." AND ".$db->qstr($nr)."";
			$potential_error = SEG_ERROR_SQL_CANNOT_EDIT;
		}
		else {
			$this->sql = "INSERT INTO $tb(`entry_id`,`ref_no`,`amount`)\n".
				"VALUES(".
					$db->qstr($id).",".
					$db->qstr($nr).",".
					$db->qstr($amount).
				 ")";
			$potential_error  = SEG_ERROR_SQL_CANNOT_CREATE;
		}
		if ($this->result=$db->Execute($this->sql))
			return TRUE;
		else {
			$errorReporter->raiseError($potential_error,
				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
			);
			return FALSE;
		}
	}

	function getBillingStatements($filters) {
		global $db, $errorReporter;
		$count=0;
		$rowcount = 10;
		$offset = 0;

		$this->sql =
			"SELECT SQL_CALC_FOUND_ROWS \n".
				"b.bill_dte `date`, b.bill_nr `nr`,\n".
				"w.name `ward`,\n".
				"fn_compute_bill(b.bill_nr) `due`,\n".
				"fn_compute_bill_grants(b.bill_nr) `grant`,\n".
				"b.request_flag `request_flag`\n".
				"FROM seg_billing_encounter b\n".
				"INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr\n".
				"INNER JOIN care_ward w ON w.nr=e.current_ward_nr\n";
		$where = array();

		foreach ($filters as $key=>$value ) {
			switch(strtoupper($key)) {
				case 'PID':
					$where[] = "e.pid=".$db->qstr($value);
				break;
				case 'DATE':
					$where[] = "DATE(b.bill_dte)=".$db->qstr($value);
				break;
				case 'OFFSET':
					$offset = $value;
				break;
				case 'ROWCOUNT':
					$rowcount = $value;
				break;
			}
		}

		$this->sql.="WHERE (".implode(")\nAND (", $where).")";

		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount";

		if ($this->result = $db->Execute($this->sql)) {
			if (!$this->result->RecordCount()) {
				$errorReporter->raiseError( SEG_NOTICE_SQL_NO_RECORD,
					array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
				);
			}
			return $this->result;
		}
		else {
			$errorReporter->raiseError(SEG_FATAL_ERROR_SQL,
				array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() )
			);
			return false;
		}
	}

}