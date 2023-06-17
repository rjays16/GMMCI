var AJAXTimerID=0;
var lastSearch="";

function checkEnter(e,searchID){
	var characterCode; //literal character code will be stored in this variable

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		startAJAXSearch(searchID,0);
	}else{
		return true;
	}
}

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID+'2');
	var area = $("area").value;

	var is_cash = window.parent.$('is_cash').value;
	var discountid = window.parent.$('discountid').value;
	var discount = window.parent.$('discount').value;

	var var_area = window.parent.$('area');
	var var_ptype = window.parent.$('ptype').value;
	var notcash = window.parent.$('iscash0');
	var user_origin = window.parent.$('user_origin');
	var ref_source;
	var encounter_nr = window.parent.$('encounter_nr').value;
	var is_senior = window.parent.$('issc').checked;
	var pat_walkin = window.parent.$('is_walkin').checked;
	var area_type = window.parent.$('area_type').value;
	var is_walkin = 0;

	var source_req = window.parent.$('source_req').value;
	var patient_enctype = window.parent.$('patient_enctype').innerHTML;
	var is_charge2comp = window.parent.$('is_charge2comp');
	var compID = window.parent.$('compID');
	var urgent = window.parent.$('priority1');
	var isStat = 0;


	if (is_charge2comp)
		is_charge2comp = is_charge2comp.value;
	else
		is_charge2comp = 0;

	if (compID)
		compID = compID.value;
	else
		compID = '';

	if (patient_enctype=='IC')
		source_req = 'IC';

	if ((encounter_nr=="") || (pat_walkin))
			is_walkin = 1;

	if (urgent.checked)
		isStat = 1;

	switch (user_origin.value){
		case 'blood' :  ref_source = 'BB'; break;
		case 'lab' 	 :	ref_source = 'LB'; break;
		case 'splab' :  ref_source = 'SPL'; break;
		case 'iclab' :  ref_source = 'IC'; break;
	}

	if((var_ptype==1)&&(is_cash==0))
		ptype = "ER";
	else
		ptype = "";

	if (!is_senior){
		if (encounter_nr==""){
				 discountid = '';
				 discount = 0;
		}
	}

	var aLabServ = $("parameterselect2").value;

	var keyword;
	keyword = searchEL.value;
	keyword = keyword.replace("'","^");
	//alert('aLabServ = '+aLabServ);
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";

		AJAXTimerID = setTimeout("xajax_populateSpecialLabServiceList('"+ptype+"','"+area_type+"','"+encounter_nr+"','"+ref_source+"','"+is_cash+"','"+discountid+"','"+discount+"','"+is_senior+"','"+is_walkin+"','"+aLabServ+"','"+source_req+"','"+isStat+"','"+is_charge2comp+"','"+compID+"','"+searchID+"','"+keyword+"',"+page+")",100);
		lastSearch = searchEL.value;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}

