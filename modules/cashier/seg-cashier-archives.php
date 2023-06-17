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

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
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
 $smarty->assign('sToolbarTitle',"Cashier::OR Master Archives");



 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::OR Master Archives");



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


if ($_POST['deleteor']) {
	global $db;
	$db->StartTrans();
	$uncancelOK = $cClass->DeleteOR($_POST['deleteor']);
	if ($uncancelOK) {
		$db->CompleteTrans();
		$smarty->assign('sysInfoMessage','Payment successfully deleted!');
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
<style type="text/css">
.tabFrame table {
	font-family: Arial;
}
</style>
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

	/*
	function printOR(orno) {
		url = "seg-cashier-print.php<?= URL_APPEND ?>&clear_ck_sid=<?= $clear_ck_sid ?>&nr="+orno;
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
	*/

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

			case 'payor':
				if ($F('selpayor') == "name") {
					var strname = $('name').value;
					strname = strname.toUpperCase();
					var bvalid = (
									/^[A-Z0-9Ñ\-\. ]{3}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(strname) ||
									/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(strname) ||
									/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(strname) ||
									/^\d+$/.test(strname)
					);
					if (!bvalid) alert('You have to enter at least 3 chars in lastname\nthen comma and at least 2 chars in firstname!');
					return bvalid;
				}
			break;

			default:
			break;
		}
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

function deleteOR(orno) {
	if (confirm('Do you wish to DELETE this payment entry?')) {
		$('deleteor').value = orno;
		var form1 = document.forms[0];
		form1.submit();
	}
}

	function printOR(orno) {
		url = "seg-cashier-print.php<?= URL_APPEND ?>&clear_ck_sid=<?= $clear_ck_sid ?>&nr="+orno;
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}

	function editOR(orno) {
		url = "seg-cashier-main.php<?= URL_APPEND ?>&clear_ck_sid=<?= $clear_ck_sid ?>&or="+orno+"&from=recent";
		//window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
		window.location.href = url;
	}

	function tooltip(text) {
		return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
		TEXTPADDING,2, BORDER,0,
		VAUTO, WRAP,
		BGCLASS,'olTooltipBG',
		FGCLASS,'olTooltipFG',
		TEXTFONTCLASS,'olTooltipTxt'
	);
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


	function pSearchClose() {
		cClick();
	}

	//added by maimai 01-01-2014
	function printTransactions(orno){
		url = "seg-cashier-all-transactions-pdf.php<?= URL_APPEND ?>&clear_ck_sid=<?= $clear_ck_sid ?>&or="+orno+"&from=recent";
		window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
	//end maimai
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);


# Setup filters
$filters = array();
$filters["ISCASH"] = 1;
if (!$_REQUEST['mode']) $_REQUEST['mode'] = 'date';
if (!$_REQUEST['seldate']) $_REQUEST['seldate'] = 'today';
if (!$_REQUEST['selpayor']) $_REQUEST['selpayor'] = 'name';

if ($_REQUEST['seldatedept'])
	$filters["DEPT"] = $_REQUEST['seldatedept'];


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
	# Payor mode
	case "payor":
		switch(strtolower($_REQUEST["selpayor"])) {
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

	case 'orno':
			if($_REQUEST['or_number'])
				$filters["ORNO"] = $_REQUEST['or_number'];
	break;
}

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
		$name = strtoupper($row["or_name"]);
		if (!$name) $name='<i style="font-weight:normal">No name</i>';

		if (substr($row['pid'],0,1)=='W') {
			$show_pid = "<span style=\"font-size:11px; color:#000080\">Walkin PID:".substr($row['pid'],1)."</span>";
		}
		else
			$show_pid = "<span style=\"font-size:11px; color:#000080\">PID:".$row['pid']."</span>";
		$name .= "<br/>".$show_pid;

		$class = (($count%2)==0)?"":"wardlistrow2";
		if ($row['or_date'] && $row['or_date']!='0000-00-00 00:00:00') {
			$ordate = date('Y-m-d h:ia',strtotime($row["or_date"]));
		}

		$items = explode("\n",$row["items"]);
		foreach ($items as $i=>$v) {
			$items[$i] = "<span style=\"\">$v</span>";
		}
		$items = implode(", ",$items);
		$cancelColor = "#b62529";
             $template = '<img class="{class}" src="' .  $root_path . 'images/{image}" onclick="{clickFn}" {extras}/>';


        //added by maimai 01-01-2014
        $print_summary = strtr($template, array(
            '{title}' => 'Print Cash Transactions',
            '{class}' => 'link',
            '{image}' => 'cashier_reports.gif',
            '{clickFn}' => 'printTransactions(\''.$row["or_no"].'\');return false',
            '{extras}' => 'onmouseover="tooltip(\'Print Cash Transactions\')" onmouseout="nd()"'
        ));
        //end maimai

		if ($row['cancel_date']) {
			//$edit 	= "<img title=\"Edit\" class=\"disabled\" src=\"".$root_path."images/cashier_edit.gif\" onclick=\"return false\" style=\"opacity:0.2\" style=\"margin:1px\" />\n";
//            $print = "<img title=\"Print\" class=\"disabled\" src=\"".$root_path."images/cashier_print.gif\" onclick=\"return false\" style=\"opacity:0.2\" style=\"margin:1px\" />\n";
//			$cancel = "<input title=\"Uncancel\" class=\"link\" type=\"image\" src=\"".$root_path."images/cashier_uncancel.gif\" onclick=\"uncancelOR('".$row["or_no"]."');return false;\"  onmouseover=\"tooltip('<span style=color:blue>Un</span>cancel OR')\" onmouseout=\"nd()\"/>\n";
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
			//$edit 	= "<input title=\"Edit\" class=\"link\" type=\"image\" src=\"".$root_path."images/cashier_edit.gif\" onclick=\"editOR('".$row["or_no"]."');return false;\" onmouseover=\"tooltip('Edit entry')\" onmouseout=\"nd()\"/>";
//			$print 	= "<input title=\"Print\" class=\"link\" type=\"image\" src=\"".$root_path."images/cashier_print.gif\" onclick=\"printOR('".$row["or_no"]."');return false;\"  onmouseover=\"tooltip('Print summary')\" onmouseout=\"nd()\"/>";
//			$cancel = "<input title=\"Cancel\" class=\"link\" type=\"image\" src=\"".$root_path."images/cashier_cancel.gif\" onclick=\"cancelOR('".$row["or_no"]."');return false;\"  onmouseover=\"tooltip('Cancel OR')\" onmouseout=\"nd()\"/>";
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

		global $allowedarea;

		$allowedarea = array('_a_2_cashierdelete');
		if (validarea($_SESSION['sess_permission'],1)) {
			$delete = "<img title=\"Delete\" class=\"link\" src=\"".$root_path."images/cashier_delete.gif\" onclick=\"deleteOR('".$row["or_no"]."');return false;\"  onmouseover=\"tooltip('Delete OR')\" onmouseout=\"nd()\" />";
		}
		else {
			$delete = "<img title=\"Delete\" class=\"disabled\" src=\"".$root_path."images/cashier_delete.gif\" onclick=\"return false;\"  onmouseover=\"tooltip('Delete OR')\" onmouseout=\"nd()\" style=\"opacity:0.2\" />";
		}


		$rows .= "		<tr class=\"$class\">
		<td style=\"".($row["cancel_date"] ? "color:$cancelColor" : "color:#2d2d2d")."; font:bold 11px Tahoma\" align=\"center\">".$ordate."</td>
		<td class=\"centerAlign\" style=\"".($row["cancel_date"] ? "color:$cancelColor" : "color:#000080")."\">".
			$row["or_no"].
			($row['cancel_date'] ? '<br><span style="font:bold 11px Tahoma">(Cancelled)</span>' : '').
			"
		</td>
		<td style=\"".($row["cancel_date"] ? "color:$cancelColor" : "color:#2d2d2d")."\">".$name."</td>
		<!--
		<td></td>
		<td></td>
		-->
		<td style=\"font-size:11px;".($row["cancel_date"] ? "color:$cancelColor" : "color:#000060")."\">".$items."</td>
		<td align=\"right\" style=\"color:#003000\">".number_format($row['amount_due'],2,'.',',')."</td>
		<td align=\"center\" nowrap=\"nowrap\">
			{$edit}
			{$print}
			{$print_summary}
			{$cancel}
			{$delete}
		</td>
	</tr>\n";
	$count++;
	}
}
else {
	$rows = '		<tr><td colspan="10">No orders/requests available at this time...'.$cClass->sql.'</td></tr>';
}

