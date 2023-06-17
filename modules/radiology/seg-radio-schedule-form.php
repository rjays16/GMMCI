<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'modules/radiology/ajax/radio-undone-request.common.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='departments.php';
define('LANG_FILE','doctors.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_radio_user';   # burn added: November 22, 2007
#echo "seg-radio-schedule-form.php : 1 <br>";
require_once($root_path.'include/inc_front_chain_lang.php');
#echo "seg-radio-schedule-form.php : 2 <br>";
/*
if(!isset($dept_nr)||!$dept_nr){
	header('Location:doctors-select-dept.php'.URL_REDIRECT_APPEND.'&retpath='.$retpath);
	exit;
}
*/
//$db->debug=1;

$thisfile=basename(__FILE__);
//$breakfile="doctors-dienstplan.php".URL_APPEND."&dept_nr=$dept_nr&pmonth=$pmonth&pyear=$pyear&retpath=$retpath";

	# burn added: November 22, 2007
#$breakfile  = $root_path.'modules/laboratory/labor_test_request_pass.php'.URL_APPEND.'&target=radio_cal&user_origin=radio&dept_nr=158';

if (isset($_GET['batch_nr']) && $_GET['batch_nr']){
	$batch_nr = $_GET['batch_nr'];
}

if (isset($_GET['sub_dept_nr_name']) && $_GET['sub_dept_nr_name']){
	$sub_dept_nr_name = $_GET['sub_dept_nr_name'];
}

#echo "seg-radio-schedule-form.php : _GET : <br> \n : "; print_r($_GET); echo" <br> \n";
#echo "seg-radio-schedule-form.php : batch_nr = '".$batch_nr."' <br> \n";

require_once($root_path.'include/care_api_classes/class_radiology.php');
$obj_radio = new SegRadio;

#edited by VAN 07-08-08
#$scheduleInfo = $obj_radio->getScheduledRadioRequestInfo($batch_nr,TRUE);
$ifwalk_ = $obj_radio->selectwalkin_batch_nr($batch_nr);
$ifwalk = $ifwalk_->FetchRow();
#added by daryl
if (($ifwalk['ifwalk']) == 1){
	$scheduleInfo = $obj_radio->getScheduledRadioRequestInfo2_walk($batch_nr,TRUE);
}else{
$scheduleInfo = $obj_radio->getScheduledRadioRequestInfo2($batch_nr,TRUE);
}
// echo $obj_radio->sql;

#echo "seg-radio-schedule-form.php : scheduleInfo : <br> \n : "; print_r($scheduleInfo); echo" <br> \n";

if ((!$scheduleInfo) || empty($scheduleInfo)){
		echo "<script type='text/javascript'> \n";
		echo "alert(\"Sorry but the page cannot be displayed! Please try again!\");
				window.parent.location.href=window.parent.location.href;
				window.parent.pSearchClose();";
		echo "</script> <br> \n";
		exit();
}


extract($scheduleInfo);
	$patient_fullname=$scheduleInfo['name_last'].', '.$scheduleInfo['name_first'];
if (!empty($scheduleInfo['name_middle'])){
	$patient_fullname .= '&nbsp '.ucwords(strtolower($scheduleInfo['name_middle']));
}



	# FORMATTING of Scheduled Date/Time
$scheduled_dt = $scheduleInfo['scheduled_dt'];
if (($scheduled_dt!='0000-00-00 00:00:00')  && ($scheduled_dt!="")){
	$date_time_scheduled = explode(" ",$scheduled_dt);
	$date_scheduled = trim($date_time_scheduled[0]);
	$time_scheduled = trim($date_time_scheduled[1]);
	list($pyear, $pmonth, $pday)  = explode("-",$scheduled_dt);
}else{
	$date_scheduled='';
	$time_scheduled='';
}

	#Instructions
if (!empty($instructions)){
	$instructions_list = unserialize($instructions);
}
#echo "seg-radio-schedule-form.php : instructions_list : <br> \n : "; print_r($instructions_list); echo" <br> \n";



	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	#$phpfd=str_replace("yy","%y", strtolower($phpfd));



# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
# $smarty->assign('sToolbarTitle',$sTitle);
 $smarty->assign('sToolbarTitle',"Radiology :: ".$sub_dept_nr_name." :: <br> ".$patient_fullname);

 # href for help button
 #$smarty->assign('pbHelp',"javascript:gethelp('docs_dutyplan_edit.php','$mode','$rows')");
 $smarty->assign('pbHelp',"");

# href for return button
# $smarty->assign('pbBack','javascript:history.back();killchild();');

 # href for close button
# $smarty->assign('breakfile',$breakfile);
	#$smarty->assign('breakfile','javascript:window.parent.pSearchClose();ReloadWindow();');
	$smarty->assign('breakfile','javascript:ReloadWindow();');
	$smarty->assign('pbBack','');

 # Body onLoad javascript
 $smarty->assign('sOnLoadJs','');

 # Window bar title
# $smarty->assign('sWindowTitle',$sTitle);
// Prints something like: Wednesday 15th of January 2003 05:51:38 AM

 $smarty->assign('sWindowTitle',"Radiology :: ".$sub_dept_nr_name." :: <br> ".$patient_fullname);
# $smarty->assign('sWindowTitle',"Radiology :: ".$sub_dept_nr_name." Daily Scheduling (".date("l dS of F Y h:i:s A",mktime(0, 0, 0, $pmonth, $pday, $pyear)).")");

 # Collect extra javascript

 ob_start();
?>

<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa;
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc;
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px;
	font-weight:bold;
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>

			<!-- START for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->
<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
			<!-- END for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->

<script type="text/javascript" src="js/radio-schedule-daily.js?t=<?=time()?>"></script>

<?php
 $xajax->printJavascript($root_path.'classes/xajax-0.2.5');

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->append('JavaScript',$sTemp);

		# FORMATTING of Date Scheduled
	if (($date_scheduled!='0000-00-00')  && ($date_scheduled!=""))
		$date_scheduled = @formatDate2Local($date_scheduled,$date_format);
	else
		$date_scheduled='';

	$sDateScheduled= '<input name="date_scheduled" type="text" size="15" maxlength=10 value="'.$date_scheduled.'"'.
									'onFocus="this.select();"
									id = "date_scheduled"
									onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_scheduled_trigger" style="cursor:pointer" >
									<font size=2>[';
	ob_start();
?>
	<script type="text/javascript">
			Calendar.setup ({
					inputField : "date_scheduled", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_scheduled_trigger", singleClick : true, step : 1
			});
	</script>
<?php
	$calendarSetup = ob_get_contents();
	ob_end_clean();

	$sDateScheduled .= $calendarSetup;

	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$sDateScheduled = $sDateScheduled.$$dfbuffer.']';

	if (($scheduled_time)||(scheduled_time!="00:00:00")){
		$time_scheduled = $scheduled_time;
		$time_scheduled = date('H:i',strtotime($time_scheduled));
	}


		# FORMATTING of Time Scheduled
	$sTimeScheduled = "\n";
	$sTimeScheduled .= '<input type="text" id="time_scheduled" name="time_scheduled" value="'.$time_scheduled.'" size="4" maxlength="5" onChange="setFormatTime(this,\'selAMPM_scheduled\')">&nbsp;';
	$sTimeScheduled .= "\n".
							'<select id="selAMPM_scheduled" name="selAMPM_scheduled">'."\n".
							'	<option value="A.M.">A.M.</option>'."\n".
							'	<option value="P.M.">P.M.</option>'."\n";
	$sTimeScheduled .= "</select> \n";
	if ($time_scheduled){
		$sTimeScheduled .= '<script language="javascript">'."\n".
								'	setFormatTime($(\'time_scheduled\'),\'selAMPM_scheduled\')'.
								'</script>';
	}

//	$service_date = $radioRequestInfo['service_date'];

#echo "seg-radio-schedule-form.php : service_date = '".$service_date."' <br> \n";

	if (($service_date!='0000-00-00')  && ($service_date!=""))
		$service_date = @formatDate2Local($service_date,$date_format);
	else
		$service_date='';

#echo "seg-radio-schedule-form.php : 2 service_date = '".$service_date."' <br> \n";

	$sServiceDate= '<input name="service_date" type="text" size="15" maxlength=10 value="'.$service_date.'"'.
				'onFocus="this.select();"
				id = "service_date"
				onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
				onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="service_date_trigger" style="cursor:pointer" >
				<font size=2>[';
		ob_start();
?>
	<script type="text/javascript">
		Calendar.setup ({
			inputField : "service_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "service_date_trigger", singleClick : true, step : 1
		});
	</script>
<?php
		$calendarSetup = ob_get_contents();
		ob_end_clean();

		$sServiceDate .= $calendarSetup;

		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sServiceDate = $sServiceDate.$$dfbuffer.']';

	$radio_ins = $obj_radio->getRadioInstructionsInfo($sub_dept_nr);
#	echo "seg-radio-schedule-daily.php : radio_ins = '".$radio_ins."' <br> \n";
#	echo "seg-radio-schedule-daily.php : radio_ins : <bn>\n"; print_r($radio_ins); echo"' <br> \n";
	if ($radio_ins){
		$sInstructions='';
		while($ins_info=$radio_ins->FetchRow()){
			$checked="";
			foreach($instructions_list as $ins_value){
				if ($ins_info['nr']==trim($ins_value)){
					$checked=" checked";
				}
			}
			reset($instructions_list);
			$sInstructions .= "<input type='checkbox' name='instruction[]' id='instruction".$ins_info['nr']."' value='".$ins_info['nr']."' $checked>".$ins_info['instruction']."&nbsp;&nbsp; <br> \n";
		}
			# OTHERS option
		$checked="";
		$disabled = " disabled";
		foreach($instructions_list as $ins_value){
			$index = strpos($ins_value, ' ');
			$ins_nr = trim(substr($ins_value,0,$index));
			if ($ins_nr == '0'){
				$checked=" checked";
				$disabled="";
				#$instruction_other = trim(substr($ins_value,$index));;
				$instruction_other = trim(substr($ins_value,$index));
			}
		}
		$sInstructions .= "<input type='checkbox' name='instruction[]' id='instruction0' value='0' onClick='click_others(this)' $checked>Others, please specify : &nbsp; \n";
		$sInstructions .= "<input type='text' name='instruction_other' id='instruction_other' value='".$instruction_other."' onBlur='trimString(this,true);' $disabled><br> \n";
	}

 $smarty->assign('sInstructions',$sInstructions.'&nbsp;');

 $smarty->assign('sPanelHeaderSchedule',$sub_dept_nr_name.' Schedule Form');
 $smarty->assign('sBatchNr','<span id="batchDisplay">'.$batch_nr.'</span>&nbsp;'."\n".
									'<input type="hidden" name="batchNo" id="batchNo" value="'.($batch_nr? $batch_nr:"0").'">'."\n");
 $smarty->assign('sServiceCode','<span id="service_code">'.$service_code.'</span>&nbsp;'."\n");

  #edited by Atchup 10-01-15
 $smarty->assign('sPatientName','<input class="segInput" id="p_name" name="p_name" type="text" size="40" value= "'.ucwords(strtolower($patient_fullname)).' "font:bold 12px Arial" readonly>');

 $smarty->assign('sSelectBatchNr','<img class="segInput" name="select-batchNr" id="select-batchNr" src="'.$root_path.'images/btn_encounter_small.gif" border="0" style="cursor:pointer;"
			 onclick="overlib(
				OLiframeContent(\'seg-radio-schedule-select-batchNr.php?sub_dept_nr='.$sub_dept_nr.'\', 850, 400, \'fSelBatchNr\', 1, \'auto\'),
				WIDTH,850, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,
				CAPTION,\'Select unscheduled request\',
				MIDX,0, MIDY,0,
				STATUS,\'Select unscheduled request\'); return false;"
			 onmouseout="nd();">');

 $smarty->assign('sClearBatchNr','<input class="segInput" name="clear-batchNr" id="clear-batchNr" type="button" style="cursor:pointer;font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled>');


# $sDateScheduledValue = date("m/d/Y",mktime(0, 0, 0, $pmonth, $pday, $pyear));
# $sDateScheduled  = date("F d, Y (l)",mktime(0, 0, 0, $pmonth, $pday, $pyear));
# $sDateScheduled .= "\n".'<input type="hidden" name="date_scheduled" id="date_scheduled" value="'.$pmonth.'/'.$pday.'/'.$pyear.'">'."\n";
# $sDateScheduled .= "\n".'<input type="hidden" name="date_scheduled" id="date_scheduled" value="'.$sDateScheduledValue.'">'."\n";
 $smarty->assign('sDateScheduled',$sDateScheduled);
 $smarty->assign('sTimeScheduled',$sTimeScheduled);
 $smarty->assign('sRemarks','<textarea class="segInput" name="remarks" id="remarks" cols="50" rows="3" onChange="trimString(this,true);" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$remarks.'</textarea>');

 $smarty->assign('sServiceDate',$sServiceDate);

#edited by VAN 07-08-08
if (empty($scheduleInfo['schedule_no']))
	$smarty->assign('sScheduleButton','<img '.createLDImgSrc($root_path,'savedisc.gif','0').'" alt="Saves this schedule" onClick="saveSchedule(\'save\')" style="cursor:pointer"></a>');
else
	$smarty->assign('sScheduleButton','<img '.createLDImgSrc($root_path,'update.gif','0').'" alt="Updates this schedule" onClick="saveSchedule(\'update\')" style="cursor:pointer"></a>');

# $smarty->assign('sResetSchedule','<img src="'.$root_path.'/images/reset.gif" alt="Resets this schedule" onClick="resetForm()"></a>');

 #added by VAN 06-17-08
 $smarty->assign('sPrintButton','<img '.createLDImgSrc($root_path,'viewpdf.gif','0').'" alt="Print Copy" onClick="viewSchedule('.$batch_nr.')" style="cursor:pointer"></a>');
 #---------------------

 # collect hidden inputs

 ob_start();
?>

<input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
<input type="hidden" name="sid" id="sid" value="<?php echo $sid; ?>">
<input type="hidden" name="pday" id="pday" value="<?php echo $pday; ?>">
<input type="hidden" name="pmonth" id="pmonth" value="<?php echo $pmonth; ?>">
<input type="hidden" name="pyear" id="pyear" value="<?php echo $pyear; ?>">
<input type="hidden" name="dept_nr" id="dept_nr" value="<?php echo $dept_nr; ?>">
<input type="hidden" name="sub_dept_nr" id="sub_dept_nr" value="<?php echo $sub_dept_nr; ?>">
<input type="hidden" name="thisfile" id="thisfile" value="<?= $thisfile ?>">
<input type="hidden" name="root_path" id="root_path" value="<?= $root_path ?>">

<?php

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);

 $smarty->assign('sFormAction','action="'.$thisfile.'"');

$smarty->assign('sMainBlockIncludeFile','radiology/schedule_form_frame.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