function enableSearch(){
	var rowSrc, list;
	document.getElementById("search").value="";
	list = $('request-list');
	dBody=list.getElementsByTagName("tbody")[0];

	rowSrc = '<tr><td colspan="6" style="">No such laboratory service exists...</td></tr>';
	dBody.innerHTML = null;
	dBody.innerHTML += rowSrc;

	if (document.getElementById("parameterselect").value!="none"){
		document.getElementById("search").disabled = false;       //enable textbox for searching
		document.getElementById("search_img").disabled = false;   //enable image
	}else{
		document.getElementById("search").disabled = true;       //enable textbox for searching
		document.getElementById("search_img").disabled = true;   //enable image
	}
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function prepareAdd(id,code_num,iscode) {
     var key = $('search').value.split(",");
     var x=0;
     var searchkey = new Array();
     for(var i=0;i<key.length;i++){
         
          if (jQuery.trim(key[i].toUpperCase())!= id && iscode == 0 ){
                searchkey[x]= key[i];
                x++; 
          }else if(jQuery.trim(key[i])!= code_num && iscode == 1){
                searchkey[x]= key[i];
                x++; 
          }
     }
      
	 if (checkRequestDetails(id)){
		 var is_cash = window.parent.$('is_cash').value;
		 var discountid = window.parent.$('discountid').value;
		 var discount = window.parent.$('discount').value;
		 var is_senior = window.parent.$('issc').checked;
		 var pat_walkin = window.parent.$('is_walkin').checked;
		 var encounter_nr = window.parent.$('encounter_nr').value;
		 var user_origin = window.parent.$('user_origin');
		 var is_walkin = 0;
		 var urgent = window.parent.$('priority1');
		 var isStat = 0;

		 switch (user_origin.value){
				case 'blood' :  ref_source = 'BB'; break;
				case 'lab' 	 :	ref_source = 'LB'; break;
				case 'splab' :  ref_source = 'SPL'; break;
				case 'iclab' :  ref_source = 'IC'; break;
			}

		 if ((encounter_nr=="") || (pat_walkin))
				is_walkin = 1;

		 if (urgent.checked)
				isStat = 1;
         
         if(user_origin.value == "lab"){
            $('search').value = searchkey;   
         }else{
            $('search').value = ''; 
         }

		 //alert('js = '+is_cash+' - '+discountid+' - '+discount);
		 xajax_getAllServiceOfPackage(ref_source,id,is_cash,discountid,discount,is_senior, is_walkin, isStat);
	 }
}

//for a service that categorized as a package
function prepareAdd_Package(id,name,cash,charge,sservice,in_lis,oservice_code,ipdservice_code,group,net_price, is_blood_product) {
var details = new Object();
var deptObj = $('request_dept_in');
var doctObj = $('request_doctor_in');
var user_origin = window.parent.$('user_origin');
var isERIP = window.parent.$('isERIP');
var ptype = window.parent.$('ptype').value;
var area_type = window.parent.$('area_type').value;

if (isERIP)
	isERIP = isERIP.value;
else
	isERIP = '';

switch (user_origin.value){
	case 'blood' :  ref_source = 'BB'; break;
	case 'lab' 	 :	ref_source = 'LB'; break;
	case 'splab' :  ref_source = 'SPL'; break;
	case 'iclab' :  ref_source = 'IC'; break;
}

	//if (checkRequestDetails(id)){
		var cash = parseFloatEx(cash),
			charge = parseFloatEx(charge),
			qty=0;

		if ( isNaN(cash) || (cash < 0) || isNaN(charge) || (charge < 0) ) {
			alert("Price not set. Cannot add the product to the order yet...")
			return false
		}

		details.requestDept= $('request_dept').value;
		details.requestDoc= $('request_doctor').value;
		//details.requestDocName= $('request_doctor_name').value;
		if ($('request_doctor_in').value==0)
			 details.requestDocName = $('request_doctor_out').value;
		else
			 details.requestDocName = doctObj.options[doctObj.selectedIndex].text;
		
		details.is_in_house= $('is_in_house').value;
		details.clinicInfo= $('clinical_info').value;
		details.idGrp = group;
		details.id = id;
		details.qty = 1;
		details.name = name;
		details.prcCash = cash;
		details.prcCharge= charge;
		details.sservice= sservice;
		details.in_lis = in_lis;
		details.oservice_code = oservice_code;
        details.ipdservice_code = ipdservice_code;
        
        details.is_blood_product = is_blood_product;

		details.net_price = net_price;
		details.pay_type = '';

		var list = window.parent.document.getElementById('order-list');

		if (ref_source=='BB'){
			while (qty) {
			}
			while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
				qty = prompt("Enter quantity:")
				if (qty === null) return false;
			}
			details.qty = qty;
		}

		result = window.parent.appendOrder(list,details);
//		window.parent.refreshTotal();
		//window.parent.refreshDiscount();
		if (result)  {
			//alert('Item added to order list...');
			if (isERIP){
				if (user_origin.value=='lab'){
					if (((ptype==3)||(ptype==4))&&(area_type=='ch')){
						xajax_checkTestERLab(id);
					}
				}else{
					window.parent.enableSubmitButton(1);
				}

			}else{
					window.parent.enableSubmitButton(1);
			}
			 clearText();
		}
		else{
			alert('Failed to add item...');
			clearText();
		}
		if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount()
	//}

}

