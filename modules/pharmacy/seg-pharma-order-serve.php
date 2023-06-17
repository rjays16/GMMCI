<?php
# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_order.php");
include_once($root_path.'include/care_api_classes/curl/class_curl.php');
include_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');

$curl_obj = new Rest_Curl;
$inv_obj = new SKUInventory;
$order_obj = new SegOrder("pharma");
global $db;

if (!isset($_GET["ref"])) {
	die("Invalid item reference.");
	exit;
}
$Ref = $_GET["ref"];
if ($_REQUEST["viewonly"]) $view_only = 1;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

//$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('QuickMenu',FALSE);
$smarty->assign('bHideCopyright',TRUE);

# Title in the title bar
$smarty->assign('sToolbarTitle',"Pharmacy::Serve request");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Pharmacy::Serve request");

if (isset($_POST["submitted"])) {

	$db->StartTrans();
	$itemArray = array();
	$statusArray = array();
	$remarksArray = array();
	foreach ($_POST['items'] as $i=>$v) {
		if ($_POST['status'][$i] != $_POST['srv'][$i]) {
			$itemArray[] = $v;
			$statusArray[] = $_POST['status'][$i];
			$remarksArray[] = $_POST['remarks'][$i];
			$usedStatusArray[] = $_POST['used_status'][$i];
			$usedQtyArray[] = $_POST['unused_qty'][$i];

			$prc = $iscash ? $cash : $charge;

			$inv[] = array(
				'item'=>$v,
				'prev'=>$_POST['srv'][$i],
				'now'=>$_POST['status'][$i],
				'quantity'=>$_POST['qty'][$i],
				'price'=>$prc,
				'date'=>$_POST['dt'][$i],
				'update'=>$_POST['upd'][$i]);
		}
	}
	
	$check = $order_obj->checkIfCashTrans($Ref);

	if(!empty($itemArray))
		$saveok = $order_obj->changeServeStatus($Ref, $itemArray, $statusArray, $remarksArray);

	else {
		$order_obj->setErrorMsg('No Items Updated');
	}

	$area = $order_obj->getTransactionArea($Ref);

	if ($saveok) {
		require_once($root_path . 'include/care_api_classes/inventory/class_inventory_helper.php');
		$invHelper = new InventoryHelper();
		foreach ($inv as $i => $v) {
			if ($v['now'] == 'S') {
				$invHelper->removeStock($v['item'], $v['quantity'], $area, $Ref, SALE);
			} else if ($v['now'] == 'N') {
				$invHelper->addStock($v['item'], $area, $v['quantity'], '', $Ref, $Ref, CANCEL);
			}
		}

		$smarty->assign('sysInfoMessage','Serve details updated...');
		if($check != '1'){
			// $curl_obj->inpatientPharmaItem($Ref);
		}# updated by janjan 08/19/2015
		else{
			$curl_obj->walkinPharmaServe($Ref);
		}
		$db->CompleteTrans();
	}
	else {
		$errorMsg = $order_obj->error_msg;
		if (!$errorMsg)
			$errorMsg = 'Unknow error encountered...';

		$smarty->assign('sysErrorMessage',$errorMsg);
		$db->FailTrans();
		$db->CompleteTrans();
	}
}

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs',$onLoadJS);

# Collect javascript code

ob_start();
# Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/event.simulate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/order-gui.js?t=<?=time()?>"></script>

<script type="text/javascript">

var trayItems = 0;
var formName = "";

function validate() {
	var iscash = $("iscash1").checked;
	if (!$('refno').value) {
		alert("Please enter the reference no.");
		$('refno').focus();
		return false;
	}
	if (iscash) {
		if (!$("ordername").value && !$("pid").value) {
			alert("Please enter the payor name or select a registered person using the person search function...");
			$('ordername').focus();
			return false
		}
	}
	else {
		if (!$("pid").value) {
			alert("Please select a registered person using the person search function...");
			return false;
		}
	}
	return true;
}

//added by cha, 11-22-2010
function setServeStatus() {
	var status = document.getElementsByName('status[]');
	for(i=0;i<status.length;i++)
	{
		if($('serve_all').checked)
			status[i].value = "S";
		else
			status[i].value = "N";
	}
}


function setUnusedQty(id,allow_qty,val) {
	var qty;
	if (val=='1'){
		while (qty) {
		}
		while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0){
			qty = prompt("Enter quantity:")
			if (qty === null) {
				$('unused_sel_'+id).selectedIndex = 0;
				return false;
			}
			if (qty > allow_qty){
				$('unused_sel_'+id).selectedIndex = 0;
				alert("Quantiy must not greater than the actual quantity");
				return false;
			} 
		}
		$('unused_qty_'+id).value = qty;
	}else{
		$('unused_qty_'+id).value = 0;
	}

}


function keyF9() {
	try {
		if (validate()) document.inputform.submit()
	} catch(err) {
		alert(err)
	}
}

