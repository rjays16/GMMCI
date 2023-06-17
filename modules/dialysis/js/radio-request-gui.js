var totalDiscount = 0, totalDiscountedAmount=0, totalNet=0, totalNONSocializedAmount=0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
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

function clearRadioOrder(list) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function appendRadioOrder(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var isCash = $("iscash1").checked;
			var totalCash, totalCharge;
			var src, toolTipText;
			var btnicon;
			var lastRowNum = null,
					//items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			var trayItems=parseInt(document.getElementById('radio-counter').innerHTML);
			nf.setPlaces(2);

			if (details) {
				var id = details.id,
					idGrp = details.idGrp,
					qty = parseFloat(details.qty),
					prcCash = parseFloat(details.prcCash),
					prcCharge = parseFloat(details.prcCharge);
					totalCash = prcCash*qty;
					totalCharge = prcCharge*qty;

				alt = (dRows.length%2)+1;
				delitemImg = '<a href="javascript: nd(); removeRadioItem(\''+id+'\');">'+
									 '	<img src="../../images/btn_delitem.gif" border="0"/></a>';
				src =
					'<tr class="wardlistrow'+alt+'" id="radio-row'+id+'"> '+
					'<input type="hidden" name="radio-sservice[]" id="radio-sservice'+id+'" value="'+details.sservice+'" />'+
					'<input type="hidden" name="radio-pcash[]" id="radio-rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="radio-pcharge[]" id="radio-rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="radio-pnet[]" id="radio-rowPrcNet'+id+'" value="">'+
					'<input type="hidden" name="radio-pcashbc[]" id="radio-rowPrcNetbc'+id+'" value="'+details.prcCashNet+'">'+
					'<input type="hidden" name="radio-items[]" id="radio-rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="radio-qty[]" id="radio-rowQty'+id+'" value="'+details.qty+'" />'+
					'<input type="hidden" name="radio-qty'+id+'" id="radio-qty'+id+'" value="'+details.qty+'" />'+
					'<td class="centerAlign">'+
					delitemImg
					+'</td>'+
					'<td width="5%">&nbsp;</td>'+
					'<td id="radio-idGrp'+id+'" width="20%" nowrap="nowrap">'+idGrp+'</td>'+
					'<td id="radio-name'+id+'">'+details.name+'</td>'+
					'<td class="rightAlign" id="radio-prc'+id+'">'+formatNumber(prcCash,2)+'</td>'+
					'<td class="rightAlign" id="radio-tot'+id+'">'+formatNumber(prcCash,2)+'</td>'+
					'</tr>';
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"10\">Request list is currently empty...</td></tr>";
			}
			dBody.insert(src);
			document.getElementById('radio-counter').innerHTML = trayItems;
			updateTotalRadio();
			return true;
		}
	}
	return false;
}

function emptyRadioOrder() {
	clearRadioOrder($('radio-list'));
	appendRadioOrder($('radio-list'),null);
	$('radio-counter').innerHTML = 0;
	updateTotalRadio();
}

function removeRadioItem(id) {
	var destTable, destRows;
	var table = $('radio-list');
	var rmvRow=$('radio-row'+id);
	var item_cnt = parseInt($('radio-counter').innerHTML);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		rmvRow.remove();
		if (!document.getElementsByName("radio-items[]") || document.getElementsByName("radio-items[]").length <= 0)
		{
			appendLabOrder(table, null);
			$('radio-counter').innerHTML = 0;
		}
		item_cnt = parseInt(item_cnt-1);
		$('radio-counter').innerHTML = item_cnt;
		reclassRows(table,rndx);
	}
	updateTotalRadio();
}


function updateTotalRadio() {
	var items = document.getElementsByName('radio-items[]');
	var cash = document.getElementsByName('radio-pcash[]');
	var charge = document.getElementsByName('radio-pcharge[]');
	var prc = document.getElementsByName('radio-pcash[]');
	var total = 0.0, orig = 0.0;
	var id

	for (var i=0;i<items.length;i++) {
		id = items[i].value
		val = parseFloatEx(prc[i].value)
		total+=val;
	}

	var subTotal = $("radio-sub-total");
	var discountTotal = $("radio-discount-total");
	var netTotal = $("radio-net-total");

	subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	discountTotal.style.color = "#006600";
	discountTotal.innerHTML = "("+formatNumber(Math.abs(0),2)+")";
	netTotal.innerHTML = formatNumber(total.toFixed(2),2);
}


function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}