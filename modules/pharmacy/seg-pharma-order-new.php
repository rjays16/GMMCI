<?php
$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');

	# Start Smarty templating here
 /**
 * LOAD Smarty
 */

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_order.php");
include_once($root_path."include/care_api_classes/class_encounter.php");
include_once($root_path."include/care_api_classes/class_person.php");
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');

$enc_obj = new Encounter();
$per_obj = new Person();
$order_obj = new SegOrder("pharma");
$order_obj->setupLogger();
global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}

# Title in the title bar
$smarty->assign('sToolbarTitle',"Pharmacy::New request");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Pharmacy::New request");

#added by VAN 02-06-2012
//for bloodbank only as per Mrs Angie Balayon's request 
 
if ($area=='bb'){
    $ref_source = "BB";
}

if (isset($_POST["submitted"])) {
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
		'refno'=>$order_obj->getLastNr(date("Y-m-d")),
		'encounter_nr'=>$_POST['encounter_nr'],
		'pharma_area'=>$_POST['area'] ? strtoupper($_POST['area']) : strtoupper($_POST['area2']),
		'ordername'=>$_POST['ordername'],
		'orderaddress'=>$_POST['orderaddress'],
		'orderdate'=>$_POST['orderdate'],
		'charge_type'=>$_POST['charge_type'],
		'is_cash'=>$_POST['iscash'],
		'amount_due'=>$total,
		'is_tpl'=>$_POST['is_tpl'],
		'discount'=>$_POST['discount'],
		'discountid'=>$_POST['discountid'],
		'is_urgent'=>$_POST['priority'],
		'comments'=>$_POST['comments'],
		'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
		'create_id'=>$_SESSION['sess_temp_userid'],
		'modify_id'=>$_SESSION['sess_temp_userid'],
		'modify_time'=>date('YmdHis'),
		'create_time'=>date('YmdHis')
	);
	if ($_POST['issc']) $data["is_sc"] = 1;

	if ($_POST["pid"]) {
		if (substr($_POST["pid"],0,1)=='W') {
			$data["walkin_pid"] = substr($_POST["pid"],1,strlen($_POST["pid"]));
			$data["pid"] = NULL;
		}
		else {
			$data["pid"] = $_POST["pid"];
			$data["walkin_pid"] = NULL;
		}
		$saveok = TRUE;
	} else {
		# Generate new Walk-in entry
		require_once "{$root_path}include/care_api_classes/class_walkin.php";
		$wc = new SegWalkin();

		$walkin_data = array(
			'pid' => $wc->createPID(),
			'address' => $_POST['orderaddress'],
			'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
			'create_id'=>$_SESSION['sess_temp_userid'],
			'modify_id'=>$_SESSION['sess_temp_userid'],
			'modify_time'=>date('YmdHis'),
			'create_time'=>date('YmdHis')
		);

		$data['pid'] = NULL;
		$data['walkin_pid'] = $walkin_data['pid'];

		# parse name
		$name_arr = explode(',',$_POST['ordername']);
		if (trim($name_arr[0])) {
			$walkin_data['name_last'] = trim($name_arr[0]);
		}
		if (trim($name_arr[1])) {
			$walkin_data['name_first'] = trim($name_arr[1]);
		}

		$wc->setDataArray($walkin_data);
		$errorMsg = 'Unable to save walkin data...';
		$saveok = $wc->insertDataFromInternalArray();
	}

	if ($_SESSION['sess_temp_userid'] == 'medocs')
		$order_obj->logger->debug('REFNO:'.$data['refno']);

	if ($saveok) {
		$errorMsg = 'Unable to save request data...';
		$order_obj->setDataArray($data);
		if ($_SESSION['sess_temp_userid'] == 'medocs') $order_obj->logger->info('Saving header...');
		$saveok = $order_obj->insertDataFromInternalArray();
		if ($_SESSION['sess_temp_userid'] == 'medocs') $order_obj->logger->debug('Result:'.var_export($saveok, true));
	}

	if ($saveok) {
		$errorMsg = 'Unable to clear request details...';
		if ($_SESSION['sess_temp_userid'] == 'medocs') $order_obj->logger->info('Clearing details...');
		$saveok = $order_obj->clearOrderList($data['refno']);
		if ($_SESSION['sess_temp_userid'] == 'medocs') $order_obj->logger->debug('Result:'.var_export($saveok, true));
	}
        
	if ($saveok) {
		$errorMsg = 'Unable to save request details...';
		if ($_SESSION['sess_temp_userid'] == 'medocs') $order_obj->logger->info('Saving details...');
		$saveok = $order_obj->addOrders($data['refno'],$bulk);
		if ($_SESSION['sess_temp_userid'] == 'medocs') $order_obj->logger->debug('Result:'.var_export($saveok, true));
	}
        
 //	if ($data['charge_type'] == 'PHIC' && $data['is_cash']==0 && $saveok) {
		// Set request items as compensable by PhilHealth by default when charge type is PHIC
