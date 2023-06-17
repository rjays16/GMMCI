<?php
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Credit Memo :: View archives");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Credit Memo :: View Archives");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

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
var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

/*
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
*/

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

function deleteItem(id) {
	var dform = document.forms[0]
	$('delete').value = id
	dform.submit()
}

function printMemo(nr) {
	url = "seg-cashier-cm-print.php<?= URL_APPEND ?>&clear_ck_sid=<?= $clear_ck_sid ?>&nr="+nr;
	window.open(url,null,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}

function enableChildren(obj, enable) {
	var nodes=obj.childNodes;
	if (nodes) {
		for (var i=0;i<nodes.length;i++) {
			if (nodes[i].nodeName.toUpperCase() == 'INPUT' || nodes[i].nodeName.toUpperCase() == 'SELECT' ||
					nodes[i].nodeName.toUpperCase() == 'TEXTAREA') {
				nodes[i].disabled = !enable;
			}
			if (nodes[i].childNodes) {
				enableChildren(nodes[i], enable);
			}
		}
	}
}

function appendOrder(arg_place_holder, details) {
	$('product_name').value = details.name;
	$('product').value = details.id;
	cClick();
}

function validate() {
}
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
include($root_path."include/care_api_classes/class_credit_memo.php");
$cm = new SegCreditMemo();

if ($_POST['delete']) {
	if ($cm->deleteMemo($_POST['delete'])) {
		$smarty->assign('sysInfoMessage', 'Credit Memo successfully deleted!');
		if(ENABLE_FIS){
			require_once($root_path.'include/care_api_classes/curl/class_curl.php');
			$curl = new Rest_Curl();

			$data = array('refno'=>$_POST['delete'], 'src'=>'CMO');
			$curl->unpostEntries($data);
		}
	}
	else {
		$smarty->assign('sysErrorMessage', 'Error deleting memo: '.$db->ErrorMsg());
	}
}

if ($_REQUEST['chknr']) {
	$filters["NR"] = $_REQUEST['nr'];
}

if ($_REQUEST['chkdate']) {
	switch(strtolower($_REQUEST["seldate"])) {
		case "today":
			$filters['DATETODAY'] = "";
		break;
		case "thisweek":
			$filters['DATETHISWEEK'] = "";
		break;
		case "thismonth":
			$filters['DATETHISMONTH'] = "";
		break;
		case "specificdate":
			$dDate = date("Y-m-d",strtotime($_REQUEST["specificdate"]));				
			$filters['DATE'] = $dDate;
		break;
		case "between":
			$dDate1 = date("Y-m-d",strtotime($_REQUEST["between1"]));
			$dDate2 = date("Y-m-d",strtotime($_REQUEST["between2"]));
			$filters['DATEBETWEEN'] = array($dDate1,$dDate2);
		break;
	}
}

if ($_REQUEST['chkpayor']) {
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
}

if ($_REQUEST['chksource']) {
	$filters["SOURCE"] = $_REQUEST['source'];
}

if ($_REQUEST['chkpersonnel']) {
	$filters["PERSONNEL"] = $_REQUEST['personnel'];
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
	case 'first': default:
		$current_page=0;
	break;
}

$result = $cm->get($filters, $list_rows * $current_page, $list_rows);
$rows = "";
$last_page = 0;
$count=0;	
if ($result) {
	$rows_found = $cm->FoundRows();
	if ($rows_found) {
		$last_page = floor($rows_found / $list_rows);
		$first_item = $current_page * $list_rows + 1;
		$last_item = ($current_page+1) * $list_rows;
		if ($last_item > $rows_found) $last_item = $rows_found;
		$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
	}
	while ($row=$result->FetchRow()) {
		$class = (($count%2)==0)?"":"wardlistrow2";
		$items = explode("\n",$row["items"]);
		$items = implode(", ",$items);
			
		$records_found = TRUE;
		$items = '<span style="color:#800000">'.implode('</span>, <span style="color:#800000">',explode("\n",$row['items']))."</span>";
		$rows .= "		<tr class=\"$class\">
				<td style=\"color:#000066\" align=\"center\">".$row["memo_nr"]."</td>
				<td align=\"center\">
					".date("Y-m-d h:ia",strtotime($row["issue_date"]))."
				</td>
				<td>".$row["memo_name"]."</td>
				<td style=\"color:#404040\">".$items."</td>
				<td style=\"color:#007\" align=\"right\">".number_format($row['refund_amount'],2)."</td>
				<td align=\"right\" nowrap=\"nowrap\">
					<a title=\"Edit\" href=\"seg-cashier-pass.php".URL_APPEND."&userck=$userck&target=memoedit&src=memolist&nr=".$row["memo_nr"]."&from=memoarchives\"><img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" /></a>
					<a title=\"Print\" href=\"#\"><img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_print.gif\" border=\"0\" align=\"absmiddle\" onclick=\"printMemo('".$row["memo_nr"]."')\"/></a>
					<a title=\"Delete\" href=\"#\"><img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"if (confirm('Delete this entry?')) deleteItem('".$row["memo_nr"]."')\"/></a>
				</td>
			</tr>\n";
		$count++;
	}
}
else {
	print_r($result);
	$rows .= '		<tr><td colspan="10">'.$rc->sql.'</td></tr>';
}
if (!$rows) {
	$records_found = FALSE;
	$rows .= '		<tr><td colspan="10">No credit memo entries found...</td></tr>';
}

# Default payor mode selection
if (!$_REQUEST["selpayor"]) $_REQUEST["selpayor"]="name";
ob_start();
?>

<br>

<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="width:65%">
	<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="1" cellpadding="0">	
		<tbody>
			<tr>
				<td align="left" class="jedPanelHeader" ><strong>Search options</strong></td>
			</tr>	
			<tr height="25">
				<td nowrap="nowrap" align="right" class="jedPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="0">
						<tr>
							<td width="40" align="right"><input type="checkbox" id="chknr" name="chknr" <?= ($_REQUEST['chknr'] ? 'checked="checked"' : '') ?> onclick="enableChildren($('tdNr'),this.checked)"/></td>
							<td width="10" nowrap="nowrap" align="left"><label for="chknr" class="jedInput">Memo #</label></td>
							<td id="tdNr">
								<input class="jedInput" name="nr" id="nr" type="text" size="10" value="<?= $_REQUEST['nr'] ?>" <?= $_REQUEST['chknr'] ? '' : 'disabled="disabled"' ?>/>
							</td>							
						</tr>
						<tr>
							<td align="right"><input type="checkbox" id="chkpayor" name="chkpayor" <?= ($_REQUEST['chkpayor'] ? 'checked="checked"' : '') ?> onclick="enableChildren($('tdPayor'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chkpayor" class="jedInput">Select payor</label></td>
							<td id="tdPayor">
<script language="javascript" type="text/javascript">
<!--
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
-->
</script>
								<select class="jedInput" name="selpayor" id="selpayor" onchange="selpayorOnChange()"  <?= $_REQUEST['chkpayor'] ? '' : 'disabled="disabled"' ?>/>
									<option value="name" <?= ($_REQUEST['selpayor']=='name' ? 'selected="selected"' : '')?>>Payor Name</option>
									<option value="pid" <?= ($_REQUEST['selpayor']=='pid' ? 'selected="selected"' : '')?>>Patient ID</option>
									<option value="patient" <?= ($_REQUEST['selpayor']=='patient' ? 'selected="selected"' : '')?>>Patient Records</option>
									<option value="inpatient" <?= ($_REQUEST['selpayor']=='inpatient' ? 'selected="selected"' : '')?>>Inpatient/ER/OPD</option>
								</select>
								<span name="selpayoroptions" segOption="name" <?= ($_REQUEST["selpayor"]=="name") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="name" id="name" type="text" size="20" value="<?= $_REQUEST['name'] ?>" <?= $_REQUEST['chkpayor'] ? '' : 'disabled="disabled"' ?>/>
								</span>
								<span name="selpayoroptions" segOption="pid" <?= ($_REQUEST["selpayor"]=="pid") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="pid" id="pid" type="text" size="20" value="<?= $_REQUEST['pid'] ?>" <?= $_REQUEST['chkpayor'] ? '' : 'disabled="disabled"' ?>/>
								</span>
								<span name="selpayoroptions" segOption="patient" <?= ($_REQUEST["selpayor"]=="patient") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="patientname" id="patientname" readonly="readonly" type="text" value="<?= $_REQUEST['patientname'] ?>" <?= $_REQUEST['chkpayor'] ? '' : 'disabled="disabled"' ?>/>
									<input name="patient" id="patient" type="hidden" value="<?= $_REQUEST['patient'] ?>"/>
								<input class="jedInput" type="image" id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;" <?= $_REQUEST['chkpayor'] ? '' : 'disabled="disabled"' ?>
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
									<input class="jedInput" name="inpatientname" id="inpatientname" readonly="readonly" type="text" value="<?= $_REQUEST['inpatientname'] ?>" <?= $_REQUEST['chkpayor'] ? '' : 'disabled="disabled"' ?>/>
									<input name="inpatient" id="inpatient" type="hidden" value="<?= $_REQUEST['inpatient'] ?>"/>
								<input type="image" class="jedInput" id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;" <?= $_REQUEST['chkpayor'] ? '' : 'disabled="disabled"' ?>
								 onclick="overlib(
									OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?var_encounter_nr=inpatient&var_name=inpatientname&var_include_enc=1', 700, 400, 'fSelEnc', 0, 'auto'),
									WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=<?= $root_path ?>images/close_red.gif border=0 >',
									CAPTIONPADDING,2,
									CAPTION,'Select registered person',
									MIDX,0, MIDY,0, 
									STATUS,'Select registered person'); return false;"
								 onmouseout="nd();" />
								</span>
							</td>
						</tr>
						<tr>
							<td align="right"><input type="checkbox" id="chkdate" name="chkdate" <?= ($_REQUEST['chkdate'] ? 'checked="checked"' : '') ?> onclick="enableChildren($('tdDate'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chkdate" class="jedInput">Select date</label></td>
							<td id="tdDate">
