var ViewMode = false;

var totalDiscount = 0;

function isCash() {
	return $('iscash1').checked;
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

function emptyPharmaOrder() {
	clearPharmaOrder($('pharma-list'));
	appendPharmaOrder($('pharma-list'),null);
	$('pharma-counter').innerHTML = 0;
	updateTotalPharma();
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

function updateTotalPharma() {
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

	var subTotal = $("pharma-sub-total");
	var discountTotal = $("pharma-discount-total");
	var netTotal = $("pharma-net-total");

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

function removePharmaItem(id) {
	var destTable, destRows;
	var table = $('pharma-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
		{
			appendPharmaOrder(table, null);
			$('pharma-counter').innerHTML = 0;
		}
		reclassRows(table,rndx);
	}
	updateTotalPharma();
}

function clearPharmaOrder(list) {
	if (!list) list = $('pharma-list')
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

function appendPharmaOrder(list, details) {
	if (!list) list = $('pharma-list');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var discount = parseFloatEx($("discount").value);
			var isCash = $("iscash1").checked;
			var isSC = $("issc").checked;  // Senior Citizen checking
			var totalCash, totalCharge;
			var src;
			var lastRowNum = null,
					//items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var trayItems = parseInt(document.getElementById('pharma-counter').innerHTML);
			if (details) {
				var id = details.id,
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

				// Check coverage limit
				/*if ($('coverage').value!=-1) {
					var coverageLimit = parseFloatEx($('coverage').value);
					if (coverageLimit < tot) {
						alert("Coverage limit exceeded for this item...");
						return true;
					}
				} */

				orig = isNaN(orig) ? '<span style="margin-right:5px">-</span>' : formatNumber(orig,2);

				alt = (dRows.length%2) ? 'class="alt"' : '';
				qty = isNaN(qty) ? '<span style="margin-right:5px">-</span>;' : 'x'+formatNumber(qty,null)
				prc = isNaN(netPrice) ? '<span style="margin-right:5px">-</span>' : formatNumber(netPrice,2)
				tot = isNaN(tot) ? '<span style="margin-right:5px">-</span>' : formatNumber(tot,2)

				//var disabledAttrib = disabled ? 'disabled="disabled"' : ""

				src =
					'<tr '+alt+' id="row'+id+'" style="height:26px">' +
						'<input type="hidden" name="soc[]" id="rowSoc'+id+'" value="'+details.isSocialized+'" />'+
						'<input type="hidden" name="pdisc[]" id="rowPrcDiscounted'+id+'" value="'+details.prcDiscounted+'" />'+
						'<input type="hidden" name="pcashsc[]" id="rowPrcCashSC'+id+'" value="'+prcCashSC+'" />'+
						'<input type="hidden" name="pchargesc[]" id="rowPrcChargeSC'+id+'" value="'+prcChargeSC+'" />'+
						'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
						'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
						'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
						'<td class="centerAlign"><img class="segSimulatedLink" src="../../images/cashier_delete_small.gif" border="0" onclick="removePharmaItem(\''+id+'\')"/></td>'+
						'<td class="centerAlign">'+id+'</td>'+
						'<td><span style="color:#660000">'+details.name+'</span></td>'+
						'<td class="centerAlign" id="qty'+id+'">'+
						'<input type="text" class="segInput" name="qty[]" id="rowQty'+id+'" itemID="'+id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:right"/>'+
						'</td>'+
						'<td class="rightAlign" id="prc'+id+'">'+orig+'</td>'+
						'<td class="rightAlign">';

				if	((parseFloatEx(details.prcDiscounted)>0 && (!isSC || (isSC && parseFloatEx(seniorPrice)>0))))
					src+= '<input type="text" class="segClearInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" readonly="readonly"/>'
				else
					src+= '<input type="text" class="segInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustPrice(this)"/>'

				src+=	'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
				'</tr>';
				trayItems++;
			}
			else {
				src = "<tr style=\"height:26px\"><td colspan=\"8\">Order list is currently empty...</td></tr>";
			}
			dBody.insert(src);
			document.getElementById('pharma-counter').innerHTML = trayItems;
			updateTotalPharma();
			return true;
		}
	}
	return false;
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

//----------------------------------------------------------------------------
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
		if (parseInt(soc[i].value)==1 && iscash) {
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
			prc[i].className = "segClearInput"
			prc[i].value = formatNumber(newPrice,2)
			prc[i].readOnly = true
			prc[i].setAttribute("prevValue", newPrice)
			prc[i].setAttribute("onfocus", "")
			prc[i].setAttribute("onblur", "")
		}
		else {
			prc[i].className = "segInput"
			prc[i].readOnly = false
			prc[i].value = formatNumber(newPrice,2)
			prc[i].setAttribute("prevValue", newPrice)
			prc[i].setAttribute("onfocus", "this.value=this.getAttribute(\'prevValue\')")
			prc[i].setAttribute("onblur", "adjustPrice(this)")
		}
	}
	refreshDiscount()
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
