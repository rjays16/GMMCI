function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
}

function msgPopUp(msg){
	alert(msg);
}
/*
batch_nr,borrower_name,service_code,sub_dept_name,patient_name,releaser_name,date_borrowed
*/
//function jsListRows(sub_dept_nr,No,rid,pid,sex,lname,fname,birthdate,brgyName,munName){
function jsListRows(sub_dept_nr,No,rid,pid,batch_nr,borrower_id,borrower_name,service_code,sub_dept_name,
											patient_name,releaser_name,is_owner,date_borrowed){
//alert("jsListRows::");
	var listTable, dTBody, dRows, srcRows, detailsImg;
	var sid,lang,rpath,borrower_link;
	var bname;
//alert("jsListRows:: sub_dept_nr="+ sub_dept_nr+ "\n No="+No+"\n rid="+rid+"\n birthdate="+birthdate);
	sid = document.getElementById('sid').value;
	lang = document.getElementById('lang').value;
	rpath =document.getElementById('rpath').value;

	if(listTable= document.getElementById('Ttab'+ sub_dept_nr)){
		dTBody = listTable.getElementsByTagName("tbody")[0];
			//850, 440
		detailsImg ='onclick="return overlib(OLiframeContent(\''+rpath+'modules/radiology/seg-radio-patient.php?sid='+sid+'&lang='+lang+'&pid='+pid+'&rid='+rid+'\', 800, 440, \'fradio-list\', 1, \'auto\'), ' +
					'WIDTH, 800, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Radiology Patient`s Record\', MIDX, 0, MIDY, 0, STATUS, \'Radiology Patient`s Record\');">';

		borrower_link = rpath+'modules/radiology/seg-radio-borrower-record.php?sid='+sid+'&lang='+lang+'&borrower_id='+borrower_id;

		//if (borrower_name!='')
		if (is_owner==0)
			bname = '<a href="'+borrower_link+'">'+borrower_name+'</a>';
		else
			bname = borrower_name;
//sub_dept_nr,No,rid,pid,sex,lname,fname,birthdate,brgyName,munName
//No RID PID Sex Lastname Firstname Birthdate Brgy Muni/City Records

		if(batch_nr){
			srcRows = '<tr>'+
							'<td>'+No+'</td>'+
							'<td>'+batch_nr+'</td>'+
							'<td>'+bname+'</td>'+
							'<td>'+service_code+'</td>'+
							'<td>'+sub_dept_name+'</td>'+
							'<td>'+patient_name+'</td>'+
							'<td>'+releaser_name+'</td>'+
							'<td>'+date_borrowed+'</td>'+
							'<td><a href="javascript:void(0);" '+detailsImg+'<img src="../../images/edit.gif" border="0"></a></td>'+
						'</tr>';

//			alert("jsListRows:: hasPaid='"+hasPaid+"' srcRows ="+srcRows);
		}else{
			srcRows = '<tr><td colspan="9"  style="">No available list of radiology borrowers...</td></tr>';
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
//	oitem = 'name_last';
	oitem = 'borrow_nr';
	odir = 'ASC';
	sub_dept_nr = tab.substr(3);

//alert("jsOnClick : tab = '"+tab+"' \nkey='"+key+"'");

	//alert("JS: tab ="+'T'+tab + "\n TBody="+'TBody'+tab+ "\n searchkey="+ key+ "\n sub_dept_nr ="+ sub_dept_nr+"\n pgx="+ pgx+ "\n thisfile ="+  thisfile + "\n rpath ="+ rpath+ "\n mode=" + mode +"\n oitem="+oitem+ "\n odir="+odir);
		xajax_PopulateRadioBorrowerList('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode, oitem, odir);

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
	xajax_PopulateRadioBorrowerList('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode, oitem, odir);

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

//added by VAN 07-09-08
function viewUnreturned(){
	//alert("viewUnreturned");
	window.open("seg-radio-unreturned-pdf.php?showBrowser=1","viewPatientResult","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}
//----------------------