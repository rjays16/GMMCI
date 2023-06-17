var list_selection = new Array();

var pgStart = 0;
var pgLimit = 3;
var maxRows;
var bInitialized = false;

//-----------------------------------------
//	Trim-function for Javascript.
//-----------------------------------------
function trim(inputString) {
   // Removes leading and trailing spaces from the passed string. Also removes
   // consecutive spaces and replaces it with one space. If something besides
   // a string is passed in (null, custom object, etc.) then return the input.
   if (typeof inputString != "string") { return inputString; }
   var retValue = inputString;
   var ch = retValue.substring(0, 1);
   while (ch == " ") { // Check for spaces at the beginning of the string
      retValue = retValue.substring(1, retValue.length);
      ch = retValue.substring(0, 1);
   }
   ch = retValue.substring(retValue.length-1, retValue.length);
   while (ch == " ") { // Check for spaces at the end of the string
      retValue = retValue.substring(0, retValue.length-1);
      ch = retValue.substring(retValue.length-1, retValue.length);
   }
   while (retValue.indexOf("  ") != -1) { // Note that there are two spaces in the string - look for multiple spaces within the string
      retValue = retValue.substring(0, retValue.indexOf("  ")) + retValue.substring(retValue.indexOf("  ")+1, retValue.length); // Again, there are two spaces in each of the strings
   }
   return retValue; // Return the trimmed string back to the user
} // Ends the "trim" function
		
