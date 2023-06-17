<?php
	/*
	function populateICPMList($sElem,$keyword) {
		global $db;
		$objResponse = new xajaxResponse();

		$sql="SELECT icpm.code, icpm.description, icpm.rvu, icpm.multiplier
				FROM care_ops301_en AS icpm
				WHERE ( icpm.description REGEXP '[[:<:]]$keyword' OR icpm.code LIKE '%$keyword%' )
				ORDER BY icpm.description 
				LIMIT 20";

#$objResponse->addAlert("populateICPMList : sql = '".$sql."'");		
		$ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse->addScriptCall("clearList","procedure-list");
		while($result=$ergebnis->FetchRow()) {
			$description_short = $result["description"];
			if (strlen($description_short)>50)
				$description_short = substr(trim($result["description"]),0,50)."...";
			$objResponse->addScriptCall("addProductToList","procedure-list",trim($result["code"]),trim($description_short),trim($result["description"]),$result["rvu"],$result["multiplier"]);
		}
		if (!$rows) $objResponse->addScriptCall("addProductToList","procedure-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}
*/

#edited by VAN 04-22-08
	function populateICPMList($sElem,$searchkey,$page){
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		#$glob_obj->getConfig('pagin_or_patient_search_max_block_rows');
		#$maxRows = $GLOBAL_CONFIG['pagin_or_patient_search_max_block_rows']; # 5 rows
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$srv=new SegOps;
		#$objResponse->addAlert('page = '.$page);
		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
		#$total_srv = $srv->countSearchService($searchkey,$maxRows,$offset);
		#edited by VAN 08-27-08
		$total_srv = $srv->countSearchOP($searchkey,$maxRows,$offset);
		#$objResponse->addAlert($srv->sql);
		$total = $srv->count;
		#$objResponse->addAlert('total = '.$total);
		
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		#$ergebnis=$srv->SearchService($searchkey,$maxRows,$offset);
		#edited by VAN 08-27-08
		$ergebnis=$srv->SearchOP($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","procedure-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$description_short = $result["description"];
				if (strlen($description_short)>50)
					$description_short = substr(trim($result["description"]),0,50)."...";
	
				$objResponse->addScriptCall("addProductToList","procedure-list",trim($result["code"]),trim($description_short),trim($result["description"]),$result["rvu"],number_format($result["multiplier"],2));
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","procedure-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}

#-----------------------------

#		BURN ADDED : August 31, 2007

	function setALLDepartment($dept_nr=0){
#	global $dept_obj;

		$dept_obj=new Department;
		
		$objResponse = new xajaxResponse();
		#$objResponse->addAlert("setALLDepartment");
		$rs=$dept_obj->getAllMedicalObject();
#$objResponse->addAlert("setALLDepartment : rs = '".$rs."'");		
		$objResponse->addScriptCall("ajxClearDocDeptOptions",1);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddDocDeptOption",1,"-Select a Department-",0);
			while ($result=$rs->FetchRow()) {
			   $objResponse->addScriptCall("ajxAddDocDeptOption",1,$result["name_formal"],$result["nr"]);
			}
		if($dept_nr)
				$list='';
				$objResponse->addScriptCall("ajxSetDepartment", $dept_nr, $list); # set the department
		}
		else {
			$objResponse->addAlert("setALLDepartment : Error retrieving Department information...");
		}
		return $objResponse;
	}

	function setDepartmentOfDoc($personell_nr=0) {
#		global $dept_obj;

		$dept_obj=new Department;
		
		$objResponse = new xajaxResponse();
		#$objResponse->addAlert("setDepartmentOfDoc : personell_nr ='$personell_nr'");
      if ($personell_nr!=0){
			$result=$dept_obj->getDeptofDoctor($personell_nr);
			#$objResponse->addAlert("setDepartmentOfDoc : dept_obj->sql = '$dept_obj->sql'");
			#$objResponse->addAlert("setDepartmentOfDoc : name_formal = ".$result["name_formal"]." - ".$result["nr"]);
			if ($result){
				$list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
	#$objResponse->addAlert("setDepartmentOfDoc : list = '$list'; result['nr'] = '".$result['nr']."'");
				if (trim($list)!="")
					$list .= ",".$result["nr"];
				else
					$list .= $result["nr"];			
				$objResponse->addScriptCall("ajxSetDepartment",$result["nr"],$list); # set the department
			}
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor",$personell_nr); # set the doctor

		}else{
			$objResponse->addAlert("setDepartmentOfDoc : Error retrieving Department information of a doctor...");
		}	
		return $objResponse;
	}

	function setDoctors($dept_nr=0, $personell_nr=0) {
#		global $pers_obj;
		
		$objResponse = new xajaxResponse();

		$pers_obj=new Personell;
		#$objResponse->addAlert("dept : $dept_nr");
		if ($dept_nr)
			$rs=$pers_obj->getDoctorsOfDept($dept_nr);
		else
			$rs=$pers_obj->getDoctors(2);	# argument, $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

#		$objResponse->addAlert("setDoctors : dept_nr = '".$dept_nr."'");
#		$objResponse->addAlert("setDoctors : pers_obj->sql = '".$pers_obj->sql."'");
		#$objResponse->addAlert("setDoctors".$admit_inpatient."=".$dept_nr);
		
		$objResponse->addScriptCall("ajxClearDocDeptOptions",0);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-Select a Doctor-",0);
			
			while ($result=$rs->FetchRow()) {
			  	$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$result["name_last"];
				$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);
				$objResponse->addScriptCall("ajxAddDocDeptOption",0,$doctor_name,$result["personell_nr"]);
			}
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor", $personell_nr); # set the doctor
			if($dept_nr)
				$objResponse->addScriptCall("ajxSetDepartment", $dept_nr); # set the department
			$objResponse->addScriptCall("request_doc_handler"); # set the 'request_doctor_out' textbox
		}
		else {
			$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
		}
		return $objResponse;
	}

	
	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');	
	include_once($root_path.'include/care_api_classes/class_department.php');
	include_once($root_path.'include/care_api_classes/class_personell.php');
	
	#added by VAN 04-22-08
	#include_once($root_path.'include/care_api_classes/class_oproom.php');
	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');

	require($root_path."modules/or/ajax/op-icpm-tray.common.php");
	$xajax->processRequests();
?>