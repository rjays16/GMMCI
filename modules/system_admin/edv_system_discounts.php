<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/system_admin/ajax/discount.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile='edv-system-admi-welcome.php'.URL_APPEND;
if ($from=='add') $returnfile='edv_system_discounts.php'.URL_APPEND.'&from=set';
  else $returnfile=$breakfile;
$thisfile='edv_system_discounts.php';
$editfile='edv_system_discounts.php'.URL_REDIRECT_APPEND.'&mode=edit&from=set&item_no=';

// Clear the session variable for tracking the bill areas where discount is applied.
unset($_SESSION['bill_areas']);

/*define('LANG_FILE','edp.php');
$local_user='ck_edv_user';
require_once($root_path.'include/inc_front_chain_lang.php');
if(isset($ck_edv_admin_user)) setcookie('ck_edvzugang_user',$ck_edv_admin_user);
$breakfile='edv.php'.URL_APPEND;
$HTTP_SESSION_VARS['sess_file_return']=basename(__FILE__);*/

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"System Admin::Discounts");

# href for return button
 $smarty->assign('pbBack',$returnfile);
 
 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('currency_set.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Discounts Admin");

 # Assign Body Onload javascript code
 $onLoadJS='onLoad="xajax_listDiscounts()"';

 $smarty->assign('sOnLoadJs',$onLoadJS);
 
 // Added by LST - 07302008
 $smarty->assign('sSetBillAreasApplied','<img id="btnSetBillableAreasApplied" style="cursor:pointer" src="'.$root_path.'/images/selection_img_big.png" border=0 
 	onclick="overlib(
        OLiframeContent(\'edv_discounts_application.php'.URL_APPEND.'&id=\'+$(\'inputID\').value+\'&obj1=billareas_id&obj2=billareas_appplied&obj3=billareas_label\', 640, 350, \'fSelBAreas\', 0, \'auto\'),
        WIDTH,640, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Select Bill Areas Where to Apply Discount\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select Bill Areas Where to Apply Discount\'); return false;"
       onmouseout="nd();" />');

# Collect javascript code
ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins: -->
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
<?php
 # Load the javascript code	
echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'js/gen_routines.js"></script>'."\r\n";
echo '<script type="text/javascript" src="'.$root_path.'modules/system_admin/js/discount-gui-functions.js"></script>'."\r\n";
$xajax->printJavascript($root_path.'classes/xajax');		
$sTemp = ob_get_contents();
ob_end_clean();
$sTemp.="
<script type='text/javascript'>
	var init=false;
	var userid='".$HTTP_SESSION_VARS['sess_login_username']."';
</script>";

$smarty->append('JavaScript',$sTemp);

ob_start();
?>
<input type="hidden" id="seg_URL_APPEND" name="seg_URL_APPEND" value="<?=URL_APPEND?>"  />
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenFields',$sTemp);

# Assign page output to the mainframe template
$smarty->assign('sMainBlockIncludeFile','system_admin/discount.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
