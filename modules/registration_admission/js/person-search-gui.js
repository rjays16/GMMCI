var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var HSM = "HOSPITAL SPONSORED MEMBER";
var NBB = "SPONSORED MEMBER";

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function display(str) {
	document.write(str);
}

function prepareSelect(id) {
	var nr = $('nr'+id).value;
	var real_parent_discountid = $('real_parent_discountid'+id).value;
	var discountid = $('discountid'+id).value;
	var orig_discountid = $('orig_discountid'+id).value;
	var discount = $('discount'+id).value;
	var id = $('id'+id).innerHTML;
	var rid = $('rid'+id).value;
	var lname = $('lname'+id).innerHTML;
	var fname = $('fname'+id).innerHTML;
	var mname = $('mname'+id).innerHTML;
	var addr = $('addr'+id).innerHTML;
	var type = $('type'+id).value;
	var gender = $('gender'+id).value; //added by omick
	var age = $('age'+id).value;	//added by omick
	var date_admitted = $('date_admitted'+id).value; //added by omick, may 26, 2009
	var room_ward = $('room_ward'+id).value; //added by omick, may 26, 2009
	var adm_diagnosis = $('adm_diagnosis'+id).innerHTML;
	var dob = $('dob'+id).value;	//added by van
	var in_walkin = $('in_walkin'+id).value; //added by van
	var has_insurance = $('has_insurance'+id).value; //added by cha, august 17, 2010
    var mem_category = $('category'+id).value; // added by pol
    var mem_totalcharges = $('totalCharges'+id).value; // added by Dommie
    var chargetype = window.parent.$('charge_type'); //added by pol
    //added by VAN 06-02-08
	var enctype = $('enctype'+id).value;
	var location = $('location'+id).value;
	var is_medico = $('is_medico'+id).value;
	 //alert(orig_discountid);
	//added by VAN 06-25-08
	var senior_citizen = $('senior_citizen'+id).value;
		//alert(window.parent.$("iscash1").checked);
	var parent_discountid = $('parent_discountid'+id).value;

	var admission_dt = $('admission_dt'+id).value;	//added by van
	var discharge_date = $('discharge_date'+id).value;	//added by van

    var is_maygohome = $('is_maygohome'+id).value;    //added by van
    var bill_nr = $('bill_nr'+id).value;    //added by van
    var hasfinal_bill = $('hasfinal_bill'+id).value;    //added by van

    //Added By Jarel 07/24/
    var auto_senior = window.parent.$('auto_senior');
    if(auto_senior){
        var new_age = age.split(" ")
        window.parent.$('auto_senior').value = ((new_age[0] >= 60) ? 1 : 0 );
    }
    //End Jarel

		//added by VAN 05-20-2010
		if (var_other){
			var iscash = window.parent.$("iscash1").checked;
			var hideOtherCharges = window.parent.$("hideOtherCharges").value;

			if ((iscash==false)&&(hideOtherCharges==0))
				// can add other charges
					showbtn = 1;
			else
					showbtn = 0;
		}

		//added by VAN 08-19-2010
		var dept_area = window.parent.$('dept_area');
		if (dept_area){
			if (dept_area.value=='lab'){
				var isERIP = window.parent.$("isERIP").value;
				var isSTAT = window.parent.$('priority1').checked;

				if (isERIP==1){
					 var btnSubmit = window.parent.$("btnSubmit");
					 if ((type==1)||($('area_type'+id).value=='pw')){
							btnSubmit.setAttribute("class","");
							btnSubmit.style.cursor='pointer';
							btnSubmit.setAttribute("onclick","if (confirm(\'Process this request?\')) if (checkRequestForm()) document.inputform.submit()");
					 }else{
							btnSubmit.setAttribute("class","disabled");
							btnSubmit.style.cursor='default';
							btnSubmit.setAttribute("onclick","");
					 }
				}
			}
		}

	//added by VAN 09-17-2010
	var user_origin = window.parent.$('user_origin');
	if (user_origin){
			//alert(user_origin.value);
			if (user_origin.value=='blood'){
					var is_rdu = window.parent.$('is_rdu');
				 //alert($('currenttime').value);
				 if (((parseInt($('currenttime').value) <= 5)||(parseInt($('currenttime').value) >= 23))&&(!is_rdu.checked)){
						window.parent.setPriority(1);
				 }else{
						window.parent.setPriority(0);
				 }
			}
	}

	//added by VAN 08-24-2010
	if (enctype=='IC'){
		 var is_charge2comp = window.parent.$("is_charge2comp");
		 var compName = window.parent.$("compName");
		 var compID = window.parent.$("compID");
		 var ic_row = window.parent.$("ic_row");
		 var source_req = window.parent.$("source_req");

		 //if (source_req)
				//source_req.value = source_req.value;

		 if (ic_row){
			 if (is_charge2comp){
					if ($('is_charge2comp'+id).value==1){
						ic_row.style.display='';
						is_charge2comp.checked = true;

						if (compName)
							compName.innerHTML = $('compName'+id).value;

						if (compID)
							compID.value = $('compID'+id).value;
					}else{
							is_charge2comp.checked = false;
							compName.innerHTML = "";
							compID.value = "";
					}
			 }else{
					ic_row.style.display='none';
			 }
		 }
	}else if (enctype=='RDU'){
		var is_rdu = window.parent.$("is_rdu");

		if (is_rdu)
			is_rdu.checked=true;
	}else{
		var is_rdu = window.parent.$("is_rdu");
		if(is_rdu)
			is_rdu.checked=false;
	}
	//----------

		//var ward_nr = $('ward_nr'+id).value;

	/** Added by omick **/
	if (var_gender)
		window.parent.$(var_gender).value = gender;
	if (var_age)
		window.parent.$(var_age).value = age;
	/** End omick **/

	/** Added by omick, may 26, 2009 **/
	if (var_date_admitted)
		window.parent.$(var_date_admitted).value = date_admitted;
	if (var_room_ward) {
		window.parent.$(var_room_ward).value = room_ward;
	}
	/** End omick **/

	if (var_adm_diagnosis) {
		var obj=window.parent.$(var_adm_diagnosis);
		if (obj) obj.update(adm_diagnosis);
	}

	if (var_pid)
		window.parent.$(var_pid).value = id;
	if (var_rid)
		window.parent.$(var_rid).value = rid;
	if (var_encounter_nr)
		window.parent.$(var_encounter_nr).value = nr;
	if (var_discountid)
		window.parent.$(var_discountid).value = discountid;

	//added by cha, may 19,2010
		//edited by VAN 05-20-2010
	var val=$('dept_nr'+id).value;
	if(var_dept_nr)
		window.parent.$(var_dept_nr).value = val;
	else{
		var var_dept_nr = window.parent.$('current_dept_nr');
		if(var_dept_nr)
			var_dept_nr.value = val;
	}

	var var_doc_nr = window.parent.$('current_att_dr_nr');
	if(var_doc_nr)
		var_doc_nr.value = $('doc_nr'+id).value;

	var var_impression = window.parent.$('impression');
	if(var_impression)
		var_impression.value = $('adm_diagnosis'+id).innerHTML;

	var var_adm_diagnosis2 = window.parent.$('adm_diagnosis');
	if(var_adm_diagnosis2 )
		var_adm_diagnosis2 .innerHTML = $('adm_diagnosis'+id).innerHTML;

	if(var_ward_nr)
		window.parent.$(var_ward_nr).value = $('ward_nr'+id).value;
	
	if(var_room_nr)
		window.parent.$(var_room_nr).value = $('room_nr'+id).value;

    var insurance_nr = window.parent.$('phic_nr');    
   
	//added by cha, august 17, 2010
	if(insurance_nr) {
		insurance_nr.innerHTML = has_insurance;
              //added by pol
		
	}
	//end cha
    
    //added by pol, 01/26/2013
    var category = window.parent.$('mem_category');
    // alert(memcategory_desc);
    if(category){
    	if (mem_category==HSM || mem_category==NBB) {
    		category.style.color = "red";
    	}
        category.innerHTML = mem_category;
    }
    //end pol
    
    //added by Dommie, 03/10/2014
    var totalCharges = window.parent.$('mem_totalcharges');
    // alert(memcategory_desc);
    if(totalCharges){
        totalCharges.innerHTML = mem_totalcharges;
    }
    //end pol
    
    
     var var_orig_discountid = window.parent.$('orig_discountid');
	 if (var_orig_discountid)
				window.parent.$('orig_discountid').value = orig_discountid;

	if (var_discount)
		window.parent.$(var_discount).value = discount;
	if (var_name) {
		//window.parent.$(var_name).value = fname + " " + lname;
		if (mname)
			mname = " " + mname.substring(0,1)+".";
		var sname = '';
		if (lname) {
			if (fname) sname = lname + ", " + fname + mname;
			else sname = lname;
		}
		else sname=fname;
		window.parent.$(var_name).value = sname;
		window.parent.$(var_name).readOnly = true;
	}
	//alert(window.parent.$(var_name).value);
	if (var_enctype) {
		window.parent.$(var_enctype).value = type;
	}

	var var_ptype = window.parent.$('ptype');
	if (var_ptype) {
		window.parent.$(var_ptype).value = type;
	}

    //added by VAN 01-25-2013
    var is_maygohome_obj = window.parent.$('is_maygohome');
    if (is_maygohome_obj) {
        is_maygohome_obj.value = is_maygohome;
    }
    
    var bill_nr_obj = window.parent.$('bill_nr');
    if (bill_nr_obj) {
       bill_nr_obj.value = bill_nr;
    }
    
    var hasfinal_bill_obj = window.parent.$('hasfinal_bill');
    if (hasfinal_bill_obj) {
       hasfinal_bill_obj.value = hasfinal_bill;
    }
    
    var warningCaption ='';
    /*if ((bill_nr)||(is_maygohome)){
       if ((bill_nr!='')&&(is_maygohome==1)){ 
            warningCaption = "This patient has a saved billing and already advised to go home...";
       }else if ((bill_nr)&&(is_maygohome==0)){ 
            warningCaption = "This patient has a saved billing...";     
       }else if ((bill_nr=='')&&(is_maygohome==1)){ 
            warningCaption = "This patient is already advised to go home...";
       }     
    }*/   
    if ((bill_nr!='')&&(is_maygohome==1)) 
        warningCaption = "This patient has a saved billing and already advised to go home...";
    
    var warningcaption_obj = window.parent.$('warningcaption');
    if (warningcaption_obj) {
        warningcaption_obj.innerHTML = warningCaption;
    }          
       
    
    //---------------------

	if (var_enctype_show) {
		window.parent.$(var_enctype_show).innerHTML = enctype;
	}

	if (var_addr) {
		window.parent.$(var_addr).value = addr;
		window.parent.$(var_addr).readOnly = true;
	}
	if (var_clear)
		window.parent.$(var_clear).disabled=false;

	if (var_history)
		window.parent.$(var_history).style.display='';
        
    //added by VAN 07-04-2011
	//if (var_ctscan)
		//window.parent.$(var_ctscan).style.display='';

	//if (var_mri)
		//window.parent.$(var_mri).style.display='';

		// added by VAN 05-20-20101
		if (var_other){
			//if (showbtn==1)
				//window.parent.$(var_other).style.display='';                    //added by VAN 05-20-2010
			var iscash = window.parent.$("iscash1").checked;
			var hideOtherCharges = window.parent.$("hideOtherCharges").value;

			if ((iscash==false)&&(hideOtherCharges==0))
					// can add other charges
					showbtn = 1;
			else
					showbtn = 0;

			if (showbtn==1)
				window.parent.$(var_other).style.display='';        //added by VAN 05-20-2010
		}


	var pdi;
	if ( ('undefined' !== typeof var_parent_discountid) && (pdi=window.parent.$(var_parent_discountid)) )
	{
		pdi.value = real_parent_discountid;
	}

	//added by VAN 06-02-08
	var showPatientType = window.parent.$('patient_enctype');
	//alert(enctype);
	if (showPatientType) {
		if (enctype){
			showPatientType.innerHTML = enctype;
			//alert('mode = '+window.parent.$('is_cash').value)
			var priority = window.parent.document.inputform.priority;
			var is_cash = window.parent.$('is_cash').value;
			/*if (((type==1)||(type==3)||(type==4))&&(is_cash==0)){
				priority[0].checked = false;
				priority[1].checked = true;
			}else{
				priority[0].checked = true;
				priority[1].checked = false;
			}*/
		}else
			showPatientType.innerHTML = "None";
	}

	var showPatientLoc = window.parent.$('patient_location');
	if (showPatientLoc) {
		if (location)
			showPatientLoc.innerHTML = location;
		else
			showPatientLoc.innerHTML = "None";
	}

	var var_area_type =  window.parent.$('area_type');
	if(var_area_type)
		window.parent.$(var_area_type).value = $('area_type'+id).value;

	var showPatientMedico = window.parent.$('patient_medico_legal');
	if (showPatientMedico) {
		if (is_medico==1)
			showPatientMedico.innerHTML = "YES";
		else if (is_medico==0)
			showPatientMedico.innerHTML = "NO";
	}

	var showSex = window.parent.$('sex');
	if (showSex) {
		if (gender)
			showSex.innerHTML = gender;
		else
			showSex.innerHTML = "unknown";
	}

	var showAge = window.parent.$('age');
	if (showAge) {
		if (age)
			showAge.innerHTML = age;
		else
			showAge.innerHTML = "unknown";
	}

	var showBdate = window.parent.$('dob');
	if (showBdate) {
		if (dob)
			showBdate.innerHTML = dob;
		else
			showBdate.innerHTML = "unknown";
	}

	var date_birth = window.parent.$('date_birth');
	if (date_birth){
		if (dob)
			date_birth.value = dob;
		else
			date_birth.value = '0000-00-00';
	}

	var source = window.parent.$('source');
	var src_request;
	if (source){
		src_request = source.value;
	}else
		src_request = '';
    
    //for hact patient
    var hact = window.parent.$('is_hact');
    var is_hact = $('is_hact'+id).value;
    if (hact){
       if (is_hact==1){
            alert("WARNING!!! This is a HACT patient...");
            hact.checked = true;
       }else
            hact.checked = false;     
    }
    
    //patient's blood type
    var blood_type = window.parent.$('blood_type');
    var allow_updateBloodData = $('allow_updateBloodData');
    if (blood_type){
        blood_type.value = $('blood_type'+id).value;
        if (blood_type.value){
            blood_type.disabled = true;
            if (allow_updateBloodData.value==1) 
                blood_type.disabled = false;
        }        
    }    
    
    var check_all = window.parent.$('check_all');
    var type_charge = window.parent.$('type_charge');
    var coverage = window.parent.$('btn-coverage');        
	//default is CHARGE if and only if it is ER and encoded by the clinic, payward, rdu and IC charge to company
	if (((type==1)&&(src_request=='er'))||($('area_type'+id).value=='pw')||($('is_charge2comp'+id).value==1)||(enctype=='RDU')){
		if (window.parent.$("iscash1")){
			window.parent.$("iscash1").checked = false;
            if(type_charge) {
                type_charge.style.display='none';  
            }
            
        }    
		if (window.parent.$("iscash0")){
			window.parent.$("iscash0").checked = true;
			window.parent.$('iscash0').click();
            //added by VAS 03-21-2012
            if(type_charge) {
                type_charge.style.display='';  
            }
            
		}if (window.parent.$("is_cash")){
			window.parent.$('is_cash').value = 0;
        }    
            
        if (coverage)
            coverage.style.display='';        
            
        if(check_all) {
            check_all.disabled=false;  
        }            
	}/*else{
		if(window.parent.$("iscash1"))
			window.parent.$("iscash1").checked = true;
		if(window.parent.$("iscash0"))
			window.parent.$("iscash0").checked = false;
		if(window.parent.$('is_cash'))
			window.parent.$('is_cash').value = 1;
	}*/

	var showHRN = window.parent.$('hrn');
	if (showHRN) {
		showHRN.innerHTML = id;
	}

	var showAdmission = window.parent.$('admission_date');
	if (showAdmission) {
		showAdmission.innerHTML = admission_dt;
	}

	var showDischarge = window.parent.$('discharged_date');
	if (showDischarge) {
		showDischarge.innerHTML = discharge_date;
	}

	var showRID = window.parent.$('rid');
	if (showRID) {
		showRID.innerHTML = rid;
	}

	//var is_pe = window.parent.$('is_pe');
	var showPE = window.parent.$('is_pe');
	if (showPE){
		if (discountid=='PHS')
			showPE.disabled=false;
		else
			showPE.disabled=true;
	}
	//------------------

	//added by VAN 06-25-08
	var showSeniorCitizen = window.parent.$('issc');
	if (showSeniorCitizen) {
		if (senior_citizen==1)
			showSeniorCitizen.checked = true;
		else
			showSeniorCitizen.checked = false;
	}
	//---------------------------

	var is_cash = window.parent.$('is_cash');
	var showSWClass = window.parent.$('sw-class');
//	if (is_cash){
//		if (is_cash.value==1){
//			if (showSWClass) {
//				if (discountid)
//					showSWClass.innerHTML = parent_discountid;
//				else
//					showSWClass.innerHTML = "None";
//			}
//		}
//	}else{
//		if (showSWClass)
//			showSWClass.innerHTML = "None";
//	}


	/**
	* AJMQ 09-07-10
	* temporary fix for missing is_cash element in Pharmacy module
	*/
	var is_cash1 = window.parent.$('iscash1');
//	if (is_cash1) {
//		if (is_cash1.value==1){
			if (showSWClass) {
				if (discountid)
				{
					showSWClass.innerHTML = discountid;
				}
				else
					showSWClass.innerHTML = "None";
			}
//		}
//	}

	if(window.parent.getTotalServCharges) window.parent.getTotalServCharges(); //added by maimai 11-21-2014
	if(window.parent.setInsurances) window.parent.setInsurances(); //maimai 03-02-2014
	if (window.parent.refreshDiscount) window.parent.refreshDiscount();

	//alert(nr);
	//if (nr) {
		if(window.parent.emptyDataDialysisBilling) window.parent.emptyDataDialysisBilling(id, fname, lname, age, addr); //added by maimai 02-02-2015

		if (window.parent.pSearchClose) window.parent.pSearchClose();
		else if (window.parent.cClick) window.parent.cClick();
	//}
	//else {
	//	if (window.parent.cClick) window.parent.cClick();
	//}
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

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	//$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s)</span>';
	if (parseInt(total))
		$("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>'
	else
		$("pageShow").innerHTML = ''
	$("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
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
			startAJAXSearch('search',currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage);
		break;
	}
}

function addPerson(listID, details) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;

	details = Object.extend({
		id: '',
		lname: '',
		fname: '',
		mname: '',
		dob: '',
		sex: '',
		addr: '',
		zip: '',
		status: '',
		nr: '',
		type: '',
		discountid:'',
		discount: '',
		parent_discountid: '',
		// fix for pharma (09-08-10)
		real_parent_discountid: '',
		rid: '',
		enctype: '',
		location: '',
		ward_nr: '',
        is_maygohome: '',
        bill_nr: '',
        hasfinal_bill: '',
		is_medico: '',
		senior_citizen: '',
		orig_discountid: '',
		age: '',
		in_walkin: '',
		date_admitted: '',
		room_ward: '',
		adm_diagnosis: '',
		admission_dt: '',
		discharge_date: '',
		area_type: '',
		dept_nr: '',
		doc_nr: '',
		is_charge2comp: '',
		compName: '',
		compID: ''
	}, details);

	var id=details.id,
			lname=details.lname,
			fname=details.fname,
			mname=details.mname,
			dob=details.dob,
			sex=details.sex,
			addr=details.addr,
			zip=details.zip,
			status=details.status,
			nr=details.nr,
			type=details.type,
			discountid=details.discountid,
			discount=details.discount,
			parent_discountid = details.parent_discountid,
			// fix for pharma (09-08-10)
			real_parent_discountid = details.real_parent_discountid,
			rid=details.rid,
			//added by VAN 06-02-08
			enctype=details.enctype,
			location=details.location,
			ward_nr=details.ward_nr,
			is_medico = details.is_medico,
			senior_citizen = details.senior_citizen,
			orig_discountid = details.orig_discountid,
			age = details.age,
			in_walkin = details.in_walkin,
			date_admitted = details.date_admitted, //added by omick may 26, 2009
			room_ward = details.room_ward, //added by omick may 26, 2009
			adm_diagnosis = details.adm_diagnosis,
			admission_dt = details.admission_dt,
			discharge_date = details.discharge_date,
			area_type = details.area_type,
			dept_nr = details.dept_nr,
			doc_nr = details.doc_nr,
			is_charge2comp = details.is_charge2comp,
			compName = details.compName,
            is_hact = details.is_hact,
            blood_type = details.blood_type,
            is_maygohome = details.is_maygohome,
            bill_nr = details.bill_nr,
            hasfinal_bill = details.hasfinal_bill,
			compID = details.compID
            category = details.category
            totalCharges = details.totalCharges


		var gender = '';
		if (sex=='m') {
			gender = 'Male';
		}
		else if (sex=='f') {
			gender = 'Female';
		}
		else {
			gender = '-Not specified-'
		}

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
		// get the last row id and extract the current row no.
		if (id) {
			if (sex=='m')
				sexImg = '<img src="../../gui/img/common/default/spm.gif" border="0" />';
			else if (sex=='f')
				sexImg = '<img src="../../gui/img/common/default/spf.gif" border="0" />';
			else
				sexImg = '';
			if (type==0) {
				typ = "None";
				/*
				if (!discountid)
					typ="Walkin";
				else
					typ="Walkin("+discountid+")";
				*/
			}
			else if (type==1) typ='<span title="Case no. '+nr+'" style="color:#000080">ER Patient</span>';
			else if (type==2) typ='<span title="Case no. '+nr+'" style="color:#000080">Outpatient</span>';
			else if (type==3) typ='<span title="Case no. '+nr+'" style="color:#000080">Inpatient (ER)</span>';
			else if (type==4) typ='<span title="Case no. '+nr+'" style="color:#000080">Inpatient (OPD)</span>';
			else if (type==5) typ='<span title="Case no. '+nr+'" style="color:#000080">Dialysis</span>';
			else if (type==6) typ='<span title="Case no. '+nr+'" style="color:#000080">Industrial Clinic</span>';
			rowSrc = '<tr>'+
									'<td>'+
										'<input type="hidden" id="nr'+id+'" value="'+nr+'">'+
										'<input type="hidden" id="currenttime" value="'+details.currenttime+'">'+
                                        '<input type="hidden" id="is_hact'+id+'" name="is_hact'+id+'" value="'+details.is_hact+'">'+
                                        '<input type="hidden" id="blood_type'+id+'" name="blood_type'+id+'" value="'+details.blood_type+'">'+
										'<input type="hidden" id="dept_nr'+id+'" value="'+details.dept_nr+'">'+    //added by cha, may 18,2010
										'<input type="hidden" id="ward_nr'+id+'" value="'+details.ward_nr+'">'+    //added by cha, may 19,2010
										'<input type="hidden" id="room_nr'+id+'" value="'+details.room_nr+'">'+    //added by cha, may 19,2010
										'<input type="hidden" id="doc_nr'+id+'" value="'+details.doc_nr+'">'+
										'<input type="hidden" id="civil_status'+id+'" value="'+details.civil_status+'">'+    //added by cha, july 21,2010
										'<input type="hidden" id="photo_filename'+id+'" value="'+details.photo_filename+'">'+    //added by cha, july 21,2010
										'<input type="hidden" id="from_dialysis'+id+'" value="'+details.from_dialysis+'">'+    //added by cha, july 22,2010
										'<input type="hidden" id="has_insurance'+id+'" value="'+details.insurance+'">'+    //added by cha, august 17,2010
										'<input type="hidden" id="rid'+id+'" value="'+rid+'">'+
										'<input type="hidden" id="discountid'+id+'" value="'+discountid+'">'+
										'<input type="hidden" id="parent_discountid'+id+'" value="'+parent_discountid+'">'+
										'<input type="hidden" id="real_parent_discountid'+id+'" value="'+real_parent_discountid+'">'+
										'<input type="hidden" id="discount'+id+'" value="'+discount+'">'+
										'<input type="hidden" id="orig_discountid'+id+'" value="'+orig_discountid+'">'+
										'<input type="hidden" id="type'+id+'" value="'+type+'">'+
										'<input type="hidden" id="enctype'+id+'" value="'+enctype+'">'+
										'<input type="hidden" id="location'+id+'" value="'+location+'">'+
										'<input type="hidden" id="is_medico'+id+'" value="'+is_medico+'">'+
										'<input type="hidden" id="senior_citizen'+id+'" value="'+senior_citizen+'">'+
										'<input type="hidden" id="gender'+id+'" value="'+gender+'">'+
										'<input type="hidden" id="age'+id+'" value="'+age+'">'+
										'<input type="hidden" id="area_type'+id+'" value="'+area_type+'">'+
										'<input type="hidden" id="date_admitted'+id+'" value="'+date_admitted+'">'+ //added by omick, may 26, 2009
										'<input type="hidden" id="room_ward'+id+'" value="'+room_ward+'">'+ //added by omick, may 26, 2009
										'<input type="hidden" id="dob'+id+'" value="'+dob+'">'+
										'<input type="hidden" id="in_walkin'+id+'" value="'+in_walkin+'">'+

                                        '<input type="hidden" id="is_maygohome'+id+'" value="'+is_maygohome+'">'+
                                        '<input type="hidden" id="bill_nr'+id+'" value="'+bill_nr+'">'+
                                        '<input type="hidden" id="hasfinal_bill'+id+'" value="'+hasfinal_bill+'">'+

                                        '<input type="hidden" id="category'+id+'" value="'+category+'">'+
                                        '<input type="hidden" id="totalCharges'+id+'" value="'+totalCharges+'">'+
                                        
										'<input type="hidden" id="is_charge2comp'+id+'" value="'+is_charge2comp+'">'+
										'<input type="hidden" id="compID'+id+'" value="'+compID+'">'+
										'<input type="hidden" id="compName'+id+'" value="'+compName+'">'+

										'<span id="addr'+id+'" style="display:none">'+addr+'</span>'+
										'<input type="hidden" id="admission_dt'+id+'" value="'+admission_dt+'">'+
										'<input type="hidden" id="discharge_date'+id+'" value="'+discharge_date+'">'+
										'<span id="adm_diagnosis'+id+'" style="display:none">'+adm_diagnosis+'</span>'+
										'<span id="id'+id+'" style="color:'+(type=='W' ? '#000080' : '#660000')+'">'+id+'</span>'+
									'</td>'+
									'<td>'+sexImg+'</td>'+
									'<td><span id="lname'+id+'">'+lname+'</span></td>'+
									'<td><span id="fname'+id+'">'+fname+'</span></td>'+
									'<td><span id="mname'+id+'">'+mname+'</span></td>'+
									'<td><span>'+dob+'</span></td>'+
									'<td align="center" nowrap="nowrap"><span>'+typ+'</span></td>'+
									'<td align="center"><span style="color:#008000">'+discountid+'</span></td>'+
									'<td>'+
										'<input class="segButton" type="button" value="Select" style="color:#000066;" '+
											'onclick="prepareSelect(\''+id+'\')" '+
										'/>'+
									'</td>'+
								'</tr>';
		}
		else {
			if (!details.error) details.error = 'No such person exists...';
			rowSrc = '<tr><td colspan="9" style="">'+details.error+'</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}