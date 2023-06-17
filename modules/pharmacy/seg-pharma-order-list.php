<?php

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

//$db->debug=1;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Pharmacy::Request list");
 
 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Pharmacy::Request list");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/event.simulate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/modalbox/modalbox.js"></script>
<link rel="stylesheet" href="<?=$root_path?>css/themes/default/modalbox.css" type="text/css" media="screen" />

<script type="text/javascript">

var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

function openPDF(ref) {
	// window.open('seg-pharma-order.php'+URL_FORWARD+'&target=print&ref='+ref,'openPDF',"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	window.open('seg-pharma-order.php'+URL_FORWARD+'&target=print-jasper&ref='+ref,'openPDF',"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}

function tooltip (text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

/*
function validate() {
	switch($('mode').value) {
		case 'date':
			if ($F('seldate') == "specificdate") {
				if (!$('specificdate').value) {
					alert('Please input a date');
					$('specificdate').focus();
					return false;
				}
			}
			else if ($F('seldate') == "between") {
				if (!$('between1').value) {
					alert('Please input a date');
					$('between1').focus();
					return false;
				}
				if (!$('between2').value) {
					alert('Please input a date');
					$('between2').focus();
					return false;
				}
			}
		break;
		
		default:
		break;
	}
}
*/

function pSearchClose() {
	cClick();
}

function deleteItem(id) {
	var dform = document.forms[0]
	$('delete').value = id
	dform.submit()
}

function validate() {
}

function selpayorOnChange() {
	var optSelected = $('selpayor').options[$('selpayor').selectedIndex];
	var spans = document.getElementsByName('selpayoroptions');
	
	for (var i=0; i<spans.length; i++) {
		if (optSelected) {
			if (spans[i].getAttribute("segOption") == optSelected.value) {
				spans[i].style.display = "";
			}
			else
				spans[i].style.display = "none";
		}
	}
}

function seldateOnChange() {
	var optSelected = $('seldate').options[$('seldate').selectedIndex]
	var spans = document.getElementsByName('seldateoptions')
	for (var i=0; i<spans.length; i++) {
		if (optSelected) {
			if (spans[i].getAttribute("segOption") == optSelected.value) {
				spans[i].style.display = ""
			}
			else
				spans[i].style.display = "none"
		}
	}
}

function confirmDelete(id) {
	Effect.Fade('o'+id, { duration: 0.5, from: 1, to: 0.5});
	var html = '<div class="MB_alert"><p>Delete this pharmacy order?</p><input class="segButton" type="button" onclick="Modalbox.hide();xajax.call(\'deleteOrder\',{ parameters:['+id+']} )" value="Delete" /><input class="segButton" type="button" onclick="Modalbox.hide()" value="Cancel" /></div>';
	Modalbox.show(html, {title: 'Confirm delete', width: 300, overlayOpacity: .4, beforeHide: function() { cancelDelete(id) } });
}

function prepareDelete(id) {
	var node = $('o'+id);    
	if (node) {
		Effect.Fade(node, { duration: 0.5, to: 0});
	}
}

function cancelDelete(id) {
	var node = $('o'+id);    
	if (node) {
		Effect.Appear(node, { duration: 0.5});
	}
}

function lateAlert(msg, timeout) {
	setTimeout(function() { alert(msg) }, timeout);
}

function addRow(details) {
	list = $("olst");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null,
					id = details["bestellnum"];
					dRows = dBody.getElementsByTagName("tr");
			if (details["FLAG"]=="1") {
				var options = 
					'<a title=\"Edit\" href=\"apotheke-pass.php<?= URL_APPEND ?>&userck=<?= $userck ?>&target=orderedit&ref='+details["refno"]+'&from=orderlist\" onmouseover="tooltip(this.title)" onmouseout="nd()"><img class=\"segSimulatedLink\" src=\"<?= $root_path ?>images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" style=\"margin:1px;\" /></a>'+
					'<img title=\"Delete\" class=\"link\" src=\"<?= $root_path ?>images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"confirmDelete(\''+details["refno"]+'\')\" style=\"margin:1px;cursor:pointer\" onmouseover="tooltip(this.title)" onmouseout="nd()" />';
				
				if (details["flag"]==='LINGAP')
					options = '<img title="One or more items on this request has been covered by Lingap..." src="../../images/lingap_item.jpg" onmouseover="tooltip(this.title)" onmouseout="nd()" />';
				if (details["flag"]==='PAID')
					options = '<img title="One or more items on this request have an associated payment entry..." src="../../images/paid_item.gif" onmouseover="tooltip(this.title)" onmouseout="nd()" />';
				if (details["served"]==='1')
					options = '<img title="One or more items on this request has already been served..." src="../../images/served_item.jpg" />';

				src = 
				'<tr id="o'+details["refno"]+'" '+((dRows.length%2>0)?' class="alt"':'')+'>' +
					'<td class="centerAlign" style="color:#660000;font:bold 11px Tahoma">'+details['orderdate']+'</td>'+
					'<td align="center">'+
						details["refno"]+
					'</td>'+
					'<td align="left" style="font-size:11px">'+details["name"]+'</td>'+
					'<td align="left">'+details["items"]+'</td>'+
					'<td align="center">'+(details["is_cash"]=='1' ? 'Cash' : 'Charge')+'/<br/>'+details['urgency']+'</td>'+
					'<td align="center">'+details["area_full"]+'</td>'+						
					'<td class="centerAlign" nowrap="nowrap">'+options+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"8\">List is currently empty...</td></tr>";	
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}

function keyF8() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order-functions.php?userck=<?=$userck?>";
}

function keyOrderlist() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order.php?userck=<?=$userck?>&target=list&area=<?=$_GET['area']?>";
	}

function keyServelist() {
	window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order.php?userck=<?=$userck?>&target=servelist&area=<?=$_GET['area']?>";
}

function keyNewOrder() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order.php?userck=<?=$userck?>&target=new&area=<?=$_GET['area']?>";
}

