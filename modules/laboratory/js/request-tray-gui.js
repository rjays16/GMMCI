//added by VAN 10-02-09
function prepareAdd(id) {
	 xajax_getAllServiceOfPackage(id);
}

//for a service that is a package
function prepareAdd_Package(id,name,cash,charge,sservice,group,priceC1,priceC2,priceC3) {
	var details = new Object();
	var deptObj = $('request_dept_in');
	var doctObj = $('request_doctor_in');

	var res = checkRequestDetails();
	if (res){

		if ($('request_doctor_in').value==0){
			details.requestDoc= $('request_doctor_out').value;
			details.requestDocName= $('request_doctor_out').value;
		}else{
			details.requestDoc= $('request_doctor_in').value;
			details.requestDocName= doctObj.options[doctObj.selectedIndex].text;
		}

		if ($('request_dept_in').value==0){
			details.requestDept= $('request_dept_out').value;
			details.requestDeptName= $('request_dept_out').value;
		}else{
			details.requestDept= $('request_dept_in').value;
			details.requestDeptName= deptObj.options[deptObj.selectedIndex].text;
		}

		if ($('clinical_info').value){
			details.clinicInfo= $('clinical_info').value;
		}else{
			details.clinicInfo= "nothing";
		}

		//alert("impression = "+details.clinicInfo);
		details.is_in_house= $('is_in_house').value;

		details.id = id;
		details.name = name;

		details.prcCash = cash;
		details.prcCharge= charge;
		details.sservice = sservice;
		details.group = group;

		details.price_C1 = priceC1;
		details.price_C2 = priceC2;
		details.price_C3 = priceC3;

		var list = window.parent.document.getElementById('order-list');
		result = window.parent.appendOrder(list,details);
		//window.parent.refreshTotal();
		window.parent.refreshDiscount();
	}
	else
	{
		$('check'+id).checked = false;
	}
}
//----------------

//edited by VAN 10-02-09
//for a service that not a package
function prepareAdd_NotPackage(id) {
	var details = new Object();
	var deptObj = $('request_dept_in');
	var doctObj = $('request_doctor_in');

	//added by VAN 03-03-08
	//var list = $('order-list');
	//var dBody=list.getElementsByTagName("tbody")[0];
	//var str = dBody.innerHTML;
	//alert('prepareAdd = '+str);
	//if (str.match("Request list is currently empty")!=null)
		//details = null;

	var res = checkRequestDetails();
	if (res){

		if ($('request_doctor_in').value==0){
			details.requestDoc= $('request_doctor_out').value;
			details.requestDocName= $('request_doctor_out').value;
		}else{
			details.requestDoc= $('request_doctor_in').value;
			details.requestDocName= doctObj.options[doctObj.selectedIndex].text;
		}

		if ($('request_dept_in').value==0){
			details.requestDept= $('request_dept_out').value;
			details.requestDeptName= $('request_dept_out').value;
		}else{
			details.requestDept= $('request_dept_in').value;
			details.requestDeptName= deptObj.options[deptObj.selectedIndex].text;
		}

		if ($('clinical_info').value){
			details.clinicInfo= $('clinical_info').value;
		}else{
			details.clinicInfo= "nothing";
		}

		//alert("impression = "+details.clinicInfo);
		details.is_in_house= $('is_in_house').value;

		details.id = $('id'+id).innerHTML;
		details.name = $('name'+id).innerHTML;

		details.prcCash = $('cash'+id).value;
		details.prcCharge= $('charge'+id).value;
		details.sservice = $('sservice'+id).value;
		details.group = $('group'+id).value;

		details.price_C1 = $('price_C1'+id).value;
		details.price_C2 = $('price_C2'+id).value;
		details.price_C3 = $('price_C3'+id).value;

		var list = window.parent.document.getElementById('order-list');
		result = window.parent.appendOrder(list,details);
		//window.parent.refreshTotal();
		window.parent.refreshDiscount();
	}
	else
	{
		$('check'+id).checked = false;
	}
}

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

