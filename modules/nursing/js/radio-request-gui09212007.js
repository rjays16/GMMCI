var totalDiscount = 0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function clearEncounter() {
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	$('ordername').readOnly=!iscash;
	$('orderaddress').value="";
	$('orderaddress').readOnly=!iscash;
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
}

function pSearchClose() {
//	alert("radio-request-gui.js : pSearchClose : ");
	cClick();  //function in 'overlibmws.js'
/*
	var nr = $('encounter_nr').value;
	alert("pSearchClose : nr='"+nr+"'");
	if (nr) xajax_get_charity_discounts(nr);
*/
}

function emptyTray() {
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshDiscount();
}


function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function clearOrder(list) {	
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function appendOrder(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var isCash = $("iscash1").checked;
			var totalCash, totalCharge;
			var src;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			nf.setPlaces(2);
			
			if (details) {
				var id = details.id,
					idGrp = details.idGrp,
					qty = parseFloat(details.qty),
					prcCash = parseFloat(details.prcCash),
					prcCharge = parseFloat(details.prcCharge);
					totalCash = prcCash*qty;
					totalCharge = prcCharge*qty;

				alt = (dRows.length%2)+1;
				nf.setNumber(qty);
				nf.setPlaces(nf.NO_ROUNDING);
				qty = isNaN(qty) ? '0' : ''+nf.toFormatted();

				nf.setPlaces(2);
				nf.setNumber(prcCash);
				prcCash = isNaN(prcCash) ? 'N/A' : nf.toFormatted();				
				nf.setNumber(totalCash);
				totalCash = isNaN(totalCash) ? 'N/A' : nf.toFormatted();
				nf.setNumber(prcCharge);
				prcCharge = isNaN(prcCharge) ? 'N/A' : nf.toFormatted();				
				nf.setNumber(totalCharge);
				totalCharge = isNaN(totalCharge) ? 'N/A' : nf.toFormatted();

				if (isCash) {
					prc=prcCash;
					tot=totalCash;
				}
				else {
					prc=prcCharge;
					tot=totalCharge;
				}

				if (items) {
					for (var i=0;i<items.length;i++) {
						if (items[i].value == details.id) {
							alert('"'+details.name+'" is already in the list!');
/*
							var itemRow = $('row'+items[i].value),
									itemQty = $('rowQty'+items[i].value);
							
							itemQty.value = parseFloat(itemQty.value) + parseFloat(details.qty);
							qty = parseFloat(itemQty.value);
							
							nf.setNumber(qty*prcCash);							
							totalCash = nf.toFormatted();
							nf.setNumber(qty*prcCharge);
							totalCharge = nf.toFormatted()
					
							if (isCash) {
								prc=prcCash;
								tot=totalCash;
							}
							else {
								prc=prcCharge;
								tot=totalCharge;
							}

							$('rowPrcCash'+id).value = details.prcCash;
							$('rowPrcCharge'+id).value = details.prcCharge;
							$('rowDoc'+id).value = details.requestDoc;
							$('rowDocName'+id).value = details.requestDocName;
							$('rowHouse'+id).value = details.is_in_house;
							$('rowInfo'+id).value = details.clinicInfo;
							$('rowQty'+id).value = details.qty;
							$('qty'+id).value = details.qty;
							$('idGrp'+id).innerHTML = idGrp;
							$('name'+id).innnerHTML = details.name;
							$('prc'+id).innerHTML = prc;
							$('tot'+id).innerHTML = tot;
*/							return true;
						}
					}
					if (items.length == 0)
	 					clearOrder(list);
				}

				src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
					'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="requestDoc[]" id="rowDoc'+id+'" value="'+details.requestDoc+'" />'+
					'<input type="hidden" name="requestDocName[]" id="rowDocName'+id+'" value="'+details.requestDocName+'" />'+
					'<input type="hidden" name="isInHouse[]" id="rowHouse'+id+'" value="'+details.is_in_house+'" />'+
					'<input type="hidden" name="clinicInfo[]" id="rowInfo'+id+'" value="'+details.clinicInfo+'" />'+
					'<input type="hidden" name="qty[]" id="rowQty'+id+'" value="'+details.qty+'" />'+
					'<input type="hidden" name="qty'+id+'" id="qty'+id+'" value="'+details.qty+'" />'+
					'<td class="centerAlign"><a href="javascript:removeItem(\''+id+'\')">'+
					'	<img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
					'<td align="centerAlign"></td>'+
					'<td id="idGrp'+id+'">'+idGrp+'</td>'+
					'<td id="name'+id+'">'+details.name+'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+prc+'</td>'+
//					'<td class="centerAlign" id="qty'+id+'">'+qty+'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
				'</tr>';
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"6\">Request list is currently empty...</td></tr>";	
			}
			alert("appendOrder : src : \n"+src);
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));		
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
		//burn added : September 13, 2007
	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		emptyIntialRequestList();
	}
	refreshTotal();
}
/*
function changeTransactionType() {
	var iscash = $("iscash1").checked;
	var prcList, id, total=0;
	var pid = $('pid').value;
	if (iscash) {
		$('ordername').readOnly=pid;
		$('orderaddress').readOnly=pid;
		$('clear-enc').disabled=!pid;
		prcList = document.getElementsByName("pcash[]");
	}
	else {
		$('clear-enc').disabled=!pid;
		$('ordername').readOnly=true;
		$('orderaddress').readOnly=true;
		prcList = document.getElementsByName("pcharge[]");
	}
	for (var i=0;i<prcList.length;i++) {
		if (iscash)
			id = prcList[i].id.substring(10);
		else
			id = prcList[i].id.substring(12);
		$('prc'+id).innerHTML = formatNumber(prcList[i].value,2);
		$('tot'+id).innerHTML = formatNumber(parseFloat($('rowQty'+id).value)*parseFloat(prcList[i].value),2);
	}
	refreshTotal();
}
*/
function changeTransactionType() {
	var iscash = $("iscash1").checked;
	var prcList, id, total=0;
	var pid = $('pid').value;
	clearEncounter();

	if (iscash)
		prcList = document.getElementsByName("pcash[]");
	else
		prcList = document.getElementsByName("pcharge[]");

	for (var i=0;i<prcList.length;i++) {
		if (iscash)
			id = prcList[i].id.substring(10);
		else
			id = prcList[i].id.substring(12);
		$('prc'+id).innerHTML = formatNumber(prcList[i].value,2);
		$('tot'+id).innerHTML = formatNumber(parseFloat($('rowQty'+id).value)*parseFloat(prcList[i].value),2);
	}
	refreshTotal();
}

