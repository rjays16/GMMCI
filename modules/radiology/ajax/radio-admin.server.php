<?php

	/* popserv function (Populate Services)
	*		Fetches the database for availe services associated with a specific service group
	*/
	function psrv($grp, $serv_code, $dept_nr) {
		$srvObj=new SegRadio();
		$objResponse = new xajaxResponse();

		#$objResponse->addAlert("psrv grp : $grp");
		#$objResponse->addAlert("psrv serv code : $serv_code");

		if (($serv_code=="none")||($serv_code=="*")){
			#$objResponse->addAlert("true");
			$ergebnis=$srvObj->getRadioServices(1,"group_code = '$grp'");
		}else{
			#$objResponse->addAlert("false");
			$ergebnis=$srvObj->getRadioServices(0,"group_code = '$grp' AND ((service_code LIKE '%$serv_code%') OR (name LIKE '%$serv_code%'))");
		}

		#$objResponse->addAlert("psrv sql : $srvObj->sql");

		$objResponse->addScriptCall("crow");
		$recCount = $srvObj->count;
		#$objResponse->addAlert("psrv recCount : $recCount");
		$counter=0;
		if ($recCount>0) {
			while($result=$ergebnis->FetchRow()) {
				$counter++;
				$objResponse->addScriptCall("nrow",$result["service_code"],$result["name"],$result["price_cash"],$result["price_charge"],$dept_nr);
			}
		}
		else {
			$objResponse->addScriptCall("nrow",NULL);
		}
		# $objResponse->addAlert(print_r($srvObj->sql,TRUE));
		return $objResponse;
	}

	function nsrv($code, $name, $cash, $charge, $grp, $dept_nr) {
		// Escape passed argument
		global $db;
		$srvObj=new SegRadio();
		$objResponse = new xajaxResponse();

		#$status=$srvObj->createRadioService($code, $name, $cash, $charge, '', $grp);
		$status=$srvObj->createRadioService($code, addslashes($name), $cash, $charge, '', $grp);
		#$objResponse->addAlert("nsrv sql : $srvObj->sql");

		if ($status) {
			$objResponse->addScriptCall("nrow", $code, $name, $cash, $charge, $dept_nr, TRUE);
			$objResponse->addScriptCall("clrForm");
		}
		else {
			$objResponse->addScriptCall("showme", $srvObj->sql);
			$objResponse->addAlert("ERROR:".$db->ErrorMsg());
		}
		return $objResponse;
	}

	function dsrv($rowno,$code,$dept_nr) {
		$srvObj=new SegRadio();
		$objResponse = new xajaxResponse();

		//$objResponse->addAlert("dsrv deptnr = $dept_nr");
		$status=$srvObj->deleteRadioService($code);

		$status2=$srvObj->deleteServiceDiscounts($code,$dept_nr);

		#if ($status) {
		if (($status)&&$status2) {
			$objResponse->addScriptCall("gui_delRow", "serviceTable","srvRow",$rowno);
			$objResponse->addScriptCall("colt", "serviceTable");

		}
		else
			$objResponse->addScriptCall("showme", $srvObj->sql);
		return $objResponse;
	}

	#added by VAN 03-15-08------------------
	function populateRadioServiceList($group_code,$sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srvObj=new SegRadio();
		$offset = $page * $maxRows;

		#$total_srv = $srvObj->countSearchService($group_code,$searchkey,$maxRows,$offset);
		$ergebnis=$srvObj->SearchService($group_code,$searchkey,$maxRows,$offset);
		#$objResponse->addAlert($srvObj->sql);
		$total = $srvObj->FoundRows();
		#$objResponse->addAlert('total = '.$total);

		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		#$ergebnis=$srvObj->SearchService($group_code,$searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql = ".$srvObj->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","ServiceList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				if ($result['status']=='unavailable')
					$available = 0;
				else
					$available = 1;

				#$objResponse->addAlert("id, name, cash, charge, sservice = ".trim($result["service_code"])." , ".trim($result["name"])." , ".number_format(trim($result["price_cash"]),2,'.', '')." , ".number_format(trim($result["price_charge"]),2,'.', '')." , ".$result["is_socialized"]);
				$objResponse->addScriptCall("addProductToList","ServiceList",trim($result["service_code"]),trim($result["name"]),number_format(trim($result["price_cash"]),2,'.', ''),number_format(trim($result["price_charge"]),2,'.', ''), $result["is_socialized"],$available,$result["is_ER"]);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","ServiceList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function deleteService($code){
		global $db;
		$srvObj=new SegRadio();
		$dept_obj=new Department;
		$objResponse = new xajaxResponse();

		$sql = "SELECT service_code FROM care_test_request_radio WHERE service_code = '$code'
					UNION
							SELECT code AS service_code FROM seg_hcare_srvops WHERE code='$code' AND provider='RD'";

		 $res=$db->Execute($sql);
		 #$objResponse->addAlert("sql = ".$sql);
		 $row=$res->RecordCount();
		 #$objResponse->addAlert("row = ".$row);

		if ($row==0){

			$status=$srvObj->deleteRadioService($code);
			#$objResponse->addAlert("sql delete 1 = ".$srvObj->sql);
			$status2=$srvObj->deleteServiceDiscounts($code,'RD');
			#$objResponse->addAlert("sql delete 2 = ".$srvObj->sql);
			$status3=$srvObj->deleteRoomRate($code, 'RD');

				if (($status)&&$status2&&$status3) {
					$objResponse->addScriptCall("removeService",$code);
					$objResponse->addAlert("The radiological service is successfully deleted.");
				}else
					$objResponse->addScriptCall("showme", $srv->sql);
		 }else{
				$objResponse->addAlert("The radiological service cannot be deleted. It is already been used.");
		 }
		return $objResponse;
	}
	#----------------------------------------


	/*Added by VAS*/
	/*get the respective Group oratory services that belongs to a certain Department*/
	#function getServiceGroup($dept_nr, $group_id='') {
	#function getServiceGroup($group_code='') {
	function getServiceGroup($dept_nr='') {
		$srvObj=new SegRadio();

		$objResponse = new xajaxResponse();

		#$objResponse->addAlert("xajax getServiceGroup: group_id = '$group_code'");
		#$objResponse->addAlert("xajax getServiceGroup: dept_nr = '$dept_nr'");
		#$objResponse->addAlert("getServiceGroup");
		#$objResponse->addScriptCall("nrow_null",NULL);

		#$rs=$srvObj->getRadioServiceGroups2("department_nr = '$dept_nr'");
		$rs=$srvObj->getRadioServiceGroups("department_nr = '$dept_nr'");
		#$objResponse->addAlert("xajax getServiceGroup: sql = ".$srvObj->sql);

		#$objResponse->addAlert("getServiceGroup sql : $srvObj->sql");

		if ($rs) {
			$objResponse->addScriptCall("ajxClearOptions");
			if ($srvObj->count > 0){
					$objResponse->addScriptCall("ajxAddOption","Select Service Group",0);
			}else{
				$objResponse->addScriptCall("ajxAddOption","No Service Group",0);
			}

			while ($result=$rs->FetchRow()) {
				$objResponse->addScriptCall("ajxAddOption",$result["name"],$result["group_code"]);
			}

			#$objResponse->addScriptCall("ajxSetServiceGroup",$group_code);

		}
		else {
			#$objResponse->addAlert("getServiceGroup : Error retrieving radio service groups information...");
			$objResponse->addScriptCall("ajxClearOptions");
			$objResponse->addScriptCall("ajxAddOption","No Service Group",0);
		}
		return $objResponse;

	}

	#added by VAN 03-17-08
	function populateRadioGroupList($sElem,$searchkey,$dept_nr,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srvObj=new SegRadio();
		$offset = $page * $maxRows;
		#$objResponse->addAlert("searchkey = ".$searchkey);
		if ($searchkey==NULL)
			$searchkey = '*';
		$total_srv = $srvObj->countSearchGroup($searchkey,$dept_nr,$maxRows,$offset);
		#$objResponse->addAlert("sql c1 = ".$srvObj->sql);
		$total = $srvObj->count;
		#$objResponse->addAlert("total = ".$total);
		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srvObj->SearchGroup($searchkey,$dept_nr,$maxRows,$offset);
		#$objResponse->addAlert("sql c2 = ".$srvObj->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","radiogrouplistTable");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				#$objResponse->addAlert("sql c2 = ".$result["group_code"]." , ".$result["name"]." , ".$result["other_name"]." , ".$result["name_formal"]);
				#$code = str_replace("^","'",stripslashes($labgroupInfo['group_code']));;
				 $objResponse->addScriptCall("addRadioGroup","radiogrouplistTable",strtoupper(stripslashes($result["group_code"])),stripslashes($result["name"]),strtoupper(stripslashes($result["other_name"])), stripslashes($result["name_formal"]), $result["department_nr"]);
			}
		}
		if (!$rows) $objResponse->addScriptCall("addRadioGroup","radiogrouplistTable",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function deleteRadioGroup($grp_id, $dept_nr){
		global $db;
		$srvObj=new SegRadio();
		$objResponse = new xajaxResponse();

		//$grp_id = stripslashes($grp_id);
		$grp_id = addslashes($grp_id);

		$sql = "SELECT * FROM seg_radio_services WHERE group_code='".$grp_id."'";
		#$objResponse->addAlert("sql = ".$sql);
		#$objResponse->addAlert("grp id, dept nr  = ".$grp_id." , ".$dept_nr);
		$res=$db->Execute($sql);
		$row=$res->RecordCount();

		if ($row==0){
			$status=$srvObj->deleteServiceGroup($grp_id, $dept_nr);
			#$objResponse->addAlert("sql = ".$srvObj->sql);

			if ($status) {
				$objResponse->addScriptCall("removeRadioGroup",$grp_id, $dept_nr);
				$objResponse->addAlert("The radiological service group is successfully deleted.");
			}else{
				$objResponse->addScriptCall("showme", $srvObj->sql);
			}
		 }else{
				$objResponse->addAlert("The radiological service group cannot be deleted. It is already been used.");
		 }
		return $objResponse;
	}
	#----------------------------------

	#added by VAN 07-07-08
	function populateRadioFindingsList($sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srvObj=new SegRadio();
		$offset = $page * $maxRows;
		#$objResponse->addAlert("searchkey = ".$searchkey);
		if ($searchkey==NULL)
			$searchkey = '*';
		$total_srv = $srvObj->countSearchFindings($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c1 = ".$srvObj->sql);
		$total = $srvObj->count;
		#$objResponse->addAlert("total = ".$total);
		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srvObj->SearchFindings($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c2 = ".$srvObj->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","radiogrouplistTable");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$objResponse->addScriptCall("addRadioFindings","radiogrouplistTable",$result["id"], strtoupper(stripslashes($result["codename"])),stripslashes($result["description"]),stripslashes($result['impdesc']));
			}
		}
		if (!$rows) $objResponse->addScriptCall("addRadioFindings","radiogrouplistTable",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function deleteRadioFindings($id){
		global $db;
		$srvObj=new SegRadio();
		$objResponse = new xajaxResponse();

		#$sql = "SELECT * FROM care_test_findings_radio WHERE findings='".$id."'";

		#$res=$db->Execute($sql);
		#$row=$res->RecordCount();

		#if ($row==0){
			$status=$srvObj->deleteServiceFindings($id);
			#$objResponse->addAlert("sql = ".$srvObj->sql);

			if ($status) {
				$objResponse->addScriptCall("removeRadioFinding",$id);
				$objResponse->addAlert("The radiological service finding's code is successfully deleted.");
			}else{
				$objResponse->addScriptCall("showme", $srvObj->sql);
			}
		# }else{
			#	$objResponse->addAlert("The radiological service finding's code cannot be deleted. It is already been used.");
		# }
		return $objResponse;
	}
	#-----------------------

	#------added by VAN 07-11-08
	function populateRadioImpressionList($sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srvObj=new SegRadio();
		$offset = $page * $maxRows;
		#$objResponse->addAlert("searchkey = ".$searchkey);
		if ($searchkey==NULL)
			$searchkey = '*';
		$total_srv = $srvObj->countSearchImpressions($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c1 = ".$srvObj->sql);
		$total = $srvObj->count;
		#$objResponse->addAlert("total = ".$total);
		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srvObj->SearchImpressions($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c2 = ".$srvObj->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","radiogrouplistTable");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$objResponse->addScriptCall("addRadioFindings","radiogrouplistTable",$result["id"], strtoupper(stripslashes($result["codename"])),stripslashes($result["description"]),stripslashes($result['impdesc']));
			}
		}
		if (!$rows) $objResponse->addScriptCall("addRadioFindings","radiogrouplistTable",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function deleteRadioImpression($id){
		global $db;
		$srvObj=new SegRadio();
		$objResponse = new xajaxResponse();

		#$sql = "SELECT * FROM care_test_findings_radio WHERE findings='".$id."'";

		#$res=$db->Execute($sql);
		#$row=$res->RecordCount();

		#if ($row==0){
			$status=$srvObj->deleteServiceImpression($id);
			#$objResponse->addAlert("sql = ".$srvObj->sql);

			if ($status) {
				$objResponse->addScriptCall("removeRadioFinding",$id);
				$objResponse->addAlert("The radiological service impression's code is successfully deleted.");
			}else{
				$objResponse->addScriptCall("showme", $srvObj->sql);
			}
		# }else{
		# 		$objResponse->addAlert("The radiological service finding's code cannot be deleted. It is already been used.");
		# }
		return $objResponse;
	}
	#----------------------------

	#-----added by VAN 09-11-08
	function populateRadioPartnersList($sElem,$searchkey,$page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$srvObj=new SegRadio();
		$offset = $page * $maxRows;
		#$objResponse->addAlert("searchkey = ".$searchkey);
		if ($searchkey==NULL)
			$searchkey = '*';
		$total_srv = $srvObj->countSearchAllPartners($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c1 = ".$srvObj->sql);
		$total = $srvObj->count;
		#$objResponse->addAlert("total = ".$total);
		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;

		$ergebnis=$srvObj->SearchAllPartners($searchkey,$maxRows,$offset);
		#$objResponse->addAlert("sql c2 = ".$srvObj->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","radiogrouplistTable");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
								$rs_member = $srvObj->getAllGroupMembers($result["group_nr"]);
								if ($rs_member){
										$members = "";
										while($doctor = $rs_member->FetchRow()){
											 $doctor_name = $doctor['name_first']." ".mb_strtoupper(substr($doctor['name_middle'],0,1))." ".$doctor['name_last'];
												#$members .=  $doctor_name." ,";
												 if ($members)
														$members = $members.", ";
														$members .= $doctor_name;
										}
								}

				$objResponse->addScriptCall("addRadioFindings","radiogrouplistTable",$result["group_nr"], stripslashes($result["group_name"]),$members);
			}
		}
		if (!$rows) $objResponse->addScriptCall("addRadioFindings","radiogrouplistTable",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

	function deleteRadioPartners($id){
		global $db;
		$srvObj=new SegRadio();
		$objResponse = new xajaxResponse();

		#$sql = "SELECT * FROM care_test_findings_radio WHERE findings='".$id."'";

		#$res=$db->Execute($sql);
		#$row=$res->RecordCount();

		#if ($row==0){
			#$status=$srvObj->deleteServiceImpression($id);
			$status=$srvObj->deleteDoctorPartner($id);
			#$objResponse->addAlert("sql = ".$srvObj->sql);

			if ($status) {
				$objResponse->addScriptCall("removeRadioPartners",$id);
				$objResponse->addAlert("The radiology co-reader physician group is successfully deleted.");
			}else{
				$objResponse->addScriptCall("showme", $srvObj->sql);
			}
		# }else{
		# 		$objResponse->addAlert("The radiological service finding's code cannot be deleted. It is already been used.");
		# }
		return $objResponse;
	}
	#---------------------------
	#------------ADDED BY CELSY 08/11/10------------
	function setImpression($dept_nr){
		global $db; 
		$objResponse = new xajaxResponse();  
		$no_error = true;                                  
		$db->StartTrans();                               
				
		$sql="SELECT * FROM seg_radio_impression_code	WHERE department_nr='".$dept_nr."' 	ORDER BY codename ASC";		
		$details = "<option value='0'>-Select Impression's Code-</option>";
		$hiddenData = '';
		if($result = $db->Execute($sql)){                            
		$num_rows = $result->RecordCount();
			if($num_rows>=1){
				while($row = $result->FetchRow()) {  
					$details .= '<option id=" impid'.$row["id"].'" value="'.$row["id"].'" onMouseover="mouseOverImp(this,\''.$row["id"].'\');" onMouseout="return nd();">'.$row["codename"].'</option>';	
					$hiddenData .= '<input type="hidden" id="impcode'.$row2["id"].'" name="impcode'.$row2["id"].'" value="'.$row2["description"].'">';
				} 
			}
			$objResponse->assign('impression', 'innerHTML', $details);
			$objResponse->addAssign("hidden_data","innerHTML", $hiddenData);   
			$db->CompleteTrans();       			
	 }
	 else{
		echo "<br>ERROR2 @ radio-admin-setImpression :".$sql."<br>".$db->ErrorMsg()."<br>";
		$no_error=false;
		$db->FailTrans();
	}     
	return $objResponse;   	
}
	#---------------------------    
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/class_radiology.php');
	require($root_path."modules/radiology/ajax/radio-admin.common.php");
	#added by VAN 03-15-08
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require($root_path.'include/care_api_classes/class_department.php');

	$xajax->processRequests();

?>