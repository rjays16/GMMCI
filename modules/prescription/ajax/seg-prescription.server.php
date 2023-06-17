<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/prescription/ajax/seg-prescription.common.php');
require_once($root_path.'include/care_api_classes/prescription/class_prescription_writer.php');

function savePrescription($details)
{
	global $db;
	$objResponse = new xajaxResponse();
	$pres_obj = new SegPrescription();
	$db->StartTrans();

	//prepare prescription data header
	$data_header = array(
		'encounter_nr' => $details['encounter_nr'],
		'prescription_date' => date('Y-m-d H:i:s', strtotime($details['prescription_date'])),
		'instructions' => $details['instructions'],
        'clinical_impression' => $details['clinical_impression'],
		'history' => 'Create '.date('Y-m-d H:i:s').'['.$_SESSION['sess_temp_userid'].']',
		'create_id' => $_SESSION['sess_temp_userid'],
		'create_time' => date('Y-m-d H:i:s'),
		'modify_id' => $_SESSION['sess_temp_userid'],
		'modify_time' => date('Y-m-d H:i:s')
	);
	$prescription_id = $pres_obj->savePrescription($data_header);
	if($prescription_id!==FALSE) {
		$bulk = array();
		foreach (($details['name']) as $i=>$v) {
			$bulk[] = array(
				'item_code'=>trim($details['code'][$i]),
				'item_name'=>trim($details['name'][$i]),
				'quantity'=>trim($details['qty'][$i]),
				'dosage'=>trim($details['dosage'][$i]),
				'period_count'=>trim($details['pcount'][$i]),
				'period_interval'=>trim($details['pinterval'][$i])
			);
		}
		//$objResponse->alert(print_r($bulk,true));
		//$pres_obj->clearItems($id);
		if(count($details['name'])>0) {
			$saveok=$pres_obj->savePrescriptionItems($prescription_id, $bulk);
		}
	}

	if($saveok!==FALSE) {
		//saving for templates, if flagged as is_save to templates
		/*$bulk = array();
		foreach (($details['name']) as $i=>$v) {
			if($details['code'][$i]!="") {
				 $bulk[] = array(
					'item_code'=>trim($details['code'][$i]),
					'item_name'=>trim($details['name'][$i]),
					'quantity'=>trim($details['qty'][$i]),
					'dosage'=>trim($details['dosage'][$i]),
					'period_count'=>trim($details['pcount'][$i]),
					'period_interval'=>trim($details['pinterval'][$i])
				);
			}
		}*/
		//$objResponse->alert(print_r($bulk,true));
		if($details['is_save']==1 && count($bulk)>0) {
			//prepare template data header
			$data_header = array(
				'name' => $details['template_name'],
				'owner' => $_SESSION['sess_temp_userid'],
				'history' => 'Create '.date('Y-m-d H:i:s').'['.$_SESSION['sess_temp_userid'].']',
				'create_id' => $_SESSION['sess_temp_userid'],
				'create_time' => date('Y-m-d H:i:s'),
				'modify_id' => $_SESSION['sess_temp_userid'],
				'modify_time' => date('Y-m-d H:i:s')
			);
			$template_id = $pres_obj->saveTemplate($data_header);
			if($template_id!==FALSE) {
				//$pres_obj->clearItems($id);
				//if(count($bulk['item_name'])>0) {
					$saveok=$pres_obj->saveTemplateItems($template_id, $bulk);
				//}
			}

			if($saveok!==FALSE) {
				$db->CompleteTrans();
				$objResponse->call("disableControls");
				$objResponse->alert("Prescription successfully saved and new template was created.");
				$objResponse->assign("prescription_id", "value", $prescription_id);
				$objResponse->assign("print_prescription", "style.display", "");
				$objResponse->assign("print_prescription", "disabled", false);
			}else {
				$db->FailTrans();
				$db->CompleteTrans();
				$objResponse->alert("Prescription and template not successfully saved!");
				$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
			}

		}else {
			$db->CompleteTrans();
			$objResponse->call("disableControls");
			$objResponse->assign("prescription_id", "value", $prescription_id);
			$objResponse->alert("Prescription successfully saved!");
			$objResponse->assign("print_prescription", "style.display", "");
			$objResponse->assign("print_prescription", "disabled", false);
		}
	} else {
		$db->FailTrans();
		$db->CompleteTrans();
		$objResponse->alert("Prescription not successfully saved!");
		$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
	}

	return $objResponse;
}

