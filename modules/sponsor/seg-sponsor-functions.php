<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
/*** CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','radio.php');
define('NO_CHAIN',1);
require_once $root_path.'include/inc_front_chain_lang.php';
require_once $root_path.'include/inc_2level_reset.php';

$thisfile=basename(__FILE__);
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
$_SESSION['sess_path_referer']=$top_dir.$thisfile;

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Added for the common header top block
$smarty->assign('sToolbarTitle',"PIAD");

# Added for the common header top block
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDRadio')");

$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('title',"PIAD");

# Collect javascript
//ob_start();
//$sTemp = ob_get_contents();
//ob_end_clean();

# Append javascript to JavaScript block

$smarty->append('JavaScript',$sTemp);

$aMenu=array(

	"Lingap"=>array(
/*    'sNewLingapGrant' => array(
				'href'=>$root_path.'modules/sponsor/seg-sponsor-lingap.php'. URL_APPEND."&target=edit&userck=$userck\"",
				'label'=>"New Lingap entry",
				'description'=>"Enter a new Lingap entry (OPD)",
				'icon'=>createComIcon($root_path,'user_add.png','0') ),
*/
		'sManageLingapWalkin' => array(
				'href'=>$root_path.'modules/sponsor/seg_sponsor_lingap_walkin.php'. URL_APPEND."&userck=$userck",
				'label'=>"Lingap (Murang Gamot)",
				'description'=>"Grant requests for walk-in MG patients",
				'icon'=>createComIcon($root_path,'pill_go.png','0') ),

		'sManageLingapAccount' => array(
				'href'=>$root_path.'modules/sponsor/seg_sponsor_lingap_patient.php'. URL_APPEND."&userck=$userck",
				'label'=>"Lingap: Requests",
				'description'=>"Grant requests from cost centers",
				'icon'=>createComIcon($root_path,'user_go.png','0') ),

		'sManageLingapBilling' => array(
				'href'=>$root_path.'modules/sponsor/seg_sponsor_lingap_billing.php'. URL_APPEND."&userck=$userck",
				'label'=>"Lingap: Hospital Bill",
				'description'=>"Grant for processed hospital bills",
				'icon'=>createComIcon($root_path,'folder_user.png','0') ),

		'sManageLingapEntries' => array(
				'href'=>$root_path.'modules/sponsor/seg_sponsor_lingap_list.php'. URL_APPEND."&userck=$userck",
				'label'=>"List of Lingap referrals",
				'description'=>"List of recently encoded Lingap referrals",
				'icon'=>createComIcon($root_path,'table.png','0') ),

		'sLingapReports' => array(
				'href'=>$root_path.'modules/sponsor/seg-lingap-reports.php'. URL_APPEND."&target=reports&userck=$userck",
				'label'=>"Reports",
				'description'=>"Generate Lingap related reports",
				'icon'=>createComIcon($root_path,'report.png','0') )
	),

	"CMAP" => array(
		'sManagePatientAccount' => array(
				'href'=>$root_path.'modules/sponsor/seg_sponsor_cmap_patient.php'. URL_APPEND."&userck=$userck",
				'label'=>"CMAP entry",
				'description'=>"Main entry for CMAP. Manages grants and referrals",
				'icon'=>createComIcon($root_path,'user_go.png','0') ),
//		'sLingapAdjustments' => array(
//				'href'=>$root_path.'modules/sponsor/seg_sponsor_cmap_adjustment.php'. URL_APPEND."&userck=$userck\"",
//				'label'=>"Adjustments",
//				'description'=>"Entry for adjustments in CMAP patient accounts",
//				'icon'=>createComIcon($root_path,'note_edit.png','0') ),
		'sCMAPManager' => array(
				'href'=>$root_path.'modules/sponsor/seg_sponsor_cmap_accounts.php'. URL_APPEND."&userck=$userck",
				'label'=>"CMAP Accounts",
				'description'=>"Manages CMAP accounts and allotments",
				'icon'=>createComIcon($root_path,'group_key.png','0') ),

		'sManageCmapEntries' => array(
				'href'=>$root_path.'modules/sponsor/seg_sponsor_cmap_list.php'. URL_APPEND."&userck=$userck",
				'label'=>"List of CMAP referrals",
				'description'=>"List of recently encoded CMAP referrals",
				'icon'=>createComIcon($root_path,'table.png','0') ),

		/*'sManageCmapWalkin' => array(
				'href'=>$root_path.'modules/sponsor/seg_sponsor_cmap_walkin.php'. URL_APPEND."&userck=$userck",
				'label'=>"List of CMAP walkin",
				'description'=>"Manage CMAP walkin patients",
				'icon'=>createComIcon($root_path,'user_green.png','0') ),*/

		'sCMAPReports' => array(
				'href'=>$root_path.'modules/sponsor/seg-cmap-reports.php'. URL_APPEND."&target=reports&userck=$userck",
				'label'=>"Reports",
				'description'=>"Generate CMAP related reports",
				'icon'=>createComIcon($root_path,'report.png','0') )
	)
);

$smarty->assign('aMenu', $aMenu);

# Assign the submenu to the mainframe center block
 $smarty->assign('sMainBlockIncludeFile','common/basemenu.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
