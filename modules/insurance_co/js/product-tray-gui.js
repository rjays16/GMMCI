function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function prepareAdd(id) {
	var details = new Object();
	details.id = $('id'+id).innerHTML;
	details.name = $('name'+id).innerHTML;
	details.desc = $('desc'+id).innerHTML;
	details.amtlimit= $('amtlimit'+id).value;
	details.areas= $('areas'+id).value;
	var list = window.parent.document.getElementById('product-list');
	result = window.parent.appendOrder(list,details);
}

function enableButtonAdd(id){
	document.getElementById('add_item'+id).disabled=false;
}

function disableButtonAdd(id){
	document.getElementById('add_item'+id).disabled=true;	
}

function preset(){
	//alert("preset = "+window.parent.$('benefit_id').value);
	document.getElementById('benefit').value = window.parent.$('benefit_id').value;
	document.getElementById('area').value = window.parent.$('area').value;
	document.getElementById('serv_areas').focus();
}

function getServices(){
	var benefit = document.getElementById('benefit');
	var area = document.getElementById('area');
	var option;	
	//alert("area = "+area.value);
	//alert("benefit = "+benefit.value);
	
	//if (benefit.value == 2){
	if (area.value == 'MS'){	
		// drugs and medicines
		option = '<option value="0">-Select Service Area-</option>'+
					'<option value="DM">Drugs and Medicines</option>';
	//}else if ((benefit.value == 3)||(benefit.value == 6)||(benefit.value == 7)||(benefit.value == 8)){
	}else if (area.value == 'HS'){	
		// laboratory, radiology, others
		option = '<option value="0">-Select Service Area-</option>'+
					'<option value="LB">Laboratory</option>'+
					'<option value="RD">Radiology</option>'+
					'<option value="OA">Other Services</option>';	
	//}else if (benefit.value == 5){
	}else if (area.value == 'OR'){	
		// operating procedures
		option = '<option value="0">-Select Service Area-</option>'+
					'<option value="OR">OR Procedures</option>';
	}else{
		
		option = '<option value="0">-Select Service Area-</option>';
	}
				
	serv_areas.innerHTML = option;	
	//alert(serv_areas.innerHTML);
}

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dBody;
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

function addProductToList(listID, id, name, desc, areas) {
	var list=$(listID), dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		//alert("id = "+id);
		// get the last row id and extract the current row no.
		if (id) {
			//alert("addProductToList b4 = "+dBody.innerHTML);
			rowSrc = "<tr>"+
									'<td>'+
										'<span id="name'+id+'" style="font:bold 12px Arial">'+name+'</span><br />'+
										'<span id="desc'+id+'" style="font:normal 11px Arial; color:#003366">'+desc+'</span>'+
										'<input type="hidden" id="areas'+id+'" name="areas'+id+'" value="'+areas+'">'+
									'</td>'+
									'<td><span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
									'<td align="center"><input id="amtlimit'+id+'" align="right" type="text" style="width:100%" value="" onKeyUp="if ((this.value.length >= 1)&& !isNaN(this.value)) enableButtonAdd(\''+id+'\'); else disableButtonAdd(\''+id+'\');" style="text-align:right" onblur="this.value = isNaN(parseFloat(this.value))?\'\':parseFloat(this.value); formatNumber(this.value)"/></td>'+
									'<td>'+
										'<input name="add_item'+id+'" id="add_item'+id+'" type="button" disabled value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
											'onclick="prepareAdd(\''+id+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';				
		}
		else {
			rowSrc = '<tr><td colspan="4" style="">No such item exists...</td></tr>';
		}
		//alert("addProductToList after = "+dBody.innerHTML);
		dBody.innerHTML += rowSrc;
	}
}
