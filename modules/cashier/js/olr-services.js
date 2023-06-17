var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
	document.body.innerHTML = str;
}

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
	if (parseFloat($('deposit'+id).value)<=0) {
		alert('Please enter a valid value for deposit...');
		$('value'+id).focus();
		return false;
	}
	
	details.id = $('src'+id).value+$('id'+id).value;
	details.name = $('name'+id).value;
	details.desc = $('desc'+id).value;
	details.qty = 1;
	details.origprice = $('deposit'+id).value;
	details.price = $('deposit'+id).value;
	details.ispaid = 0;
	details.checked= 1;
	details.showdel= 1;
	details.calculate= 1;
	details.limit= -1;
	details.src = 'pp';
	details.ref = '0000000000';
	details.doreplace = 1;
	result = window.parent.addServiceToList(details);
	
	if (result && $('deposit'+id)) {
		$('deposit'+id).value = "";
		alert("Item added to payment list...");
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
			var deposit= details.deposit
			var source=details.source
			var disabled='';
			var class='';
			if (false) {
				disabled = 'disabled="disabled"';
				class = 'class="highLight"';
			}
			var rid = source+id;
			var src = '';
			switch (source) {
				case 'O': src='O.R.'; break;
				case 'L': src='Laboratory'; break;
				case 'R': src='Radiology'; break;
			}
			rowSrc = "<tr "+class+">"+
									'<td>'+
										'<input type="hidden" id="id'+rid+'" value="'+id+'">'+
										'<input type="hidden" id="src'+rid+'" value="'+source.toUpperCase()+'">'+
										'<span style="font:bold 11px Tahoma;color:#660000">'+source.toUpperCase()+id+'</span>'+
									'</td>'+
									'<td>'+
										'<input type="hidden" id="name'+rid+'" value="'+name+'">'+
										'<input type="hidden" id="desc'+rid+'" value="'+desc+'">'+
										'<span style="font:bold 12px Arial">'+
											(name.length > 30 ? name.substring(0,27).concat('...') : name)+
										'</span>'+
									'</td>'+
									'<td align="center">'+
										'<span style="font:bold 11px Tahoma;color:#000060">'+
											src+
										'</span>'+
									'</td>'+
									'<td align="center">'+
										'<span style="font:bold 11px Tahoma;color:#000060">'+
											desc+
										'</span>'+
									'</td>'+
									'<td>'+
										'<input id="deposit'+rid+'" type="text" class="jedInput" style="width:99%;text-align:right" onblur="this.value=((isNaN(this.value)||!this.value) ? \'0\' : parseFloat(this.value))" value="'+deposit+'"/>'+
									'</td>'+
									'<td>'+
										'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
											'onclick="prepareAdd(\''+rid+'\')" '+disabled +
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="10" style="">No such service exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}