<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_lab_results.php');
require($root_path.'include/care_api_classes/class_labservices_transaction.php');
require($root_path.'modules/laboratory/test_manager/ajax/seg_lab_test.common.php');

function saveTestGroup($group_name, $services, $order_nr, $form_id='')
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new Lab_Results();

	$db->StartTrans();
	if($group_id = $labObj->addGroup($group_name, $form_id)){
		for($i=0;$i<count($services);$i++)
		{
			$saveok = $labObj->addParamToGroup($group_id,$services[$i],$order_nr[$i]);
			if(!$saveok)
			{
				$db->FailTrans();
				$objResponse->call("outputResponse", "Save not successful.");
				return $objResponse;
			}
		}
		$db->CompleteTrans();
		$objResponse->call("outputResponse", "Save successful.");
	}else
	{
		$db->FailTrans();
		$objResponse->call("outputResponse", "Save not successful. ERROR:".$db->ErrorMsg());
	}

	return $objResponse;
}

function deleteTestGroup($group_id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new Lab_Results();
	$db->StartTrans();

	//get current list of services for this group
	$result = $labObj->getGroupServices($group_id);
	if($result->RecordCount()>0){
		$lab_obj = new SegLab();
		$serviceCodes = array();
		while($row=$result->FetchRow())
		{
			$serviceCodes[] = $row["service_code"];
		}

		//delete parameter assigment
		if(!empty($serviceCodes)) {
			for($i=0;$i<count($serviceCodes);$i++) {
				$res = $lab_obj->getParametersByService($serviceCodes[$i]);
				if($res){
					while($p=$res->FetchRow())
					{
						$saveok = $lab_obj->DeleteParameter($p["param_id"],$serviceCodes[$i]);
					}
				}else {
					$db->FailTrans();
					$objResponse->call("outputResponse", "DELETE not successful. ERROR=".$lab_obj->getErrorMsg()."\nSQL=".$lab_obj->sql);
					return $objResponse;
				}
			}
		}
	}

	$saveok = $labObj->deleteTestGroup($group_id);
	if($saveok)
	{
		$db->CompleteTrans();
		$objResponse->call("outputResponse", "Delete successful.");
	}else
	{
		$db->FailTrans();
		$objResponse->call("outputResponse", "Delete not successful. ERROR=".$labObj->getErrorMsg()."\nSQL=".$labObj->sql);
	}

	return $objResponse;
}

function populateTestGroup($group_id,$group_name)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new Lab_Results();

	$name = $labObj->getGroupName($group_id);
	$form_id = $labObj->getGroupForm($group_id);
	$objResponse->assign("new_group", "value", $name);
	$objResponse->assign("special_form", "value", $form_id);

	$grp_services = $labObj->getGroupServices($group_id);
	while($row = $grp_services->FetchRow())
	{
		$objResponse->call("listGroupServices", $row["service_code"], $row["name"], $row["order_nr"],$row["has_params"],$group_id,$group_name);
	}
	return $objResponse;
}

function updateTestGroup($group_id, $group_name, $services, $order_nr, $special_form='')
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new Lab_Results();

	$db->StartTrans();
	if($saveok = $labObj->editGroup($group_id, $group_name, $special_form)){

		//get current list of services for this group
		$result = $labObj->getGroupServices($group_id);
		if($result->RecordCount()>0){
			$serviceCodes = array();
			while($row=$result->FetchRow())
			{
				$serviceCodes[] = $row["service_code"];
			}
			$keys = array_values(array_diff($serviceCodes,$services));

			//delete parameter assigment
			if(!empty($keys)) {
				$lab_obj = new SegLab();
				for($i=0;$i<count($keys);$i++) {
					$saveok = $lab_obj->updateParamGroupId("", $keys[$i]);
					if(!$saveok) {
						$db->FailTrans();
						$objResponse->call("outputResponse", "Update not successful. ERROR=".$lab_obj->getErrorMsg()."\nSQL=".$lab_obj->sql);
						return $objResponse;
					}
				}
			}
		}

		if($saveok = $labObj->deleteParamsFromGroup($group_id))
		{
			for($i=0;$i<count($services);$i++)
			{
				$saveok = $labObj->addParamToGroup($group_id,$services[$i],$order_nr[$i]);
				if(!$saveok)
				{
					$db->FailTrans();
					$objResponse->call("outputResponse", "Update not successful.");
					return $objResponse;
				}
			}
			$db->CompleteTrans();
			$objResponse->call("outputResponse", "Update successful.");
		}else
		{
			$db->FailTrans();
			$objResponse->call("outputResponse", "Update not successful. ERROR:".$db->ErrorMsg());
		}
	}else
	{
		$db->FailTrans();
		$objResponse->call("outputResponse", "Update not successful. ERROR:".$db->ErrorMsg());
	}
	return $objResponse;
}

