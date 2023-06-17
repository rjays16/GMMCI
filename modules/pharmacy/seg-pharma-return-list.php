<?php
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

# Start Smarty templating here
/**
 * LOAD Smarty
 */
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Title in the title bar
$smarty->assign('sToolbarTitle', "Pharmacy::Returns/refunds");

# href for the back button
// $smarty->assign('pbBack',$returnfile);
# href for the help button
$smarty->assign('pbHelp', "javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile', $breakfile);

# Window bar title
$smarty->assign('sWindowTitle', "Pharmacy::Returns");

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs', 'onLoad=""');

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

	function enableChildren(obj, enable) {
		var nodes=obj.childNodes;
		if (nodes) {
			for (var i=0;i<nodes.length;i++) {
				if (nodes[i].nodeName.toUpperCase() == 'INPUT' || nodes[i].nodeName.toUpperCase() == 'SELECT' ||
                    nodes[i].nodeName.toUpperCase() == 'TEXTAREA'
                ) {
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
include($root_path."include/care_api_classes/class_pharma_return.php");
$rc = new SegPharmaReturn();

if ($_POST['delete']) {
	if ($rc->deleteEntry($_POST['delete'])) {
        $smarty->assign('sysInfoMessage','<div style="margin:6px">Item successfully deleted!</div>');
	}
	else {
        $smarty->assign('sysErrorMessage','<div style="margin:6px">Unable to delete item!</div>');
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

if ($_REQUEST['chkpayee']) {
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
}

if ($_REQUEST['chkprod']) {
	$filters["PRODUCT"] = $_REQUEST["product"];
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

$db->SetFetchMode(ADODB_FETCH_ASSOC);
$result = $rc->getEntries($filters, $list_rows * $current_page, $list_rows);
$rows = "";
$last_page = 0;
$count=0;
if ($result) {
	$rows_found = $rc->FoundRows();
	if ($rows_found) {
		$last_page = floor($rows_found / $list_rows);
		$first_item = $current_page * $list_rows + 1;
		$last_item = ($current_page+1) * $list_rows;
		if ($last_item > $rows_found) $last_item = $rows_found;
		$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
	}

	while ($row=$result->FetchRow()) {
		$class = (($count%2)==0)?"":"wardlistrow2";

		$items_data = explode("\n",$row["items"]);

		$items = array();
		foreach ($items_data as $i=>$v) {
			$item_parse = explode(':', $v);
			#print_r("{$item_parse[0]} <span class=\"countTip\">{$item_parse[1]}</span>");
		  $items[] = "<span style=\"color:#800000; font:bold 11px Tahoma\">{$item_parse[0]}</span> <span style=\"font:bold 11px Tahoma;color:#000\">x{$item_parse[1]}</span>";
		}

		$items = implode("<br/>",$items);
		$records_found = TRUE;
        $refund = false;
		if ($row["refund_amount_fixed"]) {
			$refund = $row["refund_amount_fixed"];
        } else {
			$refund = $row["refund_amount"];
        }

		if (is_numeric($refund) && $refund > 0) {
			$refund = number_format((float)$refund,2);
        } else {
			$refund = 'Return';
        }

		$rows .= "		<tr class=\"$class\" height=\"24\">
				<td style=\"color:#000066\" align=\"center\">".$row["return_nr"]."</td>
				<td align=\"center\">
					".substr($row["return_date"],0,10)."<br/>
					".date("h:ia",strtotime($row["return_date"]))."
				</td>
                <td>{$row['return_name']}</td>
				<td style=\"color:#404040\">".$items."</td>
				<td style=\"color:#007\" align=\"".(is_numeric($refund) ? 'right' : 'center')."\">".$refund."</td>
				<td style=\"color:#2d2d2d\">".$row['status']."</td>
				<td align=\"center\" nowrap=\"nowrap\">
					<a title=\"Edit\" href=\"apotheke-pass.php".URL_APPEND."&userck=$userck&target=returnedit&nr=".$row["return_nr"]."&from=returnlist\"><img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" /></a>
					<a title=\"Delete\" href=\"#\">
						<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"if (confirm('Delete this entry?')) deleteItem('".$row["return_nr"]."')\"/>
					</a>
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
	$rows .= '		<tr><td colspan="10">No return entries found...</td></tr>';
}

# Default payee mode selection
if (!$_REQUEST["selpayee"]) $_REQUEST["selpayee"]="name";
ob_start();
?>

<br>

<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="width:60%">
	<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="1" cellpadding="0">
		<tbody>
			<tr>
				<td align="left" class="segPanelHeader" ><strong>Search options</strong></td>
			</tr>
			<tr height="25">
				<td nowrap="nowrap" align="right" class="segPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="0">
						<tr>
							<td width="50" align="right"><input type="checkbox" id="chknr" name="chknr" <?= ($_REQUEST['chknr'] ? 'checked="checked"' : '') ?> onclick="enableChildren($('tdNr'),this.checked)"/></td>
							<td width="10" nowrap="nowrap" align="left"><label for="chknr" class="segInput">Return entry #</label></td>
							<td id="tdNr">
								<input class="segInput" name="nr" id="nr" type="text" size="10" value="<?= $_REQUEST['nr'] ?>" <?= $_REQUEST['chknr'] ? '' : 'disabled="disabled"' ?>/>
							</td>
						</tr>
						<tr>
							<td align="right"><input type="checkbox" id="chkdate" name="chkdate" <?= ($_REQUEST['chkdate'] ? 'checked="checked"' : '') ?> onclick="enableChildren($('tdDate'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chkdate" class="segInput">Select date</label></td>
							<td id="tdDate">
                                <script type="text/javascript">
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
                                </script>
								<select class="segInput" id="seldate" name="seldate" onchange="seldateOnChange()" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>>
									<option value="today" <?= $_REQUEST["seldate"]=="today" ? 'selected="selected"' : '' ?>>Today</option>
									<option value="thisweek" <?= $_REQUEST["seldate"]=="thisweek" ? 'selected="selected"' : '' ?>>This week</option>
									<option value="thismonth" <?= $_REQUEST["seldate"]=="thismonth" ? 'selected="selected"' : '' ?>>This month</option>
									<option value="specificdate" <?= $_REQUEST["seldate"]=="specificdate" ? 'selected="selected"' : '' ?>>Specific date</option>
									<option value="between" <?= $_REQUEST["seldate"]=="between" ? 'selected="selected"' : '' ?>>Between</option>
								</select>
								<span name="seldateoptions" segOption="specificdate" <?= ($_REQUEST["seldate"]=="specificdate") ? '' : 'style="display:none"' ?>>
									<input class="segInput" name="specificdate" id="specificdate" type="text" size="8" value="<?= $_REQUEST['specificdate'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<input class="segInput" type="image" src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer" onclick="return false" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "specificdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
										});
									</script>
								</span>
								<span name="seldateoptions" segOption="between" <?= ($_REQUEST["seldate"]=="between") ? '' : 'style="display:none"' ?>>
									<input class="segInput" name="between1" id="between1" type="text" size="8" value="<?= $_REQUEST['between1'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<input class="segInput" type="image" src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  onclick="return false" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "between1", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between1", singleClick : true, step : 1
										});
									</script>
									to
									<input class="segInput" name="between2" id="between2" type="text" size="8" value="<?= $_REQUEST['between2'] ?>" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<input class="segInput" type="image" src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"   onclick="return false" <?= $_REQUEST['chkdate'] ? '' : 'disabled="disabled"' ?>/>
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "between2", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between2", singleClick : true, step : 1
										});
									</script>
								</span>
							</td>
						</tr>
						<tr>
							<td align="right"><input type="checkbox" id="chkprod" name="chkprod" <?= ($_REQUEST['chkprod'] ? 'checked="checked"' : '') ?>  onclick="enableChildren($('tdProd'),this.checked)"/></td>
							<td nowrap="nowrap" align="left"><label for="chkprod" class="segInput">Product</label></td>
							<td id="tdProd">
								<input class="segInput" name="product_name" id="product_name" readonly="readonly" type="text" size="20" value="<?= $_REQUEST['product_name'] ?>" <?= $_REQUEST['chkprod'] ? '' : 'disabled="disabled"' ?>/>
									<input name="product" id="product" type="hidden" value="<?= $_REQUEST['product'] ?>"/>
									<input class="segInput" type="image" id="select-enc" src="../../images/btn_product_small.gif" border="0" align="absmiddle" style="cursor:pointer;" <?= $_REQUEST['chkprod'] ? '' : 'disabled="disabled"' ?>
										onclick="overlib(
                                OLiframeContent('seg-order-tray.php?noinput=1', 600, 340, 'fOrderTray', 0, 'no'),
                                    WIDTH,600, TEXTPADDING,0, BORDER,0,
									STICKY, SCROLL, CLOSECLICK, MODAL,
									CLOSETEXT, '<img src=<?= $root_path ?>images/close_red.gif border=0 >',
                                    CAPTIONPADDING,2,
									CAPTION,'Add product from Order tray',
                                    MIDX,0, MIDY,0,
                                    STATUS,'Add product from Order tray');return false;"
                                    onmouseout="nd();" />
							</td>
						</tr>
						<tr>
							<td></td>
							<td colspan="2">
								<input type="submit" value="Search"  class="segButton"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:100%">
	<div class="segContentPaneHeader" style="margin-top:10px">
		<h1>
			List of returns
		</h1>
	</div>
    <br/>
	<div class="segContentPane">
		<table id="" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
					<th width="12%" nowrap="nowrap">Return #</th>
					<th width="12%" align="center">Date</th>
                    <th width="15%" nowrap="nowrap">Patient Name</th>
					<th width="*">Returned Item(s)</th>
					<th width="8%">Refund</th>
					<th width="8%">Status</th>
					<th width="8%"></th>
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
    include_once($root_path . 'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common', FALSE, FALSE, FALSE);

    # Set a flag to display this page as standalone
    $bShowThisForm=TRUE;
}

?>
    <input type="hidden" name="delete" id="delete" />
</form>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

$smarty->assign('sMainFrameBlockData', $sTemp);

/**
 * show Template
 */
$smarty->display('common/mainframe.tpl');
?>