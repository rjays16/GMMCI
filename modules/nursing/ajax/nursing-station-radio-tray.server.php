<?php

	function populateRequestList($sElem,$keyword) {
		global $db;
		$objResponse = new xajaxResponse();
		$dbtable='seg_radio_services';

		$sql="SELECT r_serv.service_code, r_serv.group_code, r_serv.name, r_serv.is_socialized,
				IFNULL(r_serv.price_cash,0) AS price_cash,IFNULL(r_serv.price_charge,0) AS price_charge 
				FROM seg_radio_services AS r_serv
				WHERE name REGEXP '[[:<:]]$keyword' ORDER BY name";

		$ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse->addScriptCall("clearList","request-list");
		while($result=$ergebnis->FetchRow()) {
			$name = $result["name"];
			if (strlen($name)>40)
				$name = substr($result["name"],0,40)."...";
			$objResponse->addScriptCall("addProductToList","request-list",$result["service_code"],
												$name,$result["group_code"],$result["price_cash"],
												$result["price_charge"], $result['is_socialized']);
		}
		if (!$rows) $objResponse->addScriptCall("addProductToList","request-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}
	
	function populateRequestList2($group_code,$sElem,$keyword) {
		global $db;
		$objResponse = new xajaxResponse();
		#$dbtable='care_pharma_products_main';
		#$prctable = 'seg_pharma_prices';

		#$sql="SELECT a.*, b.ppriceppk, b.chrgrpriceppk, b.cshrpriceppk FROM $dbtable AS a LEFT JOIN $prctable AS b ON a.bestellnum=b.bestellnum WHERE artikelname REGEXP '[[:<:]]$keyword' ORDER BY artikelname";
	   
		
		if ($keyword=="*"){
			$sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g 
		   	     WHERE s.group_code=g.group_code 
					  AND s.group_code='$group_code'
				  	  ORDER BY s.name";
		}else{		  
			$sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g 
		   	     WHERE s.group_code=g.group_code 
					  AND s.group_code='$group_code'
					  AND ((s.service_code LIKE '%$keyword%') OR (s.name LIKE '%$keyword%'))
				  	  ORDER BY s.name";			  
		}
		#$objResponse->addAlert("populateRequestList sql = $sql");
		$ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		
		$objResponse->addScriptCall("clearList","request-list");
		while($result=$ergebnis->FetchRow()) {
			#$objResponse->addScriptCall("addProductToList","request-list",$result["bestellnum"],$result["artikelname"],substr($result["description"],0,40)."...",$result["cshrpriceppk"],$result["chrgrpriceppk"]);
			$objResponse->addScriptCall("addProductToList","request-list",$result["service_code"],$result["name"],$result["price_cash"],$result["price_charge"]);
		}
		if (!$rows) $objResponse->addScriptCall("addProductToList","request-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}

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
#	$dept_obj=new Department;
	include_once($root_path.'include/care_api_classes/class_personell.php');
#	$pers_obj=new Personell;

/*
	require($root_path.'include/care_api_classes/class_pharma_transaction.php');
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path."modules/laboratory/ajax/lab-new.common.php");
*/
	require($root_path."modules/nursing/ajax/nursing-station-radio-tray.common.php");
	$xajax->processRequests();
?>