function saveTestParameter($details)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new SegLab();

	$is_numeric=0;
	if($details['datatype']=='1')
		$is_numeric=1;

	$is_boolean=0;
	if($details['datatype']=='2')
		$is_boolean=1;

	$is_longtext=0;
	if($details['datatype']=='3')
		$is_longtext=1;

	/*$is_female=0;
	if($details['gender']=='1')
		$is_female=1;

	$is_male=0;
	if($details['gender']=='0')
		$is_male=1;

	if($details['gender']=='2')
	{
		$is_female=1;
		$is_male=1;
	}*/

	$data = array(
		'name'=>$details['name'],
		'is_numeric'=>$is_numeric,
		'is_boolean'=>$is_boolean,
		'is_longtext'=>$is_longtext,
		//'order_nr'=>$details['order_nr'],
		'SI_unit'=>$details['si_unit'],
		'SI_lo_normal'=>$details['si_low'],
		'SI_hi_normal'=>$details['si_high'],
		'CU_unit'=>$details['cu_unit'],
		'CU_lo_normal'=>$details['cu_low'],
		'CU_hi_normal'=>$details['cu_high'],
		/*'is_male'=>$is_male,
		'is_female'=>$is_female,*/
		'norm_type'=> $details['gender'], //added by mai 09052014
		'param_group_id'=>$details['param_group'],
		'group_id'=>$details['test_group']
	);

	if($labObj->AddParameter($details["service_code"],$details['order_nr'],$data))
	{
		$objResponse->call("outputResponse", "Save successful.");
	}
	else
	{
		$objResponse->call("outputResponse", "Save not successful. ERROR=".$labObj->getErrorMsg()."\n SQL=".$labObj->sql);
	}
	return $objResponse;
}

function deleteTestParameter($param_id,$service_code)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new SegLab();

	if($labObj->DeleteParameter($param_id,$service_code))
	{
		$objResponse->call("outputResponse", "Delete successful.");
	}
	else
	{
		$objResponse->call("outputResponse", "Delete not successful. ERROR=".$labObj->getErrorMsg()."\n SQL=".$labObj->sql);
	}
	return $objResponse;
}

function updateTestParameter($param_id,$details)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new SegLab();

	$is_numeric=0;
	if($details['datatype']=='1')
		$is_numeric=1;

	$is_boolean=0;
	if($details['datatype']=='2')
		$is_boolean=1;

	$is_longtext=0;
	if($details['datatype']=='3')
		$is_longtext=1;

	/*$is_female=0;
	if($details['gender']=='1')
		$is_female=1;

	$is_male=0;
	if($details['gender']=='0')
		$is_male=1;

	if($details['gender']=='2')
	{
		$is_female=1;
		$is_male=1;
	}*/

	$data = array(
		'name'=>$details['name'],
		'is_numeric'=>$is_numeric,
		'is_boolean'=>$is_boolean,
		'is_longtext'=>$is_longtext,
		//'order_nr'=>$details['order_nr'],
		'SI_unit'=>$details['si_unit'],
		'SI_lo_normal'=>$details['si_low'],
		'SI_hi_normal'=>$details['si_high'],
		'CU_unit'=>$details['cu_unit'],
		'CU_lo_normal'=>$details['cu_low'],
		'CU_hi_normal'=>$details['cu_high'],
		 /*'is_male'=>$is_male,
		'is_female'=>$is_female,*/
		'norm_type'=>$details['gender'], //added by mai
		'param_group_id'=>$details['param_group'],
		'group_id'=>$details['test_group']
	);

	if($labObj->UpdateParameter($param_id, $details['order_nr'], $details["service_code"], $data))
	{
		$objResponse->call("outputResponse", "Update successful.");
	}
	else
	{
		$objResponse->call("outputResponse", "Update not successful. ERROR=".$labObj->getErrorMsg()."\n SQL=".$labObj->sql);
	}
	return $objResponse;
}

