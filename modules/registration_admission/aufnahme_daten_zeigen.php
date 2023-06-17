<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'global_conf/areas_allow.php');
/*
CARE2X Integrated Information System beta 2.0.1 - 2004-07-04 for Hospitals and Health Care Organizations and Services
Copyright (C) 2002,2003,2004,2005  Elpidio Latorilla & Intellin.org
GNU GPL.
For details read file "copy_notice.txt".
*/
#echo "er permission = ".$allow_er_user;
#--------------- EDITED BY VANESSA -----------------------
$lang_tables[] = 'prompt.php';
$lang_tables[] = 'person.php';
$lang_tables[] = 'departments.php';
define('LANG_FILE', 'aufnahme.php');
define('NO_2LEVEL_CHK', 1);
#commented by VAN 01-25-08
#$local_user='aufnahme_user';

#added by VAN 01-25-08
if ($fromnurse)
    $local_user = 'ck_pflege_user';
else
    $local_user = 'aufnahme_user';

require_once($root_path . 'include/inc_front_chain_lang.php');

require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_insurance.php');
require_once($root_path . 'include/care_api_classes/class_ward.php');
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');

#---------added 03-05-07-----------
include_once($root_path . 'include/care_api_classes/class_department.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');

require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

$dept_obj = new Department;
$pers_obj = new Personell;

$ERSave = $_GET['ERSave'];

$area_type = $_GET['area_type'];
#echo "area = ".$area_type;
$phsObj = $objInfo->getAllPHSInfo();

#added by bryan 02-24-09
require_once($root_path . 'include/care_api_classes/class_vitalsign.php');
$vitals_obj = new SegVitalsign();

#added by cha, august 21, 2010
require_once($root_path . 'include/care_api_classes/class_request_source.php');
$req_src_obj = new SegRequestSource();
if ($ptype == 'ipd') {
    $request_source = $req_src_obj->getSourceIPDClinics();
} else if ($ptype == 'er') {
    $request_source = $req_src_obj->getSourceERClinics();
} else if ($ptype == 'opd') {
    $request_source = $req_src_obj->getSourceOPDClinics();
} else if ($ptype == 'phs') {
    $request_source = $req_src_obj->getSourcePHSClinics();
} else if ($ptype == 'nursing') {
    $request_source = $req_src_obj->getSourceNursingWard();
}
#end cha


if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
    $seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
    $seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

#echo "<br>user = ".$seg_user_name;

$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);
#echo "dept = ".$dept_belong['id'];
#print_r($dept_belong);
#echo  $dept_obj->sql;
if (stristr($dept_belong['job_function_title'], 'doctor') === FALSE)
    #echo "not doctor";
    $is_doctor = 0;
else
    #echo "doctor";
    $is_doctor = 1;

#commented by VAN 11-10-2008
/*
if (empty($dept_belong['id']))
    $dept_belong['id'] = "Admission";
*/
#---------added 03-05-07-----------

if (!session_is_registered('sess_parent_mod')) session_register('sess_parent_mod');
# Create objects

$encounter_obj = new Encounter($encounter_nr);
$person_obj = new Person();
$insurance_obj = new Insurance;

#----------------added 03-09-07----------------
if ($encounter_nr != NULL) {
    $patient_enc = $encounter_obj->getPatientEncounter($encounter_nr);
}
#-------------------------------------------------

$thisfile = basename(__FILE__);

#edited by VAN 01-28-08
/*
if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $breakfile=$root_path.'main/startframe.php'.URL_APPEND;
	else $breakfile='aufnahme_pass.php'.URL_APPEND.'&target=entry';
*/

/*
if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $breakfile="javascript: window.close()";
	else $breakfile='aufnahme_pass.php'.URL_APPEND.'&target=entry';
*/
#edited by VAN 04-16-08

#if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $breakfile="show_encounter_list.php".URL_APPEND;
#	else $breakfile='aufnahme_pass.php'.URL_APPEND.'&target=entry';

if ($HTTP_COOKIE_VARS['ck_login_logged' . $sid]) $breakfile = "aufnahme_daten_such.php" . URL_APPEND;
else $breakfile = 'aufnahme_pass.php' . URL_APPEND . '&target=entry';

if ($_GET['popUp'] == 1)
    $breakfile = 'javascript:window.close()';

$GLOBAL_CONFIG = array();
$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

/* Get the patient global configs */
$glob_obj->getConfig('patient_%');
$glob_obj->getConfig('person_foto_path');

$updatefile = 'aufnahme_start.php';

/* Default path for fotos. Make sure that this directory exists! */
$default_photo_path = $root_path . 'fotos/registration';
$photo_filename = 'nopic';

$dbtable = 'care_encounter';
if (($encounter_type == 3) || ($encounter_type == 4)) {
    if (!empty($GLOBAL_CONFIG['patient_financial_class_single_result'])) $encounter_obj->setSingleResult(true);

    if (!$GLOBAL_CONFIG['patient_service_care_hide']) {
        /* Get the care service classes*/
        $care_service = $encounter_obj->AllCareServiceClassesObject();

        if ($buff =& $encounter_obj->CareServiceClass()) {
            $care_class = $buff->FetchRow();
            extract($care_class);
            reset($care_class);
        }
    }
    if (!$GLOBAL_CONFIG['patient_service_room_hide']) {
        /* Get the room service classes */
        $room_service = $encounter_obj->AllRoomServiceClassesObject();

        if ($buff =& $encounter_obj->RoomServiceClass()) {
            $room_class = $buff->FetchRow();
            //while(list($x,$v)=each($room_class))	$$x=$v;
            extract($room_class);
            reset($room_class);
        }
    }
    if (!$GLOBAL_CONFIG['patient_service_att_dr_hide']) {
        /* Get the attending doctor service classes */
        $att_dr_service = $encounter_obj->AllAttDrServiceClassesObject();

        if ($buff =& $encounter_obj->AttDrServiceClass()) {
            $att_dr_class = $buff->FetchRow();
            //while(list($x,$v)=each($att_dr_class))	$$x=$v;
            extract($att_dr_class);
            reset($att_dr_class);
        }
    }
}
$encounter_obj->loadEncounterData();
#echo $encounter_obj->sql;
if ($encounter_obj->is_loaded) {
    $row =& $encounter_obj->encounter;
    //load data
    extract($row);

    # Set edit mode
    if (!$is_discharged) $edit = true;
    else $edit = false;
    # Fetch insurance and encounter classes
    if (($encounter_type == 3) || ($encounter_type == 4)) {
        $insurance_class =& $encounter_obj->getInsuranceClassInfo($insurance_class_nr);
        #echo "sql = ".$encounter_obj->sql;
        $encounter_class =& $encounter_obj->getEncounterClassInfo($encounter_class_nr);
    }
    #------------added by VAN 03-08-07---------------------

    if ($encounter_type != 2) {
        $patient_enc_cond = $encounter_obj->getPatientEncounterCond($encounter_nr);
        $patient_enc_disp = $encounter_obj->getPatientEncounterDisp($encounter_nr);
        $patient_enc_res = $encounter_obj->getPatientEncounterRes($encounter_nr);

        if ($update == 1) {
            if ($cond_code != NULL) {
                $encounter_condition =& $encounter_obj->getEncounterConditionInfo($cond_code);
            } else {
                $encounter_condition =& $encounter_obj->getEncounterConditionInfo($patient_enc_cond['cond_code']);
            }
            $encounter_disposition =& $encounter_obj->getEncounterDispositionInfo($disp_code);
            $encounter_result =& $encounter_obj->getEncounterResultInfo($result_code);
        } else {
            $encounter_condition =& $encounter_obj->getEncounterConditionInfo($patient_enc_cond['cond_code']);
            $encounter_disposition =& $encounter_obj->getEncounterDispositionInfo($patient_enc_disp['disp_code']);
            $encounter_result =& $encounter_obj->getEncounterResultInfo($patient_enc_res['result_code']);
        }
    }
    #-----------------------------------------------

    $list = 'title,name_first,name_last,name_2,name_3,name_middle,name_maiden,name_others,date_birth,
						 sex,addr_str,addr_str_nr,addr_zip,addr_citytown_nr,photo_filename';

    $person_obj->setPID($pid);
    if ($row =& $person_obj->getValueByList($list)) {
        extract($row);
    }
    #commented by VAN 01-25-10
    #$addr_citytown_name=$person_obj->CityTownName($addr_citytown_nr);
    #echo "sql person = ".$person_obj->sql;
    $encoder = $encounter_obj->RecordModifierID($encounter_nr);
    #echo "sql enc = ".$person_obj->sql;
    # Get current encounter to check if current encounter is this encounter nr
    #$current_encounter=$person_obj->CurrentEncounter($pid);
    #edited by VAN 01-25-10
    $current_encounter = $person_obj->CurrentEncounter2($encounter_nr);
    #echo "<br>sql person = ".$person_obj->sql;
    # Get the overall status
    if ($stat =& $encounter_obj->AllStatus($encounter_nr)) {
        $enc_status = $stat->FetchRow();
    }
    #echo "permi = ".$allow_opd_user;
    #echo "<br>sql enc = ".$encounter_obj->sql;
    #echo "<br>ward = ".$current_ward_nr;
    # Get ward or department infos
    #if($encounter_class_nr==1){
    #if (($dept_belong['id']=="Admission")&&(($encounter_type==3)||($encounter_type==4))){
    if (($allow_ipd_user) && (($encounter_type == 3) || ($encounter_type == 4))) {
        # Get ward name
        include_once($root_path . 'include/care_api_classes/class_ward.php');
        $ward_obj = new Ward;
        $current_ward_name = $ward_obj->WardName($current_ward_nr);
    }#elseif($encounter_class_nr==2){  #----------03-05-07---------------
    # Get ward name
    $current_dept_LDvar = $dept_obj->LDvar($current_dept_nr);

    if (isset($$current_dept_LDvar) && !empty($$current_dept_LDvar)) $current_dept_name = $$current_dept_LDvar;
    else $current_dept_name = $dept_obj->FormalName($current_dept_nr);
    #}#----------03-05-07---------------

}

