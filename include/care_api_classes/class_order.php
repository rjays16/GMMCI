<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory_helper.php');

class SegOrder extends Core {

	var $target;
	var $order_tb 		= 'seg_pharma_orders';
	var $items_tb 		= 'seg_pharma_order_items';
	var $discounts_tb = 'seg_pharma_order_discounts';
	var $prod_tb 			= "care_pharma_products_main";
	var $seg_discounts_tb = "seg_discounts";
	var $person_tb 		= "care_person";
	var $walkin_tb 		= "seg_walkin";
	var $appCov_tb      = "seg_applied_coverage";


	var $fld_pharma_order;

	function SegOrder() {
		global $db;

		$this->coretable = $this->order_tb;
		$this->setTable($this->coretable);
		$this->fld_pharma_order = $db->MetaColumnNames($this->order_tb);
		$this->setRefArray($this->fld_pharma_order);
	}

	function setTarget($target) {
	}

	function getLastNr($today) {
		global $db;
		$today = $db->qstr($today);
		//$this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->coretable WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
		$year = date('Y');
		$this->sql = "SELECT IFNULL(CAST(MAX(refno) AS UNSIGNED)+1,'".$year."000001')\n".
			"FROM seg_pharma_orders\n".
			"WHERE refno LIKE " . $db->qstr($year . '%');
		return $db->GetOne($this->sql);
	}

	function deleteOrder($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->coretable WHERE refno=$refno";
		return $this->Transact();
	}

	function clearOrderList($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "DELETE FROM $this->items_tb WHERE refno=$refno";
		return $this->Transact();
	}
        
        function updateInventory($order, $items) {                        
            $invHelper = new InventoryHelper();            
        
            $bsuccess = true;            
            foreach ($items as $v) {                                
                if ($v['serve_status'] == 'S') {                                        
                    $bsuccess = $invHelper->removeStock($v['bestellnum'], $v['quantity'], $order['pharma_area'], $order['refno'], SALE);
                    if (!$bsuccess) {
                        break;
                    }
                }
            }            
            return $bsuccess;                                    
        }       

	function addOrders($refno, $orderArray, $itemsArray) {
            global $db;
            $this->sql = "INSERT INTO $this->items_tb
                    (refno,bestellnum,quantity,pricecash,pricecharge,is_consigned,price_orig)
                     VALUES ('$refno',?,?,?,?,?,?)";
            if($buf = $db->Execute($this->sql,$orderArray)) {     
                $this->AutoServer($db->qstr($refno));
                $this->FromOR($refno);//Add autoserve for OR when request med and sup (Add by: Dommie)
                return true;
            } 
            else {
                return false;                 
            }
	}

