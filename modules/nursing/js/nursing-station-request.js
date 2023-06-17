
function getTopCheck(parent, groupId){
	var count = 0;
	var parent2 = 'grp'+groupId;
	var p = document.form_test_request.parent2;
	var cList = p.getElementsByTagName("input");

	for(var i=1; i<cList.length; i++){
		if(cList[i].type == "checkbox"){
			if(cList[i].checked && cList[i].id != 'chk_all_'+groupId){
				return cList[i].id;
			}
		}
	}
	return null;	
} // end of function getTopCheck

function toggleDisplay2(id){
	var el= document.getElementById(id);
	if(el){
		if(el.style.overflow == "hidden") el.style.overflow="visible";
		else el.style.overflow="hidden";
	}
}/*end of function toggleDisplay2 */

function jsGetServiceGroup(){
	//alert("raddept_nr="+ $F('raddept_nr'));
	var raddept_nr = document.getElementById("raddept_nr").value;
	//alert("raddept_nr = "+raddept_nr);
	//document.getElementById("dept_nr").value = raddept_nr;
	xajax_getServiceGroup(raddept_nr);
	
}

function ajxClearOptions(){
	var optionList;
	var el = document.getElementById("paramselect");
	
	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
	
}/* end of function ajaxClearOptions */


function ajxAddOption(text, value){
	var grpEl = document.getElementById("paramselect");
	
	if (grpEl) {
	    var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
		
	//	if(value==0){
			//alert("grpEl.options[grpEl.selectedIndex].value->" + grpEl.options[grpEl.selectedIndex].value + "\n grpEl.options[grpEl.selectedIndex].text->"+ grpEl.options[grpEl.selectedIndex].text);
		//	xajax_clrTable(0,grpEl.options[grpEl.selectedIndex].text); 			
	//	}
	}
	var optionsList = grpEl.getElementsByTagName('OPTION');
	
}/* end of ajxAddOption */

	
function jsGetRadioService(mod){
	var aRadioServ = $("paramselect");
	var aRadioServ_grpid = aRadioServ.options[aRadioServ.selectedIndex].value;
	var aRadioServ_grpTxt = aRadioServ.options[aRadioServ.selectedIndex].text;
	
	//alert("jsGetRadioService->aRadioServ->"+aRadioServ_grpid);
	//xajax_srvGui(aRadioServ_grpid);
	if((mod)&&(aRadioServ_grpid!=0)){
		xajax_srvGui(aRadioServ_grpid, aRadioServ_grpTxt);
		xajax_getAjxGui(aRadioServ_grpid);
	}else{
		xajax_getAjxGui(0);
		xajax_getConstructedTab();
	}	
}

 
function appendServiceItemGroup(groupId, code, name, price, chk){
     var dBody = document.getElementById("grpBody"+groupId);
	//var srvList = document.getElementById("srvlist");
	//alert("dBody="+dBody+ "  groupId-> "+"grpBody"+groupId);
	
	if(dBody){
		var dRows, lastRowNo, newRowSrc;
		dRows = dBody.getElementsByTagName("tr");
		
		if(dRows.length > 0) lastRowNo = dRows[dRows.length-1].id.replace("svcRow", "");
		lastRowNo = isNaN(lastRowNo) ? 0:(lastRowNo-0)+1;
		
		//alert("lastRowNo="+ lastRowNo);
		if(code){
			if(chk!=0){
				/*newRowSrc = '<tr class="wardlistrow1" id="svcRow'+lastRowNo+'">' +
								'<td><input type="checkbox" name="svcChk'+lastRowNo+'" id="svcChk'+lastRowNo+'" value="'+code+'" checked onChange="countItem(\''+groupId+'\',0);"></td>'+
								'<td>'+code+'<input type="hidden" id="svcCode'+lastRowNo+'" value="'+code+'"></td>'+
								'<td>'+name+'<input type="hidden" id="svcName'+lastRowNo+'" value="'+name+'"></td>'+
								'<td align="right">'+price+'<input type="hidden" id="svcPrice'+lastRowNo+'" value="'+price+'"></td>'+
							'</tr>';
			 */	
				newRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" id="svcRow'+lastRowNo+'">' +
								'<td><input type="checkbox" name="svcChk['+lastRowNo+']" id="svcChk['+lastRowNo+']" value="'+code+'" checked onChange="countItem(\''+groupId+'\',0);"></td>'+
								'<td>'+code+'<input type="hidden" id="svcCode'+lastRowNo+'" value="'+code+'"></td>'+
								'<td>'+name+'<input type="hidden" id="svcName'+lastRowNo+'" value="'+name+'"></td>'+
								'<td align="right">'+price+'<input type="hidden" id="svcPrice'+lastRowNo+'" value="'+price+'"></td>'+
							'</tr>';	
			}else{

				newRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" id="svcRow'+lastRowNo+'">' +
								'<td><input type="checkbox" name="svcChk['+lastRowNo+']" id="svcChk['+lastRowNo+']" value="'+code+'" onChange="countItem(\''+groupId+'\',0);" ></td>'+
								'<td>'+code+'<input type="hidden" id="svcCode'+lastRowNo+'" value="'+code+'"></td>'+
								'<td>'+name+'<input type="hidden" id="svcName'+lastRowNo+'" value="'+name+'"></td>'+
								'<td align="right">'+price+'<input type="hidden" id="svcPrice'+lastRowNo+'" value="'+price+'"></td>'+
							'</tr>';
								//alert("else->"+newRowSrc);
			}
		}
		dBody.innerHTML += newRowSrc;
		//srvList.innerHTML += newRowSrc;
	}
}



