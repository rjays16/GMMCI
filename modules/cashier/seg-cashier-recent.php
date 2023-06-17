<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

global $db;
require($root_path."modules/pharmacy/ajax/order.common.php");
include($root_path."include/care_api_classes/class_cashier.php");
$cClass = new SegCashier();

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
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

$title=$LDPharmacy;
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

$thisfile=basename(__FILE__);
if ($_POST['cancelor']) {
	global $db;
	$cancel_ok = $cClass->CancelOR($_POST['cancelor']);
}

if ($_POST['uncancelor']) {
	global $db;
	$uncancel_ok = $cClass->UnCancelOR($_POST['uncancelor']);
}


# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Title in the title bar
$smarty->assign('sToolbarTitle',"Cashier::View Recent");

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Cashier::Archives");


if ($_POST['cancelor']) {
	global $db;
	$db->StartTrans();
	$cancelOK = $cClass->CancelOR($_POST['cancelor']);
	if ($cancelOK) {
		$db->CompleteTrans();
		$smarty->assign('sysInfoMessage','Payment successfully cancelled!');
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		$smarty->assign('sysErrorMessage',$cClass->getErrorMsg());
	}
}

if ($_POST['uncancelor']) {
	global $db;
	$db->StartTrans();
	$uncancelOK = $cClass->UnCancelOR($_POST['uncancelor']);
	if ($uncancelOK) {
		$db->CompleteTrans();
		$smarty->assign('sysInfoMessage','Payment successfully restored!');
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		$smarty->assign('sysErrorMessage',$cClass->getErrorMsg());
	}
}


# Collect javascript code
ob_start()

?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">
function tabClick(el) {
	if (el.className!='segActiveTab') {
		$('mode').value = el.getAttribute('segSetMode');
		var dList = $(el.parentNode);
		if (dList) {
			var listItems = dList.getElementsByTagName("LI");
			for (var i=0;i<listItems.length;i++) {
				if (listItems[i] != el) {
					listItems[i].className = "";
					if ($(listItems[i].getAttribute('segTab'))) $(listItems[i].getAttribute('segTab')).style.display = "none";
				}
			}
			if ($(el.getAttribute('segTab')))
				$(el.getAttribute('segTab')).style.display = "block";
			el.className = "segActiveTab";
		}
	}
}

function showRequest(ref, dept) {
	window.location.href = "seg-cashier-main.php<?= URL_APPEND ?>&clear_ck_sid=$clear_ck_sid&mode=edit&ref="+ref+"&dept="+dept;
}

function tooltip(text) {
	return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
		TEXTPADDING,4, BORDER,0,
		VAUTO, WRAP);
}

function cancelOR(orno) {
	if (confirm('Do you wish to cancel this payment entry?')) {
		$('cancelor').value = orno;
		var form1 = document.forms[0];
		form1.submit();
	}
}

function uncancelOR(orno) {
	if (confirm('Do you wish to restore this cancelled payment entry?')) {
		$('uncancelor').value = orno;
		var form1 = document.forms[0];
		form1.submit();
	}
}

function printOR(orno) {
	url = "seg-cashier-print.php<?= URL_APPEND ?>&clear_ck_sid=<?= $clear_ck_sid ?>&nr="+orno;
	window.open(url,null,"width=800,height=600,menubar=yes,resizable=yes,scrollbars=yes");
}

function editOR(orno) {
	url = "seg-cashier-main.php<?= URL_APPEND ?>&clear_ck_sid=<?= $clear_ck_sid ?>&or="+orno+"&from=recent";
	//window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	window.location.href = url;
}

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

function disableNav() {
	with ($('pageFirst')) {
		className = 'segDisabledLink'
		setAttribute('onclick','')
	}
	with ($('pagePrev')) {
		className = 'segDisabledLink'
		setAttribute('onclick','')
	}
	with ($('pageNext')) {
		className = 'segDisabledLink'
		setAttribute('onclick','')
	}
	with ($('pageLast')) {
		className = 'segDisabledLink'
		setAttribute('onclick','')
	}
}

