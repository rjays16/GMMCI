/* MEDOCS ICD10 FUNCTION */

var highlightColor="#F57A74";	// Cell background color for a highlighted row
var keyCode;
var k_encounter,k_encounter_type, k_create_id,k_tabs;
var bol=false;

	/*
	*	Removes leading and trailing whitespaces
	*	e.g. str.trim()
	*/
String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ""); };

function setKeyCode(key,encounter,encounter_type,encounter_type_a,create_id){
	keyCode=key;
	k_encounter = encounter; 
	k_encounter_type =encounter_type;
	k_create_id=create_id;
	k_encouter_type_a = encounter_type_a;
}

// For ICD prepareAddIcdCode
function prepareAddIcdCode(encounter,encounter_type,create_id) {
	var srcTable, srcRows, srcTableBody;
	var i, isAdded, id2x, type;
	var target = 'icd';
	//alert('prepareAddIcdCode = '+encounter+' - '+encounter_type+' - '+create_id);
	var aDepartment, aDepartment_nr;
	var aDoctor, aDoctor_nr;
	var aDate,date_d; 
	//alert('type = '+encounter_type);
	//commented by VAN 02-28-08
	/*
	if (encounter_type==1){
		// ER Consultation
		if(aDepartment=$("current_dept_nr_d")){
			aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;		
		}
		if(aDoctor = $("current_doc_nr_d")){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}		
		alert('dept, doc ER = '+aDepartment_nr+' - '+aDoctor_nr);
	}else if (encounter_type==2){
		// OPD Consultation
		if($("current_dept_nr_c")){
//			alert("prepareAddIcdCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_c')= '"+$F("current_dept_nr_c")+"'");
			aDepartment_nr = $F("current_dept_nr_c");
		}
		if(aDoctor = $('current_doc_nr_c')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}		
		alert('dept, doc OPD = '+aDepartment_nr+' - '+aDoctor_nr);
	}else{
		// Inpatient
		if(aDepartment=$("current_dept_nr_f")){
//			alert("prepareAddIcdCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_f')= '"+$F("current_dept_nr_f")+"'");
			aDepartment_nr = $F("current_dept_nr_f");
		}
		if(aDoctor = $('current_doc_nr_f')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}			
		alert('dept, doc IPD = '+aDepartment_nr+' - '+aDoctor_nr);
	}
	*/
	//edited by VAN 02-28-08
	if(aDepartment=$("current_dept_nr_d")){
		aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;		
	}
	if(aDoctor = $("current_doc_nr_d")){
		aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
	}		
	//alert('dept, doc = '+aDepartment_nr+' - '+aDoctor_nr);
//	return;
/*
	//Admitted patient from OPD / ER 
	if(aDepartment=$("current_dept_nr_d")){
		aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;		
	}
	if(aDoctor = $("current_doc_nr_d")){
		aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
	}
	//Final Diagnosis / procedure
	if(aDepartment=$("current_dept_nr_f")){
		aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;		
	}
	if(aDoctor = $("current_doc_nr_f")){
		aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
	}
*/
	if(aDate= $("date_text_d")){
//		date_d = dateFormat(date_text_d);   // burn commmented: July 16, 2007
		date_d = dateFormat(aDate);
	}else if(aDate = $("txtAdmissionDate")){
//		date_d = dateFormat(txtAdmissionDate);   // burn commmented: July 16, 2007
		date_d = dateFormat(aDate);
	}
	
	//alert("prepareAddIcdCode-create_id="+create_id);
	//alert("prepareADdicdCode-dept="+aDepartment_nr);
	
	
	if(document.getElementById('icdType').checked){
		type=1;	// principal diagnosis
	}else{
		type=0;	 // other diagnosis
	}
		
	id2x=document.getElementById("icdCode").value;
	//alert('medocs_fucntion  : id2x = '+id2x);
	id2x=id2x.toUpperCase();
	
	if (srcTable=document.getElementById("icdCodeTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");
	}

	if (id2x!=-1 && (isNaN(id2x))){
		//check if the row is already in the list
		isAdded=false;

		for (i=0;i<srcRows.length;i++) {
			rowid = srcRows[i].id.replace("icdCodeRow","");
			
			if (id2x==document.getElementById("icdCodeID"+rowid).value){
				isAdded=true;
				break;
			}
		}
		//alert("inside if 02: isAdded->"+isAdded);		
		if (isAdded) {
			//Fat.fade_element("icdCodeRow"+rowid, 0, 1000, highlightColor, false);
		}else {
		  	xajax_addCode(encounter,encounter_type,date_d,id2x,aDoctor_nr,aDepartment_nr,create_id,target,type);
		  	
		}
	}else {
		//alert("inside else: isAdded->"+isAdded);
		alert("Pls. fill up icd code...");
	}
}

