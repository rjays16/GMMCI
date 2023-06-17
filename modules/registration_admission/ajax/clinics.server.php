<?php
	
	function savedServedPatient($encounter_nr, $is_served, $dept){
		global $db, $HTTP_SESSION_VARS;
		
		$objResponse = new xajaxResponse();
		$enc_obj=new Encounter;
		#$objResponse->addAlert("ajax : encounter_nr, is_served = ".$encounter_nr." , ".$is_served);
		#$objResponse->addScriptCall("onsubmitForm");
		#$save = $enc_obj->ServedEncounter($encounter_nr, $dept, $is_served, $date_served);
		if ($is_served)
			$date_served = date("Y-m-d H:i:s");
		else
			$date_served = '';	
		
		$save = $enc_obj->ServedEncounter($encounter_nr, $dept, $is_served, $date_served);
		#$objResponse->addAlert("sql = ".$enc_obj->sql);
		if ($save){
			$objResponse->addScriptCall("refreshWindow");
		}	
			
		return $objResponse;
	
	}
	
	function populatePatientList(){
		global $db, $HTTP_SESSION_VARS;
		
		$objResponse = new xajaxResponse();
		
		$objResponse->addScriptCall("onsubmitForm");
		return $objResponse;
	}
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path."modules/registration_admission/ajax/clinics.common.php");
	#added by VAN 04-17-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/class_person.php');
	require_once($root_path.'include/care_api_classes/class_ward.php');
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	include_once($root_path.'include/care_api_classes/class_paginator.php');
		 
	$xajax->processRequests();
?>