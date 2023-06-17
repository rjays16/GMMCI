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

/*	
	if (!isset($_GET["ref"])) {
		die("Invalid item reference.");
		exit;
	}
	$Ref = $_GET["ref"];
	if ($_REQUEST["viewonly"]) $view_only = 1;
*/

	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');
	
 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Pharmacy::Ordering::Edit order details");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Pharmacy::Ordering::New order");
	
	if (isset($_POST["submit"]) && !$_REQUEST['viewonly']) {
		$data = array(
			'encounter_nr'=>$_POST['encounter_nr'],
			'pharma_area'=>$_POST['area'],
			'pid'=>$_POST['pid'],
			'ordername'=>$_POST['ordername'],
			'orderaddress'=>$_POST['orderaddress'],
			'orderdate'=>$_POST['orderdate'],
			'is_cash'=>$_POST['iscash'],
			'discount'=>$_POST['discount'],
			'discountid'=>$_POST['discountid'],
			'is_urgent'=>$_POST['priority'],
			'comments'=>$_POST['comments'],
			'create_id'=>$_SESSION['sess_temp_userid'],
			'modify_id'=>$_SESSION['sess_temp_userid'],
			'modify_time'=>date('YmdHis'),
			'create_time'=>date('YmdHis')
		);
		if ($_POST["pid"]) $data["pid"] = $_POST["pid"];

		$order_obj->setDataArray($data);
		$order_obj->where = "refno=".$db->qstr($_GET['ref']);
		$saveok=$order_obj->updateDataFromInternalArray($_GET["ref"],FALSE);
		if ($saveok) {
			# Bulk write order items
			$bulk = array();
			$orig = $_POST['iscash'] ? $_POST['pcash'] :  $_POST['pcharge'];
			foreach ($_POST["items"] as $i=>$v) {
				$consigned = in_array($v, $_POST['consigned']) ? '1' : '0';
				$bulk[] = array($_POST["items"][$i],$_POST["qty"][$i],$_POST["prc"][$i],$_POST["prc"][$i], $consigned, $orig[$i]);
			}			

			$order_obj->clearOrderList($Ref);
			$order_obj->addOrders($Ref, $bulk);
			
			# Bulk write discounts
			$bulk = array();
			if ($_POST['issc']) $bulk[] = 'SC';
			/*
			foreach ($_POST["discount"] as $i=>$v) {
				if ($v) $bulk[] = array($v);
			}
			*/
			/*
			$order_obj->clearDiscounts($data['refno']);
			if ($bulk) {
				$order_obj->addDiscounts($data['refno'],$bulk);
			}
			*/
			
			$sBreakImg ='close2.gif';
			$smarty->assign('sMsgTitle','Pharmacy order successfully updated!');
			$smarty->assign('sMsgBody','The order details have been saved into the database...');
			$smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
			$printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print-jasper&userck=$userck".'&cat=pharma&ref='.$Ref;
			$smarty->assign('sPrintButton','<img class="segSimulatedLink"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');
			
			# Assign submitted form values
			$smarty->assign('sRefNo', $Ref);
			$smarty->assign('sSelectArea', $_REQUEST['area']);
			$smarty->assign('sCashCharge', ($_REQUEST['iscash']=="1") ? "Cash" : "Charge");
			$smarty->assign('sOrderDate', $_REQUEST['orderdate']);
			$smarty->assign('sOrderName', $_REQUEST['ordername']);
			$smarty->assign('sOrderAddress', $_REQUEST['orderaddress']);
			$smarty->assign('sPriority',($_REQUEST['priority']=="0") ? "Normal" : "Urgent");
			$smarty->assign('sRemarks',$_REQUEST['comments']);
			
			include_once($root_path."include/care_api_classes/class_product.php");
			$prod_obj = new Product();
			$items_array = $prod_obj->getProductName($_REQUEST['items']);
			$items = array();
			foreach ($_REQUEST['items'] as $i=>$v)
				$items[] = "<li>x". $_REQUEST['qty'][$i] . " " .$items_array[$v]. "</li>";
			$show_items = "<ul style=\"margin:0; padding-left:18px;list-style:disc\">".implode("",$items)."</ul>";
			$smarty->assign('sItems',$show_items);
			
			$smarty->assign('sMainBlockIncludeFile','order/saveok.tpl');
			$smarty->display('common/mainframe.tpl');
			exit;
		}
		else {
			$errorMsg = $db->ErrorMsg();
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sWarning','<strong>Error:</strong> An item with the same order number already exists in the database.');
			else
				$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
		}
	}

 # Assign Body Onload javascript code
 if ($view_only)
	 $onLoadJS='onload="xajax_populate_order(\''.$Ref.'\',1);refreshDiscount();"';
	else
	 $onLoadJS='onload="xajax_populate_order(\''.$Ref.'\');refreshDiscount();"';
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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#ffffff;
	border:1px outset #3d3d3d;
}
.olcg {
	background-color:#ffffff; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffff; 
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px; 
	font-weight:bold; 
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
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

	function openOrderTray() {
		window.open("seg-order-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
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
		return true;
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
if (isset($_POST["submit"]) && !$saveok) {
}
else {
	$smarty->append('JavaScript',$sTemp);

	# Fetch order data
	#$infoResult = $order_obj->getOrderInfo($Ref);
	#if ($infoResult)	$info = $infoResult->FetchRow();
	#$_POST = $info;
	$_POST["iscash"] = $info["is_cash"];
	$issc = $saved_discounts['SC'];
	
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
	$select_area = '<select class="jedInput" name="area" id="area"'.$disabled.' onchange="if (warnClear()) { emptyTray(); this.setAttribute(\'previousValue\',this.selectedIndex);} else this.selectedIndex=this.getAttribute(\'previousValue\');" previousValue="'.$index.'">'."\n".$select_area."</select>\n";
	$smarty->assign('sSelectArea',$select_area);
	
	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" '.$readOnlyAll.'/> <label for="iscash1">Cash</label>');
	$smarty->assign('sIsCharge','<input class="jedInput" style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" '.$readOnlyAll.'/> <label for="iscash0">Charge</label>');
	$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
	$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
	$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');
	$smarty->assign('sOrderName','<input class="jedInput" id="ordername" name="ordername" type="text" size="40" style="font:bold 12px Arial;" '.$readOnly.' value="'.$_POST["ordername"].'" '.$readOnlyAll.'/>');
	$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" '.(($_POST['pid'])?'':' disabled="disabled"').' '.$readOnlyAll.'/>');
	$smarty->assign('sOrderAddress','<textarea class="jedInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" '.$readOnly.' '.$readOnlyAll.'>'.$_POST["orderaddress"].'</textarea>');
	$smarty->assign('sRefNo','<input class="jedInput" id="refno" name="refno" type="text" size="10" value="'.($submitted ? $_POST['refno'] : $lastnr).'" style="font:bold 12px Arial" readonly="readonly"/>');
	$smarty->assign('sResetRefNo','<input class="jedInput" type="button" value="Reset" style="font:bold 11px Arial" onclick="xajax_reset_referenceno()" '.$readOnlyAll.'/>');
	
	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	if ($view_only) {
		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;opacity:0.5">');
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
	$smarty->assign('sNormalPriority','<input class="jedInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').$readOnlyAll.'/> <label for="p0">Normal</label>');
	$smarty->assign('sUrgentPriority','<input class="jedInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').$readOnlyAll.'/> <label for="p1">Urgent</label>');
	$smarty->assign('sComments','<textarea class="jedInput" name="comments" cols="19" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic" '.$readOnlyAll.'>'.$_POST['comments'].'</textarea>');
}

	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" align="absmiddle" style="opacity:0.5">');
	$smarty->assign('sBtnAddItem','<img id="select-enc" src="'.$root_path.'images/btn_additems.gif" border="0" align="absmiddle" style="opacity:0.5">');
	$smarty->assign('sBtnEmptyList','<img src="'.$root_path.'images/btn_emptylist.gif" border="0" align="absmiddle" style="opacity:0.5"/>');

$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.($issc?'checked="checked" ':'').' onclick="seniorCitizen()" '.$readOnlyAll.'><label for="issc" style="font:bold 11px Tahoma; ">Senior citizen</label>');
 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&ref='.$Ref.'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
 $smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submit" value="1" />
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
$smarty->assign('sMainBlockIncludeFile','order/refund.tpl');
$smarty->display('common/mainframe.tpl');

?>