<script language="javascript" type="text/javascript">
<!--
	function seldateOnChange() {
		var optSelected = $('seldate').options[$('seldate').selectedIndex]
		var spans = document.getElementsByName('seldateoptions')
		for (var i=0; i<spans.length; i++) {
			if (optSelected) {
				if (spans[i].getAttribute("segOption") == optSelected.value) {
					spans[i].style.display = ""
				}
				else
					spans[i].style.display = "none"
			}
		}
		
		disableNav()
	}
-->
</script>
								<select class="jedInput" id="seldate" name="seldate" onchange="seldateOnChange()" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>>
									<option value="today" <?= $_REQUEST["seldate"]=="today" ? 'selected="selected"' : '' ?>>Today</option>
									<option value="thisweek" <?= $_REQUEST["seldate"]=="thisweek" ? 'selected="selected"' : '' ?>>This week</option>
									<option value="thismonth" <?= $_REQUEST["seldate"]=="thismonth" ? 'selected="selected"' : '' ?>>This month</option>
									<option value="specificdate" <?= $_REQUEST["seldate"]=="specificdate" ? 'selected="selected"' : '' ?>>Specific date</option>
									<option value="between" <?= $_REQUEST["seldate"]=="between" ? 'selected="selected"' : '' ?>>Between</option>
								</select>
								<span name="seldateoptions" segOption="specificdate" <?= ($_REQUEST["seldate"]=="specificdate") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="specificdate" id="specificdate" type="text" size="8" value="<?= $_REQUEST['specificdate'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<input class="jedInput" type="image" src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer" onclick="return false" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "specificdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
										});
									</script>
								</span>
								<span name="seldateoptions" segOption="between" <?= ($_REQUEST["seldate"]=="between") ? '' : 'style="display:none"' ?>>
									<input class="jedInput" name="between1" id="between1" type="text" size="8" value="<?= $_REQUEST['between1'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<input class="jedInput" type="image" src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  onclick="return false" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "between1", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between1", singleClick : true, step : 1
										});
									</script>
									to
									<input class="jedInput" name="between2" id="between2" type="text" size="8" value="<?= $_REQUEST['between2'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<input class="jedInput" type="image" src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"   onclick="return false" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "between2", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between2", singleClick : true, step : 1
										});
									</script>
								</span>
							</td>
						</tr>
						<tr>
							<td align="right"><input type="checkbox" id="chksource" name="chksource" <?= ($_REQUEST['chksource'] ? 'checked="checked"' : '') ?>  onclick="enableChildren($('tdSource'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chksource" class="jedInput">Select cost center</label></td>
							<td id="tdSource">
								<select class="jedInput" id="source" name="source"  <?= $_REQUEST['chksource'] ? '' : 'disabled="disabled"' ?>/>
									<option value="">--All--</option>
