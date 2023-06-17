<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'modules/bloodBank/ajax/blood-request-list.common.php');
require($root_path.'include/inc_environment_global.php');

$lang_tables[]='departments.php';
define('LANG_FILE','doctors.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';   # burn added: November 22, 2007

require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

require_once($root_path.'include/care_api_classes/class_blood_bank.php');
$bloodObj = new SegBloodBank();

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$lab_obj = new SegLab;

require_once($root_path.'include/care_api_classes/class_radiology.php');
$obj_radio = new SegRadio;

if ($_GET['refno'])
	$refno = $_GET['refno'];

$scheduleInfo = $bloodObj->getBloodRequestInfo($refno, $pid, $service_code);
extract($scheduleInfo);

/*$patient_fullname=ucwords(strtolower($scheduleInfo['name_last'])).', '.ucwords(strtolower($scheduleInfo['name_first']));
if (!empty($scheduleInfo['name_middle'])){
	#$patient_fullname .= ' <font style="font-style:italic; color:#FF0000">'.ucwords(strtolower($scheduleInfo['name_middle'])).'</font>';
	$patient_fullname .= " ".ucwords(strtolower($scheduleInfo['name_middle']));
}*/

	$patient_fullname = $patient_name;
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
 $smarty->assign('sToolbarTitle',"Blood Bank :: ".$sub_dept_nr_name." :: ".$patient_fullname);

 # href for help button
 $smarty->assign('pbHelp',"");
 
# href for return button
	$smarty->assign('breakfile','');
	$smarty->assign('pbBack','');

 # Body onLoad javascript
 #$onLoadJS='onLoad="preset();"';
 $smarty->assign('sOnLoadJs','');
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Window bar title
# $smarty->assign('sWindowTitle',$sTitle);
// Prints something like: Wednesday 15th of January 2003 05:51:38 AM

 $smarty->assign('sWindowTitle',"Blood Bank :: ".$sub_dept_nr_name." :: ".$patient_fullname);

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

<script type="text/javascript" src="js/blood-processform.js?t=<?=time()?>"></script>

<?php 
 $xajax->printJavascript($root_path.'classes/xajax-0.2.5');

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->append('JavaScript',$sTemp);

 $smarty->assign('sPanelHeaderSchedule','Blood Bank Dispensing Window');
 $smarty->assign('sBatchNr','<span id="batchDisplay">'.$refno.'</span>&nbsp;'."\n".
									'<input type="hidden" name="batchNo" id="batchNo" value="'.($refno? $refno:"0").'">
									 <input type="hidden" name="pid" id="pid" value="'.($pid? $pid:"0").'">'."\n");
 $smarty->assign('sServiceName','<span id="service_name">'.$service_name.'</span>&nbsp;'."\n");
 $smarty->assign('sServiceCode','<span id="service_code">'.$service_code.'</span>&nbsp;'."\n");
 $smarty->assign('sPatientName','<input class="segInput" id="p_name" name="p_name" type="text" size="40" value="'.ucwords(strtolower($patient_name)).'" style="font:bold 12px Arial;" readonly>');

#added by VAN 08-20-09
		# FORMATTING of Date Scheduled
	if (($date_served!='0000-00-00')  && ($date_served!=""))
		$date_served = @formatDate2Local($date_served,$date_format);
	else
		$date_served='';
					
	$sDateServed= '<input name="date_served" type="text" size="15" maxlength=10 value="'.$date_served.'"'. 
									'onFocus="this.select();"  
									id = "date_served" 
									onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_served_trigger" style="cursor:pointer" >
									<font size=2>['; 			
	ob_start();
?>
	<script type="text/javascript">
			Calendar.setup ({
					inputField : "date_served", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_served_trigger", singleClick : true, step : 1
			});
	</script>
<?php
	$calendarSetup = ob_get_contents();
	ob_end_clean();
				
	$sDateServed .= $calendarSetup;
						
	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$sDateServed = $sDateServed.$$dfbuffer.']';

   $smarty->assign('sDateServed',$sDateServed);
#-------------------------

 #added by VAN 07-08-08
 
# $rs = $obj_radio->getFilmSize(); 
# $filmcnt = $obj_radio->count;
# $i=1;
#  if ($rs){  
# while($row=$rs->FetchRow()){
	
	$rs_blood = $bloodObj->getBloodProductsStock($service_code);
	
    $listoffilms = '';
    
	while($row_blood=$rs_blood->FetchRow()){
	#echo "<br>".$row_blood['bestellnum'];
	    $qty = 0;
		if ($row_blood['qty'])
			$qty = $row_blood['qty'];
		
		$expiry_date = 'NONE';
		if (($row_blood['expiry_date'])&&(($row_blood['expiry_date']!='0000-00-00')||(empty($row_blood['expiry_date']))))
			$expiry_date = date("m/d/Y",strtotime($row_blood['expiry_date']));
				
		$sBloodItem.= '
					<tr>
						<td width="2%"><input type="checkbox" id="blood'.$row_blood['bestellnum'].'" name="blood'.$row_blood['bestellnum'].'" value="'.$row['id'].'" '.(($qty)?'':'disabled="disabled" ').' onClick="enableNoBlood(\''.$row_blood['bestellnum'].'\');"; /></td>
						<td style="font-weight:bold" align="left" nowrap="nowrap"><input type="hidden" id="item'.$row_blood['bestellnum'].'" size="5" name="item'.$row_blood['bestellnum'].'" value="'.$row_blood['bestellnum'].'">'.$row_blood['bestellnum'].'</td>
						<td style="font-weight:bold" align="left">'.$row_blood['artikelname'].'</td>
						<td style="font-weight:bold" align="center"><input type="hidden" id="expiry_date'.$i.'" size="5" name="expiry_date'.$i.'" value="'.$row_blood['expiry_date'].'">'.$expiry_date.'</td>
						<td style="font-weight:bold" align="center"><input type="hidden" id="qty'.$i.'" size="5" name="qty'.$i.'" value="'.$qty.'">'.$qty.'</td>
						<td style="font-weight:bold" align="center"><input type="text" id="no_blood_used'.$row_blood['bestellnum'].'" onBlur="validate_qty('.$row_blood['bestellnum'].');" onKeyPress="return keyPressHandler(event);" readonly size="5" name="no_blood_used'.$row_blood['bestellnum'].'" value="'.$rowres['no_blood_used'].'"></td>
         	</tr>';
				
	}
	
 $smarty->assign('sBloodItem',$sBloodItem);
 #--------------------------
	
	$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
							       onclick="return overlib(
										       OLiframeContent(\'seg-adjustment-tray.php?pid='.$pid.'\', 650, 370, \'fOrderTray\', 1, \'auto\'),
										       WIDTH,650, TEXTPADDING,0, BORDER,0, 
								   			 STICKY, SCROLL, CLOSECLICK, MODAL, 
								   			 CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
                                     CAPTIONPADDING,4, 
                           			 CAPTION,\'Add items from Inventory item tray\',
        										 MIDX,0, MIDY,0, 
										       STATUS,\'Add items from Inventory item tray\');"
									 onmouseout="nd();">
			 				<img name="btnitem" id="btnitem" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
	
	$smarty->assign('sOrderItemsreg',"
							<tr>
								<td colspan=\"5\">Item list is currently empty...</td>
							</tr>");
				
#if (($dept_name_short=='USD')||($dept_name_short=='CT'))
    $smarty->assign('sDoneButton','<img src="../../images/btn_save_done.gif" alt="Done the Request" onClick="doneService()" style="cursor:pointer"></a>');     

#if ($dept_name_short=='USD')
#    $smarty->assign('sScheduleButton','');    
#else
#    $smarty->assign('sScheduleButton','<img '.createLDImgSrc($root_path,'savedisc.gif','0').'" alt="Saves this schedule" onClick="saveProcessFilmed(\'save\')" style="cursor:pointer"></a>');        
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

<input type="hidden" name="key" id="key" value="<?=$_GET['key']?>">
<input type="hidden" name="pagekey" id="pagekey" value="<?=$_GET['pagekey']?>">

<?php

 $sTemp=ob_get_contents();
 ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);

 $smarty->assign('sFormAction','action="'.$thisfile.'"');

$smarty->assign('sMainBlockIncludeFile','blood/blood-form-processframe.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe2.tpl');

?>
