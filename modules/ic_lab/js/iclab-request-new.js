var totalDiscount = 0, totalDiscountedAmount=0, totalNet=0, totalNONSocializedAmount=0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function clearEncounter() {
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	$('orderaddress').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('discount').value = '';
	$('discountid').value = '';

	$('refno').value = '';
	$('mode').value="save";

	$('btndiscount').disabled = false;
	$('sw-class').innerHTML = '';
	$('patient_enctype').innerHTML = '';
	$('patient_location').innerHTML = '';
	$('patient_medico_legal').innerHTML = '';

	$('current_att_dr_nr').value = '';
	$('current_dept_nr').value = '';
	$('impression').value = '';
	$('hrn').innerHTML = '';
	$('dob').innerHTML = '';
	$('sex').innerHTML = '';
	$('age').innerHTML = '';
	$('is_walkin').checked = false;

	if (iscash==true)
		$('is_cash').value = 1;
	else
		$('is_cash').value = 0;

	$('btnHistory').style.display = "none";
	$('ptype').value = "";
	$('orig_discountid').value = "";
	$('discount2').value = "";

	$('ic_row').style.display = "none";
	$('is_charge2comp').checked = false;
	$('compName').value = "";
	$('compID').value = "";
	//$('source_req').value = "";
}

function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
}

