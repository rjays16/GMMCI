//added by VAN 10-02-09
function prepareAdd(id) {
	 xajax_getAllServiceOfPackage(id);
}

//for a service that is a package
function prepareAdd_Package(id,name,cash,charge,sservice,group,priceC1,priceC2,priceC3) {
	var details = new Object();

	details.id = id;
	details.name = name;

	details.prcCash = cash;
	details.prcCharge= charge;
	details.sservice = sservice;
	details.group = group;

	details.price_C1 = priceC1;
	details.price_C2 = priceC2;
	details.price_C3 = priceC3;

	var list = window.parent.document.getElementById('lab-list');
	if(window.parent.$('counter').innerHTML=="0")
		window.parent.clearLabOrder(list);
	result = window.parent.appendLabOrder(list,details);
	//window.parent.refreshTotal();
	window.parent.refreshDiscount();
}
//----------------

//edited by VAN 10-02-09
//for a service that not a package
function prepareAdd_NotPackage(id) {
	var details = new Object();

	details.id = $('id'+id).innerHTML;
	details.name = $('name'+id).innerHTML;

	details.prcCash = $('cash'+id).value;
	details.prcCharge= $('charge'+id).value;
	details.sservice = $('sservice'+id).value;
	details.group = $('group'+id).value;

	details.price_C1 = $('price_C1'+id).value;
	details.price_C2 = $('price_C2'+id).value;
	details.price_C3 = $('price_C3'+id).value;

	var list = window.parent.document.getElementById('lab-list');
	if(window.parent.$('counter').innerHTML=="0")
		window.parent.clearLabOrder(list);
	result = window.parent.appendLabOrder(list,details);
	//window.parent.refreshTotal();
	window.parent.refreshDiscount();
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

function addProductToList(listID, id, name, cash, charge, sservice, group,price_C1,price_C2,price_C3, available) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i, label_but;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {

				alt = (dRows.length%2)+1;

				if (available==1){
					 label_but = '<td width="2%" align="center">'+
													'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px; cursor:pointer"'+
													 ' onclick="prepareAdd(\''+id+'\')" /> '+
												'</td>';
				} else
					 label_but = '<td width="2%" style="color:#FF0000" align="center">Unavailable</td>';

					rowSrc = '<tr class="wardlistrow'+alt+'">' +
											'<td width="*" align="left">'+
												'<span id="name'+id+'" style="font:bold 12px Arial">'+name+'</span><br />'+
											'</td>'+
											'<td width="15%" align="left">'+
												 '<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span>'+
												'<input id="sservice'+id+'" type="hidden" value="'+sservice+'"/></td>'+
												'<input id="group'+id+'" type="hidden" value="'+group+'"/></td>'+
												'<input id="price_C1'+id+'" type="hidden" value="'+price_C1+'"/></td>'+
												'<input id="price_C2'+id+'" type="hidden" value="'+price_C2+'"/></td>'+
												'<input id="price_C3'+id+'" type="hidden" value="'+price_C3+'"/></td>'+
											'</td>'+
											'<td align="center" width="20%">'+
												'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+cash+'</td>'+
											'<td align="center" width="20%">'+
												'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+charge+'</td>'+
											 ''+label_but+''+
										'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="9" style="">No such laboratory service exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}/* end of function trimString */
