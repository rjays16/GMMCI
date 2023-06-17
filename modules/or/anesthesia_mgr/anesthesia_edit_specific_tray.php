<?php
#created by CELSY 06-19-2010
#Manage anesthesia procedures
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require($root_path."modules/or/ajax/ajax_anesthesia_gui.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
																		 
$breakfile=$root_path.'modules/ipd/seg-ipd-functions.php'.URL_APPEND;
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$LDTitleMgr = "OR::Anesthesia Procedure Manager";
$smarty->assign('sToolbarTitle',"$LDTitleMgr");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDTitleMgr");

 $smarty->assign('sOnLoadJs','onLoad="preSet(); "');
 # Collect javascript code
 ob_start()                	
?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css" />
<script type="text/javascript" src="<?= $root_path ?>js/listgen/listgen.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?=$root_path?>js/listgen/css/default/default.css"></link>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/op-anesthesia-mgr.js"></script>    
<script type="text/javascript" src="js/anesthesia_mgr.js?t=<?=time()?>"></script>


<script language="javascript" >

function preSet()
{
		var specf_id = '<?echo $_GET['spec_id'];?>';
		var specf_name = '<?echo $_GET['spec_name'];?>';
}

/* if(ok)
	 {
		 edit_specific(spec_id, spec_name, new_spec_id, new_spec_name);
	 }*/
	 
</script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

$smarty->assign('specificName', '<input class="segInput" type="text" id="specific_name" name="new_specific_id" size="15" onfocus="OLmEdit=1" onblur="OLmEdit=0" value="'.$_GET['spec_id'].'"/>');
$smarty->assign('specificId', '<input class="segInput" type="text" id="specific_id" name="new_specific_name" size="15" onfocus="OLmEdit=1" onblur="OLmEdit=0" value="'.$_GET['spec_name'].'"/>');
//$smarty->assign('addSpecific', '<input class="segButton" type="button" name="add_specific" value="Add" onclick="add_specific_anesthesia(\''.$_GET['mode'].'\');"/>');
$smarty->assign('saveBtn', '<input class="segButton" type="button" name="save_specific" value="Save" onclick="edit_specific(\''.$_GET['spec_id'].'\',\''.$_GET['spec_name'].'\');"/>');
$smarty->assign('cancelBtn', '<input class="segButton" type="button" name="cancel" value="Cancel" onclick="javascript:window.parent.location.reload();"/>');

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

# Assign the form template to mainframe
 $smarty->assign('sMainBlockIncludeFile','or/anesthesia/tray_gui.tpl');
 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>

		
	