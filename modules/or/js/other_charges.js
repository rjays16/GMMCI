function retrieve_misc(table, details)
{
	 if(!table) table = $('misc_list');
	 if(table)
	 {
			var dBody=$(table).select("tbody")[0];
			if(dBody){
				var table1 = $(table).getElementsByTagName('tbody').item(0);
				if ($('empty_misc_row')) {
					table1.removeChild($('empty_misc_row'));
				}
				var dRows = dBody.getElementsByTagName("tr");
				if(details)
				{
					var total = parseFloat(details.quantity*details.price);
					alt = (dRows.length%2>0) ? ' class="alt"':''
					if(details.status!="")
					{
						if(details.status.toLowerCase()=="cmap")
							status='<td class="centerAlign"><img src="../../../images/flag_cmap.gif" title="Item charged to CMAP"/></td>';
						else if(details.status.toLowerCase()=="lingap")
							status='<td class="centerAlign"><img src="../../../images/flag_lingap.gif" title="Item charged to LINGAP"/></td>';
						else if(details.status.toLowerCase()=="paid")
							status='<td class="centerAlign"><img src="../../../images/flag_paid.gif" title="Item paid"/></td>';
						else
							status='<td class="centerAlign"><img src="../../../images/bullet.gif"/></td>';
					}else
					{
						status='<td class="centerAlign"><img src="../../../images/bullet.gif"/></td>';
					}

					var is_disabled="";
					if(details.disable=="1")
						is_disabled = '<td style="height:30px" class="centerAlign"><img class="disabled" src="../../../images/close_small.gif" border="0" style="opacity:0.4"/></td>';
					else
						is_disabled = '<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="remove_misc_charge(\''+details.code+'\')"/></td>';

					rowSrc = '<tr class="'+alt+'" id="misc_row'+details.code+'">'+
							is_disabled+
							'<td>'+
								'<span style="color:#660000">'+details.code+'</span>'+
								'<input type="hidden" name="misc_item[]" id="misc_item'+details.code+'" value="'+details.code+'"/>'+
								'<input type="hidden" name="misc_account_type[]" id="misc_account_type'+details.code+'" value="'+details.account_type+'"/>'+
								'<input type="hidden" name="misc_prc[]" id="misc_prc'+details.code+'" value="'+details.price+'"/>'+
							'</td>'+
							'<td><span style="color:#660000">'+details.type_name+'</span></td>'+
							'<td><span style="color:#660000">'+details.name+'</span></td>'+
							'<td class="centerAlign">'+
								'<input type="text" class="segInput" name="misc_qty[]" id="misc_qty'+details.code+'" value="'+details.quantity+'" style="width:57px;text-align:right" onblur="reCalcTotal(\''+details.code+'\');"/>'+
							'</td>'+
							'<td class="rightAlign" id="row_misc_prc'+details.code+'">'+formatNumber(details.price,2)+'</td>'+
							'<td class="rightAlign" id="row_misc_total'+details.code+'">'+formatNumber(total,2)+'</td>'+
							status+
							'<input type="hidden" id="misc_item_disabled'+details.code+'" name="misc_item_disabled[]" value="'+details.disable+'"/>'+
						'</tr>';
				}else
				{
					rowSrc = '<tr id="empty_misc_row"><td colspan="9">Miscellaneous charges is empty...</td></tr>';
				}
				dBody.insert(rowSrc);
				update_total_misc();
			}
	 }
}
/*function retrieve_misc(table, details) {

if ($('misc'+details.code)) {
	alert('Existing');
}
else {
var table1 = $(table).getElementsByTagName('tbody').item(0);
if ($('empty_misc_row')) {
	table1.removeChild($('empty_misc_row'));
}
var row = document.createElement("tr");

var total_charge =  details.quantity * details.price;

var array_elements = new Array();
if (details.is_removable == 1) {
	var class_disabled="";
	if(details.status!="")
	{
		class_disabled = "disabled";
	}
	array_elements.push({type: 'img', src: '../../../images/close_small.gif', class_name: class_disabled, align: 'left', title: ''});
}

var status_source = "";
var status_title = "";
if(details.status.toLowerCase()=="cmap")
{
	status_source = "../../../images/flag_cmap.gif";
	status_title = "Item charged to CMAP";
}
else if(details.status.toLowerCase()=="lingap")
{
	status_source = "../../../images/flag_lingap.gif";
	status_title = "Item charged to LINGAP";
}
else if(details.status.toLowerCase()=="paid")
{
	status_source = "../../../images/flag_paid.gif";
	status_title = "Item paid";
}else
{
	status_source = "../../../images/bullet.gif";
}

array_elements.push({type: 'td_text', name: details.code},
											{type: 'td_text', name: details.type_name},
											{type: 'td_text', name: details.name},
											{type: (details.is_removable==1) ? 'input' : 'td_text', name: (details.is_removable==1) ? 'quantity[]' : details.quantity, text_value: details.quantity, id: 'quantity'+details.code, width: '57px', cellAlign:'center', textAlign:'right'},
											{type: 'td_text', name: details.price, align:'right'},
											{type: 'td_text', name: total_charge.toFixed(2), id:'total_misc_td'+details.code, align:'right'},
											{type: 'img', src: status_source, title: status_title, align: 'center'}
											);


for (var i=0; i<array_elements.length; i++) {
	var cell = document.createElement("td");
	if (array_elements[i].type == 'td_text') {
		cell.appendChild(document.createTextNode(array_elements[i].name));
		if (array_elements[i].id) {
			cell.id = array_elements[i].id;
		}
	}
	if(array_elements[i].type == 'input')  {
		element = document.createElement(array_elements[i].type)
		cell.appendChild(element);
		element.name = array_elements[i].name;
		element.type = "text";
		element.className = "segInput";
		//element.addEventListener("change", function() {update_misc_total(details.code)}, false);
		if (array_elements[i].cellAlign) {
			cell.setAttribute('align', array_elements[i].cellAlign);
		}
		if (array_elements[i].width) {
			element.style.width = array_elements[i].width;
		}
		if (array_elements[i].textAlign) {
			element.style.textAlign = array_elements[i].textAlign;
		}
		if (array_elements[i].text_value) {
			element.value = array_elements[i].text_value;
		}
		if (array_elements[i].id) {
			element.id = array_elements[i].id;
		}
		element.addEventListener("blur", function() {
			var qty = $('quantity'+details.code).value;
			var price = $('misc_prc'+details.code).value;
			var new_total = parseFloatEx(qty) * parseFloatEx(price);
			$('total_misc_td'+details.code).innerHTML = formatNumber(new_total,2);
			$('misc_qty'+details.code).value = qty;
			update_total_misc();
		}, false);
	}

	if (array_elements[i].type == 'img') {
		img = document.createElement("img");
		cell.appendChild(img);
		img.src = array_elements[i].src;
		img.title = array_elements[i].title;	//cha added, july 7, 2010
		img.align = array_elements[i].align;  //cha added, july 7, 2010
		img.style.cursor = "pointer";
		if(details.status!="")
		{
			img.addEventListener("click", function() {remove_misc_charge(details.code)}, false);
			img.className = array_elements[i].class_name;  //cha added, july 7, 2010
		}
		else
		{
			array_elements[i].class_name = "";
			img.className = array_elements[i].class_name;  //cha added, july 7, 2010
			img.addEventListener("click", function() {return false});
		}
	}

	if (array_elements[i].align) {
		cell.align = array_elements[i].align;
	}
	row.appendChild(cell);
}
row.id = 'misc_row'+details.code;


$(table).getElementsByTagName('tbody').item(0).appendChild(row);

if (details.is_removable == 1) {
var hidden_elements = [{name: 'misc_item[]', value: details.code, id: 'misc_item'+details.code},
											{name: 'misc_prc[]', value: details.price, id: 'misc_prc'+details.code},
											{name: 'misc_qty[]', value: details.quantity, id: 'misc_qty'+details.code},
											{name: 'misc_account_type[]', value: details.account_type, id: 'misc_account_type'+details.code}];

for (var i=0; i<hidden_elements.length; i++) {
	var hidden_array = document.createElement('input');
	hidden_array.type = 'hidden';
	hidden_array.name = hidden_elements[i].name;
	hidden_array.value = hidden_elements[i].value;
	if (hidden_elements[i].id) {
		hidden_array.id = hidden_elements[i].id;
	}
	document.forms[0].appendChild(hidden_array);
}
}

update_total_misc();
}

}*/

