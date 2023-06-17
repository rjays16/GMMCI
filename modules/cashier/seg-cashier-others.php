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
$breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND;
$imgpath=$root_path."pharma/img/";
$thisfile='seg-cashier-others.php';

	global $db;

	include_once($root_path."include/care_api_classes/class_cashier_service.php");
	include_once($root_path."include/care_api_classes/class_cashier.php");
	$cClass = new SegCashier();
	$sClass = new SegCashierService();
	
	# Note: it is advisable to load this after the inc_front_chain_lang.php so
	# that the smarty script can use the user configured template theme	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

	# Saving
	if (isset($_POST["submitted"])) {

		$items = $_POST['items'];
		if (is_array($items)) {
			$ref_arr = array();
			$src_arr = array();
			$qty_arr = array();
			$svc_arr = array();
			$amt_arr = array();
			
			$total_due = 0;
			foreach ($items as $i=>$item) {
				$src_arr[] = 'OTHER';
				$ref_arr[] = '0000000000';
				$qty_arr[] = $_POST['qty'][$i];
				$svc_arr[] = $item;
				$amt_arr[] = $_POST['price'][$i] * $_POST['qty'][$i];
				$total_due += $_POST['price'][$i] * $_POST['qty'][$i];
			}
		}
		else {
		}
	

		$data = array(
			'or_no' => $_POST['orno'],
			'or_date' => $_POST['ordate'],
			'or_name' => $_POST['orname'],
			'or_address' => $_POST['oraddress'],
			'account_type' => $_POST['account_type'],
			'encounter_nr' => $_POST['encounter_nr'],
			'pid' => $_POST['pid'],
			'amount_tendered' => $_POST['amount_tendered'],
			'amount_due' => $total_due,
			'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
			'remarks' => $_POST['remarks'],
			'create_id'=>$_SESSION['sess_temp_userid'],
			'modify_id'=>$_SESSION['sess_temp_userid'],
			'modify_time'=>date('YmdHis'),
			'create_time'=>date('YmdHis')
		);
		$ORNo = $data['or_no'];

		#$saveok=$cClass->CreatePayment($ORNo, $ORDate, $AmtTendered, $Remarks);		
		#$saveok = TRUE;
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
			
			#die("SRC:".print_r($src_arr, true) . "<hr>REF:" . print_r($ref_arr, true) . "<hr>SVC:" . print_r($svc_arr, true) . "<hr>QTY:" . print_r($qty_arr, true) . "<hr>AMT:" . print_r($amt_arr, true) . "<hr>");
			if (count($src_arr) > 0) {
				$cClass->AttachMultipleRequestsWithQty($ORNo, $ref_arr, $src_arr, $svc_arr, $qty_arr, $amt_arr);
			}
			else {
				# No requests to process
			}
		}
		else {
			# Payment not saved 
		}
	}

 $smarty->assign('sRootPath',$root_path);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Cashier::Other services");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Cashier::Other services");

 # Assign Body Onload javascript code
 $onLoadJS="onload=\"refreshTotal(); 

 	shortcut.add('CTRL+A',openServices,
		{
			'type':'keypress',
			'propagate':false,
		}
	); 

 	shortcut.add('CTRL+O',openServices,
		{
			'type':'keypress',
			'propagate':false,
		}
	); 
	
 	shortcut.add('F3', enterAmountTendered,
		{
			'type':'keydown',
			'propagate':false,
		}
	); 
	
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
<script type="text/javascript" src="js/cashier-services.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var prevAccount=0;
	var trayItems = 0;

	function warnClear() {
		var items = document.getElementsByName('items[]');
		if (items.length == 0) return true;
		else return confirm('Performing this action will clear the list. Do you wish to continue?');
	}

	function openServices() {
		return overlib(OLiframeContent('seg-cashier-hospital-services.php?type='+$('account_type').options[$('account_type').selectedIndex].value, 600, 340, 'fMiscFees', 0, 'auto'),
			WIDTH,600, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTION,'Add Hospital Service', 
			MIDX,0, MIDY,0,
   		STATUS,'Other hospital services');
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
		if (!$('orname').value) {
			alert('Please enter the payee name or the patient record for this payment...')
			$('orname').focus()
			return false
		}

		if (!$('orno').value) {
			alert('Please enter the OR#')
			$('orno').focus()
			return false
		}

		if (!$('amount_tendered').value) {
			alert('Please enter a valid amount...')
			$('amount_tendered').focus()
			return false
		}
		
		var items = document.getElementsByName('items[]')
		if (items.length==0) {
			alert('Item list is empty...')
			return false;
		}

		var prices = document.getElementsByName('price[]')
		var qty = document.getElementsByName('qty[]')
		for (var i=0;i<prices.length;i++) {
			if (!prices[i].value || isNaN(parseFloatEx(prices[i].value))) {
				alert('Invalid item price...')
				prices[i].focus()
				prices[i].select()
				return false
			}
		}
		for (var i=0;i<qty.length;i++) {
			if (!qty[i].value || isNaN(qty[i].value)) {
				alert('Invalid item quantity...')
				qty[i].focus()
				qty[i].select()
				return false
			}
		}
		return true
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
				'sMessageHeader' => "Pay request successfully processed..."
			);
			
			foreach ($assignArray as $i=>$v)
				$smarty->assign($i, $v);
			
			$sBreakImg ='close2.gif';
			$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'gui/img/control/default/en/en_close2.gif" align="absmiddle" />');
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
	
