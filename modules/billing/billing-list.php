<?php
/**
* SegHIS  ....
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/billing/class_transmittal.php');

global $db;
//$db-> debug =1;
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'modules/billing/ajax/bill-list.common.php');

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

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

$breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND."&userck=$userck";

//$db->debug=1;

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

if ($_GET["src"]) {
	$smarty->assign('bHideTitleBar',TRUE);
	$smarty->assign('bHideCopyright',TRUE);

	$src_link = "&src=".$_GET["src"];
	if (isset($_GET['hid'])) $_SESSION["current_hcare_id"] = $_GET['hid'];
}
else {
# Title in the title bar
	$smarty->assign('sToolbarTitle',"Billing Main::List of Billed Patients");

	$src_link = "";
}

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
# $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");
$smarty->assign('pbHelp',"javascript:gethelp('billing_main.php')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"List of Billed Patients");

# Assign Body Onload javascript code
if ($src_link != "")
	$smarty->assign('sOnLoadJs','onLoad="selrecordOnChange();fillupTransmittalDetails(\''.$_POST["fill_up"].'\', '.$_SESSION["current_hcare_id"].')"');
else
	$smarty->assign('sOnLoadJs','onLoad="selrecordOnChange();getEncounterNosBilled()"');

# Collect javascript code
ob_start()

?>
<!-- OLiframeContent(src, width, height) script:
(include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/billing-list.js"></script>
<script language="javascript" type="text/javascript">
<!--

/**
* @author ajmq
*/
function openWindow(url, query)
{
	if (!query)
		query = [];
	window.open(url+query.join('&'),'pdf',"width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
}


function pSearchClose() {
	cClick();
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

var djConfig = { isDebug: true };
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function jumpToPage(jumptype, page) {
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

function deleteItem(id,encnr) {
	var dform = document.forms[0]
//alert (id + " " + encnr);
$('delete').value = id;
$('enc_nr').value = encnr;
$('is_discharged').value = is_discharged;
dform.submit();
}

function validate() {
	return true;
}

function keepFilters(noption) {
	var filter = '';

	if (noption == 0) {
		if ($('chkspecific').checked) {
			var opt = $('selrecord').options[$('selrecord').selectedIndex];
			filter = $(opt.value).value;
			xajax_updateFilterOption(0, true);
			xajax_updateFilterTrackers($('selrecord').value, filter);
		}
		else
			xajax_updateFilterOption(0, false);
	}
	else {
		if ($('chkdate').checked) {
			if ($('seldate').value == 'specificdate') {
				filter = $('specificdate').value;
			}
			if ($('seldate').value == 'between') {
				filter = new Array($('between1').value, $('between2').value);
			}

			xajax_updateFilterOption(1, true);
			xajax_updateFilterTrackers($('seldate').value, filter);
		}
		else
			xajax_updateFilterOption(1, false);
	}
	clearPageTracker();
}

function keepPage() {
	var pg = $('page').value;
	xajax_updatePageTracker(pg);
}

function clearPageTracker() {
	xajax_clearPageTracker();
}

function prepareSelect(enc_nr) {
//		xajax_addSelectedEncounter(enc_nr);
var list, dBody, tmp;

var elemRow = document.getElementById("row_"+enc_nr);
if (elemRow) {
	removeEncounterNr(enc_nr);
	xajax_noteSelectedEncounter(enc_nr);
}
else {
	if (!list) list = $('cases');
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		tmp = '<tr id="row_'+enc_nr+'"><td><input type="hidden" name="cases_added[]" value="'+enc_nr+'" /></td></tr>';
		dBody.innerHTML += tmp;

		xajax_noteSelectedEncounter(enc_nr);
	}
}
}

function removeEncounterNr(enc_nr) {
	var table = $('cases');
	var rmvRow=document.getElementById("row_"+enc_nr);
	if (table && rmvRow)
		table.deleteRow(rmvRow.rowIndex);
	else
		alert(table+' and '+rmvRow);
}

-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$xajax->printJavascript($root_path.'classes/xajax');

# Buffer page output
include($root_path."include/care_api_classes/billing/class_bill_info.php");

$objbill = new BillInfo();

if (!$_POST["applied"]) {
	if (isset($_SESSION["filteroption"])) {
		if (isset($_SESSION["filteroption"][0])) $_REQUEST["chkspecific"] = strcmp($_SESSION["filteroption"][0], 'true') == 0;
		if (isset($_SESSION["filteroption"][1])) $_REQUEST["chkdate"] = strcmp($_SESSION["filteroption"][1], 'true') == 0;
	}

	if (isset($_SESSION["filtertype"])) {
		switch (strtolower($_SESSION["filtertype"])) {
			case "name":
			case "case_no":
			case "hrn";
			case "bill_number":
			$_REQUEST["selrecord"] = $_SESSION["filtertype"];
			$_REQUEST[strtolower($_SESSION["filtertype"])] = $_SESSION["filter"];
			break;

			default:
			$_REQUEST["seldate"] = $_SESSION["filtertype"];
			if (is_array($_SESSION["filter"])) {
				$_REQUEST["between1"] = $_SESSION["filter"][0];
				$_REQUEST["between2"] = $_SESSION["filter"][1];
			}
			else
				if ($_SESSION["filter"] != "")
					$_REQUEST["specificdate"] = $_SESSION["filter"];
			}
		}
		else {
			if (is_null($_SESSION["filteroption"])) $_REQUEST['chkdate'] = true;

			$_REQUEST["seldate"] = "today";
		}
	}

	if (isset($_SESSION["current_page"])) {
		$_REQUEST['page'] = $_SESSION["current_page"];
	}

//echo var_export(strcmp($_REQUEST["chkspecific"], 'true') == 0 ? 'true' : 'false', true);

#}
#else
#    $_REQUEST["seldate"] = "today";

	if ($_POST['delete']) {
		if($objbill->IsDischarge( $_POST['enc_nr'], $_POST['bill_nr']) || $objbill->isWellBaby($enc_nr))  {
			if ($objbill->deleteBillInfo($_POST['delete'], $_POST['enc_nr'], $_POST['bill_nr'])) {
				$sWarning = 'Billing successfully deleted!';
			}
			else {
				$sWarning = 'Error in billing deletion: '.$db->ErrorMsg();
			}
		}
	}

	$title_sufx = (!$_GET['src']) ? 'Billings' : 'Claims to Transmit';

	if ($_REQUEST['chkdate']) {
		switch(strtolower($_REQUEST["seldate"])) {
			case "today":
			$search_title = "Today's $title_sufx";
			$filters['DATETODAY'] = "";
			break;
			case "thisweek":
			$search_title = "This Week's $title_sufx";
			$filters['DATETHISWEEK'] = "";
			break;
			case "thismonth":
			$search_title = "This Month's $title_sufx";
			$filters['DATETHISMONTH'] = "";
			break;
			case "specificdate":
			$search_title = "$title_sufx On " . date("F j, Y",strtotime($_REQUEST["specificdate"]));
			$dDate = date("Y-m-d",strtotime($_REQUEST["specificdate"]));
			$filters['DATE'] = $dDate;
			break;
			case "between":
			$search_title = "$title_sufx From " . date("F j, Y",strtotime($_REQUEST["between1"])) . " To " . date("F j, Y",strtotime($_REQUEST["between2"]));
			$dDate1 = date("Y-m-d",strtotime($_REQUEST["between1"]));
			$dDate2 = date("Y-m-d",strtotime($_REQUEST["between2"]));
			$filters['DATEBETWEEN'] = array($dDate1,$dDate2);
			break;
		}
	}

	if ($_REQUEST['chkspecific']) {
		switch(strtolower($_REQUEST["selrecord"])) {
			case "name":
			$filters["NAME"] = $_REQUEST["name"];
			break;
			case "case_no":
			$filters["CASE_NO"] = $_REQUEST["case_no"];
			break;
			case "hrn":
			$filters["HRN"] = $_REQUEST["hrn"];
			break;
			case "bill_number":
			$filters["BILL_NUMBER"] = $_REQUEST["bill_number"];
			break;
		}
	}

	//added by EJ 09/25/2014
	if ($_REQUEST['chkcateg']) {
		switch($_REQUEST['selcateg']) {
			case "G":
			$categ_nr = 1;
			break;
			case "S":
			$categ_nr = 2;
			break;
			case "NO":
			$categ_nr = 3;
			break;
			case "NS":
			$categ_nr = 4;
			break;
			case "I":
			$categ_nr = 5;
			break;
			case "PS":
			$categ_nr = 6;
			break;
			case "NW":
			$categ_nr = 7;
			break;
			case "PG":
			$categ_nr = 8;
			break;
		}
	}

	if($_REQUEST['chktype']){
		switch($_REQUEST['seltype']){
			case 1:
				$filters['ER'] = "";
			break;
			case 2:
				$filters['OPD'] = "";
			break;
			case 3:
				$filters['IPD'] = "";
			break;
		}
	}

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
		case 'first':
		$current_page=0;
		break;
	}

	$_SESSION["current_page"] = $current_page;

	//modified by EJ 09/24/2014
	if (!$_GET['src']) {
		$result = $objbill->getSavedBillings($filters, $list_rows * $current_page, $list_rows);
	}	
	else {
		if ($_REQUEST['chkcateg']) {
			$result = $objbill->getSavedBillingsForTransmittalCategory($list_rows * $current_page, $list_rows, $categ_nr, $_SESSION["current_hcare_id"]);
		}
		else {
			$result = $objbill->getSavedBillingsForTransmittal($filters, $list_rows * $current_page, $list_rows, $_SESSION["current_hcare_id"]);
		}
	}
		
//echo "<pre>".$objbill->sql."</pre>";
	$rows = "";
	$last_page = 0;
	$count=0;
	if ($result) {
		$rows_found = $objbill->FoundRows();
		if ($rows_found) {
			$last_page = floor($rows_found / $list_rows);
			$first_item = $current_page * $list_rows + 1;
			$last_item = ($current_page+1) * $list_rows;
			if ($last_item > $rows_found) $last_item = $rows_found;
			$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
		}

		while ($row = $result->FetchRow()) {
			if(!$_GET['src'])	
				$spatient = $row['name'];
			else
				$spatient = $objbill->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);

			$billingType = $objbill->GetTypeBilling($row['bill_nr']);

			$records_found = TRUE;
			if ($src_link == "") {
				$n_bill   = 0;

/**
* Fix for incorrect billing printout, 10-02-2010
*/
$query = "SELECT e.pid, b.bill_nr, b.encounter_nr, b.bill_frmdte, b.bill_dte\n".
"FROM seg_billing_encounter b ORDER BY b.bill_dte\n".
"INNER JOIN care_encounter e ON e.encounter_nr=b.encounter_nr\n".
"WHERE b.bill_nr=".$db->qstr($row['bill_nr']). " and b.is_deleted IS NULL";
$billRow = $db->GetRow( $query );
//echo $query;
#Commented By Jarel New Query for Billing List 
/*if (!empty($row["total_charge"])) $n_bill = $row["total_charge"];
if (!empty($row["total_coverage"])) $n_bill -= $row["total_coverage"];
if (!empty($row["total_computed_discount"])) $n_bill -= $row["total_computed_discount"];
//edited by jasper 04/17/2013
if (!empty($row["total_discount_amnt"]) && ($n_bill > 0)) {
$n_bill -= $row["total_discount_amnt"];
} else {
if (!empty($row["total_discount"]) && ($n_bill > 0))
$n_bill -= ($n_bill * $row["total_discount"]);
}*/

$n_bill = $row['net'];
//						if (!empty($row["total_computed_discount"])) $n_bill -= $row["total_computed_discount"];
//pid=2126711&encounter_nr=2010031219&from_dt=1279015200&bill_dt=1281772901&nr=2010061440

$objTransmittal = new Transmittal;
$isTransmitted = $objTransmittal->getPatientTrasmittalInfo($row['encounter_nr']);

/**
* Fix for erroneous billed statements, 10-04-2010
*
* @author ajmq
*/
$btns = "<td align=\"right\" nowrap=\"nowrap\">";

global $allowedarea;
$allowedarea = array('_a_3_billDeleteBtn');
if (validarea($_SESSION['sess_permission'],1) && !$isTransmitted) {
	$canDelete = "<a title=\"Delete\" href=\"#\">
	<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"if (confirm('Delete this billing?')) deleteItem('".$row["bill_nr"]."','".$row["encounter_nr"]."','".$row["is_discharged"]."')\"/>
	</a></td>";
} else {
	$canDelete  = "<a title=\"Delete\" href=\"#\">
	<img class=\"disabled\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"onclick=\"return false;\" style=\"opacity:0.2\"/>
	</a></td>";

	"<a style=\"display:none\" title=\"Print *fixed* Billing Statement\" href=\"javascript:openWindow('bill-pdf-summary.php".URL_APPEND."&userck=$userck&pid={$billRow['pid']}&encounter_nr={$billRow['encounter_nr']}&from_dt=".strtotime($billRow['bill_frmdte'])."&bill_dt=".strtotime($billRow['bill_dte'])."&nr={$row['bill_nr']}&IsDetailed=0&fix=1')\">
	<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_print.gif\" border=\"0\" align=\"absmiddle\" />
	</a>
	<a style=\"display:none\" title=\"Printout\" href=\"javascript:openWindow('form2.php".URL_APPEND."&userck=$userck&encounter_nr={$billRow['encounter_nr']}&id=18&claim=0')\">
	<img title=\"Print *fixed* Form 2\" class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_print2.gif\" border=\"0\" align=\"absmiddle\" />
	</a>";



} $allowedarea = array('_a_3_billViewBtn');

if($billingType){	
	if (validarea($_SESSION['sess_permission'],1)) {
		$canView = "<a title=\"View\" href=\"../billing_new/billing-main-new.php".URL_APPEND."&userck=$userck&nr=".$row["bill_nr"]."&from=".$root_path."modules/billing/billing-list\">
		<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" />
		</a>";
	}else {

		$canView = " <a title=\"View\" href=\"../billing_new/billing-main-new.php".URL_APPEND."&userck=$userck&nr=".$row["bill_nr"]."&from=".$root_path."modules/billing/billing-list\">
		<img class=\"disabled\" src=\"".$root_path."images/cashier_edit.gif\" onclick=\"return false;\" border=\"0\" align=\"absmiddle\" style=\"opacity:0.2\"/>
		</a>";
	}
}else{
	if (validarea($_SESSION['sess_permission'],1)) {

		$canView = "<a title=\"View\" href=\"billing-main.php".URL_APPEND."&userck=$userck&nr=".$row["bill_nr"]."&from=".$root_path."modules/billing/billing-list\">
		<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" />
		</a>";
	}else {

		$canView = " <a title=\"View\" href=\"billing-main.php".URL_APPEND."&userck=$userck&nr=".$row["bill_nr"]."&from=".$root_path."modules/billing/billing-list\">
		<img class=\"disabled\" src=\"".$root_path."images/cashier_edit.gif\" onclick=\"return false;\" border=\"0\" align=\"absmiddle\" style=\"opacity:0.2\"/>
		</a>";
	}
}



$rows .= "<tr class=\"$class\">

<td>".$row["bill_nr"]."</td>
<td>".strftime("%Y-%m-%d %I:%M %p", strtotime($row["bill_dte"]))."</td>
<td>".$spatient."</td>
<td>".$row["encounter_nr"]."</td>
<td align=\"right\">".number_format($n_bill, 2, '.', ',')."</td>".$btns."{$canView}
{$canDelete}</tr>\n";

$count++;

}
else {
	if (!(isset($_SESSION['cases'][$row["encounter_nr"]]) && ($_SESSION['cases'][$row["encounter_nr"]]))) {
		$btns = '<td align="center"><input type="checkbox" id="cases[]" name="cases[]" style="cursor:pointer" value="'.$row["encounter_nr"].'" '.
		' onclick="prepareSelect(\''.$row["encounter_nr"].'\')" /></td>';

		$rows .= "<tr class=\"$class\">
		<td width=\"10%\">".$row["insurance_nr"]."</td>
		<td width=\"10%\">".$row["categ_desc"]."</td>
		<td width=\"34%\">".$row["confine_period"]."</td>
		<td width=\"8%\">".$row["encounter_nr"]."</td>
		<td width=\"23%\">".$spatient."</td>
		<td width=\"12%\" align=\"right\">".number_format($row["this_coverage"], 2, '.', ',')."</td>".$btns."</tr>\n";

		$count++;
	}
}
}
}
else {
//		print_r($result);
	$rows .= '        <tr><td colspan="10">No claims found ...</td></tr>';
	$sWarning = $objbill->error_msg;
}

