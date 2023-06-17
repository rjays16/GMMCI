var AJAXTimerID=0;
var lastSearch="";
var opstaken_list="";

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	var nenc_nr = $('enc_nr').value;
	var b_drchrg = $('section').value == 'dr' ? true : false;
	var dr_nr = $('dr_nr').value;
	if (!dr_nr) dr_nr = 0;

	//alert($('section').value);

	trimString(searchEL);   //omit unnecessary white spaces

	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";

		AJAXTimerID = setTimeout("xajax_populateAppliedOpsList('"+nenc_nr+"','"+searchID+"','"+searchEL.value+"',"+page+","+b_drchrg+","+dr_nr+")",200);
		lastSearch = searchEL.value;
	}
}

//function startOPSearch(searchID, page) {
//    var searchEL = $(searchID);
//    var nenc_nr = $('enc_nr').value;
//    var b_drchrg = $('section').value == 'dr' ? true : false;
//    var dr_nr = $('dr_nr').value;
//    if (!dr_nr) dr_nr = 0;
//
	//alert($('section').value);
//
//    trimString(searchEL);   //omit unnecessary white spaces
//
//    if (searchEL) {
//        searchEL.style.color = "#0000ff";
//        if (AJAXTimerID) clearTimeout(AJAXTimerID);
//        $("ajax-loading").style.display = "";
//
//        AJAXTimerID = setTimeout("xajax_populateParentOpsList('"+nenc_nr+"','"+searchID+"','"+searchEL.value+"',"+page+")",200);
//        lastSearch = searchEL.value;
//    }
//}

//-----------added by VAN 04-22-08
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec = (parseInt(pageno)*pagen)+1;

	//alert('currentPage, lastPage, firstRec, total = '+currentPage+", "+lastPage+", "+firstRec+", "+total);
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
	//alert(jumpType);
	//alert(currentPage+", "+lastPage);
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
	//alert('e = '+e);
	var characterCode; //literal character code will be stored in this variable

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		e = event;
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		startAJAXSearch(searchID,0);
	}else{
		return true;
	}
}

//---------------------------------------

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}

function enableSearch(){
	//alert(enableSearch);
	var rowSrc, list;
	document.getElementById("search").value="";
	list = $('procedure-list');
	dBody=list.getElementsByTagName("tbody")[0];
	rowSrc = '<td colspan="5" style="font-weight:normal">No such procedure description/code exists...</td>';
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

function addSelectedOP(id, target) {
	var details = new Object();
	var list, dBody, tmp;

	details.refno       = $('refno'+id).value;
	details.entryno     = $('entryno'+id).value;
	details.code        = $('code'+id).value;
	details.rvu         = $('rvu'+id).value;
	details.multiplier  = $('multiplier'+id).value;

	var elemRow = document.getElementById("row_"+details.refno+';'+details.entryno+';'+details.code);
	if (elemRow)
		removeOP(details.refno+';'+details.entryno+';'+details.code);
	else {
		if (!list) list = $('opstaken-list');
		if (list) {
			dBody=list.getElementsByTagName("tbody")[0];
			tmp = '<tr id="row_'+details.refno+';'+details.entryno+';'+details.code+'"><td><input type="hidden" name="opstaken[]" value="'+details.refno+';'+details.entryno+';'+details.code+';'+details.rvu+';'+details.multiplier+';'+target+'" /></td></tr>';
			dBody.innerHTML += tmp;
		}
	}
}

function saveOPsTaken() {
	var elems = document.getElementsByName("opstaken[]");
	var s;
	window.parent.initOPsTakenArray();
	for(var i=0;i<elems.length;i++) {
		if(elems[i].name=='opstaken[]') {
			//alert(elems[i].value);
			s = (i < elems.length-1) ? '#' : '';
			window.parent.saveOPTaken(elems[i].value + s);
		}
	}
	window.parent.updateRVUTotal();
}

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;    // success
		}
		else return false;    // fail
	}
	else return false;    // fail
}

function editRVU(id) {
	$("rvuedit_"+id).style.display = "";
	$("rvurow_"+id).style.display = "none";
	$("rvuedit_"+id).focus();
}

function isESCPressed(e) {
	var kC  = (window.event) ?    // MSIE or Firefox?
			 event.keyCode : e.keyCode;
	var Esc = (window.event) ?
			27 : e.DOM_VK_ESCAPE // MSIE : Firefox
	return (kC==Esc);
}

