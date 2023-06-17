var timeoutHandle=0;
var currentKeyword="";
var highlightColor="#F57A74";	// Cell background color for a highlighted row

function toggleDisplay(id) {
	
	var el=document.getElementById(id);
	//alert("toggleDisplay id:el = "+id+" - "+el);
	if (el) {
		if (el.style.overflow=="hidden") el.style.overflow="visible";
		else el.style.overflow="hidden";
	}
	
}

/*added by VAS*/
function checkAll(parent, flag) {
	//alert("checkAll");
	var p=$(parent);
	var cList=p.getElementsByTagName('input');		
	for (var i=0;i<cList.length;i++) {
		if (cList[i].type=="checkbox")
			cList[i].checked=flag;
	}
}

function countSelected(parent,mod) {
	//alert("countSelected");
	var count=0;
	var p=$(parent);
	var cList=p.getElementsByTagName('input');		
	
	for (var i=0;i<cList.length;i++) {
		if (cList[i].type=="checkbox") {
			if (cList[i].checked&&cList[i].id!='chkall') count++;
		}
	}
	
	//alert("mod = "+mod);
	if (mod)
		count = count-1;
	
	if (count < 0)
		count = 0;
	
	return count;
}

function countItem(groupID,mod){
	//alert("count");
	//alert("groupID = "+groupID);
	//alert("countItem = "+document.inputform.svcChk0.value);
	$('selectedcount').innerHTML=countSelected('grp'+groupID, mod);
}
//checkAll('attachtList',this.checked);$('selectedcount').innerHTML=countSelected('attachtList');
//---------------------