function init() {
	//added by cha, 11-22-2010
	shortcut.add('F8', keyF8,
			{
				'type':'keydown',
				'propagate':false,
			});
	shortcut.add('F3', keyOrderlist,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	shortcut.add('F4', keyServelist,
		{
			'type':'keydown',
			'propagate':false,
		}
	);
	shortcut.add('F6', keyNewOrder,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
}
document.observe('dom:loaded', init);
</script>

<?php
#added by bryan Sept 18,2008
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', TRUE);
$olst = &$listgen->createList('olst',array('Date','Ref No.','Name','Items', 'Priority', 'Area', 'Details'),array(-1,0,0,NULL,0,0,NULL),'populateOrderList');
$olst->addMethod = 'addRow';
$olst->fetcherParams = array();
$olst->columnWidths = array("10%", "12%", "18%", "*", "10%", "10%", "5%");
$smarty->assign('sOrderList',$olst->getHTML());

$smarty->assign('sSearchResults',$rows);
$smarty->assign('sRootPath',$root_path);

#added by bryan on Sept 19,2008
$payorcheckHTML = "<input type=\"checkbox\" id=\"chkpayor\" name=\"chkpayor\" ".($_REQUEST['chkpayor'] ? 'checked="checked"' : '') ."/>";
$smarty->assign('sPayorCheckbox', $payorcheckHTML);

$payorHTML = '<select class="segInput" name="selpayor" id="selpayor" onchange="selpayorOnChange()">
									<option value="name" '. ($_REQUEST["selpayor"]=="name" ? 'selected="selected"' : '') .'>Payor Name</option>
									<option value="pid" '. ($_REQUEST['selpayor']=='pid' ? 'selected="selected"' : '') .'>Patient ID</option>
									<option value="patient" '. ($_REQUEST['selpayor']=='patient' ? 'selected="selected"' : '') .'>Patient Records</option>
									<option value="inpatient" '. ($_REQUEST['selpayor']=='inpatient' ? 'selected="selected"' : '') .'>Inpatient/ER/OPD</option>
								</select>
								<span name="selpayoroptions" segOption="name" '. (($_REQUEST['selpayor']=='name') ? '' : 'style=""' ) .'>
									<input class="segInput" name="name" id="name" type="text" size="20" value="'. $_REQUEST['name'] .'">
									<input type="hidden" name="name_old" value="'. $_REQUEST['name'] .'" >
								</span>
								<span name="selpayoroptions" segOption="pid" '. (($_REQUEST['selpayor']=='pid') ? '' : 'style="display:none"') .'>
									<input class="segInput" name="pid" id="pid" type="text" size="20" value="'. $_REQUEST['pid'] .'"/>
								</span>
								<span name="selpayoroptions" segOption="patient" '. (($_REQUEST['selpayor']=='patient') ? '' : 'style="display:none"') .'>
									<input class="segInput" name="patientname" id="patientname" readonly="readonly" type="text" size="20" value="'. $_REQUEST['patientname'] .'"/>
									<input name="patient" id="patient" type="hidden" value="'. $_REQUEST['patient'] .'"/>
									<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
									 onclick="overlib(
									OLiframeContent(\''. $root_path .'modules/registration_admission/seg-select-enc.php?var_pid=patient&var_name=patientname\', 700, 400, \'fSelEnc\', 0, \'auto\'),
									WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, DRAGGABLE,
									CLOSETEXT, \'<img src='. $root_path .'images/close_red.gif border=0 >\',
									CAPTIONPADDING,2, 
									CAPTION,\'Select registered person\',
									MIDX,0, MIDY,0, 
									STATUS,\'Select registered person\'); return false;"
									onmouseout="nd();" />
								</span>
								<span name="selpayoroptions" segOption="inpatient" '. (($_REQUEST['selpayor']=='inpatient') ? '' : 'style="display:none"') .'>
									<input class="segInput" name="inpatientname" id="inpatientname" readonly="readonly" type="text" size="20" value="'. $_REQUEST['inpatientname'] .'"/>
									<input name="inpatient" id="inpatient" type="hidden" value="'. $_REQUEST['inpatient'] .'"/>
									<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
									onclick="overlib(
									OLiframeContent(\''. $root_path .'modules/registration_admission/seg-select-enc.php?var_encounter_nr=inpatient&var_name=inpatientname&var_include_enc=1\', 700, 400, \'fSelEnc\', 0, \'auto\'),
									WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, DRAGGABLE,
									CLOSETEXT, \'<img src='. $root_path .'images/close_red.gif border=0 >\',
									CAPTIONPADDING,2, 
									CAPTION,\'Select registered person\',
									MIDX,0, MIDY,0, 
									STATUS,\'Select registered person\'); return false;"
								 onmouseout="nd();" />
								</span>
								';
									
$smarty->assign('sPayor', $payorHTML);

$datecheckHTML = "<input type=\"checkbox\" id=\"chkdate\" name=\"chkdate\" ".($_REQUEST['chkdate'] ? 'checked="checked"' : '') ."/>";
$smarty->assign('sDateCheckbox', $datecheckHTML);

$dateHTML = '<select class="segInput" id="seldate" name="seldate" onchange="seldateOnChange()">
									<option value="today" '. ($_REQUEST['seldate']=='today' ? 'selected="selected"' : '') .'>Today</option>
									<option value="thisweek" '. ($_REQUEST['seldate']=='thisweek' ? 'selected="selected"' : '') .'>This week</option>
									<option value="thismonth" '. ($_REQUEST['seldate']=='thismonth' ? 'selected="selected"' : '') .'>This month</option>
									<option value="specificdate" '. ($_REQUEST['seldate']=='specificdate' ? 'selected="selected"' : '') .'>Specific date</option>
									<option value="between" '. ($_REQUEST['seldate']=='between' ? 'selected="selected"' : '') .'>Between</option>
								</select>
								<span name="seldateoptions" segOption="specificdate" '. (($_REQUEST["seldate"]=="specificdate") ? '' : 'style="display:none"') .'>
									<input class="segInput" name="specificdate" id="specificdate" type="text" size="8" value="'. $_REQUEST['specificdate'] .'"/>
									<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "specificdate", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
										});
									</script>
								</span>
								<span name="seldateoptions" segOption="between" '. (($_REQUEST['seldate']=='between') ? '' : 'style="display:none"') .'>
									<input class="segInput" name="between1" id="between1" type="text" size="8" value="'. $_REQUEST['between1'] .'"/>
									<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "between1", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_between1", singleClick : true, step : 1
										});
									</script>
									to
									<input class="segInput" name="between2" id="between2" type="text" size="8" value="'. $_REQUEST['between2'] .'"/>
									<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "between2", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_between2", singleClick : true, step : 1
										});
									</script>
								</span>
						';

$smarty->assign('sDate', $dateHTML);

$areacheckHTML = "<input type=\"checkbox\" id=\"chkarea\" name=\"chkarea\" ".($_REQUEST['chkarea'] ? 'checked="checked"' : '') ."/>";
$smarty->assign('sAreaCheckbox', $areacheckHTML);

$areaHTML ='<select class="segInput" id="selarea" name="selarea" onchange="">';

require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$prod=$prod_obj->getAllPharmaAreas();
while($row=$prod->FetchRow()){
$checked=strtolower($row['area_code'])==strtolower($_REQUEST['selarea']) ? 'selected="selected"' : "";
$areaHTML .=	'<option value="'.$row['area_code'].'" '.$checked.'>'.$row['area_name'].'</option>\n';
}
$areaHTML .= '</select>';						
$smarty->assign('sArea', $areaHTML);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();
?>

<br>

<?php

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

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">  
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 #added by bryan on Sept 18,2008
 $smarty->assign('sMainBlockIncludeFile','pharmacy/orderlist-main.tpl');
 
 $smarty->display('common/mainframe.tpl');
?>