<?php

function populateSpecialLabServiceList($area='',$ref_source='LB',$is_cash=1,$discountid='',$discount=0,$is_senior=0,$is_walkin=1,$group_code,$sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srv=new SegSpecialLab();
		$objSS = new SocialService;
		$offset = $page * $maxRows;

		#$group_code = $ref_source;

		if (!$discount)
			$discount = 0;

		$ssInfo = $objSS->getSSClassInfo($discountid);

		if ($discountid=='SC')
			$is_senior = 1;
		else
			$is_senior = 0;

		if ($ssInfo['parentid'])
			$discountid = $ssInfo['parentid'];

		#temporary.. it should be taken from the database
		$sc_walkin_discount = 0.20;

		#--------
		if (stristr($searchkey,",")){
			$keyword_multiple = explode(",",$searchkey);
			#$objResponse->alert($keyword_multiple[0]);
			$codenum = 0;
			if (is_numeric($keyword_multiple[0]))
					$codenum = 1;

			for ($i=0;$i<sizeof($keyword_multiple);$i++){
				$keyword .= "'".trim($keyword_multiple[$i])."',";
			}
			#$objResponse->addAlert('keyword1 = '.$keyword);
			$word = trim($keyword);
			#$objResponse->addAlert('word = '.$word);
			$searchkey = substr($word,0,strlen($word)-1);
			#$objResponse->addAlert('keyword = '.$keyword);
			$multiple = 1;
		}else{
			$multiple = 0;
		}
		#----------------

		#$objResponse->alert($group_code);
		$ergebnis=$srv->SearchService($ref_source,$is_cash,$discountid,$discount, $is_senior, $is_walkin, $sc_walkin_discount,$group_code,$codenum,$searchkey,$multiple,$maxRows,$offset,$area);
		#$objResponse->addAlert($srv->sql);
		$total = $srv->FoundRows();

		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","request-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$name = $result["name"];
				if (strlen($name)>40)
					$name = substr($result["name"],0,40)."...";

				if ($result['status']=='unavailable')
						$available = 0;
				else
						$available = 1;

				$objResponse->addScriptCall("addProductToList","request-list",$result["service_code"],
														$name,$result["group_code"],$result["price_cash"],
														$result["price_charge"], $result['is_socialized'],number_format($result['net_price'], 2, '.', ''), $available);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","request-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}
#---------------------------------------------------

	function setALLDepartment($dept_nr=0){
		$dept_obj=new Department;

		$objResponse = new xajaxResponse();

		$rs=$dept_obj->getAllMedicalObject();
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
		$dept_obj=new Department;

		$objResponse = new xajaxResponse();
			if ($personell_nr!=0){
			$result=$dept_obj->getDeptofDoctor($personell_nr);
			if ($result){
				$list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
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
		$objResponse = new xajaxResponse();

		$pers_obj=new Personell;
		if ($dept_nr)
			$rs=$pers_obj->getDoctorsOfDept($dept_nr);
		else
			$rs=$pers_obj->getDoctors(2);	# argument, $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

		$objResponse->addScriptCall("ajxClearDocDeptOptions",0);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-Select a Doctor-",0);

			while ($result=$rs->FetchRow()) {
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
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-No Doctor Available-",0);
		}
		return $objResponse;
	}

	function getDeptDocValues($encounter_nr){
		global $db;
				$objResponse = new xajaxResponse();

				$enc_obj=new Encounter;

		$patient = $enc_obj->getPatientEncounter($encounter_nr);

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

	function getAllServiceOfPackage($service_code, $is_cash=1, $discountid='',$discount=0,$is_senior=0,$is_walkin=1){
				global $db;
				$objResponse = new xajaxResponse();
				$srv=new SegSpecialLab();
				$objSS = new SocialService;

				$ssInfo = $objSS->getSSClassInfo($discountid);
				if ($ssInfo['parentid'])
					$discountid = $ssInfo['parentid'];

				#temporary.. it should be taken from the database
				$sc_walkin_discount = 0.20;

				#$objResponse->alert($is_cash." - ".$discountid." - ".$discount);
				$rs_group = $srv->isServiceAPackage($service_code);
				$rs_count = $srv->count;
				if ($rs_count){
					if (!$discount)
						$discount = 0;
					$rs_group_inc = $srv->getAllServiceOfPackage($service_code, $is_cash, $discountid, $discount, $is_senior, $is_walkin, $sc_walkin_discount);
					#$objResponse->alert('pkg = '.$srv->sql);
					#lab exam request that is a package
					while ($row=$rs_group_inc->FetchRow()){
							$objResponse->addScriptCall("prepareAdd_Package",$row['service_code'],$row['name'],$row['cash'],$row['charge'],$row['sservice'],$row['group_code'],number_format($row['net_price'], 2, '.', ''));
					}

				} else{
					 #lab exam request that is not a package
					 $objResponse->addScriptCall("prepareAdd_NotPackage",$service_code);
					 #$objResponse->alert('not pkg = '.$srv->sql);
				}

				return $objResponse;
		}
		 #-----------------


	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	require($root_path."modules/special_lab/ajax/splab-service-tray.common.php");
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/care_api_classes/class_special_lab.php');
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$xajax->processRequests();
?>