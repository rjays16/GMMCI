<?php
	function populatePersonList($sElem,$searchkey,$page,$include_firstname,$include_encounter=TRUE) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		
		$person=& new Person();
		
		$ergebnis=$person->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
		#$objResponse->alert($person->sql);
		$total = $person->FoundRows();
		$lastPage = floor($total/$maxRows);
		if ($page > $lastPage) $page=$lastPage;
		
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","person-list");
		$details = (object) 'details';
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {

			$addr = implode(", ",array_filter(array($result['street_name'], $result["brgy_name"], $result["mun_name"])));
			if ($result["zipcode"])
				$addr.=" ".$result["zipcode"];
			if ($result["prov_name"])
				$addr.=" ".$result["prov_name"];

				$dob = $result["date_birth"];
				if (!$dob || $dob=="0000-00-00") $dob="Unknown";
				
				$lastId = $result["pid"];
				$details->id = $result["pid"];
				$details->lname = $result["name_last"];
				$details->fname = $result["name_first"];
				
				$details->mname = $result["name_middle"];
				
				$details->dob = $dob;
				$details->sex = $result["sex"];
				$details->addr = $addr;
				$details->zip = $result["zipcode"];
				$details->status = $result["civil_status"];
				
				#$objResponse->alert('status = '.$details->status);
				
			if (is_numeric($result["age"])){	
				if ($result["age"]==1)
					$details->age = $result["age"]." year";
				elseif (!$result["age"])
					$details->age = "unknown";
				elseif($result["age"]>1)
					$details->age = $result["age"]." years";
			}elseif (!$result["age"]){			
				$details->age = "unknown";
			}else
				$details->age = $result["age"];	
				
				$objResponse->addScriptCall("addPerson","person-list", $details);

			}
		}
		else {
			$details->error = nl2br(htmlentities($person->sql));
		}
		if (!$rows) $objResponse->addScriptCall("addPerson","person-list",$details);
        /*
		if ($rows==1 && $lastId) {			
			$objResponse->addScriptCall("prepareSelect",$lastId);
		} */
		
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');
	require_once($root_path.'classes/adodb/adodb-lib.inc.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_person.php');
	require_once($root_path."modules/dependents/ajax/dependent-psearch.common.php");
	
	#added by VAN 06-02-08
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_ward.php');
	
	$xajax->processRequests();
?>