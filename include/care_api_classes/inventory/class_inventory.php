<?php
/*
 * @package care_api
 * Class of Inventory.
 *
 * Created:  Bong S. Trazo
 * Modified: April 18, 2013 (BST)
 */
require_once('./roots.php');
require_once($root_path.'include/care_api_classes/inventory/class_item.php'); 
require_once($root_path.'include/care_api_classes/inventory/class_unit.php');   
require_once($root_path.'include/care_api_classes/inventory/class_serial.php');   
require_once($root_path.'include/care_api_classes/inventory/class_eodinventory.php');
require_once($root_path.'include/care_api_classes/class_area.php');
require_once($root_path.'include/care_api_classes/inventory/class_expiry.php'); 
require_once($root_path.'include/care_api_classes/alerts/class_alert.php');
require_once($root_path.'include/care_api_classes/inventory/class_expiry.php');
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');

class Inventory extends Item {
	#created by Bryan
	var $ok;
	var $db_error_msg;
	/*
	* @var String
	*/
	var $item_code;
	/*
	* @var Integer
	*/
	var $area_code;
	/*
	* @var Double
	*/
	var $available_qty;
	/*
	* @var Expiry[]
	*/
	var $expiry = array();
	/*
	* @var Serial[]
	*/
	var $serial_nos = array();
	
	var $serial_obj;
	/**
	* @var string Holder for SQL query.
	*/
	var $sql='';
	/**
	* @var string Holder for other details for serial_inventory before using them in queries.
	*/
	var $serial_detail_array = array();
	/**
	* @var string Holder for other details for expiry_inventory before using them in queries.
	*/
	var $expiry_detail_array;
	/**
	* @var Date Holder eod_date before using it in queries.
	*/
	var $eod_date;
	/*
	* var integer that stores the number qty(pieces) of the item per pack/whatever unit
	*/
	var $qtyperunit;
	
	var $items_array;
    var $refno;     // Reference no. of transaction affecting the inventory.
    var $trcode;    // The transaction code - RCV, ADJ, SLE, TRA, ISS, CON, RET, UPK, RPK
	
//    var $alert_obj;

    private $skuInventory;

    public function __construct() {
        $this->skuInventory = new SkuInventory();
    }
	/*
	#under investigation because apparently a function exists with the same name..
	#created setInventoryParams and is working
	*/
	function setParams($item_code, $area_code){
		
		$this->item_code = $item_code;
		$this->area_code = $area_code;
		
		//echo $this->item_code.", ".$this->area_code."<br>";
		
		return true;
	
	}      
	/*
	# sets the item_code and area code for the class.. 
	#important to set params before using add inventory and remove inventory
	*/
	function setInventoryParams($item_code, $area_code, $refno, $trcode){
//		global $db;
        
            $this->item_code = $item_code;
            $this->area_code = $area_code;

            $this->refno  = $refno;
            $this->trcode = $trcode;
		
		//echo $this->item_code.", ".$this->area_code."<br>";		
            return true;	
	}
	
	/*
	#set details for serial object
	#set Inventory Params first before calling this.. setInventoryParams 
	*/
	function setSerialObject ($property_no, $acquisition_cost, $acquisition_date, $supplier_id, $serial=NULL){
//        $this->serial_detail_array[0] = $property_no;
//        $this->serial_detail_array[1] = $acquisition_cost;
//        $this->serial_detail_array[2] = $acquisition_date;
//        $this->serial_detail_array[3] = $supplier_id;
		$this->serial_obj = new Serial();
		
		if($serial) $this->serial_obj->serial_no = $serial;
		$this->serial_obj->item_code = $this->item_code;
		$this->serial_obj->area_code = $this->area_code;
		$this->serial_obj->property_no = $property_no;
		$this->serial_obj->acquisition_cost = $acquisition_cost;
		$this->serial_obj->acquisition_date = $acquisition_date; 
		$this->serial_obj->supplier_id = (is_null($supplier_id)) ? 0 : $supplier_id;
				
		return true;
	}          
	
