<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$lang_tables[]='prompt.php';
$lang_tables[]='person.php';
//define('LANG_FILE','aufnahme.php');
define ('LANG_FILE','social_service.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');

#--------------added 03-07-07----------------
include_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
#--------------------------------------------

$thisfile=basename(__FILE__);
//$breakfile='patient.php';

#added by VAN 04-05-08
$breakfile = 'social_service_search.php';
#commented by VAN 04-05-08
/*
if($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile=$root_path."main/startframe.php".URL_APPEND;
	else $breakfile="patient.php".URL_APPEND."&target=entry";
*/
$admissionfile='aufnahme_start.php'.URL_APPEND;

# Resolve PID
if((!isset($pid)||!$pid)&&$HTTP_SESSION_VARS['sess_pid']) $pid=$HTTP_SESSION_VARS['sess_pid'];

# Save session data
$HTTP_SESSION_VARS['sess_path_referer']=$top_dir.$thisfile;
$HTTP_SESSION_VARS['sess_file_return']=$thisfile;
$HTTP_SESSION_VARS['sess_pid']=$pid;
//$HTTP_SESSION_VARS['sess_full_pid']=$pid+$GLOBAL_CONFIG['person_id_nr_adder'];
$HTTP_SESSION_VARS['sess_parent_mod']='registration';
$HTTP_SESSION_VARS['sess_user_origin']='registration';
# Reset the encounter number
$HTTP_SESSION_VARS['sess_en']=0;

# Create the person show GUI
require_once($root_path.'include/care_api_classes/class_gui_person_show.php');
$person = & new GuiPersonShow;

# Set PID to load the data
$person->setPID($pid);

# Import the current encounter number
$current_encounter = $person->CurrentEncounter($pid);
#echo "sql = ".$person->sql;
#echo $encounter_nr;
# Import the death date
$death_date = $person->DeathDate();

#Load dept info of the user who logs in
#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);


#comment by mark 06-08-2007
/*require_once($root_path.'include/care_api_classes/class_care_encounter.php');
$enc_obj = new Encounter;
$enc_Info = getEncounteInfo($_GET['encounter_nr']);
*/
require_once($root_path.'include/care_api_classes/class_social_service.php');
$ss_obj = new SocialService;
$ss_info = $ss_obj->getSServiceInfo($encounter_nr);

//echo "ss_obj=".$ss_obj."<br>";
//echo "ss_info=".$ss_info."<br>";
//echo "sql=".$ss_obj->sql;
//print_r($ss_info);

if($ss_info){
	$mode = 'show';
}else{
	$mode = 'entry';
}

# Load GUI page
//include('./gui_bridge/default/gui_person_reg_show.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
// $smarty->assign('sToolbarTitle',$LDPatientRegister);
 $smarty->assign('sToolbarTitle',"$swSocialService :: $swSSPersonInfo");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
// $smarty->assign('title',$LDPatientRegister);
 $smarty->assign('title',$swSocialService);

 # Onload Javascript code
 $smarty->assign('sOnLoadJs',"if (window.focus) window.focus();");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('person_admit.php')");

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

# Loads the standard gui tags for the registration display page
//require('./gui_bridge/default/gui_std_tags.php');

# Collect additional javascript code
ob_start();
?>

<script  language="javascript">
<!--
<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

function popRecordHistory(table,pid) {
	urlholder="./record_history.php<?php echo URL_REDIRECT_APPEND; ?>&table="+table+"&pid="+pid;
	HISTWIN<?php echo $sid ?>=window.open(urlholder,"histwin<?php echo $sid ?>","menubar=no,width=400,height=550,resizable=yes,scrollbars=yes");
}
//-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Append the extra javascript to JavaScript block
$smarty->append('JavaScript',$sTemp);

# Load the tabs
$tab_bot_line='#66ee66';
//require('./gui_bridge/default/gui_tabs_patreg.php');

