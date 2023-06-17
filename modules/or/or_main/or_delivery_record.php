<?php
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

$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;

$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room Main :: OR Delivery Record"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room Main :: OR Delivery Record");

$smarty->assign('breakfile', $breakfile);
$smarty->assign('check_date_string', $check_date_string);
$smarty->assign('or_delivery_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/delivery.css" type="text/css" />');

$javascript_array = array('<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
                          ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
                          ,'<script>var J = jQuery.noConflict();</script>'
                          ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
                          ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqDnR.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/dimensions.js"></script>'
                          ,'<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'
                          , $xajax->printJavascript($root_path.'classes/xajax-0.2.5')
                          );
$smarty->assign('javascript_array', $javascript_array);

$var_arr = array(
            "var_pid" => "pid",
            "var_name" => "patient_name",
            "var_age" => "patient_age",
            "var_encounter_nr" => "encounter_nr",
            "var_date_admitted" => "date_admitted",
            "var_room_ward" => "room_ward",
            "var_ref_no" => "ref_no"                 #Added by CHA 10-09-09
            );
foreach($var_arr as $i=>$v) {
  $vars[] = "$i=$v";
}
$var_qry = implode("&",$vars);

/** Patient Details **/
$smarty->assign('patient_name', '<input type="text" name="patient_name" id="patient_name" readonly="readonly" onclick="select_patient(\''.$var_qry.'\')" />');
$smarty->assign('patient_age', '<input type="text" name="patient_age" id="patient_age" readonly="readonly" onclick="select_patient(\''.$var_qry.'\')" />');
$smarty->assign('date_admitted', '<input type="text" name="date_admitted" id="date_admitted" readonly="readonly" onclick="select_patient(\''.$var_qry.'\')" />');
$smarty->assign('room_ward', '<input type="text" name="room_ward" id="room_ward" readonly="readonly" onclick="select_patient(\''.$var_qry.'\')" />');
$smarty->assign('bed_num', '<input type="text" name="bed_num" id="bed_num" readonly="readonly"/>');
$smarty->assign('hosp_num', '<input type="text" name="hosp_num" id="hosp_num" readonly="readonly"/>');
$smarty->assign('date_confinement', '<input type="text" name="date_confinement" id="date_confinement"/>');
$smarty->assign('physician', '<input type="text" name="physician" id="physician" readonly="readonly"/>');
$smarty->assign('gravida', '<input type="text" name="gravida" id="gravida" />');
$smarty->assign('para', '<input type="text" name="para" id="para" />');
$smarty->assign('abortion', '<input type="text" name="abortion" id="abortion"/>');
#$smarty->assign('prenatal_care', array(0=>'Yes', 1=>'No'));
#$smarty->assign('blood_type', array('A'=>'A', 'B'=>'B', 'O'=>'O', 'AB'=>'AB'));
$smarty->assign('pregnancy_complications', '<textarea name="pregnancy_complications" id="pregnancy_complications" style="width:250px"></textarea>');
/** End: Patent Details **/

