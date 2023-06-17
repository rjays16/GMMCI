var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function lpad(str, len, pad) {
	var temp;
	if (str.length >= len) return str;
	else {
		temp = str;
		for (var i=0;i<(len-str.length);i++)
			temp = pad + temp;
		return temp;
	}
}

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	//$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s)</span>';
	if (parseInt(total))
		$("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>'
	else
		$("pageShow").innerHTML = ''
	$("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',true,0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',true,currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',true,parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',true,lastPage);
		break;
	}
}

function prepareAdd(id) {
	var details = new Object();
	if (parseFloat($('qty'+id).value)<=0) {
		alert('Please enter a valid value for the quantity...');
		$('qty'+id).focus();
		return false;
	}

	price = parseFloat($('price'+id).value);
	while (isNaN(parseFloat(price)) || parseFloat(price)<=0) {
		price = prompt("Set the price for this item:")
		if (price===null) return false;
	}

	details.id = $('id'+id).value;
	details.name = $('name'+id).value;
	details.desc = $('desc'+id).value;
	details.qty = $('qty'+id).value;
	details.origprice = price;
	details.price = price;
	details.ispaid = 0;
	details.checked= 1;
	details.showdel= 1;
	details.lock = $('lock'+id).value;
	details.calculate= 1;
	details.doreplace = 1;
	details.limit= -1;
	details.src = 'other';
	details.ref = '0000000000'; 
	details.misc_type = $("type").value;
	details.disable = 0;
    details.clinicInfo = $('clinical_info').value;
	result = window.parent.addServiceToList(details);
	if (result && $('qty'+id)) {
		$('qty'+id).value = "";
		//alert("Item added to payment list...");
		window.parent.refreshTotal();
		window.parent.setTimeout('clickAmountTendered()',500);
		window.parent.cClick();
	}
}

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function addServiceToList(listID, details) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (typeof(details)=='object') {
			var name=details.name
			var desc=details.desc
			var id=details.id
			var price = details.price
			//added by: ian villanueva
			if (details.lock)
			{
				var lock = details.lock;
			}
			else
			var lock = 0;
			var svc = $('svc'+id);
			var disabled='';
			var eclass='';
			var price;
			if (false) {
				disabled = 'disabled="disabled"';
				eclass = 'class="highLight"';
			}
			prc = (parseFloat(price)!=0.0) ? formatNumber(price,2) : 'Arbitrary';
			rowSrc = "<tr "+eclass+">"+
									'<td>'+
										'<input type="hidden" id="id'+id+'" value="'+id+'">'+
										'<span style="font:bold 11px Arial;color:#660000">'+lpad(id,6,'0')+'</span>'+
									'</td>'+
									'<td>'+
										'<input type="hidden" id="name'+id+'" value="'+name+'">'+
										'<input type="hidden" id="desc'+id+'" value="'+desc+'">'+
										'<input type ="hidden" id ="lock'+id+'" value="'+lock+'">'+
										'<span style="font:bold 12px Arial">'+
											name+
											(desc ? ('<br><span style="font:normal 11px Tahoma;color:#808080">'+desc+'</span>') : '') +
										'</span>'+
									'</td>'+
									'<td align="left">'+
										'<span style="font:bold 12px Arial">'+details.dept_name+'</span>'+
									'</td>'+
									'<td align="right">'+
										'<input id="price'+id+'" type="hidden" value="'+price+'"/>'+prc+
									'</td>'+
									'<td>'+
										'<input id="qty'+id+'" type="text" class="segInput" style="width:99%;text-align:right" value="1" onblur="this.value=((isNaN(this.value)||!this.value) ? \'0\' : parseFloat(this.value))"/>'+
									'</td>'+
									'<td class="centerAlign">'+
										'<button class="segButton" '+
											'onclick="if(validate()){prepareAdd(\''+id+'\'); return false;}" '+disabled +
										'><img src="../../gui/img/common/default/add.png" />Add</button>'+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="6" style="">No such service exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

function validate(){
    if($('clinical_info').value == ''){
        alert('Please indicate the clinical impression. ');
        $('clinical_info').focus();
        return false;
    }else{
        return true;
    }    
}