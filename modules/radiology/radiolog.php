<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/*** CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','radio.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');

$thisfile=basename(__FILE__);
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
$HTTP_SESSION_VARS['sess_path_referer']=$top_dir.$thisfile;

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

# Added for the common header top block

 $smarty->assign('sToolbarTitle',$LDRadio);

 # Added for the common header top block
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDRadio')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDRadio);

# Collect javascript
ob_start();

?>
<script language="javascript">
<!--
  var urlholder;

  function srcxray(){
<?php
	if($cfg['dhtml'])
	{
	echo 'w=window.parent.screen.width;
			h=window.parent.screen.height;
			';
	}
	else echo 'w=800;
					h=600;
					';
?>
	radiologwin=window.open("radiolog-xray-javastart.php?sid=<?php echo "$sid&lang=$lang" ?>&user=<?php echo $aufnahme_user; ?>","radiologwin","menubar=no,resizable=yes,scrollbars=yes, width=" +(w-15)+ ", height=" +(h-60) );
//	radiologwin=window.open("radiolog-xray-javastart.php?sid=<?php echo "$sid&lang=$lang" ?>&user=<?php echo $aufnahme_user.'"' ?>,"radiologwin","menubar=no,resizable=yes,scrollbars=yes, width=" + (w-15) + ", height=" + (h-60) );

<?php
	if($cfg['dhtml']) echo 'window.radiologwin.moveTo(0,0);';
?>
}
//-->
</script>
<script language="javascript" src="<?php echo $root_path; ?>js/dicom.js"></script>

<?php

	$sTemp = ob_get_contents();
 	ob_end_clean();

	# Append javascript to JavaScript block

	$smarty->append('JavaScript',$sTemp);

# Prepare the submenu icons
/*
 $aSubMenuIcon=array(createComIcon($root_path,'patdata.gif','0'),
 										createComIcon($root_path,'waiting.gif','0'),
										createComIcon($root_path,'calmonth.gif','0'),
										createComIcon($root_path,'book_hotel.gif','0'),
										createComIcon($root_path,'bestell.gif','0'),
										createComIcon($root_path,'documents.gif','0'),
										createComIcon($root_path,'torso.gif','0'),
										createComIcon($root_path,'torso_br.gif','0'),
										);
*/										
 $aSubMenuIcon=array(createComIcon($root_path,'patdata.gif','0'),
 										createComIcon($root_path,'waiting.gif','0'),
										createComIcon($root_path,'calmonth.gif','0'),
										createComIcon($root_path,'book_hotel.gif','0'),
										createComIcon($root_path,'bestell.gif','0'),
										createComIcon($root_path,'documents.gif','0'),
                                        createComIcon($root_path,'file_update.gif','0')
										);

/*
createComIcon($root_path,'waiting.gif','0'),
createComIcon($root_path,'sitemap_animator.gif','0'),
createComIcon($root_path,'timeplan2.gif','0')
*/										

# Prepare the submenu item descriptions

//$smarty->assign('LDCreateTransaction',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio&user_origin=lab\">Create transaction</a>");
//$smarty->assign('LDManageTransactions',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio&user_origin=lab\">Manage transactions</a>");
//$smarty->assign('LDServicePrices',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio&user_origin=lab\">Service prices</a>");
//# $smarty->assign('LDViewAssignRequest',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_undone&user_origin=lab&dept_nr=19\">$LDViewAssignRequest</a>");   # burn added: Oct. 2, 2006
//
//
//$smarty->assign('LDViewAssignRequest',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_undone&user_origin=lab&dept_nr=158\">$LDViewAssignRequest</a>");   # burn added: July 11, 2007
//$smarty->assign('LDViewAssignRequestTxt',"$LDViewAssignRequestTxt");      # burn added: Oct. 2, 2006

#added by VAN 07-10-07
$smarty->assign('LDRadioServicesOLD',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradioserviceOLD&user_origin=lab&dept_nr=158\">Radiology Services OLD</a>");