/** Labor Details **/	#edited by cha 11-10-09
$smarty->assign('heart', '<input type="text" name="heart" id="heart" style="width:110px" />');
$smarty->assign('lungs', '<input type="text" name="lungs" id="lungs" style="width:110px"/>');
$smarty->assign('bp_1', '<input type="text" name="bp_1" id="bp_1" style="width:110px"/>');
$smarty->assign('pulse_1', '<input type="text" name="pulse_1" id="pulse_1" style="width:110px" />');
#$smarty->assign('general_condition', array('good'=>'Good', 'fair'=>'Fair', 'critical'=>'Critical', 'febrile'=>'Febrile', 'morbid'=>'Morbid', 'others'=>'Others'));
#$smarty->assign('membrane_ruptured', array('spontaneous'=>'Spontaneous', 'artificial'=>'Artificial', 'cervix_dilates'=>'Cervix Dilates'));
$smarty->assign('cervix_cm', '<input type="text" name="cervix_cm" id="cervix_cm" style="width:50px"/>');
#$smarty->assign('cervix_condition', array('premature'=>'Premature', 'early'=>'Early', 'late'=>'Late'));
#$smarty->assign('labor_onset', array('induced'=>'Induced', 'spontaneous'=>'Spontaneous'));
$smarty->assign('onset_date_time', '<input type="text" name="onset_date_time" id="onset_date_time" class="right_aligned" />');
$smarty->assign('dilation_date_time', '<input type="text" name="dilation_date_time" id="dilation_date_time" class="right_aligned" />');
$smarty->assign('childborn_date_time', '<input type="text" name="childborn_date_time" id="childborn_date_time" class="right_aligned" />');
$smarty->assign('ergonovine_date_time', '<input type="text" name="ergonovine_date_time" id="ergonovine_date_time" class="right_aligned" />');
#$smarty->assign('delivery_spont', array(0=>'Yes', 1=>'No'));
$smarty->assign('blood_given', '<input type="text" name="blood_given" id="blood_given" style="width:50px" />');
$smarty->assign('operative', '<textarea name="operative" id="operative" style="width:510px"></textarea>');
$smarty->assign('episiotomy', '<input type="text" name="episiotomy" id="episiotomy" style="width:110px" />');
#$smarty->assign('perineal_tear', array(0=>'Yes', 1=>'No'));
$smarty->assign('analgesic_given', '<textarea name="analgesic_given" id="analgesic_given" style="width:310px"></textarea>');
$smarty->assign('anesthesia_given', '<textarea name="anesthesia_given" id="anesthesia_given" style="width:310px"></textarea>');
$smarty->assign('complications', '<textarea name="complications" id="complications" style="width:310px"></textarea>');
$smarty->assign('fundus', '<input type="text" name="fundus" id="fundus" style="width:210px" />');
$smarty->assign('umbiculus', '<input type="text" name="umbiculus" id="umbiculus" style="width:210px" />');
$smarty->assign('post_bp', '<input type="text" name="post_bp" id="post_bp" style="width:110px" />');
$smarty->assign('post_temp', '<input type="text" name="post_temp" id="post_temp" style="width:110px" />');
$smarty->assign('post_pulse', '<input type="text" name="post_pulse" id="post_pulse" style="width:110px" />');
$smarty->assign('post_resprate', '<input type="text" name="post_resprate" id="post_resprate" style="width:110px" />');
#$smarty->assign('bleeding', array('normal'=>'Normal', 'moderate'=>'Moderate', 'execessive'=>'Excessive'));

$stage_range = range(0, 10);
$hour_range = range(0, 24);
$minute_range = range(0, 59);
$smarty->assign('stage', $stage_range);
$smarty->assign('hour', $hour_range);
$smarty->assign('minute', $minute_range);
/** End: Labor Details **/

/** Other details **/
$smarty->assign('pid', '<input type="hidden" name="pid" id="pid" />');
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" />');
$smarty->assign('ref_no', '<input type="hidden" name="ref_no" id="ref_no" />');                     #Added by CHA 10-09-09
$smarty->assign('submit_dr_record', '<input type="submit" id="or_dr_record_submit" value="" />');
$smarty->assign('cancel_dr_record', '<a href="'.$breakfile.'" id="or_dr_record_cancel"></a>');	#Edited by CHA 11-10-09
$smarty->assign('form_start', '<form name="or_dr_record_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_end', '</form>');
/** End: Other details **/

/** popup details **/
$number_of_pages = array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30');
$smarty->assign('number_of_pages', $number_of_pages);
$smarty->assign('page_number', '<input type="text" id="page_number" name="page_number" />');
$smarty->assign('search_field', '<input type="text" id="search_field" name="search_field" />');
$smarty->assign('departments', $list_dept);
$smarty->assign('profession', array(0=>'Select Role',
                                    7=>'Surgeon',
                                    8=>'Assistant Surgeon',
                                    12=>'Anesthesiologist',
                                    9=>'Circulating Nurse',
                                    10=>'Scrub Nurse'));
