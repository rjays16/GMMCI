<?php

#added by angelo m. 09.17.2010
function update_room_status($refno,$done_date) {

			 global $db;
			$done_date=date('Y/m/d',strtotime($done_date));
			$objResponse = new xajaxResponse();
			$query = "UPDATE seg_or_main
													SET room_nr=NULL,
															done_date='$done_date'
													WHERE or_main_refno='$refno';";
			$result = $db->Execute($query);
			if ($result) {
				$objResponse->alert('Successfully updated');
				$objResponse->call('updateList');
			}
			else {
				$objResponse->alert('Failed to update');
			}

			return $objResponse;
}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'modules/or/ajax/or-room-common.php');
	$xajax->processRequest();
?>