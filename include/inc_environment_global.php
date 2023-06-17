<?php
#
# Page generation time measurement
# define to 1 to measure page generation time
#
define('USE_PAGE_GEN_TIME',1);

#
# Doctors on duty change time
# Define the time when the doc-on-duty will change in 24 hours H.M format (eg. 3 PM = 15.00, 12 PM = 0.00)
#
define('DOC_CHANGE_TIME','7.30');

#
# Nurse on duty change time
# Define the time when the nurse-on-duty will change in 24 hours H.M format (eg. 3 PM = 15.00, 12 PM = 0.00)
#
define('NOC_CHANGE_TIME','7.30');

#
# Html output base 64 encryption
# Define to TRUE if you want to send the html output in base64 encrypted form
#
define('ENCRYPT_PAGE_BASE64',FALSE);

#
# SQL "no-date" values for different database types
#Define the "no-date" values for the db date field
#
define('NODATE_MYSQL','0000-00-00');
define('NODATE_POSTGRE','0001-01-01');
define('NODATE_ORACLE','0001-01-01');
define('NODATE_DEFAULT','0000-00-00');

#
# Admission module�s extended tabs. Care2x >= 2.0.2
# Define to TRUE for extended tabs mode
#
define('ADMISSION_EXT_TABS',FALSE);

#
# Template theme for Care2x`s own template object
# Set the default template theme
#
$template_theme='biju';
//$template_theme='default';
#
# Set the template path
#
$template_path=$root_path.'gui/html_template/';

#
# ---------- Do not edit below this ---------------------------------------------
# Load the html page encryptor
#
if(defined('ENCRYPT_PAGE_BASE64')&&ENCRYPT_PAGE_BASE64){
	include_once($root_path.'classes/html_encryptor/csource.php');
}

#
# globalize the POST, GET, & COOKIE variables
#
require_once($root_path.'include/inc_vars_resolve.php');

#
# Set global defines
#
if(!defined('LANG_DEFAULT')) define ('LANG_DEFAULT','en');

#
# Establish db connection
require_once($root_path.'include/inc_db_makelink.php');

#
# Custom Error reporting
# Edited by Alvin, 01/28/10
#
require_once($root_path.'include/care_api_classes/class_error.php');
global $errorReporter;

if (!$errorReporter)
	$errorReporter = new ErrorReporter();

#
# Session configurations
#
if(!defined('NOSTART_SESSION')||(defined('NOSTART_SESSION')&&!NOSTART_SESSION)){
	# If the session is existing, destroy it. This is a workaround for php engines which are configured to session autostart = On
	if(session_id()) session_destroy();
	# Set sessions handler to "user"
	ini_set('session.save_handler','user');
	# Set transparent session id
	if(!ini_get('session.use_trans_sid')) ini_set('session.use_trans_sid',1);
	//ini_set('session.use_trans_sid',0);
	# Set session name to "sid"
	ini_set('session.name','sid');
	# Set garbage collection max lifetime
	ini_set('session.gc_maxlifetime',10800); # = 3 Hours
	# Set cache lifetime
	//ini_set('session.cache_expire',1); # = 3 Hours
	# Start adodb session handling
	#
	# New session handler starting adodb 4.05
	#
		$GLOBALS['ADODB_SESSION_DRIVER']=$dbtypeuse;
	$GLOBALS['ADODB_SESSION_CONNECT']=$dbhost;
	$GLOBALS['ADODB_SESSION_USER'] =$dbusername;
	$GLOBALS['ADODB_SESSION_PWD'] =$dbpassword;
	$GLOBALS['ADODB_SESSION_DB'] =$dbname;
	$GLOBALS['ADODB_SESSION_TBL'] =$dbsessiontb;

		include_once($root_path.'classes/adodb/session/adodb-session2.php');

	// Old adodb 250 session handler
	//include_once($root_path.'classes/adodb/adodb-session.php');
	session_start();
}

#
# Set the url append data
#

if (ini_get('session.use_trans_sid')!=1) {
		define('URL_APPEND', '?sid='.$sid.'&lang='.$lang);
	$not_trans_id=true;
} else {
	# Patch to avoid missing constant
	 define('URL_APPEND', '?ntid=false&lang='.$lang);
	//define('URL_APPEND','?lang='.$lang);
	$not_trans_id=false;
}

