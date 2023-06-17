<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/nursing/ajax/seg-attending-doctors-common.php');
												
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/inc_date_format_functions.php');

//populate the list of attending physician
function PopulateRow($enc=''){
	global $db, $date_format;
	$objResponse = new xajaxResponse();
	
	$sql ="SELECT sm.att_hist_no, sm.encounter_nr,cl.job_function_title, cl.nr, cp.name_first, cp.name_last, cp.name_middle, sm.attend_start ".
			"\n	FROM seg_encounter_dr_mgt AS sm " .
			"\n	INNER JOIN care_personell AS cl on sm.attending_dr_nr = cl.nr ".
			"\n	INNER JOIN care_person AS cp on cl.pid = cp.pid ".
			"\n WHERE sm.encounter_nr = '".$enc."' ORDER BY sm.att_hist_no DESC";
		
	if($result = $db->Execute($sql)){
		if($result->RecordCount()){
			$objResponse->call("js_clearRow", "doc-list");
			while($row = $result->FetchRow()){
				#$objResponse->alert("result =". $row['name_first']);
				$docname = ucfirst($row['name_last']).", ".ucfirst($row['name_first'])." ".$row['name_middle'] ;
				$dateStart = @formatDate2Local($row['attend_start'],$date_format);
								
				$objResponse->call("js_addRow","doc-list", $row['nr'], $docname, $dateStart, $row['encounter_nr']);									
			}
		}else{
			//$objResponse->alert("No record exists");
		}
	}else{ 
		$objResponse->alert("ErrorMsg: ".$sql);
	}
	
	return $objResponse;
}// end of function PopulateRow

function addAttendingDoctors($enc , $doc_nr ='', $dt_start,$create_id){
	global $db, $date_format;
	$objResponse = new xajaxResponse();
	
	$create_dt = date('Y-m-d H:i:s');
	$dtStart =  date('Y-m-d H:m:s',$dt_start);			
	$dtStart =  @formatDate2STD($dt_start, $date_format); //." ".date('H:m:s');
	
	#$objResponse->alert("dt start = ". $dtStart);
	
	$sql = "INSERT INTO seg_encounter_dr_mgt (encounter_nr, attending_dr_nr, attend_start, create_id, create_dt) ".
		   "\n VALUES ('".$enc."', '".$doc_nr."','".$dtStart."' , '".$create_id."', '".$create_dt."')";	
  	
	if($db->Execute($sql)){
		//if($db->Affected_Rows()){
			$objResponse->alert("Successfully save.");	
			$objResponse->call("xajax_PopulateRow", $enc);
		//}			
	}else{
		$objResponse->alert("Please enter later date..");
		//$objResponse->alert("ErrorMsg: ".$db->ErrorMsg." - ". $sql);
	}
	
	return $objResponse;
}// end of function addAttendingDoctors

function delAttendingDoctors($enc, $attending_dr_nr, $rowno){
	global $db;
	$objResponse = new xajaxResponse();
	
	$sql = "DELETE FROM seg_encounter_dr_mgt WHERE encounter_nr ='".$enc."' AND attending_dr_nr = '".$attending_dr_nr."'";
	
	if($db->Execute($sql)){
		$objResponse->call("removeRow",$rowno);
		//$objResponse->alert("You have permanently deleted the attending physician.");
	}else{
		$objResponse->alert("ErrorMsg: ". $db->ErrorMsg."-". $sql);
	}
	
	return $objResponse;
}

//ok
function setDoctors($admit_inpatient=0, $dept_nr=0, $personell_nr=0) {
	//global $pers_obj;
	$pers_obj=new Personell;
	$objResponse = new xajaxResponse();
	#$objResponse->addAlert("dept : $dept_nr");
	if ($dept_nr)
		$rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
	else
		$rs=$pers_obj->getDoctors($admit_inpatient);	
	
	if ($rs) {
		$objResponse->addScriptCall("ajxClearOptions",0);
		if ($pers_obj->count > 0){
			$objResponse->addScriptCall("ajxAddOption",0,"-Select a Doctor-",0);
		}else{
			if ($dept_nr)
				$objResponse->addScriptCall("ajxAddOption",0,"-No Doctor Available-",0);
			else	
				$objResponse->addScriptCall("ajxAddOption",0,"-Select a Doctor-",0);
		}	
		
		while ($result=$rs->FetchRow()) {
			$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$result["name_last"];
			$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			$objResponse->addScriptCall("ajxAddOption",0,$doctor_name,$result["personell_nr"]);
		}
	}
	else {
		$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
	}
	return $objResponse;
}

//ok
function setDepartments($personell_nr=0, $encnr) {
	$dept_obj=new Department;
	$objResponse = new xajaxResponse();
	
	#$objResponse->addAlert("setDepartments : personell_nr ='$personell_nr'");
  if ($personell_nr!=0){
		$result=$dept_obj->getDeptofDoctor($personell_nr);
		#$objResponse->addAlert("setDepartments : dept_obj->sql = '$dept_obj->sql'");
		#$objResponse->addAlert("name_formal = ".$result["name_formal"]." - ".$result["nr"]);
		if ($result) {
			$objResponse->addScriptCall("ajxSetDepartment",$result["nr"]);
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor",$personell_nr);
		}
		
		#else{
		#	$objResponse->addAlert("setDepartments : Error retrieving Department information...");
		#}
	}else{
		#$objResponse->addAlert("sulod");
		$objResponse->addScriptCall("ajxSetDepartment",$encnr);
	}	
	return $objResponse;
}

//ok
function setALLDepartment($admit_inpatient){
//global $dept_obj;
	$dept_obj = new Department;
	$objResponse = new xajaxResponse();
	#$objResponse->addAlert("setALLDepartment");
	$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);
	
	#$objResponse->alert("rs = ".print_r($rs, true));
	
	if ($rs) {
		$objResponse->addScriptCall("ajxClearOptions",1); // set to department status .. 
		if ($dept_obj->count > 0){
			$objResponse->addScriptCall("ajxAddOption",1,"-Select a Department-",0);
		}else{
			$objResponse->addScriptCall("ajxAddOption",1,"-Select a Department-",0);
		}
		while ($result=$rs->FetchRow()) {
		   $objResponse->addScriptCall("ajxAddOption",1,$result["name_formal"],$result["nr"]);
		}
	}
	else {
		$objResponse->addAlert("setALLDepartment : Error retrieving Department information...");
	}
	return $objResponse;
}


$xajax->processRequest();
?>