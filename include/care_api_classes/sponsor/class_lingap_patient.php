<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

class SegLingapPatient extends Core {

	var $target;
	var $lingap_tb = "seg_lingap_ledger_patient";

	function SegLingapPatient() {
		global $db;
		$this->coretable = $this->lingap_tb;
		$this->fld_lingap = $db->MetaColumnNames($this->lingap_tb);
		$this->setRefArray($this->fld_lingap);
	}

	function getLedger($filters) {
		global $db;

		$offset = 0;
		$rowcount = 15;
		$sortSQL = "l.entry_date DESC";

		$where = array();

		if (is_array($filters)) {
		foreach ($filters as $i=>$v) {
			switch (strtolower($i)) {
				case 'pid':
					$where[] = 'pid='.$db->qstr($v);
				break;
				case 'offset':
					$offset=$v;
				break;
				case 'rowcount':
					$rowcount=$v;
				break;
				case 'sortsql':
					$sortSQL = $v;
				break;
				case 'entrytype':
					$where[] = 'entry_type='.$db->qstr($v);
				break;
			}
		}}

		$this->sql="SELECT SQL_CALC_FOUND_ROWS l.* FROM seg_lingap_ledger_patient AS l\n";
		if ($where)
			$this->sql.="WHERE (".implode(")\n AND (",$where).")\n";
		$this->sql .= "ORDER BY $sortSQL\n".
			"LIMIT $offset, $rowcount";

		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}


	function getBalance($pid) {
		global $db;
		$this->sql = "SELECT running_balance FROM seg_lingap_patient_balance WHERE pid=".$db->qstr($pid);
		return $db->GetOne($this->sql);
	}

	function updateBalance($pid, $type, $amount) {
		global $db;

		$multiplier = array(
			'adjustment'=>1.0,
			'deposit'=>1.0,
			'grant'=>-1.0,
			'transfer'=>1.0
		);

		$amount = $amount * $multiplier[$type];

		$bal=$this->getBalance($pid);
		if (!$bal) $bal=0;

		if ($type=='adjustment')
			$bal=$amount;
		else
			$bal += $amount;
		$ret = $db->Replace('seg_lingap_patient_balance',
			array('pid'=>$pid,'running_balance'=>$bal),
			'pid',$autoquote = true);

		if ($ret===0)
			return FALSE;
		else
			return TRUE;
	}

	function cancelEntry($id) {
		global $db, $errorReporter, $HTTP_SESSION_VARS;

		$this->sql =
			"SELECT entry_id,ref_source,ref_no,service_code,pid,is_deleted,quantity,amount\n".
				"FROM seg_lingap_entry_details d\n".
				"WHERE entry_id=".$db->qstr($id);
		$info=$db->GetRow($this->sql);
		if ($info !== FALSE) {
			if (!$info) {
				$errorReporter->raise( SEG_ERROR_SQL_NO_RECORD,
					$debug=array( 'sql'=>$this->sql, 'error'=>$db->ErrorMsg() ),
					$tips=array( 'Details for this Lingap grant cannot be retrieved from the database.' )
				);
				return false;
			}
			if (strtolower($info['ref_source']) == 'fb') {
					$this->sql =
						"UPDATE seg_billing_encounter SET request_flag=NULL\n".
							"WHERE bill_nr=".$db->qstr($info['ref_no'])." AND request_flag='LINGAP'";
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
                if ($tables[strtoupper($data['src'])]=='seg_lab_servdetails'){
                    $history = $this->ConcatHistory("Update request_flag [NULL] -lingap patient ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				    $history_cond = ", history=".$history."\n";
                }
				$this->sql =
                    "UPDATE {$tables[$info['ref_source']]} SET request_flag=NULL,\n".
                        $history_cond.
						"WHERE refno=".$db->qstr($info['ref_no']).
						"AND {$codes[strtolower($info['ref_source'])]}=".$db->qstr($info['service_code']).
						"AND request_flag='LINGAP'";
			}

			$this->result = $db->Execute($this->sql);
			if ($this->result) {
				$this->sql=
					"DELETE FROM seg_lingap_entries \n".
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

}

