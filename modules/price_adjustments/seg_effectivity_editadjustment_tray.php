<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/price_adjustments/ajax/price_adjustments.common.php");
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
 #$smarty->assign('sOnLoadJs','onLoad="preSet();"');

 # Collect javascript code
 ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/seg-effect-price.js?t=<?=time()?>"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script language="javascript" >
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform">
		<div style="padding:10px;width:95%;border:0px solid black">
		<table border="0" width="100%" style="border: 1px solid rgb(136, 136, 136);" align="center" cellspacing="1" cellpadding="2">
				<?
					 if($_GET['target']=='edit')
						{
							global $db;
							$sql="select p.effectivity_date, pd.service_code, pd.price_cash, pd.price_charge, p.history from seg_hospital_service_price as p".
							" inner join seg_hospital_service_price_details as pd on p.refno=pd.refno where p.refno='".$_GET['editID']."'";
							#echo $sql;
							$result=$db->Execute($sql);
							$row=$result->FetchRow();
							?>
								 <tbody>
										<tr>
											<td class="segPanel" style="font: bold 12px Tahoma; width: 10px">Service Code</td>
											<td class="segPanel"><input class="segInput" type="text" size="30" id="service_code" value="<?
												echo $row['service_code'];
											?>" readonly=""/></td>
										</tr>
										<tr>
											<td class="segPanel" style="font: bold 12px Tahoma; width: 10px">Price Cash</td>
											<td class="segPanel"><input class="segInput"  type="text" size="30" id="new_price_cash" value="<?
												echo $row['price_cash'];
											?>" onblur="callAlert(this.value,this.id); format_number(this.value,this.id,2);"/></td>
										</tr>
										<tr>
											<td class="segPanel" style="font: bold 12px Tahoma; width: 10px">Price Charge</td>
											<td class="segPanel"><input class="segInput"  type="text" size="30" id="new_price_charge" value="<?
												echo $row['price_charge'];
											?>" onblur="callAlert(this.value,this.id); format_number(this.value,this.id,2);"/></td>
										</tr>
										<tr>
											<td class="segPanel" style="font: bold 12px Tahoma; width: 10px">Effectivity Date</td>
											<td class="segPanel"><!--<input type="text" name="new_effectivity_date" id="new_effectivity_date" onchange="checkDate(this.id);" value=""/>                    -->
											<div id="new_effectivity_date" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;displaylock;float:left"><?
												echo $row['effectivity_date'];
											?></div>
										<img src="../../gui/img/common/default/show-calendar.gif" id="new_effectivity_date_trigger" align="absmiddle" style="cursor:pointer">
									<script type="text/javascript">
									Calendar.setup (
									{    displayArea: "new_effectivity_date",
											inputField : "new_effectivity_date",
											ifFormat : "%Y-%m-%d",
											showsTime : false,
											button : "new_effectivity_date_trigger",
											singleClick : true,
											step : 1
									});
									</script></td>
									<input type="hidden" id="history" value="<?echo $row['history']?>"/>
										</tr>
											<tr>
												<td>
													<img class="segSimulatedLink" id="add" name="add" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="add data" align="absmiddle"  onclick="startAJAXEditPrice('<?echo$_GET['editID']?>'); return false;" /><a href ="javascript:window.parent.cClick();"><img class="segSimulatedLink" id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="cancel" align="absmiddle"/></a>
												</td>
											</tr>
									</tbody>
						<?
						}
				?>

		</table>
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
