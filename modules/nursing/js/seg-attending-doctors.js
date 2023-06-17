

function js_onClick(){
	var sid, lang, rpath, enc;	
	
	sid = $('sid').value;
	lang =$('lang').value;
	rpath =$('root_path').value;
	enc = $('pn').value;
/*	return overlib(OLiframeContent(rpath+'modules/laboratory/seg-lab-request-new.php'+sid+'&mode=update&update=1&ref='+refno, 780,450, 'flab-request', 1, 'auto'),
						WIDTH , 780, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL ,DRAGGABLE, CLOSETEXT, 
						'<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Laboratory Request',
						MIDX, 0, MIDY, 0, STATUS,'Laboratory Request');
	*/
	return overlib(OLiframeContent(rpath+'modules/nursing/seg-attending-doctors.php?sid'+sid+'&lang='+lang+'&enc='+enc, 700,300, 'flab-request', 1, 'auto'),
						WIDTH , 700, TEXTPADDING, 0, BORDER, 0, STICKY, CLOSECLICK, MODAL ,DRAGGABLE, CLOSETEXT, 
						'<img src=../../images/close.gif border=0>', CAPTIONPADDING, 4, CAPTION, 'Attending doctors',
						MIDX, 0, MIDY, 0, STATUS,'Attending Doctors');
	
}

//ok
function js_addRow(tblId,nr, docname, datestart, enc){
	var dTable, dTbody,dRows, srcRow, imgDelete, hidden, lastRowNo;	
	if(dTable=$(tblId)){
		dTbody = dTable.getElementsByTagName('tbody')[0];	
		dRows = dTbody.getElementsByTagName('tr');
		
		if(dRows.length>0) lastRowNo=dRows[dRows.length-1].id.replace("adRow", "");
		lastRowNo= isNaN(lastRowNo)? 0:(lastRowNo-0)+1;
		
		if(nr){
			//delitemImg = '<img src="../../../images/btn_delitem.gif" style="cursor:pointer" border="0" onclick="ssDeleteDialogBox(\''+code+'\',\''+lastRowNo+'\')">';
			imgDelete= '<img src="../../images/btn_delitem.gif" style="cursor:pointer" border="0" onclick="ssDeleteDialogBox(\''+enc+'\', \''+lastRowNo+'\', \''+nr+'\')">';
			hidden = '<input type="hidden" id="nr'+lastRowNo+'" value = "'+enc+'">';
			
			srcRow = '<tr id="adRow'+lastRowNo+'">'+
						'<td>&nbsp;</td>'+
						'<td>'+docname+'</td>'+	
						'<td align="center">'+datestart+'</td>'+
						'<td>'+imgDelete+hidden+'</td>'+
					 '</tr>';
			
		}else{
			srcRow = '<tr><td style="">No Atteding physician yet.. </td></tr>';	
		}
		dTbody.innerHTML += srcRow; 	
	}
	
}// end of function js_addRow()

function js_clearRow(tableId){
	// Search for the source row table element
	var list=$(tableId),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}// end of fucntion js_clearRow()

function jsGetAttendingDoctors(){
	var doc_nr ,enc, create_id, dt_start;
	
	doc_nr = $('selDoc').options[$('selDoc').selectedIndex].value;
	enc = $('encounter_nr').value;
	create_id = $('create_id').value;
	dt_start = $('dt_start').value;
	
	//alert("Doctor: personell_nr = "+ doc_nr	+ "\n encounter nr = "+ enc + "\n create_id =" + create_id + "\n date start = "+ dt_start);
	xajax_addAttendingDoctors(enc , doc_nr,dt_start, create_id);
	//xajax_PopulateRow(enc);
	
	//if (AJAXTimerID) clearTimeout(AJAXTimerID);
	//$("ajax-loading").style.display = "";
	//AJAXTimerID = setTimeout("xajax_PopulateRow('"+enc+"')", 200);
	
}// end  of function jsGetAttendingDoctors()
//var AJAXTimerID=0;
//var lastSearch="";
//function startAJAXSearch(searchID) {
	//var searchEL = $(searchID);
	
	//if (searchEL && lastSearch != searchEL.value) {
	//	searchEL.style.color = "#0000ff";
	//	if (AJAXTimerID) clearTimeout(AJAXTimerID);
//		$("ajax-loading").style.display = "";
	//	AJAXTimerID = setTimeout("xajax_populateProductList('"+searchID+"','"+searchEL.value+"')",200);
	//	lastSearch = searchEL.value;
//	}
//}

/*function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}
*/

//remove row from the List table
function removeRow(rowNo){
	var dTable, dRow, rmvRow;
	if(rmvRow = document.getElementById("adRow"+rowNo)){
		if(dTable = document.getElementById("doc-list")){
			dRow = dTable.getElementsByTagName("tbody")[0];
			if(dRow){
				dRow.removeChild(rowNo);
				return true;
			}
			else return false; //fail
		}
		else return false;
	}
}//end of function removeRow()


