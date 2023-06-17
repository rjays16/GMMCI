<?php
/** Modified: February 27, 2009
* Changes was made to reconcile with Maramag's OR Request
* As specified by Rodolfzon Orcajo
*/
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
include_once($root_path."include/care_api_classes/class_order.php");  //load the SegOrder class
require($root_path.'modules/or/ajax/op-request-new.common.php'); //load the xajax module
require($root_path.'include/care_api_classes/class_department.php'); //load the department class
require($root_path.'include/care_api_classes/class_oproom.php'); //load the department class
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_personell.php'); //added by Cherry 04-28-10
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_request_source.php');

define('NO_2LEVEL_CHK',1);

$seg_ward = new Ward();
$seg_department = new Department();
$seg_personell = new Personell(); //added by Cherry 04-28-10
$seg_encounter = new Encounter();
$seg_room = new OPRoom();
$dept_nr = isset($_GET['dept_nr']) ? $_GET['dept_nr'] : $_POST['dept_nr'];
$op_nr = isset($_GET['op_nr']) ? $_GET['op_nr'] : $_POST['op_nr'];
$op_room = isset($_GET['op_room']) ? $_GET['op_room'] : $_POST['op_room'];
#$department = $seg_department->FormalName($dept_nr);
$operating_room = $seg_room->get_or_name($op_room);

$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;

$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room :: Request for Surgery"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room :: Request for Surgery");
$smarty->assign('breakfile', $breakfile);

$smarty->assign('check_date_string', $check_date_string);
$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');

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

