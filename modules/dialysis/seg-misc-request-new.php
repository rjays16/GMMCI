<?
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/or/ajax/op-request-new.common.php');

require_once($root_path.'include/care_api_classes/billing/class_ops.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_oproom.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php');

#added by ken 10/10/2014 for requiring class curl
require_once($root_path.'include/care_api_classes/curl/class_curl.php');
$curl_obj=new Rest_Curl;

$enc_obj=new Encounter;
$seg_department = new Department();
$seg_room = new OPRoom();
$seg_ops = new SegOps();
$seg_ormisc = new SegOR_MiscCharges();

$smarty = new Smarty_Care('common');
$smarty->assign('sToolbarTitle',"Dialysis :: Test Request");
$smarty->assign('sWindowTitle',"Dialysis :: Test Request");

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);
ob_start();
?>
<link rel="stylesheet" href="<?=$root_path?>modules/or/css/or_main.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>modules/or/js/flexigrid/lib/jquery/jquery.js"></script>
<script>var J = jQuery.noConflict();</script>
<link rel="stylesheet" href="<?=$root_path?>modules/or/css/select_or_request.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>modules/or/js/flexigrid/css/flexigrid/flexigrid.css">
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/flexigrid/flexigrid.js"></script>
<link rel="stylesheet" href="<?=$root_path?>modules/or/css/select_or_request.css" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>modules/or/js/jqmodal/jqModal.css">
<script type="text/javascript" src="<?=$root_path?>modules/or/js/jqmodal/jqModal.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/jqmodal/jqDnR.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/jqmodal/dimensions.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/misc-request-gui.js?t=<?=time()?>"></script>

<?
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

