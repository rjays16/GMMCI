<?php
	function populatePersonList($sElem,$searchkey,$page,$include_firstname,$include_encounter=TRUE) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$person=& new Person();
		$offset = $page * $maxRows;
		if ($include_encounter) {
			#$total = $person->countSearchSelectWithCurrentEncounter($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
			# $objResponse->addScript("document.write('".addslashes($person->sql)."')");
			# return $objResponse;
			#$objResponse->addAlert("total = ".$total);
			$ergebnis=$person->SearchSelectWithCurrentEncounter($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
			$total = $person->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;
		}
		else {
		#$objResponse->addAlert('sulod');
			$ergebnis=$person->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
			#$objResponse->addAlert('sql = '.$person->sql);
			$total = $person->FoundRows();
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;
/*		
			$total = $person->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
			$lastPage = floor($total/$maxRows);
			if ($page > $lastPage) $page=$lastPage;
			$ergebnis=$person->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
*/
			#$objResponse->addScriptCall("display",$person->sql);
			#return $objResponse;
		}
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","person-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
/*----replaced by pet-----may 15, 2008----to avoid hanging commas & in preparation of not requiring brgy_name-------			
				$addr = $result["street_name"];
				if ($result["brgy_name"])
					$addr.=", ".$result["brgy_name"];
				if ($result["mun_name"])
					$addr.=", ".$result["mun_name"];
				if ($result["prov_name"])
					$addr.=", ".$result["prov_name"];
				if ($result["zipcode"])
					$addr.=" ".$result["zipcode"];
#------------with---------------------------------------------------------------------------------------*/
				if ($result["street_name"] && $result["brgy_name"])
					$addr=$result["street_name"].", ".$result["brgy_name"].", ".$result["mun_name"]." ".
							$result["zipcode"]." ".$result["prov_name"];
				else {
					if ($result["street_name"] && !$result["brgy_name"]) {
						if ($result["mun_name"])
							$addr=$result["street_name"].", ".$result["mun_name"]." ".$result["zipcode"]." ".
									$result["prov_name"];
						else {
							if ($result["prov_name"])
								$addr=$result["street_name"].", ".$result["prov_name"];
							else
								$addr=$result["street_name"];
							 }
						}
					elseif (!$result["street_name"] && $result["brgy_name"])
							$addr=$result["brgy_name"].", ".$result["mun_name"]." ".$result["zipcode"]." ".
									$result["prov_name"];
					  }							 
#------------until here only---------------------------------------------------------fgdp----------------
				
#$objResponse->addAlert("populatePersonList :: person->sql = '".$person->sql."'");
#$objResponse->addAlert("populatePersonList :: addr = '".$addr."'");
				$dob = $result["date_birth"];
				if (!$dob || $dob=="0000-00-00") $dob="";

				$data = array(
					'pid'=>$result["pid"],
					'lname'=>$result["name_last"],
					'fname'=>$result["name_first"],
					'dob'=>$dob,
					'sex'=>$result["sex"],
					'addr'=>$addr,
					'zip'=>$result["zipcode"],
					'status'=>$result["status"],
					'encounter_nr'=>$result["encounter_nr"],
					'type'=>$result["encounter_type"],
					'discountid'=>$result["discountid"],
					'discount'=>$result["discount"],
					'rid'=>$result['rid']);
				
				#$objResponse->addScriptCall("addPerson","person-list", (object) $data);
				
				$objResponse->addScriptCall("addPerson","person-list",
					$result["pid"],$result["name_last"],$result["name_first"],$dob,
					$result["sex"],$addr,$result["zipcode"],$result["status"],$result["encounter_nr"],
					$result["encounter_type"],$result["discountid"],$result["discount"],$result['rid']);
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
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require($root_path.'include/care_api_classes/class_person.php');
	require($root_path."modules/pharmacy/ajax/order-psearch.common.php");
	$xajax->processRequests();
?>