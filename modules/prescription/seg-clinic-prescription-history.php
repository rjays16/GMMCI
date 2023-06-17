<?php
//created by cha Feb 8, 2010

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* Integrated Hospital Information System beta 2.0.0 - 2004-05-16
* GNU General Public License
* Copyright 2002,2003,2004 
*
* See the file "copy_notice.txt" for the licence notice
*/     
#define('LANG_FILE','specials.php');
define('LANG_FILE','nursing.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
$breakfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;
$returnfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;
$thisfile=basename(__FILE__);

//ajax

	
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');


$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Toolbar title
$smarty->assign('sToolbarTitle','Clinics:: Prescription Writer');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','Clinics:: Prescription Writer')");
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('title','Clinics:: Prescription Writer');
$smarty->assign('breakFile',$breakfile);

	ob_start();
 ?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/clinics/js/prescription-gui.js"></script>
<script type=type="text/javascript" language="javascript">

</script>
<?
$sTemp = ob_get_contents();
global $db;

$smarty->assign('sPID', '<input type="text" class="jedInput" id="pid" name="pid" size="35" align="absmiddle" readonly="" value="'.$_GET['pid'].'"/>');


ob_start();
?>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" id="userck" value="<?php echo $userck?>">  
	<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
	<input type="hidden" name="encoder" id="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	<input type="hidden" name="key" id="key">
	<input type="hidden" name="pagekey" id="pagekey"> 

 <?
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

 /**
 * show Template
 */
 # Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','clinics/prescription-history-gui.tpl');

$smarty->display('common/mainframe.tpl');

?>

