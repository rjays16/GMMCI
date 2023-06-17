<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','nursing.php');

#$local_user='ck_dialysis_user';
$local_user='ck_pflege_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

if(!isset($pid)) $pid=0;
if(!isset($encounter_nr)) $encounter_nr=0;

$thisfile='nursing-waitlist.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

#require_once $root_path."include/care_api_classes/dialysis/class_dialysis.php";
require_once $root_path."include/care_api_classes/class_encounter.php";

$enc_obj = new Encounter($encounter_nr);
global $db;

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Wait List Patient:: Details";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

#$encounter_nr = $_GET['encounter_nr'];
$encounter_nr = $_POST["encounter_nr"]?$_POST["encounter_nr"]:$_GET["encounter_nr"];
$ward_nr = $_POST["ward_nr"]?$_POST["ward_nr"]:$_GET["ward_nr"];
$encInfo=$enc_obj->getEncounterInfo($encounter_nr);

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">

/*function initialize() {
	xajax_getDoctors($('doctor_nr').value);
	xajax_getNurses($('nurse_nr').value);
	xajax_setVisitNo($('pid').value);
}     */

/*function validate() {

	if($('encounter_nr').value=="")
	{
		alert("Please select a patient first.");
		$('name').focus();
		return false;
	}
	else if($('request_doctor').value=="0")
	{
		alert("Please select the requesting doctor.");
		$('request_doctor').focus();
		return false;
	}

	return true;
}          */

/*function changeStatus(id, refno, enc_nr){
	if($(id).value==$('requestStatus_saved').value) {
		return false;
	}
	if($(id).value=="done") {
		var answer = confirm("Performing this action will disable any requests for Reference #"+refno+". Continue?")
		if(answer) {
			xajax_changeTransactionStatus(refno, $(id).value, "", enc_nr);
		}
	}else {
		var reason = prompt("Please log the reason to UNDONE request.");
		if(reason) {
			xajax_changeTransactionStatus(refno, $(id).value, reason, enc_nr);
		}
	}
	return false;
}               */

function getPatientAdmissionInfo(pn,area_type){
	//alert("pn = "+pn);
	//urlholder="<?php echo $root_path ?>modules/registration_admission/aufnahme_daten_zeigen.php<?php echo URL_REDIRECT_APPEND ?>&ptype=nursing&popUp=1&from=such&encounter_nr="+pn+"&target=search&fromnurse=1&area_type="+area_type;
	//modified by CHA, 05072010
	//adminwin<?php echo $sid ?>=window.open(urlholder,"adminwin<?php echo $sid ?>","width=950,height=570,menubar=no,left=60,top=55,resizable=yes,scrollbars=yes");
	//modified by VAN 08-19-2010
	return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/registration_admission/aufnahme_daten_zeigen.php<?php echo URL_REDIRECT_APPEND ?>&ptype=nursing&popUp=1&from=such&encounter_nr='+pn+'&target=search&fromnurse=1&area_type='+area_type,
																	850, 420, 'fGroupTray', 0, 'auto'),
																	WIDTH,850, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="pSearchClose2();">',
																 CAPTIONPADDING,2, CAPTION,'Patient Admission Data',
																 MIDX,0, MIDY,0,
																 STATUS,'Patient Admission Data');
}

function getrem(pn, ward_nr, station){
	//urlholder="nursing-station-remarks.php<?php echo URL_REDIRECT_APPEND; ?>&pn="+pn+"<?php echo "&dept_nr=$ward_nr&location_nr=$ward_nr&pday=$pday&pmonth=$pmonth&pyear=$pyear&station=$station"; ?>";
	//patientwin=window.open(urlholder,pn,"width=700,height=500,menubar=no,resizable=yes,scrollbars=yes,left=120,top=100");

	//var pday = <?echo date("d")?>
	//alert(pday);

	return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/nursing/nursing-station-remarks.php<?php echo URL_REDIRECT_APPEND ?>&ptype=nursing&popUp=1&from=such&pn='+pn+'&target=search&fromnurse=1&dept_nr='+ward_nr+'&location_nr='+ward_nr+'&station='+station,
																	820, 420, 'fGroupTray', 0, 'auto'),
																	WIDTH,820, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="pSearchClose2();">',
																 CAPTIONPADDING,2, CAPTION,'Notes',
																 MIDX,0, MIDY,0,
																 STATUS,'Notes');

}

