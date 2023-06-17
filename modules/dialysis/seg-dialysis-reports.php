<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/dialysis/ajax/dialysis-transaction.common.php');

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

$thisfile='seg-dialysis-reports.php';
$breakfile=$root_path."modules/dialysis/seg-dialysis-menu.php".URL_APPEND;

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
$title = "Dialysis :: Reports";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">

function selOnChange()
{
	switch($('selreport').value)
	{
		case 'daily':
			$('seltimefrom').style.display="";
			$('seltimeto').style.display="";
		break;
		case 'income':
			$('seltimefrom').style.display="none";
			$('seltimeto').style.display="none";
		break;
	}
}

function openReport()
{
	var url="";
	var params="";
	var report = $('selreport').value;
	switch(report)
	{
		case 'daily':
			url = "seg-dialysis-"+report+"-report.php";
			var time_from = $('fromHour').value+":"+$('fromMin').value+" "+$('fromMeridian').value;
			var time_to = $('toHour').value+":"+$('toMin').value+" "+$('toMeridian').value;
			params = "date_from="+$('seldate_from').value+"&date_to="+$('seldate_to').value+"&time_from="+time_from+"&time_to="+time_to;
		break;
		case 'income':
			url = "seg-dialysis-"+report+"-report.php";
			params = "date_from="+$('seldate_from').value+"&date_to="+$('seldate_to').value;
		break;
	}
	window.open(url+"?"+params,report,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}
document.observe('dom:loaded', selOnChange);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('seldate_from', '<input class="segInput" name="seldate_from" id="seldate_from" type="text" size="12" value=""/>
												<img src="'.$root_path.'gui/img/common/default/calendar_add.png" id="tg_seldatefrom" align="absmiddle" style="cursor:pointer;"  />');
$smarty->assign('seldatefrom_js', '<script type="text/javascript">
														Calendar.setup ({
																inputField : "seldate_from", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_seldatefrom", singleClick : true, step : 1
														});
												</script>');

$smarty->assign('seldate_to', '<input class="segInput" name="seldate_to" id="seldate_to" type="text" size="12" value=""/>
												<img src="'.$root_path.'gui/img/common/default/calendar_add.png" id="tg_seldateto" align="absmiddle" style="cursor:pointer;"  />');
$smarty->assign('seldateto_js','<script type="text/javascript">
														Calendar.setup ({
																inputField : "seldate_to", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_seldateto", singleClick : true, step : 1
														});
												</script>');

#added by VAN 09-19-08
$selectfromHour .= '<select id="fromHour" name="fromHour" class="segInput">';
for($i = 0; $i <= 12; $i++){
if ($i<10)
	$i = '0'.$i;

if ($i==7)
	$selectfromHour .= "\t<option value='$i' 'selected'>$i</option>\n";
else
	$selectfromHour .= "\t<option value='$i'>$i</option>\n";
}
$selectfromHour .= '</select>';

#minutes
$selectfromMin .= '<select id="fromMin" name="fromMin" class="segInput">';

for($i = 0; $i < 60; $i++){
if ($i<10)
	$i = '0'.$i;
$selectfromMin .= "\t<option value='$i'>$i</option>\n";
}
$selectfromMin .= '</select>';

#meridian
$selectfromMeridian .= '<select id="fromMeridian" name="fromMeridian" class="segInput">';
$meridime = array("AM", "PM");
foreach ($meridime as $i) {
$selectfromMeridian .= "\t<option value='$i'>$i</option>\n";
}
$selectfromMeridian .= '</select>';

# SHIFT : TO
#hours
$selecttoHour .= '<select id="toHour" name="toHour" class="segInput">';

if (!($i))
 $i=5;

for($i = 0; $i <= 12; $i++){
if ($i<10)
	$i = '0'.$i;

if ($i==5){
	$selecttoHour .= "\t<option value='$i' 'selected'>$i</option>\n";
}else{
	$selecttoHour .= "\t<option value='$i'>$i</option>\n";
}
}
$selecttoHour .= '</select>';

#minutes
$selecttoMin .= '<select id="toMin" name="toMin" class="segInput">';

for($i = 0; $i < 60; $i++){
if ($i<10)
	$i = '0'.$i;
$selecttoMin .= "\t<option value='$i'>$i</option>\n";
}
$selecttoMin .= '</select>';

#meridian
$selecttoMeridian .= '<select id="toMeridian" name="toMeridian" class="segInput">';
$meridime = array("AM", "PM");
foreach ($meridime as $i) {
if ($i=='PM')
	$selecttoMeridian .= "\t<option value='$i' 'selected'>$i</option>\n";
else
	$selecttoMeridian .= "\t<option value='$i'>$i</option>\n";
}
$selectfromMeridian .= '</select>';

$sTempShift_from = $selectfromHour.'&nbsp;<b>:</b>&nbsp;'.$selectfromMin.'&nbsp;'.$selectfromMeridian;
$sTempShift_to = $selecttoHour.'&nbsp;<b>:</b>&nbsp;'.$selecttoMin.'&nbsp;'.$selecttoMeridian;
$smarty->assign('sShift_from',$sTempShift_from);
$smarty->assign('sShift_to',$sTempShift_to);

$smarty->assign('view_btn', '<button class="segButton" onclick="openReport();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/page_white_acrobat.png"/>View Report</button>');

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
$smarty->assign('sMainBlockIncludeFile','dialysis/report_gui.tpl');
$smarty->display('common/mainframe.tpl');

