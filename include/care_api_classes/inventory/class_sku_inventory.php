<?php

/****
 * class SKUInventory
 *
 * Provides implementation of the use of sku ids in inventory management.
 *
 * @author Bong S. Trazo
 * @package care_api_classes/inventory
 * @version 1.0.0
 * 
 */

define('SKU_TABLE', 'seg_sku_catalog');
define('INV_TABLE','seg_inventory_ledger');

require_once('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/inventory/class_unit.php'); 

class SKUInventory extends Core {    

    private $bnewskuid;
    private $trCode;

    public function __construct() {
        
    }

    public function setTransactionCode($trCode)
    {
        $this->trCode = $trCode;
    }
    
    /***
     * Private function which generates a unique sku id.
     * 
     * @return string generated or found sku id.
     */
    private function genSkUId() {
        global $db;
        
        $skuid = '';
        $this->sql = "SELECT UUID() skuid";
        $result = $db->Execute($this->sql);
        if ($result) {            
            $row = $result->FetchRow();
            if ($row) {
                $skuid = $row['skuid'];                                
            }
        }
        return $skuid;
    }       
    
    /***
     * Private function which saves the sku id in sku catalog.
     * 
     * @param string $skuid
     * @param string $itemcode
     * @param string $areacode
     * @param date   $expirydate
     * @param string $serialno
     * @param int    $unitid
     * 
     * @return boolean TRUE if successful, FALSE otherwise
     */
    private function saveNewSKUId($skuid, $itemcode, $areacode, $expirydate = '', $serialno = '', $unitid = 0, $unitCost = 0, $lotNo = '') {
        $expirydate = (empty($expirydate)) ? '0000-00-00' : $expirydate;
        $this->sql = "INSERT INTO ".SKU_TABLE." (sku_id, item_code, area_code, expiry_date, serial_no, lot_no, unit_id, unit_cost)
                         VALUES ('{$skuid}', '{$itemcode}', '{$areacode}', '{$expirydate}', '{$serialno}', '{$lotNo}', '{$unitid}', {$unitCost})";                                                                                                   
        return $this->Transact($this->sql);
    }

    private function updateSkuUnitCost($skuId, $unitCost = 0, $packQty = 1) {
        global $db;
        $skuInfo = $this->getSKUInfo($skuId);
        if($skuInfo) {
            $unitId = $skuInfo['unit_id'];
            $unit = new Unit();
            $isBigUnit = $unit->isUnitIDBigUnit($unitId);
        }

        if($isBigUnit) {
            $unitCost = $unitCost / $packQty;
        }

        $this->sql = "UPDATE " . SKU_TABLE . " SET unit_cost = ? WHERE sku_id = ?";
        $saveOk = $db->Execute($this->sql, array($unitCost, $skuId));
        if($saveOk)
            return true;
        return false;
    }

