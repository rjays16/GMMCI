<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/pharmacy/ajax/order-tray.common.php");
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
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
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
 $smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function prepareAddEx() {
	var prod = document.getElementsByName('prod[]');
	var qty = document.getElementsByName('qty[]');
	var prcCash = document.getElementsByName('prcCash[]');
	var prcCharge = document.getElementsByName('prcCharge[]');
	var nm = document.getElementsByName('pname[]');
	
	var details = new Object();
	var list = window.opener.document.getElementById('order-list');
	var result=false;
	var msg = "";
	for (var i=0;i<prod.length;i++) {
		result = false;
		if (prod[i].checked) {
			details.id = prod[i].value;
			details.name = nm[i].value;
			details.qty = qty[i].value;
			details.prcCash = prcCash[i].value;
			details.prcCharge = prcCharge[i].value;
			result = window.opener.appendOrder(list,details);
			msg += "     x" + qty[i].value + " " + nm[i].value + "\n";
			qty[i].value = 0;
			prod[i].checked = false;
		}
	}
	window.opener.refreshTotal();
	if (msg)
		msg = "The following items were added to the order tray:\n" + msg;
	else
		msg = "An error has occurred! The selected items were not added...";	
	alert(msg);
}

function startAJAXSearch(searchID) {
	var searchEL = $(searchID);
	var areaSelected = window.parent.$('area').options[window.parent.$('area').selectedIndex].value;
	if (searchEL && lastSearch != searchEL.value) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		AJAXTimerID = setTimeout("xajax_populateProductList('"+searchID+"','"+searchEL.value+"','"+window.parent.$('discountid').value+"','"+areaSelected+"')",200);
		lastSearch = searchEL.value;
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
<script type="text/javascript" src="<?=$root_path?>modules/pharmacy/js/refund-tray-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/ellipsis.js"></script>
<style type="text/css">
<!--
.ellipsis {
	font-weight:normal;
	font-size:11px;
}

.ellipsis, .ellipsis div {
	white-space:nowrap;
	text-overflow:ellipsis; /* for internet explorer */
	overflow:hidden;
	width:200px;
}

html>body .ellipsis {
	clear:both;
}

html>body .ellipsis:after {
	content: "...";
}

html>body .ellipsis div {
	max-width:188px;
	float:left;
}
-->
</style>
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
					<div style="padding:4px 2px; padding-left:10px; ">
						Search product <input id="search" class="segInput" type="text" style="width:60%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id)" />
						<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search');return false;" align="absmiddle" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:265px; width:100%; background-color:#e5e5e5">
						<table id="product-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
							<thead>
								<tr>
									<th width="*">Name/Description</th>
									<th width="15%">Code</th>
									<th width="15%">Max. refundable</th>
									<th width="8%">Qty</th>
									<th width="1%">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="10" style="font-weight:normal">No such product exists...</td>
								</tr>
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
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
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
