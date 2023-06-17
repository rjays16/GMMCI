<?php
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');

		/*
				Populate the seg_rep_medrec_patient_icd_tbl for final icd of the patient
				for notifiable, sentinel, cancer monitoring and other reports that needs patient info
				use : CRON SCHEDULER (daily)
	 */

	 global $db;

	 echo "Locking bot...";

	 #$datenow = date('Y-m-d');
	 $previousdate = date( "Y-m-d", strtotime( "$datenow -1 day" ));
	 #echo "s = ".$previousdate;
	 $sql = "CALL sp_populate_medrec_patient_icd('".$previousdate."','".$previousdate."')";
	 #echo "ss = ".$sql;
	 $ok = $db->Execute($sql);

	 echo "<br>Lock released...";
?>
