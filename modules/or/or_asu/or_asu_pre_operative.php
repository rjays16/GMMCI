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
require_once($root_path.'include/care_api_classes/class_vitalsign.php'); //load the vital sign class

$breakfile='or_asu_pre_operation.php'.URL_APPEND;

$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room Main :: Schedule Surgery"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room Main :: Schedule Surgery");
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

$vital_sign = new SegVitalsign();
$seg_ops = new SegOps();
$refno = isset($_POST['refno']) ? $_POST['refno'] : $_GET['refno'];
$nr = $seg_ops->getOpRequestNrByRefNo($refno);
#$hospital_number = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
#$hospital_number = $pid_nr;
$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
$or_main_info = $seg_ops->get_or_main_basic_info($refno);
#$op_date =  date('Y-m-d', strtotime($_POST['operation_date']));
#$date_of_op = isset($_POST['operation_date']) ? $_POST['operation_date'] : $_GET['operation_date'];
#echo "pid sa taas= ".$pid."<br>";
#echo "hospital number= ".$hospital_number."<br>";
switch($or_main_info['time_operation']){

		case '07:00 - 08:00':
														$time_operation = '07:00:00';
														break;
		case '08:00 - 09:00':
														$time_operation = '08:00:00';
														break;
		case '09:00 - 10:00':
														$time_operation = '09:00:00';
														break;
		case '10:00 - 11:00':
														$time_operation = '10:00:00';
														break;
		case '11:00 - 12:00':
														$time_operation = '11:00:00';
														break;
		case '12:00 - 01:00':
														$time_operation = '12:00:00';
														break;
		case '01:00 - 02:00':
														$time_operation = '13:00:00';
														break;
		case '02:00 - 03:00':
														$time_operation = '14:00:00';
														break;
		case '03:00 - 04:00':
														$time_operation = '15:00:00';
														break;
		case '04:00 - 05:00':
														$time_operation = '16:00:00';
														break;
}
#echo "temperature= ".$_POST['temperature']."<br>";
#echo "pulse= ".$_POST['pulse']."<br>";

