<?php
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');
		require_once($root_path.'include/care_api_classes/class_social_service.php');

		/*
				Update the encounter type of care_encounter table.. Automatic discharge opd when it reached to 12am
				use : CRON SCHEDULER (daily)
	 */


	 global $db;

	 #edited by NIKKO 08-20-2014

	 echo "Locking bot...";

	 $db->startTrans();

	 $ok = $db->Execute("CALL sp_update_opd_discharge()");

	 if (!$ok) $db->FailTrans();
				$db->CompleteTrans();
	 echo "<br>Lock released...";
?>
