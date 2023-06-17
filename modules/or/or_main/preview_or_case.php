<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');   
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path.'include/care_api_classes/class_order.php');  //load the SegOrder class
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_oproom.php'); //load the department class
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions
require_once($root_path.'include/care_api_classes/class_person.php'); //load the person class
require_once($root_path.'include/care_api_classes/class_social_service.php'); //load the segops class
require_once($root_path.'include/care_api_classes/class_equipment_order.php'); //load the segops class
require_once($root_path.'include/care_api_classes/class_ward.php'); //load the ward class
require_once($root_path.'include/care_api_classes/class_vitalsign.php'); //load the vital sign class
global $db;  //see *manolo
$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room Main :: Post Operative Details"); //Assign a toolbar title 
$smarty->assign('sWindowTitle',"Operating Room Main :: Post Operative Details");
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
$smarty->assign('breakfile', $breakfile);
$smarty->assign('check_date_string', $check_date_string);
$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');

$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
                          , '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
                          , '<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />'
                          , '<script>var J = jQuery.noConflict();</script>'
                          , '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />'
                          );
$smarty->assign('javascript_array', $javascript_array);



$seg_ops = new SegOps();

$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$nr = $seg_ops->getOpRequestNrByRefNo($refno);
$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];


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

$status = $seg_ops->get_or_main_status($or_main_info['or_main_refno']);
$smarty->assign('status', $status);
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
$vs = $vital_sign->get_all_vital_signs($basic_info['pid'], $basic_info['encounter_nr']);
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
/** End **/

/** Form elements under fieldset "Pre-operation Details" **/

$operation_date = date('F d, Y h:ia', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));
$smarty->assign('or_operation_date', $operation_date);

$or_est_op_length = $or_main_info['est_length_op'];
$pre_operative_diagnosis = $or_main_info['pre_op_diagnosis'];
$operation_procedure = $or_main_info['operation_procedure'];
$smarty->assign('or_est_op_length', check_value($or_est_op_length)); 
$smarty->assign('pre_operative_diagnosis', check_value($pre_operative_diagnosis));
$smarty->assign('operation_procedure', check_value($operation_procedure));
$classification = array('clean'=>'Clean', 'contaminated'=>'Contaminated',
                        'wound_dehiscence'=>'Wound Dehiscence',
                        'clean_contaminated'=>'Clean/Contamianted',
                        'dirty_infected'=>'Dirty/Infected');
$smarty->assign('or_case_classification', $classification[$or_main_info['case_classification']]);

 /** End **/
 
/** Form elements under fieldset "Other Details" **/
$special_requirements = array('cp_clearance'=>'CP Clearance', 'pulmo_clearance'=>'Pulmo Clearance', 'consent'=>'Consent',
                               'pedia_clearance'=>'Pedia Clearance', 'others'=>'Others');
$smarty->assign('or_special_requirements', $special_requirements[$or_main_info['special_requirements']]);
/** End **/



$smarty->assign('or_main_cancel', '<a href="'.$breakfile.'" id="or_main_cancel"></a>');


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
 
$smarty->assign('post_time_started', check_value($post_time_started . ' ' .strtoupper($post_op_details['ts_meridian'])));
$smarty->assign('post_time_finished', check_value($post_time_finished. ' ' .strtoupper($post_op_details['tf_meridian'])));
$smarty->assign('post_operative_diagnosis', check_value($post_op_diagnosis));
$smarty->assign('operation_performed', check_value($operation_performed));
$smarty->assign('or_technique', check_value($or_technique));
                                                                  
$ward = new Ward();

//$ward_array = array(0=>'Please Select Ward');
$ward_array = $ward->get_all_active_wards_as_pair('nr', 'name');
$smarty->assign('transferred_to', check_value($ward_array[$post_op_details['transferred_to']]));
$pers_id_array = $seg_ops->getOpsPersonellNr($refno ? $refno:0, 7);
$pers_info = $seg_ops->setPersonellNrNamePID($pers_id_array);
foreach ($pers_info as $key => $value) {
  $temp_surgeon .= $value['name'] . ', ';
}
$smarty->assign('surgeons', check_value(substr($temp_surgeon, 0, -2)));

