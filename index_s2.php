<?php
/*
CARE 2X Integrated Information System for Hospitals and Health Care Organizations and Services
Care 2002, Care2x, Copyright (C) 2002,2003,2004,2005  Elpidio Latorilla

Deployment 2.1 - 2004-10-02
								
This script(s) is(are) free software; you can redistribute it and/or
modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.
																  
This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details	.
											   
You should have received a copy of the GNU General Public
License along with this script; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
																		 
Copy of GNU General Public License at: http://www.gnu.org/
													 
Source code home page: http://www.care2x.org
Contact author at: elpidio@care2x.org

This notice also applies to other scripts which are integral to the functioning of CARE 2X within this directory and its top level directory
A copy of this notice is also available as file named copy_notice.txt under the top level directory.
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
define('FROM_ROOT',1);

if(!isset($mask)) $mask=false;
if(!isset($cookie)) $cookie=false;
if(!isset($_chg_lang_)) $_chg_lang_=false;
if(!isset($boot)) $boot=false;
if(!isset($sid)) $sid='';

require('./roots.php');
require('./include/inc_environment_global.php');

//$db->debug=1;

# Register global session variables
if(!session_is_registered('sess_user_name')) session_register('sess_user_name');
if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');
if(!session_is_registered('sess_file_forward')) session_register('sess_file_forward');
if(!session_is_registered('sess_file_return')) session_register('sess_file_return');
if(!session_is_registered('sess_file_break')) session_register('sess_file_break');
if(!session_is_registered('sess_path_referer')) session_register('sess_path_referer');
if(!session_is_registered('sess_dept_nr')) session_register('sess_dept_nr');
if(!session_is_registered('sess_title')) session_register('sess_title');
if(!session_is_registered('sess_lang')) session_register('sess_lang');
if(!session_is_registered('sess_user_id')) session_register('sess_user_id');
if(!session_is_registered('sess_cur_page')) session_register('sess_cur_page');
if(!session_is_registered('sess_searchkey')) session_register('sess_searchkey');
if(!session_is_registered('sess_tos')) session_register('sess_tos'); # the session time out start time

$bname='';
$bversion='';
$user_id='';
$ip='';
$cfgid='';
$config_exists=false;

$GLOBALCONFIG=array();
$USERCONFIG=array();

/****************************************************************************
 phpSniff: HTTP_USER_AGENT Client Sniffer for PHP
 Copyright (C) 2001 Roger Raymond ~ epsilon7@users.sourceforge.net

* Check environment : Browser, OS
* @param string $bn  name of browser
* @param string $bv  version of browser
* @param string $f   CFG filename
* @param string $i   IP adress
* @param string $uid new guid (session var)
* @return all parameter using &
* @access public
*
* 02.02.2003 Thomas Wiedmann
****************************************************************************
*/

require_once('./classes/phpSniff/phpSniff.class.php'); # Sniffer for PHP

function configNew(&$bn,&$bv,&$f,$i,&$uid)
{
  global $HTTP_USER_AGENT;
  global $REMOTE_ADDR;

  # We disable the error reporting, because Konqueror 3.0.3 causes a  runtime error output that stops the program.
  #  could be a bug in phpsniff .. hmmm?
  $old_err_rep= error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
  
  # Function rewritten by Thomas Wiedmann to use phpSniff class
  
  # initialize some vars
  if(!isset($UA)) $UA = '';
  if(!isset($cc)) $cc = '';
  if(!isset($dl)) $dl = '';
  if(!isset($am)) $am = '';

  //$timer = new phpTimer();
  //$timer->start('main');
  //$timer->start('client1');
  $sniffer_settings = array('check_cookies'=>$cc,'default_language'=>$dl,'allow_masquerading'=>$am);
  $client = new phpSniff($UA,$sniffer_settings);

  # get phpSniff result
  $i=$client->get_property('ip');
  $bv=$client->get_property('version');
  $bn=$client->get_property('browser');

  # translate some browsernames for "Care2x"
  if ($bn == 'moz') { $bn='mozilla';}
  else if ($bn == 'op') { $bn='opera';}
  else if ($bn == 'ns') { $bn='netscape';}
  else if ($bn == 'ie') { $bn='msie';}

  $uid=uniqid('');
  $f='CFG'.$uid.microtime().'.cfg';

   # Return previous error reporting 
   error_reporting($old_err_rep);
}

