<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

/* Define language and local user for this module */
$thisfile=basename(__FILE__);
$lang_tables[]='prompt.php';
define('LANG_FILE','aufnahme.php');

//set break file
//$breakfile='medocs_pass.php';
$local_user = 'aufnahme_user';

//include ajax common file . .
#require($root_path.'modules/medocs/ajax/medocs_common.php'); //add by mark

// include personell and department class
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
$pers_obj=new Personell;

//include social service class
require_once($root_path.'include/care_api_classes/class_social_service.php');
$ss = new SocialService;

//set mode = show if not yet set
if(!isset($mode)){
	$mode='show';
//Save new diagnosis and procedure to care_notes and care_encounter_diagnosis  
} elseif(($mode=='create'||$mode=='update')) {
	# Prepare the posted data for saving in databank
	include_once($root_path.'include/inc_date_format_functions.php');
	$_POST['ss_notes'] = trim($_POST['ss_notes']);
	$_POST['create_time'] = date('Y-m-d H:i:s');
	$_POST['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
	
	if($mode == 'create') $_POST['ss_history'] = 'Entry: '.date('Y-m-d H:i:s').' '.$HTTP_SESSION_VARS['sess_user_name'];
	elseif ($mode == 'update') $_POST['ss_history'] = 'Update: '.date('Y-m-d H:i:s').' '.$HTTP_SESSION_VARS['sess_user_name'];	
	if($ss->saveSServiceInfo($_POST)){
		header("location:".$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&mode=show");
		exit;
	}else{
		echo "$ss->sql<br>$LDDbNoSave";		
	}
	
}// End of (if mode='create' || mode='update') mode = create new record

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_person.php');

$admissionfile='aufnahme_start.php'.URL_APPEND;

if((!isset($pid)||!$pid)&&$HTTP_SESSION_VARS['sess_pid']) $pid=$HTTP_SESSION_VARS['sess_pid'];
	elseif($pid) $HTTP_SESSION_VARS['sess_pid']=$pid;

/* Default path for fotos. Make sure that this directory exists! */
$default_photo_path=$root_path.'fotos/registration';
$photo_filename='nopic';

if(!isset($user_id) || !$user_id)
{
    $user_id=$local_user.$sid;
    $user_id=$$user_id;
}
 
if(isset($pid) && ($pid!='')) {
	$person_obj=new Person($pid);

	if($data_obj=&$person_obj->getAllInfoObject()){
		$zeile=$data_obj->FetchRow();
		while(list($x,$v)=each($zeile))	$$x=$v;       
	}
		
	$sql='SELECT * FROM care_appointment WHERE pid='.$pid;
		
	if($result=$db->Execute($sql)){
		$row=$result->RecordCount();
	}
}

require_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('person_%');
$glob_obj->getConfig('patient_%');

/* Check whether config foto path exists, else use default path */			
$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;
require_once($root_path.'include/inc_photo_filename_resolve.php');

if($HTTP_SESSION_VARS['sess_parent_mod']=='admission') {
	$parent_admit=true;
	$page_title=$LDAdmission;
}else{
	$parent_admit=false;
	$page_title=$LDPatientRegister;
}

//set page title 
//$page_title=$LDMedocs;
$page_title = $swSocialService;

# Load the entire encounter data
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter($encounter_nr);
$enc_obj->loadEncounterData();

# Get encounter class
$enc_class=$enc_obj->EncounterClass();
/*if($enc_class==2)  $HTTP_SESSION_VARS['sess_full_en']=$GLOBAL_CONFIG['patient_outpatient_nr_adder']+$encounter_nr;
	else $HTTP_SESSION_VARS['sess_full_en']=$GLOBAL_CONFIG['patient_inpatient_nr_adder']+$encounter_nr;
*/
$HTTP_SESSION_VARS['sess_full_en']=$encounter_nr;
	
if(empty($encounter_nr)&&!empty($HTTP_SESSION_VARS['sess_en'])){
	$encounter_nr=$HTTP_SESSION_VARS['sess_en'];
}elseif($encounter_nr) {
	$HTTP_SESSION_VARS['sess_en']=$encounter_nr;
}

$enc_Info = $enc_obj->getEncounterInfo($encounter_nr);

if(!empty($HTTP_SESSION_VARS['sess_login_userid']))
	$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
	$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
$userDeptInfo = $dept_obj->getUserDeptInfo($seg_user_name);

//It show list of diagnosis and procedures
if($mode=='show'){

	$rows = $ss->getSSInfo();
	$ssData = $ss->getSServiceInfo($encounter_nr);
	
	//Show the detailed description of diagnosis and procedures
}elseif(($mode=='details')&&!empty($encounter_nr)){

	// insert code here .. 

}else{
	$rows = $ss->getSSInfo();	 	
}// end of elseif statement


//$subtitle=$LDMedocs;
$subtitle = $swSocialService;
		
$buffer=str_replace('~tag~',$title.' '.$name_last,$LDNoRecordFor);
$norecordyet=str_replace('~obj~',strtolower($subtitle),$buffer); 
$HTTP_SESSION_VARS['sess_file_return']=$thisfile;

# Patch for break urls that have lang param already
//$breakfile = 'social_service_pass.php';
$breakfile = 'social_service_search.php';
if(!stristr($breakfile,'lang=')) $breakfile.=URL_APPEND;

if($mode=='show') $glob_obj->getConfig('medocs_%');

/* Load GUI page */
$returnfile=$HTTP_SESSION_VARS['sess_file_return'];

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
//require($root_path.'modules/medocs/ajax/medocs_common.php');

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

if($parent_admit) $sTitleNr= ($HTTP_SESSION_VARS['sess_full_en']);
	else $sTitleNr = ($HTTP_SESSION_VARS['sess_full_pid']);

# Title in the toolbar
 $smarty->assign('sToolbarTitle',"$page_title $encounter_nr");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"$page_title $encounter_nr");

 # Onload Javascript code
 $onLoadJs='onLoad="if (window.focus) window.focus();"';
 $smarty->assign('sOnLoadJs',$onLoadJs);
 
 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_entry.php')");

  # href for return button
 $smarty->assign('pbBack',$returnfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&target='.$target.'&mode=show&type_nr='.$type_nr);

# Buffer extra javascript code

ob_start();

?>

<script  language="javascript">
<!-- 

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

function popRecordHistory(table,pid) {
	urlholder="./record_history.php<?php echo URL_REDIRECT_APPEND; ?>&table="+table+"&pid="+pid;
	HISTWIN<?php echo $sid ?>=window.open(urlholder,"histwin<?php echo $sid ?>","menubar=no,width=400,height=550,resizable=yes,scrollbars=yes");
}
-->
</script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype1.5.js"></script>
<!-- Calendar js -->
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>

<!-- YUI Library -->
<script language="javascript" src="<?=$root_path?>js/yui/yahoo/yahoo-min.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/event/event-min.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/dom/dom.js"></script>

<?php 
		
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
//require('./gui_bridge/default/gui_tabs_medocs.php');
if($enc_obj->Is_Discharged()){

	$smarty->assign('is_discharged',TRUE);
	$smarty->assign('sWarnIcon',"<img ".createComIcon($root_path,'warn.gif','0','absmiddle').">");
	$smarty->assign('sDischarged',$LDPatientIsDischarged);
}

# Set the table columns� classes
$smarty->assign('sClassItem','class="adm_item"');
$smarty->assign('sClassInput','class="adm_input"');

$smarty->assign('LDCaseNr',$LDAdmitNr);

$smarty->assign('sEncNrPID',$HTTP_SESSION_VARS['sess_en']);

$smarty->assign('img_source',"<img $img_source>");

$smarty->assign('LDTitle',$LDTitle);
$smarty->assign('title',$title);
$smarty->assign('LDLastName',$LDLastName);
$smarty->assign('name_last',$name_last);
$smarty->assign('LDFirstName',$LDFirstName);
$smarty->assign('name_first',$name_first);

# If person is dead show a black cross and assign death date

if($death_date && $death_date != DBF_NODATE){
	$smarty->assign('sCrossImg','<img '.createComIcon($root_path,'blackcross_sm.gif','0').'>');
	$smarty->assign('sDeathDate',@formatDate2Local($death_date,$date_format));
}

	# Set a row span counter, initialize with 7
	$iRowSpan = 7;

	if($GLOBAL_CONFIG['patient_name_2_show']&&$name_2){
		$smarty->assign('LDName2',$LDName2);
		$smarty->assign('name_2',$name_2);
		$iRowSpan++;
	}

	if($GLOBAL_CONFIG['patient_name_3_show']&&$name_3){
		$smarty->assign('LDName3',$LDName3);
		$smarty->assign('name_3',$name_3);
		$iRowSpan++;
	}

	if($GLOBAL_CONFIG['patient_name_middle_show']&&$name_middle){
		$smarty->assign('LDNameMid',$LDNameMid);
		$smarty->assign('name_middle',$name_middle);
		$iRowSpan++;
	}

$smarty->assign('sRowSpan',"rowspan=\"$iRowSpan\"");

$smarty->assign('LDBday',$LDBday);
$smarty->assign('sBdayDate',@formatDate2Local($date_birth,$date_format));

$smarty->assign('LDSex',$LDSex);
if($sex=='m') $smarty->assign('sSexType',$LDMale);
	elseif($sex=='f') $smarty->assign('sSexType',$LDFemale);

$smarty->assign('LDBloodGroup',$LDBloodGroup);
if($blood_group){
	$buf='LD'.$blood_group;
	$smarty->assign('blood_group',$$buf);
}

$smarty->assign('LDDate',$LDDate);
$smarty->assign('LDDiagnosis',$LDDiagnosis);
//$smarty->assign('LDTherapy',$LDTherapy);
$smarty->assign('LDTherapy',$segIcpmDesc);

$smarty->assign('LDDetails',$LDDetails);
$smarty->assign('LDBy',$LDBy);

//Add by Mark on March 29, 2007
$smarty->assign('segDept_nr','Department');

$smarty->assign('LDExtraInfo',$LDExtraInfo);
#$smarty->assign('LDInsurance',$LDInsurance);
#$smarty->assign('LDGotMedAdvice',$LDGotMedAdvice);
$smarty->assign('LDYes',$LDYes);
$smarty->assign('LDNo',$LDNo);

#======================================================================================================

#Show list of documents 
if($mode=='show'){	
	if ($rows){
		//$smarty->assign('sDocsBlockIncludeFile','medocs/docslist_frame.tpl');
		$smarty->assign('sFormShow', true);
		$smarty->assign('sDocsBlockIncludeFile', 'social_service/social_service_frame.tpl');
		
		//heading social service
		$smarty->assign('swHeadingSocialService', "Social Service Classification");
		$smarty->assign('sRowClass','class="wardlistrow2" id="others" name="others"');
		$smarty->assign('swClassTypeTxt', "Class Type");
		
		
		//$sstype = $row['service_desc'];
		//$service_code = $ssData['service_code'];
		if($ssRow = $ssData->FetchRow()){
		  echo $ssRow['service_code'];
			$tmp = '';
		  	while($row= $rows->FetchRow()){
				$tmp =$tmp.'<input type="radio" id="service_code" name="service_code" value="'.$row['service_code'].'" ';
				if($ssRow['service_code']!='') $tmp=$tmp.'checked';
				$tmp = $tmp.'>';
				$tmp = $tmp.$row['service_desc']."&nbsp;";
			}
		}
		$smarty->assign('swClassTypeRows',$tmp);

		$smarty->assign('ssNotes','<textarea name="ss_notes" id="ss_notes" cols="70" rows="3" wrap="physical" readonly="readonly">'.trim($aux_notes_d).'</textarea>');
		//$smarty->assign('sDetailsIcon','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=details&type_nr='.$type_nr.'&nr='.$row['nr'].'"><img '.createComIcon($root_path,'info3.gif','0').'></a>');
		ob_start();
	?>
		<input type="hidden" title="Save and Discharged" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>	
	<?php	
		$sTemp = ob_get_contents();
		ob_end_clean();
		$smarty->assign('sHiddenInputs',$sTemp);	
	
	}else{
	
		# Show no record prompt

		$smarty->assign('bShowNoRecord',TRUE);
		$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','absmiddle').'>');
		$smarty->assign('norecordyet',$norecordyet);
	
	}
}elseif($mode=='details'){
	
	# Set the include file

	$smarty->assign('sDocsBlockIncludeFile','medocs/form.tpl');
	
	$smarty->assign('sDiagnosis',nl2br($row['diagnosis']));
#	$smarty->assign('sTherapy',nl2br($row['therapy']));
	$smarty->assign('sTherapy',nl2br($result_icp['therapy']));
	
	//encounter_type=3&4 show Result Disposition
	if($enc_Info['encounter_type']=='3' || $enc_Info['encounter_type']=='4'){
		$smarty->assign('sSetResult',TRUE);
		$smarty->assign('sResult',$rResult['description']);
		$smarty->assign('sDisposition',$rDisp['descrip']);
	}else{
		$smarty->assign('sSetResult',FALSE);
	}

	
# Create a new form for data entry# 
}else{
	# Create a new entry form
	# Set the include file
	$smarty->assign('sDocsBlockIncludeFile', 'social_service/social_service_frame.tpl');
		
	# Set form table as active form
	$smarty->assign('sFormShow', false);

	#heading social service
	$smarty->assign('swHeadingSocialService', "Social Service Classification");
	$smarty->assign('sRowClass','class="wardlistrow2" id="others" name="others"');
	$smarty->assign('swClassTypeTxt', "Class Type");

	# Display radio button for social service classification
	$tmp = '';
	$sstype = $row['service'];
	while($row= $rows->FetchRow()){
		$tmp =$tmp.'<input type="radio" id="service_code" name="service_code" value="'.$row['service_code'].'" ';
		if($service_discount == $row['service_discount']) $tmp = $tmp.'checked';
		$tmp = $tmp.'>';
		$tmp = $tmp.$row['service_desc']."&nbsp;";
	}

	$smarty->assign('swClassTypeRows',$tmp);
	
	$smarty->assign('ssNotes','<textarea name="ss_notes" id="ss_notes" cols="70" rows="3" wrap="physical">'.trim($ss_notes).'</textarea>');
		
	
	# Collect extra javascript
	ob_start();
?>
	<script language="javascript">
	<!-- Script Begin
	//  Script End -->
	</script>
	
<?php	
	$sTemp = ob_get_contents();
	ob_end_clean();

	$smarty->assign('sDocsJavaScript',$sTemp);
	//$smarty->assign('TP_user_name',$HTTP_SESSION_VARS['sess_user_name']);



	# Collect hidden inputs
	ob_start();
?>
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?php echo $HTTP_SESSION_VARS['sess_en']; ?>">
<input type="hidden" name="pid" value="<?php echo $HTTP_SESSION_VARS['sess_pid']; ?>">
<input type="hidden" name="modify_id" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<input type="hidden" name="create_id" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<input type="hidden" name="create_time" value="null">
<input type="hidden" name="mode" value="create">
<input type="hidden" name="target" value="<?php echo $target; ?>">
<input type="hidden" name="edit" value="<?php echo $edit; ?>">
<input type="hidden" name="is_discharged" value="<?php if(!empty($is_discharged)) echo $is_discharged; else echo $enc_obj->Is_Discharged($encounter_nr); ?>">
<!-- <input type="<?php if($setHidden) echo "hidden"; else echo "image"; ?>" onclick="if (setFrmSubmt()){ document.entryform.submit(); }" title="Save and Discharged" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>  -->
<input type="image" title="Save data" <?=createLDImgSrc($root_path,'savedisc.gif','0')?>>

<input type="hidden" name="encounter_class_nr" id="encounter_class_nr" value="<?= $enc_Info['encounter_class_nr']?>">
<input type="hidden" name="encounter_type" id="encounter_type" value="<?php if(!empty($encounter_type)) echo $encounter_type; else echo $patient['encounter_type']; ?>">

<input type="hidden" name="dob" id="dob" value="<?=@formatDate2Local($date_birth,$date_format)?>">
<input type="hidden" name="gender" id="gender" value="<?=$sex?>">

<?php
//$enc_obj->Is_Discharged($encounter_nr)
	$sTemp = ob_get_contents();
	ob_end_clean();

	$smarty->assign('ssHiddenInputs',$sTemp);
	
} 

//Cancal Button
$smarty->assign('pbBottomClose2','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancelClose.'"  align="absmiddle"></a>');
	
$smarty->assign('sMainBlockIncludeFile','medocs/main.tpl');

$smarty->display('common/mainframe.tpl');

?>