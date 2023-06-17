<?php
/*
 * @package care_api
 * Class of EOD Inventory.
 *
 * Created:  January 15, 2009 (Bryan)
 * Modified: January 15, 2009 (LST)
 *           May 24, 2013 - Reengineered inventory module (LST)
 */
require_once($root_path.'include/care_api_classes/class_core.php');
//require_once($root_path.'include/care_api_classes/inventory/class_item.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
  
class SegAdjustment extends Core{
     #created by Bryan     

    //class tables 
    var $tb_adjustment = 'seg_inventory_adjustment';
    var $tb_adjustment_details = 'seg_inventory_adjustment_details'; 
    var $tb_adjustment_reason = 'seg_inventory_adjustment_reason';
      
    var $fld_adjustment = array(
        "refno",
        "adjust_date",
        "adjusting_id",
        "area_code",
        "adjust_reason",
        "remarks",
        "is_deleted",
        "history",
        "modify_id",
        "modify_dt",
        "create_id",
        "create_dt",
    ); 
    
    var $fld_adjustment_details = array(
        "refno",
        "item_code",
        "unit_id",
        "is_unitperpc",
        "expiry_date",
        "serial_no",
        "orig_qty",
        "adj_qty",
        "reason",
        "lot_no"
    );   
    
    function prepareAdjustment() {
        $this->coretable = $this->tb_adjustment;
        $this->setRefArray($this->fld_adjustment);
    }
    
    function prepareAdjustmentDetails() {
        $this->coretable = $this->tb_adjustment_details;
        $this->setRefArray($this->fld_adjustment_details);
    }

    /**
     * Return HTML string for dropdown adjustment reasons
     * based from seg_inventory_adjustment_reason
     *
     * @param string $sclass Defaults to Physical Count
     * @return string
     */
    function getAdjustReasonOptions($sclass = 'PC')
    {
        $result = $this->getAllAdjustReasons();
        $reasons = $result->getArray();
        $soption = '';
        foreach ($reasons as $r) {
            if ($sclass == $r['adj_reason_id']) {
                $soption .= "<option value='" . $r['adj_reason_id'] . "' selected>" . $r['adj_reason_name'] . "</option>";
            } else {
                $soption .= "<option value='" . $r['adj_reason_id'] . "'>" . $r['adj_reason_name'] . "</option>";
            }
        }
        return $soption;
    }
    
    function getAllAdjustReasons() {
        global $db;
        
        $this->sql = "SELECT * FROM $this->tb_adjustment_reason ORDER BY adj_reason_name";
        $this->result = $db->Execute($this->sql);
        
        return $this->result;
    }
    
    /**
    * @internal     returns options for existing expiry dates of drug
    * @access       public
    * @author       Bryan Inno Wong
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        item_code    --  item_code of product
    * @param        area_code    --  area for consideration
    * @return       String
    */    
    function getExpiryofIteminArea($item_code, $area_code) {       
        $soption = '';
        $skuobj = new SKUInventory();
        $expirydates = $skuobj->getExpiryDates($item_code, '', $area_code);
        if ($expirydates) {
            $soption = '<option value = \'\'>-- Select --</option>'; 
            while($row = $expirydates->FetchRow()) {
                if(!is_null($row['expiry_date'])){
                    $soption .= '<option value = \''.$row['expiry_date'].'\'>'.$row['expiry_date'].'</option>';    
                }
            }            
        }                
        
        return $soption;                            
    }  
    
    //added by bryan 102709    
    function getEarliestExpiryofIteminArea($item_code, $area_code) {
        global $db;
        
        $soption = '';
        $this->sql = $db->Prepare("SELECT DISTINCT expiry_date FROM seg_eod_inventory WHERE item_code= ? AND area_code= ? ORDER BY expiry_date ASC");
        if($this->result = $db->GetOne($this->sql,array($item_code,$area_code))){
            $soption = $this->result;
        }

        return $soption;
    
    }  
    
    /**
    * @internal     returns options for serial nos
    * @access       public
    * @author       Bryan Inno Wong
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        item_code    --  item_code of product
    * @param        area_code    --  area for consideration
    * @return       String
    */    
    function getSerialofIteminArea($item_code, $area_code) {
        global $db;
        $counter = 0;
        
        $soption = '';
        $this->sql = $db->Prepare("SELECT DISTINCT serial_no FROM seg_eod_inventory WHERE item_code= ? AND area_code= ? AND serial_no!=''");
        $this->result = $db->Execute($this->sql,array($item_code,$area_code));
        $counter = $this->result->RecordCount();
        if($counter>0){
            $soption = '<option value = \'\'>-- Select --</option>'; 
            while($row = $this->result->FetchRow())
            {
                $soption .= '<option value = \''.$row['serial_no'].'\'>'.$row['serial_no'].'</option>';    
            }
        }
        return $soption;
    
    }
    