$smarty->assign('sMessageHeader',"Pay request successfully processed...");


/* Request List */
	$submitted = isset($_POST["submitted"]);
	$HTML = "";
	$smarty->assign('sRequests',$HTML);

	$types = array();
	$result = $sClass->getAccountTypes();
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
	$count = 0;
	$index = 0;
	foreach ($types as $type) {
		$typeHTML.= '						<optgroup label="'.$type['name_long'].'">';
		if (is_array($subtypes[$type['type_id']])) {
			foreach ($subtypes[$type['type_id']] as $subtype) {
				$checked=strtolower($subtype['type_id'])==strtolower($_REQUEST['type']) ? 'selected="selected"' : "";
				$typeHTML.="							<option value=\"".$subtype["type_id"]."\" $checked>".$subtype['name_long']."</option>\n";
				$count++;
				if ($checked) $index=$count;
			}
		}
/*
		else {
			$checked=strtolower($subtype['type_id'])==strtolower($_REQUEST['type']) ? 'selected="selected"' : "";
			$typeHTML.="							<option value=\"".$type["type_id"]."\" $checked>".$type['name_long']."</option>\n";
		}
		$typeHTML.= '						</optgroup>'; */
	}

	$typeHTML = "<select class=\"jedInput\" id=\"account_type\" name=\"account_type\" onchange=\"if (warnClear()) { emptyTray(); this.setAttribute('prevValue',this.selectedIndex); } else this.selectedIndex=this.getAttribute('prevValue');\" prevValue=\"$index\">\n".
		$typeHTML. 
		"					</select>";
	$smarty->assign('sSelectAccountType',$typeHTML);

	$smarty->assign('sHospitalServiceAdd','<img class="segSimulatedLink" src="'.$root_path.'images/his_additems_button.gif" align="absmiddle" '.
		'onclick="openServices()">');
	$smarty->assign('sHospitalServiceClear','<img class="segSimulatedLink" src="'.$root_path.'images/his_clear_button.gif" align="absmiddle" onclick="if (confirm(\'Clear the list?\')) clearList()"/>');
	#$smarty->assign('sHospitalServiceRemove','<img class="segSimulatedLink" src="'.$root_path.'images/his_remove_button.gif" align="absmiddle" />');

