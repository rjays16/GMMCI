function js_getBillAreas(sdiscountid, elem1) {
	var s_ids = window.parent.$(elem1).value;	
	xajax_getBillAreas(sdiscountid, s_ids);		
}

function js_addBillArea(sarea_id, sdesc, b_assigned) {
	var srcRow = '';
	
	if (sarea_id) {		
		if (isNaN(b_assigned)) b_assigned = 0;
		srcRow = '<tr>'+
 				 '<td width="80%"><input id="code_'+sarea_id+'" type="hidden" value="'+sarea_id+'">'+sdesc+'</td>'+
				 '<td width="*" align="center"><input type="checkbox" id="applied[]" name="applied[]" style="cursor:pointer" '+(Number(b_assigned) > 0 ? 'checked' : '')+' value="'+b_assigned+'" onclick="js_applyToBillArea(\''+sarea_id+'\')" /></td>'+
				 '</tr>';
				 
		if (Number(b_assigned) > 0) {
			xajax_applyToBillArea(sarea_id);
		}
	}

	$('bill_areas_list-body').innerHTML += srcRow;
}

function js_clearBillAreas() {
	$('bill_areas_list-body').innerHTML = '';
}

function js_CancelApply() {
	window.parent.cClick();	
}

function js_applyToBillArea(sarea_id) {
	xajax_applyToBillArea(sarea_id);
}

function js_submitOnClick(elem1, elem2, elem3) {
	window.parent.js_goSave(elem1, elem2, elem3);
}
