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
 $smarty->assign('sToolbarTitle',"Clinics");

//-----added 2007-10-03 FDP
 # Hide the return button
 $smarty->assign('pbBack',FALSE);
//-------------------------
#echo "root = ".$root_path;
?>
<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript">
	ShortcutKeys();
	function ShortcutKeys(){
		shortcut.add('Ctrl+Shift+N', NewRequest,
								{
									'type':'keydown',
									'propagate':false,
								}
						);

		shortcut.add('Ctrl+Shift+L', RequestList,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		shortcut.add('Ctrl+Shift+S', LabServicesAdmin,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );
	}

	function NewRequest(){
		//labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabnew&user_origin=lab
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabnew&user_origin=lab";
		window.location.href=urlholder;
	}

	function RequestList(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservrequest_new&user_origin=lab";
		window.location.href=urlholder;
	}

	function LabServicesAdmin(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservadmin&user_origin=lab";
		window.location.href=urlholder;
	}

	//---added by CHa , Feb 4, 2010-----
	function WritePresciption(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=prescription_writer&user_origin=clinic";
		window.location.href=urlholder;
	}

	function StandardPresciption()
	{
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=prescription_template&user_origin=clinic";
		window.location.href=urlholder;
	}

	function SoapEntry()
	{
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=soap_entry&user_origin=clinic";
		window.location.href=urlholder;
	}
	//---end CHA-------------------------

</script>

<?php
 # Help button href
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"Clinics");

 #$smarty->assign('sOldIcon','<img ' . createComIcon($root_path,'bestell.gif','0') . ' align="absmiddle">');
 # Edited by: AJMQ [Sept 01, 2006]
 $smarty->assign('sRequestTestIcon','<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDRequestTest',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabnew&user_origin=lab\">New Test Request</a>");
 $smarty->assign('LDRequestTest',"<a href=\"javascript:NewRequest();\">New Test Request</a>");

 #$smarty->assign('LDRequestTestOld',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglab&user_origin=lab\">New Test Request OLD</a>");

 #added by VAN
 $smarty->assign('sLabServicesRequestIcon','<img ' . createComIcon($root_path,'statbel2.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesRequest',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservrequest_new&user_origin=lab\">List of Laboratory Request</a>");
 $smarty->assign('LDLabServicesRequest',"<a href=\"javascript:RequestList();\">List of Service Requests</a>");

	# Test parameters admin submenu block

	#$smarty->assign('LDAdministration',$LDAdministration);
	$smarty->assign('LDAdministration',"Service Management");

#----------commented by VAN--------
 #$smarty->assign('LDManageTransactions',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=chemlabor&user_origin=lab\">Manage Transactions</a>");
 #-------------------------------------
 $smarty->assign('sLabServicesAdminIcon','<img ' . createComIcon($root_path,'waiting.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesAdmin',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservadmin&user_origin=lab\">Laboratory Services</a>");
 $smarty->assign('LDLabServicesAdmin',"<a href=\"javascript:LabServicesAdmin();\">Services Manager</a>");

 #---added by CHA, Feb 4, 2010---------------
 $smarty->assign('sPrescriptionIcon', '<img src="'.$root_path.'/gui/img/common/default/report_edit.png" />');
 $smarty->assign('sPrescriptionLink', "<a href=\"javascript:WritePresciption();\">Prescription Writer</a>");
 #---added by CHA, Aug 12, 2010---------------
 $smarty->assign('sStandardIcon', '<img src="'.$root_path.'/gui/img/common/default/layout_add.png" />');
 $smarty->assign('sStandardLink', "<a href=\"javascript:StandardPresciption();\">Prescription Templates</a>");
	#---added by CHA, Aug 28, 2010---------------
 $smarty->assign('sSoapIcon', '<img src="'.$root_path.'/gui/img/common/default/note_add.png" />');
 $smarty->assign('sSoapLink', "<a href=\"javascript:SoapEntry();\">S.O.A.P Entry</a>");
 #---end CHA---------------------------------

# Assign the submenu to the mainframe center block

 $smarty->assign('sMainBlockIncludeFile','clinics/submenu_lab.tpl');


 /**
 * show  Mainframe Template
 */

 $smarty->display('common/mainframe.tpl');

// require($root_path.'js/floatscroll.js');

?>