// Instantiate the Dialog
function ssDeleteDialogBox(code, rowno, nr){
	var elTarget = 'adRow'+rowno;
	var handleYes = function() {
		//alert("elTarget="+elTarget+ "code = "+ code+ "\n rowno ="+ rowno+ "\n nr = "+ nr);
		xajax_delAttendingDoctors(code, nr, rowno);
		xajax_PopulateRow(code);
		
		this.hide();
	};
	var handleNo = function() {
		this.hide();
	};
	
	//alert("hello ");
	
	YAHOO.ssadmin.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1", 
			 { width: "300px",
			   fixedcenter: true,
			   visible: false,
			   draggable: false,
			   close: true,
			   text: "Do you want to delete this physician?" ,
			   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
			   constraintoviewport: true,
			   buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
						  { text:"No",  handler:handleNo } ]
			 } );
	
	
	YAHOO.ssadmin.container.simpledialog1.setHeader("Deleting attending physician..");
	// Render the Dialog
	YAHOO.ssadmin.container.simpledialog1.render("container");
		
	YAHOO.util.Event.addListener(elTarget, "click", YAHOO.ssadmin.container.simpledialog1.show, YAHOO.ssadmin.container.simpledialog1, true);

} //end of function ssDialogBox



//ok
function jsGetDepartment(){
	//var d = document.aufnahmeform;
	//var aDoctor=d.current_att_dr_nr;
	var aDoctor = $('selDoc');
	var aPersonell_nr;
	var optionsList;
	
	//d.consulting_dr.value = d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text;
	
	aPersonell_nr = aDoctor.value;
	
	//alert("personal_nr =" + aPersonell_nr);
	
	if (aPersonell_nr != 0) {
		xajax_setDepartments(aPersonell_nr,0);		
		optionsList = aDoctor.getElementsByTagName('OPTION');
	} else{
		//d.current_dept_nr.value = 0;
		$('selDept').value = 0;
	}	
	
	/*if (d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text != "-Select a Doctor-"){
		d.consulting_dr.value = d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text;
	}else{
		d.consulting_dr.value = " ";
	}*/
	//if($('selDoc').options[$('selDoc').selectedIndex].text ! = "-Select a Doctor-"){
	//}
}

function jsGetDoctors(){
	//var d = document.aufnahmeform;
	//var aDepartment=d.current_dept_nr;
	var aDepartment = $('selDept');
	var aDepartment_nr;
	var optionsList;
	//var encounter_class_nr = <?php echo $encounter_class_nr; ?>;
	//var update = <?php echo $update; ?>;
	//var encounter_type = "<?php echo $encounter_type; ?>";
	//var dept_belong = "<?php echo $dept_belong['id']; ?>";
	var aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
		
		
	//d.consulting_dr.value = " ";
	if(aDepartment_nr != 0){
		xajax_setDoctors(1, aDepartment_nr);	
	}else{
		xajax_setDoctors(1,0);	
	}
	
	
	/*if (update != 1){	
		
		if (encounter_class_nr == 1){
			if (aDepartment_nr != 0){
				xajax_setDoctors(1,aDepartment_nr);	
			}else{
				xajax_setDoctors(1,0);			// get all IPD doctors
			}
		}else{ 
			if (aDepartment_nr != 0){
				xajax_setDoctors(0,aDepartment_nr);	
			}else{
				xajax_setDoctors(0,0);			// get all OPD doctors
			}
		}
	}else{	
		
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
	}*/
}

//ok
function ajxSetDepartment(dept_nr) {
	//document.aufnahmeform.current_dept_nr.value = dept_nr;
	$('selDept').value = dept_nr;
}
//ok
function ajxSetDoctor(personell_nr) {
	//document.aufnahmeform.current_att_dr_nr.value = personell_nr;
	$('selDoc').value = personell_nr;
}

//ok
function ajxAddOption(status, text, value) {
var grpEl;

	if (status==0){
		//grpEl=document.aufnahmeform.current_att_dr_nr;
		grpEl = $('selDoc');
	}else{
		//grpEl=document.aufnahmeform.current_dept_nr;
		grpEl = $('selDept');
	}
	
	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
	var optionsList = grpEl.getElementsByTagName('OPTION');
	
}/* end of function ajxAddOption */

//ok
function ajxClearOptions(status) {
var optionsList, el;

	if (status==0){
		//el=document.aufnahmeform.current_att_dr_nr;
		el = $('selDoc');
	}else{
		//el=document.aufnahmeform.current_dept_nr;
		el = $('selDept');
	}
	 
	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearOptions */
/*
xajax_setAllDepartmentEROPD(0,er_dept_nr);		
		xajax_setDoctorsEROPD(0, er_dept_nr, er_dr_nr);
		xajax_setALLDepartment(1);	// get all IPD Department
		xajax_setDoctors(1,dept_nr);
		xajax_setDepartments(doc,dept_nr);
*/
//($admit_inpatient=0, $dept_nr=0, $personell_nr=0) 
function preset(enc){
	//var ecn1 = document.getElementById('encounter_nr').value;
	//alert("encounter_nr=" + enc1);
	xajax_setALLDepartment(1);
	xajax_setDoctors(1,0);
	//xajax_setDepartments(doc, dept_nr);
	
	xajax_PopulateRow(enc);
}


