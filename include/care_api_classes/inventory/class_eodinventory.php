<?php
/*
 * @package care_api
 * Class of EOD Inventory.
 *
 * Created: January 15, 2009 (Bryan)
 * Modified: January 15, 2009 (LST)
 */
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/inventory/class_item.php');
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
//require_once($root_path.'include/care_api_classes/alerts/class_alert.php');
  
class EODInventory extends Core{
     #created by Bryan     
    /**
    * Table name 
    * @var string
    */    
    var $tb_eod = 'seg_eod_inventory'; # EOD (end-of-day) inventory table
    /**
    * Table name 
    * @var string
    */    
    var $tb_eod_expiry = 'seg_eod_expiry_inventory'; # EOD (end-of-day) inventory table    
    /*
    * @var String
    */
    var $item_code;
    /*
    * @var String
    */
    var $area_code;                          
    /*
    * @var Date
    */
    var $eod_date;
    /*
    * @var Double
    */
    var $eod_qty;
    
    
    /**
    * @internal     Adds item in EOD inventory.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        array (item_code, area_code, eod_date, expiry_date, eod_qty)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */         
    function addEODInventory($data) {
        global $db;
        
        if (empty($data)) return FALSE;
        
        $bSuccess = FALSE;                
        if (!isset($data['eod_date'])) 
            $data['eod_date'] = strftime("%Y-%m-%d");
        else
            $data['eod_date'] = strftime("%Y-%m-%d", strtotime($data['eod_date']));
        
        $expiry_dt = ($data['expiry_date'] != NULL) ? $data['expiry_date'] : "0000-00-00"; 
        $serial_no = ($data['serial_no'] != NULL) ? $data['serial_no'] : "";
        
        $this->sql = "SELECT eod_qty FROM $this->tb_eod 
                          WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' 
                             AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$expiry_dt."'
                             AND serial_no = '".$serial_no."') FOR UPDATE"; 
        $result = $db->Execute($this->sql);
        if (!$result->EOF) { 
            $this->sql = "UPDATE $this->tb_eod SET 
                              eod_qty = eod_qty + ".$data['eod_qty']." 
                              WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' 
                                 AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$expiry_dt."'
                                 AND serial_no = '".$serial_no."')";
            $bSuccess = $db->Execute($this->sql);
        }
        else { 
            $prevqty = $this->getRecentEODQty($data['item_code'], $data['area_code'], $data['eod_date'], $expiry_dt, $serial_no);                        
            $this->sql = "INSERT INTO $this->tb_eod (item_code, area_code, eod_date, expiry_date, serial_no, eod_qty) 
                              VALUES ('".$data['item_code']."','".$data['area_code']."','".$data['eod_date']."', '".$expiry_dt."', 
                              '".$serial_no."', ".($prevqty + $data['eod_qty']).")";
            $bSuccess = $db->Execute($this->sql);
        }      
        
        // Update later EOD inventory ...
        if ($bSuccess) {
            $this->sql = "select * from $this->tb_eod 
                              where (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' 
                              AND expiry_date = '".$expiry_dt."' AND serial_no = '".$serial_no."' 
                              AND eod_date > '".$data['eod_date']."') for update";
            $result = $db->Execute($this->sql);
            if (!$result->EOF) { 
                $this->sql = "UPDATE $this->tb_eod SET 
                                  eod_qty = eod_qty + ".$data['eod_qty']." 
                                  WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' 
                                     AND expiry_date = '".$expiry_dt."' AND serial_no = '".$serial_no."'
                                     AND eod_date > '".$data['eod_date']."')";
                $bSuccess = $db->Execute($this->sql);
            }        
        }
        
        return $bSuccess;      
    }    
    
    /**
    * @internal     Adds item in EOD expiry inventory.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        array (item_code, area_code, eod_date, expiry_date, eod_qty)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */         
   // function addEODExpiryInventory($data) {
