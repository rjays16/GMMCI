<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','aufnahme.php');
# Resolve the local user based on the origin of the script
//require_once('include/inc_local_user.php');
$breakfile = 'social_service_pass.php';
$local_user = 'medocs_user';

require($root_path.'include/inc_front_chain_lang.php');

/* If patient nr is invallid jump to registration search module*/
/*if(!isset($pid) || !$pid)
{
	header('Location:patient_register_search.php'.URL_APPEND.'&origin=admit');
	exit;
}
*/
require_once($root_path.'include/inc_date_format_functions.php');

$thisfile=basename(__FILE__);

//if($origin=='patreg_reg') $returnfile='patient_register_show.php'.URL_APPEND.'&pid='.$pid;

# Set break file
//require('include/inc_breakfile.php');
//if($HTTP_SESSION_VARS['sess_user_origin']=='admission') {
//	$breakfile=$root_path.'modules/registration_admission/aufnahme_daten_zeigen.php'.URL_APPEND.'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'];
//}elseif($HTTP_SESSION_VARS['sess_user_origin']=='registration'){
//	$breakfile=$root_path.'modules/registration_admission/show_medocs.php'.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'];
//}elseif($HTTP_COOKIE_VARS['ck_login_logged'.$sid]){
//	$breakfile=$root_path.'main/startframe.php';
//}else{
	//$breakfile='medocs_pass.php';
	$breakfile='social_service_pass.php';
//}

# Patch for break urls that have lang param already

if(!stristr($breakfile,'lang=')) $breakfile.=URL_APPEND;

/*

if(!session_is_registered('sess_pid')) session_register('sess_pid');
if(!session_is_registered('sess_full_pid')) session_register('sess_full_pid');
if(!session_is_registered('sess_en')) session_register('sess_en');
if(!session_is_registered('sess_full_en')) session_register('sess_full_en');
*/
//$headframe_title=$LDMedocs;
$headframe_title=$swSocialService;
//require('./gui_bridge/default/gui_medocs_start.php');
# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
 $smarty->assign('sToolbarTitle',$headframe_title);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_start.php')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$headframe_title);

 if(!$encounter_nr && !$pid){
	$onLoadJs='onLoad="if(document.searchform.searchkey.focus) document.searchform.searchkey.focus();"';
}

 if(defined('MASCOT_SHOW') && MASCOT_SHOW==1){
	$onLoadJs='onLoad="if (window.focus) window.focus();"';
	
}

 # Onload Javascript code
 $smarty->assign('sOnLoadJs',$onLoadJs);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_entry.php')");

  # hide return button
 $smarty->assign('pbBack',FALSE);

# Load tabs

$target='search';
//require('./gui_bridge/default/gui_tabs_medocs.php');

# Buffer page output

ob_start();

?>

<script  language="javascript">
<!-- 
function setsex(d)
{
	s=d.selectedIndex;
	t=d.options[s].text;
	if(t.indexOf("Frau")!=-1) document.aufnahmeform.sex[1].checked=true;
	if(t.indexOf("Herr")!=-1) document.aufnahmeform.sex[0].checked=true;
	if(t.indexOf("-")!=-1){ document.aufnahmeform.sex[0].checked=false;document.aufnahmeform.sex[1].checked=false;}
}

function settitle(d)
{
	if(d.value=="m") document.aufnahmeform.anrede.selectedIndex=2;
	else document.aufnahmeform.anrede.selectedIndex=1;
}

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

-->
</script>

<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>

<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>

<?php 

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

#Load tabs

$target='entry';
//include('./gui_bridge/default/gui_tabs_medocs.php');

# Buffer the page output

ob_start();

?>

<ul>

<?php 
/* If the origin is admission link, show the search prompt */
if(!isset($pid) || !$pid)
{
/* Set color values for the search mask */

$searchmask_bgcolor="#f3f3f3";
$searchprompt=$LDEntryPrompt;
$entry_block_bgcolor='#fff3f3';
$entry_border_bgcolor='#6666ee';
$entry_body_bgcolor='#ffffff';

?>
<table border=0>
  <tr>
    <td valign="bottom"><img <?php echo createComIcon($root_path,'angle_down_l.gif','0') ?>></td>
    <td><font color="#000099" SIZE=3  FACE="verdana,Arial"> <b><?php echo $LDPlsSelectPatientFirst ?></b></font></td>
    <td><img <?php echo createMascot($root_path,'mascot1_l.gif','0','absmiddle') ?>></td>
  </tr>
</table>

 <table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
     <tr>
       <td>
	   <?php
	        /* set the script for searching */
			//$search_script='medocs_data_search.php';
		   	$search_script='social_service_search.php';
			$user_origin='admit';

		   $seg_show_ICD_ICPM_options = true;   # burn added : May 3, 2007
            include($root_path.'include/inc_patient_searchmask.php');
		   $seg_show_ICD_ICPM_options = false;   # burn added : May 3, 2007
	   ?>
	</td>
     </tr>
   </table>

<?php 
}
?>

<p>
<a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> alt="<?php echo $LDCancelClose ?>"></a>
</ul>
<p>

<?php

$sTemp = ob_get_contents();
$smarty->assign('sMainDataBlock',$sTemp);

ob_end_clean();

$smarty->assign('sMainBlockIncludeFile','medocs/main_plain.tpl');

$smarty->display('common/mainframe.tpl');


?>