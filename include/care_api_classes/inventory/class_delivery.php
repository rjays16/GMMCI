<?php
/**
* @package SegHIS_api
*/                                         

/******
*
*   Class containing all properties and methods related to delivery.
*
*   Note this class should be instantiated only after a "$db" adodb  connector object 
*   has been established by an adodb instance.
*
*   @author      :    Lemuel 'Bong' S. Trazo
*   @version     :    1.0
*   @date created:    January 11, 2009
*   @date updated:    February 27, 2009
*
*****/    
require_once('roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory.php');

class Delivery extends Core {     
    /**
    * Table name for delivery header.
    * @var string
    */    
    var $tb_delivery_h = 'seg_delivery';
    /**
    * Table name for delivery details.
    * @var string
    */    
    var $tb_delivery_d = 'seg_delivery_details';
    /**
    * Array of fields in delivery header table.
    * @var array
    */   
    var $hdr_array = array('refno',
                           'po_no',
                           'receipt_date',
                           'receiving_id',
                           'area_code',
                           'pono',
                           'supplier_id',
                           'remarks',
                           'is_deleted',
                           'history',
                           'modify_id',
                           'modify_dt',
                           'create_id',
                           'create_dt',
                           'invoice_no');
    /**
    * Array of fields in delivery details table.
    * @var array
    */   
    var $det_array = array('refno',
                           'item_code',
                           'unit_price',
                           'item_qty',
                           'unit_id',
                           'is_unitperpc',
                           'expiry_date',
                           'serial_no',
                           'lot_no',
                           'qty_per_pck',
                           'small_unit',
                           'is_fg',
                           'manufacturer',);

    function Delivery() {
        $this->useDeliveryHdr();
    }
    
    function useDeliveryHdr() {
        $this->ref_array=$this->hdr_array;
        $this->coretable=$this->tb_delivery_h;    
    }
    
    function useDeliveryDetails() {
        $this->ref_array=$this->det_array;
        $this->coretable=$this->tb_delivery_d;
    }
    
    function getLastNr($today) {
        global $db;
        $today = $db->qstr($today);
        $this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->tb_delivery_h WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
        return $db->GetOne($this->sql);
    }    
    
