<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
global $db;

$local_user='ck_prod_db_user';
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

#$from = (isset($_POST['pfrom']) ? $_POST['pfrom'] : $_GET['from']);
#$title = (isset($_POST['ptitle']) ? $_POST['ptitle'] : $_GET['title']);

$from = $_REQUEST["from"];
$target = $_REQUEST['target'];
if ($target=="databank")
	$title="Cashier::Other Services Databank";
elseif ($target=="miscellaneous")
	$title="Billing :: Miscellaneous Charge Items";

//$title=$LDPharmacy;
if (($from == "") || (!isset($from)))
	$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
else
	$breakfile=$from.URL_APPEND;
$imgpath=$root_path."cashier/img/";
$thisfile='seg-cashier-services-main.php';

	# Note: it is advisable to load this after the inc_front_chain_lang.php so
	# that the smarty script can use the user configured template theme
	include_once($root_path."include/care_api_classes/class_cashier_service.php");
	include_once($root_path."include/care_api_classes/class_cashier.php");
	$cClass = new SegCashier();
	$pclass = new SegCashierService($target);

	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

if ($_POST['delete']) {
	if ($pclass->deleteService($_POST['delete'])) {
		$smarty->assign('sWarning','Service item successfully deleted!');
	}
	else {
		$sWarning = 'Error deleting service: '.$db->ErrorMsg();
	}
}

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

	# Saving
	if (isset($_POST["submitted"])) {
		$name = $_POST["name"];
		$type = $_POST["type"];

		$count=0;
		$rows = "";
		$last_page = 0;

		$result=$pclass->searchServices($name, $type, $include_locked=TRUE, $list_rows * $current_page, $list_rows);
		if ($result) {

			$rows_found = $pclass->FoundRows();
			if ($rows_found) {
				$last_page = floor($rows_found / $list_rows);
				$first_item = $current_page * $list_rows + 1;
				$last_item = ($current_page+1) * $list_rows;
				if ($last_item > $rows_found) $last_item = $rows_found;
				$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
			}

			while ($row=$result->FetchRow()) {
				$description = $row['description'] ? $row['description'] : '<em>No description</em>';
				$price = ($row['price']-0) ? number_format($row['price'],2) : 'Arbitrary';
				$class = (($count%2)==0)?"":"wardlistrow2";
				$code = $row['code'];
				$rows .= "		<tr id=\"row_".$code."\" class=\"$class\">
				<td style=\"color:#800000\"><span id=\"id_".$code."\">".$code."</span></td>
				<td><span id=\"name_".$code."\">".$row["name"]."</span><br><span id=\"desc_".$code."\" class=\"description\">".$row['name_short']."</span></td>
				<td align=\"right\"><span id=\"price_".$code."\">".$price."</span></td>
				<td style=\"color:#006\" align=\"center\"><span id=\"ptype_".$code."\">".$row["ptype_name"]."</span></td>
				<td style=\"color:#006\" align=\"center\"><span id=\"type_".$code."\">".$row["type_name"]."</span></td>
				<td style=\"color:#006\" align=\"center\"><span id=\"type_".$code."\">".$row["dept_name"]."</span></td>
				<td align=\"center\"><img id=\"lock_".$code."\" src=\"".$root_path."gui/img/common/default/lock.gif\" border=\"0\" style=\"".($row['lockflag']==1 ? "" : "display:none")."\"/></td>
				<td align=\"center\" nowrap=\"nowrap\">
					<!-- <img title=\"Edit item\" class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" onclick=\"editItem('".$code."')\" border=\"0\" alt=\"Edit item\"/> -->
					<input class=\"segSimulatedLink\" type=\"image\" title=\"Edit\" src=\"".$root_path."images/cashier_edit.gif\" onclick=\"editItem('".$code."'); return false;\" />
					<input class=\"segSimulatedLink\" type=\"image\" title=\"Delete\" src=\"".$root_path."images/cashier_delete.gif\" onclick=\"if (confirm('Delete this item?')) deleteItem('".$code."'); return false;\" />
				</td>
			</tr>\n";
				$count++;
			}
			if (!$rows)
				$rows = '		<tr><td colspan="10">No matches found in the database...</td></tr>';
		}
		else {
			$rows = '		<tr><td colspan="10">'.$pclass->sql.'</td></tr>';
		}
	}
	else {
		$rows = '		<tr><td colspan="10">Click the Search button to begin query...</td></tr>';
	}

 $smarty->assign('sSearchResults',$rows);
 $smarty->assign('sRootPath',$root_path);

 # Title in the title bar
 #$smarty->assign('sToolbarTitle',"Cashier::Other Services Manager");
 $smarty->assign('sToolbarTitle',$title);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 #$smarty->assign('sWindowTitle',"Cashier::Other Services Manager");
 $smarty->assign('sWindowTitle',$title);

 # Assign Body Onload javascript code
 $onLoadJS='onload=""';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
	 # Load the javascript code
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
<script type="text/javascript" src="js/cashier-main.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function editItem(nr) {
		if (!nr) nr="";
		if (nr) {
			return overlib(
				OLiframeContent('seg-cashier-services-edit.php?service_code='+nr+'&target=<?= $target ?>', 600, 370, 'fProduct', 0, 'no'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
				CAPTION,'Edit direct payment item',
				MIDX,0, MIDY,0,
				STATUS,'Edit payment item');
		}
		else
			return overlib(
				OLiframeContent('seg-cashier-services-edit.php?service_code='+nr+'&target=<?= $target ?>', 600, 370, 'fProduct', 0, 'auto'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
				CAPTION,'New direct payment item',
				MIDX,0, MIDY,0,
				STATUS,'New payment item');
	}

	function deleteItem(id) {
		var dform = document.forms[0]
		$('delete').value = id
		dform.submit()
	}

	function validate() {
		return true;
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
-->
</script>

<?php
#$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

# Product classification
	$types = array();
	if ($target=="miscellaneous")
		$result = $pclass->getAccountTypes(FALSE,NULL,TRUE);
	else
		$result = $pclass->getAccountTypes();
	if ($result) {
		while ($row=$result->FetchRow()) $types[] = $row;
	}

	$subtypes = array();
	if ($target=="miscellaneous")
		$result = $pclass->getSubAccountTypes(NULL,FALSE,NULL,TRUE);
	else
		$result = $pclass->getSubAccountTypes();
	if ($result) {
		while ($row=$result->FetchRow()) {
			if (!$subtypes[$row['parent_type']]) $subtypes[$row['parent_type']] = array();
				$subtypes[$row['parent_type']][] = $row;
		}
	}

	$typeHTML = "";
	foreach ($types as $type) {
		$typeHTML.= '						<optgroup label="'.$type['name_long'].'">';
		if (is_array($subtypes[$type['type_id']])) {
			foreach ($subtypes[$type['type_id']] as $subtype) {
				$checked=strtolower($subtype['type_id'])==strtolower($_REQUEST['type']) ? 'selected="selected"' : "";
				$typeHTML.="							<option value=\"".$subtype["type_id"]."\" $checked>".$subtype['name_long']."</option>\n";
				$count++;
			}
		}
/*
		else {
			$checked=strtolower($subtype['type_id'])==strtolower($_REQUEST['type']) ? 'selected="selected"' : "";
			$typeHTML.="							<option value=\"".$type["type_id"]."\" $checked>".$type['name_long']."</option>\n";
		}
		$typeHTML.= '						</optgroup>'; */
	}

	$typeHTML = "<select class=\"jedInput\" id=\"type\" name=\"type\" previousValue=\"$index\">\n".
		"						<option value=\"\" selected=\"selected\">-All-</option>".
		$typeHTML.
		"					</select>";$smarty->assign('sSelectAccountType',$typeHTML);
$smarty->assign('sCreateProduct','<input class="jedButton" type="button" value="'.($target == 'databank' ? "New Payment Item" : "New Miscellaneous Item").'" onclick="editItem()" />');
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target='.$_REQUEST['target'].'&from='.$_REQUEST['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';
?>
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
<?
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sNavControls',$sTemp);

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
	<input type="hidden" name="refno" value="<?php echo $sRefNo?>">
	<input type="hidden" name="dept" value="<?php echo $sDept?>">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
	<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
	<input type="hidden" id="jump" name="jump">
	<input type="hidden" id="delete" name="delete" value="" />
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<input class="segInput" type="button" align="center" value="Cancel payment">');
$smarty->assign('sContinueButton','<input class="segInput" type="submit" src="'.$root_path.'images/btn_submitorder" align="center" value="Process payment">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_databank_main.tpl');
$smarty->display('common/mainframe.tpl');

?>
