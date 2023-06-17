<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/laboratory/ajax/lab-admin.common.php");
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_lab_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title=$LDLab;
$breakfile=$root_path."modules/laboratory/seg-close-window.php".URL_APPEND."&userck=$userck";
#$imgpath=$root_path."pharma/img/";
                            
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
 $smarty->assign('sToolbarTitle',"Laboratory::Parameter Manager (Add/Edit)");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Laboratory::Parameter Manager (Add/Edit)");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

$smarty->assign('sOnLoadJs','onLoad=""');    
$param_id = $_GET['param_id'];
$service_code = $_GET['service_code'];

$status = $_POST["status"];
if($status)
{
    $param_id = $_POST['param_id'];
    $service_code = $_POST['service_code'];
    $dtype = $_POST['data_type'];
    $isnum=0;
    $isbool=0;
    $islongtext=0;
    if($dtype==1)
        $isnum=1;
    else if($dtype==2)
        $isbool=1;
    else if($dtype==3)
        $islongtext=1;
    $g = $_POST['gender'];
    $ismale=0;
    $isfemale=0;
    if($g==2)
    {
        $ismale=1;
        $isfemale=1;
    }
    else if($g==1)
        $isfemale=1;
    else if($g==0)
        $ismale=1;
    $data = array("name"=>$_POST['name'], "is_numeric"=>$isnum, "is_boolean"=>$isbool, "is_longtext"=>$islongtext, "order_nr"=>$_POST['order_nr'], "SI_unit"=>$_POST['si_unit'], "SI_lo_normal"=>$_POST['si_lo'], "SI_hi_normal"=>$_POST['si_hi'], "CU_unit"=>$_POST['cu_unit'], "CU_lo_normal"=>$_POST['cu_lo'], "CU_hi_normal"=>$_POST['cu_hi'], "is_male"=>$ismale, "is_female"=>$isfemale);
    if($status=="edit")
    {
        $srvObj->updateParameter($param_id, $data);
        echo "<script type='text/javascript'>window.parent.location = 'labor_test_params.php?popUp=1&service_code=$service_code';</script>";
    }
    else if($status=="add")
    {
        $srvObj->addParameter($service_code, $data);
        echo "<script type='text/javascript'>window.parent.location = 'labor_test_params.php?popUp=1&service_code=$service_code';</script>";
    }
}

$status = "add";
if($result = $srvObj->GetParameterData($param_id))
{
    $code = $result["param_id"];
    $name = $result["name"];
    if($result["is_numeric"]=="1")
        $data_type = "numeric";
    else if($result["is_boolean"]=="1")
        $data_type = "boolean";
    else if($result["is_longtext"]=="1")
        $data_type = "longtext";
    else
        $data_type = "text";
    $order_nr = $result["order_nr"];
    if($result["is_male"]=="1")
    {
        if($result["is_female"]=="1")
            $gender = "both";
        else
            $gender = "male";
    }
    else
        $gender = "female";
    $si_lo = $result["SI_lo_normal"];
    $si_hi = $result["SI_hi_normal"];
    $si_unit = $result["SI_unit"];
    $cu_lo = $result["CU_lo_normal"];
    $cu_hi = $result["CU_hi_normal"];
    $cu_unit = $result["CU_unit"];
    $status = "edit";
}

 
 ob_start();
 
 

?>
<script language="javascript" >
<!--
 

function insertRow(id, groupName, groupOtherName){

}

function validateForm(stat){
    //alert(stat);
    document.inputgroupform.submit();
}

function clearText(){
    

}

function resetForm(){

}

/*
function refreshWindow(){
    /*var grpname = $('gname').value;
    var mode = $('mode').value;
    //Service Group ".strtoupper(stripslashes($_POST['gname']))." is successfully updated!
    
    if (mode == 'save')
        alert('Service Group '+grpname+' is successfully created!');
    else
        alert('Service Group '+grpname+' is successfully updated!');    
    
    //alert('insertLabGroup');
    //window.parent.location.href=window.parent.location.href;
    //alert(window.location);
    //window.parent.location.reload();
}*/