function emptyTray() {
	var items = document.getElementsByName('items[]');
	var id;

	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));
		$('rowPrcNet'+id).parentNode.removeChild($('rowPrcNet'+id));
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		$('sservice'+id).parentNode.removeChild($('sservice'+id));
	}

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

			var totalNetCash;
			var src, toolTipText;
			var btnicon;
			var paidcnt = 0;
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			var ptype = $('ptype').value;

			nf.setPlaces(2);
			//alert('details = '+details);
			if (details) {
				var id = details.id,
					idGrp = details.idGrp,
					qty = parseFloat(details.qty),
					prcCash = parseFloat(details.prcCash),
					prcCharge = parseFloat(details.prcCharge);
					net_price = parseFloat(details.net_price);

					totalNetCash = net_price*qty;
					//alert('1 totalNetCash = '+totalNetCash);
					alt = (dRows.length%2)+1;
					nf.setNumber(qty);
					nf.setPlaces(nf.NO_ROUNDING);
					qty = isNaN(qty) ? '0' : ''+nf.toFormatted();

					nf.setPlaces(2);
					nf.setNumber(prcCash);
					prcCash = isNaN(prcCash) ? 'N/A' : nf.toFormatted();
					nf.setNumber(prcCharge);
					prcCharge = isNaN(prcCharge) ? 'N/A' : nf.toFormatted();

					nf.setNumber(totalNetCash);
					totalNetCash = isNaN(totalNetCash) ? 'N/A' : nf.toFormatted();

				if (isCash) {
					prc=prcCash;
				}
				else {
					prc=prcCharge;
				}

				tot = totalNetCash;
				//alert('js= '+tot);
				//var person_discountid = $("discountid").value;

				toolTipText = "Requesting doctor: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.requestDocName+" <br>"+
									"Clinical Impression: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.clinicInfo;


				if (items) {
					for (var i=0;i<items.length;i++) {
						if (items[i].value == details.id) {
							$('toolTipText'+id).value = toolTipText;
							$('rowPrcCash'+id).value = details.prcCash;
							$('rowPrcCharge'+id).value = details.prcCharge;
							$('rowPrcNet'+id).value = details.net_price;
							$('rowDoc'+id).value = details.requestDoc;
							$('rowDocName'+id).value = details.requestDocName;
							$('rowDept'+id).value = details.requestDept;
							$('rowHouse'+id).value = details.is_in_house;
							$('rowInfo'+id).value = details.clinicInfo;
							$('rowQty'+id).value = details.qty;
							document.getElementById('idGrp'+id).innerHTML = id;
							document.getElementById('name'+id).innerHTML = details.name;
							document.getElementById('prc'+id).innerHTML = prc;
							document.getElementById('tot'+id).innerHTML = tot;
							//alert('update = '+tot);
							var name_serv = details.name;
							alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (items.length == 0)
						clearOrder(list);
				}

			delitemImg = '<a href="javascript: nd(); removeItem(\''+id+'\');">'+
							 '	<img src="../../images/btn_delitem.gif" border="0"/></a>';
			paiditemImg = '<img src="../../images/btn_paiditem.gif" border="0" onClick="">';
			unpaiditemImg = '<img src="../../images/btn_unpaiditem.gif" border="0" onClick="">';

			charityImg = '<img src="../../images/btn_charity.gif" border="0" onClick="">';
			cmapImg = '<img src="../../images/btn_cmap.gif" border="0" onClick="">';
			lingapImg = '<img src="../../images/btn_lingap.gif" border="0" onClick="">';
			missionImg = '<img src="../../images/btn_mission.gif" border="0" onClick="">';

			// added by VAN 01-15-08
			repeatitemImg = '<img src="../../images/btn_repeat.gif" border="0" onClick="">';

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

			if (view_mode==1)
				btnicon = ((details.hasPaid==1)?paiditemImg:unpaiditemImg);
			else{
				 if ((($('parent_refno').value!=null)||($('parent_refno').value!=""))&&($('repeat').checked)){
						btnicon = repeatitemImg;
				 }else if ($('is_cash').value==1){
						if (($('hasPaid').value==1)||(details.hasPaid)){
							if (details.pay_type!=""){
								if (details.pay_type=='paid')
									 btnicon = paiditemImg;
								else if ((details.pay_type=='lingap') || (details.pay_type=='cmap')
													|| (details.pay_type=='mission') || (details.pay_type=='charity'))
									btnicon = '<img src="../../images/btn_'+details.pay_type+'.gif" border="0" onClick="">';
								else
									btnicon = delitemImg;

								paidcnt =+ 1;
								disabled = "";
							}else{
									if (paidcnt>=1)
										btnicon = unpaiditemImg;
									else
										btnicon = delitemImg;
									disabled = "disabled";
							}

						}else{
								btnicon = delitemImg;
								disabled = "disabled";
						}
				}else{
					if ($('grant_type').value!=""){
						if ($('mode').value=='update'){
							//btnicon = '<img src="../../images/btn_'+$('grant_type').value+'.gif" border="0" onClick="">';
							if ((details.pay_type=='lingap') || (details.pay_type=='cmap')
													|| (details.pay_type=='mission') || (details.pay_type=='charity'))
									btnicon = '<img src="../../images/btn_'+details.pay_type+'.gif" border="0" onClick="">';
							else
									btnicon = delitemImg;

							disabled = "";
						}else{
							btnicon = delitemImg;
							disabled = "disabled";
						}
					}else{
						btnicon = delitemImg;
						disabled = "";
					}
				}
			}

			src =
					'<tr class="wardlistrow'+alt+'" id="row'+id+'"> '+
					'<input type="hidden" name="toolTipText'+id+'" id="toolTipText'+id+'" value="'+toolTipText+'" />'+
					'<input type="hidden" name="sservice[]" id="sservice'+id+'" value="'+details.sservice+'" />'+
					'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.prcCash+'" />'+
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.prcCharge+'" />'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden" name="requestDoc[]" id="rowDoc'+id+'" value="'+details.requestDoc+'" />'+
					'<input type="hidden" name="requestDept[]" id="rowDept'+id+'" value="'+details.requestDept+'" />'+
					'<input type="hidden" name="requestDocName[]" id="rowDocName'+id+'" value="'+details.requestDocName+'" />'+
					'<input type="hidden" name="isInHouse[]" id="rowHouse'+id+'" value="'+details.is_in_house+'" />'+
					'<input type="hidden" name="clinicInfo[]" id="rowInfo'+id+'" value="'+details.clinicInfo+'" />'+
					'<input type="hidden" name="pnet[]" id="rowPrcNet'+id+'" value="'+details.net_price+'" />'+
					'<input type="hidden" name="pnetbc[]" id="rowPrcNetbc'+id+'" value="'+details.net_price+'" />'+
					'<input type="hidden"  name="qty[]" id="rowQty'+id+'" itemID="'+id+'" value="'+details.qty+'">'+
					'<td class="centerAlign">'+
					btnicon
					+'</td>'+
					'<td align="centerAlign">'+nonSocialized+'</td>'+
					'<td id="idGrp'+id+'"'+toolTipTextHandler+'>'+id+'</td>'+
					'<td id="name'+id+'"'+toolTipTextHandler+'>'+details.name+'</td>'+
					'<td class="rightAlign" id="prc'+id+'">'+prc+'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
					'</tr>';
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"8\">Request list is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			document.getElementById('counter').innerHTML = items.length;
			return true;
		}
	}
	return false;
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
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

	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		emptyIntialRequestList();
	}

	document.getElementById('counter').innerHTML = items.length;
	showSocialNotes();
	refreshDiscount();
}