$mode = 'new';
$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$nr = isset($_POST['or_request_nr']) ? $_POST['or_request_nr'] : '';
$pid = isset($_POST['hospital_number']) ? $_POST['hospital_number'] : '';
$seg_ops = new SegOps();
if (isset($_POST['submitted'])) {
	/*$data = array(
								'refno' => $refno,
								'op_request_nr' => $nr,
								'nr' => $nr,
								'request_date' => date('Y-m-d', strtotime($_POST['or_request_date'])),
								'request_time' => date('H:i:s', strtotime($_POST['or_request_date'])),
								'encounter_nr' => $_POST['encounter_nr'],
								'pid' => $_POST['hospital_number'],
								'ordername' => $_POST['patient_name'],
								'orderaddress' => $_POST['patient_address'],
								'is_cash' =>  $_POST['or_transaction_type'],
								'hasPaid' =>  0,
								'is_urgent' => 0, //temporarily
								'comments' =>  '',
								'status' =>  '',
								'history' => 'Create '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n",
								'ops_code' => '',
								'rvu' => '',
								'multiplier' => '',
								'surgeon' => '',
								'surgeon_assist' =>  '',
								'anesthesiologist' => '',
								'nurse_scrub' => '',
								'nurse_rotating' => '',
								'dept_nr' => $_POST['dept_nr'],
								'op_room' => $_POST['op_room'],
								'op_nr' => $_POST['op_nr'],
								'op_date' => date('m-d-Y', strtotime($_POST['or_operation_date'])),
								'op_time' => date('H:i:s', strtotime($_POST['or_operation_date'])),
								'diagnosis' => $_POST['pre_operative_diagnosis'],
								'op_therapy' => $_POST['operation_procedure']
							 );
		$other_data = array(
											'request_priority' => $_POST['or_request_priority'],
											'pre_operative' => $_POST['pre_op_diagnosis'],
											'proposed_surgery' => $_POST['proposed_surgery'],
											'consent_signed' => ($_POST['or_consent_signed']=='yes') ? '1' : '0',
											'or_request_case' => $_POST['or_request_case'],
											'case_classification' => $_POST['or_case_classification'],
											'or_est_op_length' => $_POST['or_est_op_length'],
											'pre_op_diagnosis' => $_POST['pre_operative_diagnosis'],
											'special_requirements' => implode(',', $_POST['or_special_requirements']),
											'operation_procedure' => $_POST['operation_procedure'],
											'operation_start' => date('H:i:s', strtotime($_POST['operation_started'].' '.$_POST['os_meridian'])),
											'operation_end' => date('H:i:s', strtotime($_POST['operation_ended'].' '.$_POST['oe_meridian'])),
											'or_type' => $_POST['or_type'] //See Maramag top
											);    */

		$ref = new SegRequestSource();
		$refsource = $ref->getSourceOR();

	#Added by Cherry 02-25-10
		$data = array(
										'refno' => $refno,
										'op_request_nr' => $nr,
										'nr' => $nr,
										'request_date' => date('Y-m-d', strtotime($_POST['or_request_date'])),
										'request_time' => date('H:i:s', strtotime($_POST['or_request_date'])),
										'encounter_nr' => $_POST['encounter_nr'],
										'pid' => $_POST['hospital_number'],
										'ordername' => $_POST['patient_name'],
										'orderaddress' => $_POST['patient_address'],
										'is_cash' =>  $_POST['or_transaction_type'],
										'hasPaid' =>  0,
										'is_urgent' => 0, //temporarily
										'comments' =>  '',
										'status' =>  '',
										'trans_type' => $_POST['or_transaction_type'],
										'date_request' => $_POST['or_request_date'],
										'or_procedure' => $_POST['package_name'],
										'procedure_id' => $_POST['package_id'],
										'request_source' => $refsource,
										'special_requirements' => implode(',', $_POST['or_special_requirements']),
										'date_operation' => date('Y-m-d H:i:s', strtotime($_POST['or_operation_date'])),
										'dr_nr' => $_POST['or_doctor'],
										'dept_nr' => $_POST['or_request_department'],
										'is_main' => 1,
										'or_type' => $_POST['or_type'],
										'request_priority' => $_POST['or_request_priority'],
										'remarks' => $_POST['remarks'],
										'history' => 'Create '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n"

		);

	if ($_POST['mode'] == 'new') {
		if ($reference_number = $seg_ops->save_or_main_request($data)) {
			$smarty->assign('sysInfoMessage','OR Main request successfully created.');
			$refno = $reference_number;
			$nr = $seg_ops->getOpRequestNrByRefNo($refno);
			$mode = 'update';
		}
	}
	else {
		if ($seg_ops->update_or_main_request($data, $other_data)) {
			$smarty->assign('sysInfoMessage','OR Main request successfully updated.');
		}
		$mode = 'update';
	}
}
if ($seg_ops->encOpsNrHasOpsServ($nr)) {
	$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
	$or_main_info = $seg_ops->get_or_main_basic_info($refno);
}

/** Form tags **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate(this)">');
$smarty->assign('form_end', '</form>');
/** End Form tags **/

/** Form elements under fieldset "Request Details" **/
$smarty->assign('required_mark', '<em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em>');
$smarty->assign('error_input', '<span id="error_form_input"></span>');

#$smarty->assign('or_request_department', '<input type="text" disabled="disabled" name="or_request_department" id="or_request_department" value="'.$department.'" />');
$smarty->assign('or_op_room', '<input type="text" disabled="disabled" name="or_op_room" id="or_op_room" value="'.$operating_room['info'].'" />');
$smarty->assign('or_transaction_type', array('1' => 'Cash', '0' => 'Charge'));
$smarty->assign('or_transaction_type_selected', isset($_POST['or_transaction_type']) ? $_POST['or_transaction_type'] : '1');

/** Added, February 27, 2009, see Maramag top **/
$smarty->assign('or_type', $seg_ops->get_or_types());
$smarty->assign('or_type_selected', 'OR');
/** End **/

#Added by Cherry 07-07-10
$department = $seg_department->getDeptDoesSurgery();
#$department = $seg_department->getAllMedical();
$smarty->assign('or_request_department', $department);
$smarty->assign('or_request_department_selected', $_POST['or_request_department']);

