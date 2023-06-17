var totalDiscount = 0;
var requestCount = 0;

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
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('clear-enc').disabled = true;
	$('btndiscount').disabled = false;
	clearCharityDiscounts();
}

function pSearchClose() {
	var nr = $('encounter_nr').value;
	if (nr) {
		$('btndiscount').disabled = true;
		xajax_get_charity_discounts(nr);
	}
}
	
function emptyTray() {
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshDiscount();
}

function clearCharityDiscounts() {
	var cNodes = document.getElementsByName("charity[]");
	if (cNodes) {
		for (var i=cNodes.length-1;i>=0;i--) {
			cNodes[i].parentNode.removeChild(cNodes[i]);
		}
	}
}

/* ----------------------------------------------------------------------
 * General list GUI functions
 * ---------------------------------------------------------------------- */

function recolorList(list,startIndex) {
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

function clearList(list) {	
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function removeItemFromList(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));		
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
	refreshTotal();
}

/* ----------------------------------------------------------------------
 * Request list GUI functions
 * ---------------------------------------------------------------------- */

function appendRequestToList(list, details) {
	/*  ***********
	 *  details  - row data container object
	 *    details.refno 		- item reference no.
	 *		details.dept  		- source department
	 *		details.items 		- HTML text
	 *		details.discount 	- HML text
	 * ************ */
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var isCash = $("iscash1").checked;
			var totalCash, totalCharge;
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('requests[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			nf.setPlaces(2);
			
			if (details) {
				var id = details.refno;
				if (items) {
					for (var i=0;i<items.length;i++) {
						if (items[i].value == details.id) {
							var itemRow = $('rq'+items[i].value);
							return true;
						}
					}
					if (items.length == 0)
	 					clearList(list);
				}

				alt = (dRows.length%2)+1;
				src = 
					'<tr class="wardlistrow'+alt+'" id="rq'+id+'">' +
					'<td class="centerAlign">'+
						details.refno+
						'<input type="hidden" name="requests[]" id="r'+details.dept+id+'" value="'+id+'" />'+
					'</td>'+
					'<td align="centerAlign">'+
						details.dept+
						'<input type="hidden" name="rdept[]" id="r'+details.dept+id+'" value="'+id+'" />'+
					'</td>'+
					'<td>'+details.items+'</td>'+
					'<td class="rightAlign">'+details.discount+'</td>'+
					'<td>'+'</td>'+
					'</tr>';
			}
			else {
				src = "<tr><td colspan=\"7\">Order list is currently empty...</td></tr>";	
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}


/* ----------------------------------------------------------------------
 * Item list GUI functions
 * ---------------------------------------------------------------------- */

function appendItemToList(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var isCash = $("iscash1").checked;
			var totalCash, totalCharge;
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			nf.setPlaces(2);
			
			if (details) {
				var id = details.id,
					qty = parseFloat(details.qty),
					prcCash = parseFloat(details.prcCash),
					prcCharge = parseFloat(details.prcCharge);
					totalCash = prcCash*qty;
					totalCharge = prcCharge*qty;
				if (items) {
					for (var i=0;i<items.length;i++) {
						if (items[i].value == details.id) {
							var itemRow = $('row'+items[i].value),
									itemQty = $('rowQty'+items[i].value);
							
							itemQty.value = parseFloat(itemQty.value) + parseFloat(details.qty);
							qty = parseFloat(itemQty.value);
							
							nf.setNumber(qty*prcCash);							
							totalCash = nf.toFormatted();
							nf.setNumber(qty*prcCharge);
							totalCharge = nf.toFormatted()
					
							if (isCash) {
								prc=prcCash;
								tot=totalCash;
							}
							else {
								prc=prcCharge;
								tot=totalCharge;
							}
							
							$('tot'+id).innerHTML = tot;
							nf.setNumber(qty);
							nf.setPlaces(nf.NO_ROUNDING);
							qty = isNaN(qty) ? 'x0' : 'x'+nf.toFormatted();
							$('qty'+id).innerHTML = qty;
							return true;
						}
					}
					if (items.length == 0)
	 					clearOrder(list);
				}

				alt = (dRows.length%2)+1;
				nf.setNumber(qty);
				nf.setPlaces(nf.NO_ROUNDING);
				qty = isNaN(qty) ? 'x0' : 'x'+nf.toFormatted();

				nf.setPlaces(2);
				nf.setNumber(prcCash);
				prcCash = isNaN(prcCash) ? 'N/A' : nf.toFormatted();				
				nf.setNumber(totalCash);
				totalCash = isNaN(totalCash) ? 'N/A' : nf.toFormatted();
				nf.setNumber(prcCharge);
				prcCharge = isNaN(prcCharge) ? 'N/A' : nf.toFormatted();				
				nf.setNumber(totalCharge);
				totalCharge = isNaN(totalCharge) ? 'N/A' : nf.toFormatted();

				if (isCash) {
					prc=prcCash;
					tot=totalCash;
				}
				else {
					prc=prcCharge;
					tot=totalCharge;
				}
				
				src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
					'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="qty[]" id="rowQty'+id+'" value="'+details.qty+'" />'+
					'<td class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="removeItem(\''+id+'\')"/></td>'+
					'<td align="centerAlign"></td>'+
					'<td>'+id+'</td>'+
					'<td>'+details.name+'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+prc+'</td>'+
					'<td class="centerAlign" id="qty'+id+'">'+qty+'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"7\">Order list is currently empty...</td></tr>";	
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}


/* ----------------------------------------------------------------------
 * 
 * ---------------------------------------------------------------------- */

function addCharityDiscount(discountid, discount) {
	var dsc = document.createElement("INPUT");
	dsc.setAttribute("type","text");
	dsc.setAttribute("id","ch"+discountid);	
	dsc.setAttribute("name","charity[]");	
	dsc.setAttribute("discount",discount);
	dsc.setAttribute("value",discountid);
	$("orderForm").appendChild(dsc);
}

function changeTransactionType() {
	var iscash = $("iscash1").checked;
	var prcList, id, total=0;
	var pid = $('pid').value;
	clearEncounter();
	if (iscash)
		prcList = document.getElementsByName("pcash");
	else
		prcList = document.getElementsByName("pcharge");
	for (var i=0;i<prcList.length;i++) {
		if (iscash)
			id = prcList[i].id.substring(10);
		else
			id = prcList[i].id.substring(12);
		$('prc'+id).innerHTML = formatNumber(prcList[i].value,2);
		$('tot'+id).innerHTML = formatNumber(parseFloat($('rowQty'+id).value)*parseFloat(prcList[i].value),2);
	}
	refreshDiscount();
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
			if (nodes[i].value) totalDiscount += parseFloat(nodes[i].getAttribute('discount'));
		}
	}
	var dItem = $("show-discount");
	if (dItem) {
		dItem.value = parseFloat(totalDiscount * 100).toFixed(2);
	}
	refreshTotal();
}

function refreshTotal() {
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();

	total = 0.0;
	for (var i=0;i<items.length;i++) {
		if (isCash)
			total+=parseFloat(cash[i].value)*parseFloat(qty[i].value);
		else
			total+=parseFloat(charge[i].value)*parseFloat(qty[i].value);
	}

	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");
	
	subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	discountTotal.innerHTML = "-"+formatNumber((total * totalDiscount).toFixed(2),2);
	netTotal.innerHTML = formatNumber((total - (total * totalDiscount)).toFixed(2),2);
}