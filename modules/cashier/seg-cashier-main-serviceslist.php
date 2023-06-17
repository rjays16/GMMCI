<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/cashier/ajax/olr-services.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

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
 $smarty->assign('sToolbarTitle',"Cashier::Other Hospital Services");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Other Hospital Services");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="$(\'search\').focus()"');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, forceSearch, page) {
	var searchEL = $(searchID);
	var olr = '';
	if ($('or').checked) olr += 'o';
	if ($('ld').checked) olr += 'l';
	if ($('rd').checked) olr += 'r';
	
	if ((searchEL && lastSearch != (searchEL.value.concat("\n"+olr)) ) || forceSearch) {
			searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		AJAXTimerID = setTimeout("xajax_populateOLRServiceList('"+searchID+"','"+searchEL.value+"','"+olr+"','"+page+"')",200);
		lastSearch = searchEL.value + '\n' + olr;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>modules/cashier/js/olr-services.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

	<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:2px 2px; padding-left:10px; ">
						Search services <input id="search" class="segInput" type="text" style="width:60%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (event.keyCode==13) startAJAXSearch(this.id,false,0)" />
						<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',true,0);return false;" align="absmiddle" />
					</div>
					<div style="padding:2px 2px; padding-left:120px;" >
						<input id="ld" class="jedInput" type="checkbox" value="ld" checked="checked" /><label class="jedInput" for="ld">Laboratory</label>
						<input id="rd" class="jedInput" type="checkbox" value="rd" checked="checked" /><label class="jedInput" for="rd">Radiology</label>
						<input id="or" class="jedInput" type="checkbox" value="or" checked="checked" /><label class="jedInput" for="or">Operating Room</label>
						
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:285px; width:100%; background-color:#e5e5e5">
						<table id="service-list" class="jedList" cellpadding="0" cellspacing="0" width="100%">
							<thead>
								<tr class="nav">
									<th colspan="9">
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
									<th width="10%">Code</th>
									<th width="*" align="left">Name/Description</th>
									<th width="10%" align="center" style="font-size:11px;padding-right:15px">Source</th>
									<th width="15%" align="center" style="font-size:11px;padding-right:15px">Group</th>
									<th width="10%" align="center" style="font-size:11px;padding-right:15px">Deposit</th>
									<th width="1%">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="6" style="font-weight:normal">No such service exists...</td>
								</tr>
							</tbody>
						</table>
					</div>
					<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="left" border="0" style="display:none"/>
				</td>
			</tr>
		</tbody>
	</table>
						
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" id="type" name="type" value="<?= $_GET['type'] ?>">

<?php

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

</div>
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
