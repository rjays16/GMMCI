<?php
ob_start();
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path.'include/care_api_classes/class_order.php');  //load the SegOrder class
require_once($root_path.'modules/or/ajax/op-request-new.common.php'); //load the xajax module
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_oproom.php'); //load the department class
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions
require_once($root_path.'include/care_api_classes/class_person.php'); //load the person class
require_once($root_path.'include/care_api_classes/class_social_service.php'); //load the segops class
require_once($root_path.'include/care_api_classes/class_equipment_order.php'); //load the segops class
require_once($root_path.'include/care_api_classes/class_ward.php'); //load the ward class
require_once($root_path.'include/care_api_classes/class_vitalsign.php'); //load the vital sign class

global $db;  //see *manolo

$breakfile='or_asu_post_list.php'.URL_APPEND;

$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room ASU :: Post Operative Details"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room ASU :: Post Operative Details");

$smarty->assign('breakfile', $breakfile);
$smarty->assign('check_date_string', $check_date_string);
$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');

#added by cha, august 21, 2010
require_once($root_path.'include/care_api_classes/class_request_source.php');
$ph_src_obj = new SegRequestSource();
$request_source = $ph_src_obj->getSourceOR();
$smarty->assign('pharma_req_src', '<input type="hidden" id="request_source" name="request_source" value="'.$request_source.'"/>');
#end cha

/*
$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
													, '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
													, '<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />'
													, '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_charge.css" type="text/css" />'
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
													, '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqDnR.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/dimensions.js"></script>'
													, '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/order-gui.js"></script>'
							, '<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.blockUI.js"></script>'
													, '<script>J.blockUI({ message: "<h1>Just a moment...</h1>" });</script>'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/order_equipment.js"></script>'
													, $xajax->printJavascript($root_path.'classes/xajax-0.2.5')
													);  */

$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
													, '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
													, '<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />'
													, '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_charge.css" type="text/css" />'
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
													, '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqDnR.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/dimensions.js"></script>'
													, '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/order-gui.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.blockUI.js"></script>'
													, '<script type="text/javascript" src="'.$root_path.'modules/or/js/order_equipment.js"></script>'
													, $xajax->printJavascript($root_path.'classes/xajax-0.2.5')
													);
$smarty->assign('javascript_array', $javascript_array);



$seg_ops = new SegOps();

$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$nr = $seg_ops->getOpRequestNrByRefNo($refno);
#echo "nr= ".$nr;
$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
//Added by Cherry 06-22-10
$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
//echo "post..".$_POST['encounter_nr']."</p>";
//echo "get..".$_GET['encounter_nr']."</p>";
//#$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
#echo "enc_nr=".$encounter_nr;
$or_main_info = $seg_ops->get_or_main_basic_info($refno);
//end Cherry
if ($seg_ops->encOpsNrHasOpsServ($nr)) {
	$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);

}

$seg_department = new Department();
$seg_room = new OPRoom();
$dept_nr = $basic_info['dept_nr'];
$op_nr = $basic_info['op_nr'];
$op_room = $basic_info['op_room'];
$department = $seg_department->FormalName($dept_nr);
$operating_room = $seg_room->get_or_name($op_room);


/** Form tags **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_end', '</form>');
/** End Form tags **/

/** Form elements under fieldset "Pre-operative Details" **/
$pre_op_checklist = $seg_ops->get_pre_op_checklist($or_main_info['or_main_refno']);
$pre_op_questions = $pre_op_checklist['questions'];
$pre_op_selected = array_flip($pre_op_checklist['selected']);

$pre_op_table = '';
foreach ($pre_op_questions as $key => $value) {
	$pre_op_table .= '<tr><td>'.$value.'</td><td>:</td>';
	if (is_int($pre_op_selected[$key])) {
		$pre_op_table .= '<td><span style="color:#009900;font-weight:bold">YES</span></td>';
	}
	else {
		$pre_op_table .= '<td><span style="color:#D50A1F;font-weight:bold">NO</span></td>';
	}
	$pre_op_table .= '</tr>';
}

$vital_sign = new SegVitalsign();
//echo "encounter_nr= ".$encounter_nr."<br>";
//echo "pid= ".$pid."<br>";
$vs = $vital_sign->get_all_vital_signs($pid, $encounter_nr); //Added by Cherry 06-22-10

//$vs = $vital_sign->get_all_vital_signs($basic_info['pid'], $basic_info['encounter_nr']);
$vs_table = '';
foreach ($vs as $key => $value) {
	$vs_table .= '<tr>
									<td>'.$value['date_taken'].'</td>
									<td>'.$value['temperature'].'</td>
									<td>'.$value['pulse_rate'].'</td>
									<td>'.$value['respiratory_rate'].'</td>
									<td>'.$value['blood_pressure'].'</td>
							 </tr>';
}

$smarty->assign('pre_op_table', $pre_op_table);
$smarty->assign('vital_signs_table', $vs_table);
/** End: Form elements under fieldset "Pre-operative Details" **/

/** Form elements under fieldset "Request Details" **/
$smarty->assign('required_mark', '<em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em>');
$smarty->assign('error_input', '<span id="error_form_input"></span>');
//added by cha 10-22-09
global $db;
$sql = "select os.encounter_nr from seg_ops_serv as os join seg_or_main as om on os.refno=om.ceo_refno".
		" and om.ceo_refno=".$db->qstr($_GET['refno']);
#echo $sql;
$result=$db->Execute($sql);
$row=$result->FetchRow();
#$encounter_nr = $row['encounter_nr'];
$smarty->assign('or_request_encounter_no',$encounter_nr);
//end cha
$smarty->assign('or_request_department', $department);
$smarty->assign('or_op_room', $operating_room['info']);
$transaction = array('1' => 'Cash', '0' => 'Charge');
#$smarty->assign('or_transaction_type', $transaction[$basic_info['is_cash']]);
$smarty->assign('or_transaction_type', $transaction[$or_main_info['trans_type']]);  //Added by Cherry 06-22-10
$smarty->assign('or_request_priority', $or_main_info['request_priority']);


#$request_date = date('F d, Y h:ia', strtotime($basic_info['request_date'].' '.$basic_info['request_time']));
$request_date = date('F d, Y h:ia', strtotime($or_main_info['date_request']));   //Added by Cherry 06-22-10
$smarty->assign('or_request_date', $request_date);
$smarty->assign('or_consent_signed', ($or_main_info['consent_signed'] == 1) ? 'Yes' : 'No');

$case = array('pay_elective'=>'Pay - Elective', 'pay_stat'=>'Pay - Stat',
							'service_elective'=>'Service - Elective', 'service_stat'=>'Service - Stat');
$smarty->assign('or_request_case', $case[$or_main_info['or_case']]);
/** End **/

/** Form elements under fieldset "Patient Information" **/
//$seg_person = new Person($basic_info['pid']);
$seg_person = new Person($pid);
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
/** End **/

/** Form elements under fieldset "Pre-operation Details" **/
//Added by Cherry 06-22-10
switch($or_main_info['time_operation']){
	case '07:00 - 08:00':
												$time_operation = '07:00am - 08:00am';
												break;
	case '08:00 - 09:00':
												$time_operation = '08:00am - 09:00am';
												break;
	case '09:00 - 10:00':
												$time_operation = '09:00am - 10:00am';
												break;
	case '10:00 - 11:00':
												$time_operation = '10:00am - 11:00am';
												break;
	case '11:00 - 12:00':
												$time_operation = '11:00am - 12:00pm';
												break;
	case '12:00 - 13:00':
												$time_operation = '12:00pm - 01:00pm';
												break;
	case '13:00 - 14:00':
												$time_operation = '01:00pm - 02:00pm';
												break;
	case '14:00 - 15:00':
												$time_operation = '02:00pm - 03:00pm';
												break;
	case '15:00 - 16:00':
												$time_operation = '03:00pm - 04:00pm';
												break;
	case '16:00 - 17:00':
												$time_operation = '04:00pm - 05:00pm';
												break;

}
$operation_date = date('F d, Y', strtotime($or_main_info['date_operation'])).' '.$time_operation;
//end Cherry
#$operation_date = date('F d, Y h:ia', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));
$smarty->assign('or_operation_date', $operation_date);

$or_est_op_length = $or_main_info['est_length_op'];
$pre_operative_diagnosis = $or_main_info['pre_op_diagnosis'];
#$operation_procedure = $or_main_info['operation_procedure'];
$operation_procedure = $or_main_info['or_procedure'];
$smarty->assign('or_est_op_length', $or_est_op_length);
$smarty->assign('pre_operative_diagnosis', $pre_operative_diagnosis);
$smarty->assign('operation_procedure', $operation_procedure);
$classification = array('clean'=>'Clean', 'contaminated'=>'Contaminated',
												'wound_dehiscence'=>'Wound Dehiscence',
												'clean_contaminated'=>'Clean/Contamianted',
												'dirty_infected'=>'Dirty/Infected');
