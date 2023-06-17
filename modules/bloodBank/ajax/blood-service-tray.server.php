<?php

#edited by VAN 03-18-08
function populateLabServiceList($area='',$sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv=new SegBloodBank();
		$offset = $page * $maxRows;

		$group_code = "B";

		$total_srv = $srv->countSearchService($group_code,$searchkey,$multiple,$maxRows,$offset,$area);
		#$objResponse->addAlert($srv->sql);
		$total = $srv->count;
		#$objResponse->addAlert('total = '.$total);

		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchService($group_code,$searchkey,$multiple,$maxRows,$offset,$area);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","request-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";

				if ($result["is_socialized"]){
						$sql2 = "SELECT DISTINCT * FROM seg_service_discounts
									 WHERE service_code='".$result["service_code"]."'";
						$res=$db->Execute($sql2);
						$row=$res->RecordCount();

						if ($row!=0){
							while($rsObj=$res->FetchRow()) {
								if ($rsObj["discountid"] == C1){
									$price_C1 = $rsObj["price"];
								}
								if ($rsObj["discountid"] == C2){
									$price_C2 = $rsObj["price"];
								}
								if ($rsObj["discountid"] == C3){
									$price_C3 = $rsObj["price"];
								}
							}
						}
					}else{
						$price_C1 = number_format(trim($result["price_cash"]),2,'.', '');
						$price_C2 = number_format(trim($result["price_cash"]),2,'.', '');
						$price_C3 = number_format(trim($result["price_cash"]),2,'.', '');
					}

				if ($result['status']=='unavailable')
						$available = 0;
				else
						$available = 1;

				$objResponse->addScriptCall("addProductToList","request-list",$result["service_code"],
														$name,$result["group_code"],$result["price_cash"],
														$result["price_charge"], $result['is_socialized'],$price_C1,$price_C2,$price_C3, $available);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","request-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}
#---------------------------------------------------

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
					#$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$result["name_last"];
				#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
				if (trim($result["name_middle"]))
					$dot  = ".";

				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";

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
			#$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-No Doctor Available-",0);
		}
		return $objResponse;
	}

	function getDeptDocValues($encounter_nr){
		global $db;
				$objResponse = new xajaxResponse();

				$enc_obj=new Encounter;

		$patient = $enc_obj->getPatientEncounter($encounter_nr);

		#$objResponse->alert($patient['current_dept_nr']);
		if (($patient['encounter_type']==1)|| ($patient['encounter_type']==2)){
			$dept_nr = $patient['current_dept_nr'];
			$doc_nr = $patient['current_att_dr_nr'];
		}elseif (($patient['encounter_type']==3)|| ($patient['encounter_type']==4)){
			$dept_nr = $patient['consulting_dept_nr'];
			$doc_nr = $patient['consulting_dr_nr'];
		}else{
			$dept_nr = 0;
			$doc_nr = 0;
		}

		$objResponse->addScriptCall("setDeptDocValues",$dept_nr, $doc_nr);

		return $objResponse;
	}

	#added by VAN 04-20-2010
	function getAllServiceOfPackage($service_code){
				global $db;
				$objResponse = new xajaxResponse();
				$srv=new SegBloodBank();

				#$objResponse->alert("ajax = ".$service_code);
				$rs_group = $srv->isServiceAPackage($service_code);
				$rs_count = $srv->count;

				#$objResponse->alert("ajax count = ".$rs_count);
				if ($rs_count){
					#$objResponse->alert("it is a package");
					$rs_group_inc = $srv->getAllServiceOfPackage($service_code);
					#$objResponse->alert($srv->sql);
					#lab exam request that is a package
					while ($row=$rs_group_inc->FetchRow()){
							#$objResponse->alert('ajax = '.$row['service_code']);
							$objResponse->addScriptCall("prepareAdd_Package",$row['service_code'],$row['name'],$row['cash'],$row['charge'],$row['sservice'],$row['group_code'],$row['priceC1'],$row['priceC2'],$row['priceC3']);
					}

				} else{
					 #lab exam request that is not a package
					 $objResponse->addScriptCall("prepareAdd_NotPackage",$service_code);
				}

				return $objResponse;
		}
		 #-----------------


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
	require($root_path."modules/bloodBank/ajax/blood-service-tray.common.php");

	#added by VAN 03-18-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_blood_bank.php');

	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$xajax->processRequests();
?>