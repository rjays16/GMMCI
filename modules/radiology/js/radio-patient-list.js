function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
}

function msgPopUp(msg){
	alert(msg);
}

function jsListRows(sub_dept_nr,No,rid,pid,sex,lname,fname,birthdate,brgyName,munName){
//alert("jsListRows::");
	var listTable, dTBody, dRows, srcRows, sid, lang, radio_finding_link, gender, detailsImg, delitemImg, paiditemImg;
	var rpath,url, xpriority, style;
//alert("jsListRows:: sub_dept_nr="+ sub_dept_nr+ "\n No="+No+"\n rid="+rid+"\n birthdate="+birthdate);
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

		/*
		detailsImg ='onclick="return overlib(OLiframeContent(\''+rpath+'modules/radiology/seg-radio-patient.php?sid='+sid+'&lang='+lang+'&pid='+pid+'&rid='+rid+'\', 900, 650, \'fradio-list\', 1, \'auto\'), ' +
					'WIDTH, 900, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, CLOSETEXT, \'<img src=../../images/close.gif border=0>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Radiology Patient`s Record\', MIDX, 0, MIDY, 0, STATUS, \'Radiology Patient`s Record\');">';
	*/
	//480, 850
	detailsImg ='onclick="return overlib(OLiframeContent(\''+rpath+'modules/radiology/seg-radio-patient.php?sid='+sid+'&lang='+lang+'&pid='+pid+'&rid='+rid+'\', 850, 440, \'fradio-list\', 1, \'auto\'), ' +
					'WIDTH, 850, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Radiology Patient`s Record\', MIDX, 0, MIDY, 0, STATUS, \'Radiology Patient`s Record\');">';

//sub_dept_nr,No,rid,pid,sex,lname,fname,birthdate,brgyName,munName
//No RID PID Sex Lastname Firstname Birthdate Brgy Muni/City Records
		if(rid){
			srcRows = '<tr>'+
							'<td>'+No+'</td>'+
							'<td>'+rid+'</td>'+
							'<td align="right">'+pid+'</td>'+
							'<td align="center">'+gender+'</td>'+
							'<td>'+lname+'</td>'+
							'<td>'+fname+'</td>'+
							'<td align="right">'+birthdate+'</td>'+
							'<td>'+brgyName+'</td>'+
							'<td>'+munName+'</td>'+
							'<td><a href="javascript:void(0);" '+detailsImg+'<img src="../../images/edit.gif" border="0"></a></td>'+
						'</tr>';

//			alert("jsListRows:: hasPaid='"+hasPaid+"' srcRows ="+srcRows);
		}else{
			srcRows = '<tr><td colspan="10"  style="">No available list of radiology patients at this moment...</td></tr>';
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
	oitem = 'name_last';
	odir = 'ASC';
	sub_dept_nr = tab.substr(3);

//alert("jsOnClick : tab = '"+tab+"' \nkey='"+key+"'");

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
