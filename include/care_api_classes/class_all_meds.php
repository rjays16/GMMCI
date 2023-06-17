<?php
# created by janjan 08/18/2015
# inventory get all meds simple one query

require_once('./roots.php'); 

class SegAllMeds extends Core{

    // var $tb_issue = "seg_areas";
    
    function getAllMedsInventory($asofdate,$item){
        global $db;
        $result = array();
        // $this->sql = "SELECT 
        //                 prod.generic,
        //               led.mvmnt_qty,
        //               areas.area_name
        //             FROM
        //                 care_pharma_products_main AS prod,
        //               seg_sku_catalog AS cat,
        //               seg_inventory_ledger AS led,
        //               seg_areas AS areas
        //             WHERE   cat.item_code = prod.bestellnum AND cat.sku_id = led.sku_id AND led.post_date <= '$asofdate' AND cat.area_code = areas.area_code
        //             ORDER BY prod.generic";
        if($item != '')
            $item = " AND cat.item_code = '$item' ";
        else
            $item = '';
        $this->sql = "SELECT 
                        prod.artikelname,
                      led.mvmnt_qty,
                      areas.area_name
                    FROM
                            seg_inventory_ledger AS led
                        INNER JOIN seg_sku_catalog AS cat
                            ON cat.sku_id = led.sku_id 
                        INNER JOIN care_pharma_products_main AS prod
                            ON prod.is_deleted = 0 ".$item." AND cat.item_code = prod.bestellnum
                        INNER JOIN seg_areas AS areas
                            ON cat.area_code = areas.area_code
                    WHERE led.tr_date <= '$asofdate'
                    ORDER BY prod.artikelname";
        $this->result = $db->Execute($this->sql);
        
        if($this->result) {
            $result = $this->result->getArray();
            return $result;
        }
        else
            return "false";
        
        
    }

    function getArtikelName($item){
        global $db;
        
        $this->sql = "SELECT artikelname FROM care_pharma_products_main WHERE bestellnum='$item'";
        $this->result = $db->Execute($this->sql);
        $row = $this->result->FetchRow();
        
        return $row['artikelname'];     
    }

    // function getAreaName($area){
    //     global $db;
        
    //     $this->sql = "SELECT area_name FROM seg_areas WHERE area_code='$area'";
    //     $this->result = $db->Execute($this->sql);
    //     $row = $this->result->FetchRow();
        
    //     return $row['area_name'];     
    // } 

    // function getInventoryAreas() {
    //     global $db;
    //     $result = array();
    //     $this->sql = "SELECT inv.area_code, ar.area_name
    //                   FROM seg_inventory_areas inv
    //                   INNER JOIN seg_areas ar ON ar.area_code = inv.area_code";
    //     $this->result = $db->Execute($this->sql);

    //     if($this->result) {
    //       $result = $this->result->getArray();
    //     }

    //     return $result;
    // }

}
?>
