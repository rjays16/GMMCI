<?php
require_once($root_path.'include/care_api_classes/class_core.php');

class SegCashierService extends Core {
	/**#@+
	* @access private
	* @var string
	*/

	/**
	* Tables
	*/
	var $target;
	var $tb_main					= 'seg_other_services';
	var $tb_type 					= 'seg_cashier_account_subtypes';

	/**
	* Field names of care_pharma_products_main or care_med_products_main tables
	* @var array
	*/

//	'is_billing_related',	(after account_type field).

	var $fld_main=array('service_code',
										'name',
										'name_short',
										'price',
										'description',
										'account_type',
										'lockflag',
                                                                                'dept_nr', //added by cha, used in cmap - 11.26.2010
										'history',
										'modify_id',
										'modify_time',
										'create_id',
										'create_time');

	/**
	* Constructor
	*/
	//function SegCashierService($target='databank') {
	//comment out by cha, 11-26-2010
	function SegCashierService($target=FALSE) {
		$this->target = $target;
		$this->coretable = $this->tb_main;
		$this->setRefArray($this->fld_main);
	}

	function getLastNr() {
		global $db;
		$this->sql="SELECT IFNULL(LPAD(MAX(CAST(service_code AS UNSIGNED)+1),8,'0'),'00000001') FROM $this->coretable";
		return $db->GetOne($this->sql);
	}

	function searchOLRServices($name, $olr, $offset, $rowcount) {
		global $db;

		$sql = array();
		$calc = "";
		if (strpos($olr,'o')!==FALSE) {
			$sql[] = "(SELECT ".(!$calc ? $calc="SQL_CALC_FOUND_ROWS" : "") ."\n".
				"'O' AS `source`,o.code AS `code`,o.description AS `name`,'Procedure' AS `group`\n".
				"FROM care_ops301_en AS o\n".
				"WHERE o.description REGEXP " . $db->qstr("[[:<:]]".$name).")";
		}
		if (strpos($olr,'l')!==FALSE) {
			$sql[] = "(SELECT ".(!$calc ? $calc="SQL_CALC_FOUND_ROWS" : "") ."\n".
				"'L' AS `source`,l.service_code AS `code`,l.name AS `name`,lg.name AS `group`\n".
				"FROM seg_lab_services AS l\n".
				"LEFT JOIN seg_lab_service_groups AS lg ON lg.group_code=l.group_code\n".
				"WHERE l.name REGEXP " . $db->qstr("[[:<:]]".$name).")";
		}
		if (strpos($olr,'r')!==FALSE) {
			$sql[] = "(SELECT ".(!$calc ? $calc="SQL_CALC_FOUND_ROWS" : "") ."\n".
				"'R' AS `source`,r.service_code AS `code`,r.name AS `name`,rg.name AS `group`\n".
				"FROM seg_radio_services AS r\n".
				"LEFT JOIN seg_radio_service_groups AS rg ON rg.group_code=r.group_code\n".
				"WHERE r.name REGEXP " . $db->qstr("[[:<:]]".$name).")";
		}
		$this->sql = implode(" UNION ", $sql);
		if ($this->sql) $this->sql .= "\nORDER BY `name`,`group`\n";
		$this->sql .=	"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function deleteService($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM $this->coretable WHERE service_code=$nr";
		return $this->Transact();
	}

	function getPayWardSubTypes() {
		global $db;

		$this->sql = "select type_id ".
					 "   from seg_cashier_account_subtypes as scas ".
						 "   where scas.billing_related <> 0";
		if ($result = $db->Execute($this->sql)) {
			if ($result->RecordCount()) {
				return $result->GetArray();
			} else { return FALSE; }
		} else { return FALSE; }
	}

