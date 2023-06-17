<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path."include/care_api_classes/class_order.php");  //load the SegOrder class
require_once($root_path.'modules/or/ajax/op-request-new.common.php'); //load the xajax module
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_oproom.php'); //load the department class
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');


$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room Main :: Approve and Schedule Surgery"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room Main :: Approve and Schedule Surgery");
#$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
$breakfile=$root_path.'modules/or/or_main/or_main_schedule.php'.URL_APPEND;
$smarty->assign('breakfile', $breakfile);
$smarty->assign('check_date_string', $check_date_string);
$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');

$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
													, '<script>var J = jQuery.noConflict();</script>'
													, '<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
													, '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/NumberFormat154.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/op-request-new.js?t='.time().'"></script>'
													, '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />'
													, $xajax->printJavascript($root_path.'classes/xajax-0.2.5')
													);
$smarty->assign('javascript_array', $javascript_array);

$seg_ops = new SegOps();
$seg_department = new Department();
$seg_encounter = new Encounter();
$seg_personell = new Personell();
$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$hospital_number = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$nr = $seg_ops->getOpRequestNrByRefNo($refno);

if (isset($_POST['submitted'])) {

	if (array_key_exists('approve_request', $_POST)) {
		$status = 'approved';
	}
	elseif (array_key_exists('disapprove_request', $_POST)) {
		$status = 'disapproved';
	}

	$data = array(
								'refno' => $refno,
								'or_main_refno' => $_POST['or_main_refno'],
								'op_date' => date('Y-m-d', strtotime($_POST['or_operation_date'])),
								'op_time' => date('H:i:s', strtotime($_POST['or_operation_date'])),
								'surgeon' => $_POST['surgeon'],
								'surgeon_assist' =>  $_POST['surgeon_assist'],
								'anesthesiologist' => $_POST['anesthesiologist'],
								'nurse_scrub' => $_POST['nurse_scrub'],
								'nurse_rotating' => $_POST['nurse_rotating'],
					 );

	 $saveok = $seg_ops->update_or_main_schedule($data);

	 if($saveok){
			$op = $seg_ops->update_or_main_status($_POST['or_main_refno'], $_POST['reason'], $status);
				if ($op == 'insert' && $status == 'approved')
					$smarty->assign('sysInfoMessage','OR Main request was successfully approved.');
				elseif ($op == 'update' && $status == 'approved')
					$smarty->assign('sysInfoMessage', 'OR Main Request approval reason successfully updated.');
				elseif ($op == 'insert' && $status = 'disapproved')
					$smarty->assign('sysInfoMessage', 'OR Main Request was successfully disapproved.');
				elseif ($op == 'update' && $status == 'disapproved')
					$smarty->assign('sysInfoMessage', 'OR Main Request disapproval reason successfully updated.');
	 }

	//if ($seg_ops->update_or_main_schedule($data)) {
		 //$smarty->assign('sysInfoMessage','OR Main request successfully scheduled.');
	//}
}

if ($seg_ops->encOpsNrHasOpsServ($nr)) {
	$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
	$or_main_info = $seg_ops->get_or_main_basic_info($refno);
}

$seg_department = new Department();
$seg_room = new OPRoom();
$dept_nr = $or_main_info['dept_nr'];
$op_nr = $basic_info['op_nr'];
$op_room = $basic_info['op_room'];
$department = $seg_department->FormalName($dept_nr);
$operating_room = $seg_room->get_or_name($op_room);

/** Form tags **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_end', '</form>');

/** End Form tags **/

/** Form elements under fieldset "Request Details" **/
$smarty->assign('required_mark', '<em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em>');
$smarty->assign('error_input', '<span id="error_form_input"></span>');

$smarty->assign('or_request_department', $department);
$smarty->assign('or_op_room', $operating_room['info']);
$transaction = array('1' => 'Cash', '0' => 'Charge');
$smarty->assign('or_transaction_type', $transaction[$basic_info['is_cash']]);
$smarty->assign('or_request_priority', $or_main_info['request_priority']);