if (!$rows) {
	$records_found = FALSE;
	$rows .= '        <tr><td colspan="10">'.(!$_GET['src'] ? 'No billings done at this time ...' : 'No claims found ...').'</td></tr>';
}
ob_start();
?>
<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid.$src_link ?>" method="post" name="suchform" onSubmit="return validate()">
	<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
	<div style="width:70%">
		<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">
			<tbody>
				<tr>
					<td align="left" class="jedPanelHeader" ><strong>Search options</strong></td>
				</tr>
				<tr>
					<td nowrap="nowrap" align="left" class="jedPanel">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td width="50" align="right">
									<input type="checkbox" id="chkspecific" name="chkspecific" onclick="selrecordOnChange(); keepFilters(0);emptier(); disableSearch();" <?= ($_REQUEST['chkspecific'] ? 'checked' : '') ?>/>
								</td>
								<td width="5%" align="right" nowrap="nowrap">Patient/Case No./Bill No./HRN</td>
								<td>
									<script language="javascript" type="text/javascript">
									<!--
									function selrecordOnChange() {
										var optSelected = $('selrecord').options[$('selrecord').selectedIndex];
										var spans = document.getElementsByName('selrecordoptions');

										for (var i=0; i<spans.length; i++) {
											if (optSelected) {
												if (spans[i].getAttribute("segOption") == optSelected.value) {
													spans[i].style.display = $('chkspecific').checked ? "" : "none";
												}
												else
													spans[i].style.display = "none";
											}
										}

//				disableNav()
}


