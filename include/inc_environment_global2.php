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
#
require_once($root_path.'include/inc_db_makelink.php');

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
	$ADODB_SESSION_DRIVER=$dbtype;
	$ADODB_SESSION_CONNECT=$dbhost;
	$ADODB_SESSION_USER =$dbusername;
	$ADODB_SESSION_PWD =$dbpassword;
	$ADODB_SESSION_DB =$dbname;

	//include_once($root_path.'classes/adodb/session/adodb-session.php');

	// Old adodb 250 session handler
	//include_once($root_path.'classes/adodb/adodb-session.php');
		include_once($root_path."classes/adodb/session/adodb-session2.php");
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

#added by VAN 02-01-10
#lab
$labrequest = array('_a_1_labcreaterequest');
for ($i=0; $i<sizeof($labrequest);$i++){
		if (ereg($labrequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_labrequest = 1;
				break;
		}else
				$allow_labrequest = 0;
}

#blood
$bloodrequest = array('_a_1_bloodcreaterequest');
for ($i=0; $i<sizeof($bloodrequest);$i++){
		if (ereg($bloodrequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_bloodrequest = 1;
				break;
		}else
				$allow_bloodrequest = 0;
}

#radio
$radiorequest = array('_a_1_radiocreaterequest');
for ($i=0; $i<sizeof($radiorequest);$i++){
		if (ereg($radiorequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_radiorequest = 1;
				break;
		}else
				$allow_radiorequest = 0;
}

#pharma
$pharmarequest = array('_a_2_pharmaordercreate');
for ($i=0; $i<sizeof($pharmarequest);$i++){
		if (ereg($pharmarequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_pharmarequest = 1;
				break;
		}else
				$allow_pharmarequest = 0;
}

#or
$orrequest = array('_a_1_opcreaterequest');
for ($i=0; $i<sizeof($orrequest);$i++){
		if (ereg($orrequest[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_orrequest = 1;
				break;
		}else
				$allow_orrequest = 0;
}

#other charges
$otherrequest = array('_a_1_nursingcreaterequest');
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

$labresult = array('_a_1_labresultswrite');
for ($i=0; $i<sizeof($labresult);$i++){
		if (ereg($labresult[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_labresult = 1;
				break;
		}else
				$allow_labresult = 0;
}

$labresult_read = array('_a_2_labresultsread');
for ($i=0; $i<sizeof($labresult_read);$i++){
		if (ereg($labresult_read[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_labresult_read = 1;
				break;
		}else
				$allow_labresult_read = 0;
}

$radioresult = array('_a_1_radioresultswrite');
for ($i=0; $i<sizeof($radioresult);$i++){
		if (ereg($radioresult[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_radioresult = 1;
				break;
		}else
				$allow_radioresult = 0;
}

$radioresult_read = array('_a_2_labresultsread');
for ($i=0; $i<sizeof($radioresult_read);$i++){
		if (ereg($radioresult_read[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_radioresult_read = 1;
				break;
		}else
				$allow_radioresult_read = 0;
}

$receive = array('_a_1_medocsreceive');
for ($i=0; $i<sizeof($receive);$i++){
		if (ereg($receive[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_receive = 1;
				break;
		}else
				$allow_receive = 0;
} 

$labrepeat = array('_a_1_labrepeat');
for ($i=0; $i<sizeof($labrepeat);$i++){
		if (ereg($labrepeat[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_labrepeat = 1;
				break;
		}else
				$allow_labrepeat = 0;
}    

$radiorepeat = array('_a_1_radiorepeat');
for ($i=0; $i<sizeof($radiorepeat);$i++){
		if (ereg($radiorepeat[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_radiorepeat = 1;
				break;
		}else
				$allow_radiorepeat = 0;
}  

$ipdcancel = array('_a_1_ipdcancel');
for ($i=0; $i<sizeof($ipdcancel);$i++){
		if (ereg($ipdcancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ipdcancel = 1;
				break;
		}else
				$allow_ipdcancel = 0;
} 

#added by VAN 06-08-09
$ipddiscancel = array('_a_1_ipddischargecancel','_a_1_erdischargecancel','_a_1_opddischargecancel','_a_1_phsdischargecancel');
for ($i=0; $i<sizeof($ipddiscancel);$i++){
		if (ereg($ipddiscancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ipddiscancel = 1;
				break;
		}else
				$allow_ipddiscancel = 0;
} 

#-------  

$opdcancel = array('_a_1_opdcancel');
for ($i=0; $i<sizeof($opdcancel);$i++){
		if (ereg($opdcancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_opdcancel = 1;
				break;
		}else
				$allow_opdcancel = 0;
}    

$ercancel = array('_a_1_ercancel');
for ($i=0; $i<sizeof($ercancel);$i++){
		if (ereg($ercancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ercancel = 1;
				break;
		}else
				$allow_ercancel = 0;
}        

$phscancel = array('_a_1_phscancel');
for ($i=0; $i<sizeof($phscancel);$i++){
		if (ereg($phscancel[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_phscancel = 1;
				break;
		}else
				$allow_phscancel = 0;
}        


$patient_register = array('_a_1_opdpatientmanage','_a_2_opdpatientregister','_a_1_erpatientmanage','_a_2_erpatientregister');
for ($i=0; $i<sizeof($patient_register);$i++){
		if (ereg($patient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_patient_register = 1;
				break;
		}else
				$allow_patient_register = 0;
}        

$phspatient_register = array('_a_1_phspatientmanage','_a_2_phspatientregister','_a_1_phspatientadmit');
for ($i=0; $i<sizeof($phspatient_register);$i++){
		if (ereg($phspatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_phs_user = 1;
				break;
		}else
				$allow_phs_user = 0;
}        


$erpatient_register = array('_a_1_erpatientmanage','_a_2_erpatientregister','_a_1_erpatientadmit');
for ($i=0; $i<sizeof($erpatient_register);$i++){
		if (ereg($erpatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_er_user = 1;
				break;
		}else
				$allow_er_user = 0;
}        

$opdpatient_register = array('_a_1_opdpatientmanage','_a_2_opdpatientregister','_a_1_opdpatientadmit');
for ($i=0; $i<sizeof($opdpatient_register);$i++){
		if (ereg($opdpatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_opd_user = 1;
				break;
		}else
				$allow_opd_user = 0;
}

$ipdpatient_register = array('_a_1_ipdpatientmanage','_a_2_ipdpatientregister','_a_1_ipdpatientadmit');
for ($i=0; $i<sizeof($ipdpatient_register);$i++){
		if (ereg($ipdpatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_ipd_user = 1;
				break;
		}else
				$allow_ipd_user = 0;
}        

$medocspatient_register = array('_a_1_medocsmedrecicd','_a_1_medocsmedrecmedical','_a_1_medocswrite');
for ($i=0; $i<sizeof($medocspatient_register);$i++){
		if (ereg($medocspatient_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_medocs_user = 1;
				break;
		}else
				$allow_medocs_user = 0;
}        

						
#echo "allow = ".$allow_opdpatient_register;    
				
$newborn_register = array('_a_1_medocspatientmanage','_a_2_medocspatientregister');
for ($i=0; $i<sizeof($newborn_register);$i++){
		if (ereg($newborn_register[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_newborn_register = 1;
				break;
		}else
				$allow_newborn_register = 0;
}

$update_permission = array('_a_2_opdpatientupdate','_a_2_erpatientupdate','_a_2_ipdpatientupdate','_a_2_medocspatientupdate',
													 '_a_1_opdpatientmanage','_a_1_erpatientmanage','_a_1_ipdpatientmanage','_a_1_medocspatientmanage');
for ($i=0; $i<sizeof($update_permission);$i++){
		if (ereg($update_permission[$i],$HTTP_SESSION_VARS['sess_permission'])){
				$allow_update = 1;
				break;
		}else
				$allow_update = 0;
}
#echo "allow = ".$allow_update;

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

function parseFloatEx($x) {
	return (float) str_replace(",","",$x);
}

define(AC_AREA, 1);			// Accommodation 
define(HS_AREA, 2);			// Hospital services 
define(MD_AREA, 3);			// Medicines
define(SP_AREA, 4);			// Supplies
define(PF_AREA, 5);			// Professional Fees (Doctors' Fees)
define(OP_AREA, 6);			// Operation (Procedures)
define(XC_AREA, 7);			// Miscellaneous charges
define(PP_AREA, 8);			// Previous payments
define(DS_AREA, 9);			// Discounts

?>