//For ICPM prepareAddIcpCode
//function prepareAddIcdCode(encounter,encounter_type,create_id,tabs) {
function prepareAddIcpCode(encounter,encounter_type,create_id) {
	var srcTable, srcRows, srcTableBody;
	var i, isAdded,type;
	var target = 'icp';
	var id1x=document.getElementById("icpCode").value;
	
	var aDepartment, aDepartment_nr;
	var aDoctor, aDoctor_nr;
	var aDate,date_p,atime, timeNow = new Date();

	if (encounter_type==1){
		// ER Consultation
		if(aDepartment=$("current_dept_nr_p")){
			aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;		
		}
		if(aDoctor = $("current_doc_nr_p")){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}
	}else if (encounter_type==2){
		// OPD Consultation
		if($("current_dept_nr_c")){
//			alert("prepareAddIcpCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_c')= '"+$F("current_dept_nr_c")+"'");
			aDepartment_nr = $F("current_dept_nr_c");
		}
		if(aDoctor = $('current_doc_nr_c')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}		
	}else{
		// Inpatient
		if(aDepartment=$("current_dept_nr_p")){
//			alert("prepareAddIcpCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_f')= '"+$F("current_dept_nr_f")+"'");
			aDepartment_nr = $F("current_dept_nr_p");
		}
		if(aDoctor = $('current_doc_nr_p')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}			
	}
/*
	#12:00 
	if($_POST['selAMPM'] == 'P.M.'){
		$hr = substr($_POST['time_text_d'],0,2);
		$min = substr($_POST['time_text_d'],-3);
		if($hr>=00){	
			$hr1 = $hr+12;	
		}	
		$_POST['time_text_d'] = $hr1.$min;
	}
*/

	//atime = js_getTime(); // burn commented: July 16, 2007
	trimString($('time_text_p'));
	atime = $F('time_text_p');
	if (atime!=''){
		var colonIndex = atime.indexOf(":");
		var hour = atime.substring(0,colonIndex);
		var minute = atime.substring(colonIndex+1);
		if ($F('selAMPM_p')=='P.M.'){
			if (parseInt(hour)<12)
				hour = parseInt(hour)+12;
			atime = hour+":"+minute;
		}else{
			if (hour=="12") //12:?? AM
				hour = "00";
			atime = hour+":"+minute;		
		}
	}
	//Date of operation performed
	if(aDate = $("date_text_p")){
		if(atime != ''){
//			date_p = dateFormat(date_text_p) + " " + atime + ":00";   // burn commmented: July 16, 2007
			date_p = dateFormat(aDate) + " " + atime + ":00";
//			alert("atime is not empty="+ date_p);
		}else{
//			date_p = dateFormat(date_text_p) + " " +  getClockTime();   // burn commmented: July 16, 2007
			date_p = dateFormat(aDate) + " " +  getClockTime();
		}
	}else if (aDate = $("txtAdmissionDate")){
//		date_p = dateFormat(txtAdmissionDate) + " " +  atime;   // burn commmented: July 16, 2007
		date_p = dateFormat(aDate) + " " +  atime;
	}
	
	
	if(document.getElementById('icpType').checked){
		type = 1;
	}else{
		type= 0;
	}
			
	if (srcTable=document.getElementById("icpCodeTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");
	}
	if (id1x!=-1 && (isNaN(id1x))){
		//check if the row is already in the list
		isAdded=false;
		for (i=0;i<srcRows.length;i++) {
			rowid = srcRows[i].id.replace("icpCodeRow","");
			if (id1x==document.getElementById("icpCodeID"+rowid).value){
				isAdded=true;
				break;
			}
		}

		if (isAdded) {
			//Fat.fade_element("icpCodeRow"+rowid, 0, 1000, highlightColor, false);
			document.getElementById("icpCode").value='';
		}else {
 			//alert("diagnosis prepareICD-encounter_type="+ ecounter_type); 
// 			alert("date_p = "+ date_p + " aDoctor_nr = "+ aDoctor_nr + " aDepartment = " + aDepartment_nr);
 			xajax_addCode(encounter,encounter_type,date_p,id1x,aDoctor_nr,aDepartment_nr,create_id,target,type);
		}
	}else {
		alert("Pls. fill up ICPM code...");
	}
}