	function searchServices($name, $type, $include_locked=FALSE, $offset=0, $rowcount=15, $orderby='s.name') {
		global $db;

		#modified by CHA, 06-06-2010
		#modified by cha, 11-26-2010 /*added care_department*/
		//edited by: ian villanueva 1-9-2013
		$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.name,s.name_short,s.price, \n".
			"s.service_code AS code, s.alt_service_code AS alt_code, s.description,t.name_long AS type_name, \n".
			"p.name_long AS ptype_name,s.account_type,s.`is_not_socialized`, d.name_formal AS dept_name\n".
			"FROM seg_other_services AS s\n".
			"LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id\n".
			"LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id\n".
			"LEFT JOIN care_department AS d ON d.nr=s.dept_nr";
		$where = array();
		if ($name) {
			#$codename = $db->qstr($codename);
			if (is_numeric($name)) {
				$where[] = "s.service_code=".(int)$name;
			}
			else {
				$where[] = "s.name REGEXP ".$db->qstr('[[:<:]]'.$name)." OR s.name_short REGEXP ".$db->qstr('[[:<:]]'.$name);
			}
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

		if ($this->target) {
			if ($this->target == "databank") $where[] = "t.billing_related=0";
			elseif ($this->target == "miscellaneous") $where[] = "t.billing_related=1";
		}

		if ($where)
			$this->sql.= " WHERE (" .  implode(") AND (", $where) . ")\n";

		$this->sql .= "ORDER BY $orderby\n";
		if ($offset >= 0 && $rowcount >= 0) {
			$this->sql.= "LIMIT $offset, $rowcount\n";
		}
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function searchServicesSansBillingCharges($name, $type, $include_locked=FALSE, $offset=0, $rowcount=15, $orderby='s.name') {
		global $db;

		$this->sql = "SELECT SQL_CALC_FOUND_ROWS s.name,s.name_short,s.price,s.service_code AS code,s.description,t.name_long AS type_name,p.name_long AS ptype_name,s.account_type,s.lockflag\n".
			"FROM seg_other_services AS s\n".
			"LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id\n".
			"LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id\n";
		$where = array( 'is_billing_related=0' );
		if ($name) {
			#$codename = $db->qstr($codename);
			if (is_numeric($name)) {
				$where[] = "s.service_code=".(int)$name;
			}
			else {
				$where[] = "s.name REGEXP ".$db->qstr('[[:<:]]'.$name)." OR s.name_short REGEXP ".$db->qstr('[[:<:]]'.$name);
			}
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

		if ($this->target) {
			if ($this->target == "databank") $where[] = "t.billing_related=0";
			elseif ($this->target == "miscellaneous") $where[] = "t.billing_related=1";
		}

		if ($where)
			$this->sql.= " WHERE (" .  implode(") AND (", $where) . ")\n";

		$this->sql .= "ORDER BY $orderby\n";
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
			"FROM seg_other_services WHERE service_code=$nr";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				$row = $buf->FetchRow();
				return $row;
			} else { return false; }
		} else { return false; }
	}

	/**
	* Checks if the service exists based on its primary key number.
	* @access public
	* @param int Item number
	* @return boolean
	*/
	function ServiceExists($nr=0){
		global $db;
		if(empty($type)||!$nr) return false;
		$this->sql="SELECT service_code FROM $this->coretable WHERE servicecode='$nr'";
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}

	function getAccountTypes($include_locked=FALSE,$exclude=NULL,$billing_related_only=FALSE) {
		global $db;
		if (!is_array($select) && $select) $select = array($select);
		$this->sql = "SELECT a.type_id,a.name_short,a.name_long FROM seg_cashier_account_types AS a\n";
		$where = array();
		if (!$include_locked)
			$where[] = "NOT a.lockflag";
		if ($exclude) {
			$where[] = "type_id NOT IN (".implode(",",$exclude).")";
		}
			if ($billing_related_only) {
			$where[] = "EXISTS (select * from seg_cashier_account_subtypes as scas\n".
						 "           where scas.parent_type = a.type_id and scas.billing_related)";
		}
		if ($where) $this->sql .= "WHERE (".implode(") AND (",$where).")\n";
		$this->sql .= "ORDER BY name_long\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else return false;
	}

	function getSubAccountTypes($parent=NULL, $include_locked=FALSE, $exclude=NULL,$billing_related_only=FALSE) {
		global $db;
		if (!is_array($parent) && $parent) $parent = array($parent);
		if (!is_array($select) && $select) $select = array($select);
		$this->sql = "SELECT a.type_id,a.name_short,a.name_long,a.parent_type,b.name_short AS parent_short,b.name_long AS parent_long FROM seg_cashier_account_subtypes AS a\n".
			"LEFT JOIN seg_cashier_account_subtypes AS b ON a.parent_type=b.type_id\n";
		$where = array();
		if ($parent) {
			$where[] = "a.parent_type IN ".implode(",",$parent).")";
		}
		if (!$include_locked)
			$where[] = "NOT a.lockflag AND NOT b.lockflag";
		if ($exclude) {
			$where[] = "a.type_id NOT IN (".implode(",",$exclude).")";
		}
		if ($billing_related_only) {
			$where[] = "a.billing_related";
		}
		if ($where) $this->sql .= "WHERE (".implode(") AND (",$where).")\n";
		$this->sql .= "ORDER BY a.name_long\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else return false;
	}


#added by daryl
	function Accomodation_OR_data ($type,$encoder,$date_start,$date_end){
		global $db;

switch ($type) {
	case 'acc':
		$or_amount = "SUM(IFNULL(sbe.total_acc_charge, 0)) - SUM(IFNULL(sbc.total_acc_coverage, 0)) - SUM(IFNULL(sbd.total_acc_discount, 0))";
		break;
	case 'or':
		$or_amount = "SUM(IFNULL(sbe.total_ops_charge, 0)) - SUM(IFNULL(sbc.total_ops_coverage, 0)) - SUM(IFNULL(sbd.total_ops_discount, 0))";
		break;
	default:
		# code...
		break;
}

if($encoder)
$or_encoder = "AND pay.modify_id =".$db->qstr($encoder);

		$this->sql = "SELECT 
					  SQL_CALC_FOUND_ROWS pay.`or_no`,
					  pay.`or_date`,
					  pay.`or_name`,
					  $or_amount AS or_amount,
					  pay.`modify_id` as or_encoder
					FROM
					  seg_pay AS pay 
					  INNER JOIN seg_pay_request pay_r 
					    ON pay.`or_no` = pay_r.`or_no` 
					  INNER JOIN seg_billing_encounter sbe 
					    ON sbe.`bill_nr` = pay_r.`service_code` 
					  LEFT JOIN seg_billing_coverage sbc 
					    ON sbc.`bill_nr` = sbe.`bill_nr` 
					  LEFT JOIN seg_billingcomputed_discount sbd 
					    ON sbd.`bill_nr` = sbc.`bill_nr` 
					WHERE pay_r.`ref_source` IN ('FB') 
					$or_encoder
					  AND DATE(pay.`or_date`) BETWEEN ".$db->qstr($date_start)." 
					  AND  ".$db->qstr($date_end)."
					GROUP BY or_no 
					ORDER BY or_date ASC ";

		if($result = $db->GetAll($this->sql))
            return $result;
        else
            return false;
	}


}
?>
