<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
*	SegHIS (Health Insurances and Benefits Menu)
*	Created by  :	Bong (LST) Trazo
*	Date Created:	2007-08-21
*/
define('LANG_FILE','specials.php');
//$local_user='ck_prod_db_user';

define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/spediens.php'.URL_APPEND;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('insurance_co');

 # Create a helper smarty object without reinitializing the GUI
 $smarty2 = new smarty_care('insurance_co', FALSE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDSpexFunctions::$LDHealthPlans");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDHealthPlans')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"$LDSpexFunctions::$LDHealthPlans");

 # Prepare the submenu icons

 $aSubMenuIcon=array(createComIcon($root_path,'redlist.gif','0'),
										createComIcon($root_path,'templates.gif','0'),
										createComIcon($root_path,'storage.gif','0')										
										);

# Prepare the submenu item descriptions

$aSubMenuText=array("Manage available health insurances",
					"Manage available hospital benefits",
					"Manage benefits schedule of health insurances");

# Prepare the submenu item links indexed by their template tags

$aSubMenuItem=array('LDSegHealthPlan' => '<a href="'.$root_path.'modules/insurance_co/insurance_co_manage.php'. URL_APPEND."&userck=$userck".'&cat=insurance">Health Insurances</a>',
										'LDSegHealthBenefits' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-retail-manage.php'. URL_APPEND."&userck=$userck".'&cat=pharma">Hospital Benefits</a>',
										'LDSegBenefitsSchedule' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-retail-prices.php'. URL_APPEND."&userck=$userck".'&cat=pharma">Benefits Schedule of Available Health Insurances</a>'
										);

# Create the submenu rows

$iRunner = 0;

while(list($x,$v)=each($aSubMenuItem)){
	$sTemp='';
	ob_start();
	if($cfg['icons'] != 'no_icon') $smarty2->assign('sIconImg','<img '.$aSubMenuIcon[$iRunner].'>');
	$smarty2->assign('sSubMenuItem',$v);
	$smarty2->assign('sSubMenuText',$aSubMenuText[$iRunner]);
	$smarty2->display('common/submenu_row.tpl');
	$sTemp = ob_get_contents();
 	ob_end_clean();
	$iRunner++;
	$smarty->assign($x,$sTemp);
}

# Assign the submenu items table to the subframe
$smarty->assign('sSubMenuRowsIncludeFile','insurance_co/menu_insurance_co.tpl');

# Assign the subframe to the mainframe center block
$smarty->assign('sMainBlockIncludeFile','common/submenu_tableframe.tpl');

/**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>