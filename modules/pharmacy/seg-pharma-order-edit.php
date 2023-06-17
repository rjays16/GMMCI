<?php
# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

include_once($root_path."include/care_api_classes/class_order.php");

$order_obj = new SegOrder("pharma");

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;
    
require_once($root_path.'include/care_api_classes/class_gui_person_show.php');
$per_obj=new GuiPersonShow;

global $db;

if (!isset($_GET["ref"])) {
	die("Invalid item reference.");
	exit;
}
$Ref = $_GET["ref"];
if ($_REQUEST["viewonly"]) $view_only = 1;
if ($_REQUEST["view_from"]=='ssview') {
	$ss_view = TRUE;
}

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}

# Title in the title bar
$smarty->assign('sToolbarTitle',"Pharmacy::Edit request");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Pharmacy::Edit request");

if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {

	$db->StartTrans();

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
//			'encounter_nr'=>$_POST['encounter_nr'],
//			'pharma_area'=>strtoupper($_POST['area']),
//			'pid'=>$_POST['pid'],
//			'ordername'=>$_POST['ordername'],
//			'orderaddress'=>$_POST['orderaddress'],
//			'orderdate'=>$_POST['orderdate'],
//			'is_cash'=>$_POST['iscash'],
//			'is_tpl'=>$_POST['is_tpl'],
		'amount_due'=>$total,
		'discount'=>$_POST['discount'],
		'discountid'=>$_POST['discountid'],
		'is_urgent'=>$_POST['priority'],
		'comments'=>$_POST['comments'],
		'modify_id'=>$_SESSION['sess_temp_userid'],
		'modify_time'=>date('YmdHis')
	);
	if ($_POST['issc'])
		$data["is_sc"] = 1;
	else
		$data['is_sc'] = 0;

//		if (substr($_POST["pid"],0,1)=='W') {
//      $data["walkin_pid"] = substr($_POST["pid"],1,strlen($_POST["pid"]));
//      $data["pid"] = NULL;
//    }
//    else {
//      $data["pid"] = $_POST["pid"];
//      $data["walkin_pid"] = NULL;
//    }

	$order_obj->setDataArray($data);
	$order_obj->where = "refno=".$db->qstr($_GET['ref']);

	$errorMsg = 'Unable to save request data...';
	$saveok=$order_obj->updateDataFromInternalArray($_GET["ref"],FALSE);

		# Bulk write order items
	if ($saveok) {
		$errorMsg = 'Unable to clear request details...';
		$saveok = $order_obj->clearOrderList($Ref);
	}

	if ($saveok) {
		$errorMsg = 'Unable to update request details...';
		$order_obj->addOrders($Ref, $bulk);
	}

	$sBreakImg ='close2.gif';
	$smarty->assign('sMsgTitle','Pharmacy request successfully updated!');
	$smarty->assign('sMsgBody','The request details have been saved into the database...');
	$smarty->assign('sBreakButton','<img class="link" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	// $printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print&userck=$userck".'&cat=pharma&ref='.$Ref;
	$printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print-jasper&userck=$userck".'&cat=pharma&ref='.$Ref;
		$smarty->assign('sPrintButton','<img class="link"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');


	if ($saveok) {
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
		$smarty->assign('sPriority',($info['priority']=="0") ? "Routine" : "Stat");
		$smarty->assign('sRemarks',$info['comments']);

		$itemsResult = $order_obj->getOrderItemsFullInfo($Ref);
		if ($itemsResult) {
			$oRows = "";
			while ($oItem=$itemsResult->FetchRow()) {
				$oRows .= '<tr>
										<td align="center" style="font:bold 11px Tahoma;color:#000080">'.$oItem['bestellnum'].'</td>
										<td>'.$oItem['artikelname'].'</td>
										<td align="right">'.number_format((float)$oItem['force_price'],2).'</td>
										<td align="center">'.number_format((float)$oItem['quantity']).'</td>
										<td align="right">'.number_format((float)$oItem['quantity']*(float)$oItem['force_price'],2).'</td>
									</tr>
';
			}
			if (!$oRows) {
				$oRows = '<tr><td colspan="10" class="segPanel3">Order list is empty...</td></tr>';
			}
		}
		if (!$oRows) {
			$oRows = '<tr><td colspan="10" class="segPanel3">Error reading order details from database...</td></tr>';
		}
		$smarty->assign('sItems',$oRows);
		$smarty->assign('sMainBlockIncludeFile','order/saveok.tpl');
		$smarty->display('common/mainframe.tpl');
		$db->CompleteTrans();
		exit;
	}
	else {
		$db->FailTrans();
		$db->CompleteTrans();
		if (!$errorMsg) {
			$errorMsg = $db->ErrorMsg();
		}
		if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
			$smarty->assign('sysErrorMessage','An item with the same order number already exists in the database.');
		else
			$smarty->assign('sysErrorMessage',"$errorMsg");
	}
}

 # Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
