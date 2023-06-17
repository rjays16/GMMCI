var totalDiscount = 0, totalDiscountedAmount=0, totalNet=0, totalNONSocializedAmount=0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
}


function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
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
		var popup = $('popUp').value;
		var isERIP = $('isERIP').value;
		var ptype = $('ptype').value;

		$('ordername').readOnly=true;
		$('orderaddress').readOnly=true;
		$('ordername').readOnly=true;

		$('select-enc').setAttribute("onclick","");
		$('select-enc').setAttribute("class","disabled");
		$('select-enc').style.cursor='default';

		$('clear-enc').disabled = true;
		$('clear-enc').style.cursor='default';


		$('iscash0').disabled=true;
		$('iscash1').disabled=true;

		$('comments').readOnly=true;

		$('is_rdu').disabled=true;
		$('is_walkin').disabled=true;

		$('priority1').disabled=true;
		$('priority0').disabled=true;
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


function initialRequestList(serv_code,grp_code,name,c_info,r_doc,r_doc_name,n_house,cash,
														charge,hasPaid,sservice,head,remarks,qty,discounted_price,doc_dept,pay_type,
														is_forward, is_monitor, every_hour, in_lis, oservice_code, is_cash, is_served,
														is_posted_lis, is_serial) {

	var details = new Object();
	var withpaid = 0;

		details.requestDoc= r_doc;
		details.requestDocName= r_doc_name;
		details.is_in_house= n_house;
		details.clinicInfo= c_info;
		details.idGrp = grp_code;
		details.id = serv_code;
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

		details.remarks = remarks;

		details.is_forward = is_forward;
		details.is_monitor = is_monitor;
		details.every_hour = every_hour;

		details.in_lis = in_lis;
		details.oservice_code = oservice_code;
		details.is_cash = is_cash;
		details.is_served = is_served;
		details.is_posted_lis = is_posted_lis;
		details.is_serial = is_serial;

		var list = document.getElementById('order-list');

		result = appendOrder(list,details);

}

function post_request(service_code, is_serial){
	var refno = $('refno').value;
	var encounter_nr = $('encounter_nr').value;
	var pid = $('pid').value;

	//if (is_serial==1){
		return overlib(
					OLiframeContent("seg-lab-post-request.php?user_origin=lab&popUp=1&viewonly=1&refno="+refno+"&pid="+pid+"&encounter_nr="+encounter_nr+"&service_code="+service_code, 800, 400, "fOrderTray", 1, "auto"),
																	WIDTH,400, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, "<img src=../../images/close.gif border=0 >",
																 CAPTIONPADDING,4, CAPTION,"Post Request to LIS",
																 MIDX,0, MIDY,0,
																 STATUS,"Post Request to LIS");
	//}else{
			//alert('temporary for not serial');
	//}
}

