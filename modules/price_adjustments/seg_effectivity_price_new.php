<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require($root_path."modules/price_adjustments/ajax/price_adjustments.common.php");
require_once($root_path.'include/care_api_classes/class_price_adjustments.php'); //load the CostCenterGuiMgr class
$target = $_GET['target'];

$smarty = new Smarty_Care('select_or_request');
$smarty->assign('sToolbarTitle',"Service Price:: Adjustments"); //Assign a toolbar title
$page=0;
//$smarty->assign('sOnLoadJs','onLoad="startAJAXSearch(\''.$page.'\'); "');

$css_and_js = array(
										//'<link rel="stylesheet" href="'.$root_path.'modules/or/css/packages.css" type="text/css" />'
										'<link rel="stylesheet" href="'.$root_path.'modules/price_adjustments/css/seg_effect_price.css" type="text/css" />'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
										,'<script>var J = jQuery.noConflict();</script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.pack.js"></script>'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.css" />'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'
										,'<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
										,'<script type="text/javascript" src="'.$root_path.'js/setdatetime.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/checkdate.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/NumberFormat154.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/price_adjustments/js/seg-effect-price.js"></script>'
										,$xajax->printJavascript($root_path.'classes/xajax_0.5')
										);
$smarty->assign('css_and_js', $css_and_js);
$breakfile=$root_path."main/spediens.php".URL_APPEND;

if (isset($_POST['is_submitted']))
{
	print_r($_POST);

}

#$smarty->assign('form_start', '<form name="package_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_start', '<form name="guimgr_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');

$smarty->assign('sCostCenters', '<select class="segInput" id="inputarea" name="inputarea" onChange="startAJAXList(this.id,0);initializeTempArray();">
					<option value="0">-Select an area-</option>
					<option value="1">Laboratory</option>
					<option value="2">Radiology</option>
					<option value="3">Pharmacy</option>
					<option value="4">Miscellaneous</option>
					<option value="5">Other Fees</option>
					</select>');
$smarty->assign('search_service', '<input type="text" class="segInput" id="service_name" size="50" onkeyup="if(this.value.length>=3) startAJAXSearch(this.id,0)" style="background-color: rgb(226, 234, 243); border-width: thin; font: bold 13px Arial;" readonly=""/>');
$id="service_name";
$smarty->assign('searchserv_btn', '<input class="segButton" id="search_serv" type="button" value="Search" onclick="startAJAXSearch(\''.$id.'\',0); return false;" disabled=""/>');

$smarty->assign('date_text', '<div id="effectiveDate" class="segInput" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;displaylock;float:left"></div>');
$smarty->assign('date_icon', '<img src="../../gui/img/common/default/show-calendar.gif" id="effect_date_trigger" align="absmiddle" style="cursor:pointer" >[YYYY/mm/dd]');
$jsCalScript = '<script type="text/javascript">
									Calendar.setup (
									{
											displayArea: "effectiveDate",
											inputField : "effectiveDate", 
											ifFormat : "%Y-%m-%d", 
											showsTime : false, 
											button : "effect_date_trigger", 
											singleClick : true, 
											step : 1
									});
									Calendar.setup (
									{
										displayArea: "selDate",   
										inputField : "selDate", 
											ifFormat : "%Y-%m-%d", 
											showsTime : false, 
											button : "selDate_trigger", 
											singleClick : true, 
											step : 1
									});
									</script>';
$smarty->assign('date_cal', $jsCalScript);
//$smarty->assign('saveBtn', '<input class="segButton" id="save" type="button" value="Save" onclick="startAJAXSave(this.id,0,modifiedCode,modifiedCash,modifiedCharge,modLen);"/>');
$smarty->assign('saveBtn', '<input class="segButton" id="save" type="button" value="Save" onclick="save_changes();"/>');
$smarty->assign('cancelBtn', '<input class="segButton" id="cancel" type="button" value="Cancel" onclick="javascript:window.location.reload();"/>');
$smarty->assign('seldate_text', '<div id="selDate" class="segInput" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;displaylock;float:left"></div>');
$smarty->assign('seldate_icon', '<img src="../../gui/img/common/default/show-calendar.gif" id="selDate_trigger" align="absmiddle" style="cursor:pointer">[YYYY/mm/dd]');
$smarty->assign('searchdate_btn', '<input id="save" name="save" type="image" src="../../gui/img/control/default/en/en_searchbtn.gif" border=0 width="72" height="23"  alt="Save data" align="absmiddle"  onclick="callAjax(); return false;"/>');
					
$smarty->assign('is_submitted', '<input type="hidden" name="is_submitted" value="TRUE" />');
ob_start();
?>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="key" id="key">
<input type="hidden" name="pagekey" id="pagekey">
<?
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('sMainBlockIncludeFile','price_adjustments/price_form.tpl'); //Assign the new_package template to the frameset
$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame

?>
<script>
J().ready(function() {
	J('#new_package').tabs();
	//J("input[@name='package_price']").keydown(function(e){return key_check(e, this.value);});
});

</script>
