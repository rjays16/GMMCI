/*var AJAXTimerID=0; 
var lastSearch="";
*/
/*function startAJAXSearch(searchID) {
	var searchEL = $(searchID);
	var searchLastname = $('firstname-too').checked ? '1' : '0';
	*/
/*	
	if (searchEL && lastSearch != searchEL.value) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("person-list-body").style.display = "none";
		 AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchEL.value+"',"+searchLastname+")",500);
		lastSearch = searchEL.value;
	}
*/
//} //end of function startAJAXSearch

/*

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("person-list-body").style.display = "";
		searchEL.style.color = "";
	}
}

*/

function pSearchClose() {
//	alert("radio-request-list.js : pSearchClose : ");
	cClick();  //function in 'overlibmws.js'
/*
	var nr = $('encounter_nr').value;
	alert("pSearchClose : nr='"+nr+"'");
	if (nr) xajax_get_charity_discounts(nr);
*/
}

	function msgPopUp(msg){
		alert(msg);
	}


function deleteRefNo(refno){
//	alert("deleteRefNo : refno = '"+refno+"'");
	var answer = confirm("You are about to delete service request #"+refno+". Are you sure?");
		//alert("answer = '"+answer+"'");
	if (answer){
//		alert("deleteRefNo : answer = '"+answer+"'");
		xajax_deleteRadioServiceRequest(refno);
	}
}

//function jsListRows(sub_dept_nr,No,batchNo,dateRequest,sub_dept_name, pid, sex, name, srvCode, srvDesp, priority){
function jsListRows(sub_dept_nr,No,refNo,rid,name,sex,dateRequest,priority,hasPaid){
	var listTable, dTBody, dRows, srcRows, sid, lang, radio_finding_link, gender, detailsImg, delitemImg, paiditemImg;
	var rpath,url, xpriority, style, onMouseOver;
//alert("jsListRows:: hasPaid='"+hasPaid+"'");
//	alert("jsListRows:: sub_dept_nr="+ sub_dept_nr+ "\n No="+No+"\n refno="+refNo+"\n dateRequest="+dateRequest);
	    sid = document.getElementById('sid').value;
		lang = document.getElementById('lang').value;
		rpath =document.getElementById('rpath').value;
	
	if(listTable= document.getElementById('Ttab'+ sub_dept_nr)){
		dTBody = listTable.getElementsByTagName("tbody")[0];
		
		// /modules/nursing/nursing-station-radio-request-new.php?sid=81da20b6cc828607dc48581a541fb9eb&lang=ennorezie=1&use_origin=lab&target=radio_test&dept_nr=&checkintern=1
		//radio_finding_link :: note - replace with edit request link 
				
		switch(sex){
				case 'f': gender = '<img src="../../gui/img/common/default/spf.gif" >'; break;
				case 'm': gender = '<img src="../../gui/img/common/default/spm.gif">'; break;
				default: gender = '&nbsp;'; break;
			}
		
		delitemImg = '<img src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onClick="deleteRefNo('+refNo+');">';
		paiditemImg = '<img src="../../images/btn_paiditem.gif" border="0" onClick="">';
				//hisdmc/hisdmc/modules/nursing/nursing-station-radio-request-new.php?sid='+sid+'&lang'+lang+
		detailsImg ='onclick="return overlib(OLiframeContent(\'seg-radio-request-new.php?sid='+sid+'&lang='+lang+'&popUp='+1+'&refno='+refNo+'\', 850, 450, \'fradio-list\', 1, \'auto\'), ' +
					'WIDTH, 850, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, CLOSETEXT, \'<img src=../../images/close.gif border=0>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Radio request\', MIDX, 0, MIDY, 0, STATUS, \'Radio request\');">';
		
		if(priority==1){
			xpriority = 'Urgent';
			style='font:bold 11px Arial; color:red';
		}else{
			xpriority = 'Normal';
			style='font:bold 11px Arial; color:#003366';
		}
		
		//<a href="http://www.samisite.com" onmouseover="return overlib('Type your comments here', CAPTION, 'Type your caption here');" onmouseout="return nd();" onfocus="this.blur()">Link statement here</a>
		//onMouseOver = 'return overlib(\''+srvDesp+'\',BORDER,0)';
		
		//'<span style="font:normal 11px Arial; color:#003366">'+srvDesp+'</span>'+
		//'<td onmouseover="'+onMouseOver+'" onmouseout="return nd();" onfocus="this.blur()">'+
		//				  		'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
		//				  		'<span style="font:bold 11px Arial">'+srvCode+'</span><br />'+
		//				  '</td>'+
		
			/*	burn added, October 11, 2007: 
			 *		If at least one service code request has been paid, the reference cannot be deleted NOR edited!
			 */
		if(refNo){
			srcRows = '<tr>'+
						  '<td align="center">'+No+'</td>'+	
						  '<td align="center">'+refNo+'</td>'+
						  '<td align="center">'+rid+'</td>'+
						  '<td align="left">'+gender+'&nbsp;&nbsp;'+name+'</td>'+
						  '<td align="center">'+dateRequest+'</td>'+
						  '<td align="center"><span style="'+style+'">'+xpriority+'</span></td>'+
						  '<td align="center"><a href="javascript:void(0);" '+detailsImg+'<img src="../../images/edit.gif" border="0"></a></td>'+
						  '<td align="center">'+((hasPaid==1)? paiditemImg:delitemImg)+'</td>'+
					  '</tr>';
			
//			alert("jsListRows:: hasPaid='"+hasPaid+"' srcRows ="+srcRows);		  
		}else{
			srcRows = '<tr><td colspan="8"  style="">No requests available at this time...</td></tr>';
		}
		dTBody.innerHTML += srcRows;
	}
	
	
}//end of function jsListRows

