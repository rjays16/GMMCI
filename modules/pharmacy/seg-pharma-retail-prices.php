<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/pharmacy/ajax/retail-pprice.common.php");
require($root_path.'include/inc_environment_global.php');
/**	
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

# The language table
define('LANG_FILE','aufnahme.php');
$local_user='ck_prod_db_user';
require($root_path.'include/inc_front_chain_lang.php');

if(empty($target)) $target='search';

switch($origin)
{
    case 'archive': $breakfile='patient_register_archive.php';
	                         break;
    case 'admit': $breakfile='patient.php';
	                         break;
    default : $breakfile='seg-pharma-retail-functions.php';
}

$breakfile.=URL_APPEND;
$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
# print_r($_SESSION);
# exit();
 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Added for the common header top block

 $smarty->assign('sToolbarTitle',"Pharmacy::Retail::Product prices");
 # Added for the common header top block

 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister." - ".$LDSearch')");
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDPatientRegister." - ".$LDSearch);
 $smarty->assign('sOnLoadJs','onLoad=""');
 $smarty->assign('pbHelp',"javascript:gethelp('person_how2search.php')");
 $smarty->assign('pbBack',FALSE);
 
 
	ob_start();
  # Load the javascript code
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'modules/pharmacy/js/retail-prices-gui-functions.js"></script>'."\r\n";
	$xajax->printJavascript($root_path.'classes/xajax');
	$sTemp = ob_get_contents();
	ob_end_clean();
	$sTemp.="
<script type='text/javascript'>
	var init=false;
	var userid='".$_SESSION['sess_temp_userid']."';
</script>";

$smarty->append('JavaScript',$sTemp);

# Assign the appending post search text
$smarty->assign('sMainBlockIncludeFile','retail/prices.tpl');

# Show mainframe

$smarty->display('common/mainframe.tpl');

?>