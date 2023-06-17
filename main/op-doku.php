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
define('LANG_FILE','or.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

setcookie(firstentry,''); // The cookie "firsentry" is used for switching the cat image

/* Check the start script as break destination*/
if (!empty($HTTP_SESSION_VARS['sess_path_referer'])&&($HTTP_SESSION_VARS['sess_path_referer']!=$top_dir.$thisfile)){
	if(file_exists($root_path.$HTTP_SESSION_VARS['sess_path_referer'])){
		$breakfile=$HTTP_SESSION_VARS['sess_path_referer'];
	}else {
		 /* default startpage */
		$breakfile = 'main/startframe.php';
	}
} else {
		 /* default startpage */
		$breakfile = 'main/startframe.php';
}
$breakfile=$root_path.$breakfile.URL_APPEND;

// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');

$HTTP_SESSION_VARS['sess_path_referer']=$top_dir.$thisfile;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Module title in the toolbar

 $smarty->assign('sToolbarTitle',$LDOr);

 # Help button href
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDOr')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDOr);

 # Append javascript code to javascript block

	$sTemp = '<SCRIPT language="JavaScript" src="'. $root_path.'js/sublinker-nd.js?t='.time().'"></SCRIPT>';

	$smarty->append('JavaScript',$sTemp);

	?>
	<script type="text/javascript">
	//new born registration
		function NewbornFxn(){
				urlholder="<?=$root_path?>modules/ipd/seg-ipd-pass.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipd_newbornreg&from=ipd";
				window.location.href=urlholder;
		}

	</script>

	<?

	# Create the submenu blocks

	# OR Surgeons submenu block

	$smarty->assign('LDOrDocs',"<img ".createLDImgSrc($root_path,'arzt2.gif','0','absmiddle')."  alt=\"$LDDoctor\">");

	#added by VAN 02-07-08
	$smarty->assign('sOrDocumentIcon','<img ' . createComIcon($root_path,'showdata.gif','0') . ' align="absmiddle">');
	#$smarty->assign('LDOrDocument',"<a href=\"".$root_path."modules/op_document/op-doku-pass.php".URL_APPEND."\" onmouseover=\"ssm('ALog'); clearTimeout(timer)\"
	#	onmouseout=\"timer=setTimeout('hsm()',1000)\" >$LDOrDocument</a>");
	$smarty->assign('LDOrDocument',"<a href=\"".$root_path."modules/op_document/op-doku-pass.php".URL_APPEND."\" >$LDOrDocument</a>");
	$smarty->assign('LDOrDocumentTxt',$LDOrDocumentTxt);

	#added by VAN 02-07-08
	$smarty->assign('sQviewDocsIcon','<img ' . createComIcon($root_path,'preview_on.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDQviewDocs',"<a href=\"".$root_path."modules/doctors/doctors-dienst-schnellsicht.php".URL_APPEND."&retpath=op\">$LDDOC $LDQuickView</a>");
	$smarty->assign('LDQviewTxtDocs',$LDQviewTxtDocs);

# OR Nursing submenu block

 $smarty->assign('LDOrNursing',"<img ".createLDImgSrc($root_path,'pflege2.gif','0','absmiddle')."  alt=\"$LDNursing\">");

$smarty->assign('sgOprequest', "<a href=\"".$root_path."modules/or/request/op_request_pass.php".URL_APPEND."&target=or_request_frm\"> OR test request</a>");

 $smarty->assign('sgOrRequest',"<a href=\"".$root_path."modules/or/request/op_request_pass.php".URL_APPEND."\">OR Request</a>");

# $smarty->assign('segNewORRequest',"<a href=\"".$root_path."modules/or/request/seg-op-request-new.php".URL_APPEND."&target=or_new_request\">New OR Request</a>");
 #added by VAN 02-07-08
 $smarty->assign('sNewORRequestIcon','<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
 $smarty->assign('segNewORRequest',"<a href=\"".$root_path."modules/or/request/op_request_pass.php".URL_APPEND."&target=or_new_request\">New OR Request</a>");

 #added by VAN 02-07-08
 $smarty->assign('sListORCasesIcon','<img ' . createComIcon($root_path,'bestell.gif','0') . ' align="absmiddle">');
 $smarty->assign('segListORCases',"<a href=\"".$root_path."modules/or/request/op_request_pass.php".URL_APPEND."&target=or_request_list\">OR Cases</a>");

 #added by VAN 02-07-08
 $smarty->assign('sOrLogBookIcon','<img ' . createComIcon($root_path,'task_tree.gif','0') . ' align="absmiddle">');
 #$smarty->assign('LDOrLogBook',"<a href=\"".$root_path."modules/or_logbook/op-pflege-logbuch-pass.php".URL_APPEND."\" onmouseover=\"ssm('PLog'); clearTimeout(timer)\"
	#    onmouseout=\"timer=setTimeout('hsm()',1000)\" >$LDOrLogBook</a>");
	$smarty->assign('LDOrLogBook',"<a href=\"".$root_path."modules/or_logbook/op-pflege-logbuch-pass.php".URL_APPEND."\">$LDOrLogBook</a>");
 $smarty->assign('LDOrLogBookTxt',$LDOrLogBookTxt);

	#added by VAN 02-07-08
	$smarty->assign('sORNOCQuickViewIcon','<img ' . createComIcon($root_path,'disc_unrd.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDORNOCQuickView',"<a href=\"".$root_path."modules/nursing_or/nursing-or-dienst-schnellsicht.php".URL_APPEND."\">$LDORNOC $LDQuickView</a>");
	$smarty->assign('LDQviewTxtNurse',$LDQviewTxtNurse);

	#added by VAN 02-07-08
	$smarty->assign('sORNOCSchedulerIcon','<img ' . createComIcon($root_path,'icon-date-hour.gif','0') . ' align="absmiddle">');
	#$smarty->assign('LDORNOCScheduler',"<a href=\"".$root_path."modules/nursing_or/nursing-or-main-pass.php".URL_APPEND."&retpath=menu&target=dutyplan\" onmouseover=\"ssm('PDienstplan'); clearTimeout(timer)\"
	#    onmouseout=\"timer=setTimeout('hsm()',1000)\">$LDORNOC $LDScheduler </a>");
	$smarty->assign('LDORNOCScheduler',"<a href=\"".$root_path."modules/nursing_or/nursing-or-main-pass.php".URL_APPEND."&retpath=menu&target=dutyplan\">$LDORNOC $LDScheduler </a>");
	$smarty->assign('LDDutyPlanTxt',$LDDutyPlanTxt);

	#added by VAN 02-07-08
	$smarty->assign('sOnCallDutyIcon','<img ' . createComIcon($root_path,'caldaysel.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDOnCallDuty',"<a href=\"spediens-bdienst-zeit-erfassung.php".URL_APPEND."&retpath=op&encoder=".$HTTP_COOKIE_VARS['ck_login_username'.$sid]."\">$LDOnCallDuty</a>");
	$smarty->assign('LDOnCallDutyTxt',$LDOnCallDutyTxt);

	# OR Anesthesia submenu block

	$smarty->assign('LDORAnesthesia',"<img ".createLDImgSrc($root_path,'anaes.gif','0','absmiddle')."  alt=\"$LDAna\">");

	#added by VAN 02-07-08
	$smarty->assign('sORAnaQuickViewIcon','<img ' . createComIcon($root_path,'sections.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDORAnaQuickView',"<a href=\"".$root_path."modules/nursing_or/nursing-or-dienst-schnellsicht.php".URL_APPEND."&retpath=menu&hilitedept=39\">$LDQuickView</a>");
	$smarty->assign('LDQviewTxtAna',$LDQviewTxtAna);

	#added by VAN 02-07-08
	$smarty->assign('sORAnaNOCSchedulerIcon','<img ' . createComIcon($root_path,'timeplan.png','0') . ' align="absmiddle">');
	#$smarty->assign('LDORAnaNOCScheduler',"<a href=\"".$root_path."modules/nursing_or/nursing-or-dienstplan.php".URL_APPEND."&dept_nr=39&retpath=menu\" onmouseover=\"ssm('AnaDienstplan'); clearTimeout(timer)\"
	#    onmouseout=\"timer=setTimeout('hsm()',1000)\">$LDORNOC $LDScheduler</a>");
	$smarty->assign('LDORAnaNOCScheduler',"<a href=\"".$root_path."modules/nursing_or/nursing-or-dienstplan.php".URL_APPEND."&dept_nr=39&retpath=menu\">$LDORNOC $LDScheduler</a>");

 #added by VAN 04-22-08
	#$smarty->assign('LDORServicesReportDiv',"<img ".createLDImgSrc($root_path,'statbel.gif','0','absmiddle')."  alt=\"Administration\">");
	$smarty->assign('LDORServicesReportDiv',"<img ".createLDImgSrc($root_path,'admin.gif','0','absmiddle')."  alt=\"Administration\">");
	$smarty->assign('sORServicesReportIcon','<img ' . createComIcon($root_path,'chart.gif','0') . ' align="absmiddle">');
	$smarty->assign('LDORServicesReport',"<a href=\"".$root_path."modules/or/request/seg-OR-reports.php".URL_APPEND."&retpath=menu&hilitedept=39\">OR Reports</a>");
	$smarty->assign('LDServicesReportTxt','View and print specific status reports');

	#---added by Cha, Feb 15, 2010------
	$smarty->assign('package_icon','<img src="'.$root_path.'/gui/img/common/default/folder_add.png" />');
	$smarty->assign('package_link','<a href="'.$root_path.'modules/or/request/op_request_pass.php'. URL_APPEND.'&target=packages">Packages</a>');
	$smarty->assign('package_desc',"Manage OR Packages");
	#end cha-----------------------------

 #Added by Cherry 02-15-10
 $smarty->assign('pre_operation_icon', '<img src="'.$root_path.'images/or_main_images/pre_operation.png" border="0" />');
 $smarty->assign('pre_operation_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=pre_operation">Pre-Operative OR ASU</a>');
 $smarty->assign('pre_operation_desc', 'Provide pre-operation details such as the pre-op checklist, etc');
 #end Cherry

 #Added by Cherry 05-19-10
$smarty->assign('post_operation_icon', '<img src="'.$root_path.'images/or_main_images/or_main_post_icon.png" border="0" />');
$smarty->assign('post_operation_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=post_operation">Post-Operative OR ASU</a>');
$smarty->assign('post_operation_desc', 'Provide post operative details such as the procedure performed, etc.');

 #---added by CHA, March 30, 2010---
 $smarty->assign('approve_asu_icon', '<img src="'.$root_path.'images/or_main_images/or_main_approve_icon.png" border="0"/>');
 $smarty->assign('approve_asu_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=approve_asu">Approve ASU OR Request</a>');
 $smarty->assign('approve_asu_desc', 'Approve pending OR ASU requests');

 $smarty->assign('schedule_asu_icon', '<img src="'.$root_path.'images/or_main_images/or_main_schedule_icon.png" border="0"/>');
 $smarty->assign('schedule_asu_link', '<a href="'.$root_path.'modules/or/or_main/asu_or_schedule.php'.URL_APPEND.'&target=schedule_asu">Schedule ASU Operation</a>');
 $smarty->assign('schedule_asu_desc', 'Schedule approved ASU OR request.');

 #---end CHA------------------------

 #Added by Cherry 09-13-10
 $smarty->assign('resched_asu_icon', '<img src="'.$root_path.'images/or_main_images/or_main_schedule_icon.png" border="0"/>');
 $smarty->assign('resched_asu_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_asu_resched">Re-schedule OR ASU Request</a>');
 $smarty->assign('resched_asu_desc', 'Re-schedule OR ASU approved Requests');

 #Added by Cherry 09-09-10
 $smarty->assign('list_asu_icon', '<img src="'.$root_path.'images/or_main_images/or_main_list_icon.png" border="0"/>');
 $smarty->assign('list_asu_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_asu_list">List of Pending OR ASU Request</a>');
 $smarty->assign('list_asu_desc', 'View, Edit, Cancel, any active OR ASU Request');
 #end Cherry

 #Added by Cherry 04-14-10
 $smarty->assign('asu_new_request_icon', '<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
 #$smarty->assign('asu_new_request_link', '<a href="'.$root_path.'modules/or/or_asu/or_asu_request.php'.URL_APPEND.'&target=or_asu_new_request">New OR ASU Request</a>');
 $smarty->assign('asu_new_request_link', '<a href="'.$root_path.'modules/or/or_asu/or_asu_request.php'.URL_APPEND.'&target=or_asu_request">New OR ASU Request</a>');
 $smarty->assign('asu_new_request_desc', 'Create new OR ASU request');
 #end Cherry

 #Added by Cherry 06-29-10
 $smarty->assign('main_new_request_icon', '<img ' . createComIcon($root_path,'patdata.gif','0') . ' align="absmiddle">');
 $smarty->assign('main_new_request_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_main_new_request">New OR Main Request</a>');
 $smarty->assign('main_new_request_desc', 'Create new OR Main request');

 $smarty->assign('list_main_icon', '<img src="'.$root_path.'images/or_main_images/or_main_list_icon.png" border="0"/>');
 $smarty->assign('list_main_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_main_list">List of Pending OR Main Request</a>');
 $smarty->assign('list_main_desc', 'View, Edit, Cancel, any active OR Main Request');

 $smarty->assign('schedule_main_icon', '<img src="'.$root_path.'images/or_main_images/or_main_approve_icon.png" border="0"/>');
 $smarty->assign('schedule_main_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_main_approve">Approve OR Main Request</a>');
 $smarty->assign('schedule_main_desc', 'Approve pending OR Main requests.');

 $smarty->assign('pre_operation_main_icon', '<img src="'.$root_path.'images/or_main_images/pre_operation.png" border="0" />');
 $smarty->assign('pre_operation_main_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=pre_operation_main">Pre-Operative OR Main</a>');
 $smarty->assign('pre_operation_main_desc', 'Provide pre-operation details such as the pre-op checklist, etc');

 $smarty->assign('post_operation_main_icon', '<img src="'.$root_path.'images/or_main_images/or_main_post_icon.png" border="0" />');
 $smarty->assign('post_operation_main_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=post_operation_main">Post-Operative OR Main</a>');
 $smarty->assign('post_operation_main_desc', 'Provide post operative details such as the procedure performed, etc.');

 #Added by Cherry 09-13-10
 $smarty->assign('resched_main_icon', '<img src="'.$root_path.'images/or_main_images/or_main_schedule_icon.png" border="0"/>');
 $smarty->assign('resched_main_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_main_resched">Re-schedule OR Main Request</a>');
 $smarty->assign('resched_main_desc', 'Re-schedule OR Main approved Requests');
 /*$smarty->assign('register_newborn_icon', '<img src="'.$root_path.'images/or_main_images/or_new_born.gif" border="0" />');
 $smarty->assign('register_newborn_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=register_newborn">Register New Born</a>');
 $smarty->assign('register_newborn_desc', 'Register new born data');
	*/
 $smarty->assign('register_newborn_icon','<img src="'.$root_path.'images/or_main_images/or_new_born.gif" />');
 $smarty->assign('register_newborn_link','<a href="javascript:NewbornFxn();">Register New Born</a>');
 $smarty->assign('register_newborn_desc','Register new born data');

 $smarty->assign('or_delivery_record_icon', '<img src="'.$root_path.'/gui/img/common/default/folder_add.png" border="0" />');
 $smarty->assign('or_delivery_record_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=delivery_record">OR Delivery Record</a>');
 $smarty->assign('or_delivery_record_desc', 'Provide delivery details');

 $smarty->assign('or_deaths_icon', '<img src="'.$root_path.'images/or_main_images/or_deaths.png" border="0" />');
 $smarty->assign('or_deaths_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=select_or_deaths">OR Deaths</a>');
 $smarty->assign('or_deaths_desc', 'Log any operating room deaths');
 #end Cherry

#Added by Celsy 06-26-10
$smarty->assign('sORScheduleIcon', '<img ' . createComIcon($root_path,'calendar.png','0') . ' align="absmiddle">');
$smarty->assign('LDORSchedule', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_view_calendar">OR Calendar</a>');
//'<a href="'.$root_path.'modules/or/or_main/or_view_calendar_sched.php'.URL_APPEND.'\">OR Calendar</a>');
$smarty->assign('LDScheduleTxt', 'View calendar of OR schedules');
#Added by Celsy 07-12-10
$smarty->assign('sORChecklistIcon', '<img ' . createComIcon($root_path,'layout_edit.png','0') . ' align="absmiddle">');
$smarty->assign('LDORChecklistMgr', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_checklist_manager">OR Checklist Manager</a>');
$smarty->assign('LDChecklistMgrTxt', 'Manage OR checklist items');
#end Celsy

/*Sutures Manager*/
#Added by Cherry 11-10-10
$smarty->assign('or_sutures_icon', '<img src="'.$root_path.'images/or_main_images/sutures.png" border="0" />');
$smarty->assign('or_sutures_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_sutures_manager">Sutures Manager</a>');
$smarty->assign('or_sutures_desc', 'Add, edit types/names of sutures');

/**
* OR Charges
*/
$smarty->assign('or_main_charges_icon', '<img src="'.$root_path.'images/or_main_images/or_charges.png" border="0" />');
$smarty->assign('or_main_charges_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_charges_select">OR Charges</a>');
$smarty->assign('or_main_charges_desc', 'Entry for OR charges');

/**--Added by CHA 09-02-2010--**/
$smarty->assign('or_anesthesia_mgr_icon', '<img src="'.$root_path.'/gui/img/common/default/pencil_add.png"/>');
$smarty->assign('or_anesthesia_mgr_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=anesthesia_mgr">OR Anesthesia Procedure</a>');
$smarty->assign('or_anesthesia_mgr_desc', 'Manage anesthesia procedures');
/**Cha End--**/

$smarty->assign('Sor_room_icon', '<img src="'.$root_path.'/gui/img/common/default/home2.gif"/>');
$smarty->assign('Sor_room_mgr_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=room_mgr">Room Status</a>');
$smarty->assign('Sor_room_mgr_desc', 'Update OR Room Status');

$smarty->assign('or_main_calendar_icon', '<img ' . createComIcon($root_path,'calendar.png','0') . ' align="absmiddle">');
$smarty->assign('or_main_calendar_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_main_view_calendar">OR Main Calendar</a>');
$smarty->assign('or_main_calendar_text', 'View calendar of OR Main Schedules');
$smarty->assign('or_asu_calendar_icon', '<img ' . createComIcon($root_path,'calendar.png','0') . ' align="absmiddle">');
$smarty->assign('or_asu_calendar_link', '<a href="'.$root_path.'modules/or/request/op_request_pass.php'.URL_APPEND.'&target=or_asu_view_calendar">OR ASU Calendar</a>');
$smarty->assign('or_asu_calendar_text', 'View calendar of OR ASU Schedules');

$smarty->assign('rootpath', $root_path);

# Collect div codes for  on-mouse-hover pop-up menu windows

$sTemp='';
ob_start();

	require('op-doku-onhover-div-menu.php');
	$sTemp = ob_get_contents();

ob_end_clean();

	$smarty->assign('sOnHoverMenu',$sTemp);

# Assign the submenu to the mainframe center block

 $smarty->assign('sMainBlockIncludeFile','or/submenu_or.tpl');

 /**
 * show  Mainframe Template
 */

 $smarty->display('common/mainframe.tpl');
?>