if (!$rows) {
	$rows = '		<tr><td colspan="6">No orders/requests found...</td></tr>';
}

ob_start();
?>

<br>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">

<div style="width:95%">
	<ul id="request-tabs" class="segTab" style="padding-left:10px; border-left:1px solid white">
		<li <?= strtolower($_REQUEST['mode'])=='date' ? 'class="segActiveTab"' : '' ?> onclick="tabClick(this)" segTab="tab0" segSetMode="date">
			<h2 class="segTabText">Search By Date</h2>
		</li>
		<li <?= strtolower($_REQUEST['mode'])=='payor' ? 'class="segActiveTab"' : '' ?> onclick="tabClick(this)" segTab="tab1" segSetMode="payor">
			<h2 class="segTabText">Search By Payor</h2>
		</li>
		<li <?= strtolower($_REQUEST['mode'])=='orno' ? 'class="segActiveTab"' : '' ?> onclick="tabClick(this)" segTab="tab2" segSetMode="orno">
			<h2 class="segTabText">Search By O.R. #</h2>
		</li>
<!--		<li <?= strtolower($_REQUEST['mode'])=='find' ? 'class="segActiveTab"' : '' ?> onclick="tabClick(this)" segTab="tab2" segSetMode="find">
			<h2 class="segTabText">Find Request</h2>
		</li>-->
		&nbsp;
	</ul>

	<div class="segTabPanel" style="width:100%;">
		<div id="tab0" class="tabFrame" <?= ($_REQUEST["mode"]=="date" || !$_REQUEST['mode']) ? '' : 'style="display:none"' ?>>
			<table cellpadding="2" cellspacing="2" border="0">
				<tbody>
					<tr>
						<td width="10%" nowrap="nowrap" align="right">Select date</td>
						<td>
