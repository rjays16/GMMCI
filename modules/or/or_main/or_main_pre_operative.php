<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_oproom.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_vitalsign.php'); //load the vital sign class
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'modules/or/ajax/op-request-new.common.php'); //load the xajax module
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

$breakfile='pre_operation_main.php'.URL_APPEND;

$smarty = new Smarty_Care('or_main_pre_operative');
$smarty->assign('sToolbarTitle',"Operating Room Main :: Schedule Surgery"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room Main :: Schedule Surgery");

$smarty->assign('breakfile', $breakfile);
$smarty->assign('check_date_string', $check_date_string);
$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');

/*
$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
													, '<script>var J = jQuery.noConflict();</script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
													, '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />'
													, $xajax->printJavascript($root_path.'classes/xajax-0.2.5'));   */

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

$vital_sign = new SegVitalsign();
$seg_ops = new SegOps();
$seg_encounter = new Encounter();
$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$nr = $seg_ops->getOpRequestNrByRefNo($refno);
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
#echo "encounter_nr= ".$encounter_nr."<br>";
$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
#echo "pid= ".$pid."<br>";
$or_main_info = $seg_ops->get_or_main_basic_info($refno);
$info = $seg_encounter->getEncounterInfo($encounter_nr);

if ($seg_ops->encOpsNrHasOpsServ($nr)) {
	$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
	#echo "basic info= ";
	#print_r($basic_info);
	$or_main_info = $seg_ops->get_or_main_basic_info($refno);
}

$seg_department = new Department();
$seg_room = new OPRoom();
//$dept_nr = $basic_info['dept_nr'];
$dept_nr = $or_main_info['dept_nr'];
$op_nr = $basic_info['op_nr'];
$op_room = $basic_info['op_room'];
$department = $seg_department->FormalName($dept_nr);
$operating_room = $seg_room->get_or_name($op_room);
#echo "department= ".$department;

/** Form tags **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_end', '</form>');
/** End Form tags **/

/** Collapsible Details **/
$smarty->assign('required_mark', '<em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em>');


$smarty->assign('or_request_department', $department);
$smarty->assign('or_op_room', $operating_room['info']);
$transaction = array('1' => 'Cash', '0' => 'Charge');
//$smarty->assign('or_transaction_type', $transaction[$basic_info['is_cash']]);
$smarty->assign('or_transaction_type', $transaction[$or_main_info['trans_type']]);
$smarty->assign('or_request_priority', $or_main_info['request_priority']);


$request_date = date('F d, Y h:ia', strtotime($basic_info['request_date'].' '.$basic_info['request_time']));
$smarty->assign('or_request_date', $request_date);
$smarty->assign('or_consent_signed', ($or_main_info['consent_signed'] == 1) ? 'Yes' : 'No');

$case = array('pay_elective'=>'Pay - Elective', 'pay_stat'=>'Pay - Stat',
							'service_elective'=>'Service - Elective', 'service_stat'=>'Service - Stat');
$smarty->assign('or_request_case', $case[$or_main_info['or_case']]);



//$seg_person = new Person($basic_info['pid']);
$seg_person = new Person($info['pid']);
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
$smarty->assign('patient_name', $patient_name);
$smarty->assign('patient_gender', $patient_gender);
$smarty->assign('patient_age', $patient_age);
$smarty->assign('patient_address', $patient_address);

$operation_date = date('F d, Y h:ia', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));
$smarty->assign('or_operation_date', $operation_date);


$or_est_op_length = $or_main_info['est_length_op'];
$pre_operative_diagnosis = $or_main_info['pre_op_diagnosis'];
$operation_procedure = $or_main_info['or_procedure'];
$smarty->assign('or_est_op_length', $or_est_op_length);
$smarty->assign('pre_operative_diagnosis', $pre_operative_diagnosis);
$smarty->assign('operation_procedure', $operation_procedure);
$classification = array('clean'=>'Clean', 'contaminated'=>'Contaminated',
												'wound_dehiscence'=>'Wound Dehiscence',
												'clean_contaminated'=>'Clean/Contamianted',
												'dirty_infected'=>'Dirty/Infected');
