<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegPharmaRefund extends Core {
	
	var $target;
	var $refund_tb = "seg_pharma_refund";
	var $refund_items_tb = "seg_pharma_refund_items";
	var $person_tb = "care_person";

	
	var $fld_pharma_order = array(
		"refno",
		"orderdate",
		"pid",
		"encounter_nr",
		"ordername",
		"orderaddress",
		"is_cash",		
		"is_urgent",
		"discount",
		"discountid",
		"comments",
		"create_id",
		"create_time",
		"modify_id",
		"modify_time"
	);
	
	function SegPharmaRefund() {
		$this->coretable = $this->refund_tb;
	}
	
	function getLastNr($today) {
		global $db;
		$today = $db->qstr($today);
		$this->sql="SELECT IFNULL(MAX(CAST(refund_no AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(refund_no,1,4)=EXTRACT(YEAR FROM NOW())";
		return $db->GetOne($this->sql);
	}
	
	function clearRefundList($refund_no) {
		global $db;
		$refund_no = $db->qstr($refund_no);
		$this->sql = "DELETE FROM $this->refund_items_tb WHERE refund_no=$refund_no";
    return $this->Transact();
	}
	
	function addOrders($refno, $orderArray) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "INSERT INTO $this->items_tb(refno,bestellnum,quantity,pricecash,pricecharge,is_consigned) VALUES($refno,?,?,?,?,?)";
		if($buf=$db->Execute($this->sql,$orderArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
	
	function getRefundableItems($no, $type, $from="") {
		switch (strtoupper($type)) {
			case "RECEIPT":
				$where = "refno";
			break;
		}
		$this->sql = "SELECT p.* FROM care_pharma_products AS p\n".
			"WHERE p.bestellnum IN\n".
				"(SELECT DISTINCT bestellnum FROM seg_pharma_order_items\n".
					"LEFT JOIN seg_pharma_order ON WHERE )";
	}
	
	function grantPharmacyRequest($refno, $items) {
		global $db;
		if (!is_array($items)) return false;
		if (empty($arrayItems))
			return TRUE;
		$this->sql="INSERT INTO seg_granted_request (ref_no, ref_source, service_code) VALUES ($refno, 'PH', ?)";
		if ($db->Execute($this->sql,array($items))) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
	}
	
	function clearDiscounts($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->discounts_tb WHERE refno=$refno";
    return $this->Transact();
	}

	function getOrderInfo($refno) {
    global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT o.*,p.name_last,p.name_first,p.name_middle\n".
				"FROM $this->coretable AS o\n".
				"LEFT JOIN $this->person_tb AS p ON p.pid=o.pid\n".
				"WHERE o.refno=$refno";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
	
	function getOrderDiscounts($refno) {
    global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT discountid\n".
				"FROM $this->discounts_tb\n".
				"WHERE refno=$refno";
		if($this->result=$db->Execute($this->sql)) {
			$ret = array();
			while ($row = $this->result->FetchRow())
				$ret[$row['discountid']] = $row['discountid'];
			return $ret;
		} else { return false; }		
	}
	
	function getOrderItems($refno) {
    global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT i.*,p.artikelname,p.description\n".
				"FROM $this->items_tb AS i\n".
				"LEFT JOIN $this->prod_tb AS p ON p.bestellnum=i.bestellnum\n".
				"WHERE i.refno=$refno";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
	
	function getOrderItemsFullInfo($refno) {
    global $db;
		$refno = $db->qstr($refno);
		$this->sql = "SELECT o.quantity,o.pricecash AS `force_price`,o.is_consigned, a.*,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),b.cshrpriceppk*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),b.cshrpriceppk*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n".
				"IFNULL(b.ppriceppk,0) AS ppriceppk,\n".
				"IFNULL(b.chrgrpriceppk,0) AS chrgrpriceppk,\n".
				"IF(a.is_socialized,\n".
					"IFNULL((SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),b.cshrpriceppk),\n".
					"cshrpriceppk) AS dprice,\n".
				"IFNULL(b.cshrpriceppk,0) AS cshrpriceppk\n".
				"FROM seg_pharma_order_items AS o\n".
				"LEFT JOIN care_pharma_products_main AS a ON o.bestellnum=a.bestellnum\n".
				"LEFT JOIN seg_pharma_prices AS b ON a.bestellnum=b.bestellnum\n".
				"WHERE o.refno = $refno";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;	 
		} else { return false; }
	}
	
	function addDiscounts($refno, $discArray) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "INSERT INTO $this->discounts_tb(refno,discountid) VALUES($refno,?)";
		if($buf=$db->Execute($this->sql,$discArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
	
	function getActiveOrders($now) {
    global $db;
		if (is_numeric($now)) $dDate = date("Ymd",$now);
		$where = array();
		if ($dDate) $where[] = "o.orderdate=$dDate";
		else $dDate = $db->qstr($dDate);
		$this->sql="SELECT o.*,p.name_last,p.name_first,p.name_middle,\n".
				"(SELECT GROUP_CONCAT(prod.artikelname SEPARATOR '\\n')\n".
    			"FROM seg_pharma_order_items AS oi\n".
			    "LEFT JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
			    "WHERE o.refno = oi.refno) AS `items`\n".
				"FROM $this->coretable AS o\n".
				"LEFT JOIN $this->person_tb AS p ON p.pid=o.pid\n".
				"WHERE NOT EXISTS(SELECT * FROM seg_pay_request AS pr WHERE pr.ref_no=o.refno AND pr.ref_source='PH')\n".
				"ORDER BY orderdate DESC,is_urgent DESC,refno ASC";
		if($this->result=$db->Execute($this->sql)) {
			if($this->result->RecordCount()) {
				return $this->result;	 
			} else { return false; }
		} else { return false; }
	}
}

?>