	function setSerial($serial_obj) {
		$this->serial_obj = new Serial();
		
		$this->serial_obj->serial_no = $serial_obj->serial_no;
		$this->serial_obj->item_code = $serial_obj->item_code;
		$this->serial_obj->area_code = $serial_obj->area_code;
		$this->serial_obj->property_no = $serial_obj->property_no;
		$this->serial_obj->acquisition_cost = $serial_obj->acquisition_cost;
		$this->serial_obj->acquisition_date = $serial_obj->acquisition_date; 
		$this->serial_obj->supplier_id = (is_null($serial_obj->supplier_id)) ? 0 : $serial_obj->supplier_id;    
		
		return true;
			 
	}	

    /***
     * Routine which posts inventory movement in inventory ledger.
     * 
     * @param  double  $qty
     * @param  mixed   $unit
     * @param  date    $expiry_date
     * @param  string  $serial_no
     * @param  date    $trns_date
     * @param  double  $unit_cost
     * 
     * @return boolean TRUE if posting succeeds, FALSE otherwise.
     */
	function addInventory($qty, $unit, $expiry_date=NULL, $serial_no=NULL, $trns_dte=NULL, $unit_cost=-1, $lotNo = '') {
		if (!is_object($unit)) {			
            $unitid = $unit;
            $unit_obj = new Unit();
            $packqty = ($unit_obj->isUnitIDBigUnit($unit)) ? $this->getQtyPerBigUnit($this->item_code) : 1;
		}
		else {
            $unitid = $unit->unit_id;
            $packqty = (!$unit->is_unit_per_pc || ($unit->is_unit_per_pc == 'undefined')) ? $this->getQtyPerBigUnit($this->item_code) : 1;
		}
        // Create sku inventory object.
        $skuobj = new SKUInventory();

        $skuid = $skuobj->getSKUId($this->item_code, $this->area_code, $expiry_date, $serial_no, $unitid, $unit_cost, $lotNo);

        //cancellation should have item avg cost as unit cost
        if($this->trcode == 'CNL') {
            $unit_cost = -1;
        }

        $bsuccess = false;

        if ($skuid) {
            $bsuccess = $skuobj->postSKUItemMovement($this->refno, $this->trcode, $trns_dte, $skuid, $qty, $packqty, $unit_cost, $this->item_code, $this->area_code);
        }
        
        if (!$bsuccess) die("SQL = ".$skuobj->sql);        
        return $bsuccess;
    }

    /**
     * Remove Item Transaction in Inventory
     * @param array $skuInfo
     * @param $trDate
     * @param $qty
     */
    public function removeItem(Array $skuInfo, $trDate, $qty) {
        $this->skuInventory->postSKUItemMovement($this->refno, $this->trcode, $trDate, $skuInfo['sku_id'], -1 * $qty, 1, -1, $this->item_code, $this->area_code);
    }

    /**
     * The unpacking process when selling an item.
     * @param array $skuInfo
     * @param $trDate
     * @param $runningQty
     * @param $qty
     * @param $qtyPerPack
     */
    public function unpackItem(Array $skuInfo, $trDate, $runningQty, $qty, $qtyPerPack) {
//        printR($runningQty);
        //remove big unit qty

        $this->skuInventory->postSKUItemMovement($this->refno, UNPACK, $trDate, $skuInfo['sku_id'], -1 * $qty, $qtyPerPack, -1, $this->item_code, $this->area_code);
        $item = new Item();

        $unitId = $item->getPcUnit($this->item_code);
        $expiryDate = $skuInfo['expiry_date'];
        $serialNo = $skuInfo['serial_no'];
        $unitCost = $skuInfo['unit_cost'];
        $lotNo = $skuInfo['lot_no'];
        //compare if there is an existing sku of that item
        $skuId =  $this->skuInventory->getSKUId($this->item_code, $this->area_code, $expiryDate, $serialNo, $unitId, $unitCost, $lotNo);
        //unpack item to get small unit
        $this->skuInventory->postSKUItemMovement($this->refno, UNPACK, $trDate, $skuId, $qty * $qtyPerPack, $qtyPerPack, $unitCost, $this->item_code, $this->area_code);
        //the qty requested is less than qty per pack
        if($runningQty < $qty * $qtyPerPack) {
            //sell those small unit
            $this->removeItem(array('sku_id' => $skuId), $trDate, $runningQty);
        } else {
            $this->removeItem(array('sku_id' => $skuId), $trDate, $qty * $qtyPerPack);
        }
    }

