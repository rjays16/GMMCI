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
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'global_conf/areas_allow.php');

#$allowedarea=&$allow_area['admit'];
$allowedarea=&$allow_area['report'];
$append=URL_REDIRECT_APPEND.'&from=pass'; 

if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');
/*
switch($target)
{
	case 'entry':$fileforward='medocs_start.php'.$append; 
						$lognote='Medocs ok';
						break;
	case 'search':$fileforward='medocs_data_search.php'.$append; 
						$lognote='Medocs search ok';
						break;
	case 'archiv':$fileforward='medocs_archive.php'.$append;
						$lognote='Medocs archive ok';
						 break;
	default: $target='entry';
				$lognote='Medocs ok';
				$fileforward='medocs_start.php'.$append;
}
*/

$target='report';
$lognote='Report ok';
$fileforward='seg_report_generator.php'.$append;

$thisfile=basename(__FILE__);
//$breakfile='startframe.php'.URL_APPEND;
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

$userck='medocs_user';

setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php'); 
setcookie(ck_2level_sid.$sid,'');

# reset the user origin
$HTTP_SESSION_VARS['sess_user_origin']='';

#added by VAN 02-23-08
#$HTTP_SESSION_VARS['inMedocs']=1;

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check') 	
	include($root_path.'include/inc_passcheck.php');

$errbuf='Report Generator';

require($root_path.'include/inc_passcheck_head.php');
?>

<BODY  onLoad="document.passwindow.userid.focus();" bgcolor=<?php echo $cfg['body_bgcolor']; ?>
<?php if (!$cfg['dhtml']){ echo ' link='.$cfg['idx_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['idx_txtcolor']; } ?>>

<!---removed, 2007-10-04 FDP
<FONT    SIZE=-1  FACE="Arial">

<P>
-----until here. then added---->
<table cellspacing="0"  class="titlebar" border=0>
	<tr valign=top  class="titlebar" >
  		<td bgcolor="#e4e9f4" valign="bottom">
		    &nbsp;&nbsp;
<!---until here--->

<?php
if($cfg['dhtml'])
 {
 /*switch($target)
{
	case 'entry':$buf=$LDMedocs; break;
	case 'search':$buf=$LDMedocs; break;
	case 'archiv':$buf=$LDMedocs; break;
	default: $target="entry";$buf=$LDMedocs;
}*/
$target="report";
$buf='Report Generator';

echo '
<script language=javascript>
<!--
 if (window.screen.width) 
 { if((window.screen.width)>1000) document.write(\'<img '.createComIcon($root_path,'penpaper.gif','0','top').'><FONT  COLOR="'.$cfg['top_txtcolor'].'"  SIZE=6  FACE="verdana"> <b>'.$buf.'</b></font>\');}
 //-->
 </script>';
 }
 ?>
<!---also added--->
		</td>
	</tr>
</table>
<!----until here---FDP--->
  
<?php require($root_path.'include/inc_passcheck_mask.php') ?>  

<p>
<?php
require($root_path.'include/inc_load_copyrite.php');
?>
</FONT>
</BODY>
</HTML>
