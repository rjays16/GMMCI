<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

#added by VAN 10-30-09
ob_start();

require($root_path.'modules/nursing/ajax/nursing-station-new-server.php');
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

#-----------------------
/**
* CARE2X Integrated Hospital Information System version deployment 1.1 (mysql) 2004-01-11
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* , elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='prompt.php';
define('LANG_FILE','nursing.php');
$local_user='ck_pflege_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'global_conf/inc_remoteservers_conf.php');
//$db->debug=true;
if(!$encoder) $encoder=$HTTP_COOKIE_VARS[$local_user.$sid];

$breakfile="nursing-station.php".URL_APPEND."&edit=1&station=$station&ward_nr=$ward_nr";
$thisfile=basename(__FILE__);

require($root_path.'include/care_api_classes/class_medocs.php');		
$objResDisp = new Medocs;		
						
require_once($root_path.'include/care_api_classes/class_person.php');		
$person_obj = new Person;

# Load date formatter
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

if( $enc_obj->loadEncounterData($pn)) {

	if(($mode=='release')&&!(isset($lock)||$lock)){
		#edited by VAN 06-27-2010
		#$date=(empty($x_date))?date('Y-m-d'):formatDate2STD($x_date,$date_format);
		#$time=(empty($x_time))?date('H:i:s'):convertTimeToStandard($x_time);

		if (empty($x_date))
			$date = date('Y-m-d');
		else{
			$date = date('Y-m-d',strtotime($x_date));
		}

		if (empty($x_time))
			$time = date('H:i:s');
		else{
			$time = $x_time.":00 ".$selAMPM;
			$time = date('H:i:s',strtotime($time));
		}
		#echo "maygohome = ".$HTTP_POST_VARS['is_maygohome'];
		#die();


		if($HTTP_POST_VARS['is_maygohome']){
			#if patient is already advised to go home and already paid the hospital bill
				#echo "r = ".$relart;
		switch($relart)
		{
			case 1: {}
			case 2: {}
			case 7: {}
			case 3: $released=$enc_obj->Discharge($pn,$relart,$date,$time);
						break;
			case 4: $released=$enc_obj->DischargeFromWard($pn,$relart,$date,$time);
						break;
			case 5: $released=$enc_obj->DischargeFromRoom($pn,$relart,$date,$time);
						break;
			case 6: $released=$enc_obj->DischargeFromBed($pn,$relart,$date,$time);
												$enc_obj->deleteDeathsInfo($pn);
												#echo "del = ".$enc_obj->sql;
												$enc_obj->addDeathsInfo($pn,$ward_nr,$HTTP_POST_VARS['option_death']);
												#echo "save = ".$enc_obj->sql;
												#die();
						break;
			default: $released=false;
		}
	}else{

						if  ($relart==7) {
								$released=$enc_obj->DischargeFromBed($pn,$relart,$date,$time);
												$enc_obj->deleteDeathsInfo($pn);
												#echo "del = ".$enc_obj->sql;
												$enc_obj->addDeathsInfo($pn,$ward_nr,$HTTP_POST_VARS['option_death']);
												#echo "save = ".$enc_obj->sql;

						}
		#if patient is advised to go home

				#$released=$enc_obj->MayGoHome($pn);
				#commented by VAN 06-27-2010
				/*
				$mgh_date = "0000-00-00 00:00:00";
				$date = $date." ".$time;

				if (($date)&&($date!=''))
						$mgh_date = date("Y-m-d H:i:s",strtotime($date));
				$released=$enc_obj->MayGoHome($pn, $mgh_date);
			*/
	 # print_r($enc_obj->sql);
	}

	$patient_enc_res = $enc_obj->getPatientEncounterRes($pn);
	$patient_enc_disp = $enc_obj->getPatientEncounterDispRefer($pn);
			
	if(isset($_POST['result_code'])) {
		if ($patient_enc_res['encounter_nr']==$pn) {
			$result['result_code']=$_POST['result_code'];
			$result['modify_id']=$_SESSION['sess_user_name'];
			$result['modify_time']=date('Y-m-d H:i:s');
			$enc_obj->setDataArray($result);
			
			if(!@$enc_obj->updateEncounterResults($pn, $patient_enc_res['result_code'])) echo "<br>$LDDbNoSave";
			} else {
				$result['encounter_nr'] = $pn;
				$result['result_code']=$_POST['result_code'];
				$result['modify_id']=$_SESSION['sess_user_name'];
				$result['modify_time']=date('Y-m-d H:i:s');
				$result['create_id']=$_SESSION['sess_user_name'];
				$result['create_time']=date('Y-m-d H:i:s');
				$enc_obj->setDataArray($result);
			
				if(!@$enc_obj->saveEncounterResults($result)) echo "<br>$LDDbNoSave"; }

				if (empty($_POST['date_death'])){
					$death_date = "0000-00-00";
					$death_time = "00:00";
					$DOA = 0;
					$DOA_reason = "";
				} else {
					$death_date = date("Y-m-d",strtotime($_POST['date_death']));
					$death_hr = trim($_POST['death_time']);
					$death_mer = trim($_POST['selAMPM_dt']);
					$death_mer = str_replace('.','',$death_mer);
					$death_time =  $death_hr.":00 ".$death_mer;
					$death_time = date("H:i:s",strtotime($death_time));
				}
			
				$person_obj->updateDeathDate($enc_obj->PID(), $death_date, $pn, $death_time);
				$enc_obj->updateDOA($pn, $DOA, $DOA_reason);
	}
			
	if(isset($_POST['disp_code'])) {
		if($patient_enc_disp['encounter_nr']==$pn) {
			$disposition['disp_code']=$_POST['disp_code'];
			$disposition['modify_id'] = $_SESSION['sess_user_name'];
			$disposition['modify_time']=date('Y-m-d H:i:s');
			$disposition['create_id']= $_SESSION['sess_user_name'];
			$disposition['create_time']=date('Y-m-d H:i:s');
			$disposition['hosp_name']= $HTTP_POST_VARS['hosp_name']; 
			$disposition['hosp_add']= $HTTP_POST_VARS['hosp_add'];
			$disposition['referral_reason_id']= $HTTP_POST_VARS['referral_reason_id']; 
			$enc_obj->setDataArray($disposition);

			if(!@$enc_obj->updateEncounterDispositionRefer($pn,$patient_enc_disp['disp_code']))
								 echo "<br>$LDDbNoSave";
			} else {
				$disposition['encounter_nr'] = $pn;
				$disposition['disp_code']=$_POST['disp_code'];
				$disposition['modify_id'] = $_SESSION['sess_user_name'];
				$disposition['modify_time']=date('Y-m-d H:i:s');
				$disposition['create_id']= $_SESSION['sess_user_name'];
				$disposition['create_time']=date('Y-m-d H:i:s');
				$disposition['hosp_name']= $HTTP_POST_VARS['hosp_name']; 
				$disposition['hosp_add']= $HTTP_POST_VARS['hosp_add'];
				$disposition['referral_reason_id']= $HTTP_POST_VARS['referral_reason_id']; 
				$enc_obj->setDataArray($disposition);
			
				if(!@$enc_obj->saveEncounterDispositionRefer($disposition))
								 echo "<br>$LDDbNoSave";
				}
	}

	if($released) {
		if(!empty($info)) {
			$data_array['notes']=$info;
			$data_array['encounter_nr']=$pn;
			$data_array['date']=$date;
			$data_array['time']=$time;
			$data_array['personell_name']=$encoder;
			$enc_obj->saveDischargeNotesFromArray($data_array);
		}
		# If patient died
		if($relart==7) {
			include_once($root_path.'include/care_api_classes/class_person.php');
			$person=new Person;
			$death['death_date']=$date;
			$death['death_encounter_nr']=$pn;
			$death['history']=$enc_obj->ConcatHistory("Discharged (cause: death) ".date('Y-m-d H:i:s')." $encoder\n");
			$death['modify_id']=$encoder;
			$death['modify_time']=date('YmdHis');
			@$person->setDeathInfo($enc_obj->PID(),$death);
			//echo $person->getLastQuery();
		}

		#echo ("location:$thisfile?sid=$sid&lang=$lang&pn=$pn&bd=$bd&rm=$rm&pyear=$pyear&pmonth=$pmonth&pday=$pday&mode=$mode&released=1&lock=1&x_date=$x_date&x_time=$x_time&relart=$relart&encoder=".strtr($encoder," ","+")."&info=".strtr($info," ","+")."&station=$station&ward_nr=$ward_nr");
		#header("location:$thisfile?sid=$sid&lang=$lang&pn=$pn&bd=$bd&rm=$rm&pyear=$pyear&pmonth=$pmonth&pday=$pday&mode=$mode&released=1&lock=1&x_date=$x_date&x_time=$x_time&relart=$relart&encoder=".strtr($encoder," ","+")."&info=".strtr($info," ","+")."&station=$station&ward_nr=$ward_nr");
		header("Location:".$thisfile."?sid=".$sid."&lang=".$lang."&pn=".$pn."&bd=".$bd."&rm=".$rm."&pyear=".$pyear."&pmonth=".$pmonth."&pday=".$pday."&mode=".$mode."&released=1&lock=1&x_date=".$x_date."&x_time=".$x_time."&relart=".$relart."&encoder=".strtr($encoder," ","+")."&info=".strtr($info," ","+")."&station=".$station."&ward_nr=".$ward_nr);
		exit;
		}
	}	// end of if (mode=release)

	#echo "sql = ".$enc_obj->sql;
	include_once($root_path.'include/care_api_classes/class_globalconfig.php');
	$GLOBAL_CONFIG=array();
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('patient_%');
	$glob_obj->getConfig('person_%');

	$result=&$enc_obj->encounter;
	/* Check whether config foto path exists, else use default path */
	$default_photo_path='fotos/registration';
	$photo_filename=$result['photo_filename'];
	$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;
	require_once($root_path.'include/inc_photo_filename_resolve.php');
	/* Load the discharge types */
	$discharge_types=&$enc_obj->getDischargeTypesData();

	$patient_ok=TRUE;
} else {
	$patient_ok=FALSE;
}