$smarty->assign('or_case_classification', $classification[$or_main_info['case_classification']]);
$requirements = array('cp_clearance'=>'CP Clearance', 'pulmo_clearance'=>'Pulmo Clearance', 'consent'=>'Consent',
															 'pedia_clearance'=>'Pedia Clearance', 'others'=>'Others');
$or_requirements = explode(",", $or_main_info['special_requirements']);
#$smarty->assign('or_special_requirements', $requirements[$or_main_info['special_requirements']]);
 #foreach($or_main_info['special_requirements'] as $k => $v){

# }

$num_requirements = sizeof($or_requirements);
$count = 1;
foreach ($or_requirements as $k => $v){
		if($num_requirements == 1 || $count == $num_requirements)
				$ans .= $requirements[$v];
		if($num_requirements > 1  && $count < $num_requirements)
				$ans .= $requirements[$v].", ";

		$count++;
}

$smarty->assign('or_special_requirements', $ans);
#Added by Cherry 07-11-10
$smarty->assign('lab_result', '<a href="javascript:void(0)" onclick="openReport(this.id,'.$refno.','.$pid.');" id="lab_result"></a>');

/** End Collapsible Details **/

/** Pre operation checklist fieldset **/


if (isset($_POST['submitted'])) {


//	$save = $seg_ops->update_pre_op_checklist(implode(',', $_POST['question']), $_POST['or_main_refno']);


	$checklist = $seg_ops->update_pre_op_checklist(implode(',', $_POST['question']), $_POST['or_main_refno'],1);
	if(!$checklist){
		$smarty->assign('sysInfoMessage','All mandatory items in the pre-operation checklist must be checked. <br/>No changes were made.');
	}
	else{
		$checklist2 = $seg_ops->update_pre_op_checklist_with_details($_POST['checkbox_ids'], $_POST['detail_ids'], $_POST['or_main_refno'],1);
		if(!$checklist2){
			$smarty->assign('sysInfoMessage','All mandatory items in the pre-operation checklist must be checked. <br/>The checklist details must be filled out.');
		}
	}
	if($checklist && $checklist2){
	$vs = $vital_sign->get_latest_vital_signs($basic_info['pid'], $basic_info['encounter_nr']);

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

	if (($vs['temp']!=$_POST['temperature'] ||
			$vs['pulse_rate']!=$_POST['pulse'] ||
			$vs['resp_rate']!=$_POST['respiratory'] ||
			$vs['systole']!=$_POST['bp_systol'] ||
			$vs['diastole']!=$_POST['bp_diastol'])) {

			$vs_array = array('encounter_nr'=>$encounter_nr,
												'pid'=>$basic_info['pid'],
												'systole'=>$_POST['bp_systol'],
												'diastole'=>$_POST['bp_diastol'],
												'temp'=>$_POST['temperature'],
												'weight'=>0,
												'resp_rate'=>$_POST['respiratory'],
												'pulse_rate'=>$_POST['pulse'],
												'date'=>'NOW()',
												'history'=>"CONCAT('Created ',NOW(),' ','{$_SESSION['sess_user_name']}','\n')",
												'modify_id'=>$_SESSION['sess_user_name'],
												'modify_dt'=>'NOW()',
												'create_id'=>$_SESSION['sess_user_name'],
												'create_dt'=>'NOW()',
												);
		 $save = $vital_sign->add_new_vital_sign($vs_array);

	}else{
		$save = true;
	}

		if ($save && $seg_ops->update_or_main_schedule($data)) {
				 $saveok = $seg_ops->update_or_main_status($or_main_info['or_main_refno'], '', 'pre_op');
				 $smarty->assign('sysInfoMessage','Pre-operation details successfully updated.');
		}
		else {
			$smarty->assign('sysInfoMessage','No changes has been made to the pre-operation details.');
		}
	}
//	$seg_ops->update_or_main_status($or_main_info['or_main_refno'], '', 'pre_op');
//	$saveok = $seg_ops->update_or_main_schedule($data);
//	if ($save || $saveok) {
//		$smarty->assign('sysInfoMessage','Pre-operation checklist successfull updated.');
//	}
//	else {
//		$smarty->assign('sysInfoMessage','No changes has been made to the pre-operation checklist.');
//	}
}

if ($result = $seg_ops->get_pre_op_checklist($or_main_info['or_main_refno'],1)) {

	$smarty->assign('questions', $result['questions']);
	$smarty->assign('questions_selected', $result['selected']);
}