    public function getAvgUnitCostOfSkus($skuIds) {
        global $db;
        $count = sizeof(explode(',', $skuIds));
        $this->result = $db->GetOne("SELECT SUM(unit_cost) total
                        FROM seg_sku_catalog
                        WHERE sku_id IN ({$skuIds})");
        if($this->result) {
            return $this->result/$count;
        }
        return 0;
    }
    
    function getPcUnit($item_code) {
        global $db;
        
        $this->sql = "SELECT pc_unit_id FROM seg_item_extended WHERE item_code = '$item_code'";
        $pc_unitid = 2;
        if ($this->result = $db->Execute($this->sql)) {
            if ($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                $pc_unitid = is_null($row['pc_unit_id']) ? 2 : $row['pc_unit_id'];
            }
        }        
        
        return $pc_unitid;        
    }
    /***
     * Public function which returns the sku id given the following:
     * 
     * @params string $itemcode
     * @params string $areacode
     * @params date   $expirydate
     * @params string $serialno
     * @params int    $unitid
     * 
     * @return string generated or found sku id or FALSE if error is encountered.
     */
    public function getSKUId($itemcode, $areacode, $expirydate = '', $serialno = '', $unitid = 0, $unitCost = 0, $lotNo = '') {
        global $db;

        $skuid = '';        
        $areacode = (!empty($areacode)) ? $areacode : "";
        $expirydate = (!empty($expirydate)) ? $expirydate : "0000-00-00";
        $serialno = (!empty($serialno)) ? $serialno : "";
        $unitid = (!empty($unitid)) ? $unitid : $this->getPcUnit($itemcode);
        $arrayFilter = array($itemcode,$areacode,$expirydate,$serialno,$unitid,$unitCost,$lotNo); 

        $this->sql = $db->Prepare("SELECT 
                        sku_id 
                     FROM 
                        ".SKU_TABLE." skc 
                     WHERE skc.`item_code` = ?
                       AND skc.`area_code` = ? 
                       AND skc.`expiry_date` = ?
                       AND skc.`serial_no` = ?
                       AND skc.`unit_id` = ?
                       AND skc.`unit_cost` = ?
                       AND skc.`lot_no` = ?
                     ORDER BY sku_id DESC");

        if($result = $db->GetOne($this->sql,$arrayFilter)){
            $skuid = $result;
        }
        
        // Generate an sku id if it doesn't exist yet ...
        if (empty($skuid)) {            
            while (empty($skuid)) {
                $skuid = $this->genSkUId();
            }                        
            // Save the new sku id in catalog ...
            $bsuccess = $this->saveNewSKUId($skuid, $itemcode, $areacode, $expirydate, $serialno, $unitid, $unitCost, $lotNo);                                    
            $this->bnewskuid = true;
        }
        else {
            $this->bnewskuid = false;
            $bsuccess = true;
        }
        
        return ($bsuccess) ? $skuid : false;
    }
    
    /***
     * 
     */
    public function isSKUIdExists($skuid) {
        global $db;
        
        $this->sql = "SELECT 
                        * 
                      FROM 
                        ".SKU_TABLE." 
                      WHERE sku_id = '{$skuid}'";                
        $result = $db->Execute($this->sql);
        if ($result) {            
            if ($result->RecordCount()) {
                return true;
            }
        }
        
        return false;                
    }
    
    /***
     * Public function which returns a string of concatenated sku ids filtered by the following:
     * 
     * @params string $itemcode
     * @params string $areacode
     * @params date   $expirydate
     * @params string $serialno
     * @params int    $unitid
     * 
     * @return string concatenated sku ids.
     */    
    public function getSKUIds($itemcode, $areacode = '', $expirydate = '', $serialno = '', $unitid = 0) {        
        global $db;
        
        $arrayFilter = array();
        $skuids = '';
        $sfilter = "item_code = ?";
        $arrayFilter[] = $itemcode;        
        if (!empty($areacode)) {
            $sfilter .= " AND ";
            $sfilter .= "area_code = ?";
            $arrayFilter[] = $areacode;
        }        
        if (!empty($expirydate)) {
            $sfilter .= " AND ";
            $sfilter .= "expiry_date = ? ";
            $arrayFilter[] = $expirydate;
        }
        if (!empty($serialno)) {
            $sfilter .= " AND ";
            $sfilter .= "serial_no = ? "; 
            $arrayFilter[] = $serialno;           
        }        
        if (!empty($unitid)) {
            $sfilter .= " AND ";
            $sfilter .= "unit_id = ? ";
            $arrayFilter[] = $unitid;            
        }
        
        $this->sql = $db->Prepare("SELECT 
                        sku_id 
                      FROM
                        ".SKU_TABLE." sku 
                      WHERE {$sfilter}
                      ORDER BY expiry_date DESC");

        // var_dump($arrayFilter);die($this->sql);
        /*
        if($result = $db->GetOne($this->sql,$arrayFilter)){
             $skuids = $result;
        }*/

        $result2 = $db->GetAll($this->sql, $arrayFilter);

        foreach ($result2 as $key) {
          $skuid[] = '"'.$key['sku_id'].'"';
        }

        if($skuid){
            $skuids = implode(",",$skuid);
        }
    // var_dump($skuids);die;
        
        return $skuids;
    }
    
    /***
     * Public function which returns a string of concatenated sku ids with inventory quantity filtered by the following:
     * 
     * @params string $itemcode
     * @params date   $refdate
     * @params string $areacode
     * @params date   $expirydate
     * @params string $serialno
     * @params int    $unitid
     * 
     * @return resultset of sku ids.
     */
    public function getSKUIdswInventory($itemcode, $refdate = '', $areacode = '', $expirydate = '', $serialno = '', $unitid = 0, $lotNo = '', $unitCost = 0) {
        global $db;

        $arrayFilter = array(); 
        
        $sfilter = "item_code = ? ";  
        $arrayFilter[] = $itemcode;      
        
        if (!empty($areacode)) {
            $sfilter .= " AND ";
            $sfilter .= "sku.area_code = ?";
            $arrayFilter[] = $areacode;  
        }
        
        if (empty($refdate)){
            $refdate = date("Y-m-d");
        }
        
        $sfilter .= " AND ";
        $sfilter .= "sil.`tr_date` <= ?";                                    
        $arrayFilter[] = $refdate;
        
        if (!empty($expirydate)) {
            $sfilter .= " AND ";
            $sfilter .= "sku.expiry_date = ?";
            $arrayFilter[] = $expirydate;  
        }
        if (!empty($serialno)) {
            $sfilter .= " AND ";
            $sfilter .= "sku.serial_no = ?";
            $arrayFilter[] = $serialno;  
        }        
        if (!empty($unitid)) {
            $sfilter .= " AND ";
            $sfilter .= "sku.unit_id = ?";
            $arrayFilter[] = $unitid;  
        }

        if (!empty($lotNo)) {
            $sfilter .= " AND ";
            $sfilter .= "sku.lot_no = ?";
            $arrayFilter[] = $lotNo;  
        }

        if (!empty($unitCost)) {
            $sfilter .= " AND ";
            $sfilter .= "sku.unit_cost = ?";
            $arrayFilter[] = $unitCost;  
        }
        
        $this->sql = $db->Prepare("SELECT 
                                    t.sku_id,
                                    (prev_qty + mvmnt_qty) rung_qty 
                                  FROM
                                    (SELECT 
                                      sil.* 
                                    FROM
                                      (seg_inventory_ledger sil 
                                        INNER JOIN seg_sku_catalog sku 
                                          ON sku.`sku_id` = sil.`sku_id`) 
                                      INNER JOIN seg_unit un 
                                        ON sku.unit_id = un.unit_id 
                                    WHERE {$sfilter}) t 
                                  ORDER BY tr_date DESC,
                                    post_date DESC,
                                    post_uid DESC 
                                  LIMIT 1");                          
        $result = $db->Execute($this->sql,$arrayFilter);                
        if ($result) {            
            if ($result->RecordCount()) {
                return $result;
            }
        }        
        return false;       
    }
    
    /***
     * Public function which returns the distinct expiry dates in inventory or in particular area.
     * 
     * @params string $itemcode
     * @params date   $refdate
     * @params string $areacode
     * 
     * @returns resultset expiry dates
     * Updated By Jarel
     * Optimize Query
     */    
    public function getExpiryDates($itemcode, $refdate = '', $areacode = '') {
        global $db;
        
        if (empty($refdate)) $refdate = date("Y-m-d");
        $arrayFilter = array();
        $arrayFilter[]=$refdate;
        $arrayFilter[] = $itemcode;
        $sfilter = "item_code = ? ";        
        if (!empty($areacode)) {
            $sfilter .= " AND ";
            $sfilter .= "area_code = ? ";
            $arrayFilter[]=$areacode;
        }

        $this->sql = $db->Prepare("SELECT 
                                          IF(
                                            SUM(mvmnt_qty * packqty) > 0,
                                            sku.expiry_date,
                                            NULL
                                          ) AS expiry_date 
                                        FROM
                                          seg_sku_catalog sku 
                                          INNER JOIN seg_inventory_ledger sel 
                                            ON sel.sku_id = sku.sku_id 
                                        WHERE DATE(tr_date) <= DATE( ? ) 
                                          AND {$sfilter} 
                                        GROUP BY expiry_date 
                                        ORDER BY expiry_date");
                  
        $result = $db->Execute($this->sql,$arrayFilter);
        if ($result) {            
            if ($result->RecordCount()) {
                return $result;
            }
        }        
        return false;             
    }
    
    /***
     * Public function to get the expiry dates of items with corresponding quantity in transaction identified by '$refno'.
     * 
     * @params string  $refno
     * @params string  $itemcode
     * @params string  $areacode
     * 
     * @returns resultset expiry dates with quantity.
     */
    public function getExpiryDateswithQtyinTrans($refno, $itemcode, $areacode, $unit_id = '') {
        global $db;
//                $db->debug = true;
        if (empty($refno)) return false;
        
        $arrayFilter = array();
        $sfilter = "item_code = ? ";
        $arrayFilter[] = $itemcode;

        $sfilter .= " AND tref_no = ? ";
        $arrayFilter[]= $refno;

        if (!empty($areacode)) {
            $sfilter .= " AND area_code = ? ";
            $arrayFilter[] = $areacode;
        }

        if (!empty($areacode)) {
            $sfilter .= " AND unit_id = ? ";
            $arrayFilter[] = $unit_id;
        }
                
        $this->sql = $db->Prepare("SELECT 
                                      sku.lot_no,
                                      sku.expiry_date,
                                      sku.serial_no,
                                      sku.unit_id,
                                      IF(
                                            SUM(ABS(mvmnt_qty) * packqty) > 0,
                                            SUM(ABS(mvmnt_qty) * packqty),
                                            NULL
                                          ) AS qty ,
                                      sku.unit_cost
                                    FROM
                                      seg_sku_catalog sku 
                                      INNER JOIN seg_inventory_ledger sel 
                                        ON sku.`sku_id` = sel.`sku_id` 
                                        AND tr_code IN ('SLE', 'ISS') 
                                    WHERE {$sfilter}
                                    ORDER BY expiry_date ");
                             
        $result = $db->Execute($this->sql,$arrayFilter);
        if ($result) {            
            if ($result->RecordCount()) {
                return $result;
            }
        }        
        return false;        
    }

    /***
     * Public function which returns the inventory quantity filtered by the following:
     * 
     * @params string $itemcode
     * @params date   $refdate
     * @params string $areacode
     * @params date   $expirydate
     * @params string $serialno
     * @params int    $unitid
     * 
     * @return double inventory quantity.
     */       
    public function getItemQty($itemcode, $refdate = '', $areacode = '', $expirydate = '', $serialno = '', $unitid = 0, $brecent = false) {
        global $db;
        
        $qty = doubleval(0);

        if (empty($refdate)) $refdate = date("Y-m-d");

        $skuids = $this->getSKUIds($itemcode, $areacode,'', $serialno, $unitid);
        // $dtefilter = ($brecent) ? "tr_date > DATE(?)" : "tr_date <= DATE(?)";
        if($expirydate=='1'){
            $dtefilter = "tr_date < DATE(?)";
        }else{
             $dtefilter = "tr_date <= DATE(?)";
        }
       
        $this->sql = $db->Prepare("SELECT 
                        SUM(mvmnt_qty * packqty) qty                         
                      FROM
                        ".INV_TABLE." sel 
                      WHERE sku_id IN (  $skuids ) 
                         AND {$dtefilter} AND tr_code != 'UPK'");
        // die($this->sql . $refdate);
        if($result = $db->GetOne($this->sql,array($refdate))){
            $qty = is_null($result) ? doubleval(0) : doubleval($result);

        }

        return $qty;        
    }

    
    public function getRecentItemQty($itemcode, $refdate = '', $areacode = '', $expirydate = '', $serialno = '', $unitid = 0) {        
        return $this->getItemQty($itemcode, $refdate, $areacode, $expirydate, $serialno, $unitid, TRUE);
    } 

    /***
     * Public function which returns the expiry date of the item identified by 'skuid'.
     * 
     * @params string $skuid
     * 
     * @return date   expiry
     */     
    public function getSKUExpiry($skuid) {
        global $db;
        
        $expiry = '0000-00-00';
        $this->sql = "SELECT 
                        expiry_date
                      FROM
                        ".SKU_TABLE." sku 
                      WHERE sku_id = '{$skuid}'";
                      
        $result = $db->Execute($this->sql);
        if ($result) {            
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                if ($row) {
                    $expiry = is_null($row['expiry_date']) ? '0000-00-00' : $row['expiry_date'];
                }
            }
        }
        
        return $expiry;        
    }
    
    /***
     * Public function which returns the current inventory count of the item identified by 'skuid'
     * 
     * @params string $skuid
     * @params date   $refdate
     * 
     * @return double inventory count
     * Updated By Jarel
     * Add Prepare Statements
     */       
    public function getSKUQty($skuid, $refdate = '') {
        global $db;
        
        $qty = 0;
        if (empty($refdate)) $refdate = date("Y-m-d");
        $this->sql = $db->Prepare("SELECT 
                        SUM(mvmnt_qty) qty 
                      FROM
                        ".INV_TABLE." sel 
                      WHERE sku_id = ?
                         AND tr_date <= DATE( ? )");
                      
        if($result = $db->GetOne($this->sql,array($skuid,$refdate))){
            $qty = is_null($result) ? 0 : $result;
        }
        
        return $qty;         
    }
    
    /***
     * Public function which returns the total inventory quantity given the list of SKU ids.
     * 
     * @param String  $skuids
     * @param Date    $refdate
     * @param boolean $brecent
     * 
     * @return double inventory count.
     */
    public function getQtyofSKUs($skuids, $refdate = '', $brecent = false) {
        global $db;

        $qty = doubleval(0);
        if (empty($refdate)) $refdate = date("Y-m-d");
        $dtefilter = ($brecent) ? "tr_date < DATE('{$refdate}')" : "tr_date <= DATE('{$refdate}')";
        $this->sql = "SELECT 
                        SUM(mvmnt_qty) qty 
                      FROM
                        ".INV_TABLE." sel 
                      WHERE sku_id IN ({$skuids}) 
                         AND {$dtefilter}";
                      
        $result = $db->Execute($this->sql);
        if ($result) {            
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                if ($row) {
                    $qty = is_null($row['qty']) ? 0 : $row['qty'];
                }
            }
        }
        
        return $qty;         
    }
    
    /***
     * Public function which returns the average cost given the list of SKU ids.
     * 
     * @param String $skuids
     * @param Date   $refdate
     * @param boolean $brecent
     * 
     * @return double average cost.
     */
    public function getAvgCostofSKUs($skuids, $refdate = '', $brecent = false) {
        global $db;
//        $db->debug = true;
        if (empty($refdate)) $refdate = date("Y-m-d");
        $dtefilter = ($brecent) ? "tr_date < DATE('{$refdate}')" : "tr_date <= DATE('{$refdate}')";                                                              
        $this->sql = "SELECT sel.* 
                      FROM ".INV_TABLE." sel 
                        INNER JOIN 
                          (SELECT sku_id,
                            (SELECT 
                              post_uid 
                            FROM
                              ".INV_TABLE." sel2 
                            WHERE sel2.sku_id = sel1.sku_id 
                               AND {$dtefilter}                            
                            ORDER BY tr_date DESC, post_date DESC 
                            LIMIT 1) postuid
                          FROM
                            ".INV_TABLE." sel1 
                          WHERE sku_id IN ({$skuids}) 
                          GROUP BY sku_id) t 
                          ON t.postuid = sel.`post_uid`
                          ORDER BY post_date";
        $result = $db->Execute($this->sql);

        if ($result) {

           return $this->getAvgCost($result->getArray());
        }

        return doubleval(0);
    }

    /***
     * Public function which returns the average cost given the list of SKU ids.
     *
     * @param String $skuids
     * @param Date   $refdate
     * @param boolean $brecent
     *
     * @return double average cost.
     */
    public function getSkuAvsCostByPostUid($postUid, $refdate = '') {
        global $db;

        if (empty($refdate)) $refdate = date("Y-m-d");
        $dtefilter = ($brecent) ? "tr_date < DATE('{$refdate}')" : "tr_date <= DATE('{$refdate}')";
        $this->sql = "SELECT sel.*
                      FROM ".INV_TABLE." sel
                        INNER JOIN
                          (SELECT sku_id,
                            (SELECT
                              post_uid
                            FROM
                              ".INV_TABLE." sel2
                            WHERE sel2.sku_id = sel1.sku_id
                               AND {$dtefilter}
                            ORDER BY tr_date DESC, post_date DESC
                            LIMIT 1) postuid
                          FROM
                            ".INV_TABLE." sel1
                          WHERE sku_id IN ({$skuids})
                          GROUP BY sku_id) t
                          ON t.postuid = sel.`post_uid`
                          ORDER BY post_date";
        $result = $db->Execute($this->sql);
        printR($skuids);
        if ($result) {
            return $this->getAvgCost($result->getArray());
        }

        return doubleval(0);
    }
    
    /***
     * Public function which returns the total quantity added or deducted from the inventory given the following:
     * 
     * @param  string  $skuids     - string of sku ids.
     * @param  date    $frmdate    - start date of period.
     * @param  date    $todate     - end date of period.
     * @param  boolean $b_outgoing - incoming by default, else outgoing.
     * 
     * @return double  total quantity.
     */
    public function getQtyInPeriod($skuids, $frmdate, $todate, $b_outgoing = false) {
        global $db;
        $qty = doubleval(0);
        $mvfilter = ($b_outgoing) ? "mvmnt_qty < 0" : "mvmnt_qty > 0";
        $this->sql = "SELECT 
                        SUM(ABS(mvmnt_qty)) tqty
                      FROM
                        seg_inventory_ledger sel 
                      WHERE sel.`sku_id` IN ({$skuids}) 
                        AND tr_date BETWEEN DATE('{$frmdate}') AND DATE('{$todate}') 
                        AND {$mvfilter}";
        
        $result = $db->Execute($this->sql);
        if($result) {
            $row = $result->FetchRow();
            $qty = is_null($row['tqty']) ? 0 : $row['tqty'];
            $cancelQty = $this->getCancelledQtyInPeriod($skuids, $frmdate, $todate);
        }
        return $qty - $cancelQty;
    }

    public function getCancelledQtyInPeriod($skuids, $frmdate, $todate) {
        global $db;
//        $db->debug = true;
        $this->sql = ("SELECT
                        SUM(ABS(mvmnt_qty)) cQty
                      FROM
                        seg_inventory_ledger sel
                      WHERE sel.`sku_id` IN ({$skuids})
                        AND tr_date BETWEEN DATE('{$frmdate}') AND DATE('{$todate}')
                        AND tr_code IN ('CNL', 'RET')");

        $result = $db->Execute($this->sql);
        if($result) {
            $row = $result->FetchRow();
            $cancelQty = $row['cQty'];
            return !is_null($cancelQty) ? intval($cancelQty) : 0;
        }

        return 0;
    }

    
    /***
     * Public function which returns the total cost added or deducted from the inventory given the following:
     * 
     * @param  string  $skuids     - string of sku ids.
     * @param  date    $frmdate    - start date of period.
     * @param  date    $todate     - end date of period.
     * @param  boolean $b_outgoing - incoming by default, else outgoing.
     * 
     * @return double  total cost.
     */
    public function getCostInPeriod($skuids, $frmdate, $todate, $b_outgoing = false) {
        global $db;
//        $db->debug = true;
        $tcost = doubleval(0);
        $mvfilter = ($b_outgoing) ? "mvmnt_qty < 0" : "mvmnt_qty > 0";
        $this->sql = "SELECT 
                        SUM(ABS(mvmnt_qty) * sk.unit_cost) tcost, SUM(ABS(mvmnt_qty)) qty
                      FROM
                        seg_inventory_ledger sel
                        INNER JOIN seg_sku_catalog sk ON sk.sku_id = sel.sku_id
                      WHERE sel.`sku_id` IN ({$skuids}) 
                        AND tr_date BETWEEN DATE('{$frmdate}') AND DATE('{$todate}') 
                        AND {$mvfilter}";
        
        $result = $db->Execute($this->sql);
        if ($result) {            
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                if ($row) {
                    $tcost = is_null($row['tcost']) ? 0 : $row['tcost'];
                }
            }
        }

        return round($tcost/$row['qty'], 2);
    }    
    
    /***
     * Public function which returns the average cost of the item identified by 'skuid'
     * 
     * @params string $skuid
     * @params date   $refdate
     * 
     * @return double Average Cost
     */     
    public function getSKUAvgCost($skuid, $refdate = '', $postDate = '') {
        global $db;
//        $db->debug = true;

        if (empty($refdate)) $refdate = date("Y-m-d");
        $this->sql = "SELECT sel.* 
                        FROM ".INV_TABLE." sel 
                        WHERE sel.`sku_id` = '{$skuid}' 
                           AND tr_date <= DATE('{$refdate}')";

        if(!empty($postDate)) {
            $this->sql .= "AND post_date <= '{$postDate}'";
        }

        $this->sql .= " ORDER BY sel.`tr_date` DESC, sel.`post_date` DESC, sel.`post_uid` DESC
                        LIMIT 1";
        $result = $db->Execute($this->sql);
        if ($result) {            
          return $this->getAvgCost($result->getArray());
        }

       return doubleval(0);
    }
    
    /**
    * Updated By Jarel
    * Use Prapare statements
    */
    private function getQtyPerBigUnit($item_code) {
        global $db;              
        
        $qty = 1;   // Default qty per pack 
        $this->sql = $db->Prepare("SELECT qty_per_pack FROM seg_item_extended WHERE item_code = ? ");         
        if($result = $db->GetOne($this->sql,array($item_code))){
            $qty = is_null($result) ? 1 : $result ;
        }   
        
        return $qty;                
    }

    /**
     * Computer Moving Avergae Cost of Inventory Ledger
     * @param array $ledger
     * @return float|int
     */
    private function getAvgCost(array $ledger) {
        $totalCost = 0;
//        printR($ledger);

        foreach($ledger as $ledgerRow) {
           $totalCost += $this->getLedgerRowAvgCost($ledgerRow);
        }
//        echo '<hr>';
        return $totalCost == 0 ? 0 : round(floatval($totalCost)/count($ledger),2);
    }

    /**
     * formula for getting avg cost per ledger row
     * @param array $ledgerRow
     * @return float|int
     */
    public function getLedgerRowAvgCost(array $ledgerRow)
    {

        try {
            $packQty = $ledgerRow['packqty'];
//            echo 'pack:' . $packQty;
            //handle error in calculation in unpack
            if(($this->trCode == 'UPK' && $ledgerRow['mvmnt_qty'] < 0))
                $packQty = 1;

            //Added By Jarel 
            if( $ledgerRow['mvmnt_qty']  + $ledgerRow['prev_qty'] <= 0){
                return 0;
            }

            $cost = (($ledgerRow['mvmnt_qty'] * $packQty * ($ledgerRow['unit_cost'] / $packQty)) + ($ledgerRow['prev_qty'] * $ledgerRow['prev_cost'])) /
                ($packQty * $ledgerRow['mvmnt_qty']  + $ledgerRow['prev_qty']);

            #added by monmon
            if($ledgerRow['tr_code'] == 'ADJ'){
                $cost = $ledgerRow['unit_cost'];
            }
//            printR($cost);
//            printR($this->trCode);
//            printR($ledgerRow['mvmnt_qty'] . '*'. $ledgerRow['unit_cost']);
//            printR($ledgerRow['prev_qty'] . '*'. $ledgerRow['prev_cost']);

            return $cost == 0 ? floatval($ledgerRow['prev_cost']) : round(floatval($cost), 2);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * modified by marc lua 08/20/2014
     * get average cost of an item according to area
     * step 1: get the latest row in the ledger for the specific itemcode and area
     * step 2: get the avg cost using the formula
     *
     * @param $itmcode
     * @param string $refdate
     * @param string $areacode
     * @param string $expirydate
     * @param string $serialno
     * @param int $unitid
     * @param bool $brecent
     * @return double Average Cost
     */
    public function getItemAvgCost($itmcode, $refdate = '', $areacode = '', $expirydate = '', $serialno = '', $unitid = 0, $brecent = false, $trCode = '') {
        global $db;
        if (empty($areacode)) {
            $areacode = 'IP';
        }
        if (empty($refdate)) {
            $refdate = date('Y-m-d');
        }
        // $dtefilter = ($brecent) ? "tr_date > DATE(?)" : "tr_date <= date(?)";
//            $dtefilter = "sel.tr_date <= DATE(?)";
//        $db->debug = true;


       $this->sql = $db->Prepare("SELECT fn_getItemAvgCost(?,?) as avg_cost ");
       // die($this->sql);
        $result = $db->GetOne($this->sql, array( $itmcode,$refdate));
        
        if ($result) {

           return $result;
        }

        // Added to return the average cost of item based on unit id (if in pack or pc.) ...
//        if ($unitid != 0) {
//            $unitobj = new Unit();
//            $isbigunit = $unitobj->isUnitIDBigUnit($unitid);
//            $packqty = ($isbigunit) ? doubleval($this->getQtyPerBigUnit($itmcode)) : doubleval(1);
//            $avgcost *= $packqty;
//        }

        return doubleval(0);
    }

    //added by julz

    public function stockcardAvgcost($refno,$refdate = '',$areacode = ''){
         global $db;
         if (empty($areacode)) {
            $areacode = 'IP';
        }
        if (empty($refdate)) {
            $refdate = date('Y-m-d');
        }

        $dtefilter = "post_date >= DATE(?)";

       $this->sql = $db->Prepare("SELECT sel.*
                    FROM seg_inventory_ledger sel
                    INNER JOIN seg_sku_catalog sk
                    ON sk.sku_id = sel.sku_id
                    WHERE sel.tr_code != 'UPK' AND sk.area_code = ? AND sel.tref_no = ? AND {$dtefilter}
                    ORDER BY sel.post_date DESC, sel.post_uid DESC
                    LIMIT 1");
     
        $result = $db->Execute($this->sql, array($areacode, $refno,$refdate));
        if ($result) {
           return $this->getAvgCost($result->getArray());
        }
        return doubleval(0);
    }

     public function bgngcost($itemcode,$refdate = '',$areacode = ''){
         global $db;
         if (empty($areacode)) {
            $areacode = 'IP';
        }
        if (empty($refdate)) {
            $refdate = date('Y-m-d');
        }

        $dtefilter = "post_date < DATE(?)";

       $this->sql = $db->Prepare("SELECT sel.*
                    FROM seg_inventory_ledger sel
                    INNER JOIN seg_sku_catalog sk
                    ON sk.sku_id = sel.sku_id
                    WHERE sel.tr_code != 'UPK' AND sk.area_code = ? AND sk.item_code = ? AND {$dtefilter}
                    ORDER BY sel.post_date DESC, sel.post_uid DESC
                    LIMIT 1");
     
        $result = $db->Execute($this->sql, array($areacode, $itemcode,$refdate));
        if ($result) {
           return $this->getAvgCost($result->getArray());
        }
        return doubleval(0);
    }

    /***
     * Public function which inserts an entry for the inventory movement in the inventory ledger.
     *
     * @params string $refno
     * @params string $trcode
     * @params date   $trdate
     * @params string $skuid
     * @params double $qty
     * @params double $packqty
     * @params double $ucost
     *
     * @return boolean TRUE if successfully saved, FALSE otherwise.
     */
    public function postSKUItemMovement($refno, $trcode, $trdate, $skuid, $qty, $packqty, $ucost = -1, $itemCode = '', $areaCode = 'IP') {
        global $db;
        #comment out by monmon : allow 0 adjustment quantity
        // if($qty == 0)
        //     return true;
        $this->trCode = $trcode;
        $trdate = (empty($trdate)) ? date("Y-m-d") : $trdate;
        $ucost = floatval($ucost);

        //use sku item avg cost 
        // if(!empty($itemCode)) {
        //     $prevcost = $this->getItemAvgCost($itemCode, '', $areaCode);
        //     $prevqty  = $this->getItemQty($itemCode, '', $areaCode );
        //     $ucost = $ucost < 0 ? $prevcost : $ucost;
        // } else {
        //     //may not be needed if the computation is right
        //     $prevcost = $this->getSKUAvgCost($skuid, $trdate);
        //     $prevqty  = $this->getSKUQty($skuid, $trdate);
        //     $ucost = $ucost < 0 ? $this->getSKUAvgCost($itemCode) : $ucost;
        // }

        
//        $prevcost = $this->getItemAvgCost($itemCode, '', $areaCode);
//        $prevqty  = $this->getItemQty($itemCode, '', $areaCode );
        $ucost = $ucost < 0 ? $this->getItemAvgCost($itemCode, '', $areaCode) : $ucost;

//        temporary handle items with 0 qty
//        if($prevqty <= 0 && $qty < 0) {
//            // $ucost = 0;
//            $prevcost = 0;
//        }

        if($trcode == 'CNL' && ($prevqty + $qty) >= 0 && $ucost == 0) {
            $skuInfo = $this->getSKUInfo($skuid);
            if($skuInfo) {
                $ucost = floatval($skuInfo['unit_cost']);
            }
        }

//        die($ucost);

        if($trcode == 'ISS' && $qty > 0) {
            $this->updateSkuUnitCost($skuid, $ucost);
            $ucost = $ucost * $packqty;
        }

        //adjustment if the unit is piece divide with packqty
        if(($trcode == 'UPK' || $trcode == 'ADJ' || $trcode == 'RCV') && $qty > 0) {
            $this->updateSkuUnitCost($skuid, $ucost, $packqty);
//            $ucost = ($ucost < 0) ? $prevcost : $ucost;
        }
        
//        $db->debug = true;
        $this->sql = "INSERT INTO ".INV_TABLE." (tref_no, tr_code, tr_date, sku_id, unit_cost, mvmnt_qty, packqty)
                         VALUES ({$db->qstr($refno)}, {$db->qstr($trcode)}, DATE('{$trdate}'), {$db->qstr($skuid)}, {$ucost}, {$qty}, {$packqty})";                                                                                                           
        $bSuccess = $this->Transact($this->sql);  
                
//        $db->debug = false;
        return $bSuccess;
    }
    
    /***
     * Public function which deletes the entry in the inventory ledger given the ff. parameters
     * 
     * @params string $refno
     * @params string $trcode
     * @params date   $trdate
     * @params string $skuid
     * 
     * @return boolean TRUE if successfully saved, FALSE otherwise.
     */      
    public function removeSKUItemMovement($refno, $trcode, $trdate, $skuid) {       
        $this->sql = "DELETE FROM ".INV_TABLE." WHERE sku_id = '".$skuid."'  ".
                     "    AND tref_no = '".$refno."' AND tr_code = '".$trcode."'".
                     "    AND tr_date = DATE('".$trdate."')";                
        $bSuccess = $this->Transact($this->sql);        
        return $bSuccess;        
    }
    
    /***
     * Public function which returns the associated information given the sku id.
     * 
     * @params string   $skuid
     * 
     * @return row of resultset, FALSE otherwise.
     */
    public function getSKUInfo($skuid) {
        global $db;
        
        $this->sql = $db->Prepare("SELECT 
                        * 
                      FROM
                        seg_sku_catalog 
                      WHERE sku_id = ? ");
        $result = $db->Execute($this->sql,array($skuid));
        if ($result) {            
            if ($result->RecordCount()) {
                return $result->FetchRow();
            }
        }        
        return false;        
    }
    
    /***
     * Public function which clears the table for temporarily holding inventory movement
     * to make the proper adjustments should a transaction affect later transactions.
     */    
    public function clearTmpTable() {
        return $this->Transact("DELETE FROM seg_inventory_ledger_tmp");
    }

    public function getReceivedCostOfSku($skuId) {
        global $db;
        $this->sql = 'SELECT unit_cost
                      FROM seg_inventory_ledger
                      WHERE sku_id = ? AND tr_code IN ("RCV", "UPK", "ISS")
                      ORDER BY post_date, post_uid
                      LIMIT 1';
        $this->result = $db->Execute($this->sql, $skuId);
        if($this->result) {
            $row = $this->result->FetchRow();
            return floatval($row['unit_cost']);
        }
        return 0;
    }
    ///added by julz
    public function getsupplierprice($itemcode){
      global $db;
        $this->sql = "SELECT supplier_price FROM care_pharma_products_main WHERE bestellnum = '$itemcode'";
        $this->result = $db->Execute($this->sql);
         if($this->result) {
            $row = $this->result->FetchRow();
            return floatval($row['supplier_price']);
        }
        return 0;
    }

    #added by monmon
    public function getLatestAdjustInfo($itemcode,$areacode,$refno){
        global $db;
        $qty = 0;

        $sql = "SELECT 
            (sil.mvmnt_qty+sil.prev_qty) qty, sil.tref_no refno,
            sil.mvmnt_qty,
            sil.unit_cost ucost,
            sil.prev_cost pcost
        FROM seg_inventory_ledger sil
        INNER JOIN seg_sku_catalog sku ON sku.sku_id = sil.sku_id
        WHERE sil.tr_code = 'ADJ'
        AND sku.item_code = '$itemcode'
        AND sku.area_code = '$areacode'
        AND sil.tref_no = '$refno'
        ORDER BY sil.post_date DESC LIMIT 1";
        
        $result = $db->Execute($sql);
        if($result){
            $row = $result->FetchRow();
            return $row;
        }
        return null;
    }
   
}
?>