<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing_new/ajax/billing_new.common.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_acl.php');

//test comment

$lang_tables[]='search.php';
define('LANG_FILE','finance.php');

if ($_GET['area'] == 'DIALYSIS')
    $local_user = 'ck_dialysis_user';
else
    $local_user = 'aufnahme_user';

require_once($root_path.'include/inc_front_chain_lang.php');

if (isset($_GET["from"]))
    $from = $_GET["from"];
else
    $from = "";
if (($from == "") || (!isset($from)))
        $breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND."&userck=$userck";
else
        $breakfile = $from.".php".URL_APPEND;
$thisfile=basename(__FILE__);

//added by Nick 05-12-2014
$objAcl = new Acl($_SESSION['sess_temp_userid']);
$updateCaseTypePermission = $objAcl->checkPermissionRaw('_a_2_billUpdateCaseType');
//end Nick

# Start Smarty templating here
/**
* LOAD Smarty
*/
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('system_admin');

# Title in toolbar
if (!isset($_GET['area']))
    $smarty->assign('sToolbarTitle',"$LDBillingMain :: Process Promissory Note");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('billing-main.php')");

 # href for close button
 #edited by VAN 12-19-08
 if (($_GET['area']=='ER') || ($_GET['area'] == 'DIALYSIS'))
         $smarty->assign('breakfile','javascript:window.parent.cClick();');
 else
         $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBillingMain :: $LDListAll");

 # Buffer page output
 ob_start();
?>
<!-- prototype -->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>/js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<!-- include billing.css -->
<link rel="stylesheet" type="text/css" href="<?=$root_path?>modules/billing/css/billing.css" />

<!-- include Billing javascript -->
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>
<script type="text/javascript" language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript">var $j = jQuery.noConflict();</script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.numeric.js?t=<?= time() ?>"></script>
<script type="text/javascript" src="js/billing-promissory-note.js?t=<?=time();?>"></script>

<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
?>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);
$smarty->assign('sOnLoadJs',"onLoad=\"preset();\"");

$submitted = isset($_POST["submitted"]);

if($_GET['encounter_nr']){
    $encounter_nr = $_GET['encounter_nr'];

    $encObj = new Encounter();
    $enc_info = $encObj->getPatientEncInfo($encounter_nr);

    if($enc_info){
        $pid = $enc_info['pid'];
        $age = $enc_info['age'];
        $name_patient = $enc_info['name_last'].", ".$enc_info['name_first'];
        $patient_type = $enc_info['patient_type'];

        if ($enc_info['street_name']){
            if ($enc_info['brgy_name']!="NOT PROVIDED")
                    $street_name = $enc_info['street_name'].", ";
            else
                    $street_name = $enc_info['street_name'].", ";
        }

        if ((!($enc_info['brgy_name'])) || ($enc_info['brgy_name']=="NOT PROVIDED"))
                $brgy_name = "";
        else
                $brgy_name  = $enc_info['brgy_name'].", ";

        if ((!($enc_info['mun_name'])) || ($enc_info['mun_name']=="NOT PROVIDED"))
                $mun_name = "";
        else{
                if ($brgy_name)
                        $mun_name = $enc_info['mun_name'];
        }

        if ((!($enc_info['prov_name'])) || ($enc_info['prov_name']=="NOT PROVIDED"))
                $prov_name = "";

        if(stristr(trim($mun_name), 'city') === FALSE){
                if ((!empty($mun_name))&&(!empty($prov_name))){
                        if ($enc_info['prov_name']!="NOT PROVIDED")
                                $prov_name = ", ".trim($enc_info['prov_name']);
                        else
                                $prov_name = "";
                }else{
                        $prov_name = "";
                }
        }else
                $prov_name = " ";

        $address = $street_name.$brgy_name.$mun_name.$prov_name;
    }
}

$smarty->assign('sProgBar','<img src="'.$root_path.'/images/ajax_bar.gif" border=0>');

//PID
$smarty->assign('sPid','<input class="segInput" id="pid" name="pid" type="text" size="15" value="'.$pid.'" style="font:bold 12px Arial; float;left;" readOnly >');

//Patient Name
$smarty->assign('sPatientName','<input class="segInput" id="pname" name="pname" type="text" size="28" value="'.mb_strtoupper($name_patient).'" style="font:bold 12px Arial; float;left;" readOnly >');

//Patient Address
$smarty->assign('sPatientAddress','<textarea class="segInput" id="paddress" name="paddress" cols="29" rows="2" style="font:bold 12px Arial" readOnly>'.mb_strtoupper($address).'</textarea>');

$smarty->assign('sEncounter','<input class="segInput" id="encounter_nr" name="encounter_nr" type="text" size="16" value="'.$encounter_nr.'" style="font:bold 12px Arial; float;left;" readOnly >');


//Bill Date
$smarty->assign('sDate', '<input class="segInput" id="billdate_promi" readOnly name="billdate" type="text" size="16" style="font:bold 12px Arial; float;left;" >');

