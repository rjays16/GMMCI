function init() {
	var encounter_nr = $('encounter_nr').value;
	var handleSubmit = function() {
		this.submit();
		this.cancel();
	};
	var handleCancel = function() {
		this.cancel();
	};

	//YAHOO.util.Event.addListener("show", "click",onClickHandlerButton);

	DOM_init();
}// end of function init ()


function keyPressHandler(event){
	var key = YAHOO.util.Event.getCharCode(event);

	if(key > 31 && (key <48 || key > 57)){
		Event.stop(event);
	}

	return true;
}


	/*	Use to close the pop-up dialog
	 *	burn added: October 12, 2007
	 */
function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
	window.location.reload();
}

//added by VAN 06-25-08
function refreshWindow(){
	rlst.reload();
	//window.location.href=window.location.href;
}

function js_showDetails(refno, dept){
	var sid, rpath, param ='';

	sid = $('sid').value;
	rpath = $('root_path').value;

	//added by VAN 06-24-08
	var discountid;
	var encounter_nr = $('encounter_nr').value;
	var pid = $('pidNr').value;


	if ($('discountId').value)
		discountid = $('discountId').value;
	else
		discountid = $('discountId2').value;

	//alert('discountid = '+discountid);
	//alert(discountid);
	switch(dept){
		case 'LB':
			return overlib(OLiframeContent(rpath+'modules/laboratory/seg-lab-request-new.php'+sid+'&user_origin=lab&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT,
						'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Laboratory Request',
						MIDX, 0, MIDY, 0, STATUS,'Laboratory Request');

		break;

		case 'BB':
						return overlib(OLiframeContent(rpath+'modules/bloodBank/seg-blood-request-new.php'+sid+'&local_user=ck_radio_user&user_origin=blood&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
												WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
												'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Blood Bank Request',
												MIDX, 0, MIDY, 0, STATUS,'Blood Bank Request');

						break;

		case 'SPL':
						return overlib(OLiframeContent(rpath+'modules/special_lab/seg-splab-request-new.php'+sid+'&local_user=ck_radio_user&user_origin=splab&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
												WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
												'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Special Lab Request',
												MIDX, 0, MIDY, 0, STATUS,'Special Lab Request');

						break;
		
		case 'IC':
						return overlib(OLiframeContent(rpath+'modules/ic_lab/seg-iclab-request-new.php'+sid+'&local_user=ck_radio_user&user_origin=iclab&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
												WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
												'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Industrial Clinic Lab Request',
												MIDX, 0, MIDY, 0, STATUS,'Industrial Clinic Lab Request');

						break;

		case 'RD':
			return overlib(OLiframeContent(rpath+'modules/radiology/seg-radio-request-new.php'+sid+'&local_user=ck_radio_user&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
						'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Radiology Request',
						MIDX, 0, MIDY, 0, STATUS,'Radiology Request');
		break;
		case 'P':
			return overlib(OLiframeContent(rpath+'modules/pharmacy/seg-pharma-order.php'+sid+'&target=edit&ref='+refno+'&viewonly=1&view_from=override&from=CLOSE_WINDOW', 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT,
						'<img src=../../images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Pharmacy Request',
						MIDX, 0, MIDY, 0, STATUS,'Pharmacy Request');
		break;
	}

}

function js_addDefaultRow(tableId){
	var dTable=$(tableId);
	if(dTable){
		rowSrc = '<tr><td style="" colspan="6">No requests available at this time.</td></tr>';
		document.getElementById('rqlisttbody').innerHTML = rowSrc;
	}
}

//added by VAN 05-08-08
function formatValue(num,dec){
	var nf = new NumberFormat(num.value);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	num.value = nf.toFormatted();
}

function formatNumber(num,dec){
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function js_showDetails(refno, dept){
	var sid, rpath, param ='';

	sid = $('sid').value;
	rpath = $('root_path').value;

	var discountid = $('discountid').value;
	var encounter_nr = $('encounter_nr').value;
	var pid = $('pid').value;

	switch(dept){
		case 'LB':
			return overlib(OLiframeContent(rpath+'modules/laboratory/seg-lab-request-new.php'+sid+'&user_origin=lab&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT,
						'<img src='+rpath+'images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Laboratory Request',
						MIDX, 0, MIDY, 0, STATUS,'Laboratory Request');

		break;

		case 'BB':
						return overlib(OLiframeContent(rpath+'modules/bloodBank/seg-blood-request-new.php'+sid+'&local_user=ck_radio_user&user_origin=blood&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
												WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
												'<img src='+rpath+'images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Blood Bank Request',
												MIDX, 0, MIDY, 0, STATUS,'Blood Bank Request');

						break;

		case 'SPL':
						return overlib(OLiframeContent(rpath+'modules/special_lab/seg-splab-request-new.php'+sid+'&local_user=ck_radio_user&user_origin=splab&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
												WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
												'<img src='+rpath+'images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Special Lab Request',
												MIDX, 0, MIDY, 0, STATUS,'Special Lab  Request');

						break;
		
		case 'IC':
						return overlib(OLiframeContent(rpath+'modules/ic_lab/seg-iclab-request-new.php'+sid+'&local_user=ck_radio_user&user_origin=iclab&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
												WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
												'<img src='+rpath+'images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Industrial Clinic Lab Request',
												MIDX, 0, MIDY, 0, STATUS,'Industrial Clinic Lab  Request');

						break;

		case 'RD':
			return overlib(OLiframeContent(rpath+'modules/radiology/seg-radio-request-new.php'+sid+'&local_user=ck_radio_user&encounter_nr='+encounter_nr+'&pid='+pid+'&popUp=1&refno='+refno+'&view_from=override&discountid='+discountid, 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
						'<img src='+rpath+'images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Radiology Request',
						MIDX, 0, MIDY, 0, STATUS,'Radiology Request');
		break;
		case 'P':
			return overlib(OLiframeContent(rpath+'modules/pharmacy/seg-pharma-order.php'+sid+'&target=edit&ref='+refno+'&viewonly=1&view_from=override&from=CLOSE_WINDOW', 820,400, 'frad-request', 0, 'auto'),
						WIDTH , 820, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL , CLOSETEXT,
						'<img src='+rpath+'images/close_red.gif border=0 onClick=refreshWindow();>', CAPTION, 'Pharmacy Request',
						MIDX, 0, MIDY, 0, STATUS,'Pharmacy Request');
		break;
	}

}

function js_addDefaultRow(tableId){
	var dTable=$(tableId);
	if(dTable){
		rowSrc = '<tr><td style="" colspan="6">No requests available at this time.</td></tr>';
		document.getElementById('rqlisttbody').innerHTML = rowSrc;
	}
}


function js_addRow_Request(details) {
	list = $("rlst");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null,
					id = details["ref_no"];
					dRows = dBody.getElementsByTagName("tr");
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1
				inputbtn ='<button class="jedInput" id="btn'+id+'" onclick="javascript:void(0);js_showDetails(\''+details['ref_no']+'\', \''+details['dept']+'\');" style="color:#000060">Apply</button>';
				switch(details["dept"]){
					case 'LB':
						dept = 'LAB';
					break;

					case 'BB':
						dept = 'BLOOD';
						break;

					case 'SPL':
						dept = 'SPLAB';
						break;
					
					case 'IC':
						dept = 'ICLAB';
						break;

					case 'RD':
						dept = 'RADIO';
						break;
					case 'P':
						dept = 'PHARMA';
						break;
				}
				src =
				'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +

					'<td class="centerAlign" style="color:#660000">'+details["ref_no"]+'</td>'+
					'<td class="centerAlign">'+details["request_date"]+'</td>'+
					'<td class="centerAlign">'+dept+'</td>'+
					'<td class="rightAlign">'+details["total_charge"]+'</td>'+
					'<td class="centerAlign">'+inputbtn+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"8\">List is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}


