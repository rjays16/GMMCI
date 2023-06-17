<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/registration_admission/ajax/comp_search.common.php");
$xajax->printJavascript($root_path.'classes/xajax');
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='prompt.php';
$lang_tables[]='person.php';
define('LANG_FILE','aufnahme.php');
#commented by VAN 01-25-08
#$local_user='aufnahme_user';
define('NO_2LEVEL_CHK',1);
#added by VAN 01-25-08
if ($fromnurse)
	$local_user='ck_pflege_user';
else
	$local_user='aufnahme_user';

require_once($root_path.'include/inc_front_chain_lang.php');

#--------------added 03-07-07----------------
include_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=& new Person($pid);

include_once($root_path.'include/care_api_classes/class_encounter.php');
# Create encounter object
$encounter_obj=new Encounter();
#--------------------------------------------


$thisfile=basename(__FILE__);
//$breakfile='patient.php';
# edited by VAN 01-25-08
/*
if($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile=$root_path."main/startframe.php".URL_APPEND;
	else $breakfile="patient.php".URL_APPEND."&target=entry";
*/

if ($_GET['ptype'])
	$ptype = $_GET['ptype'];
elseif
	($_POST['ptype'])
	$ptype = $_POST['ptype'];

#echo "upate - ".$ptype;

if (($ptype=='newborn')||($ptype=='medocs'))
	$ptype = 'ipd';

if ($fromnurse)
	$breakfile = 'javascript:window.close()';
else{
	#edited by VAN 04-17-08
	/*
	if($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile=$root_path."main/startframe.php".URL_APPEND;
		else $breakfile="patient.php".URL_APPEND."&target=entry";
	*/
	$breakfile="patient_register_search.php".URL_APPEND."&target=entry";
}

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

# Import the death date
$death_date = $person->DeathDate();

#Load dept info of the user who logs in
#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);
	#echo $dept_obj->sql;

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
 $smarty->assign('sToolbarTitle',$LDPatientRegister);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDPatientRegister);

 #-------added by VAN ------------
 global $db;
 $sql2 = "SELECT ci.* FROM care_person_insurance AS ci
 WHERE ci.pid ='".$pid."' LIMIT 1";
 $res2=$db->Execute($sql2);
 $rsObj=$res2->FetchRow();
 $row = $res2->RecordCount();
 #echo "row = ".$row;

 $sql_enc = "SELECT encounter_nr FROM care_encounter AS e
 WHERE e.pid ='".$pid."' ORDER BY encounter_date DESC LIMIT 1";
 $res_enc=$db->Execute($sql_enc);
 $rsObj_enc=$res_enc->FetchRow();
 $row_enc = $res_enc->RecordCount();
 if (is_object($res_enc))
	$encounter_nr = $rsObj_enc['encounter_nr'];
 #----------------------------------
 # Onload Javascript code
 if ($row!=0){
	$smarty->assign('sOnLoadJs',"onLoad=\"if (window.focus) window.focus(); document.getElementById('order-list').width='100%';\"");
 }else{
	$smarty->assign('sOnLoadJs',"onLoad=\"if (window.focus) window.focus();\"");
 }

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('person_admit.php')");

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

# Loads the standard gui tags for the registration display page
require('./gui_bridge/default/gui_std_tags.php');
echo '<script type="text/javascript" src="'.$root_path.'js/shortcuts.js"></script>';
# Collect additional javascript code
ob_start();
?>

