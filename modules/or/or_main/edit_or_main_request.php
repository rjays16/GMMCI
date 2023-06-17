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



     
$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room Main :: Edit Request for Surgery"); //Assign a toolbar title 
$smarty->assign('sWindowTitle',"Operating Room Main :: Edit Request for Surgery"); //Assign a toolbar title 
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
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

$seg_ops = new SegOps();
$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$nr = $seg_ops->getOpRequestNrByRefNo($refno);

if (isset($_POST['submitted']) && $_POST['mode']=='edit') {
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
                'op_room' => $_POST['or_op_room'],
                //'op_nr' => $_POST['op_nr'],
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
                      'or_type' => $_POST['or_type']
                      );
    
    if ($seg_ops->update_or_main_request($data, $other_data)) {
      $smarty->assign('sysInfoMessage','OR Main request successfully updated.');   
    }
}

if ($seg_ops->encOpsNrHasOpsServ($nr)) {
  $basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
  $or_main_info = $seg_ops->get_or_main_basic_info($refno);
}
  
$seg_department = new Department();
$seg_room = new OPRoom();
$dept_nr = $basic_info['dept_nr'];
$op_nr = $basic_info['op_nr'];
$op_room = $basic_info['op_room'];
$department = $seg_department->FormalName($dept_nr);
$operating_room = $seg_room->get_or_name($op_room);

/** Form tags **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate(this)">');
$smarty->assign('form_end', '</form>');
/** End Form tags **/

/** Form elements under fieldset "Request Details" **/
$smarty->assign('required_mark', '<em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em>');
$smarty->assign('error_input', '<span id="error_form_input"></span>');

$smarty->assign('or_request_department', '<input type="text" disabled="disabled" name="or_request_department" id="or_request_department" value="'.$department.'" />');
$smarty->assign('or_op_room', $seg_room->get_operating_rooms());
$smarty->assign('or_transaction_type', array('1' => 'Cash', '0' => 'Charge'));
$smarty->assign('or_transaction_type_selected', $basic_info['is_cash']);

/** Added, February 27, 2009, see Maramag top **/
$smarty->assign('or_type', $seg_ops->get_or_types());
$smarty->assign('or_type_selected', $or_main_info['or_type']);
/** End **/

$or_request_priority = array('Emergency'=>'Emergency', 'PHIC'=>'PHIC', 'Elective'=>'Elective');
$smarty->assign('or_request_priority', $or_request_priority);
$smarty->assign('or_request_priority_selected', $or_main_info['request_priority']);

$request_date_display = date('F d, Y h:ia', strtotime($basic_info['request_date'].' '.$basic_info['request_time']));
$request_date = date('Y-m-d H:i', strtotime($basic_info['request_date'].' '.$basic_info['request_time']));
$smarty->assign('or_request_date_display', '<div id="or_request_date_display" class="date_display">'.$request_date_display.'</div>');
$smarty->assign('or_request_date', '<input type="hidden" name="or_request_date" id="or_request_date" value="'.$request_date.'" />');
$smarty->assign('or_request_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_request_dt_picker" class="date_time_picker" />');
$smarty->assign('or_request_calendar_script', setup_calendar('or_request_date_display', 'or_request_date', 'or_request_dt_picker'));

$smarty->assign('or_consent_signed', array('yes'=>'Yes', 'no'=>'No'));
$smarty->assign('or_consent_signed_selected', ($or_main_info['consent_signed'] == 1) ? 'yes' : 'no');
$options_service = array('service_elective'=>'Elective', 'service_stat'=>'Stat');
$options_pay = array('pay_elective'=>'Elective', 'pay_stat'=>'Stat');
$smarty->assign('or_request_case_service', $options_service);
$smarty->assign('or_request_case_pay', $options_pay);
$smarty->assign('or_request_case_selected', $or_main_info['or_case']);

/** End **/

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

$seg_person = new Person($basic_info['pid']);
$person_info = $seg_person->getAllInfoArray();
$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
$person_name = ucwords($person_info['name_last']) . ', ' . ucwords($person_info['name_first']) . ' ' . $middle_initial; 
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
$smarty->assign('patient_name', '<input type="text" readonly="readonly" name="patient_name" id="patient_name" value="'.$patient_name.'" />');
$smarty->assign('patient_select_button', '<a href="javascript:void(0)" onclick="select_patient(\''.$var_qry.'\')"><div id="select_patient_button"></div></a>');
$smarty->assign('patient_gender', '<input type="text" readonly="readonly" name="patient_gender" id="patient_gender" value="'.$patient_gender.'" />');
$smarty->assign('patient_age', '<input type="text" readonly="readonly" name="patient_age" id="patient_age" value="'.$patient_age.'" />');
$smarty->assign('patient_address', '<input type="text" readonly="readonly" name="patient_address" id="patient_address" value="'.$patient_address.'" />');
/** End **/

