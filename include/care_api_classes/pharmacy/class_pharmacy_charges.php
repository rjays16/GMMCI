<?php

require_once("./roots.php");
require_once($root_path.'include/care_api_classes/class_core.php');

class PharmacyCharges extends Core {

	public function __construct() {
		$this->setupLogger();
	}

	/**
	* put your comment there...
	*
	* @param mixed $options
	*/
	public function getConfinementCharges($options) {
		global $db;
		$defaults = array(
			'encounter' => null,
			'fromDate' => null,
			'toDate' => null,
			'type' => 'M'
		);

		// fetch previous encounter
		//$previousEncounter = $db->GetOne('SELECT parent_encounter FROM care_encounter WHERE encounter_nr=' . $db->qstr($options['encounter']));

		$conditions = array();

		if (is_array($options['encounter'])) {
			$encounters = $options['encounter'];
			foreach ($encounters as $i => $v) {
				$encounters[$i] = $db->qstr($v);
			}
			$conditions[] = "o.encounter_nr IN (". implode(',', $encounters) . ")";
		}
		else {
			$conditions[] = "o.encounter_nr=".$db->qstr($options['encounter']);
		}

		$conditions[] = "oi.serve_status='S'";
		$conditions[] = "NOT o.is_cash";
		$conditions[] = "o.order_date BETWEEN ".$db->qstr($options['fromDate']) . " AND " . $db->qstr($options['toDate']);
		$conditions[] = "oi.request_flag IS NULL OR t.is_excludedfrombilling";

		$this->setQuery("SELECT oi.refno, DATE(o.orderdate) `serve_date`, TIME(o.orderdate) `serve_time`\n" .
				"oi.bestellnum `service_code`, oi.quantity, oi.pricecharge `price`\n".
			"FROM seg_pharma_order_items oi\n" .
			"INNER JOIN seg_pharma_orders o ON oi.refno=o.refno\n".
			"INNER JOIN care_pharma_products_main p ON oi.bestellnum=p.bestellnum AND p.prod_class=".$db->qstr($options['type'])."\n".
			"LEFT JOIN seg_type_charge t ON t.id=oi.request_flag\n".
			"WHERE\n".
			"(" . implode(")\n AND (", $conditions) . ")\n");

		if (($result=$db->Execute($this->getQuery())) === false) {
			return false;
		}
		$data = $result->GetRows();
		foreach ($data as $i=>$item) {
			$this->setQuery(
				"SELECT SUM(quantity)\n".
				"FROM seg_pharma_return_items ri\n".
				"WHERE\n".
					"ri.ref_no=" . $db->qstr($item['refno']) . "\n".
					"ri.bestellnum=" . $db->qstr($item['service_code']) . "\n".
				"GROUP BY ri.bestellnum\n"
			);
			$data[$i]['returns'] = (int) $db->GetOne($this->getQuery());
			$data[$i]['net_quantity'] = (int) $data[$i]['quantity'] - (int) $data[$i]['returns'];
		}
		return $data;
	}
}