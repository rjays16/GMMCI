<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/lingap_billing.common.php");


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');

$local_user='ck_grants_user';
require_once($root_path.'include/inc_front_chain_lang.php');

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

# $phpfd = config date format in PHP date() specification

if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/sponsor/seg_sponsor_pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$thisfile='seg_sponsor_lingap_billing.php';

// check for valid permissions
require_once $root_path.'include/care_api_classes/class_user.php';
$user = SegUser::getCurrentUser();

$permissionSet = array('_a_1_lingapbill');
$allow = $user->hasPermission($permissionSet);
if (!$allow)
{
	header('Location:'.$root_path.'main/login.php?'.
		'forward='.urlencode('modules/sponsor/'.$thisfile).
		'&break='.urlencode('modules/sponsor/seg-sponsor-functions.php'));
	exit;
	exit;
}


//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/sponsor/class_sponsor.php");
include_once($root_path."include/care_api_classes/sponsor/class_lingap.php");
$sc = new SegSponsor();
$lc = new SegLingap();
global $db;

$Nr = $_GET['nr'];

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Assign Body Onload javascript code
/*
if ($view_only) {
	$onLoadJS='onload="eraseCookie(\'__ret_ck\');'.($Nr ? 'xajax_populate_items(\''.$Nr.'\',1)' : '').'"';
}
else {
 $onLoadJS='onload="eraseCookie(\'__ret_ck\');'.($Nr ? 'xajax_populate_items(\''.$Nr.'\')' : '').'"';
}
*/
$smarty->assign('sOnLoadJs',$onLoadJS);

if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {
	// NO SUBMIT
}


# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">

var glst, grid, buffer,
	isLoading=false

// override default cClick function
//	$cClick = cClick;
//	cClick = function() {
//		refreashLists();
//		$cClick();
//	}

// callback triggered when Patient Search window is closed
function pSearchClose() {
	$('select-enc').disabled = ($('pid').value);
	refreshLists();
	cClick();
}

function refreshLists() {
	var o = new Object();
	if (!$('pid').value) {
		flst.clear();
		rlst.clear();
	}
	else {
		o['pid'] = $('pid').value;
		if (typeof(flst)=='object') {
			flst.fetcherParams = o;
			flst.reload();
		}
		if (typeof(rlst)=='object') {
			rlst.fetcherParams = o;
			rlst.reload();
		}
	}
}

function startLoading() {
	if (!isLoading) {
		isLoading = 1;
		return overlib('<strong>Loading items...</strong><br/><img src="../../images/ajax_bar.gif"/>',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			NOCLOSE, TIMEOUT, 10000, OFFDELAY, 10000,
			CAPTION,'Loading',
			MIDX,0, MIDY,0,
			STATUS,'Loading');
	}
}

function doneLoading() {
	if (isLoading) {
		setTimeout('cClick()', 500);
		isLoading = 0;
	}
}

function validate() {
}

function openPatientSelect() {
	if ($('select-enc').hasClassName('disabled')) return false;
<?php
$var_arr = array(
	"var_pid"=>"pid",
	"var_encounter_nr"=>"encounter_nr",
	"var_name"=>"name",
	"var_clear"=>"clear-enc",
	"var_discount"=>"sw-class",
	"var_enctype"=>"encounter_type",
	"var_enctype_show"=>"encounter_type_show",
	'var_include_enc'=>'1',
	"var_include_walkin"=>"0",
	"var_include_walkin"=>"0",
	"var_reg_walkin"=>"0"
);
$vas = array();
foreach($var_arr as $i=>$v) {
	$vars[] = "$i=$v";
}
$var_qry = implode("&",$vars);
?>
	overlib(
			OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?<?=$var_qry?>&var_include_enc=0',
			700, 400, 'fSelEnc', 0, 'no'),
			WIDTH,700, TEXTPADDING,0, BORDER,0,
			FILTER,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0>',
			CAPTIONPADDING,2,
			CAPTION,'Select registered person',
			MIDX,0, MIDY,0,
			STATUS,'Select registered person');
	return false;
}

