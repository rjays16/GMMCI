var timeoutHandle=0;
var currentKeyword="";

function med_retail_gui_clearSrcRows() {
	// Search for the source row table element
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("srcRowsTable")) {
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

function med_retail_gui_clearDestRows() {
	var destTable, destRows;
	if (destTable=document.getElementById("destRowsTable")) {
		destRows=destTable.childNodes[1].childNodes;
		// check if srcRows is valid and has more than 1 element
		if (destRows) {
			while (destRows.length>1) {
				destTable.childNodes[1].removeChild(destRows[1]);
			}
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function med_retail_gui_addSrcProductRow(productID, productName, productPrice) {
	var srcRows, srcTableBody, newRowSrc, lastRowNo;
	var i;
	if (srcTable=document.getElementById("srcRowsTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (srcRows.length>0) lastRowNo=srcRows[srcRows.length-1].id.replace("srcRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;		
		if (productID) {
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
					'<td>'+productID+'<input type="hidden" id="srcID'+lastRowNo+'" value="'+productID+'"></td>'+
					'<td>'+productName+'<input type="hidden" id="srcName'+lastRowNo+'" value="'+productName+'"></td>'+
					'<td><b>'+productPrice+'<input type="hidden" id="srcPrice'+lastRowNo+'" value="'+productPrice+'"></b></td>'+
					'<td><input type="text" id="srcQty'+lastRowNo+'" value="0" style="width:100%"></td>'+
					'<td align="center"><input type="button" id="srcAdd'+lastRowNo+'" value=">" onclick="addValidate('+lastRowNo+')" style="width:25px"></td>'+
				'</tr>';
		}
		else {
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
				'<td colspan="5">No such product exists...</td>' +
				
			 '</tr>';
		}
		srcTableBody.innerHTML += newRowSrc;
	}
}

function med_retail_gui_addDestProductRow(productID, productName, productEntryNo, productPrice, productQty) {
	var destRows, destTableBody, newRowDest, lastRowNo;
	var i, cntr;
	
	if (destTable=document.getElementById("destRowsTable")) {
		
		var refno;
		refno = document.getElementById("refnoex").value;
		
		destTableBody=destTable.getElementsByTagName("tbody")[0];
		destRows=destTableBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (destRows.length>0) lastRowNo=destRows[destRows.length-1].id.replace("destRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;		
		if (productID) { 
				newRowDest = '<tr class="wardlistrow1" id="destRow'+lastRowNo+'">' +
					'<td id="destFields'+lastRowNo+'">'+
					'<input type="hidden" id="destEntryNo'+lastRowNo+'" value="'+productEntryNo+'">'+
					'<input type="hidden" id="destPrice'+lastRowNo+'" value="'+productPrice+'">'+
					'<input type="hidden" id="destQty'+lastRowNo+'" value="'+productQty+'">'+
					'<input type="hidden" id="destID'+lastRowNo+'" value="">'+
					productID+
					'</td>'+
					'<td>'+productName+'</td>'+
					'<td>'+productQty+'</td>'+
					'<td align="center"><input type="button" id="destRmv'+lastRowNo+'" value="x" style="width:25px" onclick="xajax_delTransactionDetail(\''+refno+'\',\''+productEntryNo+'\',\''+lastRowNo+'\')"></td>'+
				'</tr>';
		}
		else {
			newRowDest = '<tr class="wardlistrow1" id="destRow'+lastRowNo+'">' +
				'<td colspan="5">No products are listed...</td>' +
			 '</tr>';
		}
		destTableBody.innerHTML += newRowDest;

		destRows=destTableBody.getElementsByTagName("tr");
		
		cntr=0;
		for (i in destRows) {
			destRows[i].style.className=(cntr%2==0)?"wardlistrow1":"wardlistrow2";
			cntr++;
		}
	}
}

function med_retail_gui_rmvDestProductRow(rowNum) {
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("destRow"+rowNum);
	if (destTable=document.getElementById("destRowsTable")) {
		destRows=destTable.childNodes[1].childNodes;
		// check if srcRows is valid and has more than 1 element
		if (destRows) {
			destTable.childNodes[1].removeChild(rmvRow);
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function addValidate(srcRowNum) {
	var pid, pname, pqty, pprice;
	refno = document.getElementById("refnoex");
	pid = document.getElementById("srcID"+srcRowNum);
	pname = document.getElementById("srcName"+srcRowNum);
	pqty = document.getElementById("srcQty"+srcRowNum);
	pprice = document.getElementById("srcPrice"+srcRowNum);
	if (!pid.value) alert("Cannot add product to retail list...");
	if (isNaN(pqty.value)||(pqty.value<=0)) {
		alert("Please enter a valid quantity value for this product");
		pqty.focus();
		return false;
	}
	if (isNaN(pprice.value)||(pprice.value<=0)) {
		alert("Please enter a valid price value for this product");
		pprice.focus();
		return false;
	}
	//med_retail_gui_addDestProductRow(pid.value, pname.value, pprice.value, pqty.value, ppackage.value);
	xajax_addTransactionDetail(refno.value, pid.value, pname.value, pprice.value, pqty.value);
	pqty.value="0";
	ppackage.value="";
	pprice.value="0.0";
	return true;
}

function fetchProductList(keyword,iscashflag) {
	//alert("fetching keyword: '"+keyword+"'");
	if (keyword!="")
		xajax_populateProductList(keyword,iscashflag);
}

function prepareSendKeyword(kword, iscashflag, ms) {
	if (timeoutHandle) {
		clearTimeout(timeoutHandle);
		timeoutHandle=0;
	}
	timeoutHandle=setTimeout("fetchProductList('"+kword+"',"+iscashflag+")",ms);
}