$pers_id_array = $seg_ops->getOpsPersonellNr($refno ? $refno:0, 8);
$pers_info = $seg_ops->setPersonellNrNamePID($pers_id_array);
foreach ($pers_info as $key => $value) {
  $temp_assistant_surgeon .= $value['name'] . ', ';
}
$smarty->assign('assistant_surgeons', check_value(substr($temp_assistant_surgeon, 0, -2)));

$pers_id_array = $seg_ops->getOpsPersonellNr($refno ? $refno:0, 12);
$pers_info = $seg_ops->setPersonellNrNamePID($pers_id_array);
foreach ($pers_info as $key => $value) {
  $temp_anesthesiologist .= $value['name'] . ', ';
}
$smarty->assign('anesthesiologists', check_value(substr($temp_anesthesiologist, 0, -2)));

$pers_id_array = $seg_ops->getOpsPersonellNr($refno ? $refno:0, 9);
$pers_info = $seg_ops->setPersonellNrNamePID($pers_id_array);
foreach ($pers_info as $key => $value) {
  $temp_scrub_nurse .= $value['name'] . ', ';
}
$smarty->assign('scrub_nurses', check_value(substr($temp_scrub_nurse, 0, -2)));

$pers_id_array = $seg_ops->getOpsPersonellNr($refno ? $refno:0, 10);
$pers_info = $seg_ops->setPersonellNrNamePID($pers_id_array);
foreach ($pers_info as $key => $value) {
  $temp_circulating_nurse .= $value['name'] . ', ';
}
$smarty->assign('circulating_nurses', check_value(substr($temp_circulating_nurse, 0, -2)));
/** End **/

/** Anesthesia Procedures **/
$anesthesias = $seg_ops->get_or_main_anesthesia($or_main_info['or_main_refno']);
$anesthesia_procedures = '';
if ($anesthesias) {
  while ($row = $anesthesias->FetchRow()) {
    $anesthesia_procedures  .= '<tr align="center"><td>'.$row['name'].'</td><td>'.$row['anesthetics'].'</td><td>'.$row['time_begun']. ' '.strtoupper($row['tb_meridian']).'</td><td>'.$row['time_ended'].' '.strtoupper($row['te_meridian']).'</td></tr>';                        
  }
}
else {
  $anesthesia_procedures  = '<tr><td colspan="4">No anesthesia procedure was added yet...</td></tr>';
}
$smarty->assign('anesthesia_procedures', $anesthesia_procedures);
/** End **/

/** Anesthetic agent **/
$smarty->assign('anesthetic_intra_operative', check_value($anesthetic_intra_operative));
$smarty->assign('anesthetic_post_operative', check_value($anesthetic_post_operative));
$smarty->assign('anesthetic_patient_status', check_value($anesthetic_patient_status));
/** End **/

/** RVUs **/
$rvu = $seg_ops->getOpsServDetailsInfo($refno);
if ($rvu){
  while($result = $rvu->FetchRow()) {
    $icpm .= '<tr>
               <td>'.$result['ops_code'].'</td>
               <td>'.$result['description'].'</td>
               <td>'.$result['rvu'].'</td>
               <td>'.$result['multiplier'].'</td>
               <td>'.$result['ops_charge'].'</td>
             </tr>';
  }
}
else {
  $icpm  = '<tr><td colspan="5">No ICPM was added yet...</td></tr>';
} 
$smarty->assign('icpm', $icpm);
/** End **/

