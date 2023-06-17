<?php
/*
 * @package care_api
 * Class of Serials (inventory by serial no.)
 *
 * Created: January 15, 2009 (Bryan)
 * Modified: January 15, 2009 (LST)
 */
require_once($root_path.'include/care_api_classes/class_core.php');

class Serial extends Core{
     #created by Bryan
    /**
    * Table name
    * @var string
    */    
    var $tb_serials = 'seg_serial_inventory'; # serials inventory table
    /*
    * @var String
    */
    var $serial_no;
    /*
    * @var String
    */
    var $item_code;
    /*
    * @var String
    */
    var $area_code;
    /*
    * @var String
    */    
    var $property_no;
    /*
    * @var decimal(10,2)
    */    
    var $acquisition_cost;
    /*
    * @var datetime
    */    
    var $acquisition_date;
    /*
    * @var Integer
    */    
    var $supplier_id;
    
    /**
    * @internal     Adds item with serial in inventory of serials.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        array (serial_no, item_code, area_code, property_no, acquisition_cost, acquisition_date, supplier_id)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */         
    function addSerialNo($data) {
        global $db;
        
        if (empty($data)) return FALSE;
        
        $bSuccess = FALSE;
        if ($data['serial_no'] != NULL) {                            
            $this->sql = "SELECT * FROM $this->tb_serials WHERE (serial_no = '".$data['serial_no']."') FOR UPDATE"; 
            $result = $db->Execute($this->sql);
            if (!$result->EOF) { 
                $this->sql = "update $this->tb_serials set 
                                 property_no      = '".$data['property_no']."',
                                 acquisition_cost =  ".$data['acquisition_cost'].",
                                 acquisition_date = '".$data['acquisition_date']."',
                                 supplier_id      =  ".$data['supplier_id']."
                              WHERE (serial_no = '".$data['serial_no']."')";
                $bSuccess = $db->Execute($this->sql);
            }
            else {
                $this->sql = "INSERT INTO $this->tb_serials (serial_no, area_code, property_no, acquisition_cost, acquisition_date, supplier_id) 
                                 VALUES ('".$data['serial_no']."', '".$data['area_code']."','".$data['property_no']."',
                                          ".$data['acquisition_cost'].", '".$data['acquisition_date']."', ".$data['supplier_id'].")"; 
                $bSuccess = $db->Execute($this->sql);
            }                 
        }  
        
        return $bSuccess;      
    }
    
    /**
    * @internal     removes an item with serial in inventory of serials.
    * @access       public
    * @author       Bryan Inno N. Wong
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        array (serial_no, item_code, area_code)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */ 
    function removeSerialNo($data) {
        global $db;
        
        if (empty($data)) return FALSE;
        
        $bSuccess = FALSE;
//        if ($data['serial_no'] != NULL) {                            
//            $sqlSTR = "SELECT * FROM $this->tb_serials WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."') FOR UPDATE"; 
//            $result = $db->Execute($sqlSTR);
//            if (!$result->EOF) { 
//                $sqlSTR = "DELETE FROM seg_serial_inventory WHERE serial_no='".$data['serial_no']."'"; 
//                $bSuccess = $db->Execute($sqlSTR);
//            }               
//        }  

        if ($data['serial_no'] != NULL) {                             
            $this->sql = "DELETE FROM seg_serial_inventory WHERE serial_no='".$data['serial_no']."'"; 
            $bSuccess = $db->Execute($this->sql);             
        } 
        
        return $bSuccess;      
    }  
    
    function getSerialsofItemExpiry($item_code, $area_code, $expiry_date) {
        global $db;
        
        $soption = '';
        $this->sql = "SELECT DISTINCT serial_no FROM seg_eod_inventory WHERE item_code='$item_code' AND area_code='$area_code' AND expiry_date='$expiry_date'";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            return $this->result;
        }
        else return false;
            
    }  
}
?>