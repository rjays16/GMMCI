<?php
//created by cha, 12-14-2010
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path."modules/registration_admission/ajax/walkin.common.php";

define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_grants_user';
require_once($root_path.'include/inc_front_chain_lang.php');

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
	var name = "<?echo $_GET['name']?>";
	var id = "<?echo $_GET['id']?>";
	if(name) {
		var nameArray = new Array();
		nameArray = name.split(",");
		$('last_name').value = nameArray[0].toUpperCase();
		$('first_name').value = nameArray[1].toUpperCase();
	}
	else if(id) {
		$('updateBtn').style.display="";
		$('saveBtn').style.display="none";
		xajax_showWalkinDetails(id);
	}
}

function stringToUpper(id, val) {
	$(id).value = val.toUpperCase();
}

function save() {
	if( validate()) {
		xajax.call( 'checkExistingWalkin', { parameters:[xajax.getFormValues('transfer-data')] } );
		return false;
	}
}

function updateWalkin() {
	var rep = confirm("Update walkin details?")
	if (rep) {
				xajax.call( 'updateWalkin', { parameters:[xajax.getFormValues('transfer-data')] } );
			}
}

function registerWalkin() {
	var rep = confirm("Save this walkin?")
	if (rep) {
				xajax.call( 'registerWalkin', { parameters:[xajax.getFormValues('transfer-data')] } );
			}
}

function assignWalkin(pid, name) {
	window.parent.$('pid').value = pid;
	window.parent.$('ordername').value = name;
	window.parent.$('clear-enc').disabled = false;
	window.parent.$('select-enc').disabled = true;
	window.parent.$('reg-walkin').disabled = true;
}

function validate() {
	if($('last_name').value=='') {
		alert("Please specify the last name.")
		$('last_name').focus();
		return false;
	}
	if($('middle_name').value=='') {
		alert("Please specify the middle name.")
		$('middle_name').focus();
		return false;
	}
	if($('first_name').value=='') {
		alert("Please specify the first name.")
		$('first_name').focus();
		return false;
	}
	if($('birthdate').value=='') {
		alert("Please specify the date of birth.")
		$('birthdate').focus();
		return false;
	}
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

</script>
<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Entry date
$dbtime_format = "Y-m-d";
$fulltime_format = "F j, Y";
$curDate = date($dbtime_format,time());
$curDate_show = date($fulltime_format,time());

# Buffer page output
ob_start();

?>
	<form id="transfer-data" style="margin:0">
	<div style="width:98%; padding:5px 0px">
		<table border="0" cellspacing="1" cellpadding="2" width="98%" align="center" style="">
			<tbody>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap"><label>Last Name</label></td>
					<td class="segPanel2" align="left" valign="middle" nowrap="nowrap">
						<input id="last_name" name="last_name" class="segInput" type="text" value="" style="width:100%" onkeyup="stringToUpper(this.id, this.value); return false;" />
					</td>
					<td class="segPanel2" align="left" valign="middle">
						<label>Walkin last name.</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap"><label>First Name</label></td>
					<td class="segPanel2" align="left" valign="middle" style="border-right:0" nowrap="nowrap">
						<input id="first_name" name="first_name" class="segInput" type="text" value="" style="width:100%"  onkeyup="stringToUpper(this.id, this.value); return false;"/>
					</td>
					<td class="segPanel2" align="left" valign="middle" style="border-left:0">
						<label>Walkin first name.</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap"><label>Middle Name</label></td>
					<td class="segPanel2" align="left" valign="middle" nowrap="nowrap">
						<input id="middle_name" name="middle_name" class="segInput" type="text" value="" style="width:100%" onkeyup="stringToUpper(this.id, this.value); return false;"/>
					</td>
					<td class="segPanel2" align="left" valign="middle">
						<label>Walkin middle name.</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap"><label>Sex</label></td>
					<td class="segPanel2" align="left" valign="middle" nowrap="nowrap">
						<input id="gender_m" name="gender" class="segInput" type="radio" checked="checked" value="M"/>Male
						<input id="gender_f" name="gender" class="segInput" type="radio" value="F"/>Female
					</td>
					<td class="segPanel2" align="left" valign="middle">
						<label>Walkin gender.</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" width="18%"><label>Birthday</label></td>
					<td class="segPanel2" align="left" valign="middle" width="30%" nowrap="nowrap">
						<input name="birthdate" id="birthdate" class="segInput" type="text" value="<?= $curDate ?>" size="16" />
						<button id="birthdate_trigger" class="segButton" onclick="return false;"><img <?= createComIcon($root_path,'calendar.png','0') ?>>Set</button>
						<script type="text/javascript">
							Calendar.setup ({
								inputField: "birthdate",
								dateFormat: "%Y-%m-%d",
								trigger: "birthdate_trigger",
								align: 'Cm/Cm/Cm/Cm/Cm',
								showTime: false,
								onSelect: function() { this.hide() }
							});
						</script>
					</td>
					<td class="segPanel2" align="left" valign="middle" width="*">
						<label>Walkin date of birth.</label>
					</td>
				</tr>
				<tr>
					<td class="segPanel" align="right" valign="middle" nowrap="nowrap">
						<label>Address</label>
					</td>
					<td class="segPanel2" align="left" valign="middle" style="border-right:0" nowrap="nowrap">
						<textarea class="segInput" id="address" name="address" rows="2" cols="23" onkeyup="stringToUpper(this.id, this.value); return false;"></textarea>
					</td>
					<td class="segPanel2" align="left" valign="middle" style="border-left:0">
						<label>Walkin address.</label>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="mode" name="mode" value="<?=$_REQUEST["mode"]?$_REQUEST["mode"]:"new"?>">
	<input type="hidden" name="walkin_id" id="walkin_id" value=""/>
	</form>
	<div style="width:98%;padding:0; padding-left:20%; text-align:left">
		<button class="segButton" id="updateBtn" onclick="updateWalkin(); return false;" style="display:none"><img src="../../gui/img/common/default/user_go.png"/>Update</button>
		<button class="segButton" id="saveBtn" onclick="save(); return false;" style="display:"><img src="../../gui/img/common/default/note_go.png"/>Register</button>
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
