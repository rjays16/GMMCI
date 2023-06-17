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

function removeItem(id) {
	var destTable, destRows;
	var table = $('memo-list');
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
			var rCookie = readCookie('__cm_ck');
			var key = "<"+rowid+">";
			var pos = 0;
			if (rCookie) {
				pos = rCookie.indexOf(key);
			}
			if (pos !== -1) {
				rCookie = rCookie.split(key).join('');
				createCookie('__cm_ck',rCookie,1);
			}
			alert("Item successfully removed!");
		}
		else "Unable to remove the item from the list...";
	}
}

function appendItem(list, details, disabled) {
	if (!list) list = $('memo-list');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var totalCash, totalCharge;
			var HTML;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (typeof(details) == 'object') {

				var id = details.id,
					orno = details.orno,
					src = details.src,
					ref = details.ref,
					qty = details.qty,
					previous = details.previous ? details.previous : 0,
					refund = details.refund,
					price = details.price,
					name = details.name,
					desc = details.desc,
					rowid = orno+'_'+src+'_'+ref+'_'+id;

				if (items) {
					if ($('id_'+rowid)) {
						return false
					}
				}
				if (items.length == 0) clearList(list)
				var rCookie = readCookie('__cm_ck');
				if (!rCookie)
					rCookie = '<'+rowid+'>';
				else {
					if (rCookie.indexOf("<"+rowid+">") === -1)
						rCookie += '<'+rowid+'>';
				}			
				createCookie('__cm_ck',rCookie,1);

				alt = (dRows.length%2)+1
				var disabledAttrib = disabled ? 'disabled="disabled"' : ""
				
				HTML = 
					'<tr class="wardlistrow'+alt+'" id="row_'+rowid+'">';

				refund_total = refund * price;
				HTML+=
					'<td>'+
						'<input type="hidden" name="orno[]" id="orno_'+rowid+'" itemID="'+rowid+'" value="'+orno+'" />'+
						'<span id="orno2_'+rowid+'" style="color:#000060">'+orno+'</span>'+
					'</td>'+
					'<td align="center">'+
						'<input type="hidden" name="src[]" id="src_'+rowid+'" itemID="'+rowid+'" value="'+src+'" />'+
						'<span id="src2_'+rowid+'" style="color:#000000">'+src+'</span>'+
					'</td>'+
					'<td align="center">'+
						'<input type="hidden" name="ref[]" id="ref_'+rowid+'" itemID="'+rowid+'" value="'+ref+'" />'+
						'<span id="ref2_'+rowid+'" style="color:#000000">'+ref+'</span>'+
					'</td>'+
					'<td align="center">'+
						'<input type="hidden" name="items[]" itemID="'+rowid+'" id="id_'+rowid+'" value="'+id+'" />'+
						'<span id="id2_'+rowid+'" style="color:#000000">'+id+'</span>'+
					'</td>'+
					'<td>'+
						'<input type="hidden" name="name[]" id="name_'+rowid+'" itemID="'+rowid+'" value="'+name+'" />'+
						'<input type="hidden" name="desc[]" id="desc_'+rowid+'" itemID="'+rowid+'" value="'+desc+'" />'+
						'<span style="color:#660000">'+name+'</span><br/>'+
						'<span style="font:normal 10px Tahoma;color:#000066">('+desc+')</span>'+
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
						'<input class="jedInput" type="text" name="refund[]" id="refund_'+rowid+'" value="'+refund+'" prevValue="'+refund+'" itemID="'+rowid+'" style="width:85%;text-align:right" onfocus="this.select()" onchange="adjustQty(this)" onkeyup="if (event.keyCode==13) this.blur()"/>'+
					'</td>'+
					'<td class="rightAlign">'+
						'<input type="hidden" name="refund_total[]" id="refund_total_'+rowid+'" itemID="'+rowid+'" value="'+refund_total+'" />'+
						'<span id="refund_total2_'+rowid+'" style="">'+formatNumber(refund_total,2)+'</span>'+
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