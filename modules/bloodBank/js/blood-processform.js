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

function clearEncounter() {
	var msg="$('batchDisplay').innerHTML = '"+$('batchDisplay').innerHTML+"' \n"+
				"$('p_name').value = '"+$('p_name').value+"' \n"+
				"$('batchNo').value = '"+$('batchNo').value+"' \n"+
				"$('clear-batchNr').disabled = '"+$('clear-batchNr').disabled+"' \n";
//alert("clearEncounter :: BEFORE \n"+msg);
	$('batchDisplay').innerHTML="";
	$('p_name').value="";
	$('batchNo').value="";
	$('clear-batchNr').disabled = true;	
	msg="$('batchDisplay').innerHTML = '"+$('batchDisplay').innerHTML+"' \n"+
				"$('p_name').value = '"+$('p_name').value+"' \n"+
				"$('batchNo').value = '"+$('batchNo').value+"' \n"+
				"$('clear-batchNr').disabled = '"+$('clear-batchNr').disabled+"' \n";
//alert("clearEncounter :: AFTER \n"+msg);
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

function showPrintPdf(update){
		if (update==1){
			  document.getElementById('printpdf').style.display = '';
		}else{
				document.getElementById('printpdf').style.display = 'none';
		}
}

function deleteScheduledRequest(batch_nr,count){
		var msg="You are about to delete entry #"+count+" with Reference Number "+batch_nr+". \n"+
				"Are you sure?";
		var answer = confirm(msg);
//		var dTable = document.getElementById('scheduled-list');
//alert("deleteScheduledRequest :: answer = '"+answer+"'");
		if (answer){
//			alert("deleteScheduledRequest :: deleted");
//			fSubmit('form_test_findings');			
			xajax_deleteScheduledRadioRequest('delete',batch_nr,$F('sub_dept_nr'),$F('pmonth'),$F('pday'),$F('pyear'));
//			refreshFindingList();
		}
}

function msgPopUp(msg){
	alert(msg);
	window.location.href=window.location.href;
}


function resetForm(){
	document.getElementById('schedule').reset();
	clearEncounter();
	$('instruction_other').disabled=true;
}

function click_others(thisObject){
	var ms='';
	var x=document.getElementsByName("instruction");
	msg ="thisObject ='"+thisObject+"'"+
			"\nx ='"+x+"'"+
			"\nx.length = '"+x.length+"'"+
			"\nx.value = '"+x.value+"'"+
			"\nx[0] ='"+x[0]+"'"+
//			"\nx[0].value ='"+x[0].value+"'"+
			"\nx[1] ='"+x[1]+"'";
//			"\nx[1].value ='"+x[1].value+"'";
	
//alert("click_others :: "+msg);	
	if (thisObject.checked==true){
		$('instruction_other').disabled=false;
	}else{
		$('instruction_other').value='';	
		$('instruction_other').disabled=true;	
	}
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


function clearScheduledList(mode) {	
	var dTable, dBody;
	if (mode=='update'){
		dTable = window.parent.document.getElementById('scheduled-list');
	}else{
		dTable = document.getElementById('scheduled-list');
	}
//alert("clearScheduledList ::  \nmode= '"+mode+"' \ndTable ='"+dTable+"'");
//	if (dTable=document.getElementById('scheduled-list')) {
	if (dTable) {
		var dBody=dTable.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
//			alert("clearScheduledList :: TRUE");
			return true;
		}
	}
//	alert("clearScheduledList :: FALSE");
	return false;
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


function jsAddScheduledRequest(mode,count,instructions,batch_nr,scheduled_time,service_code,rid,patient_name,scheduled_by,status){

	var dTable, dBody,rowSrc;
	var root_path,toolTipTextHandler,editImg,deleteImg;

	if (mode=='update'){
		root_path = window.parent.$F('root_path');
		sub_dept_nr_name = window.parent.$F('sub_dept_nr_name');
		dTable = window.parent.document.getElementById('scheduled-list');
	}else{
		root_path = $F('root_path');
		sub_dept_nr_name = $F('sub_dept_nr_name');
		dTable = document.getElementById('scheduled-list');
	}
//	if (dTable=document.getElementById('scheduled-list')) {
	if (dTable) {
		dTBody=dTable.getElementsByTagName("tbody")[0];

		toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'+count+'\').value, CAPTION,\'Instructions\',  '+
							'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '+
							'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';

		editImg = 'src="'+root_path+'gui/img/control/default/en/en_edit_icon_06.gif" border=0 width="20" height="21"';
		deleteImg = 'src="'+root_path+'gui/img/control/default/en/en_trash_06.gif" border=0 width="20" height="21"';
//
		//alert('sub_dept_nr_name = '+sub_dept_nr_name);
		var editPopUp = 'style="cursor:pointer;" '+
						'onclick="overlib('+
						'	OLiframeContent(\'seg-radio-schedule-form.php?batch_nr='+batch_nr+'&sub_dept_nr_name='+sub_dept_nr_name+'\', '+
						'	500, 450, \'fSelBatchNr\', 1, \'auto\'),'+
						'	WIDTH, 500, TEXTPADDING,0, BORDER,0, STICKY, SCROLL, CLOSECLICK, MODAL, '+
						'	CLOSETEXT, \'<img src='+root_path+'/images/close.gif border=0 >\','+
						'	CAPTIONPADDING,4, CAPTION,\'Update Scheduled Request\', MIDX,0, MIDY,0, '+
						'	STATUS,\'Update Scheduled Request\'); return false;"'+
						'onmouseout="nd();"';

		option_edit = '<img name="edit'+count+'" id="edit'+count+'" '+editImg+' '+editPopUp+'>';
		option_delete ='<img name="delete'+count+'" id="delete'+count+'" '+deleteImg+' onClick="deleteScheduledRequest('+batch_nr+','+count+');" style="cursor:pointer;"> ';
		if(batch_nr){
//alert("jsRadioUnscheduledRequest :  if(batch_nr) is true 1 : rowSrc="+rowSrc);
			id = batch_nr;
			rowSrc = '<tr>'+
							'<td align="right">'+count+
								'<input type="hidden" name="toolTipText'+count+'" id="toolTipText'+count+'" value="'+instructions+'">'+
							'</td>'+
							'<td align="left">'+batch_nr+'</td>'+
							'<input type="hidden" name="batch_nr'+count+'" id="batch_nr'+count+'" value="'+batch_nr+'">'+
							'<td align="right">'+scheduled_time+'</td>'+
							'<td align="left" '+toolTipTextHandler+'>'+service_code+'</td>'+
							'<td align="right" '+toolTipTextHandler+'>'+rid+'</td>'+
							'<td align="left" '+toolTipTextHandler+'>'+patient_name+'</td>'+
							'<td align="left">'+scheduled_by+'</td>';
			if (status=='done'){
				/*
				rowSrc +='<td align="center">'+option_edit+'</td>'+
							'<td align="center">&nbsp;</td>';
				*/
				rowSrc +='<td align="center">'+option_edit+'</td>'+
							'<td align="center"><img name="delete'+count+'" id="delete'+count+'" src="../../images/btn_donerequest.gif" align="absmiddle" border="0"/></td>';
//				rowSrc +='<td align="center" colspan="2">Done</td>';
			}else{
				rowSrc +='<td align="center">'+option_edit+'</td>'+
							'<td align="center">'+option_delete+'</td>';
			}

			rowSrc +='</tr>'+"\n";
//alert("jsRadioUnscheduledRequest :  if(batchNo) is true 2 : rowSrc="+rowSrc);
		}else{
			rowSrc = '<tr>'+
							'<td colspan="9" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">'+
							'	No request scheduled for the day'+
							'</td>'+
						'</tr>';
		}
		dTBody.innerHTML += rowSrc;
	}
}//end of function addScheduledRequest

function pSearchClose() {
//	alert("radio-schedule-daily.js : pSearchClose : ");
	cClick();  //function in 'overlibmws.js'
}

function closeWindow(){
	key = document.getElementById('key').value;
	pagekey = document.getElementById('pagekey').value;
	//alert(key+" - "+pagekey);
	window.parent.closeWindow(key,pagekey);
	window.parent.pSearchClose();	
}

//added by VAN 06-17-08
function viewSchedule(batch_nr){
	window.open("seg-radio-schedule-pdf.php?batch_nr="+batch_nr+"&showBrowser=1","viewPatientSchedule","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
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

//added by VAN 07-09-08

function checkProcessForm(){
	var filmcnt = $F('filmcnt');
	var chkbox_error = "false";
	
	for (i=1;i<=filmcnt;i++){
		if (document.getElementById('size'+i).checked==true){
			chkbox_error = true;
			break;
		}	
	}
	
	if (document.getElementById('date_served').value==''){
		alert("Please enter the date of service (film is produced).");
		$('date_served').focus();
		return false;		
	}else if (chkbox_error=="false"){
		alert("Please select a film size for the request.");
		$('size1').focus();
		return false;		
	}
	
	return true;
}


function saveProcessFilmed(mode){
	var batch_nr;
	var filmcnt = $F('filmcnt');
	var date_served = $F('date_served');
	var service_code = document.getElementById('service_code').innerHTML;
	var sizes = new Array();
    //var nooffilms = new Array();

	if (checkProcessForm()==false){
		return;
	}
	batch_nr = $F('batchNo'); 
	//no_film_used
	for (i=1;i<=filmcnt;i++){
		if (document.getElementById('size'+i).checked==true){
			//alert(i);
			//sizes[i-1] = Array(document.getElementById('size'+i).value, document.getElementById('no_film_used'+i).value, document.getElementById('no_film_spoilage'+i).value);
			//edited by VAN 08-20-09
			sizes[i-1] = Array(document.getElementById('size'+i).value, document.getElementById('item'+i).value,  document.getElementById('expiry_date'+i).value, document.getElementById('no_film_used'+i).value, document.getElementById('no_film_spoilage'+i).value);
		}	
	}
	//alert(sizes);
	//if (mode=='save'){
		xajax_saveProcessRequest('save',batch_nr,service_code, date_served,sizes);
	//}else{
	//	xajax_updateProcessRequest('update',batch_nr,sizes);
	//}
	
}
/*
function msgPopUp(msg){
	alert(msg);
}
*/


function enableNoBlood(code){
	//alert(code);
	var wblood  = $('blood'+code).checked;
	if (wblood){
			$('no_blood_used'+code).readOnly = 0;
  }else{
		  	$('no_blood_used'+code).readOnly = 1;
  }
	$('no_blood_used'+code).value = "";
	
}
//------------------

//added by VAN 06-21-09
function displayBlood(id){
	/*var wfilm  = $('sizefilm'+id).checked;
	if (wfilm){
			
			$('rowB'+id).style.display='';   
	}else{
		  $('rowB'+id).style.display='none';   	
	}*/
}

function validate_qty(index){
	var no_film_used = $('no_film_used'+index).value;	
	var no_film_spoilage = $('no_film_spoilage'+index).value;	
	var rem_qty = $('qty'+index).value;	
	var total;
	
	if (no_film_used=='')
		no_film_used = 0;
	
	if (no_film_spoilage=='')
		no_film_spoilage = 0;
	
	total = parseInt(no_film_used) + parseInt(no_film_spoilage);
	
	if (total > parseInt(rem_qty)){
		alert('Entered quantity exceeds inventory at hand.');	
		$('no_film_used'+index).value = "";
		$('no_film_spoilage'+index).value = "";
	}
}

//number only and decimal point is allowed
function keyPressHandler(e){
	var unicode=e.charCode? e.charCode : e.keyCode
	if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
	//if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
		return false //disable key press
}

function addItems(){
	
}

//-----------------------

//----- added by VAN 06-24-09 -------------
function emptyTray() {
    clearOrder($('order-list'));
    appendOrder($('order-list'),null);
    refreshDiscount();
}

function clearOrder(list) {    
    if (!list) list = $('order-list')
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0]
        if (dBody) {
            trayItems = 0
            dBody.innerHTML = ""
            return true
        }
    }
    return false;
}

function appendOrder(list, details) {
    if (!list) list = $('order-list');
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        
        if (dBody) {
            var src;
            var lastRowNum = null,
                    items = document.getElementsByName('items[]');
                    dRows = dBody.getElementsByTagName("tr");
                    
            if (details) {
            
                var id = details.id,
                    name = details.name,
                    qty = details.qty;
                    
               
                    if (items.length == 0) {
                        clearOrder(list);
                        rowno = String(1);
                    }            
                    else
                        rowno = String(items.length + 1); 
                
                if (items) {
                    for (var i=0;i<items.length;i++) {
                        if (items[i].value == details.id) {
 
                            var itemRow = $('row'+id);
                            
                            $('rowQty'+id).value = details.qty;
							$('rowUnit'+id).value = details.units;
                                                        
                            var name_serv = details.name;
                            alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');
                            
                            return true                        
                        }
                    } 
                    if (items.length == 0) clearOrder(list)
                }
                
                
                alt = (dRows.length%2)+1;
				
                src = src = 
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
					'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+details.id+'" />'+
					'<input type="hidden" name="is_unitpcs[]" id="is_pc_'+id+'" value="'+details.is_perpc+'" />'+
					'<td class="centerAlign" ><a href="javascript: nd(); removeItem(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
					'<td>'+details.name+'</td>'+
					'<td><input type="text" name="qty[]" id="rowQty'+id+'" value="'+details.qty+'" /></td>'+
					'<td>'+details.units+'</td>'+
					'<td>&nbsp;</td>'+
				'</tr>';
                
                trayItems++;
                
                 alert('Item added to item list...');
            }
            else {
                src = "<tr><td colspan=\"10\">Item list is currently empty...</td></tr>";    
            }
            dBody.innerHTML += src;
            
            return true;
        }
    }
    alert('Failed to add item...');
    return false;
    
}

function removeItem(id) {
    var destTable, destRows;
    var table = $('order-list');
    var rmvRow=document.getElementById("row"+id);
    if (table && rmvRow) {
        var rndx = rmvRow.rowIndex-1;
        table.deleteRow(rmvRow.rowIndex);
        if (!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0)
            appendOrder(table, null);
        reclassRows(table,rndx);
    }
    //refreshTotal();
    else
        alert(table+' and '+rmvRow);
}


function reclassRows(list,startIndex) {
    if (list) {
        var dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            var dRows = dBody.getElementsByTagName("tr");
            if (dRows) {
                for (i=startIndex;i<dRows.length;i++) {
                    dRows[i].className = "wardlistrow"+(i%2+1);
                }
            }
        }
    }
}

//added by VAN 09-02-09
function doneService(){
    var date_served = $F('date_served');
    var service_code = document.getElementById('service_code').innerHTML;
    batch_nr = $F('batchNo'); 
    
   if (document.getElementById('date_served').value==''){
        alert("Please enter the date of service (film is produced).");
        $('date_served').focus();
        return false;
   }else{     
        xajax_ProcessDoneRequest(batch_nr,service_code,date_served);
   }     
}

function preset(){
    var group = document.getElementById('radio_section').value;
    
    //if (group=='USD'){
			if ((group=='USD')||(group=='CT')){
        document.getElementById('filmrow').style.display = "none";
        document.getElementById('other_row').style.display = "none";
    }else{
        document.getElementById('filmrow').style.display = "";
        document.getElementById('other_row').style.display = "";
    }  
}

//------------------------------------