<?php
  
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/inventory/class_stock_card.php');
require_once($root_path.'include/care_api_classes/curl/class_curl.php');
require_once($root_path.'include/care_api_classes/inventory/class_request.php');
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');

class SegInventoryReport extends Core{
    

	/**
	* Returns the count of the total service usages of an item
	*
	* @param mixed $item
	* @param mixed $area
	* @param mixed $fromDate
	* @param mixed $toDate
	*/
	function getServiceUsageCount($item, $area, $fromDate, $toDate)
	{
		global $db;
		$this->sql = "SELECT SUM(IF(u.is_unit_per_pc, sud.qty_used, sud.qty_used*ie.qty_per_pack)) `quantity`\n".
			"FROM seg_service_usage_details sud\n".
				"INNER JOIN seg_service_usage su ON su.refno=sud.refno\n".
				"LEFT JOIN seg_unit u ON u.unit_id=sud.unit_id\n".
				"LEFT JOIN seg_item_extended ie ON sud.item_code=ie.item_code\n".
			"WHERE sud.item_code=".$db->qstr($item)."\n".
				"AND su.ref_source=".$db->qstr($area)."\n".
				"AND DATE(su.served_date) BETWEEN ".$db->qstr($fromDate)." AND ".$db->qstr($toDate);

		$result=$db->GetOne($this->sql);
		return $result;
	}

	/**
	* Returns the count of the total pharmacy dispensation of an item
	*
	* @param mixed $item
	* @param mixed $area
	* @param mixed $fromDate
	* @param mixed $toDate
	*/
	function getPharmaUsageCount($item, $area, $fromDate, $toDate)
	{
		global $db;
		$this->sql = "SELECT SUM(oi.quantity)\n".
			"FROM seg_pharma_order_items oi\n".
				"INNER JOIN seg_pharma_orders o ON o.refno=oi.refno\n".
			"WHERE oi.bestellnum=".$db->qstr($item)."\n".
				"AND o.pharma_area=".$db->qstr($area)."\n".
				"AND DATE(o.orderdate) BETWEEN ".$db->qstr($fromDate)." AND ".$db->qstr($toDate);

		$result=$db->GetOne($this->sql);
		if ($result === FALSE)
			return FALSE;

		$this->sql = "SELECT SUM(oi.quantity)\n".
			"FROM seg_more_phorder_details oi\n".
				"INNER JOIN seg_more_phorder o ON o.refno=oi.refno\n".
			"WHERE oi.bestellnum=".$db->qstr($item)."\n".
				"AND oi.area_code=".$db->qstr($area)."\n".
				"AND DATE(o.chrge_dte) BETWEEN ".$db->qstr($fromDate)." AND ".$db->qstr($toDate);

		$result2=$db->GetOne($this->sql);
		return (int)$result + (int)$result2;
	}


	/**
	* Returns the count of the total pharmacy return transactions for an item
	*
	* @param mixed $item
	* @param mixed $area
	* @param mixed $fromDate
	* @param mixed $toDate
	*/
	function getPharmaReturnsCount($item, $area, $fromDate, $toDate)
	{
		global $db;
		$this->sql = "SELECT SUM(ri.quantity)\n".
			"FROM seg_pharma_return_items ri\n".
				"INNER JOIN seg_pharma_returns r ON r.return_nr=ri.return_nr\n".
				"INNER JOIN seg_pharma_orders o ON o.refno=ri.ref_no\n".
			"WHERE ri.bestellnum=".$db->qstr($item)."\n".
				"AND o.pharma_area=".$db->qstr($area)."\n".
				"AND DATE(r.return_date) BETWEEN ".$db->qstr($fromDate)." AND ".$db->qstr($toDate);
		$result=$db->GetOne($this->sql);
		return $result;
	}


	/**
	* Returns the total count of deliveries
	*
	* @param mixed $item
	* @param mixed $area
	* @param mixed $fromDate
	* @param mixed $toDate
	*/
	function getDeliveriesCount($item, $area, $fromDate, $toDate)
	{
		global $db;
		$this->sql = "SELECT SUM(IF(u.is_unit_per_pc, dd.item_qty, dd.item_qty*ie.qty_per_pack)) `quantity`\n".
			"FROM seg_delivery_details dd\n".
				"INNER JOIN seg_delivery d ON d.refno=dd.refno\n".
				"LEFT JOIN seg_unit u ON u.unit_id=dd.unit_id\n".
				"LEFT JOIN seg_item_extended ie ON dd.item_code=ie.item_code\n".
			"WHERE dd.item_code=".$db->qstr($item)."\n".
				"AND d.area_code=".$db->qstr($area)."\n".
				"AND DATE(d.receipt_date) BETWEEN ".$db->qstr($fromDate)." AND ".$db->qstr($toDate);
		$result=$db->GetOne($this->sql);
		return $result;
	}


	/**
	* Returns the total number of issuances FROM the specified area
	*
	* @param mixed $item
	* @param mixed $area
	* @param mixed $fromDate
	* @param mixed $toDate
	*/
	function getIssuancesOutCount($item, $area, $fromDate, $toDate)
	{
		global $db;
		$this->sql = "SELECT SUM(IF(u.is_unit_per_pc, id.item_qty, id.item_qty*ie.qty_per_pack)) `quantity`\n".
			"FROM seg_issuance_details id\n".
				"INNER JOIN seg_issuance i ON i.refno=id.refno\n".
				"LEFT JOIN seg_unit u ON u.unit_id=id.unit_id\n".
				"LEFT JOIN seg_item_extended ie ON id.item_code=ie.item_code\n".
			"WHERE id.item_code=".$db->qstr($item)."\n".
				"AND i.src_area_code=".$db->qstr($area)."\n".
				"AND i.status=2\n".
				"AND DATE(i.issue_date) BETWEEN ".$db->qstr($fromDate)." AND ".$db->qstr($toDate);
		$result=$db->GetOne($this->sql);
		return $result;
	}


	/**
	* Returns the total number of issuances TO the specifed inventory area
	*
	* @param mixed $item
	* @param mixed $area
	* @param mixed $fromDate
	* @param mixed $toDate
	*/
	function getIssuancesInCount($item, $area, $fromDate, $toDate)
	{
		global $db;
		$this->sql = "SELECT SUM(IF(u.is_unit_per_pc, id.item_qty, id.item_qty*ie.qty_per_pack)) `quantity`\n".
			"FROM seg_issuance_details id\n".
				"INNER JOIN seg_issuance i ON i.refno=id.refno\n".
				"LEFT JOIN seg_unit u ON u.unit_id=id.unit_id\n".
				"LEFT JOIN seg_item_extended ie ON id.item_code=ie.item_code\n".
			"WHERE id.item_code=".$db->qstr($item)."\n".
				"AND i.area_code=".$db->qstr($area)."\n".
				"AND i.status=2\n".
				"AND DATE(i.issue_date) BETWEEN ".$db->qstr($fromDate)." AND ".$db->qstr($toDate);
		$result=$db->GetOne($this->sql);
		return $result;
	}