//function addProductToList(listID, id, name, cash, charge, sservice) {
function addProductToList(listID, id, name, cash, charge, sservice, group,price_C1,price_C2,price_C3, available) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i, label_but;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {

				alt = (dRows.length%2)+1;

				if (available==1){
					 label_but = '<td width="2%" align="center">'+
													'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px; cursor:pointer"'+
													 ' onclick="prepareAdd(\''+id+'\')" /> '+
												'</td>';
				} else
					 label_but = '<td width="2%" style="color:#FF0000" align="center">Unavailable</td>';

					rowSrc = '<tr class="wardlistrow'+alt+'">' +
											'<td width="*" align="left">'+
												'<span id="name'+id+'" style="font:bold 12px Arial">'+name+'</span><br />'+
											'</td>'+
											'<td width="15%" align="left">'+
												 '<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span>'+
												'<input id="sservice'+id+'" type="hidden" value="'+sservice+'"/></td>'+
												'<input id="group'+id+'" type="hidden" value="'+group+'"/></td>'+
												'<input id="price_C1'+id+'" type="hidden" value="'+price_C1+'"/></td>'+
												'<input id="price_C2'+id+'" type="hidden" value="'+price_C2+'"/></td>'+
												'<input id="price_C3'+id+'" type="hidden" value="'+price_C3+'"/></td>'+
											'</td>'+
											'<td align="center" width="20%">'+
												'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+cash+'</td>'+
											'<td align="center" width="20%">'+
												'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+charge+'</td>'+
											 ''+label_but+''+
										'</tr>';
		}
		else {
			/*
			if ((currentPage!=0) && (lastPage!=0) && (currentPage==lastPage))
				rowSrc = '<tr><td colspan="9">End of Records...</td></tr>';
			else
			*/
			rowSrc = '<tr><td colspan="9" style="">No such laboratory service exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

function jsSetDoctorsOfDept(){
	var aDepartment_nr = $F('request_dept_in');

	if (aDepartment_nr != 0) {
		xajax_setDoctors(aDepartment_nr,0);	//get the list of ALL doctors under "aDepartment_nr" department
	} else{
		xajax_setDoctors(0,0);	//get the list of ALL doctors from ALL departments
	}
}

function jsSetDepartmentOfDoc(){
	var aPersonell_nr = $F('request_doctor_in');

	if (aPersonell_nr != 0) {
		xajax_setDepartmentOfDoc(aPersonell_nr);
	}
	request_doc_handler();
}

function ajxClearDocDeptOptions(status) {
	var optionsList;
	var el;

	if (status==0){
		el=$('request_doctor_in');
	}else{
		el=$('request_dept_in');
	}

	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearDocDeptOptions */

function ajxAddDocDeptOption(status, text, value) {
	var grpEl;

	if (status==0){
		grpEl=$('request_doctor_in');
	}else{
		grpEl=$('request_dept_in');
	}

	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
}/* end of function ajxAddDocDeptOption */

function ajxSetDoctor(personell_nr) {
	$('request_doctor_in').value = personell_nr;
}

function ajxSetDepartment(dept_nr,list) {
	var current_dept = $('request_dept_in').value;
	var array = list.split(",");
	for (var x=0; x<array.length; x++){
		if (array[x]==current_dept){
			dept_nr=current_dept;
			break;
		}
	}
	$('request_dept_in').value = dept_nr;
}

function request_doc_handler(){
	var docValue = $F('request_doctor_in');
	var deptValue = $F('request_dept_in');

	if ((docValue==0)&&(deptValue==0)){
		$('is_in_house').value = '0';
		$('request_doctor_out').disabled = false;

		$('request_dept_out').disabled = false;

	}else{
		$('is_in_house').value = '1';

		$('request_doctor_out').value = '';
		$('request_doctor_out').disabled = true;
		$('request_dept_out').value = '';
		$('request_dept_out').disabled = true;

	}
}

function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}/* end of function trimString */

function checkRequestDetails(){
		var ptype = $('ptype').value;

		/*if (ptype){
			if (($F('request_doctor_in')=='0') && ($F('request_doctor_out')=='')){
				alert("Please specify the requesting doctor");
				$('request_doctor_out').focus();
				return false;
			}else

			if (($F('request_dept_in')=='0') && ($F('request_dept_out')=='')){
				alert("Please specify the requesting department.");
				$('request_dept_out').focus();
				return false;	
			}else	if (($F('clinical_info')=='')&&($F('request_doctor_out')=='')){
				alert("Please indicate the clinical information.");
				$('clinical_info').focus();
				return false;

			}
		}else{*/
			if (($F('request_dept_in')=='0') && ($F('request_dept_out')=='')){
				alert("Please specify the requesting department.");
				$('request_dept_out').focus();
				return false;
			}
		//}
		return true;
}

//added by CHA, MArch 20, 2010
function print_checklist(details)
{
	if(typeof(details)=="object")
	{
		var data_checklist="";
		if(details.data.length>0)
		{
			for(i=0;i<details.data.length;i++)
			{
				if(details.data[i]["type"]=="H")
				{
					$("section_label").innerHTML = details.data[i]["header"];
				}
				else if(details.data[i]["type"]=="D")
				{
					//added by VAN 06-26-10
					if (details.data[i]["available"]==1){
							label_but = "<td width='8%'>"+
														"<input type='checkbox' id='check"+details.data[i]["service_code"]+"' name='service_lists[]' onclick='if_unchecked(\""+details.data[i]["service_code"]+"\");'/>"+
													"</td>";
					} else
							label_but = "<td width='2%' style='color:#FF0000' align='center'>Unavailable</td>";

					data_checklist+="<tr class='wardlistrow"+i+"'>"+
						''+label_but+''+
						"<td width'*' align='left'>"+
							"<span id='name"+details.data[i]["service_code"]+"' style='font:bold 12px Arial'>"+details.data[i]["service_name"]+"</span><br/>"+
						"</td>"+
						"<td width='15%' align='left'>"+
							"<span id='id"+details.data[i]["service_code"]+"' style='font:bold 11px Arial;color:#660000'>"+details.data[i]["service_code"]+"</span>"+
							"<input id='sservice"+details.data[i]["service_code"]+"' type='hidden' value='"+details.data[i]["sservice"]+"'/>"+
							"<input id='group"+details.data[i]["service_code"]+"' type='hidden' value='"+details.data[i]["group_code"]+"'/>"+
							"<input id='price_C1"+details.data[i]["service_code"]+"' type='hidden' value='"+details.data[i]["service_cash"]+"'/>"+
							"<input id='price_C2"+details.data[i]["service_code"]+"' type='hidden' value='"+details.data[i]["service_cash"]+"'/>"+
							"<input id='price_C3"+details.data[i]["service_code"]+"' type='hidden' value='"+details.data[i]["service_cash"]+"'/>"+
						"</td>"+
						"<td width='20%' align='center'>"+
							"<input type='hidden' id='cash"+details.data[i]["service_code"]+"' value='"+details.data[i]["service_cash"]+"'/>"+details.data[i]["service_cash"]+
						"</td>"+
						"<td width='20%' align='center'>"+
							"<input type='hidden' id='charge"+details.data[i]["service_code"]+"' value='"+details.data[i]["service_charge"]+"'/>"+details.data[i]["service_charge"]+
						"</td>"+
					"</tr>";
				}
			}
		}
		else
		{
			data_checklist = '<tr><td colspan="9" style="">No such laboratory service exists...</td></tr>';
		}
		$("product-list").innerHTML = data_checklist;
	}
}

function if_unchecked(id)
{
	if($('check'+id).checked==0)
	{
		window.parent.removeItem(id);
	}
	else
	{
		prepareAdd(id);
	}
}
//end CHa