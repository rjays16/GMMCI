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
	$("stock_nr").style.color = error ? "#ff0000" : "";
	$("stock_nr").value=newRefNo;
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


function appendOrderEx(list, details, disabled) {
	if (!list) list = $('order-list')
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0]
		if (dBody) {
			var discount = parseFloatEx($("discount").value)
			var isCash = $('iscash1').checked
			var isSenior = $('issc').checked
			
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
					
			if (details.id) {
				var id, qty, cash, charge,
						senior_cash, senior_charge
				id					=	details.id,
				qty					=	parseFloatEx(details.qty),
				cash				=	parseFloatEx(details.prcCash),
				charge			=	parseFloatEx(details.prcCharge),
				senior_cash	=	parseFloatEx(details.prcCashSC)
				
				// Cash type transactions can avail discounts
				if (isCash) {
					
				}
				else {
				}
			}
		}
	}
}

function appendOrder(list, details, disabled) {
	if (!list) list = $('order-list');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			if (details!=null) {
				var id = details.id,
					qty = parseFloatEx(details.qty);

				if (items) {
					if ($('rowID'+id)) {
						var itemRow = $('row'+id),
								itemQty = $('rowQty'+id)
						itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty)
						itemQty.setAttribute('prevValue',itemQty.value)
						return true
					}
					if (items.length == 0) clearOrder(list)
				}

				alt = (dRows.length%2)+1
				var disabledAttrib = disabled ? 'disabled="disabled"' : ""
				src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />';
				
				if (disabled)
					src+='<td></td>'
				else
					src+='<td class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="if (confirm(\'Remove this item?\')) removeItem(\''+id+'\')"/></td>'
				

				src+=
					'<td>'+id+'</td>'+
					'<td><span style="color:#660000">'+details.name+'</span></td>'+
					'<td class="rightAlign" id="qty'+id+'">'+
						'<input type="text" class="jedInput" name="qty[]" id="rowQty'+id+'" itemID="'+id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:95%;text-align:right"'+(disabled ? ' disabled="disabled"' : '')+' onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustQty(this)"/>'+
					'</td>'+
				'</tr>';
				
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"8\">Stock list is currently empty...</td></tr>";	
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
}

function adjustQty(obj) {
	var id = obj.getAttribute("itemID");
	if (isNaN(obj.value)) {
		obj.value = obj.getAttribute("prevValue");
		return false;
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	return true;
}