function insurance_clearHealthPlans() {
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("hplans_table")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.childNodes;
		// check if srcRows is valid and has more than 1 element	
		if (srcRows) {
			while (srcRows.length>0) {
				srcTableBody.removeChild(srcRows[0]);
			}
			//for (iterator=srcTable.) {
			//	srcTable.childNodes[1].removeChild(srcRows[1]);
			//}
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function updateSelection(nhcare_id, bChecked) {
	var bfound = false, i;
	
	if (bChecked) 
		list_selection[list_selection.length] = nhcare_id;
	else {
		for (i = 0; i < list_selection.length; i++) {
			if (list_selection[i] == nhcare_id) {
				bfound = true;
				break;	
			}
		}
		
		if (bfound) {
			list_selection.splice(i, 1);	
		}
	}
}

function delSelectedItems() {
	var i;
	
	for (i = 0; i < list_selection.length; i++) {
		
		
		
		
	}	
}
		
function addHealthPlan(nhcare_id, shcare_desc, shcare_company, shcare_contactp, shcare_addr1, shcare_addr2, 
					   shcare_contactno) {	
	var i, cntr, s_addr;

	if (destTable=document.getElementById("hplans_table")) {					
		destTableBody=destTable.getElementsByTagName("tbody")[0];
		destRows=destTableBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (destRows.length>0) lastRowNo=destRows[destRows.length-1].id.replace("hplan_info","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;
		
		if (nhcare_id) {
			if (shcare_addr1 != '' && shcare_addr2 != '') 
				s_addr = trim(shcare_addr1) + ', ' + trim(shcare_addr2);
			else if (shcare_addr2 == '') 
				s_addr = trim(shcare_addr1);
			else if (shcare_addr1 == '')
				s_addr = trim(shcare_addr2);
			else
				s_addr = '';
										
			newRowDest = '<tr class="wardlistrow1" id="hplan_info'+lastRowNo+'">' +
				'<td style="height:44px"><input onclick="updateSelection('+nhcare_id+', checked);" type="checkbox" value=""><input type="hidden" id="hcare_id'+lastRowNo+'" value="'+nhcare_id+'"></td>'+				
				'<td style="height:44px">'+shcare_desc+'<input type="hidden" id="hcare_desc'+lastRowNo+'" value="'+shcare_desc+'"></td>' +
				'<td>'+shcare_company+'<input type="hidden" id="hcare_company'+lastRowNo+'" value="'+shcare_company+'">' +
				'<br>'+s_addr+'<input type="hidden" id="hcare_addr1'+lastRowNo+'" value="'+shcare_addr1+'">'+
				'<input type="hidden" id="hcare_addr2'+lastRowNo+'" value="'+shcare_addr2+'">'+				  				
				'</td>'+
				'<td>'+shcare_contactp+'<input type="hidden" id="hcare_contact_person'+lastRowNo+'" value="'+shcare_contactp+'">'+
				'<br>'+shcare_contactno+'<input type="hidden" id="hcare_contact_no'+lastRowNo+'" value="'+shcare_contactno+'"></td>'+				
				'<td align="center"><input type="button" id="hcareUpdate'+lastRowNo+'" value="EDIT" onclick="prepareUpdate('+lastRowNo+')" style="color:#000066"></td>'+
			'</tr>';				
		}
		else {
			newRowDest = '<tr class="wardlistrow1" id="emptyRow'+lastRowNo+'">' +
				'<td colspan="6">No such health care available .... </td>' +
			 '</tr>';
		}		
		
		destTableBody.innerHTML += newRowDest;

		destRows=destTableBody.getElementsByTagName("tr");		
		cntr=0;
		for (i in destRows) {
			destRows[i].style.className=((cntr%2==0)?"wardlistrow1":"wardlistrow2");
			cntr++;
		}
	}
}	


function doClearCarePersons() {
	var srcTable, srcRows, srcTableBody;
	var iterator;
	if (srcTable=document.getElementById("my_table")) {
		srcTableBody=srcTable.getElementsByTagName("tbody")[0];
		srcRows=srcTableBody.childNodes;
		// check if srcRows is valid and has more than 1 element	
		if (srcRows) {
			while (srcRows.length>0) {
				srcTableBody.removeChild(srcRows[0]);
			}
			//for (iterator=srcTable.) {
			//	srcTable.childNodes[1].removeChild(srcRows[1]);
			//}
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail

}


function addCarePerson(pid, firstname, midname, lastname) {
	var destRows, destTableBody, newRowDest, lastRowNo;	
	var i, cntr, s_name;

	if (destTable=document.getElementById("my_table")) {					
		destTableBody=destTable.getElementsByTagName("tbody")[0];
		destRows=destTableBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (destRows.length>0) lastRowNo=destRows[destRows.length-1].id.replace("my_info","");
		lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;
		
		if (pid) {
			s_name = lastname + ", " + firstname + " " + midname;
										
			newRowDest = '<tr class="wardlistrow1" id="my_info'+lastRowNo+'">' +
				'<td style="height:22px">'+pid+'</td>'+				
				'<td>'+s_name+'</td>'+
			'</tr>';				
		}
		else {
			newRowDest = '<tr class="wardlistrow1" id="emptyRow'+lastRowNo+'">' +
				'<td colspan="6">No persons available .... </td>' +
			 '</tr>';
		}		
		
		destTableBody.innerHTML += newRowDest;

		destRows=destTableBody.getElementsByTagName("tr");		
		cntr=0;
		for (i in destRows) {
			destRows[i].style.className=((cntr%2==0)?"wardlistrow1":"wardlistrow2");
			cntr++;
		}
	}
}	

function getMaxRows(nRows) {
	maxRows = nRows;
}

function prevPage() {
	if (bInitialized) {
		if ((pgStart - pgLimit) >= 0)
			pgStart -= pgLimit;
		xajax_getCarePersons(pgStart, pgLimit);	
	}
}

function nextPage() {	
	var bJustInit;

	if (!bInitialized) {
		xajax_getResultsetMaxRows();
		bInitialized = true;				
		bJustInit = true;
	}
	else {
		bJustInit = false;
	}
	
	if (bInitialized) {		
		if (!bJustInit) {
			if ((pgStart + pgLimit) < maxRows) 
				pgStart += pgLimit;
		}					  
		xajax_getCarePersons(pgStart, pgLimit);		
	}
	
}

