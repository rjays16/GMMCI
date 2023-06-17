<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'global_conf/areas_allow.php');

#$allowedarea=&$allow_area['edp'];
$breakfile='edv.php?sid='.$sid.'&lang='.$lang;

switch($target)
{
	case 'sqldb':
								$allowedarea=array("_a_1_sysad_access");
								$title=$LDSqlDb;
								//$userck="ck_edv_mysql_user";
								$fileforward='phpmyadmin-start.php?lang='.$lang.'&sid='.$sid;
								//$fileforward="../start.php";
								break;
	case 'adminlogin':
								$allowedarea=array("_a_1_sysad_access");
								$title=$LDSystemLogin;
								//$userck='ck_edv_admin_user';
								$fileforward='edv_system_admin_mframe.php?lang='.$lang.'&sid='.$sid;
								break;
	case 'adminoverride':
								$allowedarea=array("_a_1_sysad_override");
								$title='Test Request Overriding';
								$fileforward='override/seg_patient_search.php?lang='.$lang.'&sid='.$sid;
								break;
	case 'adminoverride_show':
								$allowedarea=array("_a_1_sysad_override");
								$title='Test Request Overriding';
								$fileforward = 'override/seg_override.php'.$append."&user_ck=$user_ck&from=$from&pid=$pid&encounter_nr=$encounter_nr&origin=patreg_reg&mode=entry&discountid=$discountid";	//edited by cha 10-20-2009
								break;
	case 'adminprovcancel':
								$allowedarea=array("_a_1_sysad_cancellation");
								$title='Request charge type cancellation';
								$fileforward = 'request_cancellation/seg-request-cancel.php?lang='.$lang.'&sid='.$sid;
								break;
	case 'FisMapping':
								$allowedarea=array("_a_1_sysad_fisMapping");
								$title='Manage Accounts in Accounting';
								$fileforward = 'fismapping/seg_fis_mapping.php?lang='.$lang.'&sid='.$sid;
								break;
	case 'currency_admin':
								$allowedarea=array("_a_1_sysad_access");
								$title=$LDSystemLogin;
								//$userck='ck_edv_admin_user';
								$fileforward="edv_system_format_currency_add.php?sid=$sid&lang=$lang&from=$from";
								break;
	case 'modulgenerator':
								$allowedarea=array("_a_1_sysad_access");
								$title=$LDSystemLogin;
								//$userck='ck_edv_admin_user';
								$fileforward=$root_path."modules/system_admin/sub_modul_neu.php?sid=$sid&lang=$lang&from=$from";
	break;

	case 'quickinformer':
		$title = "Quick informer settings";
		$allowedarea = array('_a_1_sysad_general');
		$fileforward="edv_general_info_quick.php".URL_APPEND;
	break;

	case 'paginator':
		$title = "Paginator max rows";
		$allowedarea = array('_a_1_sysad_general');
		$fileforward="edv_paginator_maxrows.php".URL_APPEND;
	break;

	case 'usermanagement':
		$title = "Access permissions";
		$allowedarea = array('_a_1_sysad_users');
		$fileforward=$root_path."modules/system_admin/edv_user_access_edit.php?sid=$sid&lang=$lang&from=$from";
	break;

	case 'timeout':
		$title = "Timeout settings";
		$allowedarea = array('_a_1_sysad_timeout');
		$fileforward=$root_path."modules/system_admin/edv_system_timeout.php?sid=$sid&lang=$lang&from=$from";
	break;

	case 'news':
		$title = "Manage news display";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_news_display.php?sid=$sid&lang=$lang";
	break;

	case 'dateformat':
		$title = "Date format settings";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_system_format_date.php?sid=$sid&lang=$lang";
	break;

	case 'currencyset':
		$title = "Manage currencies::Set currency";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_system_format_currency_set.php?sid=$sid&lang=$lang&target=currency_admin";
	break;

	case 'currencyadd':
		$title = "Manage currencies::Add new currency";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_system_format_currency_add.php?sid=$sid&lang=$lang&target=currency_admin";
	break;

	case 'discount':
		$title = "Manage discounts";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_system_discounts.php?sid=$sid&lang=$lang";
	break;

	case 'mainmenuhideshow':
		$title = "Main menu (show/hide/sort order)";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_system_menu_main_set.php?sid=$sid&lang=$lang&target=currency_admin";
	break;

	case 'mainmenuconfig':
		$title = "Main menu (configuration options)";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_system_menu_main_display_set.php?sid=$sid&lang=$lang&target=currency_admin";
	break;

	case 'dataentry':
		$title = "Data entry options (show/hide)";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_system_forms_set.php?sid=$sid&lang=$lang&target=currency_admin";
	break;

	case 'theme':
		$title = "Theme::Control buttons & Images";
		$allowedarea = array('_a_1_sysad_gui');
		$fileforward="edv_system_controls_theme.php?sid=$sid&lang=$lang&target=currency_admin";
	break;

	case 'deptadmincreate':
		$title = "Manage departments::Create";
		$allowedarea = array('_a_1_sysad_deptadmin');
		$fileforward=$root_path."modules/dept_admin/dept_new.php".URL_APPEND;
	break;

	case 'deptadminlist':
		$title = "Manage departments::List";
		$allowedarea = array('_a_1_sysad_deptadmin');
		$fileforward=$root_path."modules/dept_admin/dept_list.php".URL_APPEND;
	break;

	case 'deptadminconfig':
		$title = "Manage departments::Configuration options";
		$allowedarea = array('_a_1_sysad_deptadmin');
		$fileforward=$root_path."modules/dept_admin/dept_list_config.php".URL_APPEND;
	break;

	case 'oradmincreate':
		$title = "OR Admin::Create";
		$allowedarea = array('_a_1_sysad_oradmin');
		$fileforward=$root_path."modules/or_admin/or_new.php".URL_APPEND;
	break;

	case 'oradminlist':
		$title = "OR Admin::List & configure";
		$allowedarea = array('_a_1_sysad_oradmin');
		$fileforward=$root_path."modules/or_admin/or_list_config.php".URL_APPEND;
	break;

	case 'hospinfoedit':
		$title = "Hospital info::Edit";
		$allowedarea = array('_a_1_sysad_hospinfo');
		$fileforward="seg_hospital_info_edit.php".URL_APPEND;
	break;

	case 'hospinfoview':
		$title = "Hospital info::View";
		$allowedarea = array('_a_1_sysad_hospinfo');
		$fileforward="seg_hospital_info.php".URL_APPEND;
	break;

	case 'hospservicescreate':
		$title = "Other hospital services::Create";
		$allowedarea = array('_a_1_sysad_hospservices');
		$fileforward=$root_path."modules/insurance_co/seg_other_hospitalserv_new.php".URL_APPEND;
	break;

	case 'hospserviceslist':
		$title = "Other hospital services::List";
		$allowedarea = array('_a_1_sysad_hospservices');
		$fileforward=$root_path."modules/insurance_co/seg_other_hospitalserv_list.php".URL_APPEND;
	break;

	case 'insurancebenefitcreate':
		$title = "Insurance benefits::Create";
		$allowedarea = array('_a_1_sysad_insurance_manage','_a_2_sysad_insurance_benefit');
		$fileforward=$root_path."modules/insurance_co/seg_insurance_benefit_new.php".URL_APPEND;
	break;

	case 'insurancebenefitlist':
		$title = "Insurance benefits::List";
		$allowedarea = array('_a_1_sysad_insurance_manage','_a_2_sysad_insurance_benefit');
		$fileforward=$root_path."modules/insurance_co/seg_other_hospitalserv_list.php".URL_APPEND;
	break;

	case 'insuranceconfinementcreate':
		$title = "Confinement types::Create";
		$allowedarea = array('_a_1_sysad_insurance_manage','_a_2_sysad_insurance_confinement');
		$fileforward=$root_path."modules/insurance_co/seg_insurance_confinement_new.php".URL_APPEND;
	break;

	case 'insuranceconfinementlist':
		$title = "Confinement types::List";
		$allowedarea = array('_a_1_sysad_insurance_manage','_a_2_sysad_insurance_confinement');
		$fileforward=$root_path."modules/insurance_co/seg_insurance_confinement_list.php".URL_APPEND;
	break;

	case 'insuranceroomtypecreate':
		$title = "Room types::Create";
		$allowedarea = array('_a_1_sysad_insurance_manage','_a_2_sysad_insurance_roomtype');
		$fileforward=$root_path."modules/insurance_co/seg_insurance_roomtype_new.php".URL_APPEND;
	break;

	case 'insuranceroomtypelist':
		$title = "Room types::List";
		$allowedarea = array('_a_1_sysad_insurance_manage','_a_2_sysad_insurance_roomtype');
		$fileforward = $root_path."modules/insurance_co/seg_insurance_roomtype_list.php".URL_APPEND;
	break;

	default:{header('Location:'.$root_path.'language/'.$lang.'/lang_'.$lang.'_invalid-access-warning.php'); exit;};
}