$smarty->assign('or_case_classification', $classification[$or_main_info['case_classification']]);

 /** End **/

/** Form elements under fieldset "Other Details" **/
$special_requirements = array('cp_clearance'=>'CP Clearance', 'pulmo_clearance'=>'Pulmo Clearance', 'consent'=>'Consent',
															 'pedia_clearance'=>'Pedia Clearance', 'others'=>'Others');
$array = explode(',', $or_main_info['special_requirements']);
$temp = array();
foreach ($array as $key => $value) {
				$temp[] = $special_requirements[$value];
}

$smarty->assign('or_special_requirements', implode(', ', $temp));
#$smarty->assign('or_special_requirements', $special_requirements[$or_main_info['special_requirements']]);
/** End **/

#Added by Cherry 07-11-10
$smarty->assign('clinical_summary', '<a href="javascript:void(0)" onclick="openReport(this.id,'.$refno.','.$pid.');" id="clinical_summary"></a>');

#Added by Cherry 07-25-10
$smarty->assign('op_technique_form', '<a href="javascript:void(0)" onclick="openReport(this.id,'.$refno.','.$pid.');" id="op_technique_form"></a>');


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
$add_icpm_onclick = 'onclick="return overlib(OLiframeContent(\''.$root_path.'modules/or/request/seg-op-tray.php\', 625, 380, \'fOrderTray\', 1, \'auto\'),WIDTH, 380, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',CAPTIONPADDING,4,
				CAPTION,\'Add procedure codes from ICPM tray\',MIDX,0, MIDY,0, STATUS,\'Add procedure codes from ICPM tray\');"onmouseout="nd();"';
/** End **/

#added by CHA 11-17-09
$add_anesthesia_onclick = 'onclick="return overlib(OLiframeContent(\''.$root_path.'modules/or/or_main/or_main_post_anesthesia_tray.php\', 700, 400, \'fOrderTray\', 1, \'auto\'),WIDTH, 380, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',CAPTIONPADDING,4,
				CAPTION,\'Add anaesthesia procedure\',MIDX,0, MIDY,0, STATUS,\'Add anaesthesia procedure\');"onmouseout="nd();"';                    #550, 280
#end CHA

/** Buttons for add surgeon, assistant surgeon, scrub nurse, circulating nurse **/

$smarty->assign('add_surgeon', '<a href="javascript:void(0)" id="add_surgeon" '.$add_surgeon_onclick.'></a>');
$smarty->assign('add_assistant_surgeon', '<a href="#" id="add_assistant_surgeon" '.$add_assistant_surgeon_onclick.'></a>');
$smarty->assign('add_scrub_nurse', '<a hre="#" id="add_scrub_nurse" '.$add_scrub_nurse_onclick.'></a>');
$smarty->assign('add_circulating_nurse', '<a href="#" id="add_circulating_nurse" '.$add_circulating_nurse_onclick.'></a>');
$smarty->assign('add_anesthesiologist', '<a href="#" id="add_anesthesiologist" '.$add_anesthesiologist_onclick.'></a>');
#$smarty->assign('add_anesthesia_procedure', '<a href="#" id="add_anesthesia_procedure" onclick="show_popup()"></a>');
$smarty->assign('add_anesthesia_procedure', '<a href="#" id="add_anesthesia_procedure" '.$add_anesthesia_onclick.'></a>');	#edited by CHA 11-17-09
$smarty->assign('add_icpm', '<a href="javascript:void(0)" id="add_icpm" '.$add_icpm_onclick.'></a>');
$smarty->assign('empty_icpm', '<a href="javascript:void(0)" id="empty_icpm" onclick="emptyTray()"></a>');
$smarty->assign('supplies_add_button','<a href="javascript:void(0)" id="add_item" onclick="return openOrderTray2();"></a>');
$smarty->assign('supplies_empty_button','<a href="javascript:void(0)" id="empty_item" onclick="if (confirm(\'Clear the order list?\')) emptyTraySupplies()"></a>');
$smarty->assign('add_equipment', '<a href="javascript:void(0)" id="add_equipment" onclick="show_popup_equipment()"></a>');
$smarty->assign('empty_equipment', '<a href="javascript:void(0)" id="empty_equipment" onclick="empty_equipment()"></a>');
/** End **/



/** Other form elements **/
$smarty->assign('or_main_submit', '<input type="button" id="or_main_submit" value="" onclick="confirming();" />');
$smarty->assign('or_main_cancel', '<a href="'.$breakfile.'" id="or_main_cancel"></a>');

//added by CHA 10-23-09
$smarty->assign('or_main_surgical_memo_report', '<a href="javascript:void(0)" onclick="openReport(this.id,'.$refno.');" id="or_main_surgical_memo_report"></a>');
$smarty->assign('or_main_anesthesia_report', '<a href="javascript:void(0)" onclick="openReport(this.id,'.$refno.');" id="or_main_anesthesia_report"></a>');
$smarty->assign('or_main_op_room_report', '<a href="javascript:void(0)" onclick="openReport(this.id,'.$refno.');" id="or_main_op_room_report"></a>');
//end CHA
//Commented by Cherry 06-22-10
//if(empty($encounter_nr)){
//	$encounter_nr = $pharma['encounter_nr'];
//}
$hospital_number = $pharma['pid'];

