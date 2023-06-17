var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;

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

	//added by VAN 06-02-08
	var enctype = $('enctype'+id).value;
	var location = $('location'+id).value;
	var is_medico = $('is_medico'+id).value;
	 //alert(orig_discountid);
	//added by VAN 06-25-08
	var senior_citizen = $('senior_citizen'+id).value;
		//alert(window.parent.$("iscash1").checked);

	var admission_dt = $('admission_dt'+id).value;	//added by van
	var discharge_date = $('discharge_date'+id).value;	//added by van

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

	if(var_ward_nr)
		window.parent.$(var_ward_nr).value = $('ward_nr'+id).value;

	if(var_room_nr)
		window.parent.$(var_room_nr).value = $('room_nr'+id).value;

	//added by cha, july 21, 2010
	if(var_civil_status)
		window.parent.$(var_civil_status).value = $('civil_status'+id).value;
	if(var_dob)
		window.parent.$(var_dob).value = dob;
	if(var_location)
		window.parent.$(var_location).value = $('location'+id).value;
	if(var_type)
		window.parent.$(var_type).value = $('type'+id).value;
	if(var_photo_filename)
	{
		var src = $('photo_filename'+id).value;
		if(src!="") {
			/*window.parent.$(var_photo_filename).innerHTML =
				'<img width="180px" height="150px" border="0" name="headpic" src="../../fotos/registration/'+src+'">';*/
				window.parent.$(var_photo_filename).src = "../../fotos/registration/"+src;
				window.parent.$('photo_src').value = "../../fotos/registration/"+src;
		}
		else {
			/*window.parent.$(var_photo_filename).innerHTML =
				'<img width="180px" height="150px" src="../../gui/img/control/default/en/en_x-blank.gif" name="headpic" border="0">';*/
				window.parent.$(var_photo_filename).src = "../../gui/img/control/default/en/en_x-blank.gif";
				window.parent.$('photo_src').value = "../../gui/img/control/default/en/en_x-blank.gif";
		}
	}


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
		window.parent.$(var_enctype).value = enctype;  //modified by cha, july 22, 2010
	}

//	var var_area = window.parent.$('area');
//	if (var_area) {
//		window.parent.$(var_area).value = enctype;
//	}

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

	//added by VAN 06-02-08
	var showPatientType = window.parent.$('patient_enctype');
	//alert(enctype);
	if (showPatientType) {
		if (enctype){
			showPatientType.innerHTML = enctype;
			//alert('mode = '+window.parent.$('is_cash').value)
			var priority = window.parent.document.inputform.priority;
			var is_cash = window.parent.$('is_cash').value;
			if (((type==1)||(type==3)||(type==4))&&(is_cash==0)){
				priority[0].checked = false;
				priority[1].checked = true;
			}else{
				priority[0].checked = true;
				priority[1].checked = false;
			}
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

	var showSWClass = window.parent.$('sw-class');
	if (showSWClass) {
		if (discountid)
			showSWClass.innerHTML = discountid;
		else
			showSWClass.innerHTML = "None";
	}
	if (window.parent.refreshDiscount) window.parent.refreshDiscount();

	//added by cha, july 22, 2010
	if($('from_dialysis'+id).value=="1")
	{
		window.parent.setVisitNo(nr);
	}
	//end cha

	//alert(nr);
	//if (nr) {
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
		rid: '',
		enctype: '',
		location: '',
		ward_nr: '',
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
		area_type: ''
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
			area_type = details.area_type

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
			else if (type==5) typ='<span title="Case no. '+nr+'" style="color:#000080">Dialysis</span>';   //added by cha, july 23, 2010
			rowSrc = '<tr>'+
									'<td>'+
										'<input type="hidden" id="nr'+id+'" value="'+nr+'">'+
										'<input type="hidden" id="dept_nr'+id+'" value="'+details.dept_nr+'">'+    //added by cha, may 18,2010
										'<input type="hidden" id="ward_nr'+id+'" value="'+details.ward_nr+'">'+    //added by cha, may 19,2010
										'<input type="hidden" id="room_nr'+id+'" value="'+details.room_nr+'">'+    //added by cha, may 19,2010
										'<input type="hidden" id="civil_status'+id+'" value="'+details.civil_status+'">'+    //added by cha, july 21,2010
										'<input type="hidden" id="photo_filename'+id+'" value="'+details.photo_filename+'">'+    //added by cha, july 21,2010
										'<input type="hidden" id="from_dialysis'+id+'" value="'+details.from_dialysis+'">'+    //added by cha, july 22,2010
										'<input type="hidden" id="rid'+id+'" value="'+rid+'">'+
										'<input type="hidden" id="discountid'+id+'" value="'+discountid+'">'+
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
											'onclick="prepareSelect(\''+id+'\')"/>'+
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