function saveParamGroup($paramgrp_name)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new SegLab();

	if($saveok=$labObj->addParamGroup($paramgrp_name))
	{
		$objResponse->call("outputResponse", "Save successful.");
	}
	else
	{
		$objResponse->call("outputResponse", "Save not successful. ERROR=".$labObj->getErrorMsg()."\n SQL=".$labObj->sql);
	}

	return $objResponse;
}

function deleteParamGroup($param_grp_id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new SegLab();

	if($saveok=$labObj->deleteParamGroup($param_grp_id))
	{
		$objResponse->call("outputResponse", "Delete successful.");
	}
	else
	{
		$objResponse->call("outputResponse", "Delete not successful. ERROR=".$labObj->getErrorMsg()."\n SQL=".$labObj->sql);
	}

	return $objResponse;
}

function updateParamGroup($id, $name)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new SegLab();

	if($saveok=$labObj->updateParamGroup($id, $name))
	{
		$objResponse->call("outputResponse", "Update successful.");
	}
	else
	{
		$objResponse->call("outputResponse", "Update not successful. ERROR=".$labObj->getErrorMsg()."\n SQL=".$labObj->sql);
	}

	return $objResponse;
}

function removeGrpAssignment($grp_id, $service_id)
{
	global $db;
	$objResponse = new xajaxResponse();

	$sql = "UPDATE seg_lab_result_groupparams SET status='deleted', modify_id='".$_SESSION["sess_temp_userid"]."', ".
	"modify_time=NOW() WHERE service_code='".$service_id."' AND group_id='".$grp_id."'";
	if($result = $db->Execute($sql))
	{
		if($db->Affected_Rows()>0)
		{
			$objResponse->call("outputResponse", "Remove group assignment successful.");
		}
	}else
	{
		$objResponse->call("outputResponse", "Remove group assignment not successful. ERROR=".$db->ErrorMsg()."\n SQL=".$sql);
	}

	return $objResponse;
}

/*function addTestGrpAssignment($grp_id, $service_id)
{
	global $db;
	$objResponse = new xajaxResponse();

	return $objResponse;
}*/

function emptyParameters($service_code)
{
	global $db;
	$objResponse = new xajaxResponse();

	$db->StartTrans();
	$sql = "SELECT param_id FROM seg_lab_result_param_assignment WHERE status <> 'delete' AND service_code=".$db->qstr($service_code);
	$result = $db->Execute($sql);
	if($result->RecordCount()>0)
	{
		while($row=$result->FetchRow())
		{
			$sql2 = "UPDATE seg_lab_result_params SET status='deleted', modify_id='".$_SESSION["sess_temp_userid"]."', ".
					"modify_dt=NOW() WHERE param_id=".$db->qstr($row["param_id"]);
			if($result2 = $db->Execute($sql2))
			{
				if(!$db->Affected_Rows()) {
					$db->FailTrans();
					$objResponse->call("outputResponse", "Delete all parameters not successful. ERROR=".$db->ErrorMsg()."\n SQL=".$sql2);
				}
			}
		}
		/*$sql3 = "UPDATE seg_lab_result_param_assignment SET status='deleted', modify_id='".$_SESSION["sess_temp_userid"]."', ".
				"modify_date=NOW() WHERE service_code=".$db->qstr($service_code);*/
		$sql3 = "DELETE FROM seg_lab_result_param_assignment WHERE service_code=".$db->qstr($service_code);
		if($result3 = $db->Execute($sql3)) {
			if($db->Affected_Rows()>0) {
				$db->CompleteTrans();
				$objResponse->call("outputResponse", "Delete all parameters successful.");
			}
		}
		else {
			$db->FailTrans();
			$objResponse->call("outputResponse", "Delete all parameters not successful. ERROR=".$db->ErrorMsg()."\n SQL=".$sql3);
		}
	}
	else {
		$db->FailTrans();
		$objResponse->call("outputResponse", "Delete all parameters not successful. ERROR=".$db->ErrorMsg()."\n SQL=".$sql);
	}
	/*$sql = "UPDATE seg_lab_result_params SET status='deleted', modify_id='".$_SESSION["sess_temp_userid"]."', ".
	"modify_dt=NOW() WHERE service_code=".$db->qstr($service_code);

	if($result = $db->Execute($sql))
	{
		if($db->Affected_Rows()>0)
		{
			$objResponse->call("outputResponse", "Delete all parameters successful.");
		}
	}else
	{
		$objResponse->call("outputResponse", "Delete all parameters not successful. ERROR=".$db->ErrorMsg()."\n SQL=".$sql);
	}*/

	return $objResponse;
}

