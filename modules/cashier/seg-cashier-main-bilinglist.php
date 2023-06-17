<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','order.php');
define('NO_2LEVEL_CHK',1);
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

$title=$LDPharmacy;
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
$imgpath=$root_path."pharma/img/";

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::Process billing");

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Process billing");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

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
<style type="text/css">
<!--
.tabFrame {
	margin:5px;
}
-->
</style> 

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
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
	
	function showBill(ref) {
		window.location.href = "seg-cashier-billing-main.php<?= URL_APPEND ?>&clear_ck_sid=$clear_ck_sid&nr="+ref;
	}
	
	function validate() {
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
	
	
	function pSearchClose() {
		$('search').disabled = true
		cClick()
		document.forms[0].submit()
	}
-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
global $db;

include($root_path."include/care_api_classes/class_cashier.php");
$cClass = new SegCashier();

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
if ($_POST['submitted'] || $_REQUEST['patient']) {
	$patient = $_REQUEST['patient'];
	$result = $cClass->GetPatientBillingEncounter($patient, $list_rows * $current_page, $list_rows);
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
			$class = (($count%2)==0)?"":"wardlistrow2";
			foreach ($items as $i=>$v) {
				$items[$i] = "<span style=\"white-space:nowrap;color:".stringToColor($v)."\">$v</span>";
			}
			
			$rows .= "		<tr class=\"$class\">
				<td>".$row["bill_nr"]."</td>
				<td style=\"color:#000080\" class=\"centerAlign\">".$row["bill_dte"]."</td>
				<td style=\"color:#000080\" class=\"centerAlign\">".$row["bill_frmdte"]."</td>
				<td style=\"color:#008000\" >".$row["encounter_nr"]."</td>
				<td>".$row["name_first"]." ".($row["name_middle"] ? (substr($row["name_middle"],0,1).". ") : '').$row['name_last']."</td>
				<td align=\"center\">".($row["is_paid"] ? ("<img src=\"".$root_path."images/paid_item.gif\" align=\"absmiddle\" />") : "")."</td>
				<td class=\"centerAlign\"><input class=\"segButton\" type=\"button\" style=\"color:#000060\" value=\"View\" onclick=\"showBill('".$row["bill_nr"]."')\" /></td>
			</tr>\n";
			$count++;
		}
		if (!$rows)
			$rows = '		<tr><td colspan="10">No billing records found for this patient...</td></tr>';
	}
	else
		$rows = '		<tr><td colspan="10">'.$cClass->sql.'</td></tr>';
}

if (!$rows)
	$rows = '		<tr><td colspan="10">Select patient record and click Search to begin query...</td></tr>';


ob_start();
?>

<br>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">

<div style="width:50%">

	<hr width="100%" size="1" />
	<div style="white-space:nowrap	">
		<span>Select patient record</span>
		<input class="segInput" name="patientname" id="patientname" readonly="readonly" style="width:150px" type="text" value="<?= $_REQUEST['patientname'] ?>" />
		<input name="patient" id="patient" type="hidden" value="<?= $_REQUEST['patient'] ?>"/>
		<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="cursor:pointer;"
			onclick="overlib(
				OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?var_pid=patient&var_name=patientname&var_include_enc=1', 700, 400, 'fSelEnc', 0, 'auto'),
				WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?= $root_path ?>images/close_red.gif border=0 >',
				CAPTION,'Select registered person',
				MIDX,0, MIDY,0, 
				STATUS,'Select registered person'); return false;"
  	    onmouseout="nd();" />
		<input id="search" type="submit" class="segButton" value="Search" <?= $_REQUEST['patient'] ? '' : 'disabled="disabled"' ?>/>
	</div>
	<hr width="100%" size="1" />

	<input type="hidden" name="submitted" value="1">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
</div>

<div style="width:80%">
	<div class="">
		<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
					<th width="10%" nowrap="nowrap">Bill Nr.</th>
					<th width="15%" nowrap="nowrap">Bill Date</th>
					<th width="15%" nowrap="nowrap">Bill From</th>
					<th width="10%" nowrap="nowrap"><?= $LDEncounterNr ?></th>
					<th width="*" nowrap="nowrap">Patient Name</th>
					<th width="10%" nowrap="nowrap">Status</th>
					<th width="1%">&nbsp;</th>
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