$smarty->assign('sOnLoadJs',$onLoadJS);

$lastnr = $order_obj->getLastNr(date("Y-m-d"));
if ($_REQUEST['encounterset']) {
	$person = $order_obj->getPersonInfoFromEncounter($_REQUEST['encounterset']);
}
$infoResult = $order_obj->getOrderInfo($Ref);

//$saved_discounts = $order_obj->getOrderDiscounts($Ref);
if ($infoResult)  $info = $infoResult->FetchRow();
if ($info['encounter_nr'])
	$encType = $db->GetOne("SELECT encounter_type FROM care_encounter WHERE encounter_nr=".$db->qstr($info['encounter_nr']));

$_POST = $info;
$_POST['encounter_type'] = $encType;
$_POST["iscash"] = $info["is_cash"];
$issc = ($info['is_sc'] == '1');

#added by VAN 01-29-2013
#get encounter info
$billinfo = $enc_obj->hasSavedBilling($info['encounter_nr']);
if ($billinfo){
    $bill_nr = $billinfo['bill_nr'];
    $hasfinal_bill = $billinfo['is_final'];
    
    if ($info['encounter_nr'])
        $is_maygohome = $db->GetOne("SELECT is_maygohome FROM care_encounter WHERE encounter_nr=".$db->qstr($info['encounter_nr']));
    
    $is_maygohome = $is_maygohome;
}

$warningCaption = '';
/*if (($bill_nr)||($is_maygohome)){
   if (($bill_nr)&&($is_maygohome)) 
        $warningCaption = "This patient has a saved billing and already advised to go home...";
   elseif (($bill_nr)&&!($is_maygohome)) 
        $warningCaption = "This patient has a saved billing...";     
   elseif (!($bill_nr)&&($is_maygohome)) 
        $warningCaption = "This patient is already advised to go home...";
        
   $view_only = true;
   $viewonly = true;
   $_REQUEST['viewonly'] = true;          
}*/

if (($bill_nr)&&($is_maygohome)){
   $warningCaption = "This patient has a saved billing and already advised to go home...";
        
   $view_only = true;
   $viewonly = true;
   $_REQUEST['viewonly'] = true;          
}

$smarty->assign("sWarning","<em><font color='RED'><strong>&nbsp;<span id='warningcaption'>".$warningCaption."</span></strong></font></em>"); 


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
		//xajax.call('', )
        
