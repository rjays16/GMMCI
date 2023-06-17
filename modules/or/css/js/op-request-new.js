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
//	$('ordername').readOnly=!iscash;
	$('orderaddress').value="";
//	$('orderaddress').readOnly=!iscash;
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
			trayops_code = 0;
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
            if ($("iscash1"))  {
			var isCash = $("iscash1").checked;
            }
			var totalCash, totalCharge;
			var src, toolTipText;
			var lastRowNum = null,
					ops_code = document.getElementsByName('ops_code[]');
					dRows = dBody.getElementsByTagName("tr");
			var alt = (dRows.length%2)+1;

			var nf = new NumberFormat();
			nf.setPlaces(2);
			
			if (details) {
				var id = details.id;
/*
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
				toolTipText = "Requesting doctor: <br>   "+details.requestDocName+" <br>"+
									"Clinical Impression: <br>   "+details.clinicInfo;
*/
				if (ops_code) {
					for (var i=0;i<ops_code.length;i++) {
						if (ops_code[i].value == details.id) {
							$('rvu'+id).value = details.rvu;
							$('multiplier'+id).value = details.multiplier;
							//$('ops_charge'+id).value = details.ops_charge;
							document.getElementById('description'+id).innerHTML = details.description;
							document.getElementById('rvu_display'+id).innerHTML = details.rvu;
							document.getElementById('multiplier_display'+id).innerHTML = details.multiplier;
							alert('"'+details.id+'" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (ops_code.length == 0)
	 					clearOrder(list);
				}
//					'<tr class="wardlistrow'+alt+'" id="row'+id+'" title="'+toolTipText+'" '+
//					'		onMouseOver="return overlib(\''+toolTipText+'\', AUTOSTATUS,  TEXTPADDING, 4, FGCOLOR, \'#cceecc\', TEXTFONTCLASS, \'oltxt\', WRAP);" onmouseout="nd();">' +
//					'		onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Details\');" onmouseout="nd();">' +

					src = '<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
								'<td class="center"><a href="javascript:removeItem(\''+id+'\',$(\''+list.id+'\'),\'ops_code[]\')">'+
								'	<img src="../../../images/btn_delitem.gif" border="0"/></a>'+
								'</td>'+
								'<td>&nbsp;</td>'+
								'<td align="left">'+
								'	<span style="font:bold 12px Arial;color:#660000">'+id+'</span>'+
								'	<input name="ops_code[]" id="rowID'+id+'" type="hidden" value="'+id+'">'+
								'</td>'+									
								'<td>'+
								'	<span id="description'+id+'" style="font:bold 12px Arial">'+details.description+'</span><br />'+
								'</td>'+
								'<td>'+
								'	<input name="rvu[]" id="rvu'+id+'" type="hidden" value="'+details.rvu+'">'+
								'	<span id="rvu_display'+id+'">'+details.rvu+'</span></td>'+
								'<td>'+
								'	<input name="multiplier[]" id="multiplier'+id+'" type="hidden" value="'+details.multiplier+'">'+
								'	<span id="multiplier_display'+id+'">'+details.multiplier+'</span></td>'+
								'<td>'+
								'	<input name="ops_charge[]" id="ops_charge'+id+'" type="text" size="10" maxlength="10" '+
								'		onblur="trimString(this); chkDecimal(this,\''+id+'\');" onFocus="this.select();" value="'+formatNumber(details.ops_charge,2)+'">'+
								'</td>'+
							'</tr>';
				//trayops_code++;
			}
			else {
				src = '<tr><td colspan="7" style="">List is currently empty...</td></tr>';
			}
//alert("appendOrder : src : \n"+src);
			dBody.innerHTML += src;
//			alert("appendOrder : $('row"+details.id+"').title = '"+$('row'+details.id).title+"'");
			return true;
		}
	}
	return false;
}