function isValidSearch(key) {
	if (typeof(key)=='undefined') return false;
	var s=key.toUpperCase();
	var skey =$('name').value;
	var skey1 = $('case_no').value;
	var skey2 = $('bill_number').value;
	var skey3 = $('hrn').value;
	if (skey=='' && skey2=='' && skey3=='') 
		{return (
			/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
			/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
			/^\d{10,}$/.test(s)
			);}
	else if (skey=='' && skey1=='' && skey3=='') 
		{ return (
			/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
			/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
			/^\d{5,}$/.test(s)
			);}
	else if (skey=='' && skey1=='' && skey2=='') 
		{ return (
			/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
			/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
			/^\d{7,}$/.test(s)
			);}
	return (
		/^[A-Z0-9,A-Z0-9?\-\.]{3,}$/.test(s)
		);
}

function disableSearch(){
	if( $('chkspecific').checked ){
		if ($('name').value != '' || $('bill_number').value != '' || $('case_no').value != '' || $('hrn').value != '')
		{
			if ($('bill_number').value != '')
				b=isValidSearch(document.getElementById('bill_number').value);
			else if ($('case_no').value != '')
				b=isValidSearch(document.getElementById('case_no').value);
			else if ($('hrn').value != '')
				b=isValidSearch(document.getElementById('hrn').value);
			else
				b=isValidSearch(document.getElementById('name').value);
			document.getElementById("search-btn").disabled = false;
		}
		else
			document.getElementById("search-btn").disabled = true;
	}
	else if($('chkdate').checked && !$('chkspecific').checked && !$('chkcateg').checked)
		document.getElementById("search-btn").disabled = false;
	else if(!$('chkdate').checked && !$('chkspecific').checked && $('chkcateg').checked)
		document.getElementById("search-btn").disabled = false;
	else
		document.getElementById("search-btn").disabled = true;
}

