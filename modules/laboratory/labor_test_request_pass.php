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
$lang_tables=array('departments.php');
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

/*if ($pdaten=="ja") setcookie(pdatencookie,"ja");*/

require_once($root_path.'global_conf/areas_allow.php');

/* Set the allowed area basing on the target */
#if($target=='admin') $allowedarea=&$allow_area['test_receive'];
# else $allowedarea=&$allow_area['test_order'];

#echo "labor_test_request_pass.php : 1 target = '".$target."' <br> \n";
#echo "labor_test_request_pass.php : 1 allowedarea = "; print_r($allowedarea); echo " <br> \n";

if(!isset($target)||!$target) $target='chemlabor';

# Set the origin
if(!isset($user_origin)||empty($user_origin)) $user_origin='lab';

/* Set the default file forward */
$fileforward=$root_path."modules/nursing/nursing-station-patientdaten-doconsil-".$target.".php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target;

$thisfile='labor_test_request_pass.php';

# Set the breakfile
switch($user_origin){
	case 'lab':$breakfile="labor.php".URL_APPEND; break;
	case 'amb': $breakfile=$root_path."modules/ambulatory/ambulatory.php".URL_APPEND; break;
}

$test_pass_logo='micros.gif';

$userck='ck_lab_user';


# If target is generic, Filter the cheblab, patho, bactlab,bloodbank and radiology tests
if($target=='generic'){
	switch($subtarget){
		case 8 : $target='admin'; $subtarget='patho';break; # 8 = pathology
		case 19: $target='admin'; $subtarget='radio'; break; # 19 = radiology
		case 22: $target='admin'; $subtarget='chemlabor'; break; # 22 = central lab
		case 23: $target='admin'; $subtarget='chemlabor'; break; # 23 = serological lab
		case 24: $target='admin'; $subtarget='chemlabor'; break; # 24 = chemical lab
		case 25: $target='admin'; $subtarget='baclabor'; break; # 25 = bacteriological lab
		case 41: $target='admin'; $subtarget='blood';  break; # 41 = blood bank
	}
}

#added by VAN 07-02-08
#echo "done = ".$_GET['done'];
$done = $_GET['done'];
#----------------------