$smarty->assign('search_button', '<input type="submit" id="search_button" value="Search" />');
/** end: popup details **/

/** after submittig **/
#print_r($_POST);
//added by CHA 10-09-09
if($_POST)
{
  $opsObj = new SegOps();
  $result = $opsObj->saveOrDelivery($_POST);
	#echo "<br><br>result: ".$result;
	if($opsObj->saveOrDelivery($_POST))
	{
	    $smarty->assign('sysInfoMessage','OR Delivery details successfully saved.');
  }
  else if($opsObj->updateOrDelivery($_POST))
  {
  		$smarty->assign('sysInfoMessage','OR Delivery details successfully updated.');
	}
  else {
    $smarty->assign('sysInfoMessage','OR Delivery details not saved.');
  }
}
//end CHA

//added by CHA 10-23-09
$smarty->assign('or_delivery_record_report', '<a href="javascript:void(0)" onclick="openReport(this.id);" id="or_delivery_record_report"></a>');
//end cha

$smarty->assign('sMainBlockIncludeFile','or/or_delivery_record.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame
?>

<script>
//select_patient('<?=$var_qry?>')
var placenta_prev_hour_val = 0;
var placenta_prev_min_val = 0;
var baby_prev_hour_val = 0;
var baby_prev_min_val = 0;
var cervix_prev_hour_val = 0;
var cervix_prev_min_val = 0;
var onset_prev_hour_val = 0;
var onset_prev_min_val = 0;
var membranes_prev_hour_val = 0;
var membranes_prev_min_val = 0;

/*Calendar.setup ({
  inputField : "membranes_date_time",
  ifFormat : "%m/%d/%Y %H:%M",
  showsTime : true,
  button : "membranes_date_time",
  singleClick : true,
  step : 1
});
Calendar.setup ({
  inputField : "cervix_date_time",
  ifFormat : "%m/%d/%Y %H:%M",
  showsTime : true,
  button : "cervix_date_time",
  singleClick : true,
  step : 1
});
Calendar.setup ({
  inputField : "baby_date_time",
  ifFormat : "%m/%d/%Y %H:%M",
  showsTime : true,
  button : "baby_date_time",
  singleClick : true,
  step : 1
});
Calendar.setup ({
  inputField : "placenta_date_time",
  ifFormat : "%m/%d/%Y %H:%M",
  showsTime : true,
  button : "placenta_date_time",
  singleClick : true,
  step : 1
});*/
Calendar.setup ({
  inputField : "onset_date_time",
  ifFormat : "%m/%d/%Y %H:%M",
  showsTime : true,
  button : "onset_date_time",
  singleClick : true,
  step : 1
});
Calendar.setup ({
  inputField : "dilation_date_time",
  ifFormat : "%m/%d/%Y %H:%M",
  showsTime : true,
  button : "dilation_date_time",
  singleClick : true,
  step : 1
});
Calendar.setup ({
  inputField : "childborn_date_time",
  ifFormat : "%m/%d/%Y %H:%M",
  showsTime : true,
  button : "childborn_date_time",
  singleClick : true,
  step : 1
});
Calendar.setup ({
  inputField : "ergonovine_date_time",
  ifFormat : "%m/%d/%Y %H:%M",
  showsTime : true,
  button : "ergonovine_date_time",
  singleClick : true,
  step : 1
});
Calendar.setup ({
  inputField : "date_confinement",
  ifFormat : "%F %d, %Y %h:%ia",
  showsTime : true,
  button : "date_confinement",
  singleClick : true,
  step : 1
});

function select_patient(params) {
var additional = '&var_include_enc=1';
J('#dr_patient_popup').jqmShow();
  J('#delivery_patient_table').flexigrid({
    url: '<?=$root_path?>modules/or/ajax/ajax_or_delivery_patient.php',
    dataType: 'json',
    colModel : [{display: 'Reference Number', width:90, name:'refno', sortable: true, align: 'left'},
                {display: 'Hospital Number', width:100, name:'refno', sortable: true, align: 'left'},
                {display: 'Patient Name', width:110, name:'personell_name', sortable: true, align: 'left'},
                {display: 'Date of Operation', width:100, name:'personell_name', sortable: true, align: 'left'},
                {display: 'OP Room', width:50, name:'personell_name', sortable: true, align: 'left'},
                {display: 'Action', width:80, name:'action', sortable: false, align: 'left'}
                ],
    sortname: ['op_date'],
    domain: ['delivery_personell'],
    sortorder: "desc",
    useRp: true,
    rp: 5,
    qtype: 0,
    resizable: true});
}

J().ready(function() {
  J('#ord_popup').jqm({
    overlay: 80,
    onShow: function(h) {
      h.w.fadeIn(5, function(){h.o.show();});
    },
    onHide: function(h){
      h.w.fadeOut(5, function(){h.o.remove();});
    }
  });
  J('#dr_patient_popup').jqm({
    overlay: 80,
    onShow: function(h) {
      h.w.fadeIn(5, function(){h.o.show();});
    },
    onHide: function(h){
      h.w.fadeOut(5, function(){h.o.remove();});
    }
  });


  /*var labors = ['membranes', 'onset', 'cervix', 'baby', 'placenta'];
  for (var i=0; i<labors.length; i++) {
  	alert("labors[i]: "+labors[i]);
    J("select[@name='"+labors[i]+"_hour']").change(function(){recalculate(parseInt(this.value), 0)});
    J("select[@name='"+labors[i]+"_minute']").change(function(){recalculate(parseInt(this.value), 1)});
  }*/

  J("select[@name='membranes_hour']").change(function(){recalculate(parseInt(this.value), 0, 'membranes')});
  J("select[@name='membranes_minute']").change(function(){recalculate(parseInt(this.value), 1, 'membranes')});
  J("select[@name='onset_hour']").change(function(){recalculate(parseInt(this.value), 0, 'onset')});
  J("select[@name='onset_minute']").change(function(){recalculate(parseInt(this.value), 1, 'onset')});
  J("select[@name='labor_duration_hour']").change(function(){recalculate(parseInt(this.value), 0, 'labor_duration')});
  J("select[@name='labor_duration_minute']").change(function(){recalculate(parseInt(this.value), 1, 'labor_duration')});
  J("select[@name='cervix_hour']").change(function(){recalculate(parseInt(this.value), 0, 'cervix')});
  J("select[@name='cervix_minute']").change(function(){recalculate(parseInt(this.value), 1, 'cervix')});
  J("select[@name='baby_hour']").change(function(){recalculate(parseInt(this.value), 0, 'baby')});
  J("select[@name='baby_minute']").change(function(){recalculate(parseInt(this.value), 1, 'baby')});
  J("select[@name='placenta_hour']").change(function(){recalculate(parseInt(this.value), 0, 'placenta')});
  J("select[@name='placenta_minute']").change(function(){recalculate(parseInt(this.value), 1, 'placenta')});

  /*var details = ['lmi', 'edc', 'aog', 'gravida', 'para', 'full_term', 'premature', 'abortion', 'no_of_living', 'antepartum', 'intrapartum', 'postpartum', 'total_est_blood', 'total_urine', 'bp', 'hr', 'pr', 'rr'];*/
  var details = ['gravida', 'para', 'abortion', 'heart', 'lungs', 'bp_1', 'pulse_1', 'cervix_cm', 'blood_given', 'episiotomy', 'post_bp', 'post_temp', 'post_pulse', 'post_resprate'];
  for (var i=0; i<details.length; i++) {
    J("input[@name='"+details[i]+"']").keydown(function(e){return key_check(e, J("input[@name='"+details[i]+"']").val());});
  }

});

function add_staff() {
  J('#ord_popup').jqmShow();
  J('#delivery_personell_table').flexigrid({
    url: '<?=$root_path?>modules/or/ajax/ajax_or_delivery_personell.php',
    dataType: 'json',
    colModel : [{display: 'Reference Number', width:90, name:'refno', sortable: true, align: 'left'},
                {display: 'Hospital Number', width:90, name:'refno', sortable: true, align: 'left'},
                {display: 'Patient Name', width:110, name:'personell_name', sortable: true, align: 'left'},
                {display: 'Date of Operation', width:110, name:'personell_name', sortable: true, align: 'left'},
                {display: 'Action', width:80, name:'action', sortable: false, align: 'left'}
                ],
    sortname: ["nr"],
    domain: ['delivery_personell2'],
    sortorder: "desc",
    useRp: true,
    rp: 5,
    qtype: 0,
    resizable: true});
}

function key_check(e, value) {
   var character = String.fromCharCode(e.keyCode);
   var number = /^\d+$/;

   //if (e.keyCode==9 || e.keyCode==116) {
   if ((e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || e.keyCode==9 || (e.keyCode==191 || e.keyCode==111) || (e.keyCode>=36 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105))) {
     return true;
   }
   if (character.match(number)==null) {
     return false;
   }
   else {
     return true;
   }
}

function recalculate(added_value, flag, field) {
  var total_hours = parseInt(J("#total_hours").html()) * 60;
  var total_minutes = parseInt(J("#total_minutes").html());
   var total_time = 0;
   //var total_time = total_hours + total_minutes + added_value;
  if (!flag) {
    added_value = added_value * 60;
  }
  //added by cha 10-09-09
	if(field=="membranes")
	{
		//alert("membranes previous min:"+membranes_prev_min_val+" previous hour:"+membranes_prev_hour_val);
		if(flag)
		{
		  if(added_value<membranes_prev_min_val)
		  {
		   added_value = membranes_prev_min_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - membranes_prev_min_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
		else
		{
			if(added_value<membranes_prev_hour_val)
		  {
		   added_value = membranes_prev_hour_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - membranes_prev_hour_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
	}
	else if(field=="onset")
	{
	  //alert("onset previous min:"+onset_prev_min_val+" previous hour:"+onset_prev_hour_val);
	  if(flag)
		{
		  if(added_value<onset_prev_min_val)
		  {
		   added_value = onset_prev_min_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - onset_prev_min_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
		else
		{
			if(added_value<onset_prev_hour_val)
		  {
		   added_value = onset_prev_hour_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - onset_prev_hour_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
	}
	else if(field=="cervix")
	{
	 	//alert("cervix previous min:"+cervix_prev_min_val+" previous hour:"+cervix_prev_hour_val);
	 	if(flag)
		{
		  if(added_value<cervix_prev_min_val)
		  {
		   added_value = cervix_prev_min_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - cervix_prev_min_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
		else
		{
			if(added_value<cervix_prev_hour_val)
		  {
		   added_value = cervix_prev_hour_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - cervix_prev_hour_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
	}
	else if(field=="baby")
	{
	  //alert("baby previous min:"+baby_prev_min_val+" previous hour:"+baby_prev_hour_val);
	  if(flag)
		{
		  if(added_value<baby_prev_min_val)
		  {
		   added_value = baby_prev_min_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - baby_prev_min_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
		else
		{
			if(added_value<baby_prev_hour_val)
		  {
		   added_value = baby_prev_hour_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - baby_prev_hour_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
	}
	else if(field=="placenta")
	{
	  //alert("placenta previous min:"+placenta_prev_min_val+" previous hour:"+placenta_prev_hour_val);
	  if(flag)
		{
		  if(added_value<placenta_prev_min_val)
		  {
		   added_value = placenta_prev_min_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - placenta_prev_min_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
		else
		{
			if(added_value<placenta_prev_hour_val)
		  {
		   added_value = placenta_prev_hour_val - added_value;
		   total_time = total_hours + total_minutes - added_value;
			}
		  else
		  {
		   added_value = added_value - placenta_prev_hour_val;
		   total_time = total_hours + total_minutes + added_value;
			}
		}
	}
	//end

  var total_hour_time = Math.floor(total_time/60);
  var total_minute_time = total_time - (total_hour_time * 60);
  J("#total_hours").html(total_hour_time.toString());
  J("#total_minutes").html(total_minute_time.toString());
  J("#total_hours").attr("previous_value", added_value/60);

  if (flag)
    J("#total_minutes").attr("previous_value", added_value);
  else
     J("#total_hours").attr("previous_value", added_value/60)

  //added by cha 10-10-09
  if(flag)
  {
  	if(field=="membranes")  membranes_prev_min_val = added_value;
		else if(field=="onset")	onset_prev_min_val = added_value;
		else if(field=="cervix")	cervix_prev_min_val = added_value;
		else if(field=="baby")	baby_prev_min_val = added_value;
		else if(field=="placenta")	placenta_prev_min_val = added_value;
	}
  else
  {
  	if(field=="membranes")  membranes_prev_hour_val = added_value;
		else if(field=="onset")	onset_prev_hour_val = added_value;
		else if(field=="cervix")	cervix_prev_hour_val = added_value;
		else if(field=="baby")	baby_prev_hour_val = added_value;
		else if(field=="placenta")	placenta_prev_hour_val = added_value;
	}
	/*if(field=="membranes")   alert("membranes current min:"+membranes_prev_min_val+" current hour:"+membranes_prev_hour_val);
	else if(field=="onset")  alert("onset current min:"+onset_prev_min_val+" current hour:"+onset_prev_hour_val);
	else if(field=="cervix") alert("cervix current min:"+cervix_prev_min_val+" current hour:"+cervix_prev_hour_val);
	else if(field=="baby")   alert("baby current min:"+baby_prev_min_val+" current hour:"+baby_prev_hour_val);
	else if(field=="placenta")  alert("placenta current min:"+placenta_prev_min_val+" current hour:"+placenta_prev_hour_val);  */
  //end
}

function toggle_slide(input_field, e) {
  if (e.checked)
    J("input[@name='"+input_field+"']").fadeOut('fast');
  else
    J("input[@name='"+input_field+"']").fadeIn('fast');
}

//added by CHA 10-23-09
function openReport(rept_id)
{
	var refno = document.getElementById('ref_no').value;
	if(refno)
	{
	if(rept_id=='or_delivery_record_report') window.open('or_delivery_room_record.php?refno='+refno,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	}
	else
		alert("Please select a patient first");

}
//end CHA

//added by cha 11-11-09
function setBloodType(id,blood_value)
{
	//alert("id="+id+" val="+blood_value);
	for(i=0;i<document.or_dr_record_form.blood_type.length;i++)
      {
          if(document.or_dr_record_form.blood_type[i].value==blood_value)
          {
             //alert('sex='+document.or_dr_record_form.blood_type[i].value);
             document.or_dr_record_form.blood_type[i].checked = true;
          }
          else
          {
          	document.or_dr_record_form.blood_type[i].checked = false;
					}
      }
}

function setPrenatal(prenatal_care)
{
	//prenatal care
	for(i=0;i<document.or_dr_record_form.prenatal_care.length;i++)
	{
		if(document.or_dr_record_form.prenatal_care[i].value==prenatal_care)
		{
			document.or_dr_record_form.prenatal_care[i].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.prenatal_care[i].checked = false;
		}
	}
}

function setGenCondition(general_condition,others)
{
	//general condition
	for(b=0;b<document.or_dr_record_form.general_condition.length;b++)
	{
		if(document.or_dr_record_form.general_condition[b].value==general_condition)
		{
			document.or_dr_record_form.general_condition[b].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.general_condition[b].checked = false;
		}
	}
	if(general_condition=="others")
	{
		document.getElementById('general_condition_others').style.display='';
		document.getElementById('general_condition_others').value=others;
	}
	else
	{
		document.getElementById('general_condition_others').style.display='none';
	}
}

function setBloodType(blood_value)
{
//blood type
	for(a=0;a<document.or_dr_record_form.blood_type.length;a++)
	{
		if(document.or_dr_record_form.blood_type[a].value==blood_value)
		{
			document.or_dr_record_form.blood_type[a].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.blood_type[a].checked = false;
		}
	}
}

function setMembrane(membrane_ruptured)
{
//membrane ruptured
	for(c=0;c<document.or_dr_record_form.membrane_ruptured.length;c++)
	{
		if(document.or_dr_record_form.membrane_ruptured[c].value==membrane_ruptured)
		{
			document.or_dr_record_form.membrane_ruptured[c].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.membrane_ruptured[c].checked = false;
		}
	}
}

function setCervix(cervix_condition)
{
//cervix condition
	for(d=0;d<document.or_dr_record_form.cervix_condition.length;d++)
	{
		if(document.or_dr_record_form.cervix_condition[d].value==cervix_condition)
		{
			document.or_dr_record_form.cervix_condition[d].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.cervix_condition[d].checked = false;
		}
	}
}

function setDelivery(delivery_spont)
{
//delivery_spont
	for(e=0;e<document.or_dr_record_form.delivery_spont.length;e++)
	{
		if(document.or_dr_record_form.delivery_spont[e].value==delivery_spont)
		{
			document.or_dr_record_form.delivery_spont[e].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.delivery_spont[e].checked = false;
		}
	}
}

function setPerineal(perineal_tear)
{
//perineal_tear
	for(f=0;f<document.or_dr_record_form.perineal_tear.length;f++)
	{
		if(document.or_dr_record_form.perineal_tear[f].value==perineal_tear)
		{
			document.or_dr_record_form.perineal_tear[f].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.perineal_tear[f].checked = false;
		}
	}
}

function setBleeding(bleeding)
{
//bleeding
	for(g=0;g<document.or_dr_record_form.bleeding.length;g++)
	{
		if(document.or_dr_record_form.bleeding[g].value==bleeding)
		{
			document.or_dr_record_form.bleeding[g].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.bleeding[g].checked = false;
		}
	}
}

function setLabor(labor_onset)
{
//labor onset
	for(h=0;h<document.or_dr_record_form.labor_onset.length;h++)
	{
		if(document.or_dr_record_form.labor_onset[h].value==labor_onset)
		{
			document.or_dr_record_form.labor_onset[h].checked = true;
		}
		else
		{
	  	document.or_dr_record_form.labor_onset[h].checked = false;
		}
	}
}

function setLaborDurationHr(hour)
{
	//alert(hour);
	for(h=0;h<document.or_dr_record_form.labor_duration_hour.length;h++)
	{
		if(document.or_dr_record_form.labor_duration_hour.options[h].value==hour)
		{
			document.or_dr_record_form.labor_duration_hour.options[h].selected = true;
		}
		else
		{
			document.or_dr_record_form.labor_duration_hour.options[0].selected = true;
		}

	}
}

function setLaborDurationMin(min)
{
	//alert(min);
	for(h=0;h<document.or_dr_record_form.labor_duration_minute.length;h++)
	{
		if(document.or_dr_record_form.labor_duration_minute.options[h].value==min)
		{
			document.or_dr_record_form.labor_duration_minute.options[h].selected = true;
		}
		else
		{
			document.or_dr_record_form.labor_duration_minute.options[0].selected = true;
		}
	}
}

function check_others(value)
{
	if(value=="others")
	{
	 document.getElementById('general_condition_others').style.display='';
	}
	else
	{
		document.getElementById('general_condition_others').style.display='none';
	}
}
//end cha
</script>