    /**
    * @internal     Save delivery detail and update the inventory accordingly.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        area_code   - area of department where delivery is made.
    * @param        array (refno, item_code, unit_price, item_qty, unit_id, is_unitperpc)
    * @param        trns_dte    - date of delivery.
    * @param        expiry_date - expiry date of item if any.
    * @param        serial      - object describing the item's serial information.
    * @return       boolean TRUE if successful, FALSE otherwise.
    */     
    function saveDeliveryDetail($area_code, $data, $trns_dte, $expiry_date=NULL, $serial=NULL, $lotNo = '', $fg_items = NULL,$manufacturer = '') {
        global $db; 
    
        if(!is_array($data)){ return FALSE;}                 
        
        $invobj = new Inventory();
        $invobj->area_code = $area_code;
        $invobj->item_code = $data["item_code"];
        $invobj->refno = $data["refno"];
        $invobj->trcode = RECEIVE;
        
        $sqlfilter = array();
        
        if (!is_null($expiry_date)) {        
            $data['expiry_date'] = $expiry_date;
            $sqlfilter[] = "expiry_date = '$expiry_date'";
        }
        
        if (!is_null($serial)) {
            $invobj->setSerial($serial);
            $data['serial_no'] = $serial->serial_no;
            $sqlfilter[] = "serial_no = '".$serial->serial_no."'"; 
        }

        //Added by Jarel 06172015
        if (!is_null($fg_items)) {
            $data['is_fg'] = $fg_items;
            $sqlfilter[] = "is_fg = '$fg_items'";
        }
        //End

        if (!empty($lotNo)) {
            $data['lot_no'] = $lotNo;
            $sqlfilter[] = "lot_no = '".$lotNo."'";
        }

        if (!empty($manufacturer)) {
            $data['manufacturer'] = $manufacturer;
            $sqlfilter[] = "manufacturer = '".$manufacturer."'";
        }


        
        if (!empty($sqlfilter)) 
            $strfilter = "and (".implode(") and (", $sqlfilter).") ";            
        else
            $strfilter = "";       
            
//        if ($bSuccess) {

        $strSQL = "select * from $this->coretable 
                      where refno = '".$data['refno']."' 
                         and item_code = '".$data['item_code']."'
                         and unit_price = ".$data['unit_price']."

                         and unit_id = ".$data['unit_id']." ".$strfilter."for update";

        $result = $db->Execute($strSQL);
        if (!$result->EOF) { 
            $strSQL = "update $this->coretable set 
                             item_qty = item_qty + ".$data['item_qty'].",
                             qty_per_pck = ".$data['qty_per_pck']." ,
                             is_fg = ".$data['is_fg']."
                            where refno = '".$data['refno']."'
                             and item_code = '".$data['item_code']."'
                             and unit_price = ".$data['unit_price']."

                             and unit_id = ".$data['unit_id']." ".$strfilter;                        
            $bSuccess = $db->Execute($strSQL);            
             if (!$bSuccess) $this->setErrorMsg($db->ErrorMsg());    
        }                         
        else {

            $this->setDataArray($data); 
            $bSuccess = $this->insertDataFromInternalArray();

            if (!$bSuccess) $this->setErrorMsg($db->ErrorMsg());
        }                
        
//        }        
        if (!$bSuccess) $this->setErrorMsg($db->ErrorMsg());
        
        $unitobj = new Unit();

        if(!$data['is_unitperpc']){
           
          //  $unitobj->unit_id = $data['unit_id'];
            $unitobj->is_unit_per_pc = 1;
           // $data['item_qty'] = floatval($data['item_qty']) * floatval($data['qty_per_pck']);
        }else{
           // $unitobj->unit_id = $data['unit_id'];
        $unitobj->is_unit_per_pc = $data['is_unitperpc'];
        }
        
        $unitobj->unit_id = $data['unit_id'];
        $data['item_qty'] = floatval($data['item_qty']) * floatval($data['qty_per_pck']);
        
        if ($bSuccess) {
            $bSuccess = $invobj->addInventory($data["item_qty"], $unitobj, $expiry_date, $serial->serial_no, $trns_dte, $data["unit_price"], $lotNo);
            if (!$bSuccess) $this->setErrorMsg($db->ErrorMsg());
        }
        
//        if ($bSuccess) $db->Execute("CALL sp_clear_delivery_details_tmp");
                
//        if ($bSuccess) {
//            $strSQL = "select * from $this->coretable 
//                          where refno = '".$data['refno']."' 
//                             and item_code = '".$data['item_code']."' 
//                             and unit_id = ".$data['unit_id']." ".$strfilter."for update";
//                             
//            $result = $db->Execute($strSQL);
//            if (!$result->EOF) { 
//                $strSQL = "update $this->coretable set 
//                                 item_qty = item_qty + ".$data['item_qty']."
//                              where refno = '".$data['refno']."' 
//                                 and item_code = '".$data['item_code']."' 
//                                 and unit_id = ".$data['unit_id']." ".$strfilter; 
//                $bSuccess = $db->Execute($strSQL);   
//            }                         
//            else {
//                $this->setDataArray($data); 
//                $bSuccess = $this->insertDataFromInternalArray();
//            }
//        }

        return $bSuccess;                
    }  
    
    /**
    * @internal     Delete delivery transaction.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        ref_no   - reference no. of delivery transaction
    * @return       boolean TRUE if successful, FALSE otherwise.
    */     
    function delDelivery($refno) {
        global $db;
        
        $bSuccess = true;                
        
        $trns_dte = $this->getDeliveryDate($refno);
        if ($trns_dte) {
            $this->startTrans();
            
            $bSuccess = $this->delDeliveryDetail($refno, $trns_dte);
            if ($bSuccess) {
                $this->sql = "delete from $this->tb_delivery_h ".
                             "   where refno = '$refno'";
                $bSuccess = $db->Execute($this->sql);
            }
            
            if (!$bSuccess) $this->failTrans();
            $this->completeTrans();            
        }
        
        return $bSuccess;
    } 
        
