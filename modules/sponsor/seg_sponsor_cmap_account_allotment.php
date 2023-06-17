<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/cmap_allotment.common.php");

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_grants_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');


$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$title = "Auto-compute billing payment";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','onLoad="init()"');

include_once($root_path."include/care_api_classes/sponsor/class_grant_account.php");

# Collect javascript code
ob_start()

?>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>
<style type="text/css">
.priorityIndicator {
	font: bold 10px Tahoma;
	background-color: #43609c;
	border-color:1px solid #768bb7;
	padding: 2px 4px;
	color: white;
	-moz-border-radius: 4px;
}

ul.sortable {
	width: 100%;
	list-style: none;
	margin: 0;
	display: block;
	border-collapse: collapse;
}

ul.sortable li {
	margin: 0;
	width: 100%;
}

.olbg {
	background-color: transparent;
	border: 0;
}

.olcg {
	background-color: transparent;
	background-image: none;
	text-align:center;
	margin:0;
}

.olcgif {
	background-color: transparent;
	text-align: center;
}

.olfg {
	background-color: transparent;
	text-align: center;
}

.olfgif {
	background-color: none;
	text-align: center;
}

.olcap {
	display: none;
	font-family:Tahoma;
	font-size:11px;
	font-weight:bold;
	color:white;
	margin-top:0px;
	margin-bottom:1px;
}

a.olclo {
	display: none;
	font-family:Verdana;
	font-size:11px;
	font-weight:bold;
	color:#ddddff;
}

.olText {
	font:bold 11px Tahoma;
	color:#2d2d2d;
}
</style>
<script type="text/javascript" >
var AJAXTimerID=0;
var isLoading=false;

function init() {
}

function save() {
	if (validate()) {
		//startLoading();
		xajax.call( 'save',
			{
				parameters: [
					xajax.getFormValues('data'),
					$('chk-saro').checked ? xajax.getFormValues('saro') : null,
					$('chk-nca').checked ? xajax.getFormValues('nca') : null
				]
			}
		);
	}
}

function validate() {
	return true;
}

