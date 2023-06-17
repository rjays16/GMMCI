var AJAXTimerID=0;
var lastSearch="";
var bSearchCurrentOps = true;
var curops_view = 0;

var refreshTimerID=0;

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	trimString(searchEL);   //omit unnecessary white spaces

	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";

		if (window.parent.$('encounter_nr')) {
			var enc_nr  = window.parent.$('encounter_nr').value;
			AJAXTimerID = setTimeout("xajax_populateICPMList('"+searchID+"','"+searchEL.value+"',"+page+",'"+enc_nr+"')",200);
		}
		else
			AJAXTimerID = setTimeout("xajax_populateICPMList('"+searchID+"','"+searchEL.value+"',"+page+")",200);
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
						if (bSearchCurrentOps)
								getCurrentOpsInEncounter(0);
						else
					startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
						if (bSearchCurrentOps)
								getCurrentOpsInEncounter(parseInt(currentPage)-1);
						else
					startAJAXSearch('search',parseInt(currentPage)-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
						if (bSearchCurrentOps)
								getCurrentOpsInEncounter(parseInt(currentPage)+1);
						else
					startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
						if (bSearchCurrentOps)
								getCurrentOpsInEncounter(parseInt(lastPage));
						else
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

function prepareAdd(id) {
	var details = new Object();

	details.id          = id;
	details.description = $('descriptionFull'+id).value;
	details.code        = $('code'+id).value;
	details.rvu         = $('rvu'+id).value;
	details.multiplier  = $('multiplier'+id).value;
	details.ops_charge  = $('ops_charge'+id).value;
	details.op_date     = $('op_date_'+id).value;

	var enc = window.parent.document.getElementById('encounter_nr').value;
	var bill_dt = window.parent.document.getElementById('billdate').value;

	window.parent.addMiscOp(bill_dt, enc, details.code, details.rvu, details.multiplier, details.ops_charge, details.op_date);
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

function getCurrentOpsInEncounter(page, is_all) {
	var enc = window.parent.document.getElementById('encounter_nr').value;
	var bill_dt = window.parent.document.getElementById('billdate').value;
	var bill_frmdte = window.parent.document.getElementById('bill_frmdte').value;
	bSearchCurrentOps = true;

	if (is_all == 1) {
		is_all = (curops_view == 1) ? 0 : 1;
		curops_view = (curops_view == 1) ? 0 : 1;
	}

	xajax_getCurrentOpsInEncounter(enc, bill_frmdte, bill_dt, page, ((typeof(is_all) == 'undefined') ? 0: is_all));
}

function chngDefaultOption() {
		bSearchCurrentOps = false;
}

function delMiscOps(code){
		var enc = window.parent.document.getElementById('encounter_nr').value;
		var bill_dt = window.parent.document.getElementById('billdate').value;
		var bill_frmdte = window.parent.document.getElementById('bill_frmdte').value;
		var bdflag;

		var godel = confirm("Are you sure you wish to delete selected procedure?");
		if (godel) {
				window.parent.goDelMiscOps(enc, bill_dt, bill_frmdte, code);

		if (refreshTimerID) clearTimeout(refreshTimerID);
		refreshTimerID = setTimeout("getCurrentOpsInEncounter(0)",1000);

//		getCurrentOpsInEncounter(0);
//		window.parent.setDeleteFlag();
//		bdflag = (window.parent.getDeleteFlag() ? true : false);
				//alert(window.parent.getDeleteFlag() ? "TRUE" : "FALSE");
				}
}

function toggleCurOpsHeader($bshow) {
	if ($bshow) {
		document.getElementById('ops_header').style.display = "none";
		document.getElementById('curops_header').style.display = "";
	}
	else {
		document.getElementById('curops_header').style.display = "none";
		document.getElementById('ops_header').style.display = "";
	}
}

function editGroupID(id) {
	$("editgrp_"+id).style.display = "";
	$("showgrp_"+id).style.display = "none";
	$("editgrp_"+id).focus();
}

function cancelEditGrpID(id) {
	$("editgrp_"+id).style.display = "none";
	$("showgrp_"+id).style.display = "";
}

function isESCPressed(e) {
	var kC  = (window.event) ?    // MSIE or Firefox?
			 event.keyCode : e.keyCode;
	var Esc = (window.event) ?
			27 : e.DOM_VK_ESCAPE // MSIE : Firefox
	return (kC==Esc);
}

function applyGrpID(e, id, refno, entryno, provider) {
	var characterCode;

//    bClickedHCare = false;

	if (e) {
		if(e && e.which) { //if which property of event object is supported (NN4)
			characterCode = e.which; //character code is contained in NN4's which property
		}
		else {
			characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}
	}
	else
		characterCode = 13;

	if ( (characterCode == 13) || (isESCPressed(e)) ) {
		var grpid = $("editgrp_"+id).value;

		$("showgrp_"+id).innerHTML = grpid;

		$("showgrp_"+id).style.display = "";
		$("editgrp_"+id).style.display = "none";

		// Update the group id/code in the database ...
		xajax_updateGrpID(grpid, id, refno, entryno, provider);
		}
}

function addCurrentOpsToList(listID, id, op_date, group_id, description, descriptionFull, rvu, multiplier, provider, refno, entryno, ncount) {
		var list=$(listID), dRows, dBody, rowSrc;
		var i;

		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");

				// get the last row id and extract the current row no.
				if (id) {
//            alert("addCurrentOpsToList : id = '"+id+"' "+listID);

						rowSrc = '<tr>'+
					 '    <input id="refno'+id+'" type="hidden" value="'+refno+'">'+
					 '    <input id="entryno'+id+'" type="hidden" value="'+entryno+'">'+
					 '    <input id="provider'+id+'" type="hidden" value="'+provider+'">'+
										'<td>'+
												'<span id="description'+id+'" style="font:bold 12px Arial">'+description+'</span><br />'+
												'<input id="descriptionFull'+id+'" type="hidden" value="'+descriptionFull+'">'+
										'</td>'+
					 '<td align="center">'+formatNumber(Number(ncount),0)+'</td>'+
										'<td>'+
										'    <span style="font:bold 12px Arial;color:#660000">'+id+'</span>'+
										'    <input id="code'+id+'" type="hidden" value="'+id+'">'+
										'</td>'+
					 '<td>'+
					 '    <input style="width:95%;display:none;text-align:left" type="text" id="editgrp_'+id+'" value="'+group_id+'" onFocus="this.select();" onblur="cancelEditGrpID(\''+id+'\');" onkeyup="applyGrpID(event,\''+id+'\', \''+refno+'\', '+entryno+', \''+provider+'\');">'+
					 '    <a id="showgrp_'+id+'" style="cursor:pointer" onclick="editGroupID(\''+id+'\')">'+group_id+'</a>'+
					 '    <input id="group'+id+'" type="hidden" value="'+group_id+'">'+
					 '</td>'+
					 '<td align="center">'+op_date+'</td>'+
										'<td align="center">'+'<input id="rvu'+id+'" type="hidden" value="'+rvu+'">'+rvu+'</td>'+
										'<td align="center">'+'<input id="multiplier'+id+'" type="hidden" value="'+multiplier+'">'+multiplier+'</td>'+
										'<td align="right">'+formatNumber(Number(rvu) * Number(multiplier),2)+'</td>';

						if (provider == 'OA')
								rowSrc += '<td align="center"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer" onclick="delMiscOps(\''+id+'\')" ></td></tr>';
						else
								rowSrc += '<td align="center">&nbsp;</td></tr>';
				}
				else {
			rowSrc = '<tr><td colspan="9" style="">No procedure encoded yet ...</td></tr>';
				}

				dBody.innerHTML += rowSrc;
		}
}

function addProductToList(listID, id, description, descriptionFull, rvu, multiplier) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {
//			alert("addProductToList : id = '"+id+"' "+listID);

			rowSrc = '<tr>'+
					'<td>'+
						'<span id="description'+id+'" style="font:bold 12px Arial">'+description+'</span><br />'+
						'<input id="descriptionFull'+id+'" type="hidden" value="'+descriptionFull+'">'+
					'</td>'+
					'<td>'+
					'	<span style="font:bold 12px Arial;color:#660000">'+id+'</span>'+
					'	<input id="code'+id+'" type="hidden" value="'+id+'">'+
					'</td>'+
					'<td>'+'<input id="rvu'+id+'" type="hidden" value="'+rvu+'">'+rvu+'</td>'+
					'<td>'+'<input id="multiplier'+id+'" type="hidden" value="'+multiplier+'">'+multiplier+'</td>'+
					'<td>'+
						'<input style="text-align:right" name="ops_charge'+id+'" id="ops_charge'+id+'" disabled type="text" size="10" maxlength="10"'+
						' onblur="trimString(this); chkDecimal(this,\''+id+'\');" onFocus="this.select();" value="'+formatNumber(Number(rvu) * Number(multiplier),2)+'">'+
					'</td>'+
					'<td>'+
						'<input id="op_date_'+id+'" type="hidden" value="">'+
						'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
							'onclick="showOPDatePrompt(\''+id+'\');">'+
					'</td>'+
				'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="6" style="">No such procedure description/code exists...</td></tr>';
		}
//		alert("aaddProductToList : rowSrc \n"+rowSrc);
		dBody.innerHTML += rowSrc;
	}
}