if (isset($_POST['submitted'])) {
	//edited by celsy 07/08/10 function for saving checklist
//	print_r("char lang");
//	print_r($_POST['checkbox_ids']);
//	print_r($_POST['detail_ids']);
	$checklist = $seg_ops->update_pre_op_checklist(implode(',', $_POST['question']), $_POST['or_main_refno'],3);
	if(!$checklist){
		$smarty->assign('sysInfoMessage','All mandatory items in the pre-operation checklist must be checked. <br/>No changes were made.');
	}
	else{
		$checklist2 = $seg_ops->update_pre_op_checklist_with_details($_POST['checkbox_ids'], $_POST['detail_ids'], $_POST['or_main_refno'],3);
		if(!$checklist2){
			$smarty->assign('sysInfoMessage','All mandatory items in the pre-operation checklist must be checked. <br/>The checklist details must be filled out.');
		}
	}
	if($checklist && $checklist2){
	 $vs = $vital_sign->get_latest_vital_signs($pid, $encounter_nr);
	 $data = array(
									'refno' => $refno,
									'or_main_refno' => $_POST['or_main_refno'],
									'op_date' => date('Y-m-d', strtotime($or_main_info['date_operation'])),
									'op_time' => date('H:i:s', strtotime($time_operation)),
									'surgeon' => $_POST['surgeon'],
									'surgeon_assist' =>  $_POST['surgeon_assist'],
									'anesthesiologist' => $_POST['anesthesiologist'],
							);

	 if (($vs['temp']!=$_POST['temperature'] ||
		$vs['pulse_rate']!=$_POST['pulse'] ||
		$vs['resp_rate']!=$_POST['respiratory'] ||
		$vs['systole']!=$_POST['bp_systol'] ||
		$vs['diastole']!=$_POST['bp_diastol'])) {

			$vs_array = array('encounter_nr'=>$encounter_nr,
											'pid'=>$pid,
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
	 }
			#$seg_ops->update_or_main_status($or_main_info['or_main_refno'], '', 'pre_op');
			if ($save || $seg_ops->update_or_main_schedule($data)) {
					 $seg_ops->update_or_main_status($or_main_info['or_main_refno'], '', 'pre_op');
					 #$smarty->assign('sysInfoMessage','OR Main request successfully scheduled.');
					 $smarty->assign('sysInfoMessage','Pre-operation details successfully updated.');
			}
			/*if ($save) {
			$smarty->assign('sysInfoMessage','Pre-operation checklist successfull updated.');
			}*/
		else {
			$smarty->assign('sysInfoMessage','No changes has been made to the pre-operation details.');
		}
	}
}

//-------Added by Cherry May 21, 2010-------
if ($result = $seg_ops->get_pre_op_checklist($or_main_info['or_main_refno'],3)) {

	$smarty->assign('questions', $result['questions']);
	#print_r($result['selected']);
	$smarty->assign('questions_selected', $result['selected']);
	#$smarty->assign('questions_selected', '');
}

//---------added by celsy 07/08/2010--------//
//for displaying the checklist ietms with details
if ($result = $seg_ops->get_pre_op_checklist_with_details($or_main_info['or_main_refno'],3)) {
	$str='';
	$detail_id = '';
	$checkbox_id = '';
	$questions_with_detail=$result['questions2'];
	$selected_cb=$result['selected2'];
	$labels = $result['labels'];
	$details = $result['details'];

//			print_r($result['questions2']);
//			print_r($result['selected2']);
//			print_r($result['labels']);
//			print_r($result['details']);

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
}

 //---------end CELSY-----------//

#echo "pid= ".$pid."<br>";
#echo "encounter nr= ".$or_main_info['encounter_nr']."<br>";
#echo "<br>";
$vs = $vital_sign->get_latest_vital_signs($pid, $encounter_nr);

#echo "vs temp= ".$vs['temp']."<br>";
#$vs = $vital_sign->get_latest_vital_signs($basic_info['pid'], $basic_info['encounter_nr']);
$smarty->assign('temperature', '<input type="text" name="temperature" value="'.$vs['temp'].'" />');
$smarty->assign('pulse', '<input type="text" name="pulse" value="'.$vs['pulse_rate'].'" id="pulse" />');
$smarty->assign('respiratory', '<input type="text" name="respiratory" value="'.$vs['resp_rate'].'" />');
$smarty->assign('bp_systol', '<input type="text" name="bp_systol" value="'.$vs['systole'].'" style="width: 72px" />');
$smarty->assign('bp_diastol', '<input type="text" name="bp_diastol" value="'.$vs['diastole'].'" style="width: 73px" />');
$smarty->assign('error_input', '<span id="error_form_input"></span>');
//------------------END-------------------

/*if ($seg_ops->encOpsNrHasOpsServ($nr)) {
		$basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
		$or_main_info = $seg_ops->get_or_main_basic_info($refno);
} */

#---added by CHa, March 19, 2010---
global $db;
		$query = "select operator, assistant, scrub_nurse, rotating_nurse, an_doctor from care_encounter_op where refno=".$db->qstr($refno);
		$res = $db->Execute($query);
		if($res->RecordCount()>0)
		{
				while($row=$res->FetchRow())
				{
				 $surgeons = unserialize($row['operator']);
				 if($surgeons)
				 {
						 $x=0;
						 $y=$x+1;
						 $surgeons_list="";
						 while($x<count($surgeons))
						 {
								 $query = "SELECT concat(ifnull(cp.name_first,' '), ifnull(cp.name_middle,' '), ifnull(cp.name_last,' ')) as `name` ".
								 "FROM care_person AS cp INNER JOIN care_personell as cr ON cp.pid=cr.pid ".
								 "WHERE cr.nr=".$db->qstr($surgeons["operator+".$y]);
								 $surgeons_name[] = array("name"=>$db->GetOne($query), "id"=>$surgeons["operator+".$y]);
								 $surgeons_list.='<tr class="wardlistrow" id="row'.$surgeons_name[$x]["id"].'">'.
																				'<td class="center"><a href="javascript:removeItem(\''.$surgeons_name[$x]["id"].'\',$(\'surgeon_list\'),\'surgeon[]\')">'.
																				'    <img src="../../../images/btn_delitem.gif" border="0"/></a>'.
																				'</td>'.
																				'<td>&nbsp;</td>'.
																				'<td align="left">'.
																				'    <span style="font:bold 12px Arial;color:#660000">'.$surgeons_name[$x]["name"].'</span>'.
																				'    <input id="rowID'.$surgeons_name[$x]["id"].'" type="hidden" value="'.$surgeons_name[$x]["id"].'" name="surgeon[]"/>'.
																				'</td>'.
																		'</tr>';
								 $x++;
								 $y+=$x;
						 }
				 }
				 else
				 {
						 $surgeons_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				 }

				 $asst_surgeons = unserialize($row['assistant']);
				 if($asst_surgeons)
				 {
						 $x=0;
						 $y=$x+1;
						 while($x<count($asst_surgeons))
						 {
								 $query = "SELECT concat(ifnull(cp.name_first,' '), ifnull(cp.name_middle,' '), ifnull(cp.name_last,' ')) as `name` ".
								 "FROM care_person AS cp INNER JOIN care_personell as cr ON cp.pid=cr.pid ".
								 "WHERE cr.nr=".$db->qstr($asst_surgeons["assistant+".$y]);
								 $asst_surgeons_name[] = array("name"=>$db->GetOne($query), "id"=>$asst_surgeons["assistant+".$y]);
								 $asst_surgeons_list.='<tr class="wardlistrow" id="row'.$surgeons_name[$x]["id"].'">'.
																'<td class="center"><a href="javascript:removeItem(\''.$asst_surgeons_name[$x]["id"].'\',$(\'assistant_surgeon_list\'),\'surgeon_assist[]\')">'.
																'    <img src="../../../images/btn_delitem.gif" border="0"/></a>'.
																'</td>'.
																'<td>&nbsp;</td>'.
																'<td align="left">'.
																'    <span style="font:bold 12px Arial;color:#660000">'.$asst_surgeons_name[$x]["name"].'</span>'.
																'    <input id="rowID'.$asst_surgeons_name[$x]["id"].'" type="hidden" value="'.$asst_surgeons_name[$x]["id"].'" name="surgeon_assist[]"/>'.
																'</td>'.
														'</tr>';
								 $x++;
								 $y+=$x;
						 }
				 }
				 else
				 {
						 $asst_surgeons_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				 }


				 $scrub_nurses = unserialize($row['scrub_nurse']);
				 if($scrub_nurses)
				 {
						 $x=0;
						 $y=$x+1;
						 while($x<count($scrub_nurses))
						 {
								 $query = "SELECT concat(ifnull(cp.name_first,' '), ifnull(cp.name_middle,' '), ifnull(cp.name_last,' ')) as `name` ".
								 "FROM care_person AS cp INNER JOIN care_personell as cr ON cp.pid=cr.pid ".
								 "WHERE cr.nr=".$db->qstr($scrub_nurses["scrub_nurse+".$y]);
								 $scrub_nurses_name[] = array("name"=>$db->GetOne($query), "id"=>$scrub_nurses["scrub_nurse+".$y]);
								 $scrub_nurses_list.='<tr class="wardlistrow" id="row'.$scrub_nurses_name[$x]["id"].'">'.
										'<td class="center"><a href="javascript:removeItem(\''.$scrub_nurses_name[$x]["id"].'\',$(\'scrub_nurse_list\'),\'nurse_scrub[]\')">'.
										'    <img src="../../../images/btn_delitem.gif" border="0"/></a>'.
										'</td>'.
										'<td>&nbsp;</td>'.
										'<td align="left">'.
										'    <span style="font:bold 12px Arial;color:#660000">'.$scrub_nurses_name[$x]["name"].'</span>'.
										'    <input id="rowID'.$scrub_nurses_name[$x]["id"].'" type="hidden" value="'.$scrub_nurses_name[$x]["id"].'" name="nurse_scrub[]"/>'.
										'</td>'.
								'</tr>';
								 $x++;
								 $y+=$x;
						 }
				 }
				 else
				 {
						 $scrub_nurses_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				 }

				 $rotating_nurses = unserialize($row['rotating_nurse']);
				 if($rotating_nurses)
				 {
						 $x=0;
						 $y=$x+1;
						 while($x<count($rotating_nurses))
						 {
								 $query = "SELECT concat(ifnull(cp.name_first,' '), ifnull(cp.name_middle,' '), ifnull(cp.name_last,' ')) as `name` ".
								 "FROM care_person AS cp INNER JOIN care_personell as cr ON cp.pid=cr.pid ".
								 "WHERE cr.nr=".$db->qstr($rotating_nurses["rotating_nurse+".$y]);
								 $rotating_nurses_name[] = array("name"=>$db->GetOne($query), "id"=>$rotating_nurses["rotating_nurse+".$y]);
								 $rotating_nurses_list.='<tr class="wardlistrow" id="row'.$rotating_nurses_name[$x]["id"].'">'.
										'<td class="center"><a href="javascript:removeItem(\''.$rotating_nurses_name[$x]["id"].'\',$(\'circulating_nurse_list\'),\'nurse_rotating[]\')">'.
										'    <img src="../../../images/btn_delitem.gif" border="0"/></a>'.
										'</td>'.
										'<td>&nbsp;</td>'.
										'<td align="left">'.
										'    <span style="font:bold 12px Arial;color:#660000">'.$rotating_nurses_name[$x]["name"].'</span>'.
										'    <input id="rowID'.$rotating_nurses_name[$x]["id"].'" type="hidden" value="'.$rotating_nurses_name[$x]["id"].'" name="nurse_rotating[]"/>'.
										'</td>'.
								'</tr>';
								 $x++;
								 $y+=$x;
						 }
				 }
				 else
				 {
						 $rotating_nurses_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				 }

				 $anesthesiologists = unserialize($row['an_doctor']);
				 if($anesthesiologists)
				 {
						 $x=0;
						 $y=$x+1;
						 while($x<count($anesthesiologists))
						 {
								 $query = "SELECT concat(ifnull(cp.name_first,' '), ifnull(cp.name_middle,' '), ifnull(cp.name_last,' ')) as `name` ".
								 "FROM care_person AS cp INNER JOIN care_personell as cr ON cp.pid=cr.pid ".
								 "WHERE cr.nr=".$db->qstr($anesthesiologists["an_doctor+".$y]);
								 $anesthesiologists_name[] = array("name"=>$db->GetOne($query), "id"=>$anesthesiologists["an_doctor+".$y]);
								 $anesthesiologist_list.='<tr class="wardlistrow" id="row'.$anesthesiologists_name[$x]["id"].'">'.
										'<td class="center"><a href="javascript:removeItem(\''.$anesthesiologists_name[$x]["id"].'\',$(\'anesthesiologist_list\'),\'anesthesiologist[]\')">'.
										'    <img src="../../../images/btn_delitem.gif" border="0"/></a>'.
										'</td>'.
										'<td>&nbsp;</td>'.
										'<td align="left">'.
										'    <span style="font:bold 12px Arial;color:#660000">'.$anesthesiologists_name[$x]["name"].'</span>'.
										'    <input id="rowID'.$anesthesiologists_name[$x]["id"].'" type="hidden" value="'.$anesthesiologists_name[$x]["id"].'" name="anesthesiologist[]"/>'.
										'</td>'.
								'</tr>';
								 $x++;
								 $y+=$x;
						 }
				 }
				 else
				 {
						 $anesthesiologist_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				 }

				}//end of while
		}
		else
		{
				$anesthesiologist_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				$rotating_nurses_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				$scrub_nurses_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				$asst_surgeons_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
				$surgeons_list = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
		}
$smarty->assign('surgeon_list_body', $surgeons_list);
$smarty->assign('asst_surgeon_list_body', $asst_surgeons_list);
$smarty->assign('scrub_nurse_list_body', $scrub_nurses_list);
$smarty->assign('rotating_nurse_list_body', $rotating_nurses_list);
$smarty->assign('anesthesiologist_list_body', $anesthesiologist_list);

#-----end Cha, March 20, 2010-------

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

/** Form elements under fieldset "Request Details" **/
$smarty->assign('required_mark', '<em><img src="'.$root_path.'images/or_main_images/required_mark.png" /></em>');
$smarty->assign('error_input', '<span id="error_form_input"></span>');

$smarty->assign('or_request_department', $department);
$smarty->assign('or_op_room', $operating_room['info']);
$transaction = array('1' => 'Cash', '0' => 'Charge');
#$smarty->assign('or_transaction_type', $transaction[$basic_info['is_cash']]);
$smarty->assign('or_transaction_type', $transaction[$or_main_info['trans_type']]);
#$smarty->assign('or_request_priority', $or_main_info['request_priority']);   not needed as of now


#$request_date = date('F d, Y h:ia', strtotime($basic_info['request_date'].' '.$basic_info['request_time']));  not needed  as of now
#$smarty->assign('or_request_date', $request_date);  not needed as of now
$smarty->assign('or_request_date', date('F d, Y h:ia', strtotime($or_main_info['date_request'])));
#$smarty->assign('or_consent_signed', ($or_main_info['consent_signed'] == 1) ? 'Yes' : 'No');   not needed as of now
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

$operation_date = date('F d, Y', strtotime($or_main_info['date_operation'])).'  '.$time_operation;
$smarty->assign('or_operation_date', $operation_date);

$case = array('pay_elective'=>'Pay - Elective', 'pay_stat'=>'Pay - Stat',
														'service_elective'=>'Service - Elective', 'service_stat'=>'Service - Stat');
$smarty->assign('or_request_case', $case[$or_main_info['or_case']]);
/** End **/

/** Form elements under fieldset "Patient Information" **/
#$seg_person = new Person($basic_info['pid']);
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
$smarty->assign('patient_name', check_value(ucwords(strtolower($patient_name))));
$smarty->assign('patient_gender', check_value($patient_gender));
$smarty->assign('patient_age', $patient_age);
$smarty->assign('patient_address', check_value($patient_address));
/** End **/

/** Form elements under fieldset "Pre-operation Details" **/

/*$operation_date = date('F d, Y h:ia', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));
$smarty->assign('or_operation_date', $operation_date);

$operation_date_display = date('F d, Y h:ia', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));
$operation_date = date('Y-m-d H:i', strtotime($basic_info['op_date'].' '.$basic_info['op_time']));

$smarty->assign('or_operation_date_display', '<div id="or_operation_date_display" class="date_display">'.$operation_date_display.'</div>');
$smarty->assign('or_operation_date_value', '<input type="hidden" name="or_operation_date" id="or_operation_date" value="'.$operation_date.'" />');
$smarty->assign('or_operation_dt_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="or_operation_dt_picker" class="date_time_picker" />');
$smarty->assign('or_operation_calendar_script', setup_calendar('or_operation_date_display', 'or_operation_date', 'or_operation_dt_picker'));
*/



#$or_est_op_length = $or_main_info['est_length_op'];   not needed as of now
#$pre_operative_diagnosis = $or_main_info['pre_op_diagnosis'];  not needed as of now
$operation_procedure = $or_main_info['or_procedure'];
#echo "or procedure= ".$or_main_info['or_procedure'];
$smarty->assign('operation_procedure', check_value($operation_procedure));
#$smarty->assign('or_est_op_length', check_value($or_est_op_length));  not needed as of now
#$smarty->assign('pre_operative_diagnosis', check_value($pre_operative_diagnosis));  not needed as of now
/*$classification = array('clean'=>'Clean', 'contaminated'=>'Contaminated',
																								'wound_dehiscence'=>'Wound Dehiscence',
																								'clean_contaminated'=>'Clean/Contamianted',
																								'dirty_infected'=>'Dirty/Infected');
$smarty->assign('or_case_classification', $classification[$or_main_info['case_classification']]);
*/
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
#$smarty->assign('submit_schedule', '<input type="submit" id="or_main_schedule_submit" value="" />');
#$smarty->assign('cancel_schedule', '<a href="'.$breakfile.'" id="schedule_cancel"></a>');
$smarty->assign('submit_pre_operation', '<input type="submit" id="pre_op_submit" value="" />');
$smarty->assign('cancel_pre_operation', '<a href="'.$breakfile.'" id="schedule_cancel"></a>');
#$encounter_nr = $basic_info['encounter_nr'];
#$hospital_number = $basic_info['pid'];
#$hospital_number = $pid;
$op_date = $or_main_info['date_operation'];
#echo "op date sa baba= ".$op_date."<br>";
#echo "encounter_nr= ".$encounter_nr."<br>";
#echo "refno= ".$refno."<br>";
#echo "pid sa baba= ".$pid."<br>";
$list_surgeon = implode(", ",$surgeon_list);

$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
#$smarty->assign('hospital_number', '<input type="hidden" name="hospital_number" id="hospital_number" value="'.$hospital_number.'" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('op_nr', '<input type="hidden" name="op_nr" value="'.$op_nr.'" />');
$smarty->assign('op_room', '<input type="hidden" name="op_room" value="'.$op_room.'" />');
$smarty->assign('dept_nr', '<input type="hidden" name="dept_nr" value="'.$dept_nr.'" />');
$smarty->assign('refno', '<input type="hidden" name="refno" value="'.$refno.'" />');
$smarty->assign('or_request_nr', '<input type="hidden" name="or_request_nr" value="'.$nr.'" />');
$smarty->assign('or_main_refno', '<input type="hidden" name="or_main_refno" value="'.$or_main_info['or_main_refno'].'" />');
$smarty->assign('operation_date', '<input type="hidden" name="operation_date" value="'.$op_date.'" />');
$smarty->assign('pid', '<input type="hidden" name="pid" id="pid" value="'.$pid.'" />');
$smarty->assign('list_surgeon', '<input type="hidden" name="list_surgeon" id="list_surgeon" value="'.$list_surgeon.'"/>');
/** End **/

$smarty->assign('sMainBlockIncludeFile','or/or_asu_pre_operative.tpl'); //Assign the or_main template to the frameset
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
}


