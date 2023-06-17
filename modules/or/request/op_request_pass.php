<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'global_conf/areas_allow.php');
/*
echo '<pre>';
print_r($_GET);
echo '<pre>';
echo '<br/>';
echo URL_APPEND;*/
$ptype = $_GET['ptype'];  //Added by Cherry 04-30-10
#echo 'ptype= '.$ptype;

$allowedarea=&$allow_area['op_room'];

if($retpath=='calendar_opt'){
	$append=URL_APPEND."&dept_nr=$dept_nr&retpath=$retpath&pday=$pday&pmonth=$pmonth&pyear=$pyear";
	$breakfile=$root_path."modules/calendar/calendar-options.php".URL_APPEND."&dept_nr=$dept_nr&retpath=$retpath&day=$pday&month=$pmonth&year=$pyear";
}else{
	$append=URL_APPEND;
	$breakfile=$root_path."main/op-doku.php".URL_APPEND;
}

if(!isset($dept_nr)) $dept_nr='';

switch($target)
{
	case 'or_new_request':
		$allowedarea = array("_a_1_opcreaterequest");
		$fileforward="seg-op-request-select-dept.php".URL_REDIRECT_APPEND."&target=or_new_request";
		$title="OR::Create new request";
	break;

	case 'search':$fileforward="op-pflege-logbuch-such-javastart.php".URL_REDIRECT_APPEND."&dept_nr=$dept_nr";
						$title=$LDSearch;
						break;
	case 'archiv':$fileforward="op-pflege-logbuch-arch-javastart.php".URL_REDIRECT_APPEND."&dept_nr=$dept_nr";
						$title=$LDArchive;
						break;
	case 'or_request_list':
				$fileforward="seg-op-request-list.php".URL_REDIRECT_APPEND."&dept_nr=$dept_nr&target=or_request_list";
				$target="or_request_list";
				$title=$LDNewData;
			break;

	case 'or_request_frm':
			$fileforward="seg-or-request-new.php".URL_REDIRECT_APPEND."&dept_nr=$dept_nr&target=or_request_frm";
			$target="or_request_frm";
			$title="Request New";
			break;
	//default:$fileforward="op-pflege-logbuch-javastart.php".URL_REDIRECT_APPEND."&dept_nr=$dept_nr";

		/** Omick Start
		* December 12, 2008
		* For the Main OR Request
		*/

		case 'or_main':
			//$fileforward = 'seg-or-main.php'.URL_REDIRECT_APPEND.'&dept_nr='.$dept_nr.'&target=or_main_form';
			$fileforward="seg-op-request-select-dept.php".URL_REDIRECT_APPEND."&target=or_main";
			$target = 'or_main_form';
			$title = 'OR Main';
		break;

		case 'packages':
			$allowedarea = array('_a_1_opORmain');
			$fileforward=$root_path.'modules/or/packages/list_packages.php'.URL_REDIRECT_APPEND."&target=packages";
			$title = 'Packages';
		break;

		/*case 'or_main_request':
			$allowedarea = array('_a_1_opORmain');
			 $fileforward="seg-op-request-select-dept.php".URL_REDIRECT_APPEND."&target=or_main_request";
			$title = 'New OR Main Request';
		break;

		case 'or_main_request_get':
			$allowedarea = array('_a_1_opORmain');
			$fileforward="seg-op-request-select-dept.php".URL_REDIRECT_APPEND."&target=or_main_request_get&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ptype=$ptype";
			$title = 'New OR Main Request';
		break; */
		//Added by Cherry 06-29-10
		 case 'or_main_new_request':
			$allowedarea = array('_a_l_opORmain');
			#$fileforward=$root_path.'modules/or/or_main/or_main_request.php'.URL_REDIRECT_APPEND."&target=or_main_new_request&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ptype=$ptype";
			$fileforward="seg-op-request-select-dept.php".URL_REDIRECT_APPEND."&target=or_main_new_request&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ptype=$ptype";
			$title = 'New OR Main Request';
		 break;

		 case 'or_main_new_request_get':
			$allowedarea = array('_a_l_opORmain');
			//$allowedarea = array('_a_1_opcreaterequest');
			$fileforward="seg-op-request-select-dept.php".URL_REDIRECT_APPEND."&target=or_main_new_request_get&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ptype=$ptype";
			$title = 'New OR Main Request';
		 break;
		//End Cherry

		//Added by Cherry 04-30-10
		case 'or_asu_request_get':
			$allowedarea = array('_a_l_opORmain');
			$fileforward="seg-op-request-select-dept.php".URL_REDIRECT_APPEND."&target=or_asu_request_get&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ptype=$ptype";
			$title = 'New OR ASU Request';
		break;

		case 'or_asu_request':
			$allowedarea = array('_a_l_opORmain');
			$fileforward="seg-op-request-select-dept.php".URL_REDIRECT_APPEND."&target=or_asu_request&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ptype=$ptype";
			$title = 'New OR ASU Request';
		break;

		#Added by Cherry 09-13-10
		case 'or_asu_resched':
			$allowedarea =  array('_a_1_opsked');
			$fileforward=$root_path.'modules/or/or_asu/or_asu_reschedule.php'.URL_REDIRECT_APPEND.'&target=or_asu_resched';
			$title = 'Re-schedule OR ASU Request';
		break;

		case 'or_other_charges_get':
			#$allowedarea = array('_a_1_opORmain');
			#edited by VAN 01-10-21
			$allowedarea = array('_a_1_nursingcreaterequest');
			$ward = $_GET['ward'];
			$fileforward=$root_path.'modules/or/request/or_other_charges_get.php'.URL_REDIRECT_APPEND."&target=or_other_charges_get&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ward=$ward";
			$title = 'Other Clinic Charges';
		break;
		case 'select_or_main_request':
			$fileforward='select_or_request.php'.URL_REDIRECT_APPEND.'&target=select_or_main_request';
			$title = 'New OR Main Request';
		break;
		case 'or_charges_select':
			$allowedarea = array('_a_1_opORmain');
			$fileforward='seg_or_charges_select.php'.URL_REDIRECT_APPEND.'&target=select_or_main_request';
			$title = 'OR Inpatient List for OR Charging';
		break;

		case 'or_charges':
			$allowedarea = array('_a_1_opORmain');
			$ward = $_GET['ward'];
			$fileforward=$root_path.'modules/or/request/seg_or_charges.php'.URL_REDIRECT_APPEND."&target=or_other_charges_get&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ward=$ward";
			$title = 'OR Charges';
		break;

		case 'select_or_request':
			$allowedarea = array('_a_1_opmaincharges');
			$fileforward='select_or_request.php'.URL_REDIRECT_APPEND.'&target=select_or_request';
			$title = 'OR Charges';
		break;

		case 'select_or_deaths':
			$allowedarea = array('_a_1_ordeaths');
			$fileforward=$root_path.'modules/or/or_main/select_or_deaths.php'.URL_REDIRECT_APPEND.'&target=select_or_deaths';
			$title = 'OR Deaths';
		break;

		case 'pre_operation':
			$allowedarea = array('_a_1_orpreop');
			//$fileforward=$root_path.'modules/or/or_main/pre_operation.php'.URL_REDIRECT_APPEND.'&target=pre_operation';
			#$fileforward=$root_path.'modules/or/or_main/pre_operation.php'.URL_REDIRECT_APPEND.'&target=pre_operation';
			$fileforward=$root_path.'modules/or/or_asu/or_asu_pre_operation.php'.URL_REDIRECT_APPEND.'&target=pre_operation';
						$title = 'Pre-Operative OR ASU';
		break;

		case 'pre_operation_main':
			$allowedarea = array('_a_l_orpreop');
			$fileforward=$root_path.'modules/or/or_main/pre_operation_main.php'.URL_REDIRECT_APPEND.'&target=pre_operation_main';
			$title = 'Pre-Operative OR Main';
		break;

		#Added by Cherry 07-11-10
		case 'post_operation_main':
			$allowedarea =  array('_a_1_oppost');
			#$fileforward=$root_path.'modules/or/or_main/or_main_post_list.php'.URL_REDIRECT_APPEND.'&target=or_main_post_list';
			$fileforward = $root_path.'modules/or/or_main/or_main_post_list.php'.URL_REDIRECT_APPEND.'&target=post_operation_main';
						$title = 'Post Operative OR Main';
		break;

		case 'or_main_calendar':
			$allowedarea = array('_a_1_orcalendar');
			$fileforward=$root_path.'modules/or/or_main/or_main_calendar.php'.URL_REDIRECT_APPEND.'&target=or_main_calendar';
			$title = 'Calendar of Operations';
		break;

		case 'seg-OR-reports':
			$allowedarea = array('_a_1_opreport');
			//$fileforward=$root_path.'modules/or/request/seg-OR-reports.php'.URL_REDIRECT_APPEND.'&target=seg-OR-reports';
			$fileforward=$root_path.'modules/or/request/seg_or_reports.php'.URL_REDIRECT_APPEND.'&target=seg-OR-reports';
			$title = 'OR Reports';
		break;

		 case 'or_main_list':
			$allowedarea =  array('_a_1_oppending');
			$fileforward=$root_path.'modules/or/or_main/or_main_list.php'.URL_REDIRECT_APPEND.'&target=or_main_list';
			$title = 'List of Pending OR Main Request';
		break;

		case 'or_asu_list':
			$allowedarea =  array('_a_1_oppending');
			$fileforward=$root_path.'modules/or/or_asu/or_asu_list.php'.URL_REDIRECT_APPEND.'&target=or_asu_list';
			$title = 'List of Pending OR ASU Request';
		break;

		#--modified by CHA, April 6, 2010
		case 'approve_asu':
			$allowedarea =  array('_a_1_opapprove');
			#$fileforward=$root_path.'modules/or/or_main/asu_or_approve.php'.URL_REDIRECT_APPEND.'&target=or_main_approve';
			$fileforward=$root_path.'modules/or/or_asu/or_asu_approve.php'.URL_REDIRECT_APPEND.'&target=approve_asu';
			$title = 'Approve Active ASU OR Request';
		break;
		#--end Cha--

		case 'or_main_approve':
			$allowedarea =  array('_a_1_opsked');
			$fileforward=$root_path.'modules/or/or_main/or_main_approve.php'.URL_REDIRECT_APPEND.'&target=or_main_approve';
			$title = 'Approve OR Main Request';
		break;

		#Added by Cherry 11-10-10
		case 'or_sutures_manager':
			$allowedarea =  array('_a_1_opORmain');
			$fileforward=$root_path.'modules/or/or_main/or_sutures_mgr.php'.URL_REDIRECT_APPEND.'&target=or_sutures';
			$title = 'OR :: Sutures';
		break;

		#Added by Cherry 09-13-10
		case 'or_main_resched':
			$allowedarea =  array('_a_1_opsked');
			$fileforward=$root_path.'modules/or/or_main/or_main_reschedule.php'.URL_REDIRECT_APPEND.'&target=or_main_resched';
			$title = 'Re-schedule OR Main Request';
		break;

		#--added by Celsy June 28, 2010
		case 'or_view_calendar':
			$allowedarea =  array('_a_1_opORmain');
			$fileforward=$root_path.'modules/or/or_main/or_view_schedule.php'.URL_REDIRECT_APPEND.'&target=or_view_calendar';
			$title = 'OR :: Schedule Viewer';
		break;

		#--added by Celsy August 9, 2010  since na overwrite xa
		case 'or_checklist_manager':
			$allowedarea =  array('_a_1_opORmain');
			$fileforward=$root_path.'modules/or/or_main/or_checklist_items_mgr.php'.URL_REDIRECT_APPEND.'&target=or_checklist_manager';
			$title = 'OR :: Schedule Viewer';
		break;

		#--end Celsy

				#edited by Cherry May 20, 2010
		case 'post_operation':
			$allowedarea =  array('_a_1_oppost');
			#$fileforward=$root_path.'modules/or/or_main/or_main_post_list.php'.URL_REDIRECT_APPEND.'&target=or_main_post_list';
			$fileforward = $root_path.'modules/or/or_asu/or_asu_post_list.php'.URL_REDIRECT_APPEND.'&target=post_operation';
						$title = 'Post Operative OR Main';
		break;

		case 'or_main_cases':
			$allowedarea =  array('_a_1_oppending');
			$fileforward=$root_path.'modules/or/or_main/or_main_cases.php'.URL_REDIRECT_APPEND.'&target=or_main_cases';
			$title = 'List of OR Main Cases';
		break;

		/** Omick End */

	 /*added by cha 06-08-09*/
	 case 'select_or_schedule':
			$allowedarea =  array('_a_1_opsked');
			$fileforward=$root_path.'modules/or/or_main/or_main_view_schedule.php'.URL_REDIRECT_APPEND.'&target=or_main_view_schedule';
			$title = 'OR Schedule';
	 break;
	 /*end cha*/

	 /**-- added by cha 10-07-09 --**/
//		case 'new_delivery_record':
		case 'delivery_record':
				$allowedarea =  array('_a_1_opdelivery');
				$fileforward=$root_path.'modules/or/or_main/or_delivery_record.php'.URL_REDIRECT_APPEND.'&target=new_delivery_record';
				$title = 'OR Delivery Details';
		 break;
	 /**-- end cha --**/

	case 'select_or_main_request':
			$fileforward='select_or_request.php'.URL_REDIRECT_APPEND.'&target=select_or_main_request';
			$title = 'New OR Main Request';
	break;

	#----added by CHA, Aug. 18, 2010----------------
	case 'clinic_charges':
			//$title="Clinics::Prescription Writer";
			//$breakfile="labor.php".URL_APPEND;
			$allowedarea = array('_a_1_nursingcreaterequest','_a_1_addcharges','_a_1_labcreaterequest','_a_1_bloodcreaterequest','_a_1_splabcreaterequest','_a_1_radiocreaterequest');
			$fileforward=$root_path."modules/clinics/seg-clinic-charges.php".URL_REDIRECT_APPEND."&target=or_other_charges_get&ptype=$ptype&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&area_type=$area_type&dr_nr=$dr_nr&dept_nr=$dept_nr&is_dr=$is_dr&area_type=$area_type";
			break;
	#----end CHA------------------------------------

	/***added by cha September 2, 2010***/
		 case 'anesthesia_mgr':
					$allowedarea = array('_a_1_opORmain');
					$fileforward = $root_path.'modules/or/anesthesia_mgr/anesthesia_proc_mgr.php'.URL_REDIRECT_APPEND.'&target=anesthesia_procedure';
					$title = "OR Anesthesia Procedures";
		 break;
		 /***end cha***/

	case 'room_mgr':
					$allowedarea = array('_a_1_opORmain');
					$fileforward = $root_path.'modules/or/request/seg-or-room-mgr.php'.URL_REDIRECT_APPEND.'&target=anesthesia_procedure';
					$title = "OR Anesthesia Procedures";
		 break;

	case 'or_main_view_calendar':
			$allowedarea =  array('_a_1_ormaincalendar');
			$fileforward=$root_path.'modules/or/or_main/or_view_schedule.php'.URL_REDIRECT_APPEND.'&target=or_main_view_calendar';
			$title = 'OR Main :: Schedule Viewer';
		break;
	case 'or_asu_view_calendar':
			$allowedarea =  array('_a_1_orasucalendar');
			$fileforward=$root_path.'modules/or/or_main/or_view_schedule.php'.URL_REDIRECT_APPEND.'&target=or_asu_view_calendar';
			$title = 'OR ASU :: Schedule Viewer';
		break;

	default:$fileforward="op_request_gui.php".URL_REDIRECT_APPEND."&dept_nr=$dept_nr";
				$target="entry";
				$title=$LDNewData;
}