	//Add autoserve for OR when request med and sup (Add by: Dommie)
	function FromOR($refNo) {
		global $db;
		
		$this->sql2 = "SELECT `seg_pharma_orders`.`pharma_area`
						FROM `seg_pharma_orders` 
						LEFT JOIN `seg_pharma_order_items` ON `seg_pharma_order_items`.`refno` = `seg_pharma_orders`.`refno` 
						WHERE `seg_pharma_orders`.`refno` = '".$refno."'";

                $PharmArea = $db->GetRow($this->sql2); 
						
						if($ref['pharma_area']=='OR' || $ref['pharma_area']=='MHC' || $ref['pharma_area']=='MG')
						{
							$this->sqlupdate = "UPDATE $this->items_tb 
	                                        SET serve_status = 'S',
	                                        serve_dt = NOW()
	                                        WHERE refno ='".$refno."'";
	                    			$db->Execute($this->sqlupdate);

	                    	$this->sqlupdate2 = "UPDATE $this->order_tb
	                                        SET serve_status = 'S'
	                                        WHERE refno ='".$refno."'";
	                    			$db->Execute($this->sqlupdate2);
						}
						//return true;
	}
	//added by poliam
	function AutoServer($refno, $orderArray){
	 	global $db;

		$this->sql1 = "SELECT is_cash FROM $this->order_tb WHERE refno = ".$refno; 
                $this->cash = $db->Execute($this->sql1);
                
                while ($row = $this->cash->FetchRow()) {
                    $IsCash = $row["is_cash"];
                }
                
                $ref = $db->GetRow("SELECT encounter_nr,
                                    IF(is_cash,NULL,charge_type) AS charge_type,pharma_area 
                                     FROM seg_pharma_orders\n".
                                    "WHERE refno=".$refno); //Add pharma_area field (Add by: Dommie)

                $this->sql2 = "SELECT bestellnum 
                                FROM $this->items_tb 
                                WHERE refno =".$refno;
                $this->item = $db->Execute($this->sql2); 
				
                while ($row = $this->item->FetchRow()) {
                    $ItemCode = $row["bestellnum"];
                    if ($IsCash == '0') {
                    	if($ref['charge_type'] == 'PHIC') {

                    		$total = $db->GetRow("SELECT pricecash*quantity AS total,
							serve_status 
							FROM seg_pharma_order_items\n".
							"WHERE refno=".$refno."
							AND bestellnum='".$ItemCode."'");
							             				   
                    		define('__PHIC_ID__', 18);

							$cov = $db->GetRow("SELECT coverage,
													item_code
												 FROM seg_applied_coverage\n".
									"WHERE ref_no='T{$ref['encounter_nr']}'\n".
									"AND source='M'\n".
									"AND item_code='".$ItemCode."'\n".
									"AND hcare_id=".__PHIC_ID__);

                    		if ($cov['item_code']){
                    			//remove for hospital does want auto serve
                    			$coverage = parseFloatEx($cov['coverage']) + parseFloatEx($total['total']);
                    			// $this->sqlCovUpdate ="UPDATE $this->appCov_tb
                    			// 					SET coverage=".$coverage."\n".
                    			// 					"WHERE ref_no='T{$ref['encounter_nr']}'
                    			// 					AND item_code=".$ItemCode;
                    			//$db->Execute($this->sqlCovUpdate);
                    			if($ref['pharma_area']=='OR' || $ref['pharma_area']=='MHC' || $ref['pharma_area']=='MG')//Add autoserve for OR when request med and sup (Add by: Dommie)
                    			{
	                    			$this->sqlupdate = "UPDATE $this->items_tb 
	                                        SET serve_status = 'S',
	                                        serve_dt = NOW()
	                                        WHERE refno =".$refno;
	                    			$db->Execute($this->sqlupdate);
                                                
	                    			$this->sqlupdate2 = "UPDATE $this->order_tb
	                                        SET serve_status = 'S'
	                                        WHERE refno =".$refno;
	                    			$db->Execute($this->sqlupdate2);
	                    		}
                    		}else{
                    			//remove for hospital does want auto serve
                    			// $this->sqlAppCovUpdate = "INSERT INTO seg_applied_coverage
                    			// 						(ref_no,source,item_code,hcare_id,coverage)
                    			// 						VALUE
                    			// 						('T{$ref['encounter_nr']}','M','".$ItemCode."','".__PHIC_ID__."','".$total['total']."')";
                    			//$db->Execute($this->sqlAppCovUpdate);
	                    		if($ref['pharma_area']=='OR' || $ref['pharma_area']=='MHC' || $ref['pharma_area']=='MG')//Add autoserve for OR when request med and sup (Add by: Dommie)
                    			{	
	                    			$this->sqlupdate = "UPDATE $this->items_tb 
	                                        SET serve_status = 'S',
	                                        serve_dt = NOW() 
	                                        WHERE refno =".$refno;
	                    			$db->Execute($this->sqlupdate);
	                    			$this->sqlupdate2 = "UPDATE $this->order_tb
	                                        SET serve_status = 'S'
	                                        WHERE refno =".$refno;
	                    			$db->Execute($this->sqlupdate2);
                    			}
                    	}
                    	}
                    	else if ($ref['charge_type'] == 'PERSONAL')
                    	{
                    		if($ref['pharma_area']=='OR' || $ref['pharma_area']=='MHC' || $ref['pharma_area']=='MG')//Add autoserve for OR when request med and sup (Add by: Dommie)
                    			{
                    		$this->sqlupdate = "UPDATE $this->items_tb 
                                        SET serve_status = 'S',
                                        serve_dt = NOW()
                                        WHERE refno =".$refno;
                    		$db->Execute($this->sqlupdate);
                    		$this->sqlupdate2 = "UPDATE $this->order_tb
                                        SET serve_status = 'S'
                                        WHERE refno =".$refno;
                    		$db->Execute($this->sqlupdate2);
                    	}
                    	}
                 } else {                         
                 }
        	}
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
		$this->sql="SELECT o.*,\n".
//				"IFNULL(p.name_last,w.name_last) AS name_last,".
//				"IFNULL(p.name_first,w.name_first) AS name_first,".
//				"IFNULL(p.name_middle,'') AS name_middle,\n".
			"a.area_name\n".
			"FROM $this->coretable AS o\n".
			"LEFT JOIN $this->person_tb AS p ON p.pid=o.pid\n".
			"LEFT JOIN $this->walkin_tb AS w ON w.pid=o.walkin_pid\n".
			"LEFT JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
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

	function getPersonInfoFromEncounter($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql= "
SELECT ps.nr AS personnelID, sri.rid, enc.encounter_nr, cp.senior_ID, cp.fromtemp, cp.pid,cp.name_last,cp.name_first,cp.date_birth,cp.addr_zip, cp.sex,cp.death_date,cp.status,cp.street_name,
sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,(SELECT encounter_type FROM care_encounter AS enc WHERE cp.pid=enc.pid AND enc.is_discharged=0 AND enc.encounter_status <> 'cancelled' AND enc.status NOT IN ('deleted','hidden','inactive','void') ORDER BY encounter_date DESC LIMIT 1) AS encounter_type,
enc.current_ward_nr, enc.current_room_nr, current_dept_nr, enc.is_medico,
SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discountid)),20) AS discountid,
SUBSTRING(MAX(CONCAT(scg.grant_dte,scg.discount)),20) AS discount,
scgp.discountid AS discountid_pid, scgp.discount AS discount_pid, d.parentid
FROM care_encounter AS enc
INNER JOIN care_person AS cp ON cp.pid=enc.pid
LEFT JOIN seg_radio_id AS sri ON sri.pid=cp.pid
LEFT JOIN seg_charity_grants AS scg ON scg.encounter_nr=enc.encounter_nr
LEFT JOIN seg_charity_grants_pid AS scgp ON scgp.pid=cp.pid
LEFT JOIN seg_discount AS d ON (d.discountid=scg.discountid OR d.discountid=scgp.discountid)
LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
LEFT JOIN seg_municity AS sm ON sm.mun_nr=sb.mun_nr
LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
LEFT JOIN care_personell AS ps ON cp.pid=ps.pid AND date_exit NOT IN ('0000-00-00', DATE(NOW())) AND contract_end NOT IN ('0000-00-00', DATE(NOW()))
WHERE enc.encounter_nr=$nr AND cp.status NOT IN ('deleted','hidden','inactive','void') AND (death_date in (null,'0000-00-00',''))
GROUP BY cp.pid,scg.encounter_nr
ORDER BY name_last ASC";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result->FetchRow();
		} else { return false; }
	}

	function getERRequest($encounter_nr) {
		global $db;
		if ($encounter_nr)
			$encounter_nr = $db->qstr($encounter_nr);
		$this->sql = "SELECT refno FROM seg_pharma_orders WHERE encounter_nr=$encounter_nr AND pharma_area='ER' ORDER BY orderdate DESC";
		return $this->result=$db->GetOne($this->sql);
	}

	function getRecentWardRefInDateRange($frm,$to,$encounter_nr='') {
		global $db;
		if ($encounter_nr)
			$encounter_nr = $db->qstr($encounter_nr);
		$frm = date("Y-m-d H:i:s",$frm);
		$to = date("Y-m-d H:i:s",$to);
		$this->sql = "SELECT refno FROM seg_pharma_orders WHERE orderdate>='$frm' AND orderdate<='$to' ".($encounter_nr ? "AND pharma_area='WD' AND encounter_nr=$encounter_nr " : '')."ORDER BY orderdate DESC,refno DESC";
		return $this->result=$db->GetOne($this->sql);
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

	function getOrderItemsFullInfo($refno, $discountID) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "SELECT o.quantity,o.pricecash AS `force_price`,o.is_consigned,a.*,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
				"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n".
				"IFNULL(a.price_charge,0) AS chrgrpriceppk,\n".
				"IF(a.is_socialized,\n".
					"IFNULL((SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),a.price_cash),\n".
					"a.price_cash) AS dprice,\n".
				"IFNULL(a.price_cash,0) AS cshrpriceppk,\n".
				"o.serve_status,o.serve_remarks,o.request_flag\n".
				"FROM seg_pharma_order_items AS o\n".
				"LEFT JOIN care_pharma_products_main AS a ON o.bestellnum=a.bestellnum\n".
				"WHERE o.refno = $refno";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getOrderItemsForServe($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql = "SELECT a.bestellnum,a.artikelname,\n".
			"o.request_flag,oo.charge_type,\n".
			"o.quantity,o.is_consigned,\n".
			"o.pricecash, o.pricecharge,\n".
			"o.serve_status,o.serve_remarks,\n".
			"o.is_unused, oo.pharma_area\n".
			"FROM seg_pharma_order_items AS o\n".
			"INNER JOIN seg_pharma_orders AS oo ON oo.refno=o.refno\n".
			"LEFT JOIN care_pharma_products_main AS a ON o.bestellnum=a.bestellnum\n".
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
				return true;
		} else { return false; }
	}

