<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');   
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
include_once($root_path."include/care_api_classes/class_order.php");  //load the SegOrder class
require_once($root_path.'include/inc_date_format_functions.php'); //load the date format functions
require($root_path.'modules/or/ajax/op-request-new.common.php'); //load the xajax module
$seg_ops = new SegOps(); 
$seg_order = new SegOrder('pharma');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG); 
$glob_obj->getConfig('refno_%');
if($glob_obj->getConfig('date_format')) {
  $date_format=$GLOBAL_CONFIG['date_format'];
}                                                                                                                     

/**
* Start: Omick December 04, 2008
* Changes the date format to
* %m/%d/%Y
*/

$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
// End
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
 echo '<script>';
   require_once($root_path.'include/inc_checkdate_lang.php');
 echo '</script>';
$smarty = new Smarty_Care('or_main');

/** Call the javascripts Start */

$javascript_array = array ('<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/op-request-new.js?t='.time().'"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/NumberFormat154.js"></script>'
                          ,'<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
                          ,'<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'
                          ,'<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'
                          ,'<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'
                          ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
                          ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/order-gui.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/pharmacy/js/tray-gui.js?t='.time().'"></script>'
                          ,$xajax->printJavascript($root_path.'classes/xajax-0.2.5'));
$smarty->assign('javascript_array', $javascript_array);
/** Call the javascripts End */

$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');

$smarty->assign('sToolbarTitle',"Operating Room :: Main "); //Assign a toolbar title
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
$smarty->assign('breakfile',$breakfile); //Close button
//$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

