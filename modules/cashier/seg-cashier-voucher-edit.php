<?php

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require($root_path."modules/cashier/ajax/memo.common.php");

global $db;
include_once($root_path."include/care_api_classes/class_credit_memo.php");
$cm = new SegCreditMemo();
include_once($root_path."include/care_api_classes/class_cashier.php");
$cc = new SegCashier();

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$sRefNo = $_GET['ref'];
$sDept = $_GET['dept'];

# Saving
if (isset($_POST["submitted"]) && !$_REQUEST['viewonly']) {

	$data = array(
		'memo_name'=>$_POST['memo_name'],
		'pid'=>$_POST['pid'],
		'encounter_nr'=>$_POST['encounter_nr'],
		'memo_address'=>$_POST['memo_address'],	
		'issue_date'=>$_POST['issue_date'],
		'remarks'=>$_POST['remarks'],
		'personnel'=>$_POST['personnel'],
		'refund_amount'=>$_POST['total_refund'],
		'modify_id'=>$_SESSION['sess_temp_userid'],
		'modify_time'=>date('YmdHis')
	);
	
	if ($Nr) {
		$data["history"]=$cm->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
		print_r($data);
		$cm->setDataArray($data);
		$cm->where = "memo_nr=".$db->qstr($Nr);
		$cm->useMemo();
		$saveok=$cm->updateDataFromInternalArray($Nr,FALSE);
	}
	else {
		$Nr = $cm->getLastNr();
		$data['memo_nr']=$Nr;
		$data['create_id']=$_SESSION['sess_temp_userid'];
		$data['create_time']=date('YmdHis');
		$data['history']="Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n";
		$cm->setDataArray($data);
		$cm->useMemo();
		$saveok = $cm->insertDataFromInternalArray();
		if ($saveok) {
		}
		else {
			$errorMsg = $db->ErrorMsg();
			print_r($cm->sql);
		}
	}

	if ($saveok) {
		# Bulk write memo items
		$bulk = array();
		foreach ($_POST["items"] as $i=>$v) {
			$bulk[] = array($_POST["orno"][$i],$_POST["src"][$i],$_POST["ref"][$i],$_POST["items"][$i],$_POST["name"][$i],$_POST["desc"][$i],$_POST["refund"][$i],$_POST["price"][$i]);
		}
		$cm->clearMemoItems($Nr);
		$cm->addMemoItems($Nr, $bulk);
		#$smarty->assign('sWarning','<div style="margin:6px">Credit memo details successfully saved!</div>');
	}
	else {
		$errorMsg = $db->ErrorMsg();
		if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
			$smarty->assign('sWarning','<br><strong>Error:</strong> An item with the same memo number already exists in the database.');
		else {
			if ($errorMsg)
				$smarty->assign('sWarning',"<br><strong>Error:</strong> $errorMsg");
			else
				$smarty->assign('sWarning',"<br><strong>Unknown error occurred!</strong>");
			#print_r($order_obj->sql);
		}
	}		
}

$smarty->assign('sRootPath',$root_path);

# Title in the title bar
$smarty->assign('sToolbarTitle',"Cashier :: Cash vouchers :: Add cash voucher");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Cashier :: Cash vouchers :: Add cash voucher");

# Assign Body Onload javascript code
if ($view_only) { 		
	$onLoadJS="onload=\"xajax_populate_items('$sRefNo','$sDept',1);\"";
}
else {
	$onLoadJS="onload=\"xajax_populate_items('$sRefNo','$sDept');\"";
}
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
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/cashier-voucher.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;
	
	// Preload images
	var img1 = new Image();
	img1.src = "<?= $root_path ?>images/images/ajax_bar.gif";
	
	function init() {
	}
	
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

	function validate() {

/*
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
		
*/
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

/*	
	if (document.addEventListener) {
	  document.addEventListener("DOMContentLoaded", init, false);
	}
*/
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
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
			'sMemoNr' => $Nr,
			'sMemoName' => $_POST['memo_name'],
			'sMemoAddress' => $_POST['memo_address'],
			'sIssueDate' => $_POST['issue_date'],
			'sAmountDue' => $total_due,
			'sAmountTendered' => $_POST['amount_tendered'],
			'sAmountChange' => $change,
			'sRemarks' => $_POST['remarks'],
			'sMessageHeader' => "Credit Memo details successfully saved..."
		);
		
		$items = "";
		foreach ($_POST["items"] as $i=>$v) {
			#$bulk[] = array($_POST["orno"][$i],$_POST["src"][$i],$_POST["ref"][$i],$_POST["items"][$i],$_POST["name"][$i],$_POST["desc"][$i],$_POST["refund"][$i],$_POST["price"][$i]);
			$items .= '
	<tr>
		<td class="jedPanel3" align="center">'.$_POST['orno'][$i].'</td>
		<td class="jedPanel3" align="center">'.$_POST['src'][$i].'</td>
		<td class="jedPanel3" align="center">'.$v.'</td>
		<td class="jedPanel3" align="left">'.$_POST['name'][$i].'</td>
		<td class="jedPanel3" align="right">'.number_format((float)$_POST['price'][$i],2).'</td>
		<td class="jedPanel3" align="center">'.$_POST['refund'][$i].'</td>
		<td class="jedPanel3" align="right">'.number_format((float)$_POST['refund_total'][$i],2).'</td>
	</tr>
';
		}
		$assignArray['sItems'] = $items;
		
		foreach ($assignArray as $i=>$v)
			$smarty->assign($i, $v);
		
		$sBreakImg ='close2.gif';
		$smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
		
		$printfile = $root_path.'modules/cashier/seg-cashier-cm-print.php'. URL_APPEND."&nr=".$Nr;
		$smarty->assign('sPrintButton','<img class="segSimulatedLink"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');
		
		$smarty->assign('sMainBlockIncludeFile','cashier/memo_saveok.tpl');
		$smarty->display('common/mainframe.tpl');
		exit();
	}
	else {
		$smarty->assign('sWarning',"Error processing request...<br>".
		"Error:".$db->ErrorMsg()."<br>".
		"SQL:".$cClass->sql);
	}
}

