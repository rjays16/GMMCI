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
		var strTime=thisTime.value;
		var stime =strTime.substring(0,5);
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
//alert("ajxSetDoctor ; personell_nr = "+personell_nr);
	$('borrower_id').value = personell_nr;
	if (personell_nr==0){
		$('borrower_dept').disabled=true;
		$('borrower_id').disabled=true;
		$('borrower_self').checked = true;
	}else{
		$('borrower_self').checked = false;
		$('borrower_dept').disabled=false;
		$('borrower_id').disabled=false;
	}
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
		var answer = confirm("You are about to update the Borrowing/Releasing Form. Do you want to continue?");
		//alert("answer = '"+answer+"'");
		if (answer){
			//continue with the update of the borrowing details
		}else{
			return false;
		}
	}

	if ((!$('borrower_self').checked) &&($F('borrower_id')==0)){
		alert("Please select a borrower first.");
		$('borrower_id').focus();
		return false;
	}else	if ($F('date_borrowed')==""){
		alert("Please indicate the date borrowed.");
		$('date_borrowed').focus();
		return false;
	}else if ($F('time_borrowed')==""){
		alert("Please indicate the time borrowed.");
		$('time_borrowed').focus();
		return false;
	}else if (($F('releaser_id_new')=="")||($F('releaser_id_new')==0)){
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
//alert("jsPrepareBorrowArray :: \n"+msg);

	var borrowArray=new Array();
		borrowArray['borrow_nr'] = $F('borrow_nr');
		borrowArray['batch_nr'] = $F('batchNo');
		borrowArray['borrower_id'] = $F('borrower_id');
		borrowArray['borrower_id']=checkIfSelfBorrow();		//added code by angelo m. 08.10.2010
		borrowArray['date_borrowed'] = $F('date_borrowed');
		borrowArray['time_borrowed'] = time_borrowed_formatted;
		borrowArray['releaser_id'] = $F('releaser_id_new');
		borrowArray['releaser_fullname'] = $F('releaser_fullname_new');
		borrowArray['remarks'] = $F('remarks');

		borrowArray['date_returned'] = $F('date_returned');
		borrowArray['time_returned'] = time_returned_formatted;
		borrowArray['receiver_id'] = $F('receiver_id_new');
		borrowArray['receiver_fullname'] = $F('receiver_fullname_new');

msg="jsPrepareBorrowArray :: \nborrowArray : "+borrowArray+" \n"+
		"borrowArray.length='"+borrowArray.length+"' \n"+
		"$('batchNo').name ='"+$('batchNo').name+"' \n";
//alert("jsPrepareBorrowArray :: \n"+msg);
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
//alert("jsSaveBorrow :: \n"+msg);
	xajax_saveRadioBorrow(borrowArray);
}//end of function jsSaveBorrow

function jsUpdateBorrow(){
	var borrowArray=new Array();
	borrowArray=jsPrepareBorrowArray();
//		borrowArray['borrow_nr'] = $F('borrow_nr');
msg="borrowArray : "+borrowArray+" \n"+
		"borrowArray.length='"+borrowArray.length+"' \n"+
		"$('borrow_nr').name ='"+$('borrow_nr').name+"' \n";
//alert("jsUpdateBorrow :: \n"+msg);
	xajax_updateRadioBorrow(borrowArray);
	/*
		borrowArray['borrow_nr'] = $F('borrow_nr');
		borrowArray['batch_nr'] = $F('batchNo');
		borrowArray['borrower_id'] = $F('borrower_id');
		borrowArray['date_borrowed'] = $F('date_borrowed');
		borrowArray['time_borrowed'] = time_borrowed_formatted;
		borrowArray['releaser_id'] = $F('releaser_id_new');
		borrowArray['releaser_fullname'] = $F('releaser_fullname_new');
		borrowArray['remarks'] = $F('remarks');

		borrowArray['date_returned'] = $F('date_returned');
		borrowArray['time_returned'] = time_returned_formatted;
		borrowArray['receiver_id'] = $F('receiver_id_new');
		borrowArray['receiver_fullname'] = $F('receiver_fullname_new');


	*/



//	alert(borrowArray["borrower_id"]);
//	alert("self borrow records "+ borrowArray["borrower_id"]);
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
	}else if (($F('receiver_id_new')=="")||($F('receiver_id_new')==0)){
		alert("The user is unrecognizable or the user's session time has expired.\nPlease login again properly.");
		return false;
	}
	alert("Clicking on the Return button will ONLY save the information in Return Form.");
	return true;
}//end of function checkReturnForm