    /**
     * Get current item qty
     * @param $skuId
     * @param $trDate
     * @param $runqty
     * @return float
     */
    public function getCurrQty($skuId, $trDate, $runqty) {
       /* $curqty =  $this->skuInventory->getSKUQty($skuId, $trDate);
        //if qty request is less qty of sku
        if($runqty < $curqty) {
            return intval($runqty);
        }*/
        return intval($runqty);
        //return intval($curqty);
    }
	
    /***
     * Routine which posts a negative inventory movement in inventory ledger.
     * 
     * @param  double  $qty
     * @param  mixed   $unit
     * @param  date    $expiry_date
     * @param  string  $serial_no
     * @param  date    $trns_date
     * @param  boolean $bGetItems (used in billing.server.php)
     * 
     * @return boolean TRUE if posting succeeds, FALSE otherwise.
     */
    function remInventory($qty, $unit, $expiry_date=NULL, $serial_no=NULL, $trns_dte=NULL, $bGetItems = FALSE) {

        $unitObj = new Unit();
        $skuObj = new SKUInventory();

        if (empty($trns_dte)) $trns_dte = date("Y-m-d");
        $runqty = $qty;
        $bsuccess = false;
        $rsskuids = $skuObj->getSKUIdswInventory($this->item_code, '', $this->area_code);
        $packQty = $this->getQtyPerBigUnit($this->item_code);
        if ($rsskuids) {
            if ($bGetItems) $this->items_array = array();
            $skus = $rsskuids->getArray();
            $index = 0;
            while ($runqty > 0) {
                //echo 'index:'. $index . ': count=' . sizeof($skus);
                if ($index < sizeof($skus) && $index < count($skus)) {
                    $row = $skus[$index];
                    $index += 1;
                    $skuid = $row['sku_id'];
                    $curqty = $runqty;
                    //printR($skuid . ':' . $curqty . ':'. $index);
//                  current qty is 0
                    if ($row['rung_qty'] <= 0 && $index < sizeof($skus)) {
                        continue;
                    }
                } else {
                    $curqty = $runqty;
                }


                $skuInfo = $skuObj->getSKUInfo($skuid);
                $skuUnitId = $skuInfo['unit_id'];
                $isBigUnit = $unitObj->isUnitIDBigUnit($skuUnitId);
//                printR('run:'. $runqty . ',' . $curqty);
                if($isBigUnit) {

                    //unpack the right number of packs
                    $unpackQty = floor($runqty / $packQty);
                    if($runqty % $packQty != 0) {
                        $unpackQty += 1;
                    }
                    if($unpackQty > $curqty)
                        $unpackQty = $curqty;

                    $this->unpackItem($skuInfo, $trns_dte, $runqty, $unpackQty, $packQty);
                    $curqty = $unpackQty * $packQty;
                } else {
                    $this->removeItem($skuInfo, $trns_dte, $curqty);
                }
//                printR($runqty . ','. $curqty);
                $runqty -= $curqty;


                if ($row) {
                    if ($bGetItems) {
                        $skuinfo = $skuObj->getSKUInfo($skuid);
                        if ($skuinfo) {                        
                            $this->items_array[] = array('code'=>$this->item_code, 'qty'=>$curqty, 'expiry'=>$skuinfo['expiry_date'], 'serial'=>$skuinfo['serial_no']);
                        }
                        else {
                            $this->items_array[] = array('code'=>$this->item_code, 'qty'=>$curqty, 'expiry'=>$expirydt, 'serial'=>'');                            
                        }
                    }                
                }
            }

            $bsuccess = true;
            
            if ($bsuccess) {
               /*
                Not used
                Save alert to db when item is below or equal to critical level
                Comment out

                $alertqty = $skuObj->getItemQty($this->item_code, $trns_dte, $this->area_code);

                $objitm = new Item();
                $min_qty = $objitm->getMinQty($this->item_code);

                if($alertqty <= $min_qty) { 
                    if ($alertqty >= 0) 
                        $message = "Quantity running low for item ".$objitm->getItemDesc($this->item_code);
                    else
                        $message = "Quantity of ".$objitm->getItemDesc($this->item_code)." in inventory is already negative!";

                    $alert_obj = new SegAlert();
                    $alert_obj->postAlert($this->area_code, 6, "", "Critical Quantity", $message, "H", "");
                }
               */
            }
        }
        else {
            $skuId = $skuObj->getSKUId($this->item_code, $this->area_code);                                    
            $this->removeItem(array('sku_id' => $skuId), $trns_dte, $runqty);
            $this->items_array[] = array('code'=>$this->item_code, 'qty'=>$runqty, 'expiry'=>'', 'serial'=>'');   
            //$this->setErrorMsg("No inventory of item ".$this->item_code." in ".$this->area_code);
            $bsuccess = true;
        }

        $this->sql = $skuObj->sql;        
        return $bsuccess;                                   
    }
    