function saveTemplate($details)
{
	global $db;
	$objResponse = new xajaxResponse();
	$pres_obj = new SegPrescription();

	//prepare header data
	$data_header = array(
		'name' => $details['template_name'],
		'owner' => $details['template_owner'],
		'history' => 'Create '.date('Y-m-d H:i:s').'['.$details['template_owner'].']',
		'create_id' => $_SESSION['sess_temp_userid'],
		'create_time' => date('Y-m-d H:i:s'),
		'modify_id' => $_SESSION['sess_temp_userid'],
		'modify_time' => date('Y-m-d H:i:s')
	);
	$id = $pres_obj->saveTemplate($data_header);
	if($id!==FALSE) {
		$bulk = array();
		foreach (($details['name']) as $i=>$v) {
			if($details['code'][$i]!="") {
				 $bulk[] = array(
					'item_code'=>trim($details['code'][$i]),
					'item_name'=>trim($details['name'][$i]),
					'quantity'=>trim($details['qty'][$i]),
					'dosage'=>trim($details['dosage'][$i]),
					'period_count'=>trim($details['pcount'][$i]),
					'period_interval'=>trim($details['pinterval'][$i])
				);
			}
		}
		//$pres_obj->clearItems($id);
		$saveok=$pres_obj->saveTemplateItems($id, $bulk);

		if($saveok!==FALSE) {
				$db->CompleteTrans();
				$objResponse->alert("New prescription template successfully created.");
				$objResponse->call("closeTemplate");
		}else {
				$db->FailTrans();
				$db->CompleteTrans();
				$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
		}
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
	}

	return $objResponse;
}

function deleteTemplate($id)
{
	global $db;
	$objResponse = new xajaxResponse();
	$pres_obj = new SegPrescription();
	$deleteok = $pres_obj->deleteTemplate($id);
	if($deleteok!==FALSE) {
//		$objResponse->call("outputResponse","Standard prescription successfully deleted.");
        $objResponse->alert("Standard prescription successfully deleted.");
        $objResponse->call('searchTemplate');
	}else {
		$objResponse->call("outputResponse","ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
	}
	return $objResponse;
}

function showEditTemplate($id, $name)
{
	global $db;
	$objResponse = new xajaxResponse();
	$pres_obj = new SegPrescription();
	$data = $pres_obj->listTemplates($name, 'name ASC', 0, 1);
	if($data!==FALSE) {
		$header_data = $data->FetchRow();
		$items_data = $pres_obj->getTemplateItems($id);
		if($items_data!==FALSE) {
			$objResponse->assign("template_name", "value", ucfirst($header_data["name"]));
			$objResponse->assign("owner", "value", ucfirst($header_data["owner_name"]));
			$objResponse->assign("template_owner", "value", $header_data["owner"]);
			$objResponse->call("clearList", "prescriptionlist");
			while($row=$items_data->FetchRow())
			{
				$objResponse->call("addDrug", $row['item_code'], $row['item_name'], number_format($row['quantity'],0), $row['dosage'],
					$row['period_count'], $row['period_interval'], $row['generic']);
			}
			$objResponse->assign("modeval", "value", "update");
			$objResponse->assign("template_id", "value", $id);
		}else {
			$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
		}
	} else {
		$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
	}

	return $objResponse;
}

function updateTemplate($id, $details)
{
	global $db;
	$objResponse = new xajaxResponse();
	$pres_obj = new SegPrescription();
	$db->StartTrans();

	$updateok = $pres_obj->updateTemplate($id, $details['template_name']);
	if($updateok!==FALSE) {
		$clearok = $pres_obj->clearTemplateItems($id);
		if($clearok!==FALSE) {
				$bulk = array();
				foreach (($details['name']) as $i=>$v) {
					if($details['code'][$i]!="") {
						 $bulk[] = array(
							'item_code'=>trim($details['code'][$i]),
							'item_name'=>trim($details['name'][$i]),
							'quantity'=>trim($details['qty'][$i]),
							'dosage'=>trim($details['dosage'][$i]),
							'period_count'=>trim($details['pcount'][$i]),
							'period_interval'=>trim($details['pinterval'][$i])
						);
					}
				}
				$saveok=$pres_obj->saveTemplateItems($id, $bulk);
				if($saveok!==FALSE) {
						$db->CompleteTrans();
						$objResponse->alert("Prescription template successfully updated.");
						$objResponse->call("closeTemplate");
				}else {
						$db->FailTrans();
						$db->CompleteTrans();
						$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
				}
		}else {
				$db->FailTrans();
				$db->CompleteTrans();
				$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
		}
	}else {
			$db->FailTrans();
			$db->CompleteTrans();
			$objResponse->alert("ERROR:".$pres_obj->getErrorMsg()."\n SQL:".$pres_obj->getLastQuery());
	}

	return $objResponse;
}

$xajax->processRequest();
?>
