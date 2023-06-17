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
require_once($root_path.'include/inc_front_chain_lang.php');

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
$breakfile  = $root_path.'modules/laboratory/labor_test_request_pass.php'.URL_APPEND.'&target=radio_cal&user_origin=radio&dept_nr=158';

require_once($root_path.'include/care_api_classes/class_radiology.php');
$obj_radio = new SegRadio;

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
$dept_obj->preloadDept($dept_nr);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;
$pers_obj->useDutyplanTable();

//day="+d+"&month="+m+"&year="+y;
if (isset($_GET['day']) && $_GET['day']){
	$pday = $_GET['day'];
}

if (isset($_GET['month']) && $_GET['month']){
	$pmonth = $_GET['month'];
}

if (isset($_GET['year']) && $_GET['year']){
	$pyear = $_GET['year'];
}

#echo "seg-radio-schedule-daily.php : _GET : <br> \n : "; print_r($_GET); echo" <br> \n";
#echo "seg-radio-schedule-daily.php : date('n') = '".date('n')."' <br> \n";

if ($pmonth=='') $pmonth=date('n');
if ($pyear=='') $pyear=date('Y');

/* Establish db connection */
if(!isset($db)||!$db) include($root_path.'include/inc_db_makelink.php');
if($dblink_ok)
	{	
		if($mode=='save')
		{
					
					$arr_1_txt=array();
					$arr_2_txt=array();
					$arr_1_pnr=array();
					$arr_2_pnr=array();

					for($i=0;$i<$maxelement;$i++)
					{
						$tdx="ha".$i;
						$ddx="hr".$i;
						$ax="a".$i;
						$rx="r".$i;
						
						if(!empty($$ax)) $arr_1_txt[$ax]=$$ax;
						if(!empty($$rx)) $arr_2_txt[$rx]=$$rx;
						if(!empty($$tdx)) $arr_1_pnr[$tdx]=$$tdx;
						if(!empty($$ddx)) $arr_2_pnr[$ddx]=$$ddx;
						
					}
					
					$ref_buffer=array();
					// Serialize the data
					$ref_buffer['duty_1_txt']=serialize($arr_1_txt);
					$ref_buffer['duty_2_txt']=serialize($arr_2_txt);
					$ref_buffer['duty_1_pnr']=serialize($arr_1_pnr);
					$ref_buffer['duty_2_pnr']=serialize($arr_2_pnr);
					
					$ref_buffer['dept_nr']=$dept_nr;
					$ref_buffer['role_nr']=15;
					$ref_buffer['year']=$pyear;
					$ref_buffer['month']=$pmonth;
					$ref_buffer['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];

					if($dpoc_nr=$pers_obj->DOCDutyplanExists($dept_nr,$pyear,$pmonth)){
						$ref_buffer['history']=$pers_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
						$ref_buffer['modify_time']=date('YmdHis');
						// Point to the internal data array
						$pers_obj->setDataArray($ref_buffer);
															
						if($pers_obj->updateDataFromInternalArray($dpoc_nr)){

							# Remove the cache plan
							if(date('Yn')=="$pyear$pmonth"){
								$pers_obj->deleteDBCache('DOCS_'.date('Y-m-d'));
							}
							header("location:$thisfile?sid=$sid&lang=$lang&saved=1&dept_nr=$dept_nr&pyear=$pyear&pmonth=$pmonth&retpath=$retpath");
							exit;
						}else echo "<p>".$pers_obj->getLastQuery."<p>$LDDbNoSave"; 
					} // else create new entry
					else
					{
						$ref_buffer['history']="Create: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n";
						$ref_buffer['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
						$ref_buffer['create_time']=date('YmdHis');
						// Point to the internal data array
						$pers_obj->setDataArray($ref_buffer);

						//echo "create";

							if($pers_obj->insertDataFromInternalArray()){
								# Remove the cache plan
								if(date('Yn')=="$pyear$pmonth"){
									$pers_obj->deleteDBCache('DOCS_'.date('Y-m-d'));
								}
								header("location:$thisfile?sid=$sid&lang=$lang&saved=1&dept_nr=$dept_nr&pyear=$pyear&pmonth=$pmonth&retpath=$retpath");
								exit;
							}else{
								echo "<p>".$pers_obj->getLastQuery."<p>$LDDbNoSave";
							} 
					}//end of else
						
		 }// end of if(mode==save)
		 else
		 {
			if($dutyplan=&$pers_obj->getDOCDutyplan($dept_nr,$pyear,$pmonth)){
			
				$aelems=unserialize($dutyplan['duty_1_txt']);
				$relems=unserialize($dutyplan['duty_2_txt']);
				$a_pnr=unserialize($dutyplan['duty_1_pnr']);
				$r_pnr=unserialize($dutyplan['duty_2_pnr']);
			}
		}
}
	else { echo "$LDDbNoLink<br>"; } 


$maxdays=date("t",mktime(0,0,0,$pmonth,1,$pyear));

$firstday=date("w",mktime(0,0,0,$pmonth,1,$pyear));

function makefwdpath($path,$dpt,$mo,$yr,$saved)
{
	if ($path==1)
	{	
		$fwdpath='doctors-dienstplan.php?';
		if($saved!="1") 
		{  
			if ($mo==1) {$mo=12; $yr--;}
				else $mo--;
		}
		return $fwdpath.'dept='.$dpt.'&pmonth='.$mo.'&pyear='.$yr;
	}
	else return "doctors-dienstplan-checkpoint.php";
}

# Prepare page title
 $sTitle = "$LDMakeDutyPlan :: ";
 $LDvar=$dept_obj->LDvar();
 if(isset($$LDvar)&&$$LDvar) $sTitle = $sTitle.$$LDvar;
	 else $sTitle = $sTitle.$dept_obj->FormalName();

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
 
 $dept_info = $dept_obj->getDepartmentInfo("nr='".$sub_dept_nr."'","nr");
 $sub_dept_nr_name = $dept_info['name_short'];

# Title in toolbar
# $smarty->assign('sToolbarTitle',$sTitle);
 $smarty->assign('sToolbarTitle',"Radiology :: ".$sub_dept_nr_name." :: Daily Scheduling (".$monat[$pmonth]." ".$pday.", ".$pyear.")");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('docs_dutyplan_edit.php','$mode','$rows')");

# href for return button
 $smarty->assign('pbBack','javascript:history.back();killchild();');

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Body onLoad javascript
 $smarty->assign('sOnLoadJs','onUnload="killchild()"');

 # Window bar title
# $smarty->assign('sWindowTitle',$sTitle);
// Prints something like: Wednesday 15th of January 2003 05:51:38 AM

 $smarty->assign('sWindowTitle',"Radiology :: ".$sub_dept_nr_name." :: Daily Scheduling (".$monat[$pmonth]." ".$pday.", ".$pyear.")");
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

<script language="javascript">
	var urlholder;
	var infowinflag=0;

function popselect(elem,mode)
{
	w=window.screen.width;
	h=window.screen.height;
	ww=300;
	wh=500;
	var tmonth=document.dienstplan.month.value;
	var tyear=document.dienstplan.jahr.value;
	urlholder="doctors-dienstplan-poppersonselect.php?elemid="+elem + "&dept_nr=<?php echo $dept_nr ?>&month="+tmonth+"&year="+tyear+ "&mode=" + mode + "&retpath=<?php echo $retpath ?>&user=<?php echo $ck_doctors_dienstplan_user."&lang=$lang&sid=$sid"; ?>";
	
	popselectwin=window.open(urlholder,"pop","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
	window.popselectwin.moveTo((w/2)+80,(h/2)-(wh/2));
}

function killchild()
{
 if (window.popselectwin) if(!window.popselectwin.closed) window.popselectwin.close();
}

function cal_update()
{
	var filename="doctors-dienstplan-planen.php?<?php echo "sid=$sid&lang=$lang" ?>&retpath=<?php echo $retpath ?>&dept_nr=<?php echo $dept_nr; ?>&pmonth="+document.dienstplan.month.value+"&pyear="+document.dienstplan.jahr.value;
	window.location.replace(filename);
}
</script>

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

	$radio_ins = $obj_radio->getRadioInstructionsInfo($sub_dept_nr);
	#echo "sql = ".$obj_radio->sql;
#	echo "seg-radio-schedule-daily.php : radio_ins = '".$radio_ins."' <br> \n";
#	echo "seg-radio-schedule-daily.php : radio_ins : <bn>\n"; print_r($radio_ins); echo"' <br> \n";

	if ($radio_ins){
		$sInstructions='';
		while($ins_info=$radio_ins->FetchRow()){
			$sInstructions .= "<input type='checkbox' name='instruction[]' id='instruction".$ins_info['nr']."' value='".$ins_info['nr']."'>".$ins_info['instruction']."&nbsp;&nbsp; <br> \n";
		}
			# OTHERS option
		$sInstructions .= "<input type='checkbox' name='instruction[]' id='instruction0' value='0' onClick='click_others(this)'>Others, please specify : &nbsp; \n";
		$sInstructions .= "<input type='text' name='instruction_other' id='instruction_other' value='' onBlur='trimString(this,true);' disabled><br> \n";
	}
	#added by VAN 06-17-08
	else{
		$sInstructions .= "<input type='checkbox' name='instruction[]' id='instruction0' value='0' onClick='click_others(this)'>Others, please specify : &nbsp; \n";
		$sInstructions .= "<input type='text' name='instruction_other' id='instruction_other' value='' onBlur='trimString(this,true);' disabled><br> \n";
	}

 $smarty->assign('sInstructions',$sInstructions.'&nbsp;');
 
 $smarty->assign('sPanelHeaderSchedule',$sub_dept_nr_name.' Schedule Form');
 $smarty->assign('sBatchNr','<span id="batchDisplay">'.$batchNo.'</span>&nbsp;'."\n".
									'<input type="hidden" name="batchNo" id="batchNo" value="'.($batchNo? $batchNo:"0").'">'."\n");
 $smarty->assign('sServiceCode','<span id="service_code">'.$service_code.'</span>&nbsp;'."\n");
 $smarty->assign('sPatientName','<input class="segInput" id="p_name" name="p_name" type="text" size="40" value="'.$patient_name.'" style="font:bold 12px Arial;" readonly>');
 $smarty->assign('sSelectBatchNr','<img class="segInput" name="select-batchNr" id="select-batchNr" src="'.$root_path.'images/btn_encounter_small.gif" border="0" style="cursor:pointer;"
			 onclick="overlib(
				OLiframeContent(\'seg-radio-schedule-select-batchNr.php?sub_dept_nr='.$sub_dept_nr.'\', 850, 400, \'fSelBatchNr\', 1, \'auto\'),
				WIDTH,850, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4, 
				CAPTION,\'Select unscheduled request\',
				MIDX,0, MIDY,0, 
				STATUS,\'Select unscheduled request\'); return false;"
			 onmouseout="nd();">');
 $smarty->assign('sClearBatchNr','<input class="segInput" name="clear-batchNr" id="clear-batchNr" type="button" style="cursor:pointer;font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled>');

#added by VAN 04-29-08
#echo "patient = ".$patientType;
$smarty->assign('sPatientType','<span id="ptypeDisplay">'.$patientType.'</span>&nbsp;'."\n".
									'<input type="hidden" name="patientType" id="patientType" value="'.$patientType.'">'."\n");

#-------------------------										


 $sDateScheduledValue = date("m/d/Y",mktime(0, 0, 0, $pmonth, $pday, $pyear));
 $sDateScheduled  = date("F d, Y (l)",mktime(0, 0, 0, $pmonth, $pday, $pyear));
# $sDateScheduled .= "\n".'<input type="hidden" name="date_scheduled" id="date_scheduled" value="'.$pmonth.'/'.$pday.'/'.$pyear.'">'."\n";
 $sDateScheduled .= "\n".'<input type="hidden" name="date_scheduled" id="date_scheduled" value="'.$sDateScheduledValue.'">'."\n";
 $smarty->assign('sDateScheduled',$sDateScheduled);
 $smarty->assign('sTimeScheduled',$sTimeScheduled);
 $smarty->assign('sRemarks','<textarea class="segInput" name="remarks" id="remarks" cols="50" rows="3" onChange="trimString(this,true);" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$remarks.'</textarea>');

 $smarty->assign('sScheduleButton','<img '.createLDImgSrc($root_path,'savedisc.gif','0').'" alt="Saves this schedule" onClick="saveSchedule(\'save\')" style="cursor:pointer"></a>');
 $smarty->assign('sResetSchedule','<img src="'.$root_path.'/images/reset.gif" alt="Resets this schedule" onClick="resetForm()" style="cursor:pointer"></a>');

$smarty->assign('sPanelHeaderScheduledForTheDay','List of Scheduled Requests for '.date("F d, Y (l)",mktime(0, 0, 0, $pmonth, $pday, $pyear)));

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
<input type="hidden" name="sub_dept_nr_name" id="sub_dept_nr_name" value="<?= $sub_dept_nr_name ?>">
<input type="hidden" name="thisfile" id="thisfile" value="<?= $thisfile ?>">
<input type="hidden" name="root_path" id="root_path" value="<?= $root_path ?>">


<?php

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);

 ob_start();
?>
<script language="javascript">
	xajax_PopulateRadioScheduledRequests('reload','<?=$sub_dept_nr?>', '<?=$pmonth?>', '<?=$pday?>', '<?=$pyear?>');
</script>
<?php
 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->assign('sPresets',$sTemp);

 $smarty->assign('sFormAction','action="'.$thisfile.'"');

$smarty->assign('sMainBlockIncludeFile','radiology/schedule_plan_entry_frame.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
