<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/laboratory/ajax/lab-admin.common.php");
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();


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

 $smarty->assign('sOnLoadJs','onLoad=""');	

 $grpcode = $_GET['id'];
 $mode = $_GET['mode'];
 #echo "mode = ".$mode;
 $labgroupInfo = $srvObj->getAllLabGroupInfo($grpcode);	
 #print_r($labgroupInfo);	
 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
function saveGroup(mode){
	//alert('id, name, other, mode = '+$('gcode').value+" , "+$('gname').value+" , "+$('goname').value+" , "+mode);
	xajax_saveLabGroup($('gcode').value, $('gname').value, $('goname').value, mode);
	//alert(window.location.href);
	window.location.href=window.location.href;
}
// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
	<div style="background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden;">
	<table border="0" width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">
		<tbody>
			<tr>
				<td>Code</td>
				<td><input type="text" name="gcode" id="gcode" size="5" value="<?= $labgroupInfo['group_code'];?>" <?= ($grpcode)?'readonly':''?>></td>
			</tr>
			<tr>
				<td>Name</td>
				<td><input type="text" name="gname" id="gname" size="25" value="<?= $labgroupInfo['name'];?>"></td>
			</tr>
			<tr>
				<td>Other Name</td>
				<td><input type="text" name="goname" id="goname" size="25" value="<?= $labgroupInfo['other_name'];?>"></td>
			</tr>
			<tr>
				<td colspan="2">
					<?php if ($mode=='save'){?>
						<img id="save" name="save" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="Save" title="Save" onClick="saveGroup('save');">
					<?php }elseif ($mode=='update'){ ?>
						<img id="update" name="update" src="../../gui/img/control/default/en/en_update" border=0 alt="Update" title="Update" onClick="saveGroup('update');">
					<?php } ?>
					&nbsp;<img id="save" name="save" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" onClick="javascript:window.parent.cClick();">
				</td>
				<!--
				<td><img id="save" name="save" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" title="Cancel" onClick="javascript:window.parent.cClick();"></td>
				-->
			</tr>
		</tbody>
	</table>
	</div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">

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
