<?php
#created by VANESSA A. SAREN 07-07-09

require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'classes/adodb/adodb.inc.php');
include_once($root_path.'include/inc_seg_mylib.php');

class SegCron extends Core {

	/**
	* SQL query result. Resulting ADODB record object.
	* @var object
	*/
	var $result;

	/**
	* Resulting record count
	* @var int
	*/
	var $count;

		var $seg_dbdest;
		var $bIsOk;
		var $bIsConnected;

		var $baseDateTime = '1900-01-01 00:00:00';
		var $baseTrxDNo = '0';

	/**
	* Constructor
	* @param string refno
					*/
	function SegCron(){
		$this->bIsConnected = false;
		$this->ConnecttoSegHisDB();
	}

	function ConnecttoSegHisDB() {
				GLOBAL $DBType, $DBHost, $DBUser, $DBPassword, $DBName, $DBPersist;

				$this->seg_dbdest = &ADONewConnection($DBType);

				error_reporting(0);  // Hide errors

				if (!isset($DBPersist) or !empty($DBPersist))     // Use persistent connection (default)
						$bConnected = $this->seg_dbdest->PConnect($DBHost, $DBUser, $DBPassword, $DBName);
				else                                                      // Use single connection
						$bConnected = $this->seg_dbdest->Connect($DBHost, $DBUser, $DBPassword, $DBName);

				if (!$bConnected) {
						// In the name of protocol correctness, monitoring and performance
						// profiling, set the appropriate error headers for machine comsumption
						if (isset($_SERVER['SERVER_PROTOCOL'])) {
								// Avoid it with cron.php. Note that we assume it's HTTP/1.x
								header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
						}
						// and then for human consumption...
						ShowErrorMsg('ERROR connecting to MySQL!');
						return(NULL);
				}

				/// Forcing ASSOC mode for ADOdb (some DBs default to FETCH_BOTH)
				$this->seg_dbdest->SetFetchMode(ADODB_FETCH_ASSOC);

				error_reporting(E_ALL ^ E_NOTICE);       // Show errors from now on.

				/// Starting here we have a correct DB conection but me must avoid
				/// to execute any DB transaction until "set names" has been executed
				/// some lines below!
				configure_dbconnection($this->seg_dbdest, $DBType);

				$this->bIsConnected = true;
				return($this->seg_dbdest);
		}

		function isConnected() { return($this->bIsConnected); }

		function getDestConnection() { return($this->seg_dbdest); }

		function beginTransaction() { return($this->seg_dbdest->StartTrans()); }
		function failTransaction() { return($this->seg_dbdest->FailTrans()); }
		function endTransaction() { return($this->seg_dbdest->CompleteTrans()); }

		function releaseFetchLock($db) {
				$qry = "select RELEASE_LOCK('mylock')";
				if (!($db->Execute($qry))) {
						ShowErrorMsg($db->ErrorMsg());
				}
		}

}
?>