switch ($mode) {
    /*
    case 'save':
      $data = array(
                'refno' => $_POST['refno'],
                'nr' => $_POST['op_request_nr'],
                'request_date' => formatDate2STD($_POST['request_date'], $date_format),
                'request_time' => date('H:i:s'),
                'encounter_nr' => $_POST['encounter_nr'],
                'pid' => $_POST['pid'],
                'ordername' => $_POST['patient_name'],
                'orderaddress' => $_POST['patient_address'],
                'is_cash' =>  0, //temporarily
                'hasPaid' =>  '',
                'is_urgent' => 0, //temporarily
                'comments' =>  'Whatever',
                'status' =>  '',
                'history' => 'Create '.date('Y-m-d H:i:s').' '.$_SESSION['sess_user_name']." \n",
                'ops_code' => $_POST['ops_code'],
                'rvu' => $_POST['rvu'],
                'multiplier' => $_POST['multiplier'],
                'surgeon' => $_POST['surgeon'],
                'surgeon_assist' =>  $_POST['surgeon_assist'],
                'anesthesiologist' => $_POST['anesthesiologist'],
                'nurse_scrub' => $_POST['nurse_scrub'],
                'nurse_rotating' => $_POST['nurse_rotating'],
                'dept_nr' => $_POST['department'],
                'op_room' => $_POST['operating_room'],
                'op_nr' => $_POST['op_nr'],
                'op_date' => $_POST['operation_date'],
                'op_time' => $_POST['operation_started'] . ' ' . $_POST['os_meridian'],
                'diagnosis' => $_POST['operation_diagnosis'],
                'op_therapy' => $_POST['operation_performed']
               );
               
      if ($reference_number = $seg_ops->saveOpsBilling($data)) {
        $consent_signed = array('yes'=>'1', 'no'=>'0');
        $consent_signed = $consent_signed[$_POST['consent_signed']];
   
          $anesthesias = array('anesthesia_procedure' => $_POST['anesthesia_procedure'],
                               'anesthetics' => $_POST['anesthetics'],
                               'time_begun' => $_POST['time_begun'],
                               'tb_meridian' => $_POST['tb_meridian'],
                               'time_ended' => $_POST['time_ended'],
                               'te_meridian' => $_POST['te_meridian']);
          $data = array('request_type' =>  $_POST['request_type'],
                      'pre_operative' => $_POST['pre_op_diagnosis'],
                      'proposed_surgery' => $_POST['proposed_surgery'],
                      'consent_signed' => $consent_signed,
                      'case' => $_POST['case'],
                      'case_classification' => $_POST['case_classification'],
                      'operation_start' => date('H:i:s', strtotime($_POST['operation_started'].' '.$_POST['os_meridian'])),
                      'operation_end' => date('H:i:s', strtotime($_POST['operation_ended'].' '.$_POST['oe_meridian'])));
        
          $seg_ops->insert_care_encounter_op_main($reference_number, $data);
          $seg_ops->insert_care_encounter_anesthesia($reference_number, $anesthesias);   
          extract(calculate_total_pharma_order());
          $order_data = array(
            'refno'=>$seg_order->getLastNr(date("Y-m-d")),
            'encounter_nr'=>$_POST['encounter_nr'],
            'pharma_area'=>'OR',
            'pid'=>$_POST['pid'],
            'ordername'=>$_POST['patient_name'],
            'orderaddress'=>$_POST['patient_address'],
            'orderdate'=>formatDate2STD($_POST['request_date'], $date_format),
            'is_cash'=> 0,  //temp
            'amount_due'=>$total,
            'is_tpl'=> 1, //temp
            'discount'=>$_POST['discount'],
            'discountid'=>$_POST['discountid'],
            'is_urgent'=>$_POST['priority'],
            'comments'=>'', //temp
            'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
            'create_id'=>$_SESSION['sess_temp_userid'],
            'modify_id'=>$_SESSION['sess_temp_userid'],
            'modify_time'=>date('YmdHis'),
            'create_time'=>date('YmdHis')
            );
         
         $seg_order->setDataArray($order_data);
         $saveok = $seg_order->insertDataFromInternalArray();
         if ($saveok) {
            $seg_order->clearOrderList($order_data['refno']);
            $seg_order->addOrders($order_data['refno'],$bulk);
            
            $bulk = array();
            if ($_POST['issc']) $bulk[] = 'SC';
            foreach ($_POST["discount"] as $i=>$v) {
                if ($v) $bulk[] = array($v);
            }
            $seg_order->clearDiscounts($order_data['refno']);
            if ($bulk) {
                $seg_order->addDiscounts($order_data['refno'],$bulk);
            }
                      
            $seg_ops->insert_care_encounter_pharma_order($order_data['refno'], $reference_number);
         }         
   }
   break; */
}
$refno = '2008000021';
$op_request_nr = $seg_ops->getOpRequestNrByRefNo($refno);
echo $op_request_nr;
if ($refno = $seg_ops->encOpsNrHasOpsServ($op_request_nr)){ 
    echo '<br/>yah ' . $refno;
    if ($ref_basic_info = $seg_ops->getAllEncounterOpsServiceInfo($op_request_nr)) {
        echo '<pre>';
        print_r($ref_basic_info);
        echo '</pre>';
        extract($ref_basic_info);
        //$or_main_basic_info = $seg_ops->get_or_main_basic_info($op_request_nr);
        extract($or_main_basic_info);                                 
        
        
    }
}
/** Form and form elements creation Start **/                                                                             
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');
$smarty->assign('request_type', array('emergency'=>'Emergency', 'elective'=>'Elective'));
$smarty->assign('selected_request', 'emergency');



$var_arr = array(
            "var_pid" => "pid",
            "var_name" => "patient_name",
            "var_addr" => "patient_address",
            "var_encounter_nr" => "encounter_nr",
            "var_discountid" => "discountid",
            "var_discount" => "discount"
        );
        $vas = array();
        foreach($var_arr as $i=>$v) {
            $vars[] = "$i=$v";
        }
        $var_qry = implode("&",$vars);

