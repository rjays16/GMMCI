<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/prescription/ajax/seg-soap.common.php');
require_once($root_path.'include/care_api_classes/prescription/class_doctors_soap.php');

function saveSoapNote($type, $note, $pid)
{
	global $db;
	$objResponse = new xajaxResponse();
	$soapObj = new SegDoctorsSoap();

	//prepare data
	$data = array(
		'id' => create_guid(),
		'personell_nr' => $soapObj->getPersonellNr(),
		'pid' => $pid,
		'soap' => strtoupper($type),
		'note' => $note,
		'create_time' => date('Y-m-d H:i:s'),
		'create_id' => $_SESSION['sess_temp_userid']
	);

	$saveok = $soapObj->saveNote($data);
	if($saveok!==FALSE) {
		$objResponse->alert("Note successfully saved!");
		switch($type)
		{
			case 's':
				$objResponse->assign("subjective-list", "innerHTML", "");
				$objResponse->assign("subjective_text", "value", "");
				break;
			case 'o':
				$objResponse->assign("objective-list", "innerHTML", "");
				$objResponse->assign("objective_text", "value", "");
				break;
			case 'a':
				$objResponse->assign("assessment-list", "innerHTML", "");
				$objResponse->assign("assessment_text", "value", "");
				break;
			case 'p':
				$objResponse->assign("plan-list", "innerHTML", "");
				$objResponse->assign("plan_text", "value", "");
				break;
		}
		$objResponse->call("refreshList", $type);
	} else {
		$objResponse->alert("Error:".$soapObj->getErrorMsg()." Last Query:".$soapObj->getLastQuery());
	}

	return $objResponse;
}

function showNotes($type, $pid)
{
	global $db;
	$objResponse = new xajaxResponse();
	$soapObj = new SegDoctorsSoap();

	$objResponse->assign("doctor_nr", "value", $soapObj->getPersonellNr());
	$bullets = array (
		'bullet_black.png',
		'bullet_blue.png',
		'bullet_green.png',
		'bullet_orange.png',
		'bullet_pink.png',
		'bullet_purple.png',
		'bullet_red.png',
		'bullet_star.png',
		'bullet_yellow.png',
		'bullet_white.png'
	);
	$result = $soapObj->listPatientsDoctors($pid);
	if($result!==FALSE) {
		$i=0;
		$doctors = array();
		$objResponse->assign("doctors-list", "innerHTML", "");
		while($row=$result->FetchRow())
		{
			$doctors[] = array(
				'id' => $row["personell_nr"],
				'bullet_color' => $bullets[$i]
			);

			$objResponse->call("listDoctors", $row["personell_nr"],
				strtoupper($row["name_last"]).", ".substr(strtoupper($row["name_first"]),0,1).".",
				$bullets[$i]);
			$i++;
		}
	}

	if(strtolower($type)!="all") {
		$data = $soapObj->getNotes($type, $pid);
		if($data!==FALSE) {
			while($row=$data->FetchRow())
			{
				switch(strtolower($type))
				{
					case 's': $listId = 'subjective-list'; break;
					case 'o': $listId = 'objective-list'; break;
					case 'a': $listId = 'assessment-list'; break;
					case 'p': $listId = 'plan-list'; break;
				}

				//assign bullet color
				foreach($doctors as $i=>$v)
				{
					if($row["personell_nr"]==$v["id"]) {
						$bullet_color = $v["bullet_color"];
					}
				}
				$objResponse->call("showNotes",$listId, date('d-M-Y h:ia', strtotime($row["create_time"])), $row["note"], $row["id"], $row["is_cancelled"], $type, $bullet_color, $row["personell_nr"]);
			}
		} else {
			 $objResponse->alert("Error:".$soapObj->getErrorMsg()." Last Query:".$soapObj->getLastQuery());
		}
	} else {
		$types = array('S','O','A','P');
		foreach($types as $i=>$v)
		{
			$data = $soapObj->getNotes($v, $pid);
			if($data!==FALSE) {
				while($row=$data->FetchRow())
				{
					switch(strtolower($v))
					{
						case 's': $listId = 'subjective-list'; break;
						case 'o': $listId = 'objective-list'; break;
						case 'a': $listId = 'assessment-list'; break;
						case 'p': $listId = 'plan-list'; break;
					}

					//assign bullet color
					foreach($doctors as $a=>$b)
					{
						if($row["personell_nr"]==$b["id"]) {
							$bullet_color = $b["bullet_color"];
						}
					}
					$objResponse->call("showNotes",$listId, date('d-M-Y h:ia', strtotime($row["create_time"])), $row["note"], $row["id"], $row["is_cancelled"], $v, $bullet_color, $row["personell_nr"]);
				}
			} else {
			 $objResponse->alert("Error:".$soapObj->getErrorMsg()." Last Query:".$soapObj->getLastQuery());
			}
		}
	}
	return $objResponse;
}

