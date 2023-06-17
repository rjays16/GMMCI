<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/pharmacy/ajax/order.common.php");

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title=$LDPharmacy;
$imgpath=$root_path."pharma/img/";
$thisfile='seg-cashier-billing-main.php';

$sBillNr=$_GET["nr"];

include_once($root_path."include/care_api_classes/class_cashier.php");

$cClass = new SegCashier();
global $db;
$rowDetails = $cClass->GetBillingDetails($sBillNr);
$rowCoverage = $cClass->GetBillingCoverage($sBillNr);
$rowDiscount = $cClass->GetBillingDiscount($sBillNr);

$breakfile=$root_path."modules/cashier/seg-cashier-billing-list.php".URL_APPEND."&patient=".$rowDetails["pid"];

	# Note: it is advisable to load this after the inc_front_chain_lang.php so
	# that the smarty script can use the user configured template theme
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');
	
	# Saving
	if (isset($_POST["submitted"])) {
	
		$data = array(
			'or_no' => $_POST['orno'],
			'or_date' => $_POST['ordate'],
			'or_name' => $_POST['orname'],
			'encounter_nr' => $_POST['encounter_nr'],
			'pid' => $_POST['pid'],
			'or_address' => $_POST['oraddress'],
			'amount_tendered' => $_POST['amount_tendered'],
			'amount_due' => $_POST['total_due'],
			'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
			'remarks' => $_POST['remarks'],
			'create_id'=>$_SESSION['sess_temp_userid'],
			'modify_id'=>$_SESSION['sess_temp_userid'],
			'modify_time'=>date('YmdHis'),
			'create_time'=>date('YmdHis')
		);
		$ORNo = $data['or_no'];
		$cClass->setDataArray($data);
		$cClass->usePay();
		$saveok=$cClass->insertDataFromInternalArray();
		
		if ($saveok) {
			if ($_POST["chkcheck"]) {
				$checkno = $_POST["checkno"];
				$checkdate = $_POST["checkdate"];
				$checkbank = $_POST["checkbank"];
				$checkpayee = $_POST["checkpayee"];
				$checkamount = $_POST["checkamount"];
				$cClass->AttachCheckDetails($ORNo, $checkno, $checkdate, $checkbank, $checkpayee, $checkamount);
			}
			
			if ($_POST["chkcard"]) {
				$cardno = $_POST["cardno"];
				$cardbank = $_POST["cardbank"];
				$cardbrand = $_POST["cardbrand"];
				$cardname = $_POST["cardname"];
				$cardexpr = $_POST["cardexpr"];
				$cardcode = $_POST["cardcode"];
				$cardamount = $_POST["cardamount"];
				$cClass->AttachCardDetails($ORNo, $cardno, $cardbank, $cardbrand, $cardname, $cardexpr, $cardcode, $cardamount);
			}
			
			$ref_arr = array($sBillNr);
			$src_arr = array("FB");
			$svc_arr = array($sBillNr);
			$amt_arr = array($_POST['total_due']);
			$cClass->AttachMultipleRequests($ORNo, $ref_arr, $src_arr, $svc_arr, $amt_arr);			
		}
		else {
			# Payment not saved 
		}
	}

 $smarty->assign('sRootPath',$root_path);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::Process billing account");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Process billing account");

 # Assign Body Onload javascript code
 $onLoadJS="onload=\"
 	shortcut.add('F2', enterSubTotal,
		{
			'type':'keydown',
			'propagate':false,
		}
	);

 	shortcut.add('F3', enterAmountTendered,
		{
			'type':'keydown',
			'propagate':false,
		}
	);
	
	refreshTotal();
	\"";
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