function gui_rmvIcdCodeRow(rowNum) {
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("icdCodeRow"+rowNum);
	if (destTable=document.getElementById("icdCodeTable")) {
		destRows=destTable.getElementsByTagName("tbody")[0];
		// check if srcRows is valid and has more than 1 element
		// alert(destRows);
		if (destRows) {
			destRows.removeChild(rmvRow);
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function gui_rmvIcpCodeRow(rowNum) {
	var destTable, destRows, rmvRow;
	rmvRow=document.getElementById("icpCodeRow"+rowNum);
	if (destTable=document.getElementById("icpCodeTable")) {
		destRows=destTable.getElementsByTagName("tbody")[0];
		// check if srcRows is valid and has more than 1 element
		//alert(destRows);
		if (destRows) {
			destRows.removeChild(rmvRow);
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

// add row for diagnosis
function gui_addIcdCodeRow(encounter,code,desc,target,create_id,type) {
	var srcTable, srcRows, srcTableBody, newRowSrc, lastRowNo;
	var i,fontweight;
	//if (highlight==null) highlight=false;
	//alert("type->"+type);
	if (srcTable=document.getElementById("icdCodeTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");
		
		if (srcRows.length>0) lastRowNo=srcRows[srcRows.length-1].id.replace("icdCodeRow","");
		
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;		
		
		if(type=='O'){
			fontweight = 'normal';	
		}else{
			fontweight = 'bold; color:#CC0000;';	
		}	
		
		if (isNaN(code)) {
			newRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" style="font-weight:'+fontweight+'" id="icdCodeRow'+lastRowNo+'" onclick="getType(\''+type+'\',0,'+lastRowNo+')" >'+
					'<td>'+
						'<input type="hidden" id="icdCodeID'+lastRowNo+'" name="icdCodeID['+lastRowNo+']" value="'+code+'" >'+code+
					'</td>'+
					'<td>'+
						'<input type="hidden" id="icdCodeDesc'+lastRowNo+'" name="icdCodeDesc['+lastRowNo+']" value="'+desc+'" >'+desc+
					'</td>'+
					'<td align="center">'+
						'<input type="hidden" id="enCode'+lastRowNo+'" value="'+encounter+'">'+
						'<input type="hidden" id="targetIcdCode'+lastRowNo+'" value="'+target+'">'+
						'<input type="hidden" id="typeIcdCode'+lastRowNo+'" name="typeIcdCode'+lastRowNo+'" value="'+type+'">'+
						'<input type="button" id="icdCodeRmv'+lastRowNo+'" value="x" onclick="xajax_rmvCode(\''+encounter+'\',\''+code+'\',\''+target+'\','+lastRowNo+',\''+create_id+'\');" style="width:25px">'
					'</td>'+
					'</tr>';
			
		}
		else {
			newRowSrc = '<tr class="wardlistrow1" id="icdCodeRow'+lastRowNo+'">' +
				'<td colspan="5">No discount added</td>' +
			 '</tr>';
		}
		srcTableBody.innerHTML += newRowSrc;
		
		//summary();
		//if (highlight) Fat.fade_element("icdCodeRow"+lastRowNo, 0, 1000, highlightColor, false);
	}
	//alert('target = '+target);
	// added by VAN 02-25-08
	/*
	if (target=='icd'){
		//alert('icd');	
		document.getElementById('icdCode').value='';
	}else if (target=='icp'){
		//alert('icp');	
		document.getElementById('icpCode').value='';
	}
	*/	
	document.getElementById('icdCode').value='';
	
	//preset();
}

//For ICPM add IcpCodeRow //hightlight
function gui_addIcpCodeRow(encounter,code,desc,target,create_id,type,docName) {
	var srcTable, srcRows, srcTableBody, newRowSrc, lastRowNo;
	var i;
	//if (highlight==null) highlight=false;
	
	if (srcTable=document.getElementById("icpCodeTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");
		
    	if (srcRows.length>0) lastRowNo=srcRows[srcRows.length-1].id.replace("icpCodeRow","");
		
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;		

		if(type=='O'){
			fontweight = 'normal';	
		}else{
			fontweight = 'bold; color:#CC0000;';	
		}

		if (isNaN(code)){
				newRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" style="font-weight:'+fontweight+'" id="icpCodeRow'+lastRowNo+'" onclick="getType(\''+type+'\',1,'+lastRowNo+')" >'+
					'<td>'+
						'<input type="hidden" id="icpCodeID'+lastRowNo+'" name="icpCodeID['+lastRowNo+']" value="'+code+'" >'+code+
					'</td>'+
					'<td>'+
						'<input type="hidden" id="icpCodeDesc'+lastRowNo+'" name="icpCodeDesc['+lastRowNo+']" value="'+desc+'" >'+desc+
					'</td>'+
					'<td>'+
						'<input type="hidden" id="docName'+lastRowNo+'" name="docName['+lastRowNo+']" value="'+docName+'" >'+docName+
					'</td>'+
					'<td align="center">'+
						'<input type="hidden" id="enIcpCode'+lastRowNo+'" value="'+encounter+'">'+
						'<input type="hidden" id="targetIcpCode'+lastRowNo+'" value="'+target+'">'+
						'<input type="hidden" id="typeIcpCode'+lastRowNo+'" name="typeIcpCode'+lastRowNo+'" value="'+type+'">'+
						'<input type="button" id="icpCodeRmv'+lastRowNo+'" value="x" onclick="xajax_rmvCode(\''+encounter+'\',\''+code+'\',\''+target+'\','+lastRowNo+',\''+create_id+'\',\''+type+'\');" style="width:25px">'
					'</td>'+
					'</tr>';					
		}else {
			newRowSrc = '<tr class="wardlistrow1" id="icdCodeRow'+lastRowNo+'">' +
				'<td colspan="5">No discount added</td>' +
			 '</tr>';
		}
		srcTableBody.innerHTML += newRowSrc;
		
		/*
		if (target=='icd'){
		//alert('icd');	
			document.getElementById('icdCode').value='';
		}else if (target=='icp'){
			//alert('icp');	
			document.getElementById('icpCode').value='';
		}*/
		
		document.getElementById('icpCode').value='';
		
		//summary();
		//if (highlight) Fat.fade_element("icpCodeRow"+lastRowNo, 0, 1000, highlightColor, false);
	}
}


function trimString(objct){
//	alert("inside frunction trimString: objct = '"+objct+"'");
	objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
}

function gui_clearIcdCodeRows(){
	var srcTable, srcRows, srcTableBody;
	if (srcTable=document.getElementById("icdCodeTable")){
		srcTableBody=srcTable.getElementByTagName("tbody")[0];
		srcRows=srcTableBody.childNodes;
		if (srcRows){
			while(srcRows.length>0){
				srcTableBody.removeChild(srcRows[0]);
			}
			return true; // 
		}else return false;
	}else return false;
}

function gui_clearIcpCodeRows(){
	var srcTable, srcRows, srcTableBody;
	if (srcTable=document.getElementById("icpCodeTable")){
		srcTableBody=srcTable.getElementByTagName("tbody")[0];
		srcRows=srcTableBody.childNodes;
		if (srcRows){
			while(srcRows.length>0){
				srcTableBody.removeChild(srcRows[0]);
			}
			return true; // 
		}else return false;
	}else return false;
}

function setType(type){
	if(type==0){  // Icd - Diagnosis
		if($("icdType").checked){
			$('icdTypeName').innerHTML = "Principal Diagnosis";	
		}else{
			$('icdTypeName').innerHTML = "Other Diagnosis";	
		}
	}else{ // Icp - Procedures
		if($("icpType").checked){
			$('icpTypeName').innerHTML = "Principal Procedures";	
		}else{
			$('icpTypeName').innerHTML = "Other Procedures";		
		}
	}
}


function getType(type,cat,rowNo){
	if(cat==0){
		if($('typeIcdCode'+rowNo).value == 'P'){	
			$("icdType").checked = true;
			if($("icdType").checked){
				$('icdTypeName').innerHTML = "Principal Diagnosis";
			}
		}else{
			$("icdType").checked = false;
			$('icdTypeName').innerHTML = "Other Diagnosis";
		}
	}else{
		if($('typeIcpCode'+rowNo).value == 'P'){	
			$('icpType').checked = true;
			if($('icpType').checked){
				$('icpTypeName').innerHTML = "Principal Procedures";
			}
		}else{
			$('icpType').checked = false;
			$('icpTypeName').innerHTML = "Other Procedures";
		}
	}
}

var seg_validDate=true;
//var seg_validTime=false;

function seg_setValidDate(bol){
	seg_validDate=bol;
//	alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");	
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
	
	trimString(thisTime);

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
	
	jtime = hour + ":" + minute;
	js_setTime(jtime);
	
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




var js_time = "";
function js_setTime(jstime){
	js_time = jstime;	
}

function js_getTime(){
	return js_time;	
}

function validateTime(S) {
    return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
}


/*
		Checks the doctor and department when entering DIAGNOSIS codes in ER mode
		burn added : April 30, 2007
*/
function checkDeptDocDiagnosisERMode(encounter_type){
	//alert('checkDeptDocDiagnosisERMode : encounter_type = '+encounter_type);
		if ($('current_doc_nr_d'))
			trimString($('current_doc_nr_d'));
		if ($('current_dept_nr_d'))
			trimString($('current_dept_nr_d'));

		//if ( ((encounter_type==1)||(encounter_type==3)) 
		  if ( ((encounter_type==1)||(encounter_type==3)|| (encounter_type==2)||(encounter_type==4)) 										
				&& $('current_doc_nr_d') && ($F('current_doc_nr_d')==0) ){
			alert("Select the physician for this diagnosis!");
			$('current_doc_nr_d').focus();			
			return false;
		//}else if ( ((encounter_type==1)||(encounter_type==3)) 
		}else if ( ((encounter_type==1)||(encounter_type==3) || (encounter_type==2)||(encounter_type==4)) 												
						&& $('current_dept_nr_d') && ($F('current_dept_nr_d')==0) ){
			alert("Select the department for this diagnosis!");
			$('current_dept_nr_d').focus();
			return false;
		}else if ((encounter_type==2) && $('current_doc_nr_c') && ($F('current_doc_nr_c')==0)){
			alert("Select the physician for this diagnosis!");
			$('current_doc_nr_c').focus();			
			return false;
		}else if ($('current_doc_nr_f') && ($F('current_doc_nr_f')==0)){
			alert("Select the physician for this diagnosis!");
			$('current_doc_nr_f').focus();			
			return false;
		}else{
//			return false;
			return true;
		}
}/* end of function checkDeptDocDiagnosisERMode */

/*
		Checks the doctor and department when entering PROCEDURE codes in ER mode
		burn added : April 30, 2007
*/
function checkDeptDocProcedureERMode(encounter_type){
	//alert('checkDeptDocProcedureERMode : encounter_type = '+encounter_type);
		if ($('current_doc_nr_p'))
			trimString($('current_doc_nr_p'));
		if ($('current_dept_nr_p'))
			trimString($('current_dept_nr_p'));
		
		if ( ((encounter_type==1)||(encounter_type==3)) 
				&& $('current_doc_nr_p') && ($F('current_doc_nr_p')==0)){
			alert("Select the physician for this procedure!");
			$('current_doc_nr_p').focus();
			return false;
		}else if ( ((encounter_type==1)||(encounter_type==3)) 
				&& $('current_dept_nr_p') && ($F('current_dept_nr_p')==0)){
			alert("Select the department for this procedure!");
			$('current_dept_nr_p').focus();
			return false;
		}else if ((encounter_type==2) && $('current_doc_nr_p') && ($F('current_doc_nr_p')==0)){
			alert("Select the physician for this procedure!");
			$('current_doc_nr_p').focus();
			return false;
/*		
		}else if ($('current_doc_nr_f') && ($F('current_doc_nr_f')==0)){
			alert("please select the correct answer.");
			alert("Select the physician for this diagnosis!");
			$('current_doc_nr_f').focus();			
			return false;
*/
		}else if($('time_text_p') && ($F('time_text_p') == '')){
			alert("Please fill up the time of operation.");
			$('time_text_p').focus();			
		}else{
			return true;
		}
}/* end of function checkDeptDocProcedureERMode */

function checkRadioButton(object_name){

	var obj = document.getElementsByName(object_name);
//alert("inside function checkRadioButton! obj = '"+obj+"'; object_name = '"+object_name+"'");	
//alert("inside function checkRadioButton! obj.length = '"+obj.length+"'");	
//alert("inside function checkRadioButton! obj[0].checked = '"+obj[0].checked+"'");	
	for (i=obj.length-1; i > -1; i--) {
		if (obj[i].checked) {
			return true;   // at least one button has been selected
		}
	}
	return false;
}/* end of function checkRadioButton */

function setFrmSubmt(){
//alert("inside function setFrmSubmt! $('current_dept_nr_f') = '"+$('current_dept_nr_f')+"'");
		//alert("setFrmSubmt : $F('current_doc_nr_c') ='"+$F('current_doc_nr_c')+"'");
		//alert("setFrmSubmt : seg_validDate ='"+seg_validDate+"'; seg_validTime ='"+seg_validTime+"'");

		trimString($('date_text_d'));
		trimString($('time_text_d'));
		if ($('current_doc_nr_f'))
			trimString($('current_doc_nr_f'));
		if ($('current_dept_nr_f'))
			trimString($('current_dept_nr_f'));

		if ($('cond_code')){
			if (!checkRadioButton('cond_code')){
				alert("Please select a condition at ER.");
				$('cond_code').focus();
				return false;
			}
		}
		if($('result_code')){
			if (!checkRadioButton('result_code')){
				alert("Please select a result.");
				$('result_code').focus();
				return false;
			}
		}
		if($('disp_code')){ 
			if (!checkRadioButton('disp_code')){
				alert("Please select a dispostion.");
				$('disp_code').focus();
				return false;
			}
		}
		//commented by VAN 02-29-08
		/*
		if ( $('current_doc_nr_c') && ($F('current_doc_nr_c')==0)){
			alert("Select a Consulting Physician!");
			$('current_doc_nr_c').focus();
			return false;
		}else */
		if ( $('current_doc_nr_f') && ($F('current_doc_nr_f')==0)){
			alert("Select an Attending Physician!");
			$('current_doc_nr_f').focus();
			return false;
		}else if ( $('current_dept_nr_f') && ($F('current_dept_nr_f')==0)){
			alert("Select an Attending Department!");
			$('current_dept_nr_f').focus();
			return false;
		}else if (($F('date_text_d')=='')||(seg_validDate==false)){
			alert("Enter the Discharge Date!");
			$('date_text_d').focus();
			return false;
		}else if(($F('time_text_d')=='') && (seg_validTime==false) && ($F('encounter_type')!= 2)){
		//}else if (($F('time_text_d')=='')||(seg_validTime==false)||($F('encounter_type')== 2)){
			alert("Enter the Discharge Time!");
			$('time_text_d').focus();
			return false;
		}else{
//			return false;
			bol=true;
			return true;
		}
}

function getFrmSubmt(){
//alert("inside function getFrmSubmt! val = '"+val+"'");
	if(bol==true){ 	
	 	return true;
	}else{
		return false;
	}
}


function clearField(elementId){
	//alert("elementid="+elementId);
	$(elementId).value = '';
	$(elementId).focus();
	//$(elementId).style.diplay = 'none';
}


//--- YAHOO.util.Event ----------//

function clrField(elId, ev){
	var clr = function(e){
		$(elId).value = '';
		$(elId).focus();
	}
	YAHOO.util.Event.addListener(elId,ev,clr);
}

function inputCodeHandler(el, encounter, encounter_type, encounter_type_a, create_id){
	//var input = function (e){	
   //e = e || window.event.e;
	//	if(e.keyCode == '123'){
	switch(el){
		case "icdCode":                 
			if (checkDeptDocDiagnosisERMode(encounter_type) && checkICDSpecific())
				prepareAddIcdCode(encounter,encounter_type, create_id);
		break;
		case "icpCode":
			if (checkDeptDocProcedureERMode(encounter_type))
				prepareAddIcpCode(encounter,encounter_type,create_id);			
		break;
	}
	//YAHOO.util.Event.on(el,"keypress", input);
} // end function inputCodeHandler


function getClockTime()
{
   var now    = new Date();
   var hour   = now.getHours();
   var minute = now.getMinutes();
   var second = now.getSeconds();
   var ap = "AM";
   if (hour   > 11) { ap = "PM";             }
   if (hour   > 12) { hour = hour - 12;      }
   if (hour   == 0) { hour = 12;             }
   if (hour   < 10) { hour   = "0" + hour;   }
   if (minute < 10) { minute = "0" + minute; }
   if (second < 10) { second = "0" + second; }
   var timeString = hour +
                    ':' +
                    minute +
                    ':' +
                    second;// +
                //    " " +
                //    ap;
   return timeString;
} // function getClockTime()

function dateFormat(obj){
	var dt = obj.value;
	var st = dt.toString();
	var dd = st.substring(3,5) ; 
	var mm = st.substring(0,2);
	var yr = st.substring(6);
	var timestring = yr + '-' +
				   mm + '-' +
				   dd;
	 return timestring;
} // dateFormat()