#Added by Cherry 04-28-10
//$smarty->assign('or_doctor', $seg_personell->getDoctors());
$doctors = $seg_personell->getDoctors(1);
$listDoctors[0]="-Select a Doctor-";
			if (is_object($doctors)){
				while($drInfo=$doctors->FetchRow()){
				#print_r($drInfo);
						$middleInitial = "";
						if (trim($drInfo['name_middle'])!=""){
								$thisMI=split(" ",$drInfo['name_middle']);
								foreach($thisMI as $value){
										if (!trim($value)=="")
												$middleInitial .= $value[0];
								}
								if (trim($middleInitial)!="")
										$middleInitial .= ". ";
						}

						$name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
						$name_doctor = ucwords(strtolower($name_doctor)).", MD";

						#echo "<br> dr = ".$name_doctor;
						#$listDoctors['doctor_name']=$name_doctor;

						$listDoctors[$drInfo["personell_nr"]]=$name_doctor;
						#$listDoctors['doctor_nr']=$drInfo["personell_nr"];


				}
		 }
# print_r($listDoctors);
$smarty->assign('or_doctor', $listDoctors);
$smarty->assign('or_doctor_selected', $_POST['or_doctor']);

//$or_request_priority = array('Emergency'=>'Emergency', 'PHIC'=>'PHIC', 'Elective'=>'Elective'); //See Maramag top
$or_request_priority = array('Emergency'=>'Emergency', 'Elective'=>'Elective');
$smarty->assign('or_request_priority', $or_request_priority);
//$smarty->assign('or_request_priority_selected', $_POST['or_request_priority']);
$smarty->assign('or_request_priority_selected', isset($_POST['or_request_priority']) ? $_POST['or_request_priority'] : 'Elective');

$request_date_display = isset($_POST['or_request_date']) ? date('F d, Y h:ia', strtotime($_POST['or_request_date'])) : date('F d, Y h:ia');
$request_date = isset($_POST['or_request_date']) ? date('Y-m-d H:i', strtotime($_POST['or_request_date'])) : date('Y-m-d H:i');
$smarty->assign('or_request_date_display', '<div id="or_request_date_display" class="date_display">'.$request_date_display.'</div>');
$smarty->assign('or_request_date', '<input type="hidden" name="or_request_date" id="or_request_date" value="'.$request_date.'" />');
$smarty->assign('or_request_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_request_dt_picker" class="date_time_picker" />');
$smarty->assign('or_request_calendar_script', setup_calendar('or_request_date_display', 'or_request_date', 'or_request_dt_picker'));

$smarty->assign('or_consent_signed', array('yes'=>'Yes', 'no'=>'No'));
$smarty->assign('or_consent_signed_selected', $_POST['or_consent_signed']);
$options_service = array('service_elective'=>'Elective', 'service_stat'=>'Stat');
$options_pay = array('pay_elective'=>'Elective', 'pay_stat'=>'Stat');
$smarty->assign('or_request_case_service', $options_service);
$smarty->assign('or_request_case_pay', $options_pay);
$smarty->assign('or_request_case_selected', $_POST['or_request_case']);

/** End **/

$var_arr2 = array(
												"var_id" => "package_id",
												"var_name" => "package_name",
												);


foreach($var_arr2 as $i=>$v) {
	$vars2[] = "$i=$v";
}
$var_package_qry = implode("&",$vars2);

/** Form elements under fieldset "Patient Information" **/
$var_arr = array(
						"var_pid" => "hospital_number",
						"var_name" => "patient_name",
						"var_addr" => "patient_address",
						"var_age" => "patient_age",
						"var_gender" => "patient_gender",
						"var_encounter_nr" => "encounter_nr"
						);
$vas = array();
foreach($var_arr as $i=>$v) {
	$vars[] = "$i=$v";
}
$var_qry = implode("&",$vars);

