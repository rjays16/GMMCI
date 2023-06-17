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
require($root_path.'modules/social_service/ajax/social_client_common_ajx.php');
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

require_once($root_path.'include/care_api_classes/class_social_service.php');
$objSS = new SocialService;

require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];

$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$date_format2 = '%m/%d/%Y';

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');


 # Onload Javascript code
 $onLoadJs='onLoad="if (window.focus) window.focus();getDependent();"';
 $smarty->assign('sOnLoadJs',$onLoadJs);
 
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

# Buffer extra javascript code
ob_start();

?>

<script language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<script type="text/javascript" src="<?=$root_path?>js/masking/html-form-input-mask.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>modules/social_service/css/social_service.css" type="text/css" />
<link rel="stylesheet" href="<?= $root_path ?>css/seg/wirecake.css" type="text/css" /> 

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script> 

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.table.addrow.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>

<script type="text/javascript" src="<?= $root_path ?>js/dateformat.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/datefuncs.js" ></script> 

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<!-- YUI Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">

<!--added by VAN 05-08-08-->
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

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
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.numeric.js?t=<?= time() ?>"></script>
<script type="text/javascript" src="js/social_service_intake.js?t=<?= time() ?>"></script>

<script language="javascript" type="text/javascript"> 
	YAHOO.namespace("example.container");
	YAHOO.util.Event.onDOMReady(init);
    
	function DOM_init() {
		//xajax_PopulateSSC('<?=$HTTP_SESSION_VARS['sess_en']?>','<?=$HTTP_SESSION_VARS['sess_pid']?>','<?=$withrec?>', 'lcr');
        //rlst.reload();
	}
    
    var J = jQuery.noConflict();
    
    J().ready(function() {   
        //J('#tab_form').tabs();
        J( "#tab_form" ).tabs();                         
        //J('#tab_form').tabs({active:0});
        J('#age_dep').numeric();
        J('#mincome_dep').numeric();
        J("#nr_dep").numeric();
        J("#contact_number").numeric();       
        J("#m_income2").numeric();
        J("#living_amount").numeric();
        J("#other_income").numeric();
        J("#light_amount").numeric();
        J("#water_amount").numeric();
        J("#food_amount").numeric();
        J("#educ_amount").numeric();
        J("#clothing_amount").numeric();
        J("#trans_amount").numeric();
        J("#light_amount").numeric();
        J("#fuel_amount").numeric();
        J("#househelp_amount").numeric();
        J("#medical_amount").numeric();
        J("#plan_amount").numeric();
        J("#others_amount").numeric();
        J("#referral_number").numeric();
    });
    
    jQuery(function($J){
        $J("#interview_date").mask("99/99/9999");
        $J( "#DeMe_cancel" ).button({text: true,icons: {primary: "ui-icon-cancel"}});
        $J( "#DeMe_submit" ).button({text: true,icons: {primary: "ui-icon-disk"}});
        $J( "#DeMe_print" ).button({text: true,icons: {primary: "ui-icon-print"}});
        $J( "#Assess_print" ).button({text: true,icons: {primary: "ui-icon-print"}});
        $J( "#social_submit" ).button({text: true,icons: {primary: "ui-icon-disk"}});
        $J( "#problem_submit" ).button({text: true,icons: {primary: "ui-icon-disk"}});
        $J( "#findings_submit" ).button({text: true,icons: {primary: "ui-icon-disk"}}); 
        $J( "#case_submit" ).button({text: true,icons: {primary: "ui-icon-disk"}});
    });
    
</script>

<input type="hidden" name="root_path" id="root_path" value="<?=$root_path?>">
<input type="hidden" name="sid" id="sid" value="<?=URL_APPEND?>">
<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

global $db;

$encounter_nr = $_GET['encounter_nr'];
$pid = $_GET['pid'];
$parent_enc = $_GET['parent_enc'];

if(empty($encounter_nr)&&!empty($HTTP_SESSION_VARS['sess_en'])){
    $HTTP_SESSION_VARS['sess_en'] = $_GET['encounter_nr'];
}elseif($encounter_nr) {
    $HTTP_SESSION_VARS['sess_en']=$encounter_nr;
}  
    
if((!isset($pid)||!$pid)&&$HTTP_SESSION_VARS['sess_pid']) $pid=$HTTP_SESSION_VARS['sess_pid'];
    elseif($pid) $HTTP_SESSION_VARS['sess_pid']=$pid;
    
   
# Load the entire encounter data
require_once($root_path.'include/care_api_classes/class_encounter.php');    
$enc_obj=new Encounter($encounter_nr);

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new person();

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward();  
    
$enc_obj->loadEncounterData();
if ($encounter_nr)
    $enc_Info = $enc_obj->getEncounterInfo($encounter_nr);
else
    $enc_Info = $person_obj->getAllInfoArray($pid); 
       
extract($enc_Info);

$sql_typ = "SELECT encounter_type, pid FROM care_encounter WHERE encounter_nr='".$encounter_nr."'";
$rs_typ = $db->Execute($sql_typ);
if ($rs_typ){
    $row_typ = $rs_typ->FetchRow();
    $encounter_type = $row_typ['encounter_type'];
}else
    $encounter_type = 0;
    
if (($encounter_type==1) || ($encounter_type==3) || ($encounter_type==4))
    $socialInfo = $objSS->getLatestClassification($encounter_nr,0);
else
    $socialInfo = $objSS->getLatestClassificationByPid($pid,1);
 
if (empty($discountId))
    $discountId = $socialInfo['discountid'];
    
if(empty($other_name)){
    $other_name = $socialInfo['other_name'];
}

if(empty($id_no)){
    $id_no = $socialInfo['id_number'];
}

$getParent = $objSS->getSSInfo($discountId,'','');                  
if (is_object($getParent)){
    while ($result=$getParent->FetchRow()) {
    $parentId = $result['parentid'];        
    }
}       

if(!empty($socialInfo['personal_circumstance'])){
    $submod = $socialInfo['personal_circumstance'];
    $mod = 1;
}elseif(!empty($socialInfo['community_situation'])){
    $mod = 2;
    $submod = $socialInfo['community_situation'];
}elseif(!empty($socialInfo['nature_of_disease'])){
    $mod = 3;
    $submod = $socialInfo['nature_of_disease'];    
} 


$smarty->assign('sCasenr',$HTTP_SESSION_VARS['sess_en']);
$smarty->assign('sHRN',$HTTP_SESSION_VARS['sess_pid']);

$patient_name = $name_last.", ".$name_first." ".$name_middle;
$smarty->assign('sPatient_name',mb_strtoupper($patient_name));

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
#    $prov_name = $prov_name;

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

$ssaddress = wordwrap($address, 47, "<br />\n");
$smarty->assign('sAddress',mb_strtoupper($ssaddress));

if($sex=='m') 
    $gender = 'Male';
elseif($sex=='f')
    $gender = 'Female';
else
    $gender = 'Unspecified';     

$smarty->assign('sSexType',mb_strtoupper($gender));
$smarty->assign('sAge',$age." old");

if (($date_birth)&&($date_birth!='0000-00-00'))
    $date_birth = date("F d, Y", strtotime($date_birth));
else
    $date_birth = 'Unspecified';    

$smarty->assign('sBdayDate',$date_birth);
$smarty->assign('sBirthPlace',mb_strtoupper($place_birth));

