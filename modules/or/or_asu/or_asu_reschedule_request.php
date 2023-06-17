<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path.'modules/or/ajax/order.common.php'); //load the xajax module
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_oproom.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions

$breakfile='or_asu_reschedule.php'.URL_APPEND;

$smarty = new Smarty_Care('or_main_request');

#$breakfile=$root_path.'modules/or/or_main/or_main_approve.php'.URL_APPEND;
$smarty->assign('breakfile', $breakfile);
$smarty->assign('check_date_string', $check_date_string);
$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');

/*$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
										,$xajax->printJavascript($root_path.'classes/xajax_0.5')); */

$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
													,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js">'
													, '<script>var J = jQuery.noConflict();</script>'
													, '<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
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
													, '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'
										,$xajax->printJavascript($root_path.'classes/xajax_0.5'));

$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
													, '<script>var J = jQuery.noConflict();</script>'
													, '<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
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
													, '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>');
$smarty->assign('javascript_array', $javascript_array);


$seg_ops = new SegOps();
$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
#echo "encounter_nr= ".$encounter_nr."<br>";
#echo "or_main_refno= ".$_POST['or_main_refno'];
$nr = $seg_ops->getOpRequestNrByRefNo($refno);
#echo $nr;   //$nr from care_encounter_op
$or_main_info = $seg_ops->get_or_main_basic_info($refno);
#print_r($or_main_info);
$or_main_refno = $or_main_info['or_main_refno'];
#echo "<br>";
$cur_bed_nr = $or_main_info['bed_nr'];
if($or_main_info['room_nr']){

	$_POST['or_room'] = $or_main_info['room_nr'];
	$_POST['cur_bed_nr'] = $or_main_info['bed_nr'];
	$_POST['date_operation'] = $or_main_info['date_operation'];
	$cur_bed_nr = $or_main_info['bed_nr'];
	 //echo "bed= ".$_POST['cur_bed_nr'];
	//echo "<script>setBed()</script>";
	//echo "<script language=javascript>checkdetails()</script>";
}

if (isset($_POST['submitted'])) {

	if($_POST['stat']=='1'){
		$status = 'resched';
	}else{
		$status = '';
	}
	//echo "stat= ".$_POST['stat']."<br>";
	//echo "op date= ".$_POST['or_operation_date'];

	if(!$_POST['remarks'])
		$reason = "";
	else
		$reason = $_POST['reamrks'];

	$data = array('room_nr'=> $_POST['or_room'],
								'bed_nr'=> $_POST['cur_bed_nr'],
								'final_date_operation'=> date('Y-m-d', strtotime($_POST['or_operation_date'])),
								'length_op'=> $_POST['length_operation']
					);

	$op = $seg_ops->update_or_main_status($_POST['or_main_refno'], $reason, $status);
	$save = $seg_ops->update_or_main_approval($_POST['or_main_refno'], $data);

	if ($op == 'insert' && $status == 'approved' && $save)
		$smarty->assign('sysInfoMessage','OR Main request was successfully approved.');
	elseif ($op == 'update' && $status == 'approved' && $save)
		$smarty->assign('sysInfoMessage', 'OR Main Request approval reason successfully updated.');
	elseif ($op == 'insert' && $status = 'disapproved' && $save)
		$smarty->assign('sysInfoMessage', 'OR Main Request was successfully disapproved.');
	elseif ($op == 'update' && $status == 'disapproved' && $save)
		$smarty->assign('sysInfoMessage', 'OR Main Request disapproval reason successfully updated.');

}

if ($seg_ops->encOpsNrHasOpsServ($nr)) {
	$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
	$or_main_info = $seg_ops->get_or_main_basic_info($refno);
}



