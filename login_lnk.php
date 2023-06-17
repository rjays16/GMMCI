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
$cfg = $USERCONFIG;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
<link rel="stylesheet" href="images/template_css.css" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #FFFFFF;
}
.style1 {
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 12px;
	color: #446573;
}
a {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 12px !important;
	color: #364B5F !important;
}
a:hover {
	color: #FF6600 !important;
}
-->
</style></head>
<script language="javascript">
<!--
	function toggleBanner() {
		if (window.parent.document.getElementById('banner').height==124) {
			document.getElementById('bannerRow').style.display = "none";
			window.parent.document.getElementById('banner').height = 25;
			window.parent.resizeContent();
		}
		else {
			document.getElementById('bannerRow').style.display = "";
			window.parent.document.getElementById('banner').height=124;
			window.parent.resizeContent();
		}
	}
-->
</script>

<body>
	<table align="center" border="0" cellpadding="0" cellspacing ="0" width="100%" height="100" bgcolor="#666666">
		<tbody>
			<tr id="bannerRow">
				<td colspan="4">
					<!-- floating logo-->
					<div id="divTopRight" style="position:absolute">
						<img src="images/seglogo.png"/>
					</div>
					<div align="left">
<script type="text/javascript">
<!--
var ns = (navigator.appName.indexOf("Netscape") != -1);
var d = document;
var px = document.layers ? "" : "px";
function JSFX_FloatDiv(id, sx, sy)
{
	var el=d.getElementById?d.getElementById(id):d.all?d.all[id]:d.layers[id];
	window[id + "_obj"] = el;
	if(d.layers)el.style=el;
	el.cx = el.sx = sx;el.cy = el.sy = sy;
	el.sP=function(x,y){this.style.left=x+px;this.style.top=y+px;};
	el.flt=function()
	{
		var pX, pY;
		pX = (this.sx >= 0) ? 0 : ns ? innerWidth :
		document.documentElement && document.documentElement.clientWidth ?
		document.documentElement.clientWidth : document.body.clientWidth;
		pY = ns ? pageYOffset : document.documentElement && document.documentElement.scrollTop ?
		document.documentElement.scrollTop : document.body.scrollTop;
		if(this.sy<0)
		pY += ns ? innerHeight : document.documentElement && document.documentElement.clientHeight ?
		document.documentElement.clientHeight : document.body.clientHeight;
		this.cx += (pX + this.sx - this.cx)/8;this.cy += (pY + this.sy - this.cy)/8;
		this.sP(this.cx, this.cy);
		setTimeout(this.id + "_obj.flt()", 40);
	}
	return el;
}
JSFX_FloatDiv("divTopRight", -220, 2).flt();
-->
</script>

						<table width="100%" border="0" cellspacing="0" cellpadding="0"  >
							<tr>
								<td bgcolor="#000033" width="1" background="images/gmmci.png" height="103"> 
							
									<!--<object classid="clsid:166B1BCA-3F9C-11CF-8075-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,0,0" >
										<param name="src" value="images/segbanner2_a.swf">
										<embed src="images/segbanner2_a.swf" pluginspage="http://www.macromedia.com/shockwave/download/" width="508" height="72"></embed>
									</object>-->
								</td>
								</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td width="968" height="24" valign="top" background="images/bar_05.gif" bgcolor="#9CB5D1">
					<table width="98%" height="24" border="0" align="center" cellpadding="0" cellspacing="0">
						<tbody>
							<tr>
								<td width="2%"><img src="images/bar_03.jpg" onClick="toggleBanner()" style="cursor:pointer"></td>
								<td width="1%" background="images/bar_05.gif" bgcolor="#9CB5D1" valign="middle" nowrap="nowrap" style="<?= $_SESSION['sess_login_username'] ? '' : 'display:none' ?>">
									<div style="padding-right:6px;margin-top:-4px;background:url(images/menu_separator.gif) right 0 repeat-y transparent" align="left" id="login_username">
										<?= "Welcome, <b>".$_SESSION['sess_login_username']."</b>" ?>
									</div>
								</td>
								<td width="98%" background="images/bar_05.gif" bgcolor="#9CB5D1" valign="middle">
									<div style="margin-top:-5px;padding-left:4px;">
										<a id="login_link" href="main/login.php" target="contframe" style="display:<? echo $_SESSION['sess_login_username'] ? "none":"" ?>">Login</a>
										<a id="logout_link" href="main/logout_confirm.php" target="contframe" style="display:<? echo $_SESSION['sess_login_username'] ? "":"none" ?>">Logout</a>
									</div>
								</td>
							 </tr>
						 </tbody>
					 </table>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>