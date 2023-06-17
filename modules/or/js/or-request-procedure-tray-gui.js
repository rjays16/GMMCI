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
    //alert("id= "+id);

    //var nr = $('nr'+id).value;
    //var discountid = $('discountid'+id).value;
    //var orig_discountid = $('orig_discountid'+id).value;
    //var discount = $('discount'+id).value;    
    //var id = $('id'+id).innerHTML;  Commented by Cherry 02-25-10
    var id = $('id'+id).value;
    
    //alert(id);    Commented by Cherry 03-01-10
    
    //var rid = $('rid'+id).value;
    //var lname = $('lname'+id).innerHTML;
    //var fname = $('fname'+id).innerHTML;
    //var mname = $('mname'+id).innerHTML;
    //var addr = $('addr'+id).innerHTML;
    //var type = $('type'+id).value;    
    var name = $('name'+id).innerHTML; //added by Cherry 02-24-10
    
    //alert(name);
    
    //added by VAN 06-02-08
    //var enctype = $('enctype'+id).value;    
    //var location = $('location'+id).value;    
    //var is_medico = $('is_medico'+id).value;
     //alert(orig_discountid);
    //added by VAN 06-25-08
    //var senior_citizen = $('senior_citizen'+id).value;
    
    //var admission_dt = $('admission_dt'+id).value;    //added by van
    //var discharge_date = $('discharge_date'+id).value;    //added by van
    
    /*if(name){
        window.parent.$('procedure_fld').value = name;
        
    }*/
    
    //Added by Cherry 02-24-10
    if(var_id){
       window.parent.$(var_id).value = id;
   }
    //alert(window.parent.$(var_name).value);   
    if(var_name){
        window.parent.$(var_name).value = name;
        window.parent.$(var_name).readOnly = true;
    }
    /* Commented by Cherry 02-24-10
    if (var_pid) 
        window.parent.$(var_pid).value = id;
    if (var_rid) 
        window.parent.$(var_rid).value = rid;
    if (var_encounter_nr)
        window.parent.$(var_encounter_nr).value = nr;
    if (var_discountid)
        window.parent.$(var_discountid).value = discountid;
   
   var var_orig_discountid = window.parent.$('orig_discountid'); 
   if (var_orig_discountid)
        window.parent.$('orig_discountid').value = orig_discountid;
            
    if (var_discount) 
        window.parent.$(var_discount).value = discount;
    if (var_name) {
        //window.parent.$(var_name).value = fname + " " + lname;
        if (mname)
            mname = mname.substring(0,1)+".";
        //window.parent.$(var_name).value = fname + " " + mname + " " + lname;
        window.parent.$(var_name).value = lname+", "+fname+ " " + mname;
        window.parent.$(var_name).readOnly = true;
    }
    
    if (var_enctype) {
        window.parent.$(var_enctype).value = type;
    }
    
    var var_area = window.parent.$('area'); 
    if (var_area) {
        window.parent.$(var_area).value = enctype;
    }
    
    if (var_enctype_show) {
        window.parent.$(var_enctype_show).innerHTML = enctype;
    }
    
    if (var_addr) {
        window.parent.$(var_addr).value = addr;
        window.parent.$(var_addr).readOnly = true;
    }*/
    if (var_clear)
        window.parent.$(var_clear).disabled=false;    
        
    //added by VAN 06-02-08
    //var showPatientType = window.parent.$('patient_enctype');
    //alert(enctype);
    /*if (showPatientType) {
        if (enctype)
            showPatientType.innerHTML = enctype;
        else
            showPatientType.innerHTML = "None";
    }*/
    
    /*var showPatientLoc = window.parent.$('patient_location');
    if (showPatientLoc) {
        if (location)
            showPatientLoc.innerHTML = location;
        else
            showPatientLoc.innerHTML = "None";
    }*/
    
    /*var showPatientMedico = window.parent.$('patient_medico_legal');
    if (showPatientMedico) {
        if (is_medico==1)
            showPatientMedico.innerHTML = "YES";
        else if (is_medico==0)
            showPatientMedico.innerHTML = "NO";
    }*/
    //------------------------------------
    
    //added by VAN 03-05-09
    /*var showHRN = window.parent.$('hrn');
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
    }*/
    
    //------------------
    
    //added by VAN 06-25-08
    /*var showSeniorCitizen = window.parent.$('issc');
    if (showSeniorCitizen) {
        if (senior_citizen==1)
            showSeniorCitizen.checked = true;
        else
            showSeniorCitizen.checked = false;
    }*/
    //---------------------------

    var showSWClass = window.parent.$('sw-class');
    if (showSWClass) {
        if (discountid)
            showSWClass.innerHTML = discountid;
        else
            showSWClass.innerHTML = "None";
    }
    if (window.parent.refreshDiscount) window.parent.refreshDiscount();    

    if (id) {
        if (window.parent.pSearchClose) window.parent.pSearchClose();
        else if (window.parent.cClick) window.parent.cClick();
    }
    else {
        if (window.parent.cClick) window.parent.cClick();
    }
}

function clearList(listID) {
    // Search for the source row table element
    var list=$(listID),dRows, dBody;
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        if (dBody) {
            dBody.innerHTML = "";
            return true;    // success
        }
        else return false;    // fail
    }
    else return false;    // fail
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
    //alert('addPerson');
    //alert(details);
   //Commented by Cherry 02-24-10
    /*var id=details.id, 
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
            is_medico = details.is_medico,
            senior_citizen = details.senior_citizen,
            orig_discountid = details.orig_discountid,
            admission_dt = details.admission_dt,
            discharge_date = details.discharge_date
         */ 
        
        //Added by Cherry 02-24-10
        var id = details.id,
            name = details.name;    
            
         //alert("name = "+details.name);    //NaN                                
    
    if (list) {
        dBody=list.getElementsByTagName("tbody")[0];
        dRows=dBody.getElementsByTagName("tr");
        // get the last row id and extract the current row no.
        if (id) {
           
            rowSrc = '<tr>'+
                                    '<td>'+
                                        '<input type="hidden" id="id'+id+'" value="'+id+'">'+
                                        
                                        '<span id="name'+id+'">'+name+'</span>'+
                                    
                                    '<td>'+
                                        '<input type="button" value="Select" style="color:#000066; font-weight:bold; padding:0px 2px" '+
                                            'onclick="prepareSelect(\''+id+'\')" '+
                                        '/>'+
                                    '</td>'+
                                '</tr>';
        }
        else {
            if (!details.error) details.error = 'No such procedure exists...';
            rowSrc = '<tr><td colspan="9" style="">'+details.error+'</td></tr>';
        }
        dBody.innerHTML += rowSrc;
    }
}