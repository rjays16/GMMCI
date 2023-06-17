<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/dialysis/ajax/dialysis-transaction.common.php');
//require_once $root_path.'include/care_api_classes/dialysis/class_dialysis_request.php';


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_dialysis_user';
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

$thisfile='seg-dialysis-request-view.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once $root_path."include/care_api_classes/dialysis/class_dialysis.php";
require_once $root_path."include/care_api_classes/class_encounter.php";
$dialysis_obj = new SegDialysis();
$enc_obj = new Encounter($encounter_nr);
global $db;

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Dialysis :: New Request";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

#save data here
if(isset($_POST["submitted"])) {

	$visit_no = ($_POST["visit_no"]+1);
	$dialysis_data = array(
		'encounter_nr'=>$_POST["encounter_nr"],
		'pid' => $_POST["pid"],
		'transaction_date'=>$_POST["requestdate"],
		'status'=>strtoupper($_POST["request_status"]),
		'requesting_doctor'=>$_POST["request_doctor"],
		'attending_nurse'=>$_POST["attending_nurse"],
		'dialysis_type'=>strtoupper($_POST["dialysis_type"]),
		'remarks'=>$_POST["remarks"],
		'reason'=>$_POST["reason"]
	);

	//update dialysis
	$saveok = $dialysis_obj->updateTransactionDetails($_POST["refno"],$dialysis_data);
	if($saveok!==FALSE) {
		$saveok = true;
	}else {
		echo "error:".$dialysis_obj->getErrorMsg()."\nsql:".$dialysis_obj->sql;
	}
	if($saveok) {
		$smarty->assign('sysInfoMessage','Dialysis transaction successfully updated.');
	}else {
		$smarty->assign('sysErrorMessage','<strong>Error:</strong> Cannot update dialysis transaction.<br/> SQL_ERROR:'.$dialysis_obj->getErrorMsg());
	}

}

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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">

function initialize() {
	xajax_getDoctors($('doctor_nr').value);
	xajax_getNurses($('nurse_nr').value);
	xajax_setVisitNo($('pid').value);
}

function validate() {

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
	else if($('attending_nurse').value=="0")
	{
		alert("Please select the attending nurse.");
		$('attending_nurse').focus();
		return false;
	}

	return true;
}

/*function changeStatus(id, refno){
	var answer = confirm("Performing this action will disable any requests for Reference #"+refno+". Continue?")
	if(answer) {
		var reason = prompt("Please log the reason.");
		//xajax_changeTransactionStatus(refno, $(id).value, reason);
		$('reason').value = reason;
	}
} */