function jsSaveReturn(){
	var borrowArray=new Array();
	borrowArray=jsPrepareBorrowArray();
		borrowArray['borrow_nr'] = $F('borrow_nr');
msg="borrowArray : "+borrowArray+" \n"+
		"borrowArray.length='"+borrowArray.length+"' \n"+
		"$('borrow_nr').name ='"+$('borrow_nr').name+"' \n";
//alert("jsSaveReturn :: \n"+msg);
	xajax_updateRadioReturn(borrowArray);
}//end of function jsSaveReturn

function jsDoneBorrow(){

/*	var msg="You are about to update the Returning Details ONLY. \n"+
				" Warning: Editing an entry data is an irrevocable process.  \n"+
				"          Once you click on the Return button, the entry will be locked from further editing. \n"+
				" Do you want to continue?";
*/
	var msg=" Warning: Once you click on the OK button, \n"+
				"\t [a] The last successfully saved Borrowing/Releasing and Return Forms data ONLY will (still) apply. \n"+
				"\t [b] The entry will be locked from further editing. \n"+
				" Do you want to continue?";
	var answer = confirm(msg);
		//alert("answer = '"+answer+"'");
	if (answer){
		//continue with the process...
	}else{
		return false;
	}

msg="borrowArray['borrow_nr'] = '"+$F('borrow_nr')+"' \n";
//alert("jsDoneBorrow :: "+msg);
	xajax_updateRadioDone($F('borrow_nr'));
}//end of function jsDoneBorrow


	/*
	*	Sets the borrow number for this transaction
	*		and the mode to 'borrowed'
	*	created: burn November 7, 2007
	*/
function ajxSetBorrowNr(borrow_nr){
var msg="$F('mode') ='"+$F('mode')+"' \n"+
			"borrow_nr ='"+borrow_nr+"' \n"
			"$F('releaser_id') ='"+$F('releaser_id')+"' \n"+
			"$F('releaser_fullname') = '"+$F('releaser_fullname')+"' \n"+
			"$F('update') = '"+$F('update')+"' \n";

//alert("ajxSetBorrowNr :: BEFORE : \n"+msg);

	$('borrow_nr').value = borrow_nr;
	$('mode').value = 'borrowed';
	$('releaser_id').value=$F('releaser_id_new');
	$('releaser_fullname').value = $F('releaser_fullname_new');

msg="$F('mode') ='"+$F('mode')+"' \n"+
			"borrow_nr ='"+borrow_nr+"' \n"
			"$F('releaser_id') ='"+$F('releaser_id')+"' \n"+
			"$F('releaser_fullname') = '"+$F('releaser_fullname')+"' \n"+
			"$F('update') = '"+$F('update')+"' \n";
//alert("ajxSetBorrowNr :: AFTER : \n"+msg);
}//end of function ajxSetBorrowNr

	/*
	*	Sets the borrow number for this transaction
	*		and the mode to 'borrowed'
	*	created: burn November 8, 2007
	*/
function ajxSetReturnMode(){
var msg="$F('mode') ='"+$F('mode')+"'\n"+
			"$F('receiver_id') ='"+$F('receiver_id')+"' \n"+
			"$F('receiver_fullname') = '"+$F('receiver_fullname')+"' \n"+
			"$F('update') = '"+$F('update')+"' \n";
//alert("ajxSetReturnMode :: BEFORE : \n"+msg);

	$('mode').value = 'returned';
	$('receiver_id').value=$F('receiver_id_new');
	$('receiver_fullname').value = $F('receiver_fullname_new');

msg="$F('mode') ='"+$F('mode')+"'\n"+
			"$F('receiver_id') ='"+$F('receiver_id')+"' \n"+
			"$F('receiver_fullname') = '"+$F('receiver_fullname')+"' \n"+
			"$F('update') = '"+$F('update')+"' \n";
//alert("ajxSetReturnMode :: AFTER : \n"+msg);
}//enc of function ajxSetReturnMode

	/*
	*	Sets the the mode to 'done'
	*	created: burn November 8, 2007
	*/
