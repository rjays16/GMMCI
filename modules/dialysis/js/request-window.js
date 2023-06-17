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
	 else {
		 return x;
	 }
}

function createTableHeader(divId, tableId, refno, type)
{
	var div = $(divId);
	var tableSrc =
			'<div align="right" style="width:100%;margin-bottom:3px">'+
				'<table width="100%" cellpadding="0" cellspacing="0">'+
					'<tr>'+
						'<td align="right" id="btn-'+refno+'"></td>'+
					'</tr>'+
				'</table>'+
			'</div>'+
			'<table width="100%" class="segList" id="'+tableId+'" cellpadding="0" cellspacing="0">'+
				'<thead>'+
					'<tr class="nav">'+
						'<th colspan="6" style="text-align: left;">Reference no. '+refno+' ('+type+')</th>'+
					'</tr>'+
				'</thead>'+
				'<thead>'+
					'<tr>'+
						'<th align="center">Date</th>'+
						'<th align="center">Status</th>'+
						'<th align="center">Item Description</th>'+
						'<th align="center">Quantity</th>'+
						'<th align="center">Unit Price</th>'+
						'<th align="center">Total</th>'+
					'</tr>'+
				'</thead>'+
				'<tbody>'+
				'</tbody>'+
			'</table><br/>';
	div.insert(tableSrc);
}

function printRequestlist(divId, tableId, details)
{
	var div = $(divId);
	var table = $(tableId);
	var dBody = table.select("tbody")[0];
	/*var table1 = $(table).getElementsByTagName('tbody').item(0);
	if ($('empty_misc_row')) {
		table1.removeChild($('empty_misc_row'));
	}*/
	var dRows = dBody.getElementsByTagName("tr")
	if(details){
		alt = (dRows.length%2>0) ? ' class="alt"':''
		rowSrc =
			'<tr class="'+alt+'" id="ip_row'+details.refno+details.item_code+'">'+
				'<td class="centerAlign" width="14%">'+details.order_date+'</td>'+
				'<td class="centerAlign" width="10%">'+details.status+'</td>'+
				'<td class="leftAlign" width="*">'+details.item_name+'</td>'+
				'<td class="centerAlign" width="8%">'+details.item_qty+'</td>'+
				'<td class="rightAlign" width="8%">'+formatNumber(details.item_prc,2)+'</td>'+
				'<td class="rightAlign" width="8%">'+formatNumber(details.total_prc,2)+'</td>'+
			'</tr>';
		dBody.insert(rowSrc);
	}
}

function openEditRequest(id, refno)
{
	switch(id)
	{
		case 'lab_requests': editLabRequest(refno); break;
		case 'splab_requests': editSpLabRequest(refno); break;
		case 'blood_requests': editBloodRequest(refno); break;
		case 'radio_requests': editRadioRequest(refno); break;
		case 'ip_requests': editPharmaRequest(refno); break;
		case 'mg_requests': editPharmaRequest(refno); break;
		case 'misc_requests': editMiscRequest(refno); break;
	}
}

function openDeleteRequest(id, refno)
{
	 switch(id)
	{
		case 'lab_requests': deleteLabRequest(refno); break;
		case 'splab_requests': deleteSpLabRequest(refno); break;
		case 'blood_requests': deleteBloodRequest(refno); break;
		case 'radio_requests': deleteRadioRequest(refno); break;
		case 'ip_requests': deletePharmaRequest(refno); break;
		case 'mg_requests': deletePharmaRequest(refno); break;
		case 'misc_requests': deleteMiscRequest(refno); break;
	}
}

function editLabRequest(refno)
{
	var pid=$('pid').value;
	return overlib(OLiframeContent('../../modules/laboratory/seg-lab-request-new.php?popUp='+1+'&ref='+refno+'&pid='+pid+'&encounter_nr='+$('encounter_nr').value+'&user_origin=lab&ischecklist=1',
		800, 440, 'flab-list', 1, 'auto'),
	WIDTH, 750, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
	'<img src=../../images/close.gif border=0 onclick="refreshPage();">',
	CAPTIONPADDING, 4, CAPTION, 'Laboratory Request', MIDX, 0, MIDY, 0, STATUS, 'Laboratory Request');
}

