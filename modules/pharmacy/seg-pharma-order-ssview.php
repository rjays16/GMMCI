<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

	include_once($root_path."include/care_api_classes/class_order.php");

	$order_obj = new SegOrder("pharma");
	global $db;
	
	if (!isset($_GET["ref"])) {
		die("Invalid item reference.");
		exit;
	}
	$Ref = $_GET["ref"];
	$view_only = 1;
	$ss_view = TRUE;

	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');
	
	if ($_GET["from"]=="CLOSE_WINDOW") {
	 $smarty->assign('bHideTitleBar',TRUE);
	 $smarty->assign('bHideCopyright',TRUE);
	}

	$title = "Social Service::Apply classificaition to request";
	
 # Title in the title bar
 $smarty->assign('sToolbarTitle',$title);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$title);

	if (isset($_POST["submitted"])) {
	
		$bulk = array();
		$orig = $_POST['iscash'] ? $_POST['pcash'] :  $_POST['pcharge'];
		$total = 0;
		foreach ($_POST["items"] as $i=>$v) {
			$consigned = in_array($v, $_POST['consigned']) ? '1' : '0';
			$bulk[] = array($_POST["items"][$i],$_POST["qty"][$i],
				parseFloatEx($_POST["prc"][$i]),
				parseFloatEx($_POST["prc"][$i]), 
				$consigned, $orig[$i]);
			$total += (parseFloatEx($_POST["prc"][$i]) * (float) $_POST["qty"][$i]);
		}	
		$data = array(
			'encounter_nr'=>$_POST['encounter_nr'],
			'pharma_area'=>strtoupper($_POST['area']),
			'pid'=>$_POST['pid'],
			'ordername'=>$_POST['ordername'],
			'orderaddress'=>$_POST['orderaddress'],
			'orderdate'=>$_POST['orderdate'],
			'is_cash'=>$_POST['iscash'],
			'is_tpl'=>$_POST['is_tpl'],
			'amount_due'=>$total,
			'discount'=>$_POST['discount'],
			'discountid'=>$_POST['discountid'],
			'is_urgent'=>$_POST['priority'],
			'comments'=>$_POST['comments'],
			'modify_id'=>$_SESSION['sess_temp_userid'],
			'modify_time'=>date('YmdHis')
		);
		if ($_POST['issc']) $data["is_sc"] = 1;
		else $data["is_sc"] = 0;
		if ($_POST["pid"]) $data["pid"] = $_POST["pid"];

		$order_obj->setDataArray($data);
		$order_obj->where = "refno=".$db->qstr($_GET['ref']);
		$saveok=$order_obj->updateDataFromInternalArray($_GET["ref"],FALSE);
		if ($saveok) {
			# Bulk write order items

			$order_obj->clearOrderList($Ref);
			$order_obj->addOrders($Ref, $bulk);
			
			/*
			# Bulk write discounts
			$bulk = array();
			if ($_POST['issc']) $bulk[] = 'SC';
			foreach ($_POST["discount"] as $i=>$v) {
				if ($v) $bulk[] = array($v);
			}
			$order_obj->clearDiscounts($Ref);
			if ($bulk) {
				$order_obj->addDiscounts($Ref,$bulk);
			}
			*/
			
			$sBreakImg ='close2.gif';
			$smarty->assign('sMsgTitle','Pharmacy order successfully updated!');
			$smarty->assign('sMsgBody','The order details have been saved into the database...');
			$smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
			$printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print-jasper&userck=$userck".'&cat=pharma&ref='.$Ref;
			$smarty->assign('sPrintButton','<img class="segSimulatedLink"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');
			
			
			$infoResult = $order_obj->getOrderInfo($Ref);
			if ($infoResult)	$info = $infoResult->FetchRow();
			# Assign submitted form values
			
			$smarty->assign('sRefNo', $Ref);
			$smarty->assign('sSelectArea', $info['area_name']);
			$smarty->assign('sCashCharge', 
				($info['is_cash']=="1" ? 
					("Cash".($info['is_tpl']=="1" ? " (TPL)" : "")) : 
					"Charge"));
			$smarty->assign('sOrderDate', date("F j, Y g:ia",strtotime($info['orderdate'])));
			$smarty->assign('sOrderName', $info['ordername']);
			$smarty->assign('sOrderAddress', $info['orderaddress']);
			$smarty->assign('sPriority',($info['priority']=="0") ? "Normal" : "Urgent");
			$smarty->assign('sRemarks',$info['comments']);
			
			$itemsResult = $order_obj->getOrderItemsFullInfo($Ref);
			if ($itemsResult) {
				$oRows = "";
			 	while ($oItem=$itemsResult->FetchRow()) {
					$oRows .= '<tr>
											<td class="jedPanel3" style="font:bold 11px Tahoma;color:#000080">'.$oItem['bestellnum'].'</td>
											<td class="jedPanel3">'.$oItem['artikelname'].'</td>
											<td class="jedPanel3" align="right">'.number_format((float)$oItem['force_price'],2).'</td>
											<td class="jedPanel3" align="center">'.number_format((float)$oItem['quantity']).'</td>
											<td class="jedPanel3" align="right">'.number_format((float)$oItem['quantity']*(float)$oItem['force_price'],2).'</td>
										</tr>
';
				}
				if (!$oRows) {
					$oRows = '<tr><td colspan="10" class="jedPanel3">Order list is empty...</td></tr>';
				}
			}
			if (!$oRows) {
				$oRows = '<tr><td colspan="10" class="jedPanel3">Error reading order details from database...</td></tr>';
			}
			$smarty->assign('sItems',$oRows);
			
			/*
			include_once($root_path."include/care_api_classes/class_product.php");
			$prod_obj = new Product();
			$items_array = $prod_obj->getProductName($_REQUEST['items']);
			$items = array();
			foreach ($_REQUEST['items'] as $i=>$v)
				$items[] = "<li>x". $_REQUEST['qty'][$i] . " " .$items_array[$v]. "</li>";
			$show_items = "<ul style=\"margin:0; padding-left:18px;list-style:disc\">".implode("",$items)."</ul>";
			$smarty->assign('sItems',$show_items);
			*/
			
			
			$smarty->assign('sMainBlockIncludeFile','order/saveok.tpl');
			$smarty->display('common/mainframe.tpl');
			exit;
		}
		else {
			$errorMsg = $db->ErrorMsg();
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sWarning','<strong>Error:</strong> An item with the same order number already exists in the database.');
			else {
				if ($errorMsg)
					$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
				else
					$smarty->assign('sWarning',"<strong>Cannot edit a pharmacy order already billed for this encounter!</strong>");
				#print_r($order_obj->sql);
			}
		}
	}

 # Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/order-gui.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;
	
	function init() {
<?php
	if ($view_only) {
?>
		// Edit/Submit shortcuts
		shortcut.add('F2', keyF2,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F3', keyF3,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F5', keyF5,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F12', keyF12,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
<?php
	}
?>

<?php
	if ($view_only)
		echo 'xajax_populate_order(\''.$Ref.'\',$(\'discountid\').value,1);';
	else
		echo 'xajax_populate_order(\''.$Ref.'\',$(\'discountid\').value);';
?>
		refreshDiscount();
	}
	
	function keyF2() {
		openOrderTray();
	}
	
	function keyF3() {
		if (confirm('Clear the order list?'))	emptyTray();
	}
	
	function keyF5() {
	}

	function keyF12() {
		if (validate()) document.inputform.submit()
	}

	function openOrderTray() {
		var area = $('area').value;
		var url = 'seg-order-tray.php?area='+area;
		overlib(
			OLiframeContent(url, 660, 360, 'fOrderTray', 0, 'no'),
			WIDTH,600, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
			CAPTIONPADDING,2, 
			CAPTION,'Add product from Order tray',
			MIDX,0, MIDY,0, 
			STATUS,'Add product from Order tray');
		return false
	}
	
	function validate() {
		var iscash = $("iscash1").checked;
		if (!$('refno').value) {
			alert("Please enter the reference no.");
			$('refno').focus();
			return false;
		}
		if (iscash) {
			if (!$("ordername").value && !$("pid").value) {
				alert("Please enter the payer's name or select a registered person using the person search function...");
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
		if (document.getElementsByName('items[]').length==0) {
			alert("Item list is empty...");
			return false;
		}
		return confirm('Process this pharmacy order?');
	}
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages
$lastnr = $order_obj->getLastNr(date("Y-m-d"));

# Render form values
$smarty->assign('ssView',$ss_view);

if ($_REQUEST['encounterset']) {
	$person = $order_obj->getPersonInfoFromEncounter($_REQUEST['encounterset']);
}

/*
if (isset($_POST["submitted"]) && !$saveok) {
	
	#* Submitted form, but error occured
	$readOnly = (!$_POST['iscash'] || $_POST['pid']) ? 'readonly="readonly"' : "";

	require_once($root_path.'include/care_api_classes/class_product.php');
	$prod_obj=new Product;
	$prod=$prod_obj->getAllPharmaAreas();
	$select_area = '<select class="jedInput" name="area" id="area" onchange="if (warnClear()) { emptyTray(); true;} else {return false;}">'."\n";
	while($row=$prod->FetchRow()){
		$checked=strtolower($row['area_code'])==strtolower($_POST['area']) ? 'selected="selected"' : "";
		$select_area .= "	<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
	}
	$smarty->assign('sSelectArea',$select_area);

	$smarty->assign('sRefNo','<input id="refno" name="refno" type="text" size="8" value="'.$_POST['refno'].'" style="font:bold 12px Arial" readonly="readonly"/>');
	$count=0;

	if ($_REQUEST['billing']) {
		$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" onclick="return false" /><label class="jedInput" for="iscash1">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" checked="checked" onclick="return false" /><label class="jedInput" for="iscash0">Charge</label>');
		$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" disabled="disabled" /><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
	}
	else {
		$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label class="jedInput" for="iscash1">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label class="jedInput" for="iscash0">Charge</label>');
		$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
	}
	
	$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
	$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
	$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');
	$smarty->assign('sOrderName','<input class="jedInput" id="ordername" name="ordername" type="text" size="30" value="'.$_POST['ordername'].'" style="font:bold 12px Arial; float:left;" '.$readOnly.'/>');
	$smarty->assign('sOrderAddress','<textarea class="jedInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="font:bold 12px Arial" '.$readOnly.'>'.$_POST['orderaddress'].'</textarea>');
	$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()"'.(($_POST['pid'])?'':' disabled="disabled"').' />');
	$smarty->assign('sSelectEnc','<input class="jedInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" onclick="alert(\'Hello\')" style="margin-left:2px"/>');
	$smarty->assign('sResetRefNo','<input class="jedInput" type="button" value="Reset" style="font:bold 11px Arial" disabled="disabled" onclick="xajax_reset_referenceno()"/>');
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/><label class="jedInput" for="priority0">Normal</label>');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="priority1">Urgent</label>');
	$smarty->assign('sComments','<textarea class="jedInput" name="comments" cols="16" rows="2" style="float:left; margin-left:5px;"></textarea>');
	
	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$dOrderDate = strtotime($_POST['orderdate']);
	$curDate = date($dbtime_format,$dOrderDate);
	$curDate_show = date($fulltime_format,$dOrderDate);
	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:2px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	if ($view_only) {
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="margin-left:2px;opacity:0.5">');
	}
	else {
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
		$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			displayArea : \"show_orderdate\",
			inputField : \"orderdate\",
			ifFormat : \"%Y-%m-%d %H:%M\", 
			daFormat : \"	%B %e, %Y %I:%M%P\", 
			showsTime : true, 
			button : \"orderdate_trigger\", 
			singleClick : true,
			step : 1
		});
	</script>";
		$smarty->assign('jsCalendarSetup', $jsCalScript);	
	}
	
	$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));

	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Order list is currently empty...</td>
				</tr>");
	if ($src) $smarty->assign('sOrderItems',$src);
}
else {

*/

	/* No submitted data */
	$smarty->append('JavaScript',$sTemp);

	# Fetch order data
	$infoResult = $order_obj->getOrderInfo($Ref);
	
	//$saved_discounts = $order_obj->getOrderDiscounts($Ref);
	if ($infoResult)	$info = $infoResult->FetchRow();
	if ($info['encounter_nr'])
		$encType = $db->GetOne("SELECT encounter_type1 FROM care_encounter WHERE encounter_nr=".$db->qstr($info['encounter_nr']));
	
	$_POST = $info;
	$_POST['encounter_type'] = $encType;
	$_POST["iscash"] = $info["is_cash"];
	$issc = ($info['is_sc'] == '1');
	
	if ($person) {
		$_POST['pid'] = $person['pid'];
		$_POST['encounter_nr'] = $person['encounter_nr'];
		$_POST['ordername'] = $person['name_first']." ".$person['name_last'];
		
		$addr = implode(", ",array_filter(array($person['street_name'], $person["brgy_name"], $person["mun_name"])));
		if ($person["zipcode"])
			$addr.=" ".$person["zipcode"];
		if ($person["prov_name"])
			$addr.=" ".$person["prov_name"];
		$_POST['orderaddress'] = $addr;
		$_POST['discount_id'] = $person['discount_id'];
		$_POST['discount'] = $person['discount'];
	}
	
	$submitted = true;
	$readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";
	$readOnlyAll = "";
	if ($view_only) {
		$readOnly = "";
		$readOnlyAll = 'readonly="readonly" disabled="disabled"';
	}

	require_once($root_path.'include/care_api_classes/class_product.php');
	$prod_obj=new Product;
	$prod=$prod_obj->getAllPharmaAreas();
	
	$index = 0;
	$count = 0;
	$select_area = '';
	while($row=$prod->FetchRow()){
		$checked=strtolower($row['area_code'])==strtolower($_POST['pharma_area']) ? 'selected="selected"' : "";
		$select_area .= "	<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
		if ($checked) $index = $count;
		$count++;
	}
	$select_area = '<select class="jedInput" name="area" id="area"'.$disabled.' onchange="if (warnClear()) { emptyTray(); this.setAttribute(\'previousValue\',this.selectedIndex);} else this.selectedIndex=this.getAttribute(\'previousValue\');" previousValue="'.$index.'" '.$readOnlyAll.'>'."\n".$select_area."</select>\n";
	$smarty->assign('sSelectArea',$select_area);
	
	if ($_REQUEST['billing']) {
		$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" onclick="return false" disabled="disabled" /><label class="jedInput" for="iscash1">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" checked="checked" onclick="return false" /><label class="jedInput" for="iscash0">Charge</label>');
		$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" disabled="disabled" /><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
	}
	else {
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" '.$readOnlyAll.'/><label class="jedInput" for="iscash1">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" '.$readOnlyAll.'/><label class="jedInput" for="iscash0">Charge</label>');
		$smarty->assign('sIsTPL','<input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').' '.$readOnlyAll.'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label>');
	}
	
	if ($person) {
		$smarty->assign('sOrderName','<input class="jedInput" id="ordername" name="ordername" type="text" size="30" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["ordername"].'"/>');
		$smarty->assign('sOrderAddress','<textarea class="jedInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="font:bold 12px Arial" readonly="readonly" >'.$_POST["orderaddress"].'</textarea>');
		$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" disabled="disabled" />');
	}
	else {
		$smarty->assign('sOrderName','<input class="jedInput" id="ordername" name="ordername" type="text" size="30" style="font:bold 12px Arial;" '.$readOnly.' value="'.$_POST["ordername"].'" '.$readOnlyAll.'/>');
		$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled="disabled" />');
		$smarty->assign('sOrderAddress','<textarea class="jedInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="font:bold 12px Arial" '.$readOnly.' '.$readOnlyAll.'>'.$_POST["orderaddress"].'</textarea>');
	}

	$smarty->assign('sOrderEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
	$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');
	if ($_POST['encounter_type'])	$smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
	else $smarty->assign('sOrderEncTypeShow', 'WALK-IN');


	$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');	
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
	$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
	$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');

	$smarty->assign('sRefNo','<input class="jedInput" id="refno" name="refno" type="text" size="10" value="'.($submitted ? $_POST['refno'] : $lastnr).'" style="font:bold 12px Arial" readonly="readonly"/>');
	$smarty->assign('sResetRefNo','<input class="jedInput" type="button" value="Reset" style="font:bold 11px Arial" disabled="disabled" onclick="xajax_reset_referenceno()" '.$readOnlyAll.'/>');

	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	if ($_REQUEST['dateset']) {
		$curDate = date($dbtime_format,$_REQUEST['dateset']);
		$curDate_show = date($fulltime_format, $_REQUEST['dateset']);
	}
	else {
		#$curDate = date($dbtime_format);
		#$curDate_show = date($fulltime_format);
		$dOrderDate = strtotime($_POST['orderdate']);
		$curDate = date($dbtime_format,$dOrderDate);
		$curDate_show = date($fulltime_format,$dOrderDate);
	}		
	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');

	if ($view_only || $_REQUEST['billing']) {
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
	}
	else {
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
		$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			displayArea : \"show_orderdate\",
			inputField : \"orderdate\",
			ifFormat : \"%Y-%m-%d %H:%M\", 
			daFormat : \"	%B %e, %Y %I:%M%P\", 
			showsTime : true, 
			button : \"orderdate_trigger\", 
			singleClick : true,
			step : 1
		});
	</script>";
		$smarty->assign('jsCalendarSetup', $jsCalScript);	
	}
	
$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
$smarty->assign('sNormalPriority','<input class="jedInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').$readOnlyAll.'/><label class="jedInput" for="p0">Normal</label>');
$smarty->assign('sUrgentPriority','<input class="jedInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').$readOnlyAll.'/><label class="jedInput" for="p1">Urgent</label>');
$smarty->assign('sComments','<textarea class="jedInput" name="comments" cols="16" rows="2" style="float:left; margin-top:3px;margin-left:5px;" '.$readOnlyAll.'>'.$_POST['comments'].'</textarea>');
//}

