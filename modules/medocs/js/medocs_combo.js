<!--
//======== AjaxScript ==========
	//added by VAN 04-23-09
		function jsAssignDrICD(attendingDr){
				document.getElementById('current_doc_nr_d').value = attendingDr;
		}

		//added by VAN 02-27-08

	function unhideObject(){
		//if (document.getElementById('time_text_d').value=="NaN")
		//	document.getElementById('time_text_d').value = "";
		//document.getElementById('icdCode').focus();
				//edited by VAN 04-23-09
				document.getElementById('current_doc_nr_f').focus();
	if (document.getElementById('encounter_type').value!=2){
		if (document.getElementById('isdischarge').checked){
			//ER Medocs
			if (document.getElementById('encounter_type').value==1){
				document.getElementById('bodyDischarge3').style.display = '';
			}
			//if (document.getElementById('encounter_type').value==3){
				//document.getElementById('bodyDischarge3').style.display = '';
			//}
			document.getElementById('bodyDischarge').style.display = '';
			document.getElementById('bodyDischarge2').style.display = '';
			document.getElementById('divSaveButton').style.display = '';

			//if (document.getElementById('time_text_d').value=="NaN")
			//	document.getElementById('time_text_d').value = "";
		}else{
			//ER Medocs
			if (document.getElementById('encounter_type').value==1){
				document.getElementById('bodyDischarge3').style.display = 'none';
			}

			//if (document.getElementById('encounter_type').value==3){
				//document.getElementById('bodyDischarge3').style.display = 'none';
			//}
			document.getElementById('bodyDischarge').style.display = 'none';
			document.getElementById('bodyDischarge2').style.display = 'none';
			document.getElementById('divSaveButton').style.display = 'none';
		}
	}
		//added by VAN 06-28-08
		if (document.getElementById('disp_hidden').value!="")
			showDeathDate2();
		//-----------------------
	}

	function showDeathDate2(){
		if ((document.getElementById('disp_hidden').value==4)||(document.getElementById('disp_hidden').value==8)){
			document.getElementById('death_date_span').style.display = '';
		}else{
			document.getElementById('death_date_span').style.display = 'none';
			document.getElementById('death_date').value = '';
		}
	}

	//Initialize for consulting doctors & departments
	function preset_c(){
		//commented by VAN 02-18-08
		/*
		var encounter_type = $('encounter_type').value;
		var encounter_class_nr  = $('encounter_class_nr').value;
		var dept_nr = $('consulting_dept_nr').value;
		var doc_nr = $('consulting_dr_nr').value;

		//alert("preset_c : dept_nr="+ dept_nr+"'");
		//alert("preset_c : encounter_class_nr='"+encounter_class_nr+"'");

		if (encounter_class_nr==1){
			xajax_setDoctors_c(1,dept_nr,doc_nr); //get all IPD doctors
			xajax_setALLDepartment_c(1,dept_nr); //get all IPD hospital department
		}else{
			xajax_setDoctors_c(0,dept_nr,doc_nr); //get all OPD doctors
			xajax_setALLDepartment_c(0,dept_nr); //get all OPD hospital department
		}
		*/
		//alert('body = '+document.getElementById('divSaveButton').innerHTML);
		//added by VAN 02-27-08
		//alert('discharged = '+document.getElementById('is_discharged').value);
		//alert('dept = '+document.getElementById('userdept').value);

		//alert('enc, enc2 = '+document.getElementById('encounter_type').value);
		// if not medical records
		//edited by VAN 02-25-08
		// consulting doctor
		var encounter_type=document.getElementById('encounter_type').value;
		var doc_nr;
		//var dept_nr = document.getElementById('current_dept_nr_c').value;
		var dept_nr;
//alert('type = '+document.getElementById('encounter_type').value);
		if (document.getElementById('userdept').value!=151){
			//ER Medocs
			if (document.getElementById('encounter_type').value==1){
				document.getElementById('bodyDischarge3').style.display = 'none';
			}

			document.getElementById('bodyDischarge').style.display = 'none';
			document.getElementById('bodyDischarge2').style.display = 'none';
			document.getElementById('divSaveButton').style.display = 'none';
		}

		//alert('consulting dr = '+ $('consulting_dr_nr').value);
		//alert('current dr = '+ $('current_att_dr_nr').value);

		/*
		if ($('consulting_dr_nr').value)
			doc_nr = $('consulting_dr_nr').value;
		else
			doc_nr = $('current_att_dr_nr').value;
		*/
		if (document.getElementById('consulting_dr_nr').value)
			doc_nr = document.getElementById('consulting_dr_nr').value;
		else
			doc_nr = document.getElementById('current_att_dr_nr').value;

		if (encounter_type==1){
			if (document.getElementById('consulting_dept_nr').value==null)
				dept_nr = document.getElementById('consulting_dept_nr').value;
			else
				 dept_nr = document.getElementById('current_dept_nr').value;
		}else{
			dept_nr = document.getElementById('consulting_dept_nr').value;
		}
		//alert('preset = '+encounter_type+" - "+dept_nr);
		//alert('preset_c = '+document.getElementById('current_dept_nr_c').value);
		if(encounter_type==2){

			if (dept_nr){
				//alert('here');
				if (doc_nr)
					//xajax_setDoctors_c(1,dept_nr,doc_nr);  //get all IPD doctors
					xajax_setDoctors_c(0,dept_nr,doc_nr);  //get all IPD doctors
				else
					xajax_setDoctors_c(0,dept_nr);  //get all OPD doctors
				xajax_setALLDepartment_c(0,dept_nr); //get all OPD clinical Department
			}else{

				xajax_setDoctors_c(0,0);  //get all OPD doctors
				xajax_setALLDepartment_c(0,0); //get all OPD clinical Department
			}
		//}else if(encounter_type == 1 || encounter_type ==3){  //ER patient
		}else if(encounter_type == 1 || encounter_type ==3){  //ER patient
			//added by VAN 02-27-08
			//alert('here');
			if (dept_nr){
				if (doc_nr)
					xajax_setDoctors_c(1,dept_nr,doc_nr);  //get all IPD doctors
				else
					xajax_setDoctors_c(1,dept_nr);  //get all IPD doctors

				xajax_setALLDepartment_c(1,dept_nr); //get all IPD clinical Department
			}else{
				xajax_setDoctors_c(1,0);  //get all IPD doctors
				xajax_setALLDepartment_c(1,0); //get all IPD clinical Department
			}
		}else{

			//xajax_setDoctors_c(1,0);  //get all IPD doctors
			//edited by VAN 02-28-08
			xajax_setDoctors_c(1,0,doc_nr);  //get all IPD doctors
			xajax_setALLDepartment_c(1,dept_nr); //get all IPD hospital Department
		}
	}//end preset_c -> for consulting during ER

	//preset_d for diagnosis
	function preset_d(){
		var encounter_type=document.getElementById('encounter_type').value;
		var dept_nr = document.getElementById('current_dept_nr').value;
		//alert('dept_nr = '+dept_nr);
		if(encounter_type==2){
			xajax_setDoctors_d(0,0);  //get all OPD doctors
			//xajax_setALLDepartment_d(0,dept_nr); //get all OPD clinical Department
			//edited by VAN 02-28-08
			xajax_setALLDepartment_d(0,0); //get all OPD clinical Department
		}else if(encounter_type == 1 || encounter_type ==3){  //ER patient
			xajax_setDoctors_d(1,0);  //get all IPD doctors
			xajax_setALLDepartment_d(1,0); //get all IPD clinical Department
		}else{
			xajax_setDoctors_d(1,0);  //get all IPD doctors
			//xajax_setALLDepartment_d(1,dept_nr); //get all IPD hospital Department
			//edited by VAN 02-28-08
			xajax_setALLDepartment_d(1,0); //get all IPD hospital Department
		}
		//for Result, disposition, condition
		//xajax_setAddBtn(encounter_type);
	}

	//preset_p for procedure
	function preset_p(){
		var encounter_type=document.getElementById('encounter_type').value;
//		var dept_nr = document.getElementById('current_dept_nr').value;

//alert("preset_p : encounter_type = '"+encounter_type+"'");
		if(encounter_type==2){
			xajax_setDoctors_p(0,0);  //get all OPD doctors
			xajax_setALLDepartment_p(0,0); //get all OPD clinical Department
		}else{
			xajax_setDoctors_p(1,0);  //get all IPD doctors
			xajax_setALLDepartment_p(1,0); //get all IPD clinical Department
		}
		//for Result, disposition, condition
		//xajax_setAddBtn(encounter_type);
	}

	//preset_p for procedure
	function preset_pORIG(){
		var encounter_type=document.getElementById('encounter_type').value;
		var dept_nr = document.getElementById('current_dept_nr').value;

		//alert("preset_p : encounter_type = '"+encounter_type+"'");
		if(encounter_type==2){
			xajax_setDoctors_p(0,dept_nr);  //get all OPD doctors
			xajax_setALLDepartment_p(0,dept_nr); //get all OPD clinical Department
		}else if(encounter_type == 1 || encounter_type ==3){
			xajax_setDoctors_p(1,0);  //get all OPD doctors
			xajax_setALLDepartment_p(1,0); //get all OPD clinical Department
		}else{
			xajax_setDoctors_p(1,0);  //get all IPD doctors
			xajax_setALLDepartment_p(1,dept_nr); //get all IPD hospital Department
		}
		//for Result, disposition, condition
		//xajax_setAddBtn(encounter_type);
	}

	//preset_f for final discharged
	function preset_f(){

		var encounter_type=document.getElementById('encounter_type').value;
		var dept_nr = document.getElementById('current_dept_nr').value;
		var doc_nr = document.getElementById('current_att_dr_nr').value;
		var dept_nr_c = document.getElementById('consulting_dept_nr').value;

//		alert("preset_f : encounter_type = '"+encounter_type+"'; dept_nr='"+dept_nr+"'; doc_nr = '"+doc_nr+"'");
//alert('preset_f = '+dept_nr_c);

		if(encounter_type==2){
			if (doc_nr){
				//alert('here = '+doc_nr);
				xajax_setDoctors_f(0,dept_nr_c,doc_nr);  //get all OPD doctors
			}else{
				xajax_setDoctors_f(0,dept_nr_c);  //get all OPD doctors
			}
			xajax_setALLDepartment_f(0,dept_nr_c); //get all OPD clinical Department
						//xajax_setALLDepartment_f(0,0); //get all OPD clinical Department

		}else if(encounter_type==4){ // burn added: June 4, 2007
			xajax_setDoctors_f(1,dept_nr,doc_nr);  //get all OPD doctors
			xajax_setALLDepartment_f(1,dept_nr); //get all OPD clinical Department
						//xajax_setALLDepartment_f(1,0); //get all OPD clinical Department

		}else{
			//alert('hello');
			//edited by VAN 02-27-08
			if (doc_nr){
				xajax_setDoctors_f(1,dept_nr,doc_nr);  //get all IPD doctors
			}else{
				xajax_setDoctors_f(1,dept_nr);  //get all IPD doctors
			}
			xajax_setALLDepartment_f(1,dept_nr); //get all IPD hospital Department
						//xajax_setALLDepartment_f(1,0); //get all IPD hospital Department

		}
				document.getElementById('current_dept_nr_f').value = '124';
	}

	//clear ajax Options for consulting during ER transaction
	function ajxClearOptions_c(status) {
		var optionsList;
		var el;

		if (status == 0){
			el = document.entryform.current_doc_nr_c; //doctors
		}else{
			el = document.entryform.current_dept_nr_c;	//departments
		}

		if (el) {
			optionsList = el.getElementsByTagName('OPTION');
			for(var i=optionsList.length-1; i>=0; i--){
				optionsList[i].parentNode.removeChild(optionsList[i]);
			}
		}
	}//end of function ajaxClearOption_c

	//clear ajax Options for Diagnosis
	function ajxClearOptions_d(status) {
		var optionsList;
		var el;

		if (status==0){
			el=document.entryform.current_doc_nr_d;
		}else{
			el=document.entryform.current_dept_nr_d;
		}

		if (el) {
			optionsList = el.getElementsByTagName('OPTION');
			for (var i=optionsList.length-1;i>=0;i--) {
				optionsList[i].parentNode.removeChild(optionsList[i]);
			}
		}
	}//end of function ajxClearOption_d

	//clear ajax Options for Procedure
	function ajxClearOptions_p(status) {
		var optionsList;
		var el;

		if (status==0){
			el=document.entryform.current_doc_nr_p;
		}else{
			el=document.entryform.current_dept_nr_p;
		}

		if (el) {
			optionsList = el.getElementsByTagName('OPTION');
			for (var i=optionsList.length-1;i>=0;i--) {
				optionsList[i].parentNode.removeChild(optionsList[i]);
			}
		}
	}//end of function ajxClearOptions_p

	//clear ajax Options for final discharged
	function ajxClearOptions_f(status) {
		var optionsList;
		var el;

		if (status==0){
			el=document.entryform.current_doc_nr_f;
		}else{
			el=document.entryform.current_dept_nr_f;
		}

		if (el) {
			optionsList = el.getElementsByTagName('OPTION');
			for (var i=optionsList.length-1;i>=0;i--) {
				optionsList[i].parentNode.removeChild(optionsList[i]);
			}
		}
	}//end of function ajxClearOption_f

	//Add option for consulting doctors & departments
	function ajxAddOption_c(status, text, value){
		var grpEl;
		//alert("ajxAddOption_c : status = '"+status+"'; text = '"+text+"'; value = '"+value+"'");
		if(status == 0) {
			//grpEl=$('current_doc_nr_c'); //doctors
			grpEl=document.getElementById("current_doc_nr_c");
		}else{
			//grpEl=$('current_dept_nr_c'); //depts
			grpEl=document.getElementById("current_dept_nr_c");
		}

		if (grpEl){
			var opt = new Option(text, value);
			opt.id = value;
			grpEl.appendChild(opt);
		}
		var optionsList = grpEl.getElementsByTagName('OPTION');

	}// end of function ajxAddOption

	//Add option for Diagnosis
	function ajxAddOption_d(status, text, value) {
		var grpEl;
//alert("ajxAddOption_d : status = '"+status+"' \n text = '"+text+"' \n value = '"+value+"'");
		if (status==0){
			//grpEl=document.entryform.current_doc_nr;
			grpEl=document.getElementById("current_doc_nr_d");
		}else{
			//grpEl=document.entryform.current_dept_nr;
			grpEl=document.getElementById("current_dept_nr_d");
		}

		if (grpEl) {
			var opt = new Option( text, value );
			opt.id = value;
			grpEl.appendChild(opt);
		}
		var optionsList = grpEl.getElementsByTagName('OPTION');

	} /* end of function ajxAddOption */

	//Add option for Procedure
	function ajxAddOption_p(status, text, value) {
		var grpEl;
//alert("ajxAddOption_p : status = '"+status+"' \n text = '"+text+"' \n value = '"+value+"'");
		if (status==0){
			//grpEl=document.entryform.current_doc_nr;
			grpEl=document.getElementById("current_doc_nr_p");
		}else{
			//grpEl=document.entryform.current_dept_nr;
			grpEl=document.getElementById("current_dept_nr_p");
		}

		if (grpEl) {
			var opt = new Option( text, value );
			opt.id = value;
			grpEl.appendChild(opt);
		}
		var optionsList = grpEl.getElementsByTagName('OPTION');

	} /* end of function ajxAddOption */

	//Add option for Final discharged
	function ajxAddOption_f(status, text, value) {
		var grpEl;

		if (status==0){
			//grpEl=document.entryform.current_doc_nr;
			grpEl=document.getElementById("current_doc_nr_f");
		}else{
			//grpEl=document.entryform.current_dept_nr;
			grpEl=document.getElementById("current_dept_nr_f");
		}

		if (grpEl) {
			var opt = new Option( text, value );
			opt.id = value;
			grpEl.appendChild(opt);
		}
		var optionsList = grpEl.getElementsByTagName('OPTION');

	} /* end of function ajxAddOption */


	//consulting Doctors & Departments
	/*
	function ajxSetDepartment_c(dept_nr){
		//commented by VAN 02-18-08
		document.entryform.current_dept_nr_c.value = dept_nr;
	}
	*/
	//edited by VAN 02-18-08
	//consulting Doctors & Departments
	function ajxSetDepartment_c(dept_nr, list) {
		//alert('ajxSetDepartment_c');
		var current_dept = document.entryform.current_dept_nr_c.value;
		var array = list.split(",");
		for (var x=0; x<array.length; x++){
			if (array[x]==current_dept){
				dept_nr=current_dept;
				break;
			}
		}
		document.entryform.current_dept_nr_c.value = dept_nr;
	}


	function ajxSetDoctor_c(personell_nr) {
		//alert('ajxSetDoctor_c : personell_nr = '+personell_nr);
		document.entryform.current_doc_nr_c.value = personell_nr;
	}

	//Diagnosis
	function ajxSetDepartment_d(dept_nr, list) {
			// burn added : June 6, 2007
		var current_dept = document.entryform.current_dept_nr_d.value;
		var array = list.split(",");
//		alert("ajxSetDepartment_d : current_dept = '"+current_dept+"'; array.length = '"+array.length+"'");
		for (var x=0; x<array.length; x++){
//			alert("ajxSetDepartment_d : array["+x+"] = '"+array[x]+"'");
			if (array[x]==current_dept){
				dept_nr=current_dept;
				break;
			}
		}
		document.entryform.current_dept_nr_d.value = dept_nr;
	}

	function ajxSetDoctor_d(personell_nr) {
		document.entryform.current_doc_nr_d.value = personell_nr;
	}

	//Procedure
	function ajxSetDepartment_p(dept_nr, list) {
			// burn added : June 6, 2007
		var current_dept = document.entryform.current_dept_nr_p.value;
		var array = list.split(",");
//		alert("ajxSetDepartment_p : current_dept = '"+current_dept+"'; array.length = '"+array.length+"'");
		for (var x=0; x<array.length; x++){
//			alert("ajxSetDepartment_p : array["+x+"] = '"+array[x]+"'");
			if (array[x]==current_dept){
				dept_nr=current_dept;
				break;
			}
		}
		document.entryform.current_dept_nr_p.value = dept_nr;
	}

	//Procedure
	function ajxSetDepartment_pORIG(dept_nr, list) {
			// burn added : June 6, 2007
		var current_dept = document.entryform.current_dept_nr_p.value;
		var array = list.split(",");
//		alert("ajxSetDepartment_p : current_dept = '"+current_dept+"'; array.length = '"+array.length+"'");
		for (var x=0; x<array.length; x++){
//			alert("ajxSetDepartment_p : array["+x+"] = '"+array[x]+"'");
			if (array[x]==current_dept){
				dept_nr=current_dept;
				break;
			}
		}
		document.entryform.current_dept_nr_p.value = dept_nr;
	}

	function ajxSetDoctor_p(personell_nr) {
		document.entryform.current_doc_nr_p.value = personell_nr;
	}
	//Final diagnosis and procedure
	function ajxSetDepartment_f(dept_nr) {
		//document.entryform.current_dept_nr_f.value = dept_nr;
		document.getElementById('current_dept_nr_f').value = dept_nr;
				//alert(document.entryform.current_dept_nr_f.value+" - "+dept_nr)
	}

	function ajxSetDoctor_f(personell_nr) {
		document.entryform.current_doc_nr_f.value = personell_nr;
	}


	//Get Department for Diagnosis
	function jsGetDepartment_d(){
		var d = document.entryform;
		//var aDoctor=d.current_att_dr_nr;
		var aDoctor=d.current_doc_nr_d;
		var aPersonell_nr;
		var optionsList;
		//alert('jsGetDepartment_d');
		aPersonell_nr = aDoctor.value;

		if (aPersonell_nr != 0) {
//			alert("jsGetDepartment_d : d.current_dept_nr_d.value = '"+d.current_dept_nr_d.value+"'");
			xajax_setDepartments_d(aPersonell_nr);
			optionsList = aDoctor.getElementsByTagName('OPTION');
		} else{
			d.current_dept_nr_d.value = 0;
		}

		//if (d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text != "-Select a Doctor-"){
		//if (d.current_doc_nr.options[d.current_doc_nr.selectedIndex].text != "-Select a Doctor-"){
			//d.consulting_dr.value = d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text;
		//	d.consulting_dr.value = d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text;
		//}else{
		//	d.consulting_dr.value = "";
		//}
	}//end of function jsGetDepartment_d

	//Get Department for Procedure
	function jsGetDepartment_p(){
		var d = document.entryform;
		var aDoctor=d.current_doc_nr_p;
		var aPersonell_nr;
		var optionsList;

		aPersonell_nr = aDoctor.value;

		if (aPersonell_nr != 0) {
			xajax_setDepartments_p(aPersonell_nr);
			optionsList = aDoctor.getElementsByTagName('OPTION');
		} else{
			d.current_dept_nr_p.value = 0;
		}

	}//End of function jsGetDepartment_p

	//Get Consulting Department
	function jsGetDepartment_c(){
		//commented by VAN 02-18-08
		/*
		var d = document.entryform;
		var aDoctor = d.current_doc_nr_c;
		var aPersonell_nr, optionsList;

		aPersonell_nr = aDoctor.value;
		if(aPersonell_nr != 0 ){
			if (d.current_dept_nr_c.value==0)   // burn added : June 4, 2007
				xajax_setDepartments_c(aPersonell_nr);
			optionsList = aDoctor.getElementsByTagName('OPTION');
		}else{
			d.current_dept_nr_c.value = 0;
		}
		*/

		var d = document.entryform;
		var aDoctor=d.current_doc_nr_c;
		var aPersonell_nr;
		var optionsList;

		aPersonell_nr = aDoctor.value;
		//alert('jsGetDepartment_c = '+aPersonell_nr);
		if (aPersonell_nr != 0) {
			xajax_setDepartments_c(aPersonell_nr);
			optionsList = aDoctor.getElementsByTagName('OPTION');
		} else{
			d.current_dept_nr_c.value = 0;
		}
	} // End of function jsGetDepartment_c



	//Get Department for Final diagnosis / procedure
	function jsGetDepartment_f(){
		var d = document.entryform;
		//var aDoctor=d.current_att_dr_nr;
		var aDoctor=d.current_doc_nr_f;
		var aPersonell_nr;
		var optionsList;

		aPersonell_nr = aDoctor.value;

		if (aPersonell_nr != 0) {
			if (d.current_dept_nr_f.value==0)   // burn added : June 4, 2007
				//xajax_setDepartments_p(aPersonell_nr);
								//alert('js = '+aPersonell_nr);
								xajax_setDepartments_f(aPersonell_nr);
				 optionsList = aDoctor.getElementsByTagName('OPTION');
		} else{
			d.current_dept_nr_f.value = 0;
		}

	}//End of function jsGetDepartment_f

	//Get Doctors for Diagnosis
	function jsGetDoctors_d(){
		//var d = document.entryform;
		//var aDepartment=d.current_dept_nr;
		var aDepartment = document.getElementById("current_dept_nr_d");
		var aDepartment_nr;
		var optionsList;
		var encounter_type =document.getElementById("encounter_type").value;

		var aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;

		//if (encounter_class_nr == 1){
			if(encounter_type!=2){
				if (aDepartment_nr != 0){
					xajax_setDoctors_d(1,aDepartment_nr);
				}else{
					xajax_setDoctors_d(1,0);			// get all IPD doctors
				}
			}else{
				if (aDepartment_nr != 0){
					xajax_setDoctors_d(0,aDepartment_nr);
				}else{
					xajax_setDoctors_d(0,0);			// get all OPD doctors
				}
			}
		//}else{
		/*
			if ((encounter_type==2)&&(encounter_class_nr==2)&&(dept_belong!="Admission")){
				if (aDepartment_nr != 0){
					xajax_setDoctors(0,aDepartment_nr);
				}else{
					xajax_setDoctors(0,0);			// get all IPD doctors
				}
			}else{
				 if (aDepartment_nr != 0){
					xajax_setDoctors(1,aDepartment_nr);
				}else{
					xajax_setDoctors(1,0);			// get all IPD doctors
				}
			}
		} */
	}//End of function jsGetDoctors_d

	//Get Doctors for Procedure
	function jsGetDoctors_p(){
		var aDepartment = document.getElementById("current_dept_nr_p");
		var aDepartment_nr;
		var optionsList;
		var encounter_type = document.getElementById("encounter_type").value;

		var aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;

		//if (encounter_class_nr == 1){
			if(encounter_type!=2){
				if (aDepartment_nr != 0){
					xajax_setDoctors_p(1,aDepartment_nr);
				}else{
					xajax_setDoctors_p(1,0);			// get all IPD doctors
				}
			}else{
				if (aDepartment_nr != 0){
					xajax_setDoctors_p(0,aDepartment_nr);
				}else{
					xajax_setDoctors_p(0,0);			// get all OPD doctors
				}
			}

	}//End of function jsGetDoctors_p

	//Get Consulting Doctors
	function jsGetDoctors_c(){
		// commented by VAN 02-18-08
		/*
		var aDepartment = $('current_dept_nr_c');
		var encounter_type = $('encounter_type');
		var aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
		var optionsList;

		if(encounter_type != 2 ){
			if(aDepartment_nr = 0){
				xajax_setDoctors_c(1, aDepartment_nr);
			}else{
				xajax_setDoctors_c(1, 0); // get all IPD doctors
			}
		}else{
			if(aDepartment_nr != 0){
				xajax_setDoctors_c(0,aDepartment_nr);
			}else{
				xajax_setDoctors_c(0,0);   //get all OPD doctors
			}
		}
		*/
		//consulting doctor
		var aDepartment = document.getElementById("current_dept_nr_c");
		var aDepartment_nr;
		var optionsList;
		var encounter_type =document.getElementById("encounter_type").value;

		aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
		//alert('aDepartment_nr = '+aDepartment_nr+" - "+encounter_type);
		//if (encounter_class_nr == 1){
			if(encounter_type!=2){
				if (aDepartment_nr != 0){
					xajax_setDoctors_c(1,aDepartment_nr);
				}else{
					xajax_setDoctors_c(1,0);			// get all IPD doctors
				}
			}else{
				if (aDepartment_nr != 0){
					xajax_setDoctors_c(0,aDepartment_nr);
				}else{
					xajax_setDoctors_c(0,0);			// get all OPD doctors
				}
			}
	} //end of function jsGetDoctors_c

	//Get Doctors for Final Diagnosis / Procedure
	function jsGetDoctors_f(){
		var aDepartment = document.getElementById("current_dept_nr_f");
		var aDepartment_nr;
		var optionsList;
		var encounter_type = document.getElementById("encounter_type").value;

		//if(encounter_type == 2){
		//	var aDepartment_nr = document.getElementById("current_dept_nr");
		//}else{
			var aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
	//	}


		//if (encounter_class_nr == 1){
			if(encounter_type!=2){
				if (aDepartment_nr != 0){
					xajax_setDoctors_f(1,aDepartment_nr);
				}else{
					xajax_setDoctors_f(1,0);			// get all IPD doctors
				}
			}else{
				if (aDepartment_nr != 0){
					xajax_setDoctors_f(0,aDepartment_nr);
				}else{
					xajax_setDoctors_f(0,0);			// get all OPD doctors
				}
			}

	}//End of function jsGetDoctors_f



	function loadConDispResData(){
		var d = document.entryform;
		//var encounter_type= $('encounter_type').value;
		var encounter_type= document.getElementById('encounter_type').value;
		//var cond_code = $('cond_code_h').value;
		var cond_code = document.getElementById('cond_code_h').value;
		//var disp_code= $('disp_code_h').value;
		//var result_code = $('result_code_h').value;
		//var discharge_time = $('discharge_time_h').value;
		var discharge_time = document.getElementById('discharge_time_h').value;
       
		//edited by VAN 02-28-08
		var disp_code;
		var result_code;

		//if ($('disp_code_h').value)
		if (document.getElementById('disp_code_h').value)
			//disp_code = $('disp_code_h').value;
			disp_code = document.getElementById('disp_code_h').value;
		else{
			if ((encounter_type==4)||(encounter_type==3))
				disp_code = 7;		//Admission discharged
			else
				disp_code = 2;		//ER discharged
		}

		//if ($('result_code_h').value)
		if (document.getElementById('result_code_h').value)
			//result_code = $('result_code_h').value;
			result_code = document.getElementById('result_code_h').value;
		else{
			if ((encounter_type==4)||(encounter_type==3))
				result_code = 5;	//recovered
			else
				result_code = 2;	//recovered
		}
//alert('type = '+encounter_type);
//alert("$('disp_code') = '"+$('disp_code')+"'; disp_code = '"+disp_code+"'");
//alert("$('result_code') = '"+$('result_code')+"'; result_code = '"+result_code+"'");
		if(encounter_type == 0){
			if(cond_code) d.cond_code[cond_code-1].checked = true; //condition at ER
		}else if(encounter_type !=2){

//			if(disp_code) d.disp_code[disp_code-1].checked = true; //disposition of inpatient
//			if(result_code) d.result_code[result_code-1].checked = true; //result of inpatient
			//edited by VAN 02-28-08
			//if((disp_code)&&($('result_code'))) d.disp_code[disp_code-1].checked = true; //disposition of inpatient
			//if((result_code)&&($('result_code'))) d.result_code[result_code-1].checked = true; //result of inpatient
			//if (encounter_type==4){
			//commented by VAN 06-12-08
			/*
			if ((encounter_type==4)||(encounter_type==3)){
				//if((disp_code)&&($('disp_code'))) d.disp_code[0].checked = true; //disposition of inpatient
				//if((result_code)&&($('result_code'))) d.result_code[0].checked = true; //result of inpatient
				//if((disp_code)&&($('disp_code'))) d.disp_code[0].checked = true; //disposition of inpatient
				if((disp_code)&&(document.getElementById('disp_code'))) d.disp_code[0].checked = true; //disposition of inpatient
				if((result_code)&&(document.getElementById('result_code'))) d.result_code[0].checked = true; //result of inpatient
			}else{
				//alert(disp_code);
				//if((disp_code)&&($('result_code'))) d.disp_code[disp_code-1].checked = true; //disposition of inpatient
				//if((disp_code)&&($('result_code'))) d.disp_code[disp_code-1].checked = true; //disposition of inpatient
				if((disp_code)&&(document.getElementById('result_code'))) d.disp_code[disp_code-1].checked = true; //disposition of inpatient
				//if((disp_code)&&(document.getElementById('result_code'))) d.disp_code[0].checked = true; //disposition of inpatient
				if((result_code)&&(document.getElementById('result_code'))) d.result_code[result_code-1].checked = true; //result of inpatient
				//if((result_code)&&(document.getElementById('result_code'))) d.result_code[0].checked = true; //result of inpatient
			}
			*/
		}
		//alert('discharge_time = '+discharge_time);
		//if($('time_text_d')){
        
		if(document.getElementById('time_text_d')){
			//if(discharge_time) $('time_text_d').value = cnvrtTimeFormat(discharge_time); // time discharged
			//if(discharge_time) document.getElementById('time_text_d').value = cnvrtTimeFormat(discharge_time); // time discharged
			//if(discharge_time)
			//alert("here = '"+discharge_time+"'");
			//if((discharge_time != '00:00:00')||(discharge_time != '')||(discharge_time != null)||(discharge_time != ' ')) {
			//edited by VAN 06-28-08
            if ((discharge_time != '00:00:00')&&(discharge_time)){
				//edited by VAN 04-17-2011
                if (discharge_time.substr(0,2) == '12'){
					$('time_text_d').value = "12:"+discharge_time.substr(3);
					$('selAMPM').value = 'P.M.';
				}else if (discharge_time.substr(0,2) == '00'){
					var temp = "12:"+discharge_time.substr(3);
					$('time_text_d').value = "12:"+discharge_time.substr(3);
					$('selAMPM').value = 'A.M.';
				}else{
					//document.getElementById('time_text_d').value = cnvrtTimeFormat(discharge_time); // time discharged
					cnvrtTimeFormat(discharge_time);
				}
			}else{
				//document.getElementById('time_text_d').value = "00:00";
				//document.getElementById('time_text_d').value = "";
				//edited by VAN 04-17-2011
				//$('time_text_d').value = "12:00";
                $('time_text_d').value = "";
				$('selAMPM').value = 'A.M.';
			}
			//alert("time->"+cnvrtTimeFormat(discharge_time));
          
		}

	}

	function cnvrtTimeFormat(t){
		//var t1 = toString();
        //var time = t.substr(0,2);
		//var st1 = parseInt(time); //first 2 digit
        var st1 = t.substr(0,2); //first 2 digit
		var st2 = t.substr(3); //second 3 digit
		var t3,t4;
		//var t = '0';
        
		if (st1 > 12){
                t3 = parseInt(st1) - 12;
				if (t3 < 10)
					t3 = '0'+t3;
				t4 = t3+":"+st2;
				$('time_text_d').value = t4;
				//alert($('time_text_d').value);
				$('selAMPM').value = 'P.M.';
		}else if ((st1 >= 00) && (st1 < 12)){
                //t3 = parseInt(st1);
                t3 = st1;
                if (t3 < 10){
                    if ((t3.substr(0,1) > 0)&&(t3.length < 3))
					    t3 = '0'+t3;
                }    
				t4 = t3+":"+st2;
				$('time_text_d').value = t4;
				$('selAMPM').value = 'A.M.';
		}
        
		//if($('selAMPM')){
		//edited by VAN 04-17-2011
		/*if(document.getElementById('selAMPM')){
			if(st1 > 12){
				//if(st1>11) $('selAMPM').value = 'P.M.';
				if(st1>11) document.getElementById('selAMPM').value = 'P.M.';
				st1 = st1 + 00;
				t3 = (st1-12).toString();
				t4 = t3.concat(st2);
				//added by VAN 02-28-08
				if (t3<10)
					t4 = t.concat(t4);
			}else{
				//if(st1<=11) $('selAMPM').value = 'A.M.';
				if(st1<=11) document.getElementById('selAMPM').value = 'A.M.';
				t4 = (st1).toString().concat(st2);
			}
			return t4;
		}else{
			return false;
		}*/

	}

	//added by VAN 06-28-08
	function showDeathDate(objValue){
		//alert('showDeathDate');
		if ((objValue==4)||(objValue==8)){
			document.getElementById('death_date_span').style.display = '';

			if ((document.getElementById('death_date2').value!='')&&(document.getElementById('death_date2').value!='0000-00-00'))
				document.getElementById('death_date').value = document.getElementById('death_date2').value;

		}else{
			document.getElementById('death_date_span').style.display = 'none';
			document.getElementById('death_date').value = '';
		}

		document.getElementById('disp_hidden').value = objValue;
	}

	function formatdischargetime(){
        if ($('time_text_d').value=='0:00')
			$('time_text_d').value= '00:00';
	}

//======== End AjaxScript========
-->