/** Medicines and Supplies **/
$social_service = new SocialService();
$social_service_details = $social_service->getLatestClassificationByPid($basic_info['pid']); //get the discount and discountid
$pharma = $seg_ops->get_seg_pharma_or_main($refno);
if ($pharma) {
    $pharma_refno = $pharma['pharma_refno'];
}
else {
    $pharma_refno = 0;
}
$order_obj = new SegOrder('pharma');
$result = $order_obj->getOrderItemsFullInfo($pharma_refno, $social_service_details['discountid']);
if ($result) {
  $meds_and_supplies = '';
  $net_total = 0;
  while ($row = $result->FetchRow()) {
    
    $price = number_format((float)$row['quantity']*(float)$row['force_price'],2);
       
    $meds_and_supplies .= '<tr>
                            <td align="left">'.$row["artikelname"].'</td>
                            <td>'.number_format((float)$row['force_price'],2).'</td>
                            <td align="center">'.number_format((float)$row['quantity']).'</td>
                            <td align="right">'.$price.'</td>
                          </tr>';
    $net_total += $price;
  }
  if ($result->RecordCount() <= 0)
    $meds_and_supplies  = '<tr><td colspan="4">No medicines and supplies were added yet...</td></tr>';
}
else {
  $meds_and_supplies  = '<tr><td colspan="4">No medicines and supplies were added yet...</td></tr>';
}
$smarty->assign('medicines_and_supplies', $meds_and_supplies);
$smarty->assign('net_total', number_format($net_total, 2));
/** End **/

/** Equipments **/
$equipment_order = new SegEquipmentOrder();
$equipment_refno = $equipment_order->get_equipment_refno($refno);
$result = $equipment_order->get_order_items($equipment_refno);
$net_total_equipments = 0;
if ($result) {
while ($row = $result->FetchRow()) {
  $price = number_format((float)$row['number_of_usage']*(float)$row['amount'],2);
  $unit = (int)$row['number_of_usage'] > 1 ? $row['equipment_unit'].'s' : $row['equipment_unit'];
  $equipments .= '<tr>
                   <td>'.$row['equipment_name'].'</td>
                   <td align="left">'.number_format($row['amount'], 2).'</td>
                   <td align="center">'.$row['number_of_usage']. ' '.$unit.'</td>
                   <td align="right">'.$price.'</td>
                 </tr>';
  $net_total_equipments += $price;
}
}
else {
  $equipments  = '<tr><td colspan="4">No equipment was added yet...</td></tr>';
}
$smarty->assign('equipments', $equipments);
$smarty->assign('net_total_equipments', number_format($net_total_equipments, 2));
/** End **/

/** Sponge Count **/
$result = $seg_ops->get_sponge_count($or_main_info['or_main_refno']);
if ($result->RecordCount()) {
  while ($row = $result->FetchRow()) {
    $sponges .= '<tr>
                     <td align="center">'.$row['artikelname'].'</td>
                     <td align="center">'.$row['initial_count'].'</td>
                     <td align="center">
                       <table>
                         <tr>
                           <td width="52px" align="center">'.$row['f_count_table'].'</td>
                           <td width="52px" align="center">'.$row['f_count_floor'].'</td>
                           <td width="52px" align="center">'.($row['f_count_table']+$row['f_count_floor']).'</td>
                         </tr>
                       </table>
                     </td>
                     <td align="center">
                       <table>
                         <tr>
                           <td width="52px" align="center">'.$row['s_count_table'].'</td>
                           <td width="52px" align="center">'.$row['s_count_floor'].'</td>
                           <td width="52px" align="center">'.($row['s_count_table']+$row['s_count_floor']).'</td>
                         </tr>
                       </table>
                     </td>
                 </tr>';
  }
}
else {
  $sponges  = '<tr><td colspan="4">No sponge was added yet...</td></tr>';
}
$smarty->assign('sponges', $sponges);
/** End **/
                                                           
/** Form elements under Others Details **/
$smarty->assign('sponge_count', $sponge_count);
$smarty->assign('needle_count', $needle_count);
$smarty->assign('instrument_count', $instrument_count);
/** End **/
$smarty->assign('bHideCopyright', true);  
$smarty->assign('bHideTitleBar', true);
$smarty->assign('sMainBlockIncludeFile','or/preview_or_case.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame   

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
function toggle_pre_op() {
  J('#pre_op_details').slideToggle('100');
}
function toggle_post_op() {
  J('#post_operative_details').slideToggle('100');
}

J('#cancel').click(function() { 
   location.href='<?=$breakfile?>'; 
   return false; 
}); 

</script>
