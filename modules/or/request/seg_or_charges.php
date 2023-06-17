<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('NO_2LEVEL_CHK',1);

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
include_once($root_path."include/care_api_classes/class_order.php");  //load the SegOrder class
require_once($root_path.'modules/or/ajax/or_charges.common.php'); //load the xajax module
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_oproom.php'); //load the department class
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_social_service.php'); //load the segops class
require_once($root_path.'include/care_api_classes/class_equipment_order.php'); //load the segops class
require_once($root_path.'include/care_api_classes/class_ward.php'); //load the segops class
require_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php'); //load the SegOR_MiscCharges class

$seg_department = new Department();
$seg_room = new OPRoom();
$seg_ops = new SegOps();
$seg_ormisc = new SegOR_MiscCharges();

$smarty = new Smarty_Care('common');
$smarty->assign('sToolbarTitle',"OR :: Other charges"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"OR :: Other charges");

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);

#added by cha, august 21, 2010
require_once($root_path.'include/care_api_classes/class_request_source.php');
$ph_src_obj = new SegRequestSource();
$request_source = $ph_src_obj->getSourceOR();
$smarty->assign('pharma_req_src', '<input type="hidden" id="request_source" name="request_source" value="'.$request_source.'"/>');
#end cha

# Collect javascript code
ob_start();
# Load the javascript code
?>
<link rel="stylesheet" href="<?= $root_path ?>modules/or/css/or_main.css" type="text/css" />
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/flexigrid/lib/jquery/jquery.js"></script>
<script type="text/javascript">
var J = jQuery.noConflict();
</script>
<link rel="stylesheet" href="<?= $root_path ?>modules/or/css/select_or_request.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>modules/or/js/flexigrid/css/flexigrid/flexigrid.css">
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>modules/or/css/select_or_request.css" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>modules/or/js/jqmodal/jqModal.css">
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/jqmodal/jqModal.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/jqmodal/jqDnR.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/jqmodal/dimensions.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/order-gui.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/order_equipment.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/other_charges.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<script type="text/javascript">

