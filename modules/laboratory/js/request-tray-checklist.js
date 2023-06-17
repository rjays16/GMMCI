function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
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

function print_checklist(details, nr)
{
	var dataSrc = "";
	var headerSrc = "";
	var bodySrc = "";
	var cash_label;
	var divSrc = $('checklist-div');
	if(details)
	{
		if(details.length) {
			var source="";
			for(i=0;i<details.length;i++)
			{
				cur=details[i]["type"];
				if(details[i]["type"]=="H")
				{
					 source+= '<div style="margin-top:10px"><span style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d;" align="center">'+details[i]["header"]+'</span></div>';
				}
				else if(details[i]["type"]=="D")
				{
						var available = '<span align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #ff0000; width:15%">&nbsp;</span>';
						if(details[i]["available"]=="1")
						{
							 chklist = "<span class='checklist-add'><input type='checkbox' id='check"+details[i]["service_code"]+"' name='service_lists[]' onclick='if_unchecked(\""+details[i]["service_code"]+"\");'/></span>";
						}else
						{
								chklist = "<span class='checklist-add'><input type='checkbox' id='check"+details[i]["service_code"]+"' name='service_lists[]' disabled=''/></span>";
								available = '&nbsp;&nbsp;<span class="checklist-available">UNAVAILABLE</span>';
						}

						// edited by VAN 02-09-2011
						var socialize_label;
						if(details[i]["sservice"]==0)
							socialize_label = '<span class="socialize_label_id"><img src="../../images/btn_nonsocialized.gif" border="0" onClick=""></span>';
						else
							socialize_label = '<span align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #ff0000; width:15%">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
						// ------

						if (window.parent.$("iscash1").checked){
							cash_label = "<span id='cash2"+details[i]["service_code"]+"' class='checklist-price'>"+formatNumber(details[i]["service_cash"],2)+"</span>";
						}else{
							cash_label = "<span id='cash2"+details[i]["service_code"]+"' class='checklist-price'>"+formatNumber(details[i]["service_charge"],2)+"</span>";
						}

						//edited by VAN 07-30-2010
						//delete C1-C3 info pricelist
					 source+= "<div class=\"checklist-data\">"+
							socialize_label+
							chklist+
							"<span id='idGrp"+details[i]["service_code"]+"' class='checklist-code'>"+details[i]["service_code"]+"</span>"+
							"<span id='name"+details[i]["service_code"]+"' class='checklist-name'>"+details[i]["service_name"]+"</span>"+
							cash_label+
							"<input id='cash"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["service_cash"]+"'/>"+
							"<input id='id"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["service_code"]+"'/>"+
							"<input id='charge"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["service_charge"]+"'/>"+
							"<input id='sservice"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["sservice"]+"'/>"+
							"<input id='group"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["group_code"]+"'/>"+
							"<input id='net_price"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["service_net_price"]+"'/>"+
							"<input id='in_lis"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["service_in_lis"]+"'/>"+
							"<input id='is_package"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["service_is_package"]+"'/>"+
							"<input id='is_profile"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["service_is_profile"]+"'/>"+
                            "<input id='child_test"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["service_child_test"]+"'/>"+
                            "<input id='oservice_code"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["oservice_code"]+"'/>"+
                            "<input id='ipdservice_code"+details[i]["service_code"]+"' type='hidden' value='"+details[i]["ipdservice_code"]+"'/>"+
                            "<input id='is_blood_product"+details[i]["is_blood_product"]+"' type='hidden' value='"+details[i]["service_is_blood_product"]+"'/>"+
						available+"</div>";
				}
			}
			divSrc.innerHTML+=source+'<br/>';
		}
	}else{
		headerSrc = '<div id="section_label" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d;" align="center">'+
												'SERVICE NOT FOUND..'+
												'</div>';
		divSrc.innerHTML=headerSrc;
	}
}

function clearChecklist()
{
	$('checklist-div').innerHTML="";
}

function print_checklist_message(text)
{
	headerSrc = '<div id="section_label" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d;" align="center">'+
												text+
												'</div>';
	$('checklist-div').innerHTML=headerSrc;
}
//end CHa

//added by VAN 08-17-2010
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


function ajxSetDepartment(dept_nr,list) {
		var current_dept = $('request_dept').value;
		var array = list.split(",");
		for (var x=0; x<array.length; x++){
			if (array[x]==current_dept){
				dept_nr=current_dept;
				break;
			}
		}
		$('request_dept').value = dept_nr;
}

function ajxSetDoctor(personell_nr) {
	$('request_doctor_in').value = personell_nr;
}

function jsSetDoctorsOfDept(){
	var aDepartment_nr = $F('request_dept');

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

//---------------