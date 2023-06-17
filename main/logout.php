<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
if (!isset($logout)||!$logout) {header('Location:'.$root_path.'/language/'.$lang.'/lang_'.$lang.'_invalid-access-warning.php'); exit;}; 

# Reset all login cookies 

//setcookie('ck_login_pw'.$sid,'',0,'/');
//setcookie('ck_login_userid'.$sid,'',0,'/');
//setcookie('ck_login_username'.$sid,'',0,'/');
setcookie('ck_login_logged'.$sid,'',0,'/');
setcookie('ck_login_reset'.$sid,FALSE,0,'/');

# Empty session login values
$HTTP_SESSION_VARS['sess_temp_userid']='';
$HTTP_SESSION_VARS['sess_login_userid']='';
$HTTP_SESSION_VARS['sess_login_username']='';
$HTTP_SESSION_VARS['sess_login_pw']='';

//$HTTP_POST_VARS['slogout']=1;   

/*
#-------------added by van 03-19-07-------

	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Admission'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Medocs'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Doctors'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Nursing'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='OR'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Laboratories'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Radiology'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Pharmacy'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Medical Depot'";
	$result=$db->Execute($sql_update);
	$sql_update = "UPDATE care_menu_main SET is_visible=1 WHERE name='Special Tools'";
	$result=$db->Execute($sql_update);
	
	#----------------------------------------
*/	
#
# Redirect to login page for eventual new login
#
#---reload Menu -----
$HTTP_SESSION_VARS['sess_user_name'] = NULL;
$HTTP_SESSION_VARS['sess_login_username'] = NULL;

#----added by vanessa 03-20-07-----------
#refresh main menu
echo " \n <script type=\"text/javascript\">window.parent.location.href=window.parent.location.href;</script>";
#-------------------

	#burn added : October 5, 2007
$HTTP_SESSION_VARS['sess_temp_userid'] = NULL;
$HTTP_SESSION_VARS['sess_user_personell_nr'] = NULL;
$HTTP_SESSION_VARS['sess_user_pid'] = NULL;
$HTTP_SESSION_VARS['sess_user_fullname'] = NULL;
$HTTP_SESSION_VARS['sess_temp_personell_nr'] = NULL;
$HTTP_SESSION_VARS['sess_temp_pid'] = NULL;
$HTTP_SESSION_VARS['sess_temp_fullname'] = NULL;


header("location: login.php".URL_REDIRECT_APPEND."&is_logged_out=1");

exit;
?>
<?php   /*
   if($HTTP_SESSION_VARS['sess_login_username']==''){  ?>
    <a href="login_lnk.php" target="banner"></a>
   <?}   */  ?>  


