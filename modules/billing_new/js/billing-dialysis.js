var refno;
var list = $('#session-list');
var delete_class;
var dialogSelEnc;
var arrBillnr = new Array();
var disabled;
var last_encr;

$(document).ready(function(){
	preset();
});

function preset(){
	refno = $("#ref_no").val();

	if(refno){
		xajax_getSessions(refno);
	}

	showButtons();
}

function clickFn(action){
	var pageSelEnc = "", title="";
	var pid = $("#pid").val();
	var details = new Object();

	switch(action){
		case 'add':
			pageSelEnc = "../../modules/billing_new/search-finalbill.php?pid="+pid+"&refno="+refno;
			title = "List of Billed Encounters";

			dialogSelEnc = $('<div></div>')
                    .html('<iframe style="border: 0px; " src="' + pageSelEnc + '" width="100%" height=400px></iframe>')
                    .dialog({
                    autoOpen: false,
                    modal: true,
                    show: 'fade',
                    hide: 'fade',
                    height: 'auto',
                    width: '800',
                    title: title,
                    position: 'top',
                  });

     		dialogSelEnc.dialog('open');
		break;
		case 'done':
			var conf = confirm("Are you sure you want to close this cycle? ");
			if(conf){
				details = {
					id:refno,
					trans_flag:"done",
					history: "done"
				};

				xajax_updateCycle(details);
			}
		break;
		case 'undone':
			var conf = confirm("Are you sure you want to undone this cycle? ");
			if(conf){
				details = {
					id:refno,
					trans_flag:"active",
					history: "undone",
					enc_nr: last_encr
				};

				xajax_updateCycle(details);
			}
		break;
		case 'save':
			var conf = confirm("Are you sure you want save this transaction?");
			if(conf){
				
				details = {
					id:refno,
					pid: $("#pid").val(),
					trans_flag:"active",
					history: "update"
				};

				var data = setItems();
				xajax_saveCycle(data, details);
			}
		break;
		case 'search':
			pageSelEnc = "../../modules/registration_admission/seg-select-enc.php?var_pid=pid&var_age=p_age&var_name=p_name&var_addr=p_add&var_include_enc=0";
			title = "Search Patient";

			dialogSelEnc = $('<div></div>')
                    .html('<iframe style="border: 0px; " src="' + pageSelEnc + '" width="100%" height=400px></iframe>')
                    .dialog({
                    autoOpen: false,
                    modal: true,
                    show: 'fade',
                    hide: 'fade',
                    height: 'auto',
                    width: '800',
                    title: title,
                    position: 'top',
                  });

     		dialogSelEnc.dialog('open');
		break;
	}
}

function setItems(){
	var data = new Object();
	
	for(var i=0; i<arrBillnr.length; i++){
		var bill_nr = arrBillnr[i];

		data[i] = {
			id: refno,
			bill_nr: $("#bill_nr_"+bill_nr).val(),
			session_date: $("#session_date_"+bill_nr).val()
		}
	}

	return data;
}

function closeSelEncDiaglog(){
    dialogSelEnc.dialog('close');
}

function clearData(){
	var status = $("#status");
	if(status.val().toLowerCase() == 'done'){
		delete_class = 'disabled'; 
		disabled = 'disabled';
	}else if(status.val().toLowerCase() == 'active'){
		delete_class = 'segSimulatedLink';
	}
}

function appendSessions(details){
	var dBody=$("#session-body");
	if (dBody) {
		if(details){
			var no = details.no,
				enc_nr = details.enc_nr,
				enc_dte = details.enc_dte,
				bill_nr = details.bill_nr,
				bill_dte = details.bill_dte,
				proc = details.proc,
				diag = details.diag,
				enc_type = details.enc_type;

			if(jQuery.inArray(bill_nr, arrBillnr) >= 0){
				alert("Already in the list");
			}else{

				if(no<1){
					no = arrBillnr.length + 1;
				}

				last_encr = enc_nr;

				src = "<tr id=\""+bill_nr+"\"class='jedPanel'>";
				src += "<input type='hidden' id='bill_nr_"+bill_nr+"' value='"+bill_nr+"'>";
				src += "<input type='hidden' id='session_date_"+bill_nr+"' value='"+enc_dte+"'>";
				src += "<td>"+enc_nr+"</td>";
				src += "<td>"+enc_dte+"</td>";
				src += "<td>"+enc_type+"</td>";
				src += "<td>"+diag+"</td>";
				src += "<td>"+proc+"</td>";
				src += "<td align=\"right\">";

				if(delete_class == 'disabled'){
					src+="<a title=\"Delete\"><img class=\""+delete_class+"\" src=\"../../images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\"/></a>"+"</td>";
				}else{
					src+="<a title=\"Delete\"><img class=\""+delete_class+"\" src=\"../../images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"removeSession(\'"+bill_nr+"\');\"/></a>"+"</td>";
				}

				src += "</tr>";
				
				arrBillnr.push(bill_nr);

				dBody.append(src);
				return true;
			}
			
		}
	}

	return false;
}

function removeSession(bill_nr){
	arrBillnr.splice(jQuery.inArray(bill_nr, arrBillnr), 1);
	$('#'+bill_nr).remove();
}

function pageReload(id){
	var url = "../billing_new/dialysis-edit.php"+$("#url").val()+"&userck=$userck&nr="+id+"&from=billing-list";
	window.location.href = url;
}

function warnClear(){
	if(!$("#pid").val()){
		return true;
	}else{
		return confirm("Are you sure you want to clear all data?");
	}
}

function emptyDataDialysisBilling(pid='', fname='', lname='', page='', padd='', status=''){
	$("#pid").val(pid);
	$("#p_name").val(fname+" "+lname);
	$("#p_age").val(page);
	$("#p_add").val(padd);
	$("#status").val(status);
	hideButtons();
	showButtons();
	arrBillnr.length = 0;
}

function pSearchClose(){
	dialogSelEnc.dialog("close");
}

function showButtons(){
	if($("#pid").val() && $("#status").val().toLowerCase() != 'done'){
		$("#addBtn").show();
		$("#saveBtn").show();
	}
}

function hideButtons(){
	$("#addBtn").hide();
	$("#saveBtn").hide();
	$("#session-body").html('');
}