function deleteLabRequest(refno)
{
	var answer = confirm("Are you sure you want to delete the laboratory request with a reference no. "+(refno)+"?");
	if (answer){
			xajax_deleteRequest(refno);
	}
}

function editSpLabRequest(refno)
{
	var pid=$('pid').value;
	return overlib(OLiframeContent('../../modules/special_lab/seg-splab-request-new.php?popUp=1&ref='+refno+'&pid='+pid+'&encounter_nr='+$('encounter_nr').value+'&user_origin=splab&ischecklist=1',
		800, 440, 'flab-list', 1, 'auto'),
	WIDTH, 750, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
	'<img src=../../images/close.gif border=0 onclick="refreshPage();">',
	CAPTIONPADDING, 4, CAPTION, 'Special Laboratory Request', MIDX, 0, MIDY, 0, STATUS, 'Special Laboratory Request');
}

function deleteSpLabRequest(refno)
{
	var answer = confirm("Are you sure you want to delete the laboratory request with a reference no. "+(refno)+"?");
	if (answer){
			xajax_deleteRequest(refno);
	}
}

function editBloodRequest(refno)
{
	var pid=$('pid').value;
	return overlib(OLiframeContent('../../modules/laboratory/seg-lab-request-new.php?popUp='+1+'&repeat=0&ref='+refno+'&pid='+pid+'&encounter_nr='+$('encounter_nr').value+'&user_origin=blood&ischecklist=1',
		800, 440, 'flab-list', 1, 'auto'),
	WIDTH, 750, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
	'<img src=../../images/close.gif border=0 onclick="refreshPage();">',
	CAPTIONPADDING, 4, CAPTION, 'Blood Request', MIDX, 0, MIDY, 0, STATUS, 'Blood Request');
}

function deleteBloodRequest(refno){
	var answer = confirm("Are you sure you want to delete the blood request with a reference no. "+(refno)+"?");
		if (answer){
			xajax_deleteRequest(refno);
		}
}

function editRadioRequest(refno) {

	return overlib(
		OLiframeContent('../../modules/radiology/seg-radio-request-new.php?popUp=1&refno='+refno+'&ischecklist=1',
			800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Radiology Request',
		MIDX,0, MIDY,0,
		STATUS,'Radiology Request');
}

function deleteRadioRequest(refno){
	var answer = confirm("You are about to delete service request #"+refno+". Are you sure?");
	if (answer){
		xajax_deleteRadioServiceRequest(refno);
	}
}

function editPharmaRequest(refno) {
	 return overlib(
		OLiframeContent('../../modules/pharmacy/seg-pharma-order.php?target=edit&from=CLOSE_WINDOW&ref='+refno,
			800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Pharmacy Request',
		MIDX,0, MIDY,0,
		STATUS,'Pharmacy Request');
}

function deletePharmaRequest(refno)
{
	var answer = confirm("Delete this pharmacy order?");
	if(answer){
		xajax_deleteOrder(refno);
	}
}

function editMiscRequest(refno)
{
	return overlib(
	OLiframeContent('../../modules/dialysis/seg-misc-request-new.php?from=CLOSE_WINDOW'+'&pid='+$('pid').value+'&encounter_nr='+$('encounter_nr').value+'&mode=edit&area=dialysis&refno='+refno,
		700, 440, 'fGroupTray', 0, 'auto'),
	WIDTH,700, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="../../images/close.gif" border=0 onclick="refreshPage();">',
	CAPTIONPADDING,2, CAPTION,'Miscellaneous Request',
	MIDX,0, MIDY,0,
	STATUS,'Miscellaneous Request');
}

function deleteMiscRequest(refno)
{
	var answer = confirm("Delete this miscellaneous request?");
	if(answer){
		xajax_deleteMiscRequest(refno);
	}
}
