	function getAge(type){

		// FORMAT: mm/dd/yyyy
		try{
//			valid = IsValidDate(obj,'MM/dd/yyyy');
//			valid = IsValidDate($('dob'),'MM/dd/yyyy');
			valid = IsValidDate(document.getElementById("dob"),'MM/dd/yyyy');
		}catch(e){
			alert("Please check the date of birth of the person. It MUST be filled-in first!");
			return -1;
		}
		try{
//			valid = IsValidDate(obj,'MM/dd/yyyy');
//			valid = IsValidDate($('txtAdmissionDate'),'MM/dd/yyyy');
			valid = IsValidDate(document.getElementById("txtAdmissionDate"),'MM/dd/yyyy');
		}catch(e){
			alert("Please check the discharge date.  It MUST be filled-in first!");
			document.getElementById("date_text_d").select();
			return -1;
		}

//		var dob=$F('dob');
//		var discharged_date=$F('date_text_d');
		var dob=document.getElementById("dob").value;   // birthdate
		var ad=document.getElementById("txtAdmissionDate").value;   // discharged date
		var dateNow = new Date();
		var valid;
//			dob = obj.value;
		var dobMonth = dob.substring(0,2);
		var dobDay = dob.substring(3,5);
		var dobYear = dob.substring(6,10);
		var adMonth = ad.substring(0,2);
		var adDay = ad.substring(3,5);
		var adYear = ad.substring(6,10);
		var pastDate = new Date(2000,dobMonth-1,dobDay);
		var presentDate = new Date(2000,adMonth-1,adDay);
		var age;
		var age2;
		if (type=="years"){
			// compute age in terms of years
			age = parseInt(adYear) - parseInt(dobYear);
			age2 = (presentDate.getTime()-pastDate.getTime())/31536000000;
		}else{
			// compute age in terms of days
			age = (parseInt(adYear) - parseInt(dobYear))*365;
			age2 = (presentDate.getTime()-pastDate.getTime())/86400000;
		}
//		alert("age = '"+age+"' in terms of "+type+"; age2 = '"+age2+"'");
		var msg = " ad = '"+ad+"' \n dob = '"+dob+"' \n"+
			" adMonth = '"+adMonth+"' \n dobMonth = '"+dobMonth+"' \n"+
			" adDay = '"+adDay+"' \n dobDay = '"+dobDay+"' \n"+
			" adYear = '"+adYear+"' \n dobYear = '"+dobYear+"' \n"+
			" presentDate = '"+presentDate+"' \n pastDate = '"+pastDate+"' \n"+
			" age = '"+age+"' \n age2 = '"+age2+"'";
//			alert("msg : \n"+msg);
		age = age + age2;
//		alert("final age = '"+age+"' in "+type);
		if ((isNaN(age)) || (dobMonth==0)||(dobDay==0)||(dobYear==0)){
			alert("Please check the date of birth of the person!");
			return -1;
			//document.aufnahmeform.age.value='';
		}else{
			return age;
			//document.aufnahmeform.age.value=age;
			//document.aufnahmeform.place_birth.focus();
		}
	}/* end of function getAge */


	function maleOnly(ICDCode){
		var maleICDs = new Array("B26.0","C60","C61","C62","C63","D07.4","D07.5","D07.6","D17.6","D29",
										 "D40","E29","E89.5","F52.4","I86.1","L29.1","N40","N41","N42","N43",
										 "N44","N45","N46","N47","N48","N49","N50","N51","Q53","Q54",
										 "Q55","R86","S31.2","S31.3","Z12.5");
		var i=0;
		
		while(i < maleICDs.length){
			if (maleICDs[i]==ICDCode.substring(0,maleICDs[i].length))
				return false;
			i=i+1;
		}
		return true;
	}/* end of function maleOnly */

	function femaleOnly(ICDCode){
		var femaleICDs = new Array("A34","B37.3","C51","C52","C53","C54","C55","C56","C57","C58",
										 "C79.6","D06","D07.0","D07.1","D07.2","D07.3","D25","D25","D27","D28",
										 "D39","E28","E89.4","F52.5","F53","I86.3","L29.2","L70.5","M80.0","M80.1",
										 "M81.0","M81.1","M83.0","N70","N71","N72","N73","N74","N75","N76",
										 "N77","N78","N79","N80","N81","N82","N83","N84","N85","N86",
										 "N87","N88","N89","N90","N91","N92","N93","N94","N95","N96",
										 "N97","N98","N99.2","N99.3","O","P54.6","Q50","Q51","Q52","R87",
										 "S31.4","S37.4","S37.5","S37.6","T19.2","T19.3","T83.3","Y76.-","Z01.4","Z12.4",
										 "Z30.1","Z30.3","Z30.5","Z31.1","Z31.2","Z32","Z33","Z34","Z35","Z36",
										 "Z39","Z43.7","Z87.5","Z97.5");
		var i=0;
//		alert("ICDCode = '"+ICDCode+"'");
//		alert("femaleICDs.length = '"+femaleICDs.length+"'");
//		return 0;
		while(i < femaleICDs.length){
			if (femaleICDs[i]==ICDCode.substring(0,femaleICDs[i].length))
				return false;
			i=i+1;
		}
		return true;
	}/* end of function femaleOnly */

	function obstetrics(ICDCode){
		var age;
        /*
		if (ICDCode.substring(0,1)=="O"){
			age = getAge("years");
//			alert("obstetrics: ICDCode ='"+ICDCode+"' \n age = '"+age+"' year(s) old");
			if (age==-1)
				return false;
			if ( (age<12) || (age>50) ){
				alert("This ICD code is for FEMALES ages 12 to 50 YEARS OLD ONLY!");
				return false;
			}
		}
        */
		return true;
	}/* end of function obstetrics */

	function neonatal(ICDCode){
		/*
        var age = getAge("days");			
        
		if ( (age<0) || (age>7) ){
			return false;
		}
        */
		return true;
	}/* end of function neonatal */

		/*
			Checks for gender-specific ICD Code
			return boolean: true, valid; otherwise, false.
		*/
	function checkICDSpecific(){
		//alert(document.getElementById("icdCode"));
		var ICDCode = document.getElementById("icdCode");
		//edited by VAN 03-28-08
		//ICDCode = ICDCode.value.toUpperCase();
		//alert('ICDCode.value = '+icdCode.value);
		ICDCode = icdCode.value.toUpperCase();
        /*
		if (ICDCode.substring(0,1)=="P"){
			if (!neonatal(ICDCode)){
				alert("This ICD code is for newly born ages 0 to 7 DAYS ONLY!");
				return false;
			}
		}
        */
//		if ($F('gender')=="f"){   // for female
//alert("document.getElementById('gender').value = '"+document.getElementById("gender").value+"'");
		if (document.getElementById("gender").value=="f"){   // for female
			if (!maleOnly(ICDCode)){
				alert("This ICD code is for MALES ONLY!");
				return false;
			}
			if (!obstetrics(ICDCode)){
				return false;
			}
		}else{   // for male
			if (!femaleOnly(ICDCode)){
				alert("This ICD code is for FEMALES ONLY!");
				return false;
			}
		}
		return true;
	}/* end of function checkICDSpecific */