$seg_department = new Department();
$seg_room = new OPRoom();
//$dept_nr = $basic_info['dept_nr'];
$or_main_info = $seg_ops->get_or_main_basic_info($refno);
$dept_nr = $or_main_info['dept_nr'];
//$encounter_nr = $or_main_info['encounter_nr'];
//echo "dept_nr= ".$dept_nr;
$op_nr = $basic_info['op_nr'];
//$op_room = $basic_info['op_room'];
$department = $seg_department->FormalName($dept_nr);
//$operating_room = $seg_room->get_or_name($op_room);
$operating_room = $seg_ops->get_or_room($dept_nr);
//print_r($operating_room);
$length = $or_main_info['length_op'];
$final = $or_main_info['final_date_operation'];

#---------setup options for Motor Vehicle Driven----------#
//$listRoom=array();
$listRoom[0]="-Select Room-";
	if(is_object($operating_room)){
		while($roomInfo = $operating_room->FetchRow()){
			$listRoom[$roomInfo['room_nr']] = $roomInfo['room_name'];
		}
	}
#print_r($listRoom);

/** Form tags **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate(this)"> ');
$smarty->assign('form_end', '</form>');
/*$smarty->assign('form_approve', '<form name="approve_form" method="POST" action="'.$_SERVER['PHP_SELF'].'"
																	onsubmit="return validate(\'approve_or_main_request\', \'approval\')">');
$smarty->assign('form_disapprove', '<form name="disapprove_form" method="POST" action="'.$_SERVER['PHP_SELF'].'"
																		onsubmit="return validate(\'disapprove_or_main_request\', \'disapproval\')">');
																		 */
/** End Form tags **/

/** Info under fieldset "Request Details" **/
$smarty->assign('required_mark', '<em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em>');
$smarty->assign('or_request_department', $department);
//$smarty->assign('or_op_room', $operating_room['info']);
$transaction = array('1' => 'Cash', '0' => 'Charge');
#$smarty->assign('or_transaction_type', $transaction[$basic_info['is_cash']]);
$smarty->assign('or_transaction_type', ($or_main_info['trans_type']==0)? 'Charge' : 'Cash');
$smarty->assign('or_request_priority', $or_main_info['request_priority']);


#$request_date = date('F d, Y h:ia', strtotime($basic_info['request_date'].' '.$basic_info['request_time']));
$request_date = date('F d, Y h:ia', strtotime($or_main_info['date_request']));
$smarty->assign('or_request_date', $request_date);
$smarty->assign('or_consent_signed', ($or_main_info['consent_signed'] == 1) ? 'Yes' : 'No');

$case = array('pay_elective'=>'Pay - Elective', 'pay_stat'=>'Pay - Stat',
							'service_elective'=>'Service - Elective', 'service_stat'=>'Service - Stat');
$smarty->assign('or_request_case', $case[$or_main_info['or_case']]);

/** End **/

/** Info under fieldset "Patient Information" **/


#$seg_person = new Person($basic_info['pid']);
#$pid = $_GET['pid'];
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
#$operation_date = date('F d, Y h:ia', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));
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
														$time_operation = '11:00am - 12:00nn';
														break;
		case '12:00 - 01:00':
														$time_operation = '12:00nn - 01:00pm';
														break;
		case '01:00 - 02:00':
														$time_operation = '01:00pm - 02:00pm';
														break;
		case '02:00 - 03:00':
														$time_operation = '02:00pm - 03:00pm';
														break;
		case '03:00 - 04:00':
														$time_operation = '03:00pm - 04:00pm';
														break;
		case '04:00 - 05:00':
														$time_operation = '04:00pm - 05:00pm';
														break;
}

$operation_date = date('F d, Y', strtotime($or_main_info['date_operation']));
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

 /** End **/

 /** Form elements under fieldset "Schedule Details" **/
$query = "SELECT sp.or_no, spr.ref_source FROM seg_pay AS sp
					LEFT JOIN seg_pay_request AS spr ON spr.or_no = sp.or_no
					WHERE sp.encounter_nr = '$encounter_nr'
					AND spr.ref_source IN ('OR')
					ORDER BY or_date DESC; ";
$result = $db->Execute($query);
$row = $result->FetchRow();
$receipt = $row['or_no'];
#echo "receipt= ".$receipt;

