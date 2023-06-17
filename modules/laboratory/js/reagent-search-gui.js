var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function display(str) {
	document.write(str);
}

function prepareSelect(id) {
	var details = new Object();
	
	var amount = prompt("Amount will be used (in mL).","");
	//alert(relationship);
	if (amount){
		details.reagent_code= $('id'+id).innerHTML;
		details.reagent_name= $('name'+id).innerHTML;
		details.item_qty= amount;
		details.unit_id= 2;
		details.is_unitperpc= 1;
		
		var list = window.parent.document.getElementById('reagent-list');
		result = window.parent.appendReagents(list,details);
		
	}else{
		if(amount==""){	
			alert('Please enter the amount of reagents that the examination will be used .');	
			prepareSelect(id);
		}else
			return true;
	}
}

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);	
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	//$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s)</span>';
	if (parseInt(total))
		$("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>'
	else
		$("pageShow").innerHTML = ''
	$("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage);
		break;
	}
}

function addReagents(listID, details) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
  
	var id=details.reagent_code, 
			name=details.reagent_name, 
			other_name=details.other_name;
		
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
		// get the last row id and extract the current row no.
		if (id) {
           	rowSrc = '<tr>'+
									'<td width="8%">'+
										'<span id="id'+id+'" style="color:#660000">'+id+'</span>'+
									'</td>'+
									'<td width="*"><span id="name'+id+'">'+name.toUpperCase()+'</span></td>'+
									'<td width="15%"><span id="other_name'+id+'">'+other_name.toUpperCase()+'</span></td>'+
									'<td width="5%">'+
										'<input type="button" value="Select" style="color:#000066; font-weight:bold; padding:0px 2px" '+
											'onclick="prepareSelect(\''+id+'\')" '+
										'/>'+
									'</td>'+
									'<td><span>&nbsp;</span></td>'+
								'</tr>';
							
							
		}
		else {
			if (!details.error) details.error = 'No such person exists...';
			rowSrc = '<tr><td colspan="9" style="">'+details.error+'</td></tr>';
		}
		dBody.innerHTML += rowSrc;
		
	}
}