function changeTransactionType() {
	var iscash = $("iscash1").checked;
	var prcList, id, total=0;
	var pid = $('pid').value;
	var encounter_nr = $('encounter_nr').value;
	//clearEncounter();

	if ((pid)&&(!encounter_nr)&&(!iscash)){
		alert('Charging is only allowed for current hospital patients...');
		$("iscash1").checked = true;
		iscash = true;
	}else{
		if (iscash){
			$('sw-class').innerHTML = $F('discountid');
			prcList = document.getElementsByName("pcash[]");
			$('is_walkin').checked = false;
			$('is_walkin').disabled = false;
		}else{
			$('sw-class').innerHTML = 'None';
			prcList = document.getElementsByName("pcharge[]");
			$('is_walkin').checked = false;
			$('is_walkin').disabled = true;
		}

		if (iscash==true){
			$('is_cash').value = 1;
			//$('type_charge').style.display='none';
		}else{
			$('is_cash').value = 0;
			//$('type_charge').style.display='';
		}
		$('type_charge').style.display='';

		for (var i=0;i<prcList.length;i++) {
			if (iscash)
				id = prcList[i].id.substring(10);
			else
				id = prcList[i].id.substring(12);
			$('prc'+id).innerHTML = formatNumber(prcList[i].value,2);
			$('tot'+id).innerHTML = formatNumber(parseFloat($('rowQty'+id).value)*parseFloat(prcList[i].value),2);
		}

		refreshDiscount();
	}
}

function refreshDiscount() {
	//var nodes = $("discount");
	totalDiscount = 0;
	totalNet = 0;
	totalDiscountedAmount = 0;
	totalNONSocializedAmount = 0;


	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var net = document.getElementsByName('pnet[]');

	var sservice = document.getElementsByName('sservice[]');
	var prcCash, prcCharge, prcNet, id, isCash = $("iscash1").checked;
	var qty = document.getElementsByName('qty[]');
	//var person_discountid = $("discountid").value;

	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		prcCash = parseFloat(cash[i].value);
		//totalCash = prcCash*parseFloat(qty[i].value);
		prcCharge = parseFloat(charge[i].value);
		//totalCharge = prcCharge*parseFloat(qty[i].value);
		prcNet = parseFloat(net[i].value);

		if (isCash)
			totalPrice = prcCash*parseFloat(qty[i].value);
		else
			totalPrice = prcCharge*parseFloat(qty[i].value);

		totalNet = prcNet*parseFloat(qty[i].value);
		totalDiscount = totalPrice - totalNet;
		totalDiscountedAmount += totalDiscount;
	}
	//alert('totalDiscountedAmount = '+totalDiscountedAmount);
	refreshTotal();
}

function refreshTotal() {
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();
	var NetTotal = 0;

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

		//var dAdjAmount = $("show-discount");
		NetTotal =  total - totalDiscountedAmount;

		subTotal.innerHTML = formatNumber(total.toFixed(2),2);
		discountTotal.innerHTML = "-"+formatNumber(totalDiscountedAmount.toFixed(2),2);
		netTotal.innerHTML = formatNumber(NetTotal.toFixed(2),2);
}

Array.prototype.max = function() {
var max = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] > max) max = this[i];
return max;
}

Array.prototype.min = function() {
var min = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] < min) min = this[i];
return min;
}

