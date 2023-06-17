<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/or/ajax/or-room-common.php');


define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');
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
$currDate=date('m/d/Y');

if (!$_GET['from'])
	$breakfile=$root_path."/main/op-doku.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."/main/op-doku.php".URL_APPEND;
}

$thisfile='seg-or-room-details.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Operating Room :: Room Status Details";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);



$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
# Collect javascript code
ob_start();
	 # Load the javascript code
?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>


<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>

<script type="text/javascript">
var $J = jQuery.noConflict();


//document.observe('dom:loaded', initialize);
</script>
<script type="text/javascript" language="javascript">
	function update_room_status(){
			if($('room_status').value=='done'){
				if(op=confirm("Are you sure you want to process?")){
					xajax_update_room_status($('or_main_refno').value,$('done_date').value);
				}
			}else
				alert("Please select done to process.");
	}
	function updateList(){
		window.parent.refreshList();
	}
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$or_main_refno=$_GET['or_main_refno'];
global $db;

$strSQL="SELECT
						som.ceo_refno      AS case_no,
						sor.room_name,
						som.date_operation,
						fn_get_person_lastname_first(ce.pid) AS patient_name
					FROM seg_or_main AS som
						INNER JOIN seg_or_room AS sor
							ON sor.room_nr = som.room_nr
						INNER JOIN care_encounter AS ce
							ON ce.encounter_nr = som.encounter_nr
					WHERE som.or_main_refno = '$or_main_refno'";
$result=$db->Execute($strSQL);
if($row=$result->FetchRow()){
	$dte_operation = date('m/d/Y',strtotime($row['date_operation']));
	$patient_name = $row['patient_name'];
	$case_number  = $row['case_no'];
	$room_name = $row['room_name'];
}
else
	$dte_operation=$default_date;

$default_date=date('m/d/Y');


$s_or_main_refno='<input type="hidden" name="or_main_refno" id="or_main_refno" value="'.$or_main_refno.'"/>';

$operation_date='
<input type="text" disabled="true"
 id="operation_date" style="font: bold 12px Arial;"
 value="'.$dte_operation.'"
 maxlength="10"
 size="10"
 name="operation_date"/>';

 $case_number='
<input type="text" disabled="true"
 id="case_number" style="font: bold 12px Arial;" size="15"
 value="'.$case_number.'"
 name="case_number"/>';

 $patient_name='
<input type="text" disabled="true"
 id="patient_name" style="font: bold 12px Arial;"  size="30"
 value="'.$patient_name.'"
 name="patient_name"/>';

 $room_name='
<input type="text" disabled="true"
 id="room_name" style="font: bold 12px Arial;" size="30"
 value="'.$room_name.'"
 name="room_name"/>';



$done_date='
<input type="text"
 id="done_date" style="font: bold 12px Arial;"
 value="'.$default_date.'"
 maxlength="10"
 size="10"
 name="done_date"/>
<img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;" id="done_date_trigger"
src="'.$root_path.'gui/img/common/default/show-calendar.gif">'.
'<script type="text/javascript">
							Calendar.setup ({
								inputField : "done_date", ifFormat : "%m/%d/%Y",
								 showsTime : false,
								 button : "done_date_trigger",
								 singleClick : true,
								 step : 1
							});
</script>';

$room_status_op='<select name="room_status" id="room_status">
									<option value="done">Done</option>
									<option value="undone">Undone</option>
								</select>';




$save_button='<img height="23" border="0" align="absmiddle" width="72"
onclick="update_room_status();" title="Save
data" src="'.$root_path.'/gui/img/control/default/en/en_savedisc.gif"
name="saveButton" id="saveButton">';

$cancel_button='<img height="23" border="0" align="absmiddle" width="72"
onclick="javascript:window.parent.cClick();" title="Cancel" src="'.$root_path.'/gui/img/control/default/en/en_cancel.gif"
name="cancelButton" id="cancelButton">';



$smarty->assign('s_case_number', $case_number);
$smarty->assign('s_room_name', $room_name);
$smarty->assign('s_patient_name', $patient_name);
$smarty->assign('s_or_main_refno', $s_or_main_refno);
$smarty->assign('s_room_status', $room_status_op);
$smarty->assign('s_operation_date', $operation_date);
$smarty->assign('s_done_date', $done_date);
$smarty->assign('s_room_save', $save_button);
$smarty->assign('s_room_cancel', $cancel_button);

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'" method="POST" id="or_room_form" name="or_room_form">');
$smarty->assign('form_end','</form>');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />

<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
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
$smarty->assign('sMainBlockIncludeFile','or/or_room_update.tpl');
$smarty->display('common/mainframe.tpl');