$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
$smarty->assign('hospital_number', '<input type="hidden" name="hospital_number" id="hospital_number" value="'.$hospital_number.'" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('op_nr', '<input type="hidden" name="op_nr" value="'.$op_nr.'" />');
$smarty->assign('op_room', '<input type="hidden" name="op_room" value="'.$op_room.'" />');
$smarty->assign('dept_nr', '<input type="hidden" name="dept_nr" value="'.$dept_nr.'" />');
$smarty->assign('refno', '<input type="hidden" name="refno" value="'.$refno.'" />');
$smarty->assign('pid', '<input type="hidden" name="pid" value="'.$pid.'" />');   //Added by Cherry 06-22-10
$smarty->assign('or_request_nr', '<input type="hidden" name="or_request_nr" value="'.$nr.'" />');
$smarty->assign('or_main_refno', '<input type="hidden" name="or_main_refno" value="'.$or_main_info['or_main_refno'].'" />');
/** End **/

/** Popup elements **/

$smarty->assign('close_anesthesia', '<a href="javascript:void(0)" id="close_anesthesia" class="jqmClose"></a>');
$smarty->assign('resize', '<img src="'.$root_path.'images/or_main_images/resize.gif" class="jqResize" />');

$smarty->assign('close_equipment', '<a href="javascript:void(0)" id="close_equipment" class="jqmClose"></a>');

/** End **/

/** Elements for flexigrid **/
$number_of_pages = array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30');
$smarty->assign('number_of_pages', $number_of_pages);
$smarty->assign('page_number', '<input type="text" id="page_number" name="page_number" />');
$smarty->assign('search_field', '<input type="text" id="search_field" name="search_field" />');
$smarty->assign('departments', $list_dept);
$smarty->assign('selected_department', 'all');
$smarty->assign('search_button', '<input type="submit" id="search_button" value="Search" />');
/** End **/

/** For OR Charges **/
$social_service = new SocialService();
$social_service_details = $social_service->getLatestClassificationByPid($basic_info['pid']); //get the discount and discountid
$is_sc = ($social_service_details['discountid'] == 'SC') ? '1' : '0';

$smarty->assign('pharma_area', '<input type="hidden" name="pharma_area" id="pharma_area" value="OR" />');
$smarty->assign('issc', '<input type="hidden" name="issc" id="issc" value="'.$is_sc.'" />');
$smarty->assign('discountid', '<input type="hidden" name="discountid" id="discountid" value="'.$social_service_details['discountid'].'" />');
$smarty->assign('discount', '<input type="hidden" name="discount" id="discount" value="'.$social_service_details['discount'].'" />');
$smarty->assign('transaction_type', '<input type="hidden" name="transaction_type" id="transaction_type" value="'.$basic_info['is_cash'].'" />');

/** End **/

global $db;


$query="select refno from seg_pharma_orders where encounter_nr='".$encounter_nr."' order by orderdate desc";
$existing=0;
#echo "query before order refno..".$query."</p>";
$result=$db->Execute($query);

if($result){
	$row=$result->FetchRow();
	$existing=$row['refno'];
}
if($existing==0){
	$seg_order = new SegOrder('pharma');
	$order_refno=$seg_order->getLastNr(date("Y-m-d"));
}
else{
	$order_refno=$existing;
}
#echo  "order refno..".$order_refno."</p>";

if (isset($_POST['submitted'])) {
	 $seg_order = new SegOrder('pharma');




	 #$order_refno = ($mode == 'new' || $_POST['pharma_refno'] == 0) ? $seg_order->getLastNr(date("Y-m-d")) : $pharma_refno;
	 $cnt_anesthetic = count($_POST['anesthetic_id']);
	 $order_anesthetic_id = Array();
	 $order_anesthetic_qty = Array();
	 $order_anesthetic_cash = Array();
	 $order_anesthetic_charge = Array();
	 for($i=0;$i<$cnt_anesthetic;$i++)
	 {
			$order_anesthetic_id[$i] = explode(",",$_POST['anesthetic_id'][$i]);
			$order_anesthetic_qty[$i] = explode(",",$_POST['anesthetic_qty'][$i]);
			$order_anesthetic_cash[$i] = explode(",",$_POST['anesthetic_pcash'][$i]);
			$order_anesthetic_charge[$i] = explode(",",$_POST['anesthetic_pcharge'][$i]);
	 }

 $data = array('refno' => $refno,
							 'or_main_refno' => $_POST['or_main_refno'],
							 'post_time_started' => date('H:i', strtotime($_POST['post_time_started'].' '.$_POST['pts_meridian'])),
							 'post_time_finished' => date('H:i', strtotime($_POST['post_time_finished'].' '.$_POST['ptf_meridian'])),
							 'post_operative_diagnosis' => $_POST['post_operative_diagnosis'],
							 'surgeon' => $_POST['surgeon'],
							 'surgeon_assist' =>  $_POST['surgeon_assist'],
							 'anesthesiologist' => $_POST['anesthesiologist'],
							 'nurse_scrub' => $_POST['nurse_scrub'],
							 'nurse_rotating' => $_POST['nurse_rotating'],
							 'rvu' => $_POST['rvu'],
							 'ops_code' => $_POST['ops_code'],
							 'multiplier' => $_POST['multiplier'],
							 'ops_charge' => $_POST['ops_charge'],
							 'anesthesia_id' => $_POST['anesthesia_id'],                        /**-modified by cha 01-05-2010-**/
							 'time_begun' => $_POST['anesthesia_timestart'],
							 'time_ended' => $_POST['anesthesia_timeend'],
							 'tb_meridian' => $_POST['anesthesia_ts_meridian'],
							 'te_meridian' => $_POST['anesthesia_te_meridian'],
							 'anesthetics' => $order_anesthetic_id,
							 'anesthesia_procedure_category' => $_POST['anesthesia_category'],
							 'anesthesia_procedure_specific'=> $_POST['anesthesia_specific'],
							 'order_refno' => $order_refno,																																		/**-end CHA 01-05-2010-**/
							 'intra_operative' => $_POST['anesthetic_intra_operative'],
							 'post_operative' => $_POST['anesthetic_post_operative'],
							 'or_status' => $_POST['anesthetic_patient_status'],
							 'or_technique' => $_POST['or_technique'],
							 'operation_performed' => $_POST['operation_performed'],
							 'transferred_to' => $_POST['transferred_to'],
							 'sponge_count' => $_POST['sponge_count'],
							 'sutures' => $_POST['sutures'],
							 'sponge_os' => $_POST['sponge_os'],
							 'sponge_ap' => $_POST['sponge_ap'],
							 'sponge_cb' => $_POST['sponge_cb'],
							 'sponge_pp' => $_POST['sponge_pp'],
							 'sponge_peanuts' => $_POST['sponge_peanuts'],
							 'needle_count' => $_POST['needle_count'],
							 'instrument_count' => $_POST['instrument_count'],
							 'fluids' => $_POST['fluids'],
							 'drain_inserted' => $_POST['drain_inserted'],
							 'packs_inserted' => $_POST['packs_inserted'],
							 'blood_replacement' => $_POST['blood_replacement'],
							 'blood_loss' => $_POST['blood_loss'],
							 'tissues_removed' => $_POST['tissues_removed'],
							 'remarks' => $_POST['remarks']
							);

//echo "</br>".print_r($data);
 if ($seg_ops->update_post_operative($data)) {
		 $smarty->assign('sysInfoMessage','OR Main post operative details successfully updated.');
 }


	//add the anesthetics to ordered items from pharma
	//added by cha 01-06-2010
	for($i=0;$i<count($order_anesthetic_id);$i++)
	{
			for($j=0;$j<count($order_anesthetic_id[$i]);$j++)
			{
				$_POST['items'][] = $order_anesthetic_id[$i][$j];
				$_POST['qty'][] = $order_anesthetic_qty[$i][$j];
				$_POST['pcash'][] = $order_anesthetic_cash[$i][$j];
				$_POST['pcharge'][] = $order_anesthetic_charge[$i][$j];
				$_POST['prc'][] = $order_anesthetic_cash[$i][$j];
			}
	}

 //$db->StartTrans();   //see *manolo
 extract(calculate_total_pharma_order());
 /*
 $order_data = array(
						'refno' => $order_refno,
						'encounter_nr' => $basic_info['encounter_nr'],
						'pharma_area' => 'OR',
						'pid' => $basic_info['pid'],
						'ordername' => $person_name,
						'orderaddress' => $person_address,
						'orderdate' => date('Y-m-d H:i'),
						'is_cash' => $basic_info['is_cash'],
						'amount_due' => $total,
						'is_tpl'=> 1, //temp
						'discount' => $_POST['discount'],
						'discountid' => $_POST['discountid'],
						'is_urgent' =>0, //temp
						'comments' =>'', //temp
						'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
						'create_id' =>$_SESSION['sess_temp_userid'],
						'modify_id' =>$_SESSION['sess_temp_userid'],
						'modify_time' =>date('YmdHis'),
						'create_time'=>date('YmdHis')
						); */
	$order_data = array(
						'refno' => $order_refno,
						'encounter_nr' => $_POST['encounter_nr'],
						'pharma_area' => 'OR',
						'request_source' => $_POST['request_source'], #added by cha, august 21, 2010
						'pid' => $_POST['pid'],
						'ordername' => $person_name,
						'orderaddress' => $person_address,
						'orderdate' => date('Y-m-d H:i'),
						'is_cash' => $or_main_info['trans_type'],
						'amount_due' => $total,
						'is_tpl'=> 1, //temp
						'discount' => $_POST['discount'],
						'discountid' => $_POST['discountid'],
						'is_urgent' =>0, //temp
						'comments' =>'', //temp
						'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
						'create_id' =>$_SESSION['sess_temp_userid'],
						'modify_id' =>$_SESSION['sess_temp_userid'],
						'modify_time' =>date('YmdHis'),
						'create_time'=>date('YmdHis')
						);

	$seg_order->setDataArray($order_data);
	$order_count = count($_POST['items']);
	if (($mode == 'new' || $_POST['pharma_refno'] == 0) && $order_count > 0) {
		$currentErrorMsg = "Unable to save order information..."; //See *manolo

		$saveok = $seg_order->insertDataFromInternalArray();
		#echo "insert query: ".$seg_order->sql;
		#process_inventory('new', $order_data['refno'], $_POST['items'], $_POST['qty']);
	}
	elseif ($mode == 'edit' || $_POST['pharma_refno'] != 0) {
		$pharma_refno = $_POST['pharma_refno'];
		$seg_order->where = "refno=".$db->qstr($pharma_refno);

		$currentErrorMsg = "Unable to save order information..."; //See *manolo
		$saveok = $seg_order->updateDataFromInternalArray($pharma_refno,FALSE);
		#echo "update query: ".$seg_order->sql;

		#process_inventory('edit', $pharma_refno, $_POST['items'], $_POST['qty']);
	}

	if ($saveok) {
		#echo "check if save is ok...."."<br>";
		#$order_refno = ($mode == 'new' || $_POST['pharma_refno'] == 0) ? $order_data['refno'] : $pharma_refno;
		#echo "order_refno= ".$order_refno."<br>";
		$currentErrorMsg = "Unable to update order details..."; //see *manolo

		$saveok = $seg_order->clearOrderList($order_refno);  //see *manolo

		#if ($saveok) $saveok = $seg_order->addOrders($order_refno, $bulk);  //see *manolo
		if ($saveok){
//			echo "check if save is ok...going to add orders now"."<br>";
//			print_r($bulk);
//			echo "<br>"."<br>";
			 $saveok = $seg_order->addOrders($order_refno, $bulk);  //see *manolo
			 #echo print_r($bulk,true)."</p>";
			 #echo "sql: ".$seg_order->sql;
			 #echo "save: ".$saveok;
		}
		#echo "saveok:".var_export($saveok,true)."/";
//		echo "order data: ".print_r($order_data);
//		if ($saveok) {  //see maramag for sponge thingy
			if (count($_POST['items']) > 0) {
				$item_array = $_POST['items'];
				$prc_array = $_POST['prc'];
				$status_array = array_fill(0, count($item_array), 'S');
				$remarks_array = array_fill(0, count($item_array), '');
				$saveok = $seg_order->changeServeStatus($order_refno, $item_array, $status_array, $remarks_array);
			}

			$bulk = array();
			if ($_POST['issc']) $bulk[] = 'SC';

			foreach ($_POST["discount"] as $i=>$v) {
				if ($v) $bulk[] = array($v);
			}

			$seg_order->clearDiscounts($order_refno);
			if ($bulk) $seg_order->addDiscounts($order_refno, $bulk);

			if ($mode == 'new' || $_POST['pharma_refno'] == 0) {

			 if ($saveok){
				$saveok = $seg_ops->insert_care_encounter_pharma_order($order_data['refno'], $refno);
			 }

			}
//		}

		//if ($saveok) //$db->CompleteTrans();  //see *manolo
 }

 if (!$saveok) {
		 /** see manolo **/
			$errorMsg = $db->ErrorMsg();
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sysErrorMessage','<strong>Error:</strong> An item with the same order number already exists in the database.');
			else
				$smarty->assign('sysErrorMessage',"<strong>$currentErrorMsg</strong><br/><strong>DB Error:</strong> $errorMsg");
			//$db->FailTrans();
			/** see manolo **/
 }

 if ($_POST['discount'] == '' || !isset($_POST['discount'])) {
	 $discount = 0;
 }

 $equipment_array = array('request_refno' => $refno,
													'area' => 'OR',
													'order_date' => date('Y-m-d H:i'),
													'pid' => $basic_info['pid'],
													'encounter_nr' => $basic_info['encounter_nr'],
													'patient_name' => $person_name,
													'patient_address' => $person_address,
													'discountid' => $_POST['discountid'],
													'discount' => $discount,
													'is_cash' => $basic_info['is_cash'],
													'is_sc' => $is_sc,
													'number_of_usage' => $_POST['number_of_usage'],
													'equipments' => $_POST['equipments'],
													'equipment_serial' => $_POST['equipment_serial'],
													'original_price' => $_POST['original_price'],
													'adjusted_price' => $_POST['adjusted_price'],
													'account_total' => $_POST['account_total'],
													'price_cash' => $_POST['equipment_cash'],
													'price_charge' => $_POST['equipment_charge']
												 );



 $seg_equipment = new SegEquipmentOrder($equipment_array);

	 if ($seg_equipment->calculate_total_orders()) {
		 if (($mode == 'new' || $_POST['equipment_refno'] == 0) && count($_POST['equipments']) > 0) {
			 if ($equipment_refno = $seg_equipment->add_order()) {
				 if ($seg_equipment->validate()) {
					 $seg_equipment->add_order_item_by_bulk($equipment_refno);
				 }
			 }
		 }
		 if ($mode == 'edit' && $_POST['equipment_refno'] != 0) {
			 $equipment_refno = $_POST['equipment_refno'];
			 if ($seg_equipment->calculate_total_orders()) {
				 if ($seg_equipment->update_order($equipment_refno)) {
					 if ($seg_equipment->delete_order_item($equipment_refno)) {
						 if (count($_POST['equipments']) > 0) {
							 if ($seg_equipment->validate()) {
								 $seg_equipment->add_order_item_by_bulk($equipment_refno);
							 }
						 }
					 }
				 }
			 }
		 }
	 }

	$sponge_array = array('or_main_refno' => $_POST['or_main_refno'],
												'sponges' => $_POST['sponges'],
												'sponges_quantity' => $_POST['sponge_quantity'],
												'first_count_on_table' => $_POST['first0'],
												'first_count_on_floor' => $_POST['first1'],
												'second_count_on_table' => $_POST['second0'],
												'second_count_on_floor' => $_POST['second1']);
 if ($seg_ops->remove_sponges($_POST['or_main_refno'])) {
	 if (count($_POST['sponges']) > 0) {
		 $seg_ops->add_sponge_item_by_bulk($sponge_array);
	 }
 }

 $mode = 'edit';
}
#echo "sss = ".$order_refno;
//if ($mode == 'edit') {    edited by Cherry 07-06-10
	#$pharma = $seg_ops->get_seg_pharma_or_main($refno);
	if ($_POST['encounter_nr']){
		$encounter_nr	= $_POST['encounter_nr'];
	}
	elseif ($_GET['encounter_nr']){
		$encounter_nr	= $_GET['encounter_nr'];
	}

	if ($_POST['pid'])
		$pid	= $_POST['pid'];
	elseif ($_GET['pid'])
		$pid	= $_GET['pid'];
	#echo "sss = ".$encounter_nr;
	#edited by CHE 07-06-2010
	#echo $_GET['encounter_nr']."  pid ".$_POST['pid'];
	$pharma = $seg_ops->get_seg_pharma_orderRefno($encounter_nr,$pid);
	#echo  "fetch..".$seg_ops->sql."</p>";
	#echo $seg_ops->sql;
	#echo $seg_ops->sql;
	if ($pharma) {
		#$pharma_refno = $pharma['pharma_refno'];
		$pharma_refno = $pharma['refno'];
	}
	else {
		$pharma_refno = 0;
	}
/*}
else {
	$pharma_refno = 0;
} */


$equipment = new SegEquipmentOrder();
$equipment_refno = $equipment->get_equipment_refno($refno);

if (!$pharma_refno)
	$pharma_refno= $order_refno;

$smarty->assign('equipment_refno', '<input type="hidden" name="equipment_refno" value="'.$equipment_refno.'" />');
$smarty->assign('pharma_refno', '<input type="hidden" name="pharma_refno" value="'.$pharma_refno.'" />');
$smarty->assign('mode', '<input type="hidden" name="mode" value="'.$mode.'" />');

$smarty->assign('is_in_or_main_post', '<input id="is_in_or_main_post" type="hidden" name="is_in_or_main_post" value="1" />');
/** Form elements under fieldset "Post-operative Details **/
$post_op_details = $seg_ops->get_or_main_post_details($or_main_info['or_main_refno']);

$post_time_started = $post_op_details['time_started'];
$post_time_finished = $post_op_details['time_finished'];
$post_op_diagnosis = $post_op_details['post_op_diagnosis'];
$anesthetic_intra_operative = $post_op_details['intra_operative'];
$anesthetic_post_operative = $post_op_details['post_operative'];
$anesthetic_patient_status = $post_op_details['or_status'];
$operation_performed = $post_op_details['operation_performed'];
$or_technique = $post_op_details['or_technique'];
$sponge_count = ($post_op_details['sponge_count'] > 0) ? $post_op_details['sponge_count'] : 0;
$needle_count = $post_op_details['needle_count'] > 0 ? $post_op_details['needle_count'] : 0 ;
$instrument_count = $post_op_details['instrument_count'] > 0 ? $post_op_details['instrument_count'] : 0;
$fluids = $post_op_details['fluids'];
$drain_inserted = $post_op_details['drain_inserted'];
$packs_inserted = $post_op_details['packs_inserted'];
$blood_replacement = $post_op_details['blood_replacement'];
$blood_loss = $post_op_details['blood_loss'];
$tissues_removed = $post_op_details['tissues_removed'];
$remarks = $post_op_details['remarks'];

#added code by angelo m. 09.03.2010
#start here
$sutures = $post_op_details['sutures'];
$sponge_os = $post_op_details['sponge_os'];
$sponge_ap = $post_op_details['sponge_ap'];
$sponge_cb = $post_op_details['sponge_cb'];
$sponge_pp = $post_op_details['sponge_pp'];
$sponge_peanuts = $post_op_details['sponge_peanuts'];
//#end here



$smarty->assign('post_time_started', '<input type="text" name="post_time_started" id="post_time_started" value="'.$post_time_started.'" onchange="isValidTime(1);" />');
$smarty->assign('post_time_finished', '<input type="text" name="post_time_finished" id="post_time_finished"  value="'.$post_time_finished.'" onchange="isValidTime(2);" />');
$smarty->assign('pts_meridian', array('am' => 'AM', 'pm' => 'PM'));
$smarty->assign('ptf_meridian', array('am' => 'AM', 'pm' => 'PM'));
$smarty->assign('post_operative_diagnosis', '<textarea name="post_operative_diagnosis">'.$post_op_diagnosis.'</textarea>');
$smarty->assign('operation_performed', '<textarea name="operation_performed">'.$operation_performed.'</textarea>');
$smarty->assign('or_technique', '<textarea name="or_technique">'.$or_technique.'</textarea>');
$smarty->assign('pts_meridian_selected', strtolower($post_op_details['ts_meridian']));
$smarty->assign('ptf_meridian_selected', strtolower($post_op_details['tf_meridian']));
$ward = new Ward();

//$ward_array = array(0=>'Please Select Ward');

$smarty->assign('transferred_to', $ward->get_all_active_wards_as_pair('nr', 'name'));
/** End **/

/** Anesthetic agent **/
$smarty->assign('anesthetic_intra_operative', '<textarea name="anesthetic_intra_operative">'.$anesthetic_intra_operative.'</textarea>');
$smarty->assign('anesthetic_post_operative', '<textarea name="anesthetic_post_operative">'.$anesthetic_post_operative.'</textarea>');
$smarty->assign('anesthetic_patient_status', '<textarea name="anesthetic_patient_status">'.$anesthetic_patient_status.'</textarea>');
/** End **/

/** Form elements under Others Details **/
#$smarty->assign('sponge_count', '<input type="text" readonly="readonly" name="sponge_count" id="sponge_count" value="'.$sponge_count.'" />');
$smarty->assign('sponge_count', '<input type="text" name="sponge_count" id="sponge_count" value="'.$sponge_count.'" onkeypress="return isNumberKey(event)"/>');
#added code by angelo m. 09.03.2010
#start here
$smarty->assign('sutures', '<input type="text" name="sutures" id="sutures"  value="'.$sutures.'"  />');
$smarty->assign('sponge_os', '<input type="text" name="sponge_os" id="sponge_os"  value="'.$sponge_os.'" onkeypress="return isNumberKey(event)" />');
$smarty->assign('sponge_ap', '<input type="text" name="sponge_ap" id="sponge_ap" value="'.$sponge_ap.'" onkeypress="return isNumberKey(event)"/>');
$smarty->assign('sponge_cb', '<input type="text" name="sponge_cb" id="sponge_cb" value="'.$sponge_cb.'" onkeypress="return isNumberKey(event)"/>');
$smarty->assign('sponge_pp', '<input type="text" name="sponge_pp" id="sponge_pp" value="'.$sponge_pp.'" onkeypress="return isNumberKey(event)"/>');
$smarty->assign('sponge_peanuts', '<input type="text" name="sponge_peanuts" id="sponge_peanuts" value="'.$sponge_peanuts.'" onkeypress="return isNumberKey(event)"/>');
#end here


$smarty->assign('needle_count', '<input type="text" name="needle_count" id="needle_count" value="'.$needle_count.'" />');
$smarty->assign('instrument_count', '<input type="text" name="instrument_count" id="instrument_count" value="'.$instrument_count.'" />');

/*---added by CHA 10-22-09---*/
$smarty->assign('fluids', '<textarea name="fluids">'.$fluids.'</textarea>');
$smarty->assign('drain_inserted', '<input type="text" name="drain_inserted" id="drain_inserted" value="'.$drain_inserted.'" />');
$smarty->assign('packs_inserted', '<input type="text" name="packs_inserted" id="packs_inserted" value="'.$packs_inserted.'" />');
$smarty->assign('blood_replacement', '<input type="text" name="blood_replacement" id="blood_replacement" value="'.$blood_replacement.'" />');
$smarty->assign('blood_loss', '<input type="text" name="blood_loss" id="blood_loss" value="'.$blood_loss.'" />');
$smarty->assign('tissues_removed', '<input type="text" name="tissues_removed" id="tissues_removed" value="'.$tissues_removed.'" />');
$smarty->assign('remarks', '<textarea name="remarks">'.$remarks.'</textarea>');

/*---end CHA---*/

/** End **/

/**--Added by CHA 10/07/09 --**/
global $db;
$query="select or_type from seg_or_main where ceo_refno=".$db->qstr($_GET['refno']);
#echo "\nquery: ".$query;
$result=$db->Execute($query);
if($result)
	$row=$result->FetchRow();
	#echo "\ntype: ".$row['or_type'];
	//Commented by Cherry 06-22-10
	/*if($row['or_type']=="DR")
			 $smarty->assign('sMainBlockIncludeFile','or/or_main_post_dr.tpl');
	else
		$smarty->assign('sMainBlockIncludeFile','or/or_main_post.tpl');*/
/**--END CHA--**/

$smarty->assign('sMainBlockIncludeFile','or/or_asu_post.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

function calculate_total_pharma_order() {
	$bulk = array();
	$orig = $_POST['iscash'] ? $_POST['pcash'] :  $_POST['pcharge'];
	$total = 0;
	foreach ($_POST["items"] as $i=>$v) {
		$consigned = in_array($v, $_POST['consigned']) ? '1' : '0';
		$bulk[] = array(
		"item"=>$_POST["items"][$i],
		//"dosage"=>$_POST["dosage"][$i], //see *manolo
		"qty"=>$_POST["qty"][$i],
		"price_cash"=>parseFloatEx($_POST["prc"][$i]),
		"price_charge"=>parseFloatEx($_POST["prc"][$i]),
		"consigned"=>$consigned, "price_orig"=>$orig[$i]);
		$total += (parseFloatEx($_POST["prc"][$i]) * (float) $_POST["qty"][$i]);
	}
	return array('total' => $total, 'bulk' => $bulk);
}

function process_inventory($mode, $pharma_refno, $current_items, $current_quantities) {
	global $root_path;
	$seg_order_inventory = new SegOrder('pharma');
	require_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
	$unit = new Unit();
	$unit->unit_id = null;
	$unit->is_unit_per_pc = 1;
	if ($mode=='new') {
		foreach ($current_items as $key => $value) {
			$inventory = new Inventory();
			$inventory->area_code = 'OR';
			$inventory->item_code = $value;
			$matrix = $seg_order_inventory->prepareInventoryMatrix($inventory->item_code, $inventory->area_code);
			$inventory->remInventory($current_quantities[$key], $unit, $matrix[0]);
			if ($matrix) {
				$q1 = (int)$current_quantities[$key];
				foreach ($matrix as $mat) {
					$q2 = (int)$mat[1];
					if (($q1-$q2)>0) {
						$saveok = $inventory->remInventory($q2, $unit, $mat[0]);
						$q2 = $q2-$q1;
					}
					else {
						$saveok = $inventory->remInventory($q1, $unit, $mat[0]);
						if (!$saveok) echo 'Error Db';//echo $db->ErrorMsg();
						break;
					}
					if (!$saveok) break;
				}
			}
		}
	}
	if ($mode=='edit') {

		#$items_in_dbase = $seg_order_inventory->get_order_items($pharma_refno);
		#$items_in_dbase = $seg_order_inventory->get_order_items($pharma_refno);
		foreach ($items_in_dbase['items'] as $key => $value)  {
			$inventory = new Inventory();
			$inventory->area_code = 'OR';
			$inventory->item_code = $value;
			$inventory->addInventory($items_in_dbase['quantities'][$key], $unit, null, null, '');
		}
		foreach ($current_items as $key => $value) {
			$inventory = new Inventory();
			$inventory->area_code = 'OR';
			$inventory->item_code = $value;
			$matrix = $seg_order_inventory->prepareInventoryMatrix($inventory->item_code, $inventory->area_code);

			if ($matrix) {
				$q1 = (int)$current_quantities[$key];
				foreach ($matrix as $mat) {
					$q2 = (int)$mat[1];
					if (($q1-$q2)>0) {
						$saveok = $inventory->remInventory($q2, $unit, $mat[0]);
						$q2 = $q2-$q1;
					}
					else {
						$saveok = $inventory->remInventory($q1, $unit, $mat[0]);
						if (!$saveok) echo 'Error Db'; //$db->ErrorMsg();
						break;
					}
					if (!$saveok) break;
				}
			}
		}
	}
}



function setup_calendar($display_area, $input_field, $button) {

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
?>
<script>
function openOrderTray() {
				overlib(
						OLiframeContent('<?=$root_path?>modules/or/request/seg-order-tray.php', 660, 360, 'fOrderTray', 0, 'no'),
						WIDTH,600, TEXTPADDING,0, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
						CAPTIONPADDING,2,
						CAPTION,'Add product from Order tray',
						MIDX,0, MIDY,0,
						STATUS,'Add product from Order tray');
				return false

}

function openOrderTray2() {
				var discount = $('discountid').value;
				var area = $('pharma_area').value;
				var url = '<?=$root_path?>modules/or/request/seg-order-tray.php?area='+area+'&d='+discount;
				overlib(
						OLiframeContent(url, 660, 360, 'fOrderTray', 0, 'no'),
						WIDTH,600, TEXTPADDING,0, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
						CAPTIONPADDING,2,
						CAPTION,'Add pharmacy item from Order tray',
						MIDX,0, MIDY,0,
						STATUS,'Add product from Order tray');
				return false
}

function toggle_details() {
	J('#request_details').slideToggle('100');
}
function toggle_pre_op() {
	J('#pre_op_details').slideToggle('100');
}
J().ready(function() {
	J('#or_main_anesthesia')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');
	J('#or_main_equipment')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');
	J('#or_main_oxygen')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');

});

J('#or_main_anesthesia').jqm({
overlay: 80,
onShow: function(h) {
	h.w.fadeIn(1000, function(){h.o.show();});
},
onHide: function(h){
	h.w.fadeOut(1000, function(){h.o.remove();});
}});

J('#or_main_equipment').jqm({
overlay: 80,
onShow: function(h) {
	h.w.fadeIn(1000, function(){h.o.show();});
},
onHide: function(h){
	h.w.fadeOut(1000, function(){h.o.remove();});
}});

J('#or_main_oxygen').jqm({
overlay: 80,
onShow: function(h) {
	h.w.fadeIn(1000, function(){h.o.show();});
},
onHide: function(h){
	h.w.fadeOut(1000, function(){h.o.remove();});
}});

function show_popup() {
	J('#or_main_anesthesia').jqmShow();
	J('#or_anesthesia_table').flexigrid({
	url: '<?=$root_path?>modules/anesthesia/ajax_anesthesia.php?table=anesthesia_procedure_list',
	dataType: 'json',
	colModel : [
						{display: 'Anesthesia', name : 'name', width : 180, sortable : true, align: 'left'},
						{display: 'Select', name:'select', width: 140, sortable: false, align: 'left'}
						],
	sortname: ["nr"],
	sortorder: 'desc',
	useRp: true,
	rp: 5,
	resizable: true,
	domain: ['or_main_anesthesia']
});
}

function show_oxygen() {
	J('#or_main_oxygen').jqmShow();
	J('#or_oxygen_table').flexigrid({
	url: '<?=$root_path?>modules/or/ajax/ajax_oxygen.php?table=equipment_list',
	dataType: 'json',
	colModel : [
						{display: 'Serial Number', name : 'serial_number', width : 130, sortable : true, align: 'left'},
						{display: 'Remaining Quantity', name : 'remaining_quantity', width : 100, sortable : true, align: 'left'},
						{display: 'Expiry Date', name : 'expiry_date', width : 100, sortable : true, align: 'left'},
						{display: 'Select', name:'select', width: 140, sortable: false, align: 'left'}
						],
	sortname: ["expiry_date"],
	sortorder: 'asc',
	useRp: true,
	rp: 5,
	resizable: true,
	domain: ['or_main_oxygen']
	});
}

function hide_oxygen() {
	J('#or_main_oxygen').jqmHide();
}

function show_popup_equipment() {
	J('#or_main_equipment').jqmShow();
	J('#or_equipment_table').flexigrid({
	url: '<?=$root_path?>modules/or/ajax/ajax_equipment.php?table=equipment_list',
	dataType: 'json',
	colModel : [
						{display: 'Equipment', name : 'name', width : 90, sortable : true, align: 'left'},
						{display: 'Equipment Description', name : 'name', width : 180, sortable : true, align: 'left'},
						{display: 'Unit', name : 'name', width : 30, sortable : true, align: 'left'},
						{display: 'Cash', name : 'name', width : 50, sortable : true, align: 'left'},
						{display: 'Charge', name : 'name', width : 50, sortable : true, align: 'left'},
						{display: 'Is Socialized', name : 'name', width : 20, sortable : true, align: 'left'},
						{display: 'Select', name:'select', width: 100, sortable: false, align: 'left'}
						],
	sortname: ["equipment_name"],
	sortorder: 'desc',
	useRp: true,
	rp: 5,
	resizable: true,
	domain: ['or_main_equipment']
});
}


function populate_anesthesia_fields(details, iterator) {
	var anesthetics = document.getElementsByName('anesthetics[]'); //or J("input[@name='anesthetics[]']") wud work but it's kinda overkill for this purpose
	var time_begun = document.getElementsByName('time_begun[]');
	var time_ended = document.getElementsByName('time_ended[]');
	var tb_meridian = document.getElementsByName('tb_meridian[]');
	var te_meridian = document.getElementsByName('te_meridian[]');

	anesthetics[iterator].value = details.anesthetics;
	time_begun[iterator].value = details.time_begun;
	time_ended[iterator].value = details.time_ended;

	for(var i=0; i<tb_meridian[iterator].length; i++) {
		if (details.tb_meridian == tb_meridian[iterator].options[i].value) {
		tb_meridian[iterator].options[i].selected = true;
	}
	}
	for(var i=0; i<te_meridian[iterator].length; i++) {
		if (details.te_meridian == te_meridian[iterator].options[i].value) {
		te_meridian[iterator].options[i].selected = true;
	}
	}

}

function add_or_main_anesthesia(table, id, name) {

if ($('anesthesia_procedure_hidden'+id)) {
	alert('Existing');
}
else {
var table1 = $(table).getElementsByTagName('tbody').item(0);
if ($('empty_anesthesia_procedure_row')) {
	table1.removeChild($('empty_anesthesia_procedure_row'));
}
var row = document.createElement("tr");

var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
											{type: 'td_text', name: name},
											{type: 'input', name: 'anesthetics[]', text_value: ''},
											{type: 'input', name: 'time_begun[]', is_time: true, meridian: 'tb_meridian[]'},
											{type: 'input', name: 'time_ended[]', is_time: true, meridian: 'te_meridian[]'}];
for (var i=0; i<array_elements.length; i++) {
	var cell = document.createElement("td");
	if (array_elements[i].type == 'td_text')
		cell.appendChild(document.createTextNode(array_elements[i].name));
	if(array_elements[i].type == 'input')  {
		element = document.createElement(array_elements[i].type)
		cell.appendChild(element);
		element.name = array_elements[i].name;
		element.type = "text";
		if (array_elements[i].text_value) {
			element.value = array_elements[i].text_value;
		}
	}
	if (array_elements[i].type == 'img') {
		img = document.createElement("img");
		cell.appendChild(img);
		img.src = array_elements[i].src;
		img.style.cursor = "pointer";
		img.addEventListener("click", function() {remove_anesthesia_procedure(table, id)}, false);
	}

	if (array_elements[i].is_time) {
		element = document.createElement("select");
		element.name = array_elements[i].meridian;
		cell.appendChild(element);
		var options = ['AM', 'PM'];
		for (var j=0; j<options.length; j++) {
			var option = document.createElement("option");
			option.text = options[j];
			option.value = options[j];
			element.options[j] = option;
		}

	}
	cell.align = "center";
	row.appendChild(cell);
}
row.id = 'anesthesia_procedure'+id;
$(table).getElementsByTagName('tbody').item(0).appendChild(row);

var hidden_array = document.createElement('input');
hidden_array.name = "anesthesia_procedure[]";
hidden_array.type = "hidden";
hidden_array.value = id;
hidden_array.id = "anesthesia_procedure_hidden"+id;
document.forms[0].appendChild(hidden_array);
}
}

//modified by cha 01-06-2010
function remove_anesthesia_procedure(table, id) {
	var anesthetics = document.getElementById('anesthetic_id[]').value;
	xajax_refresh_order_anesthetics(anesthetics);

	var table1 = $(table).getElementsByTagName('tbody').item(0);
	table1.removeChild($('row'+id));
	//document.forms[0].removeChild($('anesthesia_procedure_hidden'+id));

	var anesthesia_cnt = parseInt(document.getElementById('anesthesia_cnt').value);
	anesthesia_cnt = anesthesia_cnt - 1;
	document.getElementById('anesthesia_cnt').value = parseInt(anesthesia_cnt);

	//if (!document.getElementsByName('anesthesia_procedure[]') || document.getElementsByName('anesthesia_procedure[]').length <= 0) {
	if (document.getElementById('anesthesia_cnt').value <= 0) {
		append_empty_anesthesia()
	}
}

function validate() {
 var errors = new Array();
 var errors_field = new Array();

	var array_elements = [ {field: J("input[@name='post_time_started']"),
												 field_value: J("input[@name='post_time_started']").val() + ' ' + J("select[@name='pts_meridian']").val(),
												 msg: 'Please enter a valid time',
												 msg_dest: J('#error_form_input', J('#time_started_msg')),
												 is_textfield: true,
												 type: 'time',
												 },
												 {field: J("input[@name='post_time_finished']"),
												 field_value: J("input[@name='post_time_finished']").val() + ' ' + J("select[@name='ptf_meridian']").val(),
												 msg: 'Please enter a valid time',
												 msg_dest: J('#error_form_input', J('#time_finished_msg')),
												 is_textfield: true,
												 type: 'time',
												 },
												 {field: J("textarea[@name='post_operative_diagnosis']"),
												 field_value: J("textarea[@name='post_operative_diagnosis']").val(),
												 msg: 'Please enter a post operative diagnosis',
												 msg_dest: J('#error_form_input', J('#post_op_msg')),
												 is_textfield: true
												 },
												 ];

	for (var i=0; i<array_elements.length; i++) {
		if (array_elements[i].field_value == '' || !array_elements[i].field_value
			 || (array_elements[i].type == 'time' && !is_valid_time(array_elements[i].field_value))) {
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
	if (errors_field.length > 0) {
		errors_field[0].focus();
		return false;
	}

 var surgeon_id = document.getElementsByName('surgeon[]');
 var anesthesiologist_id = document.getElementsByName('anesthesiologist[]');
 var anesthesia_id = document.getElementsByName('anesthesia_id[]');
 var icpm = document.getElementsByName('ops_code[]');
 var order_items = document

 if (surgeon_id.length == 0) {
	 errors.push('Please add a surgeon for this operation.')
 }
 if (anesthesiologist_id.length == 0) {
	 errors.push('Please add an anesthesiologist for this operation');
 }

if (anesthesia_id.length == 0) {
	 errors.push('Please add an anesthesia procedure');
 }
 if (icpm.length == 0) {
	 errors.push('Please add an ICPM');
 }

 //modified by cha 01-07-2010
 var time_begun = document.getElementsByName('anesthesia_timestart[]');
 var time_ended = document.getElementsByName('anesthesia_timeend[]');
 var tb_meridian = document.getElementsByName('anesthesia_ts_meridian[]');
 var te_meridian = document.getElementsByName('anesthesia_te_meridian[]');
 var anesthetics = document.getElementsByName('anesthetic_id[]');
 var is_error_time_flag = false;
 var is_error_anesthetics = false;

 for (var i=0; i<anesthesia_id.length; i++) {
	 if (!is_valid_time(time_begun[i].value + ' ' + tb_meridian[i].value)) {
			is_error_time_flag = true;
	 }
	 if (!is_valid_time(time_ended[i].value + ' ' + te_meridian[i].value)) {
		 is_error_time_flag = true;
	 }
	 if (anesthetics[i] == '') {
		 is_error_anesthetics = true;
	 }
 }

 if (is_error_time_flag) {
	 errors.push('Invalid time');
 }
 if (is_error_anesthetics) {
	 errors.push('One of the anesthetics is empty');
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

function is_valid_time(time_string) {

	var timePat = /^(\d{1,2}):(\d{2})(:(\d{2}))?(\s?(AM|am|PM|pm))?$/;

	var matchArray = time_string.match(timePat);
	if (matchArray == null) {
		//time is not a valid format
		return false;
	}
	hour = matchArray[1];
	minute = matchArray[2];
	second = matchArray[4];
	ampm = matchArray[6];

	if (second=="") { second = null; }
	if (ampm=="") { ampm = null }

	if (hour < 1  || hour > 12) {
		//hour must be between 1 and 12
		return false;
	}
	if (minute<0 || minute > 59) {
		//minute must be between 0 and 59
		return false;
	}
	if (second != null && (second < 0 || second > 59)) {
		//second must be between 0 and 59
		return false;
	}
	return true;
}

//formatting time
//added code by angelo m. 09.03.2010
function setFormatTime(thisTime,AMPM){

	var strTime = thisTime.value;
	var stime = strTime.substring(0,5);
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	var jtime = "";

//		trimString(thisTime);

	if (thisTime.value==''){
		seg_validTime=false;
		return;
	}

	stime = stime.replace(':', '');

	if (stime.length == 3){
		hour = stime.substring(0,1);
		minute = stime.substring(1,3);
	} else if (stime.length == 4){
		hour = stime.substring(0,2);
		minute = stime.substring(2,4);
	}else{
		alert("Invalid time format.");
		thisTime.value = "";
		seg_validTime=false;
		thisTime.focus();
		return;
	}

	jtime = hour + ":" + minute;
//		js_setTime(jtime);

	if (hour==0){
		 hour = 12;
		 document.getElementById(AMPM).value = "AM";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "PM";
	}

	ftime =  hour + ":" + minute;

	if(!ftime.match(f1) && !ftime.match(f2)){
		thisTime.value = "";
		alert("Invalid time format.");
		seg_validTime=false;
		thisTime.focus();
	}else{
		thisTime.value = ftime;
		seg_validTime=true;
	}
	return thisTime.value
}// end of function setFormatTime





function append_empty_anesthesia() {
	var table1 = $('anesthesia_procedure_list').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	var cell = document.createElement("td");
	row.id = "empty_anesthesia_procedure_row";
	cell.appendChild(document.createTextNode('Anesthesia procedure is currently empty..'));

	cell.colSpan = "7";
	row.appendChild(cell);
	$('anesthesia_procedure_list').getElementsByTagName('tbody').item(0).appendChild(row);
}

function add_to_sponge(details) {

	var sponge_table = $('sponge_list').getElementsByTagName('tbody').item(2);


	if ($('empty_sponge_row'))
		sponge_table.removeChild($('empty_sponge_row'));

	var parent_row = document.createElement("tr");
	parent_row.id = 'sponge_row'+details.id;
	var array_elements = [{type: 'td_text', value: details.name},
											{type: 'td_text', value: 'text_field'},
											{type: 'td_table', depth: 3, prefix: 'first'},
											{type: 'td_table', depth: 3, prefix: 'second'}];
	for (var i=0; i<array_elements.length; i++) {
		var parent_cell = document.createElement("td");
		parent_cell.align = "center";
		if (array_elements[i].type=='td_text') {
			if (array_elements[i].value=='text_field') {
				var input = document.createElement("input");
				input.type="text";
				input.readOnly = true;
				input.value = details.qty;
				input.id = "sponge_qty"+details.id;
				input.name = 'sponge_quantity[]';
				parent_cell.appendChild(input);
			}
			else {
				parent_cell.appendChild(document.createTextNode(array_elements[i].value));
				var input = document.createElement("input");
				input.type = "hidden";
				input.value = details.id;
				input.name = 'sponges[]';
				parent_cell.appendChild(input);
			}

		}
		if (array_elements[i].type=='td_table') {
			var child_table = document.createElement("table");
			var child_row = document.createElement("tr");
			for (var j=0; j<array_elements[i].depth; j++) {
				var child_cell = document.createElement("td");

				var input = document.createElement("input");

				input.type = "text";
				if (j==2) {
					input.readOnly = true;
				}
				input.name = array_elements[i].prefix+j+'[]';
				input.id = array_elements[i].prefix+details.id+j;
				input.setAttribute('onkeyup', "total_sponge_count('"+array_elements[i].prefix+details.id+"')");
				child_cell.appendChild(input);
				child_row.appendChild(child_cell);
			}
			child_table.appendChild(child_row);
			parent_cell.appendChild(child_table);
		}
		parent_row.appendChild(parent_cell);

	}
	sponge_table.appendChild(parent_row);
	final_total_sponge_count();

}



function total_sponge_count(id) {
	var number = /^\d+$/;
	var on_table = $(id+0).value;
	var on_floor = $(id+1).value;
	if (on_table.match(number) && on_floor.match(number)) {
		var total = parseInt(on_table)+parseInt(on_floor);
		$(id+2).value = total;
	}
}

function remove_sponge(id) {
	//alert($('sponge_row'+id));
	var table1 = $('sponge_list').getElementsByTagName('tbody').item(2);
	if ($('sponge_row'+id))
		table1.removeChild($('sponge_row'+id));
	if (!($('sponge_row'+id))) {
		var tr = document.createElement("tr");
		tr.id = "empty_sponge_row";
		var td = document.createElement("td");
		td.appendChild(document.createTextNode('No sponge item was added yet to the list of supplies...'));
		td.colSpan = "4";
		tr.appendChild(td);
		table1.appendChild(tr);
	}
	final_total_sponge_count();
}

//function adjust_sponge_quantity(id, value) {
//function adjust_sponge_quantity(details) {

//	if(details.type=="replace")
//		$('sponge_qty'+id).value = details.qty;
//	else if(details.type=="add")
//		$('sponge_qty'+id).value = $('sponge_qty'+id).value + details.qty;

//	final_total_sponge_count();
//}

function adjust_sponge_quantity(id, qty, type) {
	if(type=='replace')
		$('sponge_qty'+id).value = parseFloat(qty);
	else if(type=='add')
		$('sponge_qty'+id).value = parseFloat($('sponge_qty'+id).value);
	final_total_sponge_count();
}


function final_total_sponge_count() {
	var sponges = document.getElementsByName('sponge_quantity[]');
	var total = 0;
	for (var i=0; i<sponges.length; i++) {
		total += parseInt(sponges[i].value);
	}
	$('sponge_count').value = total;
}

J('#cancel').click(function() {
	 location.href='<?=$breakfile?>';
	 return false;
});

//added by CHA 10-23-09
function openReport(rept_id,refno, pid)
{
	//alert("report: "+rept_id);
	if(rept_id=='or_main_surgical_memo_report') window.open('or_surgical_memo_pdf.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	else if(rept_id=='or_main_anesthesia_report') window.open('or_anesthesia_record_pdf.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	else if(rept_id=='or_main_op_room_report') window.open('operation_room_record_pdf.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	else if(rept_id=='clinical_summary') window.open('or_clinical_summary_pdf.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	else if(rept_id=='op_technique_form') window.open('or_op_technique_form_pdf.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	else  alert("report: "+rept_id+" "+refno);
}
//end CHA

//added  by CHA 01-14-2010
function view_anesthetic_tray(srvqty,srvid,srvCash,srvCharge,rowid)
{
	var path =  '../../../modules/or/or_main/or_main_post_anesthetic_tray.php?id='+rowid+'&srvid='+srvid+'&srvqty='+srvqty+'&srvCash='+srvCash+'&srvCharge='+srvCharge+'&mode=view';
	return overlib(
						OLiframeContent(path, 660, 150, 'fOrderTray', 1, 'auto'),
						WIDTH,600, TEXTPADDING,0, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						CLOSETEXT, '<img src=../../../images/close_red.gif border=0 >',
						CAPTIONPADDING,4,
						CAPTION,'View requested anaesthetics',
						MIDX,0, MIDY,0,
						STATUS,'View requested anaesthetics');
}

function addslashes(str)
{
	 return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");
}

function populate_anesthesia_procedure(listID,details,details_len)
{

		document.getElementById('anesthesia_cnt').value = details_len;
		document.getElementById('anesthesia_procedure_list_body').innerHTML = "";
		var rowSrc = document.getElementById('anesthesia_procedure_list_body').innerHTML;

		for(i=0;i<details_len;i++)
		{
			var text="";
			var anesth = details[i]['srvname'];
			for(j=0;j<details[i]['anesthetics_count'];j++)
			{

				text+=""+anesth[j]+",";
			}
			rowSrc += "<tr class='wardlistrow' id='row"+details[i]['id']+"'>"+
							"<td width='5%' align='center'><img src='../../../images/btn_delitem.gif' style='cursor: pointer;' onclick='remove_anesthesia_procedure(\""+listID+"\",\""+details[i]['id']+"\");'/></td>"+
							"<td width='30%' align='center'>"+details[i]['name_category']+" ["+details[i]['name_specific']+"]</td>"+
							"<td width='20%' align='center'>"+details[i]['time_begun']+" "+details[i]['tb_meridian']+"</td>"+
							"<td width='20%' align='center'>"+details[i]['time_ended']+" "+details[i]['te_meridian']+"</td>"+
							"<td width='20%' align='center' id='rowtext"+details[i]['id']+"'>"+text+"</td>"+
							"<td width='10%' align='center' id='rowimg"+details[i]['id']+"'><img src='../../../images/cashier_view_red.gif' style='cursor: pointer;' onclick='view_anesthetic_tray(\""+details[i]['srvqty']+"\",\""+details[i]['anesthetics']+"\",\""+details[i]['srvcash']+"\",\""+details[i]['srvcharge']+"\",\""+details[i]['id']+"\");'/></td>"+
							"<input type='hidden' id='anesthesia_id[]' name='anesthesia_id[]' value='"+details[i]['id']+"'/>"+
							"<input type='hidden' id='anesthesia_category[]' name='anesthesia_category[]' value='"+details[i]['name_category']+"'/>"+
							"<input type='hidden' id='anesthesia_specific[]' name='anesthesia_specific[]' value='"+details[i]['name_specific']+"'/>"+
							"<input type='hidden' id='anesthesia_timestart[]' name='anesthesia_timestart[]' value='"+details[i]['time_begun']+"'/>"+
							"<input type='hidden' id='anesthesia_timeend[]' name='anesthesia_timeend[]' value='"+details[i]['time_ended']+"'/>"+
							"<input type='hidden' id='anesthesia_ts_meridian[]' name='anesthesia_ts_meridian[]' value='"+details[i]['tb_meridian']+"'/>"+
							"<input type='hidden' id='anesthesia_te_meridian[]' name='anesthesia_te_meridian[]' value='"+details[i]['te_meridian']+"'/>"+
							"<input type='hidden' id='anesthetic_id[]' name='anesthetic_id[]' value='"+details[i]['anesthetics']+"'/>"+
							"<input type='hidden' id='anesthetic_qty[]' name='anesthetic_qty[]' value='"+details[i]['srvqty']+"'/>"+
							"<input type='hidden' id='anesthetic_pcash[]' name='anesthetic_pcash[]' value='"+details[i]['srvcash']+"'/>"+
							"<input type='hidden' id='anesthetic_pcharge[]' name='anesthetic_pcharge[]' value='"+details[i]['srvcharge']+"'/>"+
							"</tr>";
		}
		document.getElementById('anesthesia_procedure_list_body').innerHTML = rowSrc;
}

//end cHA

//added code by angelo 09.06.2010
//start here
function isNumberKey(evt){
				 var charCode = (evt.which) ? evt.which : event.keyCode
				 if (charCode > 31 && (charCode < 48 || charCode > 57))
						return false;

				 return true;
}

function isValidTime(caseTime){
	switch(caseTime){
		case 1:
				 setFormatTime($("post_time_started"),"pts_meridian");
				 break;
		case 2:
				 setFormatTime($("post_time_finished"),"ptf_meridian");
				 break;
		default:
		break;
	}
}

//formatting time
function setFormatTime(thisTime,AMPM){

	var strTime = thisTime.value;
	var stime = strTime.substring(0,5);
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	var jtime = "";

//		trimString(thisTime);

	if (thisTime.value==''){
		seg_validTime=false;
		return;
	}

	stime = stime.replace(':', '');

	if (stime.length == 3){
		hour = stime.substring(0,1);
		minute = stime.substring(1,3);
	} else if (stime.length == 4){
		hour = stime.substring(0,2);
		minute = stime.substring(2,4);
	}else{
		alert("Invalid time format.");
		thisTime.value = "";
		seg_validTime=false;
		thisTime.focus();
		return;
	}

	jtime = hour + ":" + minute;
//		js_setTime(jtime);

	if (hour==0){
		 hour = 12;
		 document.getElementById(AMPM).value = "AM";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "PM";
	}

	ftime =  hour + ":" + minute;

	if(!ftime.match(f1) && !ftime.match(f2)){
		thisTime.value = "";
		alert("Invalid time format.");
		seg_validTime=false;
		thisTime.focus();
	}else{
		thisTime.value = ftime;
		seg_validTime=true;
	}
}// end of function setFormatTime

function confirming(){
	var opt = confirm("Are you sure you want to process?");
	if(opt){
		if(validate())
			document.main_or_form.submit();
	}


}
//end here




xajax_populatePersonnel(<?=$refno? $refno:0?>,7,'surgeon_list','surgeon[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,8,'assistant_surgeon_list','surgeon_assist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,12,'anesthesiologist_list','anesthesiologist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,9,'scrub_nurse_list','nurse_scrub[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,10,'circulating_nurse_list','nurse_rotating[]');
xajax_populateOpsCodeListByRefNo(<?=$refno? $refno:0?>);
xajax_populate_or_main_anesthesia(<?=$or_main_info['or_main_refno']?>);

xajax_populate_order('<?=$order_refno?>', J('discountid').value);
xajax_populate_sponge_list(<?=$or_main_info['or_main_refno']?>);

xajax_populate_equipment_order(<?=$equipment_refno?>, 'equipment_list');
xajax_populate_equipment_oxygen(<?=$equipment_refno?>, 'equipment_list');
xajax_is_already_billed('<?=$encounter_nr?>');
document.body.onLoad = refreshDiscountSupplies();
document.body.onLoad = update_total();
</script>