//        global $db;
//        
//        if (empty($data)) return FALSE;
//        
//        $bSuccess = FALSE;                
//        if (!$data['eod_date']) $data['eod_date'] = strftime("%Y-%m-%d %H:%M:%S");
//        
//        $sqlSTR = "SELECT eod_qty FROM $this->tb_eod_expiry WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$data['expiry_date']."') FOR UPDATE"; 
//        $result = $db->Execute($sqlSTR);
//        if (!$result->EOF) { 
//            $sqlSTR = "UPDATE $this->tb_eod_expiry SET 
//                          eod_qty = eod_qty + ".$data['eod_qty']." 
//                          WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$data['expiry_date']."')";
//            $bSuccess = $db->Execute($sqlSTR);
//        }
//        else {
//            $sqlSTR = "INSERT INTO $this->tb_eod_expiry (item_code, area_code, eod_date, expiry_date, eod_qty) 
//                          VALUES ('".$data['item_code']."','".$data['area_code']."','".$data['eod_date']."', '". $data['expiry_date']."', ".$data['eod_qty'].")";
//            $bSuccess = $db->Execute($sqlSTR);
//        }      
//        
        // Update later EOD inventory ...
//        if ($bSuccess) {
//            $sqlSTR = "select * from $this->tb_eod_expiry 
//                          where (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' AND expiry_date = '".$data['expiry_date']."' AND eod_date > '".$data['eod_date']."') 
//                          for update";
//            $result = $db->Execute($sqlSTR);
//            if (!$result->EOF) { 
//                $sqlSTR = "UPDATE $this->tb_eod_expiry SET 
//                              eod_qty = eod_qty + ".$data['eod_qty']." 
//                              WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' AND expiry_date = '".$data['expiry_date']."' AND eod_date > '".$data['eod_date']."')";
//                $bSuccess = $db->Execute($sqlSTR);
//            }        
//        }
//        
//        return $bSuccess;      
//    }     
//    
    /**
    * @internal     Removes item in EOD inventory.
    * @access       public
    * @author       Bryan Inno N. Wong
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        array (item_code, area_code, eod_date, eod_qty)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */         
    function removeEODInventory($data) {
        global $db;
        
        if (empty($data)) return FALSE;
//        $alert_obj =  new SegAlert();
        
        $bSuccess = FALSE;                
        if (!isset($data['eod_date'])) 
            $data['eod_date'] = strftime("%Y-%m-%d");
        else
            $data['eod_date'] = strftime("%Y-%m-%d", strtotime($data['eod_date']));  
        
        $expiry_dt = ($data['expiry_date'] != NULL) ? $data['expiry_date'] : "0000-00-00"; 
        $serial_no = ($data['serial_no'] != NULL) ? $data['serial_no'] : "";        
        
        $this->sql = "SELECT eod_qty FROM $this->tb_eod 
                         WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' 
                            AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$expiry_dt."'
                            AND serial_no = '".$serial_no."') FOR UPDATE"; 
        $result = $db->Execute($this->sql);
        if (!$result->EOF) {
            $row = $result->FetchRow();
            $neod_qty = (is_null($row['eod_qty']) ? 0 : $row['eod_qty']);
            if($neod_qty != $data['qty'])
            {
                $this->sql = "UPDATE $this->tb_eod SET 
                                 eod_qty = eod_qty - ".$data['eod_qty']." 
                                 WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' 
                                    AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$expiry_dt."'
                                    AND serial_no = '".$serial_no."')";
                $bSuccess = $db->Execute($this->sql);
            }
            else {
                 $this->sql = "DELETE FROM $this->tb_eod WHERE (item_code='$this->item_code' AND area_code='$this->area_code' 
                                  AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$expiry_dt."'
                                  AND serial_no = '".$serial_no."')";
                 $bSuccess = $db->Execute($this->sql);
            }             
        }     
        else {
            $prevqty = $this->getRecentEODQty($data['item_code'], $data['area_code'], $data['eod_date'], $expiry_dt, $serial_no);
            $this->sql = "INSERT INTO $this->tb_eod (item_code, area_code, eod_date, expiry_date, serial_no, eod_qty) 
                             VALUES ('".$data['item_code']."','".$data['area_code']."','".$data['eod_date']."', 
                                     '".$expiry_dt."', '".$serial_no."', ".($prevqty - $data['eod_qty']).")";
            $bSuccess = $db->Execute($this->sql);           
        }
        