function update_total_misc() {
	//to do: discounts

	var misc = document.getElementsByName('misc_item[]');
	var quantity = document.getElementsByName('misc_qty[]');
	var original_misc_price = document.getElementsByName('misc_prc[]');

	var sub_total = 0;
	var discount_total = 0;
	var net_total = 0;

	for (var i=0; i<misc.length; i++) {
		sub_total += parseFloat(quantity[i].value * original_misc_price[i].value);
		//discount_total += parseFloat(number_of_usage[i].value * adjusted_price[i].value);
		//net_total += parseFloat(account_total[i].value);
	}
	discount = 0;

	J('#misc_subtotal').html(formatNumber(sub_total, 2));
	J('#misc_discount_total').html('('+formatNumber(0, 2)+')');
	J('#misc_net_total').html(formatNumber(sub_total, 2));

}

function append_empty_misc() {
	var table1 = $('misc_list').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	var cell = document.createElement("td");
	row.id = "empty_misc_row";
	cell.appendChild(document.createTextNode('Miscellaneous charges is empty...'));

	cell.colSpan = "8";
	row.appendChild(cell);
	$('misc_list').getElementsByTagName('tbody').item(0).appendChild(row);
}

function update_misc_total(id) {

	 J('#total_misc_td'+id).html((J('#quantity'+id).val() * J('#original_misc_price'+id).val()).toFixed(2));
	 update_total_misc();
}