/**
* Create simple session id (sid), save a encrpyted  sid to a cookie with a dynamic name 
* consisting of concatenating "ck_sid" and the sid itself.
* For more information about the encryption class, see the proper docs of the pear's "hcemd5.php" class.
*/
//$sid=uniqid('');
$sid=session_id();
$ck_sid_buffer='ck_sid'.$sid;

include('include/inc_init_crypt.php'); // initialize crypt
$ciphersid=$enc_hcemd5->encodeMimeSelfRand($sid);
setcookie($ck_sid_buffer,$ciphersid);
$HTTP_COOKIE_VARS[$ck_sid_buffer]=$ciphersid;

#
# Simple counter, counts all hits including revisits
# Uncomment the following line  if you  like to count the hits, then make sure
# that the path /counter/hits/ and the file /counter/hitcount.txt  are system writeable
#
// include('./counter/count.php');


if((isset($boot)&&$boot)||!isset($HTTP_COOKIE_VARS['ck_config'])||empty($HTTP_COOKIE_VARS['ck_config'])) {
    configNew($bname,$bversion,$user_id,$ip,$cfgid);
} else {
    $user_id=$HTTP_COOKIE_VARS['ck_config'];
}

#
# Load user config API. Get the user config data from db
#
require_once('include/care_api_classes/class_userconfig.php');
$cfg_obj=new UserConfig;

if($cfg_obj->exists($user_id)) {
	$cfg_obj->getConfig($user_id);
	$USERCONFIG=$cfg_obj->buffer;
    $config_exists=true;  // Flag that user config is existing
}else{
	$cfg_obj->_getDefault();
	$USERCONFIG=$cfg_obj->buffer;
}

# Load global configurations API
require_once('include/care_api_classes/class_globalconfig.php');
$glob_cfg=new GlobalConfig($GLOBALCONFIG);

# Get the global config for language usage
$glob_cfg->getConfig('language_%');
# Get the global config for frames 
$glob_cfg->getConfig('gui_frame_left_nav_width');
# Get the global config for lev nav border 
$glob_cfg->getConfig('gui_frame_left_nav_border');

$savelang=0;
/*echo $GLOBALCONFIG['language_non_single'];
while (list($x,$v)=each($GLOBALCONFIG)) echo $x.'==>'.$v.'<br>';
*/
# Start checking language properties 
if(!$GLOBALCONFIG['language_single']) {
    # We get the language code
    if($_chg_lang_&&!empty($lang)) {
		    $savelang=1;
	}else{
		//echo $lang=$USERCONFIG['lang'];
        if($USERCONFIG['lang']) $lang=$USERCONFIG['lang'];
			    else  include('chklang.php');
	 } 
}else{

    # If single language is configured, we get the user configured lang
	if(!empty($USERCONFIG['lang']) && file_exists('language/'.$USERCONFIG['lang'].'/lang_'.$USERCONFIG['lang'].'_startframe.php')) {
	    $lang=$USERCONFIG['lang'];
	} else {
	    # If user config lang is not available, we get the global system lang configuration
	    if(!empty($GLOBALCONFIG['language_default']) && file_exists('language/'.$GLOBALCONFIG['language_default'].'/lang_'.$GLOBALCONFIG['language_default'].'_startframe.php')) {
            $lang=$GLOBALCONFIG['language_default'];
		} else {
	        $lang=LANG_DEFAULT; # Comes from inc_environment_global.php, the last chance, usually set to "en"
	    }	
	}
}

