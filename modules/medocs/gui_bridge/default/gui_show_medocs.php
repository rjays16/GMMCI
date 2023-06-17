<?php
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

 #added by VAN 04-28-08
 $breakfile = 'medocs_data_search.php'.URL_APPEND;

if($parent_admit) $sTitleNr= ($HTTP_SESSION_VARS['sess_full_en']);
	else $sTitleNr = ($HTTP_SESSION_VARS['sess_full_pid']);

$name_patient = $name_last.", ".$name_first." ".$name_middle;
# Title in the toolbar
 #$smarty->assign('sToolbarTitle',"$page_title $encounter_nr");
	#$smarty->assign('sToolbarTitle',"$page_title :: $pid");
	$smarty->assign('sToolbarTitle',"$page_title :: $name_patient ($pid)");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"$page_title :: $name_patient ($pid)");

 # Onload Javascript code
 #$onLoadJs='onLoad="if (window.focus) window.focus(); preSetPage();"';

 if ($mode=='show')
	 $onLoadJs='onLoad="if (window.focus) window.focus();"';
 else
	$onLoadJs='onLoad="if (window.focus) window.focus(); unhideObject(); formatdischargetime();"';

 #$onLoadJs='class="tundra" onLoad="if (window.focus) window.focus();"';
 #$onLoadJs='class="yui-skin-sam" onLoad="if (window.focus) window.focus();"';
 $smarty->assign('sOnLoadJs',$onLoadJs);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_entry.php')");

	# href for return button
 $smarty->assign('pbBack',$returnfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&target='.$target.'&mode=show&type_nr='.$type_nr);


#added by VAN 02-27-08
if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
	$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
	$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

ob_start();

?>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<script  language="javascript">

var $J = jQuery.noConflict();

/* added by genz 
   This will trigger for the transfer in disoposition to 
   enter the hospital name and address.
*/
function disposition_settings(value){
 	if(value == 8){
 		document.getElementById('disp').style.display = 'block';
 	}
 	else{
 		document.getElementById('disp').style.display = 'none';
 	}
}


jQuery(function($){
     $J("#date_text_d").mask("99/99/9999");
});

jQuery(function($){
     $J("#time_text_d").mask("99:99");
});

jQuery(function($){
     $J("#request_date").mask("99/99/9999");
});

jQuery(function($){
     $J("#date_text_p").mask("99/99/9999");
});

jQuery(function($){
     $J("#time_text_p").mask("99:99");
});

jQuery(function($){
     $J("#death_date").mask("99/99/9999");
});

jQuery(function($){
     $J("#death_time").mask("99:99");
});

<!--

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

function popRecordHistory(table,pid) {
	urlholder="./record_history.php<?php echo URL_REDIRECT_APPEND; ?>&table="+table+"&pid="+pid;
	HISTWIN<?php echo $sid ?>=window.open(urlholder,"histwin<?php echo $sid ?>","menubar=no,width=400,height=550,resizable=yes,scrollbars=yes");
}



-->
</script>

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
<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>

<script language="javascript" src="<?=$root_path?>js/yui-2.3/build/yahoo/yahoo-min.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui-2.3/build/event/event-min.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui-2.3/build/container/container.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui-2.3/build/dom/dom.js"></script>

<?php
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';
	/*echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';*/
	echo '<script type="text/javascript" src="'.$root_path.'js/shortcuts.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/medocs_function.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/medocs_combo.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/ICDCodeParticulars.js"></script>';

	#added by VAN 04-02-08
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/jquery-1.2.1.pack.js"></script>';
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'modules/medocs/js/medocs_combo.css">';

	#added by Cherry 01-26-11
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';

	$discharged = $enc_obj->Is_Discharged($encounter_nr);

	$received = $enc_obj->Is_ReceivedChart($encounter_nr);

	$xajax->printJavascript($root_path.'classes/xajax');

?>


<!--added by VAN 03-31-08-->
<!--
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/dojo-0.9.1/dijit/themes/tundra/tundra.css">
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/dojo-0.9.1/dojo/resources/dojo.css">


<script type="text/javascript" src="<?=$root_path?>js/dojo-0.9.1/dojo/dojo.js"
				djConfig="parseOnLoad: true"></script>
<script type="text/javascript">
			 dojo.require("dojo.parser");
			 //dojo.require("dijit.form.ComboBox");
		 dojo.require("dijit.form.FilteringSelect");
			 dojo.require("dojo.data.ItemFileReadStore");
		 dojo.addOnLoad(evtOnClick);

		 /*
		 dojo.require("dojo.widget.ComboBox");
		 dojo.require("dojo.event.*");
		 dojo.addOnLoad(evtOnClick);
		 */
</script>
-->


<script type="text/javascript">
shortcut("F6",
	function(){
		urlholder = "<?=$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=".$encounter_type."&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr?>";
		window.location.href=urlholder;
	}
);

shortcut("Esc",
	function(){
		urlholder = "<?=$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=show&type_nr='.$type_nr.'&encounter_class_nr = '.$encounter_class_nr?>";
		window.location.href=urlholder;
	}
);

shortcut("F2",
	function(){
		urlholder = "medocs_data_search.php<?=URL_APPEND;?>";
		window.location.href=urlholder;
	}
);

shortcut("F4",
	function(){
		updateReceivedDate('<?=$encounter_nr?>');
	}
);


shortcut("Enter",
	function(){
		var codetype = document.getElementById('codetype').value;

		//alert('codetype = '+codetype);

		if (codetype=='icd'){
			if (checkDeptDocDiagnosisERMode(<?=$encounter_type?>) && checkICDSpecific() && (document.getElementById('icdCode').value!='')){
					prepareAddIcdCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')
				hideDiv('icd');
				$(icdCode).blur();
			}
		}else{
			if ((checkDeptDocProcedureERMode(<?=$encounter_type?>))&& (document.getElementById('icpCode').value!='')){
					prepareAddIcpCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')
				hideDiv('icp');
				$(icpCode).blur();
			}
		}
	}
);

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

// added by shand 08/28/2013
function ConfinementHistory(pid){
	//window.open("../../modules/registration_admission/certificates/cert_med_interface.php?encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
		return overlib(
					OLiframeContent("../../modules/registration_admission/confiment_history.php?pid="+pid, 850, 440, "fOrderTray", 1, "auto"),
																	WIDTH,440, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, "<img src=../../images/close.gif border=0 >",
																 CAPTIONPADDING,4, CAPTION,"CONFINEMENT HISTORY",
																 MIDX,0, MIDY,0,
																 STATUS,"MEDICAL CERTIFICATE HISTORY");
}



function viewCertConf(){
	window.open("../../modules/registration_admission/certificates/cert_conf_interface.php?encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
}

function viewDeathError(){
	//window.open("../../modules/registration_admission/certificates/cert_Death_erroneousEntry_pdf.php?pid="+<?=$pid?>+"&encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
    //edited by jasper 02/17/13
    window.open("../../modules/registration_admission/certificates/cert_death_erroneous_pdf_jasper.php?pid="+<?=$pid?>+"&encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
}

//added by VAN 03-01-2013
function viewBirthError(){
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
           window.open("../../modules/registration_admission/certificates/cert_birth_erroneous_pdf_jasper.php?pid="+<?=$pid?>+"&sign_name="+name.val()+"&encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
           $J("#dialogBirth").dialog("close");
         },
        Cancel: function() {
           $J("#dialogBirth").dialog("close");
         }
      }
    });
    //window.open("../../modules/registration_admission/certificates/cert_birth_erroneous_pdf_jasper.php?pid="+<?=$pid?>+"&encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
}

