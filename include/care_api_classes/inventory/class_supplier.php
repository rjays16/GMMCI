<?php
/*
 * @package care_api
 * Class of Suppliers.
 *
 * Created: June 16, 2009 (Bryan)
 * 
 */
require_once($root_path.'include/care_api_classes/class_core.php');

class SegSupplier extends Core {
    
    var $tb_supplier = "seg_supplier"; 
    
    var $supplier_cols = array(
        "supplier_id",
        "name",
        "addr",
        "phone",
        "email",
        "history",
        "modify_id",
        "modify_time",
        "create_id",
        "create_time"
    );     
    
    function setSupplier() {
        $this->coretable = $this->tb_supplier;
        $this->setTable($this->coretable);
        $this->setRefArray($this->supplier_cols);
    }   
    
    function getSupplierOptions() {
        global $db;
        
        $soption = '';
        $this->sql = "SELECT supplier_id,name FROM $this->tb_supplier ORDER BY name";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            $soption = '<option value = \'\'>-- Select --</option>'; 
            while($row = $this->result->FetchRow())
            {
                $soption .= '<option value = \''.$row['supplier_id'].'\'>'.$row['name'].'</option>';    
            }
        }
        return $soption;
        
    }
    
    function getSupplierName($supplier_id) {
        global $db;
        
        $soption = '';
        $this->sql = "SELECT name FROM $this->tb_supplier WHERE supplier_id=$supplier_id";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            while($row = $this->result->FetchRow())
            {
                $soption = $row['name'];    
            }
        }
        return $soption;
        
    }
    
    function getAllSuppliers(){
         global $db;
        
        $soption = '';
        $this->sql = "SELECT * FROM $this->tb_supplier ORDER BY name ASC";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            return $this->result;
        }
        else return false;
    }
    

}
  
?>
