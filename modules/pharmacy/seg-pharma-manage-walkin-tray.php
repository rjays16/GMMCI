<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path."modules/pharmacy/ajax/pharma-walkin.common.php");
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
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/pharma-walkin.js?t=<?=time()?>"></script>
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
<div style="width:98%; padding:5px 0px">
	<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="border:1px solid #888888">
<?
	if($_GET['target']=='add') {
?>
		<tbody>
			<tr>
				<td class="segPanel" width="25">PID</td>
				<td class="segPanel" width="30">
					<input class="segInput" type="text" size="20" id="new_walkin_pid" disabled=""/>
					<input class="segButton" type="button" value="Get PID" onclick="getPID(); return false;"/>
				</td>
			</tr>
			<tr>
				<td class="segPanel">Last Name</td>
				<td class="segPanel">
					<input class="segInput" type="text" size="30" id="new_walkin_lastname"/>
				</td>
			</tr>
			<tr>
				<td class="segPanel">First Name</td>
				<td class="segPanel">
					<input class="segInput" type="text" size="30" id="new_walkin_firstname"/>
				</td>
			</tr>
			<tr>
				<td class="segPanel">Gender</td>
				<td class="segPanel">
					<input class="segInput" type="radio" name="new_walkin_sex" id="new_walkin_sex_m" value="M">
					<label class="segInput" for="new_walkin_sex_m">Male</label>
					<input class="segInput" type="radio" name="new_walkin_sex" id="new_walkin_sex_f" value="F">
					<label class="segInput" for="new_walkin_sex_f">Female</label>
				</td>
			</tr>
			<tr>
				<td class="segPanel">Address</td>
				<td class="segPanel">
					<input class="segInput" type="text" size="30" id="new_walkin_address"/>
				</td>
			</tr>
			<tr>
				<td class="segPanel"width="10%" nowrap="nowrap" align="left">Birthdate</td>
				<td class="segPanel" valign="middle">
					<input class="segInput" type="text" size="15" id="new_walkin_birthdate"></input>
					<img class="link" src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="bdate_trigger"/>
					<span style="font:normal 10px Arial">[YYYY-mm-dd]</span>
					<script type="text/javascript">
						Calendar.setup (
						{
								inputField : "new_walkin_birthdate",
								ifFormat : "%Y-%m-%d",
								showsTime : false,
								button : "bdate_trigger",
								singleClick : true,
								step : 1
						}
						);
						</script>
				</td>
			</tr>
			<tr>
				<td>
					<img class="link" id="add" name="add" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="add data" align="absmiddle"  onclick="startAJAXAdd(); return false;" /><a href ="javascript:window.parent.cClick();"><img class="segSimulatedLink" id="cancel" name="cancel" src="../../gui/img/control/default/en/en_close2.gif" border=0 alt="cancel" align="absmiddle"/></a>
				</td>
			</tr>
		</tbody>
<?
	}
	else if($_GET['target']=='edit')
	{
		global $db;
		$sql="SELECT name_last,name_first,address,date_birth,sex FROM seg_walkin WHERE pid=".$db->qstr($_GET['editID']);
		$row=$db->GetRow($sql);
		#$row=$result->FetchRow();
?>

		<tbody>
			<tr>
				<td class="segPanel" width="25">PID</td>
				<td class="segPanel" width="30"><input type="text" size="30" id="new_walkin_pid" value="<?= $_GET['editID'] ?>" disabled=""/></td>
			</tr>
			<tr>
				<td class="segPanel">Last Name</td>
				<td class="segPanel">
					<input class="segInput" type="text" size="30" id="new_walkin_lastname" value="<?= $row['name_last'] ?>"/>
				</td>
			</tr>
			<tr>
				<td class="segPanel">First Name</td>
				<td class="segPanel">
					<input class="segInput" type="text" size="30" id="new_walkin_firstname" value="<?= $row['name_first'] ?>"/>
				</td>
			</tr>
			<tr>
				<td class="segPanel">Gender</td>
				<td class="segPanel">
<?php
					if($row['sex']=='M') {
?>
					<input class="segInput" type="radio" name="new_walkin_sex" id="new_walkin_sex_m" value="M" checked="">
					<label class="segInput" for="new_walkin_sex_m">Male</label>
					<input class="segInput" type="radio" name="new_walkin_sex" id="new_walkin_sex_f" value="F">
					<label class="segInput" for="new_walkin_sex_f">Female</label>
<?php
					}
					else if($row['sex']=='F') {
?>
					<input class="segInput" type="radio" name="new_walkin_sex" id="new_walkin_sex_m" value="M">
					<label class="segInput" for="new_walkin_sex_m">Male</label>
					<input class="segInput" type="radio" name="new_walkin_sex" id="new_walkin_sex_f" value="F" checked="">
					<label class="segInput" for="new_walkin_sex_f">Female</label>
<?php
					}
?>
				</td>
			</tr>
			<tr>
				<td class="segPanel">Address</td>
				<td class="segPanel"><input type="text" size="30" id="new_walkin_address" value="<?= $row['address'] ?>"/></td>
			</tr>
			<tr>
				<td class="segPanel">Birthdate</td>
				<td class="segPanel"><input type="text" size="15" id="new_walkin_birthdate" value="<?= $row['date_birth'] ?>"/>
						<img class="link" src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="bdate_trigger" />
						<span style="font:normal 10px Arial">[YYYY-mm-dd]</span>
						<script type="text/javascript">
							Calendar.setup (
							{
								inputField : "new_walkin_birthdate",
								ifFormat : "%Y-%m-%d",
								showsTime : false,
								button : "bdate_trigger",
								singleClick : true,
								step : 1
							}
							);
							</script>
				</td>
			</tr>
			<tr>
				<td>
					<img class="link" id="add" name="add" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="add data" align="absmiddle"  onclick="startAJAXEdit('<?echo$_GET['editID']?>'); return false;" /><a href ="javascript:window.parent.cClick();"><img class="segSimulatedLink" id="cancel" name="cancel" src="../../gui/img/control/default/en/en_close2.gif" border=0 alt="cancel" align="absmiddle"/></a>
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