	/**
	* put your comment there...
	*
	* @param mixed $item
	* @param mixed $area
	* @param mixed $extra
	* @param string $fromDate
	* @param mixed $toDate
	* @return ADODB
	*/
	function getStockMovement ($item, $area, $fromDate=null, $toDate=null) {
		/**
		* @var ADOConnection $db
		* @global
		*/
		global $db;

		if (($area=='XRAY') || ($area=='RADIO') || ($area=='USD'))
			$refSource = 'RD';
		else
			$refSource = $area;
		$refSource = $db->qstr($refSource);

		$item = $db->qstr($item);
		$area = $db->qstr($area);

		/**
		* Attempt to generate a start date if npt supplied
		*/
		if (empty($fromDate)) {
			$fromDate = $db->GetOne("SELECT eod_date FROM seg_eod_inventory WHERE item_code=".$item." ORDER BY eod_date LIMIT 1");
			if (empty($fromDate))
				$fromDate = '1970-01-01';
		}
		else
			$fromDate = date("Y-m-d",strtotime($fromDate));
		$fromDate = $db->qstr($fromDate);

		/**
		* Attempt to generate an end date if not supplied
		*/
		if (empty($toDate))
			$toDate = '2099-01-01';
		else
			$toDate = date("Y-m-d",strtotime($toDate));
		$toDate = $db->qstr($toDate);

		$this->sql =
	"SELECT ppm.artikelname,f.cutoff_date,su2.unit_name,\n".
		"f.inqty as inqty,f.qty as outqty,f.adjust,f.expiry,\n".
		"f.area, f.refno, f.create_date\n".
	"FROM (\n";


		$query = array();

		// Service usage (combined small unit and big unit)
		$query[] = "SELECT su.create_tm as cutoff_date, sud.item_code as item,
			0 as inqty, sud.qty_used as qty, -1 `adjust`,NULL `expiry`,
			'usage' as area, su.refno as refno , modify_tm AS create_date
		FROM seg_service_usage as su
			INNER JOIN seg_service_usage_details as sud ON su.refno=sud.refno
			INNER JOIN seg_unit as u ON sud.unit_id=u.unit_id
		WHERE
			su.ref_source=$refSource
			AND sud.item_code=$item
			AND DATE(su.create_tm) BETWEEN $fromDate AND $toDate";


	// Service usage (big unit)
//	$query[] = "SELECT su.create_tm as cutoff_date, sud.item_code as item,
//		0 as inqty, sud.qty_used * (ie.qty_per_pack) as qty, -1 `adjust`,
//		'usage' as area, su.refno as refno, modify_tm AS create_date
//	FROM seg_service_usage as su
//		INNER JOIN seg_service_usage_details as sud ON su.refno=sud.refno
//		INNER JOIN seg_unit as u ON sud.unit_id=u.unit_id
//		INNER JOIN seg_item_extended as ie ON sud.item_code=ie.item_code
//	WHERE
//		u.is_unit_per_pc = 0
//		AND su.ref_source=$refSource
//		AND sud.item_code=$item
//		AND DATE(su.create_tm) BETWEEN $fromDate AND $toDate";


		// Issuances via Billing miscellaneous charges
		$query[] = "SELECT mp.chrge_dte as cutoff_date, mpd.bestellnum as item,
			0 as inqty, mpd.quantity as qty, -1 `adjust`, NULL `expiry`,
			CONCAT(fn_get_person_name(e.pid),' (phic)') as area, mp.refno as refno,modify_dt AS create_date
		FROM seg_more_phorder as mp
			INNER JOIN seg_more_phorder_details as mpd ON mp.refno=mpd.refno
			INNER JOIN care_encounter e ON e.encounter_nr=mp.encounter_nr
		WHERE
			mpd.bestellnum=$item
			AND mpd.area_code=$area
			AND DATE(mp.chrge_dte) BETWEEN $fromDate AND $toDate";

		// issuance - de la cruz, juan (Cash, OR#)
		// pharma issueances
		$query[] = "SELECT po.orderdate as cutoff_date, poi.bestellnum as item,
			0 as inqty, poi.quantity as qty,-1 `adjust`,NULL `expiry`,
			CONCAT('issuance - ',
				IF(po.is_cash,
					CASE
						WHEN poi.request_flag='PAID' THEN
							CONCAT( po.ordername, ' (cash)' )
						ELSE
							CONCAT( po.ordername, ' (cash, no or)')
					END,
					CASE
						WHEN po.type_charge=1 THEN
							CONCAT( po.ordername, ' (credit)' )
						WHEN po.type_charge=2 THEN
							CONCAT( po.ordername, ' (free)' )
						WHEN po.type_charge=3 THEN
							CONCAT( po.charge_name, ' (guarantor)' )
						ELSE
							CONCAT( po.ordername, ' (phic)' )
					END
				)
			) area,
			po.refno as refno,modify_time AS create_date
		FROM seg_pharma_orders po
			INNER JOIN seg_pharma_order_items poi ON po.refno=poi.refno
		WHERE po.pharma_area=$area
			AND poi.serve_status='S'
			AND poi.bestellnum=$item
			AND DATE(po.orderdate) BETWEEN $fromDate AND $toDate";


		// returns
		$query[] =  "SELECT mp.return_date AS cutoff_date, mpd.bestellnum AS item,
			mpd.quantity AS inqty,0 AS qty,-1 `adjust`,NULL `expiry`,
			'return' AS AREA, mp.return_nr AS refno,modify_time AS create_date
		FROM seg_pharma_returns AS mp
			INNER JOIN seg_pharma_return_items AS mpd ON mp.return_nr=mpd.return_nr
		WHERE mp.pharma_area=$area
			AND mpd.bestellnum=$item
			AND DATE(mp.return_date) BETWEEN $fromDate AND $toDate";


		// IF(iad.adj_qty>0, iad.adj_qty, 0) as inqty, IF(iad.adj_qty<0, ABS(iad.adj_qty), 0) as qty, -1 `adjust`,
		// Adjustments
		$query[] = "SELECT ia.adjust_date as cutoff_date, iad.item_code as item,
			0 as inqty, 0 as qty, (orig_qty + adj_qty) `adjust`,iad.expiry_date `expiry`,
			IF( adj_reason_name IS NOT NULL,
				CONCAT('adjustment',' (',IF(iad.reason='TO',CONCAT('TRANSFER TO PHS - ',CAST(fn_get_mun_name(iad.mun_nr) AS binary)),adj_reason_name),')'),'adjustment') area,
				ia.refno as refno, modify_dt AS create_date
		FROM seg_inventory_adjustment as ia
			INNER JOIN seg_inventory_adjustment_details as iad ON ia.refno=iad.refno
			INNER JOIN seg_inventory_adjustment_reason AS dr ON dr.adj_reason_id=iad.reason
		WHERE
			ia.area_code=$area
			AND iad.item_code=$item
			AND DATE(ia.adjust_date) BETWEEN $fromDate AND $toDate
			AND (iad.orig_qty + iad.adj_qty)>=0";


		// Equipment use
		$query[]="SELECT eo.order_date as cutoff_date, eoi.equipment_id as item,
			0 as inqty, eoi.number_of_usage as qty,-1 `adjust`,NULL `expiry`,
			'equipment use' as area, eo.refno as refno, modified_date AS create_date
		FROM seg_equipment_orders as eo
			INNER JOIN seg_equipment_order_items as eoi ON eo.refno=eoi.refno
		WHERE
			eo.area=$area
			AND eoi.equipment_id=$item
			AND DATE(eo.order_date) BETWEEN $fromDate AND $toDate";


		// deliveries
		$query[]= "SELECT d.receipt_date as cutoff_date, dd.item_code as
			item,dd.item_qty as inqty,0 as qty,-1 `adjust`,dd.expiry_date `expiry`,
			IF(d.supplier_id=0,'delivery',
				CONCAT(IF(fn_get_supplier_name(d.supplier_id) IS NULL,'delivery',
					CONCAT('delivery (',fn_get_supplier_name(d.supplier_id),')')),''
				)
			) area,
			d.refno as refno, modify_dt AS create_date
		FROM seg_delivery as d
			INNER JOIN seg_delivery_details as dd ON d.refno=dd.refno
			INNER JOIN seg_unit as u ON dd.unit_id=u.unit_id
		WHERE
			d.area_code=$area
			AND dd.item_code=$item
			AND DATE(d.receipt_date) BETWEEN $fromDate AND $toDate";


		// issuances
		$query[] = "SELECT ish.issue_date as cutoff_date, isd.item_code as item,
			IF(ish.area_code=$area, isd.item_qty,0) as inqty,
			IF(ish.src_area_code=$area, isd.item_qty,0) as inqty,
			-1 `adjust`,NULL `expiry`,
			a.area_name as area, ish.refno as refno, acknowledge_date AS create_date
		FROM seg_issuance as ish
			INNER JOIN seg_issuance_details as isd ON ish.refno=isd.refno
			INNER JOIN seg_unit as u ON isd.unit_id=u.unit_id
			INNER JOIN seg_areas as a ON ish.src_area_code=a.area_code
		WHERE
			(ish.area_code=$area OR ish.src_area_code=$area)
			AND isd.item_code=$item
			AND isd.status=2
			AND DATE(ish.issue_date) BETWEEN $fromDate AND $toDate";


		// conversion
		$query[] = "SELECT ic.convert_date as cutoff_date, ic.item_deduct as item,
			IF(ic.item_add=$item,ic.d_qty,0) as inqty,
			IF(ic.item_deduct=$item,ic.d_qty,0) as qty,
			-1 `adjust`,NULL `expiry`,
			'conversion' as area, ic.refno as refno, 0 as unit_price
		FROM seg_item_conversion as ic
		WHERE
			ic.area_code=$area
			AND (ic.item_add=$item OR ic.item_add=$item)
			AND DATE(ic.convert_date) BETWEEN $fromDate AND $toDate";

		$this->sql .= "(". implode(")\nUNION ALL\n(", $query) . ")\n".
			") f\n" .
			"INNER JOIN care_pharma_products_main as ppm ON f.item=ppm.bestellnum\n" .
			"INNER JOIN seg_item_extended as sie ON f.item=sie.item_code\n".
			"INNER JOIN seg_unit as su2 ON sie.pc_unit_id=su2.unit_id\n".
			"ORDER BY f.cutoff_date";


		if (($this->result=$db->Execute($this->sql)) !== false) {
			return $this->result->GetRows();
		}
		else{
			return false;
		}
	}



