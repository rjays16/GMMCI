<?php

	// Cheat function !!!!
	//  - Loads both the list of services associated with the service group, and the
	//  parameters associated with the first service on the list
	function loadsrvparam($gid) {
		# Create the SegLab class
		$srv=new SegLab;
		
		# Fetch the list of services available
		$ergebnis=$srv->getLabServices("group_id=$gid");
		$objResponse = new xajaxResponse();		
		$recCount = $srv->result->RecordCount();
		$counter=0;

		$list = array();
		$first=FALSE;
		if ($recCount>0) {
			while($result=$ergebnis->FetchRow()) {
				$list[] = $result["name"];
				$list[] = $result["service_code"];
				if ($counter==0) {
					# Save the default service's ID
					$first=$result["service_code"];
				}
				$counter++;
			}				
		}
		
#		$objResponse->addAlert("showme");
		
		$objResponse->addScriptCall("showme", $srv->sql);		
		$objResponse->addScriptCall("clearOptions","selectservice");
		$objResponse->addScriptCall("populateOptions","selectservice",$list);
		
		
		# Load the parameters for the default service
		if ($first) {	
			$ergebnis=$srv->getLabParams("service_code='".addslashes($first)."'");
			$objResponse->addScriptCall("crow");
			$recCount = $srv->result->RecordCount();
			$counter=0;
			if ($recCount>0) {
				while($r=$ergebnis->FetchRow()) {
					$counter++;
					$objResponse->addScriptCall("nrow",$r["param_id"],$r["name"],$r["msr_unit"],$r["median"],
						$r["lo_bound"],$r["hi_bound"],$r["lo_critical"],$r["hi_critical"],$r["lo_toxic"],$r["hi_toxic"],FALSE);
				}
				$objResponse->addScriptCall("refreshTitle");
			}
			else {
				$objResponse->addScriptCall("nrow",NULL);
			}
		}
		else {
			#$objResponse->addScriptCall("nrow",NULL);
			$objResponse->addScriptCall("nrow",NULL);
		}		
		return $objResponse;		
	}
	
	function lsrv($gid) {
		$srv=new SegLab;
		$ergebnis=$srv->getLabServices("group_id=$gid");
		$objResponse = new xajaxResponse();		
		$recCount = $srv->result->RecordCount();
		$counter=0;
		$list = array();
		if ($recCount>0) {
			while($result=$ergebnis->FetchRow()) {
				$list[] = $result["name"];
				$list[] = $result["service_code"];
				$counter++;
			}				
		}
		# $objResponse->addAlert(print_r($srv->sql,TRUE));
		$objResponse->addScriptCall("showme", $srv->sql);		
		$objResponse->addScriptCall("clearOptions","selectservice");
		$objResponse->addScriptCall("populateOptions","selectservice",$list);
		return $objResponse;
	}
	
	function pparam($svcode) {		
		$srv=new SegLab;
		$ergebnis=$srv->getLabParams("service_code='".addslashes($svcode)."'");
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("crow");
		$recCount = $srv->result->RecordCount();
		$counter=0;
		if ($recCount>0) {
			while($r=$ergebnis->FetchRow()) {
				$counter++;
				$objResponse->addScriptCall("nrow",$r["param_id"],$r["name"],$r["msr_unit"],$r["median"],
					$r["lo_bound"],$r["hi_bound"],$r["lo_critical"],$r["hi_critical"],$r["lo_toxic"],$r["hi_toxic"],FALSE);
			}				
		}
		else {
			# $objResponse->addScriptCall("nrow",NULL);
			$objResponse->addScriptCall("nrow",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,TRUE);
		}
		# $objResponse->addAlert(print_r($srv->sql,TRUE));
		return $objResponse;		
	}
	
	function nparam($name, $svcode) {
			// Escape passed argument
		global $db;
		$srv=new SegLab;
		$id=$srv->createLabParam($svcode, $name);
		$objResponse = new xajaxResponse();
		if ($id!==FALSE) {
			$objResponse->addScriptCall("nrow", $id, $name, '', '', '', '', '', '', '', '', TRUE);
			$objResponse->addScriptCall("clrForm");
		}
		else {
			$objResponse->addScriptCall("showme", $srv->sql);
			#$objResponse->addAlert("ERROR:".$db->ErrorMsg());
			$objResponse->addAlert("ERROR:".print_r($id,TRUE));
		}
		return $objResponse;
	}
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_labservices_transaction.php');
	require($root_path."modules/laboratory/ajax/lab-param.common.php");
	$xajax->processRequests();
?>