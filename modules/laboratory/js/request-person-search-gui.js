var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
	document.write(str);
}

function prepareSelect(id) {
	//alert("prepareSelect");
	var nr = $('nr'+id).value;
	var discountid = $('discountid'+id).value;
	var discount = $('discount'+id).value;	
	var id = $('id'+id).innerHTML;
	var lname = $('lname'+id).innerHTML;
	var fname = $('fname'+id).innerHTML;
	var addr = $('addr'+id).innerHTML;
	window.parent.$('pid').value = id;
	window.parent.$('encounter_nr').value = nr;
	window.parent.$('discountid').value = discountid;
	window.parent.$('discount').value = discount;
	window.parent.$('ordername').value = fname + " " + lname;
	window.parent.$('ordername').readOnly = true;
	window.parent.$('orderaddress').value = addr;
	window.parent.$('orderaddress').readOnly = true;
	window.parent.$('clear-enc').disabled=false;
	
	var iscash = window.parent.$("iscash1").checked;
	//var dBody=window.product-list.getElementsByTagName("tbody")[0];
	if (iscash)
		//alert("body = "+window.parent.$("order-list").innerHTML);
		//window.parent.resetPriceTray();
		window.parent.changeTransactionType(0);
	
	if (nr)
		window.parent.pSearchClose();
	else
		window.parent.cClick();
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

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);	
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	$("pageShow").innerHTML = '<span>Showing '+formatNumber((firstRec),0)+'-'+formatNumber((lastRec),0)+' out of '+formatNumber((parseInt(total)),0)+' record(s).</span>';
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

function addPerson(listID, id, lname, fname, dob, sex, addr, zip, status, nr, type, discountid, discount) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
		// get the last row id and extract the current row no.
		if (id) {
			if (sex=='m')
				sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
			else if (sex=='f')
				sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
			else
				sexImg = '';			
			if (type==0) typ="Walkin";
			else if (type==1) typ='<a title="'+nr+'" href="#">ER Consul</a>';
			else if (type==2) typ='<a title="'+nr+'" href="#">OPD Consult</a>';
			else if (type==3) typ='<a title="'+nr+'" href="#">ER Inpatient</a>';
			else if (type==4) typ='<a title="'+nr+'" href="#">OPD Inpatient</a>';
			rowSrc = '<tr>'+
									'<td>'+
										'<input type="hidden" id="nr'+id+'" value="'+nr+'">'+
										'<input type="hidden" id="discountid'+id+'" value="'+discountid+'">'+
										'<input type="hidden" id="discount'+id+'" value="'+discount+'">'+
										'<span id="id'+id+'" style="color:#660000">'+id+'</span>'+
									'</td>'+
									'<td>'+sexImg+'</td>'+
									'<td><span id="lname'+id+'">'+lname+'</span></td>'+
									'<td><span id="fname'+id+'">'+fname+'</span></td>'+
									'<td><span>'+dob+'</span></td>'+
									'<td><span id="addr'+id+'" style="display:none">'+addr+'</span>'+zip+'</td>'+
									'<td><span>'+status+'</span></td>'+
									'<td align="center"><span>'+typ+'</span></td>'+
									'<td>'+
										'<input type="button" value="Select" style="color:#000066; font-weight:bold; padding:0px 2px" '+
											'onclick="prepareSelect(\''+id+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="9" style="">No such person exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}