<?php
/*
 * @package care_api
 * Class of Expiry.
 *
 * Created:  (Bryan)
 * Modified: January 15, 2009 (LST)
 */
require_once($root_path.'include/care_api_classes/class_core.php');       
   
class Expiry extends Core{
     #created by Bryan
    /**
    * Table name 
    * @var string
    */    
    var $tb_expiry = 'seg_expiry_inventory'; # EOD (end-of-day) inventory table
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
    var $expiry_date;     
    /*
    * @var Double
    */
    var $available_qty;     
     
    /**
    * @internal     Adds item in expiry inventory.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        array (item_code, area_code, expiry_date, qty)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */
    function addExpiry($data) {
        global $db;
        
        if (empty($data)) return FALSE;
        
        $bSuccess = FALSE;
        if ($data['expiry_date'] != NULL) {           
            $sqlSTR = "SELECT qty FROM $this->tb_expiry WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."') FOR UPDATE"; 
            $result = $db->Execute($sqlSTR);
            if (!$result->EOF)
            { 
                $sqlSTR = "UPDATE $this->tb_expiry SET 
                              expiry_date = '".$data['expiry_date']."', 
                              qty = qty + ".$data['qty']." 
                              WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."')"; 
                $bSuccess = $db->Execute($sqlSTR);
            }
            else
            {
                $sqlSTR ="INSERT INTO $this->tb_expiry (item_code, area_code, expiry_date, qty) 
                             VALUES ('".$data['item_code']."','".$data['area_code']."','".$data['expiry_date']."',".$data['qty'].")";
                $bSuccess = $db->Execute($sqlSTR);
            }            
        }
        
        return $bSuccess; 
    }
    
    /**
    * @internal     removes item in expiry inventory.
    * @access       public
    * @author       Bryan Inno N. Wong
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        array (item_code, area_code, expiry_date, qty)
    * @return       boolean TRUE if successful, FALSE otherwise.
    */
    function removeExpiry($data) {
        global $db;
        
        if (empty($data)) return FALSE;
        
        $bSuccess = FALSE;
        if ($data['expiry_date'] != NULL) {           
            $sqlSTR = "SELECT qty FROM $this->tb_expiry WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."') FOR UPDATE"; 
            $result = $db->Execute($sqlSTR);
            if (!$result->EOF)
            { 
                $row = $result->FetchRow();
                if($row['qty'] != $data['qty']) 
                {
                    $sqlSTR = "UPDATE $this->tb_expiry SET 
                              expiry_date = '".$data['expiry_date']."', 
                              qty = qty - ".$data['qty']." 
                              WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."')"; 
                    $bSuccess = $db->Execute($sqlSTR);
                }
                else
                {
                    $sqlSTR = "DELETE FROM seg_expiry_inventory WHERE (item_code='".$data['item_code']."' AND area_code='".$data['area_code']."')";
                    $bSuccess = $db->Execute($sqlSTR);
                }
            }           
        }
        
        return $bSuccess; 
    }
    
    
    
    function getExpiriesofItem($item_code, $area_code) {
        global $db;
        
        $soption = '';
        $this->sql = "SELECT DISTINCT expiry_date FROM seg_eod_inventory WHERE item_code='$item_code' AND area_code='$area_code' ORDER BY expiry_date";
        $this->result = $db->Execute($this->sql);
        if($this->result){
            return $this->result;
        }
        else return false;
            
    }
}
?>
