// http://xxxemofreakxxx.blogspot.com/2006/08/strapping-young-lad.html
var highlightColor="#F57A74";
var rows=0;
var rowColors=new Array("#E6EFF7","#CBDEEE");


function showme(str) {
	$('sqlerror').value=str;
}

function gui_clearTable(id) {
	//alert("gui_clearTable");
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
	//alert("gui_delRow");
	rmvRow=document.getElementById(rowid+rowno);
	if (rmvRow) {
		rmvRow.parentNode.removeChild(rmvRow);
		return true;
	}
	else return false;	// fail
}

/*
*  Recolors table rows alternately. Use to preserve alternate row coloring when adding/deleting new 
* table rows.
*/
function colt(id) {
	//alert("colt");	
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

function crow2() {
	//alert("crow2");
	gui_clearTable('serviceTable');
}

function nrow2(refno, pid, name, rdate, enctype, flash) {
	var dRowSrc;
	//alert("nrow2 = "+refno);
	
	if (refno){
		//alert("true");
		dRowSrc = '<tr id="srvRow{{ROWNO}}" height="24">' +
					'<td style="padding-left:4px" align="left">'+pid+'<input type="hidden" id="srvpid{{ROWNO}}" value="'+pid+'"><input type="hidden" id="srvrefno{{ROWNO}}" value="'+refno+'"></td>'+
					'<td style="padding-left:4px" align="left">'+name+'<input type="hidden" id="srvname{{ROWNO}}" value="'+name+'"></td>'+
					'<td style="padding-right:6px" align="left">'+rdate+'<input type="hidden" id="srvrdate{{ROWNO}}" value="'+rdate+'"></td>'+
					'<td style="padding-right:6px" align="left">'+enctype+'<input type="hidden" id="srvenctype{{ROWNO}}" value="'+enctype+'"></td>'+
					'<td align="center"><a href="" id="srvEdit{{ROWNO}}" onclick="editService(\''+refno+'\',\''+pid+'\',{{ROWNO}});return false;" style="text-decoration:underline">Edit</a></td>'+
					'<td align="center"><a href="" id="srvDel{{ROWNO}}" onclick="if(confirm(\'Do you wish to remove this service?\')) { xajax_drequestor({{ROWNO}},\''+refno+'\',\''+pid+'\'); closechild();} return false;" style="text-decoration:underline">Delete</a></td>'+
					'</tr>';
	}else{
		//alert("false");		
		dRowSrc = '<tr id="srvRow{{ROWNO}}">' +
			'<td colspan="6">No lab service requests are available at this moment...</td>' +
			'</tr>';
	}
	if (!rows) gui_clearTable('serviceTable');
		rowno=gui_addRow('serviceTable','srvRow',dRowSrc);
	if (!pid) rows=0;
		colt('serviceTable');
	if (flash && rowno!==false) fader('srvRow'+rowno);
}

/*
function xrow2(rowno, refno, pid, name, rdate, enctype) {
	var dRowSrc;
	alert("xrow2 = "+rowno+", "+refno+", "+pid+", "+name+", "+rdate+", "+enctype);
	
	if (refno){
		//alert("true");
		dRowSrc = '<tr id="srvRow{{ROWNO}}" height="24">' +
					'<td style="padding-left:4px" align="left">'+pid+'<input type="hidden" id="srvpid'+rowno+'}" value="'+pid+'"><input type="hidden" id="srvrefno{{ROWNO}}" value="'+refno+'"></td>'+
					'<td style="padding-left:4px" align="left">'+name+'<input type="hidden" id="srvname'+rowno+'}" value="'+name+'"></td>'+
					'<td style="padding-right:6px" align="left">'+rdate+'<input type="hidden" id="srvrdate'+rowno+'}" value="'+rdate+'"></td>'+
					'<td style="padding-right:6px" align="left">'+enctype+'<input type="hidden" id="srvenctype'+rowno+'}" value="'+enctype+'"></td>'+
					'<td align="center"><a href="" id="srvEdit'+rowno+'" onclick="editService(\''+refno+'\',\''+pid+'\','+rowno+');return false;" style="text-decoration:underline">Edit</a></td>'+
					'<td align="center"><a href="" id="srvDel'+rowno+'" onclick="if(confirm(\'Do you wish to remove this service?\')) { xajax_drequestor('+rowno+',\''+refno+'\',\''+pid+'\'); closechild();} return false;" style="text-decoration:underline">Delete</a></td>'+
					'</tr>';
	}else{
		//alert("false");		
		dRowSrc = '<tr id="srvRow'+rowno+'">' +
			'<td colspan="6">No lab service requests are available at this moment...</td>' +
			'</tr>';
	}
	
	$('srvRow'+rowno).innerHTML=dRowSrc;
	fader('srvRow'+rowno);
}
*/