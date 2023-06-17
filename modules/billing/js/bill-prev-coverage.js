var PREV_REC=-1, NEXT_REC=1;

function init(e){
	YAHOO.util.Event.addListener("btnSave", "click", js_SavePrevCoverage);
	YAHOO.util.Event.addListener("btnCancel", "click", js_CancelEdit);	
	YAHOO.util.Event.addListener("btnAdd", "click", js_NewPrevCoverage); 	
	YAHOO.util.Event.addListener("btnEdit", "click", js_EditPrevCoverage);
	YAHOO.util.Event.addListener("btnDelete", "click", js_DelPrevCoverage);

	shortcut.add("Ctrl+A", function(){ js_NewPrevCoverage(); }, {
			'type':'keypress',
			'propagate':false});
	shortcut.add("Ctrl+E", function(){ js_EditPrevCoverage(); }, {
			'type':'keypress',
			'propagate':false});	
	shortcut.add("Ctrl+D", function(){ js_DelPrevCoverage(); }, {
			'type':'keypress',
			'propagate':false});
	shortcut.add("ESC", function(){ cClick(); });	
	shortcut.add("Ctrl+C", function(){ js_CancelEdit(); }, {
			'type':'keypress',
			'propagate':false});
	shortcut.add("Ctrl+S", function(){ js_SavePrevCoverage(); }, {
			'type':'keypress',
			'propagate':false});	
}//end function init

function js_SavePrevCoverage() {
	$('save_clicked').value = '1';
	document.forms[0].submit();
}

function js_initWindow() {
	var enc = $('enc_nr').value;
	var frm_dt = $('disclose_dte').value;	
	
	var seg_URL_APPEND = $F('seg_URL_APPEND');		
	window.location.href='billing-prev-coverage.php'+seg_URL_APPEND+'&nr='+enc+'&frmdte='+frm_dt;	
}

function js_CancelEdit() {
	var mode = $('mode').value;		
	var add_clicked = $('add_clicked').value;	
					
	if ((mode == 'add') && (add_clicked == '0')) 
		js_initWindow();
	else {		
		var enc = $('enc_nr').value;
		var frm_dt = $('disclose_dte').value;	
		var id = $('disclose_id').value;
		var hcare_id = $('old_hcare_id').value;	
		var entry_no = $('entry_no').value;	
	
		$('mode').value = '';
		$('postdate_trigger').style.visibility = 'hidden';
		
		fillInsuranceCombo(enc, frm_dt, 1);	
		
		if (mode == 'edit') 			
			js_showCoverageDetails(id, hcare_id, entry_no);
		else
			js_showCoverageDetails(id, hcare_id, 1);
	}		
}

function js_NewPrevCoverage() {
	var enc = $('enc_nr').value;
	var frm_dt = $('disclose_dte').value;
	
	$('mode').value = 'add';
	$('add_clicked').value = 1;
	
	$('postdate_trigger').style.visibility = '';	
	
	$('detail_section').style.visibility = "hidden";
	
	$('footer_edit').style.display = "none";
	$('footer_view').style.display = "none";
	
	fillInsuranceCombo(enc, frm_dt, 0);	
}

function js_EditPrevCoverage() {
	var elms=document.forms[0].elements;
	var elemfound = false;
	var n;
	
	for (var i=0; i < elms.length; i++)
		if (elms[i].name == 'amntbox[]') {
			if (!elemfound) {
				n = i;
				elemfound = true;
			}
			myElement = elms[i];
			$(myElement.value).disabled = ''
		}	
	$('postdate_trigger').style.visibility = '';
	$(elms[n].value).focus();
	
	$('footer_edit').style.display = "";
	$('footer_view').style.display = "none";	
	
	$('mode').value = 'edit';
}

function js_DelPrevCoverage() {
	var id = $('disclose_id').value;
	var hcare_id = $('old_hcare_id').value;	
	var entry_no = $('entry_no').value;		
	
	var message = "Do you really want to delete this posted previous coverage?\nClick OK to delete, CANCEL otherwise!";
	var ret_val = false;	

	ret_val = confirm(message);
	if (ret_val == true) {	
		xajax_delPrevCoverageDetail(id, hcare_id, entry_no);
	}
}

function fillInsuranceCombo(enc_nr, frm_dte, mode) {
	if (mode == 0)
		xajax_getHealthInsurancesForEdit(enc_nr);
	else
		xajax_getHealthInsurancesForViewing(enc_nr, frm_dte);
}

function js_AddOptions(tagId, text, value){
	var elTarget = $(tagId);
	if(elTarget){
		var opt = new Option(text, value);
		//var opt = new Option(value, value);
		opt.id = value;
		elTarget.appendChild(opt);
	}
	var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function js_ClearOptions(tagId){
	var optionsList, el=$(tagId);
	if(el){
		optionsList = el.getElementsByTagName('OPTION');
		for(var i=optionsList.length-1; i >=0 ; i--){
			optionsList[i].parentNode.removeChild(optionsList[i]);	
		}
	}
}//end of function js_ClearOptions

function js_AddCoverageDetail(fld_code, particulars, coverage, n_places) {
	var srcRow;
	var smode = $('mode').value;
	
	if (fld_code) {		
		srcRow = '<tr>'+			
					'<td><input id="code'+particulars+'" type="hidden" value="'+particulars+'"></td>'+
					'<td width="65%">'+particulars+'</td>'+					
					'<td align="center"><input type="hidden" name="amntbox[]" value="'+fld_code+'"><input style="text-align:right" type="text" size="15" maxlength="15" id="'+fld_code+'" name="'+fld_code+'" '+ (smode == '' ? 'disabled' : '') +
					   ' onblur="trimString(this); chkDecimalnPlaces(this,\''+particulars+'\', '+n_places+');" onFocus="this.select();" value="'+formatNumber(coverage, n_places)+'"></td>'
				'</tr>';
		
		$('coverage_details').innerHTML += srcRow;
	}		
}

function js_showCoverageDetails(id, hcare_id, entry_no) {	
	var mode = $('mode').value;
	
	if (mode == '') $('old_hcare_id').value = hcare_id;
	$('hcare_id').value = hcare_id;
	$('entry_no').value = entry_no;

	xajax_showPrevCoverageDetails(id, hcare_id, entry_no);		
	$('detail_section').style.visibility = "";
	
	if (mode != '') {
		$('footer_edit').style.display = "";
		$('footer_view').style.display = "none";
	}
	else {		
		$('footer_view').style.display = "";
		$('footer_edit').style.display = "none";
	}
}

function jumpToRec(ndirection) {
	var n = $('entry_no').value;
	n = Number(n) + Number(ndirection);	
	$('entry_no').value = n;		
	
	var id = $('disclose_id').value;
	var hcare_id = $('hcare_combo').options[$('hcare_combo').selectedIndex].value;	
	
	js_showCoverageDetails(id, hcare_id, n);	
}

function js_setRecLinks(b_showprev, b_shownext) {
	if (b_showprev) 
		$('prevRec').style.visibility = "";
	else
		$('prevRec').style.visibility = "hidden";

	if (b_shownext) 
		$('nextRec').style.visibility = "";
	else
		$('nextRec').style.visibility = "hidden";
}
