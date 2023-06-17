
function tooltip(text) {
	return overlib(text,WRAP,0,HAUTO,VAUTO, BGCLASS,'olTooltipBG', FGCLASS,'olTooltipFG', TEXTFONTCLASS,'olTooltipTxt', SHADOW,0, SHADOWX,2, SHADOWY,2, SHADOWOPACITY, 25);
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function resetControls() {
	$('name').value="";
	$('pid').value="";
	$('encounter_nr').value="";
/*	$('address').value="";
	$('diagnosis').innerHTML="";
	$('age').value="";
	$('gender').value="";
	$('birthdate').value="";
	$('civil_status').value="";
	$('location').value="";
	$('admission_date').value="";
	$('discharge_date').value="";
	$('patient_type').value="";*/
	$('encounter_type').value="";
	$('encounter_type_show').innerHTML="";
	$('select-enc').disabled = false;
	openPatientSelect();
}

function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function addServiceToList(details) {

	if ($('item-row'+details.id)) {
		alert('This item is already in the list.');
		return false;
	}
	else
	{
		var table = $('request-list');
		if(table){
			var dBody = table.select("tbody")[0];
			if ($('empty-row')) {
				table.getElementsByTagName('tbody').item(0).removeChild($('empty-row'));
			}
			if(dBody){
				var dRows = dBody.getElementsByTagName("tr");
				if(details){
					var total = parseFloat(details.qty*details.price);
					alt = (dRows.length%2>0) ? ' class="alt"':''
					rowSrc = '<tr class="'+alt+'" id="item-row'+details.id+'">'+
							'<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+details.id+'\')"/></td>'+
							/*'<td>'+
								'<span style="color:#660000">'+details.id+'</span>'+
								'<input type="hidden" name="items[]" id="item'+details.id+'" value="'+details.id+'"/>'+
							//	'<input type="hidden" name="item_prc[]" id="item_prc'+details.id+'" value="'+details.price+'"/>'+
							'</td>'+*/
							'<td>'+
								'<span style="color:#660000">'+details.name+'</span>'+
								'<input type="hidden" name="items[]" id="item'+details.id+'" value="'+details.id+'"/>'+
								'</td>'+
							'<td class="centerAlign">'+
								'<input type="text" class="segInput" name="item_qty[]" id="item_qty'+details.id+'" value="'+details.qty+'" style="width:57px;text-align:right" onblur="reCalcTotal(\''+details.id+'\');"/>'+
							'</td>'+
							'<td class="rightAlign" id="row_prc'+details.id+'">'+
									'<input type="text" class="segInput" name="item_prc[]" id="item_prc'+details.id+'" value="'+formatNumber(details.price,2)+'" style="width:57px;text-align:right" onblur="reCalcTotal(\''+details.id+'\');"/>'+
							'</td>'+
							'<td class="rightAlign" id="row_total'+details.id+'">'+formatNumber(total,2)+'</td>'+
						'</tr>';
				}else
				{
					rowSrc = '<tr id="empty-row"><td colspan="9">Request list is empty...</td></tr>';
				}
				dBody.insert(rowSrc);
			}
		}
	}
}

function removeItem(id) {
	var table1 = $('request-list').getElementsByTagName('tbody').item(0);
	table1.removeChild($('item-row'+id));

	if (!document.getElementsByName('items[]') || document.getElementsByName('items[]').length <= 0) {
		append_empty();
	}
}

function append_empty() {
	var table1 = $('request-list').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	var cell = document.createElement("td");
	row.id = "empty-row";
	cell.appendChild(document.createTextNode('Request list is empty...'));

	cell.colSpan = "6";
	row.appendChild(cell);
	$('request-list').getElementsByTagName('tbody').item(0).appendChild(row);
}

function reCalcTotal(id)
{
	var qty = $('item_qty'+id).value;
	var price = $('item_prc'+id).value;
	var new_total = parseFloatEx(qty) * parseFloatEx(price);
	$('row_total'+id).innerHTML = formatNumber(new_total,2);
	$('item_prc'+id).value = formatNumber(price,2);
}

function emptyItems() {
	var table1 = $('request-list').getElementsByTagName('tbody').item(0);
	table1.innerHTML = '<tr id="empty-row"><td colspan="6">Request list is empty...</td></tr>';
}

function forceSubmit() {
	var dform = document.forms[0];
	dform.submit();
}

function showBilled(chkbox) {
	$('onlybilled').value = (chkbox.checked) ? "1" : "";
	forceSubmit();
}