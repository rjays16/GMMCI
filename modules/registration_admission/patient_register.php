<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 
* See the file "copy_notice.txt" for the licence notice
*/
#echo "permission = ".$HTTP_SESSION_VARS['sess_permission'];
$lang_tables[]='emr.php';
$lang_tables[]='person.php';
$lang_tables[]='date_time.php';
#$lang_tables[]='place.php';   # burn added: March 1, 2007
define('LANG_FILE','aufnahme.php');

$local_user='aufnahme_user';
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

//added by cha, august 6, 2010
$from_ic = $_GET['from_ic'];

#echo "basename(__FILE__) = '".basename(__FILE__)."' <br> \n";

$thisfile=basename(__FILE__);
	# burn added : May 22, 2007
	# used when duplication of person registration
$seg_thisfile=basename(__FILE__);
#if (empty($HTTP_POST_VARS['sid']))
	$seg_thisfile = $seg_thisfile.URL_APPEND;
#else
#	$seg_thisfile = $seg_thisfile."?sid=".$HTTP_POST_VARS['sid']."&lang=".$HTTP_POST_VARS['lang']."&checkintern=".$HTTP_POST_VARS['checkintern'];

$default_filebreak=$root_path.'main/startframe.php'.URL_APPEND;


if(empty($HTTP_SESSION_VARS['sess_path_referer']) || !file_exists($root_path.$HTTP_SESSION_VARS['sess_path_referer'])) {
    $breakfile=$default_filebreak;
} else {
    $breakfile=$root_path.$HTTP_SESSION_VARS['sess_path_referer'].URL_APPEND;
}

if ($ptype=='er')
	$breakfile=$root_path.'modules/er/seg-er-functions.php'.URL_APPEND;
elseif ($ptype=='opd')
	$breakfile=$root_path.'modules/opd/seg-opd-functions.php'.URL_APPEND;
elseif ($ptype=='ipd')
	$breakfile=$root_path.'modules/ipd/seg-ipd-functions.php'.URL_APPEND;
elseif ($ptype=='medocs')
	$breakfile=$root_path.'modules/medocs/seg-medocs-functions.php'.URL_APPEND;
	
if(!session_is_registered('sess_pid')) session_register('sess_pid');
if(!isset($insurance_show)) $insurance_show=true;

$newdata=1;
$target='entry';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 
#added by VAN 
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
	$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
	$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	
#echo "<br>user = ".$seg_user_name;
		
$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

#added by VAN 07-02-08
if (($allow_newborn_register)&&($ptype=='newborn'))
	$LDPatientRegister = "New Born Registration";
	
 $smarty->assign('sToolbarTitle',$LDPatientRegister);

 # href for help button	
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister')");

 #modified by cha, august 6, 2010------------
 if(!$from_ic)
 $smarty->assign('breakfile',$breakfile);
 else
		$smarty->assign('breakfile','');
 #end cha------------------------------------

 # Window bar title
 $smarty->assign('title',$LDPatientRegister);
 #edited by VAN 02-22-08
 
#added by VAN 11-09-2008
if ($allow_er_user)
 	$val = 0;
 else
 	$val = 1;
#-------------------------
 $smarty->assign('pbHelp',"javascript:gethelp('person_reg_newform.php','".$error."','".$error_person_exists."')");

 # Hide the return button
 $smarty->assign('pbBack',FALSE);


/* Create the tabs */
$tab_bot_line='#66ee66';
#modified by cha, august 6, 2010------------------
#if(!$from_ic) {
require('./gui_bridge/default/gui_tabs_patreg.php');
#}
#end cha------------------------------------------

global $theBarangay, $theMunicity;
$theBarangay= NULL;
$theMunicity = NULL;

require_once($root_path.'include/care_api_classes/class_gui_input_person.php');

$inperson = & new GuiInputPerson;

$inperson->setPID($pid);
$inperson->setUserDept($user_dept_info['dept_nr']);   # burn added: March 14, 2007
$inperson->pretext = $sTemp;
$inperson->setDisplayFile('patient_register_show.php');

#echo "patient_register.php : inperson->seg_error_person_exists = '".$inperson->seg_error_person_exists."' <br> \n";
#echo "patient_register.php : inperson->getError() = '".$inperson->getError()."' <br> \n";

$sRegForm=$inperson->create();

$onLoadJs = "onLoad=\"if (window.focus) window.focus(); preset(); jsShowDetails(false); checkIfSenior(); EnableButton(".$val.");";
if ($theBarangay) 
  $onLoadJs.="xajax_setBarangay($theBarangay);";
elseif ($theMunicity) {
  $onLoadJs.="xajax_setMuniCity($theMunicity);";
}
else $onLoadJs.="alert('No brgy or mun...');";
$onLoadJs.="\"";
$smarty->assign('sOnLoadJs',$onLoadJs);

$smarty->assign('sRegForm',$sRegForm);

#modified by cha, august 6, 2010---------------------------------
if(!$from_ic) {
$smarty->assign('sSearchLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="patient_register_search.php'.URL_APPEND.'">'.$LDPatientSearch.'</a>');
$smarty->assign('sArchiveLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="patient_register_archive.php'.URL_APPEND.'&newdata=1&from=entry">'.$LDArchive.'</a>');
}
#end cha---------------------------------------------------------

$sCancel="<a href=";
if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $sCancel.=$breakfile;
	else $sCancel.='aufnahme_pass.php';
#$sCancel.=URL_APPEND.'><img '.createLDImgSrc($root_path,'cancel.gif','0').' alt="'.$LDCancelClose.'"></a>';
$sCancel.='><img '.createLDImgSrc($root_path,'cancel.gif','0').' alt="'.$LDCancelClose.'"></a>';
#commented by VAN 04-17-08
#$smarty->assign('pbCancel',$sCancel);

$smarty->assign('sMainBlockIncludeFile','registration_admission/reg_input.tpl');

$smarty->display('common/mainframe.tpl');

#require($root_path.'js/floatscroll.js');

?>