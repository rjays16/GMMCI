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
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');
	
	if (isset($_POST["submitted"])) {
		$data = array(
			'refno'=>$_POST['refno'],
			'encounter_nr'=>$_POST['encounter_nr'],
			'pid'=>$_POST['pid'],
			'ordername'=>$_POST['ordername'],
			'orderaddress'=>$_POST['orderaddress'],
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
		if ($_POST['orderdate']) {
			$time = strtotime($_POST['orderdate']);
			$data["orderdate"] = date("Ymd",$time);
		}
		if ($_POST["pid"]) $data["pid"] = $_POST["pid"];
		
		$order_obj->setDataArray($data);
		$saveok=$order_obj->insertDataFromInternalArray();
		
		if ($saveok) {
			# Bulk write order items
			$bulk = array();

			foreach ($_POST["items"] as $i=>$v) {
				$consigned = in_array($v, $_POST['consigned']) ? '1' : '0';
				$bulk[] = array($_POST["items"][$i],$_POST["qty"][$i],$_POST["prc"][$i],$_POST["prc"][$i], $consigned);
			}
			$order_obj->clearOrderList($data['refno']);
			$order_obj->addOrders($data['refno'],$bulk);
			
			# Bulk write discounts
			$bulk = array();
			if ($_POST['issc']) $bulk[] = 'SC';
	
			/*
			foreach ($_POST["discount"] as $i=>$v) {
				if ($v) $bulk[] = array($v);
			}
			*/
			$order_obj->clearDiscounts($data['refno']);
			if ($bulk) {
				$order_obj->addDiscounts($data['refno'],$bulk);
			}
			$smarty->assign('sWarning',"Order item successfully created.");
		}
		else {
			$errorMsg = $db->ErrorMsg();
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sWarning','<strong>Error:</strong> An item with the same order number already exists in the database.');
			else
				$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
		}
	}

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Pharmacy::Ordering::New order");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Pharmacy::Ordering::New order");

 # Assign Body Onload javascript code
 $onLoadJS='onload="refreshDiscount();"';
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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

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
		var regexDate = new RegExp("(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\\d\\d");
		if (!regexDate.test($("orderdate").value)) {
			alert("Please enter a valid date (MM/DD/YYYY format)...");
			$("orderdate").focus();
			return false;
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
if (isset($_POST["submitted"]) && $saveok) {
	header("Location:".$root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&mode=list&userck=$userck".'&cat=pharma');
}
elseif (isset($_POST["submitted"]) && !$saveok) {

### Obsolete

/*
	$readOnly = (!$_POST['iscash'] || $_POST['pid']) ? 'readonly="readonly"' : "";
	$smarty->assign('sRefNo','<input class="jedInput" id="refno" name="refno" type="text" size="8" value="'.$_POST['refno'].'" style="font:bold 12px Arial"/>');
	$smarty->assign('sOrderDate','<input class="jedInput" name="orderdate" type="text" size="10" value="'.$_POST['orderdate'].'" style="font:bold 12px Arial">');
	$count=0;
	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType()" />Cash');
	$smarty->assign('sIsCharge','<input class="jedInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType()" />Charge');
	$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
	$smarty->assign('sOrderName','<input class="jedInput" id="ordername" name="ordername" type="text" size="40" value="'.$_POST['ordername'].'" style="font:bold 12px Arial; float:left;" '.$readOnly.'/>');
	$smarty->assign('sOrderAddress','<textarea class="jedInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" '.$readOnly.'>'.$_POST['orderaddress'].'</textarea>');
	$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()"'.(($_POST['pid'])?'':' disabled="disabled"').' />');
	$smarty->assign('sSelectEnc','<input class="jedInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" onclick="alert(\'Hello\')" style="margin-left:2px"/>');
	$smarty->assign('sResetRefNo','<input class="jedInput" type="button" value="Reset" style="font:bold 11px Arial" onclick="xajax_reset_referenceno()"/>');
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/>Urgent');
	$smarty->assign('sComments','<textarea class="jedInput" name="comments" cols="19" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic"></textarea>');
*/
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"8\">Order list is currently empty...</td>
				</tr>");
				
	if (is_array($_POST['items'])) {
		$script = '<script type="text/javascript" language="javascript">';
		$items = $_POST['items'];
		$prc = array();
		$qty = array();
		foreach ($items as $i=>$item) {
			$prc[$i] = $_POST['prc'][$i];
			$qty[$i] = $_POST['qty'][$i];
			$con[$i] = in_array($item, $_POST['consigned']) ? '1' : '0';
			if (!is_numeric($prc[$i])) $prc[$i] = 'null';
			if (!is_numeric($qty[$i])) $qty[$i] = '0';
		}
		
		$script .= "var item0=['" .implode("','",$items)."'];";
		$script .= "var prc0=[" .implode(",",$prc). "];";
		$script .= "var qty0=[" .implode(",",$qty). "];";
		$script .= "var con0=[" .implode(",",$con). "];";
		$script .= "xajax_add_item('" .$_POST['discountid']. "', item0, qty0, prc0, con0);";
		$script .= "</script>";
		$src = $script;
	}


	if ($src) $smarty->assign('sOrderItems',$src);
}
else {
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"8\">Order list is currently empty...</td>
				</tr>");
}


# Render form elements
	$submitted = isset($_POST["submitted"]);
	$readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";
	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /> <label for="iscash1">Cash</label>');
	$smarty->assign('sIsCharge','<input class="jedInput" style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" /> <label for="iscash0">Charge</label>');
	$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
	$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
	$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');
	$smarty->assign('sOrderName','<input class="jedInput" id="ordername" name="ordername" type="text" size="40" style="font:bold 12px Arial;" '.$readOnly.' value="'.$_POST["ordername"].'"/>');
	$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" '.(($_POST['pid'])?'':' disabled="disabled"').' />');
	$smarty->assign('sOrderAddress','<textarea class="jedInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" '.$readOnly.'>'.$_POST["orderaddress"].'</textarea>');
	$smarty->assign('sRefNo','<input class="jedInput" id="refno" name="refno" type="text" size="10" value="'.($submitted ? $_POST['refno'] : $lastnr).'" style="font:bold 12px Arial"/>');
	$smarty->assign('sResetRefNo','<input class="jedInput" type="button" value="Reset" style="font:bold 11px Arial" onclick="xajax_reset_referenceno()"/>');
	
	$curDate = date("m/d/Y");
	$smarty->assign('sOrderDate','<input class="jedInput" name="orderdate" id="orderdate" type="text" size="10" value="'.($submitted ? $_POST['orderdate'] : $curDate).'" style="font:bold 12px Arial">');
	
	$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
	$smarty->assign('sNormalPriority','<input class="jedInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/> <label for="p0">Normal</label>');
	$smarty->assign('sUrgentPriority','<input class="jedInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/> <label for="p1">Urgent</label>');
	$smarty->assign('sComments','<textarea class="jedInput" name="comments" cols="19" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$_POST['comment'].'</textarea>');


$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="if (warnClear()) { emptyTray(); overlib(
        OLiframeContent(\'seg-order-select-enc.php\', 700, 400, \'fSelEnc\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); } return false;"
       onmouseout="nd();" />');

$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
       onclick="return overlib(
        OLiframeContent(\'seg-order-tray.php\', 600, 340, \'fOrderTray\', 1, \'auto\'),
        WIDTH,600, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Add product from Order tray\',
        MIDX,0, MIDY,0, 
        STATUS,\'Add product from Order tray\');"
       onmouseout="nd();">
			 <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');
$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label for="issc" style="font:bold 11px Tahoma; ">Senior citizen</label>');

/*
$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');
$smarty->assign('sBtnDiscounts','<input class="segInput" type="image" id="btndiscount" src="'.$root_path.'images/btn_discounts.gif"
       onclick="overlib(
        OLiframeContent(\'seg-order-discounts.php\', 380, 125, \'if1\', 1, \'auto\'),
        WIDTH,380, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Change discount options\',
        REF,\'btndiscount\', REFC,\'LL\', REFP,\'UL\', REFY,2, 
        STATUS,\'Change discount options\'); return false;"
       onmouseout="nd();">');
*/
#$smarty->assign('sBtnPDF','<a href="#"><img src="'.$root_path.'images/btn_printpdf.gif" border="0"></a>');

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}


 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
 $smarty->assign('sFormEnd','</form>');

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
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="if (validate()) document.inputform.submit()"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/form.tpl');
$smarty->display('common/mainframe.tpl');

?>