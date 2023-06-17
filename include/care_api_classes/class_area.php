<?php

require_once('./roots.php'); 

class SegArea extends Core{

    var $tb_issue = "seg_areas";
    
    function getAreaName($area){
        global $db;
        
        $this->sql = "SELECT area_name FROM seg_areas WHERE area_code='$area'";
        $this->result = $db->Execute($this->sql);
        $row = $this->result->FetchRow();
        
        return $row['area_name'];     
    } 

    function getInventoryAreas() {
        global $db;
        $result = array();
        $this->sql = "SELECT inv.area_code, ar.area_name
                      FROM seg_inventory_areas inv
                      INNER JOIN seg_areas ar ON ar.area_code = inv.area_code";
        $this->result = $db->Execute($this->sql);

        if($this->result) {
          $result = $this->result->getArray();
        }

        return $result;
    }

}
?>