<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/cashier-main.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function openOrderTray() {
		window.open("seg-order-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
	}
	
	function refreshTotal() {
		subtotal = parseFloat($("show-sub-total").getAttribute('value'))
		if (isNaN(subtotal)) subtotal = 0
	
		var discountTotal = 0
		nettotal = subtotal
		
		if ($('show-sub-total')) $('show-sub-total').innerHTML = formatNumber(subtotal, 2)

		if ($('show-discount-total')) $('show-discount-total').innerHTML = (discountTotal <= 0) ? '('+formatNumber(Math.abs(discountTotal), 2)+')' : '<span style="color:red">'+formatNumber(discountTotal,2)+'</span>'
		if ($('show-discount-total')) $('show-discount-total').setAttribute('value',discountTotal)

		if ($('show-net-total')) $('show-net-total').innerHTML = formatNumber(nettotal, 2)
		if ($('show-net-total')) $('show-net-total').setAttribute('value',nettotal)
	
		refreshAmountChange()
	}
	
	function enterSubTotal() {
		var x = prompt("Enter total deposit amount :")
		if (isNaN(x) || x<0) alert("Invalid amount entered...")
		else {
			$('show-sub-total').value = x
			$("show-sub-total").innerHTML = formatNumber(x,2)
			$("show-sub-total").setAttribute('value',x)
		}
		refreshTotal()
	}
	
	function enterAmountTendered() {
		var x = prompt("Enter amount tendered :")
		if (isNaN(x) || x<0) alert("Invalid amount entered...")
		else {
			$('amount_tendered').value = x
			$("show-amt-tendered").innerHTML = formatNumber(x,2)
			$("show-amt-tendered").setAttribute('value',x)
		}
		refreshTotal()
	}
	
	function validate() {
		if (!$('orno').value) {
			alert('Please enter the OR#')
			$('orno').focus()
			return false;
		}
		
		if (!$('amount_tendered').value) {
			alert('Please enter the OR#')
			$('orno').focus()
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
 $smarty->assign('bShowHospitalServices',TRUE);


# Render form values
if (isset($_POST["submitted"])) {
	if ($saveok) {
		# $smarty->assign('sWarning',"Pay request successfully processed...");
		$change = ($_POST['amount_tendered'] - $total_due);
		if ($change < 0) $change = '<span style="color:green">Charity</span>';
		$assignArray = array(
			'sUseCheck' => $_POST['chkcheck'],
			'sUseCard' => $_POST['chkcard'],
			'sORNo' => $_POST['orno'],
			'sORName' => $_POST['orname'],
			'sORAddress' => $_POST['oraddress'],
			'sORDate' => $_POST['ordate'],
			'sPID' => $_POST['pid'],
			'sEncounterNr' => $_POST['encounter_nr'],
			'sAmountDue' => $total_due,
			'sAmountTendered' => $_POST['amount_tendered'],
			'sAmountChange' => $change,
			'sRemarks' => $_POST['remarks'],
			'sCheckNo' => $_POST['checkno'],
			'sCheckDate' => $_POST['checkdate'],
			'sCheckBank' => $_POST['checkbank'],
			'sCheckName' => $_POST['checkpayee'],
			'sCheckAmount' => $_POST['checkamount'],
			'sCardNo' => $_POST['cardno'],
			'sCardBank' => $_POST['cardbank'],
			'sCardBrand' => $_POST['cardbrand'],
			'sCardName' => $_POST['cardname'],
			'sCardExpiry' => $_POST['cardexpdate'],
			'sCardAmount' => $_POST['cardamount'],
			'sMessageHeader' => "Request successfully processed..."
		);
		
		foreach ($assignArray as $i=>$v)
			$smarty->assign($i, $v);
		
		$sBreakImg ='close2.gif';
		$smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
		$printfile = $root_path.'modules/cashier/seg-cashier-print.php'. URL_APPEND."&nr=".$_POST['or_no'];
		$smarty->assign('sPrintButton','<img class="segSimulatedLink"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');
		$smarty->assign('sMainBlockIncludeFile','cashier/cashier_message.tpl');
		$smarty->display('common/mainframe.tpl');
		exit();
	}
	else {
		$smarty->assign('sWarning',"Error processing request...<br>".
		"Error:".$db->ErrorMsg()."<br>".
		"SQL:".$cClass->sql);
	}
}

	#$resultInfo = $cClass->GetRequestInfo($sRefNo,$sDept);
	$smarty->assign('sORNo','<input class="jedInput" id="orno" name="orno" type="text" size="15" value=""/>');
	
	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
	$smarty->assign('sORDate','<span id="show_ordate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['ordate'])) : $curDate_show).'</span><input class="jedInput" name="ordate" id="ordate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['ordate'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="ordate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
	$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			displayArea : \"show_ordate\",
			inputField : \"ordate\",
			ifFormat : \"%Y-%m-%d %H:%M\", 
			daFormat : \"	%B %e, %Y %I:%M%P\", 
			showsTime : true, 
			button : \"ordate_trigger\", 
			singleClick : true,
			step : 1
		});
	</script>";
	
	$smarty->assign('jsCalendarSetup', $jsCalScript);	
	
	$smarty->assign('sORName','<input class="jedInput" id="orname" name="orname" type="text" size="30" readonly="readonly" value="'.$rowDetails['fullname'].'"/>');
	$smarty->assign('sOREncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$rowDetails["encounter_nr"].'"/>');
	$smarty->assign('sOREncID','<input id="pid" name="pid" type="hidden" value="'.$rowDetails["pid"].'"/>');
	$smarty->assign('sORAddress','<textarea class="jedInput" id="oraddress" name="oraddress" cols="27" readonly="readonly" rows="2">'.$rowDetails["address"].'</textarea>');
	$smarty->assign('sAmountTendered','<input class="jedInput" id="amount_tendered" name="amount_tendered" type="text" size="20" value="" onfocus="amtTenderedOnBlurFocusHandle(this)" onblur="amtTenderedOnBlurFocusHandle(this)"/>');	
	$smarty->assign('sRemarks','<textarea class="jedInput" name="remarks" cols="25" rows="2" style="float:left; font-size:12px; font-weight:normal;"></textarea>');

	/* Check options */
	$smarty->assign('sCheckOption','<input class="jedInput" id="chkcheck" name="chkcheck" type="checkbox" onchange="enableInputChildren('."'check-details'".',this.checked)"/><label class="jedInput" for="chkcheck">Use check</label>');
	$smarty->assign('sCheckNo','<input class="jedInput" id="checkno" name="checkno" type="text" size="15" value="'.$checkno.'" disabled="disabled" />');
	$smarty->assign('sCheckDate','<input class="jedInput" id="checkdate" name="checkdate" type="text" size="15" value="'.$checkdate.'" disabled="disabled" />');
	$smarty->assign('sCheckBankName','<input class="jedInput" id="checkbank" name="checkbank" type="text" size="30" value="'.$checkbank.'" disabled="disabled" />');
	$smarty->assign('sCheckPayee','<input class="jedInput" id="checkpayee" name="checkpayee" type="text" size="30" value="'.$checkpayee.'" disabled="disabled" />');	
	$smarty->assign('sCheckAmount','<input class="jedInput" id="checkamount" name="checkamount" type="text" size="15" value="'.$checkamount.'" disabled="disabled" />');	

	/* Credit Card */
	$smarty->assign('sCardOption','<input class="jedInput" id="chkcard" name="chkcard" type="checkbox" onchange="enableInputChildren('."'card-details'".',this.checked)"/><label class="jedInput" for="chkcard">Use Card</label>');
	$smarty->assign('sCardNo','<input class="jedInput" id="cardno" name="cardno" type="text" size="15" value="'.$cardno.'" style="" disabled="disabled" />');
	$smarty->assign('sCardIssuingBank','<input class="jedInput" id="cardbank" name="cardbank" type="text" size="30" value="'.$cardbank.'" disabled="disabled" />');
	$smarty->assign('sCardBrand','<input class="jedInput" id="cardbrand" name="cardbrand" type="text" size="30" value="'.$cardbrand.'" disabled="disabled" />');
	$smarty->assign('sCardName','<input class="jedInput" id="cardname" name="cardname" type="text" size="30" value="'.$cardname.'" disabled="disabled" />');
	$smarty->assign('sCardExpiryDate','<input class="jedInput" id="cardexpdate" name="cardexpdate" type="text" size="10" value="'.$cardexpdate.'" disabled="disabled" />');
	$smarty->assign('sCardSecurityCode','<input class="jedInput" id="cardcode" name="cardcode" type="text" size="5" value="'.$cardcode.'" disabled="disabled" />');
	$smarty->assign('sCardAmount','<input class="jedInput" id="cardamount" name="cardamount" type="text" size="15" value="'.$cardamount.'" disabled="disabled" />');
	
	/* Print billing details */
	$detailsArray = array(
		'acc'=>'Accommodation',
		'med'=>'Medicines',
		'sup'=>'Supplies',
		'srv'=>'Services',
		'ops'=>'Procedures',
		'doc'=>'Doctor\'s Fees',
		'msc'=>'Miscellaneous'
	);
	
	$count = 0;
	$grand_total = 0;
	foreach ($detailsArray as $i => $details) {
		$total = (float) $rowDetails[$i];
		$discount = (float) $rowDiscount;
		$discounted = $total - ($total * $discount);
		$coverage = (float) $rowCoverage[$i];
		$excess = $discounted - $coverage;
		if ($excess < 0) $excess = 0;
		$grand_total += $excess;
		$class = ($count%2>0) ? 'alt' : "";
		$HTML .= "
			<tr class=\"$class\">
				<td style=\"color:#000000;font:bold 12px Tahoma;\"><span style=\"margin:2px\">$details</span></td>
				<td align=\"right\">".number_format($total,2)."</td>
				<td align=\"right\" style=\"white-space:nowrap\">".number_format($discount*$total,2)."</td>
				<td align=\"right\">".number_format($discounted,2)."</td>
				<td align=\"right\">".number_format($coverage,2)."</td>
				<td style=\"color:#000060;font:bold 14px Arial\" align=\"right\">".number_format($excess,2)."</td>
			</tr>
";
		$count++;
	}
	$smarty->assign('sBillDetails',$HTML);
	$prev = (float)$rowDetails['prev'];
	if (!$prev) $prev = 0;
	$smarty->assign('sPrevPayments',"(".number_format($prev,2) . ")");
	
	$grand_total -= $prev;
	$smarty->assign('sTotalPayment',number_format($grand_total,2));
	$smarty->assign('sGUIvSubTotal',$grand_total);
	$smarty->assign('sGUISubTotal',number_format($grand_total,2));

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}


 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&nr='.$sBillNr.'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
 $smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

/*
include_once($root_path."include/care_api_classes/class_discount.php");
$discountClass = new SegDiscount();
$src = "";
if ($result = $discountClass->getAllDataObject()) {
	$posted_discounts=array();
	if ($_POST["discount"]) {
		foreach ($_POST["discount"] as $i=>$v) {
			if ($v) $posted_discounts[$v] = $v;
		}
	}

	while ($row = $result->FetchRow()) {
		echo '	<input type="hidden" id="discount_'.$row['discountid'].'" name="discount[]" discount="'.$row["discount"].'" value="'.$posted_discounts[$row["discountid"]].'" />';
	}
}
*/
?>
	<input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="refno" value="<?php echo $sRefNo?>">
  <input type="hidden" name="dept" value="<?php echo $sDept?>">
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
  <input type="hidden" name="iscash" id="iscash1" value="0">
  <input type="hidden" name="total_due" value="<?php echo  $grand_total?>">
	
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/his_cancel_button.gif" align="absmiddle" />');
$smarty->assign('sContinueButton','<img class="segSimulatedLink" src="'.$root_path.'images/his_process_button.gif" align="absmiddle" onclick="if (confirm(\'Process this payment?\')) if (validate()) document.inputform.submit()" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_billing.tpl');
$smarty->display('common/mainframe.tpl');

?>