function openOrderTray2() {
	var discount = J('#discountid').val();
	var area = J('#charge_area_list').val();

	if (area==null)
		area = 'IP';

	var url = '<?=$root_path?>modules/or/request/seg-order-tray.php?area='+area+'&d='+discount;
	overlib(
		OLiframeContent(url, 660, 360, 'fOrderTray', 0, 'no'),
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
	var misc_form = $("misc_service_type");
	var i = misc_form.selectedIndex;
	if(misc_form.value!="0")
	{
		return overlib(OLiframeContent('seg-other-charges-tray.php?type='+misc_form.value+'&type_name='+misc_form.options[i].text, 600, 340, 'fMiscFees', 0, 'no'),
			WIDTH,600, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=../../../images/close_red.gif border=0 >',
			CAPTION,'Miscellaneous charges',
			MIDX,0, MIDY,0,
			STATUS,'Miscellaneous charges');
	}
	else {
		alert("Select a service type first.");
		return false;
	}
}

function openSelectICPM()
{
	return overlib(OLiframeContent('../../../modules/or/request/or_charged_accomodation.php'+'<?=URL_APPEND?>'+'&enc_nr='+$('encounter_nr').value+'&target=ac',
		 600, 350, 'fMiscFees', 0,'auto'),
			WIDTH,600, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=../../../images/close_red.gif border=0 >',
			CAPTION,'Select Procedures w/ Accommodation',
			MIDX,0, MIDY,0,
			STATUS,'Select Procedures w/ Accommodation');
}

function show_oxygen() {
	 var area = J('#charge_area_list').val();
	 var myurl = '<?=$root_path?>modules/or/ajax/ajax_oxygen.php?table=equipment_list&area='+area;


	J('#or_oxygen_table').flexigrid({
	url: myurl,
	dataType: 'json',
	colModel : [
		{display: 'Serial Number', name : 'serial_number', width : 130, sortable : true, align: 'left'},
		{display: 'Remaining Quantity', name : 'remaining_quantity', width : 100, sortable : true, align: 'left'},
		{display: 'Expiry Date', name : 'expiry_date', width : 100, sortable : true, align: 'left'},
		{display: 'Select', name:'select', width: 140, sortable: false, align: 'left'}
	],
	sortname: ["expiry_date"],
	sortorder: 'asc',
	useRp: true,
	rp: 5,
	resizable: true,
	domain: ['or_main_oxygen']
	});
	J('#or_oxygen_table').flexOptions({url:myurl});
	J('#or_oxygen_table').flexReload();
	J('#or_main_oxygen').jqmShow();
}

function hide_oxygen() {
	J('#or_main_oxygen').jqmHide();
}

function show_popup_equipment() {
	J('#or_main_equipment').jqmShow();
	J('#or_equipment_table').flexigrid({
	url: '<?=$root_path?>modules/or/ajax/ajax_equipment.php?table=equipment_list',
	dataType: 'json',
	colModel : [
						{display: 'Equipment', name : 'name', width : 90, sortable : true, align: 'left'},
						{display: 'Equipment Description', name : 'name', width : 180, sortable : true, align: 'left'},
						{display: 'Unit', name : 'name', width : 30, sortable : true, align: 'left'},
						{display: 'Cash', name : 'name', width : 50, sortable : true, align: 'left'},
						{display: 'Charge', name : 'name', width : 50, sortable : true, align: 'left'},
						{display: 'Is Socialized', name : 'name', width : 20, sortable : true, align: 'left'},
						{display: 'Select', name:'select', width: 100, sortable: false, align: 'left'}
						],
	sortname: ["equipment_name"],
	sortorder: 'desc',
	useRp: true,
	rp: 5,
	resizable: true,
	domain: ['or_main_equipment']
});

}

function show_popup_accommodation() {
	J('#other_accommodation').jqmShow();
	populate_room_list();
}

function populate_room_list() {
	//xajax_populate_room_list(J("#ward_list").val(), 'room_list'); removed by CHA, April 9, 2010
	$('total_rvu').value = "";
	$('multiplier').value = "";
	$('oprm_chrg').value = "";

	if($('opwardlist').value) {
		var ward = $('opwardlist').value;
	} else {
		var ward = false;
	}
	//alert("selward::"+ward)
	xajax_setORWardOptions(ward);
}

function get_room_rate() {
	xajax_get_room_rate(J("#room_list").val(), 'room_rate');
}

function empty_accommodation() {
	var rep = confirm("Delete all the accommodation charges?")
	if(rep) {
		var refno = $('opchrg_refno').value;
		var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0)
		table1.innerHTML = '<tr id="empty_accommodation_row"><td colspan="7">No room accomodation charged...</td></tr>';
		xajax_delAllOpAccommodation($('encounter_nr').value, refno);
		update_total_charge_accommodation();
	} else return FALSE;
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

function num_check(id, val) {
	if(isNaN(parseFloatEx(val)) || parseFloatEx(val)<0) {
		alert("Invalid input.");
		$(id).focus();
		return false;
	}
	$(id).value = parseFloatEx(val);
	return true;
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



function appendOrderSupplies(list, details, disabled) {
	if (!list) list = $('supplies-list');
	if (list) {
		var dBody=list.select("tbody")[0];
		if (dBody) {
			var discount = parseFloatEx($("discount").value);
			var isCash = ($('iscash1') == null) ? parseInt($('transaction_type').value) : $('iscash1').checked;
			var isSC = parseInt($("issc").value);  // Senior Citizen checking
			var totalCash, totalCharge;
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");

			if (details) {
				var id = details.id,
					//dosage=details.dosage,              //Commented by Cherry 07-06-10
					qty = parseFloatEx(details.qty),
					prcCash = parseFloatEx(details.prcCash),
					prcCharge = parseFloatEx(details.prcCharge),
					prcCashSC = parseFloatEx(details.prcCashSC),
					prcChargeSC = parseFloatEx(details.prcChargeSC),
					totalCash, totalCharge;
				netPrice = isCash ? prcCash : prcCharge;
				orig = netPrice;
				if (isSC)	{
					seniorPrice = parseFloatEx(isCash ? details.prcCashSC : prcChargeSC);
					if (seniorPrice > 0)
						netPrice = seniorPrice
				}

				// Check if item is socialized and discount is of effect
				if (parseInt(details.isSocialized)==1 && isCash) {
					if (discount==1.0)
						netPrice=0;
					else {
						if (parseFloatEx(details.prcDiscounted) < netPrice) {
							netPrice = parseFloatEx(details.prcDiscounted)
							if (netPrice == 0) netPrice = orig;
						}
					}
				}
				if (details.forcePrice) netPrice = details.forcePrice;
				tot = netPrice*qty;
				orig = isNaN(orig) ? '<span style="margin-right:5px">-</span>' : formatNumber(orig,2);
				if (items) {
					if ($('row'+id)) {
						alert('Item was already added...')
						return false;
//						var itemRow = $('row'+id),
//								itemQty = $('rowQty'+id)
//						itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty)
//						itemQty.setAttribute('prevValue',itemQty.value)

//												/** Added by omick for some sponge thingy **/
//													if ($('sponge_qty'+id)) {
//														var sponge_qty = $('sponge_qty'+id);
//														sponge_qty.value = itemQty.value;
//													}
//												/** End **/
//						qty = parseFloatEx(itemQty.value)
//						tot = netPrice*qty
//						$('rowPrcCashSC'+id).value		= prcCashSC
//						$('rowPrcChargeSC'+id).value	= prcChargeSC
//						$('rowPrcCash'+id).value			= details.prcCash
//						$('rowPrcCharge'+id).value		= details.prcCharge
//						$('rowPrc'+id).setAttribute("prevValue",orig)
//						$('qty'+id).innerHTML 				= isNaN(qty) ? '<span style="margin-right:5px">-</span>' : 'x'+formatNumber(qty,null)
//						$('rowPrc'+id).value 					= isNaN(netPrice) ? '<span style="margin-right:5px">-</span>' : formatNumber(netPrice,2)
//						$('tot'+id).innerHTML 				= isNaN(tot) ? '<span style="margin-right:5px">-</span>' : formatNumber(tot,2)
//						return true
					}
					if (!items.length) clearOrderSupplies(list)
				}

				alt = (dRows.length%2>0) ? ' class="alt"':''
				qty = isNaN(qty) ? '<span style="margin-right:5px">-</span>;' : 'x'+formatNumber(qty,null)
				prc = isNaN(netPrice) ? '<span style="margin-right:5px">-</span>' : formatNumber(netPrice,2)
				tot = isNaN(tot) ? '<span style="margin-right:5px">-</span>' : formatNumber(tot,2)

				var disabledAttrib = disabled ? 'disabled="disabled"' : ""

				var status = "";
				if(details.serveStatus!="")
				{
					if(details.serveStatus.toLowerCase()=="cmap")
						status='<td class="centerAlign"><img src="../../../images/flag_cmap.gif" title="Item charged to CMAP"/></td>';
					else if(details.serveStatus.toLowerCase()=="lingap")
						status='<td class="centerAlign"><img src="../../../images/flag_lingap.gif" title="Item charged to LINGAP"/></td>';
					else if(details.serveStatus.toLowerCase()=="paid")
						status='<td class="centerAlign"><img src="../../../images/flag_paid.gif" title="Item paid"/></td>';
					else
						status='<td class="centerAlign"><img src="../../../images/bullet.gif"/></td>';
				}else
				{
					status='<td class="centerAlign"><img src="../../../images/bullet.gif"/></td>';
				}


				src = '<tr'+alt+' id="row'+id+'">';
				var is_disabled = '';
				if (details.disable=="1")
				{
					src+='<td style="height:30px" class="centerAlign"><img class="disabled" src="../../../images/close_small.gif" border="0" style="opacity:0.4"/></td>'
					is_disabled = ' readonly="" ';
				}
				else
				{
					src+='<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="removeItemSupplies(\''+id+'\')"/></td>'
				}

				src+=
					'<td>'+id+
						'<input type="hidden" name="soc[]" id="rowSoc'+id+'" value="'+details.isSocialized+'" />'+
						'<input type="hidden" name="pdisc[]" id="rowPrcDiscounted'+id+'" value="'+details.prcDiscounted+'" />'+
						'<input type="hidden" name="pcashsc[]" id="rowPrcCashSC'+id+'" value="'+prcCashSC+'" />'+
						'<input type="hidden" name="pchargesc[]" id="rowPrcChargeSC'+id+'" value="'+prcChargeSC+'" />'+
						'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
						'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
						'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'</td>'+
					'<td><span style="color:#660000">'+details.name+'</span></td>'+
					'<td class="centerAlign" style="display:none"><input type="checkbox" name="consigned[]" value="'+id+'" '+(parseInt(details.isConsigned)==1 ? 'checked="checked"' : '')+' '+(disabled ? 'disabled="disabled"' : '')+'></td>'+
					'<td class="centerAlign" id="qty'+id+'">'+
						'<input type="text" '+is_disabled+' class="segInput" name="qty[]" id="rowQty'+id+'" classificationID="'+details.classification+'" itemID="'+id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:right"'+(disabled ? ' disabled="disabled"' : '')+' onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustQty(this)"/>'+
					'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+orig+'</td>'+
					'<td class="rightAlign">'

				if	(disabled || (parseFloatEx(details.prcDiscounted)>0 && (!isSC || (isSC && parseFloatEx(seniorPrice)>0))))
					src+= '<input type="text" class="segClearInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" readonly="readonly"/>'
				else
					src+= '<input type="text" class="segInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustPrice(this)"/>'

				src+=	'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
					status+
					'<input type="hidden" id="item_disabled'+id+'" name="item_disabled[]" value="'+details.disable+'"/>'+
				'</tr>';
				trayItems++;

								if ($('is_in_or_main_post')) {
									if (details.classification=='Sponge') {
										add_to_sponge(details);
									}
							 }
			}
			else {
				src = "<tr><td colspan=\"9\">Order list is currently empty...</td></tr>";
			}

			dBody.insert(src);

			//if (dosage) $('dosage'+id).value=dosage;      //Commented by Cherry 07-06-10



			return true;
		}
	}
	return false;
}

function changeUsageOptions(val) {
	if(val==1) {
		$("row_days").style.display = "none";
		$("row_hours").style.display = "none";
		$("row_rvu").style.display = "";
		$("row_multiplier").style.display = "";
	}else {
		$("row_days").style.display = "";
		$("row_hours").style.display = "";
		$("row_rvu").style.display = "none";
		$("row_multiplier").style.display = "none";
	}
}

J().ready(function() {
	J.blockUI({ message: "<h1>Fetching charges...</h1>" });

	J('#misc_charge')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');
	J('#or_main_equipment')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');
	J('#or_main_oxygen')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');

		J('#other_accommodation')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');

	J("#room_rate").keydown(function(e){return key_check2(e, J("#room_rate").val());});
	J("#room_hours").keydown(function(e){return key_check(e, J("#room_hours").val());});
	J("#room_days").keydown(function(e){return key_check(e, J("#room_days").val());});

	J('#misc_charge').jqm({
	overlay: 80
	});

	J('#or_main_equipment').jqm({
	overlay: 80
	});

	J('#or_main_oxygen').jqm({
	overlay: 80
	});

	J('#other_accommodation').jqm({
overlay: 80
});

	xajax_set_pharma_refno('<?=$encounter_nr?>', "WD", J('#discountid').val());	//pharma area for ward, "WD"
	xajax_populate_misc_order('misc_list', '<?=$encounter_nr?>', 'WD');
	xajax_populate_accommodation('<?=$encounter_nr?>',"WD");
	xajax_populate_room_accommodation('<?=$encounter_nr?>');
	update_total();
	update_total_misc();
	J.unblockUI();

});

</script>

<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('transaction_types', array('1'=>'Cash',	 '0' => 'Charge'));
$smarty->assign('transaction_type', $_POST['transaction_type']);

/** Form tags **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');
/** End Form tags **/

$transaction_date_display = isset($_POST['transaction_date']) ? date('F d, Y h:ia', strtotime($_POST['transaction_date'])) : date('F d, Y h:ia');
$transaction_date = isset($_POST['transaction_date']) ? date('Y-m-d H:i', strtotime($_POST['transaction_date'])) : date('Y-m-d H:i');
$smarty->assign('transaction_date_display', '<div id="transaction_date_display" class="date_display">'.$transaction_date_display.'</div>');
$smarty->assign('transaction_date', '<input type="hidden" name="transaction_date" id="transaction_date" value="'.$transaction_date.'" />');
$smarty->assign('transaction_date_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="transaction_date_picker" class="date_time_picker" />');
$smarty->assign('transaction_date_calendar_script', setup_calendar('transaction_date_display', 'transaction_date', 'transaction_date_picker'));

$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$seg_person = new Person($pid);
$person_info = $seg_person->getAllInfoArray();
//$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
//$person_name = ucwords($person_info['name_last']) . ', ' . ucwords($person_info['name_first']) . ' ' . $middle_initial;
$person_name = $db->GetOne("SELECT fn_get_person_name(".$db->qstr($pid).")");
$person_address = $db->GetOne("SELECT fn_get_complete_address(".$db->qstr($pid).")");
//$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));
//if ($person_info["zipcode"])
//	$person_address.=" ".$person_info["zipcode"];
//if ($person_info["prov_name"])
//	$person_address.=" ".$person_info["prov_name"];

$smarty->assign('pid', '<input type="hidden" name="pid" id="pid" value="'.$pid.'" />');
$smarty->assign('patient_name', $person_name);
$encounter_types = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');
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
$seg_order = new SegOrder('pharma');
$equipment = new SegEquipmentOrder();


if (isset($_POST['submitted'])) {
	$saveok_cnt = 0;
	$no_items = 0;
	$area = $_POST['pharma_area'];

	$pharma_refno = $seg_ops->get_pharma_order_mode($encounter_nr, $area); // edit
	//$equipment_refno = $equipment->get_equipment_refno_other($encounter_nr, $area); //edit

	//start saving of medicine and supplies (revised)
	$order_refno = ($pharma_refno == 0) ? $seg_order->getLastNr(date("Y-m-d")) : $pharma_refno;
	$order_data = array(
		'refno' => $order_refno,
		'encounter_nr' => $encounter_nr,
		'pharma_area' => $area, //edit
		'request_source'=> $_POST['request_source'],
		'pid' => $pid,
		'ordername' => $person_name,
		'orderaddress' => $person_address,
		'orderdate' => date('Y-m-d H:i'),
		'is_cash' => $_POST['transaction_type'],
		'amount_due' => $total,
		'is_tpl'=> 1, //temp
		'discount' => $_POST['discount'],
		'discountid' => $_POST['discountid'],
		'is_urgent' =>0, //temp
		'comments' =>'', //temp
		'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
		'create_id' =>$_SESSION['sess_temp_userid'],
		'modify_id' =>$_SESSION['sess_temp_userid'],
		'modify_time' =>date('YmdHis'),
		'create_time'=>date('YmdHis')
	);
	extract(calculate_total_pharma_order());
	$saveok = $seg_ops->saveMedsAndSuppliesCharges($pharma_refno,$order_refno,$order_data,$bulk,$post_items,$area);

	if($saveok) $saveok_cnt++;
	if(count($_POST['items'])==0)
	{
	 $no_items=1;
	}

	if ($_POST['discount'] == '' || !isset($_POST['discount'])) {
	 $discount = 0;
	}
	//end saving of medicine and supplies

	//start saving miscellaneous
	foreach($_POST["misc_item"] as $i=>$item)
	{
		if($_POST["misc_item_disabled"][$i]=="0")
		{
			$miscItems[] = $_POST["misc_item"][$i];
			$miscQty[] = $_POST["misc_qty"][$i];
			$miscPrc[] = $_POST["misc_prc"][$i];
			$miscType[] = $_POST["misc_account_type"][$i];
		}
	}
	$array = array(
		'charge_date' => $_POST['transaction_date'],
		'encounter_nr' => $encounter_nr,
		'misc' => $miscItems,
		'quantity' => $miscQty,
		'price' => $miscPrc,
		'account_type' => $miscType,
		'is_cash' => $_POST['transaction_type'],
		'area' => $area
	); //edit
	//$saveok = $seg_ormisc->saveMiscCharges($array);
	$saveok = $seg_ormisc->saveMiscellaneous($array);
	if($saveok) $saveok_cnt++;
	if(count($_POST['misc'])==0)
	{
	 $no_items=1;
	}
//end saving miscellaneous
	//echo "savecnt=".$saveok_cnt." no_itms=".$no_items;
	if($saveok_cnt<=1 && $no_items==1)
	{
		 $smarty->assign('sysErrorMessage','<strong>Error:</strong> Cannot save OR main charges.<br>'.$seg_ormisc->getErrorMsg()."<br><br>LAST SQL:".$seg_ormisc->getLastQuery());
	}
	else if($saveok_cnt>1)
	{
		 $smarty->assign('sysInfoMessage','OR Main charges successfully submitted.');
	}
}

//added by cha, june 9, 2010
$service_type_code = array (49,50,51,52,53,54,"");
$service_type_name = array ("Physical Medicine & Rehab", "Dental", "Orthopedics", "ENT-HNS", "Pediatrics", "Special Lab", "Other");
$service_type_options = "<option value=''>ALL ACCOUNTS</option";
for($i=0;$i<count($service_type_code);$i++)
{
	$service_type_options.="<option value='".$service_type_code[$i]."'>".$service_type_name[$i]."</option>";
}
$smarty->assign('miscServiceTypes', $service_type_options);
//end cha

$smarty->assign('supplies_add_button','<a href="javascript:void(0)" id="add_item" onclick="return openOrderTray2();"></a>');
$smarty->assign('supplies_empty_button','<a href="javascript:void(0)" id="empty_item" onclick="emptyTraySupplies()"></a>');
$smarty->assign('add_equipment', '<a href="javascript:void(0)" id="add_equipment" onclick="show_popup_equipment()"></a>');
$smarty->assign('empty_equipment', '<a href="javascript:void(0)" id="empty_equipment" onclick="empty_equipment()"></a>');
$smarty->assign('add_room', '<a href="javascript:void(0)" id="add_room" onclick="show_popup_accommodation()"></a>');
$smarty->assign('empty_room', '<a href="javascript:void(0)" id="empty_room" onclick="empty_accommodation()"></a>');
$smarty->assign('add_misc', '<a href="javascript:void(0)" id="add_misc" onclick="show_popup_misc()"></a>');
$smarty->assign('empty_misc', '<a href="javascript:void(0)" id="empty_misc" onclick="empty_misc()"></a>');

$smarty->assign('discountid', '<input type="hidden" name="discountid" id="discountid" value="'.$social_service_details['discountid'].'" />');
$smarty->assign('discount', '<input type="hidden" name="discount" id="discount" value="'.$social_service_details['discount'].'" />');

$smarty->assign('issc', '<input type="hidden" name="issc" id="issc" value="'.$is_sc.'" />');

$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
$smarty->assign('billdate', '<input type="hidden" name="billdate" id="billdate" value="'.date("Y-m-d H:i:s").'" />');
$confine_type = $db->GetOne("select confinetype_id from seg_encounter_confinement where encounter_nr='".$encounter_nr."'");
$smarty->assign('confinement_type', '<input type="hidden" id="confine_type" name="confine_type" value="'.$confine_type.'"');

$number_of_pages = array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30');
$smarty->assign('number_of_pages', $number_of_pages);
$smarty->assign('page_number', '<input type="text" id="page_number" name="page_number" />');
$smarty->assign('search_field', '<input type="text" id="search_field" name="search_field" />');

$smarty->assign('other_charges_submit', '<input type="submit" id="or_main_submit" value="" />');
$smarty->assign('other_charges_cancel', '<a href="'.$breakfile.'" id="or_main_cancel"></a>');

$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode'];
$smarty->assign('mode', '<input type="hidden" name="mode" value="'.$mode.'" />');

$care_ward = new Ward();
$array = $care_ward->get_all_active_wards_as_pair('nr', 'name');
$smarty->assign('ward_list', $array);

$smarty->assign('close_equipment', '<a href="javascript:void(0)" id="close_equipment" class="jqmClose"></a>');
$smarty->assign('close_misc_charge', '<a href="javascript:void(0)" id="close_misc_charge" class="jqmClose"></a>');
$smarty->assign('close_oxygen', '<a href="javascript:void(0)" id="close_oxygen" class="jqmClose"></a>');
$smarty->assign('close_other_accommodation', '<a href="javascript:void(0)" id="close_other_accommodation" class="jqmClose"></a>');
$smarty->assign('pharma_area', '<input type="hidden" id="pharma_area" name="pharma_area" value="WD"/>'); //pharma area for ward, "WD"

$smarty->assign('add_accommodation', '<a href="javascript:void(0)" id="add_accommodation" onclick="validate_accommodation()"></a>');
$smarty->assign('add_accommodation_cancel', '<a href="javascript:void(0)" id="cancel_accommodation" class="jqmClose"></a>');
$smarty->assign('room_list', array(0=>'No room is available under this ward'));
$smarty->assign('room_rate', '<input type="text" id="room_rate" name="room_rate" class="short" />');
$smarty->assign('room_days', '<input type="text" id="room_days" name="room_days" />');
$smarty->assign('room_hours', '<input type="text" id="room_hours" name="room_hours" />');
$smarty->assign('room_type', '<input type="hidden" id="room_type" name="room_type" />');

#--added by CHa, April 9,2010 (copied from billing)
// Select operation procedures charged with accommodation ...
$smarty->assign('sSelectOps', '<a title="Procedures" href="#"><img id="ops_selected"
		onclick="openSelectICPM();"
		 onmouseout="nd();"
		 class="segSimulatedLink" src="'.$root_path.'gui/img/common/default/task_tree.gif"
		 border="0" align="absmiddle" ></a>');
#--end CHa--

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','or/or_charges.tpl'); //Assign the or_main template to the frameset
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

function calculate_total_pharma_order() {
	$bulk = array();
	$orig = $_POST['iscash'] ? $_POST['pcash'] :  $_POST['pcharge'];
	$total = 0;
	foreach ($_POST["items"] as $i=>$v) {
		if($_POST["item_disabled"][$i]=="0")
		{
			$consigned = in_array($v, $_POST['consigned']) ? '1' : '0';
			$bulk[] = array(
			"item"=>$_POST["items"][$i],
			"qty"=>$_POST["qty"][$i],
			"price_cash"=>parseFloatEx($_POST["prc"][$i]),
			"price_charge"=>parseFloatEx($_POST["prc"][$i]),
			"consigned"=>$consigned, "price_orig"=>$orig[$i]
			);
			$total += (parseFloatEx($_POST["prc"][$i]) * (float) $_POST["qty"][$i]);
			$items[]=$_POST["items"][$i];
		}
	}
	$post_items = array ( "items" => $items);
	return array('total' => $total, 'bulk' => $bulk, 'post_items'=>$post_items);
}