function guiSrvTabContent(No,batchNo,dateRequest ,srvCode, srvName,grpCode, encounter){
	var dRows1,dRowSrc1, lastRowNo1;
	var dRows,dRowSrc, lastRowNo;
	var grpdtBody, tabValue, idsrvRow;
	var atBody  = document.getElementById("grpTabALL");
    
     //alert("guiSrvTabContent: batchNo="+batchNo+"\n dateRequest="+dateRequest+"\n srvCode="+srvCode+"\n srvName="+srvName);
	//alert(" guiSrvTabContent:  grpCode = "+ grpCode);
	
	switch (grpCode){
		case '164': tabValue = 'GR'; idsrvRow = 'srvRowGR';
		break;
		case '165': tabValue = 'US'; idsrvRow = 'srvRowUS';
		break;
		case '166': tabValue = 'SP'; idsrvRow = 'srvRowSP';
		break;    
		case '167': tabValue = 'CT'; idsrvRow = 'srvRowCT';
		break;
	}
	
	dtBody = document.getElementById("grpTab"+tabValue);
	//alert("dtBody="+ dtBody + " \n grpTab=grpTab"+tabValue);
	if(dtBody){
		dRows = dtBody.getElementsByTagName("tr");

		//get the last row id and extract the current row no.
		if(dRows.length >0 ) lastRowNo = dRows[dRows.length-1].id.replace(idsrvRow,"");
		lastRowNo = isNaN(lastRowNo) ? 0:(lastRowNo-0)+1;
//		alert(" lastRowNo = isNaN(lastRowNo) ? 0:(lastRowNo-0)+1; -->"+ lastRowNo);
		
		if(srvCode){
			
			dRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" id="'+idsrvRow+lastRowNo+'" >'+
							'<td style="padding-left:4px" align="left">'+No+'<input type="hidden" id="srvRow'+lastRowNo+'" value="'+No+'" ></td>'+
							'<td style="padding-left:4px" align="left">'+batchNo+'<input type="hidden" id="srvRow'+lastRowNo+'" value="'+batchNo+'" ></td>'+
							'<td style="padding-left:4px" align="left">'+dateRequest+'<input type="hidden" id="srvRow'+lastRowNo+'" value="'+dateRequest+'" ></td>'+
					   		'<td style="padding-left:4px" align="left">'+srvCode+'<input type="hidden" id="srvRow'+lastRowNo+'" value="'+srvCode+'" ></td>'+
					   		'<td style="padding-left:4px" align="left">'+srvName+'<input type="hidden" id="srvRow'+lastRowNo+'" value="'+srvName+'"></td>'+
					   		'<td style="padding-left:4px" align="center"><input type="button" id=srvRowRmv'+lastRowNo+'" value="x" onclick="xajax_delSrv(\''+tabValue+'\',\''+lastRowNo+'\',\''+batchNo+'\', \''+encounter+'\');" style="width:20px; height:20px">'+
					  '</tr>';
		}else{
			dRowSrc =  '<tr class="wardlistrow1" id="srvRow'+lastRowNo+'">' +
							'<td colspan="4">No services are available for this group...</td>' +
			 			'</tr>';
		}	
		
		//alert("guiSrvTabContent: dRowSrc="+dRowSrc);
		
		dtBody.innerHTML += dRowSrc;
	}
			
	//return false;
} /* end of function guiSrvTabContent */


