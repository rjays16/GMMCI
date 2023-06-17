<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/cmap_account.common.php");


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
		$breakfile = $root_path.'modules/cashier/seg-cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$thisfile='seg_sponsor_cmap_accounts.php';


// check for valid permissions
require_once $root_path.'include/care_api_classes/class_user.php';
$user = SegUser::getCurrentUser();

$permissionSet = array('_a_1_cmapadmin');
$allow = $user->hasPermission($permissionSet);
if (!$allow)
{
	header('Location:'.$root_path.'main/login.php?'.
		'forward='.urlencode('modules/sponsor/'.$thisfile).
		'&break='.urlencode('modules/sponsor/seg-sponsor-functions.php'));
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
include_once($root_path."include/care_api_classes/sponsor/class_cmap_account.php");
$ac = new SegCMAPAccount;


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

# Collect javascript code
ob_start();

# Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">
var glst, grid, buffer,
	isLoading=false

// callback triggered when Patient Search window is closed
function pSearchClose() {
	if ($('pid').value) {
		xajax.call('updateBalance', { parameters:[$('pid').value]});
		$('select-enc').removeClassName('link').addClassName('disabled');
		$('adjust-balance').addClassName('link').removeClassName('disabled');
	}
	else {
		$('select-enc').addClassName('link').removeClassName('disabled');
		$('adjust-balance').removeClassName('link').addClassName('disabled');
	}
	refreashLists();
	cClick();
}

function refreashLists() {
	var o = new Object();
	if (!$('pid').value) {
		flst.clear();
		rlst.clear();
	}
	else {
		o['pid'] = $('pid').value;
		xajax.call('updateBalance', { parameters: [o['pid']] } );
		if (typeof(alst)=='object') {
			alst.fetcherParams = o;
			alst.reload();
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

function openAccountMgr()
{
	return overlib(
		OLiframeContent('<?=$root_path?>modules/sponsor/seg_sponsor_cmap_accounts_manager.php',
			600, 350, 'fWizard', 0, 'no'),
		WIDTH,600, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
		CAPTIONPADDING,2,
		CAPTION,'CMAP Accounts Manager',
		MIDX,0, MIDY,0,
		STATUS,'CMAP Accounts Manager');
}

function editAllotment( entry, tab ) {
	if (!entry)
		entry = '';
	if (!tab)
		tab = 'details';
	var nr = $('account').value;
	if (!$('account').value) return false;
	overlib(
		OLiframeContent('<?= $root_path ?>modules/sponsor/seg_sponsor_cmap_account_allotment.php?'+Object.toQueryString({
			entry: entry,
			tab: tab,
			nr: nr
		}),
			500, 350, 'fWizard', 0, 'no'),
		WIDTH,500, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
		CAPTIONPADDING,2,
		CAPTION,'Edit allotment entry',
		MIDX,0, MIDY,0,
		STATUS,'Edit allotment entry');
	return false;
}

function selectAccount() {
	var activeAccount = $('account').value;
	if (!activeAccount) {
		$('actual-balance').value="";
		$('referred-balance').value="";
		alst.clear();
	}
	else {
		var o = new Object;
		o['nr'] = activeAccount;
		xajax.call('updateBalance', { parameters: [o['nr']] } );
		if (typeof(alst)=='object') {
			alst.fetcherParams = o;
			alst.reload();
		}
	}
}

function tooltip (text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function addAllotment(details) {
	list = $('alst');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
			if (typeof(details)=='object') {
				var
					id=details["id"],
					nr=details["nr"],
					date=details["date"],
					//account_id=details["account_id"],
					//account_name=details["account_name"],
					amount=details["amount"],
					encoder=details["encoder"],
					saro=details["saro"],
					nca=details["nca"],
					remarks=details["remarks"],
					status=details["status"];

				var dRows = dBody.select("tr");
				var alt = (dRows.length%2>0) ? 'alt':'';

				var row = new Element('tr', { class: alt, id:'fi_'+id, style:'height:26px' } ).update(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'fi_date_'+id})
							.setStyle({ font:'bold 11px Tahoma', color:'#000080' })
							.update(date)
					)
				).insert(
					new Element('td', { class:'rightAlign' } ).update(
						new Element('span', { id: 'fi_amount_'+id, style:'color:#660000'}).update(formatNumber(amount,2))
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'fi_encoder_'+id, style:'color:#660000'}).update(encoder)
					)
				).insert(
					new Element('td', { class:'leftAlign' } ).update(
						new Element('span', { id: 'fi_remarks_'+id}).update(remarks)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						(saro ?
							new Element('img', { id: 'fi_saro_'+id, class:'link', src:'../../gui/img/common/default/page_white_edit.png' }
								).observe('click',
									function(event) {
										editAllotment( id, 'saro' );
									}
								).tooltip('SARO No. '+saro)
							:
							new Element('img', { id: 'fi_saro_'+id, class:'link', src:'../../gui/img/common/default/page_white_add.png' })
							.observe('click',
								function(event) {
									editAllotment( id, 'saro' );
								}
							).tooltip('Add SARO')
						)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						(nca ?
							new Element('img', { id: 'fi_nca_'+id, class:'link', src:'../../gui/img/common/default/page_white_edit.png' }
								).observe('click',
									function(event) {
										editAllotment( id, 'nca' );
									}
								).tooltip('NCA No: ' +nca)
							:
							new Element('img', { id: 'fi_nca_'+id, class:'link', src:'../../gui/img/common/default/page_white_add.png' })
								.observe('click',
									function(event) {
										editAllotment( id, 'nca' );
									}
								).tooltip('Add NCA')
						)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('span', { id: 'fi_status_'+id}).update(status)
					)
				).insert(
					new Element('td', { class:'centerAlign' } ).update(
						new Element('img',{ id:'fi_edit_'+id, class:'segSimulatedLink', src:'../../images/cashier_edit.gif' }
						).setStyle( { margin:'1px' }
						).observe( 'click',
							function(event) {
								editAllotment( id, 'details' );
							}
						).tooltip('Edit allotment')
					).insert(
						new Element('img',{ id:'fi_delete_'+id, class:'segSimulatedLink', src:'../../images/cashier_delete.gif' }
						).setStyle( { margin:'1px' }
						).observe( 'click',
							function(event) {
							}
						).tooltip('Delete allotment')
					)
				);
				dBody.insert(row);
			}
			else {
				dBody.update('<tr><td colspan="6">List is currently empty...</td></tr>');
			}
			return true;
		}
	}
	return false;
}