function newGroupId()
{
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "SELECT group_id FROM seg_lab_result_groupname WHERE status <> 'deleted' ORDER BY group_id DESC";
	$group_id = $db->GetOne($sql);
	$error_msg = $db->ErrorMsg();
	if($group_id || empty($error_msg))
	{
		$objResponse->assign("group_id", "value", $group_id+1);
	}else
	{
		$objResponse->alert("SQL ERROR:".$error_msg."\nSQL:".$sql);
	}
	return $objResponse;
}

function newOrderNo($service_id, $group_id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "SELECT pa.order_nr FROM seg_lab_result_param_assignment AS pa \n".
		"LEFT JOIN seg_lab_result_params AS p ON pa.param_id=p.param_id \n".
		"WHERE pa.status <> 'deleted' \n";
		//"AND pa.service_code=".$db->qstr($service_id);
	if($group_id!="")
		$sql.=" AND group_id=".$db->qstr($group_id);
	$sql.=" ORDER BY pa.order_nr DESC";
	//$objResponse->alert($sql);
	$order_no = $db->GetOne($sql);
	$error_msg = $db->ErrorMsg();
	if($order_no || empty($error_msg))
	{
		$objResponse->assign("order_no", "value", $order_no+1);
	}else
	{
		$objResponse->alert("SQL ERROR:".$error_msg."\nSQL:".$sql);
	}
	return $objResponse;
}

function copyParams($service_id, $paramId_array, $paramOrder_array)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new SegLab();
	$db->StartTrans();

	for($i=0;$i<count($paramId_array);$i++)
	{
		$sql = "SELECT IF(EXISTS(SELECT pa.param_id FROM seg_lab_result_param_assignment AS pa \n".
		"	WHERE pa.service_code='".$service_id."' AND pa.param_id='".$paramId_array[$i].
		"' AND pa.order_nr='".$paramOrder_array[$i]."'),1,0) AS `is_exists`";
		$is_exists = $db->GetOne($sql);
		if(!$is_exists) {
			$saveok = $labObj->addParamAssignment($paramId_array[$i],$service_id,$paramOrder_array[$i],1);
			if(!$saveok) {
				$db->FailTrans();
				$objResponse->alert("Copy not successful. ERROR=".$labObj->getErrorMsg()."\n SQL=".$labObj->sql);
				return $objResponse;
			}
		}
	}
	$db->CompleteTrans();
	$objResponse->alert("Copy successful.");
	$objResponse->call("outputResponse", "");

	return $objResponse;
}

