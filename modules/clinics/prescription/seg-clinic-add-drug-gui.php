<?php
//created by cha Feb 6, 2010

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* Integrated Hospital Information System beta 2.0.0 - 2004-05-16
* GNU General Public License
* Copyright 2002,2003,2004 
*
* See the file "copy_notice.txt" for the licence notice
*/     
#define('LANG_FILE','specials.php');
define('LANG_FILE','nursing.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
$breakfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;
$returnfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;
$thisfile=basename(__FILE__);

//ajax

	
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');


$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Toolbar title
$smarty->assign('sToolbarTitle','Clinics:: Prescription Writer');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','Clinics:: Prescription Writer')");
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('title','Clinics:: Prescription Writer');
$smarty->assign('breakFile',$breakfile);

	ob_start();
 ?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/clinics/js/prescription-gui.js"></script>
<script type=type="text/javascript" language="javascript">

</script>
<?
$sTemp = ob_get_contents();
global $db;
$dbtime_format = "Y-m-d";
$fulltime_format = "F j, Y";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);
	
$smarty->assign('sSearchDrug', '<input type="text" class="jedInput" id="search_key" name="search_key" size="30" align="absmiddle"/>');
$smarty->assign('sSearchBtn', '<input type="image" class="jedInput" src="../../../gui/img/control/default/en/en_searchbtn.gif" style="cursor:pointer" align="absmiddle" id="save_prescription">');

$smarty->assign('sDosage', '<input type="text" class="jedInput" id="dosage" name="dosage" size="45" align="absmiddle"/>');
$smarty->assign('sQuantity', '<input type="text" class="jedInput" id="quantity" name="quantity" size="10" align="absmiddle"/>');

$res = $db->Execute("select * from seg_unit order by unit_name");
$options='<option value="0">--Select--</option>';
while($row=$res->FetchRow())
{
	$options.='<option value="'.$row['unit_id'].'">'.$row['unit_name'].'</option>';
}
$smarty->assign('sQuantityUnits','<select id="qty_unit" name="qty_unit">'.$options.'</select>');
$smarty->assign('sRoute', '<input type="text" class="jedInput" id="route" name="route" size="45" align="absmiddle"/>');
$option_days='';
for($i=1;$i<=31;$i++)
{
	 $option_days.='<option value="'.$i.'">'.$i.'</option>';
}
$smarty->assign('sPeriodDays', '<select id="period_days name="period_days">'.$option_days.'</select>');
$option_period='';
$period = array('Day(s)', 'Week(s)', 'Month(s)', 'Year(s)');
for($i=0;$i<count($period);$i++)
{
	$option_period.='<option value="'.$i.'">'.$period[$i].'</option>';
}
$smarty->assign('sPeriod', '<select id="period" name="period">'.$option_period.'</select>');
$smarty->assign('sRefill', '<select id="refill" name="refill">
<option value="0">0</option>
<option value="1">1</option>
</select>');

$smarty->assign('sStartDate','<span id="show_startdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['requestdate'])) : $curDate_show).'</span>
<input class="jedInput" name="startdate" id="startdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['startdate'])) : $curDate).'" style="font:bold 12px Arial">');

$smarty->assign('sCalendarIcon','<img '.createComIcon($root_path,'show-calendar.gif','0').' id="startdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup({
		displayArea : \"show_startdate\",
			inputField : \"startdate\", 
			ifFormat : \"%Y-%m-%d\", 
			daFormat : \" %B %e, %Y\", 
			showsTime : false, 
			button : \"startdate_trigger\", 
			singleClick : true, 
			step : 1
});
</script>";    
$smarty->assign('jsCalendarSetup', $jsCalScript);
$smarty->assign('sDrugReason', '<textarea class="segInput" id="reason_drug" name="reason_drug" cols="40" rows="2" style="font:bold 12px Arial;"></textarea>');
$smarty->assign('sSaveDosage','<input type="checkbox" class="segInput" name="is_save" id="is_save"/>');
$smarty->assign('sSigBtn', '<input type="button" class="segButton" value="Add SIG"  align="absmiddle" style="cursor:pointer" onclick="open_add_sig()"/>');
ob_start();
?>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" id="userck" value="<?php echo $userck?>">  
	<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
	<input type="hidden" name="encoder" id="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	<input type="hidden" name="key" id="key">
	<input type="hidden" name="pagekey" id="pagekey"> 

 <?
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

 /**
 * show Template
 */
 # Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','clinics/add-drug-prescription.tpl');

$smarty->display('common/mainframe.tpl');

?>