//echo "$target $subtarget";
#seglabservrequest
# Refilter
#echo "target = ".$target;
switch($target)
{
	case 'seglabservadmin':
			$allowedarea=array("_a_1_labadmin");
			$title="Laboratory - Laboratory Services";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-services-admin.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			#$fileforward="seg-labservices-manage.php".URL_APPEND;
			#echo "fileforward = $fileforward";
			break;
	/*
	case 'seglabservadminOLD':
			$allowedarea=array("_a_1_labadmin");
			$title="Laboratory - Laboratory Services";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-services-admin-031208.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			#$fileforward="seg-labservices-manage.php".URL_APPEND;
			#echo "fileforward = $fileforward";
			break;
	*/
	#added by VAN 03-10-08
	case 'seglabservgroup':
			$allowedarea=array("_a_1_labadmin");
			$title="Laboratory - Laboratory Service Group";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-services-groups-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

		case 'seglabreagents':
						$allowedarea=array("_a_1_labadmin");
						$title="Laboratory - Laboratory Service Reagents";
						$breakfile="labor.php".URL_APPEND;
						$fileforward="seg-lab-services-reagents-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;

	case 'seglabinventory':
						$allowedarea=array("_a_1_labreagentinventory");
						$title="Laboratory - Reagents Inventory";
						$breakfile="labor.php".URL_APPEND;
						#$fileforward="seg-lab-reagent-inventory.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						#edited by VAN 06-10-09
						//$fileforward="seg-lab-reagent-adjustment.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						#edited by BRYAN 01-05-10
						$fileforward=$root_path."modules/supply_office/seg-inventory-adjustment.php".URL_REDIRECT_APPEND."&target=adjustment&from=".$src;
						break;

	#added by VAN
	case 'seglabservrequest':
			$title="Laboratory - Laboratory Services Request";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-request-list.inc.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			#$fileforward="seg-lab-services-requestor.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			#$fileforward="seg-labservices-manage.php".URL_APPEND;
			#echo "fileforward = $fileforward";
			break;

	#--------added by VAN 11-08-07-------------
	case 'seglabOrder':
			$allowedarea=array("_a_1_labmanageundone","_a_1_labresultswrite");
			#edited by VAN 07-02-08
			if ($done)
				$title="Laboratory - Done Laboratory Services Request";
			else
				$title="Laboratory - Undone Laboratory Services Request";
			#$title="Laboratory - Undone and Done Laboratory Services Request";
			$breakfile="labor.php".URL_APPEND;
			#$fileforward="seg-lab-request-order-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			$fileforward="seg-lab-request-order-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&done=".$done;
			break;

	case 'seglabreports':
			$allowedarea=array("_a_1_labreports");
			$title="Laboratory - Laboratory Status Reports";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-reports.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;
	#--------------------------------------------
    
    #added by VAN 07-16-2013
    case 'seglabResult':
            $allowedarea=array("_a_1_labmanageundone","_a_1_labresultswrite","_a_2_labresultsread");
            $title="Laboratory Results";
            
            $breakfile="labor.php".URL_APPEND;
            $fileforward="seg-lab-result-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
            break;
    #------------------------

	#added by VAN 05-11-2010
	case 'charges':
			$allowedarea=array("_a_1_labcharges");
			$title="Laboratory - Other Clinic Charges";
			$breakfile="labor.php".URL_APPEND;
			$ward = $_GET['ward'];
			#$fileforward="seg-lab-other-charges.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			$fileforward=$root_path.'modules/laboratory/seg-lab-other-charges.php'.URL_REDIRECT_APPEND."&target=charges&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ward=$ward&from_or=1";
			break;
	#-----------------------

	#added by CHA, May 11,2010
	case 'lab_other_charges':
		 $allowedarea = array('_a_1_opORmain','_a_1_opdpatientmanage','_a_1_erpatientmanage','_a_1_ipdpatientmanage','_a_1_phspatientmanage','_a_1_labcharges','_a_1_opcharges','_a_1_opmaincharges');
		 $ward = $_GET['ward'];
		 $fileforward=$root_path.'modules/or/request/or_other_charges_get_or.php'.URL_REDIRECT_APPEND."&target=or_other_charges_get&pid=$pid&encounter_nr=$encounter_nr&popUp=$popUp&ward=$ward&from_lab=1";
		 $title = 'Laboratory::Other Clinic Charges';
		 break;
	#-------------------------

	#added by VAN 05-24-2010
	case 'samples':
			$allowedarea=array("_a_1_labmanagerequest");
			$title="Laboratory - Laboratory Services Request";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-request-new-list.php".URL_REDIRECT_APPEND."&samplelist=1&user_origin=".$user_origin;
			break;
	#-----------------------

	#added by VAN 09-29-2010
	case 'serve':
			$allowedarea=array("_a_1_labmanagerequest");
			$title="Laboratory - Laboratory Services Request";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-request-post-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

	case 'seglabservrequest_new':
			$allowedarea=array("_a_1_labmanagerequest");
			$title="Laboratory - Laboratory Services Request";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-request-new-list.php".URL_REDIRECT_APPEND."&samplelist=0&user_origin=".$user_origin;
			break;
	case 'seglabtest':
						$allowedarea=array("_a_1_labtest");
						$title="Laboratory - Laboratory Tests";
						$breakfile="labor.php".URL_APPEND;
						//$fileforward="labor_test.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						$fileforward="test_manager/seg_lab_test_main.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;
	case 'seglab':
			$title=$LDTestRequest." - Central Laboratory";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="seg-lab-test-request.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;
		case 'seglabnew':
			$allowedarea=array("_a_1_labcreaterequest");
			$title=$LDTestRequest." - Central Laboratory";
			$breakfile="labor.php".URL_APPEND;

			if (!$_GET['popUp'])
				 $HTTP_SESSION_VARS['url'] = "";

			$fileforward="seg-lab-request-new.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&popUp=".$_GET['popUp'].$HTTP_SESSION_VARS['url'];
			break;
		case 'blood':
				$allowedarea=array("_a_1_bloodcreaterequest");
				$title=$LDBloodOrder;
				$breakfile="labor.php".URL_APPEND;
				if (!$_GET['popUp'])
				 $HTTP_SESSION_VARS['url'] = "";

			$fileforward="../../modules/bloodBank/seg-blood-request-new.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&popUp=".$_GET['popUp'].$HTTP_SESSION_VARS['url'];
			break;

		case 'blood_list':
			$allowedarea=array("_a_1_bloodmanagerequest");
			$title="Laboratory - Blood Services Request";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="../../modules/bloodBank/seg-blood-request-new-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

		case 'promissory_note':
			$allowedarea=array("_a_1_bloodpromissory");
			$title="Laboratory - Blood Services Request";
			$breakfile="labor.php".URL_APPEND;
			$fileforward="../../modules/bloodBank/seg-blood-promissory-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
		break;

		case 'blood_donor':
				$title="Laboratory - Blood Donor Registration";
				$breakfile="labor.php".URL_APPEND;
				$fileforward="../../modules/bloodBank/seg_blood_donor_register.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
		break;

	case 'blood_result':
			$allowedarea=array("_a_1_bloodmanageundone","_a_1_labresultswrite");
			#edited by VAN 07-02-08
			if ($done)
				$title="Laboratory - Done Blood Services Request";
			else
				$title="Laboratory - Undone Blood Services Request";
			#$title="Laboratory - Undone and Done Laboratory Services Request";
			$breakfile="labor.php".URL_APPEND;
			#$fileforward="seg-lab-request-order-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			$fileforward="../../modules/bloodBank/seg-blood-request-order-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&done=".$done;
			break;

		# burn added : July 18, 2007
	case 'segradioDOCScheduler':
			$title="Radiology - Scheduler";
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
			$allowedarea = array('_a_1_radiodoctorschedule');
				# NOTE: radiology dept nr in care_department table is 158
			$fileforward=$root_path."modules/radiology/radiologists-dienstplan-planen.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&dept_nr=158&retpath=menu&pmonth=".$pmonth."&pyear=".$pyear;
#echo"labor_test_request_pass.php : inside case : _GET : <br> \n"; print_r($_GET); echo" <br> \n";
#echo "labor_test_request_pass.php : URL_APPEND = '".URL_APPEND."' <br> \n";
#echo "labor_test_request_pass.php : URL_REDIRECT_APPEND = '".URL_REDIRECT_APPEND."' <br> \n";
#echo "labor_test_request_pass.php : fileforward = '".$fileforward."' <br> \n";
			break;

	#added by VAN 07-10-07
	case 'segradioservice':
			$title="Radiology - Radiology Services";
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
			$userck='ck_radio_user';   # burn added : October 24, 2007
			$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
			$fileforward=$root_path."modules/radiology/seg-radio-services-admin.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
		break;

	#added by VAN 03-15-08
	case 'segradioserviceOLD':
			$title="Radiology - Radiology Services";
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
			$userck='ck_radio_user';   # burn added : October 24, 2007
			$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
			$fileforward=$root_path."modules/radiology/seg-radio-services-admin031508.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
		break;

	#added by VAN 03-17-08
	case 'segradiogroup':
			$title="Radiology - Radiology Service Group";
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$userck='ck_radio_user';
			$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
			$fileforward=$root_path."modules/radiology/seg-radio-services-groups-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

	#added by VAN 07-07-08
	case 'segradiofindings':
			$title="Radiology - Radiology Finding's Code";
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$userck='ck_radio_user';
			$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
			$fileforward=$root_path."modules/radiology/seg-radio-services-findings-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

	case 'segradioimpression':
			$title="Radiology - Radiology Impression's Code";
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$userck='ck_radio_user';
			$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
			$fileforward=$root_path."modules/radiology/seg-radio-services-impression-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

	case 'segradioreader':
			$title="Radiology - Radiology Doctor Partner's Reader";
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$userck='ck_radio_user';
			$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
			$fileforward=$root_path."modules/radiology/seg-radio-services-doctors-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;
	#--------------------------

	case 'segradiotech':
			$title="Radiology - Radiologic Technologist";
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$userck='ck_radio_user';
			$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
			$fileforward=$root_path."modules/radiology/seg-radio-services-findings-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;
	#------------------

	case 'radiorequestlist':
			$title="Radiology - List of Radiology request";
			$allowedarea = array('_a_1_radiomanagerequest');
			$userck='ck_radio_user';   # burn added : September 24, 2007
			#$allowedarea=&$allow_area['radio'];
			$breakfile = $root_path."modules/radiology/radiolog.php".URL_APPEND;
			$fileforward = $root_path."modules/radiology/seg-radio-request-new-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."target=".$target."&dept_nr=".$dept_nr;
	break;
	case 'radio_cal':
			$title="Scheduling - Radiology";
			$userck='ck_radio_user';   # burn added : September 24, 2007
			$allowedarea=array('_a_1_radioschedule');
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
				 /* Set the default file forward */
			$fileforward=$root_path."modules/radiology/seg-radio-schedule-calendar.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
		break;

	#added by VAN 03-26-08
	case 'radio_cal_list':
			$title="Scheduled Request - Radiology";
			$userck='ck_radio_user';   # burn added : September 24, 2007
			$allowedarea=array('_a_1_radioschedule');
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
				 /* Set the default file forward */
			$fileforward=$root_path."modules/radiology/seg-radio-scheduled-request-list.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
		break;

	#added by VAN 04-21-08
	case 'segradioreports':
			$userck='ck_radio_user';
			$title="Radiology - Radiology Status Reports";
			$allowedarea=array("_a_1_radioreports");
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$fileforward=$root_path."modules/radiology/seg-radio-reports.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

	case 'radio':
				$title=$LDTestRequest." - ".$LDTestType[$target];
				$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
		break;
	case 'radio_undone':
			$title="Undone Requests - Radiology";
			$userck='ck_radio_user';   # burn added : September 24, 2007
			$allowedarea=array('_a_1_radioundone');
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
				 /* Set the default file forward */
			$fileforward=$root_path."modules/radiology/radiology_undone_request.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
		break;
	case 'radio_done':
			$title="Archive - Radiology";
			$userck='ck_radio_user';   # burn added : September 24, 2007
			$allowedarea=array('_a_1_radiodone');
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
				 /* Set the default file forward */
			$fileforward=$root_path."modules/radiology/radiology_done_request.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
						break;
	case 'radio_unified':
						$title="Unified Results - Radiology";
						$userck='ck_radio_user';
						$allowedarea=array('_a_1_radiounified');
						$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
						$fileforward=$root_path."modules/radiology/radiology_unified_results.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
						break;
	case 'radio_patient':     # burn added : October 18, 2007
			$title="Patient List - Radiology";
			$userck='ck_radio_user';
			$allowedarea=array('_a_1_radioborrow');
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
				 /* Set the default file forward */
			$fileforward=$root_path."modules/radiology/seg-radio-patient-list.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
		break;
	case 'radio_borrow':     # burn added : October 18, 2007
			$title="Borrowing System - Radiology";
			$userck='ck_radio_user';
			$allowedarea=array('_a_1_radioborrow');
			$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
			$test_pass_logo='thorax_sm.jpg';
				 /* Set the default file forward */
			$fileforward=$root_path."modules/radiology/seg-radio-borrowers-list.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
		break;

	#added by VAN 08-17-2009
	case 'segradiofilm':
						$userck='ck_radio_user';
						$title="Radiology - Film Encoding";
						$allowedarea=array("_a_1_radioinventory");
						$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
						$fileforward=$root_path."modules/radiology/seg-radio-reports.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;

	case 'segradioinventory':
						$userck='ck_radio_user';
						$title="Radiology - Inventory";
						$allowedarea=array("_a_1_radioinventory");
						$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
						$fileforward=$root_path."modules/radiology/seg-radio-reports.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;

	case 'segradioadjust':
						$userck='ck_radio_user';
						$title="Radiology - Adjust Inventory";
						$allowedarea=array("_a_1_radioadjust");
						$breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
						//$fileforward=$root_path."modules/radiology/seg-radio-item-adjustment.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						$fileforward=$root_path."modules/supply_office/seg-inventory-adjustment.php".URL_REDIRECT_APPEND."&target=adjustment&from=".$src;
			break;
	#------------------
	//temp added by mark on Aug 24 2007
	case 'radio_test':
				$title="test Radio request form - Radiology";
			$userck='ck_radio_user';   # burn added : September 24, 2007
			$allowedarea=array('_a_1_radiocreaterequest');
			$breakfile = $root_path."modules/radiology/radiolog.php".URL_APPEND;
			#$fileforward = $root_path."modules/nursing/nursing-station-radio-request-new.php".URL_REDIRECT_APPEND."norezie=1&use_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
			#$fileforward = $root_path."modules/radiology/seg-radio-request-new.php".URL_REDIRECT_APPEND."norezie=1&use_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
			#edited by VAN 01-11-08
			if (!$_GET['popUp'])
				 $HTTP_SESSION_VARS['url'] = "";
			#$fileforward = $root_path."modules/radiology/seg-radio-request-new.php".URL_REDIRECT_APPEND."norezie=1&use_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr."&repeat=".$_GET[repeat]."&prevbatchnr=".$_GET['prevbatchnr']."&prevrefno=".$_GET['prevrefno'];
			$fileforward = $root_path."modules/radiology/seg-radio-request-new.php".URL_REDIRECT_APPEND."&noresize=1&use_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr."&repeat=".$_GET[repeat]."&prevbatchnr=".$_GET['prevbatchnr']."&prevrefno=".$_GET['prevrefno']."&repeaterror=".$_GET['repeaterror']."&popUp=".$_GET['popUp'].$HTTP_SESSION_VARS['url'];
				break;
	case 'admin':
				$title=$LDPendingRequest." - ".$LDTestType[$subtarget];
						if($subtarget=='radio'){
				 $breakfile=$root_path."modules/radiology/radiolog.php".URL_APPEND;
				 $test_pass_logo="thorax_sm.jpg";
			}
						$fileforward="labor_test_request_admin_".$subtarget.".php".URL_REDIRECT_APPEND."&target=".$target."&subtarget=".$subtarget."&noresize=1&&user_origin=".$user_origin;
			break;
	case 'generic':
						include_once($root_path.'include/care_api_classes/class_department.php');
			$dept_obj=new Department;
			if($dept_obj->preloadDept($subtarget)){
				 $buffer=$dept_obj->LDvar();
				 if(isset($$buffer)&&!empty($$buffer))
						$title=$LDPendingRequest." - ".$$buffer;
				 else
						$title=$LDPendingRequest." - ".$dept_obj->FormalName();
			}
						$fileforward="labor_test_request_admin_generic.php".URL_REDIRECT_APPEND."&target=".$target."&subtarget=".$subtarget."&noresize=1&&user_origin=".$user_origin;
			if($user_origin=='amb'){
				 $userck='ck_amb_user';
				 $breakfile=$root_path.'modules/ambulatory/ambulatory.php'.URL_APPEND;
			} else {
				 $userck='ck_lab_user';
							 $breakfile=$root_path."modules/doctors/doctors.php".URL_APPEND;
						}
						break;
	case 'seghemoDOCScheduler':
						$title="Hemodialysis - Scheduler";
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
						$allowedarea = array('_a_1_radiodoctorschedule');
								# NOTE: radiology dept nr in care_department table is 158
						$fileforward=$root_path."modules/hemodialysis/radiologists-dienstplan-planen.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&dept_nr=158&retpath=menu&pmonth=".$pmonth."&pyear=".$pyear;
#echo"labor_test_request_pass.php : inside case : _GET : <br> \n"; print_r($_GET); echo" <br> \n";
#echo "labor_test_request_pass.php : URL_APPEND = '".URL_APPEND."' <br> \n";
#echo "labor_test_request_pass.php : URL_REDIRECT_APPEND = '".URL_REDIRECT_APPEND."' <br> \n";
#echo "labor_test_request_pass.php : fileforward = '".$fileforward."' <br> \n";
						break;

	#added by VAN 07-10-07
	case 'seghemoservice':
						$title="Hemodialysis - Hemodialysis Services";
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
						$userck='ck_radio_user';   # burn added : October 24, 2007
						$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
						$fileforward=$root_path."modules/hemodialysis/seg-radio-services-admin.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
				break;

		#added by VAN 03-15-08
		case 'seghemoserviceOLD':
						$title="Hemodialysis - Hemodialysis Services";
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
						$userck='ck_radio_user';   # burn added : October 24, 2007
						$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
						$fileforward=$root_path."modules/hemodialysis/seg-radio-services-admin031508.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
				break;

		#added by VAN 03-17-08
		case 'seghemogroup':
						$title="Hemodialysis - Hemodialysis Service Group";
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$userck='ck_radio_user';
						$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
						$fileforward=$root_path."modules/hemodialysis/seg-radio-services-groups-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;

		#added by VAN 07-07-08
		case 'seghemofindings':
						$title="Hemodialysis - Hemodialysis Finding's Code";
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$userck='ck_radio_user';
						$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
						$fileforward=$root_path."modules/hemodialysis/seg-radio-services-findings-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;

		case 'seghemoimpression':
						$title="Hemodialysis - Hemodialysis Impression's Code";
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$userck='ck_radio_user';
						$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
						$fileforward=$root_path."modules/hemodialysis/seg-radio-services-impression-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;

		case 'seghemoreader':
						$title="Hemodialysis - Hemodialysis Doctor Partner's Reader";
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$userck='ck_radio_user';
						$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
						$fileforward=$root_path."modules/hemodialysis/seg-radio-services-doctors-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;
		#--------------------------

		case 'seghemotech':
						$title="Hemodialysis - Technologist";
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$userck='ck_radio_user';
						$allowedarea=array('_a_1_radioadmin');   # burn added : October 24, 2007
						$fileforward=$root_path."modules/hemodialysis/seg-radio-services-findings-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;
		#------------------

	case 'hemorequestlist':
						 $title="Hemodialysis - List of Hemodialysis request";
						$allowedarea = array('_a_1_radiomanagerequest');
						$userck='ck_radio_user';   # burn added : September 24, 2007
						#$allowedarea=&$allow_area['radio'];
						 $breakfile = $root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						 $fileforward = $root_path."modules/hemodialysis/seg-radio-request-new-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."target=".$target."&dept_nr=".$dept_nr;
		break;
	case 'hemo_cal':
						$title="Hemodialysis - Scheduling";
						$userck='ck_radio_user';   # burn added : September 24, 2007
						$allowedarea=array('_a_1_radioschedule');
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
							 /* Set the default file forward */
						$fileforward=$root_path."modules/hemodialysis/seg-radio-schedule-calendar.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
				break;

		#added by VAN 03-26-08
		case 'hemo_cal_list':
						$title="Hemodialysis - Scheduled Request";
						$userck='ck_radio_user';   # burn added : September 24, 2007
						$allowedarea=array('_a_1_radioschedule');
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
							 /* Set the default file forward */
						$fileforward=$root_path."modules/hemodialysis/seg-radio-scheduled-request-list.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
				break;

	#added by VAN 04-21-08
	case 'seghemoreports':
						$userck='ck_radio_user';
						$title="Hemodialysis - Hemodialysis Status Reports";
						$allowedarea=array("_a_1_radioreports");
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$fileforward=$root_path."modules/hemodialysis/seg-radio-reports.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
						break;

	case 'hemo':
							$title=$LDTestRequest." - ".$LDTestType[$target];
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
				break;
	case 'hemo_undone':
						$title="Hemodialysis - Undone Requests";
						$userck='ck_radio_user';   # burn added : September 24, 2007
						$allowedarea=array('_a_1_radioundone');
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
							 /* Set the default file forward */
						$fileforward=$root_path."modules/hemodialysis/radiology_undone_request.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
				break;
	case 'hemo_done':
						$title="Hemodialysis - Archive";
						$userck='ck_radio_user';   # burn added : September 24, 2007
						$allowedarea=array('_a_1_radiodone');
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
							 /* Set the default file forward */
						$fileforward=$root_path."modules/hemodialysis/radiology_done_request.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
						break;
	case 'hemo_unified':
						$title="Hemodialysis - Unified Results";
						$userck='ck_radio_user';
						$allowedarea=array('_a_1_radiounified');
						$breakfile=$root_path."modules/hemodialysis/radiolog.php".URL_APPEND;
						$test_pass_logo='thorax_sm.jpg';
						$fileforward=$root_path."modules/hemodialysis/radiology_unified_results.php".URL_REDIRECT_APPEND."&noresize=1&user_origin=".$user_origin."&target=".$target."&dept_nr=".$dept_nr;
						break;

	#added by VAN 07-23-2010
	case 'specialLab':
				$allowedarea=array("_a_1_splabcreaterequest");
				$title='Special Laboratory :: New Request';
				$breakfile="labor.php".URL_APPEND;
				if (!$_GET['popUp'])
				 $HTTP_SESSION_VARS['url'] = "";

			$fileforward="../../modules/special_lab/seg-splab-request-new.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&popUp=".$_GET['popUp'].$HTTP_SESSION_VARS['url'];
			break;

		case 'specialLab_list':
			$allowedarea=array("_a_1_splabmanagerequest");
			$title='Special Laboratory :: Request List';
			$breakfile="labor.php".URL_APPEND;
			$fileforward="../../modules/special_lab/seg-splab-request-new-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

		case 'specialLab_result':
			$allowedarea=array("_a_1_splabmanageundone","_a_1_splabresultswrite");
			if ($done)
				$title="Special Laboratory :: Done Services Request";
			else
				$title="Special Laboratory :: Undone Services Request";

			$breakfile="labor.php".URL_APPEND;
			$fileforward="../../modules/special_lab/seg-splab-request-order-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&done=".$done;
			break;
	#--------------------

	#added by VAN 08-31-2010
	case 'ICLab':
				$allowedarea=array("_a_1_iclabcreaterequest");
				$title='Industrial Clinic Laboratory :: New Request';
				$breakfile="labor.php".URL_APPEND;
				if (!$_GET['popUp'])
				 $HTTP_SESSION_VARS['url'] = "";

			$fileforward="../../modules/ic_lab/seg-iclab-request-new.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&popUp=".$_GET['popUp'].$HTTP_SESSION_VARS['url'];
			break;

		case 'ICLab_list':
			$allowedarea=array("_a_1_iclabmanagerequest");
			$title='Industrial Clinic Laboratory :: Request List';
			$breakfile="labor.php".URL_APPEND;
			$fileforward="../../modules/ic_lab/seg-iclab-request-new-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;
			break;

		case 'ICLab_result':
			$allowedarea=array("_a_1_iclabmanageundone","_a_1_iclabresultswrite");
			if ($done)
				$title="Industrial Clinic Laboratory :: Done Services Request";
			else
				$title="Industrial Clinic Laboratory :: Undone Services Request";

			$breakfile="labor.php".URL_APPEND;
			$fileforward="../../modules/ic_lab/seg-iclab-request-order-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin."&done=".$done;
			break;
	#--------------------
    
    #added by VAN 03-25-2013
    case "reportgen": 
                $src = $_GET['from'];
                
                switch ($src){
                    case 'bloodbank' :
                            $department = 'Blood Bank';             
                            $dept_nr = '190';
                            $ptype = 'bb';
                            break;
                                                
                    default :                       
                            $department = '';
                            $dept_nr = '';
                            $ptype = '';             
                }
                
                $title=$department."::Hospital Reports";
                $allowedarea=array('_a_1_blood_report_launcher');
                $fileforward=$root_path."modules/reports/report_launcher.php".URL_REDIRECT_APPEND."&ptype=".$ptype."&from=".$src."&dept_nr=".$dept_nr;
                break;                                       

	default :
				$title=$LDTestRequest." - ".$LDTestType[$target];
}# end of switch stmt