//        $sqlSTR = "SELECT eod_qty FROM $this->tb_eod 
//                      WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' 
//                         AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$expiry_dt."'
//                         AND serial_no = '".$serial_no."') FOR UPDATE"; 
//        $result = $db->Execute($sqlSTR);
//        if (!$result->EOF) {
//            $row = $result->FetchRow();
//            $neod_qty = (is_null($row['eod_qty']) ? 0 : $row['eod_qty']);
//            
//            $objitm = new Item();
//            $min_qty = $objitm->getMinQty($data['item_code']);
//            
//            $fetchname = "SELECT area_name FROM seg_areas WHERE area_code='".$data['area_code']."'";
//            $resultname = $db->Execute($fetchname);
//            $rowname = $resultname->FetchRow();
//            
//            if($neod_qty <= $min_qty) { 
//                if ($neod_qty >= 0) 
//                    $message = "Quantity running low for ".$objitm->getItemDesc($this->item_code)." in ".$rowname['area_name'];
//                else
//                    $message = "Quantity of ".$objitm->getItemDesc($this->item_code)." in inventory is negative in ".$rowname['area_name'];
//                $alert_obj->postAlert("SUP", 6, "", "Critical Quantity", $message, "H", "");
//            }   
//        }
                
        // Update later EOD inventory ...
        if ($bSuccess) {
            $this->sql = "UPDATE $this->tb_eod SET 
                             eod_qty = eod_qty - ".$data['eod_qty']." 
                             WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' 
                                AND expiry_date = '".$expiry_dt."' AND serial_no = '".$serial_no."' 
                                AND eod_date > '".$data['eod_date']."')";
            $bSuccess = $db->Execute($this->sql);    
        }
        
//        if (!$bSuccess) $this->sql = $db->ErrorMsg();
        return $bSuccess;      
    }    
        