function guiSrvTabAll(No,batchNo,dateRequest ,srvCode, srvName,grpCode,encounter_nr){
	var dRows,dRowSrc, lastRowNo, grpIndex;
	var atBody  = document.getElementById("grpTabALL");
    
     //alert("guiSrvTabContent: batchNo="+batchNo+"\n dateRequest="+dateRequest+"\n srvCode="+srvCode+"\n srvName="+srvName);
	//alert(" guiSrvTabContent:  grpCode = "+ grpCode);
	switch (grpCode){
		case '164': grpIndex = 'GR'; break;
		case '165': grpIndex = 'US'; break;
		case '166': grpIndex = 'SP'; break;
		case '167': grpIndex = 'CT'; break;
	}
	if(atBody){
		dRows = atBody.getElementsByTagName("tr");
		//alert("atBody="+ atBody+ "\n dRows="+dRows+" \n srvCode="+srvCode );
		//get the last row id and extract the current row no.
		if(dRows.length >0 ) lastRowNo = dRows[dRows.length-1].id.replace("srvRowAll"+grpIndex,"");
		lastRowNo = isNaN(lastRowNo) ? 0:(lastRowNo-0)+1;
		
		if(srvCode){
			dRowSrc = '<tr class="wardlistrow'+(lastRowNo%2)+'" id="srvRowAll'+grpIndex+lastRowNo+'" >'+
							'<td style="padding-left:4px" align="left">'+No+'<input type="hidden" id="srvRow'+lastRowNo+'" value="'+No+'" ></td>'+
							'<td style="padding-left:4px" align="left">'+batchNo+'<input type="hidden" id="srvRowBatch'+lastRowNo+'" value="'+batchNo+'" ></td>'+
							'<td style="padding-left:4px" align="left">'+dateRequest+'<input type="hidden" id="srvRowDate'+lastRowNo+'" value="'+dateRequest+'" ></td>'+
					   		'<td style="padding-left:4px" align="left">'+srvCode+'<input type="hidden" id="srvRowCode'+lastRowNo+'" value="'+srvCode+'" ></td>'+
					   		'<td style="padding-left:4px" align="left">'+srvName+'<input type="hidden" id="srvRowName'+lastRowNo+'" value="'+srvName+'"></td>'+
					   		'<td style="padding-left:4px" align="center"><input type="hidden" id="srvRowGrp'+grpIndex+'" value="'+grpIndex+'"><input type="button" id=srvRowRmv'+lastRowNo+'" value="-"  style="width:20px; height:20px">'+
					  '</tr>';
		}else{
			dRowSrc =  '<tr class="wardlistrow1" id="srvRowAll'+lastRowNo+'">' +
							'<td colspan="4">No services are available for this group...</td>' +
			 			'</tr>';
		}	
		
		//alert("guiSrvTabContent: dRowSrc="+dRowSrc);
		
		atBody.innerHTML += dRowSrc;
	}
	//return false;
} /* end of function guiSrvTabAll */

function guiSrvDelete(tabValue,rowNum){
	var destTable, destRows, rmvRow;
	
	//alert("guiSrvDelete: tabValue = "+ tabValue + " \n rowNum = "+ rowNum);

	if(destTable = document.getElementById("srvTable"+tabValue)){
		destRows = destTable.getElementsByTagName("tbody")[0];
		
		//alert("guiSrvDelete: destTable="+destTable + "\n destRows = " +  destRows);
		
		if(destRows){
			rmvRow = document.getElementById("srvRow"+tabValue+rowNum);
		//	alert("rmvRow = "+rmvRow +" \n srvRow = "+ tabValue+rowNum);
			
			destRows.removeChild(rmvRow);	
			return true; // success 
		}
		else return false; //fail
	}
	else return false; // fail
}/* end of function guiSrvDelete */


function guiSrvClearRows(grpId){
	var srcTable, srcRows, srcTBody, tbIndex;
	
	switch(grpId){
		case '164': tbIndex = 'GR'; break;
		case '165': tbIndex = 'US'; break;
		case '166': tbIndex = 'SP'; break;
		case '167': tbIndex = 'CT'; break;
		default : tbIndex = 'ALL';
	}
	
	if(srcTable = document.getElementById("srvTable"+tbIndex)){
		srcTBody = srcTable.getElementsByTagName("tbody")[0];
		srcRows = srcTBody.childNodes;
		if(srcRows){
			while(srcRows.length > 0){
				srcTBody.removeChild(srcRows[0]);
			}
			return true;
		}else return false;
	}else return false;	
}/* End of function guiSrvClearRows */

