<?php
		error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
		require('./roots.php');
		require($root_path.'include/inc_environment_global.php');
		require_once($root_path.'include/care_api_classes/class_social_service.php');

		/*
				Update the patient social service classification.. Automatic to expire the classification of the
				patient after the expiry date
				use : CRON SCHEDULER (daily)
	 */

		#Open destination connection.
	 $rsObj = new SocialService();

	 global $db;

	 #get expiry length
	 #edited by VAN 03-17-2011
	 $name_discount = 'discount expiry';
	 $rowInfo = $rsObj->getExpiryLengthByName($name_discount, 'ss');

	 $exipry_days = 0;
	 if ($rowInfo['value'])
			$exipry_days = $rowInfo['value'];

	 echo "Locking bot...";

	 $rsObj->startTrans();
	 #$datenow = date('Y-m-d');
	 $previousdate = date( "Y-m-d", strtotime( "$datenow -1 day" ));
	 #echo "s = ".$previousdate;
	 $ok = $db->Execute("CALL sp_update_granted_discount('".$previousdate."','".$exipry_days."')");

	 if (!$ok) $rsObj->FailTrans();
				$rsObj->CompleteTrans();
	 echo "<br>Lock released...";
?>
