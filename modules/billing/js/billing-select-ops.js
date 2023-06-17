var AJAXTimerID=0;
var lastSearch="";
var view_all = 0;

function startAJAXSearch(searchID, page, toggle) {
		var searchEL = $(searchID);
		var nenc_nr = $('enc_nr').value;
		var b_drchrg = $('section').value == 'dr' ? true : false;
		var dr_nr = $('dr_nr').value;
		if (!dr_nr) dr_nr = 0;
		toggle = (typeof(toggle) == 'undefined') ? 0 : Number(toggle);
		if (toggle != 0) view_all = (view_all) ? 0 : 1;

		//alert($('section').value);
		trimString(searchEL);   //omit unnecessary white spaces

		if (searchEL) {
				searchEL.style.color = "#0000ff";
				if (AJAXTimerID) clearTimeout(AJAXTimerID);
				$("ajax-loading").style.display = "";

				AJAXTimerID = setTimeout("xajax_populateAppliedOpsList('"+nenc_nr+"','"+searchID+"','"+searchEL.value+"',"+page+","+b_drchrg+","+dr_nr+","+Number(view_all)+")",200);
				lastSearch = searchEL.value;
		}
}

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

function addSelectedOP(id, target, entry_no) {
		var details = new Object();
		var list, dBody, tmp;

		details.refno       = $('refno_'+entry_no+'_'+id).value;
		details.entryno     = entry_no;
		details.code        = $('code'+entry_no+'_'+id).value;
		details.rvu         = $('rvu'+entry_no+'_'+id).value;
		details.multiplier  = $('multiplier'+entry_no+'_'+id).value;

		var elemRow = document.getElementById("row_"+details.refno+';'+details.entryno+';'+details.code);
		if (elemRow) {
				removeOP(details.refno+';'+details.entryno+';'+details.code);
//				alert(elemRow.innerHTML);
		}
		else {
				if (!list) list = $('opstaken-list');
				if (list) {
						dBody=list.getElementsByTagName("tbody")[0];
						tmp = '<tr id="row_'+details.refno+';'+details.entryno+';'+details.code+'"><td><input type="hidden" name="opstaken[]" value="'+details.refno+';'+details.entryno+';'+details.code+';'+details.rvu+';'+details.multiplier+';'+target+'" /></td></tr>';
						dBody.innerHTML += tmp;

//						alert(tmp);
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

function editRVU(id, entryno) {
		$("rvuedit_"+entryno+'_'+id).style.display = "";
		$("rvurow_"+entryno+'_'+id).style.display = "none";
		$("rvuedit_"+entryno+'_'+id).focus();
		return true;
}

function isESCPressed(e) {
		var kC  = (window.event) ?    // MSIE or Firefox?
						 event.keyCode : e.keyCode;
		var Esc = (window.event) ?
						27 : e.DOM_VK_ESCAPE // MSIE : Firefox
		return (kC==Esc);
}

function applyRVU(e, id, entryno) {
		var characterCode;

		if(e && e.which) { //if which property of event object is supported (NN4)
				characterCode = e.which; //character code is contained in NN4's which property
		}
		else {
				characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}

		if ( (characterCode == 13) || (isESCPressed(e)) ) {
				var rvu = $("rvuedit_"+entryno+'_'+id).value;
				if ( !(isNaN(parseInt(rvu))) && (parseInt(rvu)>=0) ) {
						var multiplier = $("multiplier"+entryno+'_'+id).value;

						$("chrgrow_"+entryno+'_'+id).innerHTML = '<input id="chrg'+entryno+'_'+id+'" type="hidden" value="'+Number(rvu) * Number(multiplier)+'">'+formatNumber(Number(rvu) * Number(multiplier),2);
						$("rvurow_"+entryno+'_'+id).innerHTML  = '<input id="rvu'+entryno+'_'+id+'" type="hidden" value="'+rvu+'">'+rvu;
				}

				$("rvuedit_"+entryno+'_'+id).style.display = "none";
				$("rvurow_"+entryno+'_'+id).style.display = "";
				$("op_selected"+entryno+'_'+id).focus();
		}
}

function addAppliedOPtoList(listID, id, description, descriptionFull, opdate, rvu, multiplier, bselected, entry_no, refno, groupcode, b_dr) {
		var list=$(listID), dRows, dBody, rowSrc, i;
		var target = $('section').value;

		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");

				// get the last row id and extract the current row no.
				if (id) {
						rowSrc = '<tr>'+
										'<td>'+
												'<span id="description'+entry_no+'_'+id+'" style="font:bold 12px Arial">'+description+'</span><br />'+
												'<input id="descriptionFull'+entry_no+'_'+id+'" type="hidden" value="'+descriptionFull+'">'+
										'</td>'+
										'<td>'+
										'    <span style="font:bold 12px Arial;color:#660000">'+id+'</span>'+
										'    <input id="code'+entry_no+'_'+id+'" type="hidden" value="'+id+'">'+
										'</td>'+
										'<td align="center">'+'<input id="groupcode'+entry_no+'_'+id+'" type="hidden" value="'+groupcode+'">'+groupcode+'</td>'+
										'<td align="center">'+'<input id="opdate'+entry_no+'_'+id+'" type="hidden" value="'+opdate+'">'+opdate+'</td>'+
										'<td align="center">'+(b_dr == 1 ? '<input style="width:95%;display:none;text-align:right" type="text" id="rvuedit_'+entry_no+'_'+id+'" value="'+rvu+'" onkeyup="applyRVU(event,'+id+','+entry_no+');">' : '')+'<span style="width:95%'+(b_dr==1 ? ');cursor:pointer' : '')+'" id="rvurow_'+entry_no+'_'+id+'" '+(b_dr == 1 ? 'onclick="editRVU('+id+','+entry_no+');"' : '')+'>'+
														'<input id="rvu'+entry_no+'_'+id+'" type="hidden" value="'+rvu+'">'+rvu+'</span></td>'+
										'<td align="center">'+'<input id="multiplier'+entry_no+'_'+id+'" type="hidden" value="'+multiplier+'">'+multiplier+'</td>'+
										'<td align="right"><span id="chrgrow_'+entry_no+'_'+id+'"><input id="chrg'+entry_no+'_'+id+'" type="hidden" value="'+Number(rvu) * Number(multiplier)+'">'+formatNumber(Number(rvu) * Number(multiplier),2)+'</span></td>'+
										'<td align="center">'+
												'<input onclick="addSelectedOP('+id+',\''+target+'\','+entry_no+');" type="checkbox" id="op_selected'+entry_no+'_'+id+'" name="op_selected'+entry_no+'_'+id+'" value="" '+((bselected > 0) ? 'checked' : '')+'>'+
												'<input id="entryno_'+id+'" type="hidden" value="'+entry_no+'">'+
												'<input id="refno_'+entry_no+'_'+id+'" type="hidden" value="'+refno+'">'+
										'</td>'+
								'</tr>';
				}
				else {
						rowSrc = '<tr><td colspan="8" style="">No such procedure description/code exists...</td></tr>';
				}
				dBody.innerHTML += rowSrc;

				if (id) {
						if (bselected > 0) {
								addSelectedOP(id,target, entry_no);
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