//--- with modifications by pet from previous death_cert.js started by burn & vanessa ---

	/*
			The trimString function will trim the string, i.e., it will remove
			all whitespaces in the beginning and end of a string.  Only a single
			whitespace appears between tokens/words.

			input: object
			output: trimmed object (string) value
	*/

	function trimString(obj){
		obj.value = obj.value.replace(/^\s+|\s+$/g,"");
		obj.value = obj.value.replace(/^\s+$/g," ");
	}// end of function trimString

	function preSet(){

		if ($('informant_address').value==""){
			UpdateInformantAddress();
		}
		showforchild();
		EnableBirthRank();
		chkCompletedYrs();
		chkCivilStatus();
		chkAttendant();

		//added by VAN 08-08-08
		checkUnnatural();
		//---------------

		if ($('delivery_method_info').value=="")
			$('delivery_method_info').disabled = true;
		if ($('attendant_title').value=="")
			$('attendant_title').value = 'Resident Physician';
		if ($('attendant_address').value=="")
			$('attendant_address').innerHTML = 'Gonzales Maranan Medical Center Incorporated, Quezon Ave., Digos City';
		if ($('corpse_disposal_others').value=="")
			$('corpse_disposal_others').disabled = true;
		if ($('is_late_reg').value=="1"){
			showLateReg($('is_late_reg'));
			$('late_dead_name').readOnly=true;
			$('late_place_death').readOnly=true;
		}
	}// end of function preSet

	function DCivilStat(obj,otherStat){
		var objValue = obj.value;
		var objOtherStat = otherStat.value;

		if ((objValue=="single")||(objValue=="married")||(objValue=="widowed")||(objValue=="unknown")){
			expandcontract("if_civilstatOthers",'none');
			$('civil_status').value=objValue;
		}else{
			expandcontract("if_civilstatOthers",'');
				$('civil_status').value=objOtherStat;
		}
		$('d_civilstat_tmp').value=$('civil_status').value;
		$('decease_civilstatus').value=$('civil_status').value;
	}// end of function DCivilStat

	function enableOneDayOldText(){
		//added by VAN 08-09-08
		if ((($('death_age').value==0)||($('death_months').value==""))&&(($('death_months').value==0)||($('death_age').value==""))
           &&(($('death_days').value==0)||($('death_days').value==""))){
					$('hours').readOnly = false;
					$('minutes').readOnly = false;
					$('sec').readOnly = false;
		}else{
				$('hours').readOnly = true;
				$('minutes').readOnly = true;
				$('sec').readOnly = true;
		}
	}

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
		enableOneDayOldText()

	}// end of function updateBdate

	function updateBdate2(obj){
		if ((obj.value!==null)&&($('date_birth3')=="0000-00-00")){
			$('birth_day').value="0";
			$('birth_month').value="0";
			$('birth_year').value="0";
		}
		enableOneDayOldText()
	}// end of function updateBdate2

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

		if (objValue==1){
			objBirthRank.value = '';
			objBirthRankOthers.value = '';
			for(var i=0; i<objBirthRank.length;i++ ){
				objBirthRank[i].disabled = true;
				objBirthRank[i].checked = false;
			}
			objBirthRankOthers.disabled = true;
			//added by VAN 08-09-08
			$('birth_rank').value="";

		}else if (objValue==2){
			objBirthRankOthers.value = '';
			for(var i=0; i<objBirthRank.length-1;i++ ){
				objBirthRank[i].disabled = false;
			}
			objBirthRank[2].disabled = true;
			objBirthRank[0].checked = true;
			objBirthRankOthers.disabled = true;
			//added by VAN 08-09-08
			$('birth_rank').value="first";

		}else{
			for(var i=0; i<objBirthRank.length;i++ ){
				objBirthRank[i].disabled = false;
			}
			objBirthRank[0].checked = true;
			//added by VAN 08-09-08
			$('birth_rank').value="first";
		}
		$('birth_type').value=objValue;
		//commented by VAN 08-09-08
		//$('birth_rank').value='';
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
	}// end function multipleBirth

	var countingNumber = true;
	var wholeNumber = false;

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

	function updateBirthRank(){
		$('birth_rank').value =$('birth_rank_others').value;
	}// end of function updateBirthRank

	function showforchild(){
		if ($('death_age').value<7) {
			expandcontract('Row1','');
			expandcontract('Row2','none');
		}else{
			expandcontract('Row1','none');
			expandcontract('Row2','');
		}
	}// end of function showforchild

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

	function checkWord(objValue){
		var order=new Array("first", "second", "third", "fourth", "fifth", "sixth", "seventh", "eighth", "ninth","tenth");
		return order.in_array(objValue);
	}// end of function checkWord

	function getAge(obj){
		var dob;
		var valid;

		try{

			valid = IsValidDate(obj,'MM/dd/yyyy');
			dod = obj.value;
			dob = $('date_birth2').value;

			if(dod == ""){
				$('death_age').value = '';
				$('death_months').value = '';
				$('death_days').value = '';
				//disable textfield under 1 day
				$('hours').value = '';
				$('minutes').value = '';
				$('sec').value = '';
				$('hours').readOnly = true;
				$('minutes').readOnly = true;
				$('sec').readOnly = true;
				$('d_citizenship').focus();
				return false;
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

			var age = parseInt(dodYear) - parseInt(dobYear);

			var ageYear = (presentDate.getTime()-pastDate.getTime())/31536000000;///86400000;

			age = age + ageYear;

			if(age<1){
				var ageMonth = parseInt(age*12);
				var ageDay = ((age*12)-ageMonth) * 30;

				//below 1 year
				if(ageMonth == 0 && Math.round(ageDay)<1){
					// under 1 day
					$('death_age').value = 0;
					$('death_months').value = 0;
					$('death_days').value = 0;
					//disable textfields under 1 year
					$('hours').disabled = false;
					$('minutes').disabled = false;
					$('sec').disabled = false;

				}else{ //under 1 year old
					$('death_months').value = parseInt(ageMonth);
					$('death_days').value = parseInt(Math.round(ageDay));
					$('death_age').value = 0;
					//disable textfield under 1 day
					$('hours').value = 0;
					$('minutes').value = 0;
					$('sec').value = 0;
					$('hours').readOnly = true;
					$('minutes').readOnly = true;
					$('sec').readOnly = true;
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
				$('death_months').value = 0;
				$('death_days').value = 0;

				//disable textfield under 1 day
				$('hours').value = 0;
				$('minutes').value = 0;
				$('sec').value = 0;
				$('hours').readOnly = true;
				$('minutes').readOnly = true;
				$('sec').readOnly = true;

				expandcontract('Row1','none');
				expandcontract('Row2','');
				$('age0to7days').value=0;
				if ((isNaN(age)) ||(dobMonth==0)||(dobDay==0)||(dobYear==0)){
					document.death_certificate.death_age.value='';
				}else{
					if ((parseFloat(age))<=0)
						$('death_age').value = 0;
					else {
						$('death_age').value = Math.floor((parseFloat(age)));
					}
				}
			}
		}catch(e){
			document.death_certificate.death_age.value='';
		}
	}// end of function getAge

	function getAge2(obj){
		if (((!$('death_age'))||($('death_age').value=="0"))&&($('date_birth3')!="0000-00-00"))
		//if ($('date_birth3')!="0000-00-00")
		//if (chkDdate==true){
			getAge(obj);
	}

	function expandcontract(tbodyid,dis) {
	  document.getElementById(tbodyid).style.display = dis;
	}// end of function chckForm

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
			if ((objMannerOfDeathAccident.value=="")||(objMannerOfDeathAccident.value=="VA"))
				objMannerOfDeathAccident.value = "VA";
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
				objValue = objValue + " - "+objMannerOfDeathAccident.value;
				break;
			case "4":
				objValue = objValue + " - "+objMannerOfDeathOthers.value;
				break;
		}
		$('death_manner').value=objValue;

		checkIfNatural();
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
		chkAttendant();
	}// end of function typeOfAttendant

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

    //added by jasper 01/17/13
    function fnCheckLateIsAttended(obj){
        var objValue = obj.value;
        var objLateAttendedBy = $('late_attended_by');
        //alert (objValue);
        if (objValue=="1") {
          objLateAttendedBy.disabled = false;
        }
        else if (objValue=="2") {
           objLateAttendedBy.disabled = true;
           objLateAttendedBy.value = "";
        }
        $('late_is_attended').value=objValue;
        //alert($('late_is_atteded').value);
    }// end of function fnCheckLateIsAttended

	function UpdateDeathDate(mode){
		if (mode){
			$('death_date').value=$('attended_to_date').value;
		}else{
			$('attended_to_date').value=$('death_date').value;
		}

		enableOneDayOldText();
	}// end of function UpdateDeathDate

	function UpdateLDeathDate(){
		if ($('late_ddate').value!=""){
			$('death_date').value=$('late_ddate').value;
			$('attended_to_date').value=$('death_date').value;
		}else{
			$('late_ddate').value=$('death_date').value;
		}
	}// end of function UpdateLDeathDate

	function UpdateCemetery(){
		$('cemetery_name_address').value = $('late_cemetery').value;
	}

	function printDeathCert(id){
		if (id==0)
			id="";
		if (window.showModalDialog){  //for IE
			window.showModalDialog("cert_death_pdf.php?id="+id,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}else{
			window.open("cert_death_pdf.php?id="+id,"deathCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}
	}// end of function printDeathCert

    //added by jasper 01/09/2013
    function printDeathCertNew(id){
        if (id==0)
            id="";
        if (window.showModalDialog){  //for IE
            window.showModalDialog("cert_death_pdf_jasper.php?id="+id,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
        }else{
            window.open("cert_death_pdf_jasper.php?id="+id,"deathCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
        }
    }// end of function printDeathCert

	function chckDeathForm(d){
		var d_manner=$F('death_manner');
		var a_type = $F('attendant_type');
		var c_disposal=$F('corpse_disposal');

		var birthdate = new Date($F('date_birth2'));
		var deathdate = new Date($F('death_date'));

		var ddc = document.death_certificate;
		var certAttend = ddc.death_cert_attended;
		var certTimeAtt = ddc.death_time;

        var delivery_method = $('delivery_method').value;
        //alert(delivery_method.substring(0,1));
		if ($F('death_date')==""){
			alert("Please enter the date of death.");
			$('death_date').focus();
			return false;
		}else if (birthdate > deathdate){
			alert('birthdate = '+birthdate);
			alert('deathdate = '+deathdate);
			alert("Death date should not be earlier than the birth date");
			$('death_date').focus();
			return false;
		}else if ( (!($('hours').disabled)&&(!($('hours').readOnly))) &&
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
		/*}else if ( ($F('age0to7days')=="1") && ($F('birth_type')!="1") && ($F('birth_rank')!="1") &&
				($F('birth_rank')!="2") && ($F('birth_rank')!="3") && ($F('birth_rank_others')=="")  ){
		*/
		//edited by VAN 08-09-08
		}else if(($F('age0to7days')=="1") && ($F('birth_type')=="3") && ($F('birth_rank')=="third")
						&& ($F('birth_rank_others')=="")){
			alert("Please enter the rank of this child (for multiple birth).");
			$('birth_rank_tmp').focus();
			return false;
		}else if(($('civil_status').value=="others")&&($F('d_civilstat_tmp')==null)){
			alert("Please select a specific civil status.");
			ddc.d_civilstat_tmp[0].focus();
			return false;
		}else if ((d_manner.substring(0,1)=="3") && ($F('death_manner_accident')=="")){
			alert("Please specify the type of accident.");
			$('death_manner_accident').focus();
			return false;
		}else if ((d_manner.substring(0,1)=="4") && ($F('death_manner_info')=="")){
			alert("Please specify the manner of death.");
			$('death_manner_info').focus();
			return false;
        //added by jasper 01/24/13
        }else if ((delivery_method.substring(0,1)=="2") && ($('delivery_method_info').value=="" || $('delivery_method_info').value==" " || $('delivery_method_info').value==null)) {
            alert("Please specify other method of delivery.");
            $('delivery_method_info').focus();
            return false;
		//edited by VAN 08-08-08
		}else if(((d_manner.substring(0,1)=="1")||(d_manner.substring(0,1)=="2")) && ($('place_occurrence').value=="")){
				alert("Please specify the place of occurrence.");
				$('place_occurrence').focus();
				return false;
		//-----------------
		}else if ((a_type.substring(0,1)=="5") && ($F('attendant_type_others')=="")){
			alert("Please specify the type of attendant.");
			$('attendant_type_others').focus();
			return false;
 		}else if ((certAttend.value=="1") && (certTimeAtt.value=="")){
			alert("Please specify the time of death.");
			$('death_time').focus();
			return false;
		}else	if ((c_disposal.substring(0,1)=="3") && ($F('corpse_disposal_others')=="")){
			alert("Please specify the manner of corpse disposal.");
			$('corpse_disposal_others').focus();
			return false;
		}
		/*
		else if((ddc.birth_rank_tmp[2].checked)&&($F('birth_rank_others')=="")){
			alert("Please specify the type of birth.");
			$('birth_rank_others').focus();
			return false;
		}
		*/

		if (ddc.is_late_reg.checked==true){
			if ((ddc.late_affiant_name.value=="")||(ddc.late_affiant_name.value==" ")||(ddc.late_affiant_name.value==null)){
				alert("Please enter the name of the affiant.");
				ddc.late_affiant_name.focus();
				return false;
			}else if ((ddc.late_cemetery.value=="")||(ddc.late_cemetery.value==" ")||(ddc.late_cemetery.value==null)){
				alert("Please enter the name of the cemetery or crematory.");
				ddc.late_cemetery.focus();
				return false;
			}else if ((ddc.late_bcdate.value=="")||(ddc.late_bcdate.value==" ")||(ddc.late_bcdate.value==null)){
				alert("Please enter the date of burial or cremation.");
				ddc.late_bcdate.focus();
				return false;
            //added by jasper 01/20/13
            }else if ((ddc.late_is_attended.value=="")||(ddc.late_is_attended.value==" ")||(ddc.late_is_attended.value==null)){
                alert("Please specify if the deceased was or was not attended at the time of death.");
                //ddc.late_death_cause.focus();
                return false;
             }else if ((ddc.late_is_attended.value=="1")&&((ddc.late_attended_by.value=="")||(ddc.late_attended_by.value==" ")||(ddc.late_attended_by.value==null))){
                alert("Please specify the attendant at the time of death.");
                ddc.late_attended_by.focus();
                return false;
             }else if ((ddc.late_death_cause.value=="")||(ddc.late_death_cause.value==" ")||(ddc.late_death_cause.value==null)){
                alert("Please enter the cause of death of the deceased.");
                ddc.late_death_cause.focus();
                return false;
            }else if ((ddc.late_reason.value=="")||(ddc.late_reason.value==" ")||(ddc.late_reason.value==null)){
                alert("Please enter the reason for delayed registration.");
                ddc.late_reason.focus();
                return false;
            }else if ((ddc.late_sign_date.value=="")||(ddc.late_sign_date.value==" ")||(ddc.late_sign_date.value==null)){
                alert("Please enter the date when the affidavit took place.");
                ddc.late_sign_date.focus();
                return false;
            }else if ((ddc.late_sign_place.value=="")||(ddc.late_sign_place.value==" ")||(ddc.late_sign_place.value==null)){
                alert("Please enter the address where the affidavit took place.");
                ddc.late_sign_place.focus();
                return false;
            //added by jasper 01/20/13
			}else if ((ddc.affiant_com_tax_nr.value=="")||(ddc.affiant_com_tax_nr.value==" ")||(ddc.affiant_com_tax_nr.value==null)){
				alert("Please enter the community tax number of the affiant.");
				ddc.affiant_com_tax_nr.focus();
				return false;
			}else if ((ddc.affiant_com_tax_date.value=="")||(ddc.affiant_com_tax_date.value==" ")||(ddc.affiant_com_tax_date.value==null)){
				alert("Please enter the issuance date of the affiant's community tax certificate.");
				ddc.affiant_com_tax_date.focus();
				return false;
			}else if ((ddc.affiant_com_tax_place.value=="")||(ddc.affiant_com_tax_place.value==" ")||(ddc.affiant_com_tax_place.value==null)){
				alert("Please enter the issuance place of the affiant's community tax certificate.");
				ddc.affiant_com_tax_place.focus();
				return false;
			}else if ((ddc.late_officer_date_sign.value=="")||(ddc.late_officer_date_sign.value==" ")||(ddc.late_officer_date_sign.value==null)){
				alert("Please enter the signing date of the Administering Officer.");
				ddc.late_officer_date_sign.focus();
				return false;
			}else if ((ddc.late_officer_place_sign.value=="")||(ddc.late_officer_place_sign.value==" ")||(ddc.late_officer_place_sign.value==null)){
				alert("Please enter the signing place of the Administering Officer.");
				ddc.late_officer_place_sign.focus();
				return false;
			}else if ((ddc.late_officer_name.value=="")||(ddc.late_officer_name.value==" ")||(ddc.late_officer_name.value==null)){
				alert("Please enter the name of the Administering Officer.");
				ddc.late_officer_name.focus();
				return false;
			}else if ((ddc.late_officer_title.value=="")||(ddc.late_officer_title.value==" ")||(ddc.late_officer_title.value==null)){
				alert("Please enter the title/designation of the Administering Officer.");
				ddc.late_officer_title.focus();
				return false;
			}else if ((ddc.late_officer_address.value=="")||(ddc.late_officer_address.value==" ")||(ddc.late_officer_address.value==null)){
				alert("Please enter the address of the Administering Officer.");
				ddc.late_officer_address.focus();
				return false;
            }
		}

		return true;
	}// end of function chckDeathForm

	function chkCivilStatus(){
		var objOthrCivilStat = document.death_certificate.d_civilstat_tmp;
		var objDCivilStat = document.death_certificate.decease_civilstatus;
		if ($('civil_status').value=="child"){
			objOthrCivilStat[0].checked=true;
			objDCivilStat[4].checked=true;	}
		if ($('civil_status').value=="divorced"){
			objOthrCivilStat[1].checked=true;
			objDCivilStat[4].checked=true;}
		if ($('civil_status').value=="separated"){
			objOthrCivilStat[2].checked=true;
			objDCivilStat[4].checked=true;	}
		if ($('civil_status').value=="others"){
			objDCivilStat[4].checked=true;	}
		if (($('civil_status').value=="single")||($('civil_status').value=="married")||
			($('civil_status').value=="widowed")||($('civil_status').value=="unknown")){
			for(var i=0; i<objOthrCivilStat.length;i++ )
				objOthrCivilStat[i].checked=false;
		}
	}

	function chkAttendant(){
		if ((($('attendant_type_tmp').value!="4 - None")&&($('attendant_type_tmp').value!=""))&&
			($('attendant_type').value!="4 - None")){
			document.death_certificate.death_cert_attended[1].checked=true;
			document.death_certificate.death_cert_attended.value="1";
			//document.death_certificate.death_time.disabled=false;
			//document.death_certificate.selAMPM.disabled=false;
			expandcontract("if_attendant",'');
		}else {
			document.death_certificate.death_cert_attended[0].checked=true;
			document.death_certificate.death_cert_attended.value="0";
			//document.death_certificate.death_time.disabled=true;
			//document.death_certificate.selAMPM.disabled=true;
			expandcontract("if_attendant",'none');
		}
		certificationOfDeath(document.death_certificate.death_cert_attended);
	}

	function EnableBirthRank(){
		var objBirthRank = document.death_certificate.birth_type_tmp;
		var objBirthRankOthers = document.death_certificate.birth_rank_tmp;
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
	}// end of function EnableBirthRank

	//added by VAN 09-16-08
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
		var brgyObj = $('residence_brgy');
		var munObj = $('residence_mun');
		var provObj = $('residence_prov');

		informant_address  = document.getElementById('residence_basic').value;
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

	function showLateReg(obj){

		if (obj.checked==true){
			document.getElementById('late_reg').style.display = '';

			if (($('late_place_death').value=="")||($('late_place_death').value==" ") || ($('late_place_death').value==null))
				$('late_place_death').value = "Gonzales Maranan Medical Center Incorporated, Digos City";

			if (($('late_cemetery').value=="")||($('late_cemetery').value==" ") || ($('late_cemetery').value==null))
				$('late_cemetery').value = $('cemetery_name_address').value;

			if (($('late_officer_place_sign').value=="")||($('late_officer_place_sign').value==" ") || ($('late_officer_place_sign').value==null))
				$('late_officer_place_sign').value = "DAVAO CITY";

			if (($('late_reason').value=="")||($('late_reason').value==" ") || ($('late_reason').value==null))
				$('late_reason').value = "Negligence";
            //if ($('late_is_attended').value=="" || $('late_is_attended').value==null || $('late_is_attended').value!="1") {
            //   $('is_attended_tmp').checked=true;
            //}
        }else	{
			document.getElementById('late_reg').style.display = 'none';
		}
		$('late_dead_name').readOnly=true;
		$('late_place_death').readOnly=true;

	}// end of function showLateReg

	function chkCompletedYrs(){
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

		if (($('date_birth2').value!=null) && (($('death_date').value=="")||($('death_date').value==" ")||($('death_date').value==null)))
			$('death_age').readOnly = true;
	}// end of function chkCompletedYrs


	//added by VAN 08-08-08
	function checkUnnatural(){
		var objDeathManner = document.death_certificate.death_manner_tmp;
		var death_manner = '<?=$death_manner_tmp?>';
		if ($('unnatural_death').checked==true){
            //alert("checked");
			if(death_manner==1)
				objDeathManner[0].checked=true;
			else if(death_manner==2)
				objDeathManner[1].checked=true;
			else if(death_manner==3)
				objDeathManner[2].checked=true;
			else if(death_manner==4)
				objDeathManner[3].checked=true;
		}else{
            //alert("unchecked")
			objDeathManner[0].checked=false;
			objDeathManner[1].checked=false;
			objDeathManner[2].checked=false;
			objDeathManner[3].checked=false;

			$('death_manner_accident').value = " ";
			$('death_manner_info').value = " ";
			$('place_occurrence').value = " ";
			$('death_manner').value = " ";
		}
	}

	function checkIfNatural(){
		var objDeathManner = document.death_certificate.death_manner_tmp;
		if ((objDeathManner[0].checked==true)||(objDeathManner[1].checked==true)
			||(objDeathManner[2].checked==true)||(objDeathManner[3].checked==true))
				$('unnatural_death').checked = true;
	   else
			$('unnatural_death').checked = false;
	}

    //added by jasper 01/16/2013
    function fnMaternalCondition(obj) {
       var objValue = obj.value;
       //alert(objValue);
       $('maternal_condition').value=objValue;
       $('maternal_cond').checked=true;
    }// end of fnMaternalCondition

    //added by jasper 01/16/2013
    function fnCheckMaternal() {
       var objMaternalCond = document.death_certificate.maternal_condition_tmp;
       //var death_manner = '<?=$death_manner_tmp?>';
       if ($('maternal_cond').checked==false){
           //alert("unchecked")
           objMaternalCond[0].checked=false;
           objMaternalCond[1].checked=false;
           objMaternalCond[2].checked=false;
           objMaternalCond[3].checked=false;
           objMaternalCond[4].checked=false;

           $('maternal_condition').value = 0;
       }
        else {
           //alert("checked")
           $('maternal_condition').value = 5;
           objMaternalCond[4].checked=true;
        }
    }// end of fnCheckMaternal

	//----------------