$smarty->assign('or_receipt', '<input type="text" name="or_receipt" id="or_receipt" value="'.$receipt.'" readonly="readonly" />');
$smarty->assign('or_room', $listRoom);
$smarty->assign('or_room_selected', $_POST['or_room']);
$smarty->assign('remarks', '<textarea name="remarks" id="remarks">'.$_POST['remarks'].'</textarea>');
#$smarty->assign('length_operation', '<input type="text" name="length_operation" id="length_operation" value="'.$_POST['length_operation'].'" />');
$smarty->assign('length_operation', '<input type="text" name="length_operation" id="length_operation" value="'.$length.'" />');

//$operation_date_display = isset($_POST['or_operation_date']) ? date('F d, Y h:ia', strtotime($_POST['or_operation_date'])) : date('F d, Y h:ia');
$operation_date_display = isset($_POST['or_operation_date']);
//$operation_date = isset($_POST['or_operation_date']) ? date('Y-m-d H:i', strtotime($_POST['or_operation_date'])) : date('Y-m-d H:i');
$operation_date = isset($_POST['or_operation_date']);

//$smarty->assign('final_operation_date_display', '<div id="or_operation_date_display" class="date_display">'.$operation_date_display.'</div>');
$smarty->assign('final_operation_date', '<input type="text" name="or_operation_date" id="or_operation_date" value="'.$final.'" checkDate(); />');
$smarty->assign('final_operation_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_operation_dt_picker" class="date_time_picker" onClick="openScheduleView()" />');

/*$smarty->assign('final_operation_date_display', '<div id="or_operation_date_display" class="date_display">'.$operation_date_display.'</div>');
$smarty->assign('final_operation_date', '<input type="hidden" name="or_operation_date" id="or_operation_date" value="'.$_POST['or_operation_date'].'" checkDate();/>');
$smarty->assign('final_operation_dt_picker', '<img src="'.$root_path.'images/pickdate.png" id="final_operation_dt_picker" class="date_time_picker" onClick="openScheduleView()" />');
#$smarty->assign('final_operation_dt_picker', '<button class="segButton" style="cursor:pointer" onClick=""><img src="'.$root_path.'gui/img/common/default/calculator_delete.png"/>Pick Date</button>');
#$smarty->assign('final_operation_calendar_script', setup_calendar('or_operation_date_display', 'final_operation_date', 'final_operation_dt_picker'));
																													*/
 /** End **/

/** Form elements under fieldset "Other Details" **/
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
#print_r(explode(",",$or_main_info['special_requirements']));

/** End **/

/** Other form elements **/
$current_status = $or_main_info['status'];
/*if ($current_status == 'approved') {
	//$smarty->assign('or_main_disapprove', '<a href="javascript:void(0)" onclick="show_popup_disapprove('.$refno.')" id="or_main_disapprove"></a>');
	$smarty->assign('or_main_disapprove', '<input name="or_main_disapprove" type="submit" value="" id="or_main_disapprove" />');


	$operation = 'Disapprove/Schedule';
	#$smarty->assign('or_main_schedule', '<a href="../../or/or_main/or_main_schedule_request.php?refno='.$refno.'" id="or_main_schedule_approve"></a>');
	$smarty->assign('or_main_schedule', '<a href="javascript:void(0)" onclick="show_popup_disapprove('.$refno.')" id="or_main_disapprove"></a>');

}
elseif ($current_status == 'disapproved') {
	//$smarty->assign('or_main_approve', '<a href="javascript:void(0)"  onclick="checkdetails(); show_popup_approve('.$refno.')" id="or_main_approve"></a>');
	//$smarty->assign('or_main_approve', '<input name="approve_request" type="submit" value="" id="or_approve_submit" />');
	$smarty->assign('or_main_approve', '<input name="or_main_approve" type="submit" value="" id="or_main_approve" />');
	$operation = 'Approve';
}
else {
	$operation = 'Approve/Disapprove';
	$smarty->assign('or_main_disapprove', '<input name="or_main_disapprove" type="submit" value="" onclick="setStat(2);" id="or_main_disapprove" />');
	//$smarty->assign('or_main_approve', '<a href="javascript:void(0)"  onclick="show_popup_approve('.$refno.')" id="or_main_approve"></a>');
	$smarty->assign('or_main_approve', '<input name="or_main_approve" type="submit" value="" onclick="setStat(1);" id="or_main_approve" />');
}
									 */