//Display for no services are available for this group
function appendServiceItemToGroup2(groupID) {
	//var dBody = document.getElementById('grpBody'+groupID);
	if(groupID) var dBody = document.getElementById('grpBody'+groupID);
	else var dBody = document.getElementById('grpBody');
	if (dBody) {
			var dRows, lastRowNo, newRowSrc;
			dRows=dBody.getElementsByTagName("tr");
			
			// get the last row id and extract the current row no.
			if (dRows.length>0) lastRowNo=dRows[dRows.length-1].id.replace("dRow","");
			lastRowNo=isNaN(lastRowNo)?0:(lastRowNo-0)+1;	
			
			newRowSrc = '<tr class="wardlistrow1" id="srcRow'+lastRowNo+'">' +
				'<td colspan="4">No services are available for this group...</td>' +
			 '</tr>';
	}
		dBody.innerHTML += newRowSrc;
}

function ajxClearTable(groupID){
	//alert("ajxClearTable");	
	var dBody = document.getElementById('grpBody'+groupID);
	//alert("dBody grp = "+dBody);
	if (dBody) {
		dBody.innerHTML = " ";
	}
}



//function countItem(groupId, mod){
function countItem(groupId, mod){
	$("selectedcount").innerHTML = countSelected(groupId, mod); //countSelected("grp"+groupId, mod);
}/*end of function countItem */

function countSelected(parent,mod) {
	var count=0;
	var p=$('grpBody'+parent);
	var cList=p.getElementsByTagName('input');		
	//alert("/cList->"+cList+" /p->"+p+ " \n /parent->"+parent + "/mod ->"+ mod + "\n cList.length->"+cList.length);
	var allCheckbox = 0;
	for (var i=0;i<cList.length;i++) {
		if (cList[i].type=="checkbox") {
			if (cList[i].checked&&cList[i].id!='chk_all_') count++;	
			allCheckbox++;
		}
	}
	
	if(allCheckbox != count){
		$('chk_all_'+parent).checked = false;
	}
	if (count < 0)
		count = 0;
	
	return count;
}

function checkAll(flag){
	var p= $("listcontainer");
	var cList = p.getElementsByTagName("input");
	
	//alert("checkAll->cList->"+cList+ "\n cList.length->"+cList.length);
	for (var i=0; i<cList.length; i++){
		if(cList[i].type == "checkbox")
			cList[i].checked = flag;
	}
}/* end of function checkAll */

/*
function gui_clearTable(id){
	var dTable, dRows, dTBody;
	var rows;
	
	if(dTable = document.getElementById(id)){
		dTBody = dTable.getElementsByTagName("tbody")[0];
		dRows = dTBody.childNodes;
		
		if(dRows){
			while(dRows.length>0){
				dTBody.removeChild(dRows[0]);
			}
			rows = 0;
	    	return true; // success
		}else return false; // fail	
	}else return false;  //fail
} */
/* end of function gui_clearTable */


function gui_delRow(tableId, rowId, rowNo){
	var rmRow;
	rmRow = document.getElementById(rowId+rowNo);
	if(rmRow){
		rmRow.parentNod.removeChild(rmRow);
		return true;
	}
	else return false;
}/* end of function gui_delRow */


function xrow(rowno, code, name, cash, charge) {
	var dRowSrc,
		cashprc, chargeprc;
	if (code) {
		if (isNaN(cash))
			cashprc="N/A";
		else {
			cashprc=cash-0;
			cashprc=cashprc.toFixed(2);
		}
		if (isNaN(charge))
			chargeprc="N/A";
		else{
			chargeprc=charge-0;
			chargeprc=chargeprc.toFixed(2);
		}
		dRowSrc = 
					'<td style="padding-left:4px" align="left">'+code+'<input type="hidden" id="srvCode'+rowno+'}" value="'+code+'"></td>'+
					'<td style="padding-left:4px">'+name+'<input type="hidden" id="srvname'+rowno+'" value="'+name+'"></td>'+
					'<td style="padding-right:6px" align="right"><b>'+cashprc+'<input type="hidden" id="srvCash'+rowno+'" value="'+cashprc+'"></b></td>'+
					'<td style="padding-right:6px" align="right"><b>'+chargeprc+'<input type="hidden" id="srvCharge'+rowno+'" value="'+chargeprc+'"></b></td>'+
					'<td align="center"><a href="" id="srvEdit'+rowno+'" onclick="editService(\''+code+'\','+rowno+');return false;" style="text-decoration:underline">Edit</a></td>'+
					'<td align="center"><a href="" id="srvDel'+rowno+'" onclick="if(confirm(\'Do you wish to remove this service?\')) { xajax_dsrv('+rowno+',\''+code+'\'); } return false;" style="text-decoration:underline">Delete</a></td>';
	}
	else {		
		dRowSrc = '<tr id="srvRow'+rowno+'">' +
			'<td colspan="6">No services are available for this group...</td>' +
			'</tr>';
	}
//'<td colspan="6"><input type="text" id="groupid" value="'+name+'"></td>' +
/*
var color;
 	if ($('srvRow'+rowno).className.indexOf("wardlistrow1")!=-1) 
		color=rowColors[0];
	else
		color=rowColors[1]; */
	$('srvRow'+rowno).innerHTML=dRowSrc;
	fader('srvRow'+rowno);	
}

