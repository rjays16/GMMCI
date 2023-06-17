
<?php
 	/*
 	* Created By Jarel 
 	* Created On 11/12/2013
 	* Auto Update PHS status to 'expired' if the SON/DAUGHTER's Age > 21 Using CRON SCHEDULER(daily)
 	*/
	
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');

	global $db;

	$name = 'dependents max age';

	$sql = "SELECT value FROM seg_default_value WHERE name='$name' LIMIT 1";

	if ($result = $db->Execute($sql)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$value = $row['value'];
			}
		}
	}

	 echo "Locking bot...";

	 $db->startTrans();
	 
	 $ok = $db->Execute("CALL sp_update_dependent_status('".$value."')");

	 if (!$ok) $db->FailTrans();
				$db->CompleteTrans();
	 echo "<br>Lock released...";
?>