function refreshDiscount() {
	var nodes = document.getElementsByName("discount[]");
	totalDiscount = 0;
	for (var i=0;i<nodes.length;i++) {
		if (nodes[i].value) totalDiscount += parseFloat(nodes[i].getAttribute('discount'));
	}
	var dItem = $("show-discount");
	if (dItem) {
		dItem.value = parseFloat(totalDiscount * 100).toFixed(2);
	}
	refreshTotal();
}

function refreshTotal() {
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();

	total = 0.0;
	for (var i=0;i<items.length;i++) {
		if (isCash)
			total+=parseFloat(cash[i].value)*parseFloat(qty[i].value);
		else
			total+=parseFloat(charge[i].value)*parseFloat(qty[i].value);
	}

	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");
	
	subTotal.innerHTML = formatNumber(total.toFixed(2),2);
	discountTotal.innerHTML = "-"+formatNumber((total * totalDiscount).toFixed(2),2);
	netTotal.innerHTML = formatNumber((total - (total * totalDiscount)).toFixed(2),2);
//	alert("refreshTotal : subTotal.innerHTML='"+subTotal.innerHTML+"' \nnetTotal.innerHTML='"+netTotal.innerHTML+"'");
}

function preset(iscash){
//	alert("preset : iscash = '"+iscash+"' \nF('ordername') = '"+$F('ordername')+"'");
	$('ordername').readOnly=!iscash;
	$('orderaddress').readOnly=!iscash;
	if ($F('ordername')){
		$('clear-enc').disabled = false;
	}else{
		$('clear-enc').disabled = true;
	}
}

