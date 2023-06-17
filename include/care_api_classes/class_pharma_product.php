<?php
require_once($root_path.'include/care_api_classes/class_core.php');

class SegPharmaProduct extends Core {
	/**#@+
	* @access private
	* @var string
	*/

	/**
	* Tables
	*/
	var $tb_pmain	 			= 'care_pharma_products_main';
	var $tb_class 			= 'seg_product_classification';
	var $tb_prod_class 	= 'seg_pharma_products_classification';
	var $tb_areas 	 		= 'seg_areas';
	var $tb_avail 	 		= 'seg_pharma_products_availability';
	var $tb_types       = 'seg_type_product';                   // Added by LST 12.31.2008
	var $tb_units       = 'seg_unit';             						 //added by Bryan 03-25-09
	var $tb_extended    = 'seg_item_extended'; 							    //added by Bryan 03-25-09
	var $max_digits		= 8;
	/**
	* Field names of care_pharma_products_main or care_med_products_main tables
	* @var array
		* Field name is_compensable added by bryan 102909
	*/
	var $fld_prodmain;
	var $fld_type;

	/**
	* Constructor
	*/
	function SegPharmaProduct() {
		global $db;
		$this->coretable = $this->tb_pmain;
		$this->fld_prodmain = $db->MetaColumnNames($this->tb_pmain);
		$this->fld_type = $db->MetaColumnNames($this->tb_types);
		$this->setRefArray($this->fld_prodmain);

	}

	function createNR() {
		global $db;
		$this->sql = "SELECT MAX(CONVERT(bestellnum,UNSIGNED))+1 FROM care_pharma_products_main";
		if($buf=$db->Execute($this->sql)) {
			if($buf) {
				$row = $buf->FetchRow();
				return $row[0];
			} else { return false; }
		} else { return false; }
	}

	function getCode($name, $type, $unit) {
		global $db;
		$this->sql = "SELECT fn_get_product_code( \n".
			$db->qstr($name).",\n".
			"(SELECT type_code FROM seg_type_product WHERE nr=".$db->qstr($type)."),\n".
			"(SELECT unit_code FROM seg_unit WHERE unit_id=".$db->qstr($unit).")\n".
		")";
		return $db->GetOne($this->sql);
	}

	function deleteProduct($id, $flag=1) {
		global $db;
		$this->sql = "UPDATE care_pharma_products_main SET is_deleted=".$db->qstr($flag)." WHERE bestellnum=".$db->qstr($id);
		if(($this->result=$db->Execute($this->sql))!==FALSE) {
			return true;
		} else { return false; }
	}

	function searchProducts($codename, $generic, $classification, $prodclass, $offset=0, $rowcount=10, $sort_order='artikelname') {
		global $db;
		if (!$offset) $offset = 0;
		if (!$rowcount) $rowcount = 10;
		$this->sql =
				"SELECT SQL_CALC_FOUND_ROWS p.*,
				IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='SC' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS sc_price,
				IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='C1' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS c1_price,
				IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='C2' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS c2_price,
				IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='C3' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS c3_price
				".
				/*
					(SELECT GROUP_CONCAT(DISTINCT aa.area_name SEPARATOR ', ')
						FROM seg_pharma_products_availability AS a
						LEFT JOIN seg_pharma_areas AS aa ON aa.area_code = a.area_code
						WHERE a.bestellnum=p.bestellnum) AS availability,
					(SELECT GROUP_CONCAT(DISTINCT cc.class_name SEPARATOR ', ')
						FROM seg_pharma_products_classification AS c
						LEFT JOIN seg_product_classification AS cc ON cc.class_code = c.class_code
						WHERE c.bestellnum=p.bestellnum) AS classification
				*/
				"FROM care_pharma_products_main AS p
				";
		$where = array("p.is_deleted!=1");
		if ($codename) {
			#$codename = $db->qstr($codename);
			$where[] = "p.bestellnum='$codename' OR p.artikelname REGEXP '[[:<:]]$codename'";
		}

		if ($generic) {
			# $generic = $db->qstr($generic);
			$where[] = "p.generic REGEXP '[[:<:]]$generic'";
		}
		if ($classification) {
			# $classification = $db->qstr($classification);
			$where[] = "EXISTS(SELECT * FROM seg_pharma_products_classification WHERE bestellnum=p.bestellnum AND class_code='$classification')";
		}
		if ($prodclass) {
			$prodclass = $db->qstr($prodclass);
			$where[] = "p.prod_class=$prodclass";
		}

		if ($where)
			$this->sql.= " WHERE (" .  implode(") AND (", $where) . ")\n";

		$this->sql .=	"ORDER BY $sort_order LIMIT $offset, $rowcount";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}
	