var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function jumpToPage(el,jumptype, page) {
	if (el.className=="segDisabledLink") return false;
	var form1 = document.forms[0];

	switch (jumptype) {
		case FIRST_PAGE:
			$('jump').value = 'first';
		break;
		case PREV_PAGE:
			$('jump').value = 'prev';
		break;
		case NEXT_PAGE:
			$('jump').value = 'next';
		break;
		case LAST_PAGE:
			$('jump').value = 'last';
		break;
		case SET_PAGE:
			$('jump').value = page;
		break;
	}
	form1.submit();
}

function more(i) {
	var e = $('show_'+i), f = $('more_'+i);
	if (e) {
		if (e.style.display == 'none') {
			e.style.display = '';
			if (f) {
				f.innerHTML = '&laquo;less';
			}
		}
		else {
			e.style.display = 'none';
			if (f) {
				f.innerHTML = 'more&raquo;';
			}
		}
	}
}


function pSearchClose() {
	cClick();
}
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Setup filters
$filters = array();
#$filters["ISCASH"] = 1;
#if (!$_REQUEST['mode']) $_REQUEST['mode'] = 'date';
#if (!$_REQUEST['seldate']) $_REQUEST['seldate'] = 'today';
#if (!$_REQUEST['selpayee']) $_REQUEST['selpayee'] = 'name';

#if ($_REQUEST['seldatedept'])
#	$filters["DEPT"] = $_REQUEST['seldatedept'];
if (strcasecmp($_SESSION['sess_temp_userid'],'admin')) {
	$filters['encoder'] = $_SESSION['sess_temp_userid'];
}

#$filters['DAYSAGO'] = 1;

# Resolve current page (pagination)
$current_page = $_REQUEST['page'];
if (!$current_page) $current_page = 0;
$list_rows = 15;
switch (strtolower($_REQUEST['jump'])) {
	case 'last':
		$current_page = $_REQUEST['lastpage'];
	break;
	case 'prev':
		if ($current_page > 0) $current_page--;
	break;
	case 'next':
		if ($current_page < $_REQUEST['lastpage']) $current_page++;
	break;
	case 'first': default:
		$current_page=0;
	break;
}

$count=0;
$rows = "";
$last_page = 0;
switch(strtolower($_REQUEST["mode"])) {
	# Payee mode
	case "payee":
		switch(strtolower($_REQUEST["selpayee"])) {
			case "name":
				$filters["NAME"] = $_REQUEST["name"];
			break;
			case "pid":
				$filters["PID"] = $_REQUEST["pid"];
			break;
			case "patient":
				$filters["PATIENT"] = $_REQUEST["patient"];
			break;
			case "inpatient":
				$filters["INPATIENT"] = $_REQUEST["inpatient"];
			break;
		}
	break;

	# Date mode
	case "date": case "find" :
		if (strtolower($_REQUEST["mode"])=="date") {
			if ($_REQUEST['seldatedept'])
				$filters["DEPT"] = $_REQUEST['seldatedept'];
			switch(strtolower($_REQUEST["seldate"])) {
				case "today":
					$search_title = "Today's Requests";
					$filters['DATETODAY'] = "";
				break;
				case "thisweek":
					$search_title = "This Week's Requests";
					$filters['DATETHISWEEK'] = "";
				break;
				case "thismonth":
					$search_title = "This Month's Requests";
					$filters['DATETHISMONTH'] = "";
				break;
				case "specificdate":
					$search_title = "Requests On " . date("F j, Y",strtotime($_REQUEST["specificdate"]));
					$dDate = date("Y-m-d",strtotime($_REQUEST["specificdate"]));
					$filters['DATE'] = $dDate;
				break;
				case "between":
					$search_title = "Requests From " . date("F j, Y",strtotime($_REQUEST["between1"])) . " To " . date("F j, Y",strtotime($_REQUEST["between2"]));
					$dDate1 = date("Y-m-d",strtotime($_REQUEST["between1"]));
					$dDate2 = date("Y-m-d",strtotime($_REQUEST["between2"]));
					$filters['DATEBETWEEN'] = array($dDate1,$dDate2);
				break;
			}
		}
		else {
			if ($_REQUEST['selfinddept'])	$filters["DEPT"] = $_REQUEST['selfinddept'];
			if ($_REQUEST['refno']) $filters["REFNO"] = $_REQUEST['refno'];
		}
	break;
}