$encinfo = $enc_obj->getPatientEncounter($pn);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Toolbar title

 $smarty->assign('sToolbarTitle',$LDReleasePatient);

 # href for the return button
 $smarty->assign('pbBack',FALSE);

# href for the  button
 $smarty->assign('pbHelp',"javascript:gethelp('inpatient_discharge.php','','','$station','$LDReleasePatient')");

 $popUp = $_GET['popUp'];
 if ($popUp==1)
	$breakfile = '';

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDReleasePatient);

 #added by VAN 10-30-09
 $onLoadJS='onLoad="preSet();"';
 $smarty->assign('sOnLoadJs',$onLoadJS);
 #----------------

 # Collect extra javascrit code if patient is not released yet
 #commented by VAN 06-27-2010
if(!$released){

	#ob_start();
?>

<script language="javascript">
<!--

//added by VAN 06-27-2010
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
}/* end of function trimString */


var js_time = "";
function js_setTime(jstime){
	js_time = jstime;
}

function js_getTime(){
	return js_time;
}

function validateTime(S) {
		return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
}

var seg_validDate=true;
//var seg_validTime=false;

function seg_setValidDate(bol){
	seg_validDate=bol;
//	alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//	var time = $('time_text_d');
//alert(AMPM);
	var stime = thisTime.value;
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	var jtime = "";

	trimString(thisTime);

	if (thisTime.value==''){
		seg_validTime=false;
		return;
	}

	stime = stime.replace(':', '');

	if (stime.length == 3){
		hour = stime.substring(0,1);
		minute = stime.substring(1,3);
	} else if (stime.length == 4){
		hour = stime.substring(0,2);
		minute = stime.substring(2,4);
	}else{
		alert("Invalid time format.");
		thisTime.value = "";
		seg_validTime=false;
		thisTime.focus();
		return;
	}

	jtime = hour + ":" + minute;
	js_setTime(jtime);

	if (hour==0){
		 hour = 12;
		 document.getElementById(AMPM).value = "AM";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "PM";
	}

	ftime =  hour + ":" + minute;

	if(!ftime.match(f1) && !ftime.match(f2)){
		thisTime.value = "";
		alert("Invalid time format.");
		seg_validTime=false;
		thisTime.focus();
	}else{
		thisTime.value = ftime;
		seg_validTime=true;
	}
}// end of function setFormatTime


