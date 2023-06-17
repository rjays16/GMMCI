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

function checkScheduleForm(){
//alert("checkScheduleForm :: $F('batchNo') = '"+$F('batchNo')+"' \n$F('time_scheduled') = '"+$F('time_scheduled')+"'");
	if (($F('batchNo')== '') || ($F('batchNo')=='0')){
		alert("Please select a request first.");
		$('select-batchNr').focus();
		return false;	
	}else if ($F('date_scheduled') == ''){
		alert("Please enter the scheduled date.");
		$('date_scheduled').focus();
		return false;	
	}else if ($F('time_scheduled')== ''){
		alert("Please enter the scheduled time.");
		$('time_scheduled').focus();
		return false;	
	}
	return true;
}

function saveSchedule(mode){
	var batch_nr,scheduled_dt,instructions,remarks,list_ins,service_date;

	if (checkScheduleForm()==false){
		return;
	}
	batch_nr = $F('batchNo'); 
	remarks = $F('remarks');
	scheduled_date = $F('date_scheduled');
	scheduled_time = jsFormatTime('scheduled');
//alert("saveSchedule :: mode = '"+mode+"' \n$F('service_date')= '"+$F('service_date')+"'");
	if ($('service_date')){
		service_date = $F('service_date');
	}
//alert("saveSchedule :: mode = '"+mode+"' \nservice_date= '"+service_date+"'");

var msg = " batch_nr = '"+batch_nr+"' \n"+
	" remarks = '"+remarks+"' \n"+
	" scheduled_date = '"+scheduled_date+"' \n"+
	" scheduled_time = '"+scheduled_time+"' \n"+
	" instructions = '"+instructions+"'";
//alert("saveSchedule :: \n"+msg);
	instructions = document.getElementsByName('instruction[]');
	/*
	msg = "instructions = '"+instructions+"' \n"+
		"instructions.length = '"+instructions.length+"' \n"+
		"instructions[0].checked = '"+instructions[0].checked+"' \n"+
		"instructions[1].checked = '"+instructions[1].checked+"' \n"+
		"instructions[0].value = '"+instructions[0].value+"' \n"+
		"instructions[1].value = '"+instructions[1].value+"' \n";
	*/	
//alert("saveSchedule :: \n"+msg);
	list_ins = document.getElementsByName('instruction[]');
	instructions = new Array();
	for(var i=0; i < list_ins.length; i++){
		var temp = list_ins[i].value;
		if (list_ins[i].checked){
			if (list_ins[i].value==0){
				//others
				temp = temp+" "+$F('instruction_other');
			}
			instructions.push(temp);
		}
	}
	msg = "instructions = '"+instructions+"' \n"+
		"instructions.length = '"+instructions.length+"' \n";
//alert("saveSchedule :: \n"+msg);
	if (mode=='save'){
//		dTable = document.getElementById('scheduled-list');
		xajax_saveScheduledRequest('save',batch_nr,scheduled_date, scheduled_time,instructions,remarks,$F('sub_dept_nr'),$F('pmonth'),$F('pday'),$F('pyear'));
	}else{
//		dTable = window.parent.document.getElementById('scheduled-list');
		xajax_updateScheduledRequest('update',batch_nr,scheduled_date,service_date,scheduled_time,instructions,remarks,$F('sub_dept_nr'),$F('pmonth'),$F('pday'),$F('pyear'));
	}
}

function msgPopUp(msg){
	alert(msg);
}


function resetForm(){
	document.getElementById('schedule').reset();
	clearEncounter();
	$('instruction_other').disabled=true;
}

function jsRadioNoFoundScheduledRequest(mode){
	var dTable,dTBody,rowSrc;

	if (mode=='update'){
		dTable = window.parent.document.getElementById('scheduled-list');
	}else{
		dTable = document.getElementById('scheduled-list');
	}
//alert("jsRadioNoFoundScheduledRequest ::  \nmode= '"+mode+"' \ndTable ='"+dTable+"'");
//	if (dTable=document.getElementById('scheduled-list')) {
	if (dTable){
		dTBody=dTable.getElementsByTagName("tbody")[0];
		rowSrc = '<tr><td colspan="9" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">No request scheduled for the day</td></tr>';
		dTBody.innerHTML += rowSrc;
//alert("jsRadioNoFoundRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
}


function pSearchClose() {
//	alert("radio-schedule-daily.js : pSearchClose : ");
	cClick();  //function in 'overlibmws.js'
}

function closePopUpWindow(msg,updateParentWindow){
//alert("closePopUpWindow :: $F('update') ='"+$F('update')+"'");
	if (msg!=""){
		alert(msg);
	}
	if (updateParentWindow==1){
		window.parent.location.href=window.parent.location.href;
	}
	window.parent.pSearchClose();
}

//added by VAN 07-08-08
function ReloadWindow(){
	//alert('here');	
	window.parent.location.href=window.parent.location.href;
	window.parent.pSearchClose();
}


function jsPrepareScheduleArray(){
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
//alert("jsPrepareBorrowArray :: \n"+msg);

	var borrowArray=new Array();
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

msg="jsPrepareScheduleArray :: \nborrowArray : "+borrowArray+" \n"+
		"borrowArray.length='"+borrowArray.length+"' \n"+
		"$('batchNo').name ='"+$('batchNo').name+"' \n";
//alert("jsPrepareScheduleArray :: \n"+msg);
	return borrowArray;
}//end of function jsPrepareScheduleArray


function closeThisWindow(){
//alert("closeThisWindow :: $F('update') ='"+$F('update')+"'");
	if ($F('update')==1)
		window.parent.location.href=window.parent.location.href;
	window.parent.pSearchClose();
}

function closeAfterDone(){
//alert("closeAfterDone :: $F('update') ='"+$F('update')+"'");
	window.parent.location.href=window.parent.location.href;
	window.parent.pSearchClose();
}
