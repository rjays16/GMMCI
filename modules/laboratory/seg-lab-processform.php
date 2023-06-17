<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'modules/laboratory/ajax/lab-new.common.php');
require($root_path.'include/inc_environment_global.php');
#echo "path = ".$root_path.'modules/laboratory/ajax/lab-new.common.php';
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

if (isset($_GET['refno']) && $_GET['refno']){
	$refno = $_GET['refno'];
}

if (isset($_GET['service_code']) && $_GET['service_code']){
	$service_code = $_GET['service_code'];
}

if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}

#echo "code = ".$service_code;

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj = new SegLab();

#edited by VAN 07-08-08
$requestInfo = $srvObj->getLabRequestInfo($refno, $pid, $service_code);
#echo "sql = ".$srvObj->sql;

if ((!$requestInfo) || empty($requestInfo)){
		echo "<script type='text/javascript'> \n";
		echo "alert(\"Sorry but the page cannot be displayed! Please try again!\");
				window.parent.location.href=window.parent.location.href;
				window.parent.pSearchClose();";				
		echo "</script> <br> \n";
		exit();
}

extract($requestInfo);
$patient_fullname=ucwords(strtolower($name_last)).', '.ucwords(strtolower($name_first))." ".ucwords(strtolower($name_middle));
/*
if (!empty($requestInfo['name_middle'])){
	$patient_fullname .= ' <font style="font-style:italic; color:#FF0000">'.ucwords(strtolower($requestInfo['name_middle'])).'</font>';
}
*/
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
 $smarty->assign('sToolbarTitle',"Laboratory :: Inventory");

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

 $smarty->assign('sWindowTitle',"Laboratory :: Inventory");

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

<script type="text/javascript" src="js/lab-process-reagents.js?t=<?=time()?>"></script>

<?php 
 $xajax->printJavascript($root_path.'classes/xajax-0.2.5');

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->append('JavaScript',$sTemp);

 $smarty->assign('sPanelHeaderSchedule','Update Inventory');
 $smarty->assign('sBatchNr','<span id="batchDisplay">'.$refno.'</span>&nbsp;'."\n".
									'<input type="hidden" name="refno" id="refno" value="'.($refno? $refno:"0").'">'."\n");
 
 $smarty->assign('sServiceName','<span id="service_name">'.$service_name.'</span>&nbsp;'."\n");
 $smarty->assign('sServiceCode','<input type="hidden" name="service_code" id="service_code" value="'.$service_code.'"><span id="service_code">'.$service_code.'</span>&nbsp;'."\n");
 $smarty->assign('sAreaName','<input type="hidden" id="dept_nr" name="dept_nr" value="'.$dept_nr.'"><input type="hidden" id="area_code" name="area_code" value="'.$area_code.'"><span id="area_name">'.$area_name.'</span>&nbsp;'."\n");
 $smarty->assign('sPatientName','<input class="segInput" id="p_name" name="p_name" type="text" size="40" value="'.ucwords(strtolower($patient_fullname)).'" style="font:bold 12px Arial;" readonly>');

 #added by VAN 07-08-08

 $rs = $srvObj->getReagents($service_code); 
 $reagentcnt = $srvObj->count;
 $i=1;
 if($reagentcnt){ 
 while($row=$rs->FetchRow()){
 
 	if ($row['is_unitperpc'])
		$checked = "checked";
	else	
		$checked = "";
		
 	$sReagents .= '
					<tr>
						<td style="font-weight:bold"><input type="hidden" id="reagent_code'.$i.'" name="reagent_code'.$i.'" value="'.$row['reagent_code'].'">'.mb_strtoupper($row['reagent_name']).'</td>
						<td style="font-weight:bold"><input type="text" id="amount'.$i.'" size="5" name="amount'.$i.'" value="'.$rowres['item_qty'].'"></td>
						<td width="2%"><input type="text" id="unit'.$i.'" name="unit'.$i.'" size=5 value="'.$row['unit_id'].'" /></td>
						<td width="2%"><input type="checkbox" id="perpc'.$i.'" name="perpc'.$i.'" value="'.$row['is_unitperpc'].'" '.$checked.' /></td>
					</tr>';
	$i++;				
 }
 }else{
     $sReagents .= '
                    <tr>
                        <td style="font-weight:bold" colspan="4">No reagents used in this examination.</td>
                        
                    </tr>';     
 }
 $smarty->assign('sReagentUsed',$sReagents);

 #--------------------------
	

$smarty->assign('sScheduleButton','<img '.createLDImgSrc($root_path,'savedisc.gif','0').'" alt="Saves this schedule" onClick="saveProcessReagents(\'save\')" style="cursor:pointer"></a>');	

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

<input type="hidden" name="reagentcnt" id="reagentcnt" value="<?=$reagentcnt?>" />

<?php

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);

 $smarty->assign('sFormAction','action="'.$thisfile.'"');

$smarty->assign('sMainBlockIncludeFile','laboratory/schedule_form_processframe.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
