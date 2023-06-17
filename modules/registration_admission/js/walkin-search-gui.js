var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function display(str) {
	document.write(str);
}

function prepareSelect(id) {
	var pid = $('id'+id).value;
	var name = $('fullname'+id).value;
	var addr = $('address'+id).value;
        
	if (var_pid)
		window.parent.$(var_pid).value = (noprefix==1 ? '' : 'W')+pid;
	if (var_name) {
		window.parent.$(var_name).value = name;
		window.parent.$(var_name).readOnly = true;
	}
	if (var_addr) {
		window.parent.$(var_addr).value = addr;
		window.parent.$(var_addr).readOnly = true;
	}
	if (var_clear)
		window.parent.$(var_clear).disabled=false;

	if (window.parent.pSearchClose) window.parent.pSearchClose();
	else if (window.parent.cClick) window.parent.cClick();
}

function addWalkin(details) {
	var list=$('wlst'), dRows, dBody, rowSrc;
	var i;
	var id=details['id'],
			fullname=details['fullname'].toUpperCase(),
			sex=details['sex'],
			address=details['address'],
			lastTransaction=details['lastTransaction']

	if (list) {
		dBody=list.select("tbody")[0];
		dRows=dBody.select("tr");
		// get the last row id and extract the current row no.
		if (id) {
			if (sex=='M')
				sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
			else if (sex=='F')
				sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
			else
				sexImg = '';

			rowSrc = '<tr style="height:28px">'+
									'<td class="centerAlign">'+
										'<input type="hidden" id="id'+id+'" value="'+id+'">'+
										'<input type="hidden" id="fullname'+id+'" value="'+fullname+'">'+
										'<input type="hidden" id="sex'+id+'" value="'+sex+'">'+
										'<input type="hidden" id="address'+id+'" value="'+address+'">'+
										'<input type="hidden" id="last'+id+'" value="'+lastTransaction+'">'+
										'<span id="show_id'+id+'" style="color:#660000">'+id+'</span>'+
									'</td>'+
									'<td class="centerAlign">'+sexImg+'</td>'+
									'<td><span id="show_fullname'+id+'">'+fullname+'</span></td>'+
									'<td><span id="show_address'+id+'">'+address+'</span></td>'+
									'<td class="centerAlign"><span id="show_last'+id+'">'+lastTransaction+'</span></td>'+
									'<td>'+
										'<input class="segButton" type="button" value="Select" style="color:#000066" '+
											'onclick="prepareSelect(\''+id+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			if (!details.error) details.error = 'No such person exists...';
			rowSrc = '<tr><td colspan="9" style="">'+details.error+'</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}