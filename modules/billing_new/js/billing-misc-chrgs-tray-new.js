var AJAXTimerID=0;
var lastSearch="";


function display(str) {
    if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
}

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	//trimString(searchEL);   //omit unnecessary white spaces
	if (!page) page = 0;
    var last_page;

	if (true) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";

		// AJAXTimerID = setTimeout("xajax_populateChrgsList('"+searchID+"','"+searchEL.value+"',"+page+")",200);
		// lastSearch = searchEL.value;

		var script = "xajax_populateChrgsList('"+searchID+"','"+searchEL.value+"',"+page+")";
        AJAXTimerID = setTimeout(script,200);
        lastSearch = searchEL.value;
        lastSearchPage = page;
	}
}

function endAJAXSearch(searchID) {
    var searchEL = $(searchID);
    if (searchEL) {
        $("ajax-loading").style.display = "none";
        searchEL.style.color = "";
    }
}//end endAJAXSearch

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
	var data_misc = new Object();

    qty=0;
    while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
        qty = prompt("Enter quantity:")
        if (qty === null) return false;
    }

	data_misc.code        = code;
	data_misc.name        = $('name_'+code).value;
	data_misc.acct_type   = $('acct_type_'+code).value;
	data_misc.msc_charge  = $('msc_charge_'+code).value;
    data_misc.qty         = qty;
	data_misc.enc_nr 		= window.parent.document.getElementById('encounter_nr').value;
	data_misc.bill_dt 	= window.parent.document.getElementById('billdate').value;
	data_misc.bill_frmdte = window.parent.document.getElementById('date_admitted').value;
	

    window.parent.addMiscChrg(data_misc);
}

function addMiscItemToList(details) {
	var list=$('miscellaneous-list'), dRows, dBody, rowSrc, i;
	var code = details.code;
	var scode = details.scode;
	var name = details.name;
	var description = details.description;
	var actype = details.account_type;
	var price = details.price;
	var ptype = details.ptype_name;
	var mtype = details.type_name;
	var sclass = details.class;
	
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (code) {
			rowSrc = '<tr>'+
						 '<tr id="row_'+code+'" class="'+sclass+'">'+
						 '<td style="color:#800000">'+
						 	'<span id="id_'+code+'">'+scode+'</span>'+
						 	'<input id="acct_type_'+code+'" type="hidden" value="'+actype+'">'+
						 '</td>'+
						 '<td><span id="name_'+code+'">'+name+'</span><br/><span id="desc_'+code+'" class="description">'+description+'</span></td>'+
						 '<td style="color:#006" align="center"><span id="ptype_'+code+'">'+ptype+'</span></td>'+
						 '<td style="color:#006" align="center"><span id="type_'+code+'">'+mtype+'</span></td>'+
						'<td>'+
							'<input style="text-align:right"'+( Number(price)-0 ? ' disabled ' : ' ')+'name="msc_charge_'+code+'" id="msc_charge_'+code+'" type="text" size="10" maxlength="10"'+
							' onblur="trimString(this); chkDecimal(this,\''+code+'\');" onFocus="this.select();" value="'+formatNumber(Number(price),2)+'">'+
						'</td>'+
						'<td>'+
							'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
								'onclick="prepareAdd(\''+code+'\')">'+
						'</td>'+
					 '</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="6" style="">No such miscellaneous charge exists...</td></tr>';
		}

		dBody.innerHTML += rowSrc;
	}
}
