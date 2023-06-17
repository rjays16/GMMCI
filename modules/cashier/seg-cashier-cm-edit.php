<?php

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require($root_path."modules/cashier/ajax/memo.common.php");

global $db;
include_once($root_path."include/care_api_classes/class_credit_memo.php");
$cm = new SegCreditMemo();

include_once($root_path."include/care_api_classes/class_person.php");
$person = new Person();
#added by janken 10/14/2014 for including the curl class 
require_once($root_path.'include/care_api_classes/curl/class_curl.php');
$curl = new Rest_Curl();

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');


$Nr = $_GET['nr'];

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

	//Added By Jarel 09/08/2014
	if ($saveok) {
		foreach ($_POST["items"] as $i=>$v) {
			if($person->hasCashierSaveInLedger($_POST["orno"][$i])){
				if($_POST["src"][$i]=='FB'){
					$data_ledger['pid'] = $data['pid'];
				   	$data_ledger['encounter_nr'] = $data['encounter_nr'];
				   	$data_ledger['bill_nr'] = $_POST["items"][$i];
				   	$data_ledger['entry_type'] = 'credit';
				   	$data_ledger['amount'] = $_POST["price"][$i];
				   	$data_ledger['pay_type'] = 'memo';
				   	$data_ledger['description'] = $Nr;
					$saveok = $person->savePersonLedgerInfo($data_ledger);
				}
			}
		}
	}
	//Added By Jarel 09/08/2014

	if ($saveok) {
		# Bulk write memo items
		$bulk = array();
		foreach ($_POST["items"] as $i=>$v) {
			$bulk[] = array($_POST["orno"][$i],$_POST["src"][$i],$_POST["ref"][$i],$_POST["items"][$i],$_POST["name"][$i],$_POST["desc"][$i],$_POST["refund"][$i],$_POST["price"][$i]);
		}
		$cm->clearMemoItems($Nr);  
		$cm->addMemoItems($Nr, $bulk);
        #added by VAS 09-10-2012
        $cm->FlagItems($bulk);
		#$smarty->assign('sWarning','<div style="margin:6px">Credit memo details successfully saved!</div>');
                #added by janken 10/14/2014 for recording the credit memo trans to fis
		$curl->creditMemoAdd($Nr, $_POST['issue_date']);
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
if ($_GET['nr'])
	$smarty->assign('sToolbarTitle',"Cashier :: Credit memo :: Edit memo");
else
	$smarty->assign('sToolbarTitle',"Cashier :: Credit memo :: Create memo");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
if ($_GET['nr'])
	$smarty->assign('sWindowTitle',"Cashier::Edit credit memo");
else
	$smarty->assign('sWindowTitle',"Cashier::Create credit memo");

# Assign Body Onload javascript code
if ($view_only) { 		
	$onLoadJS='onload="eraseCookie(\'__cm_ck\');'.($Nr ? 'xajax_populate_items(\''.$Nr.'\',1)' : '').'"';
}
else {
 $onLoadJS='onload="eraseCookie(\'__cm_ck\');'.($Nr ? 'xajax_populate_items(\''.$Nr.'\')' : '').'"';
}
$smarty->assign('sOnLoadJs',$onLoadJS);


# Collect javascript code

ob_start();
	 # Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/cashier-memo.js?t=<?=time()?>"></script>
<script type="text/javascript">
var trayItems = 0;

// Preload images
var img1 = new Image();
img1.src = "<?= $root_path ?>/images/ajax_bar.gif";

function init() {
    
    <?php
    if ( !$_GET['reference'] && !$_GET['ref'] && !$_GET['or'] ) {
    ?>
            personSelect();
    <?php
    }
    $var_arr = array(
	"var_pid"=>"pid",
	"var_encounter_nr"=>"encounter_nr",
	"var_name"=>"memo_name",
	"var_addr"=>"memo_address",
	"var_clear"=>"clear-enc"
    );
    $vas = array();
    foreach($var_arr as $i=>$v) {
            $vars[] = "$i=$v";
    }
    $var_qry = implode("&",$vars);
    ?>
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
	if (!document.getElementsByName('items[]').length) {
		alert("No refundable items specified...");
		return false;
	}
	
	if (!$('pid').value) {
		alert("Please select payor...");
		return false;
	}
	
	if (!$('personnel').value) {
		alert("Please select a collection officer...");
		$('personnel').focus();
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
/*ADDED BY: DOMMIE*/
function personSelect() {
	var searchScript;
	if ($('search-walkin').checked)
        {
		searchScript = "seg-select-walkin.php";
        }
        else
        {
		searchScript = "seg-select-enc.php";     
        }  
        overlib(
			OLiframeContent('<?=$root_path?>modules/registration_admission/'+searchScript+'?<?=$var_qry?>&var_include_enc=0',
			700, 400,'fSelEnc', 0,'auto'),
			WIDTH,700, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT,'<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTION,'Select registered person',
			MIDX,0, MIDY,0, 
			STATUS,'Select registered person'); return false;
}

document.observe("dom:loaded", function() {
	// initially hide all containers for tab content
	init();
	$$('input[type=text]').each(function(o)  {
		o.observe('keypress', function(event){
			if (event.keyCode == Event.KEY_RETURN) {
				this.blur();
				Event.stop(event);
				return false;
			}
			return true;
		}.bindAsEventListener(o))
	});
	refreshTotal();
});
/*	
if (document.addEventListener) {
	document.addEventListener("DOMContentLoaded", init, false);
}
*/
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

if ($_POST['submitted'] && !$saveok) {
	$pay_info = $_POST;
}
else {
	if (!$_GET['nr']) {
		$pay_info['memo_nr'] = $cm->getLastNr();
	}
	else {
		$pay_info = $cm->getMemoInfo($Nr);
	}
}

$smarty->assign('sMemoNr','<input class="jedInput" id="memo_nr" name="memo_nr" type="text" size="15" value="'.$pay_info['memo_nr'].'" readonly="readonly" />');
$smarty->assign('sResetNr','<input class="segButton" type="button" value="Reset" style="font:bold 11px Arial" '.($_GET['nr'] ? 'disabled="disabled"' : '').' onclick="xajax_reset_nr()"/>');
$smarty->assign('sORWalkin','<input class="segInput" id="search-walkin" type="checkbox" />');
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
$smarty->assign('sIssueDate','<span id="show_issuedate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['ordate'])) : $curDate_show).'</span><input class="jedInput" name="issue_date" id="issue_date" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['issue_date'])) : $curDate).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="issuedate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_issuedate\",
		inputField : \"issue_date\",
		ifFormat : \"%Y-%m-%d %H:%M\", 
		daFormat : \"	%B %e, %Y %I:%M%P\", 
		showsTime : true, 
		button : \"issuedate_trigger\", 
		singleClick : true,
		step : 1
	});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);	

