<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCMAP extends Core {

	var $target;
	var $cmap_tb = "seg_cmap_entries";
	var $cmap_items_tb="seg_cmap_entry_details";

	var $fld_cmap;
	var $fld_cmap_items;

	function SegCMAP() {
		global $db;
		$this->coretable = $this->cmap_tb;
		$this->fld_cmap = $db->MetaColumnNames($this->cmap_tb);
		$this->setRefArray($this->fld_cmap);

		$this->fld_cmap_items = $db->MetaColumnNames($this->cmap_items_tb);
	}

	function getNewControl($today=NULL) {
		global $db;
		$today = $db->qstr($today);
		$this->sql="SELECT IFNULL(MAX(CAST(control_nr AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(control_nr,1,4)=EXTRACT(YEAR FROM NOW())";
		return $db->GetOne($this->sql);
	}

	function deleteEntry($id) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->coretable WHERE entry_id=".$db->qstr($id);
		return $this->Transact();
	}

	function clearEntry($id) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->cmap_items_tb WHERE entry_id=".$db->qstr($id);
		return $this->Transact();
	}

	function addDetails($id, $arr) {
		global $db, $HTTP_SESSION_VARS;
		//$this->sql = "INSERT INTO $this->cmap_items_tb(entry_id,ref_source,ref_no,service_code,service_name,quantity,amount) VALUES(".$db->qstr($id).",?,?,?,?,?,?)";
		$this->_statement = "INSERT INTO $this->cmap_items_tb(entry_id,ref_source,ref_no,service_code,service_name,quantity,amount) VALUES(".$db->qstr($id).",?,?,?,?,?,?)";

		foreach ($arr as $i=>$v) {

			$src = $v[0];
			$ref = $v[1];
			$item = $v[2];
			$name = $v[3];
			$qty = $v[4];
			$amt = $v[5];

			$this->result=$db->Execute( $this->_statement, array($src, $ref, $item, $name, $qty, $amt) );
			if ($this->result) {
				switch (strtoupper($src)) {
					case 'PH':
						$this->result=$db->Replace('seg_pharma_order_items',
							array(
								'refno'=>$ref,
								'bestellnum'=>$item,
								'request_flag'=>'CMAP'
							),
							array('refno','bestellnum'),
							$autoquote=TRUE
						);
					break;
					case 'LD':
                        #added by VAS 07-04-2012
                        #add history for update
                        $history = $this->ConcatHistory("Update request_flag [CMAP] ".date('Y-m-d H:i:s')." at CMAP ".$HTTP_SESSION_VARS['sess_user_name']."\n");
						$this->result=$db->Replace('seg_lab_servdetails',
							array(
								'refno'=>$ref,
								'service_code'=>$item,
								'request_flag'=>'CMAP',
                                'history'=>$history
							),
							array('refno','service_code'),
							$autoquote=TRUE
						);
					break;
					case 'RD':
						$this->result=$db->Replace('care_test_request_radio',
							array(
								'refno'=>$ref,
								'service_code'=>$item,
								'request_flag'=>'CMAP'
							),
							array('refno','service_code'),
							$autoquote=TRUE
						);
					break;
					default:
						$this->result=FALSE;
					break;
				}

				if ($this->result===0) return FALSE;
			}
			else {
				return FALSE;
			}
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
			"(SELECT GROUP_CONCAT(d.service_name SEPARATOR ', ') FROM seg_cmap_entry_details AS d WHERE d.entry_id=e.entry_id) AS `items`,\n".
			"(SELECT SUM(d.amount) FROM seg_cmap_entry_details AS d WHERE d.entry_id=e.entry_id) AS `total`\n".
			"FROM seg_cmap_entries AS e\n".
			"$whereSQL\n";
		$this->sql .= "ORDER BY $sortSQL\n" .
			"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }

	}


}
