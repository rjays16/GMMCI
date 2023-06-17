var ViewMode = false;

var totalDiscount = 0;

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function warnClear() {
	var items = document.getElementsByName('items[]');
	if (items.length == 0) return true;
	else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function resetNr(newRefNo,error) {
	$("return_nr").style.color = error ? "#ff0000" : "";
	$("return_nr").value=newRefNo;
}

function pSearchClose() {
	var nr = $('encounter_nr').value;
	/*
	if (nr) {
		$('btndiscount').disabled = true;
		cClick();
		//xajax_get_charity_discounts(nr);
	}
	*/
	cClick();
}

function emptyList() {
	clearList($('return-list'));
	appendItem($('return-list'),null);
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

/*
function clearCharityDiscounts() {
	var cNodes = document.getElementsByName("charity[]");
	if (cNodes) {
		for (var i=cNodes.length-1;i>=0;i--) {
			cNodes[i].parentNode.removeChild(cNodes[i]);
		}
	}
}

function addCharityDiscount(discountid, discount) {
	var dsc = document.createElement("INPUT");
	dsc.setAttribute("type","text");
	dsc.setAttribute("id","ch"+discountid);
	dsc.setAttribute("name","charity[]");
	dsc.setAttribute("discount",discount);
	dsc.setAttribute("value",discountid);
	$("orderForm").appendChild(dsc);
}
*/

function clearList(list) {
	if (!list) list = $('order-list')
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0]
		if (dBody) {
			trayItems = 0
			dBody.innerHTML = ""
			eraseCookie('__ret_ck')
			return true
		}
	}
	return false
}

function prepareDelete(rowid) {
	if (confirm('Remove this item from the list?')) {
		if (removeItem(rowid)) {
			var rCookie = readCookie('__ret_ck');
			var key = "<"+rowid+">";
			var pos = 0;
			if (rCookie) {
				pos = rCookie.indexOf(key);
			}
			if (pos !== -1) {
				rCookie = rCookie.split(key).join('');
				createCookie('__ret_ck',rCookie,1);
			}
			alert("Item successfully removed!");
		}
		else "Unable to remove the item from the list...";
	}
}

function appendItem(list, details, disabled) {
	if (!list) list = $('return-list');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var totalCash, totalCharge;
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (details !== null) {
				var id = details.id,
					ref = details.ref,
					qty = details.qty,
					previous = details.previous,
					returned = details.returned ? details.returned : 0,
					price = details.price,
					name = details.name,
					generic = details.generic,
					rowid = ref+'_'+id;

				tot = price*qty;
				if (items) {
					if ($('id_'+rowid)) {
						return false
					}
				}
				if (items.length == 0) clearList(list)
				var rCookie = readCookie('__ret_ck');
				if (!rCookie)
					rCookie = '<'+rowid+'>';
				else {
					if (rCookie.indexOf("<"+rowid+">") === -1)
						rCookie += '<'+rowid+'>';
				}
				createCookie('__ret_ck',rCookie,1);

				alt = (dRows.length%2)+1
				var disabledAttrib = disabled ? 'disabled="disabled"' : ""

				src =
					'<tr class="wardlistrow'+alt+'" id="row_'+rowid+'">';

				refund = returned * price;
				src+=
					'<td class="centerAlign">'+
						'<input type="hidden" name="ref[]" id="ref_'+rowid+'" itemID="'+rowid+'" value="'+ref+'" />'+
						'<span id="ref2_'+rowid+'" style="color:#000060">'+ref+'</span>'+
					'</td>'+
					'<td align="left">'+
						'<input type="hidden" name="items[]" itemID="'+rowid+'" id="id_'+rowid+'" value="'+id+'" />'+
						'<span id="id2_'+rowid+'" style="color:#000060">'+id+'</span>'+
					'</td>'+
					'<td>'+
						'<span style="color:#660000">'+name+'</span><br/>'+
						'<span style="font-size:11px;font-weight:normal">'+generic+'</span>'+
					'</td>'+
					'<td class="centerAlign">'+
						'<input type="hidden" name="qty[]" id="qty_'+rowid+'" itemID="'+rowid+'" value="'+qty+'" />'+
						'<span style="">'+formatNumber(qty)+'</span>'+
					'</td>'+
					'<td class="centerAlign">'+
						'<input type="hidden" name="previous[]" id="previous_'+rowid+'" itemID="'+rowid+'" value="'+previous+'" />'+
						'<span id="previous2_'+rowid+'" style="color:#008000">'+formatNumber(previous)+'</span>'+
					'</td>'+
					'<td class="rightAlign">'+
						'<input type="hidden" name="price[]" id="price_'+rowid+'" itemID="'+rowid+'" value="'+price+'" />'+
						'<span id="price2_'+rowid+'" style="">'+formatNumber(price,2)+'</span>'+
					'</td>'+
					'<td class="centerAlign">'+
						'<input ' + disabledAttrib + ' class="segInput" type="text" name="returned[]" id="returned_'+rowid+'" value="'+returned+'" prevValue="'+returned+'" itemID="'+rowid+'" style="width:85%;text-align:right" onchange="adjustQty(this)" onkeyup="if (event.keyCode==13) this.blur()"/>'+
					'</td>'+
					'<td class="rightAlign">'+
						'<input type="hidden" name="refund[]" id="refund_'+rowid+'" itemID="'+rowid+'" value="'+refund+'" />'+
						'<span id="refund2_'+rowid+'" style="">'+formatNumber(refund,2)+'</span>'+
					'</td>'+
					'<td class="centerAlign">' + (disabled ? '<img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="prepareDelete(\''+rowid+'\');"/>' : '') + '</td>' +
				'</tr>';

				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"15\">Item list is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}

function adjustQty(obj) {
	var id = obj.getAttribute("itemID");
	if (isNaN(obj.value) || obj.value<0) {
		alert("Invalid returned quantity entered...");
		obj.focus();
		obj.value = obj.getAttribute("prevValue");
		return false;
	}
	if ((parseFloatEx(obj.value)+parseFloatEx($('previous_'+id).value)) > parseFloatEx($('qty_'+id).value)) {
//	if (parseFloatEx(obj.value) > parseFloatEx($('qty_'+id).value)) {
		alert("Quantity returned exceeded the maximum returnable...");
		obj.focus();
		obj.value = obj.getAttribute("prevValue");
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		var refund = parseFloatEx($('price_'+id).value)*parseFloatEx($('returned_'+id).value);
		$('refund_'+id).value = refund;
		$('refund2_'+id).innerHTML = formatNumber(refund,2);
	}
	refreshRefund();
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	return true;
}


function removeItem(id) {
	var destTable, destRows;
	var table = $('return-list');
	var rmvRow=document.getElementById("row_"+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
			appendItem(table, null);
		reclassRows(table,rndx);
		return true;
	}
	return false;
}

function refreshRefund() {
	var items = document.getElementsByName('items[]');
	var refund = document.getElementsByName('refund[]');

	var id
	var total = 0
	for (var i=0;i<items.length;i++) {
		id = items[i].getAttribute('itemID');
		total+=parseFloatEx(refund[i].value);
	}
	$('refund_amount').value = formatNumber(total,2);
	if (!$('chk_adjust').checked) {
		$('refund_amount_fixed').value = total;
	}
}