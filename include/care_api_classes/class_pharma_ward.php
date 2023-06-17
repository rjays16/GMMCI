<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegPharmaWard extends Core {
	
	var $items_tb;
	var $discounts_tb;
	var $seg_discounts_tb = "seg_discounts";
	var $person_tb = "care_person";


	var $fld_ward = array(
		"ward_id",
		"ward_name",
		"create_id",
		"create_dt",
		"modify_id",
		"modify_dt"
	);
		
	
	var $fld_stock = array(
		"stock_nr",
		"stock_date",
		"pharma_area",
		"ward_id",
		"ward_name",
		"create_id",
		"create_time",
		"modify_id",
		"modify_time",
		"history"
	);
	
	var $fld_stock_items = array(
		"stock_nr",
		"bestellnum",
		"quantity"
	);
	
	var $ward_tb = 'seg_pharma_wards';
	var $stock_tb = 'seg_pharma_ward_stocks';
	var $stock_items_tb = 'seg_pharma_ward_stock_items';
	var $prod_tb = 'care_pharma_products_main';
	
	function SegPharmaWard() {
		$this->useWard();
	}
	
	function useWard() {
		$this->coretable = $this->ward_tb;
		$this->setRefArray($this->fld_ward);
	}
	
	function useStock() {
		$this->coretable = $this->stock_tb;
		$this->setRefArray($this->fld_stock);
	}
	
	function useStockItems() {
		$this->coretable = $this->stock_items_tb;
		$this->setRefArray($this->fld_stock_items);
	}
	
	function createPharmaWard($name) {
		global $db;
		$this->useWard();
		$name = $db->qstr($name);
		$id = $db->qstr($_SESSION['sess_temp_userid']);
		$this->sql = "INSERT INTO $this->coretable(ward_name,create_id,create_dt,modify_id,modify_dt) VALUES($name,$id,NOW(),$id,NOW())";
		if($buf=$db->Execute($this->sql)) {
			return $db->Insert_ID();
		} else { return false; }
	}
	
	function updatePharmaWard($wid,$name) {
		global $db;
		$this->useWard();
		$wid = $db->qstr($wid);
		$name = $db->qstr($name);
		$id = $db->qstr($_SESSION['sess_temp_userid']);
		$this->sql = "UPDATE $this->coretable SET ward_name=$name,modify_id=$id,modify_dt=NOW() WHERE ward_id=$wid";
		if($buf=$db->Execute($this->sql)) {
			return true;
		} else { return false; }
	}
	
	function getAll() {
		global $db;
		$this->useWard();
		$this->sql="SELECT SQL_CALC_FOUND_ROWS p.* FROM $this->coretable AS p ORDER BY ward_name\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
	
	function deletePharmaWard($id) {
		global $db;
		$this->useWard();
		$id = $db->qstr($id);
		$this->sql = "DELETE FROM $this->coretable WHERE ward_id=$id";
		return $this->Transact();
	}
	
	function getLastNr() {
		global $db;
		$this->useStock();
		$today = $db->qstr($today);
		$this->sql="SELECT IFNULL(MAX(stock_nr)+1,1) FROM $this->coretable";
		return $db->GetOne($this->sql);
	}
	
	function addStocks($nr, $stockArray) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useStockItems();
		$this->sql = "INSERT INTO $this->coretable(stock_nr,bestellnum,quantity) VALUES($nr,?,?)";
		if($buf=$db->Execute($this->sql,$stockArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
	
	function clearStocks($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useStockItems();
		$this->sql = "DELETE FROM $this->coretable WHERE stock_nr=$nr";
		return $this->Transact();
	}
	
	function getStockDetails($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useStock();
		$this->sql="SELECT s.*\n".
			"FROM $this->coretable AS s\n".
			"WHERE s.stock_nr=$nr";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result->FetchRow();	 
		} else { return false; }
	}
	
	function getStockItems($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useStockItems();
		$this->sql="SELECT i.*,p.artikelname,p.generic\n".
			"FROM $this->stock_items_tb AS i\n".
			"LEFT JOIN $this->prod_tb AS p ON p.bestellnum=i.bestellnum\n".
			"WHERE i.stock_nr=$nr";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
		
	function getStockList($filters, $offset=0, $rowcount=15, $sort_sql="stock_date DESC") {
		global $db;

		$this->useStock();
		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;
		
				$phFilters = array();
		if (is_array($filters)) {
		foreach ($filters as $i=>$v) {
			switch (strtolower($i)) {
				case 'area':
					if (strtoupper($v)!='ALL')
						$phFilters[] = 'pharma_area='.$db->qstr($v);
				break;
				case 'encoder':
					$phFilters[] = 's.create_id='.$db->qstr($v);
				break;
				case 'refno':
					$phFilters[] = "s.stock_nr='$v'";
				break;
				case 'thisshift':
					$phFilters[] = "IF(HOUR(NOW())<7,".
						"s.stock_date<=NOW() AND s.stock_date>=FROM_UNIXTIME(UNIX_TIMESTAMP(DATE(NOW()))-61200),".
						"s.stock_date<=NOW() AND s.stock_date>=FROM_UNIXTIME(UNIX_TIMESTAMP(DATE(NOW()))+25200))";
				break;
				case 'datetoday':
					$phFilters[] = 'DATE(stock_date)=DATE(NOW())';
				break;
				case 'datethisweek':
					$phFilters[] = 'YEAR(stock_date)=YEAR(NOW()) AND WEEK(stock_date)=WEEK(NOW())';
				break;
				case 'datethismonth':
					$phFilters[] = 'YEAR(stock_date)=YEAR(NOW()) AND MONTH(stock_date)=MONTH(NOW())';
				break;
				case 'date':
					$phFilters[] = "DATE(stock_date)='$v'";
				break;
				case 'datebetween':
					$phFilters[] = "DATE(stock_date)>='".$v[0]."' AND DATE(stock_date)<='".$v[1]."'";
				break;
				case 'ward':
					$phFilters[] = "s.ward_id='$v'";
				break;
			}
		}}
		
		$phWhere=implode(") AND (",$phFilters);
		if ($phWhere) $phWhere = "($phWhere)";
		else $phWhere = "1";
		
#		$havingClause = implode(") AND (",$filters);
#		if ($havingClause) $havingClause = "HAVING ($havingClause)";
		$this->sql="SELECT SQL_CALC_FOUND_ROWS s.*,u.name AS `encoder`,w.ward_name,a.area_name AS `area_full`,\n".
			"(SELECT GROUP_CONCAT(CONCAT(prod.artikelname,' (',si.quantity,')') SEPARATOR '\\n')\n".
				"FROM seg_pharma_ward_stock_items AS si\n".
				"LEFT JOIN care_pharma_products_main AS prod ON prod.bestellnum=si.bestellnum\n".
				"WHERE s.stock_nr = si.stock_nr) AS `items`\n".
			"FROM $this->coretable AS s\n".
			"LEFT JOIN seg_pharma_areas AS a ON a.area_code=s.pharma_area\n".
			"LEFT JOIN seg_pharma_wards AS w ON w.ward_id=s.ward_id\n".
			"LEFT JOIN care_users AS u ON u.login_id=s.create_id\n".
			"WHERE\n".
				"($phWhere)\n";
		if ($phHaving) $this->sql .= "HAVING (" . implode(") AND (",$phHaving) . ")\n";
		$this->sql .= "ORDER BY $sort_sql\n" . 
			"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
}

?>