$smarty->assign('or_main_schedule_approve', '<input name="or_main_schedule_approve" type="submit" value="" onclick="setStat(1);" id="or_main_approve" />');
//$smarty->assign('or_main_schedule_approve', '<input name="or_main_schedule_approve" type="submit" value="" onclick="setStat(1);" id="or_main_schedule_approve" />');
$smarty->assign('sToolbarTitle',"Operating Room Main :: ".$operation." Request for Surgery"); //Assign a toolbar title
$smarty->assign('or_main_cancel', '<a href="'.$breakfile.'" id="or_main_cancel"></a>');
$smarty->assign('error_msg', '<span id="error_form_input"></span>');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('stat', '<input type="hidden" value="" name="stat" id="stat" />');
$smarty->assign('op_nr', '<input type="hidden" name="op_nr" value="'.$op_nr.'" />');
//$smarty->assign('op_room', '<input type="hidden" name="op_room" value="'.$op_room.'" />');
$smarty->assign('dept_nr', '<input type="hidden" name="dept_nr" value="'.$dept_nr.'" />');
$smarty->assign('current_bed_nr', '<input type="hidden" name="cur_bed_nr" id="cur_bed_nr" value="'.$cur_bed_nr.'" />');
$smarty->assign('refno', '<input type="hidden" name="refno" value="'.$refno.'" />');
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" value="'.$encounter_nr.'" />');
$smarty->assign('or_request_nr', '<input type="hidden" name="or_request_nr" value="'.$nr.'" />');
$smarty->assign('or_main_refno', '<input type="hidden" name="or_main_refno" value="'.$or_main_refno.'" />');
$smarty->assign('date_operation', '<input type="hidden" name="date_operation" id="date_operation" value="'.$or_main_info['date_operation'].'" />');

#Added by Cherry 05-13-10
$smarty->assign('pid', '<input type="hidden" name="pid" value="'.$pid.'" />');
/** End **/

$sBed .=
					'<table id="tbBedRoom" border="0" cellpadding="0" cellspacing="2" width="100%">
					 </table>
					';
//echo "bed = ".$sBed;
$smarty->assign('sbed',$sBed);

/**Form Elements in Popup Form Reason for Approval **/
$smarty->assign('close_approve', '<a href="#" id="close_approve" class="jqmClose"></a>');
$smarty->assign('approve_reason_label', '<label>Reason for Approval: <em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em></label>');
$smarty->assign('approve_reason', '<textarea name="reason" cols="43" rows="5" ></textarea>');
$smarty->assign('or_approve_submit', '<input name="approve_request" type="submit" value="" id="or_approve_submit" />');
$smarty->assign('or_approve_cancel', '<a href="#" id="or_approve_cancel" class="jqmClose"></a>');
$smarty->assign('approve_label_msg', 'This request cannot be approved unless a reason for approval is provided.');
/** End **/

/**Form Elements in Popup Form Reason for Disapproval **/
$smarty->assign('close_disapprove', '<a href="#" id="close_disapprove" class="jqmClose"></a>');
$smarty->assign('disapprove_reason_label', '<label>Reason for Disapproval: <em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em></label>');
$smarty->assign('disapprove_reason', '<textarea name="reason" cols="43" rows="5" ></textarea>');
$smarty->assign('or_disapprove_submit', '<input name="disapprove_request" type="submit" value="" id="or_disapprove_submit" />');
$smarty->assign('or_disapprove_cancel', '<a href="#" id="or_disapprove_cancel" class="jqmClose"></a>');

/** End **/

