<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

/* Define language and local user for this module */
$thisfile=basename(__FILE__);
$lang_tables[]='prompt.php';
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','aufnahme.php');

//set break file
//$breakfile='medocs_pass.php';
$local_user = 'aufnahme_user';

//include xajax common file . .
require($root_path.'modules/system_admin/ajax/seg_override_common.php');
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

// include personell and department class
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

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
}

require_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('person_%');
$glob_obj->getConfig('patient_%');

/* Check whether config foto path exists, else use default path */
$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;
require_once($root_path.'include/inc_photo_filename_resolve.php');

//set page title
//$page_title=$LDMedocs;
$page_title = $swSocialService;

# Load the entire encounter data
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter($encounter_nr);
$enc_obj->loadEncounterData();

#echo "pid = ".$pid;

# Get encounter class
$enc_class=$enc_obj->EncounterClass();
$HTTP_SESSION_VARS['sess_full_en']=$encounter_nr;

#added by VAN
if(empty($encounter_nr)&&!empty($HTTP_SESSION_VARS['sess_en'])){
	#$encounter_nr=$HTTP_SESSION_VARS['sess_en'];
		$HTTP_SESSION_VARS['sess_en'] = $_GET['encounter_nr'];
}elseif($encounter_nr) {
	$HTTP_SESSION_VARS['sess_en']=$encounter_nr;
}
				#  echo "enc = ".$HTTP_SESSION_VARS['sess_en'];
$enc_Info = $enc_obj->getEncounterInfo($encounter_nr);
extract($enc_Info);
#echo $enc_obj->sql;
if(!empty($HTTP_SESSION_VARS['sess_login_userid']))
	$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
	$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
$userDeptInfo = $dept_obj->getUserDeptInfo($seg_user_name);

//$subtitle=$LDMedocs;
$subtitle = $swSocialService;

$buffer=str_replace('~tag~',$title.' '.$name_last,$LDNoRecordFor);
$norecordyet=str_replace('~obj~',strtolower($subtitle),$buffer);
$HTTP_SESSION_VARS['sess_file_return']=$thisfile;

# Patch for break urls that have lang param already
if ($_GET['from'])
	$breakfile = $root_path."modules/system_admin/edv-main-pass.php".URL_APPEND."&user_ck=$user_ck&target=adminoverride";
else
	$breakfile = $root_path."modules/system_admin/edv.php".URL_APPEND;

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

$HTTP_SESSION_VARS['sess_en'] = $_GET['encounter_nr'];

if($parent_admit) $sTitleNr= ($HTTP_SESSION_VARS['sess_full_en']);
	else $sTitleNr = ($HTTP_SESSION_VARS['sess_full_pid']);

 $stitle = 'Override Test Request';