    function getTrIsolationLevel() {
        global $db;
        
        $sessionlevel = '';        
        $this->sql = "SELECT @@tx_isolation sisolevel";
        $result = $db->Execute($this->sql);
        if ($result) {            
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                if ($row) {
                    $sessionlevel = $row['sisolevel'];
                }
            }
        }
        return $sessionlevel;
    }
    
    function setTrIsolationLevel($isolationlevel) {
        $isolationlevel = str_replace("-", " ", $isolationlevel);
        $this->sql = "SET SESSION TRANSACTION ISOLATION LEVEL {$isolationlevel}";
        return $this->Transact();        
    }
    
    function getRemovedItemsFromInventory() {
        return $this->items_array;
    }
	
    /*
    *returns Expiry[]
    */    
	function getExpiry() {
		return $this->expiry;	
	}
    
    /*
    *returns Serial[]
    */    
	function getSerialNos() {
		return $this->serial_nos;	
	}
    
    /***
     *  Return the total quantity of item on stock.
     */
	function getTotalQty() {
        $skuobj = new SKUInventory();
        $n_qty = $skuobj->getItemQty($this->item_code, NULL, $this->area_code);
		$this->available_qty = $n_qty;        
		return $n_qty;                
	} 
	
    /***
     *  Return the average cost of item.
     */
	function getItemCost() {        
        $skuobj = new SKUInventory();
        $ucost = $skuobj->getItemAvgCost($this->item_code);
        return $ucost;        
	}  
	
	#added by Bryan on March 03, 2009
    /***
     * Returns the inventory quantity of 'item_code'
     */
	function getInventoryAtHand ($item_code, $area) {		
        $skuobj = new SKUInventory();
        $refdate = date("Y-m-d");        
        $tqty = $skuobj->getItemQty($item_code, $refdate, $area);
        return $tqty;                
	} 
			
	 #added by Bryan on May 25, 2009
    /***
     * Returns the inventory quantity of 'item_code' as of 
     */
	function getInventoryAtHandbyDate ($item_code, $area, $date=FALSE) {
        $skuobj = new SKUInventory();
        if(!$date) $date=date("Y-m-d");        
        $tqty = $skuobj->getItemQty($item_code, $date, $area);
        return $tqty;                
	} 
	
	#added by Bryan on May 25, 2009
	function getInventoryAtHandbyDateWithSerial ($item_code, $area, $date=FALSE){
		global $db;
		if(!$date) $date=date("Y-m-d");
		$counter = 0;
		
		$adj_obj = new SegArea();
		$eod_obj = new EODInventory();
		$expiry_obj = new Expiry();
		$serial_obj = new Serial();
		
		$resultExp = $expiry_obj->getExpiriesofItem($item_code, $area);
		if($resultExp){
			while($rowExp = $resultExp->FetchRow()){
				$resultSerial = $serial_obj->getSerialsofItemExpiry($item_code, $area, $rowExp['expiry_date']);
				if($resultSerial){
					while($rowSer = $resultSerial->FetchRow()){
						$counter += $eod_obj->getCurrentEODQty($item_code, $area, $date, $rowExp['expiry_date'],$rowSer['serial_no']);
					}
				}
				else{
					$counter += $eod_obj->getCurrentEODQty($item_code, $area, $date, $rowExp['expiry_date']);
				}
			}
		}   
					 
		return $counter;
	} 
	
    #added by Bryan on May 25, 2009
    function getRecentInvAtHandbyDateWithSerial ($item_code, $area, $date=FALSE){
        global $db;
        if(!$date) $date=date("Y-m-d");
        $counter = 0;
        
        $adj_obj = new SegArea();
        $eod_obj = new EODInventory();
        $expiry_obj = new Expiry();
        $serial_obj = new Serial();
        
        $resultExp = $expiry_obj->getExpiriesofItem($item_code, $area);
        if($resultExp){
            while($rowExp = $resultExp->FetchRow()){
                $resultSerial = $serial_obj->getSerialsofItemExpiry($item_code, $area, $rowExp['expiry_date']);
                if($resultSerial){
                    while($rowSer = $resultSerial->FetchRow()){
                        $counter += $eod_obj->getRecentEODQty($item_code, $area, $date, $rowExp['expiry_date'],$rowSer['serial_no']);
                    }
                }
                else{
                    $counter += $eod_obj->getCurrentEODQty($item_code, $area, $date, $rowExp['expiry_date']);
                }
            }
        }   
                     
        return $counter;
    } 
    
	function getInventoryAtHandIncludeDate ($item_code, $area, $date=FALSE){
//		global $db;
//		if(!$date) $date=date("Y-m-d");
//		$counter = 0;
//		
//		$adj_obj = new SegArea();
//		$eod_obj = new EODInventory();
//		$expiry_obj = new Expiry();
//		
//		$resultExp = $expiry_obj->getExpiriesofItem($item_code, $area);
//		if($resultExp){
//			while($rowExp = $resultExp->FetchRow()){
//				$counter += $eod_obj->getCurrentEODQty($item_code, $area, $date, $rowExp['expiry_date']);
//			}
//		}   
//					 
//		return $counter;
        return $this->getInventoryAtHandbyDate($item_code, $area, $date);
	}     
	
	#added by Bryan on April 13, 2009
    /***
     * Modified by LST -- 05/24/2013
     *    - removed $area parameter, not needed in routine.
     *    - added $offset and $rowcount for pagination
     *    - modified query as required by new design of inventory system
     */