function init() {
//update serve status
shortcut.add('F9', keyF9,
{
	'type':'keydown',
	'propagate':false,
}
);
}

document.observe('dom:loaded', init);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Render form values
# Fetch order data
$infoResult = $order_obj->getOrderInfo($Ref);
#$saved_discounts = $order_obj->getOrderDiscounts($Ref);
if ($infoResult)	$info = $infoResult->FetchRow();
$_POST = $info;
$_POST["iscash"] = $info["is_cash"];
$issc = $saved_discounts['SC'];

$submitted = true;
$readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";
$readOnlyAll = "";
$view_only = 1;
if ($view_only) {
	$readOnly = "";
	$readOnlyAll = 'readonly="readonly" disabled="disabled"';
}

require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$prod=$prod_obj->getAllPharmaAreas();
$select_area = '<select class="segInput" name="area" id="area" onchange="if (warnClear()) { emptyTray(); true;} else {return false;}" '.$readOnlyAll.'>'."\n";
while($row=$prod->FetchRow()){
	$checked=strtolower($row['area_code'])==strtolower($_POST['pharma_area']) ? 'selected="selected"' : "";
	$select_area .= "	<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
}
$smarty->assign('sSelectArea',$select_area);

$smarty->assign('sIsCash','<input class="segInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" '.$readOnlyAll.'/> <label class="segInput" for="iscash1">Cash</label>');
$smarty->assign('sIsCharge','<input class="segInput" style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" '.$readOnlyAll.'/> <label class="segInput" for="iscash0">Charge</label>');

$smarty->assign('sChargeType',
	'<select id="charge_type" name="charge_type" class="segInput" '.($_POST['iscash']==='0'?'':'style="display:none"').' disabled="disabled">
	<option value="PERSONAL" '.($_POST['charge_type']=='PERSONAL'?'selected="selected"':'').'>PERSONAL</option>
	<option value="PHIC" '.($_POST['charge_type']=='PHIC'?'selected="selected"':'').'>PHIC</option>
	<option value="LINGAP" '.($_POST['charge_type']=='LINGAP'?'selected="selected"':'').'>LINGAP</option>
	<option value="CMAP" '.($_POST['charge_type']=='CMAP'?'selected="selected"':'').'>CMAP</option>
	</select>');

$smarty->assign('sIsTPL','<input class="segInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').' '.$readOnlyAll.'/><label class="segInput" for="is_tpl" style="color:#006600">To pay later</label>');
$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');
$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="33" style="font:bold 12px Arial;" '.$readOnly.' value="'.strtoupper($_POST["ordername"]).'" '.$readOnlyAll.'/>');
$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" '.(($_POST['pid'])?'':' disabled="disabled"').' '.$readOnlyAll.'/>');
$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="30" rows="2" style="font:bold 12px Arial" '.$readOnly.' '.$readOnlyAll.'>'.$_POST["orderaddress"].'</textarea>');
$smarty->assign('sRefNo','<input class="segInput" id="refno" name="refno" type="text" size="10" value="'.($submitted ? $_POST['refno'] : $lastnr).'" style="font:bold 12px Arial" readonly="readonly"/>');
$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial" onclick="xajax_reset_referenceno()" '.$readOnlyAll.'/>');

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);
$smarty->assign('sOrderDate','<span id="show_orderdate" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="segInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="margin-left:2px;opacity:0.5">');
$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
$smarty->assign('sNormalPriority','<input class="segInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').$readOnlyAll.'/> <label class="segInput" for="p0">Normal</label>');
$smarty->assign('sUrgentPriority','<input class="segInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').$readOnlyAll.'/> <label class="segInput" for="p1">Urgent</label>');
$smarty->assign('sComments','<textarea class="segInput" name="comments" cols="16" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic" '.$readOnlyAll.'>'.$_POST['comment'].'</textarea>');

$result = $order_obj->getOrderItemsForServe($Ref);
$total = 0;
$toggle = FALSE;
$total_served = 0;
$total_items = 0;

