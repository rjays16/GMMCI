var trayItems = 0;

function openOrderTray() {
	window.open("seg-order-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
}

function validate() {
	var iscash = $("iscash1").checked;
	if (!$('refno').value) {
		alert("Please enter the reference no.");
		$('refno').focus();
		return false;
	}
	if (iscash) {
		if (!$("ordername").value && !$("pid").value) {
			alert("Please enter the payer's name or select a registered person using the person search function...");
			$('ordername').focus();
			return false
		}
	}
	else {
		if (!$("pid").value) {
			alert("Please select a registered person using the person search function...");
			return false;
		}
	}
	var regexDate = new RegExp("(0[1-9]|1[012])[-  /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d");
	if (!regexDate.test($("orderdate").value)) {
		alert("Please enter a valid date (MM/DD/YYYY format)...");
		$("orderdate").focus();
		return false;
	}
	
	return true;
}

function pSearchClose(){
	cClick();	
}

function jsBilling(){
	var nr = $('encounter_nr').value
	//alert("hello mark"+ encounter_nr); 
	if(nr!=''){
		//alert("hello mark jsbilling= "+ nr); 
		xajax_mainBilling(nr);
	}else{
		$('select-enc').click();        // Added by LST ... 03.23.2009
	}
}


