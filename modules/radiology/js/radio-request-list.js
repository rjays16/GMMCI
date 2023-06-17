function pSearchClose() {
//	alert("radio-request-list.js : pSearchClose : ");
	cClick();  //function in 'overlibmws.js'
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
// edited by VAN 01-15-08
//function jsListRows(sub_dept_nr,No,refNo,rid,name,sex,dateRequest,priority,hasPaid){
function jsListRows(sub_dept_nr,No,refNo,rid,name,sex,dateRequest,priority, repeat,paid, or_no, enctype,location,pid, encounter_nr,age,bdate){
	var listTable, dTBody, dRows, srcRows, sid, lang, radio_finding_link, gender, detailsImg, delitemImg, paiditemImg;
	var rpath,url, xpriority, style, onMouseOver;
	
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
		
		// added by VAN 01-15-08
		repeatitemImg = '<img src="../../images/btn_repeat.gif" border="0" onClick="">';
		
				//hisdmc/hisdmc/modules/nursing/nursing-station-radio-request-new.php?sid='+sid+'&lang'+lang+
		detailsImg ='onclick="return overlib(OLiframeContent(\'seg-radio-request-new.php?sid='+sid+'&lang='+lang+'&popUp='+1+'&refno='+refNo+'&encounter_nr='+encounter_nr+'&pid='+pid+'\', 850, 450, \'fradio-list\', 1, \'auto\'), ' +
					'WIDTH, 850, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=ReloadWindow();>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Radiology request\', MIDX, 0, MIDY, 0, STATUS, \'Radiology request\');">';
		
		if(priority==1){
			xpriority = 'Urgent';
			style='font:bold 11px Arial; color:red';
		}else{
			xpriority = 'Normal';
			style='font:bold 11px Arial; color:#003366';
		}
		
		if(refNo){
			if (or_no!=''){
					//refnum = '<font color="BLUE">'+charge_type+'</font>';
					refnum = refNo;
					//or_no = charge_type;
			}else{
                
			    if (repeat==1)
				    refnum = refNo;
			    else if (paid!=0)
                    refnum = refNo;
                else
                    refnum = '<font color="#FF0000">Not Paid</font>';
			}
			
			srcRows = '<tr>'+
						  '<td align="center">'+No+'</td>'+	
						  '<td align="center">'+refnum+'</td>'+
						  '<td align="center">'+rid+'</td>'+
						  '<td align="left">'+gender+'&nbsp;&nbsp;'+name+'</td>'+
							'<td align="left">'+pid+'</td>'+
							'<td align="left">'+age+'</td>'+
							'<td align="left">'+bdate+'</td>'+
							'<td align="left" style="font-size:11px">'+enctype+'</td>'+
							'<td align="left" style="font-size:11px">'+location+'</td>'+
						  '<td align="center">'+dateRequest+'</td>'+
							'<td align="center" style="font-size:11px"><font color="#000066">'+or_no+'</font></td>'+
						  '<td align="center"><span style="'+style+'">'+xpriority+'</span></td>'+
						  '<td align="center"><a href="javascript:void(0);" '+detailsImg+'<img src="../../images/edit.gif" border="0"></a></td>'+
							'<td align="center">'+((repeat==1)?repeatitemImg:((paid==1)? paiditemImg:delitemImg))+'</td>'+
					  '</tr>';
			
			//alert("jsListRows:: hasPaid='"+hasPaid+"' srcRows ="+srcRows);		  
// edited by VAN 01-15-08 orig line 123 : '<td align="center">'+((hasPaid==1)? paiditemImg:delitemImg)+'</td>'+
		
		}else{
			srcRows = '<tr><td colspan="12"  style="">No requests available at this time...</td></tr>';
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
	//oitem = 'create_dt';
	oitem = 'is_urgent';
	//odir = 'ASC';
	odir = 'DESC';
	sub_dept_nr = tab.substr(3);
	
	//alert('key jsOnClick ='+key);

//alert("jsOnClick : tab = '"+tab+"' \nkey='"+key+"' \n$F('search-refno')='"+$F('search-refno')+"'");
   
	//alert("JS: tab ="+'T'+tab + "\n TBody="+'TBody'+tab+ "\n searchkey="+ key+ "\n sub_dept_nr ="+ sub_dept_nr+"\n pgx="+ pgx+ "\n thisfile ="+  thisfile + "\n rpath ="+ rpath+ "\n mode=" + mode +"\n oitem="+oitem+ "\n odir="+odir);
    xajax_PopulateRadioRequest('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode, oitem, odir, 1);
    
} // end of  function jsOnClick 
//onButtonClick /onSelectChild

//added by VAN 03-03-08
function checkEnter(e){
	//alert('e = '+e);	
	var characterCode; //literal character code will be stored in this variable

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		//e = event;
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		$('skey').value=$('search-refno').value; jsOnClick();
	}else{
		return true;
	}		
}

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
	
	//alert('key jsSortHandler ='+key);
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

//added by VAN 01-29-10
function isValidSearch(key) {

		if (typeof(key)=='undefined') return false;
		var s=key.toUpperCase();
		return (
						/^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
		);
}

function DisabledSearch(){
		var b=isValidSearch(document.getElementById('search-refno').value);
		document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
		document.getElementById("search-btn").disabled = !b;
}
//--------------------------

function ReloadWindow(){
		$('skey').value=$('search-refno').value;
		jsOnClick();
}