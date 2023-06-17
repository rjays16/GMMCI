var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function display(str) {
	if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function parseFloatEx(x) {
	 if (x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
	 }
	 //Added by Omick, December 15, 2008
	 //This function should return something if the condition wasn't met
	 else {
		 return x;
	 }
}

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	if (parseInt(total))
		$("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>'
	else
		$("pageShow").innerHTML = ''
	$("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
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
			startAJAXSearch('search',currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage);
		break;
	}
}

function prepareAdd(id) {
	var details = new Object();

	var cash = parseFloatEx($('cash'+id).value),
			charge = parseFloatEx($('charge'+id).value),
			qty=0;

	if ( isNaN(cash) || (cash < 0) || isNaN(charge) || (charge < 0) ) {
		alert("Price not set. Cannot add the product to the order yet...")
		return false
	}

	details.id = $('id'+id).value
	details.stock=$('stock'+id).value
	details.name = $('name'+id).innerHTML
	details.desc = $('desc'+id).innerHTML
	details.prcCash = parseFloatEx($('cash'+id).value)
	details.prcCharge= parseFloatEx($('charge'+id).value)
	details.prcCashSC= parseFloatEx($('cashsc'+id).value)
	details.prcChargeSC= parseFloatEx($('chargesc'+id).value)
	details.isSocialized= $('soc'+id).value
	details.prcDiscounted= parseFloatEx($('d'+id).value)
		details.classification = $('classification'+id).value
	if ($('noqty'+id).value != '1') {
		/*
		while (qty) {
		}
		*/
		while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
			qty = prompt("Enter quantity:")
			if (qty === null) return false;
			if (parseFloat(qty)>parseFloat(details.stock)) {
				alert('Quantity entered exceeds quantity in stock...');
				return false;
			}
		}
	}
	details.qty = qty;

	var list = window.parent.document.getElementById('supplies-list')

	result = window.parent.appendOrderSupplies(list,details)
	if (result)
		alert('Item added to order list...');
	else
		alert('Failed to add item...');
	if (window.parent && window.parent.refreshDiscountSupplies) window.parent.refreshDiscountSupplies();
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

function addProductToList(listID, details ) {

	// ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.


		if (typeof(details)=="object") {
			var id = details.id,
				name = details.name,
				desc = details.desc,
				cash = details.cash,
				charge = details.charge,
				cashsc = details.cashsc,
				chargesc = details.chargesc,
				restricted = details.restricted,
				expiry = details.expiry,
				d = details.d,
				soc = details.soc,
				noqty = details.noqty,
								classification = details.classification;

			var cashHTML, chargeHTML;
			var cashSeniorHTML, chargeSeniorHTML;
			var noSelect=false;
			var stock=0;

			// parse expiration dates
			if (expiry.length) {
				expDates = expiry.split('\n');
				expiryHTML='<select class="segInput" id="expiry'+id+'" onchange="change">';
				for (i=0;i<expDates.length;i++) {
					exp = expDates[i].split(':');
					stock+=parseInt(exp[1]);
				}
				expiryHTML+='</select>';
			}
			else {
				expiryHTML = "-";
				qtyHTML = '<input type id="expiry'+id+'" value="0">'+'<span>0</span>';
				noSelect = true;
			}


			//if (d>=0)
			rowSrc = "<tr"+(restricted=="1"?' class="red"':"")+">"+
									'<td>'+
										(restricted=="1" ?
											('<div style="margin-top:7px; padding:2px;float:left"><img title="Restricted item" src="../../gui/img/common/default/warn2.gif" align="absmiddle" border="0"></div>'):'')+
										'<div style="float:left; padding:2px">'+
											'<span id="name'+id+'" style="font:bold 12px Arial;color:'+(restricted=='1'?'#c00000':'#000066')+'">'+name+'</span><br />'+
											'<div id="desc'+id+'" style="font:normal 11px Arial; color:'+(restricted=='1'?'#c00000':'#404040')+'">'+desc+'</div>'+
											'<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
											'<input id="id'+id+'" type="hidden" value="'+id+'"/>'+
										'</div>'+
									'</td>'+
									'<td align="right" '+(cash<=0 ? '' : '')+'>'+
										'<input id="noqty'+id+'" type="hidden" value="'+(noqty ? '1' : '0')+'"/>'+
										'<input id="d'+id+'" type="hidden" value="'+d+'"/>'+
																				'<input id="classification'+id+'" type="hidden" value="'+classification+'"/>'+
										'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+
											(d!=cash ? '<span style="color:#008000">' : '')+
											(d>0 ?
												formatNumber(d,2) : formatNumber(cash,2))+
											(d!=cash ? '</span>' : '')+
									'</td>'+
									'<td align="right">'+
										'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+(charge>0 ? formatNumber(charge,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="cashsc'+id+'" type="hidden" value="'+cashsc+'"/>'+(cashsc>0 ? formatNumber(cashsc,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="chargesc'+id+'" type="hidden" value="'+chargesc+'"/>'+(chargesc>0 ? formatNumber(chargesc,2) : '-')+
									'</td>'+
									'<td align="center">'+
										'<input id="stock'+id+'" type="hidden" value="'+stock+'"/>'+
										'<span style="color:#008000">'+stock+'</span>'+
									'</td>'+
									/*
									'<td align="center">'+
										'<input class="segInput" id="qty'+id+'" type="text" style="text-align:right;width:30px" value="" '+(noqty ? 'disabled="disabled"' : '')+' style="text-align:right" onblur="this.value = isNaN(parseFloatEx(this.value))?\'\':parseFloatEx(this.value)"/>'+
									'</td>'+
									 */
/*									'<td align="center">'+
										'<input class="jedInput" id="qty'+id+'" type="text" style="text-align:right;width:30px" value="" '+(noqty ? 'disabled="disabled"' : '')+' style="text-align:right" onblur="this.value = isNaN(parseFloatEx(this.value))?\'\':parseFloatEx(this.value)"/>'+
									'</td>'+ */
									'<td align="left">'+
										'<input id="select'+id+'" class="segButton" type="button" value=">" style="color:#000066"'+
											((parseInt(stock)<=0)?' disabled="disabled"':'')+
											' onclick="prepareAdd(\''+id+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="8" style="">No such product exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

//added by CHA 11-19-09
function addProductToAnestheticList(listID, details ) {

	// ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.


		if (typeof(details)=="object") {
			var id = details.id,
				name = details.name,
				desc = details.desc,
				cash = details.cash,
				charge = details.charge,
				cashsc = details.cashsc,
				chargesc = details.chargesc,
				restricted = details.restricted,
				expiry = details.expiry,
				d = details.d,
				soc = details.soc,
				noqty = details.noqty,
								classification = details.classification;

			var cashHTML, chargeHTML;
			var cashSeniorHTML, chargeSeniorHTML;
			var noSelect=false;
			var stock=0;

			// parse expiration dates
			if (expiry.length) {
				expDates = expiry.split('\n');
				expiryHTML='<select class="segInput" id="expiry'+id+'" onchange="change">';
				for (i=0;i<expDates.length;i++) {
					exp = expDates[i].split(':');
					stock+=parseInt(exp[1]);
				}
				expiryHTML+='</select>';
			}
			else {
				expiryHTML = "-";
				qtyHTML = '<input type id="expiry'+id+'" value="0">'+'<span>0</span>';
				noSelect = true;
			}


			//if (d>=0)
			rowSrc = "<tr"+(restricted=="1"?' class="red"':"")+">"+
									'<td>'+
										(restricted=="1" ?
											('<div style="margin-top:7px; padding:2px;float:left"><img title="Restricted item" src="../../gui/img/common/default/warn2.gif" align="absmiddle" border="0"></div>'):'')+
										'<div style="float:left; padding:2px">'+
											'<span id="name'+id+'" style="font:bold 12px Arial;color:'+(restricted=='1'?'#c00000':'#000066')+'">'+name+'</span><br />'+
											'<div id="desc'+id+'" style="font:normal 11px Arial; color:'+(restricted=='1'?'#c00000':'#404040')+'">'+desc+'</div>'+
											'<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
											'<input id="id'+id+'" type="hidden" value="'+id+'"/>'+
										'</div>'+
									'</td>'+
									'<td align="right" '+(cash<=0 ? '' : '')+'>'+
										'<input id="noqty'+id+'" type="hidden" value="'+(noqty ? '1' : '0')+'"/>'+
										'<input id="d'+id+'" type="hidden" value="'+d+'"/>'+
																				'<input id="classification'+id+'" type="hidden" value="'+classification+'"/>'+
										'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+
											(d!=cash ? '<span style="color:#008000">' : '')+
											(d>0 ?
												formatNumber(d,2) : formatNumber(cash,2))+
											(d!=cash ? '</span>' : '')+
									'</td>'+
									'<td align="right">'+
										'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+(charge>0 ? formatNumber(charge,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="cashsc'+id+'" type="hidden" value="'+cashsc+'"/>'+(cashsc>0 ? formatNumber(cashsc,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="chargesc'+id+'" type="hidden" value="'+chargesc+'"/>'+(chargesc>0 ? formatNumber(chargesc,2) : '-')+
									'</td>'+
									'<td align="center">'+
										'<input id="stock'+id+'" type="hidden" value="'+stock+'"/>'+
										'<span style="color:#008000">'+stock+'</span>'+
									'</td>'+
									/*
									'<td align="center">'+
										'<input class="segInput" id="qty'+id+'" type="text" style="text-align:right;width:30px" value="" '+(noqty ? 'disabled="disabled"' : '')+' style="text-align:right" onblur="this.value = isNaN(parseFloatEx(this.value))?\'\':parseFloatEx(this.value)"/>'+
									'</td>'+
									 */
/*									'<td align="center">'+
										'<input class="jedInput" id="qty'+id+'" type="text" style="text-align:right;width:30px" value="" '+(noqty ? 'disabled="disabled"' : '')+' style="text-align:right" onblur="this.value = isNaN(parseFloatEx(this.value))?\'\':parseFloatEx(this.value)"/>'+
									'</td>'+ */
									'<td align="left">'+
										'<input id="select'+id+'" class="segButton" type="button" value=">" style="color:#000066"'+
											((parseInt(stock)<=0)?' disabled="disabled"':'')+
											' onclick="prepareAddAnesthetic(\''+id+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="8" style="">No such product exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

function prepareAddAnesthetic(id) {
	var details = new Object();

	var cash = parseFloatEx($('cash'+id).value),
			charge = parseFloatEx($('charge'+id).value),
			qty=0;

	if ( isNaN(cash) || (cash < 0) || isNaN(charge) || (charge < 0) ) {
		alert("Price not set. Cannot add the product to the order yet...")
		return false
	}

	details.id = $('id'+id).value
	details.stock=$('stock'+id).value
	details.name = $('name'+id).innerHTML
	details.desc = $('desc'+id).innerHTML
	details.prcCash = parseFloatEx($('cash'+id).value)
	details.prcCharge= parseFloatEx($('charge'+id).value)
	details.prcCashSC= parseFloatEx($('cashsc'+id).value)
	details.prcChargeSC= parseFloatEx($('chargesc'+id).value)
	details.isSocialized= $('soc'+id).value
	details.prcDiscounted= parseFloatEx($('d'+id).value)
		details.classification = $('classification'+id).value
	if ($('noqty'+id).value != '1') {

		while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
			qty = prompt("Enter quantity:")
			if (qty === null) return false;
			if (parseFloat(qty)>parseFloat(details.stock)) {
				alert('Quantity entered exceeds quantity in stock...');
				return false;
			}
		}
	}
	details.qty = qty;
	//var data = {"codename":details.name,"codeid":details.id,"qty":details.qty,"pcharge":details.prcCharge,"pcash":details.prcCash};
	/*var list = window.parent.document.getElementById('or_anesthesia_table-body') */
	var text = details.name.split(",");
	var textname='';
	for(x=0;x<text.length;x++)
	{
		textname+=text[x];
	}
	//alert(textname);
	rowSrc = "<tr id='list_anesthetic_"+details.id+"'>"+
		"<input type='hidden' id='name"+details.id+"' value='"+textname+"'/>"+
		"<input type='hidden' id='qty"+details.id+"' value='"+details.qty+"'/>"+
		"<input type='hidden' id='pcash"+details.id+"' value='"+details.prcCash+"'/>"+
		"<input type='hidden' id='pcharge"+details.id+"' value='"+details.prcCharge+"'/>"+
		"<input type='hidden' value='"+details.id+"' name='"+$('tableid').value+"' id='anesth_id"+$('tableid').value+"'/>"+
		"</tr>";

	if(window.parent.document.getElementById('anesthetics_list').innerHTML += rowSrc)
	{
		window.parent.document.getElementById('is_added').value="1";
		window.parent.document.getElementById('view-anesth'+$('tableid').value).style.display="";
		window.parent.document.getElementById('rowspacer'+$('tableid').value).style.display="none";
		alert('Item added to order list...');
	}
	else
	{
		 alert('Failed to add item...');
	}
	/*result = window.parent.appendOrderSupplies(list,details)
	if (result)
		alert('Item added to order list...');
	else
		alert('Failed to add item...');
	if (window.parent && window.parent.refreshDiscountSupplies) window.parent.refreshDiscountSupplies();*/
}

function addPackageItemsToList(listID, details ) {

	// ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		//alert('hello1')

		if (typeof(details)=="object") {
			var id = details.id,
				name = details.name,
				desc = details.desc,
				restricted = details.restricted,
				options = details.opt,
				mode = details.mode;
											//alert('hello2')
			//if (d>=0)
			rowSrc = "<tr"+(restricted=="1"?' class="red"':"")+">"+
									'<td>'+
										(restricted=="1" ?
											('<div style="margin-top:7px; padding:2px;float:left"><img title="Restricted item" src="../../gui/img/common/default/warn2.gif" align="absmiddle" border="0"></div>'):'')+
										'<div style="float:left; padding:2px">'+
											'<span id="name'+id+'" style="font:bold 12px Arial;color:'+(restricted=='1'?'#c00000':'#000066')+'">'+name+'</span><br />'+
											'<div id="desc'+id+'" style="font:normal 11px Arial; color:'+(restricted=='1'?'#c00000':'#404040')+'">'+desc+'</div>'+
										'</div>'+
									'</td>'+
									'<td>'+
										'<input type="text" id="qty'+id+'" size="5" onkeydown="return key_check(event, this.value)" maxlength="3"/>'+
									'</td>'+
									//'<td>'+
//										'<select id="unit'+id+'">'+
//											options+
//										'</select>'+
//									'</td>'+
									'<td align="left">'+
										'<input id="select'+id+'" class="segButton" type="button" value=">" style="color:#000066"'+
											' onclick="prepareAddPackageItem(\''+id+'\',\''+restricted+'\',\''+mode+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="8" style="">No such product exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}
function prepareAddPackageItem(id,restricted, mode) {
	var details = new Object();

	details.id = id;
	details.name = $('name'+id).innerHTML;
	details.desc = $('desc'+id).innerHTML;
	details.qty = $('qty'+id).value;
	//details.unit = $('unit'+id).value;
	if(parseFloat($('qty'+id).value)>0){
				var exist = mode+'itemlist'+details.id;
			if(window.parent.document.getElementById(exist)==null){
				var rowSrc='<tr id="'+mode+'itemlist'+details.id+'">'+
				'<td width="1%" align="left">'+
					'<img src="../../../images/or_main_images/delete_item.png" style="cursor:pointer;" onclick="remove_package_item(\''+mode+'\',\''+details.id+'\');" title="Delete Item">'+
					'<input type="hidden" id="'+mode+'_item_id[]" name="'+mode+'_item_id[]" value="'+details.id+'"/>'+
				'</td>'+
				'<td width="1%" align="left" nowrap="nowrap">'+
					'<div style="float:left; padding:2px">'+
						'<span id="name'+details.id+'" style="font:bold 12px Arial;color:'+(restricted=='1'?'#c00000':'#000066')+'">'+details.name+'</span><br />'+
						'<div id="desc'+details.id+'" style="font:normal 11px Arial; color:'+(restricted=='1'?'#c00000':'#404040')+'">'+details.desc+'</div>'+
					'</div>'+
				'</td>'+
				'<td width="5%" align="left" nowrap="nowrap">'+
					'<div style="float:left; padding:2px">'+
					'<span id="name'+details.id+'" style="font:bold 12px Arial;">'+details.qty+'</span><br />'+
					'</div>'+
					'<input type="hidden" id="'+mode+'_item_qty[]" name="'+mode+'_item_qty[]" value="'+details.qty+'"/>'+
					'<input type="hidden" id="'+mode+'_itm_qty'+details.id+'" name="'+mode+'_itm_qty'+details.id+'" value="'+details.qty+'"/>'+
					//'<input type="hidden" id="'+mode+'_item_unit[]" name="'+mode+'_item_unit[]" value="'+details.unit+'"/>'+
					//'<input type="hidden" id="item_purpose[]" name="item_purpose[]" value="'+mode+'"/>'+
				'</td>'+
				'</tr>';
				window.parent.document.getElementById('items_table').innerHTML += rowSrc;
				window.parent.document.getElementById('item_purpose_list').innerHTML+='<input type="hidden" id="item_purpose[]" name="item_purpose[]" value="'+mode+'"/>';
			}
			else{
				var prvQty = mode+'_itm_qty'+details.id;
				var actualQty= parseFloat(window.parent.document.getElementById(prvQty).value) + parseFloat(details.qty);
				var rowSrc=''+
				'<td width="1%" align="left">'+
					'<img src="../../../images/or_main_images/delete_item.png" style="cursor:pointer;" onclick="remove_package_item(\''+mode+'\',\''+details.id+'\');">'+
					'<input type="hidden" id="'+mode+'_item_id[]" name="'+mode+'_item_id[]" value="'+details.id+'"/>'+
				'</td>'+
				'<td width="1%" align="left" nowrap="nowrap">'+
					'<div style="float:left; padding:2px">'+
						'<span id="name'+details.id+'" style="font:bold 12px Arial;color:'+(restricted=='1'?'#c00000':'#000066')+'">'+details.name+'</span><br />'+
						'<div id="desc'+details.id+'" style="font:normal 11px Arial; color:'+(restricted=='1'?'#c00000':'#404040')+'">'+details.desc+'</div>'+
					'</div>'+
				'</td>'+
				'<td width="5%" align="left" nowrap="nowrap">'+
					'<div style="float:left; padding:2px">'+
					'<span id="name'+details.id+'" style="font:bold 12px Arial;">'+actualQty+'</span><br />'+
					'</div>'+
					'<input type="hidden" id="'+mode+'_item_qty[]" name="'+mode+'_item_qty[]" value="'+actualQty+'"/>'+
					'<input type="hidden" id="'+mode+'_itm_qty'+details.id+'" name="'+mode+'_itm_qty'+details.id+'" value="'+actualQty+'"/>'+

					//'<input type="hidden" id="'+mode+'_item_unit[]" name="'+mode+'_item_unit[]" value="'+details.unit+'"/>'+
					//'<input type="hidden" id="item_purpose[]" name="item_purpose[]" value="'+mode+'"/>'+
				'</td>';
				window.parent.document.getElementById(exist).innerHTML = rowSrc;
			}
	}
	else{
		alert("Please enter quantity.");
		$('qty'+id).focus();
	}
}

function key_check(e, value){
	var number = /^\d+$/;
	//---edited by CHA, Feb 1, 2010----
	//alert(e.keyCode);
	if((e.keyCode>= 48 && e.keyCode<=57) || (e.keyCode>= 96 && e.keyCode<=105) || (e.keyCode==127) || (e.keyCode==8) || (e.keyCode>= 28 && e.keyCode<=31))
	{
		return true;
	}
	else
	{
		return false;
	}
	//----end CHA----------------------
}
//end CHA