	function search( $filters ) {
		global $db;
		if (!$offset) $offset=0;
		if (!$rowcount) $rowcount=10;
		if (!$sort_order) $sort_order='artikelname';

		$show_deleted = "exclude";
		$where = array();
		foreach ( $filters as $filter=>$value ) {
			switch (strtolower($filter)) {
				case 'codename':
					$where[] = "p.bestellnum=".$db->qstr($value)." OR p.artikelname REGEXP ".$db->qstr("[[:<:]]$value");
					break;
				case 'generic':
					//modified by cha, october 14, 2010
					//$where[] = "p.generic REGEXP ".$db->qstr("[[:<:]]$generic");
					$where[] = "p.generic REGEXP ".$db->qstr("[[:<:]]$value");
					break;
				case 'classification':
				//modified by cha, october 14, 2010
					//$where[] = "EXISTS(SELECT 1 FROM seg_pharma_products_classification WHERE bestellnum=p.bestellnum AND class_code=".$db->qstr($classification).")";
					$where[] = "EXISTS(SELECT 1 FROM seg_pharma_products_classification WHERE bestellnum=p.bestellnum AND class_code=".$db->qstr($value).")";
					break;
				case 'showdeleted':
					$show_deleted = $value;
					break;
				case 'prodclass':
					//modified by cha, october 14, 2010
					//$where[] = "ptype.prod_class=".$db->qstr($prodclass);
					$where[] = "p.prod_class=".$db->qstr($value);
					break;
				case 'offset':
					$offset = $value;
					break;
				case 'rowcount':
					$rowcount = $value;
					break;
				case 'sort':
					$sort_order = $value;
					break;
			}
		}

		if (strtolower($show_deleted)=='all') {
		}
		elseif (strtolower($show_deleted)=='show') {
			$where[] = "p.is_deleted";
		}
		else {
			$where[] = "NOT p.is_deleted";
		}

		$this->sql =
			"SELECT SQL_CALC_FOUND_ROWS p.*,\n".
				"IFNULL((SELECT d.price FROM seg_service_discounts AS d WHERE d.discountid='SC' AND d.service_code=p.bestellnum AND d.service_area='PH'),NULL) AS sc_price,\n".
			"sv.stock\n".
			"FROM care_pharma_products_main AS p\n".
			"LEFT JOIN seg_type_product AS ptype ON ptype.nr=p.type_nr\n".
			"LEFT JOIN (SELECT st.item_code,SUM(st.qty) AS stock FROM\n".
				"(SELECT eod.item_code,eod.area_code,eod.eod_date,SUBSTRING(MAX(CONCAT(eod.eod_date,eod.eod_qty)),11) AS qty\n".
					"FROM seg_eod_inventory AS eod\n".
					"GROUP BY eod.item_code,eod.area_code) AS st\n".
				"GROUP by item_code) AS sv ON sv.item_code=p.bestellnum\n";

		if ($where)
			$this->sql.= " WHERE (" .  implode(")\n AND (", $where) . ")\n";
		$this->sql .=  "ORDER BY $sort_order\n";
		if (!$filters["no_limit"]) {
			$this->sql .=  "LIMIT $offset, $rowcount\n";
		}else{
			$this->sql .=  "";
		}
		// die($this->sql);
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	// Added by EJ 07/23/2014
	function getHospStock($item_code) {
		global $db;
		$item_code = $db->qstr($item_code);
		$this->sql = "SELECT SUM(mvmnt_qty * packqty) AS stock
					  FROM seg_sku_catalog sku INNER JOIN seg_inventory_ledger led
					  ON sku.sku_id = led.sku_id
					  WHERE sku.item_code = $item_code";

		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getProductInfo($nr) {
		global $db;

		$this->sql = $db->Prepare("SELECT *,\n".
			"(SELECT GROUP_CONCAT(DISTINCT class_code SEPARATOR ',') FROM seg_pharma_products_classification AS c WHERE c.bestellnum=p.bestellnum) AS classification,\n".
			"(SELECT GROUP_CONCAT(DISTINCT area_code SEPARATOR ',') FROM seg_pharma_products_availability AS av WHERE av.bestellnum = p.bestellnum) AS availability\n".
			"FROM care_pharma_products_main AS p WHERE bestellnum= ? ");
		
		if($buf=$db->Execute($this->sql,$nr)) {
			if($buf->RecordCount()) {
				$row = $buf->FetchRow();
				return $row;
			} else { return false; }
		} else { return false; }
	}

		function getExtendedProductInfo($item_no) {
				global $db;

				$this->sql = $db->Prepare("SELECT * FROM seg_item_extended WHERE item_code= ? ");

				if($buf=$db->Execute($this->sql,array($item_no))) {
						if($buf->RecordCount()) {
								$row = $buf->FetchRow();
								return $row;
						} else { return false; }
				} else { return false; }
		}

	function clearProductClassification($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM $this->tb_prod_class WHERE bestellnum=$nr";
		return $this->Transact();
	}

	function clearProductAvailability($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM $this->tb_avail WHERE bestellnum=$nr";
		return $this->Transact();
	}

	function clearProductDiscounts($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "DELETE FROM seg_service_discounts WHERE service_code=$nr AND service_area='PH'";
		return $this->Transact();
	}

	function setProductAvailability($nr, $availArray) {
		global $db;
		$bulk = array();
		foreach ($availArray as $avail) {
			$bulk[] = array($avail);
		}
		$nr = $db->qstr($nr);
		$this->sql = "INSERT INTO $this->tb_avail(bestellnum,area_code) VALUES($nr,?)";
		if($buf=$db->Execute($this->sql,$bulk)) {
			return true;
		} else { return false; }
	}

	function setProductClassification($nr, $csfArray) {
		global $db;
		$bulk = array();
		foreach ($csfArray as $csf) {
			$bulk[] = array($csf);
		}
		$nr = $db->qstr($nr);
		$this->sql = "INSERT INTO $this->tb_prod_class(bestellnum,class_code) VALUES($nr,?)";
		if($buf=$db->Execute($this->sql,$bulk)) {
			return true;
		} else { return false; }
	}

	function setProductDiscounts($nr, $dscArray, $prcArray) {
		global $db;

		$bulk = array();
		foreach ($dscArray as $i=>$dsc) {
			$bulk[] = array($dsc, $prcArray[$i]);
		}

		$nr = $db->qstr($nr);
		$this->sql = "INSERT INTO seg_service_discounts(discountid,service_code,price,service_area) VALUES(?,$nr,?,'PH')";
		if($buf=$db->Execute($this->sql,$bulk)) {
				return true;
		} else { return false; }
	}

	function getProductDiscounts($nr) {
		global $db;
		$nr = $db->qstr($nr);
		$this->sql = "SELECT discountid,price FROM seg_service_discounts WHERE service_code=$nr AND service_area='PH'";
		if($buf=$db->Execute($this->sql)) {
			return $buf;
		} else { return false; }
	}

	function search_products_for_tray($keyword, $discountID, $area, $offset=0, $rowcount=10) {
		global $db;

		#filter added by bryan on Nov 7,2008
		$this->sql="SELECT SQL_CALC_FOUND_ROWS a.*,\n".
/*			"(SELECT GROUP_CONCAT(CONCAT(ei.expiry_date,':',IFNULL(ei.qty,0)) SEPARATOR '\\n') FROM seg_inventory AS ei WHERE ei.item_code=a.bestellnum AND ei.serial_no='' AND ei.qty>0 AND ei.area_code=".$db->qstr($area).") AS `expiration_dates`,".  */
            // "IFNULL(fn_getitemqty(a.bestellnum, NULL, '{$area}', NULL, NULL, NULL), 0) stock_qty,\n".
			"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
			"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_charge*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n";

		if ($discountID) {
			$this->sql .= "IF(a.is_socialized".
					($area ? " AND (IFNULL((SELECT allow_socialized FROM seg_areas AS a1 WHERE a1.area_code=".$db->qstr($area)."),1))" : "").
					",\n".
					"IFNULL(\n".
						"(SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),\n".
						"IFNULL(a.price_cash,-1)),\n".
					"IFNULL(a.price_cash,-1)) AS dprice,\n";
		}
		else
				$this->sql .= "a.price_cash AS dprice,\n";

		// check if GMAP
		$this->sql .= "IFNULL((SELECT cat.description FROM seg_type_product_category as cat WHERE cat.id=a.category_id),-1) AS type_cat,";

		$this->sql .= "IFNULL(a.price_cash,-1) AS cshrpriceppk,\n".
				"IFNULL(a.price_charge,-1) AS chrgrpriceppk,\n".
								"(SELECT spc.class_name FROM seg_pharma_products_classification sppc INNER JOIN seg_product_classification spc ON (spc.class_code=sppc.class_code) WHERE sppc.bestellnum=a.bestellnum AND spc.class_name='Sponge') as class_name \n".
				"FROM care_pharma_products_main AS a\n".
				"LEFT JOIN seg_type_product AS ptype ON ptype.nr=a.type_nr\n";

		$where = array();
		$where[] = "ptype.prod_class IN ('M','S')";
		$where[] = "a.is_deleted!=1";
		if ($keyword && $keyword!='*') {
			$terms = preg_split("/[\s,]+/",$keyword);
			foreach ($terms as $i=>$v)
				$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
			$regexp = implode(")|(",$terms);
			$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
		}
		#if ($area) $where[] = "a.bestellnum IN (SELECT bestellnum FROM seg_pharma_products_availability WHERE area_code='$area')";
		if ($where)
			$this->sql.= "WHERE (".implode(") AND (",$where).")\n";

		$this->sql .= "ORDER BY artikelname\n";
		if ($rowcount) {
			$this->sql .= "LIMIT $offset, $rowcount\n";
		}
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

		function searchItemsForReqstOrIssuance($keyword, $stype, $offset=0, $rowcount=10) {
				global $db;

				$this->sql
					= "SELECT SQL_CALC_FOUND_ROWS a.*,itm.*,\n".
					 "(SELECT unit_name FROM seg_unit AS su1 WHERE su1.unit_id = itm.pack_unit_id) AS pack_unitname,\n".
					 "(SELECT unit_name FROM seg_unit as su2 WHERE su2.unit_id = itm.pc_unit_id) AS pc_unitname  \n".
					 "FROM care_pharma_products_main AS a LEFT JOIN seg_item_extended AS itm ".
					 "ON a.bestellnum = itm.item_code\n";

				$where = array();
				$where[] = "a.is_deleted!=1";
				if ($keyword && $keyword!='*') {
						$terms = preg_split("/[,]+/",$keyword);
						$terms_quoted = array();
						foreach ($terms as $i=>$v) {
							if ($terms[$i])
								$terms_quoted[] = preg_quote(preg_replace("/\s+/"," ",trim($terms[$i])));
						}
						$regexp = implode(")|(",$terms_quoted);
						$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
				}
//        if ($stype) $where[] = "prod_class = '$stype'";
				if ($stype) $where[] = "a.type_nr = $stype";
				if ($where)
						$this->sql.= "WHERE (".implode(") AND (",$where).")\n";

				$this->sql .= "ORDER BY artikelname\n";
				if ($rowcount) {
						$this->sql .= "LIMIT $offset, $rowcount\n";
				}
				if($this->result=$db->Execute($this->sql)) {
						return $this->result;
				} else { return false; }
		}

		function getDefBigUnitID(&$big_unitid, &$big_unitname) {
				global $db;

				$strSQL = $db->Prepare("select unit_id, unit_name ".
									"   from seg_unit ".
									"   where is_default <> 0 and is_unit_per_pc = 0 ".
									"   order by unit_id limit 1");

				if ($result = $db->GetRow($strSQL)) {
					$big_unitid = $result['unit_id'];
					$big_unitname = $result['unit_name'];
						
				}
		}

		function getDefSmallUnitID(&$small_unitid, &$small_unitname) {
				global $db;

				$strSQL = $db->Prepare("select unit_id, unit_name ".
									"   from seg_unit ".
									"   where is_default <> 0 and is_unit_per_pc <> 0 ".
									"   order by unit_id limit 1");

				if ($result = $db->GetRow($strSQL)) {
					$small_unitid = $result['unit_id'];
					$small_unitname = $result['unit_name'];
				}
		}

		function getAllUnitOption($b_smallunit = true) {
				global $db;

				$this->sql = "SELECT * FROM $this->tb_units where is_unit_per_pc ".(($b_smallunit) ? "<> 0" : "= 0");
				$soption = "";
				if($this->result=$db->Execute($this->sql)) {
						while($row = $this->result->FetchRow()){
								$soption .= "<option".($row['is_default'] == '1'   ? " selected " : " ") ."value = '".$row['unit_id']."'>".$row['unit_name']."</option>";
						}
				}

				return $soption;
		}

		function getSmallUnitOption() {
				global $db;

				$this->sql = "SELECT * FROM $this->tb_units WHERE is_unit_per_pc=1";
				$soption = "";
				if($this->result=$db->Execute($this->sql)) {
						while($row = $this->result->FetchRow()){
								$soption .= "<option".($row['is_default'] == '1'   ? " selected " : " ") ."value = '".$row['unit_id']."'>".$row['unit_name']."</option>";
						}
				}

				return $soption;
		}

		function getBigUnitOption() {
				global $db;

				$this->sql = "SELECT * FROM $this->tb_units WHERE is_unit_per_pc=0";
				$soption = "";
				if($this->result=$db->Execute($this->sql)) {
						while($row = $this->result->FetchRow()){
								$soption .= "<option".($row['is_default'] == '1'   ? " selected " : " ") ."value = '".$row['unit_id']."'>".$row['unit_name']."</option>";
						}
				}

				return $soption;
		}

		function getUnitInfo($unit_id) {
				global $db;

				$this->sql = "SELECT * FROM $this->tb_units WHERE unit_id=$unit_id";

				if($this->result=$db->Execute($this->sql)) {
						return $this->result->fetchRow();
				}
				else {
						return false;
				}
		}

		function setExtendedInfo($item_no,$smallunit,$bigunit,$perpack,$minqty) {
				global $db;

				$this->sql = "INSERT INTO $this->tb_extended(item_code,min_qty,pack_unit_id,pc_unit_id,qty_per_pack) VALUES('$item_no',$minqty,$bigunit,$smallunit,$perpack)";
				if($buf=$db->Execute($this->sql)) {
					return true;
				} else { return false; }

		}

		function updateExtendedInfo($item_no,$smallunit,$bigunit,$perpack,$minqty) {
				global $db;

				$this->sql = "UPDATE $this->tb_extended SET min_qty=$minqty,pack_unit_id=$bigunit,pc_unit_id=$smallunit,qty_per_pack=$perpack WHERE item_code='$item_no'";
				if($buf=$db->Execute($this->sql)) {
					 return true;
				} else { return false; }

		}

		function getTypes($b_all=false) {
				global $db;

				if ($b_all)
						$this->sql = "select * from $this->tb_types order by name";
				else
						$this->sql = "select * from $this->tb_types where is_inactive = 0 order by name";
				if($this->result=$db->Execute($this->sql)) {
						return $this->result;
				} else { return false; }
		}

		function getItemType($type_nr) {
				global $db;

				$this->sql = "select * from $this->tb_types where nr = $type_nr";
				if($this->result=$db->Execute($this->sql)) {
						return $this->result->FetchRow();
				} else { return false; }
		}

		function getTypebyProdClass($prod_class=""){
				global $db;

				$this->sql = "SELECT nr FROM $this->tb_types WHERE prod_class='$prod_class'";

				if($this->result=$db->Execute($this->sql)) {
						 return $this->result->FetchRow();
				} else { return false; }
		}

		function useItemType(){
				$this->coretable=$this->tb_types;
				$this->ref_array=$this->fld_type;
		}

		function saveItemType(&$data) {
				if(!is_array($data)) return FALSE;
				$this->useItemType();
				$this->buffer_array=NULL;
				return $this->insertDataFromInternalArray();
		}

		function delItemType($type_nr) {
				global $db;
				$type_nr = $db->qstr($type_nr);
				$this->sql = "delete from $this->tb_types where nr = $type_nr";
				return $this->Transact();
		}

		function getProdClassName($sclass) {
				$sname = '';

				switch ($sclass) {
						case 'M':
								$sname = "Medicine";
								break;
						case 'S':
								$sname = "Med. Supply";
								break;
						case 'E':
								$sname = "Equipment";
								break;
						case 'NS':
								$sname = "Nonmed Supply";
								break;
						case 'B':
								$sname = "Blood";
								break;
						case 'HS':
								$sname = "Housekeeping Supply";
						default:
								$sname = "NC";
				}
				return $sname;
		}

		function getProdClassOption($sclass='') {
				$soption = "<option".($sclass == ''   ? " selected " : " ") ."value = ''>-- Select the Classification --</option>".
									 "<option".($sclass == 'M'  ? " selected " : " ") ."value = 'M'>Medicine</option>".
									 "<option".($sclass == 'S'  ? " selected " : " ") ."value = 'S'>Medical Supply</option>".
									 "<option".($sclass == 'E'  ? " selected " : " ") ."value = 'E'>Equipment</option>".
									 "<option".($sclass == 'NS' ? " selected " : " ") ."value = 'NS'>Nonmedical Supply</option>".
									 "<option".($sclass == 'B'  ? " selected " : " ") ."value = 'B'>Blood</option>".
									 "<option".($sclass == 'HS'  ? " selected " : " ") ."value = 'HS'>Housekeeping Supply</option>";;
				return $soption;
		}
		#added by monmon : fetch areas in inventory
		function getInventoryAreas(){
			global $db;
			$options = "<option value =''>-- Select Area --</option>";
			$this->sql = "SELECT * FROM seg_areas";
			$this->result = $db->Execute($this->sql);
			while ($row = $this->result->FetchRow()) {
				$options .= "<option value ='".$row['area_code']."'>".$row['area_name']."</option>";
			}
			return $options;
		}
		#end
		function getItemTypeOption($sclass='') {
				global $db;

				$soption = "<option".($sclass == ''   ? " selected " : " ") ."value = ''>-- Select Classification --</option>";

				$this->sql = "select * from seg_type_product";
				$this->result = $db->Execute($this->sql);

				while($row = $this->result->FetchRow()){
						$soption .= "<option".($sclass == $row["prod_class"]  ? " selected " : " ") ."value = '".$row["prod_class"]."'>".$row["name"]."</option>";
				}
				return $soption;
		}

        function getProdClasses() {
            global $db;
            
            $strSQL = "SELECT 
                          GROUP_CONCAT(
                            DISTINCT prod_class 
                            ORDER BY prod_class SEPARATOR '\',\''
                          ) prodclass 
                        FROM
                          seg_type_product stp 
                        WHERE NOT is_inactive";
            $row = $db->GetRow($strSQL);            
            return ($row) ? "'".$row['prodclass']."'" : $row;            
        }
	/**
	* Checks if the product exists based on its primary key number.
	* @access public
	* @param int Item number
	* @param string Determines the final table name
	* @return boolean
	*/
		#added by Bryan on Nov. 28, 2008
		function search_products_for_issuance_tray($keyword, $discountID, $area, $offset=0, $rowcount=10, $filter=NULL, $areadest) {
				global $db;

				#filter added by bryan on Nov 7,2008
                // Not used so commented out by LST -- 02.07.2012 -
//				$filterSQL = "";
//				$filterSQL .= ") (AND a.prod_class='".$filter."'";
                // -------------------------------------------------
                
                
                // added by LST -- 02.07.2012
                $alltypes = false;
                if (!$filter) {
                    $alltypes = $this->getProdClasses();
                }

				$this->sql="SELECT DISTINCT SQL_CALC_FOUND_ROWS b.refno AS request_refno, request_date, fn_get_personellname_lastfirstmi(requestor_id) AS requestor, 
							 GROUP_CONCAT(DISTINCT bestellnum SEPARATOR ',')
							 AS item_codes,

							 a.*,\n".
								"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
								"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_charge*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n";

				if ($discountID) {
                    $this->sql .= "IF(a.is_socialized".
                                    ($area ? " AND (IFNULL((SELECT allow_socialized FROM seg_areas AS a1 WHERE a1.area_code='$area'),1))" : "").
                                    ",\n".
                                    "IFNULL(\n".
                                            "(SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),\n".
                                            "IFNULL(a.price_cash,-1)),\n".
                                    "IFNULL(a.price_cash,-1)) AS dprice,\n";
				}
				else
                    $this->sql .= "a.price_cash AS dprice,\n";

				$this->sql .= "IFNULL(a.price_cash,-1) AS cshrpriceppk,\n".
								"IFNULL(a.price_charge,-1) AS chrgrpriceppk\n".
								"FROM care_pharma_products_main AS a \n";

				$this->sql .= "LEFT JOIN seg_internal_request_details AS b ON a.bestellnum=b.item_code\n";     //seg_requests_served

				if ($area) $this->sql .= "JOIN seg_internal_request AS c ON b.refno=c.refno\n";   // b.request_refno

				if ($filter || $alltypes) $this->sql .= "JOIN seg_type_product AS d ON a.type_nr=d.nr\n";

				$where = array();
				if ($keyword && $keyword!='*') {
						$terms = preg_split("/[\s,]+/",$keyword);
						foreach ($terms as $i=>$v)
								$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
						$regexp = implode(")|(",$terms);
						$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
				}

				if ($area) $where[] = "c.area_code_dest='$area'";
				 if ($areadest) $where[] = "c.area_code='$areadest'";

				if ($filter) {
                    $where[] = "d.prod_class='$filter'";                
                }
                else {
                    if ($alltypes) $where[] = "d.prod_class IN ($alltypes)";
                }

				if ($where)
						$this->sql.= "WHERE (".implode(") AND (",$where).")\n";

				$this->sql .= "GROUP BY request_refno ORDER BY request_refno\n";
				if ($rowcount) {
						//$this->sql .= "LIMIT $offset, $rowcount\n";
				}
				if($this->result=$db->Execute($this->sql)) {
						return $this->result;
				} else { return false; }
		}

		/**
		* Checks if the product exists based on its primary key number.
		* @access public
		* @param int Item number
		* @param string Determines the final table name
		* @return boolean
		*/
		#added by Bryan on April. 13, 2009
		function search_products_for_adjustment_tray($keyword, $discountID, $area, $offset=0, $rowcount=10, $filter=NULL, $areadest) {
				global $db;

				#filter added by bryan on Nov 7,2008
				$filterSQL = "";
				$filterSQL .= ") (AND a.prod_class='".$filter."'";

				#echo $filter."pangit<br>";

				$this->sql="SELECT DISTINCT SQL_CALC_FOUND_ROWS a.*,\n".
								"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
								"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_charge*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n";

				if ($discountID) {
						$this->sql .= "IF(a.is_socialized".
										($area ? " AND (IFNULL((SELECT allow_socialized FROM seg_areas AS a1 WHERE a1.area_code='$area'),1))" : "").
										",\n".
										"IFNULL(\n".
												"(SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),\n".
												"IFNULL(a.price_cash,-1)),\n".
										"IFNULL(a.price_cash,-1)) AS dprice,\n";
				}
				else
								$this->sql .= "a.price_cash AS dprice,\n";

				$this->sql .= "IFNULL(a.price_cash,-1) AS cshrpriceppk,\n".
								"IFNULL(a.price_charge,-1) AS chrgrpriceppk\n".
								"FROM care_pharma_products_main AS a \n";

				$this->sql .= "LEFT JOIN seg_internal_request_details AS b ON a.bestellnum=b.item_code\n";     //seg_requests_served

				if ($area) $this->sql .= "JOIN seg_internal_request AS c ON b.refno=c.refno\n";   // b.request_refno

				if ($filter) $this->sql .= "JOIN seg_type_product AS d ON a.type_nr=d.nr\n";

				$where = array();
				if ($keyword && $keyword!='*') {
						$terms = preg_split("/[\s,]+/",$keyword);
						foreach ($terms as $i=>$v)
								$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
						$regexp = implode(")|(",$terms);
						$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
				}

				if ($area) $where[] = "c.area_code_dest='$area'";
				 if ($areadest) $where[] = "c.area_code='$areadest'";

				if ($filter) $where[] = "d.prod_class='$filter'";

				if ($where)
						$this->sql.= "WHERE (".implode(") AND (",$where).")\n";

				$this->sql .= "ORDER BY artikelname\n";
				if ($rowcount) {
						$this->sql .= "LIMIT $offset, $rowcount\n";
				}
				if($this->result=$db->Execute($this->sql)) {
						return $this->result;
				} else { return false; }
		}


	function ProductExists($nr=0,$type=''){
		global $db;
		if(empty($type)||!$nr) return false;
		$this->useProduct($type);
		$this->sql="SELECT bestellnum FROM $this->coretable WHERE bestellnum='$nr'";

				if($buf=$db->Execute($this->sql)) {
						if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
	}
		#############
		#by bryan
		function ConcatFieldStringType($fieldname,$str=''){
				global $dbtype;

				switch($dbtype){
						case 'mysql' :
						case 'mysqlt': return "CONCAT($fieldname,\"$str\")";
													 break;
						case 'postgres': return "$fieldname || \"$str\"";
								break;
						case 'postgres7':return "$fieldname || \"$str\"";
								break;
						default: return "$fieldname || \"$str\"";
				}
		}
		/**
		* Returns  a "history" field concat string for sql query.
		*
		* This function resolves the problems of concatenating the "history" field value with a string in different db types
		* @param str String
		* @return string
		*/
		function ConcatHistoryType($str=''){
				return $this->ConcatFieldStringType('history',$str);
		}
		
		/*
		*Created By Maimai
		*Get Lists of Pending Requisition
		*02-03-2015
		*/

		function getListofPedingRequisition($to_area, $from_area){
			global $db;

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS 
							  sird.`refno` AS request_refno,
							  sir.`request_date`,
							  fn_get_personellname_lastfirstmi (sir.`requestor_id`) AS requestor,
							  GROUP_CONCAT(sird.`item_code`) AS item_codes,
							  GROUP_CONCAT(cppm.`artikelname`) AS item_names 
							FROM
							  seg_internal_request_details sird 
							  LEFT JOIN 
							    (SELECT 
							      srs.`item_code`,
							      srs.`request_refno`,
							      IFNULL(SUM(srs.`served_qty`), 0) AS served_qty 
							    FROM
							      seg_requests_served srs 
							    GROUP BY srs.`request_refno`,
							      srs.`item_code`) t 
							    ON (
							      t.request_refno = sird.`refno` 
							      AND t.item_code = sird.`item_code`
							    ) 
							  LEFT JOIN seg_internal_request sir 
							    ON sir.`refno` = sird.`refno` 
							  LEFT JOIN care_pharma_products_main cppm 
							    ON cppm.`bestellnum` = sird.`item_code` 
							WHERE (
							    sird.`item_qty` <> t.served_qty 
							    OR t.served_qty IS NULL
							  ) 
							  AND sir.`area_code_dest` = ".$db->qstr($to_area)." 
							  AND sir.`area_code` = ".$db->qstr($from_area)." 
							GROUP BY sir.`refno` 
							ORDER BY sird.`refno` ";

			if($this->result = $db->Execute($this->sql)){
				return $this->result;
			}

			return false;
		}

		function getListofPendingItemsRefno($refno){
			global $db;

			$this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS 
							  sird.`item_code` AS bestellnum,
							  cppm.`artikelname`,
							  cppm.`generic`,
							  cppm.`price_cash` AS dprice,
							  sird.`item_qty` - IFNULL(t.served_qty, 0) AS qty_requested,
							  sird.`unit_id`,
							  u.unit_name, 
							  u.is_unit_per_pc 
							FROM
							  seg_internal_request_details sird 
							  LEFT JOIN 
							    (SELECT 
							      srs.`item_code`,
							      srs.`request_refno`,
							      IFNULL(SUM(srs.`served_qty`), 0) AS served_qty 
							    FROM
							      seg_requests_served srs 
							    GROUP BY srs.`request_refno`,
							      srs.`item_code`) t 
							    ON (
							      t.request_refno = sird.`refno` 
							      AND t.item_code = sird.`item_code`
							    ) 
							  LEFT JOIN seg_internal_request sir 
							    ON sir.`refno` = sird.`refno` 
							  LEFT JOIN care_pharma_products_main cppm 
							    ON cppm.`bestellnum` = sird.`item_code`
							  LEFT JOIN seg_unit u ON u.unit_id = sird.unit_id 
							WHERE (
							    sird.`item_qty` <> t.served_qty 
							    OR t.served_qty IS NULL
							  ) 
							  AND sir.`refno` =  ".$db->qstr($refno);

			if($this->result = $db->Execute($this->sql)){
				return $this->result;
			}

			return false;
		}
		######################

		/**
		* Created By Jarel
		* Created On 1/26/2014
		* Get product description
		*/
		function getProductDesc($item_code)
		{
			global $db;
			$this->sql = $db->Prepare("SELECT artikelname FROM `care_pharma_products_main` a WHERE a.`bestellnum` = ? ");
			return $db->GetOne($this->sql,array($item_code));
		}



		/**
		* Created By Jarel
		* Created On 1/26/2014
		* Get product description
		*/
		function search_products_for_issuance_tray2($refno, $area, $areadest, $filter=NULL) {
			global $db;

            $alltypes = false;
            if (!$filter) {
                $alltypes = $this->getProdClasses();
            }

			$this->sql="SELECT DISTINCT SQL_CALC_FOUND_ROWS b.refno AS request_refno, request_date, fn_get_personellname_lastfirstmi(requestor_id) AS requestor,".
							"a.*,".
							"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_cash*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS cashscprice,\n".
							"IFNULL((SELECT d1.price FROM seg_service_discounts AS d1 WHERE d1.service_code=a.bestellnum AND d1.service_area='PH' AND d1.discountid='SC'),a.price_charge*(1-IFNULL((SELECT discount FROM seg_discount WHERE discountid='SC'),0.2))) AS chargescprice,\n";

			if ($discountID) {
                $this->sql .= "IF(a.is_socialized".
                                ($area ? " AND (IFNULL((SELECT allow_socialized FROM seg_areas AS a1 WHERE a1.area_code='$area'),1))" : "").
                                ",\n".
                                "IFNULL(\n".
                                        "(SELECT d2.price FROM seg_service_discounts AS d2 WHERE d2.service_code=a.bestellnum AND d2.service_area='PH' AND d2.discountid='$discountID'),\n".
                                        "IFNULL(a.price_cash,-1)),\n".
                                "IFNULL(a.price_cash,-1)) AS dprice,\n";
			}
			else
                $this->sql .= "a.price_cash AS dprice,\n";

			$this->sql .= "IFNULL(a.price_cash,-1) AS cshrpriceppk,\n".
							"IFNULL(a.price_charge,-1) AS chrgrpriceppk\n".
							"FROM care_pharma_products_main AS a \n";

			$this->sql .= "LEFT JOIN seg_internal_request_details AS b ON a.bestellnum=b.item_code\n";     //seg_requests_served

			if ($area) $this->sql .= "JOIN seg_internal_request AS c ON b.refno=c.refno\n";   // b.request_refno

			if ($filter || $alltypes) $this->sql .= "JOIN seg_type_product AS d ON a.type_nr=d.nr\n";

			$where = array();

			if($refno) $where[] = "c.refno='$refno'";
			if ($area) $where[] = "c.area_code_dest='$area'";
			if ($areadest) $where[] = "c.area_code='$areadest'";

			if ($filter) {
                $where[] = "d.prod_class='$filter'";                
            }
            else {
                if ($alltypes) $where[] = "d.prod_class IN ($alltypes)";
            }

			if ($where)
					$this->sql.= "WHERE (".implode(") AND (",$where).")\n";

			$this->sql .= "ORDER BY request_refno\n";
			if ($rowcount) {
					//$this->sql .= "LIMIT $offset, $rowcount\n";
			}
			if($this->result=$db->Execute($this->sql)) {
					return $this->result;
			} else { return false; }
		}

		function get_list(){
			global $db;

			$this->sql = "SELECT 
							  bestellnum,
							  artikelname,
							  price_cash,
							  IF(
							    prod_class = 'M',
							    'Meds',
							    'Supplies'
							  ) item_type 
							FROM
							  care_pharma_products_main 
							WHERE is_deleted <> 1 
							ORDER BY artikelname ";

			$this->result = $db->Execute($this->sql);
			if($this->result){
				return $this->result;
			}

			return false;
		}
}
?>