//		/**
//		* Returns the consolidated stock count for a specific item based on a given reference date
//		*
//		* @param string $item Item code of the item
//		* @param string $areaCode Area code of the inventory area
//		* @param date $referenceDate
//		* @param bool $byExpiry Return an array with individual expiry date as key and the corresponding stock count as values
//		*
//		* @return int
//		*/
//		public function getStockCount($item, $areaCode=null, $referenceDate=null, $byExpiry=false) {
//			global $db;
//
//			$where = array(
//				'item_code=' . $db->qstr($item)
//			);
//			if (!empty($areaCode)) {
//				$where[] = 'area_code=' . $db->qstr($areaCode);
//			}
//			if (empty($referenceDate)) {
//				$referenceDate=date('Y-m-d');
//			}
//			$where[] = 'eod_date<=' . $db->qstr($referenceDate);
//
//			$this->sql =
//				"SELECT\n".
//					"eod.expiry_date,SUBSTRING(MAX(CONCAT(eod.eod_date,eod.eod_qty)),11) qty\n".
//				"FROM seg_eod_inventory eod\n".
//				"WHERE (\n" .
//					implode(")\nAND (", $where) . ")\n" .
//				"GROUP BY eod.item_code,eod.area_code" . ($byExpiry ? ',eod.expiry_date' : '');
//
//			if (($result=$db->Execute($this->sql)) !== false) {
//				$rows = $result->GetRows();
//				$stockCount = array();
//				foreach ($rows as $row) {
//					$stockCount[$row['expiry_date']] = $row['qty'];
//				}
//				if ($byExpiry) {
//					return $stockCount;
//				}
//				else {
//					$count = 0;
//					foreach ($stockCount as $stock) {
//						$count += $stock;
//					}
//					return $count;
//				}
//			}
//			return false;
//		}



    /**
    * @internal     Gets the most recent EOD quantity.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        itm_code    --  item code
    * @param        area_code   --  inventory area
    * @param        eod_date    --  current date
    * @param        expiry_date
    * @param        serial no.
    * @return       double
    */     
    function getRecentEODQty($itm_code, $area_code, $eod_date, $expiry_dt='0000-00-00', $serial_no='') {
        $skuobj = new SKUInventory();        
        $nqty = $skuobj->getRecentItemQty($itm_code, $eod_date, $area_code, $expiry_dt, $serial_no);
        return $nqty;
                
//        global $db;
//        
//        $this->sql = "select eod_qty from $this->tb_eod 
//                      where (item_code='".$itm_code."' AND area_code='".$area_code."' 
//                         AND expiry_date = '".$expiry_dt."' AND serial_no = '".$serial_no."'
//                         AND eod_date < '".$eod_date."') order by eod_date desc limit 1";
//                      
//        $nqty = 0;
//        if ($result = $db->Execute($this->sql)) {
//            if ($result->RecordCount()) {
//                $row = $result->FetchRow();
//                $nqty = is_null($row["eod_qty"]) ? 0 : $row["eod_qty"]; 
//            }
//        }      
//        return $nqty;        
    }
    
    /**
    * @internal     Gets the current EOD quantity based on eod_date.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        itm_code    --  item code
    * @param        area_code   --  inventory area
    * @param        eod_date    --  current date
    * @param        expiry_date
    * @param        serial no.
    * @return       double
    */     
    function getCurrentEODQty($itm_code, $area_code, $eod_date, $expiry_dt='0000-00-00', $serial_no='') {
        $skuobj = new SKUInventory();        
        $nqty = $skuobj->getItemQty($itm_code, $eod_date, $area_code, $expiry_dt, $serial_no);
        return $nqty;
        
//        global $db;
//				if($serial_no==''){
//						$this->sql = "select eod_qty from $this->tb_eod
//													where (item_code='".$itm_code."' AND area_code='".$area_code."'
//														 AND expiry_date = '".$expiry_dt."'
//														 AND eod_date <= '".$eod_date."') order by eod_date desc limit 1";
//				}
//				else{
//        $this->sql = "select eod_qty from $this->tb_eod 
//                      where (item_code='".$itm_code."' AND area_code='".$area_code."' 
//                         AND expiry_date = '".$expiry_dt."' AND serial_no = '".$serial_no."'
//                         AND eod_date <= '".$eod_date."') order by eod_date desc limit 1";
//				}
//                      
//        $nqty = 0;
//        if ($result = $db->Execute($this->sql)) {
//            if ($result->RecordCount()) {
//                $row = $result->FetchRow();
//                $nqty = is_null($row["eod_qty"]) ? 0 : $row["eod_qty"]; 
//            }
//        }  
//        return $nqty;        
    }
    
    /**
    * @internal     Gets the current inventory quantity regardless of expiry date or serial no.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        itm_code    --  item code
    * @param        area_code   --  inventory area
    * @param        eod_date    --  current date 
    * @return       double
    */     
    function getCurrentQty($itm_code, $area_code, $eod_date) {
        $skuobj = new SKUInventory();        
        $nqty = $skuobj->getItemQty($itm_code, $eod_date, $area_code);
        return $nqty;        
        
//        global $db;
//        
//        $this->sql = "select sum(eod_qty) as eod_qty 
//                        from(
//                        select eod_date, eod_qty from seg_eod_inventory as e1 
//                          where (item_code = '$itm_code'
//                             and area_code = '$area_code'
//                             and eod_date <= '$eod_date'    
//                             and eod_date = (select max(eod_date) from seg_eod_inventory as e2 
//                                                where e2.item_code = e1.item_code 
//                                                   and e2.area_code = e1.area_code
//                                                   and e2.expiry_date = e1.expiry_date
//                                                   and e2.serial_no = e1.serial_no)
//                        )
//                        group by eod_date) as t";
//        $nqty = 0;
//        if ($result = $db->Execute($this->sql)) {
//            if ($result->RecordCount()) {
//                $row = $result->FetchRow();
//                $nqty = is_null($row["eod_qty"]) ? 0 : $row["eod_qty"]; 
//            }
//        }  
//        return $nqty;                
    }    
    
    /**
    * @internal     Gets the most recent EOD Expiry quantity.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        itm_code    --  item code
    * @param        area_code   --  inventory area
    * @param        expiry_dt   --  expiry date of item
    * @param        eod_date    --  current date
    * @return       double
    */     
    //function getRecentEODExpiryQty($itm_code, $area_code, $expiry_dt, $eod_date) {