function addServiceToList(details) {

	if ($('misc_row'+details.id)) {
		alert('This item is already in the list.');
		return false;
	}
	else
	{
		var table = $('misc_list');
		if(table){
			var dBody = table.select("tbody")[0];
			if ($('empty_misc_row')) {
				table.getElementsByTagName('tbody').item(0).removeChild($('empty_misc_row'));
			}
			if(dBody){
				var dRows = dBody.getElementsByTagName("tr");
				if(details){
					var total = parseFloat(details.qty*details.price);
					alt = (dRows.length%2>0) ? ' class="alt"':''
					rowSrc = '<tr class="'+alt+'" id="misc_row'+details.id+'">'+
							'<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="remove_misc_charge(\''+details.id+'\')"/></td>'+
							'<td>'+
								'<span style="color:#660000">'+details.id+'</span>'+
								'<input type="hidden" name="misc_item[]" id="misc_item'+details.id+'" value="'+details.id+'"/>'+
								'<input type="hidden" name="misc_account_type[]" id="misc_account_type'+details.id+'" value="'+details.misc_type+'"/>'+
								'<input type="hidden" name="misc_prc[]" id="misc_prc'+details.id+'" value="'+details.price+'"/>'+
							'</td>'+
							'<td><span style="color:#660000">'+details.misc_type_name+'</span></td>'+
							'<td><span style="color:#660000">'+details.name+'</span></td>'+
							'<td class="centerAlign">'+
								'<input type="text" class="segInput" name="misc_qty[]" id="misc_qty'+details.id+'" value="'+details.qty+'" style="width:57px;text-align:right" onblur="reCalcTotal(\''+details.id+'\');"/>'+
							'</td>'+
							'<td class="rightAlign" id="row_misc_prc'+details.id+'">'+formatNumber(details.price,2)+'</td>'+
							'<td class="rightAlign" id="row_misc_total'+details.id+'">'+formatNumber(total,2)+'</td>'+
							'<td class="centerAlign"><img src="../../../images/bullet.gif"/></td>'+
							'<input type="hidden" id="misc_item_disabled'+details.id+'" name="misc_item_disabled[]" value="'+details.disable+'"/>'+
						'</tr>';
				}else
				{
					rowSrc = '<tr id="empty_misc_row"><td colspan="9">Miscellaneous charges is empty...</td></tr>';
				}
				dBody.insert(rowSrc);
				update_total_misc();
			}
		}
	}
}

