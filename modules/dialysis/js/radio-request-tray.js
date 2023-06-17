var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec = (parseInt(pageno)*pagen)+1;

	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;

	if (parseInt(total)==0)
		$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	else
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

	$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";

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
			startAJAXSearch('search',parseInt(currentPage)-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(lastPage));
		break;
	}
}

function checkEnter(e,searchID){
	var characterCode; //literal character code will be stored in this variable

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		startAJAXSearch(searchID,0);
	}else{
		return true;
	}
}

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	var area = $("area").value;
	var dept_nr = $("dept_nr").value;

	var var_area = window.parent.$('area');
	var var_ptype = window.parent.$('ptype');
	var notcash = window.parent.$('iscash0');

	if (var_area)
		var_area = window.parent.$(var_area).value;

	if (var_ptype)
		var_ptype = window.parent.$(var_ptype).value;

	if (notcash)
		notcash = window.parent.$(notcash).checked;

	if(((var_area=='ER PATIENT')||(var_area=='ER')||(var_ptype==1))&&(notcash==true))
		area = "ER";
	else
		area = "";

	var area_type = $('area_type').value;

	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		AJAXTimerID = setTimeout("xajax_populateRadioServiceList('"+dept_nr+"','"+area+"','"+area_type+"','"+searchID+"','"+searchEL.value+"',"+page+")",100);
		lastSearch = searchEL.value;
	}
}


function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}

function enableSearch(){
	var rowSrc, list;
	document.getElementById("search").value="";
	list = $('request-list');
	dBody=list.getElementsByTagName("tbody")[0];
	rowSrc = '<tr><td colspan="6" style="">No such radiological service exists...</td></tr>';
	dBody.innerHTML = null;
	dBody.innerHTML += rowSrc;

	if (document.getElementById("parameterselect").value!="none"){
		document.getElementById("search").disabled = false;       //enable textbox for searching
		document.getElementById("search_img").disabled = false;   //enable image
	}else{
		document.getElementById("search").disabled = true;       //enable textbox for searching
		document.getElementById("search_img").disabled = true;   //enable image
	}
}

function prepareAdd(id) {
	var details = new Object();
	details.idGrp = $('idGrp'+id).innerHTML;
	details.id = $('id'+id).value;
	details.qty = 1;
	details.name = $('name'+id).innerHTML;
	details.prcCash = $('cash'+id).value;
	details.prcCharge= $('charge'+id).value;

	details.prcCashNet= details.prcCash;

	details.sservice= $('sservice'+id).value;
	var list = window.parent.document.getElementById('radio-list');
	if(window.parent.$('radio-counter').innerHTML=="0")
		window.parent.clearRadioOrder(list);
	result = window.parent.appendRadioOrder(list,details);

}

function clearList(listID) {
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

function addProductToList(listID, id, name, grp_code, cash, charge, sservice, available) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		if (id) {
				if (available==1){
					 label_but = '<td width="2%" align="center">'+
													'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
														'onclick="prepareAdd(\''+id+'\')" '+
													'/>'+
												'</td>';
				} else
					 label_but = '<td width="2%" style="color:#FF0000" align="center">Unavailable</td>';

				rowSrc = "<tr>"+
									'<td width="*" align="left">'+
										'<span id="name'+id+'" style="font:bold 12px Arial">'+name+'</span><br />'+
									'	<input id="sservice'+id+'" type="hidden" value="'+sservice+'"/>'+
									'</td>'+
									'<td width="15%" align="left">'+
									'	<span id="idGrp'+id+'" style="font:bold 11px Arial;color:#660000">'+id+' ('+grp_code+')</span>'+
									'	<input id="id'+id+'" type="hidden" value="'+id+'"/>'+
									'</td>'+
									'<td align="right" width="20%">'+
										'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+cash+'</td>'+
									'<td align="right" width="20%">'+
										'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+charge+'</td>'+
									''+label_but+''+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="6" style="">No such radiological service exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}