//for a service that not a package
function prepareAdd_NotPackage(id) {
var details = new Object();
var deptObj = $('request_dept_in');
var doctObj = $('request_doctor_in');
var user_origin = window.parent.$('user_origin');
var isERIP = window.parent.$('isERIP');
var ptype = window.parent.$('ptype').value;
var area_type = window.parent.$('area_type').value;

if (isERIP)
	isERIP = isERIP.value;
else
	isERIP = '';
 
switch (user_origin.value){
	case 'blood' :  ref_source = 'BB'; break;
	case 'lab' 	 :	ref_source = 'LB'; break;
	case 'splab' :  ref_source = 'SPL'; break;
	case 'iclab' :  ref_source = 'IC'; break;
}
	// alert(ref_source);
	//if (checkRequestDetails(id)){
		var cash = parseFloatEx($('cash'+id).value),
			charge = parseFloatEx($('charge'+id).value),
			qty=0;

		if ( isNaN(cash) || (cash < 0) || isNaN(charge) || (charge < 0) ) {
			alert("Price not set. Cannot add the product to the order yet...")
			return false
		}

		//alert($('discount_name'+id).value);
		details.requestDept= $('request_dept').value;
		details.requestDoc= $('request_doctor').value;

		if ($('request_doctor_in').value==0)
			 details.requestDocName = $('request_doctor_out').value;
		else
			 details.requestDocName = doctObj.options[doctObj.selectedIndex].text;
		
		details.is_in_house= $('is_in_house').value;
		details.clinicInfo= $('clinical_info').value;
		details.idGrp = $('idGrp'+id).innerHTML;
		details.id = $('id'+id).value;
		details.qty = 1;
		details.name = $('name'+id).innerHTML;
		//details.desc = $('desc'+id).innerHTML;
		details.prcCash = $('cash'+id).value;
		details.prcCharge= $('charge'+id).value;
		details.sservice= $('sservice'+id).value;
		details.in_lis = $('in_lis'+id).value;
		details.oservice_code = $('oservice_code'+id).value;
        details.ipdservice_code = $('ipdservice_code'+id).value;
        
        details.is_package = $('is_package'+id).value;
        details.is_profile = $('is_profile'+id).value;
        details.child_test = $('child_test'+id).value;
        
        //details.is_blood_product = $('is_blood_product'+id).value;
        
		details.net_price = $('net_price'+id).value;
		details.pay_type = '';

		var list = window.parent.document.getElementById('order-list');

		if (ref_source=='BB'){
			while (qty) {
			}
			while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0) {
				qty = prompt("Enter quantity:")
				if (qty === null) return false;
			}
			details.qty = qty;
		}

		result = window.parent.appendOrder(list,details);
		if (result){
			//alert('Item added to order list...');
			if (isERIP){
				if (user_origin.value=='lab'){
					if (((ptype==3)||(ptype==4))&&(area_type=='ch')){
						xajax_checkTestERLab(id);
					}
				}else{
					window.parent.enableSubmitButton(1);
				}

			}else{
					window.parent.enableSubmitButton(1);
			}
			 clearText();
		}else{
			alert('Failed to add item...');
			clearText();
		}
		if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount()
	//}
}

function enableButtonClear(isenable){
	 var items = window.parent.document.getElementsByName('items[]');
	 //var cnt = 0;
	 //alert(items.length);
	 if (isenable==1){
		//if (items.length > 1)
			//window.parent.enableSubmitButton(0);
		//else
			window.parent.enableSubmitButton(1);
			cnt += 1;
	 }else{
		if (cnt >= 1)
			window.parent.enableSubmitButton(1);
		else
			window.parent.enableSubmitButton(0);
	 }

	 clearText();
}