/*
		burn added : September 13, 2007
*/
function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
}

function initialRequestList(serv_code,grp_code,name,c_info,r_doc,r_doc_name,n_house,cash,charge) {
	var details = new Object();

		var msg = "serv_code='"+serv_code+"'\ngrp_code='"+grp_code+
					 "'\nname='"+name+"\nc_info='"+c_info+"'\nr_doc='"+r_doc+"'\nn_house='"+n_house+
					 "'\ncash='"+cash+"'\ncharge='"+charge+"'\n";	
//		alert("initialRequestList 1 : "+msg);

		details.requestDoc= r_doc;
		details.requestDocName= r_doc_name;
		details.is_in_house= n_house;
		details.clinicInfo= c_info;
		details.idGrp = serv_code+' ('+grp_code+')';
		details.id = serv_code;
		details.qty = 1;
		details.name = name;
		details.prcCash = cash;
		details.prcCharge= charge;
		//details.discount_name= $('discount_name'+id).value;
		var list = document.getElementById('order-list');
		var msg = "requestDoc='"+details.requestDoc+"'\ndetails.is_in_house='"+details.is_in_house+
					 "'\ndetails.qty='"+details.qty+"\nserv_code='"+serv_code+"'\ndetails.id='"+details.id+
					 "'\ndetails.idGrp='"+details.idGrp+
					 "'\ndetails.name='"+details.name+"'\ndetails.prcCash='"+details.prcCash+
					 "'\ndetails.prcCharge='"+details.prcCharge+"'\n";	
//		alert("initialRequestList 2 : "+msg);

		result = appendOrder(list,details);
//		refreshTotal();	
}
		/*	
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words 
				input: object
				output: object (string) value is trimmed
		*/
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," "); 
}/* end of function trimString */


	function checkRequestForm(){
	var items = document.getElementsByName('items[]');
	var iscash = $("iscash1").checked;

		if (iscash)
			$('is_cash').value=1;
		else
			$('is_cash').value=0;

//	alert("checkRequestForm : items.length = '"+items.length+"' \n iscash='"+iscash+"'\n$F(encounter_nr)='"+$F('encounter_nr')+"'\n$F(pid)='"+$F('pid')+"'\n$F(is_cash)='"+$F('is_cash')+"'");
//		alert("$F('save') = '"+$F('save')+"'");
/*
		if ($F('save')=='0'){
//			alert("false : $F('save') = '"+$F('save')+"'");
			// if the button clicked is not the for Referral, SAVE or SAVE&DONE buttons
			return false;
		}
*/
		if (items.length==0){
			alert("Please add a request first.");
			$('btnAdd').focus();
			return false;	
		}else if($F('ordername') == ''){
			alert("Please indicate the patient's name's.");
			if (iscash)
				$('ordername').focus();
			else
				$('select-enc').focus();
			return false;
		}else if($F('request_date') == ''){
			alert("Please indicate the date of service.");
			$('request_date').focus();
			return false;
		}
/*		
		else	if (($F('is_in_house')=='0') && ($F('request_doctor_out')=='')){
			alert("Please specify the requesting doctor");
			$('request_doctor_out').focus();
			return false;	
		}

		if ($F('is_in_house')=='1'){
			$('request_doctor').value = $F('request_doctor_in');
		}else{
			$('request_doctor').value = $F('request_doctor_out');
		}
*/
		if (iscash)
			$('is_cash').value=1;
		else
			$('is_cash').value=0;
		return true;
	}

