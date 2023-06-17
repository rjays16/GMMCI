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
	
	var relationship = prompt("Relationship to the holder.","");
	//alert(relationship);
	if (relationship){
		var id = $('id'+id).innerHTML;
		var lname = $('lname'+id).innerHTML;
		var fname = $('fname'+id).innerHTML;
		var mname = $('mname'+id).innerHTML;
		
		if (mname)
			mname = mname.substring(0,1)+".";
		
		details.id= $('id'+id).innerHTML;
		details.name= lname+", "+fname+ " " + mname;
		details.relationship= relationship;
		details.bdate= $('bdate'+id).innerHTML;
		details.age = $('age'+id).innerHTML;
		
		details.status = $('status'+id).value;
		details.sex = $('sex'+id).value;
		var list = window.parent.document.getElementById('dep-list');
		result = window.parent.appendDependents(list,details);
		
	}else{
		if(relationship==""){	
			alert('Please the relationship to the holder.');	
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

function addPerson(listID, details) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
   
	var id=details.id, 
			lname=details.lname, 
			fname=details.fname,
			mname=details.mname,
			dob=details.dob, 
			sex=details.sex, 
			addr=details.addr, 
			zip=details.zip, 
			status=details.status,
			age=details.age;
			
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
		// get the last row id and extract the current row no.
		if (id) {
           // alert(orig_discountid);
			if (sex=='m')
				sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
			else if (sex=='f')
				sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
			else
				sexImg = '';			
				
				rowSrc = '<tr>'+
									'<td width="8%">'+
										'<span id="id'+id+'" style="color:#660000">'+id+'</span>'+
										'<input id="status'+id+'" type="hidden" value="'+status+'"/>'+	
										'<input id="sex'+id+'" type="hidden" value="'+sex+'"/>'+	
									'</td>'+
									'<td width="4%">'+sexImg+'</td>'+
									'<td width="*"><span id="lname'+id+'">'+lname.toUpperCase()+'</span></td>'+
									'<td width="15%"><span id="fname'+id+'">'+fname.toUpperCase()+'</span></td>'+
									'<td width="15%"><span id="mname'+id+'">'+mname.toUpperCase()+'</span></td>'+
									'<td width="10%"><span id="bdate'+id+'">'+dob+'</span></td>'+
									'<td width="10%"><span id="age'+id+'">'+age+'</span></td>'+
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
