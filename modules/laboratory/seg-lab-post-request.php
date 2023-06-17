<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

	$lang_tables[] = 'departments.php';
	define('LANG_FILE','lab.php');
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);

	$local_user='ck_lab_user';
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/laboratory/ajax/lab-post.common.php');

	$dbtable='care_config_global'; // Taboile name for global configurations
	$GLOBAL_CONFIG=array();
	$new_date_ok=0;

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

	$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;

	$breakfile = "";

	$title="Laboratory";

	# Create laboratory object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();

	global $db, $allow_labrepeat;

	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

	if (!isset($popUp) || !$popUp){
		if (isset($_GET['popUp']) && $_GET['popUp']){
			$popUp = $_GET['popUp'];
		}
		if (isset($_POST['popUp']) && $_POST['popUp']){
			$popUp = $_POST['popUp'];
		}
	}

	if ($_GET['encounter_nr'])
		$encounter_nr = $_GET['encounter_nr'];

	if ($_GET['pid'])
		$pid = $_GET['pid'];

	if ($_GET['refno'])
		$refno=$_GET['refno'];

	if ($_GET['user_origin'])
		$user_origin = $_GET['user_origin'];

	if ($_GET['service_code'])
		$service_code = $_GET['service_code'];

	$smarty->assign('breakfile',$breakfile);
	$smarty->assign('pbBack','');

	if ($repeaterror){
		$smarty->assign('sysErrorMessage','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
	}

	# Title in the title bar
	$LDLab = "Laboratory";

	$smarty->assign('sToolbarTitle',"$LDLab :: New Test Request");

	# href for the help button
	$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");


	 # Window bar title
	 $smarty->assign('sWindowTitle',"$LDLab :: New Test Request");

	 # Assign Body Onload javascript code
	 #$onLoadJS='onLoad="preset();"';
	 $onLoadJS='onLoad="preset('.$refno.',\''.$service_code.'\');"';
	 $smarty->assign('sOnLoadJs',$onLoadJS);

	 if ($popUp){
		 $smarty->assign('bHideTitleBar',TRUE);
		 $smarty->assign('bHideCopyright',TRUE);
	 }
	 # Collect javascript code

	 ob_start();
	 # Load the javascript code
	 #$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
     $xajax->printJavascript($root_path.'classes/xajax_0.5');
?>

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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcuts.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/prototypeui/window.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>


<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/lab-post-request.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
</script>

<?php

	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

	$row_rs = $srvObj->getTestCode($service_code);
    $smarty->assign('sRefno',$refno);
	$smarty->assign('sTestName',$row_rs['name']);
	$smarty->assign('sTestCode',$service_code);
    
    $patient = $srvObj->getPatientOrderInfo($refno);
    $smarty->assign('sHRN',$patient['pid']);
    $smarty->assign('sPatientName',$patient['patient_name'].'<input type="hidden" name="pid" id="pid" value="'.$patient['pid'].'"');
    $smarty->assign('sAge',$patient['age']);
    
    if ($patient['sex']=='m')
        $gender = 'Male';
    elseif ($patient['sex']=='f')
        $gender = 'Female';
    else
        $gender = 'Unspecified';            
    $smarty->assign('sGender',$gender);
    

	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"11\">Request list is currently empty...</td>
				</tr>");

ob_start();
$sTemp='';
?>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">

	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="datenow" id="datenow" value="<?=date("Ymd")?>">
    <input type="hidden" name="currentdate" id="currentdate" value="<?=date("m/d/Y h:i a")?>">
	<input type="hidden" name="is_serial" id="is_serial" value="<?=$is_serial?>"
   

<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','laboratory/lab-post-request.tpl');
$smarty->display('common/mainframe.tpl');

?>