define('URL_REDIRECT_APPEND','?sid='.$sid.'&lang='.$lang);

#
# Page generation time start
#
if(defined('USE_PAGE_GEN_TIME')&&USE_PAGE_GEN_TIME){
	include($root_path.'classes/ladezeit/ladezeitclass.php');
	$pgt=new ladezeit();
	$pgt->start();
}
//echo URL_APPEND; echo URL_REDIRECT_APPEND;
#
# Template align tags, default values
#
$TP_ALIGN='left'; # template variable for document direction
$TP_ANTIALIGN='right';
$TP_DIR='ltr';



#--------------------- start-------------------------------------------------

#added by VAN 03-07-2013
$updateBBDates = array('_a_1_bloodupdateDates','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($updateBBDates);$i++){
        if (ereg($updateBBDates[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_updateBBDates = 1;
                break;
        }else
                $allow_updateBBDates = 0;
}

#added by shan 02/06/2013
#allow to view medical medical certifacate

$viewMedcert = array('_a_1_viewmedicalcertifacate','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($viewMedcert);$i++){
        if (ereg($viewMedcert[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_viewMedcert = 1;
                break;
        }else
                $allow_viewMedcert = 0;
}

#added by shan 02/06/2013
#allow to view billing
$viewBilling = array('_a_1_viewbilling','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($viewBilling);$i++){
        if (ereg($viewBilling[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_viewBilling = 1;
                break;
        }else
                $allow_viewBilling = 0;
}

#added by shan 05/21/2013
#allow to undo MGH in medical records

$allow_MGH = array('_a_1_canUndoMGH','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($allow_MGH);$i++){
        if (ereg($allow_MGH[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_allow_MGH = 1;
                break;
        }else
                $allow_allow_MGH = 0;
}

#added by shan 05/21/2013
#allow to change the OR NUMBER field
$opdornumber = array('_a_1_opdornumber','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($opdornumber);$i++){
        if (ereg($opdornumber[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_opdornumber = 1;
                break;
        }else
                $allow_opdornumber = 0;
}

#added by pol 02/06/2013
#allow to view billing report
$BillingReports = array('_a_1_billreports','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($BillingReports);$i++){
        if (ereg($BillingReports[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_BillingReports = 1;
                break;
        }else
                $allow_BillingReports = 0;
}
#end pol
#added by nick 2/4/14
#allog to print Transmittal History in PDF or Excel(Monthly)
$transmittalHistoryReport = array('_a_1_transmittalHistoryReport','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($transmittalHistoryReport);$i++){
        if (ereg($transmittalHistoryReport[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_transmittalHistoryReport = 1;
                break;
        }else
                $allow_transmittalHistoryReport = 0;
}
#end nick
#added by shan 04/03/2013
#allow to view cashier achieve
$viewCashierArchier = array('_a_1_cashierarchives','_a_0_all', 'System_Admin', 'Cashier Archieve');
for ($i=0; $i<sizeof($viewCashierArchier);$i++){
        if (ereg($viewCashierArchier[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_viewCashierArchier = 1;
                break;
        }else
                $allow_viewCashierArchier = 0;
}
//------------------------ for billing ------------------------
#allow to delete in list of billing 
$canViewBillingList = array('_a_2_billingList','_a_0_all', 'System_Admin', 'View Billing List');
for ($i=0; $i<sizeof($canViewBillingList);$i++){
        if (ereg($canViewBillingList[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_canViewBillingList = 1;
                break;
        }else
                $allow_canViewBillingList = 0;
}

$canDelete = array('_a_2_billDeleteBtn','_a_0_all', 'System_Admin', 'List of Billing Delete');
for ($i=0; $i<sizeof($canDelete);$i++){
        if (ereg($canDelete[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_canDelete = 1;
                break;
        }else
                $allow_canDelete = 0;

}

$canView = array('_a_2_billViewBtn','_a_0_all', 'System_Admin', 'List of Billing View');
for ($i=0; $i<sizeof($canView);$i++){
        if (ereg($canView[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_canView = 1;
                break;
        }else
                $allow_canView = 0;
}


//------------------ end 02/06/2013 ----------------------------------------------


#added by VAN 09-30-2012
$updateBloodData = array('_a_1_bloodupdateData','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($updateBloodData);$i++){
        if (ereg($updateBloodData[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_updateBloodData = 1;
                break;
        }else
                $allow_updateBloodData = 0;
}

$updateNameData = array('_a_1_updateNameData','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($updateNameData);$i++){
        if (ereg($updateNameData[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_updateNameData = 1;
                break;
        }else
                $allow_updateNameData = 0;
}

$ORprint = array('_a_1_cashiernewor','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($ORprint);$i++){
        if (ereg($ORprint[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_ORprint = 1;
                break;
        }else
                $allow_ORprint = 0;
}

//added by jarel 03-04-2013
$CancelDeath = array('_a_1_canceldeath','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($CancelDeath);$i++){
        if (ereg($CancelDeath[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_CancelDeath = 1;
                break;
        }else
                $allow_CancelDeath = 0;
}

$dependent_only = array('_a_2_dependents_manager_only');
for ($i=0; $i<sizeof($dependent_only);$i++){
        if (ereg($dependent_only[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_dependent_only = 1;
                break;
        }else
                $allow_dependent_only = 0;
}

#added by VAN 11-17-2011
$access_system_admin = array('_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($access_system_admin);$i++){
        if (ereg($access_system_admin[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_accessSysAd = 1;
                break;
        }else
                $allow_accessSysAd = 0;
}

$accessOBGYNEUTZ = array('_a_1_radioOBGYNEUTZ','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($accessOBGYNEUTZ);$i++){
        if (ereg($accessOBGYNEUTZ[$i],$HTTP_SESSION_VARS['sess_permission'])){
                $allow_accessOBGYNEUTZ = 1;
                break;
        }else
                $allow_accessOBGYNEUTZ = 0;
}

#added by VAN 05-16-2011
$accessMRI = array('_a_1_radioMRI','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($accessMRI);$i++){
		if (ereg($accessMRI[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessMRI = 1;
				break;
		}else
				$allow_accessMRI = 0;
}

#added by VAN 04-30-2010
$accessCT = array('_a_1_radioCT','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($accessCT);$i++){
		if (ereg($accessCT[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessCT = 1;
				break;
		}else
				$allow_accessCT = 0;
}

$accessUTZ = array('_a_1_radioUTZ','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($accessUTZ);$i++){
		if (ereg($accessUTZ[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessUTZ = 1;
				break;
		}else
				$allow_accessUTZ = 0;
}

$accessXRAY = array('_a_1_radioXRAY','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($accessXRAY);$i++){
		if (ereg($accessXRAY[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessXRAY = 1;
				break;
		}else
				$allow_accessXRAY = 0;
}

$accessmanualpay = array('_a_1_radiomanualpay');
for ($i=0; $i<sizeof($accessmanualpay);$i++){
		if (ereg($accessmanualpay[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessmanualpay = 1;
				break;
		}else
				$allow_accessmanualpay = 0;
}
#-----------------------


#added by VAN 02-01-10
#lab
$labrequest = array('_a_1_labcreaterequest','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($labrequest);$i++){
		if (ereg($labrequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_labrequest = 1;
				break;
		}else
				$allow_labrequest = 0;
}

#blood
$bloodrequest = array('_a_1_bloodcreaterequest','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($bloodrequest);$i++){
		if (ereg($bloodrequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_bloodrequest = 1;
				break;
		}else
				$allow_bloodrequest = 0;
}

#radio
$radiorequest = array('_a_1_radiocreaterequest','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($radiorequest);$i++){
		if (ereg($radiorequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_radiorequest = 1;
				break;
		}else
				$allow_radiorequest = 0;
}

#pharma
$pharmarequest = array('_a_2_pharmaordercreate','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($pharmarequest);$i++){
		if (ereg($pharmarequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_pharmarequest = 1;
				break;
		}else
				$allow_pharmarequest = 0;
}

#or
$orrequest = array('_a_1_opcreaterequest','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($orrequest);$i++){
		if (ereg($orrequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_orrequest = 1;
				break;
		}else
				$allow_orrequest = 0;
}

#other charges
$otherrequest = array('_a_1_nursingcreaterequest','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($otherrequest);$i++){
		if (ereg($otherrequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_otherrequest = 1;
				break;
		}else
				$allow_otherrequest = 0;
}

#-----------------------

#added by VAN 09-14-09
$clinic = array('_a_1_inclinic');
for ($i=0; $i<sizeof($clinic);$i++){
		if (ereg($clinic[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_only_clinic = 1;
				break;
		}else
				$allow_only_clinic = 0;
}

$consult_admit = array('_a_1_admissionwrite','_a_1_phspatientadmit','_a_1_erpatientadmit','_a_1_opdpatientadmit','_a_1_ipdpatientadmit');
for ($i=0; $i<sizeof($consult_admit);$i++){
		if (ereg($consult_admit[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_consult_admit = 1;
				break;
		}else
				$allow_consult_admit = 0;
}

$add_charges = array('_a_1_addcharges');
for ($i=0; $i<sizeof($add_charges);$i++){
		if (ereg($add_charges[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_add_charges = 1;
				break;
		}else
				$allow_add_charges = 0;
}

$referral = array('_a_1_referral');
for ($i=0; $i<sizeof($referral);$i++){
		if (ereg($referral[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_referral = 1;
				break;
		}else
				$allow_referral = 0;
}

#added by VAN 07-30-09
$updateDate = array('_a_1_updateDate');
for ($i=0; $i<sizeof($updateDate);$i++){
		if (ereg($updateDate[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_updateDate = 1;
				break;
		}else
				$allow_updateDate = 0;
}

$updateData = array('_a_1_updateData');
for ($i=0; $i<sizeof($updateData);$i++){
		if (ereg($updateData[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_updateData = 1;
				break;
		}else
				$allow_updateData = 0;
}

$accessOPD = array('_a_1_medocsOPD');
for ($i=0; $i<sizeof($accessOPD);$i++){
		if (ereg($accessOPD[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessOPD = 1;
				break;
		}else
				$allow_accessOPD = 0;
}

$accessPHS = array('_a_1_medocsPHS');
for ($i=0; $i<sizeof($accessPHS);$i++){
		if (ereg($accessPHS[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessPHS = 1;
				break;
		}else
				$allow_accessPHS = 0;
}


$accessER = array('_a_1_medocsER');
for ($i=0; $i<sizeof($accessER);$i++){
		if (ereg($accessER[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessER = 1;
				break;
		}else
				$allow_accessER = 0;
}

$accessIPD = array('_a_1_medocsIPD');
for ($i=0; $i<sizeof($accessIPD);$i++){
		if (ereg($accessIPD[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_accessIPD = 1;
				break;
		}else
				$allow_accessIPD = 0;
}

#--------------------

#added by VAS 11-09-08
$canserve = array('_a_1_served');
for ($i=0; $i<sizeof($canserve);$i++){
		if (ereg($canserve[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_canserve = 1;
				break;
		}else
				$allow_canserve = 0;
}

$vitalsigns = array('_a_1_opdvitalsigns', '_a_1_ervitalsigns', '_a_1_ipdvitalsigns');
for ($i=0; $i<sizeof($vitalsigns);$i++){
		if (ereg($vitalsigns[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_vitalsigns = 1;
				break;
		}else
				$allow_vitalsigns = 0;
}

$labresult = array('_a_1_labresultswrite','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($labresult);$i++){
		if (ereg($labresult[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_labresult = 1;
				break;
		}else
				$allow_labresult = 0;
}

$labresult_read = array('_a_2_labresultsread','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($labresult_read);$i++){
		if (ereg($labresult_read[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_labresult_read = 1;
				break;
		}else
				$allow_labresult_read = 0;
}

$radioresult = array('_a_1_radioresultswrite','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($radioresult);$i++){
		if (ereg($radioresult[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_radioresult = 1;
				break;
		}else
				$allow_radioresult = 0;
}

$radioresult_read = array('_a_2_labresultsread','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($radioresult_read);$i++){
		if (ereg($radioresult_read[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_radioresult_read = 1;
				break;
		}else
				$allow_radioresult_read = 0;
}

$receive = array('_a_1_medocsreceive','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($receive);$i++){
		if (ereg($receive[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_receive = 1;
				break;
		}else
				$allow_receive = 0;
}

$labrepeat = array('_a_1_labrepeat','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($labrepeat);$i++){
		if (ereg($labrepeat[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_labrepeat = 1;
				break;
		}else
				$allow_labrepeat = 0;
}

$radiorepeat = array('_a_1_radiorepeat','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($radiorepeat);$i++){
		if (ereg($radiorepeat[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_radiorepeat = 1;
				break;
		}else
				$allow_radiorepeat = 0;
}

$ipdcancel = array('_a_1_ipdcancel','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($ipdcancel);$i++){
		if (ereg($ipdcancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ipdcancel = 1;
				break;
		}else
				$allow_ipdcancel = 0;
}

#added by VAN 06-08-09
$ipddiscancel = array('_a_1_ipddischargecancel','_a_1_erdischargecancel','_a_1_opddischargecancel','_a_1_phsdischargecancel','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($ipddiscancel);$i++){
		if (ereg($ipddiscancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ipddiscancel = 1;
				break;
		}else
				$allow_ipddiscancel = 0;
}

#-------

$opdcancel = array('_a_1_opdcancel','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($opdcancel);$i++){
		if (ereg($opdcancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_opdcancel = 1;
				break;
		}else
				$allow_opdcancel = 0;
}

$ercancel = array('_a_1_ercancel','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($ercancel);$i++){
		if (ereg($ercancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ercancel = 1;
				break;
		}else
				$allow_ercancel = 0;
}

$phscancel = array('_a_1_phscancel','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($phscancel);$i++){
		if (ereg($phscancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_phscancel = 1;
				break;
		}else
				$allow_phscancel = 0;
}


$patient_register = array('_a_1_opdpatientmanage','_a_2_opdpatientregister','_a_1_erpatientmanage','_a_2_erpatientregister','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($patient_register);$i++){
		if (ereg($patient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_patient_register = 1;
				break;
		}else
				$allow_patient_register = 0;
}

$phspatient_register = array('_a_1_phspatientmanage','_a_2_phspatientregister','_a_1_phspatientadmit','_a_2_phspatientupdate','_a_2_phspatientview','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($phspatient_register);$i++){
		if (ereg($phspatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_phs_user = 1;
				break;
		}else
				$allow_phs_user = 0;
}


$erpatient_register = array('_a_1_erpatientmanage','_a_2_erpatientregister','_a_1_erpatientadmit','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($erpatient_register);$i++){
		if (ereg($erpatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_er_user = 1;
				break;
		}else
				$allow_er_user = 0;
}

$opdpatient_register = array('_a_1_opdpatientmanage','_a_2_opdpatientregister','_a_1_opdpatientadmit','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($opdpatient_register);$i++){
		if (ereg($opdpatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_opd_user = 1;
				break;
		}else
				$allow_opd_user = 0;
}

$ipdpatient_register = array('_a_1_ipdpatientmanage','_a_2_ipdpatientregister','_a_1_ipdpatientadmit','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($ipdpatient_register);$i++){
		if (ereg($ipdpatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ipd_user = 1;
				break;
		}else
				$allow_ipd_user = 0;
}

$medocspatient_register = array('_a_1_medocsmedrecicd','_a_1_medocsmedrecmedical','_a_1_medocswrite','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($medocspatient_register);$i++){
		if (ereg($medocspatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_medocs_user = 1;
				break;
		}else
				$allow_medocs_user = 0;
}


#echo "allow = ".$allow_opdpatient_register;

$newborn_register = array('_a_1_medocspatientmanage','_a_2_medocspatientregister','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($newborn_register);$i++){
		if (ereg($newborn_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_newborn_register = 1;
				break;
		}else
				$allow_newborn_register = 0;
}

$update_permission = array('_a_2_opdpatientupdate','_a_2_erpatientupdate','_a_2_ipdpatientupdate','_a_2_medocspatientupdate',
                           '_a_1_opdpatientmanage','_a_1_erpatientmanage','_a_1_ipdpatientmanage','_a_1_medocspatientmanage',
                           '_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($update_permission);$i++){
		if (ereg($update_permission[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_update = 1;
				break;
		}else
				$allow_update = 0;
}
#echo "allow = ".$allow_update;

$icpatient_register = array('_a_1_icpatientmanage','_a_2_icpatientregister','_a_1_ictransadd','_a_1_ictransmanage','_a_0_all', 'System_Admin');
for ($i=0; $i<sizeof($icpatient_register);$i++){
		if (ereg($icpatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ic_user = 1;
				break;
		}else
				$allow_ic_user = 0;
}

#
# Function to return the <html> or <html dir-rtl> tag
#
function html_ret_rtl($lang){
	global $TP_ALIGN,$TP_ANTIALIGN, $TP_DIR;
	if(($lang=='ar')||($lang=='fa')){
		$TP_ANTIALIGN=$TP_ALIGN;
		$TP_ALIGN='right';
		$TP_DIR='rtl';
		return '<HTML dir=rtl>';
		}else{
			return '<HTML>';
		}
}

#
# Function to echo the returned value from function html_ret_rtl()
#

function html_rtl($lang){
	echo html_ret_rtl($lang);
}

function stringToColor($str) {
	$valid_colors = array(
		"navy","blue","blueviolet","brown","cadetblue","chocolate","coral","crimson","darkblue","darkcyan","darkgoldenrod","darkgreen",
		"darkmagenta","darkolivegreen","darkorange","darkorchid","darkred","darksalmon","darkslateblue","darkslategray",
		"darkviolet","deeppink","dimgray","firebrick","forestgreen","fuchsia","goldenrod","gray","green","indianred","indigo",
		"lightseagreen","lightslategray","limegreen","magenta","maroon","mediumblue","mediumorchid","mediumpurple","mediumvioletred",
		"midnightblue","olive","olivedrab","orange","orangered","purple","red","saddlebrown","seagreen","sienna","slategray","teal",
		"tomato");
	if ($str) {
/*
		$hash = md5($str);
		$total = 0;

		for ($i=0;$i<strlen($hash);$i++) {
#				$total = (int)($total * 16);
			$total += hexdec($hash[$i]);
		}
		return $valid_colors[abs($total) % count($valid_colors)]; */
		return '#2d2d80';
	}
	else return "0";
}

if (!function_exists('parseFloatEx')) {
	function parseFloatEx($x) {
		return (float) str_replace(",","",$x);
	}
}


if (!function_exists('validarea')) {
	function validarea(&$zeile2, $permit_type_all = 1){
	global $allowedarea;
	global $level2_permission;

	// if System_admin return true
	if(ereg('System_Admin', $zeile2)){
		return 1;
		}elseif(in_array('no_allow_type_all', $allowedarea)){ // check if the type "all" is blocked, if so return false
		return 0;
		}elseif($permit_type_all && ereg('_a_0_all', $zeile2)){ // if type "all" , return true
		return 1;
		}else{                                                                  // else scan the permission
		# Modified by AJMQ (04/02/08)

		if (is_array($level2_permission) && $level2_permission) {
			$lvl2access_ok=0;
			foreach($level2_permission as $j=>$v) {
				if(ereg($v,$zeile2)) {
					$lvl2access_ok=1;
					break;
				}
			}
		}
		else
			$lvl2access_ok=1;
			for($j=0;$j<sizeof($allowedarea);$j++){
			if(ereg($allowedarea[$j],$zeile2)) {
				return $lvl2access_ok;
			}
		}
	}
	return 0;           // otherwise the user has no access permission in the area, return false
	}
}

/**
*
* Halts the execution only if logged in as medocs or admin and outputs the value of the variable passed.
* Use as a debugging tool for deployment environment
*
* @param mixed $var
*
*/
function seg_die(&$var)
{
	if ($_SESSION['sess_temp_userid'] == 'medocs' || $_SESSION['sess_temp_userid'] == 'admin')
	{
		die( '<pre>'.$var.'</pre>' );
	}
}


/**
*
* Prints the contents of $var only if logged in as medocs or admin.
* Use as a debugging tool for deployment environment
*
* @param mixed $var
*
*/
function seg_inspect(&$var)
{
	if ($_SESSION['sess_temp_userid'] == 'medocs' || $_SESSION['sess_temp_userid'] == 'admin')
	{
		echo '<pre>'.var_export($var, true) . '</pre><hr/>';
	}
}


/**
*
* @param string $key Unique key ascribed to the benchmark
* @param bool $start True starts the benchmark, false ends it
* @param mixed $data Any data you wish to attach to the benchmark, only valid when starting a benchmark
*/
function seg_benchmark($key, $start=true, $data=null) {

	if ($_SESSION['sess_temp_userid'] == 'medocs' || $_SESSION['sess_temp_userid'] == 'admin') {
		if ($start)
		{
			if (!isset($_SESSION['benchmark'])) {
				$_SESSION['benchmark'] = array();
			}
			$_SESSION['benchmark'][$key] = array(
				'start' => microtime(true),
				'stop' => null,
				'data' => $data
			);
			return $_SESSION['benchmark'][$key];
		}
		else {
			$benchmark = $_SESSION['benchmark'];
			if (isset($benchmark[$key])) {
				$_SESSION['benchmark'][$key]['stop'] = microtime(true);
				$_SESSION['benchmark'][$key]['elapsed'] = $_SESSION['benchmark'][$key]['stop'] - $_SESSION['benchmark'][$key]['start'];
				return $_SESSION['benchmark'][$key];
			}
			else
				return false;
		}
	}
}



/**
 * A temporary method of generating GUIDs of the correct format for our DB.
 * @return String contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
 *
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function create_guid()
{
	$microTime = microtime();
	list($a_dec, $a_sec) = explode(" ", $microTime);

	$dec_hex = dechex($a_dec* 1000000);
	$sec_hex = dechex($a_sec);

	ensure_length($dec_hex, 5);
	ensure_length($sec_hex, 6);

	$guid = "";
	$guid .= $dec_hex;
	$guid .= create_guid_section(3);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= $sec_hex;
	$guid .= create_guid_section(6);

	return $guid;

}

function create_guid_section($characters)
{
	$return = "";
	for($i=0; $i<$characters; $i++)
	{
		$return .= dechex(mt_rand(0,15));
	}
	return $return;
}

function ensure_length(&$string, $length)
{
	$strlen = strlen($string);
	if($strlen < $length)
	{
		$string = str_pad($string,$length,"0");
	}
	else if($strlen > $length)
	{
		$string = substr($string, 0, $length);
	}
}


define('AC_AREA', 1);			// Accommodation
define('HS_AREA', 2);			// Hospital services
define('MD_AREA', 3);			// Medicines
define('SP_AREA', 4);			// Supplies
define('PF_AREA', 5);			// Professional Fees (Doctors' Fees)
define('OP_AREA', 6);			// Operation (Procedures)
define('XC_AREA', 7);			// Miscellaneous charges
define('PP_AREA', 8);			// Previous payments
define('DS_AREA', 9);			// Discounts
define('ER_PATIENT', 1);        //ER Patient
define('OUT_PATIENT', 2);       //Out Patient
define('DIALYSIS_PATIENT', 5);  //Dialysis Patient

define('DELETED', 'deleted');
define('DIALYSIS_ENCOUNTER_TYPE', 5);
define('CF2_EFFECTIVITY', "DATE('2010-09-01')"); 		// Start using new Philhealth Form on this date.
define('PHIC_ID', 18);          // Added by LST - 11/11/2012

define('java_dbaccess', "jdbc:mysql://$dbhost:3306/$dbname?user=$dbusername&password=$dbpassword");
define('java_include', 'http://localhost:8080/JavaBridge/java/Java.inc');
define('java_classpath', '/srv/tomcat/webapps/JavaBridge/WEB-INF/lib/');
define('java_resource', '/srv/tomcat/webapps/JavaBridge/resource/');
define('java_tmp', '/srv/tomcat/webapps/JavaBridge/tmp');
define('java_cache', '/srv/tomcat/webapps/JavaBridge/cache/');

// define('java_classpath', 'C:/xampp/tomcat/webapps/JavaBridge/WEB-INF/lib/');
// define('java_resource', 'C:/xampp/tomcat/webapps/JavaBridge/resource/');
// define('java_tmp', 'C:/xampp/tomcat/webapps/JavaBridge/tmp');
// define('java_cache', 'C:/xampp/tomcat/webapps/JavaBridge/cache/');
$yii=dirname(dirname(__FILE__)) . '/classes/yii/yii.php';
$config=dirname(dirname(__FILE__)) . '/frontend/protected/config/main.php';
//Inventory transaction codes
define('RECEIVE', 'RCV');
define('ADJUST', 'ADJ');
define('SALE','SLE');
define('TRANSFER', 'TRA');
define('ISSUANCE', 'ISS');
define('CONSUME', 'CON');
define('PHRETURN', 'RET');
define('UNPACK', 'UPK');
define('REPACK', 'RPK');
define('CANCEL', 'CNL');
define('UNDO', 'UND');
define('DEFAULT_UNIT', 2);

require_once($root_path.'include/inc_tomcat_bridge.php');