<!---------added by VAN----------->
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<script  language="javascript">
var $J = jQuery.noConflict();

	function ChangeToBaby(){
		var pid = '<?=$pid?>';

		var res = confirm('Are you sure you want to change the status of the patient?');

		if (res){
		    xajax_changeStatus(pid);
		}
	}

	//added by VAN 09-16-09
 //OPD
	shortcut("F2",
		function(){
			var ptype = '<?=$ptype;?>';
            var msg;
			//edited by VAN 01-30-2012
            if ($('allowF2').value==1){
               directConsultAdmit(ptype); 
            }else{
               if (ptype=='opd'){
                   msg = " have an OPD CONSULTATION."; 
                }else if (ptype=='er'){
                   msg = " have an ER CONSULTATION.";  
                }else if (ptype=='ipd'){
                   msg = " be ADMITTED.";  
                }else if (ptype='phs'){
                   msg = " have an OPD CONSULTATION.";  
                } 
                alert('This patient can\'t '+msg+' The patient is currently admitted or has an ER Consultation.');
            }
		}
        
	);


	function directConsultAdmit(ptype){
		var urlholder;

		if (ptype=='opd'){
			urlholder = "../../modules/registration_admission/aufnahme_start.php?<?=URL_APPEND?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype="+ptype;
		}else if (ptype=='er'){
			urlholder = "../../modules/registration_admission/aufnahme_start.php?<?=URL_APPEND?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=1&ptype="+ptype;
		}else if (ptype=='ipd'){
			urlholder = "../../modules/registration_admission/aufnahme_start.php?<?=URL_APPEND?>&pid=<?php echo $pid ?>&&origin=patreg_reg&encounter_class_nr=1&encounter_type=3&seg_direct_admission=1&ptype="+ptype;
		}else if (ptype='phs'){
			urlholder = "../../modules/registration_admission/aufnahme_start.php?<?=URL_APPEND?>&pid=<?php echo $pid ?>&origin=patreg_reg&encounter_class_nr=2&ptype="+ptype;
		}

		window.location.href=urlholder;
	}
 //--------------------

 function viewDeathError(){
	 window.open("../../modules/registration_admission/certificates/cert_Death_erroneousEntry_pdf.php?pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	}

	function viewCertMed(pid){
		//window.open("../../modules/registration_admission/certificates/cert_med_interface.php?encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
			 return overlib(
					OLiframeContent("../../modules/registration_admission/med_cert_history.php?pid="+pid, 850, 440, "fOrderTray", 1, "auto"),
																	WIDTH,440, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, "<img src=../../images/close.gif border=0 >",
																 CAPTIONPADDING,4, CAPTION,"MEDICAL CERTIFICATE HISTORY",
																 MIDX,0, MIDY,0,
																 STATUS,"MEDICAL CERTIFICATE HISTORY");
	} 
    
 //added by VAN 03-01-2013
function viewBirthError(){
    var encounter_nr = '<?=$encounter_nr;?>';
    
    if (encounter_nr=='')
        encounter_nr = 0;
        
    $J("#dialogBirth").dialog({
      autoOpen: true,
      resizable: false,
      height: 150,
      width: 300,
      modal: true,
      buttons: {
        OK: function() {
           var name = $J("#signatory");
           //$("#dialogBirth").load("../../modules/registration_admission/certificates/cert_birth_erroneous_pdf_jasper.php?pid="+<?=$pid?>+"&encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1");
           window.open("../../modules/registration_admission/certificates/cert_birth_erroneous_pdf_jasper.php?pid="+<?=$pid?>+"&sign_name="+name.val()+"&encounter_nr="+encounter_nr+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
           $J("#dialogBirth").dialog("close");
         },
        Cancel: function() {
           $J("#dialogBirth").dialog("close");
         }
      }
    });
    
    //window.open("../../modules/registration_admission/certificates/cert_birth_erroneous_pdf_jasper.php?pid="+<?=$pid?>+"&encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
}   

 function ReloadWindow(){
	window.location.href=window.location.href;
 }
 
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
require('./gui_bridge/default/gui_tabs_patreg.php');

# Display the data
$sRegForm = $person->create();

$smarty->assign('sRegForm',$sRegForm);

#added by VAN 02-19-08
$patient_info2 = $person_obj->getAllInfoObject($pid);
$patient_info = $patient_info2->FetchRow();
$fromtemp  = $patient_info['fromtemp'];

#echo "dept belong =".$dept_belong['id'];
#reg_options of Admission and Medocs department is invisible if not yet currently admitted.
#---------edited by vanessa 03-26-07---------

#added by VAN 02-28-08
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter();

$patient = $enc_obj->getLastestEncounter($pid);
$encounter_nr = $patient['encounter_nr'];

$discharged = $enc_obj->Is_Discharged($encounter_nr);
$patient_result = $enc_obj->getPatientEncounterResult($encounter_nr);
#echo "<br>result = ".$patient_result['result_code'];

#transferred and edited by VAN 02-28-08
if($discharged){
	if (($patient_result['result_code']==4)||($patient_result['result_code']==8)){
		$smarty->assign('is_discharged',TRUE);
		$smarty->assign('sWarnIcon',"<img ".createComIcon($root_path,'warn.gif','0','absmiddle').">");
		#$smarty->assign('sDischarged',$LDPatientIsDischarged.' and already dead');
		$smarty->assign('sDischarged','  This patient is already dead.');
		$is_died = 1;
	}
}


#-------------------------------

	# NO options/transactions should be displayed/possible
	# for patients with TEMPORARY patient number (pid)
#if ($is_died!=1){
#echo "temp = ".$fromtemp;
#if (substr($pid,0,1)=="T"){   # burn added : July 25, 2007

#edited by VAN 06-25-08
if ((substr($pid,0,1)=="T")||($fromtemp)){
	ob_start();
			$encounter_obj = $enc_obj;   #added by VAN 08-15-09
		require('./gui_bridge/default/gui_temporary_patient_reg_options.php');
		$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->assign('sRegOptions',$sTemp);
}else{
	#if (($dept_belong['id']=="Admission")||($dept_belong['id']=="Medocs")){
	if (($allow_ipd_user)||($allow_medocs_user)){
	#	if(((isset($current_encounter)&&$current_encounter))||($dept_belong['id']=="Medocs")){
		# Load and display the options table
			ob_start();
								$encounter_obj = $enc_obj;   #added by VAN 08-15-09
				require('./gui_bridge/default/gui_patient_reg_options.php');
				$sTemp = ob_get_contents();
			ob_end_clean();
			$smarty->assign('sRegOptions',$sTemp);
	#	}
	#}elseif (($dept_belong['id']=="ER")||($dept_belong['id']=="OPD-Triage")){
	}elseif (($allow_er_user)||($allow_opd_user)||($allow_phs_user)){
		ob_start();
						$encounter_obj = $enc_obj;   #added by VAN 08-15-09
			require('./gui_bridge/default/gui_patient_reg_options.php');
			$sTemp = ob_get_contents();
		ob_end_clean();
		$smarty->assign('sRegOptions',$sTemp);

	}
}# end of else stmt of if (substr($pid,0,1)!="T") stmt
#}
#----------------------------------------------

# If the data is not new , show new search button

if (!$newdata) {

	if($target=="search") $newsearchfile='patient_register_search.php'.URL_APPEND;
		else $newsearchfile='patient_register_archive.php'.URL_APPEND;
	#edited by VAN 01-25-08
	if ($fromnurse!=1)
		$smarty->assign('pbNewSearch',"<a href=\"$newsearchfile\"><img ".createLDImgSrc($root_path,'new_search.gif','0','absmiddle')."></a>");
}

#-----------added by vanessa 03-24-07-----------
#$smarty->assign('pbUpdateData',"<a href=\"patient_register.php".URL_APPEND."&pid=$pid&update=1\"><img ".createLDImgSrc($root_path,'update_data.gif','0','absmiddle')."></a>");
#echo "dept_belong =".$dept_belong['job_function_title'];

#($dept_belong['id']=="Medocs")
#if ((stristr($dept_belong['job_function_title'], strtolower('Head'))||stristr($dept_belong['job_function_title'], strtolower('Supervisor'))) != FALSE) {
#if (($dept_belong['id']=="Medocs") || ($dept_belong['parent_dept_nr']==151) || ((stristr($dept_belong['job_function_title'], strtolower('Head'))||stristr($dept_belong['job_function_title'], strtolower('Supervisor'))) != FALSE)) {
if (($allow_medocs_user) || ($allow_patient_register) || ($allow_update) || ((stristr($dept_belong['job_function_title'], strtolower('Head'))||stristr($dept_belong['job_function_title'], strtolower('Supervisor'))) != FALSE)) {
	$smarty->assign('pbUpdateData',"<a href=\"patient_register.php".URL_APPEND."&pid=$pid&update=1&ptype=$ptype\"><img ".createLDImgSrc($root_path,'update_data.gif','0','absmiddle')."></a>");
	$smarty->assign('pbRegInsurance','<a href="javascript:void(0);"
										 onclick="return overlib(
													OLiframeContent(\'seg-reg-insurance-tray.php?pid='.$pid.'&encounter_nr='.$encounter_nr.'&frombilling=0\', 600, 450, \'fOrderTray\', 1, \'auto\'),
													WIDTH,600, TEXTPADDING,0, BORDER,0,
													STICKY, SCROLL, CLOSECLICK, MODAL,
													CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
													CAPTIONPADDING,4,
													CAPTION,\'Add insurance details\',
													MIDX,0, MIDY,0,
													STATUS,\'Add insurance details\');"
												 	onmouseout="nd();">
							<img name="btninsurance" id="btninsurance" '.createLDImgSrc($root_path,'btn_insurance.gif','0','absmiddle').'></a>');
}

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

        #added by VAN 01-30-2012
        #F2 shortcut is restricted if $allowF2 is 0
        #if inpatient and still admitted
        #echo "<br>enctype = ".$enctype;
        #echo "<br>isdisc = ".$isdischarged;
        
        if ((($enctype==1)||($enctype==3)||($enctype==4))&&($isdischarged))
            $allowF2 = 1;
        elseif ((($enctype==1)||($enctype==3)||($enctype==4))&&(!$isdischarged))
            $allowF2 = 0;
        elseif (($enctype==2)||(!$enctype))
            $allowF2 = 1;
        else
            $allowF2 = 0;
		# Create new button to fresh input form
		$sNewRegBuffer='
		<form action="patient_register.php" method=post>
		<input type=submit value="'.$LDRegisterNewPerson.'">
		<input type=hidden name="sid" value="'.$sid.'">
		<input type=hidden name="lang" value="'.$lang.'">
        <input type=hidden name="allowF2" id="allowF2" value="'.$allowF2.'">
		</form>';
	}
	$smarty->assign('pbRegNewPerson',$sNewRegBuffer);

# Assign help links
#edited by VAN 01-25-08
if ($fromnurse!=1){
	$smarty->assign('sSearchLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="patient_register_search.php'.URL_APPEND.'">'.$LDPatientSearch.'</a>');
	$smarty->assign('sArchiveLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="patient_register_archive.php'.URL_APPEND.'&newdata=1&from=entry">'.$LDArchive.'</a>');
}

#commented by VAN 01-25-08
/*
$sCancel="<a href=";
if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $sCancel.=$breakfile;
	else $sCancel.='aufnahme_pass.php';
$sCancel.=URL_APPEND.'><img '.createLDImgSrc($root_path,'cancel.gif','0').' alt="'.$LDCancelClose.'"></a>';
*/
if ($fromnurse){
	$sCancel="<a href=";
	if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $sCancel.=$breakfile;
		else $sCancel.='aufnahme_pass.php';
	$sCancel.='><img '.createLDImgSrc($root_path,'cancel.gif','0').' alt="'.$LDCancelClose.'"></a>';

}else{
	$sCancel="<a href=";
	if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $sCancel.=$breakfile;
		else $sCancel.='aufnahme_pass.php';
	$sCancel.=URL_APPEND.'><img '.createLDImgSrc($root_path,'cancel.gif','0').' alt="'.$LDCancelClose.'"></a>';

}

$smarty->assign('pbCancel',$sCancel);

# Assign the page template to mainframe block
$smarty->assign('sMainBlockIncludeFile','registration_admission/reg_show.tpl');

# Show main frame
$smarty->display('common/mainframe.tpl');

#show floating Scroll
//echo '<script type="text/javascript" src="'.$root_path.'js/floatscroll.js"></script>';

//added by jasper 03/18/2013
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;
$signatory = $pers_obj->get_Signatory('errorbirth', true);
?>
<!--added by jasper 03/18/2013  -->
<div id="dialogBirth" style="display:none" title="Erroneous Birth Certificate">
  <form>
      <br> Signatory:
      <select id="signatory">
        <?php
             while ($row = $signatory->FetchRow()) {
                 echo "<option value='".$row['name']."'>".$row['name']."</option>";
             }
         ?>
      </select>
  </form>
</div>