    function getMovementinAreaForSC ($area, $item=null, $extra=0, $fromdate=null, $todate=null) {
        global $db;
//        $db->debug = true;
		# edited by VAN 01-20-2011
		#fix the query.. copy the query in PHS
		#set a temporary date to filter only those request not earlier of the set value
		#clear inventory table only and pharma order is not included
		#get the first end of date stock
		#echo "ss ".$fromdate;
		$sql_eod = "SELECT eod_date FROM seg_eod_inventory WHERE item_code='".$item."' ORDER BY eod_date LIMIT 1";
		$rs_eod = $db->Execute($sql_eod);

		if (is_object($rs_eod)){
			$row_eod = $rs_eod->FetchRow();
			$tempdate = date("Y-m-d",strtotime($row_eod['eod_date']));
		}

		 $fromdate = date("Y-m-d",strtotime($fromdate));
		 #echo "s = ".$tempdate;
		 if (empty($tempdate))
					$tempdate = date("Y-m-d");

		 if ($fromdate) {
			 if ($tempdate < $fromdate)
					$tempdate = date("Y-m-d",strtotime($fromdate));
		 }

		 if ($tempdate){
					$fromdate = $tempdate;
			}
		 #echo $fromdate;

		 if (($area=='XRAY') || ($area=='RADIO') || ($area=='USD'))
				$ref_source = 'RD';
		 else
				$ref_source = $area;

        if($extra==0){

				$this->sql = " select DISTINCT ppm.artikelname,f.cutoff_date,su2.unit_name,f.inqty as inqty,f.qty as outqty,f.adjust, f.area, f.refno, f.create_date
        FROM 
		(SELECT su.create_tm as cutoff_date, sud.item_code as item,
			0 as inqty, sud.qty_used as qty, -1 `adjust`,
			'usage' as area, su.refno as refno , modify_tm AS create_date
    FROM seg_service_usage as su
				INNER JOIN seg_service_usage_details as sud ON su.refno=sud.refno
				INNER JOIN seg_unit as u ON sud.unit_id=u.unit_id
				WHERE u.is_unit_per_pc = 1 AND su.ref_source='$ref_source' AND sud.item_code=" .$db->qstr($item). "
    UNION ALL (
		SELECT su.create_tm as cutoff_date, sud.item_code as item,
		0 as inqty, sud.qty_used * (ie.qty_per_pack) as qty, -1 `adjust`,
		'usage' as area, su.refno as refno, modify_tm AS create_date
    FROM seg_service_usage as su
				INNER JOIN seg_service_usage_details as sud ON su.refno=sud.refno
				INNER JOIN seg_unit as u ON sud.unit_id=u.unit_id
				INNER JOIN seg_item_extended as ie ON sud.item_code=ie.item_code
				WHERE u.is_unit_per_pc = 0 AND su.ref_source='$ref_source' AND sud.item_code=" .$db->qstr($item). "
    )
    UNION ALL (
		select mp.chrge_dte as cutoff_date, mpd.bestellnum as item,
		0 as inqty, mpd.quantity as qty, -1 `adjust`,
		'billing misc' as area, mp.refno as refno,modify_dt AS create_date
    FROM seg_more_phorder as mp
				INNER JOIN seg_more_phorder_details as mpd ON mp.refno=mpd.refno
				INNER JOIN seg_inventory AS i ON i.item_code=mpd.bestellnum AND i.area_code=mpd.area_code
				WHERE mpd.area_code='$area' AND mp.chrge_dte >= '$fromdate'
    )
    UNION ALL (
		select po.orderdate as cutoff_date, poi.bestellnum as item,
		0 as inqty, poi.quantity as qty,-1 `adjust`,
		CONCAT('issuance',' - ',ordername,
										IF((SELECT p.or_no
						FROM seg_pay AS p
						INNER JOIN seg_pay_request AS d ON d.or_no=p.or_no
						WHERE p.pid=po.pid AND ref_source='PH' LIMIT 1),
										CONCAT(' : ',(SELECT p.or_no
						FROM seg_pay AS p
						INNER JOIN seg_pay_request AS d ON d.or_no=p.or_no
						WHERE p.pid=po.pid AND ref_source='PH' LIMIT 1),'')
										,'')) AS AREA,
		po.refno as refno,modify_time AS create_date
    FROM seg_pharma_orders as po
        LEFT JOIN seg_pharma_order_items as poi ON po.refno=poi.refno
				INNER JOIN seg_inventory AS i ON i.item_code=poi.bestellnum AND i.area_code=po.pharma_area
        WHERE po.pharma_area='$area' AND poi.serve_status='S'
				AND poi.bestellnum='$item'
				AND po.orderdate >= '$fromdate'
    )
    UNION ALL (
				SELECT DISTINCT mp.return_date AS cutoff_date, mpd.bestellnum AS item,
				mpd.quantity AS inqty,0 AS qty,-1 `adjust`,
				'return meds' AS AREA, mp.return_nr AS refno,modify_time AS create_date
				FROM seg_pharma_returns AS mp
				INNER JOIN seg_pharma_return_items AS mpd ON mp.return_nr=mpd.return_nr
				INNER JOIN seg_inventory AS i ON i.item_code=mpd.bestellnum AND i.area_code=mp.pharma_area
				WHERE mp.pharma_area='$area'
				AND mpd.bestellnum='$item'
				AND mp.return_date >= '$fromdate'
    )
    UNION ALL (
		select ia.adjust_date as cutoff_date, iad.item_code as item,
		0 as inqty, 0 as qty, (orig_qty + adj_qty) `adjust`,
		IF(adj_reason_name IS NOT NULL,
				CONCAT('adjustment',' (',IF(iad.reason='TO',CONCAT('TRANSFER TO PHS - ',CAST(fn_get_mun_name(iad.mun_nr) AS binary)),adj_reason_name),')'),'adjustment') as area,
				ia.refno as refno, modify_dt AS create_date FROM seg_inventory_adjustment as ia
				INNER JOIN seg_inventory_adjustment_details as iad ON ia.refno=iad.refno
				INNER JOIN seg_inventory_adjustment_reason AS dr ON dr.adj_reason_id=iad.reason
				WHERE ia.area_code='$area'
    )
    UNION ALL (
		select eo.order_date as cutoff_date, eoi.equipment_id as item,
		0 as inqty, eoi.number_of_usage as qty,-1 `adjust`,
		'equipment order' as area, eo.refno as refno, modified_date AS create_date FROM seg_equipment_orders as eo
				INNER JOIN seg_equipment_order_items as eoi ON eo.refno=eoi.refno
				WHERE eo.area='$area'
    )
    UNION ALL (
    select d.receipt_date as cutoff_date, dd.item_code as 
		item,dd.item_qty as inqty,0 as qty,-1 `adjust`,
		IF(d.supplier_id=0,'delivery',
				 CONCAT(IF(fn_get_supplier_name(d.supplier_id) IS NULL,'delivery',
									CONCAT('delivery (',fn_get_supplier_name(d.supplier_id),')')),''
								)
			) AS area,
		d.refno as refno, modify_dt AS create_date FROM seg_delivery as d
				INNER JOIN seg_delivery_details as dd ON d.refno=dd.refno
				INNER JOIN seg_unit as u ON dd.unit_id=u.unit_id
        WHERE d.area_code='$area' 
            AND u.is_unit_per_pc = 1
    )
    UNION ALL (
		select d.receipt_date as cutoff_date, dd.item_code as item,
		dd.item_qty * (ie.qty_per_pack) as inqty,0 as qty,-1 `adjust`,
		IF(d.supplier_id=0,'delivery',
				 CONCAT(IF(fn_get_supplier_name(d.supplier_id) IS NULL,'delivery',
									CONCAT('delivery (',fn_get_supplier_name(d.supplier_id),')')),''
								)
			) AS area,
		d.refno as refno, modify_dt AS create_date FROM seg_delivery
    as d
				INNER JOIN seg_delivery_details as dd ON d.refno=dd.refno
				INNER JOIN seg_unit as u ON dd.unit_id=u.unit_id
				INNER JOIN seg_item_extended as ie ON dd.item_code=ie.item_code
        WHERE d.area_code='$area'
            AND u.is_unit_per_pc = 0 
    )
    UNION ALL (
		select ish.issue_date as cutoff_date, isd.item_code as item,
		isd.item_qty as inqty,0 as qty,-1 `adjust`,
		a.area_name as area, ish.refno as refno, acknowledge_date AS create_date
    FROM seg_issuance as ish
				INNER JOIN seg_issuance_details as isd ON ish.refno=isd.refno
				INNER JOIN seg_unit as u ON isd.unit_id=u.unit_id
				INNER JOIN seg_areas as a ON ish.src_area_code=a.area_code
				WHERE ish.area_code='$area'
				AND isd.status = 2
            AND u.is_unit_per_pc = 1
    )
    UNION ALL (
		select ish.issue_date as cutoff_date, isd.item_code as item,
		isd.item_qty * (ie.qty_per_pack) as inqty,0 as qty,-1 `adjust`,
		a.area_name as area, ish.refno as refno, acknowledge_date AS create_date
    FROM seg_issuance as ish
				INNER JOIN seg_issuance_details as isd ON ish.refno=isd.refno
				INNER JOIN seg_unit as u ON isd.unit_id=u.unit_id
				INNER JOIN seg_item_extended as ie ON isd.item_code=ie.item_code
				INNER JOIN seg_areas as a ON ish.src_area_code=a.area_code
				WHERE ish.area_code='$area'
				AND isd.status = 2
            AND u.is_unit_per_pc = 0
    )
    UNION ALL (
		select ish2.issue_date as cutoff_date, isd2.item_code as item,
		0 as inqty,isd2.item_qty as qty,-1 `adjust`,
		a.area_name as area, ish2.refno as refno, acknowledge_date AS create_date
    FROM seg_issuance as ish2
				INNER JOIN seg_issuance_details as isd2 ON ish2.refno=isd2.refno
				INNER JOIN seg_unit as u ON isd2.unit_id=u.unit_id
				INNER JOIN seg_areas as a ON ish2.area_code=a.area_code
        WHERE ish2.src_area_code='$area' 
				AND isd2.status = 2
            AND u.is_unit_per_pc = 1
    )
    UNION ALL (
		select ish2.issue_date as cutoff_date, isd2.item_code as item,
		0 as inqty,isd2.item_qty * (ie.qty_per_pack) as qty,-1 `adjust`,
		a.area_name as area, ish2.refno as refno, acknowledge_date AS create_date
    FROM seg_issuance as ish2
				INNER JOIN seg_issuance_details as isd2 ON ish2.refno=isd2.refno
				INNER JOIN seg_unit as u ON isd2.unit_id=u.unit_id
				INNER JOIN seg_item_extended as ie ON isd2.item_code=ie.item_code
				INNER JOIN seg_areas as a ON ish2.area_code=a.area_code
        WHERE ish2.src_area_code='$area' 
				AND isd2.status = 2
            AND u.is_unit_per_pc = 0
    )

		UNION ALL (
		select ic.convert_date as cutoff_date, ic.item_deduct as item,
		0 as inqty,ic.d_qty as qty, -1 `adjust`,
		'conversion' as area, ic.refno as refno, 0 as unit_price
		FROM seg_item_conversion as ic
				INNER JOIN seg_areas as a ON ic.area_code=a.area_code
				WHERE ic.area_code='$area'
		)
		UNION ALL (
		select ic2.convert_date as cutoff_date, ic2.item_add as	item,
		ic2.a_qty as inqty,0 as qty,-1 `adjust`,
		'conversion' as area, ic2.refno as refno, 0 as unit_price
		FROM seg_item_conversion as ic2
				INNER JOIN seg_areas as a ON ic2.area_code=a.area_code
				WHERE ic2.area_code='$area'
		)

    ) as f
		INNER JOIN care_pharma_products_main as ppm ON f.item=ppm.bestellnum
		INNER JOIN seg_item_extended as sie ON f.item=sie.item_code
		INNER JOIN seg_unit as su2 ON sie.pc_unit_id=su2.unit_id
		INNER JOIN seg_inventory AS i ON i.item_code=ppm.bestellnum AND area_code='$area'
        ";
        }
        else {
						$this->sql = "select ppm.artikelname,ppm.bestellnum,f.cutoff_date,su2.unit_name,SUM(f.inqty) as inqty,SUM(f.qty) as outqty, f.area, f.refno FROM
							 (select /*DATE(su.served_date)*/ su.create_tm as cutoff_date, sud.item_code as item,0 as inqty, sud.qty_used as qty, 'usage' as area, su.refno as refno
    FROM seg_service_usage as su
				INNER JOIN seg_service_usage_details as sud ON su.refno=sud.refno
				INNER JOIN seg_unit as u ON sud.unit_id=u.unit_id
				WHERE u.is_unit_per_pc = 1 AND su.ref_source='$ref_source' AND DATE(su.served_date)>='$fromdate' AND DATE(su.served_date)<='$todate'
    UNION ALL (
		select /*DATE(su.served_date)*/ su.create_tm as cutoff_date, sud.item_code as item,0 as inqty,
		sud.qty_used * (ie.qty_per_pack) as qty, 'usage' as area, su.refno as refno
    FROM seg_service_usage as su
				INNER JOIN seg_service_usage_details as sud ON su.refno=sud.refno
				INNER JOIN seg_unit as u ON sud.unit_id=u.unit_id
				INNER JOIN seg_item_extended as ie ON sud.item_code=ie.item_code
				WHERE u.is_unit_per_pc = 0 AND su.ref_source='$ref_source' AND DATE(su.served_date)>='$fromdate' AND DATE(su.served_date)<='$todate'
    )
    UNION ALL (
		select mp.chrge_dte as cutoff_date, mpd.bestellnum as item,0 as inqty,
		mpd.quantity as qty, 'billing misc' as area, mp.refno as refno,modify_dt AS create_date
    FROM seg_more_phorder as mp
				INNER JOIN seg_more_phorder_details as mpd ON mp.refno=mpd.refno
				INNER JOIN seg_inventory AS i ON i.item_code=mpd.bestellnum AND i.area_code=mpd.area_code
				WHERE mpd.area_code='$area'
				AND mp.chrge_dte >= '$fromdate'
    )
    UNION ALL (
		select po.orderdate as cutoff_date, poi.bestellnum as item,0 as inqty,
		poi.quantity as qty,
		CONCAT('pharma dispensing',' - (',ordername,
										IF((SELECT p.or_no
						INNER JOIN seg_pay_request AS d ON d.or_no=p.or_no
						WHERE p.pid=po.pid AND ref_source='PH' LIMIT 1),
										CONCAT(' : ',(SELECT p.or_no
						FROM seg_pay AS p
						INNER JOIN seg_pay_request AS d ON d.or_no=p.or_no
						WHERE p.pid=po.pid AND ref_source='PH' LIMIT 1),'')
										,')')) AS AREA,
		po.refno as refno,modify_time AS create_date
    FROM seg_pharma_orders as po
        LEFT JOIN seg_pharma_order_items as poi ON po.refno=poi.refno
				INNER JOIN seg_inventory AS i ON i.item_code=poi.bestellnum AND i.area_code=po.pharma_area
				WHERE po.pharma_area='$area' AND poi.serve_status='S'
				AND poi.bestellnum='$item'
				AND po.orderdate >= '$fromdate'
		)
		UNION ALL (
				SELECT DISTINCT mp.return_date AS cutoff_date, mpd.bestellnum AS item,mpd.quantity AS inqty,
						0 AS qty, 'return meds' AS AREA, mp.return_nr AS refno,modify_time AS create_date
				FROM seg_pharma_returns AS mp
				INNER JOIN seg_pharma_return_items AS mpd ON mp.return_nr=mpd.return_nr
				INNER JOIN seg_inventory AS i ON i.item_code=mpd.bestellnum AND i.area_code=mp.pharma_area
				WHERE mp.pharma_area='$area'
				AND mpd.bestellnum='$item'
				AND mp.return_date >= '$fromdate'
    )
    UNION ALL (
    select DATE(ia.adjust_date) as cutoff_date, iad.item_code as item,0 as inqty, 
		abs(iad.adj_qty) as qty, 'adjustment' as area, ia.refno as refno
    FROM seg_inventory_adjustment as ia
				INNER JOIN seg_inventory_adjustment_details as iad ON ia.refno=iad.refno
        WHERE (iad.reason = 'D' OR (iad.reason = 'PC' AND iad.adj_qty < 0)) AND ia.area_code='$area' AND DATE(ia.adjust_date)>='$fromdate' AND DATE(ia.adjust_date)<='$todate'
    )
    UNION ALL (
    select DATE(eo.order_date) as cutoff_date, eoi.equipment_id as item,0 as 
		inqty, eoi.number_of_usage as qty, 'equipment order' as area, eo.refno as refno
    FROM seg_equipment_orders as eo
				INNER JOIN seg_equipment_order_items as eoi ON eo.refno=eoi.refno
        WHERE eo.area='$area' AND DATE(eo.order_date)>='$fromdate' AND DATE(eo.order_date)<='$todate'
    )
    UNION ALL (
    select DATE(ia.adjust_date) as cutoff_date, iad.item_code as 
		item,iad.adj_qty as inqty,0 as qty, 'adjustment' as area, ia.refno as refno
    FROM seg_inventory_adjustment as ia
				INNER JOIN seg_inventory_adjustment_details as iad ON ia.refno=iad.refno
        WHERE (iad.reason IN ('R','DO','F') OR (iad.reason = 'PC' AND iad.adj_qty > 0)) AND ia.area_code='$area' 
        AND DATE(ia.adjust_date)>='$fromdate' AND DATE(ia.adjust_date)<='$todate'
    )
    UNION ALL (
    select DATE(d.receipt_date) as cutoff_date, dd.item_code as 
		item,dd.item_qty as inqty,0 as qty,
		IF(d.supplier_id=0,'delivery',
				 CONCAT(IF(fn_get_supplier_name(d.supplier_id) IS NULL,'delivery',
									CONCAT('delivery (',fn_get_supplier_name(d.supplier_id),')')),''
								)
			) AS area,
		d.refno as refno
    FROM seg_delivery as d
				INNER JOIN seg_delivery_details as dd ON d.refno=dd.refno
				INNER JOIN seg_unit as u ON dd.unit_id=u.unit_id
        WHERE d.area_code='$area' 
            AND u.is_unit_per_pc = 1 AND DATE(d.receipt_date)>='$fromdate' AND DATE(d.receipt_date)<='$todate'
    )
    UNION ALL (
    select DATE(d.receipt_date) as cutoff_date, dd.item_code as 
		item,dd.item_qty * (ie.qty_per_pack) as inqty,0 as qty,
		IF(d.supplier_id=0,'delivery',
				 CONCAT(IF(fn_get_supplier_name(d.supplier_id) IS NULL,'delivery',
									CONCAT('delivery (',fn_get_supplier_name(d.supplier_id),')')),''
								)
			) AS area,
		d.refno as refno
    FROM seg_delivery 
    as d
				INNER JOIN seg_delivery_details as dd ON d.refno=dd.refno
				INNER JOIN seg_unit as u ON dd.unit_id=u.unit_id
				INNER JOIN seg_item_extended as ie ON dd.item_code=ie.item_code
        WHERE d.area_code='$area'
            AND u.is_unit_per_pc = 0 AND DATE(d.receipt_date)>='$fromdate' AND DATE(d.receipt_date)<='$todate'
    )
    UNION ALL (
    select DATE(ish.issue_date) as cutoff_date, isd.item_code as 
		item,isd.item_qty as inqty,0 as qty, a.area_name as area, ish.refno as refno
    FROM seg_issuance as ish
				INNER JOIN seg_issuance_details as isd ON ish.refno=isd.refno
				INNER JOIN seg_unit as u ON isd.unit_id=u.unit_id
				INNER JOIN seg_areas as a ON ish.src_area_code=a.area_code
				WHERE ish.area_code='$area'
				AND isd.status = 2
            AND u.is_unit_per_pc = 1 AND DATE(ish.issue_date)>='$fromdate' AND DATE(ish.issue_date)<='$todate'
    )
    UNION ALL (
    select DATE(ish.issue_date) as cutoff_date, isd.item_code as 
		item,isd.item_qty * (ie.qty_per_pack) as inqty,0 as qty, a.area_name as area, ish.refno as refno FROM seg_issuance as ish
				INNER JOIN seg_issuance_details as isd ON ish.refno=isd.refno
				INNER JOIN seg_unit as u ON isd.unit_id=u.unit_id
				INNER JOIN seg_item_extended as ie ON isd.item_code=ie.item_code
				INNER JOIN seg_areas as a ON ish.src_area_code=a.area_code
				WHERE ish.area_code='$area'
				AND isd.status = 2
            AND u.is_unit_per_pc = 0 AND DATE(ish.issue_date)>='$fromdate' AND DATE(ish.issue_date)<='$todate'
    )
    UNION ALL (
    select DATE(ish2.issue_date) as cutoff_date, isd2.item_code as 
		item,0 as inqty,isd2.item_qty as qty, a.area_name as area, ish2.refno as refno FROM seg_issuance as ish2
				INNER JOIN seg_issuance_details as isd2 ON ish2.refno=isd2.refno
				INNER JOIN seg_unit as u ON isd2.unit_id=u.unit_id
				INNER JOIN seg_areas as a ON ish2.area_code=a.area_code
        WHERE ish2.src_area_code='$area' 
				AND isd2.status = 2
            AND u.is_unit_per_pc = 1 AND DATE(ish2.issue_date)>='$fromdate' AND DATE(ish2.issue_date)<='$todate'
    )
    UNION ALL (
    select DATE(ish2.issue_date) as cutoff_date, isd2.item_code as 
		item,0 as inqty,isd2.item_qty * (ie.qty_per_pack) as qty, a.area_name as area, ish2.refno as refno
    FROM seg_issuance as ish2
				INNER JOIN seg_issuance_details as isd2 ON ish2.refno=isd2.refno
				INNER JOIN seg_unit as u ON isd2.unit_id=u.unit_id
				INNER JOIN seg_item_extended as ie ON isd2.item_code=ie.item_code
				INNER JOIN seg_areas as a ON ish2.area_code=a.area_code
        WHERE ish2.src_area_code='$area' 
				AND isd2.status = 2
            AND u.is_unit_per_pc = 0 AND DATE(ish2.issue_date)>='$fromdate' AND DATE(ish2.issue_date)<='$todate'
    )

		UNION ALL (
		select ic.convert_date as cutoff_date, ic.item_deduct as
		item,0 as inqty,ic.d_qty as qty, 'conversion' as area, ic.refno as refno, 0 as unit_price
		FROM seg_item_conversion as ic
				INNER JOIN seg_areas as a ON ic.area_code=a.area_code
				WHERE ic.area_code='$area' AND DATE(ic.convert_date)>='$fromdate' AND DATE(ic.convert_date)<='$todate'
		)
		UNION ALL (
		select ic2.convert_date as cutoff_date, ic2.item_add as
		item,ic2.a_qty as inqty,0 as qty, 'conversion' as area, ic2.refno as refno, 0 as unit_price
		FROM seg_item_conversion as ic2
				INNER JOIN seg_areas as a ON ic2.area_code=a.area_code
				WHERE ic2.area_code='$area' AND DATE(ic2.convert_date)>='$fromdate' AND DATE(ic2.convert_date)<='$todate'
		)

    ) as f
		INNER JOIN care_pharma_products_main as ppm ON f.item=ppm.bestellnum
		INNER JOIN seg_item_extended as sie ON f.item=sie.item_code
		INNER JOIN seg_unit as su2 ON sie.pc_unit_id=su2.unit_id
		INNER JOIN seg_inventory AS i ON i.item_code=ppm.bestellnum AND area_code='$area'
            ";  
        }       
        
        $where = array();
        
        if($item){
            $where[]="ppm.bestellnum='$item'";
        }
        
				if ($todate)
					$cutoff_where = " AND f.cutoff_date >= '$fromdate' ";

        if($extra==0){
            if ($where)
								$this->sql .= "WHERE (".implode(") AND (",$where).") $cutoff_where  ORDER BY cutoff_date";
            else $this->sql .= " ORDER BY cutoff_date";
        }
        else{
            if ($where)
								$this->sql .= " WHERE (".implode(") AND (",$where).") $cutoff_where  GROUP BY ppm.bestellnum ORDER BY cutoff_date";
						else $this->sql .= " GROUP BY ppm.bestellnum ORDER BY ppm.artikelname";
        }        

        if ($this->result=$db->Execute($this->sql)) {
        if ($this->count=$this->result->RecordCount()){
            return $this->result;
         }else{
                return FALSE;
         }
        }else{
            return FALSE;
        }
    }
    