function reCalcTotal(id)
{
	var qty = $('misc_qty'+id).value;
	var price = $('row_misc_prc'+id).innerHTML;
	var new_total = parseFloatEx(qty) * parseFloatEx(price);
	$('row_misc_total'+id).innerHTML = formatNumber(new_total,2);
	update_total_misc();
}

function remove_misc_charge(id) {
	var table1 = $('misc_list').getElementsByTagName('tbody').item(0);
	table1.removeChild($('misc_row'+id));

	if (!document.getElementsByName('misc_item[]') || document.getElementsByName('misc_item[]').length <= 0) {
		append_empty_misc();
	}
	update_total_misc();
}

function addSlashes(str) {
	var ret = str.replace('"','\\"');
	return ret.replace("'","\\'");
}

//added by cHA, April 9, 2010 (function copied from billing)
function jsOpAccChrgOptionsChange(obj, value){
	if(obj.id == 'opwardlist') {
		//$('opw_nr').value  = value;
		xajax_setORWardRooms(value);
	}
	/*else {
		$('opr_nr').value = value;
	}*/
}

function initOPsTakenArray() {
	$('opstaken').innerHTML = '';
}

function saveOPTaken(rowval) {
	$('opstaken').innerHTML += rowval;
}

function updateRVUTotal() {
	var ops = $('opstaken').innerHTML;
	var enc_nr = $('encounter_nr').value;
	var bill_dt = $('billdate').value;
	var type = $('confine_type').value;
	xajax_updateRVUTotal(ops, enc_nr, bill_dt, type);
}

function applyRVUandMult(r, m, c) {
	var n = 0;
	if (Number(m) != Number(0)) {
		n = Number($('total_rvu').value);
		$('total_rvu').value = formatNumber(n + Number(r),2);
		$('multiplier').value = formatNumber(Number(m),2);
		//$('oprm_chrg').value = formatNumber(Number(c),2);
		$('oprm_chrg').value = c;
	}
	else {
		$('total_rvu').value = '';
		$('multiplier').value = '';
		$('oprm_chrg').value = '';
	}
}

function remRVUandMult(r) {
	var n = 0;

	n = Number($('total_rvu').value);
	$('total_rvu').value = formatNumber(n- Number(r),2);
}
//end CHa

function validate_accommodation()
{
	var is_error = false;
	if (J('#opwardlist').val() == 0) {
		alert('Please select an O.R. ward!');
		is_error = true;
	}
	else if(J('#orlist').val() == 0)
	{
		alert('Please select an operating room!');
		is_error = true;
	}
	/*else if(J('#total_rvu').val() == '' || J('#multiplier').val() == '')
	{
		//alert("You must select the procedure(s) associated with this accommodation!\nClick the icon at the right of total RVU!");
		//is_error = true;
		xajax_saveAdditionalRoomCharge($('encounter_nr').value, $('oprm_chrg').value, $('opwardlist').value,$('orlist').value);
		return false;
	}  */
	else if (isNaN(parseFloat(J('#oprm_chrg').val())) || parseFloat(J('#oprm_chrg').val())<0) {
		alert('Invalid O.R. accomodation charge!');
		is_error = true;
	}
	else if(J('#oprm_chrg').val() == 0)
	{
		alert("O.R. accommodation charge must be nonzero!");
		is_error = true;
	}
	var opward_nr = J('#opwardlist').val();
	var oproom_nr = J('#orlist').val();
	var table = 'accommodation_list';
	var accom_id = opward_nr+""+oproom_nr;
	if ($('accommodation_row'+accom_id)) {
		alert('Existing');
		is_error = true;
	}

	if (!is_error) {

		var sel = document.getElementsByName('or_usage');
		for(i=0;i<sel.length;i++) {
			if(sel[i].checked && sel[i].value==1)
			{
					//copied from billing
					var ops = $('opstaken').innerHTML;
					var data = new Object();
					data.opacc_enc_nr = $('encounter_nr').value;
					data.opw_nr = $('opwardlist').value;
					data.opr_nr = $('orlist').value;
					data.total_rvu = $('total_rvu').value;
					data.multiplier = $('multiplier').value;
					data.oprm_chrg = $('oprm_chrg').value;

					xajax_saveORAccommodation(data, $('billdate').value, ops);
					//end billing code
			}
			else if(sel[i].checked && sel[i].value==0)
			{
					if($('total_days').value<=0) {
						alert("Number of days should be greater than zero.")
						$('total_days').focus();
						return false;
					} else {
						 xajax_saveAdditionalRoomCharge($('encounter_nr').value, $('oprm_chrg').value,
								$('opwardlist').value, $('orlist').value, $('total_days').value, $('total_hours').value);
					}
			}
		}
	}
}