//Encounter date (not admission date).
$smarty->assign('sAdmissionDate','<input class="segInput" id="admission_date" name="admission_date" type="text" size="16"  value="'.$enc_date.'" style="font:bold 12px Arial" readOnly>');

// Last bill date.
$smarty->assign('sLastBillDate','<input class="segInput" id="lastbill_date" name="lastbill_date" type="text" size="16"  value="" style="font:bold 12px Arial" readOnly>');

//Admission date.
$smarty->assign('sAdmitDate','<input class="segInput" id="date_admitted" name="date_admitted" type="text" size="16"  value="'.$admission_date.'" style="font:bold 12px Arial" readOnly>');

//Age
$smarty->assign('sAge','<input class="segInput" id="age" name="age" type="text" size="16" style="font:bold 12px Arial" value="'.$age.'" readOnly>');

//Days Admitted
$smarty->assign('sAdmDays','<input class="segInput" id="days_admitted" name="days_admitted" type="text" size="16"  value="" style="font:bold 12px Arial" readOnly>');

//Confinement Type
$smarty->assign('sConfineType','<input class="segInput" id="confine_type" name="confine_type" type="text" size="16"  value="'.$patient_type.'" style="font:bold 12px Arial" readOnly>');

//Type of Payment
$smarty->assign('sTypeofPayment', '<input type = "radio" name="type_of_payment" id="sum_pay" style="margin-left:10px" class="jedInput"/>
    in one sum payable <br>
    <input type = "radio" name="type_of_payment" id="install_pay" style="margin-left:10px" class="jedInput"/> in installment
    ');

//Confinement Type
$smarty->assign('sTotalBill','<input class="segInput" id="total_bill" name="total_bill" type="text" size="16"  value="" style="font:bold 12px Arial" readOnly>');

//Amount
$smarty->assign('sAmount','<input class="segInput" id="amount" name="amount" type="text" size="32"  value="" style="font:bold 12px Arial">');

//Name of Guarantor
$smarty->assign('sNameGuarantor','<input class="segInput" id="name_g" name="name_g" type="text" size="32"  value="" style="font:bold 12px Arial">');

//Address of Guarantor
$smarty->assign('sAddressGuarantor','<input class="segInput" id="address_g" name="address_g" type="text" size="32"  value="" style="font:bold 12px Arial">');

//Relationship to Patient
$smarty->assign('sReltoPatient','<input class="segInput" id="rel_g" name="rel_g" type="text" size="32"  value="" style="font:bold 12px Arial">');

//Remarks
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="29" rows="2" style="font:bold 12px Arial"></textarea>');

//Due Date
$dbtime_format = "Y-m-d";
$fulltime_format = "F j, Y";

$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);

$jsCalScript = "
            <script type=\"text/javascript\">
                Calendar.setup ({
                    displayArea : \"show_duedate\",
                    inputField : \"duedate\",
                    ifFormat : \"%Y-%m-%d\",
                    daFormat : \"   %B %e, %Y\",
                    showsTime : true,
                    button : \"orderdate_trigger\",
                    singleClick : true,
                    step : 1
                });
            </script>";

$smarty->assign('sDueDate','<span id="show_duedate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.$curDate_show.'</span>
        <input class="jedInput" name="duedate" id="duedate" type="hidden" value="'.$curDate.'">');

$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">'.$jsCalScript);

//Buttons
$smarty->assign('sBtnSave', '<button onclick="checkFields();" aria-disabled="false" role="button" id="btnSave" name="btnSave" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial;"><span class="ui-button-icon-primary ui-icon ui-icon-disk"></span><span id = "spanBtnSave"class="ui-button-text">
                                        Save</span></button>');

$smarty->assign('sBtnPrint', '<button onclick="printPromi();" aria-disabled="false" role="button" id="btnPrint" name="btnPrint" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" type="submit" style="height: 30px; cursor: pointer; font:bold 12px Arial;"><span class="ui-button-icon-primary ui-icon ui-icon-disk"></span><span class="ui-button-text">
                                        Print</span></button>');

ob_start();
?>

<input type="hidden" id="admission_dte" name="admission_dte" value="<?= (($encounter_type==3)||($encounter_type==4)) ? ((isset($_GET['bill_fromdate']))? $_GET['bill_fromdate'] : $admission_dt) :((isset($_GET['bill_fromdate']))? $_GET['bill_fromdate'] : $encounter_date); ?>" />

<input type="hidden" id="enc" name="enc" value="<?= $encounter_nr ?>" />

<input type="hidden" id="mode_of_promi" name="mode_of_promi"/>

<input type="hidden" id="submitted" name="submitted" value="1"/>

<input type="hidden" id="refno_promi" name="refno_promi"/>

<?php
$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sTailScripts', $stemp);


ob_start();
?>

<?php

$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sSaveinputs', $stemp);
# Assign page output to the mainframe template

//$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->assign('sMainBlockIncludeFile','billing_new/billing-promisory-note.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>