$rsInfo = $cc->GetRequestInfo($sRefNo, $sDept);
$info = $rsInfo->FetchRow();

$smarty->assign('sRefNo','<input class="jedInput" id="ref_no" name="ref_no" type="text" size="15" value="'.$sRefNo.'" disabled="disabled"/>');

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
if ($pay_info['issue_date']) {
	$curDate = date($dbtime_format, strtotime($pay_info['issue_date']));
	$curDate_show = date($fulltime_format, strtotime($pay_info['issue_date']));
}
else {
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
}
$smarty->assign('sEntryDate','<span id="show_entrydate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['ordate'])) : $curDate_show).'</span><input class="jedInput" name="entry_date" id="entry_date" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['entry_date'])) : $curDate).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="entrydate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_entrydate\",
		inputField : \"entry_date\",
		ifFormat : \"%Y-%m-%d %H:%M\", 
		daFormat : \"	%B %e, %Y %I:%M%P\", 
		showsTime : true, 
		button : \"entrydate_trigger\", 
		singleClick : true,
		step : 1
	});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);	

$smarty->assign('sVoucherName','<input class="jedInput" id="voucher_name" name="voucher_name" type="text" size="30" value="'.$info['request_name'].'" disabled="disabled"/>');
$smarty->assign('sVoucherAddress','<textarea class="jedInput" id="memo_address" name="voucher_address" cols="27" rows="2" disabled="disabled">'.$info['request_address'].'</textarea>');
$smarty->assign('sVoucherEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$pay_info["encounter_nr"].'"/>');
$smarty->assign('sVoucherEncID','<input id="pid" name="pid" type="hidden" value="'.$pay_info["pid"].'"/>');
if ($_GET['nr'])
	$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" disabled="disabled"/>');
else
	$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" '.(($pay_info['pid'])?'':' disabled="disabled"').' />');

$smarty->assign('sTotalRefund','<input name="total_refund" id="total_refund" type="hidden" value="'.$pay_info['refund_amount'].'"/><input class="segClearInput" id="total_refund_show" type="text" size="20" value="'.number_format($pay_info['refund_amount'],2).'" readonly="readonly" style="font:bold 18px Arial;color:#000066"/>');
$smarty->assign('sRemarks','<textarea class="jedInput" name="remarks" cols="22" rows="2" style="float:left;">'.htmlentities($pay_info['remarks']).'</textarea>');
$smarty->assign('sMemoList',"				<tr>
				<td colspan=\"15\">Item list is currently empty...</td>
			</tr>");
			
$sponsorHTML = '<select class="jedInput" id="sponsor-template">
	<option value="" style="font-weight:bold">--Select sponsor--</option>
';
include_once($root_path."include/care_api_classes/class_sponsor.php");
$sc = new SegSponsor();
$sponsors = $sc->get();
while($row=$sponsors->FetchRow()){
	$sponsorHTML .= "									<option value=\"".$row['sp_id']."\">".$row['sp_name']."</option>\n";
}
$sponsorHTML .= "					</select>";
$smarty->assign('sSponsorTemplate',$sponsorHTML);


$smarty->assign('sAddCoverage','<input class="jedInput" type="image" src="'.$root_path.'images/his_add_coverage.gif" align="absmiddle" onclick="addCoverage(); this.blur(); return false;" style="outline:0"/>');
$smarty->assign('sClearCoverage','<input class="jedInput" type="image" src="'.$root_path.'images/his_clear_button.gif" align="absmiddle" onclick="return false"/>');

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&nr='.$Nr.'&from='.$_GET['from'].'" method="POST" id="inputForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
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
$smarty->assign('sBreakButton','<a href="'.$breakfile.'"><img class="segSimulatedLink" src="'.$root_path.'images/his_cancel_button.gif" align="absmiddle" /></a>');
$smarty->assign('sContinueButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_process_cm.gif" align="absmiddle" onclick="if (confirm(\'Process this payment?\')) if (validate()) document.inputform.submit()" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_voucher.tpl');
$smarty->display('common/mainframe.tpl');
?>