    function getReasonName($reason_id){
        global $db;
        
        $this->sql = "SELECT adj_reason_name FROM $this->tb_adjustment_reason WHERE adj_reason_id='$reason_id'";
        $this->result = $db->Execute($this->sql);
        $row = $this->result->FetchRow();
        
        return $row['adj_reason_name'];
        
    }
    
    function getAdjustmentOut($item_code, $start_date, $end_date){
        global $db;
        
        $totalqty = 0;
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "SELECT sum(ad.adj_qty) as total from $this->tb_adjustment as a 
                        LEFT JOIN $this->tb_adjustment_details as ad ON a.refno=ad.refno
                        WHERE (ad.item_code = '$item_code'
                        AND a.adjust_date >= '".$start_date."' AND a.adjust_date <= '".$end_date."'
                        AND (ad.reason = 'D' OR (ad.reason = 'PC' AND ad.adj_qty < 0))
                        AND ad.unit_id='".$rowextended['pc_unit_id']."')";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        
        $this->sql = "SELECT sum(ad.adj_qty) as total from $this->tb_adjustment as a 
                        LEFT JOIN $this->tb_adjustment_details as ad ON a.refno=ad.refno
                        WHERE (ad.item_code = '$item_code'
                        AND a.adjust_date >= '".$start_date."' AND a.adjust_date <= '".$end_date."'
                        AND (ad.reason = 'D' OR (ad.reason = 'PC' AND ad.adj_qty < 0))
                        AND ad.unit_id='".$rowextended['pack_unit_id']."')";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty += ($rowResult['total'] * $rowextended['qty_per_pack']);    
        }
        return $totalqty;
    }
    
    function getAdjustmentIn($item_code, $start_date, $end_date){
        global $db;
        
        $totalqty = 0;
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "SELECT sum(ad.adj_qty) as total from $this->tb_adjustment as a 
                        LEFT JOIN $this->tb_adjustment_details as ad ON a.refno=ad.refno
                        WHERE (ad.item_code = '$item_code'
                        AND a.adjust_date >= '".$start_date."' AND a.adjust_date <= '".$end_date."'
                        AND (ad.reason IN ('R','DO','F') OR (ad.reason = 'PC' AND ad.adj_qty > 0))                        AND ad.unit_id='".$rowextended['pc_unit_id']."')";
        $this->result = $db->Execute($this->sql);
    
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        
        $this->sql = "SELECT sum(ad.adj_qty) as total from $this->tb_adjustment as a 
                        LEFT JOIN $this->tb_adjustment_details as ad ON a.refno=ad.refno
                        WHERE (ad.item_code = '$item_code'
                        AND a.adjust_date >= '".$start_date."' AND a.adjust_date <= '".$end_date."'
                        AND (ad.reason IN ('R','DO','F') OR (ad.reason = 'PC' AND ad.adj_qty > 0))                        AND ad.unit_id='".$rowekxtended['pac_unit_id']."')";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty += ($rowResult['total'] * $rowextended['qty_per_pack']);    
        }
        return $totalqty;
    }
    
    function getBillingOut($item_code, $start_date, $end_date){
        global $db;
        
        $totalqty = 0;
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "SELECT sum(ad.quantity) as total from seg_more_phorder as a 
                        LEFT JOIN seg_more_phorder_details as ad ON a.refno=ad.refno
                        WHERE (ad.bestellnum = '$item_code'
                        AND a.chrge_dte >= '".$start_date."' AND a.chrge_dte <= '".$end_date."')";

        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        
        return $totalqty;
    }
    
    function getServiceUsageOut($item_code, $start_date, $end_date){
        global $db;
        
        $totalqty = 0;
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "SELECT sum(ad.qty_used) as total from seg_service_usage as a 
                        LEFT JOIN seg_service_usage_details as ad ON a.refno=ad.refno
                        WHERE (ad.item_code = '$item_code'
                        AND a.served_date >= '".$start_date."' AND a.served_date <= '".$end_date."'
                        AND ad.unit_id='".$rowextended['pc_unit_id']."')";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        
        $this->sql = "SELECT sum(ad.qty_used) as total from seg_service_usage as a 
                        LEFT JOIN seg_service_usage_details as ad ON a.refno=ad.refno
                        WHERE (ad.item_code = '$item_code'
                        AND a.served_date >= '".$start_date."' AND a.served_date <= '".$end_date."'
                        AND ad.unit_id='".$rowextended['pack_unit_id']."')";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty += ($rowResult['total'] * $rowextended['qty_per_pack']);    
        }
        return $totalqty;
    }
    
    function getPharmaOrderOut($item_code, $start_date, $end_date){
        global $db;
        
        $totalqty = 0;
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "SELECT sum(ad.quantity) as total from seg_pharma_orders as a 
                        LEFT JOIN seg_pharma_order_items as ad ON a.refno=ad.refno
                        WHERE (ad.bestellnum = '$item_code'
                        AND a.orderdate >= '".$start_date."' AND a.orderdate <= '".$end_date."'
                        AND ad.serve_status='S')";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        
        return $totalqty;
    }
    
    function getEquipmentOrderOut($item_code, $start_date, $end_date){
        global $db;
        
        $totalqty = 0;
        $obj = new SegPharmaProduct();
        
        $rowextended = $obj->getExtendedProductInfo($item_code);
        
        $this->sql = "SELECT sum(ad.number_of_usage) as total from seg_equipment_orders as a 
                        LEFT JOIN seg_equipment_order_items as ad ON a.refno=ad.refno
                        WHERE (ad.equipment_id = '$item_code'
                        AND a.order_date >= '".$start_date."' AND a.order_date <= '".$end_date."')";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $rowResult = $this->result->FetchRow();
            $totalqty = $rowResult['total'];    
        }
        
        return $totalqty;
    }
	
	function getLastNr($today) {
        global $db;
        $today = $db->qstr($today);
        $this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),'000001')) FROM $this->tb_adjustment WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
        return $db->GetOne($this->sql);
    }  
    
    #added by VAN 08-20-09  
    function getUnitSelection($is_pc){
        global $db;
        
        $this->sql="SELECT * FROM seg_unit WHERE is_unit_per_pc=$is_pc";    
                        
        #echo "sql = ".$this->sql;
        if ($this->result=$db->Execute($this->sql)) {
        if ($this->count=$this->result->RecordCount()){
            return $this->result;
         }else{
                return FALSE;
         }
         }else{
            return FALSE;
        }
    }
    
    
    function saveItemPerUsage($data){
        global $db;
        
        extract($data);
        
        $userid = $HTTP_SESSION_VARS['sess_temp_userid'];
        
        $this->sql = "INSERT INTO seg_service_usage(refno, served_date, ref_source, create_id, create_tm, modify_id, modify_tm)
                      VALUES('$refno', '$served_date', '$ref_source', '$userid',NOW(),'$userid',NOW())";
       
        $this->result = $db->Execute($this->sql);
        if ($db->Affected_Rows()) {
          return true;
        }
        else {
          return false;
        }
    }
    
    function saveItemPerUsageDetails($refno, $ref_source, $info_array){
        global $db;
        
        $this->sql="INSERT INTO seg_service_usage_details(refno, ref_source,service_code,item_code,qty_used, unit_id) ". 
                   "VALUES('$refno', '$ref_source', ?, ?, ?, ?)";
        
        $ok=$db->Execute($this->sql,$info_array);
        $this->count=$db->Affected_Rows();
        return $ok;
    }
    
    function clearItemPerUsageDetails($refno, $ref_source) {
        global $db;
        
        $this->sql = "DELETE FROM seg_service_usage_details 
                        WHERE refno=$refno
                        AND ref_source = '$ref_source'";
        return $this->Transact();
    }
    #------------------------
    
    /**
    * @internal     Extract the history of adjustments given a particular filter.
    * @access       public
    * @author       Bryan Inno Wong
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        filters - array of filters.
    * @param        offset - from which record.
    * @param        rowcount - how many records (rows) to extract.
    * @return       resultset of extracted records.
    */     
    function getPostedAdjustments($filters, $offset=0, $rowcount=15, $areas = '') {
        global $db;

        if (!$offset) $offset = 0;
        if (!$rowcount) $rowcount = 15;
        
        if (is_array($filters)) {
            foreach ($filters as $i=>$v) {
                switch (strtolower($i)) {
                    case 'datetoday':
                        $phFilters[] = 'DATE(adjust_date)=DATE(NOW())';
                        break;
                    case 'datethisweek':
                        $phFilters[] = 'YEAR(adjust_date)=YEAR(NOW()) AND WEEK(adjust_date)=WEEK(NOW())';
                        break;
                    case 'datethismonth':
                        $phFilters[] = 'YEAR(adjust_date)=YEAR(NOW()) AND MONTH(adjust_date)=MONTH(NOW())';
                        break;
                    case 'date':
                        $phFilters[] = "DATE(adjust_date)='$v'";
                        break;
                    case 'datebetween':
                        $phFilters[] = "DATE(adjust_date)>='".$v[0]."' AND DATE(adjust_date)<='".$v[1]."'";
                        break;
                    case 'name':
                        $phFilters[] = "concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) REGEXP '[[:<:]]".substr($db->qstr($v),1);
                        break;
                    case 'item_desc':
                        $phFilters[] = "(select count(sird3.refno) as ncount  
                            from $this->tb_adjustment_details as sird3 inner join care_pharma_products_main as cppm on sird3.item_code = cppm.bestellnum
                            where cppm.artikelname REGEXP ".$db->qstr($v)." and sird3.refno = sirh.refno) > 0";
                        break;
                    case 'ref_no':
                        $phFilters[] = "sirh.refno REGEXP ".$db->qstr($v);
                        break;
                }
            }
        }
        
        if ($areas != '') {
            $phFilters[] = "area_code in ($areas)";    
        }
        
        $phWhere=implode(") AND (",$phFilters);
        if ($phWhere) $phWhere = "($phWhere)";
        else $phWhere = "1";   
        
        $this->sql = "select SQL_CALC_FOUND_ROWS sirh.refno, adjust_date, 
                            (select area_name from seg_areas as sa1 where sa1.area_code = sirh.area_code) as adjusting_area, 
                            concat(cp.name_last, if(isnull(cp.name_first) or cp.name_first = '', if(isnull(cp.name_middle) or cp.name_middle = '', '', ', '), concat(', ', cp.name_first)), if(isnull(cp.name_middle) or cp.name_middle = '', '', concat(' ', cp.name_middle))) as adjustor, 
                            (select GROUP_CONCAT(DISTINCT artikelname ORDER BY artikelname DESC SEPARATOR ', ') as items 
                               from $this->tb_adjustment_details as sird inner join care_pharma_products_main as cppm on sird.item_code = cppm.bestellnum
                               group by sird.refno having sird.refno = sirh.refno) as particulars                         
                         from ($this->tb_adjustment as sirh inner join care_personell as cpl on 
                            sirh.adjusting_id = cpl.nr) inner join care_person as cp on cpl.pid = cp.pid 
                         where ($phWhere)                            
                         order by adjust_date asc 
                         limit $offset, $rowcount";                         
                                 
        if($this->result=$db->Execute($this->sql)) {
            return $this->result;     
        } else { return false; }                
    }

    public function getAdjustments($params)
    {
        global $db;

        $this->sql = "SELECT b.adjust_date,
						(select d.area_name from seg_areas as d where d.area_code = b.area_code) as area_name,c.bestellnum,c.artikelname,a.adj_qty,e.unit_name,f.adj_reason_name
						FROM (seg_inventory_adjustment_details as a
						JOIN seg_inventory_adjustment as b ON a.refno=b.refno)
						JOIN care_pharma_products_main as c ON a.item_code=c.bestellnum
						JOIN seg_unit as e ON a.unit_id=e.unit_id
						LEFT JOIN seg_inventory_adjustment_reason as f ON a.reason=f.adj_reason_id  ";

        $where = array();

        if (array_key_exists('as_of_date', $params)) {
            $where[] = "DATE(b.adjust_date) = " . $db->qstr(date('Y-m-d', strtotime($params['as_of_date'])));
        } else {
            return array();
        }

        /*
        if (array_key_exists('to_date', $params)) {
            $where[] = "DATE(b.adjust_date) <= " . $db->qstr(date('Y-m-d', strtotime($params['to_date'])));
        }
        */
        //area or department
        $where[] = "b.area_code=" . $db->qstr($params['area']);

        if (array_key_exists('adjustr', $params)) {
            $where[] = "a.reason=" . $db->qstr($params['adjustr']);
        }

        if ($where)
            $this->sql .= "WHERE (" . implode(") AND (", $where) . ")";

        $this->sql .= " ORDER BY b.adjust_date";
        $result = $db->Execute($this->sql);

        if ($result) {
            $data = array();
            while ($row = $result->FetchRow()) {

                    $data[] = array(
                        'adjust_date' => date("m/d/y h:ia", strtotime($row['adjust_date'])),
                        'area_name' => $row['area_name'],
                        'item_code' => $row['bestellnum'],
                        'artikelname' => $row['artikelname'],
                        'adj_qty' => $row['adj_qty'],
                        'unit_name' => $row['unit_name'],
                        'reason' => isset($row['adj_reason_name']) ? $row['adj_reason_name'] : 'No Reason',
                    );



            }
            return $data;
        }
        return false;
    }
}
?>