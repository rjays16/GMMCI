<?php
require_once('./roots.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');

#this class will be extended by other inventory items
class Item extends SegPharmaProduct {
     #created by Bryan
    /*
    *code key for the item
    * @var String
    */
    var $item_code;
    /*
    *description for the item
    * @var String
    */
    var $item_desc;
    /*
    *item type for the item
    * @var String
    */
    var $item_type;
    /*
    *
    * @var Double
    */
    var $total_inventory;
    /*
    *
    * @var Double
    */
    var $minimum_qty;
    /*
    *
    * @var Category
    */
    var $category;    
    /*
    *
    * @var Double
    */         
    function getAvgCost($item_code = '', $refdate = '') {
        $skuobj = new SKUInventory();                        
        $nCost = $skuobj->getItemAvgCost($item_code, $refdate);        
        
//        global $db;
//        
//        $refdate = (strcmp($refdate, '0000-00-00 00:00:00') == 0) ? strftime("%Y-%m-%d %H:%M:%S") : $refdate;  
//        
//        $this->sql = "select fn_getavgcost('$item_code', '$refdate') as avg_cost";
//        $nCost = 0;
//        if ($this->result = $db->Execute($this->sql)) {
//            if ($this->result->RecordCount()) {
//                $row = $this->result->FetchRow();
//                $nCost = is_null($row['avg_cost']) ? 0 : $row['avg_cost'];
//            }
//        }        
        
        return $nCost;
    }
    
    function getPcUnit($item_code) {
        global $db;
        
        $this->sql = "SELECT pc_unit_id FROM seg_item_extended WHERE item_code = '$item_code'";
        $pc_unitid = 0;
        if ($this->result = $db->Execute($this->sql)) {
            if ($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                $pc_unitid = is_null($row['pc_unit_id']) ? 0 : $row['pc_unit_id'];
            }
        }        
        
        return $pc_unitid;        
    }
    
    /**
    * Updated By Jarel
    * Use Prapare statements
    */
    function getQtyPerBigUnit($item_code='') {
        global $db;
        
        if ($item_code == '') $item_code = $this->item_code;
        $qty = 1;   // Default qty per pack 
        $this->sql = $db->Prepare("SELECT qty_per_pack FROM seg_item_extended WHERE item_code = ? ");         
        if ($result = $db->GetOne($this->sql,array($item_code))) {
            $qty = is_null($result) ? 1 : $result ;
        }
        return $qty;                
    }  
    
    function getMinQty($item_code='') {
        global $db;
        
        if ($item_code == '') $item_code = $this->item_code;
        
        $this->sql = "select min_qty 
                         from seg_item_extended 
                         where item_code = '$item_code'";
        $mqty = 0;
        if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                $mqty = is_null($row['min_qty']) ? 0 :  $row['min_qty'];
            }            
        } 
        
        return $mqty;            
    } 
    
    function getItemDesc($item_code='') {
        global $db;
        
        if ($item_code == '') $item_code = $this->item_code;
        
        $this->sql = "select artikelname 
                         from care_pharma_products_main 
                         where bestellnum = '$item_code'";
        $itemdesc = 'No Name';
        if($this->result=$db->Execute($this->sql)) {
            if($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                $itemdesc = is_null($row['artikelname']) ? '' :  $row['artikelname'];
            }            
        } 
        
        return $itemdesc;         
    }
    
    /***
     * This routine returns the smaalest unit of measure of given item '$itmcode'
     * and passes it back to calling routine.
     * 
     * @author  LST
     * @params String   $itmcode
     * @params integer  $small_unitid
     *
     */    
    function getItemSmallUnitID($itmcode, &$small_unitid, &$small_unitname) {
        global $db;
        
        $small_unitid = DEFAULT_UNIT;
        $small_unitname = "piece(s)";
        
        $this->sql = "SELECT 
                            su.unit_id,
                            unit_name 
                        FROM
                            seg_unit su 
                            INNER JOIN seg_item_extended i 
                                ON su.`unit_id` = i.`pc_unit_id` 
                        WHERE i.`item_code` = '{$itmcode}'";
                                                                       
        $result = $db->Execute($this->sql);
        if ($result) {            
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                if ($row) {
                    $small_unitid = is_null($row['unit_id']) ? DEFAULT_UNIT : $row['unit_id'];
                    $small_unitname = is_null($row['unit_name']) ? "piece(s)" : $row['unit_name'];
                }
            }
        }        
        return;                        
    }

    function getSuppPrice($item_code){
        global $db;

        $this->sql = "SELECT p.supplier_price FROM care_pharma_products_main p WHERE p.bestellnum = ".$db->qstr($item_code);

        return $db->GetOne($this->sql);
    }
}
?>