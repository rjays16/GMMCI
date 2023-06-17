<?php
require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegSponsor extends Core {
	
	function SegSponsor() {           
	}
	
	function getRequestInfo($source, $nr, $code) {
		global $db;
		switch (strtoupper($source)) {
			case "PH":
				$this->sql = "SELECT\n".
						"o.pid AS pid,\n".
						"p.artikelname AS `description`,\n".
						"oi.quantity AS `quantity`,\n".
						"oi.pricecash*oi.quantity AS `total`,\n".
						"oi.price_orig*oi.quantity AS `orig`,\n".
						"(SELECT SUM(ce.amount) FROM seg_cmap_entries_ph AS ce\n".
							"WHERE ce.ref_no=oi.refno AND ce.service_code=oi.bestellnum) AS `grant`\n".
					"FROM seg_pharma_order_items AS oi\n".
					"INNER JOIN seg_pharma_orders AS o ON o.refno=oi.refno\n".
					"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=oi.bestellnum\n".
					"WHERE oi.refno=".$db->qstr($nr)." AND oi.bestellnum=".$db->qstr($code)."\n";
			break;
			
			case "LD":
				$this->sql = "SELECT\n".
						"s.name AS `description`,\n".
						"l.pid AS pid,\n".
						"ld.quantity AS `quantity`,\n".
						"ld.price_cash*ld.quantity AS `total`,\n".
						"ld.price_cash_orig*ld.quantity AS `orig`,\n".
						"(SELECT SUM(ce.amount) FROM seg_cmap_entries_ld AS ce\n".
							"WHERE ce.ref_no=ld.refno AND ce.service_code=ld.service_code) AS `grant`\n".
					"FROM seg_lab_servdetails AS ld\n".
					"INNER JOIN seg_lab_serv AS l ON l.refno=ld.refno\n".
					"LEFT JOIN seg_lab_services AS s ON s.service_code=ld.service_code\n".
					"WHERE ld.refno=".$db->qstr($nr)." AND ld.service_code=".$db->qstr($code)."\n";
			break;
			
			case "RD":
				$this->sql = "SELECT\n".
						"s.name AS `description`,\n".
						"r.pid AS pid,\n".
						"1 AS `quantity`,\n".
						"rd.price_cash AS `total`,\n".
						"rd.price_cash_orig AS `orig`,\n".
						"(SELECT SUM(ce.amount) FROM seg_cmap_entries_rd AS ce\n".
							"WHERE ce.ref_no=rd.refno AND ce.service_code=rd.service_code) AS `grant`\n".
					"FROM care_test_request_radio AS rd\n".
					"INNER JOIN seg_radio_serv AS r ON r.refno=rd.refno\n".
					"LEFT JOIN seg_radio_services AS s ON s.service_code=rd.service_code\n".
					"WHERE rd.refno=".$db->qstr($nr)." AND rd.service_code=".$db->qstr($code)."\n";
			break;
			case "FB":
				$this->sql = "SELECT\n".
						"fn_get_person_lastname_first(e.pid) AS `description`,\n".
						"e.pid AS pid,\n".
						"1 AS `quantity`,\n".
						"fn_compute_bill(b.bill_nr) AS `total`,\n".
						"fn_compute_bill(b.bill_nr) AS `orig`,\n".
						"(SELECT SUM(ce.amount) FROM seg_cmap_entries_fb AS ce\n".
							"WHERE ce.ref_no=b.bill_nr AND ce.service_code=b.bill_nr) AS `grant`\n".
					"FROM seg_billing_encounter AS b\n".
					"INNER JOIN care_encounter AS e ON e.encounter_nr=b.encounter_nr\n".
					"WHERE b.bill_nr=".$db->qstr($nr)."\n";
			break;
		}

		if ($this->result = $db->GetRow($this->sql))
			return $this->result;
		else
			return false;
	}
	
	function getRequestDetails($source, $nr, $offset=0, $rowcount=10, $sort_sql="") {
		global $db;
		switch (strtoupper($source)) {
			case "PH":
				$this->sql = "SELECT oi.bestellnum AS `code`,".
						"p.artikelname AS `description`,".
						"oi.quantity AS `quantity`,".
						"oi.pricecash AS `discount`,".
						"oi.price_orig AS `price`\n".
					"FROM seg_pharma_order_items AS oi\n".
					"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=oi.bestellnum\n".
					"WHERE refno=".$db->qstr($nr)."\n";
			break;
		}

		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset,$rowcount";
		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}
	
	function getBillingEncounters($filters, $offset=0, $rowcount=10, $sort_sql="") {
		global $db;
		
		$this->sql = "SELECT b.bill_nr AS `nr`,b.bill_dte AS `date`,enc.pid\n".
			"FROM seg_billing_encounter AS b\n".
			"LEFT JOIN care_encounter AS enc ON enc.encounter_nr=b.encounter_nr\n";

		$where = array();
		foreach ($filters as $key=>$value) {
			switch(strtoupper($key)) {
				case 'PID':
					$where[] = "pid=".$db->qstr($value);
				break;
				case 'DATE':
					$where[] = "DATE(b.bill_dte)='".date("Y-m-d",strtotime($value))."'";
				break;
			}    
		}
		
		if ($where) $this->sql .= "WHERE (".implode(")\n AND (",$where).")\n";
		
		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount";
		
		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}
	
	function getRequestItems($filters, $offset=0, $rowcount=10, $sort_sql="") {
		global $db;

		$count=0;
		$sql=array();
		
		if (!$filters['DATE']) $filters['DATE']=date("Y-m-d");
		
		$this->sql = "";
		if (!$filters["EXCLUDE_PH"]) {
			$sql[$count] = "SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'PH' AS `source`,o.orderdate AS `date`,o.refno AS `refno`,\n".
				"oi.bestellnum AS `itemno`,p.artikelname AS `name`,oi.quantity AS `qty`,oi.price_orig*oi.quantity AS `total`,oi.pricecash*oi.quantity AS `discounted`\n".
				"FROM seg_pharma_order_items AS oi\n".
				"INNER JOIN seg_pharma_orders AS o ON o.refno=oi.refno\n".
				"LEFT JOIN care_pharma_products_main AS p ON p.bestellnum=oi.bestellnum\n";
				
			$where = array();
			
			$where[] = "o.is_cash=1";
			foreach ($filters as $key=>$value ) {
				switch($key) {
					case 'PID':
						$where[] = "o.pid=".$db->qstr($value);
					break;
					case 'DATE':
						$where[] = "DATE(o.orderdate)=".$db->qstr($value);
					break;
				}
			}
			
			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
			$count++;
		}

		if (!$filters["EXCLUDE_LD"]) {
			$sql[$count] = "SELECT".(!$count?" SQL_CALC_FOUND_ROWS":"")." 'LD' AS `source`,l.serv_dt AS `date`,l.refno AS `refno`,\n".
				"ld.service_code AS `itemno`,s.name AS `name`,1 AS `qty`,ld.price_cash AS `total`,ld.price_cash_orig As `discounted`\n".
				"FROM seg_lab_servdetails AS ld\n".
				"INNER JOIN seg_lab_serv AS l ON l.refno=ld.refno\n".
				"LEFT JOIN seg_lab_services AS s ON s.service_code=ld.service_code\n";
				
			$where = array();
			
			$where[] = "l.is_cash=1";
			foreach ($filters as $key=>$value ) {
				switch($key) {
					case 'PID':
						$where[] = "l.pid=".$db->qstr($value);
					break;
					case 'DATE':
						$where[] = "l.serv_dt=".$db->qstr($value);
					break;
				}
			}
			
			$sql[$count].="WHERE (".implode(")\nAND (", $where).")";
		}

		
		$this->sql = "(".implode(")\n UNION ALL (", $sql).")";
		if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
		$this->sql.="LIMIT $offset, $rowcount";
		
		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}
	
	function getRequests($filters, $offset=0, $rowcount=10, $sort_sql="") {
		global $db;
		$defs = array(
			'PH' => 
				array(
					'core' => 'seg_pharma_orders',
					'nr' => 'refno',
					'date' => 'orderdate',
					'pid' => 'pid'
				),
			'LD' => 
				array(
					'core' => 'seg_lab_serv',
					'nr' => 'refno',
					'date' => "CAST(CONCAT(serv_dt,' ',serv_tm) AS DATETIME)",
					'pid' => 'pid'
				),
			'RD' =>
				array(
					'core' => 'seg_radio_serv',
					'nr' =>  'refno',
					'date' => "CAST(CONCAT(request_date,' ',request_time) AS DATETIME)",
					'pid' => 'pid'
				)
		);

		$sel_request=array("LD","RD","PH","FB");
		$sel_request=array("LD","RD","PH");
		#$sel_request=array("FB");
		$i=0;
		foreach ($sel_request as $k) {
			if ($defs[$k]) {
				$count_rows_sql = ($i==0 ? "SQL_CALC_FOUND_ROWS" : "");
				$sql = "SELECT $count_rows_sql '$k' AS `source`,".
					"{$defs[$k]['nr']} AS `nr`,".
					"{$defs[$k]['date']} AS `date` ".
					"FROM {$defs[$k]['core']}\n"; 
				
				# Process filters
				$where = array();
				foreach ($filters as $key=>$value) {
					switch(strtoupper($key)) {
						case 'PID':
							$where[] = "{$defs[$k]['pid']}=".$db->qstr($value);
						break;
					}
				}
				if ($where)
				$sql .= "WHERE (".implode($where,') AND (').")\n";
				$queries[] = $sql;
				$i++;
			}
		}
		
		$this->sql = "(".implode(") UNION (", $queries).")";
		if ($this->sql) {
			if ($sort_sql) $this->sql.="ORDER BY $sort_sql\n";
			$this->sql.="LIMIT $offset, $rowcount";
		}
		
		if ($this->result = $db->Execute($this->sql))
			return $this->result;
		else
			return false;
	}
	
	function getGrantInfo($src, $refno, $item=FALSE) {
		global $db;
		
		if ($item) {
			switch(strtoupper($src)) {
				case 'PH':
					$this->sql = 
						"SELECT p.artikelname AS `description`,oi.quantity AS `quantity`,oi.pricecash AS `item_price`,o.orderdate AS `date`,\n".
							"(oi.price_orig * oi.quantity) AS `price`,\n".
							"(oi.pricecash * oi.quantity) AS `discount`\n".
							"FROM seg_pharma_order_items AS oi\n".
							"LEFT JOIN seg_pharma_orders AS o ON oi.refno=o.refno\n".
							"LEFT JOIN care_pharma_products_main AS p ON oi.bestellnum=p.bestellnum\n".
							"WHERE oi.refno=".$db->qstr($refno)." AND oi.bestellnum=".$db->qstr($item);
				break;
			}    
		}
		else {
			switch(strtoupper($src)) {
				case 'PH':
					$this->sql = 
						"SELECT o.orderdate AS `date`,\n".
							"(SELECT SUM(oi.price_orig*oi.quantity) FROM seg_pharma_order_items AS oi WHERE oi.refno=o.refno) AS `price`,\n".
							"(SELECT SUM(oi.pricecash*oi.quantity) FROM seg_pharma_order_items AS oi WHERE oi.refno=o.refno) AS `discount`,\n".
							"(SELECT SUM(g.amount) FROM seg_granted_request AS g WHERE g.ref_source='PH' AND g.ref_no=o.refno) AS `grants`\n".
							"FROM seg_pharma_orders AS o\n".
							"WHERE o.refno=".$db->qstr($refno);
				break;
				case '':
				break;
			}
		}

		if ($this->result = $db->GetRow($this->sql))
			return $this->result;
		else 
			return false;
	}
}