include_once($root_path . 'include/inc_date_format_functions.php');

/* Update History */
if (!$newdata) $encounter_obj->setHistorySeen($HTTP_SESSION_VARS['sess_user_name'], $encounter_nr);
/* Get insurance firm name*/
if (($encounter_type == 3) || ($encounter_type == 4))
    $insurance_firm_name = $insurance_obj->getFirmName($insurance_firm_id);
/* Check whether config path exists, else use default path */
$photo_path = (is_dir($root_path . $GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;


/* Prepare text and resolve the numbers */
require_once($root_path . 'include/inc_patient_encounter_type.php');

/* Save encounter nrs to session */
$HTTP_SESSION_VARS['sess_pid'] = $pid;
$HTTP_SESSION_VARS['sess_en'] = $encounter_nr;
$HTTP_SESSION_VARS['sess_full_en'] = $full_en;
$HTTP_SESSION_VARS['sess_parent_mod'] = 'admission';
$HTTP_SESSION_VARS['sess_user_origin'] = 'admission';
$HTTP_SESSION_VARS['sess_file_return'] = $thisfile;

/* Prepare the photo filename */
require_once($root_path . 'include/inc_photo_filename_resolve.php');
echo '<script type="text/javascript" src="' . $root_path . 'js/shortcuts.js"></script>';
?>
<!---------added by VAN----------->
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="js/vitals.js?t=<?= time() ?>"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="js/reg-insurance-gui.js?t=<?= time() ?>"></script>
<script language="javascript">

    //added by VAN 08-29-09
    function enableTextBox(objID) {
        //alert('case = '+obj.id);
        if (objID == 'medicoOT') {
            if (document.getElementById(objID).checked)
                document.getElementById('description').style.display = "";
            else
                document.getElementById('description').style.display = "none";
        }
    }

    function preSet(is_medico) {
        //var is_medico = document.getElementById('is_medico_case').value;
        //alert('just ignore this message 2 = '+is_medico);
        var ptype = '<?=$ptype?>';
        loadClinical();
        if ((ptype == 'er') || (ptype == 'ipd')) {
            if (is_medico == 1) {
                document.getElementById('ERMedico').style.display = '';
                //added by VAN 06-12-08
                document.getElementById('ERMedicoPOI').style.display = '';
                document.getElementById('ERMedicoTOI').style.display = '';
                document.getElementById('ERMedicoDOI').style.display = '';

                if (document.getElementById('description').value == 'none') {
                    enableTextBox('medicoOT');
                }
            } else {
                document.getElementById('ERMedico').style.display = 'none';
                //added by VAN 06-12-08
                document.getElementById('ERMedicoPOI').style.display = 'none';
                document.getElementById('ERMedicoTOI').style.display = 'none';
                document.getElementById('ERMedicoDOI').style.display = 'none';
            }
        }
    }

    //OPD
    shortcut("F2",
        function () {
            var ptype = '<?=$ptype?>';
            var mod;

            if (ptype == 'ipd')
                mod = 3;
            else if (ptype == 'opd')
                mod = 2;
            else if (ptype == 'er')
                mod = 1;
            //alert('p = '+ptype);
            //viewClinicalForm(2);
            viewClinicalForm(mod);
        }
    );
    /*
    //ER
    shortcut("F4",
        function(){
            viewClinicalForm(1);
        }
    );

    //IPD
    shortcut("F6",
        function(){
            viewClinicalForm(3);
        }
    );
    */
    shortcut("Esc",
        function () {
            urlholder = "../../modules/registration_admission/patient_register_search.php?<?=URL_APPEND?>&origin=pass&target=search&checkintern=1";
            window.location.href = urlholder;
        }
    );

    function viewClinicalForm(formID) {
        if (formID == 1) {
            window.open("../../modules/registration_admission/show_er_clinical_form.php?encounter_nr="+<?=$encounter_nr?>+
            "&showBrowser=1", "viewClinicalForm", "width=950,height=700,fullscreen=yes,menubar=no,resizable=yes,scrollbars=yes"
        )
            ;
        } else if (formID == 2) {
            window.open("../../modules/registration_admission/show_opd_clinical_form.php?encounter_nr="+<?=$encounter_nr?>+
            "&showBrowser=1", "viewClinicalForm", "width=800,height=700,menubar=no,resizable=yes,scrollbars=yes"
        )
            ;
        } else if (formID == 3) {
            window.open("../../modules/registration_admission/show_cover_sheet.php?encounter_nr="+<?=$encounter_nr?>+
            "&showBrowser=1", "viewClinicalForm", "width=800,height=700,menubar=no,resizable=yes,scrollbars=yes"
        )
            ;
        }
    }

    function viewCertMed(pid) {
        //window.open("../../modules/registration_admission/certificates/cert_med_interface.php?encounter_nr="+<?=$encounter_nr?>+"&showBrowser=1","viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
        return overlib(
            OLiframeContent("../../modules/registration_admission/med_cert_history.php?pid=" + pid, 850, 440, "fOrderTray", 1, "auto"),
            WIDTH, 440, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, "<img src=../../images/close.gif border=0 >",
            CAPTIONPADDING, 4, CAPTION, "MEDICAL CERTIFICATE HISTORY",
            MIDX, 0, MIDY, 0,
            STATUS, "MEDICAL CERTIFICATE HISTORY");
    }

    function viewCertConf() {
        window.open("../../modules/registration_admission/certificates/cert_conf_interface.php?encounter_nr="+<?=$encounter_nr?>+
        "&showBrowser=1", "viewClinicalForm", "width=800,height=700,menubar=no,resizable=yes,scrollbars=yes"
    )
        ;
    }

    function viewDeathError() {
        window.open("../../modules/registration_admission/certificates/cert_Death_erroneousEntry_pdf.php?pid="+<?=$pid?>+
        "&encounter_nr="+<?=$encounter_nr?>+
        "&showBrowser=1", "viewClinicalForm", "width=800,height=700,menubar=no,resizable=yes,scrollbars=yes"
    )
        ;
    }

    //added by VAN 07-28-08

    function BloodResItem() {
        return overlib(
            OLiframeContent('../../modules/laboratory/seg-lab-request-result-patient-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&user_origin=lab&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
                850, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 850, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Blood Bank Results',
            MIDX, 0, MIDY, 0,
            STATUS, 'Blood Bank Results');
    }

    function LabResItem() {
        return overlib(
            OLiframeContent('../../modules/laboratory/seg-lab-request-result-patient-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&user_origin=lab&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
                800, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Laboratory Results',
            MIDX, 0, MIDY, 0,
            STATUS, 'Laboratory Results');
    }

    function RadioResItem() {
        // 855, 450
        return overlib(
            OLiframeContent('../../modules/radiology/radiology_patient_request.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dept_nr=158',
                800, 440, 'fGroupTray', 1, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 4, CAPTION, 'Radiology Results',
            MIDX, 0, MIDY, 0,
            STATUS, 'Radiology Results');
    }

    //------------------------

    function BillingItem(is_ER) {
        var area;

        if (is_ER)
            area = "ER";
        else
            area = "clinic";

        return overlib(
            OLiframeContent('../../modules/billing/billing-main.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=' + area + '&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dept_belong['personell_nr']?>&dept_nr=<?=$dept_belong['dept_nr']?>&is_dr=<?=$is_doctor?>',
                850, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 850, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Billing',
            MIDX, 0, MIDY, 0,
            STATUS, 'Billing');
    }

    function js_showBillDetails() {
        //var rpath = $('root_path').value;
        var enc = '<?=$encounter_nr?>';
        var pid = '<?=$pid?>';
        var bill_dt = '<?=date('Y-m-d')?>';
        //var frm_dte = $('bill_frmdte').value;
        var frm_dte = '<?=date('Y-m-d')?>';

        //added by VAN 02-13-08
        var detailed;
        //if ($('IsDetailed').checked)
        detailed = 1;
        //else
        //    detailed = 0;

        urlholder = '../../modules/billing/bill-pdf-summary.php?pid=' + pid + '&encounter_nr=' + enc + '&from_dt=' + frm_dte + '&bill_dt=' + bill_dt + '&IsDetailed=' + detailed;

        nleft = (screen.width - 680) / 2;
        ntop = (screen.height - 520) / 2;
        printwin = window.open(urlholder, "Print Billing", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
    }

    //added by VAN 07-11-08
    function BloodItem(is_ER) {
        var area;

        if (is_ER)
            area = "ER";
        else
            area = "clinic";

        return overlib(
            OLiframeContent('../../modules/bloodBank/seg-blood-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&user_origin=blood&popUp=1&area=' + area + '&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dept_belong['personell_nr']?>&dept_nr=<?=$dept_belong['dept_nr']?>&is_dr=<?=$is_doctor?>&ptype=<?=$ptype?>&area_type=<?=$area_type?>&ischecklist=1',
                800, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Blood Bank Request',
            MIDX, 0, MIDY, 0,
            STATUS, 'Blood Bank Request');
    }

    //Added by Cherry 08-12-10
    function reloadWindow() {
        window.location.href = window.location.href;
    }

    function updateConsultation() {
        var area = "";

        //in the meantime
        return overlib(
            OLiframeContent('../../modules/registration_admission/seg-patient-update-consult-details.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&user_origin=blood&popUp=1&area=' + area + '&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dept_belong['personell_nr']?>&dept_nr=<?=$dept_belong['dept_nr']?>&is_dr=<?=$is_doctor?>&ptype=<?=$ptype?>&area_type=<?=$area_type?>&ischecklist=1',
                800, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="reloadWindow();">',
            CAPTIONPADDING, 2, CAPTION, 'Update Consultation Details',
            MIDX, 0, MIDY, 0,
            STATUS, 'Update Consultation Details');
    }

    //End Cherry

    function SpecialLabItem(is_ER) {
        var area;

        if (is_ER)
            area = "ER";
        else
            area = "clinic";

        return overlib(
            OLiframeContent('../../modules/special_lab/seg-splab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&user_origin=splab&popUp=1&area=' + area + '&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dept_belong['personell_nr']?>&dept_nr=<?=$dept_belong['dept_nr']?>&is_dr=<?=$is_doctor?>&ptype=<?=$ptype?>&area_type=<?=$area_type?>&ischecklist=1',
                800, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Special Laboratory Request',
            MIDX, 0, MIDY, 0,
            STATUS, 'Special Laboratory Request');
    }

    function LabItem(is_ER) {
        var area;

        if (is_ER)
            area = "ER";
        else
            area = "clinic";

        return overlib(
            OLiframeContent('../../modules/laboratory/seg-lab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&user_origin=lab&popUp=1&area=' + area + '&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dept_belong['personell_nr']?>&dept_nr=<?=$dept_belong['dept_nr']?>&is_dr=<?=$is_doctor?>&ptype=<?=$ptype?>&area_type=<?=$area_type?>&ischecklist=1',
                770, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 770, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Laboratory Request',
            MIDX, 0, MIDY, 0,
            STATUS, 'Laboratory Request');
    }

    function RadioItem(is_ER) {
        var area;

        if (is_ER)
            area = "ER";
        else
            area = "clinic";

        return overlib(
            OLiframeContent('../../modules/radiology/seg-radio-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=' + area + '&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dept_belong['personell_nr']?>&dept_nr=<?=$dept_belong['dept_nr']?>&is_dr=<?=$is_doctor?>&ptype=<?=$ptype?>&area_type=<?=$area_type?>&ischecklist=1',
                800, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Radiology Request',
            MIDX, 0, MIDY, 0,
            STATUS, 'Radiology Request');
    }

    function PharmaItem(enctype, area) {
        var area;
        /*
        if (is_ER)
            area = "ER";
        else
            area = "clinic";
        */
        /*
        if (enctype==1)
            //area = "ER";
                        area = "IP";
        else if (enctype==2)
            //area = "MG";
                        area = "IP";
        else if (enctype==5)
            //area = "PHS";
                        area = "IP";
        else if ((enctype==3)||(enctype==4)||(enctype==6))
            area = "IP";
        */

        return overlib(
            OLiframeContent('../../modules/pharmacy/seg-pharma-order.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&ptype=<?=$ptype?>&area=' + area + '&pid=<?=$pid?>&encounterset=<?=$encounter_nr?>&is_dr=<?=$is_doctor?>&billing=1&request_source=<?=$request_source?>',
                800, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Pharmacy Request',
            MIDX, 0, MIDY, 0,
            STATUS, 'Pharmacy Request');
    }

    //added by cha, 05012010 (source:hiscgh by bryan 012810)
    function modeHistory(mode) {
        return overlib(
            OLiframeContent('../../modules/registration_admission/seg-mode-history.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&ptype=<?=$ptype?>&pid=<?=$pid?>&encounterset=<?=$encounter_nr?>&is_dr=<?=$is_doctor?>&mode=' + mode,
                800, 420, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Requests History',
            MIDX, 0, MIDY, 0,
            STATUS, 'Requests History');
    }

    //added by omick 10/2/2009
    //modified by cha 07/07/2010
    function other_charges() {
        var ptype = "<?=$ptype?>";
        //var url = '<?=$root_path?>modules/or/request/op_request_pass.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=OR&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ward=<?=$current_ward_nr?>&target='+(ptype=='opd'?'opd_clinic_charges':'or_other_charges_get');
        var url = '<?=$root_path?>modules/or/request/op_request_pass.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&target=clinic_charges&dr_nr=<?=$dept_belong['personell_nr']?>&dept_nr=<?=$dept_belong['dept_nr']?>&is_dr=<?=$is_doctor?>&ptype=<?=$ptype?>&area_type=<?=$area_type?>';
        return overlib(
            OLiframeContent(url,
                850, 400, 'fGroupTray', 0, 'auto'),
            WIDTH, 850, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, DRAGGABLE, CAPTION, 'Clinical Examinations and other Requests',
            MIDX, 0, MIDY, 0,
            STATUS, 'Clinical Examinations and other Requests');
    }

    function other_charges2() {
        return overlib(
            OLiframeContent('../../modules/or/request/op_request_pass.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=OR&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ward=<?=$current_ward_nr?>&target=or_other_charges_get',
                850, 400, 'fGroupTray', 0, 'auto'),
            WIDTH, 850, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, DRAGGABLE, CAPTION, 'Other Clinic Charges',
            MIDX, 0, MIDY, 0,
            STATUS, 'Other Clinic Charges');
    }

    function ORItem(is_ER) {
        var area;
        var ptype = '<?=$ptype?>';

        /*	if (ptype=='ipd')
               mod = 3;
           else if (ptype=='opd')
               mod = 2;
           else if (ptype=='er')
               mod = 1;		 */

        if (ptype == 'ipd' || ptype == 'er')
            return overlib(
                OLiframeContent('../../modules/or/request/op_request_pass.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=OR&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ptype=<?=$ptype?>&target=or_main',     //edited by CHa, April 6,2010
                    800, 440, 'fGroupTray', 1, 'auto'),
                WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
                CAPTIONPADDING, 4, CAPTION, 'Operating Room Request',
                MIDX, 0, MIDY, 0,
                STATUS, 'Operating Room Request');
        else if (ptype == 'opd')
            return overlib(
                OLiframeContent('../../modules/or/request/op_request_pass.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=OR&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ptype=<?=$ptype?>&target=or_asu_request_get',
                    800, 440, 'fGroupTray', 1, 'auto'),
                WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
                CAPTIONPADDING, 4, CAPTION, 'Operating Room Request',
                MIDX, 0, MIDY, 0,
                STATUS, 'Operating Room Request');
        /*
        if (is_ER)
            area = "ER";
        else
            area = "clinic";
            */
        return overlib(
            OLiframeContent('../../modules/or/request/op_request_pass.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=OR&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&target=or_main_request_get',
                800, 440, 'fGroupTray', 1, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 4, CAPTION, 'Operating Room Request',
            MIDX, 0, MIDY, 0,
            STATUS, 'Operating Room Request');
    }

    function ReferItem() {

        return overlib(
            OLiframeContent('seg-patient-admission.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&pid=<?php echo "$pid"?>&encounter_nr=<?=$encounter_nr?>&status=show',
                600, 350, 'fDiagnosis', 1, 'auto'),
            WIDTH, 600, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 4, CAPTION, 'Refer/Transfer Patient to other Department',
            MIDX, 0, MIDY, 0,
            STATUS, 'Refer/Transfer Patient to other Department');
    }

    function ReferOtherItem() {

        return overlib(
            OLiframeContent('seg-patient-admission.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&pid=<?php echo "$pid"?>&encounter_nr=<?=$encounter_nr?>&status=show&is_dept=hosp',
                600, 350, 'fDiagnosis', 1, 'auto'),
            WIDTH, 600, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 4, CAPTION, 'Refer/Transfer Patient to other Hospital',
            MIDX, 0, MIDY, 0,
            STATUS, 'Refer/Transfer Patient to other Hospital');
    }

    function Vitals() {

        return overlib(
            OLiframeContent('<?=$root_path?>modules/registration_admission/seg-vitalsopen.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&target=dependents&popUp=1&pid=<?=$pid?>',
                800, 440, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
            CAPTIONPADDING, 2, CAPTION, 'Vital Signs',
            MIDX, 0, MIDY, 0,
            STATUS, 'Vital Signs');
    }

    function loadClinical() {
        var ERSave = '<?=$ERSave?>';

        if (ERSave == 1) {
            //if (window.showModalDialog){
            window.showModalDialog('<?=$root_path?>modules/registration_admission/show_er_clinical_form.php?encounter_nr=<?=$encounter_nr?>', 'width=900,height=700,menubar=no,resizable=yes,scrollbars=no');
            //}else{
            //	window.open('<?=$root_path?>modules/registration_admission/show_er_clinical_form.php?encounter_nr=<?=$encounter_nr?>','ERClinicalForm','modal,width=900,height=700,menubar=no,resizable=yes,scrollbars=no');
            //}
        }
    }

    function clinical_chart() {
        window.open('<?=$root_path?>modules/nursing/clinical_chart.php?pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>', 'view_clinical_chart', 'fullscreen=yes,menubar=no,resizable=yes,scrollbars=yes');
    }

    var ptype = '<?=$ptype?>';
    //var ERSave = '<?=$_GET['ERSave']?>';

    //if (ERSave==1)
    //document.body.onLoad = loadClinical();

    /**
     * Added by Jan Chris Ogel
     * 7/3/2019
     */
    function cf4_gen() {
        var url = '<?=$root_path?>index.php?r=eclaims/generator/index&encounter_nr=' + '<?=$encounter_nr?>';
        window.open(url, '_blank');
    }

    function cf4pdf() {
        var url = '<?=$root_path?>index.php?r=eclaims/generator/index&encounter_nr=' + '<?=$encounter_nr?>';
        window.open(url, '_blank');
    }

    function cf3pdf() {
        var url = '<?=$root_path?>index.php?r=eclaims/generator/index&encounter_nr=' + '<?=$encounter_nr?>';
        window.open(url, '_blank');
    }
    /** end ogel */


</script>

<?

# Start Smarty templating here
/**
 * LOAD Smarty
 */
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

#edited by VAN 04-17-08
$smarty->assign('segAdmissionShow', "true");

# Title in the toolbar
# $smarty->assign('sToolbarTitle',$LDPatientData.' ('.$encounter_nr.')');   # burn commented : May 15, 2007
if (($encounter_type == '1') || ($encounter_type == '2')) {
    $smarty->assign('sToolbarTitle', $LDConsultationData . ' (' . $encounter_nr . ')');   # burn added : May 15, 2007
} else {
    $smarty->assign('sToolbarTitle', $LDPatientData . ' (' . $encounter_nr . ')');   # burn added : May 15, 2007
}
/*
	if ($ERSave){
		$openWindow = "
				if (window.showModalDialog){
					window.showModalDialog('".$root_path."modules/registration_admission/show_er_clinical_form.php?encounter_nr=".$encounter_nr."','width=900,height=700,menubar=no,resizable=yes,scrollbars=no');
				}else{
					window.open('".$root_path."modules/registration_admission/show_er_clinical_form.php?encounter_nr=".$encounter_nr."','ERClinicalForm','modal,width=900,height=700,menubar=no,resizable=yes,scrollbars=no');
				}";

		$smarty->assign('sOnLoadJs',"onLoad=\"javascript: $openWindow \"");
	}
*/
if (($allow_er_user) && ($encounter_type == 1)) {
    $area_p = 'ER';
} else {
    $area_p = 'clinic';
}
#echo "area_type = ".$area_type;
$HTTP_SESSION_VARS['url'] = "&area=" . $area_p . "&pid=" . $pid . "&encounter_nr=" . $encounter_nr . "&dr_nr=" . $dept_belong['personell_nr'] . "&dept_nr=" . $dept_belong['dept_nr'] . "&is_dr=" . $is_doctor . "&ptype=" . $ptype . "&area_type=" . $area_type;

# href for help button
$smarty->assign('pbHelp', "javascript:gethelp('admission_how2new.php')");

if ((($ptype == 'nursing') && ($_GET['popUp']) || ($_GET['fromnurse'])))
    $breakfile = '';

#$smarty->assign('breakfile',$breakfile);
$smarty->assign('breakfile', $breakfile);

# Window bar title
if (($encounter_type == '1') || ($encounter_type == '2')) {
    $smarty->assign('title', $LDConsultationData . ' (' . $encounter_nr . ')');   # burn added : May 15, 2007
} else {
    $smarty->assign('title', $LDPatientData . ' (' . $encounter_nr . ')');   # burn added : May 15, 2007
}

# href for help button
$smarty->assign('pbHelp', "javascript:gethelp('admission_show.php','$from')");

# Hide the return button
$smarty->assign('pbBack', FALSE);

# Collect extra javascript

ob_start();

require($root_path . 'include/inc_js_barcode_wristband_popwin.php');
require('./include/js_poprecordhistorywindow.inc.php');

$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript', $sTemp);

# Load tabs
$parent_admit = TRUE;
#echo "fromnurse = ".$fromnurse;
if (!$fromnurse)
    include('./gui_bridge/default/gui_tabs_patadmit.php');

if ($is_discharged) {

    $smarty->assign('is_discharged', TRUE);
    $smarty->assign('sWarnIcon', "<img " . createComIcon($root_path, 'warn.gif', '0', 'absmiddle') . ">");
    if ($current_encounter) $smarty->assign('sDischarged', $LDEncounterClosed);
    else {
        if ($death_date != '0000-00-00')
            $smarty->assign('sDischarged', $LDPatientIsDischarged . " and already dead.");
        else
            $smarty->assign('sDischarged', $LDPatientIsDischarged);
    }
}

if ($is_DOA == 1) {
    $smarty->assign('is_discharged', TRUE);
    $smarty->assign('sWarnIcon', "<img " . createComIcon($root_path, 'warn.gif', '0', 'absmiddle') . ">");
    $smarty->assign('sDischarged', "This patient is already dead.");
}

#added by VAN 05-26-08
$patient_result = $encounter_obj->getPatientEncounterResult($encounter_nr);
if (($patient_result['result_code'] == 4) || ($patient_result['result_code'] == 8))
    $isDied = 1;
else
    $isDied = 0;
#------------------

$smarty->assign('LDCaseNr', $LDCaseNr);
$smarty->assign('encounter_nr', $encounter_nr);

# Create the encounter barcode image

if (file_exists($root_path . 'cache/barcodes/en_' . $encounter_nr . '.png')) {
    $smarty->assign('sEncBarcode', '<img src="' . $root_path . 'cache/barcodes/en_' . $encounter_nr . '.png" border=0 width=180 height=35>');
} else {
    $smarty->assign('sHiddenBarcode', "<img src='" . $root_path . "classes/barcode/image.php?code=" . $encounter_nr . "&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2&form_file=en' border=0 width=0 height=0>");
    $smarty->assign('sEncBarcode', "<img src='" . $root_path . "classes/barcode/image.php?code=" . $encounter_nr . "&style=68&type=I25&width=180&height=40&xres=2&font=5' border=0>");
}

$smarty->assign('img_source', "<img $img_source>");

if (($encounter_type == '1') || ($encounter_type == '2')) {
    $smarty->assign('LDAdmitDate', $LDConsultDate);
    $smarty->assign('LDAdmitTime', $LDConsultTime);
} else {
    $smarty->assign('LDAdmitDate', $LDAdmitDate);
    $smarty->assign('LDAdmitTime', $LDAdmitTime);
}

# burn added: March 29, 2007
if (($encounter_type == 1) || ($encounter_type == 2)) {
    # ER/OPD
    $segAdmitDateTime = $encounter_date;
} else {
    # Inpatient
    $segAdmitDateTime = $admission_dt;
}

$smarty->assign('sAdmitDate2', @formatDate2Local($segAdmitDateTime, $date_format));
$smarty->assign('sAdmitDate', @formatDate2Local($segAdmitDateTime, $date_format));   # burn added: March 29, 2007
$smarty->assign('sAdmitTime', @formatDate2Local($segAdmitDateTime, $date_format, 1, 1));   # burn added: March 29, 2007

$smarty->assign('LDTitle', $LDTitle);
$smarty->assign('title', $title);
$smarty->assign('LDLastName', $LDLastName);
$smarty->assign('name_last', $name_last);
$smarty->assign('LDFirstName', $LDFirstName);
$smarty->assign('name_first', $name_first);

# If person is dead show a black cross and assign death date

if ($death_date && $death_date != DBF_NODATE) {
    $smarty->assign('sCrossImg', '<img ' . createComIcon($root_path, 'blackcross_sm.gif', '0') . '>');
    $smarty->assign('sDeathDate', @formatDate2Local($death_date, $date_format));
}

# Set a row span counter, initialize with 6
$iRowSpan = 6;

if ($GLOBAL_CONFIG['patient_name_2_show'] && $name_2) {
    $smarty->assign('LDName2', $LDName2);
    $smarty->assign('name_2', $name_2);
    $iRowSpan++;
}

if ($GLOBAL_CONFIG['patient_name_3_show'] && $name_3) {
    $smarty->assign('LDName3', $LDName3);
    $smarty->assign('name_3', $name_3);
    $iRowSpan++;
}

if ($GLOBAL_CONFIG['patient_name_middle_show'] && $name_middle) {
    $smarty->assign('LDNameMid', $LDNameMid);
    $smarty->assign('name_middle', $name_middle);
    $iRowSpan++;
}
$smarty->assign('sRowSpan', "rowspan=\"$iRowSpan\"");

$smarty->assign('LDBday', $LDBday);
$smarty->assign('sBdayDate', @formatDate2Local($date_birth, $date_format));

$smarty->assign('LDSex', $LDSex);
if ($sex == 'm') $smarty->assign('sSexType', $LDMale);
elseif ($sex == 'f') $smarty->assign('sSexType', $LDFemale);

$smarty->assign('LDBloodGroup', $LDBloodGroup);
if ($blood_group) {
    $buf = 'LD' . $blood_group;
    $smarty->assign('blood_group', $$buf);
} else {
    $smarty->assign('blood_group', 'Not Indicated');
}

## populates the vital sign details of encounter with the oldest ##
$rowvitaldataResult = $vitals_obj->getOldestVitalDetailsbyPid($pid, $encounter_nr);
$rowvitaldata = $rowvitaldataResult->FetchRow();
$unittemp = $vitals_obj->getUnitName($rowvitaldata['temp_unit']);
$unitbp = $vitals_obj->getUnitName($rowvitaldata['bp_unit']);
$unitweight = $vitals_obj->getUnitName($rowvitaldata['weight_unit']);
$unitrr = $vitals_obj->getUnitName($rowvitaldata['rr_unit']);
$unitpr = $vitals_obj->getUnitName($rowvitaldata['pr_unit']);
$unitheight = $vitals_obj->getUnitName($rowvitaldata['height_unit']);
$vitalHTML = '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:0%">
								<tr>
										<td width="50%">
												<table>
														<tr>
																<td width="40%">
																		<span style="font: 11px Arial;">Blood Pressure </span>
																</td>
																<td width="60%">
																		<input class="segInput" readonly id="vital_bp_sys" name="vital_bp_sys" type="text" size="1" style="padding-left:0px;font:bold 11px Arial;" value="' . $rowvitaldata['systole'] . '" onkeydown="return key_check(event, this.value)" onblur="convertNumberValue(this, this.value);"/><span>/<span><input class="segInput" readonly id="vital_bp_dias" name="vital_bp_dias" type="text" size="1" style="padding-left:0px;font:bold 11px Arial;" value="' . $rowvitaldata['diastole'] . '" onkeydown="return key_check(event, this.value)" onblur="convertNumberValue(this, this.value);"/><span style="font: 11px Arial;"> ' . $unitbp['unit_name'] . ' </span>
																		<input class="segInput" id="vital_no" name="vital_no" type="hidden" value="' . $rowvitaldata['vitalsign_no'] . '" />
																</td>
														</tr>
														<tr>
																<td width="45%">
																		<span style="font: 11px Arial;">Temperature (T)</span>
																</td>
																<td width="55%">
																		<input class="segInput" readonly id="vital_t" name="vital_t" type="text" size="5" style="padding-left:4px;font:bold 11px Arial" value="' . $rowvitaldata['temp'] . '" onkeydown="return key_check(event, this.value)" onblur="convertNumberValue(this, this.value);"/><span style="font: 11px Arial;">  ' . $unittemp['unit_name'] . '</span>
																</td>
														</tr>
														<tr>
																<td width="45%">
																		<span style="font: 11px Arial;">Weight (W)</span>
																</td>
																<td width="55%">
																		<input class="segInput" readonly id="vital_w" name="vital_w" type="text" size="5" style="padding-left:4px;font:bold 11px Arial" value="' . $rowvitaldata['weight'] . '" onkeydown="return key_check(event, this.value)" onblur="convertNumberValue(this, this.value);"/><span style="font: 11px Arial;"> ' . $unitweight['unit_name'] . '</span>
																</td>
														</tr>
												</table>
										</td>
										<td width="5%"></td>
										<td width="45%" align="right" valign="top">
												<table>
														<tr>
																<td width="45%">
																		<span style="font: 11px Arial;">Resp. Rate (RR)</span>
																</td>
																<td width="55%">
																		<input class="segInput" readonly id="vital_rr" name="vital_rr" type="text" size="5" style="padding-left:4px;font:bold 11px Arial" value="' . $rowvitaldata['resp_rate'] . '" onkeydown="return key_check(event, this.value)" onblur="convertNumberValue(this, this.value);"/><span style="font: 11px Arial;"> ' . $unitrr['unit_name'] . '</span>
																</td>
														</tr>
														<tr>
																<td width="45%">
																		<span style="font: 11px Arial;">Pulse Rate (PR)</span>
																</td>
																<td width="55%">
																		<input class="segInput" readonly id="vital_pr" name="vital_pr" type="text" size="5" style="padding-left:4px;font:bold 11px Arial" value="' . $rowvitaldata['pulse_rate'] . '" onkeydown="return key_check(event, this.value)" onblur="convertNumberValue(this, this.value);"/><span style="font: 11px Arial;"> ' . $unitpr['unit_name'] . '</span>
																</td>
														</tr>
                                                        <tr>
                                                                <td width="45%">
                                                                        <span style="font: 11px Arial;">Height (H)</span>
                                                                </td>
                                                                <td width="55%">
                                                                        <input class="segInput" readonly id="vital_h" name="vital_h" type="text" size="5" style="padding-left:4px;font:bold 11px Arial" value="' . $rowvitaldata['height'] . '" onkeydown="return key_check(event, this.value)" onblur="convertNumberValue(this, this.value);"/><span style="font: 11px Arial;"> ' . $unitheight['unit_name'] . '</span>
                                                                </td>
                                                        </tr>
												</table>
										</td>
								</tr>
						</table>';

$smarty->assign('LDVitalSigns', '<span>Vital Signs</span>');
$smarty->assign('vital_signs', '<img src="' . $root_path . 'images/btn_add.gif" align="absmiddle" style="cursor:pointer" onclick="openVital();">');
$smarty->assign('vital_signs', $vitalHTML);

$HTTP_SESSION_VARS['vital_no'] = $rowvitaldata['vitalsign_no'];

$smarty->assign('LDAddress', $LDAddress);
#	$segAddress=$street_name.', '.$brgy_name.', '.$mun_name.' '.$zipcode.' '.$prov_name;   # burn added: March 12, 2007
//--------------added by pet--------------may 3, 2008--------------------------------------
if (($street_name) && ($brgy_name))
    $street_name_comma = ", ";
else
    $street_name_comma = "";

if (($brgy_name) && ($mun_name))
    $brgy_name_comma = ", ";
else
    $brgy_name_comma = "";

$segAddress = $street_name . $street_name_comma . $brgy_name . $brgy_name_comma . $mun_name . ' ' . $zipcode . ' ' . $prov_name;
//--------------until here only----------------------pet--------------------fgdpm----------
$smarty->assign('segAddress', $segAddress);   # burn added: March 12, 2007
$smarty->assign('LDAdmitClass', $LDAdmitClass);

# Suggested by Dr. Sarat Nayak to emphasize the OUTPATIENT encounter type

if (isset($$encounter_class['LD_var']) && !empty($$encounter_class['LD_var'])) {
    $eclass = $$encounter_class['LD_var'];
} else {
    $eclass = $encounter_class['name'];
}

if (($encounter_type == 1) || ($encounter_type == 3) || ($encounter_type == 4)) {

    if ($encounter_type == 3) {
        $eclass = $LDStationary2;
    } elseif ($encounter_type == 4) {
        $eclass = $LDAmbulant2;
    } else {
        $eclass = $LDStationary;
    }

    if ($encounter_type == 1) {
        $fcolor = 'red';
    } else {
        $fcolor = 'green';
    }
#echo "status = ".$encounter_status;
    $eclass = '<b>' . strtoupper($eclass) . '</b>';

} elseif ($encounter_type == 2) {
    $fcolor = 'blue';
    $eclass = '<b>' . strtoupper($LDAmbulant) . '</b>';
}

if ($encounter_status == 'direct_admission') {
    $fcolor = 'green';
    $eclass = '<b>' . strtoupper($LDInpatientDirectAdmission) . '</b>';
}

$smarty->assign('sAdmitClassInput', "<font color=$fcolor>$eclass</font>");
#echo "off = ".$official_receipt_nr;
# Official receipt number, available/required ONLY when generating OPD encounter
if (($encounter_type == 2) || ($encounter_type == 4)) {
    $smarty->assign('segORNumber', "OR Number");
    #$smarty->assign('sORNumber',ucwords(strtolower(trim($official_receipt_nr))));
    $smarty->assign('sORNumber', $official_receipt_nr);
}

#information info
if ($encounter_class_nr != 2) {  # not OPD
    $smarty->assign('LDInformant', $LDInformant);
    $smarty->assign('informant_name', ucwords(strtolower(trim($informant_name))));

    $smarty->assign('LDInfoAdd', $LDInfoAdd);
    $smarty->assign('info_address', ucwords(strtolower(trim($info_address))));

    $smarty->assign('LDInfoRelation', $LDInfoRelation);
    $smarty->assign('relation_informant', ucwords(strtolower(trim($relation_informant))));

}

#added by VAN 06-20-08
$smarty->assign('LDConfidential', 'Confidential');
if ($is_confidential)
    $confidential_statement = 'YES';
else
    $confidential_statement = 'NO';

$smarty->assign('sConfidential', '<input type="hidden" name="is_confidential" id="is_confidential" value="' . $is_confidential . '">' . $confidential_statement);
#----------------------

#added by VAN 04-29-08
#if ($dept_belong['id']=="ER"){
if (($allow_er_user) && ($ptype == 'er')) {
    #---added by VAN 06-13-08
    $smarty->assign('LDTriageCategory', 'Triage Category');
    $category_name = $encounter_obj->getTriageCategoryInfo($category);
    $smarty->assign('sCategory', $category_name['category']);
}

#---------------------
//modified by Francis 05-06-2013
if (($allow_er_user) && (($ptype == 'er') || ($ptype == 'ipd'))) {
    #echo "is_medico = ".$is_medico;
    #$is_medico = 1;
    $smarty->assign('sOnLoadJs', "onLoad=\"preSet($is_medico);\"");
    $smarty->assign('LDMedico', $LDMedico);

    if ($is_medico)
        $medico_statement = 'YES';
    else
        $medico_statement = 'NO';

    $smarty->assign('Medico', '<input type="hidden" name="is_medico_case" id="is_medico_case" value="' . $is_medico . '">' . $medico_statement);
    #$smarty->assign('Medico',$medico_statement);

    $smarty->assign('LDMedicoCases', 'Medico Legal Cases: ');

    $medico_cases = $encounter_obj->getMedicoCases();

    if (is_object($medico_cases)) {
        $sTemp = '';
        $count = 0;
        while ($result = $medico_cases->FetchRow()) {
            $sTemp = $sTemp . '<input name="medico' . $result['code'] . '" id="medico' . $result['code'] . '" type="checkbox" disabled="disabled" value="' . $result['code'] . '" ';
            #if($patient_enc_res['result_code']==$result['code']) $sTemp = $sTemp.'checked';
            $medico = $encounter_obj->getEncounterByMedicoCases($encounter_nr, $pid, $result['code']);
            #echo $encounter_obj->sql;
            #if($result2['medico_cases']==$result['code']) $sTemp = $sTemp.'checked';
            if ($medico['medico_cases'] == $result['code']) $sTemp = $sTemp . 'checked';

            if ($medico['description'])
                $desc = $medico['description'];

            $sTemp = $sTemp . '>';
            $sTemp = $sTemp . $result['medico_cases'] . "<br>";

            if ($count <= 5) {
                $rowMedicoA = $sTemp;
                if ($count == 5) {
                    $sTemp = '';
                }
            } else {
                $rowMedicoB = $sTemp;
            }
            $count++;

        }
    }

    $smarty->assign('sdescription', '<textarea readonly id="description" name="description" cols="25" rows="2">' . trim(stripslashes($desc)) . '</textarea>');
    #$smarty->assign('sdescription','<textarea readonly id="description" name="description" cols="25" rows="2">'.trim($desc).'</textarea>');
    $smarty->assign('rowMedicoA', $rowMedicoA);
    $smarty->assign('rowMedicoB', $rowMedicoB);

    #added by VAN 06-12-08
    $smarty->assign('LDPOI', 'Place of Incident (POI)');
    $smarty->assign('sPOI', $POI);
    $smarty->assign('LDTOI', 'Time of Incident (TOI)');

    if ($TOI != '00:00:00')
        $TOI_val = date("h:i A", strtotime($TOI));
    else
        $TOI_val = "Not Indicated";

    $smarty->assign('sTOI', $TOI_val);
    $smarty->assign('LDDOI', 'Date of Incident (DOI)');

    if ($DOI != '0000-00-00')
        $DOI_val = date("F d, Y", strtotime($DOI));
    else
        $DOI_val = "Not Indicated";

    $smarty->assign('sDOI', $DOI_val);
    #-------------------


    $smarty->assign('LDDOA', "Is Dead on Arrival?");
    if ($is_DOA == 1) {
        $DOA = "YES&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Reason : </b>" . $is_DOA_reason;
    } else {
        $DOA = "NO";
    }
    $smarty->assign('sDOA', $DOA);
}

# burn added : May 17, 2007
$consulting_dr_name = '';
#commented by VAN 03-08-2013
#if ($current_att_dr_nr)
$dr_nr = $current_att_dr_nr;
#else
#	$dr_nr = $consulting_dr_nr;

//Modified by Jarel 09102015
$smarty->assign('sAttDrItems', "
				<tr>
					<td colspan=\"10\">Doctor list is currently empty...</td>
				</tr>");

if ($encounter_obj->hasRecordEncounterDRAdd($encounter_nr)) {
    $items = $encounter_obj->getEncounterDRAddList($encounter_nr, 'con');
    if ($items) {
        foreach ($items AS $row) {
            $src .= '
				<tr class="wardlistrow' . $alt . '" id="row' . $row['hcare_id'] . '">
					<td class="centerAlign"><img src="../../images/insurance.gif" border="0"/></td>
					<td width="*">' . $row['name'] . '</td>
					<td></td>
				</tr>
			';
        }//End Foreach
    }//End If

    $smarty->assign('sAttDrItems', $src);
} else {
    if ($dr_nr) {
        if ($doc_info = $pers_obj->getPersonellInfo($dr_nr)) {

            $middleInitial = "";
            if (trim($doc_info['name_middle']) != "") {
                $thisMI = split(" ", $doc_info['name_middle']);
                foreach ($thisMI as $value) {
                    if (!trim($value) == "")
                        $middleInitial .= $value[0];
                }
                if (trim($middleInitial) != "")
                    $middleInitial = " " . $middleInitial . ".";
            }
            # the lastest attending/consultin physician
            $consulting_dr_name = "Dr. " . $doc_info['name_first'] . " " . $doc_info['name_2'] . $middleInitial . " " . $doc_info['name_last'];
        }
    } else {
        $consulting_dr_name = "Doctor Att list is currently empty...";
    }

    $src = "<tr>
				<td colspan=\"10\">" . $consulting_dr_name . "</td>
			</tr>";

    $smarty->assign('sAttDrItems', $src);
}
#End Modified Jarel

#for IPD with ward
#if (($dept_belong['id']=="Admission")&&(($encounter_type==3)||($encounter_type==4))){
if ((($allow_ipd_user) && ($ptype == 'ipd')) && (($encounter_type == 3) || ($encounter_type == 4))) {
    $smarty->assign('LDWard', $LDWard);
    #echo "ward = ".$current_ward_name;
    if ($current_ward_nr == 0) {
        $smarty->assign('sWardInput', 'No Ward');
    } else {
        $smarty->assign('sWardInput', '<a href="' . $root_path . 'modules/nursing/' . strtr('nursing-station-pass.php' . URL_APPEND . '&rt=pflege&edit=1&station=' . $current_ward_name . '&location_id=' . $current_ward_name . '&ward_nr=' . $current_ward_nr, ' ', ' ') . '">' . $current_ward_name . '</a>');
    }

    #------------added by VAN 01-31-08
    $smarty->assign('LDRoom', 'Room');
    $smarty->assign('LDBed', 'Bed');

    if ($current_room_nr == 0) {
        #$current_room = 'No Room';
        if ($area)
            $current_room = $area;
        else
            $current_room = 'No Room';
    } else
        $current_room = $current_room_nr;

    $smarty->assign('sLDRoom', $current_room);

    #echo "enc, ward = ".$encounter_nr." - ".$current_ward_nr;
    $patientloc = $encounter_obj->getPatientLocation($encounter_nr, $current_ward_nr);
    #echo "bed = ".$patientloc['location_nr'];

    if ($patientloc['location_nr']) {
        $bednr = $patientloc['location_nr'];
        $bed = $patientloc['location_nr'];
    } else {
        $bednr = 0;
        $bed = 'No Bed';
    }

    $smarty->assign('sLDBed', $bed);
    #--------------------------------------

    $smarty->assign('LDDoctor', $LDDoctor3);
    //$smarty->assign('doctor_name',$consulting_dr_name);

} elseif ($encounter_class_nr == 1) {
    $smarty->assign('LDDoctor', $LDDoctor3);
    //$smarty->assign('doctor_name',$consulting_dr_name);

} elseif ($encounter_class_nr == 2) {
    $smarty->assign('LDDoctor', $LDDoctor1);
    //$smarty->assign('doctor_name',$consulting_dr_name);
}

#$smarty->assign('doctor_name',"Dr. ".$consulting_dr);

$smarty->assign('LDDepartment', "$LDClinic/$LDDepartment");
$smarty->assign('sDeptInput', '<a href="' . $root_path . 'modules/ambulatory/' . strtr('amb_clinic_patients_pass.php' . URL_APPEND . '&rt=pflege&edit=1&dept=' . $$current_dept_LDvar . '&location_id=' . $$current_dept_LDvar . '&dept_nr=' . $current_dept_nr, ' ', ' ') . '">' . $current_dept_name . '</a>');


if ($encounter_type == 1) {
    $smarty->assign('LDDepartment', "Consulting $LDDepartment");
    $smarty->assign('LDDoctor', "<font color='red'>Consulting Physician</font>");
    #$smarty->assign('doctor_name',$consulting_dr_name);
} elseif (($encounter_type == 2) || ($encounter_type == 5)) {
    $smarty->assign('LDDepartment', "Consulting $LDClinic");
    $smarty->assign('LDDoctor', "<font color='red'>Consulting Physician</font>");
#	$smarty->assign('doctor_name',$consulting_dr_name);
} elseif (($encounter_type == 3) || ($encounter_type == 4) || ($encounter_type == 6)) {
    $smarty->assign('LDDepartment', "Attending $LDDepartment");
    $smarty->assign('LDDoctor', "<font color='red'>Attending Physician</font>");
    #$smarty->assign('doctor_name',$consulting_dr_name);

    #added by VAN 04-18-2010
    /*$smarty->assign('LDConsultant',"Consulting Doctors");

    $smarty->assign('sDoctorItems',"
                <tr>
                    <td colspan=\"4\">Consulting doctor's list is currently empty...</td>
                </tr>");

    $result = $encounter_obj->getConsultingDr($encounter_nr);
    $totaldr = $encounter_obj->count;
    $smarty->assign('scounter',$totaldr);
    #echo "s = ".$totaldr;
    #echo "sql = ".$encounter_obj->sql;
    if (is_object($result)){
        $rows=array();
        $src = '';
        while ($row=$result->FetchRow()) {
            $rows[] = $row;
        }
        foreach ($rows as $i=>$row) {
            if ($row) {
                $counts++;
                $alt = ($counts%2)+1;

                $src .= '
                    <tr class="wardlistrow'.$alt.'" id="row'.$row['consulting_dr'].'">
                        <input type="hidden" name="drlist[]" id="rowDr'.$row['consulting_dr'].'" value="'.$row['consulting_dr'].'" />
                        <input type="hidden" name="deptlist[]" id="rowDept'.$row['consulting_dr'].'" value="'.$row['consulting_dept'].'" />
                        <td class="centerAlign"><img src="../../images/claim_ok.gif" border="0"/>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td width="*" id="con_dr'.$row['consulting_dr'].'">'.$row['consulting_dr_name'].'</td>
                        <td width="30%" align="left" id="con_dept'.$row['consulting_dr'].'">'.$row['consulting_dept_name'].'</td>
                    </tr>
                ';
            }
        }
        if ($src) $smarty->assign('sDoctorItems',$src);
    }
    #------ end VAN 04-18-2010 ----------
    */
}
//$smarty->assign('doctor_name',$consulting_dr_name);

#-------------added 03-14-07-------------
if (($encounter_class_nr == 1) && (!$seg_direct_admission)) {
    $smarty->assign('segShowIfFromER', "true");
} else {
    $smarty->assign('segShowIfFromER', "");
}

if ((($allow_opd_user) && ($ptype == 'opd')) || (($allow_er_user) && ($ptype == 'er'))) {
    $smarty->assign('segComplaint', TRUE);
    $smarty->assign('segChiefComplaint', "Chief Complaint");
    $smarty->assign('chief_complaint', trim($chief_complaint));   # burn added: May 16, 2007
}

#Added by Jarel 03-07-13
$dr_nr = $consulting_dr_nr;

//Modified by Jarel 09102015
$smarty->assign('sConDrItems', "
				<tr>
					<td colspan=\"10\">Doctor list is currently empty...</td>
				</tr>");

$src = '';
if ($encounter_obj->hasRecordEncounterDRAdd($encounter_nr)) {
    $items = $encounter_obj->getEncounterDRAddList($encounter_nr, 'adm');
    if ($items) {
        foreach ($items AS $row) {
            $src .= '
				<tr class="wardlistrow' . $alt . '" id="row' . $row['hcare_id'] . '">
					<td class="centerAlign"><img src="../../images/insurance.gif" border="0"/></td>
					<td width="*">' . $row['name'] . '</td>
					<td></td>
				</tr>
			';
        }//End Foreach
    }//End If
} else {
    if ($dr_nr) {
        if ($doc_info = $pers_obj->getPersonellInfo($dr_nr)) {

            $middleInitial = "";
            if (trim($doc_info['name_middle']) != "") {
                $thisMI = split(" ", $doc_info['name_middle']);
                foreach ($thisMI as $value) {
                    if (!trim($value) == "")
                        $middleInitial .= $value[0];
                }
                if (trim($middleInitial) != "")
                    $middleInitial = " " . $middleInitial . ".";
            }
            $consulting_dr_name = "Dr. " . $doc_info['name_first'] . " " . $doc_info['name_2'] . $middleInitial . " " . $doc_info['name_last'];
        }
    } else {
        $consulting_dr_name = "Doctor Att list is currently empty...";
    }

    $src = "<tr>
				<td colspan=\"10\">" . $consulting_dr_name . "</td>
			</tr>";
}


$dept_nr = $patient_enc['consulting_dept_nr'];
$patient_consulting_dept = $dept_obj->FormalName($dept_nr);

if (($allow_ipd_user) && ($ptype == 'ipd')) {
    $admit_label = "Admitting ";
    $smarty->assign('segERDiagnosis', $admit_label . $LDDiagnosis);
    $smarty->assign('er_opd_diagnosis', trim($er_opd_diagnosis));
    $smarty->assign('segEROPDDr', "<font color='red'>Admitting Physician</font>");
    //$smarty->assign('sERDrInput',$consulting_dr_name);
    $smarty->assign('segEROPDDepartment', "<font color='red'>Admitting $LDDepartment</font>");
    $smarty->assign('sERDeptInput', $patient_consulting_dept);
    $smarty->assign('sConDrItems', $src);
}

if (($allow_er_user) && ($ptype == 'er')) {
    $smarty->assign('segERDiagnosis', "Admitting Diagnosis");
    $smarty->assign('er_opd_diagnosis', trim($er_opd_diagnosis));
}

#if ($dept_belong['id']!="OPD-Triage"){
#if ((!$allow_opd_user)&&($ptype!='opd')){
if ($ptype) {
#if ((($dept_belong['id']=="Admission")||($dept_belong['id']=="ER"))&&((($patient_enc['encounter_type']==1)||($patient_enc['encounter_type']==3))&&($patient_enc['encounter_status']!='direct_admission'))){

    #added by VAN 04-16-08
    #referral

    $smarty->assign('LDDiagnosis', $LDDiagnosis);
    $smarty->assign('referrer_diagnosis', ucwords(strtolower(trim($referrer_diagnosis))));

    $smarty->assign('LDRecBy', $LDRecBy);
    $dr_name = $pers_obj->get_Person_name($referrer_dr);
    $dr_fname = ucwords(strtolower(trim($dr_name['name_first'])));
    $dr_fname2 = ucwords(strtolower(trim($dr_name['name_2'])));
    $dr_lname = ucwords(strtolower(trim($dr_name['name_last'])));
    $ref_dr_name = "Dr. " . $dr_fname . " " . $dr_fname2 . " " . $dr_lname;

    if (is_numeric($referrer_dr)) {
        $smarty->assign('referrer_dr_name', ucwords(strtolower(trim($ref_dr_name))));
    } else {
        $smarty->assign('referrer_dr_name', ucwords(strtolower(trim($referrer_dr))));
    }

    $smarty->assign('LDRecDept', $LDRecDept);

    if (is_numeric($referrer_dept)) {
        $smarty->assign('referrer_dept_name', trim($dept_obj->FormalName($referrer_dept)));
    } else {
        $smarty->assign('referrer_dept_name', ucwords(strtolower(trim($referrer_dept))));
    }

    if ($encounter_class_nr != 2) {  # not OPD
        $smarty->assign('LDRecIns', $LDRecIns);
        $smarty->assign('referrer_institution', ucwords(strtolower(trim($referrer_institution))));
    }

    $smarty->assign('LDSpecials', $LDSpecials);
    $smarty->assign('referrer_notes', ucwords(strtolower(trim($referrer_notes))));

# if($dept_belong['id']!="ER"){
#if((!$allow_er_user)&&($ptype!='er')){
    if (($allow_er_user) || ($allow_ipd_user) || ($allow_phs_user)) {
        $smarty->assign('LDBillType', $LDBillType);

        if (isset($$insurance_class['LD_var']) && !empty($$insurance_class['LD_var'])) $smarty->assign('sBillTypeInput', $$insurance_class['LD_var']);
        else $smarty->assign('sBillTypeInput', $insurance_class['name']);
        #----commented by VAN 09-03-07-----------
        /*
        $smarty->assign('LDInsuranceNr',$LDInsuranceNr);
        if(isset($insurance_nr)&&$insurance_nr) $smarty->assign('insurance_nr',$insurance_nr);

        $smarty->assign('LDInsuranceCo',$LDInsuranceCo);
        $smarty->assign('insurance_firm_name',$insurance_firm_name);
        */

        #----------added by VAN 09-03-07-------------

        if ($error_ins_nr) $smarty->assign('LDInsuranceNr', "<font color=red>$LDInsuranceList</font>");
        else  $smarty->assign('LDInsuranceNr', $LDInsuranceList);

        #$smarty->assign('sOrderItems',"<tr>
        #									<td colspan=\"10\">Insurance list is currently empty...</td>
        #				</tr>");


        $smarty->assign('sOrderItems', "
				<tr>
					<td colspan=\"10\">Insurance list is currently empty...</td>
				</tr>");

        # Note: make a class function for this part later
        $result = $encounter_obj->getPersonInsuranceItems($encounter_nr);
        #echo "sql = ".$encounter_obj->sql;
        $rows = array();
        while ($row = $result->FetchRow()) {
            $rows[] = $row;
        }
        $src = '';
        foreach ($rows as $i => $row) {
            if ($row) {
                $count++;
                $alt = ($count % 2) + 1;

                $sql2 = "SELECT ci.* FROM care_person_insurance AS ci
						WHERE ci.pid ='" . $pid . "'
						AND ci.hcare_id = '" . $row['hcare_id'] . "'";
                $res = $db->Execute($sql2);

                $row2 = $res->RecordCount();

                if ($row2 != 0) {
                    while ($rsObj = $res->FetchRow()) {
                        $ins_nr = $rsObj["insurance_nr"];
                        if ($rsObj["is_principal"]) {
                            $principal = "YES";
                        } else {
                            $principal = "NO";
                        }
                    }
                }

                $src .= '
				<tr class="wardlistrow' . $alt . '" id="row' . $row['hcare_id'] . '">
					<input type="hidden" name="items[]" id="rowID' . $row['hcare_id'] . '" value="' . $row['hcare_id'] . '" />
					<input type="hidden" name="nr[]" id="rowNr' . $row['hcare_id'] . '" value="' . $ins_nr . '" />
					<input type="hidden" name="is_principal[]" id="rowis_principal' . $row['hcare_id'] . '" value="' . $rsObj["is_principal"] . '" />
					<td class="centerAlign"><img src="../../images/insurance.gif" border="0"/>&nbsp;' . $count . '</td>
					<td width="*" id="name' . $row['hcare_id'] . '">' . $row['firm_id'] . '</td>
					<td width="20%" align="right" id="inspin' . $row['hcare_id'] . '">' . $ins_nr . '</td>
					<td width="18%" class="centerAlign" id="insprincipal' . $row['hcare_id'] . '">' . $principal . '</td>
					<td></td>
				</tr>
			';
            }
        }
        if ($src) $smarty->assign('sOrderItems', $src);

        #------------------------------------------

    }    # ---------end if ER
} #-----end if OPD

$smarty->assign('LDFrom', $LDFrom);
$smarty->assign('LDTo', $LDTo);


if (!$GLOBAL_CONFIG['patient_service_care_hide'] && $sc_care_class_nr) {
    $smarty->assign('LDCareServiceClass', $LDCareServiceClass);

    while ($buffer = $care_service->FetchRow()) {
        if ($sc_care_class_nr == $buffer['class_nr']) {
            if (empty($$buffer['LD_var'])) $smarty->assign('sCareServiceInput', $buffer['name']);
            else $smarty->assign('sCareServiceInput', $$buffer['LD_var']);
            break;
        }
    }

    if ($sc_care_start && $sc_care_start != DBF_NODATE) {
        $smarty->assign('sCSFromInput', ' [ ' . @formatDate2Local($sc_care_start, $date_format) . ' ] ');
        $smarty->assign('sCSToInput', ' [ ' . @formatDate2Local($sc_care_end, $date_format) . ' ]');
    }
}


if (!$GLOBAL_CONFIG['patient_service_room_hide'] && $sc_room_class_nr) {
    $smarty->assign('LDRoomServiceClass', $LDRoomServiceClass);

    while ($buffer = $room_service->FetchRow()) {
        if ($sc_room_class_nr == $buffer['class_nr']) {
            if (empty($$buffer['LD_var'])) $smarty->assign('sCareRoomInput', $buffer['name']);
            else $smarty->assign('sCareRoomInput', $$buffer['LD_var']);
            break;
        }
    }
    if ($sc_room_start && $sc_room_start != DBF_NODATE) {
        $smarty->assign('sRSFromInput', ' [ ' . @formatDate2Local($sc_room_start, $date_format) . ' ] ');
        $smarty->assign('sRSToInput', ' [ ' . @formatDate2Local($sc_room_end, $date_format) . ' ]');
    }
}

if (!$GLOBAL_CONFIG['patient_service_att_dr_hide'] && $sc_att_dr_class_nr) {
    $smarty->assign('LDAttDrServiceClass', $LDAttDrServiceClass);

    while ($buffer = $att_dr_service->FetchRow()) {
        if ($sc_att_dr_class_nr == $buffer['class_nr']) {
            if (empty($$buffer['LD_var'])) $smarty->assign('sCareDrInput', $buffer['name']);
            else $smarty->assign('sCareDrInput', $$buffer['LD_var']);
            break;
        }
    }
    if ($sc_att_dr_start && $sc_att_dr_start != DBF_NODATE) {
        $smarty->assign('sDSFromInput', ' [ ' . @formatDate2Local($sc_att_dr_start, $date_format) . ' ] ');
        $smarty->assign('sDSToInput', ' [ ' . @formatDate2Local($sc_att_dr_end, $date_format) . ' ]');
    }
}

#--------------------added 03-08-07----------------------
#if (($dept_belong['id']=="Admission")&&(($patient_enc['encounter_type']==3)||($patient_enc['encounter_type']==4))){
#if (($dept_belong['id']=="Admission")&&(($patient_enc['encounter_type']==3)&&($patient_enc['encounter_status']!='direct_admission'))){
#if ((($dept_belong['id']=="Admission")||($dept_belong['id']=="ER"))&&((($patient_enc['encounter_type']==1)||($patient_enc['encounter_type']==3))&&($patient_enc['encounter_status']!='direct_admission'))){
#if (($dept_belong['id']=="Admission")&&(($patient_enc['encounter_type']==3)||($patient_enc['encounter_type']==4))){
if (($allow_ipd_user) && (($patient_enc['encounter_type'] == 3) || ($patient_enc['encounter_type'] == 4))) {
    if ($patient_enc['encounter_status'] == 'direct_admission') {
        $LDCondition = 'Condition at Other Institution';
        $LDResults = 'Results from Other Institution';
        $LDDisposition = 'Disposition from Other Institution';
    }

    $smarty->assign('LDCondition', $LDCondition);
    $smarty->assign('sCondition', $encounter_condition['cond_desc']);
    $smarty->assign('LDResults', $LDResults);
    #echo "result = ".$encounter_result['result_desc'];
    $smarty->assign('sResults', $encounter_result['result_desc']);
    $smarty->assign('LDDisposition', $LDDisposition);
    #echo "result = ".$encounter_disposition['disp_desc'];

    /*added by Genz*/
    if ($patient_enc_disp['disp_code'] == 3) {
        $hosp = '(' . $patient_enc_disp['hosp_name'] . ')';
    }

    $smarty->assign('sDisposition', $encounter_disposition['disp_desc'] . ' ' . $hosp);
}

#-------------------------------------------------------

#added by VAN 10-12-2011
$smarty->assign('LDSmokers', "History of Smoking");

if ($smoker_history == 'na')
    $smoker_history = "N/A";
$smarty->assign('sSmokersInput', mb_strtoupper($smoker_history));
$smarty->assign('LDDrinker', "Alcohol Drinker");
if ($drinker_history == 'na')
    $drinker_history = "N/A";
$smarty->assign('sDrinkerInput', mb_strtoupper($drinker_history));
#--------------

$LDAdmitBy = 'Encoded By';

$smarty->assign('LDAdmitBy', $LDAdmitBy);
#if (empty($encoder)) $encoder = $patient_enc['modify_id'];
if (!empty($patient_enc['create_id']))
    $encoder = $patient_enc['create_id'];
else
    $encoder = $patient_enc['modify_id'];
$smarty->assign('encoder', $encoder);

$smarty->assign('LDDeptBelong', $LDDepartment);
$smarty->assign('sDeptBelong', $dept_belong['name_formal']);

# Buffer the options block
ob_start();
$HTTP_SESSION_VARS['dept_id'] = $dept_belong['id'];

#added by VAN 07-06-09
$row_ipd = $encounter_obj->getLatestEncounter($pid);
$enctype = $row_ipd['encounter_type'];
$isdischarged = $row_ipd['is_discharged'];
#echo "w = ".$encounter_obj->sql;
#----------------------

require('./gui_bridge/default/gui_patient_encounter_showdata_options.php');
$sTemp = ob_get_contents();

ob_end_clean();

$smarty->assign('sAdmitOptions', $sTemp);

$sTemp = '';

if (!$is_discharged) {

    # Buffer the control buttons
    ob_start();

    include('./include/bottom_controls_admission.inc.php');
    $sTemp = ob_get_contents();

    ob_end_clean();

    $smarty->assign('sAdmitBottomControls', $sTemp);
}

#edited by VAN 01-25-08
#$breakfile = 'patient_register_show.php'.URL_APPEND.'&pid='.$pid;  #-----added by vanessa--------

if ($fromnurse)
    $breakfile = 'javascript:window.close()';
else
    $breakfile = 'patient_register_show.php' . URL_APPEND . '&pid=' . $pid;

#$smarty->assign('pbBottomClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');


#if ($dept_belong['id']!="Admission"){
if ((!$allow_ipd_user) && ($ptype != 'ipd')) {
    #if ($dept_belong['id']=="ER"){
    if (($allow_er_user) && ($ptype == 'er')) {
        $smarty->assign('sAdmitLink', '<img ' . createComIcon($root_path, 'varrow.gif', '0') . '> <a href="aufnahme_start.php' . URL_APPEND . '&mode=?">' . $LDIPDWantEntry . '</a>');
        #}elseif ($dept_belong['id']=="OPD-Triage"){
    } elseif (($allow_opd_user) && ($ptype == 'opd')) {
        $smarty->assign('sAdmitLink', '<img ' . createComIcon($root_path, 'varrow.gif', '0') . '> <a href="aufnahme_start.php' . URL_APPEND . '&mode=?">' . $LDOPDWantEntry . '</a>');
    }
}

#edited by VAN 01-26-08
if (($fromnurse != 1) && (!$allow_only_clinic)) {
    $smarty->assign('sSearchLink', '<img ' . createComIcon($root_path, 'varrow.gif', '0') . '> <a href="aufnahme_daten_such.php' . URL_APPEND . '">' . $LDAdmWantSearch . '</a>');
    $smarty->assign('sArchiveLink', '<img ' . createComIcon($root_path, 'varrow.gif', '0') . '> <a href="aufnahme_list.php' . URL_APPEND . '&newdata=1">' . $LDAdmWantArchive . '</a>');
}

$smarty->assign('sMainBlockIncludeFile', 'registration_admission/admit_show.tpl');

$smarty->display('common/mainframe.tpl');

?>
