<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path."modules/sponsor/ajax/cmap_patient_transfer.common.php";
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_CHAIN',1);
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

#added by cha, june 6, 2010
global $db;
$id = $_REQUEST["entry_id"] ? $_REQUEST["entry_id"]:"";
$sql = "SELECT l.control_nr, l.amount, a.account_nr, a.running_balance `balance`, l.remarks\n".
	"FROM seg_cmap_ledger_patient l\n".
	"INNER JOIN seg_cmap_accounts a ON l.associated_id=a.account_nr\n".
	"WHERE l.pid=".$db->qstr($_REQUEST["pid"])." AND l.entry_id=".$db->qstr($id);
$details = $db->GetRow($sql);


# Get patient information
$db->SetFetchMode(ADODB_FETCH_ASSOC);
//$pid = $_REQUEST['pid'];
//if (strpos($_REQUEST['pid'], 'W') !== FALSE)
//{
//	$pid = substr($pid,1);
//	$person_table = 'seg_walkin';
//}
//else
//	$person_table = 'care_person';
//$query = "SELECT name_last, name_first FROM $person_table WHERE pid=".$db->qstr($pid);
$query = "SELECT fn_get_person_name(".$db->qstr($_REQUEST['pid']).")";
$patient = $db->GetOne($query);
if ($patient === FALSE)
	die('Invalid person ID detected...');

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
.displayTotals {
	text-align:right;
	font-family:Arial;
	font-size:16px;
	font-weight:bold;
}

.displayTotalsLink {
	font-family:Arial;
	font-size:16px;
	font-weight:bold;
	cursor:pointer;
	color:#000066;
}

span.displayTotalsLink:hover {
	text-decoration:underline;
	color:#660000;
}

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
		xajax.call( 'save', { parameters:[ '<?php echo $_REQUEST['pid'] ?>', xajax.getFormValues('transfer-data') ] } );
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
			CLOSETEXT, '<img style=display:none/>', CLOSECLICK,
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

function checkCmapNo(id) {
	if($(id).value!="") {
		xajax_checkExistingCmapNo($(id).value);
	}
}

function checkReferralNo(id) {
	if($(id).value!=""){
		xajax_checkExistingReferralNo($('cmap_account').value, $(id).value);
	}
}

</script>
<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Entry date
$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format,time());
$curDate_show = date($fulltime_format,time());


# Buffer page output
ob_start();

?>
	<form id="transfer-data" style="margin:0">
	<div style="width:100%; padding:10px; -moz-box-sizing:border-box">
		<table border="0" cellspacing="1" cellpadding="2" width="99%" align="center" style="">
			<tbody>
				<tr>
					<td colspan="3" style="-moz-border-radius:4px; background-color:#5795d8; padding: 6px; color: #fff; font: bold 14px 'Arial Narrow'">
						<span><?= strtoupper($patient) ?></span>
						<span style="float:right; font-size:11px; font-weight:normal"><strong>PID:</strong> <?= $_REQUEST['pid'] ?></span></td>
				</tr>
				<tr height="2"></tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" width="18%"><label>Entry date</label></td>
					<td class="segPanel2" align="left" valign="middle" width="30%" nowrap="nowrap">
						<input name="referral_date" id="referral_date" class="segInput" readonly="readonly" type="text" value="<?= $curDate ?>" size="16" />
						<button id="referral_date_trigger" class="segButton" onclick="return false;"><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
						<script type="text/javascript">
							Calendar.setup ({
								inputField: "referral_date",
								dateFormat: "%Y-%m-%d %H:%M",
								trigger: "referral_date_trigger",
								showTime: true,
								onSelect: function() { this.hide() }
							});
						</script>
					</td>
					<td class="segPanel2" align="left" valign="middle" width="*">
						<label>Date of this referral</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap"><label>Account</label></td>
					<td class="segPanel2" align="left" valign="middle" style="border-right:0" nowrap="nowrap">
						<select id="cmap_account" name="cmap_account" class="segInput" onchange="if(this.value!=''){$('referral_nr').disabled=false;}else{$('referral_nr').disabled=true;} xajax.call('getFund', {parameters:[ this.value ]} )">
							<option value="">-- Select CMAP account --</option>
