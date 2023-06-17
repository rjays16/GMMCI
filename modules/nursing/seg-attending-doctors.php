<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
#require($root_path."modules/pharmacy/ajax/order-tray.common.php");
require($root_path."modules/nursing/ajax/seg-attending-doctors-common.php");
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';

require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);


require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

# href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype1.5.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>



<!-- YUI-2.2 Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/connection/connection.js" ></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/fonts/fonts.css">

<script type="text/javascript">
	YAHOO.namespace("ssadmin.container");
</script>
<script type="text/javascript" src="js/seg-attending-doctors.js"></script>

<script type="text/javascript">
 preset(<?=$enc?>);
</script>	

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);


# Buffer page output
 ob_start();
?>

<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%">
	<tbody>
		<tr>
			<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
				<div style="padding:4px 2px; padding-left:10px; ">
					Select Physician <select id="selDoc" name="selDoc" style="width:auto; margin-left:10px; font: bold 12px Arial" onchange="jsGetDepartment();" >
										<option>-Select a Doctor-</option>
									 </select>
									 <select id="selDept" name="selDept" style="widows:auto; margin-left:10px; font: bold 12px Arial" onchange="jsGetDoctors();">
										<option>-Select a Department-</option>
									 </select>	
					<input type="image" src="<?= $root_path ?>images/his_addbtn.gif" onclick="jsGetAttendingDoctors(); return false;" align="absmiddle" />				 	
					<!--<input id="search" class="segInput" type="text" style="width:60%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id)" />
					<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search');return false;" align="absmiddle" />-->
				
				</div>
			</td>
		</tr>
		<tr>
			<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
				<div style="padding:4px 2px; padding-left:48px;">
					Date Start <input type="text" name="dt_start" id="dt_start" style="width:12%; margin-left:10px; font: bold 12px Arial" value="<?=date("m/d/Y")?>" > &nbsp;<strong style="font-size:10px">[mm/dd/yyyy]</strong>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div  id="container" style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:265px; width:100%; background-color:#e5e5e5">
					<table id="doc-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
						<thead>
							<tr>
								<th width="10%"></th>
								<th width="">Attending Physician </th>
								<th width="20%">Date start </th>
								<th width="5%"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="6" style="font-weight:normal">No attending physician yet...</td>
							</tr>
						</tbody>
					</table>
					<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>				
				</div>
			</td>
		</tr>
	</tbody>
</table>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?php echo $enc?>">
	<input type="hidden" name="create_id" id="create_id" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']?>" >
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<!--<input type="hidden" name="mode" value="search">-->

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
<?php if ($from=="multiple")
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
?>
</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
	