function startLoading() {
	if (!isLoading) {
		isLoading = 1;
		return overlib('<img src="../../images/loading6.gif"/>',
			WIDTH,300, TEXTPADDING,5, BORDER,0,
			SHADOW, 0,
			MODALCOLOR, '#ffffff',
			MODALOPACITY, 80,
			STICKY, MODAL,
			CAPTION,'',
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

function tooltip (text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function enterAmount() {
	var amt;
	while (isNaN(amt)) {
		amt = prompt('Enter amount to be transferred:');
		if (amt===null) return false;
	}
	$('amount').value = amt;
	$('show_amount').value = formatNumber(amt,2);
}

function tabClick(obj) {
	if ($(obj).hasClassName('segActiveTab')) return false;
	var dList = $(obj).up();
	var tab;

	if (dList) {
		var listItems = dList.select("LI");
	}

	listItems.each(
		function(x) {
			if (x.getAttribute('segTab')!=obj.getAttribute('segTab')) {
				x.removeClassName('segActiveTab');
				tab = x.getAttribute('segTab');
				if ($(tab))
					$(tab).hide();
			}
		}
	);

	tab = obj.getAttribute('segTab');
	if ($(tab))
		$(tab).show();
	$(obj).addClassName('segActiveTab');
}

function enableInputChildren(id, enable) {
	var el=$(id);
	if (el) {
		var children = el.select("INPUT,BUTTON");
		if (children) {
			children.each(
				function(obj) {
					if (obj.id.indexOf('chk-') === -1) {
						obj.disabled = !enable;
					}
				}
			);
			return true;
		}
	}
	return false;
}
</script>
<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

if ($_GET['entry']) {
	require_once "{$root_path}include/care_api_classes/sponsor/class_cmap_allotment.php";
	$entryId = $_GET['entry'];
	$allotment = new SegCmapAllotment($entryId);
	$info = $allotment->fetch();
}

# Buffer page output
ob_start();

?>

	<div style="width:98%; padding:5px 0px">
		<ul id="request-tabs" class="segTab" style="padding-left:10px">
			<li class="<?= !isset($_GET['tab']) || $_GET['tab']=='details' ? 'segActiveTab' : '' ?>" onclick="tabClick(this)" segTab="details">
				<h2 class="segTabText">Allotment details</h2>
			</li>
			<li class="<?= $_GET['tab']=='saro' ? 'segActiveTab' : '' ?>" onclick="tabClick(this)" segTab="saro">
				<h2 class="segTabText">SARO details</h2>
			</li>
			<li class="<?= $_GET['tab']=='nca' ? 'segActiveTab' : '' ?>" onclick="tabClick(this)" segTab="nca">
				<h2 class="segTabText">NCA details</h2>
			</li>
		</ul>

		<div class="frame" style="width:100%; padding-top:10px; min-height:180px">

			<div id="details" <?= !isset($_GET['tab']) || $_GET['tab']=='details' ? '' : 'style="display:none"' ?>>
				<form id="data">
					<input type="hidden" id="id" name="id" value="<?= $_GET['entry'] ?>" />
					<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="">
						<tbody>
							<tr>
								<td class="segPanel" align="right" valign="middle" width="20%"><label>Allotment date</label></td>
								<td class="segPanel2" align="left" valign="middle" width="*" nowrap="nowrap">
<?php
$time_format = "F j, Y";

# Allotment date
if ($info['allotment_date']) {
	$int_entry_date = strtotime($info['allotment_date']);
	$date_show = date($time_format, $int_entry_date);
}
else {
	$date_show = date($time_format,time());
}
?>
									<input type="text" name="allotment_date" id="allotment_date" class="segInput" value="<?= $date_show ?>" style="width:100px" readonly="readonly" />
									<button id="allotment_date_trigger" class="segButton" onclick="return false;"><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
									<script type="text/javascript">
										Calendar.setup ({
											inputField: "allotment_date",
											dateFormat: "%B %e, %Y",
											trigger: "allotment_date_trigger",
											showTime: false,
											onSelect: function() { this.hide() }
										});
									</script>


									<input id="cmap_account" name="cmap_account" type="hidden" value="<?= $_GET['nr'] ?>" />
								</td>
								<td class="segPanel2" align="left" valign="middle" width="30%" style="">
									<strong>Date of this transfer</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle"><strong>Amount</strong></td>
								<td class="segPanel2" align="left" valign="middle" style="border-right:0" nowrap="">
									<input id="show_amount" class="segInput" type="text" value="<?= number_format( (float)$info['amount'],2) ?>" size="15" readonly="readonly" style="text-align:right; font:bold 12px Tahoma; width:100px" />
									<input id="amount" name="amount" type="hidden" value="<?= $info['amount'] ?>" />
									<button class="segButton" onclick="enterAmount(); return false;" <?= $EntryId ? 'disabled="disabled"': '' ?>><img src="../../gui/img/common/default/money_add.png" />Set</button>
								</td>
								<td class="segPanel2" align="left" valign="middle" style="border-left:0">
									<strong>Amount to be transferred</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle">
									<strong>Amound (words)</strong>
								</td>
								<td class="segPanel2" align="left" valign="middle" style="border-right:0">
									<textarea class="segInput" id="amount_in_words" name="amount_in_words" rows="1" cols="23"><?= $info['amount_word'] ?></textarea>
								</td>
								<td class="segPanel2" align="left" valign="middle" style="border-left:0">
									<strong>Amount specified in words</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle">
									<strong>Remarks</strong>
								</td>
								<td class="segPanel2" align="left" valign="middle" style="border-right:0">
									<textarea class="segInput" id="remarks" name="remarks" rows="2" cols="23"><?= $info['remarks'] ?></textarea>
								</td>
								<td class="segPanel2" align="left" valign="middle" style="border-left:0">
									<strong>Additional notes/comments</strong>
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
<?php
if ($_GET['entry']) {
	require_once "{$root_path}include/care_api_classes/sponsor/class_cmap_saro.php";
	$entryId = $_GET['entry'];
	$saro = new SegCmapSaro($entryId);
	$info = $saro->fetch();
}
?>
			<div id="saro" <?= $_GET['tab']=='saro' ? '' : 'style="display:none"' ?>>
				<form id="saro">
					<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="">
						<tbody>
							<tr>
								<td class="segPanel" align="right" valign="middle" width="20%"><strong>Has SARO</strong></td>
								<td class="segPanel2" align="left" valign="middle" width="*" style="">
									<input id="chk-saro" class="segInput" type="checkbox" value="saro" onclick="enableInputChildren('saro',this.checked)" <?= $info['id'] ? 'checked="checked"' : '' ?>>
								</td>
								<td class="segPanel2" align="left" valign="middle" width="35%" style="">
									<strong>Check if this allotment entry has SARO request</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle"><strong>SARO No.</strong></td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<input id="saro_no" name="saro_no" class="segInput" type="text" size="15" value="<?= $info['saro_no'] ?>" <?= $info['id'] ? '' : 'disabled="disabled"' ?> />
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>SARO Control no.</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle"><strong>SARO date</strong></td>
								<td class="segPanel2" align="left" valign="middle" nowrap="nowrap">
<?php
# SARO date
if ($info['saro_date']) {
	$int_entry_date = strtotime($info['saro_date']);
	$date_show = date($time_format, $int_entry_date);
}
else {
	$date_show = date($time_format,time());
}
?>
									<input type="text" name="saro_date" id="saro_date" class="segInput" value="<?= $date_show ?>" style="width:100px" readonly="readonly" />
									<button id="saro_date_trigger" class="segButton" onclick="return false;" <?= $info['id'] ? '' : 'disabled="disabled"' ?>><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
									<script type="text/javascript">
										Calendar.setup ({
											inputField: "saro_date",
											dateFormat: "%B %e, %Y",
											trigger: "saro_date_trigger",
											showTime: false,
											onSelect: function() { this.hide() }
										});
									</script>
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>Date of this SARO release</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle"><strong>Dept. Code</strong></td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<input id="dept_code" name="dept_code" class="segInput" type="text" size="15" value="<?= $info['dept_code'] ?>" <?= $info['id'] ? '' : 'disabled="disabled"' ?> />
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>Refer to SARO form</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle"><strong>Agency Code</strong></td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<input id="agency_code" name="agency_code" class="segInput" type="text" size="15" value="<?= $info['agency_code'] ?>" <?= $info['id'] ? '' : 'disabled="disabled"' ?> />
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>Refer to SARO form</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle"><strong>Fund Code</strong></td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<input id="fund_code" name="fund_code" class="segInput" type="text" size="15" value="<?= $info['fund_code'] ?>" <?= $info['id'] ? '' : 'disabled="disabled"' ?> />
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>Refer to SARO form</strong>
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
<?php
if ($_GET['entry']) {
	require_once "{$root_path}include/care_api_classes/sponsor/class_cmap_nca.php";
	$entryId = $_GET['entry'];
	$nca = new SegCmapNca($entryId);
	$info = $nca->fetch();
}
?>
			<div id="nca" <?= $_GET['tab']=='nca' ? '' : 'style="display:none"' ?>>
				<form id="nca">
					<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="">
						<tbody>
							<tr>
								<td class="segPanel" align="right" valign="middle" width="20%"><strong>Has NCA</strong></td>
								<td class="segPanel2" align="left" valign="middle" width="*" style="">
									<input id="chk-nca" class="segInput" type="checkbox" value="saro" onclick="enableInputChildren('nca',this.checked)" <?= $info['nca_no'] ? 'checked="checked"' : '' ?>>
								</td>
								<td class="segPanel2" align="left" valign="middle" width="40%" style="">
									<strong>Check if this allotment has been issued an NCA</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle"><strong>NCA No.</strong></td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<input id="nca_no" name="nca_no" class="segInput" type="text" size="15" value="<?= $info['nca_no'] ?>" <?= $info['nca_no'] ? '' : 'disabled="disabled"' ?> />
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>NCA Control no.</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle" ><strong>NCA date</strong></td>
								<td class="segPanel2" align="left" valign="middle" nowrap="nowrap">
<?php
$time_format = "F j, Y";

# SARO date
if ($info['nca_date']) {
	$int_entry_date = strtotime($info['nca_date']);
	$date_show = date($time_format, $int_entry_date);
}
else {
	$date_show = date($time_format,time());
}
?>
									<input type="text" name="nca_date" id="nca_date" class="segInput" value="<?= $date_show ?>" style="width:100px" readonly="readonly" />
									<button id="nca_date_trigger" class="segButton" onclick="return false;" <?= $info['id'] ? '' : 'disabled="disabled"' ?>><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
									<script type="text/javascript">
										Calendar.setup ({
											inputField: "nca_date",
											dateFormat: "%B %e, %Y",
											trigger: "nca_date_trigger",
											showTime: false,
											onSelect: function() { this.hide() }
										});
									</script>
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>Date of this NCA issuance</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle" ><strong>MDS Sub-account</strong></td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<input id="mds_subaccount_no" name="mds_subaccount_no" class="segInput" type="text" size="15" value="<?= $info['mds_subaccount_no'] ?>" <?= $info['nca_no'] ? '' : 'disabled="disabled"' ?> />
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>Refer to NCA form</strong>
								</td>
							</tr>
							<tr>
								<td class="segPanel" align="right" valign="middle"><strong>GSB Branch</strong></td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<input id="gsb_branch" name="gsb_branch" class="segInput" type="text" size="15" value="<?= $info['gsb_branch'] ?>" <?= $info['nca_no'] ? '' : 'disabled="disabled"' ?>  />
								</td>
								<td class="segPanel2" align="left" valign="middle" style="">
									<strong>Refer to NCA form</strong>
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
		</div>
	</div>
	</form>
	<div style="width:99%;padding:2px; padding-left:10px">
		<button class="segButton" onclick="save(); return false"><img src="../../gui/img/common/default/disk.png" />Save</button>
		<button class="segButton" onclick="parent.cClick(); return false"><img src="../../gui/img/common/default/cancel.png" />Close</button>
	</div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="entry" value="<?= $_GET['entry'] ?>">

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

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
