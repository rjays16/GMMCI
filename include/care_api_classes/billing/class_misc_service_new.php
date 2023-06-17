<?php
require_once($root_path.'include/care_api_classes/class_core.php');

class SegMiscService extends Core {

	var $tb_main = 'seg_other_services';
	var $tb_pharma = 'care_pharma_products_main';
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
						
	function SegMiscService() {
		$this->coretable = $this->tb_main;
		$this->setRefArray($this->fld_main);
	}
	
	function searchServices($name, $type, $include_locked=FALSE, $offset=0, $rowcount=15) {
		global $db;
		
		$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.name as item_name,s.price,s.alt_service_code AS item_code,
					t.name_long AS type_name,p.name_long AS ptype_name,s.account_type, 'MISC' AS source
					FROM $this->tb_main AS s
					LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
					LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id ";
		$sql_ph = " SELECT IF(p.generic!='', CONCAT(TRIM(p.artikelname),' - ',UPPER(TRIM(p.generic))), 
					p.artikelname) AS item_name, p.price_charge AS price, p.bestellnum AS item_code, 
					'' AS type_name, '' AS ptype_name, '' AS account_type, 'PHARMA' AS source
					FROM $this->tb_pharma AS p
					WHERE prod_class = 'S' ";
		$where = array();
		$where_ph = array();
		if ($name) {
			$where[] = " s.name REGEXP ".$db->qstr('[[:<:]]'.$name);
			$where_ph[] = " p.artikelname REGEXP ".$db->qstr('[[:<:]]'.$name);
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
			$where[] = " NOT s.lockflag ";
			$where_ph[] = " NOT p.lock_flag ";
		}
		
		$this->sql.= " WHERE (t.billing_related <> 0) ";
		
		if ($where)
			$this->sql.= " AND (" .  implode(") AND (", $where) . ") ";
		
		$this->sql .= " UNION ".$sql_ph;
		
		if($where_ph)
			$this->sql .= " AND (" .  implode(") AND (", $where_ph) . ") ";

		$this->sql .= " ORDER BY item_name ";			
		if ($offset >= 0 && $rowcount >= 0) {
			$this->sql.= " LIMIT $offset, $rowcount ";
		}
		#echo 'sql '. $this->sql;
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