function appendPersonnel(list,pers_type,details) {

	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var src, toolTipText;
			var lastRowNum = null,
					pers_list = document.getElementsByName(pers_type),
					dRows = dBody.getElementsByTagName("tr");
			var alt = (dRows.length%2)+1;
//alert("appendPersonnel :: pers_list.length = '"+pers_list.length+"'");
			if (details) {
				var id = details.id;
				if (pers_list) {
					for (var i=0;i<pers_list.length;i++) {
//alert("appendPersonnel :: pers_list[i].value ='"+pers_list[i].value+"' == details.id ='"+details.id+"'");
						if (pers_list[i].value == details.id) {
//							alert('"'+details.id+'" is already in the list & has been UPDATED!');
							alert('"'+details.name_pers+'" is already in the list!');
							return true;
						}
					}
					if (pers_list.length == 0)
	 					clearOrder(list);
				}
//					'<tr class="wardlistrow'+alt+'" id="row'+id+'" title="'+toolTipText+'" '+
//					'		onMouseOver="return overlib(\''+toolTipText+'\', AUTOSTATUS,  TEXTPADDING, 4, FGCOLOR, \'#cceecc\', TEXTFONTCLASS, \'oltxt\', WRAP);" onmouseout="nd();">' +
//					'		onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Details\');" onmouseout="nd();">' +

					src = '<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
								'<td class="center"><a href="javascript:removeItem(\''+id+'\',$(\''+list.id+'\'),\''+pers_type+'\')">'+
								'	<img src="../../../images/btn_delitem.gif" border="0"/></a>'+
								'</td>'+
								'<td>&nbsp;</td>'+
								'<td align="left">'+
								'	<span style="font:bold 12px Arial;color:#660000">'+details.name_pers+'</span>'+
								'	<input name="'+pers_type+'" id="rowID'+id+'" type="hidden" value="'+id+'">'+
								'</td>'+									
							'</tr>';
			}
			else {
				src = '<tr><td colspan="3" style="">List is currently empty...</td></tr>';
			}
//alert("appendOrder : src : \n"+src);
			dBody.innerHTML += src;
//			alert("appendOrder : $('row"+details.id+"').title = '"+$('row'+details.id).title+"'");
			return true;
		}
	}
	return false;
}

	function chkDecimal(obj,id){
		var objValue = obj.value;
//alert("chkDecimal : \nobj ='"+obj+"' \nobjValue='"+objValue+"' \nid='"+id+"'");
//alert("chkDecimal : \n ('rowID'+id).value = '"+$('rowID'+id).value+"'");

		if (objValue=="")
			return false;
		if ( (isNaN(parseInt(objValue))) || (parseInt(objValue)<0) ){
			alert("Invalid Procedue/Operation charge for "+$F('rowID'+id))
			obj.value="0.00";
			obj.focus();
			return false;
		}
		var nf = new NumberFormat();
		nf.setPlaces(2);
		nf.setNumber(objValue);
//		obj.value = parseFloat(objValue);
		obj.value = nf.toFormatted();
		return true;
	}// end of function chkDecimal


