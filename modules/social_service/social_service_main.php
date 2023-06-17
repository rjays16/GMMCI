<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* Seg Hospital Information System 
* GNU General Public License
* Copyright 2007
* 
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Module title in the toolbar
 $smarty->assign('sToolbarTitle',"Social Service");

 # Hide the return button
 $smarty->assign('pbBack',FALSE);
//-------------------------

 # Help button href
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"Social Service");
 
 //NOTE: REMOVE THIS AFTER SOCIAL SERVICE PAGE IS COMPLETE
 #echo "<center><b>- THIS SITE IS UNDER CONSTRUCTION -</b></center>";
# echo "<center><b>By: MLHE :)</b></center>";

 #----- START SOCIAL SERVICE MENU ----------
//New classification of patient 
 $smarty->assign('sRequestTestIcon','<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
# $smarty->assign('LDClassifyNewPatient',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabnew&user_origin=lab\">New Test Request</a>");
 $smarty->assign('LDClassifyNewPatient',"<a href=\"social_service_pass.php?sid=$sid&lang=$lang&target=entry\">Classify Patient</a>");
//Lit of classified patient 
 $smarty->assign('sLabServicesRequestIcon','<img ' . createComIcon($root_path,'statbel2.gif','0') . ' align="absmiddle">');
# $smarty->assign('LDListOfClassifiedPatient',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservrequest_new&user_origin=lab\">List of Laboratory Request</a>");
 $smarty->assign('LDListOfClassifiedPatient',"<a href=\"social_service_pass.php?sid=$sid&lang=$lang&target=list\">List of Classified Patients</a>");

/*
 $smarty->assign('sLabServicesOrderIcon','<img ' . createComIcon($root_path,'hfolder.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDLabServicesOrder',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabOrder&user_origin=lab\">Undone/Done Requests</a>");
*/
 
 #----- SOCIAL SERVICE MANAGEMENT ---------		
//Social Serivce management 
  $smarty->assign('sBloodRequestIcon','<img ' . createComIcon($root_path,'redlist.gif','0') . ' align="absmiddle">');	
 # $smarty->assign('LDManageClassification',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=blood&user_origin=lab\">$LDBloodRequest</a>");
   $smarty->assign('LDManageClassification',"<a href=\"social_service_pass.php?sid=$sid&lang=$lang&target=admin\">Social Services</a>");

#added by VAN 07-05-08
$smarty->assign('sModifierIcon','<img ' . createComIcon($root_path,'mt_sel.gif','0') . ' align="absmiddle">');	
$smarty->assign('LDManageModifiers',"<a href=\"social_service_pass.php?sid=$sid&lang=$lang&target=modifier\">Social Services' Modifiers</a>");
#----------------	

  $smarty->assign('sBloodTestReceptionIcon','<img ' . createComIcon($root_path,'task_tree.gif','0') . ' align="absmiddle">');
#  $smarty->assign('LDSocialReports',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=admin&subtarget=blood&user_origin=lab\">$LDTestReception</a>");
  $smarty->assign('LDSocialReports',"<a href=\"social_service_pass.php?sid=$sid&lang=$lang&target=reports\">Social Service Reports</a>");

# Assign the submenu to the mainframe center block
 $smarty->assign('sMainBlockIncludeFile','social_service/social_service_sub_menu.tpl');

 /**
 * show  Mainframe Template
 */
 $smarty->display('common/mainframe.tpl');
?>