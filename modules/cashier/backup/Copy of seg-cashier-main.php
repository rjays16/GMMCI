<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/cashier/ajax/cashier-main.common.php");

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

include_once($root_path."include/care_api_classes/class_cashier_service.php");
include_once($root_path."include/care_api_classes/class_cashier.php");
$cClass = new SegCashier();
$sClass = new SegCashierService();

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title=$LDPharmacy;
if (!$_GET['from'])
	$breakfile=$root_path."modules/cashier/seg-cashier-requests.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$imgpath=$root_path."pharma/img/";
$thisfile='seg-cashier-main.php';


$sRefNoArray = array();
$sDeptArray = array();
if ($_REQUEST['reference']) {
	foreach ($_REQUEST['reference'] as $i=>$v) {
		$values = explode("_",$v);
		$sRefNoArray[] = $values[1];
		$sDeptArray[] = strtolower($values[0]);
	}
}
elseif ($_GET['or']) {
	$rs_pay_ref = $cClass->GetPayReferences($_GET['or']);
	$sRefNoArray = array();
	$sDeptArray = array();
	while ($row_pay_ref = $rs_pay_ref->FetchRow()) {
		$sRefNoArray[] = $row_pay_ref['ref_no'];
		$sDeptArray[] = $row_pay_ref['ref_source'];
	}
}
elseif ($_GET['ref']) {
	$sRefNoArray[]=$_GET["ref"];
	$sDeptArray[]=strtolower($_GET["dept"]);
}

