/* MEDOCS ICD10 FUNCTION */

var highlightColor="#F57A74";	// Cell background color for a highlighted row
var keyCode;
var k_encounter,k_encounter_type, k_create_id,k_tabs;
var bol=false;
var geticdtype = {};

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
	//alert(keyCode+" - "+k_encounter+" - "+k_encounter_type+" - "+k_create_id+" - "+k_encouter_type_a);
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
	//if(aDepartment=$("current_dept_nr_d")){
    if(document.getElementById('current_dept_nr_d').value != "0"){
        if(aDepartment=document.getElementById('current_dept_nr_d')){ //Edited by Jarel 03-01-13 
		aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
	}
    }else{
        if(aDepartment=document.getElementById('current_dept_nr_f')){ //Edited by Jarel 03-01-13 
            aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
        }
    }
    
    if(document.getElementById('current_doc_nr_d').value != "0"){
        if(aDoctor = document.getElementById('current_doc_nr_d')){ //Edited by Jarel 03-01-13
		aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
	}
    }else{
        if(aDoctor = document.getElementById('current_doc_nr_f')){ //Edited by Jarel 03-01-13
            aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
        }    
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
	//if(aDate= $("date_text_d")){
	if(aDate= document.getElementById('date_text_d')){
//		date_d = dateFormat(date_text_d);   // burn commmented: July 16, 2007
		date_d = dateFormat(aDate);
	//}else if(aDate = $("txtAdmissionDate")){
	}else if(aDate = document.getElementById('txtAdmissionDate')){
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
		//edited by VAN 03-28-08
	//id2x=document.getElementById("icdCode").value;
	//alert(icdCode.value);
	id2x = icdCode.value;
	//alert('medocs_fucntion  : id2x = '+id2x);
	id2x=id2x.toUpperCase();

	if (srcTable=document.getElementById("icdCodeTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");
	}
			//alert('icd = '+id2x);
//commented by VAN 10-15-2008
//	if (id2x!=-1 && (isNaN(id2x))){
		//check if the row is already in the list
		isAdded=false;
			 // alert('icd = '+document.getElementById('icdCode').value);
				//alert('icd = '+id2x);
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

			//added by daryl
			//11/15/2013
				//xajax_save_Seg_encounter_diagnoses(encounter,id2x,create_id);

				xajax_addCode(encounter,encounter_type,date_d,id2x,aDoctor_nr,aDepartment_nr,create_id,target,type);
		}
//COMMENTED BY VAN 10-15-2008
	/*}else {
		//alert("inside else: isAdded->"+isAdded);
		alert("Pls. fill up icd code...");
	}*/
}

//added by Dommie 03/07/14
function isESCPressed(e) {
	var kC  = (window.event) ?    // MSIE or Firefox?
			 event.keyCode : e.keyCode;
	var Esc = (window.event) ?
			27 : e.DOM_VK_ESCAPE // MSIE : Firefox
	return (kC==Esc);
}

function editAltDesc(id) {
	$("descmain_edit"+id).style.display = "";
	$("descmain"+id).style.display = "none";
	$("icdCodeDesc_"+id).focus();
}
function cancelAltDesc(id) {
	$("descmain_edit"+id).style.display = "none";
	$("descmain"+id).style.display = "";
}

function addslashes(str) {
	str=str.replace("'","\\");
	return str;
}

