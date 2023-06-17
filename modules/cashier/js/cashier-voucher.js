/* Controls */
function openPayments() {

	var id = $('pid').value;
	if (!id) {
		alert('Select payee for this refund transaction...');
		return false;
	}
	else {
		return overlib(OLiframeContent('seg-cashier-cm-paylist.php?id='+id, 700, 380, 'fMiscFees', 0, 'no'),
			WIDTH,600, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
			CAPTION,'Select payment to be refunded', 
			MIDX,0, MIDY,0,
			STATUS,'Select payment to be refunded');
	}
}

function resetNr(newNr,error) {
	$("memo_nr").style.color = error ? "#ff0000" : "";
	$("memo_nr").value=newNr;
}

function clearEncounter() {
	if ($('orname')) {
			$('orname').value="";
		$('orname').readOnly=false;
	}
	if ($('oraddress')) {
		$('oraddress').value="";
		$('oraddress').readOnly=false;
	}
	if ($('pid')) 
		$('pid').value="";
	if ($('encounter_nr'))
		$('encounter_nr').value="";
	if ($('clear-enc')) {
		$('clear-enc').disabled = true;
		$('clear-enc').disabled = true;
	}
	if ($('sw-class')) 
		$('sw-class').innerHTML = 'None';
}

function addSlashes(str) {
	var ret = str.replace('"','\\"');
	return ret.replace("'","\\'");
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

function reclassRows(list, startIndex) {
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

function flagCheckBoxesByName(name, flag) {
	var items = document.getElementsByName(name);
	for (var i=0; i<items.length; i++)
		if (items[i].type.toLowerCase()=='checkbox') {
			if (!items[i].disabled)	items[i].checked = flag;
		}
	refreshTotal();
}

function emptyTray(src, ref) {
	clearList(src,ref);
	addServiceToList(src,ref,null);
	refreshTotal()
	refreshAmountChange()
}

function clearList(list) {
	if (!list) list = $('memo-list');
	var dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			eraseCookie('__cm_ck')
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function emptyList() {
	clearList();
	appendItem();
}

function addCoverage() {
	var details={sponsor:null, amount:0};
	while (!details.control) {
		details.control=prompt("Enter the voucher control no.");
		if (details.control === null) return false;
	}
	appendItem(null, details);
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('sponsor-list');
	var rmvRow=document.getElementById("row_"+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
			appendItem();
		reclassRows(table,rndx);
		return true;
	}
	return false;
}

function prepareDelete(rowid) {
	if (confirm('Remove this item from the list?')) {
		if (removeItem(rowid)) {
			alert("Item successfully removed!");
		}
		else "Error: Unable to remove the item from the list...";
	}
}

function appendItem(list, details, disabled) {
	if (!list) list = $('sponsor-list');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var totalCash, totalCharge;
			var HTML;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (typeof(details) == 'object') {

				var control=details.control,
					sponsor = details.sponsor,
					amount = details.amount,
					rowid = details.control;

				if (items) {
					if ($('control_'+rowid)) {
						return false
					}
				}
				
				if (items.length == 0) clearList(list)

				alt = (dRows.length%2)+1
				var disabledAttrib = disabled ? 'disabled="disabled"' : ""
				
				HTML = '<tr class="wardlistrow'+alt+'" id="row_'+rowid+'">';

				HTML+=
					'<td align="left">'+
						'<input type="hidden" name="items[]" id="control_'+rowid+'" itemID="'+rowid+'" value="'+control+'" />'+
						'<span id="control2_'+rowid+'" style="color:#600000">'+control+'</span>'+
					'</td>'+
					'<td align="left">'+
						'<select class="jedInput" id="sponsor_'+rowid+'" name="sponsor[]">';
				if (sponsor) $('sponsor-template').value=sponsor;
				HTML += $('sponsor-template').innerHTML;
				HTML+='</select>'+
					'</td>'+
					'<td align="right">'+
						'<input class="jedInput" type="text" name="amount[]" itemID="'+rowid+'" id="amount_'+rowid+'" value="'+formatNumber(amount,2)+'" onfocus="this.value=parseFloatEx(this.value);this.select()" onblur="this.value=formatNumber(parseFloatEx(this.value),2)" onkeypress="if (event.keyCode==13) { this.blur(); return false; }" style="text-align:right;width:99%"/>'+
					'</td>'+
					'<td class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="prepareDelete(\''+rowid+'\');"/></td>'
				'</tr>';
				trayItems++;
			}
			else {
				HTML = "<tr><td colspan=\"15\">Item list is currently empty...</td></tr>";	
			}
			dBody.innerHTML += HTML;
			return true;
		}
	}
	return false;
}

function adjustQty(obj) {
	var id = obj.getAttribute("itemID");
	if (isNaN(obj.value) || obj.value<=0) {
		alert("Invalid refurnd quantity entered...");
		obj.value = obj.getAttribute("prevValue");
		obj.focus();
		obj.select();
		return false;
	}
	if ((parseFloatEx(obj.value)+parseFloatEx($('previous_'+id).value)) > parseFloatEx($('qty_'+id).value)) {
		alert("Quantity refunded exceeds the maximum refundable...");
		obj.value = obj.getAttribute("prevValue");
		obj.focus();
		obj.select();
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		var refund = parseFloatEx($('price_'+id).value)*parseFloatEx($('refund_'+id).value);
		$('refund_total_'+id).value = refund;
		$('refund_total2_'+id).innerHTML = formatNumber(refund,2);
	}
	refreshTotal();
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	return true;
}


function _setValue(id, value) {
	var obj = $(id);
	if (obj) {
		if (obj.value==null) obj.innerHTML = value;
		else obj.value = value;
		return true;
	}
	else return false;
}

function refreshTotal() {
	totals = document.getElementsByName('refund_total[]')
	total_refund = 0;
	if (totals) {
		for (var j=0; j<totals.length; j++) {
			total_refund += parseFloatEx(totals[j].value);
		}
	}
	
	$('total_refund').value = total_refund;
	$('total_refund_show').value = formatNumber(total_refund,2);
	
	return total_refund;
}