#added by VAN 03-15-08
$smarty->assign('LDRadioServices',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradioservice&user_origin=lab&dept_nr=158\">Radiology Services</a>");
$smarty->assign('LDRadioServicesGroups',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradiogroup&user_origin=lab&dept_nr=158\">Radiology Groups</a>");
$smarty->assign('sRadioServicesIcon','<img ' . createComIcon($root_path,'waiting.gif','0') . ' align="absmiddle">');
$smarty->assign('sRadioServicesGroupIcon','<img ' . createComIcon($root_path,'sitemap_animator.gif','0') . ' align="absmiddle">');
$smarty->assign('sRadioDOCSchedulerIcon','<img ' . createComIcon($root_path,'timeplan2.gif','0') . ' align="absmiddle">');

#added by VAN 07-07-08
$smarty->assign('LDRadioFindingCode',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradiofindings&user_origin=lab&dept_nr=158\">Radiology Finding's Code</a>");
$smarty->assign('sRadioFindingCodeIcon','<img ' . createComIcon($root_path,'bilder.gif','0') . ' align="absmiddle">');

$smarty->assign('LDRadioImpressionCode',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradioimpression&user_origin=lab&dept_nr=158\">Radiology Impression's Code</a>");
$smarty->assign('sRadioImpressionCodeIcon','<img ' . createComIcon($root_path,'articles.gif','0') . ' align="absmiddle">');

$smarty->assign('LDRadioDoctorPartner',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradioreader&user_origin=lab&dept_nr=158\">Radiology's Co-reader Physicians</a>");
$smarty->assign('sRadioDoctorPartnerIcon','<img ' . createComIcon($root_path,'newteam.gif','0') . ' align="absmiddle">');
#---------------

$smarty->assign('LDRadioTech',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradiotech&user_origin=lab&dept_nr=158\">Serve Request</a>");
$smarty->assign('sRadioTechIcon','<img ' . createComIcon($root_path,'disc_unrd.gif','0') . ' align="absmiddle">');
#-----------------------

#add by Mark : Aug 24, 2007	
$smarty->assign('LDRadioRequestList',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radiorequestlist&user_origin=lab&dept_nr=158\">List of Radiology Service Request</a>");
#burn added : July 18, 2007
$smarty->assign('LDRadioDOCScheduler',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradioDOCScheduler&user_origin=radiology&retpath=menu\">Radiology Doctors Scheduler</a>");

#added by VAN 04-21-08
$smarty->assign('sRadioReportIcon','<img ' . createComIcon($root_path,'chart.gif','0') . ' align="absmiddle">');
$smarty->assign('LDRadioReport',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=segradioreports&user_origin=radiology&retpath=menu\">Radiology Reports</a>");
										
/*
$aSubMenuText=array($LDTestRequestRadioTxt,
					"List of radiological service requests",
					$LDViewAssignRequestTxt,
					$LDNurseTestRequestRadio,
					"Create new radiological service request",
					$LDTestReceptionTxt,
					$LDDicomImagesTxt,
					$LDUploadDicomTxt,
					$LDSelectViewerTxt,
					$LDNewsTxt
				); 
*/
/*
$aSubMenuText=array("Create new radiological service request",
						"List of radiological service requests",
						"Calendar for scheduling",
						"List of scheduled requests",
						"List of pending and for referral requests",
						"List of served and done requests",
						"List of all radiology patients",
						"List of all borrowed films",
						"View list of patients whose films are being borrowed"
				); 
*/
/*
$aSubMenuText=array("Create new radiological service request",
						"List of radiological service requests",
						"Calendar for scheduling",
						"List of scheduled requests",
						"List of pending and for referral requests",
						"List of served and done requests",
						"List of all radiology patients",
						"List of all borrowed films",
				); 
*/
$aSubMenuText=array("Create new radiological service request",
						"List of radiological service requests",
						"Calendar for scheduling",
						"Record served radiological (XRAY, CT-SCAN, MRI, ULTRASOUND and others) requests and list of scheduled requests",
						"List of pending and for referral requests",
						"List of served and done requests",
                        "Simultaneous viewing of multiple exam results"
				); 
				
