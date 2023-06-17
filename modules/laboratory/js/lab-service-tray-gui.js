function prepareAdd(id,is_param) {
	var details = new Object();
	var table_name;
	if (is_param==1){
			table_name = 'order-list_res';
	}else{
		table_name = 'order-list';
	}
	//details.id = $('service_code'+id).value;
	//details.name = $('name'+id).innerHTML;
	 //alert(is_param);
		details.id = $('id'+id).innerHTML;
		details.name = $('name'+id).innerHTML;
		details.prcCash = $('cash'+id).value;
		details.prcCharge= $('charge'+id).value;

		//alert('details = '+details.id+" , "+details.name+" , "+details.prcCash+" , "+details.prcCharge);
	var list = window.parent.document.getElementById(table_name);
	result = window.parent.appendOrder(list,details,is_param);
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

/*
function enableButtonAdd(id){
	//alert("enableButtonAdd = "+id);
	document.getElementById('add_service'+id).disabled=false;
}

function disableButtonAdd(id){
	//alert("enableButtonAdd = "+id);
	document.getElementById('add_service'+id).disabled=true;
}
*/

function addProductToList(listID, is_param, id, name, cash, charge) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
		if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {

								alt = (dRows.length%2)+1;

										rowSrc = '<tr class="wardlistrow'+alt+'">' +
																		'<td width="*" align="left">'+
																				'<span id="name'+id+'" style="font:bold 12px Arial">'+name+'</span><br />'+
																		'</td>'+
																		'<td width="15%" align="left">'+
																			 '<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span>'+

																		'</td>'+
																		'<td align="center" width="20%">'+
																				'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+cash+'</td>'+
																		'<td align="center" width="20%">'+
																				'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+charge+'</td>'+
																		'<td width="2%">'+
																				'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px; cursor:pointer" '+
																						'onclick="prepareAdd(\''+id+'\',\''+is_param+'\')" '+
																				'/>'+
																		'</td>'+
																'</tr>';
		}else {
			rowSrc = '<tr><td colspan="11" style="">No such laboratory service exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;

	}
}