function applyAltDesc(e,id,code,user_id) {
	var characterCode;
	var enc_nr   = $('encounter_nr').value;
	// var user_id  = $('create_id').value;
	
	if (e) {
		if(e && e.which) { //if which property of event object is supported (NN4)
			characterCode = e.which; //character code is contained in NN4's which property
		}
		else {
			characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}
	}
	else
		characterCode = 13;

	if ( (characterCode == 13) || (isESCPressed(e)) ) {
		var altDesc =$("icdCodeDesc_"+id).value;
		if(altDesc !="")
		{
			xajax_saveAltDiagDesc(enc_nr, code, altDesc, user_id);
			$("descmain"+id).innerHTML = '<a onClick="editAltDesc(\''+id+'\')" style="cursor:pointer" >'+altDesc+'</a>';
		}		
		$("descmain_edit"+id).style.display = "none";
		$("descmain"+id).style.display = "";
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
	 /*
	if (encounter_type==1){
		// ER Consultation
		//if(aDepartment=$("current_dept_nr_p")){
		if(aDepartment=document.getElementById('current_dept_nr_p')){
			aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
		}
		//if(aDoctor = $("current_doc_nr_p")){
		if(aDoctor = document.getElementById('current_doc_nr_p')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}
	}else if (encounter_type==2){
		// OPD Consultation
		//if($("current_dept_nr_c")){
		if(document.getElementById('current_dept_nr_c')){
//			alert("prepareAddIcpCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_c')= '"+$F("current_dept_nr_c")+"'");
			//aDepartment_nr = $F("current_dept_nr_c");
			aDepartment_nr = document.getElementById('current_dept_nr_c').value;
		}
		if(aDoctor = document.getElementById('current_doc_nr_c')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}
	}else{
		// Inpatient
		//if(aDepartment=$("current_dept_nr_p")){
		if(aDepartment=document.getElementById('current_dept_nr_p')){
//			alert("prepareAddIcpCode : aDepartment.value = '"+aDepartment.value+"'; $F('current_dept_nr_f')= '"+$F("current_dept_nr_f")+"'");
			//aDepartment_nr = $F("current_dept_nr_p");
			aDepartment_nr = document.getElementById('current_dept_nr_p').value;
		}
		//if(aDoctor = $('current_doc_nr_p')){
		if(aDoctor = document.getElementById('current_doc_nr_p')){
			aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
		}
	}
	*/
	//edited by VAN 02-28-08
	//if(aDepartment=$("current_dept_nr_d")){
	if(aDepartment=document.getElementById('current_dept_nr_p')){
		aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
	}
	//if(aDoctor = $("current_doc_nr_d")){
	if(aDoctor = document.getElementById('current_doc_nr_p')){
		aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
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
	//trimString($('time_text_p'));
	trimString(document.getElementById('time_text_p'));
	//atime = $F('time_text_p');
	atime = document.getElementById('time_text_p').value;
	if (atime!=''){
		var colonIndex = atime.indexOf(":");
		var hour = atime.substring(0,colonIndex);
		var minute = atime.substring(colonIndex+1);
		//if ($F('selAMPM_p')=='P.M.'){
		if (document.getElementById('selAMPM_p').value=='P.M.'){
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
	//if(aDate = $("date_text_p")){
	if(aDate = document.getElementById('date_text_p')){
		if(atime != ''){
//			date_p = dateFormat(date_text_p) + " " + atime + ":00";   // burn commmented: July 16, 2007
			date_p = dateFormat(aDate) + " " + atime + ":00";
//			alert("atime is not empty="+ date_p);
		}else{
//			date_p = dateFormat(date_text_p) + " " +  getClockTime();   // burn commmented: July 16, 2007
			date_p = dateFormat(aDate) + " " +  getClockTime();
		}
	//}else if (aDate = $("txtAdmissionDate")){
	}else if (aDate = document.getElementById('txtAdmissionDate')){
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
	//if (id1x!=-1 && !(isNaN(id1x))){
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
			//alert("date_p = "+ date_p + " aDoctor_nr = "+ aDoctor_nr + " aDepartment = " + aDepartment_nr);
			//alert('target = '+target);
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
function gui_addIcdCodeRow(encounter,code,desc,target,create_id,type, doc, dept, ok) {
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
		//COMMENTED BY VAN 10-15-2008
	//	if (isNaN(code)) {
		//edit by daryl
		//11/16/2013

			/*03/07/14 Commented by Dommie
			newRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" style="font-weight:'+fontweight+'" id="icdCodeRow'+lastRowNo+'"  >'+
					'<td>'+
						'<input type="hidden" id="icdCodeID'+lastRowNo+'" name="icdCodeID['+lastRowNo+']" value="'+code+'" >'+code+
					'</td>'+
					'<td>'+
						'<input type="hidden" id="icdCodeDesc'+lastRowNo+'" name="icdCodeDesc['+lastRowNo+']" value="'+desc+'" >'+desc+
					'</td>'+
					'<td>'+doc+'</td>'+
					'<td>'+dept+'</td>'+
					'<td align="center">'+
						'<input type="hidden" id="enCode'+lastRowNo+'" value="'+encounter+'">'+
						'<input type="hidden" id="targetIcdCode'+lastRowNo+'" value="'+target+'">'+
						'<input type="hidden" id="typeIcdCode'+lastRowNo+'" name="typeIcdCode'+lastRowNo+'" value="'+type+'">'+
						'<input type="button" id="icdCodeRmv'+lastRowNo+'" value="x" onclick="xajax_rmvCode(\''+type+'\',\''+encounter+'\',\''+code+'\',\''+target+'\','+lastRowNo+',\''+create_id+'\');" style="width:25px">'
					'</td>'+
					'</tr>';
					The description of ICD must be editable
					*/

			newRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" style="font-weight:'+fontweight+'" id="icdCodeRow'+lastRowNo+'"  >'+
					'<td>'+
						'<input type="hidden" id="icdCodeID'+lastRowNo+'" name="icdCodeID['+lastRowNo+']" value="'+code+'" >'+code+
					'</td>'+
					'<td>'+

						//'<input type="hidden" id="icdCodeDesc'+lastRowNo+'" name="icdCodeDesc['+lastRowNo+']" value="'+desc+'" >'+
						'<span id="descmain_edit'+lastRowNo+'" style="display:none;">' +
							'<input style="width:95%;"  id="icdCodeDesc_'+lastRowNo+'" type="text" name="icdCodeDesc['+lastRowNo+']" value="'+desc+'" onFocus="this.select();" onblur="cancelAltDesc(\''+lastRowNo+'\');" onkeyup="applyAltDesc(event,\''+lastRowNo+'\',\''+code+'\',\''+create_id+'\');">'+
						'</span>'+
						'<span id="descmain'+lastRowNo+'">' +
							'<a onClick="editAltDesc(\''+lastRowNo+'\')" style="cursor:pointer" >'+desc+'</a>' +
						'</span>' +

					'</td>'+
					'<td>'+doc+'</td>'+
					'<td>'+dept+'</td>'+
					'<td align="center">'+
						'<input type="hidden" id="enCode'+lastRowNo+'" value="'+encounter+'">'+
						'<input type="hidden" id="targetIcdCode'+lastRowNo+'" value="'+target+'">'+
						'<input type="hidden" id="typeIcdCode'+lastRowNo+'" name="typeIcdCode'+lastRowNo+'" value="'+type+'">'+
						'<input type="button" id="icdCodeRmv'+lastRowNo+'" value="x" onclick="xajax_rmvCode(\''+type+'\',\''+encounter+'\',\''+code+'\',\''+target+'\','+lastRowNo+',\''+create_id+'\');" style="width:25px">'
					'</td>'+
					'</tr>';		
	//COMMENTED BY VAN 10-15-2008
	/*
		}

	else {
			newRowSrc = '<tr class="wardlistrow1" id="icdCodeRow'+lastRowNo+'">' +
				'<td colspan="5">No ICD added</td>' +
			 '</tr>';
		}
		*/
		srcTableBody.innerHTML += newRowSrc;
		document.getElementById('icdCode').focus();
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
	document.getElementById('icdDesc').value='';
	//if (ok==1)
		//ReloadWindow();
	//preset();
}

//For ICPM add IcpCodeRow //hightlight
function gui_addIcpCodeRow(encounter,code,desc,target,create_id,type,doc,dept) {
	var srcTable, srcRows, srcTableBody, newRowSrc, lastRowNo;
	var i;
	//if (highlight==null) highlight=false;

	if (srcTable=document.getElementById("icpCodeTable")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.getElementsByTagName("tr");

			if (srcRows.length>0) lastRowNo=srcRows[srcRows.length-1].id.replace("icpCodeRow","");

		//lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;
		lastRowNo=(isNaN(lastRowNo))?0:(lastRowNo-0)+1;

		if(type=='O'){
			fontweight = 'normal';
		}else{
			fontweight = 'bold; color:#CC0000;';
		}
//alert(target);
		if (isNaN(code)){
		//if (!(isNaN(code))){
				newRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" style="font-weight:'+fontweight+'" id="icpCodeRow'+lastRowNo+'" onclick="getType(\''+type+'\',1,'+lastRowNo+')" >'+
					'<td align="left">'+
						'<input type="hidden" id="icpCodeID'+lastRowNo+'" name="icpCodeID['+lastRowNo+']" value="'+code+'" >'+code+
					'</td>'+
					'<td align="left">'+
						'<input type="hidden" id="icpCodeDesc'+lastRowNo+'" name="icpCodeDesc['+lastRowNo+']" value="'+desc+'" >'+desc+
					'</td>'+
					/*'<td align="left">'+
						'<input type="hidden" id="docName'+lastRowNo+'" name="docName['+lastRowNo+']" value="'+docName+'" >'+docName+
					'</td>'+*/
					'<td>'+doc+'</td>'+
					/*'<td>'+dept+'</td>'+*/
					'<td align="center">'+
						'<input type="hidden" id="enIcpCode'+lastRowNo+'" value="'+encounter+'">'+
						'<input type="hidden" id="targetIcpCode'+lastRowNo+'" value="'+target+'">'+
						'<input type="hidden" id="typeIcpCode'+lastRowNo+'" name="typeIcpCode'+lastRowNo+'" value="'+type+'">'+
						'<input type="button" id="icpCodeRmv'+lastRowNo+'" value="x" onclick="xajax_rmvCode(\''+type+'\',\''+encounter+'\',\''+code+'\',\''+target+'\','+lastRowNo+',\''+create_id+'\');" style="width:25px">'
					'</td>'+
					'</tr>';
		}else {
			newRowSrc = '<tr class="wardlistrow1" id="icdCodeRow'+lastRowNo+'">' +
				'<td colspan="5">No ICPM added</td>' +
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
		document.getElementById('icpDesc').value='';

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
	//edited by Daryl
	//11/18/2013
	if(type==0 || geticdtype.types==0 ){  // Icd - Diagnosis
		//if($("icdType").checked){
		if(document.getElementById('icdType').checked){
			document.getElementById('icdTypeName').innerHTML = "Principal Diagnosis";
		}else{
			document.getElementById('icdTypeName').innerHTML = "Other Diagnosis";
		}

	}else{ // Icp - Procedures
		if(document.getElementById('icdType').checked){
			//$('icpTypeName').innerHTML = "Principal Procedures";
			document.getElementById('icdTypeName').innerHTML = "Principal Procedures";
		}else{
			//$('icpTypeName').innerHTML = "Other Procedures";
			document.getElementById('icdTypeName').innerHTML = "Other Procedures";
		}
	}

//added by daryl
//11/18/2013
	if (geticdtype.types == "P")
	{
			document.getElementById('icdTypeName').innerHTML = "Other Procedures";
			document.getElementById('icdType').checked;

			if(document.getElementById('icdType').checked)
			{
				alert("Only One Primary allowed for ICD code");
			document.getElementById('icdType').checked = false;

			}

		}
	}

//added by daryl
//11/15/2013
//functions when adding
function setType2(type){
		geticdtype.types = type;

	// alert("set2 ='"+type+"'");

		if (type == "P")
		{
			document.getElementById('icdTypeName').innerHTML = "Other Diagnosis";
			document.getElementById('icdType').checked = false;
		}
}

//added by daryl
//11/16/2013
//function when deleting
function setType3(type){
 	
	// alert("set3 ='"+type+"'");

if (type == 'P')
{			
		geticdtype.types = 0;
			document.getElementById('icdType').checked = true;
			document.getElementById('icdType').disabled = false;
			document.getElementById('icdTypeName').innerHTML = "Principal Diagnosis";

}
}

function getType(type,cat,rowNo){
	if(cat==0){
		//if($('typeIcdCode'+rowNo).value == 'P'){
		if(document.getElementById('typeIcdCode'+rowNo).value == 'P'){
			document.getElementById("icdType").checked = true;
			if(document.getElementById("icdType").checked){
				document.getElementById('icdTypeName').innerHTML = "Principal Diagnosis";
			}
		}else{
			document.getElementById("icdType").checked = false;
			document.getElementById('icdTypeName').innerHTML = "Other Diagnosis";
		}
	}else{
		if(document.getElementById('typeIcpCode'+rowNo).value == 'P'){
			document.getElementById('icpType').checked = true;
			if(document.getElementById('icpType').checked){
				document.getElementById('icpTypeName').innerHTML = "Principal Procedures";
			}
		}else{
			document.getElementById('icpType').checked = false;
			document.getElementById('icpTypeName').innerHTML = "Other Procedures";
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
//alert(AMPM);
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
		 document.getElementById(AMPM).value = "A.M.";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "P.M.";
	}
    
    if ((typeof hour)=='number'){
        if (hour < 10)
            hour = '0'.concat(hour);
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
		if (document.getElementById('current_doc_nr_d'))
			trimString(document.getElementById('current_doc_nr_d'));
		if (document.getElementById('current_dept_nr_d'))
			trimString(document.getElementById('current_dept_nr_d'));
	/*
		//if ( ((encounter_type==1)||(encounter_type==3))
			if ( ((encounter_type==1)||(encounter_type==3)|| (encounter_type==2)||(encounter_type==4))
				&& document.getElementById('current_doc_nr_d') && (document.getElementById('current_doc_nr_d').value==0) ){
			alert("Select the physician for this diagnosis!");
			document.getElementById('current_doc_nr_d').focus();
			return false;
		//}else if ( ((encounter_type==1)||(encounter_type==3))
		}else if ( ((encounter_type==1)||(encounter_type==3) || (encounter_type==2)||(encounter_type==4))
						&& document.getElementById('current_dept_nr_d') && (document.getElementById('current_dept_nr_d')==0) ){
			alert("Select the department for this diagnosis!");
			document.getElementById('current_dept_nr_d').focus();
			return false;
		}
		else if ((encounter_type==2) && document.getElementById('current_doc_nr_c') && (document.getElementById('current_doc_nr_c').value==0)){
			alert("Select the physician for this diagnosis!");
			document.getElementById('current_doc_nr_c').focus();
			return false;
		}else if (document.getElementById('current_doc_nr_f') && (document.getElementById('current_doc_nr_f').value==0)){
			alert("Select the physician for this diagnosis!");
			document.getElementById('current_doc_nr_f').focus();
			return false;
		}else{
//			return false;
			return true;
		}
		*/
		return true;
}/* end of function checkDeptDocDiagnosisERMode */

/*
		Checks the doctor and department when entering PROCEDURE codes in ER mode
		burn added : April 30, 2007
*/
function checkDeptDocProcedureERMode(encounter_type){
	//alert('checkDeptDocProcedureERMode : encounter_type = '+encounter_type);
		if (document.getElementById('current_doc_nr_p'))
			trimString(document.getElementById('current_doc_nr_p'));
		if (document.getElementById('current_dept_nr_p'))
			trimString(document.getElementById('current_dept_nr_p'));
		/*
		if ( ((encounter_type==1)||(encounter_type==3))
				&& document.getElementById('current_doc_nr_p') && (document.getElementById('current_doc_nr_p').value==0)){
			alert("Select the physician for this procedure!");
			document.getElementById('current_doc_nr_p').focus();
			return false;
		}else*/ /*if ( ((encounter_type==1)||(encounter_type==3)||(encounter_type==2)||(encounter_type==4))
				&& document.getElementById('current_dept_nr_p') && (document.getElementById('current_dept_nr_p').value==0)){
			alert("Select the department for this procedure!");
			document.getElementById('current_dept_nr_p').focus();
			return false;       */
		/*}else if ((encounter_type==2) && document.getElementById('current_doc_nr_p') && (document.getElementById('current_doc_nr_p').value==0)){
			alert("Select the physician for this procedure!");
			document.getElementById('current_doc_nr_p').focus();
			return false;*/
/*
		}else if ($('current_doc_nr_f') && ($F('current_doc_nr_f')==0)){
			alert("please select the correct answer.");
			alert("Select the physician for this diagnosis!");
			$('current_doc_nr_f').focus();
			return false;
*/
/*
		}else if(document.getElementById('time_text_p') && (document.getElementById('time_text_p').value == '')){
			alert("Please fill up the time of operation.");
			document.getElementById('time_text_p').focus();
			*/
		//}else{

			return true;
		//}
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

		trimString(document.getElementById('date_text_d'));
		trimString(document.getElementById('time_text_d'));

		var admission_date = new Date(document.getElementById('txtAdmissionDate').value);
		var discharge_date = new Date(document.getElementById('date_text_d').value);
		var tbl_diagnosis = document.getElementById('icdCodeTable');

		if (document.getElementById('current_doc_nr_f'))
			trimString(document.getElementById('current_doc_nr_f'));
		if (document.getElementById('current_dept_nr_f'))
			trimString(document.getElementById('current_dept_nr_f'));
		//commented by VAN 06-12-08
		/*
		if (document.getElementById('cond_code')){
			if (!checkRadioButton('cond_code')){
				alert("Please select a condition at ER.");
				document.getElementById('cond_code').focus();
				return false;
			}
		}
		*/

		if(document.getElementById('result_code')){
			if (!checkRadioButton('result_code')){
				alert("Please select a result.");
				document.getElementById('result_code').focus();
				return false;
			}else{
				//added by VAN 06-28-08
				if ((document.getElementById('disp_hidden').value==4)||(document.getElementById('disp_hidden').value==8)){
				//alert((document.getElementById('death_date').value));
				//alert('here ='+(document.getElementById('death_date').value='')+" || "+(document.getElementById('death_date').value=' '));
					if (!(document.getElementById('death_date').value)||(seg_validDate==false)){
						alert("Please enter a death date.");
						document.getElementById('death_date').focus();
						return false;
					}
				}
			}
		}

		if(tbl_diagnosis.rows.length == 0){
			alert("Please type a diagnosis");
			tbl_diagnosis.focus();
			return false;
		}

		if(document.getElementById('disp_code')){
			if (!checkRadioButton('disp_code')){
				alert("Please select a dispostion.");
				document.getElementById('disp_code').focus();
				return false;
			}
		}

        if (($('date_text_d').value=='') || ($('date_text_d').value=='00/00/0000')){
            alert("Please enter the discharge date.");
            document.getElementById('date_text_d').value='';
            document.getElementById('date_text_d').focus();
            return false;
        }

								 if (($('time_text_d').value=='') || ($('time_text_d').value=='0:00') || ($('time_text_d').value=='00:00')){
						 alert("Please enter the discharge time.");
						 document.getElementById('time_text_d').value='';
						 document.getElementById('time_text_d').focus();
						 return false;
					}
		//commented by VAN 02-29-08
		/*
		if ( $('current_doc_nr_c') && ($F('current_doc_nr_c')==0)){
			alert("Select a Consulting Physician!");
			$('current_doc_nr_c').focus();
			return false;
		}else */
		/*
		if ( document.getElementById('current_doc_nr_f') && (document.getElementById('current_doc_nr_f').value==0)){
			alert("Select an Attending Physician!");
			document.getElementById('current_doc_nr_f').focus();
			return false;
		}else if ( document.getElementById('current_dept_nr_f') && (document.getElementById('current_dept_nr_f').value==0)){
			alert("Select an Attending Department!");
			document.getElementById('current_dept_nr_f').focus();
			return false;
		}else if ((document.getElementById('date_text_d').value=='')||(seg_validDate==false)){
			alert("Enter the Discharge Date!");
			document.getElementById('date_text_d').focus();
			return false;
		//commented by VAN 04-28-08
		/*
		}else if((document.getElementById('time_text_d').value=='') && (seg_validTime==false) && (document.getElementById('encounter_type').value!= 2)){
		//}else if (($F('time_text_d')=='')||(seg_validTime==false)||($F('encounter_type')== 2)){
			alert("Enter the Discharge Time!");
			document.getElementById('time_text_d').focus();
			return false;
		*/
		//added by VAN 06-12-08
		//}else
		if(admission_date > discharge_date){
			alert("Discharge date should not be earlier than the admission date!");
			document.getElementById('date_text_d').value='';
			document.getElementById('date_text_d').focus();
			return false;
		}else if(document.getElementById('time_text_d').value==''){
			document.getElementById('time_text_d').value = '00:00';
			return true;
		//added by VAN 06-12-08
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
	document.getElementById(elementId).value = '';
	document.getElementById(elementId).focus();
	//$(elementId).style.diplay = 'none';
}


//--- YAHOO.util.Event ----------//

function clrField(elId, ev){
	var clr = function(e){
		document.getElementById(elId).value = '';
		document.getElementById(elId).focus();
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

//----------------------added by VAN 03-31-08
function setVal1(value) {
	console.debug("Selected "+value);
}

var AJAXTimerID=0;

function populateICD_ICP(target, keyword){
	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	//alert('keyword= '+keyword);
	//alert(target);
	if (target=='icd'){
		keyword = document.getElementById('icdCode').value
		AJAXTimerID = setTimeout("xajax_populateICD_ICP("+target+",'"+keyword+"')",50);
	}else{
		keyword = document.getElementById('icpCode').value
		AJAXTimerID = setTimeout("xajax_populateICD_ICP("+target+",'"+keyword+"')",50);
	}
 }

//clear ajax Options for ICD/ICP
/*
function ajxClearOptions_ICD_ICP() {
	var optionsList;
	var el;

	el = document.getElementById("autoSuggestionsList");

	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}//end of function ajxClearOption_ICD_ICP
*/

function ajxClearOptions_ICD() {
	el = document.getElementById("autoSuggestionsList_icd");
	el.innerHTML = "";
}//end of function ajxClearOption_ICD_ICP

function ajxClearOptions_ICP() {
	el = document.getElementById("autoSuggestionsList_icp");
	el.innerHTML = "";
}//end of function ajxClearOption_ICD_ICP


function ajxAddOption_ICD_ICP(text, value, size, target){
	//alert("ajxAddOption_c : target = '"+target+"'; text = '"+text+"'; value = '"+value+"'; size = '"+size+"'");

	if (target=='icd')
		grpEl=document.getElementById("autoSuggestionsList_icd");
	else
		grpEl=document.getElementById("autoSuggestionsList_icp");

	if (size==1)
		grpEl.style.height = size * 35;
	else if (size==2)
		grpEl.style.height = size * 30;
	else if ((size>2)&&(size<=7))
		grpEl.style.height = size * 21;
	else
		grpEl.style.height = size * 16;

	if (size<=7){
		grpEl.style.overflowY = 'hidden';
	}else{
		grpEl.style.overflowY = 'scroll';
	}

	grpEl.style.overflowX = 'scroll';

	if (grpEl){
		text = value+" : "+text;
		//var opt = new Option(text, value);
		//<li onClick="fill(\''.$result["diagnosis_code"].'\');">'.$result["diagnosis_code"]." : ".$desc.'</li>
		var opt=document.createElement("LI");
		opt.id = value;
		//opt.innerText=text;
		opt.appendChild(document.createTextNode(text));
		//opt.setAttribute("onClick", "fill('"+value+"');hideDiv();");
		opt.setAttribute("onClick", "fill('"+value+"','"+target+"');");
		//<li onKeyDown="" onKeyPress="" onKeyUp=""></li>
		//opt.setAttribute("onKeyDown", "alert('down');");
		//opt.setAttribute("onKeyUp", "alert('up');");
		//opt.setAttribute("onKeyPress","alert('keypress');");
		grpEl.appendChild(opt);
	}

	var optionsList = grpEl.getElementsByTagName('LI');
	showDiv(target);
	//alert(grpEl.innerHTML);
}// end of function ajxAddOption


/*
function ajxAddOption_ICD_ICP(text, value, size){
	//alert("ajxAddOption_c : text = '"+text+"'; value = '"+value+"'");

	grpEl=document.getElementById("autoSuggestionsList");

	if (size==1)
		size = 2;

	 grpEl.size = size;

	if (grpEl){
		text = value+" : "+text;
		var opt = new Option(text, value);
		opt.id = value;
		opt.setAttribute("onClick", "fill('"+value+"');");
		grpEl.appendChild(opt);
	}
	var optionsList = grpEl.getElementsByTagName('OPTION');
	grpEl.selectedIndex = 0;
	showDiv();
	//alert(grpEl.innerHTML);
}// end of function ajxAddOption
*/

// added by VAN 04-02-08
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		$.post("gui_bridge/default/gui_medocs_icd_connect.php", {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				//$('#suggestions').show();
				showDiv();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup

function fill(thisValue, target) {
	if (target=='icd')
		$('#icdCode').val(thisValue);
	else
		$('#icpCode').val(thisValue);

	hideDiv(target)
	//setTimeout("$('#suggestions').hide();", 200);
}

function hideDiv(target){
	//setTimeout("$('#suggestions').hide();", 200);
	if (target=='icd')
		$J('#suggestions_icd').hide();
	else
		$J('#suggestions_icp').hide();
}

function showDiv(target){
	if (target=='icd')
		$J('#suggestions_icd').show();
	else
		$J('#suggestions_icp').show();
}

//added by VAN 02-03-08
// Author: Matt Kruse <matt@mattkruse.com>
// WWW: http://www.mattkruse.com/
function autoComplete (field, select1, property, forcematch) {
	var found = false;
	//select1 = document.getElementById('combo');
	for (var i = 0; i < select1.options.length; i++) {
	if (select1.options[i][property].toUpperCase().indexOf(field.value.toUpperCase()) == 0) {
		found=true; break;
		}
	}
	if (found) { select1.selectedIndex = i; }
	else { select1.selectedIndex = -1; }
	if (field.createTextRange) {
		if (forcematch && !found) {
			field.value=field.value.substring(0,field.value.length-1);
			return;
			}
		var cursorKeys ="8;46;37;38;39;40;33;34;35;36;45;";
		if (cursorKeys.indexOf(event.keyCode+";") == -1) {
			var r1 = field.createTextRange();
			var oldValue = r1.text;
			var newValue = found ? select1.options[i][property] : oldValue;
			if (newValue != field.value) {
				field.value = newValue;
				var rNew = field.createTextRange();
				rNew.moveStart('character', oldValue.length) ;
				rNew.select();
				}
			}
		}
	}

function ajxPromptDialog(encounter,encounter_type,aDate,code,doc_nr,dept_nr,create_id,target,type){
		var answer = confirm("The ICD code will be automatically save in the library. Are you sure you want to continue? If yes, just click OK otherwise CANCEL.");
		// alert(encounter+", "+encounter_type+", "+aDate+", "+code+", "+doc_nr+", "+dept_nr+", "+create_id+", "+target,type);
		 if (answer){
		    xajax_saveICDifnotExist(encounter,encounter_type,aDate,code,doc_nr,dept_nr,create_id,target,type);
		 }
}

function ReloadWindow(){
	window.location.href=window.location.href;
}

//---notification
function prepareAddNotificationCode(encounter_nr, notification, request_date){
    var srcTable, srcRows, srcTableBody;
    var i, isAdded, id;
    
    id = notification;
    
    if (srcTable=document.getElementById('notificationCodeTable')) {
        srcTableBody=srcTable.getElementsByTagName("tbody")[0];
        srcRows=srcTableBody.getElementsByTagName("tr");
    }
    
    isAdded=false;
    
    for (i=0;i<srcRows.length;i++) {
        rowid = srcRows[i].id.replace("notificationCodeRow","");
        
        if (id==document.getElementById("notificationCodeID"+rowid).value){
            isAdded=true;
            break;
        }
    }
   
    if (isAdded) {
        alert('Notification is already in the tray...');
    }else {
        xajax_addNotificationCode(encounter_nr, id, request_date);
    }
} 

function addNotification(){
    var encounter_nr = $J('#encounter_nr').val();
         
    if (($J('#notificationCode').val().length > 0) && ($J('#request_date').val().length > 0)){ 
        prepareAddNotificationCode(encounter_nr,$J('#notificationCode').val(),$J('#request_date').val());
    }else{
        
        if ($J('#request_date').val().length == 0){
            alert('Please enter a request date...');
            $J('#request_date').focus();
            return false;
        }
        
        if ($J('#notificationCode').val().length == 0){
            //alert('Please select a notification...');
            //$J('#notificationCode').focus();
            var desc = $J.trim($J('#notificationDesc').val());
            if (desc.length != 0){
                res = confirm('This notification does not exist in the database. Do you want to save it?');
               
                if (res){
                    var desc = $J('#notificationDesc').val();
                    xajax_InsertNotificationCode(encounter_nr, desc, $J('#request_date').val());
                    return false;
                } 
            }else{
                alert('Please select or put a notification...');
                $J('#notificationDesc').focus();
                return false;
            }    
        }
    }
    
}

function gui_addNotificationCodeRow(encounter_nr,id,desc,request_date) {
    var srcTable, srcRows, srcTableBody, newRowSrc, lastRowNo;
    var i,fontweight;
    
    if (srcTable=document.getElementById("notificationCodeTable")) {
        srcTableBody=srcTable.getElementsByTagName("tbody")[0];
        srcRows=srcTableBody.getElementsByTagName("tr");

        if (srcRows.length>0) lastRowNo=srcRows[srcRows.length-1].id.replace("notificationCodeRow","");

        lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;

        //fontweight = 'bold; color:#CC0000;';
        fontweight = 'normal';
        
        newRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" style="font-weight:'+fontweight+'" id="notificationCodeRow'+lastRowNo+'" >'+
                    
                    '<td width="*">'+
                        '<input type="hidden" id="notificationCodeID'+lastRowNo+'" name="notificationCodeID['+lastRowNo+']" value="'+id+'" >'+
                        '<input type="hidden" id="notificationCodeDesc'+lastRowNo+'" name="notificationCodeDesc['+lastRowNo+']" value="'+desc+'" >'+desc+
                    '</td>'+
                    '<td width="20%">'+request_date+'</td>'+
                    '<td align="center" width="5%">'+
                        '<input type="hidden" id="enCode'+lastRowNo+'" value="'+encounter_nr+'">'+
                        '<input type="button" id="notificationCodeRmv'+lastRowNo+'" value="x" onclick="xajax_rmvNotificationCode(\''+encounter_nr+'\',\''+id+'\',\''+lastRowNo+'\');" style="width:25px">'
                    '</td>'+
                    '</tr>';
    
        srcTableBody.innerHTML += newRowSrc;
        $J('#notificationCode').focus();
    }
    
    $J('#notificationCode').val('');
    $J('#notificationDesc').val('');
    $J('#request_date').val('');
}

function gui_rmvNotificationCodeRow(rowNum) {
    var destTable, destRows, rmvRow;
    rmvRow=document.getElementById("notificationCodeRow"+rowNum);
    if (destTable=document.getElementById("notificationCodeTable")) {
        destRows=destTable.getElementsByTagName("tbody")[0];
        // check if srcRows is valid and has more than 1 element
        if (destRows) {
            destRows.removeChild(rmvRow);
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
}

//----------------------------