//document.observe('dom:loaded', function(){
//	$('actual-balance').observe('mouseover', function(){ tooltip('Balance remaining after actual charges are deducted'); })
//		.observe('mouseout', function() { nd(); });
//	$('referred-balance').observe('mouseover', function(){ tooltip('Balance remaining after referrals'); })
//		.observe('mouseout', function() { nd(); });
//});

// tooltips!!!

function createTooltip(element, tip) {
	if ($(element)) {
		var tip;
		if (tip || (tip = $(element).readAttribute('tooltip'))) {
			$(element).observe('mouseover', function() {
					tooltip(tip)
				}).observe('mouseout', function(){
					nd();
				});
		}
		else {
			return false;
		}
	}
}

document.observe('dom:loaded', function() {
	$$('[tooltip]').each(function(element) {
		createTooltip(element);
	});
	Element.addMethods({
		tooltip: function(element, tip) {
			element = $(element);
			createTooltip(element, tip);
			return element;
		}
	});
});


</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

# Allotment list
$alst = &$listgen->createList(
	array(
		'LIST_ID' => 'alst',
		'COLUMN_HEADERS' => array('Date','Amount','Encoder','Remarks','SARO','NCA','Status',''),
		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populateAllotments',
		'INITIAL_MESSAGE' => "Please select a patient first...",
		'ADD_METHOD' => 'addAllotment',
		'FETCHER_PARAMS' => array(),
		'COLUMN_WIDTHS' => array('12%', '14%', '20%', '*', '8%','8%','10%', '4%')
	)
);
$smarty->assign('lstAllotments',$alst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = "CMAP::Accounts";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

$sel_html = "<select class=\"segInput\" id=\"account\" name=\"account\" onchange=\"selectAccount()\" >\n".
	"<option value=\"\">-- Select CMAP account --</option>";
$result = $ac->get();
if ($result) {
	while ($row=$result->FetchRow()) {
		$sel_html.="<option value=\"{$row['account_nr']}\">{$row['account_name']}</option>\n";
	}
}
$sel_html.='</select>';

$smarty->assign('sSelectAccount', $sel_html);
$smarty->assign('sActualBalance','<input class="segClearInput" id="actual-balance" type="text" style="margin-left:5px; font:bold 16px Arial; color:#000080; background-color:#fff; border:1px dashed #4e8ccf; text-align:right; width:150px" readonly="readonly" value="" tooltip="Balance remaining after actual charges"/>');
$smarty->assign('sReferredBalance','<input class="segClearInput" id="referred-balance" type="text" style="margin-left:5px; font:bold 16px Arial; color:#000080; background-color:#fff; border:1px dashed #4e8ccf; text-align:right; width:150px" readonly="readonly" value="" tooltip="Balance remaining after referrals"/>');
$smarty->assign('sAdjustBalance','<img id="adjust-balance" class="disabled" src="../../images/btn_edit_small.gif" border="0" onclick="" />');

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
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/cmap_accounts.tpl');
$smarty->display('common/mainframe.tpl');