//---------added by celsy 09/09/2010--------//
//for displaying the checklist ietms with details
if ($result = $seg_ops->get_pre_op_checklist_with_details($or_main_info['or_main_refno'],1)) {
	$str='';
	$detail_id = '';
	$checkbox_id = '';
	$questions_with_detail=$result['questions2'];
	$selected_cb=$result['selected2'];
	$labels = $result['labels'];
	$details = $result['details'];

	 foreach ($questions_with_detail as $q_index => $value) {
			$str .= '<input type="checkBox" id="question'.$q_index.'" onClick="toggledetail('.$q_index.');"';

			if(in_array($q_index, $selected_cb)){
				 $checkbox_id .= $q_index.',';
				 $str .= 'checked="checked">'.$value.'</br>';
				 $str .= '<div id ="detail_div'.$q_index.'" style="display:block"><dd>';
			}
			else{
				$str .= '>'.$value.'</br>';
				$str .= '<div id="detail_div'.$q_index.'" style="display:none"><dd>';
			}
			$str .= '<label id="detail_label">'.$labels[$q_index].'</label>:';
			$str .= '<input class="segInput" type="text" id="detail'.$q_index.'" name="detail" value="'.$details[$q_index].'" style="width:187px" onfocus="OLmEdit=1" onblur="OLmEdit=0"/>';
			$str .= '</div>';
	 }
	 $smarty->assign('checkbox_ids', '<input type="hidden" name="checkbox_ids" id="checkbox_ids" value="'.$checkbox_id.'">');
	 $smarty->assign('detail_ids', '<input type="hidden" name="detail_ids" id="detail_ids" value="">');
	 $smarty->assign('checkboxes_with_details', $str);
}//end celsy

#$vs = $vital_sign->get_latest_vital_signs($basic_info['pid'], $basic_info['encounter_nr']);
$vs = $vital_sign->get_latest_vital_signs($basic_info['pid'], $encounter_nr);
$smarty->assign('temperature', '<input type="text" name="temperature" value="'.$vs['temp'].'" />');
$smarty->assign('pulse', '<input type="text" name="pulse" value="'.$vs['pulse_rate'].'" id="pulse" />');
$smarty->assign('respiratory', '<input type="text" name="respiratory" value="'.$vs['resp_rate'].'" />');
$smarty->assign('bp_systol', '<input type="text" name="bp_systol" value="'.$vs['systole'].'" style="width: 72px" />');
$smarty->assign('bp_diastol', '<input type="text" name="bp_diastol" value="'.$vs['diastole'].'" style="width: 73px" />');
$smarty->assign('error_input', '<span id="error_form_input"></span>');

/** End Pre operation checklist fieldset **/

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

/** Click scripts **/
$add_laboratory_onclick = 'onclick="laboratory_request()"';
$add_blood_onclick = 'onclick="blood_request()"';
$add_radiology_onclick = 'onclick="radiology_request()"';
/** End Click scripts **/

/** Other Requests **/
$smarty->assign('add_laboratory', '<a href="javascript:void(0)" id="add_laboratory" '.$add_laboratory_onclick.'></a>');
$smarty->assign('add_blood', '<a href="#" id="add_blood" '.$add_blood_onclick.'></a>');
$smarty->assign('add_radiology', '<a hre="#" id="add_radiology" '.$add_radiology_onclick.'></a>');
/** End Other Requests **/

/** Other form elements **/
$smarty->assign('submit_pre_operation', '<input type="submit" id="pre_op_submit" value="" />');
$smarty->assign('cancel_pre_operation', '<a href="'.$breakfile.'" id="schedule_cancel"></a>');
#$encounter_nr = $basic_info['encounter_nr'];
$pid = $basic_info['pid'];
$encounter_nr = $or_main_info['encounter_nr'];
#$hospital_number = $or_main_info['pid'];

