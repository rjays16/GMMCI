var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
	if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)	
}

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);	
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
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
			startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage);
		break;
	}
}

function prepareAdd(id, name) {
	//alert("id="+id+" name="+name);
	window.parent.$('walkin-issuance_product_name').value = name; 
	window.parent.$('walkin-issuance_product_code').value = id; 
	if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount();
	if (window.parent.pSearchClose) window.parent.pSearchClose();
	else if (window.parent.cClick) window.parent.cClick();
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

function addProductToList(listID, details ) {
	// ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
			
		if (typeof(details)=="object") {
			var id = details.id,
				name = details.name,
				desc = details.desc,
				cash = details.cash,
				charge = details.charge,
				cashsc = details.cashsc,
				chargesc = details.chargesc,
				d = details.d,
				soc = details.soc,
				noqty = details.noqty;
				
			var cashHTML, chargeHTML;
			var cashSeniorHTML, chargeSeniorHTML;
			
			if (d>=0)
			
			rowSrc = "<tr>"+
									'<td>'+
										'<span id="name'+id+'" style="font:bold 12px Arial;color:#000066">'+name+'</span><br />'+
										'<div style=""><div id="desc'+id+'" style="font:normal 11px Arial; color:#404040">'+desc+'</div></div>'+
									'</td>'+
									'<td align="center">'+
										'<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
										'<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
									'<td align="right" '+(cash<=0 ? '' : '')+'>'+
										'<input id="noqty'+id+'" type="hidden" value="'+(noqty ? '1' : '0')+'"/>'+
										'<input id="d'+id+'" type="hidden" value="'+d+'"/>'+
										'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+
											(d!=cash ? '<span style="color:#008000">' : '')+
											(d>0 ? 
											 	formatNumber(d,2) : formatNumber(cash,2))+
											(d!=cash ? '</span>' : '')+
										'</td>'+
									'<td align="right">'+
										'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+(charge>0 ? formatNumber(charge,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="cashsc'+id+'" type="hidden" value="'+cashsc+'"/>'+(cashsc>0 ? formatNumber(cashsc,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="chargesc'+id+'" type="hidden" value="'+chargesc+'"/>'+(chargesc>0 ? formatNumber(chargesc,2) : '-')+
									'</td>'+
/*									'<td align="center">'+
										'<input class="jedInput" id="qty'+id+'" type="text" style="text-align:right;width:30px" value="" '+(noqty ? 'disabled="disabled"' : '')+' style="text-align:right" onblur="this.value = isNaN(parseFloatEx(this.value))?\'\':parseFloatEx(this.value)"/>'+
									'</td>'+ */
									'<td>'+
										'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
											'onclick="prepareAdd(\''+id+'\',\''+name+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="8" style="">No such product exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}