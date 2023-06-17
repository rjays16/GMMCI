<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/lingap_billing_request.common.php");

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

//$local_user='ck_grants_user';
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
 #$title="Sponsor grants";

if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-sponsor-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$thisfile='seg_sponsor_lingap_billing_request.php';

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

$smarty->assign('QuickMenu', FALSE);
$smarty->assign('bHideCopyright',TRUE);

# Assign Body Onload javascript code

if ($view_only) {
	$onLoadJS='onload="selectGrant(true)"';
}
else {
	$onLoadJS='onload="selectGrant(true)"';
}
$smarty->assign('sOnLoadJs',$onLoadJS);


# No POSTING, form interaction is done in AJAX

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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">
var glst, isLoading=false;
var LocalModule

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

function startLoading() {
	if (!isLoading) {
		isLoading = 1;

		return overlib('<img src="../../images/loading6.gif"/>',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			FGCLASS,'cg_AjaxLoader',
			CGCLASS,'cg_AjaxLoader',
			BGCLASS,'bg_AjaxLoader',
			CAPTIONFONTCLASS,'cap_AjaxLoader',
			CLOSEFONTCLASS,'clo_AjaxLoader',
			TEXTFONTCLASS,'text_AjaxLoader',
			MODALCOLOR,'#ffffff', MODALOPACITY,80,
			SHADOW,0, FILTER,
			STICKY, CLOSECLICK, MODAL, CLOSETEXT,'<span></span>',
			CAPTION,'Loading',
			MIDX,0, MIDY,0,
			STATUS,'Loading');
	}
}

function doneLoading() {
	if (isLoading) {
		//setTimeout('cClick()', 500);
		cClick();
		isLoading = 0;
	}
}

function clearFlaggedInputs( ) {
	var clearErrors = $$('.errorInput');
	if (clearErrors) clearErrors.each( function(x) { x.removeClassName('errorInput') } );
}

function flagInput(obj) {
	if ($(obj)) {
		$(obj).addClassName('errorInput');
	}
}

function setFullAmount() {
	var amt;
	var due=$('due').value;
	while (isNaN(amt)) {
		amt = prompt('Enter amount to be granted:');
		if (amt===null) return false;
		if (parseFloatEx(amt) > parseFloatEx(due)) amt=due;
	}
	$('grant').value = amt;
	$('grant_view').value = formatNumber(amt,2);
}

function setPartialAmount() {
	var amt=parseFloatEx($('due').value);
	$('grant').value = amt;
	$('grant_view').value = formatNumber(amt,2);
}

function save() {
	clearFlaggedInputs();
	startLoading();
	xajax.call( 'save',
		{
			parameters: [
				xajax.getFormValues('data', true)
			],
			onError:function(){
				doneLoading();
				SegAlerts.alert( {message:'Error sending request...'} );
			}
		}
	);
}

function selectGrant(forceSelect) {

	var selected = $('select-grant').value;
	if (!forceSelect)
		if ($('eid').value == selected)
			return false;
	$('eid').value = selected;

	startLoading();
	xajax.call( 'loadGrantDetails',
		{
			parameters: [
				selected, '<?= $_REQUEST['nr'] ?>'
			],
			onError:function(){
				doneLoading();
				SegAlerts.alert( {message:'Error sending request...'} );
			}
		}
	);
}
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');
$listgen->printJavascript($root_path);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$title = 'Lingap :: Billing grant';

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

//if ($_REQUEST['action']=='edit') {
//	$sql =
//		"SELECT e.entry_id,e.control_nr,e.entry_date,e.remarks,e.is_advance,\n".
//			"d.ref_no,d.amount `grant`\n".
//			"FROM seg_lingap_entries e\n".
//			"INNER JOIN seg_lingap_entries_fb d ON d.entry_id=e.entry_id\n".
//			"WHERE entry_id=".$db->qstr($_REQUEST['id']);
//	if ($lingap_info=$db->GetRow($sql)) {
//		$_REQUEST['nr'] = $row['ref_no'];
		//print_r($lingap_info);
//	}
//}

# Get previous saved grants
$sql =
	"SELECT d.entry_id `id`,e.entry_date `date`,d.amount\n".
		"FROM seg_lingap_entries_bill d\n".
			"INNER JOIN seg_lingap_entries e ON e.id=d.entry_id\n".
		"WHERE d.ref_no=".$db->qstr($_REQUEST['nr']);
