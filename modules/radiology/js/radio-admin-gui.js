// http://xxxemofreakxxx.blogspot.com/2006/08/strapping-young-lad.html
var timeoutHandle=0;
var highlightColor="#F57A74";
var rows=0;
var rowColors=new Array("#E6EFF7","#CBDEEE");


function showme(str) {
	$('sqlerror').value=str;
}

function gui_clearTable(id) {
	var dTable, dRows, dTBody;
	if (dTable=document.getElementById(id)) {
		dTBody=dTable.getElementsByTagName("tbody")[0];
		dRows=dTBody.childNodes;
		// check if srcRows is valid and has more than 1 element	
		if (dRows) {
			while (dRows.length>0) {
				dTBody.removeChild(dRows[0]);
			}
			rows=0;
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function gui_addRow(tableid, rowid, rowcode) {
	var dRows, dTBody, dRowSrc, lastRowNo;
	var i;	
	if (dTable=document.getElementById(tableid)) {
		dTBody=dTable.getElementsByTagName("tbody")[0];
		dRows=dTBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (dRows.length>0) lastRowNo=dRows[dRows.length-1].id.replace(rowid,"");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;
		dRowSrc=rowcode;

		// Replace tokens
		dRowSrc = dRowSrc.replace(/{{ROWNO}}/g, lastRowNo);
		dTBody.innerHTML += dRowSrc;
		rows++;
		return lastRowNo;
	}
	return false;
}

function gui_repRow(tableid, rowid, rowno, rowcode) {
	var dTable, dRows, repRow;
	repRow=document.getElementById(rowid+rowno);
	if (dTable=document.getElementById(tableid)) {
		dRows=dTable.childNodes[1].childNodes;
		// check if srcRows is valid and has more than 1 element
		if (dRows) {
			// dTable.childNodes[1].removeChild(rmvRow);
			dRows.innerHTML = rowcode;
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function gui_delRow(tableid, rowid, rowno) {
	var rmvRow;
	rmvRow=document.getElementById(rowid+rowno);
	if (rmvRow) {
		rmvRow.parentNode.removeChild(rmvRow);
		return true;
	}
	else return false;	// fail
}

function clrForm() {
	$('srvcode').value='';
	$('srvname').value='';
	$('srvcash').value='';
	$('srvcharge').value='';
	$('srvcode').focus();
}

/*
*  Recolors table rows alternately. Use to preserve alternate row coloring when adding/deleting new 
* table rows.
*/
function colt(id) {
	var cntr=0;
	var dRows, dTBody, dRowSrc, lastRowNo;
	var i;	
	
	if (dTable=document.getElementById(id)) {
		dTBody=dTable.getElementsByTagName("tbody")[0];
		dRows=dTBody.getElementsByTagName("tr");
		for (i=0;i<dRows.length;i++) {
			if (dRows.item(i).id) {
				dRows.item(i).className=(cntr%2==0)?"wardlistrow1":"wardlistrow2";
				cntr++;
			}
		}
	}
}

function fader(id) {
	if (id)	Fat.fade_element(id, 0, 1000, highlightColor, false);
}

function crow() {
	gui_clearTable('serviceTable');
}

function nrow(code, name, cash, charge, dept_nr, flash) {
	var dRowSrc,
		cashprc, chargeprc;
	//alert("dept_nr = "+dept_nr);
	if (code) {
		if (isNaN(cash))
			cashprc="N/A";
		else {
			cashprc=cash-0;
			cashprc=cashprc.toFixed(2);
		}
		if (isNaN(charge))
			chargeprc="N/A";
		else{
			chargeprc=charge-0;
			chargeprc=chargeprc.toFixed(2);
		}
		dRowSrc = '<tr id="srvRow{{ROWNO}}" height="24">' +
					'<td style="padding-left:4px" align="left">'+code+'<input type="hidden" id="srvCode{{ROWNO}}" value="'+code+'"></td>'+
					'<td style="padding-left:4px">'+name+'<input type="hidden" id="srvname{{ROWNO}}" value="'+name+'"></td>'+
					'<td style="padding-right:6px" align="right"><b>'+cashprc+'<input type="hidden" id="srvCash{{ROWNO}}" value="'+cashprc+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+chargeprc+'<input type="hidden" id="srvCharge{{ROWNO}}" value="'+chargeprc+'"></b></td>'+
					'<td align="center"><a href="" id="srvEdit{{ROWNO}}" onclick="editService(\''+code+'\',{{ROWNO}});return false;" style="text-decoration:underline">Edit</a></td>'+
					'<td align="center"><a href="" id="srvDel{{ROWNO}}" onclick="if(confirm(\'Do you wish to remove this service?\')) { xajax_dsrv({{ROWNO}},\''+code+'\',\''+dept_nr+'\'); closechild();} return false;" style="text-decoration:underline">Delete</a></td>'+
					'</tr>';
					
					//'<td align="center"><a href="" id="srvDel{{ROWNO}}" onclick="if(confirm(\'Do you wish to remove this service?\')) { xajax_dsrv({{ROWNO}},\''+code+'\'); } return false;" style="text-decoration:underline">Delete</a></td>'+
	}
	else {		
		dRowSrc = '<tr id="srvRow{{ROWNO}}">' +
			'<td colspan="6">No services are available for this group...</td>' +
			'</tr>';
	}
	if (!rows) gui_clearTable('serviceTable');
	rowno=gui_addRow('serviceTable','srvRow',dRowSrc);
	if (!code) rows=0;
	colt('serviceTable');
	if (flash && rowno!==false) fader('srvRow'+rowno);
}

/*
function nrow_null(flash) {
	var dRowSrc
	
		dRowSrc = '<tr id="srvRow{{ROWNO}}">' +
			'<td colspan="6">&nbsp;</td>' +
			'</tr>';
			
	if (!rows) gui_clearTable('serviceTable');
	rowno=gui_addRow('serviceTable','srvRow',dRowSrc);
	if (!code) rows=0;
	colt('serviceTable');
	if (flash && rowno!==false) fader('srvRow'+rowno);
}
*/

function xrow(rowno, code, name, cash, charge, dept_nr) {
	var dRowSrc,
		cashprc, chargeprc;
	if (code) {
		if (isNaN(cash))
			cashprc="N/A";
		else {
			cashprc=cash-0;
			cashprc=cashprc.toFixed(2);
		}
		if (isNaN(charge))
			chargeprc="N/A";
		else{
			chargeprc=charge-0;
			chargeprc=chargeprc.toFixed(2);
		}
		dRowSrc = 
					'<td style="padding-left:4px" align="left">'+code+'<input type="hidden" id="srvCode'+rowno+'}" value="'+code+'"></td>'+
					'<td style="padding-left:4px">'+name+'<input type="hidden" id="srvname'+rowno+'" value="'+name+'"></td>'+
					'<td style="padding-right:6px" align="right"><b>'+cashprc+'<input type="hidden" id="srvCash'+rowno+'" value="'+cashprc+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+chargeprc+'<input type="hidden" id="srvCharge'+rowno+'" value="'+chargeprc+'"></b></td>'+
					'<td align="center"><a href="" id="srvEdit'+rowno+'" onclick="editService(\''+code+'\','+rowno+');return false;" style="text-decoration:underline">Edit</a></td>'+
					'<td align="center"><a href="" id="srvDel'+rowno+'" onclick="if(confirm(\'Do you wish to remove this service?\')) { xajax_dsrv('+rowno+',\''+code+'\',\''+dept_nr+'\'); closechild();} return false;" style="text-decoration:underline">Delete</a></td>';
	}
	else {		
		dRowSrc = '<tr id="srvRow'+rowno+'">' +
			'<td colspan="6">No services are available for this group...</td>' +
			'</tr>';
	}
//'<td colspan="6"><input type="text" id="groupid" value="'+name+'"></td>' +
/*
var color;
 	if ($('srvRow'+rowno).className.indexOf("wardlistrow1")!=-1) 
		color=rowColors[0];
	else
		color=rowColors[1]; */
	$('srvRow'+rowno).innerHTML=dRowSrc;
	fader('srvRow'+rowno);	
}

function validate(code, name, cash, charge) {
	
	//alert("valid");
	
	var ecode=$(code),
		ename=$(name),
		ecash=$(cash),
		echarge=$(charge);
	
	
	if($F('dept_nr')==0){
		alert("Please select a department.");
		$('dept_nr').focus();
		return false;
	}
	
	if($F('parameterselect')==0){
		alert("Please select a radiology service group.");
		$('parameterselect').focus();
		return false;
	}
	
	//if(d.gname.value==""){
	//	alert("Please enter a Laboratory Service Group name.");
	//	d.gname.focus();
	//}
	
	if (!ecode.value) {
		alert("Enter the service code...");
		ecode.focus();
		return false;
	}
	if (!ename.value) {
		alert("Enter the service name...");
		ename.focus();
		return false;
	}
	if (isNaN(ecash.value)) {
		alert("Enter the price(cash)...");
		ecash.focus();
		return false;
	}
	if (isNaN(ecash.value)) {
		alert("Enter the price(charge)...");
		ecash.focus();
		return false;
	}
	//xajax_nsrv(ecode.value, ename.value, ecash.value, echarge.value, group_id);
	group_id = $F('parameterselect');
	dept_nr = $F('dept_nr');
	//alert("xajax_nsrv");
	xajax_nsrv(ecode.value, ename.value, ecash.value, echarge.value, group_id, dept_nr);
	
}