function emptier(){
	document.getElementById('name').value='';
	document.getElementById('case_no').value='';
	document.getElementById('bill_number').value='';
	document.getElementById('hrn').value='';
}
-->
</script>
<select class="jedInput" name="selrecord" id="selrecord" onchange="selrecordOnChange(); keepFilters(0);emptier();disableSearch();"/>
<option value="name" <?= $_REQUEST["selrecord"]=="name" ? 'selected="selected"' : '' ?>>Patient Name</option>
<option value="case_no" <?= $_REQUEST["selrecord"]=="case_no" ? 'selected="selected"' : '' ?>>Case No.</option>
<option value="hrn" <?= $_REQUEST["selrecord"]=="hrn" ? 'selected="selected"' : '' ?>>HRN</option>
<option value="bill_number" <?= $_REQUEST["selrecord"]=="bill_number" ? 'selected="selected"' : '' ?>>Bill No.</option>
</select>
<td>
	<span name="selrecordoptions" segOption="name" <?= ($_REQUEST["selrecord"]=="name") && $_REQUEST['chkspecific'] ? '' : 'style="display:none"' ?>>
		<input class="jedInput" name="name" id="name" onblur="keepFilters(0);" onkeyup="disableSearch();" type="text" size="30" value="<?= $_REQUEST['name'] ?>"/>
		<input type="hidden" name="name_old" value="<?= $_REQUEST['name'] ?>" />
	</span>
	<span name="selrecordoptions" segOption="case_no" <?= ($_REQUEST["selrecord"]=="case_no") && $_REQUEST['chkspecific'] ? '' : 'style="display:none"' ?>>
		<input class="jedInput" name="case_no" id="case_no" onblur="keepFilters(0);" onkeyup="disableSearch();" type="text" size="30" value="<?= $_REQUEST['case_no'] ?>"/>
	</span>
	<span name="selrecordoptions" segOption="hrn" <?= ($_REQUEST["selrecord"]=="hrn") && $_REQUEST['chkspecific'] ? '' : 'style="display:none"' ?>>
		<input class="jedInput" name="hrn" id="hrn" onblur="keepFilters(0);" onkeyup="disableSearch();" type="text" size="30" value="<?= $_REQUEST['hrn'] ?>"/>
	</span>
	<span name="selrecordoptions" segOption="bill_number" <?= ($_REQUEST["selrecord"]=="bill_number") && $_REQUEST['chkspecific'] ? '' : 'style="display:none"' ?>>
		<input class="jedInput" name="bill_number" id="bill_number" onblur="keepFilters(0);" onkeyup="disableSearch();" type="text" size="30" value="<?= $_REQUEST['bill_number'] ?>"/>
	</span></td>
