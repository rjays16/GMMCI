var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

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
	
	if (parseInt(total)==0)
		$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	else
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	
	/*
	$("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	*/
	$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0,0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1,0);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1,0);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage,0);
		break;
	}
}

function prepareSelect(id, from, witholding_tax) {
	var firm_name = $('firmname'+id).innerHTML;
	var firm_addr = $('address'+id).innerHTML;	
	
	window.parent.$('hcare_id').value = id;
	window.parent.$('hcname').value = firm_name;
	window.parent.$('hcaddress').value = firm_addr;
	//window.parent.$('w_tax').value = witholding_tax;	
	
    if (!from || (from == 0)) {
    window.parent.$('tbl_transmit_details_hdr_row1').style.display = "none";
	window.parent.$('tbl_transmit_details_hdr_row2').style.display = "";

    }
    else {
        window.parent.$('tbl_claim_details_hdr_row1').style.display = "none";
        window.parent.$('tbl_claim_details_hdr_row2').style.display = "";            
		
    } 
    if(window.parent.$('hcare_id').value == 18){
				window.parent.$('tbl_report2').style.display = "";	
	}	       
	else{
		window.parent.$('tbl_report1').style.display = "none";
	}
	window.parent.jsInitDetailsSection();
	window.parent.assignHCareID(id);
	window.parent.cClick();			
}

function addInsurance(listID, id, firmId, firmName, phone, fax, mail, witholding_tax) {
	var list=$(listID), dRows, dBody, rowSrc, i;
	
	if (list) {
	   dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
        var frmpg = ($('from').value != 1) ? 0 : 1;
        
		if (id) {
			alt = (dRows.length%2)+1;			

//			rowSrc = '<tr>'+
			rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
							'<td width="15%">'+firmId+'</td>'+
				            '<td width="30%"><span id="firmname'+id+'">'+firmName+'</span></td>'+
             				'<td width="15%">'+phone+'</td>'+
            				'<td width="15%">'+fax+'</td>'+
							'<td width="22%"><span id="address'+id+'">'+mail+'</span></td>'+
							'<td width="3%" align="center"><input type="button" value="Select" style="color:#000066; font-weight:bold; padding:0px 2px; cursor:pointer" '+
									'onclick="prepareSelect(\''+id+'\', \''+frmpg+'\', \''+witholding_tax+'\')" /></td>'+
					'</tr>';				
		}
		else {
			rowSrc = '<tr><td colspan="6">No insurance firm available at this time...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

