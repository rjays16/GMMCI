<?php

/**
* class InventoryHelper
*
* Provides abstraction for adding and removing stocks using Inventory module
*
* @author Alvin Quinones
* @package care_api_classes/inventory
* @version 1.0.0
*/

require("./roots.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
require_once($root_path.'include/care_api_classes/class_order.php');

class InventoryHelper {


	/**
	* Default constructor
	*
	*/
	function InventoryHelper() {

	}

	/**
	* internal function for retrieving the current available stocks for
	* the specific item; usually called internally by removeStock function
	*
	* @param mixed $id item id of the inventory item
	* @param mixed $area area to check the current available stocks
	*/
//	function _prepare_inventory_matrix($id, $area) {
//		global $db;
//		$this->sql = "SELECT expiry_date,qty FROM seg_inventory\n".
//			"WHERE item_code=".$db->qstr($id)." AND area_code=".$db->qstr($area)." AND serial_no=''\n".
//			"ORDER BY expiry_date ASC";
//
//		$matrix = array();
//		if ($this->result=$db->Execute($this->sql)) {
//			while ($row=$this->result->FetchRow()) {
//				$matrix[] = array($row['expiry_date'],$row['qty']);
//			}
//			return $matrix;
//		}
//		else {
//		}
//		return FALSE;
//	}

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
	/**
	* Adds stock of an item to the specified inventory area
	*
	* @param mixed  $item id of the item to be added
	* @param mixed  $area area code of the inventory area
	* @param mixed  $qty quantity to be added to stock
    * @param date   $trdate - transaction date
    * @param string $refno - return reference no.
	* @return bool returns TRUE on success, FALSE otherwise
	*/
	function addStock($item, $area, $qty, $trdate = '', $refno = '', $srcrefno = '', $transactionCode = '', $unit_id='', $is_unit_per_pc=1) {
            if(empty($transactionCode)) {
                $transactionCode = PHRETURN;
            }

            $unitObj = new Unit();
            
            if($unit_id){
                $unitObj->unit_id = $unit_id;
                $unitObj->is_unit_per_pc = $is_unit_per_pc;
            }else{
                $unit_id = $this->getPcUnit($item);
                $unitObj->unit_id = $unit_id;
                $unitObj->is_unit_per_pc = 1;
            }
           
		$invobj = new Inventory();        
        
//		$invobj->area_code = $area;
//		$invobj->item_code = $item;        
        if (empty($trdate)) $trdate = date('Y-m-d');
        $invobj->setInventoryParams($item, $area, $refno, $transactionCode);
        
        $skuobj = new SKUInventory();
        $expirydts = $skuobj->getExpiryDateswithQtyinTrans($srcrefno, $item, $area, $unit_id);


        $runqty = $qty;
        
        $saveok = false;
        $dataExpiry = $expirydts->FetchRow();

        if (empty($expirydts) || is_null($dataExpiry[0]['unit_id'])) {
            $expirydts = array();
            $expirydts[] = array('expiry_date' => '0000-00-00', 
                                'unit_id' => ($unit_id ? $unit_id : DEFAULT_UNIT), 
                                'qty' => $qty,
                                'unit_cost' => 0);
        }


        
        if (!empty($expirydts)) {
            foreach($expirydts as $k=>$v) {
                if(!is_null($v['qty'])){
                    $serialNo = $v['serial_no'];
                    $lotNo = $v['lot_no'];
                    $expirydte = $v['expiry_date'];
                    $unitObj->unit_id = $v['unit_id'];
                    if($transactionCode == 'CNL') {
                        $unitCost = $v['unit_cost'];
                    } else {
                        $unitCost = $skuobj->getItemAvgCost($item, $trdate, $area);
                    }

                    if ($runqty < $v['qty']) {
                        $saveok = $invobj->addInventory($runqty, $unitObj, $expirydte, $serialNo, $trdate, $unitCost, $lotNo);
                        $runqty = 0;
                    }
                    else {
                        $saveok = $invobj->addInventory($v['qty'], $unitObj, $expirydte, $serialNo, $trdate, $unitCost, $lotNo);
                        $runqty -= $v['qty'];                    
                    }
                    
                    if (($runqty == 0) || (!$saveok)) break;
                }
            }
            if (($runqty > 0) && ($saveok)) {
                $saveok = $invobj->addInventory($runqty, $unitObj, $expirydte, $serialNo, $trdate, $unitCost, $lotNo);
            }
        }
        
        if ($saveok) $skuobj->clearTmpTable();
        return $saveok;

//		$saveok = $invobj->addInventory($qty, $unitObj, null, null, date('Y-m-d H:i:s'));
//        $saveok = $invobj->addInventory($qty, $unitObj, null, null, $trdate);
//		$this->sql = $invobj->sql;
//		if ($saveok !== false)
//			return true;
//		else
//			return false;  
        
	}

// ---- commented out by LST - 06.05.2013 -- since it is not being used ...    
	/**
	* Removes stocks of an item from the given inventory area; if the quantity
	* specified is
	*
	* @param mixed $item id of the item to be removed
	* @param mixed $area area code of the inventory area
	* @param mixed $qty quantity to remove from stock
    * @param date  $trdate - transaction date
    * @param string $refno - return reference no.
	* @return bool returns TRUE on success, FALSE otherwise
	*/
//	function removeStock($item, $area, $qty, $trdate, $refno = '') {
//		$unitObj = new Unit();
//		$unitObj->unit_id = null;
//		$unitObj->is_unit_per_pc = 1;
//
//		$invobj = new Inventory();   
//        
//        if (empty($trdate)) $trdate = date('Y-m-d');
//        $invobj->setInventoryParams($item, $area, $refno, PHRETURN);        
//
//		$inventory_matrix = $this->_prepare_inventory_matrix($invobj->item_code,$invobj->area_code);
//		if ($inventory_matrix) {
//			$scheme = array();
//			$q1 = (float) $qty;
//			foreach ($inventory_matrix as $mat) {
//				$q2 = (float)$mat[1];
//				if (($q1-$q2)>0) {
//					$saveok = $invobj->remInventory($q2, $unitObj, $mat[0], $trdate);
//					$this->sql = $invobj->sql;
//					$q2 = $q2-$q1;
//				}
//				else {
//					$saveok = $invobj->remInventory($q1, $unitObj, $mat[0]);
//					$this->sql = $invobj->sql;
//					if ($saveok) {
//						return true;
//					}
//				}
//				if (!$saveok) {
//					return false;
//				}
//			}
//		}
//		else {
//			return false;
//		}
//		if ($saveok !== false)
//			return true;
//		else
//			return false;
//	}

    /**
     * @author Marc Lua
     * easier function to be called for removing items in inventory
     * @param $itemCode
     * @param int $qty
     * @param string $area
     * @param string $refNo
     * @param string $transactionCode
     * @return bool
     */
    public function removeStock($itemCode, $qty = 0, $area = '', $refNo = '', $transactionCode = '', $transactionDate = '', $unitId = '') {                        
        global $db;

        //handles when the refno, item code has undergone QSTR
        $refNo = $this->removeQstr($refNo);
        $itemCode = $this->removeQstr($itemCode);
        
//        $qty = $this->removeQstr($qty);
        //get area if no area provided based from ref #
        
//        if (empty($area) && !empty($refNo)) {
//            $orderObj = new SegOrder();
//            $area = $orderObj->getTransactionArea($refNo);
//            if(!$area) {
//                return false;
//            }
//        }

        if(empty($unitId)) {
           $unitId = $this->getPcUnit($itemCode);
        }

        $invObj = new Inventory();
        $invObj->setInventoryParams($itemCode, $area, $refNo, $transactionCode);
        $bsuccess = $invObj->remInventory($qty, $unitId, null, null);
        return $bsuccess;
    }

    private function removeQstr($str) {
        return str_replace(array('\'', '"'), '', $str);
    }

    //Added by EJ 10/14/2014
    function getInventoryBillingStockDetails($refno, $item_code) {
        global $db;

        $this->sql = $db->Prepare("SELECT 
                      smpd.bestellnum AS srv_code,
                      smpd.quantity AS qty,
                      smp.area_code AS area_code,
                      smp.refno AS refno
                    FROM
                      seg_more_phorder AS smp 
                      LEFT JOIN seg_more_phorder_details AS smpd
                      ON smpd.refno = smp.refno
                    WHERE smp.encounter_nr = ? AND smpd.bestellnum = ? 
                     ORDER BY smpd.`entry_no` DESC 
                        LIMIT 1 ");
        if($this->result=$db->Execute($this->sql,array($refno,$item_code))) {
            return $this->result;
        } else { return false; }
    }
}
?>