<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
//error_reporting(E_WARNING);
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

//if(!session_is_registered('sess_nursing_station')) session_register('sess_nursing_station');
//if(isset($station)&&!empty($station)) $HTTP_SESSION_VARS['sess_nursing_station']=$station;

$allowedarea=&$allow_area['wards'];

//$fileforward="nursing-station.php".URL_REDIRECT_APPEND."&edit=$edit&retpath=$retpath&station=".$HTTP_SESSION_VARS['sess_nursing_station'];
#$fileforward="nursing-station.php".URL_REDIRECT_APPEND."&fwd_nr=$fwd_nr&edit=$edit&retpath=$retpath&station=$station&ward_nr=$ward_nr&pday=$pday&pmonth=$pmonth&pyear=$pyear";
#edited by VAN 04-12-08
#echo "key = ".$_GET['key'];
#echo "<br>pagekey = ".$_GET['pagekey'];

$fileforward="nursing-station.php".URL_REDIRECT_APPEND."&fwd_nr=$fwd_nr&edit=$edit&retpath=$retpath&pid=$pid&station=$station&ward_nr=$ward_nr&key=".$_GET['key']."&pagekey=".$_GET['pagekey']."&pday=$pday&pmonth=$pmonth&pyear=$pyear";
//$fileforward="nursing-station.php?sid=$sid&edit=$edit&retpath=$retpath&station=$station";
$thisfile="nursing-station-pass.php";
if($retpath=="quick") $breakfile="nursing-schnellsicht.php".URL_APPEND;
 else $breakfile="nursing.php".URL_APPEND;

$lognote="$LDNursingStation ".strtoupper($station)." ok";

$HTTP_SESSION_VARS['sess_parent_mod']='';

$userck='ck_pflege_user';

//echo $fileforward;
//reset cookie;
// reset all 2nd level lock cookies
setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php'); 
setcookie('ck_2level_sid'.$sid,'',0,'/');

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check') 	
	include($root_path.'include/inc_passcheck.php');

$errbuf="$LDNursingStation ".strtoupper($station);

//$minimal=0;

require($root_path.'include/inc_passcheck_head.php');
?>
<BODY  onLoad="document.passwindow.userid.focus();" bgcolor=<?php echo $cfg['body_bgcolor']; ?>
<?php if (!$cfg['dhtml']){ echo ' link='.$cfg['idx_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['idx_txtcolor']; } ?>>

<FONT    SIZE=-1  FACE="Arial">

<!---replaced, 2007-10-05 FDP--------
<P>
<img <?php echo createComIcon($root_path,'wheelchair.gif','0','top') ?>>
<FONT  COLOR="<?php echo $cfg[top_txtcolor] ?>"  SIZE=6  FACE="verdana"> <b><?php echo "$LDNursingStation ".stripslashes($station); ?></b></font>
-----with this--------->
<table cellspacing="0"  class="titlebar" border=0>
	<tr valign=top  class="titlebar" >
  		<td bgcolor="#e4e9f4" valign="bottom">
		    &nbsp;&nbsp;
			<img <?php echo createComIcon($root_path,'wheelchair.gif','0','absmiddle') ?>>
			<font color="<?php echo $cfg[top_txtcolor] ?>"  size=6  face="verdana"> <b><?php echo "$LDNursingStation :: ".stripslashes(strtoupper($station)) ?></b></font>
		</td>
	</tr>
</table>
<!----until here only, 2007-10-05 FDP--->

<table width=100% border=0 cellpadding="0" cellspacing="0"> 

<?php require($root_path.'include/inc_passcheck_mask.php') ?>  

<?php
require($root_path.'include/inc_load_copyrite.php');
?>

</FONT>


</BODY>
</HTML>