//
//		define('__PHIC_ID__', 18); // hardcode hcare ID (temporary workaround)
//
//		foreach ($_POST["items"] as $i=>$v) {
//			$sql = "SELECT coverage FROM seg_applied_coverage\n".
//				"WHERE ref_no='T{$_POST['encounter_nr']}'\n".
//					"AND source='M'\n".
//					"AND item_code=".$db->qstr($v)."\n".
//					"AND hcare_id=".__PHIC_ID__;
//			$coverage = parseFloatEx($db->GetRow($sql)) + (parseFloatEx($_POST["prc"][$i]) * (float) $_POST["qty"][$i]);

//			$errorMsg = 'Unable to update item coverages for item #'.$v.'...';
//			$result = $db->Replace('seg_applied_coverage',
//				array(
//					'ref_no'=>"T{$_POST['encounter_nr']}",
//					'source'=>'M',
//					'item_code'=>$v,
//					'hcare_id'=>__PHIC_ID__,
//					'coverage'=>$coverage
//				),
//				array('ref_no', 'source', 'item_code', 'hcare_id'),
//				$autoquote=TRUE
//			);
//
//			$saveok = $result > 0;
//			print_r($result);
//			if (!$saveok) break;
//		}

//
//		$cc = new SegCoverage();
//		$saveok = $cc->clearReference($data['refno'], 'M');
//		if ($saveok) {
//			$errorMsg = 'Unable to update item coverages for this request...';
//
//			$ref_array = array();
//			$src_array = array();
//			$code_array = array();
//			$hcare_array = array();
//			$coverage_array = array();
//
//			foreach ($_POST["items"] as $i=>$v) {
//				$ref_array[$i] = $data['refno'];
//				$src_array[$i] = 'M';
//				$code_array[$i] = $v;
//				$hcare_array[$i] = 18; // Hard code PHIC insurance ID...edit later
//				$coverage_array[$i] = (parseFloatEx($_POST["prc"][$i]) * (float) $_POST["qty"][$i]);
//			}
//			$saveok = $cc->addCoverage( $ref_array, $src_array, $code_array, $hcare_array, $coverage_array);
//		}