#$filters["NOCANCEL"] = 1;
require_once $root_path . 'include/care_api_classes/class_acl.php';
$acl = new Acl($_SESSION['sess_temp_userid']);
$canEditOwn = $acl->checkPermissionRaw(array('_a_1_cashiermanageentry', '_a_2_cashiereditown'));
$canEditAll = $acl->checkPermissionRaw(array('_a_1_cashiermanageentry','_a_2_cashiereditall'));
$canCancel = $acl->checkPermissionRaw(array('_a_1_cashiermanageentry','_a_2_cashiercancel'));

$result = $cClass->GetPayments($filters, $list_rows * $current_page, $list_rows);
if ($result) {
	$rows_found = $cClass->FoundRows();
	if ($rows_found) {
		$last_page = floor($rows_found / $list_rows);
		$first_item = $current_page * $list_rows + 1;
		$last_item = ($current_page+1) * $list_rows;
		if ($last_item > $rows_found) $last_item = $rows_found;
		$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
	}
	while ($row=$result->FetchRow()) {
		$name = $row["or_name"];
		if (!$name) $name='<i style="font-weight:normal">No name</i>';
		$class = (($count%2)==0)?"":"alt";

		if (!$row['items'] && $row['deposit']) {
			$row['items'] = "Deposit";
		}
		$span = '<span style="color:#2d2d2d;font:bold 11px Arial">';
		//edited by ken 2/24/2014 for displaying the doctor account
		$items = $span . implode('</span>, '.$span, explode("\n", $row['items']));
		if($row['d_items']&&$row['items']) $items .= ', '.$row['d_items']. '</span>';
		if($row['d_items'] && !$row['items']) $items .= ''.$row['d_items']. '</span>';

		$collection = $row['type_main'];
		if ($row['type_sub']) $collection.="<br/>(".$row['type_sub'].")";
		if ($row['or_date'] && $row['or_date']!='0000-00-00 00:00:00') {
			$ordate = date('Y-m-d h:ia',strtotime($row["or_date"]));
		}

		$cancelColor = "#b62529";

		$template = '<img class="{class}" src="' .  $root_path . 'images/{image}" onclick="{clickFn}" {extras}/>';

		if ($row['cancel_date']) {
			$edit = strtr($template, array(
				'{title}' => 'Edit entry',
				'{class}' => 'link disabled',
				'{image}' => 'cashier_edit.gif',
				'{clickFn}' => 'return false;',
				'{extras}' => 'onmouseover="tooltip(\'Edit entry\')" onmouseout="nd()"'
			));
			$print = strtr($template, array(
				'{title}' => 'Print summary',
				'{class}' => 'link disabled',
				'{image}' => 'cashier_print.gif',
				'{clickFn}' => 'return false;',
				'{extras}' => 'onmouseover="tooltip(\'Print summary\')" onmouseout="nd()"'
			));
			$cancel = strtr($template, array(
				'{title}' => 'Undo cancellation',
				'{class}' => $canCancel ? 'link' : 'disabled',
				'{image}' => 'cashier_uncancel.gif',
				'{clickFn}' => $canCancel ?
					'uncancelOR(\''.$row['or_no'].'\');return false;' :
					'return false;',
				'{extras}' => 'onmouseover="tooltip(\'<span style=color:#008>Undo cancellation</span>\')" onmouseout="nd()"'
			));
		}
		else {
			$allowEdit = ($canEditAll || ($canEditOwn && $row['create_id'] == $_SESSION['sess_temp_userid']));
			$edit = strtr($template, array(
				'{title}' => 'Edit entry',
				'{class}' => $allowEdit ? 'link' : 'disabled',
				'{image}' => 'cashier_edit.gif',
				'{clickFn}' => $allowEdit ?
					'editOR(\''.$row["or_no"].'\');return false' :
					'return false;',
				'{extras}' => 'onmouseover="tooltip(\'Edit entry\')" onmouseout="nd()"'
			));
			$print = strtr($template, array(
				'{title}' => 'Print summary',
				'{class}' => 'link',
				'{image}' => 'cashier_print.gif',
				'{clickFn}' => 'printOR(\''.$row['or_no'].'\');return false;',
				'{extras}' => 'onmouseover="tooltip(\'Print summary\')" onmouseout="nd()"'
			));
			$cancel = strtr($template, array(
				'{title}' => 'Cancel entry',
				'{class}' => $canCancel ? 'link' : 'disabled',
				'{image}' => 'cashier_cancel.gif',
				'{clickFn}' => $canCancel ?
					'cancelOR(\''.$row['or_no'].'\');return false;' :
					'return false;',
				'{extras}' => 'onmouseover="tooltip(\'<span style=color:#aa0000>Cancel entry</span>\')" onmouseout="nd()"'
			));
		}


		$rows .= "		<tr class=\"$class\">
		<td class=\"centerAlign\">".$ordate."</td>
		<td class=\"centerAlign\" style=\"".($row["cancel_date"] ? "color:$cancelColor" : "color:#000080")."\">".
			$row["or_no"].
			($row['cancel_date'] ? '<br><span style="font:bold 11px Tahoma">(Cancelled)</span>' : '').
			"
		</td>
		<td>$name</td>
<!--		<td style=\"font:bold 11px Tahoma\">$collection</td> -->
		<td style=\"color:#000060\">".$items."</td>
		<td align=\"right\" style=\"color:#003000\">".number_format($row['amount_due'],2,'.',',')."</td>
		<td align=\"center\" nowrap=\"nowrap\">
			{$edit}
			{$print}
			{$cancel}
		</td>
	</tr>\n";
	$count++;
	}
}
else {
	$rows = '		<tr><td colspan="10">No orders/requests available at this time...'.$cClass->sql.'</td></tr>';
}