function deleteSoapNote($id, $type)
{
	global $db;
	$objResponse = new xajaxResponse();
	$soapObj = new SegDoctorsSoap();
	$delok = $soapObj->deleteNote($id);
	if($delok!==FALSE) {
		switch(strtolower($type))
		{
			case 's':
				$objResponse->assign("subjective-list", "innerHTML", "");
				$objResponse->assign("subjective_text", "value", "");
				break;
			case 'o':
				$objResponse->assign("objective-list", "innerHTML", "");
				$objResponse->assign("objective_text", "value", "");
				break;
			case 'a':
				$objResponse->assign("assessment-list", "innerHTML", "");
				$objResponse->assign("assessment_text", "value", "");
				break;
			case 'p':
				$objResponse->assign("plan-list", "innerHTML", "");
				$objResponse->assign("plan_text", "value", "");
				break;
		}
		$objResponse->call("refreshList", $type);
	}
	return $objResponse;
}

function undoDeleteSoapNote($id, $type)
{
	global $db;
	$objResponse = new xajaxResponse();
	$soapObj = new SegDoctorsSoap();
	$delok = $soapObj->undoDeleteNote($id);
	if($delok!==FALSE) {
		switch(strtolower($type))
		{
			case 's':
				$objResponse->assign("subjective-list", "innerHTML", "");
				$objResponse->assign("subjective_text", "value", "");
				break;
			case 'o':
				$objResponse->assign("objective-list", "innerHTML", "");
				$objResponse->assign("objective_text", "value", "");
				break;
			case 'a':
				$objResponse->assign("assessment-list", "innerHTML", "");
				$objResponse->assign("assessment_text", "value", "");
				break;
			case 'p':
				$objResponse->assign("plan-list", "innerHTML", "");
				$objResponse->assign("plan_text", "value", "");
				break;
		}
		$objResponse->call("refreshList", $type);
	}
	return $objResponse;
}

function toggleDoctor($doctor_nr, $pid, $mode)
{
	global $db;
	$objResponse = new xajaxResponse();
	$soapObj = new SegDoctorsSoap();

	$bullets = array (
		'bullet_black.png',
		'bullet_blue.png',
		'bullet_green.png',
		'bullet_orange.png',
		'bullet_pink.png',
		'bullet_purple.png',
		'bullet_red.png',
		'bullet_star.png',
		'bullet_white.png',
		'bullet_yellow.png'
	);
	$result = $soapObj->listPatientsDoctors($pid);
	if($result!==FALSE) {
		$i=0;
		$doctors = array();
		while($row=$result->FetchRow())
		{
			$doctors[] = array(
				'id' => $row["personell_nr"],
				'bullet_color' => $bullets[$i]
			);
			$i++;
		}
	}

	$objResponse->assign("subjective-list", "innerHTML", "");
	$objResponse->assign("objective-list", "innerHTML", "");
	$objResponse->assign("assessment-list", "innerHTML", "");
	$objResponse->assign("plan-list", "innerHTML", "");
	$types = array('S','O','A','P');
	foreach($types as $i=>$v)
	{
		if($mode=='toggle') {
			$data = $soapObj->toggleNotes($v, $doctor_nr, $pid);
		} else if($mode=='untoggle') {
			$data = $soapObj->untoggleNotes($v, $doctor_nr, $pid);
		}

		if($data!==FALSE) {

			while($row=$data->FetchRow())
			{
				switch(strtolower($v))
				{
					case 's': $listId = 'subjective-list'; break;
					case 'o': $listId = 'objective-list'; break;
					case 'a': $listId = 'assessment-list'; break;
					case 'p': $listId = 'plan-list'; break;
				}

				//assign bullet color
				foreach($doctors as $a=>$b)
				{
					if($row["personell_nr"]==$b["id"]) {
						$bullet_color = $b["bullet_color"];
					}
				}
				$objResponse->call("showNotes",$listId, date('d-M-Y h:ia', strtotime($row["create_time"])), $row["note"], $row["id"], $row["is_cancelled"], $v, $bullet_color, $row["personell_nr"]);
			}
		} else {
		 $objResponse->alert("Error:".$soapObj->getErrorMsg()." Last Query:".$soapObj->getLastQuery());
		}
	}

	return $objResponse;
}

$xajax->processRequest();