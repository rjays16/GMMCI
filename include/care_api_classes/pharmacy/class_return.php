<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
//require_once($root_path.'include/care_api_classes/inventory/class_inventory_helper.php');

class PharmacyReturn extends Core {

	var $target;
	var $return_tb = "seg_pharma_returns";
	var $return_items_tb = "seg_pharma_return_items";
	var $person_tb = "care_person";

	var $fld_return;

	function PharmacyReturn() {
		global $db;
		$this->coretable = $this->return_tb;
		$this->fld_return = $db->MetaColumnNames($this->return_tb);
		$this->fld_return_items = $db->MetaColumnNames($this->return_items_tb);

		$gc = new GlobalConfig($GLOBAL_CONFIG);
		$gc->getConfig('pharmacy_return_cutoff');
		if ( $GLOBAL_CONFIG['pharmacy_return_cutoff'] ) {
			$this->CutOff = $GLOBAL_CONFIG['pharmacy_return_cutoff'];
		}
		else {
			$this->CutOff = '3 MONTH';
		}
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
		return $db->GetOne($this->sql);
	}

	function deleteEntry($nr) {
		global $db;
		$this->sql = "DELETE FROM $this->coretable WHERE return_nr=".$db->qstr($nr);
		$this->result = $db->Execute($this->sql);
		if ($this->result !== false) {
			return true;
		}
		else {
			return false;
		}
	}

	function GetCutoff() {
		return $this->CutOff;
	}

	function GetReturnables( $filters ) {
		global $db;

		$offset = 0;
		$row_count = 10;
		$sort = 'artikelname ASC';


		$where = array();
		$where[] = "oi.serve_status='S'";
		$where[] = "o.is_cash=0";

		$return_id = null;
		foreach ($filters as $i=>$v) {
			switch (strtolower($i)) {
				case 'returnid':
					$return_id = $v;
					break;
				case 'pid':
					if ($v) {
						$where[] = "o.pid=".$db->qstr($v);
					}
					break;
				case 'encounter':
					if ($v) {
						$where[] = "o.encounter_nr=".$db->qstr($v);
					}
					break;
				case 'name':
					if ($v) {
						$where[] = "p.artikelname REGEXP ".$db->qstr("[[:<:]]$v");
					}
					break;
				case 'offset':
					$offset = (int) $v;
					break;
				case 'maxrows':
					$row_count = (int) $v;
					break;
				case 'sort':
					$sort = $v;
					break;
			}
		}

		if ($this->CutOff) {
			$where[] = "DATE(oi.serve_dt) >= DATE(NOW()-INTERVAL $this->CutOff)";
		}

		$this->sql =
			"SELECT SQL_CALC_FOUND_ROWS oi.bestellnum,p.artikelname,p.generic,p.packing,\n".
				"(SELECT IFNULL(SUM(IFNULL(r.quantity,0)),0) FROM seg_pharma_return_items r\n".
					"WHERE r.ref_no=oi.refno AND r.bestellnum=oi.bestellnum".
						($return_id ? (" AND return_nr<>".$db->qstr($return_id)) : "").
				") `returns`,\n".
				"SUM(oi.quantity) `served`\n".
			"FROM seg_pharma_order_items oi\n".
			"INNER JOIN seg_pharma_orders o ON o.refno=oi.refno\n".
			"INNER JOIN care_person cp ON cp.pid=o.pid\n".
			"INNER JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum\n".
			"WHERE (".implode(")\n AND (", $where).")\n".
			"GROUP BY oi.bestellnum\n".
			"ORDER BY $sort\n".
			"LIMIT $offset,$row_count\n";
		if(($this->result=$db->Execute($this->sql))!== false) {
			return true;
		} else { return false; }
	}

