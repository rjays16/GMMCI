var timeoutHandle=0;
function retail_clearPersons() {
	// Search for the source row table element
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("personsTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.childNodes;
		// check if srcRows is valid and has more than 1 element	
		if (srcRows) {
			while (srcRows.length>0) {
				srcTableBody.removeChild(srcRows[0]);
			}
			//for (iterator=srcTable.) {
			//	srcTable.childNodes[1].removeChild(srcRows[1]);
			//}
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function retail_addPerson(pID, pFName, pGName, pDOB) {
	var srcRows, srcTableBody, newRowSrc, lastRowNo;
	var i;	
	if (srcTable=document.getElementById("personsTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (srcRows.length>0) lastRowNo=srcRows[srcRows.length-1].id.replace("srcRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;
		if (pID) {
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
				'<td>'+pID+'<input type="hidden" id="srcID'+lastRowNo+'" value="'+pID+'"></td>'+
				'<td>'+pFName+'<input type="hidden" id="srcFName'+lastRowNo+'" value="'+pFName+'"></td>'+
				'<td>'+pGName+'<input type="hidden" id="srcGName'+lastRowNo+'" value="'+pGName+'"></td>'+
				'<td>'+pDOB+'<input type="hidden" id="srcDOB'+lastRowNo+'" value="'+pDOB+'"></td>'+					
				'<td align="center"><input type="button" id="srcAdd'+lastRowNo+'" value=">" onclick="xajax_populateTransactions('+pID+')" style="width:25px"></td>'+
			'</tr>';
		}
		else {
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
				'<td colspan="5">No match found...</td>' +
			 '</tr>';
		}
		srcTableBody.innerHTML += newRowSrc;
	}
}

function retail_clearTransactions() {
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("detailsTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.childNodes;
		// check if srcRows is valid and has more than 1 element	
		if (srcRows) {
			while (srcRows.length>0) {
				srcTableBody.removeChild(srcRows[0]);
			}
			//for (iterator=srcTable.) {
			//	srcTable.childNodes[1].removeChild(srcRows[1]);
			//}
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail

}

function retail_addTransaction(refno, pid, pname, pdate, iscash) {
	var destRows, destTableBody, newRowDest, lastRowNo;
	var i, cntr;

	if (destTable=document.getElementById("detailsTable")) {
		
		destTableBody=destTable.getElementsByTagName("tbody")[0];
		destRows=destTableBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (destRows.length>0) lastRowNo=destRows[destRows.length-1].id.replace("destRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;		
		
		if (refno) {
			newRowDest = '<tr class="wardlistrow1" id="destRow'+lastRowNo+'">' +
				'<td style="height:22px"><b>'+refno+'</b><input type="hidden" id="destRefNo'+lastRowNo+'" value="'+refno+'">'+
				'<input type="hidden" id="destPID'+lastRowNo+'" value="'+pid+'">'+
				'<input type="hidden" id="destPName'+lastRowNo+'" value="'+pname+'"></td>'+
				'<td>'+pdate+'<input type="hidden" id="destPDate'+lastRowNo+'" value="'+pdate+'"></td>'+
				'<td>'+((iscash!=0)?'Cash':'Charge')+'<input type="hidden" id="destIsCash'+lastRowNo+'" value="'+iscash+'"></td>'+				
				'<td align="center"><input type="button" id="destEdit'+lastRowNo+'" onclick=\'launchTransactionEdit("'+refno+'", "'+pid+'", "'+pname+'", "'+pdate+'", "'+iscash+'")\' value="Edit" style="width:40px"></td>'+
				'<td align="center"><input type="button" id="destRmv'+lastRowNo+'" onclick="prepareDelete('+lastRowNo+')" value="Rmv" style="width:40px"></td>'+
//				'<td align="center"><a href="#" id="destEdit'+lastRowNo+'" onclick=\'launchTransactionEdit("'+refno+'", "'+pid+'", "'+pname+'", "'+pdate+'", "'+iscash+'")\'><u>Edit</u></td>'+
//				'<td align="center"><a href="#" id="destRmv'+lastRowNo+'" onclick="prepareDelete('+lastRowNo+')"><u>Rmv</u></td>'+
			'</tr>';
		}
		else {
			newRowDest = '<tr class="wardlistrow1" id="destRow'+lastRowNo+'">' +
				'<td colspan="5">No transactions found for this person...</td>' +
			 '</tr>';
		}
		
		destTableBody.innerHTML += newRowDest;

		destRows=destTableBody.getElementsByTagName("tr");		
		cntr=0;
		for (i in destRows) {
			destRows[i].style.className=((cntr%2==0)?"wardlistrow1":"wardlistrow2");
			cntr++;
		}
	}
}

function retail_rmvTransaction(rowNum) {
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("destRow"+rowNum);
	if (destTable=document.getElementById("detailsTable")) {
		destRows=destTable.getElementsByTagName("tbody")[0];
		// check if srcRows is valid and has more than 1 element
		if (destRows) {
			destRows.removeChild(rmvRow);
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function retail_clearRefTransactions() {
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("refTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.childNodes;
		// check if srcRows is valid and has more than 1 element	
		if (srcRows) {
			while (srcRows.length>0) {
				srcTableBody.removeChild(srcRows[0]);
			}
			//for (iterator=srcTable.) {
			//	srcTable.childNodes[1].removeChild(srcRows[1]);
			//}
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail

}

function retail_addRefTransaction(refno, pid, pname, pdate, iscash) {
	var destRows, destTableBody, newRowDest, lastRowNo;
	var i, cntr;

	if (destTable=document.getElementById("refTable")) {
		
		destTableBody=destTable.getElementsByTagName("tbody")[0];
		destRows=destTableBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (destRows.length>0) lastRowNo=destRows[destRows.length-1].id.replace("refRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;		
		
		if (refno) {
			newRowDest = '<tr class="wardlistrow1" id="refRow'+lastRowNo+'">' +
				'<td style="height:22px"><b>'+refno+'</b><input type="hidden" id="refRefNo'+lastRowNo+'" value="'+refno+'">'+
				'<input type="hidden" id="refPID'+lastRowNo+'" value="'+pid+'">'+
				'<input type="hidden" id="refPName'+lastRowNo+'" value="'+pname+'"></td>'+
				'<td>'+pdate+'<input type="hidden" id="refPDate'+lastRowNo+'" value="'+pdate+'"></td>'+
				'<td>'+((iscash!=0)?'Cash':'Charge')+'<input type="hidden" id="refIsCash'+lastRowNo+'" value="'+iscash+'"></td>'+				
				'<td align="center"><input type="button" id="refEdit'+lastRowNo+'" onclick=\'launchTransactionEdit("'+refno+'", "'+pid+'", "'+pname+'", "'+pdate+'", "'+iscash+'")\' value="Edit" style="width:40px"></td>'+
				'<td align="center"><input type="button" id="refRmv'+lastRowNo+'" onclick="prepareDelete('+lastRowNo+')" value="Rmv" style="width:40px"></td>'+
//				'<td align="center"><a href="#" id="refEdit'+lastRowNo+'" onclick=\'launchTransactionEdit("'+refno+'", "'+pid+'", "'+pname+'", "'+pdate+'", "'+iscash+'")\'><u>Edit</u></td>'+
//				'<td align="center"><a href="#" id="refRmv'+lastRowNo+'" onclick="prepareDelete('+lastRowNo+',true)"><u>Rmv</u></td>'+
			'</tr>';
		}
		else {
			newRowDest = '<tr class="wardlistrow1" id="refRow'+lastRowNo+'">' +
				'<td colspan="5">Invalid reference number...</td>' +
			 '</tr>';
		}
		
		destTableBody.innerHTML += newRowDest;
		destRows=destTableBody.getElementsByTagName("tr");		
		cntr=0;
		for (i in destRows) {
			destRows[i].style.className=((cntr%2==0)?"wardlistrow1":"wardlistrow2");
			cntr++;
		}
	}
}

function retail_rmvRefTransaction(rowNum) {
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("refRow"+rowNum);
	if (destTable=document.getElementById("refTable")) {
		destRows=destTable.getElementsByTagName("tbody")[0];
		// check if srcRows is valid and has more than 1 element
		if (destRows) {
			destRows.removeChild(rmvRow);
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function prepareDelete(rowNo, isref) {
	var refno;
	if (confirm("Do you wish to delete this transaction?")) {
		if (isref)
			refno = document.getElementById("refRefNo"+rowNo).value;
		else
			refno = document.getElementById("destRefNo"+rowNo).value;
		if (refno) {
				xajax_delTransaction(refno, rowNo, isref);
		}
		else
			alert("Invalid reference number encountered...");
		endif;
	}
}

function launchTransactionEdit(refno, pid, pname, pdate, iscash) {
		if (refno) {
		document.getElementById("sRefNo").value=refno;
		document.getElementById("sPayerID").value=pid;
  	document.getElementById("sPayerName").value=pname;
		document.getElementById("sPurchaseDate").value=pdate;
		document.getElementById("sIsCash").value=iscash;
		document.forms[0].submit();
		return true;
	}
	else return false;
}

function fetchPersonList(kword, ms) {
	if (timeoutHandle) {
		clearTimeout(timeoutHandle);
		timeoutHandle=0;
	}
	if (kword) {
		timeoutHandle=setTimeout("xajax_populatePersonList('"+kword+"')",ms);
	}
}

function fetchRefList(ref, ms) {
	if (timeoutHandle) {
		clearTimeout(timeoutHandle);
		timeoutHandle=0;
	}
	if (ref) {
		timeoutHandle=setTimeout("xajax_populateTransactionsByRefNo('"+ref+"')",ms);
	}
}