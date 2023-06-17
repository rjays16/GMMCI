<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
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

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function prepareAdd(id) {
	var details = new Object();
	
	details.ref = $('ref_'+id).value;
	details.id = $('id_'+id).value;
	details.name = $('name_'+id).value;
	details.generic = $('generic_'+id).value;
	details.previous = $('prev_'+id).value;
	details.qty = $('qty_'+id).value;
	details.price = $('price_'+id).value;

	result = window.parent.parent.appendItem(null,details)
	if (result) {	
		$('add_'+id).style.display = "none";
		$('added_'+id).style.display = "";
		alert("Item successfully added...");
	}
}


// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

	<table width="100%" cellspacing="2" cellpadding="2" style="margin:0.7%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; "></div>
				</td>
			</tr>
			<tr>
				<td>
					<table id="product-list" cellpadding="1" cellspacing="1" width="100%" style="border:1px solid #006699">
						<tbody>
							<tr>
								<td width="16%" class="jedPanelHeader">Code</td>
								<td width="*" class="jedPanelHeader" nowrap="nowrap">Item</td>
								<td width="5%" class="jedPanelHeader" align="center">Quantity</td>
								<td width="5%" class="jedPanelHeader" nowrap="nowrap" align="center">Returned</td>
								<td width="12%" class="jedPanelHeader" nowrap="nowrap" align="center">Price</td>
								<td width="5%" class="jedPanelHeader" nowrap="nowrap" align="center">Served?</td>
								<td width="12%" class="jedPanelHeader" nowrap="nowrap" align="center"></td>
							</tr>
<?php
	$ref = $_GET['ref'];
	$Nr = $_GET['nr'];
	require($root_path.'include/care_api_classes/class_pharma_return.php');
	$rc = new SegPharmaReturn();
	if ($_GET['mode'] == 'or') {
		$result = $rc->GetORDetailsForReturn($ref, $Nr);
	}
	elseif ($_GET['mode'] == 'ref') {
		$result = $rc->GetRefDetailsForReturn($ref, $Nr);
	}
	#print_r($rc->sql);
	$deptArray = array();
	if ($result) {
		$count = 0;
		while ($row = $result->FetchRow()) {
?>
							<tr>
								<td class="jedPanel3" align="center">
									<input type="hidden" id="ref_<?= $count ?>" value="<?=  $row["ref_no"] ?>"/>
									<input type="hidden" id="id_<?= $count ?>" value="<?=  $row["service_code"] ?>"/>
									<strong style="color:#006000"><?= $row["service_code"] ?></strong>
								</td>
								<td class="jedPanel3">
									<input type="hidden" id="name_<?= $count ?>" value="<?=  $row["artikelname"] ?>"/>
									<input type="hidden" id="generic_<?= $count ?>" value="<?=  $row["generic"] ?>"/>
									<span><?= $row["artikelname"] ?></span><br />
<?php
	if ($row["generic"]) {
?>
									<span style="color:#226;font:normal 11px Tahoma; margin-top:-3px">(<?= $row["generic"] ?>)</span>
<?php
	}
	elseif ($row['prod_class']=='S') {
?>
									<span style="color:#226;font:normal 11px Tahoma; margin-top:-3px">(Supplies)</span>
<?php
	}
?>
								</td>
								<td class="jedPanel3" align="center">
									<input type="hidden" id="qty_<?= $count ?>" value="<?=  $row["qty"] ?>"/>
									<span style="font:bold 14px Arial"><?= (int)$row["qty"] ?></span>
								</td>
								<td class="jedPanel3" align="center">
									<input type="hidden" id="prev_<?= $count ?>" value="<?= (int)$row["previous_returns"] ?>"/>
									<span style="color:#006000;font:bold 14px Arial"><?= (int)$row["previous_returns"] ?></span>
								</td>
								<td class="jedPanel3" align="right">
									<input type="hidden" id="price_<?= $count ?>" value="<?=  $row["price"] ?>"/>
									<strong style="color:#000080;"><?= number_format($row["price"],2) ?></strong>
								</td>
								<td class="jedPanel3" align="center">
									
<?php
	$sColor = array('N'=>"#f00000",'S'=>"#0000f0");
	$status = $row['serve_status'];
	echo '									<span style="font:bold 14px Arial;color:'.$sColor[$status].'">'.$status.'</span>';
?>								</td>
								<td class="jedPanel3" align="center">
<?php
	if ($status == "S") {
		$find = (strpos($_COOKIE["__ret_ck"],"<".$row["ref_no"]."_".$row["service_code"].">") !== FALSE);
	if (!$find) {
		$find = ((float)$row['previous_returns'] >= (float)$row['qty']);		
	}
	$returned = ((float)$row['previous_returns'] >= (float)$row['qty']);
?>
									<!-- <img id="add_<?= $count ?>" title="Add Item" class="segSimulatedLink" src="<?= $root_path ?>/images/panel_down.gif" border="0" align="absmiddle" onclick="prepareAdd('<?= $count ?>')" style="margin:0;<?= ($find === FALSE ? '' : 'display:none') ?>"> -->
									<input id="add_<?= $count ?>" type="button" class="jedButton" value="Add item" onclick="prepareAdd('<?= $count ?>')" style="margin:0;<?= ($find === FALSE ? '' : 'display:none') ?>"/>
									<span id="added_<?= $count ?>" style="font:bold 11px Tahoma;color:#006000;<?= ($find === FALSE ? 'display:none' : '') ?>"><?= $returned ? '<span style="color:#006060">Returned</span>' : 'Added' ?></span>
<?php
	}
	else {
?>
									<input id="add_<?= $count ?>" type="button" class="jedButton" value="Add item" disabled="disabled"/>
									<!-- <img title="This item has not been served." src="<?= $root_path ?>/images/panel_down_grey.gif" border="0" align="absmiddle" style="opacity:.7"> -->
<?php
	}
?>
								</td>
							</tr>
<?php
			$count++;
		}
		if (!$count) {
?>
							<tr>
								<td class="jedPanel3" align="center" colspan="10"></td>
							</tr>
								
<?php
		}
	}
	else {
?>
							<tr>
								<td class="jedPanel3" align="center" colspan="10"><?= $rc->sql ?></td>
							</tr>
								
<?php
	}
?>
						</tbody>
					</table>
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