$smarty->assign('sMainBlockIncludeFile','or/or_asu_reschedule_request.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

function setup_calendar($display_area, $input_field, $button) {
	global $root_path;
	$calendar_script =
		'<script type="text/javascript">
			 Calendar.setup ({
				 displayArea : "'.$display_area.'",
				 inputField : "'.$input_field.'",
				 ifFormat : "%Y-%m-%d",
				 daFormat : "%B %e, %Y",
				 showsTime : false,
				 button : "'.$button.'",
				 singleClick : true,
				 step : 1
			 });
			</script>';
	return $calendar_script;

function setup_calendar2($display_area, $input_field, $button){
	 global $root_path;
	 $calendar_script =
		'<script type="text/javascript">
			 Calendar.setup ({
				 displayArea : "'.$display_area.'",
				 inputField : "'.$input_field.'",
				 ifFormat : "%Y-%m-%d",
				 daFormat : "%B %e, %Y",
				 showsTime : false,
				 button : "'.$button.'",
				 singleClick : true,
				 onClose: function(cal) { cal.hide();checkDate();},
				 step : 1
			 });
			</script>';
		return $calendar_script;
}
}

?>

<script>
/** Approve OR Request **/
/*$('#approve_or_main_request').jqm({
overlay: 80,
onShow: function(h) {
	h.w.fadeIn(1000, function(){h.o.show();});
},
onHide: function(h){
	h.w.fadeOut(1000, function(){h.o.remove();});
}});                     */

function show_popup_approve(refno) {
	$('#approve_or_main_request').jqmShow();
}

function openScheduleView() {
			//added by cha, 11-18-2010
	var dept = "<?php echo $or_main_info['dept_nr']?>";
	var priority = "<?php echo $or_main_info['request_priority']?>";
	overlib(
				OLiframeContent('<?=$root_path?>modules/or/request/seg_or_schedules.php?m=orasu&dept='+dept+'&priority='+priority, 800, 400,
					'schedView', 0, 'auto'),
				WIDTH, 800, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src="<?=$root_path?>images/close_red.gif" border=0 >',
				CAPTIONPADDING,2, MIDX,0, MIDY,0,
				CAPTION, 'OR Schedule Viewer',
				STATUS, 'OR Schedule Viewer'
			);
			return false;
		}

function checkdetails(){
	if($('or_receipt').value == null || $('or_receipt').value == '')
		alert('No Official Receipt Yet');
}

	function checkDate(){
				var d = document.main_or_form;
				//alert(document.getElementById('or_operation_date_display').value);
				/*if (document.getElementById('or_operation_date_display').value=='' || document.getElementById('or_operation_date_display').value==undefined){
						alert('true');
						document.getElementById('op_time').style.display = 'none';
				}else{
						alert('false')
						 document.getElementById('op_time').style.display = '';
				}*/
				 //alert(document.getElementById('or_operation_date').value);
				/* if (d.or_operation_date.value==''){
						//alert('true');
						document.getElementById('op_time').style.display = 'none';
				}else{
						//alert('false')
						 document.getElementById('op_time').style.display = '';
				} */


		}

function preset(){
	//alert('HOY GISING!');
	var room = document.main_or_form.or_room.value;
	var bed = document.main_or_form.cur_bed_nr.value;
	var date = document.main_or_form.date_operation.value;
	var refno = document.main_or_form.refno.value;

	xajax_getBeds(room, date, refno);
}

function setStat(num){
	//alert('num= '+num);

	document.getElementById('stat').value = num;
	var churva = document.getElementById('stat').value;
	//alert('churva= '+churva);
	/*if(num=='1'){
		confirm('Are you sure you want to approve this request?');
	}else if(num=='2'){
		confirm('Are you sure you want to disapprove this request?');
	}*/

	/*$('submitted').value = true;
	sub = $('submitted').value;
	alert($('sub= '+sub);  */
}

/*$('#disapprove_or_main_request').jqm({
overlay: 80,
onShow: function(h) {
	h.w.fadeIn(1000, function(){h.o.show();});
},
onHide: function(h){
	h.w.fadeOut(1000, function(){h.o.remove();});
}});     */

function setBed(){
	//alert('setting bed...');
	var room = document.main_or_form.or_room.value;
	var date = document.main_or_form.date_operation.value;
	var refno = document.main_or_form.refno.value;
	//alert('date= '+date);
	xajax_getBeds(room,date,refno);
	//alert('setting bed...'+room);
}

//added by VAN 08-20-08
function getCurrentBed(current_value){
	 $('cur_bed_nr').value = current_value;
	var trylang = $('cur_bed_nr').value;
	//document.getElementById('cur_bed_nr').value = current_value;
	//chckRoomModeStatus();
	//alert('bedding= '+trylang);
}

//Added by Cherry 09-12-10
function ajxGetBedRoom(nr_bed, patient, patient2, occupied_nr){
	//alert('ajxGetBedRoom, nr_bed= '+nr_bed);
	var d = document.main_or_form;
	var aWardNr = d.current_ward_nr;
	var aRoomNr = d.or_room;
	var dBody = document.getElementById('tbBedRoom');
	var gender, sex, occupy;
	//var room = "<?php echo $current_room_nr; ?>";
	var room = d.or_room.value;
	//var bed = $('cur_bed_nr').value;
	//var bed = document.getElementById('cur_bed_nr').value;
	var bed = d.cur_bed_nr.value;
	//alert("bed document = "+document.getElementById('bed_nr').value);
	//alert('room = '+room);
	//alert('bed = '+bed);
	if (dBody) {

		var dRows, newRowSrc, i;
		dRows=dBody.getElementsByTagName("tr");
		//alert('drows= '+dRows);
		// clear the table
		dBody.innerHTML = "";
		//alert("bed = "+document.getElementById('bed_nr').value);
		//$('prev_bed_nr').value = document.getElementById('bed_nr').value;
		//$('cur_bed_nr').value = document.getElementById('bed_nr').value;
		//alert('nr_bed= '+nr_bed);
		if (nr_bed!=0) {
				//alert('inside nr_bed');
			for (i=1; i <= nr_bed; i++){
				//alert(i+" < = "+document.getElementById('cur_bed_nr').value);
				//alert(i+" < = "+d.cur_bed_nr.value);
				if ((document.getElementById('cur_bed_nr').value==i)&&(aRoomNr.value==room))
					occupy = '<img src="<?=$root_path?>gui/img/common/default/check-r.gif" border="0">';
				else
					occupy = "";

				/*if (patient[aRoomNr.value][i]=='m'){
					//sex = 'Male Patient';
					sex = '<img src="../../gui/img/common/default/spm.gif" border="0"> Occupied';
					if ((document.getElementById('cur_bed_nr').value==i)&&(aRoomNr.value==room))
						vacant = '<input type="radio" id="current_bed_nr" checked name="current_bed_nr" onClick="getCurrentBed(this.value);" value="'+i+'"">';
					else
						vacant = '<img src="../../gui/img/common/default/padlock.gif" border="0">';

				}else if (patient[aRoomNr.value][i]=='f'){
					//sex = 'Female Patient';
					sex = '<img src="../../gui/img/common/default/spm.gif" border="0"> Occupied';
					if ((document.getElementById('bed_nr').value==i)&&(aRoomNr.value==room))
						vacant = '<input type="radio" id="current_bed_nr" checked name="current_bed_nr" onClick="getCurrentBed(this.value);" value="'+i+'"">';
					else
						vacant = '<img src="../../gui/img/common/default/padlock.gif" border="0">';

				}                  */
				 //alert('meow? '+patient2[i-1]);
				if (patient[aRoomNr.value][i]==patient2[i-1] && occupied_nr<=i){
					//sex = 'Male Patient';
					sex = '<img src="<?=$root_path?>/gui/img/common/default/patdata.gif" border="0"> Occupied';
					if ((document.getElementById('cur_bed_nr').value==i)&&(aRoomNr.value==room))
						vacant = '<input type="radio" id="current_bed_nr" checked name="current_bed_nr" onClick="getCurrentBed(this.value);" value="'+i+'"">';
					else
						vacant = '<img src="<?=$root_path?>/gui/img/common/default/padlock.gif" border="0">';

				}

				else{
					sex = '<img src="<?=$root_path?>/gui/img/common/default/patdata.gif" border="0"> Vacant';
					vacant = '<input type="radio" id="current_bed_nr" name="current_bed_nr" onClick="getCurrentBed(this.value);" value="'+i+'"">';
				}

				newRowSrc = '<tr>' +
									/*'<td align="left">'+String.fromCharCode(64+i)+'</td>'+ */
									'<td align="left" width="1%">'+i+'</td>'+
									'<td align="left" width="10%">'+sex+'</td>'+
									'<td align="center" width="25%">'+vacant+'</td>'+
									'<td align="left" width="*">'+occupy+'</td>'+
								'</tr>';
				dBody.innerHTML += newRowSrc;
				//alert(dBody.innerHTML);
			}
		}else{
			dBody.innerHTML = "";
		}
	}
	/*$sBed .=
					'<table id="tbBedRoom" border="0" cellpadding="0" cellspacing="2" width="40%">
					 </table>
					';    */
 //echo "bed = ".$sBed;
 //$smarty->assign('sbed',$sBed);
	//$objResponse->assign();
	//alert('nr_bed = '+nr_bed);
}

function show_popup_disapprove(refno) {
	$('#disapprove_or_main_request').jqmShow();

}

function hide_popup() {
	$('#cancel_or_main_request').jqmHide();
}

function validate(e) {
	var d = document.main_or_form;
	var status = $('stat').value;
	var room = d.or_room.value;
	var receipt = d.or_receipt.value;
	var bed = d.cur_bed_nr.value;
	var remarks = d.remarks.value;
	var length = d.length_operation.value;
	var transtype = $('transtype').innerHTML;
	//alert('transtype= '+transtype);
	//alert('length= '+length);
	/*alert('room= '+room);
	alert('receipt= '+receipt);
	alert('bed= '+bed); */
	//alert('bed in validate'+bed);
		/* if((document.getElementById('or_receipt').value=='' || document.getElementById('or_receipt').value==null)
			&& (document.getElementById('or_room_selected').value=='' || document.getElementById('or_room_selected').value==null)
			&& (document.getElementById('cur_bed_nr').value=='' || document.getElementById('cur_bed_nr').value==null)
			&& (document.getElementById('remarks').value=='' || document.getElementById('remarks').value==null)
			){
				alert('Please fill in all the necessary fields');
				return false;
		 }else{
				 if(document.getElementById('or_receipt').value=='' || document.getElementById('or_receipt').value==null){
				alert('No Official Receipt Yet!');
				return false;
				}

		 }                  */
			if (!receipt){
				if (transtype=='Cash'){
					alert('No Official Receipt Yet!');
					return false;
				}
		 }else if((length == '') && (room='0') || (bed=='')){
			 alert('Please fill-in all necessary fields');
			 return false;
		 }else{
			 if(status=='2' && (remarks=='' || remarks==null)){
					alert('Please provide the reason for disapproving the request');
					return false;
			 }else{
				 return true;
			 }
		 }

}

/*function validate(domain, operation) {

	var array_elements = [ {field: $("textarea[@name='reason']", $('#'+domain)),
												 field_value: $("textarea[@name='reason']", $('#'+domain)).val(),
												 msg: 'Please provide the reason for ' + operation,
												 msg_dest: $('#error_form_input', $('#'+domain))
												 }
												 ];
	var errors = new Array();
	for (var i=0; i<array_elements.length; i++) {
		if (array_elements[i].field_value == '' || !array_elements[i].field_value) {
			array_elements[i].msg_dest.html(array_elements[i].msg);
			errors.push(array_elements[i].field);
			if (array_elements[i].is_textfield) {
				array_elements[i].field.addClass('error_field');
			}
		}
		else {
			array_elements[i].msg_dest.html('');
			array_elements[i].field.removeClass('error_field');
		}
	}
	if (errors.length > 0) {
		errors[0].focus();
		return false;
	}
	else {
		return true;
	}
}  */
</script>