function displayRowDeath(){
		var objDischargeType = document.discform.relart;

		 if (objDischargeType[6].checked==true)
				 document.getElementById('row_deaths').style.display = '';
		 else
				 document.getElementById('row_deaths').style.display = 'none';

}

//added by VAN 10-30-09
function preSet(){
	var mgh = '<?=$encinfo["is_maygohome"]?>';
	var released = '<?=$_GET['released']?>';

	setShowRow(mgh);
}

function jsSetMGH(encounter_nr,is_mgh){
	//alert(encounter_nr);
	//var admission = d.admission_date.value+" "+admission_time.value+":00 "+d.selAMPM2.value;
	var mgh_date = document.getElementById('x_date').value+" "+document.getElementById('x_time').value+":00 "+document.getElementById('selAMPM').value;

	xajax_setMGH(encounter_nr, is_mgh, mgh_date);
}

function setShowRow(mgh){
	if (mgh==1){
		document.getElementById('row_undo').style.display='';
		document.getElementById('row_mgh').style.display='none';
		document.getElementById('row_discharge').style.display='';

		document.getElementById('row_disctype').style.display='';
		document.getElementById('row_notes').style.display='';
		document.getElementById('w_date').innerHTML = 'Discharged ';
		document.getElementById('w_time').innerHTML = 'Discharged ';
	}else{
		document.getElementById('row_undo').style.display='none';
		document.getElementById('row_mgh').style.display='';
		document.getElementById('row_discharge').style.display='none';

		document.getElementById('row_disctype').style.display='none';
		document.getElementById('row_notes').style.display='none';
		document.getElementById('w_date').innerHTML = 'MGH ';
		document.getElementById('w_time').innerHTML = 'MGH ';
	}

}
//--------------------------------

