var ViewMode = false;

function display(str) {
	document.write(str);
}

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

function resetRefNo(newRefNo,error) {
	$("refno").style.color = error ? "#ff0000" : "";
	$("refno").value=newRefNo;
}

function clearEncounter() {
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	$('ordername').readOnly=!iscash;
	$('orderaddress').value="";
	$('orderaddress').readOnly=!iscash;
	$('is_tpl').disabled = !iscash;
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('clear-enc').disabled = true;
	$('btndiscount').disabled = false;
	$('sw-class').innerHTML = 'None';
	//clearCharityDiscounts();
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
	
function emptyTray() {
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshDiscount();
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

function clearOrder(list) {	
	if (!list) list = $('order-list')
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0]
		if (dBody) {
			trayItems = 0
			dBody.innerHTML = ""
			return true
		}
	}
	return false
}

function appendOrder(list, details, disabled) {
	if (!list) list = $('return-list');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var totalCash, totalCharge;
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (details) {
				var id = details.id,
					type = details.type,
					ref = details.ref,
					qty = details.quantity,
					price = details.price,
					name = details.name,
					desc = details.desc,
					rowid = ref+'_'+id;

				tot = price*qty;
				if (items) {
					if ($('id_'+rowid)) {
						return false
					}
					if (items.length == 0) clearOrder(list)
				}

				alt = (dRows.length%2)+1
				//qty = isNaN(qty) ? '<span style="margin-right:5px">-</span>;' : 'x'+formatNumber(qty,null)
				prc = isNaN(netPrice) ? '<span style="margin-right:5px">-</span>' : formatNumber(netPrice,2)
				tot = isNaN(tot) ? '<span style="margin-right:5px">-</span>' : formatNumber(tot,2)			
				
				var disabledAttrib = disabled ? 'disabled="disabled"' : ""
				
				src = 
					'<tr class="wardlistrow'+alt+'" id="row_'+rowid+'">' +
						'<input type="hidden" name="soc[]" id="rowSoc'+id+'" value="'+details.isSocialized+'" />'+
						'<input type="hidden" name="soc[]" id="rowSoc'+id+'" value="'+details.isSocialized+'" />'+
						'<input type="hidden" name="pdisc[]" id="rowPrcDiscounted'+id+'" value="'+details.prcDiscounted+'" />'+
						'<input type="hidden" name="pcashsc[]" id="rowPrcCashSC'+id+'" value="'+prcCashSC+'" />'+
						'<input type="hidden" name="pchargesc[]" id="rowPrcChargeSC'+id+'" value="'+prcChargeSC+'" />'+
						'<input type="hidden" name="pcash[]" id="'+id+'" value="'+details.prcCash+'" />'+
						'<input type="hidden" name="price[]" id="price_'+rowid+'" value="'+details.prcCharge+'" />';
				
				if (disabled)
					src+='<td></td>'
				else
					src+='<td class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+id+'\')"/></td>'

				src+=
					'<td>'+
						'<input type="hidden" name="ref[]" id="ref_'+rowid+'" value="'+ref+'" />'+
						'<span id="ref2_'+rowid+'">'+ref+'</span>'+
					'</td>'+
					'<td>'+
						'<input type="hidden" name="items[]" id="id_'+rowid+'" value="'+id+'" />'+
						'<span id="id2_'+rowid+'">'+id+'</span>'+
					'</td>'+
					'<td>'+
						'<span style="color:#660000">'+name+'</span><br/>'+
						'<span style="font-size:10px;font-weight:normal">'+desc+'</span>'+
					'</td>'+
					'<td class="rightAlign">'+
						'<span style="">'+qty+'</span>'+
					'</td>'+
					'<td class="rightAlign">'+
						'<span style="">'+price+'</span>'+
					'</td>'+
					'<td class="rightAlign">'+
						'<input type="text" class="jedInput" name="quantity[]" id="qty_'+rowid+'" itemID="'+id+'" value="'+qty+'" prevValue="'+qty+'" style="width:95%;text-align:right"'+(disabled ? ' disabled="disabled"' : '')+' onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustQty(this)"/>'+
					'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+orig+'</td>'+
					'<td class="rightAlign">'
				
				if	(disabled || (parseFloatEx(details.prcDiscounted)>0 && (!isSC || (isSC && parseFloatEx(seniorPrice)>0))))
					src+= '<input type="text" class="jedClearInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" readonly="readonly"/>'
				else
					src+= '<input type="text" class="jedInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustPrice(this)"/>'

				src+=	'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
				'</tr>';
				
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"8\">Order list is currently empty...</td></tr>";	
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
			appendOrder(table, null);
		reclassRows(table,rndx);
	}
	refreshTotal();
}