/*edited by VAS*/
/*
function appendGroup(groupID, groupName) {
	//alert("appendGroup = "+saved);
	//alert("saveOK = "+$F('modeval'));
	//alert("appendGroup = grp"+groupID);
	var el=document.getElementById("listContainer");
	//alert("el = "+el)
	var newRow;
	if (el) {
		if ($F('modeval')=="update"){
		newRow = 
	'<span>' +
				'Selected: <span id="selectedcount">0</span>' +
	'</span>' +	
	'<table id="grp'+groupID+'" style="margin-bottom:5px" width="70%" border="0" cellpadding="0" cellspacing="1">'+
		'<thead class="">' +
			'<td colspan="4">'+
				'<table width="100%" cellpadding="2" cellspacing="2" border="0"><tr>'+
					'<td width="*" class="reg_header">'+groupName+'</td>'+
					'<td width="1%" align="right" style="padding:2px;font-weight:normal" class="reg_header"><span class="reglink" onclick="toggleDisplay(\'grpHead'+groupID+'\');toggleDisplay(\'grpBody'+groupID+'\');">Show/Hide</span>'+
				'</tr></table>'+
			'</td>'+
		'</thead>'+
		'<thead id="grphead'+groupID+'" class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;" id="srcRowsHeader">'+
			'<td width="1"><input id="chk_all_'+groupID+'" name="chk_all" type="checkbox" onClick="checkAll(grp'+groupID+',this.checked);countItem('+groupID+',1);" disabled></td>'+
			'<td width="20%" nowrap>Code</td>'+																		
			'<td width="65%" nowrap>Description</td>'+
			'<td width="15%">Price</td>'+
		'</thead>'+
		'<tbody id="grpBody'+groupID+'" style="height:0;overflow:visible"></tbody>'
		'</table>';
		}else{
			
			newRow = 
	'<span>' +
				'Selected: <span id="selectedcount">0</span>' +
	'</span>' +	
	'<table id="grp'+groupID+'" style="margin-bottom:5px" width="70%" border="0" cellpadding="0" cellspacing="1">'+
		'<thead class="">' +
			'<td colspan="4">'+
				'<table width="100%" cellpadding="2" cellspacing="2" border="0"><tr>'+
					'<td width="*" class="reg_header">'+groupName+'</td>'+
					'<td width="1%" align="right" style="padding:2px;font-weight:normal" class="reg_header"><span class="reglink" onclick="toggleDisplay(\'grpHead'+groupID+'\');toggleDisplay(\'grpBody'+groupID+'\');">Show/Hide</span>'+
				'</tr></table>'+
			'</td>'+
		'</thead>'+
		'<thead id="grphead'+groupID+'" class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;" id="srcRowsHeader">'+
			'<td width="1"><input id="chk_all_'+groupID+'" name="chk_all" type="checkbox" onClick="checkAll(grp'+groupID+',this.checked);countItem('+groupID+',1);"></td>'+
			'<td width="20%" nowrap>Code</td>'+																		
			'<td width="65%" nowrap>Description</td>'+
			'<td width="15%">Price</td>'+
		'</thead>'+
		'<tbody id="grpBody'+groupID+'" style="height:0;overflow:visible"></tbody>'
		'</table>';
		}
		el.innerHTML = el.innerHTML +newRow;
	}
}
*/
/*
function appendGroup(groupID, groupName) {
	var el=document.getElementById("listContainer");
	var newRow;
	alert("appendGroup groupID = "+groupID);
	//alert("el = "+el);
	/*
	if (el) {
		newRow = 
	'<span>' +
				'Selected: <span id="selectedcount">0</span>' +
	'</span>' +	
	'<table id="grp'+groupID+'" style="margin-bottom:5px" width="70%" border="0" cellpadding="0" cellspacing="1">'+
		'<thead class="">' +
			'<td colspan="4">'+
				'<table width="100%" cellpadding="2" cellspacing="2" border="0"><tr>'+
					'<td width="*" class="reg_header">'+groupName+'</td>'+
					'<td width="1%" align="right" style="padding:2px;font-weight:normal" class="reg_header"><span class="reglink" onclick="toggleDisplay(\'grpHead'+groupID+'\');toggleDisplay(\'grpBody'+groupID+'\');">Show/Hide</span>'+
				'</tr></table>'+
			'</td>'+
		'</thead>'+
		'<thead id="grphead'+groupID+'" class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;" id="srcRowsHeader">'+
			'<td width="1"><input id="chk_all_'+groupID+'" name="chk_all" type="checkbox" onClick="checkAll(grp'+groupID+',this.checked);countItem('+groupID+',1);"></td>'+
			'<td width="20%" nowrap>Code</td>'+																		
			'<td width="65%" nowrap>Description</td>'+
			'<td width="15%">Price</td>'+
		'</thead>'+
		'<tbody id="grpBody'+groupID+'" style="height:0;overflow:visible"></tbody>'
		'</table>';
		*//*
		if (el) {
		newRow = 
	'<span>' +
				'Selected: <span id="selectedcount">0</span>' +
	'</span>' +	
	'<table id="grp'+groupID+'" style="margin-bottom:5px" width="70%" border="0" cellpadding="0" cellspacing="1">'+
		'<thead class="">' +
			'<td colspan="4">'+
				'<table width="100%" cellpadding="2" cellspacing="2" border="0"><tr>'+
					'<td width="*" class="reg_header">'+groupName+'</td>'+
					'<td width="1%" align="right" style="padding:2px;font-weight:normal" class="reg_header"><span class="reglink" onclick="toggleDisplay(\'grpHead'+groupID+'\');toggleDisplay(\'grpBody'+groupID+'\');">Show/Hide</span>'+
				'</tr></table>'+
			'</td>'+
		'</thead>'+
		'<thead id="grphead'+groupID+'" class="reg_list_titlebar" style="height:0;overflow:visible;font-weight:bold;padding:4px;" id="srcRowsHeader">'+
			'<td width="1"><input id="chk_all'+groupID+'" name="chk_all" type="checkbox" onClick="checkAll(grp'+groupID+',this.checked);countItem(\''+groupID+'\',1);"></td>'+
			'<td width="20%" nowrap>Code</td>'+																		
			'<td width="65%" nowrap>Description</td>'+
			'<td width="15%">Price</td>'+
		'</thead>'+
		'<tbody id="grpBody'+groupID+'" style="height:0;overflow:visible"></tbody>'
		'</table>';
		el.innerHTML = el.innerHTML +newRow;
		//alert("newRow = "+newRow);
		//alert("el.innerHTML = "+el.innerHTML);
	}
}*/

