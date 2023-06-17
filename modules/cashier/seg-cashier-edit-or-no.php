<?php
//created by cha 05-20-09

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','cashier.php');
$local_user='ck_cashier_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$title='Cashier';
if (!$_GET['from'])
  $breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND."&userck=$userck";
else {
  if ($_GET['from']=='CLOSE_WINDOW')
    $breakfile = "javascript:window.parent.cClick();";
  else
    $breakfile = $root_path.'modules/cashier/cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-cashier-edit-or-no.php';
$returnfile=$root_path.'modules/cashier/seg-cashier-functions.php'.URL_APPEND.'&userck=$userck';
//ajax
require_once($root_path."modules/cashier/ajax/seg-cashier-edit-or.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::OR Editing");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::OR Editing");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/cashier-edit-or.js?t=<?=time()?>"></script>  

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

$smarty->assign('sFromOrNo',"<b>OR From </b><input class=\"segInput\" type=\"text\" id=\"fromOrNo\" size=\"15\" />");
$smarty->assign('sToOrNo',"<b>OR To </b><input class=\"segInput\" type=\"text\" id=\"toOrNo\" size=\"15\" />");


ob_start();
$sTemp='';
$sTable=''; 
?>
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
  <input type="hidden" name="key" id="key">

<div style="width:70%; padding:1px;">
  <table width="100%">
    <tr>
      <td width="50%">
        <img class="segSimulatedLink" id="save" name="save" type="image" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 width="72" height="23"  alt="Save data" align="absmiddle"  onclick="var x=checkInput(this.id); if(x){startAJAXSave(this.id);} return false;">
        <a href="<?= $returnfile ?>"><img class="segSimulatedLink" src="../../gui/img/control/default/en/en_cancel.gif"border=0 width="72" height="23" alt="Cancel" align="absmiddle"></a>
      </td>
      <td align="right"><img class="segSimulatedLink" id="genNewOR" name="genNewOR" src="../../gui/img/control/default/en/en_gen_or.gif" border="0" alt="Generate data" align="absmiddle"  onclick="var x=checkInput(this.id); if(x){var y=getNewOR(); if(y){startAJAXGenerate(this.id);}} return false;" /></td>
    </tr>
  </table>
</div>
<div style="display:block; width:70%;">
<table id="ORList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th width="12%" align="center">OR No</th>
        <th width="16%" align="center">Date</th>
        <th width="15%" align="center">Encoder</th>
        <th width="28%" align="center">Payor Name</th>
        <th width="10%" align="center">Status</th>
        <th width="*" align="center"></th>
      </tr>
    </thead>
    <tbody id="ORList-body">
        <tr><td colspan="7" style="">No OR series selected yet..</td></tr>
    </tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>

<div style="width:70%; padding:1px;">
  <table width="100%">
    <tr>
      <td width="50%">
        <img class="segSimulatedLink" id="save" name="save" type="image" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 width="72" height="23"  alt="Save data" align="absmiddle"  onclick="var x=checkInput(this.id); if(x){startAJAXSave(this.id);} return false;">
        <a href="<?= $returnfile ?>"><img class="segSimulatedLink" src="../../gui/img/control/default/en/en_cancel.gif"border=0 width="72" height="23" alt="Cancel" align="absmiddle"></a>
      </td>
    </tr>
  </table>
</div>

<?php
 $sTable = ob_get_contents();
$sTemp = ob_get_contents();
ob_end_clean();
 $smarty->assign('sTable',$sTable);
 $smarty->assign('sHiddenInputs',$sTemp);
 $smarty->assign('saveButton','<input id="save" name="save" type="image" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 width="72" height="23"  alt="Save data" align="absmiddle"  onclick="var x=checkInput(this.id); if(x){startAJAXSave(this.id);} return false;">  &nbsp;&nbsp;');
 $smarty->assign('cancelButton','<a href ="'.$returnfile.'"><img src="../../gui/img/control/default/en/en_cancel.gif"border=0 width="72" height="23" alt="Cancel"   align="absmiddle"></a>');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/edit-or-mainblock.tpl'); 

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>