<?php
require_once($root_path.'include/care_api_classes/class_core.php');

class SegMiscService extends Core {

	var $tb_main = 'seg_other_services';	
	var $fld_main=array('service_code',
						'name',
						'price',
						'status',
						'locked',
						'account_type',
						'history',
						'modify_id',
						'modify_dt',
						'create_id',
						'create_dt');

	#added by genz
	function GetBillPatientOperations($enc_nr){
		global $db;
		$this->sql = "SELECT 
					  smod.`ops_code` AS ops_code,
					  smod.`description` AS description,
					  smod.`op_date` AS ops_date,
					  smod.`rvu` AS rvu
					FROM
					  seg_misc_ops smo 
					  INNER JOIN seg_misc_ops_details smod 
					    ON smod.`refno` = smo.`refno` 
					WHERE smo.`encounter_nr` = ".$db->qstr($enc_nr);

		#echo $this->sql;

		if($this->result=$db->Execute($this->sql)) {
		    if($this->result->RecordCount()) {
				return $this->result;
		    } else { return FALSE;}
		} else { return FALSE;}
	}
	#end by genz
						
	function SegMiscService() {
		$this->coretable = $this->tb_main;
		$this->setRefArray($this->fld_main);
	}
	
	function searchServices($name, $type, $include_locked=FALSE, $offset=0, $rowcount=15) {
		global $db;
		
		$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.name,s.price,s.alt_service_code AS code,t.name_long AS type_name,p.name_long AS ptype_name,s.account_type\n".
			"FROM $this->tb_main AS s\n".
			"LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id\n".
			"LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id\n";
		$where = array();
		if ($name) {
			#$codename = $db->qstr($codename);
			$where[] = "s.name REGEXP ".$db->qstr('[[:<:]]'.$name);
		}
		if ($type) {
			if (is_array($type)) {			
				if (count($type) > 0) 
					$where[] = "s.account_type IN "."(".implode(", ", $type).") OR s.account_type is null";
				else
					$where[] = "s.account_type is null";
			}
			else
				$where[] = "s.account_type=".$db->qstr($type);
		}
		
		if (!$include_locked) {
			$where[] = " NOT s.lockflag";
		}
		
		$this->sql.= " WHERE (t.billing_related <> 0)";
		
		if ($where)
			$this->sql.= " AND (" .  implode(") AND (", $where) . ")\n";

		$this->sql .= "ORDER BY s.name\n";			
		if ($offset >= 0 && $rowcount >= 0) {
			$this->sql.= "LIMIT $offset, $rowcount\n";
		}
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
	
	function getServiceInfo($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "SELECT *\n".
			"FROM $this->tb_main WHERE alt_service_code=$nr";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				$row = $buf->FetchRow();
				return $row;
			} else { return false; }
		} else { return false; }
	}											
}
?>