if (!$sRefNoArray || !$sDeptArray) {
	die("Invalid reference code or source department...");
}

	# Note: it is advisable to load this after the inc_front_chain_lang.php so
	# that the smarty script can use the user configured template theme
	
	global $db;
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');
	
	# Saving
	if (isset($_POST["submitted"])) {
	
		#die(print_r($_POST)	);
	
		$ORNo = $_POST["orno"];
		$ORDate = $_POST["ordate"];
		$AmtTendered = $_POST["amount_tendered"];
		$Remarks = $_POST["remarks"];
		
		$requests = $_POST['requests'];
		$total_due = 0;
		if (is_array($requests)) {
			$ref_arr = array();
			$src_arr = array();
			$svc_arr = array();
			$qty_arr = array();
			$amt_arr = array();
			foreach ($requests as $req) {
				$srcDept = strtolower(substr($req, 0, 2));
				$refNo = substr($req, 2);
				
				$items = $_POST[$req];
				if (is_array($items)) {
					foreach ($items as $i=>$item) {
						$src_arr[] = $srcDept;
						$ref_arr[] = $refNo;							
						$svc_arr[] = $item;
						$qty_arr[] = $_POST['qty_'.$req][$i];
						$amt_arr[] = $_POST['total_'.$req][$i];
						$total_due += $_POST['total_'.$req][$i];
					}						
				}
				else {
					# Item list is empty
					# Some error handling, maybe
				}
			}
		}
		//Poriferanbob Quadrilateraltrousers
		
		$data = array(
			'or_date' => $_POST['ordate'],
			'or_name' => $_POST['orname'],
			'or_address' => $_POST['oraddress'],
			'amount_tendered' => $_POST['amount_tendered'],
			'amount_due' => $total_due,
			'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
			'remarks' => $_POST['remarks'],
			'modify_id'=>$_SESSION['sess_temp_userid'],
			'modify_time'=>date('YmdHis'),
		);
		$cClass->usePay();
		
		if ($_POST['encounter_nr']) 
			$data['encounter_nr'] = $_POST['encounter_nr'];
		if ($_POST['pid']) 
			$data['pid'] = $_POST['pid'];
			
		if ($_GET['or']) {
			$cClass->setDataArray($data);
			$cClass->where = "or_no=".$db->qstr($_GET['or']);
			$saveok=$cClass->updateDataFromInternalArray($_GET["or"],FALSE);
			$ORNo = $_POST['or'];
		}
		else {
			$data['or_no']=$_POST['orno'];
			$data['create_id']=$_SESSION['sess_temp_userid'];
			$data['create_time']=date('YmdHis');
			$cClass->setDataArray($data);
			$saveok=$cClass->insertDataFromInternalArray();
			$ORNo = $data['or_no'];
		}

		#$saveok=$cClass->CreatePayment($ORNo, $ORDate, $AmtTendered, $Remarks);		
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
			
			
			#$requests = $_POST['requests'];
			if (is_array($requests)) {
				/*
				$ref_arr = array();
				$src_arr = array();
				$svc_arr = array();
				$amt_arr = array();
				foreach ($requests as $req) {
					$srcDept = strtolower(substr($req, 0, 2));
					$refNo = substr($req, 2);
					$total_due += $_POST['subtotal_'.$req];
					if ($srcDept != 'ph') {
						# if Department is not pharmacy
						$items = $_POST[$req];
						if (is_array($items)) {
							foreach ($items as $item) {
								$src_arr[] = $srcDept;
								$ref_arr[] = $refNo;
								$svc_arr[] = $item;
								$amt_arr[] = $_POST['subtotal_'.$req];
							}
						}
						else {
							# Item list is empty
						}
					}
					else {
						# if Department is pharmacy
						# Do not include service code
						$src_arr[] = $srcDept;
						$ref_arr[] = $refNo;
						$svc_arr[] = "";
						$amt_arr[] = $_POST['subtotal_'.$req];
					}
				}
				*/
				# die(print_r($src_arr, true) . "<hr>" . print_r($ref_arr, true) . "<hr>" . print_r($svc_arr, true) . "<hr>" . print_r($amt_arr, true) . "<hr>");
				if (count($src_arr) > 0) {
					$cClass->AttachMultipleRequestsWithQty($ORNo, $ref_arr, $src_arr, $svc_arr, $qty_arr, $amt_arr);
				}
				else {
					# No requests to process
				}
			}
			else {
				# Invalid request posted
			}
		}
		else {
			# Payment not saved 
		}
	}

 $smarty->assign('sRootPath',$root_path);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::Process request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Process request");

 # Assign Body Onload javascript code
 $onLoadJS="onload=\"
 	shortcut.add('F2', enterAmountTendered,
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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
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
	
	function enterAmountTendered() {
		var x = prompt("Enter amount tendered :")
		if (isNaN(x) || x<0) alert("Invalid amount entered...")
		else {
			$('amount_tendered').value = x
			$("show-amt-tendered").innerHTML = formatNumber(x,2)
			$("show-amt-tendered").setAttribute('value',x)
		}
		refreshAmountChange()
	}

	function openOrderTray() {
		window.open("seg-order-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
	}
	
	function validate() {
	/*
		if (!$('account_type').value) {
			alert('Please select the account type')
			$('account_type').focus()
			return false
		}
	*/
		
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
	
	function disableChildrenInputs(node, disable) {
		if (node) {
			var children = node.getElementsByTagName('INPUT')
			for (var i=0;i<children.length;i++) {
				if (children[i].type != 'checkbox')	children[i].disabled = disable
			}
		}
	}
	
	function okORNo(ok) {
		if (ok) {
		}
	}
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages
 $smarty->assign('bShowHospitalServices',FALSE);

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
		
		$printfile = $root_path.'modules/cashier/seg-cashier-print.php'. URL_APPEND."&nr=".$ORNo;
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

# Render form values
	$HTML = "";
	if ($_GET['or']) {
		$pay_info = $cClass->GetPayInfo($_GET['or'], true);
		$rs_pay_req = $cClass->GetPayRequests($_GET['or']);
		$checked_requests = array();
		while ($row_pay_req = $rs_pay_req->FetchRow()) {
			$checked_requests[] = $row_pay_req['ref_source'] . $row_pay_req['ref_no'] . $row_pay_req['service_code'];
		}
	}
	
	foreach ($sRefNoArray as $index=>$sRefNo) {
		$sDept = $sDeptArray[$index];
		$resultInfo = $cClass->GetRequestInfo($sRefNo,$sDept);
		if ($resultInfo) $rRow = $resultInfo->FetchRow();
		$count=0;
		$request_name = "";
		$request_address = "";
		if (!$_GET['or']) {
			$pay_info['pid'] = $rRow['request_pid'];
			$pay_info['encounter_nr'] = $rRow['request_encounter'];
		}
		$src = $rRow['source_dept'];
		$rRow['is_cash'] = TRUE;
		$isCash = TRUE;
		if (!$request_name) $request_name = $rRow["request_name"];
		if (!$request_address) $request_address = $rRow["request_address"];
		
#		$limit =  ----- continue -----
		if (is_numeric($rRow['grant_amount']))
			$limit = $rRow['grant_amount'];
		else
			$limit = NULL;
			
		$rsDetails = $cClass->GetRequestDetails($sRefNo, $sDept, $_GET['or']);
		if ($rsDetails) {
			$srcRef = $sDept.$sRefNo;
			$dept_names = array('ph'=>'Pharmacy request', 'rd'=>'Radiology request', 'ld'=>'Laboratory request', 'fb'=>'Final billing',	'pp'=>'Partial payment', 'or'=>'Operating room', 'other'=>'Misc. services');
			$name = $dept_names[strtolower($sDept)] . " no. $sRefNo";
			$HTML .= "
<div id=\"$srcRef\" class=\"dashlet\">
	<div align=\"left\" class=\"dashletHeader\">
		<h1>".$name."</h1>
	</div>
	<input name=\"requests[]\" type=\"hidden\" srcDept=\"$sDept\" refNo=\"$sRefNo\" value=\"$srcRef\"/>
	<input name=\"iscash[]\" type=\"hidden\" srcDept=\"$sDept\" refNo=\"$sRefNo\" value=\"$isCash\"/>
	<table id=\"list_".$srcRef."\" class=\"jedList\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;margin-bottom:10px\">
		<thead>
			<tr id=\"row_".$srcRef."\">
				<th width=\"3%\" style=\"padding:0 2px;\">
					<input type=\"checkbox\" onchange=\"flagCheckBoxesByName('".$srcRef."[]',this.checked); calcSubTotal('$sDept','$sRefNo')\" checked=\"checked\">
				</th>
				<th align=\"left\" width=\"10%\" nowrap>Item No</th>
				<th align=\"left\" width=\"*\" nowrap=\"nowrap\">Item Description</th>
				<th align=\"right\" width=\"9%\" nowrap=\"nowrap\" style=\"font-size:90%\">Price/item (Orig)</th>
				<th align=\"right\" width=\"9%\" nowrap=\"nowrap\" style=\"font-size:90%\">Price/item (Adj)</th>
				<th align=\"right\" width=\"9%\" nowrap=\"nowrap\">Quantity</th>
				<th align=\"right\" width=\"9%\" nowrap=\"nowrap\">Price (Orig)</th>
				<th align=\"right\" width=\"9%\" nowrap=\"nowrap\" >Price (Adj)</th>
			</tr>
		</thead>
		<tbody>
";
			setlocale(LC_MONETARY, 'en_US');
			$subtotal = 0.0;
			$subtotalorig = 0.0;
			while ($rowDetails=$rsDetails->FetchRow()) {
			
				$class = ($count%2>0) ? '' : "";
				$price = $rowDetails[$rRow['is_cash'] ? "price_cash" : "price_charge"];				
				$orig = $isCash ? $rowDetails["price_cash_orig"] : $rowDetails["price_charge_orig"];
				$sOrig = $orig ? money_format("%(!.2i",$orig) : "-";
			
				$srcRefItem = $srcRef . $rowDetails['item_no'];
				$total = $price*$rowDetails["quantity"];
				$totalorig = $orig*$rowDetails["quantity"];

				$item_checked = in_array($srcRefItem, $checked_requests) || !$_GET['or'];
				if (!$rowDetails["is_paid"] && $item_checked) {
					$subtotal += $total;
					$subtotalorig += $totalorig;
				}
			
				$disabled = (!is_null($limit)) ? 'onclick="return false"' : "";				
				
				$item_no_color = $rowDetails["is_paid"] ? "#999999" : "#660000";
				$item_name_color = $rowDetails["is_paid"] ? "#999999" : "0";
				
				$HTML .= "
			<tr id=\"row_".$srcRefItem."\" class=\"".$class."\">
				<td style=\"padding:0 3px;\">";
				if ($rowDetails["is_paid"])
					$HTML .= "&nbsp;<input id=\"".$srcRefItem."\" name=\"".$srcRef."[]\" type=\"hidden\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" disabled=\"disabled\" value=\"\"/>";
				else {
					if (!is_null($limit))
						$HTML .= "<input id=\"".$srcRefItem."\" name=\"".$srcRef."[]\" type=\"hidden\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" value=\"".$rowDetails['item_no']."\"/><input id=\"".$srcRefItem."\" type=\"checkbox\" checked=\"checked\" ".$disabled." value=\"".$rowDetails['item_no']."\"/>";
					else
						$HTML .= "<input id=\"".$srcRefItem."\" name=\"".$srcRef."[]\" type=\"checkbox\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" ".($item_checked ? 'checked="checked"' : '')." ".$disabled." value=\"".$rowDetails['item_no']."\" onchange=\"calcSubTotal('$sDept','$sRefNo'); refreshTotal(); disableChildrenInputs($('row_".$srcRefItem."'),!this.checked)\"/>";
				}
				$desc = $rowDetails['item_name'];
				if (strlen($desc)>40) $desc = substr($desc,0,40) . "...";
				if (!$desc) $desc = "[Unknown item]";
				
				if ($item_group)
					$item_group = "(".$rowDetails['item_group'].")";
				
				
				$HTML .= "</td>
				<td align=\"left\"><span id=\"id_".$srcRefItem."\" style=\"font:bold 11px Arial;color:$item_no_color\">".$rowDetails['item_no']."</span></td>
				<td align=\"left\" style=\"overflow:hidden\"><span id=\"desc_".$srcRefItem."\" style=\";font:bold 12px Arial;color:$item_name_color\">".''.htmlentities($desc).' '.$item_group."</span></td>\n";
				$adjColor = ($price < $orig) ? "#060" : "#0";
				if ($rowDetails["is_paid"]) {
					$HTML .= "				<td align=\"right\" colspan=\"4\">
					<input id=\"price_".$srcRefItem."\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" disabled=\"disabled\"  value=\"0\"/>
					<input id=\"qty_".$srcRefItem."\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" disabled=\"disabled\" value=\"0\"/>
					<input id=\"total_".$srcRefItem."\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" disabled=\"disabled\" value=\"0\"/>
					<img src=\"".$root_path."images/paid_item.gif\" align=\"absmiddle\" />
				</td>
				<td align=\"right\">
					<img src=\"".$root_path."images/paid_item.gif\" align=\"absmiddle\" />
				</td>";
				}
				else {
					if (is_null($limit)) {
						$HTML .= "				<td align=\"right\">
					<input id=\"priceorig_".$srcRefItem."\" name=\"priceorig_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"".$orig."\"/>".$sOrig."
				</td>
				<td align=\"right\" style=\"color:$adjColor\">
					<input id=\"price_".$srcRefItem."\" name=\"price_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"".$price."\"/>".money_format("%(!.2i",$price)."
				</td>
				<td align=\"right\">
					<input id=\"qty_".$srcRefItem."\" name=\"qty_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"".$rowDetails["quantity"]."\"/>".($rowDetails["quantity"]>1 ? ('x'.$rowDetails["quantity"]) : ($sDept=="ph" ? "x1" : "&nbsp;"))."
				</td>
				<td align=\"right\">
					<input id=\"totalorig_".$srcRefItem."\" name=\"totalorig_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"$totalorig\"/>".money_format("%(!.2i",$totalorig)."
				</td>
				<td align=\"right\" style=\"color:$adjColor\">
					<input id=\"total_".$srcRefItem."\" name=\"total_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"$total\"/>".money_format("%(!.2i",$total)."
				</td>\n";
					}
					else {
						$HTML .= "				<td align=\"right\">
					<input id=\"priceorig_".$srcRefItem."\" name=\"priceorig_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"".$orig."\"/>".$sOrig."
				</td>
				<td align=\"right\" style=\"color:$adjColor\">
					<input id=\"price_".$srcRefItem."\" name=\"price_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"".$price."\"/>
					<img src=\"".$root_path."images/charity.gif\" align=\"absmiddle\" />
				</td>
				<td align=\"right\">
					<input id=\"qty_".$srcRefItem."\" name=\"qty_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"".$rowDetails["quantity"]."\"/>".($rowDetails["quantity"]>1 ? ('x'.$rowDetails["quantity"]) : "&nbsp;")."
				</td>
				<td align=\"right\">
					<input id=\"totalorig_".$srcRefItem."\" name=\"totalorig_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"$totalorig\"/>".money_format("%(!.2i",$totalorig)."
				</td>
				<td align=\"right\" style=\"color:$adjColor\">
					<input id=\"total_".$srcRefItem."\" name=\"total_".$srcRef."[]\" srcDept=\"".$sDept."\" refNo=\"".$sRefNo."\" itemID=\"".$rowDetails['item_no']."\" type=\"hidden\" value=\"$total\"/>
					<img src=\"".$root_path."images/charity.gif\" align=\"absmiddle\" />
				</td>\n";
					}
				}
				$HTML .= "
			</tr>";
				$count++;
			}

			if (!is_null($limit)) {
				$subtotal = $limit;
			}
			$HTML .= "
		</tbody>
		<tfoot>
			<tr>
				<th colspan=\"2\" align=\"left\" nowrap=\"nowrap\"><span class=\"segLink\" style=\"font-size:10px\" onclick=\"toggleTBody('list_".$srcRef."')\">Hide/Show details</span></th>
				<th align=\"left\" colspan=\"2\" nowrap=\"nowrap\">Items (<span id=\"items_".$srcRef."\">".$count."</span>)</th>
				<th align=\"right\" nowrap=\"nowrap\">Orig Subtotal:</th>
				<th align=\"left\" nowrap=\"nowrap\" style=\"font-weight:normal\">
					<input type=\"hidden\" id=\"subtotal_orig_".$srcRef."\" name=\"subtotal_orig_".$srcRef."\" value=\"$subtotalorig\"/>
					<span id=\"show_subtotal_orig_".$srcRef."\" >".money_format("%(!.2i",$subtotalorig)."</span>
				</th>
				<th align=\"right\" nowrap=\"nowrap\">Adj Subtotal:</th>
				<th align=\"left\" nowrap=\"nowrap\" style=\"font-weight:normal\">
					<input type=\"hidden\" id=\"charity_".$srcRef."\" name=\"charity_".$srcRef."\" value=\"$limit\"/>
					<input type=\"hidden\" id=\"subtotal_".$srcRef."\" name=\"subtotal_".$srcRef."\" value=\"$subtotal\"/>\n".
					(!is_null($limit) ? 					
					"					<span style=\"font-weight:bold;color:yellow\" id=\"show_subtotal_".$srcRef."\" >".money_format("%(!.2i",$subtotal)."</span>
					<img src=\"".$root_path."images/charity.gif\" align=\"absmiddle\" />" :
					"					<span id=\"show_subtotal_".$srcRef."\" >".money_format("%(!.2i",$subtotal)."</span>")."
				</th>
			</tr>
		</tfoot>
	</table>
</div>";
		}	# if-then
	} # for-do
	$smarty->assign('sRequests',$HTML);
	$smarty->assign('sHospitalServiceDiscount','<span class="segLink" onclick="">view discount<img align="absmiddle" src="'.$root_path.'images/cashier_discount_small.gif"/></span>');
	$smarty->assign('sHospitalServiceRemoveAll','<span class="segLink" onclick="">remove all<img align="absmiddle" src="'.$root_path.'images/cashier_delete_small.gif"/></span>');
	$smarty->assign('sHospitalServiceRemove','<span class="segLink" onclick="">remove <img align="absmiddle" src="'.$root_path.'images/cashier_delete_small.gif"/></span>');
	$smarty->assign('sHospitalServiceToggle','<span class="segLink" onclick="">toggle<img align="absmiddle" src="'.$root_path.'images/cashier_checkbox.gif"/></span>');
	$smarty->assign('sOtherHospitalServices',"
				<tr>
					<td colspan=\"10\">Services list is currently empty...</td>
				</tr>");

	if ($_POST['submitted'] && !$saveok) {
	}
	else {
		if ($_GET['or']) {
			$request_name = $pay_info['or_name'];
			$request_address = $pay_info['or_address'];
		}
	}

	$smarty->assign('sORNo','<div style="white-space:nowrap"><input class="jedInput" id="orno" name="orno" type="text" size="15" value="'.$pay_info['or_no'].'"/></div>');

	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	if ($pay_info['or_date']) {
		$curDate = date($dbtime_format, strtotime($pay_info['or_date']));
		$curDate_show = date($fulltime_format, strtotime($pay_info['or_date']));
	}
	else {
		$curDate = date($dbtime_format);
		$curDate_show = date($fulltime_format);
	}
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
	
	$smarty->assign('sORName','<input class="jedInput" id="orname" name="orname" type="text" size="30" readonly="readonly" value="'.$request_name.'"/>');
	$smarty->assign('sORAddress','<textarea class="jedInput" id="oraddress" name="oraddress" cols="27" readonly="readonly" rows="2">'.$request_address.'</textarea>');
	$smarty->assign('sOREncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$pay_info["encounter_nr"].'"/>');
	$smarty->assign('sOREncID','<input id="pid" name="pid" type="hidden" value="'.$pay_info["pid"].'"/>');
	
	$smarty->assign('sORName','<input class="jedInput" id="orname" name="orname" type="text" size="30" '.(($pay_info['pid'])?' readonly="readonly"':'').' value="'.$request_name.'"/>');
	$smarty->assign('sORAddress','<textarea class="jedInput" id="oraddress" name="oraddress" cols="27" rows="2" '.(($pay_info['pid'])?' readonly="readonly"':'').'>'.$request_address.'</textarea>');
	$smarty->assign('sOREncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$pay_info["encounter_nr"].'"/>');
	$smarty->assign('sOREncID','<input id="pid" name="pid" type="hidden" value="'.$pay_info["pid"].'"/>');
	$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" '.(($pay_info['pid'])?'':' disabled="disabled"').' />');
	#$smarty->assign('sSWClass','<div style="margin-top:5px"><span style="font:bold 11px Tahoma">Classification: </span><span id="sw-class" style="font:bold 14px Arial;color:#006633">'.($pay_info['discountid'] ? $_POST['discountid'] : 'None').'</span></div>');	
	
	$var_arr = array(
		"var_pid"=>"pid",
		"var_encounter_nr"=>"encounter_nr",
		"var_name"=>"orname",
		"var_addr"=>"oraddress",
		"var_clear"=>"clear-enc"
	);
	$vas = array();
	foreach($var_arr as $i=>$v) {
		$vars[] = "$i=$v";
	}
	$var_qry = implode("&",$vars);
	
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="overlib(
        OLiframeContent(\''.$root_path."modules/registration_admission/seg-select-enc.php?$var_qry&var_include_enc=0',".
				'700, 400, \'fSelEnc\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); return false;"
       onmouseout="nd();" />');


	$smarty->assign('sAmountTendered','<input class="jedInput" id="amount_tendered" name="amount_tendered" type="text" size="20" value="'.number_format($pay_info['amount_tendered'],2,'.','').'" onfocus="amtTenderedOnBlurFocusHandle(this)" onblur="amtTenderedOnBlurFocusHandle(this)" />');	
	$smarty->assign('sRemarks','<textarea class="jedInput" name="remarks" cols="25" rows="2" style="float:left; font-size:12px; font-weight:normal;">'.htmlentities($pay_info['remarks']).'</textarea>');
	
/*
	$types = array();
	$disallowed_types = array(
		4, // Payward
		7, // Consignment
		8, // Sub-account
		11, // City Aid
		12 // PHIC

	);
	$result = $sClass->getAccountTypes(NULL,$disallowed_types);
	if ($result) {
		while ($row=$result->FetchRow()) $types[] = $row;
	}
	
	$subtypes = array();
	$result = $sClass->getSubAccountTypes();
		if ($result) {
		while ($row=$result->FetchRow()) {
			if (!$subtypes[$row['parent_type']]) $subtypes[$row['parent_type']] = array();
				$subtypes[$row['parent_type']][] = $row;
		}
	}
	
	$typeHTML = "";
	foreach ($types as $type) {
		$typeHTML.= '						<optgroup label="'.$type['name_long'].'">';
		if (is_array($subtypes[$type['type_id']])) {
			foreach ($subtypes[$type['type_id']] as $subtype) {
				$checked=strtolower($subtype['type_id'])==strtolower($_REQUEST['type']) ? 'selected="selected"' : "";
				$typeHTML.="							<option value=\"".$subtype["type_id"]."\" $checked>".$subtype['name_long']."</option>\n";
				$count++;
			}
		}
	}

	$typeHTML = "<select class=\"jedInput\" id=\"account_type\" name=\"account_type\" onchange=\"if (warnClear()) { emptyTray(); } else return false;\" previousValue=\"$index\">\n".
		"<option value=\"\" selected=\"selected\">- Select one -</option>\n".
		$typeHTML. 
		"					</select>";
	$smarty->assign('sSelectAccountType',$typeHTML);
*/

	/* Check options */
	$chk_disabled = $pay_info['check_or_no'] ? '' : 'disabled="disabled"';
	$smarty->assign('sCheckOption','<input class="jedInput" id="chkcheck" name="chkcheck" type="checkbox" onchange="enableInputChildren(\'check-details\',this.checked)" '.($pay_info['check_or_no'] ? 'checked="checked"' : '').'/><label class="jedInput" for="chkcheck">Use check</label>');
	$smarty->assign('sCheckNo','<input class="jedInput" id="checkno" name="checkno" type="text" size="15" value="'.$pay_info['check_no'].'" '.$chk_disabled.' />');
	$smarty->assign('sCheckDate','<input class="jedInput" id="checkdate" name="checkdate" type="text" size="15" value="'.$pay_info['check_date'].'" '.$chk_disabled.' />');
	$smarty->assign('sCheckBankName','<input class="jedInput" id="checkbank" name="checkbank" type="text" size="30" value="'.$pay_info['check_bank_name'].'" '.$chk_disabled.' />');
	$smarty->assign('sCheckPayee','<input class="jedInput" id="checkpayee" name="checkpayee" type="text" size="30" value="'.$pay_info['check_name'].'" '.$chk_disabled.' />');	
	$smarty->assign('sCheckAmount','<input class="jedInput" id="checkamount" name="checkamount" type="text" size="15" value="'.$pay_info['check_amount'].'" '.$chk_disabled.' />');	

	/* Credit Card */
	$crd_disabled = $pay_info['card_or_no'] ? '' : 'disabled="disabled"';
	$smarty->assign('sCardOption','<input class="jedInput" id="chkcard" name="chkcard" type="checkbox" onchange="enableInputChildren('."'card-details'".',this.checked)" '.($pay_info['check_or_no'] ? 'checked="checked"' : '').'/><label class="jedInput" for="chkcard">Use Card</label>');
	$smarty->assign('sCardNo','<input class="jedInput" id="cardno" name="cardno" type="text" size="15" value="'.$pay_info['card_no'].'" style="" '.$crd_disabled.' />');
	$smarty->assign('sCardIssuingBank','<input class="jedInput" id="cardbank" name="cardbank" type="text" size="30" value="'.$pay_info['card_bank_name'].'" '.$crd_disabled.' />');
	$smarty->assign('sCardBrand','<input class="jedInput" id="cardbrand" name="cardbrand" type="text" size="30" value="'.$pay_info['card_brand'].'" '.$crd_disabled.' />');
	$smarty->assign('sCardName','<input class="jedInput" id="cardname" name="cardname" type="text" size="30" value="'.$pay_info['card_name'].'" '.$crd_disabled.' />');
	$smarty->assign('sCardExpiryDate','<input class="jedInput" id="cardexpdate" name="cardexpdate" type="text" size="10" value="'.$pay_info['card_expiry_date'].'" '.$crd_disabled.' />');
	$smarty->assign('sCardSecurityCode','<input class="jedInput" id="cardcode" name="cardcode" type="text" size="5" value="'.$pay_info['card_security_code'].'" '.$crd_disabled.' />');
	$smarty->assign('sCardAmount','<input class="jedInput" id="cardamount" name="cardamount" type="text" size="15" value="'.$pay_info['card_amount'].'" '.$crd_disabled.' />');	


	$smarty->assign('sBtnAddExistingRequest','<input class="jedInput" type="button" value="Add existing request" />');
	$smarty->assign('sBtnAddMiscFees','<input class="jedInput" type="button" value="Other hospital services" />');
	
	$smarty->assign('sBtnAddMiscFees','<input class="jedInput" type="button" value="Other hospital services" 
       onclick="return overlib(
        OLiframeContent(\'seg-cashier-hospital-services.php\', 600, 340, \'fMiscFees\', 1, \'auto\'),
        WIDTH,600, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Add Hospital Service\',
        MIDX,0, MIDY,0, 
        STATUS,\'Other hospital services\');"
       onmouseout="nd();" />');

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}


$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&ref='.$sRefNo.'&dept='.$sDept.'&or='.$_GET['or'].'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
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

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/his_cancel_button.gif" align="absmiddle" />');
$smarty->assign('sContinueButton','<img class="segSimulatedLink" src="'.$root_path.'images/his_process_button.gif" align="absmiddle" onclick="if (confirm(\'Process this payment?\')) if (validate()) document.inputform.submit()" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_main.tpl');
$smarty->display('common/mainframe.tpl');

?>