$lognote="$title ok";

//reset cookie;
// reset all 2nd level lock cookies
setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php'); setcookie('ck_2level_sid'.$sid,'');
require($root_path.'include/inc_passcheck_internchk.php');

/*
echo "labor_test_request_pass.php : HTTP_SESSION_VARS : <br>\n"; print_r($HTTP_SESSION_VARS); echo" <br> \n";
echo "labor_test_request_pass.php : target = '".$target."' <br> \n";
echo "labor_test_request_pass.php : allowedarea = "; print_r($allowedarea); echo " <br> \n";
echo "labor_test_request_pass.php : userck = '".$userck."' <br> \n";
echo "labor_test_request_pass.php : 1 pass = '".$pass."' <br> \n";
*/
if ($pass=='check'){
/*
echo "labor_test_request_pass.php : 2 pass = '".$pass."' <br> \n";
echo "labor_test_request_pass.php : 2 fileforward = '".$fileforward."' <br> \n";
echo"labor_test_request_pass.php : 2 _GET : <br> \n"; print_r($_GET); echo" <br> \n";
*/
	include($root_path.'include/inc_passcheck.php');

}

$errbuf=$title;
$minimal=1;
require_once($root_path.'include/inc_config_color.php');
require($root_path.'include/inc_passcheck_head.php');