</td>
</tr>
<tr>
	<td width="50" align="right">
			<input type="checkbox" id="chktype" name="chktype" <?= ($_REQUEST['chktype'] ? 'checked' : '') ?>/>
	</td>
	<td width="5%" align="left" nowrap="nowrap">Patient Type</td>
	<td>
			<select class="jedInput" name="seltype" id="seltype"/>
				<option value="0" <?= $_REQUEST["seltype"]=="0" ? 'selected="selected"' : '' ?>>ALL</option>
				<option value="1" <?= $_REQUEST["seltype"]=="1" ? 'selected="selected"' : '' ?>>ER</option>
				<option value="2" <?= $_REQUEST["seltype"]=="2" ? 'selected="selected"' : '' ?>>OPD</option>
				<option value="3" <?= $_REQUEST["seltype"]=="3" ? 'selected="selected"' : '' ?>>IPD</option>
			</select>
	</td>
</tr>
<tr>
	<td width="5%" align="right"><input type="checkbox" id="chkdate" name="chkdate" <?= ($_REQUEST['chkdate'] ? 'checked' : '') ?> onclick="seldateOnChange();keepFilters(1);disableSearch();"/></td>
	<td width="15%" nowrap="nowrap" align="left"><?= ($_GET["src"]) ? 'Discharge' : 'Bill' ?> date</td>
	<td width="20%" align="left">
		<script language="javascript" type="text/javascript">
		<!--
		function seldateOnChange() {
			var filter = '';

			var optSelected = $('seldate').options[$('seldate').selectedIndex]
			var spans = document.getElementsByName('seldateoptions')
			for (var i=0; i<spans.length; i++) {
				if (optSelected) {
					if (spans[i].getAttribute("segOption") == optSelected.value) {
						spans[i].style.display = $('chkdate').checked ? "" : "none";

						if (optSelected.value == "specificdate")
							filter = $(optSelected.value).value
						else
							filter = new Array($('between1').value, $('between2').value);
					}
					else
						spans[i].style.display = "none"
				}
			}

//				disableNav()
}
-->
</script>
<select class="jedInput" id="seldate" name="seldate" onchange="seldateOnChange(); keepFilters(1);">
	<option value="today" <?= $_REQUEST["seldate"]=="today" ? 'selected="selected"' : '' ?>>Today</option>
	<option value="thisweek" <?= $_REQUEST["seldate"]=="thisweek" ? 'selected="selected"' : '' ?>>This week</option>
	<option value="thismonth" <?= $_REQUEST["seldate"]=="thismonth" ? 'selected="selected"' : '' ?>>This month</option>
	<option value="specificdate" <?= $_REQUEST["seldate"]=="specificdate" ? 'selected="selected"' : '' ?>>Specific date</option>
	<option value="between" <?= $_REQUEST["seldate"]=="between" ? 'selected="selected"' : '' ?>>Between</option>
