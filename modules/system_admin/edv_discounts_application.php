<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/system_admin/ajax/discount_application.common.php");
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('system_admin');
 
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE); 

$smarty->assign('sOnLoadJs','onLoad="js_getBillAreas(\''.$_GET['id'].'\',\''.$_GET['obj1'].'\');"');

ob_start();
?>
<!-- prototype -->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>/js/shortcut.js"></script>
<script type="text/javascript" src="js/discount_application.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

ob_start();
?>
<br>
<table id="bill_areas_list" class="segList" cellpadding="1" cellspacing="1" width="94%">
	<thead>
		<tr>
			<th width="80%">Bill Area</th>
			<th width="*">Check to Apply</th>
		</tr>
	</thead>
	<tbody id="bill_areas_list-body">
	</tbody>	
</table>
<br>
<table width="94%" id="bill_areas_list-footer" name="bill_areas_list-footer" cellpadding="1" cellspacing="1">	
	<tr>
		<td width="75%">&nbsp;</td>
		<td align="center" width="*"><img src="<?= $root_path ?>images/btn_submitorder" align="center" onclick="js_submitOnClick('<?= $_GET['obj1'] ?>','<?= $_GET['obj2'] ?>','<?= $_GET['obj3'] ?>');" style="cursor:pointer" /></td>
		<td align="left" width="*"><img id="btnCancel" name="btnCancel" style="cursor:pointer" <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border=0 onclick="js_CancelApply();" ></td>
	</tr>	
</table>
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