if (!$rows) {
	$rows = '		<tr><td colspan="10">No orders/requests found...</td></tr>';
}

ob_start();
?>

<br>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
	<input type="hidden" id="cancelor" name="cancelor" value="">
	<input type="hidden" id="uncancelor" name="uncancelor" value="">

<div style="width:90%">
	<div class="dashlet">
		<table class="dashletHeader" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="*">
					<h1>Search result: </h1>
				</td>
			</tr>
		</table>
	</div>
	<div class="segContentPane">
		<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" style="">
			<thead>
				<tr class="nav">
					<th colspan="9">
						<div id="pageFirst" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
							<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
							<span title="First">First</span>
						</div>
						<div id="pagePrev" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
							<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
							<span title="Previous">Previous</span>
						</div>
						<div id="pageShow" style="float:left; margin-left:10px">
							<span><?= $nav_caption ?></span>
						</div>
						<div id="pageLast" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
							<span title="Last">Last</span>
							<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
						</div>
						<div id="pageNext" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
							<span title="Next">Next</span>
							<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
						</div>
					</th>
				</tr>
				<tr>
					<th width="8%">Date</th>
					<th width="10%" nowrap="nowrap">OR No.</th>
					<th width="15%">Name</th>
<!--					<th width="15%">Collection</th> -->
					<th width="*">Item(s)</th>
					<th width="8%">Amount</th>
					<th width="1%"></th>
				</tr>
			</thead>
			<tbody>
<?= $rows ?>
			</tbody>
		</table>
	</div>
</div>
<br />

<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">

</form>
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
?>