$smarty->assign('patient_name', '<input type="text" name="patient_name" id="patient_name" class="segInput" value="'.$ordername.'" />');
$smarty->assign('patient_select','<img name="select-enc" id="select-enc" src="../../../images/btn_encounter_small.gif" style="cursor:pointer; vertical-align:bottom" 
        onclick="overlib(
        OLiframeContent(\''.$root_path."modules/registration_admission/seg-select-enc.php?$var_qry',".'700, 400, \'fSelEnc\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
        CAPTIONPADDING,2, CAPTION,\'Select registered person\',MIDX,0, MIDY,0, STATUS,\'Select registered person\'); return false;"
        onmouseout="nd();" />');
$smarty->assign('pre_op_diagnosis', '<input type="text" name="pre_op_diagnosis" id="pre_op_diagnosis" class="segInput" value="'.$pre_operative.'" />');
$smarty->assign('proposed_surgery', '<input type="text" name="proposed_surgery" id="proposed_surgery" class="segInput" value="'.$proposed_surgery.'" />');

$consent_signed = (!$consent_signed) ? 'no':'yes';
$smarty->assign('consent_signed', array('yes'=>'Yes', 'no'=>'No'));
$smarty->assign('consent_signed_selected', $consent_signed);

$priority_selected = (!$is_urgent) ? '0':'1';
$smarty->assign('priority', array('0'=>'Normal', '1'=>'Urgent'));
$smarty->assign('priority_selected', $priority_selected);
$options_service = array('service_elective'=>'Elective', 'service_stat'=>'Stat');
$options_pay = array('pay_elective'=>'Elective', 'pay_stat'=>'Stat');
$smarty->assign('case_selected', 'service_elective');
$smarty->assign('service', $options_service);
$smarty->assign('pay', $options_pay);
$smarty->assign('op_case_selected', $op_case);

$smarty->assign('case_classification', array('clean'=>'Clean',
                                             'contaminated'=>'Contaminated',
                                             'wound_dehiscence'=>'Wound Dehiscence',
                                             'clean_contaminated'=>'Clean/Contamianted',
                                             'dirty_infected'=>'Dirty/Infected'));
$smarty->assign('case_classification_selected', $case_classification);                                               
$smarty->assign('add_surgical_memo','<img '.createLDImgSrc($root_path,'add.gif','0','center').' alt="Add Surgical Memorandum" name="surgical_memo_button" id="surgical_memo_button" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('add_anesthesia_procedure','<img '.createLDImgSrc($root_path,'add.gif','0','center').' alt="Add Anesthesia Procedure" name="anesthesia_procedure_button" id="anesthesia_procedure_button" class="jqModal" onclick="clickme()" style="cursor:pointer">');    
//$dept_nr = 159;
$onclick_event='onclick="overlib(
        OLiframeContent(\'seg-op-request-select-personnel.php?personnel_type=surgeon&dept_nr='.$dept_nr.'&table_name=surgeon-list\', 400, 300, \'fSelBatchNr\', 1, \'auto\'),
        WIDTH,400, TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, CAPTION,\'Select surgeon\',MIDX,0, MIDY,0,STATUS,\'Select surgeon\'); return false;"onmouseout="nd();"';
$smarty->assign('add_surgeon','<img '.createLDImgSrc($root_path,'add.gif','0','center').' alt="Add Surgeon" name="surgeon_button" id="surgeon_button" onsubmit="return false;" style="cursor:pointer" '.$onclick_event.'>');

$onclick_event='onclick="overlib(
        OLiframeContent(\'seg-op-request-select-personnel.php?personnel_type=surgeon_assist&dept_nr='.$dept_nr.'&table_name=asst-surgeon-list\', 400, 300, \'fSelBatchNr\', 1, \'auto\'),
        WIDTH,400,TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, CAPTION,\'Select surgeon\',MIDX,0, MIDY,0,STATUS,\'Select surgeon\'); return false;"onmouseout="nd();"';
$smarty->assign('add_asst_surgeon','<img '.createLDImgSrc($root_path,'add.gif','0','center').' alt="Add Assistant Surgeon" name="asst_surgeon_button" id="asst_surgeon_button" onsubmit="return false;" style="cursor:pointer" '.$onclick_event.'>');

$onclick_event='onclick="overlib(
        OLiframeContent(\'seg-op-request-select-personnel.php?personnel_type=anesthesiologist&dept_nr='.$dept_nr.'&table_name=anesthesiologist-list\', 400, 300, \'fSelBatchNr\', 1, \'auto\'),
        WIDTH,400,TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4,CAPTION,\'Select anesthesiologist\',MIDX,0, MIDY,0,STATUS,\'Select anesthesiologist\'); return false;"
        onmouseout="nd();"';
$smarty->assign('add_anesthesiologist','<img '.createLDImgSrc($root_path,'add.gif','0','center').' alt="Add Anesthesiologist" name="anesthesiologist_button" id="anesthesiologist_button" onsubmit="return false;" style="cursor:pointer" '.$onclick_event.'>');

$onclick_event='onclick="overlib(
        OLiframeContent(\'seg-op-request-select-personnel.php?personnel_type=nurse_scrub&dept_nr='.$dept_nr.'&table_name=nurse-scrub-list\', 400, 300, \'fSelBatchNr\', 1, \'auto\'),
        WIDTH,400,TEXTPADDING,0, BORDER,0,STICKY,SCROLL,CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4,CAPTION,\'Select scrub nurse\',MIDX,0, MIDY,0,STATUS,\'Select scrub nurse\'); return false;"
        onmouseout="nd();"';
$smarty->assign('add_scrub_nurse','<img '.createLDImgSrc($root_path,'add.gif','0','center').' alt="Add Scrub Nurse" name="scrub_nurse_button" id="scrub_nurse_button" onsubmit="return false;" style="cursor:pointer" '.$onclick_event.'>');

$onclick_event='onclick="overlib(
        OLiframeContent(\'seg-op-request-select-personnel.php?personnel_type=nurse_rotating&dept_nr='.$dept_nr.'&table_name=nurse-rotating-list\', 400, 300, \'fSelBatchNr\', 1, \'auto\'),
        WIDTH,400,TEXTPADDING,0, BORDER,0,STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4,CAPTION,\'Select circulating nurse\',MIDX,0, MIDY,0,STATUS,\'Select circulating nurse\'); return false;"
        onmouseout="nd();"';
$smarty->assign('add_circulating_nurse','<img '.createLDImgSrc($root_path,'add.gif','0','center').' alt="Add Circulating Nurse" name="circulating_nurse_button" id="circulating_nurse_button" onsubmit="return false;" style="cursor:pointer" '.$onclick_event.'>');

$default_request_date= ($request_date)? date('m/d/Y', strtotime($request_date)):date("m/d/Y");
$default_operation_date = ($op_date) ?   date('m/d/Y', strtotime($request_date)):date("m/d/Y");


$request_date_calendar = setup_calendar('request_date', $phpfd, 'request_date_trigger', $default_request_date);
$smarty->assign('request_date', $request_date_calendar['field']);
$smarty->assign('request_date_cal_icon', $request_date_calendar['icon']);

$operation_date_calendar = setup_calendar('operation_date', $phpfd, 'operation_date_trigger', $default_operation_date);
$smarty->assign('operation_date', $operation_date_calendar['field']);
$smarty->assign('operation_date_cal_icon', $operation_date_calendar['icon']);

$smarty->assign('operation_started', '<input type="text" name="operation_started" id="operation_started" class="segInput" value="'.$operation_start.'" />');
$options = array('AM'=>'AM', 'PM'=>'PM');
$smarty->assign('os_meridian_opts', $options);

$os_meridian = ($os_meridian == 'PM') ? 'PM':'AM';  //For mode update only
$smarty->assign('os_meridian_selected', $os_meridian);

$smarty->assign('operation_ended', '<input type="text" name="operation_ended" id="operation_ended" class="segInput" value="'.$operation_end.'" />');
$options = array('AM'=>'AM', 'PM'=>'PM');
$smarty->assign('oe_meridian_opts', $options);
 
$oe_meridian = ($oe_meridian == 'PM') ? 'PM':'AM';
echo 'OE is '.$oe_meridian;
$smarty->assign('oe_meridian_selected', $oe_meridian);

$smarty->assign('operation_performed', '<textarea name="operation_performed" cols="101">'.$op_therapy.'</textarea>');
$smarty->assign('operation_diagnosis', '<textarea name="operation_diagnosis" cols="101">'.$diagnosis.'</textarea>');
$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
       onclick="return overlib(
        OLiframeContent(\'seg-op-tray.php\', 625, 380, \'fOrderTray\', 1, \'auto\'),
        WIDTH, 380, TEXTPADDING,0, BORDER,0, 
                STICKY, SCROLL, CLOSECLICK, MODAL, 
                CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
                CAPTION,\'Add procedure codes from ICPM tray\',
        MIDX,0, MIDY,0, 
        STATUS,\'Add procedure codes from ICPM tray\');"
       onmouseout="nd();">
             <img name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_addicpm.gif" border="0"></a>');
# <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');

$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()">
        <input type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_emptylist.gif" border="0"></a>');
$smarty->assign('department', '<input type="hidden" name="department" id="department" value="'.$dept_nr.'" />');
$smarty->assign('operating_room', '<input type="hidden" name="operating_room" id="operating_room" value="'.$op_room.'" />');
$smarty->assign('op_nr', '<input type="hidden" name="op_nr" id="op_nr" value="'.$op_nr.'" />');
$smarty->assign('supplies_add_button','<img class="segSimulatedLink" id="supplies_add" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="return openOrderTray();">');
$smarty->assign('is_senior','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');

/** Form and form elements creation End **/


$smarty->assign('sMainBlockIncludeFile','or/or_main.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

function setup_calendar($input_field, $date_format, $image_icon, $default_value) {
global $root_path;


$calendar_script = "
        <script type=\"text/javascript\">
            Calendar.setup ({
                inputField : \"$input_field\", ifFormat : \"$date_format\", showsTime : false, button : \"$image_icon\", singleClick : true, step : 1
            });
        </script>
        ";
$calendar = array();

$calendar['field'] = '<input name="'.$input_field.'" id="'.$input_field.'" type="text" size="10" 
                                            value="'.$default_value.'" style="font:bold 12px Arial"
                                            onFocus="this.select();"  
                                            onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
                                            onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
                                            onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">';
$calendar['icon'] = '<img '. createComIcon($root_path,'show-calendar.gif','0') . ' id="'.$image_icon.'" align="absmiddle" style="cursor:pointer">'.$calendar_script;
return $calendar;
}

function calculate_total_pharma_order() {
  $bulk = array();
  $orig = $_POST['iscash'] ? $_POST['pcash'] :  $_POST['pcharge'];
  $total = 0;
  foreach ($_POST["items"] as $i=>$v) {
    $consigned = in_array($v, $_POST['consigned']) ? '1' : '0';
    $bulk[] = array($_POST["items"][$i],$_POST["qty"][$i],
    parseFloatEx($_POST["prc"][$i]),
    parseFloatEx($_POST["prc"][$i]), 
    $consigned, $orig[$i]);
    $total += (parseFloatEx($_POST["prc"][$i]) * (float) $_POST["qty"][$i]);
  }
  return array('total' => $total, 'bulk' => $bulk);
}

function listPersonnel($position, $pers_info, $position_title){
        if (is_array($pers_info) && !empty($pers_info)){
            $i = 1;
            foreach($pers_info as $pers_nr=>$pers_pidName){
                $list .= '<input type="hidden" name="'.$position.'[]" id="'.$position.$pers_nr.'" value="'.$pers_nr.'">';
                $list .= '['.$i.'] '.$pers_pidName['name']."<br>\n";
                $i++;
            }
            $list = '<span style="text-align:justify;color:#000000;">'."\n".$list.'</span>';    
        }else{
            $list = '<span style="text-align:center;color:#FF0000;font-weight:bold;">No '.$position_title.'</span>';
        }
        return $list;
}


?>
<script>
xajax_populateOpsCodeListByRefNo(<?=$refno? $refno:0?>);
xajax_populatePersonnel(<?=$refno? $refno:0?>,7,'surgeon-list','surgeon[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,8,'asst-surgeon-list','surgeon_assist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,12,'anesthesiologist-list','anesthesiologist[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,9,'nurse-scrub-list','nurse_scrub[]');
xajax_populatePersonnel(<?=$refno? $refno:0?>,10,'nurse-rotating-list','nurse_rotating[]');
xajax_populate_or_main_anesthesia(<?=$refno? $refno:0?>);
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
</script>