switch ($encounter_type){
    case '1' :  $enctype = "ER PATIENT";
                $location = "ER";
                $label = 'Consultation Date';
                $transaction_date = date("F d, Y h:i A",strtotime($encounter_date));
                $impression = $enc_obj->getLatestImpression($pid, $encounter_nr);
                $diagnosis = $impression;
                break;
    case '2' :
                $enctype = "OUTPATIENT";
                if ($current_dept_nr)
                    $dept = $dept_obj->getDeptAllInfo($current_dept_nr);

                $location = stripslashes($dept['name_formal']);
                $label = 'Consultation Date';
                $transaction_date = date("F d, Y h:i A",strtotime($encounter_date));
                $impression = $enc_obj->getLatestImpression($pid, $encounter_nr);
                $diagnosis = $impression;;
                break;
    case '3' :  $enctype = "INPATIENT (ER)";
                $patient_type = "IN";
                if ($current_ward_nr)
                    $ward = $ward_obj->getWardInfo($current_ward_nr);

                $location = stripslashes($ward['name']);
                $label = 'Admission Date';
                $transaction_date = date("F d, Y h:i A",strtotime($admission_dt));
                $diagnosis = $er_opd_diagnosis;
                break;
    case '4' :
                $enctype = "INPATIENT (OPD)";
                if ($current_ward_nr)
                    $ward = $ward_obj->getWardInfo($current_ward_nr);

                $location = stripslashes($ward['name']);
                $label = 'Admission Date';
                $transaction_date = date("F d, Y h:i A",strtotime($admission_dt));
                $diagnosis = $er_opd_diagnosis;
                break;
    case '5' :
                $enctype = "RDU";
                $location = "RDU";
                $label = 'Consultation Date';
                $transaction_date = date("F d, Y h:i A",strtotime($encounter_date));
                $impression = $enc_obj->getLatestImpression($pid, $encounter_nr);
                $diagnosis = $impression;
                break;
    case '6' :
                $enctype = "INDUSTRIAL CLINIC";
                $location = "INDUSTRIAL CLINIC";
                $label = 'Consultation Date';
                $transaction_date = date("F d, Y h:i A",strtotime($encounter_date));
                $impression = $enc_obj->getLatestImpression($pid, $encounter_nr);
                $diagnosis = $impression;
                break;
    default :
                $enctype = "WALK-IN";
                $location = "WALK-IN";
                $label = 'Consultation Date';
                $transaction_date = 'NONE';
                $diagnosis = 'NONE'; 
                break;
    }

$smarty->assign('sPType',$enctype);
$smarty->assign('sLocation',$location);
$smarty->assign('smonthly_income_remarks',$monthly_income_remarks);
$smarty->assign('smonthly_expenses_remarks',$monthly_expenses_remarks);



$smarty->assign('slabel',$label);

$smarty->assign('sAdmissionDate',$transaction_date);

$patSS = $objSS->getPatientMSS($HTTP_SESSION_VARS['sess_pid']);

if (!$patSS)
    $patSS['mss_no'] = 'No Classification Yet';
$smarty->assign('sMss_no',$patSS['mss_no']);

$p_status = $enc_obj->getPatientCaseType($HTTP_SESSION_VARS['sess_pid']);
$smarty->assign('sCategory',$p_status);

$pss_status = $objSS->getPatientCaseType_pid($HTTP_SESSION_VARS['sess_pid']);
if (!$pss_status)
    $pss_status = "New";
$smarty->assign('sSSCategory',$pss_status);


$ssdiagnosiss = wordwrap($diagnosis, 47, "<br />\n");
$smarty->assign('sDiagnosis',$ssdiagnosiss);

if($_GET['mode']=='new')
    $objSS_details = $objSS->getSocialServPatientEncounterByMSS($patSS['mss_no']);
else
    $objSS_details = $objSS->getSocialServPatientEncounter($encounter_nr, $pid);

if($objSS_details['date_interview'] && $objSS_details['date_interview'] != '0000-00-00'){
    $date_interview =  date("m/d/Y",strtotime($objSS_details['date_interview']));    
}elseif($objSS_details['create_time']){
    $date_interview =  date("m/d/Y",strtotime($objSS_details['create_time'])); 
}else{
    $date_interview = date("m/d/Y"); 
}                                      
$date_interview_text = '<div class="input text">
                    <div style="display:inline-block">
                        <input type="text" maxlength="10" size="8" id="interview_date" name="interview_date" class="segInput" style="font:bold 12px Arial" value="'.$date_interview.'">
                        <br>
                        <span style="margin-left:2px; font:normal 10px Tahoma; color:#447BC4" class="small">[mm/dd/yyyy]</span>
                    </div>
                    <button id="interview_date-trigger" style="margin-left: 4px; cursor: pointer;" onclick="return false" title="Select Interview Date">
                        <span class="icon calendar"></span>
                    </button>
                  </div>
                  '; 
                  
 $jsCalScript  = '<script type="text/javascript">
                    now = new Date();
                    Calendar.setup ({
                            inputField: "interview_date",
                            dateFormat: "'.$date_format2.'",
                            trigger: "interview_date-trigger",
                            showTime: false,
                            fdow: 0,
                            max : Calendar.dateToInt(now),
                            onSelect: function() { this.hide() }
                    });
                  </script>
                  ';

$smarty->assign('jsCalendarSetup', $jsCalScript);

$smarty->assign('date_interview', $date_interview_text);


if ($objSS_details['status']){
    $status=$objSS_details['status'];
}elseif ($civil_status) {
    $status= $civil_status;
}

$sql_cstatus = 'SELECT * FROM seg_social_civilstatus ORDER BY name';
$rs_cstatus = $db->Execute($sql_cstatus);
$cstatus_option="<option value=''>-Select Civil Status-</option>";
$cstatus_dep_option="<option value=''>-Select Civil Status-</option>";
if (is_object($rs_cstatus)){
    while ($row_cstatus=$rs_cstatus->FetchRow()) {
        $selected='';
        $selected_dep='';

        if ($status==$row_cstatus['id']){
            $selected='selected';
        }
        
        if ($cstatus_dep==$row_cstatus['id'])
            $selected_dep='selected';
                
        $cstatus_option.='<option '.$selected.' value="'.$row_cstatus['id'].'">'.ucwords($row_cstatus['name']).'</option>';
        
        $cstatus_dep_option.='<option '.$selected_dep.' value="'.ucwords($row_cstatus['name']).'">'.ucwords($row_cstatus['name']).'</option>';
    }
}
$cstatus_selection = '<select name="civil_status" id="civil_status" class="segInput" style="width:200px; font:bold 12px Arial;">
                        '.$cstatus_option.'
                    </select>';
$smarty->assign('civil_status', $cstatus_selection);

$sql_creligion = 'SELECT * FROM seg_religion ORDER BY religion_name';
$rs_creligion = $db->Execute($sql_creligion);
if (is_object($rs_creligion)){
    while ($row_creligion=$rs_creligion->FetchRow()) {
        $selected='';     
        if ($objSS_details['religion']==$row_creligion['religion_nr'])
            $selected='selected';
                
        $creligion_option.='<option '.$selected.' value="'.$row_creligion['religion_nr'].'">'.ucwords($row_creligion['religion_name']).'</option>';
    }
}
$creligion_selection = '<select name="religion" id="religion" class="segInput" style="width:200px; font:bold 12px Arial;">
                        '.$creligion_option.'
                    </select>';
$smarty->assign('religion', $creligion_selection);

$cstatus_dep_selection = '<select name="cstatus_dep" id="cstatus_dep" class="segInput" style="width:165px">
                        '.$cstatus_dep_option.'
                    </select>';
$smarty->assign('cstatus_dep', $cstatus_dep_selection);
if($objSS_details['address']){
    $address1 = $objSS_details['address'];        
}else{
    $address1 = $address;
}

$smarty->assign('temp_address','<textarea class="segInput" id="address" name="address*" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:bold; width:410px;">'.stripslashes($address1).'</textarea>');                  
$smarty->assign('companion','<input class="segInput" id="companion" name="companion" type="text" size="30" value="'.$objSS_details['companion'].'" style="width:200px; font:bold 12px Arial;"/>');
$smarty->assign('contact_number','<input class="segInput" id="contact_number" name="contact_number" type="text" size="30" value="'.$objSS_details['contact_no'].'" style="width:200px; font:bold 12px Arial;"/>');
$smarty->assign('employer_address','<textarea class="segInput" id="employer_address" name="employer_address" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:bold; width:410px;">'.stripslashes($objSS_details['employer_address']).'</textarea>');                  
$smarty->assign('employer','<input class="segInput" id="employer" name="employer" type="text" size="30" value="'.$objSS_details['employer'].'" style="width:200px; font:bold 12px Arial;"/>');
                  
