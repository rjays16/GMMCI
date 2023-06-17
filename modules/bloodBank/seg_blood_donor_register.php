<?php
#created by CHA 07-30-2009
#Manage blood donors
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
	
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_core.php');  
require($root_path."modules/bloodBank/ajax/blood-donor-register.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');
#$xajax->printJavascript($root_path.'classes/xajax');
#$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);

#$breakfile = "labor.php";
$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$LDBloodBank = "Blood Bank";  
$smarty->assign('sToolbarTitle',"$LDBloodBank :: Blood Donor Registration");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBloodBank :: Blood Donor Registration");
 
 $smarty->assign('sOnLoadJs','onLoad="preSet(); "');
 # Collect javascript code
 ob_start()

?>
<style>

#municipality_autocomplete, #barangay_autocomplete {
	padding-bottom:1.75em;
	width: 185px;
 
}
</style>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function preSet(){
	//document.getElementById('search').focus();
	startAJAXSearch(0);
}

function BackMainMenu(){
		urlholder="labor.php<?=URL_APPEND?>";
		window.location.href=urlholder;
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
<script type="text/javascript" src="js/blood-register-donor.js?t=<?=time()?>"></script>
										
<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
if (!$_REQUEST['mode']) $_REQUEST['mode'] = 'donor_register'; 
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');
$smarty->assign('sDonorName',"<b>Donor Name </b><input class=\"segInput\" type=\"text\" id=\"donor_search\" size=\"25\" onkeypress=\"checkEnter(event)\" onkeyup=\"if (this.value.length >= 3) startAJAXSearch(0); return false;\"/>");
ob_start();

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

?>

<form action="<?php echo $breakfile?>" method="post">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
<input type="hidden" name="key" id="key">
<input type="hidden" name="pagekey" id="pagekey"> 
</form>



<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
 $smarty->assign('sMainBlockIncludeFile','blood/blood_register_donor.tpl');   
 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?> 

