var timeoutHandle=0;
function retail_clearProductPrices() {
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("ppriceTable")) {
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

function retail_addProductPrice(pid, pname, pprice, pricecash, pricecharge) {
	var destRows, destTableBody, newRowDest, lastRowNo;
	var i, cntr;

	if (destTable=document.getElementById("ppriceTable")) {			
		
		destTableBody=destTable.getElementsByTagName("tbody")[0];
		destRows=destTableBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (destRows.length>0) lastRowNo=destRows[destRows.length-1].id.replace("ppriceRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;
		
		if (pid) {
			newRowDest = '<tr class="wardlistrow1" id="ppriceRow'+lastRowNo+'">' +
				'<td style="height:22px"><b>'+pid+'</b><input type="hidden" id="ppricePID'+lastRowNo+'" value="'+pid+'"></td>'+
				'<td>'+pname+'<input type="hidden" id="ppricePName'+lastRowNo+'" value="'+pname+'"></td>'+
				'<td align="center"><input type="text" id="ppricePPrice'+lastRowNo+'" value="'+pprice+'" style="width:60px"></td>'+
				'<td align="center"><input type="text" id="ppricePriceCash'+lastRowNo+'" value="'+pricecash+'" style="width:60px"></td>'+
				'<td align="center"><input type="text" id="ppricePriceCharge'+lastRowNo+'" value="'+pricecharge+'" style="width:60px"></td>'+				
				'<td align="center"><input type="button" id="ppriceUpdate'+lastRowNo+'" value="Go!" onclick="prepareUpdate('+lastRowNo+')"></td>'+
			'</tr>';
		}
		else {
			newRowDest = '<tr class="wardlistrow1" id="destRow'+lastRowNo+'">' +
				'<td colspan="5">No such product exists in the Pharmacy database...</td>' +
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

function retail_rmvProductPrice(rowNum) {
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("ppriceRow"+rowNum);
	if (destTable=document.getElementById("ppriceTable")) {
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

function prepareUpdate(rowNo) {
	var id, pprice, pricecash, pricecharge;
	id=$("ppricePID"+rowNo);
	pprice=$("ppricePPrice"+rowNo);
	pricecash=$("ppricePriceCash"+rowNo);
	pricecharge=$("ppricePriceCharge"+rowNo);
	
	if (!id.value) {
		alert("Invalid product specified...");
		return false;
	}
	else if (isNaN(pprice.value)) {
		alert("Enter a valid value for the product's purchase price.");
		pprice.focus();
		return false;
	}
	else if (isNaN(pricecash.value)) {
		alert("Enter a valid value for the product's retail price (cash).");
		pricecash.focus();
		return false;
	}
	else if (isNaN(pricecharge.value)) {
		alert("Enter a valid value for the product's retail price (charge).");
		pricecharge.focus();
		return false;
	}
	//alert("update");
	xajax_updateProductPrice(userid, rowNo, id.value, pprice.value, pricecash.value, pricecharge.value);
	return true;
}

function ppricecolorrow(rowNo) {
	Fat.fade_element("ppriceRow"+rowNo, 0, 1000, "#F57A74", false);
}

function prepareDelete(rowNo, isref) {
	var refno;
	if (confirm("Do you wish to delete this transaction?")) {
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