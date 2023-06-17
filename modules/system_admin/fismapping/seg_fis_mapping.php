<?php
//created by cha August 12, 2010

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/system_admin/fismapping/ajax/fis_mapping.common.php');

define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'modules/system_admin/edv.php'.URL_APPEND;
$returnfile=$root_path.'modules/system_admin/edv.php'.URL_APPEND;

$thisfile=basename(__FILE__);

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');


$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg_fis_mapping.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path.'include/care_api_classes/class_seg_fis_mapping.php');
$objfis = new FisMapping();
# Toolbar title
$smarty->assign('sToolbarTitle','Account Mapping');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# Window bar title
$smarty->assign('sWindowTitle',"Request Cancellation");
$smarty->assign('breakFile',$breakfile);

ob_start();
?>
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

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<script type="text/javascript" src="js/fis_mapping_request.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="js/md5.js"></script>
<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('form_end','</form>');



$Account = $objfis->GetAccountlist();
$total = $objfis->FoundRows();
for ($i=0; $i < $total ; $i++) { 
    $accountOption .="<option value =".$Account[$i][0].">".$Account[$i][1]."</option>";
}
 
$smarty->assign('accountTransaction', '<select class="segInput" id="accountTransaction" name="accountTransaction"  onchange="ChangeTransaction();" style="width:60%">
                                         <option value="0">-Select an area-</option>'.
                                         $accountOption.'
                                        </select>');

$smarty->assign('serviceArea', '<select class="segInput" id="service_area" name="service_area" style="width:60%" onchange="$(\'selpatient\').disabled=false;$(\'name\').disabled=false;">
                    <option value="0">-Select an area-</option>
                    <option value="LD">Laboratory</option>
                    <option value="RD">Radiology</option>
                    <option value="PH">Pharmacy</option>
                    <option value="OT">Miscellaneous</option>
                    </select>');

$smarty->assign('search_btn', '<button class="segButton" id="search" name="search" onclick="checklist(); return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');
$smarty->assign('AddAccount_btn', '<button class="segButton" id="AddAccount" name="AddAccount" onclick="addaccount(); return false;" style="cursor:pointer;display:none;"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Add Account</button>');


$smarty->assign('pSearchName', '<input type="text" style="width:40%" id="name" class="segInput" />');
$smarty->assign('pSearchId', '<input type="text" style="width:40%" id="pid" class="segInput"/>');
$smarty->assign('pSearchEnc', '<input type="text" style="width:40%" id="encounter_nr" class="segInput"/>');;
$smarty->assign('rootpath', $root_path);
ob_start();
?>

<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" id="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" id="encoder" value="<?php echo  str_replace(" ","+",$_COOKIE[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<?
$sTemp = ob_get_contents();
$sTable = ob_get_contents();
ob_end_clean();
$smarty->assign('sTable',$sTable);
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('sMainBlockIncludeFile','system_admin/fis_mapping.tpl');
$smarty->display('common/mainframe.tpl');

?>