#echo "labor_test_request_pass.php : 3 pass = '".$pass."' <br> \n";
?>

<BODY onLoad="if (window.focus) window.focus(); document.passwindow.userid.focus();">
<FONT    SIZE=-1  FACE="Arial">
<!--replaced, 2007-10-05 FDP--------------
<P>
<img <?php echo createComIcon($root_path,$test_pass_logo,'0','absmiddle') ?>><FONT  COLOR="<?php echo $cfg[top_txtcolor] ?>"  size=5 FACE="verdana"> <b><?php echo $title;  ?></b></font>
<p>
-----with this--------------------------->
<table cellspacing="0"  class="titlebar" border=0>
	<tr valign=top  class="titlebar" >
			<td bgcolor="#e4e9f4" valign="bottom">
				&nbsp;&nbsp;
			<img <?php echo createComIcon($root_path,$test_pass_logo,'0','absmiddle') ?>>
			<font color="<?php echo $cfg[top_txtcolor] ?>"  size=6  face="verdana"> <b><?php echo $title ?></b></font>
		</td>
	</tr>
</table>
<!----until here only, 2007-10-05 FDP--->

<table width=100% border=0 cellpadding="0" cellspacing="0">

<?php require($root_path.'include/inc_passcheck_mask.php') ?>

<p>

<?php
require($root_path.'include/inc_load_copyrite.php');

?>
</FONT>


</BODY>
</HTML>
