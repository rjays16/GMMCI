<?php
  
require("./roots.php");
require_once($root_path.'include/care_api_classes/inventory/class_item.php');
  
class Medicine extends Item {
     #created by Bryan
    /*
    * assigns medicine type (M) for item
    */
    var $item_type = "M";
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
    
    
    function getInventory(){
        /*
        * must return Inventory[]
        */
        return $this->inventory;
    }

    public function getMedicinesList($fromDate, $toDate, $areaCode, $noExpiry) {
        global $db;
        $data = array();
        $where = "WHERE  sku.`area_code` = '{$areaCode}' AND (sku.`expiry_date` BETWEEN '{$fromDate}' AND '{$toDate}')";

        if($noExpiry) {
            $where = "WHERE  sku.`area_code` = '{$areaCode}' AND (sku.`expiry_date` BETWEEN '{$fromDate}' AND '{$toDate}' OR sku.`expiry_date` = '0000-00-00')";
        }

        $this->sql = "SELECT
                    sku.`item_code`, ar.`area_name`, m.`artikelname`, SUM(mvmnt_qty * packqty) qty, sku.`expiry_date`
                    FROM
                    seg_inventory_ledger sel INNER JOIN (seg_sku_catalog sku INNER JOIN care_pharma_products_main m
                    ON m.`bestellnum` = sku.`item_code`) ON sel.`sku_id` = sku.`sku_id`
                    INNER JOIN seg_areas ar ON ar.area_code = sku.area_code
                    {$where}
                    GROUP BY sku.`sku_id`
                    HAVING SUM(mvmnt_qty * packqty) > 0
                    ORDER BY sku.`expiry_date` DESC";

        $this->result = $db->Execute($this->sql);
        if($this->result) {
            while($row = $this->result->FetchRow()) {
                $expiry_date = date('m/d/Y', strtotime($row['expiry_date']));
                if ($expiry_date == "01/01/1970") {
                    $expiry_date = "NO EXPIRY";
                }

                $data[] = array(
                    'area' => $row['area_name'],
                    'expiry_date' => $expiry_date,
                    'item_code' => $row['item_code'],
                    'artikelname' => $row['artikelname'],
                    'qty' => intval($row['qty']),
                );
            }

        }
        return $data;
    }
}
?>
