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
$smarty->assign('sToolbarTitle',"Service Price:: Manager"); //Assign a toolbar title
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
										,'<script type="text/javascript" src="'.$root_path.'modules/price_adjustments/js/seg-pricelist.js"></script>'
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
$id="service_name";
$smarty->assign('sCostCenters', '<select class="segInput" id="inputarea" name="inputarea" onChange="startAJAXList(this.id,0);initializeTempArray();startAJAXSearch(\''.$id.'\',0);ClearText();">
					<option value="0">-Select an area-</option>
					<option value="1">Laboratory</option>
					<option value="2">Radiology</option>
					<option value="3">Pharmacy</option>
					<option value="4">Miscellaneous</option>
					<option value="5">Other Fees</option>
					</select>');
$smarty->assign('search_service', '<input type="text" class="segInput" id="service_name" name="service_name" size="50" onkeyup="if(this.value.length>=3) startAJAXSearch(this.id,0);" style="background-color: rgb(226, 234, 243); border-width: thin; font: bold 13px Arial;" readonly=""/>');

$smarty->assign('searchserv_btn', '<input class="segButton" id="search_serv" type="button" value="Search" onclick="startAJAXSearch(\''.$id.'\',0); return false;" disabled=""/>');


$smarty->assign('saveBtn', '<input class="segButton" id="save" type="button" value="Save" onclick="save_changes();"/>');
$smarty->assign('cancelBtn', '<input class="segButton" id="cancel" type="button" value="Cancel" onclick="javascript:window.location.reload();"/>');

#$smarty->assign('saveBtn', '<img type="image" name="save" id="save" src="'.$root_path.'images/his_savebtn.gif" border="0" style="cursor:pointer;" onclick="save_changes();"></a>');
#$smarty->assign('cancelBtn', '<img type="image" name="cancel" id="cancel" src="'.$root_path.'images/his_cancel_button.gif" border="0" style="cursor:pointer;" onclick="javascript:window.location.reload();"></a>');

$smarty->assign('searchdate_btn', '<input id="save" name="save" type="image" src="../../gui/img/control/default/en/en_searchbtn.gif" border=0 width="72" height="23"  alt="Save data" align="absmiddle"  onclick="callAjax(); return false;"/>');

#added by VAN 07-14-2010
$smarty->assign('sCostCenters2', '<select class="segInput" id="inputarea2" name="inputarea2" onChange="callAjax(); return false;">
					<option value="0">-Select an area-</option>
					<option value="1">Laboratory</option>
					<option value="2">Radiology</option>
					<option value="3">Pharmacy</option>
					<option value="4">Miscellaneous</option>
					<option value="5">Other Fees</option>
					</select>');

$result=$db->Execute("SELECT area_code, upper(name) AS name FROM seg_service_area ORDER BY name");
$option_area="<option value='0'>-Select Area-</option>";
while ($row=$result->FetchRow()) {
	$option_area.='<option value="'.$row['area_code'].'">'.$row['name'].'</option>';
}
$smarty->assign('sAreas', '<select class="segInput" id="area_code" name="area_code">
					'.$option_area.'
					</select>');

$result2=$db->Execute("SELECT area_code, upper(name) AS name FROM seg_service_area ORDER BY name");
$option_area2="<option value='0'>-Select All Area-</option>";
while ($row2=$result2->FetchRow()) {
	$option_area2.='<option value="'.$row2['area_code'].'">'.$row2['name'].'</option>';
}
$smarty->assign('sAreas2', '<select class="segInput" id="area_code2" name="area_code2" onChange="callAjax(); return false;">
					'.$option_area2.'
					</select>');

$smarty->assign('searchkeyText', '<input type="text" class="segInput" id="searchkey" name="searchkey" size="50" onkeyup="if(this.value.length>=3) startAJAXSearch(this.id,0)" style="background-color: rgb(226, 234, 243); border-width: thin; font: bold 13px Arial;"/>');
#-----------
$smarty->assign('is_submitted', '<input type="hidden" name="is_submitted" value="TRUE" />');
ob_start();
?>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="key" id="key">
<input type="hidden" name="pagekey" id="pagekey">
<input type="hidden" name="is_edit" id="is_edit" value="1">
<?
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('sMainBlockIncludeFile','price_adjustments/pricelist_form.tpl'); //Assign the new_package template to the frameset
$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame

?>
<script>
J().ready(function() {
	J('#new_package').tabs();
	//J("input[@name='package_price']").keydown(function(e){return key_check(e, this.value);});
});

</script>