function applyRVU(e, id) {
	var characterCode;

	if(e && e.which) { //if which property of event object is supported (NN4)
		characterCode = e.which; //character code is contained in NN4's which property
	}
	else {
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if ( (characterCode == 13) || (isESCPressed(e)) ) {
		var rvu = $("rvuedit_"+id).value;
		if ( !(isNaN(parseInt(rvu))) && (parseInt(rvu)>=0) ) {
			var multiplier = $("multiplier"+id).value;

			$("chrgrow_"+id).innerHTML = '<input id="chrg'+id+'" type="hidden" value="'+Number(rvu) * Number(multiplier)+'">'+formatNumber(Number(rvu) * Number(multiplier),2);
			$("rvurow_"+id).innerHTML  = '<input id="rvu'+id+'" type="hidden" value="'+rvu+'">'+rvu;
		}

		$("rvuedit_"+id).style.display = "none";
		$("rvurow_"+id).style.display = "";
		$("op_selected"+id).focus();
	}
}

function addAppliedOPtoList(listID, id, description, descriptionFull, rvu, multiplier, bselected, entry_no, refno, b_dr) {
	var list=$(listID), dRows, dBody, rowSrc, i;
	var target = $('section').value;

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {
			rowSrc = '<tr>'+
					'<td>'+
						'<span id="description'+id+'" style="font:bold 12px Arial">'+description+'</span><br />'+
						'<input id="descriptionFull'+id+'" type="hidden" value="'+descriptionFull+'">'+
					'</td>'+
					'<td>'+
					'    <span style="font:bold 12px Arial;color:#660000">'+id+'</span>'+
					'    <input id="code'+id+'" type="hidden" value="'+id+'">'+
					'</td>'+
					'<td align="center">'+(b_dr == 1 ? '<input style="width:95%;display:none;text-align:right" type="text" id="rvuedit_'+id+'" value="'+rvu+'" onkeyup="applyRVU(event,'+id+');">' : '')+'<span style="width:95%'+(b_dr==1 ? ');cursor:pointer' : '')+'" id="rvurow_'+id+'" '+(b_dr == 1 ? 'onclick="editRVU('+id+');"' : '')+'>'+
							'<input id="rvu'+id+'" type="hidden" value="'+rvu+'">'+rvu+'</span></td>'+
					'<td align="center">'+'<input id="multiplier'+id+'" type="hidden" value="'+multiplier+'">'+multiplier+'</td>'+
					'<td align="right"><span id="chrgrow_'+id+'"><input id="chrg'+id+'" type="hidden" value="'+Number(rvu) * Number(multiplier)+'">'+formatNumber(Number(rvu) * Number(multiplier),2)+'</span></td>'+
					'<td align="center">'+
						'<input onclick="addSelectedOP('+id+',\''+target+'\');" type="checkbox" id="op_selected'+id+'" name="op_selected'+id+'" value="" '+((bselected > 0) ? 'checked' : '')+'>'+
						'<input id="entryno'+id+'" type="hidden" value="'+entry_no+'">'+
						'<input id="refno'+id+'" type="hidden" value="'+refno+'">'+
					'</td>'+
				'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="6" style="">No such procedure description/code exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;

		if (id) {
			if (bselected > 0) {
				addSelectedOP(id,target);
			}
		}
	}
}

function removeOP(id) {
	var table = $('opstaken-list');
	var rmvRow=document.getElementById("row_"+id);
	if (table && rmvRow)
		table.deleteRow(rmvRow.rowIndex);
	else
		alert(table+' and '+rmvRow);
}

//added by CHA, from billing, APRIL 10,2010
/*function initOPsTakenArray() {
	$('opstaken').innerHTML = '';
}

function saveOPTaken(rowval) {
	$('opstaken').innerHTML += rowval;
}

function updateRVUTotal() {
	var ops = $('opstaken').innerHTML;
	var enc_nr = $('enc_nr').value;
	//var bill_dt = $('billdate').value;
	//var type = $('confineTypeOption').options[$('confineTypeOption').selectedIndex].value;
	alert(""+ops+"//"+enc_nr+"/");
	//xajax_updateRVUTotal(ops, enc_nr, bill_dt, type);
} */
//end CHA