function changeStatus(id, refno, enc_nr){
	if($(id).value=="1") {
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
}

function openHistoryReport()
{
	window.open('seg-dialysis-history-report.php?pid='+$('pid').value,'history_report',"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}

document.observe('dom:loaded', initialize);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);

$pid = $_POST["pid"]?$_POST["pid"]:$_GET["pid"];
$encounter_nr = $_POST["encounter_nr"]?$_POST["encounter_nr"]:$_GET["encounter_nr"];
$refno = $_POST["refno"] ? $_POST["refno"] : $_GET["refno"];
$request_info = $dialysis_obj->getTransactionDetails($pid,$encounter_nr, $refno);

$smarty->assign('submitBtn','<button class="segButton" onclick="if (confirm(\'Process this dialysis request?\')) return validate();"><img src="'.$root_path.'gui/img/common/default/arrow_redo.png"/>Update</button>');
$smarty->assign('cancelBtn','<button class="segButton" onclick="javascript:window.parent.cClick();"><img src="'.$root_path.'gui/img/common/default/cancel.png"/>Cancel</button>');
$smarty->assign('historyBtn','<button class="segButton" onclick="return false;"><img src="'.$root_path.'gui/img/common/default/report.png"/>Print History</button>');

$smarty->assign('encounter_nr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$encounter_nr.'"/>');
$smarty->assign('patient_id','<input id="pid" name="pid" class="clear" type="text" value="'.$pid.'" readonly="readonly" style="color:#006600; font:bold 16px Arial;"/>');
$smarty->assign('patient_name','<input class="segInput" id="name" name="name" type="text" size="30" style="font:bold 12px Arial; color:#0000ff" readonly="readonly" value="'.$request_info["patient_name"].'"/>');

/*$new_refno = $dialysis_obj->getNewRefno();
$refno = $_POST["reference_no"] ? $_POST["reference_no"] : $new_refno;
$smarty->assign('requestReferenceNo', '<input class="segInput" id="reference_no" name="reference_no" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="'.$refno.'"/>');*/

$visit_no = $_POST["visit_no"] ? $_POST["visit_no"] : $request_info["visit_no"];
$smarty->assign('visit_no', '<input class="clear" id="visit_no" name="visit_no" type="text" style="font:bold 12px Arial" readonly="readonly" value="'.$visit_no.'"/>');
$smarty->assign('reference_no', '<input class="clear" id="refno" name="refno" type="text" style="font:bold 12px Arial" readonly="readonly" value="'.$request_info["refno"].'"/>');

$smarty->assign('requestDoctors', '<select class="segInput" id="request_doctor" name="request_doctor" style="font:bold 12px Arial"></select>');
$smarty->assign('requestNurses', '<select class="segInput" id="attending_nurse" name="attending_nurse" style="font:bold 12px Arial"></select>');

$dialysis_type = strtolower($_POST["dialysis_type"] ? $_POST["dialysis_type"]:$request_info["dialysis_type"]);
$smarty->assign('requestDialysisType',
				 '<input type="radio" id="dtypeb" name="dialysis_type" '.(($dialysis_type=="before")?'checked="checked" ':'').' value="before"/><label>Before Dialysis</label>&nbsp;&nbsp;
				 <input type="radio" id="dtypea" name="dialysis_type" '.(($dialysis_type=="after")?'checked="checked" ':'').' value="after"/><label>After Dialysis</label>
				 ');
//$smarty->assign('dialysis_type', '<input type="hidden" id="dialysis_type" name="dialysis_type" value="";/>');
$smarty->assign('requestRemarks','<textarea class="segInput" id="remarks" name="remarks" style="width:100%"></textarea>');
$encoder = $_POST["request_encoder"] ? $_POST["request_encoder"] : $_SESSION["sess_user_name"];
$smarty->assign('requestEncoder', '<input class="segInput" id="request_encoder" name="request_encoder" type="text" size="30" style="font:bold 12px Arial" value="'.$encoder.'"/>');

$dialysis_status = strtolower($_POST["request_status"] ? $_POSt["request_status"]:$request_info["status"]);
$smarty->assign('requestStatus', '<select class="segInput" id="request_status" name="request_status" onchange="changeStatus(this.id, \''.$refno.'\',\''.$encounter_nr.'\')">
				<option value="undone" '.(($dialysis_status=="undone")?'selected="selected" ':'').'>Undone</option>
				<option value="done" '.(($dialysis_status=="done")?'selected="selected" ':'').'>Done</option>
				</select>
				');

$req_date = $_POST["requestdate"] ? $_POST["requestdate"]:$request_info["transaction_date"];
$smarty->assign('requestDate','<span id="show_requestdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($request_info["transaction_date"] ? date($fulltime_format,strtotime($req_date)) : $curDate_show).'</span>
<input class="jedInput" name="requestdate" id="requestdate" type="hidden" value="'.($req_date ? date($dbtime_format,strtotime($req_date)) : $curDate).'" style="font:bold 12px Arial">');

$smarty->assign('sCalendarIcon','<img '.createComIcon($root_path,'date_add.png','0').' id="requestdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup({
		displayArea : \"show_requestdate\",
			inputField : \"requestdate\",
			ifFormat : \"%Y-%m-%d %H:%M\",
			daFormat : \" %B %e, %Y %I:%M%P\",
			showsTime : true,
			button : \"requestdate_trigger\",
			singleClick : true,
			step : 1
});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);

$remarks = $_POST["remarks"] ? $_POST["remarks"]:$request_info["remarks"];
$smarty->assign('requestRemarks','<textarea class="segInput" id="remarks" name="remarks" style="width:300px">'.$remarks.'</textarea>');
$encoder = $_POST["request_encoder"] ? $_POST["request_encoder"] : $_SESSION["sess_user_name"];
$smarty->assign('requestEncoder', '<input class="clear" id="request_encoder" name="request_encoder" type="text" size="30" style="font:bold 12px Arial" value="'.$encoder.'"/>');

$smarty->assign('doctor_nr', '<input type="hidden" id="doctor_nr" name="doctor_nr" value="'.$request_info["requesting_doctor"].'"/>');
$smarty->assign('nurse_nr', '<input type="hidden" id="nurse_nr" name="nurse_nr" value="'.$request_info["attending_nurse"].'"/>');
$smarty->assign('log_reason', '<input type="hidden" id="reason" name="reason" value=""/>');

$smarty->assign('historyBtn','<button class="segButton" onclick="openHistoryReport();return false;"><img src="'.$root_path.'gui/img/common/default/report.png"/>Print History</button>');

$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('encounter_type', '<input type="hidden" name="encounter_type" id="encounter_type" />');

//$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="validate();">');
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
$smarty->assign('sMainBlockIncludeFile','dialysis/request_view.tpl');
$smarty->display('common/mainframe.tpl');