function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
	refreshWindow();
}

function pSearchClose2() {
	cClick();  //function in 'overlibmws.js'
}

function refreshWindow(){
	//alert('refresh = '+window.location.href);
	window.location.href=window.location.href;
}

function Transfer(wd, pn, station)
{
	//urlholder="nursing-station-assignwaiting.php<?php echo URL_REDIRECT_APPEND ?>&pn="+pn+"&pat_station="+pw+"&ward_nr="+wd+"&station="+pw+"&transfer=1&modetransfer="+modetransfer+"&waiting="+waiting;
	//if(confirm("<?php echo $LDSureTransferPatient ?>")){
	/*if(confirm("Do you really have to transfer this patient?")){
		urlholder="nursing-station-transfer-select.php<?php echo URL_REDIRECT_APPEND ?>&pn="+pn+"&pat_station="+pw+"&ward_nr=<?php echo $ward_nr ?>&station=<?php echo $station ?>";
		transwin<?php echo $sid ?>=window.open(urlholder,"transwin<?php echo $sid ?>","width=650,height=600,menubar=no,resizable=yes,scrollbars=yes, left=150,top=60");
	}   */
	return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/nursing/nursing-station-assignwaiting.php<?php echo URL_REDIRECT_APPEND ?>&ptype=nursing&popUp=1&from=such&pn='+pn+'&target=search&fromnurse=1&pat_station='+station+'&ward_nr='+wd+'&station='+station+'&waiting=1',
																	820, 420, 'fGroupTray', 0, 'auto'),
																	WIDTH,820, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick=" pSearchClose();">',
																 CAPTIONPADDING,2, CAPTION,'Assign Bed',
																 MIDX,0, MIDY,0,
																 STATUS,'Assign Bed');


}

function release(room,bed,pid)
{
	urlholder="nursing-station-patient-release.php<?php echo URL_REDIRECT_APPEND; ?>&rm="+room+"&bd="+bed+"&pn="+pid+"<?php echo "&pyear=".$pyear."&pmonth=".$pmonth."&pday=".$pday."&tb=".str_replace("#","",$cfg['top_bgcolor'])."&tt=".str_replace("#","",$cfg['top_txtcolor'])."&bb=".str_replace("#","",$cfg['body_bgcolor'])."&d=".$cfg['dhtml']; ?>&station=<?php echo $station; ?>&ward_nr=<?php echo $ward_nr; ?>&popUp=1";
	//indatawin=window.open(urlholder,"bedroom","width=700,height=450,menubar=no,resizable=yes,scrollbars=yes"
	window.location.href=urlholder;
}


function key_check(e, value) {
	 var character = String.fromCharCode(e.keyCode);
	 var number = /^\d+$/;
	 var reg = /^[-+]?[0-9]+((\.)|(\.[0-9]+))?$/;
	 if (character=='¾') {
		 character = '.';
	 }
	 //alert('e = '+e.keyCode);
	 var text_value = value+character;
	 if ((e.keyCode==190 && text_value.match(reg)!=null) || (e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || e.keyCode==9 || e.keyCode==110 || (e.keyCode>=36 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105))) {
			return true;
	 }
	 if (character.match(number)==null) {
		 return false;
	 }
}