function preset(iscash){
	//var view_from = window.parent.$('view_from');
	var ptype = $('ptype').value;
	//if (view_from)
		//$('view_from').value =  view_from.value;
	//alert($F('view_from'));

	$("iscash1").focus();

	if ($('discountid').value=='SC')
		$('issc').checked = true;

	if (($('view_from').value=='ssview') || ($('view_from').value=='override'))
		$('btndiscount').style.display = "";
	else
		$('btndiscount').style.display = "none";

	iscash = $("iscash1").checked;

	if (iscash==true){
		$('is_cash').value = 1;
		//$('type_charge').style.display='none';
	}else{
		$('is_cash').value = 0;
		//$('type_charge').style.display='';
	}
	$('type_charge').style.display='';

	if ($F('ordername')){
		$('clear-enc').disabled = false;
		$('btnHistory').style.display = "";
		//$('btnOther').style.display = "";
	}else{
		$('clear-enc').disabled = true;
		$('btnHistory').style.display = "none";
		//$('btnOther').style.display = "none";
	}

	if (($F('view_from')=='ssview')||($F('view_from')=='override')){
		//$('show-discount').readOnly=false;
		$('btndiscount').style.display='';

		/*$('btnSubmit').setAttribute("onclick","");
		$('btnSubmit').setAttribute("class","disabled");
		$('btnSubmit').style.cursor='default';*/

		if ($F('view_from')=='override')
			$('override_row').style.display = "";
		else
			$('override_row').style.display = "none";
	}else{
		//$('show-discount').readOnly=true;
		$('override_row').style.display = "none";
	}

	if (($F('hasPaid')==1)||($('repeat').checked)||($F('view_from')=='ssview')||($F('view_from')=='override')){
		$('ordername').readOnly=true;
		$('orderaddress').readOnly=true;
		$('ordername').readOnly=true;

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

		//$('btnCancel').setAttribute("onclick","");
		//$('btnCancel').setAttribute("class","disabled");
		//$('btnCancel').style.cursor='default';
		$('btnSubmit').setAttribute("onclick","");
		$('btnSubmit').setAttribute("class","disabled");
		$('btnSubmit').style.cursor='default';

		$('iscash0').disabled=true;
		$('iscash1').disabled=true;

		$('priority0').disabled = true;
		$('priority1').disabled = true;
		$('comments').readOnly=true;

		document.getElementsByName('btnRefreshDiscount').disabled = true;
		document.getElementsByName('btnRefreshTotal').disabled = true;
	}

	var refno = document.getElementById('refno').value;
	var area_type = $('area_type').value;

	if ($('mode').value=='save'){
		// er patient and payward patient
		if ((area_type=='pw')||(ptype==1)||($('is_charge2comp').checked)||($('source_req').value=='RDU')){
			$("iscash1").checked = false;
			$("iscash0").checked = true;
		}else if (area_type=='ch'){
			$("iscash1").checked = true;
			$("iscash0").checked = false;
		}else{
			$("iscash1").checked = true;
			$("iscash0").checked = false;
		}
	}

	//for Industrial Clinic
	if ($('source_req').value=='IC'){
		 if ($('is_charge2comp').checked){
				$('ic_row').style.display = '';
		 }else
				$('ic_row').style.display = 'none';
	}else{
		 $('ic_row').style.display = 'none';
	}

	CheckRepeatInfo();
}

function CheckRepeatInfo(id){
		var isrepeat = '<?=$_GET["repeat"];?>';
		if (($('repeat').checked)||(isrepeat==1)){
			$('grant_type').value="";
			document.getElementById('repeatinfo').style.display = '';
		}else	{
			document.getElementById('repeat').disabled = true;
			document.getElementById('repeatinfo').style.display = 'none';
		}
	}

function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
}

function initialRequestList(serv_code,grp_code,name,c_info,r_doc,r_doc_name,n_house,cash,charge,hasPaid,sservice,head,remarks,qty,discounted_price,doc_dept,pay_type) {
	var details = new Object();
	var withpaid = 0;
	var isrepeat = '<?=$_GET["repeat"];?>';

		details.requestDoc= r_doc;
		details.requestDocName= r_doc_name;
		details.is_in_house= n_house;
		details.clinicInfo= c_info;
		details.idGrp = grp_code;
		details.id = serv_code;
		details.qty = qty;
		details.name = name;

		details.prcCash = cash;
		details.prcCharge= charge;
		details.hasPaid = hasPaid;
		details.sservice = sservice;
		details.discounted_price = discounted_price;

		details.requestDept = doc_dept;
		details.pay_type = pay_type;
		details.net_price = discounted_price;
		details.pay_type = pay_type;
		//alert(details.pay_type);

		details.head = head;
		details.remarks = remarks;

		if (($('repeat').checked)||(isrepeat==1)){
			details.discounted_price = 0;
			details.net_price = 0;
		}

		var list = document.getElementById('order-list');

		result = appendOrder(list,details);
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
	}else if($F('orderdate') == ''){
		alert("Please indicate the date of request.");
		$('orderdate').focus();
		return false;
	}else if (($('priority1').checked)&&(!$('priority0').checked)&&($('comments').value=='')){
		alert("Enter a remarks why the request should be a stat case or urgent.");
		$('comments').focus();
		return false;
	}

	if ($('repeat').checked){
		if ($('remarks').value=='') {
			alert("Enter a remarks why the request should be repeated.");
			$('remarks').focus();
			return false;
		}else if ($('approved_by_head').value=='') {
			alert("Enter a name who approved .");
			$('approved_by_head').focus();
			return false;
		}else if ($('headID').value=='') {
			alert("Enter a user ID who approved .");
			$('headID').focus();
			return false;
		}else if ($('headpasswd').value=='') {
			alert("Enter a password who approved .");
			$('headpasswd').focus();
			return false;
		}
	}

	$('inputform').submit();
	return true;
}

