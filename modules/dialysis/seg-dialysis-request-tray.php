<?
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/dialysis/ajax/dialysis-transaction.common.php');

require_once($root_path.'include/care_api_classes/billing/class_ops.php');
include_once($root_path."include/care_api_classes/class_order.php");
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_oproom.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'include/care_api_classes/class_equipment_order.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
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
<script type="text/javascript" src="<?=$root_path?>modules/or/js/other_charges.js"></script>
<script type="text/javascript" src="js/lab-request-gui.js"></script>
<script type="text/javascript" src="js/blood-request-gui.js"></script>
<script type="text/javascript" src="js/radio-request-gui.js"></script>
<script type="text/javascript" src="js/pharma-request-gui.js"></script>
<script type="text/javascript" src="js/misc-request-gui.js"></script>

<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');


$result = $enc_obj->getChargeType("WHERE id NOT IN ('paid','phs','charity')","ordering");
$options="";
$grant_type = $info['grant_type'];
if (!($grant_type)){
	$grant_type = '';
	$disabled = "";
}else
	$disabled = "disabled";
$options = "<option value=''>PERSONAL</option>";
while ($row=$result->FetchRow()) {
	if ($grant_type==$row['id'])
		$checked = "selected";
	else
		$checked = "";

	$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
}
$smarty->assign('sChargeTyp','<select class="segInput" name="grant_type" id="grant_type" '.$disabled.' style="display:none">'.$options.'</select>');
$trans_type_options =
	'<input type="radio" id="iscash1" name="iscash" value="1" onclick="changeTransactionType(this.id);" checked="checked">Cash&nbsp;&nbsp;'.
	'<input type="radio" id="iscash0" name="iscash" value="0" onclick="changeTransactionType(this.id);">Charge';
$smarty->assign('transaction_type', $trans_type_options);
$smarty->assign('sRDU','<input type="checkbox" name="is_rdu" id="is_rdu" value="1" />');
$priority_type_options =
	'<input type="radio" name="priority" id="priority" value="0" checked="checked"/>Routine&nbsp;&nbsp;'.
	'<input type="radio" name="priority" id="priority" value="1"/>Stat';
$smarty->assign('sPriority', $priority_type_options);
$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" wrap="physical"  cols="40" rows="5" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic"></textarea>');


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

$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));
if ($person_info["zipcode"])
	$person_address.=" ".$person_info["zipcode"];
if ($person_info["prov_name"])
	$person_address.=" ".$person_info["prov_name"];


$smarty->assign('pid', '<input type="hidden" name="pid" id="pid" value="'.$pid.'" />');
$smarty->assign('patient_name', $person_name);
$smarty->assign('patient_age', $person_info["age"]);
if($person_info["sex"]=="f")
	$gender="Female";
if($person_info["sex"]=="m")
	$gender="Male";
$smarty->assign('patient_gender', $gender);


$encounter_types = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)', "5"=>'DIALYSIS');
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
$seg_encounter = new Encounter();
$encounter_details = $seg_encounter->getEncounterInfo($encounter_nr);
$encounter_type = $encounter_types[$encounter_details['encounter_type']];

$smarty->assign('encounter_type', $encounter_type);
$ward_nr = isset($_GET['ward']) ? $_GET['ward'] : $_POST['ward'];
$smarty->assign('ward', '<input type="hidden" name="ward" value="'.$ward_nr.'" />');
$social_service = new SocialService();

$social_service_details = $social_service->getLatestClassificationByPid($encounter_nr);
$is_sc = ($social_service_details['discountid'] == 'SC') ? '1' : '0';
$patient_classification = isset($social_service_details['discount_id']) ? $social_service_details['discount_id'] : 'None';
$smarty->assign('patient_classification', $patient_classification);

$service_type_code = array (49,50,51,52,53,54,"");
$service_type_name = array ("Physical Medicine & Rehab", "Dental", "Orthopedics", "ENT-HNS", "Pediatrics", "Special Lab", "Other");
$service_type_options = "<option value='0'> -Select service type- </option";
for($i=0;$i<count($service_type_code);$i++)
{
	$service_type_options.="<option value='".$service_type_code[$i]."'>".$service_type_name[$i]."</option>";
}
$smarty->assign('miscServiceTypes', $service_type_options);