$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
$smarty->assign('pid', '<input type="hidden" name="pid" id="pid" value="'.$pid.'" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('op_nr', '<input type="hidden" name="op_nr" value="'.$op_nr.'" />');
$smarty->assign('op_room', '<input type="hidden" name="op_room" value="'.$op_room.'" />');
$smarty->assign('dept_nr', '<input type="hidden" name="dept_nr" value="'.$dept_nr.'" />');
$smarty->assign('refno', '<input type="hidden" name="refno" value="'.$refno.'" />');
$smarty->assign('or_request_nr', '<input type="hidden" name="or_request_nr" value="'.$nr.'" />');
$smarty->assign('or_main_refno', '<input type="hidden" name="or_main_refno" value="'.$or_main_info['or_main_refno'].'" />');
$smarty->assign('can_be_deleted_image', '<img src="../../../images/close_small.gif" border="0" />');
$smarty->assign('cannot_be_deleted_image', '<img src="../../../images/close_small_disabled.gif" border="0" />');
$smarty->assign('unofficial_result_image', '<img src="../../../images/or_main_images/requests/unofficial_result.png" />');
$smarty->assign('official_result_image', '<img src="../../../images/or_main_images/requests/official_result.png" />');
/** End **/



$smarty->assign('sMainBlockIncludeFile','or/or_main_pre_operative.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

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

//added by Cherry 07-05-10
function openReport(rept_id,refno, pid)
{
	//alert("report: "+rept_id);
	//if(rept_id=='lab_result') window.open('modules/laboratory/seg-lab-result-pdf.php?refno='+refno+'&pid='+pid,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	if(rept_id=='lab_result')
		window.open('<?=$root_path?>modules/laboratory/seg-lab-result-pdf.php?refno='+refno+'&pid='+pid,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	//else if(rept_id=='or_main_surgical_memo_report') window.open('or_surgical_memo_pdf.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	//else if(rept_id=='or_main_anesthesia_report') window.open('or_anesthesia_record_pdf.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	//else if(rept_id=='or_main_op_room_report') window.open('operation_room_record_pdf.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	else  alert("report: "+rept_id+" "+refno);
}
//end Cherry

/**
function laboratory_request(){
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-new.php?<?=URL_APPEND?>&popUp=1&pid=<?=$hospital_number?>&encounter_nr=<?=$encounter_nr?>&dept_nr=<?=$dept_nr?>&area=OR&from_or=1&or_refno=<?=$refno?>',
										850, 450, 'fGroupTray', 0, 'auto'),
										WIDTH,800, TEXTPADDING,0, BORDER,0,
										STICKY, SCROLL, CLOSECLICK, MODAL,
										CLOSETEXT, '<img src=../../../images/close.gif border=0 onClick="close_overlib(1)">',
										CAPTIONPADDING,2, CAPTION,'Laboratory Request',
										MIDX,0, MIDY,0,
										STATUS,'Laboratory Request');
}

function blood_request() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/bloodBank/seg-blood-request-new.php?<?=URL_APPEND?>&popUp=1&area=OR'+'&pid=<?=$hospital_number?>&encounter_nr=<?=$encounter_nr?>&dept_nr<?=$dept_nr?>&from_or=2&or_refno=<?=$refno?>',
										 850, 440, 'fGroupTray', 0, 'auto'),
										 WIDTH,800, TEXTPADDING,0, BORDER,0,
										 STICKY, SCROLL, CLOSECLICK, MODAL,
										 CLOSETEXT, '<img src=../../../images/close.gif border=0 onClick="close_overlib(2)">',
										 CAPTIONPADDING,2, CAPTION,'Blood Bank Request',
										 MIDX,0, MIDY,0,
										 STATUS,'Blood Bank Request');
}

function radiology_request() {
	return overlib(
					OLiframeContent('<?=$root_path?>modules/radiology/seg-radio-request-new.php?<?=URL_APPEND?>&popUp=1&area=OR'+'&pid=<?=$hospital_number?>&encounter_nr=<?=$encounter_nr?>&dept_nr=<?=$dept_nr?>&from_or=3&or_refno=<?=$refno?>',
													 850, 440, 'fGroupTray', 0, 'auto'),
													 WIDTH,800, TEXTPADDING,0, BORDER,0,
													 STICKY, SCROLL, CLOSECLICK, MODAL,
													 CLOSETEXT, '<img src=../../../images/close.gif border=0 onClick="">',
													 CAPTIONPADDING,2, CAPTION,'Radiology Request',
													 MIDX,0, MIDY,0,
													 STATUS,'Radiology Request');
}

function close_overlib(test) {
	if (test==1)
		xajax_laboratory_test(<?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>);
	else if (test==2)
		xajax_blood_test(<?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>);
	else if (test==3)
		xajax_radiology_test(<?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>);
	else
		alert('Invalid request!');
	cClick();
}
**/

//added by celsy 07/08/10
function toggledetail(cbox_id) {
		var cbox = document.getElementById('question'+cbox_id);
		var area = document.getElementById('detail_div'+cbox_id);
		var checkbox_list = document.getElementById('checkbox_ids');
		if(cbox.checked){
			area.style.display = 'block';
			checkbox_list.value = checkbox_list.value.concat(cbox_id+',');
		}
		else{
			area.style.display = 'none';
			checkbox_list.value = checkbox_list.value.replace(cbox_id+',' ,'');
		}
}//end celsy

J("input[@name='pulse']").keydown(function(e){
	return key_check(e, J("input[@name='pulse']").val());
});

J("input[@name='temperature']").keydown(function(e){
	return key_check(e, J("input[@name='temperature']").val());
});

J("input[@name='respiratory']").keydown(function(e){
	return key_check(e, J("input[@name='respiratory']").val());
});

J("input[@name='bp_systol']").keydown(function(e){
	return key_check(e, J("input[@name='bp_systol']").val());
});

J("input[@name='bp_diastol']").keydown(function(e){
	return key_check(e, J("input[@name='bp_systol']").val());
});

function key_check(e, value) {
	 var character = String.fromCharCode(e.keyCode);
	 var number = /^\d+$/;
	 var reg = /^[-+]?[0-9]+((\.)|(\.[0-9]+))?$/;
	 if (character=='¾') {
		 character = '.';
	 }
	 var text_value = value+character;
	 //if ((e.keyCode==190 && text_value.match(reg)!=null) || (e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || (e.keyCode>=36 && e.keyCode<=40))) {
	 if ((e.keyCode==190 && text_value.match(reg)!=null) || (e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || e.keyCode==9 || (e.keyCode==191 || e.keyCode==111) || (e.keyCode>=36 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105))) {
		 return true;
	 }
	 if (character.match(number)==null) {
		 return false;
	 }
}

function validate() {
 var errors = new Array();
 var errors_field = new Array();
 var reg = /^[-+]?[0-9]+(\.[0-9]+)?$/;

	var array_elements = [ {field: J("input[@name='pulse']"),
												 field_value: J("input[@name='pulse']").val(),
												 msg: 'Please enter a valid pulse rate.',
												 msg_dest: J('#error_form_input', J('#pulse_msg')),
												 is_textfield: true
												 },
												 {field: J("input[@name='temperature']"),
												 field_value: J("input[@name='temperature']").val(),
												 msg: 'Please enter a valid temperature.',
												 msg_dest: J('#error_form_input', J('#temp_msg')),
												 is_textfield: true
												 },
												 {field: J("input[@name='respiratory']"),
												 field_value: J("input[@name='respiratory']").val(),
												 msg: 'Please enter a valid respiratory rate.',
												 msg_dest: J('#error_form_input', J('#resp_msg')),
												 is_textfield: true
												 },
												 {field: J("input[@name='bp_systol']"),
												 field_value: J("input[@name='bp_systol']").val(),
												 msg: 'Please enter a valid systole.',
												 msg_dest: J('#error_form_input', J('#bp_systole')),
												 is_textfield: true
												 },
												 {field: J("input[@name='bp_diastol']"),
												 field_value: J("input[@name='bp_diastol']").val(),
												 msg: 'Please enter a valid diastole.',
												 msg_dest: J('#error_form_input', J('#bp_diastole')),
												 is_textfield: true
												 },
												 ];

	for (var i=0; i<array_elements.length; i++) {
		if (array_elements[i].field_value.match(reg)==null) {
			array_elements[i].msg_dest.html(array_elements[i].msg);
			errors_field.push(array_elements[i].field);
			if (array_elements[i].is_textfield) {
				array_elements[i].field.addClass('error_field');
			}
		}
		else {
			array_elements[i].msg_dest.html('');
			array_elements[i].field.removeClass('error_field');
		}
	}
	//added by celsy 07/10/10
 var detail;
 var details_list = document.getElementById('detail_ids');
 var checkbox_list = document.getElementById('checkbox_ids');
 var checked_arr = new Array();
 checked_arr = checkbox_list.value.split(",");
 details_list.value = '';
 for(var i=0; i<checked_arr.length-1; i++){
	 detail = document.getElementById('detail'+checked_arr[i]);
	 if(detail.value.replace(/\s+/g,'') == '') {     //check if textfield is just whitespace
		errors.push('Fill out checkbox details');
		break;
	 }
	 else{
		details_list.value = details_list.value.concat(detail.value+',');
	 }
 }
	if (errors.length > 0) {
				for (var i=0; i<errors.length; i++) {
						alert(errors[i]);
						if(errors[i]=='Fill out checkbox details') {
							detail.focus();
						}
				}
				return false;
	}
 //end celsy

	if (errors_field.length > 0) {
		errors_field[0].focus();
		return false;
	}

	else {
		return true;
	}

}
/**
function delete_blood_request(refno){
	var answer = confirm("Are you sure you want to delete the blood request with a reference no. "+(refno)+"?");
		if (answer){
			 xajax_delete_blood_request(refno, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>);
		}
}

function delete_radiology_request(refno){
	var answer = confirm("Are you sure you want to delete the radio request with a reference no. "+(refno)+"?");
		if (answer){
			 xajax_delete_radiology_request(refno, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>);
		}
}

function delete_laboratory_request(refno){
	var answer = confirm("Are you sure you want to delete the laboratory request with a reference no. "+(refno)+"?");
		if (answer){
			 xajax_delete_laboratory_request(refno, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>);
		}
}

function delete_laboratory_service_code(refno, service_code) {
	var tbody = document.getElementById(refno);
	var row_length = tbody.rows.length;

	var answer = confirm("Are you sure you want to delete this laboratory service item, "+(service_code)+"?");
		if (answer){
			 if (row_length > 1) {
				 xajax_delete_laboratory_service_code(refno, service_code, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>, 0);
			 }
			 else {
				 var answer = confirm("Are you sure you really want to do this? \n Take note that this is the last item under this request. \n Removing this item will also remove the laboratory request. \n Do you still want to continue?");
				 if (answer)
					 xajax_delete_laboratory_service_code(refno, service_code, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>, 1);
			 }
		}
}

function delete_blood_service_code(refno, service_code) {
	var tbody = document.getElementById(refno);
	var row_length = tbody.rows.length;

	var answer = confirm('Are you sure you want to delete this blood service item, '+(service_code)+'?');
	if (answer) {
		if (row_length > 1) {
			xajax_delete_blood_service_code(refno, service_code, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>, 0);
		}
		else {
			var answer = confirm("Are you sure you really want to do this? \n Take note that this is the last item under this request. \n Removing this item will also remove the blood request. \n Do you still want to continue?");
			if (answer)
				xajax_delete_blood_service_code(refno, service_code, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>, 1);
		}
	}
}

function delete_radiology_service_code(refno, service_code) {
	var tbody = document.getElementById(refno);
	var row_length = tbody.rows.length;

	var answer = confirm('Are you sure you want to delete this radio service item, '+(service_code)+'?');
	if (answer) {
		if (row_length > 1) {
			xajax_delete_radiology_service_code(refno, service_code, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>, 0);
		}
		else {
			var answer = confirm("Are you sure you really want to do this? \n Take note that this is the last item under this request. \n Removing this item will also remove the radio request. \n Do you still want to continue?");
			if (answer)
				xajax_delete_radiology_service_code(refno, service_code, <?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>, 1);
		}
	}
}

function view_laboratory_result(refno, pid, service_code, done) {
	window.open('<?=$root_path?>modules/repgen/pdf_lab_results.php?pid='+pid+'&refno='+refno+'&service_code='+service_code+'&done='+done, 'Rep_Gen', "width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

xajax_laboratory_test(<?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>);
xajax_blood_test(<?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>);
xajax_radiology_test(<?=$encounter_nr?>, <?=$hospital_number?>, <?=$refno?>); **/
xajax_populatePersonnel(<?=$refno? $refno:0?>,7,'surgeon_list','surgeon[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,8,'assistant_surgeon_list','surgeon_assist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,12,'anesthesiologist_list','anesthesiologist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,9,'scrub_nurse_list','nurse_scrub[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,10,'circulating_nurse_list','nurse_rotating[]');
</script>