</select>
</td>
<td>
	<span name="seldateoptions" segOption="specificdate" <?= ($_REQUEST["seldate"]=="specificdate") && $_REQUEST['chkdate'] ? '' : 'style="display:none"' ?>>
		<input onchange="keepFilters(1);" class="jedInput" name="specificdate" id="specificdate" type="text" size="8" value="<?= $_REQUEST['specificdate'] ?>"/>
		<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
		<script type="text/javascript">
		Calendar.setup ({
			inputField : "specificdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
		});
		</script>
	</span>
	<span name="seldateoptions" segOption="between" <?= ($_REQUEST["seldate"]=="between") && $_REQUEST['chkdate'] ? '' : 'style="display:none"' ?>>
		<input onchange="keepFilters(1);" class="jedInput" name="between1" id="between1" type="text" size="8" value="<?= $_REQUEST['between1'] ?>"/>
		<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
		<script type="text/javascript">
		Calendar.setup ({
			inputField : "between1", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between1", singleClick : true, step : 1
		});
		</script>
		to
		<input onchange="keepFilters(1);" class="jedInput" name="between2" id="between2" type="text" size="8" value="<?= $_REQUEST['between2'] ?>"/>
		<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
		<script type="text/javascript">
		Calendar.setup ({
			inputField : "between2", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between2", singleClick : true, step : 1
		});
		</script>
	</span>