$rs_obj = $person_obj->getEducationalAttainment();    
if (is_object($rs_obj)){
    while ($result=$rs_obj->FetchRow()) {
        $selected='';
        $selected_dep='';
        if ($objSS_details['educational_attain']==$result['educ_attain_nr'])
            $selected='selected';
            
        if ($educ_dep_select==$result['educ_attain_nr'])
            $selected_dep='selected';
                
        $educ_option.='<option '.$selected.' value="'.$result['educ_attain_nr'].'">'.ucwords($result['educ_attain_name']).'</option>';
        $educ_dep_option.='<option '.$selected_dep.' value="'.ucwords($result['educ_attain_name']).'">'.ucwords($result['educ_attain_name']).'</option>';
    }
}
$educ_selection = '<select name="educ_select" id="educ_select" class="segInput" style="font:bold 12px Arial; width:200px">
                        '.$educ_option.'
                    </select>';
$smarty->assign('attainment', $educ_selection);

$educ_dep_option = '<select name="educ_dep_select" id="educ_dep_select" class="segInput" style="width:165px">
                        '.$educ_dep_option.'
                    </select>';
$smarty->assign('education_dep', $educ_dep_option);

$sql_source = 'SELECT * FROM seg_source_income ORDER BY source_income_id';
$rs_source = $db->Execute($sql_source);
if (is_object($rs_source)){
    while ($row_source=$rs_source->FetchRow()) {
        $selected='';
        $selected_dep='';
        if ($objSS_details['occupation']==$row_source['source_income_id'])
            $selected='selected';
        if ($source_dep_income==$row_source['source_income_id'])
            $selected_dep='selected';    
        $source_option.='<option '.$selected.' value="'.$row_source['source_income_id'].'">'.ucwords($row_source['source_income_desc']).'</option>';
        $source_dep_option.='<option '.$selected_dep.' value="'.ucwords($row_source['source_income_desc']).'">'.ucwords($row_source['source_income_desc']).'</option>';
    }
}
$source_selection = '<select name="occupation" id="occupation" class="segInput" style="width:200px; font:bold 12px Arial;" onchange="showOT(this.value)">
                        '.$source_option.'
                    </select>';
$smarty->assign('occupation', $source_selection);