    /**
    * @internal     Delete delivery detail and update the inventory accordingly.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        ref_no   - reference no. of delivery transaction
    * @param        trns_dte - transaction date (delivery date)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */ 
    function delDeliveryDetail($refno, $trns_dte) {
        global $db;
        
//        $bSuccess = true;
        
        // Update the inventory of each item in the delivery detail to delete before 
        // deleting the delivery transaction ...
//        $this->sql = "select * from $this->tb_delivery_d 
//                      where refno = '$refno'";
//        $result = $db->Execute($this->sql);
//        if ($result->RecordCount()) {
//            $invobj = new Inventory();
//            $unitobj = new Unit();
//            
//            $invobj->area_code = $this->getCurrentAreaCode($refno);                        
//            
//            while ($row = $result->FetchRow()) {                            
//                $unitobj->unit_id = $row['unit_id'];
//                $unitobj->is_unit_per_pc = $row['is_unitperpc'];
//                
//                $invobj->item_code = $row["item_code"];                
//                $bSuccess = $invobj->remInventory($row["item_qty"], $unitobj, $row['expiry_date'], $row['serial_no'], $trns_dte);
//                if (!$bSuccess) {
//                    $this->sql = $invobj->sql;    
//                    break;
//                }
//            }                                      
//        }  
        
//        if ($bSuccess) {
            // ... if successfully update the inventory, delete the details of delivery.
        $this->sql = "delete from $this->tb_delivery_d ".
                     "   where refno = '$refno'";
        $bSuccess = $db->Execute($this->sql);

        if ($bSuccess) $db->Execute("CALL sp_clear_seginventoryledger_tmp");    
            
//            if ($bSuccess) $db->Execute("CALL sp_clear_delivery_details_tmp");                        
//        }                    
        
        return $bSuccess;       
    }        
    
    /**
    * @internal     Extract the history of deliveries given a particular filter.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        filters - array of filters.
    * @param        offset - from which record.
    * @param        rowcount - how many records (rows) to extract.
    * @return       resultset of extracted records.
    */     
    function getPostedDeliveries($filters, $offset=0, $rowcount=15, $areas = '') {
        global $db;

        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;
        
        if (is_array($filters)) {
            foreach ($filters as $i=>$v) {
                switch (strtolower($i)) {
                    case 'datetoday':
                        $phFilters[] = 'DATE(receipt_date)=DATE(NOW())';
                        break;
                    case 'datethisweek':
                        $phFilters[] = 'YEAR(receipt_date)=YEAR(NOW()) AND WEEK(receipt_date)=WEEK(NOW())';
                        break;
                    case 'datethismonth':
                        $phFilters[] = 'YEAR(receipt_date)=YEAR(NOW()) AND MONTH(receipt_date)=MONTH(NOW())';
                        break;
                    case 'date':
                        $phFilters[] = "DATE(receipt_date)='$v'";
                        break;
                    case 'datebetween':
                        $phFilters[] = "DATE(receipt_date)>='".$v[0]."' AND DATE(receipt_date)<='".$v[1]."'";
                        break;
                    case 'name':
                        $phFilters[] = "concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) REGEXP '[[:<:]]".substr($db->qstr($v),1);
                        break;
                    case 'item_desc':
                        $phFilters[] = "(select count(sdd3.refno) as ncount  
                            from seg_delivery_details as sdd3 inner join care_pharma_products_main as cppm on sdd3.item_code = cppm.bestellnum
                            where cppm.artikelname REGEXP ".$db->qstr($v)." and sdd3.refno = sd.refno) > 0";
                        break;
                    case 'ref_no':
                        $phFilters[] = "sd.refno REGEXP ".$db->qstr($v);
                        break;
                }
            }
        }
        