$smarty->assign('sMemoName','<input class="jedInput" id="memo_name" name="memo_name" type="text" size="30" readonly="readonly" value="'.$pay_info['memo_name'].'"/>');
$smarty->assign('sMemoAddress','<textarea class="jedInput" id="memo_address" name="memo_address" cols="27" rows="2" readonly="readonly">'.$pay_info['memo_address'].'</textarea>');
$smarty->assign('sMemoEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$pay_info["encounter_nr"].'"/>');
$smarty->assign('sMemoEncID','<input id="pid" name="pid" type="hidden" value="'.$pay_info["pid"].'"/>');
if ($_GET['nr'])
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" disabled="disabled"/>');
else
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" '.(($pay_info['pid'])?'':' disabled="disabled"').' />');


ob_start();
?>
<select class="jedInput" name="personnel" id="personnel">
									<option value="" style="font-weight:bold">-- Select personnel --</option>
<?php
	$sql = "SELECT u.name,u.login_id,u.personell_nr,a.location_nr\n".
		"FROM care_users AS u\n".
			"LEFT JOIN care_personell AS p ON u.personell_nr=p.nr\n".
			"LEFT JOIN care_personell_assignment AS a ON a.personell_nr=p.nr\n".
		"WHERE location_nr=170\n".
		"ORDER BY login_id";
	$cashiers = $db->Execute($sql);
	while($row=$cashiers->FetchRow()){
		$selected = ($row["login_id"] == $pay_info["personnel"]) ? 'selected=""' : "";
		echo "									<option value=\"".$row['login_id']."\" $selected>".$row['name']."</option>\n";
	}
?>
								</select>
<?php

$temp = ob_get_contents();
ob_end_clean();
$smarty->assign('sPersonnel',$temp);


#$smarty->assign('sSWClass','<div style="margin-top:5px"><span style="font:bold 11px Tahoma">Classification: </span><span id="sw-class" style="font:bold 14px Arial;color:#006633">'.($pay_info['discountid'] ? $_POST['discountid'] : 'None').'</span></div>');	



if ($_GET['or']) {
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="opacity:0.2">');
}
else {
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
		 onmouseout="nd();" onclick="personSelect();"/>');
}

$smarty->assign('sTotalRefund','<input name="total_refund" id="total_refund" type="hidden" value="'.$pay_info['refund_amount'].'"/><input class="segClearInput" id="total_refund_show" type="text" size="20" value="'.number_format($pay_info['refund_amount'],2).'" readonly="readonly" style="font:bold 16px Arial;color:#000066;padding:0px 2px;border:1px dashed #808080; text-align:right"/>');
$smarty->assign('sRemarks','<textarea class="jedInput" name="remarks" cols="22" rows="2" style="float:left;">'.htmlentities($pay_info['remarks']).'</textarea>');

$smarty->assign('sMemoAdd','<img class="segSimulatedLink" src="'.$root_path.'images/btn_refund_items.gif" align="absmiddle" '.
	'onclick="openPayments()">');
$smarty->assign('sMemoClearAll','<img class="segSimulatedLink" src="'.$root_path.'images/his_clear_button.gif" align="absmiddle" onclick="if (confirm(\'Clear the list?\')) emptyList()"/>');
$smarty->assign('sMemoList',"				<tr>
				<td colspan=\"15\">Item list is currently empty...</td>
			</tr>");



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
$smarty->assign('sContinueButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_process_cm.gif" align="absmiddle" onclick="if (confirm(\'Process this refund entry?\')) if (validate()) document.inputform.submit()" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_memo.tpl');
$smarty->display('common/mainframe.tpl');
?>