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
    var discount = $('discount'+id).value;    
    var id = $('id'+id).innerHTML;
    var rid = $('rid'+id).value;
    var lname = $('lname'+id).innerHTML;
    var fname = $('fname'+id).innerHTML;
    var mname = $('mname'+id).innerHTML;
    var addr = $('addr'+id).innerHTML;
    var type = $('type'+id).value;    
    
    //added by VAN 06-02-08
    var enctype = $('enctype'+id).value;    
    var location = $('location'+id).value;    
    var is_medico = $('is_medico'+id).value;
    
    //added by VAN 06-25-08
    var senior_citizen = $('senior_citizen'+id).value;

        if (var_pid) 
        window.parent.$(var_pid).value = id;
    if (var_rid) 
        window.parent.$(var_rid).value = rid;
    if (var_encounter_nr)
        window.parent.$(var_encounter_nr).value = nr;
    if (var_discountid)
        window.parent.$(var_discountid).value = discountid;
    if (var_discount) 
        window.parent.$(var_discount).value = discount;
    if (var_name) {
        //window.parent.$(var_name).value = fname + " " + lname;
        if (mname)
            mname = mname.substring(0,1)+".";
        window.parent.$(var_name).value = fname + " " + mname + " " + lname;
        window.parent.$(var_name).readOnly = true;
    }
    if (var_enctype) {
        window.parent.$(var_enctype).value = type;
    }
    if (var_enctype_show) {
        window.parent.$(var_enctype_show).innerHTML = enctype;
    }
    
    if (var_addr) {
        window.parent.$(var_addr).value = addr;
        window.parent.$(var_addr).readOnly = true;
    }
    if (var_clear)
        window.parent.$(var_clear).disabled=false;    
        
    //added by VAN 06-02-08
    var showPatientType = window.parent.$('patient_enctype');
    //alert(enctype);
    if (showPatientType) {
        if (enctype)
            showPatientType.innerHTML = enctype;
        else
            showPatientType.innerHTML = "None";
    }
    
    var showPatientLoc = window.parent.$('patient_location');
    if (showPatientLoc) {
        if (location)
            showPatientLoc.innerHTML = location;
        else
            showPatientLoc.innerHTML = "None";
    }
    
    var showPatientMedico = window.parent.$('patient_medico_legal');
    if (showPatientMedico) {
        if (is_medico==1)
            showPatientMedico.innerHTML = "YES";
        else if (is_medico==0)
            showPatientMedico.innerHTML = "NO";
    }
    //------------------------------------
    
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

    if (nr) {
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
    var i

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
            is_medico = details.is_medico,
            senior_citizen = details.senior_citizen
            

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
            rowSrc = '<tr>'+
                                    '<td>'+
                                        '<input type="hidden" id="nr'+id+'" value="'+nr+'">'+
                                        '<input type="hidden" id="rid'+id+'" value="'+rid+'">'+
                                        '<input type="hidden" id="discountid'+id+'" value="'+discountid+'">'+
                                        '<input type="hidden" id="discount'+id+'" value="'+discount+'">'+
                                        '<input type="hidden" id="type'+id+'" value="'+type+'">'+
                                        '<input type="hidden" id="enctype'+id+'" value="'+enctype+'">'+
                                        '<input type="hidden" id="location'+id+'" value="'+location+'">'+
                                        '<input type="hidden" id="is_medico'+id+'" value="'+is_medico+'">'+
                                        '<input type="hidden" id="senior_citizen'+id+'" value="'+senior_citizen+'">'+
                                        '<span id="addr'+id+'" style="display:none">'+addr+'</span>'+
                                        '<span id="id'+id+'" style="color:#660000">'+id+'</span>'+
                                    '</td>'+
                                    '<td>'+sexImg+'</td>'+
                                    '<td><span id="lname'+id+'">'+lname+'</span></td>'+
                                    '<td><span id="fname'+id+'">'+fname+'</span></td>'+
                                    '<td><span id="mname'+id+'">'+mname+'</span></td>'+
                                    '<td><span>'+dob+'</span></td>'+
                                    '<td align="center" nowrap="nowrap"><span>'+typ+'</span></td>'+
                                    '<td align="center"><span style="color:#008000">'+discountid+'</span></td>'+
                                    '<td>'+
                                        '<input type="button" value="Select" style="color:#000066; font-weight:bold; padding:0px 2px" '+
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