        if ($areas != '') {
            $phFilters[] = "area_code in ($areas)";    
        }        
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere) and is_deleted = 0";
        else $phWhere = "is_deleted = 0";        

        $this->sql = "select SQL_CALC_FOUND_ROWS sd.refno, receipt_date, remarks, concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as received_by, 
                        (select GROUP_CONCAT(DISTINCT artikelname ORDER BY artikelname DESC SEPARATOR ', ') as items 
                            from seg_delivery_details as sdd inner join care_pharma_products_main as cppm on sdd.item_code = cppm.bestellnum
                            group by sdd.refno having sdd.refno = sd.refno) as particulars, 
                        (select sum(unit_price * item_qty * qty_per_pck) as total 
                            from seg_delivery_details as sdd2 
                            group by sdd2.refno having sdd2.refno = sd.refno) as amount 
                        from (seg_delivery as sd inner join care_personell as cpl on 
                           sd.receiving_id = cpl.nr) inner join care_person as cp on cpl.pid = cp.pid 
                        where ($phWhere) 
                        order by receipt_date asc 
                        limit $offset, $rowcount";                           
        
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }                
    }
    
    /**
    * @internal     Return the area_code of particular delivery.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        srefno - reference no. to identify the delivery.
    * @return       the area_code.
    */         
    function getCurrentAreaCode($srefno) {
        global $db;  
        
        $this->sql = "select area_code from $this->tb_delivery_h where refno = '$srefno'";
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                return $row["area_code"];
            }
            else
                return false;
        } else { return false; }
    }
    
    /**
    * @internal     Return the delivery date of particular delivery.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        srefno - reference no. to identify the delivery.
    * @return       the delivery date.
    */      
    function getDeliveryDate($srefno) {
        global $db;  
        
        $this->sql = "select receipt_date from $this->tb_delivery_h where refno = '$srefno'";
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount()) {
                $row = $this->result->FetchRow();
                return $row["receipt_date"];
            }
            else
                return false;
        } else { return false; }
    }
    
    /**
    * @internal     Return the header information of particular delivery.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        srefno - reference no. to identify the header information.
    * @return       resultset of extracted records.
    */ 
    function getDeliveryHeader($srefno) {
        global $db;  
        
        $this->sql = "select h.*, concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as received_by
                         from ($this->tb_delivery_h as h inner join care_personell as cpl on h.receiving_id = cpl.nr) 
                            inner join care_person as cp on cpl.pid = cp.pid 
                         where refno = '$srefno'";                         
        if($this->result=$db->Execute($this->sql)) {
            if ($this->result->RecordCount())
                return $this->result->FetchRow();
            else
                return false;
        } else { return false; }
    }
    
    /**
    * @internal     Return the details of particular delivery.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        srefno - reference no. to identify the delivery details.
    * @return       resultset of extracted records.
    */     
    function getDeliveryDetails($srefno) {
        global $db;
        
        $this->sql = "select * from $this->tb_delivery_d where refno = '$srefno'";
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }        
    }
    
    function clearTmpTable() {
//        return $this->Transact("CALL sp_clear_delivery_details_tmp");
        return $this->Transact("CALL sp_clear_seginventoryledger_tmp");        
    }

    /**
    * put your comment there...
    * 
    * @author Alvin Qui�ones
    * @param mixed $itemCode
    * @return mixed
    */
    public function getLatestPurchasePrice($itemCode) {
        global $db;
        $this->sql = "SELECT `details`.`unit_price`\n" .
            "FROM seg_delivery_details `details`\n" .
            "INNER JOIN seg_delivery `header` ON `details`.`refno`=`header`.`refno`\n" .
            "WHERE\n" . 
                "`details`.`item_code` = " . $db->qstr($itemCode) . "\n" .
                "AND `details`.`unit_price` > 0.0\n" .
            "ORDER BY `header`.`receipt_date` DESC\n";
            
        return $db->GetOne($this->sql);
    }
} 
?>