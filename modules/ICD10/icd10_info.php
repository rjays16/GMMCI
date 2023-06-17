<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','icd10icpm.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Load the insurance object
require_once($root_path.'include/care_api_classes/class_icd10.php');
$icd_obj=new Icd;

switch($retpath)
{
	case 'list': $breakfile='icd10_list.php'.URL_APPEND; break;
	case 'search': $breakfile='icd10_search.php'.URL_APPEND; break;
	default: $breakfile='icd10_manage.php'.URL_APPEND;
}

if (isset($_SESSION['diagnosis_code'])){
	$diagnosis_code=$_SESSION['diagnosis_code'];
}
unset($_SESSION['diagnosis_code']);

#echo "HTTP_POST_VARS['diagnosis_code']=".$HTTP_POST_VARS['diagnosis_code'];
#echo "<br>HTTP_session['diagnosis_code']=".$_SESSION['diagnosis_code'];
#echo "<br>code=".$diagnosis_code;



if(strlen($diagnosis_code)== 11){
		$icdcodeA = substr($diagnosis_code,0,5);
		$icdcodeB = substr($diagnosis_code,6,10);
		#edited by VAN 02-25-08
		#$diagnosis_code1 = $icdcodeA.",".$icdcodeB;
		$diagnosis_code1 = $icdcodeA."-".$icdcodeB;
}else{
	$diagnosis_code1 = $diagnosis_code;
}
/*---removed, 10-25-2007, FDP---
echo "diagnosis_code=".$diagnosis_code1;
echo "row=".$row=$icd_obj->getIcd10Info($diagnosis_code);
---until here only------FDP-----*/
#echo "diagnosis_code=".$diagnosis_code1;
#echo "row=".$row=$icd_obj->getIcd10Info($diagnosis_code);

#edited by VAN 02-25-08
#if(isset($diagnosis_code)&&$diagnosis_code&&($row=$icd_obj->getIcd10Info($diagnosis_code1))){
if(isset($diagnosis_code)&&$diagnosis_code&&($row=$icd_obj->getIcd10Info($diagnosis_code1))){

	$refcode=$row->FetchRow();
	$edit=true;

	if(strlen($refcode['diagnosis_code'])== 11){
		$icdcodeA = substr($refcode['diagnosis_code'],0,5);
		$icdcodeB = substr($refcode['diagnosis_code'],6,10);
		$diagnosis_code = $icdcodeA."-".$icdcodeB;
	}else{
		$diagnosis_code = $refcode['diagnosis_code'];
	}

}else{
	#redirect to search mode
}

$bgc=$root_path.'gui/img/skin/default/tableHeaderbg3.gif';
$bgc2='#eeeeee';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDicd10 :: $LDData");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_info.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDicd10 :: $LDData");

# Colllect javascript code

ob_start();

?>

 <ul>
<?php
if(isset($save_ok)&&$save_ok){
?>
<img <?php echo createMascot($root_path,'mascot1_r.gif','0','absmiddle') ?>><font class="prompt" face="Verdana, Arial" size=3>
<b>
<?php
 	echo $LDICDCodeSaved; //---edited from [echo "ICD code has been successfully save." //$LDFirmInfoSaved], 10-25-2007, FDP---
?>
</b></font>
<?php
}
?>
<table border=0 cellpadding=4 >
  <tr class="wardlisttitlerow">
  	<td><b><?php echo $LDicd10Code; ?></b></td>
  	<td><b><?php echo $LDdescriptionA; ?></b></td>
  </tr>
  <tr>
    <td align=left class="adm_input"></font><?php echo $diagnosis_code//echo $refcode['diagnosis_code'] ?><br></td>
    <td class="adm_input"><?php echo $refcode['description'] ?><br></td>
  </tr>
  <tr>
    <td><a href="icd10_update.php<?php echo URL_APPEND.'&retpath='.$retpath.'&diagnosis_code='.urlencode($diagnosis_code);//$refcode['diagnosis_code']; ?>"><img <?php echo createLDImgSrc($root_path,'update.gif','0') ?> border="0"></a></td>
    <td  align=right><a href="icd10_list.php<?php echo URL_APPEND; ?>"><img <?php echo createLDImgSrc($root_path,'list_all.gif','0') ?> border="0"></a><a href="<?php echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a></td>
  </tr>
</table>
<p>
<form action="icd10_new.php" method="post">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
<input type="submit" value="<?php echo $LDNeedEmptyFormPls ?>">
</form>

</ul>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>