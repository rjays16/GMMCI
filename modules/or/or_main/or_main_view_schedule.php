<?php
  //created by cha 06-10-09

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

$local_user='ck_op_pflegelogbuch_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$title='OR Main';
if (!$_GET['from'])
  $breakfile=$root_path."main/op-doku.php".URL_APPEND."&userck=$userck";
else {
  if ($_GET['from']=='CLOSE_WINDOW')
    $breakfile = "javascript:window.parent.cClick();";
  else
    $breakfile = $root_path.'modules/or/request/op_request_pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$thisfile='or_main_view_schedule.php';
$returnfile=$root_path.'main/op-doku.php'.URL_APPEND.'&userck=$userck';
//ajax
require_once($root_path."modules/or/ajax/op_view_schedule.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');


 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 $smarty->assign('sToolbarTitle',"OR Main::List of Schedules");
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");
 $smarty->assign('breakfile',$breakfile);
 $smarty->assign('sWindowTitle',"OR Main::List of Schedules");
 $smarty->assign('sOnLoadJs','onLoad=""');

/*$surgBuffer='<a href="or_main_view_schedule.php'.URL_APPEND.'&target=surgeon">SURGEONS</a>';
$asurgBuffer='<a href="or_main_view_schedule.php'.URL_APPEND.'&target=asstsurgeon">ASST SURGEONS</a>';
$anBuffer='<a href="or_main_view_schedule.php'.URL_APPEND.'&target=anesth">ANESTHESIOLOGISTS</a>';
$nurBuffer='<a href="or_main_view_schedule.php'.URL_APPEND.'&target=nurse">NURSES</a>'; 
 $smarty->assign('resSurgeons',$surgBuffer);
 $smarty->assign('asstSurgeons',asurgBuffer); 
 $smarty->assign('Anesths',$anBuffer); 
 $smarty->assign('Nurses',$nurBuffer); 
 $smarty->assign('sHSpacer','<img src="'.$root_path.'gui/img/common/default/pixel.gif" height=1 width=25>');
 $smarty->assign('bShowTabs',TRUE);  */
 # Collect javascript code
 ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/op-view-schedule.js?t=<?=time()?>"></script>   

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

<?
 $sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');
$smarty->assign('sHiddenInputs',$sTemp);

//test codes
/*global $db;
$sql='select cp.operator,cp.assistant,cp.scrub_nurse,cp.rotating_nurse,cp.an_doctor,cp.op_therapy from care_encounter_op as cp,seg_ops_personell as sp where cp.nr=sp.refno';
$result=$db->Execute($sql); 
while($row=$result->FetchRow())
{
   echo "".unserialize($row['operator'])." ".unserialize($row['assistant'])." ".unserialize($row['scrub_nurse'])." ".unserialize($row['rotating_nurse'])." ".unserialize($row['an_doctor'])." ".unserialize($row['op_therapy'])."<br>";
}  */


# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','or/or_main_view_schedule.tpl'); 

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>