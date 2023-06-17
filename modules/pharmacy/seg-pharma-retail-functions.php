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
define('LANG_FILE','retail.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile='apotheke.php'.URL_APPEND;

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
 $smarty->assign('sToolbarTitle',"$LDPharmacy::$LDPharmaRetail");

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
										createComIcon($root_path,'discussions.gif','0'),
										createComIcon($root_path,'storage.gif','0')										
										);

# Prepare the submenu item descriptions

$aSubMenuText=array("Create new purchase record",
										"Manage transactions and transaction details",
										"Manage product retail prices",
										"Manage product information"
										);

# Prepare the submenu item links indexed by their template tags

$aSubMenuItem=array('LDSegNewPurchase' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-retail-new.php'. URL_APPEND."&userck=$userck".'&cat=pharma">Create purchase</a>',
										'LDSegPharmaManageTrans' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-retail-manage.php'. URL_APPEND."&userck=$userck".'&cat=pharma">Manage transactions</a>',
										'LDSegPharmaPrices' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-retail-prices.php'. URL_APPEND."&userck=$userck".'&cat=pharma">Product prices</a>',
										'LDPharmaDb' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'.URL_APPEND."&mode=dbank"."&userck=$userck".'"><nobr>Product databank</nobr></a>'
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
$smarty->assign('sSubMenuRowsIncludeFile','retail/menu_retail.tpl');

# Assign the subframe to the mainframe center block
$smarty->assign('sMainBlockIncludeFile','common/submenu_tableframe.tpl');

  /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