function removeItem(id,list,pers_type) {
    
	var destTable, destRows;
	var table = list;//$('order-list')
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
		//burn added : September 13, 2007
	var ops_code = document.getElementsByName(pers_type);
	if (ops_code.length == 0){
		emptyIntialList(list);
	}
//	refreshTotal();
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
	return;
	
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
	var ops_code = document.getElementsByName('ops_code[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();

	total = 0.0;
	for (var i=0;i<ops_code.length;i++) {
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
function emptyIntialList(list){
//	clearOrder($('order-list'));
//	appendOrder($('order-list'),null);
//alert("emptyIntialList :: list ='"+list+"'");
	clearOrder(list);
	appendOrder(list,null);
}

function emptyIntialListById(list_id){
	emptyIntialList($(list_id));
}

function initialOpsCodeList(ops_code, description, rvu, multiplier, ops_charge) {
	var details = new Object();

		var msg = "ops_code='"+ops_code+
					 "'\nrvu='"+rvu+
					 "'\nmultiplier='"+multiplier+
					 "'\nops_charge='"+ops_charge+
					 "'\ndescription='"+description+"'\n";	
//		alert("initialOpsCodeList : "+msg);

	details.id= ops_code;
	details.description= description;
	details.code= ops_code;
	details.rvu= rvu;
	details.multiplier= multiplier;
	details.ops_charge=ops_charge;
	var msg = "details='"+details+
				 "\ndetails.id='"+details.id+
				 "'\ndetails.description='"+details.description+
				 "'\ndetails.code='"+details.code+
				 "'\ndetails.rvu='"+details.rvu+
				 "'\ndetails.multiplier='"+details.multiplier+"'\n";	
//alert("initialOpsCodeList : "+msg);

		var list = document.getElementById('order-list');
//		alert("initialOpsCodeList 2 : "+msg);

		result = appendOrder(list,details);
//		refreshTotal();	
}

function initialPersonnelList(id, name, list_id, pers_type) {
	var details = new Object();

		var msg = "id='"+id+
					 "'\nname='"+name+
					 "'\nlist_id='"+list_id+
 					 "'\npers_type='"+pers_type+
					 "'\n";	
//		alert("initialPersonnelList :: "+msg);

	details.id= id;
	details.name_pers = name;
		var msg = "details.id='"+details.id+
					 "'\ndetails.name_pers='"+details.name_pers+
 					 "'\nlist_id='"+list_id+
 					 "'\npers_type='"+pers_type+
					 "'\n";	
//alert("initialPersonnelList :: "+msg);

		var list = document.getElementById(list_id);
//		alert("initialPersonnelList :: 2  "+msg);
//		alert("initialPersonnelList :: list = '"+list+"'");

		result = appendPersonnel(list,pers_type,details);
//		refreshTotal();	
}

	/*
	* burn created : December 18, 2007
	*/
function getCurrentListOfPersonnel(pers_type){
	var pers_list = document.getElementsByName(pers_type);
	var list = new Array();
	

	if (pers_list) {
		for (var i=0;i<pers_list.length;i++) {
//alert("getCurrentListOfPersonnel :: pers_list[i].value ='"+pers_list[i].value+"'");
			list.push(pers_list[i].value);
		}
	}
	return list;
}//end of function getCurrentListOfPersonnel

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
         
	var ops_code = document.getElementsByName('ops_code[]');
   
	var surgeon_id = document.getElementsByName('surgeon[]');
	var anesthesiologist_id = document.getElementsByName('anesthesiologist[]');
	
var msg ="surgeon_id ='"+surgeon_id+"' \n"+
			"surgeon_id.length ='"+surgeon_id.length+"' \n"+
			"anesthesiologist_id ='"+anesthesiologist_id+"' \n"+
			"anesthesiologist_id.length ='"+anesthesiologist_id.length+"' \n";
	
	var iscash = $("iscash1").checked;

		if (iscash)
			$('is_cash').value=1;
		else
			$('is_cash').value=0;

//	alert("checkRequestForm : ops_code.length = '"+ops_code.length+"' \n iscash='"+iscash+"'\n$F(encounter_nr)='"+$F('encounter_nr')+"'\n$F(pid)='"+$F('pid')+"'\n$F(is_cash)='"+$F('is_cash')+"'");
//		alert("$F('save') = '"+$F('save')+"'");
/*
		if ($F('save')=='0'){
//			alert("false : $F('save') = '"+$F('save')+"'");
			// if the button clicked is not the for Referral, SAVE or SAVE&DONE buttons
			return false;
		}
*/
//alert("checkRequestForm :: \n"+msg);
		if($F('ordername') == ''){
			alert("Please indicate the patient's name's.");
			if (iscash)
				$('ordername').focus();
			else
				$('select-enc').focus();
			return false;
		}else if($F('request_date') == ''){
			alert("Please indicate the date of request.");
			$('request_date').focus();
			return false;
		}else if($F('op_date') == ''){
			alert("Please indicate the date of operation.");
			$('op_date').focus();
			return false;
		}else if($F('op_time') == ''){
			alert("Please indicate the time of operation.");
			$('time_op').focus();
			return false;
		}else if(surgeon_id.length == 0){
			alert("Please select a surgeon.");
			$('surgeonButton').focus();
			return false;
		}else if(anesthesiologist_id.length == 0){
			alert("Please select an anesthesiologist.");
			$('anesthesiologistButton').focus();
			return false;
		}else if($F('diagnosis') == ''){
			alert("Please type in the diagnosis.");
			$('diagnosis').focus();
			return false;
		}else if($F('op_therapy') == ''){
			alert("Please type in the operation.");
			$('op_therapy').focus();
			return false;
		}else if (ops_code.length==0){
			alert("Please add a procedure/operation code first.");
			$('btnAdd').focus();
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
		return true;
	}
	
	
	var seg_validTime=false;
	function setFormatTime(thisTime,AMPM){
	//	var time = $('time_text_d');
		var stime = thisTime.value;
		var hour, minute;
		var ftime ="";
		var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
		var f2 = /^[0-9]\:[0-5][0-9]$/;
		var jtime = "";
		
		trimString(thisTime,false);
		//alert('thisTime = '+thisTime.value);
	
		if (thisTime.value==''){
			seg_validTime=false;
			return;
		}
		
		stime = stime.replace(':', '');
		
		if (stime.length == 3){
			hour = stime.substring(0,1);
			minute = stime.substring(1,3);
		} else if (stime.length == 4){
			hour = stime.substring(0,2);
			minute = stime.substring(2,4);
		}else{
			alert("Invalid time format.");
			thisTime.value = "";
			seg_validTime=false;
			thisTime.focus();
			return;
		}
		
		//jtime = hour + ":" + minute;
		//js_setTime(jtime);
		
		if (hour==0){
			 hour = 12;
			 $(AMPM).value = "A.M.";		
		}else	if((hour > 12)&&(hour < 24)){
			 hour -= 12;
			 $(AMPM).value = "P.M.";
		}
	
		ftime =  hour + ":" + minute;
		
		if(!ftime.match(f1) && !ftime.match(f2)){
			thisTime.value = "";
			alert("Invalid time format.");
			seg_validTime=false;   
			thisTime.focus();
		}else{
			thisTime.value = ftime;
			seg_validTime=true;   
		}
	}// end of function setFormatTime
	
	/*
	*	input: string, 'operation'
	*	return: string, time in 24-hour format (hh:mm:00)
	*	burn created: November 5, 2007
	*/
function jsFormatTime(id){

	trimString($('time_'+id),false);
	var atime = $F('time_'+id);

	if (atime!=''){
		var colonIndex = atime.indexOf(":");
		var hour = atime.substring(0,colonIndex);
		var minute = atime.substring(colonIndex+1);
		if ($F('selAMPM_'+id)=='P.M.'){
			if (parseInt(hour)<12)
				hour = parseInt(hour)+12;
			atime = hour+":"+minute+":00";
		}else{
			if (hour=="12") //12:?? AM
				hour = "00";
			atime = hour+":"+minute+":00";		
		}
	}
	return atime;
}