function populate_accommodation(details) {
	var oproom_nr = details.oproom_nr;
	var opward_nr = details.opward_nr;
	var bill_frmdte = details.bill_frmdte;
	var accom_id = opward_nr+""+oproom_nr;
	var table = 'accommodation_list';

		var table1 = $(table).getElementsByTagName('tbody').item(0);
		if ($('empty_accommodation_row')) {
			table1.removeChild($('empty_accommodation_row'));
		}

		if(details.refno) {
			var total_rvu = details.rvu;
			var multiplier = details.multiplier;
			var charge =  details.charge;
			var ordr_name = details.ward_name+" Room#:"+details.room_name;
			var total_days = "n/a";
			var total_hours = "n/a";

			var rowSrc =
			'<tr class="alt" id="accommodation_row'+accom_id+'">'+
					'<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="remove_accommodation2(\''+accom_id+'\',\''+bill_frmdte+'\',\''+oproom_nr+'\')"/></td>'+
					'<td class="leftAlign"><span style="color:#660000">'+ordr_name+'</span></td>'+
					'<td class="centerAlign"><span style="color:#660000">'+total_rvu+'</span></td>'+
					'<td class="centerAlign"><span style="color:#660000">'+formatNumber(multiplier,2)+'</span></td>'+
					'<td class="centerAlign"><input type="text" class="segInput" style="text-align:right;width:50%"  id="total_days'+accom_id+'" name="total_days" value="'+total_days+'" disabled="disabled"/></td>'+
					'<td class="centerAlign"><input type="text" class="segInput" style="text-align:right;width:50%"  id="total_hours'+accom_id+'" name="total_hours" value="'+total_hours+'" disabled="disabled"/></td>'+
					'<td class="centerAlign"><input type="text"  class="segInput" style="text-align:right;width:80%" id="room_charge'+accom_id+'" name="room_charge[]" value="'+formatNumber(charge,2)+'" onchange="update_total_charge_accommodation();"/>'+
						'<input type="hidden" id="opward_nr'+accom_id+'" name="opward_nr[]"  value="'+opward_nr+'"/>'+
						'<input type="hidden" id="oproom_nr'+accom_id+'" name="oproom_nr[]"  value="'+oproom_nr+'"/>'+
						'<input type="hidden" id="total_rvu'+accom_id+'" name="total_rvu[]"  value="'+total_rvu+'"/>'+
						'<input type="hidden" id="multiplier'+accom_id+'" name="multiplier[]"  value="'+multiplier+'"/>'+
						'<input type="hidden" id="total_accommodation'+accom_id+'" name="total_accommodation[]"  value="'+charge+'"/>'+
						'<input type="hidden" id="opchrg_refno" name="opchrg_refno"  value="'+details.refno+'"/>'+
					'</td>'+
			'</tr>';

		} else {
			var rowSrc =
			"<tr id='empty_accommodation_row'>"+
				"<td colspan='7'>No room accomodation charged...</td>"+
			"</tr>";
		}
		table1.insert(rowSrc);
		update_total_charge_accommodation();
}