if (isset($_POST['submitted'])) {
 $saveok_cnt = 0;
 $no_items = 0;

	//start saving miscellaneous
	foreach($_POST["misc_item"] as $i=>$item)
	{
		if($_POST["misc_item_disabled"][$i]=="0")
		{
			if($_POST["misc_request_flag"][$i]){
				$flag = $_POST["misc_request_flag"][$i];
			}
			$miscItems[] = $_POST["misc_item"][$i];
			$miscQty[] = $_POST["misc_qty"][$i];
			$miscPrc[] = $_POST["misc_prc"][$i];
            $miscAdj[] = $_POST["misc_adj_prc"][$i];
			$miscType[] = $_POST["misc_account_type"][$i];
            $miscClinic[] = $_POST["misc_clinicInfo"][$i];
            $miscFlag[] = $flag;
		}
	}
    
   	if($_POST['mode']=='new') {
		$refno = $seg_ormisc->getMiscRefno(date('Y-m-d H:i:s')); 
	} else if($_POST['mode']=='edit') {
		$refno = $_POST['refno'];
	}
                                                           
	$array = array(
								'refno' => $refno,
								'charge_date' => $_POST['transaction_date'],
								'encounter_nr' => $encounter_nr,
                                'pid' => $pid,
								'misc' => $miscItems,
                                'discountid' => $_POST['discountid'],
                                'discount' =>  $_POST['discount'],
                                'misc' => $miscItems,
								'quantity' => $miscQty,
                                'adj_amnt' => $miscAdj,
								'price' => $miscPrc,
								'request_flag' => $miscFlag,
								'account_type' => $miscType,
								'is_cash' => $_POST['transaction_type'],
                                'clinical_info' => $miscClinic,
								'area' => $_POST['area']); //edit
	if($_POST['mode']=='new') {
		$saveok = $seg_ormisc->saveMiscCharges($array);
		#added by ken 10/10/2014 for passing the data into FIS
		// $curl_obj->inpatientMiscRequest($refno);
	} else if($_POST['mode']=='edit') {
		$saveok = $seg_ormisc->updateMiscCharges($array);
		#added by ken 10/10/2014 for passing the data into FIS
		// $curl_obj->inpatientMiscRequest($refno);
	}

	 if($saveok) $saveok_cnt++;
	 if(count($_POST['misc'])==0)
	 {
		 $no_items=1;
	 }
	 //end saving miscellaneous
	if($saveok_cnt==0 && $no_items==1)
	{
		 $smarty->assign('sysErrorMessage','<strong>Error:</strong> Cannot save miscellaneous charges. '.$seg_ormisc->getErrorMsg().'\nSQL:'.$seg_ormisc->getLastQuery());
	}
	else if($saveok_cnt>0)
	{
		 $smarty->assign('sysInfoMessage','Miscellaneous charges successfully submitted.');

		   $smarty->assign('sMsgTitle','Miscellaneous request successfully saved!');
      $smarty->assign('sMsgBody','The request details have been saved into the database...');
      $sBreakImg ='close2.gif';
      $smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
      
      $printfile = $root_path.'modules/dialysis/seg-misc-request-print.php'. URL_APPEND."&target=print&userck=$userck".'&ref='.$refno;
      $smarty->assign('sPrintButton','<img class="segSimulatedLink"  src="'.$root_path.'images/btn_printpdf.gif" border="0" align="absmiddle" alt="Print" onclick="openWindow(\''.$printfile.'\')" onsubmit="return false;" style="cursor:pointer">');

      // $printfile2 = $root_path.'modules/pharmacy/seg-pharma-order.php'. URL_APPEND."&target=print-charge&userck=$userck".'&cat=pharma&or_type=charge&request_source='.$_REQUEST['request_source'].'&ref='.$data['refno'];
      // $smarty->assign('sPrintChargeInvoice','<button onclick="openWindow(\''.$printfile2.'\'); return false;" style="cursor:pointer"><img border="0" src="'.$root_path.'gui/img/common/default/printer_icon.gif"  style="cursor:pointer"><span style="font-weight:bold;">Print Charge Invoice</span></button>');
      

      $Ref = $refno;
      $infoResult = $seg_ormisc->getOrderInfo($Ref);
      // echo $seg_ormisc->sql;
      if ($infoResult)  $info = $infoResult->FetchRow();

      $smarty->assign('sRefNo', $Ref);
      // $smarty->assign('sSelectArea', $info['area_name']);
      $smarty->assign('sCashCharge',($info['is_cash']=="1" ) ? "Cash" : "Charge");
      $smarty->assign('sOrderDate', date("F j, Y g:ia",strtotime($info['orderdate'])));
      $smarty->assign('sOrderName', $info['ordername']);
      $smarty->assign('sOrderAddress', $info['orderaddress']);
      // $smarty->assign('sPriority',($info['priority']=="0") ? "Routine" : "Stat");
      // $smarty->assign('sRemarks',$info['comments']);


      $itemsResult = $seg_ormisc->getOrderItemsFullInfo($Ref);
      if ($itemsResult) {
        $oRows = "";
         while ($oItem=$itemsResult->FetchRow()) {

            $oRows .= '<tr>
                      <td align="center" style="font:bold 11px Tahoma;color:#000080">'.$oItem['service_code'].'</td>
                      <td >'.$oItem['name'].'</td>
                      <td  align="right">'.number_format((float)$oItem['chrg_amnt'],2).'</td>
                      <td align="center">'.number_format((float)$oItem['quantity']).'</td>
                      <td align="right">'.number_format((float)$oItem['adjusted_amnt'],2).'</td>
                    </tr>';

        }
        if (!$oRows) {
          $oRows = '<tr><td colspan="10" class="segPanel3">Order list is empty...</td></tr>';
        }
      }

      $smarty->assign('sItems',$oRows);

      $smarty->assign('sMainBlockIncludeFile','order/saveok.tpl');
      $smarty->display('common/mainframe.tpl');
      $db->CompleteTrans();
      exit();
	}
}

$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');

/*$trans_type_options =
	'<input type="radio" id="iscash1" name="iscash" value="1" checked="checked">Cash&nbsp;&nbsp;'.
	'<input type="radio" id="iscash0" name="iscash" value="0">Charge';
$smarty->assign('transaction_type', $trans_type_options); */
//$smarty->assign('transaction_types', array('1'=>'Cash',	 '0' => 'Charge'));
$transaction_date_display = isset($_POST['transaction_date']) ? date('F d, Y h:ia', strtotime($_POST['transaction_date'])) : date('F d, Y h:ia');
$transaction_date = isset($_POST['transaction_date']) ? date('Y-m-d H:i', strtotime($_POST['transaction_date'])) : date('Y-m-d H:i');
$smarty->assign('transaction_date_display', '<div id="transaction_date_display" class="date_display">'.$transaction_date_display.'</div>');
$smarty->assign('transaction_date', '<input type="hidden" name="transaction_date" id="transaction_date" value="'.$transaction_date.'" />');
$smarty->assign('transaction_date_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="transaction_date_picker" class="date_time_picker" />');
$smarty->assign('transaction_date_calendar_script', setup_calendar('transaction_date_display', 'transaction_date', 'transaction_date_picker'));

$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$seg_person = new Person($pid);
$person_info = $seg_person->getAllInfoArray();
$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
$person_name = ucwords($person_info['name_last']) . ', ' . ucwords($person_info['name_first']) . ' ' . $middle_initial;

