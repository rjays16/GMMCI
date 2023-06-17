/* MEDOCS ICD10 FUNCTION */

var highlightColor="#F57A74";	// Cell background color for a highlighted row
var keyCode;
var k_encounter,k_encounter_type, k_create_id,k_tabs;
var bol=false;


//shortcut keys
/*
shortcut("F12",
	function(){
		encounter = k_encounter; 
		encounter_type =k_encounter_type;
        create_id=k_create_id;
		tabs =k_tabs;			
		if(keyCode==0){
			if (encounter_type==1 && checkDeptDocDiagnosisERMode()){
				prepareAddIcdCode(encounter,encounter_type,create_id);
			}else{
				prepareAddIcdCode(encounter,encounter_type,create_id);
			}
			//alert("prepareAddIcdCode->encounter"+encounter+"<br>encounter_type="+encounter_type+"<br>encounter_type_a"+encounter_type_a+"<br>create_id"+create_id);
		}else if(keyCode==1){
			prepareAddIcpCode(encounter,encounter_type,create_id);	
			//alert("prepareAddIcpCode->encounter"+encounter+"encounter_type="+encounter_type+"create_id"+create_id);
		}else{
			return false;	
		}
		
	}
);
*/

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
	
	var aDepartment, aDepartment_nr;
	var aDoctor, aDoctor_nr;
	var aDate,date_d; 
	
	if (encounter_type==1){
		// ER Consultation
		if(aDepartment=$("current_dept_nr_d")){
			aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;		
		}
		if(aDoctor = $("current_doc_nr_d")){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}		
	}else if (encounter_type==2){
		// OPD Consultation
		if($("current_dept_nr_c")){
//			alert("prepareAddIcdCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_c')= '"+$F("current_dept_nr_c")+"'");
			aDepartment_nr = $F("current_dept_nr_c");
		}
		if(aDoctor = $('current_doc_nr_c')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}		
	}else{
		// Inpatient
		if(aDepartment=$("current_dept_nr_f")){
//			alert("prepareAddIcdCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_f')= '"+$F("current_dept_nr_f")+"'");
			aDepartment_nr = $F("current_dept_nr_f");
		}
		if(aDoctor = $('current_doc_nr_f')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}			
	}
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
		date_d = aDate.value;
	}else if(aDate = $("txtAdmissionDate")){
		date_d = aDate.value;
	}
	
	//alert("prepareAddIcdCode-create_id="+create_id);
	//alert("prepareADdicdCode-dept="+aDepartment_nr);
	
	
	if(document.getElementById('icdType').checked){
		type=1;	// principal diagnosis
	}else{
		type=0;	 // other diagnosis
	}
		
	id2x=document.getElementById("icdCode").value;
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
			//if(encounter_type == 1){ 
			  //if(checkDeptDocDiagnosisERMode()){
			  	xajax_addCode(encounter,encounter_type,date_d,id2x,aDoctor_nr,aDepartment_nr,create_id,target,type);
			  //}
			//}else{
				//xajax_addCode(encounter,encounter_type,date_d,id2x,aDoctor_nr,aDepartment_nr,create_id,target,type);
			//}
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
	var aDate,date_p;

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
		if(aDepartment=$("current_dept_nr_f")){
//			alert("prepareAddIcpCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_f')= '"+$F("current_dept_nr_f")+"'");
			aDepartment_nr = $F("current_dept_nr_f");
		}
		if(aDoctor = $('current_doc_nr_f')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}			
	}
