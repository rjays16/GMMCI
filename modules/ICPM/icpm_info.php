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
require_once($root_path.'include/care_api_classes/class_icpm.php');
$icpm_obj=new Icpm;

$phic = $_GET['phic'];
if ($phic)
 	$phic_caption = " For PHIC";

switch($retpath)
{
	case 'list': $breakfile='icpm_list.php'.URL_APPEND.'&phic='.$phic; break;
	case 'search': $breakfile='icpm_search.php'.URL_APPEND.'&phic='.$phic; break;
	default: $breakfile='icpm_manage.php'.URL_APPEND.'&phic='.$phic; 
}

#added by VAN 02-25-08
if (isset($_SESSION['code'])){
	$ref_code=$_SESSION['code'];	
}

unset($_SESSION['code']);
#echo "code = ".$code;

if(isset($ref_code)&&$ref_code&&($row=$icpm_obj->getIcpmInfo($ref_code, $phic))){
	$refcode=$row->FetchRow();
	$edit=true;
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
 $smarty->assign('sToolbarTitle',"$segICPM :: $segData $phic_caption");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_info.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segICPM :: $segData $phic_caption");

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
 	echo $LDICPMCodeSaved; //---edited from [echo "ICPM code has been successfully save." //$LDFirmInfoSaved], 10-25-2007, FDP---
?>
</b></font>
<?php 
} 
?>
<table border=0 cellpadding=4 >
  <tr class="wardlisttitlerow">
  	<td><b><?php echo $segICPMCode; ?></b></td>
  	<td><b><?php echo $LDdescriptionA; ?></b></td>
  </tr>
  <tr>
    <td align=left class="adm_input"></font><?php echo $refcode['code'] ?><br></td>
    <td class="adm_input"><?php echo $refcode['description'] ?><br></td>
  </tr> 
  <tr>
    <td><a href="icpm_update.php<?php echo URL_APPEND.'&retpath='.$retpath.'&ref_code='.$refcode['code'].'&phic='.$phic; ?>"><img <?php echo createLDImgSrc($root_path,'update.gif','0') ?> border="0"></a></td>
    <td  align=right><a href="icpm_list.php<?php echo URL_APPEND.'&phic='.$phic; ?>"><img <?php echo createLDImgSrc($root_path,'list_all.gif','0') ?> border="0"></a><a href="<?php echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a></td>
  </tr>
</table>
<p>

<form action="icpm_new.php?phic=<?=$_GET['phic']?>" method="post">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
<input type="hidden" name="phic" id="phic" value="<?php echo $_GET['phic']?>">

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