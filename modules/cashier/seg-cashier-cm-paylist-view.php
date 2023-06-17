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
 $smarty->assign('sOnLoadJs','onLoad="window.parent.$(\'d'.$_GET['row'].'\').style.height=document.body.offsetHeight-14;" style="margin:0;padding:0"');
 #$smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function prepareAdd(id) {
	var details = new Object();
	details.orno = $('or_'+id).value;
	details.src = $('src_'+id).value;
	details.ref = $('ref_'+id).value;
	details.id = $('id_'+id).value;
	details.name = $('name_'+id).value;
	details.desc = $('desc_'+id).value;
	details.previous = $('prev_'+id).value;
	details.refund = $('qty_'+id).value;
	details.qty = $('qty_'+id).value;
	details.price = $('price_'+id).value;

	result = window.parent.parent.appendItem(null,details)
	if (result) {	
		window.parent.parent.refreshTotal();
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

	<table width="100%" cellspacing="2" cellpadding="2" style="">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" ></td>
			</tr>
			<tr>
				<td>
					<table id="product-list" cellpadding="1" cellspacing="1" width="100%" style="border:1px solid #006699">
						<tbody>
							<tr>
								<td width="12%" class="jedPanelHeader" align="center">Code</td>
								<td width="5%" class="jedPanelHeader" align="center" nowrap="nowrap">Ref No.</td>
								<td width="5%" class="jedPanelHeader" align="center">Source</td>
								<td width="*" class="jedPanelHeader" nowrap="nowrap">Item</td>
								<td width="5%" class="jedPanelHeader" align="center">Qty</td>
								<td width="5%" class="jedPanelHeader" nowrap="nowrap" align="center">Prev</td>
								<td width="12%" class="jedPanelHeader" nowrap="nowrap" align="center">Price</td>
								<td width="1%" class="jedPanelHeader" nowrap="nowrap" align="center"></td>
							</tr>
<?php

	$source_array = array(
		'PH'=>'PHARMA',
		'RD'=>'RADIO',
		'LD'=>'LAB',
		'FB'=>'BILLING',
		'PP'=>'DEPOSIT',
		'OR'=>'OR',
		'OTHER'=>'OTHER',
	);
	$ORNo = $_GET['or'];
	$Nr = $_GET['nr'];	
	require($root_path.'include/care_api_classes/class_credit_memo.php');
	$cm = new SegCreditMemo();	
	$result = $cm->GetORDetailsForCM($ORNo, $Nr);
	if ($result) {
		$count = 0;
		while ($row = $result->FetchRow()) {
			$name_group = explode("\n",$row['name_group']);
			$price = (float) $row['price'];
?>
							<tr>
								<td class="jedPanel3" align="center">
									<input type="hidden" id="or_<?= $count ?>" value="<?=  $_GET['or'] ?>"/>									
									<input type="hidden" id="id_<?= $count ?>" value="<?=  $row["service_code"] ?>"/>
									<strong style="font:bold 10px Tahoma;color:#006000"><?= $row["service_code"] ?></strong>
								</td>
								<td class="jedPanel3" align="center">
									<input type="hidden" id="ref_<?= $count ?>" value="<?=  $row["ref_no"] ?>"/>
									<strong style="font:bold 11px Tahoma;"><?= $row["ref_no"] ?></strong>
								</td>
								<td class="jedPanel3" align="center">
									<input type="hidden" id="src_<?= $count ?>" value="<?=  $row["ref_source"] ?>"/>
									<strong style="font:bold 11px Tahoma;"><?= $row["ref_source"] ?></strong>
								</td>
								<td class="jedPanel3">
									<input type="hidden" id="name_<?= $count ?>" value="<?=  $name_group[0] ?>"/>
									<input type="hidden" id="desc_<?= $count ?>" value="<?=  $name_group[1] ?>"/>
									<span style="font:bold 11px Tahoma"><?= $name_group[0] ?></span><br />
<?php
	if ($name_group[1]) {
?>
									<span style="color:#226;font:normal 10px Tahoma; margin-top:-3px">(<?= $name_group[1] ?>)</span>
<?php
	}
	else {
?>
									<span style="color:#226;font:normal 11px Tahoma; margin-top:-3px"></span>
<?php
	}
?>
								</td>
								<td class="jedPanel3" align="center">
									<input type="hidden" id="qty_<?= $count ?>" value="<?=  (int)$row["quantity"] ?>"/>
									<span style="font:bold 14px Arial"><?= (int)$row["quantity"] ?></span>
								</td>
								<td class="jedPanel3" align="center">
									<input type="hidden" id="prev_<?= $count ?>" value="<?= (int)$row["refunded"] ?>"/>
									<span style="color:#006000;font:bold 14px Arial"><?= (int)$row["refunded"] ?></span>
								</td>
								<td class="jedPanel3" align="right">
									<input type="hidden" id="price_<?= $count ?>" value="<?=  $price ?>"/>
									<strong style="color:#000080;"><?= number_format($price,2) ?></strong>
								</td>
								<td class="jedPanel3" align="center">
<?php
	if ((float)$row['quantity']>0) {
		$zkey = "<".$_REQUEST['or']."_".$row["ref_source"]."_".$row["ref_no"]."_".$row["service_code"].">";
		$find = (strpos($_COOKIE["__cm_ck"],$zkey) !== FALSE);
		if (!$find) {
			//$find = ((float)$row['refunded'] >= (float)$row['quantity']);		
			$refunded = ((float)$row['refunded'] >= (float)$row['quantity']);
			$find = $refunded;
		}
		
?>
									<!-- <img id="add_<?= $count ?>" title="Add Item" class="segSimulatedLink" src="<?= $root_path ?>/images/panel_down.gif" border="0" align="absmiddle" onclick="prepareAdd('<?= $count ?>')" style="margin:0;<?= ($find === FALSE ? '' : 'display:none') ?>"> -->
									<input id="add_<?= $count ?>" type="button" class="jedButton" value="Refund" onclick="prepareAdd('<?= $count ?>')" style="margin:0;<?= ($find === FALSE ? '' : 'display:none') ?>"/>
									<input id="added_<?= $count ?>" type="button" class="jedButton" value="<?= $refunded ? 'Done' : 'Refunded' ?>" style="margin:0;<?= ($find === FALSE ? 'display:none' : '') ?>" disabled="disabled"/>
<?php
	}
	else {
?>
									<input id="add_<?= $count ?>" type="button" class="jedButton" value="Refund" disabled="disabled"/>
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
								<td class="jedPanel3" align="left" colspan="10">No items found for this payment...</td>
							</tr>
								
<?php
		}
	}
	else {
?>
							<tr>
								<td class="jedPanel3" align="center" colspan="10"><?= $cm->sql ?></td>
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