function toggle_details() {
		J('#request_details').slideToggle('100');
}

//------------Added by Cherry May 28, 2010------------------
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
//--------------------------End-----------------------------

function validate() {
 var errors = new Array();
 var errors_field = new Array();
 var reg = /^[-+]?[0-9]+(\.[0-9]+)?$/;
 var surgeon_id = document.getElementsByName('surgeon[]');
 var anesthesiologist_id = document.getElementsByName('anesthesiologist[]');
 var asst_surgeon_id = document.getElementsByName('surgeon_assist[]');
 var cnt1 = 0;
 var cnt2 = 0;
 var checker = 0;

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

 if (surgeon_id.length == 0) {
		 errors.push('Please add a surgeon for this operation.');
 }
 if (anesthesiologist_id.length == 0) {
		 errors.push('Please add an anesthesiologist for this operation');
 }
 //Added by Cherry 06-02-10
 for (cnt1=0; cnt1<surgeon_id.length; cnt1++) {
		for (cnt2=0; cnt2<asst_surgeon_id.length; cnt2++) {
				if (surgeon_id[cnt1].value == asst_surgeon_id[cnt2].value) {
						checker++;
				}
		}
 }

 if (checker > 0) {
		errors.push('Persons added as surgeons must not be re-added as assistant surgeons');
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
 //end celsy


 /*if (asst_surgeon_id[0]) {
		 errors.push('asst_surgeon= '+asst_surgeon_id[0].value);
		 //errors.push('Please add an assistant surgeon for this operation');
 }*/

 /*if (surgeon_id.value == asst_surgeon_id.value) {
		 errors.push('The surgeon and the asst surgeon should not be the same person');
 }*/

		if (errors.length > 0) {
				for (var i=0; i<errors.length; i++) {
						alert(errors[i]);
						if(errors[i]=='Fill out checkbox details') {
							detail.focus();
						}
				}
				return false;
		}
		else {
				return true;
		}
}

//commen out by CHa, March 20, 2010//
/*xajax_populatePersonnel(<?=$refno? $refno:0?>,7,'surgeon_list','surgeon[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,8,'assistant_surgeon_list','surgeon_assist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,12,'anesthesiologist_list','anesthesiologist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,9,'scrub_nurse_list','nurse_scrub[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,10,'circulating_nurse_list','nurse_rotating[]');*/
</script>
