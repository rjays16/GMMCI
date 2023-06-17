<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    
    require($root_path.'include/inc_environment_global.php');
    
    require($root_path."modules/laboratory/ajax/lab-new.common.php");
    $xajax->printJavascript($root_path.'classes/xajax');


define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
$breakfile = "labor.php";

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

  $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");

 
global $allow_ipdcancel, $allow_opdcancel, $allow_ercancel;

require_once($root_path.'include/care_api_classes/class_encounter.php');
$encObj=new Encounter();

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;    

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj = new Personell;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$hosp_obj = new Hospital_Admin;

$encounter_nr = $_GET['encounter_nr'];
$referral_nr = $_GET['referral_nr'];
$pid = $_GET['pid'];
$result = $encObj->SearchAdmissionList($pid, $referral_nr);
$personInfo = $result->FetchRow();
$dept_nr = $personInfo["referrer_dept"];
$is_dept = $personInfo["is_dept"];
if($is_dept==1){
    $tmp = $dept_obj->getDeptAllInfo($dept_nr);
    $dept_name = $tmp["name_formal"];
    $dpt = "dept";
}
else{
    $tmp = $hosp_obj->getOtherHospitalInfo($dept_nr);
    $dept_name = $tmp["hosp_name"];
    $dpt ="hosp";
}
$doctor = $personInfo['referrer_dr'];
$doctorinfo = $pers_obj->get_Person_name($doctor);
$middleInitial = "";
if (trim($doctorinfo['name_middle'])!=""){
    $thisMI=split(" ",$doctorinfo['name_middle']);    
    foreach($thisMI as $value){
        if (!trim($value)=="")
        $middleInitial .= $value[0];
    }
    if (trim($middleInitial)!="")
    $middleInitial .= ".";
}
if($personInfo['is_referral']==0)
{
    $status="Transfer";
}
else
{
    $status="Refer";
}

$doctor_name = $doctorinfo['name_first']." ".$doctorinfo['name_2']." ".$middleInitial." ".$doctorinfo['name_last'];
$doctor_name = ucwords(strtolower($doctor_name));
$doctor_name = htmlspecialchars($doctor_name);

# Collect javascript code
 ob_start()

?>

<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function preSet(){
    document.getElementById('search').focus();
}


//---------------adde by VAN 02-06-08

//------------------------------------------

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";
  
function refreshWindow(){
    //window.location.href=window.location.href;
}
                            
function ReloadWindow(){
    window.location.href=window.location.href;
}


//------------------------------------------
// -->
</script>

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



<?php

$sTemp = ob_get_contents();
ob_end_clean();
//$smarty->append('JavaScript',$sTemp);

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
$hclabObj = new HCLAB;

ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="seg-patient-admission.php?is_dept=<?= $dpt ?>" method="post" name="suchform" onSubmit="">
    <div id="tabFpanel">
        <table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
            <tr>
                <td class="segPanelHeader" align="left" colspan="2"> Admission Details </td>
            </tr>
            <tr>
                <td width="35%" class="segPanel"><strong>Referral Number</strong></td>
                <td class="segPanel"><?=$referral_nr?></td>
            </tr>
            <tr>
                <td width="35%" class="segPanel"><strong>Referral Type</strong></td>
                <td class="segPanel"><?=$status?></td>
            </tr>
            <tr>
                <td class="segPanel"><strong>Referrer Diagnosis</strong></td>
                <td class="segPanel"><?=$personInfo["referrer_diagnosis"]?></td>
            </tr>
            <tr>
                <td class="segPanel"><strong>Referring Doctor</strong></td>
                <td class="segPanel"><?=$doctor_name?></td>
            </tr>
            <tr>
                <td class="segPanel"><strong>Department</strong></td>
                <td class="segPanel"><?=$dept_name?></td>
            </tr>
            <tr>
                <td class="segPanel"><strong>Notes</strong></td>
                <td class="segPanel"><?=$personInfo["referrer_notes"]?></td>
            </tr>
        </table>
    </div>
</div>
    <input type="hidden" name="encounter_nr" value="<?php echo $encounter_nr ?>">
    <input type="hidden" name="status" id="status" value="edit">
    <input type="hidden" name="refno" value="<?php echo $referral_nr ?>">
    <input type="hidden" name="transaction_type" value="<?php echo $status ?>">
    <input type="hidden" name="doctor" value="<?php echo $doctor ?>">
    <input type="hidden" name="dept" value="<?php echo $personInfo["referrer_dept"] ?>">
    <input type="hidden" name="diagnosis" value="<?php echo $personInfo["referrer_diagnosis"] ?>">
    <input type="hidden" name="notes" value="<?php echo $personInfo["referrer_notes"] ?>">
    <input type="hidden" name="date" value="<?php echo date("m/d/Y",strtotime($personInfo["create_time"]));?>">
    <input type="hidden" name="reason" id="reason" value="">
<?php
    if(!$encObj->BillingDone($encounter_nr))
    {
        echo "<input type=image name=submit value='update' src='../../images/his_editbtn.gif'>";
    }
    if($allow_ipdcancel || $allow_opdcancel || $allow_ercancel)
    {
        echo "<script type='text/javascript'>function delete(){ var where_to= confirm('Do you really want to cancel admission?');
         if (where_to== true)
         {
            var answer = prompt ('Reason:','');
            var x = document.getElementById('reason');
            x.value = answer;
            var y = document.getElementById('status');
            y.value = 'cancel';
            return true;
         } 
         else
         {return false;}}</script>";
        echo "<input type=image name=submit value='cancel' src='../../images/his_cancel_button.gif' onclick='javascript: return delete();'>";
    }
?>
</form>
<br />
<hr>


<form action="<?php echo $breakfile?>" method="post">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="done" id="done" value="<?=$_GET['done']?>" />

<!-- added by VAN 07-28-08 -->
<input type="hidden" name="is_doctor" id="is_doctor" value="<?=($is_doctor)?1:0?>">
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$encounter_nr?>">

</form>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