function pruf(d){
	if(!d.sure.checked){
		return false;
	}else{
		if(!d.encoder.value){
			alert("<?php echo $LDAlertNoName ?>");
			d.encoder.focus();
			return false;
		}
		if (!d.x_date.value){ alert("<?php echo "$LDAlertNoDate ";
 $dfbuffer="LD_".strtr($date_format,".-/","phs");
	echo $$dfbuffer;
?>"); d.x_date.focus();return false;}
		if (!d.x_time.value){ alert("<?php echo $LDAlertNoTime ?>"); d.x_time.focus();return false;}
		// Check if death
		if(d.relart[6].checked==true&&d.x_date.value!=""){
			if(!confirm("<?php echo $LDDeathDateIs ?> "+d.x_date.value+". <?php echo "$LDIsCorrect $LDProceedSave" ?>")) return false;
		}
		return true;
	}
}

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

//-->
</script>


<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>

<link rel="stylesheet" type="text/css" media="all" href="<?php echo $root_path; ?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?php echo $root_path; ?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?php echo $root_path; ?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?php echo $root_path; ?>js/jscalendar/calendar-setup_3.js"></script>


<?php

	$sTemp = ob_get_contents();
	ob_end_clean();

	$smarty->append('JavaScript',$sTemp);

} // End of if !$released
 else{
	 ?>

<script type="text/javascript">
	//added by VAN 06-27-2010
	function preSet(){
		var released = '<?=$_GET['released']?>';

		document.getElementById('row_disctype').style.display='';
		//document.getElementById('row_deaths').style.display='';
		document.getElementById('row_notes').style.display='';
		document.getElementById('row_undo_discharge').style.display='';

		document.getElementById('w_date').innerHTML = 'Discharged ';
		document.getElementById('w_time').innerHTML = 'Discharged ';
	}

	function cancelDischarged(encounter_nr){
		res = confirm('Are you really sure to cancel the discharge info?');

		if (res)
				xajax_cancelDischarged(encounter_nr);
	}

	function redirectWindow(encounter_nr){
			 urlholder='<?=$breakfile?>';
			 window.location.href=urlholder;
	}
</script>

	<?php
 }
if(($mode=="release")&&($released)){
	$smarty->assign('sPrompt',$LDJustReleased);
}

if($patient_ok){

	$smarty->assign('thisfile',$thisfile);
	$smarty->assign('sBarcodeLabel','<img src="'.$root_path.'main/imgcreator/barcode_label_single_large.php?sid='.$sid.'&lang='.$lang.'&fen='.$full_en.'&en='.$pn.'" width=282 height=178>');
	$smarty->assign('img_source','<img '.$img_source.' align="top">');
	$smarty->assign('LDLocation',$LDPatListElements[0]);
	$smarty->assign('sLocation',$rm.strtoupper(chr($bd+96)));
	$smarty->assign('LDDate',$LDDate);

	if($released){
		$smarty->assign('released',TRUE);
		$smarty->assign('x_date',nl2br($x_date));
	}else{
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

		$smarty->assign('sDateInput','<input type="text" id="x_date" name="x_date" size=12 maxlength=10 value="'.formatdate2Local(date('Y-m-d'),$date_format).'"  onBlur="IsValidDate(this,\''.$date_format.'\')"  onKeyUp="setDate(this,\''.$date_format.'\',\''. $lang.'\')">');
		#$smarty->assign('sDateMiniCalendar',"<a href=\"javascript:show_calendar('discform.x_date','$date_format')\"><img ".createComIcon($root_path,'show-calendar.gif','0','top')."></a>");
		#edited by VAN  06-27-2010
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$TP_date_format=$$dfbuffer;

		$smarty->assign('sDateMiniCalendar','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="x_date_trigger" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');

		$jsCalScript = "<script type=\"text/javascript\">

											Calendar.setup (
											{
												inputField : \"x_date\",
												ifFormat : \"$phpfd\",
												showsTime : false,
												button : \"x_date_trigger\",
												singleClick : true,
												step : 1
											}
											);
											</script>
											";

		$smarty->assign('jsCalendarSetup', $jsCalScript);
	}
	$smarty->assign('LDClockTime',$LDClockTime);

	if($released)
		$smarty->assign('x_time',nl2br($x_time));
	else{
		#$smarty->assign('sTimeInput','<input type="text" id="x_time" name="x_time" size=12 maxlength=12 value="'.convertTimeToLocal(date('H:i:s')).'" onKeyUp=setTime(this,\''.$lang.'\')>');
		#edited by VAN 06-27-2010
		$time_disc = date("h:i");
		$meridian = date("A");

		if ($meridian=='PM'){
			$selected1 = "";
			$selected2 = "selected";
		}else{
			$selected1 = "selected";
			$selected2 = "";
		}


		$admtime = '<input type="text" id="x_time" name="x_time" size="4" maxlength="5" value="'.$time_disc.'" onChange="setFormatTime(this,\'selAMPM\')" />
							<select id="selAMPM" name="selAMPM">
								<option value="AM" '.$selected1.'>A.M.</option>
								<option value="PM" '.$selected2.'>P.M.</option>
							</select>&nbsp;<font size=1>[hh:mm]</font>';

		$smarty->assign('sTimeInput',$admtime);
	}
	$smarty->assign('LDReleaseType',$LDReleaseType);

	$sTemp = '';
	if($released){

		while($dis_type=$discharge_types->FetchRow()){
			if($dis_type['nr']==$relart){
				$sTemp = $sTemp.'&nbsp;';
				if(isset($$dis_type['LD_var'])&&!empty($$dis_type['LD_var'])) $sTemp = $sTemp.$$dis_type['LD_var'];
					else $sTemp = $sTemp.$dis_type['name'];
				break;
			}
		}
	}else{
		$init=1;
		while($dis_type=$discharge_types->FetchRow()){
							# We will display only discharge types 1 to 7
							if($dis_type['nr']<8){
					 $sTemp = $sTemp.'&nbsp;';
								 $onchange = '';
								 #if ($dis_type['nr']=='7')
								 #   $onchange = " onclick='displayRowDeath()' ";

					 $sTemp = $sTemp.'<input type="radio" name="relart" id="relart" onclick="displayRowDeath()" value="'.$dis_type['nr'].'"';
					 if($init){
						$sTemp = $sTemp.' checked';
						$init=0;
						 }
					 $sTemp = $sTemp.'>';
					 if(isset($$dis_type['LD_var'])&&!empty($$dis_type['LD_var'])) $sTemp = $sTemp.$$dis_type['LD_var'];
						else $sTemp = $sTemp.$dis_type['name'];
					 $sTemp = $sTemp.'<br>';
							}
		}
	}#$dis_type['nr']
	$smarty->assign('sDischargeTypes',$sTemp);

		$sTemp_death = '<input type="radio" id="option_death" name="option_death" value="1" /> Under 48 Hours
										<input type="radio" id="option_death" name="option_death" value="2" /> Beyond 48 Hours';

		$smarty->assign('sDeathRows',$sTemp_death);

	$smarty->assign('LDNotes',$LDNotes);

	if($released) $smarty->assign('info',nl2br($info));

	$smarty->assign('LDNurse',$LDNurse);

	$smarty->assign('encoder',$encoder);

	#if(!(($mode=='release')&&($released))) {

		$smarty->assign('bShowValidator',TRUE);

	#	if ($encinfo["is_maygohome"]){
			#$HTTP_POST_VARS['is_maygohome']=0;
			$smarty->assign('pbSubmit','<input type="submit" value="'.$LDRelease.'" style="cursor:pointer">');
			$smarty->assign('sValidatorCheckBox','<input type="checkbox" name="sure" value="1"><input type="hidden" id="is_maygohome" name="is_maygohome" value="1">');
			$smarty->assign('LDYesSure',$LDYesSure);

			$encounter_nr = $pn;
			$hasfinal_bill = $enc_obj->hasFinalBilling($encounter_nr);

			if ($hasfinal_bill){
				$smarty->assign('stoggleIcon','<img src="'.$root_path.'images/btn_mgh_undo.gif" border="0" align="center">');
				$smarty->assign('sToggleText','Can\'t undo MGH because the patient has a final bill.');
			}else{
				$smarty->assign('stoggleIcon','<img src="'.$root_path.'images/btn_mgh_undo.gif" border="0" align="center" style="cursor:pointer" onclick="jsSetMGH(\''.$pn.'\',0);">');
				$smarty->assign('sToggleText','Click to Undo May Go Home status');
			}
#		}else{
			#$HTTP_POST_VARS['is_maygohome']=1;
			#$smarty->assign('pbSubmit','<input type="submit" value="May Go Home" style="cursor:pointer">');
			#$smarty->assign('sValidatorCheckBox','<input type="checkbox" name="sure" value="1"><input type="hidden" id="is_maygohome" name="is_maygohome" value="0">');
			#$smarty->assign('LDYesSure','Yes, I\'m sure. Patient can go home.');

			$smarty->assign('stoggleIcon2','<img src="'.$root_path.'images/btn_mgh.gif" border="0" align="center" style="cursor:pointer" onclick="jsSetMGH(\''.$pn.'\',1);">');
			$smarty->assign('sToggleText2','Click if advised to go home');
#		}
#	}
	$smarty->assign('sUndoDischarge','<img src="'.$root_path.'images/btn_undo_discharge.gif" border="0" align="center" style="cursor:pointer" onclick="cancelDischarged(\''.$pn.'\',1);">');
	$smarty->assign('sUndoDischargeText','Click if you will cancel the discharge.');

	$sTemp = '<input type="hidden" name="mode" value="release">';

	if(($released)||($lock)) $sTemp = $sTemp.'<input type="hidden" name="lock" value="1">';

	$sTemp = $sTemp.'<input type="hidden" name="sid" value="'.$sid.'">
		<input type="hidden" name="lang" value="'.$lang.'">
		<input type="hidden" name="station" value="'.$station.'">
		<input type="hidden" name="ward_nr" value="'.$ward_nr.'">
		<input type="hidden" name="dept" value="'.$dept.'">
		<input type="hidden" name="dept_nr" value="'.$dept_nr.'">
		<input type="hidden" name="pday" value="'.$pday.'">
		<input type="hidden" name="pmonth" value="'.$pmonth.'">
		<input type="hidden" name="pyear" value="'.$pyear.'">
		<input type="hidden" name="rm" value="'.$rm.'">
		<input type="hidden" name="bd" value="'.$bd.'">
		<input type="hidden" name="pn" value="'.$pn.'">
		<input type="hidden" name="s_date" value="'."$pyear-$pmonth-$pday".'">';

	$smarty->assign('sHiddenInputs',$sTemp);


}else{
	$smarty->assign('sPrompt',"$LDErrorOccured $LDTellEdpIfPersist");
}

if(($mode=='release')&&($released)) $sBreakButton= '<img '.createLDImgSrc($root_path,'close2.gif','0').'>';
	else $sBreakButton= '<img '.createLDImgSrc($root_path,'cancel.gif','0').' border="0">';

$smarty->assign('pbCancel','<a href="'.$breakfile.'">'.$sBreakButton.'</a>');

$smarty->assign('sMainBlockIncludeFile','nursing/discharge_patient_form.tpl');

 /**
 * show Template
 */

 $smarty->display('common/mainframe.tpl');
 // $smarty->display('debug.tpl');
 ?>