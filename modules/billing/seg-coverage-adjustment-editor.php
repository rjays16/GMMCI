<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/billing/ajax/seg-coverage-adjustment.common.php");

/**
* SegHIS Integrated Hospital Information System
* Billing Module
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
//$local_user='ck_prod_user';
//$local_user='aufnahme_user';
$local_user=$_GET['userck'];

require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

$title=$LDPharmacy;
if (!$_GET['from'])
	$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/pharmacy/apotheke-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$thisfile='seg-coverage-adjustment-editor.php';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

/*
if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}
*/
$title = "Adjust coverage";

# Title in the title bar
$smarty->assign('sToolbarTitle',$title);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',$title);

# Assign Body Onload javascript code
//if ($_GET['mode']) {
//  $mode = strtolower($_GET['mode']);
			//if ($mode[0]=='o') $smarty->assign('sTabOClass','segActiveTab');
			//else $smarty->assign('sTabMClass','segActiveTab');
//
//  if (strpos($mode,'m')===FALSE) $smarty->assign('sTabMClass','segDisabledTab');
//  if (strpos($mode,'o')===FALSE) $smarty->assign('sTabOClass','segDisabledTab');
//
//  $onLoadJS="onload=\"tabClick($('tab".$mode[0]."')); window.parent.overridecClick();\"";
//}
//else {
//  $mode = 'M';
//  $onLoadJS="onload=\"tabClick($('tabm')); window.parent.overridecClick();\"";
//}

$onLoadJS="onload=\"loadItems(); window.parent.overridecClick();\"";

$smarty->assign('sOnLoadJs',$onLoadJS);
#$smarty->assign('sOnUnLoadJs',"onUnload=\"javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();\"");
#$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('QuickMenu',FALSE);

# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<!-- START for setting the DATE (NOTE: should be IN this ORDER) -->
<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('sEncounterNr',$_GET['encounter_nr']);
$smarty->assign('sBillNr',$_GET['bill_nr']);
$smarty->assign('sUserCK',$_GET['userck']);
$smarty->assign('sBillDte',(($_GET['bill_dt']) ? $_GET['bill_dt'] : strtotime("now")));
$smarty->assign('sForce',$_GET['force']);
$smarty->assign('sSaveButton','<input id="save" class="segInput" type="image" src="../../gui/img/control/default/en/en_savedisc.gif" onclick="save(); return false;" disabled="disabled"/>');
/*
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&target=new&clear_ck_sid=".$clear_ck_sid.$qs.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');
*/

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="refno" id="refno" value="<?php if($_GET['encounter_nr']) echo "T".$_GET['encounter_nr']; else echo $_GET['bill_nr']; ?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update; ?>">
	<input type="hidden" name="target" value="<?php echo $target ?>">
	<input type="hidden" id="bill_nr" name="bill_nr" value="<?php echo (($_GET['bill_nr']) ? $_GET['bill_nr'] : ''); ?>">
	<input type="hidden" id="force_startdate" name="force_startdate" value="<?php echo $_GET['force'] ?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="if (validate()) document.inputform.submit()"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','billing/seg-coverage-adjustment.tpl');
$smarty->display('common/mainframe.tpl');

?>