function add_accommodation(bill_frmdte, opcode, refno) {
	var opward_nr = J('#opwardlist').val();
	var oproom_nr = J('#orlist').val();
	var table = 'accommodation_list';
	var accom_id = opward_nr+""+oproom_nr;
	if ($('accommodation_row'+accom_id)) {
		alert('Existing');
	}
	else {
		var table1 = $(table).getElementsByTagName('tbody').item(0);
		if ($('empty_accommodation_row')) {
			table1.removeChild($('empty_accommodation_row'));
		}

		if(refno) {
			var total_rvu = parseInt(J('#total_rvu').val());
			var multiplier = parseInt(J('#multiplier').val());
			var charge = J('#oprm_chrg').val();
			//var ordr_name = ""+J('#orlist :selected').text();
			var w = $('opwardlist').selectedIndex;
			var ward_name = $('opwardlist').options[w].text;
			var r = $('orlist').selectedIndex;
			var room_name = $('orlist').options[r].text;
			var ordr_name = ward_name+" Room#:"+room_name;
			var total_days = "n/a";
			var total_hours = "n/a";

			var rowSrc =
			'<tr class="alt" id="accommodation_row'+accom_id+'">'+
					'<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="remove_accommodation2(\''+accom_id+'\',\''+bill_frmdte+'\',\''+oproom_nr+'\')"/></td>'+
					'<td class="leftAlign"><span style="color:#660000">'+ordr_name+'</span></td>'+
					'<td class="centerAlign"><span style="color:#660000">'+total_rvu+'</span></td>'+
					'<td class="centerAlign"><span style="color:#660000">'+formatNumber(multiplier,2)+'</span></td>'+
					'<td class="centerAlign"><input type="text" class="segInput" style="text-align:right;width:50%"  id="total_days'+accom_id+'" name="total_days" value="'+total_days+'" disabled="disabled"/></td>'+
					'<td class="centerAlign"><input type="text" class="segInput" style="text-align:right;width:50%"  id="total_hours'+accom_id+'" name="total_hours" value="'+total_hours+'" disabled="disabled"/></td>'+
					'<td class="centerAlign"><input type="text"  class="segInput" style="text-align:right;width:80%" id="room_charge'+accom_id+'" name="room_charge[]" value="'+formatNumber(charge,2)+'" onchange="update_total_charge_accommodation();"/>'+
						'<input type="hidden" id="opward_nr'+accom_id+'" name="opward_nr[]"  value="'+opward_nr+'"/>'+
						'<input type="hidden" id="oproom_nr'+accom_id+'" name="oproom_nr[]"  value="'+oproom_nr+'"/>'+
						'<input type="hidden" id="total_rvu'+accom_id+'" name="total_rvu[]"  value="'+total_rvu+'"/>'+
						'<input type="hidden" id="multiplier'+accom_id+'" name="multiplier[]"  value="'+multiplier+'"/>'+
						'<input type="hidden" id="total_accommodation'+accom_id+'" name="total_accommodation[]"  value="'+charge+'"/>'+
						'<input type="hidden" id="opchrg_refno" name="opchrg_refno"  value="'+refno+'"/>'+
					'</td>'+
			'</tr>';;

		} else {
			var rowSrc =
			"<tr id='empty_accommodation_row'>"+
				"<td colspan='7'>No room accomodation charged...</td>"+
			"</tr>";
		}
		table1.insert(rowSrc);
		update_total_charge_accommodation();
	}

}