$smarty->assign('ot_occupation', '<tr id="ot_occupation" style="'.(($objSS_details['occupation']!='16')?'display:none':'').'">
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong></strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;&nbsp;</td>
                                            <td width="20%" nowrap="nowrap" class="reg_item"><strong>Other Occupation</strong></td>
                                            <td width="30%" nowrap="nowrap" class="segInput">&nbsp;<input class="segInput" id="ot_occu" name="ot_occu" type="text" size="30" value="'.$objSS_details['other_occupation'].'" style="width:200px; font:bold 12px Arial;"/></td>
                                    </tr>');

$source_dep_selection = '<select name="source_dep" id="source_dep" class="segInput" style="width:165px" onchange="showOTDep(this.value)">
                        '.$source_dep_option.'
                    </select>';
$smarty->assign('occupation_dep', $source_dep_selection);
$smarty->assign('ot_occupation_dep','<tr id="ot_dep_occu" style="display:none">
                <td nowrap="nowrap" ><strong>Other Occupation</strong></td>
                <td class="segSocial">&nbsp;&nbsp;<input class="segInput" id="ot_source_dep" name="ot_source_dep" type="text" size="30" value="" style="width:165px"; font:bold 12px Arial;"/></td>
                </tr>');

$sql_relationship = 'SELECT * FROM seg_social_relationships ORDER BY name';
$rs_relationship = $db->Execute($sql_relationship);
$relationship_option="<option value=''>-Select Relation-</option>";
if (is_object($rs_relationship)){
    while ($row_relationship=$rs_relationship->FetchRow()) {
        $selected='';
        $selected_dep='';
        if ($source_income==$row_relationship['id'])
            $selected='selected';
        if ($source_dep_income==$row_relationship['id'])
            $selected_dep='selected';    
        $relationship_option.='<option '.$selected.' value="'.$row_relationship['name'].'">'.ucwords($row_relationship['name']).'</option>';
 }
}
$relationship_selection = '<select name="relation_dep" id="relation_dep" class="segInput" style="width:165px">
                        '.$relationship_option.'
                    </select>';
$smarty->assign('relation_dep', $relationship_selection);

$smarty->assign('informant','<input class="segInput" id="resp" name="resp*" type="text" size="30" value="'.$objSS_details['informant_name'].'" style="width:200px; font:bold 12px Arial;"/>');
$smarty->assign('pat_relation','<input class="segInput" id="relation" name="relation*" type="text" size="30" value="'.$objSS_details['relation_informant'].'" style="width:200px;font:bold 12px Arial;"/>');
$smarty->assign('informant_address','<textarea class="segInput" id="informant_address" name="informant_address*" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:bold; width:410px;">'.stripslashes($objSS_details['info_address']).'</textarea>');                  

$sql = "SELECT memcategory_desc,hcare_id FROM seg_encounter_memcategory `sem` 
        INNER JOIN seg_memcategory `sm` ON sm.memcategory_id=sem.memcategory_id
        INNER JOIN seg_encounter_insurance `s` ON s.encounter_nr = sem.encounter_nr
        WHERE sem.encounter_nr=$encounter_nr";
$rs_phic = $db->Execute($sql);
if (is_object($rs_phic)){
    while ($row_phic=$rs_phic->FetchRow()) {
     $phic_category = $row_phic['memcategory_desc'];
     $phic_member = $row_phic['hcare_id'];   
    }    
}
if($phic_member==18){
    $isMember = "Yes";
    $cat = $phic_category; 
}else{
    $isMember = "No";
    $cat = "Non-Philhealth Member";
}

if ($objSS_details['is_poc']=='0') {
    $is_poc_no = 'selected';
    $is_poc_yes = '';
} elseif ($objSS_details['is_poc']=='1') {
    $is_poc_no = '';
    $is_poc_yes = 'selected'; 
}
  
$smarty->assign('phic_member','<input class="segInput" id="phic_member" name="phic_member" type="text" size="30" value="'.$isMember.'" style="width:200px;font:bold 12px Arial;" readOnly/>');
$smarty->assign('phic_category','<input class="segInput" id="phic_category" name="phic_category" type="text" size="30" value="'.$cat.'" style="width:200px;font:bold 12px Arial;" readOnly />');
$smarty->assign('is_poc','<select name="is_poc" id="is_poc" class="segInput" style="width:200px;font:bold 12px Arial;">
                            <option '.$is_poc_no.' value="0">No</option>
                            <option '.$is_poc_yes.' value="1">Yes</option> 
                        </select>');
 
$rs_class = $objSS->getSSInfo1(0);
$class_option="<option value=''>-Select Classification-</option>";
if (is_object($rs_class)){
    while ($result=$rs_class->FetchRow()) {
        $selected='';
        if ($parentId==$result['discountid']){
            $selected='selected';                
        }
        if ($discountId==$result['discountid']){
            $selected='selected';                
        }
            $class_option.='<option '.$selected.' value="'.$result['discountid'].'">'.ucwords($result['discountdesc']).'</option>';
    }
}
$class_selection = '<select name="service_code*" id="service_code" class="segInput" onchange="getSubClass(this.value);" style="width:200px;font:bold 12px Arial;">
                        '.$class_option.'
                    </select>';
$smarty->assign('classification', $class_selection);

$rs_class = $objSS->getSSInfo1(1);
$class_option="<option value=''>-Select Additional Support-</option>";
if (is_object($rs_class)){
    while ($result=$rs_class->FetchRow()) {
        $selected='';
        if ($parentId==$result['discountid']){
            $selected='selected';                
        }
        if ($discountId==$result['discountid']){
            $selected='selected';                
        }
            $class_option.='<option '.$selected.' value="'.$result['discountid'].'">'.ucwords($result['discountdesc']).'</option>';
    }
}
$class_selection = '<select name="additional_support" id="additional_support" class="segInput" onchange="changeIndex(this.value)" style="width:200px;font:bold 12px Arial;">
                        '.$class_option.'
                    </select>';
$smarty->assign('additional_support', $class_selection);

if($parentId=='D' || $parentId=='C3'){
    $rs_sectoral = $objSS->getSSChildArray($parentId);
    $disable = '';
}else{ 
    $disable = 'disabled';
}
         
$sectoral_option="<option value=''>-Select Sub Classification-</option>";
if (is_object($rs_sectoral)){
    while ($result=$rs_sectoral->FetchRow()) {
        $selected='';
        if ($discountid==$result['discountid'])
            $selected='selected';   
        $sectoral_option.='<option '.$selected.' value="'.$result['discountid'].'">'.ucwords($result['discountdesc']).'</option>';
    }
}
$sectoral_selection = '<select '.$disable.' name="subservice_code" id="subservice_code" class="segInput" style="width:200px;font:bold 12px Arial;" onchange="showSCID(this.value);">
                        '.$sectoral_option.'
                    </select>';
$smarty->assign('sectoral', $sectoral_selection);

$rs_modifier = $objSS->getModifier2();
$modifier_option="<option value=''>-Select Modifier-</option>";
if (is_object($rs_modifier)){
    while ($result=$rs_modifier->FetchRow()) {
        $selected='';
        if ($mod==$result['mod_code'])
            $selected='selected';
        $modifier_option.='<option '.$selected.' value="'.$result['mod_code'].'">'.ucwords($result['mod_short']).'</option>';
    }
}
$modifier_selection = '<select name="modifier_select" id="modifier_select" class="segInput" onchange="getSubMod(this.value);" style="width:200px;font:bold 12px Arial;">
                        '.$modifier_option.'
                    </select>';
$smarty->assign('modifier', $modifier_selection);

$rs_sub_modifier = $objSS->getModifiers($mod);
$sub_modifier_option="<option value=''>-Select Sub Modifier-</option>";
if (is_object($rs_sub_modifier)){
    while ($result=$rs_sub_modifier->FetchRow()){
        $selected='';
        $attributes = '';
        if ($submod==$result['mod_subcode'])
            $selected='selected';
        $mod_desc = $result['mod_subdesc'];
        $mod_code = $result['mod_subcode'];
        $attributes .= 'onmouseover="return overlib($(\'submod'.$mod_code.'\').value, CAPTION,\'Details\', BORDER,0,TEXTPADDING,5, TEXTFONTCLASS,\'oltxt\', CAPTIONFONTCLASS,\'olcap\',WIDTH,400, FGCLASS,\'olfgPopup\', FIXX,10, FIXY,10);"'; 
        $attributes .= 'onMouseout ="mouseOut();"';
        $sub_modifier_option.='<option '.$selected.' '.$attributes.' value="'.$result['mod_subcode'].'">'.ucwords($result['mod_subcode']).'</option>';
        $sub_modifier_input.='<input class="segInput" id="submod'.$result['mod_subcode'].'" name="submod'.$result['mod_subcode'].'" type="hidden" value="'.$mod_desc.'"/>';
    }
}
$sub_modifier_selection = '<select name="sub_modifier_select" id="sub_modifier_select" class="segInput" style="width:200px;font:bold 12px Arial;" onchange="">
                        '.$sub_modifier_option.'
                    </select>';
$smarty->assign('sub_modifier', $sub_modifier_selection.$sub_modifier_input);

$smarty->assign('id_row','<tr id="id_no_tag" style="'.(($discountid!='SC')?'display:none':'').'">
                                <td width="20%" nowrap="nowrap" class="reg_item"><strong>ID number</strong></td>
                                <td width="*" nowrap="nowrap" class="segInput">&nbsp;<input class="segInput" id="id_no" name="id_no" type="text" size="30" value="'.$id_no.'" style="width:200px;font:bold 12px Arial;"/></td>
                                </tr>');
$smarty->assign('other_row','<tr id="other_tag" style="'.(($discountid!='OT')?'display:none':'').'">
                                <td width="20%" nowrap="nowrap" class="reg_item"><strong>Other Classification</strong></td>
                                <td width="*" nowrap="nowrap" class="segInput">&nbsp;<input class="segInput" id="other_row" name="other_row" type="text" size="30" value="'.$other_name.'" style="width:200px;font:bold 12px Arial;"/></td>
                                </tr>');
                               
                                
$smarty->assign('name_dep','<input class="segInput" type="text" style="width:165px" name="name_dep" id="name_dep" />');
$smarty->assign('age_dep','<input class="segInput" type="text" style="width:30px" name="age_dep" id="age_dep" />');
$smarty->assign('monthly_income_dep','<input class="segInput" type="text" value="" style="width:165px" name="mincome_dep" id="mincome_dep" />');
$smarty->assign('addbtn', '<button id="addbtn" name="addbtn" class="icon-only" onclick="showDependentDialog();" type="submit">
                                           <span class="ui-icon ui-icon-plusthick" style="display:inline-block;"></span>
                                           </button>');
                                           
$smarty->assign('household_no','<input class="segInput" id="nr_dep" name="nr_dep*" type="text" size="10" value="'.(($objSS_details['nr_dependents'])?$objSS_details['nr_dependents']:1).'" style="font:bold 12px Arial; text-align:right" onblur="computeCapita();"/>');
$smarty->assign('income','<input class="segInput" id="m_income2" name="m_income2*" type="text" size="10" value="'.(($objSS_details['income'])?$objSS_details['income']:'0.00').'" style="font:bold 12px Arial; text-align:right" onblur="computeMonthly();formatValue(this,2);"/>');
$smarty->assign('other_income','<input class="segInput" id="other_income" name="other_income" type="text" size="10" value="'.$objSS_details['other_income'].'" style="font:bold 12px Arial; text-align:right" onblur="computeMonthly();formatValue(this,2);"/>');
$smarty->assign('other_source_income','<textarea class="segInput" id="other_source_income" name="other_source_income" cols="40" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:bold">'.stripslashes($objSS_details['source_income']).'</textarea>');                  
$smarty->assign('capita_income','<input class="segInput" id="capita_income" name="capita_income" type="text" size="10" value="0.00" style="font:bold 12px Arial; text-align:right" readOnly/>');
$smarty->assign('total_income','<input class="segInput" id="total_income" name="total_income" type="text" size="10" value="" style="font:bold 12px Arial; text-align:right" readOnly/>');

//Daryl
//add Remarks in Monthly Income
//10/17/2013
$smarty->assign('monthly_income_remarks','<textarea class="segInput" id="monthly_income_remarks" name="monthly_income_remarks*" cols="40" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:bold">'.$objSS_details['monthly_income_remarks'].'</textarea>');                  

$smarty->assign('monthly_expenses_remarks','<textarea class="segInput" id="monthly_expenses_remarks" name="monthly_expenses_remarks*" cols="40" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:bold">'.$objSS_details['monthly_expenses_remarks'].'</textarea>');                  



$smarty->assign('light_amount','<input class="segInput" id="light_amount" name="light_amount" type="text" size="10" value="'.$objSS_details['ligth_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('living_amount','<input class="segInput" id="living_amount" name="living_amount" type="text" size="10" value="'.$objSS_details['hauz_lot_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('water_amount','<input class="segInput" id="water_amount" name="water_amount" type="text" size="10" value="'.$objSS_details['water_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('fuel_amount','<input class="segInput" id="fuel_amount" name="fuel_amount" type="text" size="10" value="'.$objSS_details['fuel_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('food_amount','<input class="segInput" id="food_amount" name="food_amount" type="text" size="10" value="'.$objSS_details['food_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('househelp_amount','<input class="segInput" id="househelp_amount" name="househelp_amount" type="text" size="10" value="'.$objSS_details['househelp_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('educ_amount','<input class="segInput" id="educ_amount" name="educ_amount" type="text" size="10" value="'.$objSS_details['education_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('medical_amount','<input class="segInput" id="medical_amount" name="medical_amount" type="text" size="10" value="'.$objSS_details['med_expenditure'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('clothing_amount','<input class="segInput" id="clothing_amount" name="clothing_amount" type="text" size="10" value="'.$objSS_details['clothing_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('plan_amount','<input class="segInput" id="plan_amount" name="plan_amount" type="text" size="10" value="'.$objSS_details['insurance_mortgage'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('trans_amount','<input class="segInput" id="trans_amount" name="trans_amount" type="text" size="10" value="'.$objSS_details['transport_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('others_amount','<input class="segInput" id="others_amount" name="others_amount" type="text" size="10" value="'.$objSS_details['other_expense'].'" style="font:bold 12px Arial; text-align:right" onblur="computeTotal();formatValue(this,2);"/>');
$smarty->assign('total_expenses','<input class="segInput" id="total_expenses" name="total_expenses" type="text" size="10" value="'.$objSS_details['total_monthly_expense'].'" style="font:bold 12px Arial; text-align:right" readOnly/>');

$sql_living = 'SELECT * FROM seg_social_house_type';
$rs_living = $db->Execute($sql_living);
$living_option="<option value=''>-Not Indicated-</option>";
if (is_object($rs_living)){
    while ($row_living=$rs_living->FetchRow()) {
        $selected='';
        if ($objSS_details['house_type']==$row_living['house_type_nr'])
            $selected='selected';
        $living_option.='<option '.$selected.' value="'.$row_living['house_type_nr'].'">'.ucwords($row_living['house_description']).'</option>';
    }
}
$living_selection = '<select name="living" id="living" class="segInput" style="width:150px;font:bold 12px Arial;" onChange="changeVal();">
                        '.$living_option.'
                    </select>';
$smarty->assign('living', $living_selection);

$sql_light_source = 'SELECT * FROM seg_social_light_source';
$rs_light_source = $db->Execute($sql_light_source);
$light_source_option="<option value=''>-Not Indicated-</option>";
if (is_object($rs_light_source)){
    while ($row_light_source=$rs_light_source->FetchRow()) {
        $selected='';
        if ($objSS_details['light_source']==$row_light_source['id'])
            $selected='selected';
        $light_source_option.='<option '.$selected.' value="'.$row_light_source['id'].'">'.ucwords($row_light_source['name']).'</option>';
    }
}
$light_source_selection = '<select name="light_source" id="light_source" class="segInput" style="width:150px;font:bold 12px Arial;">
                        '.$light_source_option.'
                    </select>';
$smarty->assign('light_source', $light_source_selection);

$sql_water_source = 'SELECT * FROM seg_social_water_source';
$rs_water_source = $db->Execute($sql_water_source);
$water_source_option="<option value=''>-Not Indicated-</option>";
if (is_object($rs_water_source)){
    while ($row_water_source=$rs_water_source->FetchRow()) {
        $selected='';
        if ($objSS_details['water_source']==$row_water_source['id'])
            $selected='selected';
        $water_source_option.='<option '.$selected.' value="'.$row_water_source['id'].'">'.ucwords($row_water_source['name']).'</option>';
    }
}
$water_source_selection = '<select name="water" id="water" class="segInput" style="width:150px;font:bold 12px Arial;">
                        '.$water_source_option.'
                    </select>';
$smarty->assign('water_source', $water_source_selection);

$sql_fuel_source = 'SELECT * FROM seg_social_fuel_source';
$rs_fuel_source = $db->Execute($sql_fuel_source);
$fuel_source_option="<option value=''>-Not Indicated-</option>";
if (is_object($rs_fuel_source)){
    while ($row_fuel_source=$rs_fuel_source->FetchRow()) {
        $selected='';
        if ($objSS_details['fuel_source']==$row_fuel_source['id'])
            $selected='selected';
        $fuel_source_option.='<option '.$selected.' value="'.$row_fuel_source['id'].'">'.ucwords($row_fuel_source['name']).'</option>';
    }
}
$fuel_source_selection = '<select name="fuel" id="fuel" class="segInput" style="width:150px;font:bold 12px Arial;">
                        '.$fuel_source_option.'
                    </select>';
$smarty->assign('fuel_source', $fuel_source_selection);

$smarty->assign('duration_prob','<textarea class="segInput" id="duration_prob" name="duration_prob" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; width:450px; font-weight:bold">'.stripslashes($objSS_details['duration_problem']).'</textarea>');                  
$smarty->assign('prev_treatment','<textarea class="segInput" id="prev_treatment" name="prev_treatment" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; width:450px; font-weight:bold">'.stripslashes($objSS_details['duration_treatment']).'</textarea>');                  
$smarty->assign('present_treatment','<textarea class="segInput" id="present_treatment" name="present_treatment" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; width:450px; font-weight:bold">'.stripslashes($objSS_details['treatment_plan']).'</textarea>');                  
$smarty->assign('health_access','<textarea class="segInput" id="health_access" name="health_access" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; width:450px; font-weight:bold">'.stripslashes($objSS_details['accessibility_problem']).'</textarea>');                        
$smarty->assign('final_diagnosis','<textarea class="segInput" id="final_diagnosis" name="final_diagnosis" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; width:450px; font-weight:bold">'.stripslashes($objSS_details['final_diagnosis']).'</textarea>');   

$sql_source_referral = 'SELECT * FROM seg_social_source_referral';
$rs_source_referral = $db->Execute($sql_source_referral);
$source_referral_option="<option value=''>-Not Indicated-</option>";
if (is_object($rs_source_referral)){
    while ($row_source_referral=$rs_source_referral->FetchRow()) {
        $selected='';
        if ($objSS_details['source_referral']==$row_source_referral['source_nr'])
            $selected='selected';
        $source_referral_option.='<option '.$selected.' value="'.$row_source_referral['source_nr'].'">'.ucwords($row_source_referral['source']).'</option>';
    }
}
$source_referral_selection = '<select name="source_referral" id="source_referral" class="segInput" style="width:200px; font:bold 12px Arial;">
                        '.$source_referral_option.'
                    </select>';
$smarty->assign('source_referral', $source_referral_selection);

$smarty->assign('name_referral','<input class="segInput" id="name_referral" name="name_referral" type="text" size="30" value="'.$objSS_details['name_referral'].'" style="width:200px; font:bold 12px Arial;"/>');
$smarty->assign('name_address','<textarea class="segInput" id="name_address" name="name_address" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; width:450px; font-weight:bold">'.stripslashes($objSS_details['info_agency']).'</textarea>');                  
$smarty->assign('referral_number','<input class="segInput" id="referral_number" name="referral_number" type="text" size="30" max="10" value="'.$objSS_details['info_contact_no'].'" style="width:200px; font:bold 12px Arial;"/>');
$smarty->assign('remarks','<textarea class="segInput" id="remarks" name="remarks" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; width:450px; font-weight:bold">'.stripslashes($objSS_details['remarks']).'</textarea>');                  
if($objSS_details['social_worker']){
    $social_worker = $objSS_details['social_worker'];    
}else{
    $social_worker = $HTTP_SESSION_VARS['sess_user_name'];
}
$smarty->assign('social_worker','<input class="segInput" id="social_worker" name="social_worker" type="text" size="30" max="10" value="'.$social_worker.'" style="width:200px; font:bold 12px Arial;"/>');                                  
$smarty->assign('DeMe_submit','<button id="DeMe_submit" name="DeMe_submit" class="DeMe_submit" type="submit" onclick="saveDeMeData()" style="margin-left: 4px; cursor: pointer; font:bold 12px Arial;">Save Demographic and Medical Data</button>');
$smarty->assign('DeMe_cancel','<button id="DeMe_cancel" name="DeMe_cancel" class="DeMe_submit" type="submit" onclick="javascript:window.parent.cClick();" style="margin-left: 4px; cursor: pointer; font:bold 12px Arial;">Cancel</button>');                    
$smarty->assign('DeMe_print','<button id="DeMe_print" name="DeMe_print" class="DeMe_print" type="submit" onclick="javascript:window.parent.reportProfile('.$encounter_nr.','.$pid.');" style="margin-left: 4px; cursor: pointer; font:bold 12px Arial;">Print</button>');
$c=1;
$rs_assessHead = $objSS->getAssessHeader();
if (is_object($rs_assessHead)){
    while($result = $rs_assessHead->Fetchrow()){
        $group = $result['group'];
        if($group=='SF')
            $temp .= '<tr><td colspan="6"><strong>'.$result['id'].'. '.strtoupper($result['desc']).'</strong></td></tr>';
        else 
            $temp1 .= '<tr><td colspan="6"><strong>'.$c++.'. '.strtoupper($result['desc']).'</strong></td></tr>'; 
            
            $rs_assessDetails = $objSS->getAssessDetails($result['id']);
            if(is_object($rs_assessDetails)){
                while($result1= $rs_assessDetails->Fetchrow()){
                    
                    if($group=='SF'){
                        $rs_sfunctioning = $objSS->getSocialFunctioning($pid,$encounter_nr,$result1['id']);
                        if(is_object($rs_sfunctioning)){
                            while($result_sf = $rs_sfunctioning->Fetchrow()){
                                $sf_interaction = $result_sf['interaction_id'];
                                $sf_severity = $result_sf['severity_id'];
                                $sf_duration = $result_sf['duration_id'];
                                $sf_coping = $result_sf['coping_id'];
                                $sf_other = $result_sf['others'];    
                            }    
                        }
                    }else{
                        $rs_sproblem = $objSS->getSocialProblems($pid,$encounter_nr,$result1['id']);
                        if(is_object($rs_sproblem)){
                            while($result_sp = $rs_sproblem->Fetchrow()){
                                $sp_severity = $result_sp['severity_id'];
                                $sp_duration = $result_sp['duration_id'];
                                $sp_other = $result_sp['others'];    
                            }    
                        }                        
                    }
                    
                    if(strtoupper($result1['desc'])=='OTHER'){
                        if($group=='SF')
                            $temp .= '<tr> <td width="1%">&nbsp;</td> <td width="18%">'.strtoupper($result1['desc']).' <input id="txt_'.$result1['id'].'" name="txt_'.$result1['id'].'" value="'.$sf_other.'" type="text" size="30"></td>';
                        else
                            $temp1 .= '<tr> <td width="1%">&nbsp;</td> <td width="*" colspan="2">'.strtoupper($result1['desc']).' <input id="txt_'.$result1['id'].'" name="txt_'.$result1['id'].'" value="'.$sp_other.'" type="text" size="30"></td>';    
                    }else{
                        if($group=='SF')
                            $temp .= '<tr> <td width="1%">&nbsp;</td> <td width="18%">'.strtoupper($result1['desc']).'</td>';
                        else
                            $temp1  .= '<tr> <td width="1%">&nbsp;</td> <td width="*" colspan="2">'.strtoupper($result1['desc']).'</td>'; 
                    }
                    
                    $sql_social = "SELECT * FROM seg_social_type_interaction ORDER BY type_nr";
                    $rs_interact = $db->Execute($sql_social);  
                        if (is_object($rs_interact)){
                            $option_interact ='';
                            while ($interact_result=$rs_interact->FetchRow()){
                                $selected = '';
                                if($sf_interaction==$interact_result['type_nr']){
                                    $selected = 'selected';
                                } 
                                $option_interact .='<option '.$selected.' value="'.$interact_result['type_nr'].'">'.ucwords($interact_result['type_of_interaction']).'</option>';  
                            }
                        }
                    if($group=='SF') 
                        $temp .= '<td width="11%" align="center"><select name="'.$result1['id'].'_problem" id="'.$result1['id'].'_problem" class="segInput">'. $option_interact.'</select></td>';
                    
                    $sql_severity = "SELECT * FROM seg_social_severity_index ORDER BY severity_nr";
                    $rs_severity = $db->Execute($sql_severity);  
                    if (is_object($rs_severity)){
                        $option_severity ='';
                        while ($severity_result=$rs_severity->FetchRow()) {
                            $selected = '';
                            if($sf_severity == $severity_result['severity_nr'] && $group == 'SF'){
                                $selected = 'selected';    
                            }elseif ($sp_severity == $severity_result['severity_nr'] && $group !='SF'){
                                $selected = 'selected';
                            }
                            $option_severity .='<option '.$selected.' value="'.$severity_result['severity_nr'].'">'.ucwords($severity_result['severity_index']).'</option>';
                        }
                    }
                    
                    if($group=='SF')
                        $temp .= '<td width="11%" align="center"><select name="'.$result1['id'].'_severity" id="'.$result1['id'].'_severity" class="segInput">'.$option_severity.'</select></td>';
                    else
                        $temp1 .= '<td width="18%" align="rigth"><select name="'.$result1['id'].'_severity" id="'.$result1['id'].'_severity" class="segInput">'.$option_severity.'</select></td>'; 
                    
                    $sql_duration = "SELECT * FROM seg_social_duration_index ORDER BY duration_nr";
                    $rs_duration = $db->Execute($sql_duration);  
                    if (is_object($rs_duration)){
                        $option_duration = '';
                        while ($duration_result=$rs_duration->FetchRow()){
                            $selected = '';
                            if($sf_severity == $duration_result['duration_nr'] && $group == 'SF'){
                                $selected = 'selected';    
                            }elseif ($sp_severity == $duration_result['duration_nr'] && $group !='SF'){
                                $selected = 'selected';
                            }
                            $option_duration .= '<option '.$selected.' value="'.$duration_result['duration_nr'].'">'.ucwords($duration_result['duration_index']).'</option>'; 
                        }
                    }
                    
                    if($group=='SF')
                        $temp .= '<td width="11%" align="center"><select name="'.$result1['id'].'_duration" id="'.$result1['id'].'_duration" class="segInput">'.$option_duration.'</select></td>';
                    else
                        $temp1 .= '<td width="18%" align="rigth"><select name="'.$result1['id'].'_duration" id="'.$result1['id'].'_duration" class="segInput">'.$option_duration.'</select></td>';  
                    
                    $sql_coping = "SELECT * FROM seg_social_coping_index ORDER BY coping_nr";
                    $rs_coping = $db->Execute($sql_coping);  
                    if (is_object($rs_coping)){
                        $option_coping ='';
                        while ($coping_result=$rs_coping->FetchRow()) {
                            $selected = '';
                            if($sf_coping==$coping_result['coping_nr']){
                                $selected = 'selected';    
                            }
                            $option_coping .= '<option '.$selected.' value="'.$coping_result['coping_nr'].'">'.ucwords($coping_result['coping_index']).'</option>'; 
                        }
                    }
                    if($group=='SF')
                        $temp .= '<td width="11%" align="center"><select name="'.$result1['id'].'_coping" id="'.$result1['id'].'_coping" class="segInput">'.$option_coping.'</select></td>'; 

                    $temp .= '</tr>';
                    $temp1 .= '</tr>';    
                }   
            }   
        

    }    
} 

$rs_snoproblem = $objSS->getNoSocialProblem($pid,$encounter_nr);
if(is_object($rs_snoproblem)){
    while($result_snp = $rs_snoproblem->Fetchrow()){
        $no_social = $result_snp['no_social_problem'];   
    }    
}
$smarty->assign('no_social_problem',' <td colspan="6">NO SOCIAL INTERACTION PROBLEMS <input type="checkbox" id="no_social_problem" name="no_social_problem" '.(($no_social!=0)?"checked":'').' value=""></td>');

$rs_findings= $objSS->getSocialFindings($pid,$encounter_nr);
if(is_object($rs_findings)){
    while($result_f = $rs_findings->Fetchrow()){
        $pproblem = $result_f['problem_presented']; 
        $other = $result_f['other_problem'];
        $counsel = $result_f['counseling_done'];
        $topic = $result_f['topic_concern'];
        $nreason = $result_f['no_reason'];
        $sdiagnosis = $result_f['social_diagnosis'];
        $intervention = $result_f['intervention'];
        $action = $result_f['action_taken'];
        $remarks = $result_f['remarks'];  
    }    
}
$c=0;
$rs_problem = $objSS->getPatientProblem();
$count = $rs_problem->RecordCount();
if (is_object($rs_problem)){
    $problem_nr = explode(",",$pproblem);
    while($result = $rs_problem->Fetchrow()){
        $check = '';
        $c++;
        $count--;
        foreach($problem_nr as $value){
            if($value == $result['id']){
                $check = 'checked';
            }    
        }    
        if(strtoupper($result['desc'])=='OTHERS'){
            if($check=='checked')
                $display = "display:''";
            else
                $display = "display:none"; 
            $td .= '<td width="50%" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_problems" name="'.$result['id'].'_problems" '.$check.' onclick="hidetext('.$result['id'].')"> '.ucwords($result['desc']).' 
                             <input id="other_problem" name="other_problem" type="text" size="30" style="'.$display.'" value="'.$other.'"></td>';     
        }else
            $td .= '<td width="50%" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_problems" name="'.$result['id'].'_problems" '.$check.'> '.ucwords($result['desc']).'</td>';  
    
        if($c==2 || $count<=1){
            $problemTemp .= '<tr>'.$td.'</tr>';
            $c=0;
            $td=''; 
        }
    }   
}
  
$rs_topics = $objSS->getTopicConcern();
if (is_object($rs_topics)){
    $topic_nr = explode(",",$topic);
    while($result = $rs_topics->Fetchrow()){
    $check = '';
    foreach($topic_nr as $value){
        if($value == $result['id']){
            $check = 'checked';
        }    
    }
    $topicTemp .= '<tr><td><input type="checkbox" id="'.$result['id'].'" name="'.$result['id'].'_topics" '.$check.'>'.ucwords($result['desc']).'</td></tr>';    
    }
}
$smarty->assign('counseling','<td width="*" nowrap="nowrap"><input type="checkbox" id="counseling_done" name="counseling_done" '.(($counsel!=0)?"checked":'').'> YES</td>');
$smarty->assign('no_reason','<td width="*" nowrap="nowrap"  class="segInput"><input type="text" id="no_reason" name="no_reason" value="'.$nreason.'" size="100"></td>');
$smarty->assign('social_diagnosis','<td width="*" nowrap="nowrap"  class="segInput"><input type="text" id="social_diagnosis" name="social_diagnosis" value="'.$sdiagnosis.'" size="100"></td>');
$smarty->assign('intervention','<td width="*" nowrap="nowrap"  class="segInput"><input type="text" id="intervention" name="intervention" value="'.$intervention.'" size="100"></td>');
$smarty->assign('action_taken','<td width="*" nowrap="nowrap"  class="segInput"><input type="text" id="action_taken" name="action_taken" value="'.$action.'" size="100"></td>');
$smarty->assign('fremarks','<td width="*" nowrap="nowrap"  class="segInput"><input type="text" id="fremarks" name="fremarks" value="'.$remarks.'" size="100"></td>');
$smarty->assign('sfTemp',$temp);
$smarty->assign('peTemp',$temp1);
$smarty->assign('topictemp',$topicTemp);
$smarty->assign('problemtemp',$problemTemp);
//$smarty->assign('social_submit','<button id="social_submit" name="social_submit" class="social_submit" type="submit" onclick="saveSocialFunctioning();" style="margin-left: 10px; cursor: pointer; font:bold 12px Arial;">Save Social Functioning</button>');
//$smarty->assign('problem_submit','<button id="problem_submit" name="problem_submit" class="problem_submit" type="submit" onclick="saveSocialProblem();" style="margin-left: 4px; cursor: pointer; font:bold 12px Arial;">Save Problems in the Environment</button>');
$smarty->assign('findings_submit','<button id="findings_submit" name="findings_submit" class="findings_submit" type="submit" onclick="saveAssessment(0);" style="margin-left: 4px; cursor: pointer; font:bold 12px Arial;">Save Assessment Data</button>');
$smarty->assign('Assess_print','<button id="Assess_print" name="Assess_print" class="Assess_print" type="submit" onclick="javascript:window.parent.reportProfile('.$encounter_nr.','.$pid.');" style="margin-left: 4px; cursor: pointer; font:bold 12px Arial;">Print</button>');


$rs_case= $objSS->getSocialCase($pid,$encounter_nr);
if(is_object($rs_case)){
    while($result_c = $rs_case->Fetchrow()){
        $planning = $result_c['planning']; 
        $provision = $result_c['provision'];
        $outgoing = $result_c['outgoing'];
        $incoming = $result_c['incoming'];
        $follow_up = $result_c['follow_up'];
        $leading_reasons = $result_c['leading_reasons'];
        $social_work = $result_c['social_work'];
        $discharge_services = $result_c['discharge_services'];
        $case_con = $result_c['case_con'];
        $coordination = $result_c['coordination'];
        $documentation = $result_c['documentation'];
        $coordination_o = $result_c['others_coordination'];
        $documentation_o = $result_c['others_documentation'];
        $cremarks = $result_c['remarks'];  
    }    
}

$sql_planning = 'SELECT * FROM seg_social_planning ORDER BY id';
$rs_planning = $db->Execute($sql_planning);
$count = $rs_planning->RecordCount();
$c=0;
 if(is_object($rs_planning)){
    $planning_nr = explode(",",$planning); 
    while($result = $rs_planning->Fetchrow()){
        $check = '';
        $c++;
        $count--;
        foreach($planning_nr as $value){
            if($value == $result['id']){
                $check = 'checked';
            }    
        } 
        $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_planning" name="'.$result['id'].'_planning" '.$check.'> '.ucwords($result['desc']).'</td>';
     
         if($c==2 || $count==0){
                $planningTemp .= '<tr>'.$td.'</tr>';
                $c=0;
                $td=''; 
         }       
    }
 }

$sql_Hreferral = 'SELECT * FROM seg_social_concrete_referral ORDER BY id';
$rs_Hreferral = $db->Execute($sql_Hreferral);
 if(is_object($rs_Hreferral)){
    while($result = $rs_Hreferral->Fetchrow()){
        $referralTemp .= '<table><tr><td width="150px" class="reg_item"><strong>'.strtoupper($result['desc']).'</strong></td>
                            <td width="*" nowrap="nowrap" class="segInput">
                                <table width="100%">';
        $desc = strtolower(str_replace(' ', '', $result['desc']));
        $sql_Dreferral = 'SELECT * FROM seg_social_concrete_referral_details WHERE concrete_id='.$result['id'];
        $rs_Dreferral = $db->Execute($sql_Dreferral);
        $count = $rs_Dreferral->RecordCount();
        $c=0;
        if(is_object($rs_Dreferral)){
            $provision_nr = explode(",",$provision);
            $outgoing_nr = explode(",",$outgoing); 
            $incoming_nr = explode(",",$incoming);  
            while($result1 = $rs_Dreferral->Fetchrow()){
            $check = '';
            $c++;
            $count--;
            if($result['id'] == 1){
                foreach($provision_nr as $value){
                    if($value == $result1['id']){
                        $check = 'checked';
                    }    
                }     
            }elseif($result['id'] == 2){
                foreach($outgoing_nr as $value){
                    if($value == $result1['id']){
                        $check = 'checked';
                    }    
                }                
            }elseif($result['id'] == 3){
                foreach($incoming_nr as $value){
                    if($value == $result1['id']){
                        $check = 'checked';
                    }    
                }                
            }

            $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result1['id'].'_'.$desc.'" name="'.$result1['id'].'_'.$desc.'" '.$check.'> '.ucwords($result1['desc']).'</td>';
     
            if($c==2 || $count==0){
                $referralD .= '<tr>'.$td.'</tr>';
                $c=0;
                $td=''; 
            }    
            }
        }                                         
        
        $referralTemp .= $referralD.'</table> </td> </tr> </table> <br/>';
        $referralD = '';
     }
 }
 
$sql_Hpsycho = 'SELECT * FROM seg_social_psycho_counselling ORDER BY id';
$rs_Hpsycho = $db->Execute($sql_Hpsycho);
 if(is_object($rs_Hpsycho)){
    while($result = $rs_Hpsycho->Fetchrow()){
        $psychoTemp .= '<table><tr><td width="150px" class="reg_item"><strong>'.strtoupper($result['desc']).'</strong></td>
                            <td width="*" nowrap="nowrap" class="segInput">
                                <table width="100%">';
        $desc = strtolower(str_replace(' ', '', $result['desc']));
        $sql_Dpsycho = 'SELECT * FROM seg_social_psycho_counselling_details WHERE psycho_id='.$result['id'];
        $rs_Dpsycho = $db->Execute($sql_Dpsycho);
        $count = $rs_Dpsycho->RecordCount();
        $c=0;
        if(is_object($rs_Dpsycho)){
            $leading_reasons_nr = explode(",",$leading_reasons);
            $social_work_nr = explode(",",$social_work);
            $discharge_services_nr = explode(",",$discharge_services);
            while($result1 = $rs_Dpsycho->Fetchrow()){
            $check = '';
            $c++;
            $count--;
            if($result['id'] == 1){
                foreach($leading_reasons_nr as $value){
                    if($value == $result1['id']){
                        $check = 'checked';
                    }    
                }     
            }elseif($result['id'] == 2){
                foreach( $social_work_nr as $value){
                    if($value == $result1['id']){
                        $check = 'checked';
                    }    
                }                
            }elseif($result['id'] == 3){
                foreach($discharge_services_nr as $value){
                    if($value == $result1['id']){
                        $check = 'checked';
                    }    
                }                
            }
            
            $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result1['id'].'_'.$desc.'" name="'.$result1['id'].'_'.$desc.'" '.$check.'> '.ucwords($result1['desc']).'</td>';
     
            if($c==2 || $count==0){
                $psychoD .= '<tr>'.$td.'</tr>';
                $c=0;
                $td=''; 
            }    
            }
        }                                         
        
        $psychoTemp .= $psychoD.'</table> </td> </tr> </table><br/>';
        $psychoD = '';
     }
 }
 
$sql_case = 'SELECT * FROM seg_social_case_con ORDER BY id';
$rs_case = $db->Execute($sql_case);
$count = $rs_case->RecordCount();
$c=0;
 if(is_object($rs_case)){
    $case_con_nr = explode(",",$case_con);
    while($result = $rs_case->Fetchrow()){
        $check = '';
        $c++;
        $count--;
        foreach($case_con_nr as $value){
            if($value == $result['id']){
                $check = 'checked';
            }    
        } 
        $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_case" name="'.$result['id'].'_case" '.$check.'> '.ucwords($result['desc']).'</td>';
     
         if($c==2 || $count==0){
                $caseTemp .= '<tr>'.$td.'</tr>';
                $c=0;
                $td=''; 
         }       
    }
 }
 
$sql_followup = 'SELECT * FROM seg_social_followup_services ORDER BY id';
$rs_followup = $db->Execute($sql_followup);
$count = $rs_followup->RecordCount();
$c=0;
 if(is_object($rs_followup)){
    $follow_up_nr = explode(",",$follow_up);
    while($result = $rs_followup->Fetchrow()){
        $check = '';
        $c++;
        $count--;
        foreach($follow_up_nr as $value){
            if($value == $result['id']){
                $check = 'checked';
            }    
        }         
        $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_followup" name="'.$result['id'].'_followup" '.$check.'> '.ucwords($result['desc']).'</td>';
     
         if($c==2 || $count==0){
                $followupTemp .= '<tr>'.$td.'</tr>';
                $c=0;
                $td=''; 
         }       
    }
 }
 
$sql_coordination = 'SELECT * FROM seg_social_coordination ORDER BY id';
$rs_coordination = $db->Execute($sql_coordination);
$count = $rs_coordination->RecordCount();
$c=0;
 if(is_object($rs_coordination)){
    $coordination_nr = explode(",",$coordination);
    while($result = $rs_coordination->Fetchrow()){
        $check = '';
        $c++;
        $count--;
        foreach($coordination_nr as $value){
            if($value == $result['id']){
                $check = 'checked';
            }    
        }         
        if(strtoupper($result['desc'])=='OTHERS'){
            if($check=='checked')
                $display = "display:''";
            else
                $display = "display:none";
            $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_coordination" name="'.$result['id'].'_coordination" '.$check.' onclick="hidetext1(this.id)"> '.ucwords($result['desc']).' 
                             <input id="other_coordination" name="other_coordination" type="text" size="30" style="'.$display.'" value="'.$coordination_o.'"></td>';  
        }else{
            $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_coordination" name="'.$result['id'].'_coordination" '.$check.'> '.ucwords($result['desc']).'</td>';    
        }
         if($c==2 || $count==0){
                $coordinationTemp .= '<tr>'.$td.'</tr>';
                $c=0;
                $td=''; 
         }       
    }
 }
 
$sql_documentation = 'SELECT * FROM seg_social_documentation ORDER BY id';
$rs_documentation = $db->Execute($sql_documentation);
$count = $rs_documentation->RecordCount();
$c=0;
 if(is_object($rs_documentation)){
    $documentation_nr = explode(",",$documentation);
    while($result = $rs_documentation->Fetchrow()){
        $check = '';
        $c++;
        $count--;
        foreach($documentation_nr as $value){
            if($value == $result['id']){
                $check = 'checked';
            }    
        }
        if(strtoupper($result['desc'])=='OTHERS'){
            if($check=='checked')
                $display = "display:''";
            else
                $display = "display:none";
            $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_documentation" name="'.$result['id'].'_documentation" '.$check.' onclick="hidetext1(this.id)"> '.ucwords($result['desc']).' 
                             <input id="other_documentation" name="other_documentation" type="text" size="30" style="'.$display.'" value="'.$documentation_o.'"></td>';  
        }else{
            $td .= '<td width="300px" nowrap="nowrap"><input type="checkbox" id="'.$result['id'].'_documentation" name="'.$result['id'].'_documentation" '.$check.'> '.ucwords($result['desc']).'</td>';    
        }
        
        if($c==2 || $count==0){
                $documentationTemp .= '<tr>'.$td.'</tr>';
                $c=0;
                $td=''; 
         }       
    }
 }
 
$smarty->assign('planningTemp',$planningTemp);
$smarty->assign('referralTemp',$referralTemp); 
$smarty->assign('psychoTemp',$psychoTemp);
$smarty->assign('caseTemp',$caseTemp);
$smarty->assign('followupTemp',$followupTemp);
$smarty->assign('coordinationTemp',$coordinationTemp);
$smarty->assign('documentationTemp',$documentationTemp);
$smarty->assign('cremarks','<td width="*" nowrap="nowrap"  class="segInput"><input type="cremarks" id="cremarks" name="cremarks" value="'.$cremarks.'" size="100"></td>');     
$smarty->assign('case_submit','<button id="case_submit" name="case_submit" class="case_submit" type="submit" onclick="saveSocialCase();" style="margin-left: 4px; cursor: pointer; font:bold 12px Arial;">Save Case Management Services</button>');

$sTempx = '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">
           <input type="hidden" name="parent_enc" id="parent_enc" value="'.$parent_enc.'">
           <input type="hidden" name="pid" id="pid" value="'.$pid.'">
           <input type="hidden" name="mode" id="mode" value="'.$_GET['mode'].'">
           <input type="hidden" name="dep_list_id" id="dep_list_id" value=0>
           <input type="hidden" id="mssno" name="mssno" value="'.$patSS['mss_no'].'">
           <input class="segInput" id="submod" name="submod" type="text" size="30" value="'.$mod_desc.'" style="font:bold 12px Arial; display:none"/>
           <input type="hidden" id="date_format" name="date_format" value="'.$date_format.'">
           <input type="hidden" name="encoder_id" id="encoder_id" value="'.$HTTP_SESSION_VARS['sess_login_personell_nr'].'"> 
           <input type="hidden" id="autosave" name="autosave" value="0">
          ';
ob_start(); 
$smarty->assign('sHiddenInputs', $sTempx);

$smarty->assign('sHiddenInputsB',$jTemp);

$xTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sTailScripts', $xTemp);

$smarty->assign('sMainBlockIncludeFile','social_service/social_service_intake.tpl');

$smarty->display('common/mainframe.tpl');

?>