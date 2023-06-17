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
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

#$breakfile='apotheke.php'.URL_APPEND;
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
 $smarty->assign('sToolbarTitle',"$LDPharmacy");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPharmacy')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"$LDPharmacy::$LDPharmaDb");

 # Prepare the submenu icons

 $aSubMenuIcon=array(createComIcon($root_path,'order.gif','0'),
										createComIcon($root_path,'manage_orders.gif','0'),
										createComIcon($root_path,'disc_unrd.gif','0'),
										createComIcon($root_path,'bul_arrowgrnsm.gif','0'),

										createComIcon($root_path,'wardstock.gif','0'),
										createComIcon($root_path,'recent.gif','0'),
										createComIcon($root_path,'medicine.gif','0'),
										createComIcon($root_path,'wardlist.gif','0'),

										createComIcon($root_path,'import_address.gif','0'),
										createComIcon($root_path,'import_address_2.gif','0'),
										createComIcon($root_path,'hfolder.gif','0'),

										createComIcon($root_path,'storage.gif','0'),
										createComIcon($root_path,'newpatient.gif','0'),
										createComIcon($root_path,'chart.gif','0')
										);

# Prepare the submenu item descriptions

$aSubMenuText=array("Create new pharmacy request",
										"List of active pharmacy requests",
										"Record served pharmacy requests",
										"Set/change default pharmacy area",

										"Create new pharmacy ward stock",
										"View ward stocks for this shift",
										"Manage pharmacy ward stocks",
										"Pharmacy wards list",

										"Create new pharmacy return entry (without refund)",
										"Create new pharmacy return entry (with refund)",
										"Manage previous pharmacy return entries",

										"Manage products, product information and product prices",
										"Manage walk-in patients",
										"Generate pharmacy reports"
										);

# Prepare the submenu item links indexed by their template tags

$aSubMenuItem=array('LDSegPharmaNewOrder' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&target=ordernew">Create new request</a>',
										'LDSegPharmaOrderManage' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target=orderlist">Manage requests</a>',
										'LDSegPharmaOrderServe' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&target=servelist">Serve request</a>',
										'LDSegPharmaSetArea' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&set=1&target=">Default area</a>',

										'LDSegPharmaNewStock' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&target=newstock">New ward stock</a>',
										'LDSegPharmaRecentStocks' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&target=recentstock">Recent stocks</a>',
										'LDSegPharmaStocksManage' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&target=managestock">Manage ward stocks</a>',
										'LDSegPharmaWardManage' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target=manageward">Wards list</a>',

										//'LDSegPharmaNewReturn' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target=returnnew">New return entry</a>',
										'LDSegPharmaNewReturn' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&target=returnnew">New return entry</a>',
										//'LDSegPharmaNewRefund' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target=refundnew">New refund entry</a>',
										'LDSegPharmaNewRefund' => '<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&target=refundnew">New refund entry</a>',
										'LDSegPharmaReturnList' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target=returnlist">Manage returns/refunds</a>',

										'LDPharmaDb' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'.URL_APPEND."&userck=$userck".'&target=databank"><nobr>Product databank</nobr></a>',
										'LDPharmaWalkinManage' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'.URL_APPEND."&userck=$userck".'&target=managewalkin"><nobr>Walk-in manager</nobr></a>',
										'LDPharmaReports' => '<a href="'.$root_path.'modules/pharmacy/apotheke-pass.php'.URL_APPEND."&userck=$userck".'&target=reports"><nobr>Reports</nobr></a>'
										);

require_once($root_path.'include/care_api_classes/class_order.php');
$oc=new SegOrder();
$buf=$oc->getPharmaArea($_SESSION["sess_pharma_area"],"area_name");
$smarty->assign('sCurrentArea',$buf['area_name']);
$smarty->assign('sSetAreaLink','<a href="'.$root_path.'modules/pharmacy/seg-pharma-select-area.php'. URL_APPEND."&userck=$userck".'&set=1&target=">Default area</a>');
# Create the submenu rows

$iRunner = 0;

while(list($x,$v)=each($aSubMenuItem)){
	$sTemp='';
	ob_start();
	if($cfg['icons'] != 'no_icon') $smarty2->assign('sIconImg','<img '.$aSubMenuIcon[$iRunner].'>');
	$smarty2->assign('sSubMenuItem',$v);
	$smarty2->assign('sSubMenuText',$aSubMenuText[$iRunner]);
	$smarty2->display('common/seg_submenu_row.tpl');
	$sTemp = ob_get_contents();
	ob_end_clean();
	$iRunner++;
	$smarty->assign($x,$sTemp);
}

# Assign the subframe to the mainframe center block
$smarty->assign('sMainBlockIncludeFile','order/menu_order.tpl');

	/**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
<!-- Added by cha, 11-22-2010-->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/event.simulate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" language="javascript">
function init() {
	//go to new request
	shortcut.add('F6', keyF2,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//go to order list
	shortcut.add('F3', keyF3,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//go to serve list
	shortcut.add('F4', keyF4,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//go to default area
	shortcut.add('F10', keyF10,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
}

function keyF2() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-select-area.php?&userck=<?=$userck?>&target=ordernew";
}

function keyF3() {
		window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=orderlist";
}

function keyF4() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-select-area.php?&userck=<?=$userck?>&target=servelist";
}

function keyF10() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-select-area.php?&userck=<?=$userck?>&set=1&target=";
}

document.observe('dom:loaded', init);
</script>