function ajxClearTable(groupID){
	//alert("ajxClearTable");	
	var dBody = document.getElementById('grpBody'+groupID);
	//alert("dBody grp = "+dBody);
	if (dBody) {
		dBody.innerHTML = " ";
	}
}


function appendServiceItemToGroup(groupID, code, name, price, chk) {
	//alert("appendServiceItemToGroup grpid ="+groupID);
	var dBody = document.getElementById('grpBody'+groupID);
	//var dBody = document.getElementById('grpBody2');
	//var dBody = $('grpBody'+groupID);
	//alert("dBody grp = "+dBody + " - "+groupID+" - grpBody"+groupID);
	if (dBody) {
		var dRows, lastRowNo, newRowSrc;
		dRows=dBody.getElementsByTagName("tr");
		// get the last row id and extract the current row no.
		if (dRows.length>0) lastRowNo=dRows[dRows.length-1].id.replace("dRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;	
		//alert("lastRowNo = "+lastRowNo);
		
		//alert("dBody.innerHTML = "+dBody.innerHTML);
		if (code) {
			//alert("code = "+code+" - "+chk);
			if (chk!=0){
				//alert("true");
				newRowSrc = '<tr class="wardlistrow1" id="svcRow'+lastRowNo+'">' +
					'<td><input type="checkbox" name="svcChk'+lastRowNo+'" id="svcChk'+lastRowNo+'" value="'+code+'" checked onClick="countItem(\''+groupID+'\',0);"></td>'+
					'<td>'+code+'<input type="hidden" id="svcCode'+lastRowNo+'" value="'+code+'"></td>'+
					'<td>'+name+'<input type="hidden" id="svcName'+lastRowNo+'" value="'+name+'"></td>'+
					'<td align="right">'+price+'<input type="hidden" id="svcPrice'+lastRowNo+'" value="'+price+'"></td>'+
				'</tr>';	
			}else {
				//alert("false");
				newRowSrc = '<tr class="wardlistrow1" id="svcRow'+lastRowNo+'">' +
					'<td><input type="checkbox" name="svcChk'+lastRowNo+'" id="svcChk'+lastRowNo+'" value="'+code+'" onClick="countItem(\''+groupID+'\',0);"></td>'+
					'<td>'+code+'<input type="hidden" id="svcCode'+lastRowNo+'" value="'+code+'"></td>'+
					'<td>'+name+'<input type="hidden" id="svcName'+lastRowNo+'" value="'+name+'"></td>'+
					'<td align="right">'+price+'<input type="hidden" id="svcPrice'+lastRowNo+'" value="'+price+'"></td>'+
				'</tr>';	
			}	
		
		}
		/*
		else {
			//alert("false");
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
				'<td colspan="4">No such service exists...</td>' +
			 '</tr>';
		}*/
		dBody.innerHTML += newRowSrc;
		//alert("dBody.innerHTML = "+dBody.innerHTML);
	}
}

function appendServiceItemToGroup2(groupID) {
	var dBody = document.getElementById('grpBody'+groupID);
	if (dBody) {
			var dRows, lastRowNo, newRowSrc;
			dRows=dBody.getElementsByTagName("tr");
			
			// get the last row id and extract the current row no.
			if (dRows.length>0) lastRowNo=dRows[dRows.length-1].id.replace("dRow","");
			lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;	
			
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
				'<td colspan="4">No services are available for this group...</td>' +
			 '</tr>';
	}
		dBody.innerHTML += newRowSrc;
		
}


/*
function appendServiceItemToGroup(groupID, code, name, price) {
	//alert("appendServiceItemToGroup = "+saved);
	var dBody = document.getElementById('grpBody'+groupID);
	if (dBody) {
		var dRows, lastRowNo, newRowSrc;
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (dRows.length>0) lastRowNo=dRows[dRows.length-1].id.replace("dRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;		
		
		//alert("lastRowNo = "+lastRowNo);
		if (code) {
			if ($F('modeval')=="update"){
			newRowSrc = '<tr class="wardlistrow1" id="svcRow'+lastRowNo+'">' +
					'<td><input type="checkbox" name="svcChk'+lastRowNo+'" id="svcChk'+lastRowNo+'" value="'+code+'" onClick="countItem('+groupID+',0);" disabled></td>'+
					'<td>'+code+'<input type="hidden" id="svcCode'+lastRowNo+'" value="'+code+'"></td>'+
					'<td>'+name+'<input type="hidden" id="svcName'+lastRowNo+'" value="'+name+'"></td>'+
					'<td align="right">'+price+'<input type="hidden" id="svcPrice'+lastRowNo+'" value="'+price+'"></td>'+
				'</tr>';
			}else{
				newRowSrc = '<tr class="wardlistrow1" id="svcRow'+lastRowNo+'">' +
					'<td><input type="checkbox" name="svcChk'+lastRowNo+'" id="svcChk'+lastRowNo+'" value="'+code+'" onClick="countItem('+groupID+',0);"></td>'+
					'<td>'+code+'<input type="hidden" id="svcCode'+lastRowNo+'" value="'+code+'"></td>'+
					'<td>'+name+'<input type="hidden" id="svcName'+lastRowNo+'" value="'+name+'"></td>'+
					'<td align="right">'+price+'<input type="hidden" id="svcPrice'+lastRowNo+'" value="'+price+'"></td>'+
				'</tr>';	
			}
		}
		else {
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
				'<td colspan="4">No such service exists...</td>' +
			 '</tr>';
		}
		dBody.innerHTML += newRowSrc;
	}
		
}
*/
function removeServiceItemFromList(listID, itemID) {
}

function clearServiceList(listID) {
}


function gui_clearSrcRows() {
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

function gui_clearDestRows() {
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

function gui_addSrcDetailRow(srvCode, srvDesc, srvPrice) {
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
					'<td>'+srvCode+'<input type="hidden" id="srcCode'+lastRowNo+'" value="'+srvCode+'"></td>'+
					'<td>'+srvDesc+'<input type="hidden" id="srcDesc'+lastRowNo+'" value="'+srvDesc+'"></td>'+
					'<td align="center"><input type="button" id="srcAdd'+lastRowNo+'" value=">" onclick="addValidate('+lastRowNo+')" style="width:25px"></td>'+
				'</tr>';
		}
		else {
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
				'<td colspan="4">No such service exists...</td>' +
			 '</tr>';
		}
		srcTableBody.innerHTML += newRowSrc;
	}
}

function gui_addDestProductRow(productID, productName, productEntryNo, productPrice, productQty, summarize) {
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
					'<input type="hidden" id="destProductName'+lastRowNo+'" value="'+productName+'">'+
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
		if (summarize) {
			summary();
		}
		destRows=destTableBody.getElementsByTagName("tr");
		
		cntr=0;
		for (i in destRows) {
			destRows[i].style.className=(cntr%2==0)?"wardlistrow1":"wardlistrow2";
			cntr++;
		}
	}
}

