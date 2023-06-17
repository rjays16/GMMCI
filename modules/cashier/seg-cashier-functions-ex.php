<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','order.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Create a helper smarty object without reinitializing the GUI
 $smarty2 = new smarty_care('common', FALSE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDPharmacy::$LDPharmaOrder");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPharmacy $LDPharmaDb')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"$LDPharmacy::$LDPharmaDb");

 # Prepare the submenu icons

 $aSubMenuIcon=array(createComIcon($root_path,'settings_tree.gif','0'),
										createComIcon($root_path,'eyeglass.gif','0'),
										createComIcon($root_path,'settings_tree.gif','0'),
										createComIcon($root_path,'settings_tree.gif','0'),
										createComIcon($root_path,'eyeglass.gif','0'),
										);

# Prepare the submenu item descriptions

$aSubMenuText=array("Process orders and requests from pharmacy, laboratory and radiology",
										"Process hospital billing accounts",
										"Process other hospital items/services",
										"Create new deposit",
										"Manage cash deposits and partial payments"
										);

# Prepare the submenu item links indexed by their template tags

$aSubMenuItem=array('LDSegCashierRequests' => '<a href="'.$root_path.'modules/cashier/seg-cashier-requests.php'. URL_APPEND."&userck=$userck".'&cat=pharma">Process requests</a>',
										'LDSegCashierBilling' => '<a href="'.$root_path.'modules/cashier/seg-pharma-order.php'. URL_APPEND."&mode=list&userck=$userck".'&cat=pharma">Billing</a>',
										'LDSegCashierOthers' => '<a href="'.$root_path.'modules/cashier/seg-cashier-other.php'. URL_APPEND."&userck=$userck".'&cat=pharma">Other services</a>',
										'LDSegCashierNewDeposit' => '<a href="'.$root_path.'modules/cashier/seg-cashier-deposit.php'. URL_APPEND."&userck=$userck".'&cat=pharma">New deposit</a>',
										'LDSegCashierManageDeposit' => '<a href="'.$root_path.'modules/cashier/seg-pharma-refund.php'. URL_APPEND."&userck=$userck".'&cat=pharma">Manage deposits</a>',
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
$smarty->assign('sSubMenuRowsIncludeFile','cashier/menu.tpl');

# Assign the subframe to the mainframe center block
$smarty->assign('sMainBlockIncludeFile','common/submenu_tableframe.tpl');

  /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