function seniorCitizen() {
	var iscash = $("iscash1").checked
	var issc = $("issc").checked
	var discount = parseFloatEx($("discount").value)
	var pdisc = document.getElementsByName('pdisc[]')
	var soc = document.getElementsByName('soc[]')
	var items = document.getElementsByName('items[]')
	var cash = document.getElementsByName('pcash[]')
	var charge = document.getElementsByName('pcharge[]')
	var cashsc = document.getElementsByName('pcashsc[]')
	var chargesc = document.getElementsByName('pchargesc[]')
	var prc = document.getElementsByName('prc[]')
	var isCash = $("iscash1").checked
	var newPrice, discountPrice, seniorPrice, cashPrice, chargePrice,
			cashSc, chargeSc
	
	for (var i=0;i<items.length;i++) {
		priceCash = parseFloatEx(cash[i].value)
		priceCharge = parseFloatEx(charge[i].value)
		newPrice = iscash ?  priceCash : priceCharge
		discountPrice = newPrice
		if (parseInt(soc[i].value)==1) {
			if (discount==1.0)	newPrice = 0
			else {
				discountPrice = parseFloatEx(pdisc[i].value)
				if (discountPrice > 0) newPrice = discountPrice
			}
		}
		
		seniorPrice = 1.0
		if (issc) {
			cashSc = parseFloatEx(cashsc[i].value)
			chargeSc = parseFloatEx(chargesc[i].value)
			seniorPrice = Math.min(newPrice, iscash ? cashSc : chargeSc)
			if (seniorPrice > 0) newPrice = seniorPrice
		}
		
		// disabled flag
		disabledFlag = false
		//alert('issc:'+issc+'\ndsc:'+discountPrice+'\nsprc:'+seniorPrice)
		if (disabledFlag || (discountPrice >0 && (!issc || (issc && seniorPrice>0)))) {
			prc[i].className = "jedClearInput"
			prc[i].value = formatNumber(newPrice,2)
			prc[i].readOnly = true
			prc[i].setAttribute("prevValue", newPrice)
			prc[i].setAttribute("onfocus", "")
			prc[i].setAttribute("onblur", "")
		}
		else {
			prc[i].className = "jedInput"
			prc[i].readOnly = false
			prc[i].value = formatNumber(newPrice,2)
			prc[i].setAttribute("prevValue", newPrice)
			prc[i].setAttribute("onfocus", "this.value=this.getAttribute(\'prevValue\')")
			prc[i].setAttribute("onblur", "adjustPrice(this)")
		}
	}
	refreshDiscount()
}

function changeTransactionType() {
	clearEncounter();
	refreshDiscount();
}

function adjustPrice(obj) {
	var id = obj.getAttribute("itemID");
	if (isNaN(obj.value)) {
		obj.value = formatNumber(obj.getAttribute("prevValue"),2);
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		$('tot'+id).innerHTML = formatNumber(obj.value*parseFloatEx($('rowQty'+id).value),2);
		refreshDiscount();
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	obj.value = formatNumber(obj.value,2);
	return true;
}

function adjustQty(obj) {
	var id = obj.getAttribute("itemID");
	if (isNaN(obj.value)) {
		obj.value = obj.getAttribute("prevValue");
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		$('tot'+id).innerHTML = formatNumber(parseFloatEx($('rowPrc'+id).value)*parseFloatEx($('rowQty'+id).value),2);
		refreshDiscount();
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	//obj.value = formatNumber(obj.value,2);
	return true;
}

function refreshDiscount() {
	var nodes;
	var nr = $('encounter_nr').value;
	if (nr)
		nodes = document.getElementsByName("charity[]");
	else
		nodes=document.getElementsByName("discount[]");
	totalDiscount = 0;
	if (nodes) {
		for (var i=0;i<nodes.length;i++) {
			if (nodes[i].value) totalDiscount += parseFloatEx(nodes[i].getAttribute('discount'));
		}
	}
	var dItem = $("show-discount");
	if (dItem) {
		dItem.value = parseFloatEx(totalDiscount * 100).toFixed(2);
	}
	refreshTotal();
}

function refreshTotal() {
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var prc = document.getElementsByName('prc[]');
	var isCash = $("iscash1").checked;
	var total = 0.0, orig = 0.0;
	var id
	for (var i=0;i<items.length;i++) {
		id = items[i].value
		orig+=parseFloatEx(isCash ? cash[i].value : charge[i].value)*parseFloatEx(qty[i].value);
		val = parseFloatEx(prc[i].value)*parseFloatEx(qty[i].value)
		total+=val;
		$('tot'+id).innerHTML = formatNumber(val,2)
	}

	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");
	
	subTotal.innerHTML = formatNumber(orig.toFixed(2),2);
	disc = total-orig;
	if (disc <= 0) {
		discountTotal.style.color = "#006600";
		discountTotal.innerHTML = "("+formatNumber(Math.abs(disc),2)+")";
	}
	else {
		discountTotal.style.color = "red";
		discountTotal.innerHTML = formatNumber(Math.abs(disc),2);
	}
	netTotal.innerHTML = formatNumber(total.toFixed(2),2);
}