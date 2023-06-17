	function trimString(obj){
		obj.value = obj.value.replace(/^\s+|\s+$/g,"");
		obj.value = obj.value.replace(/^\s+$/g," ");
	}
	
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

	function typeOfBirth(obj){
		var objValue = obj.value;
		var objBirthRank = document.death_certificate.birth_rank_tmp;
		var objBirthRankOthers = document.death_certificate.birth_rank_others;
		
		//alert("typeOfBirth : objValue = '"+objValue+"'; objBirthRank ='"+objBirthRank+"'; objBirthRank.length ='"+objBirthRank.length+"'");

		if (objValue==1){
			objBirthRank.value = '';
			objBirthRankOthers.value = '';
			for(var i=0; i<objBirthRank.length;i++ ){
			 	objBirthRank[i].disabled = true;
			 	objBirthRank[i].checked = false;
			} 
			objBirthRankOthers.disabled = true;
		//added by VAN 05-27-08
		}else if (objValue==2){
			for(var i=0; i<objBirthRank.length-1;i++ ){
			 	objBirthRank[i].disabled = false;
			} 
			objBirthRank[2].disabled = true;
		 	objBirthRank[0].checked = true;
			objBirthRankOthers.disabled = true;
		//-------------	
		}else{
			
			for(var i=0; i<objBirthRank.length;i++ ){
			 	objBirthRank[i].disabled = false;
			} 
		 	objBirthRank[0].checked = true;
//			objBirthRankOthers.disabled = false;
			
		}
		$('birth_type').value=objValue;
		$('birth_rank').value='';
		//alert("typeOfBirth : $F('birth_rank') ='"+$F('birth_rank')+"'");
	}/* end of function typeOfBirth */

 	function rankOfBirth(obj){
		var objValue = obj.value;
		var objBirthRankOthers = $('birth_rank_others');
		
		//alert("rankOfBirth: objValue = '"+objValue+"'; objBirthRankOthers.value ='"+objBirthRankOthers.value+"'");

		//if ((objValue!="1")&&(objValue!="2")){
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
		//alert("rankOfBirth : $F('birth_rank') ='"+$F('birth_rank')+"'");
	}/* end of function rankOfBirth */

	var countingNumber = true;
	var wholeNumber = false;
	
	//added by VAN 05-26-08
	//number only and decimal point is allowed
	function keyPressHandler(e){
		var unicode=e.charCode? e.charCode : e.keyCode
		//if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
		if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
			return false //disable key press
	}
	
	function UpdateDeathDate(mode){
		if (mode){
			$('death_date').value=$('attended_to_date').value	
		}else{
			$('attended_to_date').value=$('death_date').value	
		}
	}
	//------------------------------------
	function chkInteger(obj,noZero){
		//alert('here');
//	function chkInteger(obj){
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
				case 'pregnancy_length':
						msg=" Invalid length of pregnancy! \n A valid number is expected.";
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

	function checkTime(obj){
		var objValue = parseInt(obj.value, 10);
		var objName = obj.name;
		var maxValue;
		
//		alert("obj.value = '"+obj.value+"'; objValue = '"+objValue+"'");

		if (obj.value=="")
			return false;

		switch (objName){
			case 'hours':
				msg=" Invalid number of hours! \n A valid number is expected.";
				maxValue = 24;
				break;
			case 'minutes':
				msg=" Invalid number of minutes! \n A valid number is expected.";
				maxValue = 60;
				break;
			case 'sec':
				msg=" Invalid number of seconds! \n A valid number is expected.";
				maxValue = 60;
				break;
		}

		if ( isNaN(objValue) || (objValue < 0) || (objValue >= maxValue) ){
			alert(msg);
			obj.value = '';
			obj.focus();
			return false;
		}
		return true;
	}// end of function checkTime
	

function multipleBirth(obj){
	var objValue = obj.value;
	var objMultipleSpecify = $('multiple_birth_specify');
	
	if((objValue !="1") && (objValue != "2")){
		$('multiple_birth').value = '';
		objMultipleSpecify.disabled = false;
	}else{
		objMultipleSpecify.disabled = true;
		objMultipleSpecify.value = '';
	}
	$('multiple_birth').value = objValue;
}/* end function multipleBirth */

	var seg_validTime=false;

	function setFormatTime(thisTime){
	//	var time = $('time_text_d');
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

function getBirthdate(obj){
	var age = parseInt(obj.value);
	var bdate = document.death_certificate.death_date.value;
	var dateNow = new Date();
	
	//		alert("getBirthdate : age = '"+age+"' \n obj.value = '"+obj.value+"'");
	
	if (!isNaN(age)){
		document.death_certificate.death_date.value=(dateNow.getMonth()+1)+"/"+dateNow.getDate()+"/"+(dateNow.getFullYear()-age);
		//document.death_certificate.death_date.value="";
		document.death_certificate.death_age.value=age;
	}else{
		if (obj.value){
			if (bdate){// retain the old value if existing
				getAge(document.death_certificate.date_birth);
			}else{//reset birthdate and age
				document.death_certificate.death_age.value='';
			}
		}else{
			document.death_certificate.death_date.value='';
			document.death_certificate.death_age.value='';				
		}
	}
}
//added by VAN
function updateBdate(){
	var bdate;
	var month, day;
	
	if ($('birth_month').value<10)
		month = "0"+$('birth_month').value;
	else	
		month = $('birth_month').value;
		
	if ($('birth_day').value<10)
		day = "0"+$('birth_day').value;
	else
		day = $('birth_day').value;
		
	bdate = month+"/"+day+"/"+$('birth_year').value;
	$('date_birth2').value =bdate;
	
	getAge($('death_date'));
	expandcontract('Row1','');
	expandcontract('Row2','none');
}

function convertToWords(obj){
		//alert(objvalue);
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
		//$('birth_rank_others').focus();
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
	
}

function updateBirthRank(){
	$('birth_rank').value =$('birth_rank_others').value;	
}

function showforchild(){
	if($('death_age').value<7){
		expandcontract('Row1','');
		expandcontract('Row2','none');	
	}else{
		expandcontract('Row1','none');
		expandcontract('Row2','');	
	}
}

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
		//alert(objValue);
		//alert(word);
		return word;
	}
	
	//check if the object value exists in the array
	Array.prototype.in_array = function ( obj ) {
	var len = this.length;
	for ( var x = 0 ; x <= len ; x++ ) {
		if ( this[x] == obj ) return true;
	}
	return false;	
	}
	
	
	function checkWord(objValue){
		var order=new Array("first", "second", "third", "fourth", "fifth", "sixth", "seventh", "eighth", "ninth","tenth");
		//alert(objValue);
		//alert(order.in_array(objValue));
		return order.in_array(objValue);
	}
//--------------------
function getAge(obj){
	var dob;
	var valid;
		//  mm/dd/yyyy
	//alert('hello');	
	try{
		
		valid = IsValidDate(obj,'MM/dd/yyyy');
		dod = obj.value;
		dob = $('date_birth2').value; 
		//alert(dod+" - "+dob);
		if(dod == ""){
			
			//alert("dod2='"+dod+"'");
			$('death_age').value = '';
			$('death_months').value = '';
			$('death_days').value = '';
			//disable textfield under 1 day
			$('hours').value = '';
			$('minutes').value = '';
			$('sec').value = '';
			$('hours').disabled = true;
			$('minutes').disabled = true;
			$('sec').disabled = true;
			$('d_citizenship').focus();
			return false;
		}
		//alert('hello');		
		var dodMonth = dod.substring(0,2);
		var dodDay = dod.substring(3,5);
		var dodYear = dod.substring(6,10);
		
		var dobMonth = dob.substring(0,2);
		var dobDay = dob.substring(3,5);
		var dobYear = dob.substring(6,10);
		
		//birthday
		var pastDate = new Date(2000,dobMonth-1,dobDay);
		//deathday
		var presentDate = new Date(2000,dodMonth-1,dodDay);
		
		//alert("presentDate="+presentDate);
		//alert(parseInt(dodYear)+" - "+parseInt(dobYear));
		var age = parseInt(dodYear) - parseInt(dobYear);
		
		//alert("age='"+age+"'");
		//alert("ageYear="+presentDate.getTime()+"\n pastDate.getTime()="+pastDate.getTime());
		
		var ageYear = (presentDate.getTime()-pastDate.getTime())/31536000000;///86400000;
		
		//alert("ageYear="+ageYear);
		age = age + ageYear;
		//alert("1 : age="+ age);
		
		if(age<1){
			var ageMonth = parseInt(age*12);
//			var ageDay = parseInt(((age*12)-ageMonth) * 30);
			var ageDay = ((age*12)-ageMonth) * 30;
			
			//alert("age='"+age+"'\n ageMonth='"+ageMonth+"' \n ageDay='"+ageDay+"'\n Math.round(ageDay)='"+Math.round(ageDay)+"'");
			//below 1 year
			if(ageMonth == 0 && Math.round(ageDay)<1){
				// under 1 day
				/*
				$('death_age').value = '';
				$('death_months').value = '';
				$('death_days').value = '';
				*/
				$('death_age').value = 0;
				$('death_months').value = 0;
				$('death_days').value = 0;
				//disable textfields under 1 year
				//$('death_age').disabled = true;
				//$('death_months').disabled = true;
				//$('death_days').disabled = true;			
				
				$('hours').disabled = false;
				$('minutes').disabled = false;
				$('sec').disabled = false;
				
			}else{ //under 1 year old
				//$('death_age').disabled = true;
				//$('death_months').disabled = false;
				//$('death_days').disabled = false;
				
				//alert("age="+age+" ageMonth="+ageMonth+"ageDay="+ageDay);
				$('death_months').value = parseInt(ageMonth);
				$('death_days').value = parseInt(Math.round(ageDay));
				
				//$('death_age').value = '';
				$('death_age').value = 0;
				//disable textfield under 1 day
				/*
				$('hours').value = '';
				$('minutes').value = '';
				$('sec').value = '';
				*/
				$('hours').value = 0;
				$('minutes').value = 0;
				$('sec').value = 0;
				$('hours').disabled = true;
				$('minutes').disabled = true;
				$('sec').disabled = true;
			}
			
			if(ageMonth == 0 && ageDay>= 0 && ageDay<= 7){
				expandcontract('Row1','');
				expandcontract('Row2','none');
				$('age0to7days').value=1;
			}else{
				expandcontract('Row1','none');
				expandcontract('Row2','');
				$('age0to7days').value=0;
			}
		}else{
			//above 1 year
			//$('death_age').disabled = false;
			//$('death_months').value = '';
			//$('death_days').value = '';
			$('death_months').value = 0;
			$('death_days').value = 0;
			//$('death_months').disabled = true;
			//$('death_days').disabled = true;
			
			//disable textfield under 1 day
			//$('hours').value = '';
			//$('minutes').value = '';
			//$('sec').value = '';
			$('hours').value = 0;
			$('minutes').value = 0;
			$('sec').value = 0;
			$('hours').disabled = true;
			$('minutes').disabled = true;
			$('sec').disabled = true;
			
			expandcontract('Row1','none');
			expandcontract('Row2','');
			$('age0to7days').value=0;
			if ((isNaN(age)) ||(dobMonth==0)||(dobDay==0)||(dobYear==0)){
				document.death_certificate.death_age.value='';
			}else{
				//alert("death_age="+age);
				//$('death_age').value = age;
				//$('death_age').value = (parseFloat(age)).toFixed(2);
				if ((parseFloat(age))<=0)
					$('death_age').value = 0;
				else	
					$('death_age').value = Math.floor((parseFloat(age)));
//				$('d_citizenship').focus();
				//document.death_certificate.death_age.value=age;
				//document.death_certificate.d_citizenship.focus();
			}	
		}
		//$('d_citizenship').focus();
	}catch(e){
		//alert('hello');
		document.death_certificate.death_age.value='';				
	}
	//alert(document.death_certificate.death_age.value);
}

function expandcontract(tbodyid,dis) {
  document.getElementById(tbodyid).style.display = dis;
}

function IsAttendant(obj){
	var d = $('if_attendant');
	if(obj.checked){
		expandcontract(d,'');
	}else{
		expandcontract(d,'none');	
	}
}


function mannerOfDeath(obj){
		var objValue = obj.value;
		var objMannerOfDeathOthers = $('death_manner_info');

		var objMannerOfDeathAccident = $('death_manner_accident');

		if (objValue=="4"){
			objMannerOfDeathOthers.disabled = false;
		}else{
			objMannerOfDeathOthers.value = "";
			objMannerOfDeathOthers.disabled = true;
		}
		
		if (objValue=="3"){
			objMannerOfDeathAccident.disabled = false;
		}else{
			objMannerOfDeathAccident.value = "";
			objMannerOfDeathAccident.disabled = true;
		}
		
		switch(objValue){
			case "1":
				objValue = objValue + " - Homicide";
				break;
			case "2":
				objValue = objValue + " - Suicide";
				break;
			case "3":
				objValue = objValue + " - Accident";
				break;
			case "4":
				objValue = objValue + " - "+objMannerOfDeathOthers.value;
				break;
		}
		$('death_manner').value=objValue;
}// end of function mannerOfDeath 

	function typeOfAttendant(obj){
		var objValue = obj.value;
		var objAttendantTypeOthers = $('attendant_type_others');

		if (objValue=="5"){
			objAttendantTypeOthers.disabled = false;
		}else{
			objAttendantTypeOthers.value = "";
			objAttendantTypeOthers.disabled = true;
		}
		if (objValue=="4"){ 
			expandcontract("if_attendant",'none');
		}else{
			expandcontract("if_attendant",'');
		}

		switch(objValue){
			case "1":
				objValue = objValue + " - Private Physician";
				break;
			case "2":
				objValue = objValue + " - Public Health Officer";
				break;
			case "3":
				objValue = objValue + " - Hospital Authority";
				break;
			case "4":
				objValue = objValue + " - None";
				break;
			case "5":
				objValue = objValue + " - " + objAttendantTypeOthers.value;
				break;
		}
		$('attendant_type').value=objValue;
	}/* end of function typeOfAttendant */

	function certificationOfDeath(obj){
		var objValue = obj.value;
		var objDeathTime = $('death_time');
		var objselAMPM = $('selAMPM');

		if (objValue=="1"){
			objDeathTime.disabled = false;
			objselAMPM.disabled = false;
		}else{
			objDeathTime.value = "";
			objDeathTime.disabled = true;
			objselAMPM.value = 'A.M.';
			objselAMPM.disabled = true;
		}
	}// end of function certificationOfDeath
	
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

	function printDeathCert(id){
		if (id==0) 
			id="";
		if (window.showModalDialog){  //for IE
			window.showModalDialog("cert_death_pdf.php?id="+id,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}else{
			window.open("cert_death_pdf.php?id="+id,"deathCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}
	}/* end of function printBirthCert */
	
	/*
	//added by VAN 03-01-08
	function chkDeathDate(d){
		alert('check death');
		var birthdate = new Date($F('birth_date'));
		var deathdate = new Date($F('death_date'));
		
		if (Date.parse(birthdate) > Date.parse(deathdate)){	
			alert("Death date should not be earlier than the birth date");
			$('death_date').focus();
			//return false;
		}
		//return true;
	}
	*/
	
	function chckDeathForm(d){
		var msg='';
		
		//alert("d = '"+d+"'");
		msg= " $F('birth_rank') ='"+$F('birth_rank')+"'; \n $F('birth_rank_others') ='"+$F('birth_rank_others')+"'";
		//alert(msg);
		msg = " $F('death_manner') ='"+$F('death_manner')+
				"'; \n $F('death_manner').substring(0,1) ='"+$F('death_manner').substring(0,1)+
				"'; \n $F('death_manner_info') ='"+$F('death_manner_info')+"'";
		//alert(msg);
		msg = "$F('attendant_type') ='"+$F('attendant_type')+
				"'; \n $F('attendant_type').substring(0,1) ='"+$F('attendant_type').substring(0,1)+
				"'; \n $F('attendant_type_others') ='"+$F('attendant_type_others')+"'";
		//alert(msg);
		//alert("$F('age0to7days') = '"+$F('age0to7days')+"'; $('hours').disabled = '"+$('hours').disabled+"'");
		
		var d_manner=$F('death_manner');
		var a_type = $F('attendant_type');
		
		
		var birthdate = new Date($F('birth_date'));
		var deathdate = new Date($F('death_date'));
		//added by VAN 05-27-08
		//var birthdate = $F('birth_date');
		//var deathdate = $F('death_date');
		
		//alert('here = '+Date.parse(birthdate));
		//alert(Date.parse(birthdate)+" > "+Date.parse(deathdate));
		//alert("date = "+Date.parse(birthdate)+" > "+Date.parse(deathdate)+" - "+Date.parse(birthdate) > Date.parse(deathdate));
		//alert(birthdate +" > "+deathdate +" = " + birthdate > deathdate);
		
		if ($F('death_date')==""){	
			alert("Please enter the date of death.");
			$('death_date').focus();
			return false;
		// added by VAN 02-20-08	
		//}else if (Date.parse(birthdate) > Date.parse(deathdate)){	
		}else if (birthdate > deathdate){	
			alert("Death date should not be earlier than the birth date");
			$('death_date').focus();
			return false;
		}else if ( !($('hours').disabled) && 
			  (($F('hours')=="") || (parseInt($F('hours'))==0)) && 
			  (($F('minutes')=="") || (parseInt($F('minutes'))==0) ) && 
			  (($F('sec')=="") || (parseInt($F('sec'))==0) ) ){
			alert("Please specify the age of the baby in hours/minutes/seconds.");
			$('hours').focus();
			return false;
		}else if ( ($F('age0to7days')=="1") && ($F('delivery_method')=="2") && ($F('delivery_method_info')=="") ){
			alert("Please specify the method of delivery.");
			$('delivery_method_info').focus();
			return false;
		}else if ( ($F('age0to7days')=="1") && ($F('birth_type')!="1") && ($F('birth_rank')!="1") && 
				($F('birth_rank')!="2") && ($F('birth_rank_others')=="") ){
			alert("Please enter the rank of this child (for multiple birth).");
			$('birth_rank_tmp').focus();
			return false;
		}else	if ((d_manner.substring(0,1)=="4") && ($F('death_manner_info')=="")){
			alert("Please specify the manner of death.");
			$('death_manner_info').focus();
			return false;
		}else if ((a_type.substring(0,1)=="5") && ($F('attendant_type_others')=="")){
			alert("Please specify the type of attendant.");
			$('attendant_type_others').focus();
			return false;
 		}else if ( ($F('death_cert_attended')=="1") && ($F('death_time')=="")){
			alert("Please specify the time of death.");
			$('death_time').focus();
			return false;
		}else if((document.death_certificate.birth_rank_tmp[2].checked)&&($F('birth_rank_others')=="")){
			alert("Please specify the type of birth.");
			$('birth_rank_others').focus();
			return false;
		}
		//alert($F('birth_rank_others'));
		msg = "2 : $F('death_manner') ='"+$F('death_manner')+"'";
		//alert(msg);
		msg = "2 : $F('attendant_type') ='"+$F('attendant_type')+"'";
		//alert(msg);
		return true;
	}/* end of function chckDeathForm */

function EnableBirthRank(){
	var objBirthRank = document.death_certificate.birth_type_tmp;
	var objBirthRankOthers = document.death_certificate.birth_rank_tmp;
	//alert('here');
	if(objBirthRank[0].checked){
		objBirthRankOthers[0].disabled = true;	
		objBirthRankOthers[1].disabled = true;	
		objBirthRankOthers[2].disabled = true;	
		$('birth_rank_others').readOnly = true;
	}else if(objBirthRank[1].checked){
		objBirthRankOthers[0].disabled = false;	
		objBirthRankOthers[1].disabled = false;	
		objBirthRankOthers[2].disabled = true;	
		$('birth_rank_others').readOnly = true;
	}else if(objBirthRank[2].checked){
		objBirthRankOthers[0].disabled = false;	
		objBirthRankOthers[1].disabled = false;	
		objBirthRankOthers[2].disabled = false;	
		$('birth_rank_others').readOnly = false;	
	}
}

//added by VAN 05-28-08
function preSet(){
	//getAge($('death_date'));
	//alert($('death_age').value);
	showforchild();
	EnableBirthRank();
	EnableCivilStatusOther();
	if ($('delivery_method_info').value=="")
		$('delivery_method_info').disabled = true;	
	if ($('attendant_title').value=="")
		$('attendant_title').value = 'Resident Physician';
	if ($('attendant_address').value=="")		
		$('attendant_address').innerHTML = 'Gonzales Maranan Medical Center Incorporated, Quezon Ave., Digos City';	
    if ($('corpse_disposal_others').value=="")
		$('corpse_disposal_others').disabled = true;	
}

function EnableCivilStatusOther(){
	//alert('here');	
	//alert("1 = "+document.death_certificate.decease_civilstatus[0].checked);
	//alert("2 = "+document.death_certificate.decease_civilstatus[1].checked);
	//alert("3 = "+document.death_certificate.decease_civilstatus[2].checked);
	//alert("4 = "+document.death_certificate.decease_civilstatus[3].checked);
	//alert("5 = "+document.death_certificate.decease_civilstatus[4].checked);
	if (document.death_certificate.decease_civilstatus[4].checked){
		$('d_civil_status').readOnly = false;
	}else{
		$('d_civil_status').readOnly = true;	
	}	
}
//----------------------------
	/*
	function clickHandler(e){
		var elTarget = YAHOO.util.Event.getTarget(e);
		var objId = document.getElementById(elTarget.id);
		var objBirthRank = document.death_certificate.multiple_birth_tmp;
		var objBirthRankOthers = document.death_certificate.multiple_birth_specify;
		
		//alert(elTarget.id);
		
		if(elTarget.nodeName.toUpperCase() == "INPUT"){			
			switch (elTarget.id){
			case 'type_of_birth_tmp':
				//var birthType = document.getElementById("type_of_birth_tmp");
				alert(birthType.value);
				if(birthType.value == 1){
					//$('multiple_birth_tmp').value = '';
					objBirthRank.value = '';
					objBirthRankOthers = '';
					for(var i=0; i<objBirthRank.length; i++){
						objBirthRank[i].disabled = true;
			 			objBirthRank[i].checked = false;
					}
					objBirthRankOthers.disabled = true;		
				}else{
					for(var i=0; i<objBirthRank.length; i++){
						objBirthRank[i].disabled = false;
					}
					objBirthRank[0].checked = true;
				}
				$('birth_type').value = objId.value;
				alert("typeofBirth : $F('multiple_birth') = '"+$F('multiple_birth')+"'");
				break;
			case 'multiple_birth_tmp':
				alert(objId.value);
				break;
			}
			
		}
		
		
		
		
	}	
	YAHOO.util.Event.on("death_certificate", "click", clickHandler); 
	
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g," ");
	}
	
	*/