$smarty->assign('pid', '<input type="hidden" name="pid" id="pid" value="'.$pid.'" />');
$smarty->assign('patient_name', $person_name);

$encounter_types = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)', "5"=>'DIALYSIS', "6"=>'INDUSTRIAL CLINIC');
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
$seg_encounter = new Encounter();
$encounter_details = $seg_encounter->getEncounterInfo($encounter_nr);
$encounter_type = $encounter_types[$encounter_details['encounter_type']];

if (($encounter_nr)||($pid)){
        $discountid = $encounter_details['discountid'];
        $discount = $encounter_details['discount'];
}

if (($encounter_details['encounter_type']==2)||($encounter_details['encounter_type']==1))
    $impression = $encounter_details['chief_complaint'];
elseif (($encounter_details['encounter_type']==3)||($encounter_details['encounter_type']==4))
    $impression = $encounter_details['er_opd_diagnosis'];
    
if (!$impression) {
    $impression = '';
    $impression = $enc_obj->getLatestImpression($pid, $encounter_nr);
}

$smarty->assign('encounter_type', $encounter_type);

$social_service = new SocialService();
if($encounter_type == 'OUTPATIENT'){
    $social_service_details = $social_service->getLatestClassificationByPid($pid);    
}else{
    $social_service_details = $social_service->getLatestClassificationByPid($encounter_nr,0);
}
$is_sc = ($social_service_details['discountid'] == 'SC') ? '1' : '0';

if ($_GET['view_from'])
    $view_from = $_GET['view_from'];

 if ($view_from=='ssview'){
     if ($_GET['discountid'] || $_POST['discountid']){
         $discountid = ($_GET['discountid']) ? $_GET['discountid'] : $_POST['discountid'];
         $infoSS = $social_service->getSSClassInfo($discountid);

         if ($infoSS['parentid'])
                $discountid = $infoSS['parentid'];
         else
                $discountid = $discountid;

         $discount = $infoSS['discount'];
     }

 }else{
      $view_from = '';
 }
        

    $infoSS2 = $social_service->getSSClassInfo($discountid);

    if (($infoSS2['parentid'])&&($infoSS2['parentid']=='D'))
        $discountid2 = $infoSS2['parentid'];
    else
        $discountid2 = $discountid;
    
$smarty->assign('view_from','<input type="hidden" name="view_from" id="view_from" value="'.$view_from.'" />');
$smarty->assign('sClassification',($discountid2) ? $discountid2:'None');
$smarty->assign('discount','<input type="hidden" name="discount" id="discount" value="'.$discount.'" /><input type="hidden" name="discountid" id="discountid" value="'.$discountid2.'" />'); 
$smarty->assign('add_misc_btn', '<button class="segButton" onclick="show_popup_misc();return false;" id="add_misc_btn"><img src="'.$root_path.'gui/img/common/default/cart_add.png"/>Add Item</button>');
$smarty->assign('empty_misc_btn', '<button class="segButton" onclick="empty_misc();return false;" id="empty_misc_btn"><img src="'.$root_path.'gui/img/common/default/delete.png"/>Empty</button>');
if($_GET['mode'] == 'edit') $smarty->assign('print_report', '<img class="link" border="0" align="middle" onsubmit="return false;" style="cursor:pointer;position:absolute;margin-left:-250px;" onclick="window.open(\'../../modules/dialysis/seg-misc-request-print.php?ref='.$_GET['refno'].'\',\'Miscellaneous Print\', \'toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes\')" alt="Print" src="../../images/btn_printpdf.gif">');
$smarty->assign('other_charges_submit', '<input type="button" id="or_main_submit" value="" onclick="validate(); return false;"/>');
$smarty->assign('other_charges_cancel', '<a href="'.$breakfile.'" id="or_main_cancel"></a>');
$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" onclick="validate(); return false;" style="cursor:pointer" src="'.$root_path.'images/btn_discounts2.gif" border="0">');

$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
$mode = $_GET['mode']?$_GET['mode']:'edit'; 
$smarty->assign('mode', '<input type="hidden" id="mode" name="mode" value="'.$mode.'"/>');
$area = $_GET['area']?$_GET['area']:$_POST['area'];
$smarty->assign('area', '<input type="hidden" id="area" name="area" value="'.$area.'"/>');
$smarty->assign('impression', '<input type="hidden" id="impression" name="impression" value="'.$impression.'"/>');
/*$refno = $_GET['refno']?$_GET['refno']:$_POST['refno'];
$smarty->assign('refno', '<input type="hidden" id="refno" name="refno" value="'.$refno.'"/>');
 */