if ($result) {
	while ($row=$result->FetchRow()) {
		$toggle=!$toggle;
//added by CHA 09-23-09

		$check=FALSE;
//$checked=$order_obj->checkOrderItemPaid($Ref,$row['bestellnum']);
		$checked = $row['serve_status']!='S';
		$checked = $info["is_cash"]=='0' || $row['request_flag'];

		if ($info["is_cash"]=='0') {
			if ($row['charge_type']=='PHIC') {
				$status='<img title="PHIC" src="'.$root_path.'images/phic_item.gif" align="absmiddle"/>';
			}
			else
				$status="<span style=\"font-family:Tahoma; font-size:11px; color:#000080\">CHARGE</span>";
		}
		else {
//			switch($row['request_flag']) {
//				case 'LINGAP':
//					$status='<img title="Lingap" src="'.$root_path.'images/lingap_item.gif" align="absmiddle"/>';
//				break;
//				case 'CMAP':
//					$status='<img title="CMAP" src="'.$root_path.'images/cmap_item.gif" align="absmiddle"/>';
//				break;
//				case 'PAID':
//					$status='<img title="Paid" src="'.$root_path.'images/paid_item.gif" align="absmiddle"/>';
//				break;
//				case 'CHARITY':
//					$status='<span style="font-family:Tahoma; font-size:11px; color:#000080">CHARITY</span>';
//				break;
//				default:
//					$status='<span style="font-family:Tahoma; font-size:11px; color:#000080">Not paid</span>';
//				break;
//			}

			if ($row['request_flag']) {
				$status='<img title="Lingap" src="'.$root_path.'images/flag_'.strtolower($row['request_flag']).'.gif" align="absmiddle"/>';
			}
			else {
				$status='<span style="font-family:Tahoma; font-size:11px; color:#000080">Not paid</span>';
			}
		}

		$rows .= "
		<input name=\"items[]\" type=\"hidden\" value=\"{$row['bestellnum']}\">
		<input name=\"unused_qty[]\" id=\"unused_qty_{$row['bestellnum']}\" type=\"hidden\" value=\"0\">
		<input name=\"qty[]\" type=\"hidden\" value=\"{$row['quantity']}\">
		<input name=\"prc[]\" type=\"hidden\" value=\"{$price}\">
		<input name=\"srv[]\" type=\"hidden\" value=\"{$row['serve_status']}\">
		<input name=\"dt[]\" type=\"hidden\" value=\"{$row['serve_date']}\">
		<input name=\"upd[]\" type=\"hidden\" value=\"{$row['last_update']}\">
		<input name=\"cash[]\" type=\"hidden\" value=\"{$row['pricecash']}\">
		<input name=\"charge[]\" type=\"hidden\" value=\"{$row['pricecharge']}\">
		<tr".($toggle?"":" class=\"alt\"").">
		<td class=\"centerAlign\" style=\"color:#800000\">{$row['bestellnum']}</td>
		<td>{$row["artikelname"]}</td>
		<td align=\"center\">{$row['quantity']}</td>
		<td align=\"right\">".number_format($row['pricecash'],2)."</td>
		<td align=\"right\">".number_format($row['quantity']*$row['pricecash'],2)."</td>
		<td align=\"center\">
		{$status}
		</td>
		<td>
		<select name=\"status[]\" class=\"segInput\" style=\"font-size:11px;\" ".($checked ? '' : 'disabled="disabled"').">
		<option value=\"N\"".($row['serve_status']=='N' ? ' selected="selected"' : '').">Not served</option>
		<option value=\"S\"".($row['serve_status']=='S' ? ' selected="selected"' : '').">Served</option>
		</select>
		</td>
		<td>
		<select name=\"used_status[]\" class=\"segInput\" id=\"unused_sel_{$row['bestellnum']}\" style=\"font-size:11px;\" onchange=\"setUnusedQty({$row['bestellnum']},{$row['quantity']},this.value);\" ".($row['pharma_area'] == 'BB' ? '' : 'disabled="disabled"').">
		<option value=\"0\"".($row['is_unused']==0 ? ' selected="selected"' : '').">Used</option>
		<option value=\"1\"".($row['is_unused']==1 ? ' selected="selected"' : '').">Not Used</option>
		</select>
		</td>
		<td style\"padding:2px\" align=\"center\">
		<input type=\"text\" name=\"remarks[]\" class=\"segInput\" value=\"".(htmlentities($row['serve_remarks']))."\" ".($checked ? '' : 'disabled="disabled"')." style=\"width:98%\"/>
		</td>
		</tr>";
		if ($row['serve_status']=='S')
			$total_served++;
		$total_items++;
		$total += $row["quantity"]*$row["pricecash"];
	}
	if (!$rows)
		$rows="<tr><td colspan=\"10\">No items found...</td></tr>";
}

$smarty->assign('sTotalPrice',number_format($total,2,'.',','));
$smarty->assign('sOrderItems',$rows);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=serve&ref='.$Ref.'&from=CLOSE_WINDOW" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
<script type="text/javascript" language="javascript">
<!--
if (window.parent) {
	if (window.parent.$('serve_<?= $Ref ?>')) {
		window.parent.$('serve_<?= $Ref ?>').innerHTML = '<?php
		if ($total_items == $total_served) echo "<span style=\"color:#00c\">SERVED</span>";
		elseif ($total_served == 0) echo "<span style=\"color:#c00\">Not served</span>";
		else echo "<span style=\"color:#606\">Partially served</span>";
		?>';
	}
}
-->
</script>

<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sSubmitImg ='update.gif';
$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,$sSubmitImg,'0','center').' alt="'.$LDBack2Menu.'" style="cursor:pointer">');
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/serve.tpl');
$smarty->display('common/mainframe.tpl');

?>