-->
</script> 
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<!--<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform" onSubmit="return validateform();">-->
<form ENCTYPE="multipart/form-data" action="<?=$thisfile?>" method="POST" name="inputgroupform" id="inputgroupform">
    <div style="background-color:#e5e5e5; color: #2d2d2d; overflow:hidden;">
    <table border="0" width="100%" cellspacing="2" cellpadding="2" style="margin:0.7%; font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; overflow-y:hidden">
        <tbody>
            <tr>
                <td>Name</td>
                <td>
                    <input type="hidden" name="param_id" id="param_id" size="25" value="<?= $code ?>" >
                    <input type="text" name="name" id="name" size="25" value="<?= $name ?>" >
                </td>
            </tr>
            <tr>
                <td>Data type</td>
                <td><select name="data_type" id="data_type">
                <option value=4 <? if($data_type=="text") echo "selected='selected'";?>>Text</option>
                <option value=3 <? if($data_type=="longtext") echo "selected='selected'";?>>Long Text</option>
                <option value=1 <? if($data_type=="numeric") echo "selected='selected'";?>>Numeric</option>
                <option value=2 <? if($data_type=="boolean") echo "selected='selected'";?>>Boolean</option>
                </select></td>
            </tr>
            <tr>
                <td>Order Number</td>
                <td><input type="text" name="order_nr" id="order_nr" size="3" value="<?= $order_nr?>"></td>
            </tr>
            <tr>
                <td>Gender</td>
                <td><select name="gender" id="gender">
                <option value=2 <? if($gender=="both") echo "selected='selected'";?>>Both</option>
                <option value=0 <? if($gender=="male") echo "selected='selected'";?>>Male</option>
                <option value=1 <? if($gender=="female") echo "selected='selected'";?>>Female</option>
                </select></td>
            </tr>
            <tr>
                <td>SI Range</td>
                <td><input type="text" name="si_lo" id="si_lo" size="3" value="<?= $si_lo?>">-
                <input type="text" name="si_hi" id="si_hi" size="3" value="<?= $si_hi?>">
                <input type="text" name="si_unit" id="si_unit" size="4" value="<?= $si_unit?>"></td>
            </tr>
            <tr>
                <td>CU Range</td>
                <td><input type="text" name="cu_lo" id="cu_lo" size="3" value="<?= $cu_lo?>">-
                <input type="text" name="cu_hi" id="cu_hi" size="3" value="<?= $cu_hi?>">
                <input type="text" name="cu_unit" id="cu_unit" size="4" value="<?= $cu_unit?>"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php if($status=="edit"){?>
                        <img id="save" name="save" src="../../gui/img/control/default/en/en_update.gif" border=0 alt="Update" title="Update" style="cursor:pointer" onclick="javascript:validateForm('edit');">
                    <?php }else{ ?>
                        <img id="save" name="save" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 alt="Save" title="Save" style="cursor:pointer" onclick="javascript:validateForm('add');">
                    <?php } ?>
                    &nbsp;
                    <img id="cancel" name="cancel" src="../../gui/img/control/default/en/en_cancel.gif" border=0 alt="Cancel" onclick="javascript:window.parent.cClick();" title="Cancel" style="cursor:pointer">
                    &nbsp;&nbsp;
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <input type="hidden" name="sid" value="<?php echo $sid?>">
    <input type="hidden" name="lang" value="<?php echo $lang?>">
    <input type="hidden" name="cat" value="<?php echo $cat?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
    <input type="hidden" name="status" value="<?php echo $status ?>">
    <input type="hidden" name="service_code" value="<?php echo $service_code ?>">
</form>
<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
    /**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
    include_once($root_path.'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common',FALSE,FALSE,FALSE);
    
    # Set a flag to display this page as standalone
    $bShowThisForm=TRUE;
}

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