function openEntry(nr) {
	if (!$('pid').value) return false;
	if (!nr) nr='';
	overlib(
		OLiframeContent('<?= $root_path ?>modules/sponsor/seg_sponsor_lingap_billing_request.php?nr='+encodeURIComponent(nr)+'&from=CLOSE_WINDOW',
			450, 320, 'fWizard', 0, 'no'),
			WIDTH,450, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 onclick=refreshLists()>',
			CAPTIONPADDING,2,
			CAPTION,'Entty for Lingap billing grants',
			MIDX,0, MIDY,0,
			STATUS,'Entty for Lingap billing grants');
	return false;
}

function openBillingStatement(ss) {
	if (!$('pid').value) return false;
	window.open('../social_service/seg-report-patrequest-for-lingap.php?control_nr='+ss,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
	return false;
}


function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function tooltip(text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function resetControls() {
	$('name').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('select-enc').disabled = false;
	$('sw-class').innerHTML = 'None';
	$('encounter_type_show').innerHTML = 'WALK-IN';
	$('encounter_type').value = '';

	//alert('msg:'+rlst.initialMessage)
	if (typeof(flst) == 'object') {
		rlst.clear({message:flst.initialMessage});
	}
	if (typeof(rlst) == 'object') {
		rlst.clear({message:rlst.initialMessage});
	}
	if (typeof(alst) == 'object') {
		rlst.clear({message:alst.initialMessage});
	}
	new Effect.Appear($('rqsearch'),{ duration:0.5 });
}

function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function addBillingStatementRow(details) {
	list = $('rlst');
	if (list) {
		try {
			var dBody=list.select("tbody")[0];
			if (dBody) {
				if (typeof(details)=='object') {
					var id=details["nr"],
						nr=details["nr"],
						ward=details["ward"],
						date=details["date"],
						due=parseFloatEx(details["due"]),
						grant=parseFloatEx(details["grant"]),
						disabled=(details["disabled"]=='1');

					var dRows = dBody.select("tr");
					var alt = (dRows.length%2>0) ? 'alt':'';
					var disabledAttrib = disabled ? 'disabled="disabled"' : "";

					var row = new Element('tr', { class: alt, id:'ri_'+id , style:'height:26px' } ).update(
						new Element('td', { class:'centerAlign' } ).update(
							new Element('img', { id: 'ri_expand_'+id, class:'link', src:'../../gui/img/common/default/expand.gif' } )
						).insert(
							new Element('img', { id: 'ri_expand_'+id, class:'disabled', src:'../../gui/img/common/default/collapse.gif' } )
						)
					).insert(
						new Element('td', { class:'centerAlign' } ).update(
							new Element('span', { id: 'ri_date_'+id }).update(date)
						)
					).insert(
						new Element('td', { class:'centerAlign' } ).update(
							new Element('span', { id: 'ri_ward_'+id }).update(ward)
						)
					).insert(
						new Element('td', { class:'centerAlign' } ).update(
							new Element('span', { id: 'ri_nr_'+id }).update(nr)
						)
					).insert(
						new Element('td', { class:'rightAlign' } ).update(
							new Element('span', { id: 'ri_due_'+id}).update( formatNumber(due,2) )
						)
					).insert(
						new Element('td', { class:'rightAlign' } ).update(
							new Element('span', { id: 'ri_grant_'+id}).update( formatNumber(grant,2) )
						)
					).insert(
						new Element('td', { class:'centerAlign' } ).update(
							new Element('button',{ id:'ri_delete_'+id, class:'segButton' }
							).update(
								new Element('img', { src:'../../gui/img/common/default/table_lightning.png' })
							).insert('Grant'
							).observe( 'click',
								function(event) {
									openEntry(nr);
									return false;
								}
							).observe( 'mouseover',
								function(event) {
									tooltip('Add grant to this patient billing');
								}
							).observe( 'mouseout',
								function(event) {
									nd();
								}
							)
						)
					);
					dBody.insert(row);
				}
				else {
					dBody.update('<tr><td colspan="6">List is currently empty...</td></tr>');
				}
				return true;
			}
		} catch(e) {
			alert("An exception occurred in the script. Error name: " + e.name + ". Error message: " + e.message);
		}
	}
	return false;
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=$('row'+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		rmvRow.remove();
		if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
			appendOrder(table, null);
		reclassRows(table,rndx);
	}
}

function changeTransactionType() {
	clearEncounter();
	refreshDiscount();
}

function clickGrant() {
	if (parseFloatEx($('grant-amount').value)==0) {
		alert('Please enter an amount...');
		return false;
	}
	xajax.call('addGrant', { parameters:[ iSrc, iNr, iCode, iArea, $('grant-account').value, parseFloatEx($('grant-amount').value) ] });
}


</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

$rlst = &$listgen->createList(
	array(
		'LIST_ID' => 'rlst',
		'COLUMN_HEADERS' => array('','Bill date','Ward','Bill no.','Amount due','Grant amount', 'Options'),
		'COLUMN_SORTING' => array(LG_SORT_UNSORTABLE, LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populateBillingStatements',
		'INITIAL_MESSAGE' => "Please select a patient first...",
		'EMPTY_MESSAGE' => "Patient has no Billing grants found...",
		'ADD_METHOD' => 'addBillingStatementRow',
		'FETCHER_PARAMS' => array(),
		'COLUMN_WIDTHS' => array('6%', '15%', '18%', '18%', '*', '14%', '12%', '15%')
	)
);
$smarty->assign('lstRequest',$rlst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = "Lingap :: Hospital Bills";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

$smarty->assign('sSelectEnc','<button id="select-enc" class="segButton" border="0" onclick="openPatientSelect(); return false;">Select</button>');
$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sPatientID','<input id="pid" name="pid" class="clear" type="text" value="'.$_POST["pid"].'" readonly="readonly" style="color:#006600; font:bold 16px Arial"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="" readonly="readonly" value="'.$_POST["name"].'"/>');
$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls()"/>');
$smarty->assign('sPatientEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');
if ($_POST['encounter_type'])  $smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
	if ($person['encounter_type'])
		$smarty->assign('sOrderEncTypeShow',$enc[$person['encounter_type']]);
	else  $smarty->assign('sOrderEncTypeShow', 'N/A');
}
$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));

$dbtime_format = "Y-m-d";
//$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);

$smarty->assign('sRequestFilterDate','
	<input class="segInput" name="date_request" id="date_request" type="text" size="8" value="'.$curDate.'"/>
	<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_date_request" align="absmiddle" class="segSimulatedLink"  />
	<script type="text/javascript">
		Calendar.setup ({
			inputField : "date_request", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_date_request", singleClick : true, step : 1
		});
	</script>
');

if ($_POST['entry_date'])
	$dEntryDate = strtotime($_POST['entry_date']);
else
	$dEntryDate = time();

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format,$dEntryDate);
$curDate_show = date($fulltime_format,$dEntryDate);

$smarty->assign('sEntryDate',
'<span id="show_entry_date" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.
$curDate_show.'</span>
<input class="segInput" name="entry_date" id="entry_date" type="hidden" value="'.
$curDate.'" style="">');

if ($view_only)
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
else {
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_entry_date\",
		inputField : \"entry_date\",
		ifFormat : \"%Y-%m-%d %H:%M\",
		daFormat : \"  %B %e, %Y %I:%M%P\",
		showsTime : true,
		button : \"entry_date_trigger\",
		singleClick : true,
		step : 1
	});
	</script>";
	$smarty->assign('jsCalendarSetup', $jsCalScript);
}

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/lingap_billing.tpl');
$smarty->display('common/mainframe.tpl');