function populate_room_accommodation(details) {
	var oproom_nr = details.oproom_nr;
	var opward_nr = details.opward_nr;
	var bill_frmdte = details.chrge_dte;
	var accom_id = opward_nr+""+oproom_nr;
	var table = 'accommodation_list';

		var table1 = $(table).getElementsByTagName('tbody').item(0);
		if ($('empty_accommodation_row')) {
			table1.removeChild($('empty_accommodation_row'));
		}

		if(details.encounter_nr) {
			var total_rvu = "n/a";
			var multiplier = "n/a";
			var charge =  details.charge;
			var ordr_name = details.ward_name+" Room#:"+details.room_name;
			var total_days = details.days;
			var total_hours = details.hours;

			var rowSrc =
			'<tr class="alt" id="accommodation_row'+accom_id+'">'+
					'<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="remove_room_accommodation(\''+accom_id+'\',\''+details.encounter_nr+'\',\''+opward_nr+'\',\''+oproom_nr+'\');"/></td>'+
					'<td class="leftAlign"><span style="color:#660000">'+ordr_name+'</span></td>'+
					'<td class="centerAlign"><span style="color:#660000">'+total_rvu+'</span></td>'+
					'<td class="centerAlign"><span style="color:#660000">'+multiplier+'</span></td>'+
					'<td class="centerAlign"><input type="text" class="segInput" style="text-align:right;width:50%"  id="total_days'+accom_id+'" name="total_days" value="'+total_days+'" /></td>'+
					'<td class="centerAlign"><input type="text" class="segInput" style="text-align:right;width:50%"  id="total_hours'+accom_id+'" name="total_hours" value="'+total_hours+'" /></td>'+
					'<td class="centerAlign"><input type="text"  class="segInput" style="text-align:right;width:80%" id="room_charge'+accom_id+'" name="room_charge[]" value="'+formatNumber(charge,2)+'" onchange="update_total_charge_accommodation();"/>'+
						'<input type="hidden" id="opward_nr'+accom_id+'" name="opward_nr[]"  value="'+opward_nr+'"/>'+
						'<input type="hidden" id="oproom_nr'+accom_id+'" name="oproom_nr[]"  value="'+oproom_nr+'"/>'+
						'<input type="hidden" id="total_rvu'+accom_id+'" name="total_rvu[]"  value="'+total_rvu+'"/>'+
						'<input type="hidden" id="multiplier'+accom_id+'" name="multiplier[]"  value="'+multiplier+'"/>'+
						'<input type="hidden" id="total_accommodation'+accom_id+'" name="total_accommodation[]"  value="'+charge+'"/>'+
					'</td>'+
			'</tr>';

		} else {
			var rowSrc =
			"<tr id='empty_accommodation_row'>"+
				"<td colspan='7'>No room accomodation charged...</td>"+
			"</tr>";
		}
		table1.insert(rowSrc);
		update_total_charge_accommodation();
}

function add_room_accommodation(bill_frmdte, charge, enc_nr) {
	var opward_nr = J('#opwardlist').val();
	var oproom_nr = J('#orlist').val();
	var table = 'accommodation_list';
	var accom_id = opward_nr+""+oproom_nr;
	if ($('accommodation_row'+accom_id)) {
		alert('Existing');
	}
	else {
		var table1 = $(table).getElementsByTagName('tbody').item(0);
		if ($('empty_accommodation_row')) {
			table1.removeChild($('empty_accommodation_row'));
		}

		if(enc_nr) {
			var total_rvu = "n/a";
			var multiplier = "n/a";
			var charge = charge;
			var w = $('opwardlist').selectedIndex;
			var ward_name = $('opwardlist').options[w].text;
			var r = $('orlist').selectedIndex;
			var room_name = $('orlist').options[r].text;
			var ordr_name = ward_name+" Room#:"+room_name;
			var total_days = parseInt(J('#total_days').val());
			var total_hours = parseInt(J('#total_hours').val());

			var rowSrc =
			'<tr class="alt" id="accommodation_row'+accom_id+'">'+
					'<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="remove_accommodation2(\''+accom_id+'\',\''+bill_frmdte+'\',\''+oproom_nr+'\')"/></td>'+
					'<td class="leftAlign"><span style="color:#660000">'+ordr_name+'</span></td>'+
					'<td class="centerAlign"><span style="color:#660000">'+total_rvu+'</span></td>'+
					'<td class="centerAlign"><span style="color:#660000">'+multiplier+'</span></td>'+
					'<td class="centerAlign"><input type="text" class="segInput" style="text-align:right;width:50%"  id="total_days'+accom_id+'" name="total_days" value="'+total_days+'"/></td>'+
					'<td class="centerAlign"><input type="text" class="segInput" style="text-align:right;width:50%"  id="total_hours'+accom_id+'" name="total_hours" value="'+total_hours+'" /></td>'+
					'<td class="centerAlign"><input type="text"  class="segInput" style="text-align:right;width:80%" id="room_charge'+accom_id+'" name="room_charge[]" value="'+formatNumber(charge,2)+'" onchange="update_total_charge_accommodation();"/>'+
						'<input type="hidden" id="opward_nr'+accom_id+'" name="opward_nr[]"  value="'+opward_nr+'"/>'+
						'<input type="hidden" id="oproom_nr'+accom_id+'" name="oproom_nr[]"  value="'+oproom_nr+'"/>'+
						//'<input type="hidden" id="total_rvu'+accom_id+'" name="total_rvu[]"  value="'+total_rvu+'"/>'+
						//'<input type="hidden" id="multiplier'+accom_id+'" name="multiplier[]"  value="'+multiplier+'"/>'+
						'<input type="hidden" id="total_accommodation'+accom_id+'" name="total_accommodation[]"  value="'+charge+'"/>'+
					'</td>'+
			'</tr>';;

		} else {
			var rowSrc =
			"<tr id='empty_accommodation_row'>"+
				"<td colspan='7'>No room accomodation charged...</td>"+
			"</tr>";
		}
		table1.insert(rowSrc);
		update_total_charge_accommodation();
	}

}