# Display the data
$sRegForm = $person->create();

$smarty->assign('sRegForm',$sRegForm);

#echo "dept belong =".$dept_belong['id'];
#reg_options of Admission and Medocs department is invisible if not yet currently admitted.
#---------edited by vanessa 03-26-07---------
/*
if (($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs")){		
#	if(((isset($current_encounter)&&$current_encounter))||($dept_belong['id']=="Medocs")){
	# Load and display the options table
		ob_start();
			require('./gui_bridge/default/gui_patient_reg_options.php');
			$sTemp = ob_get_contents();
		ob_end_clean();
		$smarty->assign('sRegOptions',$sTemp);
#	}
}elseif (($dept_belong['id']=="ER")||($dept_belong['id']=="OPD-Triage")){
	ob_start();
		require('./gui_bridge/default/gui_patient_reg_options.php');
		$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->assign('sRegOptions',$sTemp);

}*/




ob_start();
?>
	<table border =0 cellpadding=0 cellspacing=0 bgcolor="#f4f7fb">
		<tr>
			<td width="32" align=center>&nbsp;</td>
			<td colspan="2" align=center background='<?php echo createComIcon($root_path,'opt_tl.jpg','0','',FALSE) ?>'>&nbsp;</td>
			<td vAlign=top >&nbsp;</td>			
		</tr>
		<tr>
			<td rowspan="16" align=center bgcolor="#F4F7FB" background='<?php echo createComIcon($root_path,'opt_r.jpg','0','',FALSE) ?>'>&nbsp;</td> 
			<td width="32" align=center><img <?php echo createComIcon($root_path,'pdata.gif','0','',FALSE) ?>></td>
			<td vAlign=top >
				<font face="Verdana,Helvetica,Arial" size=2> 
				<nobr>
					<a href="social_service_show.php<?php echo URL_APPEND ?>&encounter_nr=<?php echo $current_encounter ?>&origin=patreg_reg&mode=<?=$mode?>"><?="Classify Patient"?></a>
				</nobr> 
				</font>
			</td>
			<td width="10" rowspan="16" vAlign=top background='<?php echo createComIcon($root_path,'opt_t.jpg','0','',FALSE) ?>'>&nbsp;</td>
		</tr>
		<TR>
			<td>&nbsp;</td>
			<td colspan="2"background="<?php echo createComIcon($root_path,'opt_b.jpg','0','',FALSE) ?>">&nbsp;</td>
			<td >&nbsp;</td>
		</TR>				
	</table>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sRegOptions',  $sTemp);

#----------------------------------------------

# If the data is not new , show new search button

if (!$newdata) { 

//	if($target=="search") $newsearchfile='patient_register_search.php'.URL_APPEND;
	if($target=="search") $newsearchfile='social_service_search.php'.URL_APPEND;
		else $newsearchfile='patient_register_archive.php'.URL_APPEND;

	$smarty->assign('pbNewSearch',"<a href=\"$newsearchfile\"><img ".createLDImgSrc($root_path,'new_search.gif','0','absmiddle')."></a>");
}

