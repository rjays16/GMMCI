<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$lang_tables = array('departments.php');
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'global_conf/areas_allow.php');

//FIXME - user management
/*if($target =='admin') {
	$allowedarea = &$allow_area['social_manage'];
} else {
	$allowedarea = &$allow_area['social_reports'];
}*/
$allowedarea=&$allow_area['admit'];
$append=URL_REDIRECT_APPEND.'&from=pass'; 

if(!session_is_registered('sess_user_origin')) session_register('sess_user_origin');


switch($target){
	case 'entry':
		$title = 'Social Service - Classify';
	   $breakfile = 'social_service_main.php'.URL_APPEND;
		$fileforward='social_service_search.php'.$append."&user_ck=$user_ck&from=$from"; 
	   $lognote='Social service ok';
		$allowedarea=array('_a_1_ssclassifypatient');
	   break;
	case 'search':
	   $title = 'Social Service - Search';
		$breakfile = 'social_service_main.php'.URL_APPEND;
		$fileforward='social_service_search.php'.$append."&user_ck=$user_ck&from=$from";
	   $lognote='Social service search ok';
		$allowedarea=array('_a_1_ssclassifylist');
	   break;
	case 'reports':
	   $title = 'Social Service - Reports';
		$breakfile = 'social_service_main.php'.URL_APPEND;
		$fileforward = 'social_service_reports.php'.$append."&user_ck=$user_ck&from=$from";
	   $lognote ='Social service Reports ok';	
		$allowedarea=array('_a_1_ssreports');
	   break;
	case 'list':
		$title = 'Social Service - Patient List';
		$breakfile = 'social_service_main.php'.URL_APPEND;
	   $fileforward = 'social_service_list.php'.$append."&user_ck=$user_ck&from=$from";
	   $lognote = 'Social service list ok';
		$allowedarea=array('_a_1_ssclassifylist');
	   break;
	case 'admin':
		$title = 'Social Service - Management';
		$breakfile = 'social_service_main.php'.URL_APPEND;
	   $fileforward = 'administrator/social_service_admin.php'.$append."&user_ck=$user_ck&from=$from";
	   $lognote = 'Social service admin ok';
		$allowedarea=array('_a_1_ssadmin');
	   break;
	case 'show':
		$title = 'Social Service - Patient List';
		$breakfile = 'social_service_main.php'.URL_APPEND;
	  $fileforward = 'social_service_show.php'.$append."&user_ck=$user_ck&from=$from&pid=$pid&encounter_nr=$encounter_nr&origin=patreg_reg&mode=entry";
	  $lognote = 'Social service list ok';
		$allowedarea=array('_a_1_ssclassifylist');
	break;
	   
	#added by VAN 07-05-08
	case 'modifier':
		$title = 'Social Service - Modifier Management';
		$breakfile = 'social_service_main.php'.URL_APPEND;
	    $fileforward = 'administrator/social_service_modifier.php'.$append."&user_ck=$user_ck&from=$from";
	    $lognote = 'Social service modifier ok';
		$allowedarea=array('_a_1_ssadmin');
	   break;
	#-----------------------   
	
	default: 
	   $target='entry';
		$allowedarea=array('_a_1_ssclassifylist');
		$lognote='Social service ok';
		$fileforward='social_service_search.php'.$append.'&from='.$from;
		//$fileforward='social_service_start.php'.$append;				
} // end switch	



$thisfile=basename(__FILE__);
//$breakfile='startframe.php'.URL_APPEND;
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

$local_user='aufnahme_user';
$userck = 'aufnahme_user';

setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php'); 
setcookie(ck_2level_sid.$sid,'');

# reset the user origin
//$HTTP_SESSION_VARS['sess_user_origin']='';

require($root_path.'include/inc_passcheck_internchk.php');

if ($pass=='check') 	
	include($root_path.'include/inc_passcheck.php');

//$errbuf=$LDMedocs;

$errbuf = $swSocialService; 
$minimal = 1;

require_once($root_path.'include/inc_config_color.php');
require($root_path.'include/inc_passcheck_head.php');
?>

<BODY  onLoad="document.passwindow.userid.focus();" bgcolor=<?php echo $cfg['body_bgcolor']; ?>
<?php if (!$cfg['dhtml']){ echo ' link='.$cfg['idx_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['idx_txtcolor']; } ?>>

<!---removed, 2007-10-04 FDP
<FONT    SIZE=-1  FACE="Arial">

<P>
<----until here. then added--->
<table cellspacing=0 class="titlebar" border=0>
	<tr valign=top class="titlebar">
		<td bgcolor="#e4e9f4" valign="bottom">
		&nbsp; &nbsp;
<!---until here------>

<?php
if($cfg['dhtml'])
 {
	switch($target){
		case 'entry': $buf = $swSocialService; break;
		case 'search': $buf = $swSocialService; break;
		default: $target="entry"; $buf= $swSocialService; 		
		}
    echo '
          <script language=javascript>
            <!--
             if (window.screen.width) 
 			{ if((window.screen.width)>1000) document.write(\'<img '.createComIcon($root_path,'people.gif','0','absmiddle').'><FONT  COLOR="'.$cfg['top_txtcolor'].'"  SIZE=6  FACE="verdana"> <b>'.$buf.'</b></font>\');}
 		    //-->
 		  </script>';
 }
?>
<!---also added--->
		</td>
	</tr>
</table>
<!----until here---FDP--->
   
<table width=100% border=0 cellpadding="0" cellspacing="0"> 
<!--
<tr>
<td colspan=3><?php if($target=="entry") echo '<img '.createLDImgSrc($root_path,'newdata-b.gif','0').' alt="'.$LDAdmit.'">';
								else{ echo'<a href="social_service_pass.php?sid='.$sid.'&target=entry&lang='.$lang.'"><img '.createLDImgSrc($root_path,'newdata-gray.gif','0').' alt="'.$LDAdmit.'"'; if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
							if($target=="search") echo '<img '.createLDImgSrc($root_path,'such-b.gif','0').' alt="'.$LDSearch.'">';
								else{ echo '<a href="medocs_pass.php?sid='.$sid.'&target=search&lang='.$lang.'"><img '.createLDImgSrc($root_path,'such-gray.gif','0').' alt="'.$LDSearch.'" ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
/*							if($target=="archiv") echo '<img '.createLDImgSrc($root_path,'arch-blu.gif','0').'  alt="'.$LDArchive.'">';
								else{ echo '<a href="medocs_pass.php?sid='.$sid.'&target=archiv&lang='.$lang.'"><img '.createLDImgSrc($root_path,'arch-gray.gif','0').' alt="'.$LDArchive.'" ';if($cfg['dhtml'])echo'style=filter:alpha(opacity=70) onMouseover=hilite(this,1) onMouseOut=hilite(this,0)'; echo '></a>';}
*/						?></td>
</tr>
</table>

 -->
<?php require($root_path.'include/inc_passcheck_mask.php') ?>  

<p>
<?php
require($root_path.'include/inc_load_copyrite.php');
?>
<!---removed, 2007-10-04 FDP
</FONT>
---that only--->
</BODY>
</HTML>