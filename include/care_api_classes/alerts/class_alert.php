<?php

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegAlert extends Core {
  var $tbUserAlerts = "seg_user_alerts";
  var $id;
  
  function SegAlert($alertId = NULL) {
    $this->id = $alertId;
    if ($this->id) $this->get();
    $this->coretable = "seg_alerts";
  }
  
  
  function postAlert($area_posted, $category_code, $related_data, $header_text, $message, $priority, $actions) {
    global $db;
    
    $this->sql = "INSERT INTO $this->coretable(area_code,cat_id,data,header_text,message,priority,actions,alert_time) 
VALUES(".$db->qstr($area_posted).",".$db->qstr($category_code).",".$db->qstr($related_data).",".
$db->qstr($header_text).",".$db->qstr($message).",".$db->qstr($priority).",".$db->qstr($actions).",NOW())";

    if ($this->result=$db->Execute($this->sql)) {
      return $this->result;
    }
    else return false;
  }
  
  function get($alertId=NULL) {
    global $db;
    if (is_null($alertId)) {      
      if (is_null($this->id))
        return false;
      else $alertId = $this->id;
    }
    $this->sql = "SELECT a.alert_id,a.data,a.header_text,a.message,a.priority,a.actions,a.alert_time,
a.cat_id,ac.category_name AS `category`,ac.breadcrumbs AS `category_path`,
a.area_code,aa.area_name AS `area`,aa.breadcrumbs AS `area_path`
FROM seg_alerts AS a
LEFT JOIN seg_alert_categories AS ac ON ac.cat_id=a.cat_id
LEFT JOIN seg_areas AS aa ON aa.area_code=a.area_code
WHERE a.alert_id=".$db->qstr($alertId);

    if ($this->result=$db->GetRow($this->sql)) {
      return $this->result;
    }
    else return FALSE;
  }
  
  function getUserAlert($userId, $alertId=NULL) {
    global $db;
    if (is_null($alertId)) {      
      if (is_null($this->id))
        return false;
      else $alertId = $this->id;
    }
    $this->sql = "SELECT alert_date, status FROM seg_user_alerts WHERE alert_id=".$db->qstr($alertId)." AND login_id=".$db->qstr($userId);
    if ($this->result=$db->GetRow($this->sql)) {
      return $this->result;
    }
    else return FALSE;    
  }
}