$patient_name = isset($_POST['patient_name']) ? $_POST['patient_name'] : '';
$patient_gender = isset($_POST['patient_gender']) ? $_POST['patient_gender'] : '';
$patient_age = isset($_POST['patient_age']) ? $_POST['patient_age'] : '';
$patient_address = isset($_POST['patient_address']) ? $_POST['patient_address'] : '';
$hospital_number = isset($_POST['hospital_number']) ? $_POST['hospital_number'] : '';
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : '';

$smarty->assign('package_id', '<input type="hidden" name="package_id" id="package_id" value="'.$package_id.'" />');
$smarty->assign('package_name', '<input type="text" readonly="readonly" name="package_name" id="package_name" value="'.$package_name.'" />');
$smarty->assign('procedure_select', '<a href="javascript:void(0)" onclick="select_procedure(\''.$var_package_qry.'\')"><div id="select_procedure_button"></div></a>');

$smarty->assign('patient_name', '<input type="text" readonly="readonly" name="patient_name" id="patient_name" value="'.$patient_name.'" />');
$smarty->assign('patient_select_button', '<a href="javascript:void(0)" onclick="select_patient(\''.$var_qry.'\')"><div id="select_patient_button"></div></a>');
$smarty->assign('patient_gender', '<input type="text" readonly="readonly" name="patient_gender" id="patient_gender" value="'.$patient_gender.'" />');
$smarty->assign('patient_age', '<input type="text" readonly="readonly" name="patient_age" id="patient_age" value="'.$patient_age.'" />');
$smarty->assign('patient_address', '<input type="text" readonly="readonly" name="patient_address" id="patient_address" value="'.$patient_address.'" />');
$smarty->assign('patient_hospital_number', '<input type="text" readonly="readonly" name="hospital_number" id="hospital_number" value="'.$hospital_number.'" />');

#$smarty->assign('patient_hospital_number', '<input type="text" readonly="readonly" name="patient_hospital_number" id="patient_hospital_number" value="'.$patient_hospital_number.'" />');

$ward = $seg_ward->getListWards();
$smarty->assign('ward', $ward);
$smarty->assign('ward_selected', $_POST['ward']);


/** End **/

/** Form elements under fieldset "Pre-operation Details" **/
$operation_date_display = isset($_POST['or_operation_date']) ? date('F d, Y h:ia', strtotime($_POST['or_operation_date'])) : date('F d, Y h:ia');
$operation_date = isset($_POST['or_operation_date']) ? date('Y-m-d H:i', strtotime($_POST['or_operation_date'])) : date('Y-m-d H:i');

//$smarty->assign('or_operation_date_display', '<div id="or_operation_date_display" class="date_display">'.$operation_date_display.'</div>');
//$smarty->assign('or_operation_date', '<input type="hidden" name="or_operation_date" id="or_operation_date" value="'.$operation_date.'" />');
//$smarty->assign('or_operation_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_operation_dt_picker" class="date_time_picker" />');
//$smarty->assign('or_operation_calendar_script', setup_calendar('or_operation_date_display', 'or_operation_date', 'or_operation_dt_picker'));
//$smarty->assign('or_operation_date_display', '<div id="or_operation_date_display" class="date_display">'.$operation_date_display.'</div>');
$smarty->assign('or_operation_date', '<input type="text" name="or_operation_date" id="or_operation_date" value="'.$_POST['or_operation_date'].'" checkDate(); />');
$smarty->assign('or_operation_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_operation_dt_picker" class="date_time_picker" onClick="openScheduleView()" />');

$received_date_display = isset($_POST['or_received_date']) ? date('F d, Y h:ia', strtotime($_POST['or_received_date'])) : date('F d, Y h:ia');
$received_date = isset($_POST['or_received_date']) ? date('Y-m-d H:i', strtotime($_POST['or_received_date'])) : date('Y-m-d H:i');

