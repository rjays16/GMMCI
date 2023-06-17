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
require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

switch($retpath)
{
	case 'list': $breakfile='ethnic_list.php'.URL_APPEND.'&target=ethnic'; break;
	case 'search': $breakfile='ethnic_search.php'.URL_APPEND.'&target=ethnic'; break;
	default: $breakfile='edv-system_manage.php'.URL_APPEND.'&target=ethnic'; 
}


$nr = $_GET['ethnic_nr'];

if(isset($nr)&&$nr&&($row=$person_obj->getEthnicInfo($nr))){
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
 $smarty->assign('sToolbarTitle',"Ethnic Group :: $segData");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('icd10_info.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Ethnic Group :: $segData");

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
 #	echo $LDICPMCodeSaved; //---edited from [echo "ICPM code has been successfully save." //$LDFirmInfoSaved], 10-25-2007, FDP---
	echo "Ethnic group has been succesfully saved.";
?>
</b></font>
<?php 
} 
?>
<table border=0 cellpadding=4 width="40%" >
  <tr class="wardlisttitlerow">
  	<td width="10%"><b>Ethnic</b></td>
  </tr>
  <tr>
    <td class="adm_input"><?php echo $refcode['name'] ?><br></td>
  </tr> 
  <tr>
    <td  align=center><a href="ethnic_update.php<?php echo URL_APPEND.'&retpath='.$retpath.'&ethnic_nr='.$refcode['nr']; ?>"><img <?php echo createLDImgSrc($root_path,'update.gif','0') ?> border="0"></a>&nbsp;&nbsp;<a href="ethnic_list.php<?php echo URL_APPEND; ?>"><img <?php echo createLDImgSrc($root_path,'list_all.gif','0') ?> border="0"></a><a href="<?php echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a></td>
  </tr>
</table>
<p>

<form action="ethnic_new.php" method="post">
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