<?php
require_once "{$root_path}include/care_api_classes/sponsor/class_cmap_account.php";
$ac = new SegCMAPAccount();

$result = $ac->get();
while ($row = $result->FetchRow()) {
	if($row["account_nr"]==$details["account_nr"])
	{
	 ?>
		<option value="<?= $row["account_nr"] ?>" selected="selected"><?= $row["account_name"] ?></option>
	 <?php
	}else{
	 ?>
		<option value="<?= $row["account_nr"] ?>"><?= $row["account_name"] ?></option>
	 <?php
	}
}
?>
						</select>
					</td>
					<td class="segPanel2" align="left" valign="middle" style="border-left:0">
						<label>CMAP Account</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap"><label>Referral Ctrl No.</label></td>
					<td class="segPanel2" align="left" valign="middle" nowrap="nowrap">
						<input id="referral_nr" name="referral_nr" class="segInput" type="text" value="<?=$details["referral_nr"]?>" style="width:100%" onblur="checkReferralNo(this.id);"  disabled="disabled"/>
					</td>
					<td class="segPanel2" align="left" valign="middle">
						<label>Referral control number (optional)</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap"><label>CMAP Ctrl No.</label></td>
					<td class="segPanel2" align="left" valign="middle" nowrap="nowrap">
						<input id="control_nr" name="control_nr" class="segInput" type="text" value="<?=$details["control_nr"]?>" style="width:100%" onblur="checkCmapNo(this.id);" />
					</td>
					<td class="segPanel2" align="left" valign="middle">
						<label>Assigned CMAP control number (optional)</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap"><label>CMAP fund</label></td>
					<td class="segPanel2" align="left" valign="middle" style="border-right:0" nowrap="nowrap">
						<input id="show_fund" class="segInput" type="text" value="<?=$details['balance']?$details['balance']:'0.00'?>" size="15" style="text-align:right; font:bold 12px Tahoma" readonly="readonly">
						<input id="fund" name="fund" type="hidden" value="<?=$details['balance']?$details['balance']:'0.00'?>" />
					</td>
					<td class="segPanel2" align="left" valign="middle" stylse="border-left:0">
						<label>Current running balance for the CMAP account</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap">
						<label>Amount</label>
					</td>
					<td class="segPanel2" align="left" valign="middle" style="border-right:0" nowrap="nowrap">
						<input id="show_amount" class="segInput" type="text" value="<?=$details['amount']?$details['amount']:'0.00'?>" size="15" readonly="readonly" style="text-align:right; font:bold 12px Tahoma" />
						<input id="amount" name="amount" type="hidden" value="<?=$details['amount']?$details['amount']:'0.00'?>" />
						<input id="amount_orig" name="amount_orig" type="hidden" value="<?=$details['amount']?$details['amount']:'0.00'?>" />
						<button class="segButton" onclick="enterAmount(); return false;"><img src="../../gui/img/common/default/money_add.png"/>Set</button>
					</td>
					<td class="segPanel2" align="left" valign="middle" style="border-left:0">
						<label>Referral amount</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap">
						<label>Remarks</label>
					</td>
					<td class="segPanel2" align="left" valign="middle" style="border-right:0" nowrap="nowrap">
						<textarea class="segInput" id="remarks" name="remarks" rows="2" cols="23"><?echo $details["remarks"];?></textarea>
					</td>
					<td class="segPanel2" align="left" valign="middle" style="border-left:0">
						<label>Additional notes/comments</label>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="mode" name="mode" value="<?=$_REQUEST["mode"]?$_REQUEST["mode"]:"new"?>">
	</form>
	<div style="width:98%;padding:0; padding-left:20%; text-align:left">
		<button class="segButton" onclick="save(); return false;"><img src="../../gui/img/common/default/disk.png"/>Save referral</button>
		<button class="segButton" onclick="parent.cClick(); return false;"><img src="../../gui/img/common/default/cancel.png"/>Close</button>
	</div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">

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