$saved_grants = array();
if (($result=$db->Execute($sql)) !== FALSE) {
	while ($row = $result->FetchRow()) {
		$saved_grants[] = $row;
	}
}

# Controls
foreach ( $saved_grants as $grant ) {
	$grants_html = "<option value=\"{$grant['id']}\" style=\"font-weight:bold\">".date("m-M-Y",strtotime($grant['date']))." (P ".number_format($grant['amount'], 2).")</option>\n";
}
$smarty->assign('sSelectMode',
'<select class="segInput" id="select-grant">
<option value="">--New Referral--</option>'.$grants_html.'</select>
<button id="select_trigger" class="segButton" onclick="selectGrant(); return false">Select</button>
');
$smarty->assign('sEntryId','<input id="eid" name="eid" type="hidden" value="" />');
$smarty->assign('sBillNr','<input class="segInput" id="bill_nr" name="bill_nr" type="text" size="20" value="'.$_REQUEST['nr'].'" disabled="disabled" />');
$smarty->assign('sPID','<input class="segInput" id="pid" name="pid" type="text" size="20" value="" disabled="disabled" />');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="50" value="" disabled="disabled" />');
$smarty->assign('sControlNr','<input id="control_nr" name="control_nr" class="segInput" type="text" autocomplete="off" value="" />');
$smarty->assign('sIsAdvance','<input class="segInput" id="is_advance" name="is_advance" type="checkbox" value="1" />');
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="30" rows="2" style=""></textarea>');
$smarty->assign('sAmountDue','<input type="hidden" id="due" name="due" value="0"/><input class="segInput" id="due_view" type="input" value="0.00" disabled="disabled" />');
$smarty->assign('sGrantAmount','<input type="hidden" id="grant" name="grant" value="0"/><input class="segInput" id="grant_view" type="input" value="0.00" disabled="disabled" />');
$smarty->assign('sPartialGrant','<button id="partial" class="segButton" onclick="setFullAmount(); return false;" disabled="disabled"><img src="../../gui/img/common/default/emoticon_smile.png" />Partial</button>');
$smarty->assign('sFullGrant','<button id="full" class="segButton" onclick="setPartialAmount(); return false;" disabled="disabled"><img src="../../gui/img/common/default/emoticon_happy.png" />Full</button>');

$date_set=$request_date;
if ($lingap_info['entry_date'])
	$entry_date = strtotime($lingap_info['entry_date']);
else
	$entry_date = time();

$smarty->assign('sEntryDate','<input class="segInput" name="entry_date" id="entry_date" type="text" value="" />');
if ($view_only)
	$smarty->assign('sCalendarIcon','<button class="segButton" id="entry_date_trigger" disabled="disabled"><img '.createComIcon($root_path,'calendar.png','0') . '>Select date</button>');
else {
	$smarty->assign('sCalendarIcon','<button class="segButton" id="entry_date_trigger" onclick="return false;"><img '.createComIcon($root_path,'calendar.png','0') . '>Select date</button>');
	$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		inputField: \"entry_date\",
		dateFormat: \"%Y-%m-%d %H:%M\",
		trigger: \"entry_date_trigger\",
		showTime: true,
		align: \"Cm/Cm/Cm/Cm/Cm\",
		onSelect: function() { this.hide() }
	});
</script>";
	$smarty->assign('jsCalendarSetup', $jsCalScript);
}

# Save/Cancel buttons
if (!$view_only) {
	$smarty->assign('sContinueButton','<button id="form_save" class="segButton" onclick="save(); return false;"><img src="'.$root_path.'gui/img/common/default/disk.png" />Save</button>');
	$smarty->assign('sBreakButton','<button id="form_cancel" class="segButton" onclick="window.location=\''.$breakfile.'\'; return false"><img src="'.$root_path.'gui/img/common/default/cancel.png" />Cancel</button>');
}

# Form tags
$smarty->assign('sFormStart','<form id="data">');
$smarty->assign('sFormEnd','</form>');

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
	<input type="hidden" id="entry_id" name="entry_id" value="<?= $lingap_info['entry_id'] ?>">
	<input type="hidden" id="ss" name="ss" value="<?= $_REQUEST['ss'] ?>">
	<input type="hidden" id="refno" name="refno" value="">
	<input type="hidden" id="refsource" name="refsource" value="">

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/lingap_billing_request.tpl');
$smarty->display('common/mainframe.tpl');