$smarty->assign('lab_reference_no', '<input type="text" class="segInput" readonly="readonly" id="lab_refno" name="lab_refno" value=""/>');
$smarty->assign('lab_phic', '<span id="lab_phic" style="font:14px Arial bold">0.00</span>');
$smarty->assign('add_lab_btn', '<button class="segButton" onclick="openLabTray();return false;" id="add_lab_btn"><img src="'.$root_path.'gui/img/common/default/add.png"/>Add Item</button>');
$smarty->assign('empty_lab_btn', '<button class="segButton" onclick="emptyLabOrder();return false;" id="empty_lab_btn"><img src="'.$root_path.'gui/img/common/default/cart_delete.png"/>Empty</button>');

$smarty->assign('blood_reference_no', '<input type="text" class="segInput" readonly="readonly" id="blood_refno" name="blood_refno" value=""/>');
$smarty->assign('blood_phic', '<span id="blood_phic" style="font:14px Arial bold">0.00</span>');
$smarty->assign('add_blood_btn', '<button class="segButton" onclick="openBloodBankTray();return false;" id="add_blood_btn"><img src="'.$root_path.'gui/img/common/default/add.png"/>Add Item</button>');
$smarty->assign('empty_blood_btn', '<button class="segButton" onclick="emptyBloodOrder();return false;" id="empty_blood_btn"><img src="'.$root_path.'gui/img/common/default/cart_delete.png"/>Empty</button>');

$smarty->assign('pharma_reference_no', '<input type="text" class="segInput" readonly="readonly" id="pharma_refno" name="pharma_refno" value=""/>');
$smarty->assign('pharma_phic', '<span id="pharma_phic" style="font:14px Arial bold">0.00</span>');
$smarty->assign('add_pharma_btn', '<button class="segButton" onclick="openOrderTray2(); return false;" id="add_pharma_btn"><img src="'.$root_path.'gui/img/common/default/add.png"/>Add Item</button>');
$smarty->assign('empty_pharma_btn', '<button class="segButton" onclick="emptyPharmaOrder();return false;" id="empty_pharma_btn"><img src="'.$root_path.'gui/img/common/default/cart_delete.png"/>Empty</button>');

$smarty->assign('misc_reference_no', '<input type="text" class="segInput" readonly="readonly" id="misc_refno" name="misc_refno" value=""/>');
$smarty->assign('misc_phic', '<span id="misc_phic" style="font:14px Arial bold">0.00</span>');
$smarty->assign('add_misc_btn', '<button class="segButton" onclick="show_popup_misc();return false;" id="add_misc_btn"><img src="'.$root_path.'gui/img/common/default/add.png"/>Add Item</button>');
$smarty->assign('empty_misc_btn', '<button class="segButton" onclick="empty_misc();return false;" id="empty_misc_btn"><img src="'.$root_path.'gui/img/common/default/cart_delete.png"/>Empty</button>');

$smarty->assign('radio_reference_no', '<input type="text" class="segInput" readonly="readonly" id="radio_refno" name="radio_refno" value=""/>');
$smarty->assign('radio_phic', '<span id="radio_phic" style="font:14px Arial bold">0.00</span>');
$smarty->assign('add_radio_btn', '<button class="segButton" onclick="openRadioTray();return false;" id="add_radio_btn"><img src="'.$root_path.'gui/img/common/default/add.png"/>Add Item</button>');
$smarty->assign('empty_radio_btn', '<button class="segButton" onclick="emptyRadioOrder();return false;" id="empty_radio_btn"><img src="'.$root_path.'gui/img/common/default/cart_delete.png"/>Empty</button>');

$smarty->assign('other_charges_submit', '<input type="submit" id="or_main_submit" value="" />');
$smarty->assign('other_charges_cancel', '<a href="'.$breakfile.'" id="or_main_cancel"></a>');

