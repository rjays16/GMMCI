<?php
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

#laboratory test manager
#revised by cha, june 19, 2010
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_lab_user';
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');

require_once($root_path . 'modules/laboratory/ajax/lab-manual-result-common.php');

$smarty = new Smarty_Care('common');
$smarty->assign('sToolbarTitle',"$LDLab :: Tests Manager");
$smarty->assign('bHideTitleBar',true);
$smarty->assign('bHideCopyright',true);

$css_and_js = array(
										'<link rel="stylesheet" href="'.$root_path.'modules/laboratory/test_manager/test_manager.css" type="text/css" />'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
										,'<script>var J = jQuery.noConflict();</script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.pack.js"></script>'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.css" />'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/laboratory/js/lab-manual-result.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/listgen/listgen.js"></script>'
										,'<link rel="stylesheet" href="'.$root_path.'js/listgen/css/default/default.css" type="text/css"/>'
										,$xajax->printJavascript($root_path.'classes/xajax_0.5'));
$smarty->assign('css_and_js', $css_and_js);
$breakfile=$root_path."modules/laboratory/labor.php";


$smarty->assign('formstart', '<form name="testmgr_form" method="POST" ');
$smarty->assign('formend', '</form>');

$sections = "<option value='0'>-Select a section-</option>";
$result = $db->Execute("SELECT form_id, 
								name 
							FROM seg_lab_result_forms 
							WHERE status <> 'deleted' 
							ORDER BY name ASC");
while($row=$result->FetchRow())
{
	$sections.="<option value='".$row["form_id"]."'>".$row["name"]."</option>";
}
$smarty->assign('GroupFromSearch', '<select id="Group_Form_search" name="Group_Form_search" class="segInput" onchange="search(this.id); return false;">'.$sections.'</select>');

$smarty->assign('is_submitted', '<input type="hidden" name="is_submitted" value="TRUE" />');
$smarty->assign('toolsBtn', '<button class="segButton" id="tools_btn" onclick="return false;"><img src="'.$root_path.'gui/img/common/default/wrench.png"/>Tools</button>');
ob_start();
?>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="key" id="key">
<input type="hidden" name="pagekey" id="pagekey">
<?
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('sMainBlockIncludeFile','laboratory/lab-manual-result.tpl'); //Assign the new_package template to the frameset
$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame

?>
<script>
J().ready(function() {
	J('#lab_test').tabs({
		onShow: function(anchor, divShow, divHide) {
			//alert(divShow.id);
		}
	});
});
document.observe('dom:loaded', initialize);
</script>
