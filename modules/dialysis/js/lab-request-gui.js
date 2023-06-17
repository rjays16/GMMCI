
function appendLabOrder(list,details)
{
	if(list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var isCash = $("iscash1").checked;
			var person_discountid = $("discountid").value;
			var totalCash, totalCharge;
			var src, adjust_amount, discount_percentage;
			var nonSocialized, valprice;
			var lastRowNum = null,
					//items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			var cashprice,trayItems=parseInt(document.getElementById('counter').innerHTML);
			//var ptype = $('ptype').value;
			nf.setPlaces(2);
			alt = (dRows.length%2)+1;
			if (details) {
				src =
					'<tr class="wardlistrow'+alt+'" id="row'+details.id+'">'+
					'<input type="hidden" name="lab-sservice[]" id="lab-rowsservice'+details.id+'" value="'+details.sservice+'" />'+
					'<input type="hidden" name="lab-price_C1[]" id="lab-rowpriceC1'+details.id+'" value="'+details.price_C1+'" />'+
					'<input type="hidden" name="lab-price_C2[]" id="lab-rowpriceC2'+details.id+'" value="'+details.price_C2+'" />'+
					'<input type="hidden" name="lab-price_C3[]" id="lab-rowpriceC3'+details.id+'" value="'+details.price_C3+'" />'+
					'<input type="hidden" name="lab-price_C1orig[]" id="lab-rowpriceC1orig'+details.id+'" value="'+details.price_C1+'" />'+
					'<input type="hidden" name="lab-price_C2orig[]" id="lab-rowpriceC2orig'+details.id+'" value="'+details.price_C2+'" />'+
					'<input type="hidden" name="lab-price_C3orig[]" id="lab-rowpriceC3orig'+details.id+'" value="'+details.price_C3+'" />'+
					'<input type="hidden" name="lab-pcash[]" id="lab-rowPrcCash'+details.id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="lab-pcashorig[]" id="lab-rowPrcCashorig'+details.id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="lab-pcharge[]" id="lab-rowPrcCharge'+details.id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="lab-items[]" id="lab-rowID'+details.id+'" value="'+details.id+'" />'+
					'<input type="hidden" name="lab-request_flag[]" id="lab-rowFlag'+details.id+'" value="" />'+
					'<td class="centerAlign" ><a href="javascript: nd(); removeLabItem(\''+details.id+'\')"><img src="../../gui/img/common/default/cross.png" border="0"/></a></td>'+
					'<td class="centerAlign"><img src="../../gui/img/common/default/page_white_acrobat.png" border="0" onclick="return false;"></td>'+
					'<td class="centerAlign"><span style="font:12px Arial; color:#e62b2c">Undone</span></td>'+
					'<td width="15%" id="lab-id'+details.id+'">'+details.id+'</td>'+
					'<td width="*" id="lab-name'+details.id+'">'+details.name+'</td>'+
					'<td class="rightAlign" id="lab-prc'+details.id+'" width="15%">'+formatNumber(details.prcCash,2)+'</td>'+
					'<td class="rightAlign" id="lab-tot'+details.id+'" width="17%">'+formatNumber(details.prcCash,2)+'</td>'+
				'</tr>';
				trayItems++;
			}else {
				src = "<tr><td colspan=\"10\">Request list is currently empty...</td></tr>";
			}
			dBody.insert(src);
			document.getElementById('counter').innerHTML = trayItems;
			updateTotalLab();
			return true;
		}
	}
	return false;
}

function clearLabOrder(list)
{
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		//$('socialServiceNotes').style.display='none';
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
}

function removeLabItem(id) {
	var destTable, destRows;
	var table = $('lab-list');
	var rmvRow=$('row'+id);
	var item_cnt = parseInt($('counter').innerHTML);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		rmvRow.remove();
		if (!document.getElementsByName("lab-items[]") || document.getElementsByName("lab-items[]").length <= 0)
		{
			appendLabOrder(table, null);
			$('counter').innerHTML = 0;
		}
		item_cnt = parseInt(item_cnt-1);
		$('counter').innerHTML = item_cnt;
		reclassRows(table,rndx);
	}
	updateTotalLab();
}

function emptyLabOrder() {
	clearLabOrder($('lab-list'));
	appendLabOrder($('lab-list'),null);
	$('counter').innerHTML = 0;
	updateTotalLab();
}

function updateTotalLab() {
	var items = document.getElementsByName('lab-items[]');
	var cash = document.getElementsByName('lab-pcash[]');
	var charge = document.getElementsByName('lab-pcharge[]');
	var prc = document.getElementsByName('lab-pcash[]');
	var total = 0.0, orig = 0.0;
	var id

	for (var i=0;i<items.length;i++) {
		id = items[i].value
		val = parseFloatEx(prc[i].value)
		total+=val;
	}

	var subTotal = $("lab-sub-total");
	var discountTotal = $("lab-discount-total");
	var netTotal = $("lab-net-total");

	subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	discountTotal.style.color = "#006600";
	discountTotal.innerHTML = "("+formatNumber(Math.abs(0),2)+")";
	netTotal.innerHTML = formatNumber(total.toFixed(2),2);
}

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