function jsOnClick(){
	var tab;
	var key, pgx, thisfile, rpath, sub_dept_nr, mode;
	
	try{
		tab = dojo.widget.byId('rlistContainer').selectedChild;
	}catch(evt){
		tab = 'tab0';
	}
	mode = document.getElementById('smode').value;
	rpath = document.getElementById('rpath').value;
	setPgx(0); //resets to the first page every time a tab is clicked
	pgx = document.getElementById('pgx').value;
	key = document.getElementById('skey').value;
	thisfile = document.getElementById('thisfile').value;
	oitem = 'create_dt';
	odir = 'ASC';
	sub_dept_nr = tab.substr(3);

//alert("jsOnClick : tab = '"+tab+"' \nkey='"+key+"' \n$F('search-refno')='"+$F('search-refno')+"'");

	//alert("JS: tab ="+'T'+tab + "\n TBody="+'TBody'+tab+ "\n searchkey="+ key+ "\n sub_dept_nr ="+ sub_dept_nr+"\n pgx="+ pgx+ "\n thisfile ="+  thisfile + "\n rpath ="+ rpath+ "\n mode=" + mode +"\n oitem="+oitem+ "\n odir="+odir);
    xajax_PopulateRadioRequest('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode, oitem, odir);
    
} // end of  function jsOnClick 
//onButtonClick /onSelectChild

function evtOnClick(){
	dojo.event.connect(dojo.widget.byId('rlistContainer').tablist, "onSelectChild", "jsOnClick");
}

function jsSortHandler(items, oitem, dir, sub_dept_nr){
	var tab = dojo.widget.byId('rlistContainer').selectedChild;
	var key, pgx, thisfile, rpath, mode;
	
	setOItem(items);
	setODir(dir);
	
	mode = document.getElementById('smode').value;
	rpath = document.getElementById('rpath').value;
	pgx = document.getElementById('pgx').value;
	key = document.getElementById('skey').value;
	thisfile = document.getElementById('thisfile').value;
	oitem = $('oitem').value;
	odir = $('odir').value;
	
	//alert("jsSortHandler: Tab="+tab+"\n sub_dept_nr ="+ sub_dept_nr+"\n pgx="+ pgx+ "\n thisfile ="+  thisfile + "\n rpath ="+ rpath+ "\n mode=" + mode +"\n oitem="+oitem+ "\n odir="+odir);
	xajax_PopulateRadioRequest('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode, oitem, odir);
	
} // end of function jsSortHandler

function setTotalCount(val){
	$('totalcount').value = val;
}

function setPgx(val){
	$('pgx').value = val;
}

function setOItem(val){
	$('oitem').value = val;
}

function setODir(val){
	$('odir').value = val;
}

function trimStringSearchMask(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g, "");
	objct.value = objct.value.replace(/\s+/g, " ");
}

