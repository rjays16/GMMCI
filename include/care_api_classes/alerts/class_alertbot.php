<?php

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegAlertBot extends Core {
  var $tbUserAlerts = "seg_user_alerts";
  
  function SegAlertBot() {
    $this->coretable = "seg_alerts";
  }
  
  function getActiveUserBots($userid) {
    global $db;
    
  }
  
  function getFilters($botid) {
    global $db;
    $this->sql = "SELECT * FROM seg_alert_bot_filters WHERE bot_id=".$db->qstr($botid);
    if ($result = $db->Execute()) {
      $ret = array();
      while ($row = $result->FetchRow()) {
        $ret[] = $row;
      }
      return $ret;
    }
    else {
      return FALSE;
    }
  }
  
  # Log in the current time in which user has accessed the alert bot
  function logRun($userid) {
    global $db;
    $this->sql = "UPDATE care_users SET last_alert=NOW() WHERE login_id=".$db->qstr($userid);
    if ($result = $db->Execute($this->sql)) {
      return TRUE;
    }
    else return FALSE;
  }
  
  #   Fetch all offline alerts from previous bot run, this includes unacknowledged and
  # deferred messages
  function getOfflineAlerts($userid) {
    global $db;
    $this->sql = "SELECT FROM seg_user_alerts AS ua 
LEFT JOIN seg_alerts AS a ON a.alert_id=ua.alert_id
WHERE status=0 OR status=2";
    if ($result=$db->Execute($this->sql)) {
      $rows=array();
      while ($row=$result->FetchRow()) {
        $rows[] = $row;
      }
      return $rows;
    }
    else return FALSE;
  }
  
  function getUserAlertBox($userid, $offset=0, $rowcount=10) {
    global $db;
    $this->sql = "SELECT ua.alert_date,ua.status,a.alert_id,a.data,a.header_text,a.message,a.priority,a.actions,a.alert_time,
a.cat_id,ac.category_name AS `category`,ac.breadcrumbs AS `category_path`,ac.icon,
a.area_code,aa.area_name AS `area`,aa.breadcrumbs AS `area_path`
FROM seg_user_alerts AS ua
INNER JOIN seg_alerts AS a ON ua.alert_id=a.alert_id
LEFT JOIN seg_alert_categories AS ac ON ac.cat_id=a.cat_id
LEFT JOIN seg_areas AS aa ON aa.area_code=a.area_code
WHERE ua.login_id=".$db->qstr($userid)." AND ua.status<>3
ORDER BY a.alert_time DESC
LIMIT $offset, $rowcount";
    if ($this->result=$db->Execute($this->sql)) {
      $rows=array();
      while ($row=$this->result->FetchRow()) {
        $rows[] = $row;
      }
      return $rows;
    }
    else return FALSE;
  }
  
  #   Get the latest alerts for the specified user, do not include alerts already in
  # that user's alert inbox
    function getNewAlertsForUser($userid, $botid=NULL) {
      global $db;
      
  #    $filters = $this->getFilters($botid);
  #    if ($filters) {
  /*
        $this->sql = "SELECT a.alert_id,a.data,a.header_text,a.message,a.priority,a.actions,a.alert_time,
  a.cat_id,ac.category_name AS `category`,ac.breadcrumbs AS `category_path`,
  a.area_code,aa.area_name AS `area`,aa.breadcrumbs AS `area_path`
  FROM seg_alerts AS a
  LEFT JOIN seg_alert_categories AS ac ON ac.cat_id=a.cat_id
  LEFT JOIN seg_areas AS aa ON aa.area_code=a.area_code
  WHERE a.alert_time>(SELECT IFNULL(last_alert,'0000-00-00 00:00:00') FROM care_users AS u WHERE u.login_id=".$db->qstr($userid).")
  HAVING a.alert_id NOT IN 
  (SELECT alert_id FROM seg_user_alerts AS ua WHERE ua.login_id=".$db->qstr($userid)." 
    AND (ua.status=1 OR ua.status=3 OR (ua.status=2 AND (NOW()<ua.alert_date)))
  )";
  */
        $this->sql = "SELECT a.alert_id,a.alert_time FROM seg_alerts AS a
WHERE a.alert_time>(SELECT IFNULL(last_alert,'0000-00-00 00:00:00') FROM care_users AS u WHERE u.login_id=".$db->qstr($userid).")
HAVING a.alert_id NOT IN 
(SELECT alert_id FROM seg_user_alerts AS ua WHERE ua.login_id=".$db->qstr($userid)." 
  AND (ua.status=1 OR ua.status=3 OR (ua.status=2 AND (NOW()<ua.alert_date)))
)";
  
      if ($result=$db->Execute($this->sql)) {
        $rows=array();
        while ($row=$result->FetchRow()) {
          $rows[] = $row;
        }
        return $rows;
      }
      else return FALSE;
  #    }
  #    else return FALSE;
    }
  
  function sendAlertToUserInbox($loginId, $alertId) {
    global $db;
    /*
    $ret = $db->Replace( $this->tbUserAlerts,
      array('login_id'=>$db->qstr($loginId), 'alert_id'=>$db->qstr($alertId)),
      array('login_id','alert_id'),
      $autoquote = FALSE
    );
    */
    $this->sql = "INSERT INTO $this->tbUserAlerts(login_id, alert_id) VALUES(".$db->qstr($loginId).",".$db->qstr($alertId).")";
    if ($result = $db->Execute($this->sql)) {
      return TRUE;
    }
    else return FALSE;
  }
  
  function flag($userid, $alertid, $flag) {
    global $db;
    
    $this->sql = "UPDATE seg_user_alerts SET status=".$db->qstr($flag);
    if ($flag==2) // Defer 5 minutes
      $this->sql .=",alert_date=NOW()+INTERVAL 5 MINUTE";
    else
      $this->sql .=",alert_date=NOW()";
    $this->sql .= " WHERE login_id=".$db->qstr($userid)." AND alert_id=".$db->qstr($alertid);
    if ($result = $db->Execute($this->sql)) {
      return TRUE;
    }
    else return FALSE;
  }
}


