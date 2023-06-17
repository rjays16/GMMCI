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
            case 11 :  word = "eleventh";
                         break;
            case 12 :  word = "twelfth";
                         break;
            case 13 :  word = "thirteenth";
                         break;
            case 14 :  word = "fourteenth";
                         break;
            case 15 :  word = "fifteenth";
                         break;
            case 16 :  word = "sixteenth";
                         break;
            case 17 :  word = "seventeenth";
                         break;
            case 18 :  word = "eighteenth";
                         break;
            case 19 :  word = "nineteenth";
                         break;
            case 20 : word = "twentieth";
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
	var order=new Array("first", "second", "third", "fourth", "fifth", "sixth", "seventh", "eighth", "ninth","tenth", "eleventh","twelfth","thirteenth","fourteenth","fifteenth","sixteenth","seventeenth","eighteenth","nineteenth","twentieth");
	return order.in_array(objValue);
}// end of function checkWord

function convertToWords(obj){
	var objValue = obj.value;
	var objName = obj.name;
	var word;
	
	if (objValue=="")
		obj.value = '';
	else{
	if (objValue>20){
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

//added by VAN
function stristr( haystack, needle, bool ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: stristr('Kevin van Zonneveld', 'Van');
    // *     returns 1: 'van Zonneveld'
    // *     example 2: stristr('Kevin van Zonneveld', 'VAN', true);
    // *     returns 2: 'Kevin '
 
    var pos = 0;
 
    pos = haystack.toLowerCase().indexOf( needle.toLowerCase() );
    if( pos == -1 ){
        return false;
    } else{
        if( bool ){
            return haystack.substr( 0, pos );
        } else{
            return haystack.slice( pos );
        }
    }
	}
	
function ucwords( str ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: _argos
    // *     example 1: ucwords('kevin van zonneveld');
    // *     returns 1: 'Kevin Van Zonneveld'
    // *     example 2: ucwords('HELLO WORLD');
    // *     returns 2: 'HELLO WORLD'
 
    return str.replace(/^(.)|\s(.)/g, function ( $1 ) { return $1.toUpperCase ( ); } );
}
	
function UpdateInformantAddress(){
		var informant_address;
		var brgyObj = $('m_residence_brgy');
		var munObj = $('m_residence_mun');
		var provObj = $('m_residence_prov');
		
		informant_address  = document.getElementById('m_residence_basic').value;
		//alert(brgyObj.options[brgyObj.selectedIndex].text);
		//alert(munObj.options[munObj.selectedIndex].text);
		if (informant_address!=''){
			//informant_address = informant_address+", "+ucwords(munObj.options[munObj.selectedIndex].text.toLowerCase());
			informant_address = informant_address+", "+ucwords(brgyObj.options[brgyObj.selectedIndex].text.toUpperCase())+", "+ucwords(munObj.options[munObj.selectedIndex].text.toUpperCase());
		}else{
			//informant_address = informant_address+" "+ucwords(munObj.options[munObj.selectedIndex].text.toLowerCase());
			informant_address = informant_address+" "+ucwords(brgyObj.options[brgyObj.selectedIndex].text.toUpperCase())+", "+ucwords(munObj.options[munObj.selectedIndex].text.toUpperCase());
		}
		
		if (stristr(munObj.options[munObj.selectedIndex].text, 'City',true)){
			
		}else{
			if (informant_address!=''){
				informant_address = informant_address+", "+ucwords(provObj.options[provObj.selectedIndex].text.toUpperCase());
			}else{
				informant_address = informant_address+" "+ucwords(provObj.options[provObj.selectedIndex].text.toUpperCase());
			}
		}	
		//alert('add = '+informant_address);		
		//informant_address = document.getElementById('m_residence_basic').value+" "+munObj.options[munObj.selectedIndex].text+", "+provObj.options[provObj.selectedIndex].text;
		document.getElementById('informant_address').value=informant_address;
	
	}
	
function preSet(){	

	if ($('informant_address').value==""){
		UpdateInformantAddress();
	}
		
	if ($('attendant_title').value=="")
		$('attendant_title').value = 'Resident Physician';
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
		if (objDeliveryMethodInfo.value=="")
			objDeliveryMethodInfo.value = "Caesarian";
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
	var dfc = document.fetaldeath_certificate;
	var a_type = $F('attendant_type');
	
	if((dfc.birth_rank_tmp[2].checked)&&($F('birth_rank_others')=="")){
		alert("Please specify the rank of delivery of the fetus.");
		$('birth_rank_others').focus();
		return false;
	}else if((dfc.delivery_method_tmp[1].checked)&&($F('delivery_method_info')=="")){
		alert("Please enter the method of delivery of the fetus.");
		$('delivery_method_info').focus();
		return false;
	}else if ($F('birth_order')==""){
		alert("Please enter the order of birth of the fetus.");
		$('birth_order').focus();
		return false;
	}else if ($F('birth_weight')==""){
		alert("Please enter the weight at birth of the fetus.");
		$('birth_weight').focus();
		return false;
	}else if ($F('m_age')==""){
		alert("Please enter the age of the mother at the time of this delivery.");
		$('m_age').focus();
		return false;
	}else if ($F('m_total_alive')==""){
		alert("Please enter the total number of children born alive.");
		$('m_total_alive').focus();
		return false;
	}else if ($F('m_still_living')==""){
		alert("Please enter the number of children still living.");
		$('m_still_living').focus();
		return false;
	}else if ($F('m_now_dead')==""){
		alert("Please enter the number of children born alive but are now dead.");
		$('m_now_dead').focus();
		return false;
	/*}else if ($F('f_age')==""){
		alert("Please enter the age of the father at the time of this delivery.");
		$('f_age').focus();
		return false;
	
	}else if (($F('parent_marriage_date')!="")&&($F('parent_marriage_place')=="")){
		alert("Please enter the place of marriage of the parents.");
		$('parent_marriage_place').focus();
		return false;
	
	}else if (($F('parent_marriage_date')=="")&&($F('parent_marriage_place')!="")){
		alert("Please enter the date of marriage of the parents.");
		$('parent_marriage_date').focus();
		return false;
	*/	
	}else if ($F('pregnancy_length')==""){
		alert("Please enter the length of pregnancy.");
		$('pregnancy_length').focus();
		return false;
	}else if ((a_type.substring(0,1)=="5") && ($F('attendant_type_others')=="")){
		alert("Please enter the type of attendant.");
		$('attendant_type_others').focus();
		return false;
	}else if ($F('death_time')==""){
		alert("Please enter the time of death.");
		$('death_time').focus();
		return false;
	}else if ($F('attendant_name')=="0"){
		alert("Please enter the name of the attendant.");
		$('attendant_name').focus();
		return false;
	}else if ($F('attendant_title')==""){
		alert("Please enter the title of the attendant.");
		$('attendant_title').focus();
		return false;
	}else if ($F('attendant_address')==""){
		alert("Please enter the address of the attendant.");
		$('attendant_address').focus();
		return false;
	}else if ($F('attendant_date_sign')==""){
		alert("Please enter the signing date of the attendant.");
		$('attendant_date_sign').focus();
		return false;
	//}else if ((dfc.corpse_disposal.substring(0,1)=="3") && ($F('corpse_disposal_others')=="")){
	//	alert("Please specify the manner of corpse disposal.");
	//	$('corpse_disposal_others').focus();
	//	return false;			
	}else if ($F('informant_name')==""){
		alert("Please enter the name of the informant.");
		$('informant_name').focus();
		return false;
	}else if ($F('informant_relation')==""){
		alert("Please enter the relationship of the informant to the fetus.");
		$('informant_relation').focus();
		return false;
	}else if ($F('informant_address')==""){
		alert("Please enter the address of the informant.");
		$('informant_address').focus();
		return false;
	}else if ($F('informant_date_sign')==""){
		alert("Please enter the signing date of the informant.");
		$('informant_date_sign').focus();
		return false;
	}else if ($F('encoder_name')==""){
		alert("Please enter the name of the encoder.");
		$('encoder_name').focus();
		return false;
	}else if ($F('encoder_title')==""){
		alert("Please enter the title of the encoder.");
		$('encoder_title').focus();
		return false;
	}else if ($F('encoder_date_sign')==""){
		alert("Please enter the signing date of the encoder.");
		$('encoder_date_sign').focus();
		return false;
	}

	return true;
}// end of function chckForm

	//added by VAN 07-31-09
	function checkFather(obj){
		
		var objValue = obj.value;
		if ((objValue=="n/a") || (objValue=="N/A")){
			$('f_name_first').value = "n/a";		
			$('f_name_middle').value = "n/a";
			$('f_name_last').value = "n/a";
			
			$('f_citizenship').value = "n/a";
			$('f_religion').value = 105;
			$('f_occupation').value = 359;
			$('f_age').value = "n/a";
			
			$('name_middle').value = "";
			$('name_last').value = $('name_middle2').value;
			
			return false;
		}else{
			if (obj.id=='f_name_first'){
				if (($('f_name_middle').value)&&($('f_name_middle').value!='n/a'))
					$('f_name_middle').value = $('f_name_middle').value;
				else
					$('f_name_middle').value = "";
				
				if (($('f_name_last').value)&&($('f_name_last').value!='n/a'))
					$('f_name_last').value = $('f_name_last').value;
				else
					$('f_name_last').value = "";
					
			}else if (obj.id=='f_name_middle'){
				if (($('f_name_first').value)&&($('f_name_first').value!='n/a'))
					$('f_name_first').value = $('f_name_first').value;
				else
					$('f_name_first').value = "";
				
				if (($('f_name_last').value)&&($('f_name_last').value!='n/a'))
					$('f_name_last').value = $('f_name_last').value;
				else
					$('f_name_last').value = "";
			}else if (obj.id=='f_name_last'){
				if (($('f_name_first').value)&&($('f_name_first').value!='n/a'))
					$('f_name_first').value = $('f_name_first').value;
				else
					$('f_name_first').value = "";
					
				if (($('f_name_middle').value)&&($('f_name_middle').value!='n/a'))
					$('f_name_middle').value = $('f_name_middle').value;
				else
					$('f_name_middle').value = "";	
			}	
			
			return true;
		}	
	}
	
	function parentName(obj){
		var parentType = obj.name.substring(0,1).toLowerCase();

		var is_exists, f_name_first, f_name_first_new, fname1, fname2;
				
		if (parentType=="m"){
		   // mother

			if (($F('m_name_first')=="")&&($F('m_name_middle')=="")&&($F('m_name_last')==""))
				$('m_fullname').innerHTML = "(mother's name)";
			else
				$('m_fullname').innerHTML = $F('m_name_first')+" "+$F('m_name_middle')+" "+$F('m_name_last');
		}else{
		   //father

			if (($F('f_name_first')=="")&&($F('f_name_middle')=="")&&($F('f_name_last')==""))
				$('f_fullname').value = "(father's name)";
			else{
				//edited by VAN 08-28-08
				f_name_first = $F('f_name_first');
				is_exists = f_name_first.search(/,/i);
				if (is_exists>0){
					f_name_first_new = f_name_first.split(",");
					fname1 = f_name_first_new[0];
					fname2 = f_name_first_new[1].replace(" ","");
				}else{
					fname1 = f_name_first;
					fname2 = "";
				}
				
				if (fname2)
					fname2 = ", "+fname2;
							
				$('f_fullname').value = fname1+""+fname2+" "+$F('f_name_middle')+" "+$F('f_name_last');
			}	
		}
	}/* end of function parentName */
	
	function updateChildSName(){
		var mother_Lname = $('m_name_last').value;
		var father_Lname = $('f_name_last').value;
		var father_Fname = $('f_name_first').value;
		
        var parent_marriage_date = $('parent_marriage_date').value;
        
        if (((parent_marriage_date=='')) || ((father_Fname==null) || (father_Fname=="") || (father_Fname==" ") || (father_Fname=="n/a") || (father_Fname=="N/A"))){
			father_Lname = "";
		
		//alert('mother_Lname = '+mother_Lname);
		    if ((mother_Lname)&&(father_Lname=="")){
			//alert("1");
			$('name_last').value = mother_Lname;
			$('name_middle').value = "";
		}else{
			//alert("2");
			$('name_last').value = father_Lname;
			$('name_middle').value = mother_Lname;
		}	
        }else if (((parent_marriage_date!='')) && !((father_Fname==null) || (father_Fname=="") || (father_Fname==" ") || (father_Fname=="n/a") || (father_Fname=="N/A"))){
            $('name_last').value = father_Lname;
            $('name_middle').value = mother_Lname;
        }    	

	}
//-----------------------------------