/*		
	$smarty->assign('sHospitalServiceDiscount','<span class="segLink" onclick="">view discount<img align="absmiddle" src="'.$root_path.'images/cashier_discount_small.gif"/></span>');
	$smarty->assign('sHospitalServiceRemoveAll','<span class="segLink" onclick="">remove all<img align="absmiddle" src="'.$root_path.'images/cashier_delete_small.gif"/></span>');
	
	$smarty->assign('sHospitalServiceToggle','<span class="segLink" onclick="">toggle<img align="absmiddle" src="'.$root_path.'images/cashier_checkbox.gif"/></span>');
*/
	$smarty->assign('sOtherHospitalServices',"				<tr>
					<td colspan=\"10\">Services list is currently empty...</td>
				</tr>");

	$smarty->assign('sORNo','<input class="jedInput" id="orno" name="orno" type="text" size="15" value="'.$_POST['orno'].'"/>');

	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";
	$dbdate_format = "Y-m-d";
	$fulldate_format = "F j, Y";
	
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

	$smarty->assign('sORName','<input class="jedInput" id="orname" name="orname" type="text" size="30" value="'.$_POST['orname'].'"/>');
	$smarty->assign('sORAddress','<textarea class="jedInput" id="oraddress" name="oraddress" cols="27" rows="2">'.$_POST['oraddress'].'</textarea>');
	$smarty->assign('sAmountTendered','<input class="jedInput" id="amount_tendered" name="amount_tendered" type="text" size="20" value="'.$_POST['amount_tendered'].'" onblur="amtTenderedOnBlurFocusHandle(this)"/>');	
	$smarty->assign('sRemarks','<textarea class="jedInput" name="remarks" cols="20" rows="2" style="float:left; font-size:12px; font-weight:normal;">'.$_POST['remarks'].'</textarea>');
	$smarty->assign('sOREncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
	$smarty->assign('sOREncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
	$smarty->assign('sORDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
	$smarty->assign('sORDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');
	$smarty->assign('sClearEnc','<input class="jedInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" '.(($_POST['pid'])?'':' disabled="disabled"').' />');
	$smarty->assign('sSWClass','<div style="margin-top:5px"><span style="font:bold 11px Tahoma">Classification: </span><span id="sw-class" style="font:bold 14px Arial;color:#006633">'.($_POST['discountid'] ? $_POST['discountid'] : 'None').'</span></div>');	

	$var_arr = array(
		"var_pid"=>"pid",
		"var_encounter_nr"=>"encounter_nr",
		"var_discountid"=>"discountid",
		"var_discount"=>"discount",
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


		/* Check options */
	$smarty->assign('sCheckOption','<input class="jedInput" id="chkcheck" name="chkcheck" type="checkbox" onchange="enableInputChildren('."'check-details'".',this.checked)"/><label class="jedInput" for="chkcheck">Use check</label>');
	$smarty->assign('sCheckNo','<input class="jedInput" id="checkno" name="checkno" type="text" size="20" value="'.$_POST['checkno'].'" disabled="disabled" />');
	$smarty->assign('sCheckDate','
		<span id="show_checkdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'
			.($submitted ? date($fulldate_format,strtotime($_POST['checkdate'])) : date($fulldate_format)).'</span>
		<input class="jedInput" name="checkdate" id="checkdate" type="hidden" value="'.($submitted ? date($dbdate_format,strtotime($_POST['checkdate'])) : date($dbdate_format)).'" style="font:bold 12px Arial">
		<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="checkdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">
		<script type="text/javascript">
			Calendar.setup ({
				displayArea : "show_checkdate",
				inputField : "checkdate",
				ifFormat : "%Y-%m-%d",
				daFormat : "	%B %e, %Y",
				showsTime : false, 
				button : "checkdate_trigger", 
				singleClick : true,
				step : 1
			});
		</script>
	');
	$smarty->assign('sCheckBankName','<input class="jedInput" id="checkbank" name="checkbank" type="text" size="30" value="'.$_POST['checkbank'].'" disabled="disabled" />');
	$smarty->assign('sCheckPayee','<input class="jedInput" id="checkpayee" name="checkpayee" type="text" size="30" value="'.$_POST['checkpayee'].'" disabled="disabled" />');	
	$smarty->assign('sCheckAmount','<input class="jedInput" id="checkamount" name="checkamount" type="text" size="15" value="'.$_POST['checkamount'].'" disabled="disabled" />');	

	/* Credit Card */
	$smarty->assign('sCardOption','<input class="jedInput" id="chkcard" name="chkcard" type="checkbox" onchange="enableInputChildren('."'card-details'".',this.checked)"/><label class="jedInput" for="chkcard">Use Card</label>');
	$smarty->assign('sCardNo','<input class="jedInput" id="cardno" name="cardno" type="text" size="15" value="'.$_POST['cardno'].'" style="" disabled="disabled" />');
	$smarty->assign('sCardIssuingBank','<input class="jedInput" id="cardbank" name="cardbank" type="text" size="30" value="'.$_POST['cardbank'].'" disabled="disabled" />');
	$smarty->assign('sCardBrand','<input class="jedInput" id="cardbrand" name="cardbrand" type="text" size="30" value="'.$_POST['cardbrand'].'" disabled="disabled" />');
	$smarty->assign('sCardName','<input class="jedInput" id="cardname" name="cardname" type="text" size="30" value="'.$_POST['cardname'].'" disabled="disabled" />');
	$smarty->assign('sCardExpiryDate','
		<span id="show_cardexpdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'
			.($submitted ? date($fulldate_format,strtotime($_POST['cardexpdate'])) : date($fulldate_format)).'</span>
		<input class="jedInput" name="cardexpdate" id="cardexpdate" type="hidden" value="'.($submitted ? date($dbdate_format,strtotime($_POST['cardexpdate'])) : date($dbdate_format)).'" style="font:bold 12px Arial">
		<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="cardexpdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">
		<script type="text/javascript">
			Calendar.setup ({
				displayArea : "show_cardexpdate",
				inputField : "cardexpdate",
				ifFormat : "%Y-%m-%d",
				daFormat : "	%B %e, %Y",
				showsTime : false, 
				button : "cardexpdate_trigger", 
				singleClick : true,
				step : 1
			});
		</script>
	');
	$smarty->assign('sCardSecurityCode','<input class="jedInput" id="cardcode" name="cardcode" type="text" size="5" value="'.$_POST['cardcode'].'" disabled="disabled" />');
	$smarty->assign('sCardAmount','<input class="jedInput" id="cardamount" name="cardamount" type="text" size="15" value="'.$_POST['cardamount'].'" disabled="disabled" />');	

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

	$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&ref='.$sRefNo.'&dept='.$sDept.'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
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
	<input type="hidden" name="amount_total" value="<?= $_POST['amount_total'] ?>">
	
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/his_cancel_button.gif" align="absmiddle" onclick="window.href.location=\''.$breakfile.'\'"/>');
$smarty->assign('sContinueButton','<img class="segSimulatedLink" src="'.$root_path.'images/his_process_button.gif" align="absmiddle" onclick="if (confirm(\'Process this payment?\')) if (validate()) document.inputform.submit()" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','cashier/cashier_others.tpl');
$smarty->display('common/mainframe.tpl');

?>