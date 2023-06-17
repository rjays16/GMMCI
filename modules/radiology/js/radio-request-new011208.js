var totalDiscount = 0, totalDiscountedAmount=0, totalNet=0, totalNONSocializedAmount=0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function clearEncounter() {
	var rid = $F('rid');
//	alert("clearEncounter : rid = '"+rid+"'");
	return;
	var iscash = $("iscash1").checked;
	$('ordername').value="";
//	$('ordername').readOnly=!iscash;
	$('orderaddress').value="";
//	$('orderaddress').readOnly=!iscash;
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
//	alert("clearEncounter : b4 clearing of value : \n $F('discount') = '"+$F('discount')+"' \n $F('discountid') = '"+$F('discountid')+"'");
	$('discount').value = '';
	$('discountid').value = '';
	refreshDiscount();
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
		$('socialServiceNotes').style.display='none';
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

/*
		burn added : October 12, 2007
*/
function showSocialNotes() {	
	var isShow='none';
	var sservice = document.getElementsByName('sservice[]');
	for (var i=0;i<sservice.length;i++) {
		if (sservice[i].value == 0) {
			isShow='';   //there is still a nonsocialized item in the list
		}
	}
	$('socialServiceNotes').style.display=isShow;
}

function appendOrder(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var isCash = $("iscash1").checked;
			var totalCash, totalCharge;
			var src, toolTipText;
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
//				toolTipText = "<span style='text-align:justify'>Requesting doctor: <br>   "+details.requestDocName+" <br>"+
//									"Clinical Impression: <br>   "+details.clinicInfo+ "</span>";
//				toolTipText = "Requesting doctor: "+details.requestDocName+" "+
//									"Clinical Impression: "+details.clinicInfo;
				toolTipText = "Requesting doctor: <br>   "+details.requestDocName+" <br>"+
									"Clinical Impression: <br>   "+details.clinicInfo;

				if (items) {
					for (var i=0;i<items.length;i++) {
						if (items[i].value == details.id) {
							$('toolTipText'+id).value = toolTipText;
							$('rowPrcCash'+id).value = details.prcCash;
							$('rowPrcCharge'+id).value = details.prcCharge;
							$('rowDoc'+id).value = details.requestDoc;
							$('rowDocName'+id).value = details.requestDocName;
							$('rowHouse'+id).value = details.is_in_house;
							$('rowInfo'+id).value = details.clinicInfo;
							$('rowQty'+id).value = details.qty;
							$('qty'+id).value = details.qty;
							document.getElementById('idGrp'+id).innerHTML = idGrp;
							document.getElementById('name'+id).innerHTML = details.name;
							document.getElementById('prc'+id).innerHTML = prc;
							document.getElementById('tot'+id).innerHTML = tot;
/*							$('idGrp'+id).innerHTML = idGrp;
							$('name'+id).innnerHTML = details.name;
							$('prc'+id).innerHTML = prc;
							$('tot'+id).innerHTML = tot;
*/							alert('"'+details.name+'" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (items.length == 0)
	 					clearOrder(list);
				}
//					'<tr class="wardlistrow'+alt+'" id="row'+id+'" title="'+toolTipText+'" '+
//					'		onMouseOver="return overlib(\''+toolTipText+'\', AUTOSTATUS,  TEXTPADDING, 4, FGCOLOR, \'#cceecc\', TEXTFONTCLASS, \'oltxt\', WRAP);" onmouseout="nd();">' +
//					'		onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Details\');" onmouseout="nd();">' +
			delitemImg = '<a href="javascript: nd(); removeItem(\''+id+'\');">'+
							 '	<img src="../../images/btn_delitem.gif" border="0"/></a>';		
			paiditemImg = '<img src="../../images/btn_paiditem.gif" border="0" onClick="">';
			unpaiditemImg = '<img src="../../images/btn_unpaiditem.gif" border="0" onClick="">';
			refno_hasPaid = $F('hasPaid');
			view_mode = 0;
			if ($F('view_from')!='')
				view_mode = 1;
			toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Details\',  '+
							'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
							'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';
			nonSocialized='';
			if (details.sservice==0){
				nonSocialized='<img src="../../images/btn_nonsocialized.gif" border="0" onClick=""'+
								' onMouseOver="return overlib(\'This is a non-socialized service which means..secret!\', CAPTION,\'Non-socialized Service\',  '+
								'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
								'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();">';
				$('socialServiceNotes').style.display='';
			}
				//nonSocialized='<span style="color:#FF0000;font-size:14px">+&nbsp;</span>';
/*
					'		onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Details\',  '+
					'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
					'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();">' +
*/
			src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'"> '+
					'<input type="hidden" name="toolTipText'+id+'" id="toolTipText'+id+'" value="'+toolTipText+'" />'+
					'<input type="hidden" name="sservice[]" id="sservice'+id+'" value="'+details.sservice+'" />'+
					'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="pnet[]" id="rowPrcNet'+id+'" value="">'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="requestDoc[]" id="rowDoc'+id+'" value="'+details.requestDoc+'" />'+
					'<input type="hidden" name="requestDocName[]" id="rowDocName'+id+'" value="'+details.requestDocName+'" />'+
					'<input type="hidden" name="isInHouse[]" id="rowHouse'+id+'" value="'+details.is_in_house+'" />'+
					'<input type="hidden" name="clinicInfo[]" id="rowInfo'+id+'" value="'+details.clinicInfo+'" />'+
					'<input type="hidden" name="qty[]" id="rowQty'+id+'" value="'+details.qty+'" />'+
					'<input type="hidden" name="qty'+id+'" id="qty'+id+'" value="'+details.qty+'" />'+
					'<td class="centerAlign">'+
					((view_mode==1||(tot==0))?"&nbsp;":((details.hasPaid==1)?paiditemImg:((refno_hasPaid==1)?unpaiditemImg:delitemImg)))
					+'</td>'+
					'<td align="centerAlign">'+nonSocialized+'</td>'+
					'<td id="idGrp'+id+'"'+toolTipTextHandler+'>'+idGrp+'</td>'+
					'<td id="name'+id+'"'+toolTipTextHandler+'>'+details.name+'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+prc+'</td>'+
//					'<td class="centerAlign" id="qty'+id+'">'+qty+'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
					'</tr>';
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"6\">Request list is currently empty...</td></tr>";	
			}
//			alert("appendOrder : src : \n"+src);
			dBody.innerHTML += src;
//			alert("appendOrder : $('row"+details.id+"').title = '"+$('row'+details.id).title+"'");
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
		$('rowPrcNet'+id).parentNode.removeChild($('rowPrcNet'+id));		
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		$('sservice'+id).parentNode.removeChild($('sservice'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
		//burn added : September 13, 2007
	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		emptyIntialRequestList();
	}
	showSocialNotes();
//	refreshTotal();
	refreshDiscount();
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
//	refreshTotal();
	refreshDiscount();	
}

function checkCharityAmount(totalNONSocializedAmount){
	var dAdjAmount = $("show-discount"), adjusted_amount=0,msg='';

	if (dAdjAmount) {
//		dItem.value = parseFloat(totalDiscount * 100).toFixed(2);
		adjusted_amount=dAdjAmount.value;
	}
	if ((totalNet-totalNONSocializedAmount) - parseFloat(adjusted_amount) < 0){
		//the adjusted amount is MORE than the Net Total (OR payable amount)
		msg="ERROR :: One of these ocurred, \n"+
				"  [1] The Adjusted Amount is MORE than the Net Total (OR payable amount). \n"+
				"  [2] Non-socialized services are not covered in the Adjusted Amount.";
		alert(msg);
		dAdjAmount.value = $F('latest_valid_show-discount');
		return false;
	}else{
		//alert("totalDiscountedAmount = '"+totalDiscountedAmount+"'");
		totalDiscountedAmount += parseFloat(adjusted_amount);
		totalNet = totalNet - parseFloat(adjusted_amount);	
		return true;
	}
}

function refreshDiscount() {
/*
	var nodes = document.getElementsByName("discount[]");
	totalDiscount = 0;
	for (var i=0;i<nodes.length;i++) {
		if (nodes[i].value) totalDiscount += parseFloat(nodes[i].getAttribute('discount'));
	}
*/
	var nodes = $("discount");
	totalDiscount = nodes.value;
	totalNet = 0;
	totalDiscountedAmount = 0;
	totalNONSocializedAmount = 0;

	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var net = document.getElementsByName('pnet[]');
	var sservice = document.getElementsByName('sservice[]');
	var prcCash, prcCharge, qty=1, id, isCash = $("iscash1").checked;


	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		qty = $('rowQty'+id).value;
		prcCash = parseFloat(cash[i].value);
		totalCash = prcCash*qty;
		prcCharge = parseFloat(charge[i].value);
		totalCharge = prcCharge*qty;
		if (isCash){
			tot = totalCash;			
		}else{
			tot = totalCharge;
		}
		// NOTE : (burn, December 19, 2007)
		//		[a] Any form of discounts on CHARGED type of transaction will be reflected 
		//				on its final billing statement
		if ((isCash)&&(sservice[i].value=='1')){
				//if socialized service & type of transaction is cash
			tot = totalCash - (totalCash*totalDiscount);
			totalDiscountedAmount += parseFloat(totalCash*totalDiscount);
		}
		if ((isCash)&&(sservice[i].value=='0')){
				//if NON-socialized service & type of transaction is cash
			totalNONSocializedAmount += parseFloat(tot);
		}

		document.getElementById('tot'+id).innerHTML = formatNumber(tot.toFixed(2),2);
		net[i].value = parseFloat(tot);
		totalNet += parseFloat(tot);
	}
//alert("refreshDiscount : totalDiscountedAmount = '"+totalDiscountedAmount+"'");
	var correctCharity=checkCharityAmount(totalNONSocializedAmount);   //adjustments from Social Service
	refreshTotal();
	var msg='';
	for (var i=0;i<net.length;i++) {
		msg += "\n net["+i+"] = '"+net[i].value+"'";
	}
//alert("refreshDiscount :: "+msg);
	return correctCharity;
}

function refreshTotal() {
//	alert("refreshTotal");
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
//	discountTotal.innerHTML = "-"+formatNumber((total * totalDiscount).toFixed(2),2);
//	netTotal.innerHTML = formatNumber((total - (total * totalDiscount)).toFixed(2),2);
	discountTotal.innerHTML = "-"+formatNumber(totalDiscountedAmount.toFixed(2),2);
	netTotal.innerHTML = formatNumber(totalNet.toFixed(2),2);
//	alert("refreshTotal : subTotal.innerHTML='"+subTotal.innerHTML+"' \nnetTotal.innerHTML='"+netTotal.innerHTML+"'");
	
	
	
}

function preset(iscash){
//	alert("preset : iscash = '"+iscash+"' \nF('ordername') = '"+$F('ordername')+"'");
//	$('ordername').readOnly=!iscash;
//	$('orderaddress').readOnly=!iscash;
	if ($F('ordername')){
		$('clear-enc').disabled = false;
	}else{
		$('clear-enc').disabled = true;
	}
	if ($F('view_from')=='ssview'){
		$('show-discount').readOnly=false;
		$('btndiscount').style.display='';
	}else{
		$('show-discount').readOnly=true;
	}
	
	//----------------------------------
	
	if (($F('hasPaid')==1)||($F('view_from')=='ssview')){
		$('iscash0').disabled=true;
		$('iscash1').disabled=true;
		$('ordername').readOnly=true;
		$('orderaddress').readOnly=true;
		$('ordername').readOnly=true;
		$('request_date').readOnly=true;
		$('priority0').disabled = true;	
		$('priority1').disabled = true;	
		$('comments').readOnly=true;

		$('select-enc').setAttribute("onclick","");
		$('select-enc').setAttribute("class","disabled");
		$('select-enc').style.cursor='default';

		$('clear-enc').disabled = true;	
		$('clear-enc').style.cursor='default';

		$('btnAdd').setAttribute("onclick","");
		$('btnAdd').setAttribute("class","disabled");
		$('btnAdd').style.cursor='default';
		
		$('btnEmpty').setAttribute("onclick","");
		$('btnEmpty').setAttribute("class","disabled");
		$('btnEmpty').style.cursor='default';

		$('btndiscount').disabled = true;	

		$('btnSubmit').setAttribute("onclick","");
		$('btnSubmit').setAttribute("class","disabled");
		$('btnSubmit').style.cursor='default';
		
		document.getElementsByName('btnRefreshDiscount').disabled = true;
		document.getElementsByName('btnRefreshTotal').disabled = true;
	}
}

/*
		burn added : September 13, 2007
*/
function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
}

function initialRequestList(serv_code,grp_code,name,c_info,r_doc,r_doc_name,n_house,cash,charge,hasPaid,sservice) {
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
		details.hasPaid = hasPaid;
		details.sservice = sservice;
		//details.discount_name= $('discount_name'+id).value;
		var list = document.getElementById('order-list');
		var msg = "requestDoc='"+details.requestDoc+"'\ndetails.is_in_house='"+details.is_in_house+
					 "'\ndetails.qty='"+details.qty+"\nserv_code='"+serv_code+"'\ndetails.id='"+details.id+
					 "'\ndetails.idGrp='"+details.idGrp+"'\ndetails.name='"+details.name+
					 "'\ndetails.prcCash='"+details.prcCash+"'\ndetails.prcCharge='"+details.prcCharge+
					 "'\ndetails.hasPaid='"+details.hasPaid+"'\ndetails.sservice='"+details.sservice+"'\n";
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
		$('inputform').submit();
		return true;
	}

