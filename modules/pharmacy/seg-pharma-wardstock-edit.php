<?php

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_pharma_ward.php");

/*
#added by bryan on sept 26, 2008
$title="Pharmacy::Ward Stocks::Edit ward stock";
$userck="ck_prod_order_user";
$allowedarea=array("_a_1_pharmawardstocksmanage","_a_2_pharmawardstockscreate");
$level2_permission=array("_a_1_pharmaallareas");
if ($area!='all') $level2_permission[] = "_a_2_pharmaarea".$area;
#
*/

$wc = new SegPharmaWard();
global $db;

$NR = $_GET["nr"];
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
	
# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

if (isset($_POST["submit"]) && !$_REQUEST['viewonly']) {
	$bulk = array();
	foreach ($_POST["items"] as $i=>$v) {
		$bulk[] = array($_POST["items"][$i],$_POST["qty"][$i]);
	}	

	$data = array(
		'pharma_area'=>strtoupper($_POST['pharma_area']),
		'ward_id'=>$_POST['ward_id'],
		'stock_date'=>$_POST['stock_date'],
		'modify_id'=>$_SESSION['sess_temp_userid'],
		'modify_time'=>date('YmdHis')
	);
	
	if ($NR) {
		$data["history"]=$wc->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
		$wc->useStock();
		$wc->setDataArray($data);
		$wc->where = "stock_nr=".$db->qstr($NR);
		$saveok=$wc->updateDataFromInternalArray($NR,FALSE);
	}
	else {
		$data['create_id']=$_SESSION['sess_temp_userid'];
		$data['create_time']=date('YmdHis');
		$data['history']="Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n";
		$wc->useStock();
		$wc->setDataArray($data);
		$saveok = $wc->insertDataFromInternalArray();
		if ($saveok) {
			$NR = $wc->insert_id;
		}
		else {
			$errorMsg = $db->ErrorMsg();
			print_r($wc->sql);
		}
	}

	if ($saveok) {
		# Bulk write order items
		$wc->clearStocks($NR);
		$wc->addStocks($NR, $bulk);
		$smarty->assign('sysInfoMessage','Ward stock details successfully saved!');
	}
	else {
		$errorMsg = $db->ErrorMsg();
		if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
			$smarty->assign('sysErrorMessage','An item with the same stock number already exists in the database.');
		else {
			if ($errorMsg)
				$smarty->assign('sysErrorMessage',"<strong>$errorMsg</strong>");
			else
				$smarty->assign('sysErrorMessage',"<strong>Unknown error occurred!</strong>");
			#print_r($order_obj->sql);
		}
	}
}

if ($NR) {
	$stockmode = "Edit ward stock";

 # Assign Body Onload javascript code
	if ($view_only)
		$onLoadJS='onload="xajax_populate_stock(\''.$NR.'\',1);"';
	else
		$onLoadJS='onload="xajax_populate_stock(\''.$NR.'\');"';
	$smarty->assign('sOnLoadJs',$onLoadJS);
}
else {
	$_POST['pharma_area'] = $_GET['area'];
	$stockmode = "New ward stock";
}

# Title in the title bar
$smarty->assign('sToolbarTitle',"Pharmacy::$stockmode");

# Window bar title
$smarty->assign('sWindowTitle',"Pharmacy::$stockmode");

# Assign Body Onload javascript code
if ($NR) {
	if ($view_only)
		$onLoadJS='onload="xajax_populate_stock(\''.$NR.'\',1)"';
	else
		$onLoadJS='onload="xajax_populate_stock(\''.$NR.'\')"';
	$smarty->assign('sOnLoadJs',$onLoadJS);
}


# Collect javascript code
ob_start();

 # Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/wardstocks-gui.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function openOrderTray() {
		window.open("seg-order-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
	}
	
	function validate() {
		return confirm('Process this ward stock?');
	}
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages
$lastnr = $wc->getLastNr();

# Render form values
$smarty->assign('ssView',$ss_view);

if (isset($_POST["submit"]) && !$saveok) {
	/* Submitted form, but error occured */

	$count=0;
	
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Order list is currently empty...</td>
				</tr>");
	if ($src) $smarty->assign('sOrderItems',$src);
}
else {
	/* No submitted data */
#	$smarty->append('JavaScript',$sTemp);

	# Fetch order data
	#$infoResult = $order_obj->getOrderInfo($Ref);
	#$saved_discounts = $order_obj->getOrderDiscounts($Ref);
	#if ($infoResult)	$info = $infoResult->FetchRow();
	#$_POST = $info;
	#$_POST["iscash"] = $info["is_cash"];
	$issc = $saved_discounts['SC'];
	
#	$submitted = true;
	$readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";
	$readOnlyAll = "";
	if ($view_only) {
		$readOnly = "";
		$readOnlyAll = 'readonly="readonly" disabled="disabled"';
	}
	#var_dump($_POST);
}