$smarty->assign('or_received_date_display', '<div id="or_received_date_display" class="date_display">'.$operation_date_display.'</div>');
$smarty->assign('or_received_date', '<input type="hidden" name="or_received_date" id="or_received_date" value="'.$operation_date.'" />');
$smarty->assign('or_received_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_received_dt_picker" class="date_time_picker" />');
$smarty->assign('or_received_calendar_script', setup_calendar('or_received_date_display', 'or_received_date', 'or_received_dt_picker'));

$or_est_op_length = isset($_POST['or_est_op_length']) ? $_POST['or_est_op_length'] : '';
$pre_operative_diagnosis = isset($_POST['pre_operative_diagnosis']) ? $_POST['pre_operative_diagnosis'] : '';
$operation_procedure = isset($_POST['operation_procedure'])  ? $_POST['operation_procedure'] : '';
$remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
$smarty->assign('or_est_op_length', '<input type="text" name="or_est_op_length" id="or_est_op_length" value="'.$or_est_op_length.'" />');
$smarty->assign('pre_operative_diagnosis', '<textarea name="pre_operative_diagnosis" id="pre_operative_diagnosis">'.$pre_operative_diagnosis.'</textarea>');
$smarty->assign('remarks', '<textarea name="remarks" id="remarks">'.$remarks.'</textarea>');
$smarty->assign('operation_procedure', '<textarea name="operation_procedure" id="operation_procedure">'.$operation_procedure.'</textarea>');
$smarty->assign('or_case_classification', array('clean'=>'Clean',
																								'contaminated'=>'Contaminated',
																								'wound_dehiscence'=>'Wound Dehiscence',
																								'clean_contaminated'=>'Clean/Contamianted',
																								'dirty_infected'=>'Dirty/Infected'));
$smarty->assign('or_case_classification_selected', $_POST['or_case_classification']);
 /** End **/

/** Form elements under fieldset "Other Details" **/
$special_requirements = array('cp_clearance'=>'CP Clearance', 'pulmo_clearance'=>'Pulmo Clearance', 'consent'=>'Consent',
															 'pedia_clearance'=>'Pedia Clearance', 'others'=>'Others');
$smarty->assign('or_special_requirements', $special_requirements);
//$smarty->assign('or_special_requirements_selected', $_POST['or_special_requirements']);
#$try = explode(',', $or_main_info['special_requirements']);
$smarty->assign('or_special_requirements_selected', explode(',', $or_main_info['special_requirements']));
/**Added by Cherry 02-17-10**/
$operation_time = array('07:00 - 08:00'=>'7:00am - 8:00am', '08:00 - 9:00'=>'8:00am - 9:00am', '09:00 - 10:00'=>'9:00am - 10:00am',
												'10:00 - 11:00'=>'10:00am - 11:00am', '11:00 - 12:00'=>'11:00am - 12:00nn',
												'12:00 - 13:00'=>'12:00nn - 1:00pm', '13:00 - 14:00'=>'1:00pm - 2:00pm', '14:00 - 15:00'=>'2:00pm - 3:00pm',
												'15:00 - 16:00'=>'3:00pm - 4:00pm', '16:00 - 17:00'=>'4:00pm - 5:00pm');
$smarty->assign('time_of_operation', $operation_time);
#$smarty->assign('or_special_requirements_selected', $_POST['time_of_operation']);
$smarty->assign('time_of_operation_selected', $_POST['time_of_operation']);
/** End **/

/** Other form elements **/
$smarty->assign('or_main_submit', '<input type="submit" id="or_main_submit" value="" />');
$smarty->assign('or_main_cancel', '<a href="'.$breakfile.'" id="request_cancel"></a>');
$smarty->assign('or_main_print', '<a href="javascript:void(0)" onclick="printRequest('.$refno.', 0)" id="or_main_print"></a>');
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : '';
$hospital_number = isset($_POST['hospital_number']) ? $_POST['hospital_number'] : '';