//$misc_refno = $seg_ormisc->getMiscRefno(date('Y-m-d H:i:s'));
if($mode=='edit') {
	$misc_refno = $_GET['refno']?$_GET['refno']:$refno;
}
$smarty->assign('reference_no', '<input type="text" class="segInput" readonly="readonly" id="refno" name="refno" value="'.$misc_refno.'"/>');

$misc_trans = $seg_ormisc->getMiscOrderItemsByRefno($misc_refno);
$row = $misc_trans->FetchRow();

if($mode=='edit'){
    if($row['is_cash']==1)
        $iscash = 1;
    elseif ($row['is_cash']==0)
        $iscash = 0;    
}else{
    if($encounter_details['encounter_type']==2){
        $iscash = 1;    
    }else
        $iscash = 0;  
}

$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($iscash!=0)?'checked="checked" ':'').' onclick="if (warnClear()) { empty_misc(); changeTransaction(this.value); return true;} else return false;" /><label for="iscash1" class="jedInput">Cash</label>');
$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($iscash==0)?'checked="checked" ':'').' onclick="if (warnClear()) { empty_misc(); changeTransaction(this.value); return true;} else return false;"  style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
$smarty->assign('transaction_type', '<input type="hidden" name="transaction_type" id="transaction_type" value="'.$iscash.'" />');
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','dialysis/misc_request_tray.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

function setup_calendar($display_area, $input_field, $button) {
	global $root_path;
	$calendar_script =
		'<script type="text/javascript">
			 Calendar.setup ({
				 displayArea : "'.$display_area.'",
				 inputField : "'.$input_field.'",
				 ifFormat : "%Y-%m-%d %H:%M",
				 daFormat : "%B %e, %Y %I:%M%P",
				 showsTime : true,
				 button : "'.$button.'",
				 singleClick : true,
				 step : 1
			 });
			</script>';
	return $calendar_script;
}
?>

<script>

J().ready(function() {
	J('#misc_charge')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');
});

J('#misc_charge').jqm({
overlay: 80
});

function validate()
{
    if($('view_from').value!="ssview")
	    var rep = confirm("Process this request?");
    else
        var rep = confirm("Apply discount?");
	if(rep) {
		var el = document.getElementsByName('misc_item[]');
		if(el.length<=0) {
			alert("Cannot save this transaction. No items in the list.")
			return false;
		}else { 
			document.main_or_form.submit();
		}
	}
}

function show_popup_misc() {
    var pid = $('pid').value;
    var enc = $('encounter_nr').value;
	return overlib(OLiframeContent('misc-request-tray.php?pid='+pid+'&encounter_nr='+enc, 600, 330, 'fMiscFees', 0, 'no'),
		WIDTH,600, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
		CAPTION,'Add Miscellaneous hospital services',
		MIDX,0, MIDY,0,
		STATUS,'Add Miscellaneous hospital services');
}

function key_check(e, value) {
	 var character = String.fromCharCode(e.keyCode);
	 var number = /^\d+$/;
	 if ((e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || e.keyCode==9 || (e.keyCode==191 || e.keyCode==111) || (e.keyCode>=36 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105))) {
		 return true;
	 }
	 if (character.match(number)==null) {
		 return false;
	 }
	 else {
		 return true;
	 }
}

function key_check2(e, value) {
	 var character = String.fromCharCode(e.keyCode);
	 var number = /^\d+$/;
	 var reg = /^[-+]?[0-9]+((\.)|(\.[0-9]+))?$/;
	 if (character=='?') {
		 character = '.';
	 }
	 var text_value = value+character;
	 if ((e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || e.keyCode==9 || (e.keyCode>=36 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105))) {
		 return true;
	 }
	 if (character.match(number)==null) {
		 return false;
	 }
}

function changeTransaction(iscash){
    $('transaction_type').value = iscash;   
}

if($('mode').value=='edit') {
    if($('view_from').value=="ssview"){
        $('add_misc_btn').disabled = true;
        $('empty_misc_btn').disabled = true;
        $('or_main_cancel').style.display = "none";
        $('or_main_submit').style.display = "none";       
        $('iscash1').disabled = true;
        $('iscash0').disabled = true;    
    }else{
        $('btndiscount').style.display = "none";            
    } 
   xajax_get_misc_request_by_refno('misc_list', $('refno').value);
}else{
    $('btndiscount').style.display = "none";
}

update_total_misc();
J.unblockUI();
</script>
