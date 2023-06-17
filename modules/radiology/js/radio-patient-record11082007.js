		/*	
			This will trim the string i.e. removes leading,
			trailing and (OPTIONAL) in-between whitespaces of a string 
			input: objct, object
					allow_in_between_spaces, boolean
			output: object (string) value is trimmed
		*/
	function trimString(objct,allow_in_between_spaces){
	//	alert("inside frunction trimString: objct = '"+objct+"'");
		objct.value.replace(/^\s+|\s+$/g,"");   //Removes ONLY leading and trailing whitespaces
		if (allow_in_between_spaces)
			objct.value = objct.value.replace(/\s+/g," ");   //ONLY a single whitespace appears in between tokens/words 			
		else
			objct.value = objct.value.replace(/\s+/g,"");   //Removes ONLY in-between whitespaces
	}

	var seg_validTime=false;
	function setFormatTime(thisTime,AMPM){
	//	var time = $('time_text_d');
		var stime = thisTime.value;
		var hour, minute;
		var ftime ="";
		var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
		var f2 = /^[0-9]\:[0-5][0-9]$/;
		var jtime = "";
		
		trimString(thisTime,false);
	
		if (thisTime.value==''){
			seg_validTime=false;
			return;
		}
		
		stime = stime.replace(':', '');
		
		if (stime.length == 3){
			hour = stime.substring(0,1);
			minute = stime.substring(1,3);
		} else if (stime.length == 4){
			hour = stime.substring(0,2);
			minute = stime.substring(2,4);
		}else{
			alert("Invalid time format.");
			thisTime.value = "";
			seg_validTime=false;
			thisTime.focus();
			return;
		}
		
		//jtime = hour + ":" + minute;
		//js_setTime(jtime);
		
		if (hour==0){
			 hour = 12;
			 $(AMPM).value = "A.M.";		
		}else	if((hour > 12)&&(hour < 24)){
			 hour -= 12;
			 $(AMPM).value = "P.M.";
		}
	
		ftime =  hour + ":" + minute;
		
		if(!ftime.match(f1) && !ftime.match(f2)){
			thisTime.value = "";
			alert("Invalid time format.");
			seg_validTime=false;   
			thisTime.focus();
		}else{
			thisTime.value = ftime;
			seg_validTime=true;   
		}
	}// end of function setFormatTime


function jsSetDoctorsOfDept(){
	var aDepartment_nr = $F('borrower_dept');
		
//	alert("jsSetDoctorsOfDept : aDepartment_nr ='"+aDepartment_nr+"'");

	if (aDepartment_nr != 0) {
		xajax_setDoctors(aDepartment_nr,0);	//get the list of ALL doctors under "aDepartment_nr" department
	} else{
		xajax_setDoctors(0,0);	//get the list of ALL doctors from ALL departments
	}	
//	alert("jsSetDoctorsOfDept : aDepartment_nr ='"+aDepartment_nr+"'");
}// end of function jsSetDoctorsOfDept

function jsSetDepartmentOfDoc(){
	var aPersonell_nr = $F('borrower_id');
		
//	alert("jsSetDepartmentOfDoc : aPersonell_nr ='"+aPersonell_nr+"'");

	if (aPersonell_nr != 0) {
		xajax_setDepartmentOfDoc(aPersonell_nr);
	}
}// end of function jsSetDepartmentOfDoc

	/*
	*	Clears the list of options [0, doctors; 1, departments]
	*	burn added : October 31, 2007
	*/
