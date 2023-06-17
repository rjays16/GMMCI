<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
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
define('LANG_FILE','place.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

//note: subject to change.. 
//$breakfile='startframe.php'.URL_APPEND;
$breakfile=$root_path."main/spediens.php".URL_APPEND;
$thisfile=basename(__FILE__);
$local_user='aufnahme_user';

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

# Toolbar title

 $smarty->assign('sToolbarTitle',$segAddressMenu);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDSpexFunctions')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$segAddressMenu);

 # Prepare the submenu icons

 $aSubMenuIcon=array(createComIcon($root_path,'adrsbook_regn.gif','0'), //icon changed from notepad.gif, 10-27-2007, fdp
						   createComIcon($root_path,'adrsbook_prov.gif','0'), //icon changed from dollarsign.gif, 10-27-2007, fdp
						   createComIcon($root_path,'adrsbook_mcity.gif','0'), //icon changed from man-gr.gif, 10-27-2007, fdp
						   createComIcon($root_path,'adrsbook_brgy.gif','0') //icon changed, 10-27-2007, fdp
/*---------no longer needed, conferred with BKC, 10-26-2007, fdp-----------						   
						   ,createComIcon($root_path,'man-gr.gif','0')
------------------------------until here only----------fdp---------------*/
							);

# Prepare the submenu item descriptions

$aSubMenuText=array($segRegionMngrTxt,$segProvinceMngrTxt,$segMuniCityMngrTxt,$segBryMngrTxt);	//---restored, 10-26-2007, fdp---
//$aSubMenuText=array($segRegionMngrTxt,$segProvinceMngrTxt,$segMuniCityMngrTxt,$segBryMngrTxt,$LDAddress);

# Prepare the submenu item links indexed by their template tags

$aSubMenuItem=array('segRegionMngr' => '<a href="'.$root_path.'modules/address/region/region_manage.php'.URL_APPEND.'">'.$segRegionMngr.'</a>',
					'segProvinceMngr' => '<a href="'.$root_path.'modules/address/province/province_manage.php'.URL_APPEND.'">'.$segProvinceMngr.'</a>',
					'segMuniCtyMngr' => '<a href="'.$root_path.'modules/address/municity/municity_manage.php'.URL_APPEND.'">'.$segMuniCtyMngr.'</a>',
					'segBrgyMngr' => '<a href="'.$root_path.'modules/address/brgy/brgy_manage.php'.URL_APPEND.'">'.$segBrgyMngr.'</a>'
/*-----no longer needed, conferred with BKC, 10-26-2007, fdp----------
					, 'segAddress' => '<a href="'.$root_path.'modules/address/address_new.php'.URL_APPEND.'">'.$LDAddress.'</a>'
----------------------until here only-------------------fdp----------*/					
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

# Create conditional submenu items

if(($cfg['bname']=="msie")&&($cfg['bversion']>4)){
	$sTemp='';
	ob_start();
		if($cfg['icons'] != 'no_icon') $smarty2->assign('sIconImg','<img '.createComIcon($root_path,'uhr.gif','0').'>');
		$smarty2->assign('sSubMenuItem','<a href="'.$root_path.'modules/tools/clock.php?sid='.$sid.'&lang='.$lang.'">'.$LDClock.'</a>');
		$smarty2->assign('sSubMenuText',$LDDigitalClock);
		$smarty2->display('common/submenu_row.tpl');
 		$sTemp = ob_get_contents();
 	ob_end_clean();

	$smarty->assign('LDClock',$sTemp);
	$smarty->assign('bShowClock',TRUE);
}

# Assign the submenu to the mainframe center block

 $smarty->assign('sMainBlockIncludeFile','common/submenu_address.tpl');

 /**
 * show Template
 */
 
 $smarty->display('common/mainframe.tpl');
 // $smarty->display('debug.tpl');
 ?>