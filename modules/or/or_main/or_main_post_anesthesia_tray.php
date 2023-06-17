<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/or/ajax/op-request-new.common.php");
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title=$LDLab;
$breakfile=$root_path."modules/laboratory/seg-close-window.php".URL_APPEND."&userck=$userck";
#$imgpath=$root_path."pharma/img/";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDLabDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDLabDb $LDSearch");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="preSet();"');

 # Collect javascript code
 ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/or-anesthesia-tray.js?t=<?=time()?>"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script language="javascript" >
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();
#added code by angelo m. 09.03.2010
#start here
$ops_obj=new SegOps;
global $db;
$sql="select SQL_CALC_FOUND_ROWS id, name from care_type_anaesthesia where status <> 'deleted'";
$result=$db->Execute($sql);
$strAnesthesiaItems="";
while($row=$result->FetchRow()){
		$strAnesthesiaItems=$strAnesthesiaItems.'<option value="'.$row['id'].'">'.$row['name'].'</option>';
}
#end here

?>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform">
		<div style="padding:10px;width:95%;border:0px solid black">
		<font class="warnprompt"><br></font>
		<table border="0" width="80%">
		 <tbody>
				 <tr>
					 <td align="right" width="20%" class="segPanelHeader">Category</td>
					 <td class="segPanel">
						 <select name="anaesthesia_list" id="anaesthesia_list" onchange="populate_anaesthesia_type()">
						 <option>-Select Anaesthesia Type-</option>
						 <?php echo $strAnesthesiaItems; ?>
						 </select>
					 </td>
				 </tr>
				 <tr id="sub_anaesthesia" style="display:none" class="segPanelHeader">
					 <td align="right">Specific</td>
					 <td class="segPanel"><select name="sub_anaesthesia_list" id="sub_anaesthesia_list"></select></td>
				 </tr>
				 <tr>
					 <td align="right" class="segPanelHeader">Time Begun</td>
					 <td class="segPanel"><input type="text" id="time_begun" onblur="validate_time(this.id);"  onchange="setFormatTime(this,'ts_meridian');" size="12"/><select name="ts_meridian" id="ts_meridian">
							<option value="am">AM</option>
							<option value="pm">PM</option>
						</select></td>
				 </tr>
				 <tr>
					 <td align="right" class="segPanelHeader">Time Ended</td>
					 <td class="segPanel"><input type="text" id="time_ended" onblur="validate_time(this.id);" onchange="setFormatTime(this,'tf_meridian');" size="12"/><select name="tf_meridian" id="tf_meridian">
							<option value="am">AM</option>
							<option value="pm">PM</option>
						</select></td>
				 </tr>
				 <tr>
					<!--<td align="right" class="segPanelHeader" width="100"><input type="button" id="add_anesthesia" onclick="show_anesthesia_table();" value="+ Add"/></td>-->
					 <td>
						 <img class="segSimulatedLink" id="add" name="add" src="../../../images/btn_add.gif" border=0 alt="add data" align="absmiddle"  onclick="show_anesthesia_table(); return false;" />
					 </td>
				 </tr>
			 </tbody>
		</table>
		<br/>
		<div id="add_anesthetics_div" style="">
		 <table class="segList" width="80%" id="or_anesthesia_table" align="center">
			<thead id="or_anesthesia_table-head">
				<tr>
					<th align="center" width="5%"></th>
					<th align="center" width="20%">Anaesthesia</th>
					<th align="center" width="30%">Sub-Anaesthesia</th>
					<th align="center" width="10%">Time Begun</th>
					<th align="center" width="10%">Time Ended</th>
					<th align="center" width="20%">Anesthetics</th>
					<th align="center" width="15%"></th>
				</tr>
			</thead>
			<tbody id="or_anesthesia_table-body">
			<tr id="empty_anesthesia_row" style=""><td colspan="7">No anaesthesia procedure added...</td></tr>
			</tbody>
		</table>
		</div>
		<div id="submit_or_anesthesia_div" style="">
		<table>
			<tbody>
				<tr>
					<td width="71%"></td>
					<td><img class='segSimulatedLink' id='clear_items' name='clear_items' src='../../../images/btn_emptylist.gif' border=0 alt='clear items' align='absmiddle' onclick='empty_anesthesia_list();'/></td>
					<td><img class='segSimulatedLink' id='add_items' name='add_items' src='../../../images/btn_submit.gif' border=0 alt='add items' align='absmiddle' onclick='prepare_anesthesia_list();'/></td>
				</tr>
			</tbody>
		</table>
		</div>
		<div id="anesthetics_list" style="display:none">
		</div>
		<input type="hidden" name="is_added" id="is_added" value=""/>
		<input type="hidden" name="anesthesia_count" id="anesthesia_count"/>
</div>
</form>

		<input type="hidden" name="sid" value="<?php echo $sid?>">
		<input type="hidden" name="lang" value="<?php echo $lang?>">
		<input type="hidden" name="cat" value="<?php echo $cat?>">
		<input type="hidden" id="userck" name="userck" value="<?php echo $userck ?>">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="key" id="key">
		<input type="hidden" name="pagekey" id="pagekey">


<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
		/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
		include_once($root_path.'gui/smarty_template/smarty_care.class.php');
		$smarty = new smarty_care('common',FALSE,FALSE,FALSE);

		# Set a flag to display this page as standalone
		$bShowThisForm=TRUE;
}

?>

<form action="<?php echo $breakfile?>" method="post">
		<input type="hidden" name="sid" value="<?php echo $sid ?>">
		<input type="hidden" name="lang" value="<?php echo $lang ?>">
		<input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>

</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);
# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