function gui_rmvDestProductRow(rowNum) {
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("destRow"+rowNum);
	if (destTable=document.getElementById("destRowsTable")) {
		destRows=destTable.childNodes[1].childNodes;
		// check if srcRows is valid and has more than 1 element
		if (destRows) {
			destTable.childNodes[1].removeChild(rmvRow);
			summary();
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
	//pharma_retail_gui_addDestProductRow(pid.value, pname.value, pprice.value, pqty.value, ppackage.value);
	xajax_addTransactionDetail(refno.value, pid.value, pname.value, pprice.value, pqty.value);
	pqty.value="0";
	//ppackage.value="";
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
	timeoutHandle=setTimeout("fetchProductList('"+kword+"',"+(iscashflag?1:0)+")",ms);
}

/* DISCOUNT GUI FUNCTIONS */
var discountOptions=new Array;

function clearDiscountOptions() {
	var selObj = $("selDiscount");
	while (discountOptions.length>0)
		discountOptions.pop();
	while (selObj.options.length>0)
		selObj.removeChild(selObj.options[0]);
}

function addDiscountOption(id, desc, discount, isDefault) {
	var discountItem = new Object,
			selObj, newopt;
	discountItem.id = id;
	discountItem.desc = desc;
	discountItem.discount = discount;
	discountOptions[discountOptions.length]=discountItem;
	selObj = $("selDiscount");
	//alert(selObj.options.length);
	selObj.options[selObj.options.length] = new Option(desc+" ("+(discount*100)+"%)", id, isDefault, false);	
}

function prepareAddRDiscount() {
	var idx=$('selDiscount').selectedIndex;	
	var srcTable, srcRows, srcTableBody;
	var i, isAdded;

	if (srcTable=document.getElementById("rdiscountTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");
	}
	
	if (idx!=-1 && !(isNaN(idx))) {

		//check if the row is already in the list
		isAdded=false;
		for (i=0;i<srcRows.length;i++) {
			rowid = srcRows[i].id.replace("rdiscountRow","");
			if (discountOptions[idx].id==$("rdiscountID"+rowid).value) {
				isAdded=true;
				break;
			}
		}
		
		if (isAdded) {
			Fat.fade_element("rdiscountRow"+rowid, 0, 1000, highlightColor, false);
		}
		else {
			xajax_addRetailDiscount(refno,
														discountOptions[idx].id, 
														discountOptions[idx].desc, 
														discountOptions[idx].discount);
		}
	}
	else {
		alert("Pls. select a discount option...");
	}
}

function gui_clearRDiscountRows() {
	// Search for the source row table element
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("rdiscountTable")) {
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

function gui_addRDiscountRow(id, desc, discount, highlight) {
	var srcTable, srcRows, srcTableBody, newRowSrc, lastRowNo;
	var i;
	if (highlight==null) highlight=false;
	if (srcTable=document.getElementById("rdiscountTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");
		
		// get the last row id and extract the current row no.
		if (srcRows.length>0) lastRowNo=srcRows[srcRows.length-1].id.replace("rdiscountRow","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;		
		if (!isNaN(id)) {
			newRowSrc = '<tr class="wardlistrow1" id="rdiscountRow'+lastRowNo+'">'+
				'<td width="*"><input type="hidden" id="rdiscountID'+lastRowNo+'" value="'+id +'">'+
				desc+'<input type="hidden" id="rdiscountDesc'+lastRowNo+'" value="'+desc+'"></td>'+
				'<td width="80" align="right"><b>'+(discount*100).toFixed(2)+'%<input type="hidden" id="rdiscountDiscount'+lastRowNo+'" value="'+discount+'"></b></td>'+
				'<td width="40" align="center"><input type="button" id="rdiscountRmv'+lastRowNo+'" value="x" onclick="xajax_rmvRetailDiscount(\''+refno+'\','+id+','+lastRowNo+')" style="width:25px"></td>'+
			'</tr>';
		}
		else {
			newRowSrc = '<tr class="wardlistrow1" id="rdiscountRow'+lastRowNo+'">' +
				'<td colspan="5">No discount added</td>' +
			 '</tr>';
		}
		srcTableBody.innerHTML += newRowSrc;
		summary();
		if (highlight) Fat.fade_element("rdiscountRow"+lastRowNo, 0, 1000, highlightColor, false);
	}
}

/* 
 *
 */

function gui_rmvRDiscountRow(rowNum) {
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("rdiscountRow"+rowNum);
	if (destTable=document.getElementById("rdiscountTable")) {
		destRows=destTable.childNodes[1].childNodes;
		// check if srcRows is valid and has more than 1 element
		if (destRows) {
			destTable.childNodes[1].removeChild(rmvRow);
			summary();
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}



function js_round(x, n) {
	if (x > 8191 && x < 10485) {
		x = x-5000;
		return Math.round(x*Math.pow(10,n))/Math.pow(10,n)+5000;
	} else {
		return Math.round(x*Math.pow(10,n))/Math.pow(10,n);
	}
}

/* 
 *   GUI function, displays the summary for the transaction, including the financial details for the 
 * transaction.
 */
function summary() {
	var dscTable, dscTableBody, dscRows;
	var i, rowid;
	var totalDiscount=0;
	
	// Obtain the total discount for the transaction
	totalDiscount=0;
	if (dscTable=$("rdiscountTable")) {
		dscTableBody=dscTable.getElementsByTagName("tbody")[0];
		dscRows=dscTableBody.getElementsByTagName("tr");
		for (i=0;i<dscRows.length;i++) {
			rowid = dscRows[i].id.replace("rdiscountRow","");
			totalDiscount+=($("rdiscountDiscount"+rowid).value-0);			
		}
	}	
	//totalDiscount=totalDiscount.toString().substring(2);
	if (totalDiscount>1.0) totalDiscount=1.0;
	$("txtTotalDiscount").innerHTML = (totalDiscount*100).toFixed(2)+"%";	


	var itmTable, itmTableBody, itmRows;
	var itemName, itemQty, itemPrice;
	var totalPrice=0, price, discount;
	var sTable, sTableBody;
	
	// Obtain the information for the items included in the transaction
	if (itmTable=$("destRowsTable")) {
		itmTableBody=itmTable.getElementsByTagName("tbody")[0];
		itmRows=itmTableBody.getElementsByTagName("tr");
		
		// This is the summary table
		sTable=$("summaryTable");
		sTableBody=sTable.getElementsByTagName("tbody")[0];
		sTableBody.innerHTML = "";

		for (i=1;i<itmRows.length;i++) {
			rowid = itmRows[i].id.replace("destRow","");
			itemName=$("destProductName"+rowid).value;
			itemQty=$("destQty"+rowid).value;
			itemPrice=$("destPrice"+rowid).value;
			price = itemPrice*itemQty;
			discount = price*totalDiscount;
			totalPrice+=price;
			sTableBody.innerHTML += '<tr class="wardlistrow'+(i%2+1)+'">'+
				'<td align="right">'+itemQty+'&nbsp;</td>'+
				'<td align="left">'+itemName+'</td>'+
				'<td align="right">'+price.toFixed(2)+'</td>'+
				'<td align="right">'+discount.toFixed(2)+'</td>'+
				'<td align="right">'+(price-discount).toFixed(2)+'</td></tr>';
		}
		
		
		totalPrice=totalPrice-0;
		var totalNet = totalPrice-totalPrice*totalDiscount,
				totalDiscounted=totalPrice*totalDiscount;
		sTableBody.innerHTML += '<tr class="reg_list_titlebar" style="font-weight:bold">'+
				'<td style="border-top:2px solid #000055">&nbsp;</td>'+
				'<td style="border-top:2px solid #000055">&nbsp;T O T A L</td>'+
				'<td style="border-top:2px solid #000055" align="right">'+totalPrice.toFixed(2)+'</td>'+
				'<td style="border-top:2px solid #000055" align="right">'+totalDiscounted.toFixed(2)+'</td>'+
				'<td style="border-top:2px solid #000055" align="right">'+totalNet.toFixed(2)+'</td></tr>';
	}	
}

/* END: DISCOUNT JAVASCRIPT */
