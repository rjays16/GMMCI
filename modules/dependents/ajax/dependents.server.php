<?php
	
	function populateDependentsList($pid) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$dependent_Obj=new SegDependents();
		
		$offset = $page * $maxRows;
		
		$ergebnis=$dependent_Obj->getAllDependents($pid);
		#$objResponse->addAlert($dependent_Obj->sql);
		$rows=0;

		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			
			while($result=$ergebnis->FetchRow()) {
				
				$middleInitial = "";
				if (trim($result['name_middle'])!=""){
					$thisMI=split(" ",$result['name_middle']);	
					foreach($thisMI as $value){
						if (!trim($value)=="")
						$middleInitial .= $value[0];
					}
					if (trim($middleInitial)!="")
					$middleInitial .= ".";
				}
	
				$dependent_name = $result['name_last'].", ".$result['name_first']." ".$middleInitial;
				$dependent_name = ucwords(strtolower($dependent_name));
				$dependent_name = htmlspecialchars($dependent_name);
				
				if (is_numeric($result['person_age'])){	
					if ($result['person_age']==1)
						$age = $result['person_age']." year";
					elseif (!$result['person_age'])
						$age = "unknown";
					elseif($result['person_age']>1)
						$age = $result['person_age']." years";
				}elseif (!$result['person_age']){			
					$age = "unknown";
				}else
					$age = $result['person_age'];	
				
				$objResponse->addScriptCall("initialDependentList",$result['dependent_pid'],$dependent_name,$result['relationship'],
				                                                   $result['date_birth'],$age,$result['sex'],$result['civil_status']);
			}#end of while
		} #end of if
		
		if (!$rows) $objResponse->addScriptCall("initialDependentList",NULL);
		
		return $objResponse;
	}
	
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require($root_path."modules/dependents/ajax/dependents.common.php");
	#added by VAN 04-17-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	
	require_once($root_path.'include/care_api_classes/class_seg_dependents.php');
	include_once($root_path.'include/care_api_classes/class_paginator.php');
		 
	$xajax->processRequests();
?>