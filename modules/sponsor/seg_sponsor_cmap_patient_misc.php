<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/sponsor/ajax/cmap_patient_request.common.php");

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
 #$title="Sponsor grants";

if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-sponsor-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$thisfile='seg_sponsor_cmap_patient_request.php';

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/sponsor/class_cmap.php");
$cc = new SegCMAP;

include_once($root_path."include/care_api_classes/sponsor/class_cmap_patient.php");
$pc = new SegCMAPPatient;

global $db;

$Nr = $_GET['nr'];

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

$smarty->assign('QuickMenu', FALSE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('bHideTitleBar',TRUE);

# Assign Body Onload javascript code
/*
if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {

	$total = 0;
	$bulk = array();
	foreach ($_POST["item"] as $i=>$v) {
		$bulk[] = array(
			$_POST["src"][$i],
			$_POST["ref"][$i],
			$_POST["item"][$i],
			$_POST["service"][$i],
			$_POST["qty"][$i],
			$_POST['amount'][$i],
		);
		$total += (float) $_POST['amount'][$i];
	}

	$saveok = TRUE;

	# check patient Balance
	$bal = $pc->getBalance($_POST['pid']);
	if ($total > (float) $bal) {
		$error_message = 'Total price of items (P'.number_format($total,2).') exceeds the current balance for this patient (P'.number_format($bal,2).')';
		$saveok = FALSE;
	}

	$db->StartTrans();
	if ($saveok) {
		$data = array(
			'control_nr'=>$_POST['control_nr'],
			'encounter_nr'=>$_POST['encounter_nr'],
			'pid'=>$_POST['pid'],
			'name'=>$_POST['name'],
			'address'=>$_POST['address'],
			'entry_date'=>$_POST['entry_date'],
			'remarks'=>$_POST['remarks'],
			'modify_id'=>$_SESSION['sess_temp_userid']
		);

		$cc->setDataArray($data);

		if ($_POST['mode']=='edit') {
	//    $data["history"]=$lc->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
	//    $lc->setDataArray($data);
	//    $lc->where = "control_nr=".$db->qstr($_POST['control_nr']);
	//    $saveok=$lc->updateDataFromInternalArray($_POST['control_nr'],FALSE);
			$error_message = 'Editing of CMAP  entries is disallowed...';
			$saveok = FALSE;
		}
		else {
			$data['create_id']=$_SESSION['sess_temp_userid'];
			$data['history']="Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n";
			$cc->setDataArray($data);

			$error_message = 'Unable to save CMAP entry information...';
			$saveok = $cc->insertDataFromInternalArray();
		}
	}

	if ($saveok) {
		$id=$db->Insert_ID();
		# Bulk write entry items

		$error_message = 'Unable to update CMAP entry details...';
		$cc->clearEntry($id);
		$saveok=$cc->addDetails($id, $bulk);
	}

	if ($saveok) {
		// Update patient ledger
		$error_message = 'Unable to update patient ledger for CMAP...';

		$ledger_data = array();
		$ledger_data['entry_data'] = $data['pid'];
		$ledger_data['control_nr'] = $data['control_nr'];
		$ledger_data['pid'] = $data['pid'];
		$ledger_data['entry_type'] = 'grant';
		$ledger_data['amount'] = $total;
		$ledger_data['remarks'] = $data['remarks'];
		$ledger_data['history'] = "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n";
		$ledger_data['modify_id'] = $_SESSION['sess_temp_userid'];
		$ledger_data['modify_time'] = date('YmdHis');
		$ledger_data['create_id']=$_SESSION['sess_temp_userid'];
		$ledger_data['create_time']=date('YmdHis');

		$pc->setDataArray($ledger_data);
		$saveok=$pc->insertDataFromInternalArray();
	}

	if ($saveok) {
		$error_message = 'Unable to update account balance...';
		$saveok = $pc->updateBalance($data['pid'], 'grant', $total);
	}

	if ($saveok) {
		$smarty->assign('sysInfoMessage','CMAP entry successfully saved!');
		$db->CompleteTrans();
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();

		if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
			$smarty->assign('sysErrorMessage','An entry with the same control number already exists in the database.');
		else {
			if (defined('DEBUG'))
				$smarty->assign('sysErrorMessage','Description:'.$error_message.'<br> SQLError:'.$db->ErrorMsg());
			else
				$smarty->assign('sysErrorMessage',$error_message);
			#print_r($order_obj->sql);
		}
	}
}
*/

# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>

<!-- Core module and plugins:
-->
<link href="<?=$root_path?>js/prototypeui/themes/window/window.css" rel="stylesheet" type="text/css">
<link href="<?=$root_path?>js/prototypeui/themes/window/alphacube.css" rel="stylesheet" type="text/css">
<link href="<?=$root_path?>js/prototypeui/themes/window/lighting.css" rel="stylesheet" type="text/css">
<link href="<?=$root_path?>js/prototypeui/themes/shadow/mac_shadow.css" rel="stylesheet" type="text/css">
<style type='text/css'>

	.message {
		font-family: Georgia;
		text-align: center;
		margin-top: 20px;
	}

	.spinner {
		background: url(../../images/spinner.gif) no-repeat center center;
		height: 40px;
	}

	.container {
		font: normal 12px Tahoma;
		padding: 4px 5px;
		margin: 0;
		background-color: #E4E9F4;
		border: 5px solid #4E8CCF;
		border-width: 5px 0px;
		-moz-border-radius: 0px 0px 6px 6px;
	}

	.container h1 {
		display: none;
		background-repeat: no-repeat;
		font-family: Tahoma;
		font-size: 18px;
		font-weight: normal;
		color: #580408;
		vertical-align:middle;
		margin: 0;
		padding: 0;
		padding-top: 6px;
		padding-left: 36px;
		height: 30px;
	}

	.container p {
		font: normal 11px Tahoma;
		color: #2d2d2d;
		margin: 3px 0px;
	}

	.errorfg {
		background-color: #cccccc;
	}

	.clearbg {
		background-color: transparent;
		border: 0;
	}

	.clearcg {
		background-color: transparent;
		background-image: none;
		text-align:center;
		margin:0;
	}

	.clearcgif {
		background-color: transparent;
		text-align: center;
	}

	.clearfg {
		background-color: transparent;
		text-align: center;
	}

	.clearfgif {
		background-color: none;
		text-align: center;
	}

	.clearcap {
		display: none;
		font-family:Tahoma;
		font-size:11px;
		font-weight:bold;
		color:white;
		margin-top:0px;
		margin-bottom:1px;
	}

	a.clearclo {
		display: none;
		font-family:Verdana;
		font-size:11px;
		font-weight:bold;
		color:#ddddff;
	}

	.cleartext {
		font:bold 11px Tahoma;
		color:#2d2d2d;
	}
</style>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/prototypeui/window.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/seg_alerts.js?t=<?=time()?>"></script>
<script type="text/javascript" src="js/cmap_patient_request.js?t=<?=time()?>"></script>
<script type="text/javascript" language="javascript">
<!--
	eraseCookie('__cmap_ck');

	var glst, grid, buffer,
		isLoading=false

	// callback triggered when Patient Search window is closed
	function pSearchClose() {
		//$('select-enc').className = ($('pid').value == '') ? 'segSimulatedLink' : 'segDisabledLink';
		if ($('pid').value) $('select-enc').removeClassName('link').addClassName('disabled');
		else $('select-enc').addClassName('link').removeClassName('disabled');

		//$('rqsearch').show();
		search();
		cClick();
	}

	function search() {
		var o = new Object();
		if (!$('pid').value) return false;
		o['pid'] = $('pid').value;
		o['date'] = $('date_request').value;
		if (typeof(rlst)=='object') {
			rlst.fetcherParams = o;
			rlst.reload();
		}
	}

	function startLoading() {
		if (!isLoading) {
			isLoading = 1;
			return overlib('<img src="../../images/loading6.gif"/>',
				WIDTH,300, TEXTPADDING,5, BORDER,0,
				SHADOW, 0,
				MODALCOLOR, '#ffffff',
				MODALOPACITY, 80,
				FGCLASS, 'clearfg',
				CGCLASS, 'clearcg',
				BGCLASS, 'clearbg',
				TEXTFONTCLASS, 'cleartext',
				CAPTIONFONTCLASS, 'clearcap',
				CLOSEFONTCLASS, 'clearclo',
				STICKY, MODAL,
				CLOSECLICK, TIMEOUT, 0, OFFDELAY, 0,
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

	function clearErroneousInputs( ) {
		var clearErrors = $$('.errorInput');
		if (clearErrors) clearErrors.each( function(x) { x.removeClassName('errorInput') } );
	}

	function validate() {
		clearErroneousInputs();

		if (!$('control_nr').value) {
			$('control_nr').addClassName('errorInput').focus();
			alert('Enter the Control Number for this entry...');
			return false;
		}

		if (!$$('[name="amount[]"]').length) {
			alert('No items selected...');
			return false;
		}

		return true;
	}

	function openGrant(args) {
		var wm = UI.defaultWM;
		var windows = wm.windows();

		// Reminder: check for duplicate windows

		var w = new UI.Window({
			width: 360,
			height: 220,
			shadow: true,
			minimize: false,
			maximize: false,
			resizable: false,
			draggable: false,
			show: Element.appear,
			hide: Element.fade,
			method: "GET",
		}).center();
		w.content.update('<div class="message">Please wait...</div><div class="spinner"></div>');
		var wSize = w.getSize();

		if (windows.length) {
			var pos = w.getPosition();
			w.setPosition(pos.top+windows.length*10,pos.left+windows.length*10);
		}
		w.show();
		w.setAjaxContent('ajax/cmap_grant.ajax.php?wid='+w.id+'&src='+args.src+'&nr='+args.nr+'&code='+args.code, { method: "GET" });
		w.activate();
	}

	function closeGrant(wId) {
		var wm = UI.defaultWM;
		var windows = wm.windows();
		windows.each(function(win) { if (win.id == wId) win.destroy(); });
	}

	function save(wId) {
		if (validate()) {
			startLoading();
			xajax.call( 'save', { parameters:[wId, xajax.getFormValues(wId+'_transfer_data')] } );
		}
	}

	function validate() {
		return true;
	}

	function tooltip (text) {
		return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
	}

	function partialAmount(wId) {
		var amt;
		var balance=$(wId+'_referral').value;
		var due=$(wId+'_due').value;
		while (isNaN(amt)) {
			amt = prompt('Enter amount to be transferred:');
			if (amt===null) return false;
			if (parseFloatEx(amt) > parseFloatEx(balance)) amt=balance;
			if (parseFloatEx(amt) > parseFloatEx(due)) amt=due;
		}

		$(wId+'_amount').value = amt;
		$(wId+'_show_amount').value = formatNumber(amt,2);
	}

	function fullAmount(wId) {
		var balance=parseFloatEx($(wId+'_referral').value);
		var due=parseFloatEx($(wId+'_due').value);
		var amt;

		amt = balance;
		if (amt>due) amt=due;

		$(wId+'_amount').value = amt;
		$(wId+'_show_amount').value = formatNumber(amt,2);
	}

	function search() {
		//if (/\d{4}-\d{2}-\d{2}/.match(this.value)) { $(this).setStyle({color:\'\'}); if(rlst.fetcherParams) rlst.fetcherParams[\'date\']=this.value;rlst.reload(); } else { $(this).setStyle({color:\'red\'}); }
		var o = new Object;
		o['PID'] = '<?= htmlentities($_GET['pid']) ?>';
		if ($('basic-source').value) {
			o['FILTER_SOURCE'] = $('basic-source').value;
		}
		if ($('basic-name').value) {
			o['FILTER_NAME'] = $('basic-name').value;
		}
		if ($('date_request').value) {
			o['FILTER_DATE'] = $('date_request').value;
		}
		if ($('basic-grant').value) {
			o['FILTER_GRANT'] = $('basic-grant').value;
		}
		rlst.fetcherParams = o;
		rlst.reload();
		return false;
	}
-->
</script>
<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Setup dyynamic lists
$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', FALSE);

$rlst = $listgen->createList(
	array(
		'LIST_ID' => 'rlst',
		'COLUMN_HEADERS' => array('Date','Source','Reference','Item name','Qty','Total due','Grant'),
		'COLUMN_SORTING' => array(LG_SORT_DESC, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_NONE, LG_SORT_UNSORTABLE),
		'AJAX_FETCHER' => 'populatePatientRequestList',
		'INITIAL_MESSAGE' => "Please select a patient first...",
		'EMPTY_MESSAGE' => "No cost center requests found for this patient...",
		'ADD_METHOD' => 'addPatientRequest',
		'FETCHER_PARAMS' => array('PID'=>$_GET['pid']),
		'RELOAD_ONLOAD' => TRUE,
		'COLUMN_WIDTHS' => array("12%", "8%", "14%", "*", "8%", "12%", "10%")
	)
);
$smarty->assign('lstRequest',$rlst->getHTML());

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$title = 'CMAP :: Process CMAP request entry';

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

# Controls
$smarty->assign('sControlNo','<input id="control_nr" name="control_nr" class="segInput" type="text" value="'.$_POST["control_nr"].'" />');
//$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_GET["encounter_nr"].'"/>');
$smarty->assign('sPatientID','<input id="pid" name="pid" class="segInput" type="text" value="'.$_GET['pid'].'" readonly="readonly"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="font:bold 12px Arial;" readonly="readonly" value="'.$_GET['name'].'"/>');
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="30" rows="2" style="">'.$_POST['remarks'].'</textarea>');

$dbtime_format = "Y-m-d";
$curDate = date($dbtime_format);
$smarty->assign('sRequestFilterDate','
	<input class="segInput" name="date_request" id="date_request" type="text" size="8" value="" onchange=""/>
	<img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_date_request" align="absmiddle" class="link"  />
	<script type="text/javascript">
		Calendar.setup ({
			inputField : "date_request", ifFormat : "%Y-%m-%d", showsTime : false, button : "tg_date_request", singleClick : true, step : 1
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
$curDate.'" style="font:bold 12px Arial">');

if ($view_only)
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
else {
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entry_date_trigger" class="link" align="absmiddle" style="margin-left:2px;cursor:pointer">');
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

# Totals
require_once "{$root_path}include/care_api_classes/sponsor/class_cmap_patient.php";
$pc = new SegCMAPPatient;
$smarty->assign('sAccountBalance', $pc->getBalance($_GET['pid']));

$smarty->assign('sCoverageTotal',0);


# Save/Cancel buttons
$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder.gif" align="center">');
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
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
	<input type="hidden" id="refno" name="refno" value="">
	<input type="hidden" id="refsource" name="refsource" value="">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="link" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="link" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','sponsor/cmap_patient_request.tpl');
$smarty->display('common/mainframe.tpl');