function undoCopyOfParams($service_id, $group_id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$labObj = new SegLab();
	$db->StartTrans();

	/*$sql = "UPDATE seg_lab_result_param_assignment SET status='deleted', modify_date=NOW(), \n".
			"modify_id=".$db->qstr($_SESSION['sess_temp_userid'])." WHERE is_copied='1' \n".
			"AND service_code=".$db->qstr($service_id);*/
	$sql = "DELETE FROM seg_lab_result_param_assignment WHERE is_copied='1' AND service_code=".$db->qstr($service_id);
	if ($result=$db->Execute($sql)) {
		if ($db->Affected_Rows()>0) {
			$db->CompleteTrans();
			$objResponse->alert("Delete copied parameters successful.");
			$objResponse->call("outputResponse", "");
		}else {
			$db->FailTrans();
			$objResponse->alert("Delete copied parameters not successful. ERROR=".$db->ErrorMsg()."\n SQL=".$sql);
			//$objResponse->call("outputResponse", "");
		}
	}else {
		$db->FailTrans();
		$objResponse->alert("Delete copied parameters not successful. ERROR=".$db->ErrorMsg()."\n SQL=".$sql);
		//$objResponse->call("outputResponse", "");
	}
	return $objResponse;
}

function checkExistingParam($param_name, $service_code)
{
	global $db;
	$objResponse = new xajaxResponse();

	$sql = "SELECT IF(EXISTS(SELECT p.name FROM seg_lab_result_params AS p \n".
					"	LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id \n".
					"	WHERE p.status <> 'deleted'	AND pa.service_code='".$service_code."' \n".
					"	AND p.name like '%".$param_name."%') \n".
				",1,0) AS `is_exists`";
	$is_exists = $db->GetOne($sql);
	if($is_exists){
		$message = $param_name." already exists for this service.";
		$objResponse->call("checkAlert", TRUE, $message);
	}else {
		$objResponse->call("checkAlert", NULL);
	}
	return $objResponse;
}

function populateParamChecklist($service_id)
{
	global $db;
	$objResponse = new xajaxResponse();

	$sql = "SELECT p.param_id, p.name, pa.order_nr, pg.name as `pg_name` \n".
				"FROM seg_lab_result_params AS p \n".
				"LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id \n".
				"LEFT JOIN seg_lab_result_paramgroups AS pg ON p.param_group_id=pg.param_group_id \n".
				"WHERE pa.service_code='".$service_id."' AND (ISNULL(p.status) OR p.status <>'deleted') \n".
				"ORDER BY pa.order_nr";
	$result = $db->Execute($sql);
	$objResponse->assign("param-list-body", "innerHTML", "");
	if($result->RecordCount()>0) {
		while($row=$result->FetchRow())
		{
			$details->param_id = $row["param_id"];
			$details->param_name = $row["name"];
			$details->param_order = $row["order_nr"];
			$details->param_group = $row["pg_name"];
			$objResponse->call("printChecklist", $details);
		}
	}

	return $objResponse;
}

function addNewFormGroup($value, $id){
	$objResponse = new xajaxResponse();
	$labObj = new Lab_Results();

	$Checker = $labObj->CheckIfExist($value);
	if($Checker == $value){
		$objResponse->call("RefreshList", 'already Exists!',$id);
	}else{
		$SaveOk=$labObj->AddFormGroup($value);
		if($SaveOk)
			$objResponse->call("RefreshList", 'Save Successful!',$id);
		else
			$objResponse->call("RefreshList", 'Save not successful!',$id);
	}
	return $objResponse;
}

function DeleteFormGroup($id, $name, $tableId){
	$objResponse = new xajaxResponse();
	$labObj = new Lab_Results();
	$SaveOk = $labObj->DeleteFormGroup($id, $name);
	if($SaveOk)
		$objResponse->call("RefreshList", 'Deleted Successful!',$tableId);
	else
		$objResponse->call("RefreshList", 'Failed to delete',$tableId);

	return $objResponse;
}

function UpdateFormName($id, $name, $tableId){
	$objResponse = new xajaxResponse();
	$labObj = new Lab_Results();
	$Checker = $labObj->CheckIfExist($value);
	
	if($Checker == $name){
		$objResponse->call("RefreshList", 'already Exists!',$tableId);
	}else{
		$SaveOk = $labObj->UpdateFormGroup($id, $name);

		if($SaveOk){
			$objResponse->call("RefreshList", 'Update Successful!',$tableId);
		}else{
			$objResponse->call("RefreshList", 'Failed to Update',$tableId);
		}
	}
	return $objResponse;
}

$xajax->processRequest();
?>
