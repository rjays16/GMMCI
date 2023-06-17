<?php
//created by cha 2009-04-15

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
$thisfile='seg_effectivity_price.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

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
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />

<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>


<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/clinics/js/jqmodal/jqModal.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/clinics/js/jqmodal/jqDnR.js"></script>
<script type=type="text/javascript" language="javascript">
var J = jQuery.noConflict();
J().ready(function() {
  J('#ex2').jqm({ajax: 'examples/2.html', trigger: 'a.ex2trigger'});
});
</script>
<style type="text/css">  
/* jqModal base Styling courtesy of;
  Brice Burgess <bhb@iceburg.net> */

/* The Window's CSS z-index value is respected (takes priority). If none is supplied,
  the Window's z-index value will be set to 3000 by default (in jqModal.js). You
  can change this value by either;
    a) supplying one via CSS
    b) passing the "zIndex" parameter. E.g.  (window).jqm({zIndex: 500}); */
  
.jqmWindow {
    display: none;
    
    position: fixed;
    top: 17%;
    left: 50%;
    
    margin-left: -300px;
    width: 600px;
    
    background-color: #EEE;
    color: #333;
    border: 1px solid black;
    padding: 12px;
}

.jqmOverlay { background-color: #000; }

/* Fixed posistioning emulation for IE6
     Star selector used to hide definition from browsers other than IE6
     For valid CSS, use a conditional include instead */
* html .jqmWindow {
     position: absolute;
     top: expression((document.documentElement.scrollTop || document.body.scrollTop) + Math.round(17 * (document.documentElement.offsetHeight || document.body.clientHeight) / 100) + 'px');
}

</style>

<?
 $sTemp = ob_get_contents();
	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
	
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

$smarty->assign('sPatientID', '<input id="pid" name="pid" class="segInput" type="text" value="'.$_POST["pid"].'" readonly="readonly"/>');
$smarty->assign('sPatientName','<input class="segInput" id="pname" name="pname" type="text" size="35" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["pname"].'"/>');
$smarty->assign('sPatientAddress', '<textarea class="segInput" id="paddress" name="paddress" cols="32" rows="3" readonly="" style="font:bold 12px Arial">'.stripslashes($_POST['paddress']).'</textarea>');
$smarty->assign('sPatientComplaint', '<textarea class="segInput" id="pcomplaint" name="pcomplaint"cols="40" rows="2" style="font:bold 12px Arial;" readonly="">'.stripslashes($_POST['pcomplaint']).'</textarea>');
$smarty->assign('sPatientDiagnosis', '<textarea class="segInput" id="pdiagnosis" name="pdiagnosis" cols="40" rows="2" style="font:bold 12px Arial;" readonly="">'.stripslashes($_POST['pcomplaint']).'</textarea>');
$smarty->assign('sPatientAge', '<input type="text" class="segInput" id="patage" name="patage" size="5" readonly="" value="'.$_POST['patage'].'"/>');

$smarty->assign('sSelectEnc','<img id="select-enc" class="link" src="../../images/btn_encounter_small.gif" border="0" onclick="openPatientSelect()" />');
$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls()"/>');

$smarty->assign('sRequestDate','<span id="show_requestdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['requestdate'])) : $curDate_show).'</span>
<input class="jedInput" name="requestdate" id="requestdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['requestdate'])) : $curDate).'" style="font:bold 12px Arial">');

$smarty->assign('sCalendarIcon','<img '.createComIcon($root_path,'show-calendar.gif','0').' id="requestdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup({
		displayArea : \"show_requestdate\",
			inputField : \"requestdate\", 
			ifFormat : \"%Y-%m-%d %H:%M\", 
			daFormat : \" %B %e, %Y %I:%M%P\", 
			showsTime : true, 
			button : \"requestdate_trigger\", 
			singleClick : true, 
			step : 1
});
</script>";    
$smarty->assign('jsCalendarSetup', $jsCalScript);

$smarty->assign('sSavePrescription','<input type="checkbox" class="segInput" name="is_save" id="is_save"/>');
$smarty->assign('sSaveOptions','<select id="save_option" name="save_option">
					<option value="0">as new Standard Prescription</option>
					<option value="1">and overwrite Existing</option>
				</select>');
$smarty->assign('sPrescriptionTags', '<input type="text" class="segInput" name="prescription_tag" id="prescription_tag" size="60" style="font:bold 12px Arial;" value="'.$_POST['prescription_tag'].'"/>');
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
 $sTable = ob_get_contents();
ob_end_clean();
$smarty->assign('sTable',$sTable);
 $smarty->assign('sHiddenInputs',$sTemp);
 $smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

 /**
 * show Template
 */
 # Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','clinics/new-prescription.tpl');

$smarty->display('common/mainframe.tpl');

?>