<?php
	if (!$_REQUEST['viewonly']) {
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
		updateCoverage([<?= $_POST['encounter_nr'] ?>]);
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

	function openCoverages() {
		var enc_nr = $('encounter_nr').value;
		if (enc_nr) {
			var url = '../../modules/insurance_co/seg_coverage_editor.php?userck=<?= $_GET['userck'] ?>&encounter_nr='+enc_nr+'&from=CLOSE_WINDOW&force=1';
			overlib(
				OLiframeContent(url, 740, 400, 'fCoverages', 0, 'auto'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
				CAPTIONPADDING,2,
				CAPTION,'Insurance coverages',
				MIDX,0, MIDY,0,
				STATUS,'Insurance coverages');
		}
		else {
			alert('No patient with confinement case selected...');
		}
		return false
	}

	function openOrderTray() {
		var discount = $('discountid').value;
		var area = $('area').value;
		var url = 'seg-order-tray.php?area='+area+'&d='+discount;
		overlib(
			OLiframeContent(url, 660, 360, 'fOrderTray', 0, 'no'),
			WIDTH,600, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Add pharmacy item from Order tray',
			MIDX,0, MIDY,0,
			STATUS,'Add product from Order tray');
		return false;
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
		return confirm('Process this pharmacy request?');
	}
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Render form values
$smarty->assign('ssView',$ss_view);

/* No submitted data */
$smarty->append('JavaScript',$sTemp);

# Fetch order data

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
$select_area = '<select class="segInput" name="area" id="area" disabled="disabled" onchange="if (warnClear()) { emptyTray(); this.setAttribute(\'previousValue\',this.selectedIndex);} else this.selectedIndex=this.getAttribute(\'previousValue\');" previousValue="'.$index.'" '.$readOnlyAll.'>'."\n".$select_area."</select>\n";
$smarty->assign('sSelectArea',$select_area);

if ($_REQUEST['billing']) {
	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" onclick="return false" disabled="disabled" /><label class="segInput" for="iscash1"  style="font:bold 12px Arial; color:#3e7bc6">Cash</label>');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" checked="checked" onclick="return false" /><label class="segInput" for="iscash0" style="font:bold 12px Arial; color:#c64c3e">Charge</label>');
	$smarty->assign('sIsTPL','<input class="segInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" disabled="disabled" /><label class="segInput" for="is_tpl" style="color:#006600">To pay later</label>');
}
else {
	$smarty->assign('sIsCash','<input class="segInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" disabled="disabled" /><label class="segInput" for="iscash1" style="font:bold 12px Arial; color:#3e7bc6">Cash</label>');
	$smarty->assign('sIsCharge','<input class="segInput" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" disabled="disabled" /><label class="segInput" for="iscash0" style="font:bold 12px Arial; color:#c64c3e">Charge</label>');
	$smarty->assign('sIsTPL','<input class="segInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').' '.$readOnlyAll.'/><label class="segInput" for="is_tpl" style="color:#006600">To pay later</label>');
}

$smarty->assign('sChargeType',
'<select id="charge_type" name="charge_type" class="segInput" '.($_POST['iscash']==='0'?'':'style="display:none"').' disabled="disabled">
	<option value="PERSONAL" '.($_POST['charge_type']=='PERSONAL'?'selected="selected"':'').'>TPL</option>
	<option value="PHIC" '.($_POST['charge_type']=='PHIC'?'selected="selected"':'').'>PHIC</option>
</select>');


if ($person) {
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="" readonly="readonly" value="'.$_POST["ordername"].'"/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="" readonly="readonly" >'.$_POST["orderaddress"].'</textarea>');
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" disabled="disabled" />');
}
else {
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="" readonly="readonly" value="'.$_POST["ordername"].'" '.$readOnlyAll.'/>');
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" onclick="clearEncounter()" disabled="disabled" />');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="" readonly="readonly">'.$_POST["orderaddress"].'</textarea>');
}


$smarty->assign('sOrderEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');
if ($_POST['encounter_type'])	$smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
	if ($person['encounter_type'])
		$smarty->assign('sOrderEncTypeShow',$enc[$person['encounter_type']]);
	else	$smarty->assign('sOrderEncTypeShow', 'WALK-IN');
}


#added by dommie


$encNr = $_POST['encounter_nr'];
if(!$encNr)
{
    $encNr = $per_obj->CurrentEncounter($_POST["pid"]);
}
if($encNr)
{
	$admDateTmp = $enc_obj->AdmissionDate($encNr);
	$admDate = date('m/d/Y h:i A', strtotime($admDateTmp));
}
else
{
	$admDate = '';
}

$smarty->assign('sAdmDate',$admDate);

$phic_nr = $db->GetOne("SELECT fn_get_phic_number('".$_POST["encounter_nr"]."') AS `phic_nr`");
$smarty->assign('sPhicNo', $phic_nr);

    if($_POST["encounter_nr"]){
        $sql_mc = "SELECT cif.`firm_id` FROM seg_encounter_insurance AS sei 
					INNER JOIN care_insurance_firm AS cif ON cif.`hcare_id` = sei.`hcare_id` 
					WHERE cif.hcare_id != 18 AND sei.encounter_nr =".$db->qstr($_POST["encounter_nr"]);
        $category = $db->GetOne($sql_mc);
        if($category){                        
                        $CategoryUi = $category;
                    }else{
                        $CategoryUi = 'None';    
                    }
    } else {
                    
                }
     
$smarty->assign('sMemCategory', $CategoryUi);
#end pol

	if($_POST["encounter_nr"]){
			        $sql_mc = "SELECT SUM(seg_pharma_order_items.pricecharge*(seg_pharma_order_items.quantity)) AS TotalCharges
									FROM seg_pharma_orders 
									LEFT JOIN seg_pharma_order_items 
									ON  seg_pharma_orders.refno= seg_pharma_order_items.refno									
									WHERE seg_pharma_order_items.serve_status ='S' AND is_cash=0 AND seg_pharma_orders.encounter_nr=".$db->qstr($_POST["encounter_nr"]);
			        $rs_totalCharges = $db->Execute($sql_mc);
			        $row_totalCharges = $rs_totalCharges->FetchRow();

			        $sql_mc ="SELECT SQL_CALC_FOUND_ROWS  
								(SELECT IFNULL(SUM(IFNULL(r.quantity,0)),0) FROM seg_pharma_return_items r 
								WHERE r.ref_no=oi.refno AND r.bestellnum=oi.bestellnum) *
								oi.pricecharge AS TotalReturnCharge
								FROM seg_pharma_order_items oi 
								INNER JOIN seg_pharma_orders o ON o.refno=oi.refno 
								INNER JOIN care_person cp ON cp.pid=o.pid 
								INNER JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum 
								WHERE (oi.serve_status='S') AND (o.is_cash=0) 
								AND (o.encounter_nr=".$db->qstr($_POST["encounter_nr"]).") 
								GROUP BY oi.bestellnum";
					$rs_ReturnCharges = $db->Execute($sql_mc);
					while ($res_ReturnCharges=$rs_ReturnCharges->FetchRow()) {
							$row_TotalReturnCharges=$row_TotalReturnCharges+$res_ReturnCharges['TotalReturnCharge'];
						}

					$TotalCharges=$row_totalCharges['TotalCharges']-$row_TotalReturnCharges;
			        $totalChargesUi = $TotalCharges;
			    }
    
$smarty->assign('sTotalCharges', $totalChargesUi);//Added by: Dommie

$smarty->assign('sOrderEncNr','<input type="hidden" id="encounter_nr" name="encounter_nr" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');

$smarty->assign('sRefNo','<input class="segInput" id="refno" name="refno" type="text" size="10" value="'.($submitted ? $_POST['refno'] : $lastnr).'" style="" readonly="readonly"/>');
$smarty->assign('sResetRefNo','<input class="segButton" type="button" value="Reset" disabled="disabled" onclick="xajax_reset_referenceno()" '.$readOnlyAll.'/>');

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
$smarty->assign('sOrderDate','<span id="show_orderdate" class="segInput" style="color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="segInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="">');

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
$smarty->assign('sNormalPriority','<input class="segInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').$readOnlyAll.'/><label class="segInput" for="p0">Routine</label>');
$smarty->assign('sUrgentPriority','<input class="segInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').$readOnlyAll.'/><label class="segInput" for="p1">Stat</label>');
$smarty->assign('sComments','<textarea class="segInput" name="comments" cols="16" rows="2" style="float:left; margin-top:3px;margin-left:5px;" '.$readOnlyAll.'>'.$_POST['comments'].'</textarea>');
//}