<script type="text/javascript">
	function seldateOnChange() {
		var optSelected = $('seldate').options[$('seldate').selectedIndex];
		var spans = document.getElementsByName('seldateoptions');
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
</script>
							<select class="segInput" id="seldate" name="seldate" onchange="seldateOnChange()">
								<option value="today" <?= $_REQUEST["seldate"]=="today" ? 'selected="selected"' : '' ?>>Today</option>
								<option value="thisweek" <?= $_REQUEST["seldate"]=="thisweek" ? 'selected="selected"' : '' ?>>This week</option>
								<option value="thismonth" <?= $_REQUEST["seldate"]=="thismonth" ? 'selected="selected"' : '' ?>>This month</option>
								<option value="specificdate" <?= $_REQUEST["seldate"]=="specificdate" ? 'selected="selected"' : '' ?>>Specific date</option>
								<option value="between" <?= $_REQUEST["seldate"]=="between" ? 'selected="selected"' : '' ?>>Between</option>
							</select>
							<span name="seldateoptions" segOption="specificdate" <?= ($_REQUEST["seldate"]=="specificdate") ? '' : 'style="display:none"' ?>>
								<input class="segInput" name="specificdate" id="specificdate" type="text" size="8" value="<?= $_REQUEST['specificdate'] ?>"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "specificdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
									});
								</script>
							</span>
							<span name="seldateoptions" segOption="between" <?= ($_REQUEST["seldate"]=="between") ? '' : 'style="display:none"' ?>>
								<input class="segInput" name="between1" id="between1" type="text" size="8" value="<?= $_REQUEST['between1'] ?>"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "between1", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between1", singleClick : true, step : 1
									});
								</script>
								to
								<input class="segInput" name="between2" id="between2" type="text" size="8" value="<?= $_REQUEST['between2'] ?>"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "between2", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between2", singleClick : true, step : 1
									});
								</script>
							</span>
						</td>
						<td nowrap="nowrap"></td>
					</tr>