# Prepare the submenu item links indexed by their template tags
/*
$aSubMenuItem=array('LDCreateNewRadioServiceRequest' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=radio\" >New Test Request</a>",
					'LDRadioServiceRequestList' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radiorequestlist&user_origin=radio&dept_nr=158\">List of Service Requests</a>",
					'LDRadioScheduleRequestCalendar' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_cal&user_origin=radio&dept_nr=158\">Schedule</a>",
					'LDRadioScheduleRequestList' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_cal_list&user_origin=radio&dept_nr=158\">List of Scheduled Requests</a>",
					'LDUndoneRequest' =>"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_undone&user_origin=radio&dept_nr=158\">Undone Requests</a>",
					'LDDoneRequest' =>"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_done&user_origin=radio&dept_nr=158\">Archive</a>",
					'LDRadioPatientList' =>"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_patient&user_origin=radio&dept_nr=158\">By patient's name, all films</a>",
					'LDRadioBorrowList' =>"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_borrow&user_origin=radio&dept_nr=158\">By borrower's name</a>"
				);   
*/
$aSubMenuItem=array('LDCreateNewRadioServiceRequest' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=radio\" >New Test Request</a>",
					'LDRadioServiceRequestList' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radiorequestlist&user_origin=radio&dept_nr=158\">List of Service Requests</a>",
					'LDRadioScheduleRequestCalendar' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_cal&user_origin=radio&dept_nr=158\">Schedule</a>",
					'LDRadioScheduleRequestList' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_cal_list&user_origin=radio&dept_nr=158\">Served and Scheduled Requests</a>",
					'LDUndoneRequest' =>"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_undone&user_origin=radio&dept_nr=158\">Undone Requests</a>",
					'LDDoneRequest' =>"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_done&user_origin=radio&dept_nr=158\">Archive</a>",
                    'LDUnifiedResults' =>"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_unified&user_origin=radio&dept_nr=158\">Unified Results</a>"
				);   

#edited by VAN 04-21-08
$smarty->assign('sRadioPatientListIcon','<img ' . createComIcon($root_path,'torso.gif','0') . ' align="absmiddle">');
$smarty->assign('LDRadioPatientList',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_patient&user_origin=radio&dept_nr=158\">By patient's name, all films</a>");

$smarty->assign('sRadioBorrowListIcon','<img ' . createComIcon($root_path,'torso_br.gif','0') . ' align="absmiddle">');
$smarty->assign('LDRadioBorrowList',"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_borrow&user_origin=radio&dept_nr=158\">By borrower's name</a>");
				
/*
$aSubMenuItem=array('LDTestRequestRadio' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio&user_origin=lab\">$LDTestRequestRadio</a>",
					'LDRadioServiceRequestList' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radiorequestlist&user_origin=lab&dept_nr=158\">List of Service Request</a>",
					'LDViewAssignRequest' =>"<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_undone&user_origin=lab&dept_nr=158\">$LDViewAssignRequest</a>",
					'LDCreateNewRadioServiceRequest' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab\" >New Service Request</a>",
					'LDNurseTestRequestRadio' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab\" >$LDNurseTestRequestRadio</a>",
					'LDTestReception' => "<a href=\"".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=admin&subtarget=radio&user_origin=lab\" >$LDTestReception</a>",
					'LDDicomImages' => "<a href=\"radio_pass.php".URL_APPEND."&target=view\">$LDDicomImages</a>",
					'LDUploadDicom' => "<a href=\"radio_pass.php".URL_APPEND."&target=upload\">$LDUploadDicom</a>",
					'LDSelectViewer' => "<a href=\"javascript:popSelectDicomViewer('$sid','$lang')\">$LDSelectViewer</a>",
					'LDNews' => "<a href=\"".$root_path."modules/news/newscolumns.php". URL_APPEND."&dept_nr=19\">$LDNews</a>"
				);   
*/

#Insert Code for transactions here : Mark:) 08-16-2006
#$smarty->assign('LDCreateTransaction',"<a href=\>Create Transaction</a>");


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

# Assign the submenu to the mainframe center block

 $smarty->assign('sMainBlockIncludeFile','radiology/submenu_radiology.tpl');

 /**
 * show Template
 */

 $smarty->display('common/mainframe.tpl');
?>