$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
$smarty->assign('mode', '<input type="hidden" name="mode" value="'.$mode.'" />');
$smarty->assign('pharma_area', '<input type="hidden" id="pharma_area" name="pharma_area" value="WD"/>'); //pharma area for ward, "WD"
$smarty->assign('discountid', '<input type="hidden" name="discountid" id="discountid" value="'.$social_service_details['discountid'].'" />');
$smarty->assign('discount', '<input type="hidden" name="discount" id="discount" value="'.$social_service_details['discount'].'" />');
$smarty->assign('issc', '<input type="hidden" name="issc" id="issc" value="'.$is_sc.'" />');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');


$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','dialysis/request_tray.tpl'); //Assign the or_main template to the frameset
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

function openOrderTray2() {
				var discount = J('#discountid').val();
				var area = J('#charge_area_list').val();

				if (area==null)
						area = 'IP';

				var url = 'pharma-request-tray.php?area='+area+'&d='+discount;
				overlib(
						OLiframeContent(url, 600, 400, 'fOrderTray', 0, 'no'),
						WIDTH,600, TEXTPADDING,0, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
						CAPTIONPADDING,2,
						CAPTION,'Add pharmacy item from Order tray',
						MIDX,0, MIDY,0,
						STATUS,'Add product from Order tray');
				return false
}

function show_popup_misc() {
	/*var misc_form = $("misc_service_type");
	var i = misc_form.selectedIndex;
	if(misc_form.value!="0")
	{*/
		//return overlib(OLiframeContent('misc-request-tray.php?type='+misc_form.value+'&type_name='+misc_form.options[i].text, 600, 380, 'fMiscFees', 0, 'no'),
		return overlib(OLiframeContent('misc-request-tray.php', 600, 380, 'fMiscFees', 0, 'no'),
				WIDTH,600, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
				CAPTION,'Add Miscellaneous hospital services',
				MIDX,0, MIDY,0,
				STATUS,'Add Miscellaneous hospital services');
	/*}else
	{
		alert("Select a service type first.");
		return false;
	}*/
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
	 if (character=='¾') {
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

function empty_misc() {
	 var table1 = $('misc_list').getElementsByTagName('tbody').item(0);
	table1.innerHTML = '<tr id="empty_misc_row"><td colspan="7">Miscellaneous charges empty...</td></tr>';
	J("input[@name='misc_item[]']").remove();
	J("input[@name='misc_prc[]']").remove();
	J("input[@name='misc_account_type[]']").remove();
	J("input[@name='misc_qty[]']").remove();
	update_total_misc();
}

function changeTransactionType(id)
{
	if($(id).value=="0"){
		$('grant_type').style.display="";
	} else {
		$('grant_type').style.display="none";
	}
}

function openLabTray() {

	overlib(
				OLiframeContent('lab-request-tray.php?area=&ptype=', 600, 400, 'fOrderTray', 0, 'no'),       /*modified by CHA, March 26,2010*/
										WIDTH,440, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL,
								CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
									 CAPTIONPADDING,4, CAPTION,'Add laboratory service item from request tray',
									 MIDX,0, MIDY,0,
									 STATUS,'Add laboratory service item from request tray');
	return false;
}

function openBloodBankTray() {

	overlib(
				OLiframeContent('blood-bank-request-tray.php?area=&ptype=', 600, 400, 'fOrderTray', 0, 'no'),       /*modified by CHA, March 26,2010*/
										WIDTH,440, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL,
								CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
									 CAPTIONPADDING,4, CAPTION,'Add blood bank service item from request tray',
									 MIDX,0, MIDY,0,
									 STATUS,'Add blood bank service item from request tray');
	return false;
}

function openRadioTray() {

	overlib(
				OLiframeContent('radio-request-tray.php?area=&ptype=', 600, 400, 'fOrderTray', 0, 'no'),       /*modified by CHA, March 26,2010*/
										WIDTH,440, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL,
								CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
									 CAPTIONPADDING,4, CAPTION,'Add radiological service item from request tray',
									 MIDX,0, MIDY,0,
									 STATUS,'Add radiological service item from request tray');
	return false;
}

updateTotalLab();
updateTotalBlood();
updateTotalRadio()
updateTotalPharma()
J.unblockUI();
</script>