<!--					<tr>
						<td align="right" nowrap="nowrap">Select department</td>
						<td>
							<select class="segInput" id="seldatedept" name="seldatedept">
								<option value="">All</option>
<?php
	$depts = array(
		'ld'=>'Laboratory',
		'ph'=>'Pharmacy',
		'rd'=>'Radiology',
		'misc'=>'Miscellaneous'
	);

	foreach ($depts as $i=>$v) {
		$sel = (strtolower($_REQUEST['seldatedept']) == strtolower($i)) ? 'selected="selected"' : '';
		echo "								<option value=\"$i\" $sel>$v</option>\n";
	}
?>
							</select>
						</td>
					</tr> -->
					<tr>
						<td align="right">
							<button class="segButton"><img src="<?= $root_path ?>gui/img/common/default/magnifier.png">Search</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="tab1" class="tabFrame" <?= ($_REQUEST["mode"]=="payor") ? '' : 'style="display:none"' ?>>
			<table cellpadding="2" cellspacing="2" border="0">
				<tbody>
					<tr>
						<td width="10%" align="right" nowrap="nowrap">Select payor<br />search mode</td>
						<td>
<script type="text/javascript">
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
</script>
							<select class="segInput" name="selpayor" id="selpayor" onchange="selpayorOnChange()"/>
								<option value="name" <?= ($_REQUEST['selpayor']=='name' ? 'selected="selected"' : '')?>>Payor Name</option>
								<option value="pid" <?= ($_REQUEST['selpayor']=='pid' ? 'selected="selected"' : '')?>>Patient ID</option>
								<option value="patient" <?= ($_REQUEST['selpayor']=='patient' ? 'selected="selected"' : '')?>>Patient Records</option>
								<option value="inpatient" <?= ($_REQUEST['selpayor']=='inpatient' ? 'selected="selected"' : '')?>>Inpatient/ER/OPD</option>
							</select>
							<span name="selpayoroptions" segOption="name" <?= ($_REQUEST["selpayor"]=="name") ? '' : 'style="display:none"' ?>>
								<input class="segInput" name="name" id="name" type="text" size="20" value="<?= $_REQUEST['name'] ?>"/>
							</span>
							<span name="selpayoroptions" segOption="pid" <?= ($_REQUEST["selpayor"]=="pid") ? '' : 'style="display:none"' ?>>
								<input class="segInput" name="pid" id="pid" type="text" size="20" value="<?= $_REQUEST['pid'] ?>"/>
							</span>
							<span name="selpayoroptions" segOption="patient" <?= ($_REQUEST["selpayor"]=="patient") ? '' : 'style="display:none"' ?>>
								<input class="segInput" name="patientname" id="patientname" readonly="readonly" type="text" value="<?= $_REQUEST['patientname'] ?>"/>
								<input name="patient" id="patient" type="hidden" value="<?= $_REQUEST['patient'] ?>"/>
							<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
				       onclick="overlib(
				        OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?var_pid=patient&var_name=patientname', 700, 400, 'fSelEnc', 0, 'auto'),
				        WIDTH,700, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL,
								CLOSETEXT, '<img src=<?= $root_path ?>images/close_red.gif border=0 >',
								CAPTIONPADDING,2,
								CAPTION,'Select registered person',
				        MIDX,0, MIDY,0,
			        	STATUS,'Select registered person'); return false;"
      				 onmouseout="nd();" />
							</span>
							<span name="selpayoroptions" segOption="inpatient" <?= ($_REQUEST["selpayor"]=="inpatient") ? '' : 'style="display:none"' ?>>
								<input class="segInput" name="inpatientname" id="inpatientname" readonly="readonly" type="text" value="<?= $_REQUEST['inpatientname'] ?>"/>
								<input name="inpatient" id="inpatient" type="hidden" value="<?= $_REQUEST['inpatient'] ?>"/>
							<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
				       onclick="overlib(
				        OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?var_encounter_nr=inpatient&var_name=inpatientname&var_include_enc=1', 700, 400, 'fSelEnc', 0, 'auto'),
				        WIDTH,700, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL,
								CLOSETEXT, '<img src=<?= $root_path ?>images/close.gif border=0 >',
				        CAPTIONPADDING,4,
								CAPTION,'Select registered person',
				        MIDX,0, MIDY,0,
			        	STATUS,'Select registered person'); return false;"
      				 onmouseout="nd();" />
							</span>
						</td>
					</tr>
					<tr>
						<td align="right">
							<button class="segButton"><img src="<?= $root_path ?>gui/img/common/default/magnifier.png">Search</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="tab2" class="tabFrame" <?= ($_REQUEST["mode"]=="orno") ? '' : 'style="display:none"' ?>>
			<table cellpadding="2" cellspacing="2" border="0">
					<tbody>
						<tr>
							<td width="15%" align="left">O.R. number</td>
							<td>
								<input class="segInput" name="or_number" id="or_number" type="text" value="<?= $_REQUEST['or_number'] ?>" size="20" />
							</td>
						</tr>
						<tr>
						<td align="left">
							<button class="segButton"><img src="<?= $root_path ?>gui/img/common/default/magnifier.png">Search</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
