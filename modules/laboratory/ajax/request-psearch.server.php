<?php
	function populatePersonList($sElem,$searchkey,$include_firstname) {
		global $db;
		$objResponse = new xajaxResponse();
		$person=& new Person();
		$ergebnis=$person->SearchSelect($searchkey,15,"","name_last","ASC",$include_firstname);
		#$objResponse->addAlert($person->sql);
		$rows=0;
		$objResponse->addScriptCall("clearList","person-list");
		
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				
				$addr = $result["street_name"].", ".$result["brgy_name"].", ".$result["mun_name"].", ".$result["prov_name"]." ".$result["zipcode"];				
				
				$objResponse->addScriptCall("addPerson","person-list",
					$result["pid"],$result["name_last"],$result["name_first"],$result["date_birth"],
					$result["sex"],$addr,$result["zipcode"],$result["status"],$result["enctype"]);
			}
		}
		if (!$rows) $objResponse->addScriptCall("addPerson","person-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path.'include/care_api_classes/class_person.php');
	require($root_path."modules/laboratory/ajax/request-psearch.common.php");
	$xajax->processRequests();
?>