function warnClear() {
	var items = document.getElementsByName('items[]');
	if (items.length == 0) return true;
	else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function viewClaimStub(is_cash,refno){
		window.open("seg-claimstub.php?refno="+refno+"&is_cash="+is_cash+"&showBrowser=1","viewClaimStab","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");

}

//added by VAN 10-16-09
function viewHistory(pid,encounter_nr){
	window.open("seg-iclab-request-history.php?pid="+pid+"&encounter_nr="+encounter_nr+"&ref_source=IC&showBrowser=1","viewRequestHistory","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

//added by VAN 01-11-10
//only digit is allowed
function key_check(e, value){
	if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
		return true;
	}else
		return false;
}

function checkIfWalkin(){
	if (warnClear()) {
		emptyTray();
		if ($('is_walkin').checked){
			$('discount2').value = $('discount').value;
			$('orig_discountid').value = $('discountid').value;
			$('discountid').value = '';

			if ($('issc').checked){
				$('discountid').value = 'SC';
				//should be taken from the database temporary only
				$('discount').value = 0.20;
			}else
				$('discount').value = 0;
			refreshDiscount();
		}else{
			$('discount').value = $('discount2').value;
			$('discountid').value = $('orig_discountid').value;
		}
	}
}

function resetValue(){
	var items = document.getElementsByName('items[]');
	var net = document.getElementsByName('pnet[]');
	var netbc = document.getElementsByName('pnetbc[]');

	nettotal = 0;
	$('show-discount').value = formatNumber(nettotal,2);
	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		net[i].value = netbc[i].value;
		amount = $('rowPrcNetbc'+id).value;
		$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
	}
	refreshDiscount();
}

function clearValue(){
	$('show-discount').value= "";
	$('is_free').checked = false;

	resetValue();
}

function computeDiscount(discount_amt){
	 var items = document.getElementsByName('items[]');
	 var net = document.getElementsByName('pnet[]');
	 var netbc = document.getElementsByName('pnetbc[]');
	 var val, nettotal, price_per_service, final_net, netamt, pricelist, discount_given, amount;
	 var no_item=0;

	 discount_given = discount_amt.replace(",","");
	 discount_given = parseFloat(discount_given);
	 nettotal = $('show-net-total').innerHTML;
	 nettotal = parseFloat(nettotal);

	 if (items){
		 pricelist = new Array();
		 netamt = 0;
		 for (var i=0;i<items.length;i++) {
				pricelist[i] = parseFloat(netbc[i].value);

				if (netbc[i].value > 0){
					no_item += 1;
					netamt = netamt + parseFloat(netbc[i].value);
				}
		 }

		 netamt = parseFloat(netamt);

		 if (discount_given > netamt){
				 alert('The discount given is MORE than the Net Total (OR payable amount)');
				 for (var i=0;i<items.length;i++) {
						id = items[i].value;
						net[i].value = netbc[i].value;
						amount = $('rowPrcNetbc'+id).value;
						$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
				 }
				 $('show-discount').value = '0.00';
				 $('show-discount').focus();
				 refreshDiscount();
		 }else{
				 if (discount_given > 0){
						 price_per_service =  (netamt - discount_given/parseInt(no_item));

						 if (price_per_service > pricelist.min()){
								final_net = (netamt- discount_given) / netamt;
								withdis = 1;
						 }else{
								withdis = 0;
						 }

						 for (var i=0;i<items.length;i++) {
								 id = items[i].value;

								 if (withdis==0){
										price_per_service = Math.round(parseFloat(price_per_service)*100)/100;
										price_per_service = formatNumber(price_per_service.toFixed(2),2);
										net[i].value = price_per_service.replace(",","");
										$('tot'+id).innerHTML = price_per_service
								 }else{
										discountprice = netbc[i].value * final_net;
										discountprice = Math.round(parseFloat(discountprice)*100)/100;
										discountprice = formatNumber(discountprice.toFixed(2),2);
										net[i].value = discountprice.replace(",","");
										$('tot'+id).innerHTML = discountprice;
								 }
						 }
						 refreshDiscount();
				 }
		 }
	 }

	 //refreshDiscount();
}

function formatDiscount(valamount){
		document.getElementById('show-discount').value = formatNumber(valamount,2);
}

function setDiscount(){
	var nettotal=0;
	var items = document.getElementsByName('items[]');
	var net = document.getElementsByName('pnet[]');
	var netbc = document.getElementsByName('pnetbc[]');

	if ($('is_free').checked){
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			nettotal = nettotal + parseFloat(netbc[i].value);
		}

		$('show-discount').value = nettotal;
		computeDiscount($('show-discount').value);
		formatDiscount($('show-discount').value);
	}else{
		nettotal = 0;
		$('show-discount').value = formatNumber(nettotal,2);
		resetValue();
	}
}

