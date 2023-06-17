<?php
require_once('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');

class SegEquipment extends Core {
  
  var $equipment_details = array();
  
  function SegEquipment($equipment_details = array()) {
    if (!is_array($equipment_details))
      $this->set_equipment_details_by_id($equipment_details);
    else
      $this->set_equipment_details($equipment_details);
      
  }
  
  function set_equipment_details($equipment_details) {
    $this->equipment_details = $equipment_details;
  }
  
  function get_equipment_details() {
    return $this->equipment_details;
  }
  
  function set_equipment_details_by_id($equipment_id) {
    global $db;

    $query = "SELECT bestellnum as equipment_id, artikelname as equipment_name, description as equipment_description, 
              unit as equipment_unit, price_cash as equipment_cash, price_charge as equipment_charge,
              is_socialized FROM care_pharma_products_main WHERE type_nr=4 AND bestellnum='$equipment_id'";
    if ($result = $db->Execute($query)) {
      if($result->RecordCount()) {
        $this->equipment_details = $result->FetchRow();
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }
  
  function get_oxygen_details($serial_no) {
    global $db;
    $query = "SELECT serial_no, qty FROM seg_inventory WHERE serial_no=$serial_no";
    if ($result = $db->Execute($query)) {
      if ($result->RecordCount() > 0) {
        return $result->FetchRow();
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }
  }
  
  
  //Todo: add equipment, remove equipment
  
}  
?>