$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
#$smarty->assign('hospital_number', '<input type="hidden" name="hospital_number" id="hospital_number" value="'.$hospital_number.'" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('mode', '<input type="hidden" name="mode" value="'.$mode.'" />');
$smarty->assign('op_nr', '<input type="hidden" name="op_nr" value="'.$op_nr.'" />');
$smarty->assign('op_room', '<input type="hidden" name="op_room" value="'.$op_room.'" />');
$smarty->assign('dept_nr', '<input type="hidden" name="dept_nr" value="'.$dept_nr.'" />');
$smarty->assign('refno', '<input type="hidden" name="refno" value="'.$refno.'" />');
$smarty->assign('or_request_nr', '<input type="hidden" name="or_request_nr" value="'.$nr.'" />');
/** End **/

$smarty->assign('sMainBlockIncludeFile','or/or_main_request.tpl'); //Assign the or_main template to the frameset
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

?>
<script>

/* Added by Cherry 09-04-10 */
function printRequest(refno, type) {
	//alert("refno= "+refno+" type= "+type);
	window.open("<?=$root_path?>modules/or/request/or_request_form_pdf.php?type="+type+"&refno="+refno,"Request for Surgery","modal, width=900,height=700,menubar=no,resizable=yes,scrollbars=no");
}

/* End Cherry */

function openScheduleView() {
	var dept = $$('[name=or_request_department]').first();
	if (dept.value == '0')
	{
		alert('Please select a department first...');
		dept.focus();
		return;
	}

	//added by cha, 11-18-2010
	var obj = document.getElementsByName('or_request_priority');
	for(i=0;i<obj.length;i++) {
		if(obj[i].checked) {
			var priority = obj[i].value;
		}
	}
	overlib(
		OLiframeContent('<?=$root_path?>modules/or/request/seg_or_schedules.php?m=ormain&dept='+dept.value+'&priority='+priority, 800, 410,
			'schedView', 0, 'auto'),
		WIDTH, 680, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>images/close_red.gif" border=0 >',
		CAPTIONPADDING,2, MIDX,0, MIDY,0,
		CAPTION, 'OR Schedule Viewer',
		STATUS, 'OR Schedule Viewer'
	);

	//window.open('<?=$root_path?>modules/or/request/seg_or_schedules.php?m=orasu&dept='+dept.value, 'scheduler', "width=900,height=700,menubar=no,resizable=no,scrollbars=no");
	return false;
}

function select_procedure(params){

	 // var additional = '&var_include_enc='+($('or_transaction_type').checked?'0':'1');
//alert(params+additional);
overlib(
				OLiframeContent('<?=$root_path?>modules/or/or_main/seg-select-proc.php?'+params, 700, 400,
												'fSelEnc', 0, 'auto'),
				WIDTH, 700, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT,
				'<img src="<?=$root_path?>images/close_red.gif" border=0 >',
				CAPTIONPADDING,2, CAPTION, 'Select procedure',
				MIDX,0, MIDY,0, STATUS, 'Select procedure');

								/*overlib(
								OLiframeContent('<?=$root_path?>modules/registration_admission/seg-select-enc.php?'+params, 700, 400,
																								'fSelEnc', 0, 'auto'),
								WIDTH, 700, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT,
								'<img src="<?=$root_path?>images/close_red.gif" border=0 >',
								CAPTIONPADDING,2, CAPTION, 'Select procedure',
								MIDX,0, MIDY,0, STATUS, 'Select procedure'); */
				return false;
}

function select_patient(params) {

var additional = '&var_include_enc='+($('or_transaction_type').checked?'0':'1');
//alert(params+additional);
overlib(
				OLiframeContent('<?=$root_path?>modules/registration_admission/seg-select-enc.php?'+params+additional, 700, 400,
												'fSelEnc', 0, 'auto'),
				WIDTH, 700, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT,
				'<img src="<?=$root_path?>images/close_red.gif" border=0 >',
				CAPTIONPADDING,2, CAPTION, 'Select registered person',
				MIDX,0, MIDY,0, STATUS, 'Select registered person');
				return false;
}

