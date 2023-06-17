var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;    // success
		}
		else return false;    // fail
	}
	else return false;    // fail
}

function endAJAXList(listID) {
	var listEL = $(listID);
	if (listEL) {
		//$("ajax-loading").style.display = "none";
		$("GuarantorList-body").style.display = "";
		searchEL.style.color = "";
	}
}

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec=(parseInt(pageno)*pagen)+1;
	totalRows=total;

	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;

	if (parseInt(total)==0)
	{
		$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	}
	else if(parseInt(total)>0)
	{
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

		$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
		$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
		$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
		$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	}
	else
	{
		 $("pageShow").innerHTML = '<span>Showing 0 out of 0 record(s).</span>';
	}
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch(0);
			document.getElementById('pagekey').value=0;
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch(parseInt(currentPage)-1);
			document.getElementById('pagekey').value=currentPage-1;
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch(parseInt(currentPage)+1);
			document.getElementById('pagekey').value=parseInt(currentPage)+1;
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch(parseInt(lastPage));
			document.getElementById('pagekey').value=parseInt(lastPage);
		break;
	}
}

function addslashes(str) {
	str=str.replace("'","\\'");
	return str;
}

function checkEnter(e){
	//alert('e = '+e);
	var characterCode; //literal character code will be stored in this variable
	if(e && e.which) { //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	} else {
		//e = event;
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13) { //if generated character code is equal to ascii 13 (if enter key)
		startAJAXSearch(0);
	} else {
		return true;
	}
}

function tooltip(text)
{
	return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
		TEXTPADDING,4, BORDER,0,
		VAUTO, WRAP);
}

function startAJAXSearch(page, mode)
{
	if(mode=='edit')
	{
		var searchID=$('new_walkin_name').value;
	}
	else
	{
		var searchID=$('walkin_name').value;
	}

	//alert('search id='+searchID);
	if (page)
		document.getElementById('pagekey').value = page;
	else
		document.getElementById('pagekey').value = '0';

	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	AJAXTimerID = setTimeout("xajax_searchPharmaWalkin('"+searchID+"',"+page+")",50);
}

function viewPharmaWalkinList(tableID, id, name, address, date_reg)
{
	var list=$(tableID), dRows, dBody, rowSrc;
	var i;
	var classified, mode, editlink;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
		if (id) {
			alt = (dRows.length%2)+1;
			 text1="Edit Account";
			 text2="Delete Account";
			 text3="Reports";
			 rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(id)+'" value="'+id+'">'+
					'<td align="center"><span style="font:bold 11px Arial;color:#660000">'+id+'</span></td>'+
					'<td align="left">'+name+'</td>'+
					'<td align="left">'+address+'</td>'+
					'<td align="center">'+date_reg+'</td>'+
					'<td align="center">'+
					'<img class="link" border="0" align="absmiddle" src="../../images/cashier_edit.gif" onclick="editWalkin(\''+id+'\'); return false;" onmouseover="tooltip(\''+text1+'\');" onMouseout="return nd();"/> '+
					'<img class="link" border="0" align="absmiddle" src="../../images/cashier_delete.gif" onclick="deleteWalkin(\''+id+'\'); return false;" onmouseover="tooltip(\''+text2+'\');" onMouseout="return nd();"/> '+
					'<img class="link" border="0" align="absmiddle" src="../../images/cashier_reports.gif" onclick="openReport(\''+id+'\'); return false;" onmouseover="tooltip(\''+text3+'\');" onMouseout="return nd();"/>'+
					'</td>'+
				'</tr>';
		}
		else {
				rowSrc = '<tr><td colspan="10" style="">No guarantor selected yet..</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

function newWalkin()
{
	return overlib(
		OLiframeContent('seg-pharma-manage-walkin-tray.php?target=add', 500, 300, 'fOrderTray', 0, 'auto'),
			WIDTH,440, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
			CAPTION,'New walk-in Patient',
			MIDX,0, MIDY,0,
			STATUS,'New walk-in Patient');
}

function startAJAXAdd()
{
	var lastname = $('new_walkin_lastname').value;
	var firstname = $('new_walkin_firstname').value;
	var address = $('new_walkin_address').value;
	var birthdate = $('new_walkin_birthdate').value;
	var pid = $('new_walkin_pid').value;
	var gender = "";
	for(i=0;i<document.suchform.new_walkin_sex.length;i++)
	{
		if(document.suchform.new_walkin_sex[i].checked)
		{
			gender=document.suchform.new_walkin_sex[i].value;
		}
	}
	//alert('name: '+name+' title: '+title);
	xajax_saveNewAccount(pid,lastname,firstname,gender,address,birthdate);
}

function refreshFrame(outputResponse)
{
	alert(outputResponse);
	$('new_walkin_lastname').value="";
	$('new_walkin_firstname').value="";
	$('new_walkin_sex').value="";
	$('new_walkin_address').value="";
	$('new_walkin_birthdate').value="";
	$('new_walkin_pid').value="";
}

function deleteWalkin(delID)
{
	var reply = confirm("Delete this walk-in data?");
	if(reply)
	{
		xajax_deleteWalkin(delID);
		startAJAXSearch(0,'delete');
	}
}

function editWalkin(editID)
{
	return overlib(
		OLiframeContent('seg-pharma-manage-walkin-tray.php?target=edit&editID='+editID, 500, 300, 'fOrderTray', 0, 'no'),
		WIDTH,440, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
		CAPTIONPADDING,2,
		CAPTION,'Edit walk-in account',
		MIDX,0, MIDY,0,
		STATUS,'Edit walk-in account');
}

function startAJAXEdit(id)
{
	var lastname = $('new_walkin_lastname').value;
	var firstname = $('new_walkin_firstname').value;
	var address = $('new_walkin_address').value;
	var birthdate = $('new_walkin_birthdate').value;
	var gender = "";
	for(i=0;i<document.suchform.new_walkin_sex.length;i++)
	{
		if(document.suchform.new_walkin_sex[i].checked)
		{
			gender=document.suchform.new_walkin_sex[i].value;
		}
	}
	xajax_saveEditAccount(id,lastname,firstname,gender,address,birthdate);
}

function getPID()
{
	xajax_getPID();
}

function setPID(new_pid)
{
	$('new_walkin_pid').value = new_pid;
}

function openReport(id)
{
	window.open("seg-pharma-report-walkin-issuance.php?mode=walkin&pid="+id,"","width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
}