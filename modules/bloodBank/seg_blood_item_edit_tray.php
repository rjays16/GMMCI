<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/bloodBank/ajax/blood-donor-register.common.php");
require($root_path.'include/inc_environment_global.php');
$xajax->printJavascript($root_path.'classes/xajax_0.5'); 
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
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 //$smarty->assign('sOnLoadJs','onLoad="preSet('.$_GET['donorID'].');"');   
 # Collect javascript code
 ob_start(); 
?>
<script type="text/javascript" src="<?=$root_path?>modules/laboratory/js/request-tray-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
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

function refreshFrame(outputResponse)
{
		alert(""+outputResponse);
		window.parent.location.reload();
}

function updateBloodItem(donorID, itemID)
{
			xajax_updateBloodItem(donorID, itemID, document.getElementById('donate_qty').value, document.getElementById('donate_unit').value);
}
</script> 
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform"> 
<div style="width:100%">   
				 <div style="padding:10px;width:95%;border:0px solid black">
				 <font class="warnprompt"></font>
				 <table border="0" width="100%">
				 <tbody class="submenu">
						<tr>
								<td class="segPanelHeader" align="right" width="140"><b>Quantity:</b></td>
								<td class="segPanel" width="70%"><input type="text" size="10" id="donate_qty" 
										value="<?
												global $db;
												$sql = "select qty from seg_donor_transaction where donor_id='".$_GET['donorID']."' and item_id='".$_GET['itemID']."' and status not in ('deleted')";
												$result = $db->Execute($sql);
												$row = $result->FetchRow();
												echo $row['qty'];
										?>">
										</input></td>
						</tr>
						<tr>
								<td class="segPanelHeader" align="right" width="140"><b>Unit:</b></td>
								<td class="segPanel" width="70%">
										<select name="donate_unit" id="donate_unit">
										<?php
												global $db;
												$sql1 = "select unit from seg_donor_transaction where donor_id='".$_GET['donorID']."' and item_id='".$_GET['itemID']."' and status not in ('deleted')";
												$result1 = $db->Execute($sql1);
												$row1 = $result1->FetchRow();
												$sql2="select unit_name from seg_unit";
												$result2 = $db->Execute($sql2);
												$options_unit=""; 
												while($row2=$result2->FetchRow())
												{
														if($row1['unit']==$row2['unit_name'])
														{
															 $options_unit.='<option value="'.$row2['unit_name'].'" selected="">'.$row2['unit_name'].'</option>\n';
														}
														else
														{
															 $options_unit.='<option value="'.$row2['unit_name'].'">'.$row2['unit_name'].'</option>\n';
														}            
												}
												echo $options_unit;
										?>
										</select>
									</td>
						</tr>        
				</tbody>
				</table>
				<table>
					<tr>
						<td><input id="addBlood" name="addBlood" type="image" src="../../gui/img/control/default/en/en_update.gif" border=0 width="72" height="23"  alt="addBlood" align="absmiddle" onclick="updateBloodItem('<?echo $_GET['donorID'];?>','<?echo $_GET['itemID'];?>'); return false;"/> <a href ="javascript:window.parent.cClick();"><input id="cancel" name="cance" type="image" src="../../gui/img/control/default/en/en_cancel.gif" border=0 width="72" height="23"  alt="cancel" align="absmiddle"/></a></td>
					</tr>  
				</table>
				<br>
</div>  
</div>
</form>
	
		<input type="hidden" name="sid" value="<?php echo $sid?>">
		<input type="hidden" name="lang" value="<?php echo $lang?>">
		<input type="hidden" name="cat" value="<?php echo $cat?>">
		<input type="hidden" id="userck" name="userck" value="<?php echo $userck ?>">
		<input type="hidden" name="mode" value="search">


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