# Fetch data
if ($NR) {
	$row = $wc->getStockDetails($NR);
	if ($row) {
		$_POST['stock_date'] = $row['stock_date'];
		$_POST['pharma_area'] = $row['pharma_area'];
		$_POST['ward_id'] = $row['ward_id'];
	}
	else {
		die($wc->sql);
		die("Invalid reference no. encountered...");
	}
}

# Select pharmacy area
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
$select_area = '<select class="jedInput" name="pharma_area" id="area"'.$disabled.' onchange="if (warnClear()) { emptyTray(); this.setAttribute(\'previousValue\',this.selectedIndex);} else this.selectedIndex=this.getAttribute(\'previousValue\');" previousValue="'.$index.'" '.$readOnlyAll.'>'."\n".$select_area."</select>\n";
$smarty->assign('sSelectArea',$select_area);


# Stock NR
$smarty->assign('sRefNo','<input class="jedInput" id="stock_nr" name="stock_nr" type="text" size="10" value="'.($NR ? $NR : $lastnr).'" style="font:bold 12px Arial" readonly="readonly"/>');
$smarty->assign('sResetRefNo','<input class="jedInput" type="button" value="Reset" style="font:bold 11px Arial" onclick="xajax_reset_stocknr()" '.($NR ? 'disabled="disabled"' : '').'/>');

# Stock date
$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
if ($_POST['stock_date']) {
	$dStockDate = strtotime($_POST['stock_date']);
	$curDate = date($dbtime_format,$dStockDate);
	$curDate_show = date($fulltime_format,$dStockDate);
}
else {
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
}

$smarty->assign('sOrderDate','<span id="show_stock_date" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($curDate_show).'</span><input class="jedInput" name="stock_date" id="stock_date" type="hidden" value="'.($curDate).'" style="font:bold 12px Arial">');
if ($view_only) {
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="stock_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;opacity:0.5">');
}
else {
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="stock_date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			displayArea : \"show_stock_date\",
			inputField : \"stock_date\",
			ifFormat : \"%Y-%m-%d %H:%M\", 
			daFormat : \"	%B %e, %Y %I:%M%P\", 
			showsTime : true, 
			button : \"stock_date_trigger\", 
			singleClick : true,
			step : 1
		});
	</script>";
	$smarty->assign('jsCalendarSetup', $jsCalScript);	
}


# Render ward list
$wards=$wc->getAll();
$wHTML = '';
while($row=$wards->FetchRow()){
	$checked=strtolower($row['ward_id'])==strtolower($_POST['ward_id']) ? 'selected="selected"' : "";
	$wHTML .= "	<option value=\"".$row['ward_id']."\" $checked>".$row['ward_name']."</option>\n";
	if ($checked) $index = $count;
	$count++;
}
$wHTML = '<select class="jedInput" name="ward_id" id="ward_id"'.$disabled.' '.$readOnlyAll.'>'."\n".$wHTML."</select>\n";
$smarty->assign('sSelectWard',$wHTML);

if ($view_only) 
	$smarty->assign('sBtnAddItem','<img id="add_i" src="'.$root_path.'images/btn_additems.gif" border="0" align="absmiddle" style="opacity:0.7">');
else
	$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
       onclick="return overlib(
        OLiframeContent(\'seg-order-tray.php\', 600, 340, \'fOrderTray\', 0, \'auto\'),
        WIDTH,600, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
        CAPTIONPADDING,2, 
				CAPTION,\'Add product from Order tray\',
        MIDX,0, MIDY,0, 
        STATUS,\'Add product from Order tray\');"
       onmouseout="nd();">
			 <img name="add_i" id="add_i" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
if ($view_only) 
	$smarty->assign('sBtnEmptyList','<img src="'.$root_path.'images/btn_emptylist.gif" border="0" align="absmiddle" style="opacity:0.5"/>');
else
	$smarty->assign('sBtnEmptyList','<a href="javascript:if (confirm(\'Clear the stock list?\')) emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');

if (!$NR) {
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"8\">Stock list is currently empty...</td>
				</tr>");
}

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&nr='.$NR.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submit" value="1" />
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	<input type="hidden" name="view_from" value="<?= $_REQUEST['view_from'] ?>" />
<?php if (isset($_REQUEST['viewonly'])) { ?>	<input type="hidden" name="viewonly" value="<?= $_REQUEST['viewonly'] ?>" /><?php } ?>
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder.gif" align="center">');
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/wardstockform.tpl');
$smarty->display('common/mainframe.tpl');

?>