$request_date = date('F d, Y h:ia', strtotime($basic_info['request_date'].' '.$basic_info['request_time']));
$smarty->assign('or_request_date', $request_date);
$smarty->assign('or_consent_signed', ($or_main_info['consent_signed'] == 1) ? 'Yes' : 'No');

$case = array('pay_elective'=>'Pay - Elective', 'pay_stat'=>'Pay - Stat',
							'service_elective'=>'Service - Elective', 'service_stat'=>'Service - Stat');
$smarty->assign('or_request_case', $case[$or_main_info['or_case']]);
/** End **/

/** Form elements under fieldset "Patient Information" **/
$seg_person = new Person($basic_info['pid']);
$person_info = $seg_person->getAllInfoArray();
$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
$person_name = $person_info['name_last'] . ', ' . $person_info['name_first'] . ' ' . $middle_initial;
$person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
$person_age = (int)$seg_person->getAge(date('m/d/Y', strtotime($person_info['date_birth'])));
$person_age = is_int($person_age) ? $person_age . ' years old' : '-Not specified-';


$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));
if ($person_info["zipcode"])
	$person_address.=" ".$person_info["zipcode"];
if ($person_info["prov_name"])
	$person_address.=" ".$person_info["prov_name"];

$patient_name = $person_name;
$patient_gender = $person_gender;
$patient_age = $person_age;
$patient_address = $person_address;

$smarty->assign('patient_name', check_value(ucwords(strtolower($patient_name))));
$smarty->assign('patient_gender', check_value($patient_gender));
$smarty->assign('patient_age', $patient_age);
$smarty->assign('patient_address', check_value($patient_address));
$smarty->assign('patient_hospital_number', $hospital_number);
$smarty->assign('package_name', $or_main_info['or_procedure']);

/** End **/

/** Form elements under fieldset "Pre-operation Details" **/

$operation_date = date('F d, Y h:ia', strtotime($or_main_info['date_operation']));
$smarty->assign('or_operation_date', $operation_date);

$operation_date_display = date('F d, Y h:ia', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));
$operation_date = date('Y-m-d H:i', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));

$smarty->assign('or_operation_date_display', '<div id="or_operation_date_display" class="date_display">'.$operation_date_display.'</div>');
$smarty->assign('or_operation_date_value', '<input type="hidden" name="or_operation_date" id="or_operation_date" value="'.$operation_date.'" />');
$smarty->assign('or_operation_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_operation_dt_picker" class="date_time_picker" />');
$smarty->assign('or_operation_calendar_script', setup_calendar('or_operation_date_display', 'or_operation_date', 'or_operation_dt_picker'));


$or_est_op_length = $or_main_info['est_length_op'];
$or_type = $or_main_info['or_type'];
$pre_operative_diagnosis = $or_main_info['pre_op_diagnosis'];
$reason = isset($_POST['reason']) ? $_POST['reason'] : $_GET['reason'];
$operation_procedure = $or_main_info['or_procedure'];
$smarty->assign('or_est_op_length', check_value($or_est_op_length));
$smarty->assign('or_type', $or_type);
#$smarty->assign('pre_operative_diagnosis', check_value($pre_operative_diagnosis));
$smarty->assign('operation_procedure', check_value($operation_procedure));
$classification = array('clean'=>'Clean', 'contaminated'=>'Contaminated',
												'wound_dehiscence'=>'Wound Dehiscence',
												'clean_contaminated'=>'Clean/Contamianted',
												'dirty_infected'=>'Dirty/Infected');
$smarty->assign('or_case_classification', $classification[$or_main_info['case_classification']]);

#Added by Cherry 07-07-10
$info_patient = $seg_encounter->getEncounterInfo($or_main_info['encounter_nr']);
//print_r($info_patient);
$patient_ward = $info_patient['ward_name'];
$patient_diagnosis = $info_patient['er_opd_diagnosis'];
$patient_received_date = date("F j, Y g:i a", strtotime($info_patient['encounter_date']));

$smarty->assign('ward', $patient_ward);
$smarty->assign('or_received_date_time', $patient_received_date);
$smarty->assign('pre_operative_diagnosis', $patient_diagnosis);
$smarty->assign('remarks', $or_main_info['remarks']);