//added by VAN 02-18-09
function updateReceivedDate(encounter_nr){
	//alert('enc = '+encounter_nr);
	var date = '<?= date("m/d/Y")?>';
	var is_discharged = '<?=$discharged?>';
	//alert('d = '+is_discharged);
	var received_date = prompt("Received date [mm/dd/YYYY]",date);
	var discharged_date, discharged_time, res;
	var obj_value = new Object();

	obj_value.received_date = received_date;

	if (obj_value.received_date){
		if (is_discharged==0){
			res = confirm('Patient is not yet discharge, you want to discharge the patient?');

			if (res){
				discharged_date = prompt("Discharged date [mm/dd/YYYY]",date);
				if (discharged_date)
					obj_value.discharged_date = discharged_date;
				else
					obj_value.discharged_date = '';

				if (obj_value.discharged_date){
					discharged_time = prompt("Discharged time [00:00 AM]");
					if (discharged_time)
						obj_value.discharged_time = discharged_time;
					else
						obj_value.discharged_time = "";

					if (obj_value.discharged_time)
						xajax_updateReceivedDate(encounter_nr, obj_value);
						//alert(obj_value.received_date+" - "+obj_value.discharged_date+" - "+obj_value.discharged_time);
					else
						discharged_time = prompt("Discharged time [00:00 A]");
				}
			}else{
				xajax_updateReceivedDate(encounter_nr, obj_value);
			}
		}else{
		//alert(obj_value.received_date+" - "+obj_value.discharged_date+" - "+obj_value.discharged_time);
			xajax_updateReceivedDate(encounter_nr, obj_value);
		}
	}

}

function cancelDischarged(encounter_nr){
		res = confirm('Are you really sure to cancel the discharge info?');

		if (res)
				xajax_cancelDischarged(encounter_nr);
}

//added by jarel 03-04-2013
function cancelDeath(encounter_nr,pid){
        res = confirm('Are you really sure to cancel the Death info?');

        if (res)
                xajax_cancelDeath(encounter_nr,pid);
}
//added by shand 05-21-2013
function undoMGH(encounter_nr){
    res = confirm('Are you really sure to undo the MGH status of the patient?');
    
    if(res){
        xajax_undoMGH(encounter_nr);
        //xajax_undoIsfinal(encounter_nr);
    }    
}


function cancelReceivedDate(encounter_nr){
		res = confirm('Are you really sure to cancel the received chart info?');

		if (res)
				xajax_cancelReceived(encounter_nr);
}

function ReloadWindow(){
	window.location.href=window.location.href;
 }

 //added by VAS 12-20-2011
 function undoCancellation(encounter_nr, pid){
    res = confirm('Are you really sure to undo the cancellation of the patient\'s case record?');

        if (res)
                xajax_undoCancellation(encounter_nr, pid);
 }
 //-------------------------

 function InvalidDeathDate(){
    var dd = new Date($('death_date').value);
    var ad = new Date($('txtAdmissionDate').value);
    if(dd < ad){
        alert('Death date must later than admission date');
        $('death_date').value ='';
        $('death_date').focus();
    }
 }

</script>


<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

require('./gui_bridge/default/gui_tabs_medocs.php');

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

