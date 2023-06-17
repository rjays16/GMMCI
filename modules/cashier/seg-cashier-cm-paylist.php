<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

global $db;
require($root_path."modules/pharmacy/ajax/order.common.php");
include($root_path."include/care_api_classes/class_cashier.php");
$cClass = new SegCashier();
define('NO_2LEVEL_CHK',1);
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
	$cancel_ok = $cClass->CancelOR($_POST['cancelor']);
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
 $smarty->assign('sToolbarTitle',"");
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Credit memo::View payments");

 # Assign Body Onload javascript code
 if ($cancel_ok)
	 $smarty->assign('sOnLoadJs','onLoad="alert(\'Payment successfully cancelled!\')"');

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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/cashier/js/cashier-memo-paylist.js"></script>
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

	function cancelOR(orno) {
		if (confirm('Do you wish to cancel this payment entry?')) {
			$('cancelor').value = orno;
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

global $db;

# Setup filters
$filters = array();
#$filters["ISCASH"] = 1;
#if (!$_REQUEST['mode']) $_REQUEST['mode'] = 'date';
#if (!$_REQUEST['seldate']) $_REQUEST['seldate'] = 'today';
#if (!$_REQUEST['selpayee']) $_REQUEST['selpayee'] = 'name';

#if ($_REQUEST['seldatedept'])
#	$filters["DEPT"] = $_REQUEST['seldatedept'];
/*
if (strcasecmp($_SESSION['sess_temp_userid'],'admin')) {
	$filters['encoder'] = $_SESSION['sess_temp_userid'];
}   */
//$_SESSION["current_page"] = $current_page;



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

$filters["PATIENT"] = $_REQUEST['id'];
$filters["NOCANCEL"] = 1;
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
		#$class = (($count%2)==0)?"":"wardlistrow2";

		if (!$row['items'] && $row['deposit']) {
			$row['items'] = "Deposit";
		}
		#$span = '<span style="color:#800000;font:bold 11px Tahoma">';
		#$items = $span . implode('</span>, '.$span, explode("\n", $row['items'])) . '</span>';

		$items = implode(', ', explode("\n", $row['items']));
		if (strlen($items)>55) {
			$items = substr($items, 0, 55) .
				'<span id="show_'.$count.'" style="display:none">'.substr($items,55).'</span><span id="more_'.$count.'" class="segLink" style="margin-left:5px;font:bold 11px Arial" onclick="more(\''.$count.'\')" style>more&raquo;</span>';
		}

		$collection = $row['type_main'];
		if ($row['type_sub']) $collection.="<br/>(".$row['type_sub'].")";
		if ($row['or_date'] && $row['or_date']!='0000-00-00 00:00:00') {
			$ordate = date('Y-m-d h:ia',strtotime($row["or_date"]));
		}
		$rows .= "		<tr class=\"$class\">
		<td class=\"centerAlign\">".$ordate."</td>
		<td class=\"centerAlign\" style=\"color:#000080\">
			<input id=\"or_$count\" type=\"hidden\" value=\"".$row['or_no']."\"/>
			".$row["or_no"]."
		</td>
		<td>$name</td>
		<td style=\"color:#404040;font:bold 11px Tahoma\">".$items."</td>
		<td align=\"right\" style=\"color:#000080\">".number_format($row['amount_due'],2,'.',',')."</td>
		<td class=\"centerAlign\">
			<img id=\"expand-$count\" title=\"Expand\" src=\"".$root_path."images/cashier_expand.gif\" class=\"segSimulatedLink\" onclick=\"showDetails('$count',true)\">
			<img id=\"collapse-$count\" title=\"Collapse\" src=\"".$root_path."images/cashier_collapse.gif\" class=\"segSimulatedLink\" style=\"display:none\" onclick=\"showDetails('$count',false)\">
		</td>
	</tr>
	<tr id=\"details-$count\" style=\"display:none\">
		<td id=\"detailstd-$count\" style=\"padding:4px\" class=\"plain\" colspan=\"8\" align=\"center\">
			<div>
				<iframe id=\"d$count\" frameborder=\"0\" scrolling=\"no\" style=\"height:0px;overflow-x:hidden;overflow-y:hidden;border:3px solid #003366;width:99%;background-color:white;display:block\"></iframe>
			</div>
		</td>
	</tr>
";
	$count++;
	}
}
else {
	$rows = '		<tr><td colspan="10">Data unavailable at this time...'.$cClass->sql.'</td></tr>';
}

if (!$rows) {
	$rows = '		<tr><td colspan="10">No recent payment entries found for this person...</td></tr>';
}

ob_start();
?>

<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">

<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
<input type="hidden" id="id" name="id" value="<?= $_REQUEST['id'] ?>">

<div style="width:98%;padding:0.4%">
	<table width="100%" cellspacing="2" cellpadding="2">
		<tbody>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:350px; width:100%; background-color:#e5e5e5">
						<table class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0" style="">
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
					<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
				</td>
			</tr>
		</tbody>
	</table>
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