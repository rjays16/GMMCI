<?php

require("./roots.php");
require_once($root_path.'include/care_api_classes/inventory/class_item.php');
class MedicalSupply extends Item {
     #created by Bryan
    /*
    * assigns medical supply type (S) for item
    */
    var $item_type = "S";
    /*
    *
    * @var Date
    */
    var $expiry_date;
    /*
    *
    * @var Iventory[]
    */
    var $inventory;

    const MEDICAL_SUPPLY_TYPE = 2;

    function getInventory(){
        /*
        * must return Inventory[]
        */
        return $this->inventory;
    }

    /*
     * Get list of medical supplies based on date and department.
     */
    public function getSuppliestList($asOf = null, $areaCode) {
        global $db;

        if(!isset($asOf))
            return array();

        if($asOf !== '') {
            $asOf = date('Y-m-d', strtotime($asOf));
        }
        $this->sql = $db->Prepare('SELECT
                      sku.`item_code`, m.`artikelname`,
                      SUM(mvmnt_qty * packqty) qty
                    FROM
                      seg_inventory_ledger sel INNER JOIN (seg_sku_catalog sku INNER JOIN care_pharma_products_main m
                      ON m.`bestellnum` = sku.`item_code`) ON sel.`sku_id` = sku.`sku_id`
                    WHERE tr_date <= ?
                    AND sku.`area_code` = ?
                    AND m.prod_class IN ("S")
                    AND sel.tr_code NOT IN ("UPK")
                    GROUP BY sku.`item_code`
                    HAVING SUM(mvmnt_qty * packqty) > 0
                    ORDER BY m.`artikelname`');

        $this->result = $db->Execute($this->sql, array($asOf, $areaCode));
        if($this->result) {
            $data = array();
            $index = 0;
            while(true) {
                $row = $this->result->FetchRow();
                if(!$row)
                    break;
                $data[$index]['item_code'] = $row['item_code'];
                $data[$index]['artikelname'] = $row['artikelname'];
                $data[$index]['qty'] = intval($row['qty']);
                $index++;
           }

        }
        return $data;
    }

    public function getMedsList($asOf = null, $areaCode) {
        global $db;

        if(!isset($asOf))
            return array();

        if($asOf !== '') {
            $asOf = date('Y-m-d', strtotime($asOf));
        }
        $this->sql = $db->Prepare('SELECT
                      sku.`item_code`, m.`artikelname`,
                      SUM(mvmnt_qty * packqty) qty
                    FROM
                      seg_inventory_ledger sel INNER JOIN (seg_sku_catalog sku INNER JOIN care_pharma_products_main m
                      ON m.`bestellnum` = sku.`item_code`) ON sel.`sku_id` = sku.`sku_id`
                    WHERE tr_date <= ?
                    AND sku.`area_code` = ?
                    AND m.prod_class IN ("M")
                    AND sel.tr_code NOT IN ("UPK")
                    GROUP BY sku.`item_code`
                    HAVING SUM(mvmnt_qty * packqty) > 0
                    ORDER BY m.`artikelname`');

        $this->result = $db->Execute($this->sql, array($asOf, $areaCode));
        if($this->result) {
            $data = array();
            $index = 0;
            while(true) {
                $row = $this->result->FetchRow();
                if(!$row)
                    break;
                $data[$index]['item_code'] = $row['item_code'];
                $data[$index]['artikelname'] = $row['artikelname'];
                $data[$index]['qty'] = intval($row['qty']);
                $index++;
           }

        }
        return $data;
    }
}
?>

