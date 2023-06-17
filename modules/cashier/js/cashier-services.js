function pSearchClose() {
	cClick();
}

function clearEncounter() {
	$('orname').value="";
	$('orname').readOnly=false;
	$('oraddress').value="";
	$('oraddress').readOnly=false;
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('clear-enc').disabled = true;
	$('sw-class').innerHTML = 'None';
//	clearCharityDiscounts();
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
function flagCheckBoxesByName(name, flag) {
	var items = document.getElementsByName(name);
	for (var i=0; i<items.length; i++)
		if (items[i].type.toLowerCase()=='checkbox') {
			if (!items[i].disabled)	items[i].checked = flag;
		}
	refreshTotal();
}

function emptyTray() {
	clearList();
	addServiceToList();
	refreshTotal()
	refreshAmountChange()
}

function reclassRows(startIndex) {
	var list = $('list_hs0000000000');
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

function clearList() {
	// Search for the source row table element
	var list=$('list_hs0000000000'),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			addServiceToList();
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function removeServiceFromList(id) {
	var row = $('row_'+id);
	if (row) {
		row.parentNode.removeChild(row)
		reclassRows(row.rowIndex)
		var items = document.getElementsByName('items[]')
		if (!items.length) {
			addServiceToList()
		}
	}
}

function addServiceToList(details) {
	var i;
	var list=$('list_hs0000000000'), dRows, dBody, rowSrc;

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		if (details) {
			var id = details.id,
					desc = details.desc,
					price = parseFloatEx(details.price),
					qty = parseFloatEx(details.qty);

			if (isNaN(price)) price = 0;
			if (isNaN(qty)) qty= 0;
			var	total = price*qty;


			var items = document.getElementsByName('items[]');
			var suffix = 'a', suffix_new;
			for (var i=0;i<items.length;i++) {
				if (items[i].getAttribute('itemID') == id) {
					suffix_new = items[i].getAttribute('suffix');
					suffix = String.fromCharCode(suffix_new.charCodeAt(0)+1);
				}
			}

			var rowid = id.concat(suffix);
			var class=((items.length%2)>0) ? 'alt':'';
			rowSrc =
				'<tr id="row_'+rowid+'" class="'+class+'">'+
					'<td style="">'+
						'<input id="'+rowid+'" name="items[]" type="hidden" suffix="'+suffix+'" itemID="'+id+'" value="'+rowid+'"/>'+
						'<img src="../../images/cashier_delete_small.gif" class="segSimulatedLink" onclick="if (confirm(\'Remove this item from the list?\')) { removeServiceFromList(\''+rowid+'\'); reclassRows(0); }">'+
					'</td>'+
					'<td align="left"><span id="id_'+rowid+'" style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
					'<td align="left"><span id="desc_'+rowid+'" style="font:bold 12px Arial">'+desc+'</span></td>'+
					'<td align="right">'+
						'<input class="segInput" id="price_'+rowid+'" name="price[]" itemID="'+id+'" type="'+(price?'hidden':'text')+'" value="'+formatNumber(price,2)+'" style="font-size:12px; width:80px; text-align:right" onblur="this.value=formatNumber(this.value,2);refreshItemTotal(\''+rowid+'\')" />'+(price?formatNumber(price,2):'')+
					'</td>'+
					'<td align="right">'+
						'<input class="segInput" id="qty_'+rowid+'" name="qty[]" itemID="'+id+'" type="text" value="'+qty+'" style="font-size:12px; width:99%; text-align:right" onblur="refreshItemTotal(\''+rowid+'\')"/>'+
					'</td>'+
					'<td align="right">'+
						'<input id="total_'+rowid+'" name="total[]" itemID="'+id+'" type="hidden" value="'+total+'"/>'+
						'<span id="total_disp_'+rowid+'">'+formatNumber(total,2)+'</span>'+
					'</td>'+
				'</tr>';
			if (items.length==0)
				dBody.innerHTML = rowSrc;
			else
				dBody.innerHTML += rowSrc;
			refreshTotal()
			return true;
		}
		else {
			rowSrc = '<tr><td colspan="10">Service list is currently empty...</td></tr>';
			dBody.innerHTML = rowSrc;
			return true;
		}
	}

	return false;
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

function refreshItemTotal(rowid) {
	var newtotal = parseFloatEx($('price_'+rowid).value) * parseFloatEx($('qty_'+rowid).value)
	_setValue('total_'+rowid, newtotal)
	_setValue('total_disp_'+rowid, formatNumber(newtotal,2))
	refreshTotal()
}

function refreshTotal() {
	var totals = document.getElementsByName('total[]')
	var subtotal = 0
	var id


	if (totals) {
		for (var i=0; i<totals.length; i++) {
			id = totals[i].getAttribute("itemID")
			subtotal += parseFloatEx(totals[i].value)
		}
	}
	_setValue('subtotal_hs0000000000')
	_setValue('show_subtotal_hs0000000000',formatNumber(subtotal,2))

	var discountTotal
	if ($('show-discount-total'))
		discountTotal = parseFloatEx($('show-discount-total').innerHTML)
	else
		discountTotal = 0
	if (isNaN(discountTotal)) discountTotal = 0
	var nettotal = subtotal-discountTotal

	if ($('show-sub-total')) $('show-sub-total').innerHTML = formatNumber(subtotal, 2)
	if ($('show-sub-total')) $('show-sub-total').setAttribute('value',subtotal)


	if ($('show-discount-total')) $('show-discount-total').innerHTML = (discountTotal <= 0) ? '('+formatNumber(Math.abs(discountTotal), 2)+')' : '<span style="color:red">'+formatNumber(discountTotal,2)+'</span>'
	if ($('show-discount-total')) $('show-discount-total').setAttribute('value',discountTotal)


	if ($('show-net-total')) $('show-net-total').innerHTML = formatNumber(nettotal, 2)
	if ($('show-net-total')) $('show-net-total').setAttribute('value',nettotal)

	refreshAmountChange()
	return subtotal
}

function amtTenderedOnBlurFocusHandle(obj) {
	obj.value = parseFloatEx(obj.value)
	if (isNaN(obj.value)) obj.value = 0.0;
	$("show-amt-tendered").setAttribute('value',obj.value)
	$("show-amt-tendered").innerHTML = formatNumber(obj.value,2)
	refreshAmountChange()
	return true
}

function refreshAmountChange() {
	var change
	var total = $('show-net-total') ? parseFloatEx($('show-net-total').getAttribute('value')) : 0
	if (isNaN(total)) total=0
	var tendered = $('amount_tendered') ? parseFloatEx($('amount_tendered').value) : 0
	if (isNaN(tendered)) tendered=0
	var change = tendered-total

	color = (change>=0) ? 'black' : 'red'

	if ($('show-amt-tendered')) {
		$('show-amt-tendered').setAttribute('value',tendered)
		$('show-amt-tendered').innerHTML = formatNumber(tendered,2)
		$('show-change').style.color = color
	}

	if ($('show-change')) {
		$('show-change').setAttribute('value',change)
		$('show-change').innerHTML = formatNumber(change,2)
	}
}