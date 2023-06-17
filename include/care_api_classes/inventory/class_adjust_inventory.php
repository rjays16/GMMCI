<?php  
#created by VAN 02-24-09 
require_once($root_path.'include/care_api_classes/class_core.php');

class Adjustment extends Core {
    
    var $tb_adjustment = "seg_inventory_adjustment";
    var $tb_adjustment_detail = "seg_inventory_adjustment_details";
    
    var $fld_adjustment = array(
        "refno",
        "adjust_date",
        "adjusting_id",
        "area_code",
        "remarks",
		"is_deleted",
		"history",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt"
    );        
    
    var $fld_adjustment_detail = array(
        "refno",
        "item_code",
        "unit_id",
        "is_unitperpc",
        "expiry_date",
		"serial_no",
		"orig_qty",
		"adj_qty"
    );    
        
    function setAdjustmentHdr() {
        $this->coretable = $this->tb_adjustment;
        $this->setTable($this->coretable);
        $this->setRefArray($this->fld_adjustment);
    }
    
    function setAdjustmentDetail() {
        $this->coretable = $this->tb_adjustment_detail;
        $this->setTable($this->coretable);
        $this->setRefArray($this->fld_adjustment_detail);
    }    
    
	function saveAdjustmentDetail() {
        global $db;
    	
		$this->sql_get = "SELECT * FROM $this->coretable 
                             WHERE refno = '".$this->data_array['refno']."' 
                         	 AND item_code = '".$this->data_array['item_code']."' 
							 AND unit_id = '".$this->data_array['unit_id']."' 
                         	 AND expiry_date = ".$this->data_array['expiry_date']." for update";
                         
        $result = $db->Execute($this->sql_get);
        if (!$result->EOF) { 
            $this->sql = "UPDATE $this->coretable SET 
                             adj_qty = adj_qty + ".$this->data_array['adj_qty']."
                          WHERE refno = '".$this->data_array['refno']."' 
                             AND item_code = '".$this->data_array['item_code']."' 
							 AND unit_id = '".$this->data_array['unit_id']."' 
                             AND expiry_date = ".$this->data_array['expiry_date']; 
            
			$bSuccess = $db->Execute($this->sql);   
        }                         
        else
            $bSuccess = $this->insertDataFromInternalArray();

        return $bSuccess;
	}
    
    function getLastNr($today, $ref_init) {
		global $db;
		$this->setAdjustmentHdr();
		$today = $db->qstr($today);
		$this->sql="SELECT IFNULL(MAX(CAST(refno AS UNSIGNED)+1),CONCAT(EXTRACT(YEAR FROM NOW()),$ref_init)) FROM $this->coretable WHERE SUBSTRING(refno,1,4)=EXTRACT(YEAR FROM NOW())";
		return $db->GetOne($this->sql);
	}	
	
	function startTrans() {
        global $db;
        $db->StartTrans();
    }
    
    function failTrans() {
        global $db;
        $db->FailTrans();
    }
    
    function completeTrans() {
        global $db;
        $db->CompleteTrans();        
    }
	
	function delAdjustmentDetails($srefno) {
        global $db;
        
        $this->sql = "DELETE FROM ".$this->tb_adjustment_detail." ".
                     "WHERE refno = '".$srefno."'";
        #$bSuccess = $db->Execute($this->sql);
        return $this->Transact();
    }
    
}
?>