#
# After having a language code check if the critical scripts exist and set warning
#
$createwarn=file_exists('create_admin.php');
$initwarn=file_exists('./install/initialize.php');
$md5warn=file_exists('./install/encode_pw_md5.php');
$installwarn=file_exists('./install/install.php');
if($createwarn||$installwarn||$md5warn){
	#
	# Load necessary language tables
	#
	$lang_tables[]='create_admin.php';
	include_once('./include/inc_load_lang_tables.php');
	include_once('include/inc_charset_fx.php');
	if($createwarn){
		include('./include/inc_create_admin_warning.php');
	}
	if($initwarn){
		include('./include/inc_init_warning.php');
	}
	if($md5warn){
		include('./include/inc_md5_warning.php');
	}
	if($installwarn){
		include('./include/inc_install_warning.php');
	}
	#
	# exit to avoid running the program
	#
	exit;
}

#
# Prepare language file path
#
$lang_file='language/'.$lang.'/lang_'.$lang.'_startframe.php';

#
# We check if language table exists, if not, english is used
#
if(file_exists($lang_file)) {
    include($lang_file);
} else {
    include('language/en/lang_en_startframe.php');  # en = english is the default language table
	$lang='en';
}

#
# The language detection is finished, we save it to session
#
$HTTP_SESSION_VARS['sess_lang']=$lang;

/*$ck_lang_buffer='ck_lang'.$sid;
setcookie($ck_lang_buffer,$lang);*/

/*$HTTP_COOKIE_VARS[$ck_lang_buffer]=$lang;*/
	 //echo $mask;
if((isset($mask)&&$mask)||!$config_exists||$savelang) {
	if(!$config_exists) {

		//$cfg_obj->getConfig('default');
		//$USERCONFIG=&$cfg_obj->buffer;

		configNew($bname,$bversion,$user_id,$ip,$cfgid);

		$USERCONFIG['bname']=$bname;
		$USERCONFIG['bversion']=$bversion;
		$USERCONFIG['cid']=$cfgid;
	}
	// *****************************
	//save browser info to user config array
	// *****************************
	if(empty($ip)) $USERCONFIG['ip']=$REMOTE_ADDR;
	$USERCONFIG['mask']=$mask;
	$USERCONFIG['lang']=$lang;
	if(((($bname=='msie') ||($bname=='opera')) &&($bversion>4)) ||(($bname=='netscape')&&($bversion>3.5)) ||($bname=='mozilla')) {
		$USERCONFIG['dhtml']=1;
	}
	// *****************************
	// Save config to db
	// *****************************
	$mask=$USERCONFIG['mask']; # save mask before serializing
	$cfg_obj->saveConfig($user_id,$USERCONFIG);
	setcookie('ck_config',$user_id,time()+(3600*24*365)); # expires after 1 year
}

#
# save user_id to session
#
$HTTP_SESSION_VARS['sess_user_id']=$user_id;
if(empty($HTTP_SESSION_VARS['sess_user_name'])) $HTTP_SESSION_VARS['sess_user_name']='default';

#
# set the initial session timeout start value
#
$HTTP_SESSION_VARS['sess_tos']=date('His');

#
# Load character set fx
#
include_once('include/inc_charset_fx.php');

#
# Load image fx
#
require_once('include/inc_img_fx.php');

#
# Start smarty templating
#
# Workaround for user config array to work inside the smarty class
#
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<title>Segworks Hospital Information System - Online</title>


<link rel="stylesheet" href="images/template_css.css" type="text/css">
<!--<link rel="shortcut icon" href="http://localhost/segclinic/templates/247portal-b-brown/favicon.ico">
<link rel="alternate" title="SegClinic " href="http://localhost/segclinic/index2.php?option=com_rss&amp;no_html=1" type="application/rss+xml">
//-->
<script language="JavaScript" type="text/javascript">
    <!--
    function MM_reloadPage(init) {  //reloads the window if Nav4 resized
      if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
        document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
      else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
    }
    MM_reloadPage(true);
    //-->
  </script>
