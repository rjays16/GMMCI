function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
	//added by VAN 04-12-08
	refreshWindow();
}

function js_ClickAddWard(){
	var rpath = $('rpath').value;
	var sid = $('sid').value;
	
	return overlib(OLiframeContent(''+rpath+'modules/nursing/nursing-station-new.php?sid='+sid+'&lang=en&popUp=1&mw=1&station=&name=', 750, 500, 'fnursing-new', 1, 'auto'),
					WIDTH, 750, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, CLOSETEXT, '<img src=../../images/close.gif border=0>', 
					CAPTIONPADDING, 4, CAPTION, 'Create New Ward', MIDX, 0, MIDY, 0, STATUS, 'Create New Ward');
	
}

function init(){
	YAHOO.util.Event.addListener("btnaddWard", "click", js_ClickAddWard);
}

function js_AddRow(nr, name, wardId, desp, rm_nr, tmpClosed, type){
	var dTable, dTbody, dRows, srcRow;
	var href, img, url, status, wtype;
	
	//added by VAN 04-11-08
	var key = document.getElementById('key').value;
	var pagekey = document.getElementById('pagekey').value;
	
	//alert("key, page = "+document.getElementById('key').value+" - "+document.getElementById('pagekey').value);
	
	if(dTable = $('wardList')){	
		dTbody = dTable.getElementsByTagName('tbody')[0];
		
		url = $('url_append').value;
		if(nr){
			img = '<img src="../../gui/img/common/default/bul_arrowgrnsm.gif">';
			//href = 'nursing-station-info.php'+url+'&mode=show&station='+name+'&ward_nr='+nr;
			//edited by VAN 04-11-08
			href = 'nursing-station-info.php'+url+'&mode=show&station='+name+'&ward_nr='+nr+'&key='+key+'&pagekey='+pagekey;
			
			if(tmpClosed!=0){
				status = '<font color="red">Temporarily closed</font>';	
				//alert("tmpClosed "+ tmpClosed + "status = "+ status);
			}else if(rm_nr == ''){
					status = '<a href="nursing-station-new-createbeds.php'+url+'&ward_nr='+nr+'">Create ward</a>'; 
			}else{
				if (rm_nr==1)	
					status= rm_nr+' Room';
				else if(rm_nr>1)
					status= rm_nr+' Rooms';
			}
			
			if (type==1)
				wtype = 'Charity';
			else
				wtype = 'Payward';
			
			srcRow = '<tr>'+
						'<td width="3%">'+img+'</td>'+
						'<td width="20%"><a href="'+href+'">'+name+'</a></td>'+
						'<td width="15%"><a href="'+href+'">'+wardId+'</a></td>'+
						'<td width="*">'+desp+'</td>'+
						'<td width="5%">'+wtype+'</td>'+
						'<td width="1%">&nbsp;</td>'+
						'<td width="15%"><span>'+status+'</span></td>'+
					'</tr>'	;
		
		}
		dTbody.innerHTML += srcRow;
	}
	
}//end of function js_AddRow()

function js_ClearRow(){
	
	
}

//added by VAN 04-09-08-------------------------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);	
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	
	//$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	if (parseInt(total)==0)
		$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	else
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

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

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("twardList").style.display = "";
		searchEL.style.color = "";
	}
}

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	if (searchEL.value)
		document.getElementById('key').value = searchEL.value;
	else
		document.getElementById('key').value = '*';
			
	document.getElementById('pagekey').value = page;
	
	keyword = searchEL.value.replace("'","^");
	
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("twardList").style.display = "none";
		//AJAXTimerID = setTimeout("xajax_PopulateRow('"+searchID+"','"+searchEL.value+"',"+page+")",50);
		AJAXTimerID = setTimeout("xajax_PopulateRow('"+searchID+"','"+keyword+"',"+page+")",50);
		lastSearch = searchEL.value;
	}
}

function startAJAXSearch2(keyword, page) {
	//alert('key, page = '+keyword+" - "+page);
	keyword = keyword.replace("'","^");
	
	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	//$("ajax-loading").style.display = "";
//	document.getElementById("ajax-loading").style.display = "";
//	$("twardList").style.display = "none";
	AJAXTimerID = setTimeout("xajax_PopulateRow('search','"+keyword+"',"+page+")",50);
	lastSearch = keyword;
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
//------------------------------------------------------------

//added by VAN 04-11-08
/*
function EditWardForm(id){
	return overlib(
         OLiframeContent('nursing-station-info-ward-edit.php?popUp=1', 700, 400, 'fWardTray', 1, 'auto'),
        							WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=../../images/close.gif border=0>',
						         CAPTIONPADDING,4, CAPTION,'Edit hospital ward',
						         MIDX,0, MIDY,0, 
						         STATUS,'Edit hospital ward');							
}

function EditRoomForm(id){
	return overlib(
         OLiframeContent('nursing-station-info-room-edit.php?popUp=1', 700, 400, 'fRoomTray', 1, 'auto'),
        							WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=../../images/close.gif border=0>',
						         CAPTIONPADDING,4, CAPTION,'Edit hospital ward room',
						         MIDX,0, MIDY,0, 
						         STATUS,'Edit hospital ward room');							
}
*/
function refreshWindow(){
	//alert('refresh = '+window.location.href);	
	window.location.href=window.location.href;
}

function EditWardForm(ward_nr){
	//alert('id = '+id);
	return overlib(
         OLiframeContent('nursing-station-new.php?popUp=1&ward_nr='+ward_nr, 700, 400, 'fWardTray', 1, 'auto'),
        							WIDTH,700, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=../../images/close.gif border=0 onClick="refreshWindow();">',
						         CAPTIONPADDING,4, CAPTION,'Edit hospital ward',
						         MIDX,0, MIDY,0, 
						         STATUS,'Edit hospital ward');							
}

//-------------------------------------------------