    function getUnitPriceOfItemInDelivery ($item, $refno) {
        global $db;
        
        $this->sql = "select unit_price from seg_delivery_details as dd join seg_delivery as d ON dd.refno=d.refno
                        WHERE dd.item_code='$item' and d.refno='$refno'";
        $this->result = $db->Execute($this->sql);
       
        $this->row = $this->result->FetchRow();
        
        return $this->row['unit_price'];     
    }
    
		#added by VAN 01-27-2011
		#get the stock on hand for last month
		function getStockAtHand($item_code, $area){
		global $db;

		$this->sql="SELECT SUM(qty) AS current_inventory FROM seg_inventory
								WHERE item_code='$item_code' AND area_code='$area'";

		$result=$db->Execute($this->sql);
			if($result){
					return $result->FetchRow();
			}else return false;
	}


    private function sum($array)
    {
        if (empty($array))
            return 0;
        else
            return intval($array[0]['movespeed']) + $this->sum(array_slice($array, 1));
    }

    public function getFastSlowMoving(Array $params)
    {
        global $db;
        $data = array();
//        $db->debug = true;
        list($fromDate, $toDate, $toPercent, $fromPercent) = array(
            date('Y-m-d', strtotime($params['from_date'])),
            date('Y-m-d', strtotime($params['to_date'])),
            intval($params['to_percent']),
            intval($params['from_percent']),
        );

//        $db->debug = true;
        $this->sql = $db->Prepare("SELECT c.artikelname, bestellnum, COUNT(item_qty) AS movespeed
                FROM seg_issuance_details as a
                JOIN seg_issuance as b ON a.refno=b.refno
                JOIN care_pharma_products_main as c ON a.item_code=c.bestellnum");
        $where = array();

        if (array_key_exists('from_date', $params)) {
            $where[] = "b.issue_date>=" . $db->qstr($fromDate);
        }

        if (array_key_exists('to_date', $params)) {
            $where[] = "b.issue_date<=" . $db->qstr($toDate);
        }

        if (array_key_exists('area', $params)) {
            $where[] = "b.src_area_code=" . $db->qstr($params['area']);
        }

        if ($where)
            $this->sql .= " WHERE (" . implode(") AND (", $where) . ") GROUP BY bestellnum ORDER BY COUNT(bestellnum) DESC";
        else
            $this->sql .= " GROUP BY bestellnum ORDER BY COUNT(bestellnum) DESC";

        $result = $db->Execute($this->sql);
        $items = $result->GetArray();
        $total = $this->sum($items);

        if ($total > 0) {
            foreach ($items as $row) {

                $account = array();
                $collection = array();
                $a_types = explode("\n", $row['a_types']);
                foreach ($a_types as $i => $type) {
                    $type_arr = explode('|', $type);
                    if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
                    if (!in_array($type_arr[0], $account)) $account[] = $type_arr[0];
                    if (!in_array($type_arr[1], $collection)) $collection[] = $type_arr[1];
                }

                $percent = ($row['movespeed'] / $total) * 100;

                if ($percent >= $fromPercent && $percent <= $toPercent) {

                    $data[] = array(
                        'item_code' => $row['bestellnum'],
                        'artikelname' => $row['artikelname'],
                        'movespeed' => $row['movespeed'],
                        'percent' => number_format($percent, 2)
                    );
                }

            }


        }
        return $data;
    }

    public function getDailyReplenishment(Array $params)
    {
        global $db;
//        $db->debug = true;

        if (array_key_exists('as_of_date', $params)) {
            $asOfDate = date('Y-m-d', strtotime($params['as_of_date']));
        }

        $this->sql = 'SELECT
                  b.issue_date,
                  (SELECT
                    d.area_name
                  FROM
                    seg_areas AS d
                  WHERE d.area_code = b.src_area_code) AS src_area,
                  c.bestellnum,
                  c.artikelname,
                  a.item_qty,
                  e.unit_name,
                  a.status,
                  (SELECT
                    f.area_name
                  FROM
                    seg_areas AS f
                  WHERE f.area_code = b.area_code) AS dest_area
                FROM
                  (
                    seg_issuance_details AS a
                    JOIN seg_issuance AS b
                      ON a.refno = b.refno
                  )
                  JOIN care_pharma_products_main AS c
                    ON a.item_code = c.bestellnum
                  JOIN seg_unit AS e
                    ON a.unit_id = e.unit_id';


        $where = array();

        if (array_key_exists('as_of_date', $params)) {
            $where[] = "date(issue_date) = " . $db->qstr($asOfDate);
        }
        $where[] = "b.area_code = " . $db->qstr($params['area']);

        if ($where)
            $this->sql .= " WHERE (" . implode(") AND (", $where) . ") ";

        $this->sql .= 'UNION
                  (SELECT
                    g.receipt_date,
                    "-delivery-",
                    j.bestellnum,
                    j.artikelname,
                    i.item_qty,
                    k.unit_name,
                    g.remarks,
                    h.area_name
                  FROM
                    seg_delivery AS g
                    JOIN seg_areas AS h
                      ON g.area_code = h.area_code
                    JOIN seg_delivery_details AS i
                      ON g.refno = i.refno
                    JOIN seg_unit AS k
                      ON i.unit_id = k.unit_id
                    JOIN care_pharma_products_main AS j
                      ON i.item_code = j.bestellnum';

        $where2 = array();

        if (array_key_exists('as_of_date', $params)) {
            $where2[] = "date(receipt_date) = " . $db->qstr($asOfDate);
        }
        $where2[] = "g.area_code = " . $db->qstr($params['area']);

        if ($where2)
            $this->sql .= " WHERE (" . implode(") AND (", $where2) . ") ";

        $this->sql .= ')';

        $result = $db->Execute($this->sql);
        if ($result) {
            $data = array();

            while ($row = $result->FetchRow()) {
                $account = array();
                $collection = array();
                $a_types = explode("\n", $row['a_types']);
                foreach ($a_types as $i => $type) {
                    $type_arr = explode('|', $type);
                    if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
                    if (!in_array($type_arr[0], $account)) $account[] = $type_arr[0];
                    if (!in_array($type_arr[1], $collection)) $collection[] = $type_arr[1];
                }
                if ($row['status'] == '1') $stat = 'cancelled';
                else if (($row['status'] == '2')) $stat = 'approved';
                else if (($row['status'] == '0')) $stat = 'issued';
                else $stat = $row['status'];

                $data[] = array(
                    'issue_date' => date("m/d/Y", strtotime($row['issue_date'])),
                    'dest_area' => $row['dest_area'],
                    'src_area' =>$row['src_area'],
                    'item_code' =>$row['bestellnum'],
                    'artikelname' =>$row['artikelname'],
                    'item_qty' =>number_format($row['item_qty'], 0, '.', ','),
                    'unit_name' => $row['unit_name'],
                );
            }
            return $data;
        }

        return false;
    }

    public function getOxygenUtilization($params) {
        global $db;
        $data = array();

        $this->sql = "SELECT seo.order_date, si.serial_no, seo.patient_name, seoi.number_of_usage
                FROM seg_equipment_orders AS seo
                LEFT JOIN seg_equipment_order_items AS seoi ON seo.refno = seoi.refno
                LEFT JOIN seg_inventory AS si ON si.serial_no = seoi.serial_no
                WHERE seoi.equipment_id='OT' ";

        if (array_key_exists('serial_number', $params)) {
            $this->sql .= " AND seoi.serial_no=" . $db->qstr($params['serial_number']);
        }

        if (array_key_exists('from_date', $params)) {
            $params['from_date'] .= ' 23:59:59';
            $this->sql  .= " AND (seo.order_date >= " . $db->qstr($params['from_date']);
        }

        if (array_key_exists('to_date', $params)) {
            $params['to_date'] .= ' 23:59:59';
            $this->sql .= " AND (seo.order_date <= " . $db->qstr($params['to_date']);
        }

        $this->sql  .= " ORDER BY serial_no, seo.order_date ASC";

        $result=$db->Execute($this->sql );

        $serial = "";
        $total = 0;
        if ($result) {
            while ($row=$result->FetchRow()) {
                if(empty($serial) || $serial != $row['serial_no'] ){
                    $serial = $row['serial_no'];
                    $sql2 = "SELECT SUM(seoi.number_of_usage) + si.qty AS total
                     FROM seg_equipment_orders AS seo, seg_inventory AS si, seg_equipment_order_items AS seoi
                     WHERE seo.refno = seoi.refno AND seoi.equipment_id = si.item_code
                     AND seoi.equipment_id='OT' AND si.serial_no=seoi.serial_no AND seoi.serial_no='".$serial."'";
                    $rs2=$db->Execute($sql2);
                    if($rs2 && $row2=$rs2->FetchRow())
                        $total = $row2["total"];
                    $usage=0;
                }
                else
                    $total = $total - $usage;

                $data[]=array(
                    'order_date' => $row['order_date'],
                    'serial_no' => $row['serial_no'],
                    'patient_name' => strtoupper($row['patient_name']),
                    'initial' => $total,
                    'initial_sign' => "",
                    'final' => $total - $row['number_of_usage'],
                    'final_sign' => "",
                    'consumed' => $row['number_of_usage']
                );
                $usage=$row['number_of_usage'];
            }
        }
        else {
            print_r($this->sql);
            print_r($db->ErrorMsg());
            exit;
        }
        return $data;
    }

    public function getStockCard($itemCode, $areaCode, $dateFrom, $dateTo, $beginQty = 0, $beginCost = 0)
    {
        global $db;

        $stockCard = new StockCard();
        $req_obj = new Request();
        $sku_obj = new SKUInventory();

        $delivery_info = $req_obj->getDeliveryInfo($itemCode);

//        $db->debug = true;
        $this->sql = 'SELECT  sil.packqty, sku.sku_id, sku.expiry_date, sku.unit_id, tr_code, post_uid, post_date, tr_date, tref_no,
                      mvmnt_qty, sil.unit_cost, prev_cost, sil.prev_qty, (mvmnt_qty * packqty)  movement, (IF(tr_code = "RCV" AND IFNULL(sdd.`qty_per_pck`, 0) > 1, (((mvmnt_qty / IFNULL(sdd.`qty_per_pck`, 0)) * sil.unit_cost) / mvmnt_qty), sil.unit_cost) * ABS(mvmnt_qty)) cost,
                      IFNULL(sdd.`qty_per_pck`, 0) qtyperpack
        FROM seg_inventory_ledger sil
        INNER JOIN seg_sku_catalog sku ON sil.sku_id = sku.sku_id
        INNER JOIN seg_areas a ON sku.area_code = a.area_code
        LEFT JOIN `seg_delivery_details` sdd ON sdd.refno = sil.tref_no AND sku.item_code = sdd.item_code
        WHERE sku.`item_code` = ? AND tr_date BETWEEN DATE(?) AND DATE(?)
        AND sku.`area_code` =  ? AND tr_code NOT IN ("UPK") 
        ORDER BY post_date, post_uid';

           // print_r($this->sql);die();
        $this->result = $db->Execute($this->sql, array($itemCode, $dateFrom, $dateTo, $areaCode));
   
        $data = array();
        $prevQty = 0;
        if($this->result) {
            $result = $this->result->getArray();

            $currCost = $beginCost * $beginQty;
            $currQty = $beginQty;
            $totalQty = $beginQty;
            $avgCost = $beginCost;
            $prev_totalQty = 0;
            foreach($result as $k => $val) {
                $movement = intval($val['movement']);
                //echo 'move:' .$movement;

                
                $prev_totalQty = $totalQty;
                $totalQty += $movement;
                $isSource = $movement > 0 ? 1 : 0;

                #comment out by raymond
                // if($movement < 0) {
                //     $currCost -= $avgCost*abs($movement);
                //      // die($currCost);
                // } else if(in_array($val['tr_code'], array('RCV', 'ADJ', 'ISS')))
                //     $currCost += $val['cost'];
                // else {
                //     $currCost += $avgCost*abs($movement);
                // }
               
                if($val['tr_code'] == 'RCV'|| $val['tr_code'] == 'CNL' || $val['tr_code'] == 'RET' || ($val['tr_code']=='ISS'&& $movement>0) || ($val['tr_code']=='ADJ' && $movement>=0)){
                	#following the equation : Prev Total Cost + Total Received = New Total Cost
                	$currCost += $val['cost'];
                }
                else{
                	#following the equation : Prev Total Cost - Total Issued = New Total Cost
                	$currCost -= $val['cost'];	
                }



                $currQty += $movement;

                if ($avgCost == 0 && $movement > 0) {
                    $currCost = $val['cost'];
                    $avgCost = $currCost / $currQty;
                }else {
                    $avgCost = $currCost / $currQty;
                    $currCost = $avgCost * $currQty;
                }

                if($currCost == 0) {
                    $avgCost = 0;
                }
                $particulars = "";
                $note = "";

                $cost = $this->getCost($val['tr_code'], $val['tref_no'], $itemCode);

                if($val['tr_code']=="RCV" || ($val['tr_code']=='ADJ' && $movement>=0)){
                	$del_info = $this->getDeliveryInfo($val['tref_no'], $itemCode, $val['mvmnt_qty']);
                	if($del_info){
                		$curl_obj = new Rest_Curl();
                		$supplier = json_decode($curl_obj->getSupplier($del_info['supplier_id']), true); 
                		$particulars.="\n".$supplier[1]."(Expire: ".$del_info['expiry_date'].", Lot #: ".$del_info['lot_no'].", Invoice #: ".$del_info['invoice_no'].", Serial #: ".$del_info['serial_no'].", Supplier : ".$del_info['manufacturer'].")";
                		$lot_no = $del_info['lot_no'];
                		$supplier_desc = $del_info['manufacturer'];
                		$serial = $del_info['serial_no'];
                		if($del_info['is_fg']){
                			$note = " (Free Goods)"; 
                			$cost = 0;
                		}
                	}
                }
                elseif(($val['tr_code']=='ISS') || ($val['tr_code']=='ADJ' && $movement<0)){
                	$lot_no = $delivery_info[0]['lot_no'];
                	$supplier_desc = $delivery_info[0]['manufacturer'];
                	$serial = $delivery_info[0]['serial_no'];
                	$expiry = $delivery_info[0]['expiry_date'];
                	$invoice = $delivery_info[0]['invoice_no'];
                	$particulars.="\n".$supplier[1]."(Expire: ".$expiry.", Lot #: ".$lot_no.", Invoice #: ".$invoice.", Serial #: ".$serial.", Supplier : 1 ".$supplier_desc.")";
                }
                else{
                	$lot_no = '';
                	$supplier_desc = '';
                	$serial = '';
                	$expiry = '';
                	$invoice = '';
                	$particulars.= '';
                }

              	//modified by julz  

                if($val['tr_code']=="RCV" || 
                	($val['tr_code']=='ADJ' && $movement>=0) ||
                	($val['tr_code']=='ISS'&& $movement>0) || 
                	$val['tr_code']=="CNL" ||
                	$val['tr_code']=="RET"
            	){
            		if($val['tr_code'] == 'RCV' && $val['qtyperpack'] > 1){
	                	$inUnit = (($movement / $val['qtyperpack']) * $val['unit_cost']) / $movement;  
	                }
                	else
                		$inUnit = $val['unit_cost'];

                	$outunit = 0;
                }else{
                	$inUnit = 0;
                	$outunit = $val['unit_cost'];
                }

                $rcvqty = $movement > 0 ? abs($movement) : 0;
                $issueqty = $movement < 0 ? abs($movement) : 0;
               	
                if($val['tr_code']=='ADJ'){
                	// $adjCost = number_format($sku_obj->getItemAvgCost($itemCode,'',$areaCode),2);
                	
                	// $adjData = $sku_obj->getLatestAdjustInfo($itemCode,$areaCode,$val['tref_no']);
                	// // var_dump($adjData);die;
                	// if($adjData){
                	// 	$unitcost = doubleval($adjData['ucost']);
                	// 	$prevcost = doubleval($adjData['pcost']);
                	
                	// 	if($unitcost == $prevcost){
                	// 		$adjCost = number_format($unitcost,2);
                	// 	}
                	// 	else if($unitcost == 0){
                	// 		$adjCost = number_format($prevcost,2);
                	// 	}
                	// 	else if($unitcost > 0){
                	// 		$adjCost = number_format($unitcost,2);
                	// 	}
						 
                	// 	$adjQty = intval($adjData['qty']);

                	// 	$adjRefno = $adjData['refno'];
                	// 	// if($adjRefno == $val['tref_no']){

                			if($totalQty <=0){
                				$currCost = 0;
                			}
                	// // 		$totalQty = $adjQty+($totalQty-$adjQty);
                			// $currCost = $totalQty*$val['unit_cost'];

                			// $avgCost = $currCost / $totalQty;

                	// 	// }
                	// }
                }

                $data[] = array(
                    'tr_date' => $val['tr_date'],
                    'tref_no' => $val['tref_no'],
                    'trdesc' => $stockCard->getFormattedTransactionCode($val['tref_no'], $itemCode, $val['tr_code'], $isSource, true).$note,
                    'in_qty' => abs($rcvqty),#$movement > 0 ? abs($movement) : 0, #rcv qty
                   'in_cost' => $movement >= 0 ? number_format($rcvqty*$inUnit,2) : 0,#$movement > 0 ? number_format($val['unit_cost']*$movement,2) : 0, #rcv total
                    'out_qty' => abs($issueqty),#$movement < 0 ? abs($movement) : 0, #issue qty
                    'in_unit' => $inUnit, #rcv cost
                    'out_unit' => $outunit, #issue cost
                    'qty'=> $movement,
                    'cost'=> number_format($cost, 2),//number_format(($val['cost']/$movement),2),
                    'out_cost' => $movement < 0 ? number_format($issueqty*$outunit,2):0,#$movement < 0 ? number_format($val['cost'],2) : 0, #issue total
                    'netqty' => number_format($totalQty,0),
                    'unit_costs' => $sku_obj->stockcardAvgcost($val['tref_no'], $dateFrom,$areaCode,''),
                    'avg_cost' =>  $totalQty >= 0 ? number_format($avgCost,2) : '0.00',
                    'totalcost' => $totalQty >= 0 ? number_format(ABS($currCost),2) : '0.00', #number_format(($cost * abs($movement)),2),//number_format($val['cost'],2),
                    'particular'=>$particulars,
                    'lot_no'=>$lot_no,
                    'supplier'=>$supplier_desc,
                	'serial'=>$serial,
                );
            }
        }
  //      die;
        return $data;
    }

    function getCost($tr_code, $tr_no, $item_code){
    	global $db;

    	switch(strtoupper($tr_code)){
    		case 'SLE':
    			$sql = 'SELECT price_orig FROM seg_pharma_order_items 
    							WHERE bestellnum = '.$db->qstr($item_code).
    								" AND refno = ".$db->qstr($tr_no);
    			$currQty = $db->GetOne($sql);
    			if(!$currQty){
    				$sql = 'SELECT unit_price FROM seg_more_phorder_details 
    							WHERE refno = '.$db->qstr($tr_no).
    								" AND bestellnum = ".$db->qstr($item_code);
    			}else{
    				return $currQty;
    			}
    		break;
    		 case 'RCV':
    		 	$sql = "SELECT unit_price FROM seg_delivery_details 
    		 				WHERE refno = ".$db->qstr($tr_no).
    		 					" AND item_code = ".$db->qstr($item_code);
    		 break;
    	}

    	if($sql){
    		return $db->GetOne($sql);
    	}else{
    		return 0;
    	}
    }

    function getDeliveryInfo($refno, $item_code, $qty){
    	global $db;

    	$this->sql = "SELECT 
					  d.`expiry_date`,
					  d.`lot_no`,
					  d.`serial_no`,
					  h.`invoice_no`,
					  h.`supplier_id`,
					  d.`is_fg`,  
					  (d.`item_qty` * d.`qty_per_pck`) quantity,
					  manufacturer 
					FROM
					  seg_delivery_details d 
					  LEFT JOIN seg_delivery h 
					    ON h.`refno` = d.`refno` 
					WHERE d.`refno` = ".$db->qstr($refno)." 
					  AND d.`item_code` = ".$db->qstr($item_code);
					  //" HAVING quantity = ".$db->qstr($qty);


		if($this->result=$db->Execute($this->sql)){
			return $this->result->FetchRow();
		}

		return false;
    }

    public function reOrderingPoint($having, $date, $area){
    	global $db;
    	
    	if($having){
    		$sql = " HAVING qty <= ".$db->qstr($having);
    	}else{
    		$sql = " HAVING qty <= re_order_point ";
    	}

    	$this->sql = "SELECT 
						  SUM(sil.`mvmnt_qty` * sil.`packqty`) qty,
						  ssc.item_code,
						  (SELECT 
						    artikelname 
						  FROM
						    care_pharma_products_main 
						  WHERE bestellnum = ssc.`item_code`) item,
						  (SELECT 
						    is_deleted  
						  FROM
						    care_pharma_products_main 
						  WHERE bestellnum = ssc.`item_code`) deleted,
						  IFNULL((SELECT IFNULL(min_qty, 1) FROM seg_item_extended WHERE item_code = ssc.`item_code`),1) re_order_point  
						FROM
						  seg_inventory_ledger sil 
						  LEFT JOIN seg_sku_catalog ssc 
						    ON (ssc.`sku_id` = sil.`sku_id` AND ssc.area_code = ".$db->qstr($area).")  
						WHERE DATE(sil.`tr_date`) <= DATE(".$db->qstr($date).")  
						GROUP BY ssc.`item_code` 
						".$sql." AND deleted <> 1
						ORDER BY item ";
						
		if($this->result=$db->Execute($this->sql)){
			return $this->result;
		}

		return false;
    }
}
  
