<?php
	
	function deleteOccupationItem($occupation_nr, $occupation_name){
		global $db;
		$person_obj=new Person;
		$objResponse = new xajaxResponse();
		
		$sql = "SELECT * FROM care_person WHERE occupation='".$occupation_nr."'";
		#$objResponse->addAlert('sql = '.$sql);
		$res=$db->Execute($sql);
		$row=$res->RecordCount();
		
		if ($row==0){		  
			$status=$person_obj->deleteOccupationItem($occupation_nr);
			
			if ($status) {
				$objResponse->addScriptCall("removeOccupation",$occupation_nr);
				$objResponse->addAlert("The occupation ".strtoupper($occupation_name)." is successfully deleted.");
			}else{
				$objResponse->addScriptCall("showme", $person_obj->sql);
			}	
		 }else{
		 		$objResponse->addAlert("The occupation ".strtoupper($occupation_name)." cannot be deleted. It is already been used.");
		 }
		return $objResponse;
	}
	
	function deleteReligionItem($religion_nr, $religion_name){
		global $db;
		$person_obj=new Person;
		$objResponse = new xajaxResponse();
		
		$sql = "SELECT * FROM care_person WHERE religion='".$religion_nr."'";
		#$objResponse->addAlert('sql = '.$sql);
		$res=$db->Execute($sql);
		$row=$res->RecordCount();
		
		if ($row==0){		  
			$status=$person_obj->deleteReligionItem($religion_nr);
			
			if ($status) {
				$objResponse->addScriptCall("removeReligion",$religion_nr);
				$objResponse->addAlert("The religion ".strtoupper($religion_name)." is successfully deleted.");
			}else{
				$objResponse->addScriptCall("showme", $person_obj->sql);
			}	
		 }else{
		 		$objResponse->addAlert("The religion ".strtoupper($religion_name)." cannot be deleted. It is already been used.");
		 }
		return $objResponse;
	}
	
	function deleteEthnicItem($ethnic_nr, $ethnic_name){
		global $db;
		$person_obj=new Person;
		$objResponse = new xajaxResponse();
		
		$sql = "SELECT * FROM care_person WHERE ethnic_orig='".$ethnic_nr."'";
		#$objResponse->addAlert('sql = '.$sql);
		$res=$db->Execute($sql);
		$row=$res->RecordCount();
		
		if ($row==0){		  
			$status=$person_obj->deleteEthnicItem($ethnic_nr);
			
			if ($status) {
				$objResponse->addScriptCall("removeEthnic",$ethnic_nr);
				$objResponse->addAlert("The ethnic group ".strtoupper($ethnic_name)." is successfully deleted.");
			}else{
				$objResponse->addScriptCall("showme", $person_obj->sql);
			}	
		 }else{
		 		$objResponse->addAlert("The ethnic group ".strtoupper($ethnic_name)." cannot be deleted. It is already been used.");
		 }
		return $objResponse;
	}
	

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_person.php');
	require($root_path."modules/system_admin/ajax/edv-admin.common.php");
	$xajax->processRequests();
?>