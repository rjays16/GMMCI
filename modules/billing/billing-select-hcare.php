<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require_once($root_path.'modules/billing/ajax/billing-hsearch.common.php');
require($root_path.'include/inc_environment_global.php');	
#	require_once($root_path.'modules/insurance_co/ajax/hcplan-admin.common.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='search.php';
define('LANG_FILE','finance.php');
define('NO_2LEVEL_CHK',1);

$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;
$thisfile=basename(__FILE__);
if (isset($_GET["from"])) {
    if ($_GET["from"] == 0) 
        $breakfile = $root_path."modules/billing/billing-transmittal.php".URL_APPEND."&userck=$userck";    
    else
        $breakfile = $root_path."modules/billing/claim-posting.php".URL_APPEND."&userck=$userck";
}
else
$breakfile = $root_path."modules/billing/billing-transmittal.php".URL_APPEND."&userck=$userck";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDInsuranceCo :: $LDManager");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('insurance_list.php')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDInsuranceCo :: $LDListAll");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','');
 $smarty->assign('sOnLoadJs','onLoad="document.getElementById(\'search\').focus();"');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--

//--------------added by VAN 09-12-07------------------
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page, mod) {
	var searchEL = $(searchID);
	if (mod) 
		searchEL.value = "";
	
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("hcplanlistTable-body").style.display = "none";
		AJAXTimerID = setTimeout("xajax_populateInsuranceList('"+searchID+"','"+searchEL.value+"',"+page+")",100);
		lastSearch = searchEL.value;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("hcplanlistTable-body").style.display = "";
		searchEL.style.color = "";
	}	
}


// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>modules/billing/js/billing-hcare-search-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
#include($root_path."include/care_api_classes/class_order.php");
#$order = new SegOrder('pharma');

# Load the insurance object
#require_once($root_path.'include/care_api_classes/class_insurance.php');
#$ins_obj=new Insurance;

ob_start();
?>
<table width="98%" cellspacing="2" cellpadding="2" style="margin:1%">
	<tbody>
		<tr>
			<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
				<table width="95%" border="0" cellpadding="0" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:1%">
					<tr>
						<td width="15%">
							Search health insurance<br />								
						</td>
						<td valign="middle" width="*">
							<input id="search" class="segInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0,0)" />
							<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0,0);return false;" align="absmiddle" /><br />
						</td>
					</tr>
					<tr>
						<td></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:308px; width:100%; background-color:#e5e5e5">
					<table id="hcplanlistTable" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
						<thead>
							<tr class="nav">
								<th colspan="6">
									<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
										<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
										<span title="First">First</span>
									</div>
									<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
										<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
										<span title="Previous">Previous</span>
									</div>
									<div id="pageShow" style="float:left; margin-left:10px">
										<span></span>
									</div>
									<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
										<span title="Last">Last</span>
										<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
									</div>
									<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
										<span title="Next">Next</span>
										<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
									</div>
								</th>
							</tr>
							<tr>
								<!--<th width="1%"></th>-->
								<th width="15%" align="center">Firm ID</th>
								<th width="30%" align="left">Insurance Company Name</th>
								<th width="15%" align="center">Phone No.</th>
								<th width="15%" align="center">Fax No.</th>
								<th width="22%" align="center">Address</th>
								<th width="3%" align="center">&nbsp;</th>
							</tr>
						</thead>
						<tbody id="hcplanlistTable-body">
							<?= $rows ?>
						</tbody>
					</table>
					<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="mode" value="search">
<input type="hidden" id="from" name="from" value="<?=$_GET['from']?>">                      
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
<form action="<?php echo $breakfile?>" method="post">
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>
<?php if ($from=="multiple")
echo '<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>';
?>

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