$requesting_doctor = $seg_personell->get_Person_name($or_main_info['dr_nr']);
//print_r($requesting_doctor);
$smarty->assign('or_doctor', $requesting_doctor['dr_name']);

 /** End **/

/** Form elements under fieldset "Other Details" **/
$special_requirements = array('cp_clearance'=>'CP Clearance', 'pulmo_clearance'=>'Pulmo Clearance', 'consent'=>'Consent',
															 'pedia_clearance'=>'Pedia Clearance', 'others'=>'Others');
$smarty->assign('or_special_requirements', $special_requirements);
//$smarty->assign('or_special_requirements_selected', $_POST['or_special_requirements']);
#$try = explode(',', $or_main_info['special_requirements']);
$smarty->assign('or_special_requirements_selected', explode(',', $or_main_info['special_requirements']));
/*$array = explode(',', $or_main_info['special_requirements']);
$temp = array();
foreach ($array as $key => $value) {
		$temp[] = $special_requirements[$value];
}

$smarty->assign('or_special_requirements', implode(', ', $temp));   */
/** End **/

/** Onclick events for each button **/
$add_surgeon_onclick = 'onclick="overlib(
				OLiframeContent(\''.$root_path.'modules/or/request/seg-op-request-select-personnel.php?personnel_type=surgeon&dept_nr='.$dept_nr.'&table_name=surgeon_list\', 500, 300, \'fSelBatchNr\', 1, \'auto\'),
				WIDTH,400, TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4, CAPTION,\'Select surgeon\',MIDX,0, MIDY,0,STATUS,\'Select surgeon\'); return false;"onmouseout="nd();"';
$add_assistant_surgeon_onclick = 'onclick="overlib(
				OLiframeContent(\''.$root_path.'modules/or/request/seg-op-request-select-personnel.php?personnel_type=surgeon_assist&dept_nr='.$dept_nr.'&table_name=assistant_surgeon_list\', 500, 300, \'fSelBatchNr\', 1, \'auto\'),
				WIDTH,400,TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4, CAPTION,\'Select surgeon\',MIDX,0, MIDY,0,STATUS,\'Select surgeon\'); return false;"onmouseout="nd();"';
$add_anesthesiologist_onclick = 'onclick="overlib(
				OLiframeContent(\''.$root_path.'modules/or/request/seg-op-request-select-personnel.php?personnel_type=anesthesiologist&dept_nr='.$dept_nr.'&table_name=anesthesiologist_list\', 500, 300, \'fSelBatchNr\', 1, \'auto\'),
				WIDTH,400,TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,CAPTION,\'Select anesthesiologist\',MIDX,0, MIDY,0,STATUS,\'Select anesthesiologist\'); return false;"
				onmouseout="nd();"';
$add_scrub_nurse_onclick = 'onclick="overlib(
				OLiframeContent(\''.$root_path.'modules/or/request/seg-op-request-select-personnel.php?personnel_type=nurse_scrub&dept_nr='.$dept_nr.'&table_name=scrub_nurse_list\', 500, 300, \'fSelBatchNr\', 1, \'auto\'),
				WIDTH,400,TEXTPADDING,0, BORDER,0,STICKY,SCROLL,CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,CAPTION,\'Select scrub nurse\',MIDX,0, MIDY,0,STATUS,\'Select scrub nurse\'); return false;"
				onmouseout="nd();"';
$add_circulating_nurse_onclick = 'onclick="overlib(
				OLiframeContent(\''.$root_path.'modules/or/request/seg-op-request-select-personnel.php?personnel_type=nurse_rotating&dept_nr='.$dept_nr.'&table_name=circulating_nurse_list\', 500, 300, \'fSelBatchNr\', 1, \'auto\'),
				WIDTH,400,TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,CAPTION,\'Select circulating nurse\',MIDX,0, MIDY,0,STATUS,\'Select circulating nurse\'); return false;"
				onmouseout="nd();"';
/** End **/

/** Buttons for add surgeon, assistant surgeon, scrub nurse, circulating nurse **/

$smarty->assign('add_surgeon', '<a href="javascript:void(0)" id="add_surgeon" '.$add_surgeon_onclick.'></a>');
$smarty->assign('add_assistant_surgeon', '<a href="#" id="add_assistant_surgeon" '.$add_assistant_surgeon_onclick.'></a>');
$smarty->assign('add_scrub_nurse', '<a hre="#" id="add_scrub_nurse" '.$add_scrub_nurse_onclick.'></a>');
$smarty->assign('add_circulating_nurse', '<a href="#" id="add_circulating_nurse" '.$add_circulating_nurse_onclick.'></a>');
$smarty->assign('add_anesthesiologist', '<a href="#" id="add_anesthesiologist" '.$add_anesthesiologist_onclick.'></a>');

/** End **/

/** Other form elements **/

$smarty->assign('submit_schedule', '<input type="submit" id="or_main_schedule_submit" value="" />');
$smarty->assign('cancel_schedule', '<a href="'.$breakfile.'" id="schedule_cancel"></a>');
$smarty->assign('or_approve_submit', '<input name="approve_request" type="submit" value="" id="or_approve_submit" />');
$smarty->assign('or_disapprove_submit', '<input name="disapprove_request" type="submit" value="" id="or_disapprove_submit" />');
$encounter_nr = $basic_info['encounter_nr'];
$hospital_number = $basic_info['pid'];

$smarty->assign('reason', '<textarea name="reason" id="reason">'.$reason.'</textarea>');
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
$smarty->assign('hospital_number', '<input type="text" name="hospital_number" id="hospital_number" value="'.$hospital_number.'" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('or_main_cancel', '<a href="'.$breakfile.'" id="or_main_cancel"></a>');
$smarty->assign('error_msg', '<span id="error_form_input"></span>');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('op_nr', '<input type="hidden" name="op_nr" value="'.$op_nr.'" />');
$smarty->assign('op_room', '<input type="hidden" name="op_room" value="'.$op_room.'" />');
$smarty->assign('dept_nr', '<input type="hidden" name="dept_nr" value="'.$dept_nr.'" />');
$smarty->assign('refno', '<input type="hidden" name="refno" value="'.$refno.'" />');
$smarty->assign('or_request_nr', '<input type="hidden" name="or_request_nr" value="'.$nr.'" />');
$smarty->assign('or_main_refno', '<input type="hidden" name="or_main_refno" value="'.$or_main_info['or_main_refno'].'" />');
/** End **/

$smarty->assign('sMainBlockIncludeFile','or/or_main_schedule_request.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

function setup_calendar($display_area, $input_field, $button) {
	global $root_path;
	$calendar_script =
		'<script type="text/javascript">
			 Calendar.setup ({
				 displayArea : "'.$display_area.'",
				 inputField : "'.$input_field.'",
				 ifFormat : "%Y-%m-%d %H:%M",
				 daFormat : "%B %e, %Y %I:%M%P",
				 showsTime : true,
				 button : "'.$button.'",
				 singleClick : true,
				 step : 1
			 });
			</script>';
	return $calendar_script;
}

function check_value($the_value) {
	$the_value = trim($the_value);
	if ($the_value == '' || strlen($the_value) <= 3) {
		return '-Not Specified-';
	}
	else {
		return $the_value;
	}
}
?>
<script>

function toggle_details() {
	J('#request_details').slideToggle('100');
}

function validate() {
 var errors = new Array();
 var surgeon_id = document.getElementsByName('surgeon[]');
 var anesthesiologist_id = document.getElementsByName('anesthesiologist[]');

 if (surgeon_id.length == 0) {
	 errors.push('Please add a surgeon for this operation.')
 }
 if (anesthesiologist_id.length == 0) {
	 errors.push('Please add an anesthesiologist for this operation');
 }

	if (errors.length > 0) {
		for (var i=0; i<errors.length; i++) {
			alert(errors[i]);
		}
		return false;
	}
	else {
		return true;
	}
}
xajax_populatePersonnel(<?=$refno? $refno:0?>,7,'surgeon_list','surgeon[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,8,'assistant_surgeon_list','surgeon_assist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,12,'anesthesiologist_list','anesthesiologist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,9,'scrub_nurse_list','nurse_scrub[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,10,'circulating_nurse_list','nurse_rotating[]');
</script>
