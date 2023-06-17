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
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
$thisfile=$root_path.'modules/billing/bill-main-menu.php';

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
 $smarty->assign('sToolbarTitle',"Billing Section");

//-----added 2007-10-03 FDP
 # Hide the return button
 $smarty->assign('pbBack',FALSE);
//-------------------------

 # Help button href
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
// $smarty->assign('title',$LDLab);
 $smarty->assign('title',"Billing Section");

// --- Added by LST --- 4-18-2008 / Modified -- 6-26-2008 ----
unset($_SESSION["filteroption"]);
unset($_SESSION["filtertype"]);
unset($_SESSION["filter"]);
unset($_SESSION["current_page"]);
//------------------------------------
 
 //NOTE: REMOVE THIS AFTER SOCIAL SERVICE PAGE IS COMPLETE
 #echo "<center><b>- THIS SITE IS UNDER CONSTRUCTION -</b></center>";
# echo "<center><b>By: MLHE :)</b></center>";
 
 //Billing Service
 // $smarty->assign('sRequestTestIcon','<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
 // $smarty->assign('LDViewBill',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=seg_billing&user_origin=lab\">Process Billing</a>");
 //$smarty->assign('LDViewBill',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabnew&user_origin=lab\">Request Billing</a>");
 
 //added by Francis.L.G 11-08-13
 //Billing Service nonPHIC
 //$smarty->assign('sRequestTestIcon','<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
 #comment by shandy 01/02/2014
 //$smarty->assign('LDViewBillnPHIC',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=seg_billing_nPHIC&user_origin=lab\">Process Billing (Non PHIC)</a>");

//added by Francis.L.G 11-08-13
//Billing Service PHIC
$smarty->assign('sRequestTestIcon','<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
$smarty->assign('LDViewBillPHIC',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=seg_billing_PHIC&user_origin=lab\">Process Billing (New)</a>");

 //List of Billed  patient
 $smarty->assign('sLabServicesRequestIcon','<img ' . createComIcon($root_path,'statbel2.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDListOfBilling',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=seg_billing_list&user_origin=lab\">List of Billed Patients</a>");
 
#Bill Management
#edited by VAN 02-07-08
 #$smarty->assign('sManagePackageIcon','<img ' . createComIcon($root_path,'copy_adrs.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDManageClassification',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=packagemanage&user_origin=lab\">Manage Packages</a>");

 $smarty->assign('sLDOtherServicesIcon','<img ' . createComIcon($root_path,'sitemap_webcam.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDOtherServices',"<a href=".$root_path.'modules/cashier/seg-cashier-pass.php'. URL_APPEND."&target=miscellaneous&userck=$userck&from=$thisfile".">Manage Miscellaneous Services</a>");

 $smarty->assign('sLDSocialReportsIcon','<img ' . createComIcon($root_path,'file_update.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDBillReports',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=seg_billing_transmittal&user_origin=lab\">Transmittal</a>");
 
 $smarty->assign('sLDTransmittalsHistIcon','<img ' . createComIcon($root_path,'indexbox2.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDTransmittalsHistory',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=seg_transmittal_history&user_origin=lab\">Transmittal History</a>");

//pol start
# administration
# $smarty->assign('sLDBillingReportsIcon','<img ' . createComIcon($root_path,'report.png','0') . ' align="absmiddle">');
# $smarty->assign('LDBillingReports',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=seg_billing_reports&user_origin=lab\">Reports</a>");

//added by Nick, 2-1-14
 $smarty->assign('sLDBillingReportsIcon_jasper','<img src="'.$root_path.'/images/print.png" style="width:20px;height:20px;" align="absmiddle">');
 $smarty->assign('LDBillingReports_jasper',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=seg_billing_reports&user_origin=lab&jasperPrint=1\">Reports</a>");
//end nick
//pol end

$smarty->assign('sLDPostClaimIcon','<img ' . createComIcon($root_path,'post_claim.gif','0') . ' align="absmiddle">');
$smarty->assign('LDClaimsPost',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=post_claim&user_origin=bill\">Process Claim</a>");

$smarty->assign('sLDClaimsHistIcon','<img ' . createComIcon($root_path,'claims_hist.gif','0') . ' align="absmiddle">');
$smarty->assign('LDClaimsHistory',"<a href=\"bill-pass.php?sid=$sid&lang=$lang&target=post_hist&user_origin=bill\">Claims History</a>");

//Promissory Note
$smarty->assign('sPromissoryNoteIcon','<img ' . createComIcon($root_path,'articles.gif','0') . ' align="absmiddle">');
$smarty->assign('LDPromissoryNote',"<a href=\"bill-pass.php?target=seg_billing_promissory\">Promissory Note</a>");

$smarty->assign('sPNListIcon','<img ' . createComIcon($root_path,'text_list_numbers.png','0') . ' align="absmiddle">');
$smarty->assign('LDListPromi',"<a href=\"bill-pass.php?target=seg_billing_promissory_list\">Lists of Promissory Note</a>");

//Dialysis
$smarty->assign('sLDNewDialysisIcon','<img ' . createComIcon($root_path,'blood_note.png','0') . ' align="absmiddle">');
$smarty->assign('LDNewDialysis',"<a href=\"bill-pass.php?target=new_dialysis\">New Dialysis</a>");

$smarty->assign('sLDListDialysisIcon','<img ' . createComIcon($root_path,'list.gif','0') . ' align="absmiddle">');
$smarty->assign('LDListDialysis',"<a href=\"bill-pass.php?target=list_dialysis\">List of Dialysis Patients</a>");

//PF Excess
$smarty->assign('sPfExcessIcon','<img ' . createComIcon($root_path,'user_suit.png','0') . ' align="absmiddle">');
$smarty->assign('LDListPfExcess',"<a href=\"bill-pass.php?target=list_pf_excess\">List of Doctor's Excess</a>");

# Assign the submenu to the mainframe center block
 $smarty->assign('sMainBlockIncludeFile','billing/billing_main_menu.tpl');


 /**
 * show  Mainframe Template
 */
 $smarty->display('common/mainframe.tpl');
?>