/*
	//Admitted patient from OPD / ER 
	if(aDepartment=$("current_dept_nr_p")){
		aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;		
	}
	if(aDoctor = $("current_doc_nr_p")){
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
	//Discharged date for final diagnosis and procedure
	if(aDate= $("date_text_d")){
		date_p = aDate.value;
	}else if( aDate = $("txtAdmissionDate") ){
		date_p = aDate.value;	
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
		}else {
		  //  xajax_addCode(encounter,encounter_type,id2x,aDoctor_nr,aDepartment_nr,create_id,"icd",enc_diagnosis)
		   if(encounter_type == 1){
		 		if(checkDeptDocProcedureERMode()){
		 			//alert("diagnosis prepareICD-encounter_type="+ ecounter_type); 
		 			xajax_addCode(encounter,encounter_type,date_p,id1x,aDoctor_nr,aDepartment_nr,create_id,target,type);
		 		}  
		   }else{
		   		xajax_addCode(encounter,encounter_type,date_p,id1x,aDoctor_nr,aDepartment_nr,create_id,target,type);
		   }
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
		//alert(destRows);
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
	//preset();
}

//For ICPM add IcpCodeRow //hightlight
function gui_addIcpCodeRow(encounter,code,desc,target,create_id,type) {
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
var seg_validTime=false;

function seg_setValidDate(bol){
	seg_validDate=bol;
//	alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");	
}

function setFormatTime(){
	var time = $('time_text_d');
	var stime = time.value;
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	
	trimString($('time_text_d'));
	   // burn added : June 6, 2007
	if ($F('time_text_d')==''){
		seg_validTime=false;
		return;
	}
	if (stime.length == 3){
		hour = stime.substring(0,1);
		minute = stime.substring(1,3);
		
		ftime =  hour + ":" + ((minute < 10) ? "0" : "") + minute;
		
		if(!ftime.match(f2)){
			time.value = "";
			alert("Invalid time format");
			seg_validTime=false;   // burn added : June 6, 2007
		}else{
			time.value = ftime;
			seg_validTime=true;   // burn added : June 6, 2007
		}
	} else if (stime.length == 4){
		hour = stime.substring(0,2);
		minute = stime.substring(2,4);
		
		if(hour >12){
			 hour -= 12;
			 $('selAMPM').value = "P.M.";
		}
		ftime = hour + ":" + ((minute < 10) ? "0" : "") + minute;
		
		if(!ftime.match(f1)){
			time.value = "";
			alert("Invalid time format");
			seg_validTime=false;   // burn added : June 6, 2007
		}else{
			time.value = ftime;
			seg_validTime=true;   // burn added : June 6, 2007
		}
	}else{
		alert("Invalid time format");
		time.value = "";
		seg_validTime=false;   // burn added : June 6, 2007
	}
}// end of function setFormatTime


function validateTime(S) {
    return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
}

/*
		Checks the doctor and department when entering DIAGNOSIS codes in ER mode
		burn added : April 30, 2007
*/
function checkDeptDocDiagnosisERMode(encounter_type){
		if ($('current_doc_nr_d'))
			trimString($('current_doc_nr_d'));
		if ($('current_dept_nr_d'))
			trimString($('current_dept_nr_d'));

		if ((encounter_type==1) && $('current_doc_nr_d') && ($F('current_doc_nr_d')==0)){
			alert("Select the physician for this diagnosis!");
			$('current_doc_nr_d').focus();			
			return false;
		}else if ((encounter_type==1) && $('current_dept_nr_d') && ($F('current_dept_nr_d')==0)){
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
		if ($('current_doc_nr_p'))
			trimString($('current_doc_nr_p'));
		if ($('current_dept_nr_p'))
			trimString($('current_dept_nr_p'));
		
		if ((encounter_type==1) && $('current_doc_nr_p') && ($F('current_doc_nr_p')==0)){
			alert("Select the physician for this procedure!");
			$('current_doc_nr_p').focus();
			return false;
		}else if ((encounter_type==1) && $('current_dept_nr_p') && ($F('current_dept_nr_p')==0)){
			alert("Select the department for this procedure!");
			$('current_dept_nr_p').focus();
			return false;
		}else if ((encounter_type==2) && $('current_doc_nr_c') && ($F('current_doc_nr_c')==0)){
			alert("Select the physician for this procedure!");
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

		if ( $('current_doc_nr_c') && ($F('current_doc_nr_c')==0)){
			alert("Select an Consulting Physician!");
			$('current_doc_nr_c').focus();
			return false;
		}else if ( $('current_doc_nr_f') && ($F('current_doc_nr_f')==0)){
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
		}else if (($F('time_text_d')=='')||(seg_validTime==false)){
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
alert("inside function getFrmSubmt! val = '"+val+"'");
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
	//	}
	//}
	//YAHOO.util.Event.on(el,"keypress", input);
} // end function inputCodeHandler