function appendOrder(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];

		if (dBody) {
			var lastRowNum = null,
					items = document.getElementsByName('items[]');
					dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			var status,served_stat,ispost,inlis,toolTipText;
			var disabled, clickable;
			var id = details.id;
			var status_img, title;

			var alt = (dRows.length%2)+1;

			if (items) {
				if (items.length == 0)
						clearOrder(list);
			}

			if (details.is_cash==0){
				disabled = "";
				clickable = 1;
			}else{
				if (details.hasPaid==1){
					disabled = "";
					clickable = 1;
				}else{
					disabled = "disabled";
					clickable = 0;
				}
			}

			if (details.is_served==1){
				status_img = 'charity.gif';
				selected = 'selected="selected1"';
				title_stat = 'status is SERVED';
			}else{
				status_img = 'borrowed.gif';
				selected = '';
				title_stat = 'NOT YET served';
			}
			//details.is_posted_lis = 1;
			if (details.is_posted_lis==1){
				//post_img = 'cashier_uncancel.gif';
				//title = 'Already in LIS';
				if (details.is_serial==1){
					post_img = 'cashier_ok.gif';
					title = 'Already in LIS and a SERIAL TEST';
				}else{
					post_img = 'cashier_uncancel.gif';
					title = 'Already in LIS';
				}
			}else{
				//if the service is consist of series of test   
				if (details.is_serial==1){
					post_img = 'cashier_unlock.gif';
					title = 'NOT yet in LIS and a SERIAL TEST';
				}else{
					post_img = 'cashier_edit_3.gif';
					title = 'NOT yet in LIS';
				}
			}

			status = '<select '+disabled+' id="item_status'+id+'" name="status[]" class="segInput" style="font-size: 11px;" onChange="setStatusServ(\''+id+'\',this.value,\''+details.name+'\',\''+details.in_lis+'\',\''+details.oservice_code+'\')">'+
							 '<option value="N" '+selected+'>Not served</option>'+
							 '<option value="S" '+selected+'>Served</option>'+
							 '</select>';

			served_stat = '<img name="served_stat'+id+'" id="served_stat'+id+'" src="../../images/'+status_img+'" border="0" onClick="" title="'+title_stat+'">';

			if (clickable==1){
				post_status = '<img name="ispost'+id+'" id="ispost'+id+'" src="../../images/'+post_img+'" border="0" onClick="post_request(\''+id+'\',\''+details.is_serial+'\');" style="cursor:pointer" title="'+title+'">';
			}else{
				post_status = '<img name="post_stat'+id+'" id="post_stat'+id+'" src="../../images/btn_unpaiditem.gif" border="0" title="Can\'t be served.">';
			}


			if (details.in_lis==1){
				inlis = '<font color="#000066">YES</font>';
			}else{
				inlis = '<font color="#FF0000">NO</font>';
			}

			nf.setPlaces(2);
			//alert('details = '+details);

			if (details) {
			 toolTipText = "Requesting doctor: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.requestDocName+" <br>"+
									"Clinical Impression: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+details.clinicInfo;

				toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'+id+'\').value, CAPTION,\'Details\',  '+
							'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
							'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';

				src =
					'<tr class="wardlistrow'+alt+'" id="row'+id+'"> '+
					'<input type="hidden" name="toolTipText'+id+'" id="toolTipText'+id+'" value="'+toolTipText+'" />'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
					'<input type="hidden"  name="inLIS'+id+'" id="inLIS'+id+'" itemID="'+id+'" value="'+details.in_lis+'">'+
					'<input type="hidden"  name="oservice_code'+id+'" id="oservice_code'+id+'" itemID="'+id+'" value="'+details.oservice_code+'">'+
					'<input type="hidden" name="nameitems'+id+'" id="nameitems'+id+'" value="'+details.name+'" />'+
					'<td '+toolTipTextHandler+' align="centerAlign">'+cnt+'</td>'+
					'<td '+toolTipTextHandler+' align="centerAlign">'+id+'</td>'+
					'<td '+toolTipTextHandler+' id="name'+id+'">'+details.name+'</td>'+
					'<td width="5%" id="net_price-row'+id+'" align="center">'+details.net_price+'</td>'+
					'<td width="5%" id="status-row'+id+'" align="center">'+status+'</td>'+
					'<td width="5%" id="is_forward-row'+id+'" align="center">'+served_stat+'</td>'+
					'<td class="centerAlign" id="prc'+id+'">'+post_status+'</td>'+
					'<td class="centerAlign" id="tot'+id+'">'+inlis+'</td>'+
					'</tr>';
				trayItems++;
				cnt++;
			}
			else {
				src = "<tr><td colspan=\"10\">Request list is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;

			document.getElementById('counter').innerHTML = items.length;

			return true;
		}
	}

	return false;
}

function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
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

function setServeStatus() {
	//var item_array =  new Array();
	var status = document.getElementsByName('status[]');
	var refno = $('refno').value;
	var service_code_list = '';
	var service_code_list2 = '';
	var status_label = 'item_status';
	//var in_lis_label = 'inLIS';
	//var name_service_label = 'nameitems';
	//var opd_service_code_label = 'oservice_code';

	for(i=0;i<status.length;i++){
		str =  status[i].id;
		service_code = str.substr(status_label.length);

		if($('serve_all').checked){
			is_served = 1;
			if (!status[i].disabled){
				status[i].value = "S";
				service_code_list += "'"+service_code+"',";
				service_code_list2 += service_code+",";
			}
		}else{
			is_served = 0;
			if (!status[i].disabled){
				status[i].value = "N";
				service_code_list += "'"+service_code+"',";
				service_code_list2 += service_code+",";
			}
		}

		//item_array[i-1] = Array(refno, status[i].value);
	}
	 //alert(service_code_list);
	xajax_setStatusServAll(refno, service_code_list, service_code_list2, is_served);
}

function setStatusServ(service_code, is_served, service_name, in_lis, oservice_code){
		var refno = $('refno').value;
	 //alert('id = '+id);
	 xajax_setStatusServ(refno, service_code, is_served, service_name, in_lis, oservice_code);
}

function setStatusImage(service_code, is_served){
		if (is_served==1)
			$('served_stat'+service_code).src = "../../images/charity.gif";
		else
			$('served_stat'+service_code).src = "../../images/borrowed.gif";
}

function setStatusImageAll(is_served){
		var status = document.getElementsByName('status[]');

		var status_label = 'item_status';
		//alert('is_served = '+is_served);
		for(i=0;i<status.length;i++){
			str =  status[i].id;
			service_code = str.substr(status_label.length);
			//alert('service_code = '+service_code);
			if (!status[i].disabled){
				if (is_served==1)
						$('served_stat'+service_code).src = "../../images/charity.gif";
				else
						$('served_stat'+service_code).src = "../../images/borrowed.gif";
			}
		}
}

function ResetValue(service_code){
	alert('Error in updating the request status.');
	$('item_status'+service_code).value = "N";
}