//	function getActiveOrdersEx($filters, $offset=0, $rowcount=15) {
//		global $db;
//		#if (is_numeric($now)) $dDate = date("Ymd",$now);
//		#$where = array();
//		#if ($dDate) $where[] = "o.orderdate=$dDate";
//		#else $dDate = $db->qstr($dDate);
//		if (!$offset) $offset = 0;
//		if (!$rowcount) $rowcount = 15;
//
//		$phFilters = array();
//		if (is_array($filters)) {
//		foreach ($filters as $i=>$v) {
//			switch (strtolower($i)) {
//				case 'datetoday':
//					$phFilters[] = 'DATE(orderdate)=DATE(NOW())';
//				break;
//				case 'datethisweek':
//					$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND WEEK(orderdate)=WEEK(NOW())';
//				break;
//				break;
//				case 'datethismonth':
//					$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND MONTH(orderdate)=MONTH(NOW())';
//				break;
//				case 'date':
//					$phFilters[] = "DATE(orderdate)=".$db->qstr($v);
//				break;
//				case 'datebetween':
//					$phFilters[] = "orderdate BETWEEN ".$db->qstr($v[0])." AND ".$db->qstr($v[1]);
//				break;
//				case 'name':
//					if (strpos($v,',')!==false) {
//						$split_name = explode(',', $v);
//						$phFilters[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0]).'%'). " OR w.name_last LIKE ".$db->qstr(trim($split_name[0]).'%');
//						$phFilters[] = "cp.name_first LIKE ".$db->qstr(trim($split_name[1]).'%'). " OR w.name_first LIKE ".$db->qstr(trim($split_name[1]).'%');
//					}
//					else {
//						if ($v) {
//							$phFilters[] = "cp.name_last LIKE ".$db->qstr(trim($v).'%'). " OR w.name_last LIKE ".$db->qstr(trim($v).'%');
//						}
//					}
//				break;
//				case 'pid':
//					$phFilters[] = "o.pid=".$db->qstr($v);
//				break;
//				case 'patient':
//					$phFilters[] = "o.pid=".$db->qstr($v)." OR o.walkin_pid=".$db->qstr($v);
//				break;
//				case 'inpatient':
//					$phFilters[] = "o.encounter_nr=".$db->qstr($v);
//				break;
//				case 'walkin':
//					$phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
//				break;
//				case 'area':
//					$phFilters[] = 'pharma_area='.$db->qstr($v);
//				break;
//			}
//		}}
//
//		if (!$phFilters) {
//			$phFilters[] = 'orderdate >= NOW()-INTERVAL 1 MONTH';
//		}
//		$phWhere=implode(") AND (",$phFilters);
//		if ($phWhere) $phWhere = "($phWhere)";
//		$this->sql="SELECT SQL_CALC_FOUND_ROWS\n".
//					"o.orderdate,o.refno,o.pid,fn_get_person_name(IFNULL(o.pid,CONCAT('W',o.walkin_pid))) name,\n".
//					"o.is_cash,o.charge_type,a.area_name AS `area_full`,\n".
//					"(SELECT GROUP_CONCAT(CONCAT(IFNULL(oi.request_flag,''),'\\t',IFNULL(oi.serve_status,'N'),'\\t',prod.artikelname) SEPARATOR '\\n')\n".
//						"FROM seg_pharma_order_items AS oi\n".
//						"INNER JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
//						"WHERE o.refno = oi.refno) AS `items`\n".
//				"FROM $this->coretable AS o\n".
//				"LEFT JOIN care_person AS cp ON o.pid=cp.pid\n".
//				"LEFT JOIN seg_walkin AS w ON o.walkin_pid=w.pid\n".
//				"INNER JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
//				($phWhere ? "WHERE ($phWhere)\n" : "").
//				"ORDER BY orderdate DESC,is_urgent DESC,refno ASC\n".
//				"LIMIT $offset, $rowcount";
//
//		//return mysql_query($this->sql,$db->_connectionID);
//		if($this->result=$db->Execute($this->sql)) {
//			return $this->result;
//		}
//		else {
//			return false;
//		}
//	}



	function getActiveOrders($filters, $offset=0, $rowcount=15) {
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$phFilters = array();
		$personFilters = array();
		$walkinFilters = array();
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'datetoday':
						$phFilters[] = 'DATE(orderdate)=DATE(NOW())';
					break;
					case 'datethisweek':
						$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND WEEK(orderdate)=WEEK(NOW())';
					break;
					break;
					case 'datethismonth':
						$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND MONTH(orderdate)=MONTH(NOW())';
					break;
					case 'date':
						$phFilters[] = "DATE(orderdate)=".$db->qstr($v);
					break;
					case 'datebetween':
						$phFilters[] = "orderdate BETWEEN ".$db->qstr($v[0])." AND ".$db->qstr($v[1]);
					break;
					case 'name':
						if (strpos($v,',')!==false) {
							$split_name = explode(',', $v);
							$personFilters[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0]).'%');
							$personFilters[] = "cp.name_first LIKE ".$db->qstr(trim($split_name[1]).'%');

							$walkinFilters[] = "w.name_last LIKE ".$db->qstr(trim($split_name[0]).'%');
							$walkinFilters[] = "w.name_first LIKE ".$db->qstr(trim($split_name[1]).'%');
						}
						else {
							if ($v) {
								$personFilters[] = "cp.name_last LIKE ".$db->qstr(trim($v).'%');
								$walkinFilters[] = "w.name_last LIKE ".$db->qstr(trim($v).'%');
							}
						}
					break;
					case 'pid':
					case 'patient':
						$phFilters[] = "o.pid=".$db->qstr($v);
					break;
					case 'inpatient':
						$phFilters[] = "o.encounter_nr=".$db->qstr($v);
					break;
					case 'walkin':
						$phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
					break;
					case 'area':
						$phFilters[] = 'pharma_area='.$db->qstr($v);
					break;
				}
			}
		}

		if (!$phFilters) {
			$phFilters[] = 'orderdate >= NOW()-INTERVAL 1 MONTH';
		}

		$query = "SELECT {calcRows}\n".
				"o.orderdate,o.refno,o.pid, {nameQuery},\n".
				"o.is_cash,o.charge_type,a.area_name AS `area_full`,o.is_urgent,\n".
				"(SELECT GROUP_CONCAT(CONCAT(IFNULL(oi.request_flag,''),'\\t',IFNULL(oi.serve_status,'N'),'\\t',prod.artikelname) SEPARATOR '\\n')\n".
					"FROM seg_pharma_order_items AS oi\n".
					"INNER JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
					"WHERE o.refno = oi.refno) AS `items`\n".
			"FROM $this->coretable AS o\n".
			"{personJoin}\n".
			"LEFT JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
			"{where}\n";

		// main query
		$queries = array();

		if ($personFilters) {
			$personWhere = array_merge($phFilters, $personFilters);
			$personWhere = implode(") AND (",$personFilters);
			if ($personWhere) $personWhere = "($personWhere)";
			$queries[] = strtr($query, array(
				'{calcRows}' => 'SQL_CALC_FOUND_ROWS',
				'{nameQuery}' => "fn_get_person_name(o.pid) `name`",
				'{personJoin}' => "LEFT JOIN care_person AS cp ON o.pid=cp.pid",
				'{where}' => ($personWhere ? "WHERE ({$personWhere})" : "")
			));
		}

		if ($walkinFilters) {
			$walkinFilters = array_merge($phFilters, $walkinFilters);
			$walkinWhere = implode(") AND (",$walkinFilters);
			if ($walkinWhere) $walkinWhere = "($walkinWhere)";
			$queries[] = strtr($query, array(
				'{calcRows}' => empty($queries) ? 'SQL_CALC_FOUND_ROWS' : '',
				'{nameQuery}' => "fn_get_walkin_name(o.walkin_pid) `name`",
				'{personJoin}' => "LEFT JOIN seg_walkin AS w ON o.walkin_pid=w.pid",
				'{where}' => ($walkinWhere ? "WHERE ({$walkinWhere})" : "")
			));
		}

		if (empty($queries)) {
			$phWhere = implode(") AND (",$phFilters);
			if ($phWhere) $phWhere = "($phWhere)";
			$queries[] = strtr($query, array(
				'{calcRows}' => 'SQL_CALC_FOUND_ROWS',
				'{nameQuery}' => "fn_get_person_name(IFNULL(o.pid,CONCAT('W',o.walkin_pid))) `name`",
				'{personJoin}' => "",
				'{where}' => ($phWhere ? "WHERE ({$phWhere})" : "")
			));
		}


		$this->sql = implode($queries, "UNION ALL\n") .
			"ORDER BY orderdate DESC,is_urgent DESC,refno ASC\n".
			"LIMIT $offset, $rowcount";


		//return mysql_query($this->sql,$db->_connectionID);
		if(($this->result=$db->Execute($this->sql)) !== false) {
			return $this->result;
		}
		else {
			return false;
		}
	}



	#edited by VAN 12-22-08
	#function getServeReadyOrders($filters, $offset=0, $rowcount=15) {
	function getServeReadyOrders($filters, $offset=0, $rowcount=15, $isreturned=0) {
		global $db;

		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 15;

		$phFilters = array("o.orderdate > NOW()-INTERVAL 2 MONTH");
		$phFields = array();
		//$phHaving = array("(is_cash AND paid) OR (NOT is_cash)");

		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'withtotals':
						$phFields[] = '(SELECT SUM(oi.pricecash*oi.quantity) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno) AS amount_due';
					break;
					case 'withservecount':
						$phFields[] = "(SELECT COUNT(*) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno) AS `count_total_items`";
						$phFields[] = "(SELECT COUNT(*) FROM seg_pharma_order_items AS oi WHERE o.refno = oi.refno AND oi.serve_status='S') AS `count_served_items`";
					break;
					case 'area':
						if (strtoupper($v)!='ALL')
							$phFilters[] = 'pharma_area='.$db->qstr($v);
					break;
					case 'refno':
						$phFilters[] = "o.refno=".$db->qstr($v);
					break;
					case 'refno+name':
						//$phFilters[] = "ordername REGEXP '[[:<:]]".substr($db->qstr($v),1);
						if (strpos($v, ',') !== FALSE) {
							$split_name = explode(',', $v);
							$lname = trim($split_name[0]);
							$fname = trim($split_name[1]);
							$phFilters[] = "p.name_last LIKE ".$db->qstr($lname.'%');
							$phFilters[] = "p.name_first LIKE ".$db->qstr($fname.'%');
						}
						else {
							$phFilters[] = "p.name_last LIKE ".$db->qstr($v.'%');
						}
					break;
	//				case 'nopay':
	//					$phFilters[] = "pay.or_no IS NULL";
	//				break;
					case 'nopay':
						$phFilters[] = "is_cash=0";
						break;
					case 'daysago':
						$wFilters[] = "DATEDIFF(NOW(),orderdate)<=".$db->qstr($v);
						break;
					case 'datetoday':
						$phFilters[] = 'DATE(orderdate)=DATE(NOW())';
						break;
					case 'datethisweek':
						$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND WEEK(orderdate)=WEEK(NOW())';
						break;
					case 'datethismonth':
						$phFilters[] = 'YEAR(orderdate)=YEAR(NOW()) AND MONTH(orderdate)=MONTH(NOW())';
						break;
					case 'date':
						$phFilters[] = "DATE(orderdate)=".$db->qstr($v);
						break;
					case 'datebetween':
						$phFilters[] = "orderdate BETWEEN ".$db->qstr($v[0])." AND ".$db->qstr($v[1]);
						break;
					case 'name':
						if (strpos($v,',')!==false) {
							$split_name = explode(',', $v);
							$phFilters[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0]).'%'). " OR w.name_last LIKE ".$db->qstr(trim($split_name[0]).'%');
							$phFilters[] = "cp.name_first LIKE ".$db->qstr(trim($split_name[1]).'%'). " OR w.name_first LIKE ".$db->qstr(trim($split_name[1]).'%');
						}
						else {
							if ($v) {
								$phFilters[] = "cp.name_last LIKE ".$db->qstr(trim($v).'%'). " OR w.name_last LIKE ".$db->qstr(trim($v).'%');
							}
						}
						break;
					case 'pid':
						$phFilters[] = "o.pid REGEXP ".$db->qstr($v);
						break;
					case 'patient':
						$phFilters[] = "o.pid=".$db->qstr($v);
						break;
					case 'inpatient':
						$phFilters[] = "o.encounter_nr=".$db->qstr($v);
						break;
					case 'walkin':
						$phFilters[] = "ordername=".$db->qstr($v)." AND (ISNULL(pid) OR LENGTH(pid)=0) AND (ISNULL(encounter_nr) OR LENGTH(encounter_nr)=0)";
						break;
					case 'serve':
						switch (strtolower($v)) {
							case 's':
								$phHaving[] = "count_total_items=count_served_items";
								break;
							case 'p':
								$phHaving[] = "(count_served_items<count_total_items) AND (count_served_items>0)";
								break;
							case 'n':
								$phHaving[] = "count_served_items=0";
								break;
						}
						break;
				}
			}
		}

		$withDateFilters = strpos(strtoupper(implode("_",array_keys($filters))),"DATE") !== FALSE;
		if (!$withDateFilters) {
			// if no date is specified, fetch only requests that are at most 1 month old
			$phFilters[] = "DATE(orderdate)>(DATE(NOW())-INTERVAL 2 DAY)";
		}

		$phWhere=implode(") AND (",$phFilters);
		if ($phWhere) $phWhere = "($phWhere)";
		else $phWhere = "1";
		$fields=implode(",\n",$phFields);
		if ($fields) $fields .= ',';

		$havingClause = implode(") AND (",$filters);
		if ($havingClause) $havingClause = "HAVING ($havingClause)";

		#added by VAN 12-22-08 temporary .. pls change it :)
		if ($isreturned){
			$sql_pay = " LEFT JOIN seg_pay_request AS pay ON pay.ref_no=o.refno AND am.ref_source='PH'\n";
		}

		$this->sql="SELECT SQL_CALC_FOUND_ROWS\n".
			"o.orderdate,o.refno,o.pid,fn_get_person_name(IFNULL(o.pid,CONCAT('W',o.walkin_pid))) name,\n".
			"o.is_cash,o.charge_type,\n".
			"a.area_name AS `area_full`,IFNULL(am.amount,-1) AS ss_amount,\n".
			$fields.
			"(SELECT GROUP_CONCAT(CONCAT(IFNULL(LCASE(oi.request_flag),''),'\\t',IFNULL(oi.serve_status,'N'),'\\t',prod.artikelname) SEPARATOR '\\n')\n".
				"FROM seg_pharma_order_items AS oi\n".
				"LEFT JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
				"WHERE o.refno = oi.refno) AS `items`,\n".
			"EXISTS(SELECT NULL FROM seg_pharma_order_items AS oi WHERE oi.refno=o.refno AND oi.request_flag IS NOT NULL) AS `paid`\n".
			#"EXISTS(SELECT NULL FROM seg_pharma_order_items AS i WHERE i.refno=o.refno AND i.request_flag='LINGAP') AS `lingap`,\n".
			#"EXISTS(SELECT NULL FROM seg_pharma_order_items AS i WHERE i.refno=o.refno AND i.request_flag='CMAP') AS `cmap`\n".
			"FROM $this->coretable o\n".
			"LEFT JOIN care_person cp ON cp.pid=o.pid\n".
			"LEFT JOIN seg_walkin AS w ON o.walkin_pid=w.pid\n".
			"LEFT JOIN seg_pharma_areas a ON a.area_code=o.pharma_area\n".
			"LEFT JOIN seg_charity_amount am ON am.ref_no=o.refno AND am.ref_source='PH'\n".
