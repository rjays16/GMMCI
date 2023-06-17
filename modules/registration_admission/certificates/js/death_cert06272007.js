
function trimString(obj){
	obj.value = obj.value.replace(/^\s+|\s+$/g,"");
	obj.value = obj.value.replace(/^\s+$/g," ");
}


function typeofBirth(obj){
	var objValue = obj.value;
	var objMultiple = document.death_certificate.multiple_birth_tmp;
	var objMultipleSpecify = document.death_certificate.multiple_birth_specify;
	
	if(objValue == 1){
		objMultiple.value = '';
		objMultipleSpecify.value = '';
		for(var i=0; i<objMultiple.length;i++){
			objMultiple[i].disabled = true;
			objMultiple[i].checked = false;
		}
		objMultipleSpecify.disabled = true;
	}else{
		for(var i=0; i<objMultiple.length; i++){
			objMultiple[i].disabled = false;
		}
		objMultiple[0].checked = true;
	}
	$('birth_type').value = objValue;
		alert("typeOfBirth : $F('birth_rank') ='"+$F('birth_rank')+"'");
}

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
	var stime = thisTime.value;
	var hr, min;
	var ft = "";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	
	trimString(thisTime);
	
	if(thisTime.value==''){
		seg_validTime = false;
		return;
	}
	
	stime = stime.replace(':', '');
	if(stime.lenght == 3){
		hr = stime.substring(0,1);
		min = stime.substring(1,3);
	}else if (stime.length == 4){
		hr = stime.substring(0,2);
		min = stime.substring(2,4);
	}else{
		alert("Invalid  time format.");
		thisTime.value = "";
		seg_validTime = false;
		return;
	}
	
	if(hr >12){
		hr -= 12;
		$('selAMPM').value = "P.M.";
	}
	
	ftime = hr + ":" + min;
	
	if((!ftime.match(f1)) && (!ftime.match(f2))){
		thisTime.value = "";
		alert("Invalid time format.");
		seg_validTime = false;
	}else{
		thisTime.value = ftime;
		seg_validTime = True;
	}
}//end function setFormatTime

function getBirthdate(obj){
	var age = parseInt(obj.value);
	var bdate = document.death_certificate.death_date.value;
	var dateNow = new Date();
	
	//		alert("getBirthdate : age = '"+age+"' \n obj.value = '"+obj.value+"'");
	
	if (!isNaN(age)){
		document.death_certificate.death_date.value=(dateNow.getMonth()+1)+"/"+dateNow.getDate()+"/"+(dateNow.getFullYear()-age);
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

function getAge(obj){
	var dob;
	var valid;
		//  mm/dd/yyyy
	
	try{
		valid = IsValidDate(obj,'MM/dd/yyyy');
		dod = obj.value;
		dob = $('date_birth').value; 
		
		if(dod == ""){
			alert("dod2='"+dod+"'");
			$('death_age').value = '';
			$('d_citizenship').focus();
			return;
		}
				
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
		var age = parseInt(dodYear) - parseInt(dobYear);
		
		//alert("age='"+age+"'");
		//alert("ageYear="+presentDate.getTime()+"\n pastDate.getTime()="+pastDate.getTime());
		
		var ageYear = (presentDate.getTime()-pastDate.getTime())/31536000000;///86400000;
		
		//alert("ageYear="+ageYear);
		age = age + ageYear;
		alert("1 : age="+ age);
		
		if(age<1){
			var ageMonth = parseInt(age*12);
			var ageDay = ((age*12)-ageMonth) * 30;
			
		//	alert("age="+age+" ageMonth="+ageMonth+"ageDay="+ageDay);
			//below 1 year
			if(ageMonth == 0 && ageDay<1){
				// under 1 day
				$('death_age').value = '';
				$('death_months').value = '';
				$('death_days').value = '';

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
				$('death_days').value = parseInt(ageDay);
				
				//disable textfield under 1 day
				$('hours').value = '';
				$('minutes').value = '';
				$('sec').value = '';
				$('hours').disabled = true;
				$('minutes').disabled = true;
				$('sec').disabled = true;
			}
			
			if(ageMonth == 0 && ageDay>= 0 && ageDay<= 7){
				expandcontract('Row1','');
			}else{
				expandcontract('Row1','none');
			}
			
		}else{
			//above 1 year
			//$('death_age').disabled = false;
			$('death_months').value = '';
			$('death_days').value = '';
			//$('death_months').disabled = true;
			//$('death_days').disabled = true;
			
			//disable textfield under 1 day
			$('hours').value = '';
			$('minutes').value = '';
			$('sec').value = '';
			$('hours').disabled = true;
			$('minutes').disabled = true;
			$('sec').disabled = true;
			
			expandcontract('Row1','none');
			if ((isNaN(age)) ||(dobMonth==0)||(dobDay==0)||(dobYear==0)){
				document.death_certificate.death_age.value='';
			}else{
				alert("death_age="+age);
				//$('death_age').value = age;
				$('death_age').value = (parseFloat(age)).toFixed(2);
				$('d_citizenship').focus();
				//document.death_certificate.death_age.value=age;
				//document.death_certificate.d_citizenship.focus();
			}	
		}
	}catch(e){
		document.death_certificate.death_age.value='';				
	}
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