</td>
</tr>

<!-- Added by EJ 09/24/2014 -->
<tr>
	<td width="5%" align="right"><input type="checkbox" id="chkcateg" name="chkcateg" <?= ($_REQUEST['chkcateg'] ? 'checked' : '') ?> onclick="keepFilters(1); disableSearch();"/></td>
	<td width="15%" nowrap="nowrap" align="left">Category</td>
	<td width="20%" align="left">
		<select class="jedInput" id="selcateg" name="selcateg" onchange="keepFilters(1);">
			<option value="G" <?= $_REQUEST["selcateg"]=="G" ? 'selected="selected"' : '' ?>>EMPLOYED-GOV`T</option>
			<option value="S" <?= $_REQUEST["selcateg"]=="S" ? 'selected="selected"' : '' ?>>EMPLOYED-PRIVATE</option>
			<option value="NW" <?= $_REQUEST["selcateg"]=="NW" ? 'selected="selected"' : '' ?>>INDIVIDUAL PAYING-OWWA</option>
			<option value="NO" <?= $_REQUEST["selcateg"]=="NO" ? 'selected="selected"' : '' ?>>INDIVIDUALLY PAYING-OWP</option>
			<option value="NS" <?= $_REQUEST["selcateg"]=="NS" ? 'selected="selected"' : '' ?>>INDIVIDUALLY PAYING-SELF EMPLOYED</option>
			<option value="PG" <?= $_REQUEST["selcateg"]=="PG" ? 'selected="selected"' : '' ?>>LIFETIME MEMBER-GOV`T</option>
			<option value="PS" <?= $_REQUEST["selcateg"]=="PS" ? 'selected="selected"' : '' ?>>LIFETIME MEMBER-PRIVATE</option>
			<option value="I" <?= $_REQUEST["selcateg"]=="I" ? 'selected="selected"' : '' ?>>SPONSORED MEMBER</option>
		</select>
	</tr>
