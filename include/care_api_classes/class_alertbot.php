<?php

// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require("./roots.php");	
require_once($root_path.'include/care_api_classes/class_core.php');

class SegAlertBot extends Core {
	
	function SegAlertBot() {
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
	
	#   Get the latest alerts for the specified user, do not include alerts already in
	# that user's alert inbox
	function getNewAlerts($userid, $botid) {
		global $db;
		
#		$filters = $this->getFilters($botid);
#		if ($filters) {
		$this->sql = "SELECT * FROM seg_alerts AS a
WHERE a.alert_date>(SELECT ISNULL(last_alert,'0000-00-00 00:00:00') FROM care_users AS u WHERE u.login_id=".$db->qstr($userid).")
HAVING NOT EXISTS(SELECT 1 FROM seg_user_alerts AS ua WHERE ua.alert_id=a.alert_id)";
		if ($result=$db->Execute($this->sql)) {
			$rows=array();
			while ($row=$result->FetchRow()) {
				$rows[] = $row;
			}
			return $rows;
		}
		else return FALSE;
#		}
#		else return FALSE;
	}
}


?>