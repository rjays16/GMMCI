<?php
	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_core.php');
	require_once($root_path.'include/care_api_classes/sponsor/class_cmap_walkin.php');
	require_once($root_path.'modules/sponsor/ajax/cmap_walkin.common.php');

	function registerWalkin($data)
	{
		global $db;
		$objResponse = new xajaxResponse();
		$core = new Core();
		$cmapObj = new CmapWalkin();

		$db->StartTrans();
		$hrn = $cmapObj->createId();
		$values = array(
			'id' => $hrn,
			'lastname' => $data['last_name'],
			'firstname' => $data['first_name'],
			'middlename' => $data['middle_name'],
			'birthdate' => $data['birthdate'],
			'address' => $data['address'],
			'gender' => $data['gender']
		);
		$core->setTable('seg_cmap_walkin', TRUE);
		$saveok = $core->save($values);

		if($saveok!==FALSE) {
			$db->CompleteTrans();
			$objResponse->alert('CMAP walkin registered successfully...');
			$objResponse->call('assignWalkin', $hrn, $data['last_name'].", ".$data['first_name']." ".$data['middle_name']);
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

		$sql = "SELECT id FROM seg_cmap_walkin WHERE lastname=".$db->qstr($data['last_name'])."\n".
								"AND firstname=".$db->qstr($data['first_name'])." AND middlename=".$db->qstr($data['middle_name'])." \n".
								"AND birthdate=".$db->qstr($data['birthdate']);
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
		$cmap_obj = new CmapWalkin();

		$filters['ID'] = $id;
		$result =$cmap_obj->searchWalkin($filters);
		if($result!==FALSE) {
			$data = $result->FetchRow();
			$objResponse->assign("last_name", "value", $data['lastname']);
			$objResponse->assign("first_name", "value", $data['firstname']);
			$objResponse->assign("middle_name", "value", $data['middlename']);
			$objResponse->assign("address", "value", $data['address']);
			$objResponse->assign("birthdate", "value", $data['birthdate']);
			$objResponse->assign("gender_m", "checked", "checked");
			if(strtolower($data['gender'])=="female")
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
		$cmap_obj = new CmapWalkin();

		$values = array(
			'id' => $data['walkin_id'],
			'lastname' => $data['last_name'],
			'firstname' => $data['first_name'],
			'middlename' => $data['middle_name'],
			'birthdate' => $data['birthdate'],
			'address' => $data['address'],
			'gender' => $data['gender']
		);
		$result = $cmap_obj->updateWalkinDetails($values);
		if($result!==FALSE) {
			$objResponse->alert("Update walkin details successful!");
			$objResponse->call("window.parent.cClick");
		}
		return $objResponse;
	}

$xajax->processRequest();