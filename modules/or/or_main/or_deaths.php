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

$breakfile='select_or_deaths.php'.URL_APPEND;

$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room Main :: OR Deaths"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room Main :: OR Deaths");

$smarty->assign('breakfile', $breakfile);
$smarty->assign('check_date_string', $check_date_string);
$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');
$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
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
$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$nr = $seg_ops->getOpRequestNrByRefNo($refno);

if (isset($_POST['submitted'])) {

	$data = array(
								'or_main_refno' => $_POST['or_main_refno'],
								'date_time_of_death' => $_POST['death_date'],
								'cause_of_death' => $_POST['cause_of_death'],
								'patient_classification' => $_POST['patient_classification'],
								'death_time_range' => $_POST['death_time_range']
					 );
	if ($seg_ops->update_or_deaths($data)) {
		 $smarty->assign('sysInfoMessage','OR death successfully saved.');
		 $mode = 'edit';
	}
	else {
			$smarty->assign('sysErrorMessage', 'OR Death record was not successfully saved.');
	}
}


if ($seg_ops->encOpsNrHasOpsServ($nr)) {
	$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
	$or_main_info = $seg_ops->get_or_main_basic_info($refno);
}

$seg_department = new Department();
$seg_encounter = new Encounter();
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
$smarty->assign('patient_name', $patient_name);
$smarty->assign('patient_gender', $patient_gender);
$smarty->assign('patient_age', $patient_age);
$smarty->assign('patient_address', $patient_address);
/** End **/

/** Form elements under fieldset "Pre-operation Details" **/
$info_patient = $seg_encounter->getEncounterInfo($encounter_nr);
$patient_diagnosis = $info_patient['er_opd_diagnosis'];

$or_est_op_length = $or_main_info['est_length_op'];
$pre_operative_diagnosis = $or_main_info['pre_op_diagnosis'];
$operation_procedure = $or_main_info['or_procedure'];
$smarty->assign('or_est_op_length', $or_est_op_length);
#$smarty->assign('pre_operative_diagnosis', $pre_operative_diagnosis);
$smarty->assign('pre_operative_diagnosis', $patient_diagnosis);
$smarty->assign('operation_procedure', $operation_procedure);
$classification = array('clean'=>'Clean', 'contaminated'=>'Contaminated',
												'wound_dehiscence'=>'Wound Dehiscence',
												'clean_contaminated'=>'Clean/Contamianted',
												'dirty_infected'=>'Dirty/Infected');
$smarty->assign('or_case_classification', $classification[$or_main_info['case_classification']]);

 /** End **/

/** Form elements under fieldset "Other Details" **/
$requirements = array('cp_clearance'=>'CP Clearance', 'pulmo_clearance'=>'Pulmo Clearance', 'consent'=>'Consent',
															 'pedia_clearance'=>'Pedia Clearance', 'others'=>'Others');
$or_requirements = explode(",", $or_main_info['special_requirements']);

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
/** End **/


/** OR Death Details **/

if ($mode == 'edit') {
	$death_details = $seg_ops->get_death_details($or_main_info['or_main_refno']);
}


$death_date_display = ($death_details['date_time_of_death'] == '') ? date('F d, Y h:ia') : date('F d, Y h:ia', strtotime($death_details['date_time_of_death']));
$death_date = ($death_details['date_time_of_death'] == '') ? date('Y-m-d H:i') : date('Y-m-d H:i', strtotime($death_details['date_time_of_death']));
$smarty->assign('death_date_display', '<div id="death_date_display" class="date_display">'.$death_date_display.'</div>');
$smarty->assign('death_date_value', '<input type="hidden" name="death_date" id="death_date" value="'.$death_date.'" />');
$smarty->assign('death_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="death_dt_picker" class="date_time_picker" />');
$smarty->assign('death_calendar_script', setup_calendar('death_date_display', 'death_date', 'death_dt_picker'));

$smarty->assign('cause_of_death', '<textarea name="cause_of_death">'.$death_details['cause_of_death'].'</textarea>');
$smarty->assign('death_time_range', array(0=>'Under 48 hours', 1=>'Beyond 48 hours'));
$smarty->assign('patient_classification', array(0=>'Pedia', 1=>'Adult'));
$patient_classification_selected = ($death_details['patient_classification'] == '0') ? 0 : 1;
$death_time_range_selected = ($death_details['death_time_range'] == '0') ? 0 : 1;
$smarty->assign('patient_classification_selected', $patient_classification_selected);
$smarty->assign('death_time_range_selected', $death_time_range_selected);


/** End: OR Death Deatils **/


/** Other form elements **/
$smarty->assign('submit_or_death', '<input type="submit" id="or_main_submit" value="" />');
$smarty->assign('cancel_or_death', '<a href="'.$breakfile.'" id="schedule_cancel"></a>');

$smarty->assign('refno', '<input type="hidden" name="refno" value="'.$refno.'" />');
$smarty->assign('or_main_refno', '<input type="hidden" name="or_main_refno" value="'.$or_main_info['or_main_refno'].'" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('mode', '<input type="hidden" value="'.$mode.'" name="mode" />');
/** End **/

$smarty->assign('sMainBlockIncludeFile','or/or_deaths.tpl'); //Assign the or_main template to the frameset
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
?>

<script>
function toggle_details() {
	J('#request_details').slideToggle('100');
}
</script>