<?php
		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');
		require_once($root_path.'include/care_api_classes/class_cron.php');

		/*
				Update the discharging status.. Automatic discharge the patient after the end of the day
				use : CRON SCHEDULER (daily)
	 */

		#Open destination connection.
	 $rsObj = new SegCron();

	 global $db;

	 if (!$rsObj)
				ShowErrorMsg('No connection to destination database opened!');
	 else {
				$bSuccess = true;      // Initialize status tracker.

				$rsObj->ConnecttoSegHisDB();

						# Populate the seg_consolidated income table.
						if ($rsObj->isConnected()) {
								echo "Locking bot...";
								#$rsObj->getBaseDateTime($rsObj->getDestConnection()); // Get the base date and time

								$rsObj->beginTransaction();
								$datenow = date('Y-m-d');
								$tomorrow =  date( "Y-m-d h:i:s ",mktime(0, 0, 0, date("m"), date("d")+1, date("y")));
								#$previousdate = date( "Y-m-d", strtotime( "$datenow -1 day" ));
								$strSQL = "CALL sp_deactivate_personnell('".$tomorrow."'); ";
								$db->Execute($strSQL);



								if (!($destObj->bIsOk)) $rsObj->failTransaction();
											$rsObj->endTransaction();
								}

								// Release lock on fetch tracker ...
								echo "<br>Lock released...";
								$rsObj->releaseFetchLock($rsObj->getDestConnection());
						}

?>
