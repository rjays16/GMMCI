var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	//trimString(searchEL);   //omit unnecessary white spaces

	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";

		AJAXTimerID = setTimeout("xajax_populateServicesList('"+searchID+"','"+searchEL.value+"',"+page+")",200);
		lastSearch = searchEL.value;
	}
}

function enableSearch(){
	//alert(enableSearch);
	var rowSrc, list;
	document.getElementById("search").value="";
	list = $('miscellaneous-list');
	dBody=list.getElementsByTagName("tbody")[0];
	rowSrc = '<td colspan="5" style="font-weight:normal">No such miscellaneous service exists...</td>';
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

function prepareAdd(code) {
	var details = new Object();

    qty=0;
    while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
        qty = prompt("Enter quantity:")
        if (qty === null) return false;
    }

	details.code        = code;
	details.name        = $('name_'+code).value;
	details.acct_type   = $('acct_type_'+code).value;
	details.msc_charge  = $('msc_charge_'+code).value;
    details.qty         = qty;

	var enc = window.parent.document.getElementById('encounter_nr').value;
	var bill_dt = window.parent.document.getElementById('billdate').value;

    window.parent.addMiscService(bill_dt, enc, details.code, details.acct_type, details.msc_charge, details.qty);
}

function addMiscSrvcToList(listID, code, scode, name, actype, price, ptype, mtype, eclass) {
	var list=$(listID), dRows, dBody, rowSrc, i;

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (code) {
			rowSrc = '<tr>'+
						 '<tr id="row_'+code+'" class="'+eclass+'">'+
						 '<td style="color:#800000">'+
						 	'<span id="id_'+code+'">'+scode+'</span>'+
						 	'<input id="acct_type_'+code+'" type="hidden" value="'+actype+'">'+
						 '</td>'+
						 '<td><span id="name_'+code+'">'+name+'</span></td>'+
						 '<td style="color:#006" align="center"><span id="ptype_'+code+'">'+ptype+'</span></td>'+
						 '<td style="color:#006" align="center"><span id="type_'+code+'">'+mtype+'</span></td>'+
						'<td>'+
							'<input style="text-align:right" name="msc_charge_'+code+'" id="msc_charge_'+code+'" type="text" size="10" maxlength="10"'+
							' onblur="trimString(this); chkDecimal(this,\''+code+'\');" onFocus="this.select();" value="'+formatNumber(Number(price),2)+'">'+
						'</td>'+
						'<td>'+
							'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
								'onclick="prepareAdd(\''+code+'\')">'+
						'</td>'+
					 '</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="6" style="">No such miscellaneous service exists...</td></tr>';
		}
//		alert("aaddProductToList : rowSrc \n"+rowSrc);
		dBody.innerHTML += rowSrc;
	}
}

