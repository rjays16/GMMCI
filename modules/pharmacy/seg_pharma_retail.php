<?php
error_reporting(E_COMPLIE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

//define language file
define('LANG_FILE','products.php');
$local_user = 'ck_prod_db_user';

//include Segpharma
require_once($root_path.'include/care_apu_classes/class_pharma_transaction.php');
$pharma_obj = new SegPharma;

$GLOBAL_CONFIG = array();
$new_date_ok = 0;

//Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_function.php');

$glob_obj = new GLOBALConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format = $GLOBAL_CONFIG['data_format'];
$date_format = $GLOBAL_CONFIG['date_format'];

//set page title
$title = $LDPharmacy;
//TODO : not determine yet what file to a breakfile
$breakfile = '';
$imgpath = $root_path.'pharma/img';
$thisfile = 'seg_pharma_retail.php';

//Save data routine
// lay code here

//Start Smarty templating here
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

//Title in the title bar
$smarty->assign('sToolbarTitle', "$title :: New Purchase");
 
//href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php', 'input', '$mode', '$cat')");

//href for the help button
$smarty->assign('breakfile', $breakfile);

//Window bar title
$smarty->assign('sWindowTitle',"$title :: New Purchase");

//Assign Body.. Onload javascript code
$onLoadJS = 'onLoad="' ;
if($mode != ;'save'&&$mode != 'update')
	$onLoadJS .='document.inputform.bestellnum.focus();';
	
if($saveok) $onLoadJS .='';
$onLoadJS .='"';
$smarty->assign('sOnLoadJs', $onLoadJS);

//Collect javascript code
ob_start();
	
	#Load the javascript code
	require($root_path.'include/inc_js_retail.php');
	echo '<link rel="stylesheet" type="text/css" media="all" href = "'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'modules/pharmacy/js/retail-new-gui-functions.js"></script>'."\r\n";
	$xajax->printJavascript($root_path.'classes/xajax');
	$sTemp = ob_get_contents();
ob_end_clean();
$sTemp.="
<script type='text/javascript'>
	var init=false;
	var refno='$refno';
</script>";
$smarty->append('JavaScript',$sTemp);	








?>