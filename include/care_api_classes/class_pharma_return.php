<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegPharmaReturn extends Core {
	
	var $target;
	var $return_tb = "seg_pharma_returns";
	var $return_items_tb = "seg_pharma_return_items";
	var $person_tb = "care_person";
	
	var $fld_return;
	
	function SegPharmaReturn() {
		global $db;
		$this->coretable = $this->return_tb;
		$this->fld_return = $db->MetaColumnNames($this->return_tb);
		$this->fld_return_items = $db->MetaColumnNames($this->return_items_tb);
	}
	
	function useReturn() {
		$this->coretable = $this->return_tb;
		$this->setRefArray($this->fld_return);
	}
	
	function useReturnItems() {
		$this->coretable = $this->return_items_tb;
		$this->setRefArray($this->fld_return_items);
	}
	
	function getLastNr() {
		global $db;
		$this->useReturn();
		$this->sql="SELECT IFNULL(MAX(CAST(return_nr AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(return_nr,1,4)=EXTRACT(YEAR FROM NOW())";
		//$this->sql="SELECT IFNULL(MAX(CAST(return_nr AS UNSIGNED)+1),1) FROM $this->coretable";
		return $db->GetOne($this->sql);
	}
	
	function deleteEntry($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM $this->coretable WHERE return_nr=$nr";
    return $this->Transact();
	}
	
	function GetReturnInfo($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useReturn();
		$this->sql = "SELECT r.return_nr,r.return_date,r.refund_amount,r.refund_amount_fixed,r.comments
FROM $this->coretable AS r WHERE return_nr=$nr";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result->FetchRow();
		} else { return false; }
	}
	
	function GetReturnItems($nr) {
		global $db;
		if ($nr) $prevWhere = " AND rix.return_nr!=".$db->qstr($nr);
		$nr = $db->qstr($nr);
		$this->useReturn();
		$this->sql = "SELECT ri.return_nr,ri.ref_no,ri.bestellnum,ri.quantity AS `returned`,
p.artikelname,p.generic,
oi.pricecash AS `price`,oi.quantity,
(SELECT SUM(quantity) FROM seg_pharma_return_items AS rix WHERE rix.bestellnum=ri.bestellnum AND rix.ref_no=ri.ref_no $prevWhere) AS previous_returns
FROM seg_pharma_return_items AS ri
LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=ri.bestellnum
LEFT JOIN seg_pharma_order_items AS oi ON oi.refno=ri.ref_no AND oi.bestellnum=ri.bestellnum
WHERE ri.return_nr=$nr";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	
	function GetORDetailsForReturn($orno, $returnNr=NULL) {
		global $db;
		$orno = $db->qstr($orno);
		if ($returnNr) $prevWhere = " AND rix.return_nr!=".$db->qstr($returnNr);
		$this->sql = "SELECT r.ref_no,r.service_code,r.ref_source,r.qty,r.amount_due,\n".
			"p.artikelname,p.generic,p.prod_class,IF(o.is_cash,p.price_cash,p.price_charge) AS price,\n".
			"o.is_cash,i.serve_status,\n".
			"(SELECT SUM(quantity) FROM seg_pharma_return_items AS rix WHERE rix.bestellnum=r.service_code AND rix.ref_no=r.ref_no $prevWhere) AS previous_returns\n".
			"FROM seg_pay_request AS r\n".
			"LEFT JOIN seg_pharma_orders AS o ON o.refno=r.ref_no\n".
			"LEFT JOIN seg_pharma_order_items AS i ON i.refno=r.ref_no AND i.bestellnum=r.service_code\n".
			"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=r.service_code\n".
			"WHERE or_no = $orno AND r.ref_source='PH' ORDER BY p.artikelname";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	
	function GetRefDetailsForReturn($refno, $returnNr=NULL) {
		global $db;
		$refno = $db->qstr($refno);
		if ($returnNr) $prevWhere = " AND rix.return_nr!=".$db->qstr($returnNr);
		$this->sql = "SELECT i.refno AS `ref_no`,i.bestellnum AS `service_code`,i.quantity As `qty`,\n".
			"p.artikelname,p.generic,p.prod_class,IF(o.is_cash,i.pricecash,i.pricecharge) AS price,\n".
			"o.is_cash,i.serve_status,\n".
//			"EXISTS(SELECT * FROM seg_pharma_return_items AS rix WHERE rix.bestellnum=i.bestellnum AND rix.ref_no=i.refno $prevWhere) AS returned\n".
			"(SELECT SUM(quantity) FROM seg_pharma_return_items AS rix WHERE rix.bestellnum=i.bestellnum AND rix.ref_no=i.refno $prevWhere) AS previous_returns\n".
			"FROM seg_pharma_order_items AS i\n".
			"LEFT JOIN seg_pharma_orders AS o ON i.refno=o.refno\n".
			"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=i.bestellnum\n".
			"WHERE i.refno=$refno ORDER BY p.artikelname";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	
	function addReturnItems($nr, $itemArray) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useReturnItems();
		$this->sql = "INSERT INTO $this->coretable(return_nr,ref_no,bestellnum,quantity) VALUES($nr,?,?,?)";
		if($buf=$db->Execute($this->sql,$itemArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
	
	function clearReturnItems($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useReturnItems();
		$this->sql = "DELETE FROM $this->coretable WHERE return_nr=$nr";
    return $this->Transact();
	}
	
	function getEntries($filters, $offset=0, $rowcount=15) {
    global $db;
		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;
		
		if (is_array($filters)) {
		foreach ($filters as $i=>$v) {
			switch (strtolower($i)) {
				case 'nr':
					$phFilters[] = "r.return_nr=".$db->qstr($v);
				break;
				case 'datetoday':
					$phFilters[] = 'DATE(r.return_date)=DATE(NOW())';
				break;
				case 'datethisweek':
					$phFilters[] = 'YEAR(r.return_date)=YEAR(NOW()) AND WEEK(r.return_date)=WEEK(NOW())';
				break;
				break;
				case 'datethismonth':
					$phFilters[] = 'YEAR(r.return_date)=YEAR(NOW()) AND MONTH(r.return_date)=MONTH(NOW())';
				break;
				case 'date':
					$phFilters[] = "DATE(r.return_date)='$v'";
				break;
				case 'datebetween':
					$phFilters[] = "r.return_date>='".$v[0]."' AND r.return_date<='".$v[1]."'";
				break;
				case 'product':
					$phFilters[] = "EXISTS(SELECT * FROM seg_pharma_return_items AS i WHERE bestellnum=".$db->qstr($v)." AND i.return_nr=r.return_nr)";
				break;
			}
		}}
		
		$phWhere=implode(") AND (",$phFilters);
		if ($phWhere) $phWhere = "($phWhere)";
		else $phWhere = "1";
		
		$this->sql="SELECT SQL_CALC_FOUND_ROWS r.*,
(SELECT GROUP_CONCAT(CONCAT(p.artikelname,':',i.quantity) SEPARATOR '\\n') FROM seg_pharma_return_items AS i
LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=i.bestellnum
WHERE i.return_nr=r.return_nr) AS `items`
FROM seg_pharma_returns AS r
WHERE ($phWhere)\n";
		$this->sql .= "ORDER BY r.return_date DESC\n" . 
			"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
	
}

?>