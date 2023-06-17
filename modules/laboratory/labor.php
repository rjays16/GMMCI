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
 $smarty->assign('sToolbarTitle',$LDLab);

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

		shortcut.add('Ctrl+Shift+D', TestRequestList,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		shortcut.add('Ctrl+Shift+B', BloodRequest,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );
				/*
		shortcut.add('Ctrl+Shift+P', PendingBloodRequest,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );
					*/
		shortcut.add('Ctrl+Shift+S', LabServicesAdmin,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		 //added by VAN 03-10-08
		 shortcut.add('Ctrl+Shift+G', LabServicesGroup,
							{
								'type':'keydown',
								'propagate':false,
							}
						 );

		 shortcut.add('Ctrl+Shift+R', LabDeptReport,
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

	function TestRequestList(done){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabOrder&user_origin=lab&done="+done;
		window.location.href=urlholder;
	}
    
    function TestResultList(){
        urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabResult&user_origin=lab";
        window.location.href=urlholder;
    }

	function BloodRequest(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood&user_origin=blood";
		window.location.href=urlholder;
	}

	//added by VAN 05-11-2010
	function OtherCharges(){
		 urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=charges&user_origin=lab";
		 window.location.href=urlholder;
	}

	function RequestSample(){
			urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=samples&user_origin=lab";
		 window.location.href=urlholder;
	}
	//------------
		/*
	function PendingBloodRequest(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_list&subtarget=blood&user_origin=lab";
		window.location.href=urlholder;
	}
			*/

		function BloodPromissoryNote() {
				urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=promissory_note&user_origin=blood";
				window.location.href=urlholder;
		}

		//added by CHA 07-30-2009
		function BloodDonorRegistration()
		{
			 urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_donor&user_origin=blood";
			 window.location.href=urlholder;
		}
		//end cha

	function BloodRequestList(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_list&user_origin=blood";
		window.location.href=urlholder;
	}

	function BloodTestRequestList(done){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=blood_result&user_origin=blood&done="+done;
		window.location.href=urlholder;
	}

	function LabServicesAdmin(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservadmin&user_origin=lab";
		window.location.href=urlholder;
	}

	function LabServicesAdminOLD(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservadminOLD&user_origin=lab";
		window.location.href=urlholder;
	}

	//added by VAN 03-10-08
	function LabServicesGroup(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservgroup&user_origin=lab";
		window.location.href=urlholder;
	}

		//added by Raissa 02-02-09
		function LabTests(){
				urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabtest&user_origin=lab";
				//urlholder="labor_test.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabtests&user_origin=lab";
				window.location.href=urlholder;
		}

		function LabReagents(){
				urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabreagents&user_origin=lab";
				window.location.href=urlholder;
		}

	function LabReagentsInventory(){
				urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabinventory&user_origin=lab";
				window.location.href=urlholder;
		}

	function LabDeptReport(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabreports&user_origin=lab";
		window.location.href=urlholder;
	}

	//added by VAN 07-23-2010
	function SpecialLabRequest(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=specialLab&user_origin=splab";
		window.location.href=urlholder;
	}

	function SpecialLabRequestList(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=specialLab_list&user_origin=splab";
		window.location.href=urlholder;
	}

	function SpecialLabTestRequestList(done){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=specialLab_result&user_origin=splab&done="+done;
		window.location.href=urlholder;
	}
	//--------------------

	//added by VAN 08-31-2010
	function ICLabRequest(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=ICLab&user_origin=iclab";
		window.location.href=urlholder;
	}

	function ICLabRequestList(){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=ICLab_list&user_origin=iclab";
		window.location.href=urlholder;
	}

	function ICLabTestRequestList(done){
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=ICLab_result&user_origin=iclab&done="+done;
		window.location.href=urlholder;
	}
	//--------------------
    
    //added by VAS 03-25-2013
    //report generator
    function ReportGenFxn(){
       urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=reportgen&from=bloodbank";
       window.location.href=urlholder; 
    }

</script>

<?php
 # Help button href
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDLab);

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

 #$smarty->assign('LDLabServicesRequestOld',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservrequest&user_origin=lab\">Laboratory Services Request Old</a>");

#added by VAN 11-08-07
 $smarty->assign('sLabServicesOrderIcon','<img ' . createComIcon($root_path,'hfolder.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesOrder',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabOrder&user_origin=lab\">Undone/Done Requests</a>");
 $smarty->assign('LDLabServicesOrder',"<a href=\"javascript:TestRequestList(0);\">List of Undone Requests</a>");

 #added by VAN 07-02-08
 $smarty->assign('sLabServicesDoneIcon','<img ' . createComIcon($root_path,'task_tree.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesOrder',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabOrder&user_origin=lab\">Undone/Done Requests</a>");
 $smarty->assign('LDLabServicesDone',"<a href=\"javascript:TestRequestList(1);\">List of Done Requests</a>");
 
 $smarty->assign('sLabServicesResultIcon','<img ' . createComIcon($root_path,'yellowlist.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDLabServicesResult',"<a href=\"javascript:TestResultList();\">List of Laboratory Results</a>");
 #------------------

 #added by VAN 05-11-2010
 $smarty->assign('sOtherClinicalIcon','<img ' . createComIcon($root_path,'documents.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDOtherClinical',"<a href=\"javascript:OtherCharges();\">Other Clinical Charges</a>");

 $smarty->assign('sLabServicesRequestSampleIcon','<img ' . createComIcon($root_path,'application_form_edit.png','0') . ' align="absmiddle">');
 $smarty->assign('LDLabServicesRequestSample',"<a href=\"javascript:RequestSample();\">Requests With or W/out Sample</a>");

 # Medical lab submenu block

 #----------commented by VAN--------

 $smarty->assign('LDMedLab',$LDMedLab);
 $smarty->assign('LDMedLabTestRequest',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=chemlabor&user_origin=lab\">$LDTestRequest</a>");
 $smarty->assign('LDTestRequestChemLabTxt',$LDTestRequestChemLabTxt);

	$smarty->assign('LDMedLabTestReception',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=admin&subtarget=chemlabor&user_origin=lab\">$LDTestReception</a>");
	$smarty->assign('LDTestReceptionTxt',$LDTestReceptionTxt);

	$smarty->assign('LDSeeData',"<a href=\"labor_datasearch_pass.php?sid=$sid&lang=$lang&route=validroute\">$LDSeeData </a>");
	$smarty->assign('LDSeeLabData',$LDSeeLabData);

	$smarty->assign('LDNewData',"<a href=\"labor_datainput_pass.php?sid=$sid&lang=$lang\">$LDNewData</a>");
	$smarty->assign('LDEnterLabData',$LDEnterLabData);

	# Pathology lab submenu block

	$smarty->assign('LDPathLab',$LDPathLab);

	$smarty->assign('LDPathLabTestRequest',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=patho&user_origin=lab\">$LDTestRequest</a>");
	$smarty->assign('LDTestRequestPathoTxt',$LDTestRequestPathoTxt);

	$smarty->assign('LDPathLabTestReception',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=admin&subtarget=patho&user_origin=lab\">$LDTestReception</a>");

	# Bacteriology lab submenu block

	$smarty->assign('LDBacLab',$LDBacLab);

	$smarty->assign('LDBacLabTestRequest',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=baclabor&user_origin=lab\">$LDTestRequest</a>");
	$smarty->assign('LDTestRequestBacterioTxt',$LDTestRequestBacterioTxt);

	$smarty->assign('LDBacLabTestReception',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=admin&subtarget=baclabor&user_origin=lab\">$LDTestReception</a>");

	# Blood lab submenu block

	$smarty->assign('LDBloodBank','Blood Bank');

	$smarty->assign('sBloodRequestIcon','<img ' . createComIcon($root_path,'redlist.gif','0') . ' align="absmiddle">');
	#$smarty->assign('LDBloodRequest',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=blood&user_origin=lab\">$LDBloodRequest</a>");
	$smarty->assign('LDBloodRequest',"<a href=\"javascript:BloodRequest();\">$LDBloodRequest</a>");

	$smarty->assign('LDBloodRequestTxt',$LDBloodRequestTxt);

	#$smarty->assign('sBloodTestReceptionIcon','<img ' . createComIcon($root_path,'task_tree.gif','0') . ' align="absmiddle">');
	#$smarty->assign('LDBloodTestReception',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=admin&subtarget=blood&user_origin=lab\">$LDTestReception</a>");
	#$smarty->assign('LDBloodTestReception',"<a href=\"javascript:PendingBloodRequest();\">$LDTestReception</a>");

	#added by VAN
 $smarty->assign('sBloodServicesRequestIcon','<img ' . createComIcon($root_path,'statbel2.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesRequest',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservrequest_new&user_origin=lab\">List of Laboratory Request</a>");
 $smarty->assign('LDBloodServicesRequest',"<a href=\"javascript:BloodRequestList();\">List of Service Requests</a>");

 #$smarty->assign('LDLabServicesRequestOld',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservrequest&user_origin=lab\">Laboratory Services Request Old</a>");

#added by VAN 11-08-07
 $smarty->assign('sBloodServicesOrderIcon','<img ' . createComIcon($root_path,'hfolder.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesOrder',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabOrder&user_origin=lab\">Undone/Done Requests</a>");
 $smarty->assign('LDBloodLabServicesOrder',"<a href=\"javascript:BloodTestRequestList(0);\">List of Undone Requests</a>");

 #added by VAN 07-02-08
 $smarty->assign('sBloodServicesDoneIcon','<img ' . createComIcon($root_path,'task_tree.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesOrder',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabOrder&user_origin=lab\">Undone/Done Requests</a>");
 $smarty->assign('LDBloodServicesDone',"<a href=\"javascript:BloodTestRequestList(1);\">List of Done Requests</a>");



 //Added by Omick 02-06-2009
$smarty->assign('blood_promissory_icon','<img src="'.$root_path.'images/laboratory/promissory_note.png" align="absmiddle">');
$smarty->assign('blood_promissory_title',"<a href=\"javascript:BloodPromissoryNote();\">Promissory Note</a>");

#Added by CHA 07-30-2009
$smarty->assign('blood_donor_icon','<img src="'.$root_path.'gui/img/common/default/group.png" align="absmiddle">');
$smarty->assign('blood_donor_title',"<a href=\"javascript:BloodDonorRegistration();\">Blood Donor Registration</a>");
#end CHA

#------------------commented----------------------------------------

	# Test parameters admin submenu block

	#$smarty->assign('LDAdministration',$LDAdministration);
	$smarty->assign('LDAdministration',"Laboratory Service Management");

#----------commented by VAN--------
 #$smarty->assign('LDManageTransactions',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=chemlabor&user_origin=lab\">Manage Transactions</a>");
 #-------------------------------------
 $smarty->assign('sLabServicesAdminIcon','<img ' . createComIcon($root_path,'waiting.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesAdmin',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservadmin&user_origin=lab\">Laboratory Services</a>");
 $smarty->assign('LDLabServicesAdmin',"<a href=\"javascript:LabServicesAdmin();\">Laboratory Services</a>");

 #$smarty->assign('LDLabServicesAdminOLD',"<a href=\"javascript:LabServicesAdminOLD();\">Laboratory Services OLD</a>");

 #added by VAN 03-10-08
 $smarty->assign('sLabServicesGroupsIcon','<img ' . createComIcon($root_path,'sitemap_animator.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesAdmin',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabservadmin&user_origin=lab\">Laboratory Services</a>");
 $smarty->assign('LDLabServicesGroups',"<a href=\"javascript:LabServicesGroup();\">Laboratory Sections</a>");

 #added by Raissa 02-02-09
 $smarty->assign('sLabTestsIcon','<img ' . createComIcon($root_path,'documents.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDLabTests',"<a href=\"javascript:LabTests();\">Laboratory Tests</a>");

 $smarty->assign('sLabReagentsIcon','<img ' . createComIcon($root_path,'book_hotel.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDLabReagents',"<a href=\"javascript:LabReagents();\">Laboratory Reagents</a>");

 $smarty->assign('sLabReagentsInventoryIcon','<img ' . createComIcon($root_path,'timeplan2.gif','0') . ' align="absmiddle">');
 $smarty->assign('LDLabReagentsInventory',"<a href=\"javascript:LabReagentsInventory();\">Reagents Inventory</a>");

 $smarty->assign('sLabServicesReportIcon','<img ' . createComIcon($root_path,'chart.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDLabServicesReport',"<a href=\"labor_test_request_pass.php?sid=$sid&lang=$lang&target=seglabreports&user_origin=lab\">Laboratory Reports</a>");
 $smarty->assign('LDLabServicesReport',"<a href=\"javascript:LabDeptReport();\">Laboratory Reports</a>");

	#----------commented by VAN --------------------
	#$smarty->assign('LDTestParameters',"<a href=\"seg-lab-services-param-pass.php?sid=$sid&lang=$lang&user_origin=lab\">$LDTestParameters</a>");
	#$smarty->assign('LDTestParametersTxt',$LDTestParametersTxt);
	#-------------------------------------------------

	#added by VAN 07-23-2010
	$smarty->assign('LDSpecialLab','Special Laboratory');
	$smarty->assign('sSpecialLabRequestIcon','<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDSpecialLabRequest',"<a href=\"javascript:SpecialLabRequest();\">Special Laboratory Request</a>");
	$smarty->assign('sSpecialLabServicesRequestIcon','<img ' . createComIcon($root_path,'statbel2.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDSpecialLabServicesRequest',"<a href=\"javascript:SpecialLabRequestList();\">List of Service Requests</a>");
	$smarty->assign('sSpecialLabServicesOrderIcon','<img ' . createComIcon($root_path,'hfolder.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDSpecialLabLabServicesOrder',"<a href=\"javascript:SpecialLabTestRequestList(0);\">List of Undone Requests</a>");
	$smarty->assign('sSpecialLabServicesDoneIcon','<img ' . createComIcon($root_path,'task_tree.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDSpecialLabServicesDone',"<a href=\"javascript:SpecialLabTestRequestList(1);\">List of Done Requests</a>");
	#----------------------

	#added by VAN 08-31-2010
	$smarty->assign('LDICLab','Industrial Clinic Laboratory');
	$smarty->assign('sICLabRequestIcon','<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDICLabRequest',"<a href=\"javascript:ICLabRequest();\">IC Laboratory Request</a>");
	$smarty->assign('sICLabServicesRequestIcon','<img ' . createComIcon($root_path,'statbel2.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDICLabServicesRequest',"<a href=\"javascript:ICLabRequestList();\">List of Service Requests</a>");
	$smarty->assign('sICLabServicesOrderIcon','<img ' . createComIcon($root_path,'hfolder.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDICLabLabServicesOrder',"<a href=\"javascript:ICLabTestRequestList(0);\">List of Undone Requests</a>");
	$smarty->assign('sICLabServicesDoneIcon','<img ' . createComIcon($root_path,'task_tree.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDICLabServicesDone',"<a href=\"javascript:ICLabTestRequestList(1);\">List of Done Requests</a>");
	#----------------------

$smarty->assign('sBloodGenerateReportIcon','<img ' . createComIcon($root_path,'icon-reports.png','0') . ' align="absmiddle">');
$smarty->assign('LDBloodGenerateReport',"<a href=\"javascript:ReportGenFxn();\">Reports</a>");
    
# Assign the submenu to the mainframe center block

 $smarty->assign('sMainBlockIncludeFile','laboratory/submenu_lab.tpl');


 /**
 * show  Mainframe Template
 */

 $smarty->display('common/mainframe.tpl');

// require($root_path.'js/floatscroll.js');

?>