function ajxClearDocDeptOptions(status) {
	var optionsList;
	var el;

	if (status==0){
		el=$('borrower_id');
	}else{
		el=$('borrower_dept');
	}
	 
	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearDocDeptOptions */

	/*
	*	Adds an item in the list of options [0, doctors; 1, departments]
	*	burn added : October 31, 2007
	*/
function ajxAddDocDeptOption(status, text, value) {
	var grpEl;

	if (status==0){
		grpEl=$('borrower_id');
	}else{
		grpEl=$('borrower_dept');
	}
	
	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
}/* end of function ajxAddDocDeptOption */

function ajxSetDoctor(personell_nr) {
//	alert("ajxSetDoctor ; personell_nr = "+personell_nr);
	$('borrower_id').value = personell_nr;
}

	/*
	*	Sets the department
	*	input: dept_nr, the selected department number
	*				list, the list of the departments including the parent & sub-departments of the selected department
	*	burn added : October 31, 2007
	*/
function ajxSetDepartment(dept_nr,list) {
		var current_dept = $('borrower_dept').value;
		var array = list.split(",");
//		alert("ajxSetDepartment : current_dept = '"+current_dept+"' \nlist = '"+list+"' \narray.length = '"+array.length+"'");
		for (var x=0; x<array.length; x++){
//			alert("ajxSetDepartment : array["+x+"] = '"+array[x]+"'");
			if (array[x]==current_dept){
					//the selected current department is in the list
				dept_nr=current_dept;
				break;
			}		
		}
		$('borrower_dept').value = dept_nr;
}

function checkBorrowForm(mode){
	if (mode){
		//mode==1, update
		var answer = confirm("You are about to update the borrowing details. Do you want to continue?");
		//alert("answer = '"+answer+"'");
		if (answer){
			//continue with the update of the borrowing details
		}else{
			return false;
		}
	}
	if ($F('borrower_id')==0){
		alert("Please select a borrower first.");
		$('borrower_id').focus();
		return false;	
	}else if ($F('date_borrowed')==""){
		alert("Please indicate the date borrowed.");
		$('date_borrowed').focus();
		return false;	
	}else if ($F('time_borrowed')==""){
		alert("Please indicate the time borrowed.");
		$('time_borrowed').focus();
		return false;	
	}else if (($F('releaser_id')=="")||($F('releaser_id')==0)){
		alert("The user is unrecognizable or the user's session time has expired.\nPlease login again properly.");
		return false;	
	}
	return true;
}//end of function checkBorrowForm

function jsPrepareBorrowArray(){
	var time_borrowed_formatted=jsFormatTime('borrowed');
	var time_returned_formatted=jsFormatTime('returned');
	var msg="$F('batchNo') = '"+$F('batchNo')+"' \n"+
				"$F('borrower_id') = '"+$F('borrower_id')+"' \n"+
				"$F('date_borrowed') = '"+$F('date_borrowed')+"' \n"+
				"$F('time_borrowed') = '"+$F('time_borrowed')+"' \n"+
				"time_borrowed_formatted = '"+time_borrowed_formatted+"' \n"+
				"$F('releaser_id') = '"+$F('releaser_id')+"' \n"+
				"$F('releaser_fullname') = '"+$F('releaser_fullname')+"' \n"+
				"$F('date_returned') = '"+$F('date_returned')+"' \n"+
				"$F('time_returned') = '"+$F('time_returned')+"' \n"+
				"time_returned_formatted = '"+time_returned_formatted+"' \n"+
				"$F('receiver_id') = '"+$F('receiver_id')+"' \n"+
				"$F('receiver_fullname') = '"+$F('receiver_fullname')+"' \n"+
				"$F('remarks') = '"+$F('remarks')+"' \n";
alert("jsPrepareBorrowArray :: \n"+msg);

	var borrowArray=new Array();
		borrowArray['borrow_nr'] = $F('borrow_nr');
		borrowArray['batch_nr'] = $F('batchNo');
		borrowArray['borrower_id'] = $F('borrower_id');
		borrowArray['date_borrowed'] = $F('date_borrowed');
		borrowArray['time_borrowed'] = time_borrowed_formatted;
		borrowArray['releaser_id'] = $F('releaser_id');
		borrowArray['releaser_fullname'] = $F('releaser_fullname');
		borrowArray['remarks'] = $F('remarks');	

		borrowArray['date_returned'] = $F('date_returned');
		borrowArray['time_returned'] = time_returned_formatted;
		borrowArray['receiver_id'] = $F('receiver_id');
		borrowArray['receiver_fullname'] = $F('receiver_fullname');

msg="jsPrepareBorrowArray :: \nborrowArray : "+borrowArray+" \n"+
		"borrowArray.length='"+borrowArray.length+"' \n"+
		"$('batchNo').name ='"+$('batchNo').name+"' \n";
alert("jsPrepareBorrowArray :: \n"+msg);
	return borrowArray;
}

function jsSaveBorrow(){
//	createBorrowEntry($batch_nr='', $borrower_id=0, $date_borrowed='', $time_borrowed='', 
//									 $releaser_id=0, $releaser_fullname='', $remarks=''){

	var borrowArray=new Array();
	borrowArray=jsPrepareBorrowArray();

msg="borrowArray : "+borrowArray+" \n"+
		"borrowArray.length='"+borrowArray.length+"' \n"+
		"$('batchNo').name ='"+$('batchNo').name+"' \n";
alert("jsSaveBorrow :: \n"+msg);
	xajax_saveRadioBorrow(borrowArray);
}//end of function jsSaveBorrow

function jsUpdateBorrow(){
	var borrowArray=new Array();
	borrowArray=jsPrepareBorrowArray();
//		borrowArray['borrow_nr'] = $F('borrow_nr');
msg="borrowArray : "+borrowArray+" \n"+
		"borrowArray.length='"+borrowArray.length+"' \n"+
		"$('borrow_nr').name ='"+$('borrow_nr').name+"' \n";
alert("jsUpdateBorrow :: \n"+msg);
	xajax_updateRadioBorrow(borrowArray);
}//end of function jsUpdateBorrow

function checkReturnForm(){
	var dateTimeBorrowed = $F('date_borrowed')+" "+jsFormatTime('borrowed');
	var dateTimeReturned = $F('date_returned')+" "+jsFormatTime('returned');
/*
	var dateBool = Date.parse($F('date_borrowed')) > Date.parse($F('date_returned'));
	var dateTimeBool = Date.parse(dateTimeBorrowed) > Date.parse(dateTimeReturned);
msg="dateTimeBorrowed = '"+dateTimeBorrowed+"' \n"+
		"dateTimeReturned = '"+dateTimeReturned+"' \n"+
		"Date.parse($F('date_borrowed')) = '"+ Date.parse($F('date_borrowed'))+"' \n"+
		"Date.parse(dateTimeBorrowed)    = '"+ Date.parse(dateTimeBorrowed)+"' \n"+
		"Date.parse($F('date_returned'))  = '"+ Date.parse($F('date_returned'))+"' \n"+
		"Date.parse(dateTimeReturned)    = '"+ Date.parse(dateTimeReturned)+"' \n"+
		"dateBool = '"+dateBool+"' \n"+
		"dateTimeBool = '"+dateTimeBool+"' \n";
alert("checkReturnForm :: \n"+msg);
return false;
*/
	if ($F('date_returned')==""){
		alert("Please indicate the date borrowed.");
		$('date_returned').focus();
		return false;	
	}else if ($F('time_returned')==""){
		alert("Please indicate the time borrowed.");
		$('time_returned').focus();
		return false;	
	}else if (Date.parse(dateTimeBorrowed) > Date.parse(dateTimeReturned)) {
		alert("Date-Time Borrowed cannot be after Date-Time Returned!")
		$('date_returned').select();
		return false;
	}else if (($F('receiver_id')=="")||($F('receiver_id')==0)){
		alert("The user is unrecognizable or the user's session time has expired.\nPlease login again properly.");
		return false;	
	}
	var msg="You are about to update the Returning Details ONLY. \n"+
				" Warning: Editing an entry data is an irrevocable process.  \n"+
				"          Once you click on the Return button, the entry will be locked from further editing. \n"+
				" Do you want to continue?";
	var answer = confirm(msg);
		//alert("answer = '"+answer+"'");
	if (answer){
		return true;
	}else{
		return false;
	}
}//end of function checkReturnForm

function jsSaveReturn(){
	var borrowArray=new Array();
	borrowArray=jsPrepareBorrowArray();
		borrowArray['borrow_nr'] = $F('borrow_nr');
msg="borrowArray : "+borrowArray+" \n"+
		"borrowArray.length='"+borrowArray.length+"' \n"+
		"$('borrow_nr').name ='"+$('borrow_nr').name+"' \n";
alert("jsSaveReturn :: \n"+msg);
	xajax_updateRadioReturn(borrowArray);
}//end of function jsSaveReturn

	/*
	*	Sets the borrow number for this transaction
	*		and the mode to 'borrowed'
	*	created: burn November 7, 2007
	*/
function ajxSetBorrowNr(borrow_nr){
alert("ajxSetBorrowNr :: borrow_nr ='"+borrow_nr+"'");
	$('borrow_nr').value = borrow_nr;
	$('mode').value = 'borrowed';
	$('releaser_id_new').value=$F('releaser_id');
	$('releaser_fullname_new').value = $F('releaser_fullname');
}//enc of function ajxSetBorrowNr
	/*
	*	input: string, 'borrowed' or 'returned'
	*	return: string, time in 24-hour format (hh:mm:00)
	*	burn created: November 5, 2007
	*/
function jsFormatTime(id){

	trimString($('time_'+id),false);
	var atime = $F('time_'+id);

	if (atime!=''){
		var colonIndex = atime.indexOf(":");
		var hour = atime.substring(0,colonIndex);
		var minute = atime.substring(colonIndex+1);
		if ($F('selAMPM_'+id)=='P.M.'){
			if (parseInt(hour)<12)
				hour = parseInt(hour)+12;
			atime = hour+":"+minute+":00";
		}else{
			if (hour=="12") //12:?? AM
				hour = "00";
			atime = hour+":"+minute+":00";		
		}
	}
	return atime;
}

	/*
	*	Sets the buttons and some rows
	*	burn created : October 31, 2007
	*/
function preset(){
//alert("preset :: $F('mode') ='"+$F('mode')+"'");
//alert("$('borrow-table').innerHTML : \n"+$('borrow-table').innerHTML);
var msg= "$('btnBorrow') = '"+$('btnBorrow')+"' \n"+
			"document.getElementById('btnBorrow')='"+document.getElementById('btnBorrow')+"' \n"+
			"$('btnUpdateBorrow') = '"+$('btnUpdateBorrow')+"' \n"+
			"document.getElementById('btnUpdateBorrow')='"+document.getElementById('btnUpdateBorrow')+"' \n"+
			"$('btnReturn') = '"+$('btnReturn')+"' \n"+
			"document.getElementById('btnReturn')='"+document.getElementById('btnReturn')+"' \n";
	msg="$F('mode')='"+$F('mode')+"'";
//alert("preset :: "+msg);
	if ($F('mode')=='save'){
			//save mode of borrow entry

		$('btnBorrow').style.display='';
		$('btnUpdateBorrow').style.display='none';
		$('btnReturn').style.display='none';
		$('btnUpdateReturn').style.display='none';
		$('btnDone').style.display='none';

		$('headerReturned').style.display='none';
		$('dateReturned').style.display='none';
		$('timeReturned').style.display='none';

		$('nextFilmReleaser').style.display='none';
		$('filmReceiver').style.display='none';
		$('nextFilmReceiver').style.display='none';
//	}else if ($F('mode')=='update_borrow'){
	}else if ($F('mode')=='borrowed'){
			//update mode of borrow entry
		$('btnBorrow').style.display='none';
		$('btnUpdateBorrow').style.display='';
		$('btnReturn').style.display='';
		$('btnUpdateReturn').style.display='none';
		$('btnDone').style.display='none';

		$('headerReturned').style.display='';
		$('dateReturned').style.display='';
		$('timeReturned').style.display='';

		if ($F('releaser_id_new')==$F('releaser_id'))
			$('nextFilmReleaser').style.display='none';
		else
			$('nextFilmReleaser').style.display='';
		$('filmReceiver').style.display='';
		$('nextFilmReceiver').style.display='none';
	}else if ($F('mode')=='save_return'){
			//save mode of return entry
		$('borrower_id').disabled=true;
		$('borrower_dept').disabled=true;
		$('date_borrowed').disabled=true;
		$('date_borrowed_trigger').disabled=true;
		$('time_borrowed').disabled=true;
		$('selAMPM_borrowed').disabled=true;

		$('btnBorrow').style.display='none';
		$('btnUpdateBorrow').style.display='none';
		$('btnReturn').style.display='';
		$('btnUpdateReturn').style.display='none';
		$('btnDone').style.display='';

		$('headerReturned').style.display='';
		$('dateReturned').style.display='';
		$('timeReturned').style.display='';

		$('nextFilmReleaser').style.display='none';
		$('filmReceiver').style.display='';
		$('nextFilmReceiver').style.display='none';
	}else if ($F('mode')=='update_return'){
			//update mode of return entry
		$('borrower_id').disabled=true;
		$('borrower_dept').disabled=true;
		$('date_borrowed').disabled=true;
		$('date_borrowed_trigger').disabled=true;
		$('time_borrowed').disabled=true;
		$('selAMPM_borrowed').disabled=true;
		
		$('btnBorrow').style.display='none';
		$('btnUpdateBorrow').style.display='none';
		$('btnReturn').style.display='none';
		$('btnUpdateReturn').style.display='';
		$('btnDone').style.display='';
		
		$('nextFilmReleaser').style.display='none';
		$('filmReceiver').style.display='';
		$('nextFilmReceiver').style.display='';
	}

}//end of function preset








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
		
		detailsImg ='onclick="return overlib(OLiframeContent(\''+rpath+'modules/radiology/seg-radio-patient.php?sid='+sid+'&lang='+lang+'&pid='+pid+'&rid='+rid+'\', 900, 650, \'fradio-list\', 1, \'auto\'), ' +
					'WIDTH, 900, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, CLOSETEXT, \'<img src=../../images/close.gif border=0>\', '+
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