<?php
	$cost_centers = array(
		'pp'=>'Deposits',
		'fb'=>'Final Billing',
		'ld'=>'Laboratory',
		'or'=>'Operating Room',
		'ph'=>'Pharmacy',
		'rd'=>'Radiology',
		'other'=>'Other payments'
	);
	
	foreach ($cost_centers as $i=>$v) {
		$sel = (strtolower($_REQUEST['source']) == strtolower($i)) ? 'selected="selected"' : '';
		echo "								<option value=\"$i\" $sel>$v</option>\n";
	}
?>
								</select>
							</td>							
						</tr>
						<tr>
							<td align="right"><input type="checkbox" id="chkpersonnel" name="chkpersonnel" <?= ($_REQUEST['chkpersonnel'] ? 'checked="checked"' : '') ?>  onclick="enableChildren($('tdPersonnel'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chkpersonnel" class="jedInput">Select assigned personnel</label></td>
							<td id="tdPersonnel">
								<select class="jedInput" id="personnel" name="personnel"  <?= $_REQUEST['chkpersonnel'] ? '' : 'disabled="disabled"' ?>/>
									<option value="">--All--</option>
<?php
	$sql = "SELECT u.name,u.login_id,u.personell_nr,a.location_nr\n".
		"FROM care_users AS u\n".
			"LEFT JOIN care_personell AS p ON u.personell_nr=p.nr\n".
			"LEFT JOIN care_personell_assignment AS a ON a.personell_nr=p.nr\n".
		"WHERE location_nr=170\n".
		"ORDER BY login_id";
	$cashiers = $db->Execute($sql);
	while($row=$cashiers->FetchRow()){
		$selected = ($row["login_id"] == $_REQUEST["personnel"]) ? 'selected=""' : "";
		echo "									<option value=\"".$row['login_id']."\" $selected>".$row['name']." <span style=\"font-weight:normal;color:#000066\">(".$row['login_id'].")</span></option>\n";
	}
?>
								</select>
							</td>							
						</tr>
						<tr>
							<td></td>
							<td colspan="2">
								<input type="submit" value="Search"  class="jedButton"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:85%">
	<div class="segContentPaneHeader" style="margin-top:10px">
		<h1>
			Search result: 
		</h1>
	</div>


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
					<th width="5%" nowrap="nowrap">Memo Nr.</th>
					<th width="8%" align="center" nowrap="nowrap">Issue Date</th>
					<th width="15%" align="center">Name</th>
					<th width="*" nowrap="nowrap">Refunded Item/s</th>
					<th width="12%">Amount</th>
					<th width="6%"></th>
				</tr>
			</thead>
			<tbody>
<?= $rows ?>
			</tbody>
		</table>
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
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">

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