function convertNumberValue(id, val){
		if ($(id)) $(id).value = formatNumber(val, 2)
		if ($(id)) $(id).setAttribute('value',val)
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

//document.observe('dom:loaded', initialize);
</script>

<?php
#$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);

$pid = $_POST["pid"]?$_POST["pid"]:$_GET["pid"];
$encounter_nr = $_POST["encounter_nr"]?$_POST["encounter_nr"]:$_GET["encounter_nr"];
#$patient_name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
$patient_name = stripslashes(strtoupper($encInfo['name_last'])).', '.stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle']));
$ward = $_POST["ward"]?$_POST["ward"]:$_GET["ward"];
$patient_id = $encInfo['pid'];
$birthdate = date("F j, Y", strtotime($encInfo['date_birth']));
$area_type = $_GET['area_type'];
$refno = $_POST["refno"] ? $_POST["refno"] : $_GET["refno"];

//$smarty->assign('cancelBtn','<button class="segButton" onclick="refreshWindow(); javascript:window.parent.cClick();"><img src="'.$root_path.'gui/img/common/default/cancel.png"/>Cancel</button>');
$smarty->assign('cancelBtn','<button class="segButton" onclick="pSearchClose();"><img src="'.$root_path.'gui/img/common/default/cancel.png"/>Cancel</button>');

$smarty->assign('encounter_nr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$encounter_nr.'"/>');
$smarty->assign('casenum','<input class="clear" id="casenum" name="casenum" type="text" readonly="readonly" style="color:#006600; font:bold 14px Arial;" value="'.$encounter_nr.'"/>');
$smarty->assign('patient_id','<input id="pid" name="pid" class="clear" type="text" value="'.$patient_id.'" readonly="readonly" style="color:#006600; font:bold 14px Arial;"/>');
$smarty->assign('patient_name','<input class="clear" id="name" name="name" type="text" size="40" style="color:#006600; font:bold 14px Arial;" readonly="readonly" value="'.$patient_name.'"/>');
$smarty->assign('ward','<input id="ward" name="ward" type="hidden" value="'.$ward.'"/>');
$smarty->assign('birthday','<input class="clear" id="birthday" name="birthday" type="text" style="color:#006600; font:bold 14px Arial;" readonly="readonly" value="'.$birthdate.'"/>');
$smarty->assign('ward_nr','<input id="ward_nr" name="ward_nr" type="hidden" value="'.$ward_nr.'"/>');
$smarty->assign('patient_ward', '<input class="clear" id="patient_ward" name="patient_ward" type="text" style="color:#006600; font:bold 14px Arial;" readonly="readonly" value="'.$ward.'"/>');

//options
$smarty->assign('patient_details', '<img src="../../gui/img/common/default/pdata.gif" style="cursor: pointer; cursor: hand;" onClick="getPatientAdmissionInfo(\''.$encounter_nr.'\',\''.$area_type.'\')"> ');
$smarty->assign('patient_details_info', 'View admission data');
$smarty->assign('nurse_notes', '<img src="../../gui/img/common/default/bubble2.gif" style="cursor: pointer; cursor: hand;" onClick="getrem(\''.$encounter_nr.'\',\''.$ward_nr.'\',\''.$station.'\')">');
$smarty->assign('nurse_notes_info', 'Read or write notes about the patient');
$smarty->assign('patient_transfer', '<img src="../../gui/img/common/default/xchange.gif" style="cursor: pointer; cursor: hand;" onClick="Transfer(\''.$ward_nr.'\',\''.$encounter_nr.'\',\''.$station.'\')">');
$smarty->assign('patient_transfer_info', 'Assign bed to patient');
$smarty->assign('patient_discharge', '<img src="../../gui/img/common/default/bestell.gif" style="cursor: pointer; cursor: hand;" onClick="release(\''.$room_nr.'\',\''.$bed_nr.'\',\''.$encounter_nr.'\')">');
$smarty->assign('patient_discharge_info', 'Discharge patient in ward');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','nursing/waitlist_details.tpl');
$smarty->display('common/mainframe.tpl');