#				$sql_pay. #added by VAN 12-22-08*/
#				"LEFT JOIN seg_pay_request AS pr ON pr.ref_no=o.refno AND pr.ref_source='PH'\n".
#				"LEFT JOIN seg_pay AS pay ON (pay.or_no=pr.or_no AND pay.cancel_date IS NULL)\n".
			"WHERE\n".
				"($phWhere)\n";
		if ($phHaving) $this->sql .= "HAVING (" . implode(") AND (",$phHaving) . ")\n";
		$this->sql .= "ORDER BY orderdate DESC,is_urgent DESC,refno ASC\n" .
			"LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function changeServeStatus($refno, $itemsArray, $statusArray, $remarksArray, $usedStatusArray, $usedQtyArray) {
		global $db;

		if (!$itemsArray || !$statusArray) return FALSE;
		if (!is_array($itemsArray)) $itemsArray = array($itemsArray);
		if (!is_array($statusArray)) $statusArray = array($statusArray);
		if (!is_array($remarksArray)) $remarksArray = array($remarksArray);
		if (!is_array($usedStatusArray)) $usedStatusArray = array($usedStatusArray);
		if (!is_array($usedQtyArray)) $usedQtyArray = array($usedQtyArray);
		
		$ref = $db->GetRow("SELECT encounter_nr,
								IF(is_cash,NULL,charge_type) AS charge_type
								 FROM seg_pharma_orders\n".
									"WHERE refno=".$db->qstr($refno));

		foreach ($itemsArray as $i=>$item) {
			$dbOk = TRUE;

			$new_serve_status = $statusArray[$i];
			$data = array(
				"refno"=>$db->qstr($refno),
				"bestellnum"=>$db->qstr($item),
				"serve_remarks"=>$db->qstr($remarksArray[$i]),
				"is_unused" => $db->qstr($usedStatusArray[$i]),
				"unused_qty" => $db->qstr($usedQtyArray[$i])
			);


			# Update serve date for the item
			if (strtoupper($statusArray[$i])=='S') {
				$data["serve_dt"]="NOW()";
			} else {
				$data["serve_dt"]="NULL";
			}

			# Get request item details
			$this->sql = "SELECT pricecash*quantity AS total,
							serve_status 
							FROM seg_pharma_order_items\n".
							"WHERE refno=".$db->qstr($refno)." 
							AND bestellnum=".$db->qstr($item);
			$item_details = $db->GetRow($this->sql);
			if (!$item_details) {
				$this->error_msg = 'Unable to retrieve request item details...';
				return FALSE;
			}


			$old_serve_status = $item_details['serve_status'];
			if ($old_serve_status != $new_serve_status) {
				$data['serve_status'] = $db->qstr($new_serve_status);

				# Handle applied coverage for PHIC and other benefits

				if ($ref['charge_type'] == 'PHIC') {

					if ($old_serve_status == 'N' && $new_serve_status=='S') {

						// Hardcode hcare ID (temporary workaround)
						define('__PHIC_ID__', 18);

						$this->sql = "SELECT coverage FROM seg_applied_coverage\n".
							"WHERE ref_no='T{$ref['encounter_nr']}'\n".
								"AND source='M'\n".
								"AND item_code=".$db->qstr($item)."\n".
								"AND hcare_id=".__PHIC_ID__;

						$coverage = parseFloatEx($db->GetOne($this->sql)) + parseFloatEx($item_details['total']);
						$result = $db->Replace('seg_applied_coverage',
							array(
								'ref_no'=>"T{$ref['encounter_nr']}",
								'source'=>'M',
								'item_code'=>$item,
								'hcare_id'=>__PHIC_ID__,
								'coverage'=>$coverage
							),
							array('ref_no', 'source', 'item_code', 'hcare_id'),
							$autoquote=TRUE
						);

						if ($result) $dbOk = TRUE;
						else {
							$this->error_msg = "Unable to update applied coverage for item #{$item}...";
							$dbOk = FALSE;
						}
					}
					elseif ($old_serve_status == 'S' && $data['serve_status']=='N') {

						// Possible but leads to some complications
						// Handle later

						$this->error_msg = "Cannot unserve item #{$item} due to PHIC coverage...";
						$dbOk = FALSE;
					}
                      
				}
			}

			if ($dbOk) {
				$ok = $db->Replace("seg_pharma_order_items",
					$data,
					array("refno","bestellnum"),
					$autoquote = FALSE);

				$dbOk = ($ok>0);
				if (!$dbOk) {
					$this->error_msg = "Unable to update serve status for item #{$item}...";
				}
			}
			if (!$dbOk) return FALSE;
		}
		return TRUE;
	}

	//added by ken /5/28/2014 for FIS integration
	#modified by ken 10/16/2014 checking it is cash or charge trans.,
	function checkIfCashTrans($refno){
		global $db;

		// $is_cash = $db->GetOne("SELECT is_cash
		// 							FROM seg_pharma_orders
		// 							WHERE refno = ".$db->qstr($refno));

		$sql = "SELECT is_cash
				FROM seg_pharma_orders
				WHERE refno = ".$db->qstr($refno);

		$result = $db->Execute($sql);
		$row = $result->FetchRow();

		// if($is_cash)
		// 	return $is_cash['is_cash'];
		// else
		// 	return false;

		return $row['is_cash'];
	}

	function getPharmaArea($area, $fields='*') {
		global $db;
		$area = $db->qstr($area);
		$this->sql = "SELECT $fields FROM seg_pharma_areas WHERE area_code=$area";
		if($this->result=$db->GetRow($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	//added by CHA 09-23-09
	function checkOrderItemPaid($refno,$bestellnum)
	{
		global $db;
		$sql1="select r.or_no from seg_pay_request as r join seg_pay as p where r.ref_no=".$db->qstr($refno)." and r.service_code=".$db->qstr($bestellnum).
						" and r.or_no=p.or_no and p.cancelled_by='' and r.ref_source='PH'";
		$sql2="select d.entry_id from seg_lingap_entry_details as d join seg_lingap_entries as h ".
						"where d.entry_id=h.entry_id and d.ref_no=".$db->qstr($refno)." and d.service_code=".$db->qstr($bestellnum)." and h.is_deleted=0";
	 // echo "<br>sql1:".$sql1;
		//echo "<br>sql2:".$sql2;
		$result1 = $db->Execute($sql1);
		$row1 = $result1->FetchRow();
		$result2 = $db->Execute($sql2);
		$row2 = $result2->FetchRow();
	 // echo  "<br>or_no: ".$row1['or_no'];
		if($row1['or_no']!="" && $row2['entry_id']!="") return true;
		if($row1['or_no']!="" && $row2['entry_id']=="") return true;
		if($row1['or_no']=="" && $row2['entry_id']!="") return true;
		return false;
	}
	//end CHA


	#created by cha, may 31,2010
	function getWalkinIssuance($pid='', $area='', $product_code='', $from_dt='', $to_dt='')
	{
		global $db;
		$where = " WHERE ";

		if($product_code)
			$where.=" i.bestellnum=".$db->qstr($product_code)." AND\n";
		if($area)
			$where.=" p.pharma_area=".$db->qstr($area)."AND\n";
		if($from_dt || $to_dt)
			$where.=" (i.serve_dt BETWEEN ".$db->qstr($from_dt)." AND ".$db->qstr($to_dt).") AND\n";
		if($pid)
			 $where.=" w.pid=".$db->qstr($pid)." AND\n";

		$this->sql=
			"SELECT i.serve_dt as `date`, CONCAT(w.name_last, ', ', w.name_first) AS `name`,\n".
				"p.pharma_area AS`area`, i.quantity, m.artikelname\n".
			"FROM seg_pharma_order_items AS i\n".
				"INNER JOIN seg_pharma_orders AS p ON p.refno=i.refno\n".
				"INNER JOIN seg_walkin AS w ON p.walkin_pid=w.pid\n".
				"INNER JOIN care_pharma_products_main AS m ON i.bestellnum=m.bestellnum\n".
			$where." i.serve_status IN ('S','N')\n".
			"ORDER BY `name`, i.serve_dt ASC";
			//die("<pre>".$this->sql."</pre>");
		if( ($this->result=$db->Execute($this->sql)) !== false )
		{
			return $this->result;
		}
		else
		{
			return false;
		}
	}
	#end cha

#added by daryl
		function getpatientinfo($pids, $encounter_nr) {
		global $db;
		$pid = $db->qstr($pids);
		$encounter = $db->qstr($encounter_nr);
		$this->sql="SELECT ce.`current_room_nr` as room,\n".
			"p.`age` AS age,\n".
			"p.`sex` AS sex,\n".
			"p.`civil_status` AS civil\n".
			"FROM $this->person_tb AS p\n".
			"LEFT JOIN care_encounter AS ce ON p.pid=ce.pid\n".
			"WHERE p.pid=$pid AND ce.encounter_nr=$encounter";
			// return $this->sql;
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}


	function get_create_person($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT cu.name\n".
			"FROM seg_pharma_orders AS spo\n".
			"INNER JOIN care_users AS cu ON spo.create_id = cu.login_id\n".
			"WHERE spo.refno = $refno\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}


	function get_modify_person($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT cu.name\n".
			"FROM seg_pharma_orders AS spo\n".
			"INNER JOIN care_users AS cu ON spo.modify_id = cu.login_id\n".
			"WHERE spo.refno = $refno\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getinsurance($pids) {
		global $db;
		$pid = $db->qstr($pids);
		$this->sql="SELECT c.`firm_id`,c.`hcare_id` \n".
			"FROM  care_encounter a \n".
			"INNER JOIN seg_encounter_insurance b ON a.`encounter_nr` =  b.`encounter_nr`\n".
			"INNER JOIN care_insurance_firm c ON b.`hcare_id` = c.`hcare_id`\n".
			"WHERE a.`pid` =  $pid  AND a.`is_discharged` <> 1\n";	
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	/**
     * get pharma area inventory module
     * @param $refno
     * @return mixed
     */
    function getTransactionArea($refno) {
        global $db;
        $this->sql = $db->Prepare("SELECT pharma_area FROM seg_pharma_orders WHERE refno=?");
        return $db->GetOne($this->sql, $refno);
    }

    /**
     * get is cash inventory module
     * @param $refno
     * @return bool
     */
    function isCash($refno) {
        global $db;
        $this->sql = "SELECT is_cash FROM seg_pharma_orders WHERE refno=".$db->qstr($refno);
        $result = $db->Execute($this->sql);
        return $result==='1';
    }

}

