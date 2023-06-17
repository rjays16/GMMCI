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

function clearBloodOrder(list) {
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

function appendBloodOrder(list,details)
{
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var isCash = $("iscash1").checked;
			var totalCash, totalCharge;
			var src, toolTipText;
			var lastRowNum = null,
					//items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			nf.setPlaces(2);
			var trayItems=parseInt(document.getElementById('blood-counter').innerHTML);
			if (details) {
				var id = details.id,
					idGrp = details.idGrp,
					qty = parseFloat(details.qty),
					prcCash = parseFloat(details.prcCash),
					prcCharge = parseFloat(details.prcCharge);
					totalCash = prcCash*qty;
					totalCharge = prcCharge*qty;

				alt = (dRows.length%2)+1;

				delitemImg = '<a href="javascript: nd(); removeBloodItem(\''+id+'\');">'+
							 '	<img src="../../gui/img/common/default/cross.png" border="0"/></a>';

				src =
						'<tr class="wardlistrow'+alt+'" id="blood-row'+id+'"> '+
						'<input type="hidden" name="blood-sservice[]" id="blood-sservice'+id+'" value="'+details.sservice+'" />'+
						'<input type="hidden" name="blood-pcash[]" id="blood-rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
						'<input type="hidden" name="blood-pcharge[]" id="blood-rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
						'<input type="hidden" name="blood-items[]" id="blood-rowID'+id+'" value="'+id+'" />'+
						'<input type="hidden" name="blood-price_C1[]" id="blood-rowpriceC1'+id+'" value="'+details.price_C1+'" />'+
						'<input type="hidden" name="blood-price_C2[]" id="blood-rowpriceC2'+id+'" value="'+details.price_C2+'" />'+
						'<input type="hidden" name="blood-price_C3[]" id="blood-rowpriceC3'+id+'" value="'+details.price_C3+'" />'+
						'<td class="centerAlign">'+
						delitemImg
						+'</td>'+
						'<td id="blood-idGrp'+id+'">'+id+'</td>'+
						'<td id="blood-name'+id+'">'+details.name+'</td>'+
						'<td class="centerAlign" id="rowQty'+id+'">'+
							'<input type="text" class="segInput" name="blood-qty[]" id="blood-qty'+id+'" value="'+details.qty+'" itemID="'+id+'" prevValue="'+details.qty+'" style="width:80%;text-align:right" onblur="adjustQty(this)" onKeyDown="keyEnter(event, this);"/>'+
						'</td>'+
						'<td class="rightAlign" id="blood-prc'+id+'">'+formatNumber(prcCash,2)+'</td>'+
						'<td class="rightAlign" id="blood-total'+id+'">'+formatNumber(totalCash,2)+'</td>'+
						'</tr>';
					trayItems++;
			}
			else {
				src = "<tr><td colspan=\"10\">Request list is currently empty...</td></tr>";
			}
			dBody.insert(src);
			document.getElementById('blood-counter').innerHTML = trayItems;
			updateTotalBlood();
			return true;
		}
	}
	return false;
}

function keyEnter(e,d){
	if (e.keyCode == 13){
		adjustQty(d);
	}else{
		return false;
	}

}

function adjustQty(obj) {
	var id = obj.getAttribute("itemID");

		if (isNaN(obj.value)) {
		obj.value = obj.getAttribute("prevValue");
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		$('blood-total'+id).innerHTML = formatNumber(parseFloatEx($('blood-rowPrcCash'+id).value)*parseFloatEx($('blood-qty'+id).value),2);
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	updateTotalBlood();
	return true;
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}


function removeBloodItem(id) {
	var destTable, destRows;
	var table = $('blood-list');
	var rmvRow=$('blood-row'+id);
	var item_cnt = parseInt($('blood-counter').innerHTML);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		rmvRow.remove();
		if (!document.getElementsByName("blood-items[]") || document.getElementsByName("blood-items[]").length <= 0)
		{
			appendBloodOrder(table, null);
			$('blood-counter').innerHTML = 0;
		}
		item_cnt = parseInt(item_cnt-1);
		$('blood-counter').innerHTML = item_cnt;
		reclassRows(table,rndx);
	}
	updateTotalBlood();
}


function updateTotalBlood(){
	var items = document.getElementsByName('blood-items[]');
	var cash = document.getElementsByName('blood-pcash[]');
	var charge = document.getElementsByName('blood-pcharge[]');
	var prc = document.getElementsByName('blood-pcash[]');
	var qty = document.getElementsByName('blood-qty[]');
	var total = 0.0, orig = 0.0;
	var id

	for (var i=0;i<items.length;i++) {
		id = items[i].value
		val = parseFloatEx(prc[i].value)*parseFloatEx(qty[i].value)
		total+=val;
	}

	var subTotal = $("blood-sub-total");
	var discountTotal = $("blood-discount-total");
	var netTotal = $("blood-net-total");

	subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	discountTotal.style.color = "#006600";
	discountTotal.innerHTML = "("+formatNumber(Math.abs(0),2)+")";
	netTotal.innerHTML = formatNumber(total.toFixed(2),2);
}

function emptyBloodOrder(){
	clearBloodOrder($('blood-list'));
	appendBloodOrder($('blood-list'),null);
	$('blood-counter').innerHTML = 0;
	updateTotalBlood();
}


function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}
