// http://xxxemofreakxxx.blogspot.com/2006/08/strapping-young-lad.html
var highlightColor="#E4F0A2";
//#F57A74
var rows=0;

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
	$('pname').value='';
	$('pname').focus();
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
	gui_clearTable('paramTable');
}

function nrow(id, name, msrunit, median, lbound, ubound, lcrit, ucrit, ltoxic, utoxic, flash) {
	var dRowSrc;
	if (id) {
		if (!name) name='';
		if (!msrunit) msrunit='';
		if (!median) median='';
		if (!lbound) lbound='';
		if (!ubound) ubound='';
		if (!lcrit) lcrit='';
		if (!ucrit) ucrit='';
		if (!ltoxic) ltoxic='';
		if (!utoxic) utoxic='';
				
		dRowSrc = '<tr id="pRow{{ROWNO}}" height="24">' +
					'<td style="padding-left:4px" align="left">'+name+'<input type="hidden" id="pid{{ROWNO}}" value="'+id+'"><input type="hidden" id="pname{{ROWNO}}" value="'+name+'"></td>'+
					'<td style="padding-left:4px">'+msrunit+'<input type="hidden" id="pmsrunit{{ROWNO}}" value="'+msrunit+'"></td>'+
					'<td style="padding-right:6px" align="right"><b>'+median+'<input type="hidden" id="pmedian{{ROWNO}}" value="'+median+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+lbound+'<input type="hidden" id="plbound{{ROWNO}}" value="'+lbound+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+ubound+'<input type="hidden" id="pubound{{ROWNO}}" value="'+ubound+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+lcrit+'<input type="hidden" id="plcrit{{ROWNO}}" value="'+lcrit+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+ucrit+'<input type="hidden" id="pucrit{{ROWNO}}" value="'+ucrit+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+ltoxic+'<input type="hidden" id="pltoxic{{ROWNO}}" value="'+ltoxic+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+utoxic+'<input type="hidden" id="putoxic{{ROWNO}}" value="'+utoxic+'"></b></td>'+
					'<td align="center"><img '+editImageSrc+' id="pEdit{{ROWNO}}" onclick="editParam('+id+',{{ROWNO}});return false;" style="cursor:pointer"></td>'+
				'</tr>';
	}
	else {		
		dRowSrc = '<tr id="pRow{{ROWNO}}" height="24">' +
			'<td colspan="10">No paramters defined for this service...</td>' +
			 '</tr>';
	}
	if (!rows) gui_clearTable('paramTable');
	rowno=gui_addRow('paramTable','pRow',dRowSrc);
	if (!id) rows=0;
	colt('paramTable');
	if (flash && rowno!==false) fader('pRow'+rowno);
}

function xrow(rowno, id, name, msrunit, median, lbound, ubound, lcrit, ucrit, ltoxic, utoxic) {
	var dRowSrc;
	if (id) {
		if (!name) name='';
		if (!msrunit) msrunit='';
		if (!median) median='';
		if (!lbound) lbound='';
		if (!ubound) ubound='';
		if (!lcrit) lcrit='';
		if (!ucrit) ucrit='';
		if (!ltoxic) ltoxic='';
		if (!utoxic) utoxic='';

		dRowSrc = 
					'<td style="padding-left:4px" align="left">'+name+'<input type="hidden" id="pid'+rowno+'" value="'+id+'"><input type="hidden" id="pname'+rowno+'" value="'+name+'"></td>'+
					'<td style="padding-left:4px">'+msrunit+'<input type="hidden" id="pmsrunit'+rowno+'" value="'+msrunit+'"></td>'+
					'<td style="padding-right:6px" align="right"><b>'+median+'<input type="hidden" id="pmedian'+rowno+'" value="'+median+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+lbound+'<input type="hidden" id="plbound'+rowno+'" value="'+lbound+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+ubound+'<input type="hidden" id="pubound'+rowno+'" value="'+ubound+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+lcrit+'<input type="hidden" id="plcrit'+rowno+'" value="'+lcrit+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+ucrit+'<input type="hidden" id="pucrit'+rowno+'" value="'+ucrit+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+ltoxic+'<input type="hidden" id="pltoxic'+rowno+'" value="'+ltoxic+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+utoxic+'<input type="hidden" id="putoxic'+rowno+'" value="'+utoxic+'"></b></td>'+
					'<td align="center"><img '+editImageSrc+' id="pEdit'+rowno+'" onclick="editParam('+id+','+rowno+');return false;" style="cursor:pointer"></td>';

	}
	else {		
		dRowSrc = '<td colspan="10">No services are available for this group...</td>';
	}

	$('pRow'+rowno).innerHTML=dRowSrc;
	fader('pRow'+rowno);	
}

/* clearSelect
*  - Clears the contents of a <SELECT> element
*/

function clearOptions(id) {
	if ($(id).options) {
		$(id).options.length = 0;
		return true;
	}
	return false;
}

function populateOptions(id, list)
{
	var box2 = $(id);
	box2.options.length = 0;
	for(i=0;i<list.length;i+=2)
	{
		if (i==0) {
			service_code=list[i+1];
		}
		box2.options[i/2] = new Option(list[i],list[i+1]);
	}
}

function refreshTitle() {
	var gr=$('selectgroup'), sv=$('selectservice');
	var grname=gr.options[gr.selectedIndex].text;
	var svname=sv.options[sv.selectedIndex].text;
	$('tabletitle').innerHTML = dept_name+'>'+grname+'>'+svname;
}

function getSelectedService() {
	var sv=$('selectservice');
	if (sv.selectedIndex!==-1) {		
		return sv.options[sv.selectedIndex].value;
	}
	return false;
}

function validate(name) {
	var sv=$('selectservice'), svid;
	var ename=$(name);
	svid=getSelectedService();
	if (svid) {
		if (!ename.value) {
			alert("Enter the name for the new parameter...");
			ename.focus();
			return false;
		}
		xajax_nparam(ename.value,svid);	
		return true;
	}
	else {
		alert("No service was selected...");
		return false;
	}
}