#echo "op_request_pass.php : fileforward = '".$fileforward."' <br> \n";
#echo "op_request_pass.php : HTTP_SESSION_VARS : "; print_r($HTTP_SESSION_VARS); echo " <br><br> \n";
$thisfile=basename(__FILE__);

$lognote="OP Logs $title ok";

$userck='ck_op_pflegelogbuch_user';
//reset cookie;
// reset all 2nd level lock cookies
//setcookie($userck.$sid,'');
//require($root_path.'include/inc_2level_reset.php');
//setcookie(ck_2level_sid.$sid,'');

require($root_path.'include/inc_passcheck_internchk.php');

#echo "op_request_pass.php : 1 pass = '".$pass."' <br> \n";

if ($pass=='check')
	include($root_path.'include/inc_passcheck.php');

#echo "op_request_pass.php : 2 pass = '".$pass."' <br> \n";

$errbuf="OP Logs $title";

require($root_path.'include/inc_passcheck_head.php');
?>

<BODY <?php if (!$nofocus)
				{ echo 'onLoad="document.passwindow.userid.focus();';
					if($retpath=="calendar_opt") echo "window.resizeTo(800,600);window.moveTo(20,20);";
					echo '"';
				}
				echo  ' bgcolor='.$cfg['body_bgcolor'];
				if (!$cfg['dhtml']){ echo ' link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; }
?>>
<p>
<FONT    SIZE=-1  FACE="Arial">

<P>
<img <?php echo createComIcon($root_path,'people.gif','0','absmiddle') ?>>
<FONT  COLOR="<?php echo $cfg[top_txtcolor] ?>"  SIZE=5  FACE="verdana" > <b><?php echo "$title" ?></b></font>

<table width=100% border=0 cellpadding="0" cellspacing="0">
<tr>
<td colspan=3><?php if($target=="entry") echo '<img '.createLDImgSrc($root_path,'newdata-b.gif','0').' alt="'.$LDAdmit.'">';
								else{ echo'<a href="'.$thisfile.$append.'&target=entry"><img '.createLDImgSrc($root_path,'newdata-gray.gif','0').'  alt="'.$LDAdmit.'" ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
							if($target=="search") echo '<img '.createLDImgSrc($root_path,'such-b.gif','0').' alt="'.$LDSearch.'">';
								else{ echo '<a href="'.$thisfile.$append.'&target=search"><img '.createLDImgSrc($root_path,'such-gray.gif','0').' alt="'.$LDSearch.'"  ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
							if($target=="archiv") echo '<img '.createLDImgSrc($root_path,'arch-blu.gif','0').' alt="'.$LDArchive.'">';
								else{ echo '<a href="'.$thisfile.$append.'&target=archiv"><img '.createLDImgSrc($root_path,'arch-gray.gif','0').' alt="'.$LDArchive.'"  ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
						?></td>
</tr>
<?php require($root_path.'include/inc_passcheck_mask.php') ?>
<p>
<!-- <img src="../img/small_help.gif"> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDIntro2 $LDOrLogBook" ?></a><br>
<img src="../img/small_help.gif"> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDWhat2Do $LDOrLogBook" ?></a><br>

<p>
 -->
<?php
require($root_path.'include/inc_load_copyrite.php');
?>
</FONT>
</BODY>
</HTML>
</BODY>
</HTML>