$userck='ck_edv_user';
$thisfile='edv-main-pass.php';
$lognote="$title ok";

// reset all 2nd level lock cookies
setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php'); setcookie('ck_2level_sid'.$sid,'');

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check')
	include($root_path.'include/inc_passcheck.php');

$errbuf=$title;
$minimal=1;
require($root_path.'include/inc_passcheck_head.php');
?>

<BODY  <?php if (!$nofocus) echo 'onLoad="document.passwindow.userid.focus()"'; echo  ' bgcolor='.$cfg['body_bgcolor'];
 if (!$cfg['dhtml']){ echo ' link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; }
?>>

<P>
<img src="../../gui/img/common/default/kwheel.gif" border=0 align="middle">
<FONT  COLOR="<?php echo $cfg[top_txtcolor] ?>"  SIZE=5  FACE="verdana"> <b><?php echo $title ?></b></font>
<p>
<table width=100% border=0 cellpadding="0" cellspacing="0">

<?php require($root_path.'include/inc_passcheck_mask.php') ?>

<p>
<!-- <img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDIntro2 $title " ?></a><br>
<img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDWhat2Do $title " ?>?</a><br>
 -->
<p>
<?php
require($root_path.'include/inc_load_copyrite.php');
?>

</BODY>
</HTML>