//    function getItemsinArea($area, $filter, $keyword=null, $offset=0, $rowcount=10){
    function getItemsinArea($filter, $keyword=null, $offset=0, $rowcount=10){
		global $db;
        
        $this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS 
                        p.*,
                        pex.*,
                        (SELECT 
                          z.unit_name 
                        FROM
                          seg_unit AS z 
                        WHERE pex.pack_unit_id = z.unit_id) AS pack_unitname,
                        (SELECT 
                          y.unit_name 
                        FROM
                          seg_unit AS `y` 
                        WHERE pex.pc_unit_id = y.unit_id) AS pc_unitname 
                      FROM
                        care_pharma_products_main p 
                        LEFT JOIN seg_item_extended pex 
                          ON p.`bestellnum` = pex.`item_code` ".
                        (($filter) ? "LEFT JOIN seg_type_product t 
                          ON t.nr = p.type_nr" : "");
//                      "WHERE fn_getitemqty(fn_getSKUIds(p.`bestellnum`, ".(($area) ? $area : "NULL").", NULL, NULL, NULL), DATE(NOW())) > 0";
		
//		$this->sql = "select *,
//						(select z.unit_name from seg_unit as z where c.pack_unit_id=z.unit_id) as pack_unitname,
//						(select y.unit_name from seg_unit as y where c.pc_unit_id=y.unit_id) as pc_unitname
//						from seg_eod_inventory as a 
//						left join care_pharma_products_main as b ON a.item_code=b.bestellnum
//						left join seg_item_extended as c ON a.item_code=c.item_code ";
//	
//		if ($filter) $this->sql .= "left JOIN seg_type_product AS d ON b.type_nr=d.nr\n";
						
		$where = array();
		if ($keyword && $keyword!='*') {
			$terms = preg_split("/[,|]+/",$keyword);
			foreach ($terms as $i=>$v)
				$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
			$regexp = implode(")|(",$terms);
			$where[] = "(p.artikelname REGEXP '([[:<:]]($regexp))' OR p.generic REGEXP '[[:<:]]($regexp)')";
		}		
		
        $where[] = "NOT p.is_deleted";   // Exclude deleted items ...
        
		if ($filter) $where[] = "t.prod_class='$filter'";
		
		if ($where) {
            $this->sql.= "\nWHERE";
			$this->sql.= " (".implode(") AND (",$where).")\n";               
        }
					
         $this->sql .= "GROUP BY p.bestellnum ORDER BY p.artikelname \n";
         $this->sql .= "LIMIT {$offset}, {$rowcount}\n";
						
         $this->result = $db->Execute($this->sql);
         if ($this->result) {
			return $this->result;
			} else { return false; }
	}
	
	function getItemsBetweenDate ($start_date, $end_date, $area='') {
		global $db;
		
		$this->sql = "select DISTINCT item_code
						from seg_eod_inventory as a 
						left join care_pharma_products_main as b ON a.item_code=b.bestellnum ";
						
		$where = array();
		if ($keyword && $keyword!='*') {
			$terms = preg_split("/[,|]+/",$keyword);
			foreach ($terms as $i=>$v)
				$terms[$i] = preg_quote(preg_replace("/^\s+|\s+$/","",$terms[$i]));
			$regexp = implode(")|(",$terms);
			$where[] = "(artikelname REGEXP '([[:<:]]($regexp))' OR generic REGEXP '[[:<:]]($regexp)')";
		}
		
		if ($area) $where[] = "a.area_code='$area'";
		
		if ($filter) $where[] = "b.prod_class='$filter'";
		
		if ($start_date) $where[] = "a.eod_date >= '".$start_date."'";  
		
		if ($end_date) $where[] = "a.eod_date <= '".$end_date."'";  
		
		if ($where)
			$this->sql.= "WHERE (".implode(") AND (",$where).")\n";               
					
		 $this->sql.= "group by a.item_code";
						
		 if($this->result=$db->Execute($this->sql)) {
			return $this->result;
			} else { return false; }
	} 

    //Created by EJ 11/04/2014
    function getPendingQty($id, $refno, $area, $area_dest) {
        global $db;
        
        $arrayFilter = array($id,$area,$area_dest,$refno);    

        $this->sql = $db->Prepare("SELECT 
                      SUM(
                        a.item_qty * IF(
                          a.is_unitperpc != 1,
                          (SELECT 
                            qty_per_pack 
                          FROM
                            seg_item_extended 
                          WHERE item_code =a.item_code),
                          a.is_unitperpc
                        )
                      ) - 
                      (SELECT 
                        IFNULL(SUM(served_qty), 0) served_qty 
                      FROM
                        seg_requests_served 
                      WHERE item_code = a.item_code 
                        AND request_refno = a.refno) AS pending 
                    FROM
                      seg_internal_request_details a 
                      INNER JOIN seg_internal_request b 
                        ON a.refno = b.refno 
                    WHERE (
                        a.item_code = ?
                        AND b.area_code_dest = ?
                        AND b.area_code = ?
                        AND a.refno = ?
                      )");
        
        if($result = $db->GetOne($this->sql,$arrayFilter)){
            return $result;
        }else {
            return false;
        }
    }
	
}
?>