function clearText(){
	$('search').select();
	$('search').focus();
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

function addProductToList(listID, id, name, grp_code, code_num,iscode, cash, charge, sservice,in_lis,oservice_code,ipdservice_code,net_price, available, is_blood_product, is_package, is_profile, child_test) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	//alert("addProductToList : id = '"+id+"'");
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {

				if (available==1){
					 label_but = '<td width="2%" align="center">'+
													'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
														'onclick="prepareAdd(\''+id+'\',\''+code_num+'\',\''+iscode+'\')" '+
													'/>'+
												'</td>';
				} else
					 label_but = '<td width="2%" style="color:#FF0000" align="center">Unavailable</td>';

				// edited by VAN 02-09-2011
				var socialize_label;
				if(sservice==0)
					socialize_label = '<span class="socialize_label_id"><img src="../../images/btn_nonsocialized.gif" border="0" onClick="">&nbsp;</span>';
				else
					socialize_label = '<span align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #ff0000; width:15%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				// ------
                  
				rowSrc = "<tr>"+
									'<td width="*" align="left">'+
										socialize_label+
										'<span id="name'+id+'" style="font:bold 12px Arial">'+name+'</span><br />'+
									'	<input id="sservice'+id+'" type="hidden" value="'+sservice+'"/>'+
									'	<input id="in_lis'+id+'" type="hidden" value="'+in_lis+'"/>'+
                                    '   <input id="is_package'+id+'" type="hidden" value="'+is_package+'"/>'+
                                    '   <input id="is_profile'+id+'" type="hidden" value="'+is_profile+'"/>'+
                                    '   <input id="child_test'+id+'" type="hidden" value="'+child_test+'"/>'+
                                    '   <input id="is_blood_product'+id+'" type="hidden" value="'+is_blood_product+'"/>'+
									'	<input id="oservice_code'+id+'" type="hidden" value="'+oservice_code+'"/>'+
                                    '   <input id="ipdservice_code'+id+'" type="hidden" value="'+ipdservice_code+'"/>'+
									'	<input id="group'+id+'" type="hidden" value="'+grp_code+'"/>'+
									'	<input id="net_price'+id+'" type="hidden" value="'+net_price+'"/>'+
									'</td>'+
									'<td width="17%" align="left">'+
									'	<span id="idGrp'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span>'+
									'	<input id="id'+id+'" type="hidden" value="'+id+'"/>'+
									'</td>'+
									'<td align="right" width="15%">'+
										'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+cash+'</td>'+
									'<td align="right" width="15%">'+
										'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+charge+'</td>'+
									''+label_but+''+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="6" style="">'+$('caption').value+'</td></tr>';
		}

		dBody.innerHTML += rowSrc;
	}
}

function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}/* end of function trimString */


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

function checkRequestDetails(id){
		var ischecklist = window.parent.$('ischecklist').value;
		var ptype = window.parent.$('ptype').value;

		/*if (($F('request_doctor_in')=='0') && ($F('request_doctor_out')=='')){
				if ((ischecklist==1)&&((ptype!=5)&&(ptype!=6))){
					alert("Please specify the requesting doctor");
					$('request_doctor_in').focus();
					$('check'+id).checked = false;
					return false;
				}

		}else	if (($F('clinical_info')=='')&&($F('request_doctor_out')=='')){
				alert("Please indicate the clinical information.");
				$('clinical_info').focus();

				if ((ischecklist==1)&&((ptype!=5)&&(ptype!=6)))
					$('check'+id).checked = false;

				return false;
		}*/

		return true;
}

var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec = (parseInt(pageno)*pagen)+1;

	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;

	if (parseInt(total)==0)
		$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	else
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

	$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";

}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',parseInt(currentPage)-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			//alert('sulod ba');
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(lastPage));
		break;
	}
}