/**Added by Cherry 02-18-10**/

 function checkType(thisType){
				//alert('checkType');
				//alert($('cert_type').value);
				type = thisType;
				//alert(type);
		}

	 /* Added by Cherry 02-19-10 */
		function preset(){
				//alert('preset =^_^=');
				var d = document.main_or_form;
				if(d.refno.value==null || d.refno.value=='')
					document.getElementById('or_main_print').style.display='none';
				else
					document.getElementById('or_main_print').style.display='';
				checkDate();
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
				 /*if (d.or_operation_date.value==''){
						//alert('true');
						document.getElementById('op_time').style.display = 'none';
				}else{
						//alert('false')
						 document.getElementById('op_time').style.display = '';
				}   */


		}

		/*End Cherry*/

function validate(e) {

	var d = document.main_or_form;

		if(d.package_name.value=='' || d.patient_name.value=='' || d.or_doctor.value=='0'
		|| d.or_request_department.value=='0' || d.or_special_requirements.length=='0'
		|| d.or_operation_date.value==''){
						alert('Please fill-in all necessary fields');
						if(d.or_request_department.value=='0'){
							d.or_request_department.focus();
						}else if(d.package_name.value==''){
							d.package_name.focus();
						}else if(d.or_doctor.value=='0'){
							d.or_doctor.focus();
						}else if(d.patient_name.value==''){
							d.patient_name.focus();
						}else if(d.or_special_requirements.length=='0'){
							d.or_special_requirements.focus();
						}else if(d.or_operation_date.value==''){
							d.or_operation_date.focus();
						}
						return false;
		}else{
			var sure = confirm('Submit this request?');
				if(sure){
					//d.submitted.value = true;
					return true;
				}else{
					return false;
				}
		}

	var array_elements = [ {field: J("input[@name='or_transaction_type']"),
												 field_value: J("input[@name='or_transaction_type']:checked").val(),
												 msg: 'Please choose a transaction type',
												 msg_dest: J('#error_form_input', J('#transaction_type_msg')),
												 },
												 {field: J("input[@name='or_request_priority']"),
												 field_value: J("input[@name='or_request_priority']:checked").val(),
												 msg: 'Please choose a priority',
												 msg_dest: J('#error_form_input', J('#priority_msg')),
												 },
												 /*{field: J("input[@name='or_consent_signed']"),
												 field_value: J("input[@name='or_consent_signed']:checked").val(),
												 msg: 'Please specify whether the consent was signed or not',
												 msg_dest: J('#error_form_input', J('#or_consent_signed_msg')),
												 },
												 {field: J("input[@name='or_request_case']"),
												 field_value: J("input[@name='or_request_case']:checked").val(),
												 msg: 'Please specify the type of case',
												 msg_dest: J('#error_form_input', J('#or_request_case_msg')),
												 }, */
												 {field: J("input[@name='patient_name']"),
												 field_value: J("input[@name='patient_name']").val(),
												 msg: 'Please choose a patient',
												 msg_dest: J('#error_form_input', J('#patient_name_msg')),
												 is_textfield: true
												 },
												 /*{field: J("input[@name='or_case_classification']"),
												 field_value: J("input[@name='or_case_classification']:checked").val(),
												 msg: 'Please determine the case classification',
												 msg_dest: J('#error_form_input', J('#or_case_classification_msg')),
												 }, */
												 {field: J("input[@name='or_special_requirements']"),
												 field_value: J("input[@name='or_special_requirements[]']:checked").length,
												 msg: 'Please specify a special requirement',
												 msg_dest: J('#error_form_input', J('#special_req_msg')),
												 },
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
}
</script>