$smarty->assign('sRootPath',$root_path);
#if ($view_only || $_REQUEST['billing'])
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="opacity:0.2">');
/*
else
	$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
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

if ($view_only) {
	$smarty->assign('sBtnAddItem','<img id="select-enc" class="disabled" src="'.$root_path.'images/btn_additems.gif" border="0" align="absmiddle" />');
	$smarty->assign('sBtnEmptyList','<img id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" class="disabled" border="0" align="absmiddle" />');
	$smarty->assign('sBtnCoverage','<img id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" class="disabled" border="0" align="absmiddle" />');
	$printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print-jasper&userck=$userck".'&cat=pharma&ref='.$Ref;
	$smarty->assign('sPrintButton','<img class="link"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="center" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');
}
else {
	$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" align="absmiddle" onclick="return openOrderTray();">');
	$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" align="absmiddle" onclick="if (confirm(\'Clear the order list?\')) emptyTray()"/>');
	$smarty->assign('sBtnCoverage','<img class="segSimulatedLink" id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" border="0" align="absmiddle" onclick="return openCoverages();"/>');
}

$smarty->assign('sDiscountShow','<input type="checkbox" '.($isDisableSC && $info['is_cash'] == 0 ? 'disabled' : '').' is_nurse='.($isDisableSC ? 1 : 0).' name="issc" id="issc" '.($issc?'checked="checked" ':'').' onclick="seniorCitizen()" '.$readOnlyAll.'><label for="issc" class="segInput">Senior citizen</label>');
#$smarty->assign('sBtnPDF','<a href="#"><img src="'.$root_path.'images/btn_printpdf.gif" border="0"></a>');


$qs = "";
if( $_GET['billing'] ) $qs .= "&billing=".$_GET['billing'];
if( $_GET['pid'] ) $qs .= "&pid=".$_GET['pid'];
if( $_GET['encounterset'] ) $qs .= "&encounterset=".$_GET['encounterset'];

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&ref='.$Ref.$qs.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');
//$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('bShowQuickKeys',FALSE);

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
    
    <!--//added by VAN 01-29-2013 -->
    <input type="hidden" name="is_maygohome" id="is_maygohome" value="<?=$is_maygohome?>">
    <input type="hidden" name="bill_nr" id="bill_nr" value="<?=$bill_nr?>">
    <input type="hidden" name="hasfinal_bill" id="hasfinal_bill" value="<?=$hasfinal_bill?>">
    
	<input type="hidden" name="view_from" value="<?= $_REQUEST['view_from'] ?>" />
<?php if (isset($_REQUEST['viewonly'])) { ?>	<input type="hidden" name="viewonly" value="<?= $_REQUEST['viewonly'] ?>" /><?php } ?>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input class="link" type="image" src="'.$root_path.'images/btn_submitorder.gif" align="center">');
	$smarty->assign('sBreakButton','<img class="link" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	// $printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print&userck=$userck".'&cat=pharma&ref='.$Ref;
	$printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print-jasper&userck=$userck".'&cat=pharma&ref='.$Ref;
	$smarty->assign('sPrintButton','<img class="link"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="center" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/form.tpl');
$smarty->display('common/mainframe.tpl');

?>