function jsGetServices(){
	var aGroup_id = $F("raddept_nr");
	var refno = $F("refno");
	var aIsCash;
	
	if($F('is_cash') == 1){
		aIsCash = 1;
	}else{
		aIsCash = 0;
		
		//xajax_populateService(populateServices(aGroup_id, aIsCash, refno, "none");
	}
	
}

function jsViewServices(){
	jsGetServices();
	//insert additional code here.
}

//		BURN ADDED : August 7, 2007

		/*	
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words 
				input: object
				output: object (string) value is trimmed
		*/
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," "); 
}/* end of function trimString */


function checkRequestForm(d){

	if (($F('is_in_house')=='0') && ($F('request_doctor_out')=='')){
		alert("Please specify the requesting doctor");
		$('request_doctor_out').focus();
		return false;	
	//}else{
		/* kuya mark : supply in here other required fields... :=) */
	}else if($F('clinical_info') == ''){
			alert("Please indicate the clinical information.");
			$('clinical_info').focus();
			return false;
		//}
		
	}
	
	if ($F('is_in_house')=='1'){
		$('request_doctor').value = $F('request_doctor_in');
	}else{
		$('request_doctor').value = $F('request_doctor_out');
	}
	return true;
}

	/*
	*	Clears the list of options [0, doctors; 1, departments]
	*	burn added ; August 6, 2007
	*/
function ajxClearDocDeptOptions(status) {
	var optionsList;
	var el;

	if (status==0){
		el=$('request_doctor_in');
	}else{
		el=$('request_dept');
	}
	 
	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearDocDeptOptions */

	/*
	*	Adds an item in the list of options [0, doctors; 1, departments]
	*	burn added ; August 6, 2007
	*/
function ajxAddDocDeptOption(status, text, value) {
	var grpEl;

	if (status==0){
		grpEl=$('request_doctor_in');
	}else{
		grpEl=$('request_dept');
	}
	
	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
}/* end of function ajxAddDocDeptOption */

function ajxSetDepartment(dept_nr) {
	$('request_dept').value = dept_nr;
}

function ajxSetDoctor(personell_nr) {
	//alert("personell_nr = "+personell_nr);
	$('request_doctor_in').value = personell_nr;
}

function jsSetDoctorsOfDept(){
	var aDepartment_nr = $F('request_dept');
		
//		alert("jsRequestDoctor : aDepartment_nr ='"+aDepartment_nr+"'");

	if (aDepartment_nr != 0) {
		xajax_setDoctors(aDepartment_nr,0);	//get the list of ALL doctors under "aDepartment_nr" department
	} else{
		xajax_setDoctors(0,0);	//get the list of ALL doctors from ALL departments
	}	
}

function jsSetDepartmentOfDoc(){
	var aPersonell_nr = $F('request_doctor_in');
		
//		alert("jsRequestDepartment : aPersonell_nr ='"+aPersonell_nr+"'");

	if (aPersonell_nr != 0) {
		xajax_setDepartmentOfDoc(aPersonell_nr);
	}
	request_doc_handler();
}

function request_doc_handler(){
	var docValue = $F('request_doctor_in');
	
	if (docValue==0){
		$('is_in_house').value = '0';
		$('request_doctor_out').disabled = false;
		$('request_doctor').value = '';
	}else{
		$('is_in_house').value = '1';
		$('request_doctor').value = $F('request_doctor_in');
		$('request_doctor_out').value = '';		
		$('request_doctor_out').disabled = true;		
	}
}