function ajxSetDoneMode(){
var msg="$F('mode') ='"+$F('mode')+"'\n";
//alert("ajxSetDoneMode :: BEFORE : \n"+msg);

	$('mode').value = 'done';

msg="$F('mode') ='"+$F('mode')+"'\n";
//alert("ajxSetDoneMode :: AFTER : \n"+msg);
}//enc of function ajxSetDoneMode

	/*
	*	Sets the the update to '1',
	*		which means there is a change in the status [borrowed & done ONLY]
	*	created: burn November 9, 2007
	*/
function ajxSetUpdate(){
//alert("ajxSetUpdate :: BEFORE : $F('update') = '"+$F('update')+"'");
	$('update').value=1;
//alert("ajxSetUpdate :: AFTER : $F('update') = '"+$F('update')+"'");
}
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

		$('headerReturned').style.display='none';
		$('dateReturned').style.display='none';
		$('timeReturned').style.display='none';
		$('filmReceiver').style.display='none';
		$('btnReturn').style.display='none';
		$('btnUpdateReturn').style.display='none';

		$('btnDone').style.display='none';
		$('doneButtonHr').style.display='none';

		//added by VAN 07-10-08
		$('penaltyRow').style.display='none';

	}else if ($F('mode')=='borrowed'){
			//update mode of borrow entry
		$('btnBorrow').style.display='none';
		$('btnUpdateBorrow').style.display='';

		$('headerReturned').style.display='';
		$('dateReturned').style.display='';
		$('timeReturned').style.display='';
		$('filmReceiver').style.display='';
		$('btnReturn').style.display='';

		//added by VAN 07-10-08
		$('penaltyRow').style.display='';

		$('btnUpdateReturn').style.display='none';

		$('btnDone').style.display='none';
		$('doneButtonHr').style.display='none';
	}else if ($F('mode')=='returned'){
			//return info/entries has been added
		$('btnBorrow').style.display='none';
		$('btnUpdateBorrow').style.display='';

		$('headerReturned').style.display='';
		$('dateReturned').style.display='';
		$('timeReturned').style.display='';
		$('filmReceiver').style.display='';
		$('btnReturn').style.display='none';
		$('btnUpdateReturn').style.display='';
		//added by VAN 07-10-08
		$('penaltyRow').style.display='';

		$('btnDone').style.display='';
		$('doneButtonHr').style.display='';
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

		$('filmReceiver').style.display='';
	}
//alert("$F('borrower_id') = '"+$F('borrower_id')+"'");
/*
	if ($F('borrower_id')>0){
		$('borrower_self').checked = false;
	}else{
		$('borrower_dept').disabled=true;
		$('borrower_id').disabled=true;
		$('borrower_self').checked = true;
	}
	alert("$F('borrower_id') = '"+$F('borrower_id')+"'");
*/
}//end of function preset

function checkIfSelfBorrow(){
		if ($('borrower_self').checked){
			return $F('pid');
		}
		else{
			return $F('borrower_id');
		}

}

function selfBorrower(){

	if ($('borrower_self').checked){
		$('borrower_dept').value=0;
		$('borrower_dept').disabled=true;
		$('borrower_id').disabled=true;
	}else{
		$('borrower_dept').disabled=false;
		$('borrower_id').disabled=false;
	}
}

function closeThisWindow(){
//alert("closeThisWindow :: $F('update') ='"+$F('update')+"'");
	// commented by VAN 07-24-08
	//if ($F('update')==1)
		window.parent.location.href=window.parent.location.href;
	window.parent.pSearchClose();
}

function closeAfterDone(){
//alert("closeAfterDone :: $F('update') ='"+$F('update')+"'");
	window.parent.location.href=window.parent.location.href;
	window.parent.pSearchClose();
}
