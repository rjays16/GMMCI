<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

global $db;

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

if($_GET['user_origin']){
	$breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND;
}else{
	$break_file = $root_path.'main/startframe.php'.URL_APPEND;
}
 
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
 }
 else {
	 # Title in the title bar
	 $smarty->assign('sToolbarTitle',"List of Dialysis Patients");

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
 $smarty->assign('sWindowTitle',"List of Dialysis Patients");


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
<script language="javascript" type="text/javascript">

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

	}


	function isValidSearch(key) {
		if (typeof(key)=='undefined') return false;
		var s=key.toUpperCase();
		var skey =$('inpatient').value;
		var skey1 = $('erpatient').value;
		//var skey2 = $('bill_number').value;
		var skey3 = $('outpatient').value;
		if (skey=='' && skey2=='' && skey3=='') {
			return (
				/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
				/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
				/^\d{10,}$/.test(s)
			);
		}else if (skey=='' && skey1=='' && skey3=='') { 
			return (
				/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
				/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
				/^\d{5,}$/.test(s)
			);
		}else if (skey=='' && skey1=='' && skey2=='') { 
			return (
				/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
				/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
				/^\d{7,}$/.test(s)
			);
		}
		
		return (
		/^[A-Z0-9,A-Z0-9?\-\.]{3,}$/.test(s)
		);
	}

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

	function validate() {
		$('page').value = 0;
		return true;
	}

	function keepFilters(noption) {
		var filter = '';

		if (noption == 0) {
			if ($('chkspecific').checked) {
				var opt = $('selrecord').options[$('selrecord').selectedIndex];
			//	filter = $(opt.value).value;
				xajax_updateFilterOption(0, true);
				//xajax_updateFilterTrackers($('selrecord').value, filter);
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

	function refreshPage(){
		location.reload();
	}
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$xajax->printJavascript($root_path.'classes/xajax');

# Buffer page output
include($root_path."include/care_api_classes/class_dialysis_billing.php");

$objpatients = new Dialysis_billing();


if (isset($_SESSION["current_page"])) {
		$_POST['page'] = $_SESSION["current_page"];
}


$title_sufx = "Dialysis Patient";

if ($_POST['chkdate']) {
	switch(strtolower($_POST["seldate"])) {
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
				$search_title = "$title_sufx On " . date("F j, Y",strtotime($_POST["specificdate"]));
				$dDate = date("Y-m-d",strtotime($_POST["specificdate"]));
				$filters['DATE'] = $dDate;
		break;
		case "between":
				$search_title = "$title_sufx From " . date("F j, Y",strtotime($_POST["between1"])) . " To " . date("F j, Y",strtotime($_POST["between2"]));
				$dDate1 = date("Y-m-d",strtotime($_POST["between1"]));
				$dDate2 = date("Y-m-d",strtotime($_POST["between2"]));
				$filters['DATEBETWEEN'] = array($dDate1,$dDate2);
		break;
	}
}

if ($_POST['chkspecific_']) {
		switch(strtolower($_POST["selrecord"])) {
				case "done":
						$filters["DONE"] = $_POST["done"];
				break;
				case "active":
						$filters["ACTIVE"] = $_POST["active"];
				break;
		}
}

if($_POST['keyword']){
	$keyword = $_POST['keyword'];
}else{
	$keyword = "";
}

if ($_POST['chkspecific']) {
		switch(strtolower($_POST["selrecordby"])) {
				case "name":
						$filters["name"] = $_POST["name"];
				break;
				case "pid":
						$filters["pid"] = $_POST["pid"];
				break;
				case "encounter_nr":
						$filters["encounter_nr"] = $_POST["encounter_nr"];
				break;
		}
}


$current_page = $_POST['page'];
if (!$current_page) $current_page = 0;
$list_rows = 15;
switch (strtolower($_POST['jump'])) {
		case 'last':
				$current_page = $_POST['lastpage'];
		break;
		case 'prev':
				if ($current_page > 0) $current_page--;
		break;
		case 'next':
				if ($current_page < $_POST['lastpage']) $current_page++;
		break;
		case 'first':
				$current_page=0;
		break;
}

$_SESSION["current_page"] = $current_page;

//display
if (!$_GET['src']){
	$result = $objpatients->getDialysisPatients($filters, $list_rows * $current_page, $list_rows, $keyword);
}else{
	$result = $objpatients->getDialysisPatients($filters, $list_rows * $current_page, $list_rows, $keyword);
}


$rows = "";
$last_page = 0;
$count=0;

if ($result) {
	
	$rows_found = $objpatients->FoundRows();
	if ($rows_found) {
			$last_page = floor($rows_found / $list_rows);
			$first_item = $current_page * $list_rows + 1;
			$last_item = ($current_page+1) * $list_rows;
			if ($last_item > $rows_found) $last_item = $rows_found;
			$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
	}

	while ($row = $result->FetchRow()) {

		$canEdit = "<a title=\"View\" href=\"../billing_new/dialysis-edit.php".URL_APPEND."&userck=$userck&nr=".$row["id"]."&from=billing-list\">
		<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" />
		</a>";

		$rows .= "<tr class=\"$class\">
					<td>".$row["id"]."</td>
					<td>".$row["start_date"]."</td>
					<td>".$row["end_date"]."</td>
					<td>".$row["pid"]."</td>
					<td>".$row["p_name"]."</td>
					<td>".$row["sessions"]."</td>
					<td>".$row['trans_flag']."</td>
					<td align=\"right\">".$canEdit."</td>
				</tr>\n";

		$count++;
	}
}
else {
	$rows .= '        <tr><td colspan="10">No patients found ...</td></tr>';
	$sWarning = $objpatients->error_msg;
}

if (!$rows) {
		$records_found = FALSE;
		$rows .= '        <tr><td colspan="10">'.(!$_GET['src'] ? 'No patient admitted at this time ...' : 'No claims found ...').'</td></tr>';
}
ob_start();
?>

<form action="<?= $thisfile.URL_APPEND.'&sid=$sid&target=seg_billing_list_patients&refresh=true&user_origin='.$_GET['user_origin'] ?>" method="post" name="suchform" onSubmit="return validate()">
	<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
	<div style="width:70%">
		<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">
			<tbody>
				<tr><td align="left" class="jedPanelHeader" ><strong>Search options</strong></td></tr>
				<tr>
					<td nowrap="nowrap" align="left" class="jedPanel">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td width="50" align="right">
									<input type="checkbox" id="chkspecific" name="chkspecific" onclick="selrecordOnChange(); keepFilters(0);" <?= ($_POST['chkspecific'] ? 'checked' : '') ?>/>
								</td>
								<td width="5%" align="left" nowrap="nowrap">Search By</td>
								<td>
									<select class="jedInput" name="selrecordby" id="selrecordby" onchange="selrecordOnChange(); keepFilters(0);"/>
										<option value="name" <?= $_POST["selrecordby"]=="name" ? 'selected="selected"' : '' ?>>Name</option>
										<option value="pid" <?= $_POST["selrecordby"]=="pid" ? 'selected="selected"' : '' ?>>HRN</option>
									</select>
								<td>
									<span>
										<input class="jedInput" name="keyword" id="keyword" onblur="keepFilters(0);" type="text" size="30" value="<?= $_POST['keyword'] ?>"/>
									</span>	
								</td>
							</tr>
							<tr>
								<td width="50" align="right">
									<input type="checkbox" id="chkspecific_" name="chkspecific_" onclick="selrecordOnChange(); keepFilters(0);" <?= ($_POST['chkspecific_'] ? 'checked' : '') ?>/>
								</td>
								<td width="5%" align="left" nowrap="nowrap">Status</td>
								<td>
									<select class="jedInput" name="selrecord" id="selrecord" onchange="selrecordOnChange(); keepFilters(0);"/>
											<option value="done" <?= $_POST["selrecord"]=="done" ? 'selected="selected"' : '' ?>>Done</option>
											<option value="active" <?= $_POST["selrecord"]=="active" ? 'selected="selected"' : '' ?>>Active</option>    
									</select>
								</td>
							</tr>
							<tr>
								<td width="5%" align="right"><input type="checkbox" id="chkdate" name="chkdate" <?= ($_POST['chkdate'] ? 'checked' : '') ?> onclick="seldateOnChange();keepFilters(1);"/></td>
								<td width="15%" nowrap="nowrap" align="left">Date</td>
								<td width="20%" align="left">
									<script language="javascript" type="text/javascript">
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
										}
									</script>
									<select class="jedInput" id="seldate" name="seldate" onchange="seldateOnChange(); keepFilters(1);">
											<option value="today" <?= $_POST["seldate"]=="today" ? 'selected="selected"' : '' ?>>Today</option>
											<option value="thisweek" <?= $_POST["seldate"]=="thisweek" ? 'selected="selected"' : '' ?>>This week</option>
											<option value="thismonth" <?= $_POST["seldate"]=="thismonth" ? 'selected="selected"' : '' ?>>This month</option>
											<option value="specificdate" <?= $_POST["seldate"]=="specificdate" ? 'selected="selected"' : '' ?>>Specific date</option>
											<option value="between" <?= $_POST["seldate"]=="between" ? 'selected="selected"' : '' ?>>Between</option>
									</select>
									</td>
									<td>
									<span name="seldateoptions" segOption="specificdate" <?= ($_POST["seldate"]=="specificdate") && $_POST['chkdate'] ? '' : 'style="display:none"' ?>>
											<input onchange="keepFilters(1);" class="jedInput" name="specificdate" id="specificdate" type="text" size="8" value="<?= $_POST['specificdate'] ?>"/>
											<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
											<script type="text/javascript">
													Calendar.setup ({
															inputField : "specificdate", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
													});
											</script>
									</span>
									<span name="seldateoptions" segOption="between" <?= ($_POST["seldate"]=="between") && $_POST['chkdate'] ? '' : 'style="display:none"' ?>>
											<input onchange="keepFilters(1);" class="jedInput" name="between1" id="between1" type="text" size="8" value="<?= $_POST['between1'] ?>"/>
											<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
											<script type="text/javascript">
													Calendar.setup ({
															inputField : "between1", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between1", singleClick : true, step : 1
													});
											</script>
											to
											<input onchange="keepFilters(1);" class="jedInput" name="between2" id="between2" type="text" size="8" value="<?= $_POST['between2'] ?>"/>
											<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
											<script type="text/javascript">
													Calendar.setup ({
															inputField : "between2", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_between2", singleClick : true, step : 1
													});
											</script>
									</span>
								</td>
							</tr>
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
		<tr>
			<td>
				<h1>
					Search result: <?php echo $search_title;  ?>
				</h1>
			</td>
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
						 <th width="5%">Reference #</th>
						 <th width="7%">Date Started</th>
						 <th width="7%">Date Ended</th>
						 <th width="10%">HRN</th>
						 <th width="*">Name</th>
						 <th width="5%">Sessions</th>
						 <th width="10%">Status</th>
						 <th width="1%">Action</th>
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

<input type="hidden" id="delete" name="delete" value="" />

<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">
<input type="hidden" id="applied" name="applied" value="1">
<input type="hidden" id="root_path" name="root_path" value="<?php echo $root_path ?>" />
<input type="hidden" id="fill_up" name="fill_up" value="">
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