<!--
		<div id="tab2" class="tabFrame" <?= ($_REQUEST["mode"]=="find") ? '' : 'style="display:none"' ?>>
			<table cellpadding="2" cellspacing="2" border="0">
				<tbody>
					<tr>
						<td width="15%" align="right" nowrap="nowrap">Reference no.</td>
						<td>
							<input class="segInput" name="refno" id="refno" type="text" value="<?= $_REQUEST['refno'] ?>" size="20" />
						</td>
					</tr>
					<tr>
						<td align="right" nowrap="nowrap">Select department</td>
						<td>
							<select class="segInput" id="selfinddept" name="selfinddept">
								<option value="">All</option>
<?php
	$depts = array(
		'ld'=>'Laboratory',
		'ph'=>'Pharmacy',
		'rd'=>'Radiology',
		'misc'=>'Miscellaneous'
	);

	foreach ($depts as $i=>$v) {
		$sel = (strtolower($_REQUEST['selfinddept']) == strtolower($i)) ? 'selected="selected"' : '';
		echo "								<option value=\"$i\" $sel>$v</option>\n";
	}
?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right">
							<input class="segButton" type="submit" value="Search"/>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
-->
	</div>


	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
</div>
<br />
<div style="width:95%">
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
					<th width="10%">Date</th>
					<th width="10%" nowrap="nowrap">OR No.</th>
					<th width="20%">Name</th>
<!--
					<th width="15%" nowrap="nowrap">Income Center</th>
					<th width="15%">Collection</th>
-->
					<th width="*">Item(s)</th>
					<th width="15%">Amount</th>
					<th width="12%"></th>
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
<input type="hidden" id="cancelor" name="cancelor" value="">
<input type="hidden" id="uncancelor" name="uncancelor" value="">
<input type="hidden" id="deleteor" name="deleteor" value="">

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