//	}
        
        if ($saveok) {
            $itemsResult = $order_obj->getOrderItemsFullInfo($data['refno']);
            if ($itemsResult) {                
                $saveok = $order_obj->updateInventory($data, $itemsResult);
            }
            
            if ($saveok) {
                $oRows = "";
                if ($itemsResult) {                    
                        foreach($itemsResult as $oItem) {                                                                                  
                            $oRows .= '<tr>
                                            <td align="center" style="font:bold 11px Tahoma;color:#000080">'.$oItem['bestellnum'].'</td>
                                            <td >'.$oItem['artikelname'].'</td>
                                            <td align="right">'.number_format((float)$oItem['force_price'],2).'</td>
                                            <td align="center">'.number_format((float)$oItem['quantity']).'</td>
                                            <td align="right">'.number_format((float)$oItem['quantity']*(float)$oItem['force_price'],2).'</td>
                                    </tr>';
                        }
                        if (!$oRows) {
                                $oRows = '<tr><td colspan="10" class="segPanel3">Order list is empty...</td></tr>';
                        }
                }
                if (!$oRows) {
                        $oRows = '<tr><td colspan="10" class="segPanel3">Error reading order details from database...</td></tr>';
                }       
            }
        }

	if ($saveok) {            
                $skuobj = new SKUInventory();
                $skuobj->clearTmpTable();
                $db->CompleteTrans();                
            
		// If everything goes well
		$smarty->assign('sMsgTitle','Pharmacy request successfully saved!');
		$smarty->assign('sMsgBody','The request details have been saved into the database...');
		$sBreakImg ='close2.gif';
		$smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
		$printfile = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print-jasper&userck=$userck".'&cat=pharma&ref='.$data['refno'];
		$smarty->assign('sPrintButton','<img class="segSimulatedLink"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');

//		$Ref = $data['refno'];
//
//		$infoResult = $order_obj->getOrderInfo($Ref);
//
//		if ($infoResult)  $info = $infoResult->FetchRow();

		# Assign submitted form values
//		$smarty->assign('sSelectArea', $_REQUEST['area']);
//		$smarty->assign('sRefNo', $data['refno']);
//		$smarty->assign('sCashCharge', ($_REQUEST['iscash']=="1") ? "Cash" : "Charge");
//		$smarty->assign('sOrderDate', $_REQUEST['orderdate']);
//		$smarty->assign('sOrderName', $_REQUEST['ordername']);
//		$smarty->assign('sOrderAddress', $_REQUEST['orderaddress']);
//		$smarty->assign('sPriority',($_REQUEST['priority']=="0") ? "Normal" : "Urgent");
//		$smarty->assign('sRemarks',$_REQUEST['comments']);
                                                              
		$smarty->assign('sRefNo', $data['refno']);
		$smarty->assign('sSelectArea', $data['pharma_area']);
		$smarty->assign('sCashCharge',
			($data['is_cash']=="1" ?
				("Cash".($data['is_tpl']=="1" ? " (TPL)" : "")) :
				"Charge"));
		$smarty->assign('sOrderDate', date("F j, Y g:ia",strtotime($data['orderdate'])));
		$smarty->assign('sOrderName', $data['ordername']);
		$smarty->assign('sOrderAddress', $data['orderaddress']);
		$smarty->assign('sPriority',($data['is_urgent']=="0") ? "Routine" : "Stat");
		$smarty->assign('sRemarks',$data['comments']);
		                                                
		$smarty->assign('sItems',$oRows);

		$smarty->assign('sMainBlockIncludeFile','order/saveok.tpl');
		$smarty->display('common/mainframe.tpl');                
                
		exit;
	}
	else {
		// Some error occurred along the way
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
#$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('bShowQuickKeys',FALSE);

# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/event.simulate.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/pharmacy/js/autocomplete.js"></script>
<link rel="stylesheet" href="<?=$root_path?>modules/pharmacy/css/autocomplete.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/order-gui.js?t=<?=time()?>"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();
</script>

<script type="text/javascript" language="javascript">
	var trayItems = 0;

	function init() {
		$('phic_cov').update('None');
            //added by pol 10/12/2013
		
		
	

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
		shortcut.add('F10', keyF10,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F9', keyF9,
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
		shortcut.add('F8', keyF8,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F3', keyOrderlist,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
		shortcut.add('F4', keyServelist,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
<?php
	}
?>
		refreshDiscount();
	}

	function keyF8() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order-functions.php?userck=<?=$userck?>";
	}

	function keyOrderlist() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order.php?userck=<?=$userck?>&target=list&area=<?=$area?>";
	}

	function keyServelist() {
		window.location = "<?=$root_path?>modules/pharmacy/seg-pharma-order.php?userck=<?=$userck?>&target=servelist&area=<?=$area?>";
	}

	function keyF2() {
		openOrderTray();
	}

	//function keyF3() {
	function keyF10() {
		if (confirm('Clear the order list?'))	emptyTray();
	}

	function keyF9() {
<?php
	$var_arr = array(
		"var_pid"=>"pid",
		"var_encounter_nr"=>"encounter_nr",
		"var_parent_discountid"=>"discountid",
		"var_discount"=>"discount",
		"var_name"=>"ordername",
		"var_addr"=>"orderaddress",
		"var_clear"=>"clear-enc",
		"var_enctype"=>"encounter_type",
		"var_enctype_show"=>"encounter_type_show",
		"var_include_walkin"=>"1",
		"var_reg_walkin"=>"1"
	);
	$vas = array();
	foreach($var_arr as $i=>$v) {
		$vars[] = "$i=$v";
	}
	$var_qry = implode("&",$vars);
?>
        //added by VAN 02-06-2012
        //for bloodbank only as per Mrs Angie Balayon's request 
        var ref_source2 = $('area').value;
        var ref_source1 = '<?=$ref_source?>';
        
        if (ref_source1=='BB')
            ref_source = ref_source1;
        else if (ref_source2=='BB')  
            ref_source = ref_source2;  
        else
            ref_source = "";    
        //-----------------    
        
        $('warningcaption').innerHTML = '';  
        
		if (warnClear()) {
			emptyTray(); overlib(
				OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?<?=$var_qry?>&ref_source='+ref_source+'&var_include_enc='+($('iscash1').checked?'0':'1'),
				700, 400, 'fSelEnc', 0, 'no'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
				CAPTIONPADDING,2,
				CAPTION,'Select registered person',
				MIDX,0, MIDY,0,
				STATUS,'Select registered person');
		}
		return false;
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
        var auto_senior = $('auto_senior').value;
        var url = 'seg-order-tray.php?area='+area+'&d='+discount+'&auto_senior='+auto_senior;
        var goadd = true;
        
/*        if (area=='BB'){
           if (validatePHIC())
              goadd = true;     
           else
              goadd  = false;
        }*/
		
        //if (goadd){
            
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
/*        }else{
           alert('Cannot charge a blood request to PHIC because the PHIC number is not eligible. \n '+
                  'Either a temporary number or the number format is not correct. \n '+
                  'Please charge it but not to PHIC or pay the request instead.'); 
        }   */     
		return true;
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
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

$lastnr = $order_obj->getLastNr(date("Y-m-d"));


if ($_REQUEST['encounterset']) {
	$person = $order_obj->getPersonInfoFromEncounter($_REQUEST['encounterset']);
}

# Render form values
if (isset($_POST["submitted"]) && !$saveok) {
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

if ($person) {
	$_POST['pid'] = $person['pid'];
	$_POST['encounter_nr'] = $person['encounter_nr'];
	$_POST['ordername'] = $db->GetOne("SELECT fn_get_person_name(".$db->qstr($person['pid']).")");
	//$person['name_first']." ".$person['name_last'];
//	$addr = implode(", ",array_filter(array($person['street_name'], $person["brgy_name"], $person["mun_name"])));
//	if ($person["zipcode"])
//		$addr.=" ".$person["zipcode"];
//	if ($person["prov_name"])
//		$addr.=" ".$person["prov_name"];

	$addr = $db->GetOne("SELECT fn_get_complete_address(".$db->qstr($person['pid']).")");
	$_POST['orderaddress'] = $addr;
	$_POST['discount_id'] = $person['discount_id'];
	$_POST['discount'] = $person['discount'];
    $age = $db->GetOne("SELECT  `fn_get_ageyr`(NOW(),fn_get_birth_date(".$db->qstr($person['pid'])."))");
    $auto_senior = (($age>=60) ? '1' : '0');

}

require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$prod=$prod_obj->getAllPharmaAreas();
//modified by cha, 11-24-2010
//$disabled = (strtolower($_GET['area']) != 'all') ? ' disabled="disabled"' : '';
$disabled = (strtolower($_GET['area']) != 'all') ? '' : '';
$index = 0;
$count = 0;
$select_area = '';
while($row=$prod->FetchRow()){
	$checked=strtolower($row['area_code'])==strtolower($_GET['area']) ? 'selected="selected"' : "";
	$select_area .= "	<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
	if ($checked) $index = $count;
	$count++;
}
$select_area = '<select class="segInput" name="area" id="area"'.$disabled.' onchange="if (warnClear()) { emptyTray(); this.setAttribute(\'previousValue\',this.selectedIndex);} else this.selectedIndex=this.getAttribute(\'previousValue\');" previousValue="'.$index.'">'."\n".$select_area."</select>\n".
	"<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['area']."\"/>";
$smarty->assign('sSelectArea',$select_area);

$smarty->assign('sIsCash','<input class="segInput" type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="segInput" style="font:bold 12px Arial; color:#3e7bc6">Cash</label>');
$smarty->assign('sIsCharge','<input class="segInput" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="segInput" for="iscash0" style="font:bold 12px Arial; color:#c64c3e">Charge</label>');
$smarty->assign('sIsTPL','<input class="segInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($_POST["is_tpl"]=="1")?'checked="checked" ':'').'/><label class="segInput" for="is_tpl" style="color:#006600">To pay later</label>');

#added by VAN 01/23/2013
#requested by Ma'am Angie of Blood Bank and with approval of Sir Justol
#open to all pharmacy area
#$option_mission = '';
#if ($ref_source=='BB')
    $option_mission = '<option value="MISSION">MISSION</option>
                       <option value="PCSO">PCSO</option>
                      ';

$smarty->assign('sChargeType', '<select id="charge_type" name="charge_type" class="segInput" style="display:none" onchange="if (warnClear()) { emptyTray(); changeChargeType(); return true;} else {return false;}">
<option value="PERSONAL">TPL</option>
<option value="PHIC">PHIC</option>
<!--	<option value="LINGAP">LINGAP</option>
<option value="CMAP">CMAP</option> -->
'.$option_mission.'
</select>');


$smarty->assign('sOrderEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sOrderEncType','<input id="encounter_type" name="encounter_type" type="hidden" value="'.$_POST["encounter_type"].'"/>');
if ($_POST['encounter_type'])	$smarty->assign('sOrderEncTypeShow',$enc[$_POST['encounter_type']]);
else {
	if ($person['encounter_type'])
		$smarty->assign('sOrderEncTypeShow',$enc[$person['encounter_type']]);
	else	$smarty->assign('sOrderEncTypeShow', 'WALK-IN');
}

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
// $smarty->assign('sAdmDate',$enc[$_POST['encounter_type']]);

#added by genz

$phic_nr = $db->GetOne("SELECT fn_get_phic_number('".$_POST["encounter_nr"]."') AS `phic_nr`");
$smarty->assign('sPhicNo', $phic_nr);

 if($_POST["encounter_nr"]){
        $sql_mc = "SELECT cif.`firm_id` FROM seg_encounter_insurance AS sei 
					INNER JOIN care_insurance_firm AS cif ON cif.`hcare_id` = sei.`hcare_id` 
					WHERE cif.hcare_id != 18 AND sei.encounter_nr =".$db->qstr($_POST["encounter_nr"]);
        $other_insurance_nr = $db->GetOne($sql_mc);
        if($other_insurance_nr){                        
                        $other_insurance_nr_ui = $other_insurance_nr;
                    }else{
                        $other_insurance_nr_ui = 'None';    
                    }
    }

     $smarty->assign('sMemCategory', $other_insurance_nr_ui);

	if($_POST["encounter_nr"]){
       $sql_mc = "SELECT SUM(seg_pharma_order_items.pricecharge*(seg_pharma_order_items.quantity)) AS TotalCharges
									FROM seg_pharma_orders 
									LEFT JOIN seg_pharma_order_items 
									ON  seg_pharma_orders.refno= seg_pharma_order_items.refno									
									WHERE seg_pharma_order_items.serve_status ='S' AND is_cash=0 AND seg_pharma_orders.encounter_nr=".$db->qstr($_POST["encounter_nr"]);
			        $totalCharges = $db->GetOne($sql_mc);
        if($totalCharges){                        
                        $totalChargesUi = number_format($totalCharges,2);
                    }else{
                        $totalChargesUi = 'None';    
                    }
    }



    
$smarty->assign('sTotalCharges', $totalChargesUi);

#end by genz

$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$_POST["pid"].'"/>');
$smarty->assign('sOrderDiscountID','<input id="discountid" name="discountid" type="hidden" value="'.$_POST["discountid"].'"/>');
$smarty->assign('sOrderDiscount','<input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>');


if ($person) {
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="" readonly="readonly" value="'.$_POST["ordername"].'"/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="" readonly="readonly" >'.$_POST["orderaddress"].'</textarea>');
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="" value="Clear" disabled="disabled" />');
}
else {
	/* $smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["ordername"].'"/>'); */
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="30" style="font:" value="'.$_POST["ordername"].'" onfocus="autoSuggestWalkin(this)" autocomplete="off"/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="27" rows="2" style="" readonly="readonly" onfocus="this.select()">'.$_POST["orderaddress"].'</textarea>');
	$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Clear" onclick="clearEncounter()" '.(($_POST['pid'])?'':' disabled="disabled"').' />');
}
$smarty->assign('sRefNo','<input class="segInput" id="refno" name="refno" type="text" size="10" value="'.($submitted ? $_POST['refno'] : $lastnr).'" style=""/>');
$smarty->assign('sResetRefNo','<input class="segButton" type="button" value="Reset" onclick="xajax_reset_referenceno()"/>');

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
if ($_REQUEST['dateset']) {
	$curDate = date($dbtime_format,$_REQUEST['dateset']);
	$curDate_show = date($fulltime_format, $_REQUEST['dateset']);
}
else {
	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);
}
$smarty->assign('sOrderDate','<span id="show_orderdate" class="segInput" style="color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="segInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="">');

if ($_REQUEST['billing']) {
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

# LINGAP/CMAP
if (true) {
	$sponsorHTML = '<select class="segInput" name="sponsor" id="sponsor">
<option value="" style="font-weight:bold">No coverage</option>
';
	include_once($root_path."include/care_api_classes/class_sponsor.php");
	$sc = new SegSponsor();
	$sponsors = $sc->get();
	while($row=$sponsors->FetchRow()){
		$sponsorHTML .= "									<option value=\"".$row['sp_id']."\">".$row['sp_name']."</option>\n";
	}
	$sponsorHTML .= "					</select>";
	$smarty->assign('sSponsor',$sponsorHTML);
}

$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
$smarty->assign('sNormalPriority','<input class="segInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/><label class="segInput" for="p0">Routine</label>');
$smarty->assign('sUrgentPriority','<input class="segInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/><label class="segInput" for="p1">Stat</label>');
$smarty->assign('sComments','<textarea class="segInput" name="comments" cols="14" rows="2" style="float:left; margin-left:3px;margin-top:3px">'.$_POST['comment'].'</textarea>');

if ($_REQUEST['billing'])
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="opacity: 0.2"/>');
else
	$smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer" onclick="keyF9()" onmouseout="nd();" />');

$smarty->assign('sRootPath',$root_path);

$text="add";
$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="openOrderTray()">');
$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" onclick="if (confirm(\'Clear the order list?\')) emptyTray()"/>');
$smarty->assign('sBtnCoverage','<img class="segSimulatedLink" id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" border="0" onclick="return openCoverages();"/>');
$smarty->assign('sDiscountShow','<input type="checkbox" is_nurse='.($isDisableSC ? 1 : 0).' name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="segInput" for="issc" style="font:bold 12px Arial;">Senior citizen</label>');

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
/*
	$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			inputField : \"orderdate\", ifFormat : \"$phpfd\", showsTime : false, button : \"orderdate_trigger\", singleClick : true, step : 1
		});
	</script>
	";
$smarty->assign('jsCalendarSetup', $jsCalScript);*/

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

$qs = "";
if ( $_GET['billing'] ) $qs .= "&billing=".$_GET['billing'];
if ( $_GET['pid'] ) $qs .= "&pid=".$_GET['pid'];
if ( $_GET['encounterset'] ) $qs .= "&encounterset=".$_GET['encounterset'];

$smarty->assign("sWarning","<em><font color='RED'><strong>&nbsp;<span id='warningcaption'>".$warningCaption."</span></strong></font></em>"); 

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&target=new&clear_ck_sid=".$clear_ck_sid.$qs.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
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
	<input type="hidden" name="target" value="<?php echo $target ?>">

	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">
	<input type="hidden" name="billing" id="billing" value="<?= $_REQUEST['billing'] ?>">
	<input type="hidden" name="dateset" id="dateset" value="<?= $_REQUEST['dateset'] ?>">
	<input type="hidden" name="encounterset" id="encounterset" value="<?= $_REQUEST['encounterset'] ?>">
    
    <input type="hidden" name="is_maygohome" id="is_maygohome" value="<?=$is_maygohome?>">
    <input type="hidden" name="bill_nr" id="bill_nr" value="<?=$bill_nr?>">
    <input type="hidden" name="hasfinal_bill" id="hasfinal_bill" value="<?=$hasfinal_bill?>">
    <input type="hidden" name="auto_senior" id="auto_senior" value="<?=$auto_senior?>">
<?php

$sTemp = ob_get_contents();
ob_end_clean();

/*
global $GPC;
echo $GPC;
echo "<hr>sid:$sid;clear:$clear_ck_sid";
*/

$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="if (validate()) document.inputform.submit()"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','order/form.tpl');
$smarty->display('common/mainframe.tpl');

?>