//        global $db;
//        
//        $strSQL = "select eod_qty from $this->tb_eod_expiry 
//                      where (item_code='".$itm_code."' AND area_code='".$area_code."' AND expiry_date = '".$expiry_dt."' AND eod_date < '".$eod_date."')
//                      order by eod_date desc limit 1";
//                      
//        $nqty = 0;
//        if ($result = $db->Execute($strSQL)) {
//            if ($result->RecordCount()) {
//                $row = $result->FetchRow();
//                $nqty = is_null($row["eod_qty"]) ? 0 : $row["eod_qty"]; 
//            }
//        }      
//        return $nqty;        
//    }    
//    
    /**
    * @internal     Removes item in EOD expiry inventory.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        array (item_code, area_code, eod_date, expiry_date, eod_qty)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */         
 //   function removeEODExpiryInventory($data) {
//        global $db;
//        
//        if (empty($data)) return FALSE;
//        
//        $bSuccess = FALSE;                
//        if (!$data['eod_date']) $data['eod_date'] = strftime("%Y-%m-%d %H:%M:%S");
//        
//        $sqlSTR = "SELECT eod_qty FROM $this->tb_eod_expiry WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$data['expiry_date']."') FOR UPDATE"; 
//        $result = $db->Execute($sqlSTR);
//        if (!$result->EOF) {
//            $row = $result->FetchRow();
//            if($row['eod_qty']!=$data['qty'])
//            {
//                $sqlSTR = "UPDATE $this->tb_eod_expiry SET 
//                          eod_qty = eod_qty - ".$data['eod_qty']."
//                          WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$data['expiry_date']."')";
//                $bSuccess = $db->Execute($sqlSTR);
//            }
//            else {
//                 $sqlSTR = "DELETE FROM $this->tb_eod_expiry WHERE (item_code='$this->item_code' AND area_code='$this->area_code' AND eod_date = '".$data['eod_date']."' AND expiry_date = '".$data['expiry_date']."')";
//                 $bSuccess = $db->Execute($sqlSTR);
//            }             
//        }     
//        else {
//            $prevqty = $this->getRecentEODExpiryQty($data['item_code'], $data['area_code'], $data['expiry_date'], $data['eod_date']);
//            $sqlSTR = "INSERT INTO $this->tb_eod_expiry (item_code, area_code, eod_date, expiry_date, eod_qty) 
//                          VALUES ('".$data['item_code']."','".$data['area_code']."','".$data['eod_date']."', '". $data['expiry_date']."', ".$data['eod_qty'].")";
//            $bSuccess = $db->Execute($sqlSTR);            
//            
//            
//        }
//        
        // Update later EOD inventory ...
//        if ($bSuccess) {
//            $sqlSTR = "select * from $this->tb_eod_expiry 
//                          where (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' AND expiry_date = '".$data['expiry_date']."' AND eod_date > '".$data['eod_date']."') 
//                          for update";
//            $result = $db->Execute($sqlSTR);
//            if (!$result->EOF) { 
//                $sqlSTR = "UPDATE $this->tb_eod_expiry SET 
//                              eod_qty = eod_qty - ".$data['eod_qty']." 
//                              WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."' AND expiry_date = '".$data['expiry_date']."' AND eod_date > '".$data['eod_date']."')";
//                $bSuccess = $db->Execute($sqlSTR);
//            }        
//        }
//        
//        return $bSuccess;      
//    }    

    function getAreasContainingItem($item='') {
        global $db;
        
        $this->sql = "SELECT 
                        DISTINCT sku.`area_code`
                      FROM
                        seg_sku_catalog sku INNER JOIN ( SELECT 
                                                            sel.`sku_id`, SUM(mvmnt_qty * packqty) qty 
                                                            FROM
                                                            seg_inventory_ledger sel 
                                                            WHERE tr_date <= DATE(NOW()) 
                                                            GROUP BY sku_id ) t 
                           ON sku.`sku_id` = t.sku_id      
                          WHERE t.qty > 0 AND item_code = '{$item}' 
                      ORDER BY expiry_date";
        
//        $sql = "SELECT DISTINCT area_code FROM $this->tb_eod WHERE item_code='$item'";
        if($result = $db->Execute($this->sql)){
            return $result;
        }
        return false;  
    }

}
?>