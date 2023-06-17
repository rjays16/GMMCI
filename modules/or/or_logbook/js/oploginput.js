
function trimString(objct){
	objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
}

var seg_validTime = false;
function setFormatTime(thisTime, AMPM){
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
}	//end of function setFormatTime()

function validateTime(s){
	return /^([01]?[0-9])(:[0-5][0-9])?$/.test(s);
}


function saveAppointment(){
	//alert("hello");	
	var sDate = document.getElementById("sched_date").value;
	var sTime = document.getElementById("time").value;
	var sPID = document.getElementById("pid").value;
	
	var doc = document.getElementById("doc").value;
	var purpose = document.getElementById("purpose").value;
	
	var sDept = document.getElementById("to_dept_nr").value;
	var sEncounter = document.getElementById("enc_nr").value;
	
	var encoder = document.getElementById("encoder").value;
	
	
	//alert("pid="+ sPID + "\n sDate="+ sDate + "\n sTIme="+ sTime + "\n sDept=" + sDept + "\n sEncounter= " + sEncounter + "\n doctors=" + doc  +"\n purpose="+ purpose);
	
	if(sDate==""){
		alert("Please specify the date.");
		return false;
	}else if(sTime == ""){
		alert("Endicate time for the schedule.");
		return false;
	}else if(doc == ""){
		alert("Select surgeon to be appointted.");
		return false;
	}else{
		xajax_saveAppointment(sPID, sDate, sTime, sDept, doc, purpose, sEncounter, encoder );		
	}
	return true;	
}

function presetDoc_s(dept_nr){
	//alert("dept_nr"+ dept_nr);
	xajax_setDoctors(dept_nr); //get all Surgery doctors
}

function ajxClearOptions_s(){
	var optionsList;
	var el;
	el = document.getElementById("doc");
	
	if(el){
		//optionsList = el.getElementByTagName('option');
		optionsList = el.getElementsByTagName('OPTION');
		//alert("optionlist="+optionsList);
		for(var i=optionsList.length-1; i>=0; i--){
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}	
	}
}

function ajxSetDoctor_s(personell_nr){
	document.getElementById('doc').value = personell_nr;
}


function ajxAddOption_s(text, value){
	var grpEl;
	//alert("ajxAddOption_s : doc="+text1+ "; personell_nr="+ value1);
	grpEl = document.getElementById("doc"); //doctors
	if(grpEl){
		var opt = new Option(text,value);
		opt.id = value;
		grpEl.appendChild(opt);
	}
//	var optionsList = grpEl.getElementsByTagName('OPTION');
	
}// end of ajxAddOption_s












//Instantiate the Diaglog
/*
YAHOO.oploginput.container.sched = new YAHOO.widget.Dialog("sched", 
														{ width: "300px", 
														  fixedcenter : true,
														  visible : false,
														  constraintoviewport : true,
														  buttons : [ { text : "Submit", handler:handleSu, isDefault: true},
														  			  { text : "Cancel", handler:handleCa }	]
														} );
														
														*/
														
														