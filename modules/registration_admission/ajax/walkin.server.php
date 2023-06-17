<?php
//created by cha, 12-14-2010
	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_core.php');
	require_once($root_path.'include/care_api_classes/class_walkin.php');
	require_once($root_path.'modules/registration_admission/ajax/walkin.common.php');

	function registerWalkin($data)
	{
		global $db;
		$objResponse = new xajaxResponse();
		$core = new Core();
		$walkin = new SegWalkin();

		$db->StartTrans();
		$hrn = $walkin->createPID();
		$values = array(
			'pid' => $hrn,
			'name_last' => $data['last_name'],
			'name_first' => $data['first_name'],
			'name_middle' => $data['middle_name'],
			'date_birth' => $data['birthdate'],
			'address' => $data['address'],
			'sex' => $data['gender']
		);
		$core->setTable('seg_walkin', TRUE);
		$saveok = $core->save($values);

		if($saveok!==FALSE) {
			$db->CompleteTrans();
			$objResponse->alert('Walkin registered successfully...');
			$objResponse->call('assignWalkin', 'W'.$hrn, $data['last_name'].", ".$data['first_name']." ".$data['middle_name']);
			$objResponse->call('parent.cClick');
		} else {
			$db->FailTrans();
			$db->CompleteTrans();
			$objResponse->alert('Error saving walkin data! Please contact your Administrator!');
			$objResponse->call('doneLoading');
		}
		return $objResponse;
	}

	function checkExistingWalkin($data)
	{
		global $db;
		$objResponse = new xajaxResponse();

		$sql = "SELECT pid FROM seg_walkin WHERE name_last=".$db->qstr($data['last_name'])."\n".
								"AND name_first=".$db->qstr($data['first_name'])." AND name_middle=".$db->qstr($data['middle_name'])." \n".
								"AND date_birth=".$db->qstr($data['birthdate']);
		$exists = $db->GetOne($sql);
		if($exists!==FALSE) {
			$objResponse->alert("Name already exists!");
		} else {
			$objResponse->call("registerWalkin");
		}

		return $objResponse;
	}

	function showWalkinDetails($id)
	{
		global $db;
		$objResponse = new xajaxResponse();
		$walkin = new SegWalkin();

		$filters['ID'] = $id;
		$result =$walkin->searchWalkin($filters);
		if($result!==FALSE) {
			$data = $result->FetchRow();
			$objResponse->assign("last_name", "value", $data['name_last']);
			$objResponse->assign("first_name", "value", $data['name_first']);
			$objResponse->assign("middle_name", "value", $data['name_middle']);
			$objResponse->assign("address", "value", $data['address']);
			$objResponse->assign("birthdate", "value", $data['date_birth']);
			$objResponse->assign("gender_m", "checked", "checked");
			if(strtolower($data['sex'])=="female")
				$objResponse->assign("gender_f", "checked", "checked");
			$objResponse->assign("walkin_id", "value", $id);
			$objResponse->assign("updateBtn", "style.display", "");
			$objResponse->assign("saveBtn", "style.display", "none");
		}
		return $objResponse;
	}

	function updateWalkin($data)
	{
		global $db;
		$objResponse = new xajaxResponse();
		$walkin = new SegWalkin();

		$values = array(
			'id' => $data['walkin_id'],
			'lastname' => $data['last_name'],
			'firstname' => $data['first_name'],
			'middlename' => $data['middle_name'],
			'birthdate' => $data['birthdate'],
			'address' => $data['address'],
			'gender' => $data['gender']
		);
		$result = $walkin->updateWalkinDetails($values);
		if($result!==FALSE) {
			$objResponse->alert("Update walkin details successful!");
			$objResponse->call("window.parent.cClick");
		}
		return $objResponse;
	}

	function deleteWalkin($pid)
	{
		 global $db;
		$objResponse = new xajaxResponse();
		$walkin = new SegWalkin();
		$result = $walkin->deleteWalkinData($pid);
		if($result!==FALSE) {
			$objResponse->alert("Walkin successfully deleted!");
			$objResponse->call("refreshPage");
		}
		return $objResponse;
	}

$xajax->processRequest();