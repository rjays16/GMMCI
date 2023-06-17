<script language="javascript">
    <!--
    function openDRGComposite() {
        <?php if ($cfg['dhtml'])
        echo '
			w=window.parent.screen.width;
			h=window.parent.screen.height;';
    else
        echo '
			w=800;
			h=650;';
        ?>

        drgcomp_<?php echo $HTTP_SESSION_VARS['sess_full_en'] . "_" . $op_nr . "_" . $dept_nr . "_" . $saal ?>= window.open("<?php echo $root_path ?>modules/drg/drg-composite-start.php<?php echo URL_REDIRECT_APPEND . "&display=composite&pn=" . $HTTP_SESSION_VARS['sess_full_en'] . "&edit=$edit&is_discharged=$is_discharged&ln=$name_last&fn=$name_first&bd=$date_birth&dept_nr=$dept_nr&oprm=$saal"; ?>", "drgcomp_<?php echo $encounter_nr . "_" . $op_nr . "_" . $dept_nr . "_" . $saal ?>", "menubar=no,resizable=yes,scrollbars=yes, width=" + (w - 15) + ", height=" + (h - 60));
        window.drgcomp_<?php echo $HTTP_SESSION_VARS['sess_full_en'] . "_" . $op_nr . "_" . $dept_nr . "_" . $saal ?>.moveTo(0, 0);
    }

    function Vitals(perid, enco_nr) {
        var encounter = enco_nr;
        return overlib(
            OLiframeContent('<?=$root_path?>modules/registration_admission/seg-vitalsopen.php?encount_nr=' + encounter + '&pid=perid&sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&target=dependents&popUp=1&pid=<?=$pid?>',
                800, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Vital Signs',
            MIDX, 0, MIDY, 0,
            STATUS, 'Vital Signs');

        alert(encounter);
    }

    function getinfo(pn) {
        <?php /* if($edit)*/
        {
            echo '
	urlholder="' . $root_path . 'modules/nursing/nursing-station-patientdaten.php' . URL_REDIRECT_APPEND;
            echo '&pn=" + pn + "';
            echo "&pday=$pday&pmonth=$pmonth&pyear=$pyear&edit=$edit&station=$station";
            echo '";';
            echo '
	patientwin=window.open(urlholder,pn,"width=700,height=600,menubar=no,resizable=yes,scrollbars=yes");
	';
        }

        /*else echo '
        window.location.href=\'nursing-station-pass.php'.URL_APPEND.'&rt=pflege&edit=1&station='.$station.'\'';*/
        ?>
    }

    function cancelEnc() {
        if (confirm("<?php echo $LDSureToCancel ?>")) {
            $("#usrpwDialog").dialog({
                autoOpen: true,
                modal: true,
                show: "blind",
                hide: "explode",
                title: "Enter your username and password",
                position: "top", //added by VAN 12-19-2012
                buttons: {
                    OK: function () {
                        pw = $("#password").val();
                        usr = $("#username").val();
                        window.location.href = "aufnahme_cancel.php<?php echo URL_REDIRECT_APPEND ?>&mode=cancel&encounter_nr=<?php echo $HTTP_SESSION_VARS['sess_en'] ?>&cby=" + usr + "&pw=" + pw;
                    },
                    Cancel: function () {
                        $(this).dialog("close");
                    }
                },
                close: function () {
                    $(this).dialog("close");
                }

            });
        }
    }

    //added by VAN 10-06-08
    function admitPatient(encounter_nr, encounter_type) {
        if (confirm("Are you sure? You want to admit this patient?")) {
            usr = prompt("Please enter your username.", "");
            if (usr && usr != "") {
                pw = prompt("Please enter your password.", "");
                if (pw && pw != "") {
                    window.location.href = "aufnahme_admit.php<?php echo URL_REDIRECT_APPEND ?>&mode=admit&encounter_nr=" + encounter_nr + "&encounter_type=" + encounter_type + "&cby=" + usr + "&pw=" + pw;
                }
            }
        }
    }


    //-->
</script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<link type="text/css" href="<?= $root_path ?>js/jquery/css/jquery-ui.css" rel="stylesheet">
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>
<?php
# Let us detect if data entry is allowed
//echo $enc_status['is_disharged'].'<p>'. $enc_status['encounter_status'].'<p>d= '. $enc_status['in_dept'].'<p>w= '. $enc_status['in_ward'];
#uncommented by VAN 02-19-08
/*
if($enc_status['is_disharged']){
    #if(stristr('cancelled',$enc_status['encounter_status'])){
    if((stristr('cancelled',$enc_status['encounter_status'])) || (stristr('disallow_cancel',$enc_status['encounter_status']))){
        $data_entry=false;
    }
}elseif(!$enc_status['encounter_status']||stristr('cancelled',$enc_status['encounter_status'])){
    if(!$enc_status['in_ward']&&!$enc_status['in_dept']) $data_entry=false;
}
*/
/*
	if(($enc_status['is_disharged'])||((stristr('cancelled',$enc_status['encounter_status'])) || (stristr('disallow_cancel',$enc_status['encounter_status'])))){
		$data_entry=false;
	}elseif(!$enc_status['encounter_status']||((stristr('cancelled',$enc_status['encounter_status'])) || (stristr('disallow_cancel',$enc_status['encounter_status'])))){
		if(!$enc_status['in_ward']&&!$enc_status['in_dept'])
			$data_entry=false;
	}else
		$data_entry=true;
*/
#echo "<br>data entry = ".$data_entry."<br>";

#echo "dept_belong = ".$dept_belong['id']." - ".$dept_belong['job_function_title'];
#commented by VAN 02-19-08
/*
if( !$is_discharged&&!$enc_status['in_ward']&&!$enc_status['in_dept']&&
	 ( !$enc_status['encounter_status']||stristr('cancelled',$enc_status['encounter_status']) ||
		($enc_status['encounter_status']=='direct_admission')
	 )
	){
*/
global $allow_updateData, $allow_add_charges, $allow_consult_admit, $allow_phs_user, $allow_only_clinic, $allow_referral, $allow_ipdcancel, $allow_opdcancel, $allow_ercancel, $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update, $allow_viewMedcert, $allow_viewBilling;


$cancel = false;
if (($allow_ipdcancel) && ($ptype == 'ipd'))
    $cancel = true;
elseif (($allow_opdcancel) && ($ptype == 'opd'))
    $cancel = true;
elseif (($allow_ercancel) && ($ptype == 'er'))
    $cancel = true;
elseif (($allow_phscancel) && ($ptype == 'phs'))
    $cancel = true;

#echo "e = ".$enctype;
#echo "<br>d= ".$isdischarged;
#echo "allow ipd cancel = ".$allow_ipdcancel;
#echo "<br>allow opd cancel = ".$allow_opdcancel;
#echo "<br>allow er cancel = ".$allow_ercancel;
#echo "job = ".$dept_belong['job_function_title'];
if (stristr($dept_belong['job_function_title'], 'doctor') === FALSE)
    #echo "not doctor";
    $is_doctor = 0;
else
    #echo "doctor";
    $is_doctor = 1;
/*
if( !$is_discharged&&!$enc_status['in_ward']&&!$enc_status['in_dept']&&
	 ( !$enc_status['encounter_status']||(stristr('cancelled',$enc_status['encounter_status']) === FALSE) ||
		($enc_status['encounter_status']=='direct_admission')
	 )
	){
*/
if (!$is_discharged && !$enc_status['in_dept'] &&
    (!$enc_status['encounter_status'] || (stristr('cancelled', $enc_status['encounter_status']) === FALSE) ||
        ($enc_status['encounter_status'] == 'direct_admission')
    )
) {
//if(!$is_discharged&&!$enc_status['in_ward']&&!$enc_status['in_dept']&&(!$enc_status['encounter_status']||stristr('cancelled',$enc_status['encounter_status']))){
//if(!$enc_status['is_discharged']&&!$enc_status['in_ward']&&!$enc_status['in_dept']&&(!$enc_status['encounter_status']||stristr('cancelled',$enc_status['encounter_status']))){
    $data_entry = false;            # disallow data entry
} else {
    $data_entry = true;         #allow data entry
}

#echo "<br>data entry = ".$data_entry."<br>";

# Create the template object
if (!is_object($TP_obj)) {
    include_once($root_path . 'include/care_api_classes/class_template.php');
    $TP_obj = new Template($root_path);
}

# Assign the icons

if ($cfg['icons'] != 'no_icon') {
    #commented by van
    /*
    $TP_iconPost = '<img '.createComIcon($root_path,'post_discussion.gif','0').'>';
    $TP_iconFolder = '<img '.createComIcon($root_path,'open.gif','0').'>';
    $TP_iconBubble = '<img '.createComIcon($root_path,'bubble.gif','0').'>'; */
    #if ($dept_belong['id'] == "Medocs"){
    #	$TP_iconTalk = '<img '.createComIcon($root_path,'discussions.gif','0').'>';
    #}
    /*
    $TP_iconEye = '<img '.createComIcon($root_path,'eye_s.gif','0').'>';
    $TP_iconOGuy = '<img '.createComIcon($root_path,'prescription.gif','0').'>';
    $TP_iconHeads = '<img '.createComIcon($root_path,'new_group.gif','').'>';
    $TP_iconWGuy = '<img '.createComIcon($root_path,'people_search_online.gif','0').'>';
    $TP_iconWTorso = '<img '.createComIcon($root_path,'man-whi.gif','0').'>';
    */

    #added by VAN 02-12-08
    $TP_iconIDCard = '<img ' . createComIcon($root_path, 'new_address.gif', '0') . '>';

    #added by VAN 02-12-08
    #if ($HTTP_SESSION_VARS['dept_id'] != 'OPD-Triage'){
    if (!$allow_opd_user) {
        $TP_iconPreg = '<img ' . createComIcon($root_path, 'man-red.gif', '0') . '>';
        $TP_iconBirth = '<img ' . createComIcon($root_path, 'hfolder.gif', '0') . '>';
    }

    $TP_iconGTeen = '<img ' . createComIcon($root_path, 'bn.gif', '0') . '>';
    $TP_iconCrossTeen = '<img ' . createComIcon($root_path, 'bnplus.gif', '0') . '>';

    #added by VAN 02-12-08
    #if ($encounter_type==1)
    #$TP_iconPDF = '<img '.createComIcon($root_path,'icon_acro.gif','0').'>';

    $TP_iconPDFClinicalForm = '<img ' . createComIcon($root_path, 'icon_acro.gif', '0') . '>';
    #$TP_iconPDFMedicalCertificate = '<img '.createComIcon($root_path,'icon_acro.gif','0').'>';
    $TP_iconXPaper = '<img ' . createComIcon($root_path, 'nopmuser.gif', '0') . '>';

    #$TP_iconConsult = '<img '.createComIcon($root_path,'post_discussion.gif','0').'>';

} else {
    /*
    $TP_iconPost = '';
    $TP_iconFolder = '';
    $TP_iconBubble = '';
    $TP_iconTalk = '';
    $TP_iconEye = '';
    $TP_iconOGuy = '';
    $TP_iconHeads = '';
    $TP_iconWGuy ='';
    $TP_iconWTorso = '';
 */

    $TP_iconIDCard = '';

    #added by VAN 02-12-08
    $TP_iconPreg = '';
    $TP_iconBirth = '';

    $TP_iconGTeen = '';
    $TP_iconCrossTeen = '';
    $TP_iconPDF = '';
    $TP_iconPDFClinicalForm = '';
    #$TP_iconPDFMedicalCertificate = '';
    $TP_iconXPaper = '';
    $TP_iconBirthCert = '';
    #$TP_iconConsult = '';

    $TP_iconVitalsigns = '';
}

/*  ---commented by van 04-27-07---
$TP_href_1="show_sick_confirm.php".URL_APPEND ."&pid=$pid&target=$target";
if($data_entry){
	$TP_SICKCONFIRM="<a href=\"show_sick_confirm.php".URL_APPEND ."&pid=$pid&target=$target\">$LDSickReport</a>";
}else{
	$TP_SICKCONFIRM="<font color='#333333'>$LDSickReport</font>";
}

if($data_entry){
	$TP_DIAGXRESULTS="<a href=\"show_diagnostics_result.php".URL_APPEND."&pid=$pid&target=$target\">$LDDiagXResults</a>";
}else{
	$TP_DIAGXRESULTS="<font color='#333333'>$LDDiagXResults</font>";
}

if($data_entry){
	$TP_DIAGNOSES="<a href=\"show_diagnosis.php".URL_APPEND."&pid=$pid&target=$target\">$LDDiagnoses</a>";
}else{
	$TP_DIAGNOSES="<font color='#333333'>$LDDiagnoses</font>";
}

if($data_entry){
	$TP_PROCEDURES="<a href=\"show_procedure.php".URL_APPEND."&pid=$pid&target=$target\">$LDProcedures</a>";
}else{
	$TP_PROCEDURES="<font color='#333333'>$LDProcedures</font>";
}

if($data_entry){
	$TP_DRG="<a href=\"javascript:openDRGComposite()\">$LDDRG</a>";
}else{
	$TP_DRG="<font color='#333333'>$LDDRG</font>";
}

if($data_entry){
	$TP_PRESCRIPTIONS="<a href=\"show_prescription.php".URL_APPEND."&pid=$pid&target=$target\">$LDPrescriptions</a>";
}else{
	$TP_PRESCRIPTIONS="<font color='#333333'>$LDPrescriptions</font>";
}

if($data_entry){
	$TP_NOTESREPORTS="<a href=\"show_notes.php".URL_APPEND."&pid=$pid&target=$target\">$LDNotes $LDAndSym $LDReports</a>";
}else{
	$TP_NOTESREPORTS="<font color='#333333'>$LDNotes $LDAndSym $LDReports</font>";
}
$TP_href_11="show_immunization.php".URL_APPEND."&pid=$pid&target=$target";
if($data_entry){
	$TP_IMMUNIZATION="<a href=\"show_immunization.php".URL_APPEND."&pid=$pid&target=$target\">$LDImmunization</a>";
}else{
	$TP_IMMUNIZATION="<font color='#333333'>$LDImmunization</font>";
}

if($data_entry){
	$TP_MSRMNTS="<a href=\"show_weight_height.php".URL_APPEND."&pid=$pid&target=$target\">$LDMeasurements</a>";
}else{
	$TP_MSRMNTS="<font color='#333333'>$LDMeasurements</font>";
}
*/
# If the sex is female, show the pregnancies option link
#if($data_entry||$sex=='f') {
#edited by VAN 01-25-08

#if ($HTTP_SESSION_VARS['dept_id'] != 'OPD-Triage'){
if (!$allow_opd_user) {

    if (($data_entry && $sex == 'f') && ($fromnurse != 1)) {
        $TP_preg_BLK = "<a href=\"show_pregnancy.php" . URL_APPEND . "&pid=$pid&target=$target\">$LDPregnancies</a>";
    } else {
        $TP_preg_BLK = "<font color='#333333'>$LDPregnancies</font>";
    }

    if (($fromnurse != 1) && ($fromtemp == 1)) {
        $TP_BIRTHDX = "<a href=\"show_birthdetail.php" . URL_APPEND . "&pid=$pid&target=$target\">$LDBirthDetails</a>";
    } else {
        $TP_BIRTHDX = "<font color='#333333'>$LDBirthDetails</font>";
    }
}
#*/  #------commented by van 04-27-07-------

#$dept_belong['id']." - ".$dept_belong['job_function_title']

#added by VAN 01-25-08
if ($fromnurse)
    $TP_HISTORY = "<font color='#333333'>$LDRecordsHistory</font>";
else
    $TP_HISTORY = "<a href=\"javascript:popRecordHistory('care_encounter'," . $HTTP_SESSION_VARS['sess_en'] . ")\">$LDRecordsHistory</a>";
#edited by VAN 02-12-08
#$TP_HISTORY="<a href=\"record_history_pdf.php".URL_APPEND."&pid=$pid&target=$target\">$LDRecordsHistory</a>";

# Links to chart folder
$TP_href_17 = 'javascript:getinfo(\'' . $HTTP_SESSION_VARS['sess_en'] . '\')';

/*  -----commented by van 04-27-07
if($data_entry){
	$TP_CHARTSFOLDER="<a href=\"javascript:getinfo('".$HTTP_SESSION_VARS['sess_en']."')\">$LDChartsRecords</a>";
}else{
	$TP_CHARTSFOLDER="<font color='#333333'>$LDChartsRecords</font>";
}
*/ #-------commented by van

# Links to patient registration data display
#edited by VAN 01-25-08
if ($fromnurse) {
    $TP_PATREGSHOW = "<font color='#333333'>$LDShow $LDPatientRegister</font>";
} else {
    $TP_PATREGSHOW = "<a href=\"patient_register_show.php" . URL_APPEND . "&pid=" . $HTTP_SESSION_VARS['sess_pid'] . "&from=$from&newdata=1&target=$target&ptype=$ptype\">$LDShow $LDPatientRegister</a>";
    $redirectShow = 'patient_register_show.php' . URL_APPEND . '&pid=' . $HTTP_SESSION_VARS['sess_pid'] . '&from=' . $from . '&newdata=1&target=' . $target . '&ptype=' . $ptype;

}
#--------added cond, only department head is allowed to update the person info sheet.
#echo "job = ".strtolower($dept_belong['job_function_title'])." - ".stristr($dept_belong['job_function_title'], 'head');

#if (stristr($dept_belong['job_function_title'], 'head') === FALSE){
#edited by VAN 01-25-08

# FOOBAR
#if (((stristr($dept_belong['job_function_title'], 'head') === FALSE))||($fromnurse)){
/*if (($allow_only_clinic)||($ptype=='phs')){
	// disable update person info sheet
	$TP_PATREGUPDATE="<font color='#333333'>$LDUpdate $LDPatientRegister</font>";

}else{
	// enable update person info sheet
	$TP_PATREGUPDATE="<a href=\"patient_register.php".URL_APPEND."&pid=$pid&update=1&ptype=$ptype\">$LDUpdate $LDPatientRegister</a>";
	$redirectUpdate = 'patient_register.php'.URL_APPEND.'&pid'.$pid.'&update=1&ptype='.$ptype;
}
*/

if ($allow_updateData) {
    // enable update person info sheet
    $TP_PATREGUPDATE = "<a href=\"patient_register.php" . URL_APPEND . "&pid=$pid&update=1&ptype=$ptype\">$LDUpdate $LDPatientRegister</a>";
    $redirectUpdate = 'patient_register.php' . URL_APPEND . '&pid' . $pid . '&update=1&ptype=' . $ptype;
} else {
    $TP_PATREGUPDATE = "<font color='#333333'>$LDUpdate $LDPatientRegister</font>";
}

# Links to medocs module

#---added by van--
#if ($dept_belong['id'] == "Medocs"){
/*
	if($data_entry){  #commented by van
		$TP_MEDOCS="<a href=\"".$root_path."modules/medocs/show_medocs.php".URL_APPEND."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&edit=$edit&from=$from&is_discharged=$is_discharged&target=$target\">$LDMedocs</a>";
	}else{
		$TP_MEDOCS="<font color='#333333'>$LDMedocs</font>";
	}
	*/
#}

# Links to pdf doc generator
/* --------commented by van---
if($data_entry){
	$TP_PRINT_PDFDOC="<a href=\"".$root_path."modules/pdfmaker/admission/admitdata.php".URL_APPEND."&enc=".$HTTP_SESSION_VARS['sess_en']."\" target=_blank>$LDPrintPDFDoc</a>";
}else{
	$TP_PRINT_PDFDOC="<font color='#333333'>$LDPrintPDFDoc</font>";
}
*/ #-----commented by van
#echo "enc type = ".$encounter_type;
if (($is_discharged != 1) && (($death_date == "0000-00-00") && (($isDied != 1) || ($is_DOA != 1)))) {
    if ($encounter_type == 1) {   # Clinical Cover Sheet for ER patient
        $TP_iconPDF = '<img ' . createComIcon($root_path, 'icon_acro.gif', '0') . '>';
        $TP_SHOW_COVERSHEET = "<a href=\"" . $root_path . "modules/registration_admission/show_er_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>ER Clinical Form Sheet</a>";
        #$TP_SHOW_COVERSHEET = "<a href=\"javascript:void(0);\" onclick=\"viewClinicalForm(1);\">ER Clinical Form Sheet</a>";
        $redirectForm = $root_path . 'modules/registration_admission/show_er_clinical_form.php?encounter_nr=' . $encounter_nr;

    } elseif ($encounter_type == 2) {   # Clinical Cover Sheet for Outpatient
        $TP_iconPDF = '<img ' . createComIcon($root_path, 'icon_acro.gif', '0') . '>';
        #$TP_SHOW_COVERSHEET = "<a href=\"".$root_path."modules/registration_admission/show_opd_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>OPD Clinical Form Sheet</a>";
        $TP_SHOW_COVERSHEET = "<a href=\"javascript:void(0);\" onclick=\"viewClinicalForm(2);\">OPD Clinical Form Sheet</a>";
        $redirectForm = $root_path . 'modules/registration_admission/show_opd_clinical_form.php?encounter_nr=' . $encounter_nr;
        /*
            if ($is_discharged==1){   # Medical Certificate
                $TP_SHOW_MEDICAL_CERT = "<a href=\"".$root_path."modules/registration_admission/certificates/medical_certificate.php?encounter_nr=$encounter_nr\" target=_blank>Medical Certificate</a>";
                $redirectCert = $root_path.'modules/registration_admission/certificates/medical_certificate.php?encounter_nr='.$encounter_nr;
            }else{
                $TP_iconPDFMedicalCertificate = '';
            }
        */
    } elseif (($encounter_type == 3) || ($encounter_type == 4)) {   # Clinical Cover Sheet for Inpatient
        $TP_iconPDF = '<img ' . createComIcon($root_path, 'icon_acro.gif', '0') . '>';
        #$TP_SHOW_COVERSHEET = "<a href=\"".$root_path."modules/registration_admission/show_cover_sheet.php?encounter_nr=$encounter_nr\" target=_blank>Inpatient Clinical Cover Sheet</a>";
        $TP_SHOW_COVERSHEET = "<a href=\"javascript:void(0);\" onclick=\"viewClinicalForm(3);\">Inpatient Clinical Cover Sheet</a>";
        $redirectForm = $root_path . 'modules/registration_admission/show_cover_sheet.php?encounter_nr=' . $encounter_nr;
        //$redirectForm = 'show_cover_sheet.php?encounter_nr='.$encounter_nr;
        /*
            if ($is_discharged==1){   # Medical Certificate
                $TP_SHOW_MEDICAL_CERT = "<a href=\"".$root_path."modules/registration_admission/certificates/medical_certificate.php?encounter_nr=$encounter_nr\" target=_blank>Medical Certificate</a>";
                $redirectCert = $root_path.'modules/registration_admission/certificates/medical_certificate.php?encounter_nr='.$encounter_nr;
            }else{
                $TP_iconPDFMedicalCertificate = '';
            }
        */
    }
}
#added by VAN 07-28-08
#if (($dept_belong['id']=="Medocs")||($dept_belong['id']=="ER")){
if ((($allow_medocs_user) || ($allow_er_user)) && ($allow_viewMedcert) && (!$allow_only_clinic)) {
    if (($is_discharged == 1) || ($encounter_type == 1)) {   # Medical Certificate
        $TP_iconPDFMedicalCertificate = '<img ' . createComIcon($root_path, 'icon_acro.gif', '0') . '>';
        #$TP_SHOW_MEDICAL_CERT = "<a href=\"".$root_path."modules/registration_admission/certificates/cert_med_interface.php?encounter_nr=$encounter_nr\" target=_blank>Medical Certificate</a>";
        $TP_SHOW_MEDICAL_CERT = "<a href=\"javascript:void(0);\" onclick=\"viewCertMed($pid);\">Medical Certificate</a>";
        $redirectCert = $root_path . 'modules/registration_admission/certificates/cert_med_interface.php?encounter_nr=' . $encounter_nr;
    } elseif (($encounter_type == 3) || ($encounter_type == 4)) {
        #$TP_iconPDFMedicalCertificate = '';
        $TP_iconPDFMedicalCertificate = '<img ' . createComIcon($root_path, 'icon_acro.gif', '0') . '>';
        #$TP_SHOW_MEDICAL_CERT = "<a href=\"".$root_path."modules/registration_admission/certificates/cert_conf_interface.php?encounter_nr=$encounter_nr\" target=_blank>Cert. of Confinement</a>";
        $TP_SHOW_MEDICAL_CERT = "<a href=\"javascript:void(0);\" onclick=\"viewCertConf();\">Cert. of Confinement</a>";
        $redirectCert = $root_path . 'modules/registration_admission/certificates/cert_conf_interface.php?encounter_nr=' . $encounter_nr;
    }
}
#echo "er = ".$allow_medocs_user;
#echo "doa = ".$is_DOA;
#added by VAN 07-28-08
#if (($dept_belong['id']=="Medocs")||($dept_belong['id']=="ER")){
if ((($allow_medocs_user) || ($allow_er_user)) && (!$allow_only_clinic)) {
    if (($death_date != "0000-00-00") || ($isDied == 1) || ($is_DOA == 1)) {
        $TP_iconDeathCert = '<img ' . createComIcon($root_path, 'icon_acro.gif', '0') . '>';
        if ($is_DOA == 1) {
            $TP_SHOW_DEATH_CERT = "<a href=\"" . $root_path . "modules/registration_admission/certificates/cert_DOA_pdf.php?pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Death Certificate</a>";
            $redirectDeathCert = $root_path . 'modules/registration_admission/certificates/cert_DOA_pdf.php?pid=$pid&encounter_nr=$encounter_nr';
        } else {
            $TP_SHOW_DEATH_CERT = "<a href=\"" . $root_path . "modules/registration_admission/certificates/cert_death_interface.php?pid=$pid\" target=_blank>Death Certificate</a>";
            $redirectDeathCert = $root_path . 'modules/registration_admission/certificates/cert_death_interface.php?pid=' . $pid;
        }
    } else {
        $TP_SHOW_DEATH_CERT = "";
    }
}
#added by VAN 07-28-08
#if (($dept_belong['id']=="Medocs")||($dept_belong['id']=="ER")){
if ((($allow_medocs_user) || ($allow_er_user)) && (!$allow_only_clinic)) {
    if ($fromtemp == 1) {
        $TP_iconBirthCert = '<img ' . createComIcon($root_path, 'icon_acro.gif', '0') . '>';
        $TP_SHOW_BIRTH_CERT = "<a href=\"" . $root_path . "modules/registration_admission/certificates/cert_birth_interface.php?pid=$pid\" target=_blank>Birth Certificate</a>";
        $redirectBirthCert = $root_path . 'modules/registration_admission/certificates/cert_birth_interface.php?pid=' . $pid;
    } else {
        $TP_SHOW_BIRTH_CERT = "";
    }
}
#added by bryan on Feb 7,09
#for vital signs
##########################

if (($allow_vitalsigns)) {

    $TP_iconVital = '<img ' . createComIcon($root_path, 'torso_br1.gif', '0') . '>';
    $TP_VITAL_SIGNS = "<a href='javascript:void(0);' onclick='Vitals(" . $pid . "," . $encounter_nr . ");' >Vital Signs</a>";

} else {
    $TP_VITAL_SIGNS = "";

}

##########################

$patient = $encounter_obj->getLastestEncounter($pid, 1);

$cnt = $encounter_obj->count;
$showFxn = 0;

// Added by LST .... 12.03.2009 --- this is to restrict MGH patients from making requests ....
$is_MGH = ($patient['is_maygohome'] != 0);

if (($patient['is_discharged'] == 1) && (($patient['encounter_type'] == 3) || ($patient['encounter_type'] == 4) || ($patient['encounter_type'] == 6)))
    $showFxn = 1;
elseif (($patient['encounter_type'] == 1) || ($patient['encounter_type'] == 2) || ($patient['encounter_type'] == 5))
    $showFxn = 1;
elseif ($cnt == 0)
    $showFxn = 1;
#echo $encounter_obj->sql;
#added by VAN 04-28-08
#echo "dept_belong : ".$dept_belong['id'];
#echo "e = ".$isdischarged;

#if (($encounter_type==2)&&($allow_opd_user)){
if ((($allow_opd_user) && ($ptype == 'opd') && (($enctype != 3) || ($enctype != 4) || ($enctype != 1)) && ($isdischarged)) || ((($allow_opd_user) && ($ptype == 'opd')) || (($allow_phs_user) && ($ptype == 'phs'))) && (($enctype == 2) || (empty($enctype)))) {
    #if (!$allow_only_clinic) {
    #if ((($allow_phs_user)&&($ptype=='phs'))||(($allow_opd_user)&&($ptype=='opd')&&(!$allow_only_clinic))) {
    if (((($allow_phs_user) && ($ptype == 'phs')) || (($allow_opd_user) && ($ptype == 'opd'))) && (!$allow_only_clinic)) {
        if (($allow_opd_user) && ($ptype == 'opd') && ($allow_consult_admit)) {
            $TP_iconConsult = '<img ' . createComIcon($root_path, 'post_discussion.gif', '0') . '>';
            $TP_SHOW_Consultation = "<a href=\"aufnahme_start.php" . URL_APPEND . "&pid=$pid&origin=patreg_reg&encounter_class_nr=2&ptype=" . $ptype . "\">$LDOPDConsultation</a>";
            $redirectAdConsult = 'aufnahme_start.php' . URL_APPEND . '&pid=' . $pid . '&origin=patreg_reg&encounter_class_nr=2&ptype=' . $ptype;  //shortcut key PageUp
        } elseif (($allow_phs_user) && ($ptype == 'phs') && ($allow_consult_admit)) {
            $TP_iconConsult = '<img ' . createComIcon($root_path, 'post_discussion.gif', '0') . '>';
            $TP_SHOW_Consultation = "<a href=\"aufnahme_start.php" . URL_APPEND . "&pid=$pid&origin=patreg_reg&encounter_class_nr=2&ptype=" . $ptype . "\">PHS Consultation</a>";
            $redirectAdConsult = 'aufnahme_start.php' . URL_APPEND . '&pid=' . $pid . '&origin=patreg_reg&encounter_class_nr=2&ptype=' . $ptype;  //shortcut key PageUp
        }
    }
    $TP_iconConsultList = '<img ' . createComIcon($root_path, 'qkvw.gif', '0') . '>';
    $TP_SHOW_ConsultationList = "<a href=\"show_encounter_list.php" . URL_APPEND . "&pid=$pid&target=$target&ptype=" . $ptype . "\">$LDListEncounters</a>";
    $redirectEncList = 'show_encounter_list.php' . URL_APPEND . '$pid=' . $pid . '&target=' . $target . '&ptype=' . $ptype;    //shortcut key shift+l 76
}
#echo $dept_belong['id'];
#added by VAN 07-02-08
#print_r($dept_belong);
#if (($encounter_type==1)&&(($dept_belong['id']=='ER')||($is_doctor))){
#if (($is_doctor)||(($dept_belong['id']=='ER')&&($encounter_type==1))||(($dept_belong['id']!='OPD-Triage')&&($encounter_type==2))){
#if (($is_doctor)||(($allow_er_user)&&($encounter_type==1))||((!$allow_opd_user)&&($encounter_type==2))){
#echo "al = ".$allow_area['lab_request'];
$TP_SHOW_Lab = "";
$TP_SHOW_Blood = "";
$TP_SHOW_Radio = "";
$TP_SHOW_Pharma = "";
$TP_SHOW_OR = "";

#if (($allow_area['lab_request'])||($allow_area['blood_request'])||($allow_area['radio_request'])||($allow_area['pharma_request'])||($allow_area['or_request'])||($is_doctor)||(($allow_er_user)&&($encounter_type==1))||(($allow_opd_user)&&($encounter_type==2))||(($allow_phs_user)&&($encounter_type==5))||(($allow_ipd_user)&&(($encounter_type==3)||($encounter_type==4)||($encounter_type==6)))){
if (($is_doctor) || ($allow_labrequest) || ($allow_radiorequest) || ($allow_bloodrequest) || ($allow_pharmarequest) || ($allow_orrequest) || ($allow_otherrequest) || (($allow_er_user) && ($encounter_type == 1)) || (($allow_opd_user) && ($encounter_type == 2)) || (($allow_phs_user) && ($encounter_type == 5)) || (($allow_ipd_user) && (($encounter_type == 3) || ($encounter_type == 4) || ($encounter_type == 6)))) {
    #echo "sulod";
    #if (($dept_belong['id']=='ER')&&($encounter_type==1)){
    if (($allow_er_user) && ($encounter_type == 1)) {
        $is_ER = 1;
    } else {
        $is_ER = 0;
    }
    #echo $dept_belong['personell_nr'];

    #lab
    if ($allow_labrequest) {
        $TP_iconLab = '<img ' . createComIcon($root_path, 'document_post.gif', '0') . '>';
        #$TP_SHOW_Lab = "<a href=\"".$root_path."modules/laboratory/seg-lab-request-new.php?area=ER&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Laboratory Request</a>";
        #$TP_SHOW_Lab = '<a href="javascript:labRequest();" target=_blank>Laboratory Request</a>';
        #$redirectLab = $root_path.'modules/laboratory/seg-lab-request-new.php?area=ER&pid=$pid&encounter_nr=$encounter_nr';
        if (!$is_MGH)
            $TP_SHOW_Lab = "<a href=\"javascript:void(0);\" onclick=\"LabItem($is_ER);\" onmouseout=\"nd();\">Laboratory Request</a>";
        else
            $TP_SHOW_Lab = "<font color='#333333'>Laboratory Request</font>";
        #$redirectLab = $root_path.'modules/laboratory/seg-lab-request-new.php?area=ER&pid=$pid&encounter_nr=$encounter_nr';

        $TP_iconSpecialLab = '<img ' . createComIcon($root_path, 'copy.gif', '0') . '>';
        if (!$is_MGH)
            $TP_SHOW_SpecialLab = "<a href=\"javascript:void(0);\" onclick=\"SpecialLabItem($is_ER);\" onmouseout=\"nd();\">Special Laboratory Request</a>";
        else
            $TP_SHOW_SpecialLab = "<font color='#333333'>Special Laboratory Request</font>";
    }

    #blood
    if ($allow_bloodrequest) {
        $TP_iconBlood = '<img ' . createComIcon($root_path, 'bnplus.gif', '0') . '>';
        if (!$is_MGH)
            $TP_SHOW_Blood = "<a href=\"javascript:void(0);\" onclick=\"BloodItem($is_ER);\" onmouseout=\"nd();\">Blood Bank Request</a>";
        else
            $TP_SHOW_Blood = "<font color='#333333'>Blood Bank Request</font>";
    }

    #radio
    if ($allow_radiorequest) {
        $TP_iconRadio = '<img ' . createComIcon($root_path, 'Appointment.gif', '0') . '>';
        #$TP_SHOW_Radio = "<a href=\"".$root_path."modules/radiology/seg-radio-request-new.php?area=ER&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>Radiology Request</a>";
        #$redirectRadio = $root_path.'modules/radiology/seg-radio-request-new.php?area=ER&pid=$pid&encounter_nr=$encounter_nr';
        if (!$is_MGH)
            $TP_SHOW_Radio = "<a href=\"javascript:void(0);\" onclick=\"RadioItem($is_ER);\" onmouseout=\"nd();\">Radiology Request</a>";
        else
            $TP_SHOW_Radio = "<font color='#333333'>Radiology Request</font>";
    }

    $sql2 = "SELECT i.*, e.pid, f.firm_id, f.name
												FROM care_person_insurance AS i
												LEFT JOIN care_person AS e
												ON e.pid = i.pid
												INNER JOIN care_insurance_firm AS f
												ON f.hcare_id = i.hcare_id
												WHERE i.pid = $pid
												OR i.pid=(SELECT parent_pid FROM seg_dependents AS dep WHERE (dep.dependent_pid='$pid' OR dep.dependent_pid='$pid'))
												AND i.hcare_id ='27'
												ORDER BY f.firm_id LIMIT 1";
    $res = $db->Execute($sql2);
    if ($res)
        $row = $res->FetchRow();
    #echo "p = ".$sql2;
    #echo "<br>e = ".$row['insurance_nr'];
    #pharma

    #if ((($ptype=='phs')&&($phsObj['has_pharma'])&&($row['insurance_nr']))||($ptype!='phs')){
    if ($allow_pharmarequest) {
        $TP_iconPharma = '<img ' . createComIcon($root_path, 'pill-016.gif', '0') . '>';
        #$TP_SHOW_Pharma = "<a href=\"".$root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target=order_er&encounter_nr=$encounter_nr\" target=_blank>Pharmacy Request</a>';
        #$TP_SHOW_Pharma = "<a href=\"".$root_path."modules/pharmacy/seg-pharma-order.php?area=ER&pid=$pid&encounterset=$encounter_nr\" target=_blank>Pharmacy Request</a>";
        #$redirectPharma = $root_path.'modules/pharmacy/seg-pharma-order.php?area=ER&pid=$pid&encounterset=$encounter_nr';
        if ($encounter_type == 2)
            $TP_SHOW_Pharma = "<a href=\"javascript:void(0);\" onclick=\"PharmaItem($encounter_type,'MG');\" onmouseout=\"nd();\">Pharmacy Request (MG)</a>";
        else {
            $TP_iconPharma2 = '<img ' . createComIcon($root_path, 'pill-016.gif', '0') . '>';

            if (!$is_MGH) {
                $TP_SHOW_Pharma = "<a href=\"javascript:void(0);\" onclick=\"PharmaItem($encounter_type,'IP');\" onmouseout=\"nd();\">Pharmacy Request</a>";
                $TP_SHOW_Pharma2 = "<a href=\"javascript:void(0);\" onclick=\"PharmaItem($encounter_type,'MG');\" onmouseout=\"nd();\">Pharmacy Request (MG)</a>";
                /*if ($ptype=='er') {
                         $TP_iconPharma3 = '<img '.createComIcon($root_path,'pill-016.gif','0').'>';
                        $TP_SHOW_Pharma3 = "<a href=\"javascript:void(0);\" onclick=\"PharmaItem($encounter_type,'ER');\" onmouseout=\"nd();\">Pharmacy Request (ER)</a>";
                }*/
            } else {

                $TP_SHOW_Pharma = "<font color='#333333'>Pharmacy Request</font>";
                $TP_SHOW_Pharma2 = "<font color='#333333'>Pharmacy Request (MG)</font>";
                /*if ($ptype=='er') {
                         $TP_iconPharma3 = '<img '.createComIcon($root_path,'pill-016.gif','0').'>';
                        $TP_SHOW_Pharma3 = "<font color='#333333'>Pharmacy Request (ER)</font>";
                }*/
            }
        }
    }

    #--added by CHa May 1, 2010 (source: hiscgh by bryan 012810)
    $view_request_history_icon = '<img ' . createComIcon($root_path, 'briefcase.png', '0') . '>';
    $view_request_history_link = "<a href=\"javascript:void(0);\" onclick=\"modeHistory('all');\" onmouseout=\"nd();\">Transaction History</a>";
    #--end cha

    #added by CHA 09-03-09
    $TP_iconMisc = '<img ' . createComIcon($root_path, 'box.png', '0') . '>';
    $TP_SHOW_Misc = "<a href=\"javascript:void(0);\" onclick=\"alert('Miscellaneous Request');\" onmouseout=\"nd();\">Miscellaneous Request</a>";
    #end cha

    if ($allow_otherrequest) {
        #added by CHA 09-03-09
        $TP_iconMisc = '<img ' . createComIcon($root_path, 'box.png', '0') . '>';
        $TP_SHOW_Misc = "<a href=\"javascript:void(0);\" onclick=\"alert('Miscellaneous Request');\" onmouseout=\"nd();\">Miscellaneous Request</a>";
        #end cha
    }
    #-----Added by Cherry 08-12-10------
    if ($is_doctor) {
        $TP_update_details_icon = '<img ' . createComIcon($root_path, 'overlays.png') . '>';
        $TP_update_details_link = "<a href=\"javascript:void(0);\" onclick=\"updateConsultation();\" onmouseout=\"nd();\">Update Consultation Data</a>";
    }
    #-----End Cherry------

    #OR
    if ($allow_orrequest) {
        $TP_iconOR = '<img ' . createComIcon($root_path, 'Heart beating.gif', '0') . '>';
        #$TP_SHOW_OR = "<a href=\"".$root_path."modules/or/request/seg-op-request-select-dept.php?area=ER&pid=$pid&encounter_nr=$encounter_nr\" target=_blank>OR Request</a>";
        #$redirectOR = $root_path.'modules/registration_admission/certificates/cert_birth_interface.php?pid='.$pid;
        if (!$is_MGH)
            $TP_SHOW_OR = "<a href=\"javascript:void(0);\" onclick=\"ORItem($is_ER);\" onmouseout=\"nd();\">OR Request</a>";
        else
            $TP_SHOW_OR = "<font color='#333333'>OR Request</font>";
    }

    if (($encounter_type == 3) || ($encounter_type == 4) || ($encounter_type == 2) || ($encounter_type == 1)) {
        //added by omick
        $clinical_chart_icon = '<img ' . createComIcon($root_path, 'chart_icon.jpg', '0') . '>';
        $clinical_chart_link = '<a href="javascript:void(0)" onclick="clinical_chart()" onmouseout="nd()">Clinical Chart</a>';
        //added by omick, 10/15/2009
        //  $or_type = $seg_ops->get_encounter_or_type($encounter_nr);
        // $array = array('OR' => 'OR/PACU', 'PACU' => 'OR/PACU', 'DR' => 'DR/NICU', 'NICU' => 'DR/NICU');
        #edited by VAN 06-27-2010
        #edited by CHA 08-18-2010
        if ($allow_add_charges) {
            $other_charges_icon = '<img ' . createComIcon($root_path, 'or_charges.png', '0') . '>';
            $other_charges_icon2 = '<img ' . createComIcon($root_path, 'or_charges.png', '0') . '>';

            #updated by VAN 06-25-2013
            $billinfo = $encounter_obj->hasSavedBilling($encounter_nr);

            if ($billinfo) {
                $bill_nr = $billinfo['bill_nr'];
                $hasfinal_bill = $billinfo['is_final'];
                $is_maygohome = $is_MGH;
            }

            #if (!$is_MGH){
            if (($bill_nr) && ($is_maygohome)) {
                #$other_charges_link = '<a href="javascript:void(0)" onclick="other_charges()" onmouseout="nd()">Examinations</a>';
                #$other_charges_link2 = '<a href="javascript:void(0)" onclick="other_charges2()" onmouseout="nd()">Other Clinic Charges</a>';
                $other_charges_link = "<font color='#333333'>Examinations</font>";
                $other_charges_link2 = "<font color='#333333'>Other Clinic Charges</font>";
            } else {
                #$other_charges_link = "<font color='#333333'>Examinations</font>";
                #$other_charges_link2 = "<font color='#333333'>Other Clinic Charges</font>";
                $other_charges_link = '<a href="javascript:void(0)" onclick="other_charges()" onmouseout="nd()">Examinations</a>';
                $other_charges_link2 = '<a href="javascript:void(0)" onclick="other_charges2()" onmouseout="nd()">Other Clinic Charges</a>';
            }
        }
    }

    /**
     * Added by Jan Chris Ogel
     * 7/3/2019
     */
    $cf4_gen_icon = '<img ' . createComIcon($root_path, 'layout_link.png', '0') . '>';
    $cf4_gen = '<a href="javascript:void(0)" onclick="cf4_gen()" onmouseout="nd()">CF4 Generator</a>';

//    $cf4pdf_icon = '<img ' . createComIcon($root_path, 'page_white_acrobat.png', '0') . '>';
//    $cf4pdf = '<a href="javascript:void(0)" onclick="cf4pdf()" onmouseout="nd()">CF4 PDF</a>';
//
//    $cf3pdf_icon = '<img ' . createComIcon($root_path, 'page_white_acrobat.png', '0') . '>';
//    $cf3pdf = '<a href="javascript:void(0)" onclick="cf3pdf()" onmouseout="nd()">CF3 PDF</a>';
    /** end ogel  */

    #if (($allow_er_user)&&($encounter_type==1)||($allow_opd_user)&&($encounter_type==2)||($allow_ipd_user)&&($encounter_type==3||$encounter_type==4)){
    if ((($allow_er_user) && ($encounter_type == 1)) || (($allow_opd_user) && ($encounter_type == 2)) || (($allow_ipd_user) && ($encounter_type == 3 || $encounter_type == 4))) {
        #Referral
        if ($allow_referral) {
            #if ($ptype!='phs'){
            $TP_iconRT = '<img ' . createComIcon($root_path, 'hfolder.gif', '0') . '>';
            $TP_SHOW_RT = "<a href=\"javascript:void(0);\" onclick=\"ReferItem();\" onmouseout=\"nd();\">Refer/Transfer Department</a>";
            #}
            $TP_iconRO = '<img ' . createComIcon($root_path, 'hfolder.gif', '0') . '>';
            $TP_SHOW_RO = "<a href=\"javascript:void(0);\" onclick=\"ReferOtherItem();\" onmouseout=\"nd();\">Refer/Transfer to Other Hospitals</a>";
        }
    }
}/*else{
	$TP_SHOW_Lab = "";
		$TP_SHOW_Blood = "";
	$TP_SHOW_Radio = "";
	$TP_SHOW_Pharma = "";
	$TP_SHOW_OR = "";
}*/
#if (!($is_doctor)&&(($dept_belong['id']=='ER')||($encounter_type==1))){
if (!($is_doctor) && (($allow_er_user) & ($allow_viewBilling) && ($encounter_type == 1))) {
    #billing
    $TP_iconBilling = '<img ' . createComIcon($root_path, 'archives.gif', '0') . '>';
    $TP_SHOW_Billing = "<a href=\"javascript:void(0);\" onclick=\"BillingItem($is_ER);\" onmouseout=\"nd();\">Billing</a>";

} else {
    $TP_SHOW_Billing = "";
}
#------------------------

#added by VAN 07-28-08
#show results
if (($is_doctor) || ($allow_labresult_read) || ($allow_labresult) || ($allow_radioresult) || ($allow_radioresult_read)) {
    if (($allow_labresult_read) || ($allow_labresult)) {
        #lab
        $TP_iconLabRes = '<img ' . createComIcon($root_path, 'chart.gif', '0') . '>';
        $TP_SHOW_LabRes = "<a href=\"javascript:void(0);\" onclick=\"LabResItem();\" onmouseout=\"nd();\">Laboratory Results</a>";

        #bloodbank
        $TP_iconBloodRes = '<img ' . createComIcon($root_path, 'document_post.gif', '0') . '>';
        $TP_SHOW_BloodRes = "<a href=\"javascript:void(0);\" onclick=\"BloodResItem();\" onmouseout=\"nd();\">Blood Bank Results</a>";

    }

    if (($allow_radioresult) || ($allow_radioresult_read)) {
        #radio
        $TP_iconRadioRes = '<img ' . createComIcon($root_path, 'notepad.gif', '0') . '>';
        $TP_SHOW_RadioRes = "<a href=\"javascript:void(0);\" onclick=\"RadioResItem();\" onmouseout=\"nd();\">Radiology Results</a>";
    }


    if ((($is_doctor) || ($allow_ipd_user)) && (($encounter_type == 1) || ($encounter_type == 2))) {
        $TP_iconAdmit = '<img ' . createComIcon($root_path, 'patdata.gif', '0') . '>';
        #$TP_SHOW_Admit = "<a href=\"javascript:void(0);\" onclick=\"admitPatient(".$HTTP_SESSION_VARS['sess_en'].",".$encounter_type.");\" onmouseout=\"nd();\">Admit Patient</a>";
        /*window.location.href="aufnahme_start.php<?php echo URL_REDIRECT_APPEND ?>&encounter_nr="+encounter_nr+"&update=1&ptype="<?=$ptype?>;*/
        $TP_SHOW_Admit = '<a href="aufnahme_start.php' . URL_APPEND . '&encounter_nr=' . $encounter_nr . '&update=1&ptype=' . $ptype . '&istobeadmitted=1">Admit Patient</a>';
    }
} else {
    $TP_SHOW_LabRes = "";
    $TP_SHOW_BloodRes = "";
    $TP_SHOW_RadioRes = "";
    $TP_SHOW_Admit = "";
}
#-----------------------


ob_start();
include_once($root_path . 'modules/registration_admission/include/yh_options.php');
$temp3 = ob_get_contents();
ob_end_clean();

$TP_YH_OPTIONS = $temp3;

# If encounter_status empty or 'allow_cancel', show the cancel option link
if (!$data_entry && ($enc_status['encounter_status'] != 'cancelled') && !$enc_status['is_discharged'] && ($cancel)) {

    if (($ptype == 'opd') || ($ptype == 'er') || ($ptype == 'phs'))
        $TP_xenc_BLK = "<a href=\"javascript:cancelEnc('" . $HTTP_SESSION_VARS['sess_en'] . "')\">Cancel Consultation</a>";
    else
        $TP_xenc_BLK = "<a href=\"javascript:cancelEnc('" . $HTTP_SESSION_VARS['sess_en'] . "')\">$LDCancelThisAdmission</a>";

} else {
    $TP_xenc_BLK = "<font color='#333333'>$LDCancelThisAdmission</font>";
}

# Load the template
$TP_options = $TP_obj->load('registration_admission/tp_pat_admit_options.htm');
#echo "option = ".$TP_options;
eval("echo $TP_options;");
?>

<div class="segPanel" id="usrpwDialog" style="display:none" align="left">
    <div align="center" style="overflow:hidden">
        <br/>
        Username: &nbsp<input type="text" name="username" id="username" value="">
        <br/>
        <br/>
        Password: &nbsp<input type="password" name="password" id="password" value="">
    </div>
</div>
