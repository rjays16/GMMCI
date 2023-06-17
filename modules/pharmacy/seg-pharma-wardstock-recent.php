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

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

/*$title="Recent ward stocks";
$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";
$thisfile='seg-pharma-products-main.php';*/


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
 $smarty->assign('sToolbarTitle','Pharmacy::Recent ward stocks');

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Pharmacy::Recent ward stocks');

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start();

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
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
	var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

	function openPDF(ref) {
		window.open('seg-pharma-order.php'+URL_FORWARD+'&target=print-jasper&ref='+ref,'openPDF',"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}

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
		wslist.reload();
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
	
	function deleteItem(id) {
		var dform = document.forms[0]
		$('delete').value = id
		dform.submit()
	}
	
	function view(id) {
		overlib(
			OLiframeContent('apotheke-pass.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>&target=orderview&ref='+id+'&from=CLOSE_WINDOW', 800, 420, 'fSelEnc', 0, 'auto'),
			WIDTH,800, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>images/close_red.gif border=0 >',
			CAPTION,'View order',
			MIDX,0, MIDY,0, 
			STATUS,'View order');
	}
	
	function edit(nr) {
		overlib(
			OLiframeContent('apotheke-pass.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>&target=editstock&nr='+nr+'&from=CLOSE_WINDOW', 650, 350, 'fSelEnc', 0, 'auto'),
			WIDTH,650, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>images/close_red.gif border=0 >',
			CAPTION,'Edit ward stock entry',
			MIDX,0, MIDY,0, 
			STATUS,'Edit ward stock entry');
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
	
	function validate() {
	}
	//&area=$area&from=".$src."&nr=$nr
	<!--added by bryan on Spet 16,2008-->
	function addStockRow(details) {
		list = $("wslst");
		if (list) {
			var dBody=list.getElementsByTagName("tbody")[0];
			if (dBody) {
				var lastRowNum = null,
						nr = details["stock_nr"];
						dRows = dBody.getElementsByTagName("tr");
				if (details["FLAG"]=="1") {
					alt = (dRows.length%2)+1
					src =
					'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
						'<td  style="color:800000;font:bold 11px Tahoma">'+details['stock_date']+'</td>'+
						'<td align="center">'+nr+'</td>'+
						'<td align="left">'+details["ward_name"]+'</td>'+
						'<td align="center">'+details["items"]+'</td>'+
						'<td align="right">'+details["encoder"]+'</td>'+
						'<td align="center">'+details["area_full"]+'</td>'+
						'<td class="centerAlign" nowrap="nowrap">'+
							'<img class="segSimulatedLink" src="<?=$root_path ?>images/cashier_edit.gif" border="0" align="absmiddle" onclick="edit(\''+nr+'\')" />\n'+
							'<img title="Delete" class="segSimulatedLink" src="<?= $root_path ?>images/cashier_delete.gif" border="0" align="absmiddle" onclick="if (confirm(\'Delete this order?\')) deleteItem(\''+nr+'\')"/>'+
						'</td>'+
					'</tr>';
				}
				else {
					src = "<tr><td colspan=\"8\">List is currently empty...</td></tr>";	
				}
				dBody.innerHTML += src;
				return true;
			}
		}
		return false;
	}
	
-->
</script>

<?php
#added by bryan Sept 16,2008
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$listgen->setListSettings('MAX_ROWS','10');
$listgen->setListSettings('RELOAD_ONLOAD', TRUE);
$wslst = &$listgen->createList('wslst',array('Date','Stock NR','Ward','Items', 'Encoder', 'Area', 'Details'),array(1,0,0,0,0,0,NULL),'populateWardstockList');
$wslst->addMethod = 'addStockRow';
$wslst->fetcherParams = array($_GET['area']);
$wslst->columnWidths = array("10%", "10%", "10%", "*", "10%", "10%", "10%");
$smarty->assign('sWardstockList',$wslst->getHTML());

$smarty->assign('sSearchResults',$rows);
$smarty->assign('sRootPath',$root_path);


?>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
include($root_path."include/care_api_classes/class_pharma_ward.php");
$wc = new SegPharmaWard();

if ($_POST['delete']) {
	if ($order->deleteOrder($_POST['delete'])) {
		$sWarning = 'Item successfully deleted!';
	}
	else {
		$sWarning = 'Error deleting order: '.$db->ErrorMsg();
	}
}

if ($_REQUEST['area']) {
	$filters["AREA"] = $_REQUEST['area'];
}

$filters["ENCODER"] = $_SESSION['sess_temp_userid'];
$filters["THISSHIFT"] = "";


$current_page = $_REQUEST['page'];
if (!$current_page) $current_page = 0;
$list_rows = 10;
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
$result = $wc->getStockList($filters, $list_rows * $current_page, $list_rows);
$rows = "";
$last_page = 0;
$count=0;	
if ($result) {
	$rows_found = $wc->FoundRows();
	if ($rows_found) {
		$last_page = floor($rows_found / $list_rows);
		$first_item = $current_page * $list_rows + 1;
		$last_item = ($current_page+1) * $list_rows;
		if ($last_item > $rows_found) $last_item = $rows_found;
		$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
	}
	while ($row=$result->FetchRow()) {

		#$class = (($count%2)==0)?"":"wardlistrow2";
		$items = explode("\n",$row["items"]);
		$items = implode(", ",$items);
			
		$total_items = (int) $row['count_total_items'];
		$total_served = (int) $row['count_served_items'];

		$records_found = TRUE;
		
		$dt = strtotime($row["stock_date"]);		
		$rows .= "		<tr class=\"$class\">
				<td align=\"center\">
					".date("Y-m-d h:ia",$dt)."
					<!-- <a href=\"seg-pharma-order.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid&target=list&view=date&sdate=".urlencode(str_replace("-","",$row["orderdate"]))."\">
						".substr($row["stock_date"],0,10)."
					</a> -->
				</td>
				<td style=\"color:#000066; text-align:center\">".$row["stock_nr"]."</td>
				<td style=\"color:#660000\">".$row["ward_name"]."</td>
				<td>".$items."</td>
				<td align=\"left\">".$row["encoder"]."</td>
				<td style=\"color:#007\" align=\"center\">".$row["area_full"]."</td>
				<td align=\"right\" nowrap=\"nowrap\">
					<a title=\"Edit\" href=\"apotheke-pass.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid&target=editstock&nr=".$row["stock_nr"]."&from=recentstock\"><img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\"/></a>
					<a title=\"Delete\" href=\"#\">
						<img class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"if (confirm('Delete this order?')) deleteItem('".$row["refno"]."')\"/>
					</a>
				</td>
			</tr>\n";
		$count++;
	}
}
else {
	print_r($result);
	$rows .= '		<tr><td colspan="10">'.$order->sql.'</td></tr>';
}

if (!$rows) {
	$records_found = FALSE;
	$rows .= '		<tr><td colspan="10">No orders/requests available at this time...</td></tr>';
}

ob_start();
?>

<!-- commented out by bryan on sept 24, 2008
<form action="<?= $thisfile.URL_APPEND."&target=recent&clear_ck_sid=".$clear_ck_sid ?>&area=<?= $_REQUEST['area'] ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
<div style="width:85%">
	<div class="segContentPaneHeader" style="margin-top:10px">
		<h1>
			Your ward stocks for this shift:
<?php
	echo $search_title;
?>
		</h1>
	</div>

		<div class="segContentPane">
		<table id="" class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
					<th width="10%"><a href="#">Date</a></th>
					<th width="6%" nowrap="nowrap">Stock Nr</th>
					<th width="15%">Ward</th>
					<th width="*">Items</th>
					<th width="10%">Encoder</th>
					<th width="4%">Area</th>
					<th width="6%">Details</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<br />
	</div>
</div>
-->
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
 $smarty->assign('sMainBlockIncludeFile','pharmacy/wardstocklist-main.tpl');
 $smarty->display('common/mainframe.tpl');
?>