# Title in the toolbar
 #$smarty->assign('sToolbarTitle',"$page_title $encounter_nr");
 $smarty->assign('sToolbarTitle',"$stitle :: Grant Request");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister')");

 #$smarty->assign('breakfile',$breakfile);
 #edited by VAN 05-13-08
 if ($popUp!='1'){
	 # href for the close button
	 $smarty->assign('breakfile',$breakfile);
 }else{
	# CLOSE button for pop-ups
	 $smarty->assign('breakfile','javascript:window.parent.cClick();');
 }

 # Window bar title
 #$smarty->assign('title',"$page_title $encounter_nr");
 $smarty->assign('title',"$stitle :: Grant Request");

 #transferred by VAN 11-19-09
 #added by VAN 06-24-08
 global $db;

 # Onload Javascript code
 $onLoadJs='onLoad="if (window.focus) window.focus();"';
 $smarty->assign('sOnLoadJs',$onLoadJs);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_entry.php')");

	# href for return button
 $smarty->assign('pbBack',$returnfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&target='.$target.'&mode=show&type_nr='.$type_nr);

# Buffer extra javascript code

ob_start();

if (empty($HTTP_SESSION_VARS['sess_en']))
	$HTTP_SESSION_VARS['sess_en'] = 0;

//end cha
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

<script type="text/javascript" src="<?=$root_path?>js/masking/html-form-input-mask.js"></script>
<!--<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype1.5.js"></script>-->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<!-- Calendar js -->
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>

<!-- YUI Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">

<!--added by VAN 05-08-08-->
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>


<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
# ListGen
$listgen->printJavascript($root_path);
?>
<script type="text/javascript" src="<?=$root_path?>modules/system_admin/override/js/seg_override.js?t=<?= time() ?>"></script>

<script language="javascript" type="text/javascript">
	YAHOO.namespace("example.container");
	YAHOO.util.Event.onDOMReady(init);

	function DOM_init() {
		rlst.reload();
	}
</script>


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

$discountid = 'PHS';
$smarty->assign('sEncNrPID',$HTTP_SESSION_VARS['sess_en'].'
															<input type="hidden" name="pid" id="pid" value="'.$HTTP_SESSION_VARS['sess_pid'].'">
															<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$HTTP_SESSION_VARS['sess_en'].'">
															<input type="hidden" name="discountid" id="discountid" value="'.$discountid.'">
															<input type="hidden" name="view_from" id="view_from" value="override">');

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
if($sex=='m') $smarty->assign('sSexType',mb_strtoupper($LDMale));
	elseif($sex=='f') $smarty->assign('sSexType',mb_strtoupper($LDFemale));

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

$smarty->assign('sPersonnelNo',$personnelID);

$smarty->assign('sJobFunction',$job_function_title);

#added by VAN 07-19-2010
$smarty->assign('sBirthPlace',mb_strtoupper($place_birth));
$smarty->assign('sAge',$age);

if (empty($occupation_name))
	$occupation_name = 'Not Indicated';
$smarty->assign('sOccupation',mb_strtoupper($occupation_name));

$smarty->assign('sCivilStat',mb_strtoupper($civil_status));
$smarty->assign('sReligion',mb_strtoupper($religion_name));

	if ($street_name){
		if ($brgy_name!="NOT PROVIDED")
			$street_name = $street_name.", ";
		else
			$street_name = $street_name.", ";
	}#else
		#$street_name = "";

	if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
		$brgy_name = "";
	else
		$brgy_name  = $brgy_name.", ";

	if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
		$mun_name = "";
	else{
		if ($brgy_name)
			$mun_name = $mun_name;
		#else
			#$mun_name = $mun_name;
	}

	if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
		$prov_name = "";
	#else
	#	$prov_name = $prov_name;

	if(stristr(trim($mun_name), 'city') === FALSE){
		if ((!empty($mun_name))&&(!empty($prov_name))){
			if ($prov_name!="NOT PROVIDED")
				$prov_name = ", ".trim($prov_name);
			else
				$prov_name = "";
		}else{
			#$province = trim($prov_name);
			$prov_name = "";
		}
	}else
		$prov_name = " ";

	$address = $street_name.$brgy_name.$mun_name.$prov_name;

$smarty->assign('sAddress',mb_strtoupper($address));
#---------

//Add by Mark on March 29, 2007
$smarty->assign('segDept_nr','Department');

$smarty->assign('LDExtraInfo',$LDExtraInfo);
$smarty->assign('LDYes',$LDYes);
$smarty->assign('LDNo',$LDNo);

$listgen->setListSettings('MAX_ROWS','10');
$rlst = &$listgen->createList('rlst',array('Batch No','Request Date','Dept','Total','Discount'),array(0,-1,0,0,NULL),'populateRequests');
$rlst->addMethod = 'js_addRow_Request';
$rlst->fetcherParams = array($_GET['encounter_nr']);
$rlst->columnWidths = array("22%", "29%", "15%", "*", "1%");
$smarty->assign('sRequestList',$rlst->getHTML());

#added by VAN 05-13-08
$smarty->assign('sPid',$HTTP_SESSION_VARS['sess_pid'].'<input type="hidden" name="mpid" id="mpid" value="'.$HTTP_SESSION_VARS['sess_pid'].'"><input type="hidden" name="can_classify" id="can_classify" value="'.$can_classify.'">');

ob_start();
?>

<!--added by VAN 05-13-08 -->
<input type="hidden" name="encoder_name" id="encoder_name" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">

<?php

$sTempx = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs', $sTempx);

ob_start();
#print_r($HTTP_SESSION_VARS);

?>

<input type="hidden" name="encNr" id="encNr" value="<?php echo $HTTP_SESSION_VARS['sess_en']; ?>">
<input type="hidden" name="pidNr" id="pidNr" value="<?php echo $HTTP_SESSION_VARS['sess_pid']; ?>">
<input type="hidden" name="encoderName" id="encoderName" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<!--<input type="hidden" name="encoderId" id="encoderId" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">-->

<?php
$jTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputsB',$jTemp);

ob_start();
?>

<input type="hidden" name="root_path" id="root_path" value="<?=$root_path?>">
<input type="hidden" name="sid" id="sid" value="<?=URL_APPEND?>">

<?php
$xTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sTailScripts', $xTemp);

$smarty->assign('sDocsBlockIncludeFile', 'system_admin/override/seg_override_frame.tpl');

$smarty->assign('sMainBlockIncludeFile','system_admin/override/seg_override_main.tpl');

$smarty->display('common/mainframe.tpl');

?>