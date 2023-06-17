//--- adapted from death_cert.js and cert_birth_certificate.php --- pet --- added on june 02, 2008 ---

	/*	
			The trimString function will trim the string, i.e., it will remove 
			all whitespaces in the beginning and end of a string.  Only a single
			whitespace appears between tokens/words.
			
			input: object
			output: trimmed object (string) value
	*/
	
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," "); 
}// end of function trimString

var seg_validTime=false;

function setFormatTime(thisTime){
	var stime = thisTime.value;
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	
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
	
	if (hour==0){
		 hour = 12;
		 $('selAMPM').value = "A.M.";		
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 $('selAMPM').value = "P.M.";
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

var countingNumber = true;
var wholeNumber = false;

function convertword(objValue){
	var word;
	objValue = parseInt(objValue);
	switch(objValue){
		case 1 :  word = "first";
					 break;
		case 2 :  word = "second";
					 break;
		case 3 :  word = "third";
					 break;
		case 4 :  word = "fourth";
					 break;
		case 5 :  word = "fifth";
					 break;
		case 6 :  word = "sixth";
					 break;
		case 7 :  word = "seventh";
					 break;
		case 8 :  word = "eighth";
					 break;
		case 9 :  word = "ninth";
					 break;
		case 10 : word = "tenth";
					 break;
	}
	return word;
}// end of function convertword

//check if the object value exists in the array
Array.prototype.in_array = function ( obj ) {
	var len = this.length;
	for ( var x = 0 ; x <= len ; x++ ) {
		if ( this[x] == obj ) return true;
	}
return false;	
}

function updateBirthRank(){
	$('birth_rank').value =$('birth_rank_others').value;
}// end of function updateBirthRank

function checkWord(objValue){
	var order=new Array("first", "second", "third", "fourth", "fifth", "sixth", "seventh", "eighth", "ninth","tenth");
	return order.in_array(objValue);
}// end of function checkWord

function convertToWords(obj){
	var objValue = obj.value;
	var objName = obj.name;
	var word;
	
	if (objValue=="")
		obj.value = '';
	else{
	if (objValue>10){
		alert('Are you sure of the order you have entered?');
		obj.value="";
		obj.focus();
		return false;
	}else{
		if (isNaN(objValue)){
			valid = checkWord(objValue);
			if (valid){
				obj.value = objValue;	
			}else{
				alert('Incorrect input. Please check the spelling.');
				obj.value = "";	
			}		
		}else{
			word = convertword(objValue);
			obj.value = word;	
		}	
	}
	}
}// end of function convertToWords
	
function EnableBirthRank(){
	var objBirthRank = document.fetaldeath_certificate.birth_type_tmp;
	var objBirthRankOthers = document.fetaldeath_certificate.birth_rank_tmp;
	
	if(objBirthRank[0].checked){
		objBirthRankOthers[0].disabled = true;	
		objBirthRankOthers[1].disabled = true;	
		objBirthRankOthers[2].disabled = true;
		$('birth_rank_others').readOnly = true;	
		$('birth_rank_others').disabled = true;
	}else if(objBirthRank[1].checked){
		objBirthRankOthers[0].disabled = false;	
		objBirthRankOthers[1].disabled = false;	
		objBirthRankOthers[2].disabled = true;	
		$('birth_rank_others').readOnly = true;	
		$('birth_rank_others').disabled = true;
	}else if(objBirthRank[2].checked){
		objBirthRankOthers[0].disabled = false;	
		objBirthRankOthers[1].disabled = false;	
		objBirthRankOthers[2].disabled = false;	
		$('birth_rank_others').readOnly = false;	
		$('birth_rank_others').disabled = false;		
	}
}// end of function EnableBirthRank
	
function preSet(){	
	if ($('attendant_title').value=="")
		$('attendant_title').value = 'Physician';
	if ($('attendant_address').value=="")		
		$('attendant_address').innerHTML = 'Gonzales Maranan Medical Center Incorporated, Quezon Ave., Digos City';
	EnableBirthRank();
	if ($('delivery_method_info').value=="")
		$('delivery_method_info').disabled = true;
    if ($('corpse_disposal_others').value=="")
		$('corpse_disposal_others').disabled = true;
}// end of function preset

//number only and decimal point is allowed
function keyPressHandler(e){
	var unicode=e.charCode? e.charCode : e.keyCode
	if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
		return false //disable key press
}// end of function keyPressHandler

function chkInteger(obj,noZero){
	var objValue = obj.value;
	var objName = obj.name;
	
	if (objValue=="")
		return false;
	if (	isNaN(parseInt(objValue, 10)) || (parseInt(objValue, 10) < 0) ||
			((noZero) && (parseInt(objValue, 10)==0)) ){
		switch (objName){
			case 'birth_order':
					msg=" Invalid birth order! \n A valid number is expected.";
				break;
			case 'birth_rank_tmp':
			case 'birth_rank_others':
					msg=" Invalid birth rank! \n A valid number is expected.";
					$('birth_rank').value='';
				break;
			case 'm_total_alive':
					msg=" Invalid total number of children born alive! \n A valid number is expected.";
				break;
			case 'm_still_living':
					msg=" Invalid number of children still living includin this birth! \n A number is expected.";
				break;
			case 'm_now_dead':
					msg=" Invalid number of children born alive but are now dead! \n A valid number is expected.";
				break;
			case 'm_age':
					msg=" Invalid age of the mother! \n A valid number is expected.";
				break;
			case 'f_age':
					msg=" Invalid age of the father! \n A valid number is expected.";
				break;
			case 'birth_weight':
					msg=" Invalid weight at birth! \n A valid number is expected.";
				break;					
		}
		alert(msg);
		obj.value="";
		obj.focus();
		return false;
	}
	
	obj.value = parseInt(objValue, 10);
	return true;
}// end of function chkInteger

function chkDecimal(obj){
	var objValue = obj.value;
	var objName = obj.name;
	var ms='';

	if (objValue=="")
		return false;
	if (!parseFloat(objValue)){
		switch (objName){
			case 'm_age':
					msg=" Invalid age of the mother! \n A valid number is expected.";
				break;
			case 'f_age':
					msg=" Invalid age of the father! \n A valid number is expected.";
				break;
			case 'birth_weight':
					msg=" Invalid wieght at birth! \n A valid number is expected.";
				break;					
		}
		alert(msg);
		obj.value="";
		obj.focus();
		return false;
	}
	obj.value = parseFloat(objValue);
	return true;
}// end of function chkDecimal

function typeOfBirth(obj){
	var objValue = obj.value;
	var objBirthRank = document.fetaldeath_certificate.birth_rank_tmp;
	var objBirthRankOthers = document.fetaldeath_certificate.birth_rank_others;
	
	if (objValue==1){
		objBirthRank.value = '';
		objBirthRankOthers.value = '';
		for(var i=0; i<objBirthRank.length;i++ ){
			objBirthRank[i].disabled = true;
			objBirthRank[i].checked = false;
		} 
		objBirthRankOthers.disabled = true;

	}else if (objValue==2){
		objBirthRankOthers.value = '';
		for(var i=0; i<objBirthRank.length-1;i++ ){
			objBirthRank[i].disabled = false;
		} 
		objBirthRank[2].disabled = true;
		objBirthRank[0].checked = true;
		objBirthRankOthers.disabled = true;

	}else{
		for(var i=0; i<objBirthRank.length;i++ ){
			objBirthRank[i].disabled = false;
		} 
		objBirthRank[0].checked = true;
	}
	$('birth_type').value=objValue;
	$('birth_rank').value='';
}// end of function typeOfBirth

function rankOfBirth(obj){	
	var objValue = obj.value;
	var objBirthRankOthers = $('birth_rank_others');

	if ((objValue!="first")&&(objValue!="second")){
		objBirthRankOthers.disabled = false;
		$('birth_rank').value='';
		objBirthRankOthers.readOnly = false;
	}else{
		objBirthRankOthers.disabled = true;
		objBirthRankOthers.value = '';
	}
	$('birth_rank').value=objValue;
	$('birth_rank_others').value='';
}// end of function rankOfBirth

function typeOfAttendant(obj){
	var objValue = obj.value;
	var objAttendantTypeOthers = $('attendant_type_others');

	if (objValue=="5"){
		objAttendantTypeOthers.disabled = false;
	}else{
		objAttendantTypeOthers.value = "";
		objAttendantTypeOthers.disabled = true;
	}
	switch(objValue){
		case "1":
			objValue = objValue + " - Physician";
			break;
		case "2":
			objValue = objValue + " - Nurse";
			break;
		case "3":
			objValue = objValue + " - Midwife";
			break;
		case "4":
			objValue = objValue + " - Hilot";
			break;
		case "5":
			objValue = objValue + " - " + objAttendantTypeOthers.value;
			break;
		case "6":
			objValue = objValue + " - None";
			break;
	}
	$('attendant_type').value=objValue;

}// end of function typeOfAttendant

function occurrenceOfFetalDeath(obj){
	var objValue = obj.value;
	
	switch(objValue){
		case "1":
			objValue = objValue + " - Before labor";
			break;
		case "2":
			objValue = objValue + " - During labor/delivery";
			break;
		case "3":
			objValue = objValue + " - Unknown";
			break;
	}
	$('death_occurrence').value=objValue;
}// end of function occurrenceOfFetalDeath 

function checkIfAutopsied(obj){
	var objValue = obj.value;
	
	switch(objValue){
		case "1":
			objValue = objValue + " - Yes";
			break;
		case "2":
			objValue = objValue + " - No";
			break;
	}
	$('is_autopsy').value=objValue;
}// end of function checkIfAutopsied 

function typeOfDisposal(obj){
	var objValue = obj.value;
	var objDisposalTypeOthers = $('corpse_disposal_others');

	if (objValue=="3"){
		objDisposalTypeOthers.disabled = false;
	}else{
		objDisposalTypeOthers.value = "";
		objDisposalTypeOthers.disabled = true;
	}
	switch(objValue){
		case "1":
			objValue = objValue + " - Burial";
			break;
		case "2":
			objValue = objValue + " - Cremation";
			break;
		case "3":
			objValue = objValue + " - " + objDisposalTypeOthers.value;
			break;
	}
	$('corpse_disposal').value=objValue;

}// end of function typeOfDisposal

function methodOfDelivery(obj){
	var objValue = obj.value;
	var objDeliveryMethodInfo = $('delivery_method_info');

	if (objValue=="2"){
		objDeliveryMethodInfo.disabled = false;
	}else{
		objDeliveryMethodInfo.value = "";
		objDeliveryMethodInfo.disabled = true;
	}
	
	switch(objValue){
		case "1":
			objValue = objValue + " - Normal spontaneous vertex";
			break;
		case "2":
			objValue = objValue + " - "+objDeliveryMethodInfo.value;
			break;
	}
	$('delivery_method').value=objValue;
}// end of function methodOfDelivery

function printFetalDeathCert(id){
	if (id==0) 
		id="";
	if (window.showModalDialog){  //for IE
		window.showModalDialog("cert_death_fetal_pdf.php?id="+id,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
	}else{
		window.open("cert_death_fetal_pdf.php?id="+id,"FetalDeathCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
	}
}// end of function printFetalDeathCert

function chckForm(d){
	var msg='';
	msg= " $F('birth_rank') ='"+$F('birth_rank')+"'; \n $F('birth_rank_others') ='"+$F('birth_rank_others')+"'";
	var a_type = $F('attendant_type');
	
	if((document.fetaldeath_certificate.birth_rank_tmp[2].checked)&&($F('birth_rank_others')=="")){
		alert("Please specify the type of birth.");
		$('birth_rank_others').focus();
		return false;

	}else	if (($F('is_married')==1) && (($F('parent_marriage_date')=="")||($F('parent_marriage_place')==""))){
		alert("Please enter the date and place of marriage.");
		$('parent_marriage_date').focus();
		return false;
	}else if ((a_type.substring(0,1)=="5") && ($F('attendant_type_others')=="")){
		alert("Please enter the type of attendant.");
		$('attendant_type_others').focus();
		return false;
	}

	msg = "2 : $F('attendant_type') ='"+$F('attendant_type')+"'";

	return true;
}// end of function chckForm


