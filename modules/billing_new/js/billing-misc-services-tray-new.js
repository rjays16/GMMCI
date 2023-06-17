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

		//AJAXTimerID = setTimeout("xajax_populateServicesList('"+searchID+"','"+searchEL.value+"',"+page+")",200);
		//lastSearch = searchEL.value;

		var script = "xajax_populateServicesList('"+searchID+"','"+searchEL.value+"',"+page+")";
        AJAXTimerID = setTimeout(script,200);
        lastSearch = searchEL.value;
        lastSearchPage = page;

	}
}//end startAJAXSearch

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
	rowSrc = '<td colspan="7" style="font-weight:normal">No such miscellaneous service exists...</td>';
	dBody.innerHTML = null;
	dBody.innerHTML += rowSrc;

	if (document.getElementById("parameterselect").value!="none"){
		document.getElementById("search").disabled = false;       //enable textbox for searching
		document.getElementById("search_img").disabled = false;   //enable image
	}else{
		document.getElementById("search").disabled = true;       //enable textbox for searching
		document.getElementById("search_img").disabled = true;   //enable image
	}
}//end enableSearch

function prepareAdd(code, source) {
	var details = new Object();
	var area_code = $('area_code').value;

	if(source=='Pharmacy' && area_code==''){
		alert('You must select the pharmacy area');
		return false;
	}
    
    qty=0;
    while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
        qty = prompt("Enter quantity:")
        if (qty === null) return false;
    }

	details.code        = code;
	details.acct_type   = $('acct_type_'+code).value;
	details.msc_charge  = $('msc_charge_'+code).value;
    details.qty         = qty;
    details.source 		= source;
    details.area_code 	= area_code; 
	details.enc_nr 		= window.parent.document.getElementById('encounter_nr').value;
	details.bill_dt 	= window.parent.document.getElementById('billdate').value;
	details.bill_frmdte = window.parent.document.getElementById('date_admitted').value;
	details.tbl_loc 	= 'xlo';
	window.parent.addMiscService(details);
}//end prepareAdd

function addMiscSrvcToList(details) {
	var list=$('miscellaneous-list'), dRows, dBody, rowSrc, i;
	var code = details.code;
	var scode = details.scode;
	var name = details.name;
	var actype = details.account_type; 
	var price = details.price; 
	var ptype = details.ptype_name; 
	var mtype = details.type_name;
	var eclass = details.class;
	var source = details.source;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (Number(code)>0) {
			rowSrc = '<tr>'+
						 '<tr id="row_'+code+'" class="'+eclass+'">'+
						 '<td style="color:#800000">'+
						 	'<span id="id_'+code+'">'+scode+'</span>'+
						 	'<input id="acct_type_'+code+'" type="hidden" value="'+actype+'">'+
						 '</td>'+
						 '<td><span id="name_'+code+'">'+name+'</span></td>'+
						 '<td><span id="source_'+code+'">'+source+'</span></td>'+
						 '<td style="color:#006" align="center"><span id="ptype_'+code+'">'+ptype+'</span></td>'+
						 '<td style="color:#006" align="center"><span id="type_'+code+'">'+mtype+'</span></td>'+
						'<td>'+
							'<input style="text-align:right" name="msc_charge_'+code+'" id="msc_charge_'+code+'" type="text" size="10" maxlength="10"'+
							' onblur="trimString(this); chkDecimal(this,\''+code+'\');" onFocus="this.select();" value="'+formatNumber(Number(price),2)+'">'+
						'</td>'+
						'<td>'+
							'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
								'onclick="prepareAdd(\''+code+'\',\''+source+'\')">'+
						'</td>'+
					 '</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="7" style="">No such miscellaneous service exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}//end addMiscSrvcToList
function getPharmaAreas() {
    xajax_getPharmaAreas();    

}//end getPharma_Areas

function js_setOption(tagId, value){
    $(tagId).value = value;    
}// end of function js_setOption

function js_AddOptions(tagId, text, value){
    var elTarget = $(tagId);
    if(elTarget){
        var opt = new Option(text, value);
        opt.id = value;
        elTarget.appendChild(opt);
    }
    var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function js_ClearOptions(tagId){
    var optionsList, el=$(tagId);
    if(el){
        optionsList = el.getElementsByTagName('OPTION');
        for(var i=optionsList.length-1; i >=0 ; i--){
            optionsList[i].parentNode.removeChild(optionsList[i]);    
        }
    }
}//end of function js_ClearOptions

function jsOptionChange(obj, value){
    if(obj.id== 'area_combo'){
        $('area_code').value  = value;    
    }
}