<!-- Ended by EJ 09/24/2014 -->

	<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	<tr>
		<td></td>
		<td colspan="2">
			<input type="submit" id="search-btn" style="cursor:pointer" value="Search"  class="jedButton"/>
		</td>
	</tr>
</table>
</td>
</tr>
</tbody>
</table>
</div>

<div style="width:<?= (!$_GET['src']) ? '90' : '100' ?>%">
	<table width="100%" class="segContentPaneHeader" style="margin-top:10px">
		<tr><td>
			<h1>
				Search result:
				<?php
				echo $search_title;  ?></h1></td>
				<?php if ($_GET['src']) { ?>
				<td align="right"><img src="<?= $root_path ?>images/btn_submitorder.gif" align="center" onclick="assignEncNrsBilled();$('fill_up').value = '1';document.forms[0].submit();" style="cursor:pointer" /></td>
				<?php } ?>
			</tr>
		</table>
		<div class="segContentPane">
			<table id="" class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
				<thead>
					<tr class="nav">
						<th colspan="9">
							<div id="pageFirst" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(FIRST_PAGE)">
								<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
								<span title="First">First</span>
							</div>
							<div id="pagePrev" class="<?= ($current_page > 0) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:left" onclick="jumpToPage(PREV_PAGE)">
								<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
								<span title="Previous">Previous</span>
							</div>
							<div id="pageShow" style="float:left; margin-left:10px">
								<span><?= $nav_caption ?></span>
							</div>
							<div id="pageLast" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(LAST_PAGE)">
								<span title="Last">Last</span>
								<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
							</div>
							<div id="pageNext" class="<?= ($current_page < $last_page) ? 'segSimulatedLink' : 'segDisabledLink' ?>" style="float:right" onclick="jumpToPage(NEXT_PAGE)">
								<span title="Next">Next</span>
								<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
							</div>
						</th>
					</tr>
					<tr>
						<?= (!$_GET['src']) ?

						'<th width="10%">Bill No.</th>
						<th width="20%">Bill Date/Time</th>
						<th width="*">Patient</th>
						<th width="10%">Case No.</th>
						<th width="15%">Billed Amount</th>
						<th width="6%">'.(($src_link != '') ? '&nbsp;' : 'Details').'</th>' :
						'<th width="10%">Policy No.</th>
						<th width="10%">Classification</th>
						<th width="34%">Confinement</th>
						<th width="8%">Case No.</th>
						<th width="23%">Patient</th>
						<th width="12%">Total Claim</th>
						<th width="3%">&nbsp;</th>' ?>
					</tr>
				</thead>
				<tbody>
					<?= $rows ?>
				</tbody>
			</table>
			<br />
		</div>
	</div>

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

<input type="hidden" id="delete" name="delete" value="" />
<!--added by jasper 04/23/2013 -->
<input type="hidden" id="enc_nr" name="enc_nr" value="" />
<input type="hidden" id="is_discharged" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">
<input type="hidden" id="applied" name="applied" value="1">
<input type="hidden" id="root_path" name="root_path" value="<?php echo $root_path ?>" />
<input type="hidden" id="fill_up" name="fill_up" value="">
<div style="display:none" id="cases_selected">
	<table id="cases">
		<tbody>
		</tbody>
	</table>
</div>
<div style="display:none" id="cases_list"></div>
</form>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

$smarty->assign('sMainFrameBlockData',$sTemp);

/**
* show Template
*/
$smarty->display('common/mainframe.tpl');
?>