<style type="text/css">
<!--
.style2 {color: #666666}
body {
	margin-left: 8px;
	margin-top: 0px;
	margin-right: 8px;
	margin-bottom: 5px;
	background-color: #E6EDF0;
}
a:hover {
	color: #000000;
}
.style3 {
	color: #5B8799;
	font-weight: bold;
}
-->
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>

<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" height="837" width="100%">
  <tbody><tr>
    <td height="837" valign="top"><a name="up" id="up"></a><iframe src="login_lnk.php" id="banner" name="banner" width="100%" frameborder="0" scrolling="no">banner frame</iframe>
      <table style="border: 1px solid rgb(153, 160, 170);" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody><tr>
          <td height="494" valign="top"><table align="center" bgcolor="#eef0f0" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody><tr>
              <td width="20%" height="713" valign="top" background="images/modulback.gif" style="border-right: 1px solid rgb(153, 160, 170); border-bottom: 1px solid rgb(255, 255, 255);">                <table border="0" cellpadding="0" cellspacing="0" width="188">
                  <tbody><tr>
                    <td>			<table width="92%" cellpadding="0" cellspacing="0" class="moduletable">
                      <tbody>
                        <tr>
                          <td valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="moduletable">
                              <tbody>
                                <tr align="left">
                                  <th >Main Menu</th>
                                </tr>
                                <tr align="left">
                                  <td><a href="main/startframe.php" class="mainlevel" target="contframe">Home</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/registration_admission/patient_register_pass.php" class="mainlevel" target="contframe">Patients</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/appointment_scheduler/appt_main_pass.php" class="mainlevel" target="contframe">Appointments</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/registration_admission/aufnahme_pass.php" class="mainlevel" target="contframe">Admission</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/ambulatory/ambulatory.php" class="mainlevel" target="contframe">Ambulatory </a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/medocs/medocs_pass.php" class="mainlevel" target="contframe">Medocs</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/doctors/doctors.php" target="contframe" class="mainlevel">Doctors</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/nursing/nursing.php" target="contframe" class="mainlevel">Nursing</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="main/op-doku.php" class="mainlevel" target="contframe">OP Room</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/laboratory/labor.php" target="contframe" class="mainlevel" >Laboratories</a></td>
                                </tr>
                                <tr align="left">
                                  <td><font face="arial,verdana,helvetica" size="2"><b><a href="modules/radiology/radiolog.php" target="contframe" class="mainlevel">Radiology</a></b></font></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/pharmacy/apotheke.php" class="mainlevel" target="contframe">Pharmacy</a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/med_depot/medlager.php" target="contframe" class="mainlevel">Medical Depot </a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/phone_directory/phone.php" class="mainlevel" target="contframe">Directory </a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/tech/technik.php" target="contframe" class="mainlevel">Tech Support </a></td>
                                </tr>
                                <tr align="left">
                                  <td><a href="modules/system_admin/edv.php" class="mainlevel" target="contframe">System Admin </a> </td>
                                </tr>
                                <tr align="left">
                                  <td><a href="main/spediens.php" class="mainlevel" target="contframe">Special Tools</a> </td>
                                </tr>
                                <tr align="left">
                                  <td><a href="#" class="mainlevel">Contact Us</a></td>
                                </tr>
                              </tbody>
                          </table></td>
                        </tr>
                      </tbody>
                    </table>
                      <table class="moduletable" cellpadding="0" cellspacing="0">
						<tbody><tr>
				<td>
				
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody><tr align="left"><td>&nbsp;</td>
</tr>
</tbody></table>				</td>
			</tr>
			</tbody></table>
						<table class="moduletable" cellpadding="0" cellspacing="0">
							<tbody><tr>
					<th valign="top">
										News Updates </th>
				</tr>
							<tr>
				<td>
					<form action="/HIS/clinic/index_1.php" method="post" name="login">
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody><tr>
		<td><table width="100%"  border="0" cellspacing="2" cellpadding="2">
                 
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/news/open-time.php" target="contframe">Admission Hours  </a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/news/newscolumns.php" target="contframe">Management</a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/news/departments.php" target="contframe">Departments</a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/cafeteria/cafenews.php" target="contframe">Cafeteria News </a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/news/newscolumns.php" target="contframe">Admission</a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/news/newscolumns.php" target="contframe">Exhibitions</a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="/HIS/clinic/modules/news/newscolumns.php" target="contframe">Education</a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/news/newscolumns.php" target="contframe">Studies</a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/news/newscolumns.php" target="contframe">Physical Therapy</a> </td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/news/newscolumns.php" target="contframe">Health Tips</a> </td>
                  </tr>
                  <tr>
                    <td height="17"><a href="../../HIS/clinic/modules/calendar/calendar.php" target="contframe">Calendar</a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="javascript:gethelp()">Help</a></td>
                  </tr>
                  <tr>
                    <td height="17"><a href="modules/news/editor-pass.php" target="contframe">Submit News</a></td>
                  </tr>
                  <tr>
                    <td height="17">Credits</td>
                  </tr>
                </table>		</td>
	</tr>
	<tr>
		<td>
		<a href="http://localhost/SegClinic/index.php?option=com_registration&amp;task=lostPassword">		</a>
		</td>
	</tr>
			<tr>
			<td>&nbsp;			</td>
		</tr>
			</tbody></table>
                                        </form>
					</td>
			</tr>
			</tbody></table>
						<table class="moduletable" cellpadding="0" cellspacing="0">
							<tbody><tr>
					<th valign="top">&nbsp;										</th>
				</tr>
							<tr>
				<td>
				
<div class="syndicate">

	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=RSS0.91&amp;no_html=1">		</a>
	</div>
	
	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=RSS1.0&amp;no_html=1">		</a>
	</div>
	
	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=RSS2.0&amp;no_html=1">		</a>
	</div>
	
	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=ATOM0.3&amp;no_html=1">		</a>
	</div>
	
	<div align="center">
	<a href="http://localhost/segclinic/index2.php?option=com_rss&amp;feed=OPML&amp;no_html=1">		</a>
	</div>
	</div>
				</td>
			</tr>
			</tbody></table>
			</td>
                  </tr>
                </tbody></table>
                </td>
              <td width="100%" valign="top" bgcolor="#FFFFFF" style="border-left: 1px solid rgb(255, 255, 255); border-right: 1px solid rgb(255, 255, 255); border-bottom: 1px solid rgb(255, 255, 255);">
			  
			  <iframe src="main/startframe.php" name="contframe" width="100%" height="740" frameborder="0" scrolling="no">***</iframe>
                </td>
              </tr>
          </tbody></table></td>
        </tr>
      </tbody></table>
      <table align="center" background="images/center2.jpg" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody><tr>
          <td width="10" height="104"><img src="images/left3.jpg"></td>
          <td width="952" align="right" valign="top"><table background="images/center2.jpg" border="0" cellpadding="0" cellspacing="0" height="29" width="100%">
            <tbody><tr>
              <td height="29" align="right"><span onmousedown="document.getElementById('contframe').contentWindow.scrollByLines(-5)" style="cursor:pointer"><img src="images/frm_up.jpg"></span>
								<span	onmousedown="document.getElementById('contframe').contentWindow.scrollByLines(5)" style="cursor:pointer"><img src="images/frm_dwn.jpg"></span>&nbsp;</td>
            </tr>
          </tbody></table>            
            <table background="images/center4.jpg" border="0" cellpadding="0" cellspacing="0" height="73" width="740">
            <tbody><tr>
              <td width="668" height="73" align="center"><span class="style2">Powered by: </span><br />
                <span class="style3">Segworks Technologies Corporation</span></td>
              <td width="72" align="right" valign="top"><img src="images/top.jpg" usemap="#Map" border="0" height="73" width="44" />
                <map name="Map" id="Map">
                  <area shape="rect" coords="0,25,28,53" href="#" />
                </map></td>
            </tr>
          </tbody>
            </table></td>
          <td width="10"><img src="images/right3.jpg"></td>
        </tr>
      </tbody></table>
      </td>
  </tr>
</tbody></table>
<!-- 1144654829 -->
</body></html>