$smarty->assign('sRootPath',$root_path);
#if ($view_only || $_REQUEST['billing']) 
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="opacity:0.2">');
/*
else
	$smarty->assign('sSelectEnc','<input class="jedInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
       onclick="if (warnClear()) { emptyTray(); overlib(
        OLiframeContent(\'seg-order-select-enc.php\', 700, 400, \'fSelEnc\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
        CAPTIONPADDING,2, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); } return false;"
       onmouseout="nd();" />');
*/

$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.($issc?'checked="checked" ':'').' onclick="seniorCitizen()" '.$readOnlyAll.'><label for="issc" class="jedInput">Senior citizen</label>');
#$smarty->assign('sBtnPDF','<a href="#"><img src="'.$root_path.'images/btn_printpdf.gif" border="0"></a>');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=ssview&ref='.$Ref.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');
$smarty->assign('bShowQuickKeys',$view_only);

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
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
	
	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">	
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">
	<input type="hidden" name="view_from" value="<?= $_REQUEST['view_from'] ?>" />
<?php if (isset($_REQUEST['viewonly'])) { ?>	<input type="hidden" name="viewonly" value="<?= $_REQUEST['viewonly'] ?>" /><?php } ?>
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder" align="center">');
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/form.tpl');
$smarty->display('common/mainframe.tpl');

?>