	function GetReturnInfo($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useReturn();
		$this->sql = "SELECT r.return_nr,r.pharma_area,r.return_name,r.return_address,r.pid,r.encounter_nr,r.return_date,r.refund_amount,r.comments
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

	/**
	* put your comment there...
	*
	* @param mixed $nr
	* @param mixed $bulk
	*/
	function returnItems($nr, $bulk) {
		global $db;

		//$inventoryHelper = new InventoryHelper();

		if (!$bulk)
			return false;

		$this->sql = "SELECT ref_no ref,bestellnum code,quantity qty FROM seg_pharma_return_items WHERE return_nr=".$db->qstr($nr);
		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		if ( ($result=$db->Execute($this->sql)) !== false) {
			$old_returns = $result->GetRows();
		}
		else {
			return false;
		}

		if ($old_returns) {
			// re-key $old_returns array, so instead of numeric keys, we
			// have the reference no and item no as the combined key.
			// this will allow for easy comparison between old return items
			// and the new return items
			foreach ($old_returns as $i=>$r) {
				$new_key = $r['ref'].$r['code'];
				$old_returns[$new_key] = $r;
				unset($old_returns[$i]);
			}
		}

		// re-key new return items as well
		$new_returns = array();
		foreach ($bulk as $i=>$b) {
			$new_key = $b['ref'].$b['code'];
			$new_returns[$new_key] = $b;
		}

		$areas = array();
		foreach ($new_returns as $i=>$new) {

			$result = $db->Replace('seg_pharma_return_items',
				array(
					'return_nr'=>$db->qstr($nr),
					'ref_no'=>$db->qstr($new['ref']),
					'bestellnum'=>$db->qstr($new['code']),
					'quantity'=>$db->qstr($new['qty'])
				),
				array('return_nr', 'ref_no', 'bestellnum'),
				$autoQuote=false
			);
			if ($result===0) {
				return false;
			}

			// setup array for saving area values in order to minimize database lookup
			if (!$areas[$new['ref']]) {
				$areas[ $new['ref'] ] = $db->GetOne("SELECT pharma_area FROM seg_pharma_orders WHERE refno=".$new['ref']);
				if (!$areas[$new['ref']]) {
					// no area found
					return false;
				}
			}

			if (!$result) {
				return false;
			}

			// new return has a corresponding old entry
			if (isset($old_returns[$i])) {
				$old = $old_returns[$i];

				$qty = (float) $new['qty'] - (float) $old['qty'];

				if ($qty!=0)  {
					if ($qty>0) {
						if (!$inventoryHelper->addStock( $new['code'], $areas[$new['ref']], $qty )) {
							return false;
						}
					}
					else {
						if (!$inventoryHelper->removeStock( $new['code'], $areas[$new['ref']], abs($qty) )) {
							return false;
						}
					}
				}
				else {
					// ignore, invalid quantity or the old qty and new qty are the same
				}

				// remove the old entry from the list
				unset($old_returns[$i]);
			}
			// new entry DOES not have a corresponding item from the
			// old entries
			else {
				$qty = (float) $new['qty'];
				if ($qty>0) {
					if (!$inventoryHelper->addStock( $new['code'], $areas[$new['ref']], $qty )) {
						return false;
					}
				}
				else {
					// ignore, invalid quantity
				}
			}
		}

		// process old return list
		foreach ($old_returns as $i=>$old) {
			$this->sql = "DELETE FROM seg_pharma_return_items WHERE return_nr=".$db->qstr($nr)."\n".
				"AND ref_no=".$db->qstr($old['ref'])."\n".
				"AND bestellnum=".$db->qstr($old['code']);
			if (($result=$db->Execute($this->sql)) === false) {
				return false;
			}

			// setup array for saving area values in order to minimize database lookup
			if (!$areas[$old['ref']]) {
				$areas[ $old['ref'] ] = $db->GetOne("SELECT pharma_area FROM seg_pharma_orders WHERE refno=".$old['ref']);
				if (!$areas[$old['ref']]) {
					// no area found
					return false;
				}
			}

			$qty = (float) $old['qty'];
			if ($qty>0) {
				if (!$inventoryHelper->removeStock( $old['code'], $areas[$old['ref']], $qty )) {
					return false;
				}
			}
			else {
				// ignore, invalid quantity
			}
		}
		return true;
	}


	/**
	* put your comment there...
	*
	* @param mixed $nr
	* @param mixed $itemArray
	*/
	function addReturnItems($nr, $itemArray) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "INSERT INTO seg_pharma_return_items(return_nr,ref_no,bestellnum,quantity) VALUES($nr,?,?,?)";
		if($buf=$db->Execute($this->sql,$itemArray)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}


	/**
	* put your comment there...
	*
	* @param mixed $nr
	* @return TRUE/FALSE
	*/
	function clearReturnItems($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->useReturnItems();
		$this->sql = "DELETE FROM $this->coretable WHERE return_nr=$nr";
		return $this->Transact();
	}



	/**
	* put your comment there...
	*
	* @param mixed $filters
	* @param mixed $offset
	* @param mixed $rowcount
	* @return ADODB
	*/
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
(SELECT GROUP_CONCAT(CONCAT(p.artikelname,' (',i.quantity,')') SEPARATOR '\\n') FROM seg_pharma_return_items AS i
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