#-----------added by vanessa 03-24-07-----------
#$smarty->assign('pbUpdateData',"<a href=\"patient_register.php".URL_APPEND."&pid=$pid&update=1\"><img ".createLDImgSrc($root_path,'update_data.gif','0','absmiddle')."></a>");
#echo "dept_belong =".$dept_belong['job_function_title'];
#commented by VAN 07-28-08
/*
if ((stristr($dept_belong['job_function_title'], strtolower('Head'))||stristr($dept_belong['job_function_title'], strtolower('Supervisor'))) != FALSE) {
	$smarty->assign('pbUpdateData',"<a href=\"patient_register.php".URL_APPEND."&pid=$pid&update=1\"><img ".createLDImgSrc($root_path,'update_data.gif','0','absmiddle')."></a>");
}
*/
# If currently admitted show button link to admission data display
if($current_encounter){   #-----------commented 03-14-07 by vanessa --------

#if(($current_encounter)&&$dept_belong['id']!="OPD-Triage"&&$dept_belong['id']!="ER"){
   #$smarty->assign('pbShowAdmData',"<a href=\"aufnahme_daten_zeigen.php".URL_APPEND."&encounter_nr=$current_encounter&origin=patreg_reg\"><img ".createLDImgSrc($root_path,'admission_data.gif','0','absmiddle')."></a>");
	
# Else if person still living, show button links to admission
}elseif(!$death_date||$death_date==$dbf_nodate){

	
  #--------comment 03-07-07-----------------
  /*
	$smarty->assign('pbAdmitInpatient',"<a href=\"$admissionfile&pid=$pid&origin=patreg_reg&encounter_class_nr=1\"><img ".createLDImgSrc($root_path,'admit_inpatient.gif','0','absmiddle')."></a>");
	$smarty->assign('pbAdmitOutpatient',"<a href=\"$admissionfile&pid=$pid&origin=patreg_reg&encounter_class_nr=2\"><img ".createLDImgSrc($root_path,'admit_outpatient.gif','0','absmiddle')."></a>");
  */	
    
  #------------comment 03-13-07------------------
  /*
  if ($dept_belong['id'] == "ER"){
  		$smarty->assign('pbAdmitOutpatient',"<a href=\"$admissionfile&pid=$pid&origin=patreg_reg&encounter_class_nr=1&dr_nr=0&dept=0&encounter_type=0\"><img ".createLDImgSrc($root_path,'admit_inpatient.gif','0','absmiddle')."></a>");
  }elseif($dept_belong['id'] == "OPD-Triage"){
  		$smarty->assign('pbAdmitOutpatient',"<a href=\"$admissionfile&pid=$pid&origin=patreg_reg&encounter_class_nr=2&dr_nr=0&dept=0&encounter_type=0\"><img ".createLDImgSrc($root_path,'admit_outpatient.gif','0','absmiddle')."></a>");
  }elseif($HTTP_SESSION_VARS['sess_user_name']=="Administrator"){
  		$smarty->assign('pbAdmitOutpatient',"<a href=\"$admissionfile&pid=$pid&origin=patreg_reg&encounter_class_nr=1&dr_nr=0&dept=0&encounter_type=0\"><img ".createLDImgSrc($root_path,'admit.gif','0','absmiddle')."></a>");	
  } 
  */	
}

#echo "patient_register_show.php : allow_entry = '".$allow_entry."' <br> \n";
	if ($allow_entry){   # burn added: March 12, 2007

		# Create new button to fresh input form
		$sNewRegBuffer='
		<form action="patient_register.php" method=post>
		<input type=submit value="'.$LDRegisterNewPerson.'">
		<input type=hidden name="sid" value="'.$sid.'">
		<input type=hidden name="lang" value="'.$lang.'">
		</form>';
	}
$smarty->assign('pbRegNewPerson',$sNewRegBuffer);

# Assign help links
//$smarty->assign('sSearchLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="patient_register_search.php'.URL_APPEND.'">'.$LDPatientSearch.'</a>');
//$smarty->assign('sArchiveLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="patient_register_archive.php'.URL_APPEND.'&newdata=1&from=entry">'.$LDArchive.'</a>');

$sCancel="<a href=";
if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $sCancel.=$breakfile;
//	else $sCancel.='aufnahme_pass.php';
	else $sCancel.='social_service_pass.php';	
$sCancel.=URL_APPEND.'><img '.createLDImgSrc($root_path,'cancel.gif','0').' alt="'.$LDCancelClose.'"></a>';

$smarty->assign('pbCancel',$sCancel);

# Assign the page template to mainframe block
$smarty->assign('sMainBlockIncludeFile','registration_admission/reg_show.tpl');

# Show main frame
$smarty->display('common/mainframe.tpl');


?>