/** Form elements under fieldset "Pre-operation Details" **/
$operation_date_display = date('F d, Y h:ia', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));
$operation_date = date('Y-m-d H:i', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));

$smarty->assign('or_operation_date_display', '<div id="or_operation_date_display" class="date_display">'.$operation_date_display.'</div>');
$smarty->assign('or_operation_date', '<input type="hidden" name="or_operation_date" id="or_operation_date" value="'.$operation_date.'" />');
$smarty->assign('or_operation_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_operation_dt_picker" class="date_time_picker" />');
$smarty->assign('or_operation_calendar_script', setup_calendar('or_operation_date_display', 'or_operation_date', 'or_operation_dt_picker'));

$or_est_op_length = $or_main_info['est_length_op'];
$pre_operative_diagnosis = $or_main_info['pre_op_diagnosis'];
$operation_procedure = $or_main_info['operation_procedure'];
$smarty->assign('or_est_op_length', '<input type="text" name="or_est_op_length" id="or_est_op_length" value="'.$or_est_op_length.'" />'); 
$smarty->assign('pre_operative_diagnosis', '<textarea name="pre_operative_diagnosis" id="pre_operative_diagnosis">'.$pre_operative_diagnosis.'</textarea>');
$smarty->assign('operation_procedure', '<textarea name="operation_procedure" id="operation_procedure">'.$operation_procedure.'</textarea>');
$smarty->assign('or_case_classification', array('clean'=>'Clean',
                                                'contaminated'=>'Contaminated',
                                                'wound_dehiscence'=>'Wound Dehiscence',
                                                'clean_contaminated'=>'Clean/Contamianted',
                                                'dirty_infected'=>'Dirty/Infected'));
$smarty->assign('or_case_classification_selected', $or_main_info['case_classification']);
 /** End **/
 
/** Form elements under fieldset "Other Details" **/
$special_requirements = array('cp_clearance'=>'CP Clearance', 'pulmo_clearance'=>'Pulmo Clearance', 'consent'=>'Consent',
                               'pedia_clearance'=>'Pedia Clearance', 'others'=>'Others');
$smarty->assign('or_special_requirements', $special_requirements);
$smarty->assign('or_special_requirements_selected', explode(',', $or_main_info['special_requirements']));

/** End **/

/** Other form elements **/
$smarty->assign('or_main_submit', '<input type="submit" id="or_main_submit" value="" />');
$smarty->assign('or_main_cancel', '<a href="'.$breakfile.'" id="request_cancel"></a>');
$encounter_nr = $basic_info['encounter_nr'];
$hospital_number = $basic_info['pid'];

$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
$smarty->assign('hospital_number', '<input type="hidden" name="hospital_number" id="hospital_number" value="'.$hospital_number.'" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('mode', '<input type="hidden" name="mode" value="'.$mode.'" />');
//$smarty->assign('op_nr', '<input type="hidden" name="op_nr" value="'.$op_nr.'" />');
//$smarty->assign('op_room', '<input type="hidden" name="op_room" value="'.$op_room.'" />');
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

?>
<script>

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

function validate(e) {
  
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
                         {field: J("input[@name='or_consent_signed']"), 
                         field_value: J("input[@name='or_consent_signed']:checked").val(), 
                         msg: 'Please specify whether the consent was signed or not',
                         msg_dest: J('#error_form_input', J('#or_consent_signed_msg')),
                         },
                         {field: J("input[@name='or_request_case']"), 
                         field_value: J("input[@name='or_request_case']:checked").val(), 
                         msg: 'Please specify the type of case',
                         msg_dest: J('#error_form_input', J('#or_request_case_msg')),
                         },
                         {field: J("input[@name='patient_name']"), 
                         field_value: J("input[@name='patient_name']").val(), 
                         msg: 'Please choose a patient',
                         msg_dest: J('#error_form_input', J('#patient_name_msg')),
                         is_textfield: true
                         },
                         {field: J("input[@name='or_case_classification']"), 
                         field_value: J("input[@name='or_case_classification']:checked").val(), 
                         msg: 'Please determine the case classification',
                         msg_dest: J('#error_form_input', J('#or_case_classification_msg')),
                         },
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