if($death_date && $death_date != DBF_NODATE && ($encounter_nr==$death_encounter_nr)){
	$smarty->assign('sCrossImg','<img '.createComIcon($root_path,'blackcross_sm.gif','0').'>');

	if (($death_date!='')&&(($death_date=='0000-00-00')||($death_date=='1970-01-01')))
		$death_date = '00/00/0000';
	else
		$death_date = date("m/d/Y",strtotime($death_date));
	#$smarty->assign('sDeathDate',@formatDate2Local($death_date,$date_format));
	$smarty->assign('sDeathDate',$death_date);
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

if ($encounter_type_a==1){
	$segEncounterType="ER";
}elseif ($encounter_type_a==2){
	$segEncounterType="OPD";
}elseif ($encounter_type_a==3){

		# burn added : May 24, 2007
	if ($enc_Info['encounter_status']=='direct_admission')
		$segEncounterType="Inpatient (Direct Admission)";
	else
		$segEncounterType="Inpatient (ER)";

#}elseif ($encounter_type==4){
}elseif ($encounter_type_a==4){
	$segEncounterType="Inpatient (OPD)";
}
$smarty->assign('segEncounterTypeLabel','Encounter Type');
if($encounter_type){
	$smarty->assign('segEncounterType',$segEncounterType);
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
$smarty->assign('LDInsurance',$LDInsurance);
$smarty->assign('LDGotMedAdvice',$LDGotMedAdvice);
$smarty->assign('LDYes',$LDYes);
$smarty->assign('LDNo',$LDNo);

$patient_enc = $enc_obj->getPatientEncounterInsurance($encounter_nr);
#echo "sql = ".$enc_obj->sql;

#added by VAN 02-20-09
$smarty->assign('LDReceivedDate','Date Chart Received : ');

//added by Francis 02-22-2014
$transmitted = $enc_obj->getTransInfo($encounter_nr);

$received = "not yet";
$received_date = $patient_enc['received_date'];
if (($received_date)&&($received_date!='0000-00-00'))
	$received = date("m/d/Y",strtotime($received_date));

$smarty->assign('sReceivedDate',$received);

$smarty->assign('LDDischargedDate','Date Discharged :');

$discharged_dt = "still in";
$discharged_date = $patient_enc['discharge_date'];
if (($discharged_date)&&($discharged_date!='0000-00-00')){
	$discharged_time = $patient_enc['discharge_time'];

    if (($discharged_time)&&($discharged_time!='00:00:00')){
      $discharged_dt = date("m/d/Y",strtotime($discharged_date));
      $discharged_tm = date("h:i A",strtotime($discharged_time));
    }else{
	  $discharged_dt = date("m/d/Y",strtotime($discharged_date));
      if ($discharged_dt!='00/00/0000')
        $discharged_tm = '12:00 AM';
      else
        $discharged_tm = 'Unspecified';
    }

}

if ($discharged_dt=='still in')
    $smarty->assign('sDischargedDate',$discharged_dt);
else
    $smarty->assign('sDischargedDate',$discharged_dt."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Time : ".$discharged_tm);

if ($patient_enc['hcare_id']==18)
	$phic_membership = "YES";
else
	$phic_membership = "NO";

$smarty->assign('LDPHIC','PhilHealth Member? :');
$smarty->assign('sPHIC',$phic_membership);

if($transmitted)
{
    $isTrans = "YES";
}
else
{
    $isTrans = "NO";
}

$smarty->assign('LDTrans','Transmitted :');
$smarty->assign('sTrans',$isTrans);


#------------------
//TODO: fix show list of documents
#Show list of documents
$patient_result = $enc_obj->getPatientEncounterResult($encounter_nr);
if ($discharged['is_discharged']){
	$smarty->assign('is_discharged',TRUE);
	$smarty->assign('sWarnIcon',"<img ".createComIcon($root_path,'warn.gif','0','absmiddle').">");
	if (($patient_result['result_code']==4)||($patient_result['result_code']==8)||($enc_Info['is_DOA']==1)||($death_date!='0000-00-00'))
		$smarty->assign('sDischarged',$LDPatientIsDischarged.' and already dead.');
	else{
		#if ($is_discharged)
			$smarty->assign('sDischarged',$LDPatientIsDischarged);
		#else
		#	$smarty->assign('is_discharged',FALSE);
	}
}else{
	if (($patient_result['result_code']==4)||($patient_result['result_code']==8)||($enc_Info['is_DOA']==1)||($death_date!='0000-00-00')){
		$smarty->assign('is_discharged',TRUE);
		$smarty->assign('sWarnIcon',"<img ".createComIcon($root_path,'warn.gif','0','absmiddle').">");
		if ($enc_Info['is_DOA']==1)
			$smarty->assign('sDischarged','  This patient is already dead (DOA).');
		else
			$smarty->assign('sDischarged','  This patient is already dead.');
	}
}

$encounter_status = $enc_Info['encounter_status'];
if ($encounter_status=='cancelled')
   $smarty->assign('sDischarged','  This encounter was cancelled. Undo the cancellation to continue updating this case.');

#added by VAN
if (($patient_result['result_code']==4)||($patient_result['result_code']==8))
	$isDied = 1;
else
	$isDied = 0;
#echo "fromtemp = ".$result['fromtemp'];

if($mode=='show'){
	#----added by VAN
	if (($fromtemp) || ($isDied) || ($discharged) || !($discharged)){
		$source = 'medocs';
		ob_start();
			$encounter_obj = $enc_obj;   #added by VAN 08-15-09
			require($root_path.'modules/registration_admission/gui_bridge/default/gui_temporary_patient_reg_options.php');
			$sTemp = ob_get_contents();
			#$target = 'search';
		ob_end_clean();
		$smarty->assign('sRegOptions',$sTemp);
	}
	$smarty->assign('sShow',TRUE);

	if (($encounter_type_a==3)||($encounter_type_a==4))
		$smarty->assign('sPHICShow',TRUE);
	else
		$smarty->assign('sPHICShow',FALSE);
	/*
	$sTemprow = '<td width="78%">
						{{include file="registration_admission/basic_data.tpl"}}
					</td>
					<td width="22%">{{$sRegOptions}}</td>';
	$smarty->assign('sTrow',$sTemprow);
	*/
	#---------------

    #---- notification
    #medocs object = $objResDisp
    $res=$objResDisp->getNotificationEnc($encounter_nr);

    if($res){
        $dCount = $res->RecordCount();
        if($dCount>0){
             while($row=$res->FetchRow()){
                if($row['is_deleted']!='1'){
                   #$result['notification'].= 'Date Requested : '.$row['date_requested']." : ".$row['description']." <br> \n";
                   #$result['notification'].= $row['date_requested']." : ".$row['description']." <br> \n";
                   $result['dates_notification'].= $row['date_requested']." <br> \n";
                   $result['notification'].= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row['description']." <br> \n";
                }
             }
             $rows = $dCount;
        }
    }
    #---------------
    
    #added by VAN 06-10-2013
    $res2=$objResDisp->getOperationsEnc($encounter_nr);

    if($res2){
        $dCount2 = $res2->RecordCount();
        if($dCount2>0){
             while($row=$res2->FetchRow()){
                if (($row['op_date']!='0000-00-00') && ($row['op_date']!=''))
                    $row['op_date'] = date("m/d/Y",strtotime($row['op_date'])); 
                 
                $result2['description'].= $row['description']."<br> \n";
                $result2['ops_code'].= $row['ops_code']." <br> \n";
                $result2['rvu'].= $row['rvu']." <br> \n";
                $result2['op_date'].= $row['op_date']." <br> \n";
                $result2['quantity'].= $row['quantity']." <br> \n";
             }
             $rows = $dCount2;
        }
    }
    

	if($rows){
		# Set the document list template file

		$smarty->assign('sDocsBlockIncludeFile','medocs/docslist_frame.tpl');

		$smarty->assign('LDDetails',$LDDetails);
        
		$sTemp = '';
		$toggle=0;
		$row=$result;
        
        #added by VAN 06-10-2013
        #requested by Ma'am Lani, the medical records can only view all operations
        #encoded by the Billing clerk
        $smarty->assign('segHeadingOperation','Operations (Encoded by Billing):');
        
        if (!empty($result2['description'])){
            $smarty->assign('sRowClass','class="wardlistrow2" id="billproc" name="billproc"');

            $smarty->assign('sOperationsName',$result2['description']);
            $smarty->assign('sCode',$result2['ops_code']);
            $smarty->assign('sRVU',$result2['rvu']);
            $smarty->assign('sOpsDate',$result2['op_date']);
            $smarty->assign('sQuantity',$result2['quantity']);

            ob_start();
                $smarty->display('medocs/operationlist_row.tpl');
                $sTemp2 = $sTemp2.ob_get_contents();
            ob_end_clean();   
        }else{
            $sTemp2 =    '<tr class="wardlistrow2" id="billproc" name="billproc">
                            <td colspan="5" align="center"><font color="red">No Operations...</font></td>
                         </tr>
                        ';
        }
        $smarty->assign('sOperationListRows',$sTemp2);
        #----------------------

		$smarty->assign('segHeadingPrincipal','Principal:');
		$smarty->assign('segHeadingOthers','Others:');

        #------ notification
        #populate in show mode
        $smarty->assign('segHeadingNotification','Notification:');

        if (!empty($result['notification'])){
            $smarty->assign('sRowClass','class="wardlistrow2" id="notification" name="notification"');

            $smarty->assign('sDatesNotification',$result['dates_notification']);
            $smarty->assign('sNotification',$result['notification']);

            ob_start();
                $smarty->display('medocs/notificationlist_row.tpl');
                $sTemp = $sTemp.ob_get_contents();
            ob_end_clean();

        }else{
            $sTemp =    '<tr class="wardlistrow2" id="notification" name="notification">
                            <td colspan="2" align="center"><font color="red">No Notifications...</font></td>
                         </tr>
                        ';
        }
        $smarty->assign('sNotificationListRows',$sTemp);
        #-------------------

        $sTemp = '';

		if ( (!empty($result['diagnosis_principal']) && isset($result['diagnosis_principal'])) ||
				(!empty($result['therapy_principal']) && isset($result['therapy_principal'])) ){
			$smarty->assign('sRowClass','class="wardlistrow1" id="principal" name="principal"');

			$smarty->assign('sDiagnosis',$result['diagnosis_principal']);
			$smarty->assign('sTherapy',$result['therapy_principal']);

			ob_start();
				$smarty->display('medocs/docslist_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
			ob_end_clean();
		}else{
			$sTemp =	'		<tr class="wardlistrow1" id="principal" name="principal">
			<td colspan="2" align="center"><font color="red">No Principal Diagnosis/Procedure</font></td>
		</tr>
';
		}
		$smarty->assign('sDocsListRowsPrincipal',$sTemp);

		$sTemp = '';
		if ( (!empty($result['diagnosis_others']) && isset($result['diagnosis_others'])) ||
				(!empty($result['therapy_others']) && isset($result['therapy_others'])) ){
			$smarty->assign('sRowClass','class="wardlistrow2" id="others" name="others"');

			$smarty->assign('sDiagnosis',$result['diagnosis_others']);
			$smarty->assign('sTherapy',$result['therapy_others']);

			ob_start();
				$smarty->display('medocs/docslist_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
			ob_end_clean();

		}else{
			$sTemp =	'		<tr class="wardlistrow2" id="others" name="others">
			<td colspan="2" align="center"><font color="red">No Other Diagnosis/Procedure</font></td>
		</tr>
';
		}
		$smarty->assign('sDocsListRowsOthers',$sTemp);

	}else{

		# Show no record prompt

		$smarty->assign('bShowNoRecord',TRUE);

		$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','absmiddle').'>');
		$smarty->assign('norecordyet',$norecordyet);

	}
}elseif($mode=='details'){

	$smarty->assign('sShow',FALSE);

	$row=$result;

	# Show the record details

	# Set the include file

	$smarty->assign('sDocsBlockIncludeFile','medocs/form.tpl');

	$smarty->assign('sExtraInfo',nl2br($row['aux_notes']));

	if(stristr($row['short_notes'],'got_medical_advice')) $smarty->assign('sYesNo',$LDYes);
		else $smarty->assign('sYesNo',$LDNo);

	$smarty->assign('sDiagnosis',nl2br($row['diagnosis']));
	$smarty->assign('sTherapy',nl2br($result_icp['therapy']));

	if($enc_Info['encounter_type']=='3' || $enc_Info['encounter_type']=='4'){
		$smarty->assign('sSetResult',TRUE);
		$smarty->assign('sResult',$rResult['description']);
		$smarty->assign('sDisposition',$rDisp['descrip']);
	}else{
		$smarty->assign('sSetResult',FALSE);
	}

	$smarty->assign('sDate',formatDate2Local($row['date'],$date_format));
	$smarty->assign('sAuthor',$row['create_id']);

# Create a new form for data entry###################
}else {

	# Create a new entry form

	#added by VAN 02-18-08
	$smarty->assign('sShow',FALSE);
	# Set the include file

	$smarty->assign('sDocsBlockIncludeFile','medocs/form.tpl');

	# Set form table as active form
	$smarty->assign('bSetAsForm',TRUE);

    //For ICD and ICP control Add by  Mark on March 29, 2007
	ob_start();
		require("gui_medocs_icd.inc.php");
		 $sCodeControl1= ob_get_contents();
	ob_end_clean();
	$smarty->assign('codeControl1',$sCodeControl1);

	//Operation Interface
	ob_start();
		require("gui_medocs_icp.inc.php");
		$sCodeControl2= ob_get_contents();
	ob_end_clean();
	$smarty->assign('codeControl2',$sCodeControl2);

    #for notification
    ob_start();
    require("gui_medocs_notification.inc.php");
    $sCodeControl_Notification= ob_get_contents();
    ob_end_clean();
    $smarty->assign('codeControl_Notification',$sCodeControl_Notification);
    #----------------

	$patient_enc_cond = $enc_obj->getPatientEncounterCond($encounter_nr);
	$patient_enc_disp = $enc_obj->getPatientEncounterDispRefer($encounter_nr);
	$patient_enc_res = $enc_obj->getPatientEncounterRes($encounter_nr);

	$cond_code = $patient_enc_cond['cond_code'];
	$result_code = $patient_enc_res['result_code'];
	$disp_code = $patient_enc_disp['disp_code'];

	#$smarty->assign('sLabelDischarge','To be Discharge?');

	//user is from Medical Records
	if($allow_medocs_user){
		//Admission patient from OPD || patient from ER
			if ($encounter_type_a!=2){
				if ($encounter_type==1 && $encounter_type_a==1){
					$rowCond=$objResDisp->_getCondition("E");
					$rowResult=$objResDisp->_getResult("E");
					$rowDisp=$objResDisp->_getDisp("E");
				}else{
					$rowResult=$objResDisp->_getResult("A");
					$rowDisp=$objResDisp->_getDisp("A");
				}
			}

			//populate diagnosis and procedure xajax
			if($mode=="new" && $is_discharged==0){
				$smarty->assign('sTailScripts','<script language="javascript">
                                                    xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");
                                                    xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");
                                                    xajax_populateNotification("'.$encounter_nr.'");
                                                </script>');
			}

			#commented by VAN 06-12-08

			$smarty->assign('sSetDeptDiagnosis',true); //show Select Doctors and Departments for diagnosis van
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy

			if ($encounter_type==1 && $encounter_type_a==1)
				$smarty->assign('sSetCon',true); //Hide Condition row
			else
				$smarty->assign('sSetCon',false); //show Condition row van

			if($encounter_type_a == 2){
				$smarty->assign('sSetResult',false); //Show Result row and Disposition row
			}else{
				$smarty->assign('sSetResult',true); //Show Result row and Disposition row
			}

			$smarty->assign('sSetDeptDischarged',true); //discharged department
			#added by VAN 02-28-08
			if ($encounter_type==2){
				$smarty->assign('sDocLabel','Consulting'); //discharged department
				$smarty->assign('sSetDischarged',false); //show discharged time van
			}else{
				$smarty->assign('sDocLabel','Attending'); //discharged department
				$smarty->assign('sSetDischarged',true); //show discharged time van
			}

			if ((stristr($dept_belong['job_function_title'], 'head') === FALSE)){
				if ($is_discharged){
					#edited by VAN 06-27-08
					$enableSave = 1;   #hide save and discharge button
					$smarty->assign('sAdmittedOpd_a',true);
					$smarty->assign('sAdmittedOpd_b',false);
				}else{
					$enableSave = 1;   #show save and discharge button
					$smarty->assign('sAdmittedOpd_a',false);
					$smarty->assign('sAdmittedOpd_b',true);
				}
			}else{
				$enableSave = 1;   #show save and discharge button
				$smarty->assign('sAdmittedOpd_a',false);
				$smarty->assign('sAdmittedOpd_b',true);
			}

			$smarty->assign('sDiagnosisNotes', true); //show admitting diagnosis

			//added by EJ 09/26/2014
			$aux_note = $enc_obj->getAuxNote($encounter_nr);

			$smarty->assign('txtAreaDiagnosis','<textarea name="aux_notes" id="aux_notes" cols="75" rows="3" wrap="physical">'.trim($patient_enc['er_opd_diagnosis']).'</textarea>');
			#----------------------commented by VAN 03-27-08
	//User is from Admitting section department
	}elseif($allow_ipd_user){
		//ER patient
			#edited by VAN 02-27-08
			$rowCond=$objResDisp->_getCondition("A");
			$rowResult=$objResDisp->_getResult("A");
			$rowDisp=$objResDisp->_getDisp("A");

			//populate diagnosis and procedure xajax
			if($mode=="new" && $is_discharged==0){
				$smarty->assign('sTailScripts','<script language="javascript">
                                                    xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");
                                                    xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");
                                                    xajax_populateNotification("'.$encounter_nr.'");
                                                </script>');
			}
			$smarty->assign('sDiagnosisNotes', true); //show admitting diagnosis
			$smarty->assign('txtAreaDiagnosis','<textarea name="aux_notes" id="aux_notes" cols="75" rows="3" wrap="physical">'.trim($patient_enc['er_opd_diagnosis']).'</textarea>');
			#commented by VAN 06-12-08

			$smarty->assign('sSetConsult',true); //show consulting doctors & departments.
			$smarty->assign('sSetDeptDiagnosis',true); //show Select Doctors and Departments for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy
			#added by VAN 02-28-08
			if ($encounter_type==2)
				$smarty->assign('sDocLabel','Consulting'); //discharged department
			else
				$smarty->assign('sDocLabel','Attending'); //discharged department

			$smarty->assign('sSetDischarged',true); //show discharged time van
			$smarty->assign('sAdmittedOpd_a',false);  # admission,encoded & discharge
			$smarty->assign('sAdmittedOpd_b',true);	# admission,encoded
			$smarty->assign('sSetCon',false); //Hide Condition row van
			#edited by VAN 02-27-08
			if ($encounter_type_a==2){
				$smarty->assign('sSetResult',false); //Show Result row and Disposition row van
			}else{
				$smarty->assign('sSetResult',true); //Show Result row and Disposition row van
			}

			# added by VAN 02-18-08
			$enableSave = 1;   #show save and discharge button

	}elseif($allow_er_user){
		if($encounter_type == 1 && $encounter_class_nr == 1){
			$rowCond=$objResDisp->_getCondition("E");
			$rowResult=$objResDisp->_getResult("E");
			$rowDisp=$objResDisp->_getDisp("E");
			if($mode == "new" && $is_discharged == 0){
				$smarty->assign('sTailScripts','<script language="javascript">
                                                      xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");
                                                      xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");
                                                      xajax_populateNotification("'.$encounter_nr.'");
                                                </script>');
			}

			#commented by VAN 06-12-08
			$smarty->assign('sSetConsult',true); //show consulting doctors & departments.
			$smarty->assign('sSetResult',true); // show result row and Disposition row
			$smarty->assign('sSetCon',true); // show condition row
			$smarty->assign('sSetDeptDiagnosis', true); // show Doctor and Departement combobox for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy

			#added by VAN 02-28-08
			if ($encounter_type==2)
				$smarty->assign('sDocLabel','Consulting'); //discharged department
			else
				$smarty->assign('sDocLabel','Attending'); //discharged department

			$smarty->assign('sSetDischarged', true); // show discharged time
			$smarty->assign('sAdmittedOpd_a',false);
			$smarty->assign('sAdmittedOpd_b',true);
			# added by VAN 02-18-08
			$enableSave = 1;   #show save and discharge button

		}// end if (encounter_type)

	}else{
		//no permission

	}

	#only ER and Medical Records can discharged a patient
	if(($allow_er_user)||($allow_medocs_user)){
		if ($enableSave){
			if ($encounter_type!=2)
				$smarty->assign('sCheckDischarge','<input type="checkbox" id="isdischarge" name="isdischarge" value="1" checked onClick="unhideObject();"> <strong>To be Discharged?</strong>');

			if ((stristr($dept_belong['job_function_title'], 'head') === FALSE))
				$setHidden = false; #hide image save
			else
				$setHidden = false; #show image save
		}
	}

	//Display condition, result, disposition checkbox/radio
	#edited by VAN 02-28-08
	if($encounter_type_a!=2){
		//Display Condition for ER admission only if encounter_type = 1
		if(($encounter_type == 1 ||$encounter_type == 3) && $encounter_class_nr != 2){
			if(is_object($rowCond)){
				$sTmp ='';
				$c=0;

				while($cond=$rowCond->FetchRow()){
					$sTmp =$sTmp.'<input name="cond_code" id="cond_code" type="radio" value="'.$cond['cond_code'].'" ';
					if($cond_code == $cond['cond_code']) $sTmp = $sTmp.'checked';
					$sTmp = $sTmp.'>';
					$sTmp = $sTmp.$cond['cond_desc']."<br>";
					if($c<=2){
						$rowConditionA = $sTmp;
						if($c==2){ $sTmp='';}
					}else{ $rowConditionB = $sTmp;}
					$c++;
				}
			}
			$smarty->assign('rowConditionA',$rowConditionA);
			$smarty->assign('rowConditionB',$rowConditionB);
		}

		//Display option for Result/ Inpatient
		if(is_object($rowResult)){
			$sTmp = '';
			$count=0;
			#added by VAN 02-28-08
			while($result=$rowResult->FetchRow()){
				$sTmp=$sTmp.'<input name="result_code" onClick="showDeathDate(this.value);" id="result_code" type="radio" value="'.$result['result_code'].'" ';
				if($result_code == $result['result_code']) $sTmp= $sTmp.'checked';
				$sTmp = $sTmp.'>';
				$sTmp = $sTmp.$result['result_desc'];

				#added by VAN 06-28-08
				if (($result['result_code']==4)||($result['result_code']==8)){
					if ($death_date!='0000-00-00')
						$death_date = date("m/d/Y",strtotime($death_date));
					else
						$death_date = "";

					if (($death_date)&&($death_date!='0000-00-00')){
						$death_date = date("m/d/Y",strtotime($death_date));

						$meridian = date("A",strtotime($death_time));
						$death_time = date("h:i",strtotime($death_time));

						if ($meridian=='PM'){
							$selected1 = "";
							$selected2 = "selected";
						}else{
							$selected1 = "selected";
							$selected2 = "";
						}
					}else{
						$death_date = "";
						$death_time = "";
						$selected1 = "selected";
						$selected2 = "";
					}



					$deathtime = '<input type="text" id="death_time" name="death_time" size="4" maxlength="5" value="'.$death_time.'" onChange="setFormatTime(this,\'selAMPM_dt\')" />
							<select id="selAMPM_dt" name="selAMPM_dt">
								<option value="A.M." '.$selected1.'>A.M.</option>
								<option value="P.M." '.$selected2.'>P.M.</option>
							</select>&nbsp;<font size=1>[hh:mm]</font>';

					$sTmp = $sTmp.'&nbsp;&nbsp;<span id="death_date_span" style="display:none"><input type="text" name="death_date" id="death_date" size=10 maxlength=10 onblur="IsValidDate(this,\''.$date_format.'\');" value="'.$death_date.'">
										<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="death_date_trigger" align="absmiddle" style="cursor:pointer"> <font size=1>[mm/dd/yyyy]</font>
										'.$deathtime.'
									</span>';
				}
				#----------------
					$sTmp = $sTmp.'<br>';
					if($count<=2){
						$rowResultA =$sTmp;
						if($count==2){$sTmp='';}
					}else{ $rowResultB =$sTmp;
					}
				$count++;

			}
		}
		$smarty->assign('rowResultA',$rowResultA);
		$smarty->assign('rowResultB',$rowResultB);

		//Display Disposition
		if(is_object($rowDisp)){
			$sTmp = '';
			$count=0;
			#edited by VAN 02-27-08
			if(($allow_er_user)||($allow_ipd_user)){
				#added by VAN 02-28-08

				while($result=$rowDisp->FetchRow()){
					if ($result['disp_desc']!='Admitted'){
						$sTmp = $sTmp.'<input name="disp_code" id="disp_code" type="radio" value="'.$result['disp_code'].'" onclick="disposition_settings('.$result['disp_code'].');"';
						if($disp_code == $result['disp_code']) $sTmp = $sTmp.'checked';
						$sTmp = $sTmp.'>';
						$sTmp = $sTmp.$result['disp_desc']."<br>";

						#added by genz checker
						if($result['disp_code'] == '8'){
							$sTmp = $sTmp.'<div id="disp" style="display:none">
											 <b>Hospital Information</b><br>
											 <label>Name: </label><input type="text" name="hosp_name" placeholder="Hospital Name" size="18px" value="'.$patient_enc_disp['hosp_name'].'"/><br>
											 <label>Address: </label><input type="text" name="hosp_add" placeholder="Hospital Address" size="18px" value="'.$patient_enc_disp['hosp_add'].'"/><br>
											 <label>Reason: </label><select name="referral_reason" id="referral_reason" value="'.$patient_enc_disp['referral_reason'].'"></select></div>' ;
						}
						#end

						if($count<=2){
							$rowDispA = $sTmp;
							if($count==2) $sTmp = '';
						}else{ $rowDispB = $sTmp; }
						$count++;
					}
				}
			}else{
				while($result=$rowDisp->FetchRow()){
				$sTmp = $sTmp.'<input name="disp_code" id="disp_code" type="radio" value="'.$result['disp_code'].'" ';
				if($disp_code == $result['disp_code']) $sTmp = $sTmp.'checked';
				$sTmp = $sTmp.'>';
				$sTmp = $sTmp.$result['disp_desc']."<br>";

				if($count<=2){
					$rowDispA = $sTmp;
					if($count==2) $sTmp = '';
				}else{ $rowDispB = $sTmp; }
				$count++;

			}
			}

		}
		$smarty->assign('rowDispA',$rowDispA);
		$smarty->assign('rowDispB',$rowDispB);

	}//End of if Statement encounter_type!=2

	# Collect extra javascript

	ob_start();

?>
	<script language="javascript">
	<!-- Script Begin

	function chkForm(d) {
		/*
		if(!d.short_notes[0].checked&&!d.short_notes[1].checked){
			alert("<?php echo $LDPlsMedicalAdvice ?>");
			d.short_notes[0].focus();
			return false;
		}else if(d.date.value==""){
			alert("<?php echo $LDPlsEnterDate ?>");
			d.date.focus();
			return false;
		}else if(d.personell_name.value==""){
			alert("<?php echo $LDPlsEnterFullName ?>");
			d.personell_name.focus();
			return false;
		}else{
			return true;
		} */
		//alert("save");
	}

	function validate_f(){
		trimString($('date_text_d"'));
		trimString($('time_text_d'));
		trimString($('current_doc_nr_f'));
		trimString($('current_dept_nr_f'));
		//alert("inside function validate_f! ");
		if ($F('date_text_d"')==''){
			alert("Enter the Discharge Date!");
			$('date_text_d"').focus();
			return false;
		//commented by VAN 04-28-08
		/*
		}else if ($F('time_text_d')==''){
			alert("Enter the Discharge Time!");
			$('time_text_d').focus();
			return false;
		*/
		/*
		}else if ($F('current_doc_nr_f')==0){
			alert("Select an Attending Physician!");
			$('current_doc_nr_f').focus();
			return false;
		}else if ($F('current_dept_nr_f')==0){
			alert("Select an Attending Department!");
			$('current_dept_nr_f').focus();
			return false;
			*/
		}else{
			return true;
		}
	}/* end of function validate_f */

	//  Script End -->
	</script>

<?php

	$sTemp = ob_get_contents();
	ob_end_clean();

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

	$smarty->assign('sDocsJavaScript',$sTemp);

	// show ER encounter date/time and OPD encounter date/time
	if($encounter_type == 1 || $encounter_type == 2){
		//show er encounter date & time
		$smarty->assign('sAdmissionDate',@formatDate2Local($enc_Info['er_opd_datetime'],$date_format));
		$smarty->assign('sAdmissionTime',@formatDate2Local($enc_Info['er_opd_datetime'],$date_format,FALSE,TRUE));
	// show admitting date & time
	}else{
		//show admitting date & time
		$smarty->assign('sAdmissionDate',@formatDate2Local($enc_Info['admission_dt'],$date_format));
		$smarty->assign('sAdmissionTime',@formatDate2Local($enc_Info['admission_dt'],$date_format,FALSE,TRUE));
	}

	if($is_discharged==0){
		#$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_d" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');    # burn added : June 6, 2007
        $smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_d" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }"');
	}else{
		#$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local($patient_enc['discharge_date'],$date_format).'" id="date_text_d" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');    # burn added : June 6, 2007
        $smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local($patient_enc['discharge_date'],$date_format).'" id="date_text_d" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }"');
	}
	$smarty->assign('sDateValidateJs_p',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_p" onBlur="IsValidDate(this,\''.$date_format.'\')"');

	$TP_href_date="javascript:show_calendar('entryform.date','".$date_format."')";
	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$TP_date_format=$$dfbuffer;

	$smarty->assign('sDateMiniCalendar_d','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="date_trigger_d" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']&nbsp;[hh:mm]</font>');
	$smarty->assign('sDateMiniCalendar_p','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="date_trigger_p" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']&nbsp;[hh:mm]</font>');


	#set format time onkeyup event
	$smarty->assign('sFormatTime','onChange="setFormatTime(this,\'selAMPM\')"');   # burn added : June 6, 2007

	if($enc_Info['encounter_type']=="4"){
		$smarty->assign('bSetEntry',TRUE);
	}

	#-----------------edited by VAN 02-18-08
	#USe this for ER discharged consultant to select doctors
			$sDoc = '';
			#uncommented by VAN 02-18-08
				$sDoc = $sDoc.'<select id="current_doc_nr_c" name="current_doc_nr_c" onChange="jsGetDepartment_c();" >
							<option value="0">-Select a Doctor-</option>';
				$sDoc = $sDoc.'</select>';

	$smarty->assign('consultingDoc', $sDoc);

	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_c" name="current_dept_nr_c" onChange="jsGetDoctors_c();" >';
	$sDept = $sDept.'</select>';

	$sDept = $sDept.' <input type="hidden" name="current_dept_nr_c" id="current_dept_nr_c"  value ="'.$enc_Info['er_opd_admitting_dept_nr'].'">';
	$smarty->assign('consultingDept',$sDept);
	#-----------------------------------------------

	//Use this for ER discharged diagnosis to select doctors
	$sDoc ='';
	$sDoc = $sDoc.'<select id="current_doc_nr_d" name="current_doc_nr_d" onChange="jsGetDepartment_d();" >
							<option value="0">-Select a Doctor-</option>';
	$sDoc = $sDoc.'</select>';
		$smarty->assign('sDoctorInputD',$sDoc);

	//Display combo for Doctors & Departments //Use this ER discharged diagnosis code to select department
	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_d" name="current_dept_nr_d" onChange="jsGetDoctors_d();" >
							<option value="0">-Select a Department-</option>';
	$sDept = $sDept.'</select>';
	$smarty->assign('sDeptInputD',$sDept);

	//Use this for ER discharged procedure to select doctors
	$sDoc ='';
	$sDoc = $sDoc.'<select id="current_doc_nr_p" name="current_doc_nr_p" onChange="jsGetDepartment_p();" >
							<option value="0">-Select a Doctor-</option>';
	$sDoc = $sDoc.'</select>';
	$smarty->assign('sDoctorInputP',$sDoc);

	//Use this for ER discharged procedure code to select department
	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_p" name="current_dept_nr_p" onChange="jsGetDoctors_p();" >
							<option value="0">-Select a Department-</option>';
	$sDept = $sDept.'</select>';
	$smarty->assign('sDeptInputP',$sDept);

	//Time of performed procedure
	$stime = '';
	$stime = $stime.'<input type="text" id="time_text_p" name="time_text_p" size="4" maxlength="5" onChange="setFormatTime(this,\'selAMPM_p\')" />&nbsp;';
	$stime = $stime.'<select id="selAMPM_p" name="selAMPM_p">
						<option value="A.M.">A.M.</option>
						<option value="P.M.">P.M.</option>';
	$stime = $stime.'</select>';
	$smarty->assign('sTimeP',$stime);

########################## START ###########################
	//Use this for Final discharged diagnosis and procedure to select department || doctors
	$sDoc ='';
	$sDoc = $sDoc.'<select id="current_doc_nr_f" name="current_doc_nr_f" onChange="jsAssignDrICD(this.value); jsGetDepartment_f();" >
								<option value="0">-Select a Doctor-</option>';   # burn commented : June 4, 2007
	$sDoc = $sDoc.'</select>';
	$smarty->assign('sDoctorInputF',$sDoc);

	//Display combo for Doctors & Departments //Use this ER discharged diagnosis code to select department

	 # VAN uncommented : 02-26-08
	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_f" name="current_dept_nr_f" onChange="jsGetDoctors_f();" >
							<option value="0">-Select a Department-</option>';

		#$sDept = $sDept.'<select id="current_dept_nr_f" name="current_dept_nr_f" onChange="jsGetDoctors_f();" >';
	$sDept = $sDept.'</select>';

#commented by VAN 02-26-08

	if(($encounter_type == 1)||($encounter_type == 2)){
		#$sDept = "\n (".$enc_Info['er_opd_admitting_dept_name'].")";
		$sDept = $sDept.' <input type="hidden" name="current_dept_nr_f" id="current_dept_nr_f"  value ="'.$enc_Info['er_opd_admitting_dept_nr'].'">';
	}else{
		#$sDept = "\n (".$enc_Info['name_formal'].")";
		$sDept = $sDept.' <input type="hidden" name="current_dept_nr_f" id="current_dept_nr_f"  value ="'.$enc_Info['current_dept_nr'].'">';
	}

	$smarty->assign('sDeptInputF',$sDept);
######################### END ###############################


	$smarty->assign('sTailScripts2','<script language="javascript">preset_d();preset_p();preset_f();preset_c();loadConDispResData();</script>');
	$smarty->assign('sTailScripts','<script language="javascript">
                                        xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");
                                        xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");
                                        xajax_populateNotification("'.$encounter_nr.'");
                                   </script>');

	ob_start();

?>
			<!--EDITED: SEGWORKS -->
			<script type="text/javascript">
			now = new Date();
            Calendar.setup ({
			    //inputField : "date_text_d", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_d", singleClick : true, step : 1
			    inputField: "date_text_d",
                dateFormat: "%m/%d/%Y",
                trigger: "date_trigger_d",
                showTime: false,
                fdow: 0,
                max : Calendar.dateToInt(now),
                onSelect: function() { this.hide() }
            });
			</script>
			<script type="text/javascript">
            now = new Date();
			Calendar.setup ({
				//inputField : "date_text_p", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_p", singleClick : true, step : 1
                inputField: "date_text_p",
                dateFormat: "%m/%d/%Y",
                trigger: "date_trigger_p",
                showTime: false,
                fdow: 0,
                max : Calendar.dateToInt(now),
                onSelect: function() { this.hide() }
			});
			</script>

			<!-- added by VAN 06-28-08 -->
			<script type="text/javascript">
            now = new Date();
			Calendar.setup ({
				//inputField : "death_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "death_date_trigger", singleClick : true, step : 1
			    inputField: "death_date",
                dateFormat: "%m/%d/%Y",
                trigger: "death_date_trigger",
                showTime: false,
                fdow: 0,
                max : Calendar.dateToInt(now),
                onSelect: function() { this.hide() }
            });
			</script>
			<!-- -->
	<?php

		$sDateJS .= $calendarSetup;
		$smarty->assign('TP_user_name',$HTTP_SESSION_VARS['sess_user_name']);

	# Collect hidden inputs
	//ob_start();

    if (!(empty($patient_enc['discharge_time']))&&($patient_enc['discharge_time']<>'0000-00-00')){
        $discharge_time_h = date("H:i",strtotime($patient_enc['discharge_time']));
    }else{
        $discharge_time_h= '';
    }
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

<input type="hidden" name="current_dept_nr" id="current_dept_nr"  value ="<?php echo $enc_Info['current_dept_nr']; ?>">
<input type="hidden" name="current_att_dr_nr" id="current_att_dr_nr"  value ="<?php echo $enc_Info['attending_physician_nr']; ?>">

<input type="hidden" name="consulting_dept_nr" id="consulting_dept_nr"  value ="<?php echo $enc_Info['er_opd_admitting_dept_nr']; ?>">
<input type="hidden" name="consulting_dr_nr" id="consulting_dr_nr"  value ="<?php echo $enc_Info['er_opd_admitting_physician_nr']; ?>">

<input type="hidden" name="encounter_class_nr" id="encounter_class_nr" value="<?= $enc_Info['encounter_class_nr']?>">
<!--edited by VAN 02-28-08 -->
<!--
<input type="hidden" name="encounter_type" id="encounter_type" value="<?php if(!empty($encounter_type)) echo $encounter_type; else echo $patient['encounter_type']; ?>">
-->
<input type="hidden" name="encounter_type" id="encounter_type" value="<?php if(!empty($encounter_type_a)) echo $encounter_type_a; else echo $patient['encounter_type']; ?>">
<input type="hidden" name="dob" id="dob" value="<?=@formatDate2Local($date_birth,$date_format)?>">
<input type="hidden" name="gender" id="gender" value="<?=$sex?>">

<input type="hidden" name="cond_code_h" id="cond_code_h" value="<?=$patient_enc_cond['cond_code']?>">
<input type="hidden" name="disp_code_h" id="disp_code_h" value="<?=$patient_enc_disp['disp_code']?>">
<input type="hidden" name="result_code_h" id="result_code_h" value="<?= $patient_enc_res['result_code']?>">
<input type="hidden" name="discharge_time_h" id="discharge_time_h" value="<?=$discharge_time_h?>">

<input type="hidden" name="codetype" id="codetype" value="" />

<!-- added by VAN 06-28-08 -->
<?php
		if (empty($disp_hidden))
			$disp_hidden = $patient_enc_res['result_code'];
?>
<input type="hidden" name="disp_hidden" id="disp_hidden" value="<?=$disp_hidden?>" />
<input type="hidden" name="death_date2" id="death_date2" value="<?=$death_date?>"/>

<input type="hidden" name="userdept" id="userdept" value="<?=$userDeptInfo['dept_nr']?>">
<?php
	#added by VAN 02-18-08

if ($encounter_type!=2){
?>
	<div id="divSaveButton">
		<input type="<?php if($setHidden) echo "hidden"; else echo "image"; ?>" onclick="if (setFrmSubmt()){ document.entryform.submit(); }" title="Save and Discharge" <?php echo createLDImgSrc($root_path,'savedisc2.gif','0'); ?>>
	</div>
<?php } ?>
<script>

(function(){
	var inputa = function (e){
		e = e || window.event.e;
		if(e.keyCode == '123'){
			inputCodeHandler("icdCode", "<?=$HTTP_SESSION_VARS['sess_en'] ?>", "<?=$encounter_type ?>","<?=$encounter_type_a ?>", "<?= $HTTP_SESSION_VARS['sess_user_name']?>");
		}
	}
	YAHOO.util.Event.on("icdCode","keypress", inputa);

		var inputc = function (e){
				e = e || window.event.e;
				if(e.keyCode == '123'){
						inputCodeHandler("icdDesc", "<?=$HTTP_SESSION_VARS['sess_en'] ?>", "<?=$encounter_type ?>", "<?=$encounter_type_a ?>", "<?= $HTTP_SESSION_VARS['sess_user_name']?>");
				}
		}
		YAHOO.util.Event.on("icdDesc","keypress", inputc);

	var inputb = function (e){
		e = e || window.event.e;
		if(e.keyCode == '123'){
			inputCodeHandler("icpCode", "<?=$HTTP_SESSION_VARS['sess_en'] ?>", "<?=$encounter_type ?>", "<?=$encounter_type_a ?>", "<?= $HTTP_SESSION_VARS['sess_user_name']?>");
		}
	}
	YAHOO.util.Event.on("icpCode","keypress", inputb);

		var inputd = function (e){
				e = e || window.event.e;
				if(e.keyCode == '123'){
						inputCodeHandler("icpDesc", "<?=$HTTP_SESSION_VARS['sess_en'] ?>", "<?=$encounter_type ?>", "<?=$encounter_type_a ?>", "<?= $HTTP_SESSION_VARS['sess_user_name']?>");
				}
		}
		YAHOO.util.Event.on("icpDesc","keypress", inputd);

})();

</script>
<?php
																									//$enc_obj->Is_Discharged($encounter_nr)
	$sTemp = ob_get_contents();
	ob_end_clean();

	$smarty->assign('sHiddenInputs',$sTemp);

}

if ($mode=='show'||$mode=='details'){
	if($enc_diagnosis=='') $enc_diagnosis=TRUE;

   if ($enc_Info['encounter_status']=='cancelled'){

   }else{
	if(($mode=='show'||$mode=='details')&&!$enc_obj->Is_Discharged()){
		if($allow_ipd_user){
			$smarty->assign('sHideNewRecLink',true);
		}else{
			$smarty->assign('sHideNewRecLink',true);
		}

		#edited by VAN 02-29-08
		if ($encounter_nr){
			$smarty->assign('sNewLinkIcon','<img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>');
			$lnk="<a href=".$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=".$encounter_type."&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr.">".$LDEnterNewRecord."</a>";
			$smarty->assign('sNewRecLink','<span id="enterNewRecord">'.$lnk.'</span>');
		}
	}else{
		$smarty->assign('bSetAsForm',TRUE);
		#edited by VAN 02-19-08
		if ((!$discharged)||($allow_medocs_user)||($allow_er_user)||($allow_ipd_user)){
			$smarty->assign('sHideNewRecLink',true);
			$smarty->assign('sNewLinkIcon','<img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>');
			$lnk="<a href=".$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=".$encounter_type."&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr.">Edit Record </a>";
			$smarty->assign('sNewRecLink','<span id="enterNewRecord">'.$lnk.'</span>');
		}
	}
   }
	//for OPD and Inpatient View Mode

	$lnk=$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=2&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr;
	$smarty->assign('segOpdBtn','<input type="image" onclick="xajax_showDiagnosisTherapy('.$encounter_nr.',2,\''.$lnk.'\')" '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>OPD '); // tabs images..

	$lnk=$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=4&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr;
	$smarty->assign('segInpatientBtn', '<input type="image" onclick="xajax_showDiagnosisTherapy('.$encounter_nr.',4,\''.$lnk.'\')" '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>INPATIENT '); // tabs images..

	if($mode=='details'){
		$smarty->assign('sPdfLinkIcon','<img '.createComIcon($root_path,'icon_acro.gif','0','absmiddle').'>');
		$smarty->assign('sMakePdfLink','<a href="'.$root_path."modules/pdfmaker/medocs/report.php".URL_APPEND."&enc=".$HTTP_SESSION_VARS['sess_en']."&mnr=".$nr.'&target='.$target.'" target=_blank>'.$LDPrintPDFDoc.'</a>');
	}
}

	ob_start();
?>
	<script>
	function redirectWindow(){
		window.location.href ="<?=$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=".$encounter_type."&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr;?>";
	}
	(function(){
		var init = function (e){
			e = e || window.event.e;
			if(e.keyCode == '121'){
				redirectWindow();
			}
		}
		YAHOO.util.Event.addListener(window, "keypress", init);
	})();

	</script>

<?php
	$sTmp = ob_get_contents();
	ob_end_clean();

	$smarty->assign('sKeyListener',$sTmp);

if(($mode!='show'&&!$nolist) ||($mode=='show'&&$nolist&&$rows>1)){
	$smarty->assign('sListLinkIcon','<img '.createComIcon($root_path,'l-arrowgrnlrg.gif','0','absmiddle').'>');
	$smarty->assign('sListRecLink','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=show&type_nr='.$type_nr.'&encounter_class_nr = '.$encounter_class_nr.'">'.$LDShowDocList.'</a>');
}

$smarty->assign('pbBottomClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancelClose.'"  align="absmiddle"></a>');
# if discharged do the ff codes
	if ( $enc_obj->Is_Discharged($encounter_nr) && ($mode=="show")){
			# set print form if the encounter is already discharged
			# and it is in view mode
			if (($allow_medocs_user)||($allow_opd_user)||($allow_er_user)||($allow_ipd_user)){
				if ($encounter_type==1){   # Clinical Cover Sheet for ER patient
					$segPrintIcon = '<img '.createComIcon($root_path,'icon_acro.gif','0','absmiddle').'  title="Print this form."  align="absmiddle">';
					$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_er_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>ER Clinical Form Sheet</a>";
				}elseif ($encounter_type==2){   # Clinical Cover Sheet for Outpatient
					$segPrintIcon = '<img '.createComIcon($root_path,'icon_acro.gif','0','absmiddle').'  title="Print this form."  align="absmiddle">';
					$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_opd_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>OPD Clinical Form Sheet</a>";
				}elseif (($encounter_type==3)||($encounter_type==4)){   # Clinical Cover Sheet for Inpatient
		#			$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_cover_sheet.php?encounter_nr=$encounter_nr\" target=_blank>".$segPrintIcon."Inpatient Clinical Cover Sheet</a>";
					$segPrintIcon = '<img '.createComIcon($root_path,'icon_acro.gif','0','absmiddle').'  title="Print this form."  align="absmiddle">';
					$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_cover_sheet.php?encounter_nr=$encounter_nr\" target=_blank>Inpatient Clinical Cover Sheet</a>";
				}
			}
		$smarty->assign('segPrint','&nbsp;&nbsp;&nbsp;&nbsp;'.$segPrintIcon.'<span id="printForm">'.$formToPrint.'</span>');   # burn added : April 28, 2007
	}

$smarty->assign('sMainBlockIncludeFile','medocs/main.tpl');

$smarty->display('common/mainframe.tpl');
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