function initOPDatePrompt() {
	// Define various event handlers for Dialog
	var handleSubmit = function() {
		this.submit();
	};
	var handleCancel = function() {
		this.cancel();
	};

	// Instantiate the Dialog
	YAHOO.opdateprompt.container.opDateBox = new YAHOO.widget.Dialog("opDateBox",
																		 { width : "390px",
																			fixedcenter : true,
																			visible : false,
																			constraintoviewport : true,
																			buttons : [ { text:"OK", handler:handleSubmit },
																						{ text:"Cancel", handler:handleCancel } ]
																		 } );

	YAHOO.opdateprompt.container.opDateBox.validate = function() {
		var data  = this.getData();

		if (!isDate(data.op_date, 'MM-dd-yyyy')) {
			alert("Please indicate the correct date!");
			$('op_date').focus();
			return false;
		}
		else {
			var opdte = getDateFromFormat(data.op_date, 'MM-dd-yyyy');
			var fopdte = formatDate(new Date(opdte), 'yyyy-MM-dd');
			$('op_date_'+$('op_code').value).value = fopdte;

			prepareAdd($('op_code').value);
			return true;
		}
	};
}

function showOPDatePrompt(id) {
	$('opDateBox').style.display = "";
	$('op_code').value = id;

	YAHOO.opdateprompt.container.opDateBox.render();
	YAHOO.opdateprompt.container.opDateBox.show();
}