function append_empty_accommodation() {
	var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	var cell = document.createElement("td");
	row.id = "empty_accommodation_row";
	cell.appendChild(document.createTextNode('Additional accommodation empty...'));

	cell.colSpan = "7";
	row.appendChild(cell);
	$('accommodation_list').getElementsByTagName('tbody').item(0).appendChild(row);
}

function remove_accommodation(id) {
	var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0);
	table1.removeChild($('accommodation_row'+id));
	 document.forms[0].removeChild($('total_accommodation'+id));
	var hidden_array = document.createElement('input');
	hidden_array.type = 'hidden';
	hidden_array.name = 'removed_room_nr[]';
	hidden_array.value = id;
	hidden_array.id = 'removed_room_nr'+id;
	update_total_charge_accommodation();
	document.forms[0].appendChild(hidden_array);
	if (table1.getElementsByTagName('tr').length <= 0) {
		append_empty_accommodation();
	}
}

function remove_accommodation2(id, bill_frmdte, opcode) {
	var rep = confirm("Delete this accomodation charge?")
	if(rep) {
			var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0);
			table1.removeChild($('accommodation_row'+id));

			//added by CHa, April 12,2010 *from billing
			xajax_delOpAccommodation($('encounter_nr').value, bill_frmdte, opcode);
			//end CHa

			update_total_charge_accommodation();
			if (table1.getElementsByTagName('tr').length <= 0) {
				append_empty_accommodation();
			}
	}
	else return false;
}

function remove_room_accommodation(id, enc_nr, ward_nr, room_nr)
{
	var rep = confirm("Delete this accomodation charge?")
	if(rep) {
			var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0);
			table1.removeChild($('accommodation_row'+id));

			xajax_deleteRoomAccommodation(enc_nr, ward_nr, room_nr);

			update_total_charge_accommodation();
			if (table1.getElementsByTagName('tr').length <= 0) {
				append_empty_accommodation();
			}
	}
	else return false;
}

function update_accommodation_total(id) {
	 var room_hours = parseInt(J('#room_hours'+id).val()/24);
	 var final_room_hours = room_hours == 0 ? J('#room_hours'+id).val() : ((J('#room_hours'+id).val()) - (room_hours * 24));
	 var final_room_days = parseInt(J('#room_days'+id).val()) + room_hours;
	 var computed_days = final_room_hours > 5 ? final_room_days + 1 : final_room_days;
	 var total = computed_days + 'day' + ((computed_days > 1) ? 's' : ' ') + '= ' + formatNumber(computed_days * J('#room_rate'+id).val(), 2);
	 J('#total_accommodation_td'+id).html(total);
	 J('#total_accommodation'+id).val(computed_days * J('#room_rate'+id).val());
	 update_total_charge_accommodation();

}

function update_total_charge_accommodation() {
	 var total_accommodation = document.getElementsByName('total_accommodation[]');
	 var sub_total = 0;
	 var discount_total = 0;
	 var net_total = 0;

	 for (var i=0; i<total_accommodation.length; i++) {
		//sub_total += parseFloat(number_of_usage[i].value * original_price[i].value);
		//discount_total += parseFloat(number_of_usage[i].value * adjusted_price[i].value);
		net_total += parseFloat(total_accommodation[i].value);
	 }

	 J('#accommodation_subtotal').html(formatNumber(net_total, 2));
	 J('#accommodation_discount_total').html('(0.00)');
	 J('#accommodation_net_total').html(formatNumber(net_total, 2));
}

